<?php
/**
 * Header element
 *
 * @since 3.0.0
 * @see Fixedtoc_Element
 */
class Fixedtoc_Element_Header extends Fixedtoc_Element {
	/**
	 * Set the tag name.
	 *
	 * @since 3.0.0
	 * @see Fixedtoc_Element
	 *
	 * @return void
	 */	
	protected function set_tagname() {
		$this->tagname = 'header';
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
			'id' 			=> 'ftwp-header',
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
		require_once 'class-element-header-control-icon.php';
		$obj_control_icon = new Fixedtoc_Dom( new Fixedtoc_Element_Header_Control_Icon );
		require_once 'class-element-header-title.php';
		$obj_header_title = new Fixedtoc_Dom( new Fixedtoc_Element_Header_title );
		require_once 'class-element-header-minimize-icon.php';
		$obj_minimize_icon = new Fixedtoc_Dom( new Fixedtoc_Element_Header_Minimize_Icon );
		$this->content = $obj_control_icon->get_html() . $obj_minimize_icon->get_html() . $obj_header_title->get_html();
	}
}