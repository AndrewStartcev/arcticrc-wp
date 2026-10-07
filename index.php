<?php
defined( 'ABSPATH' ) || exit;

get_header();
?>
<main class="page" id="main">
	<div class="page__container">
		<?php while ( have_posts() ) : the_post(); ?>
			<article <?php post_class(); ?>>
				<div class="page-heading">
					<h1 class="page-heading__title"><?php the_title(); ?></h1>
				</div>
				<?php the_content(); ?>
			</article>
		<?php endwhile; ?>
	</div>
</main>
<?php get_footer(); ?>
