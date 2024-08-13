<?php

namespace PDFEmbedder\Admin;

use PDFEmbedder\Options;
use PDFEmbedder\Helpers\Links;
use PDFEmbedder\Helpers\Assets;
use PDFEmbedder\Helpers\Multisite;

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
		( new Education\SettingsBottomBanner() )->hooks();

		// Register settings and their validation method.
		register_setting(
			'pdfemb_options',
			Options::KEY,
			[ Options::class, 'validate' ]
		);

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

		add_action( 'pdfemb_admin_settings_render_section_settings', [ $this, 'render_settings' ] );
		add_action( 'pdfemb_admin_settings_extra', [ $this, 'render_ut_setting' ], 0 );
		add_action( 'pdfemb_admin_settings_render_section_mobile', [ $this, 'render_mobile' ] );
		add_action( 'pdfemb_admin_settings_render_section_secure', [ $this, 'render_secure' ] );
		add_action( 'pdfemb_admin_settings_render_section_about', [ $this, 'render_about' ] );

		// Plugins page.
		add_filter( Multisite::is_network_activated() ? 'network_admin_plugin_action_links' : 'plugin_action_links', [ $this, 'register_plugin_action_links' ], 10, 2 );

		if ( Multisite::is_network_activated() ) {
			add_action( 'network_admin_edit_' . self::SLUG, [ $this, 'save_network_options' ] );
		}
	}

	/**
	 * Register plugin admin area.
	 *
	 * @since 4.7.0
	 */
	public function register_menu() {

		if ( Multisite::is_network_activated() ) {
			add_submenu_page(
				'settings.php',
				__( 'PDF Embedder settings', 'pdf-embedder' ),
				__( 'PDF Embedder', 'pdf-embedder' ),
				'manage_network_options',
				self::SLUG,
				[ $this, 'render_page' ]
			);
		} else {
			add_options_page(
				__( 'PDF Embedder settings', 'pdf-embedder' ),
				__( 'PDF Embedder', 'pdf-embedder' ),
				'manage_options',
				self::SLUG,
				[ $this, 'render_page' ]
			);
		}
	}

	/**
     * Register sections used in plugin admin area.
	 *
	 * @since 4.7.0
	 */
	protected function get_sections(): array {

		/**
		 * Filter the list of admin area sections.
		 *
		 * @since 4.7.0
		 *
		 * @param array $sections List of admin area sections.
		 */
		return apply_filters(
			'pdfemb_admin_sections',
			[
				'settings' => __( 'Settings', 'pdf-embedder' ),
				'mobile'   => __( 'Mobile', 'pdf-embedder' ),
				'secure'   => __( 'Secure', 'pdf-embedder' ),
				'about'    => __( 'About', 'pdf-embedder' ),
			]
		);
	}

	/**
	 * Render plugin admin area.
	 *
	 * @since 4.7.0
	 */
	public function render_page() { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh

		wp_enqueue_script(
			'pdfemb_admin',
			Assets::url( 'js/admin/pdfemb-admin.js', true ),
			[ 'jquery' ],
			Assets::ver(),
			false
		);

		wp_enqueue_style(
			'pdfemb_admin_css',
			Assets::url( 'css/admin/pdfemb-admin.css', true ),
			[],
			Assets::ver()
		);

		$submit_page_url = Multisite::is_network_activated() ? 'edit.php?action=' . self::SLUG : 'options.php';

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
					<h1><?php esc_html_e( 'PDF Embedder', 'pdf-embedder' ); ?></h1>
				</div>

				<div class="pdfemb-links">
					<a href="<?php echo esc_url( Links::get_utm_link( 'https://wp-pdf.com/docs/', 'Admin Header', 'Help Icon' ) ); ?>" target="_blank">
						<img src="<?php echo esc_url( Assets::url( 'img/admin/icon-help.svg', false ) ); ?>" alt="<?php esc_attr_e( 'Help Icon', 'pdf-embedder' ); ?>"/>
					</a>
				</div>
			</div>

			<div id="pdfemb-content">
				<h2 id="pdfemb-tabs" class="nav-tab-wrapper">
					<?php
					foreach ( $this->get_sections() as $section => $title ) {
						$active = $section === 'settings' ? 'nav-tab-active' : '';
						?>
						<a href="#<?php echo esc_attr( $section ); ?>" id="<?php echo esc_attr( $section ); ?>-tab" class="nav-tab <?php echo esc_attr( $active ); ?>">
							<?php echo esc_html( $title ); ?>
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

				<div id="pdfemb-tabswrapper">

					<form action="<?php echo esc_url( $submit_page_url ); ?>" method="post" id="pdfemb_form" enctype="multipart/form-data">
						<?php
						foreach ( $this->get_sections() as $section => $title ) {
							$active = $section === 'settings' ? 'active' : '';
							$desc   = sprintf( /* translators: %s - tab title. */
								esc_html__( '%s tab content with all the settings and additional information', 'pdf-embedder' ),
								esc_attr( $title )
							);

							echo '<div
								id="' . esc_attr( $section ) . '-section"
								class="pdfembtab ' . esc_attr( $active ) . '"
								aria-labelledby="' . esc_attr( $section ) . '-tab"
								aria-description="' . esc_attr( $desc ) . '"
								>';
							/**
							 * Render a specific section.
							 *
							 * @since 4.7.0
							 */
							do_action( 'pdfemb_admin_settings_render_section_' . $section );
							echo '</div>';
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
					<a href="#about" class="free-plugins"><?php esc_html_e( 'Free Plugins', 'pdf-embedder' ); ?></a>
				</p>
			</div>
		</div>
		<?php
	}

	/**
	 * Render the admin page and its Settings tab.
	 * Extended in Premium.
	 *
	 * @since 4.7.0
	 */
	public function render_settings() { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh

		$options = pdf_embedder()->options()->get();
		?>

		<h3>
			<?php esc_html_e( 'PDF Embedder Configuration', 'pdf-embedder' ); ?>
		</h3>

		<p>
			<?php esc_html_e( 'To use the plugin, just embed PDFs in the same way as you would normally embed images in your posts/pages - but try with a PDF file instead.', 'pdf-embedder' ); ?>
		</p>
		<p>
			<?php esc_html_e( "From the post editor, click Add Media, and then drag-and-drop your PDF file into the media library. When you insert the PDF into your post, it will automatically embed using the plugin's viewer.", 'pdf-embedder' ); ?>
		</p>

		<hr/>

		<h3>
			<?php esc_html_e( 'Default Viewer Settings', 'pdf-embedder' ); ?>
		</h3>

		<div class="pdfemb-admin-setting pdfemb-admin-setting-width">
			<label for="input_pdfemb_width" class="textinput">
				<?php esc_html_e( 'Width', 'pdf-embedder' ); ?>
			</label>
			<input id='input_pdfemb_width' class='textinput' name='pdfemb[pdfemb_width]' size='10' type='text' value='<?php echo esc_attr( $options['pdfemb_width'] ); ?>'/>
		</div>

		<br class="clear"/>

		<div class="pdfemb-admin-setting pdfemb-admin-setting-height">
			<label for="input_pdfemb_height" class="textinput">
				<?php esc_html_e( 'Height', 'pdf-embedder' ); ?>
			</label>
			<input id='input_pdfemb_height' class='textinput' name='pdfemb[pdfemb_height]' size='10' type='text' value='<?php echo esc_attr( $options['pdfemb_height'] ); ?>'/>
		</div>

		<br class="clear"/>

		<p class="desc big">
			<em>
				<?php
				printf(
					wp_kses(
						__( 'Enter <code>max</code> or an integer number of pixels.', 'pdf-embedder' ),
						[
							'code' => [],
						]
					)
				);
				?>
			</em>
		</p>

		<div class="pdfemb-admin-setting pdfemb-admin-setting-toolbar-location">
			<label for="pdfemb_toolbar" class="textinput">
				<?php esc_html_e( 'Toolbar Location', 'pdf-embedder' ); ?>
			</label>
			<select name='pdfemb[pdfemb_toolbar]' id='pdfemb_toolbar' class='select'>
				<option value="top" <?php echo $options['pdfemb_toolbar'] === 'top' ? 'selected' : ''; ?>>
					<?php esc_html_e( 'Top', 'pdf-embedder' ); ?>
				</option>
				<option value="bottom" <?php echo $options['pdfemb_toolbar'] === 'bottom' ? 'selected' : ''; ?>>
					<?php esc_html_e( 'Bottom', 'pdf-embedder' ); ?>
				</option>
				<option value="both" <?php echo $options['pdfemb_toolbar'] === 'both' ? 'selected' : ''; ?>>
					<?php esc_html_e( 'Both', 'pdf-embedder' ); ?>
				</option>
				<option value="none" <?php echo $options['pdfemb_toolbar'] === 'none' ? 'selected' : ''; ?>>
					<?php esc_html_e( 'No Toolbar', 'pdf-embedder' ); ?>
				</option>
			</select>
		</div>

		<br class="clear"/>
		<br class="clear"/>

		<div class="pdfemb-admin-setting pdfemb-admin-setting-toolbar-hover">
			<label class="textinput">
				<?php esc_html_e( 'Toolbar Hover', 'pdf-embedder' ); ?>
			</label>
			<span>
				<input type="radio" name='pdfemb[pdfemb_toolbarfixed]' id='pdfemb_toolbarfixed_off' class='radio' value="off" <?php echo $options['pdfemb_toolbarfixed'] === 'off' ? 'checked' : ''; ?>/>
				<label for="pdfemb_toolbarfixed_off" class="radio"><?php esc_html_e( 'Toolbar appears only on hover over document', 'pdf-embedder' ); ?></label>
			</span>
			<br/>
			<span>
				<input type="radio" name='pdfemb[pdfemb_toolbarfixed]' id='pdfemb_toolbarfixed_on' class='radio' value="on" <?php echo $options['pdfemb_toolbarfixed'] === 'on' ? 'checked' : ''; ?>/>
				<label for="pdfemb_toolbarfixed_on" class="radio">
					<?php esc_html_e( 'Toolbar always visible', 'pdf-embedder' ); ?>
	            </label>
			</span>
		</div>

		<br class="clear">

		<p>
			<?php
			printf(
				wp_kses( /* translators: %s - URL to wp-pdf.com doc. */
					__( 'You can override these defaults for specific embeds by modifying the shortcodes - see <a href="%s" target="_blank">instructions</a>.', 'pdf-embedder' ),
					[
						'a' => [
							'href'   => [],
							'target' => [],
						],
					]
				),
				esc_url( Links::get_utm_link( 'https://wp-pdf.com/docs/', 'Admin - Settings', 'Override Shortcode Defaults' ) )
			);
			?>
		</p>

		<hr>

		<h3>
			<?php esc_html_e( 'Miscellaneous', 'pdf-embedder' ); ?>
		</h3>

		<br class="clear"/>

		<?php
		/**
		 * Fires after the main settings section.
		 *
		 * @since 4.7.0
		 */
		do_action( 'pdfemb_admin_settings_extra' );
		?>

		<hr class="clear">

		<p class="submit">
			<button type="submit" class="button button-primary" id="submit" name="submit">
				<?php esc_html_e( 'Save Changes', 'pdf-embedder' ); ?>
			</button>
		</p>

		<?php
	}

	/**
	 * Render the Usage Tracking setting.
	 *
	 * @since 4.7.0
	 */
	public function render_ut_setting() {

		$options = pdf_embedder()->options()->get();
		?>

		<br class="clear"/>

		<div class="pdfemb-admin-setting pdfemb-admin-setting-usagetracking">
			<label for="usagetracking" class="textinput">
				<?php esc_html_e( 'Allow Usage Tracking', 'pdf-embedder' ); ?>
			</label>
			<span>
					<input type="checkbox" name="pdfemb[usagetracking]" id="usagetracking" class="checkbox" <?php echo $options['usagetracking'] === 'on' ? 'checked' : ''; ?>/>

					<label for="usagetracking" class="checkbox plain">
						<?php esc_html_e( 'By allowing us to track usage data, we can better help you, as we will know which WordPress configurations, themes, and plugins we should test.', 'pdf-embedder' ); ?>
					</label>
				</span>
		</div>

		<br class="clear"/>

		<?php
	}

	/**
	 * Render the admin page and its Mobile tab.
	 * Redefined in Premium.
	 *
	 * @since 4.7.0
	 */
	public function render_mobile() {
		?>

		<h3>
			<?php esc_html_e( 'Mobile-friendly embedding using PDF Embedder Premium', 'pdf-embedder' ); ?>
		</h3>

		<p>
			<?php esc_html_e( "This free version of the plugin should work on most mobile browsers, but it will be cumbersome for users with small screens - it is difficult to position the document entirely within the screen, and your users' fingers may catch the entire browser page when they're trying only to move about the document...", 'pdf-embedder' ); ?>
		</p>

		<p>
			<?php
			echo wp_kses(
				__(
					"Our <strong>PDF Embedder Premium</strong> plugin on its Basic plan solves this problem with an intelligent 'full screen' mode.
						When the document is smaller than a certain width, the document displays only as a 'thumbnail' with a large
						'View in Full Screen' button for the user to click when they want to study your document.
						This opens up the document in a way that has the full focus of the mobile browser, and the user can move around the
						document without hitting other parts of the web page by mistake. Click Exit to return to the regular web page.",
					'pdf-embedder'
				),
				[
					'strong' => [],
				]
			);
			?>
		</p>

		<p>
			<?php esc_html_e( 'The user can also touch and scroll continuously between all pages of the PDF which is much easier than clicking the next/prev buttons to navigate.', 'pdf-embedder' ); ?>
		</p>

		<p>
			<?php
			printf(
				wp_kses( /* translators: %s - URL to wp-pdf.com page. */
					__( 'See our website <a href="%s" target="_blank">wp-pdf.com</a> for more details and purchase options.', 'pdf-embedder' ),
					[
						'a' => [
							'href'   => [],
							'target' => [],
						],
					]
				),
				esc_url( Links::get_utm_link( 'https://wp-pdf.com/premium/', 'Admin - Mobile', 'Premium Inline Education' ) )
			);
			?>
		</p>

		<?php
	}

	/**
	 * Render the admin page and its Secure tab.
	 * Redefined in Premium.
	 *
	 * @since 4.7.0
	 */
	public function render_secure() {
		?>

		<h3>
			<?php esc_html_e( 'Protect your PDFs using PDF Embedder Premium', 'pdf-embedder' ); ?>
		</h3>
		<p>
			<?php
			echo wp_kses(
				__( 'Our <strong>PDF Embedder Premium</strong> plugin on its Pro plan provides the same simple but elegant viewer for your website visitors, with the added protection that it is difficult for users to download or print the original PDF document.', 'pdf-embedder' ),
				[
					'strong' => [],
				]
			);
			?>
		</p>

		<p>
			<?php esc_html_e( 'This means that your PDF is unlikely to be shared outside your site where you have no control over who views, prints, or shares it.', 'pdf-embedder' ); ?>
		</p>

		<p>
			<?php esc_html_e( "Optionally add a watermark containing the user's name or email address to discourage sharing of screenshots.", 'pdf-embedder' ); ?>
		</p>

		<p>
			<?php
			printf(
				wp_kses( /* translators: %s - URL to wp-pdf.com page. */
					__( 'See our website <a href="%s" target="_blank">wp-pdf.com</a> for more details and purchase options.', 'pdf-embedder' ),
					[
						'a' => [
							'href'   => [],
							'target' => [],
						],
					]
				),
				esc_url( Links::get_utm_link( 'https://wp-pdf.com/secure/', 'Admin - Secure', 'Premium Education' ) )
			);
			?>
		</p>

		<?php
	}

	/**
	 * Render the admin page and its About tab.
	 *
	 * @since 4.7.0
	 */
	public function render_about() {
		?>

		<h3 class="headline-title">
			<?php esc_html_e( 'Our Plugins', 'pdf-embedder' ); ?>
		</h3>

		<p>
			<?php esc_html_e( 'Get the most out of your site with these plugins.', 'pdf-embedder' ); ?>
		</p>

		<div class="pdfemb-partners-wrap">
			<?php ( new Partners() )->show(); ?>
		</div>

		<?php
	}

	/**
	 * When user is on our admin page, display footer text that asks to rate us.
	 *
	 * @since 4.7.0
	 *
	 * @param string $text Footer text.
	 */
	public function render_page_footer( string $text ): string {

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
	public function media_send_shortcode_to_editor( string $html, int $id, array $attachment ): string { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh

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
	 * Get the plugin settings page URL.
	 *
	 * @since 4.7.0
	 *
	 * @param string $page Tab to link to.
	 */
	public function get_settings_url( string $page = 'settings' ): string {

		if ( ! empty( $page ) ) {
			$page = '#' . sanitize_key( $page );
		}

		return Multisite::is_network_activated()
			? network_admin_url( 'settings.php?page=' . self::SLUG . $page )
			: admin_url( 'options-general.php?page=' . self::SLUG . $page );
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
				'settings'   => '<a href="' . esc_url( $this->get_settings_url() ) . '">' . esc_html__( 'Settings', 'pdf-embedder' ) . '</a>',
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

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		pdf_embedder()->options()->save( wp_unslash( $_POST[ Options::KEY ] ) );

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
}
