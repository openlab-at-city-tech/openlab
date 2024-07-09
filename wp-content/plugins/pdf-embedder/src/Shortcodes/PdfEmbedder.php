<?php

namespace PDFEmbedder\Shortcodes;

use PDFEmbedder\Options;
use PDFEmbedder\Viewer\Viewer;
use PDFEmbedder\Viewer\ViewerInterface;

/**
 * Main class for the [pdf-embedder] shortcode.
 *
 * @since 4.7.0
 */
class PdfEmbedder {

	/**
	 * Shortcode tag.
	 *
	 * @since 4.7.0
	 *
	 * @var string
	 */
	const TAG = 'pdf-embedder';

	/**
	 * Shortcode main render method.
	 *
	 * @since 4.7.0
	 *
	 * @param array  $user_atts Shortcode attributes provided by a user.
	 * @param string $content   Shortcode content, that is inside the shortcode opening and closing tags.
	 */
	public function render( array $user_atts, string $content = '' ): string {

		$a = $this->get_processed_atts( $user_atts );

		if ( empty( $a['url'] ) || empty( esc_url( set_url_scheme( $a['url'] ) ) ) ) {
			return '<!-- PDF Embedder: Please provide an "URL" attribute in your shortcode. -->';
		}

		/**
		 * Filter the viewer instance for the shortcode.
		 *
		 * @since 4.8.0
		 *
		 * @param ViewerInterface $renderer The viewer instance.
		 */
		$viewer = apply_filters( 'pdfemb_shortcode_viewer', new Viewer() );

		$viewer->set_options( $a );
		$viewer->enqueue_inline_assets();

		$html = $viewer->render();

		// Process content that might have been added inside the shortcode.
		if ( ! empty( $content ) ) {
			$html .= do_shortcode( $content );
		}

		return $html;
	}

	/**
	 * Get processed shortcode attributes, filtered and with defaults.
	 * Make sure that user-provided attributes have valid values.
	 * If invalid - reset to defaults.
	 * We also deal with options having a prefix "pdfemb_" vs attributes not having it.
	 *
	 * @since 4.8.0
	 *
	 * @param array $user_atts Shortcode attributes.
	 */
	protected function get_processed_atts( array $user_atts ): array {

		$prefixed_atts = Options::prefix( $user_atts );

		// Get the user-defined non-options attributes from the shortcode attributes.
		$non_options = array_diff_key( $prefixed_atts, pdf_embedder()->options()->get_defaults() );

		// Validate the values of attributes that are options.
		$bloated_validated_all = Options::validate( $prefixed_atts );

		// Merge the user-defined non-options attributes with the validated options.
		$bloated_validated_with_users = array_merge(
			$bloated_validated_all,
			$non_options
		);

		$prefixed_validated = [];

		// Now combine the validated options with the user-defined non-options attributes
		// without keys that are present in the options but not in attributes.
		foreach ( $prefixed_atts as $key => $value ) {
			if ( array_key_exists( $key, $bloated_validated_with_users ) ) {
				$prefixed_validated[ $key ] = $bloated_validated_with_users[ $key ];
			}
		}

		$validated = Options::unprefix( $prefixed_validated );

		/**
		 * Filter shortcode and block attributes before rendering on the front-end.
		 *
		 * @since 1.0.0
		 *
		 * @param array $validated User-provided already validated attributes, not escaped/sanitized.
		 */
		return (array) apply_filters( 'pdfemb_filter_shortcode_attrs', $validated );
	}
}
