<?php
/**
 * CSV export for the Editoria11y dashboard.
 *
 * Hooks into `admin_init`, gates on `?ed11y_export_results_csv` + a valid
 * nonce + the same capability the dashboard menu uses, then streams a CSV
 * to the browser. CSV-injection-safe: every cell value runs through
 * `sanitize_value()` before write.
 *
 * @package Editoria11y
 */

namespace Editoria11y\Admin;

use Editoria11y\TestNames;
use WP_User_Query;

defined( 'ABSPATH' ) || exit;

/**
 * Owns the CSV export endpoint.
 */
class CsvExport {

	/** Wire the admin_init listener. */
	public function init() {
		add_action( 'admin_init', array( __CLASS__, 'maybe_export' ) );
	}

	/**
	 * Sanitize a value for safe CSV output, preventing CSV injection.
	 *
	 * @see https://owasp.org/www-community/attacks/CSV_Injection
	 *
	 * @param mixed $value The value to sanitize.
	 * @return mixed The sanitized value (strings are escaped, other types
	 *               passed through).
	 */
	public static function sanitize_value( $value ) {
		if ( ! is_string( $value ) ) {
			return $value;
		}
		// Flatten control characters that could split cells. Quote escaping
		// deliberately stays with fputcsv() — a manual "→"" pass here got
		// re-doubled by fputcsv and corrupted every cell containing quotes.
		$escaped = preg_replace( '/[\t\n\r]/', ' ', $value );
		if ( preg_match( '/^[=\-@+]/', $escaped ) ) {
			// Neutralize spreadsheet formula injection.
			return "'" . $escaped;
		}
		return $escaped;
	}

	/**
	 * Rows fetched per query. The export previously buffered the entire
	 * results join in PHP before streaming — an OOM risk on large scanned
	 * sites; batches keep memory flat regardless of table size.
	 */
	const EXPORT_BATCH = 1000;

	/**
	 * Gate for the export endpoint: query flag + nonce + the shared
	 * report-reader capability (the export can never be looser than the
	 * dashboard it exports). Split from the streaming so tests can cover
	 * the boundary without hitting exit().
	 */
	public static function current_request_is_authorized_export(): bool {
		return isset( $_GET['ed11y_export_results_csv'] )
			&& isset( $_REQUEST['_wpnonce'] )
			&& wp_verify_nonce( $_REQUEST['_wpnonce'], 'ed1ref' ) // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- nonce values are compared, not stored.
			&& current_user_can( ed11y_report_reader_capability() );
	}

	/**
	 * If the current request is a CSV export request, stream the CSV and
	 * exit. Otherwise no-op.
	 *
	 * @SuppressWarnings(PHPMD.ExitExpression)
	 */
	public static function maybe_export() {

		if ( ! self::current_request_is_authorized_export() ) {
			// Incorrect referrer, nonce or user role.
			return;
		}

		// Discard any stray buffered output (another plugin echoing on
		// admin_init) — it would prepend garbage to the download — and
		// bail if headers are already gone: a corrupt inline dump is
		// worse than a failed download.
		while ( ob_get_level() > 0 ) {
			ob_end_clean();
		}
		if ( headers_sent() ) {
			return;
		}

		// Generate filename with site name and current date.
		$site_title = sanitize_file_name( get_bloginfo( 'name' ) );
		$date       = gmdate( 'Y-m-d' );
		$filename   = "{$site_title}_accessibility_issues_{$date}.csv";

		/**
		 * Filter the CSV export filename.
		 *
		 * @param string $filename The default filename.
		 */
		$filename = apply_filters( 'ed11y_csv_export_filename', $filename );
		// Re-sanitize after the filter: a quote or CR/LF from a third-party
		// callback would break (or inject into) the Content-Disposition header.
		$filename = sanitize_file_name( $filename );

		header( 'Content-type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
		header( 'Pragma: no-cache' );
		header( 'Expires: 0' );

		$file = fopen( 'php://output', 'w' ); // phpcs:ignore WordPress.WP.AlternativeFunctions -- streaming to the response body, not the filesystem.
		self::write_csv( $file );

		exit();
	}

	/**
	 * Write the report rows as CSV to an open stream.
	 *
	 * Public and handle-based so tests can stream into php://temp. Rows
	 * are fetched in EXPORT_BATCH chunks (page_url/pid/result_key ordered
	 * for a stable walk); author display names are resolved per batch and
	 * memoized across batches.
	 *
	 * @param resource $file Writable stream.
	 *
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
	 */
	public static function write_csv( $file ): void {
		$test_name = TestNames::core_names();

		global $wpdb;
		$utable     = $wpdb->prefix . 'ed11y_urls';
		$rtable     = $wpdb->prefix . 'ed11y_results';
		$post_table = $wpdb->prefix . 'posts';

		// UTF-8 BOM: Excel misreads unmarked UTF-8 CSVs as ANSI, turning
		// non-ASCII page titles into mojibake.
		fwrite( $file, "\xEF\xBB\xBF" ); // phpcs:ignore WordPress.WP.AlternativeFunctions

		/**
		 * Action hook to add metadata rows before the header and data rows.
		 * Hooked functions should write their own fputcsv() calls.
		 *
		 * @param resource $file The file handle for php://output.
		 * @param array    $data Deprecated as of the 3.0 streaming rewrite —
		 *                       rows are now fetched in batches after this
		 *                       hook fires, so this is always an empty array.
		 */
		do_action( 'ed11y_csv_export_before_headers', $file, array() );

		// Default CSV headers.
		$headers = array(
			__( 'Page', 'editoria11y' ),
			__( 'URL', 'editoria11y' ),
			__( 'Issue', 'editoria11y' ),
			__( 'Count', 'editoria11y' ),
			__( 'Author', 'editoria11y' ),
			__( 'Page Type', 'editoria11y' ),
			__( 'Detected on', 'editoria11y' ),
			__( 'Status', 'editoria11y' ),
			__( 'Edit', 'editoria11y' ),
		);

		/**
		 * Filter the CSV column headers.
		 *
		 * @param array $headers The default column headers.
		 */
		$headers = apply_filters( 'ed11y_csv_export_headers', $headers );

		fputcsv( $file, $headers );

		$admin       = get_admin_url();
		$date_format = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
		$authors     = array();
		$offset      = 0;

		do {
			// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery -- table names are $wpdb->prefix.literal; complex join, batched read.
			$data = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT
					{$rtable}.result_key,
					{$rtable}.result_count,
					{$utable}.pid,
					{$utable}.page_url,
					{$utable}.page_title,
					{$utable}.entity_type,
					{$utable}.page_total,
					{$utable}.post_id,
					{$post_table}.post_author,
					{$post_table}.post_status,
					{$rtable}.created as created
					FROM {$rtable}
					LEFT JOIN {$utable} ON {$utable}.pid={$rtable}.pid
					LEFT JOIN {$post_table} ON {$utable}.post_id={$post_table}.ID
					ORDER BY page_url ASC, {$rtable}.pid ASC, {$rtable}.result_key ASC
					LIMIT %d OFFSET %d;",
					self::EXPORT_BATCH,
					$offset
				)
			);
			// phpcs:enable
			$batch_size = count( $data );
			$offset    += $batch_size;

			// Resolve display names new to this batch. An empty `include`
			// would make WP_User_Query return EVERY user, so only query
			// when the batch introduced unseen author ids.
			$user_ids = array();
			foreach ( $data as $value ) {
				if ( $value->post_author && ! isset( $authors[ $value->post_author ] ) && ! in_array( $value->post_author, $user_ids, true ) ) {
					$user_ids[] = $value->post_author;
				}
			}
			if ( ! empty( $user_ids ) ) {
				$user_query = new WP_User_Query(
					array(
						'include' => $user_ids,
						'fields'  => array( 'ID', 'display_name' ),
					)
				);
				foreach ( $user_query->get_results() as $value ) {
					$authors[ $value->ID ] = $value->display_name;
				}
			}

			/**
			 * Filter the CSV export data rows.
			 *
			 * @param array $data The current batch of result rows (the export
			 *                    streams in batches as of 3.0; this fires once
			 *                    per batch, not once for the whole set).
			 */
			$data = apply_filters( 'ed11y_csv_export_data', $data );

			foreach ( $data as $result ) {

				$row = array(
					$result->page_title,
					$result->page_url,
					$test_name[ $result->result_key ] ?? '',
					$result->result_count,
					$authors[ $result->post_author ] ?? '',
					$result->entity_type,
					// Stored as UTC; render in the site's timezone and format.
					get_date_from_gmt( (string) $result->created, $date_format ),
					$result->post_status ?? 'publish',
					// Gate the edit-link on the integer post_id, not on
					// $result->post_status — the latter is a string ('publish' /
					// 'draft' / etc.) and `0 < 'publish'` evaluates falsy in PHP 8,
					// which previously sent every row down the page_url branch.
					// post_id===0 (archive / taxonomy / search routes) has no
					// backing edit page, so the page_url is the right link there.
					0 < (int) $result->post_id ?
						$admin . 'post.php?post=' . (int) $result->post_id . '&action=edit'
						: $result->page_url,
				);

				/**
				 * Filter each CSV row before export.
				 *
				 * @param array  $row    The current row data.
				 * @param object $result The current result object from the database.
				 */
				$row = apply_filters( 'ed11y_csv_export_row', $row, $result );

				$row = array_map( array( __CLASS__, 'sanitize_value' ), $row );
				fputcsv( $file, $row );
			}
		} while ( self::EXPORT_BATCH === $batch_size );

		/**
		 * Action hook to add additional rows after the data.
		 * Hooked functions should write their own fputcsv() calls.
		 *
		 * @param resource $file The file handle for php://output.
		 * @param array    $data The final batch of rows (deprecated; see
		 *                       ed11y_csv_export_before_headers).
		 */
		do_action( 'ed11y_csv_export_after_data', $file, array() );
	}
}
