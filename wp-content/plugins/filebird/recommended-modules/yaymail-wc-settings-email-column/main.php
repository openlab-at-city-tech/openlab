<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'YaymailWCSettingsEmailColumn' ) ) {
	class YaymailWCSettingsEmailColumn {
		protected static $instance = null;
		private $nonce             = '';
		public $plugin_dir_url       = '';

		public function __construct() {
			$this->nonce = wp_create_nonce( 'yaymail_wc_settings_email_column' );
			$this->plugin_dir_url = plugins_url( '', __FILE__ ) . '/';
		}

		public static function get_instance() {
			if ( null == self::$instance ) {
				self::$instance = new static();
				self::$instance->init();
			}
			return self::$instance;
		}



		public function init() {
			add_action( 'admin_init', function () {
				if ( ! function_exists( 'WC' ) || defined( 'YAYMAIL_VERSION' ) ) {
					return;
				}
				// User dismissed the column via "No, thanks" — register nothing.
				// To restore: delete_option( 'yaymail_wc_settings_email_column_hidden' ).
				if ( get_option( 'yaymail_wc_settings_email_column_hidden' ) ) {
					return;
				}
				if ( function_exists( 'njt_ads_toggle_is_enabled' ) && ! njt_ads_toggle_is_enabled( 'yaymail-wc-settings-email-column' ) ) {
					return;
				}
                add_action( 'admin_footer', array( $this, 'add_script' ) );
				add_filter( 'woocommerce_email_setting_columns', array( $this, 'woocommerce_email_setting_columns' ) );
				add_action( 'woocommerce_email_setting_column_yaymail_cs', array( $this, 'woocommerce_email_setting_column_yaymail_cs' ) );
				add_action( 'wp_ajax_yaymail_wc_settings_install_activate', array( $this, 'ajax_install_activate_yaymail' ) );
				add_action( 'wp_ajax_yaymail_wc_settings_dismiss_column', array( $this, 'ajax_dismiss_column' ) );
			});
		}

		public function add_script(){
			if ( function_exists( 'get_current_screen' ) ) {
				$screen = get_current_screen();
				if ( ! $screen || ! in_array( $screen->id, array( 'woocommerce_page_wc-settings', 'woocommerce_page_wc-addons' ) ) ) {
					return;
				}
			} else {
				return;
			}
			wp_enqueue_script( 'yaymail-wc-settings-email-column', $this->plugin_dir_url . 'assets/js/script.js', array( 'jquery' ), '1.4.0', true );
			wp_enqueue_style( 'yaymail-wc-settings-email-column', $this->plugin_dir_url . 'assets/css/style.css', array(), '1.4.0' );

			// State-aware modal copy: an already-installed (but inactive) plugin only needs activating.
			$is_installed = (bool) $this->get_yaymail_plugin_file();
			if ( $is_installed ) {
				$confirm_heading = 'Customize WooCommerce Emails';
				$confirm_body    = 'This will activate the YayMail plugin.';
				$confirm_button  = 'Activate Now';
				$help_text       = 'Drag and drop to design your emails. This will activate the YayMail plugin.';
			} else {
				$confirm_heading = 'Customize WooCommerce Emails';
				$confirm_body    = 'This will install & activate the free YayMail plugin from WordPress.org.';
				$confirm_button  = 'Install Now';
				$help_text       = 'Drag and drop to design your emails. This will install the YayMail plugin from WordPress.org';
			}

			wp_localize_script(
				'yaymail-wc-settings-email-column',
				'yaymailWCSettingsEmailColumn',
				array(
					'nonce'              => $this->nonce,
					'yaymailUrl'         => admin_url( 'admin.php?page=yaymail-settings#/email-templates' ),
					'helpText'			 => $help_text,
					'confirmHeading'     => $confirm_heading,
					'confirmBody'        => $confirm_body,
					'confirmInstall'     => $confirm_button,
					'confirmCancel'      => 'No, thanks',
					'yaymailPluginUrl'   => esc_url( 'https://wordpress.org/plugins/yaymail/' )
				)
			);
			?>
			<style>
				.wc-email-settings-table-yaymail_cs .woocommerce-help-tip {color: #999;} #tiptip_holder,#tiptip_content{max-width: 220px !important;}
			</style>
			<?php
		}

		/**
		 * Returns the plugin file path (e.g. "yaymail-pro/yaymail-pro.php") of an
		 * installed YayMail variant, or false if none is installed.
		 *
		 * Detection is by plugin FOLDER name across known YayMail slugs, preferring
		 * Pro. This is more robust than matching a single main-file name, since the
		 * variants ship different main files.
		 */
		private function get_yaymail_plugin_file( $reset = false ) {
			static $cache = null;
			if ( $reset ) {
				$cache = null;
			}
			if ( $cache !== null ) {
				return $cache;
			}
			if ( ! function_exists( 'get_plugins' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			// Folder slugs in preference order: Pro first, then free, then legacy.
			$known_slugs = array( 'yaymail-pro', 'yaymail', 'email-customizer-for-woocommerce' );

			// Map folder slug => plugin file for every installed plugin.
			$by_folder = array();
			foreach ( get_plugins() as $plugin_file => $plugin_data ) {
				$folder = dirname( $plugin_file ); // "." when a single-file plugin (no folder).
				if ( '.' !== $folder && ! isset( $by_folder[ $folder ] ) ) {
					$by_folder[ $folder ] = $plugin_file;
				}
			}

			$cache = false;
			foreach ( $known_slugs as $slug ) {
				if ( isset( $by_folder[ $slug ] ) ) {
					$cache = $by_folder[ $slug ];
					break;
				}
			}
			return $cache;
		}

		public function ajax_install_activate_yaymail() {
			if ( ! function_exists( 'current_user_can' ) || ! current_user_can( 'install_plugins' ) ) {
				wp_send_json_error( array( 'message' => 'You do not have permission to install plugins.' ) );
			}
			check_ajax_referer( 'yaymail_wc_settings_email_column', 'nonce', true );

			$plugin_file = $this->get_yaymail_plugin_file();

			// Install if not present yet
			if ( ! $plugin_file ) {
				try {
					$this->plugin_installer( 'yaymail' );
				} catch ( \Exception $e ) {
					wp_send_json_error( array( 'message' => $e->getMessage() ) );
				}
				// Re-detect after install (force reset cache)
				$plugin_file = $this->get_yaymail_plugin_file( true );
			}

			if ( ! $plugin_file ) {
				wp_send_json_error( array( 'message' => 'Could not locate YayMail plugin file after install.' ) );
			}

			// Activate
			$result = activate_plugin( $plugin_file );
			if ( is_wp_error( $result ) ) {
				wp_send_json_error( array( 'message' => $result->get_error_message() ) );
			}

			wp_send_json_success();
		}

		/**
		 * Persist the user's "No, thanks" dismissal so the column stops rendering.
		 */
		public function ajax_dismiss_column() {
			if ( ! function_exists( 'current_user_can' ) || ! current_user_can( 'manage_woocommerce' ) ) {
				wp_send_json_error( array( 'message' => 'You do not have permission to change this setting.' ) );
			}
			check_ajax_referer( 'yaymail_wc_settings_email_column', 'nonce', true );

			update_option( 'yaymail_wc_settings_email_column_hidden', 1, false );

			wp_send_json_success();
		}

		private function plugin_installer( $slug ) {
			require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
			require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
			require_once ABSPATH . 'wp-admin/includes/class-wp-ajax-upgrader-skin.php';
			require_once ABSPATH . 'wp-admin/includes/class-plugin-upgrader.php';

			$api = plugins_api(
				'plugin_information',
				array(
					'slug'   => $slug,
					'fields' => array(
						'short_description' => false,
						'sections'          => false,
						'requires'          => false,
						'rating'            => false,
						'ratings'           => false,
						'downloaded'        => false,
						'last_updated'      => false,
						'added'             => false,
						'tags'              => false,
						'compatibility'     => false,
						'homepage'          => false,
						'donate_link'       => false,
					),
				)
			);

			if ( is_wp_error( $api ) ) {
				throw new \Exception( esc_html( $api->get_error_message() ) );
			}

			$skin     = new \WP_Ajax_Upgrader_Skin();
			$upgrader = new \Plugin_Upgrader( $skin );
			$result   = $upgrader->install( $api->download_link );

			if ( is_wp_error( $result ) ) {
				throw new \Exception( esc_html( $result->get_error_message() ) );
			}
		}

		public function woocommerce_email_setting_columns( $columns ) {
			$action_column = $columns['actions'];
			unset( $columns['actions'] );
			$columns['yaymail_cs'] = 'YayMail';
			$columns['actions'] = $action_column;
			return $columns;
		}
		public function woocommerce_email_setting_column_yaymail_cs( $email ) {
			?>
			<td class="wc-email-settings-table-yaymail_cs">
				<a href="#" class="button yaymail-wc-settings-install-yaymail"><?php echo esc_html( 'Customize this email' ); ?></a>
			</td>
			<?php
		}
	}
}

YaymailWCSettingsEmailColumn::get_instance();
