<?php
/**
 * Search results template.
 *
 * @package sample-00
 */

get_header();
?>

<header class="archive-header">
	<h1 class="archive-title">
		<?php
		/* translators: %s: search query. */
		printf( esc_html__( 'Search results for: %s', 'sample-00' ), '<span>' . esc_html( get_search_query() ) . '</span>' );
		?>
	</h1>
</header>

<?php if ( have_posts() ) : ?>
	<?php
	while ( have_posts() ) :
		the_post();
		get_template_part( 'template-parts/content' );
	endwhile;

	the_posts_pagination();
	?>
<?php else : ?>
	<p><?php esc_html_e( 'Nothing found.', 'sample-00' ); ?></p>
	<?php get_search_form(); ?>
<?php endif; ?>

<?php get_footer(); ?>
