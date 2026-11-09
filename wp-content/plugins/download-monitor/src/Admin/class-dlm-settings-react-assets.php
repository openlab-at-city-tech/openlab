<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DLM_Settings_React_Assets {

	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'settings_scripts' ), 20 );
	}

	public function settings_scripts() {
		$screen = get_current_screen();

		if ( ! $screen || 'dlm_download_page_download-monitor-settings' !== $screen->id ) {
			return;
		}

		$asset_path = plugin_dir_path( DLM_PLUGIN_FILE ) . 'assets/js/extensions/settings.asset.php';

		if ( ! file_exists( $asset_path ) ) {
			return;
		}

		wp_enqueue_editor();

		$asset = require $asset_path;
		$deps  = $asset['dependencies'];

		foreach ( array( 'dlm-pa-listing-admin-settings', 'dlm-mailchimp-connection', 'dlm_xl_tokens_table' ) as $handle ) {
			if ( wp_script_is( $handle, 'registered' ) ) {
				$deps[] = $handle;
			}
		}

		wp_enqueue_script( 'dlm-settings-app', DLM_URL . 'assets/js/extensions/settings.js', $deps, $asset['version'], true );
		wp_enqueue_style( 'dlm-settings-app', DLM_URL . 'assets/js/extensions/settings.css', array( 'wp-components' ), $asset['version'] );

		wp_localize_script(
			'dlm-settings-app',
			'dlmSettings',
			array(
				'extensionsUrl' => admin_url( 'edit.php?post_type=dlm_download&page=dlm-extensions' ),
			)
		);
	}
}
