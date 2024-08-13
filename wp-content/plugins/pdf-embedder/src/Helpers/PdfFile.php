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
	 *
	 * @param string $url The URL of the attachment.
	 */
	public static function get_id_by_url( string $url ): int {

		global $wpdb;

		// Get the attachment ID by its URL.
		$pdf_id = wp_cache_get( 'pdfemb_url_to_id_' . md5( $url ) );

		if ( empty( $pdf_id ) ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$pdf_id = $wpdb->get_var(
				$wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type = 'attachment' AND guid = %s;", $url )
			);

			wp_cache_set( 'pdfemb_url_to_id_' . md5( $url ), $pdf_id );
		}

		return (int) $pdf_id;
	}
}
