<?php
defined( 'ABSPATH' ) || exit;

function arcticrc_setup() {
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo' );
	add_theme_support(
		'html5',
		array( 'search-form', 'gallery', 'caption', 'style', 'script' )
	);

	register_nav_menus(
		array(
			'primary'           => 'Основное меню',
			'footer_navigation' => 'Подвал — навигация',
			'footer_directions' => 'Подвал — направления',
		)
	);
}
add_action( 'after_setup_theme', 'arcticrc_setup' );

function arcticrc_admin_footer_text() {
	return sprintf(
		'Разработка сайта <a href="%s" target="_blank" rel="noopener noreferrer">%s</a>',
		esc_url( 'https://starcev.agency/' ),
		esc_html( 'Андрей Старцев' )
	);
}
add_filter( 'admin_footer_text', 'arcticrc_admin_footer_text' );
