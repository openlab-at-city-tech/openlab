<?php
/**
 * Control the Admin.
 *
 * @since 3.0.0
 */
class Fixedtoc_Admin_Control {

	/*
	 * Constructor.
	 *
	 * @since 3.0.0
	 * @access public
	 */
	public function __construct() {
// 		$this->init();
		$this->hooks();
	}

	/**
	 * Init
	 *
	 * @since 3.0.0
	 * @access private
	 *        
	 * @return void
	 */
	private function init() {}

	/**
	 * Hooks
	 *
	 * @since 3.0.0
	 * @access private
	 *        
	 * @return void
	 */
	private function hooks() {
		// Validate data
		require_once 'field-data/class-validate-data.php';
		
		if ( is_admin() ) {
			// Settings
			require_once 'setting/class-setting.php';
			new Fixedtoc_Setting();
			
			// Post meta box
			require_once 'metabox/class-metabox.php';
			new Fixedtoc_Metabox();
		}
		
		// Customize
		require_once 'customizer/class-customize.php';
		new Fixedtoc_Customize();
		
		// Widget
		require_once 'widget/class-widget.php';
		add_action( 'widgets_init', array( $this, 'register_widget' ) );
	}

	/**
	 * Sanitize data when saving
	 *
	 * @since 3.0.0
	 * @access public
	 *        
	 * @param mixed $vals        	
	 * @return object
	 */
	public static function sanitize( $vals ) {
		if ( $vals && is_array( $vals ) ) {
			$data = fixedtoc_get_field_data();
			foreach ( $vals as $key => $val ) {
				$sanitize = isset( $data[$key]['sanitize'] ) && $data[$key]['sanitize'] ? $data[$key]['sanitize'] : '';
				
				if ( ! $sanitize ) {
					continue;
				}
				
				if ( is_array( $sanitize ) ) {
					if ( method_exists( $sanitize[0], $sanitize[1] ) ) {
						$vals[$key] = call_user_func( array( $sanitize[0], $sanitize[1] ), $val );
					}
				} else {
					if ( function_exists( $sanitize ) ) {
						$vals[$key] = call_user_func( $sanitize, $val );
					}
				}
			}
		}
		
		return $vals;
	}
	
	/**
	 * register the widget for Fixed TOC.
	 *
	 * @since 3.1.8
	 * @access public
	 *
	 * @return void
	 */
	public function register_widget() {
		register_widget( "Fixedtoc_Widget" );
	}

}