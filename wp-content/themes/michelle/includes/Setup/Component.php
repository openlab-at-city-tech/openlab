<?php
/**
 * Theme setup component.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since  1.0.0
 */

namespace WebManDesign\Michelle\Setup;

use WebManDesign\Michelle\Component_Interface;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class Component implements Component_Interface {

	/**
	 * Initialization.
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public static function init() {

		// Processing

			// Theme upgrade action.
			Upgrade::init();
			// Media setup.
			Media::init();
			// Post editor setup.
			Editor::init();

			// Actions

				add_action( 'after_setup_theme', __CLASS__ . '::content_width', 0 );
				add_action( 'after_setup_theme', __CLASS__ . '::after_setup_theme' );

	} // /init

	/**
	 * Theme setup.
	 *
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public static function after_setup_theme() {

		// Processing

			// Make theme available for translation.
			load_theme_textdomain( 'michelle', get_template_directory() . '/languages' );

			// Let WordPress manage the document title.
			add_theme_support( 'title-tag' );

			// Add default posts and comments RSS feed links to head.
			add_theme_support( 'automatic-feed-links' );

			// Add support for core custom logo.
			add_theme_support( 'custom-logo', array(
				'unlink-homepage-logo' => true,
			) );

			// Responsive embedded content.
			add_theme_support( 'responsive-embeds' );

			// Switch default core markup for to output valid HTML5.
			add_theme_support( 'html5', array(
				'caption',
				'comment-form',
				'comment-list',
				'gallery',
				'navigation-widgets',
				'search-form',
				'script',
				'style',
			) );

			// Custom background.
			add_theme_support( 'custom-background', array(
				'default-color' => 'ffffff',
			) );

			/**
			 * Declare support for child theme stylesheet automatic enqueuing.
			 * @link  https://github.com/webmandesign/child-theme
			 */
			add_theme_support( 'child-theme-stylesheet' );

	} // /after_setup_theme

	/**
	 * Sets the $content_width in pixels, based on the theme design.
	 *
	 * $content_width variable defines the maximum allowed width for images,
	 * videos, and oEmbeds displayed within a theme.
	 *
	 * @since  1.0.0
	 *
	 * @global  int $content_width
	 *
	 * @return  void
	 */
	public static function content_width() {

		// Processing

			/**
			 * We cannot use WebManDesign\Michelle\Customize\Mod::get() here as we are setting
			 * these before the actual theme options are declared.
			 */
			$content_width = absint( get_theme_mod( 'layout_width_content', 1400 ) );

			// Allow filtering.
			$GLOBALS['content_width'] = absint(
				/**
				 * Filters WordPress $content_width global variable.
				 *
				 * @since  1.0.0
				 *
				 * @param  int $content_width
				 */
				apply_filters( 'michelle/setup/content_width', $content_width )
			);

	} // /content_width

}
