<?php

/**
* Fired during plugin activation
*
* @link       https://www.therealbenroberts.com
* @since      1.0.0
*
* @package    BP_Toolkit
* @subpackage BP_Toolkit/includes
*/

/**
* Fired during plugin activation.
*
* This class defines all code necessary to run during the plugin's activation.
*
* @since      1.0.0
* @package    BP_Toolkit
* @subpackage BP_Toolkit/includes
* @author     Ben Roberts <me@therealbenroberts.com>
*/
class BP_Toolkit_Activator {

	/**
	* Short Description. (use period)
	*
	* Long Description.
	*
	* @since    1.0.0
	*/
	public static function activate() {

		if ( defined( 'BP_TOOLKIT_VERSION' ) ) {
			$version = BP_TOOLKIT_VERSION;
		} else {
			$version = '2.0.1';
		}
		$bp_toolkit = 'bp_toolkit';

		/**
		* The class responsible for defining all actions that occur in the admin area.
		*/
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-bp-toolkit-admin.php';

		$plugin_admin = new BP_Toolkit_Admin( $bp_toolkit, $version );

		$plugin_admin->setup_report_post_type();

		wp_insert_term(
			'Spam', // the term
			'report-type' // the taxonomy
		);

		wp_insert_term(
			'Offensive', // the term
			'report-type' // the taxonomy
		);

		wp_insert_term(
			'Misleading or scam', // the term
			'report-type' // the taxonomy
		);

		wp_insert_term(
			'Violent or abusive', // the term
			'report-type' // the taxonomy
		);

		// ATTENTION: This is *only* done during plugin activation hook in this example!
		// You should *NEVER EVER* do this on every page load!!
		flush_rewrite_rules();

	}

}
