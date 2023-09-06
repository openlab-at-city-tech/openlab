<?php
class Question_Categories_List_Table extends WP_List_Table{
    private $plugin_name;
    private $title_length;
    protected $current_user_can_edit;
    /** Class constructor */
    public function __construct($plugin_name) {
        $this->plugin_name = $plugin_name;
        $this->title_length = Quiz_Maker_Data::get_listtables_title_length('question_categories');
        $this->current_user_can_edit = Quiz_Maker_Data::quiz_maker_capabilities_for_editing();
        parent::__construct( array(
            'singular' => __( 'Question Category', $this->plugin_name ), //singular name of the listed records
            'plural'   => __( 'Question Categories', $this->plugin_name ), //plural name of the listed records
            'ajax'     => false //does this table support ajax?
        ) );
        add_action( 'admin_notices', array( $this, 'question_category_notices' ) );
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

    /**
     * Disables the views for 'side' context as there's not enough free space in the UI
     * Only displays them on screen/browser refresh. Else we'd have to do this via an AJAX DB update.
     *
     * @see WP_List_Table::extra_tablenav()
     */
    public function extra_tablenav($which) {

        $quiz_cat_description = array(
            "with"    => __( "With description", $this->plugin_name),
            "without" => __( "Without description", $this->plugin_name),
        );

        $description_key = null;

        if( isset( $_GET['filterbyDescription'] ) && sanitize_text_field( $_GET['filterbyDescription'] ) != ""){
            $description_key = sanitize_text_field( $_GET['filterbyDescription'] );
        }

        ?>

        <div id="quiz-filter-div-<?php echo esc_attr( $which ); ?>" class="alignleft actions bulkactions">

            <select name="filterbyDescription-<?php echo esc_attr( $which ); ?>" id="bulk-action-quiz-cat-description-selector-<?php echo esc_attr( $which ); ?>">
                <option value=""><?php echo __('With/without description',$this->plugin_name); ?></option>
                <?php
                    foreach($quiz_cat_description as $key => $cat_description) {
                        $selected = "";
                        if( $description_key === sanitize_text_field($key) ) {
                            $selected = "selected";
                        }
                        echo "<option ".$selected." value='".esc_attr( $key )."'>".$cat_description."</option>";
                    }
                ?>
            </select>
            <input type="button" id="doaction-quiz-<?php echo esc_attr( $which ); ?>" class="ays-quiz-question-tab-all-filter-button-<?php echo esc_attr( $which ); ?> button" value="<?php echo __( "Filter", $this->plugin_name ); ?>">
        </div>

        <a style="" href="?page=<?php echo esc_attr( sanitize_text_field( $_REQUEST['page'] ) ); ?>" class="button"><?php echo __( "Clear filters", $this->plugin_name ); ?></a>
        <?php
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

        $admin_url = get_admin_url( null, 'admin.php' );
        $get_properties = http_build_query($_GET);

        $status_links_url = $admin_url . "?" . $get_properties;
        $publish_url = esc_url( add_query_arg('fstatus', 1, $status_links_url) );
        $unpublish_url = esc_url( add_query_arg('fstatus', 0, $status_links_url) );

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

        if( isset( $_GET['filterbyDescription'] ) && sanitize_text_field( $_GET['filterbyDescription'] ) != ""){
            $description_key = sanitize_text_field( $_GET['filterbyDescription'] );
            
            switch ( $description_key ) {
                case 'with':
                    $where[] = ' `description` != "" ';
                    break;
                case 'without':
                default:
                    $where[] = ' `description` = "" ';
                    break;
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

    public function get_question_category( $id ) {
        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_categories WHERE id=" . absint( intval( $id ) );

        $result = $wpdb->get_row($sql, 'ARRAY_A');

        return $result;
    }

    public function duplicate_question_categories( $id ){
        global $wpdb;

        if ( is_null( $id ) || empty($id) || $id == 0 ) {
            return;
        }

        $question_category_table = $wpdb->prefix . 'aysquiz_categories';
        $question_category_data = $this->get_question_category($id);
        
        $title = (isset($question_category_data['title']) && $question_category_data['title'] != "") ? stripslashes( sanitize_text_field( $question_category_data['title'] ) ) : __("Copy", 'quiz-maker');
        $description =  (isset($question_category_data['description']) && $question_category_data['description'] != "") ? wp_kses_post( $question_category_data['description'] ) : "";
        $publish = (isset($question_category_data['published']) && $question_category_data['published'] != "") ? absint( sanitize_text_field( $question_category_data['published'] ) ) : 0;

        $result = $wpdb->insert(
            $question_category_table,
            array(
                'title'         =>  "Copy - " . $title,
                'description'   => $description,
                'published'     => $publish
            ),
            array(
                '%s', //title
                '%s', //description
                '%d'  //published
            )
        );
        if( $result >= 0 ){
            $message = "duplicated";
            $url = esc_url_raw( remove_query_arg(array('action', 'question_category')  ) ) . '&status=' . $message;
            wp_redirect( $url );
        }
        
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

            // Author
            $author_id = isset($_REQUEST['ays_quiz_question_category_author']) ? intval( $_REQUEST['ays_quiz_question_category_author'] ) : 0;

            $description =  stripslashes($data['ays_description'] );
            $publish = absint( intval( $data['ays_publish'] ) );
            $message = '';
            if( $id == 0 ){
                $result = $wpdb->insert(
                    $question_category_table,
                    array(
                        'author_id'     => $author_id,
                        'title'         => $title,
                        'description'   => $description,
                        'published'     => $publish
                    ),
                    array( 
                        '%d', // author_id
                        '%s', // title
                        '%s', // description
                        '%d'  // published
                    )
                );
                $message = 'created';
                $question_category_insert_id = $wpdb->insert_id;
            }else{
                $result = $wpdb->update(
                    $question_category_table,
                    array(
                        'author_id'     => $author_id,
                        'title'         => $title,
                        'description'   => $description,
                        'published'     => $publish
                    ),
                    array( 'id' => $id ),
                    array( 
                        '%d', // author_id
                        '%s', // title
                        '%s', // description
                        '%d'  // published
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

    public static function ays_quiz_published_unpublished_questions_categories( $id, $status = 'published' ) {
        global $wpdb;

        $categories_table = esc_sql( $wpdb->prefix . "aysquiz_categories" );

        if ( is_null( $id ) || absint( sanitize_text_field( $id ) ) == 0 ) {
            return null;
        }

        $id = absint( sanitize_text_field( $id ) );

        switch ( $status ) {
            case 'published':
                $published = 1;
                break;
            case 'unpublished':
                $published = 0;
                break;
            default:
                $published = 1;
                break;
        }

        $categories_result = $wpdb->update(
            $categories_table,
            array(
                'published' => $published,
            ),
            array( 'id' => $id ),
            array(
                '%d'
            ),
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

        $where = array();
        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_categories";

        if( isset( $_REQUEST['fstatus'] ) ){
            $fstatus = $_REQUEST['fstatus'];
            if($fstatus !== null){
                $where[] = " published = ".$fstatus." ";
            }
        }

        $search = ( isset( $_REQUEST['s'] ) ) ? $_REQUEST['s'] : false;
        if( $search ){
            $where[] = sprintf(" title LIKE '%%%s%%' ", esc_sql( $wpdb->esc_like( $search ) )  );
        }

        if( isset( $_GET['filterbyDescription'] ) && sanitize_text_field( $_GET['filterbyDescription'] ) != ""){
            $description_key = sanitize_text_field( $_GET['filterbyDescription'] );
            
            switch ( $description_key ) {
                case 'with':
                    $where[] = ' `description` != "" ';
                    break;
                case 'without':
                default:
                    $where[] = ' `description` = "" ';
                    break;
            }
        }

        if(count($where) !== 0){
            $sql .= " WHERE ".implode(" AND ", $where);
        }

        return $wpdb->get_var( $sql );
    }

    public static function all_record_count() {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_categories";

        $where = array();

        $search = ( isset( $_REQUEST['s'] ) ) ? $_REQUEST['s'] : false;
        if( $search ){
            $where[] = sprintf(" title LIKE '%%%s%%' ", esc_sql( $wpdb->esc_like( $search ) )  );
        }

        if( isset( $_GET['filterbyDescription'] ) && sanitize_text_field( $_GET['filterbyDescription'] ) != ""){
            $description_key = sanitize_text_field( $_GET['filterbyDescription'] );
            
            switch ( $description_key ) {
                case 'with':
                    $where[] = ' `description` != "" ';
                    break;
                case 'without':
                default:
                    $where[] = ' `description` = "" ';
                    break;
            }
        }

        if(count($where) !== 0){
            $sql .= " WHERE ".implode(" AND ", $where);
        }

        return $wpdb->get_var( $sql );
    }

    public static function published_question_categories_count() {
        global $wpdb;
        $where = array();

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_categories WHERE published=1";

        if( isset( $_GET['filterbyDescription'] ) && sanitize_text_field( $_GET['filterbyDescription'] ) != ""){
            $description_key = sanitize_text_field( $_GET['filterbyDescription'] );
            
            switch ( $description_key ) {
                case 'with':
                    $where[] = ' `description` != "" ';
                    break;
                case 'without':
                default:
                    $where[] = ' `description` = "" ';
                    break;
            }
        }

        if(count($where) !== 0){
            $sql .= " AND ".implode(" AND ", $where);
        }

        return $wpdb->get_var( $sql );
    }
    
    public static function unpublished_question_categories_count() {
        global $wpdb;
        $where = array();

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_categories WHERE published=0";

        if( isset( $_GET['filterbyDescription'] ) && sanitize_text_field( $_GET['filterbyDescription'] ) != ""){
            $description_key = sanitize_text_field( $_GET['filterbyDescription'] );
            
            switch ( $description_key ) {
                case 'with':
                    $where[] = ' `description` != "" ';
                    break;
                case 'without':
                default:
                    $where[] = ' `description` = "" ';
                    break;
            }
        }
        
        if(count($where) !== 0){
            $sql .= " AND ".implode(" AND ", $where);
        }

        return $wpdb->get_var( $sql );
    }

    /** Text displayed when no customer data is available */
    public function no_items() {
        
        if(isset( $item['id'] ) && intval($item['id']) === 1){
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
        $current_user = get_current_user_id();
        $author_id = (isset( $item['author_id'] ) && $item['author_id'] != 0) ? intval( $item['author_id'] ) : 0;
        
        if(intval($item['id']) === 1){
            return;
        }

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
        $delete_nonce = wp_create_nonce( $this->plugin_name . '-delete-question-category' );
        $current_user = get_current_user_id();
        $author_id    = (isset( $item['author_id'] ) && $item['author_id'] != 0) ? intval( $item['author_id'] ) : 0;

        $owner = false;
        if( $current_user == $author_id ){
            $owner = true;
        }

        if( $this->current_user_can_edit ){
            $owner = true;
        }

        $category_title_length = intval( $this->title_length );

        $column_t = esc_attr( stripcslashes($item['title']) );
        $t = esc_attr($column_t);

        $restitle = Quiz_Maker_Admin::ays_restriction_string("word", $column_t, $category_title_length);

        $url = remove_query_arg( array('status') );
        $url_args = array(
            "page"          => esc_attr( $_REQUEST['page'] ),
            "question_category" => absint( $item['id'] ),
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

        $actions['duplicate'] = sprintf( '<a href="?page=%s&action=%s&question_category=%d">'. __('Duplicate', $this->plugin_name) .'</a>', esc_attr( $_REQUEST['page'] ), 'duplicate', absint( $item['id'] ) );
        
        if( $owner && intval($item['id']) !== 1){
            $url_args['action'] = "delete";
            $url_args['_wpnonce'] = $delete_nonce;
            $url = add_query_arg( $url_args, $url );
            $actions['delete'] = sprintf( '<a class="ays_confirm_del" data-message="%s" href="%s">'. __('Delete', $this->plugin_name) .'</a>', $restitle, $url );
        }

        return $title . $this->row_actions( $actions );
    }

    function column_description( $item ) {
        $desc = stripslashes( esc_html( strip_tags($item[ 'description' ]) ) );
        $description = Quiz_Maker_Admin::ays_restriction_string("word", $desc, 15);

        $description = "<div class='ays-quiz-list-table-description-column' title='". $desc ."'> ". $description ." </div>";

        return $description;
    }

    function column_published( $item ) {
        $status = (isset( $item['published'] ) && $item['published'] != '') ? absint( sanitize_text_field( $item['published'] ) ) : '';

        $status_html = '';
        switch( $status ) {
            case 1:
                $status_html = '<span class="ays-publish-status"><i class="ays_fa ays_fa_check_square_o" aria-hidden="true"></i>'. __('Published',$this->plugin_name) . '</span>';
                break;
            case 0:
                $status_html = '<span class="ays-publish-status"><i class="ays_fa ays_fa_square_o" aria-hidden="true"></i>'. __('Unpublished',$this->plugin_name) . '</span>';
                break;
            default:
                $status_html = '<span class="ays-publish-status"><i class="ays_fa ays_fa_square_o" aria-hidden="true"></i>'. __('Unpublished',$this->plugin_name) . '</span>';
                break;
        }

        return $status_html;
    }

    function column_items_count( $item ) {
        global $wpdb;

        $result = '';
        if ( isset( $item['id'] ) && absint( $item['id'] ) > 0 && ! is_null( sanitize_text_field( $item['id'] ) ) ) {
            $id = absint( esc_sql( $item['id'] ) );

            $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_questions WHERE category_id = " . $id;

            $result = $wpdb->get_var($sql);

            if ( ! is_null( $result ) && $result > 0 ) {
                $result = sprintf( '<a href="?page=%s&filterby=%d" target="_blank">%s</a>', 'quiz-maker-questions', $id, $result );
            }
        }

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
            'published'     => __( 'Status', $this->plugin_name ),
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
            'bulk-published'    => __('Publish', $this->plugin_name),
            'bulk-unpublished'  => __('Unpublish', $this->plugin_name),
            'bulk-delete'       => __('Delete', $this->plugin_name),
        );

        $if_user_created = Quiz_Maker_Data::ays_quiz_if_current_user_created("aysquiz_categories");

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

        $per_page     = $this->get_items_per_page( 'question_categories_per_page', 20 );
        $current_page = $this->get_pagenum();
        $total_items  = self::record_count();

        $search = ( isset( $_REQUEST['s'] ) ) ? $_REQUEST['s'] : false;

        $do_search = ( $search ) ? sprintf(" title LIKE '%%%s%%' ", esc_sql( $wpdb->esc_like( $search ) )  ) : '';

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

            $delete_ids = ( isset( $_POST['bulk-delete'] ) && ! empty( $_POST['bulk-delete'] ) ) ? esc_sql( $_POST['bulk-delete'] ) : array();

            // loop over the array of record IDs and delete them
            foreach ( $delete_ids as $id ) {
                self::delete_question_categories( $id );

            }

            // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
            // add_query_arg() return the current url
            $url = esc_url_raw( remove_query_arg( array('action', 'question_category', '_wpnonce') ) ) . '&status=deleted';
            wp_redirect( $url );
        } elseif ((isset($_POST['action']) && $_POST['action'] == 'bulk-published')
                  || (isset($_POST['action2']) && $_POST['action2'] == 'bulk-published')
        ) {

            $published_ids = ( isset( $_POST['bulk-delete'] ) && ! empty( $_POST['bulk-delete'] ) ) ? esc_sql( $_POST['bulk-delete'] ) : array();

            // loop over the array of record IDs and mark as read them

            foreach ( $published_ids as $id ) {
                self::ays_quiz_published_unpublished_questions_categories( $id , 'published' );
            }

            // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
            // add_query_arg() return the current url
            $url = esc_url_raw( remove_query_arg(array('action', 'question', '_wpnonce')  ) ) . '&status=published';
            wp_redirect( $url );
        } elseif ((isset($_POST['action']) && $_POST['action'] == 'bulk-unpublished')
                  || (isset($_POST['action2']) && $_POST['action2'] == 'bulk-unpublished')
        ) {

            $unpublished_ids = ( isset( $_POST['bulk-delete'] ) && ! empty( $_POST['bulk-delete'] ) ) ? esc_sql( $_POST['bulk-delete'] ) : array();

            // loop over the array of record IDs and mark as read them

            foreach ( $unpublished_ids as $id ) {
                self::ays_quiz_published_unpublished_questions_categories( $id , 'unpublished' );
            }

            // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
            // add_query_arg() return the current url
            $url = esc_url_raw( remove_query_arg(array('action', 'question', '_wpnonce')  ) ) . '&status=unpublished';
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
        }elseif ( 'published' == $status ){
            $updated_message = esc_html( __( 'Question category published.', $this->plugin_name ) );
        }elseif ( 'unpublished' == $status ){
            $updated_message = esc_html( __( 'Question category unpublished.', $this->plugin_name ) );
        }elseif ( 'duplicated' == $status ){
            $updated_message = esc_html( __( 'Question category duplicated.', $this->plugin_name ) );
        }

        $error_statuses = array( 'failed' );
        $notice_class = 'notice-success';
        if( in_array( $status, $error_statuses ) ){
            $notice_class = 'notice-error';
        }

        if ( empty( $updated_message ) )
            return;

        ?>
        <div class="notice <?php echo esc_attr( $notice_class ); ?> is-dismissible">
            <p> <?php echo $updated_message; ?> </p>
        </div>
        <?php
    }
}
