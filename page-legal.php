<?php
/**
 * Template Name: Юридическая страница
 * Template Post Type: page
 *
 * @package ArcticRC
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<main class="page page--legal" id="main">
		<div class="page__container">
			<?php arcticrc_breadcrumbs(); ?>
			<article class="legal-content">
				<?php the_content(); ?>
			</article>
		</div>
	</main>
	<?php
endwhile;

get_footer();
