<?php
/**
 * 목록 아이템: 제목·요약·메타(카테고리 · 날짜 · 기자) + 오른쪽 썸네일. 카테고리/태그/기자/검색/최신 목록 공용.
 *
 * @package fv-news-portal
 */

$category = fvnews_portal_primary_category();
?>
<article <?php post_class( 'list-item' ); ?>>
	<div class="list-body">
		<h2 class="list-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<p class="list-excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
		<p class="meta">
			<?php if ( $category ) : ?>
				<a href="<?php echo esc_url( get_category_link( $category ) ); ?>"><?php echo esc_html( $category->name ); ?></a>
			<?php endif; ?>
			<span><?php echo esc_html( get_the_date( 'Y.m.d H:i' ) ); ?></span>
			<span><?php echo esc_html( get_the_author() ); ?> 기자</span>
		</p>
	</div>
	<?php if ( has_post_thumbnail() ) : ?>
		<a class="thumb list-thumb" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true"><?php the_post_thumbnail( 'medium' ); ?></a>
	<?php endif; ?>
</article>
