<?php
/**
 * Footer template: logo + policy links + business info + copyright.
 *
 * @package sample-01
 */
?>
</main>
<footer class="site-footer">
	<div class="footer-inner">
		<div class="footer-logo">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="footer-logo-text"><?php bloginfo( 'name' ); ?></a>
			<?php endif; ?>
		</div>

		<ul class="footer-links">
			<li><a href="<?php echo esc_url( home_url( '/terms/' ) ); ?>"><?php esc_html_e( '이용약관', 'sample-01' ); ?></a></li>
			<li><a href="<?php echo esc_url( home_url( '/privacy/' ) ); ?>"><?php esc_html_e( '개인정보처리방침', 'sample-01' ); ?></a></li>
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
			<?php bloginfo( 'name' ); ?><?php esc_html_e( '의 모든 콘텐츠는 저작권법의 보호를 받은 바, 무단 전재, 복사, 배포 등을 금합니다.', 'sample-01' ); ?><br>
			&copy; Copyright <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php esc_html_e( '주식회사 패스트뷰', 'sample-01' ); ?>. All Rights Reserved.
		</p>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
