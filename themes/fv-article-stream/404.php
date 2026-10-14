<?php
/**
 * 404 not found template.
 *
 * @package fv-article-stream
 */

get_header();
?>

<section class="error-404 reader-column">
	<h1 class="entry-title"><?php esc_html_e( 'Page not found', 'fv-article-stream' ); ?></h1>
	<p><?php esc_html_e( 'The page you are looking for does not exist.', 'fv-article-stream' ); ?></p>
	<?php get_search_form(); ?>
</section>

<?php get_footer(); ?>
