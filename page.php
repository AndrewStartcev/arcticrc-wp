<?php
defined( 'ABSPATH' ) || exit;

get_header();
?>
<main class="page" id="main">
	<div class="page__container">
		<?php while ( have_posts() ) : the_post(); ?>
			<nav class="breadcrumbs" aria-label="Хлебные крошки">
				<a class="breadcrumbs__link" href="<?php echo esc_url( home_url( '/' ) ); ?>">Главная</a>
				<span aria-hidden="true">/</span>
				<span aria-current="page"><?php the_title(); ?></span>
			</nav>
			<div class="page-heading">
				<h1 class="page-heading__title"><?php the_title(); ?></h1>
			</div>
			<?php the_content(); ?>
		<?php endwhile; ?>
	</div>
</main>
<?php get_footer(); ?>
