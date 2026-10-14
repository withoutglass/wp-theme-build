<?php
/**
 * Archive template — category, tag, date, author listings.
 *
 * @package fv-article-rail
 */

get_header();
?>

<div class="list-stage">

<?php if ( have_posts() ) : ?>
	<header class="archive-header">
		<h1 class="archive-title"><?php the_archive_title(); ?></h1>
		<?php the_archive_description( '<div class="archive-description">', '</div>' ); ?>
	</header>

	<?php
	while ( have_posts() ) :
		the_post();
		get_template_part( 'template-parts/content', 'list' );
	endwhile;

	the_posts_pagination();
	?>
<?php else : ?>
	<p><?php esc_html_e( 'No posts found.', 'fv-article-rail' ); ?></p>
<?php endif; ?>

</div>

<?php get_footer(); ?>
