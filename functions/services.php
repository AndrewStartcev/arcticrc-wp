<?php
/**
 * Services integration and seeded content.
 *
 * @package ArcticRC
 */

defined( 'ABSPATH' ) || exit;

const ARCTICRC_SERVICE_SEED_VERSION = '2026-10-07-1';

function arcticrc_service_block_visible( $field_name, $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();

	if ( ! $post_id || ! function_exists( 'get_field' ) ) {
		return true;
	}

	$value = get_field( $field_name, $post_id );

	return false !== $value && '0' !== (string) $value;
}

function arcticrc_service_card_bullets( $post_id ) {
	if ( ! function_exists( 'get_field' ) ) {
		return array();
	}

	$rows = get_field( 'service_card_bullets', $post_id );

	return is_array( $rows ) ? $rows : array();
}

function arcticrc_render_service_card( $post, $index = 0 ) {
	$post = get_post( $post );

	if ( ! $post || 'service' !== $post->post_type ) {
		return;
	}

	$number  = '/' . str_pad( (string) ( (int) $index + 1 ), 2, '0', STR_PAD_LEFT );
	$bullets = arcticrc_service_card_bullets( $post->ID );
	$image   = get_the_post_thumbnail_url( $post->ID, 'large' );

	if ( ! $image ) {
		$image = arcticrc_asset_url( 'media/web/18d059bc1b44b09a13800860e84b987e755b9dc4.webp' );
	}
	?>
	<article class="service-card" id="service-<?php echo esc_attr( str_pad( (string) ( (int) $index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?>">
		<div class="service-card__body">
			<div class="service-card__heading">
				<h3 class="service-card__title"><?php echo esc_html( get_the_title( $post ) ); ?></h3>
				<span class="service-card__number"><?php echo esc_html( $number ); ?></span>
			</div>
			<div class="service-card__details">
				<?php if ( $bullets ) : ?>
					<ul class="service-card__bullets">
						<?php foreach ( $bullets as $bullet ) : ?>
							<?php if ( ! empty( $bullet['text'] ) ) : ?><li><?php echo esc_html( $bullet['text'] ); ?></li><?php endif; ?>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
				<a class="action action--primary action--card-link service-card__action" href="<?php echo esc_url( get_permalink( $post ) ); ?>">
					<span class="action__marker" aria-hidden="true"></span>
					<span class="action__label">Подробнее</span>
				</a>
			</div>
		</div>
		<img class="service-card__image" src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( get_the_title( $post ) ); ?>" loading="lazy" decoding="async">
	</article>
	<?php
}

function arcticrc_seed_project( $slug, $title, $image_path ) {
	$post = get_page_by_path( $slug, OBJECT, 'project' );

	if ( ! $post ) {
		$post_id = wp_insert_post(
			array(
				'post_type'   => 'project',
				'post_status' => 'publish',
				'post_title'  => $title,
				'post_name'   => $slug,
			)
		);
	} else {
		$post_id = $post->ID;
	}

	if ( ! $post_id || is_wp_error( $post_id ) ) {
		return 0;
	}

	$image_id = arcticrc_seed_media_asset( $image_path, $title );

	if ( $image_id && ! has_post_thumbnail( $post_id ) ) {
		set_post_thumbnail( $post_id, $image_id );
	}

	return (int) $post_id;
}

function arcticrc_seed_soil_testing_service() {
	if ( ! is_admin() || ! current_user_can( 'manage_options' ) || ! function_exists( 'update_field' ) ) {
		return;
	}

	$service = get_page_by_path( 'soil-testing', OBJECT, 'service' );

	if ( ! $service ) {
		return;
	}

	$post_id = (int) $service->ID;

	$cover_id = arcticrc_seed_media_asset(
		'media/web/18d059bc1b44b09a13800860e84b987e755b9dc4.webp',
		'Полевые испытания грунтов сваями'
	);

	if ( $cover_id && ! has_post_thumbnail( $post_id ) ) {
		set_post_thumbnail( $post_id, $cover_id );
	}

	if ( ! get_field( 'service_card_bullets', $post_id ) ) {
		update_field(
			'field_arcticrc_service_card_bullets',
			array(
				array( 'field_arcticrc_service_card_bullet' => 'Геотехнический мониторинг' ),
				array( 'field_arcticrc_service_card_bullet' => 'Инженерно-геологических изыскания' ),
				array( 'field_arcticrc_service_card_bullet' => 'Инклинометрические измерения скважин' ),
				array( 'field_arcticrc_service_card_bullet' => 'Оценка вибрационного воздействия' ),
			),
			$post_id
		);
	}

	$defaults = array(
		'field_arcticrc_service_lead'             => 'Полевые испытания грунтов сваями от 30.000 рублей.',
		'field_arcticrc_service_test_cta_title'   => 'Не нашли нужное испытание?',
		'field_arcticrc_service_test_cta_text'    => 'Подберём метод испытаний под задачи вашего проекта',
		'field_arcticrc_service_prices_title'     => 'Стоимость услуг',
		'field_arcticrc_service_company_title'    => 'Не обещания, а система, которая работает',
		'field_arcticrc_service_projects_title'   => 'Проекты',
		'field_arcticrc_service_contact_title'    => 'Начнем с разговора — доведем до результата',
		'field_arcticrc_service_contact_intro'    => 'Свяжитесь с нами по телефону, оставьте заявку на консультацию или приезжайте в офис! Мы ответим на ваши вопросы',
	);

	foreach ( $defaults as $field_key => $value ) {
		$current = get_field( $field_key, $post_id );
		if ( null === $current || '' === $current ) {
			update_field( $field_key, $value, $post_id );
		}
	}

	$test_titles = array(
		'Статические испытания грунтов сваями',
		'Штамповые испытания (поверхностные)',
		'Штамповые испытания (глубинные)',
		'Динамические испытания',
		'Контроль сплошности и длины свай',
		'Испытания анкеров',
		'Испытания бетона',
	);

	if ( ! get_field( 'service_tests', $post_id ) ) {
		$tests = array();

		foreach ( $test_titles as $index => $title ) {
			$image_id = arcticrc_seed_media_asset(
				'media/web/test-' . str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ) . '-card.webp',
				$title
			);

			$tests[] = array(
				'field_arcticrc_service_test_title' => $title,
				'field_arcticrc_service_test_image' => $image_id,
			);
		}

		update_field( 'field_arcticrc_service_tests', $tests, $post_id );
	}

	$scenery_desktop = arcticrc_seed_media_asset( 'media/web/717e398ba5846ae9e7f340452f391bf09bfd29a2.webp', 'Полевые испытания — фон desktop' );
	$scenery_mobile  = arcticrc_seed_media_asset( 'media/web/soil-testing-hero-mobile.webp', 'Полевые испытания — фон mobile' );

	if ( $scenery_desktop && ! get_field( 'service_scenery_desktop', $post_id ) ) {
		update_field( 'field_arcticrc_service_scenery_desktop', $scenery_desktop, $post_id );
	}
	if ( $scenery_mobile && ! get_field( 'service_scenery_mobile', $post_id ) ) {
		update_field( 'field_arcticrc_service_scenery_mobile', $scenery_mobile, $post_id );
	}

	if ( ! get_field( 'service_price_groups', $post_id ) ) {
		$price_groups = array(
			array(
				'title' => 'Статические испытания',
				'rows'  => array(
					array( 'title' => 'Горизонтальная нагрузка', 'detail' => 'Талый грунт', 'price' => 'от 60 000 ₽' ),
					array( 'title' => 'Испытания на выдавливание', 'detail' => 'Мерзлый грунт', 'price' => 'от 132 000 ₽' ),
					array( 'title' => 'Испытания на выдавливание', 'detail' => 'Талый грунт', 'price' => 'от 60 000 ₽' ),
					array( 'title' => 'Испытания на выдергивание', 'detail' => 'Талый грунт', 'price' => 'от 60 000 ₽' ),
				),
			),
			array(
				'title' => 'Динамические испытания',
				'rows'  => array(
					array( 'title' => 'Динамические испытания', 'detail' => '', 'price' => 'от 30 000 ₽' ),
					array( 'title' => 'Испытания грунтов методом PDA', 'detail' => 'На воде', 'price' => 'от 144 000 ₽' ),
					array( 'title' => 'Испытания грунтов методом PDA', 'detail' => 'На суше', 'price' => 'от 96 000 ₽' ),
					array( 'title' => 'Испытания динамическим штампом', 'detail' => '(ОДМ 218)', 'price' => 'от 24 000 ₽' ),
				),
			),
			array(
				'title' => 'Штамповые испытания',
				'rows'  => array(
					array( 'title' => 'На глине и суглинке', 'detail' => '', 'price' => 'от 64 000 ₽' ),
					array( 'title' => 'На песке и щебне', 'detail' => '', 'price' => 'от 24 000 ₽' ),
				),
			),
			array(
				'title' => 'Глубинные испытания',
				'rows'  => array(
					array( 'title' => 'Для песчаного грунта', 'detail' => '', 'price' => 'от 90 000 ₽' ),
					array( 'title' => 'На суглинке', 'detail' => '', 'price' => 'от 120 000 ₽' ),
					array( 'title' => 'Измерение сплошности сваи', 'detail' => 'Единица измерения: свая', 'price' => 'от 3 500 ₽' ),
				),
			),
		);

		$acf_groups = array();

		foreach ( $price_groups as $group ) {
			$rows = array();
			foreach ( $group['rows'] as $row ) {
				$rows[] = array(
					'field_arcticrc_service_price_title'  => $row['title'],
					'field_arcticrc_service_price_detail' => $row['detail'],
					'field_arcticrc_service_price_value'  => $row['price'],
				);
			}

			$acf_groups[] = array(
				'field_arcticrc_service_price_group_title' => $group['title'],
				'field_arcticrc_service_price_rows'        => $rows,
			);
		}

		update_field( 'field_arcticrc_service_price_groups', $acf_groups, $post_id );
	}

	if ( ! get_field( 'service_company_features', $post_id ) ) {
		$equipment_desktop = arcticrc_seed_media_asset( 'media/web/proof-equipment-desktop.webp', 'Своё оборудование desktop' );
		$equipment_mobile  = arcticrc_seed_media_asset( 'media/web/proof-equipment-mobile.webp', 'Своё оборудование mobile' );
		$permafrost_desktop = arcticrc_seed_media_asset( 'media/web/proof-permafrost-desktop.webp', 'Работа в мерзлоте desktop' );
		$permafrost_mobile  = arcticrc_seed_media_asset( 'media/web/proof-permafrost-mobile.webp', 'Работа в мерзлоте mobile' );

		update_field(
			'field_arcticrc_service_company_features',
			array(
				array(
					'field_arcticrc_service_company_feature_stat'    => '50+',
					'field_arcticrc_service_company_feature_title'   => 'единиц своей техники',
					'field_arcticrc_service_company_feature_body'    => 'Собственные буровые установки типа УРБ-2А2, ЛБУ-50, УБМ-831М, МТЛБ с буровой установкой, сваебойная техника ЧТЗ СП49, стройматик 320, и другая спец.техника для работы на объектах',
					'field_arcticrc_service_company_feature_desktop' => $equipment_desktop,
					'field_arcticrc_service_company_feature_mobile'  => $equipment_mobile,
				),
				array(
					'field_arcticrc_service_company_feature_stat'    => '',
					'field_arcticrc_service_company_feature_title'   => 'Оказываем услуги в условиях мерзлоты',
					'field_arcticrc_service_company_feature_body'    => 'Оборудование и опыт позволяет работать в условиях Крайнего Севера',
					'field_arcticrc_service_company_feature_desktop' => $permafrost_desktop,
					'field_arcticrc_service_company_feature_mobile'  => $permafrost_mobile,
				),
			),
			$post_id
		);
	}

	if ( ! get_field( 'service_company_credentials', $post_id ) ) {
		update_field(
			'field_arcticrc_service_company_credentials',
			array(
				array( 'field_arcticrc_service_company_credential_title' => 'Аккредитованная лаборатория', 'field_arcticrc_service_company_credential_body' => 'Лаборатория включена в реестр Федеральной службы по аккредитации (Росаккредитация)', 'field_arcticrc_service_company_credential_size' => 'compact' ),
				array( 'field_arcticrc_service_company_credential_title' => 'Своё оборудование', 'field_arcticrc_service_company_credential_body' => 'Сертифицированное и поверенное оборудование', 'field_arcticrc_service_company_credential_size' => 'narrow' ),
				array( 'field_arcticrc_service_company_credential_title' => 'Сотрудники зарегистрированны в НОПРИЗ', 'field_arcticrc_service_company_credential_body' => 'Что подтверждает высокую квалификацию исполнителей и гарантию качества работы', 'field_arcticrc_service_company_credential_size' => 'medium' ),
				array( 'field_arcticrc_service_company_credential_title' => 'СРО по изысканиям, проектированию и строительству', 'field_arcticrc_service_company_credential_body' => 'В том числе с опасными и технически сложными объектами', 'field_arcticrc_service_company_credential_size' => 'full' ),
			),
			$post_id
		);
	}

	$project_ids = array(
		arcticrc_seed_project(
			'soil-testing-mount-workplace',
			'Испытания грунтов плоским штампом на объекте Mount — workplace, согласно ГОСТ 20276.1-2020.',
			'media/web/c40ba464477e42825628a598601f7e2cc2acea29.webp'
		),
		arcticrc_seed_project(
			'soil-bearing-capacity',
			'Соответствие несущей способности грунтов расчетным нагрузкам на объекте',
			'media/web/de3892be829af185db31152835844e0786424f66.webp'
		),
	);
	$project_ids = array_values( array_filter( $project_ids ) );

	if ( $project_ids && ! get_field( 'service_projects', $post_id ) ) {
		update_field( 'field_arcticrc_service_projects', $project_ids, $post_id );
	}

	update_post_meta( $post_id, '_arcticrc_service_seed_version', ARCTICRC_SERVICE_SEED_VERSION );
}
add_action( 'admin_init', 'arcticrc_seed_soil_testing_service', 45 );
