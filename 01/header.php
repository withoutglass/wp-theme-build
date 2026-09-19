<?php
/**
 * Header template: slim bar with home link only (1채널 = 1사이트, 카테고리 없음).
 *
 * @package sample-01
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
				<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( '홈', 'sample-01' ); ?></a></li>
				<?php $header_youtube = sample01_channel_option( 'youtube_url' ); ?>
				<?php if ( $header_youtube ) : ?>
					<li><a href="<?php echo esc_url( $header_youtube ); ?>" target="_blank" rel="noopener"><?php esc_html_e( '유튜브 바로가기', 'sample-01' ); ?></a></li>
				<?php endif; ?>
				<?php $header_email = sample01_channel_option( 'contact_email' ); ?>
				<?php if ( $header_email ) : ?>
					<li><a href="mailto:<?php echo esc_attr( $header_email ); ?>"><?php esc_html_e( '문의하기', 'sample-01' ); ?></a></li>
				<?php endif; ?>
			</ul>
		</nav>
	</div>
</header>
<main class="site-content">
