<?php
defined( 'ABSPATH' ) || exit;

function arcticrc_asset_version( $relative_path ) {
	$file = get_template_directory() . '/' . ltrim( $relative_path, '/' );

	return file_exists( $file )
		? (string) filemtime( $file )
		: (string) wp_get_theme()->get( 'Version' );
}

function arcticrc_enqueue_assets() {
	$uri = get_template_directory_uri();

	$styles = array(
		'fonts'             => 'assets/css/fonts.css',
		'tokens'            => 'assets/css/tokens.css',
		'base'              => 'assets/css/base.css',
		'route-page'        => 'assets/css/routes/page.css',
		'action'            => 'assets/css/components/action.css',
		'breadcrumbs'       => 'assets/css/components/breadcrumbs.css',
		'client-logo'       => 'assets/css/components/client-logo.css',
		'collection'        => 'assets/css/components/collection.css',
		'company-proof'     => 'assets/css/components/company-proof.css',
		'contact-details'   => 'assets/css/components/contact-details.css',
		'document-card'     => 'assets/css/components/document-card.css',
		'documents-section' => 'assets/css/components/documents-section.css',
		'enquiry-card'      => 'assets/css/components/enquiry-card.css',
		'enquiry-dialog'    => 'assets/css/components/enquiry-dialog.css',
		'enquiry-form'      => 'assets/css/components/enquiry-form.css',
		'enquiry-section'   => 'assets/css/components/enquiry-section.css',
		'equipment-card'    => 'assets/css/components/equipment-card.css',
		'media-hero'        => 'assets/css/components/media-hero.css',
		'page-heading'      => 'assets/css/components/page-heading.css',
		'price-list'        => 'assets/css/components/price-list.css',
		'project-card'      => 'assets/css/components/project-card.css',
		'record-rail'       => 'assets/css/components/record-rail.css',
		'scroll-return'     => 'assets/css/components/scroll-return.css',
		'service-card'      => 'assets/css/components/service-card.css',
		'site-footer'       => 'assets/css/components/site-footer.css',
		'site-header'       => 'assets/css/components/site-header.css',
		'site-nav'          => 'assets/css/components/site-nav.css',
		'source-map'        => 'assets/css/components/source-map.css',
		'test-card'         => 'assets/css/components/test-card.css',
	);

	$dependencies = array();

	foreach ( $styles as $handle => $path ) {
		$full_handle = 'arcticrc-' . $handle;

		wp_enqueue_style(
			$full_handle,
			$uri . '/' . $path,
			$dependencies,
			arcticrc_asset_version( $path )
		);

		$dependencies = array( $full_handle );
	}

	wp_enqueue_style(
		'arcticrc-theme',
		get_stylesheet_uri(),
		$dependencies,
		(string) wp_get_theme()->get( 'Version' )
	);

	$scripts = array(
		'enquiry-dialog' => 'assets/js/components/enquiry-dialog.js',
		'enquiry-form'   => 'assets/js/components/enquiry-form.js',
		'record-rail'    => 'assets/js/components/record-rail.js',
		'scroll-return'  => 'assets/js/components/scroll-return.js',
		'site-nav'       => 'assets/js/components/site-nav.js',
	);

	foreach ( $scripts as $handle => $path ) {
		$full_handle = 'arcticrc-' . $handle;

		wp_enqueue_script(
			$full_handle,
			$uri . '/' . $path,
			array(),
			arcticrc_asset_version( $path ),
			true
		);

		wp_script_add_data( $full_handle, 'strategy', 'defer' );
	}

	$needs_yandex_map = (
		is_front_page()
		&& function_exists( 'arcticrc_home_geography_is_map' )
		&& arcticrc_home_geography_is_map()
	) || (
		is_page_template( 'page-contacts.php' )
		&& function_exists( 'arcticrc_contacts_map_is_interactive' )
		&& arcticrc_contacts_map_is_interactive()
	);

	if ( $needs_yandex_map ) {
		$api_key = arcticrc_option( 'yandex_maps_api_key', '' );

		if ( $api_key ) {
			wp_enqueue_script(
				'arcticrc-yandex-maps-api',
				'https://api-maps.yandex.ru/2.1/?apikey=' . rawurlencode( $api_key ) . '&lang=ru_RU',
				array(),
				null,
				true
			);

			wp_enqueue_script(
				'arcticrc-yandex-map',
				$uri . '/assets/js/components/yandex-map.js',
				array( 'arcticrc-yandex-maps-api' ),
				arcticrc_asset_version( 'assets/js/components/yandex-map.js' ),
				true
			);

			wp_script_add_data( 'arcticrc-yandex-map', 'strategy', 'defer' );
		}
	}
}
add_action( 'wp_enqueue_scripts', 'arcticrc_enqueue_assets' );
