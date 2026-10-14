<?php
/**
 * Header template: slim bar with home link only (1채널 = 1사이트, 카테고리 없음).
 *
 * @package fv-article-news
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
				<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( '홈', 'fv-article-news' ); ?></a></li>
				<?php $header_youtube = fvarticle_news_channel_option( 'youtube_url' ); ?>
				<?php if ( $header_youtube ) : ?>
					<li><a href="<?php echo esc_url( $header_youtube ); ?>" target="_blank" rel="noopener"><?php esc_html_e( '유튜브 바로가기', 'fv-article-news' ); ?></a></li>
				<?php endif; ?>
			</ul>
		</nav>
	</div>
</header>
<main class="site-content">
