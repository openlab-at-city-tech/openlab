<?php
/**
 * Albums Preview class.
 *
 * @since 3.5.0
 *
 * @package Envira_Gallery
 * @author  Envira Gallery Team
 */

namespace Imagely\NGG\Admin;

/**
 * Albums Preview Class
 *
 * @since 3.5.0
 */
class Ecommerce_Preview {

	/**
	 * Holds base singleton.
	 *
	 * @since 3.5.0
	 *
	 * @var object
	 */
	public $base = null;

	/**
	 * Class Hooks
	 *
	 * @since 3.5.0
	 *
	 * @return void
	 */
	public function hooks() {

		if ( nextgen_is_plus_or_pro_enabled() ) {
			return;
		}

		add_action( 'admin_menu', [ $this, 'admin_menu' ], 10 );
	}

	/**
	 * Helper Method to add Admin Menu
	 *
	 * @since 3.5.0
	 *
	 * @return void
	 */
	public function admin_menu() {

		// Ecommerce page - hidden from menu but accessible via direct URL.
		// Using empty string '' as parent creates hidden pages without PHP 8.1+ deprecation warnings.
		add_submenu_page(
			'',
			esc_html__( 'Ecommerce', 'nggallery' ),
			esc_html__( 'Ecommerce', 'nggallery' ),
			apply_filters( 'envira_gallery_menu_cap', 'manage_options' ),
			NGG_PLUGIN_SLUG . '-albums',
			[ $this, 'page' ]
		);

		// Set page title for hidden pages to avoid strip_tags() deprecation warning.
		add_action( 'admin_head', [ $this, 'set_page_title' ] );
	}

	/**
	 * Set page title for the ecommerce page to avoid deprecation warnings.
	 *
	 * @since 3.5.0
	 * @return void
	 */
	public function set_page_title() {
		global $title;
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['page'] ) && ( NGG_PLUGIN_SLUG . '-albums' ) === $_GET['page'] ) {
			$title = esc_html__( 'Ecommerce', 'nggallery' );
		}
	}

	/**
	 * Helper Method to display Admin Page
	 *
	 * @since 3.5.0
	 *
	 * @return void
	 */
	public function page() {
		// If here, we're on an Envira Gallery or Album screen, so output the header.
		nextgen_load_admin_partial( 'ecommerce', [] );
	}
}
