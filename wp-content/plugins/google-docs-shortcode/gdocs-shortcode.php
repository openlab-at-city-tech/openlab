<?php
/*
Plugin Name: Google Docs Shortcode
Plugin URI: https://github.com/cuny-academic-commons/google-docs-shortcode
Description: Easily embed a Google Doc into your blog posts
Author: r-a-y
Author URI: http://profiles.wordpress.org/r-a-y
Version: 0.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Initializer.
 */
function ray_gdoc_shortcode_init() {
	add_shortcode( 'gdoc', 'ray_google_docs_shortcode' );

	/**
	 * Support for Shortcake.
	 */
	if ( function_exists( 'shortcode_ui_register_for_shortcode' ) ) {
		shortcode_ui_register_for_shortcode(
			'gdoc',
			array(

				'label' => __( 'Google Drive', 'google-docs-shortcode' ),

				'listItemImage' => '<img src="https://developers.google.com/drive/images/drive_icon_mono.png" alt="" />',

				// @todo Figure out how to do conditional attributes to support 'size' and 'seamless'
				'attrs' => array(
					array(
						'label' => __( 'Google Doc Link', 'google-docs-shortcode' ),
						'attr'  => 'link',
						'type'  => 'text',
						'description' => __( 'Paste the published-to-the-web Google Doc link or a publicly-shared Google Doc link here.', 'gdrive' )
					),

					array(
						'label' => __( 'Width', 'google-docs-shortcode' ),
						'attr'  => 'width',
						'type'  => 'number',
						'meta' => array(
							'style' => 'width:75px'
						),
						'description' => __( "Enter width in pixels. If left blank, this defaults to the theme's width.", 'gdrive' )
					),

					array(
						'label' => __( 'Height', 'google-docs-shortcode' ),
						'attr'  => 'height',
						'type'  => 'number',
						'meta' => array(
							'style' => 'width:75px'
						),
						'description' => __( "Enter height in pixels. If left blank, this defaults to 300.", 'gdrive' )
					),

					array(
						'label' => __( 'Type (non-Google Doc only)', 'google-docs-shortcode' ),
						'attr'  => 'type',
						'type' => 'select',
						'options' => array(
							'' => '--',
							'audio' => __( 'Audio', 'google-docs-shortcode' ),
							'other' => __( 'Other (Image, PDF, Microsoft Office, etc.)', 'google-docs-shortcode' ),
						),
						'description' => __( "If your Google Drive item is not a Doc, Slide, Spreadsheet or Form, select the type of item you are embedding.", 'gdrive' )
					),

					// maybe later...
					/*
					array(
						'label' => __( 'Size', 'google-docs-shortcode' ),
						'attr'  => 'size',
						'type' => 'select',
						'options' => array(
							'small'  => __( 'Small - 480 x 299', 'google-docs-shortcode' ),
							'medium' => __( 'Medium - 960 x 559', 'google-docs-shortcode' ),
							'large'  => __( 'Large - 1440 x 839', 'google-docs-shortcode' )
						),
						'description' => __( 'This is only applicable to Google Slides. If you want to set a custom width and height, use the options above.', 'google-docs-shortcode' )
					),
					*/

					// This doesn't quite work yet; commenting out for now
					// @link https://github.com/fusioneng/Shortcake/pull/413
					/*
					array(
						'label' => __( 'Show Doc Header/Footer', 'google-docs-shortcode' ),
						'attr'  => 'seamless',
						'type' => 'checkbox',
						'value' => 0,
						'description' => __( 'This is only applicable to Google Docs.', 'google-docs-shortcode' )
					),
					*/
				),

			)
		);
	}
}
add_action( 'init', 'ray_gdoc_shortcode_init' );

/**
 * Shortcode to embed a Google Doc.
 *
 * eg. [gdoc link="https://docs.google.com/document/pub?id=XXX"]
 */
function ray_google_docs_shortcode( $atts ) {
	global $content_width;

	$r = shortcode_atts( array(
		'link'     => false,

		// type
		'type'     => false,

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
	if ( ! $r['link'] || ( strpos( $r['link'], '://docs.google.com' ) === false && strpos( $r['link'], '://drive.google.com' ) === false ) ) {
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

	// non-google doc
	} elseif ( ! empty( $r['type'] ) ) {
		$type = $r['type'];

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

		// http://webapps.stackexchange.com/a/84399
		case 'audio' :
			$id = str_replace( 'https://drive.google.com/file/d/', '', $r['link'] );
			$id = str_replace( '/view?usp=sharing', '', $id );
			$id = esc_attr( $id );
			break;
	}

	// support "anyone with link" functionality
	if ( false !== strpos( $r['link'], '/edit?usp=sharing' ) ) {
		$r['link'] = str_replace( '/edit?usp=sharing', '/preview', $r['link'] );
		$r['link'] = str_replace( '&widget=true', '', $r['link'] );
		$r['link'] = str_replace( '&embedded=true', '', $r['link'] );
	} elseif ( false !== strpos( $r['link'], '/view?usp=sharing' ) ) {
		$r['link'] = str_replace( '/view?usp=sharing', '/preview', $r['link'] );
	}

	// set width
	$r['width'] = ' width="' . esc_attr( $r['width'] ) . '"';

	// set height
	$r['height'] = ' height="' . esc_attr( $r['height'] ) . '"';

	// audio uses HTML5
	if ( 'audio' === $r['type'] ) {
		$output = "<audio controls>
		<source src='http://docs.google.com/uc?export=open&id={$id}'>
		<p>" . __( 'Your browser does not support HTML5 audio', 'google-docs-shortcode' ) . "</p>
		</audio>";

	// everything else uses an IFRAME
	} else {
		$output = '<iframe id="gdoc-' . md5( $r['link'] ) . '" class="gdocs_shortcode gdocs_' . esc_attr( $type ) . '" src="' .  esc_url( $r['link'] ) . '"' . $r['width'] . $r['height'] . $extra . '></iframe>';
	}

	return apply_filters( 'ray_google_docs_shortcode_output', $output, $type );
}
