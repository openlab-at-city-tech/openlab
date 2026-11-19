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
 * REST controller for scan results and the dashboard read endpoints (PUT /result, GET /dashboard).
 */
class ApiResults extends WP_REST_Controller {

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

		// Report results from scan.
		register_rest_route(
			$namespace,
			'/result',
			array(
				'methods'             => 'PUT',
				'callback'            => array( $this, 'update_item' ),
				'permission_callback' => array( $this, 'update_item_permissions_check' ),
				'args'                => $this->get_endpoint_args_for_item_schema( true ),
			)
		);

		// Return sitewide data.
		register_rest_route(
			$namespace,
			'/dashboard',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_results' ),
					'permission_callback' => array( $this, 'read_dashboard_permissions_check' ),
					'args'                => array(
						'context' => array(
							'default' => 'view',
						),
					),
				),

			)
		);
	}

	/**
	 * Option flag marking the legacy post_id backfill as complete for THIS
	 * blog. An option, not a transient: this is a one-shot data migration
	 * marker, and the original site-transient version was double-broken on
	 * multisite — network-scoped flag guarding per-blog table writes (only
	 * the first blog to load a dashboard ever backfilled), and set before
	 * the unbounded loop (a timeout marked the job done with partial data).
	 */
	const GOT_POST_IDS_OPTION = 'ed11y_got_post_ids';

	/**
	 * Rows examined per dashboard load. url_to_postid() parses rewrite
	 * rules per call, so the batch keeps a legacy-heavy dashboard load
	 * from timing out; the next load continues where this one stopped
	 * (resolved rows drop out of the WHERE).
	 */
	const ADD_POST_ID_BATCH = 500;

	/**
	 * Associate old records with post ID. Todo: remove.
	 */
	private function add_post_id() {
		global $wpdb;
		$utable = $wpdb->prefix . 'ed11y_urls';

		// phpcs:disable
		$missing_id = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT
						{$utable}.page_url
						FROM {$utable}
						WHERE (
						    $utable.post_id = 0
						    AND
						    (
						        $utable.entity_type = 'Page'
						        OR
						        $utable.entity_type = 'Post'
						    )
						)
						LIMIT %d
						;",
				self::ADD_POST_ID_BATCH
			)
		);
		// phpcs:enable
		$updated = 0;
		foreach ( $missing_id as $value ) {
			$post_id = url_to_postid( $value->page_url );
			if ( ! empty( $post_id ) ) {
				$wpdb->update( // phpcs:ignore
					$utable,
					array(
						'post_id' => $post_id,
					),
					array(
						'page_url' => $value->page_url,
					),
					array(
						'%d',
						'%s',
					)
				);
				++$updated;
			}
		}

		// Done when the table is drained — or when a full batch resolved
		// nothing (url_to_postid() is deterministic, so an all-unresolvable
		// batch would just re-run forever; stop and accept those rows as
		// permanent post_id=0 routes).
		if ( count( $missing_id ) < self::ADD_POST_ID_BATCH || 0 === $updated ) {
			update_option( self::GOT_POST_IDS_OPTION, 1, false );
		}
	}

	/**
	 * Get dashboard table data.
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_REST_Response
	 */
	public function get_results( WP_REST_Request $request ): WP_REST_Response {
		global $wpdb;
		$validate = new Validate();
		$users    = array();

		$utable     = $wpdb->prefix . 'ed11y_urls';
		$rtable     = $wpdb->prefix . 'ed11y_results';
		$post_table = $wpdb->prefix . 'posts';

		if ( empty( get_option( self::GOT_POST_IDS_OPTION ) ) ) {
			$this->add_post_id();
		}

		// Sanitize all params before use.
		$params = $request->get_params();
		// Defensive defaults: PHP 8 raises "Undefined array key" warnings for
		// any param the caller omits. The dashboard JS sends every key on
		// every request, but third-party REST consumers (and PHPUnit tests
		// targeting the permission callback) reasonably omit them. `+=`
		// preserves keys the caller did set and only fills in the rest.
		$defaults = array(
			'view'        => 'pages',
			'count'       => 25,
			'offset'      => 0,
			'direction'   => 'DESC',
			'sort'        => '',
			'result_key'  => '',
			'entity_type' => '',
			'post_status' => '',
			'p_author'    => '',
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
		$author      = is_numeric( $params['p_author'] ) ? intval( $params['p_author'] ) : false;
		$post_status = ! empty( $params['post_status'] ) && 'false' !== $params['post_status'] ? esc_sql( $params['post_status'] ) : false;

		$post_status_filter = function ( $where, $post_status, $utable, $post_table ) {
			if ( ! empty( $post_status ) ) {
				// Filtering by published status.
				$where = empty( $where ) ? 'WHERE ' : $where . 'AND ';
				$where = 'publish' === $post_status ?
					$where . "( {$utable}.post_id = '0' OR ( {$utable}.post_id > '0' AND {$post_table}.post_status = '{$post_status}' ) )"
					: $where . "{$utable}.post_id > '0' AND {$post_table}.post_status = '{$post_status}'";
			}
			return $where;
		};

		if ( 'pages' === $params['view'] ) {
			/**
			 * Dashboard panel: list of pages with issues.
			 */

			// Sort by sanitized param; page total is default. The global
			// whitelist covers all views; keys not selected by THIS view's
			// query (e.g. dismissal_status) would be an ORDER BY error, so
			// fall back to the default instead.
			$pages_sorts = array( 'pid', 'page_url', 'page_title', 'entity_type', 'page_total', 'dev_total', 'post_status', 'post_modified', 'post_author' );
			$order_by    = $order_by && in_array( $order_by, $pages_sorts, true ) ? $order_by : 'page_total';

			// Build where clause based on sanitized params.
			// Alert + dev columns alias to per-key counts when a result_key
			// filter is active so the same `page_total` / `dev_total` sort
			// keys keep working in both filtered and unfiltered views.
			if ( $result_key ) {
				// Filtering by test name.
				$total_column = "{$rtable}.result_count";
				$dev_column   = "{$rtable}.dev_count";
				$where        = "WHERE {$rtable}.result_key = '{$result_key}' AND {$total_column} > '0'";
			} else {
				$total_column = "{$utable}.page_total";
				$dev_column   = "{$utable}.dev_total";
				$where        = "WHERE {$total_column} > '0'";
			}
			if ( $entity_type ) {
				// Filtering by entity type.
				$where = empty( $where ) ? 'WHERE ' : $where . 'AND ';
				$where = $where . "{$utable}.entity_type = '{$entity_type}'";
			}
			$where = $post_status_filter( $where, $post_status, $utable, $post_table );

			if ( 0 < $author ) {
				// Filtering by author ID number, which has been cast to integer.
				$where = empty( $where ) ? 'WHERE ' : $where . 'AND ';
				$where = $where . "{$post_table}.post_author = '{$author}'";
			}

			/*
			Complex counts and joins required a direct DB call.
			Variables are all validated or sanitized.
			*/
			// phpcs:disable
			$data = $wpdb->get_results(
				"SELECT DISTINCT
						{$utable}.pid,
						{$utable}.page_url,
						{$utable}.page_title,
						{$utable}.entity_type,
						$total_column AS page_total,
						$dev_column AS dev_total,
						{$post_table}.post_status AS post_status,
						{$post_table}.post_modified AS post_modified,
						{$post_table}.post_author AS post_author
						FROM {$utable}
						LEFT JOIN {$rtable} ON {$utable}.pid={$rtable}.pid
						LEFT JOIN {$post_table} ON {$utable}.post_id={$post_table}.ID
						{$where}
						ORDER BY {$order_by} {$direction}
						LIMIT {$count}
						OFFSET {$offset}
						;"
			);

			if ( empty($where) ) {
				$rowcount = $wpdb->get_var(
					"SELECT
    			COUNT({$utable}.pid)
				FROM {$utable}"
				);
			} else {
				$rowcount = $wpdb->get_var(
					"SELECT COUNT(DISTINCT {$utable}.pid) AS row_count
							  FROM {$utable}
							  LEFT JOIN {$rtable}      ON {$utable}.pid = {$rtable}.pid
							  LEFT JOIN {$post_table}  ON {$utable}.post_id = {$post_table}.ID
							  {$where};"
				);
			}

			// Get user display names. An empty `include` array would make
			// WP_User_Query drop the filter and return EVERY user, so only
			// query when the page of rows actually references authors
			// (post_id=0 routes — archives, search — have none).
			$user_ids = [];
			foreach ( $data as $value ) {
				if ( $value->post_author && !in_array($value->post_author, $user_ids ) )
					$user_ids[] = $value->post_author;
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

		} elseif ( 'keys' === $params['view'] ) {
			/**
			 * Dashboard panel: list of issues.
			 */
			if ( 'dev_count' === $order_by ) {
				// Sort by aggregated dev count, mirroring the count branch.
				$order_by = 'SUM(' . $wpdb->prefix . 'ed11y_results.dev_count)';
			} elseif ( 'result_key' !== $order_by ) {
				// Everything else — including whitelisted-but-inapplicable
				// keys from other views — aggregates on total count.
				$order_by = 'SUM(' . $wpdb->prefix . 'ed11y_results.result_count)';
			}

			/*
			Complex counts and joins required a direct DB call.
			Variables are all validated or sanitized.
			*/
			// phpcs:disable
			$rowcount = $wpdb->get_var(
				"SELECT COUNT( DISTINCT result_key ) AS row_count
				FROM {$rtable};"
			);

			$data = $wpdb->get_results(
				"SELECT
					SUM({$rtable}.result_count) AS count,
					SUM({$rtable}.dev_count) AS dev_count,
					{$rtable}.result_key,
					MAX({$rtable}.result_name) AS result_name
					FROM {$rtable}
					INNER JOIN {$utable} ON {$rtable}.pid={$utable}.pid
					LEFT JOIN {$post_table} ON {$utable}.post_id={$post_table}.ID
					GROUP BY {$rtable}.result_key
					ORDER BY {$order_by} {$direction}
					LIMIT {$count}
					OFFSET {$offset}
					;"
			);
			// phpcs:enable

		} elseif ( 'recent' === $params['view'] ) {
			/**
			* Dashboard panel: recent issues.
			*/

			// Sort by sanitized param; page total is default; keys this
			// view's SELECT doesn't expose fall back (ORDER BY error otherwise).
			$recent_sorts = array( 'pid', 'page_url', 'page_title', 'entity_type', 'page_total', 'dev_total', 'result_key', 'result_count', 'dev_count', 'created', 'post_status' );
			$order_by     = $order_by && in_array( $order_by, $recent_sorts, true ) ? $order_by : 'page_total';

			// Build where clause based on sanitized params.
			$where = '';
			if ( $result_key ) {
				// Filtering by test name.
				$where = "WHERE {$rtable}.result_key = '{$result_key}'";
			}
			if ( $entity_type ) {
				// Filtering by entity type.
				$where = empty( $where ) ? 'WHERE ' : $where . 'AND ';
				$where = $where . "{$utable}.entity_type = '{$entity_type}'";
			}

			$where = $post_status_filter( $where, $post_status, $utable, $post_table );

			if ( ! empty( $where ) ) {
				/*
				Complex counts and joins required a direct DB call.
				Variables are all validated or sanitized.
				Subquery needed because I couldn't get DISTINCT working.
				*/
				// phpcs:disable
				$data = $wpdb->get_results(
					"SELECT
							{$rtable}.result_key,
					    	{$rtable}.result_count,
							{$rtable}.dev_count,
							{$rtable}.result_name,
							{$utable}.pid,
							{$utable}.page_url,
							{$utable}.page_title,
							{$utable}.entity_type,
							{$utable}.page_total,
							{$utable}.dev_total,
							{$post_table}.post_status,
							{$rtable}.created as created
							FROM {$utable}
							INNER JOIN {$rtable} ON {$utable}.pid={$rtable}.pid
							LEFT JOIN {$post_table} ON {$utable}.post_id={$post_table}.ID
							{$where}
							ORDER BY {$order_by} {$direction}
							LIMIT {$count}
							OFFSET {$offset}
							;"
				);

				$rowcount = $wpdb->get_var(
					"SELECT COUNT({$utable}.pid)
					FROM {$rtable}
					INNER JOIN {$utable} ON {$rtable}.pid={$utable}.pid
					LEFT JOIN {$post_table} ON {$utable}.post_id={$post_table}.ID
					{$where};"
				);
				// phpcs:enable

			} else {
				/*
				Complex counts and joins required a direct DB call.
				Variables are all validated or sanitized.
				*/
				// phpcs:disable
				$data = $wpdb->get_results(
					"SELECT
					    {$rtable}.result_key,
					    {$rtable}.result_count,
					    {$rtable}.dev_count,
					    {$rtable}.result_name,
						{$utable}.pid,
						{$utable}.page_url,
						{$utable}.page_title,
						{$utable}.entity_type,
						{$utable}.page_total,
						{$utable}.dev_total,
						{$post_table}.post_status,
						{$rtable}.created as created
					FROM {$rtable}
					INNER JOIN {$utable} ON {$rtable}.pid={$utable}.pid
					LEFT JOIN {$post_table} ON {$utable}.post_id={$post_table}.ID
					ORDER BY {$order_by} {$direction}
					LIMIT {$count}
					OFFSET {$offset}
					;"
				);

				$rowcount = $wpdb->get_var(
					"SELECT COUNT(pid)
					FROM {$utable};"
				);
				// phpcs:enable
			}
		} else {
			// Unknown view: previously fell through with $data/$rowcount
			// undefined (PHP 8 warnings + a [null, null, []] payload).
			return new WP_REST_Response( array( 'error' => 'Unknown view.' ), 400 );
		}

		return new WP_REST_Response( array( $data, $rowcount, $users ), 200 );
	}


	/**
	 * Update one item from the collection
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_REST_Response
	 */
	public function update_item( $request ): WP_REST_Response {

		// Structural validation up front: no route schema is registered, so
		// get_endpoint_args_for_item_schema() enforces nothing and a hostile
		// or truncated body used to reach send_results() unchecked (a missing
		// `dismissals` key was a PHP 8 TypeError fatal). Optional keys get
		// defaults in send_results(); only structurally unusable bodies 400.
		$data = $request->get_param( 'data' );
		if (
			! is_array( $data )
			|| '' === trim( (string) ( $data['page_url'] ?? '' ) )
			|| ( isset( $data['results'] ) && ! is_array( $data['results'] ) )
			|| ( isset( $data['dismissals'] ) && ! is_array( $data['dismissals'] ) )
		) {
			return new WP_REST_Response( array( 'error' => 'Malformed scan payload.' ), 400 );
		}

		$data = $this->send_results( $request );
		if ( ! ( in_array( false, $data, true ) ) ) {
			return new WP_REST_Response( $data, 200 );
		}
		return new WP_REST_Response( $data, 500 );
	}

	/**
	 * Returns the pid from the URL table.
	 *
	 * @param string $url of post.
	 * @param string $post_id WP post ID.
	 */
	public function get_pid( string $url, string $post_id ): ?string {
		global $wpdb;
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
		// Not found by post ID, or post ID not provided.
		if ( empty( $pid ) ) {
			global $wpdb;
			return $wpdb->get_var( // phpcs:ignore
				$wpdb->prepare(
					"SELECT pid FROM {$wpdb->prefix}ed11y_urls
				WHERE page_url=%s;",
					array(
						$url,
					)
				)
			);
		}
		return $pid;
	}

	/**
	 * Returns true when the given URL row still owns an `okAll` dismissal.
	 *
	 * Deleting such a row cascades the dismissal away (FK ON DELETE
	 * CASCADE), which invalidates the long-lived static config payload —
	 * callers must bump the config version after the delete commits.
	 *
	 * @param int $pid URL row id.
	 */
	private function pid_has_okall_dismissal( int $pid ): bool {
		global $wpdb;
		return (bool) $wpdb->get_var( // phpcs:ignore
			$wpdb->prepare(
				"SELECT 1 FROM {$wpdb->prefix}ed11y_dismissals
				WHERE pid = %d AND dismissal_status = 'okAll' LIMIT 1;",
				array( $pid )
			)
		);
	}

	/**
	 *
	 * Attempts to send item to DB
	 *
	 * Runs inside a real transaction: the sequence is url upsert + N result
	 * upserts + stale cleanup + possible url delete, and $wpdb never throws,
	 * so without one a mid-sequence failure left half-written state behind
	 * a 500. Note for the test harness: START TRANSACTION implicitly
	 * commits WP_UnitTestCase's wrapping transaction — the suites driving
	 * this method already run real DDL (same implicit commit) and reset
	 * their tables manually.
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function send_results( WP_REST_Request $request ): array {

		$params  = $request->get_params();
		$results = $params['data'];
		// update_item() has rejected structurally unusable bodies; fill in
		// defaults for optional keys so older client payloads (and PHP 8's
		// undefined-key warnings / count() TypeError) can't take this down.
		$results += array(
			'pid'          => -1,
			'post_id'      => 0,
			'entity_type'  => '',
			'page_title'   => '',
			'page_count'   => 0,
			'dev_count'    => 0,
			'results'      => array(),
			'dismissals'   => array(),
			'okAllApplied' => array(),
		);
		// Mirror the JS senders' varchar(190) truncation so every writer —
		// results and dismissals alike — keys a long URL on the same string.
		if ( mb_strlen( (string) $results['page_url'] ) > 190 ) {
			$results['page_url'] = mb_substr( (string) $results['page_url'], 0, 189 );
		}
		// Normalize the optional array payloads before they are counted/iterated.
		if ( ! is_array( $results['dismissals'] ) ) {
			$results['dismissals'] = array();
		}
		if ( ! is_array( $results['okAllApplied'] ) ) {
			$results['okAllApplied'] = array();
		}
		$now = gmdate( 'Y-m-d H:i:s' );
		// A page is "empty" (its URL row is deleted at the end) only when it has
		// no content issues, no developer issues, no page-scoped dismissals to
		// refresh, and no global-dismissal mapping to store. The old check read
		// page_count + dismissals only; once counts split into content/dev
		// buckets that would drop a developer-only (or okAll-only) page.
		$rows   = ( (int) $results['page_count'] > 0
			|| (int) $results['dev_count'] > 0
			|| count( $results['dismissals'] ) > 0
			|| count( $results['okAllApplied'] ) > 0 ) ? 1 : 0; // If 0 at end, delete URL.
		$return = array();
		global $wpdb;

		// Bump the config cachebust only after COMMIT: the static payload
		// carries globalDismissals for 30 days, so a cascade-deleted okAll
		// must invalidate it — but bumping inside the transaction would
		// desync the options cache if we roll back.
		$bump_after_commit = false;

		$wpdb->query( 'START TRANSACTION' ); // phpcs:ignore

		// Handle clicks from dashboard to changed URLS first to prevent URL
		// collisions: the dashboard passes the stale row's pid via ?ed1ref so
		// a renamed permalink replaces its old row instead of colliding.
		// The DELETE is scoped to the post the caller is reporting on
		// (pid AND post_id must both match): the pid alone is client
		// input, and honoring it unscoped would let any edit_posts user
		// cascade-delete arbitrary pages' results and dismissals. post_id=0
		// routes (archives, search) have no stable identity to verify, so
		// they never take this branch — get_pid() resolves them by URL.
		$reported_post_id = (int) $results['post_id'];
		if ( $results['pid'] > -1 && $reported_post_id > 0 ) {
			$stale_had_okall = $this->pid_has_okall_dismissal( (int) $results['pid'] );
			$deleted         = $wpdb->query( // phpcs:ignore
				$wpdb->prepare(
					"DELETE FROM {$wpdb->prefix}ed11y_urls
					WHERE
						(pid = %d AND post_id = %d AND page_url != %s)
					;",
					array(
						$results['pid'],
						$reported_post_id,
						$results['page_url'],
					)
				)
			);
			if ( $deleted && $stale_had_okall ) {
				$bump_after_commit = true;
			}
		}

		$pid = $this->get_pid( $results['page_url'], $results['post_id'] ); // may be 0.

		// Check if any results exist.
		if ( 0 < $rows ) {

			$dev_total = isset( $results['dev_count'] ) ? (int) $results['dev_count'] : 0;
			if ( empty( $pid ) ) {
				// Insert results. ON DUPLICATE KEY (page_url is UNIQUE as of
				// schema 2.1): if a concurrent first-scan of the same URL won
				// the race between our get_pid() read and this insert, fold
				// into its row instead of erroring. Placeholders are repeated
				// rather than using VALUES()/alias syntax — the only form
				// both MySQL 8.4+ and MariaDB accept.
				$return[] = $wpdb->query( // phpcs:ignore
					$wpdb->prepare(
						"INSERT INTO {$wpdb->prefix}ed11y_urls
						(page_url,
						 post_id,
						entity_type,
						page_title,
						page_total,
						dev_total)
					VALUES (%s, %d, %s, %s, %d, %d)
					ON DUPLICATE KEY UPDATE
						post_id = %d,
						entity_type = %s,
						page_title = %s,
						page_total = %d,
						dev_total = %d;",
						array(
							$results['page_url'],
							$results['post_id'],
							$results['entity_type'],
							$results['page_title'],
							$results['page_count'],
							$dev_total,
							$results['post_id'],
							$results['entity_type'],
							$results['page_title'],
							$results['page_count'],
							$dev_total,
						)
					)
				);
				// Get new pid.
				$pid = $this->get_pid( $results['page_url'], $results['post_id'] );
			} else {
				// Update result for existing PID.
				$return[] = $wpdb->update( // phpcs:ignore
					$wpdb->prefix . 'ed11y_urls',
					array(
						'page_url'    => $results['page_url'],
						'post_id'     => $results['post_id'],
						'entity_type' => $results['entity_type'],
						'page_title'  => $results['page_title'],
						'page_total'  => $results['page_count'],
						'dev_total'   => $dev_total,
					),
					array(
						'pid' => $pid,
					),
					array(
						'%s',
						'%d',
						'%s',
						'%s',
						'%d',
						'%d',
					),
					'%d'
				);
			}

			foreach ( $results['results'] as $key => $value ) {
				// Accept the legacy "value is an int" shape and the v3
				// "value is { content_count, dev_count, result_name }" shape.
				if ( is_array( $value ) ) {
					$content_count = (int) ( $value['content_count'] ?? 0 );
					$dev_count     = (int) ( $value['dev_count'] ?? 0 );
					$result_name   = (string) ( $value['result_name'] ?? '' );
				} else {
					$content_count = (int) $value;
					$dev_count     = 0;
					$result_name   = '';
				}

				$response = $wpdb->query( // phpcs:ignore
					$wpdb->prepare(
						"INSERT INTO {$wpdb->prefix}ed11y_results
                            (pid,
                            result_key,
                            result_count,
                            dev_count,
                            result_name,
                            created,
                            updated)
                        VALUES (%d, %s, %d, %d, %s, %s, %s)
                        ON DUPLICATE KEY UPDATE
                            result_count = %d,
                            dev_count = %d,
                            result_name = %s,
                            updated = %s
                            ;",
						array(
							$pid,
							$key,
							$content_count,
							$dev_count,
							$result_name,
							$now,
							$now,
							$content_count,
							$dev_count,
							$result_name,
							$now,
						)
					)
				);
				$rows    += $response ? $response : 0;
				$return[] = $response;
			}

			foreach ( $results['dismissals'] as $value ) {
				// $value is [ result_key, element_id ] from the JS payload.
				// element_id is already a hashed digest produced by the library
				// via createDismissalKey() — store/match as-is.
				$response = $wpdb->query( // phpcs:ignore
					$wpdb->prepare(
						"UPDATE {$wpdb->prefix}ed11y_dismissals
                        SET updated = %s, stale = 0, stale_date = NULL
                        WHERE pid = %d AND result_key = %s AND element_id = %s;",
						array(
							$now,
							$pid,
							$value[0],
							$value[1],
						)
					)
				);
				$rows    += $response ? $response : 0;
				$return[] = $response;
			}

			// Global-dismissal mapping. Every okAll element the page currently
			// renders gets one row per (pid, element): the dashboard subtracts
			// these from the page's raw count (in the row's content/dev bucket),
			// and a reset finds every affected page through them. Rows are
			// refreshed here so the stale sweep below leaves them alone; a
			// missing one is created as a mechanical row (user 0) so the human
			// dismissals list — which hides user-0 okAll rows — is not flooded
			// with one entry per page. The origin dismisser's row (user > 0,
			// written by the dismiss endpoint) is refreshed in place, never
			// overwritten. See ed11y_get_global_dismissals() / ApiDismissals.
			foreach ( $results['okAllApplied'] as $applied ) {
				if ( ! is_array( $applied ) ) {
					continue;
				}
				$applied_key     = (string) ( $applied['result_key'] ?? '' );
				$applied_element = (string) ( $applied['element_id'] ?? '' );
				if ( '' === $applied_key || '' === $applied_element ) {
					continue;
				}
				$applied_name   = (string) ( $applied['result_name'] ?? '' );
				$applied_bucket = empty( $applied['in_content'] ) ? 0 : 1;
				$existing_id    = $wpdb->get_var( // phpcs:ignore
					$wpdb->prepare(
						"SELECT id FROM {$wpdb->prefix}ed11y_dismissals
						WHERE pid = %d AND result_key = %s AND element_id = %s AND dismissal_status = 'okAll'
						LIMIT 1;",
						array( $pid, $applied_key, $applied_element )
					)
				);
				if ( $existing_id ) {
					$response = $wpdb->query( // phpcs:ignore
						$wpdb->prepare(
							"UPDATE {$wpdb->prefix}ed11y_dismissals
							SET updated = %s, stale = 0, stale_date = NULL, in_content = %d
							WHERE id = %d;",
							array( $now, $applied_bucket, $existing_id )
						)
					);
				} else {
					$response = $wpdb->query( // phpcs:ignore
						$wpdb->prepare(
							"INSERT INTO {$wpdb->prefix}ed11y_dismissals
								(pid, result_key, user, element_id, dismissal_status, result_name, created, updated, stale, in_content)
							VALUES (%d, %s, %d, %s, 'okAll', %s, %s, %s, 0, %d);",
							array( $pid, $applied_key, 0, $applied_element, $applied_name, $now, $now, $applied_bucket )
						)
					);
				}
				$rows    += $response ? $response : 0;
				$return[] = $response;
			}
		}

		if ( 0 < $pid ) {
			// Remove any old results.
			$response = $wpdb->query( // phpcs:ignore
				$wpdb->prepare(
					"DELETE FROM {$wpdb->prefix}ed11y_results
					WHERE pid = %d AND updated != %s ;",
					array(
						$pid,
						$now,
					)
				)
			);
			// Do not increment row count on deletions.
			$return[] = $response;

			// Mark any out-of-date dismissals as stale and stamp stale_date the
			// first time we notice. IFNULL keeps the original detection time
			// if a row was already marked stale on a previous scan.
			$response = $wpdb->query( // phpcs:ignore
				$wpdb->prepare(
					"UPDATE {$wpdb->prefix}ed11y_dismissals
					SET stale = 1, stale_date = IFNULL(stale_date, %s)
					WHERE pid = %d AND updated != %s ;",
					array(
						$now,
						$pid,
						$now,
					)
				)
			);
			$rows    += $response ? $response : 0;
			$return[] = $response;

			if ( 0 === $rows ) {
				// No records for this route.
				$empty_had_okall = $this->pid_has_okall_dismissal( (int) $pid );
				$response        = $wpdb->query( // phpcs:ignore
					$wpdb->prepare(
						"DELETE FROM {$wpdb->prefix}ed11y_urls WHERE pid = %d;",
						array(
							$pid,
						)
					)
				);
				if ( $response && $empty_had_okall ) {
					$bump_after_commit = true;
				}
			}
		}

		if ( in_array( false, $return, true ) ) {
			$wpdb->query( 'ROLLBACK' ); // phpcs:ignore
			return $return;
		}
		$wpdb->query( 'COMMIT' ); // phpcs:ignore
		if ( $bump_after_commit ) {
			ed11y_bump_config_version();
		}
		return $return;
	}

	/**
	 * Check if a given request has access to update a specific item
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return bool|WP_Error
	 */
	public function update_item_permissions_check( $request ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInExtendedClass
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
	 * Check if a given request has access to read sitewide dashboard data.
	 *
	 * The PUT /result writer keeps `edit_posts` so any logged-in editor —
	 * including authors editing their own drafts — can post scan results.
	 * The GET /dashboard reader is stricter: it surfaces every tracked
	 * page across the site plus author display names, which an Author role
	 * has no business seeing. The cap mirrors Admin\Dashboard::register_menu()
	 * so the REST gate matches the menu visibility (manage_options when
	 * `ed11y_report_restrict` is on, else edit_others_posts).
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return bool|WP_Error
	 */
	public function read_dashboard_permissions_check( $request ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInExtendedClass
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
