<?php
/**
 * Integrations
 *
 * @package Broken_Link_Checker
 */

if ( ! class_exists( 'blcIntegrations' ) ) {

	/**
	 * Integrations
	 */
	class blcIntegrations {

		/**
		 * Blc Integrations constructor.
		 */
		public function __construct() {
			if ( class_exists( '\Elementor\Plugin' ) ) {
				require_once BLC_DIRECTORY_LEGACY . '/integrations/elementor.php';
			}
			if ( class_exists( '\SiteOrigin_Panels' ) ) {
				require_once BLC_DIRECTORY_LEGACY . '/integrations/siteorigin.php';
				blcSiteOrigin::instance();
			}
		}
	}
	new blcIntegrations();
}
