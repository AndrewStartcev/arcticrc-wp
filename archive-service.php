<?php
defined( 'ABSPATH' ) || exit;

get_header();

$selected_services = function_exists( 'get_field' ) ? get_field( 'services_archive_items', 'option' ) : array();

if ( ! $selected_services ) {
	$selected_services = get_posts(
		array(
			'post_type'      => 'service',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			),
		)
	);
}

$phone   = arcticrc_option( 'site_phone', '+7(916)-616-02-20' );
$email   = arcticrc_option( 'site_email', 'engineering@arcticrc.ru' );
$address = arcticrc_option( 'site_address', 'Москва, 2-й Кожевнический пер, д.1, помещ. 1-H' );
$socials = arcticrc_socials();
?>
<main class="page page--services" id="main">
	<div class="page__container">
		<?php arcticrc_breadcrumbs(); ?>

		<div class="page-heading">
			<h1 class="page-heading__title"><?php echo esc_html( get_field( 'services_archive_title', 'option' ) ?: 'Уверенность начинается с основания' ); ?></h1>
		</div>

		<?php if ( arcticrc_services_archive_block_visible( 'services_archive_visible' ) ) : ?>
			<div class="page__cards page__cards--services">
				<div class="collection collection--services">
					<?php foreach ( (array) $selected_services as $index => $service ) : ?>
						<?php arcticrc_render_service_card( $service, $index ); ?>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( arcticrc_services_archive_block_visible( 'services_contact_visible' ) ) : ?>
			<section class="enquiry-section" id="request" aria-label="Связаться с нами">
				<div class="contact-details contact-details--shared">
					<div class="contact-details__heading">
						<div class="page-heading page-heading--section">
							<h2 class="page-heading__title"><?php echo esc_html( get_field( 'services_contact_title', 'option' ) ?: 'Начнем с разговора — доведем до результата' ); ?></h2>
						</div>
					</div>
					<p class="contact-details__intro"><?php echo esc_html( get_field( 'services_contact_intro', 'option' ) ?: '' ); ?></p>

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

				<?php echo arcticrc_cf7_form_html( 'consultation', 'consultation', 'enquiry-form enquiry-form--consultation enquiry-section__form' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</section>
		<?php endif; ?>
	</div>
</main>
<?php get_footer(); ?>
