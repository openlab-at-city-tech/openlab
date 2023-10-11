<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://ays-pro.com/
 * @since      1.0.0
 *
 * @package    Quiz_Maker
 * @subpackage Quiz_Maker/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Quiz_Maker
 * @subpackage Quiz_Maker/public
 * @author     AYS Pro LLC <info@ays-pro.com>
 */
class Quiz_Maker_All_Results
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    protected $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;


    protected $settings;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string $plugin_name The name of the plugin.
     * @param      string $version The version of this plugin.
     */
    public function __construct($plugin_name, $version){

        $this->plugin_name = $plugin_name;
        $this->version = $version;

        add_shortcode('ays_all_results', array($this, 'ays_generate_all_results_method'));

        $this->settings = new Quiz_Maker_Settings_Actions($this->plugin_name);
    }

    public function enqueue_styles(){
        wp_enqueue_style($this->plugin_name . '-dataTable-min', AYS_QUIZ_PUBLIC_URL . '/css/quiz-maker-dataTables.min.css', array(), $this->version, 'all');
    }

    public function enqueue_scripts() {
        wp_enqueue_script( $this->plugin_name . '-datatable-min', AYS_QUIZ_PUBLIC_URL . '/js/quiz-maker-datatable.min.js', array('jquery'), $this->version, true);
        wp_enqueue_script( $this->plugin_name . '-all-results-public', AYS_QUIZ_PUBLIC_URL . '/js/all-results/all-results-public.js', array('jquery'), $this->version, true);
        wp_localize_script( $this->plugin_name . '-datatable-min', 'quizLangDataTableObj', array(
            "sEmptyTable"           => __( "No data available in table", $this->plugin_name ),
            "sInfo"                 => __( "Showing _START_ to _END_ of _TOTAL_ entries", $this->plugin_name ),
            "sInfoEmpty"            => __( "Showing 0 to 0 of 0 entries", $this->plugin_name ),
            "sInfoFiltered"         => __( "(filtered from _MAX_ total entries)", $this->plugin_name ),
            // "sInfoPostFix":          => __( "", $this->plugin_name ),
            // "sInfoThousands":        => __( ",", $this->plugin_name ),
            "sLengthMenu"           => __( "Show _MENU_ entries", $this->plugin_name ),
            "sLoadingRecords"       => __( "Loading...", $this->plugin_name ),
            "sProcessing"           => __( "Processing...", $this->plugin_name ),
            "sSearch"               => __( "Search:", $this->plugin_name ),
            // "sUrl":                  => __( "", $this->plugin_name ),
            "sZeroRecords"          => __( "No matching records found", $this->plugin_name ),
            "sFirst"                => __( "First", $this->plugin_name ),
            "sLast"                 => __( "Last", $this->plugin_name ),
            "sNext"                 => __( "Next", $this->plugin_name ),
            "sPrevious"             => __( "Previous", $this->plugin_name ),
            "sSortAscending"        => __( ": activate to sort column ascending", $this->plugin_name ),
            "sSortDescending"       => __( ": activate to sort column descending", $this->plugin_name ),
        ) );
    }

    public function get_user_reports_info( $show_publicly, $attr ){
        global $wpdb;

        $where = array();
        $where_condition = "";

        $current_user = wp_get_current_user();
        $id = $current_user->ID;

        if (! $show_publicly) {
            if($id == 0){
                return null;
            }
        }

        $category_id = (isset($attr['id']) && $attr['id'] != '') ? absint( sanitize_text_field($attr['id']) ) : null;

        if( !is_null($category_id) && $category_id > 0 ){
            $where[] = ' q.quiz_category_id = ' . $category_id;
        }

        if( ! empty($where) ){
            $where_condition = " WHERE " . implode( " AND ", $where );
        }

        $reports_table = $wpdb->prefix . "aysquiz_reports";
        $quizes_table = $wpdb->prefix . "aysquiz_quizes";
        $sql = "SELECT q.quiz_category_id,r.quiz_id, r.options, q.title, r.start_date, r.end_date, r.duration, r.score, r.points, r.id, r.user_name, r.user_email, r.user_id,
                    TIMESTAMPDIFF(second, r.start_date, r.end_date) AS duration_2
                FROM $reports_table AS r
                LEFT JOIN $quizes_table AS q
                ON r.quiz_id = q.id
                ". $where_condition ."
                ORDER BY r.id DESC";
        $results = $wpdb->get_results($sql, "ARRAY_A");

        return $results;

    }

    public function ays_all_results_html( $attr ){
        global $wpdb;

        $quizes_table  = esc_sql( $wpdb->prefix . "aysquiz_quizes" );

        $quiz_settings = $this->settings;
        $quiz_settings_options = ($quiz_settings->ays_get_setting('options') === false) ? json_encode(array()) : $quiz_settings->ays_get_setting('options');
        $quiz_set_option = json_decode(stripcslashes($quiz_settings_options), true);
        
        $quiz_set_option['ays_show_result_report'] = !isset($quiz_set_option['ays_show_result_report']) ? 'on' : $quiz_set_option['ays_show_result_report'];
        $show_result_report = isset($quiz_set_option['ays_show_result_report']) && $quiz_set_option['ays_show_result_report'] == 'on' ? true : false;

        // Show publicly
        $quiz_set_option['all_results_show_publicly'] = isset($quiz_set_option['all_results_show_publicly']) ? $quiz_set_option['all_results_show_publicly'] : 'off';
        $all_results_show_publicly = (isset($quiz_set_option['all_results_show_publicly']) && $quiz_set_option['all_results_show_publicly'] == "on") ? true : false;

        $results = $this->get_user_reports_info( $all_results_show_publicly, $attr );

        // SVG icon | Pass
        $pass_svg = '<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="green"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/></svg>';

        // SVG icon | Fail
        $fail_svg = '<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="brown"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z"/></svg>';

        $custom_fields = Quiz_Maker_Data::get_custom_fields_for_shortcodes();

        //User results
        $user_results_custom_fields = isset($custom_fields['user_results']) && !empty($custom_fields['user_results']) ? $custom_fields['user_results'] : array();

        $default_all_results_columns = array(
            'user_name'  => 'user_name',
            'quiz_name'  => 'quiz_name',
            'start_date' => 'start_date',
            'end_date'   => 'end_date',
            'duration'   => 'duration',
            'score'      => 'score',
            'status'     => '',
            'user_email' => '',
            // 'details'    => 'details',
        );
        
        if( !empty($user_results_custom_fields) ){
            foreach ($user_results_custom_fields as $custom_field_key => $custom_field) {
                $default_all_results_columns[$custom_field_key] = $custom_field_key;
            }
        }

        $all_results_columns = (isset( $quiz_set_option['all_results_columns'] ) && !empty($quiz_set_option['all_results_columns']) ) ? $quiz_set_option['all_results_columns'] : $default_all_results_columns;
        $all_results_columns_order = (isset( $quiz_set_option['all_results_columns_order'] ) && !empty($quiz_set_option['all_results_columns_order']) ) ? $quiz_set_option['all_results_columns_order'] : $default_all_results_columns;

        $all_results_columns_order_arr = $all_results_columns_order;

        foreach( $default_all_results_columns as $key => $value ){
            if( !isset( $all_results_columns[$key] ) ){
                $all_results_columns[$key] = '';
            }

            if( !isset( $all_results_columns_order[$key] ) ){
                $all_results_columns_order[$key] = $key;
            }

            if ( ! in_array( $key , $all_results_columns_order_arr) ) {
                $all_results_columns_order_arr[] = $key;
            }
        }

        foreach( $all_results_columns_order as $key => $value ){
            if( !isset( $all_results_columns[$key] ) ){
                if( isset( $all_results_columns[$value] ) ){
                    $all_results_columns_order[$value] = $value;
                }
                unset( $all_results_columns_order[$key] );
            }
        }

        foreach ($all_results_columns_order_arr  as $key => $value) {
            if( isset( $all_results_columns_order[$value] ) ){
                $all_results_columns_order_arr[$value] = $value;
            }

            if ( is_int( $key ) ) {
                unset( $all_results_columns_order_arr[$key] );
            }
        }

        $all_results_columns_order = $all_results_columns_order_arr;

        $default_all_results_column_names = array(
            "user_name"     => __( 'User name', $this->plugin_name ),
            "quiz_name"     => __( 'Quiz name', $this->plugin_name ),
            "start_date"    => __( 'Start date', $this->plugin_name ),
            "end_date"      => __( 'End date', $this->plugin_name ),
            "duration"      => __( 'Duration', $this->plugin_name ),
            "score"         => __( 'Score', $this->plugin_name ),
            "user_email"    => __( 'Email', $this->plugin_name ),
            // "details"       => __( 'Details', $this->plugin_name )
        );

        if( !empty($user_results_custom_fields) ){
            foreach ($user_results_custom_fields as $custom_field_key => $custom_field_value) {
                $default_all_results_column_names[$custom_field_key] = $custom_field_value;
            }
        }

        $ays_default_header_value = array(
            "user_name"     => "<th style='width:20%;'>" . __( "User Name", $this->plugin_name ) . "</th>",
            "quiz_name"     => "<th style='width:20%;'>" . __( "Quiz Name", $this->plugin_name ) . "</th>",
            "start_date"    => "<th style='width:15%;'>" . __( "Start", $this->plugin_name ) . "</th>",
            "end_date"      => "<th style='width:15%;'>" . __( "End", $this->plugin_name ) . "</th>",
            "duration"      => "<th style='width:10%;'>" . __( "Duration", $this->plugin_name ) . "</th>",
            "score"         => "<th style='width:10%;'>" . __( "Score", $this->plugin_name ) . "</th>",
            "status"        => "<th style='width:10%;'>" . __( "Status", $this->plugin_name ) . "</th>",
            "user_email"    => "<th style='width:10%;'>" . __( "Email", $this->plugin_name ) . "</th>",
            // "details"       => "<th style='width:20%;'>" . __( "Details", $this->plugin_name ) . "</th>"
        );

        if( !empty($user_results_custom_fields) ){
            foreach ($user_results_custom_fields as $custom_field_key => $custom_field_value) {
                $ays_default_header_value[$custom_field_key] = "<th style='width:10%;'>" .$custom_field_value. "</th>";
            }
        }

        if($results === null){
            $all_results_html = "<p style='text-align: center;font-style:italic;'>" . __( "You must log in to see your results.", $this->plugin_name ) . "</p>";
            return $all_results_html;
        }
        
        if( empty( $results ) ){
            $all_results_html = "<p style='text-align: center;font-style:italic;'>" . __( "There are no results yet.", $this->plugin_name ) . "</p>";
            return $all_results_html;
        }

        $all_results_html = "<div class='ays-quiz-all-results-container'>
        <table id='ays-quiz-all-result-score-page' class='display'>
        <thead>
        <tr>";
        
        foreach ($all_results_columns_order as $key => $value) {
            if ( isset($all_results_columns[$value]) && $all_results_columns[$value] != '' && isset( $ays_default_header_value[$value] )) {
                $all_results_html .= $ays_default_header_value[$value];
            }
        }
        
        $all_results_html .= "</tr></thead>";

        $quiz_pass_score_arr = array();
        $quiz_pass_score_type_arr = array();
        foreach($results as $key => $result){
            $id         = isset($result['id']) ? $result['id'] : null;
            $quiz_id    = isset($result['quiz_id']) ? absint($result['quiz_id']) : null;
            $user_id    = isset($result['user_id']) ? intval($result['user_id']) : 0;
            $title      = isset($result['title']) ? $result['title'] : "";
            $start_date = date_create($result['start_date']);
            $start_date = date_format($start_date, 'H:i:s M d, Y');
            $end_date   = date_create($result['end_date']);
            $end_date   = date_format($end_date, 'H:i:s M d, Y');
            $duration   = isset($result['duration']) ? $result['duration'] : 0;
            $score      = isset($result['score']) ? $result['score'] : 0;
            $points     = isset($result['points']) ? $result['points'] : 0;
            $user_email = isset($result['user_email']) ? $result['user_email'] : null;

            if ($duration == null) {
                $duration = isset($result['duration_2']) ? $result['duration_2'] : 0;
            }

            $start_date_for_ordering = strtotime($result['start_date']);
            $end_date_for_ordering = strtotime($result['end_date']);
            $duration_for_ordering = $duration;

            $duration = Quiz_Maker_Data::secondsToWords($duration);
            if ($duration == '') {
                $duration = '0 ' . __( 'second' , $this->plugin_name );
            }

            if ($user_id == 0) {
                $user_name = (isset($result['user_name']) && $result['user_name'] != '') ? $result['user_name'] : __('Guest', $this->plugin_name);
            }else{
                $user_name = (isset($result['user_name']) && $result['user_name'] != '') ? $result['user_name'] : '';
                if($user_name == ''){
                    $user = get_userdata( $user_id );
                    if($user !== false){
                        $user_name = $user->data->display_name ? $user->data->display_name : $user->user_login;
                    }else{
                        continue;
                    }
                }
            }

            $status     = '';
            $pass_score = 0;
            $quiz_pass_score_type = 'percentage';
            if ( ! is_null( $quiz_id ) || ! empty( $quiz_id ) ) {
                if ( ! array_key_exists( $quiz_id , $quiz_pass_score_arr ) ) {

                    $sql = "SELECT options FROM " . $quizes_table . " WHERE id=" . intval( $quiz_id );
                    $quiz_options = $wpdb->get_var( $sql );
                    $quiz_options = $quiz_options != '' ? json_decode( $quiz_options, true ) : array();
                    $pass_score = isset( $quiz_options['pass_score'] ) && $quiz_options['pass_score'] != '' ? absint( $quiz_options['pass_score'] ) : 0;

                    // Quiz Pass Score type
                    $quiz_pass_score_type = (isset($quiz_options['quiz_pass_score_type']) && $quiz_options['quiz_pass_score_type'] != '') ? sanitize_text_field( $quiz_options['quiz_pass_score_type'] ) : 'percentage';

                    $quiz_pass_score_arr[ $quiz_id ] = $pass_score;
                    $quiz_pass_score_type_arr[ $quiz_id ] = $quiz_pass_score_type;

                } else {
                    $pass_score = ( isset( $quiz_pass_score_arr[ $quiz_id ] ) && $quiz_pass_score_arr[ $quiz_id ] != '' ) ? absint( $quiz_pass_score_arr[ $quiz_id ] ) : 0;

                    $quiz_pass_score_type = ( isset( $quiz_pass_score_type_arr[ $quiz_id ] ) && $quiz_pass_score_type_arr[ $quiz_id ] != '' ) ? sanitize_text_field( $quiz_pass_score_type_arr[ $quiz_id ] ) : 'percentage';
                }

                switch ( $quiz_pass_score_type ) {
                    case 'point':
                        $user_score = floatval( $points );
                        break;
                    
                    case 'percentage':
                    default:
                        $user_score = absint( $score );
                        break;
                }

                $status = '';
                if( $pass_score != 0 ){
                    if( $user_score >= $pass_score ){
                        $status .= "<div class='ays-quiz-score-column-check-box'>";
                            $status .= $pass_svg;
                            $status .= "<span class='ays-quiz-score-column-check'> " . __( "Passed", $this->plugin_name ) . "</span>";
                        $status .= "</div>";
                    }else{
                        $status .= "<div class='ays-quiz-score-column-check-box'>";
                            $status .= $fail_svg;
                            $status .= "<span class='ays-quiz-score-column-times'> " . __( "Failed", $this->plugin_name ) . "</span>";
                        $status .= "</div>";

                    }
                }
            }

            $ays_default_html_order = array(
                "user_name"     => "<td>$user_name</td>",
                "quiz_name"     => "<td>$title</td>",
                "start_date"    => "<td data-order='". $start_date_for_ordering ."'>$start_date</td>",
                "end_date"      => "<td data-order='". $end_date_for_ordering ."'>$end_date</td>",
                "duration"      => "<td data-order='". $duration_for_ordering ."' class='ays-quiz-duration-column'>$duration</td>",
                "score"         => "<td class='ays-quiz-score-column'>$score%</td>",
                "status"        => "<td class='ays-quiz-status-column'>$status</td>",
                "user_email"    => "<td class='ays-quiz-user-email-column'>$user_email</td>",

                // "details" => "<td><button type='button' data-id='".$id."' class='ays-quiz-user-sqore-pages-details'>".__("Details", $this->plugin_name)."</button></td>"
            );

            $attribute_options = (isset($result['options']) && $result['options'] != '') ? json_decode( $result['options'], true ) : '';

            $attribute_info = array();
            if($attribute_options != ''){
                $attribute_info = (isset($attribute_options['attributes_information']) && !empty( $attribute_options['attributes_information'] )) ? $attribute_options['attributes_information'] : array();
            }

            if( !empty($user_results_custom_fields) ){
                foreach ($user_results_custom_fields as $custom_field_key => $custom_field_value) {
                    if(isset( $attribute_info[$custom_field_value] ) && $attribute_info[$custom_field_value] != ''){
                        $ays_default_html_order[$custom_field_key] = "<td style='width:10%;'>" .$attribute_info[$custom_field_value]. "</td>";
                    }else{
                        $ays_default_html_order[$custom_field_key] = "<td style='width:10%;'></td>";
                    }
                }
            }

            $all_results_html .= "<tr>";
            foreach ($all_results_columns_order as $key => $value) {
                if ( isset($all_results_columns[$value]) && $all_results_columns[$value] != '' && isset( $ays_default_html_order[$value] ) ) {
                    $all_results_html .= $ays_default_html_order[$value];
                }
            }
            $all_results_html .= "</tr>";
        }

        $all_results_html .= "</table>
            </div>";
        
        return $all_results_html;
    }

    public function ays_generate_all_results_method( $attr ) {

        $this->enqueue_styles();
        $this->enqueue_scripts();
        $all_results_html = $this->ays_all_results_html( $attr );
        $all_results_html = Quiz_Maker_Data::ays_quiz_translate_content( $all_results_html );
        
        return str_replace(array("\r\n", "\n", "\r"), '', $all_results_html);
    }


}
