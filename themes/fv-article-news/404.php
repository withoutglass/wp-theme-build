<?php
/**
 * 404 not found template.
 *
 * @package fv-article-news
 */

get_header();
?>

<section class="error-404">
	<h1 class="entry-title"><?php esc_html_e( 'Page not found', 'fv-article-news' ); ?></h1>
	<p><?php esc_html_e( 'The page you are looking for does not exist.', 'fv-article-news' ); ?></p>
	<?php get_search_form(); ?>
</section>

<?php get_footer(); ?>
