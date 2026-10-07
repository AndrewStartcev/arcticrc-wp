<?php
/**
 * Automatic base content provisioning from the approved static markup.
 *
 * No import button is required. The theme creates/updates its managed
 * pages and the initial service automatically when the seed version changes.
 *
 * @package ArcticRC
 */

defined( 'ABSPATH' ) || exit;

const ARCTICRC_CONTENT_SEED_VERSION = '2026-10-07-2';

/**
 * Read the <main> block from a static source file and adapt paths to WordPress.
 */
function arcticrc_source_main_markup( $filename ) {
	$path = get_template_directory() . '/html/' . basename( $filename );

	if ( ! is_readable( $path ) ) {
		return '';
	}

	$html = file_get_contents( $path );

	if ( false === $html || ! preg_match( '/<main\b[^>]*>.*?<\/main>/si', $html, $match ) ) {
		return '';
	}

	$markup = $match[0];

	$theme_assets = wp_make_link_relative( trailingslashit( get_template_directory_uri() ) . 'assets/' );

	$replacements = array(
		'assets/'                          => $theme_assets,
		'/index.html#'                    => '/#',
		'/index.html'                     => '/',
		'/services.html'                  => '/services/',
		'/services-soil-testing.html'     => '/services/soil-testing/',
		'/equipment-rent.html'            => '/equipment-rent/',
		'/equipment-sale.html'            => '/equipment-sale/',
		'/contacts.html'                  => '/contacts/',
		'https://arcticrc.ru/privacy/'    => '/privacy/',
		'https://arcticrc.ru/policy/'     => '/policy/',
	);

	return strtr( $markup, $replacements );
}

/**
 * Insert or update a managed page.
 */
function arcticrc_seed_page( $slug, $title, $source_file, $menu_order = 0 ) {
	$existing = get_page_by_path( $slug, OBJECT, 'page' );
	$content  = arcticrc_source_main_markup( $source_file );

	$postarr = array(
		'post_type'    => 'page',
		'post_status'  => 'publish',
		'post_title'   => $title,
		'post_name'    => $slug,
		'post_content' => $content,
		'menu_order'   => $menu_order,
	);

	if ( $existing ) {
		$postarr['ID'] = $existing->ID;
		$post_id       = wp_update_post( wp_slash( $postarr ), true );
	} else {
		$post_id = wp_insert_post( wp_slash( $postarr ), true );
	}

	if ( is_wp_error( $post_id ) ) {
		return 0;
	}

	update_post_meta( $post_id, '_arcticrc_managed_seed', ARCTICRC_CONTENT_SEED_VERSION );
	update_post_meta( $post_id, '_wp_page_template', 'page-source.php' );

	return (int) $post_id;
}

/**
 * Insert or update a managed service.
 */
function arcticrc_seed_service( $slug, $title, $source_file, $excerpt = '' ) {
	$existing = get_page_by_path( $slug, OBJECT, 'service' );
	$content  = arcticrc_source_main_markup( $source_file );

	$postarr = array(
		'post_type'    => 'service',
		'post_status'  => 'publish',
		'post_title'   => $title,
		'post_name'    => $slug,
		'post_excerpt' => $excerpt,
		'post_content' => $content,
	);

	if ( $existing ) {
		$postarr['ID'] = $existing->ID;
		$post_id       = wp_update_post( wp_slash( $postarr ), true );
	} else {
		$post_id = wp_insert_post( wp_slash( $postarr ), true );
	}

	if ( is_wp_error( $post_id ) ) {
		return 0;
	}

	update_post_meta( $post_id, '_arcticrc_managed_seed', ARCTICRC_CONTENT_SEED_VERSION );

	return (int) $post_id;
}

/**
 * Create/update theme navigation menus from the approved markup.
 */
function arcticrc_seed_menu( $name, $location, $items ) {
	$menu = wp_get_nav_menu_object( $name );

	if ( ! $menu ) {
		$menu_id = wp_create_nav_menu( $name );
	} else {
		$menu_id = (int) $menu->term_id;
	}

	if ( is_wp_error( $menu_id ) || ! $menu_id ) {
		return;
	}

	$current = wp_get_nav_menu_items( $menu_id );
	if ( $current ) {
		foreach ( $current as $item ) {
			wp_delete_post( $item->ID, true );
		}
	}

	foreach ( $items as $item ) {
		wp_update_nav_menu_item(
			$menu_id,
			0,
			array(
				'menu-item-title'  => $item['title'],
				'menu-item-url'    => home_url( $item['url'] ),
				'menu-item-status' => 'publish',
				'menu-item-type'   => 'custom',
			)
		);
	}

	$locations              = get_theme_mod( 'nav_menu_locations', array() );
	$locations[ $location ] = $menu_id;
	set_theme_mod( 'nav_menu_locations', $locations );
}

/**
 * Fill global ACF options from the approved markup.
 */
function arcticrc_seed_global_options() {
	if ( ! function_exists( 'update_field' ) ) {
		return;
	}

	$values = array(
		'field_arcticrc_site_phone'         => '+7(916)-616-02-20',
		'field_arcticrc_site_email'         => 'engineering@arcticrc.ru',
		'field_arcticrc_site_address'       => 'Москва, 2-й Кожевнический пер, д.1, помещ. 1-H',
		'field_arcticrc_site_company_name'  => 'ООО "ГК ЦЕНТР АРКТИЧЕСКИХ ИЗЫСКАНИЙ"',
		'field_arcticrc_site_inn'           => '9721265458',
		'field_arcticrc_site_kpp'           => '772101001',
		'field_arcticrc_site_legal_address' => 'г. Москва, ул Михайлова, д 31А, кв 404',
		'field_arcticrc_copyright'          => 'Все права защищены',
		'field_arcticrc_privacy_url'        => '/policy/',
		'field_arcticrc_personal_data_url'  => '/privacy/',
	);

	foreach ( $values as $field_key => $value ) {
		$current = get_field( $field_key, 'option' );

		if ( null === $current || '' === $current || false === $current ) {
			update_field( $field_key, $value, 'option' );
		}
	}

	$socials = get_field( 'field_arcticrc_site_socials', 'option' );

	if ( empty( $socials ) || ! is_array( $socials ) ) {
		update_field(
			'field_arcticrc_site_socials',
			array(
				array(
					'field_arcticrc_social_icon'      => '',
					'field_arcticrc_social_name'      => 'Telegram',
					'field_arcticrc_social_url'       => 'https://telegram.org/',
					'field_arcticrc_social_link_text' => 'Telegram',
				),
				array(
					'field_arcticrc_social_icon'      => '',
					'field_arcticrc_social_name'      => 'WhatsApp',
					'field_arcticrc_social_url'       => 'https://www.whatsapp.com/',
					'field_arcticrc_social_link_text' => 'WhatsApp',
				),
			),
			'option'
		);
	}
}

/**
 * Provision the approved initial site content.
 */
function arcticrc_seed_base_content() {
	if ( get_option( 'arcticrc_content_seed_version' ) === ARCTICRC_CONTENT_SEED_VERSION ) {
		return;
	}

	$home_id = arcticrc_seed_page( 'home', 'Главная', 'index.html', 0 );
	arcticrc_seed_page( 'contacts', 'Контакты', 'contacts.html', 40 );
	arcticrc_seed_page( 'equipment-rent', 'Аренда оборудования и спецтехники', 'equipment-rent.html', 20 );
	arcticrc_seed_page( 'equipment-sale', 'Продажа оборудования и спецтехники', 'equipment-sale.html', 30 );

	arcticrc_seed_service(
		'soil-testing',
		'Полевые испытания грунтов сваями',
		'services-soil-testing.html',
		'Полевые испытания грунтов сваями от 30 000 рублей.'
	);

	update_option( 'arcticrc_services_archive_markup', arcticrc_source_main_markup( 'services.html' ), false );

	if ( $home_id ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $home_id );
	}

	arcticrc_seed_global_options();

	arcticrc_seed_menu(
		'Основное меню',
		'primary',
		array(
			array( 'title' => 'Услуги', 'url' => '/services/' ),
			array( 'title' => 'Оборудование', 'url' => '/equipment-rent/' ),
			array( 'title' => 'Проекты', 'url' => '/services/soil-testing/#projects' ),
			array( 'title' => 'Документация', 'url' => '/#documents' ),
		)
	);

	arcticrc_seed_menu(
		'Навигация в подвале',
		'footer_navigation',
		array(
			array( 'title' => 'Главная', 'url' => '/' ),
			array( 'title' => 'Услуги', 'url' => '/services/' ),
			array( 'title' => 'География', 'url' => '/#geography' ),
			array( 'title' => 'Заказчики', 'url' => '/#clients' ),
			array( 'title' => 'Документация', 'url' => '/#documents' ),
			array( 'title' => 'Контакты', 'url' => '/contacts/' ),
		)
	);

	arcticrc_seed_menu(
		'Направления',
		'footer_directions',
		array(
			array( 'title' => 'Полевые испытания грунтов сваями', 'url' => '/services/soil-testing/' ),
			array( 'title' => 'Аренда оборудования и спецтехники', 'url' => '/equipment-rent/' ),
			array( 'title' => 'Инженерные изыскания', 'url' => '/services/' ),
			array( 'title' => 'Геодезические изыскания', 'url' => '/services/' ),
			array( 'title' => 'Лабораторные исследования', 'url' => '/services/' ),
		)
	);

	update_option( 'arcticrc_content_seed_version', ARCTICRC_CONTENT_SEED_VERSION, false );

	flush_rewrite_rules( false );
}
add_action( 'admin_init', 'arcticrc_seed_base_content', 30 );

/**
 * Replace repeated values from the source markup with current global settings.
 */
function arcticrc_apply_global_markup( $markup ) {
	$phone             = arcticrc_option( 'site_phone', '+7(916)-616-02-20' );
	$email             = arcticrc_option( 'site_email', 'engineering@arcticrc.ru' );
	$office_address    = arcticrc_option( 'site_address', 'Москва, 2-й Кожевнический пер, д.1, помещ. 1-H' );
	$privacy_url       = arcticrc_option( 'site_privacy_url', '/policy/' );
	$personal_data_url = arcticrc_option( 'site_personal_data_url', '/privacy/' );
	$socials           = arcticrc_socials();

	$replacements = array(
		'+7(916)-616-02-20'                              => $phone,
		'tel:+79166160220'                               => 'tel:' . arcticrc_phone_href( $phone ),
		'engineering@arcticrc.ru'                        => $email,
		'mailto:engineering@arcticrc.ru'                 => 'mailto:' . sanitize_email( $email ),
		'Москва, 2-й Кожевнический пер, д.1, помещ. 1-H' => $office_address,
		'/policy/'                                       => $privacy_url,
		'/privacy/'                                      => $personal_data_url,
	);

	foreach ( $socials as $social ) {
		$name = strtolower( $social['name'] );

		if ( false !== strpos( $name, 'telegram' ) ) {
			$replacements['https://telegram.org/'] = $social['url'];
		}

		if ( false !== strpos( $name, 'whatsapp' ) ) {
			$replacements['https://www.whatsapp.com/'] = $social['url'];
		}
	}

	return strtr( $markup, $replacements );
}

/**
 * Render trusted source markup saved by the theme without wpautop changing it.
 */
function arcticrc_render_seeded_post_content( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();

	if ( ! $post_id ) {
		return;
	}

	$markup = get_post_field( 'post_content', $post_id );
	$markup = arcticrc_apply_global_markup( $markup );

	echo do_shortcode( $markup ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
