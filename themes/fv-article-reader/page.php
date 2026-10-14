<?php
/**
 * Static page template: 어두운 히어로 띠(제목) → 1단 본문.
 *
 * @package fv-article-reader
 */

get_header();
?>

<?php
while ( have_posts() ) :
	the_post();
	?>
	<article <?php post_class(); ?>>
		<header class="hero">
			<div class="hero-inner">
				<h1 class="hero-title"><?php the_title(); ?></h1>
			</div>
		</header>
		<div class="reader-column">
			<div class="entry-content">
				<?php the_content(); ?>
			</div>
		</div>
	</article>
	<?php
endwhile;
?>

<?php get_footer(); ?>
