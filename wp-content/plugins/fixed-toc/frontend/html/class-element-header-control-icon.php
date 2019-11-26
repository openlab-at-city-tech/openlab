<?php
/**
 * Header control icon element
 *
 * @since 3.0.0
 * @see Fixedtoc_Element
 */
class Fixedtoc_Element_Header_Control_Icon extends Fixedtoc_Element {
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
			'id' 				=> 'ftwp-header-control',
			'class' 		=> 'ftwp-icon-' . fixedtoc_get_val( 'trigger_icon' )
		);
	}
}