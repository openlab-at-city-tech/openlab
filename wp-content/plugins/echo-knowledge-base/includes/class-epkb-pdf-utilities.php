<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Shared PDF utilities used across article import, training notes, and AI storage uploads.
 */
class EPKB_PDF_Utilities {

	const MAX_PDF_FILE_SIZE = 51380224; // 49 MB.
	const PDF_AI_EXECUTION_TIME_LIMIT = 600; // 10 minutes.
	const PDF_HEADER_SEARCH_WINDOW = 1024;
	const MAX_BASE64_PDF_LENGTH = 68506968; // 49 MB encoded as base64.
	const MAX_AI_STRUCTURE_TEXT_LENGTH = 400000;
	const MAX_AI_STRUCTURE_CHUNKS = 5;
	const AI_STRUCTURE_CHUNK_SIZE = 80000;

	/** @var array|null Last AI structuring debug info: model, elapsed_seconds, chunks */
	private static $last_ai_debug = null;
	private static $timeout_error_handler_registered = false;
	private static $timeout_error_response_type = 'ajax';

	/**
	 * Get debug info from the most recent AI structuring call.
	 *
	 * @return array|null
	 */
	public static function get_last_ai_debug() {
		return self::$last_ai_debug;
	}

	/**
	 * Get the PDF size validation error message.
	 *
	 * @return string
	 */
	public static function get_pdf_too_large_message() {
		return sprintf(
			/* translators: %s is the maximum PDF file size. */
			__( 'PDF file exceeds the %s size limit.', 'echo-knowledge-base' ),
			size_format( self::MAX_PDF_FILE_SIZE )
		);
	}

	/**
	 * Set shared timeout handling for long-running PDF requests.
	 *
	 * @param string $response_type ajax|rest
	 */
	public static function setup_timeout_error_handling( $response_type = 'ajax' ) {

		self::$timeout_error_response_type = $response_type === 'rest' ? 'rest' : 'ajax';

		if ( function_exists( 'set_time_limit' ) ) {
			@set_time_limit( self::PDF_AI_EXECUTION_TIME_LIMIT );
		}

		if ( self::$timeout_error_handler_registered || ! function_exists( 'register_shutdown_function' ) ) {
			return;
		}

		self::$timeout_error_handler_registered = true;
		register_shutdown_function( array( __CLASS__, 'maybe_send_timeout_error' ) );
	}

	/**
	 * Convert PHP execution timeout fatals into JSON responses for PDF requests.
	 */
	public static function maybe_send_timeout_error() {

		$error = error_get_last();
		if ( empty( $error['message'] ) || empty( $error['type'] ) || (int) $error['type'] !== E_ERROR ) {
			return;
		}

		if ( strpos( $error['message'], 'Maximum execution time' ) === false ) {
			return;
		}

		$timeout_seconds = self::extract_timeout_seconds( $error['message'] );
		while ( ob_get_level() > 0 ) {
			@ob_end_clean();
		}

		if ( ! headers_sent() ) {
			status_header( 500 );
			header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
		}

		if ( self::$timeout_error_response_type === 'rest' ) {
			echo wp_json_encode( array(
				'code'    => 'pdf_timeout',
				'message' => self::get_pdf_timeout_error_message( $timeout_seconds ),
				'data'    => array(
					'status'          => 500,
					'timeout_seconds' => $timeout_seconds,
				),
			) );
			return;
		}

		echo wp_json_encode( array(
			'error'           => true,
			'message'         => self::get_pdf_timeout_error_message( $timeout_seconds ),
			'error_code'      => 'pdf_timeout',
			'timeout_seconds' => $timeout_seconds,
		) );
	}

	/**
	 * Get the user-facing timeout message for PDF requests.
	 *
	 * @param int $timeout_seconds
	 * @return string
	 */
	public static function get_pdf_timeout_error_message( $timeout_seconds = 0 ) {

		$timeout_seconds = $timeout_seconds > 0 ? $timeout_seconds : self::PDF_AI_EXECUTION_TIME_LIMIT;

		return sprintf(
			esc_html__( 'PDF processing timed out after %s seconds. Increase the server timeout and try again.', 'echo-knowledge-base' ),
			number_format_i18n( $timeout_seconds )
		);
	}

	/**
	 * Get the timeout duration from a PHP fatal error message.
	 *
	 * @param string $message
	 * @return int
	 */
	private static function extract_timeout_seconds( $message ) {

		if ( preg_match( '/Maximum execution time of\s+(\d+)(?:\+\d+)?\s+seconds exceeded/i', $message, $matches ) ) {
			return (int) $matches[1];
		}

		return 0;
	}

	/**
	 * Decode and validate a base64-encoded PDF payload.
	 *
	 * @param string $encoded_pdf Base64-encoded PDF data.
	 * @param string $file_name Original file name.
	 * @return array|WP_Error Array with file_name, file_size, content_hash, mime_type, binary keys.
	 */
	public static function decode_base64_pdf( $encoded_pdf, $file_name = 'document.pdf' ) {

		if ( ! is_string( $encoded_pdf ) ) {
			return new WP_Error( 'invalid_base64', __( 'Invalid PDF data.', 'echo-knowledge-base' ) );
		}

		$encoded_pdf = trim( $encoded_pdf );
		if ( $encoded_pdf === '' ) {
			return new WP_Error( 'empty_pdf', __( 'PDF file is empty.', 'echo-knowledge-base' ) );
		}

		if ( strpos( $encoded_pdf, ',' ) !== false && preg_match( '/^data:application\/pdf;base64,/i', $encoded_pdf ) ) {
			$parts = explode( ',', $encoded_pdf, 2 );
			$encoded_pdf = empty( $parts[1] ) ? '' : $parts[1];
		}

		$encoded_pdf = preg_replace( '/\s+/', '', $encoded_pdf );
		if ( empty( $encoded_pdf ) ) {
			return new WP_Error( 'invalid_base64', __( 'Invalid PDF data.', 'echo-knowledge-base' ) );
		}

		if ( strlen( $encoded_pdf ) > self::MAX_BASE64_PDF_LENGTH ) {
			return new WP_Error( 'pdf_too_large', self::get_pdf_too_large_message() );
		}

		$pdf_binary = base64_decode( $encoded_pdf, true );
		if ( $pdf_binary === false ) {
			return new WP_Error( 'invalid_base64', __( 'Invalid PDF data.', 'echo-knowledge-base' ) );
		}

		$validation = self::validate_pdf_binary( $pdf_binary, $file_name );
		if ( is_wp_error( $validation ) ) {
			return $validation;
		}

		$sanitized_file_name = sanitize_file_name( $file_name );
		if ( empty( $sanitized_file_name ) ) {
			$sanitized_file_name = 'document.pdf';
		}

		return array(
			'file_name'    => $sanitized_file_name,
			'file_size'    => strlen( $pdf_binary ),
			'content_hash' => md5( $pdf_binary ),
			'mime_type'    => self::detect_pdf_mime_type( $pdf_binary ),
			'binary'       => $pdf_binary,
		);
	}

	/**
	 * Validate raw PDF binary content.
	 *
	 * @param string $pdf_binary Raw PDF bytes.
	 * @param string $file_name Original file name.
	 * @return true|WP_Error
	 */
	public static function validate_pdf_binary( $pdf_binary, $file_name = 'document.pdf' ) {

		if ( ! is_string( $pdf_binary ) || $pdf_binary === '' ) {
			return new WP_Error( 'empty_pdf', __( 'PDF file is empty.', 'echo-knowledge-base' ) );
		}

		$sanitized_file_name = sanitize_file_name( $file_name );
		if ( empty( $sanitized_file_name ) ) {
			$sanitized_file_name = 'document.pdf';
		}

		if ( strtolower( pathinfo( $sanitized_file_name, PATHINFO_EXTENSION ) ) !== 'pdf' ) {
			return new WP_Error( 'invalid_pdf_extension', __( 'Please upload a valid PDF file.', 'echo-knowledge-base' ) );
		}

		if ( strlen( $pdf_binary ) > self::MAX_PDF_FILE_SIZE ) {
			return new WP_Error( 'pdf_too_large', self::get_pdf_too_large_message() );
		}

		$header_window = substr( $pdf_binary, 0, self::PDF_HEADER_SEARCH_WINDOW );
		if ( strpos( $header_window, '%PDF-' ) === false ) {
			return new WP_Error( 'invalid_pdf', __( 'The file does not appear to be a valid PDF.', 'echo-knowledge-base' ) );
		}

		$mime_type = self::detect_pdf_mime_type( $pdf_binary );
		if ( ! empty( $mime_type ) && ! in_array( $mime_type, self::get_allowed_pdf_mime_types(), true ) ) {
			return new WP_Error( 'invalid_pdf_mime', __( 'The file does not appear to be a valid PDF.', 'echo-knowledge-base' ) );
		}

		return true;
	}

	/**
	 * Convert extracted PDF text into display content.
	 *
	 * @param string $raw_text Extracted text.
	 * @param string $format_mode none|basic|ai
	 * @return string|WP_Error
	 */
	public static function format_text_for_display( $raw_text, $format_mode = 'basic' ) {

		self::reset_last_ai_debug();

		$raw_text = self::normalize_extracted_text( $raw_text );
		if ( $raw_text === '' ) {
			return new WP_Error( 'empty_content', __( 'PDF text content is empty.', 'echo-knowledge-base' ) );
		}

		if ( $format_mode === 'ai' ) {
			return self::ai_structure_text( $raw_text );
		}

		if ( $format_mode === 'none' ) {
			return $raw_text;
		}

		return self::basic_text_to_html( $raw_text );
	}

	/**
	 * Convert a PDF into display content.
	 * AI mode uploads the PDF to the configured provider and lets the model read it directly.
	 * Basic and no-format modes use extracted text supplied by the caller.
	 *
	 * @param string $pdf_binary Raw PDF bytes.
	 * @param string $file_name Original file name.
	 * @param string $format_mode none|basic|ai
	 * @param string $raw_text Extracted text for basic/none modes.
	 * @return string|WP_Error
	 */
	public static function format_pdf_for_display( $pdf_binary, $file_name, $format_mode = 'basic', $raw_text = '' ) {

		if ( $format_mode === 'ai' ) {
			self::reset_last_ai_debug();
			return self::ai_structure_pdf( $pdf_binary, $file_name );
		}

		return self::format_text_for_display( $raw_text, $format_mode );
	}

	/**
	 * Normalize extracted text before formatting or hashing.
	 *
	 * @param string $raw_text Extracted text.
	 * @return string
	 */
	public static function normalize_extracted_text( $raw_text ) {

		if ( ! is_string( $raw_text ) ) {
			return '';
		}

		$raw_text = str_replace( array( "\r\n", "\r" ), "\n", $raw_text );
		$raw_text = preg_replace( '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $raw_text );
		$raw_text = preg_replace( "/\n{3,}/", "\n\n", $raw_text );

		return trim( $raw_text );
	}

	/**
	 * Convert note/article HTML into plain text before it is sent to AI.
	 *
	 * @param string $content Content to normalize.
	 * @return string
	 */
	public static function strip_html_for_ai( $content ) {

		$content = is_string( $content ) ? $content : '';
		if ( $content === '' ) {
			return '';
		}

		$content = preg_replace( '/<(br|hr)\b[^>]*\/?>/i', "\n", $content );
		$content = preg_replace( '/<\/(p|div|section|article|blockquote|li|ul|ol|table|tr|h[1-6])>/i', "\n\n", $content );
		$content = wp_strip_all_tags( $content, false );
		$content = html_entity_decode( $content, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
		$content = preg_replace( "/[ \t]+\n/", "\n", $content );
		$content = preg_replace( "/\n{3,}/", "\n\n", $content );

		return self::normalize_extracted_text( $content );
	}

	/**
	 * Sanitize note content while allowing safe markup.
	 *
	 * @param string $content Note content.
	 * @return string
	 */
	public static function sanitize_note_content( $content ) {

		$content = is_string( $content ) ? $content : '';
		$content = preg_replace( '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $content );

		return trim( wp_kses_post( $content ) );
	}

	/**
	 * Convert raw text to HTML by wrapping paragraphs in <p> tags.
	 *
	 * @param string $raw_text Raw extracted text.
	 * @return string
	 */
	public static function basic_text_to_html( $raw_text ) {

		$text = self::normalize_extracted_text( $raw_text );

		// Insert newline before inline bullet characters so they become separate lines
		$text = preg_replace( '/(?<!\n)([●•◦▪▸])(\s)/', "\n$1$2", $text );

		$lines = explode( "\n", $text );
		$html = '';
		$current_block = ''; // 'ul', 'ol', or ''
		$block_items = [];
		$ol_start = 1;
		$pending_blank = false;

		foreach ( $lines as $line ) {
			$line = trim( $line );
			if ( $line === '' ) {
				// Don't close list blocks on blank lines — wait to see if the list continues
				if ( $current_block !== '' ) {
					$pending_blank = true;
				}
				continue;
			}

			// Unordered list: line starts with bullet character or dash/asterisk followed by whitespace
			if ( preg_match( '/^[●•◦▪▸\-\*]\s+(.+)/', $line, $m ) ) {
				if ( $current_block !== 'ul' ) {
					$html .= self::close_block( $current_block, $block_items, $ol_start );
					$current_block = 'ul';
				}
				$pending_blank = false;
				$block_items[] = esc_html( $m[1] );
				continue;
			}

			// Ordered list: line starts with number followed by . or ) and whitespace
			if ( preg_match( '/^(\d+)[.)]\s+(.+)/', $line, $m ) ) {
				if ( $current_block !== 'ol' ) {
					$html .= self::close_block( $current_block, $block_items, $ol_start );
					$current_block = 'ol';
					$ol_start = intval( $m[1] );
				}
				$pending_blank = false;
				$block_items[] = esc_html( $m[2] );
				continue;
			}

			// Continuation line: inside a list block with no blank line before it and starts lowercase (word-wrapped mid-sentence)
			if ( $current_block !== '' && ! $pending_blank && ! empty( $block_items ) && preg_match( '/^[a-z]/', $line ) ) {
				$block_items[ count( $block_items ) - 1 ] .= ' ' . esc_html( $line );
				continue;
			}

			// Regular paragraph line
			$html .= self::close_block( $current_block, $block_items, $ol_start );
			$pending_blank = false;
			$html .= '<p>' . esc_html( $line ) . '</p>' . "\n";
		}

		$html .= self::close_block( $current_block, $block_items, $ol_start );

		return $html;
	}

	/**
	 * Close an open list block and return its HTML.
	 *
	 * @param string $block_type 'ul', 'ol', or ''
	 * @param array $items List items collected so far (cleared after use).
	 * @return string
	 */
	private static function close_block( &$block_type, &$items, &$ol_start ) {
		if ( empty( $items ) || $block_type === '' ) {
			$block_type = '';
			$items = [];
			$ol_start = 1;
			return '';
		}

		$tag = $block_type;
		$start_attr = ( $tag === 'ol' && $ol_start > 1 ) ? ' start="' . $ol_start . '"' : '';
		$html = '<' . $tag . $start_attr . '>' . "\n";
		foreach ( $items as $item ) {
			$html .= '<li>' . $item . '</li>' . "\n";
		}
		$html .= '</' . $tag . '>' . "\n";

		$block_type = '';
		$items = [];
		$ol_start = 1;

		return $html;
	}

	/**
	 * Validate PDF size against the current AI provider upload limit.
	 *
	 * @param string $pdf_binary Raw PDF bytes.
	 * @param string $file_name Original file name.
	 * @param string $purpose Request label for logging.
	 * @return true|WP_Error
	 */
	public static function validate_pdf_ai_size( $pdf_binary, $file_name, $purpose = 'pdf_request' ) {

		$file_size = strlen( $pdf_binary );
		$max_file_size = EPKB_AI_Provider::get_max_file_size();
		if ( ! empty( $max_file_size ) && $file_size > $max_file_size ) {
			EPKB_AI_Log::add_log( 'PDF AI request skipped because the file exceeds the AI size limit', array(
				'file_name'     => sanitize_file_name( $file_name ),
				'file_size'     => $file_size,
				'max_file_size' => $max_file_size,
				'provider'      => EPKB_AI_Provider::get_active_provider(),
				'purpose'       => $purpose,
			) );

			return new WP_Error(
				'content_too_large',
				sprintf(
					__( 'PDF is too large for AI processing. Maximum supported size is %s for the configured AI provider.', 'echo-knowledge-base' ),
					size_format( $max_file_size )
				),
				array(
					'status'        => 400,
					'file_size'     => $file_size,
					'max_file_size' => $max_file_size,
				)
			);
		}

		return true;
	}

	/**
	 * Use AI to structure raw text into well-formatted HTML.
	 * Falls back to basic_text_to_html() on error.
	 *
	 * @param string $raw_text Raw extracted text.
	 * @return string
	 */
	public static function ai_structure_text( $raw_text ) {

		$raw_text = self::normalize_extracted_text( $raw_text );
		if ( $raw_text === '' ) {
			return '';
		}

		if ( strlen( $raw_text ) > self::MAX_AI_STRUCTURE_TEXT_LENGTH ) {
			EPKB_AI_Log::add_log( 'PDF formatting fell back to basic formatting because the extracted text is too large for AI structuring', array(
				'text_length' => strlen( $raw_text ),
				'max_length'  => self::MAX_AI_STRUCTURE_TEXT_LENGTH,
			) );
			return self::basic_text_to_html( $raw_text );
		}

		$model = self::get_ai_structuring_model_name();
		$start_time = microtime( true );

		$max_chunk_size = 100000;

		if ( strlen( $raw_text ) <= $max_chunk_size ) {
			$result = self::ai_structure_chunk( $raw_text );
			self::store_ai_structuring_debug( $model, $start_time, 1, array(
				'text_length' => strlen( $raw_text ),
			) );

			return is_wp_error( $result ) ? self::basic_text_to_html( $raw_text ) : $result;
		}

		$chunks = self::split_text_into_chunks( $raw_text, self::AI_STRUCTURE_CHUNK_SIZE );
		if ( count( $chunks ) > self::MAX_AI_STRUCTURE_CHUNKS ) {
			EPKB_AI_Log::add_log( 'PDF formatting fell back to basic formatting because the extracted text would require too many AI requests', array(
				'chunk_count' => count( $chunks ),
				'max_chunks'  => self::MAX_AI_STRUCTURE_CHUNKS,
			) );
			return self::basic_text_to_html( $raw_text );
		}

		$html_parts = array();

		foreach ( $chunks as $chunk ) {
			$result = self::ai_structure_chunk( $chunk );
			$html_parts[] = is_wp_error( $result ) ? self::basic_text_to_html( $chunk ) : $result;
		}

		self::store_ai_structuring_debug( $model, $start_time, count( $chunks ), array(
			'text_length' => strlen( $raw_text ),
		) );

		return implode( "\n", $html_parts );
	}

	/**
	 * Use AI to structure a PDF directly into well-formatted HTML.
	 *
	 * @param string $pdf_binary Raw PDF bytes.
	 * @param string $file_name Original file name.
	 * @return string|WP_Error
	 */
	public static function ai_structure_pdf( $pdf_binary, $file_name ) {

		$validation = self::validate_pdf_binary( $pdf_binary, $file_name );
		if ( is_wp_error( $validation ) ) {
			return $validation;
		}

		$size_validation = self::validate_pdf_ai_size( $pdf_binary, $file_name, 'pdf_import_structure' );
		if ( is_wp_error( $size_validation ) ) {
			return $size_validation;
		}

		$model = self::get_ai_structuring_model_name();
		$start_time = microtime( true );

		$prompt = 'IMPORTANT: Output raw HTML tags only — no Markdown syntax whatsoever. ' .
			'Convert the attached PDF into well-structured HTML for a knowledge base article. ' .
			'Use <h2> for major sections, <h3> for subsections, <p> for paragraphs, <ul>/<ol> for lists, <strong> for bold, <em> for italic. ' .
			'Do NOT use ## headings, **bold**, *italic*, or - bullet lists. No <html>/<body> wrappers, no code fences. ' .
			'Clean up PDF artifacts (broken words, page numbers, repeated headers/footers). ' .
			'Preserve ALL content — do not summarize, shorten, or omit any text.';

		$instructions = 'You are a knowledge base content expert specializing in document formatting and content structuring.';

		$response_text = self::send_ai_request( $prompt, $instructions, 'pdf_import_structure', array(
			'file_content' => $pdf_binary,
			'mime_type'    => 'application/pdf',
			'file_name'    => $file_name,
		) );
		if ( is_wp_error( $response_text ) ) {
			return $response_text;
		}

		$response_text = preg_replace( '/^```html\s*/i', '', $response_text );
		$response_text = preg_replace( '/\s*```$/', '', $response_text );
		$response_text = self::convert_markdown_to_html( $response_text );
		$response_text = self::sanitize_structured_html_response( $response_text );
		if ( is_wp_error( $response_text ) ) {
			return $response_text;
		}

		self::store_ai_structuring_debug( $model, $start_time, 1, array(
			'file_name' => sanitize_file_name( $file_name ),
			'file_size' => strlen( $pdf_binary ),
		) );

		return $response_text;
	}

	/**
	 * Split text into chunks at paragraph boundaries.
	 *
	 * @param string $text Text to split.
	 * @param int    $target_size Target chunk size.
	 * @return array
	 */
	private static function split_text_into_chunks( $text, $target_size ) {

		$paragraphs = preg_split( '/(\n\s*\n)/', $text, -1, PREG_SPLIT_DELIM_CAPTURE );
		$chunks = [];
		$current_chunk = '';

		foreach ( $paragraphs as $paragraph ) {
			if ( strlen( $current_chunk ) + strlen( $paragraph ) > $target_size && $current_chunk !== '' ) {
				$chunks[] = trim( $current_chunk );
				$current_chunk = '';
			}

			$current_chunk .= $paragraph;
		}

		if ( trim( $current_chunk ) !== '' ) {
			$chunks[] = trim( $current_chunk );
		}

		return $chunks;
	}

	/**
	 * Ask AI to structure a single chunk of extracted text.
	 *
	 * @param string $text Text chunk.
	 * @return string|WP_Error
	 */
	private static function ai_structure_chunk( $text ) {

		$prompt = 'IMPORTANT: Output raw HTML tags only — no Markdown syntax whatsoever. ' .
			'Convert the following raw text extracted from a PDF into well-structured HTML for a knowledge base article. ' .
			'Use <h2> for major sections, <h3> for subsections, <p> for paragraphs, <ul>/<ol> for lists, <strong> for bold, <em> for italic. ' .
			'Do NOT use ## headings, **bold**, *italic*, or - bullet lists. No <html>/<body> wrappers, no code fences. ' .
			'Clean up PDF artifacts (broken words, page numbers, repeated headers/footers). ' .
			'Preserve ALL content — do not summarize, shorten, or omit any text.' . "\n\n" . $text;

		$instructions = 'You are a knowledge base content expert specializing in document formatting and content structuring.';

		$response_text = self::send_ai_request( $prompt, $instructions, 'pdf_import_structure' );
		if ( is_wp_error( $response_text ) ) {
			return $response_text;
		}

		$response_text = preg_replace( '/^```html\s*/i', '', $response_text );
		$response_text = preg_replace( '/\s*```$/', '', $response_text );
		$response_text = self::convert_markdown_to_html( $response_text );
		$response_text = self::sanitize_structured_html_response( $response_text );
		if ( is_wp_error( $response_text ) ) {
			return $response_text;
		}

		return $response_text;
	}

	/**
	 * Reset AI structuring debug info before a new formatting request.
	 */
	private static function reset_last_ai_debug() {
		self::$last_ai_debug = null;
	}

	/**
	 * Get the model name used for AI structuring debug output.
	 *
	 * @return string
	 */
	private static function get_ai_structuring_model_name() {

		$runtime_profile = EPKB_AI_Provider::get_runtime_profile( 'pdf' );

		return isset( $runtime_profile['model'] ) ? $runtime_profile['model'] : 'unknown';
	}

	/**
	 * Persist debug details for the latest AI structuring request.
	 *
	 * @param string $model Model name.
	 * @param float  $start_time Request start time.
	 * @param int    $chunks Number of AI requests used.
	 * @param array  $context Additional log context.
	 */
	private static function store_ai_structuring_debug( $model, $start_time, $chunks, $context = array() ) {

		$elapsed = round( microtime( true ) - $start_time, 2 );

		self::$last_ai_debug = array(
			'model'           => $model,
			'elapsed_seconds' => $elapsed,
			'chunks'          => $chunks,
		);

		EPKB_AI_Log::add_log( 'PDF AI structuring completed', array_merge(
			array(
				'model'           => $model,
				'elapsed_seconds' => $elapsed,
				'chunks'          => $chunks,
			),
			$context
		) );
	}

	/**
	 * Send a prompt to the active AI provider and return the response text.
	 *
	 * @param string $prompt       User prompt text.
	 * @param string $instructions System instructions.
	 * @param string $label        Request label for logging.
	 * @param array  $attachment   Optional file attachment with 'file_content', 'mime_type', 'file_name' keys.
	 * @return string|WP_Error
	 */
	public static function send_ai_request( $prompt, $instructions, $label = 'ai_request', $attachment = array() ) {
		return EPKB_AI_Provider::send_prompt_request( $prompt, $instructions, $label, 'pdf', null, $attachment );
	}

	/**
	 * Detect the MIME type of a PDF binary if fileinfo is available.
	 *
	 * @param string $pdf_binary Raw PDF bytes.
	 * @return string
	 */
	private static function detect_pdf_mime_type( $pdf_binary ) {

		if ( ! function_exists( 'finfo_open' ) || ! defined( 'FILEINFO_MIME_TYPE' ) ) {
			return '';
		}

		$finfo = finfo_open( FILEINFO_MIME_TYPE );
		if ( $finfo === false ) {
			return '';
		}

		$mime_type = finfo_buffer( $finfo, substr( $pdf_binary, 0, min( strlen( $pdf_binary ), 65536 ) ) );
		finfo_close( $finfo );

		return is_string( $mime_type ) ? $mime_type : '';
	}

	/**
	 * Supported PDF MIME types returned by different fileinfo implementations.
	 *
	 * @return array
	 */
	private static function get_allowed_pdf_mime_types() {
		return array(
			'application/pdf',
			'application/x-pdf',
			'application/acrobat',
			'applications/vnd.pdf',
			'text/pdf',
			'text/x-pdf',
			'application/octet-stream',
		);
	}

	/**
	 * Convert common Markdown patterns to HTML as a safety net for AI responses that ignore the HTML-only instruction.
	 * If the response is already valid HTML, the regexes match nothing and the text passes through unchanged.
	 *
	 * @param string $text AI response text that may contain Markdown.
	 * @return string Text with Markdown converted to HTML.
	 */
	private static function convert_markdown_to_html( $text ) {

		// Headings: process ###### down to # to avoid partial matches
		for ( $level = 6; $level >= 1; $level-- ) {
			$hashes = str_repeat( '#', $level );
			$text = preg_replace( '/^' . $hashes . '\s+(.+)$/m', '<h' . $level . '>$1</h' . $level . '>', $text );
		}

		// Bold: **text** → <strong>text</strong> (must run before italic)
		$text = preg_replace( '/\*\*(.+?)\*\*/', '<strong>$1</strong>', $text );

		// Italic: *text* → <em>text</em>
		$text = preg_replace( '/\*(.+?)\*/', '<em>$1</em>', $text );

		// Unordered lists: consecutive lines starting with "- "
		$text = preg_replace_callback( '/((?:^- .+$\n?)+)/m', array( __CLASS__, 'convert_md_unordered_list' ), $text );

		// Ordered lists: consecutive lines starting with "1. ", "2. ", etc.
		$text = preg_replace_callback( '/((?:^\d+\. .+$\n?)+)/m', array( __CLASS__, 'convert_md_ordered_list' ), $text );

		return $text;
	}

	/**
	 * Callback: convert matched block of "- item" lines into <ul><li>...</li></ul>.
	 */
	private static function convert_md_unordered_list( $matches ) {
		$lines = preg_split( '/\n/', trim( $matches[1] ) );
		$items = '';
		foreach ( $lines as $line ) {
			$items .= '<li>' . preg_replace( '/^- /', '', $line ) . '</li>';
		}
		return '<ul>' . $items . '</ul>';
	}

	/**
	 * Callback: convert matched block of "1. item" lines into <ol><li>...</li></ol>.
	 */
	private static function convert_md_ordered_list( $matches ) {
		$lines = preg_split( '/\n/', trim( $matches[1] ) );
		$items = '';
		foreach ( $lines as $line ) {
			$items .= '<li>' . preg_replace( '/^\d+\. /', '', $line ) . '</li>';
		}
		return '<ol>' . $items . '</ol>';
	}

	/**
	 * Keep only the safe HTML subset used by PDF previews and imported content.
	 *
	 * @param string $html Structured HTML returned by AI.
	 * @return string|WP_Error
	 */
	private static function sanitize_structured_html_response( $html ) {

		$html = is_string( $html ) ? trim( $html ) : '';
		if ( $html === '' ) {
			EPKB_AI_Log::add_log( 'Warning: AI did not return a response while formatting the PDF. Please review the request and try again.', array( 'purpose' => 'pdf_import_structure' ) );
			return new WP_Error( 'empty_response', __( 'AI did not return a response. Please try again.', 'echo-knowledge-base' ) );
		}

		$sanitized_html = trim( wp_kses( $html, self::get_allowed_structured_html_tags() ) );
		if ( $sanitized_html !== '' ) {
			return strpos( $sanitized_html, '<' ) === false ? self::basic_text_to_html( $sanitized_html ) : $sanitized_html;
		}

		$plain_text = self::strip_html_for_ai( $html );
		if ( $plain_text !== '' ) {
			return self::basic_text_to_html( $plain_text );
		}

		EPKB_AI_Log::add_log( 'Warning: AI did not return usable content while formatting the PDF. Please review the request and try again.', array( 'purpose' => 'pdf_import_structure' ) );

		return new WP_Error( 'empty_response', __( 'AI did not return a response. Please try again.', 'echo-knowledge-base' ) );
	}

	/**
	 * Allow only the markup needed for structured PDF content.
	 *
	 * @return array
	 */
	private static function get_allowed_structured_html_tags() {
		return array(
			'p'          => array(),
			'br'         => array(),
			'hr'         => array(),
			'h1'         => array(),
			'h2'         => array(),
			'h3'         => array(),
			'h4'         => array(),
			'h5'         => array(),
			'h6'         => array(),
			'ul'         => array(),
			'ol'         => array(),
			'li'         => array(),
			'strong'     => array(),
			'em'         => array(),
			'b'          => array(),
			'i'          => array(),
			'blockquote' => array(),
			'pre'        => array(),
			'code'       => array(),
			'sup'        => array(),
			'sub'        => array(),
			'table'      => array(),
			'thead'      => array(),
			'tbody'      => array(),
			'tr'         => array(),
			'th'         => array(
				'colspan' => true,
				'rowspan' => true,
				'scope'   => true,
			),
			'td'         => array(
				'colspan' => true,
				'rowspan' => true,
			),
		);
	}
}
