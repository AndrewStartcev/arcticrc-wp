<?php
/**
 * Breadcrumbs.
 *
 * @package ArcticRC
 */

defined( 'ABSPATH' ) || exit;

function arcticrc_breadcrumb_items() {
	if ( is_front_page() ) {
		return array();
	}

	$items = array(
		array(
			'label' => 'Главная',
			'url'   => home_url( '/' ),
		),
	);

	if ( is_singular( 'service' ) ) {
		$items[] = array(
			'label' => 'Услуги',
			'url'   => home_url( '/services/' ),
		);

		$items[] = array(
			'label' => get_the_title(),
			'url'   => '',
		);

		return $items;
	}

	if ( is_post_type_archive( 'service' ) ) {
		$items[] = array(
			'label' => 'Услуги',
			'url'   => '',
		);

		return $items;
	}

	if ( is_singular( 'equipment' ) ) {
		$items[] = array(
			'label' => 'Оборудование',
			'url'   => get_post_type_archive_link( 'equipment' ),
		);

		$items[] = array(
			'label' => get_the_title(),
			'url'   => '',
		);

		return $items;
	}

	if ( is_post_type_archive( 'equipment' ) ) {
		$items[] = array(
			'label' => 'Оборудование',
			'url'   => '',
		);

		return $items;
	}

	if ( is_singular( 'project' ) ) {
		$items[] = array(
			'label' => 'Проекты',
			'url'   => get_post_type_archive_link( 'project' ),
		);

		$items[] = array(
			'label' => get_the_title(),
			'url'   => '',
		);

		return $items;
	}

	if ( is_post_type_archive( 'project' ) ) {
		$items[] = array(
			'label' => 'Проекты',
			'url'   => '',
		);

		return $items;
	}

	if ( is_page() ) {
		$post = get_queried_object();

		if ( $post instanceof WP_Post ) {
			$ancestor_ids = array_reverse( get_post_ancestors( $post ) );

			foreach ( $ancestor_ids as $ancestor_id ) {
				$items[] = array(
					'label' => get_the_title( $ancestor_id ),
					'url'   => get_permalink( $ancestor_id ),
				);
			}

			$items[] = array(
				'label' => get_the_title( $post ),
				'url'   => '',
			);
		}

		return $items;
	}

	if ( is_404() ) {
		$items[] = array(
			'label' => 'Страница не найдена',
			'url'   => '',
		);

		return $items;
	}

	$title = wp_get_document_title();

	if ( $title ) {
		$items[] = array(
			'label' => $title,
			'url'   => '',
		);
	}

	return $items;
}

function arcticrc_breadcrumbs( $echo = true ) {
	$items = arcticrc_breadcrumb_items();

	if ( ! $items ) {
		return '';
	}

	ob_start();
	?>
	<nav class="breadcrumbs" aria-label="Хлебные крошки">
		<?php foreach ( $items as $index => $item ) : ?>
			<?php if ( $index > 0 ) : ?>
				<span aria-hidden="true">/</span>
			<?php endif; ?>

			<?php if ( ! empty( $item['url'] ) ) : ?>
				<a class="breadcrumbs__link" href="<?php echo esc_url( $item['url'] ); ?>"><?php echo esc_html( $item['label'] ); ?></a>
			<?php else : ?>
				<span aria-current="page"><?php echo esc_html( $item['label'] ); ?></span>
			<?php endif; ?>
		<?php endforeach; ?>
	</nav>
	<?php
	$html = trim( ob_get_clean() );

	if ( $echo ) {
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	return $html;
}
