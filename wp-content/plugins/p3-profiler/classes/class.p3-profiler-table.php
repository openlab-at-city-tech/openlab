<?php
if ( !defined('P3_PATH') )
	die( 'Forbidden ');

/**
 * Lists the performance profiles
 *
 * @author GoDaddy.com
 * @version 1.0
 * @package P3_Profiler
 */
class P3_Profiler_Table extends WP_List_Table {

	/**************************************************************************/
	/**        SETUP                                                         **/
	/**************************************************************************/

	/**
	 * Constructor
	 * @return P3_Profiler_Table
	 */
	public function __construct() {
		parent::__construct(
			array(
				'singular'  => _n( 'scan', 'scans', 1, 'p3-profiler' ),
				'plural'    => _n( 'scan', 'scans', 2, 'p3-profiler' ),
			)
		);
	}

	/**
	 * Set up the columns, dataset, paginator
	 * @return void
	 */
    public function prepare_items() {

		// Set up columns
        $columns               = $this->get_columns();
        $hidden                = array();
        $sortable              = $this->get_sortable_columns();
        $this->_column_headers = array( $columns, $hidden, $sortable );

		// Perform bulk actions
		$this->do_bulk_action();
        $data = $this->_get_profiles();

		// Sort data
		$orderby = ( !empty( $_REQUEST['orderby']) ) ? $_REQUEST['orderby'] : 'name';
		$order   = ( !empty( $_REQUEST['order']) ) ? $_REQUEST['order'] : 'asc';
		$data    = $this->_sort( $data, $orderby, $order );

		// 20 items per page
		$per_page = 20;

		// Get page number
		$current_page = $this->get_pagenum();

		// Get total items
        $total_items = count( $data );
		
		// Carve out only the visible dataset
        $data        = array_slice( $data, ( $current_page - 1 ) * $per_page, $per_page );
        $this->items = $data;

		// Set up the paginator
        $this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
				'total_pages' => ceil( $total_items / $per_page ),
			)
		);
    }

	/**************************************************************************/
	/**        COLUMN PREP                                                   **/
	/**************************************************************************/
	
	/**
	 * If there's no column_[whatever] method available, use this to render
	 * the column
	 * @param array $item
	 * @param string $column_name
	 * @return string
	 */
    public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'name' :
			case 'date' :
			case 'count' :
			case 'filesize' :
				return $item[$column_name];
				break;
			default:
				return '';
		}
	}

	/**
	 * Render the "title" column
	 * @param array $item
	 * @return string 
	 */
    public function column_title( $item ) {
        $actions = array(
            'delete' => sprintf( '<a href="?page=%s&action=%s&name=%s">' . __( 'Delete', 'p3-profiler' ) . '</a>', sanitize_text_field( $_REQUEST['name'] ), 'delete', $item['name'] ),
        );

        //Return the title contents
        return sprintf(
			'%1$s <span style="color:silver">(id:%2$s )</span>%3$s',
			$item['name'],
			$item['name'],
			$this->row_actions( $actions )
		);
    }

	/**
	 * Render the checkbox column
	 * @param type $item
	 * @return string
	 */
    public function column_cb( $item ) {
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            $this->_args['singular'],
            $item['filename']
        );
    }

	/**
	 * Get a list of columns
	 * @return array
	 */
    public function get_columns() {
        $columns = array(
            'cb'       => '<input type="checkbox" />',
            'name'     => __( 'Name', 'p3-profiler' ),
            'date'     => __( 'Date', 'p3-profiler' ),
            'count'    => __( 'Visits', 'p3-profiler' ),
			'filesize' => __( 'Size', 'p3-profiler' ),
        );
        return $columns;
    }

	/**
	 * Get a list of sortable columns (note, do not return the checkbox column )
	 * @return array 
	 */
	public function get_sortable_columns() {
        $sortable_columns = array(
            'name'     => array( 'name', true ),
            'date'     => array( 'date', true ),
            'count'    => array( 'count', true ),
			'filesize' => array( 'filesize', true ),
        );
        return $sortable_columns;
    }

	/**
	 * Add some the "view" and "delete" links to the scan 
	 * @param string $key Internal key (scan filename )
	 * @param string $display Display key (scan filename )
	 * @return string
	 */
	private function _action_links( $key, $display ) {
		$url = esc_url( add_query_arg(
			array(
				'p3_action' => 'view-scan',
				'name' => $key,
				'current_scan' => null,
			)
		) );
		$ret  = '<a href="' . esc_attr( $url ). '" title="' . esc_attr__( 'View the results of this scan', 'p3-profiler' ) . '"><strong>' . $display . '</strong></a>';
		$ret .= '<div class="row-actions-visible">';
		$ret .= '  <span class="view">';
		$ret .= '    <a href="' . esc_attr( $url ) . '" data-name="' . esc_attr( $key ) . '" title="' . esc_attr__( 'View the results of this scan', 'p3-profiler' ) . '" class="view-results">' . __( 'View', 'p3-profiler' ) . '</a> |';
		$ret .= '  </span>';
		$ret .= '  <span>';
		$ret .= '    <a href="javascript:;" data-name="' . esc_attr( $key ) . '" title="' . esc_attr__( 'Continue this scan', 'p3-profiler' ) . '" class="p3-continue-scan">' . __( 'Continue', 'p3-profiler' ) . '</a> |';
		$ret .= '  </span>';
		$ret .= '  <span class="delete">';
		$ret .= '    <a href="javascript:;" data-name="' . esc_attr( $key ) . '" title="' . esc_attr__( 'Delete this scan', 'p3-profiler' ) . '" class="delete-scan delete">' . __( 'Delete', 'p3-profiler' ) . '</a>';
		$ret .= '  </span>';
		$ret .= '</div>';
		return $ret;
	}
	
	/**************************************************************************/
	/**        BULK ACTIONS                                                  **/
	/**************************************************************************/
	
	/**
	 * Get a list of which actions are available in the bulk actions dropdown
	 * @return string 
	 */
    public function get_bulk_actions() {
        $actions = array( 'delete' => __( 'Delete', 'p3-profiler' ) );
        return $actions;
    }

	/**
	 * Performan any bulk actions
	 * @return void
	 */
    public function do_bulk_action() {
		global $p3_profiler_plugin;
        if ( 'delete' === $this->current_action() && !empty( $_REQUEST['scan'] ) ) {
			if ( !wp_verify_nonce( $_REQUEST['p3_nonce'], 'delete_scans' ) ) {
				wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
			}
			foreach ( $_REQUEST['scan'] as $scan ) {
				$file = P3_PROFILES_PATH  . DIRECTORY_SEPARATOR . basename( $scan );
				if ( !file_exists( $file ) || !is_writable( $file ) || !unlink( $file ) ) {
					wp_die( __( 'Error removing file: ', 'p3-profiler' ) . $file );
				}
			}
			$count = count( $_REQUEST['scan'] );
			echo '<div class="updated delete-msg"><p>'
				. sprintf( _n( 'Deleted %d scan. ', 'Deleted %d scans.' , $count, 'p3-profiler' ), $count )
				. '</p></div>';
		}
    }

	/**************************************************************************/
	/**        DATA PREP                                                     **/
	/**************************************************************************/

	/**
	 * Sort the data
	 * @param array $data
	 * @param string $field Field name (e.g. 'name' or 'count')
	 * @param string $direction asc / desc
	 * @return array
	 */
	private function _sort( $data, $field, $direction ) {

		// Override the count / date fields as they've had some display markup
		// applied to them and need to be sorted on the original values
		switch ( $field ) {
			case 'count' :
				$field = '_count';
				break;
			case 'date' :
				$field = '_date';
				break;
			case 'filesize' :
				$field = '_filesize';
				break;
		}
		$sorter = new P3_Profiler_Table_Sorter( $data, $field );
		return $sorter->sort( $direction );
	}

	/**
	 * Get a list of the profiles in the profiles folder
	 * Profiles are named as "*.json".  Add additional info, too, like
	 * date and number of visits in the file
	 * @uses list_files
	 * @return type 
	 */
	private function _get_profiles() {
		$p3_profile_dir = P3_PROFILES_PATH;
		$files          = list_files( $p3_profile_dir );
		$files          = array_filter( $files, array( &$this, '_filter_json_files' ) );
		$ret            = array();
		foreach ( $files as $file ) {
			$time  = filemtime( $file );
			$count = count( file( $file ) );
			$key   = basename( $file );
			$name  = substr( $key, 0, -5 ); // strip off .json
			$ret[] = array(
				'filename'  => basename( $file ),
				'name'      => $this->_action_links( $key, $name ),
				'date'      => date( 'D, M jS', $time ) . ' at ' . date( 'g:i a', $time ),
				'count'     => number_format( $count ),
				'filesize'  => P3_Profiler_Plugin_Admin::readable_size( filesize( $file ) ),
				'_filesize' => filesize( $file ),
				'_date'     => $time,
				'_count'    => $count,
			);
		}
		return $ret;
	}

	/**
	 * Only let "*.json" files pass through
	 * @param type $file
	 * @return type 
	 */
	private function _filter_json_files( $file ) {
		return ( '.json' == substr( strtolower( $file ), -5 ) );
	}
}
