<?php
/**
 * Add sections to widget
 *
 * @since 3.0.0
 */
class Fixedtoc_Widget_Sections {
	/**
	 * Instance of Fixedtoc_Widget.
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @var object
	 */	
	private $obj_widget;
	
	/**
	 * Constructor
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @param object $obj_widget
	 */
	public function __construct( $obj_widget ) {
		$this->obj_widget = $obj_widget;
		
		// Add sections
		$this->widget_section();
		$this->contents_section();
		$this->contents_header_section();
		$this->contents_list_section();
		$this->effect_section();
		$this->color_scheme_section();
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
		$section_id = $this->obj_widget->add_section( 'contents', __( 'Contents', 'fixedtoc' ), '__return_false' );
		
		// Add fields
		$this->obj_widget->add_field( $section_id, 'contents_shape' );
		$this->obj_widget->add_field( $section_id, 'contents_border_width' );
		$this->obj_widget->add_field( $section_id, 'contents_col_exp_init' );
	}
	
	/**
	 * Widget section.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @var void
	 */
	private function widget_section() {
		// Add section
		$section_id = $this->obj_widget->add_section( 'widget', '', '__return_false' );
		
		// Add fields
		$this->obj_widget->add_field( $section_id, 'widget_fixed' );
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
		$section_id = $this->obj_widget->add_section( 'contents_header', __( 'Contents Header', 'fixedtoc' ), '__return_false' );
		
		$this->obj_widget->add_field( $section_id, 'contents_header_title' );
		$this->obj_widget->add_field( $section_id, 'contents_header_font_size' );
		$this->obj_widget->add_field( $section_id, 'contents_header_font_family' );
		$this->obj_widget->add_field( $section_id, 'contents_header_customize_font_family' );
		$this->obj_widget->add_field( $section_id, 'contents_header_font_bold' );
		$this->obj_widget->add_field( $section_id, 'contents_header_title_tag' );
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
		$section_id = $this->obj_widget->add_section( 'contents_list', __( 'Contents List', 'fixedtoc' ), '__return_false' );
		
		$this->obj_widget->add_field( $section_id, 'contents_list_font_size' );
		$this->obj_widget->add_field( $section_id, 'contents_list_font_family' );
		$this->obj_widget->add_field( $section_id, 'contents_list_customize_font_family' );
		$this->obj_widget->add_field( $section_id, 'contents_list_style_type' );
		$this->obj_widget->add_field( $section_id, 'contents_list_nested' );
		$this->obj_widget->add_field( $section_id, 'contents_list_strong_1st' );
		$this->obj_widget->add_field( $section_id, 'contents_list_colexp' );
		$this->obj_widget->add_field( $section_id, 'contents_list_sub_icon' );
		$this->obj_widget->add_field( $section_id, 'contents_list_accordion' );
		$this->obj_widget->add_field( $section_id, 'contents_list_colexp_init_state' );
	}
	
	/**
	 * Add effects section
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function effect_section() {
		$section_id = $this->obj_widget->add_section( 'effects', __( 'Effects', 'fixedtoc' ), '__return_false' );
		
		$this->obj_widget->add_field( $section_id, 'effects_active_link' );
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
		$section_id = $this->obj_widget->add_section( 'color_scheme', __( 'Color Scheme', 'fixedtoc' ), '__return_false' );
		
		$this->obj_widget->add_field( $section_id, 'color_contents_border' );
		$this->obj_widget->add_field( $section_id, 'color_contents_header' );
		$this->obj_widget->add_field( $section_id, 'color_contents_header_bg' );
		$this->obj_widget->add_field( $section_id, 'color_contents_list_bg' );
		$this->obj_widget->add_field( $section_id, 'color_contents_list_link' );
		$this->obj_widget->add_field( $section_id, 'color_contents_list_hover_link' );
		$this->obj_widget->add_field( $section_id, 'color_contents_list_active_link' );
		$this->obj_widget->add_field( $section_id, 'color_contents_list_active_link_bg' );
		$this->obj_widget->add_field( $section_id, 'color_target_hint' );
	}
}