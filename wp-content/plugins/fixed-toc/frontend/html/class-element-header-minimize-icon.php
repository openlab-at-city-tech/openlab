<?php
/**
 * Header header minimize icon element
 *
 * @since 3.0.0
 * @see Fixedtoc_Element
 */
class Fixedtoc_Element_Header_Minimize_Icon extends Fixedtoc_Element {
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
			'type' 			=> 'button',
			'id' 				=> 'ftwp-header-minimize',
			'class'			=> $this->get_cls(),
//			'title'			=> __( 'Click To minimize The Table Of Contents', 'fixedtoc' )
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
		$cls = '';
		
		if ( ! fixedtoc_is_true( 'in_widget' ) && ! fixedtoc_is_true( 'in_post' ) ) {
			$cls = 'ftwp-icon-minimize';
		}
		
		if ( fixedtoc_is_true( 'in_post' ) && fixedtoc_is_true( 'contents_collapse_init' ) ) {
			$cls = 'ftwp-icon-collapse';
		} else {
			$cls = 'ftwp-icon-expand';
		}
		
// 		return $cls;
	}
}