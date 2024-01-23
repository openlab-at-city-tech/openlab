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

		add_submenu_page(
			NGGFOLDER,
			esc_html__( 'Ecommerce', 'nggallery' ),
			esc_html__( 'Ecommerce', 'nggallery' ),
			apply_filters( 'envira_gallery_menu_cap', 'manage_options' ),
			NGG_PLUGIN_SLUG . '-albums',
			[ $this, 'page' ]
		);
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
