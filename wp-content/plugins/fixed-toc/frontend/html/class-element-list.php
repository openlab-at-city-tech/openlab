<?php
/**
 * List element
 *
 * @since 3.0.0
 * @see Fixedtoc_Element
 */
class Fixedtoc_Element_List extends Fixedtoc_Element {
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
			'id' 			=> 'ftwp-list',
			'class'		=> $this->get_cls()
		);
		
		if ( fixedtoc_is_true( 'contents_collapse_init' ) && ( fixedtoc_is_true( 'in_post' ) || fixedtoc_is_true( 'in_widget' ) ) ) {
			$this->attrs['style'] = 'display: none';
		}
	}

	/**
	 * Get the class property value
	 *
	 * @since 3.0.0
	 * @see Fixedtoc_Element
	 *
	 * @return string
	 */
	private function get_cls() {
		// List style type
		$cls = 'ftwp-liststyle-' . fixedtoc_get_val( 'contents_list_style_type' );
		
		// List effect
		$cls .= ' ftwp-effect-' . fixedtoc_get_val( 'effects_active_link' );
		
		// Nested list
		if ( fixedtoc_is_true( 'nested_list' ) ) {
			$cls .= ' ftwp-list-nest';
		}
		
		// Strong the first level of list item
		if ( fixedtoc_is_true( 'strong_first_list' ) ) {
			$cls .= ' ftwp-strong-first';
		}
		
		// Collapse/expand sub list
		if ( fixedtoc_is_true( 'colexp_list' ) ) {
			$cls .= ' ftwp-colexp';
		}
		
		// Show collapse/expand icon
		if ( fixedtoc_is_true( 'show_colexp_icon' ) ) {
			$cls .= ' ftwp-colexp-icon';
		}
		
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
		require_once 'class-element-list-item.php';
		if ( fixedtoc_is_true( 'nested_list' ) ) {
			require_once 'class-element-nested-list-item.php';
			require_once 'class-element-list-sub.php';
			if ( fixedtoc_is_true( 'show_colexp_icon' ) ) {
				require_once 'class-element-list-colexp-icon.php';
			}
			
			// Nested list
			$this->set_nested_list();
		}	else {
			
			// Normal list
			$this->set_normal_list();
		}
	}

	/**
	 * Set the normal list.
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function set_normal_list() {
		foreach ( $this->data as $i => $datum ) {
			$obj_item = new Fixedtoc_Dom( new Fixedtoc_Element_List_Item( $datum ) );
			$this->content .= $obj_item->get_html();
		}
	}

	/**
	 * Set the nested list.
	 *
	 * @since 3.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function set_nested_list() {
		foreach ( $this->data as $i => $datum ) {
			if ( false === $datum['parent_id'] ) {
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