<?php
/**
 * FV News Portal theme functions. 범용 포털형 뉴스 테마 (00 베이스라인에서 출발한 zero-base 클래식 테마).
 *
 * @package fv-news-portal
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function fvnews_portal_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 80,
			'width'       => 320,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	register_nav_menus(
		array(
			'primary' => '주 메뉴 (헤더)',
			'footer'  => '푸터 메뉴',
		)
	);
}
add_action( 'after_setup_theme', 'fvnews_portal_setup' );

function fvnews_portal_widgets_init() {
	register_sidebar(
		array(
			'name'          => '사이드바',
			'id'            => 'sidebar-1',
			'description'   => '비워두면 인기 뉴스·최신 뉴스가 기본으로 표시됩니다.',
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'fvnews_portal_widgets_init' );

function fvnews_portal_enqueue() {
	wp_enqueue_style(
		'fv-news-portal-style',
		get_stylesheet_uri(),
		array(),
		wp_get_theme()->get( 'Version' )
	);

	$accent_css = fvnews_portal_accent_css();
	if ( $accent_css ) {
		wp_add_inline_style( 'fv-news-portal-style', $accent_css );
	}

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'fvnews_portal_enqueue' );

function fvnews_portal_excerpt_length() {
	return 40;
}
add_filter( 'excerpt_length', 'fvnews_portal_excerpt_length' );

function fvnews_portal_excerpt_more() {
	return '…';
}
add_filter( 'excerpt_more', 'fvnews_portal_excerpt_more' );

// 목록 제목에서 "카테고리:" 같은 접두어를 뺀다 (한국어 번역은 "[카테고리:] 테크"처럼 나온다). 제목만 크게 보여주는 게 뉴스 사이트 관례.
add_filter( 'get_the_archive_title_prefix', '__return_empty_string' );

// 기자 목록 제목은 바이라인과 같이 "이름 기자".
function fvnews_portal_author_archive_title( $title ) {
	return is_author() ? $title . ' 기자' : $title;
}
add_filter( 'get_the_archive_title', 'fvnews_portal_author_archive_title' );

// 첫 화면이 최신 글 목록일 때 front-page.php는 페이지 번호 없이 그리므로,
// /page/2/ 같은 주소가 홈과 같은 내용을 200으로 내지 않도록 404로 돌린다.
function fvnews_portal_front_paged_404() {
	global $wp_query;
	if ( is_front_page() && is_home() && is_paged() ) {
		$wp_query->set_404();
		status_header( 404 );
		nocache_headers();
	}
}
add_action( 'template_redirect', 'fvnews_portal_front_paged_404' );

// ---------- 색 (외모 > 사용자 정의하기 > 색상) ----------

// 포인트색 프리셋: 라이트는 흰 바탕·흰 글자 대비, 다크는 어두운 바탕·검은 글자 대비가 모두 WCAG AA(4.5:1) 이상인 값.
// 첫 항목이 기본값이며 style.css :root의 --accent-l/--accent-d와 같다.
function fvnews_portal_accents() {
	return array(
		'red'    => array( '레드', '#c8102e', '#ff6b78' ),
		'navy'   => array( '네이비', '#1d4e89', '#7fb2f0' ),
		'blue'   => array( '블루', '#0b5cad', '#6aa9ff' ),
		'green'  => array( '그린', '#0a7a4b', '#4fd39a' ),
		'orange' => array( '오렌지', '#b54708', '#ffa45c' ),
		'purple' => array( '퍼플', '#6b3fa0', '#c39bff' ),
		'teal'   => array( '틸', '#0f766e', '#4fd1c5' ),
		'black'  => array( '블랙', '#111111', '#e9eaec' ),
	);
}

function fvnews_portal_sanitize_accent( $value ) {
	$accents = fvnews_portal_accents();
	return isset( $accents[ $value ] ) ? $value : array_key_first( $accents );
}

// 모드: light = 라이트만, dark = 다크만, auto = 방문자 OS 설정(prefers-color-scheme). <html>의 mode-* 클래스로 style.css가 처리한다.
function fvnews_portal_color_modes() {
	return array(
		'light' => '라이트',
		'dark'  => '다크',
		'auto'  => 'OS 설정 따르기 (기기가 다크 모드면 다크)',
	);
}

function fvnews_portal_sanitize_color_mode( $value ) {
	return array_key_exists( $value, fvnews_portal_color_modes() ) ? $value : 'light';
}

function fvnews_portal_color_mode() {
	return fvnews_portal_sanitize_color_mode( get_theme_mod( 'color_mode' ) );
}

// 기본이 아닌 포인트색이면 라이트·다크 포인트 변수만 덮어쓴다.
function fvnews_portal_accent_css() {
	$accents = fvnews_portal_accents();
	$key     = fvnews_portal_sanitize_accent( get_theme_mod( 'accent_color' ) );
	if ( array_key_first( $accents ) === $key ) {
		return '';
	}
	return ':root{--accent-l:' . $accents[ $key ][1] . ';--accent-d:' . $accents[ $key ][2] . ';}';
}

function fvnews_portal_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'fvnews_portal_colors',
		array(
			'title'       => '색상',
			'description' => '모든 조합은 글자 대비 기준(WCAG AA)을 통과한 값입니다.',
			'priority'    => 30,
		)
	);

	$wp_customize->add_setting(
		'color_mode',
		array(
			'default'           => 'light',
			'sanitize_callback' => 'fvnews_portal_sanitize_color_mode',
		)
	);
	$wp_customize->add_control(
		'color_mode',
		array(
			'type'    => 'radio',
			'section' => 'fvnews_portal_colors',
			'label'   => '모드',
			'choices' => fvnews_portal_color_modes(),
		)
	);

	$accents = fvnews_portal_accents();
	$wp_customize->add_setting(
		'accent_color',
		array(
			'default'           => array_key_first( $accents ),
			'sanitize_callback' => 'fvnews_portal_sanitize_accent',
		)
	);
	$wp_customize->add_control(
		'accent_color',
		array(
			'type'        => 'radio',
			'section'     => 'fvnews_portal_colors',
			'label'       => '포인트색',
			'description' => '메뉴 바, 섹션 제목, 링크에 쓰입니다.',
			'choices'     => wp_list_pluck( $accents, 0 ),
		)
	);
}
add_action( 'customize_register', 'fvnews_portal_customize_register' );

// ---------- 템플릿 도우미 ----------

// 대표 카테고리: 첫 번째 카테고리 (기본 카테고리 = 미분류 제외 — 슬러그는 언어마다 달라 ID로 비교).
function fvnews_portal_primary_category( $post_id = null ) {
	$default = (int) get_option( 'default_category' );
	foreach ( get_the_category( $post_id ) as $category ) {
		if ( $default !== (int) $category->term_id ) {
			return $category;
		}
	}
	return null;
}

// 홈 카테고리 섹션: 글이 있는 최상위 카테고리를 글 수 순으로 최대 6개.
function fvnews_portal_front_categories() {
	return get_categories(
		array(
			'parent'     => 0,
			'hide_empty' => true,
			'orderby'    => 'count',
			'order'      => 'DESC',
			'number'     => 6,
			'exclude'    => array( (int) get_option( 'default_category' ) ),
		)
	);
}

// 주 메뉴를 아직 만들지 않았을 때: 최상위 카테고리로 메뉴를 대신한다.
function fvnews_portal_menu_fallback() {
	echo '<ul class="menu">';
	echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">홈</a></li>';
	foreach ( fvnews_portal_front_categories() as $category ) {
		echo '<li><a href="' . esc_url( get_category_link( $category ) ) . '">' . esc_html( $category->name ) . '</a></li>';
	}
	echo '</ul>';
}

// 기사 바이라인: 기자 · 입력 시각 (· 수정 시각).
function fvnews_portal_byline() {
	$published = get_the_date( 'Y.m.d H:i' );
	$modified  = get_the_modified_date( 'Y.m.d H:i' );
	echo '<span class="byline-author"><a href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . ' 기자</a></span>';
	echo '<span>입력 <time datetime="' . esc_attr( get_the_date( 'c' ) ) . '">' . esc_html( $published ) . '</time></span>';
	if ( $modified !== $published ) {
		echo '<span>수정 <time datetime="' . esc_attr( get_the_modified_date( 'c' ) ) . '">' . esc_html( $modified ) . '</time></span>';
	}
}

// 대표 이미지가 본문에 이미 들어 있으면 기사 상단에 다시 띄우지 않는다.
function fvnews_portal_show_featured_image() {
	$thumb_id = get_post_thumbnail_id();
	if ( ! $thumb_id ) {
		return false;
	}
	$content = get_post_field( 'post_content' );
	if ( false !== strpos( $content, 'wp-image-' . $thumb_id ) ) {
		return false;
	}
	$file = pathinfo( (string) get_attached_file( $thumb_id ), PATHINFO_FILENAME );
	return ! ( $file && false !== strpos( $content, $file ) );
}
