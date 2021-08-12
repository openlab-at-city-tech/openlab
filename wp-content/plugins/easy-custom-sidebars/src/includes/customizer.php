<?php
/**
 * Customizer
 *
 * Registers any functionality to use within
 * the customizer.
 *
 * @package Easy_Custom_Sidebars
 * @author  Sunny Johal - Titanium Themes <support@titaniumthemes.com>
 */

namespace ECS\Customizer;

use ECS\Frontend;

/**
 * Prepare Custom Sidebars for Customizer
 *
 * Calls dynamic_sidebar() for each custom sidebar
 * in the output buffer so that it can be detected
 * and shown in the WordPress customizer.
 */
function prepare_sidebars_for_customizer() {
	if ( ! is_customize_preview() ) {
		return;
	}

	$all_replacements = Frontend\get_all_sidebar_replacements();

	ob_start();

	foreach ( $all_replacements as $sidebar_id => $replacement_id ) {
		dynamic_sidebar( $replacement_id );
	}

	ob_end_clean();
}
add_action( 'wp_footer', __NAMESPACE__ . '\\prepare_sidebars_for_customizer' );
