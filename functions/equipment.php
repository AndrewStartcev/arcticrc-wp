<?php
/**
 * Equipment pages and seeded equipment records.
 *
 * @package ArcticRC
 */

defined( 'ABSPATH' ) || exit;

const ARCTICRC_EQUIPMENT_SEED_VERSION = '2026-10-07-1';

function arcticrc_equipment_page_mode( $page_id = 0 ) {
	$page_id = $page_id ? (int) $page_id : get_queried_object_id();
	$slug    = $page_id ? get_post_field( 'post_name', $page_id ) : '';

	return 'equipment-sale' === $slug ? 'sale' : 'rent';
}

function arcticrc_equipment_page_block_visible( $field_name, $page_id = 0 ) {
	$page_id = $page_id ? (int) $page_id : get_queried_object_id();

	if ( ! $page_id || ! function_exists( 'get_field' ) ) {
		return true;
	}

	$value = get_field( $field_name, $page_id );

	return false !== $value && '0' !== (string) $value;
}

function arcticrc_seed_equipment_item( $slug, $title, $image_path, $tags, $mode_term_ids = array() ) {
	$post = get_page_by_path( $slug, OBJECT, 'equipment' );

	if ( ! $post ) {
		$post_id = wp_insert_post(
			array(
				'post_type'   => 'equipment',
				'post_status' => 'publish',
				'post_title'  => $title,
				'post_name'   => $slug,
			)
		);
	} else {
		$post_id = (int) $post->ID;
	}

	if ( ! $post_id || is_wp_error( $post_id ) ) {
		return 0;
	}

	$image_id = arcticrc_seed_media_asset( $image_path, $title );

	if ( $image_id && ! has_post_thumbnail( $post_id ) ) {
		set_post_thumbnail( $post_id, $image_id );
	}

	if ( function_exists( 'update_field' ) && ! get_field( 'equipment_tags', $post_id ) ) {
		$rows = array();

		foreach ( $tags as $tag ) {
			$rows[] = array(
				'field_arcticrc_equipment_tag_text' => $tag,
			);
		}

		update_field( 'field_arcticrc_equipment_tags', $rows, $post_id );
	}

	if ( $mode_term_ids ) {
		wp_set_object_terms( $post_id, array_map( 'intval', $mode_term_ids ), 'equipment_mode', false );
	}

	return (int) $post_id;
}

function arcticrc_seed_equipment_pages() {
	if ( ! is_admin() || ! current_user_can( 'manage_options' ) || ! function_exists( 'update_field' ) ) {
		return;
	}

	$rent_term = term_exists( 'rent', 'equipment_mode' );
	if ( ! $rent_term ) {
		$rent_term = wp_insert_term( 'Аренда', 'equipment_mode', array( 'slug' => 'rent' ) );
	}

	$sale_term = term_exists( 'sale', 'equipment_mode' );
	if ( ! $sale_term ) {
		$sale_term = wp_insert_term( 'Продажа', 'equipment_mode', array( 'slug' => 'sale' ) );
	}

	$mode_term_ids = array();

	foreach ( array( $rent_term, $sale_term ) as $term_result ) {
		if ( is_array( $term_result ) && ! empty( $term_result['term_id'] ) ) {
			$mode_term_ids[] = (int) $term_result['term_id'];
		} elseif ( is_numeric( $term_result ) ) {
			$mode_term_ids[] = (int) $term_result;
		}
	}

	$items = array(
		arcticrc_seed_equipment_item(
			'drilling-rigs',
			'Буровые установки',
			'media/web/ea3c9f4f69acc28f6a8d77f0a216f02c510b09ec.webp',
			array( 'УРБ-2А2', 'ЛБУ-50', 'УБМ-831' ),
			$mode_term_ids
		),
		arcticrc_seed_equipment_item(
			'piling-equipment',
			'Сваебойная техника',
			'media/web/equipment-card-01.webp',
			array( 'Установка УТЗ СП49' ),
			$mode_term_ids
		),
		arcticrc_seed_equipment_item(
			'testing-equipment',
			'Испытательное оборудование',
			'media/web/6d1d47da8d7a18471f4ec54337bfcfe2acc1c35c.webp',
			array( 'Домкраты', 'Стенды', 'Упорные конструкции' ),
			$mode_term_ids
		),
		arcticrc_seed_equipment_item(
			'measuring-equipment',
			'Измерительное оборудование',
			'media/web/81c10552a5126a65fc6661768370e13cc9e21ff2.webp',
			array( 'Цифровые манометры', 'Индикаторы', 'Датчики' ),
			$mode_term_ids
		),
		arcticrc_seed_equipment_item(
			'special-equipment',
			'Специальная техника',
			'media/web/ecc90feff0e7e1b50a78250865dd498f33be5a7d.webp',
			array( 'Техника для проведения работ на объектах' ),
			$mode_term_ids
		),
	);
	$items = array_values( array_filter( $items ) );

	$pages = array(
		'equipment-rent' => array(
			'title'        => 'Аренда и продажа оборудования',
			'description'  => 'Всё для испытаний. От оборудования до результата',
			'primary_text' => 'Заказать аренду',
			'hero_desktop' => 'media/web/8c88d7d4ceddb48328090acf6ba08531fa1d47d3.webp',
			'hero_mobile'  => 'media/web/equipment-rent-hero-mobile.webp',
		),
		'equipment-sale' => array(
			'title'        => 'Продажа оборудования и спецтехники',
			'description'  => 'Собственный парк оборудования и спецтехники для инженерных изысканий и строительных работ',
			'primary_text' => 'Обсудить проект',
			'hero_desktop' => 'media/web/884af29cf334150d6dd1ba5861a58f7105032806.webp',
			'hero_mobile'  => 'media/web/equipment-sale-hero-mobile.webp',
		),
	);

	foreach ( $pages as $slug => $data ) {
		$page = get_page_by_path( $slug, OBJECT, 'page' );

		if ( ! $page ) {
			continue;
		}

		$page_id = (int) $page->ID;

		update_post_meta( $page_id, '_wp_page_template', 'page-equipment.php' );

		$defaults = array(
			'field_arcticrc_equipment_page_title'         => $data['title'],
			'field_arcticrc_equipment_page_description'   => $data['description'],
			'field_arcticrc_equipment_page_primary_text'  => $data['primary_text'],
			'field_arcticrc_equipment_page_items_title'   => 'Техника и оборудование',
			'field_arcticrc_equipment_page_cta_title'     => 'Нужно оборудование для объекта?',
			'field_arcticrc_equipment_page_cta_text'      => 'Подберём технику под вид работ, условия площадки и сроки.',
			'field_arcticrc_equipment_page_contact_title' => 'Начнем с разговора — доведем до результата',
			'field_arcticrc_equipment_page_contact_intro' => 'Свяжитесь с нами по телефону, оставьте заявку на консультацию или приезжайте в офис! Мы ответим на ваши вопросы',
		);

		foreach ( $defaults as $field_key => $value ) {
			$current = get_field( $field_key, $page_id );

			if ( null === $current || '' === $current ) {
				update_field( $field_key, $value, $page_id );
			}
		}

		$desktop_id = arcticrc_seed_media_asset( $data['hero_desktop'], $data['title'] . ' — desktop' );
		$mobile_id  = arcticrc_seed_media_asset( $data['hero_mobile'], $data['title'] . ' — mobile' );

		if ( $desktop_id && ! get_field( 'equipment_page_hero_desktop', $page_id ) ) {
			update_field( 'field_arcticrc_equipment_page_hero_desktop', $desktop_id, $page_id );
		}

		if ( $mobile_id && ! get_field( 'equipment_page_hero_mobile', $page_id ) ) {
			update_field( 'field_arcticrc_equipment_page_hero_mobile', $mobile_id, $page_id );
		}

		if ( $items && ! get_field( 'equipment_page_items', $page_id ) ) {
			update_field( 'field_arcticrc_equipment_page_items', $items, $page_id );
		}

		update_post_meta( $page_id, '_arcticrc_equipment_seed_version', ARCTICRC_EQUIPMENT_SEED_VERSION );
	}
}
add_action( 'admin_init', 'arcticrc_seed_equipment_pages', 50 );

function arcticrc_render_equipment_card( $post, $index = 0 ) {
	$post = get_post( $post );

	if ( ! $post || 'equipment' !== $post->post_type ) {
		return;
	}

	$tags  = function_exists( 'get_field' ) ? get_field( 'equipment_tags', $post->ID ) : array();
	$image = get_the_post_thumbnail_url( $post->ID, 'large' );
	?>
	<article class="equipment-card" id="equipment-<?php echo esc_attr( str_pad( (string) ( (int) $index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?>">
		<div class="equipment-card__media">
			<?php if ( $image ) : ?><img class="equipment-card__image<?php echo 0 === (int) $index ? ' equipment-card__image--wide' : ''; ?>" src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( get_the_title( $post ) ); ?>" loading="lazy" decoding="async"><?php endif; ?>
		</div>
		<div class="equipment-card__heading">
			<h3 class="equipment-card__title"><?php echo esc_html( get_the_title( $post ) ); ?></h3>
			<span class="equipment-card__number"><?php echo esc_html( str_pad( (string) ( (int) $index + 1 ), 2, '0', STR_PAD_LEFT ) . '/' ); ?></span>
		</div>
		<?php if ( $tags ) : ?>
			<div class="equipment-card__tags">
				<?php foreach ( $tags as $tag ) : ?>
					<?php if ( ! empty( $tag['text'] ) ) : ?><span class="equipment-card__tag"><?php echo esc_html( $tag['text'] ); ?></span><?php endif; ?>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</article>
	<?php
}
