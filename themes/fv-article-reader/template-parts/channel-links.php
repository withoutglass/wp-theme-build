<?php
/**
 * 채널 SNS 버튼: 홈 프로필 블록 + 기사 하단 채널 박스 공용.
 * URL은 어드민 > 채널 설정에서 관리하며, 빈 항목의 버튼은 숨겨진다.
 * SNS를 추가할 때는 아래 목록에 한 줄 + 채널 설정 필드·저장(functions.php) + 버튼 색(style.css .btn-*).
 *
 * @package fv-article-reader
 */

// 채널 설정 키 => array( 버튼 클래스, 라벨, 아이콘 SVG )
$channel_links = array(
	'youtube_url'    => array( 'btn-youtube', __( '구독하기', 'fv-article-reader' ), '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M23 12c0-2.8-.3-4.7-.6-5.7a3 3 0 0 0-2.1-2.1C18.6 3.7 12 3.7 12 3.7s-6.6 0-8.3.5a3 3 0 0 0-2.1 2.1C1.3 7.3 1 9.2 1 12s.3 4.7.6 5.7a3 3 0 0 0 2.1 2.1c1.7.5 8.3.5 8.3.5s6.6 0 8.3-.5a3 3 0 0 0 2.1-2.1c.3-1 .6-2.9.6-5.7zM9.8 8.6l6 3.4-6 3.4z"/></svg>' ),
	'instagram_url'  => array( 'btn-instagram', __( '인스타', 'fv-article-reader' ), '<svg viewBox="0 0 24 24" aria-hidden="true" style="fill:none" stroke="currentColor" stroke-width="2"><rect x="2.5" y="2.5" width="19" height="19" rx="5.5"/><circle cx="12" cy="12" r="4.2"/><circle cx="17.4" cy="6.6" r="1.3" style="fill:currentColor" stroke="none"/></svg>' ),
	'x_url'          => array( 'btn-x', __( 'X', 'fv-article-reader' ), '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18.9 1.2h3.7l-8.1 9.2L24 22.8h-7.4l-5.8-7.6-6.6 7.6H.5l8.6-9.8L0 1.2h7.6l5.2 6.9zm-1.3 19.5h2L6.5 3.2h-2.2z"/></svg>' ),
	'tiktok_url'     => array( 'btn-tiktok', __( '틱톡', 'fv-article-reader' ), '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12.53.02C13.84 0 15.14.01 16.44 0c.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>' ),
	'facebook_url'   => array( 'btn-facebook', __( '페이스북', 'fv-article-reader' ), '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 13.5h2.5l1-4H14v-2c0-1.03 0-2 2-2h1.5V2.14c-.33-.04-1.56-.14-2.86-.14C11.93 2 10 3.66 10 6.7v2.8H7v4h3V22h4z"/></svg>' ),
	'threads_url'    => array( 'btn-threads', __( '스레드', 'fv-article-reader' ), '<svg viewBox="0 0 24 24" aria-hidden="true" style="fill:none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="4"/><path d="M16 8v5a3 3 0 0 0 6 0v-1a10 10 0 1 0-3.92 7.94"/></svg>' ),
	'naver_blog_url' => array( 'btn-naver-blog', __( '블로그', 'fv-article-reader' ), '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M16.27 3v9.1L7.73 3H2v18h5.73v-9.1L16.27 21H22V3z"/></svg>' ),
	'naver_cafe_url' => array( 'btn-naver-cafe', __( '카페', 'fv-article-reader' ), '<svg viewBox="0 0 24 24" aria-hidden="true" style="fill:none" stroke="currentColor" stroke-width="2"><path d="M18 8h1a4 4 0 0 1 0 8h-1"/><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4z"/><path d="M6 1v3M10 1v3M14 1v3"/></svg>' ),
	'kakao_url'      => array( 'btn-kakao', __( '카카오톡', 'fv-article-reader' ), '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3C6.48 3 2 6.48 2 10.8c0 2.78 1.86 5.22 4.66 6.6l-.95 3.48c-.08.3.26.54.52.37l4.14-2.74c.53.06 1.07.09 1.63.09 5.52 0 10-3.48 10-7.8S17.52 3 12 3z"/></svg>' ),
	'band_url'       => array( 'btn-band', __( '밴드', 'fv-article-reader' ), '<svg viewBox="0 0 24 24" aria-hidden="true" style="fill:none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>' ),
);
$contact_email = fvarticle_reader_channel_option( 'contact_email' );
?>
<div class="channel-links">
	<?php foreach ( $channel_links as $key => $link ) : ?>
		<?php $url = fvarticle_reader_channel_option( $key ); ?>
		<?php if ( $url ) : ?>
			<a class="<?php echo esc_attr( $link[0] ); ?>" href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener">
				<?php echo $link[2]; // phpcs:ignore WordPress.Security.EscapeOutput -- 위 목록의 고정 SVG ?>
				<?php echo esc_html( $link[1] ); ?>
			</a>
		<?php endif; ?>
	<?php endforeach; ?>
	<?php if ( $contact_email ) : ?>
		<a class="btn-mail" href="mailto:<?php echo esc_attr( $contact_email ); ?>">
			<svg viewBox="0 0 24 24" aria-hidden="true" style="fill:none" stroke="currentColor" stroke-width="2"><rect x="2.5" y="4.5" width="19" height="15" rx="2.5"/><path d="M3.5 7l8.5 6.5L20.5 7"/></svg>
			<?php esc_html_e( '메일 문의', 'fv-article-reader' ); ?>
		</a>
	<?php endif; ?>
</div>
