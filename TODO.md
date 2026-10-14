# TODO

## DB 부하 개선 (2026-10-14 점검)

점검 결과 데이터 손상·충돌 위험은 없고, 페이지 조회 시 DB 쓰기도 0건(실측).
아래는 글이 많이 쌓인 실서버에서 읽기 부하가 될 수 있는 지점이다. 보류 중.

- [ ] **05 `fv-news-portal` 사이드바 "인기 뉴스" — 인덱스 없는 정렬**
  - 위치: `themes/fv-news-portal/sidebar.php` (위젯 영역이 비어 있을 때의 기본 블록)
  - 문제: `orderby => comment_count`. wp_posts의 comment_count에는 인덱스가 없어 페이지마다 공개 글 전체를 정렬한다. 글이 수만 개면 매 페이지가 느려진다.
  - 안: 결과를 transient로 10분 캐시 + 최근 7일 글로 범위 제한(`date_query` → type_status_date 인덱스 사용).

- [ ] **02~04 페이지 번호 없는 목록의 전체 개수 쿼리**
  - 위치: reader·rail·stream의 실시간 TOP, 관련 기사, 주간 많이 본 기사 등 커스텀 `WP_Query`
  - 문제: `no_found_rows` 미지정이라 페이지당 2~4개의 `SQL_CALC_FOUND_ROWS` 카운트가 더 나간다 (MySQL 8.0.17부터 deprecated).
  - 안: 페이지 번호를 쓰지 않는 쿼리에 `'no_found_rows' => true`. (05는 이미 적용됨)

- [ ] **04 `fv-article-stream` 다음 기사 REST 엔드포인트 캐시**
  - 위치: `themes/fv-article-stream/functions.php` `fvarticle_stream_next()` — `GET /wp-json/fv-article-stream/v1/next?after=<ID>`
  - 문제: 공개·무캐시라 봇이 `after` 값을 돌며 긁으면 요청마다 기사 전체를 렌더링한다(읽기 전용, 쓰기 없음).
  - 안: 응답에 `Cache-Control: public, max-age=...` 헤더를 붙여 nginx·CDN이 캐시하게. 실서버 nginx 캐시가 REST 응답을 덮는지 먼저 확인.

### 참고 (페이지 조회와 무관, 현재 조치 불필요)

- 01~04 `save_post` 대표 이미지 자동 등록: 본문 첫 이미지가 외부 URL이면 저장 요청 중에 내려받아 미디어로 등록 → 파이프라인 대량 발행 시 발행 속도 저하 가능.
- 05 위젯: 01~04로 테마를 바꾸면 사이드바 위젯이 "비활성 위젯"으로 이동(삭제 아님). 다시 05로 오면 자동 복귀가 안 될 수 있어 외모 > 위젯에서 재배치 필요.
