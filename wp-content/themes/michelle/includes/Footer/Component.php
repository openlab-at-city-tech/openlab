<?php
/**
 * Footer component.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  1.3.0
 */

namespace WebManDesign\Michelle\Footer;

use WebManDesign\Michelle\Component_Interface;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class Component implements Component_Interface {

	/**
	 * Initialization.
	 *
	 * @since    1.0.0
	 * @version  1.0.6
	 *
	 * @return  void
	 */
	public static function init() {

		// Processing

			// Containers.
			Container::init();

			// Actions

				add_action( 'tha_footer_bottom', __CLASS__ . '::site_info', 100 );

				add_action( 'wp', __CLASS__ . '::disable', 30 );

			// Filters

				add_filter( 'pre/michelle/accessibility/link_skip_to', __CLASS__ . '::skip_links_no_footer', 10, 2 );

	} // /init

	/**
	 * Disable theme footer.
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public static function disable() {

		// Requirements check

			if ( self::is_enabled() ) {
				return;
			}


		// Processing

			remove_all_actions( 'tha_footer_before' );
			remove_all_actions( 'tha_footer_top' );
			remove_all_actions( 'tha_footer_bottom' );
			remove_all_actions( 'tha_footer_after' );

	} // /disable

	/**
	 * Is footer disabled?
	 *
	 * To check if footer is disabled, use `! Footer::is_enabled()` instead.
	 *
	 * @since  1.0.0
	 *
	 * @return  bool
	 */
	public static function is_disabled(): bool {

		// Output

			/**
			 * Filters the footer disabling.
			 *
			 * @since  1.0.0
			 *
			 * @param  bool $disabled  If true, footer is not displayed. Default: false.
			 */
			return (bool) apply_filters( 'michelle/footer/is_disabled', false );

	} // /is_disabled

	/**
	 * Is footer enabled?
	 *
	 * @since  1.0.0
	 *
	 * @return  bool
	 */
	public static function is_enabled(): bool {

		// Output

			/**
			 * Filters the footer enabling.
			 *
			 * Filtering the negated output of `Footer::is_disabled()` here
			 * so we can decide to use either "disabled" or "enabled" filter depending
			 * on circumstances.
			 *
			 * @since  1.0.0
			 *
			 * @param  bool $enabled  If true, footer is displayed. Default: ! Footer::is_disabled().
			 */
			return (bool) apply_filters( 'michelle/footer/is_enabled', ! self::is_disabled() );

	} // /is_enabled

	/**
	 * Skip links: Remove footer related links.
	 *
	 * When we display no footer, remove all related skip links.
	 *
	 * @since    1.0.0
	 * @version  1.3.0
	 *
	 * @param  mixed  $pre  Pre output.
	 * @param  string $id   Link target element ID.
	 *
	 * @return  mixed  Original pre value or empty string.
	 */
	public static function skip_links_no_footer( $pre, string $id ) {

		// Processing

			if (
				/**
				 * Disable footer related skip links?
				 *
				 * @since  1.0.0
				 *
				 * @param  bool $disable  Default: ! Footer::is_enabled().
				 */
				(bool) apply_filters( 'michelle/skip_links_no_footer', ! self::is_enabled() )
				&& in_array( $id, array( 'colophon' ) )
			) {
				$pre = '';
			}


		// Output

			return $pre;

	} // /skip_links_no_footer

	/**
	 * Site info.
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public static function site_info() {

		// Output

			get_template_part( 'templates/parts/footer/site', 'info' );

	} // /site_info

}
