<?php
defined( 'ABSPATH' ) || exit;

function arcticrc_acf_json_save_point( $path ) {
	return get_template_directory() . '/acf-json';
}
add_filter( 'acf/settings/save_json', 'arcticrc_acf_json_save_point' );

function arcticrc_acf_json_load_point( $paths ) {
	$path = get_template_directory() . '/acf-json';

	if ( ! in_array( $path, $paths, true ) ) {
		$paths[] = $path;
	}

	return $paths;
}
add_filter( 'acf/settings/load_json', 'arcticrc_acf_json_load_point' );

function arcticrc_register_options_page() {
	if ( ! function_exists( 'acf_add_options_page' ) ) {
		return;
	}

	acf_add_options_page(
		array(
			'page_title' => 'Глобальные настройки сайта',
			'menu_title' => 'Сайт',
			'menu_slug'  => 'arcticrc-settings',
			'capability' => 'manage_options',
			'redirect'   => false,
			'position'   => 3,
			'icon_url'   => 'dashicons-admin-site-alt3',
		)
	);

}
add_action( 'acf/init', 'arcticrc_register_options_page' );


/**
 * Allow trusted SVG assets in the Media Library for administrators.
 */
function arcticrc_allow_svg_uploads( $mimes ) {
	if ( current_user_can( 'manage_options' ) ) {
		$mimes['svg'] = 'image/svg+xml';
	}

	return $mimes;
}
add_filter( 'upload_mimes', 'arcticrc_allow_svg_uploads' );

function arcticrc_fix_svg_filetype( $data, $file, $filename, $mimes ) {
	if ( 'svg' === strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) ) ) {
		$data['ext']  = 'svg';
		$data['type'] = 'image/svg+xml';
	}

	return $data;
}
add_filter( 'wp_check_filetype_and_ext', 'arcticrc_fix_svg_filetype', 10, 4 );
