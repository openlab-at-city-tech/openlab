<?php

namespace PDFEmbedder\Admin\Pages;

use PDFEmbedder\Helpers\Links;

/**
 * Watermarks Page.
 *
 * @since 4.9.0
 */
class Watermarks extends Page {

	public const SLUG = 'watermarks';

	/**
	 * Get the title of the page.
	 *
	 * @since 4.9.0
	 */
	public function get_title(): string {

		return __( 'Watermarks', 'pdf-embedder' );
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
				<h3><?php esc_html_e( 'Watermark your PDFs using PDF Embedder Premium', 'pdf-embedder' ); ?></h3>

				<a href="<?php echo esc_url( Links::get_utm_link( 'https://wp-pdf.com/pricing/', 'Admin - Watermarks', 'Upgrade to Pro' ) ); ?>" class="upgrade" target="_blank">
					<?php esc_html_e( 'Upgrade to Pro', 'pdf-embedder' ); ?>
				</a>
			</header>

			<section>

				<p>
					<?php
					printf(
						wp_kses( /* translators: %s - link to instructions. */
							'See <a href="%s" target="_blank">Instructions</a> for more details.',
							[
								'a' => [
									'href'   => [],
									'target' => [],
								],
							]
						),
						esc_url( Links::get_utm_link( 'https://wp-pdf.com/docs/secure-instructions/', 'Admin - Watermarks', 'instructions' ) )
					);
					?>
				</p>

				<div class="pdfemb-admin-setting pdfemb-admin-setting-wm_text">
					<label for="pdfemb_wm_text" class="textinput">Text to display on secure PDFs</label>
					<textarea id='pdfemb_wm_text' class='textinput'></textarea>

					<p class="desc clear">
						Leave blank for no watermark on secure PDFs.<br>
						<?php
						printf(
							wp_kses( /* translators: %1$s, %2$s, %3$s - various smart tags. */
								'You can use these Smart Tags to display logged-in user information: %1$s, %2$s, %3$s.<br>For logged-out users they will be blank.',
								[
									'code' => [],
									'br'   => [],
								]
							),
							'<code>{fullname}</code>',
							'<code>{username}</code>',
							'<code>{email}</code>'
						);
						?>
					</p>
				</div>

				<div class="pdfemb-admin-setting pdfemb-admin-setting-wm_halign">
					<label for="pdfemb_wm_halign" class="textinput">Horizontal alignment</label>
					<select id='pdfemb_wm_halign' class='select'>
						<option value="left">Left</option>
						<option value="center" selected>Center</option>
						<option value="right">Right</option>
					</select>
				</div>

				<br class="clear"/>

				<div class="pdfemb-admin-setting pdfemb-admin-setting-wm_voffset">
					<label for="pdfemb_wm_voffset" class="textinput">Vertical offset (%)</label>
					<span>
						<input id='pdfemb_wm_voffset' class='textinput' size='10' type='number' value='30' />
						<label for="pdfemb_wm_voffset" class="checkbox plain" style="margin: 21px 0 0 5px;">Numerical value between 0 and 100</label>
					</span>
				</div>

				<br class="clear"/>

				<div class="pdfemb-admin-setting pdfemb-admin-setting-wm_fontsize">
					<label for="pdfemb_wm_fontsize" class="textinput">Font Size (pt)</label>
					<span>
						<input id='pdfemb_wm_fontsize' class='textinput' size='10' type='number' value='72' />
					</span>
				</div>

				<br class="clear"/>

				<div class="pdfemb-admin-setting pdfemb-admin-setting-wm_opacity">
					<label for="pdfemb_wm_opacity" class="textinput">Opacity (%)</label>
					<span>
						<input id='pdfemb_wm_opacity' class='textinput' size='10' type='number' value='20' />
						<label for="pdfemb_wm_opacity" class="checkbox plain" style="margin: 21px 0 0 5px;">Numerical value between 0 and 100</label>
					</span>
				</div>

				<br class="clear"/>

				<div class="pdfemb-admin-setting pdfemb-admin-setting-wm_rotate">
					<label for="pdfemb_wm_rotate" class="textinput">Rotation (degrees)</label>
					<span>
						<input id='pdfemb_wm_rotate' class='textinput' size='10' type='number' value='35' />
						<label for="pdfemb_wm_rotate" class="checkbox plain" style="margin: 21px 0 0 5px;">Numerical value in degrees (from 0 to 360)</label>
					</span>

					<p class="desc clear">
						If positive, the movement will be clockwise; if negative - counter-clockwise.
					</p>
				</div>

				<br class="clear"/>

				<div class="pdfemb-admin-setting pdfemb-admin-setting-wm_evenpages">
					<label for="pdfemb_wm_evenpages" class="textinput">Page Display</label>
					<span>
						<input type="checkbox" id='pdfemb_wm_evenpages' class='checkbox' />
						<label for="pdfemb_wm_evenpages" class="checkbox plain">Show only on even page numbers</label>
					</span>
				</div>

				<br class="clear"/>

				<hr />

			</section>

			<p class="submit">
				<a href="<?php echo esc_url( Links::get_utm_link( 'https://wp-pdf.com/pricing/', 'Admin - Watermarks', 'Upgrade to Pro' ) ); ?>" class="upgrade" target="_blank">
					<?php esc_html_e( 'Upgrade to PDF Embedder Pro', 'pdf-embedder' ); ?>
				</a>
			</p>
		</div>

		<?php
	}
}
