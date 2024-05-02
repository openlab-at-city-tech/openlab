<?php
/**
 * Template part for displaying a search.
 *
 * @package kadence
 */

namespace Kadence;

if ( function_exists( 'is_bbpress' ) && is_bbpress() ) {
	bbp_get_template_part( 'form', 'search' );
} else {
	get_search_form();
}
