<?php // phpcs:ignore
/**
 * Stores tests results
 * Reference https://developer.wordpress.org/rest-api/extending-the-rest-api/controller-classes/
 * POST v PUT in https://developer.wordpress.org/reference/classes/wp_rest_server/
 *
 * @package         Editoria11y
 */
class Editoria11y_Api_Results extends WP_REST_Controller {

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
					'permission_callback' => array( $this, 'update_item_permissions_check' ),
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
	 * Get dashboard table data.
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_results( $request ) {
		global $wpdb;
		require_once ED11Y_SRC . 'class-editoria11y-validate.php';
		$validate = new Editoria11y_Validate();

		// Sanitize all params before use.
		$params      = $request->get_params();
		$count       = intval( $params['count'] );
		$offset      = intval( $params['offset'] );
		$direction   = 'ASC' === $params['direction'] ? 'ASC' : 'DESC';
		$order_by    = ! empty( $params['sort'] ) && $validate->sort( $params['sort'] ) ? $params['sort'] : false;
		$entity_type = ! empty( $params['entity_type'] ) && $validate->entity_type( $params['entity_type'] ) ? $params['entity_type'] : false;
		$result_key  = ! empty( $params['result_key'] ) && true === $validate->test_name( $params['result_key'] ) ? $params['result_key'] : false;
		$utable      = $wpdb->prefix . 'ed11y_urls';
		$rtable      = $wpdb->prefix . 'ed11y_results';

		if ( 'pages' === $params['view'] ) {
			// Get top pages.

			// Sort by sanitized param; page total is default.
			$order_by = $order_by ? $order_by : 'page_total';

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

			if ( ! empty( $where ) ) {
				$order_by = "{$utable}.{$order_by}";

				/*
				Complex counts and joins required a direct DB call.
				Variables are all validated or sanitized.
				*/
				// phpcs:disable
				$data = $wpdb->get_results(
					"SELECT
							{$utable}.pid,
							{$utable}.page_url,
							{$utable}.page_title,
							{$utable}.entity_type,
							{$utable}.page_total,
							MAX({$rtable}.created) as created
							FROM {$rtable}
							INNER JOIN {$utable} ON {$rtable}.pid={$utable}.pid
							{$where}
							GROUP BY {$utable}.pid,
							{$utable}.page_url,
							{$utable}.page_title,
							{$utable}.entity_type,
							{$utable}.page_total,
							{$rtable}.created
							ORDER BY {$order_by} {$direction}
							LIMIT {$count}
							OFFSET {$offset}
							;"
				);
				
				$rowcount = $wpdb->get_var(
					"SELECT COUNT({$utable}.pid) 
					FROM {$rtable}
					INNER JOIN {$utable} ON {$rtable}.pid={$utable}.pid
					{$where};"
				);
				// phpcs:enable

			} else {
				$where = '';

				/*
				Complex counts and joins required a direct DB call.
				Variables are all validated or sanitized.
				*/
				// phpcs:disable
				$data = $wpdb->get_results(
					"SELECT
						{$utable}.pid,
						{$utable}.page_url,
						{$utable}.page_title,
						{$utable}.entity_type,
						{$utable}.page_total,
						MAX({$rtable}.created) AS created
					FROM {$utable}
					INNER JOIN {$rtable} ON {$utable}.pid={$rtable}.pid
					GROUP BY {$utable}.pid,
							{$utable}.page_url,
							{$utable}.page_title,
							{$utable}.entity_type,
							{$utable}.page_total
					ORDER BY {$order_by} {$direction}
					LIMIT {$count}
					OFFSET {$offset}
					;"
				);

				$rowcount = $wpdb->get_var(
					"SELECT COUNT(DISTINCT pid) 
					FROM {$rtable};"
				);
				// phpcs:enable
			}
		} elseif ( 'keys' === $params['view'] ) {

			if ( false === $order_by || 'count' === $order_by ) {
				$order_by = 'SUM(' . $wpdb->prefix . 'ed11y_results.result_count)';
			}

			/*
			Complex counts and joins required a direct DB call.
			Variables are all validated or sanitized.
			*/
			// phpcs:disable
			$rowcount = $wpdb->get_var(
				"SELECT COUNT(DISTINCT result_key) 
				FROM {$rtable};"
			);

			$data = $wpdb->get_results(
				"SELECT
					SUM({$rtable}.result_count) AS count,
					{$rtable}.result_key
					FROM {$rtable}
					INNER JOIN {$utable} ON {$rtable}.pid={$utable}.pid
					GROUP BY {$rtable}.result_key
					ORDER BY {$order_by} {$direction}
					LIMIT {$count}
					OFFSET {$offset}
					;"
			);
			// phpcs:enable

		}

		return new WP_REST_Response( array( $data, $rowcount ), 200 );
	}


	/**
	 * Update one item from the collection
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function update_item( $request ) {

		$data = $this->send_results( $request );
		if ( ! ( in_array( false, $data, true ) ) ) {
			return new WP_REST_Response( $data, 200 );
		}
		return new WP_REST_Response( $data, 500 );
	}

	/**
	 * Returns the pid from the URL table.
	 *
	 * @param string $url to find.
	 */
	public function get_pid( $url ) {
		// Get Page ID so we can avoid complex joins in subsequent queries.
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
		return $pid;
	}

	/**
	 *
	 * Attempts to send item to DB
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 */
	public function send_results( $request ) {

		$params  = $request->get_params();
		$results = $params['data'];
		$now     = gmdate( 'Y-m-d H:i:s' );
		$rows    = 0; // If 0 at end, delete URL.
		$pid     = false;
		$return  = array();
		global $wpdb;

		// Handle clicks from dashboard to changed URLS first to prevent URL collisions.
		if ( $results['pid'] > -1 ) {
			$response = $wpdb->query( // phpcs:ignore
				$wpdb->prepare(
					"DELETE FROM {$wpdb->prefix}ed11y_urls
					WHERE
						(pid = %d AND page_url != %s)
					;",
					array(
						$results['pid'],
						$results['page_url'],
					)
				)
			);
		}

		// Check if any results exist.
		if ( $results['page_count'] > 0 || count( $results['dismissals'] ) > 0 ) {

			// Upsert page URL.
			$response = $wpdb->query( // phpcs:ignore
				$wpdb->prepare(
					"INSERT INTO {$wpdb->prefix}ed11y_urls
						(page_url,
						entity_type,
						page_title,
						page_total)
					VALUES (%s, %s, %s, %d)
					ON DUPLICATE KEY UPDATE
						entity_type = %s,
						page_title = %s,
						page_total = %d
					;",
					array(
						$results['page_url'],
						$results['entity_type'],
						$results['page_title'],
						$results['page_count'],
						$results['entity_type'],
						$results['page_title'],
						$results['page_count'],
					)
				)
			);
			$return[] = $response;

			// Get Page ID so we can avoid complex joins in subsequent queries.
			$pid = $this->get_pid( $results['page_url'] );

			foreach ( $results['results'] as $key => $value ) {
				// Upsert results.
				$response = $wpdb->query( // phpcs:ignore
					$wpdb->prepare(
						"INSERT INTO {$wpdb->prefix}ed11y_results
                            (pid,
                            result_key,
                            result_count,
                            created,
                            updated)
                        VALUES (%s, %s, %d, %s, %s) 
                        ON DUPLICATE KEY UPDATE
                            result_count = %d,
                            updated = %s
                            ;",
						array(
							$pid,
							$key,
							$value,
							$now,
							$now,
							$value,
							$now,
						)
					)
				);
				$rows    += $response ? $response : 0;
				$return[] = $response;
			}

			foreach ( $results['dismissals'] as $key => $value ) {
				// Update last-seen date on dismissals.
				$response = $wpdb->query( // phpcs:ignore
					$wpdb->prepare(
						"UPDATE {$wpdb->prefix}ed11y_dismissals 
                        SET updated = %s, stale = 0
                        WHERE pid = %s AND result_key = %s AND element_id = %s;",
						// Todo include element_id.
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
		}

		if ( ! is_numeric( $pid ) ) {
			// Resultless pages missed the foreach.
			$pid = $this->get_pid( $results['page_url'] );
			// For pages with no issues, this is the only query.
		}

		if ( 0 < $pid ) {
			// If page is in urls table, updates are in order.

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

			// Mark any out-of-date dismissals as stale.
			$response = $wpdb->query( // phpcs:ignore
				$wpdb->prepare(
					"UPDATE {$wpdb->prefix}ed11y_dismissals 
					SET stale = 1
					WHERE pid = %d AND updated != %s ;",
					array(
						$results['page_url'],
						$now,
					)
				)
			);
			$rows    += $response ? $response : 0;
			$return[] = $response;

			if ( 0 === $rows ) {
				// No records for this route.
				$response = $wpdb->query( // phpcs:ignore
					$wpdb->prepare(
						"DELETE FROM {$wpdb->prefix}ed11y_urls WHERE pid = %d;",
						array(
							$pid,
						)
					)
				);
			}
		}
		return $return;
	}

	/**
	 * Check if a given request has access to update a specific item
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|bool
	 */
	public function update_item_permissions_check( $request ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInExtendedClass
		return current_user_can( 'edit_posts' );
	}
}
