<?php
/**
 * List item element
 *
 * @since 3.0.0
 * @see Fixedtoc_Element
 */
class Fixedtoc_Element_List_Item extends Fixedtoc_Element {
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
	 * @param array $datum
	 *        	The datum of the current item.
	 */
	public function __construct( $datum ) {
		$this->datum = $datum;
		parent::__construct();
	}
	
	/**
	 * A datum
	 *
	 * @since 3.0.0
	 * @access protected
	 * @var array
	 */
	protected $attrs = array();

	/**
	 * Set the tag name.
	 *
	 * @since 3.0.0
	 * @see Fixedtoc_Element
	 *
	 * @return void
	 */
	protected function set_tagname() {
		$this->tagname = 'li';
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
		$this->attrs = array( 'class' => 'ftwp-item' );
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
		require_once 'class-element-list-anchor.php';
		if ( isset( $this->datum['page'] ) ) {
			require_once 'class-element-list-anchor-multipage.php';
			$obj_anchor = new Fixedtoc_Dom( new Fixedtoc_Element_List_Anchor_Multipage( $this->datum ) );
		} else {
			$obj_anchor = new Fixedtoc_Dom( new Fixedtoc_Element_List_Anchor( $this->datum ) );
		}
		
		$this->content = $obj_anchor->get_html();
	}

}