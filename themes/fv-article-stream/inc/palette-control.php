<?php
/**
 * 커스터마이저 팔레트 선택 컨트롤: 라디오 목록 대신 색 견본 카드(틀·바탕·지면·포인트)로 보여준다.
 * 원본은 shared/palette-control.php — tools/build-palettes.py가 각 테마 inc/로 복사한다. 테마 쪽 사본은 직접 고치지 않는다.
 * 테마의 customize_register 안에서 require한다 (WP_Customize_Control이 그때 로드돼 있음).
 *
 * @package fv-article
 */

if ( ! class_exists( 'FV_Palette_Control' ) ) {
	class FV_Palette_Control extends WP_Customize_Control {
		public $type = 'fv_palette';

		/** @var array 팔레트 데이터 (inc/palettes.php 형식). */
		public $palettes = array();

		/** @var string 테마 기본 팔레트 키 — 카드에 "기본" 표시. */
		public $default_key = '';

		public function render_content() {
			$name = '_customize-radio-' . $this->id;
			?>
			<style>
				.fv-palettes { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-top: 8px; }
				.fv-palette { position: relative; display: grid; gap: 4px; padding: 6px; border: 1px solid #dcdcde; border-radius: 6px; background: #fff; cursor: pointer; }
				.fv-palette input { position: absolute; opacity: 0; pointer-events: none; }
				.fv-palette:has(input:checked) { border-color: #2271b1; box-shadow: 0 0 0 1px #2271b1; }
				.fv-palette input:focus-visible + .fv-palette-swatch { outline: 2px solid #2271b1; outline-offset: 2px; }
				.fv-palette-swatch { display: flex; height: 30px; border-radius: 4px; overflow: hidden; box-shadow: inset 0 0 0 1px rgba(0, 0, 0, 0.08); }
				.fv-palette-swatch span { flex: 1; }
				.fv-palette-swatch span:first-child { flex: 2; }
				.fv-palette-name { font-size: 12px; font-weight: 600; line-height: 1.3; }
				.fv-palette input:checked ~ .fv-palette-name { color: #2271b1; }
				.fv-palette-desc { font-size: 11px; color: #646970; line-height: 1.3; }
			</style>
			<?php if ( $this->label ) : ?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif; ?>
			<?php if ( $this->description ) : ?>
				<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
			<?php endif; ?>
			<div class="fv-palettes" role="radiogroup">
				<?php foreach ( $this->palettes as $key => $palette ) : ?>
					<?php
					$colors = $palette['colors'];
					$parts  = explode( ' — ', $palette['label'], 2 );
					?>
					<label class="fv-palette">
						<input type="radio" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $key ); ?>" <?php $this->link(); ?> <?php checked( $this->value(), $key ); ?>>
						<span class="fv-palette-swatch" aria-hidden="true">
							<?php foreach ( array( 'chrome', 'accent-on-chrome', 'bg', 'surface', 'accent' ) as $role ) : ?>
								<span style="background:<?php echo esc_attr( $colors[ $role ] ); ?>"></span>
							<?php endforeach; ?>
						</span>
						<span class="fv-palette-name"><?php echo esc_html( $parts[0] ); ?><?php echo $key === $this->default_key ? ' · 기본' : ''; ?></span>
						<?php if ( isset( $parts[1] ) ) : ?>
							<span class="fv-palette-desc"><?php echo esc_html( $parts[1] ); ?></span>
						<?php endif; ?>
					</label>
				<?php endforeach; ?>
			</div>
			<?php
		}
	}
}
