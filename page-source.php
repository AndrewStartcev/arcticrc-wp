<?php
/**
 * Template Name: Source markup
 *
 * Renders content provisioned from the approved static markup without
 * WordPress altering the BEM structure.
 *
 * @package ArcticRC
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	arcticrc_render_seeded_post_content();
endwhile;

get_footer();
