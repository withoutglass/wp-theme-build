<?php
/**
 * Main template — blog listing and ultimate fallback.
 *
 * @package sample-01
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
	<p><?php esc_html_e( 'No posts found.', 'sample-01' ); ?></p>
<?php endif; ?>

<?php get_footer(); ?>
