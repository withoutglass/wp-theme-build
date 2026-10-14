<?php
/**
 * Main template — blog listing and ultimate fallback.
 *
 * @package fv-article-news
 */

get_header();
?>

<?php if ( have_posts() ) : ?>
	<?php
	while ( have_posts() ) :
		the_post();
		get_template_part( 'template-parts/content' );
	endwhile;

	the_posts_pagination();
	?>
<?php else : ?>
	<p><?php esc_html_e( 'No posts found.', 'fv-article-news' ); ?></p>
<?php endif; ?>

<?php get_footer(); ?>
