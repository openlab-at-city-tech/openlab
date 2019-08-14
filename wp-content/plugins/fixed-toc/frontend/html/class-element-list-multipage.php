<?php
/**
 * List element for multipage
 *
 * @since 3.0.0
 * @see Fixedtoc_Element
 */
class Fixedtoc_Element_List_Multipage extends Fixedtoc_Element_List {

	/**
	 * Set the Content inner tags.
	 *
	 * @since 3.0.0
	 * @see Fixedtoc_Element
	 *
	 * @return void
	 */
	protected function set_content() {
		global $page, $pages, $FIXEDTOC_PAGE_LINKS;
		for ( $i = 1; $i <= count( $pages ); $i++ ) {
			if ( $page == $i ) {
				continue;
			}
			$link_page = _wp_link_page( $i );
			if (preg_match('/href="(.*?)"/i', $link_page, $matches)) {
			    $FIXEDTOC_PAGE_LINKS[ $i ] = $matches[1];
			}
		}
		
		parent::set_content();
	}

}