<?php
class AECAdmin {
		public static function add_admin_pages(){
			global $aecomments;
			$capabilities = 'administrator';
			if ( AECCore::is_multisite() ) $capabilities = 'manage_network';
			add_menu_page( 'Ajax Edit Comments', 'AEC', $capabilities, 'wpaec', array("AECAdmin", 'print_getting_started'), $aecomments->get_plugin_url( 'images/menu-icon.png' ) );
			add_submenu_page( 'wpaec', 'Getting Started', 'Getting Started', $capabilities, 'wpaec', array("AECAdmin", 'print_getting_started') );
			do_action('aec-addon-menus');
			add_submenu_page( 'wpaec', 'Settings', 'Settings', $capabilities, 'wpaecsettings', array("AECAdmin", 'print_admin_page') );
		}
		public static function print_getting_started() {
			global $aecomments;
			include_once $aecomments->get_plugin_dir( '/views/admin-getting-started.php' );
		}
		
		//Provides the interface for the admin pages
		public static function print_admin_page() {
			global $aecomments;
			include_once $aecomments->get_plugin_dir( '/views/admin-panel.php' );
		}
}