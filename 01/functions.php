<?php
/**
 * Sample 01 theme functions.
 *
 * @package sample-01
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function sample01_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );

	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'sample-01' ),
		)
	);
}
add_action( 'after_setup_theme', 'sample01_setup' );

function sample01_enqueue() {
	wp_enqueue_style(
		'sample-01-style',
		get_stylesheet_uri(),
		array(),
		wp_get_theme()->get( 'Version' )
	);
}
add_action( 'wp_enqueue_scripts', 'sample01_enqueue' );

// 목록 요약문: 본문에서 자동 추출, 30단어 + 말줄임.
function sample01_excerpt_length( $length ) {
	return 30;
}
add_filter( 'excerpt_length', 'sample01_excerpt_length' );

function sample01_excerpt_more( $more ) {
	return '…';
}
add_filter( 'excerpt_more', 'sample01_excerpt_more' );

// ---------- 채널 설정 (어드민 > 채널 설정에서 일괄 관리) ----------

// 미저장(데모) 상태의 기본값. 저장 후에는 입력된 값만 사용되며 빈 항목의 버튼은 숨겨진다.
function sample01_channel_defaults() {
	return array(
		'youtube_url'   => 'https://www.youtube.com/',
		'yt_channel_id' => 'UC_x5XG1OV2P6uZZ5FSM9Ttw', // 데모용 Google Developers 채널
		'instagram_url' => '#',
		'x_url'         => '#',
		'contact_email' => 'team@fastviewkorea.com',
	);
}

function sample01_channel_option( $key ) {
	$saved = get_option( 'sample01_channel_options', null );
	if ( null === $saved || ! is_array( $saved ) ) {
		$defaults = sample01_channel_defaults();
		return isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';
	}
	return isset( $saved[ $key ] ) ? $saved[ $key ] : '';
}

function sample01_channel_settings_menu() {
	add_menu_page(
		'채널 설정',
		'채널 설정',
		'manage_options',
		'sample01-channel',
		'sample01_channel_settings_page',
		'dashicons-video-alt3',
		61
	);
}
add_action( 'admin_menu', 'sample01_channel_settings_menu' );

function sample01_channel_settings_init() {
	register_setting(
		'sample01_channel',
		'sample01_channel_options',
		array( 'sanitize_callback' => 'sample01_channel_sanitize' )
	);
}
add_action( 'admin_init', 'sample01_channel_settings_init' );

function sample01_channel_sanitize( $input ) {
	return array(
		'youtube_url'   => esc_url_raw( $input['youtube_url'] ?? '' ),
		'yt_channel_id' => sanitize_text_field( $input['yt_channel_id'] ?? '' ),
		'instagram_url' => esc_url_raw( $input['instagram_url'] ?? '' ),
		'x_url'         => esc_url_raw( $input['x_url'] ?? '' ),
		'contact_email' => sanitize_email( $input['contact_email'] ?? '' ),
	);
}

function sample01_channel_settings_page() {
	$fields = array(
		'youtube_url'   => array( '유튜브 채널 URL', '구독하기 버튼과 헤더 "유튜브 바로가기"에 사용됩니다.' ),
		'yt_channel_id' => array( '유튜브 채널 ID', '"최근 업로드 영상" 위젯에 사용됩니다. UC로 시작하는 ID (유튜브 채널 > 정보 > 공유에서 확인).' ),
		'instagram_url' => array( '인스타그램 URL', '비워두면 버튼이 표시되지 않습니다.' ),
		'x_url'         => array( 'X(트위터) URL', '비워두면 버튼이 표시되지 않습니다.' ),
		'contact_email' => array( '문의 이메일', '헤더 "문의하기"와 메일 문의 버튼에 사용됩니다.' ),
	);
	?>
	<div class="wrap">
		<h1>채널 설정</h1>
		<p>제휴 채널에 맞게 이곳 값만 바꾸면 사이트 전체에 반영됩니다.<br>
		채널명·소개문은 <a href="<?php echo esc_url( admin_url( 'options-general.php' ) ); ?>">설정 &gt; 일반</a>(사이트 제목·태그라인),
		프로필 아바타는 <a href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>">사용자 정의하기</a>(로고)에서 변경합니다.</p>
		<form method="post" action="options.php">
			<?php settings_fields( 'sample01_channel' ); ?>
			<table class="form-table" role="presentation">
				<?php foreach ( $fields as $key => $label ) : ?>
					<tr>
						<th scope="row"><label for="sample01-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label[0] ); ?></label></th>
						<td>
							<input type="text" class="regular-text" id="sample01-<?php echo esc_attr( $key ); ?>"
								name="sample01_channel_options[<?php echo esc_attr( $key ); ?>]"
								value="<?php echo esc_attr( sample01_channel_option( $key ) ); ?>">
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
			<input type="hidden" name="action" value="sample01_backfill_thumbs">
			<?php wp_nonce_field( 'sample01_backfill_thumbs' ); ?>
			<?php submit_button( '대표 이미지 없는 글 처리하기', 'secondary' ); ?>
		</form>
	</div>
	<?php
}

// 대표 이미지 없는 글 일괄 처리 (30개씩, 버튼 반복 클릭으로 이어서 처리).
function sample01_backfill_thumbs_handler() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( '권한이 없습니다.' );
	}
	check_admin_referer( 'sample01_backfill_thumbs' );

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
		if ( sample01_set_thumbnail_from_content( $post_id ) ) {
			$ok++;
		} else {
			$skip++;
		}
	}

	$left = count( get_posts( $query_args ) );

	wp_safe_redirect(
		add_query_arg(
			array(
				'page'       => 'sample01-channel',
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
add_action( 'admin_post_sample01_backfill_thumbs', 'sample01_backfill_thumbs_handler' );

// 채널 RSS(키 불필요)에서 최근 영상 목록을 가져온다. 12시간 캐시.
function sample01_recent_videos( $count = 4 ) {
	$channel_id = sample01_channel_option( 'yt_channel_id' );
	if ( ! $channel_id ) {
		return array();
	}

	add_filter( 'wp_feed_cache_transient_lifetime', 'sample01_feed_cache_lifetime' );
	$feed = fetch_feed( 'https://www.youtube.com/feeds/videos.xml?channel_id=' . rawurlencode( $channel_id ) );
	remove_filter( 'wp_feed_cache_transient_lifetime', 'sample01_feed_cache_lifetime' );

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

function sample01_feed_cache_lifetime() {
	return 12 * HOUR_IN_SECONDS;
}

// 대표 이미지가 없으면 본문 첫 번째 이미지를 대표 이미지로 자동 등록.
function sample01_set_thumbnail_from_content( $post_id ) {
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

function sample01_auto_thumbnail_on_save( $post_id, $post ) {
	if ( wp_is_post_revision( $post_id ) || 'auto-draft' === $post->post_status ) {
		return;
	}
	sample01_set_thumbnail_from_content( $post_id );
}
add_action( 'save_post_post', 'sample01_auto_thumbnail_on_save', 20, 2 );

// 어드민 글 목록에 썸네일 미리보기 컬럼 추가.
function sample01_admin_thumb_column( $columns ) {
	$new = array();
	foreach ( $columns as $key => $label ) {
		if ( 'title' === $key ) {
			$new['thumb'] = __( '썸네일', 'sample-01' );
		}
		$new[ $key ] = $label;
	}
	return $new;
}
add_filter( 'manage_post_posts_columns', 'sample01_admin_thumb_column' );

function sample01_admin_thumb_column_content( $column, $post_id ) {
	if ( 'thumb' === $column ) {
		if ( has_post_thumbnail( $post_id ) ) {
			echo get_the_post_thumbnail( $post_id, array( 80, 50 ) );
		} else {
			echo '&mdash;';
		}
	}
}
add_action( 'manage_post_posts_custom_column', 'sample01_admin_thumb_column_content', 10, 2 );

function sample01_admin_thumb_column_css() {
	echo '<style>.column-thumb{width:96px}.column-thumb img{width:80px;height:50px;object-fit:cover;border-radius:3px;display:block}</style>';
}
add_action( 'admin_head-edit.php', 'sample01_admin_thumb_column_css' );
