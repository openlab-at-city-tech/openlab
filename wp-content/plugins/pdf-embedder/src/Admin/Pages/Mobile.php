<?php

namespace PDFEmbedder\Admin\Pages;

use PDFEmbedder\Helpers\Links;

/**
 * Mobile Page.
 *
 * @since 4.9.0
 */
class Mobile extends Page {

	public const SLUG = 'mobile';

	/**
	 * Get the title of the page.
	 *
	 * @since 4.9.0
	 */
	public function get_title(): string {

		return __( 'Mobile', 'pdf-embedder' );
	}

	/**
	 * Page content.
	 *
	 * @since 4.9.0
	 */
	public function content() {
		?>

		<div class="demo">

			<header>
				<h3><?php esc_html_e( 'Mobile-friendly embedding using PDF Embedder Premium', 'pdf-embedder' ); ?></h3>

				<a href="<?php echo esc_url( Links::get_utm_link( 'https://wp-pdf.com/pricing/', 'Admin - Mobile', 'Upgrade to Pro' ) ); ?>" class="upgrade" target="_blank">
					<?php esc_html_e( 'Upgrade to Pro', 'pdf-embedder' ); ?>
				</a>
			</header>

			<section>
				<p>When the document is smaller than the width specified below, the document displays only as a 'thumbnail' with a large 'View in Full Screen' button for the user to click to open.</p>

				<div class="pdfemb-admin-setting pdfemb-admin-setting-mobilewidth">
					<label for="input_pdfemb_mobilewidth" class="textinput">
						Mobile Width
					</label>

					<input id='input_pdfemb_mobilewidth' class='textinput' size='10' type='number' value='0' />

					<p class="desc clear">
						Enter an integer number of pixels, or <code>0</code> to disable automatic full-screen.
					</p>
				</div>

				<br class="clear"/>

				<div class="pdfemb-admin-setting pdfemb-admin-setting-mobilewidth_button_text">
					<label for="input_mobilewidth_button_text" class="textinput">
						Mobile Width Button Text
					</label>

					<input id='input_mobilewidth_button_text' class='textinput' size='50' type='text' value='View in Ful Screen' />

					<p class="desc clear">
						Enter a short string of text for the button.
					</p>
				</div>

				<br class="clear"/>

				<div class="pdfemb-admin-setting pdfemb-admin-setting-resetviewport">
					<label for="pdfemb_resetviewport" class="textinput">
						Disable Device Zoom
					</label>

					<input type="checkbox" id='pdfemb_resetviewport' class='checkbox' />
					<label for="pdfemb_resetviewport" class="checkbox plain">Enable if you are experiencing quality issues on mobile devices</label>

					<p class="desc clear">
						Some mobile browsers will use their own zoom, causing the PDF Embedder to render at a lower resolution than it should, or lose the toolbar off screen.
						<br>
						Enabling this option may help, but could potentially affect appearance in the rest of your site.
						<?php
						printf(
							wp_kses( /* translators: %s - link to documentation. */
								'See <a href="%s" target="_blank">documentation</a> for details.',
								[
									'a' => [
										'href'   => [],
										'target' => [],
									],
								]
							),
							esc_url( Links::get_utm_link( 'https://wp-pdf.com/premium-instructions/#disabledevicezoom', 'Admin - Mobile', 'Disable Device Zoom' ) )
						);
						?>
					</p>
				</div>

				<hr>
			</section>

			<p class="submit">
				<a href="<?php echo esc_url( Links::get_utm_link( 'https://wp-pdf.com/pricing/', 'Admin - Mobile', 'Upgrade to Pro' ) ); ?>" class="upgrade" target="_blank">
					<?php esc_html_e( 'Upgrade to PDF Embedder Pro', 'pdf-embedder' ); ?>
				</a>
			</p>
		</div>

		<?php
	}
}
