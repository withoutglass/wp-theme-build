<?php
/**
 * Front page: channel profile + weekly popular grid + latest list with sidebar.
 *
 * @package fv-article-news-category
 */

get_header();

get_template_part(
	'template-parts/channel-home',
	null,
	array(
		'name'        => get_bloginfo( 'name' ),
		'count'       => wp_count_posts()->publish,
		'description' => get_bloginfo( 'description' ),
		'avatar'      => get_custom_logo(),
		'query'       => array(),
	)
);

get_footer();
