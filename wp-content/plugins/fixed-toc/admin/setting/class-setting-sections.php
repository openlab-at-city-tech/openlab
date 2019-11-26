<?php
/**
 * Add sections to settings
 *
 * @since 3.0.0
 */
class Fixedtoc_Setting_Sections {
	/**
	 * Instance of Fixedtoc_Register_Setting.
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @var object
	 */	
	private $obj_setting;
	
	/**
	 * Constructor
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @param object $obj_setting
	 */
	public function __construct( $obj_setting ) {
		$this->obj_setting = $obj_setting;
		
		// Add sections
		$this->general_section();
		$this->appearance_section();
		$this->developer_section();
	}	

	/**
	 * Add general section
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function general_section() {
		$section_id = $this->obj_setting->add_section( 'general', __( 'General', 'fixedtoc' ), '__return_false' );
		
		$this->obj_setting->add_field( $section_id, 'general_enable' );
		$this->obj_setting->add_field( $section_id, 'general_post_types' );
		$this->obj_setting->add_field( $section_id, 'general_h_tags' );
		$this->obj_setting->add_field( $section_id, 'general_min_headings_num' );
		$this->obj_setting->add_field( $section_id, 'general_exclude_keywords' );
		$this->obj_setting->add_field( $section_id, 'general_title_to_id' );
		$this->obj_setting->add_field( $section_id, 'general_id_prefix' );
		$this->obj_setting->add_field( $section_id, 'general_in_widget' );
		$this->obj_setting->add_field( $section_id, 'general_shortcut' );
		$this->obj_setting->add_field( $section_id, 'debug_menu_selector' );
		$this->obj_setting->add_field( $section_id, 'debug_scroll_offset' );
	}
	
	/**
	 * Add appearance section
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function appearance_section() {
		$section_id = $this->obj_setting->add_section( 'appearance', __( 'Appearance', 'fixedtoc' ), array( $this, 'appearance_callback' ) );
	}
	
	/**
	 * Appearance section callback
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function appearance_callback() {
		echo '<a href="' . esc_attr( admin_url( 'customize.php' ) ) . '" target="_blank">' . __( 'Click on the link to set the appearance options.', 'fixedtoc' ) . '</a>';
		echo '<hr>';
	}	

	
	/**
	 * Add developer section
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function developer_section() {
		$section_id = $this->obj_setting->add_section( 'developer', __( 'Developer', 'fixedtoc' ), array( $this, 'developer_section_callback' ) );
		
		$this->obj_setting->add_field( $section_id, 'developer_debug' );
	}
	
	/**
	 * Developer section callback.
	 * 
	 * @since 3.1.0
	 * @access public
	 * 
	 * @return void
	 */
	public function developer_section_callback() {
		echo 'This section is for developer. <b>Don\'t change the options below</b> if you aren\'t familiar it!';
	}
}