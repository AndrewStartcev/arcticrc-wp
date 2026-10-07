<?php
/**
 * Contacts page integration.
 *
 * @package ArcticRC
 */

defined( 'ABSPATH' ) || exit;

const ARCTICRC_CONTACTS_SEED_VERSION = '2026-10-07-2';

function arcticrc_contacts_page_id() {
	$page = get_page_by_path( 'contacts', OBJECT, 'page' );

	return $page ? (int) $page->ID : 0;
}

function arcticrc_contacts_block_visible( $field_name, $page_id = 0 ) {
	$page_id = $page_id ? (int) $page_id : arcticrc_contacts_page_id();

	if ( ! $page_id || ! function_exists( 'get_field' ) ) {
		return true;
	}

	$value = get_field( $field_name, $page_id );

	return false !== $value && '0' !== (string) $value;
}

function arcticrc_contacts_map_is_interactive( $page_id = 0 ) {
	$page_id = $page_id ? (int) $page_id : arcticrc_contacts_page_id();

	return arcticrc_contacts_block_visible( 'contacts_map_visible', $page_id )
		&& 'map' === get_field( 'contacts_map_mode', $page_id );
}

function arcticrc_seed_contacts_page() {
	if ( ! is_admin() || ! current_user_can( 'manage_options' ) || ! function_exists( 'update_field' ) ) {
		return;
	}

	$page_id = arcticrc_contacts_page_id();

	if ( ! $page_id ) {
		return;
	}

	update_post_meta( $page_id, '_wp_page_template', 'page-contacts.php' );

	$defaults = array(
		'field_arcticrc_contacts_title'       => 'Связь с теми, кто не подводит',
		'field_arcticrc_contacts_intro'       => "Свяжитесь с нами по телефону, оставьте заявку на консультацию или приезжайте в офис!

Мы ответим на ваши вопросы",
		'field_arcticrc_contacts_map_mode'    => 'map',
		'field_arcticrc_contacts_map_lat'     => '55.7294',
		'field_arcticrc_contacts_map_lng'     => '37.6468',
		'field_arcticrc_contacts_map_zoom'    => 16,
	);

	$previous_seed = get_post_meta( $page_id, '_arcticrc_contacts_seed_version', true );

	foreach ( $defaults as $field_key => $value ) {
		$current = get_field( $field_key, $page_id );

		if ( null === $current || '' === $current ) {
			update_field( $field_key, $value, $page_id );
		}
	}

	if ( ARCTICRC_CONTACTS_SEED_VERSION !== $previous_seed ) {
		update_field( 'field_arcticrc_contacts_map_mode', 'map', $page_id );

		if ( ! get_field( 'contacts_map_lat', $page_id ) ) {
			update_field( 'field_arcticrc_contacts_map_lat', '55.7294', $page_id );
		}

		if ( ! get_field( 'contacts_map_lng', $page_id ) ) {
			update_field( 'field_arcticrc_contacts_map_lng', '37.6468', $page_id );
		}
	}

	$map_image_id = arcticrc_seed_media_asset(
		'media/web/71f1e97da12518a490ccf77ef5801e4017dd3bf2.webp',
		'Контакты — карта офиса'
	);

	if ( $map_image_id && ! get_field( 'contacts_map_image', $page_id ) ) {
		update_field( 'field_arcticrc_contacts_map_image', $map_image_id, $page_id );
	}

	update_post_meta( $page_id, '_arcticrc_contacts_seed_version', ARCTICRC_CONTACTS_SEED_VERSION );
}
add_action( 'admin_init', 'arcticrc_seed_contacts_page', 55 );
