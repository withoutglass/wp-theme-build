<?php
/**
 * Listing item with thumbnail: front page latest / archive / search.
 *
 * @package sample-01
 */
?>
<article <?php post_class( 'list-item' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<a href="<?php the_permalink(); ?>" class="list-thumb"><?php the_post_thumbnail( 'medium' ); ?></a>
	<?php else : ?>
		<a href="<?php the_permalink(); ?>" class="list-thumb"><div class="thumb-placeholder"></div></a>
	<?php endif; ?>
	<div class="list-body">
		<h2 class="entry-title">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h2>
		<p class="entry-summary"><?php echo esc_html( get_the_excerpt() ); ?></p>
		<p class="entry-meta"><?php echo esc_html( get_the_date( 'Y.m.d' ) ); ?></p>
	</div>
</article>
