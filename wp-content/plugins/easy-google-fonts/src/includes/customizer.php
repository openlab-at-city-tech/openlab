<?php
/**
 * Customizer
 *
 * Registers any functionality to use within
 * the customizer.
 *
 * @package easy-google-fonts
 * @author  Sunny Johal - Titanium Themes <support@titaniumthemes.com>
 */

namespace EGF\Customizer;

use EGF\Setup as Setup;
use EGF\Settings as Settings;
use EGF\Sanitization as Sanitization;
use EGF\Utils as Utils;

/**
 * Enqueue Customizer Control Scripts
 */
add_action(
	'customize_controls_enqueue_scripts',
	function() {
		$customizer_asset = include plugin_dir_path( __FILE__ ) . '../dist/customizer.asset.php';

		wp_enqueue_style(
			'easy-google-fonts/customizer-controls',
			Setup\get_plugin_src_url() . 'dist/customizer.css',
			[ 'wp-components' ],
			$customizer_asset['version']
		);

		wp_enqueue_script(
			'easy-google-fonts/customizer-controls',
			Setup\get_plugin_src_url() . 'dist/customizer.js',
			$customizer_asset['dependencies'],
			$customizer_asset['version'],
			true
		);

		wp_add_inline_script(
			'easy-google-fonts/customizer-controls',
			'egfGoogleFontLanguages = []; egfGoogleFonts = {}; egfGoogleFontsByCategory = {}; egfGoogleFontsByKey = {};',
			'before'
		);

		wp_localize_script(
			'easy-google-fonts/customizer-controls',
			'egfCustomize',
			get_customizer_data()
		);
	}
);

/**
 * Enqueue Customizer Preview Scripts
 */
add_action(
	'customize_preview_init',
	function() {
		$preview_asset = include plugin_dir_path( __FILE__ ) . '../dist/preview.asset.php';

		wp_enqueue_script(
			'easy-google-fonts/customizer-preview',
			Setup\get_plugin_src_url() . 'dist/preview.js',
			$preview_asset['dependencies'],
			$preview_asset['version'],
			true
		);

		wp_localize_script(
			'easy-google-fonts/customizer-preview',
			'egfCustomizePreview',
			get_customizer_data()
		);
	}
);

/**
 * Get Customizer Data
 *
 * Loaded on the window object in the
 * customizer controls and customizer
 * preview iframe.
 *
 * @return array Arr containing customizer data props.
 */
function get_customizer_data() {
	return [
		'api_key'       => Utils\get_google_api_key(),
		'translations'  => [
			'controls' => get_control_translations(),
		],
		'permissions'   => [
			'customize'          => current_user_can( 'customize' ),
			'edit_theme_options' => current_user_can( 'edit_theme_options' ),
			'publish_pages'      => current_user_can( 'publish_pages' ),
			'upload_files'       => current_user_can( 'upload_files' ),
		],
		'default_fonts' => Utils\get_default_fonts(),
		'theme_colors'  => Utils\get_theme_color_palette(),
		'panels'        => get_panels(),
		'sections'      => get_sections(),
		'settings'      => [
			'setting_key' => 'tt_font_theme_options',
			'config'      => Settings\get_settings_config(),
			'saved'       => Settings\get_saved_settings(),
		],
	];
}

/**
 * Get Control Translations
 *
 * Returns the available translation strings
 * for each control type to use in the customizer.
 * Translations are keyed by field type.
 *
 * @return array Assoc array keyed by field type.
 */
function get_control_translations() {
	$translations = [
		'common'           => [
			'close_label'         => __( 'Close', 'easy-google-fonts' ),
			'reset_label'         => __( 'Reset', 'easy-google-fonts' ),
			'theme_default_label' => __( 'Theme Default', 'easy-google-fonts' ),
		],
		'language_control' => [
			'labels' => [
				'all_languages' => __( 'All Languages', 'easy-google-fonts' ),
			],
		],
	];
	return $translations;
}


/**
 * Dynamic Settings Registration.
 */
add_filter(
	'customize_dynamic_setting_args',
	function( $setting_args, $setting_id ) {
		$id_pattern = '/^tt_font_theme_options\[([a-zA-Z0-9_-]+)\]$/';

		preg_match( $id_pattern, $setting_id, $matches );

		$setting_key = empty( $matches ) ? false : $matches[1];

		$config = Settings\get_settings_config();

		if ( empty( $setting_key ) || empty( $config[ $setting_key ] ) ) {
			return $setting_args;
		}

		$setting_props = $config[ $setting_key ];

		$transport         = empty( $setting_props['transport'] ) ? 'refresh' : $setting_props['transport'];
		$default           = isset( $setting_props['default'] ) ? '' : $setting_props['default'];
		$sanitize_callback = Sanitization\get_setting_sanitization_callback( $setting_key );

		return [
			'default'           => $default,
			'type'              => 'option',
			'transport'         => $transport,
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => $sanitize_callback,
		];
	},
	10,
	2
);

/**
 * Dynamic Settings Registration (Nested Props).
 */
add_filter(
	'customize_dynamic_setting_args',
	function( $setting_args, $setting_id ) {
		$id_pattern = '/^tt_font_theme_options\[([a-zA-Z0-9_-]+)\]\[([a-zA-Z0-9_-]+)\]$/';

		preg_match( $id_pattern, $setting_id, $matches );

		$setting_key      = empty( $matches ) ? false : $matches[1];
		$setting_key_prop = empty( $matches ) ? false : $matches[2];

		$config = Settings\get_settings_config();

		if (
			empty( $setting_key ) ||
			empty( $setting_key_prop ) ||
			! array_key_exists( $setting_key, $config ) ||
			! array_key_exists( $setting_key_prop, $config[ $setting_key ]['default'] )
		) {
			return $setting_args;
		}

		$setting_props = $config[ $setting_key ];

		if ( 'font' === $setting_props['type'] ) {
			$transport         = empty( $setting_props['transport'] ) ? 'refresh' : $setting_props['transport'];
			$default           = isset( $setting_props['default'][ $setting_key_prop ] ) ? '' : $setting_props['default'][ $setting_key_prop ];
			$sanitize_callback = Sanitization\get_setting_prop_sanitization_callback( $setting_key_prop );

			$setting_args = [
				'default'           => $default,
				'type'              => 'option',
				'transport'         => $transport,
				'capability'        => 'edit_theme_options',
				'sanitize_callback' => $sanitize_callback,
			];
		}

		return $setting_args;
	},
	20,
	2
);

/**
 * Get Panels
 *
 * Returns an array of panels to be registered
 * in the customizer.
 *
 * @return array $panels Array of panel settings.
 */
function get_panels() {
	return apply_filters(
		'egf_customizer_get_panels',
		[
			'egf_typography_panel' => [
				'name'        => 'egf_typography_panel',
				'title'       => __( 'Typography', 'easy-google-fonts' ),
				'priority'    => 10,
				'capability'  => 'edit_theme_options',
				'description' => __( 'Your theme has typography support. You can create custom font controls on the google fonts screen in the settings section.', 'easy-google-fonts' ),
			],
		]
	);
}

/**
 * Get Sections
 *
 * Returns an array of sections to be registered
 * in the customizer. To add a section to the default
 * panel set the 'panel' array value to 'default_value'.
 * wp.customize.previewer.previewUrl.set
 *
 * @return array $sections Array of sections and their settings.
 */
function get_sections() {
	return apply_filters(
		'egf_customizer_get_sections',
		[
			'typography'       => [
				'name'             => 'typography',
				'title'            => __( 'Default Typography', 'easy-google-fonts' ),
				'description'      => __( 'Your theme has typography support. You can create additional customizer font controls on the admin settings screen.', 'easy-google-fonts' ),
				'panel'            => 'egf_typography_panel',
				'redirect_url'     => '',
				'customize_action' => get_customize_action_heading( 'egf_typography_panel' ),
			],
			'theme_typography' => [
				'name'             => 'theme_typography',
				'title'            => __( 'Theme Typography', 'easy-google-fonts' ),
				'description'      => __( 'Any font controls that you have created for your theme will appear below. You can create additional font controls for your theme on the admin settings screen.', 'easy-google-fonts' ),
				'panel'            => 'egf_typography_panel',
				'redirect_url'     => '',
				'customize_action' => get_customize_action_heading( 'egf_typography_panel' ),
			],
		]
	);
}

/**
 * Add customize action heading to each section.
 *
 * @param string $panel_id Customizer panel id.
 */
function get_customize_action_heading( $panel_id ) {
	$panels = get_panels();
	return empty( $panels[ $panel_id ] )
		? __( 'Customizing', 'easy-google-fonts' )
		/* translators: &#9656; is the unicode right-pointing triangle, and %s is the panel title in the Customizer */
		: sprintf( __( 'Customizing &#9656; %s', 'easy-google-fonts' ), $panels[ $panel_id ]['title'] );
}


/**
 * Output Inline Styles In <head>
 */
add_action(
	'wp_head',
	function() {
		if ( is_customize_preview() ) {
			?>
			<script id="egf-customizer-preview"></script>
			<?php
		}
	},
	1000
);
