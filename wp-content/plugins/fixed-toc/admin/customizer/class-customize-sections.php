<?php
/**
 * Register customize sections.
 *
 * @since 3.0.0
 */
class Fixedtoc_Customize_Sections {
	
	/**
	 * Instance of Fixedtoc_Register_Customize.
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @var object
	 */	
	private $obj_customize;	
	
	/**
	 * Instance of WP_Customize.
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @var object
	 */	
	private $wp_customize;
	
	/**
	 * Contructor.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @param object $obj_customize
	 * @var void
	 */	
	public function __construct( $obj_customize ) {
		$this->obj_customize = $obj_customize;
		$this->wp_customize = $obj_customize->get_wp_customize();
		
		// Add sections
		$this->location_section();
		$this->trigger_button_section();
		$this->contents_section();
		$this->contents_header_section();
		$this->contents_list_section();
		$this->effects_section();
		$this->color_scheme_section();
		$this->customize_css_section();
	}
	
	/**
	 * Location section.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @var void
	 */
	private function location_section() {
		// Add section
		$section_id = $this->obj_customize->add_section( array(
			'title' 				=> __( 'Location', 'fixedtoc' ),
			'priority'			=> 10,
			'description'		=> __( 'Set TOC\'s location relative to the post container.', 'fixedtoc' ),
		) );
		
		// Add fields
		$this->obj_customize->add_field( $section_id, 'location_fixed_position' );
		$this->obj_customize->add_field( $section_id, 'location_horizontal_offset' );
		$this->obj_customize->add_field( $section_id, 'location_vertical_offset' );
		
		// Partial
	  $this->wp_customize->selective_refresh->add_partial( 'fixedtoc_location', array(
			'selector' => '#fixedtoc-style-inline-css',
			'settings' => array(
				'fixed_toc[location_fixed_position]',
				'fixed_toc[location_horizontal_offset]',
				'fixed_toc[location_vertical_offset]'
			),
			'render_callback' => array( $this->obj_customize, 'update_inline_style' ),
    ) );		
	}
	
	/**
	 * Trigger button section.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @var void
	 */
	private function trigger_button_section() {
		// Add section
		$section_id = $this->obj_customize->add_section( array(
			'title' 				=> __( 'Trigger Button', 'fixedtoc' ),
			'priority'			=> 20,
			'description'		=> '',
		) );
		
		// Add fields
		$this->obj_customize->add_field( $section_id, 'trigger_icon' );
		$this->obj_customize->add_field( $section_id, 'trigger_size' );
		$this->obj_customize->add_field( $section_id, 'trigger_shape' );
		$this->obj_customize->add_field( $section_id, 'trigger_border_width' );
		$this->obj_customize->add_field( $section_id, 'trigger_initial_visibility' );
		
		// Partial
	  $this->wp_customize->selective_refresh->add_partial( 'fixedtoc_button', array(
			'selector' => '#fixedtoc-style-inline-css',
			'settings' => array(
				'fixed_toc[trigger_size]',
				'fixed_toc[trigger_border_width]'
			),
			'render_callback' => array( $this->obj_customize, 'update_inline_style' ),
    ) );
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
		$section_id = $this->obj_customize->add_section( array(
			'title' 				=> __( 'Contents', 'fixedtoc' ),
			'priority'			=> 25,
			'description'		=> '',
		) );
		
		// Add fields
		$this->obj_customize->add_field( $section_id, 'contents_shape' );
		$this->obj_customize->add_field( $section_id, 'contents_border_width' );
		$this->obj_customize->add_field( $section_id, 'contents_fixed_width' );
		$this->obj_customize->add_field( $section_id, 'contents_fixed_height' );
		$this->obj_customize->add_field( $section_id, 'contents_display_in_post' );
		$this->obj_customize->add_field( $section_id, 'contents_position_in_post' );
		$this->obj_customize->add_field( $section_id, 'contents_float_in_post' );
		$this->obj_customize->add_field( $section_id, 'contents_width_in_post' );
		$this->obj_customize->add_field( $section_id, 'contents_height_in_post' );
		$this->obj_customize->add_field( $section_id, 'contents_col_exp_init' );
		
		// Partial
	  $this->wp_customize->selective_refresh->add_partial( 'fixedtoc_contents', array(
			'selector' => '#fixedtoc-style-inline-css',
			'settings' => array(
				'fixed_toc[contents_fixed_width]',
				'fixed_toc[contents_fixed_height]',
				'fixed_toc[contents_border_width]',
				'fixed_toc[contents_width_in_post]',
				'fixed_toc[contents_height_in_post]',
			),
			'render_callback' => array( $this->obj_customize, 'update_inline_style' ),
    ) );
	}
	
	/**
	 * Contents header section.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @var void
	 */
	private function contents_header_section() {
		// Add section
		$section_id = $this->obj_customize->add_section( array(
			'title' 				=> __( 'Contents Header', 'fixedtoc' ),
			'priority'			=> 30,
			'description'		=> '',
		) );
		
		// Add fields
		$this->obj_customize->add_field( $section_id, 'contents_header_title' );
		$this->obj_customize->add_field( $section_id, 'contents_header_font_size' );
		$this->obj_customize->add_field( $section_id, 'contents_header_font_family' );
		$this->obj_customize->add_field( $section_id, 'contents_header_customize_font_family' );
		$this->obj_customize->add_field( $section_id, 'contents_header_font_bold' );
		$this->obj_customize->add_field( $section_id, 'contents_header_title_tag' );
		
		// Partial
	  $this->wp_customize->selective_refresh->add_partial( 'fixedtoc_contents_header', array(
			'selector' => '#fixedtoc-style-inline-css',
			'settings' => array(
				'fixed_toc[contents_header_font_size]',
				'fixed_toc[contents_header_font_family]',
				'fixed_toc[contents_header_customize_font_family]',
				'fixed_toc[contents_header_font_bold]',
			),
			'render_callback' => array( $this->obj_customize, 'update_inline_style' ),
    ) );
	}
	
	/**
	 * Contents list section.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @var void
	 */
	private function contents_list_section() {
		// Add section
		$section_id = $this->obj_customize->add_section( array(
			'title' 				=> __( 'Contents List', 'fixedtoc' ),
			'priority'			=> 40,
			'description'		=> '',
		) );
		
		// Add fields
		$this->obj_customize->add_field( $section_id, 'contents_list_font_size' );
		$this->obj_customize->add_field( $section_id, 'contents_list_font_family' );
		$this->obj_customize->add_field( $section_id, 'contents_list_customize_font_family' );
		$this->obj_customize->add_field( $section_id, 'contents_list_style_type' );
		$this->obj_customize->add_field( $section_id, 'contents_list_nested' );
		$this->obj_customize->add_field( $section_id, 'contents_list_strong_1st' );
		$this->obj_customize->add_field( $section_id, 'contents_list_colexp' );
		$this->obj_customize->add_field( $section_id, 'contents_list_sub_icon' );
		$this->obj_customize->add_field( $section_id, 'contents_list_accordion' );
		$this->obj_customize->add_field( $section_id, 'contents_list_colexp_init_state' );
		
		// Partial
	  $this->wp_customize->selective_refresh->add_partial( 'fixedtoc_contents_list', array(
			'selector' => '#fixedtoc-style-inline-css',
			'settings' => array(
				'fixed_toc[contents_list_font_size]',
				'fixed_toc[contents_list_font_family]',
				'fixed_toc[contents_list_customize_font_family]',
				'fixed_toc[contents_list_style_type]',
				'fixed_toc[contents_list_strong_1st]',
			),
			'render_callback' => array( $this->obj_customize, 'update_inline_style' ),
    ) );
	}
	
	/**
	 * Effects section.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @var void
	 */
	private function effects_section() {
		// Add section
		$section_id = $this->obj_customize->add_section( array(
			'title' 				=> __( 'Effects', 'fixedtoc' ),
			'priority'			=> 50,
			'description'		=> __( 'Setup special animate effects.', 'fixedtoc' ),
		) );
		
		// Add fields
		$this->obj_customize->add_field( $section_id, 'effects_in_out' );
		$this->obj_customize->add_field( $section_id, 'effects_active_link' );
		
		// Partial
	  $this->wp_customize->selective_refresh->add_partial( 'fixedtoc_effects', array(
			'selector' => '#fixedtoc-style-inline-css',
			'settings' => array(
				'fixed_toc[effects_active_link]',
			),
			'render_callback' => array( $this->obj_customize, 'update_inline_style' ),
    ) );
	}
	
	/**
	 * Color scheme section.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @var void
	 */
	private function color_scheme_section() {
		// Add section
		$section_id = $this->obj_customize->add_section( array(
			'title' 				=> __( 'Color Scheme', 'fixedtoc' ),
			'priority'			=> 60,
			'description'		=> '',
		) );
		
		// Add fields
		$this->obj_customize->add_field( $section_id, 'color_button' );
		$this->obj_customize->add_field( $section_id, 'color_button_bg' );
		$this->obj_customize->add_field( $section_id, 'color_button_border' );
		$this->obj_customize->add_field( $section_id, 'color_contents_border' );
		$this->obj_customize->add_field( $section_id, 'color_contents_header' );
		$this->obj_customize->add_field( $section_id, 'color_contents_header_bg' );
		$this->obj_customize->add_field( $section_id, 'color_contents_list_bg' );
		$this->obj_customize->add_field( $section_id, 'color_contents_list_link' );
		$this->obj_customize->add_field( $section_id, 'color_contents_list_hover_link' );
		$this->obj_customize->add_field( $section_id, 'color_contents_list_active_link' );
		$this->obj_customize->add_field( $section_id, 'color_contents_list_active_link_bg' );
		$this->obj_customize->add_field( $section_id, 'color_target_hint' );
		
		// Partial refresh
	  $this->wp_customize->selective_refresh->add_partial( 'fixedtoc_color_scheme', array(
			'selector' => '#fixedtoc-style-inline-css',
			'settings' => array(
				'fixed_toc[color_button]',
				'fixed_toc[color_button_bg]',
				'fixed_toc[color_button_border]',
				'fixed_toc[color_contents_border]',
				'fixed_toc[color_contents_header]',
				'fixed_toc[color_contents_header_bg]',
				'fixed_toc[color_contents_list_bg]',
				'fixed_toc[color_contents_list_link]',
				'fixed_toc[color_contents_list_hover_link]',
				'fixed_toc[color_contents_list_active_link]',
				'fixed_toc[color_contents_list_active_link_bg]',
				'fixed_toc[color_target_hint]',
			),
			'render_callback' => array( $this->obj_customize, 'update_inline_style' ),
    ) );
	}
	
	/**
	 * Customize CSS.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @var void
	 */
	private function customize_css_section() {
		// Add section
		$section_id = $this->obj_customize->add_section( array(
			'title' 				=> __( 'Customize CSS', 'fixedtoc' ),
			'priority'			=> 100,
			'description'		=> '',
		) );
		
		// Add fields
		$this->obj_customize->add_field( $section_id, 'general_css' );
		
		// Partial
	  $this->wp_customize->selective_refresh->add_partial( 'fixedtoc_css', array(
			'selector' => '#fixedtoc-style-inline-css',
			'settings' => array(
				'fixed_toc[general_css]',
			),
			'render_callback' => array( $this->obj_customize, 'update_inline_style' ),
    ) );
	}
}