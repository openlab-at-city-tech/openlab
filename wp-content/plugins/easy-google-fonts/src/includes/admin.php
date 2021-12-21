<?php
/**
 * Theme Admin Functionality
 *
 * Registers any functionality to use within
 * the admin area.
 *
 * @package easy-google-fonts
 * @author  Sunny Johal - Titanium Themes <support@titaniumthemes.com>
 */

namespace EGF\Admin;

use EGF\Setup;

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
	$about_page     = 'options-general.php?page=easy-google-fonts&screen=about';
	$force_redirect = \intval( get_option( 'egf_force_user_redirect', false ) ) === get_current_user_id();
	$version        = get_option( 'egf_version', false );
	$latest_version = '2.0.0';

	if ( wp_doing_ajax() ) {
		return;
	}

	if ( $force_redirect || version_compare( $version, $latest_version ) === -1 ) {
		update_option( 'egf_force_user_redirect', false );
		update_option( 'egf_version', $latest_version );
		wp_safe_redirect( $about_page );
		exit;
	}
}
add_action( 'admin_init', __NAMESPACE__ . '\\maybe_redirect_to_about_page' );

/**
 * Add Admin Plugin Settings Page
 */
function add_plugin_settings_page() {
	add_options_page(
		_x( 'Easy Google Fonts', 'The text to be displayed in the title tags of the page when the menu is selected.', 'easy-google-fonts' ),
		_x( 'Easy Google Fonts', 'The text to be used for the menu.', 'easy-google-fonts' ),
		'edit_theme_options',
		'easy-google-fonts',
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
	echo '<div id="egf-root" class="wrap"></div>';
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
	$show_admin_pointer = get_option( 'egf_show_admin_pointer', false );

	if ( $show_admin_pointer && ! is_plugin_settings_page() ) {
		wp_enqueue_style( 'wp-pointer' );
		wp_enqueue_script( 'wp-pointer' );

		$pointer_asset = include plugin_dir_path( __FILE__ ) . '../dist/pointer.asset.php';

		wp_enqueue_script(
			'easy-google-fonts/pointer',
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
			'easy-google-fonts/icons',
			'https://fonts.googleapis.com/icon?family=Material+Icons',
			[],
			'2.0.0'
		);

		// Admin css.
		wp_enqueue_style(
			'easy-google-fonts/admin',
			Setup\get_plugin_src_url() . 'dist/admin.css',
			[
				'wp-components',
				'wp-editor',
			],
			$admin_asset['version']
		);

		// Load scripts and translations.
		wp_enqueue_script(
			'easy-google-fonts/admin',
			Setup\get_plugin_src_url() . 'dist/admin.js',
			$admin_asset['dependencies'],
			$admin_asset['version'],
			true
		);

		wp_add_inline_script(
			'easy-google-fonts/admin',
			'easy_google_fonts = { admin_url: "' . admin_url() . '", num_font_controls: ' . wp_count_posts( 'tt_font_control' )->publish . ', image_url: "' . plugins_url( 'easy-google-fonts' ) . '" }',
			'before'
		);

		wp_set_script_translations(
			'easy-google-fonts/admin',
			'easy-google-fonts'
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
	return 'settings_page_easy-google-fonts';
}

/**
 * Add Admin Page Help Tabs
 *
 * Adds contextual help tabs to the admin
 * font control plugin settings page.
 *
 * @since 2.0.0
 */
function add_help_tabs() {
	$screen = get_current_screen();

	$screen->add_help_tab(
		[
			'id'      => 'overview',
			'title'   => __( 'Overview', 'easy-google-fonts' ),
			'content' => get_help_tab_content(),
		]
	);

	$screen->add_help_tab(
		[
			'id'      => 'api-key',
			'title'   => __( 'Google API Key Instructions', 'easy-google-fonts' ),
			'content' => get_google_help_tab_content(),
		]
	);

	$screen->set_help_sidebar(
		'<p><strong>' . __( 'For more information:', 'easy-google-fonts' ) . '</strong></p>' .
		'<p><a href="' . admin_url( 'options-general.php?page=easy-google-fonts&screen=about' ) . '">' . __( 'About Easy Google Fonts', 'easy-google-fonts' ) . '</a></p>' .
		'<p><a href="https://titaniumthemes.com/" target="_blank">' . __( 'About Titanium Themes', 'easy-google-fonts' ) . '</a></p>' .
		'<p><a href="https://wordpress.org/support/plugin/easy-google-fonts/" target="_blank">' . __( 'Support Forums', 'easy-google-fonts' ) . '</a></p>'
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
	$content  = '<p>' . __( 'This screen is used for managing your custom font controls. It provides a way to create a custom font controls for any type of content in your theme.', 'easy-google-fonts' ) . '</p>';
	$content .= '<p>' . __( 'From this screen you can:', 'easy-google-fonts' ) . '</p>';
	$content .= '<ul><li>' . __( 'Create, edit, and delete custom font controls.', 'easy-google-fonts' ) . '</li>';
	$content .= '<li>' . __( 'Manage all of your custom font controls.', 'easy-google-fonts' ) . '</li>';
	$content .= '<li>' . __( 'Add a Google API key in order to enable automatic font updates.', 'easy-google-fonts' ) . '</li></ul>';
	$content .= '<p><strong>' . __( 'Please Note: ', 'easy-google-fonts' ) . '</strong>';
	$content .= __( 'This screen is used to manage/create new font controls. To preview fonts for each control please visit the typography section in the ', 'easy-google-fonts' );
	$content .= '<a href="' . admin_url( 'customize.php' ) . '">' . __( 'customizer', 'easy-google-fonts' ) . '</a></p>';

	return $content;
}

/**
 * Get Google Help Tab Content
 *
 * Returns the html markup to be used in
 * the help tab content on the plugin
 * settings page.
 *
 * @since 2.0.0
 */
function get_google_help_tab_content() {
	$content  = '<p><strong>' . __( 'How to get your Google API Key:', 'easy-google-fonts' ) . '</strong></p>';
	$content .= '<p>';
	$content .= '<ul>';
	$content .= '<li>' . __( 'Visit the <a href="https://code.google.com/apis/console" target="_blank">Google APIs Console</a>.', 'easy-google-fonts' ) . '</li>';
	$content .= '<li>' . __( 'Create a new project.', 'easy-google-fonts' ) . '</li>';
	$content .= '<li>' . __( 'Select APIs Under the APIs & auth menu.', 'easy-google-fonts' ) . '</li>';
	$content .= '<li>' . __( 'Turn on Web Fonts Developer API.', 'easy-google-fonts' ) . '</li>';
	$content .= '<li>' . __( 'Select Credentials under the APIs & auth menu.', 'easy-google-fonts' ) . '</li>';
	$content .= '<li>' . __( 'Create a new browser key and <strong>leave the referrer box empty</strong>.', 'easy-google-fonts' ) . '</li>';
	$content .= '<li>' . __( 'Once you have an API key, you can enter it in the Google Fonts API Key text field on the "Advanced" settings page.', 'easy-google-fonts' ) . '</li>';
	$content .= '</ul>';
	$content .= '</p>';
	$content .= '<p>' . __( 'Once you have entered a valid Google API key this plugin will automatically update itself with the latest fonts from google.', 'easy-google-fonts' ) . '</p>';

	return $content;
}
