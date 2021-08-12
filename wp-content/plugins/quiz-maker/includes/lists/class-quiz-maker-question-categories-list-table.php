<?php
class Question_Categories_List_Table extends WP_List_Table{
    private $plugin_name;
    /** Class constructor */
    public function __construct($plugin_name) {
        $this->plugin_name = $plugin_name;
        parent::__construct( array(
            'singular' => __( 'Question Category', $this->plugin_name ), //singular name of the listed records
            'plural'   => __( 'Question Categories', $this->plugin_name ), //plural name of the listed records
            'ajax'     => false //does this table support ajax?
        ) );
        add_action( 'admin_notices', array( $this, 'question_category_notices' ) );
    }

    
    protected function get_views() {
        $published_count = $this->published_question_categories_count();
        $unpublished_count = $this->unpublished_question_categories_count();
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
            "published" => "<a ".$selected_1." href='?page=".esc_attr( $_REQUEST['page'] )."&fstatus=1'>". __( 'Published', $this->plugin_name )." (".$published_count.")</a>",
            "unpublished"   => "<a ".$selected_0." href='?page=".esc_attr( $_REQUEST['page'] )."&fstatus=0'>". __( 'Unpublished', $this->plugin_name )." (".$unpublished_count.")</a>"
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
    public static function get_question_categories( $per_page = 20, $page_number = 1, $search = '' ) {

        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_categories";

        $where = array();

        if( $search != '' ){
            $where[] = $search;
        }

        if( isset( $_REQUEST['fstatus'] ) ){
            $fstatus = $_REQUEST['fstatus'];
            if($fstatus !== null){
                $where[] = " published = ".$fstatus." ";
            }
        }

        if( ! empty($where) ){
            $sql .= " WHERE " . implode( " AND ", $where );
        }

        if ( ! empty( $_REQUEST['orderby'] ) ) {
            $sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
            $sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' DESC';
        }else{
            $sql .= ' ORDER BY id DESC';
        }

        $sql .= " LIMIT $per_page";
        $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;


        $result = $wpdb->get_results( $sql, 'ARRAY_A' );

        return $result;
    }

    public function get_question_category( $id ) {
        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_categories WHERE id=" . absint( intval( $id ) );

        $result = $wpdb->get_row($sql, 'ARRAY_A');

        return $result;
    }

    public function add_edit_question_category( $data ){
        global $wpdb;
        $question_category_table = $wpdb->prefix . 'aysquiz_categories';
        $ays_change_type = (isset($data['ays_change_type']))?$data['ays_change_type']:'';

        if( isset($data["question_category_action"]) && wp_verify_nonce( $data["question_category_action"],'question_category_action' ) ){
            $id = absint( intval( $data['id'] ) );
            $title = stripslashes( sanitize_text_field( $data['ays_title'] ) );
            $title = trim( $title );

            if( empty( $title ) ){
                $url = esc_url_raw( remove_query_arg( array('status') ) ) . '&status=failed';
                wp_redirect( $url );
                exit();
            }

            $description =  stripslashes($data['ays_description'] );
            $publish = absint( intval( $data['ays_publish'] ) );
            $message = '';
            if( $id == 0 ){
                $result = $wpdb->insert(
                    $question_category_table,
                    array(
                        'title'         => $title,
                        'description'   => $description,
                        'published'     => $publish
                    ),
                    array( '%s', '%s', '%d' )
                );
                $message = 'created';
                $question_category_insert_id = $wpdb->insert_id;
            }else{
                $result = $wpdb->update(
                    $question_category_table,
                    array(
                        'title'         => $title,
                        'description'   => $description,
                        'published'     => $publish
                    ),
                    array( 'id' => $id ),
                    array( '%s', '%s', '%d' ),
                    array( '%d' )
                );
                $message = 'updated';
            }

            if( $result >= 0  ) {
                if($ays_change_type != ''){
                    if($id == null){
                        $url = esc_url_raw( add_query_arg( array(
                            "action"    => "edit",
                            "question_category" => $question_category_insert_id,
                            "status"    => $message
                        ) ) );
                    }else{
                        $url = esc_url_raw( remove_query_arg(false) ) . '&status=' . $message;
                    }
                    wp_redirect( $url );
                }else{
                    $url = esc_url_raw( remove_query_arg( array('action', 'question_category') ) ) . '&status=' . $message;
                    wp_redirect( $url );
                }
            }
        }
    }

    /**
     * Delete a customer record.
     *
     * @param int $id customer ID
     */
    public static function delete_question_categories( $id ) {
        global $wpdb;
        $wpdb->delete(
            "{$wpdb->prefix}aysquiz_categories",
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

        $filter = array();
        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_categories";

        if( isset( $_REQUEST['fstatus'] ) ){
            $fstatus = $_REQUEST['fstatus'];
            if($fstatus !== null){
                $filter[] = " published = ".$fstatus." ";
            }
        }

        $search = ( isset( $_REQUEST['s'] ) ) ? $_REQUEST['s'] : false;
        if( $search ){
            $filter[] = sprintf(" name LIKE '%%%s%%' ", $search );
        }

        if(count($filter) !== 0){
            $sql .= " WHERE ".implode(" AND ", $filter);
        }

        return $wpdb->get_var( $sql );
    }

    public static function all_record_count() {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_categories";

        $filter = array();

        $search = ( isset( $_REQUEST['s'] ) ) ? $_REQUEST['s'] : false;
        if( $search ){
            $filter[] = sprintf(" title LIKE '%%%s%%' ", $search );
        }

        if(count($filter) !== 0){
            $sql .= " WHERE ".implode(" AND ", $filter);
        }

        return $wpdb->get_var( $sql );
    }

    public static function published_question_categories_count() {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_categories WHERE published=1";

        return $wpdb->get_var( $sql );
    }
    
    public static function unpublished_question_categories_count() {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_categories WHERE published=0";

        return $wpdb->get_var( $sql );
    }

    /** Text displayed when no customer data is available */
    public function no_items() {
        
        if(intval($item['id']) === 1){
            return;
        }
        
        echo __( 'There are no question categories yet.', $this->plugin_name );
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
            case 'title':
            case 'description':
                return Quiz_Maker_Admin::ays_restriction_string("word",($item[ $column_name ]), 5);
                break;
            case 'items_count':
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
        
        if(intval($item['id']) === 1){
            return;
        }
        
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
    function column_title( $item ) {
        $delete_nonce = wp_create_nonce( $this->plugin_name . '-delete-question-category' );

        $restitle = Quiz_Maker_Admin::ays_restriction_string("word",stripcslashes($item['title']), 5);
        
        $title = sprintf( '<a href="?page=%s&action=%s&question_category=%d"><strong>%s</strong></a>', esc_attr( $_REQUEST['page'] ), 'edit', absint( $item['id'] ), $restitle );

        $actions = array(
            'edit' => sprintf( '<a href="?page=%s&action=%s&question_category=%d">'. __('Edit', $this->plugin_name) .'</a>', esc_attr( $_REQUEST['page'] ), 'edit', absint( $item['id'] ) ),
        );
        
        if(intval($item['id']) !== 1){
            $actions['delete'] = sprintf( '<a class="ays_confirm_del" data-message="%s" href="?page=%s&action=%s&question_category=%s&_wpnonce=%s">'. __('Delete', $this->plugin_name) .'</a>', $restitle, esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $delete_nonce );
        }

        return $title . $this->row_actions( $actions );
    }

    function column_published( $item ) {
        switch( $item['published'] ) {
            case "1":
                return '<span class="ays-publish-status"><i class="ays_fa ays_fa_check_square_o" aria-hidden="true"></i>'. __('Published',$this->plugin_name) . '</span>';
                break;
            case "0":
                return '<span class="ays-publish-status"><i class="ays_fa ays_fa_square_o" aria-hidden="true"></i>'. __('Unublished',$this->plugin_name) . '</span>';
                break;
        }
    }

    function column_items_count( $item ) {
        global $wpdb;
        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_questions WHERE category_id = " . $item['id'];
        $result = $wpdb->get_var($sql);
        return "<p style='text-align:center;font-size:14px;'>" . $result . "</p>";
    }


    /**
     *  Associative array of columns
     *
     * @return array
     */
    function get_columns() {
        $columns = array(
            'cb'            => '<input type="checkbox" />',
            'title'         => __( 'Title', $this->plugin_name ),
            'description'   => __( 'Description', $this->plugin_name ),
            'items_count'   => __( 'Questions Count', $this->plugin_name ),
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
            'title'         => array( 'title', true ),
            'id'            => array( 'id', true ),
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
            'bulk-delete' => __('Delete', $this->plugin_name)
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

        $per_page     = $this->get_items_per_page( 'question_categories_per_page', 20 );
        $current_page = $this->get_pagenum();
        $total_items  = self::record_count();

        $search = ( isset( $_REQUEST['s'] ) ) ? $_REQUEST['s'] : false;

        $do_search = ( $search ) ? sprintf(" title LIKE '%%%s%%' ", $search ) : '';

        $this->set_pagination_args( array(
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page'    => $per_page //WE have to determine how many items to show on a page
        ) );

        $this->items = self::get_question_categories( $per_page, $current_page, $do_search );
    }

    public function process_bulk_action() {
        //Detect when a bulk action is being triggered...
        if ( 'delete' === $this->current_action() ) {

            // In our file that handles the request, verify the nonce.
            $nonce = esc_attr( $_REQUEST['_wpnonce'] );

            if ( ! wp_verify_nonce( $nonce, $this->plugin_name . '-delete-question-category' ) ) {
                die( 'Go get a life script kiddies' );
            }
            else {
                self::delete_question_categories( absint( $_GET['question_category'] ) );

                // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
                // add_query_arg() return the current url

                $url = esc_url_raw( remove_query_arg( array('action', 'question_category', '_wpnonce') ) ) . '&status=deleted';
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
                self::delete_question_categories( $id );

            }

            // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
            // add_query_arg() return the current url
            $url = esc_url_raw( remove_query_arg( array('action', 'question_category', '_wpnonce') ) ) . '&status=deleted';
            wp_redirect( $url );
        }
    }

    public function question_category_notices(){
        $status = (isset($_REQUEST['status'])) ? sanitize_text_field( $_REQUEST['status'] ) : '';

        if ( empty( $status ) )
            return;

        if ( 'created' == $status ){
            $updated_message = esc_html( __( 'Question category created.', $this->plugin_name ) );
        }elseif ( 'updated' == $status ){
            $updated_message = esc_html( __( 'Question category saved.', $this->plugin_name ) );
        }elseif ( 'deleted' == $status ){
            $updated_message = esc_html( __( 'Question category deleted.', $this->plugin_name ) );
        }elseif ( 'failed' == $status ){
            $updated_message = esc_html( __( 'Error: You must fill out the title field.', $this->plugin_name ) );
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
