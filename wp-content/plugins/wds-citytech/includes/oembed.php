<?php

/**
 * Adds custom embeds
 */

/**
 * Adds [openprocessing] shortcode
 */
function openlab_openprocessing_shortcode( $atts ) {
	$html = '';

	$errors = array();
	foreach ( array( 'id', 'height', 'width' ) as $key ) {
		$v = isset( $atts[ $key ] ) ? (int) $atts[ $key ] : 0;
		if ( ! $v ) {
			switch ( $key ) {
				case 'id' :
					$errors[] = 'You must provide the numeric id of the openprocessing.org sketch. In a URL like "http://openprocessing.org/sketch/12345", the id is <strong>12345</strong>';
					break;

				default :
					$errors[] = sprintf( 'You didn&#8217;t provide the following necessary attribute: <strong>%s</strong>', $key );
					break;
			}
		}
	}

	if ( empty( $errors ) ) {
		$height = (int) $atts['height'];
		$width = (int) $atts['width'];
		$html = sprintf(
			'<iframe width="%s" height="%s" scrolling="no" frameborder="0" src="http://www.openprocessing.org/sketch/%s/embed/?width=%s&height=%s&border=true"></iframe>',
			$width + 28, // iframe needs padding
			$height + 50,
			(int) $atts['id'],
			$width,
			$height
		);
	} else {
		$estring = '';
		foreach ( $errors as $e ) {
			$estring .= '<li>' . $e . '</li>';
		}

		$html = sprintf( '<em>Your openprocessing.org sketch could not be displayed, because of the following errors: <ul>%s</ul></em>', $estring );
	}

	return $html;
}
add_shortcode( 'openprocessing', 'openlab_openprocessing_shortcode' );
