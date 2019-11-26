<?php
/**
 * Nested list item element
 *
 * @since 3.0.0
 * @see Fixedtoc_Element_List_Item
 */
class Fixedtoc_Element_Nested_List_Item extends Fixedtoc_Element_List_Item {
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
	 * @param array $datum The datum of the current item.
	 * @param array $datum Data of TOC
	 */
	public function __construct( $datum, $data ) {
		$this->data = $data;
		parent::__construct( $datum );
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
		parent::set_attrs();
		
		$this->attrs['class'] .= ' ftwp-has-sub';
		
		if ( fixedtoc_is_true( 'colexp_list' ) ) {
			$this->set_col_exp_cls();
		}
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
		
		$this->attrs['class'] .= ' ftwp-' . $cls;
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
		parent::set_content();
		
		// prepend a collapse/expand icon
		if ( Fixedtoc_Conditions::show_colexp_icon() ) {
			$obj_icon = new Fixedtoc_Dom( new Fixedtoc_Element_List_Colexp_Icon( $this->datum ) );
			$this->content = $obj_icon->get_html() . $this->content;
		}
		
		// Add a sub list
		$obj_sub_list = new Fixedtoc_Dom( new Fixedtoc_Element_List_Sub( $this->datum, $this->data ) );
		$this->content .= $obj_sub_list->get_html();
	}
}