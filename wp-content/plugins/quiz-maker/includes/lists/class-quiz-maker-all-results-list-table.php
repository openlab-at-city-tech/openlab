<?php
class All_Results_List_Table extends WP_List_Table{
    private $plugin_name;
    private $current_user_can_edit;
    /** Class constructor */
    public function __construct($plugin_name) {
        $this->plugin_name = $plugin_name;
        $this->current_user_can_edit = Quiz_Maker_Data::quiz_maker_capabilities_for_editing();
        parent::__construct( array(
            'singular' => __( 'Result', $this->plugin_name ), //singular name of the listed records
            'plural'   => __( 'Results', $this->plugin_name ), //plural name of the listed records
            'ajax'     => false //does this table support ajax?
        ) );
        add_action( 'admin_notices', array( $this, 'results_notices' ) );
        add_filter( 'default_hidden_columns', array( $this, 'get_hidden_columns'), 10, 2 );

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
        $titles_sql = "SELECT title, id
                       FROM {$wpdb->prefix}aysquiz_quizes WHERE published = 1 ";

        if( ! $this->current_user_can_edit ){
            $current_user = get_current_user_id();
            $titles_sql .= " AND author_id = ".$current_user." ";
        }
        $titles_sql .= " ORDER BY title ASC ";

        $quiz_titles = $wpdb->get_results($titles_sql);

        $users_sql = "SELECT user_id
                      FROM {$wpdb->prefix}aysquiz_reports";

        if( ! $this->current_user_can_edit ){
            $current_user = get_current_user_id();
            $author_quizzes = Quiz_Maker_Data::ays_get_author_quizzes_ids( $current_user );
            if( !empty( $author_quizzes ) ){
                $users_sql .= " WHERE `quiz_id` IN ( " . implode( $author_quizzes ) . " ) ";
            }else{
                $users_sql .= " WHERE 1 = 2 ";
            }
        }

        $users_sql .= " GROUP BY user_id";
        $users = $wpdb->get_results($users_sql);

        $user_ids = array();
        foreach( $users as $u ){
            $user_ids[] = $u->user_id;
        }

        $users = array();
        if( isset( $user_ids ) && !empty( $user_ids ) ){

            $users_table = esc_sql( $wpdb->prefix . 'users' );

            $quiz_user_ids = implode( ",", $user_ids );

            $sql_users = "SELECT ID,display_name FROM {$users_table} WHERE ID IN (". $quiz_user_ids .")";

            $users = $wpdb->get_results($sql_users, "ARRAY_A");

        }
        
        $quiz_id = null;
        $user_id = null;
        if( isset( $_GET['filterby'] )){
            $quiz_id = intval($_GET['filterby']);
        }
        if( isset( $_GET['filterbyuser'] )){
            $user_id = intval($_GET['filterbyuser']);
        }
        ?>
        <div id="quiz-filter-div-<?php echo esc_attr( $which ); ?>" class="alignleft actions bulkactions">
            <select name="filterby-<?php echo esc_attr( $which ); ?>" id="bulk-action-selector-<?php echo esc_attr( $which ); ?>">
                <option value=""><?php echo __('Select Quiz',$this->plugin_name)?></option>
                <?php
                    foreach($quiz_titles as $key => $q_title){
                        $selected = "";
                        if($quiz_id === intval($q_title->id)){
                            $selected = "selected";
                        }
                        echo "<option ".$selected." value='".$q_title->id."'>".$q_title->title."</option>";
                    }
                ?>
            </select>
            <select name="filterbyuser-<?php echo esc_attr( $which ); ?>" class="ays-search-users-select" id="bulk-action-select2-<?php echo esc_attr( $which ) ?>" >
                <option value=""><?php echo __('Select User',$this->plugin_name)?></option>
                <?php
                    foreach($users as $key => $value){
                        $selected2 = "";
                        if($user_id === intval($value['ID'])){
                            $selected2 = "selected";
                        }
                        echo "<option ".$selected2." value='".$value['ID']."'>".$value['display_name']."</option>";
                    }
                ?>
            </select>
            <input type="button" id="doaction-<?php echo esc_attr( $which ); ?>" class="all-results-filter-apply-<?php echo esc_attr( $which ); ?> button" value="<?php echo __( "Filter", $this->plugin_name ); ?>">
            
            <a style="display:inline-block;" href="?page=<?php echo sanitize_text_field( $_REQUEST['page'] ); ?>" class="button"><?php echo __( "Clear filters", $this->plugin_name ); ?></a>
        </div>
        <?php
    }

    protected function get_views() {
        $published_count = $this->readed_records_count();
        $unpublished_count = $this->unread_records_count();
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
            "readed" => "<a ".$selected_1." href='". $publish_url ."'>". __( 'Read', $this->plugin_name )." (".$published_count.")</a>",
            "unreaded"   => "<a ".$selected_0." href='". $unpublish_url ."'>". __( 'Unread', $this->plugin_name )." (".$unpublished_count.")</a>"
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
    public static function get_reports( $per_page = 50, $page_number = 1 ) {

        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_reports";

        if( self::get_where_condition() !== false ){
            $sql .= self::get_where_condition();
        }else{
            return array();
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
        }
        else{
            $sql .= ' ORDER BY end_date DESC';
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

        $search = isset( $_POST['s'] ) ? sanitize_text_field( $_POST['s'] ) : false;
        if( $search !== null || $search !== false ){
            $s = array();
            $s[] = sprintf( " `user_name` LIKE '%%%s%%' ", esc_sql( $wpdb->esc_like( $search ) ) );
            $s[] = sprintf( " `user_email` LIKE '%%%s%%' ", esc_sql( $wpdb->esc_like( $search ) ) );
            $s[] = sprintf( " `unique_code` LIKE '%%%s%%' ", esc_sql( $wpdb->esc_like( $search ) ) );
            $s[] = sprintf( " `user_phone` LIKE '%%%s%%' ", esc_sql( $wpdb->esc_like( $search ) ) );
            $s[] = sprintf( " `score` LIKE '%%%s%%' ", esc_sql( $wpdb->esc_like( $search ) ) );

            // $args = 'search=';
            // if($search !== null){
            //     $args .= $search;
            //     $args .= '*';
            // }

            // $users = get_users($args);
            // $user_ids_arr = array();

            // foreach ($users as $key => $value) {
            //     $user_ids_arr[] = $value->ID;
            // }

            // if (! empty($user_ids_arr)) {
            //     $user_ids = implode(',', $user_ids_arr);

            //     $s[] = ' `user_id` in ('. $user_ids .')';
            // }

            $where[] = ' ( ' . implode(' OR ', $s) . ' ) ';
        }

        if(isset( $_REQUEST['fstatus'] )){
            $fstatus = intval($_REQUEST['fstatus']);
            switch($fstatus){
                case 0:
                    $where[] = ' `read` = 0 ';
                    break;
                case 1:
                    $where[] = ' `read` = 1 ';
                    break;
            }
        }

        if( isset( $_REQUEST['filterbyuser'] ) ){
            $user_id = intval($_REQUEST['filterbyuser']);
            $where[] = ' `user_id` = '.$user_id.' ';
        }

        $is_filtered_by_quiz = false;
        if(! empty( $_REQUEST['filterby'] ) && $_REQUEST['filterby'] > 0){
            $cat_id = intval($_REQUEST['filterby']);
            $where[] = ' `quiz_id` = '.$cat_id.' ';
            $is_filtered_by_quiz = true;
        }

        if( ! Quiz_Maker_Data::quiz_maker_capabilities_for_editing() ){
            if( ! $is_filtered_by_quiz ){
                $current_user = get_current_user_id();
                $author_quizzes = Quiz_Maker_Data::ays_get_author_quizzes_ids( $current_user );
                if( !empty( $author_quizzes ) ){
                    $where[] = " `quiz_id` IN ( " . implode( $author_quizzes ) . " ) ";
                }else{
                    return false;
                }
            }
        }

        $where[] = " `status` = 'finished' ";

        if( ! empty($where) ){
            $sql = " WHERE " . implode( " AND ", $where );
        }
        return $sql;
    }

    public function get_report_by_id( $id ){
        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_reports WHERE id=" . absint( intval( $id ) );

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
        Quiz_Maker_Data::ays_delete_report_certificate( $id, 'report' );

        $wpdb->delete(
            "{$wpdb->prefix}aysquiz_reports",
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

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_reports ";

        if( self::get_where_condition() !== false ){
            $sql .= self::get_where_condition();
        }else{
            return 0;
        }

        return $wpdb->get_var( $sql );
    }

    public static function all_record_count() {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_reports";

        $where = array();

        if( ! Quiz_Maker_Data::quiz_maker_capabilities_for_editing() ){
            $current_user = get_current_user_id();
            $author_quizzes = Quiz_Maker_Data::ays_get_author_quizzes_ids( $current_user );
            if( !empty( $author_quizzes ) ){
                $where[] = " `quiz_id` IN ( " . implode( $author_quizzes ) . " ) ";
            }else{
                return 0;
            }
        }

        $where[] = " `status` = 'finished' ";

        if( ! empty($where) ){
            $sql .= " WHERE " . implode( " AND ", $where );
        }

        return $wpdb->get_var( $sql );
    }

    public static function unread_records_count() {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_reports ";

        $where = array();
        $where[] = " `read` = 0 ";

        if( ! Quiz_Maker_Data::quiz_maker_capabilities_for_editing() ){
            $current_user = get_current_user_id();
            $author_quizzes = Quiz_Maker_Data::ays_get_author_quizzes_ids( $current_user );
            if( !empty( $author_quizzes ) ){
                $where[] = " `quiz_id` IN ( " . implode( $author_quizzes ) . " ) ";
            }else{
                return 0;
            }
        }

        $where[] = " `status` = 'finished' ";

        if( ! empty($where) ){
            $sql .= " WHERE " . implode( " AND ", $where );
        }

        return $wpdb->get_var( $sql );
    }

    public function readed_records_count() {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_reports ";

        $where = array();
        $where[] = " `read` = 1 ";

        if( ! $this->current_user_can_edit ){
            $current_user = get_current_user_id();
            $author_quizzes = Quiz_Maker_Data::ays_get_author_quizzes_ids( $current_user );
            if( !empty( $author_quizzes ) ){
                $where[] = " `quiz_id` IN ( " . implode( $author_quizzes ) . " ) ";
            }else{
                return 0;
            }
        }

        $where[] = " `status` = 'finished' ";

        if( ! empty($where) ){
            $sql .= " WHERE " . implode( " AND ", $where );
        }

        return $wpdb->get_var( $sql );
    }

    public static function ays_quiz_mark_as_read( $id ) {
        global $wpdb;
        $reports_table = $wpdb->prefix . "aysquiz_reports";

        if (! is_null($id)) {
            $id = absint( intval( $id ) );
        }

        $read = 1;
        $result = $wpdb->update(
            $reports_table,
            array(
                'read' => $read,
            ),
            array( 'id' => $id ),
            array(
                '%d'
            ),
            array( '%d' )
        );
    }

    /**
     * Mark as read a customer record.
     *
     * @param int $id customer ID
     */
    public static function mark_as_read_reports( $id ) {
        global $wpdb;
        $wpdb->update(
            $wpdb->prefix . "aysquiz_reports",
            array('read' => 1),
            array('id' => $id),
            array('%d'),
            array('%d')
        );
    }

    /**
     * Mark as unread a customer record.
     *
     * @param int $id customer ID
     */
    public static function mark_as_unread_reports( $id ) {
        global $wpdb;
        $wpdb->update(
            $wpdb->prefix . "aysquiz_reports",
            array('read' => 0),
            array('id' => $id),
            array('%d'),
            array('%d')
        );
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
            case 'quiz_id':
            case 'user_id':
            case 'user_ip':
            case 'user_name':
            case 'user_email':
            case 'user_phone':
            case 'start_date':
            case 'end_date':
            case 'duration':
            case 'points':
            case 'unique_code':
            case 'status':
            case 'note_text':
            case 'id':
                return $item[ $column_name ];
                break;
            case 'score':
                return $item[ $column_name ] . " %";
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
    function column_quiz_id( $item ) {
        global $wpdb;

        $delete_nonce = wp_create_nonce( $this->plugin_name . '-delete-result' );

        $result = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}aysquiz_quizes WHERE id={$item['quiz_id']}", "ARRAY_A");
        if($item['read'] == 0){
            $result_read = "style='font-weight:bold;'";
        }else{
            $result_read = "";
        }
        if($result == null){
            $title = __( 'Quiz has been deleted', $this->plugin_name );
        }else{
            $restitle = Quiz_Maker_Admin::ays_restriction_string("word",stripcslashes($result['title']), 5);
            $title = sprintf( '<a href="javascript:void(0)" data-result="%d" class="%s" '.$result_read.'>%s</a><input type="hidden" value="%d" class="ays_result_read">', absint( $item['id'] ), 'ays-show-results', $restitle,  $item['read']);
        }
        $quiz_id =  isset($result['quiz_id']) ? $result['quiz_id'] : 0;
        $actions = array(
            'view-details' => sprintf( '<a href="javascript:void(0);" data-result="%d" class="%s">%s</a>', absint( $item['id'] ), 'ays-show-results', __( 'View details', $this->plugin_name ) ),
            'delete' => sprintf( '<a class="ays_confirm_del" data-message="this report" href="?page=%s&action=%s&result=%s&_wpnonce=%s">%s</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $delete_nonce, __( "Delete", $this->plugin_name ) )
        );

        return $title . $this->row_actions( $actions );
    }

    function column_user_id( $item ) {
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
        return $name;
    }

    function column_quiz_rate( $item ) {
        global $wpdb;

        $delete_nonce = wp_create_nonce( $this->plugin_name . '-delete-result' );

        $options = json_decode($item['options'], true);
        $rate_id = (isset($options['rate_id'])) ? $options['rate_id'] : null;
        if($rate_id !== null){
            $margin_of_icon = "style='margin-left: 5px;'";
            $result = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}aysquiz_rates WHERE id={$rate_id}", "ARRAY_A");
            $result['options'] = NULL;
            if( isset( $result['options'] ) && $this->isJSON($result['options'])){
                $review_json = json_decode($result['options'], true);
                $review = (isset( $review_json['reason'] ) && $review_json['reason'] != "") ? $review_json['reason'] : "";
            }elseif($result['options'] != ''){
                $review = (isset( $result['options'] ) && $result['options'] != "") ? $result['options'] : "";
            }else{
                $review = (isset( $result['review'] ) && $result['review'] != "") ? $result['review'] : "";
            }
            $reason = htmlentities(stripslashes(wpautop($review)));
            if($reason == ''){
                $reason = __("No review provided", $this->plugin_name);
            }
            $score = (isset( $result['score'] ) && $result['score'] != "") ? $result['score'] : "";
            $title = "<span data-result='".absint( $item['id'] )."' class='ays-show-rate-avg'>
                        $score
                        <a class='ays_help' $margin_of_icon data-template='<div class=\"rate_tooltip tooltip\" role=\"tooltip\"><div class=\"arrow\"></div><div class=\"rate-tooltip-inner tooltip-inner\"></div></div>' data-toggle='tooltip' data-html='true' title='$reason'><i class='ays_fa ays_fa_info_circle'></i></a>
                </span>";
        }else{
            $margin_of_icon = '';
            $reason = __("No rate provided", $this->plugin_name);
            $score = '';
            $title = "";
        }
        return $title;
    }

    function column_duration( $item ) {
        global $wpdb;

        $delete_nonce = wp_create_nonce( $this->plugin_name . '-delete-result' );

        $options = json_decode($item['options'], true);
        $passed_time = (isset($options['passed_time'])) ? $options['passed_time'] : null;
        if($passed_time !== null){
            $title = $passed_time;
        }else{
            $title = __('No data', $this->plugin_name);
        }
        return $title;
    }

    function column_status( $item ) {
        global $wpdb;
        if( !isset( $item['quiz_id'] ) || intval( $item['quiz_id'] ) == 0 ){
            return '';
        }

        $sql = "SELECT options FROM " . $wpdb->prefix . "aysquiz_quizes WHERE id=" . intval( $item['quiz_id'] );
        $quiz_options = $wpdb->get_var( $sql );
        $quiz_options = $quiz_options != '' ? json_decode( $quiz_options, true ) : array();
        $pass_score = isset( $quiz_options['pass_score'] ) && $quiz_options['pass_score'] != '' ? absint( $quiz_options['pass_score'] ) : 0;
        // Quiz Pass Score type
        $quiz_pass_score_type = (isset($quiz_options['quiz_pass_score_type']) && $quiz_options['quiz_pass_score_type'] != '') ? sanitize_text_field( $quiz_options['quiz_pass_score_type'] ) : 'percentage';

        switch ( $quiz_pass_score_type ) {
            case 'point':
                $score = absint( $item['points'] );
                break;
            
            case 'percentage':
            default:
                $score = absint( $item['score'] );
                break;
        }

        $status = '';
        if( $pass_score != 0 ){
            if( $score >= $pass_score ){
                $status = "<span style='color:green;font-weight:900;'><i class='ays_fa ays_fa_check' style='color:green;font-size: 18px'></i> " . __( "Passed", $this->plugin_name ) . "</span>";
            }else{
                $status = "<span style='color:brown;font-weight:900;'><i class='ays_fa ays_fa_times' style='font-size: 18px'></i> " . __( "Failed", $this->plugin_name ) . "</span>";
            }
        }

        return $status;
    }

    public function column_points( $item ) {
        global $wpdb;
        $score = "-";
        if(isset($item['points']) && isset($item['max_points'])){
            if(!empty($item['points']) && !empty($item['max_points'])){
                $score = "<p>" . round( $item['points'], 2 ) . "/" . round( $item['max_points'], 2 ) . "</p>";
            }
        }else{
            $options = json_decode($item['options'], true);
            $points = isset($options['user_points']) ? $options['user_points'] : false;
            $max_points = isset($options['max_points']) ? $options['max_points'] : false;
            if($points !== false && $max_points !== false){
                if(!empty($points) && !empty($max_points)){
                    $score = "<p>" . round( $points, 2 ) . "/" . round( $max_points, 2 ) . "</p>";
                }
            }
        }
        return $score;
    }

    public function column_unique_code( $item ) {
        global $wpdb;
        $unique_code = isset($item['unique_code']) && $item['unique_code'] != '' ? $item['unique_code'] : '<p style="text-align:center;">-</p>';
        $unique_code_html = "<strong style='text-transform:uppercase!important;white-space:nowrap;'>" . $unique_code . "</strong>";
        return $unique_code_html;
    }

    public function column_note_text( $item ) {
        $options = json_decode($item['options']);

        $note_text = ( isset($options->note_text) && $options->note_text != '' ) ? sanitize_text_field(stripslashes($options->note_text)) : '';
        $note_text = "<div class='ays-admin-note-text-list-table-". $item['id'] ."'>" . $note_text . "</div>";

        return $note_text;
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
     *  Associative array of columns
     *
     * @return array
     */
    function get_columns() {
        $columns = array(
            'cb'                    => '<input type="checkbox" />',
            'quiz_id'               => __( 'Quiz', $this->plugin_name ),
            'user_id'               => __( 'WP User', $this->plugin_name ),
            'user_ip'               => __( 'User IP', $this->plugin_name ),
            'user_name'             => __( 'Name', $this->plugin_name ),
            'user_email'            => __( 'Email', $this->plugin_name ),
            'user_phone'            => __( 'Phone', $this->plugin_name ),
            'quiz_rate'             => __( 'Rate', $this->plugin_name ),
            'start_date'            => __( 'Start', $this->plugin_name ),
            'end_date'              => __( 'End', $this->plugin_name ),
            'duration'              => __( 'Duration', $this->plugin_name ),
            'score'                 => __( 'Score', $this->plugin_name ),
            'points'                => __( 'Points', $this->plugin_name ),
            'unique_code'           => __( 'Unique Code', $this->plugin_name ),
            'status'                => __( 'Status', $this->plugin_name ),
            'note_text'             => __( 'Admin Note', $this->plugin_name ),
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
            'quiz_id'       => array( 'quiz_id', true ),
            'user_id'       => array( 'user_id', true ),
            'user_ip'       => array( 'user_ip', true ),
            'start_date'    => array( 'start_date', true ),
            'score'         => array( 'score', true ),
            'unique_code'   => array( 'unique_code', true ),
            'user_name'     => array( 'user_name', true ),
            'user_email'    => array( 'user_email', true ),
            'user_phone'    => array( 'user_phone', true ),
            'end_date'      => array( 'end_date', true ),
            'id'            => array( 'id', true ),
        );

        return $sortable_columns;
    }

    /**
     * Columns to make sortable.
     *
     * @return array
     */
    public function get_hidden_columns() {
        $sortable_columns = array(
            'user_phone',
            'end_date',
            'unique_code',
            'status',
            'id'
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
            'mark-as-read' => __( 'Mark as read', $this->plugin_name),
            'mark-as-unread' => __( 'Mark as unread', $this->plugin_name),
            'bulk-delete' => __( 'Delete', $this->plugin_name),
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

        $per_page     = $this->get_items_per_page( 'quiz_all_results_per_page', 50 );

        $current_page = $this->get_pagenum();
        $total_items  = self::record_count();

        $this->set_pagination_args( array(
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page'    => $per_page //WE have to determine how many items to show on a page
        ) );

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
                self::delete_reports( $id );

            }

            // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
            // add_query_arg() return the current url

            $url = esc_url_raw( remove_query_arg(array('action', 'result', '_wpnonce')  ) ) . '&status=' . $message;
            wp_redirect( $url );
        }

        // If the mark-as-read bulk action is triggered
        if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'mark-as-read' ) || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'mark-as-read' ) ) {

            $delete_ids = ( isset( $_POST['bulk-delete'] ) && ! empty( $_POST['bulk-delete'] ) ) ? esc_sql( $_POST['bulk-delete'] ) : array();

            // loop over the array of record IDs and delete them
            foreach ( $delete_ids as $id ) {
                self::mark_as_read_reports( $id );
            }

            // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
            // add_query_arg() return the current url

            $url = esc_url_raw( remove_query_arg(array('action', 'result', '_wpnonce') ) );

            $message = 'marked-as-read';
            $url = add_query_arg( array(
                'status' => $message,
            ), $url );
            wp_redirect( $url );
        }

        // If the mark-as-unread bulk action is triggered
        if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'mark-as-unread' ) || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'mark-as-unread' ) ) {

            $delete_ids = ( isset( $_POST['bulk-delete'] ) && ! empty( $_POST['bulk-delete'] ) ) ? esc_sql( $_POST['bulk-delete'] ) : array();

            // loop over the array of record IDs and delete them
            foreach ( $delete_ids as $id ) {
                self::mark_as_unread_reports( $id );
            }

            // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
            // add_query_arg() return the current url

            $url = esc_url_raw( remove_query_arg(array('action', 'result', '_wpnonce') ) );

            $message = 'marked-as-unread';
            $url = add_query_arg( array(
                'status' => $message,
            ), $url );

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
            $updated_message = esc_html( __( 'Result(s) deleted.', $this->plugin_name ) );

        if ( empty( $updated_message ) )
            return;

        ?>
        <div class="notice notice-success is-dismissible">
            <p> <?php echo $updated_message; ?> </p>
        </div>
        <?php
    }
}
