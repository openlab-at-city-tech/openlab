/**
 * Initialise Superfish with custom arguments.
 *
 * @package Genesis\JS
 * @author StudioPress
 * @license GPL-2.0-or-later
 */

jQuery(function ($) {
	'use strict';
	$( '.js-superfish' ).superfish({
		'delay': 100,                                         // 0.1 second delay on mouseout.
		'animation':   {'opacity': 'show', 'height': 'show'}, // Default os fade-in and slide-down animation.
		'dropShadows': false                                  // Disable drop shadows.
	});
});
