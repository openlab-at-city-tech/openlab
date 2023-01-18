<?php
/**
 * Compatibility functions for theme requirements.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since  1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class Michelle_Compatibility {

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

				add_action( 'after_switch_theme', __CLASS__ . '::switch_theme' );

				add_action( 'load-customize.php', __CLASS__ . '::customize' );

				add_action( 'template_redirect', __CLASS__ . '::preview' );

	} // /init

	/**
	 * Gets the message to warn the user about the theme requirements not being met.
	 *
	 * @since  1.0.0
	 *
	 * @return  string  Message to show to the user.
	 */
	public static function get_message() {

		// Variables

			$output = array();


		// Processing

			if ( version_compare( $GLOBALS['wp_version'], MICHELLE_WP_VERSION, '<' ) ) {
				$output[] = sprintf(
					/* translators: 1: required WP version number, 2: available WP version number */
					__( 'This theme requires at least WordPress version %1$s. You are running version %2$s.', 'michelle' ),
					MICHELLE_WP_VERSION,
					$GLOBALS['wp_version']
				);
			}

			if ( version_compare( PHP_VERSION, MICHELLE_PHP_VERSION, '<' ) ) {
				$output[] = sprintf(
					/* translators: 1: required PHP version number, 2: available PHP version number */
					__( 'This theme requires at least PHP version %1$s. You are running version %2$s.', 'michelle' ),
					MICHELLE_PHP_VERSION,
					PHP_VERSION
				);
			}

			if ( ! empty( $output ) ) {
				$output[] = __( 'Please upgrade and try again.', 'michelle' );
			}


		// Output

			return implode( PHP_EOL, $output );

	} // /get_message

	/**
	 * Adds a message for unsuccessful theme switch.
	 *
	 * Prints an update nag after an unsuccessful attempt to switch to the theme
	 * when requirements are not met.
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public static function the_notice() {

		// Output

			printf(
				'<div class="error"><p>%s</p></div>',
				esc_html( self::get_message() )
			);

	} // /the_notice

	/**
	 * Prevents switching to the theme when requirements are not met.
	 *
	 * Switches to the previously active theme instead.
	 *
	 * @since  1.0.0
	 *
	 * @param  string $old_name  Previous theme name/slug.
	 *
	 * @return  void
	 */
	public static function switch_theme( $old_name ) {

		// Processing

			switch_theme( $old_name ? $old_name : WP_DEFAULT_THEME );
			unset( $_GET['activated'] );

			add_action( 'admin_notices', __CLASS__ . '::the_notice' );

	} // /switch_theme

	/**
	 * Prevents the Customizer from being loaded when requirements are not met.
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public static function customize() {

		// Output

			wp_die(
				'<strong>' . esc_html( self::get_message() ) . '</strong>',
				'',
				array(
					'back_link' => true,
				)
			);

	} // /customize

	/**
	 * Prevents the Theme Preview from being loaded when requirements are not met.
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public static function preview() {

		// Output

			if ( isset( $_GET['preview'] ) ) {
				wp_die( '<strong>' . esc_html( self::get_message() ) . '</strong>' );
			}

	} // /preview

} // /Michelle_Compatibility

Michelle_Compatibility::init();
