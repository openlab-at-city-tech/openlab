<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'YayMailWCSettingsBanner' ) ) {
	class YayMailWCSettingsBanner {
		protected static $instance = null;
		private $nonce         = '';
		public $plugin_dir_url  = '';

		public function __construct() {
			$this->nonce          = wp_create_nonce( 'yaymail_banner_install-plugin_yaymail' );
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
				if ( function_exists( 'njt_ads_toggle_is_enabled' ) && ! njt_ads_toggle_is_enabled( 'yaymail-wc-settings-banner' ) ) {
					return;
				}

				$banner_notification = get_option( 'yaymail_wc_settings_banner_notification' );
				if ( $banner_notification !== false && time() < $banner_notification  ) {
					return;
				}
				// admin_enqueue_scripts fires reliably on all admin pages (incl. React SPAs)
				add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
				add_action( 'admin_notices', array( $this, 'banner_render' ) );
				add_action( 'wp_ajax_yaymail_banner_dismiss', array( $this, 'ajax_dismiss_plugin' ) );
				add_action( 'wp_ajax_yaymail_banner_install_activate', array( $this, 'ajax_install_activate_yaymail' ) );
			});
		}

		public function enqueue_assets() {
			$screen = get_current_screen();
			if ( ! $screen || ! in_array( $screen->id, array( 'woocommerce_page_wc-settings', 'woocommerce_page_wc-admin' ) ) ) {
				return;
			}

			$is_marketplace = ( $screen->id === 'woocommerce_page_wc-admin' );

			wp_enqueue_script( 'yaymail-wc-settings-banner', $this->plugin_dir_url . 'assets/js/script.js', array( 'jquery' ), '1.0', true );
			wp_localize_script(
				'yaymail-wc-settings-banner',
				'yaymailWcSettingsBanner',
				array(
					'nonce'          => $this->nonce,
					'is_installed'   => (bool) $this->get_yaymail_plugin_file(),
					'yaymailUrl'     => admin_url( 'admin.php?page=yaymail-settings#/email-templates' ),
					'is_marketplace' => $is_marketplace,
					'imageUrl'       => $this->plugin_dir_url . 'assets/images/yaymail-wc-settings-banner.png',
					'i18n'           => array(
						'title'       => 'Email Customizer for WooCommerce',
						'desc'        => 'YayMail helps you easily customize your WooCommerce emails with email builder. Try it today!',
						'btnInstall'  => 'Install for Free',
						'btnActivate' => 'Activate Plugin',
						'dismiss'     => 'No, Thanks',
						'imgAlt'      => 'YayMail Email Builder'
					),
				)
			);
		}

		/**
		 * Returns the plugin file path (e.g. "yaymail/yaymail.php") if YayMail is
		 * installed, or false if it is not.
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
			$cache = false;
			$all_plugins = get_plugins();
			foreach ( $all_plugins as $plugin_file => $plugin_data ) {
				$text_domain = isset( $plugin_data['TextDomain'] ) ? strtolower( $plugin_data['TextDomain'] ) : '';
				if ( basename( $plugin_file ) === 'yaymail.php' && $text_domain === 'yaymail' ) {
					$cache = $plugin_file;
					break;
				}
			}
			return $cache;
		}

		public function ajax_dismiss_plugin() {
			check_ajax_referer( 'yaymail_banner_install-plugin_yaymail', 'nonce', true );

			$days = isset( $_POST['days'] ) ? (int) sanitize_text_field( $_POST['days'] ) : 30;
			$time = time() + ( $days * 60 * 60 * 24 );

			update_option( 'yaymail_wc_settings_banner_notification', $time );
			wp_send_json_success();
		}

		public function ajax_install_activate_yaymail() {
			if ( ! function_exists( 'current_user_can' ) || ! current_user_can( 'install_plugins' ) ) {
				wp_send_json_error( array( 'message' => 'You do not have permission to install plugins.' ) );
			}
			check_ajax_referer( 'yaymail_banner_install-plugin_yaymail', 'nonce', true );

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

		public function banner_render() {
			$screen = get_current_screen();
			// Only output the PHP notice on the settings page; marketplace banner is injected by JS
			if ( ! $screen || $screen->id !== 'woocommerce_page_wc-settings' ) {
				return;
			}

			?>
				<div class="notice notice-info is-dismissible" id="yaymail-banner">
					<div class="yaymail-banner-wrapper">
						<div class="yaymail-banner-content">
							<h3><?php echo esc_html( 'Email Customizer for WooCommerce' ); ?></h3>
							<p><?php echo esc_html( 'YayMail helps you easily customize your WooCommerce emails with email builder. Try it today!' ); ?></p>
							<p class="yaymail-banner-actions">
								<button type="button" class="button button-primary yaymail-banner-install-yaymail">
									<?php if ( $this->get_yaymail_plugin_file() ) : ?>
										<?php echo esc_html( 'Activate Plugin' ); ?>
									<?php else : ?>
										<?php echo esc_html( 'Install for Free' ); ?>
									<?php endif; ?>
								</button>
								<a href="javascript:;" id="yaymail-banner-dismiss" class="yaymail-banner-inline-dismiss"><?php echo esc_html( 'No, Thanks' ); ?></a>
							</p>
						</div>
						<div class="yaymail-banner-image">
							<img src="<?php echo esc_url( $this->plugin_dir_url . 'assets/images/yaymail-wc-settings-banner.png' ); ?>" alt="<?php echo esc_attr( 'YayMail Email Builder' ); ?>" />
						</div>
					</div>
				</div>
				<style>
					#yaymail-banner .yaymail-banner-wrapper {
						display: flex;
						flex-wrap: wrap;
						align-items: center;
						justify-content: space-between;
						padding: 12px 0;
						gap: 20px;
					}
					#yaymail-banner .yaymail-banner-content {
						flex: 1;
					}
					#yaymail-banner .yaymail-banner-content h3 {
						margin: 0 0 8px;
						font-size: 14px;
					}
					#yaymail-banner .yaymail-banner-content p {
						margin: 0 0 12px;
					}
					#yaymail-banner .yaymail-banner-actions {
						margin: 0 !important;
					}
					#yaymail-banner-noti-dismiss {
						margin-left: 10px;
						text-decoration: none;
					}
					#yaymail-banner .yaymail-banner-image {
						flex-shrink: 0;
						margin-right: 30px;
					}
					#yaymail-banner .yaymail-banner-image img {
						display: block;
						width: auto;
						height: 120px;
					}

					#yaymail-banner #yaymail-banner-dismiss{
						margin-left: 10px;
						text-decoration: none;
					}
					#yaymail-banner .updating-message::before {
						color: #d63638;
					}
				</style>
			<?php
		}
	}
}

YayMailWCSettingsBanner::get_instance();
