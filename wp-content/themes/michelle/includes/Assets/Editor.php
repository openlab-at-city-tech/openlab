<?php
/**
 * Editor assets component.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  1.3.11
 */

namespace WebManDesign\Michelle\Assets;

use WebManDesign\Michelle\Component_Interface;
use WebManDesign\Michelle\Customize\Styles;
use WebManDesign\Michelle\Tool\Google_Fonts;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class Editor implements Component_Interface {

	/**
	 * Initialization.
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public static function init() {

		// Processing

			// Actions

				add_action( 'init', __CLASS__ . '::setup_classic_editor' );

				add_action( 'enqueue_block_editor_assets', __CLASS__ . '::enqueue_block_styles' );

	} // /init

	/**
	 * Setup classic editor.
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public static function setup_classic_editor() {

		// Variables

			/**
			 * The `0` key is reserved for WordPress.
			 * WordPress duplicates this stylesheet and makes an RTL version for it.
			 * This is obviously not ideal when we enqueue multiple stylesheets and
			 * want RTL for all of those. That's why we make our own RTL versions below.
			 * @see   add_editor_style()
			 * @link  https://developer.wordpress.org/reference/functions/add_editor_style/
			 */
			$styles = array( 0 => '' );

			$suffix = '';
			if ( is_rtl() ) {
				$suffix .= '-rtl';
			}

			$styles[10] = esc_url_raw( add_query_arg(
				'ver',
				'v' . MICHELLE_THEME_VERSION,
				get_theme_file_uri( 'assets/css/editor-style-classic' . $suffix . '.css' )
			) );

			/**
			 * Filters classic editor stylesheet files setup array.
			 *
			 * @since  1.0.0
			 *
			 * @param  array $styles
			 */
			$styles = (array) apply_filters( 'michelle/assets/editor/setup_classic_editor/styles', $styles );


		// Processing

			ksort( $styles );

			add_editor_style( $styles );

	} // /setup_classic_editor

	/**
	 * Enqueues block editor stylesheets.
	 *
	 * Unfortunately, we can not really use
	 *   add_theme_support( 'editor-styles' );
	 *   add_editor_style( [ 'editor-styles.css', 'google-fonts.css' ] );
	 * as that way there is no way to separate classic and block editor
	 * stylesheets (yes, all `add_editor_style()` stylesheets are being
	 * enqueued in both editors, plus block editor will replace certain
	 * selectors and wrap everything in `.editor-styles-wrapper`.)
	 *
	 * @since    1.0.0
	 * @version  1.3.11
	 *
	 * @return  void
	 */
	public static function enqueue_block_styles() {

		// Processing

			Factory::style_enqueue( array(
				'handle'   => 'michelle-editor-blocks',
				'src'      => get_theme_file_uri( 'assets/css/editor-style-blocks.css' ),
				'add_data' => array(
					'rtl'      => 'replace',
					'precache' => true,
				),
				'inline'   => array(
					'customize-styles-editor' =>
						Google_Fonts::get_stylesheet_content() . PHP_EOL . PHP_EOL
						. Styles::get_css_variables(),
				),
			) );

	} // /enqueue_block_styles

}
