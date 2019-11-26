<?php
/**
 * Container element
 *
 * @since 3.0.0
 * @see Fixedtoc_Element
 */
class Fixedtoc_Element_Container extends Fixedtoc_Element {
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
			'id' 			=> 'ftwp-container',
			'class'		=> $this->get_cls()
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
		$class = 'ftwp-wrap';
		
		// Initial set to hidden state.
		$class .= ' ftwp-hidden-state';
		
		// Minimize/maximize
		if ( 'hide' == Fixedtoc_get_val( 'trigger_initial_visibility' ) ) {
			$class .= ' ftwp-maximize';
		} else {
			$class .= ' ftwp-minimize';
		}		
		
		// Fixed to post
//		if ( ! fixedtoc_is_true( 'in_post' ) && ! fixedtoc_is_true( 'in_widget' ) ) {
//			$class .= ' ftwp-fixed-to-post';
//		}
		
		// Fixed Position
		$class .= ' ftwp-' . Fixedtoc_get_val( 'location_fixed_position' );
		
		// Minimize/Maximize effect
//		$class .= ' ftwp-animate-' . Fixedtoc_get_val( 'effects_in_out' );
		
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
		// Trigger
		require_once 'class-element-trigger.php';
		$obj_trigger = new Fixedtoc_Dom( new Fixedtoc_Element_Trigger() );
		$this->content = $obj_trigger->get_html();
		
		// Contetns
		require_once 'class-element-contents.php';
		$obj_contents = new Fixedtoc_Dom( new Fixedtoc_Element_Contents( $this->data ) );
		$this->content .= $obj_contents->get_html();
	}
}