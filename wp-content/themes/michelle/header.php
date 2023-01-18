<?php
/**
 * The header for our theme.
 *
 * @link  https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  1.3.0
 */

namespace WebManDesign\Michelle;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

do_action( 'tha_html_before' );

?>

<html <?php language_attributes(); Tool\AMP::the_atts( 'html' ); ?>>


<head>

<?php

do_action( 'tha_head_top' );
do_action( 'tha_head_bottom' );

wp_head();

?>

</head>


<body <?php body_class(); ?>>

<?php

wp_body_open();

do_action( 'tha_body_top' );

do_action( 'tha_header_before' );
do_action( 'tha_header_top' );
do_action( 'tha_header_bottom' );
do_action( 'tha_header_after' );

do_action( 'tha_content_before' );
do_action( 'tha_content_top' );
