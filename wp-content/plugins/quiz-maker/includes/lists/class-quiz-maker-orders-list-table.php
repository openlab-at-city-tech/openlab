<?php
class Quiz_Orders_List_Table extends WP_List_Table {
    private $plugin_name;
    /** Class constructor */
    public function __construct($plugin_name) {
        $this->plugin_name = $plugin_name;
        parent::__construct( array(
            'singular' => __( 'Order', $this->plugin_name ), //singular name of the listed records
            'plural'   => __( 'Orders', $this->plugin_name ), //plural name of the listed records
            'ajax'     => false //does this table support ajax?
        ) );
        add_action( 'admin_notices', array( $this, 'orders_notices' ) );

    }


    /**
     * Retrieve customers data from the database
     *
     * @param int $per_page
     * @param int $page_number
     *
     * @return mixed
     */
    public static function get_orders( $per_page = 20, $page_number = 1 ) {

        global $wpdb;

        $current_user = get_current_user_id();
        $current_user_can_edit = Quiz_Maker_Data::quiz_maker_capabilities_for_editing();
        
        $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_orders";

        if( ! $current_user_can_edit ){
            $sql = "SELECT o.* 
                FROM {$wpdb->prefix}aysquiz_orders AS o
                LEFT JOIN {$wpdb->prefix}aysquiz_quizes AS q
                    ON o.quiz_id = q.id
                WHERE q.author_id = {$current_user} 
                ";
        }

        if ( ! empty( $_REQUEST['orderby'] ) ) {

            $order_by = ( isset( $_REQUEST['orderby'] ) && sanitize_text_field( $_REQUEST['orderby'] ) != '' ) ? sanitize_text_field( $_REQUEST['orderby'] ) : 'id';
            $order_by .= ( ! empty( $_REQUEST['order'] ) && strtolower( $_REQUEST['order'] ) == 'asc' ) ? ' ASC' : ' DESC';

            $sql_orderby = sanitize_sql_orderby($order_by);

            if ( $sql_orderby ) {
                if( ! $current_user_can_edit ){
                    $sql .= ' ORDER BY o.' . $sql_orderby;
                }else{
                    $sql .= ' ORDER BY ' . $sql_orderby;
                }
            } else {
                if( ! $current_user_can_edit ){
                    $sql .= ' ORDER BY o.id DESC';
                }else{
                    $sql .= ' ORDER BY id DESC';
                }
            }
        }else{
            if( ! $current_user_can_edit ){
                $sql .= ' ORDER BY o.id DESC';
            }else{
                $sql .= ' ORDER BY id DESC';
            }
        }
        
        $sql .= " LIMIT $per_page";
        $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;

        $result = $wpdb->get_results( $sql, 'ARRAY_A' );

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
            "{$wpdb->prefix}aysquiz_orders",
            array( 'id' => $id ),
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

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_orders";

        $current_user = get_current_user_id();
        
        
        if( ! Quiz_Maker_Data::quiz_maker_capabilities_for_editing() ){
            $sql = "SELECT COUNT(*) 
                    FROM {$wpdb->prefix}aysquiz_orders AS o
                    LEFT JOIN {$wpdb->prefix}aysquiz_quizes AS q
                        ON o.quiz_id = q.id
                    WHERE q.author_id = {$current_user}";
        }
        
        return $wpdb->get_var( $sql );
    }

    /** Text displayed when no customer data is available */
    public function no_items() {
        echo __( 'There are no orders yet.', $this->plugin_name );
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
            case 'order_id':
            case 'quiz_id':
            case 'user_id':
            case 'order_full_name':
            case 'order_email':
            case 'amount':
            case 'payment_date':
            case 'payment_method':
            case 'payment_type':
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
    function column_quiz_id( $item ) {
        global $wpdb;

        $delete_nonce = wp_create_nonce( $this->plugin_name . '-delete-result' );

        $result = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}aysquiz_quizes WHERE id={$item['quiz_id']}", "ARRAY_A");

        $result_title = (  isset( $result ) && isset( $result['title'] ) && esc_attr( $result['title'] ) != "") ? stripcslashes( esc_attr( $result['title'] ) ) : "";

        $title = Quiz_Maker_Admin::ays_restriction_string("word",$result_title, 5);
        
        return $title;
    }

    /**
     * Method for name column
     *
     * @param array $item an array of DB data
     *
     * @return string
     */
    function column_user_id( $item ) {
        global $wpdb;
        if($item['user_id'] == 0){
            $title = __( "Guest", $this->plugin_name );
        }else{
            $user = get_userdata($item['user_id']);
            if($user !== false){
                $title = $user->data->display_name;
            }else{
                $title = '-';
            }

        }
        
        return $title;
    }

    /**
     * Method for amount column
     *
     * @param array $item an array of DB data
     *
     * @return string
     */
//    function column_amount( $item ) {
//        global $wpdb;
//        $title = '';
//        $item['type'] = isset( $item['type'] ) && $item['type'] != '' ? $item['type'] : 'paypal';
//        if( $item['type'] == 'stripe' ){
//            $title = "Stripe";
//        }elseif( $item['type'] == 'paypal' ){
//            $title = "PayPal";
//        }
//        return $title;
//    }

    /**
     * Method for payment method column
     *
     * @param array $item an array of DB data
     *
     * @return string
     */
    function column_payment_method( $item ) {
        global $wpdb;
        $title = '';
        $item['type'] = isset( $item['type'] ) && $item['type'] != '' ? $item['type'] : 'paypal';
        if( $item['type'] == 'stripe' ){
            $title = "Stripe";
        }elseif( $item['type'] == 'paypal' ){
            $title = "PayPal";
        }
        return $title;
    }

    function column_payment_type( $item ) {
        return ucfirst( $item['payment_type'] );
    }


    /**
     *  Associative array of columns
     *
     * @return array
     */
    function get_columns() {
        $columns = array(
            'cb'                    => '<input type="checkbox" />',
            'order_id'              => __( 'Order ID', $this->plugin_name ),
            'quiz_id'               => __( 'Quiz', $this->plugin_name ),
            'user_id'               => __( 'WP User', $this->plugin_name ),
            'order_full_name'       => __( 'Full Name', $this->plugin_name ),
            'order_email'           => __( 'Email', $this->plugin_name ),
            'amount'                => __( 'Amount', $this->plugin_name ),
            'payment_date'          => __( 'Payment date', $this->plugin_name ),
            'payment_method'        => __( 'Payment Method', $this->plugin_name ),
            'payment_type'          => __( 'Payment type', $this->plugin_name ),
            'id'                    => __( 'ID', $this->plugin_name ),
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
            'quiz_id'           => array( 'quiz_id', true ),
            'user_id'           => array( 'user_id', true ),
            'order_full_name'   => array( 'order_full_name', true ),
            'order_email'       => array( 'order_email', true ),
            'amount'            => array( 'amount', true ),
            'payment_date'      => array( 'payment_date', true ),
            'payment_method'    => array( 'type', true ),
            'payment_type'      => array( 'payment_type', true ),
            'id'                => array( 'id', true ),
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
            'bulk-delete' => __( 'Delete', $this->plugin_name )
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

            $per_page = $this->get_items_per_page('quiz_orders_per_page', 20);

            $current_page = $this->get_pagenum();
            $total_items = self::record_count();
            $this->set_pagination_args(array(
                'total_items' => $total_items, //WE have to calculate the total number of items
                'per_page' => $per_page //WE have to determine how many items to show on a page
            ));
            $this->items = self::get_orders( $per_page, $current_page );
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

                $url = esc_url_raw( remove_query_arg(array('action', 'order', '_wpnonce') ) ) . '&status=' . $message;
                wp_redirect( $url );
            }

        }elseif('see-all' === $this->current_action()){
            $this->ays_see_all_results();
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

            $url = esc_url_raw( remove_query_arg(array('action', 'order', '_wpnonce') ) ) . '&status=' . $message;
            wp_redirect( $url );
        }
    }
    
    public function orders_notices(){
        $status = (isset($_REQUEST['status'])) ? sanitize_text_field( $_REQUEST['status'] ) : '';

        if ( empty( $status ) )
            return;

        if ( 'created' == $status )
            $updated_message = esc_html( __( 'Order created.', $this->plugin_name ) );
        elseif ( 'updated' == $status )
            $updated_message = esc_html( __( 'Order saved.', $this->plugin_name ) );
        elseif ( 'deleted' == $status )
            $updated_message = esc_html( __( 'Order deleted.', $this->plugin_name ) );

        if ( empty( $updated_message ) )
            return;

        ?>
        <div class="notice notice-success is-dismissible">
            <p> <?php echo $updated_message; ?> </p>
        </div>
        <?php
    }
}
