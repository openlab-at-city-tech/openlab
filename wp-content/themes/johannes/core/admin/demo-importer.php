<?php

require_once get_parent_theme_file_path( '/inc/merlin/vendor/autoload.php' );
require_once get_parent_theme_file_path( '/inc/merlin/class-merlin.php' );

/**
 * Merlin WP configuration file.
 */

if ( ! class_exists( 'Merlin' ) ) {
	return;
}

$strings = array(
	'admin-menu'               => esc_html__( 'Johannes Setup Wizard', 'johannes' ),
	'title%s%s%s%s'            => esc_html__( '%s%s Themes &lsaquo; Theme Setup: %s%s', 'johannes' ),
	'return-to-dashboard'     => esc_html__( 'Return to the dashboard', 'johannes' ),
	'ignore'                   => esc_html__( 'Disable this wizard', 'johannes' ),

	'btn-skip'                  => esc_html__( 'Skip', 'johannes' ),
	'btn-next'                  => esc_html__( 'Next', 'johannes' ),
	'btn-start'                 => esc_html__( 'Start', 'johannes' ),
	'btn-no'                    => esc_html__( 'Cancel', 'johannes' ),
	'btn-plugins-install'       => esc_html__( 'Install', 'johannes' ),

	'btn-child-install'         => esc_html__( 'Install', 'johannes' ),
	'btn-content-install'       => esc_html__( 'Install', 'johannes' ),
	'btn-import'                => esc_html__( 'Import', 'johannes' ),
	'btn-license-activate'     => esc_html__( 'Activate', 'johannes' ),
	'btn-license-skip'         => esc_html__( 'Later', 'johannes' ),

	'welcome-header%s'         => esc_html__( 'Welcome to %s', 'johannes' ),
	'welcome-header-success%s' => esc_html__( 'Hi. Welcome back', 'johannes' ),
	'welcome%s'                => esc_html__( 'This wizard will set up your theme, install plugins, and import content. It is optional & should take only a few minutes.', 'johannes' ),
	'welcome-success%s'        => esc_html__( 'You may have already run this theme setup wizard. If you would like to proceed anyway, click on the "Start" button below.', 'johannes' ),

	'license-header%s'         => esc_html__( 'Activate %s', 'johannes' ),
	'license-header-success%s' => esc_html__( '%s is Activated', 'johannes' ),
	'license%s'                => esc_html__( 'Enter your license key to enable remote updates and theme support.', 'johannes' ),
	'license-label'            => esc_html__( 'License key', 'johannes' ),
	'license-success%s'        => esc_html__( 'The theme is already registered, so you can go to the next step!', 'johannes' ),
	'license-json-success%s'   => esc_html__( 'Your theme is activated! Remote updates and theme support are enabled.', 'johannes' ),
	'license-tooltip'          => esc_html__( 'Need help?', 'johannes' ),

	'child-header'         => esc_html__( 'Install Child Theme', 'johannes' ),
	'child-header-success' => esc_html__( 'You\'re good to go!', 'johannes' ),
	'child'                => esc_html__( 'Let\'s build & activate a child theme so you may easily make theme changes.', 'johannes' ),
	'child-success%s'      => esc_html__( 'Your child theme has already been installed and is now activated, if it wasn\'t already.', 'johannes' ),
	'child-action-link'    => esc_html__( 'Learn about child themes', 'johannes' ),
	'child-json-success%s' => esc_html__( 'Awesome. Your child theme has already been installed and is now activated.', 'johannes' ),
	'child-json-already%s' => esc_html__( 'Awesome. Your child theme has been created and is now activated.', 'johannes' ),

	'plugins-header'         => esc_html__( 'Install Plugins', 'johannes' ),
	'plugins-header-success' => esc_html__( 'You\'re up to speed!', 'johannes' ),
	'plugins'                => esc_html__( 'Let\'s install some essential WordPress plugins to get your site up to speed.', 'johannes' ),
	'plugins-success%s'      => esc_html__( 'The required WordPress plugins are all installed and up to date. Press "Next" to continue the setup wizard.', 'johannes' ),
	'plugins-action-link'    => esc_html__( 'Plugins', 'johannes' ),

	'import-header'      => esc_html__( 'Import Content', 'johannes' ),
	'import'             => esc_html__( 'Let\'s import content to your website, to help you get familiar with the theme.', 'johannes' ),
	'import-action-link' => esc_html__( 'Details', 'johannes' ),

	'ready-header'      => esc_html__( 'All done. Have fun!', 'johannes' ),
	'ready%s'           => esc_html__( 'Your theme has been all set up. Enjoy your new theme by %s.', 'johannes' ),
	'ready-action-link' => esc_html__( 'Extras', 'johannes' ),
	'ready-big-button'  => esc_html__( 'View your website', 'johannes' ),

	'ready-link-3' => '',
	'ready-link-2' => wp_kses( sprintf( '<a href="%1$s" target="_blank">%2$s</a>', 'https://mekshq.com/documentation/johannes/', esc_html__( 'Theme Documentation', 'johannes' ) ), array( 'a' => array( 'href' => array(), 'target' => array() ) ) ),
);

if ( johannes_is_kirki_active() ) {
	$strings['ready-link-1'] = wp_kses( sprintf( '<a href="'.add_query_arg( array( 'autofocus[panel]' => 'johannes_panel' ), admin_url( 'customize.php' ) ).'">%s</a>', esc_html__( 'Start Customizing', 'johannes' ) ), array( 'a' => array( 'href' => array(), 'target' => array() ) ) );
}

/**
 * Set directory locations, text strings, and other settings for Merlin WP.
 *
 * @since 1.0
 */
$johannes_wizard = new Merlin(

	// Configure Merlin with custom settings.
	$config = array(
		'directory'            => 'inc/merlin', // Location / directory where Merlin WP is placed in your theme.
		'merlin_url'           => 'johannes-importer', // The wp-admin page slug where Merlin WP loads.
		'parent_slug'          => 'themes.php', // The wp-admin parent page slug for the admin menu item.
		'capability'           => 'manage_options', // The capability required for this menu to be displayed to the user.
		'child_action_btn_url' => 'https://codex.wordpress.org/child_themes', // URL for the 'child-action-link'.
		'dev_mode'             => false, // Enable development mode for testing.
		'license_step'         => false, // EDD license activation step.
		'license_required'     => false, // Require the license activation step.
		'license_help_url'     => '', // URL for the 'license-tooltip'.
		'edd_remote_api_url'   => '', // EDD_Theme_Updater_Admin remote_api_url.
		'edd_item_name'        => '', // EDD_Theme_Updater_Admin item_name.
		'edd_theme_slug'       => '', // EDD_Theme_Updater_Admin item_slug.
		'ready_big_button_url' => get_home_url(), // Link for the big button on the ready step.
	),

	// Text strings.
	$strings

);


/**
 * Prepare files to import
 *
 * @since 1.0
 */
add_filter( 'merlin_import_files', 'johannes_demo_import_files' );

if ( !function_exists( 'johannes_demo_import_files' ) ):
	function johannes_demo_import_files() {
		return array(
			array(
				'import_file_name'         => 'Johannes default',
				'local_import_file'          => trailingslashit( get_template_directory() ) . 'inc/demo/default/content.xml',
				'local_import_widget_file'   => trailingslashit( get_template_directory() ) . 'inc/demo/default/widgets.wie',
				'local_import_customizer_file'   => trailingslashit( get_template_directory() ) . 'inc/demo/default/options.dat',
				//'local_import_redux'       => array( array( 'file_path'    => trailingslashit( get_template_directory() ) . '/inc/demo/default/options.json', 'option_name' => 'johannes_settings' ) ),
				'import_preview_image_url' => get_parent_theme_file_uri( '/screenshot.png' ),
				'import_notice'            => '',
				'preview_url'              => 'https://demo.mekshq.com/johannes/',
			)
		);
	}
endif;

/**
 * Execute custom code after the whole import has finished.
 *
 * @since 1.0
 */
add_action( 'merlin_after_all_import', 'johannes_merlin_after_import_setup' );
if ( !function_exists( 'johannes_merlin_after_import_setup' ) ):

	function johannes_merlin_after_import_setup( ) {

		/* Set Menus */
		$menus = array();

		$main_menu = get_term_by( 'name', 'Johannes Main', 'nav_menu' );
		if ( isset( $main_menu->term_id ) ) {
			$menus['johannes_menu_primary'] = $main_menu->term_id;
		}

		$social_menu = get_term_by( 'name', 'Johannes Social', 'nav_menu' );
		if ( isset( $social_menu->term_id ) ) {
			$menus['johannes_menu_social'] = $social_menu->term_id;
		}

		if ( !empty( $menus ) ) {
			set_theme_mod( 'nav_menu_locations', $menus );
		}


	}

endif;

/**
 * Unset the default widgets
 *
 * @return array
 * @since 1.0
 */

add_action( 'merlin_widget_importer_before_widgets_import', 'johannes_remove_widgets_before_import' );

if ( !function_exists( 'johannes_remove_widgets_before_import' ) ):
	function johannes_remove_widgets_before_import() {
		delete_option( 'sidebars_widgets' );
	}
endif;

/**
 * Unset the child theme generator step in merlin welcome panel
 *
 * @param unknown $steps
 * @return mixed
 * @since 1.0
 */

add_filter( 'johannes_merlin_steps', 'johannes_remove_child_theme_generator_from_merlin' );

if ( !function_exists( 'johannes_remove_child_theme_generator_from_merlin' ) ):
	function johannes_remove_child_theme_generator_from_merlin( $steps ) {
		unset( $steps['child'] );
		return $steps;
	}
endif;


/**
 * Stop initial redirect after theme is activated
 *
 * @since 1.0
 */

remove_action( 'after_switch_theme', array( $johannes_wizard, 'switch_theme' ) );

?>