<?php
/**
 * Front page: channel profile + weekly popular grid + latest list with sidebar.
 *
 * @package sample-01
 */

get_header();
?>

<section class="channel-profile">
	<div class="channel-avatar">
		<?php if ( has_custom_logo() ) : ?>
			<?php the_custom_logo(); ?>
		<?php else : ?>
			<span class="channel-avatar-fallback"><?php echo esc_html( mb_substr( get_bloginfo( 'name' ), 0, 1 ) ); ?></span>
		<?php endif; ?>
	</div>
	<div class="channel-info">
		<h1 class="channel-name"><?php bloginfo( 'name' ); ?></h1>
		<p class="channel-count">
			<?php esc_html_e( '발행 기사 수', 'sample-01' ); ?>
			<strong><?php echo esc_html( wp_count_posts()->publish ); ?></strong>
		</p>
		<p class="channel-description"><?php bloginfo( 'description' ); ?></p>
	</div>
	<?php get_template_part( 'template-parts/channel-links' ); ?>
</section>

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
		<h2 class="section-title">
			<span class="accent"><?php bloginfo( 'name' ); ?></span> <?php esc_html_e( '주간 많이 본 기사', 'sample-01' ); ?>
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
						<div class="card-thumb">
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
			<span class="accent"><?php bloginfo( 'name' ); ?></span> <?php esc_html_e( '최신 기사', 'sample-01' ); ?>
		</h2>
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
			<p><?php esc_html_e( 'No posts found.', 'sample-01' ); ?></p>
		<?php endif; ?>
	</section>

	<?php get_sidebar(); ?>
</div>

<?php get_footer(); ?>
