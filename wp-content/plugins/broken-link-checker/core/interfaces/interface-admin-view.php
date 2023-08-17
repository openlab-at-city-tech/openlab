<?php
/**
 * Interface for admin pages view.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.0.0
 *
 * @author  Panos Lyrakis <panos.lyrakis@incsub.com>
 * @package WPMUDEV_BLC\Core\Interfaces
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\Core\Interfaces;

// Abort if called directly.
defined( 'WPINC' ) || die;

interface Admin_View {
	/**
	 * Renders the admin page full view.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function render( $params = array() );

	/**
	 * Renders the page footer.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function render_header();

	/**
	 * Renders the page body content.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function render_body();

	/**
	 * Renders the page footer.
	 *
	 * @since 2.0.0
	 *
	 * @return void Renders the page footer.
	 */
	public function render_footer();
}