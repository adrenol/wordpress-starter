<?php
/**
 * Theme bootstrap.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_enqueue_scripts', 'custom_theme_enqueue_assets' );
add_action( 'after_setup_theme', 'custom_theme_editor_styles' );

/**
 * Enqueues theme styles.
 */
function custom_theme_enqueue_assets() {
	$style_path = get_theme_file_path( 'style.css' );
	$build_path = get_theme_file_path( 'assets/css/site.css' );

	wp_enqueue_style(
		'custom-theme-style',
		get_stylesheet_uri(),
		array(),
		file_exists( $style_path ) ? filemtime( $style_path ) : wp_get_theme()->get( 'Version' )
	);

	if ( file_exists( $build_path ) ) {
		wp_enqueue_style(
			'custom-theme-tailwind',
			get_theme_file_uri( 'assets/css/site.css' ),
			array( 'custom-theme-style' ),
			filemtime( $build_path )
		);
	}
}

/**
 * Loads Tailwind styles in the Gutenberg editor.
 */
function custom_theme_editor_styles() {
	add_editor_style( 'assets/css/site.css' );
}
