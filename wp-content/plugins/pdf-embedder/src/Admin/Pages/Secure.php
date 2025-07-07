<?php

namespace PDFEmbedder\Admin\Pages;

use PDFEmbedder\Helpers\Links;

/**
 * Secure Page.
 *
 * @since 4.9.0
 */
class Secure extends Page {

	public const SLUG = 'secure';

	/**
	 * Get the title of the page.
	 *
	 * @since 4.9.0
	 */
	public function get_title(): string {

		return __( 'Secure', 'pdf-embedder' );
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
				<h3><?php esc_html_e( 'Protect your PDFs using PDF Embedder Premium', 'pdf-embedder' ); ?></h3>

				<a href="<?php echo esc_url( Links::get_utm_link( 'https://wp-pdf.com/pricing/', 'Admin - Secure', 'Upgrade to Pro' ) ); ?>" class="upgrade" target="_blank">
					<?php esc_html_e( 'Upgrade to Pro', 'pdf-embedder' ); ?>
				</a>
			</header>

			<section>

				<p>
					If 'Secure PDFs' is enabled, your PDF uploads will be 'secure' by default.<br>
					That is, they should be uploaded to a special sub-folder of your site uploads area. These files should not be accessible directly, and the plugin provides a backdoor method for the embedded viewer to obtain the file contents.
				</p>

				<p>
					This means that your PDF is unlikely to be shared outside your site where you have no control over who views, prints, or shares it.<br>
					Please note that it is still always possible for a determined user to obtain the original file. Sensitive information should never be presented to viewers in any form.
				</p>

				<br class="clear" />

				<div class="pdfemb-admin-setting pdfemb-admin-setting-secure">
					<label for="pdfemb_secure" class="textinput">Secure PDFs</label>

					<input type="checkbox" id='pdfemb_secure' class='checkbox'/>
					<label for="pdfemb_secure" class="checkbox plain">
						<?php
						printf(
							wp_kses( /* translators: %s - code tag with folder path. */
								'Send new PDF media uploads to the %s folder',
								[
									'code' => [],
								]
							),
							'<code>/wp-content/uploads/securepdfs/</code>'
						);
						?>
					</label>

					<p class="desc clear">
						Prevent direct access to your PDF files by placing them in a secure folder. The plugin will handle access to these files.
					</p>
				</div>

				<div class="pdfemb-admin-setting pdfemb-admin-setting-disablerightclick">
					<label for="pdfemb_disablerightclick" class="textinput">Disable Right Click</label>

					<input type="checkbox" id='pdfemb_disablerightclick' class='checkbox' />
					<label for="pdfemb_disablerightclick" class="checkbox plain">Disable right-click mouse menu</label>

					<p class="desc clear">
						This affects only secured PDF files.
					</p>
				</div>

				<div class="pdfemb-admin-setting pdfemb-admin-setting-cacheencrypted">
					<label for="pdfemb_cacheencrypted" class="textinput">Cache Encrypted PDFs</label>

					<input type="checkbox" id='pdfemb_cacheencrypted' class='checkbox'/>
					<label for="pdfemb_cacheencrypted" class="checkbox plain">Cache encrypted versions of secure PDFs on the server</label>

					<p class="desc clear">
						This prevents your browser from encrypting/decrypting the PDF files for each user, resulting in faster PDF rendering and downloading.
					</p>
				</div>

				<div class="pdfemb-admin-setting pdfemb-admin-setting-secureattpages">
					<label for="pdfemb_secureattpages" class="textinput">Attachment Pages</label>

					<input type="checkbox" id='pdfemb_secureattpages' class='checkbox' />
					<label for="pdfemb_secureattpages" class="checkbox plain">Auto-generate Attachment Pages for Secure PDFs</label>

					<p class="desc clear">
						<?php
						printf(
							wp_kses( /* translators: %s - link to documentation. */
								'Read more in our <a href="%s" target="_blank">documentation</a>.',
								[
									'a' => [
										'href'   => [],
										'target' => [],
									],
								]
							),
							esc_url( Links::get_utm_link( 'https://wp-pdf.com/docs/auto-generate-a-wordpress-page-to-embed-the-pdf/', 'Admin - Secure', 'Attachment Pages' ) )
						);
						?>
					</p>
				</div>

				<br class="clear" />

				<hr>

			</section>

			<p class="submit">
				<a href="<?php echo esc_url( Links::get_utm_link( 'https://wp-pdf.com/pricing/', 'Admin - Secure', 'Upgrade to Pro' ) ); ?>" class="upgrade" target="_blank">
					<?php esc_html_e( 'Upgrade to PDF Embedder Pro', 'pdf-embedder' ); ?>
				</a>
			</p>

		</div>

		<?php
	}
}
