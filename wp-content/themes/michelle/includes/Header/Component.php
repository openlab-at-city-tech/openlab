<?php
/**
 * Header component.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  1.3.7
 */

namespace WebManDesign\Michelle\Header;

use WebManDesign\Michelle\Component_Interface;
use WebManDesign\Michelle\Tool\AMP;

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

			// HTML body class.
			Body_Class::init();
			// HTML head.
			Head::init();
			// Containers.
			Container::init();

			// Actions

				add_action( 'wp', __CLASS__ . '::disable', 30 );

				add_action( 'tha_html_before', __CLASS__ . '::doctype' );

				add_action( 'tha_header_top', __CLASS__ . '::site_branding' );
				add_action( 'tha_header_top', __CLASS__ . '::search_form', 30 );

				add_action( 'michelle/search_form', 'get_search_form' );

			// Filters

				add_filter( 'get_search_form',         __CLASS__ . '::get_search_form' );
				add_filter( 'get_product_search_form', __CLASS__ . '::get_search_form' ); // WooCommerce

				add_filter( 'pre/michelle/accessibility/link_skip_to', __CLASS__ . '::skip_links_no_header', 10, 2 );

	} // /init

	/**
	 * Disable theme header.
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

			remove_all_actions( 'tha_header_before' );
			remove_all_actions( 'tha_header_top' );
			remove_all_actions( 'tha_header_bottom' );
			remove_all_actions( 'tha_header_after' );

	} // /disable

	/**
	 * Is header disabled?
	 *
	 * To check if header is disabled, use `! Header::is_enabled()` instead.
	 *
	 * @since  1.0.0
	 *
	 * @return  bool
	 */
	public static function is_disabled(): bool {

		// Output

			/**
			 * Filters the header disabling.
			 *
			 * @since  1.0.0
			 *
			 * @param  bool $disabled  If true, header is not displayed. Default: false.
			 */
			return (bool) apply_filters( 'michelle/header/is_disabled', false );

	} // /is_disabled

	/**
	 * Is header enabled?
	 *
	 * @since  1.0.0
	 *
	 * @return  bool
	 */
	public static function is_enabled(): bool {

		// Output

			/**
			 * Filters the header enabling.
			 *
			 * Filtering the negated output of `Header::is_disabled()` here
			 * so we can decide to use either "disabled" or "enabled" filter depending
			 * on circumstances.
			 *
			 * @since  1.0.0
			 *
			 * @param  bool $enabled  If true, header is displayed. Default: ! Header::is_disabled().
			 */
			return (bool) apply_filters( 'michelle/header/is_enabled', ! self::is_disabled() );

	} // /is_enabled

	/**
	 * Skip links: Remove header related links.
	 *
	 * When we display no header, remove all related skip links.
	 *
	 * @since    1.0.0
	 * @version  1.3.0
	 *
	 * @param  mixed  $pre  Pre output.
	 * @param  string $id   Link target element ID.
	 *
	 * @return  mixed  Original pre value or empty string.
	 */
	public static function skip_links_no_header( $pre, string $id ) {

		// Processing

			if (
				/**
				 * Disable header related skip links?
				 *
				 * @since  1.0.0
				 *
				 * @param  bool $disable  Default: ! Header::is_enabled().
				 */
				(bool) apply_filters( 'michelle/skip_links_no_header', ! self::is_enabled() )
				&& in_array( $id, array( 'site-navigation' ) )
			) {
				$pre = '';
			}


		// Output

			return $pre;

	} // /skip_links_no_header

	/**
	 * HTML doctype.
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public static function doctype() {

		// Output

			echo '<!DOCTYPE html>';

	} // /doctype

	/**
	 * Logo, site branding.
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public static function site_branding() {

		// Output

			get_template_part( 'templates/parts/header/site', 'branding' );

	} // /site_branding

	/**
	 * Search form.
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public static function search_form() {

		// Output

			/**
			 * Action to display search form.
			 *
			 * @since  1.0.0
			 *
			 * @param  array $args
			 */
			do_action( 'michelle/search_form', array( 'echo' => true ) );

	} // /search_form

	/**
	 * Search form modification, only in header.
	 *
	 * Also compatible with WooCommerce product search form.
	 *
	 * @since    1.0.0
	 * @version  1.3.7
	 *
	 * @param  string $html
	 *
	 * @return  string
	 */
	public static function get_search_form( string $html ): string {

		// Requirements check

			if ( ! doing_action( 'tha_header_top' ) ) {
				return $html;
			}


		// Variables

			$button  = '<button id="modal-search-toggle" class="modal-search-toggle" aria-controls="modal-search" aria-expanded="false"' . AMP::get_atts( 'search/button' ) . '>';
			$button .= '<svg class="svg-icon modal-search-open" width="1.5em" aria-hidden="true" role="img" focusable="false" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 16 16"><path d="M14.7,13.3L11,9.6c0.6-0.9,1-2,1-3.1C12,3.5,9.5,1,6.5,1S1,3.5,1,6.5S3.5,12,6.5,12c1.2,0,2.2-0.4,3.1-1l3.7,3.7L14.7,13.3z M2.5,6.5c0-2.2,1.8-4,4-4s4,1.8,4,4s-1.8,4-4,4S2.5,8.7,2.5,6.5z" /></svg>';
			$button .= '<svg class="svg-icon modal-search-close" width="1.5em" aria-hidden="true" role="img" focusable="false" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 16 16"><polygon points="14.7,2.7 13.3,1.3 8,6.6 2.7,1.3 1.3,2.7 6.6,8 1.3,13.3 2.7,14.7 8,9.4 13.3,14.7 14.7,13.3 9.4,8"/></svg>';
			$button .= '<span class="screen-reader-text">';
			$button .= esc_html_x( 'Toggle search form modal box', 'Search form modal toggle button label.', 'michelle' );
			$button .= '</span>';
			$button .= '</button>';


		// Processing

			$html = str_replace(
				'<form ',
				'<form id="modal-search" ',
				$html
			);

			wp_add_inline_script(
				'michelle-scripts-footer', // -> AMP ready.
				'"use strict";!function(){var e=document.getElementById("search-form-modal");if(document.getElementById("modal-search")){var t=document.getElementById("modal-search-toggle"),n=e.querySelector("[type=search]");t.onclick=function(){o()},document.addEventListener("keydown",(function(n){if(e.classList.contains("toggled")){var l=e.querySelectorAll("a, button, input:not([type=hidden]), select"),c=l[0],s=l[l.length-1],a=document.activeElement,d=9===n.keyCode,u=27===n.keyCode,r=n.shiftKey;u&&(n.preventDefault(),o(),t.focus()),!r&&d&&s===a&&(n.preventDefault(),c.focus()),r&&d&&c===a&&(n.preventDefault(),s.focus()),d&&c===s&&n.preventDefault()}}))}else e.style.display="none";function o(){e.classList.toggle("toggled"),document.documentElement.classList.toggle("lock-scroll"),-1!==e.className.indexOf("toggled")?(t.setAttribute("aria-expanded","true"),n&&n.focus()):t.setAttribute("aria-expanded","false")}}();'
			);


		// Output

			return
				'<div id="search-form-modal" class="modal-search-container"' . AMP::get_atts( 'search/container' ) . '>'
				. $button
				. $html
				. '</div>';

	} // /get_search_form

}
