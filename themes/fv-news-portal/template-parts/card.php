<?php
/**
 * 카드: 16:9 대표 이미지 + (카테고리) + 제목 (+ 요약). 홈 헤드라인·카테고리 섹션·관련 기사 공용.
 * $args: size(이미지 크기), excerpt(요약 표시), category(카테고리 라벨 표시), heading(제목 태그).
 *
 * @package fv-news-portal
 */

$args     = wp_parse_args(
	$args,
	array(
		'size'     => 'medium_large',
		'excerpt'  => false,
		'category' => true,
		'heading'  => 'h3',
	)
);
$category = $args['category'] ? fvnews_portal_primary_category() : null;
$heading  = in_array( $args['heading'], array( 'h2', 'h3' ), true ) ? $args['heading'] : 'h3';
?>
<article <?php post_class( 'card' ); ?>>
	<a class="card-link" href="<?php the_permalink(); ?>">
		<span class="thumb">
			<?php if ( has_post_thumbnail() ) : ?>
				<?php the_post_thumbnail( $args['size'] ); ?>
			<?php endif; ?>
		</span>
		<?php if ( $category ) : ?>
			<span class="card-category"><?php echo esc_html( $category->name ); ?></span>
		<?php endif; ?>
		<<?php echo $heading; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- h2/h3 고정값. ?> class="card-title"><?php the_title(); ?></<?php echo $heading; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	</a>
	<?php if ( $args['excerpt'] ) : ?>
		<p class="card-excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
	<?php endif; ?>
</article>
