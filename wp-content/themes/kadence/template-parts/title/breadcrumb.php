<?php
/**
 * Template part for displaying a post's breadcrumb.
 *
 * @package kadence
 */

namespace Kadence;

$item_type = get_post_type();
$elements  = kadence()->option( $item_type . '_title_element_breadcrumb' );
$args = array( 'show_title' => true );
if ( isset( $elements ) && is_array( $elements ) ) {
	if ( isset( $elements['show_title'] ) && ! $elements['show_title'] ) {
		$args['show_title'] = false;
	}
}
kadence()->print_breadcrumb( $args );