<?php
/**
 * List anchor element for multipage
 *
 * @since 3.0.0
 * @see Fixedtoc_Element
 */
class Fixedtoc_Element_List_Anchor_Multipage extends Fixedtoc_Element_List_Anchor {

	/**
	 * Set the attributes array.
	 *
	 * @since 3.0.0
	 * @see Fixedtoc_Element
	 *
	 * @return void
	 */
	protected function set_attrs() {
		global $page, $FIXEDTOC_PAGE_LINKS;
		if ( $this->datum['page'] == $page ) {
			$this->attrs = array( 'class' => 'ftwp-anchor', 'href' => '#' . $this->datum['id'] );
		} else {
			$link_prefix = $FIXEDTOC_PAGE_LINKS[ $this->datum['page'] ];
			$this->attrs = array( 'class' => 'ftwp-anchor ftwp-otherpage-anchor', 'href' => $link_prefix . '#' . $this->datum['id'] );
		}
	}

}