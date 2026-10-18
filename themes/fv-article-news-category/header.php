<?php
/**
 * Header template: 홈 + 채널(카테고리) 내비 + 유튜브 바로가기. 여러 채널을 한 사이트로 묶는 멀티 채널판.
 *
 * @package fv-article-news-category
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<header class="site-header">
	<div class="header-inner">
		<nav class="site-nav">
			<ul class="nav-list">
				<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( '홈', 'fv-article-news-category' ); ?></a></li>
				<?php fvarticle_news_category_nav_items(); ?>
				<?php $header_youtube = fvarticle_news_category_channel_option( 'youtube_url' ); ?>
				<?php if ( $header_youtube ) : ?>
					<li><a href="<?php echo esc_url( $header_youtube ); ?>" target="_blank" rel="noopener"><?php esc_html_e( '유튜브 바로가기', 'fv-article-news-category' ); ?></a></li>
				<?php endif; ?>
			</ul>
		</nav>
	</div>
</header>
<main class="site-content">
