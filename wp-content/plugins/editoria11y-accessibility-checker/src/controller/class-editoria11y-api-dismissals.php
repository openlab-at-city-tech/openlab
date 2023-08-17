<?php //phpcs:ignore
/**
 * Stores tests results
 * Reference https://developer.wordpress.org/rest-api/extending-the-rest-api/controller-classes/
 * POST v PUT in https://developer.wordpress.org/reference/classes/wp_rest_server/
 *
 * @package         Editoria11y
 */
class Editoria11y_Api_Dismissals extends WP_REST_Controller {

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
					// Report results for a URL.
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_dismissals' ),
					'permission_callback' => array( $this, 'update_item_permissions_check' ),
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
	 */
	public function send_dismissal( $request ) {
		$params  = $request->get_params();
		$results = $params['data'];
		$now     = gmdate( 'Y-m-d H:i:s' );
		global $wpdb;
		$pid = $wpdb->get_var( // phpcs:ignore
			$wpdb->prepare(
				"SELECT pid FROM {$wpdb->prefix}ed11y_urls
				WHERE page_url=%s;",
				array(
					$results['page_url'],
				)
			)
		);

		if ( 'reset' === $results['dismissal_status'] ) {

			// Delete URL if total is 0, record if it never existed.
			$response = $wpdb->query( // phpcs:ignore
				$wpdb->prepare(
					"DELETE FROM {$wpdb->prefix}ed11y_dismissals 
					WHERE pid = %d 
					AND (
						dismissal_status = 'ok'
						OR
						(
							dismissal_status = 'hide'
							AND
							user = %d
						)
					);",
					array(
						$pid,
						wp_get_current_user()->ID,
					)
				)
			);

			return $response;

		} else {

			$response = $wpdb->query( // phpcs:ignore
				$wpdb->prepare(
					"INSERT INTO {$wpdb->prefix}ed11y_dismissals 
						(pid,
						result_key,
						user,
						element_id,
						dismissal_status,
						created,
						updated,
						stale)
					VALUES (%s, %s, %d, %s, %s, %s, %s, %d) 
						;",
					array(
						$pid,
						$results['result_key'],
						wp_get_current_user()->ID,
						$results['element_id'],
						$results['dismissal_status'],
						$now,
						$now,
						0,
					)
				)
			);

			return $response;
		}
	}

	/**
	 * Get dashboard table data.
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_dismissals( $request ) {
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
		$dtable      = $wpdb->prefix . 'ed11y_dismissals';

		// Get top pages.

		// Sort by sanitized param; page total is default.
		$order_by = $order_by ? $order_by : 'created';

		// Build where clause based on sanitized params.
		$where = '';
		if ( $result_key ) {
			// Filtering by test name.
			$where = "WHERE {$dtable}.result_key = '{$result_key}'";
		}
		if ( $entity_type ) {
			// Filtering by entity type.
			$where = empty( $where ) ? 'WHERE ' : $where . 'AND ';
			$where = $where . "{$utable}.entity_type = '{$entity_type}'";
		}

		if ( 'page_title' === $order_by ) {
			$order_by = "{$utable}.{$order_by}";
		} elseif ( 'display_name' === $order_by ) {
			$order_by = "{$wpdb->users}.{$order_by}";
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
					{$wpdb->users}.display_name,
					{$dtable}.result_key,
					{$dtable}.dismissal_status,
					MAX({$dtable}.created) AS created,
					{$dtable}.stale
					FROM {$dtable}
					INNER JOIN {$utable} ON ({$dtable}.pid={$utable}.pid)
					LEFT JOIN {$wpdb->users} ON ({$wpdb->users}.ID={$dtable}.user)
					{$where}
					GROUP BY
					{$utable}.pid,
					{$utable}.page_url,
					{$utable}.page_title,
					{$utable}.entity_type,
					{$wpdb->users}.display_name,
					{$dtable}.result_key,
					{$dtable}.dismissal_status,
					{$dtable}.stale
					ORDER BY {$order_by} {$direction}
					LIMIT {$count}
					OFFSET {$offset}
					;"
		);

		// Get_var with COUNT(*) would be more performant, but I can't figure out how to work it with join+group+aggregation.
		$rowcounter = $wpdb->get_results(
			"SELECT
					MAX({$dtable}.created) AS created
					FROM {$dtable}
					INNER JOIN {$utable} ON ({$dtable}.pid={$utable}.pid)
					{$where}
					GROUP BY
					{$utable}.pid,
					{$dtable}.user,
					{$dtable}.result_key,
					{$dtable}.dismissal_status,
					{$dtable}.stale
					;"
		);
		$rowcount   = $wpdb->num_rows;

		// phpcs:enable
		return new WP_REST_Response( array( $data, $rowcount ), 200 );
	}

	/**
	 * Check if a given request has access to update a specific item
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|bool
	 */
	public function update_item_permissions_check( $request ) { // phpcs:ignore
		return current_user_can( 'edit_posts' );
	}

	/**
	 * Check if a given request has access to delete a specific item
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|bool
	 */
	public function delete_item_permissions_check( $request ) { // phpcs:ignore
		return current_user_can( 'edit_others_posts' );
	}

}
