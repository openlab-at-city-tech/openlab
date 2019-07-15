<?php
/**
 * Multipage Component Widgets.
 *
 * @package Multipage
 * @subpackage Widgets
 * @since 1.4
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Register Multipage widgets.
 *
 * @since 1.4
 */
function mpp_register_widgets() {
	register_widget( 'MPP_Table_of_Contents_Widget' );
}
//add_action( 'widgets_init', 'mpp_register_widgets' );
