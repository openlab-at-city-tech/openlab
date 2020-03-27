<?php
ob_start();
class Quiz_Each_Results_List_Table extends WP_List_Table{
    private $plugin_name;
    /** Class constructor */
    public function __construct($plugin_name) {
        $this->plugin_name = $plugin_name;
        parent::__construct( array(
            'singular' => __( 'Each result', $this->plugin_name ), //singular name of the listed records
            'plural'   => __( 'Each results', $this->plugin_name ), //plural name of the listed records
            'ajax'     => false //does this table support ajax?
        ) );
        add_action( 'admin_notices', array( $this, 'each_results_notices' ) );
        add_filter( 'hidden_columns', array( $this, 'get_hidden_columns'), 10, 2 );

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
        $users_sql = "SELECT {$wpdb->prefix}aysquiz_reports.user_id
                      FROM {$wpdb->prefix}aysquiz_reports 
                      WHERE quiz_id = " . $_GET['quiz'] . "
                      GROUP BY user_id";
        $users_res = $wpdb->get_results($users_sql, 'ARRAY_A');
        $users = array();
        $quiz_id = null;
        if( isset( $_GET['wpuser'] )){
            $user_id = intval($_GET['wpuser']);
        }else{
            $user_id = get_current_user_id();
        }
        $clear_url = "?page=" . $_REQUEST['page'] . "&quiz=" . $_REQUEST['quiz'];
        ?>
        <div id="user-filter-div" class="alignleft actions bulkactions">
            <select name="filterbyuser" id="bulk-action-selector-top2">
                <option value=""><?php echo __('Select User',$this->plugin_name)?></option>
                <?php
                    foreach($users_res as $key => $user){
                        $selected = "";
                        if($user_id === intval($user['user_id'])){
                            $selected = "selected";
                        }
                        if(intval($user['user_id']) == 0){
                            $name = __( 'Guest', $this->plugin_name );
                        }else{
                            $wpuser = get_userdata( intval($user['user_id']) );
                            $name = $wpuser->data->display_name;
                        }
                        $users[$user['user_id']]['name'] = $name;
                        $users[$user['user_id']]['selected'] = $selected;
                        $users[$user['user_id']]['id'] = $user['user_id'];
                    }
                    sort($users);
                    foreach($users as $key => $user){                        
                        echo "<option ".$user['selected']." value='".$user['id']."'>".$user['name']."</option>";
                    }
                ?>
            </select>
            <input type="button" id="doaction2" class="user-filter-apply button" value="Filter">
        </div>
        <a style="margin: 3px 8px 0 0;display:inline-block;" href="<?php echo $clear_url; ?>" class="button"><?php echo __( "Clear filters", $this->plugin_name ); ?></a>
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
        $link = "?page=" . esc_attr( $_REQUEST['page'] ) . "&quiz=" . $_REQUEST['quiz'];
        $status_links = array(
            "all" => "<a ".$selected_all." href='".$link."'>All (".$all_count.")</a>",
            "readed" => "<a ".$selected_1." href='".$link."&fstatus=1'>Readed (".$published_count.")</a>",
            "unreaded"   => "<a ".$selected_0." href='".$link."&fstatus=0'>Unreaded (".$unpublished_count.")</a>"
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
    public static function get_results( $per_page = 50, $page_number = 1 ) {

        global $wpdb;
        $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_reports";

        $sql .= self::get_where_condition();
        
        if ( ! empty( $_REQUEST['orderby'] )) {
            $order_by = esc_sql( $_REQUEST['orderby'] );
            if($order_by == 'score'){
                $order_by = 'CAST(score as UNSIGNED)';
            }
            $sql .= ' ORDER BY ' . $order_by;
            $sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' DESC';
        }
        else{
            $sql .= ' ORDER BY id DESC';
        }
        $sql .= " LIMIT $per_page";
        $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;

        $result = $wpdb->get_results( $sql, 'ARRAY_A' );
        return $result;
    }

    public static function get_where_condition(){
        $where = array();
        $sql = '';

        $search = ( isset( $_REQUEST['s'] ) ) ? $_REQUEST['s'] : false;
        if( $search ){
            $s = array();
            $s[] = ' `user_name` LIKE \'%'.$search.'%\' ';
            $s[] = ' `user_email` LIKE \'%'.$search.'%\' ';
            $s[] = ' `user_phone` LIKE \'%'.$search.'%\' ';
            $s[] = ' `score` LIKE \'%'.$search.'%\' ';
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

        if(! empty( $_REQUEST['filterby'] ) && $_REQUEST['filterby'] > 0){
            $cat_id = intval($_REQUEST['filterby']);
            $where[] = ' `quiz_id` = '.$cat_id.' ';
        }

        if( isset( $_REQUEST['wpuser'] ) ){
            $user_id = intval($_REQUEST['wpuser']);
            $where[] = ' `user_id` = '.$user_id.' ';
        }
        
        if( isset( $_REQUEST['quiz'] ) ){
            $quiz_id = intval($_REQUEST['quiz']);
            $where[] = ' `quiz_id` = '.$quiz_id.' ';
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
            "{$wpdb->prefix}aysquiz_reports",
            array('id' => $id),
            array('%d')
        );
    }

    /**
     * Returns the count of records in the database.
     *
     * @return null|string
     */
    public static function record_count() {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_reports";
        $sql .= self::get_where_condition();
        return $wpdb->get_var( $sql );
    }
    
    public static function all_record_count() {
        global $wpdb;

        $quiz_id = intval($_REQUEST['quiz']);
        $quiz_id = ' WHERE `quiz_id` = '.$quiz_id.' ';        
        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_reports".$quiz_id;

        return $wpdb->get_var( $sql );
    }
    
    public static function unread_records_count() {
        global $wpdb;

        $quiz_id = intval($_REQUEST['quiz']);
        $quiz_id = ' AND `quiz_id` = '.$quiz_id.' ';
        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_reports WHERE `read` = 0".$quiz_id;

        return $wpdb->get_var( $sql );
    }
    
    public function readed_records_count() {
        global $wpdb;

        $quiz_id = intval($_REQUEST['quiz']);
        $quiz_id = ' AND `quiz_id` = '.$quiz_id.' ';
        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_reports WHERE `read` = 1".$quiz_id;

        return $wpdb->get_var( $sql );
    }

    
    public static function users_count() {
        global $wpdb;
        global $wp_roles;
        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_reports WHERE quiz_id = ". $_GET['quiz']." AND user_id = 0";
        $guests = $wpdb->get_var( $sql );
        $sql = "SELECT COUNT(`{$wpdb->prefix}aysquiz_reports`.`id`) AS q, `{$wpdb->prefix}usermeta`.`meta_value` AS v
                FROM `{$wpdb->prefix}aysquiz_reports`
                JOIN `{$wpdb->prefix}usermeta` 
                    ON `{$wpdb->prefix}usermeta`.`user_id` = `{$wpdb->prefix}aysquiz_reports`.`user_id`
                WHERE `{$wpdb->prefix}aysquiz_reports`.`user_id` != 0
                  AND `{$wpdb->prefix}aysquiz_reports`.`quiz_id` = ".$_GET['quiz']."
                  AND `{$wpdb->prefix}usermeta`.`meta_key` = '{$wpdb->prefix}capabilities'
                GROUP BY `{$wpdb->prefix}usermeta`.`meta_value`";
        $results = $wpdb->get_results( $sql );
        $user_roles = array();
        $logged_in = 0;
        foreach($results as $key => $value){
            $role = maybe_unserialize($value->v);
            if(is_array($role)){
                while ($fruit_name = current($role)) {
                    if(array_key_exists(key($role), $wp_roles->roles)){
                        $user_roles[$key]['type'] = $wp_roles->roles[ key($role) ]['name'];
                    }
                    next($role);
                }
            }else{
                $user_roles[$key]['type'] = $wp_roles->roles[ key($role) ]['name'];
            }
            
            $user_roles[$key]['percent'] = $value->q;
            
            $logged_in += intval($value->q);
        }
        
        return array(
            "guests" => $guests,
            "loggedIn" => $logged_in,
            "userRoles" => $user_roles
        );
    }
    
    public static function users_count_by_score() {
        global $wpdb;
        global $wp_roles;
        $sql = "SELECT COUNT(`{$wpdb->prefix}aysquiz_reports`.`id`) AS count, `{$wpdb->prefix}aysquiz_reports`.`score` AS score
                FROM `{$wpdb->prefix}aysquiz_reports`
                WHERE `{$wpdb->prefix}aysquiz_reports`.`quiz_id` = ".$_GET['quiz']."
                GROUP BY score
                ORDER BY CAST(score AS UNSIGNED) ASC";
        $results = $wpdb->get_results( $sql );
        $sql = "SELECT intervals FROM `{$wpdb->prefix}aysquiz_quizes` WHERE id = ".$_GET['quiz'];
        $row = $wpdb->get_row( $sql );
        if(!empty($row)){
            $intervals_res = json_decode( $row->intervals );
        }else{
            $intervals_res = array();
        }
        $intervalner = array();
        foreach($intervals_res as $inter){
            $inter_key = $inter->interval_min . "-" . $inter->interval_max . "%";
            foreach($results as $res){
                $score = intval($res->score);
                if($score >= intval($inter->interval_min) && $score <= intval($inter->interval_max)){
                    $intervalner[$inter_key][] = intval($res->count);
                }
            }
            $intervalner[$inter_key] = !isset($intervalner[$inter_key]) ? 0 : array_sum($intervalner[$inter_key]);
        }
        
        $intervals = array();
        foreach($intervalner as $key => $interval){
            $intervals[] = array(
                'interval' => $key,
                'count' => $interval
            );
        }
                
        return array(
            'intervals' => $intervals,
            'scores' => $results
        );
    }

    /** Text displayed when no customer data is available */
    public function no_items() {
       echo  __('There are no results yet.', $this->plugin_name);
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
        switch ($column_name) {
            case 'user_id':
            case 'user_ip':
            case 'user_name':
            case 'user_email':
            case 'rate':
            case 'user_phone':
            case 'start_date':
            case 'end_date':
            case 'score':
            case 'id':
                return $item[$column_name];
                break;
            default:
                return print_r($item, true); //Show the whole array for troubleshooting purposes
        }
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
            '<input type="checkbox" class="ays_result_delete" name="bulk-delete[]" value="%s" />', $item['id']
        );
    }
    
    public function column_user_id( $item ) {
        global $wpdb;

        $delete_nonce = wp_create_nonce( $this->plugin_name . '-delete-each-result' );
        
        $result = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}aysquiz_reports WHERE id={$item['id']}", "ARRAY_A");
        $user_id = intval($item['user_id']);
        if($user_id == 0){
            $name = "Guest";
        }else{
            $user = get_userdata($user_id);
            $name = $user->data->display_name;
        }
        $title = sprintf( '<a href="javascript:void(0)" data-result="%d" class="%s">%s</a><input type="hidden" value="%d" class="ays_result_read">', absint( $item['id'] ), 'ays-show-results', $name, $item['read']);
        
        $actions = array(
            'view-details' => sprintf( '<a href="javascript:void(0);" data-result="%d" class="%s">%s</a>', absint( $item['id'] ), 'ays-show-results', __('Detailed report', $this->plugin_name)),
            'delete' => sprintf( '<a class="ays_confirm_del" data-message="this report" href="?page=%s&action=%s&quiz=%s&report=%s&_wpnonce=%s">%s</a>', esc_attr( $_REQUEST['page'] ), 'delete', $result['quiz_id'], absint( $item['id'] ), $delete_nonce, __('Delete', $this->plugin_name) )
        );
        return $title . $this->row_actions( $actions ) ;
    }    
    
    function column_rate( $item ) {
        global $wpdb;

        $delete_nonce = wp_create_nonce( $this->plugin_name . '-delete-result' );
        $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_rates WHERE report_id=".$item['id'];
        $results = $wpdb->get_row($sql, "ARRAY_A");

        if($results !== null){
            $margin_of_icon = "style='margin-left: 5px;'";
            $review = $results['review'];
            $score = $results['score'];
            $reason = htmlspecialchars(stripslashes(wpautop($review)));
            if($reason == ''){
                $reason = __("No review provided", $this->plugin_name);
            }
            $title = "<span href='javascript:void(0)' data-result='".absint( $item['id'] )."' class='ays-show-rate-avg'>
                        $score
                        <a class='ays_help' $margin_of_icon data-template='<div class=\"rate_tooltip tooltip\" role=\"tooltip\"><div class=\"arrow\"></div><div class=\"rate-tooltip-inner tooltip-inner\"></div></div>' data-toggle='tooltip' data-html='true' title='$reason'><i class='ays_fa ays_fa_info_circle'></i></a>                        
                </span>";
        }else{
            $options = json_decode($item['options'], true);
            $rate_id = (isset($options['rate_id'])) ? $options['rate_id'] : null;
            if($rate_id !== null){
                $margin_of_icon = "style='margin-left: 5px;'";
                $result = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}aysquiz_rates WHERE id={$rate_id}", "ARRAY_A");
                $review = $result['review'];
                $reason = htmlspecialchars(stripslashes(wpautop($review)));
                if($reason == ''){
                    $reason = __("No review provided", $this->plugin_name);
                }
                $score = $result['score'];
                $title = "<span href='javascript:void(0)' data-result='".absint( $item['id'] )."' class='ays-show-rate-avg'>
                            $score
                            <a class='ays_help' $margin_of_icon data-template='<div class=\"rate_tooltip tooltip\" role=\"tooltip\"><div class=\"arrow\"></div><div class=\"rate-tooltip-inner tooltip-inner\"></div></div>' data-toggle='tooltip' data-html='true' title='$reason'><i class='ays_fa ays_fa_info_circle'></i></a>
                    </span>";
            }else{
                $margin_of_icon = '';
                $reason = __("No rate provided", $this->plugin_name);
                $score = '';
                $title = "";
            }
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

    function column_score( $item ) {
        global $wpdb;
        $score = "<p style='text-align:center;'>" . $item['score'] . "%</p>";        
        return $score;
    }

    function column_points( $item ) {
        global $wpdb;
        $score = "";
        if($item['points'] && $item['max_points']){
            $score = "<p style='text-align:center;'>" . $item['points'] . "/" . $item['max_points'] . "</p>";
        }else{
            $options = json_decode($item['options'], true);
            $points = isset($options['user_points']) ? $options['user_points'] : false;
            $max_points = isset($options['max_points']) ? $options['max_points'] : false;
            if($points !== false && $max_points !== false){
                $score = "<p style='text-align:center;'>" . $points . "/" . $max_points . "</p>";
            }
        }
        return $score;
    }

    /**
     *  Associative array of columns
     *
     * @return array
     */
    public function get_columns() {
        $columns = array(
            'cb'            => '<input type="checkbox" />',
            'user_id'       => __( 'WP User', $this->plugin_name ),
            'user_ip'       => __( 'User IP', $this->plugin_name ),
            'user_name'     => __( 'User Name', $this->plugin_name ),
            'user_email'    => __( 'User Email', $this->plugin_name ),
            'user_phone'    => __( 'User Phone', $this->plugin_name ),
            'rate'          => __( 'Rate', $this->plugin_name ),
            'start_date'    => __( 'Start Date', $this->plugin_name ),
            'end_date'      => __( 'End Date', $this->plugin_name ),
            'duration'      => __( 'Duration', $this->plugin_name ),
            'score'         => __( 'Score', $this->plugin_name ),
            'points'        => __( 'Points', $this->plugin_name ),
            'id'            => __( 'ID', $this->plugin_name ),
            );
        return $columns;
    }

    public function ays_see_all_results(){
        global $wpdb;
        $sql = "UPDATE {$wpdb->prefix}aysquiz_reports SET `read`=1";
        $wpdb->get_results($sql, 'ARRAY_A');
    }

    /**
     * Columns to make sortable.
     *
     * @return array
     */
    public function get_sortable_columns() {
        $sortable_columns = array(
            'id'  => array( 'id', true ),
            'user_id'       => array( 'user_id', true ),
            'user_ip'       => array( 'user_ip', true ),
            'user_name'     => array( 'user_name', true ),
            'user_email'    => array( 'user_email', true ),
            'user_phone'    => array( 'user_phone', true ),
            'start_date'    => array( 'start_date', true ),
            'end_date'      => array( 'end_date', true ),
            'duraiton'      => array( 'duraiton', true ),
            'score'         => array( 'score', true ),
        );

        return $sortable_columns;
    }

    /**
     * Columns to make hidden.
     *
     * @return array
     */
    public function get_hidden_columns() {
        $sortable_columns = array(
            'user_phone',
            'end_date',
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
            'bulk-delete' => __( 'Delete', $this->plugin_name),
            'see-all' => __( 'Mark as read', $this->plugin_name)
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

        $per_page     = $this->get_items_per_page( 'quiz_each_results_per_page', 50 );
        $current_page = $this->get_pagenum();
        $total_items  = self::record_count();

        $this->set_pagination_args( array(
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page'    => $per_page //WE have to determine how many items to show on a page
        ) );

        $this->items = self::get_results( $per_page, $current_page);
    }

    public function process_bulk_action() {
        //Detect when a bulk action is being triggered...
        $message = 'deleted';
        if ( 'delete' === $this->current_action() ) {

            // In our file that handles the request, verify the nonce.
            $nonce = esc_attr( $_REQUEST['_wpnonce'] );

            if ( ! wp_verify_nonce( $nonce, $this->plugin_name . '-delete-each-result' ) ) {
                die( 'Go get a life script kiddies' );
            }
            else {
                self::delete_reports( absint( $_GET['report'] ) );

                // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
                // add_query_arg() return the current url

                $url = esc_url_raw( remove_query_arg(array('action','report', '_wpnonce') ) ) . '&status=' . $message;
                wp_redirect( $url );
            }

        }elseif('see-all' === $this->current_action()){
            $this->ays_see_all_results();
            $url = esc_url_raw( remove_query_arg(false) );
            wp_redirect( $url );
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

            $url = esc_url_raw( remove_query_arg(array('action', 'report', '_wpnonce') ) ) . '&status=' . $message;
            wp_redirect( $url );
        }
    }

    public function each_results_notices(){
        $status = (isset($_REQUEST['status'])) ? sanitize_text_field( $_REQUEST['status'] ) : '';

        if ( empty( $status ) )
            return;


        if ( 'deleted' == $status )
            $updated_message = esc_html( __( 'Report deleted.', $this->plugin_name ) );

        if ( empty( $updated_message ) )
            return;

        ?>
        <div class="notice notice-success is-dismissible">
            <p> <?php echo $updated_message; ?> </p>
        </div>
        <?php
    }

    public static function get_quizzes_count_by_day($day,$quiz_id=0){
        global $wpdb;

        $sql = "SELECT COUNT(*) AS `count` FROM {$wpdb->prefix}aysquiz_reports WHERE DATE(end_date)='$day' AND quiz_id =".$quiz_id;
        $count = $wpdb->get_row($sql);
        return $count->count;
    }

    public static function quiz_count_by_days($quiz_id=0){
        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_reports WHERE quiz_id =".$quiz_id;
        $result = $wpdb->get_results( $sql, 'ARRAY_A' );
        if(empty($result)){
            return array( array(
                "date" =>  date("Y-m-d", current_time('timestamp')),
                "value" =>  0
            ));
        }
        $data = array();
        $arr = array();
        foreach ($result as $value){
            $day = date("Y-m-d", strtotime($value['end_date']));
                if(!in_array($day, $arr)){
                    array_push($data,array(
                        "date" =>  $day,
                        "value" =>  self::get_quizzes_count_by_day($day,$quiz_id)
                    ));
                }
            array_push($arr,$day);
        }
       return $data;
    }

    public function quiz_each_question_correct_answers($quiz_id){
        global $wpdb;
        
        $sql = "SELECT r.options, q.question_ids 
                FROM {$wpdb->prefix}aysquiz_reports AS r
                INNER JOIN {$wpdb->prefix}aysquiz_quizes AS q
                ON r.quiz_id = q.id 
                AND q.id=". $quiz_id;
        $results = $wpdb->get_results( $sql, 'ARRAY_A' );
        
        $data_array = array();
        
        if(empty($results)){
            return $data_array;
        }
        
        $question_ids = $results[0]['question_ids'];
        if( $question_ids == "" ){
            return $data_array;
        }
        $question_ids = explode(",", $question_ids);
        
        foreach ($question_ids as $id) {
           $right_count = 0;
           $answer_count = 0;
           foreach ($results as $result){
               $data = json_decode($result['options'],true);

               $answers = isset( $data['correctness'] ) ? $data['correctness'] : array();

               if(array_key_exists('question_id_'.$id,$answers)) {
                   $answer_count++;
                   if($answers['question_id_'.$id] == true){
                       $right_count++;
                   }
               }
           }
            
           $get_question = $wpdb->get_results( "SELECT question FROM {$wpdb->prefix}aysquiz_questions WHERE id=".$id, 'ARRAY_A' );
           $persent = $answer_count > 0 ? round(($right_count*100)/$answer_count) : 0;
           $question = strlen(strip_tags($get_question[0]['question'])) > 35 ? substr(strip_tags($get_question[0]['question']),0,35)."..." : strip_tags($get_question[0]['question']);
           array_push($data_array,array(
               'question' => $question,
               'count' => "$persent",
               'fill' => "100"
           ));
        }

        return $data_array;

    }
}
