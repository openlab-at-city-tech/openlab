<?php
/**
 * Create and get Form field data.
 *
 * @since 3.0.0
 */
class Fixedtoc_Field_Data {
	/**
	 * An array of data
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @var array
	 */
	private $data = array();
	
	/*
	 * Constructor.
	 *
	 * @since 3.0.0
	 * @access public
	 */
	public function __construct() {
		require_once 'abstract-field-section-data.php';
	}

	/*
	 * Add data.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @param object $obj_section. An instance of Fixedtoc_Field_Section_Data.
	 * @return array
	 */
	public function add_data( Fixedtoc_Field_Section_Data $obj_section ) {
		$this->data = array_merge( $this->data, $obj_section->get_section_data() );
	}

	/*
	 * Get the whole data.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @return array
	 */
	public function get_data() {
		return $this->data;
	}
	
	/*
	 * Get post type choices.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @return array
	 */
	public function get_posttype_choices() {
		$obj_posttypes = get_post_types( array( 'public' => true ), 'objects' );
		$choices = array();
		if ( $obj_posttypes ) {
			foreach ( $obj_posttypes as $key => $obj_posttype ) {
				$choices[ $key ] = $obj_posttype->labels->singular_name;
			}
		}
		return $choices;
	}	
	
	/*
	 * Get font family choices.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @return array
	 */
	public function get_font_family_choices() {
		return array(
			'customize'																									=> __( 'Customize' , 'fixedtoc' ),
			'inherit'																										=> __( 'Inherit' , 'fixedtoc' ),
			"Arial, Helvetica, sans-serif" 															=> "Arial, Helvetica, sans-serif",
			"'Arial Black', Gadget, sans-serif" 												=> "'Arial Black', Gadget, sans-serif",
			"'Bookman Old Style', serif" 																=> "'Bookman Old Style', serif",
			"'Comic Sans MS', cursive" 																	=> "'Comic Sans MS', cursive",
			"Courier, monospace" 																				=> "Courier, monospace",
			"Garamond, serif" 																					=> "Garamond, serif",
			"Georgia, serif" 																						=> "Georgia, serif",
			"Impact, Charcoal, sans-serif" 															=> "Impact, Charcoal, sans-serif",
			"'Lucida Console', Monaco, monospace" 											=> "'Lucida Console', Monaco, monospace",
			"'Lucida Sans Unicode', 'Lucida Grande', sans-serif" 				=> "'Lucida Sans Unicode', 'Lucida Grande', sans-serif",
			"'MS Sans Serif', Geneva, sans-serif" 											=> "'MS Sans Serif', Geneva, sans-serif",
			"'MS Serif', 'New York', sans-serif" 												=> "'MS Serif', 'New York', sans-serif",
			"'Palatino Linotype', 'Book Antiqua', Palatino, serif" 			=> "'Palatino Linotype', 'Book Antiqua', Palatino, serif",
			"Tahoma, Geneva, sans-serif" 																=> "Tahoma, Geneva, sans-serif",
			"'Times New Roman', Times, serif" 													=> "'Times New Roman', Times, serif",
			"'Trebuchet MS', Helvetica, sans-serif" 										=> "'Trebuchet MS', Helvetica, sans-serif",
			"Verdana, Geneva, sans-serif" 															=> "Verdana, Geneva, sans-serif"	
		);
	}
	
	/*
	 * Get list style type choices.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @return array
	 */
	public function get_list_style_type_choices() {
		return array(
			'none'				=> __( 'None' , 'fixedtoc' ),
			'decimal'			=> __( 'Decimal' , 'fixedtoc' ),
			'circle'			=> __( 'Circle' , 'fixedtoc' ),
			'circle-o'		=> __( 'Empty Circle' , 'fixedtoc' ),
			'square'			=> __( 'Square' , 'fixedtoc' ),
			'square-o'		=> __( 'Empty Square' , 'fixedtoc' ),
		);
	}
		
	/*
	 * Get border width choices.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @return array
	 */
	public function get_border_width_choices() {
		return array(
			'none' 				=> __( 'None' , 'fixedtoc' ),
			'thin' 				=> __( 'Thin' , 'fixedtoc' ),
			'medium' 			=> __( 'Medium' , 'fixedtoc' ),
			'bold' 				=> __( 'Bold' , 'fixedtoc' )
		);
	}
	
	/*
	 * Get shape choices.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @param bool $circle
	 * @return array
	 */
	public function get_shape_choices( $circle = false ) {
		$choices = array(
			'square' 				=> __( 'Square' , 'fixedtoc' ),
			'round' 				=> __( 'Round' , 'fixedtoc' )
		);
		if ( $circle ) {
			$choices['circle'] = __( 'Circle' , 'fixedtoc' );
		}
		return $choices;
	}
	
}


/*
 * Get field data.
 *
 * @since 3.0.0
 *
 * @param string $field_name
 * @param string $key
 * @return mixed
 */
function fixedtoc_get_field_data( $field_name = '', $key = '' ) {
	static $data;
	if ( ! $data ) {
		$obj_field_data = new Fixedtoc_Field_Data();
		
		require_once 'class-general-field-data.php';
		require_once 'class-developer-field-data.php';
		require_once 'class-location-field-data.php';
		require_once 'class-trigger-field-data.php';
		require_once 'class-contents-field-data.php'; 
		require_once 'class-contents-header-field-data.php'; 
		require_once 'class-contents-list-field-data.php';
		require_once 'class-effects-field-data.php';
		require_once 'class-color-field-data.php';
		require_once 'class-widget-field-data.php';
		
		$obj_field_data->add_data( new Fixedtoc_Field_General_Section_Data( $obj_field_data ) );
		$obj_field_data->add_data( new Fixedtoc_Field_Developer_Section_Data($obj_field_data) );
		$obj_field_data->add_data( new Fixedtoc_Location_Debug_Section_Data( $obj_field_data ) );
		$obj_field_data->add_data( new Fixedtoc_Field_Trigger_Section_Data( $obj_field_data ) );
		$obj_field_data->add_data( new Fixedtoc_Field_Contents_Section_Data( $obj_field_data ) );
		$obj_field_data->add_data( new Fixedtoc_Field_Contents_Header_Section_Data( $obj_field_data ) );
		$obj_field_data->add_data( new Fixedtoc_Field_Contents_List_Section_Data( $obj_field_data ) );
		$obj_field_data->add_data( new Fixedtoc_Field_Effects_Section_Data( $obj_field_data ) );
		$obj_field_data->add_data( new Fixedtoc_Field_Color_Section_Data( $obj_field_data ) );
		$obj_field_data->add_data( new Fixedtoc_Field_Widget_Section_Data( $obj_field_data ) );
		
		$data = $obj_field_data->get_data();
	}

	if ( $field_name && $key ) {
		return isset( $data[ $field_name ][ $key ] ) ? $data[ $field_name ][ $key ] : '';
	} elseif ( $field_name ) {
		return isset( $data[ $field_name ] ) ? $data[ $field_name ] : array();
	} else {
		return $data;
	}
}