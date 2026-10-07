<?php
defined( 'ABSPATH' ) || exit;

function arcticrc_register_content_types() {
	register_taxonomy(
		'service_direction',
		array( 'service' ),
		array(
			'labels' => array(
				'name' => 'Направления услуг',
				'singular_name' => 'Направление услуг',
				'add_new_item' => 'Добавить направление',
				'edit_item' => 'Редактировать направление',
			),
			'public' => true,
			'hierarchical' => true,
			'show_admin_column' => true,
			'show_in_rest' => false,
			'rewrite' => array(
				'slug' => 'services',
				'with_front' => false,
				'hierarchical' => true,
			),
		)
	);

	register_post_type(
		'service',
		array(
			'labels' => array(
				'name' => 'Услуги',
				'singular_name' => 'Услуга',
				'add_new_item' => 'Добавить услугу',
				'edit_item' => 'Редактировать услугу',
			),
			'public' => true,
			'show_in_rest' => false,
			'menu_icon' => 'dashicons-hammer',
			'has_archive' => 'services',
			'rewrite' => array( 'slug' => 'service-item', 'with_front' => false ),
			'supports' => array( 'title', 'excerpt', 'thumbnail', 'page-attributes' ),
			'taxonomies' => array( 'service_direction' ),
		)
	);

	register_post_type(
		'project',
		array(
			'labels' => array(
				'name' => 'Проекты',
				'singular_name' => 'Проект',
				'add_new_item' => 'Добавить проект',
				'edit_item' => 'Редактировать проект',
			),
			'public' => true,
			'show_in_rest' => false,
			'menu_icon' => 'dashicons-portfolio',
			'has_archive' => 'projects',
			'rewrite' => array( 'slug' => 'projects', 'with_front' => false ),
			'supports' => array( 'title', 'excerpt', 'thumbnail', 'page-attributes' ),
		)
	);

	register_taxonomy(
		'equipment_mode',
		array( 'equipment' ),
		array(
			'labels' => array(
				'name' => 'Режим оборудования',
				'singular_name' => 'Режим оборудования',
			),
			'public' => true,
			'hierarchical' => true,
			'show_admin_column' => true,
			'show_in_rest' => false,
			'rewrite' => false,
		)
	);

	register_post_type(
		'equipment',
		array(
			'labels' => array(
				'name' => 'Оборудование',
				'singular_name' => 'Оборудование',
				'add_new_item' => 'Добавить оборудование',
				'edit_item' => 'Редактировать оборудование',
			),
			'public' => true,
			'show_in_rest' => false,
			'menu_icon' => 'dashicons-admin-tools',
			'has_archive' => 'equipment',
			'rewrite' => array( 'slug' => 'equipment', 'with_front' => false ),
			'supports' => array( 'title', 'excerpt', 'thumbnail', 'page-attributes' ),
			'taxonomies' => array( 'equipment_mode' ),
		)
	);
}
add_action( 'init', 'arcticrc_register_content_types' );

function arcticrc_service_permalink( $permalink, $post ) {
	if ( 'service' !== $post->post_type ) {
		return $permalink;
	}

	$terms = wp_get_post_terms( $post->ID, 'service_direction', array( 'orderby' => 'term_id', 'order' => 'ASC' ) );

	if ( is_wp_error( $terms ) || ! $terms ) {
		return home_url( user_trailingslashit( 'services/' . $post->post_name ) );
	}

	$term = reset( $terms );

	return home_url( user_trailingslashit( 'services/' . $term->slug . '/' . $post->post_name ) );
}
add_filter( 'post_type_link', 'arcticrc_service_permalink', 10, 2 );

function arcticrc_service_rewrite_rules() {
	add_rewrite_rule(
		'^services/([^/]+)/([^/]+)/?
add_action( 'init', 'arcticrc_service_rewrite_rules', 20 );

function arcticrc_flush_rewrite_rules() {
	arcticrc_register_content_types();
	arcticrc_service_rewrite_rules();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'arcticrc_flush_rewrite_rules' );
,
		'index.php?service=$matches[2]&service_direction=$matches[1]',
		'top'
	);

	add_rewrite_rule(
		'^services/([^/]+)/?
add_action( 'init', 'arcticrc_service_rewrite_rules', 20 );

function arcticrc_flush_rewrite_rules() {
	arcticrc_register_content_types();
	arcticrc_service_rewrite_rules();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'arcticrc_flush_rewrite_rules' );
,
		'index.php?service=$matches[1]',
		'top'
	);
}
add_action( 'init', 'arcticrc_service_rewrite_rules', 20 );

function arcticrc_flush_rewrite_rules() {
	arcticrc_register_content_types();
	arcticrc_service_rewrite_rules();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'arcticrc_flush_rewrite_rules' );
