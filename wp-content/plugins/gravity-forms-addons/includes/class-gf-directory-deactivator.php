<?php
/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @link       https://gravityview.co
 * @since      4.2
 *
 * @package    gravity-forms-addons
 * @subpackage gravity-forms-addons/includes
 */

class GFDirectory_Deactivator {

	public static function deactivate() {
		if ( ! GFDirectory::is_gravity_page() ) {
			return;
		}

		if ( ! GFDirectory::has_access( 'gravityforms_directory_uninstall' ) ) {
			( __( "You don't have adequate permission to uninstall Directory Add-On.", 'gravity-forms-addons' ) );
		}

		//removing options
		delete_option( 'gf_addons_settings' );

		//Deactivating plugin
		deactivate_plugins( GF_DIRECTORY_PLUGIN_BASENAME );
		update_option( 'recently_activated', array( GF_DIRECTORY_PLUGIN_BASENAME => time() ) + (array) get_option( 'recently_activated' ) );
	}
}
