<?php
/**
 * Search results template.
 *
 * @package fv-article-stream
 */

get_header();
?>

<div class="list-stage">

<header class="archive-header">
	<h1 class="archive-title">
		<?php
		/* translators: %s: search query. */
		printf( esc_html__( 'Search results for: %s', 'fv-article-stream' ), '<span>' . esc_html( get_search_query() ) . '</span>' );
		?>
	</h1>
</header>

<?php if ( have_posts() ) : ?>
	<?php
	while ( have_posts() ) :
		the_post();
		get_template_part( 'template-parts/content', 'list' );
	endwhile;

	the_posts_pagination();
	?>
<?php else : ?>
	<p><?php esc_html_e( 'Nothing found.', 'fv-article-stream' ); ?></p>
	<?php get_search_form(); ?>
<?php endif; ?>

</div>

<?php get_footer(); ?>
