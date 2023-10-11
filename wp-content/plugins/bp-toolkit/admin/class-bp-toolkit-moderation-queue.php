<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Create a new table class that will extend the WP_List_Table
 */
class BPTK_Moderation_Queue_List_Table extends WP_List_Table {
	/**
	 * Prepare the items for the table to process
	 *
	 * @return Void
	 */
	public function prepare_items() {



		$columns = $this->get_columns();
		$hidden = $this->get_hidden_columns();
		$sortable = $this->get_sortable_columns();

		$data = $this->fetch_table_data();
		usort( $data, array( &$this, 'sort_data' ) );

		$perPage = 15;
		$currentPage = $this->get_pagenum();
		$totalItems = count($data);

		$this->process_bulk_action();

		$this->handle_bulk_approve();
		$this->handle_bulk_delete();

		if ( isset( $_GET['action'] ) && isset( $_GET['activity'] ) && $_GET['page'] == "bp-toolkit-moderation-queue" && $_GET['action'] == "approve") {
			$nonce = wp_unslash( $_REQUEST['_wpnonce'] );
			if ( ! wp_verify_nonce( $nonce, 'row_action' ) ) { // verify the nonce.
				$this->invalid_nonce_redirect();
			} else {
				bptk_unmoderate_activity( $_REQUEST['activity'], 'activity' );
				wp_redirect( esc_url( add_query_arg() ) );
				exit;
			}
		}

		if (isset($_GET['action']) && isset( $_GET['activity'] ) && $_GET['page'] == "bp-toolkit-moderation-queue" && $_GET['action'] == "delete") {
			$nonce = wp_unslash( $_REQUEST['_wpnonce'] );
			if ( ! wp_verify_nonce( $nonce, 'row_action' ) ) { // verify the nonce.
				$this->invalid_nonce_redirect();
			} else {
				bp_activity_delete( array( 'id' => $_REQUEST['activity'] ) );
				wp_redirect( esc_url( add_query_arg() ) );
				exit;
			}
		}



		$this->set_pagination_args( array(
			'total_items' => $totalItems,
			'per_page'    => $perPage
		) );

		$data = array_slice($data,(($currentPage-1)*$perPage),$perPage);

		$this->_column_headers = array($columns, $hidden, $sortable);
		$this->items = $data;
	}

	/**
	 * Handle bulk approval.
	 *
	 * @return void
	 */
	public function handle_bulk_approve() {
		if ( ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] === 'unmoderate' ) || ( isset( $_REQUEST['action2'] ) && $_REQUEST['action2'] === 'unmoderate' ) ) {

			$nonce = wp_unslash( $_REQUEST['_wpnonce'] );
			/*
			 * Note: the nonce field is set by the parent class
			 * wp_nonce_field( 'bulk-' . $this->_args['plural'] );
			 */
			if ( ! wp_verify_nonce( $nonce, 'bulk-bsr-1_page_bp-toolkit-moderation-queue' ) ) { // verify the nonce.
				$this->invalid_nonce_redirect();
			}
			else {
				foreach ( $_REQUEST['activities'] as $activity_id ) {
					bptk_unmoderate_activity( $activity_id, 'activity' );
					wp_redirect( esc_url( add_query_arg() ) );
					exit;
				}
			}
		}
	}

	/**
	 * Handle bulk deletion.
	 *
	 * @return void
	 */
	public function handle_bulk_delete() {
		if ( ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] === 'delete' ) || ( isset( $_REQUEST['action2'] ) && $_REQUEST['action2'] === 'delete' ) ) {

			$nonce = wp_unslash( $_REQUEST['_wpnonce'] );
			/*
			 * Note: the nonce field is set by the parent class
			 * wp_nonce_field( 'bulk-' . $this->_args['plural'] );
			 */
			if ( ! wp_verify_nonce( $nonce, 'bulk-bsr-1_page_bp-toolkit-moderation-queue' ) ) { // verify the nonce.
				$this->invalid_nonce_redirect();
			}
			else {
				foreach ( $_REQUEST['activities'] as $activity_id ) {
					bp_activity_delete( array( 'id' => $activity_id ) );
					wp_redirect( esc_url( add_query_arg() ) );
					exit;
				}
			}
		}
	}

	/**
	 * Handle situation where the moderation queue is empty.
	 *
	 * @return void
	 */
	public function no_items() {
		esc_html_e( 'Nothing currently held for moderation.', 'bp-toolkit' );
	}

	/**
	 * Override the parent columns method. Defines the columns to use in your listing table
	 *
	 * @return Array
	 */
	public function get_columns() {
		$columns = array(
			'cb'            => '<input type="checkbox" />',
			'action' => esc_html__( 'Description', 'bp-toolkit' ),
			'content' => esc_html__( 'Content', 'bp-toolkit' ),
			'component'       => esc_html__( 'Component', 'bp-toolkit' ),
		);

		return $columns;
	}


	/**
	 * Set our row actions.
	 *
	 * @param $item
	 *
	 * @return string
	 */
	function column_action( $item ) {
		$nonce = wp_create_nonce( 'row_action' );
		$actions = array(
			'unmoderate'      => sprintf(
				'<a href="?page=%s&action=%s&activity=%s&_wpnonce=%s">%s</a>',
				$_REQUEST['page'],
				'approve',
				$item['id'],
				$nonce,
				esc_html__( 'Approve', 'bp-toolkit' )
			),
			'view'      => sprintf(
				'<a target="_blank" href="%s">%s</a>',
				bp_activity_get_permalink( $item['id'] ),
				esc_html__( 'View', 'bp-toolkit' )
			),
			'view_author'      => sprintf(
				'<a target="_blank" href="%s">%s</a>',
				bp_core_get_user_domain( $item['user_id'] ),
				esc_html__( 'Author', 'bp-toolkit' )
			),
			'delete'    => sprintf(
				'<a href="?page=%s&action=%s&activity=%s&_wpnonce=%s">%s</a>',
				$_REQUEST['page'],
				'delete',
				$item['id'],
				$nonce,
				esc_html__( 'Delete', 'bp-toolkit' )
			),
		);

		return sprintf('%1$s %2$s', $item['action'], $this->row_actions($actions));
	}


	/**
	 * Set our bulk actions.
	 *
	 * @return array
	 */
	function get_bulk_actions() {
		return array(
			'unmoderate' => esc_html__( 'Approve', 'bp-toolkit' ),
			'delete'     => esc_html__( 'Delete', 'bp-toolkit' ),
		);
	}

	/**
	 * Define which columns are hidden
	 *
	 * @return Array
	 */
	public function get_hidden_columns() {
		return array();
	}

	/**
	 * Define the sortable columns
	 *
	 * @return Array
	 */
	public function get_sortable_columns() {
//		return array('title' => array('title', false));
	}

	public function fetch_table_data() {
		global $wpdb;
		$wpdb_table = $wpdb->prefix . 'bp_activity';
		$orderby = ( isset( $_GET['orderby'] ) ) ? esc_sql( $_GET['orderby'] ) : 'date_recorded';
		$order = ( isset( $_GET['order'] ) ) ? esc_sql( $_GET['order'] ) : 'DESC';
		$moderated_activity_updates = bptk_get_moderated_list( 'activity' );
		$moderated_group_updates = bptk_get_moderated_list( 'groups' );

		$moderated_items = array_merge( $moderated_activity_updates, $moderated_group_updates );

		$ids = join("','", $moderated_items);

		$activity_query = "SELECT 
                        id, action, component, content, user_id, date_recorded
                      FROM 
                        $wpdb_table
                      WHERE id IN ('$ids')
                      ORDER BY $orderby $order";

		// query output_type will be an associative array with ARRAY_A.
		$query_results = $wpdb->get_results( $activity_query, ARRAY_A  );

		// return result array to prepare_items.
		return $query_results;
	}

	/**
	 * Define what data to show on each column of the table
	 *
	 * @param  Array $item        Data
	 * @param  String $column_name - Current column name
	 *
	 * @return Mixed
	 */
	public function column_default( $item, $column_name ) {
		switch( $column_name ) {
			case 'action':
			case 'component':
			case 'content':
				return $item[ $column_name ];

			default:
				return print_r( $item, true ) ;
		}
	}

	/**
	 * Get value for checkbox column.
	 *
	 * @param object $item  A row's data.
	 * @return string Text to be placed inside the column <td>.
	 */
	protected function column_cb( $item ) {
		return sprintf(
			'<label class="screen-reader-text" for="activity_' . $item['id'] . '">' . sprintf( __( 'Select %s' ), $item['id'] ) . '</label>'
			. "<input type='checkbox' name='activities[]' id='activity_{$item['id']}' value='{$item['id']}' />"
		);
	}

	/**
	 * Allows you to sort the data by the variables set in the $_GET
	 *
	 * @return Mixed
	 */
	private function sort_data( $a, $b )
	{
		// Set defaults
		$orderby = 'date_recorded';
		$order = 'desc';

		// If orderby is set, use this as the sort column
		if(!empty($_GET['orderby']))
		{
			$orderby = $_GET['orderby'];
		}

		// If order is set use this as the order
		if(!empty($_GET['order']))
		{
			$order = $_GET['order'];
		}


		$result = strcmp( $a[$orderby], $b[$orderby] );

		if($order === 'asc')
		{
			return $result;
		}

		return -$result;
	}
}
