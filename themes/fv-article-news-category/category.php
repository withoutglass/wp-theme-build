<?php
/**
 * Category = 채널 페이지: 홈과 같은 모양, 기사는 이 카테고리 글만.
 * 아바타는 글 > 카테고리에서 지정한 채널 아이콘.
 *
 * @package fv-article-news-category
 */

get_header();

$term    = get_queried_object();
$icon_id = (int) get_term_meta( $term->term_id, 'channel_icon', true );

get_template_part(
	'template-parts/channel-home',
	null,
	array(
		'name'        => $term->name,
		'count'       => $term->count,
		'description' => $term->description,
		'avatar'      => $icon_id ? wp_get_attachment_image( $icon_id, 'medium', false, array( 'alt' => $term->name ) ) : '',
		'query'       => array( 'cat' => $term->term_id ),
	)
);

get_footer();
