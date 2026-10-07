<?php
defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	arcticrc_render_seeded_post_content();
endwhile;

get_footer();
