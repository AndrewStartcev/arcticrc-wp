<?php
/**
 * One-time migration of legal pages from the previous ArcticRC site.
 *
 * @package ArcticRC
 */

defined( 'ABSPATH' ) || exit;

const ARCTICRC_LEGAL_SEED_VERSION = '2026-10-07-1';

/**
 * Extract the legal document beginning with its H1 and stop before repeated
 * contact/footer content from the previous site.
 */
function arcticrc_extract_legal_markup( $html, $expected_title ) {
	if ( ! class_exists( 'DOMDocument' ) || ! $html ) {
		return '';
	}

	$previous = libxml_use_internal_errors( true );
	$dom      = new DOMDocument();

	$loaded = $dom->loadHTML(
		'<?xml encoding="utf-8" ?>' . $html,
		LIBXML_NOWARNING | LIBXML_NOERROR
	);

	libxml_clear_errors();
	libxml_use_internal_errors( $previous );

	if ( ! $loaded ) {
		return '';
	}

	$xpath = new DOMXPath( $dom );
	$h1    = null;

	foreach ( $xpath->query( '//h1' ) as $heading ) {
		$text = trim( preg_replace( '/\s+/u', ' ', $heading->textContent ) );

		if ( false !== mb_stripos( $text, $expected_title ) || false !== mb_stripos( $expected_title, $text ) ) {
			$h1 = $heading;
			break;
		}
	}

	if ( ! $h1 ) {
		return '';
	}

	$markup = '';

	for ( $node = $h1; $node; $node = $node->nextSibling ) {
		if ( XML_ELEMENT_NODE !== $node->nodeType ) {
			continue;
		}

		$tag = strtolower( $node->nodeName );

		if ( in_array( $tag, array( 'footer', 'nav', 'form' ), true ) ) {
			break;
		}

		$text = trim( preg_replace( '/\s+/u', ' ', $node->textContent ) );

		if ( $node !== $h1 && preg_match( '/^(Телефон|Почта|Адрес)\s*:/ui', $text ) ) {
			break;
		}

		$markup .= $dom->saveHTML( $node );
	}

	return wp_kses_post( $markup );
}

/**
 * Fetch one legal page from the previous site.
 */
function arcticrc_fetch_legacy_legal_page( $source_url, $expected_title ) {
	$response = wp_remote_get(
		$source_url,
		array(
			'timeout'     => 20,
			'redirection' => 3,
			'user-agent'  => 'ArcticRC WordPress migration',
		)
	);

	if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
		return '';
	}

	return arcticrc_extract_legal_markup( wp_remote_retrieve_body( $response ), $expected_title );
}

/**
 * Create/update legal pages once from the old site.
 */
function arcticrc_seed_legal_pages() {
	if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$pages = array(
		'policy' => array(
			'title'  => 'Политика конфиденциальности',
			'source' => 'https://arcticrc.ru/policy/',
		),
		'privacy' => array(
			'title'  => 'Согласие на обработку персональных данных',
			'source' => 'https://arcticrc.ru/privacy/',
		),
	);

	foreach ( $pages as $slug => $data ) {
		$page = get_page_by_path( $slug, OBJECT, 'page' );

		if ( $page && ARCTICRC_LEGAL_SEED_VERSION === get_post_meta( $page->ID, '_arcticrc_legal_seed_version', true ) ) {
			continue;
		}

		$content = arcticrc_fetch_legacy_legal_page( $data['source'], $data['title'] );

		if ( ! $content && $page && trim( (string) $page->post_content ) ) {
			$content = $page->post_content;
		}

		if ( ! $content ) {
			continue;
		}

		$postarr = array(
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_title'   => $data['title'],
			'post_name'    => $slug,
			'post_content' => $content,
		);

		if ( $page ) {
			$postarr['ID'] = $page->ID;
		}

		$page_id = wp_insert_post( $postarr );

		if ( is_wp_error( $page_id ) || ! $page_id ) {
			continue;
		}

		update_post_meta( $page_id, '_wp_page_template', 'page-legal.php' );
		update_post_meta( $page_id, '_arcticrc_legal_seed_version', ARCTICRC_LEGAL_SEED_VERSION );
	}
}
add_action( 'admin_init', 'arcticrc_seed_legal_pages', 60 );
