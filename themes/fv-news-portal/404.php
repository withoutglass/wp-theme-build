<?php
/**
 * 404 not found template.
 *
 * @package fv-news-portal
 */

get_header();
?>

<div class="layout">
	<div class="main-col">
		<header class="page-header">
			<h1 class="page-title">페이지를 찾을 수 없습니다</h1>
			<p class="page-description">주소가 바뀌었거나 삭제된 기사일 수 있습니다. 검색으로 찾아보세요.</p>
			<?php get_search_form(); ?>
		</header>
	</div>

	<?php get_sidebar(); ?>
</div>

<?php get_footer(); ?>
