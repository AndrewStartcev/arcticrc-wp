<?php
/**
 * Template Name: Оборудование
 * Template Post Type: page
 *
 * Shared template for rent and sale pages.
 *
 * @package ArcticRC
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	$page_id      = get_the_ID();
	$mode         = arcticrc_equipment_page_mode( $page_id );
	$purpose      = 'sale' === $mode ? 'sale' : 'rent';
	$title        = get_field( 'equipment_page_title', $page_id ) ?: get_the_title();
	$description  = get_field( 'equipment_page_description', $page_id );
	$hero_desktop = arcticrc_image_url( get_field( 'equipment_page_hero_desktop', $page_id ) );
	$hero_mobile  = arcticrc_image_url( get_field( 'equipment_page_hero_mobile', $page_id ) );
	$primary_text = get_field( 'equipment_page_primary_text', $page_id ) ?: ( 'sale' === $mode ? 'Обсудить проект' : 'Заказать аренду' );
	$catalog      = get_field( 'equipment_page_catalog', $page_id );
	$items        = get_field( 'equipment_page_items', $page_id );

	$phone   = arcticrc_option( 'site_phone', '+7(916)-616-02-20' );
	$email   = arcticrc_option( 'site_email', 'engineering@arcticrc.ru' );
	$address = arcticrc_option( 'site_address', 'Москва, 2-й Кожевнический пер, д.1, помещ. 1-H' );
	$socials = arcticrc_socials();

	$catalog_url = '';
	if ( is_array( $catalog ) && ! empty( $catalog['url'] ) ) {
		$catalog_url = $catalog['url'];
	} elseif ( is_numeric( $catalog ) ) {
		$catalog_url = wp_get_attachment_url( (int) $catalog );
	}
	?>
	<main class="page__main" id="main">
		<div class="page__hero">
			<section class="media-hero media-hero--equipment" id="equipment-hero">
				<?php if ( $hero_desktop || $hero_mobile ) : ?>
					<picture class="media-hero__media">
						<?php if ( $hero_mobile ) : ?><source media="(max-width: 600px)" srcset="<?php echo esc_url( $hero_mobile ); ?>"><?php endif; ?>
						<?php if ( $hero_desktop ) : ?><img class="media-hero__image" src="<?php echo esc_url( $hero_desktop ); ?>" alt="" fetchpriority="high" decoding="async"><?php endif; ?>
					</picture>
				<?php endif; ?>

				<div class="media-hero__content">
					<?php arcticrc_breadcrumbs(); ?>

					<div class="page-heading">
						<h1 class="page-heading__title"><?php echo esc_html( $title ); ?></h1>
					</div>

					<div class="media-hero__details">
						<?php if ( $description ) : ?><p class="media-hero__description<?php echo 'sale' === $mode ? ' media-hero__description--compact' : ''; ?>"><?php echo esc_html( $description ); ?></p><?php endif; ?>

						<div class="media-hero__actions">
							<a class="action action--primary" data-dialog-open data-purpose="<?php echo esc_attr( $purpose ); ?>" data-record-id="" href="#request">
								<span class="action__marker" aria-hidden="true"></span>
								<span class="action__label"><?php echo esc_html( $primary_text ); ?></span>
							</a>

							<?php if ( $catalog_url ) : ?>
								<a class="action action--light action--accent-marker" href="<?php echo esc_url( $catalog_url ); ?>" download>
									<span class="action__marker" aria-hidden="true"></span>
									<span class="action__label">Скачать каталог</span>
								</a>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</section>
		</div>

		<div class="page__container">
			<?php if ( arcticrc_equipment_page_block_visible( 'equipment_page_items_visible', $page_id ) ) : ?>
				<section class="page__section page__section--equipment page__section--after-hero" id="equipment">
					<div class="page-heading page-heading--section">
						<h2 class="page-heading__title"><?php echo esc_html( get_field( 'equipment_page_items_title', $page_id ) ?: 'Техника и оборудование' ); ?></h2>
					</div>

					<div class="page__section-content">
						<div class="collection collection--equipment">
							<?php foreach ( (array) $items as $index => $item ) : ?>
								<?php arcticrc_render_equipment_card( $item, $index ); ?>
							<?php endforeach; ?>

							<article class="enquiry-card enquiry-card--equipment">
								<h3 class="enquiry-card__title"><?php echo esc_html( get_field( 'equipment_page_cta_title', $page_id ) ?: 'Нужно оборудование для объекта?' ); ?></h3>
								<p class="enquiry-card__text"><?php echo esc_html( get_field( 'equipment_page_cta_text', $page_id ) ?: 'Подберём технику под вид работ, условия площадки и сроки.' ); ?></p>

								<div class="enquiry-card__actions">
									<a class="action action--light-outline" data-dialog-open data-purpose="<?php echo esc_attr( $purpose ); ?>" data-record-id="" href="#request">
										<span class="action__marker" aria-hidden="true"></span>
										<span class="action__label">Связаться с нами</span>
									</a>

									<?php if ( $catalog_url ) : ?>
										<a class="action action--inverse" href="<?php echo esc_url( $catalog_url ); ?>" download>
											<span class="action__marker" aria-hidden="true"></span>
											<span class="action__label">Скачать каталог</span>
										</a>
									<?php endif; ?>
								</div>
							</article>
						</div>
					</div>
				</section>
			<?php endif; ?>

			<?php if ( arcticrc_equipment_page_block_visible( 'equipment_page_contact_visible', $page_id ) ) : ?>
				<section class="enquiry-section" id="request" aria-label="Связаться с нами">
					<div class="contact-details contact-details--shared">
						<div class="contact-details__heading">
							<div class="page-heading page-heading--section">
								<h2 class="page-heading__title"><?php echo esc_html( get_field( 'equipment_page_contact_title', $page_id ) ?: 'Начнем с разговора — доведем до результата' ); ?></h2>
							</div>
						</div>

						<p class="contact-details__intro"><?php echo esc_html( get_field( 'equipment_page_contact_intro', $page_id ) ?: '' ); ?></p>

						<div class="contact-details__rows">
							<div class="contact-details__row">
								<div class="contact-details__value"><span class="contact-details__label">телефон</span><a class="contact-details__link" href="<?php echo esc_url( 'tel:' . arcticrc_phone_href( $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a></div>
								<div class="contact-details__actions">
									<?php foreach ( $socials as $social ) : ?>
										<a class="action action--outline action--compact action--social contact-details__action contact-details__action--social" href="<?php echo esc_url( $social['url'] ); ?>" aria-label="<?php echo esc_attr( $social['name'] ); ?>">
											<span class="action__marker contact-details__channel-marker" aria-hidden="true"></span>
											<span class="action__label"><?php if ( $social['icon'] ) : ?><img class="contact-details__channel-icon" src="<?php echo esc_url( $social['icon'] ); ?>" alt=""><?php endif; ?><span class="contact-details__channel-label"><?php echo esc_html( $social['name'] ); ?></span></span>
										</a>
									<?php endforeach; ?>
								</div>
							</div>

							<div class="contact-details__row">
								<div class="contact-details__value"><span class="contact-details__label">почта</span><a class="contact-details__link" href="<?php echo esc_url( 'mailto:' . sanitize_email( $email ) ); ?>"><?php echo esc_html( $email ); ?></a></div>
								<div class="contact-details__actions"><a class="action action--outline action--compact contact-details__action" href="<?php echo esc_url( 'mailto:' . sanitize_email( $email ) ); ?>"><span class="action__marker" aria-hidden="true"></span><span class="action__label">Написать на почту</span></a></div>
							</div>

							<div class="contact-details__row">
								<div class="contact-details__value"><span class="contact-details__label">центральный офис</span><?php echo esc_html( $address ); ?></div>
								<div class="contact-details__actions"><a class="action action--outline action--compact contact-details__action" href="<?php echo esc_url( home_url( '/contacts/#map' ) ); ?>"><span class="action__marker" aria-hidden="true"></span><span class="action__label">Показать на карте</span></a></div>
							</div>
						</div>
					</div>

					<?php echo arcticrc_cf7_form_html( 'equipment', $purpose, 'enquiry-form enquiry-form--equipment enquiry-section__form' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</section>
			<?php endif; ?>
		</div>
	</main>
	<?php
endwhile;

get_footer();
