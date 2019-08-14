<?php
/**
 * Contents element
 *
 * @since 3.0.0
 * @see Fixedtoc_Element
 */
class Fixedtoc_Element_Contents extends Fixedtoc_Element {
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
	 * @param array $data
	 *        	Data of TOC
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
		$this->tagname = 'nav';
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
			'id' => 'ftwp-contents', 
			'class' => $this->get_cls()
		); 
		
		if ( fixedtoc_is_true( 'contents_collapse_init' ) && ( fixedtoc_is_true( 'in_post' ) || fixedtoc_is_true( 'in_widget' ) ) ) {
			$this->attrs['data-colexp'] = 'collapse'; 
		}
		
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
		// Shape
		$cls = 'ftwp-shape-' . Fixedtoc_get_val( 'contents_shape' );
		
		// Border
		$cls .= ' ftwp-border-' . Fixedtoc_get_val( 'contents_border_width' );
		
		return $cls;
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
		// Header
		require_once 'class-element-header.php';
		$obj_header = new Fixedtoc_Dom( new Fixedtoc_Element_Header() );
		
		// List
		require_once 'class-element-list.php';
		global $multipage;
		if ( $multipage ) {
			require_once 'class-element-list-multipage.php';
			$obj_list = new Fixedtoc_Dom( new Fixedtoc_Element_List_Multipage( $this->data ) );
		} else {
			$obj_list = new Fixedtoc_Dom( new Fixedtoc_Element_List( $this->data ) );
		}
		
		$this->content = $obj_header->get_html() . $obj_list->get_html();
	}

}