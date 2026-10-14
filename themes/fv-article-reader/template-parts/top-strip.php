<?php
/**
 * 실시간 TOP 띠: 기사 상세 하단 가로 순위 목록 (01의 사이드바 TOP을 대체).
 * 인기 집계 미구현 — 우선 최신글로 채운다.
 *
 * @package fv-article-reader
 */

$top = new WP_Query(
	array(
		'posts_per_page'      => 5,
		'ignore_sticky_posts' => true,
	)
);
?>
<?php if ( $top->have_posts() ) : ?>
	<section class="top-strip">
		<div class="top-strip-inner">
			<h2 class="section-title"><?php esc_html_e( '실시간 TOP', 'fv-article-reader' ); ?></h2>
			<ol class="top-list">
				<?php
				while ( $top->have_posts() ) :
					$top->the_post();
					?>
					<li class="top-item">
						<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					</li>
				<?php endwhile; ?>
			</ol>
		</div>
	</section>
	<?php wp_reset_postdata(); ?>
<?php endif; ?>
