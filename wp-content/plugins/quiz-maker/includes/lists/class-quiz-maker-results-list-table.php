<?php
ob_start();
class Results_List_Table extends WP_List_Table{
    private $plugin_name;
    /** Class constructor */
    public function __construct($plugin_name) {
        $this->plugin_name = $plugin_name;
        parent::__construct( array(
            'singular' => __( 'Result', $this->plugin_name ), //singular name of the listed records
            'plural'   => __( 'Results', $this->plugin_name ), //plural name of the listed records
            'ajax'     => false //does this table support ajax?
        ) );
        add_action( 'admin_notices', array( $this, 'results_notices' ) );

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
    
    public function extra_tablenav( $which ){
        global $wpdb;
        
        ?>

    <?php
    }

    
    /**
     * Retrieve customers data from the database
     *
     * @param int $per_page
     * @param int $page_number
     *
     * @return mixed
     */
    
    public static function get_reports( $per_page = 20, $page_number = 1 ) {

        global $wpdb;
        $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_quizes ";


        if ( ! empty( $_REQUEST['orderby'] ) ) {
            $sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
            $sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' DESC';
        } else {
            $sql .= ' ORDER BY id DESC';
        }

        $sql .= " LIMIT $per_page";
        $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;

        $result = $wpdb->get_results( $sql, 'ARRAY_A' );

        return $result;

    }
    
    public static function get_report_by_quiz_id( $quiz_id ){
        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_reports WHERE quiz_id=" . absint( intval( $quiz_id ) );

        $result = $wpdb->get_row($sql, 'ARRAY_A');

        return $result;
    }

    public function get_report_by_id( $id ){
        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_reports WHERE id=" . absint( intval( $id ) );

        $result = $wpdb->get_row($sql, 'ARRAY_A');

        return $result;
    }

    public function get_reports_titles(){
        global $wpdb;

        $sql = "SELECT {$wpdb->prefix}aysquiz_quizes.id,{$wpdb->prefix}aysquiz_quizes.title FROM {$wpdb->prefix}aysquiz_quizes";

        $result = $wpdb->get_results($sql, 'ARRAY_A');

        return $result;
    }

    public static function get_results_dates($quiz_id){
        global $wpdb;

        $sql = "SELECT MIN(DATE(`end_date`)) AS `min_date`, MAX(DATE(`end_date`)) AS `max_date`
                FROM {$wpdb->prefix}aysquiz_reports
                WHERE YEAR(DATE(`end_date`)) > 2000";
        if($quiz_id !== 0){
            $sql .= " WHERE quiz_id = {$quiz_id}";
        }
        $result = $wpdb->get_results($sql, 'ARRAY_A');

        return $result;
    }
    
    public function ays_see_all_results(){
        global $wpdb;
        $sql = "UPDATE {$wpdb->prefix}aysquiz_reports SET `read`=1";
        $wpdb->get_results($sql, 'ARRAY_A');
    }
    
    public static function get_each_date_statistic($date,$quiz_id = 0){
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_reports WHERE {$wpdb->prefix}aysquiz_reports.end_date LIKE '{$date}%'";
        if($quiz_id !== 0){
            $sql .= " AND quiz_id = {$quiz_id}";
        }
        $result = $wpdb->get_row($sql, 'ARRAY_A');

        return $result;
    }



    /**
     * Delete a customer record.
     *
     * @param int $id customer ID
     */
    public static function delete_reports( $id ) {
        global $wpdb;
        $wpdb->delete(
            "{$wpdb->prefix}aysquiz_reports",
            array( 'quiz_id' => $id ),
            array( '%d' )
        );
    }


    /**
     * Returns the count of records in the database.
     *
     * @return null|string
     */
    public static function record_count() {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_quizes";
        if(! empty( $_REQUEST['orderby'] ) &&  $_REQUEST['orderby'] == 'quiz_complete' ){
            $sql = "SELECT DISTINCT COUNT(id) FROM {$wpdb->prefix}aysquiz_quizes";
        }
        return $wpdb->get_var( $sql );
    }    
    
    public static function unread_records_count() {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_reports WHERE `read` = 0;";

        return $wpdb->get_var( $sql );
    }
    
    public static function record_complete_filter_count() {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM (SELECT DISTINCT `quiz_id` FROM {$wpdb->prefix}aysquiz_reports ) AS quiz_ids";

        return $wpdb->get_var( $sql );
    }



    /** Text displayed when no customer data is available */
    public function no_items() {
        echo __( 'There are no results yet.', $this->plugin_name );
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
            case 'quiz_title':
            case 'quiz_rate':
            case 'score':
            case 'user_count':
            case 'unreads':
            case 'id':
                return $item[ $column_name ];
                break;
            default:
                return print_r( $item, true ); //Show the whole array for troubleshooting purposes
        }
    }

    /**
     * Render the bulk edit checkbox
     *
     * @param array $item
     *
     * @return string
     */
    function column_cb( $item ) {
        return sprintf(
            '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
        );
    }


    /**
     * Method for name column
     *
     * @param array $item an array of DB data
     *
     * @return string
     */
    
    /**
     * Method for name column
     *
     * @param array $item an array of DB data
     *
     * @return string
     */
    function column_quiz_title( $item ) {
        global $wpdb;

        $delete_nonce = wp_create_nonce( $this->plugin_name . '-delete-result' );

        $result = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}aysquiz_quizes WHERE id={$item['id']}", "ARRAY_A");
        $restitle = Quiz_Maker_Admin::ays_restriction_string("word",stripcslashes($result['title']), 5);
        if($result == null){
            $title = __( "Quiz has been deleted", $this->plugin_name);

            $actions = array(                
                'delete' => sprintf( '<a class="ays_confirm_del" data-message="these reports" href="?page=%s&action=%s&result=%s&_wpnonce=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $delete_nonce )
            );
        }else{
            $title = sprintf( '<a href="?page=%s&quiz=%d">%s</a><input type="hidden" value="%d" class="ays_result_read">', $this->plugin_name."-each-result", absint( $item['id'] ), $restitle, absint( $item['id'] ));

            $actions = array(
                'view_details' => sprintf( '<a href="?page=%s&quiz=%d">%s</a>', $this->plugin_name."-each-result", absint( $item['id'] ), __('View details', $this->plugin_name)),
                'delete' => sprintf( '<a class="ays_confirm_del" data-message="%s" href="?page=%s&action=%s&result=%s&_wpnonce=%s">Delete</a>', $restitle."'s reports", esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $delete_nonce )
            );
        }

        return $title . $this->row_actions( $actions );
    }


    function column_quiz_rate( $item ) {
        global $wpdb;

        $sql = "SELECT AVG(`score`) AS avg_score FROM {$wpdb->prefix}aysquiz_rates WHERE quiz_id=".$item['id'];
        $result = $wpdb->get_var($sql);
        
        if($result == null){
            return;
        }
        
        $res = round($result,1);
        
        return $res;
    }
    
    function column_score($item){
        global $wpdb;

        $sql = "SELECT AVG(`score`) AS avg_score FROM {$wpdb->prefix}aysquiz_reports WHERE quiz_id=".$item['id'];
        $result = $wpdb->get_var($sql);
        
        if($result == null){
            return;
        }
        $res = round($result,2)."%";
        
        return $res;
    }

    function column_user_count($item) {
        global $wpdb;

        $sql = "SELECT COUNT(*) AS res_count
                FROM {$wpdb->prefix}aysquiz_reports
                WHERE quiz_id=" . $item['id'];

        $quiz = $wpdb->get_row($sql, 'ARRAY_A');

        return $quiz['res_count'];
    }

    function column_unreads($item) {
        global $wpdb;
        $sql = "SELECT COUNT(*)
                FROM `{$wpdb->prefix}aysquiz_reports`
                WHERE `read` = 0 AND `quiz_id` = ".$item['id'];
        $q = intval($wpdb->get_var($sql));
        if($q != 0){
            $q = "<p style='font-size:16px;font-weight:900;color:blue'>$q</p>
            <input type='hidden' class='ays_quiz_results_unreads' value='0'>";
        }
        return $q;
    }

    
    /**
     *  Associative array of columns
     *
     * @return array
     */
    function get_columns() {
        $columns = array(
            'cb'            => '<input type="checkbox" />',
            'quiz_title'    => __( 'Quiz', $this->plugin_name ),
            'quiz_rate'     => __( 'Average Rate', $this->plugin_name ),
            'score'         => __( 'Average Score', $this->plugin_name ),
            'user_count'    => __( 'Passed Users Count', $this->plugin_name ),
            'unreads'       => __( 'Unread results', $this->plugin_name ),
            'id'            => __( 'ID', $this->plugin_name ),
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
            'quiz_title' => array( 'title', true ),
            'id'         => array( 'id', true ),
        );

        return $sortable_columns;
    }

    /**
     * Returns an associative array containing the bulk action
     *
     * @return array
     */
    public function get_bulk_actions() {
        $actions = array(
            'bulk-delete' => 'Delete',
        );

        return $actions;
    }
    
    public static function get_quizzes_count_by_days($days,$quiz_id=0){
        global $wpdb;
        $today = current_time( 'mysql' );
        $given_date = date("Y-m-d h:i:s", strtotime("-$days day",strtotime( $today)));
        $difference_date = date("Y-m-d h:i:s", strtotime("-$days day",strtotime( $given_date)));

        $sql = "SELECT COUNT(*) AS `count` FROM {$wpdb->prefix}aysquiz_reports WHERE end_date >= '$given_date'";
        $difference_sql = "SELECT COUNT(*) AS `count` FROM {$wpdb->prefix}aysquiz_reports WHERE end_date >= '$difference_date' AND end_date <= '$given_date'";

        if($quiz_id !== 0){
            $sql .= "AND quiz_id = $quiz_id";
            $difference_sql .= "AND quiz_id = $quiz_id";
        }

        $given_dates_results = $wpdb->get_results($sql);
        $difference_date_results = $wpdb->get_results($difference_sql);

        $difference_quizzes_count = intval($difference_date_results[0]->count);
        $quizzes_count = intval($given_dates_results[0]->count);
        
        if($quizzes_count == 0){
            $difference = 0;
        }else{
            if($quizzes_count - $difference_quizzes_count == 0){
                $difference = 0;
            }else{
                $difference = round((($quizzes_count-$difference_quizzes_count)/$quizzes_count)*100);
            }
        }

        if(is_nan($difference)) $difference=0;
        $result = array(
            'difference'    =>$difference,
            'quizzes_count' =>$quizzes_count
        );
        return $result;
    }

    public function get_quizzes_for_chart(){
        global $wpdb;
        $quizzes = array();
        $two_quizzes = array();
        $sql = "SELECT {$wpdb->prefix}aysquiz_quizes.id FROM {$wpdb->prefix}aysquiz_quizes INNER JOIN {$wpdb->prefix}aysquiz_reports ON {$wpdb->prefix}aysquiz_quizes.id={$wpdb->prefix}aysquiz_reports.quiz_id";
        $results = $wpdb->get_results( $sql, 'ARRAY_A' );
        foreach ($results as $key=>$result){
            $results[$key] = $result['id'];
        }
        $results = 	array_count_values($results);
        arsort($results);


        foreach ($results as $key=>$result){
            array_push($quizzes,Quizes_List_Table::get_quiz_by_id($key ));
        }
        if( ! empty( $quizzes ) ) {
            $two_quizzes['most_popular'] = $quizzes[0];
            $two_quizzes['least_popular'] = $quizzes[count($quizzes)-1];
        }else{
            $two_quizzes['most_popular']['title'] = __( 'You don\'t have popular quizzes yet', $this->plugin_name );
            $two_quizzes['least_popular']['title'] = __( 'You don\'t have popular quizzes yet', $this->plugin_name );
        }
        return $two_quizzes;
    }

    /**
     * Handles data query and filter, sorting, and pagination.
     */
    public function prepare_items() {
            $this->_column_headers = $this->get_column_info();

            /** Process bulk action */
            $this->process_bulk_action();

            $per_page = $this->get_items_per_page('quiz_results_per_page', 20);

            $current_page = $this->get_pagenum();
            $total_items = self::record_count();
            if(! empty( $_REQUEST['orderby'] ) &&  $_REQUEST['orderby'] == 'quiz_complete' ){
                $total_items = self::record_complete_filter_count();
            }
            $this->set_pagination_args(array(
                'total_items' => $total_items, //WE have to calculate the total number of items
                'per_page' => $per_page //WE have to determine how many items to show on a page
            ));
            $this->items = self::get_reports( $per_page, $current_page );
    }

    public function process_bulk_action() {
        //Detect when a bulk action is being triggered...
        $message = 'deleted';
        if ( 'delete' === $this->current_action() ) {

            // In our file that handles the request, verify the nonce.
            $nonce = esc_attr( $_REQUEST['_wpnonce'] );

            if ( ! wp_verify_nonce( $nonce, $this->plugin_name . '-delete-result' ) ) {
                die( 'Go get a life script kiddies' );
            }
            else {
                self::delete_reports( absint( $_GET['result'] ) );

                // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
                // add_query_arg() return the current url

                $url = esc_url_raw( remove_query_arg(array('action', 'quiz', '_wpnonce') ) ) . '&status=' . $message;
                wp_redirect( $url );
            }

        }


        // If the delete bulk action is triggered
        if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
            || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
        ) {

            $delete_ids = esc_sql( $_POST['bulk-delete'] );

            // loop over the array of record IDs and delete them
            foreach ( $delete_ids as $id ) {
                self::delete_reports( $id );

            }

            // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
            // add_query_arg() return the current url

            $url = esc_url_raw( remove_query_arg(array('action', 'quiz', '_wpnonce') ) ) . '&status=' . $message;
            wp_redirect( $url );
        }
    }
    
    public function results_notices(){
        $status = (isset($_REQUEST['status'])) ? sanitize_text_field( $_REQUEST['status'] ) : '';

        if ( empty( $status ) )
            return;

        if ( 'created' == $status )
            $updated_message = esc_html( __( 'Quiz created.', $this->plugin_name ) );
        elseif ( 'updated' == $status )
            $updated_message = esc_html( __( 'Quiz saved.', $this->plugin_name ) );
        elseif ( 'deleted' == $status )
            $updated_message = esc_html( __( 'Quiz deleted.', $this->plugin_name ) );

        if ( empty( $updated_message ) )
            return;

        ?>
        <div class="notice notice-success is-dismissible">
            <p> <?php echo $updated_message; ?> </p>
        </div>
        <?php
    }
}
