<?php
defined( 'ABSPATH' ) || exit;

$phone         = arcticrc_option( 'site_phone', '+7(916)-616-02-20' );
$telegram_url  = arcticrc_option( 'site_telegram_url', 'https://telegram.org/' );
$whatsapp_url  = arcticrc_option( 'site_whatsapp_url', 'https://www.whatsapp.com/' );
$is_overlay    = arcticrc_header_is_media();
$logo          = arcticrc_header_logo();
$primary_items = arcticrc_menu_items( 'primary' );
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'site' ); ?>>
<?php wp_body_open(); ?>
<a class="site__skip" href="#main">К содержимому</a>
<?php if ( $is_overlay ) : ?>
<div class="<?php echo esc_attr( arcticrc_overlay_page_class() ); ?>">
	<div class="page__header page__header--overlay">
<?php endif; ?>
<header class="site-header <?php echo $is_overlay ? 'site-header--on-media' : 'site-header--on-light'; ?>">
	<a class="site-header__brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="Главная">
		<img class="site-header__logo" src="<?php echo esc_url( arcticrc_asset_url( $logo ) ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" width="240" height="73">
	</a>
	<details class="site-nav" data-site-nav>
		<summary class="site-nav__toggle<?php echo $is_overlay ? ' site-nav__toggle--light' : ''; ?>" aria-label="Открыть или закрыть навигацию"><span class="site-nav__bars" aria-hidden="true"></span></summary>
		<nav class="site-nav__panel<?php echo $is_overlay ? '' : ' site-nav__panel--solid'; ?>" aria-label="Основная навигация">
			<div class="site-nav__links">
				<?php if ( $primary_items ) : ?>
					<?php foreach ( $primary_items as $menu_item ) : ?>
						<a class="site-nav__link" href="<?php echo esc_url( $menu_item->url ); ?>"><?php echo esc_html( $menu_item->title ); ?></a>
					<?php endforeach; ?>
				<?php else : ?>
					<a class="site-nav__link" href="<?php echo esc_url( home_url( '/services/' ) ); ?>">Услуги</a>
					<a class="site-nav__link" href="<?php echo esc_url( home_url( '/equipment/' ) ); ?>">Оборудование</a>
					<a class="site-nav__link" href="<?php echo esc_url( home_url( '/projects/' ) ); ?>">Проекты<span class="site-nav__projects-marker" aria-hidden="true"></span></a>
					<a class="site-nav__link" href="<?php echo esc_url( home_url( '/#documents' ) ); ?>">Документация</a>
				<?php endif; ?>
			</div>
			<a class="site-nav__phone" href="<?php echo esc_url( 'tel:' . arcticrc_phone_href( $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a>
			<div class="site-nav__actions">
				<a class="action <?php echo $is_overlay ? 'action--light' : 'action--outline'; ?> action--compact action--navigation site-nav__project-action" data-dialog-open data-purpose="consultation" data-record-id="" href="#request"><span class="action__label">Обсудить проект</span></a>
				<?php if ( $telegram_url ) : ?>
					<a class="action <?php echo $is_overlay ? 'action--light' : 'action--outline'; ?> action--icon action--social action--navigation-icon" href="<?php echo esc_url( $telegram_url ); ?>" aria-label="Telegram"><span class="action__label"><img class="action__image" src="<?php echo esc_url( arcticrc_asset_url( 'media/web/social-telegram.svg' ) ); ?>" alt=""></span></a>
				<?php endif; ?>
				<?php if ( $whatsapp_url ) : ?>
					<a class="action <?php echo $is_overlay ? 'action--light' : 'action--outline'; ?> action--icon action--social action--navigation-icon" href="<?php echo esc_url( $whatsapp_url ); ?>" aria-label="WhatsApp"><span class="action__label"><img class="action__image" src="<?php echo esc_url( arcticrc_asset_url( 'media/web/social-whatsapp.svg' ) ); ?>" alt=""></span></a>
				<?php endif; ?>
			</div>
		</nav>
	</details>
</header>
<?php if ( $is_overlay ) : ?>
	</div>
<?php endif; ?>
