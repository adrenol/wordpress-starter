<?php
/**
 * Theme bootstrap.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_enqueue_scripts', 'your_theme_enqueue_assets' );

/**
 * Enqueues theme styles.
 */
function your_theme_enqueue_assets() {
	$style_path = get_theme_file_path( 'style.css' );
	$build_path = get_theme_file_path( 'assets/css/site.css' );

	wp_enqueue_style(
		'your-theme-style',
		get_stylesheet_uri(),
		array(),
		file_exists( $style_path ) ? filemtime( $style_path ) : wp_get_theme()->get( 'Version' )
	);

	if ( file_exists( $build_path ) ) {
		wp_enqueue_style(
			'your-theme-tailwind',
			get_theme_file_uri( 'assets/css/site.css' ),
			array( 'your-theme-style' ),
			filemtime( $build_path )
		);
	}
}
