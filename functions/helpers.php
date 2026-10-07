<?php
defined( 'ABSPATH' ) || exit;

function arcticrc_asset_url( $path ) {
	return get_template_directory_uri() . '/assets/' . ltrim( $path, '/' );
}

function arcticrc_option( $field, $fallback = '' ) {
	if ( function_exists( 'get_field' ) ) {
		$value = get_field( $field, 'option' );

		if ( null !== $value && '' !== $value && false !== $value ) {
			return $value;
		}
	}

	return $fallback;
}

function arcticrc_phone_href( $phone ) {
	return preg_replace( '/[^\d+]/', '', (string) $phone );
}

function arcticrc_menu_items( $location ) {
	$locations = get_nav_menu_locations();

	if ( empty( $locations[ $location ] ) ) {
		return array();
	}

	$items = wp_get_nav_menu_items( $locations[ $location ] );

	return is_array( $items ) ? $items : array();
}

/**
 * Header visual mode.
 *
 * Media mode is used when the header sits on top of a hero image.
 * Keep this decision in one place instead of duplicating it in templates.
 */
function arcticrc_header_mode() {
	if ( is_front_page() ) {
		return 'media';
	}

	if ( is_page( array( 'equipment-rent', 'equipment-sale' ) ) ) {
		return 'media';
	}

	if ( is_post_type_archive( 'equipment' ) ) {
		return 'media';
	}

	return 'light';
}

function arcticrc_header_is_media() {
	return 'media' === arcticrc_header_mode();
}

function arcticrc_header_logo() {
	return arcticrc_header_is_media()
		? 'media/web/logo-99-4083.svg'
		: 'media/web/logo-99-1171.svg';
}

function arcticrc_overlay_page_class() {
	if ( is_front_page() ) {
		return 'page page--home page--overlay';
	}

	if ( is_page( array( 'equipment-rent', 'equipment-sale' ) ) || is_post_type_archive( 'equipment' ) ) {
		return 'page page--equipment page--overlay';
	}

	return 'page page--overlay';
}
