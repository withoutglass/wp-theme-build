<?php
/**
 * 스트림 기사 한 편: 히어로 띠(메타·제목·원본 영상) → 본문(+중간 광고) → 원본 영상 임베드,
 * 넓은 화면(≥1200px)에서는 오른쪽 여백에 따라오는 광고.
 * 처음 연 기사(single.php)와 REST로 이어 붙이는 기사(functions.php fvarticle_stream_next)가 같은 마크업을 쓴다.
 * data-* 값은 assets/stream.js가 주소창·탭 제목 갱신과 페이지뷰 전송에 사용한다.
 *
 * @package fv-article-stream
 */

$stream_title = html_entity_decode( get_the_title() . ' – ' . get_bloginfo( 'name' ), ENT_QUOTES, 'UTF-8' );
// 처음 연 기사만 h1, 이어 붙인 기사는 h2 (페이지당 h1 하나).
$title_tag = ( is_singular( 'post' ) && get_queried_object_id() === get_the_ID() ) ? 'h1' : 'h2';
?>
<article <?php post_class( 'stream-item' ); ?>
	data-id="<?php the_ID(); ?>"
	data-url="<?php the_permalink(); ?>"
	data-title="<?php echo esc_attr( $stream_title ); ?>">
	<header class="hero">
		<div class="hero-inner">
			<p class="hero-meta">
				<?php bloginfo( 'name' ); ?> · <?php echo esc_html( get_the_date( 'Y.m.d H:i' ) ); ?>
			</p>
			<<?php echo $title_tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- h1/h2 고정값. ?> class="hero-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></<?php echo $title_tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<?php
			// 글별 원본 영상 URL(백엔드가 meta로 전달)이 없으면 채널 URL로 폴백.
			$original_video = get_post_meta( get_the_ID(), 'source_video_url', true );
			if ( ! $original_video ) {
				$original_video = fvarticle_stream_channel_option( 'youtube_url' );
			}
			?>
			<?php if ( $original_video ) : ?>
				<a class="btn-original-video" href="<?php echo esc_url( $original_video ); ?>" target="_blank" rel="noopener">
					<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5.5l11 6.5-11 6.5z"/></svg>
					<?php esc_html_e( '원본 영상 보기', 'fv-article-stream' ); ?>
				</a>
			<?php endif; ?>
		</div>
	</header>

	<div class="reader-stage">
		<div class="reader-column">
			<div class="entry-content">
				<?php echo fvarticle_stream_incontent_ad( apply_filters( 'the_content', get_the_content() ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- the_content 필터를 거친 본문. ?>
			</div>

			<?php
			// 본문 끝 원본 영상 임베드: 글별 source_video_url이 유튜브 영상일 때만.
			$embed_url = fvarticle_stream_youtube_embed_url( get_post_meta( get_the_ID(), 'source_video_url', true ) );
			?>
			<?php if ( $embed_url ) : ?>
				<div class="video-embed">
					<iframe src="<?php echo esc_url( $embed_url ); ?>"
						title="<?php esc_attr_e( '원본 영상', 'fv-article-stream' ); ?>"
						loading="lazy"
						allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
						allowfullscreen></iframe>
				</div>
			<?php endif; ?>
		</div>

		<aside class="reader-rail" aria-label="<?php esc_attr_e( '광고', 'fv-article-stream' ); ?>">
			<div class="ad-slot ad-slot-rail"></div>
		</aside>
	</div>
</article>
