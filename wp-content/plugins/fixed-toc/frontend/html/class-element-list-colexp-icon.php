<?php
/**
 * Collapse/expand icon element to list item that has sub list
 *
 * @since 3.0.0
 * @see Fixedtoc_Element
 */
class Fixedtoc_Element_List_Colexp_Icon extends Fixedtoc_Element {
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
		$this->tagname = 'button';
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
			'type' 		=> 'button'
		);
		
		$this->set_col_exp_cls();
	}
	
	/**
	 * Set collapse/expand class name.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */	
	protected function set_col_exp_cls() {
		$cls = 'collapse';
		if ( fixedtoc_is_true( 'accordion_list' ) ) {
			$cls = 'collapse';
		} elseif ( ( false == $this->datum['parent_id'] && fixedtoc_is_true( 'expand_1st_list' ) ) 
							|| ( 'expand_all' == fixedtoc_get_val( 'contents_list_colexp_init_state' ) ) ) {
			$cls = 'expand';
		}
		
		$this->attrs['class'] = 'ftwp-icon-' . $cls;
	}
}