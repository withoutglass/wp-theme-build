#!/usr/bin/env python3
"""
색상 팔레트 빌드: shared/palettes.json(원본)을 검사한 뒤 팔레트를 쓰는 테마마다
inc/palettes.php(데이터)와 inc/palette-control.php(커스터마이저 견본 카드)를 복사해 넣는다.
테마는 zip 하나로 배포되므로 각 테마 안에 파일이 들어 있어야 한다.

  python3 tools/build-palettes.py          # 검사 + 생성
  python3 tools/build-palettes.py --check  # 검사만

검사: 테마가 실제로 쓰는 글자/배경 조합이 라이트·다크 모두 WCAG AA(4.5:1) 이상이어야 한다. 하나라도 미달이면 생성하지 않는다.

다크 변형: 팔레트에 "dark"가 없으면 라이트 팔레트의 틀·포인트 색 계열로 자동 생성해 palettes.json에 써 넣는다
(틀이 가장 어둡고 → 바탕 → 지면 순으로 밝아지는 위계 유지, 명도는 대비 기준을 넘을 때까지 조정).
마음에 들지 않으면 palettes.json의 "dark" 값을 직접 고치면 된다 — 이미 있는 값은 덮어쓰지 않는다.
"""
import colorsys
import json
import os
import shutil
import sys

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
THEMES = ['fv-article-news', 'fv-article-news-category', 'fv-article-reader', 'fv-article-rail', 'fv-article-stream']
ROLES = ['bg', 'surface', 'chrome', 'text', 'muted', 'accent', 'on-accent', 'on-chrome', 'chrome-muted', 'accent-on-chrome', 'line']

# (글자, 배경, 어디서 쓰는지)
PAIRS = [
    ('text', 'surface', '본문'),
    ('text', 'bg', '바탕 위 글자'),
    ('muted', 'surface', '메타·캡션'),
    ('muted', 'bg', '광고 자리 표시'),
    ('accent', 'surface', '링크·다음 기사 라벨'),
    ('on-accent', 'accent', '순위 배지(01·02·04)·현재 페이지 번호(01)·더 보기 버튼 hover(04)'),
    ('on-chrome', 'chrome', '틀 위 제목'),
    ('chrome-muted', 'chrome', '틀 위 보조 글자'),
    ('accent-on-chrome', 'chrome', '틀 위 포인트·버튼'),
]
MIN_RATIO = 4.5


def luminance(hex_color):
    rgb = [int(hex_color[i:i + 2], 16) / 255 for i in (1, 3, 5)]
    rgb = [c / 12.92 if c <= 0.03928 else ((c + 0.055) / 1.055) ** 2.4 for c in rgb]
    return 0.2126 * rgb[0] + 0.7152 * rgb[1] + 0.0722 * rgb[2]


def contrast(a, b):
    la, lb = luminance(a), luminance(b)
    return (max(la, lb) + 0.05) / (min(la, lb) + 0.05)


def check(palettes):
    errors = []
    keys = set()
    for p in palettes:
        key = p['key']
        if key in keys:
            errors.append(f'{key}: 키 중복')
        keys.add(key)
        for mode, colors in (('light', p['colors']), ('dark', p.get('dark'))):
            if not colors:
                errors.append(f'{key}: {mode} 값 없음')
                continue
            missing = [r for r in ROLES if r not in colors]
            if missing:
                errors.append(f'{key}/{mode}: 색 누락 {missing}')
                continue
            for fg, bg, where in PAIRS:
                ratio = contrast(colors[fg], colors[bg])
                if ratio < MIN_RATIO:
                    errors.append(f'{key}/{mode}: {fg}/{bg} = {ratio:.2f}:1 ({where})')
    return errors


def hls(hex_color):
    return colorsys.rgb_to_hls(*[int(hex_color[i:i + 2], 16) / 255 for i in (1, 3, 5)])


def to_hex(h, l, s):
    return '#%02x%02x%02x' % tuple(round(c * 255) for c in colorsys.hls_to_rgb(h, l, s))


def tune(h, s, l, step, ok):
    """명도를 step씩 옮기며 ok(색)가 참이 되는 첫 색을 찾는다."""
    while 0 <= l <= 1:
        c = to_hex(h, l, s)
        if ok(c):
            return c
        l += step
    raise ValueError('대비 기준을 맞출 수 없음')


def derive_dark(light):
    ch, _, cs = hls(light['chrome'])
    ah, _, as_ = hls(light['accent-on-chrome'])
    d = {
        'chrome': to_hex(ch, 0.055, min(cs, 0.35)),
        'bg': to_hex(ch, 0.10, min(cs, 0.25)),
        'surface': to_hex(ch, 0.17, min(cs, 0.22)),
        'text': to_hex(ch, 0.91, min(cs, 0.15)),
        'on-chrome': to_hex(ch, 0.92, min(cs, 0.18)),
    }
    d['muted'] = tune(ch, min(cs, 0.12), 0.62, 0.01, lambda c: contrast(c, d['surface']) >= 4.6 and contrast(c, d['bg']) >= 4.6)
    d['chrome-muted'] = tune(ch, min(cs, 0.15), 0.62, 0.01, lambda c: contrast(c, d['chrome']) >= 6.0)
    d['accent'] = tune(ah, as_, 0.60, 0.01, lambda c: contrast(c, d['surface']) >= 4.8)
    d['on-accent'] = d['chrome']
    d['accent-on-chrome'] = tune(ah, as_, 0.62, 0.01, lambda c: contrast(c, d['chrome']) >= 6.5)
    d['line'] = to_hex(ch, 0.25, min(cs, 0.18))
    return {r: d[r] for r in ROLES}


def php_value(s):
    return "'" + s.replace('\\', '\\\\').replace("'", "\\'") + "'"


def with_chrome_line(colors):
    colors = dict(colors)
    on = colors['on-chrome']
    colors['chrome-line'] = 'rgba(%d, %d, %d, 0.14)' % tuple(int(on[i:i + 2], 16) for i in (1, 3, 5))
    return colors


def render_php(palettes):
    lines = [
        '<?php',
        '/**',
        ' * 색상 팔레트 데이터 — 자동 생성 파일. 직접 고치지 말고 shared/palettes.json을 고친 뒤',
        ' * python3 tools/build-palettes.py 를 실행한다. 라이트·다크 모든 조합이 WCAG AA(4.5:1) 검사를 통과한 값.',
        ' *',
        ' * 형식: 키 => array( label => "이름 — 설명", colors => 라이트, dark => 다크 ) — 각 값은 CSS 변수명(-- 제외) => 값',
        ' */',
        '',
        'return array(',
    ]
    for p in palettes:
        lines.append(f"\t{php_value(p['key'])} => array(")
        lines.append(f"\t\t'label'  => {php_value(p['label'])},")
        for field in ('colors', 'dark'):
            colors = with_chrome_line(p[field])
            width = max(len(k) for k in colors) + 2
            lines.append(f"\t\t{php_value(field).ljust(8)} => array(")
            for k, v in colors.items():
                lines.append(f"\t\t\t{php_value(k).ljust(width)} => {php_value(v)},")
            lines.append('\t\t),')
        lines.append('\t),')
    lines.append(');')
    return '\n'.join(lines) + '\n'


def main():
    json_path = os.path.join(ROOT, 'shared', 'palettes.json')
    with open(json_path, encoding='utf-8') as f:
        palettes = json.load(f)

    # 라이트 on-accent 기본값(흰색), 다크 변형 자동 생성 — 이미 있는 값은 그대로 둔다.
    filled = []
    for p in palettes:
        if 'on-accent' not in p['colors']:
            p['colors']['on-accent'] = '#ffffff'
            p['colors'] = {r: p['colors'][r] for r in ROLES}
        if 'dark' not in p:
            p['dark'] = derive_dark(p['colors'])
            filled.append(p['key'])
    if filled and '--check' not in sys.argv:
        with open(json_path, 'w', encoding='utf-8') as f:
            json.dump(palettes, f, ensure_ascii=False, indent=1)
            f.write('\n')
        print(f'다크 변형 자동 생성: {", ".join(filled)}')

    errors = check(palettes)
    if errors:
        print('대비 검사 실패:')
        for e in errors:
            print('  ' + e)
        sys.exit(1)
    print(f'팔레트 {len(palettes)}개 × 라이트·다크 × 조합 {len(PAIRS)}가지 대비 검사 통과')

    if '--check' in sys.argv:
        return

    php = render_php(palettes)
    for theme in THEMES:
        inc = os.path.join(ROOT, 'themes', theme, 'inc')
        os.makedirs(inc, exist_ok=True)
        with open(os.path.join(inc, 'palettes.php'), 'w', encoding='utf-8') as f:
            f.write(php)
        shutil.copyfile(os.path.join(ROOT, 'shared', 'palette-control.php'), os.path.join(inc, 'palette-control.php'))
        print(f'  themes/{theme}/inc/ 갱신')


if __name__ == '__main__':
    main()
