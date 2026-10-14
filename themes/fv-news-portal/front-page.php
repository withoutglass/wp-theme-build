<?php
/**
 * Front page (포털형): [본문] 헤드라인(대표 1 + 주요 4) → 카테고리 섹션(대표 카드 1 + 제목 4)  [오른쪽] 사이드바.
 * 카테고리 섹션은 글이 있는 최상위 카테고리를 글 수 순으로 최대 6개 (functions.php fvnews_portal_front_categories).
 * 헤드라인에 나온 글은 카테고리 섹션에서 빼서 같은 기사가 두 번 보이지 않게 한다.
 *
 * @package fv-news-portal
 */

get_header();

$headline = new WP_Query(
	array(
		'posts_per_page'      => 5,
		'ignore_sticky_posts' => false,
		'no_found_rows'       => true,
	)
);
// 고정(sticky) 글은 posts_per_page와 별개로 앞에 덧붙으므로 헤드라인 5개로 자른다 — 고정 글이 대표 기사가 된다.
$headline->posts      = array_slice( $headline->posts, 0, 5 );
$headline->post_count = count( $headline->posts );
$shown                = wp_list_pluck( $headline->posts, 'ID' );
?>

<div class="layout">
	<div class="main-col">
		<?php if ( $headline->have_posts() ) : ?>
			<section class="headline" aria-label="헤드라인">
				<?php
				$headline->the_post();
				?>
				<div class="headline-lead">
					<?php
					get_template_part(
						'template-parts/card',
						null,
						array(
							'size'    => 'large',
							'excerpt' => true,
							'heading' => 'h2',
						)
					);
					?>
				</div>
				<ul class="headline-list">
					<?php
					while ( $headline->have_posts() ) :
						$headline->the_post();
						?>
						<li><?php get_template_part( 'template-parts/card', null, array( 'size' => 'medium' ) ); ?></li>
					<?php endwhile; ?>
				</ul>
			</section>
			<?php wp_reset_postdata(); ?>
		<?php endif; ?>

		<?php foreach ( fvnews_portal_front_categories() as $category ) : ?>
			<?php
			$section = new WP_Query(
				array(
					'cat'                 => $category->term_id,
					'posts_per_page'      => 5,
					'post__not_in'        => $shown,
					'ignore_sticky_posts' => true,
					'no_found_rows'       => true,
				)
			);
			if ( ! $section->have_posts() ) {
				continue;
			}
			?>
			<section class="cat-section">
				<header class="section-head">
					<h2 class="section-title"><a href="<?php echo esc_url( get_category_link( $category ) ); ?>"><?php echo esc_html( $category->name ); ?></a></h2>
					<a class="section-more" href="<?php echo esc_url( get_category_link( $category ) ); ?>">더보기 <span aria-hidden="true">›</span></a>
				</header>
				<div class="cat-grid">
					<?php
					$section->the_post();
					get_template_part(
						'template-parts/card',
						null,
						array(
							'excerpt'  => true,
							'category' => false,
						)
					);
					?>
					<ul class="title-list">
						<?php
						while ( $section->have_posts() ) :
							$section->the_post();
							?>
							<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
						<?php endwhile; ?>
					</ul>
				</div>
			</section>
			<?php wp_reset_postdata(); ?>
		<?php endforeach; ?>
	</div>

	<?php get_sidebar(); ?>
</div>

<?php get_footer(); ?>
