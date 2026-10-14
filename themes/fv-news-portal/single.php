<?php
/**
 * Single post template: 경로(홈 › 카테고리) → 제목 → 바이라인 → 대표 이미지 → 본문 → 태그
 * → 기자 소개 → 이전/다음 기사 → 같은 카테고리 관련 기사 → 댓글.  [오른쪽] 사이드바.
 *
 * @package fv-news-portal
 */

get_header();
?>

<div class="layout">
	<div class="main-col">
		<?php
		while ( have_posts() ) :
			the_post();
			$category = fvnews_portal_primary_category();
			?>
			<article <?php post_class( 'article' ); ?>>
				<header class="article-header">
					<nav class="breadcrumb" aria-label="현재 위치">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>">홈</a>
						<?php if ( $category ) : ?>
							<span aria-hidden="true">›</span>
							<a href="<?php echo esc_url( get_category_link( $category ) ); ?>"><?php echo esc_html( $category->name ); ?></a>
						<?php endif; ?>
					</nav>
					<h1 class="article-title"><?php the_title(); ?></h1>
					<p class="meta byline"><?php fvnews_portal_byline(); ?></p>
				</header>

				<?php if ( fvnews_portal_show_featured_image() ) : ?>
					<figure class="article-figure">
						<?php the_post_thumbnail( 'large' ); ?>
						<?php if ( get_the_post_thumbnail_caption() ) : ?>
							<figcaption><?php the_post_thumbnail_caption(); ?></figcaption>
						<?php endif; ?>
					</figure>
				<?php endif; ?>

				<div class="entry-content">
					<?php the_content(); ?>
					<?php wp_link_pages(); ?>
				</div>

				<?php the_tags( '<p class="article-tags">', '', '</p>' ); ?>

				<section class="author-box">
					<?php echo get_avatar( get_the_author_meta( 'ID' ), 64 ); ?>
					<div>
						<p class="author-name"><a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><?php the_author(); ?> 기자</a></p>
						<?php if ( get_the_author_meta( 'description' ) ) : ?>
							<p class="author-description"><?php the_author_meta( 'description' ); ?></p>
						<?php endif; ?>
					</div>
				</section>

				<?php
				the_post_navigation(
					array(
						'prev_text' => '<span class="nav-label">이전 기사</span><span class="nav-title">%title</span>',
						'next_text' => '<span class="nav-label">다음 기사</span><span class="nav-title">%title</span>',
					)
				);
				?>
			</article>

			<?php
			$related = new WP_Query(
				array(
					'cat'                 => $category ? $category->term_id : 0,
					'posts_per_page'      => 4,
					'post__not_in'        => array( get_the_ID() ),
					'ignore_sticky_posts' => true,
					'no_found_rows'       => true,
				)
			);
			?>
			<?php if ( $related->have_posts() ) : ?>
				<section class="related">
					<header class="section-head">
						<h2 class="section-title"><?php echo $category ? esc_html( $category->name ) . ' 관련 기사' : '관련 기사'; ?></h2>
					</header>
					<div class="card-grid">
						<?php
						while ( $related->have_posts() ) :
							$related->the_post();
							get_template_part(
								'template-parts/card',
								null,
								array(
									'size'     => 'medium',
									'category' => false,
								)
							);
						endwhile;
						?>
					</div>
				</section>
				<?php wp_reset_postdata(); ?>
			<?php endif; ?>

			<?php
			if ( comments_open() || get_comments_number() ) {
				comments_template();
			}
			?>
		<?php endwhile; ?>
	</div>

	<?php get_sidebar(); ?>
</div>

<?php get_footer(); ?>
