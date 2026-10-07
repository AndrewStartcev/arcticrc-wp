<?php
/**
 * Home page data and automatic seed.
 *
 * @package ArcticRC
 */

defined( 'ABSPATH' ) || exit;

const ARCTICRC_HOME_SEED_VERSION = '2026-10-07-2';

function arcticrc_home_page_id() {
	$page_id = (int) get_option( 'page_on_front' );

	if ( $page_id ) {
		return $page_id;
	}

	$page = get_page_by_path( 'home', OBJECT, 'page' );

	return $page ? (int) $page->ID : 0;
}

function arcticrc_home_value( $field, $fallback = '' ) {
	$page_id = arcticrc_home_page_id();

	if ( $page_id && function_exists( 'get_field' ) ) {
		$value = get_field( $field, $page_id );

		if ( null !== $value && '' !== $value && false !== $value ) {
			return $value;
		}
	}

	return $fallback;
}

function arcticrc_home_seed_image( $path, $title ) {
	return function_exists( 'arcticrc_seed_media_asset' )
		? arcticrc_seed_media_asset( $path, $title )
		: 0;
}

function arcticrc_seed_home_content() {
	if ( ! is_admin() || ! current_user_can( 'manage_options' ) || ! function_exists( 'update_field' ) ) {
		return;
	}

	$page_id = arcticrc_home_page_id();

	if ( ! $page_id ) {
		return;
	}

	$assets = array(
		'hero_desktop'      => arcticrc_home_seed_image( 'media/web/home-hero-desktop.webp', 'Главная — Hero desktop' ),
		'hero_mobile'       => arcticrc_home_seed_image( 'media/web/home-hero-mobile.webp', 'Главная — Hero mobile' ),
		'service'           => arcticrc_home_seed_image( 'media/web/18d059bc1b44b09a13800860e84b987e755b9dc4.webp', 'Главная — услуга' ),
		'geography_desktop' => arcticrc_home_seed_image( 'media/web/home-geography-desktop.webp', 'Главная — География desktop' ),
		'geography_mobile'  => arcticrc_home_seed_image( 'media/web/home-geography-mobile.webp', 'Главная — География mobile' ),
		'client_1'          => arcticrc_home_seed_image( 'media/web/bded0892b546505ea0e373867fc35fd89d87f761.webp', 'Заказчик 1' ),
		'client_2'          => arcticrc_home_seed_image( 'media/web/client-02.svg', 'Алроса' ),
		'client_3'          => arcticrc_home_seed_image( 'media/web/c0d4d87e866760e176a787f2b1ac770617adac28.webp', 'Заказчик 3' ),
		'client_4'          => arcticrc_home_seed_image( 'media/web/client-04.svg', 'ПИК' ),
		'client_5'          => arcticrc_home_seed_image( 'media/web/465c874c58a0290230a82a75f33e7348bd44f606.webp', 'Заказчик 5' ),
		'client_6'          => arcticrc_home_seed_image( 'media/web/a8ad7cd49a82e898b801cc6563aa56383edd5a19.webp', 'Заказчик 6' ),
		'documents_desktop' => arcticrc_home_seed_image( 'media/web/home-documents-desktop.webp', 'Главная — Документация desktop' ),
		'documents_mobile'  => arcticrc_home_seed_image( 'media/web/home-documents-mobile.webp', 'Главная — Документация mobile' ),
	);

	$defaults = array(
		'field_arcticrc_home_services_title' => 'Наши услуги',
		'field_arcticrc_home_geo_title'      => 'Наша география — от Мурманска до Владивостока',
		'field_arcticrc_home_geo_lead'       => 'Оказываем услуги по всей территории России, в том числе в условиях крайнего Севера.',
		'field_arcticrc_home_geo_mode'       => 'map',
		'field_arcticrc_home_geo_lat'        => '61.5240',
		'field_arcticrc_home_geo_lng'        => '105.3188',
		'field_arcticrc_home_geo_zoom'       => 3,
		'field_arcticrc_home_clients_title'  => 'Наши заказчики — ориентир в надёжности',
		'field_arcticrc_home_docs_title'     => 'Документация компании',
		'field_arcticrc_home_docs_lead'      => 'Вся работа подтверждена СРО, лицензиями и сертификатами — это гарантия качества, безопасности и ответственности на каждом этапе.',
		'field_arcticrc_home_contact_title'  => 'Начнем с разговора — доведем до результата',
		'field_arcticrc_home_contact_intro'  => 'Свяжитесь с нами по телефону, оставьте заявку на консультацию или приезжайте в офис! Мы ответим на ваши вопросы',
	);

	foreach ( $defaults as $field_key => $value ) {
		$current = get_field( $field_key, $page_id );
		if ( null === $current || '' === $current || false === $current ) {
			update_field( $field_key, $value, $page_id );
		}
	}

	$image_defaults = array(
		'field_arcticrc_home_geo_desktop'     => $assets['geography_desktop'],
		'field_arcticrc_home_geo_mobile'      => $assets['geography_mobile'],
		'field_arcticrc_home_docs_bg_desktop' => $assets['documents_desktop'],
		'field_arcticrc_home_docs_bg_mobile'  => $assets['documents_mobile'],
	);

	foreach ( $image_defaults as $field_key => $attachment_id ) {
		if ( $attachment_id && ! get_field( $field_key, $page_id ) ) {
			update_field( $field_key, $attachment_id, $page_id );
		}
	}

	if ( ! get_field( 'field_arcticrc_home_hero', $page_id ) ) {
		$slides = array();
		for ( $index = 0; $index < 8; $index++ ) {
			$slides[] = array(
				'field_arcticrc_home_hero_title'   => 'Инженерные изыскания',
				'field_arcticrc_home_hero_desktop' => $assets['hero_desktop'],
				'field_arcticrc_home_hero_mobile'  => $assets['hero_mobile'],
				'field_arcticrc_home_hero_bullets' => array(
					array( 'field_arcticrc_home_hero_bullet' => 'Геотехнический мониторинг' ),
					array( 'field_arcticrc_home_hero_bullet' => 'Инженерно-геологических изыскания' ),
					array( 'field_arcticrc_home_hero_bullet' => 'Инклинометрические измерения скважин' ),
					array( 'field_arcticrc_home_hero_bullet' => 'Оценка вибрационного воздействия' ),
				),
			);
		}
		update_field( 'field_arcticrc_home_hero', $slides, $page_id );
	}

	if ( ! get_field( 'field_arcticrc_home_services', $page_id ) ) {
		$service = get_page_by_path( 'soil-testing', OBJECT, 'service' );
		$rows    = array();
		for ( $index = 0; $index < 6; $index++ ) {
			$rows[] = array(
				'field_arcticrc_home_service_post'   => $service ? $service->ID : 0,
				'field_arcticrc_home_service_image'  => $assets['service'],
				'field_arcticrc_home_service_number' => '/01',
			);
		}
		update_field( 'field_arcticrc_home_services', $rows, $page_id );
	}

	if ( ! get_field( 'field_arcticrc_home_clients', $page_id ) ) {
		$names = array( 'Заказчик', 'Алроса', 'Заказчик', 'ПИК', 'Заказчик', 'Заказчик' );
		$rows  = array();
		for ( $index = 1; $index <= 6; $index++ ) {
			$rows[] = array(
				'field_arcticrc_home_client_name'  => $names[ $index - 1 ],
				'field_arcticrc_home_client_image' => $assets[ 'client_' . $index ],
			);
		}
		update_field( 'field_arcticrc_home_clients', $rows, $page_id );
	}

	if ( ! get_field( 'field_arcticrc_home_documents', $page_id ) ) {
		$rows = array();
		for ( $index = 0; $index < 4; $index++ ) {
			$rows[] = array(
				'field_arcticrc_home_document_title'  => 'Аккредитованная лаборатория',
				'field_arcticrc_home_document_type'   => 'Аккредитация',
				'field_arcticrc_home_document_weight' => '17,0 мб',
			);
		}
		update_field( 'field_arcticrc_home_documents', $rows, $page_id );
	}

	update_post_meta( $page_id, '_arcticrc_home_seed_version', ARCTICRC_HOME_SEED_VERSION );
}
add_action( 'admin_init', 'arcticrc_seed_home_content', 35 );

function arcticrc_home_geography_is_map() {
	return arcticrc_home_block_visible( 'home_geography_visible' )
		&& 'map' === arcticrc_home_value( 'home_geography_mode', 'map' );
}


function arcticrc_home_block_visible( $field_name ) {
	$page_id = arcticrc_home_page_id();

	if ( ! $page_id || ! function_exists( 'get_field' ) ) {
		return true;
	}

	$value = get_field( $field_name, $page_id );

	return false !== $value && '0' !== (string) $value;
}
