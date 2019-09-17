<?php
/**
 * Inline style
 *
 * @since 3.0.0
 */
class Fixedtoc_Inline_Style {
	/**
	 * Data of CSS
	 *
	 * @since 3.0.0
	 * @access private
	 * @var array
	 */
	private $data = array();
	
	/**
	 * Contructor
	 *
	 * @since 3.0.0
	 * @access public
	 */	
	public function __construct() {
		require_once 'abstract-style.php';
		
		require_once 'class-style-location.php';
		$this->add_data( new Fixedtoc_Style_Data_Location( $this ) );
		
		if ( fixedtoc_is_true( 'in_post' ) ) {
			require_once 'class-style-container-outer.php';
			$this->add_data( new Fixedtoc_Style_Data_Container_Outer( $this ) );
		}
		
		require_once 'class-style-contents.php';
		$this->add_data( new Fixedtoc_Style_Data_Contents( $this ) );
		
		require_once 'class-style-trigger.php';
		$this->add_data( new Fixedtoc_Style_Data_Trigger( $this ) );
		
		require_once 'class-style-header.php';
		$this->add_data( new Fixedtoc_Style_Data_Header( $this ) );
		
		require_once 'class-style-list.php';
		$this->add_data( new Fixedtoc_Style_Data_List( $this ) );
		
		require_once 'class-style-color.php';
		$this->add_data( new Fixedtoc_Style_Data_Color_Scheme( $this ) );
	}
	
	/**
	 * Add CSS data
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @param object $object_data
	 * @return string
	 */
	private function add_data( Fixedtoc_Style_Data $object_data ) {
		$new_data = $object_data->get_data();
		if ( $new_data && is_array( $new_data ) ) {
			$this->data = array_merge( $this->data, $new_data );
		}
	}
	
	/**
	 * Output CSS
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @param bool $compress
	 * @return string
	 */
	public function get_css( $compress = true ) {
		$css = '';
		if ( $this->data && is_array( $this->data ) ) {
			foreach ( $this->data as $datum ) {
				$selectors = isset( $datum['selectors'] ) ? $datum['selectors'] : '';
				$declaration = isset( $datum['declaration'] ) ? $datum['declaration'] : array();
				if ( $declaration ) {
					$css .= $this->create_css( $selectors, $declaration );
				}
			}
		}
		
		return wp_strip_all_tags( $css, $compress );
	}

	/**
	 * Create css from data.
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @param array $declaration
	 * @return string
	 */
	private function create_css( $selectors, $declaration ) {
		$css = '';
		if ( $selectors && $declaration ) {
			$selector_chain = $selectors;
			if ( is_array( $selectors ) ) {
				$selector_chain = implode( ", \n", $selectors );
			}
			
			$declaration_chain = '';
			foreach ( $declaration as $property => $value ) {
				if ( empty( $value ) ) {
					continue;
				}
				$declaration_chain .= "\t{$property}: $value;\n";
			}
			
			$declaration_chain = rtrim( $declaration_chain, "\n" );
			$css = $selector_chain . " {\n{$declaration_chain}\n}\n";
		}
		
		return $css;
	}
	
	/**
	* Convert a hexa decimal color code to its RGB equivalent
	*
	* @since 3.0.0
	* @access private
	*
	* @param string $hexStr (hexadecimal color value)
	* @param boolean $returnAsString (if set true, returns the value separated by the separator character. Otherwise returns associative array)
	* @param string $seperator (to separate RGB values. Applicable only if second parameter is true.)
	* @return array or string (depending on second parameter. Returns False if invalid hex color value)
	*/                                                                                                 
	public function hex2rgba( $hexStr, $opacity = false, $returnAsString = true, $seperator = ',' ) {
		$hexStr = preg_replace("/[^0-9A-Fa-f]/", '', $hexStr); // Gets a proper hex string
		$rgbArray = array();
		if (strlen($hexStr) == 6) { //If a proper hex code, convert using bitwise operation. No overhead... faster
			$colorVal = hexdec($hexStr);
			$rgbArray['red'] = 0xFF & ($colorVal >> 0x10);
			$rgbArray['green'] = 0xFF & ($colorVal >> 0x8);
			$rgbArray['blue'] = 0xFF & $colorVal;
		} elseif (strlen($hexStr) == 3) { //if shorthand notation, need some string manipulations
			$rgbArray['red'] = hexdec(str_repeat(substr($hexStr, 0, 1), 2));
			$rgbArray['green'] = hexdec(str_repeat(substr($hexStr, 1, 1), 2));
			$rgbArray['blue'] = hexdec(str_repeat(substr($hexStr, 2, 1), 2));
		} else {
				return false; //Invalid hex color code
		}
		
		$opacity_suffix = ( 0 <= $opacity && $opacity < 1 ) ? ',' . $opacity : '';
		
		return $returnAsString ? 'rgba(' . implode( $seperator, $rgbArray ) . $opacity_suffix . ')' : $rgbArray; // returns the rgb string or the associative array
	}
}