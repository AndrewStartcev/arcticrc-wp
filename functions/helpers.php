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
