<?php
/**
 * Header template: dark chrome bar — channel brand left, home/YouTube links right (1채널 = 1사이트, 카테고리 없음).
 *
 * @package fv-article-reader
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
		<a class="site-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php if ( has_custom_logo() ) : ?>
				<img class="brand-avatar" src="<?php echo esc_url( wp_get_attachment_image_url( get_theme_mod( 'custom_logo' ), 'thumbnail' ) ); ?>" alt="">
			<?php else : ?>
				<span class="brand-avatar brand-avatar-fallback"><?php echo esc_html( mb_substr( get_bloginfo( 'name' ), 0, 1 ) ); ?></span>
			<?php endif; ?>
			<span class="brand-name"><?php bloginfo( 'name' ); ?></span>
		</a>
		<nav class="site-nav">
			<ul class="nav-list">
				<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( '홈', 'fv-article-reader' ); ?></a></li>
				<?php $header_youtube = fvarticle_reader_channel_option( 'youtube_url' ); ?>
				<?php if ( $header_youtube ) : ?>
					<li><a href="<?php echo esc_url( $header_youtube ); ?>" target="_blank" rel="noopener"><?php esc_html_e( '유튜브 바로가기', 'fv-article-reader' ); ?></a></li>
				<?php endif; ?>
			</ul>
		</nav>
	</div>
</header>
<main class="site-content">
