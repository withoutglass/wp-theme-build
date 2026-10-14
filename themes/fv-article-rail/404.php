<?php
/**
 * 404 not found template.
 *
 * @package fv-article-rail
 */

get_header();
?>

<section class="error-404 article-paper">
	<h1 class="entry-title"><?php esc_html_e( 'Page not found', 'fv-article-rail' ); ?></h1>
	<p><?php esc_html_e( 'The page you are looking for does not exist.', 'fv-article-rail' ); ?></p>
	<?php get_search_form(); ?>
</section>

<?php get_footer(); ?>
