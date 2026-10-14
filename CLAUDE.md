# wp-theme-build

워드프레스 신규 테마를 만드는 실험/구축 프로젝트.

## 목표

여러 가지 워드프레스 테마를 만들어보고 비교·발전시킨다.

## 디렉토리 구조

- `themes/<slug>/` = 테마 소스, `releases/<slug>/` = 배포 zip, `pages/` = 사이트 세팅용 페이지 원문.
- `themes/` 아래 폴더 이름 = 워드프레스 테마 slug. 네이밍 규칙: `fv-<서비스>-<레이아웃 특징>`
  (예: `fv-article-news` = 유튜브→article 서비스의 뉴스형 2단 레이아웃).
  - 함수 접두사 `fvarticle_news_`, Text Domain `fv-article-news`, `Theme Name: FV Article News` 식으로 맞춘다.
  - wordpress.org 동명 테마의 "업데이트"로 덮어써지지 않도록 style.css에 `Update URI: false`를 둔다.
  - slug를 바꾸면 워드프레스는 별개 테마로 인식한다(로고·메뉴 위치 등 theme_mod 재설정 필요). DB 옵션 키는 바꾸지 말 것.
- 각 폴더 = 하나의 독립적인 테마 시도.
- `fv-article-reader` = 같은 서비스의 02 테마. fv-article-news를 복제해 리더형 1단 레이아웃 + 미드나잇 인디고 팔레트로 바꿨다(사이드바 없음, 본문 중간·넓은 화면 오른쪽 여백 광고). 채널 설정 옵션을 fv-article-news와 공유한다.
- `fv-article-rail` = 같은 서비스의 03 테마. fv-article-reader를 복제해 채널 레일형(왼쪽 고정 레일: 채널 프로필·실시간 TOP, 오른쪽 160×600 광고 칼럼 ≥1280px) + 딥 포레스트 팔레트로 바꿨다. 1023px 이하에서 레일은 상단 채널 바 + 가로 TOP 칩이 된다. 채널 설정 옵션 공유.
- `fv-article-stream` = 04 테마. fv-article-reader 기반 1단 + 버건디 와인 기본 팔레트. 기사 상세에서 이전 발행글을 AJAX로 이어 붙이는 무한 스트림이 핵심.
  - REST `GET /wp-json/fv-article-stream/v1/next?after=<ID>` → 서버가 렌더링한 기사 HTML(`template-parts/stream-article.php`) + url/title/next_url. 순서는 (발행 시각, ID) 내림차순 — 일괄 발행으로 같은 초의 글이 많아 get_adjacent_post는 글을 건너뛴다.
  - `assets/stream.js`: 자동 5편 후 "다음 기사 보기" 버튼. 화면 가운데 기사로 URL·탭 제목을 replaceState (GA4 향상된 측정의 "브라우저 기록 이벤트 기반 페이지 변경"이 페이지뷰로 집계). 자동 로딩 트리거는 스크롤 위치 체크 — IntersectionObserver는 End 키 점프 때 놓친다.
  - 본문 중간 광고는 REST 렌더링에서도 들어가야 해서 the_content 필터가 아닌 헬퍼를 템플릿에서 직접 호출. JS가 없으면 "다음 기사 보기"는 일반 링크.
- fv-article-reader·fv-article-rail·fv-article-stream은 외모 > 사용자 정의하기 > 색상 팔레트에서 24개 팔레트 중 색 견본 카드로 선택(theme_mod `color_palette`, 기본 = 각 테마 고유색 indigo/forest/burgundy).
  - 모드: 사용자 정의하기 > 색상 팔레트 > 모드 = 라이트(기본) / 다크 / OS 설정 따르기 (theme_mod `color_mode`). 팔레트마다 `dark` 값이 있고, 다크·OS 모드면 같은 CSS 변수를 다크 값으로 덮어쓴다(OS는 prefers-color-scheme 미디어 쿼리). 포인트색 위 글자는 `--on-accent` 변수(라이트 흰색, 다크 틀색)로 칠한다.
  - 새 팔레트에 `dark`를 안 넣으면 빌드 스크립트가 라이트 값의 색 계열로 자동 생성해 palettes.json에 써 넣는다.
  - 원본은 `shared/palettes.json`(데이터)·`shared/palette-control.php`(견본 카드 컨트롤). 고친 뒤 `python3 tools/build-palettes.py` → 대비 검사 후 각 테마 `inc/`로 생성·복사. 테마의 `inc/` 사본은 직접 고치지 않는다.
  - 검사 기준: 테마가 쓰는 글자/배경 조합 9가지(스크립트 PAIRS)가 라이트·다크 모두 4.5:1 이상. 테마에 새 색 조합을 쓰면 PAIRS에도 추가할 것.
  - 색은 전부 style.css :root 변수로만 칠할 것 — 새 색을 하드코딩하면 팔레트 전환에서 빠진다. 팔레트를 쓰는 테마를 새로 만들면 스크립트 THEMES에 추가.
- `fv-news-portal` = 05 테마. MSN 서비스와 무관한 **순수 범용 뉴스 샘플**(00에서 출발). 포털형: 상단 바(날짜·검색) → 제호 → 포인트색 카테고리 메뉴 바 → 최신 기사 띠, 홈은 헤드라인(대표 1 + 주요 4) + 카테고리 섹션(글 수 순 최상위 카테고리 최대 6개), 사이드바 위젯(비면 인기 뉴스=댓글 수 순·최신 뉴스), 기사에 바이라인·태그·기자 소개·이전/다음·같은 카테고리 관련 기사·댓글. 메뉴 위치 primary/footer(주 메뉴 없으면 카테고리로 대체).
  - 색: 흰 바탕 고정 + 포인트색 8개 프리셋(functions.php `fvnews_portal_accents`, 라이트/다크 쌍) + 모드 라이트/다크/OS. 팔레트 빌드 스크립트(shared/·tools/) 대상이 아니다. 모드는 `<html class="mode-*">`로 style.css가 처리.
- `fv-article-news`는 2026-10 `01`에서 이름을 바꿨다. 채널 설정 옵션 키 `sample01_channel_options`는 기존 데이터 유지를 위해 그대로 둔다.
- `00` = **최소 베이스라인** 테마. 실사용에 가까운 최소 골격(zero-base 클래식 테마)이며,
  새 테마는 특별한 사유가 없으면 이 구조에서 출발한다.
  구성: `style.css`, `functions.php`, `header.php`, `footer.php`, `index.php`,
  `single.php`, `page.php`, `archive.php`, `search.php`, `404.php`,
  `template-parts/content.php`(목록 아이템 공용 파트).

## 테마 구축 방식 (테마마다 선택)

1. **Zero-base 커스텀 테마** — 워드프레스 테마를 밑바닥부터 직접 구축.
2. **GeneratePress + child theme** — 가벼운 GeneratePress 부모 테마 위에 child theme로 구축.

어떤 방식을 쓸지는 각 테마 폴더별로 정한다. 새 테마를 시작할 때 방식이 명시되지 않았다면 물어본다.

## 로컬 개발 환경

- `.wp-local/` (gitignore됨)에 로컬 워드프레스가 구성되어 있다: 정적 PHP 8.3 + wp-cli + SQLite.
- 서버 실행: `.wp-local/serve` → http://localhost:8881 (admin/admin).
  (`wp server`는 임시폴더 라우터를 써서 macOS 임시파일 청소 시 죽는다 — serve 스크립트는 영구 라우터 + 멀티 워커라 안전.)
- `themes/00/`, `themes/fv-article-news/`, `themes/fv-article-reader/`, `themes/fv-article-rail/`, `themes/fv-article-stream/`, `themes/fv-news-portal/`은 `.wp-local/site/wp-content/themes/`에 심볼릭 링크되어 있어 파일 수정이 즉시 반영된다.
- wp-cli 사용: `.wp-local/wp <command>` (예: `post create`, `theme activate`).
- 새 맥에서는 `.wp-local`이 없으므로 같은 방식으로 재구성해야 한다.

## 배포

- 실서버 배포는 zip 업로드 방식: `releases/<slug>/<slug>-<버전>.zip` (예: `releases/fv-article-news/fv-article-news-0.4.4.zip`). zip 안 최상위 폴더 = 테마 slug.
- 릴리즈마다 `themes/<slug>/style.css`의 `Version:`을 올리고 같은 버전으로 zip을 새로 만든다. 이전 버전 zip은 롤백용으로 남긴다.
- zip은 `.gitignore`(*.zip) 대상이라 저장소에는 들어가지 않는다.
- 새 사이트 세팅 시 `pages/terms.html`, `pages/privacy.html` 내용으로 슬러그 `terms`, `privacy` 페이지를 만들어야 푸터 링크가 연결된다 (페이지는 DB 콘텐츠라 테마 zip에 미포함).

## UI 작업 방식

- 에이전트(Claude)와의 대화를 통해 UI를 점진적으로 구축한다.
- 레퍼런스 이미지를 제공하면 이를 참고해 구현한다.
