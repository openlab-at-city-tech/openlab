<?php

/* Define theme version */
define( 'JOHANNES_THEME_VERSION', '1.1.0' );

/* Helpers and utility functions */
include_once get_parent_theme_file_path( '/core/helpers.php' );

/* Include translation strings */
include_once get_parent_theme_file_path( '/core/translate.php' );

/* Default options */
include_once get_parent_theme_file_path( '/core/default-options.php' );

/* Load frontend scripts */
include_once get_parent_theme_file_path( '/core/enqueue.php' );

/* Template functions */
include_once get_parent_theme_file_path( '/core/template-functions.php' );

/* Menus */
include_once get_parent_theme_file_path( '/core/menus.php' );

/* Sidebars */
include_once get_parent_theme_file_path( '/core/sidebars.php' );


/* Extensions (hooks and filters to add/modify specific features ) */
include_once get_parent_theme_file_path( '/core/extensions.php' );

/* Main theme setup hook and init functions */
include_once get_parent_theme_file_path( '/core/setup.php' );


if ( is_admin() || is_customize_preview() ) {

	/* Admin helpers and utility functions  */
	include_once get_parent_theme_file_path( '/core/admin/helpers.php' );

	/* Load admin scripts */
	include_once get_parent_theme_file_path( '/core/admin/enqueue.php' );

	if( is_customize_preview() ) {
		/* Theme Options */
		include_once get_parent_theme_file_path( '/core/admin/options.php' );
	}
	
	/* Include plugins - TGM init */
	include_once get_parent_theme_file_path( '/core/admin/plugins.php' );

	/* Include AJAX action handlers */
	include_once get_parent_theme_file_path( '/core/admin/ajax.php' );

	/* Extensions ( hooks and filters to add/modify specific features ) */
	include_once get_parent_theme_file_path( '/core/admin/extensions.php' );

	/* Custom metaboxes */
	include_once get_parent_theme_file_path( '/core/admin/metaboxes.php' );

	/* Demo importer panel */
	include_once get_parent_theme_file_path( '/core/admin/demo-importer.php' );

}



?>