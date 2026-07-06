<?php
/**
 * Sample 00 theme functions.
 *
 * @package sample-00
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function sample00_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );

	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'sample-00' ),
		)
	);
}
add_action( 'after_setup_theme', 'sample00_setup' );

function sample00_enqueue() {
	wp_enqueue_style(
		'sample-00-style',
		get_stylesheet_uri(),
		array(),
		wp_get_theme()->get( 'Version' )
	);
}
add_action( 'wp_enqueue_scripts', 'sample00_enqueue' );
