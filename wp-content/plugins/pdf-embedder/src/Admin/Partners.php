<?php

namespace PDFEmbedder\Admin;

use WPPDF_Skin;
use Plugin_Upgrader;

/**
 * Partners class handles plugins list rendering and installation.
 *
 * @since 4.7.0
 */
class Partners {

	/**
     * Assign all hooks to proper places.
	 *
	 * @since 4.7.0
	 */
	public function hooks() {

		add_action( 'wp_ajax_pdfemb_partners_install', [ $this, 'install_partner' ] );
		add_action( 'wp_ajax_pdfemb_partners_activate', [ $this, 'activate_partner' ] );
		add_action( 'wp_ajax_pdfemb_partners_deactivate', [ $this, 'deactivate_partner' ] );
	}

	/**
	 * Render a list of plugin cards.
	 *
     * @since 4.7.0
	 */
	public function show() {

		wp_localize_script(
			'pdfemb_admin',
			'pdfemb_args',
			[
				'activate_nonce'   => wp_create_nonce( 'pdfemb-activate-partner' ),
				'active'           => esc_html__( 'Status: Active', 'pdf-embedder' ),
				'activate'         => esc_html__( 'Activate', 'pdf-embedder' ),
				'activating'       => esc_html__( 'Activating...', 'pdf-embedder' ),
				'ajax'             => admin_url( 'admin-ajax.php' ),
				'deactivate'       => esc_html__( 'Deactivate', 'pdf-embedder' ),
				'deactivate_nonce' => wp_create_nonce( 'pdfemb-deactivate-partner' ),
				'deactivating'     => esc_html__( 'Deactivating...', 'pdf-embedder' ),
				'inactive'         => esc_html__( 'Status: Inactive', 'pdf-embedder' ),
				'install'          => esc_html__( 'Install', 'pdf-embedder' ),
				'install_nonce'    => wp_create_nonce( 'pdfemb-install-partner' ),
				'installing'       => esc_html__( 'Installing...', 'pdf-embedder' ),
				'proceed'          => esc_html__( 'Proceed', 'pdf-embedder' ),
			]
		);

		foreach ( $this->get_plugins() as $plugin ) {
			$this->show_plugin_card( $plugin );
		}
	}

	/**
	 * Show a single plugin card in a grid.
	 *
	 * @since 4.7.0
	 *
	 * @param array $plugin Plugin data.
	 */
	public function show_plugin_card( array $plugin ) {

		if ( ! $plugin ) {
			return;
		}

		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$installed_plugins = get_plugins();
		$status            = [];

		if ( ! isset( $installed_plugins[ $plugin['basename'] ] ) ) {
			$status['label']        = __( 'Not Installed', 'pdf-embedder' );
			$status['action_class'] = 'pdfemb-partners-install';
			$status['action_label'] = __( 'Install Plugin', 'pdf-embedder' );
		} elseif ( is_plugin_active( $plugin['basename'] ) ) {
			$status['label']        = __( 'Active', 'pdf-embedder' );
			$status['action_class'] = 'pdfemb-partners-deactivate';
			$status['action_label'] = __( 'Deactivate', 'pdf-embedder' );
		} else {
			$status['label']        = __( 'Inactive', 'pdf-embedder' );
			$status['action_class'] = 'pdfemb-partners-activate';
			$status['action_label'] = __( 'Activate', 'pdf-embedder' );
		}

		?>
		<div class="pdfemb-partners">
			<div class="pdfemb-partners-main">
				<div>
					<img src="<?php echo esc_url( $plugin['icon'] ); ?>" width="64px" alt=""/>
				</div>
				<div>
					<h3><?php echo esc_html( $plugin['name'] ); ?></h3>
					<p class="pdfemb-partner-excerpt">
						<?php echo esc_html( $plugin['description'] ); ?>
					</p>
				</div>
			</div>
			<div class="pdfemb-partners-footer">
				<div class="pdfemb-partner-status">
					<?php
					printf( /* translators: %s - status. */
						esc_html__( 'Status: %s', 'pdf-embedder' ),
						'<span>' . esc_html( $status['label'] ) . '</span>'
					);
					?>
				</div>
				<div class="pdfemb-partners-install-wrap">
					<span class="spinner"></span>
					<a href="#" target="_blank"
						class="button button-primary pdfemb-partners-button <?php echo esc_attr( $status['action_class'] ); ?>"
						data-url="<?php echo esc_url( $plugin['url'] ); ?>"
						data-basename="<?php echo esc_attr( $plugin['basename'] ); ?>">
						<?php echo esc_html( $status['action_label'] ); ?>
					</a>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Get the list of plugins we recommend.
	 *
	 * @since 4.7.0
	 */
	public function get_plugins(): array {

		return [
			'google_apps_login'     => [
				'name'        => 'Google Apps Login',
				'description' => 'Simple secure login and user management through your Google Workspace (uses secure OAuth2, and MFA if enabled).',
				'icon'        => plugins_url( 'assets/img/partners/google-apps.png', PDFEMB_PLUGIN_FILE ),
				'url'         => 'https://downloads.wordpress.org/plugin/google-apps-login.zip',
				'basename'    => 'google-apps-login/google_apps_login.php',

			],
			'google_drive_embedder' => [
				'name'        => 'Google Drive Embedder',
				'description' => 'Browse for files in your Google Drive and embed them directly in your content. This plugin requires Google Apps Login.',
				'icon'        => plugins_url( 'assets/img/partners/google-drive.png', PDFEMB_PLUGIN_FILE ),
				'url'         => 'https://downloads.wordpress.org/plugin/google-drive-embedder.zip',
				'basename'    => 'google-drive-embedder/google_drive_embedder.php',
			],
		];
	}

	/**
	 * Activate Partner plugin.
	 *
	 * @since 4.7.0
	 */
	public function activate_partner() {

		// Run a security check first.
		check_admin_referer( 'pdfemb-activate-partner', 'nonce' );

		// Activate the addon.
		if ( isset( $_POST['basename'] ) ) {
			$activate = activate_plugin( wp_unslash( $_POST['basename'] ) );  // @codingStandardsIgnoreLine

			if ( is_wp_error( $activate ) ) {
				echo wp_json_encode( [ 'error' => $activate->get_error_message() ] );
				die;
			}
		}

		echo wp_json_encode( true );
		die;
	}

	/**
	 * Deactivate Partner plugin.
	 *
	 * @since 4.7.0
	 */
	public function deactivate_partner() {
		// Run a security check first.
		check_admin_referer( 'pdfemb-deactivate-partner', 'nonce' );

		// Deactivate the addon.
		if ( isset( $_POST['basename'] ) ) {
			deactivate_plugins( wp_unslash( $_POST['basename'] ) );  // @codingStandardsIgnoreLine
		}

		echo wp_json_encode( true );
		die;
	}

	/**
	 * Install Partner plugin.
	 *
	 * @since 4.7.0
	 */
	public function install_partner() {

		check_admin_referer( 'pdfemb-install-partner', 'nonce' );

		// Install the addon.
		if ( isset( $_POST['download_url'] ) ) {
			$download_url = esc_url_raw( wp_unslash( $_POST['download_url'] ) );

			// Set the current screen to avoid undefined notices.
			set_current_screen();

			// Prepare variables.
			$method = '';
			$url    = add_query_arg( 'page', Admin::SLUG, admin_url( 'options-general.php' ) );
			$url    = esc_url( $url );

			// Start output bufferring to catch the filesystem form if credentials are needed.
			ob_start();
			$creds = request_filesystem_credentials( $url, $method, false, false, null );

			if ( $creds === false ) {
				$form = ob_get_clean();
				echo wp_json_encode( [ 'form' => $form ] );
				die;
			}

			// If we are not authenticated, make it happen now.
			if ( ! WP_Filesystem( $creds ) ) {
				ob_start();
				request_filesystem_credentials( $url, $method, true, false, null );
				$form = ob_get_clean();
				echo wp_json_encode( [ 'form' => $form ] );
				die;
			}

			// We do not need any extra credentials if we have gotten this far, so let's install the plugin.
			require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
			require_once plugin_dir_path( PDFEMB_PLUGIN_FILE ) . 'deprecated/install_skin.php';

			// Create the plugin upgrader with our custom skin.
			$installer = new Plugin_Upgrader( new WPPDF_Skin() );

			$installer->install( $download_url );

			// Flush the cache and return the newly installed plugin basename.
			wp_cache_flush();

			if ( $installer->plugin_info() ) {
				$plugin_basename = $installer->plugin_info();

				wp_send_json_success( [ 'plugin' => $plugin_basename ] );

				die();
			}
		}

		// Send back a response.
		echo wp_json_encode( true );
		die;
	}
}
