<?php
/**
 * List item text element
 *
 * @since 3.0.0
 * @see Fixedtoc_Element
 */
class Fixedtoc_Element_List_Text extends Fixedtoc_Element {
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
		$this->tagname = 'span';
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
			'class'		=> 'ftwp-text'
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
		$this->content = $this->datum['title'];
	}
}