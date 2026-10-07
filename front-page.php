<?php
defined( 'ABSPATH' ) || exit;

get_header();
?>
<main class="page__main" id="main">
	<?php
	while ( have_posts() ) :
		the_post();

		/*
		 * Следующий этап: перенос секций html/index.html в template-parts/home/*
		 * с сохранением существующей BEM-разметки и привязкой к ACF/CPT.
		 */
		the_content();
	endwhile;
	?>
</main>
<?php get_footer(); ?>
