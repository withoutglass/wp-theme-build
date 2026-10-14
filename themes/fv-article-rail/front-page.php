<?php
/**
 * Front page (채널 레일형): 채널 프로필은 왼쪽 레일이 담당. 본문 영역에 주간 많이 본 기사(2×2 카드) → 최신 기사 목록.
 *
 * @package fv-article-rail
 */

get_header();
?>

<div class="front-stage">
	<?php
	// 주간 인기: 인기 집계 미구현 — 우선 최신글로 채운다.
	$weekly = new WP_Query(
		array(
			'posts_per_page'      => 4,
			'ignore_sticky_posts' => true,
		)
	);
	$weekly_ids = wp_list_pluck( $weekly->posts, 'ID' );
	?>
	<?php if ( $weekly->have_posts() ) : ?>
		<section class="weekly-popular">
			<h2 class="section-title"><?php esc_html_e( '주간 많이 본 기사', 'fv-article-rail' ); ?></h2>
			<div class="weekly-grid">
				<?php
				$rank = 0;
				while ( $weekly->have_posts() ) :
					$weekly->the_post();
					$rank++;
					?>
					<article class="weekly-card">
						<a href="<?php the_permalink(); ?>">
							<div class="card-thumb"<?php fvarticle_rail_thumb_style_attr(); ?>>
								<span class="rank-badge"><?php echo esc_html( $rank ); ?></span>
								<?php if ( has_post_thumbnail() ) : ?>
									<?php the_post_thumbnail( 'medium_large' ); ?>
								<?php else : ?>
									<div class="thumb-placeholder"></div>
								<?php endif; ?>
							</div>
							<div class="card-body">
								<h3 class="card-title"><?php the_title(); ?></h3>
							</div>
						</a>
					</article>
				<?php endwhile; ?>
			</div>
		</section>
		<?php wp_reset_postdata(); ?>
	<?php endif; ?>

	<section class="latest-articles">
		<h2 class="section-title"><?php esc_html_e( '최신 기사', 'fv-article-rail' ); ?></h2>
		<?php
		$paged  = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
		$latest = new WP_Query(
			array(
				'posts_per_page'      => 5,
				'paged'               => $paged,
				'post__not_in'        => $weekly_ids,
				'ignore_sticky_posts' => true,
			)
		);
		?>
		<?php if ( $latest->have_posts() ) : ?>
			<?php
			while ( $latest->have_posts() ) :
				$latest->the_post();
				get_template_part( 'template-parts/content', 'list' );
			endwhile;
			wp_reset_postdata();
			?>
			<nav class="front-pagination">
				<?php
				echo paginate_links( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					array(
						'total'     => $latest->max_num_pages,
						'current'   => $paged,
						'mid_size'  => 2,
						'prev_text' => '‹',
						'next_text' => '›',
					)
				);
				?>
			</nav>
		<?php else : ?>
			<p><?php esc_html_e( 'No posts found.', 'fv-article-rail' ); ?></p>
		<?php endif; ?>
	</section>
</div>

<?php get_footer(); ?>
