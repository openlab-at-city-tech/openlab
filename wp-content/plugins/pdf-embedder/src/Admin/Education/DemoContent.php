<?php

namespace PDFEmbedder\Admin\Education;

use PDFEmbedder\Helpers\Links;

/**
 * Demo pages.
 *
 * @since 4.9.0
 */
class DemoContent {

	/**
	 * Register hooks.
	 *
	 * @since 4.9.0
	 */
	public function hooks() {

		if ( pdf_embedder()->is_premium() ) {
			return;
		}

		add_action( 'pdfemb_admin_settings_extra', [ $this, 'render_premium_settings' ] );
	}

	/**
	 * Settings page: premium settings.
	 *
	 * @since 4.9.0
	 */
	public function render_premium_settings() {
		?>

		<br class="clear"/>

		<hr>

		<div class="demo">
			<header>
				<h3><?php esc_html_e( 'Premium Features', 'pdf-embedder' ); ?></h3>

				<a href="<?php echo esc_url( Links::get_utm_link( 'https://wp-pdf.com/pricing/', 'Admin - Settings', 'Upgrade to Pro' ) ); ?>" class="upgrade" target="_blank">
					<?php esc_html_e( 'Upgrade to Pro', 'pdf-embedder' ); ?>
				</a>
			</header>

			<section>

				<br class="clear" />

				<div class="pdfemb-admin-setting pdfemb-admin-setting-scrollbar">
					<label for="pdfemb_scrollbar" class="textinput">Display Scrollbars</label>

					<select id='pdfemb_scrollbar' class='select'>
						<option value="none" selected>None</option>
						<option value="vertical">Vertical</option>
						<option value="horizontal">Horizontal</option>
						<option value="both">Both</option>
					</select>

					<br/>

					<p class="desc clear">
						User can still use mouse if scrollbars are not visible.
					</p>
				</div>

				<div class="pdfemb-admin-setting pdfemb-admin-setting-page-continousscroll">
					<label for="pdfemb_continousscroll" class="textinput">Continuous Page Scrolling</label>

					<input type="checkbox" id='pdfemb_continousscroll' class='checkbox' />
					<label for="pdfemb_continousscroll" class="checkbox plain">
						Allow user to scroll up/down between all pages in the PDF
					</label>

					<p class="desc clear">
						If unchecked, user must click next/prev buttons on the toolbar to change pages.
					</p>
				</div>

				<div class="pdfemb-admin-setting pdfemb-admin-setting-page-download">
					<label for="pdfemb_download" class="textinput">Download Button</label>

					<input type="checkbox" id='pdfemb_download' class='checkbox' />
					<label for="pdfemb_download" class="checkbox plain">Provide PDF download button in toolbar</label>
				</div>

				<br class="clear" />
				<br class="clear" />

				<div class="pdfemb-admin-setting pdfemb-admin-setting-page-tracking">
					<label for="pdfemb_tracking" class="textinput">Track Views/Downloads</label>

					<input type="checkbox" id='pdfemb_tracking' class='checkbox' />
					<label for="pdfemb_tracking" class="checkbox plain">Count number of views and downloads</label>
					<p class="desc clear">
						<?php
						printf(
							wp_kses( /* translators: %s: URL to Media Library filtered by PDF files. */
								'Values will be shown in the <a href="%s" target="_blank">Media Library</a> for each individual file separately.',
								[
									'a' => [
										'href'   => [],
										'target' => [],
									],
								]
							),
							esc_url( add_query_arg( 'attachment-filter', 'post_mime_type:application/pdf', admin_url( 'upload.php' ) ) )
						);
						?>
					</p>
				</div>

				<div class="pdfemb-admin-setting pdfemb-admin-setting-page-newwindow">
					<label for="pdfemb_newwindow" class="textinput">External Links</label>

					<input type="checkbox" id='pdfemb_newwindow' class='checkbox' />
					<label for="pdfemb_newwindow" class="checkbox plain">Open links in a new browser tab/window</label>
				</div>

				<br class="clear" />
				<br class="clear" />

				<div class="pdfemb-admin-setting pdfemb-admin-setting-page-scrolltotop">
					<label for="pdfemb_scrolltotop" class="textinput">Scroll to Top</label>

					<input type="checkbox" id='pdfemb_scrolltotop' class='checkbox' />
					<label for="pdfemb_scrolltotop" class="checkbox plain">Scroll to top of page when user clicks next/prev</label>
				</div>

				<br class="clear" />
				<br class="clear" />

				<div class="pdfemb-admin-setting pdfemb-admin-setting-page-search">
					<label for="pdfemb_search" class="textinput">Search Button</label>
					<input type="checkbox" id='pdfemb_search' class='checkbox' />
					<label for="pdfemb_search" class="checkbox plain">Provides PDF search/find button in toolbar</label>
				</div>

				<br class="clear" />
				<br class="clear" />

			</section>
		</div>
		<?php
	}
}
