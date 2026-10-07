<?php
defined( 'ABSPATH' ) || exit;

$phone             = arcticrc_option( 'site_phone', '+7(916)-616-02-20' );
$email             = arcticrc_option( 'site_email', 'engineering@arcticrc.ru' );
$legal_address     = arcticrc_option( 'site_legal_address', 'г. Москва, ул Михайлова, д 31А, кв 404' );
$company           = arcticrc_option( 'site_company_name', 'ООО "ГК ЦЕНТР АРКТИЧЕСКИХ ИЗЫСКАНИЙ"' );
$inn               = arcticrc_option( 'site_inn', '9721265458' );
$kpp               = arcticrc_option( 'site_kpp', '772101001' );
$copyright         = arcticrc_option( 'site_copyright', 'Все права защищены' );
$privacy_url       = arcticrc_option( 'site_privacy_url', home_url( '/policy/' ) );
$personal_data_url = arcticrc_option( 'site_personal_data_url', home_url( '/privacy/' ) );
$navigation_items  = arcticrc_menu_items( 'footer_navigation' );
$direction_items   = arcticrc_menu_items( 'footer_directions' );

$identity_lines = array_filter(
	array(
		$company,
		$inn ? 'ИНН ' . $inn : '',
		$kpp ? 'КПП ' . $kpp : '',
		$legal_address,
	)
);
?>
<?php if ( arcticrc_header_is_media() ) : ?>
</div>
<?php endif; ?>
<footer class="site-footer">
	<picture>
		<source media="(max-width: 740px)" srcset="<?php echo esc_url( arcticrc_asset_url( 'media/web/footer-background-mobile.webp' ) ); ?>">
		<img class="site-footer__background" src="<?php echo esc_url( arcticrc_asset_url( 'media/web/footer-background-desktop.webp' ) ); ?>" alt="" loading="lazy" decoding="async">
	</picture>
	<div class="site-footer__inner">
		<div class="site-footer__branding">
			<a class="site-footer__brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<img class="site-footer__logo" src="<?php echo esc_url( arcticrc_asset_url( 'media/web/logo-100-5500.svg' ) ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" width="416" height="122" loading="lazy">
			</a>
			<div class="site-footer__identity"><?php echo nl2br( esc_html( implode( "\n", $identity_lines ) ) ); ?></div>
		</div>
		<a class="site-footer__phone" href="<?php echo esc_url( 'tel:' . arcticrc_phone_href( $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a>
		<a class="site-footer__email" href="<?php echo esc_url( 'mailto:' . sanitize_email( $email ) ); ?>"><?php echo esc_html( $email ); ?></a>
		<nav class="site-footer__navigation" aria-label="Навигация в подвале">
			<p class="site-footer__label">навигация</p>
			<?php foreach ( $navigation_items as $menu_item ) : ?>
				<a class="site-footer__link" href="<?php echo esc_url( $menu_item->url ); ?>"><?php echo esc_html( $menu_item->title ); ?></a>
			<?php endforeach; ?>
		</nav>
		<nav class="site-footer__directions" aria-label="Направления">
			<p class="site-footer__label">направления</p>
			<?php foreach ( $direction_items as $menu_item ) : ?>
				<a class="site-footer__link" href="<?php echo esc_url( $menu_item->url ); ?>"><?php echo esc_html( $menu_item->title ); ?></a>
			<?php endforeach; ?>
		</nav>
		<div class="site-footer__legal">
			<a class="site-footer__legal-link" href="<?php echo esc_url( $personal_data_url ); ?>">Соглашение на обработку персональных данных</a>
			<a class="site-footer__legal-link" href="<?php echo esc_url( $privacy_url ); ?>">Политика конфиденциальности</a>
			<span>© <?php echo esc_html( wp_date( 'Y' ) . ' ' . $copyright ); ?></span>
		</div>
	</div>
</footer>
<?php
$dialog_variant = 'consultation';
$dialog_purpose = 'consultation';

if ( is_page( 'equipment-rent' ) ) {
	$dialog_variant = 'equipment';
	$dialog_purpose = 'rent';
} elseif ( is_page( 'equipment-sale' ) ) {
	$dialog_variant = 'equipment';
	$dialog_purpose = 'sale';
}
?>
<dialog class="enquiry-dialog<?php echo 'equipment' === $dialog_variant ? ' enquiry-dialog--equipment' : ''; ?>" id="enquiry-dialog" aria-labelledby="modal-title">
	<div class="enquiry-dialog__panel" data-dialog-panel>
		<button class="enquiry-dialog__close" type="button" data-dialog-close aria-label="Закрыть форму">×</button>
		<?php
		echo arcticrc_cf7_form_html(
			$dialog_variant,
			$dialog_purpose,
			'enquiry-form ' . ( 'equipment' === $dialog_variant ? 'enquiry-form--equipment' : 'enquiry-form--consultation' )
		); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		?>
	</div>
</dialog>
<div class="scroll-return" hidden>
	<button class="action action--icon action--primary scroll-return__button" type="button" aria-label="Наверх к шапке" title="Наверх к шапке" aria-disabled="true">
		<span class="scroll-return__arrow scroll-return__arrow--up" aria-hidden="true"></span>
	</button>
</div>
<?php wp_footer(); ?>
</body>
</html>
