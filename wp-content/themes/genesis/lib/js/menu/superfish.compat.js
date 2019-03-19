/**
 * Add back-compat for older uses of Superfish.
 *
 * @package Genesis\JS
 * @author  StudioPress
 * @license GPL-2.0-or-later
 */

jQuery(function ($) {
	'use strict';
	$( 'a.sf-with-ul' ).append( '<span class="sf-sub-indicator"> &raquo;</span>' );
});
