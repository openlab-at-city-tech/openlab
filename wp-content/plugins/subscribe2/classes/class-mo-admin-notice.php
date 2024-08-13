<?php

if ( ! class_exists( 'MO_Admin_Notice' ) ) {

	/**
	 * Admin notice handler class.
	 */
	class MO_Admin_Notice {

		/**
		 * Class constructor.
		 */
		public function __construct() {
			add_action( 'admin_notices', array( $this, 'admin_notice' ) );
			add_action( 'network_admin_notices', array( $this, 'admin_notice' ) );

			add_action( 'admin_init', array( $this, 'dismiss_admin_notice' ) );
		}

		/**
         * Dismiss admin notice.
         *
		 * @return void
		 */
		public function dismiss_admin_notice() {
			if ( empty( $_GET['mo-adaction'] ) || 'mo_dismiss_adnotice' !== sanitize_key( $_GET['mo-adaction'] ) ) {
				return;
			}

			$url = admin_url();
			update_option( 'mo_dismiss_adnotice', 'true' );

			wp_safe_redirect( $url );
			exit;
		}

		/**
		 * Admin notice handler.
		 *
		 * @return void
		 */
		public function admin_notice() {
			global $pagenow;

			if ( 'index.php' === $pagenow || ( isset( $_GET['page'] ) && false !== strpos( sanitize_key( $_GET['page'] ), 's2_' ) ) ) {
				if ( get_option( 'mo_dismiss_adnotice', 'false' ) === 'true' ) {
					return;
				}

				if ( $this->is_plugin_installed() && $this->is_plugin_active() ) {
					return;
				}

				$dismiss_url = esc_url_raw(
					add_query_arg(
						array(
							'mo-adaction' => 'mo_dismiss_adnotice',
						),
						admin_url()
					)
				);

				$this->notice_css();
				$install_url = wp_nonce_url(
					admin_url( 'plugin-install.php?s=wemail&tab=search&type=term' ),
					'install-plugin_mailoptin'
				);

				$activate_url = wp_nonce_url( admin_url( 'plugins.php?action=activate&plugin=wemail%2Fwemail.php' ), 'activate-plugin_wemail/wemail.php' );
				?>
				<div class="mo-admin-notice notice notice-success">
					<div class="mo-notice-first-half">
						<p>
							<?php
							printf(
							        /* translators: Placeholders: %s - mail option admin notice */
								esc_html__( 'Free email newsletter plugin that will %1$sincrease your email list subscribers%2$s and keep them engaged with %1$sautomated and schedule newsletters%2$s.', 'subscribe2' ),
								'<span class="mo-stylize"><strong>',
								'</strong></span>'
							);
							?>
						</p>
						<p style="text-decoration: underline;font-size: 12px;"><?php esc_html_e( 'Recommended by Subscribe2 plugin', 'subscribe2' ); ?></p>
					</div>
					<div class="mo-notice-other-half">
						<?php if ( ! $this->is_plugin_installed() ) : ?>
							<a class="button button-primary button-hero" id="mo-install-mailoptin-plugin" href="<?php echo esc_url( $install_url ); ?>">
								<?php esc_html_e( 'Install weMail Now for Free!', 'subscribe2' ); ?>
							</a>
						<?php endif; ?>
						<?php if ( $this->is_plugin_installed() && ! $this->is_plugin_active() ) : ?>
							<a class="button button-primary button-hero" id="mo-activate-mailoptin-plugin" href="<?php echo esc_url( $activate_url ); ?>">
								<?php esc_html_e( 'Activate weMail Now!', 'subscribe2' ); ?>
							</a>
						<?php endif; ?>
						<div class="mo-notice-learn-more">
							<a target="_blank" href="https://getwemail.io"><?php esc_html_e( 'Learn more', 'subscribe2' ); ?></a>
						</div>
					</div>
					<a href="<?php echo esc_url( $dismiss_url ); ?>">
						<button type="button" class="notice-dismiss">
							<span class="screen-reader-text"><?php esc_html_e( 'Dismiss this notice', 'subscribe2' ); ?>.</span>
						</button>
					</a>
				</div>
				<?php
			}
		}

		/**
		 * Get current admin url.
		 *
		 * @return string
		 */
		public function current_admin_url() {
			$parts = wp_parse_url( home_url() );
			$uri   = $parts['scheme'] . '://' . $parts['host'];

			if ( array_key_exists( 'port', $parts ) ) {
				$uri .= ':' . $parts['port'];
			}

			$uri .= add_query_arg( array() );

			return $uri;
		}

		/**
		 * Check if wemail installed.
		 *
		 * @return bool
		 */
		public function is_plugin_installed() {
			$installed_plugins = get_plugins();

			return isset( $installed_plugins['wemail/wemail.php'] );
		}

		/**
		 * Check if wemail activated.
		 *
		 * @return bool
		 */
		public function is_plugin_active() {
			return is_plugin_active( 'wemail/wemail.php' );
		}

		/**
		 * Admin notice additional styles.
		 *
		 * @return void
		 */
		public function notice_css() {
			?>
			<style type="text/css">
				.mo-admin-notice {
					background: #fff;
					color: #000;
					border-left-color: #46b450;
					position: relative;
				}

				.mo-admin-notice .notice-dismiss:before {
					color: #72777c;
				}

				.mo-admin-notice .button-primary {
					background: #006799;
					text-shadow: none;
					border: 0;
					box-shadow: none;
				}

				.mo-notice-first-half {
					width: 66%;
					display: inline-block;
					margin: 10px 0;
				}

				.mo-notice-other-half {
					width: 33%;
					display: inline-block;
					padding: 20px 0;
					position: absolute;
					text-align: center;
				}

				.mo-notice-first-half p {
					font-size: 14px;
				}

				.mo-notice-learn-more a {
					margin: 10px;
				}

				.mo-notice-learn-more {
					margin-top: 10px;
				}
			</style>
			<?php
		}

		/**
		 * MO_Admin_Notice class instantiate.
		 *
		 * @return MO_Admin_Notice|null
		 */
		public static function instance() {
			static $instance = null;

			if ( is_null( $instance ) ) {
				$instance = new self();
			}

			return $instance;
		}
	}

	MO_Admin_Notice::instance();
}
