<?php
/**
 * 채널 SNS 버튼: 홈 프로필 블록 + 기사 하단 채널 박스 공용.
 * URL은 어드민 > 채널 설정에서 관리하며, 빈 항목의 버튼은 숨겨진다.
 *
 * @package sample-01
 */

$youtube_url   = sample01_channel_option( 'youtube_url' );
$instagram_url = sample01_channel_option( 'instagram_url' );
$x_url         = sample01_channel_option( 'x_url' );
$tiktok_url    = sample01_channel_option( 'tiktok_url' );
$contact_email = sample01_channel_option( 'contact_email' );
?>
<div class="channel-links">
	<?php if ( $youtube_url ) : ?>
		<a class="btn-youtube" href="<?php echo esc_url( $youtube_url ); ?>" target="_blank" rel="noopener">
			<svg viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M23 12c0-2.8-.3-4.7-.6-5.7a3 3 0 0 0-2.1-2.1C18.6 3.7 12 3.7 12 3.7s-6.6 0-8.3.5a3 3 0 0 0-2.1 2.1C1.3 7.3 1 9.2 1 12s.3 4.7.6 5.7a3 3 0 0 0 2.1 2.1c1.7.5 8.3.5 8.3.5s6.6 0 8.3-.5a3 3 0 0 0 2.1-2.1c.3-1 .6-2.9.6-5.7zM9.8 8.6l6 3.4-6 3.4z"/></svg>
			<?php esc_html_e( '구독하기', 'sample-01' ); ?>
		</a>
	<?php endif; ?>
	<?php if ( $instagram_url ) : ?>
		<a class="btn-instagram" href="<?php echo esc_url( $instagram_url ); ?>" target="_blank" rel="noopener">
			<svg viewBox="0 0 24 24" aria-hidden="true" style="fill:none" stroke="currentColor" stroke-width="2"><rect x="2.5" y="2.5" width="19" height="19" rx="5.5"/><circle cx="12" cy="12" r="4.2"/><circle cx="17.4" cy="6.6" r="1.3" style="fill:currentColor" stroke="none"/></svg>
			<?php esc_html_e( '인스타', 'sample-01' ); ?>
		</a>
	<?php endif; ?>
	<?php if ( $x_url ) : ?>
		<a class="btn-x" href="<?php echo esc_url( $x_url ); ?>" target="_blank" rel="noopener">
			<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18.9 1.2h3.7l-8.1 9.2L24 22.8h-7.4l-5.8-7.6-6.6 7.6H.5l8.6-9.8L0 1.2h7.6l5.2 6.9zm-1.3 19.5h2L6.5 3.2h-2.2z"/></svg>
			<?php esc_html_e( 'X', 'sample-01' ); ?>
		</a>
	<?php endif; ?>
	<?php if ( $tiktok_url ) : ?>
		<a class="btn-tiktok" href="<?php echo esc_url( $tiktok_url ); ?>" target="_blank" rel="noopener">
			<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12.53.02C13.84 0 15.14.01 16.44 0c.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>
			<?php esc_html_e( '틱톡', 'sample-01' ); ?>
		</a>
	<?php endif; ?>
	<?php if ( $contact_email ) : ?>
		<a class="btn-mail" href="mailto:<?php echo esc_attr( $contact_email ); ?>">
			<svg viewBox="0 0 24 24" aria-hidden="true" style="fill:none" stroke="currentColor" stroke-width="2"><rect x="2.5" y="4.5" width="19" height="15" rx="2.5"/><path d="M3.5 7l8.5 6.5L20.5 7"/></svg>
			<?php esc_html_e( '메일 문의', 'sample-01' ); ?>
		</a>
	<?php endif; ?>
</div>
