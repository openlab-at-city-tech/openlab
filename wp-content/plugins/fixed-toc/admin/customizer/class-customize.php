<?php
/**
 * Register customize page.
 *
 * @since 3.0.0
 */
class Fixedtoc_Customize {
	/**
	 * Options gname.
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @var string
	 */	
	private $option_name = 'fixed_toc';
	
	/**
	 * Capability.
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @var string
	 */	
	private $capability = 'manage_options';
	
	/**
	 * Panel id.
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @var string
	 */	
	private $panel_id = 'fixedtoc';
	
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
	 */	
	public function __construct() {
		add_action( 'customize_register' , array( $this, 'register' ) );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_controls_scripts' ) );
		add_action( 'customize_preview_init', array( $this, 'enqueue_preview_scripts' ) );
	}

	/**
	 * Register the plugin panel to the Customizer.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @void
	 */
	public function register( $wp_customize ) {
		$this->wp_customize = $wp_customize;
		
		// Add panel
		$this->wp_customize->add_panel( $this->panel_id, array(
			'title' 						=> __( 'Fixed TOC Plugin', 'fixedtoc' ),
			'description' 			=> __( 'These are global options. You can\'t save the option here if you have changed it by widget or post meta box.', 'fixedtoc' ),
			'capability'     		=> $this->capability,
//			'active_callback'		=> array( $this, 'has_toc_page' )
		) );

		// Register sections
		require_once 'class-customize-sections.php';
		new Fixedtoc_Customize_Sections( $this );
		
	}
	
	/**
	 * Enqueue script for customize control.
	 *
	 * @since 3.0.0
	 * @access public
	 */	
	public function enqueue_controls_scripts() {
		wp_enqueue_script( 'fixedtoc-customize-controls', plugins_url( 'controls.js', __FILE__ ), array( 'jquery', 'customize-controls' ), false, true );
	}

	/**
	 * This outputs the javascript needed to automate the live settings preview.
	 *
	 * @since 3.0.0
	 * @access public
	 */	
	public function enqueue_preview_scripts() {
		wp_enqueue_script( 'fixedtoc-customize-live-preview', plugins_url( 'live-preview.js', __FILE__ ), array( 'jquery', 'customize-preview' ), false, true );
	}
	
	/**
	 * Add a section to the panel
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @param array $args
	 * @return string
	 */	
	public function add_section( $args ) {
		$args = wp_parse_args( $args, array(
			'title'							=> '',
			'priority'					=> 10,
			'description'				=> '',
			'active_callback'		=> '',
			'capability'				=> $this->capability,
			'panel'							=> $this->panel_id,
		) );
		
		$id = 'fixedtoc-section-' . sanitize_title( $args['title'] );
		
		$t = $this->wp_customize->add_section( $id, $args );
		return $id;
	}
	
	/**
	 * Add a section to the special section
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @param string $section_id
	 * @param string $field_name
	 */	
	public function add_field( $section_id, $field_name ) {
		// Register setting
		$setting_id = $this->option_name . '[' . fixedtoc_get_field_data( $field_name, 'name' ) . ']';
		$setting_args = array(
			'default'								=> fixedtoc_get_field_data( $field_name, 'default' ),
			'type'									=> 'option',
			'capability'						=> $this->capability,
			'theme_supports'				=> '',
			'transport'							=> fixedtoc_get_field_data( $field_name, 'transport' ),
			'sanitize_callback'			=> fixedtoc_get_field_data( $field_name, 'sanitize' ),
			'sanitize_js_callback'	=> '',
		);
		
		$this->wp_customize->add_setting( $setting_id, $setting_args );
		
		// Add field
		$control_id = 'fixedtoc-field-' . sanitize_title( fixedtoc_get_field_data( $field_name, 'name' ) );
		
		$type = fixedtoc_get_field_data( $field_name, 'type' );
		switch ( $type ) {
			case 'color' : { $wp_customize_control = 'WP_Customize_Color_Control'; break; }
			default : $wp_customize_control = 'WP_Customize_Control';
		}
		
		$this->field_name = $field_name;
		$control_args = array(
			'label'						=> fixedtoc_get_field_data( $field_name, 'label' ),
			'description'			=> fixedtoc_get_field_data( $field_name, 'des' ),
			'section'					=> $section_id,
			'type'						=> $type,
			'settings'				=> $setting_id,
			'choices'					=> (array) fixedtoc_get_field_data( $field_name, 'choices' ),
			'input_attrs'			=> (array) fixedtoc_get_field_data( $field_name, 'input_attrs' )
		);
		
		$this->wp_customize->add_control( new $wp_customize_control(
			$this->wp_customize,
			$control_id,
			$control_args											 
		) );
	}

	/**
	 * Get the $wp_customize object.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @return object
	 */	
	public function get_wp_customize() {
		return $this->wp_customize;
	}
	
	/**
	 * Detect if it is toc page.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @return bool
	 */
	public function has_toc_page() {
		return fixedtoc_is_true( 'has_toc' );
	}

	/**
	 * Update inline style.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @var void
	 */
	public function update_inline_style() {
		require_once FTOC_ROOTDIR . 'frontend/style/class-inline-style.php';
		$obj_style = new Fixedtoc_Inline_Style();
		echo $obj_style->get_css( true );
		echo wp_strip_all_tags( fixedtoc_get_val( 'general_css' ), true );
	}
	
}