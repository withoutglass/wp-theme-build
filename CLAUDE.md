# wp-theme-build

워드프레스 신규 테마를 만드는 실험/구축 프로젝트.

## 목표

여러 가지 워드프레스 테마를 만들어보고 비교·발전시킨다.

## 디렉토리 구조

- 테마는 `01`, `02`, `03` … 식으로 넘버링한 폴더 단위로 만든다.
- 각 넘버 폴더 = 하나의 독립적인 테마 시도.
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
- `00/`, `01/`은 `.wp-local/site/wp-content/themes/`에 심볼릭 링크되어 있어 파일 수정이 즉시 반영된다.
- wp-cli 사용: `.wp-local/wp <command>` (예: `post create`, `theme activate`).
- 새 맥에서는 `.wp-local`이 없으므로 같은 방식으로 재구성해야 한다.

## 배포 (01 테마)

- 실서버 배포는 zip 업로드 방식: `01-release-<버전>.zip` (예: `01-release-0.2.0.zip`).
- 릴리즈마다 `01/style.css`의 `Version:`을 올리고 같은 버전으로 zip을 새로 만든다. 이전 버전 zip은 롤백용으로 남긴다.
- zip은 `.gitignore`(*.zip) 대상이라 저장소에는 들어가지 않는다.
- 새 사이트 세팅 시 `pages/terms.html`, `pages/privacy.html` 내용으로 슬러그 `terms`, `privacy` 페이지를 만들어야 푸터 링크가 연결된다 (페이지는 DB 콘텐츠라 테마 zip에 미포함).

## UI 작업 방식

- 에이전트(Claude)와의 대화를 통해 UI를 점진적으로 구축한다.
- 레퍼런스 이미지를 제공하면 이를 참고해 구현한다.
