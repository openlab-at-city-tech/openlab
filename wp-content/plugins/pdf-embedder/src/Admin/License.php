<?php

namespace PDFEmbedder\Admin;

use PDFEmbedder\Helpers\Macroable;

/**
 * License class.
 *
 * @since 4.7.0
 */
class License {
	use Macroable;

	/**
	 * Option key where the license status is stored.
	 *
	 * @since 4.7.0
	 */
	const OPTION_STATUS = 'pdfemb_premium_license_status';

	/**
	 * Get the current installation license key.
	 * PDFEMB_LICENSE_KEY constant has higher priority.
	 *
	 * @since 4.7.0
	 */
	public static function get_key(): string {

		// Allow wp-config constant to pass key.
		if ( defined( 'PDFEMB_LICENSE_KEY' ) && PDFEMB_LICENSE_KEY ) {
			return PDFEMB_LICENSE_KEY;
		}

		return pdf_embedder()->options()->get()['pdfemb_license_key'] ?? '';
	}

	/**
	 * Get the current installation license type.
	 * TODO: Implement the correct type (lite, premium, secure).
	 *
	 * @since 4.7.0
	 */
	public static function get_type(): string {

		/**
		 * Filter the license type.
		 *
		 * @since 4.7.0
		 *
		 * @param string $type License type.
		 */
		return (string) apply_filters( 'pdfemb_license_type', 'lite' );
	}

	/**
	 * Get the current installation license status.
	 *
	 * @since 4.7.0
	 */
	public static function get_status(): string {

		/**
		 * Filter the license status.
		 *
		 * @since 4.7.0
		 *
		 * @param string $status License status.
		 */
		return (string) apply_filters( 'pdfemb_license_status', 'lite' );
	}

	/**
	 * Get the error text depending on the type of the error.
	 *
	 * @since 4.7.0
	 *
	 * @param string $error License error.
	 */
	public static function get_error_text( string $error ): string {

		$error_strings = [
			'too_short'     => esc_html__( 'License key is too short.', 'pdf-embedder' ),
			'invalid'       => esc_html__( 'License key failed to activate.', 'pdf-embedder' ),
			'missing'       => esc_html__( 'License key does not exist in our system.', 'pdf-embedder' ),
			'expired'       => esc_html__( 'License key has expired.', 'pdf-embedder' ),
			'site_inactive' => esc_html__( 'License key is not permitted for this website.', 'pdf-embedder' ),
			'inactive'      => esc_html__( 'License key is not active for this website.', 'pdf-embedder' ),
			'disabled'      => esc_html__( 'License key has been disabled.', 'pdf-embedder' ),
			'empty'         => esc_html__( 'License key was not provided.', 'pdf-embedder' ),
		];

		return $error_strings[ $error ] ?? esc_html__( 'Unspecified error', 'pdf-embedder' );
	}
}
