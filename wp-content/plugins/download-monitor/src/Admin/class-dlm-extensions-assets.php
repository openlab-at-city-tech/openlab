<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Ported from Modula_Admin_Assets::extensions_scripts()/output_wp_css_variables().
 */
class DLM_Extensions_Assets {

	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'extensions_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'header_scripts' ) );
		add_action( 'admin_head', array( $this, 'output_wp_css_variables' ) );
	}

	public function extensions_scripts() {
		$screen = get_current_screen();

		if ( ! $screen || 'dlm_download_page_dlm-extensions' !== $screen->id ) {
			return;
		}

		$asset_path = plugin_dir_path( DLM_PLUGIN_FILE ) . 'assets/js/extensions/extensions.asset.php';

		if ( ! file_exists( $asset_path ) ) {
			return;
		}

		$asset = require $asset_path;

		wp_enqueue_script( 'dlm-extensions-app', DLM_URL . 'assets/js/extensions/extensions.js', $asset['dependencies'], $asset['version'], true );
		wp_enqueue_style( 'dlm-extensions-app', DLM_URL . 'assets/js/extensions/extensions.css', array( 'wp-components' ), $asset['version'] );

		wp_localize_script(
			'dlm-extensions-app',
			'dlmExtensionsStrings',
			array(
				'proExists'   => defined( 'DLM_PRO_VERSION' ),
				'proOutdated' => DLM_Pro_Compat::is_pro_outdated(),
			)
		);
	}

	/**
	 * The React page header renders on every DLM admin screen — same
	 * conditions as the legacy dlm_page_header()/page_header_locations().
	 */
	public function header_scripts() {
		$screen = get_current_screen();

		if ( ! $screen || 'dlm_download' !== $screen->post_type ) {
			return;
		}

		$asset_path = plugin_dir_path( DLM_PLUGIN_FILE ) . 'assets/js/extensions/header.asset.php';

		if ( ! file_exists( $asset_path ) ) {
			return;
		}

		$asset = require $asset_path;

		wp_enqueue_script( 'dlm-page-header-app', DLM_URL . 'assets/js/extensions/header.js', $asset['dependencies'], $asset['version'], true );
		wp_enqueue_style( 'dlm-page-header-app', DLM_URL . 'assets/js/extensions/header.css', array(), $asset['version'] );
	}

	public function output_wp_css_variables() {
		$screen = get_current_screen();

		if ( ! $screen || 'dlm_download' !== $screen->post_type ) {
			return;
		}

		$admin_theme_color = get_user_option( 'admin_color' );
		$wp_admin_color    = '#2271b1';

		if ( function_exists( 'wp_admin_css_color' ) ) {
			global $_wp_admin_css_colors;
			if ( isset( $_wp_admin_css_colors[ $admin_theme_color ] ) ) {
				$wp_admin_color = $_wp_admin_css_colors[ $admin_theme_color ]->colors[0];
			}
		}

		$accent_color = 'var(--wp-admin-theme-color, ' . $wp_admin_color . ')';
		$accent_hover = 'var(--wp-admin-theme-color-darker-10, ' . $wp_admin_color . ')';

		echo '
		<style id="dlm-extensions-wp-styles-inline-css">
			:root {
				--theme-normal-container-max-width: 1290px;
				--theme-narrow-container-max-width: 750px;
				--theme-content-spacing: 1.5em;
				--theme-palette-color-1: ' . $accent_color . ';
				--theme-palette-color-2: ' . $accent_hover . ';
				--theme-palette-color-4: #1d2327;
				--theme-palette-color-7: #f6f7f7;
				--theme-text-color: #2c3338;
				--theme-link-initial-color: ' . $accent_color . ';
				--theme-link-hover-color: ' . $accent_hover . ';
				--theme-border-color: #c3c4c7;
			}
		</style>';
	}
}
