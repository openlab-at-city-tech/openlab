<?php
/**
 * Sub list element
 *
 * @since 3.0.0
 * @see Fixedtoc_Element
 */
class Fixedtoc_Element_List_Sub extends Fixedtoc_Element {
	/**
	 * The datum of the parent list item.
	 *
	 * @since 3.0.0
	 * @access protected
	 * @var array
	 */	
	protected $datum;

	/**
	 * Data of the TOC.
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
	 * @param array $datum The datum of the parent list item.
	 * @param array $data Data of TOC
	 */
	public function __construct( $datum, $data ) {
		$this->datum = $datum;
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
		$this->tagname = 'ol';
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
			'class'		=> 'ftwp-sub'
		);
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
		foreach ( $this->data as $i => $datum ) {
			if ( $this->datum['id'] == $datum['parent_id'] ) {
				$next_datum = isset( $this->data[ $i + 1 ] ) ? $this->data[ $i + 1 ] : NULL;
				if ( $next_datum && $next_datum['parent_id'] == $datum['id'] ) {
					$obj_item = new Fixedtoc_Dom( new Fixedtoc_Element_Nested_List_Item( $datum, $this->data ) );
				} else {
					$obj_item = new Fixedtoc_Dom( new Fixedtoc_Element_List_Item( $datum ) );
				}
				
				$this->content .= $obj_item->get_html();
			}	
		}
	}
}