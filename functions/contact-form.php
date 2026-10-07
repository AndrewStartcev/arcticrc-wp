<?php
/**
 * Contact Form 7 integration.
 *
 * The theme owns the visual markup; CF7 is used as the mail/submission engine.
 *
 * @package ArcticRC
 */

defined( 'ABSPATH' ) || exit;

const ARCTICRC_CF7_SEED_VERSION = '2026-10-07-4';

function arcticrc_cf7_is_available() {
	return defined( 'WPCF7_VERSION' ) || post_type_exists( 'wpcf7_contact_form' );
}

function arcticrc_cf7_form_template( $variant = 'consultation' ) {
	$is_equipment = 'equipment' === $variant;
	$title        = $is_equipment
		? 'Оставьте заявку, подберём оборудование под ваш запрос'
		: 'Бесплатная консультация';
	$intro        = $is_equipment
		? ''
		: '<p class="enquiry-form__intro">Оставьте контакты, по которым мы можем связаться с вами</p>';

	$privacy_url       = arcticrc_option( 'site_privacy_url', home_url( '/policy/' ) );
	$personal_data_url = arcticrc_option( 'site_personal_data_url', home_url( '/privacy/' ) );

	return sprintf(
		'<h2 class="enquiry-form__title">%1$s</h2>
%2$s
<div class="enquiry-form__fields">
	<div class="enquiry-form__field">
		<label class="enquiry-form__label">Имя</label>
		[text* name class:enquiry-form__control autocomplete:name placeholder "Имя"]
	</div>
	<div class="enquiry-form__field">
		<label class="enquiry-form__label">Телефон</label>
		[tel* phone class:enquiry-form__control autocomplete:tel placeholder "+7 999 999 99 99"]
	</div>
</div>
[hidden purpose]
[hidden record_id]
<div class="enquiry-form__consent">
	[acceptance consent class:enquiry-form__checkbox]
	Я согласен с <a class="enquiry-form__link" href="%3$s">политикой конфиденциальности</a> и <a class="enquiry-form__link" href="%4$s">условиями соглашения на обработку персональных данных</a>
	[/acceptance]
</div>
<button class="action action--primary enquiry-form__submit" type="submit" disabled>
	<span class="action__marker" aria-hidden="true"></span>
	<span class="action__label">Оставить заявку</span>
</button>',
		esc_html( $title ),
		$intro,
		esc_url( $privacy_url ),
		esc_url( $personal_data_url )
	);
}

function arcticrc_cf7_mail_properties( $variant = 'consultation' ) {
	$recipient = arcticrc_option( 'site_email', get_option( 'admin_email' ) );
	$subject   = 'equipment' === $variant
		? '[_site_title] — заявка на оборудование'
		: '[_site_title] — новая заявка';

	return array(
		'active'             => true,
		'subject'            => $subject,
		'sender'             => '[_site_title] <wordpress@[_site_domain]>',
		'recipient'          => $recipient,
		'body'               => "Имя: [name]
Телефон: [phone]
Тип заявки: [purpose]
ID записи: [record_id]

Страница: [_url]
Дата: [_date] [_time]",
		'additional_headers' => '',
		'attachments'        => '',
		'use_html'           => false,
		'exclude_blank'      => false,
	);
}

function arcticrc_cf7_find_managed_form( $variant ) {
	$posts = get_posts(
		array(
			'post_type'      => 'wpcf7_contact_form',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => '_arcticrc_cf7_variant',
			'meta_value'     => $variant,
		)
	);

	return $posts ? (int) $posts[0] : 0;
}

function arcticrc_cf7_seed_form( $variant, $title ) {
	if ( ! arcticrc_cf7_is_available() ) {
		return 0;
	}

	$id = arcticrc_cf7_find_managed_form( $variant );

	if ( ! $id ) {
		$id = wp_insert_post(
			array(
				'post_type'   => 'wpcf7_contact_form',
				'post_status' => 'publish',
				'post_title'  => $title,
			)
		);
	} else {
		wp_update_post(
			array(
				'ID'         => $id,
				'post_title' => $title,
			)
		);
	}

	if ( ! $id || is_wp_error( $id ) ) {
		return 0;
	}

	update_post_meta( $id, '_form', arcticrc_cf7_form_template( $variant ) );
	update_post_meta( $id, '_mail', arcticrc_cf7_mail_properties( $variant ) );
	update_post_meta( $id, '_mail_2', array( 'active' => false ) );
	update_post_meta( $id, '_messages', array() );
	update_post_meta( $id, '_additional_settings', '' );
	update_post_meta( $id, '_locale', get_locale() );
	update_post_meta( $id, '_arcticrc_cf7_variant', $variant );
	update_post_meta( $id, '_arcticrc_cf7_seed_version', ARCTICRC_CF7_SEED_VERSION );

	return (int) $id;
}

function arcticrc_cf7_seed_forms() {
	if ( ! is_admin() || ! current_user_can( 'manage_options' ) || ! arcticrc_cf7_is_available() ) {
		return;
	}

	$forms = array(
		'consultation' => 'ArcticRC — Бесплатная консультация',
		'equipment'    => 'ArcticRC — Оборудование',
	);

	foreach ( $forms as $variant => $title ) {
		$id = arcticrc_cf7_find_managed_form( $variant );

		if ( $id && ARCTICRC_CF7_SEED_VERSION === get_post_meta( $id, '_arcticrc_cf7_seed_version', true ) ) {
			continue;
		}

		arcticrc_cf7_seed_form( $variant, $title );
	}
}
add_action( 'admin_init', 'arcticrc_cf7_seed_forms', 40 );

function arcticrc_cf7_form_html( $variant = 'consultation', $purpose = 'consultation', $classes = '' ) {
	if ( ! arcticrc_cf7_is_available() ) {
		return current_user_can( 'manage_options' )
			? '<p class="enquiry-form__notice">Для работы формы активируйте Contact Form 7.</p>'
			: '';
	}

	$id = arcticrc_cf7_find_managed_form( $variant );

	if ( ! $id ) {
		return '';
	}

	$classes = trim( $classes ) ?: 'enquiry-form enquiry-form--consultation';
	$html    = do_shortcode(
		sprintf(
			'[contact-form-7 id="%d" html_class="%s"]',
			$id,
			esc_attr( $classes )
		)
	);

	return preg_replace(
		'/<form\b/',
		sprintf(
			'<form data-enquiry-form data-purpose="%s"',
			esc_attr( $purpose )
		),
		$html,
		1
	);
}

function arcticrc_cf7_replace_source_forms( $markup ) {
	if ( ! arcticrc_cf7_is_available() ) {
		return $markup;
	}

	return preg_replace_callback(
		'/<form\b[^>]*class="([^"]*\benquiry-form\b[^"]*)"[^>]*>(.*?)<\/form>/si',
		function ( $match ) {
			$classes = trim( $match[1] );
			$body    = $match[2];
			$variant = false !== strpos( $classes, 'enquiry-form--equipment' ) ? 'equipment' : 'consultation';
			$purpose = 'consultation';

			if ( preg_match( '/name="purpose"\s+value="([^"]*)"/i', $body, $purpose_match ) ) {
				$purpose = sanitize_key( $purpose_match[1] );
			}

			$form = arcticrc_cf7_form_html( $variant, $purpose, $classes );

			return $form ?: $match[0];
		},
		$markup
	);
}

function arcticrc_cf7_admin_notice() {
	if ( arcticrc_cf7_is_available() || ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	echo '<div class="notice notice-warning"><p><strong>ArcticRC:</strong> для работы форм необходимо установить и активировать Contact Form 7.</p></div>';
}
add_action( 'admin_notices', 'arcticrc_cf7_admin_notice' );
