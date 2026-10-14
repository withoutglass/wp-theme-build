<?php
/**
 * Main template — 최신 기사 목록 (설정 > 읽기에서 "글 페이지"를 지정했을 때)과 최종 폴백.
 *
 * @package fv-news-portal
 */

get_header();
?>

<div class="layout">
	<div class="main-col">
		<header class="page-header">
			<h1 class="page-title">최신 기사</h1>
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
