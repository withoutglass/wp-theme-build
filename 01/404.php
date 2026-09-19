<?php
/**
 * 404 not found template.
 *
 * @package sample-01
 */

get_header();
?>

<section class="error-404">
	<h1 class="entry-title"><?php esc_html_e( 'Page not found', 'sample-01' ); ?></h1>
	<p><?php esc_html_e( 'The page you are looking for does not exist.', 'sample-01' ); ?></p>
	<?php get_search_form(); ?>
</section>

<?php get_footer(); ?>
