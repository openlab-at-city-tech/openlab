<?php
class Question_Tags_List_Table extends WP_List_Table{

    private $plugin_name;
    protected $current_user_can_edit;

    /** Class constructor */

    public function __construct($plugin_name) {

        $this->plugin_name = $plugin_name;
        $this->current_user_can_edit = Quiz_Maker_Data::quiz_maker_capabilities_for_editing();

        parent::__construct( array(
            'singular' => __( 'Question tag', $this->plugin_name ), //singular name of the listed records
            'plural'   => __( 'Question tags', $this->plugin_name ), //plural name of the listed records
            'ajax'     => false //does this table support ajax?
        ) );

        add_action( 'admin_notices', array( $this, 'question_tag_notices' ) );
    }


    protected function get_views() {

        $published_count = $this->published_question_tags_count();
        $unpublished_count = $this->unpublished_question_tags_count();
        $all_count = $this->all_record_count();
        $selected_all = "";
        $selected_0 = "";
        $selected_1 = "";

        if(isset($_GET['fstatus'])){
            switch($_GET['fstatus']){
                case "unpublished":
                    $selected_0 = " style='font-weight:bold;' ";
                    break;
                case "published":
                    $selected_1 = " style='font-weight:bold;' ";
                    break;
                default:
                    $selected_all = " style='font-weight:bold;' ";
                    break;
            }
        }else{
            $selected_all = " style='font-weight:bold;' ";
        }

        $admin_url = get_admin_url( null, 'admin.php' );
        $get_properties = http_build_query($_GET);

        $status_links_url = $admin_url . "?" . $get_properties;
        $publish_url = esc_url( add_query_arg('fstatus', 'published', $status_links_url) );
        $unpublish_url = esc_url( add_query_arg('fstatus', 'unpublished', $status_links_url) );

        $status_links = array(
            "all" => "<a ".$selected_all." href='?page=".esc_attr( $_REQUEST['page'] )."'>". __( 'All', $this->plugin_name )." (".$all_count.")</a>",
            "published" => "<a ".$selected_1." href='". $publish_url ."'>". __( 'Published', $this->plugin_name )." (".$published_count.")</a>",
            "unpublished"   => "<a ".$selected_0." href='". $unpublish_url ."'>". __( 'Unpublished', $this->plugin_name )." (".$unpublished_count.")</a>"
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
    public static function get_question_tags( $per_page = 20, $page_number = 1, $search = '' ) {

        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_question_tags";

        $where = array();

        if( $search != '' ){
            $where[] = $search;
        }

        if( isset( $_REQUEST['fstatus'] ) ){
            $fstatus = $_REQUEST['fstatus'];
            if($fstatus !== null){
                $where[] = " status = '".$fstatus."' ";
            }
        }

        if( ! empty($where) ){
            $sql .= " WHERE " . implode( " AND ", $where );
        }

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

    public function get_question_tag( $id ) {

        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_question_tags WHERE id=" . absint( intval( $id ) );

        $result = $wpdb->get_row($sql, 'ARRAY_A');

        return $result;

    }

    public function add_edit_question_tag(){

        global $wpdb;

        $question_tag_table = $wpdb->prefix . 'aysquiz_question_tags';
        $ays_change_type = (isset($_POST['ays_apply']))? $_POST['ays_apply']:'';

        if( isset($_POST["question_tag_action"]) && wp_verify_nonce( $_POST["question_tag_action"],'question_tag_action' ) ){

            //Tag ID
            $id = (isset($_POST['id']) && $_POST['id'] != '') ? intval( $_POST['id'] )  : null;

            //Tag Title
            $tag_title = ((isset($_POST['ays_quiz_question_tags_title']) && $_POST['ays_quiz_question_tags_title'] != '')) ? stripslashes( sanitize_text_field( $_POST['ays_quiz_question_tags_title'] ) ) : '';
            $tag_title = trim( $tag_title );

            if( empty( $tag_title ) ){
                $url = esc_url_raw( remove_query_arg( array('status') ) ) . '&status=failed';
                wp_redirect( $url );
                exit();
            }
            
            // Author
            $author_id = isset($_REQUEST['ays_quiz_question_tags_author']) ? intval( $_REQUEST['ays_quiz_question_tags_author'] ) : 0;

            //Tag Descrition
            $tag_description =  (isset($_POST['ays_quiz_question_tags_description']) && $_POST['ays_quiz_question_tags_description'] != '') ? stripslashes($_POST['ays_quiz_question_tags_description'] ) : '';

            //Tag Status
            $tag_status = (isset($_POST['ays_quiz_quetion_tag_status']) && $_POST['ays_quiz_quetion_tag_status'] != '') ? sanitize_text_field( $_POST['ays_quiz_quetion_tag_status'] ) : 'published';

            //Tag options
            $options = array();

            $message = '';

            //Insert Tag Data
            if( $id == 0 ){
                $result = $wpdb->insert(
                    $question_tag_table,
                    array(
                        'author_id'     => $author_id,
                        'title'         => $tag_title,
                        'description'   => $tag_description,
                        'status'        => $tag_status,
                        'options'       => json_encode($options)
                    ),
                    array( 
                        '%d', // author_id
                        '%s', // title
                        '%s', // description
                        '%s', // status
                        '%s'  // options
                    )
                );
                $message = 'created';
                $question_tag_insert_id = $wpdb->insert_id;
            }else{
                $result = $wpdb->update(
                    $question_tag_table,
                    array(
                        'author_id'     => $author_id,
                        'title'         => $tag_title,
                        'description'   => $tag_description,
                        'status'        => $tag_status,
                        'options'       => json_encode($options)
                    ),
                    array( 'id' => $id ),
                    array( 
                        '%d', // author_id
                        '%s', // title
                        '%s', // description
                        '%s', // status
                        '%s'  // options
                    ),
                    array( '%d' )
                );
                $message = 'updated';
            }

            if( $result >= 0  ) {
                if($ays_change_type != ''){
                    if($id == null){
                        $url = esc_url_raw( add_query_arg( array(
                            "action"    => "edit",
                            "question_tag" => $question_tag_insert_id,
                            "status"    => $message
                        ) ) );
                    }else{
                        $url = esc_url_raw( remove_query_arg(false) ) . '&status=' . $message;
                    }
                    wp_redirect( $url );
                }else{
                    $url = esc_url_raw( remove_query_arg( array('action', 'question_tag') ) ) . '&status=' . $message;
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
    public static function delete_question_tags( $id ) {

        global $wpdb;

        $wpdb->delete(
            "{$wpdb->prefix}aysquiz_question_tags",
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
        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_question_tags";

        if( isset( $_REQUEST['fstatus'] ) ){
            $fstatus = $_REQUEST['fstatus'];
            if($fstatus !== null){
                $filter[] = " status = '".$fstatus."'";
            }
        }
        $search = ( isset( $_REQUEST['s'] ) ) ? $_REQUEST['s'] : false;
        if( $search ){
            $filter[] = sprintf(" title LIKE '%%%s%%' ", esc_sql( $wpdb->esc_like( $search ) )  );
        }

        if(count($filter) !== 0){
            $sql .= " WHERE".implode(" AND ", $filter);
        }

        return $wpdb->get_var( $sql );

    }

    public static function all_record_count() {

        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_question_tags";

        $filter = array();

        $search = ( isset( $_REQUEST['s'] ) ) ? $_REQUEST['s'] : false;
        if( $search ){
            $filter[] = sprintf(" title LIKE '%%%s%%' ", esc_sql( $wpdb->esc_like( $search ) )  );
        }

        if(count($filter) !== 0){
            $sql .= " WHERE ".implode(" AND ", $filter);
        }

        return $wpdb->get_var( $sql );
    }

    public static function published_question_tags_count() {

        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_question_tags WHERE `status`='published'";

        return $wpdb->get_var( $sql );

    }

    public static function unpublished_question_tags_count() {

        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_question_tags WHERE `status`='unpublished'";

        return $wpdb->get_var( $sql );

    }

    /** Text displayed when no customer data is available */
    public function no_items() {
        echo __( 'There are no question tags yet.', $this->plugin_name );
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
        $current_user = get_current_user_id();
        $author_id = (isset( $item['author_id'] ) && $item['author_id'] != 0) ? intval( $item['author_id'] ) : 0;

        if( $current_user == $author_id ){
            return sprintf(
                '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
            );
        }

        if( ! $this->current_user_can_edit ){
            return '';
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
        $current_page = $this->get_pagenum();
        $delete_nonce = wp_create_nonce( $this->plugin_name . '-delete-question-tag' );
        $current_user = get_current_user_id();
        $author_id    = (isset( $item['author_id'] ) && $item['author_id'] != 0) ? intval( $item['author_id'] ) : 0;

        $owner = false;
        if( $current_user == $author_id ){
            $owner = true;
        }

        if( $this->current_user_can_edit ){
            $owner = true;
        }

        $column_t = esc_attr( stripcslashes($item['title']) );
        $t = esc_attr($column_t);

        $restitle = Quiz_Maker_Admin::ays_restriction_string("word", $column_t, 5);

        $url = remove_query_arg( array('status') );
        $url_args = array(
            "page"          => esc_attr( $_REQUEST['page'] ),
            "question_tag" => absint( $item['id'] ),
        );
        $url_args['action'] = "edit";

        if( isset( $_GET['paged'] ) && sanitize_text_field( $_GET['paged'] ) != '' ){
            $url_args['paged'] = $current_page;
        }

        $url = add_query_arg( $url_args, $url );
        
        $title = sprintf( '<a href="%s" title="%s"><strong>%s</strong></a>', $url, $t, $restitle );

        $actions = array();

        if( $owner ){
            $actions['edit'] = sprintf( '<a href="%s">'. __('Edit', $this->plugin_name) .'</a>', $url );
        }else{
            $actions['edit'] = sprintf( '<a href="%s">'. __('View', $this->plugin_name) .'</a>', $url );
        }
        
        if( $owner ){
            $url_args['action'] = "delete";
            $url_args['_wpnonce'] = $delete_nonce;
            $url = add_query_arg( $url_args, $url );
            $actions['delete'] = sprintf( '<a class="ays_confirm_del" data-message="%s" href="%s">'. __('Delete', $this->plugin_name) .'</a>', $restitle, $url );
        }

        // $title = sprintf( '<a href="?page=%s&action=%s&question_tag=%d"><strong>%s</strong></a>', esc_attr( $_REQUEST['page'] ), 'edit', absint( $item['id'] ), $restitle );

        // $actions = array(
        //     'edit' => sprintf( '<a href="?page=%s&action=%s&question_tag=%d">'. __('Edit', $this->plugin_name) .'</a>', esc_attr( $_REQUEST['page'] ), 'edit', absint( $item['id'] ) ),
        // );

        // $actions['delete'] = sprintf( '<a class="ays_confirm_del" data-message="%s" href="?page=%s&action=%s&question_tag=%s&_wpnonce=%s">'. __('Delete', $this->plugin_name) .'</a>', $restitle, esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $delete_nonce );

        return $title . $this->row_actions( $actions );
    }

    function column_published( $item ) {

        switch( $item['status'] ) {
            case "published":
                return '<span class="ays-publish-status"><i class="ays_fa ays_fa_check_square_o" aria-hidden="true"></i>'. __('Published',$this->plugin_name) . '</span>';
                break;
            case "unpublished":
                return '<span class="ays-publish-status"><i class="ays_fa ays_fa_square_o" aria-hidden="true"></i>'. __('Unublished',$this->plugin_name) . '</span>';
                break;
        }
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
            'id'            => __( 'ID', $this->plugin_name ),
        );

        if( isset( $_GET['action'] ) && ( $_GET['action'] == 'add' || $_GET['action'] == 'edit' ) ){
            return array();
        }

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

        $if_user_created = Quiz_Maker_Data::ays_quiz_if_current_user_created("aysquiz_question_tags");

        if ( ! is_null( $if_user_created ) && ! empty( $if_user_created ) && $if_user_created > 0 ) {

        } else if( ! $this->current_user_can_edit ){
            $actions = array();
        }

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

        $per_page     = $this->get_items_per_page( 'question_tags_per_page', 20 );
        $current_page = $this->get_pagenum();
        $total_items  = self::record_count();

        $search = ( isset( $_REQUEST['s'] ) ) ? $_REQUEST['s'] : false;

        $do_search = ( $search ) ? sprintf(" title LIKE '%%%s%%' ", esc_sql( $wpdb->esc_like( $search ) )  ) : '';

        $this->set_pagination_args( array(
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page'    => $per_page //WE have to determine how many items to show on a page
        ) );

        $this->items = self::get_question_tags( $per_page, $current_page, $do_search );
    }

    public function process_bulk_action() {

        //Detect when a bulk action is being triggered...
        if ( 'delete' === $this->current_action() ) {

            // In our file that handles the request, verify the nonce.
            $nonce = esc_attr( $_REQUEST['_wpnonce'] );

            if ( ! wp_verify_nonce( $nonce, $this->plugin_name . '-delete-question-tag' ) ) {
                die( 'Go get a life script kiddies' );
            }
            else {
                self::delete_question_tags( absint( $_GET['question_tag'] ) );

                // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
                // add_query_arg() return the current url

                $url = esc_url_raw( remove_query_arg( array('action', 'question_tag', '_wpnonce') ) ) . '&status=deleted';
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
                self::delete_question_tags( $id );

            }

            // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
            // add_query_arg() return the current url
            $url = esc_url_raw( remove_query_arg( array('action', 'question_tag', '_wpnonce') ) ) . '&status=deleted';
            wp_redirect( $url );
        }
    }

    public function question_tag_notices(){

        $status = (isset($_REQUEST['status'])) ? sanitize_text_field( $_REQUEST['status'] ) : '';

        if ( empty( $status ) )
            return;

        if ( 'created' == $status ){
            $updated_message = esc_html( __( 'Question tag created.', $this->plugin_name ) );
        }elseif ( 'updated' == $status ){
            $updated_message = esc_html( __( 'Question tag saved.', $this->plugin_name ) );
        }elseif ( 'deleted' == $status ){
            $updated_message = esc_html( __( 'Question tag deleted.', $this->plugin_name ) );
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
