<?php
/**
 * Create Dom
 *
 * @since 3.0.0
 */
class Fixedtoc_Dom {
	/**
	 * An instance of the class Fixedtoc_Element.
	 *
	 * @since 3.0.0
	 * @access private
	 * @var object
	 */
	private $obj_element;

	/**
	 * The tag name.
	 *
	 * @since 3.0.0
	 * @access private
	 * @var string
	 */	
	private $tagname;

	/**
	 * Content inner a pair of tags.
	 *
	 * @since 3.0.0
	 * @access private
	 * @var string
	 */	
	private $content;

	/**
	 * An array of attributes.
	 *
	 * @since 3.0.0
	 * @access private
	 * @var array
	 */
	private $attrs;
	
	/**
	 * HTML code.
	 *
	 * @since 3.0.0
	 * @access private
	 * @var string
	 */
	private $html;

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @param object $obj_element An instance of the class Fixedtoc_Element.
	 * @param bool $created Auto create html if $created=true.
	 */
	public function __construct( $obj_element, $created = true ) {
		$this->obj_element = $obj_element;
		$this->tagname = $this->obj_element->get_tagname();
		$this->content = $this->obj_element->get_content();
		$this->attrs = $this->obj_element->get_attrs();
		
		if ( $created ) {
			$this->create_html();
		}
	}
	
	/**
	 * Get the data.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @return array.
	 */
	public function get_data() {
		return $this->data;
	}
	
	/**
	 * Create html code.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @return void.
	 */	
	public function create_html() {
		if ( $this->attrs ) {
			$str_attrs = '';
			foreach ( $this->attrs as $key => $value ) {
				$key = trim( $key );
				$value = trim( $value );
				if ( empty( $key ) || empty( $value ) ) {
					continue;
				}
				$str_attrs .= ' ' . $key . '="' . esc_attr( $value ) . '"';
			}
			$str_attrs = rtrim( $str_attrs );
		}
		
		$this->html = "<" . $this->tagname . $str_attrs . '>' . $this->content . '</' . $this->tagname . ">";
	}

	/**
	 * Prepend to $this->content.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @param string $prepend Extra content prepend to the content.
	 * @return void.
	 */		
	public function prepend_content( $prepend ) {
		$this->content = $prepend . $this->content;
	}	
	
	/**
	 * Append to $this->content.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @param string $append Extra content append to the content.
	 * @return void.
	 */		
	public function append_content( $append ) {
		$this->content .= $append;
	}	
	
	/**
	 * Insert extra class property value.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @param string $cls Extra value append to the class
	 * @return void.
	 */		
	public function inset_class_attr( $cls ) {
		if ( isset( $this->attrs['class'] ) ) {
			$this->attrs['class'] .= ' ' . $cls;
		} else {
			$this->attrs['class'] = $cls;
		}
	}

	/**
	 * Get html code.
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @return string.
	 */
	public function get_html() {
		return $this->html;
	}	
}