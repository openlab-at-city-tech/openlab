<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class DLM_Admin_Scripts {

	/**
	 * Setup hooks
	 */
	public function setup() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'elementor_enqueue_scripts' ) );
		add_action( 'admin_footer', array( $this, 'add_footer_styles' ), 99 );
	}

	/**
	 * Enqueue only elementor admin specific scripts
	 */
	public function elementor_enqueue_scripts() {
		$dlm = download_monitor();

		// Enqueue Edit Post JS
		wp_enqueue_script(
			'dlm_insert_download',
			plugins_url( '/assets/js/download-operations' . ( ( ! SCRIPT_DEBUG ) ? '.min' : '' ) . '.js', $dlm->get_plugin_file() ),
			array( 'jquery' ),
			DLM_VERSION,
			true
		);

		// Notices JS
		wp_enqueue_script(
			'dlm_notices',
			plugins_url( '/assets/js/notices' . ( ( ! SCRIPT_DEBUG ) ? '.min' : '' ) . '.js', $dlm->get_plugin_file() ),
			array( 'jquery' ),
			DLM_VERSION,
		);
		wp_enqueue_script( 'dlm_modal_upsells', plugins_url( '/assets/js/modal-upsells' . ( ( ! SCRIPT_DEBUG ) ? '.min' : '' ) . '.js', $dlm->get_plugin_file() ), array( 'jquery' ), DLM_VERSION, true );
		wp_add_inline_script( 'dlm_modal_upsells', 'const dlmModalUpsellsVars = { security: "' . wp_create_nonce( 'dlm_modal_upsell' ) . '", upsells: ' . json_encode( DLM_Upsells::get_modal_upsells() ) . '};', 'before' );
		wp_enqueue_style( 'dlm-upsell-modal', plugins_url( '/assets/css/dlm-modal-upsell.css', $dlm->get_plugin_file() ), array(), DLM_VERSION );

		// Make JavaScript strings translatable
		wp_localize_script( 'dlm_insert_download', 'dlm_id_strings', $this->get_strings( 'edit-post' ) );
	}
	/**
	 * Enqueue admin scripts
	 */
	public function enqueue_scripts() {
		global $pagenow, $post;

		$dlm = download_monitor();
		wp_register_style( 'dlm-welcome-style', plugins_url( '/assets/css/welcome.css', DLM_PLUGIN_FILE ), null, DLM_VERSION );
		// Enqueue Edit Post JS
		wp_enqueue_script(
			'dlm_insert_download',
			plugins_url( '/assets/js/download-operations' . ( ( ! SCRIPT_DEBUG ) ? '.min' : '' ) . '.js', $dlm->get_plugin_file() ),
			array( 'jquery' ),
			DLM_VERSION,
			true
		);

		wp_add_inline_script( 'dlm_insert_download', 'const dlm_ajax_nonce = "' . wp_create_nonce( 'dlm_ajax_nonce' ) . '";', 'before' );
		// Notices JS
		wp_enqueue_script(
			'dlm_notices',
			plugins_url( '/assets/js/notices' . ( ( ! SCRIPT_DEBUG ) ? '.min' : '' ) . '.js', $dlm->get_plugin_file() ),
			array( 'jquery' ),
			DLM_VERSION,
			true
		);
		// Enqueue script for modal upsells
		wp_enqueue_script( 'dlm_modal_upsells', plugins_url( '/assets/js/modal-upsells' . ( ( ! SCRIPT_DEBUG ) ? '.min' : '' ) . '.js', $dlm->get_plugin_file() ), array( 'jquery' ), DLM_VERSION, true );
		wp_add_inline_script( 'dlm_modal_upsells', 'const dlmModalUpsellsVars = { security: "' . wp_create_nonce( 'dlm_modal_upsell' ) . '", upsells: ' . json_encode( DLM_Upsells::get_modal_upsells() ) . '};', 'before' );
		wp_enqueue_style( 'dlm-upsell-modal', plugins_url( '/assets/css/dlm-modal-upsell.css', $dlm->get_plugin_file() ), array(), DLM_VERSION );
		// Make JavaScript strings translatable
		wp_localize_script( 'dlm_insert_download', 'dlm_id_strings', $this->get_strings( 'edit-post' ) );

		if ( $pagenow == 'post.php' || $pagenow == 'post-new.php' ) {

			// Enqueue Downloadable Files Metabox JS
			if (
				( $pagenow == 'post.php' && isset( $post ) && 'dlm_download' === $post->post_type )
				||
				( $pagenow == 'post-new.php' && isset( $_GET['post_type'] ) && 'dlm_download' == $_GET['post_type'] )
			) {
				wp_enqueue_media(
					array(
						'post' => $post->ID,
					)
				);

				// Enqueue Edit Download JS.
				wp_enqueue_script(
					'dlm_edit_download',
					plugins_url( '/assets/js/edit-download' . ( ( ! SCRIPT_DEBUG ) ? '.min' : '' ) . '.js', $dlm->get_plugin_file() ),
					array( 'jquery' ),
					DLM_VERSION,
					true
				);

				// Make JavaScript strings translatable.
				wp_localize_script( 'dlm_edit_download', 'dlm_ed_strings', $this->get_strings( 'edit-download' ) );
				wp_add_inline_script( 'dlm_edit_download', 'var dlmUploaderInstance = {}; var dlmEditInstance = {}; let downloadable_files_field; const max_file_size = ' . absint( wp_max_upload_size() ) . ';', 'before' );

				// Enqueue React File Browser app.
				$file_browser_asset = plugins_url( '/assets/js/file-browser/index.asset.php', $dlm->get_plugin_file() );
				$asset_file         = file_exists( plugin_dir_path( $dlm->get_plugin_file() ) . 'assets/js/file-browser/index.asset.php' )
					? include plugin_dir_path( $dlm->get_plugin_file() ) . 'assets/js/file-browser/index.asset.php'
					: array( 'dependencies' => array( 'wp-element', 'wp-components', 'wp-i18n', 'jquery' ), 'version' => DLM_VERSION );

				wp_enqueue_script(
					'dlm_file_browser',
					plugins_url( '/assets/js/file-browser/index.js', $dlm->get_plugin_file() ),
					$asset_file['dependencies'],
					$asset_file['version'],
					true
				);

				wp_enqueue_style(
					'dlm_file_browser',
					plugins_url( '/assets/js/file-browser/index.css', $dlm->get_plugin_file() ),
					array( 'wp-components' ),
					$asset_file['version']
				);

				wp_localize_script(
					'dlm_file_browser',
					'dlmFileBrowser',
					array(
						'ajaxUrl' => admin_url( 'admin-ajax.php' ),
						'nonce'   => wp_create_nonce( 'list-files' ),
					)
				);

				// Enqueue React Download Information app.
				$download_information_asset = plugins_url( '/assets/js/extensions/download-information.asset.php', $dlm->get_plugin_file() );
				$asset_file                 = file_exists( plugin_dir_path( $dlm->get_plugin_file() ) . 'assets/js/extensions/download-information.asset.php' )
					? include plugin_dir_path( $dlm->get_plugin_file() ) . 'assets/js/extensions/download-information.asset.php'
					: array( 'dependencies' => array( 'wp-element', 'wp-hooks' ), 'version' => DLM_VERSION );

				wp_enqueue_script(
					'dlm_download_information',
					plugins_url( '/assets/js/extensions/download-information.js', $dlm->get_plugin_file() ),
					$asset_file['dependencies'],
					$asset_file['version'],
					true
				);

				wp_enqueue_style(
					'dlm_download_information',
					plugins_url( '/assets/js/extensions/style-download-information.css', $dlm->get_plugin_file() ),
					array(),
					$asset_file['version']
				);
			}

			// Enqueue Downloadable Files Metabox JS
			if (
				( $pagenow == 'post.php' && isset( $post ) && \WPChill\DownloadMonitor\Shop\Util\PostType::KEY === $post->post_type )
				||
				( $pagenow == 'post-new.php' && isset( $_GET['post_type'] ) && \WPChill\DownloadMonitor\Shop\Util\PostType::KEY == $_GET['post_type'] )
			) {

				// Enqueue Select2
				wp_enqueue_script(
					'dlm_select2',
					plugins_url( '/assets/js/select2/select2.min.js', $dlm->get_plugin_file() ),
					array( 'jquery' ),
					DLM_VERSION,
					true
				);

				wp_enqueue_style( 'dlm_select2_css', download_monitor()->get_plugin_url() . '/assets/js/select2/select2.min.css' );

				// Enqueue Edit Product JS
				wp_enqueue_script(
					'dlm_edit_product',
					plugins_url( '/assets/js/shop/edit-product' . ( ( ! SCRIPT_DEBUG ) ? '.min' : '' ) . '.js', $dlm->get_plugin_file() ),
					array( 'jquery', 'dlm_select2' ),
					DLM_VERSION,
					true
				);

				// Make JavaScript strings translatable
				wp_localize_script( 'dlm_edit_product', 'dlm_ep_strings', $this->get_strings( 'edit-product' ) );
			}
		}

		if ( 'edit.php' == $pagenow && isset( $_GET['post_type'] ) && 'dlm_download' === $_GET['post_type'] && ! isset( $_GET['page'] ) ) {
			wp_enqueue_style( 'dlm-upsell-modal', plugins_url( '/assets/css/dlm-modal-upsell.css', $dlm->get_plugin_file() ), array(), DLM_VERSION );

			// Enqueue Settings JS
			wp_enqueue_script(
				'dlm_download_overview',
				plugins_url( '/assets/js/overview-download' . ( ( ! SCRIPT_DEBUG ) ? '.min' : '' ) . '.js', $dlm->get_plugin_file() ),
				array( 'jquery' ),
				DLM_VERSION,
				true
			);
			// Make JavaScript strings translatable
			wp_localize_script(
				'dlm_download_overview',
				'dlm_download_overview',
				array(
					'copy_shortcode'   => esc_html__( 'Copy shortcode', 'download-monitor' ),
					'shortcode_copied' => esc_html__( 'Copied', 'download-monitor' ),
				)
			);

			// Enqueue Download Duplicator JS
			wp_enqueue_script(
				'dlm_download_duplicator',
				plugins_url( '/assets/js/download-duplicator' . ( ( ! SCRIPT_DEBUG ) ? '.min' : '' ) . '.js', $dlm->get_plugin_file() ),
				array( 'jquery' ),
				DLM_VERSION,
				true
			);
		}

		if ( 'options.php' == $pagenow && isset( $_GET['page'] ) && 'dlm_legacy_upgrade' === $_GET['page'] ) {

			// Enqueue Settings JS
			wp_enqueue_script(
				'dlm_legacy_upgrader',
				plugins_url( '/assets/js/legacy-upgrader/build/bundle.js', $dlm->get_plugin_file() ),
				array(),
				DLM_VERSION,
				true
			);

			wp_localize_script(
				'dlm_legacy_upgrader',
				'dlm_lu_vars',
				array(
					'nonce'       => wp_create_nonce( 'dlm_legacy_upgrade' ),
					'assets_path' => plugins_url( '/assets/js/legacy-upgrader/build/assets/', $dlm->get_plugin_file() ),
				)
			);

			wp_enqueue_style( 'dlm_legacy_upgrader_css', download_monitor()->get_plugin_url() . '/assets/js/legacy-upgrader/build/style.css' );
		}

		// Extensions page JS/CSS is enqueued by DLM_Extensions_Assets (React app, assets/apps/extensions/).

		do_action( 'dlm_admin_scripts_after' );
	}

	/**
	 * Get JS strings
	 *
	 * @param $file
	 *
	 * @return array
	 */
	private function get_strings( $file ) {
		switch ( $file ) {
			case 'edit-post':
				$strings = array(
					'insert_download' => __( 'Insert Download', 'download-monitor' ),
				);
				break;
			case 'edit-download':
				$strings = array(
					'confirm_delete' => __( 'Are you sure you want to delete this file ? ', 'download-monitor' ),
					'browse_file'    => __( 'Browse for a file', 'download-monitor' ),
				);
				break;
			case 'reports':
				$strings = array(
					'ajax_nonce' => wp_create_nonce( 'dlm_reports_data' ),
					'img_path'   => download_monitor()->get_plugin_url() . '/assets/images/',
				);
				break;
			default:
				$strings = array();
		}

		return $strings;
	}

	/**
	 * Add footer styles
	 *
	 * @since 4.9.11
	 */
	public function add_footer_styles() {
		if ( isset( $_GET['post_type'] ) && 'dlm_download' === sanitize_text_field( wp_unslash( $_GET['post_type'] ) ) ) {
			wp_enqueue_style( 'dlm-welcome-style' );
		}
	}
}
