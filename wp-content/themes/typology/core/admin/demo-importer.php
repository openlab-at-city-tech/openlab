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
	'admin-menu'               => esc_html__( 'Typology Setup Wizard', 'typology' ),
	'title%s%s%s%s'            => esc_html__( '%s%s Themes &lsaquo; Theme Setup: %s%s', 'typology' ),
	'return-to-dashboard'     => esc_html__( 'Return to the dashboard', 'typology' ),
	'ignore'                   => esc_html__( 'Disable this wizard', 'typology' ),

	'btn-skip'                  => esc_html__( 'Skip', 'typology' ),
	'btn-next'                  => esc_html__( 'Next', 'typology' ),
	'btn-start'                 => esc_html__( 'Start', 'typology' ),
	'btn-no'                    => esc_html__( 'Cancel', 'typology' ),
	'btn-plugins-install'       => esc_html__( 'Install', 'typology' ),

	'btn-child-install'         => esc_html__( 'Install', 'typology' ),
	'btn-content-install'       => esc_html__( 'Install', 'typology' ),
	'btn-import'                => esc_html__( 'Import', 'typology' ),
	'btn-license-activate'     => esc_html__( 'Activate', 'typology' ),
	'btn-license-skip'         => esc_html__( 'Later', 'typology' ),

	'welcome-header%s'         => esc_html__( 'Welcome to %s', 'typology' ),
	'welcome-header-success%s' => esc_html__( 'Hi. Welcome back', 'typology' ),
	'welcome%s'                => esc_html__( 'This wizard will set up your theme, install plugins, and import content. It is optional & should take only a few minutes.', 'typology' ),
	'welcome-success%s'        => esc_html__( 'You may have already run this theme setup wizard. If you would like to proceed anyway, click on the "Start" button below.', 'typology' ),

	'license-header%s'         => esc_html__( 'Activate %s', 'typology' ),
	'license-header-success%s' => esc_html__( '%s is Activated', 'typology' ),
	'license%s'                => esc_html__( 'Enter your license key to enable remote updates and theme support.', 'typology' ),
	'license-label'            => esc_html__( 'License key', 'typology' ),
	'license-success%s'        => esc_html__( 'The theme is already registered, so you can go to the next step!', 'typology' ),
	'license-json-success%s'   => esc_html__( 'Your theme is activated! Remote updates and theme support are enabled.', 'typology' ),
	'license-tooltip'          => esc_html__( 'Need help?', 'typology' ),

	'child-header'         => esc_html__( 'Install Child Theme', 'typology' ),
	'child-header-success' => esc_html__( 'You\'re good to go!', 'typology' ),
	'child'                => esc_html__( 'Let\'s build & activate a child theme so you may easily make theme changes.', 'typology' ),
	'child-success%s'      => esc_html__( 'Your child theme has already been installed and is now activated, if it wasn\'t already.', 'typology' ),
	'child-action-link'    => esc_html__( 'Learn about child themes', 'typology' ),
	'child-json-success%s' => esc_html__( 'Awesome. Your child theme has already been installed and is now activated.', 'typology' ),
	'child-json-already%s' => esc_html__( 'Awesome. Your child theme has been created and is now activated.', 'typology' ),

	'plugins-header'         => esc_html__( 'Install Plugins', 'typology' ),
	'plugins-header-success' => esc_html__( 'You\'re up to speed!', 'typology' ),
	'plugins'                => esc_html__( 'Let\'s install some essential WordPress plugins to get your site up to speed.', 'typology' ),
	'plugins-success%s'      => esc_html__( 'The required WordPress plugins are all installed and up to date. Press "Next" to continue the setup wizard.', 'typology' ),
	'plugins-action-link'    => esc_html__( 'Plugins', 'typology' ),

	'import-header'      => esc_html__( 'Import Content', 'typology' ),
	'import'             => esc_html__( 'Let\'s import content to your website, to help you get familiar with the theme.', 'typology' ),
	'import-action-link' => esc_html__( 'Details', 'typology' ),

	'ready-header'      => esc_html__( 'All done. Have fun!', 'typology' ),
	'ready%s'           => esc_html__( 'Your theme has been all set up. Enjoy your new theme by %s.', 'typology' ),
	'ready-action-link' => esc_html__( 'Extras', 'typology' ),
	'ready-big-button'  => esc_html__( 'View your website', 'typology' ),

	'ready-link-3' => '',
	'ready-link-2' => wp_kses( sprintf( '<a href="%1$s" target="_blank">%2$s</a>', 'https://mekshq.com/documentation/typology/', esc_html__( 'Theme Documentation', 'typology' ) ), array( 'a' => array( 'href' => array(), 'target' => array() ) ) ),
);

if ( typology_is_redux_active() ) {
	$strings['ready-link-1'] = wp_kses( sprintf( '<a href="'.admin_url( 'admin.php?page=typology_options' ).'" target="_blank">%s</a>', esc_html__( 'Start Customizing', 'typology' ) ), array( 'a' => array( 'href' => array(), 'target' => array() ) ) );
}

/**
 * Set directory locations, text strings, and other settings for Merlin WP.
 *
 * @since 1.0
 */
$typology_wizard = new Merlin(

	// Configure Merlin with custom settings.
	$config = array(
		'directory'            => 'inc/merlin', // Location / directory where Merlin WP is placed in your theme.
		'merlin_url'           => 'typology-importer', // The wp-admin page slug where Merlin WP loads.
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
add_filter( 'merlin_import_files', 'typology_demo_import_files' );

if ( !function_exists( 'typology_demo_import_files' ) ):
	function typology_demo_import_files() {
		return array(
			array(
				'import_file_name'         => 'Typology default',
				'local_import_file'          => get_parent_theme_file_path( '/inc/demos/01_default/content.xml' ),
				'local_import_widget_file'   => get_parent_theme_file_path( '/inc/demos/01_default/widgets.json' ),
				'local_import_redux'       => array(
					array(
						'file_path'    => get_parent_theme_file_path( '/inc/demos/01_default/options.json' ),
						'option_name' => 'typology_settings',
					)
				),
				'import_preview_image_url' => get_parent_theme_file_uri( '/screenshot.png' ),
				'import_notice'            => '',
				'preview_url'              => 'https://demo.mekshq.com/typology/',
			)
		);
	}
endif;

/**
 * Execute custom code after the whole import has finished.
 *
 * @since 1.0
 */
add_action( 'merlin_after_all_import', 'typology_merlin_after_import_setup' );
if ( !function_exists( 'typology_merlin_after_import_setup' ) ):

	function typology_merlin_after_import_setup( ) {

		/* Set Menus */


		$menus = array();

		$main_menu = get_term_by( 'name', 'Main Menu', 'nav_menu' );
		if ( isset( $main_menu->term_id ) ) {
			$menus['typology_main_menu'] = $main_menu->term_id;
		}

		if ( !empty( $menus ) ) {
			set_theme_mod( 'nav_menu_locations', $menus );
		}


		/* Set Home Page */

		$home_page_title = 'Typology Home';

		$page = get_page_by_title( $home_page_title );

		if ( isset( $page->ID ) ) {
			update_option( 'page_on_front', $page->ID );
			update_option( 'show_on_front', 'page' );
		}

		typology_import_contact_form();

	}

endif;

/**
 * Insert WPForms contact form
 *
 * @return void
 * @since 1.3.4
 */

if ( !function_exists( 'typology_import_contact_form' ) ):
	function typology_import_contact_form( ) {
		$forms = json_decode( file_get_contents( get_parent_theme_file_path( '/inc/demos/01_default/wpforms.json' ) ), true );

		if ( ! empty( $forms ) ) {

			foreach ( $forms as $form ) {

				$title  = ! empty( $form['settings']['form_title'] ) ? $form['settings']['form_title'] : '';
				$desc   = ! empty( $form['settings']['form_desc'] ) ? $form['settings']['form_desc'] : '';
				$new_id = wp_insert_post( array(
					'post_title'   => $title,
					'post_status'  => 'publish',
					'post_type'    => 'wpforms',
					'post_excerpt' => $desc,
				) );
				if ( $new_id ) {
					$form['id'] = $new_id;
					wp_update_post(
						array(
							'ID'           => $new_id,
							'post_content' => wp_slash( wp_json_encode( $form ) ),
						)
					);
				}
			}
		}

	}
endif;

/**
 * Unset default widgets
 *
 * @return array
 * @since 1.0
 */

add_action( 'merlin_widget_importer_before_widgets_import', 'typology_remove_widgets_before_import' );

if ( !function_exists( 'typology_remove_widgets_before_import' ) ):
	function typology_remove_widgets_before_import() {
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

add_filter( 'typology_merlin_steps', 'typology_remove_child_theme_generator_from_merlin' );

if ( !function_exists( 'typology_remove_child_theme_generator_from_merlin' ) ):
	function typology_remove_child_theme_generator_from_merlin( $steps ) {
		unset( $steps['child'] );
		return $steps;
	}
endif;


/**
 * Stop initial redirect after theme is activated
 *
 * @since 1.0
 */

remove_action( 'after_switch_theme', array( $typology_wizard, 'switch_theme' ) );
?>
