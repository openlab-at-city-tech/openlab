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
class Quiz_Maker_Quiz_All_Results
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

        add_shortcode('ays_quiz_all_results', array($this, 'ays_generate_quiz_all_results_method'));

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

    public function get_user_reports_info( $quiz_id, $show_publicly ){
        global $wpdb;

        $current_user = wp_get_current_user();
        $id = $current_user->ID;

        if ( empty($quiz_id) || is_null($quiz_id) ) {
            return null;
        }

         if (! $show_publicly) {
            if($id == 0){
                return null;
            }
         }

        $reports_table = $wpdb->prefix . "aysquiz_reports";
        $quizes_table = $wpdb->prefix . "aysquiz_quizes";
        $sql = "SELECT q.title, r.options, r.start_date, r.end_date, r.duration, r.score, r.id, r.user_name, r.user_id,
                       TIMESTAMPDIFF(second, r.start_date, r.end_date) AS duration_2
                FROM $reports_table AS r
                LEFT JOIN $quizes_table AS q
                ON r.quiz_id = q.id
                WHERE r.quiz_id = ". $quiz_id ."
                ORDER BY r.id DESC";
        $results = $wpdb->get_results($sql, "ARRAY_A");

        return $results;

    }

    public function ays_quiz_all_results_html( $quiz_id ){

        $quiz_settings = $this->settings;
        $quiz_settings_options = ($quiz_settings->ays_get_setting('options') === false) ? json_encode(array()) : $quiz_settings->ays_get_setting('options');
        $quiz_set_option = json_decode(stripcslashes($quiz_settings_options), true);

        $quiz_set_option['ays_show_result_report'] = !isset($quiz_set_option['ays_show_result_report']) ? 'on' : $quiz_set_option['ays_show_result_report'];
        $show_result_report = isset($quiz_set_option['ays_show_result_report']) && $quiz_set_option['ays_show_result_report'] == 'on' ? true : false;

        // Show publicly
        $quiz_set_option['quiz_all_results_show_publicly'] = isset($quiz_set_option['quiz_all_results_show_publicly']) ? $quiz_set_option['quiz_all_results_show_publicly'] : 'off';
        $quiz_all_results_show_publicly = (isset($quiz_set_option['quiz_all_results_show_publicly']) && $quiz_set_option['quiz_all_results_show_publicly'] == "on") ? true : false;


        $results = $this->get_user_reports_info( $quiz_id, $quiz_all_results_show_publicly );

        $custom_fields = Quiz_Maker_Data::get_custom_fields_for_shortcodes();

        //Quiz results
        $quiz_results_custom_fields = isset($custom_fields['quiz_results']) && !empty($custom_fields['quiz_results']) ? $custom_fields['quiz_results'] : array();

        $default_quiz_all_results_columns = array(
            'user_name'  => 'user_name',
            'start_date' => 'start_date',
            'end_date'   => 'end_date',
            'duration'   => 'duration',
            'score'      => 'score',
        );

        if( !empty($quiz_results_custom_fields) ){
            foreach ($quiz_results_custom_fields as $custom_field_key => $custom_field) {
                $default_quiz_all_results_columns[$custom_field_key] = $custom_field_key;
            }
        }

        $quiz_all_results_columns = (isset( $quiz_set_option['quiz_all_results_columns'] ) && !empty($quiz_set_option['quiz_all_results_columns']) ) ? $quiz_set_option['quiz_all_results_columns'] : $default_quiz_all_results_columns;
        $quiz_all_results_columns_order = (isset( $quiz_set_option['quiz_all_results_columns_order'] ) && !empty($quiz_set_option['quiz_all_results_columns_order']) ) ? $quiz_set_option['quiz_all_results_columns_order'] : $default_quiz_all_results_columns;

        $ays_default_header_value = array(
            "user_name"     => "<th style='width:20%;'>" . __( "User Name", $this->plugin_name ) . "</th>",
            "start_date"    => "<th style='width:17%;'>" . __( "Start", $this->plugin_name ) . "</th>",
            "end_date"      => "<th style='width:17%;'>" . __( "End", $this->plugin_name ) . "</th>",
            "duration"      => "<th style='width:13%;'>" . __( "Duration", $this->plugin_name ) . "</th>",
            "score"         => "<th style='width:13%;'>" . __( "Score", $this->plugin_name ) . "</th>",
        );

        if( !empty($quiz_results_custom_fields) ){
            foreach ($quiz_results_custom_fields as $custom_field_key => $custom_field_value) {
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

        $all_results_html = "<div class='ays-individual-quiz-all-results-container'>
        <table class='ays-individual-quiz-all-result-score-page' class='display'>
        <thead>
        <tr>";

        foreach ($quiz_all_results_columns_order as $key => $value) {
            if (isset($quiz_all_results_columns[$value]) && isset( $ays_default_header_value[$value] )) {
                $all_results_html .= $ays_default_header_value[$value];
            }
        }

        $all_results_html .= "</tr></thead>";


        foreach($results as $key => $result){
            $id         = isset($result['id']) ? $result['id'] : null;
            $user_id    = isset($result['user_id']) ? intval($result['user_id']) : 0;
            $start_date = date_create($result['start_date']);
            $start_date = date_format($start_date, 'H:i:s M d, Y');
            $end_date   = date_create($result['end_date']);
            $end_date   = date_format($end_date, 'H:i:s M d, Y');
            $score      = isset($result['score']) ? $result['score'] : 0;
            $duration   = (isset($result['duration']) && ! is_null($result['duration']) ) ? $result['duration'] : null;
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
                    $user = get_user_by('id', $user_id);
                    $user_name = $user->data->display_name ? $user->data->display_name : $user->user_login;
                }
            }
            $ays_default_html_order = array(
                "user_name" => "<td>$user_name</td>",
                "start_date" => "<td data-order='". $start_date_for_ordering ."'>$start_date</td>",
                "end_date" => "<td data-order='". $end_date_for_ordering ."'>$end_date</td>",
                "duration" => "<td data-order='". $duration_for_ordering ."' class='ays-quiz-duration-column'>$duration</td>",
                "score" => "<td class='ays-quiz-score-column'>$score%</td>",
            );

            $attribute_options = (isset($result['options']) && $result['options'] != '') ? json_decode( $result['options'], true ) : '';

            $attribute_info = array();
            if($attribute_options != ''){
                $attribute_info = (isset($attribute_options['attributes_information']) && !empty( $attribute_options['attributes_information'] )) ? $attribute_options['attributes_information'] : array();
            }

            if( !empty($quiz_results_custom_fields) ){
                foreach ($quiz_results_custom_fields as $custom_field_key => $custom_field_value) {
                    if(isset( $attribute_info[$custom_field_value] ) && $attribute_info[$custom_field_value] != ''){
                        $ays_default_html_order[$custom_field_key] = "<td style='width:10%;'>" .$attribute_info[$custom_field_value]. "</td>";
                    }else{
                        $ays_default_html_order[$custom_field_key] = "<td style='width:10%;'></td>";
                    }
                }
            }

            $all_results_html .= "<tr>";
            foreach ($quiz_all_results_columns_order as $key => $value) {
                if (isset($quiz_all_results_columns[$value]) && isset( $ays_default_html_order[$value] )) {
                    $all_results_html .= $ays_default_html_order[$value];
                }
            }
            $all_results_html .= "</tr>";
        }

        $all_results_html .= "</table>
            </div>";

        return $all_results_html;
    }

    public function ays_generate_quiz_all_results_method( $attr ) {
        $id = (isset($attr['id']) && $attr['id'] != '') ? absint(intval($attr['id'])) : null;

        if (is_null($id)) {
            $content = "<p class='wrong_shortcode_text' style='color:red;'>" . __('Wrong shortcode initialized', $this->plugin_name) . "</p>";
            return str_replace(array("\r\n", "\n", "\r"), '', $content);
        }

        $if_quiz_trashed = Quiz_Maker_Data::ays_quiz_if_quiz_trashed($id);
        
        if ( !empty($if_quiz_trashed) ) {
            $content = "<p class='wrong_shortcode_text' style='color:red;'>" . __('Trashed quiz', $this->plugin_name) . "</p>";
            return str_replace(array("\r\n", "\n", "\r"), '', $content);
        }

        $this->enqueue_styles();
        $this->enqueue_scripts();
        $quiz_all_results_html = $this->ays_quiz_all_results_html( $id );
        $quiz_all_results_html = Quiz_Maker_Data::ays_quiz_translate_content( $quiz_all_results_html );
        
        // echo $quiz_all_results_html;
        return str_replace(array("\r\n", "\n", "\r"), '', $quiz_all_results_html);
    }


}
