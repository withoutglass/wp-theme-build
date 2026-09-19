<?php
/**
 * Single post template: title/meta → ad → body(+mid ads) → channel box → related → comments.
 * 오른쪽 사이드바(실시간 TOP) 동반. 광고는 .ad-slot 플레이스홀더.
 *
 * @package sample-01
 */

get_header();
?>

<?php
while ( have_posts() ) :
	the_post();
	?>
	<div class="front-columns single-columns">
		<article <?php post_class(); ?>>
			<header class="single-header">
				<h1 class="single-title"><?php the_title(); ?></h1>
				<div class="single-meta-row">
					<p class="entry-meta">
						<?php bloginfo( 'name' ); ?> · <?php echo esc_html( get_the_date( 'Y.m.d H:i' ) ); ?>
					</p>
					<?php
					// 글별 원본 영상 URL이 없으면 채널 URL로 폴백 (연동 전까지 버튼 상시 노출).
					$original_video = get_post_meta( get_the_ID(), 'original_video_url', true );
					if ( ! $original_video ) {
						$original_video = sample01_channel_option( 'youtube_url' );
					}
					?>
					<?php if ( $original_video ) : ?>
						<a class="btn-original-video" href="<?php echo esc_url( $original_video ); ?>" target="_blank" rel="noopener">
							<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5.5l11 6.5-11 6.5z"/></svg>
							<?php esc_html_e( '원본 영상 보기', 'sample-01' ); ?>
						</a>
					<?php endif; ?>
				</div>
			</header>

			<div class="entry-content">
				<?php the_content(); ?>
			</div>

			<section class="channel-box">
				<div class="channel-avatar">
					<?php if ( has_custom_logo() ) : ?>
						<?php the_custom_logo(); ?>
					<?php else : ?>
						<span class="channel-avatar-fallback"><?php echo esc_html( mb_substr( get_bloginfo( 'name' ), 0, 1 ) ); ?></span>
					<?php endif; ?>
				</div>
				<div class="channel-info">
					<p class="channel-box-name"><?php bloginfo( 'name' ); ?></p>
					<p class="channel-description"><?php bloginfo( 'description' ); ?></p>
					<?php get_template_part( 'template-parts/channel-links' ); ?>
				</div>
			</section>

			<?php
			$related = new WP_Query(
				array(
					'posts_per_page'      => 6,
					'post__not_in'        => array( get_the_ID() ),
					'ignore_sticky_posts' => true,
				)
			);
			?>
			<?php if ( $related->have_posts() ) : ?>
				<section class="related-articles">
					<h2 class="section-title"><?php esc_html_e( '함께 볼만한 기사', 'sample-01' ); ?></h2>
					<div class="weekly-grid related-grid">
						<?php
						while ( $related->have_posts() ) :
							$related->the_post();
							?>
							<article class="weekly-card">
								<a href="<?php the_permalink(); ?>">
									<div class="card-thumb">
										<?php if ( has_post_thumbnail() ) : ?>
											<?php the_post_thumbnail( 'medium' ); ?>
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

		</article>

		<?php get_sidebar(); ?>
	</div>
<?php endwhile; ?>

<?php get_footer(); ?>
