<?php
/**
 * Single post template (무한 스트림형):
 * 기사(template-parts/stream-article.php) → [assets/stream.js가 이전 발행글을 이어 붙임, 자동 5편 후 "더 보기"]
 * → 스트림 끝: 채널 박스 → 함께 볼만한 기사 → 최근 업로드 영상 → 실시간 TOP 띠.
 * JS가 없으면 "다음 기사 보기"는 이전 발행글로 가는 일반 링크로 동작한다.
 *
 * @package fv-article-stream
 */

get_header();
?>

<?php
while ( have_posts() ) :
	the_post();
	$next_post = fvarticle_stream_previous_post( get_post() );
	?>
	<div class="stream">
		<?php get_template_part( 'template-parts/stream-article' ); ?>
	</div>

	<div class="stream-control">
		<p class="stream-status" aria-live="polite"></p>
		<?php if ( $next_post ) : ?>
			<a class="stream-more" href="<?php echo esc_url( get_permalink( $next_post ) ); ?>"><?php esc_html_e( '다음 기사 보기', 'fv-article-stream' ); ?></a>
		<?php endif; ?>
	</div>

	<div class="stream-end reader-column">
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
				<h2 class="section-title"><?php esc_html_e( '함께 볼만한 기사', 'fv-article-stream' ); ?></h2>
				<ul class="related-list">
					<?php
					while ( $related->have_posts() ) :
						$related->the_post();
						?>
						<li>
							<a class="related-item" href="<?php the_permalink(); ?>">
								<div class="related-thumb"<?php fvarticle_stream_thumb_style_attr(); ?>>
									<?php if ( has_post_thumbnail() ) : ?>
										<?php the_post_thumbnail( 'medium' ); ?>
									<?php else : ?>
										<div class="thumb-placeholder"></div>
									<?php endif; ?>
								</div>
								<span class="related-title"><?php the_title(); ?></span>
							</a>
						</li>
					<?php endwhile; ?>
				</ul>
			</section>
			<?php wp_reset_postdata(); ?>
		<?php endif; ?>

		<?php $recent_videos = fvarticle_stream_recent_videos( 4 ); ?>
		<?php if ( $recent_videos ) : ?>
			<section class="recent-videos">
				<h2 class="section-title"><?php esc_html_e( '최근 업로드 영상', 'fv-article-stream' ); ?></h2>
				<ul class="video-list">
					<?php foreach ( $recent_videos as $video ) : ?>
						<li>
							<a href="<?php echo esc_url( $video['url'] ); ?>" target="_blank" rel="noopener" class="video-item">
								<div class="video-thumb">
									<img src="<?php echo esc_url( $video['thumb'] ); ?>" alt="" loading="lazy">
									<span class="video-play" aria-hidden="true"></span>
								</div>
								<h3 class="video-title"><?php echo esc_html( $video['title'] ); ?></h3>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</section>
		<?php endif; ?>
	</div>
<?php endwhile; ?>

<?php get_template_part( 'template-parts/top-strip' ); ?>

<?php get_footer(); ?>
