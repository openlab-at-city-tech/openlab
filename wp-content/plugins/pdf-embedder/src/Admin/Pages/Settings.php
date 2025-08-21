<?php

namespace PDFEmbedder\Admin\Pages;

use PDFEmbedder\Options;
use PDFEmbedder\Helpers\Links;

/**
 * Settings Page.
 *
 * @since 4.9.0
 */
class Settings extends Page {

	public const SLUG = 'settings';

	/**
	 * Get the title of the page.
	 *
	 * @since 4.9.0
	 */
	public function get_title(): string {

		return __( 'Settings', 'pdf-embedder' );
	}

	/**
	 * Page content.
	 *
	 * @since 4.9.0
	 */
	public function content() { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh

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
				<?php esc_html_e( 'Toolbar Visibility', 'pdf-embedder' ); ?>
			</label>
			<span>
				<input type="radio" name='pdfemb[pdfemb_toolbarfixed]' id='pdfemb_toolbarfixed_off' class='radio' value="off" <?php checked( $options['pdfemb_toolbarfixed'], 'off' ); ?>/>
				<label for="pdfemb_toolbarfixed_off" class="radio">
					<?php esc_html_e( 'On hover', 'pdf-embedder' ); ?>
				</label>
			</span>
			<br/>
			<span>
				<input type="radio" name='pdfemb[pdfemb_toolbarfixed]' id='pdfemb_toolbarfixed_on' class='radio' value="on" <?php checked( $options['pdfemb_toolbarfixed'], 'on' ); ?>/>
				<label for="pdfemb_toolbarfixed_on" class="radio">
					<?php esc_html_e( 'Always visible', 'pdf-embedder' ); ?>
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
				esc_url( Links::get_utm_link( 'https://wp-pdf.com/docs/premium-instructions-attributes/', 'Admin - Settings', 'Override Shortcode Defaults' ) )
			);
			?>
		</p>

		<hr>

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
}
