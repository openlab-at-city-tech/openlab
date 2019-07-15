<?php
/**
 * List anchor element
 *
 * @since 3.0.0
 * @see Fixedtoc_Element
 */
class Fixedtoc_Element_List_Anchor extends Fixedtoc_Element {
	/**
	 * The datum of the current item.
	 *
	 * @since 3.0.0
	 * @access protected
	 * @var array
	 */	
	protected $datum;
	
	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @param array $datum The datum of the current item.
	 */
	public function __construct( $datum ) {
		$this->datum = $datum;
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
		$this->tagname = 'a';
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
			'class'		=> 'ftwp-anchor',
			'href'		=> '#' . $this->datum['id']
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
		require_once 'class-element-list-text.php';
		$obj_text = new Fixedtoc_Dom( new Fixedtoc_Element_List_Text( $this->datum ) );
		$this->content = $obj_text->get_html();
	}
}