<?php
/**
 * Theme-specific fixes for Typology.
 */

/**
 * Don't let Typology load its Welcome panel.
 */
add_action(
	'admin_init',
	function() {
		remove_action( 'admin_notices', 'typology_welcome_msg', 1 );
		remove_action( 'admin_notices', 'typology_update_msg', 1 );
	},
	20
);

/**
 * Remove Typology admin cruft.
 */
add_action(
	'wp_dashboard_setup',
	function() {
		remove_meta_box( 'typology_dashboard_widget', 'dashboard', 'side' );

		remove_submenu_page(
			'themes.php',
			'typology-importer'
		);
	},
	20
);

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
