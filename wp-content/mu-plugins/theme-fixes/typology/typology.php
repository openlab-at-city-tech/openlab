<?php
/**
 * Theme-specific fixes for Typology.
 */

/**
 * Don't let Typology load its Welcome panel.
 */
add_filter( 'pre_option_typology_welcome_box_displayed', '__return_true' );

/**
 * Ensure that Redux Framework is activated.
 */
add_action(
	'init',
	function() {
		if ( ! class_exists( 'ReduxFramework' ) ) {
			activate_plugin( 'redux-framework/redux-framework.php' );
		}
	}
);
