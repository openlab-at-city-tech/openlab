<?php
/**
 * Add sections to metabox
 *
 * @since 3.0.0
 */
class Fixedtoc_Metabox_Sections {
	/**
	 * Instance of Fixedtoc_Register_Metabox.
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @var object
	 */	
	private $obj_meta;
	
	/**
	 * Constructor
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @param object $obj_meta
	 */
	public function __construct( $obj_meta ) {
		$this->obj_meta = $obj_meta;
		
		// Add sections
		$this->general_section();
		$this->location_section();
		$this->trigger_button_section();
		$this->contents_section();
		$this->contents_header_section();
		$this->contents_list_section();
		$this->effects_section();
		$this->color_scheme_section();
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
		$section_id = $this->obj_meta->add_section( 'general', __( 'General', 'fixedtoc' ), '__return_false' );
		
		$this->obj_meta->add_field( $section_id, 'general_in_widget' );
		$this->obj_meta->add_field( $section_id, 'general_h_tags' );
		$this->obj_meta->add_field( $section_id, 'general_exclude_keywords' );
	}
	
	/**
	 * Add location section
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function location_section() {
		$section_id = $this->obj_meta->add_section( 'location', __( 'Location', 'fixedtoc' ), '__return_false' );
		
		$this->obj_meta->add_field( $section_id, 'location_fixed_position' );
		$this->obj_meta->add_field( $section_id, 'location_horizontal_offset' );
		$this->obj_meta->add_field( $section_id, 'location_vertical_offset' );
	}
	
	/**
	 * Add trigger button section
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function trigger_button_section() {
		$section_id = $this->obj_meta->add_section( 'trigger_button', __( 'Trigger Button', 'fixedtoc' ), '__return_false' );
		
		$this->obj_meta->add_field( $section_id, 'trigger_icon' );
		$this->obj_meta->add_field( $section_id, 'trigger_size' );
		$this->obj_meta->add_field( $section_id, 'trigger_shape' );
		$this->obj_meta->add_field( $section_id, 'trigger_border_width' );
		$this->obj_meta->add_field( $section_id, 'trigger_initial_visibility' );
	}
	
	/**
	 * Contents section.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @var void
	 */
	private function contents_section() {
		// Add section
		$section_id = $this->obj_meta->add_section( 'contents', __( 'Contents', 'fixedtoc' ), '__return_false' );
		
		// Add fields
		$this->obj_meta->add_field( $section_id, 'contents_fixed_width' );
		$this->obj_meta->add_field( $section_id, 'contents_fixed_height' );
		$this->obj_meta->add_field( $section_id, 'contents_shape' );
		$this->obj_meta->add_field( $section_id, 'contents_border_width' );
		$this->obj_meta->add_field( $section_id, 'contents_col_exp_init' );
		$this->obj_meta->add_field( $section_id, 'contents_display_in_post' );
		$this->obj_meta->add_field( $section_id, 'contents_position_in_post' );
		$this->obj_meta->add_field( $section_id, 'contents_float_in_post' );
		$this->obj_meta->add_field( $section_id, 'contents_width_in_post' );
		$this->obj_meta->add_field( $section_id, 'contents_height_in_post' );
	}
	
	/**
	 * Add contents header section
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function contents_header_section() {
		$section_id = $this->obj_meta->add_section( 'contents_header', __( 'Contents Header', 'fixedtoc' ), '__return_false' );
		
		$this->obj_meta->add_field( $section_id, 'contents_header_title' );
		$this->obj_meta->add_field( $section_id, 'contents_header_font_size' );
		$this->obj_meta->add_field( $section_id, 'contents_header_font_family' );
		$this->obj_meta->add_field( $section_id, 'contents_header_customize_font_family' );
		$this->obj_meta->add_field( $section_id, 'contents_header_font_bold' );
		$this->obj_meta->add_field( $section_id, 'contents_header_title_tag' );
	}
	
	/**
	 * Add contents list section
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function contents_list_section() {
		$section_id = $this->obj_meta->add_section( 'contents_list', __( 'Contents List', 'fixedtoc' ), '__return_false' );
		
		$this->obj_meta->add_field( $section_id, 'contents_list_font_size' );
		$this->obj_meta->add_field( $section_id, 'contents_list_font_family' );
		$this->obj_meta->add_field( $section_id, 'contents_list_customize_font_family' );
		$this->obj_meta->add_field( $section_id, 'contents_list_style_type' );
		$this->obj_meta->add_field( $section_id, 'contents_list_nested' );
		$this->obj_meta->add_field( $section_id, 'contents_list_strong_1st' );
		$this->obj_meta->add_field( $section_id, 'contents_list_colexp' );
		$this->obj_meta->add_field( $section_id, 'contents_list_sub_icon' );
		$this->obj_meta->add_field( $section_id, 'contents_list_accordion' );
		$this->obj_meta->add_field( $section_id, 'contents_list_colexp_init_state' );
	}
	
	/**
	 * Add effects section
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function effects_section() {
		$section_id = $this->obj_meta->add_section( 'effects', __( 'Effects', 'fixedtoc' ), '__return_false' );
		
		$this->obj_meta->add_field( $section_id, 'effects_in_out' );
		$this->obj_meta->add_field( $section_id, 'effects_active_link' );
	}
	
	/**
	 * Add color scheme section
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function color_scheme_section() {
		$section_id = $this->obj_meta->add_section( 'color_scheme', __( 'Color Scheme', 'fixedtoc' ), '__return_false' );
		
		$this->obj_meta->add_field( $section_id, 'color_button' );
		$this->obj_meta->add_field( $section_id, 'color_button_bg' );
		$this->obj_meta->add_field( $section_id, 'color_button_border' );
		$this->obj_meta->add_field( $section_id, 'color_contents_border' );
		$this->obj_meta->add_field( $section_id, 'color_contents_header' );
		$this->obj_meta->add_field( $section_id, 'color_contents_header_bg' );
		$this->obj_meta->add_field( $section_id, 'color_contents_list_bg' );
		$this->obj_meta->add_field( $section_id, 'color_contents_list_link' );
		$this->obj_meta->add_field( $section_id, 'color_contents_list_hover_link' );
		$this->obj_meta->add_field( $section_id, 'color_contents_list_active_link' );
		$this->obj_meta->add_field( $section_id, 'color_contents_list_active_link_bg' );
		$this->obj_meta->add_field( $section_id, 'color_target_hint' );
	}
}