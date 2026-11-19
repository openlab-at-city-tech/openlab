<?php

namespace PDFEmbedder\Shortcodes;

use PDFEmbedder\Options;
use PDFEmbedder\Helpers\Check;
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

		// Premium skips `load_viewer()` on cron/heartbeat, so `pdfemb_shortcode_viewer`
		// below would return a null viewer → TypeError. Shortcode and block registration
		// are already guarded against cron, but `render()` is also reachable directly.
		if ( wp_doing_cron() || Check::is_heartbeat() ) {
			return '';
		}

		$a = $this->get_processed_atts( $user_atts );

		if ( empty( $a['url'] ) || empty( esc_url( set_url_scheme( $a['url'] ) ) ) ) {
			return '<!-- PDF Embedder: Please provide an "URL" attribute in your shortcode. -->';
		}

		/**
		 * `/securepdfs/` URLs persist in the post content even after a Pro downgrade or full premium uninstall.
		 *
		 * Filter whether the current install can render PDFs stored under `/wp-content/uploads/securepdfs/`.
		 * Covers shortcode, block, and Elementor widget render paths — they all converge here.
		 *
		 * @since 5.0.0
		 *
		 * @param bool $can_render Whether secure-folder PDFs can be rendered.
		 */
		$can_render_secure_pdfs = (bool) apply_filters( 'pdfemb_can_render_secure_pdfs', false );

		if ( ! $can_render_secure_pdfs && Check::is_secure_pdf_url( $a['url'] ) ) {
			return '<!-- PDF Embedder: Embedding /securepdfs/ PDFs requires PDF Embedder Pro. -->';
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
	 * @since 5.0.0 Inline option atts run through the per-key validators under the
	 *                  `render` saving context instead of passing through unchanged.
	 *
	 * @param array $user_atts Shortcode attributes.
	 */
	protected function get_processed_atts( array $user_atts ): array {

		$prefixed_atts = Options::prefix( $user_atts );
		$options       = pdf_embedder()->options();

		// Inline-only keys (e.g. `url`, `page`, `zoom`, `pdfID`) aren't part of the
		// options array, so validators don't see them. Carry them through raw — output
		// sinks in the viewer do their own escaping/casting.
		$non_options = array_diff_key( $prefixed_atts, $options->get_defaults() );

		// Validate inline option atts under the `render` saving context. Free + each
		// plan's `validate_options` returns only the keys present in `$prefixed_atts`,
		// with values normalized by the same per-key rules used on admin save. Keys the
		// user did not pass inline are absent from the result so they fall through to
		// the merged defaults+DB layer in `Viewer::set_options()` and aren't overridden
		// by hard-coded defaults.
		$prev_context = $options->saving_context;

		$options->saving_context = 'render';

		try {
			$validated_options = Options::validate( $prefixed_atts );
		} finally {
			$options->saving_context = $prev_context;
		}

		// Validated option keys + non-option keys. They don't overlap because
		// `$non_options` is the diff against the defaults that drive validation.
		$prefixed_validated = $validated_options + $non_options;

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
