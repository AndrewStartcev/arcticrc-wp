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


function arcticrc_image_url( $value, $fallback_asset = '' ) {
	if ( is_array( $value ) && ! empty( $value['url'] ) ) {
		return $value['url'];
	}

	if ( is_numeric( $value ) ) {
		$url = wp_get_attachment_image_url( (int) $value, 'full' );
		if ( $url ) {
			return $url;
		}
	}

	if ( is_string( $value ) && filter_var( $value, FILTER_VALIDATE_URL ) ) {
		return $value;
	}

	return $fallback_asset ? arcticrc_asset_url( $fallback_asset ) : '';
}

function arcticrc_header_logo_url() {
	$field = arcticrc_header_is_media() ? 'site_logo_light' : 'site_logo_blue';
	$fallback = arcticrc_header_is_media()
		? 'media/web/logo-99-4083.svg'
		: 'media/web/logo-99-1171.svg';

	return arcticrc_image_url( arcticrc_option( $field, '' ), $fallback );
}

function arcticrc_social_fallback_icon( $name ) {
	$name = strtolower( trim( (string) $name ) );

	if ( false !== strpos( $name, 'telegram' ) ) {
		return arcticrc_asset_url( 'media/web/social-telegram.svg' );
	}

	if ( false !== strpos( $name, 'whatsapp' ) ) {
		return arcticrc_asset_url( 'media/web/social-whatsapp.svg' );
	}

	return '';
}

function arcticrc_socials() {
	$socials = arcticrc_option( 'site_socials', array() );

	if ( ! is_array( $socials ) ) {
		return array();
	}

	$normalized = array();

	foreach ( $socials as $social ) {
		$name = isset( $social['name'] ) ? trim( (string) $social['name'] ) : '';
		$url = isset( $social['url'] ) ? trim( (string) $social['url'] ) : '';

		if ( ! $url ) {
			continue;
		}

		$normalized[] = array(
			'name'      => $name,
			'url'       => $url,
			'link_text' => ! empty( $social['link_text'] ) ? $social['link_text'] : $name,
			'icon'      => arcticrc_image_url(
				isset( $social['icon'] ) ? $social['icon'] : '',
				''
			) ?: arcticrc_social_fallback_icon( $name ),
		);
	}

	return $normalized;
}
