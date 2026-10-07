<?php
defined( 'ABSPATH' ) || exit;

get_header();
?>
<main class="page page--404" id="main">
	<div class="page__container">
		<section class="error-page">
			<div class="error-page__code" aria-hidden="true">404</div>
			<div class="error-page__content">
				<p class="error-page__eyebrow">Страница не найдена</p>
				<h1 class="error-page__title">Похоже, такой страницы больше нет</h1>
				<p class="error-page__text">Адрес мог измениться, либо страница была удалена. Можно вернуться на главную или перейти к услугам.</p>
				<div class="error-page__actions">
					<a class="action action--primary" href="<?php echo esc_url( home_url( '/' ) ); ?>">
						<span class="action__marker" aria-hidden="true"></span>
						<span class="action__label">На главную</span>
					</a>
					<a class="action action--outline" href="<?php echo esc_url( home_url( '/services/' ) ); ?>">
						<span class="action__marker" aria-hidden="true"></span>
						<span class="action__label">Смотреть услуги</span>
					</a>
				</div>
			</div>
		</section>
	</div>
</main>
<?php get_footer(); ?>
