<?php
/**
 * FV Article Stream theme functions. (fv-article-reader에서 복제한 무한 스트림형 테마 — 기사 상세에서 이전 글을 AJAX로 이어 붙인다)
 *
 * @package fv-article-stream
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function fvarticle_stream_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );

	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'fv-article-stream' ),
		)
	);
}
add_action( 'after_setup_theme', 'fvarticle_stream_setup' );

// 원본 영상 URL 메타. 발행 파이프라인(백엔드)이 REST API의 meta 필드로 보내므로
// show_in_rest 등록이 없으면 저장 단계에서 조용히 버려진다. 키명은 백엔드와 합의한 값.
function fvarticle_stream_register_meta() {
	register_post_meta(
		'post',
		'source_video_url',
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'esc_url_raw',
		)
	);
}
add_action( 'init', 'fvarticle_stream_register_meta' );

function fvarticle_stream_enqueue() {
	wp_enqueue_style(
		'fv-article-stream-style',
		get_stylesheet_uri(),
		array(),
		wp_get_theme()->get( 'Version' )
	);

	$palette_css = fvarticle_stream_palette_css();
	if ( $palette_css ) {
		wp_add_inline_style( 'fv-article-stream-style', $palette_css );
	}

	if ( is_singular( 'post' ) ) {
		wp_enqueue_script(
			'fv-article-stream',
			get_theme_file_uri( 'assets/stream.js' ),
			array(),
			wp_get_theme()->get( 'Version' ),
			array(
				'in_footer' => true,
				'strategy'  => 'defer',
			)
		);
		wp_localize_script(
			'fv-article-stream',
			'fvStream',
			array(
				'endpoint' => esc_url_raw( rest_url( 'fv-article-stream/v1/next' ) ),
				'autoMax'  => 5,
			)
		);
	}
}
add_action( 'wp_enqueue_scripts', 'fvarticle_stream_enqueue' );

// ---------- 색상 팔레트 (외모 > 사용자 정의하기 > 색상 팔레트) ----------

// 팔레트 데이터는 inc/palettes.php (shared/palettes.json에서 tools/build-palettes.py로 생성).
// 테마가 쓰는 글자/배경 조합이 모두 WCAG AA(4.5:1) 이상인 팔레트만 들어 있다. 자유 색상 선택은 대비를 보장할 수 없어 두지 않는다.
function fvarticle_stream_palettes() {
	static $palettes = null;
	if ( null === $palettes ) {
		$palettes = require get_theme_file_path( 'inc/palettes.php' );
	}
	return $palettes;
}

// 기본 팔레트: style.css :root 값과 같은 팔레트.
function fvarticle_stream_default_palette() {
	return 'burgundy';
}

function fvarticle_stream_sanitize_palette( $value ) {
	$palettes = fvarticle_stream_palettes();
	return isset( $palettes[ $value ] ) ? $value : fvarticle_stream_default_palette();
}

// 색 모드: light = 라이트만, dark = 다크만, auto = 방문자 OS 설정(prefers-color-scheme)을 따른다.
function fvarticle_stream_color_modes() {
	return array(
		'light' => '라이트',
		'dark'  => '다크',
		'auto'  => 'OS 설정 따르기 (기기가 다크 모드면 다크)',
	);
}

function fvarticle_stream_sanitize_color_mode( $value ) {
	return array_key_exists( $value, fvarticle_stream_color_modes() ) ? $value : 'light';
}

function fvarticle_stream_css_vars( $colors ) {
	$css = '';
	foreach ( $colors as $name => $value ) {
		$css .= '--' . $name . ':' . $value . ';';
	}
	return $css;
}

// style.css :root는 기본 팔레트의 라이트 값. 다른 팔레트면 라이트 값을 덮어쓰고,
// 다크/OS 모드면 같은 변수를 다크 값으로 덮어쓴다 (OS 모드는 미디어 쿼리 안에서만).
function fvarticle_stream_palette_css() {
	$key     = fvarticle_stream_sanitize_palette( get_theme_mod( 'color_palette' ) );
	$mode    = fvarticle_stream_sanitize_color_mode( get_theme_mod( 'color_mode' ) );
	$palette = fvarticle_stream_palettes()[ $key ];
	$css     = '';

	if ( fvarticle_stream_default_palette() !== $key ) {
		$css .= ':root{' . fvarticle_stream_css_vars( $palette['colors'] ) . '}';
	}

	$dark = ':root{' . fvarticle_stream_css_vars( $palette['dark'] ) . 'color-scheme:dark;}';
	if ( 'dark' === $mode ) {
		$css .= $dark;
	} elseif ( 'auto' === $mode ) {
		$css .= '@media (prefers-color-scheme: dark){' . $dark . '}';
	}
	return $css;
}

function fvarticle_stream_customize_register( $wp_customize ) {
	require_once get_theme_file_path( 'inc/palette-control.php' );

	$wp_customize->add_section(
		'fvarticle_stream_palette',
		array(
			'title'       => '색상 팔레트',
			'description' => '채널 분위기에 맞는 색 조합을 고르세요. 모든 조합은 글자 대비 기준(WCAG AA)을 통과한 값입니다.',
			'priority'    => 30,
		)
	);
	$wp_customize->add_setting(
		'color_mode',
		array(
			'default'           => 'light',
			'sanitize_callback' => 'fvarticle_stream_sanitize_color_mode',
		)
	);
	$wp_customize->add_control(
		'color_mode',
		array(
			'type'        => 'radio',
			'section'     => 'fvarticle_stream_palette',
			'label'       => '모드',
			'description' => '다크 모드에서는 애드센스 광고(흰 바탕)가 밝게 튀어 보일 수 있습니다.',
			'choices'     => fvarticle_stream_color_modes(),
		)
	);
	$wp_customize->add_setting(
		'color_palette',
		array(
			'default'           => fvarticle_stream_default_palette(),
			'sanitize_callback' => 'fvarticle_stream_sanitize_palette',
		)
	);
	$wp_customize->add_control(
		new FV_Palette_Control(
			$wp_customize,
			'color_palette',
			array(
				'section'     => 'fvarticle_stream_palette',
				'label'       => '팔레트',
				'palettes'    => fvarticle_stream_palettes(),
				'default_key' => fvarticle_stream_default_palette(),
			)
		)
	);
}
add_action( 'customize_register', 'fvarticle_stream_customize_register' );

// 홈 /page/N 404 방지: 404 판정은 메인 쿼리 기준인데 front-page의 최신 기사는
// 커스텀 쿼리(5개/페이지)라 페이지 수가 어긋난다. 메인 쿼리도 5개로 맞춘다.
function fvarticle_stream_home_pagination( $query ) {
	if ( ! is_admin() && $query->is_main_query() && $query->is_home() ) {
		$query->set( 'posts_per_page', 5 );
	}
}
add_action( 'pre_get_posts', 'fvarticle_stream_home_pagination' );

// 유튜브 URL(watch/youtu.be/shorts/embed)에서 임베드 URL 생성. 유튜브가 아니면 빈 문자열.
function fvarticle_stream_youtube_embed_url( $url ) {
	if ( ! $url || ! preg_match( '~(?:youtube\.com/(?:watch\?v=|shorts/|embed/)|youtu\.be/)([\w-]{6,})~', $url, $m ) ) {
		return '';
	}
	return 'https://www.youtube.com/embed/' . $m[1];
}

// 썸네일 박스용 블러 배경: 세로형(쇼츠) 썸네일도 잘림 없이 박스를 채우도록
// 같은 이미지를 CSS 변수로 전달한다 (style.css의 ::before 블러 레이어가 사용).
// $post 생략 시 루프의 현재 글 (기사 상세의 다음 기사 카드는 글 객체를 넘긴다).
function fvarticle_stream_thumb_style_attr( $post = null ) {
	$url = get_the_post_thumbnail_url( $post, 'medium' );
	if ( $url ) {
		echo ' style="--thumb:url(\'' . esc_url( $url ) . '\')"';
	}
}

// 목록 요약문: 본문에서 자동 추출, 30단어 + 말줄임.
function fvarticle_stream_excerpt_length( $length ) {
	return 30;
}
add_filter( 'excerpt_length', 'fvarticle_stream_excerpt_length' );

function fvarticle_stream_excerpt_more( $more ) {
	return '…';
}
add_filter( 'excerpt_more', 'fvarticle_stream_excerpt_more' );

// 기사 본문 중간 광고 자리: 3번째 문단 뒤에 플레이스홀더 삽입 (문단이 4개 미만이면 생략).
// 스트림으로 이어 붙이는 기사(REST 렌더링)는 is_singular/메인 쿼리 조건이 맞지 않으므로
// the_content 필터 대신 template-parts/stream-article.php에서 직접 호출한다.
function fvarticle_stream_incontent_ad( $content ) {
	$pos = 0;
	for ( $i = 0; $i < 3; $i++ ) {
		$pos = strpos( $content, '</p>', $pos );
		if ( false === $pos ) {
			return $content;
		}
		$pos += 4;
	}
	if ( false === strpos( $content, '</p>', $pos ) ) {
		return $content;
	}
	$ad = '<div class="ad-slot ad-slot-incontent"></div>';
	return substr_replace( $content, $ad, $pos, 0 );
}

// ---------- 기사 스트림 (기사 상세 무한 로딩) ----------
// 기사 끝에 닿으면 assets/stream.js가 /wp-json/fv-article-stream/v1/next?after=<ID>로
// 바로 이전 발행글을 요청하고, 서버가 렌더링한 기사 HTML을 아래에 이어 붙인다.

// 바로 이전 발행글. 발행 시각이 같은 글이 많아(일괄 발행) get_adjacent_post처럼 시각만 비교하면
// 같은 초의 글을 건너뛰므로, (발행 시각, ID) 내림차순으로 바로 다음 순서의 글을 찾는다.
function fvarticle_stream_previous_post( $current ) {
	global $wpdb;
	$id = $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts}
			WHERE post_type = 'post' AND post_status = 'publish'
			AND ( post_date < %s OR ( post_date = %s AND ID < %d ) )
			ORDER BY post_date DESC, ID DESC
			LIMIT 1",
			$current->post_date,
			$current->post_date,
			$current->ID
		)
	);
	return $id ? get_post( $id ) : null;
}

function fvarticle_stream_register_route() {
	register_rest_route(
		'fv-article-stream/v1',
		'/next',
		array(
			'methods'             => 'GET',
			'callback'            => 'fvarticle_stream_next',
			'permission_callback' => '__return_true',
			'args'                => array(
				'after' => array(
					'required'          => true,
					'sanitize_callback' => 'absint',
				),
			),
		)
	);
}
add_action( 'rest_api_init', 'fvarticle_stream_register_route' );

// 응답: 이어 붙일 기사 HTML + 주소창/탭 제목 갱신용 정보 + 그다음 글 주소(없으면 null = 스트림 끝).
function fvarticle_stream_next( WP_REST_Request $request ) {
	$current = get_post( $request['after'] );
	if ( ! $current || 'post' !== $current->post_type || 'publish' !== $current->post_status ) {
		return new WP_Error( 'fv_stream_not_found', '기사를 찾을 수 없습니다.', array( 'status' => 404 ) );
	}

	$next = fvarticle_stream_previous_post( $current );
	if ( ! $next ) {
		return rest_ensure_response( array( 'done' => true ) );
	}

	$query = new WP_Query(
		array(
			'p'         => $next->ID,
			'post_type' => 'post',
		)
	);
	ob_start();
	while ( $query->have_posts() ) {
		$query->the_post();
		get_template_part( 'template-parts/stream-article' );
	}
	wp_reset_postdata();
	$html = ob_get_clean();

	$after_next = fvarticle_stream_previous_post( $next );

	return rest_ensure_response(
		array(
			'done'     => false,
			'id'       => $next->ID,
			'url'      => get_permalink( $next ),
			'title'    => html_entity_decode( get_the_title( $next ) . ' – ' . get_bloginfo( 'name' ), ENT_QUOTES, 'UTF-8' ),
			'html'     => $html,
			'next_url' => $after_next ? get_permalink( $after_next ) : null,
		)
	);
}

// ---------- MSN 공급 피드 (/feed/ms) ----------

function fvarticle_stream_register_ms_feed() {
	add_feed( 'ms', 'fvarticle_stream_render_ms_feed' );
}
add_action( 'init', 'fvarticle_stream_register_ms_feed' );

function fvarticle_stream_render_ms_feed() {
	get_template_part( 'rss', 'ms' );
}

// add_feed는 리라이트 규칙이라 테마 활성화 시 flush해야 /feed/ms URL이 열린다.
function fvarticle_stream_flush_rewrites() {
	fvarticle_stream_register_ms_feed();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'fvarticle_stream_flush_rewrites' );

// ms 피드 수정모드 지원: 최초 저장 시의 수정시간을 postmeta에 보관.
function fvarticle_stream_save_initial_modified_time( $post_id ) {
	if ( get_post_meta( $post_id, '_initial_modified_time', true ) == '' ) {
		$modified_time = get_post_modified_time( 'Y-m-d H:i:s', true, $post_id );
		update_post_meta( $post_id, '_initial_modified_time', $modified_time );
	}
}
add_action( 'save_post', 'fvarticle_stream_save_initial_modified_time' );

// ---------- 채널 설정 (어드민 > 채널 설정에서 일괄 관리) ----------

// 옵션 키 sample01_channel_options는 fv-article-news·reader·rail과 공유한다 — 같은 사이트에서 테마를 바꿔도 채널 설정이 유지된다.

// 미저장(데모) 상태의 기본값. 저장 후에는 입력된 값만 사용되며 빈 항목의 버튼은 숨겨진다.
function fvarticle_stream_channel_defaults() {
	return array(
		'youtube_url'   => 'https://www.youtube.com/',
		'yt_channel_id' => 'UC_x5XG1OV2P6uZZ5FSM9Ttw', // 데모용 Google Developers 채널
		'yt_api_key'    => '',
		'instagram_url' => '#',
		'x_url'         => '#',
		'tiktok_url'    => '#',
		'contact_email' => 'team@fastviewkorea.com',
		'head_scripts'  => '',
	);
}

function fvarticle_stream_channel_option( $key ) {
	$saved = get_option( 'sample01_channel_options', null );
	if ( null === $saved || ! is_array( $saved ) ) {
		$defaults = fvarticle_stream_channel_defaults();
		return isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';
	}
	return isset( $saved[ $key ] ) ? $saved[ $key ] : '';
}

function fvarticle_stream_channel_settings_menu() {
	add_menu_page(
		'채널 설정',
		'채널 설정',
		'manage_options',
		'fv-article-stream-channel',
		'fvarticle_stream_channel_settings_page',
		'dashicons-video-alt3',
		61
	);
}
add_action( 'admin_menu', 'fvarticle_stream_channel_settings_menu' );

function fvarticle_stream_channel_settings_init() {
	register_setting(
		'fvarticle_stream_channel',
		'sample01_channel_options',
		array( 'sanitize_callback' => 'fvarticle_stream_channel_sanitize' )
	);
}
add_action( 'admin_init', 'fvarticle_stream_channel_settings_init' );

function fvarticle_stream_channel_sanitize( $input ) {
	fvarticle_stream_save_identity( $input );

	// 설정 저장 시 최근 영상 캐시 비우기 (채널/키 변경 즉시 반영).
	delete_transient( 'fvarticle_stream_yt_videos' );

	return array(
		'youtube_url'    => esc_url_raw( $input['youtube_url'] ?? '' ),
		'yt_channel_id'  => sanitize_text_field( $input['yt_channel_id'] ?? '' ),
		'yt_api_key'     => sanitize_text_field( $input['yt_api_key'] ?? '' ),
		'instagram_url'  => esc_url_raw( $input['instagram_url'] ?? '' ),
		'x_url'          => esc_url_raw( $input['x_url'] ?? '' ),
		'tiktok_url'     => esc_url_raw( $input['tiktok_url'] ?? '' ),
		'facebook_url'   => esc_url_raw( $input['facebook_url'] ?? '' ),
		'threads_url'    => esc_url_raw( $input['threads_url'] ?? '' ),
		'naver_blog_url' => esc_url_raw( $input['naver_blog_url'] ?? '' ),
		'naver_cafe_url' => esc_url_raw( $input['naver_cafe_url'] ?? '' ),
		'kakao_url'      => esc_url_raw( $input['kakao_url'] ?? '' ),
		'band_url'       => esc_url_raw( $input['band_url'] ?? '' ),
		'contact_email'  => sanitize_email( $input['contact_email'] ?? '' ),
		// 헤더 스크립트는 GA/애드센스 코드 원문이 필요하므로 unfiltered_html 권한자에 한해 그대로 저장.
		'head_scripts'   => current_user_can( 'unfiltered_html' ) ? trim( (string) ( $input['head_scripts'] ?? '' ) ) : fvarticle_stream_channel_option( 'head_scripts' ),
	);
}

// 채널 설정의 헤더 스크립트를 <head>에 출력 (GA, 애드센스 등).
function fvarticle_stream_output_head_scripts() {
	$scripts = fvarticle_stream_channel_option( 'head_scripts' );
	if ( $scripts ) {
		echo "\n" . $scripts . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- 관리자가 입력한 스크립트 원문.
	}
}
add_action( 'wp_head', 'fvarticle_stream_output_head_scripts', 20 );

// 채널 정보(이름·소개·프로필 이미지·파비콘)는 WP 기본 저장 위치를 그대로 쓴다 — 사용자 정의하기·설정 > 일반과 같은 값.
// 채널 설정 폼에서 온 값만 처리한다(isset). 다른 곳에서 채널 옵션을 저장할 때 지워지지 않게.
function fvarticle_stream_save_identity( $input ) {
	if ( isset( $input['blogname'] ) ) {
		update_option( 'blogname', $input['blogname'] );
	}
	if ( isset( $input['blogdescription'] ) ) {
		update_option( 'blogdescription', $input['blogdescription'] );
	}
	foreach ( array( 'custom_logo', 'site_icon' ) as $key ) {
		if ( ! isset( $input[ $key ] ) ) {
			continue;
		}
		$attachment_id = absint( $input[ $key ] );
		if ( $attachment_id && ! wp_attachment_is_image( $attachment_id ) ) {
			continue;
		}
		if ( 'custom_logo' === $key ) {
			set_theme_mod( 'custom_logo', $attachment_id );
		} else {
			update_option( 'site_icon', $attachment_id );
		}
	}
}

function fvarticle_stream_image_field( $key, $attachment_id ) {
	$src = $attachment_id ? wp_get_attachment_image_url( $attachment_id, 'medium' ) : '';
	?>
	<div class="fv-image-field">
		<input type="hidden" name="sample01_channel_options[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $src ? $attachment_id : '' ); ?>">
		<img class="fv-image-preview" src="<?php echo esc_url( $src ); ?>" alt="" style="width:64px;height:64px;object-fit:contain;border:1px solid #dcdcde;border-radius:4px;background:#fff;vertical-align:middle;margin-right:8px"<?php echo $src ? '' : ' hidden'; ?>>
		<button type="button" class="button fv-image-select">이미지 선택</button>
		<button type="button" class="button-link button-link-delete fv-image-remove" style="margin-left:8px"<?php echo $src ? '' : ' hidden'; ?>>제거</button>
	</div>
	<?php
}

function fvarticle_stream_channel_settings_enqueue( $hook ) {
	if ( 'toplevel_page_fv-article-stream-channel' !== $hook ) {
		return;
	}
	wp_enqueue_media();
	wp_enqueue_script(
		'fv-article-stream-channel-settings',
		get_theme_file_uri( 'assets/channel-settings.js' ),
		array( 'jquery' ),
		wp_get_theme()->get( 'Version' ),
		true
	);
}
add_action( 'admin_enqueue_scripts', 'fvarticle_stream_channel_settings_enqueue' );

function fvarticle_stream_channel_settings_page() {
	$fields = array(
		'youtube_url'    => array( '유튜브 채널 URL', '구독하기 버튼과 헤더 "유튜브 바로가기"에 사용됩니다.' ),
		'yt_channel_id'  => array( '유튜브 채널 ID', '"최근 업로드 영상" 위젯에 사용됩니다. UC로 시작하는 ID (유튜브 채널 > 정보 > 공유에서 확인).' ),
		'yt_api_key'     => array( 'YouTube API 키 (선택)', '비워두면 채널 RSS로 최근 영상을 가져옵니다(권장 기본값). RSS가 안 되는 환경에서만 YouTube Data API v3 키를 발급해 입력하세요.' ),
		'instagram_url'  => array( '인스타그램 URL', '비워두면 버튼이 표시되지 않습니다.' ),
		'x_url'          => array( 'X(트위터) URL', '비워두면 버튼이 표시되지 않습니다.' ),
		'tiktok_url'     => array( '틱톡 URL', '비워두면 버튼이 표시되지 않습니다.' ),
		'facebook_url'   => array( '페이스북 URL', '비워두면 버튼이 표시되지 않습니다.' ),
		'threads_url'    => array( '스레드 URL', '비워두면 버튼이 표시되지 않습니다.' ),
		'naver_blog_url' => array( '네이버 블로그 URL', '비워두면 버튼이 표시되지 않습니다.' ),
		'naver_cafe_url' => array( '네이버 카페 URL', '비워두면 버튼이 표시되지 않습니다.' ),
		'kakao_url'      => array( '카카오톡 채널 URL', 'pf.kakao.com 주소. 비워두면 버튼이 표시되지 않습니다.' ),
		'band_url'       => array( '네이버 밴드 URL', '비워두면 버튼이 표시되지 않습니다.' ),
		'contact_email'  => array( '문의 이메일', '헤더 "문의하기"와 메일 문의 버튼에 사용됩니다.' ),
		'head_scripts'   => array( '헤더 스크립트', '모든 페이지의 <head>에 그대로 출력됩니다. Google 애널리틱스, 애드센스 코드 등을 <script> 태그째 붙여넣으세요.' ),
	);
	?>
	<div class="wrap">
		<h1>채널 설정</h1>
		<p>제휴 채널에 맞게 이곳 값만 바꾸면 사이트 전체에 반영됩니다.<br>
		채널 정보는 워드프레스 기본 설정(사이트 제목·태그라인·로고·사이트 아이콘)과 같은 값이라, 사용자 정의하기에서 바꿔도 여기에 그대로 보입니다.</p>
		<form method="post" action="options.php">
			<?php settings_fields( 'fvarticle_stream_channel' ); ?>
			<h2>채널 정보</h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="fv-article-stream-blogname">채널 이름</label></th>
					<td>
						<input type="text" class="regular-text" id="fv-article-stream-blogname" name="sample01_channel_options[blogname]" value="<?php echo esc_attr( get_option( 'blogname' ) ); ?>">
						<p class="description">사이트 제목입니다. 헤더·프로필·브라우저 탭·피드 제목에 쓰입니다.</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="fv-article-stream-blogdescription">채널 소개</label></th>
					<td>
						<textarea class="large-text" rows="3" id="fv-article-stream-blogdescription" name="sample01_channel_options[blogdescription]"><?php echo esc_html( get_option( 'blogdescription' ) ); // 저장값이 이미 엔티티 처리돼 있어 esc_textarea는 &#039; 를 이중 변환한다 ?></textarea>
						<p class="description">태그라인입니다. 홈 프로필 소개문과 피드 설명에 쓰입니다.</p>
					</td>
				</tr>
				<tr>
					<th scope="row">프로필 이미지</th>
					<td>
						<?php fvarticle_stream_image_field( 'custom_logo', (int) get_theme_mod( 'custom_logo' ) ); ?>
						<p class="description">로고입니다. 홈 프로필 아바타 등에 쓰입니다. 비우면 채널 이름 첫 글자가 보입니다.</p>
					</td>
				</tr>
				<tr>
					<th scope="row">파비콘</th>
					<td>
						<?php fvarticle_stream_image_field( 'site_icon', (int) get_option( 'site_icon' ) ); ?>
						<p class="description">사이트 아이콘입니다. 브라우저 탭·북마크에 보입니다. 512×512px 이상 정사각형 이미지를 권장합니다.</p>
					</td>
				</tr>
			</table>

			<h2>링크·연동</h2>
			<table class="form-table" role="presentation">
				<?php foreach ( $fields as $key => $label ) : ?>
					<tr>
						<th scope="row"><label for="fv-article-stream-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label[0] ); ?></label></th>
						<td>
							<?php if ( 'head_scripts' === $key ) : ?>
								<textarea class="large-text code" rows="8" id="fv-article-stream-<?php echo esc_attr( $key ); ?>"
									name="sample01_channel_options[<?php echo esc_attr( $key ); ?>]"><?php echo esc_textarea( fvarticle_stream_channel_option( $key ) ); ?></textarea>
							<?php else : ?>
								<input type="text" class="regular-text" id="fv-article-stream-<?php echo esc_attr( $key ); ?>"
									name="sample01_channel_options[<?php echo esc_attr( $key ); ?>]"
									value="<?php echo esc_attr( fvarticle_stream_channel_option( $key ) ); ?>">
							<?php endif; ?>
							<p class="description"><?php echo esc_html( $label[1] ); ?></p>
						</td>
					</tr>
				<?php endforeach; ?>
			</table>
			<?php submit_button(); ?>
		</form>

		<hr>
		<h2>썸네일 일괄 생성</h2>
		<p>대표 이미지가 없는 글의 본문 첫 이미지를 대표 이미지로 등록합니다. (테마 설치 전에 발행된 글 처리용 · 한 번에 30개씩)</p>
		<?php if ( isset( $_GET['thumb_done'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
			<div class="notice notice-info"><p>
				<?php
				printf(
					'완료: %d개 등록, %d개 실패(본문에 이미지가 없거나 다운로드 실패), 남은 글 %d개.',
					(int) ( $_GET['thumb_ok'] ?? 0 ),   // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					(int) ( $_GET['thumb_skip'] ?? 0 ), // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					(int) ( $_GET['thumb_left'] ?? 0 )  // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				);
				?>
			</p></div>
		<?php endif; ?>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="fvarticle_stream_backfill_thumbs">
			<?php wp_nonce_field( 'fvarticle_stream_backfill_thumbs' ); ?>
			<?php submit_button( '대표 이미지 없는 글 처리하기', 'secondary' ); ?>
		</form>
	</div>
	<?php
}

// 대표 이미지 없는 글 일괄 처리 (30개씩, 버튼 반복 클릭으로 이어서 처리).
function fvarticle_stream_backfill_thumbs_handler() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( '권한이 없습니다.' );
	}
	check_admin_referer( 'fvarticle_stream_backfill_thumbs' );

	$query_args = array(
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'posts_per_page' => 30,
		'fields'         => 'ids',
		'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			array(
				'key'     => '_thumbnail_id',
				'compare' => 'NOT EXISTS',
			),
		),
	);

	$ok   = 0;
	$skip = 0;
	foreach ( get_posts( $query_args ) as $post_id ) {
		if ( fvarticle_stream_set_thumbnail_from_content( $post_id ) ) {
			$ok++;
		} else {
			$skip++;
		}
	}

	$left = count( get_posts( $query_args ) );

	wp_safe_redirect(
		add_query_arg(
			array(
				'page'       => 'fv-article-stream-channel',
				'thumb_done' => 1,
				'thumb_ok'   => $ok,
				'thumb_skip' => $skip,
				'thumb_left' => $left,
			),
			admin_url( 'admin.php' )
		)
	);
	exit;
}
add_action( 'admin_post_fvarticle_stream_backfill_thumbs', 'fvarticle_stream_backfill_thumbs_handler' );

// 채널 최근 영상 목록. API 키가 있으면 Data API v3, 없으면 RSS 폴백. 12시간 캐시.
function fvarticle_stream_recent_videos( $count = 4 ) {
	$channel_id = fvarticle_stream_channel_option( 'yt_channel_id' );
	if ( ! $channel_id ) {
		return array();
	}

	$api_key = fvarticle_stream_channel_option( 'yt_api_key' );
	if ( $api_key ) {
		return fvarticle_stream_recent_videos_api( $channel_id, $api_key, $count );
	}

	add_filter( 'wp_feed_cache_transient_lifetime', 'fvarticle_stream_feed_cache_lifetime' );
	$feed = fetch_feed( 'https://www.youtube.com/feeds/videos.xml?channel_id=' . rawurlencode( $channel_id ) );
	remove_filter( 'wp_feed_cache_transient_lifetime', 'fvarticle_stream_feed_cache_lifetime' );

	if ( is_wp_error( $feed ) ) {
		return array();
	}

	$videos = array();
	foreach ( $feed->get_items( 0, $count ) as $item ) {
		$link = $item->get_permalink();
		if ( ! preg_match( '~[?&]v=([\w-]+)|/shorts/([\w-]+)~', $link, $m ) ) {
			continue;
		}
		$video_id  = ! empty( $m[1] ) ? $m[1] : $m[2];
		$videos[] = array(
			'title' => $item->get_title(),
			'url'   => $link,
			'thumb' => 'https://i.ytimg.com/vi/' . $video_id . '/mqdefault.jpg',
		);
	}
	return $videos;
}

function fvarticle_stream_feed_cache_lifetime() {
	return 12 * HOUR_IN_SECONDS;
}

// 유튜브 피드 요청은 IPv4 강제 — 일부 네트워크에서 IPv6 경로의 구글 엣지가 404/500을 내는 문제 회피.
function fvarticle_stream_force_ipv4_for_youtube( $handle, $parsed_args, $url ) {
	if ( false !== strpos( (string) $url, 'youtube.com/feeds' ) && defined( 'CURL_IPRESOLVE_V4' ) ) {
		curl_setopt( $handle, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
	}
}
add_action( 'http_api_curl', 'fvarticle_stream_force_ipv4_for_youtube', 10, 3 );

// Data API v3: 채널 업로드 재생목록(UU + 채널ID 뒷부분)에서 최근 영상 조회.
function fvarticle_stream_recent_videos_api( $channel_id, $api_key, $count ) {
	$cached = get_transient( 'fvarticle_stream_yt_videos' );
	if ( false !== $cached ) {
		return array_slice( $cached, 0, $count );
	}

	$response = wp_remote_get(
		add_query_arg(
			array(
				'part'       => 'snippet',
				'playlistId' => 'UU' . substr( $channel_id, 2 ),
				'maxResults' => 10,
				'key'        => $api_key,
			),
			'https://www.googleapis.com/youtube/v3/playlistItems'
		),
		array( 'timeout' => 10 )
	);

	if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
		// 실패도 1시간 캐시해 방문마다 재호출하지 않는다.
		set_transient( 'fvarticle_stream_yt_videos', array(), HOUR_IN_SECONDS );
		return array();
	}

	$data   = json_decode( wp_remote_retrieve_body( $response ), true );
	$videos = array();
	foreach ( (array) ( $data['items'] ?? array() ) as $item ) {
		$snippet  = $item['snippet'] ?? array();
		$video_id = $snippet['resourceId']['videoId'] ?? '';
		if ( ! $video_id ) {
			continue;
		}
		$videos[] = array(
			'title' => $snippet['title'] ?? '',
			'url'   => 'https://www.youtube.com/watch?v=' . $video_id,
			'thumb' => $snippet['thumbnails']['medium']['url'] ?? 'https://i.ytimg.com/vi/' . $video_id . '/mqdefault.jpg',
		);
	}

	set_transient( 'fvarticle_stream_yt_videos', $videos, 12 * HOUR_IN_SECONDS );
	return array_slice( $videos, 0, $count );
}

// 대표 이미지가 없으면 본문 첫 번째 이미지를 대표 이미지로 자동 등록.
function fvarticle_stream_set_thumbnail_from_content( $post_id ) {
	if ( has_post_thumbnail( $post_id ) ) {
		return false;
	}

	$content = get_post_field( 'post_content', $post_id );
	if ( ! preg_match( '/<img[^>]+src=["\']([^"\']+)["\']/i', $content, $m ) ) {
		return false;
	}
	$src = $m[1];

	// 1) 본문 img의 wp-image-{ID} 클래스에서 첨부 ID 추출.
	$attachment_id = 0;
	if ( preg_match( '/wp-image-(\d+)/', $m[0], $cm ) ) {
		$attachment_id = (int) $cm[1];
	}

	// 2) URL로 미디어 라이브러리에서 역조회 (크기 접미사 제거).
	if ( ! $attachment_id ) {
		$lookup        = preg_replace( '/-\d+x\d+(\.\w+)$/', '$1', $src );
		$attachment_id = attachment_url_to_postid( $lookup );

		// CDN 도메인으로 재작성된 URL이면 로컬 업로드 URL로 되돌려 재시도.
		if ( ! $attachment_id && preg_match( '~/wp-content/uploads/(.+)$~', $lookup, $pm ) ) {
			$attachment_id = attachment_url_to_postid( trailingslashit( wp_get_upload_dir()['baseurl'] ) . $pm[1] );
		}
	}

	// 3) 외부 이미지면 미디어 라이브러리로 가져온다.
	if ( ! $attachment_id ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';
		$sideloaded = media_sideload_image( $src, $post_id, null, 'id' );
		if ( ! is_wp_error( $sideloaded ) ) {
			$attachment_id = $sideloaded;
		}
	}

	if ( $attachment_id && wp_attachment_is_image( $attachment_id ) ) {
		return set_post_thumbnail( $post_id, $attachment_id );
	}
	return false;
}

function fvarticle_stream_auto_thumbnail_on_save( $post_id, $post ) {
	if ( wp_is_post_revision( $post_id ) || 'auto-draft' === $post->post_status ) {
		return;
	}
	fvarticle_stream_set_thumbnail_from_content( $post_id );
}
add_action( 'save_post_post', 'fvarticle_stream_auto_thumbnail_on_save', 20, 2 );

// 어드민 글 목록에 썸네일 미리보기 컬럼 추가.
function fvarticle_stream_admin_thumb_column( $columns ) {
	$new = array();
	foreach ( $columns as $key => $label ) {
		if ( 'title' === $key ) {
			$new['thumb'] = __( '썸네일', 'fv-article-stream' );
		}
		$new[ $key ] = $label;
	}
	return $new;
}
add_filter( 'manage_post_posts_columns', 'fvarticle_stream_admin_thumb_column' );

function fvarticle_stream_admin_thumb_column_content( $column, $post_id ) {
	if ( 'thumb' === $column ) {
		if ( has_post_thumbnail( $post_id ) ) {
			echo get_the_post_thumbnail( $post_id, array( 80, 50 ) );
		} else {
			echo '&mdash;';
		}
	}
}
add_action( 'manage_post_posts_custom_column', 'fvarticle_stream_admin_thumb_column_content', 10, 2 );

function fvarticle_stream_admin_thumb_column_css() {
	echo '<style>.column-thumb{width:96px}.column-thumb img{width:80px;height:50px;object-fit:cover;border-radius:3px;display:block}</style>';
}
add_action( 'admin_head-edit.php', 'fvarticle_stream_admin_thumb_column_css' );
