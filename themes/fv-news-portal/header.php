<?php
/**
 * Header template: 상단 바(날짜·검색) → 제호(로고) → 포인트색 메뉴 바 → 최신 기사 띠.
 * <html>의 mode-* 클래스로 색 모드(라이트/다크/OS)를 style.css가 처리한다.
 *
 * @package fv-news-portal
 */

$ticker = new WP_Query(
	array(
		'posts_per_page'      => 5,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	)
);
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="mode-<?php echo esc_attr( fvnews_portal_color_mode() ); ?>">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#content">본문 바로가기</a>
<header class="site-header">
	<div class="topbar">
		<div class="container topbar-inner">
			<span class="topbar-date"><?php echo esc_html( wp_date( 'Y년 n월 j일 l' ) ); ?></span>
			<?php get_search_form(); ?>
		</div>
	</div>

	<div class="container masthead">
		<?php $brand_tag = is_front_page() ? 'h1' : 'div'; // 홈에서는 제호가 페이지의 h1. ?>
		<<?php echo $brand_tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- h1/div 고정값. ?> class="site-brand">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
				<span class="screen-reader-text"><?php bloginfo( 'name' ); ?></span>
			<?php else : ?>
				<a class="site-title" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a>
			<?php endif; ?>
		</<?php echo $brand_tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<?php if ( get_bloginfo( 'description' ) ) : ?>
			<p class="site-description"><?php bloginfo( 'description' ); ?></p>
		<?php endif; ?>
	</div>

	<nav class="primary-nav" aria-label="주 메뉴">
		<div class="container">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'depth'          => 1,
					'fallback_cb'    => 'fvnews_portal_menu_fallback',
				)
			);
			?>
		</div>
	</nav>

	<?php if ( $ticker->have_posts() ) : ?>
		<div class="container ticker">
			<strong class="ticker-label">최신</strong>
			<div class="ticker-track">
				<ul class="ticker-list">
					<?php
					while ( $ticker->have_posts() ) :
						$ticker->the_post();
						?>
						<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
					<?php endwhile; ?>
				</ul>
			</div>
		</div>
		<?php wp_reset_postdata(); ?>
	<?php endif; ?>
</header>
<main id="content" class="site-content container">
