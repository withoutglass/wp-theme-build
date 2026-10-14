<?php
/**
 * Main template — blog listing and ultimate fallback.
 *
 * @package fv-article-stream
 */

get_header();
?>

<div class="list-stage">

<?php if ( have_posts() ) : ?>
	<?php
	while ( have_posts() ) :
		the_post();
		get_template_part( 'template-parts/content', 'list' );
	endwhile;

	the_posts_pagination();
	?>
<?php else : ?>
	<p><?php esc_html_e( 'No posts found.', 'fv-article-stream' ); ?></p>
<?php endif; ?>

</div>

<?php get_footer(); ?>
