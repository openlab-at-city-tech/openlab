<?php // phpcs:ignore Class file names should be based on the class name with "class-" prepended.
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The common bothend functionality of the theme.
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/codersantosh
 * @since      1.0.0
 *
 * @package    Patterns_Docs
 * @subpackage Patterns_Docs/includes
 */

/**
 * The common bothend functionality of the theme.
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @since      1.0.0
 * @package    Patterns_Docs
 * @subpackage Patterns_Docs/includes
 * @author     codersantosh <codersantosh@gmail.com>
 */
class Patterns_Docs_Include {

	/**
	 * Empty Constructor
	 */
	private function __construct() {}

	/**
	 * Gets an instance of this object.
	 * Prevents duplicate instances which avoid artefacts and improves performance.
	 *
	 * @static
	 * @access public
	 * @return object
	 * @since 1.0.0
	 */
	public static function get_instance() {
		// Store the instance locally to avoid private static replication.
		static $instance = null;

		// Only run these methods if they haven't been ran previously.
		if ( null === $instance ) {

			$instance = new self();
		}

		// Always return the instance.
		return $instance;
	}

	/**
	 * Initialize the class.
	 *
	 * @access public
	 * @return void
	 */
	public function run() {

		add_action( 'init', array( $this, 'register_scripts_and_styles' ) );
		add_action( 'after_setup_theme', array( $this, 'setup_theme' ) );
	}

	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	public function setup_theme() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 */
		load_theme_textdomain( 'patterns-docs', PATTERNS_DOCS_PATH . 'languages' );
	}

	/**
	 * Get the settings with caching.
	 *
	 * @access public
	 * @param string $key optional meta key.
	 * @return array|null
	 */
	public function get_settings( $key = '' ) {
		static $cache = null;
		if ( ! $cache ) {
			$cache = patterns_docs_get_options();
		}
		if ( ! empty( $key ) ) {
			return isset( $cache[ $key ] ) ? $cache[ $key ] : false;
		}

		return $cache;
	}

	/**
	 * Get options related to user meta with caching.
	 *
	 * @access public
	 * @param int    $user_id User ID.
	 * @param string $key optional meta key.
	 * @return mixed All Meta Value related to the theme only.
	 */
	public function get_user_meta( $user_id, $key = '' ) {
		static $cache = array();

		if ( ! isset( $cache[ $user_id ] ) ) {
			$options           = patterns_docs_get_user_meta( $user_id );
			$cache[ $user_id ] = $options;
		}

		if ( ! empty( $key ) ) {
			return isset( $cache[ $user_id ][ $key ] ) ? $cache[ $user_id ][ $key ] : false;
		}

		return $cache[ $user_id ];
	}

	/**
	 * Register scripts and styles
	 *
	 * @since    1.0.0
	 * @access   public
	 * @return void
	 */
	public function register_scripts_and_styles() {
		/* Atomic css */
		wp_register_style( 'atomic', PATTERNS_DOCS_URL . 'assets/library/atomic-css/atomic.min.css', array(), PATTERNS_DOCS_VERSION );
	}
}

if ( ! function_exists( 'patterns_docs_include' ) ) {
	/**
	 * Return instance of  Patterns_Docs_Include class
	 *
	 * @since 1.0.0
	 *
	 * @return Patterns_Docs_Include
	 */
	function patterns_docs_include() {//phpcs:ignore
		return Patterns_Docs_Include::get_instance();
	}
	patterns_docs_include()->run();
}
