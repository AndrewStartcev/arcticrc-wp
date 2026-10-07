<?php
defined( 'ABSPATH' ) || exit;

get_header();
?>
<main class="page page--404" id="main">
	<div class="page__container">
		<div class="page-heading"><h1 class="page-heading__title">Страница не найдена</h1></div>
		<p>Возможно, адрес изменился или страница была удалена.</p>
		<a class="action action--primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><span class="action__marker" aria-hidden="true"></span><span class="action__label">На главную</span></a>
	</div>
</main>
<?php get_footer(); ?>
