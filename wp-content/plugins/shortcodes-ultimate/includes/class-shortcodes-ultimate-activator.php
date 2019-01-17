<?php

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since        5.0.0
 * @package      Shortcodes_Ultimate
 * @subpackage   Shortcodes_Ultimate/includes
 */
class Shortcodes_Ultimate_Activator {

	/**
	 * Plugin activation.
	 *
	 * @since    5.0.0
	 */
	public static function activate() {

		self::check_php_version();
		self::check_wp_version();
		self::setup_defaults();

	}

	/**
	 * Check PHP version.
	 *
	 * @access  private
	 * @since   5.0.0
	 */
	private static function check_php_version() {

		$required = '5.2';
		$current  = phpversion();

		if ( version_compare( $current, $required, '>=' ) ) {
			return;
		}

		// Translators: %1$s - required version number, %2$s - current version number
		$message = __( 'Shortcodes Ultimate is not activated, because it requires PHP version %1$s (or higher). Current version of PHP is %2$s.', 'shortcodes-ultimate' );

		die( sprintf( $message, $required, $current ) );

	}

	/**
	 * Check WordPress version.
	 *
	 * @access  private
	 * @since   5.0.0
	 */
	private static function check_wp_version() {

		$required = '3.5';
		$current  = get_bloginfo( 'version' );

		if ( version_compare( $current, $required, '>=' ) ) {
			return;
		}

		// Translators: %1$s - required version number, %2$s - current version number
		$message = __( 'Shortcodes Ultimate is not activated, because it requires WordPress version %1$s (or higher). Current version of WordPress is %2$s.', 'shortcodes-ultimate' );

		die( sprintf( $message, $required, $current ) );

	}

	/**
	 * Setup plugin's default settings.
	 *
	 * @access  private
	 * @since   5.0.0
	 */
	private static function setup_defaults() {

		$defaults = array(
			'su_option_custom-formatting'    => 'on',
			'su_option_skip'                 => 'on',
			'su_option_prefix'               => 'su_',
			'su_option_custom-css'           => '',
			'su_option_supported_blocks'     => array(
				'core/paragraph',
				'core/shortcode',
				'core/freeform',
			),
			'su_option_generator_access'     => 'manage_options',
			'su_option_enable_shortcodes_in' => array( 'category_description' ),
		);

		foreach ( $defaults as $option => $value ) {

			if ( get_option( $option, 0 ) !== 0 ) {
				continue;
			}

			update_option( $option, $value, false );

		}

	}

}
