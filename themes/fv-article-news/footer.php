<?php
/**
 * Footer template: logo + policy links + business info + copyright.
 *
 * @package fv-article-news
 */
?>
<?php if ( is_single() || is_front_page() ) : ?>
	<?php // 좌우 사이드 레일(160×600) 자리 — 기사 상세·홈. 넓은 화면(≥1416px)에서만 콘텐츠 바깥에 보이고 스크롤을 따라온다. 광고 코드는 .ad-rail-slot을 채운다. ?>
	<div class="ad-rail ad-rail-left"><div class="ad-rail-slot"></div></div>
	<div class="ad-rail ad-rail-right"><div class="ad-rail-slot"></div></div>
<?php endif; ?>
</main>
<footer class="site-footer">
	<div class="footer-inner">
		<div class="footer-logo">
			<?php $footer_logo = (int) fvarticle_news_channel_option( 'footer_logo' ); ?>
			<?php if ( $footer_logo && wp_attachment_is_image( $footer_logo ) ) : ?>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="custom-logo-link" rel="home"><?php echo wp_get_attachment_image( $footer_logo, 'medium', false, array( 'alt' => get_bloginfo( 'name' ) ) ); ?></a>
			<?php elseif ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="footer-logo-text"><?php bloginfo( 'name' ); ?></a>
			<?php endif; ?>
		</div>

		<ul class="footer-links">
			<li><a href="<?php echo esc_url( home_url( '/terms/' ) ); ?>"><?php esc_html_e( '이용약관', 'fv-article-news' ); ?></a></li>
			<li><a href="<?php echo esc_url( home_url( '/privacy/' ) ); ?>"><?php esc_html_e( '개인정보처리방침', 'fv-article-news' ); ?></a></li>
			<?php $footer_email = fvarticle_news_channel_option( 'contact_email' ); ?>
			<?php if ( $footer_email ) : ?>
				<li><a href="mailto:<?php echo esc_attr( $footer_email ); ?>"><?php esc_html_e( '문의하기', 'fv-article-news' ); ?></a></li>
			<?php endif; ?>
		</ul>

		<p class="footer-info">
			<span>주식회사 패스트뷰</span>
			<span>대표: 박상우</span>
			<span>사업자등록번호: 619-87-00936</span>
			<span>주소: 서울특별시 서초구 서초대로 396, 18층</span>
			<span>대표번호: 02-6205-0936</span>
			<span>팩스: 070-8224-2545</span>
			<span>대표메일: team@fastviewkorea.com</span>
		</p>

		<p class="footer-copyright">
			<?php bloginfo( 'name' ); ?><?php esc_html_e( '의 모든 콘텐츠는 저작권법의 보호를 받은 바, 무단 전재, 복사, 배포 등을 금합니다.', 'fv-article-news' ); ?><br>
			&copy; Copyright <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php esc_html_e( '주식회사 패스트뷰', 'fv-article-news' ); ?>. All Rights Reserved.
		</p>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
