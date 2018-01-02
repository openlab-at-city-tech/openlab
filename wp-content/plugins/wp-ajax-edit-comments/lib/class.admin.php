<?php
class AECAdmin {
		public static function add_admin_pages(){
			global $aecomments;
			$capabilities = 'administrator';
			if ( AECCore::is_multisite() ) $capabilities = 'manage_network';
			$admin_hooks = array();
			$admin_hooks[] = add_menu_page( 'Ajax Edit Comments', 'AEC', $capabilities, 'wpaec', array("AECAdmin", 'print_admin_page_behavior'), 'dashicons-format-status' );
			$admin_hooks[] = add_submenu_page( 'wpaec', __( 'Behavior', 'ajaxEdit' ), __( 'Behavior', 'ajaxEdit' ), $capabilities, 'wpaec', array( 'AECAdmin', 'print_admin_page_behavior' ) );
			$admin_hooks[] = add_submenu_page( 'wpaec', __( 'Appearance', 'ajaxEdit' ), __( 'Appearance', 'ajaxEdit' ), $capabilities, 'aecappearance', array( 'AECAdmin', 'print_admin_page_appearance' ) );
			$admin_hooks[] = add_submenu_page( 'wpaec', __( 'Permissions', 'ajaxEdit' ), __( 'Permissions', 'ajaxEdit' ), $capabilities, 'aecpermissions', array( 'AECAdmin', 'print_admin_page_permissions' ) );
			$admin_hooks[] = add_submenu_page( 'wpaec', __( 'Cleanup', 'ajaxEdit' ), __( 'Cleanup', 'ajaxEdit' ), $capabilities, 'aeccleanup', array( 'AECAdmin', 'print_admin_page_cleanup' ) );

			
			foreach( $admin_hooks as $hook ) {
				add_action('admin_print_styles-' . $hook, array('AECDependencies', 'add_admin_panel_css'), 1000);
				add_action('admin_print_scripts-' . $hook, array('AECDependencies', 'add_admin_scripts'), 1000);
			}
			do_action('aec-addon-menus');
		}
		
		public static function print_admin_page_appearance() {
			global $aecomments;
			include_once $aecomments->get_plugin_dir( '/views/admin-panel/appearance.php' );
		} //end print_admin_page_appearance
		
		public static function print_admin_page_behavior() {
			global $aecomments;
			include_once $aecomments->get_plugin_dir( '/views/admin-panel/behavior.php' );
		} //end print_admin_page_behavior
		
		public static function print_admin_page_cleanup() {
			global $aecomments;
			include_once $aecomments->get_plugin_dir( '/views/admin-panel/cleanup.php' );
		} //end print_admin_page_cleanup
		
		public static function print_admin_page_permissions() {
			global $aecomments;
			include_once $aecomments->get_plugin_dir( '/views/admin-panel/permissions.php' );
		} //end print_admin_page_permissions
		
}