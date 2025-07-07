<?php

namespace PDFEmbedder\Admin\Education;

use PDFEmbedder\Helpers\Links;
use PDFEmbedder\Helpers\Assets;
use PDFEmbedder\Admin\Pages\GetPro;

/**
 * Display the helpful Get Started section on the settings page.
 *
 * @since 4.9.0
 */
class GetStarted {

	/**
	 * Where the flag value is saved.
	 *
	 * @since 4.7.0
	 */
	const OPTION_NAME = 'pdfemb_admin_getstarted_dismissed';

	/**
	 * Assign all hooks to proper places.
	 *
	 * @since 4.9.0
	 */
	public function hooks() {

		add_action( 'wp_ajax_pdfemb_admin_settings_getstarted_dismiss', [ $this, 'dismiss' ] );
		add_action( 'wp_ajax_pdfemb_admin_settings_getstarted_open', [ $this, 'open' ] );
	}

	/**
	 * Dismiss the educational message.
	 *
	 * @since 4.9.0
	 */
	public function dismiss() {

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error();
		}

		update_option( self::OPTION_NAME, time(), false );

		wp_send_json_success();
	}

	/**
	 * Dismiss the educational message.
	 *
	 * @since 4.9.0
	 */
	public function open() {

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error();
		}

		delete_option( self::OPTION_NAME );

		wp_send_json_success();
	}

	/**
	 * Determine if we can display an educational message or not.
	 *
	 * @since 4.9.0
	 */
	public function is_hidden(): bool {

		if ( pdf_embedder()->admin()->get_current_section() === GetPro::SLUG ) {
			return true;
		}

		return (bool) get_option( self::OPTION_NAME, false );
	}

	/**
	 * Display the educational message.
	 *
	 * @since 4.9.0
	 */
	public function render() {

		$hide = $this->is_hidden() ? 'hidden' : '';
		?>

		<div id="pdfemb-getstarted" class="<?php echo esc_attr( $hide ); ?>">

			<div class="content">
				<h3><?php esc_html_e( 'Get Started with PDF Embedder 🎉', 'pdf-embedder' ); ?></h3>

				<div class="editor block-editor">
					<img src="<?php echo esc_url( Assets::url( 'img/admin/getstarted/editor-block.png', false ) ); ?>" alt="" />
					<div>
						<h4><?php esc_html_e( 'Using Block Editor?', 'pdf-embedder' ); ?></h4>
						<p>
							<?php esc_html_e( 'Simply open the block inserter and search for the "PDF Embedder" block and drag and drop it in your desired location.', 'pdf-embedder' ); ?>
						</p>
					</div>
				</div>

				<hr>

				<div class="editor other">
					<img src="<?php echo esc_url( Assets::url( 'img/admin/getstarted/editor-shortcode.png', false ) ); ?>" alt="" />
					<div>
						<h4><?php esc_html_e( 'Using a Page Builder or Classic Editor?', 'pdf-embedder' ); ?></h4>
						<p>
							<?php
							printf(
								wp_kses( /* translators: %s - URL to the documentation. */
									__( 'Use the PDF Embedder shortcode to easily embed your PDFs. Learn more about embedding with Shortcodes <a href="%s" target="_blank">here</a>.', 'pdf-embedder' ),
									[
										'a' => [
											'href'   => [],
											'target' => [],
										],
									]
								),
								pdf_embedder()->is_premium()
									? esc_url( Links::get_utm_link( 'https://wp-pdf.com/docs/premium-instructions-attributes/', 'Admin - GetStarted', 'Shortcode' ) )
									: esc_url( Links::get_utm_link( 'https://wp-pdf.com/docs/free-instructions/', 'Admin - GetStarted', 'Shortcode' ) )
							);
							?>
						</p>
					</div>
				</div>

				<p>
					<?php
					printf(
						wp_kses( /* translators: %s - URL to the documentation. */
							__( 'Need more help? Check out our <a href="%s" target="_blank">Documentation here</a> →', 'pdf-embedder' ),
							[
								'a' => [
									'href'   => [],
									'target' => [],
								],
							]
						),
						esc_url( Links::get_utm_link( 'https://wp-pdf.com/docs/', 'Admin - GetStarted', 'Documentation' ) )
					);
					?>
				</p>
			</div>

			<button class="dismiss" title="<?php esc_attr_e( 'Hide this message', 'pdf-embedder' ); ?>">
				<?php echo Assets::svg( 'img/admin/getstarted/dismiss.svg' ); ?>
			</button>
		</div>

		<script type="text/javascript">
			jQuery( function ( $ ) {
				$( document ).on( 'click', '#pdfemb-getstarted .dismiss', function ( e ) {
					e.preventDefault();

					$.post( ajaxurl, {
						action: 'pdfemb_admin_settings_getstarted_dismiss',
					} );

					$( '#pdfemb-getstarted' ).slideUp( 'fast', function() {
						$( this ).addClass( 'hidden' );
					} );
				} );
			} );
		</script>

		<?php
	}
}
