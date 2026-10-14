<?php
/**
 * Static page template.
 *
 * @package fv-news-portal
 */

get_header();
?>

<div class="layout">
	<div class="main-col">
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article <?php post_class( 'article' ); ?>>
				<header class="article-header">
					<h1 class="article-title"><?php the_title(); ?></h1>
				</header>
				<div class="entry-content">
					<?php the_content(); ?>
				</div>
			</article>
			<?php
		endwhile;
		?>
	</div>

	<?php get_sidebar(); ?>
</div>

<?php get_footer(); ?>
