<?php
defined( 'ABSPATH' ) || exit;

add_filter( 'use_block_editor_for_post_type', '__return_false', 100 );
add_filter( 'gutenberg_use_widgets_block_editor', '__return_false' );
add_filter( 'use_widgets_block_editor', '__return_false' );
add_filter( 'xmlrpc_enabled', '__return_false' );

function arcticrc_disable_block_patterns() {
	remove_theme_support( 'core-block-patterns' );
}
add_action( 'after_setup_theme', 'arcticrc_disable_block_patterns', 100 );

function arcticrc_remove_block_styles() {
	foreach ( array(
		'wp-block-library',
		'wp-block-library-theme',
		'global-styles',
		'classic-theme-styles',
	) as $style ) {
		wp_dequeue_style( $style );
	}
}
add_action( 'wp_enqueue_scripts', 'arcticrc_remove_block_styles', 100 );

function arcticrc_clean_admin_bar( $wp_admin_bar ) {
	$wp_admin_bar->remove_node( 'wp-logo' );
	$wp_admin_bar->remove_node( 'customize' );
}
add_action( 'admin_bar_menu', 'arcticrc_clean_admin_bar', 999 );

remove_action( 'welcome_panel', 'wp_welcome_panel' );
