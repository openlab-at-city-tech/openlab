<?php
/**
 * Trigger element
 *
 * @since 3.0.0
 * @see Fixedtoc_Element
 */
class Fixedtoc_Element_Trigger extends Fixedtoc_Element {
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
			'type'		=> 'button',
			'id' 			=> 'ftwp-trigger',
			'class' 	=> $this->get_cls(),
			'title'		=> __( 'click To Maximize The Table Of Contents', 'fixedtoc' )
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
		// Shape
		$cls = 'ftwp-shape-' . Fixedtoc_get_val( 'trigger_shape' );
		
		// Border
		$cls .= ' ftwp-border-' . Fixedtoc_get_val( 'trigger_border_width' );
		
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
		require_once 'class-element-trigger-icon.php';
		$obj_icon = new Fixedtoc_Dom( new Fixedtoc_Element_Trigger_Icon );
		$this->content = $obj_icon->get_html();
	}
}