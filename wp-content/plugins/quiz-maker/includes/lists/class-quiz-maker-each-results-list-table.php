<?php
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
        $users_sql = "SELECT {$wpdb->prefix}aysquiz_reports.user_id
                      FROM {$wpdb->prefix}aysquiz_reports 
                      WHERE quiz_id = " . $_GET['quiz'] . "
                      GROUP BY user_id";
        $users_res = $wpdb->get_results($users_sql, 'ARRAY_A');
        $users = array();
        $quiz_id = null;
        $user_id = null;
        if( isset( $_GET['filterbyuser'] ) ){
            $user_id = intval($_GET['filterbyuser']);
        }

        $clear_url = "?page=" . $_REQUEST['page'] . "&quiz=" . $_REQUEST['quiz'];
        ?>
        <div id="user-filter-div-<?php echo $which; ?>" class="alignleft actions bulkactions">
            <select name="filterbyuser-<?php echo $which; ?>" id="bulk-action-selector-<?php echo $which; ?>">
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
                            if($wpuser !== false){
                                $name = $wpuser->data->display_name;
                            }else{
                                continue;
                            }
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
            <input type="button" id="doaction-<?php echo $which; ?>" class="user-filter-apply-<?php echo $which; ?> button" value="Filter">
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
            "all" => "<a ".$selected_all." href='".$link."'>". __("All", $this->plugin_name) ." (".$all_count.")</a>",
            "readed" => "<a ".$selected_1." href='".$link."&fstatus=1'>". __("Read", $this->plugin_name) ." (".$published_count.")</a>",
            "unreaded"   => "<a ".$selected_0." href='".$link."&fstatus=0'>". __("Unread", $this->plugin_name) . " (".$unpublished_count.")</a>"
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
            $where[ 's' ] = ' ( ' . implode(' OR ', $s) . ' ) ';
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

        $wpuser = false;
        if( isset( $_REQUEST['filterbyuser'] ) && $_REQUEST['filterbyuser'] != '' ){
            // && $_REQUEST['wpuser'] > 0
            $user_id = intval( $_REQUEST['filterbyuser'] );
            $where[] = ' `user_id` = '.$user_id.' ';
            $wpuser = true;
        }
        
        if( isset( $_REQUEST['quiz'] ) ){
            $quiz_id = intval($_REQUEST['quiz']);
            $where[] = ' `quiz_id` = '.$quiz_id.' ';
        }
        
        if( $search ){
            if( strtolower( trim( $search ) ) == 'guest' && !$wpuser ){
                unset( $where['s'] );
                $where[] = ' `user_id` = 0 ';
            }
        }

        $where[] = ' `status` = "finished" ';

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
        Quiz_Maker_Data::ays_delete_report_certificate( $id, 'report' );

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
        $quiz_id = ' WHERE `quiz_id` = '.$quiz_id.' AND `status` = "finished" ';
        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_reports".$quiz_id;

        return $wpdb->get_var( $sql );
    }
    
    public static function unread_records_count() {
        global $wpdb;

        $quiz_id = intval($_REQUEST['quiz']);
        $quiz_id = ' AND `quiz_id` = '.$quiz_id.' AND `status` = "finished" ';
        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_reports WHERE `read` = 0".$quiz_id;

        return $wpdb->get_var( $sql );
    }
    
    public function readed_records_count() {
        global $wpdb;

        $quiz_id = intval($_REQUEST['quiz']);
        $quiz_id = ' AND `quiz_id` = '.$quiz_id.' AND `status` = "finished" ';
        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_reports WHERE `read` = 1".$quiz_id;

        return $wpdb->get_var( $sql );
    }

    
    public static function users_count() {
        global $wpdb;
        global $wp_roles;
        $db_prefix = is_multisite() ? $wpdb->base_prefix : $wpdb->prefix;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_reports WHERE quiz_id = ". $_GET['quiz']." AND user_id = 0";
        $guests = $wpdb->get_var( $sql );
        $sql = "SELECT COUNT(r.`id`) AS q, um.`meta_value` AS v
                FROM `{$wpdb->prefix}aysquiz_reports` AS r
                JOIN `{$db_prefix}usermeta` AS um
                    ON um.`user_id` = r.`user_id`
                WHERE r.`user_id` != 0
                  AND r.`quiz_id` = ".$_GET['quiz']."
                  AND um.`meta_key` = '{$db_prefix}capabilities'
                GROUP BY um.`meta_value`";
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

    public static function question_category_statistics() {
        global $wpdb;
        $quizes_table = $wpdb->prefix . 'aysquiz_quizes';
        $quizes_questions_table = $wpdb->prefix . 'aysquiz_questions';
        $quizes_questions_cat_table = $wpdb->prefix . 'aysquiz_categories';
        $sql = "SELECT question_ids FROM {$quizes_table} WHERE id = ".$_GET['quiz'];
        $results = $wpdb->get_var( $sql);
        $questions_ids = array();
        $questions_counts = array();
        $questions_cat_list = array();
        if($results != ''){
            $results = explode("," , $results);
            foreach ($results as $key){
                $questions_ids[$key] = 0;
                $questions_counts[$key] = 0;

                $sql = "SELECT q.category_id, c.title FROM {$quizes_questions_table} AS q JOIN {$quizes_questions_cat_table} AS c ON q.category_id = c.id WHERE q.id = {$key}; ";
                $questions_cat_list[$key] = $wpdb->get_row( $sql);
            }
        }

        $quizes_reports_table = $wpdb->prefix . 'aysquiz_reports';
        $sql = "SELECT options FROM {$quizes_reports_table} WHERE quiz_id =".$_GET['quiz']." AND `status` = 'finished' ";
        $report = $wpdb->get_results( $sql, ARRAY_A );
        if(! empty($report)){
            foreach ($report as $key){
                $report = json_decode($key["options"]);
                $questions = $report->correctness;
                foreach ($questions as $i => $v){
                    $q = (int) substr($i ,12);
                    if(isset($questions_ids[$q])) {
                        if ($v) {
                            $questions_ids[$q]++;
                        }

                        $questions_counts[$q]++;
                    }
                }
            }
        }

        $q_cat_list = array();
        $q_cat_title = array();
        foreach ($questions_cat_list as $key_id => $val ) {
            $val_arr = (array) $val;
            if(isset($val_arr['category_id'])){
                if (!array_key_exists($val_arr['category_id'], $q_cat_list)) {
                    $q_cat_list[$val_arr['category_id']][] = $key_id;
                    $q_cat_title[$val_arr['category_id']] = $val_arr['title'];
                }else{
                    $q_cat_list[$val_arr['category_id']][] = $key_id;
                    $q_cat_title[$val_arr['category_id']] = $val_arr['title'];
                }
            }
        }

        $q_cat_lists = array('percent'=>'', 'cat_name'=>'');
        $q_cats_lists = array();
        foreach ($q_cat_list as $key1 => $value1) {
            $gumar_poqr = 0;
            $gumar_mec = 0;
            foreach ($value1 as $key2 => $value2) {
                $gumar_poqr += $questions_ids[$value2];
                $gumar_mec += $questions_counts[$value2];
            }
            if($gumar_mec == 0){
                $tokos = 0;
            }else{
                $tokos = round(($gumar_poqr*100)/$gumar_mec, 1);
            }
            $q_cat_lists['percent'] = $tokos."%";
            $q_cat_lists['cat_name'] = $q_cat_title[$key1];
            $q_cats_lists[] = $q_cat_lists;
        }

        return $q_cats_lists;
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
        $sql = "SELECT intervals FROM `{$wpdb->prefix}aysquiz_quizes` WHERE intervals != '' AND id = ".$_GET['quiz'];
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
            case 'unique_code':
            case 'certificate':
            case 'status':
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
        $class_red = '';
        if($user_id == 0){
            $name = "Guest";
        }else{
            $name = '';
            $user = get_userdata($user_id);
            if($user !== false){
                $name = $user->data->display_name;
            }else{
                $name = __( "Deleted user", $this->plugin_name );
                $class_red = ' ays_color_red ';
            }
        }
        $title = sprintf( '<a href="javascript:void(0)" data-result="%d" class="%s">%s</a><input type="hidden" value="%d" class="ays_result_read">', absint( $item['id'] ), 'ays-show-results'.$class_red, $name, $item['read']);
        
        $actions = array(
            'view-details' => sprintf( '<a href="javascript:void(0);" data-result="%d" class="%s">%s</a>', absint( $item['id'] ), 'ays-show-results', __('Detailed report', $this->plugin_name)),
            'delete' => sprintf( '<a class="ays_confirm_del" data-message="this report" href="?page=%s&action=%s&quiz=%s&report=%s&_wpnonce=%s">%s</a>', esc_attr( $_REQUEST['page'] ), 'delete', $result['quiz_id'], absint( $item['id'] ), $delete_nonce, __('Delete', $this->plugin_name) )
        );
        return $title . $this->row_actions( $actions ) ;
    }    
    
    public function column_rate( $item ) {
        global $wpdb;

        $delete_nonce = wp_create_nonce( $this->plugin_name . '-delete-result' );
        $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_rates WHERE report_id=".$item['id'];
        $results = $wpdb->get_row($sql, "ARRAY_A");

        if($results !== null){
            $margin_of_icon = "style='margin-left: 5px;'";
            $review = $results['review'];
            $score = $results['score'];
            $reason = esc_attr(stripslashes(wpautop($review)));
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
                $reason = esc_attr(stripslashes(wpautop($review)));
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
    
    public function column_duration( $item ) {
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

    public function column_score( $item ) {
        global $wpdb;
        $score = "<p>" . round( floatval( $item['score'] ), 2 ) . "%</p>";
        return $score;
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

    public function column_certificate( $item ) {
        global $wpdb;
        $options = (isset($item['options']) && $item['options'] != '') ? json_decode($item['options'], true) : array();
        $cert_file_name = isset($options['cert_file_name']) && $options['cert_file_name'] != '' ? $options['cert_file_name'] : '';
        $cert_file_url = isset($options['cert_file_url']) && $options['cert_file_url'] != '' ? $options['cert_file_url'] : '';
        $cert_file_path = isset($options['cert_file_path']) && $options['cert_file_path'] != '' ? $options['cert_file_path'] : '';
        if(file_exists($cert_file_path)){
            $cert_html = "<a class='ays_result_certificate' href='".$cert_file_url."' target='_blank'>" . __( 'Open', $this->plugin_name ) . "</a>";
            $cert_html .= "<a class='ays_result_certificate' href='".$cert_file_url."' target='_blank' download>" . __( 'Download', $this->plugin_name ) . "</a>";
            return $cert_html;
        }
        return '<p style="text-align:center;">-</p>';
    }

    public function column_status( $item ) {
        global $wpdb;
        if( !isset( $item['quiz_id'] ) || intval( $item['quiz_id'] ) == 0 ){
            return '';
        }

        $sql = "SELECT options FROM " . $wpdb->prefix . "aysquiz_quizes WHERE id=" . intval( $item['quiz_id'] );
        $quiz_options = $wpdb->get_var( $sql );
        $quiz_options = $quiz_options != '' ? json_decode( $quiz_options, true ) : array();
        $pass_score = isset( $quiz_options['pass_score'] ) && $quiz_options['pass_score'] != '' ? absint( $quiz_options['pass_score'] ) : 0;
        $score = absint( $item['score'] );

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
            'unique_code'   => __( 'Unique Code', $this->plugin_name ),
            'certificate'   => __( 'Certificate', $this->plugin_name ),
            'status'        => __( 'Status', $this->plugin_name ),
            'id'            => __( 'ID', $this->plugin_name ),
            );
        return $columns;
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
            'unique_code'   => array( 'unique_code', true ),
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
            'unique_code',
            'certificate',
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
            'send-cert' => __( 'Resend certificate', $this->plugin_name),
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

        }elseif($this->current_action() == "send-cert"){
            $mail_ids = isset( $_POST['bulk-delete'] ) ? $_POST['bulk-delete'] : '';
            $mail_ids = isset($mail_ids) && !empty($mail_ids) ? implode("," , $mail_ids) : array();
            if(isset($mail_ids) && $mail_ids != ''){
                $this->send_cert_to_user_x($mail_ids);
            }
        }


        // If the mark-as-read bulk action is triggered
        if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'mark-as-read' ) || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'mark-as-read' ) ) {

            $delete_ids = esc_sql( $_POST['bulk-delete'] );

            // loop over the array of record IDs and delete them
            foreach ( $delete_ids as $id ) {
                self::mark_as_read_reports( $id );
            }

            // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
            // add_query_arg() return the current url

            $url = esc_url_raw( remove_query_arg(array('action', 'report', '_wpnonce') ) );

            $message = 'marked-as-read';
            $url = add_query_arg( array(
                'status' => $message,
            ), $url );
            wp_redirect( $url );
        }

        // If the mark-as-unread bulk action is triggered
        if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'mark-as-unread' ) || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'mark-as-unread' ) ) {

            $delete_ids = esc_sql( $_POST['bulk-delete'] );

            // loop over the array of record IDs and delete them
            foreach ( $delete_ids as $id ) {
                self::mark_as_unread_reports( $id );
            }

            // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
            // add_query_arg() return the current url

            $url = esc_url_raw( remove_query_arg(array('action', 'report', '_wpnonce') ) );

            $message = 'marked-as-unread';
            $url = add_query_arg( array(
                'status' => $message,
            ), $url );

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

    public function send_cert_to_user_x( $report_ids ){
        global $wpdb;
        $reports_table = $wpdb->prefix."aysquiz_reports";
        $mails = '';
        $certificate = array();

        $nsite_url_base = get_site_url();
        $nsite_url_replaced = str_replace( array( 'http://', 'https://' ), '', $nsite_url_base );
        $nsite_url = trim( $nsite_url_replaced, '/' );

        $uname = 'Quiz Maker';
        $nfrom = "From: " . $uname . " <quiz_maker@".$nsite_url.">";

        $headers  = $nfrom."\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $message  = "Certificate";
        $subject  = "Quiz Maker";
        if(!empty($report_ids)){
            $sql = "SELECT * FROM ".$reports_table." WHERE id IN (".$report_ids.")";
            $results = $wpdb->get_results($sql , ARRAY_A);
            foreach($results as $key => $value){
                $options = isset($value['options']) && $value['options'] != '' ? json_decode($value['options'] , true) : array();
                $mails = isset($value['user_email']) && $value['user_email'] != '' ? $value['user_email'] : '';
                if(isset($mails) && $mails == ''){
                    continue;
                }
                $quiz_id = isset($value['quiz_id']) && $value['quiz_id'] != '' ? intval($value['quiz_id']) : '';
                if($quiz_id != ''){
                    $this_quiz_id = Quiz_Maker_Data::get_quiz_by_id($quiz_id);
                    $subject = isset($this_quiz_id['title']) && $this_quiz_id['title'] != '' ? $this_quiz_id['title'] : '';

                }
                if(!empty($options)){
                    $certificate['cert_file_path'] = isset($options['cert_file_path']) &&  $options['cert_file_path'] != '' ? $options['cert_file_path'] : '';
                }
                if(isset($certificate['cert_file_path']) && $certificate['cert_file_path'] != ''){
                    $sended = wp_mail($mails, $subject, $message, $headers, $certificate['cert_file_path']);
                }
            }
        }
    }

    public function each_results_notices(){
        $status = (isset($_REQUEST['status'])) ? sanitize_text_field( $_REQUEST['status'] ) : '';

        if ( empty( $status ) )
            return;


        if ( 'deleted' == $status )
            $updated_message = esc_html( __( 'Report deleted.', $this->plugin_name ) );
        elseif ( 'seen' == $status )
            $updated_message = esc_html( __( 'Selected reports have been marked as read.', $this->plugin_name ) );

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

            $get_question = $wpdb->get_row( "SELECT question FROM {$wpdb->prefix}aysquiz_questions WHERE id=".$id, 'ARRAY_A' );
            $persent = $answer_count > 0 ? round(($right_count*100)/$answer_count) : 0;
//           $question = strlen(strip_tags($get_question[0]['question'])) > 35 ? substr(strip_tags($get_question[0]['question']),0,35)."..." : strip_tags($get_question[0]['question']);
            $question = Quiz_Maker_Admin::ays_restriction_string("word", strip_tags($get_question['question']), 4);
            $question = html_entity_decode(stripslashes($question), ENT_COMPAT);
            array_push($data_array,array(
                'question' => $question,
                'count' => "$persent",
                'fill' => "100"
            ));
        }

        return $data_array;

    }
}
