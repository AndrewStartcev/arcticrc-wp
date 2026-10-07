<?php
/**
 * Template Name: Контакты
 * Template Post Type: page
 *
 * @package ArcticRC
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	$page_id = get_the_ID();
	$title   = get_field( 'contacts_title', $page_id ) ?: get_the_title();
	$intro   = get_field( 'contacts_intro', $page_id );

	$phone   = arcticrc_option( 'site_phone', '+7(916)-616-02-20' );
	$email   = arcticrc_option( 'site_email', 'engineering@arcticrc.ru' );
	$address = arcticrc_option( 'site_address', 'Москва, 2-й Кожевнический пер, д.1, помещ. 1-H' );
	$socials = arcticrc_socials();
	?>
	<main class="page page--contacts" id="main">
		<div class="page__container">
			<?php arcticrc_breadcrumbs(); ?>

			<div class="page__contact-heading">
				<div class="page-heading">
					<h1 class="page-heading__title"><?php echo esc_html( $title ); ?></h1>
				</div>
			</div>

			<?php if ( arcticrc_contacts_block_visible( 'contacts_contact_visible', $page_id ) ) : ?>
				<section class="enquiry-section enquiry-section--contacts" id="request" aria-label="Связаться с нами">
					<div class="contact-details">
						<?php if ( $intro ) : ?><p class="contact-details__intro"><?php echo nl2br( esc_html( $intro ) ); ?></p><?php endif; ?>

						<div class="contact-details__rows">
							<div class="contact-details__row">
								<div class="contact-details__value">
									<span class="contact-details__label">телефон</span>
									<a class="contact-details__link" href="<?php echo esc_url( 'tel:' . arcticrc_phone_href( $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a>
								</div>

								<div class="contact-details__actions">
									<?php foreach ( $socials as $social ) : ?>
										<a class="action action--outline action--compact action--social contact-details__action contact-details__action--social" href="<?php echo esc_url( $social['url'] ); ?>" aria-label="<?php echo esc_attr( $social['name'] ); ?>">
											<span class="action__marker contact-details__channel-marker" aria-hidden="true"></span>
											<span class="action__label">
												<?php if ( $social['icon'] ) : ?><img class="contact-details__channel-icon" src="<?php echo esc_url( $social['icon'] ); ?>" alt=""><?php endif; ?>
												<span class="contact-details__channel-label"><?php echo esc_html( $social['name'] ); ?></span>
											</span>
										</a>
									<?php endforeach; ?>
								</div>
							</div>

							<div class="contact-details__row">
								<div class="contact-details__value">
									<span class="contact-details__label">почта</span>
									<a class="contact-details__link" href="<?php echo esc_url( 'mailto:' . sanitize_email( $email ) ); ?>"><?php echo esc_html( $email ); ?></a>
								</div>
								<div class="contact-details__actions">
									<a class="action action--outline action--compact contact-details__action" href="<?php echo esc_url( 'mailto:' . sanitize_email( $email ) ); ?>">
										<span class="action__marker" aria-hidden="true"></span>
										<span class="action__label">Написать на почту</span>
									</a>
								</div>
							</div>

							<div class="contact-details__row">
								<div class="contact-details__value">
									<span class="contact-details__label">центральный офис</span>
									<?php echo esc_html( $address ); ?>
								</div>
								<div class="contact-details__actions">
									<a class="action action--outline action--compact contact-details__action" href="#map">
										<span class="action__marker" aria-hidden="true"></span>
										<span class="action__label">Показать на карте</span>
									</a>
								</div>
							</div>
						</div>
					</div>

					<?php echo arcticrc_cf7_form_html( 'consultation', 'consultation', 'enquiry-form enquiry-form--consultation enquiry-section__form' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</section>
			<?php endif; ?>

			<?php if ( arcticrc_contacts_block_visible( 'contacts_map_visible', $page_id ) ) : ?>
				<div class="source-map source-map--location" id="map">
					<?php if ( arcticrc_contacts_map_is_interactive( $page_id ) ) : ?>
						<div
							class="source-map__interactive source-map__interactive--location"
							data-yandex-map
							data-center-lat="<?php echo esc_attr( get_field( 'contacts_map_lat', $page_id ) ?: '55.7207' ); ?>"
							data-center-lng="<?php echo esc_attr( get_field( 'contacts_map_lng', $page_id ) ?: '37.6476' ); ?>"
							data-zoom="<?php echo esc_attr( (string) ( get_field( 'contacts_map_zoom', $page_id ) ?: 16 ) ); ?>"
						>
							<script type="application/json" data-map-points><?php echo wp_json_encode(
								array(
									array(
										'title' => $address,
										'lat'   => get_field( 'contacts_map_lat', $page_id ) ?: '55.7207',
										'lng'   => get_field( 'contacts_map_lng', $page_id ) ?: '37.6476',
									),
								),
								JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
							); ?></script>
						</div>
					<?php else : ?>
						<?php $map_image = arcticrc_image_url( get_field( 'contacts_map_image', $page_id ), 'media/web/71f1e97da12518a490ccf77ef5801e4017dd3bf2.webp' ); ?>
						<img class="source-map__image source-map__image--location" src="<?php echo esc_url( $map_image ); ?>" alt="Карта расположения офиса" loading="lazy" decoding="async">
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</main>
	<?php
endwhile;

get_footer();
