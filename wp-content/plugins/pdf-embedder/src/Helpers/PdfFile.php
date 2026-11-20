<?php

namespace PDFEmbedder\Helpers;

/**
 * Class PdfFile for file-specific helper methods.
 *
 * @since 4.8.0
 */
class PdfFile {

	/**
	 * Get the PDF file ID by its URL.
	 *
	 * @since 4.8.0
	 * @since 5.0.1 Switched from a `guid` match to `attachment_url_to_postid()`.
	 *
	 * @param string $url The URL of the attachment.
	 */
	public static function get_id_by_url( string $url ): int {

		$cache_key = 'pdfemb_url_to_id_' . md5( $url );
		$pdf_id    = wp_cache_get( $cache_key );

		if ( empty( $pdf_id ) ) {
			$pdf_id = attachment_url_to_postid( $url );

			wp_cache_set( $cache_key, $pdf_id );
		}

		return (int) $pdf_id;
	}
}
