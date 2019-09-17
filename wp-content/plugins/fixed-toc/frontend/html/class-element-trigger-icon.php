<?php
/**
 * Trigger icon element
 *
 * @since 3.0.0
 * @see Fixedtoc_Element
 */
class Fixedtoc_Element_Trigger_Icon extends Fixedtoc_Element {
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
			'class' => 'ftwp-trigger-icon ftwp-icon-' . fixedtoc_get_val( 'trigger_icon' )
		);
	}
}