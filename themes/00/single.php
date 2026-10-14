<?php
/**
 * Single post template.
 *
 * @package sample-00
 */

get_header();
?>

<?php
while ( have_posts() ) :
	the_post();
	?>
	<article <?php post_class(); ?>>
		<header class="entry-header">
			<h1 class="entry-title"><?php the_title(); ?></h1>
			<p class="entry-meta"><?php echo esc_html( get_the_date() ); ?></p>
		</header>
		<div class="entry-content">
			<?php the_content(); ?>
		</div>
	</article>

	<?php
	if ( comments_open() || get_comments_number() ) :
		comments_template();
	endif;

	the_post_navigation();
endwhile;
?>

<?php get_footer(); ?>
