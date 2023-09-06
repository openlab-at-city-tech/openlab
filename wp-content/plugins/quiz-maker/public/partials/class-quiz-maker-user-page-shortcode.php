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
class Quiz_Maker_User_Page
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

        add_shortcode('ays_user_page', array($this, 'ays_generate_user_page_method'));

        $this->settings = new Quiz_Maker_Settings_Actions($this->plugin_name);
    }

    public function enqueue_styles(){
        wp_enqueue_style($this->plugin_name . '-dataTable-min', AYS_QUIZ_PUBLIC_URL . '/css/quiz-maker-dataTables.min.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name . '-sweetalert-css', AYS_QUIZ_PUBLIC_URL . '/css/quiz-maker-sweetalert2.min.css', array(), $this->version, 'all' );
    }

    public function enqueue_scripts() {
        wp_enqueue_script( $this->plugin_name . '-datatable-min', AYS_QUIZ_PUBLIC_URL . '/js/quiz-maker-datatable.min.js', array('jquery'), $this->version, true);
        wp_enqueue_script( $this->plugin_name . '-sweetalert-js', AYS_QUIZ_PUBLIC_URL . '/js/quiz-maker-sweetalert2.all.min.js', array('jquery'), $this->version, true );
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


    /*
    ==========================================
        User page shortcode
    ==========================================
    */

    public function get_user_reports_info( $cat_id ){
        global $wpdb;

        $category_id = (isset($cat_id) && $cat_id != '') ? absint( sanitize_text_field( $cat_id ) ) : 0;

        $current_user = wp_get_current_user();
        $id = $current_user->ID;
        if($id == 0){
            return null;
        }

        $reports_table = $wpdb->prefix . "aysquiz_reports";
        $quizes_table = $wpdb->prefix . "aysquiz_quizes";
        $sql = "SELECT q.title, r.start_date, r.end_date, r.duration, r.score, r.id, r.points, r.options
                FROM $reports_table AS r
                LEFT JOIN $quizes_table AS q
                ON r.quiz_id = q.id";

        if($category_id != 0){
            $sql .= " WHERE r.user_id = {$id} AND q.quiz_category_id = {$category_id} AND r.status = 'finished'";
        }else{
            $sql .= " WHERE r.user_id = {$id} AND r.status = 'finished'";
        }

        $sql .= " ORDER BY r.id DESC";

        $results = $wpdb->get_results($sql, "ARRAY_A");

        return $results;

    }

    public function ays_user_page_html( $attr ){

        $category_id = (isset($attr['id']) && $attr['id'] != '') ? absint( sanitize_text_field( $attr['id'] ) ) : 0;

        $results = $this->get_user_reports_info( $category_id );
        wp_enqueue_style( $this->plugin_name.'-animate', AYS_QUIZ_PUBLIC_URL . '/css/animate.css', array(), $this->version, 'all');
        wp_enqueue_script( $this->plugin_name . '-user-page-public', AYS_QUIZ_PUBLIC_URL . '/js/user-page/user-page-public.js', array('jquery'), $this->version, true);
        wp_localize_script( $this->plugin_name . '-user-page-public', 'quiz_maker_ajax_user_page_public', array('ajax_url' => admin_url('admin-ajax.php')));

        $quiz_settings = $this->settings;
        $quiz_settings_options = ($quiz_settings->ays_get_setting('options') === false) ? json_encode(array()) : $quiz_settings->ays_get_setting('options');
        $quiz_set_option = json_decode($quiz_settings_options, true);

        $quiz_set_option['ays_show_result_report'] = !isset($quiz_set_option['ays_show_result_report']) ? 'on' : $quiz_set_option['ays_show_result_report'];
        $show_result_report = isset($quiz_set_option['ays_show_result_report']) && $quiz_set_option['ays_show_result_report'] == 'on' ? true : false;
        $options = $quiz_set_option;

        $custom_fields = Quiz_Maker_Data::get_custom_fields_for_shortcodes();

        //User page
        $user_page_custom_fields = isset($custom_fields['user_page']) && !empty($custom_fields['user_page']) ? $custom_fields['user_page'] : array();

        $default_user_page_columns = array(
            'quiz_name' => 'quiz_name',
            'start_date' => 'start_date',
            'end_date' => 'end_date',
            'duration' => 'duration',
            'score' => 'score',
            'points' => '',
            'download_certificate' => '',
            'details' => 'details',
        );

        if( !empty($user_page_custom_fields) ){
            foreach ($user_page_custom_fields as $custom_field_key => $custom_field) {
                $default_user_page_columns[$custom_field_key] = $custom_field_key;
            }
        }

        $options['user_page_columns'] = ! isset( $options['user_page_columns'] ) ? $default_user_page_columns : $options['user_page_columns'];
        $user_page_columns = (isset( $options['user_page_columns'] ) && !empty($options['user_page_columns']) ) ? $options['user_page_columns'] : array();
        $user_page_columns_order = (isset( $options['user_page_columns_order'] ) && !empty($options['user_page_columns_order']) ) ? $options['user_page_columns_order'] : $default_user_page_columns;

        if( ! isset( $user_page_columns['points'] ) ){
            $user_page_columns['points'] = '';
        }

        if( ! array_key_exists('points', $user_page_columns_order) ){
            $user_page_columns_order['points'] = 'points';
        }

        if( ! isset( $user_page_columns['download_certificate'] ) ){
            $user_page_columns['download_certificate'] = '';
        }

        if( ! array_key_exists('download_certificate', $user_page_columns_order) ){
            $user_page_columns_order['download_certificate'] = 'download_certificate';
        }

        $default_user_page_column_names = array(
            "quiz_name" => __( 'Quiz name', $this->plugin_name ),
            "start_date" => __( 'Start date', $this->plugin_name ),
            "end_date" => __( 'End date', $this->plugin_name ),
            "duration" => __( 'Duration', $this->plugin_name ),
            "score" => __( 'Score', $this->plugin_name ),
            "download_certificate" => __( 'Certificate', $this->plugin_name ),
            "details" => __( 'Details', $this->plugin_name ),
            "points" => __( 'Points', $this->plugin_name ),
        );

        if( !empty($user_page_custom_fields) ){
            foreach ($user_page_custom_fields as $custom_field_key => $custom_field_value) {
                $default_user_page_column_names[$custom_field_key] = $custom_field_value;
            }
        }

        $ays_default_header_value = array(
            "quiz_name" => "<th style='width:20%;'>" . __( "Quiz Name", $this->plugin_name ) . "</th>",
            "start_date" => "<th style='width:17%;' class='ays-quiz-user-results-start-date-column'>" . __( "Start", $this->plugin_name ) . "</th>",
            "end_date" => "<th style='width:17%;' class='ays-quiz-user-results-end-date-column'>" . __( "End", $this->plugin_name ) . "</th>",
            "duration" => "<th style='width:13%;'>" . __( "Duration", $this->plugin_name ) . "</th>",
            "score" => "<th style='width:13%;'>" . __( "Score", $this->plugin_name ) . "</th>",
            "download_certificate" => "<th style='width:13%;'>" . __( "Certificate", $this->plugin_name ) . "</th>",
            "details" => "<th style='width:20%;'>" . __( "Details", $this->plugin_name ) . "</th>",
            "points" => "<th style='width:13%;'>" . __( "Points", $this->plugin_name ) . "</th>",
        );

        if( !empty($user_page_custom_fields) ){
            foreach ($user_page_custom_fields as $custom_field_key => $custom_field_value) {
                $ays_default_header_value[$custom_field_key] = "<th style='width:10%;'>" .$custom_field_value. "</th>";
            }
        }

        if($results === null){
            $user_page_html = "<p style='text-align: center;font-style:italic;'>" . __( "You must log in to see your results.", $this->plugin_name ) . "</p>";
            return $user_page_html;
        }

        $res_empty_class = "";
        if ( empty( $results ) || is_null( $results ) ) {
            $res_empty_class = "ays-quiz-user-results-empty";
        }
        $user_page_html = "<div class='ays-quiz-user-results-container ". $res_empty_class ."'>
        <table id='ays-quiz-user-score-page'>
            <thead>
                <tr>";

        $columns_count = 0;
        foreach ($user_page_columns_order as $key => $value) {
            if (isset($user_page_columns[$value]) && $user_page_columns[$value] != '' && isset( $ays_default_header_value[$value] )) {
                $columns_count++;
                $user_page_html .= $ays_default_header_value[$value];
            }
        }

        $user_page_html .= "</tr></thead>";

        if( !empty( $results ) ){
            foreach($results as $result){
                $id         = isset($result['id']) ? $result['id'] : null;
                $title      = isset( $result['title'] ) && $result['title'] != '' ? $result['title'] : "";

                if( $title == '' ){
                    $title = __( 'Deleted quiz', $this->plugin_name );
                }

//                $start_date = date_create($result['start_date']);
//                $start_date = date_format($start_date, 'H:i:s M d, Y');
                $start_date = date_i18n('d M Y H:i:s', strtotime( $result['start_date'] ) );
//                $end_date   = date_create($result['end_date']);
//                $end_date   = date_format($end_date, 'H:i:s M d, Y');
                $end_date   = date_i18n('d M Y H:i:s', strtotime( $result['end_date'] ) );
                $duration   = isset($result['duration']) ? $result['duration'] : 0;
                $score      = isset($result['score']) ? $result['score'] : 0;
                $points     = isset($result['points']) ? round( floatval( $result['points'] ), 2 ) : 0;

                $start_date_for_ordering = strtotime($result['start_date']);
                $end_date_for_ordering   = strtotime($result['end_date']);
                $duration_for_ordering   = $duration;

                $cert_options = isset($result['options']) && $result['options'] != '' ? $result['options'] : array();

                $d_certificate = array();
                if(!empty($cert_options)){
                    $d_certificate = json_decode($cert_options, true);
                }
                $d_button = '';
                $data_src = '';
                if(isset($d_certificate['cert_file_url']) && $d_certificate['cert_file_url'] != ''){
                    $data_src = $d_certificate['cert_file_url'];
                    $d_button = "<a class='ays-quiz-user-d-cert' href=". $data_src ." download>".__("Download", $this->plugin_name)."</button>";
                }

                $duration = Quiz_Maker_Data::secondsToWords($duration);
                if ($duration == '') {
                    $duration = '0 ' . __( 'second' , $this->plugin_name );
                }

                $ays_default_html_order = array(
                    "quiz_name" => "<td class='ays-quiz-name-column'>$title</td>",
                    "start_date" => "<td class='ays-quiz-start-date-column' data-order='". $start_date_for_ordering ."'>$start_date</td>",
                    "end_date" => "<td class='ays-quiz-end-date-column' data-order='". $end_date_for_ordering ."'>$end_date</td>",
                    "duration" => "<td class='ays-quiz-duration-column' data-order='". $duration_for_ordering ."'>$duration</td>",
                    "score" => "<td class='ays-quiz-score-column'>$score%</td>",
                    "download_certificate" => "<td class='ays-quiz-cert-column'>".$d_button."</td>",
                    "details" => "<td class='ays-quiz-details-column'><button type='button' data-id='".$id."' class='ays-quiz-user-sqore-pages-details'>".__("Details", $this->plugin_name)."</button></td>",
                    "points" => "<td class='ays-quiz-points-column'>$points</td>",
                );

                $attribute_info = array();
                if($d_certificate != ''){
                    $attribute_info = (isset($d_certificate['attributes_information']) && !empty( $d_certificate['attributes_information'] )) ? $d_certificate['attributes_information'] : array();
                }

                if( !empty($user_page_custom_fields) ){
                    foreach ($user_page_custom_fields as $custom_field_key => $custom_field_value) {
                        if(isset( $attribute_info[$custom_field_value] ) && $attribute_info[$custom_field_value] != ''){
                            $ays_default_html_order[$custom_field_key] = "<td style='width:10%;'>" .$attribute_info[$custom_field_value]. "</td>";

                        }else{

                            $ays_default_html_order[$custom_field_key] = "<td style='width:10%;'></td>";

                        }
                    }
                }

                $user_page_html .= "<tr>";
                foreach ($user_page_columns_order as $key => $value) {
                    if (isset($user_page_columns[$value]) && $user_page_columns[$value] != '' && isset( $ays_default_html_order[$value] )) {
                        $user_page_html .= $ays_default_html_order[$value];
                    }
                }
                $user_page_html .= "</tr>";
            }
        }else{
            $user_page_html .= "<tr>
                <td colspan='". $columns_count ."'>". __( "There are no results yet.", $this->plugin_name ) ."</td>
            </tr>";
        }

        $user_page_html .= "</table>
            </div>
            <div id='ays-results-modal' class='ays-modal'>
                <div class='ays-modal-content'>
                    <div class='ays-quiz-preloader'>
                        <img class='loader' src='". AYS_QUIZ_ADMIN_URL."/images/loaders/3-1.svg'>
                    </div>
                    <div class='ays-modal-header'>
                        <span class='ays-close' id='ays-close-results'>&times;</span>
                    </div>
                    <div class='ays-modal-body' id='ays-results-body'></div>
                </div>
            </div>
            <style type='text/css'>
                @media only screen and (max-width: 760px),
                (min-device-width: 768px) and (max-device-width: 1024px)  {
                    table#ays-quiz-user-score-page td:empty { display: none !important; }
                    table#ays-quiz-user-score-page td.ays-quiz-name-column:before { content: '" . $default_user_page_column_names['quiz_name'] . "'; }
                    table#ays-quiz-user-score-page td.ays-quiz-start-date-column:before { content: '" . $default_user_page_column_names['start_date'] . "'; }
                    table#ays-quiz-user-score-page td.ays-quiz-end-date-column:before { content: '" . $default_user_page_column_names['end_date'] . "'; }
                    table#ays-quiz-user-score-page td.ays-quiz-duration-column:before { content: '" . $default_user_page_column_names['duration'] . "'; }
                    table#ays-quiz-user-score-page td.ays-quiz-score-column:before { content: '" . $default_user_page_column_names['score'] . "'; }
                    table#ays-quiz-user-score-page td.ays-quiz-cert-column:before { content: '" . $default_user_page_column_names['download_certificate'] . "'; }
                    table#ays-quiz-user-score-page td.ays-quiz-details-column:before { content: '" . $default_user_page_column_names['details'] . "'; }
                    table#ays-quiz-user-score-page td.ays-quiz-points-column:before { content: '" . $default_user_page_column_names['points'] . "'; }
                    table#ays-quiz-user-score-page button.ays-quiz-user-sqore-pages-details { margin: initial; }
                }
            </style>
            ";

        return $user_page_html;
    }

    public function user_reports_info_popup_ajax(){
        Quiz_Maker_iFrame::headers_for_ajax();

        global $wpdb;
        error_reporting(0);
        $results_table = $wpdb->prefix . "aysquiz_reports";
        $questions_table = $wpdb->prefix . "aysquiz_questions";

        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'user_reports_info_popup_ajax') {

            $setting_options = Quiz_Maker_Settings_Actions::ays_get_setting("options");
            $setting_options = json_decode($setting_options , true);
            $hide_correct_answer = isset($setting_options['user_page_hide_answer']) && $setting_options['user_page_hide_answer'] == "on" ? true : false;

            $id = absint(intval($_REQUEST['result']));
            $results = $wpdb->get_row("SELECT * FROM {$results_table} WHERE id={$id}", "ARRAY_A");
            $user_id = intval($results['user_id']);
            $quiz_id = intval($results['quiz_id']);
            $user = get_user_by('id', $user_id);

            $user_ip = $results['user_ip'];
            $options = json_decode($results['options']);
            $user_attributes = $options->attributes_information;
            $start_date = $results['start_date'];
            //$duration = $options->passed_time;
            $duration = ( isset($results['duration']) && sanitize_text_field( $results['duration'] ) != '' ) ? sanitize_text_field( $results['duration'] ) : '';
            $rate_id = isset($options->rate_id) ? $options->rate_id : null;
            $rate = Quiz_Maker_Data::ays_quiz_rate($rate_id);
            $calc_method = isset($options->calc_method) ? $options->calc_method : 'by_correctness';

            $json = json_decode(file_get_contents("http://ipinfo.io/{$user_ip}/json"));
            $country = $json->country;
            $region = $json->region;
            $city = $json->city;
            $from = $city . ', ' . $region . ', ' . $country . ', ' . $user_ip;

            $user_max_weight = isset($options->user_points) ? $options->user_points : '-';
            $quiz_max_weight = isset($options->max_points) ? $options->max_points : '-';
            $score = $calc_method == 'by_points' ? $user_max_weight . ' / ' . $quiz_max_weight : $results['score'] . '%';

            $duration = Quiz_Maker_Data::secondsToWords($duration);
            if ($duration == '') {
                $duration = '0 ' . __( 'second' , $this->plugin_name );
            }

            $correctness = isset( $options->correctness ) && !empty( $options->correctness ) ? (array)$options->correctness : array();

            $res_question_title_arr = ( isset($options->questions_title) && !empty($options->questions_title) ) ? (array)$options->questions_title : array();

            $question_id_arr = array();
            $question_correctness = array();
            foreach ($options->correctness as $key => $option) {
                if (strpos($key, 'question_id_') !== false) {
                    $current_question_id = absint(intval(explode('_', $key)[2]));

                    $question_id_arr[] = $current_question_id;
                    $question_correctness[ $current_question_id ] = $option;
                }
            }

            $results_by_categories = Quiz_Maker_Data::ays_quiz_current_result_by_category($options, $question_correctness, $question_id_arr, $calc_method);
            
            $row = "<table id='ays-results-table'>";

            $row .= '<tr class="ays_result_element">
                    <td colspan="4">
                        <div class="ays-quiz-report-table-header" id="quiz-export-pdf">
                            <h1>' . __('Quiz Information',$this->plugin_name) . '</h1>
                            <div>
                                <span class="ays-pdf-export-text">'.__("Export to" , $this->plugin_name).'</span>
                                <a download="" id="downloadFileF" hidden href=""></a>
                                <button type="button"  class="button button-primary ays-quiz-export-pdf" data-result='.$id.'>PDF</button>
                            </div>
                        </div>
                    </td>
                </tr>';
            if(isset($rate['score'])){
                $rate_html = '<tr style="vertical-align: top;" class="ays_result_element">
                <td>'.__('Rate',$this->plugin_name).'</td>
                <td>'. __("Rate Score", $this->plugin_name).":<br>" . $rate['score'] . '</td>
                <td colspan="2" style="max-width: 200px;">'. __("Review", $this->plugin_name).":<br>" . $rate['review'] . '</td>
            </tr>';
            }else{
                $rate_html = '<tr class="ays_result_element">
                <td>'.__('Rate',$this->plugin_name).'</td>
                <td colspan="3">' . $rate['review'] . '</td>
            </tr>';
            }
            $row .= '<tr class="ays_result_element">
                    <td>'.__('Start date',$this->plugin_name).'</td>
                    <td colspan="3">' . $start_date . '</td>
                </tr>
                <tr class="ays_result_element">
                    <td>'.__('Duration',$this->plugin_name).'</td>
                    <td colspan="3">' . $duration . '</td>
                </tr>
                <tr class="ays_result_element">
                    <td>'.__('Score',$this->plugin_name).'</td>
                    <td colspan="3">' . $score . '</td>
                </tr>'.$rate_html;

            if(isset( $results_by_categories) &&  !empty($results_by_categories)){
                $row .= '<tr class="ays_result_element">
                            <td>'.__('Results by Categories',$this->plugin_name).'</td>
                            <td colspan="3">'. $results_by_categories .'</td>
                        </tr>
                ';
            }


            $row .= '<tr class="ays_result_element">
                    <td colspan="4"><h1>' . __('Questions',$this->plugin_name) . '</h1></td>
                </tr>';

            $index = 1;
            $user_exp = array();
            if($results['user_explanation'] != '' || $results['user_explanation'] !== null){
                $user_exp = json_decode($results['user_explanation'], true);
            }

            foreach ($correctness as $key => $option) {
                if (strpos($key, 'question_id_') !== false) {
                    $question_id = absint(intval(explode('_', $key)[2]));
                    $question = $wpdb->get_row("SELECT * FROM {$questions_table} WHERE id={$question_id}", "ARRAY_A");
                    $question_type = isset($question['type']) && $question['type'] != '' ? sanitize_text_field( $question['type'] ) : 'radio';
                    $answers_array = Quiz_Maker_Data::get_answers_with_question_id($question_id);
                    $qoptions = isset($question['options']) && $question['options'] != '' ? json_decode($question['options'], true) : array();
                    $use_html = isset($qoptions['use_html']) && $qoptions['use_html'] == 'on' ? true : false;
                    $correct_answers = Quiz_Maker_Data::get_correct_answers($question_id);
                    $correct_answer_images = Quiz_Maker_Data::get_correct_answer_images($question_id);
                    $is_text_type = Quiz_Maker_Data::question_is_text_type($question_id);
                    $is_matching_type = Quiz_Maker_Data::is_matching_answer( $question_id );
                    $text_type = Quiz_Maker_Data::text_answer_is($question_id);
                    $not_multiple_text_types = array("number", "date");

                    $question_title = isset( $question["question"] ) && $question["question"] != '' ? strip_shortcodes(nl2br(stripslashes($question["question"]))) : '';

                    if( !empty( $res_question_title_arr ) ){
                        $question_title = isset( $res_question_title_arr[$question_id] ) && $res_question_title_arr[$question_id] != '' ? strip_shortcodes(nl2br(stripslashes($res_question_title_arr[$question_id]))) : $question_title;
                    }

                    // Incorrect matches for answers
                    $qoptions['answer_incorrect_matches'] = isset($qoptions['answer_incorrect_matches']) ? $qoptions['answer_incorrect_matches'] : array();
                    $answer_incorrect_matches = isset($qoptions['answer_incorrect_matches']) && !empty( $qoptions['answer_incorrect_matches'] ) ? $qoptions['answer_incorrect_matches'] : array();

                    // var_dump($options->user_answered);
                    // var_dump($key);

                    if($is_text_type){
                        $user_answered = Quiz_Maker_Data::get_user_text_answered($options->user_answered, $key);
                        $user_answered_images = '';
                    }elseif( $question_type == 'fill_in_blank' ){
                        $user_answered = Quiz_Maker_Data::get_user_fill_in_blank_answered($options->user_answered, $key);
                        $user_answered_images = '';
                    }elseif( $is_matching_type ){
                        $user_answered = Quiz_Maker_Data::get_user_matching_answered($options->user_answered, $key, $answer_incorrect_matches);
                        $correct_answers = Quiz_Maker_Data::get_correct_answers_for_matching_type($question_id);
                        $user_answered_images = '';
                    } else{
                        $user_answered = Quiz_Maker_Data::get_user_answered($options->user_answered, $key);
                        $user_answered_images = Quiz_Maker_Data::get_user_answered_images($options->user_answered, $key);
                    }

                    $ans_point = $option;
                    $ans_point_class = 'success';

                    // var_dump($user_answered);
                    // die();
                    if(! $is_matching_type && is_array($user_answered) && isset( $user_answered['message'] )){
                        $user_answered = $user_answered['message'];
                        $ans_point = '-';
                        $ans_point_class = 'error';
                    } elseif ( $question_type == 'fill_in_blank' ) {
                        $fill_in_blank_question_title_user_answer = $question_title;
                        foreach ($answers_array as $answer_key => $answer_data) {
                            $slug = isset($answer_data["slug"]) && $answer_data["slug"] != '' ? stripslashes(htmlentities($answer_data["slug"], ENT_QUOTES)) : '';
                            $answer_id = (isset($answer_data['id']) && $answer_data['id'] != '') ? $answer_data["id"] : "";
                            $user_answer = (isset($user_answered[$answer_id]) && $user_answered[$answer_id] != '') ? $user_answered[$answer_id] : "";
                            $corect_answer = (isset($answer_data['answer']) && $answer_data['answer'] != '') ? $answer_data["answer"] : "";

                            if( $slug == "" ){
                                continue;
                            }

                            if(mb_strtolower(trim($user_answer)) == mb_strtolower(trim($corect_answer))){
                                $answer_html = "<span style='color: #73AF55;font-weight:700;'>". $user_answer ."</span>";
                            } elseif( $user_answer == "" ){
                                $answer_html = "<span style='color: #D06079;font-weight: 700;'>". "—" ."</span>";
                            } else {
                                $answer_html = "<span style='color: #D06079;font-weight: 700;'>". $user_answer ."</span>";
                            }


                            $fill_in_blank_question_title_user_answer = str_replace( $slug ,$answer_html, $fill_in_blank_question_title_user_answer);
                        }

                        $user_answered = stripslashes( $fill_in_blank_question_title_user_answer );
                    }

                    $tr_class = "ays_result_element";
                    if(isset($user_exp[$question_id])){
                        $tr_class = "";
                    }

                    $not_influence_to_score = isset($question['not_influence_to_score']) && $question['not_influence_to_score'] == 'on' ? true : false;
                    if ( $not_influence_to_score ) {
                        $not_influance_check_td = ' colspan="2" ';
                    }else{
                        $not_influance_check_td = '';
                    }

                    if($calc_method == 'by_correctness'){
                        $row .= '<tr class="'.$tr_class.'">
                            <td>'.__('Question', $this->plugin_name).' ' . $index . ' :<br/>' . stripslashes($question_title) . '</td>';

                        $status_class = 'error';
                        $correct_answers_status_class = 'success';
                        if ($option == true) {
                            $status_class = 'success';
                        }

                        if ($not_influence_to_score) {
                            $status_class = 'no_status';
                            $correct_answers_status_class = 'no_status';
                        }

                        if(!$hide_correct_answer){
                            if($is_text_type && ! in_array($text_type, $not_multiple_text_types)){
                                $c_answers = explode('%%%', $correct_answers);
                                $c_answer = $c_answers[0];
                                foreach($c_answers as $c_ans){
                                    if(strtolower(trim($user_answered)) == strtolower(trim($c_ans))){
                                        $c_answer = $c_ans;
                                        break;
                                    }
                                }
                                $row .= '<td class="ays-report-correct-answer">'.__('Correct answer',$this->plugin_name).':<br/>';
                                $row .= '<p class="success">' . htmlentities(stripslashes($c_answer)) . '<br>'.$correct_answer_images.'</p>';
                                $row .= '</td>';
                            } elseif( $question_type == "fill_in_blank" ){

                                $fill_in_blank_question_title_correct = $question_title;

                                foreach ($answers_array as $answer_key => $answer_data) {
                                    $slug = isset($answer_data["slug"]) && $answer_data["slug"] != '' ? stripslashes(htmlentities($answer_data["slug"], ENT_QUOTES)) : '';
                                    $answer_id = (isset($answer_data['id']) && $answer_data['id'] != '') ? $answer_data["id"] : "";
                                    $corect_answer = (isset($answer_data['answer']) && $answer_data['answer'] != '') ? $answer_data["answer"] : "";

                                    if( $slug == "" ){
                                        continue;
                                    }

                                    $answer_html = "<span style='color: #73AF55;font-weight:700;'>". $corect_answer ."</span>";

                                    $fill_in_blank_question_title_correct = str_replace( $slug ,$answer_html, $fill_in_blank_question_title_correct);
                                }

                                $row .= '<td class="ays-report-correct-answer">'.__('Correct answer',$this->plugin_name).':<br/>';
                                $row .= '<p>' . (stripslashes($fill_in_blank_question_title_correct)) . '<br>'.$correct_answer_images.'</p>';
                                $row .= '</td>';

                            } elseif ( $is_matching_type ) {
                                $row .= '<td class="ays-report-correct-answer">' . __( 'Correct answer', $this->plugin_name ) . ':<br/>';
                                foreach ( $correct_answers as $correct_answer ) {
                                    $correct_answer_content = esc_attr( $correct_answer );
                                    if($use_html){
                                        $correct_answer_content = stripslashes( $correct_answer );
                                    }
                                    $row .= '<p class="' . $correct_answers_status_class . '">' . $correct_answer_content . '<br>' . $correct_answer_images . '</p>';
                                    $row .= '<hr />';
                                }
                                $row .= '</td>';
                            } else{
                                if($text_type == 'date'){
                                    $correct_answers = date( 'm/d/Y', strtotime( $correct_answers ) );
                                }
                                $correct_answer_content = htmlentities( stripslashes( $correct_answers ) );
                                if($use_html){
                                    $correct_answer_content = stripslashes( $correct_answers );
                                }

                                $row .= '<td class="ays-report-correct-answer">'.__('Correct answer',$this->plugin_name).':<br/>
                                    <p class="'.$correct_answers_status_class.'">' . $correct_answer_content . '<br>'.$correct_answer_images.'</p>
                                </td>';
                            }
                        }

                        if($text_type == 'date'){
                            if(Quiz_Maker_Admin::validateDate($user_answered, 'Y-m-d')){
                                $user_answered = date( 'm/d/Y', strtotime( $user_answered ) );
                            }
                        }

                        if ( $is_matching_type ) {
                            $row .= '<td ' . $not_influance_check_td . ' class="ays-report-user-answer">' . __( 'User answered', $this->plugin_name ) . ':<br/>';
                            foreach ( $user_answered as $user_answer ) {
                                $user_answer_content = esc_attr( $user_answer['answer'] );
                                if($use_html){
                                    $user_answer_content = stripslashes( $user_answer['answer'] );
                                }

                                $status_class = 'error';
                                if ($user_answer['correct'] == true) {
                                    $status_class = 'success';
                                }

                                $row .= '<p class="' . $status_class . '">' . $user_answer_content . '</p>';
                                $row .= '<hr />';
                            }
                            $row .= '</td>';
                        } else {
                            $user_answer_content = htmlentities( stripslashes( $user_answered ) );
                            if($use_html || $question_type == 'fill_in_blank'){
                                $user_answer_content = stripslashes( $user_answered );
                            }

                            if($question_type == "fill_in_blank"){
                                $status_class = "";
                            }

                            if($hide_correct_answer){
                                $status_class = "ays_quiz_user_page_hide_answer";
                            }
                            
                            $row .= '<td '.$not_influance_check_td.' class="ays-report-user-answer">'.__('User answered',$this->plugin_name).':<br/>
                                <p class="'.$status_class.'">' . $user_answer_content . '</p>
                            </td>';
                        }



                        if (! $not_influence_to_score && !$hide_correct_answer) {
                            if ($option == true) {
                                    $row .= '<td class="ays-report-status-icon">
                                        <svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130.2 130.2">
                                            <circle class="path circle" fill="none" stroke="#73AF55" stroke-width="6" stroke-miterlimit="10" cx="65.1" cy="65.1" r="62.1"/>
                                            <polyline class="path check" fill="none" stroke="#73AF55" stroke-width="6" stroke-linecap="round" stroke-miterlimit="10" points="100.2,40.2 51.5,88.8 29.8,67.5 "/>
                                        </svg>
                                        <p class="success">'.__('Succeed',$this->plugin_name).'!</p>
                                    </td>';
                            } else {
                                $row .= '<td class="ays-report-status-icon">
                                    <svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130.2 130.2">
                                        <circle class="path circle" fill="none" stroke="#D06079" stroke-width="6" stroke-miterlimit="10" cx="65.1" cy="65.1" r="62.1"/>
                                        <line class="path line" fill="none" stroke="#D06079" stroke-width="6" stroke-linecap="round" stroke-miterlimit="10" x1="34.4" y1="37.9" x2="95.8" y2="92.3"/>
                                        <line class="path line" fill="none" stroke="#D06079" stroke-width="6" stroke-linecap="round" stroke-miterlimit="10" x1="95.8" y1="38" x2="34.4" y2="92.2"/>
                                    </svg>
                                    <p class="error">'.__('Failed',$this->plugin_name).'!</p>
                                </td>';
                            }
                        }

                        $row .= '</tr>';

                    }elseif($calc_method == 'by_points'){
                        if($hide_correct_answer){
                            $ans_point_class = "ays_quiz_user_page_hide_answer";
                            $answer_point_box = "";
                        }else{
                            $answer_point_box = '<td class="ays-report-answer-point">'.__('Answer point',$this->plugin_name).':<br/><p class="'.$ans_point_class.'">' . htmlentities($ans_point) . '</p></td>';
                        }

                        $user_answer_content = htmlentities(do_shortcode(stripslashes($user_answered)));
                        if($question_type == 'fill_in_blank'){
                            $user_answer_content = stripslashes($user_answered);
                            $ans_point_class = "";
                        }

                        $row .= '<tr class="'.$tr_class.'">
                                <td colspan="2">'.__('Question',$this->plugin_name).' ' . $index . ' :<br/>' . (do_shortcode(stripslashes($question_title))) . '</td>
                                <td class="ays-report-user-answer ays-report-user-answer-by-points">'.__('User answered',$this->plugin_name).':<br/>';
                        if ( $is_matching_type ) {
                            foreach ( $user_answered as $user_answer ) {
                                $user_answer_content = esc_attr( $user_answer['answer'] );
                                if($use_html){
                                    $user_answer_content = stripslashes( $user_answer['answer'] );
                                }
                                $row .= '<p class="' . $ans_point_class . '">' . $user_answer_content . '</p>';
                                $row .= '<hr />';
                            }
                        } else {
                            $row .= '<p class="'.$ans_point_class.'">' . $user_answer_content . '<br>'.$user_answered_images.'</p>';
                        }

                        $row .= "</td>";
                        $row .= $answer_point_box;
                        $row .= '</tr>';
                    }
                    $index++;
                    if(isset($user_exp[$question_id])){
                        $row .= '<tr class="ays_result_element">
                            <td>'.__('User explanation for this question',$this->plugin_name).'</td>
                            <td colspan="3">'.$user_exp[$question_id].'</td>
                        </tr>';
                    }
                }
            }

            $row .= "</table>";
            echo json_encode(array(
                "status" => true,
                "rows" => $row
            ));
            wp_die();
        }
    }

    // Export result to pdf
    public function user_export_result_pdf() {
        Quiz_Maker_iFrame::headers_for_ajax();

        global $wpdb;
        $results_table   = $wpdb->prefix . "aysquiz_reports";
        $questions_table = $wpdb->prefix . "aysquiz_questions";
        $quizzes_table   = $wpdb->prefix . "aysquiz_quizes";

        $pdf_response = null;
        $pdf_content  = null;
        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'user_export_result_pdf') {
            $id      = absint(intval($_REQUEST['result']));
            $results = $wpdb->get_row("SELECT * FROM {$results_table} WHERE id={$id} AND `status` = 'finished';", "ARRAY_A");
            $user_id = intval($results['user_id']);
            $quiz_id = intval($results['quiz_id']);

            $user            = get_user_by('id', $user_id);
            // $user_ip         = $results['user_ip'];
            $options         = json_decode($results['options']);
            $user_attributes = $options->attributes_information;
            $start_date      = $results['start_date'];
            $duration        = $options->passed_time;
            $rate_id         = isset($options->rate_id) ? $options->rate_id : null;
            $rate            = Quiz_Maker_Data::ays_quiz_rate($rate_id);
            $calc_method     = isset($options->calc_method) ? $options->calc_method : 'by_correctness';
            $correctness     = isset( $options->correctness ) && !empty( $options->correctness ) ? (array)$options->correctness : array();

            if(!isset($options->user_points)){
                $options->user_points = array_sum($correctness);
            }

            $user_max_weight = isset($options->user_points) ? $options->user_points : '-';
            $quiz_max_weight = isset($options->max_points) ? $options->max_points : '-';

            $score       = ($calc_method == 'by_points') ? $user_max_weight . ' / ' . $quiz_max_weight : $results['score'] . '%';
            // $user        = ($user_id === 0) ? __( "Guest", $this->plugin_name ) : $user->data->display_name;
            $review      = (isset($rate['review']) && $rate['review'] != null) ? stripslashes(html_entity_decode(str_replace("\n", "", (strip_tags($rate['review']) )))) : '';
            // $email       = (isset($results['user_email']) && $results['user_email'] !== '') ? stripslashes($results['user_email']) : '';
            // $user_name   = (isset($results['user_name']) && $results['user_name'] !== '') ? stripslashes($results['user_name']) : '';
            // $user_phone  = (isset($results['user_phone']) && $results['user_phone'] !== '') ? stripslashes($results['user_phone']) : '';
            // $unique_code = (isset($results['unique_code']) && $results['unique_code'] !== '') ? strtoupper($results['unique_code']) : '';
            // $json    = json_decode(file_get_contents("http://ipinfo.io/{$user_ip}/json"));
            // $country = $json->country;
            // $region  = $json->region;
            // $city    = $json->city;
            // $from    = $city . ', ' . $region . ', ' . $country . ', ' . $user_ip;
            // if ($user_ip == '') {
            //     $from = '';
            // }
            $quests      = array();
            $export_data = array();

            $data_headers   = array();
            $data_questions = array();

            $data_headers['user_data'] = array(
                // 'api_user_information_header' => __( "User Information", $this->plugin_name ),
                // 'api_user_ip_header'     => __( "User IP", $this->plugin_name ),
                // 'api_user_id_header'     => __( "User ID", $this->plugin_name ),
                // 'api_user_header'        => __( "User", $this->plugin_name ),
                // 'api_user_mail_header'   => __( "Email", $this->plugin_name ),
                // 'api_user_name_header'   => __( "Name", $this->plugin_name ),
                // 'api_user_phone_header'  => __( "Phone", $this->plugin_name ),
                // 'api_checked_header'     => __( "Checked", $this->plugin_name ),

                'api_quiz_information_header' => __( "Quiz Information", $this->plugin_name ),

                // 'api_user_ip'     =>  $from,
                // 'api_user_id'     =>  $user_id."",
                // 'api_user'        =>  $user,
                // 'api_user_mail'   =>  $email,
                // 'api_user_name'   =>  $user_name,
                // 'api_user_phone'  =>  $user_phone,

                'api_start_date_header' =>  __( "Start date", $this->plugin_name ),
                'api_duration_header'   =>  __( "Duration", $this->plugin_name ),
                'api_score_header'      =>  __( "Score", $this->plugin_name ),
                'api_rate_header'       =>  __( "Rate", $this->plugin_name ),

                'api_start_date' =>  $start_date,
                'api_duration'   =>  $duration,
                'api_score'      =>  $score,
                'api_rate'       =>  $review,
            );

            // if ($user_attributes !== null) {
            //     $user_attributes = (array)$user_attributes;
            //     foreach ($user_attributes as $name => $value) {
            //         if(stripslashes($value) == ''){
            //             $attr_value = '-';
            //         }else{
            //             $attr_value = stripslashes($value);
            //         }
            //         if($attr_value == 'on'){
            //             $attr_value = __('Checked',$this->plugin_name);
            //         }
            //         $custom_fild = array(
            //             'api_custom_fild_name'  => stripslashes($name),
            //             'api_custom_fild_value' => $attr_value,
            //         );
            //         $quests[] = $custom_fild;
            //     }
            // }
            // $data_headers['custom_fild'] = $quests;

            $setting_options = Quiz_Maker_Settings_Actions::ays_get_setting("options");
            $setting_options = json_decode($setting_options , true);
            $hide_correct_answer = isset($setting_options['user_page_hide_answer']) && $setting_options['user_page_hide_answer'] == "on" ? true : false;

            $data_questions['headers'] = array(
                'api_glob_question_header'  => __( "Questions", $this->plugin_name ),
                'api_question_header'       => __( "Question", $this->plugin_name ),
                'api_correct_answer_header' => __( "Correct answer", $this->plugin_name ),
                'api_user_answer_header'    => __( "User answered", $this->plugin_name ),
                'api_hide_correct_answer'   => $hide_correct_answer,
            );

            $quests = array();
            foreach ($correctness as $key => $option) {
                if (strpos($key, 'question_id_') !== false) {
                    $question_id     = absint(intval(explode('_', $key)[2]));
                    $question_content = $wpdb->get_row("SELECT * FROM {$questions_table} WHERE id={$question_id}", "ARRAY_A");
                    $question_type = isset($question_content['type']) && $question_content['type'] != '' ? sanitize_text_field( $question_content['type'] ) : 'radio';
                    $is_matching_type = Quiz_Maker_Data::is_matching_answer( $question_id );
                    $answers_array = Quiz_Maker_Data::get_answers_with_question_id($question_id);

                    $qoptions = isset($question_content['options']) && $question_content['options'] != '' ? json_decode($question_content['options'], true) : array();

                    $question_title = isset( $question_content["question"] ) && $question_content["question"] != '' ? strip_shortcodes(stripslashes($question_content["question"])) : '';

                    // Incorrect matches for answers
                    $qoptions['answer_incorrect_matches'] = isset($qoptions['answer_incorrect_matches']) ? $qoptions['answer_incorrect_matches'] : array();
                    $answer_incorrect_matches = isset($qoptions['answer_incorrect_matches']) && !empty( $qoptions['answer_incorrect_matches'] ) ? $qoptions['answer_incorrect_matches'] : array();

                    if( $question_type == 'fill_in_blank') {
                        // $correct_answers = Quiz_Maker_Data::get_correct_answers($question_id);

                        $fill_in_blank_question_title_correct = $question_title;

                        foreach ($answers_array as $answer_key => $answer_data) {
                            $slug = isset($answer_data["slug"]) && $answer_data["slug"] != '' ? stripslashes(htmlentities($answer_data["slug"], ENT_QUOTES)) : '';
                            $answer_id = (isset($answer_data['id']) && $answer_data['id'] != '') ? $answer_data["id"] : "";
                            $corect_answer = (isset($answer_data['answer']) && $answer_data['answer'] != '') ? $answer_data["answer"] : "";

                            if( $slug == "" ){
                                continue;
                            }

                            $answer_html = $corect_answer;

                            $fill_in_blank_question_title_correct = str_replace( $slug ,$answer_html, $fill_in_blank_question_title_correct);
                        }

                        $correct_answers = $fill_in_blank_question_title_correct;

                    } elseif( $is_matching_type ){
                        
                    } else {
                        $correct_answers = Quiz_Maker_Data::get_correct_answers($question_id);
                    }


                    if(Quiz_Maker_Data::question_is_text_type($question_id)){
                        $user_answered = Quiz_Maker_Data::get_user_text_answered($options->user_answered, $key);
                    } elseif( $question_type == 'fill_in_blank' ){
                        $user_answered = Quiz_Maker_Data::get_user_fill_in_blank_answered($options->user_answered, $key);
                        $fill_in_blank_question_title_user_answer = $question_title;

                        foreach ($answers_array as $answer_key => $answer_data) {
                            $slug = isset($answer_data["slug"]) && $answer_data["slug"] != '' ? stripslashes(htmlentities($answer_data["slug"], ENT_QUOTES)) : '';
                            $answer_id = (isset($answer_data['id']) && $answer_data['id'] != '') ? $answer_data["id"] : "";
                            $user_answer = (isset($user_answered[$answer_id]) && $user_answered[$answer_id] != '') ? $user_answered[$answer_id] : "";
                            $corect_answer = (isset($answer_data['answer']) && $answer_data['answer'] != '') ? $answer_data["answer"] : "";

                            if( $slug == "" ){
                                continue;
                            }

                            if(mb_strtolower(trim($user_answer)) == mb_strtolower(trim($corect_answer))){
                                $answer_html = $user_answer;
                            } elseif( $user_answer == "" ){
                                $answer_html = "—";
                            } else {
                                $answer_html = $user_answer;
                            }


                            $fill_in_blank_question_title_user_answer = str_replace( $slug ,$answer_html, $fill_in_blank_question_title_user_answer);
                        }

                        $user_answered = stripslashes( $fill_in_blank_question_title_user_answer );

                    } elseif( $is_matching_type ){
                        $user_answered = Quiz_Maker_Data::get_user_matching_answered($options->user_answered, $key, $answer_incorrect_matches);
                        $correct_answers = Quiz_Maker_Data::get_correct_answers_for_matching_type( $question_id );
                    } else{
                        $user_answered = Quiz_Maker_Data::get_user_answered($options->user_answered, $key);
                    }

                    if ($user_answered == '' || ( isset($user_answered['status']) && $user_answered['status'] == false ) ) {
                        $user_answered = ' - ';
                    }

                    $successed_or_failed = ($option == true) ? __( "Succeed", $this->plugin_name ) : __( "Failed", $this->plugin_name );

                    $question       = esc_attr(stripslashes($question_content["question"]));
                    // $correct_answer = html_entity_decode(strip_tags(stripslashes($correct_answers)));
                    // $user_answer    = html_entity_decode(strip_tags(stripslashes($user_answered)));

                    if ( $is_matching_type ) {
                        $correct_answer_content = array();
                        foreach ( $correct_answers as $correct_answer ) {
                            $correct_answer_content[] = html_entity_decode(strip_tags(stripslashes( $correct_answer )));
                        }
                        $correct_answer = implode( ", ", $correct_answer_content );
                    } else {
                        $correct_answer = html_entity_decode(strip_tags(stripslashes($correct_answers)));
                    }

                    if ( $is_matching_type ) {
                        $user_answer_content = array();
                        foreach ( $user_answered as $value ) {
                            if ( $value['answer'] == '' ) {
                                $user_answer_content[] = " - ";
                            } else {
                                $user_answer_content[] = html_entity_decode( strip_tags( stripslashes( $value['answer'] ) ) );
                            }
                        }
                        $user_answer = implode( ", ", $user_answer_content );
                    } else {
                        $user_answer = html_entity_decode(strip_tags(stripslashes($user_answered)));
                    }
                    
                    $questions = array(
                        'api_question'       => $question,
                        'api_correct_answer' => $correct_answer,
                        'api_user_answer'    => $user_answer,
                        'api_status'         => $successed_or_failed,
                        'api_check_status'   => $option,
                    );

                    $quests[] = $questions;
                }
            }
            $data_questions['data_question'] = $quests;
            
            $pdf = new Quiz_PDF_API();
            $export_data = array(
                'status'          => true,
                'type'            => 'pdfapi',
                'api_quiz_id'     => $quiz_id,
                'data_headers'    => $data_headers,
                'data_questions'  => $data_questions
            );

            $pdf_response = $pdf->generate_report_PDF_public($export_data);

            $pdf_content  = $pdf_response['status'];

            if($pdf_content === true){
                ob_end_clean();
                $ob_get_clean = ob_get_clean();
                echo json_encode($pdf_response);
            }else{
                $export_data = array(
                    'status' => false,
                );
                ob_end_clean();
                $ob_get_clean = ob_get_clean();
                echo json_encode($export_data);
            }
            wp_die();
        }

    }

    public function ays_generate_user_page_method( $attr ){

        $this->enqueue_styles();
        $this->enqueue_scripts();
        $user_page_html = $this->ays_user_page_html( $attr );
        $user_page_html = Quiz_Maker_Data::ays_quiz_translate_content( $user_page_html );
        
        return str_replace(array("\r\n", "\n", "\r"), '', $user_page_html);
    }


}
