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
	<?php if ( $contact_email ) : ?>
		<a class="btn-mail" href="mailto:<?php echo esc_attr( $contact_email ); ?>">
			<svg viewBox="0 0 24 24" aria-hidden="true" style="fill:none" stroke="currentColor" stroke-width="2"><rect x="2.5" y="4.5" width="19" height="15" rx="2.5"/><path d="M3.5 7l8.5 6.5L20.5 7"/></svg>
			<?php esc_html_e( '메일 문의', 'sample-01' ); ?>
		</a>
	<?php endif; ?>
</div>
