<?php
/**
 * Header template: 페이지 골격(왼쪽 채널 레일 + 오른쪽 본문 영역)을 연다. 레일은 template-parts/rail.php.
 * 1채널 = 1사이트, 카테고리 없음.
 *
 * @package fv-article-rail
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
<div class="site-shell">
	<?php get_template_part( 'template-parts/rail' ); ?>
	<div class="site-main">
		<main class="site-content">
