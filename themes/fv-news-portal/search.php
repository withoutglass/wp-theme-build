<?php
/**
 * Search results template.
 *
 * @package fv-news-portal
 */

get_header();

global $wp_query;
?>

<div class="layout">
	<div class="main-col">
		<header class="page-header">
			<?php if ( '' === get_search_query() ) : ?>
				<h1 class="page-title">검색</h1>
			<?php else : ?>
				<h1 class="page-title">‘<?php echo esc_html( get_search_query() ); ?>’ 검색 결과</h1>
				<p class="page-description"><?php echo esc_html( number_format_i18n( $wp_query->found_posts ) ); ?>건</p>
			<?php endif; ?>
			<?php get_search_form(); ?>
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
			<p>검색 결과가 없습니다. 다른 검색어로 찾아보세요.</p>
		<?php endif; ?>
	</div>

	<?php get_sidebar(); ?>
</div>

<?php get_footer(); ?>
