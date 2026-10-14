#!/usr/bin/env python3
"""
색상 팔레트 빌드: shared/palettes.json(원본)을 검사한 뒤 팔레트를 쓰는 테마마다
inc/palettes.php(데이터)와 inc/palette-control.php(커스터마이저 견본 카드)를 복사해 넣는다.
테마는 zip 하나로 배포되므로 각 테마 안에 파일이 들어 있어야 한다.

  python3 tools/build-palettes.py          # 검사 + 생성
  python3 tools/build-palettes.py --check  # 검사만

검사: 테마가 실제로 쓰는 글자/배경 조합이 모두 WCAG AA(4.5:1) 이상이어야 한다. 하나라도 미달이면 생성하지 않는다.
"""
import json
import os
import shutil
import sys

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
THEMES = ['fv-article-reader', 'fv-article-rail', 'fv-article-stream']
ROLES = ['bg', 'surface', 'chrome', 'text', 'muted', 'accent', 'on-chrome', 'chrome-muted', 'accent-on-chrome', 'line']

# (글자, 배경, 어디서 쓰는지)
PAIRS = [
    ('text', 'surface', '본문'),
    ('text', 'bg', '바탕 위 글자'),
    ('muted', 'surface', '메타·캡션'),
    ('muted', 'bg', '광고 자리 표시'),
    ('accent', 'surface', '링크·다음 기사 라벨'),
    ('#ffffff', 'accent', '순위 배지(02·04)'),
    ('on-chrome', 'accent', '더 보기 버튼 hover(04)'),
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
        missing = [r for r in ROLES if r not in p['colors']]
        if missing:
            errors.append(f'{key}: 색 누락 {missing}')
            continue
        for fg, bg, where in PAIRS:
            fg_hex = fg if fg.startswith('#') else p['colors'][fg]
            ratio = contrast(fg_hex, p['colors'][bg])
            if ratio < MIN_RATIO:
                errors.append(f'{key}: {fg}/{bg} = {ratio:.2f}:1 ({where})')
    return errors


def php_value(s):
    return "'" + s.replace('\\', '\\\\').replace("'", "\\'") + "'"


def render_php(palettes):
    lines = [
        '<?php',
        '/**',
        ' * 색상 팔레트 데이터 — 자동 생성 파일. 직접 고치지 말고 shared/palettes.json을 고친 뒤',
        ' * python3 tools/build-palettes.py 를 실행한다. 모든 조합은 WCAG AA(4.5:1) 검사를 통과한 값.',
        ' *',
        ' * 형식: 키 => array( label => "이름 — 설명", colors => array( CSS 변수명(-- 제외) => 값 ) )',
        ' */',
        '',
        'return array(',
    ]
    for p in palettes:
        colors = dict(p['colors'])
        on = colors['on-chrome']
        colors['chrome-line'] = 'rgba(%d, %d, %d, 0.14)' % tuple(int(on[i:i + 2], 16) for i in (1, 3, 5))
        width = max(len(k) for k in colors) + 2
        lines.append(f"\t{php_value(p['key'])} => array(")
        lines.append(f"\t\t'label'  => {php_value(p['label'])},")
        lines.append("\t\t'colors' => array(")
        for k, v in colors.items():
            lines.append(f"\t\t\t{php_value(k).ljust(width)} => {php_value(v)},")
        lines.append('\t\t),')
        lines.append('\t),')
    lines.append(');')
    return '\n'.join(lines) + '\n'


def main():
    with open(os.path.join(ROOT, 'shared', 'palettes.json'), encoding='utf-8') as f:
        palettes = json.load(f)

    errors = check(palettes)
    if errors:
        print('대비 검사 실패:')
        for e in errors:
            print('  ' + e)
        sys.exit(1)
    print(f'팔레트 {len(palettes)}개 × 조합 {len(PAIRS)}가지 대비 검사 통과')

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
