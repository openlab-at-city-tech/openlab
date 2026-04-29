<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * AI-based PDF text extraction for scanned/image PDFs.
 * Used when client-side PDF.js extraction returns empty text.
 */
class EPKB_AI_PDF_Extractor {

	/**
	 * Extract text from a PDF binary using AI (ChatGPT or Gemini).
	 *
	 * @param string $pdf_binary Raw PDF file content
	 * @param string $file_name Original file name for logging
	 * @return array|WP_Error Array with 'text' key on success
	 */
	public static function extract_text_from_pdf( $pdf_binary, $file_name ) {

		$validation = EPKB_PDF_Utilities::validate_pdf_binary( $pdf_binary, $file_name );
		if ( is_wp_error( $validation ) ) {
			return $validation;
		}

		$size_validation = EPKB_PDF_Utilities::validate_pdf_ai_size( $pdf_binary, $file_name, 'pdf_extraction' );
		if ( is_wp_error( $size_validation ) ) {
			return $size_validation;
		}

		$prompt = 'Extract all text content from this PDF document. Return only the extracted text, preserving paragraph structure. Do not add commentary.';
		$instructions = 'You are a document text extraction specialist. Extract text accurately and completely.';

		$response_text = EPKB_PDF_Utilities::send_ai_request( $prompt, $instructions, 'pdf_extraction', array(
			'file_content' => $pdf_binary,
			'mime_type'    => 'application/pdf',
			'file_name'    => $file_name,
		) );

		if ( is_wp_error( $response_text ) ) {
			return $response_text;
		}

		if ( empty( trim( $response_text ) ) ) {
			$file_size = strlen( $pdf_binary );
			EPKB_AI_Log::add_log( 'AI PDF extraction returned empty content', array(
				'file_name' => sanitize_file_name( $file_name ),
				'file_size' => $file_size,
			) );

			return new WP_Error(
				'empty_extraction',
				__( 'AI was unable to extract text from this PDF.', 'echo-knowledge-base' ),
				array( 'status' => 400 )
			);
		}

		return array( 'text' => $response_text );
	}
}
