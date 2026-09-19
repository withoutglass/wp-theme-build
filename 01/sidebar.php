<?php
/**
 * Sidebar: 실시간 TOP — 인기 집계 미구현, 우선 최신글로 채운다.
 *
 * @package sample-01
 */

$top = new WP_Query(
	array(
		'posts_per_page'      => 5,
		'ignore_sticky_posts' => true,
	)
);
?>
<aside class="site-sidebar">
	<?php if ( $top->have_posts() ) : ?>
		<section class="realtime-top">
			<h2 class="section-title"><?php esc_html_e( '실시간 TOP', 'sample-01' ); ?></h2>
			<ol class="top-list">
				<?php
				while ( $top->have_posts() ) :
					$top->the_post();
					?>
					<li class="top-item">
						<div class="top-text">
							<h3 class="top-title">
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</h3>
							<p class="entry-meta"><?php echo esc_html( get_the_date( 'Y.m.d' ) ); ?></p>
						</div>
						<?php if ( has_post_thumbnail() ) : ?>
							<a href="<?php the_permalink(); ?>" class="top-thumb"><?php the_post_thumbnail( 'thumbnail' ); ?></a>
						<?php endif; ?>
					</li>
				<?php endwhile; ?>
			</ol>
		</section>
		<?php wp_reset_postdata(); ?>
	<?php endif; ?>

	<div class="ad-slot ad-slot-side"><span><?php esc_html_e( 'AD — 사이드바 하단 (300×250)', 'sample-01' ); ?></span></div>

	<?php // 최근 업로드 영상은 기사 상세에서만 노출. ?>
	<?php $recent_videos = is_singular( 'post' ) ? sample01_recent_videos( 4 ) : array(); ?>
	<?php if ( $recent_videos ) : ?>
		<section class="recent-videos">
			<h2 class="section-title"><?php esc_html_e( '최근 업로드 영상', 'sample-01' ); ?></h2>
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
</aside>
