<?php
/**
 * The main archive template file
 *
 * @package kadence
 */

namespace Kadence;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

kadence()->print_styles( 'kadence-content' );
/**
 * Hook for main archive content.
 */
do_action( 'kadence_archive' );

get_footer();
