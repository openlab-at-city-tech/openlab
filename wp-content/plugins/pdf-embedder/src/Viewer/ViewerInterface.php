<?php

namespace PDFEmbedder\Viewer;

/**
 * Interface for the PDF viewer.
 *
 * @since 4.8.0
 */
interface ViewerInterface {

	/**
	 * Render the PDF viewer.
	 *
	 * @since 4.8.0
	 */
	public function render(): string;

	/**
	 * Inline scripts and styles for the shortcode.
	 *
	 * @since 4.8.0
	 */
	public function enqueue_inline_assets();

	/**
	 * Pass the options needed for the viewer.
	 *
	 * @since 4.8.0
	 *
	 * @param array $atts Attributes.
	 */
	public function set_options( array $atts = [] );
}
