<?php
class All_Reviews_List_Table extends WP_List_Table{
    private $plugin_name;
    private $title_length;
    /** Class constructor */
    public function __construct($plugin_name) {
        $this->plugin_name = $plugin_name;
        parent::__construct( array(
            'singular' => __( 'Review', $this->plugin_name ), //singular name of the listed records
            'plural'   => __( 'Reviews', $this->plugin_name ), //plural name of the listed records
            'ajax'     => false //does this table support ajax?
        ) );
        $this->title_length = Quiz_Maker_Data::get_listtables_title_length('quiz_reviews');

        add_action( 'admin_notices', array( $this, 'reviews_notices' ) );

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
        // $titles_sql = "SELECT {$wpdb->prefix}aysquiz_quizes.title,
        //                       {$wpdb->prefix}aysquiz_quizes.id
        //                FROM {$wpdb->prefix}aysquiz_quizes";
        // $quiz_titles = $wpdb->get_results($titles_sql);

        // $users_sql = "SELECT {$wpdb->prefix}aysquiz_rates.user_id
        //               FROM {$wpdb->prefix}aysquiz_rates
        //               GROUP BY user_id";
        // $users = $wpdb->get_results($users_sql);
        // $quiz_id = null;
        // $user_id = null;
        // if( isset( $_GET['filterby'] )){
        //     $quiz_id = intval($_GET['filterby']);
        // }
        // if( isset( $_GET['filterbyuser'] )){
        //     $user_id = intval($_GET['filterbyuser']);
        // }

        $quiz_reviews = array(
            "1" => 1,
            "2" => 2,
            "3" => 3,
            "4" => 4,
            "5" => 5,
        );

        $review_key = null;

        if( isset( $_GET['filterbyreview'] )){
            $review_key = absint( sanitize_text_field( $_GET['filterbyreview'] ) );
        }

        $quiz_comments = array(
            "with_answer"   => __("With reviews", $this->plugin_name),
            "without_answer" => __("Without reviews", $this->plugin_name),
        );

        $comment_key = null;

        if( isset( $_GET['filterbycomment'] )){
            $comment_key = sanitize_text_field( $_GET['filterbycomment'] );
        }

        ?>
        <div id="quiz-filter-div-<?php echo $which; ?>" class="alignleft actions bulkactions ays-quiz-review-filter-main-div">
            <select name="filterbyreview-<?php echo esc_attr( $which ); ?>" id="bulk-action-quiz-rate-selector-<?php echo esc_attr( $which ); ?>">
                <option value=""><?php echo __('Select Rate',$this->plugin_name)?></option>
                <?php
                    foreach($quiz_reviews as $key => $review) {
                        $selected = "";
                        if( $review_key === absint($review) ) {
                            $selected = "selected";
                        }
                        echo "<option ".$selected." value='".esc_attr( $key )."'>".$review."</option>";
                    }
                ?>
            </select>

            <select name="filterbycomment-<?php echo esc_attr( $which ); ?>" id="bulk-action-quiz-rate-selector-<?php echo esc_attr( $which ); ?>">
                <option value=""><?php echo __('With/without reviews',$this->plugin_name); ?></option>
                <?php
                    foreach($quiz_comments as $key => $quiz_comment) {
                        $selected = "";
                        if( $comment_key === sanitize_text_field($key) ) {
                            $selected = "selected";
                        }
                        echo "<option ".$selected." value='".esc_attr( $key )."'>".$quiz_comment."</option>";
                    }
                ?>
            </select>

            <input type="button" id="doaction-quiz-<?php echo esc_attr( $which ); ?>" class="ays-quiz-question-tab-all-filter-button-<?php echo esc_attr( $which ); ?> button" value="<?php echo __( "Filter", $this->plugin_name ); ?>">
            
            <a style="display:inline-block;" href="?page=<?php echo $_REQUEST['page'] ?>&quiz=<?php echo $_REQUEST['quiz'] ?>" class="button"><?php echo __( "Clear filters", $this->plugin_name ); ?></a>
        </div>
        <?php
    }

    protected function get_views() {
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
    public static function get_reviews( $per_page = 50, $page_number = 1 ) {

        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_rates";

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
        }
        else{
            $sql .= ' ORDER BY rate_date DESC';
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

        $search = ( isset( $_REQUEST['s'] ) ) ? $_REQUEST['s'] : false;
        if( $search ){
            $s = array();
            $s[] = sprintf( " `id` LIKE '%%%s%%' ", esc_sql( $wpdb->esc_like( $search ) ) );
            $s[] = sprintf( " `user_name` LIKE '%%%s%%' ", esc_sql( $wpdb->esc_like( $search ) ) );
            $s[] = sprintf( " `user_email` LIKE '%%%s%%' ", esc_sql( $wpdb->esc_like( $search ) ) );
            $s[] = sprintf( " `user_id` LIKE '%%%s%%' ", esc_sql( $wpdb->esc_like( $search ) ) );
            $s[] = sprintf( " `review` LIKE '%%%s%%' ", esc_sql( $wpdb->esc_like( $search ) ) );

            $args = 'search=';
            if($search !== null){
                $args .= $search;
                $args .= '*';
            }

            $users = get_users($args);
            $user_ids_arr = array();

            foreach ($users as $key => $value) {
                $user_ids_arr[] = $value->ID;
            }

            if (! empty($user_ids_arr)) {
                $user_ids = implode(',', $user_ids_arr);

                $s[] = ' `user_id` in ('. $user_ids .')';
            }

            $where[] = ' ( ' . implode(' OR ', $s) . ' ) ';
        }

        if( isset( $_REQUEST['quiz'] ) && sanitize_text_field( $_REQUEST['quiz'] ) > 0){
            $quiz_id = intval( sanitize_text_field( $_REQUEST['quiz'] ) );
            $where[] = ' `quiz_id` = '. $quiz_id .' ';
        }

        if( isset( $_REQUEST['filterbyreview'] ) && absint( sanitize_text_field( $_REQUEST['filterbyreview'] ) ) > 0){
            $review_key = absint( sanitize_text_field( $_REQUEST['filterbyreview'] ) );
            $where[] = ' `score` = '. $review_key .' ';
        }

        if( isset( $_REQUEST['filterbycomment'] ) && sanitize_text_field( $_REQUEST['filterbycomment'] ) != ""){
            $comment_key = sanitize_text_field( $_REQUEST['filterbycomment'] );

            switch ( $comment_key ) {
                case 'with_answer':
                    $where[] = ' `review` != "" ';
                    break;
                case 'without_answer':
                default:
                    $where[] = ' `review` = "" ';
                    break;
            }
        }

        if( ! empty($where) ){
            $sql = " WHERE " . implode( " AND ", $where );
        }
        return $sql;
    }

    public function get_review_by_id( $id ){
        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_rates WHERE id=" . absint( intval( $id ) );

        $result = $wpdb->get_row($sql, 'ARRAY_A');

        return $result;
    }


    /**
     * Delete a customer record.
     *
     * @param int $id customer ID
     */
    public static function delete_reviews( $id ) {
        global $wpdb;
        $wpdb->delete(
            "{$wpdb->prefix}aysquiz_rates",
            array( 'id' => $id ),
            array( '%d' )
        );
    }

    /**
     * Delete a customer review only.
     *
     * @param int $id customer ID
     */
    public static function delete_only_reviews( $id ) {
        global $wpdb;

        $rates_table  = esc_sql( $wpdb->prefix . "aysquiz_rates" );

        $id = ( isset( $id ) && $id != '' ) ? absint( sanitize_text_field ( $id ) ) : null;

        if ( ! is_null( $id ) && $id > 0 ) {
            $rates_result = $wpdb->update(
                $rates_table,
                array(
                    'review' => "",

                ),
                array( 'id' => $id ),
                array( '%s' ),
                array( '%d' )
            );
        }
    }


    /**
     * Returns the count of records in the database.
     *
     * @return null|string
     */
    public static function record_count() {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_rates";
        $sql .= self::get_where_condition();
        return $wpdb->get_var( $sql );
    }

    public static function all_record_count() {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_rates";
        $sql .= self::get_where_condition();

        return $wpdb->get_var( $sql );
    }


    /** Text displayed when no customer data is available */
    public function no_items() {
        echo __( 'There are no reviews yet.', $this->plugin_name );
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
            case 'user_id':
            case 'user_ip':
            case 'user_name':
            case 'user_email':
            case 'rate_date':
            case 'id':
                return $item[ $column_name ];
                break;
            case 'score':
                return $item[ $column_name ];
                break;
            case 'review':
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
            '<input type="checkbox" class="ays_result_delete" name="bulk-delete[]" value="%s" />', $item['id']
        );
    }


    /**
     * Method for name column
     *
     * @param array $item an array of DB data
     *
     * @return string
     */
    function column_user_id( $item ) {
        $delete_nonce = wp_create_nonce( $this->plugin_name . '-delete-result' );
        $user_id = intval($item['user_id']);
        if($user_id == 0){
            $name = "Guest";
        }else{
            $name = '';
            $user = get_userdata($user_id);
            if ($user !== false) {
                $name = $user->data->display_name;
            }
        }

        $actions = array(
            'delete' => sprintf( '<a class="ays_confirm_del" data-message="this review" href="?page=%s&quiz=%s&action=%s&result=%s&_wpnonce=%s">%s</a>', esc_attr( $_REQUEST['page'] ),esc_attr( $_REQUEST['quiz'] ), 'delete', absint( $item['id'] ), $delete_nonce, __( "Delete", $this->plugin_name ) )
        );

        return $name . $this->row_actions( $actions );

    }


    function isJSON($string){
       return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
    }


    function ays_get_average_of_rates($id){
        global $wpdb;
        $sql = "SELECT AVG(`score`) AS avg_score FROM {$wpdb->prefix}aysquiz_rates WHERE quiz_id= $id";
        $result = $wpdb->get_var($sql);
        return $result;
    }

    /**
     * Method for name column
     *
     * @param array $item an array of DB data
     *
     * @return string
     */
    function column_quiz_id( $item ) {
        $quiz_id = intval( sanitize_text_field( $item['quiz_id'] ) );
        $quiz = Quiz_Maker_Data::get_quiz_by_id( $quiz_id );

        $quiz_title = (isset( $quiz['title'] ) && $quiz['title'] != "") ? sanitize_text_field( $quiz['title'] ) : "";

        $result = "<span>". $quiz_title ."<span>";
        if ( $quiz_title != "" ) {
            $result = sprintf( '<a href="?page=%s&action=edit&quiz=%d" target="_blank">%s</a>', 'quiz-maker', $quiz_id, $quiz_title );
        }

        return $result;
    }

    /**
     * Method for name column
     *
     * @param array $item an array of DB data
     *
     * @return string
     */
    function column_review( $item ) {

        $column_t = (isset( $item['review'] ) && $item['review'] != '') ? stripcslashes( nl2br( trim($item['review']) ) ) : '';
        $t = esc_attr($column_t);

        $review_title_length = intval( $this->title_length );

        $restitle = Quiz_Maker_Admin::ays_restriction_string("word", $column_t, $review_title_length);

        $title = sprintf( '<span title="%s">%s</span>', $t, $restitle );

        return $title;
    }

    /**
     *  Associative array of columns
     *
     * @return array
     */
    function get_columns() {
        $columns = array(
            'cb'          => '<input type="checkbox" />',
            'user_id'     => __( 'WP User', $this->plugin_name ),
            'quiz_id'     => __( 'Quiz', $this->plugin_name ),
            'user_ip'     => __( 'User IP', $this->plugin_name ),
            'user_name'   => __( 'Name', $this->plugin_name ),
            'user_email'  => __( 'Email', $this->plugin_name ),
            'rate_date'   => __( 'Rate Date', $this->plugin_name ),
            'score'       => __( 'Rate', $this->plugin_name ),
            'review'      => __( 'Review', $this->plugin_name ),
            'id'          => __( 'ID', $this->plugin_name ),
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
            'user_id'       => array( 'user_id', true ),
            'user_ip'       => array( 'user_ip', true ),
            'rate_date'     => array( 'rate_date', true ),
            'score'         => array( 'score', true ),
            'user_name'     => array( 'user_name', true ),
            'user_email'    => array( 'user_email', true ),
            'id'            => array( 'id', true ),
        );

        return $sortable_columns;
    }

    /**
     * Columns to make sortable.
     *
     * @return array
     */

    /**
     * Returns an associative array containing the bulk action
     *
     * @return array
     */
    public function get_bulk_actions() {
        $actions = array(
            'bulk-delete' => __('Delete', $this->plugin_name),
            'bulk-delete-review' => __('Delete only review', $this->plugin_name),
        );

        return $actions;
    }


    /**
     * Handles data query and filter, sorting, and pagination.
     */
    public function prepare_items() {

        $this->_column_headers = $this->get_column_info();

        /** Process bulk action */
        $this->process_bulk_action();

        $per_page     = $this->get_items_per_page( 'quiz_all_reviews_per_page', 50 );

        $current_page = $this->get_pagenum();
        $total_items  = self::record_count();

        $this->set_pagination_args( array(
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page'    => $per_page //WE have to determine how many items to show on a page
        ) );

        $this->items = self::get_reviews( $per_page, $current_page );
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
                self::delete_reviews( absint( $_GET['result'] ) );

                // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
                // add_query_arg() return the current url

                $url = esc_url_raw( remove_query_arg(array('action', 'result', '_wpnonce')  ) ) . '&status=' . $message;
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
                self::delete_reviews( $id );

            }

            // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
            // add_query_arg() return the current url

            $url = esc_url_raw( remove_query_arg(array('action', 'result', '_wpnonce')  ) ) . '&status=' . $message;
            wp_redirect( $url );
        } elseif ((isset($_POST['action']) && $_POST['action'] == 'bulk-delete-review')
                  || (isset($_POST['action2']) && $_POST['action2'] == 'bulk-delete-review')
        ) {

            $review_ids = ( isset( $_POST['bulk-delete'] ) && ! empty( $_POST['bulk-delete'] ) ) ? esc_sql( $_POST['bulk-delete'] ) : array();

            // loop over the array of record IDs and mark as read them

            foreach ( $review_ids as $id ) {
                self::delete_only_reviews( $id );
            }

            // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
            // add_query_arg() return the current url
            $url = esc_url_raw( remove_query_arg(array('action', 'result', '_wpnonce')  ) ) . '&status=' . $message;
            wp_redirect( $url );
        }
    }

    public function reviews_notices(){
        $status = (isset($_REQUEST['status'])) ? sanitize_text_field( $_REQUEST['status'] ) : '';

        if ( empty( $status ) )
            return;

        if ( 'created' == $status )
            $updated_message = esc_html( __( 'Quiz created.', $this->plugin_name ) );
        elseif ( 'deleted' == $status )
            $updated_message = esc_html( __( 'Review(s) deleted.', $this->plugin_name ) );

        if ( empty( $updated_message ) )
            return;

        ?>
        <div class="notice notice-success is-dismissible">
            <p> <?php echo $updated_message; ?> </p>
        </div>
        <?php
    }
}
