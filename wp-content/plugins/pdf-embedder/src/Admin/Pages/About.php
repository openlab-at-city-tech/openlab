<?php

namespace PDFEmbedder\Admin\Pages;

use PDFEmbedder\Admin\Partners;

/**
 * About Page.
 *
 * @since 4.9.0
 */
class About extends Page {

	public const SLUG = 'about';

	/**
	 * Get the title of the page.
	 *
	 * @since 4.9.0
	 */
	public function get_title(): string {

		return __( 'About', 'pdf-embedder' );
	}

	/**
	 * Page content.
	 *
	 * @since 4.9.0
	 */
	public function content() { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh
		?>

		<div class="pdfemb-about-us">
			<h3>
				<?php esc_html_e( 'Hello and welcome to PDF Embedder, the most beginner-friendly viewer for your PDF files.', 'pdf-embedder' ); ?>
				<br>
				<?php esc_html_e( 'At WPAuth, we build software that helps you achieve your goals in minutes, without long and complicated configurations.', 'pdf-embedder' ); ?>
			</h3>
			<p>
				<?php esc_html_e( 'Our goal is to take the pain out of embedding PDF files and make it easy.', 'pdf-embedder' ); ?>
			</p>
			<p>
				<?php
				printf(
					wp_kses( /* translators: %1$s - URL to wpbeginner.com, %2$s - URL to wpforms.com, %3$s - URL to wpmailsmtp.com. */
						__( 'PDF Embedder is brought to you by the same team that’s behind the largest WordPress resource site, <a href="%1$s" target="_blank">WPBeginner</a>, the most popular forms plugin, <a href="%2$s" target="_blank">WPForms</a>, the most popular SMTP and Email Log plugin, <a href="%3$s" target="_blank">WP Mail SMTP</a>, and more!', 'pdf-embedder' ),
						[
							'a' => [
								'href'  => true,
								'taget' => true,
							],
						]
					),
					'https://www.wpbeginner.com/?utm_source=pdfembedderplugin&amp;utm_medium=pluginaboutpage&amp;utm_campaign=aboutpdfembedder',
					'https://wpforms.com/?utm_source=pdfembedderplugin&amp;utm_medium=pluginaboutpage&amp;utm_campaign=aboutpdfembedder',
					'https://wpmailsmtp.com/?utm_source=pdfembedderplugin&amp;utm_medium=pluginaboutpage&amp;utm_campaign=aboutpdfembedder'
				);
				?>
			</p>
			<p>
				<?php esc_html_e( 'Yup, we know a thing or two about building awesome products that customers love.', 'pdf-embedder' ); ?>
			</p>
		</div>

		<div class="pdfemb-partners-wrap">
			<?php ( new Partners() )->show(); ?>
		</div>

		<?php
	}
}
