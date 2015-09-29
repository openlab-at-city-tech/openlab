<?php
/*
Plugin Name: Google Docs Shortcode
Plugin URI: https://github.com/cuny-academic-commons/google-docs-shortcode
Description: Easily embed a Google Doc into your blog posts
Author: r-a-y
Author URI: http://profiles.wordpress.org/r-a-y
Version: 0.3
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

	$r = shortcode_atts( array(
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

	), $atts );

	// if no link or link is not from Google Docs, stop now!
	if ( ! $r['link'] || strpos( $r['link'], '://docs.google.com' ) === false ) {
		return;
	}

	$type = $extra = false;

	// set the doc type by looking at the URL

	// document
	if ( strpos( $r['link'], '/document/' ) !== false ) {
		$type = 'doc';

	// presentation
	} elseif ( strpos( $r['link'], '/presentation/' ) !== false || strpos( $r['link'], '/present/' ) !== false ) {
		$type = 'presentation';

	// form
	} elseif ( strpos( $r['link'], '/forms/' ) !== false || strpos( $r['link'], 'form?formkey' ) !== false ) {
		$type = 'form';

	// spreadsheet
	} elseif ( strpos( $r['link'], '/spreadsheets/' ) !== false || strpos( $r['link'], '/spreadsheet/' ) !== false ) {
		$type = 'spreadsheet';

	// nada!
	} else {
		return;
	}

	// add query args depending on doc type
	switch ( $type ) {
		case 'doc' :
			if ( (int) $r['seamless'] === 1 ) {
				$r['link'] = add_query_arg( 'embedded', 'true', $r['link'] );
			}

			break;

		case 'presentation' :
			$is_old_doc = strpos( $r['link'], '/present/' ) !== false || strpos( $r['link'], '?id=' ) !== false;

			// alter the link so we're in embed mode
			// (older docs)
			$r['link'] = str_replace( '/view', '/embed', $r['link'] );

			// alter the link so we're in embed mode
			$r['link'] = str_replace( 'pub?', 'embed?', $r['link'] );

			// dimensions
			switch ( $r['size'] ) {
				case 'medium' :
					$r['width']  = 960;

					if ( $is_old_doc ) {
						$r['height'] = 749;
					} else {
						$r['height'] = 559;
					}

					break;

				case 'large' :
					$r['width']  = 1440;

					if ( $is_old_doc ) {
						$r['height'] = 1109;
					} else {
						$r['height'] = 839;
					}

					break;

				case 'small' :
				default :
					$r['width']  = 480;

					if ( $is_old_doc ) {
						$r['height'] = 389;
					} else {
						$r['height'] = 299;
					}

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
			if ( strpos( $r['link'], '/forms/' ) !== false ) {
				$r['link'] = str_replace( 'viewform', 'viewform?embedded=true', $r['link'] );

			// older form format
			} else {
				$r['link'] = str_replace( 'viewform?', 'embeddedform?', $r['link'] );
			}

			// extra iframe args
			// i'm aware that these are non-standard attributes in XHTML / HTML5,
			// but these are the attributes given by Google's embed code!
			// don't like this? use the 'ray_google_docs_shortcode_output' filter to remove it!
			$extra = ' frameborder="0" marginheight="0" marginwidth="0"';

			break;

		case 'spreadsheet' :
			$r['link'] = add_query_arg( 'widget', 'true', $r['link'] );

			// extra iframe args
			// i'm aware that these are non-standard attributes in XHTML / HTML5,
			// but these are the attributes given by Google's embed code!
			// don't like this? use the 'ray_google_docs_shortcode_output' filter to remove it!
			$extra = ' frameborder="0"';

			break;

	}

	// set width
	$r['width'] = ' width="' . esc_attr( $r['width'] ) . '"';

	// set height
	$r['height'] = ' height="' . esc_attr( $r['height'] ) . '"';

	// finally, embed the google doc!
	$output = '<iframe id="gdoc-' . md5( $r['link'] ) . '" class="gdocs_shortcode gdocs_' . esc_attr( $type ) . '" src="' .  esc_url( $r['link'] ) . '"' . $r['width'] . $r['height'] . $extra . '></iframe>';

	return apply_filters( 'ray_google_docs_shortcode_output', $output, $type );
}
add_shortcode( 'gdoc', 'ray_google_docs_shortcode' );
