<?php

namespace PDFEmbedder\Admin\Education;

use PDFEmbedder\Helpers\Links;

/**
 * SettingsLiteBottom class.
 *
 * @since 4.7.0
 */
class SettingsBottomBanner {

	/**
	 * Assign all hooks to proper places.
	 *
	 * @since 4.7.0
	 */
	public function hooks() {

		if ( pdf_embedder()->is_premium() ) {
			return;
		}

		if ( $this->is_hidden() ) {
			return;
		}

		add_action( 'pdfemb_admin_settings_after', [ $this, 'show' ] );
		add_action( 'wp_ajax_pdfemb_admin_settings_bottom_upgrade', [ $this, 'dismiss_cta' ] );
	}

	/**
	 * Determine if we can display an educational message or not.
	 *
	 * @since 4.7.0
	 */
	private function is_hidden(): bool {

		if ( get_option( 'pdfemb_admin_settings_bottom_banner_hidden', false ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Dismiss the educational message.
	 *
	 * @since 4.7.0
	 */
	public function dismiss_cta() {

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error();
		}

		update_option( 'pdfemb_admin_settings_bottom_banner_hidden', time(), false );

		wp_send_json_success();
	}

	/**
	 * Display the educational message.
	 *
	 * @since 4.7.0
	 */
	public function show() {
		?>

		<div id="pdfemb-settings-bottom-cta">
			<a href="#" class="dismiss" title="<?php esc_attr_e( 'Dismiss this message', 'pdf-embedder' ); ?>">
				<span class="dashicons dashicons-dismiss"></span>
			</a>
			<h5><?php esc_html_e( 'Get PDF Embedder Premium and Unlock all the Powerful Features', 'pdf-embedder' ); ?></h5>
			<p><?php esc_html_e( 'Thanks for being a loyal PDF Embedder user. Upgrade to PDF Embedder Premium to unlock all the awesome features and experience why PDF Embedder is the best WordPress PDF plugin.', 'pdf-embedder' ); ?></p>
			<p>
				<?php
				printf(
					wp_kses( /* translators: %s - star icons. */
						__( 'We know that you will truly love PDF Embedder. It has over 100+ five star ratings (%s) and is active on over 300,000 websites.', 'pdf-embedder' ),
						[
							'i' => [
								'class'       => [],
								'aria-hidden' => [],
							],
						]
					),
					str_repeat( '<span class="dashicons dashicons-star-filled"></span>', 5 ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				);
				?>
			</p>
			<h6><?php esc_html_e( 'Premium Features:', 'pdf-embedder' ); ?></h6>
			<div class="list">
				<ul>
					<li><?php esc_html_e( 'Mobile friendly', 'pdf-embedder' ); ?></li>
					<li><?php esc_html_e( 'Continuous page scrolling', 'pdf-embedder' ); ?></li>
					<li><?php esc_html_e( 'Download button', 'pdf-embedder' ); ?></li>
					<li><?php esc_html_e( 'Full-Screen button', 'pdf-embedder' ); ?></li>
					<li><?php esc_html_e( 'Secure Feature: Prevent visitors from downloading your PDFs', 'pdf-embedder' ); ?></li>
				</ul>
				<ul>
					<li><?php esc_html_e( 'Highly customizable', 'pdf-embedder' ); ?></li>
					<li><?php esc_html_e( 'Working hyperlinks', 'pdf-embedder' ); ?></li>
					<li><?php esc_html_e( 'Jump to any page', 'pdf-embedder' ); ?></li>
					<li><?php esc_html_e( 'Remove the link to wp-pdf.com', 'pdf-embedder' ); ?></li>
					<li><?php esc_html_e( 'Secure Feature: Add watermark on top of your PDF files', 'pdf-embedder' ); ?></li>
				</ul>
			</div>

			<script>
				function getPdfembUpdateURL( hash ) {
					let url;

					switch ( hash ) {
						case 'mobile':
							url = '<?php echo esc_url( Links::get_upgrade_link( 'Settings Bottom Mobile', 'Upgrade to Premium' ) ); ?>'.replace(/(\/secure\/|\/thumbnails\/)/, '/premium/');
							break;

						case 'secure':
							url = '<?php echo esc_url( Links::get_upgrade_link( 'Settings Bottom Secure', 'Upgrade to Premium' ) ); ?>'.replace(/(\/thumbnails\/|\/premium\/)/, '/secure/');
							break;

						case 'thumbnails':
							url = '<?php echo esc_url( Links::get_upgrade_link( 'Settings Bottom Thumbnails', 'Upgrade to Premium' ) ); ?>'.replace(/(\/secure\/|\/premium\/)/, '/thumbnails/');
							break;

						case 'about':
							url = '<?php echo esc_url( Links::get_upgrade_link( 'Settings Bottom About', 'Upgrade to Premium' ) ); ?>';
							break;

						case 'settings':
						case '':
						default:
							url = '<?php echo esc_url( Links::get_upgrade_link( 'Settings Bottom Education', 'Upgrade to Premium' ) ); ?>';
					}

					return url;
				}
			</script>

			<p>
				<a href="<?php echo esc_url( Links::get_upgrade_link( 'Settings Bottom Education', 'Upgrade to Premium' ) ); ?>" target="_blank" class="pdfemb-upgrade-url">
					<?php esc_html_e( 'Get PDF Embedder Premium Today and Unlock all the Powerful Features »', 'pdf-embedder' ); ?>
				</a>
			</p>
			<p>
				<?php
				echo wp_kses(
					__( '<strong>Bonus:</strong> PDF Embedder users get <span class="green">50% off regular price</span>, automatically applied at checkout.', 'pdf-embedder' ),
					[
						'strong' => [],
						'span'   => [
							'class' => [],
						],
					]
				);
				?>
			</p>
		</div>

		<script type="text/javascript">
			jQuery( function ( $ ) {
				$( document ).on( 'click', '#pdfemb-settings-bottom-cta .dismiss', function ( e ) {
					e.preventDefault();

					$.post( ajaxurl, {
						action: 'pdfemb_admin_settings_bottom_upgrade'
					} );

					$( '#pdfemb-settings-bottom-cta' ).remove();
				} );
			} );
		</script>

		<?php
	}
}
