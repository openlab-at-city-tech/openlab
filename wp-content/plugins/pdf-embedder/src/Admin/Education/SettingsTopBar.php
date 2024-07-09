<?php

namespace PDFEmbedder\Admin\Education;

use PDFEmbedder\Helpers\Links;

/**
 * Display or hide the notice bar at the top of the settings page.
 *
 * @since 4.7.0
 */
class SettingsTopBar {

	/**
	 * Where the flag value is saved.
	 *
	 * @since 4.7.0
	 */
	const OPTION_NAME = 'pdfemb_display_topbar';

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

		add_action( 'pdfemb_admin_settings_before', [ $this, 'show' ] );
		add_action( 'wp_ajax_pdfemb_admin_settings_topbar_upgrade', [ $this, 'dismiss_cta' ] );
	}

	/**
	 * Determine if we can display an educational message or not.
	 *
	 * @since 4.7.0
	 */
	public function is_hidden(): bool {

		return (bool) get_option( self::OPTION_NAME, false );
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

		update_option( self::OPTION_NAME, time(), false );

		wp_send_json_success();
	}

	/**
	 * Display the educational message.
	 *
	 * @since 4.7.0
	 */
	public function show() {

		?>

		<div id="pdfemb-top-notification">
			<span>
				<?php
				printf(
					wp_kses( /* translators: %1$s - URL to wp-pdf.com. */
						__( '<strong>You\'re using PDF Embedder Lite.</strong> To unlock more features <a href="%1$s" target="_blank"><strong>consider upgrading to Premium</strong></a> for %2$s off.', 'pdf-embedder' ),
						[
							'a'      => [
								'href'   => [],
								'target' => [],
							],
							'strong' => [],
						]
					),
					esc_url( Links::get_upgrade_link( 'Settings Top Bar', 'Upgrade to Premium' ) ),
					'50%'
				);
				?>
			</span>

			<button class="dismiss" data-section="admin-notice-bar"
				title="<?php esc_attr_e( 'Dismiss this message', 'pdf-embedder' ); ?>">
				<span class="dashicons dashicons-no-alt"></span>
			</button>
		</div>

		<script type="text/javascript">
			jQuery( function ( $ ) {
				$( document ).on( 'click', '#pdfemb-top-notification .dismiss', function ( e ) {
					e.preventDefault();

					$.post( ajaxurl, {
						action: 'pdfemb_admin_settings_topbar_upgrade'
					} );

					$( '#pdfemb-top-notification' ).remove();
				} );
			} );
		</script>

		<?php
	}
}
