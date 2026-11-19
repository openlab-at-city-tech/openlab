<?php
/**
 * Stores tests results
 * Reference https://developer.wordpress.org/rest-api/extending-the-rest-api/controller-classes/
 * POST v PUT in https://developer.wordpress.org/reference/classes/wp_rest_server/
 *
 * @package         Editoria11y
 */

namespace Editoria11y\Controller;

use Editoria11y\Installer;
use Editoria11y\Validate;
use WP_Error;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;
use WP_User_Query;

defined( 'ABSPATH' ) || exit;

/**
 * REST controller for dismissals (PUT/GET /ed11y/v1/dismiss).
 */
class ApiDismissals extends WP_REST_Controller {

	/**
	 * Register routes
	 */
	public function init() {
		add_action(
			'rest_api_init',
			array( $this, 'register_routes' ),
		);
	}

	/**
	 * Register the routes for the objects of the controller.
	 */
	public function register_routes() {
		$version   = '1';
		$namespace = 'ed11y/v' . $version;
		$base      = 'dismiss';
		// Set up single-page routes.
		register_rest_route(
			$namespace,
			'/' . $base,
			array(
				array(
					// Sitewide dismissal report for the dashboard.
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_dismissals' ),
					'permission_callback' => array( $this, 'read_dismissals_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema( true ),
				),
				array(
					// Report results for a URL.
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'dismiss' ),
					'permission_callback' => array( $this, 'update_item_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema( true ),
				),
			)
		);
	}



	/**
	 * Update one item from the collection
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function dismiss( $request ) {

		$data = $this->send_dismissal( $request );
		if ( is_numeric( $data ) ) {
			return new WP_REST_Response( 'Success', 200 );
		}

		return new WP_Error( 'cant-update', __( 'Results not recorded', 'editoria11y' ), array( 'status' => 500 ) );
	}

	/**
	 *
	 * Attempts to send item to DB
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity) Sequential guard clauses (shape, pid, status whitelist) ahead of one split reset/insert branch; extracting pieces would obscure the single write path.
	 */
	public function send_dismissal( WP_REST_Request $request ) {
		$params  = $request->get_params();
		$results = $params['data'];
		if ( ! is_array( $results ) ) {
			return null;
		}
		// Defaults for optional keys (PHP 8 warns on undefined keys; the
		// URL-row insert below reads all of these), plus the JS senders'
		// varchar(190) truncation so dismissals and results key long URLs
		// on the same string.
		$results += array(
			'post_id'     => 0,
			'page_url'    => '',
			'entity_type' => '',
			'page_title'  => '',
			'page_count'  => 0,
			'in_content'  => 1,
		);
		if ( mb_strlen( (string) $results['page_url'] ) > 190 ) {
			$results['page_url'] = mb_substr( (string) $results['page_url'], 0, 189 );
		}
		$now = gmdate( 'Y-m-d H:i:s' );
		global $wpdb;

		// Get Page ID so we can avoid complex joins in subsequent queries.
		$pid = $this->get_dismissal_pid( $results );
		if ( empty( $pid ) ) {
			return null;
		}

		$status = isset( $results['dismissal_status'] ) ? (string) $results['dismissal_status'] : '';
		// Whitelist the operation (and the persisted value) so an unexpected
		// payload can't write a bogus dismissal_status into the table.
		$allowed_status = array( 'ok', 'okAll', 'hide', 'reset' );
		if ( ! in_array( $status, $allowed_status, true ) ) {
			return null;
		}

		$result_key  = (string) ( $results['result_key'] ?? '' );
		$result_name = (string) ( $results['result_name'] ?? '' );
		// element_id arrives already hashed: the bundled editoria11y library
		// computes it via createDismissalKey() / dismissDigest() on the client
		// using State.option.pepper. Server stores the value as-is.
		$element_id = (string) ( $results['element_id'] ?? '' );
		// Content (1) vs. developer (0) bucket for a global dismissal, so the
		// dashboard subtracts it from — and a reset restores it to — the right
		// column. Defaults to content; ignored for the `ok`/`hide` rows.
		$in_content = empty( $results['in_content'] ) ? 0 : 1;

		if ( 'reset' === $status ) {
			// `okAll` and `ok` are shared, collaborative state: any user who can
			// dismiss (edit_posts) may restore them, including rows another user
			// created. Only `hide` is a private, per-user dismissal, scoped to
			// the caller below. Do not user-scope the shared DELETEs.
			//
			// A global dismissal is site-wide, so its reset is too: we clear the
			// element EVERYWHERE, not just on the page the reset arrived from
			// (which need not be where it was created). That single DELETE
			// removes the origin row AND every per-page mapping row the report
			// path stored — the rows the dashboard subtracts from each affected
			// page's raw count — so all those counts self-heal on the next
			// render with no per-page arithmetic. The DELETE is split from the
			// page-scoped `ok`/`hide` one so we can tell whether an okAll row was
			// actually removed — that, and only that, invalidates the static
			// config payload (which carries `globalDismissals` for 30 days);
			// page-scoped removals only touch the per-page blob, rebuilt every
			// request.
			// Restore each affected page's stored count before deleting the
			// mapping. Stored counts are okAll-ADJUSTED: they excluded this
			// element while the global dismissal was active. Pages that adjusted
			// down can't re-scan themselves on a reset, so we add the element
			// back — into the content or developer bucket each page recorded —
			// using its per-page okAll rows. Only non-stale rows count: a stale
			// row means the element is no longer on that page, so its stored
			// count already excludes it and must not be bumped.
			//
			// Not wrapped in an explicit transaction: send_dismissal() stays
			// non-transactional so the dismissal test suite keeps WP_UnitTestCase's
			// rollback isolation (see the note in ApiResults::send_results). The
			// statements are ordered read-restore-then-delete; $wpdb never throws,
			// and a re-crawl reconciles the rare mid-sequence failure.

			// Page (ed11y_urls) content/dev totals.
			$wpdb->query( // phpcs:ignore
				$wpdb->prepare(
					"UPDATE {$wpdb->prefix}ed11y_urls u
					INNER JOIN (
						SELECT pid,
							SUM( in_content = 1 ) AS content_count,
							SUM( in_content = 0 ) AS dev_count
						FROM {$wpdb->prefix}ed11y_dismissals
						WHERE result_key = %s AND element_id = %s
							AND dismissal_status = 'okAll' AND stale = 0
						GROUP BY pid
					) m ON m.pid = u.pid
					SET u.page_total = u.page_total + m.content_count,
						u.dev_total  = u.dev_total + m.dev_count;",
					array( $result_key, $element_id )
				)
			);

			// Per-test (ed11y_results) counts for the pages that still exist.
			$wpdb->query( // phpcs:ignore
				$wpdb->prepare(
					"UPDATE {$wpdb->prefix}ed11y_results r
					INNER JOIN (
						SELECT pid,
							SUM( in_content = 1 ) AS content_count,
							SUM( in_content = 0 ) AS dev_count
						FROM {$wpdb->prefix}ed11y_dismissals
						WHERE result_key = %s AND element_id = %s
							AND dismissal_status = 'okAll' AND stale = 0
						GROUP BY pid
					) m ON m.pid = r.pid AND r.result_key = %s
					SET r.result_count = r.result_count + m.content_count,
						r.dev_count    = r.dev_count + m.dev_count;",
					array( $result_key, $element_id, $result_key )
				)
			);

			// Per-test rows that were pruned when every occurrence of this test
			// on the page was globally dismissed (its stored count hit 0). Insert
			// them fresh so the "issues by type" totals restore too. result_name
			// rides the mapping; a mechanical (user 0) row may carry '', which
			// the next scan of the page corrects.
			$wpdb->query( // phpcs:ignore
				$wpdb->prepare(
					"INSERT INTO {$wpdb->prefix}ed11y_results
						(pid, result_key, result_count, dev_count, result_name, created, updated)
					SELECT d.pid, %s,
						SUM( d.in_content = 1 ),
						SUM( d.in_content = 0 ),
						MAX( d.result_name ),
						%s, %s
					FROM {$wpdb->prefix}ed11y_dismissals d
					WHERE d.result_key = %s AND d.element_id = %s
						AND d.dismissal_status = 'okAll' AND d.stale = 0
						AND NOT EXISTS (
							SELECT 1 FROM {$wpdb->prefix}ed11y_results r
							WHERE r.pid = d.pid AND r.result_key = %s
						)
					GROUP BY d.pid;",
					array( $result_key, $now, $now, $result_key, $element_id, $result_key )
				)
			);

			// Now clear the element everywhere: the origin row plus every
			// per-page mapping row the report path stored.
			$okall_deleted = (int) $wpdb->query( // phpcs:ignore
				$wpdb->prepare(
					"DELETE FROM {$wpdb->prefix}ed11y_dismissals
					WHERE result_key = %s
					AND element_id = %s
					AND dismissal_status = 'okAll';",
					array(
						$result_key,
						$element_id,
					)
				)
			);

			$other_deleted = (int) $wpdb->query( // phpcs:ignore
				$wpdb->prepare(
					"DELETE FROM {$wpdb->prefix}ed11y_dismissals
					WHERE pid = %d
					AND result_key = %s
					AND element_id = %s
					AND (
						dismissal_status = 'ok'
						OR (
							dismissal_status = 'hide'
							AND user = %d
						)
					);",
					array(
						$pid,
						$result_key,
						$element_id,
						wp_get_current_user()->ID,
					)
				)
			);

			if ( $okall_deleted > 0 ) {
				ed11y_bump_config_version();
			}

			return $okall_deleted + $other_deleted;
		}

		$response = $wpdb->query( // phpcs:ignore
			$wpdb->prepare(
				"INSERT INTO {$wpdb->prefix}ed11y_dismissals
					(pid,
					result_key,
					user,
					element_id,
					dismissal_status,
					result_name,
					created,
					updated,
					stale,
					in_content)
				VALUES (%s, %s, %d, %s, %s, %s, %s, %s, %d, %d)
					;",
				array(
					$pid,
					$result_key,
					wp_get_current_user()->ID,
					$element_id,
					$status,
					$result_name,
					$now,
					$now,
					0,
					$in_content,
				)
			)
		);

		// Only `okAll` writes invalidate the static config payload. `ok` and
		// `hide` go in the per-page blob which is rebuilt every request.
		if ( $response && 'okAll' === $status ) {
			ed11y_bump_config_version();
		}

		return $response;
	}

	/**
	 * Get dashboard table data.
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_REST_Response
	 */
	public function get_dismissals( WP_REST_Request $request ): WP_REST_Response {
		global $wpdb;
		$validate = new Validate();

		// Sanitize all params before use.
		$params = $request->get_params();
		// Defensive defaults, matching ApiResults::get_results(): PHP 8
		// raises "Undefined array key" for any param the caller omits, and
		// an omitted count would otherwise become `LIMIT 0` — a silently
		// empty report. `+=` preserves keys the caller did set.
		$defaults = array(
			'count'       => 25,
			'offset'      => 0,
			'direction'   => 'DESC',
			'sort'        => '',
			'result_key'  => '',
			'entity_type' => '',
			'dismissor'   => '',
		);
		$params  += $defaults;
		// Enforce scalars: an array-valued param would stringify to the
		// literal "Array" downstream and silently filter on nothing.
		foreach ( array_keys( $defaults ) as $key ) {
			if ( ! is_scalar( $params[ $key ] ) ) {
				$params[ $key ] = $defaults[ $key ];
			}
		}
		$count       = Validate::count( $params['count'] );
		$offset      = Validate::offset( $params['offset'] );
		$direction   = 'ASC' === $params['direction'] ? 'ASC' : 'DESC';
		$order_by    = ! empty( $params['sort'] ) && $validate->sort( $params['sort'] ) ? $params['sort'] : false;
		$entity_type = ! empty( $params['entity_type'] ) && $validate->entity_type( $params['entity_type'] ) ? $params['entity_type'] : false;
		$result_key  = ! empty( $params['result_key'] ) && 'false' !== $params['result_key'] ? esc_sql( $params['result_key'] ) : false;
		$dismissor   = is_numeric( $params['dismissor'] ) ? intval( $params['dismissor'] ) : false;
		$utable      = $wpdb->prefix . 'ed11y_urls';
		$dtable      = $wpdb->prefix . 'ed11y_dismissals';

		// Get top pages.

		// Sort by sanitized param; created is the default. The global
		// whitelist covers all readers; only columns this query actually
		// selects may pass (others would be prefixed onto the dismissals
		// table below and error).
		$dismiss_sorts = array( 'pid', 'page_url', 'page_title', 'entity_type', 'user', 'result_key', 'result_name', 'dismissal_status', 'created', 'stale' );
		$order_by      = $order_by && in_array( $order_by, $dismiss_sorts, true ) ? $order_by : 'created';

		// Build where clause based on sanitized params.
		//
		// Always hide the mechanical per-page okAll mapping rows (user 0) the
		// report path writes so the dashboard can restore counts on a reset —
		// they are not human dismissals and would otherwise flood the list with
		// one entry per page. The origin okAll row (a real dismisser, user > 0)
		// still shows. Every filter below AND-appends to this base.
		$where = "WHERE NOT ( {$dtable}.dismissal_status = 'okAll' AND {$dtable}.user = 0 )";
		if ( $result_key ) {
			// Filtering by test name.
			$where .= " AND {$dtable}.result_key = '{$result_key}'";
		}
		if ( $entity_type ) {
			// Filtering by entity type.
			$where .= " AND {$utable}.entity_type = '{$entity_type}'";
		}

		if ( 0 < $dismissor ) {
			// Filtering by author ID number, which has been cast to integer.
			$where .= " AND {$dtable}.user = '{$dismissor}'";
		}

		if ( in_array( $order_by, array( 'page_title', 'page_url', 'entity_type' ), true ) ) {
			$order_by = "{$utable}.{$order_by}";
		} else {
			$order_by = "{$dtable}.{$order_by}";
		}

		// phpcs:disable
		$data = $wpdb->get_results(
			"SELECT
					{$utable}.pid,
					{$utable}.page_url,
					{$utable}.page_title,
					{$utable}.entity_type,
					{$dtable}.user,
					{$dtable}.result_key,
					{$dtable}.result_name,
					{$dtable}.dismissal_status,
					MAX({$dtable}.created) AS created,
					{$dtable}.stale,
					MAX({$dtable}.stale_date) AS stale_date
					FROM {$dtable}
					LEFT JOIN {$utable} ON ({$dtable}.pid={$utable}.pid)
					{$where}
					GROUP BY
					{$utable}.pid,
					{$utable}.page_url,
					{$utable}.page_title,
					{$utable}.entity_type,
					{$dtable}.user,
					{$dtable}.result_key,
					{$dtable}.result_name,
					{$dtable}.dismissal_status,
					{$dtable}.stale
					ORDER BY {$order_by} {$direction}
					LIMIT {$count}
					OFFSET {$offset}
					;"
		);

		// COUNT(*) over the grouped set as a derived table: the previous
		// version materialized every grouped row in PHP just to read
		// $wpdb->num_rows, which scaled with the whole dismissals table on
		// every request.
		$rowcount = (int) $wpdb->get_var(
			"SELECT COUNT(*) FROM (
					SELECT 1
					FROM {$dtable}
					INNER JOIN {$utable} ON ({$dtable}.pid={$utable}.pid)
					{$where}
					GROUP BY
					{$utable}.pid,
					{$dtable}.user,
					{$dtable}.result_key,
					{$dtable}.dismissal_status,
					{$dtable}.stale
					) AS grouped_dismissals
					;"
		);

		// Get user display names. An empty `include` array would make
		// WP_User_Query drop the filter and return EVERY user, so only
		// query when the page of rows actually references dismissers.
		$users    = array();
		$user_ids = [];
		foreach ( $data as $value ) {
			if ( $value->user && !in_array($value->user, $user_ids ) )
				$user_ids[] = $value->user;
		}
		if ( ! empty( $user_ids ) ) {
			$user_query = new WP_User_Query(
				array(
					'include' => $user_ids,
					'fields'  => array(
						'ID',
						'display_name',
					),
				)
			);
			$users = $user_query->get_results();
		}

		// phpcs:enable
		return new WP_REST_Response( array( $data, $rowcount, $users ), 200 );
	}

	/**
	 * Returns the pid from the URL table.
	 *
	 * @param array $results from request.
	 * @param bool  $recursion if first pass.
	 */
	public function get_dismissal_pid( array $results, bool $recursion = false ): ?string {
		$post_id = $results['post_id'];
		$url     = $results['page_url'];
		if ( empty( $post_id ) && empty( $url ) ) {
			return false;
		}
		global $wpdb;
		// Initialize $pid up front; on PHP 8 the post_id===0 path otherwise
		// hits "Undefined variable" before the first empty($pid) check.
		$pid = null;
		if ( $post_id > 0 ) {
			$pid = $wpdb->get_var( // phpcs:ignore
				$wpdb->prepare(
					"SELECT pid FROM {$wpdb->prefix}ed11y_urls
				WHERE post_id=%s;",
					array(
						$post_id,
					)
				)
			);
		}
		// Not found by post ID, or post ID not provided. The page_url lookup
		// runs regardless of the recursion flag so that the post-INSERT
		// recursive call can still resolve a freshly-created row when
		// post_id is 0 (archives, non-singular routes). The `! $recursion`
		// guard stays on the INSERT branch below to prevent an infinite
		// loop if the INSERT itself fails.
		if ( empty( $pid ) ) {
			global $wpdb;
			$pid = $wpdb->get_var( // phpcs:ignore
				$wpdb->prepare(
					"SELECT pid FROM {$wpdb->prefix}ed11y_urls
				WHERE page_url=%s;",
					array(
						$url,
					)
				)
			);
		}
		if ( empty( $pid ) && ! $recursion ) {
			// Insert results. ON DUPLICATE KEY (page_url is UNIQUE as of
			// schema 2.1): a concurrent scan/dismissal of the same URL that
			// won the race folds into the existing row instead of erroring.
			// Placeholders repeated rather than VALUES()/alias syntax — the
			// only form both MySQL 8.4+ and MariaDB accept.
			$wpdb->query( // phpcs:ignore
				$wpdb->prepare(
					"INSERT INTO {$wpdb->prefix}ed11y_urls
					(page_url,
					 post_id,
					entity_type,
					page_title,
					page_total)
				VALUES (%s, %d, %s, %s, %d)
				ON DUPLICATE KEY UPDATE
					page_title = %s,
					page_total = %d;",
					array(
						$results['page_url'],
						$results['post_id'],
						$results['entity_type'],
						$results['page_title'],
						$results['page_count'],
						$results['page_title'],
						$results['page_count'],
					)
				)
			);
			// Get new pid.
			$pid = $this->get_dismissal_pid( $results, true );
		}
		return $pid;
	}

	/**
	 * Check if a given request has access to update a specific item
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|bool
	 */
	public function update_item_permissions_check( $request ) { // phpcs:ignore
		if ( 'broken' === Installer::schema_state() ) {
			return new WP_Error(
				'ed11y_schema_unavailable',
				__( 'Editoria11y database update did not complete; writes are paused until an administrator retries the update.', 'editoria11y' ),
				array( 'status' => 503 )
			);
		}
		return current_user_can( 'edit_posts' );
	}

	/**
	 * Check if a given request has access to read the sitewide dismissal report.
	 *
	 * The PUT writer keeps `edit_posts` so any logged-in editor — including
	 * authors on their own drafts — can dismiss and reset alerts. The GET
	 * reader is stricter: it surfaces every tracked page's dismissals plus
	 * the dismissing users' display names, the same class of data as
	 * GET /dashboard, so it shares the report-reader gate
	 * (manage_options when `ed11y_report_restrict` is on, else
	 * edit_others_posts).
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|bool
	 */
	public function read_dismissals_permissions_check( $request ) { // phpcs:ignore
		if ( 'broken' === Installer::schema_state() ) {
			return new WP_Error(
				'ed11y_schema_unavailable',
				__( 'Editoria11y database update did not complete; the dashboard is paused until an administrator retries the update.', 'editoria11y' ),
				array( 'status' => 503 )
			);
		}
		return current_user_can( ed11y_report_reader_capability() );
	}
}
