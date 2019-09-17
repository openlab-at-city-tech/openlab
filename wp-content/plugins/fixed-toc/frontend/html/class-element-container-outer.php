<?php
/**
 * Container outer element
 *
 * @since 3.0.0
 * @see Fixedtoc_Element
 */
class Fixedtoc_Element_Container_outer extends Fixedtoc_Element {
	/**
	 * Data of TOC.
	 *
	 * @since 3.0.0
	 * @access protected
	 * @var array
	 */	
	protected $data;
	
	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @param array $data Data of TOC
	 */
	public function __construct( $data ) {
		$this->data = $data;
		parent::__construct();
	}
	
	/**
	 * Set the tag name.
	 *
	 * @since 3.0.0
	 * @see Fixedtoc_Element
	 *
	 * @return void
	 */	
	protected function set_tagname() {
		$this->tagname = 'div';
	}

	/**
	 * Set the attributes array.
	 *
	 * @since 3.0.0
	 * @see Fixedtoc_Element
	 *
	 * @return void
	 */	
	protected function set_attrs() {
		$this->attrs = array(
			'id' 												=> 'ftwp-container-outer',
			'class'											=> $this->get_cls()
		);
	}

	/**
	 * Get the class property value.
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @return string
	 */	
	private function get_cls() {
		// Float
		$class = 'ftwp-in-post ftwp-float-' . Fixedtoc_get_val( 'contents_float_in_post' );
		
		return $class;
	}
	
	/**
	 * Set the Content inner tags.
	 *
	 * @since 3.0.0
	 * @see Fixedtoc_Element
	 *
	 * @return void
	 */	
	protected function set_content() {
		require_once 'class-element-container.php';
		$obj_container = new Fixedtoc_Dom( new Fixedtoc_Element_Container( $this->data ) );
		$this->content = $obj_container->get_html();
	}
}