<?php
defined( 'ABSPATH' ) || exit;

get_header();
?>
<main class="page" id="main">
	<div class="page__container">
		<?php while ( have_posts() ) : the_post(); ?>
			<?php arcticrc_breadcrumbs(); ?>
			<div class="page-heading">
				<h1 class="page-heading__title"><?php the_title(); ?></h1>
			</div>
			<?php the_content(); ?>
		<?php endwhile; ?>
	</div>
</main>
<?php get_footer(); ?>
