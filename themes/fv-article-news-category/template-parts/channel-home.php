<?php
/**
 * 채널 홈 본문: 채널 프로필 + 주간 많이 본 기사 + 최신 기사·사이드바.
 * 홈(사이트 전체)과 카테고리(채널) 페이지가 같이 쓴다.
 *
 * $args: name, count, description, avatar(HTML, 없으면 이름 첫 글자), query(기사 쿼리에 더할 조건, 예: cat).
 *
 * @package fv-article-news-category
 */
?>
<section class="channel-profile">
	<div class="channel-avatar">
		<?php if ( $args['avatar'] ) : ?>
			<?php echo $args['avatar']; // phpcs:ignore WordPress.Security.EscapeOutput -- the_custom_logo / wp_get_attachment_image 출력 ?>
		<?php else : ?>
			<span class="channel-avatar-fallback"><?php echo esc_html( mb_substr( $args['name'], 0, 1 ) ); ?></span>
		<?php endif; ?>
	</div>
	<div class="channel-info">
		<h1 class="channel-name"><?php echo esc_html( $args['name'] ); ?></h1>
		<p class="channel-count">
			<?php esc_html_e( '발행 기사 수', 'fv-article-news-category' ); ?>
			<strong><?php echo esc_html( $args['count'] ); ?></strong>
		</p>
		<p class="channel-description"><?php echo esc_html( $args['description'] ); ?></p>
	</div>
	<?php get_template_part( 'template-parts/channel-links' ); ?>
</section>

<?php
// 주간 인기: 인기 집계 미구현 — 우선 최신글로 채운다.
$weekly = new WP_Query(
	array_merge(
		array(
			'posts_per_page'      => 4,
			'ignore_sticky_posts' => true,
		),
		$args['query']
	)
);
// 최신 기사에서 주간 기사를 빼되, 글이 4개 이하인 채널은 다 빠져 최신 기사가 비므로 빼지 않는다.
$weekly_ids = $weekly->found_posts > 4 ? wp_list_pluck( $weekly->posts, 'ID' ) : array();
?>
<?php if ( $weekly->have_posts() ) : ?>
	<section class="weekly-popular">
		<h2 class="section-title">
			<span class="accent"><?php echo esc_html( $args['name'] ); ?></span> <?php esc_html_e( '주간 많이 본 기사', 'fv-article-news-category' ); ?>
		</h2>
		<div class="weekly-grid">
			<?php
			$rank = 0;
			while ( $weekly->have_posts() ) :
				$weekly->the_post();
				$rank++;
				?>
				<article class="weekly-card">
					<a href="<?php the_permalink(); ?>">
						<div class="card-thumb"<?php fvarticle_news_category_thumb_style_attr(); ?>>
							<span class="rank-badge"><?php echo esc_html( $rank ); ?></span>
							<?php if ( has_post_thumbnail() ) : ?>
								<?php the_post_thumbnail( 'medium_large' ); ?>
							<?php else : ?>
								<div class="thumb-placeholder"></div>
							<?php endif; ?>
						</div>
						<h3 class="card-title"><?php the_title(); ?></h3>
					</a>
				</article>
			<?php endwhile; ?>
		</div>
	</section>
	<?php wp_reset_postdata(); ?>
<?php endif; ?>

<div class="front-columns">
	<section class="latest-articles">
		<h2 class="section-title">
			<span class="accent"><?php echo esc_html( $args['name'] ); ?></span> <?php esc_html_e( '최신 기사', 'fv-article-news-category' ); ?>
		</h2>
		<?php
		$paged  = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
		$latest = new WP_Query(
			array_merge(
				array(
					'posts_per_page'      => 5,
					'paged'               => $paged,
					'post__not_in'        => $weekly_ids,
					'ignore_sticky_posts' => true,
				),
				$args['query']
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
			<p><?php esc_html_e( 'No posts found.', 'fv-article-news-category' ); ?></p>
		<?php endif; ?>
	</section>

	<?php get_sidebar(); ?>
</div>

