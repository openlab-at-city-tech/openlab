<?php
class Question_Reports_List_Table extends WP_List_Table{

    private $plugin_name;
    protected $current_user_can_edit;

    /** Class constructor */

    public function __construct($plugin_name) {

        $this->plugin_name = $plugin_name;
        $this->current_user_can_edit = Quiz_Maker_Data::quiz_maker_capabilities_for_editing();

        parent::__construct( array(
            'singular' => __( 'Question report', $this->plugin_name ), //singular name of the listed records
            'plural'   => __( 'Question reports', $this->plugin_name ), //plural name of the listed records
            'ajax'     => false //does this table support ajax?
        ) );

        add_action( 'admin_notices', array( $this, 'question_report_notices' ) );
    }

    /**
     * Override of table nav to avoid breaking with bulk actions & according nonce field
     */
    public function display_tablenav( $which ) {
        ?>
        <div class="tablenav <?php echo esc_attr( $which ); ?>">
            
            <div class="alignleft actions">
                <?php $this->bulk_actions( $which ); ?>
            </div>
             
            <?php
            $this->extra_tablenav( $which );
            $this->pagination( $which );
            ?>
            <br class="clear" />
        </div>
        <?php
    }

    protected function get_views() {
        $resolved_count = $this->resolved_records_count();
        $in_review_count = $this->in_review_records_count();
        $all_count = $this->all_record_count();
        $selected_all = "";
        $selected_0 = "";
        $selected_1 = "";
        if(isset($_GET['fstatus'])){
            switch($_GET['fstatus']){
                case "0":
                    $selected_0 = " style='font-weight:bold;' ";
                    break;
                case "1":
                    $selected_1 = " style='font-weight:bold;' ";
                    break;
                default:
                    $selected_all = " style='font-weight:bold;' ";
                    break;
            }
        }else{
            $selected_all = " style='font-weight:bold;' ";
        }

        $status_links = array(
            "all" => "<a ".$selected_all." href='?page=".esc_attr( $_REQUEST['page'] )."'>". __( 'All', $this->plugin_name )." (".$all_count.")</a>",
            "resolved" => "<a ".$selected_1." href='?page=".esc_attr( $_REQUEST['page'] )."&fstatus=1'>". __( 'Resolved', $this->plugin_name )." (".$resolved_count.")</a>",
            "in_review"   => "<a ".$selected_0." href='?page=".esc_attr( $_REQUEST['page'] )."&fstatus=0'>". __( 'In review', $this->plugin_name )." (".$in_review_count.")</a>"
        );
        return $status_links;
    }

    /**
     * Retrieve customers data from the database
     *
     * @param int $per_page
     * @param int $page_number
     *
     * @return mixed
     */
    public static function get_question_reports( $per_page = 20, $page_number = 1 ) {

        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_question_reports";

        $sql .= self::get_where_condition();

        if ( ! empty( $_REQUEST['orderby'] ) ) {
            $order_by  = ( isset( $_REQUEST['orderby'] ) && sanitize_text_field( $_REQUEST['orderby'] ) != '' ) ? sanitize_text_field( $_REQUEST['orderby'] ) : 'id';
            $order_by .= ( ! empty( $_REQUEST['order'] ) && strtolower( $_REQUEST['order'] ) == 'asc' ) ? ' ASC' : ' DESC';

            $sql_orderby = sanitize_sql_orderby($order_by);

            if ( $sql_orderby ) {
                $sql .= ' ORDER BY ' . $sql_orderby;
            } else {
                $sql .= ' ORDER BY id DESC';
            }
        }else{
            $sql .= ' ORDER BY id DESC';
        }

        $sql .= " LIMIT $per_page";
        $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;

        $result = $wpdb->get_results( $sql, 'ARRAY_A' );
        return $result;
    }

    public static function get_where_condition(){
        global $wpdb;

        $where = array();
        $sql = '';

        if(isset( $_REQUEST['fstatus'] )){            
            $fstatus = intval($_REQUEST['fstatus']);
            switch($fstatus){
                case 0:
                    $where[] = ' `resolved` = 0 ';
                    break;
                case 1:                    
                    $where[] = ' `resolved` = 1 ';
                    break;
            }
        }

        if( ! empty($where) ){
            $sql = " WHERE " . implode( " AND ", $where );
        }
        return $sql;
    }

    /**
     * Delete a customer record.
     *
     * @param int $id customer ID
     */
    public static function delete_reports( $id ) {
        global $wpdb;

        $wpdb->delete(
            "{$wpdb->prefix}aysquiz_question_reports",
            array('id' => $id),
            array('%d')
        );
    }

    public static function record_count() {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_question_reports";
        $sql .= self::get_where_condition();
        return $wpdb->get_var( $sql );
    }

    public static function all_record_count() {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_question_reports";

        return $wpdb->get_var( $sql );
    }

    public static function in_review_records_count() {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_question_reports WHERE `resolved` = 0";

        return $wpdb->get_var( $sql );
    }

    public static function resolved_records_count() {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_question_reports WHERE `resolved` = 1";

        return $wpdb->get_var( $sql );
    }

    /** Text displayed when no customer data is available */
    public function no_items() {
        echo __( 'There are no question reports yet.', $this->plugin_name );
    }

    /**
     * Render a column when no column specific method exist.
     *
     * @param array $item
     * @param string $column_name
     *
     * @return mixed
     */
    public function column_default( $item, $column_name ) {

        switch ( $column_name ) {
            case 'question_id':
                return '<p>
                            <a href="?page='. $this->plugin_name . '-questions&question='.$item[$column_name].'&action=edit">' . $item[$column_name] . ' <i class="ays_fa ays_fa_pencil_square" aria-hidden="true"></i></a>
                        </p>';
                break;
            case 'report_text':
                return nl2br( stripslashes($item[$column_name]) );
                break;
            case 'resolved':
                if ($item[$column_name]) {
                    return '<span style="color: green">Resolved</span>';
                } else {
                    return '<span style="color: red">In review</span>';
                }
                break;
            case 'report_id':
            case 'create_date':
            case 'resolve_date':
            default:
                return $item[$column_name]; 
        }
    }

    /**
     * Method for name column
     *
     * @param array $item an array of DB data
     *
     * @return string
     */
    function column_report_id( $item ) {
        $delete_nonce = wp_create_nonce( $this->plugin_name . '-delete-report' );
        $resolve_nonce = wp_create_nonce( $this->plugin_name . '-resolve-report' );
        $in_review_nonce = wp_create_nonce( $this->plugin_name . '-review-report' );
        $report_id = intval($item['id']);
        $report_status = intval($item['resolved']);

        if ($report_status) {
            $change_report_status = sprintf( '<a data-message="this review" href="?page=%s&action=%s&report=%s&_wpnonce=%s">%s</a>', esc_attr( $_REQUEST['page'] ), 'review', absint( $item['id'] ), $in_review_nonce, __( "Not Resolved", $this->plugin_name ) );
        } else {
            $change_report_status = sprintf( '<a data-message="this review" href="?page=%s&action=%s&report=%s&_wpnonce=%s">%s</a>', esc_attr( $_REQUEST['page'] ), 'resolve', absint( $item['id'] ), $resolve_nonce, __( "Resolve", $this->plugin_name ) );
        }

        $actions = array(
            'edit'   => sprintf( '<a data-message="this review" href="?page=%s&action=%s&question=%s">%s</a>', 'quiz-maker-questions', 'edit', absint( $item['question_id'] ), __( "Edit", $this->plugin_name ) ),
            'change_report_status' => $change_report_status,
            'delete' => sprintf( '<a class="ays_confirm_del" data-message="this review" href="?page=%s&action=%s&report=%s&_wpnonce=%s">%s</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $delete_nonce, __( "Delete", $this->plugin_name ) )
        );

        return $report_id . $this->row_actions( $actions );
    }
    
    /**
     * Render the bulk edit checkbox
     *
     * @param array $item
     *
     * @return string
     */
    public function column_cb( $item ) {
        return sprintf(
            '<input type="checkbox" class="ays_report_delete" name="bulk-delete[]" value="%s" />', $item['id']
        );
    }

    /**
     *  Associative array of columns
     *
     * @return array
     */
    function get_columns() {
        $columns = array(
            'cb'             => '<input type="checkbox" />',
            'report_id'             => __( 'ID', $this->plugin_name ),
            'question_id'    => __( 'Question ID', $this->plugin_name ),
            'report_text'    => __( 'Report', $this->plugin_name ),
            'create_date'    => __( 'Created', $this->plugin_name ),
            'resolve_date'   => __( 'Resolve date', $this->plugin_name ),
            'resolved'       => __( 'Resolved', $this->plugin_name ),
        );

        return $columns;
    }

    /**
     * Columns to make sortable.
     *
     * @return array
     */
    public function get_sortable_columns() {
        $sortable_columns = array(
            'report_id'            => array( 'id', true ),
        );

        return $sortable_columns;
    }

    /**
     * Mark as read a customer record.
     *
     * @param int $id customer ID
     */
    public static function mark_as_resolved_reports( $id ) {
        global $wpdb;
        $current_time = current_time('Y-m-d H:i:s');
        $wpdb->update(
            $wpdb->prefix . "aysquiz_question_reports",
            array(
                'resolved' => 1,
                'resolve_date' => $current_time
            ),
            array('id' => $id),
            array('%d', '%s'),
            array('%d')
        );
    }

    /**
     * Mark as unread a customer record.
     *
     * @param int $id customer ID
     */
    public static function mark_as_reviewed_reports( $id ) {
        global $wpdb;
        $wpdb->update(
            $wpdb->prefix . "aysquiz_question_reports",
            array('resolved' => 0, 'resolve_date' => NULL),
            array('id' => $id),
            array('%d', '%d'),
            array('%d')
        );
    }

    /**
     * Returns an associative array containing the bulk action
     *
     * @return array
     */
    public function get_bulk_actions() {
        $actions = array(
            'mark-as-resolved' => __( 'Mark as resolved', $this->plugin_name),
            'mark-as-reviewed' => __( 'Mark as reviewed', $this->plugin_name),
            'bulk-delete' => __( 'Delete', $this->plugin_name),
        );

        return $actions;
    }

    /**
     * Handles data query and filter, sorting, and pagination.
     */
    public function prepare_items() {

        global $wpdb;

        $this->_column_headers = $this->get_column_info();

        /** Process bulk action */
        $this->process_bulk_action();

        $per_page     = $this->get_items_per_page( 'quiz_question_reports_per_page', 20 );
        $current_page = $this->get_pagenum();
        $total_items  = self::record_count();

        $this->set_pagination_args( array(
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page'    => $per_page //WE have to determine how many items to show on a page
        ) );

        $this->items = self::get_question_reports( $per_page, $current_page );
    }

    public function process_bulk_action() {

        //Detect when a bulk action is being triggered...
        $message = 'deleted';
        if ( 'delete' === $this->current_action() ) {

            // In our file that handles the request, verify the nonce.
            $nonce = esc_attr( $_REQUEST['_wpnonce'] );

            if ( ! wp_verify_nonce( $nonce, $this->plugin_name . '-delete-report' ) ) {
                die( 'Go get a life script kiddies' );
            }
            else {
                self::delete_reports( absint( $_GET['report'] ) );

                // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
                // add_query_arg() return the current url

                $url = esc_url_raw( remove_query_arg(array('action', 'report', '_wpnonce')  ) ) . '&status=' . $message;
                wp_redirect( $url );
            }

        }

        //Detect when a bulk action is being triggered...
        $message = 'resolved';
        if ( 'resolve' === $this->current_action() ) {

            // In our file that handles the request, verify the nonce.
            $nonce = esc_attr( $_REQUEST['_wpnonce'] );

            if ( ! wp_verify_nonce( $nonce, $this->plugin_name . '-resolve-report' ) ) {
                die( 'Go get a life script kiddies' );
            }
            else {
                self::mark_as_resolved_reports( absint( $_GET['report'] ) );

                // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
                // add_query_arg() return the current url

                $url = esc_url_raw( remove_query_arg(array('action', 'report', '_wpnonce')  ) ) . '&status=' . $message;
                wp_redirect( $url );
            }

        }

        //Detect when a bulk action is being triggered...
        $message = 'reviewed';
        if ( 'review' === $this->current_action() ) {

            // In our file that handles the request, verify the nonce.
            $nonce = esc_attr( $_REQUEST['_wpnonce'] );

            if ( ! wp_verify_nonce( $nonce, $this->plugin_name . '-review-report' ) ) {
                die( 'Go get a life script kiddies' );
            }
            else {
                self::mark_as_reviewed_reports( absint( $_GET['report'] ) );

                // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
                // add_query_arg() return the current url

                $url = esc_url_raw( remove_query_arg(array('action', 'report', '_wpnonce')  ) ) . '&status=' . $message;
                wp_redirect( $url );
            }

        }

        // If the delete bulk action is triggered
        if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
            || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
        ) {

            $delete_ids = ( isset( $_POST['bulk-delete'] ) && ! empty( $_POST['bulk-delete'] ) ) ? esc_sql( $_POST['bulk-delete'] ) : array();

            // loop over the array of record IDs and delete them
            foreach ( $delete_ids as $id ) {
                self::delete_reports( $id );

            }

            // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
            // add_query_arg() return the current url

            $url = esc_url_raw( remove_query_arg(array('action', 'result', '_wpnonce')  ) ) . '&status=' . $message;
            wp_redirect( $url );
        }

        // If the mark-as-resolved bulk action is triggered
        if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'mark-as-resolved' ) || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'mark-as-resolved' ) ) {

            $delete_ids = ( isset( $_POST['bulk-delete'] ) && ! empty( $_POST['bulk-delete'] ) ) ? esc_sql( $_POST['bulk-delete'] ) : array();

            // loop over the array of record IDs and delete them
            foreach ( $delete_ids as $id ) {
                self::mark_as_resolved_reports( $id );
            }

            // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
            // add_query_arg() return the current url

            $url = esc_url_raw( remove_query_arg(array('action', 'result', '_wpnonce') ) );

            $message = 'marked-as-resolved';
            $url = add_query_arg( array(
                'status' => $message,
            ), $url );
            wp_redirect( $url );
        }

        // If the mark-as-unread bulk action is triggered
        if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'mark-as-unread' ) || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'mark-as-reviewed' ) ) {

            $delete_ids = ( isset( $_POST['bulk-delete'] ) && ! empty( $_POST['bulk-delete'] ) ) ? esc_sql( $_POST['bulk-delete'] ) : array();

            // loop over the array of record IDs and delete them
            foreach ( $delete_ids as $id ) {
                self::mark_as_reviewed_reports( $id );
            }

            // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
            // add_query_arg() return the current url

            $url = esc_url_raw( remove_query_arg(array('action', 'result', '_wpnonce') ) );

            $message = 'marked-as-in-review';
            $url = add_query_arg( array(
                'status' => $message,
            ), $url );

            wp_redirect( $url );
        }
    }

    public function question_report_notices(){

        $status = (isset($_REQUEST['status'])) ? sanitize_text_field( $_REQUEST['status'] ) : '';

        if ( empty( $status ) )
            return;

        if ( 'updated' == $status ){
            $updated_message = esc_html( __( 'Question report updated.', $this->plugin_name ) );
        }
        $error_statuses = array( 'failed' );
        $notice_class = 'notice-success';
        if( in_array( $status, $error_statuses ) ){
            $notice_class = 'notice-error';
        }

        if ( empty( $updated_message ) )
            return;

        ?>
        <div class="notice <?php echo $notice_class; ?> is-dismissible">
            <p> <?php echo $updated_message; ?> </p>
        </div>
        <?php
    }
}
