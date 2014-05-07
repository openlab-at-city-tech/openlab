<?php
/*
Plugin Name: Google Docs Shortcode
Plugin URI: https://github.com/cuny-academic-commons/google-docs-shortcode
Description: Easily embed a Google Doc into your blog posts
Author: r-a-y
Author URI: http://buddypress.org/community/members/r-a-y/
Version: 0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Shortcode to embed a Google Doc.
 *
 * eg. [gdoc link="https://docs.google.com/document/pub?id=XXX"]
 */
function ray_google_docs_shortcode( $atts ) {
	global $content_width;

	extract( shortcode_atts( array(
		'link'     => false,

		// dimensions
		'width'    => ! empty( $content_width ) ? $content_width : '100%',
		'height'   => 300,   // default height is set to 300

		// only for documents
		'seamless' => 1,  // if set to 'true', this will not show the Google Docs header / footer.
		                  // if set to 'false', this will show the Google Docs header / footer.

		// only for presentations
		'size'     => false, // preset presentation size, either 'small', 'medium' or 'large';
		                     // preset dimensions are as follows: small (480x389), medium (960x749), large (1440x1109)
		                     // to set custom size, set the 'width' and 'height' params instead

	), $atts ) );

	// if no link or link is not from Google Docs, stop now!
	if ( ! $link || strpos( $link, '://docs.google.com' ) === false )
		return;

	$type = $extra = false;

	// set the doc type by looking at the URL

	// document
	if ( strpos( $link, '/document/' ) !== false ) {
		$type = 'doc';

	// presentation
	} elseif ( strpos( $link, '/presentation/' ) !== false || strpos( $link, '/present/' ) !== false ) {
		$type = 'presentation';

	// form
	} elseif ( strpos( $link, '/forms/' ) !== false || strpos( $link, 'form?formkey' ) !== false ) {
		$type = 'form';

	// spreadsheet
	} elseif ( strpos( $link, '/spreadsheet/' ) !== false ) {
		$type = 'spreadsheet';

	// nada!
	} else {
		return;
	}

	// add query args depending on doc type
	switch ( $type ) {
		case 'doc' :
			if ( (int) $seamless === 1 )
				$link = add_query_arg( 'embedded', 'true', $link );

			break;

		case 'presentation' :
			// alter the link so we're in embed mode
			// (older docs)
			$link = str_replace( '/view', '/embed', $link );

			// alter the link so we're in embed mode
			$link = str_replace( 'pub?', 'embed?', $link );

			// dimensions
			switch ( $size ) {
				case 'medium' :
					$width  = 960;
					$height = 749;

					break;

				case 'large' :
					$width  = 1440;
					$height = 1109;

					break;

				case 'small' :
				default :
					$width  = 480;
					$height = 389;

					break;
			}

			// extra iframe args
			// i'm aware that these are non-standard attributes in XHTML / HTML5,
			// but these are the attributes given by Google's embed code!
			// don't like this? use the 'ray_google_docs_shortcode_output' filter to remove it!
			$extra = ' frameborder="0" allowfullscreen="true" mozallowfullscreen="true" webkitallowfullscreen="true"';

			break;

		case 'form' :
			// new form format
			if ( strpos( $link, '/forms/' ) !== false ) {
				$link = str_replace( 'viewform', 'viewform?embedded=true', $link );

			// older form format
			} else {
				$link = str_replace( 'viewform?', 'embeddedform?', $link );
			}

			// extra iframe args
			// i'm aware that these are non-standard attributes in XHTML / HTML5,
			// but these are the attributes given by Google's embed code!
			// don't like this? use the 'ray_google_docs_shortcode_output' filter to remove it!
			$extra = ' frameborder="0" marginheight="0" marginwidth="0"';

			break;

		case 'spreadsheet' :
			$link = add_query_arg( 'widget', 'true', $link );

			// extra iframe args
			// i'm aware that these are non-standard attributes in XHTML / HTML5,
			// but these are the attributes given by Google's embed code!
			// don't like this? use the 'ray_google_docs_shortcode_output' filter to remove it!
			$extra = ' frameborder="0"';

			break;

	}

	// set width
	$width = ' width="' . esc_attr( $width ) . '"';

	// set height
	$height = ' height="' . esc_attr( $height ) . '"';

	// finally, embed the google doc!
	$output = '<iframe id="gdoc-' . md5( $link ) . '" class="gdocs_shortcode gdocs_' . esc_attr( $type ) . '" src="' .  esc_url( $link ) . '"' . $width . $height . $extra . '></iframe>';

	return apply_filters( 'ray_google_docs_shortcode_output', $output, $type );
}
add_shortcode( 'gdoc', 'ray_google_docs_shortcode' );
