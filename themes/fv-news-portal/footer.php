<?php
/**
 * Footer template: 푸터 메뉴 → 제호·소개 → 저작권.
 *
 * @package fv-news-portal
 */
?>
</main>
<footer class="site-footer">
	<div class="container">
		<?php if ( has_nav_menu( 'footer' ) ) : ?>
			<nav class="footer-nav" aria-label="푸터 메뉴">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'container'      => false,
						'depth'          => 1,
					)
				);
				?>
			</nav>
		<?php endif; ?>
		<p class="footer-title"><?php bloginfo( 'name' ); ?></p>
		<?php if ( get_bloginfo( 'description' ) ) : ?>
			<p class="footer-description"><?php bloginfo( 'description' ); ?></p>
		<?php endif; ?>
		<p class="footer-copyright">&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. 무단 전재 및 재배포 금지.</p>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
