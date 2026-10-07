<?php
defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	$post_id     = get_the_ID();
	$lead        = function_exists( 'get_field' ) ? get_field( 'service_lead', $post_id ) : '';
	$tests       = function_exists( 'get_field' ) ? get_field( 'service_tests', $post_id ) : array();
	$price_groups = function_exists( 'get_field' ) ? get_field( 'service_price_groups', $post_id ) : array();
	$features    = function_exists( 'get_field' ) ? get_field( 'service_company_features', $post_id ) : array();
	$credentials = function_exists( 'get_field' ) ? get_field( 'service_company_credentials', $post_id ) : array();
	$projects    = function_exists( 'get_field' ) ? get_field( 'service_projects', $post_id ) : array();

	$phone   = arcticrc_option( 'site_phone', '+7(916)-616-02-20' );
	$email   = arcticrc_option( 'site_email', 'engineering@arcticrc.ru' );
	$address = arcticrc_option( 'site_address', 'Москва, 2-й Кожевнический пер, д.1, помещ. 1-H' );
	$socials = arcticrc_socials();
	?>
	<main class="page page--service-detail" id="main">
		<section class="page__intro">
			<div class="page__container">
				<div class="page__detail-breadcrumbs"><?php arcticrc_breadcrumbs(); ?></div>
				<div class="page__detail-heading">
					<div class="page-heading page-heading--detail">
						<h1 class="page-heading__title page-heading__title--detail"><?php the_title(); ?></h1>
						<?php if ( $lead ) : ?><p class="page-heading__lead page-heading__lead--detail"><?php echo esc_html( $lead ); ?></p><?php endif; ?>
					</div>
				</div>

				<?php if ( $tests ) : ?>
					<div class="page__cards">
						<div class="record-rail record-rail--tests" data-record-rail aria-label="<?php echo esc_attr( get_the_title() ); ?>">
							<div class="record-rail__toolbar record-rail__toolbar--tests" data-rail-controls>
								<span class="record-rail__count"><span class="record-rail__current" data-rail-current>01</span> <span class="record-rail__separator">/</span> <span data-rail-total><?php echo esc_html( str_pad( (string) count( $tests ), 2, '0', STR_PAD_LEFT ) ); ?></span></span>
								<div class="record-rail__buttons">
									<button class="action action--icon action--outline action--rail" type="button" data-rail-prev aria-label="Предыдущая запись" hidden><span class="action__arrow action__arrow--prev" aria-hidden="true"></span></button>
									<button class="action action--icon action--primary action--rail" type="button" data-rail-next aria-label="Следующая запись" hidden><span class="action__arrow action__arrow--next" aria-hidden="true"></span></button>
								</div>
							</div>
							<div class="record-rail__track" data-rail-track>
								<?php foreach ( $tests as $index => $test ) :
									$title = $test['title'] ?? '';
									$image = arcticrc_image_url( $test['image'] ?? '' );
									?>
									<article class="test-card" id="test-<?php echo esc_attr( str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?>" data-rail-item>
										<div class="test-card__top">
											<?php if ( $image ) : ?><img class="test-card__image" src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy" decoding="async"><?php endif; ?>
											<span class="test-card__number"><?php echo esc_html( str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ) . '/' ); ?></span>
										</div>
										<div class="test-card__bottom">
											<h3 class="test-card__title"><?php echo esc_html( $title ); ?></h3>
											<a class="action action--primary action--icon action--card-control test-card__action" href="#prices" aria-label="<?php echo esc_attr( $title ); ?>"><span class="action__label"><span class="action__arrow action__arrow--card" aria-hidden="true"></span></span></a>
										</div>
									</article>
								<?php endforeach; ?>
							</div>
							<article class="enquiry-card">
								<h3 class="enquiry-card__title"><?php echo esc_html( get_field( 'service_test_cta_title', $post_id ) ?: 'Не нашли нужное испытание?' ); ?></h3>
								<p class="enquiry-card__text"><?php echo esc_html( get_field( 'service_test_cta_text', $post_id ) ?: 'Подберём метод испытаний под задачи вашего проекта' ); ?></p>
								<div class="enquiry-card__actions"><a class="action action--inverse" data-dialog-open data-purpose="test" data-record-id="<?php echo esc_attr( (string) $post_id ); ?>" href="#request"><span class="action__marker" aria-hidden="true"></span><span class="action__label">Обсудить проект</span></a></div>
							</article>
						</div>
					</div>
				<?php endif; ?>
			</div>

			<?php
			$scenery_desktop = arcticrc_image_url( get_field( 'service_scenery_desktop', $post_id ) );
			$scenery_mobile  = arcticrc_image_url( get_field( 'service_scenery_mobile', $post_id ) );
			if ( $scenery_desktop || $scenery_mobile ) :
				?>
				<div class="page__scenery" aria-hidden="true">
					<picture>
						<?php if ( $scenery_mobile ) : ?><source media="(max-width: 600px)" srcset="<?php echo esc_url( $scenery_mobile ); ?>"><?php endif; ?>
						<?php if ( $scenery_desktop ) : ?><img class="page__scenery-image" src="<?php echo esc_url( $scenery_desktop ); ?>" alt="" loading="lazy" decoding="async"><?php endif; ?>
					</picture>
				</div>
			<?php endif; ?>
		</section>

		<div class="page__container">
			<?php if ( arcticrc_service_block_visible( 'service_prices_visible', $post_id ) ) : ?>
				<section class="page__section page__section--prices" id="prices">
					<div class="page-heading page-heading--section"><h2 class="page-heading__title"><?php echo esc_html( get_field( 'service_prices_title', $post_id ) ?: 'Стоимость услуг' ); ?></h2></div>
					<div class="page__section-content">
						<div class="price-list">
							<?php foreach ( (array) $price_groups as $group_index => $group ) : ?>
								<section class="price-list__group">
									<div class="price-list__heading<?php echo $group_index > 1 ? ' price-list__heading--inset' : ''; ?>">
										<span class="price-list__number" aria-hidden="true"><?php echo esc_html( str_pad( (string) ( $group_index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
										<h3 class="price-list__group-title"><?php echo esc_html( $group['title'] ?? '' ); ?></h3>
										<a class="action action--text" href="#request" data-dialog-open data-purpose="price" data-record-id="<?php echo esc_attr( (string) $post_id ); ?>"><span class="action__marker" aria-hidden="true"></span><span class="action__label">Подробнее</span></a>
									</div>
									<div class="price-list__rows">
										<?php foreach ( (array) ( $group['rows'] ?? array() ) as $row_index => $row ) : ?>
											<div class="price-list__row">
												<span class="price-list__code"><?php echo esc_html( str_pad( (string) ( $group_index + 1 ), 2, '0', STR_PAD_LEFT ) . '.' . str_pad( (string) ( $row_index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
												<div class="price-list__description"><h3 class="price-list__title"><?php echo esc_html( $row['title'] ?? '' ); ?></h3><?php if ( ! empty( $row['detail'] ) ) : ?><p class="price-list__detail"><?php echo esc_html( $row['detail'] ); ?></p><?php endif; ?></div>
												<span class="price-list__price"><?php echo esc_html( $row['price'] ?? '' ); ?></span>
												<a class="action action--primary action--icon price-list__action" href="#request" data-dialog-open data-purpose="price" data-record-id="<?php echo esc_attr( (string) $post_id ); ?>" aria-label="<?php echo esc_attr( $row['title'] ?? 'Оставить заявку' ); ?>"><span class="action__label"><span class="action__arrow action__arrow--card" aria-hidden="true"></span></span></a>
											</div>
										<?php endforeach; ?>
									</div>
								</section>
							<?php endforeach; ?>
						</div>
					</div>
				</section>
			<?php endif; ?>

			<?php if ( arcticrc_service_block_visible( 'service_company_visible', $post_id ) ) : ?>
				<section class="page__section page__section--company" id="company">
					<div class="page-heading page-heading--section page-heading--company"><h2 class="page-heading__title"><?php echo esc_html( get_field( 'service_company_title', $post_id ) ?: 'Не обещания, а система, которая работает' ); ?></h2></div>
					<div class="page__section-content">
						<div class="company-proof">
							<div class="company-proof__features">
								<?php foreach ( (array) $features as $index => $feature ) :
									$desktop = arcticrc_image_url( $feature['desktop_image'] ?? '' );
									$mobile = arcticrc_image_url( $feature['mobile_image'] ?? '' );
									?>
									<article class="company-proof__feature <?php echo 0 === $index ? 'company-proof__feature--equipment' : 'company-proof__feature--light'; ?>">
										<?php if ( $desktop || $mobile ) : ?><picture class="company-proof__media<?php echo 0 === $index ? ' company-proof__media--equipment' : ''; ?>"><?php if ( $mobile ) : ?><source media="(max-width: 600px)" srcset="<?php echo esc_url( $mobile ); ?>"><?php endif; ?><?php if ( $desktop ) : ?><img class="company-proof__image" src="<?php echo esc_url( $desktop ); ?>" alt="" loading="lazy" decoding="async"><?php endif; ?></picture><?php endif; ?>
										<div class="company-proof__feature-copy">
											<?php if ( ! empty( $feature['stat'] ) ) : ?><strong class="company-proof__stat"><?php echo esc_html( $feature['stat'] ); ?></strong><?php endif; ?>
											<h3 class="company-proof__title company-proof__feature-title<?php echo 0 !== $index ? ' company-proof__feature-title--light' : ''; ?>"><?php echo esc_html( $feature['title'] ?? '' ); ?></h3>
											<p class="company-proof__body company-proof__feature-body"><?php echo esc_html( $feature['body'] ?? '' ); ?></p>
										</div>
									</article>
								<?php endforeach; ?>
							</div>
							<div class="company-proof__credentials">
								<?php foreach ( (array) $credentials as $credential ) :
									$size = $credential['size'] ?? 'compact';
									if ( ! in_array( $size, array( 'compact', 'narrow', 'medium', 'full' ), true ) ) { $size = 'compact'; }
									?>
									<article class="company-proof__credential company-proof__credential--<?php echo esc_attr( $size ); ?>">
										<h3 class="company-proof__title company-proof__credential-title"><?php echo esc_html( $credential['title'] ?? '' ); ?></h3>
										<p class="company-proof__body company-proof__credential-body"><?php echo esc_html( $credential['body'] ?? '' ); ?></p>
									</article>
								<?php endforeach; ?>
							</div>
						</div>
					</div>
				</section>
			<?php endif; ?>
		</div>

		<?php if ( arcticrc_service_block_visible( 'service_projects_visible', $post_id ) && $projects ) : ?>
			<section class="page__section page__section--projects" id="projects">
				<div class="page__section-heading"><div class="page-heading page-heading--section"><h2 class="page-heading__title"><?php echo esc_html( get_field( 'service_projects_title', $post_id ) ?: 'Проекты' ); ?></h2></div></div>
				<div class="page__section-content page__section-content--full">
					<div class="record-rail record-rail--projects" data-record-rail aria-label="Проекты">
						<div class="record-rail__toolbar record-rail__toolbar--projects" data-rail-controls>
							<span class="record-rail__count"><span class="record-rail__current" data-rail-current>01</span> <span class="record-rail__separator">/</span> <span data-rail-total><?php echo esc_html( str_pad( (string) count( $projects ), 2, '0', STR_PAD_LEFT ) ); ?></span></span>
							<div class="record-rail__buttons"><button class="action action--icon action--outline action--rail" type="button" data-rail-prev aria-label="Предыдущая запись" hidden><span class="action__arrow action__arrow--prev" aria-hidden="true"></span></button><button class="action action--icon action--primary action--rail" type="button" data-rail-next aria-label="Следующая запись" hidden><span class="action__arrow action__arrow--next" aria-hidden="true"></span></button></div>
						</div>
						<div class="record-rail__track" data-rail-track>
							<?php foreach ( $projects as $index => $project ) :
								$image = arcticrc_image_url( $project['image'] ?? '' );
								$title = $project['title'] ?? '';
								?>
								<article class="project-card" id="project-<?php echo esc_attr( str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?>" data-rail-item>
									<?php if ( $image ) : ?><img class="project-card__image" src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy" decoding="async"><?php endif; ?>
									<h3 class="project-card__title"><?php echo esc_html( $title ); ?></h3>
								</article>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
			</section>
		<?php endif; ?>

		<?php if ( arcticrc_service_block_visible( 'service_contact_visible', $post_id ) ) : ?>
			<div class="page__container">
				<section class="enquiry-section" id="request" aria-label="Связаться с нами">
					<div class="contact-details contact-details--shared">
						<div class="contact-details__heading"><div class="page-heading page-heading--section"><h2 class="page-heading__title"><?php echo esc_html( get_field( 'service_contact_title', $post_id ) ?: 'Начнем с разговора — доведем до результата' ); ?></h2></div></div>
						<p class="contact-details__intro"><?php echo esc_html( get_field( 'service_contact_intro', $post_id ) ?: '' ); ?></p>
						<div class="contact-details__rows">
							<div class="contact-details__row"><div class="contact-details__value"><span class="contact-details__label">телефон</span><a class="contact-details__link" href="<?php echo esc_url( 'tel:' . arcticrc_phone_href( $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a></div><div class="contact-details__actions"><?php foreach ( $socials as $social ) : ?><a class="action action--outline action--compact action--social contact-details__action contact-details__action--social" href="<?php echo esc_url( $social['url'] ); ?>" aria-label="<?php echo esc_attr( $social['name'] ); ?>"><span class="action__marker contact-details__channel-marker" aria-hidden="true"></span><span class="action__label"><?php if ( $social['icon'] ) : ?><img class="contact-details__channel-icon" src="<?php echo esc_url( $social['icon'] ); ?>" alt=""><?php endif; ?><span class="contact-details__channel-label"><?php echo esc_html( $social['name'] ); ?></span></span></a><?php endforeach; ?></div></div>
							<div class="contact-details__row"><div class="contact-details__value"><span class="contact-details__label">почта</span><a class="contact-details__link" href="<?php echo esc_url( 'mailto:' . sanitize_email( $email ) ); ?>"><?php echo esc_html( $email ); ?></a></div><div class="contact-details__actions"><a class="action action--outline action--compact contact-details__action" href="<?php echo esc_url( 'mailto:' . sanitize_email( $email ) ); ?>"><span class="action__marker" aria-hidden="true"></span><span class="action__label">Написать на почту</span></a></div></div>
							<div class="contact-details__row"><div class="contact-details__value"><span class="contact-details__label">центральный офис</span><?php echo esc_html( $address ); ?></div><div class="contact-details__actions"><a class="action action--outline action--compact contact-details__action" href="<?php echo esc_url( home_url( '/contacts/#map' ) ); ?>"><span class="action__marker" aria-hidden="true"></span><span class="action__label">Показать на карте</span></a></div></div>
						</div>
					</div>
					<?php echo arcticrc_cf7_form_html( 'consultation', 'consultation', 'enquiry-form enquiry-form--consultation enquiry-section__form' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</section>
			</div>
		<?php endif; ?>
	</main>
	<?php
endwhile;

get_footer();
