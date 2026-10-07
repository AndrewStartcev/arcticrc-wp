<?php
defined( 'ABSPATH' ) || exit;

foreach ( array(
	'/functions/theme-support.php',
	'/functions/assets.php',
	'/functions/acf.php',
	'/functions/helpers.php',
	'/functions/post-types.php',
	'/functions/breadcrumbs.php',
	'/functions/contact-form.php',
	'/functions/base-content.php',
	'/functions/remove-functions.php',
) as $arcticrc_file ) {
	require_once get_template_directory() . $arcticrc_file;
}
