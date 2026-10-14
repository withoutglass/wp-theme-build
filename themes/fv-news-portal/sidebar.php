<?php
/**
 * Sidebar: 위젯을 넣으면 위젯, 비어 있으면 인기 뉴스(댓글 수 순) + 최신 뉴스.
 *
 * @package fv-news-portal
 */
?>
<aside class="sidebar" aria-label="사이드바">
	<?php if ( is_active_sidebar( 'sidebar-1' ) ) : ?>
		<?php dynamic_sidebar( 'sidebar-1' ); ?>
	<?php else : ?>
		<?php
		// 조회수 집계가 없는 범용 테마라 댓글 수를 인기 기준으로 쓴다.
		$popular = new WP_Query(
			array(
				'posts_per_page'      => 5,
				'orderby'             => array(
					'comment_count' => 'DESC',
					'date'          => 'DESC',
				),
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
			)
		);
		?>
		<?php if ( $popular->have_posts() ) : ?>
			<section class="widget">
				<h2 class="widget-title">인기 뉴스</h2>
				<ol class="rank-list">
					<?php
					while ( $popular->have_posts() ) :
						$popular->the_post();
						?>
						<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
					<?php endwhile; ?>
				</ol>
			</section>
			<?php wp_reset_postdata(); ?>
		<?php endif; ?>

		<?php
		$latest = new WP_Query(
			array(
				'posts_per_page'      => 5,
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
			)
		);
		?>
		<?php if ( $latest->have_posts() ) : ?>
			<section class="widget">
				<h2 class="widget-title">최신 뉴스</h2>
				<ul class="thumb-list">
					<?php
					while ( $latest->have_posts() ) :
						$latest->the_post();
						?>
						<li>
							<a href="<?php the_permalink(); ?>">
								<span class="thumb"><?php the_post_thumbnail( 'thumbnail' ); ?></span>
								<span class="thumb-list-title"><?php the_title(); ?></span>
							</a>
						</li>
					<?php endwhile; ?>
				</ul>
			</section>
			<?php wp_reset_postdata(); ?>
		<?php endif; ?>
	<?php endif; ?>
</aside>
