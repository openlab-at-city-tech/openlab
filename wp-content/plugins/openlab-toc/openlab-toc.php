<?php
/**
 * Plugin Name: OpenLab TOC
 * Plugin URI:  https://openlab.citytech.cuny.edu/
 * Description: Modifications for Easy TOC plugin.
 * Author:      OpenLab
 * Author URI:  https://openlab.citytech.cuny.edu/
 * Version:     1.0.0
 */

namespace OpenLab\TOC;

/**
 * Don't add ToC to the contenct if widget is active.
 */
add_action( 'template_redirect', function() {
	if ( \is_active_widget( false, false, 'ezw_tco' ) ) {
		remove_filter( 'the_content', [ 'ezTOC', 'the_content' ], 100 );
	}
} );
