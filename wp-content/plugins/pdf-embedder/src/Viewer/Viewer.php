<?php

namespace PDFEmbedder\Viewer;

use PDFEmbedder\Options;
use PDFEmbedder\Helpers\Links;
use PDFEmbedder\Helpers\Assets;

/**
 * Render the PDF file.
 *
 * @since 4.8.0
 */
class Viewer implements ViewerInterface {

	/**
	 * Processed and ready-to-use attributes.
	 *
	 * @since 4.8.0
	 *
	 * @var array
	 */
	protected $atts = [];

	/**
	 * Pass the options needed for the viewer.
	 *
	 * @since 4.8.0
	 *
	 * @param array $atts Attributes.
	 */
	public function set_options( array $atts = [] ) {

		$this->atts = array_merge( Options::unprefix( pdf_embedder()->options()->get() ), $atts );
	}

	/**
	 * Render the PDF viewer.
	 *
	 * @since 4.8.0
	 */
	public function render(): string { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh

		$title = ! empty( $this->atts['title'] ) ? $this->atts['title'] : Links::make_title_from_url( $this->atts['url'] );

		$html_node   = '';
		$extra_style = '';

		/*
		 * Extra styles based on the PDF width and height settings.
		 */
		if ( is_numeric( $this->atts['width'] ) ) {
			$extra_style .= 'width:' . (int) $this->atts['width'] . 'px;';
		} elseif ( $this->atts['width'] !== 'max' && $this->atts['width'] !== 'auto' ) {
			$this->atts['width'] = 'max';
		}

		if ( is_numeric( $this->atts['height'] ) ) {
			$extra_style .= 'height:' . (int) $this->atts['height'] . 'px;';
		} elseif ( $this->atts['height'] !== 'max' && $this->atts['height'] !== 'auto' ) {
			$this->atts['height'] = 'max';
		}

		/**
		 * Filter the HTML attributes for the PDF Embedder shortcode.
		 *
		 * @since 4.7.0
		 *
		 * @param array $html_attr HTML attributes.
		 */
		$html_attr = apply_filters(
			'pdfemb_shortcode_html_attributes',
			[
				'class'              => 'pdfemb-viewer',
				'style'              => $extra_style,
				'data-width'         => $this->atts['width'],
				'data-height'        => $this->atts['height'],
				'data-toolbar'       => $this->atts['toolbar'],
				'data-toolbar-fixed' => $this->atts['toolbarfixed'],
			]
		);

		$html_node .= '<a href="' . esc_url( set_url_scheme( $this->atts['url'] ) ) . '"';

		foreach ( $html_attr as $key => $value ) {
			if ( ! is_scalar( $key ) || ! is_scalar( $value ) ) {
				continue;
			}

			$html_node .= ' ' . sanitize_key( (string) $key ) . '="' . esc_attr( (string) $value ) . '"';
		}

		$html_node .= '>';

		$html_node .= esc_html( $title );

		$html_node .= '</a>';

		return $html_node;
	}

	/**
	 * Inline scripts and styles for the shortcode.
	 *
	 * @since 4.8.0
	 */
	public function enqueue_inline_assets() { // phpcs:ignore WPForms.PHP.HooksMethod.InvalidPlaceForAddingHooks

		// Assets should be enqueued only once on a page.
		// They are shared across all instances of the shortcode.
		static $is_enqueued = false;

		if ( $is_enqueued ) {
			return;
		}

		$is_enqueued = true;

		wp_enqueue_script( 'pdfemb_embed_pdf' );

		add_filter(
			'script_loader_tag',
			static function ( $tag, $handle, $src ) {
				if ( $handle === 'pdfemb_embed_pdf' ) {
					// phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript
					$tag = '<script type="module" src="' . esc_url( $src ) . '"></script>';
				}

				return $tag;
			},
			10,
			3
		);

		wp_enqueue_script( 'pdfemb_pdfjs' );

		wp_enqueue_style(
			'pdfemb_embed_pdf_css',
			Assets::url( 'css/pdfemb.css', true ),
			[],
			Assets::ver()
		);
	}
}
