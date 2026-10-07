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

const ARCTICRC_CONTENT_SEED_VERSION = '2026-10-07-8';


/**
 * Theme assets that must become real Media Library attachments.
 *
 * Add new approved artwork here. The seed process copies each file from
 * /assets to /uploads, creates an attachment, and returns its attachment ID.
 */
function arcticrc_seed_asset_registry() {
	return array(
		'logo_light' => array(
			'path'  => 'media/web/logo-99-4083.svg',
			'title' => 'Логотип ArcticRC — светлый',
		),
		'logo_blue' => array(
			'path'  => 'media/web/logo-99-1171.svg',
			'title' => 'Логотип ArcticRC — синий',
		),
		'telegram' => array(
			'path'  => 'media/web/social-telegram.svg',
			'title' => 'Telegram',
		),
		'whatsapp' => array(
			'path'  => 'media/web/social-whatsapp.svg',
			'title' => 'WhatsApp',
		),
		'footer_logo' => array(
			'path'  => 'media/web/logo-100-5500.svg',
			'title' => 'Логотип ArcticRC — подвал',
		),
	);
}

function arcticrc_seed_registered_assets() {
	$result = array();

	foreach ( arcticrc_seed_asset_registry() as $key => $asset ) {
		$result[ $key ] = arcticrc_seed_media_asset( $asset['path'], $asset['title'] );
	}

	return $result;
}


/**
 * Register an existing theme asset in the Media Library once.
 */
function arcticrc_seed_media_asset( $relative_path, $title = '' ) {
	$relative_path = ltrim( $relative_path, '/' );
	$source        = get_template_directory() . '/assets/' . $relative_path;

	if ( ! is_readable( $source ) ) {
		return 0;
	}

	$existing = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => '_arcticrc_seed_asset',
			'meta_value'     => $relative_path,
		)
	);

	if ( $existing ) {
		return (int) $existing[0];
	}

	$filename = basename( $source );
	$bytes    = file_get_contents( $source );

	if ( false === $bytes ) {
		return 0;
	}

	$upload = wp_upload_bits( $filename, null, $bytes );

	if ( ! empty( $upload['error'] ) ) {
		return 0;
	}

	$filetype = wp_check_filetype( $filename );
	$mime     = $filetype['type'];

	if ( ! $mime && 'svg' === strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) ) ) {
		$mime = 'image/svg+xml';
	}

	$attachment_id = wp_insert_attachment(
		array(
			'post_mime_type' => $mime ?: 'application/octet-stream',
			'post_title'     => $title ?: pathinfo( $filename, PATHINFO_FILENAME ),
			'post_status'    => 'inherit',
			'guid'           => $upload['url'],
		),
		$upload['file']
	);

	if ( is_wp_error( $attachment_id ) ) {
		return 0;
	}

	update_post_meta( $attachment_id, '_arcticrc_seed_asset', $relative_path );

	if ( 'image/svg+xml' !== $mime ) {
		require_once ABSPATH . 'wp-admin/includes/image.php';
		$metadata = wp_generate_attachment_metadata( $attachment_id, $upload['file'] );
		if ( $metadata ) {
			wp_update_attachment_metadata( $attachment_id, $metadata );
		}
	}

	return (int) $attachment_id;
}

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

	if ( ! $current ) {
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
		'field_arcticrc_yandex_maps_api_key'=> '6b4eac7a-0149-488d-b8c5-391ae43a22e4',
	);

	foreach ( $values as $field_key => $value ) {
		$current = get_field( $field_key, 'option' );

		if ( null === $current || '' === $current || false === $current ) {
			update_field( $field_key, $value, 'option' );
		}
	}

	$policy_page = get_page_by_path( 'policy', OBJECT, 'page' );
	if ( $policy_page && ! get_field( 'field_arcticrc_privacy_url', 'option' ) ) {
		update_field( 'field_arcticrc_privacy_url', $policy_page->ID, 'option' );
	}

	$privacy_page = get_page_by_path( 'privacy', OBJECT, 'page' );
	if ( $privacy_page && ! get_field( 'field_arcticrc_personal_data_url', 'option' ) ) {
		update_field( 'field_arcticrc_personal_data_url', $privacy_page->ID, 'option' );
	}

}

/**
 * Ensure theme-provided media really exists in the Media Library and ACF.
 *
 * This runs independently from the content seed version. If an upload failed
 * once, WordPress retries on the next admin request until the attachment and
 * field values actually exist.
 */
function arcticrc_sync_seed_media() {
	if ( ! is_admin() || ! current_user_can( 'upload_files' ) || ! function_exists( 'update_field' ) ) {
		return;
	}

	$assets        = arcticrc_seed_registered_assets();
	$logo_light_id = isset( $assets['logo_light'] ) ? (int) $assets['logo_light'] : 0;
	$logo_blue_id  = isset( $assets['logo_blue'] ) ? (int) $assets['logo_blue'] : 0;
	$telegram_icon = isset( $assets['telegram'] ) ? (int) $assets['telegram'] : 0;
	$whatsapp_icon = isset( $assets['whatsapp'] ) ? (int) $assets['whatsapp'] : 0;

	if ( $logo_light_id && ! get_field( 'site_logo_light', 'option' ) ) {
		update_field( 'field_arcticrc_logo_light', $logo_light_id, 'option' );
	}

	if ( $logo_blue_id && ! get_field( 'site_logo_blue', 'option' ) ) {
		update_field( 'field_arcticrc_logo_blue', $logo_blue_id, 'option' );
	}

	$current_socials = get_field( 'site_socials', 'option' );
	$rows            = array();

	if ( is_array( $current_socials ) && $current_socials ) {
		foreach ( $current_socials as $social ) {
			$name = isset( $social['name'] ) ? trim( (string) $social['name'] ) : '';
			$url  = isset( $social['url'] ) ? trim( (string) $social['url'] ) : '';
			$icon = isset( $social['icon'] ) ? $social['icon'] : 0;

			if ( is_array( $icon ) && ! empty( $icon['ID'] ) ) {
				$icon = (int) $icon['ID'];
			} elseif ( is_array( $icon ) && ! empty( $icon['id'] ) ) {
				$icon = (int) $icon['id'];
			} else {
				$icon = (int) $icon;
			}

			$lower_name = strtolower( $name );

			if ( ! $icon && false !== strpos( $lower_name, 'telegram' ) ) {
				$icon = $telegram_icon;
			}

			if ( ! $icon && false !== strpos( $lower_name, 'whatsapp' ) ) {
				$icon = $whatsapp_icon;
			}

			$rows[] = array(
				'field_arcticrc_social_icon' => $icon,
				'field_arcticrc_social_name' => $name,
				'field_arcticrc_social_url'  => $url,
			);
		}
	}

	if ( ! $rows ) {
		$rows = array(
			array(
				'field_arcticrc_social_icon' => $telegram_icon,
				'field_arcticrc_social_name' => 'Telegram',
				'field_arcticrc_social_url'  => 'https://telegram.org/',
			),
			array(
				'field_arcticrc_social_icon' => $whatsapp_icon,
				'field_arcticrc_social_name' => 'WhatsApp',
				'field_arcticrc_social_url'  => 'https://www.whatsapp.com/',
			),
		);
	}

	update_field( 'field_arcticrc_site_socials', $rows, 'option' );
}
add_action( 'admin_init', 'arcticrc_sync_seed_media', 20 );

/**
 * Provision the approved initial site content.
 */
function arcticrc_seed_base_content() {
	if ( get_option( 'arcticrc_content_seed_version' ) === ARCTICRC_CONTENT_SEED_VERSION ) {
		return;
	}

	$home_id     = arcticrc_seed_page( 'home', 'Главная', 'index.html', 0 );
	$services_id = arcticrc_seed_page( 'services', 'Услуги', 'services.html', 10 );
	arcticrc_seed_page( 'contacts', 'Контакты', 'contacts.html', 40 );
	arcticrc_seed_page( 'equipment-rent', 'Аренда оборудования и спецтехники', 'equipment-rent.html', 20 );
	arcticrc_seed_page( 'equipment-sale', 'Продажа оборудования и спецтехники', 'equipment-sale.html', 30 );

	if ( $services_id ) {
		update_post_meta( $services_id, '_wp_page_template', 'page-services.php' );
	}

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

	$markup = strtr( $markup, $replacements );

	if ( function_exists( 'arcticrc_breadcrumbs' ) ) {
		$breadcrumbs = arcticrc_breadcrumbs( false );

		$markup = preg_replace(
			'/<nav\\b[^>]*class="[^"]*\\bbreadcrumbs\\b[^"]*"[^>]*>.*?<\\/nav>/si',
			$breadcrumbs,
			$markup
		);
	}

	if ( function_exists( 'arcticrc_cf7_replace_source_forms' ) ) {
		$markup = arcticrc_cf7_replace_source_forms( $markup );
	}

	return $markup;
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
