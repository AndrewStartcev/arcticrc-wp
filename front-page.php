<?php
defined( 'ABSPATH' ) || exit;

get_header();

$page_id = arcticrc_home_page_id();
$slides = function_exists( 'get_field' ) ? get_field( 'home_hero_slides', $page_id ) : array();
$services = function_exists( 'get_field' ) ? get_field( 'home_services', $page_id ) : array();
$clients = function_exists( 'get_field' ) ? get_field( 'home_clients', $page_id ) : array();
$documents = function_exists( 'get_field' ) ? get_field( 'home_documents', $page_id ) : array();

$phone = arcticrc_option( 'site_phone', '+7(916)-616-02-20' );
$email = arcticrc_option( 'site_email', 'engineering@arcticrc.ru' );
$address = arcticrc_option( 'site_address', 'Москва, 2-й Кожевнический пер, д.1, помещ. 1-H' );
$socials = arcticrc_socials();
?>
<main class="page__main" id="main">
	<div class="page__hero">
		<div class="record-rail record-rail--hero" data-record-rail aria-label="Инженерные изыскания">
			<div class="record-rail__toolbar record-rail__toolbar--hero" data-rail-controls>
				<span class="record-rail__count"><span class="record-rail__current" data-rail-current>01</span> <span class="record-rail__separator">/</span> <span data-rail-total><?php echo esc_html( str_pad( (string) max( 1, count( $slides ) ), 2, '0', STR_PAD_LEFT ) ); ?></span></span>
				<div class="record-rail__buttons">
					<button class="action action--icon action--light action--hero-control" type="button" data-rail-prev aria-label="Предыдущая запись" hidden><span class="action__arrow action__arrow--prev" aria-hidden="true"></span></button>
					<button class="action action--icon action--primary action--hero-control" type="button" data-rail-next aria-label="Следующая запись" hidden><span class="action__arrow action__arrow--next" aria-hidden="true"></span></button>
				</div>
			</div>
			<div class="record-rail__track" data-rail-track>
				<?php foreach ( (array) $slides as $index => $slide ) :
					$desktop = arcticrc_image_url( $slide['desktop_image'] ?? '', 'media/web/home-hero-desktop.webp' );
					$mobile = arcticrc_image_url( $slide['mobile_image'] ?? '', 'media/web/home-hero-mobile.webp' );
					$title = $slide['title'] ?? 'Инженерные изыскания';
					$bullets = $slide['bullets'] ?? array();
				?>
				<section class="media-hero media-hero--home" id="hero-<?php echo esc_attr( str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?>" data-rail-item>
					<picture class="media-hero__media"><source media="(max-width: 600px)" srcset="<?php echo esc_url( $mobile ); ?>"><img class="media-hero__image" src="<?php echo esc_url( $desktop ); ?>" alt="" <?php echo 0 === $index ? 'fetchpriority="high"' : 'loading="lazy"'; ?> decoding="async"></picture>
					<div class="media-hero__content">
						<div class="media-hero__heading"><div class="page-heading"><?php echo 0 === $index ? '<h1 class="page-heading__title">' : '<h2 class="page-heading__title">'; ?><?php echo esc_html( $title ); ?><?php echo 0 === $index ? '</h1>' : '</h2>'; ?></div></div>
						<div class="media-hero__details">
							<?php if ( $bullets ) : ?><ul class="media-hero__bullets"><?php foreach ( $bullets as $bullet ) : ?><li><?php echo esc_html( $bullet['text'] ?? '' ); ?></li><?php endforeach; ?></ul><?php endif; ?>
							<div class="media-hero__actions">
								<a class="action action--primary" data-dialog-open data-purpose="consultation" data-record-id="" href="#request"><span class="action__marker" aria-hidden="true"></span><span class="action__label">Обсудить проект</span></a>
								<a class="action action--light action--accent-marker" href="<?php echo esc_url( home_url( '/services/soil-testing/#projects' ) ); ?>"><span class="action__marker" aria-hidden="true"></span><span class="action__label">Смотреть проекты</span></a>
							</div>
						</div>
					</div>
				</section>
				<?php endforeach; ?>
			</div>
		</div>
	</div>

	<div class="page__container">
		<section class="page__section page__section--services page__section--after-hero" id="services">
			<div class="page-heading page-heading--section page-heading--services"><h2 class="page-heading__title"><?php echo esc_html( arcticrc_home_value( 'home_services_title', 'Наши услуги' ) ); ?></h2></div>
			<div class="page__section-content"><div class="collection collection--services">
				<?php foreach ( (array) $services as $index => $row ) :
					$service = $row['service'] ?? null;
					if ( is_numeric( $service ) ) { $service = get_post( (int) $service ); }
					$title = $service instanceof WP_Post ? get_the_title( $service ) : 'Полевые испытания грунтов сваями';
					$url = $service instanceof WP_Post ? get_permalink( $service ) : home_url( '/services/soil-testing/' );
					$image = arcticrc_image_url( $row['image'] ?? '', 'media/web/18d059bc1b44b09a13800860e84b987e755b9dc4.webp' );
				?>
				<article class="service-card" id="service-<?php echo esc_attr( str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?>">
					<div class="service-card__body"><div class="service-card__heading"><h3 class="service-card__title"><?php echo esc_html( $title ); ?></h3><span class="service-card__number"><?php echo esc_html( $row['number'] ?? '/01' ); ?></span></div><div class="service-card__details"><ul class="service-card__bullets"><li>Геотехнический мониторинг</li><li>Инженерно-геологических изыскания</li><li>Инклинометрические измерения скважин</li><li>Оценка вибрационного воздействия</li></ul><a class="action action--primary action--card-link service-card__action" href="<?php echo esc_url( $url ); ?>"><span class="action__marker" aria-hidden="true"></span><span class="action__label">Подробнее</span></a></div></div>
					<img class="service-card__image" src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy" decoding="async">
				</article>
				<?php endforeach; ?>
			</div></div>
		</section>

		<section class="page__section page__section--map" id="geography">
			<div class="page-heading page-heading--section page-heading--geography"><h2 class="page-heading__title"><?php echo esc_html( arcticrc_home_value( 'home_geography_title', 'Наша география — от Мурманска до Владивостока' ) ); ?></h2><p class="page-heading__lead"><?php echo esc_html( arcticrc_home_value( 'home_geography_lead', 'Оказываем услуги по всей территории России, в том числе в условиях крайнего Севера.' ) ); ?></p></div>
			<div class="page__section-content"><div class="source-map source-map--geography" id="geography-map">
				<?php if ( arcticrc_home_geography_is_map() ) : ?>
					<div class="source-map__interactive source-map__interactive--geography" data-yandex-map data-center-lat="<?php echo esc_attr( arcticrc_home_value( 'home_geography_lat', '61.5240' ) ); ?>" data-center-lng="<?php echo esc_attr( arcticrc_home_value( 'home_geography_lng', '105.3188' ) ); ?>" data-zoom="<?php echo esc_attr( (string) arcticrc_home_value( 'home_geography_zoom', 3 ) ); ?>">
						<?php $points = function_exists( 'get_field' ) ? get_field( 'home_geography_points', $page_id ) : array(); ?>
						<script type="application/json" data-map-points><?php echo wp_json_encode( $points ?: array(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ); ?></script>
					</div>
				<?php else :
					$desktop = arcticrc_image_url( arcticrc_home_value( 'home_geography_desktop', '' ), 'media/web/home-geography-desktop.webp' );
					$mobile = arcticrc_image_url( arcticrc_home_value( 'home_geography_mobile', '' ), 'media/web/home-geography-mobile.webp' );
				?>
					<picture><source media="(max-width: 600px)" srcset="<?php echo esc_url( $mobile ); ?>"><img class="source-map__image source-map__image--geography" src="<?php echo esc_url( $desktop ); ?>" alt="География работ в России" loading="lazy" decoding="async"></picture>
				<?php endif; ?>
			</div></div>
		</section>
	</div>

	<section class="page__section page__section--clients" id="clients">
		<div class="page__section-heading"><div class="page-heading page-heading--section page-heading--clients"><h2 class="page-heading__title"><?php echo esc_html( arcticrc_home_value( 'home_clients_title', 'Наши заказчики — ориентир в надёжности' ) ); ?></h2></div></div>
		<div class="page__section-content page__section-content--full"><div class="record-rail record-rail--clients" data-record-rail aria-label="Наши заказчики — ориентир в надёжности">
			<div class="record-rail__toolbar record-rail__toolbar--clients" data-rail-controls><span class="record-rail__count"><span class="record-rail__current" data-rail-current>01</span> <span class="record-rail__separator">/</span> <span data-rail-total><?php echo esc_html( str_pad( (string) max( 1, count( $clients ) ), 2, '0', STR_PAD_LEFT ) ); ?></span></span><div class="record-rail__buttons"><button class="action action--icon action--outline action--rail" type="button" data-rail-prev aria-label="Предыдущая запись" hidden><span class="action__arrow action__arrow--prev" aria-hidden="true"></span></button><button class="action action--icon action--primary action--rail" type="button" data-rail-next aria-label="Следующая запись" hidden><span class="action__arrow action__arrow--next" aria-hidden="true"></span></button></div></div>
			<div class="record-rail__track" data-rail-track>
				<?php foreach ( array_chunk( (array) $clients, 4 ) as $page_clients ) : ?>
					<div class="record-rail__page" data-rail-page>
						<?php foreach ( $page_clients as $client ) : ?>
							<div class="client-logo" data-rail-item><img class="client-logo__image" src="<?php echo esc_url( arcticrc_image_url( $client['image'] ?? '' ) ); ?>" alt="<?php echo esc_attr( $client['name'] ?? 'Заказчик' ); ?>" loading="lazy" decoding="async"></div>
						<?php endforeach; ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div></div>
	</section>

	<section class="documents-section" id="documents">
		<?php $docs_desktop = arcticrc_image_url( arcticrc_home_value( 'home_documents_background_desktop', '' ), 'media/web/home-documents-desktop.webp' ); $docs_mobile = arcticrc_image_url( arcticrc_home_value( 'home_documents_background_mobile', '' ), 'media/web/home-documents-mobile.webp' ); ?>
		<picture><source media="(max-width: 600px)" srcset="<?php echo esc_url( $docs_mobile ); ?>"><img class="documents-section__background" src="<?php echo esc_url( $docs_desktop ); ?>" alt="" loading="lazy" decoding="async"></picture>
		<div class="documents-section__inner"><div class="page-heading page-heading--section page-heading--documents"><h2 class="page-heading__title"><?php echo esc_html( arcticrc_home_value( 'home_documents_title', 'Документация компании' ) ); ?></h2><p class="page-heading__lead"><?php echo esc_html( arcticrc_home_value( 'home_documents_lead', '' ) ); ?></p></div>
			<div class="documents-section__cards"><div class="collection collection--documents"><?php foreach ( (array) $documents as $index => $doc ) : ?><article class="document-card" id="document-<?php echo esc_attr( str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?>"><h3 class="document-card__title"><?php echo esc_html( $doc['title'] ?? '' ); ?></h3><div class="document-card__details"><div class="document-card__metadata"><div class="document-card__fact"><span class="document-card__fact-label">тип</span><p><?php echo esc_html( $doc['type'] ?? '' ); ?></p></div><div class="document-card__fact"><span class="document-card__fact-label">вес</span><p><?php echo esc_html( $doc['weight'] ?? '' ); ?></p></div></div><?php if ( ! empty( $doc['file'] ) ) : $file_url = is_array( $doc['file'] ) ? ( $doc['file']['url'] ?? '' ) : wp_get_attachment_url( (int) $doc['file'] ); ?><a class="action action--primary document-card__action" href="<?php echo esc_url( $file_url ); ?>" target="_blank" rel="noopener"><span class="action__marker" aria-hidden="true"></span><span class="action__label">Посмотреть файл</span></a><?php else : ?><button class="action action--primary document-card__action" type="button" disabled><span class="action__marker" aria-hidden="true"></span><span class="action__label">Посмотреть файл</span></button><?php endif; ?></div></article><?php endforeach; ?></div></div>
		</div>
	</section>

	<div class="page__container"><section class="enquiry-section" id="request" aria-label="Связаться с нами">
		<div class="contact-details contact-details--shared"><div class="contact-details__heading"><div class="page-heading page-heading--section"><h2 class="page-heading__title"><?php echo esc_html( arcticrc_home_value( 'home_contact_title', 'Начнем с разговора — доведем до результата' ) ); ?></h2></div></div><p class="contact-details__intro"><?php echo esc_html( arcticrc_home_value( 'home_contact_intro', '' ) ); ?></p><div class="contact-details__rows">
			<div class="contact-details__row"><div class="contact-details__value"><span class="contact-details__label">телефон</span><a class="contact-details__link" href="<?php echo esc_url( 'tel:' . arcticrc_phone_href( $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a></div><div class="contact-details__actions"><?php foreach ( $socials as $social ) : ?><a class="action action--outline action--compact action--social contact-details__action contact-details__action--social" href="<?php echo esc_url( $social['url'] ); ?>" aria-label="<?php echo esc_attr( $social['name'] ); ?>"><span class="action__marker contact-details__channel-marker" aria-hidden="true"></span><span class="action__label"><?php if ( $social['icon'] ) : ?><img class="contact-details__channel-icon" src="<?php echo esc_url( $social['icon'] ); ?>" alt=""><?php endif; ?><span class="contact-details__channel-label"><?php echo esc_html( $social['name'] ); ?></span></span></a><?php endforeach; ?></div></div>
			<div class="contact-details__row"><div class="contact-details__value"><span class="contact-details__label">почта</span><a class="contact-details__link" href="<?php echo esc_url( 'mailto:' . sanitize_email( $email ) ); ?>"><?php echo esc_html( $email ); ?></a></div><div class="contact-details__actions"><a class="action action--outline action--compact contact-details__action" href="<?php echo esc_url( 'mailto:' . sanitize_email( $email ) ); ?>"><span class="action__marker" aria-hidden="true"></span><span class="action__label">Написать на почту</span></a></div></div>
			<div class="contact-details__row"><div class="contact-details__value"><span class="contact-details__label">центральный офис</span><?php echo esc_html( $address ); ?></div><div class="contact-details__actions"><a class="action action--outline action--compact contact-details__action" href="<?php echo esc_url( home_url( '/contacts/#map' ) ); ?>"><span class="action__marker" aria-hidden="true"></span><span class="action__label">Показать на карте</span></a></div></div>
		</div></div>
		<?php echo arcticrc_cf7_form_html( 'consultation', 'consultation', 'enquiry-form enquiry-form--consultation enquiry-section__form' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</section></div>
</main>
<?php get_footer(); ?>
