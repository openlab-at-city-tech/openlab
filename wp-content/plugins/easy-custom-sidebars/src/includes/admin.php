<?php
/**
 * Theme Admin Functionality
 *
 * Registers any functionality to use within
 * the admin area.
 *
 * @package Easy_Custom_Sidebars
 * @author  Sunny Johal - Titanium Themes <support@titaniumthemes.com>
 */

namespace ECS\Admin;

use ECS\Setup;

/**
 * About Page Redirect
 *
 * Redirects the user to the about page once
 * when they have upgraded or activated the
 * plugin for the first time.
 *
 * @since 2.0.0
 */
function maybe_redirect_to_about_page() {
	$about_page     = 'admin.php?page=easy-custom-sidebars&screen=about';
	$force_redirect = \intval( get_option( 'ecs_force_user_redirect', false ) ) === get_current_user_id();
	$version        = get_option( 'ecs_version', false );
	$latest_version = '2.0.0';

	if ( wp_doing_ajax() ) {
		return;
	}

	if ( $force_redirect || version_compare( $version, $latest_version ) === -1 ) {
		update_option( 'ecs_force_user_redirect', false );
		update_option( 'ecs_version', $latest_version );
		wp_safe_redirect( $about_page );
		exit;
	}
}
add_action( 'admin_init', __NAMESPACE__ . '\\maybe_redirect_to_about_page' );

/**
 * Add Admin Plugin Settings Page
 */
function add_plugin_settings_page() {
	add_theme_page(
		_x( 'Sidebar Replacements', 'The text to be displayed in the title tags of the page when the menu is selected.', 'easy-custom-sidebars' ),
		_x( 'Sidebar Replacements', 'The text to be used for the menu.', 'easy-custom-sidebars' ),
		'edit_theme_options',
		'easy-custom-sidebars',
		__NAMESPACE__ . '\\get_plugin_settings_page'
	);
}
add_action( 'admin_menu', __NAMESPACE__ . '\\add_plugin_settings_page' );

/**
 * Get Plugin Settings Page
 *
 * Outputs the root div which the React App
 * will use to render the settings page to
 * the DOM.
 *
 * @since 2.0.0
 */
function get_plugin_settings_page() {
	echo '<div id="ecs-root" class="wrap"></div>';
}

/**
 * Load Admin Scripts
 *
 * Enqueue the css and the js for the
 * plugin's admin screen.
 *
 * @since 2.0.0
 */
function enqueue_admin_scripts() {
	// Admin pointer.
	$show_admin_pointer = get_option( 'ecs_show_admin_pointer', false );

	if ( $show_admin_pointer && ! is_plugin_settings_page() ) {
		wp_enqueue_style( 'wp-pointer' );
		wp_enqueue_script( 'wp-pointer' );

		$pointer_asset = include plugin_dir_path( __FILE__ ) . '../dist/admin.asset.php';
		wp_enqueue_script(
			'easy-custom-sidebars/pointer',
			Setup\get_plugin_src_url() . 'dist/pointer.js',
			$pointer_asset['dependencies'],
			$pointer_asset['version'],
			true
		);
	}

	// Plugin settings.
	if ( is_plugin_settings_page() ) {
		$admin_asset = include plugin_dir_path( __FILE__ ) . '../dist/admin.asset.php';

		wp_enqueue_style(
			'easy-custom-sidebars/icons',
			'https://fonts.googleapis.com/icon?family=Material+Icons',
			[],
			'2.0.0'
		);

		// Admin css.
		wp_enqueue_style(
			'easy-custom-sidebars/admin',
			Setup\get_plugin_src_url() . 'dist/admin.css',
			[
				'wp-components',
				'wp-editor',
			],
			$admin_asset['version']
		);

		// Load scripts and translations.
		wp_enqueue_script(
			'easy-custom-sidebars/admin',
			Setup\get_plugin_src_url() . 'dist/admin.js',
			$admin_asset['dependencies'],
			$admin_asset['version'],
			true
		);

		wp_add_inline_script(
			'easy-custom-sidebars/admin',
			'easy_custom_sidebars = { admin_url: "' . admin_url() . '", num_sidebars: ' . wp_count_posts( 'sidebar_instance' )->publish . ', image_url: "' . plugins_url( 'easy-custom-sidebars' ) . '" }',
			'before'
		);

		wp_set_script_translations(
			'easy-custom-sidebars/admin',
			'easy-custom-sidebars'
		);
	}
}
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\enqueue_admin_scripts' );

/**
 * Is Plugin Settings Page
 *
 * @return boolean true|false if the user is currently
 *                 on the settings page.
 *
 * @since 2.0.0
 */
function is_plugin_settings_page() {
	return get_plugin_settings_screen_id() === get_current_screen()->id;
}

/**
 * Get Plugin Settings Page ID
 *
 * @return string plugin setting page hook slug.
 *
 * @since 2.0.0
 */
function get_plugin_settings_screen_id() {
	return 'appearance_page_easy-custom-sidebars';
}

/**
 * Add Admin Page Help Tabs
 *
 * Adds contextual help tabs to the admin
 * themes sidebar page.
 *
 * @since 2.0.0
 */
function add_help_tabs() {
	$screen = get_current_screen();

	// Content.
	$screen->add_help_tab(
		[
			'id'      => 'overview',
			'title'   => __( 'Overview', 'easy-custom-sidebars' ),
			'content' => get_help_tab_content(),
		]
	);

	// Sidebar.
	$screen->set_help_sidebar(
		'<p><strong>' . __( 'For more information:', 'easy-custom-sidebars' ) . '</strong></p>' .
		'<p><a href="' . admin_url( 'themes.php?page=easy-custom-sidebars&screen=about' ) . '">' . __( 'About Easy Custom Sidebars', 'easy-custom-sidebars' ) . '</a></p>' .
		'<p><a href="https://wordpress.org/support/plugin/easy-custom-sidebars/" target="_blank">' . __( 'Support Forums', 'easy-custom-sidebars' ) . '</a></p>' .
		'<p><a href="http://codex.wordpress.org/Function_Reference/register_sidebar" target="_blank">' . __( 'Documentation on Registering Sidebars', 'easy-custom-sidebars' ) . '</a></p>'
	);
}
add_action(
	'load-' . get_plugin_settings_screen_id(),
	__NAMESPACE__ . '\\add_help_tabs'
);

/**
 * Get Help Tab Content
 *
 * Returns the html markup to be used in
 * the help tab content on the plugin
 * settings page.
 *
 * @since 2.0.0
 */
function get_help_tab_content() {
	$content  = '<p>' . __( 'This screen is used for managing your custom sidebars. It provides a way to replace the default widget areas that have been registed with your theme. If your theme does not natively support widget areas you can learn about adding this support by following the documentation link to the side.', 'easy-custom-sidebars' ) . '</p>';
	$content .= '<p>' . __( 'From this screen you can:', 'easy-custom-sidebars' ) . '</p>';
	$content .= '<ul><li>' . __( 'Create, edit, and delete sidebar replacements', 'easy-custom-sidebars' ) . '</li>';
	$content .= '<li>' . __( 'Choose which widget area you would like to replace', 'easy-custom-sidebars' ) . '</li>';
	$content .= '<li>' . __( 'Add, organize, and modify pages/posts etc that belong to a custom sidebar', 'easy-custom-sidebars' ) . '</li></ul>';

	return $content;
}
