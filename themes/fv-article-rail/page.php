<?php
/**
 * Static page template: 기사와 같은 밝은 지면에 제목 + 본문.
 *
 * @package fv-article-rail
 */

get_header();
?>

<?php
while ( have_posts() ) :
	the_post();
	?>
	<article <?php post_class( 'article-paper' ); ?>>
		<header class="single-header">
			<h1 class="single-title"><?php the_title(); ?></h1>
		</header>
		<div class="entry-content">
			<?php the_content(); ?>
		</div>
	</article>
	<?php
endwhile;
?>

<?php get_footer(); ?>
