<?php
/**
 * 채널 레일: 데스크톱(≥1024px)은 왼쪽 고정 세로 레일, 모바일은 상단 채널 바 + 가로 TOP 칩.
 * 채널 소개·SNS 버튼(.rail-detail)은 모바일에서는 홈에서만 보인다.
 * 실시간 TOP — 인기 집계 미구현, 우선 최신글로 채운다.
 *
 * @package fv-article-rail
 */

$top = new WP_Query(
	array(
		'posts_per_page'      => 5,
		'ignore_sticky_posts' => true,
	)
);
?>
<aside class="site-rail">
	<div class="rail-inner">
		<div class="rail-brand">
			<a class="rail-home" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<span class="rail-avatar">
					<?php if ( has_custom_logo() ) : ?>
						<img src="<?php echo esc_url( wp_get_attachment_image_url( get_theme_mod( 'custom_logo' ), 'thumbnail' ) ); ?>" alt="">
					<?php else : ?>
						<span class="rail-avatar-fallback"><?php echo esc_html( mb_substr( get_bloginfo( 'name' ), 0, 1 ) ); ?></span>
					<?php endif; ?>
				</span>
				<span class="rail-name"><?php bloginfo( 'name' ); ?></span>
			</a>
			<nav class="rail-nav">
				<ul>
					<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( '홈', 'fv-article-rail' ); ?></a></li>
					<?php $rail_youtube = fvarticle_rail_channel_option( 'youtube_url' ); ?>
					<?php if ( $rail_youtube ) : ?>
						<li><a href="<?php echo esc_url( $rail_youtube ); ?>" target="_blank" rel="noopener"><?php esc_html_e( '유튜브 바로가기', 'fv-article-rail' ); ?></a></li>
					<?php endif; ?>
				</ul>
			</nav>
		</div>

		<div class="rail-detail">
			<p class="rail-count">
				<?php esc_html_e( '발행 기사 수', 'fv-article-rail' ); ?>
				<strong><?php echo esc_html( wp_count_posts()->publish ); ?></strong>
			</p>
			<p class="rail-description"><?php bloginfo( 'description' ); ?></p>
			<?php get_template_part( 'template-parts/channel-links' ); ?>
		</div>

		<?php if ( $top->have_posts() ) : ?>
			<section class="rail-top">
				<h2 class="rail-title"><?php esc_html_e( '실시간 TOP', 'fv-article-rail' ); ?></h2>
				<ol class="top-list">
					<?php
					while ( $top->have_posts() ) :
						$top->the_post();
						?>
						<li class="top-item"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
					<?php endwhile; ?>
				</ol>
			</section>
			<?php wp_reset_postdata(); ?>
		<?php endif; ?>
	</div>
</aside>
