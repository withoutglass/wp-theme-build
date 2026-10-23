<?php
/**
 * Single post template (리더형 1단):
 * 어두운 히어로 띠(메타·제목·원본 영상) → 본문(+중간 광고, functions.php) → 원본 영상 임베드
 * → 채널 박스 → 다음 기사 카드 → 함께 볼만한 기사 → 최근 업로드 영상 → 실시간 TOP 띠.
 * 사이드바 없음. 넓은 화면(≥1200px)에서만 오른쪽 여백에 따라오는 광고. 광고는 .ad-slot 플레이스홀더.
 *
 * @package fv-article-reader
 */

get_header();
?>

<?php
while ( have_posts() ) :
	the_post();
	?>
	<article <?php post_class( 'single-article' ); ?>>
		<header class="hero">
			<div class="hero-inner">
				<p class="hero-meta">
					<?php bloginfo( 'name' ); ?> · <?php echo esc_html( get_the_date( 'Y.m.d H:i' ) ); ?>
				</p>
				<h1 class="hero-title"><?php the_title(); ?></h1>
				<?php
				// 글별 원본 영상 URL(백엔드가 meta로 전달)이 없으면 채널 URL로 폴백.
				$original_video = get_post_meta( get_the_ID(), 'source_video_url', true );
				if ( ! $original_video ) {
					$original_video = fvarticle_reader_channel_option( 'youtube_url' );
				}
				?>
				<?php if ( $original_video ) : ?>
					<a class="btn-original-video" href="<?php echo esc_url( $original_video ); ?>" target="_blank" rel="noopener">
						<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5.5l11 6.5-11 6.5z"/></svg>
						<?php esc_html_e( '원본 영상 보기', 'fv-article-reader' ); ?>
					</a>
				<?php endif; ?>
			</div>
		</header>

		<div class="reader-stage">
			<div class="reader-column">
				<div class="entry-content">
					<?php the_content(); ?>
				</div>

				<?php
				// 본문 끝 원본 영상 임베드: 글별 source_video_url이 유튜브 영상일 때만.
				// (상단 "원본 영상 보기" 버튼과 별개로 둘 다 노출한다.)
				$embed_url = fvarticle_reader_youtube_embed_url( get_post_meta( get_the_ID(), 'source_video_url', true ) );
				?>
				<?php if ( $embed_url ) : ?>
					<div class="video-embed">
						<iframe src="<?php echo esc_url( $embed_url ); ?>"
							title="<?php esc_attr_e( '원본 영상', 'fv-article-reader' ); ?>"
							loading="lazy"
							allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
							allowfullscreen></iframe>
					</div>
				<?php endif; ?>

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
				// 다음 기사 = 바로 이전 발행글. 가장 오래된 글이면 카드 생략.
				$next_post = get_previous_post();
				?>
				<?php if ( $next_post ) : ?>
					<a class="next-article" href="<?php echo esc_url( get_permalink( $next_post ) ); ?>">
						<div class="next-thumb"<?php fvarticle_reader_thumb_style_attr( $next_post ); ?>>
							<?php if ( has_post_thumbnail( $next_post ) ) : ?>
								<?php echo get_the_post_thumbnail( $next_post, 'medium' ); ?>
							<?php else : ?>
								<div class="thumb-placeholder"></div>
							<?php endif; ?>
						</div>
						<div class="next-body">
							<span class="next-label"><?php esc_html_e( '다음 기사', 'fv-article-reader' ); ?> →</span>
							<span class="next-title"><?php echo esc_html( get_the_title( $next_post ) ); ?></span>
						</div>
					</a>
				<?php endif; ?>

				<?php
				$related = new WP_Query(
					array(
						'posts_per_page'      => 6,
						'post__not_in'        => array_filter( array( get_the_ID(), $next_post ? $next_post->ID : 0 ) ),
						'ignore_sticky_posts' => true,
					)
				);
				?>
				<?php if ( $related->have_posts() ) : ?>
					<section class="related-articles">
						<h2 class="section-title"><?php esc_html_e( '함께 볼만한 기사', 'fv-article-reader' ); ?></h2>
						<ul class="related-list">
							<?php
							while ( $related->have_posts() ) :
								$related->the_post();
								?>
								<li>
									<a class="related-item" href="<?php the_permalink(); ?>">
										<div class="related-thumb"<?php fvarticle_reader_thumb_style_attr(); ?>>
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

				<?php $recent_videos = fvarticle_reader_recent_videos( 4 ); ?>
				<?php if ( $recent_videos ) : ?>
					<section class="recent-videos">
						<h2 class="section-title"><?php esc_html_e( '최근 업로드 영상', 'fv-article-reader' ); ?></h2>
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

			<aside class="reader-rail" aria-label="<?php esc_attr_e( '광고', 'fv-article-reader' ); ?>">
				<div class="ad-slot ad-slot-rail"></div>
			</aside>
		</div>
	</article>
<?php endwhile; ?>

<?php get_template_part( 'template-parts/top-strip' ); ?>

<?php get_footer(); ?>
