<?php

namespace PDFEmbedder\Admin;

use PDFEmbedder\Options;
use PDFEmbedder\Helpers\Links;
use PDFEmbedder\Helpers\Assets;
use PDFEmbedder\Admin\Pages\Page;
use PDFEmbedder\Helpers\Multisite;
use PDFEmbedder\Admin\Pages\About;
use PDFEmbedder\Admin\Pages\GetPro;
use PDFEmbedder\Admin\Pages\Mobile;
use PDFEmbedder\Admin\Pages\Secure;
use PDFEmbedder\Admin\Pages\Settings;
use PDFEmbedder\Admin\Pages\Watermarks;

/**
 * Admin page.
 *
 * @since 4.7.0
 */
class Admin {

	/**
	 * Slug of the admin page.
	 *
	 * @since 4.7.0
	 */
	const SLUG = 'pdfemb_list_options';

	/**
	 * Initialize the admin area.
	 *
	 * @since 4.7.0
	 */
	public function init() {

		global $pagenow;

		( new WPorgReview() )->hooks();
		( new Education\SettingsTopBar() )->hooks();
		( new Education\DemoContent() )->hooks();
		( new Education\GetStarted() )->hooks();

		// Styles used inside Media library screens.
		if ( $pagenow === 'upload.php' ) {
			wp_enqueue_style(
				'pdfemb_admin_other',
				Assets::url( 'css/admin/pdfemb-admin-media.css', true ),
				[ 'media-views' ],
				Assets::ver()
			);
		}
	}

	/**
	 * Assign all hooks to proper places.
	 *
	 * @since 4.7.0
	 */
	public function hooks() {

		// Embed PDF shortcode instead of link.
		add_filter( 'media_send_to_editor', [ $this, 'media_send_shortcode_to_editor' ], 20, 3 );

		// Modify footer text on our admin page.
		add_filter( 'admin_footer_text', [ $this, 'render_page_footer' ], 1, 2 );
		add_filter( 'update_footer', '__return_empty_string' );

		add_action( 'admin_print_scripts', [ $this, 'hide_unrelated_notices' ] );

		( new Partners() )->hooks();

		( new MediaLibrary() )->hooks();

		add_action( 'pdfemb_admin_settings_extra', [ $this, 'render_ut_setting' ], 0 );

		// Plugins page.
		add_filter( Multisite::is_network_activated() ? 'network_admin_plugin_action_links' : 'plugin_action_links', [ $this, 'register_plugin_action_links' ], 10, 2 );

		if ( Multisite::is_network_activated() ) {
			add_action( 'network_admin_edit_' . self::SLUG, [ $this, 'save_network_options' ] );
		}

		add_action( 'load-settings_page_' . self::SLUG, [ $this, 'save_settings' ] );
	}

	/**
	 * Save the plugin settings.
	 *
	 * @since 4.9.0
	 */
	public function save_settings() {

		// We must have all the required data.
		if ( ! isset( $_POST['pdfemb'], $_POST['section'], $_POST['_wpnonce'], $_POST['option_page'], $_POST['action'], $_POST['_wp_http_referer'] ) ) {
			return;
		}

		// Check the nonce.
		check_admin_referer( 'pdfemb_options-options' );

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.ValidatedSanitizedInput.InputNotValidated
		pdf_embedder()->options()->save( wp_unslash( $_POST[ Options::KEY ] ), sanitize_key( $_POST['section'] ) );

		// Redirect back to the settings page that was submitted.
		wp_safe_redirect( add_query_arg( 'settings-updated', 'true', esc_url_raw( wp_unslash( $_POST['_wp_http_referer'] ) ) ) );
		exit;
	}

	/**
	 * Register plugin admin area.
	 *
	 * @since 4.7.0
	 */
	public function register_menu() {

		if ( Multisite::is_network_activated() ) {
			$hook = add_submenu_page(
				'settings.php',
				__( 'PDF Embedder settings', 'pdf-embedder' ),
				__( 'PDF Embedder', 'pdf-embedder' ),
				'manage_network_options',
				self::SLUG,
				[ $this, 'render_page' ]
			);
		} else {
			$hook = add_options_page(
				__( 'PDF Embedder settings', 'pdf-embedder' ),
				__( 'PDF Embedder', 'pdf-embedder' ),
				'manage_options',
				self::SLUG,
				[ $this, 'render_page' ]
			);
		}

		add_action( 'admin_print_styles-' . $hook, [ $this, 'enqueue_admin_styles' ] );
	}

	/**
	 * Enqueue admin styles.
	 *
	 * @since 4.8.0
	 */
	public function enqueue_admin_styles() {

		wp_enqueue_script(
			'pdfemb_admin',
			Assets::url( 'js/admin/pdfemb-admin.js' ),
			[ 'jquery' ],
			Assets::ver(),
			false
		);

		wp_add_inline_script(
			'pdfemb_admin',
			'const pdfemb_args = ' . wp_json_encode(
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
			),
			'before'
		);

		wp_enqueue_style(
			'pdfemb_admin',
			Assets::url( 'css/admin/pdfemb-admin.css' ),
			[],
			Assets::ver()
		);
	}

	/**
     * Register sections used in plugin admin area.
	 *
	 * @since 4.7.0
	 *
	 * @return Page[] List of admin area pages.
	 */
	protected function get_sections(): array {

		static $inited;

		if ( $inited ) {
			return $inited;
		}

		/**
		 * Filter the list of admin area sections.
		 *
		 * @since 4.7.0
		 *
		 * @param array $sections List of admin area sections.
		 */
		$sections = (array) apply_filters(
			'pdfemb_admin_sections',
			[
				Settings::SLUG   => Settings::class,
				Mobile::SLUG     => Mobile::class,
				Secure::SLUG     => Secure::class,
				Watermarks::SLUG => Watermarks::class,
				About::SLUG      => About::class,
				GetPro::SLUG     => GetPro::class,
			]
		);

		$inited = array_map(
			static function ( $section ) {

				if ( is_subclass_of( $section, Page::class ) ) {
					return new $section();
				}

				return null;
			},
			$sections
		);

		return array_filter( $inited );
	}

	/**
	 * Render plugin admin area.
	 *
	 * @since 4.7.0
	 */
	public function render_page() { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh

		$submit_page_url = $this->get_url( $this->get_current_section() );

		if ( Multisite::is_network_activated() ) {
			$this->network_save_settings();
		}
		?>

		<div id="pdfemb-admin">

			<?php
			/**
			 * Fires before the settings page content.
			 *
			 * @since 4.7.0
			 */
			do_action( 'pdfemb_admin_settings_before' );
			?>

			<div id="pdfemb-header">
				<div class="pdfemb-logo">
					<img src="<?php echo esc_url( Assets::url( 'img/admin/logo.svg', false ) ); ?>" alt="<?php esc_attr_e( 'PDF Embedder Logo', 'pdf-embedder' ); ?>" height="40"/>
				</div>

				<div class="pdfemb-links">
					<a href="#" target="_blank" class="trigger-getstarted">
						<img src="<?php echo esc_url( Assets::url( 'img/admin/icon-rocket.svg', false ) ); ?>" alt="" width="22" />
						<?php esc_html_e( 'Get Started', 'pdf-embedder' ); ?>
					</a>
					<a href="<?php echo esc_url( Links::get_utm_link( 'https://wp-pdf.com/docs/', 'Admin Header', 'Help Icon' ) ); ?>" target="_blank">
						<img src="<?php echo esc_url( Assets::url( 'img/admin/icon-help.svg', false ) ); ?>" alt="" width="22" />
						<?php esc_html_e( 'Help', 'pdf-embedder' ); ?>
					</a>
				</div>
			</div>

			<div id="pdfemb-content">
				<h2 id="pdfemb-sections" class="nav-tab-wrapper">
					<?php
					foreach ( $this->get_sections() as $section ) {
						$active_class = $this->get_current_section() === (string) $section ? 'nav-tab-active' : '';
						$url          = $this->get_url( $section );
						$right_class  = $section::SLUG === GetPro::SLUG ? 'nav-tab-right' : '';
						?>
						<a href="<?php echo esc_url( $url ); ?>" id="pdfemb-section-<?php echo esc_attr( $section ); ?>-link" class="nav-tab <?php echo esc_attr( $active_class ); ?> <?php echo esc_attr( $right_class ); ?>">
							<?php echo esc_html( $section->get_title() ); ?>
						</a>
						<?php
					}
					?>

					<?php
					/**
					 * Render any other tab.
					 *
					 * @since 4.7.0
					 */
					do_action( 'pdfemb_admin_settings_render_tab' );
					?>
				</h2>

				<div id="pdfemb-section-wrapper">

					<?php $this->render_admin_notices(); ?>

					<?php ( new Education\GetStarted() )->render(); ?>

					<form action="<?php echo esc_url( $submit_page_url ); ?>" method="post" id="pdfemb_form" enctype="multipart/form-data">
						<input type="hidden" name="section" value="<?php echo esc_attr( $this->get_current_section() ); ?>">
						<?php
						foreach ( $this->get_sections() as $section ) {
							if ( $this->get_current_section() !== (string) $section ) {
								continue;
							}
							?>

							<div id="pdfemb-<?php echo esc_attr( $section ); ?>-section">
								<?php
								$section->content();

								/**
								 * Render a specific section.
								 *
								 * @since 4.7.0
								 * @deprecated 4.9.0
								 */
								do_action_deprecated( 'pdfemb_admin_settings_render_section_' . $section, [], '4.9.0 of PDF Embedder' );
								?>

							</div>

							<?php
						}

						/**
						 * Render any other section.
						 *
						 * @since 4.7.0
						 */
						do_action( 'pdfemb_admin_settings_render_section' );

						settings_fields( 'pdfemb_options' );
						?>
					</form>

				</div>
			</div>

			<?php
			/**
			 * Fires after the settings page content.
			 *
			 * @since 4.7.0
			 */
			do_action( 'pdfemb_admin_settings_after' );
			?>

			<div id="pdfemb-footer">
				<p class="madewith">
					<?php esc_html_e( 'Made with ♥ by the WP PDF Team', 'pdf-embedder' ); ?>
				</p>
				<p class="links">
					<?php
					$support_url = pdf_embedder()->is_premium() ? Links::get_utm_link( 'https://wp-pdf.com/contact/', 'Admin - Footer', 'Support' ) : 'https://wordpress.org/support/plugin/pdf-embedder/';
					?>
					<a href="<?php echo esc_url( $support_url ); ?>" target="_blank"><?php esc_html_e( 'Support', 'pdf-embedder' ); ?></a>
					<span>/</span>
					<a href="<?php echo esc_url( Links::get_utm_link( 'https://wp-pdf.com/docs/', 'Admin Footer', 'Docs' ) ); ?>" target="_blank"><?php esc_html_e( 'Docs', 'pdf-embedder' ); ?></a>
					<span>/</span>
					<a href="<?php echo esc_url( pdf_embedder()->admin()->get_url( 'about' ) ); ?>" class="free-plugins"><?php esc_html_e( 'Free Plugins', 'pdf-embedder' ); ?></a>
				</p>
			</div>
		</div>
		<?php
	}

	protected function render_admin_notices() {

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$is_settings_update = isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] === 'true';
		?>

		<?php if ( $is_settings_update ) : ?>
			<div id="setting-error-settings_updated" class="notice notice-success settings-error">
				<p>
					<?php esc_html_e( 'Settings were successfully saved.', 'pdf-embedder' ); ?>
				</p>
			</div>
		<?php endif; ?>

		<?php
		/**
		 * Fires in place where all the notices in the plugin admin area should be rendered.
		 *
		 * @since 4.9.0
		 */
		do_action( 'pdfemb_admin_settings_notices' );
	}

	/**
	 * Render the Usage Tracking setting.
	 *
	 * @since 4.7.0
	 */
	public function render_ut_setting() {

		$options = pdf_embedder()->options()->get();
		?>

		<h3>
			<?php esc_html_e( 'Miscellaneous', 'pdf-embedder' ); ?>
		</h3>

		<br class="clear"/>

		<div class="pdfemb-admin-setting pdfemb-admin-setting-usagetracking">
			<label for="usagetracking" class="textinput">
				<?php esc_html_e( 'Allow Usage Tracking', 'pdf-embedder' ); ?>
			</label>
			<span>
				<input type="checkbox" name="pdfemb[usagetracking]" id="usagetracking" class="checkbox" <?php checked( Options::is_on( $options['usagetracking'] ) ); ?>/>

				<label for="usagetracking" class="checkbox plain">
					<?php esc_html_e( 'By allowing us to track usage data, we can better help you, as we will know which WordPress configurations, themes, and plugins we should test.', 'pdf-embedder' ); ?>
				</label>
			</span>
		</div>

		<br class="clear"/>

		<?php
	}

	/**
	 * When user is on our admin page, display footer text that asks to rate us.
	 *
	 * @since 4.7.0
	 *
	 * @param string $text Footer text.
	 */
	public function render_page_footer( $text ) {

		if ( ! $this->is_admin_page() ) {
			return $text;
		}

		$url  = 'https://wordpress.org/support/plugin/pdf-embedder/reviews/?filter=5#new-post';
		$text = sprintf(
			wp_kses( /* translators: $1$s - PDF Embedder plugin name, $2$s - WP.org review link, $3$s - WP.org review link. */
				__( 'Please rate %1$s <a href="%2$s" target="_blank" rel="noopener noreferrer">&#9733;&#9733;&#9733;&#9733;&#9733;</a> on <a href="%3$s" target="_blank">WordPress.org</a> to help us spread the word.', 'pdf-embedder' ),
				[
					'a' => [
						'href'   => [],
						'target' => [],
						'rel'    => [],
					],
				]
			),
			'<strong>PDF Embedder</strong>',
			$url,
			$url
		);

		return $text;
	}

	/**
	 * Embed PDF shortcode instead of link.
	 *
	 * @since 4.7.0
	 *
	 * @param string $html       HTML markup for a media item sent to the editor.
	 * @param int    $id         The first key from the $_POST['send'] data.
	 * @param array  $attachment Array of attachment metadata.
	 */
	public function media_send_shortcode_to_editor( $html, $id, $attachment ): string { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh

		$pdf_url = '';
		$title   = '';

		// Use URL if we already have one.
		if ( isset( $attachment['url'] ) && preg_match( '/\.pdf$/i', $attachment['url'] ) ) {
			$pdf_url = $attachment['url'];
			$title   = $attachment['post_title'] ?? '';

		// If no URL, retrieve by ID - but only if it's a PDF.
		} elseif ( $id > 0 ) {
			$post = get_post( $id );

			if ( $post && isset( $post->post_mime_type ) && $post->post_mime_type === 'application/pdf' ) {
				$pdf_url = wp_get_attachment_url( $id );
				$title   = get_the_title( $id );
			}
		}

		if ( $pdf_url === '' ) {
			return $html;
		}

		if ( $title !== '' ) {
			$title_from_url = Links::make_title_from_url( $pdf_url );

			if ( $title === $title_from_url || Links::make_title_from_url( '/' . $title ) === $title_from_url ) {
				// This would be the default title anyway based on URL
				// OR if you take .pdf off title it would match, so that's close enough - don't load up shortcode with title param.
				$title = '';
			} else {
				$title = ' title="' . esc_attr( $title ) . '"';
			}
		}

		/**
		 * Filter the shortcode code that will be sent to the editor.
		 *
		 * @since 1.0.0
		 *
		 * @param string $shortcode  Shortcode code to be sent to the editor.
		 * @param string $html       Initial HTML markup for a media item that was intended to be sent to the editor.
		 * @param int    $id         The first key from the $_POST['send'] data.
		 * @param array  $attachment Array of attachment metadata.
		 */
		return apply_filters(
			'pdfemb_override_send_to_editor',
			'[pdf-embedder url="' . esc_url( $pdf_url ) . '"' . $title . ']',
			$html,
			$id,
			$attachment
		);
	}

	/**
	 * Check if the user is on our admin page.
	 *
	 * @since 4.8.0
	 */
	public function is_admin_page(): bool {

		// phpcs:disable WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash

		if ( ! is_admin() ) {
			return false;
		}

		// Check against basic requirements.
		if (
			empty( $_REQUEST['page'] ) ||
			strpos( $_REQUEST['page'], self::SLUG ) === false
		) {
			return false;
		}

		// phpcs:enable WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash

		return true;
	}

	/**
	 * Register plugin action links.
	 *
	 * @since 4.7.0
	 *
	 * @param array  $links Plugin action links.
	 * @param string $file  Plugin file.
	 */
	public function register_plugin_action_links( $links, $file ): array {

		if ( plugin_basename( PDFEMB_PLUGIN_FILE ) === $file ) {

			$deactivate = $links['deactivate'];

			$links = [
				'settings'   => '<a href="' . esc_url( $this->get_url() ) . '">' . esc_html__( 'Settings', 'pdf-embedder' ) . '</a>',
				'premium'    => '<a href="' . esc_url( Links::get_utm_link( 'https://wp-pdf.com/premium/', 'Plugins List', 'Premium Link' ) ) . '" target="_blank">' . esc_html__( 'Premium', 'pdf-embedder' ) . '</a>',
				'secure'     => '<a href="' . esc_url( Links::get_utm_link( 'https://wp-pdf.com/secure/', 'Plugins List', 'Secure Link' ) ) . '" target="_blank">' . esc_html__( 'Secure', 'pdf-embedder' ) . '</a>',
				'deactivate' => $deactivate,
			];
		}

		return $links;
	}

	/**
	 * Save plugin settings when in a network environment.
	 * TODO: rewrite, see Admin::save_network_options().
	 *
	 * @since 4.7.0
	 */
	protected function network_save_settings() {

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.ValidatedSanitizedInput.MissingUnslash
		if ( isset( $_REQUEST['updated'] ) && $_REQUEST['updated'] ) {
			?>
			<div id="setting-error-settings_updated" class="updated settings-error">
				<p>
					<strong><?php esc_html_e( 'Settings saved.', 'pdf-embedder' ); ?></strong>
				</p>
			</div>
			<?php
		}

		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if (
			isset( $_REQUEST['error_setting'] ) &&
			is_array( $_REQUEST['error_setting'] ) &&
			isset( $_REQUEST['error_code'] ) &&
			is_array( $_REQUEST['error_code'] )
		) {
			// phpcs:disable WordPress.Security.ValidatedSanitizedInput
			$error_code    = $_REQUEST['error_code'];
			$error_setting = $_REQUEST['error_setting'];
			// phpcs:enable WordPress.Security.ValidatedSanitizedInput

			if (
				count( $error_code ) > 0 &&
				count( $error_code ) === count( $error_setting )
			) {
				$number_of_errors = count( $error_code );

				for ( $i = 0; $i < $number_of_errors; ++$i ) {
					?>
					<div id="setting-error-settings_<?php echo esc_attr( $i ); ?>" class="error settings-error">
						<p>
							<strong>
								<?php echo esc_html( htmlentities2( Options::get_error_text( $error_setting[ $i ] . '|' . $error_code[ $i ] ) ) ); ?>
							</strong>
						</p>
					</div>
					<?php
				}
			}
		}
		// phpcs:enable WordPress.Security.NonceVerification.Recommended
	}

	/**
	 * Save all the plugin options in the network environment.
	 * TODO: rewrite, see Admin::network_save_settings().
	 *
	 * @since 4.7.0
	 */
	public function save_network_options() {

		check_admin_referer( 'pdfemb_options-options' );

		if (
			! isset( $_POST[ Options::KEY ] ) ||
			! is_array( $_POST[ Options::KEY ] )
		) {
			/*
			 * Redirect to settings page in network.
			 * We can't use wp_safe_redirect() here because in the network environment
			 * different sites within the same network may have different domains: domain mapping.
			 */
			// phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect
			wp_redirect(
				add_query_arg(
					[
						'page'    => self::SLUG,
						'updated' => true,
					],
					network_admin_url( 'admin.php' )
				)
			);
			exit;
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.ValidatedSanitizedInput.InputNotValidated
		pdf_embedder()->options()->save( wp_unslash( $_POST[ Options::KEY ] ), sanitize_key( $_POST['section'] ) );

		$error_code    = [];
		$error_setting = [];

		foreach ( get_settings_errors() as $e ) {
			if ( is_array( $e ) && isset( $e['code'] ) && isset( $e['setting'] ) ) {
				$error_code[]    = $e['code'];
				$error_setting[] = $e['setting'];
			}
		}

		/*
		 * Redirect to settings page in a network.
		 * We can't use wp_safe_redirect() here because in the network environment
		 * different sites within the same network may have different domains via domain mapping.
		 */
		// phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect
		wp_redirect(
			add_query_arg(
				[
					'page'          => self::SLUG,
					'section'       => $this->get_current_section(),
					'updated'       => true,
					'error_setting' => $error_setting,
					'error_code'    => $error_code,
				],
				network_admin_url( 'admin.php' )
			)
		);
		exit;
	}

	/**
	 * Remove unrelated notices from our plugin admin page.
	 *
	 * @since 4.8.0
	 */
	public function hide_unrelated_notices() { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded, Generic.Metrics.NestingLevel.MaxExceeded

		if ( ! $this->is_admin_page() ) {
			return;
		}

		global $wp_filter;

		// Define rules to remove callbacks.
		$rules = [
			'user_admin_notices' => [], // remove all callbacks.
			'admin_notices'      => [],
			'all_admin_notices'  => [],
			'admin_footer'       => [
				'render_delayed_admin_notices', // remove this particular callback.
			],
		];

		// Extra deny callbacks (will be removed for each hook tag defined in $rules).
		$common_deny_callbacks = [];

		$notice_types = array_keys( $rules );

		foreach ( $notice_types as $notice_type ) {
			if ( empty( $wp_filter[ $notice_type ]->callbacks ) || ! is_array( $wp_filter[ $notice_type ]->callbacks ) ) {
				continue;
			}

			$remove_all_filters = empty( $rules[ $notice_type ] );

			foreach ( $wp_filter[ $notice_type ]->callbacks as $priority => $hooks ) {
				foreach ( $hooks as $name => $arr ) {
					if ( is_object( $arr['function'] ) && is_callable( $arr['function'] ) ) {
						if ( $remove_all_filters ) {
							unset( $wp_filter[ $notice_type ]->callbacks[ $priority ][ $name ] );
						}
						continue;
					}

					$class = ! empty( $arr['function'][0] ) && is_object( $arr['function'][0] ) ? strtolower( get_class( $arr['function'][0] ) ) : '';

					// Remove all callbacks except our notices.
					if ( $remove_all_filters && strpos( $class, 'pdfembedder' ) === false ) {
						unset( $wp_filter[ $notice_type ]->callbacks[ $priority ][ $name ] );
						continue;
					}

					$cb = is_array( $arr['function'] ) ? $arr['function'][1] : $arr['function'];

					// Remove a specific callback.
					if ( ! $remove_all_filters ) {
						if ( in_array( $cb, $rules[ $notice_type ], true ) ) {
							unset( $wp_filter[ $notice_type ]->callbacks[ $priority ][ $name ] );
						}
						continue;
					}

					// Remove callbacks from `$common_deny_callbacks` denylist.
					if ( in_array( $cb, $common_deny_callbacks, true ) ) {
						unset( $wp_filter[ $notice_type ]->callbacks[ $priority ][ $name ] );
					}
				}
			}
		}
	}

	/**
	 * Get the plugin settings page URL.
	 *
	 * @since 4.9.0
	 *
	 * @param string $section Section to link to.
	 */
	public function get_url( string $section = 'settings' ): string {

		if ( ! empty( $section ) ) {
			$section = sanitize_key( $section );
		}

		return Multisite::is_network_activated()
			? add_query_arg( 'section', $section, network_admin_url( 'settings.php?page=' . self::SLUG ) )
			: add_query_arg( 'section', $section, admin_url( 'options-general.php?page=' . self::SLUG ) );
	}

	/**
	 * Get the current section.
	 *
	 * @since 4.9.0
	 *
	 * @return string Current section.
	 */
	public function get_current_section(): string {

		$sections = $this->get_sections();

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$section = isset( $_GET['section'] ) ? sanitize_key( $_GET['section'] ) : '';

		return array_key_exists( $section, $sections ) ? $section : 'settings';
	}
}
