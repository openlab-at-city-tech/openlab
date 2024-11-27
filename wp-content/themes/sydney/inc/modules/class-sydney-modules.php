<?php
/**
 * Premium modules
 *
 * @package Sydney
 */

if ( ! class_exists( 'Sydney_Modules' ) ) {
	/**
	 * Get a svg icon
	 */
	class Sydney_Modules {

		/**
		 * Constructor
		 */
		public function __construct() {
			add_action( 'admin_init', array( $this, 'activate_modules' ) );
		}		

		/**
		 * All modules registered in Sydney
		 */
		public static function get_modules( $category = false ) {

			$modules = array();

			$modules['general'] = array(
				array(
					'slug'			=> 'block-templates',
					'name'          => esc_html__( 'Block Templates', 'sydney' ),
					'type'          => 'pro',
					'link' 			=> admin_url( 'site-editor.php?path=%2Fwp_template_part%2Fall' ),
					'link_label'	=> esc_html__( 'Build templates', 'sydney' ),
					'activate_uri' 	=> '&amp;activate_module_block-templates', //param is added in dashboard class
					'text'			=> __( 'Build headers, footers etc. with the site editor.', 'sydney' ) . '<div><a target="_blank" href="#">' . __( 'Documentation article', 'sydney' ) . '</a></div>',
					'keywords'		=> array( 'header', 'footer', 'template', 'templates', 'builder' ),
				)
			);

			if ( $category ) {
				return $modules[$category];
			}
		
			//build array with all modules if no category is specified
			$all_modules = array();
			foreach ( $modules as $module ) {
				$all_modules = array_merge( $all_modules, $module );
			}

			return $all_modules;
		}

		/**
		 * Check if a specific module is activated
		 */
		public static function is_module_active( $module ) {

			$all_modules = get_option( 'sydney-modules' );
			$all_modules = ( is_array( $all_modules ) ) ? $all_modules : (array) $all_modules;

			if ( array_key_exists( $module, $all_modules ) && true === $all_modules[$module] ) {
				return true;
			}
		
			return false;
		}

		/**
		 * Activate modules on click
		 */
		public function activate_modules() {
			$modules = $this->get_modules();

			$all_modules = get_option( 'sydney-modules' );
			$all_modules = ( is_array( $all_modules ) ) ? $all_modules : (array) $all_modules;

			foreach ( $modules as $module ) {
				if ( isset( $_GET['activate_module_' . $module['slug'] ] ) ) {
					if ( '1' == $_GET['activate_module_' . $module['slug'] ] ) {
						update_option( 'sydney-modules', array_merge( $all_modules, array( $module['slug'] => true ) ) );
					} elseif ( '0' == $_GET['activate_module_' . $module['slug'] ] ) {
						update_option( 'sydney-modules', array_merge( $all_modules, array( $module['slug'] => false ) ) );
					}
				}
			}
		}
	}	
}

new Sydney_Modules();