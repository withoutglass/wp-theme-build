<?php
/**
 * Archive template — 카테고리·태그·기자·날짜 목록.
 *
 * @package fv-news-portal
 */

get_header();
?>

<div class="layout">
	<div class="main-col">
		<header class="page-header">
			<h1 class="page-title"><?php echo esc_html( wp_strip_all_tags( get_the_archive_title() ) ); ?></h1>
			<?php the_archive_description( '<div class="page-description">', '</div>' ); ?>
		</header>

		<?php if ( have_posts() ) : ?>
			<div class="list">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/list-item' );
				endwhile;
				?>
			</div>
			<?php
			the_posts_pagination(
				array(
					'mid_size'  => 2,
					'prev_text' => '‹ 이전',
					'next_text' => '다음 ›',
				)
			);
			?>
		<?php else : ?>
			<p>기사가 없습니다.</p>
		<?php endif; ?>
	</div>

	<?php get_sidebar(); ?>
</div>

<?php get_footer(); ?>
