<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * AI Content Analysis DB
 *
 * Manages content analysis data for articles including Tags, Readability, and Gap Analysis.
 * Stores analysis results, scores, and metadata in a dedicated table for better performance.
 */
class EPKB_AI_Content_Analysis_DB extends EPKB_DB {

	/**
	 * Version History:
	 * 1.0 - Initial table structure
	 * 1.1 - Renamed columns: gap_analysis_data -> gap_data, tags_usage_data -> tags_data; Added version to JSON data
	 */
	const TABLE_VERSION = '1.1';
	const PER_PAGE = 50;
	const PRIMARY_KEY = 'id';

	/**
	 * Get things started
	 */
	public function __construct( $db_check=false ) {
		parent::__construct();

		global $wpdb;
		$this->table_name = $wpdb->prefix . 'epkb_ai_content_analysis';
		$this->primary_key = self::PRIMARY_KEY;

		// Ensure latest table exists
		if ( $db_check ) {
			$this->check_db();
		}
	}

	/**
	 * Get columns and formats
	 *
	 * @return array
	 */
	public function get_column_format() {
		return array(
			'post_id'              => '%d',
			'kb_id'                => '%d',
			'overall_score'        => '%d',
			'importance'           => '%d',
			'tags_usage_score'     => '%d',
			'tags_data'            => '%s',
			'readability_score'    => '%d',
			'readability_data'     => '%s',
			'gap_analysis_score'   => '%d',
			'gap_data'             => '%s',
			'analyzed_at'          => '%s',
			'date_improved'        => '%s',
			'date_ignored'         => '%s',
			'date_done'            => '%s',
			'status'               => '%s',
			'error_message'        => '%s',
			'created'              => '%s',
			'updated'              => '%s'
		);
	}

	/**
	 * Get default column values
	 *
	 * @return array
	 */
	public function get_column_defaults() {
		return array(
			'id'                   => 0,
			'post_id'              => 0,
			'kb_id'                => 1,
			'overall_score'        => 0,
			'importance'           => 0,
			'tags_usage_score'     => 0,
			'tags_data'            => null,
			'readability_score'    => 0,
			'readability_data'     => null,
			'gap_analysis_score'   => 0,
			'gap_data'             => null,
			'analyzed_at'          => null,
			'date_improved'        => null,
			'date_ignored'         => null,
			'date_done'            => null,
			'status'               => 'pending',
			'error_message'        => null,
			'created'              => gmdate( 'Y-m-d H:i:s' ),
			'updated'              => gmdate( 'Y-m-d H:i:s' )
		);
	}

	/**
	 * Get analysis data for an article
	 *
	 * @param int $article_id Article ID
	 * @return object|null|WP_Error Analysis data or null if not found or WP_Error on failure
	 */
	public function get_article_analysis( $article_id ) {

		// Return null if table doesn't exist yet (prevents DB error logs)
		if ( ! $this->installed() ) {
			return null;
		}

		$row = $this->get_a_row_by_where_clause( array( 'post_id' => $article_id ) );
		$this->handle_db_error( $row, 'get_article_analysis' );
		if ( is_wp_error( $row ) ) {
			return $row;
		}
		if ( empty( $row ) ) {
			return null;
		}

		return $row;
	}

	/**
	 * Save or update analysis data for an article
	 *
	 * @param int $article_id Article ID
	 * @param array $analysis_data Analysis data to save
	 * @return int|WP_Error Record ID or error
	 */
	public function save_article_analysis( $article_id, $analysis_data ) {

		// Check if record exists
		$existing = $this->get_article_analysis( $article_id );

		// Prepare data with post_id
		$data = wp_parse_args( $analysis_data, array( 'post_id' => $article_id ) );
		$data['updated'] = gmdate( 'Y-m-d H:i:s' );

		if ( $existing && ! is_wp_error( $existing ) ) {
			// Update existing record
			$result = $this->update_record( $existing->id, $data );
			$this->handle_db_error( $result, 'save_article_analysis' );

			return $result ? $existing->id : new WP_Error( 'update_failed', 'Failed to update analysis' );
		} else {
			// Insert new record
			$data['created'] = gmdate( 'Y-m-d H:i:s' );
			$result = $this->insert_record( $data );
			$this->handle_db_error( $result, 'save_article_analysis' );

			return $result;
		}
	}

	/**
	 * Update Tags Usage analysis
	 *
	 * @param int $article_id Article ID
	 * @param int $score Tags usage score
	 * @param array $analysis_data Full tags analysis data
	 * @return bool|WP_Error
	 */
	public function update_tags_usage( $article_id, $score, $analysis_data ) {

		$data = array(
			'tags_usage_score' => (int) $score,
			'tags_data'        => wp_json_encode( $analysis_data )
		);

		return $this->update_analysis_component( $article_id, $data );
	}

	/**
	 * Update Readability analysis
	 *
	 * @param int $article_id Article ID
	 * @param int $score Readability score
	 * @param array $analysis_data Full readability analysis data
	 * @return bool|WP_Error
	 */
	public function update_readability( $article_id, $score, $analysis_data ) {

		$data = array(
			'readability_score' => (int) $score,
			'readability_data'  => wp_json_encode( $analysis_data )
		);

		return $this->update_analysis_component( $article_id, $data );
	}

	/**
	 * Update Gap Analysis
	 *
	 * @param int $article_id Article ID
	 * @param int $score Gap analysis score
	 * @param array $analysis_data Full gap analysis data
	 * @return bool|WP_Error
	 */
	public function update_gap_analysis( $article_id, $score, $analysis_data ) {

		$data = array(
			'gap_analysis_score' => (int) $score,
			'gap_data'           => wp_json_encode( $analysis_data )
		);

		return $this->update_analysis_component( $article_id, $data );
	}

	/**
	 * Update analysis component and recalculate overall score
	 *
	 * @param int $article_id Article ID
	 * @param array $data Component data to update
	 * @return int|WP_Error
	 */
	private function update_analysis_component( $article_id, $data ) {

		// Get current analysis
		$existing = $this->get_article_analysis( $article_id );

		// Merge with existing data or create new
		if ( $existing ) {
			$current_data = array(
				'tags_usage_score'   => $existing->tags_usage_score,
				'readability_score'  => $existing->readability_score,
				'gap_analysis_score' => $existing->gap_analysis_score
			);
		} else {
			$current_data = array(
				'tags_usage_score'   => 0,
				'readability_score'  => 0,
				'gap_analysis_score' => 0
			);
		}

		// Update with new component data
		$current_data = array_merge( $current_data, $data );

		// Calculate overall score (average of three components)
		$overall_score = round( ( $current_data['tags_usage_score'] + $current_data['readability_score'] + $current_data['gap_analysis_score'] ) / 3 );

		// Calculate article importance based on view count
		$importance = EPKB_AI_Content_Analysis_Utilities::calculate_article_importance( $article_id );

		// Add overall score, importance, and analyzed timestamp
		$data['overall_score'] = $overall_score;
		$data['importance'] = $importance;
		$data['analyzed_at'] = gmdate( 'Y-m-d H:i:s' );
		$data['status'] = 'analyzed';

		// Save or update
		return $this->save_article_analysis( $article_id, $data );
	}

	/**
	 * Set article status dates
	 *
	 * @param int $article_id Article ID
	 * @param string $action Action: 'improved', 'ignored', 'done'
	 * @param bool $set True to set, false to clear
	 * @return int|WP_Error
	 */
	public function set_article_date( $article_id, $action, $set = true ) {

		$valid_actions = array( 'improved', 'ignored', 'done' );
		if ( ! in_array( $action, $valid_actions ) ) {
			return new WP_Error( 'invalid_action', 'Invalid action' );
		}

		$field = 'date_' . $action;
		$data = array( $field => $set ? gmdate( 'Y-m-d H:i:s' ) : null );

		return $this->save_article_analysis( $article_id, $data );
	}

	/**
	 * Get analysis list with pagination and filters
	 *
	 * @param array $args Query arguments
	 * @return array
	 */
	public function get_analysis_list( $args = array() ) {
		global $wpdb;

		$defaults = array(
			'page'       => 1,
			'per_page'   => self::PER_PAGE,
			'kb_id'      => 0,
			'status'     => '',
			'min_score'  => 0,
			'max_score'  => 100,
			'search'     => '',
			'orderby'    => 'analyzed_at',
			'order'      => 'DESC'
		);

		$args = wp_parse_args( $args, $defaults );

		// Build WHERE clauses
		$where = array();

		if ( ! empty( $args['kb_id'] ) ) {
			$where[] = $wpdb->prepare( 'kb_id = %d', $args['kb_id'] );
		}

		if ( ! empty( $args['status'] ) ) {
			$where[] = $wpdb->prepare( 'status = %s', $args['status'] );
		}

		if ( $args['min_score'] > 0 ) {
			$where[] = $wpdb->prepare( 'overall_score >= %d', $args['min_score'] );
		}

		if ( $args['max_score'] < 100 ) {
			$where[] = $wpdb->prepare( 'overall_score <= %d', $args['max_score'] );
		}

		// Add search
		if ( ! empty( $args['search'] ) ) {
			$search_term = '%' . $wpdb->esc_like( $args['search'] ) . '%';
			$where[] = $wpdb->prepare( 'post_id IN (SELECT ID FROM ' . $wpdb->posts . ' WHERE post_title LIKE %s)', $search_term );
		}

		// Build query
		$where_clause = ! empty( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '';

		// Validate orderby - use sanitized column name
		$allowed_orderby = array( 'analyzed_at', 'overall_score', 'importance', 'created', 'updated' );
		$orderby = in_array( $args['orderby'], $allowed_orderby, true ) ? $args['orderby'] : 'analyzed_at';
		$orderby = esc_sql( $orderby );

		$order = strtoupper( $args['order'] ) === 'ASC' ? 'ASC' : 'DESC';

		// Calculate offset
		$page = max( 1, absint( $args['page'] ) );
		$per_page = max( 1, min( 100, absint( $args['per_page'] ) ) );
		$offset = ( $page - 1 ) * $per_page;

		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- $orderby and $order are validated above
		$sql = "SELECT * FROM {$this->table_name} {$where_clause} ORDER BY `{$orderby}` {$order} LIMIT %d OFFSET %d";
		$rows = $wpdb->get_results( $wpdb->prepare( $sql, $per_page, $offset ) );

		$this->handle_db_error( $rows, 'get_analysis_list' );

		return $rows ?: array();
	}

	/**
	 * Get count of analysis records
	 *
	 * @param array $args Query arguments
	 * @return int
	 */
	public function get_analysis_count( $args = array() ) {
		global $wpdb;

		$where = array();

		if ( ! empty( $args['kb_id'] ) ) {
			$where[] = $wpdb->prepare( 'kb_id = %d', $args['kb_id'] );
		}

		if ( ! empty( $args['status'] ) ) {
			$where[] = $wpdb->prepare( 'status = %s', $args['status'] );
		}

		if ( ! empty( $args['min_score'] ) ) {
			$where[] = $wpdb->prepare( 'overall_score >= %d', $args['min_score'] );
		}

		if ( ! empty( $args['max_score'] ) && $args['max_score'] < 100 ) {
			$where[] = $wpdb->prepare( 'overall_score <= %d', $args['max_score'] );
		}

		if ( ! empty( $args['search'] ) ) {
			$search_term = '%' . $wpdb->esc_like( $args['search'] ) . '%';
			$where[] = $wpdb->prepare( 'post_id IN (SELECT ID FROM ' . $wpdb->posts . ' WHERE post_title LIKE %s)', $search_term );
		}

		$where_clause = ! empty( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '';
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- $where_clause is built from $wpdb->prepare() calls
		$sql = "SELECT COUNT(*) FROM {$this->table_name} {$where_clause}";

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- $sql uses prepared values in $where_clause
		$count = $wpdb->get_var( $sql );
		$this->handle_db_error( $count, 'get_analysis_count' );

		return intval( $count );
	}

	/**
	 * Delete analysis data for an article
	 *
	 * @param int $article_id Article ID
	 * @return bool|WP_Error
	 */
	public function delete_article_analysis( $article_id ) {
		global $wpdb;

		$result = $wpdb->delete( $this->table_name, array( 'post_id' => $article_id ), array( '%d' ) );

		$this->handle_db_error( $result, 'delete_article_analysis' );

		if ( $result === false ) {
			return new WP_Error( 'db_error', $wpdb->last_error );
		}

		return true;
	}

	/**
	 * Clear all analysis data for a KB
	 *
	 * @param int $kb_id KB ID
	 * @return bool|WP_Error
	 */
	public function clear_kb_analysis( $kb_id ) {
		global $wpdb;

		$result = $wpdb->delete( $this->table_name, array( 'kb_id' => $kb_id ), array( '%d' ) );

		$this->handle_db_error( $result, 'clear_kb_analysis' );

		if ( $result === false ) {
			return new WP_Error( 'db_error', $wpdb->last_error );
		}

		return $result;
	}

	/**
	 * Get the table version
	 *
	 * @return string
	 */
	protected function get_table_version() {
		return self::TABLE_VERSION;
	}

	/**
	 * Create the table
	 *
	 * Table stores content analysis results for articles.
	 *
	 * Table columns:
	 * - id: Primary key
	 * - post_id: Post ID
	 * - kb_id: Knowledge Base ID
	 * - overall_score: Calculated average of all component scores (0-100)
	 * - importance: Article importance score (0-100)
	 * - tags_usage_score: Tags usage score (0-100)
	 * - tags_data: JSON data for tags analysis (includes version field)
	 * - readability_score: Readability score (0-100)
	 * - readability_data: JSON data for readability analysis (includes version field)
	 * - gap_analysis_score: Gap analysis score (0-100)
	 * - gap_data: JSON data for gap analysis (includes version field)
	 * - analyzed_at: Timestamp of last analysis
	 * - date_improved: Timestamp when marked as improved
	 * - date_ignored: Timestamp when marked as ignored
	 * - date_done: Timestamp when marked as done
	 * - status: Analysis status: 'pending', 'analyzing', 'analyzed', 'error'
	 * - error_message: Error message if status is 'error'
	 * - created: Record creation timestamp
	 * - updated: Last update timestamp
	 *
	 * IMPORTANT: When modifying this table structure, you MUST update TABLE_VERSION constant at the top of this class!
	 */
	protected function create_table() {
		global $wpdb;

		$collate = $wpdb->has_cap( 'collation' ) ? $wpdb->get_charset_collate() : '';

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		/** IMPORTANT: When modifying this table structure, you MUST update TABLE_VERSION constant at the top of this class! **/
		$sql = "CREATE TABLE {$this->table_name} (
			    id                   BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			    post_id              BIGINT(20) UNSIGNED NOT NULL,
			    kb_id                INT UNSIGNED        NOT NULL DEFAULT 1,
			    overall_score        TINYINT UNSIGNED    NOT NULL DEFAULT 0,
			    importance           TINYINT UNSIGNED    NOT NULL DEFAULT 0,
			    tags_usage_score     TINYINT UNSIGNED    NOT NULL DEFAULT 0,
			    tags_data            LONGTEXT            NULL,
			    readability_score    TINYINT UNSIGNED    NOT NULL DEFAULT 0,
			    readability_data     LONGTEXT            NULL,
			    gap_analysis_score   TINYINT UNSIGNED    NOT NULL DEFAULT 0,
			    gap_data             LONGTEXT            NULL,
			    analyzed_at          DATETIME            NULL,
			    date_improved        DATETIME            NULL,
			    date_ignored         DATETIME            NULL,
			    date_done            DATETIME            NULL,
			    status               VARCHAR(20)         NOT NULL DEFAULT 'pending',
			    error_message        VARCHAR(200)        NULL,
			    created              DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
			    updated              DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			    PRIMARY KEY (id),
			    UNIQUE KEY  uniq_post               (post_id),
			    KEY         idx_kb_id               (kb_id),
			    KEY         idx_status              (status),
			    KEY         idx_overall_score       (overall_score),
			    KEY         idx_analyzed_at         (analyzed_at)
		) $collate;";

		dbDelta( $sql );

		// Only store version if table was actually created successfully
		if ( $this->table_exists( $this->table_name ) ) {
			update_option( $this->get_version_option_name(), self::TABLE_VERSION, true );
		}
	}
}
