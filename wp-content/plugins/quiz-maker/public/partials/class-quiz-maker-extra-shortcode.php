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
class Ays_Quiz_Maker_Extra_Shortcodes_Public
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

    private $html_class_prefix = 'ays-quiz-extra-shortcodes-';
    private $html_name_prefix = 'ays-quiz-';
    private $name_prefix = 'ays_quiz_';
    private $unique_id;
    private $unique_id_in_class;

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

        add_shortcode('ays_quiz_avg_score', array($this, 'ays_generate_avg_score_method'));
        add_shortcode('ays_quiz_passed_users_count', array($this, 'ays_generate_passed_users_count_method'));
        add_shortcode('ays_quiz_failed_users_count_by_score', array($this, 'ays_generate_failed_users_count_by_score_method'));
        add_shortcode('ays_quiz_passed_users_count_by_score', array($this, 'ays_generate_passed_users_count_by_score_method'));
        add_shortcode('ays_quiz_user_passed_quizzes_count', array($this, 'ays_generate_user_passed_quizzes_count_method'));
        add_shortcode('ays_quiz_user_all_passed_quizzes_count', array($this, 'ays_generate_user_all_passed_quizzes_count_method'));
        add_shortcode('ays_quiz_user_first_name', array($this, 'ays_generate_user_first_name_method'));
        add_shortcode('ays_quiz_user_last_name', array($this, 'ays_generate_user_last_name_method'));
        add_shortcode('ays_quiz_user_nickname', array($this, 'ays_generate_user_nickname_method'));
        add_shortcode('ays_quiz_user_display_name', array($this, 'ays_generate_user_display_name_method'));
        add_shortcode('ays_quiz_user_email', array($this, 'ays_generate_user_email_method'));
        add_shortcode('ays_quiz_user_duration', array($this, 'ays_generate_user_quiz_duration_method'));
        add_shortcode('ays_quiz_creation_date', array($this, 'ays_generate_quiz_creation_date_method'));
        add_shortcode('ays_quiz_current_author', array($this, 'ays_generate_current_quiz_author_method'));
        add_shortcode('ays_quiz_questions_count', array($this, 'ays_generate_questions_count_method'));
        add_shortcode('ays_quiz_category_title', array($this, 'ays_generate_category_title_method'));
        add_shortcode('ays_quiz_category_description', array($this, 'ays_generate_category_description_method'));
        add_shortcode('ays_quiz_question_categories_title', array($this, 'ays_generate_question_categories_title_method'));
        add_shortcode('ays_quiz_question_categories_description', array($this, 'ays_generate_question_categories_description_method'));
        add_shortcode('ays_quiz_user_roles', array($this, 'ays_generate_user_roles_method'));
        add_shortcode('ays_quiz_unread_results_count', array($this, 'ays_generate_unread_results_count_method'));
        add_shortcode('ays_quiz_read_results_count', array($this, 'ays_generate_read_results_count_method'));
        add_shortcode('ays_quiz_quizzes_count', array($this, 'ays_generate_quizzes_count_method'));
        add_shortcode('ays_quiz_categories_count', array($this, 'ays_generate_quiz_categories_method'));
        add_shortcode('ays_quiz_user_website', array($this, 'ays_generate_user_website_method'));
    }

    /*
    ==========================================
        AVG score | Start
    ==========================================
    */

    public function ays_generate_avg_score_method( $attr ){

        $id = (isset($attr['id']) && $attr['id'] != '') ? absint( sanitize_text_field($attr['id']) ) : null;

        if (is_null($id) || $id == 0 ) {
            $user_progress_html = "<p class='wrong_shortcode_text' style='color:red;'>" . __('Wrong shortcode initialized', $this->plugin_name) . "</p>";
            return str_replace(array("\r\n", "\n", "\r"), "\n", $user_progress_html);
        }

        $unique_id = uniqid();
        $this->unique_id = $unique_id;
        $this->unique_id_in_class = $id . "-" . $unique_id;


        $avg_score_quiz_html = $this->ays_quiz_avg_score_html( $id );
        return str_replace(array("\r\n", "\n", "\r"), "\n", $avg_score_quiz_html);
    }

    public function ays_quiz_get_avg_score_by_id( $id ){
        global $wpdb;

        $reports_table = esc_sql( $wpdb->prefix . "aysquiz_reports" );

        if (is_null($id) || $id == 0 ) {
            return null;
        }

        $result = Quiz_Maker_Data::ays_get_average_of_scores($id);

        return $result;

    }

    public function ays_quiz_avg_score_html( $id ){

        $results = $this->ays_quiz_get_avg_score_by_id( $id );

        $content_html = array();

        if( is_null( $results ) || $results == 0 ){
            $content_html = "<p style='text-align: center;font-style:italic;'>" . __( "There are no results yet.", $this->plugin_name ) . "</p>";
            return $content_html;
        }

        $content_html[] = "<span class='". $this->html_name_prefix ."avg-score-box' id='". $this->html_name_prefix ."avg-score-box-". $this->unique_id_in_class ."' data-id='". $this->unique_id ."'>";
            $content_html[] = $results . "%";
        $content_html[] = "</span>";

        $content_html = implode( '' , $content_html);

        return $content_html;
    }

    /*
    ==========================================
        AVG score | End
    ==========================================
    */

    /*
    ==========================================
        Passed users count | Start
    ==========================================
    */

    public function ays_generate_passed_users_count_method( $attr ){

        $id = (isset($attr['id']) && $attr['id'] != '') ? absint( sanitize_text_field($attr['id']) ) : null;

        if (is_null($id) || $id == 0 ) {
            $passed_users_count_html = "<p class='wrong_shortcode_text' style='color:red;'>" . __('Wrong shortcode initialized', $this->plugin_name) . "</p>";
            return str_replace(array("\r\n", "\n", "\r"), "\n", $passed_users_count_html);
        }

        $unique_id = uniqid();
        $this->unique_id = $unique_id;
        $this->unique_id_in_class = $id . "-" . $unique_id;


        $passed_users_count_html = $this->ays_quiz_passed_users_count_html( $id );
        return str_replace(array("\r\n", "\n", "\r"), "\n", $passed_users_count_html);
    }

    public function ays_quiz_passed_users_count_html( $id ){

        $results = Quiz_Maker_Data::get_quiz_results_count_by_id($id);

        $content_html = array();

        if($results === null){
            $content_html = "<p style='text-align: center;font-style:italic;'>" . __( "There are no results yet.", $this->plugin_name ) . "</p>";
            return $content_html;
        }

        $passed_users_count = (isset( $results['res_count'] ) && $results['res_count'] != '') ? sanitize_text_field( $results['res_count'] ) : 0;

        $content_html[] = "<span class='". $this->html_name_prefix ."passed-users-count-box' id='". $this->html_name_prefix ."passed-users-count-box-". $this->unique_id_in_class ."' data-id='". $this->unique_id ."'>";
            $content_html[] = $passed_users_count;
        $content_html[] = "</span>";

        $content_html = implode( '' , $content_html);

        return $content_html;
    }

    /*
    ==========================================
        Passed users count | End
    ==========================================
    */

    /*
    ==========================================
        Failed users count by score | Start
    ==========================================
    */


    public function ays_generate_failed_users_count_by_score_method( $attr ){

        $id = (isset($attr['id']) && $attr['id'] != '') ? absint( sanitize_text_field($attr['id']) ) : null;

        if (is_null($id) || $id == 0 ) {
            $failed_users_count_html = "<p class='wrong_shortcode_text' style='color:red;'>" . __('Wrong shortcode initialized', $this->plugin_name) . "</p>";
            return str_replace(array("\r\n", "\n", "\r"), "\n", $failed_users_count_html);
        }

        $unique_id = uniqid();
        $this->unique_id = $unique_id;
        $this->unique_id_in_class = $id . "-" . $unique_id;


        $failed_users_count_html = $this->ays_quiz_failed_users_count_by_score_html( $id );
        return str_replace(array("\r\n", "\n", "\r"), "\n", $failed_users_count_html);
    }

    public function get_quiz_passed_and_failed_users_count_by_id( $id, $type = 'passed' ) {
        global $wpdb;

        $reports_table = esc_sql( $wpdb->prefix . "aysquiz_reports" );
        $quizzes_table = esc_sql( $wpdb->prefix . "aysquiz_quizes" );

        if (is_null($id) || $id == 0 ) {
            return null;
        }

        $id = absint( $id );

        $sql = "SELECT * FROM {$quizzes_table} WHERE `id`=" . $id;

        $quiz_data = $wpdb->get_row($sql, 'ARRAY_A');

        $options = (isset( $quiz_data['options'] ) && $quiz_data['options'] != '') ? json_decode( $quiz_data['options'], true ) : array();

        $quiz_pass_score = ( isset( $options['pass_score'] ) && $options['pass_score'] != '' ) ? absint( sanitize_text_field( $options['pass_score'] ) ) : 0;

        // Quiz Pass Score type
        $quiz_pass_score_type = (isset($options['quiz_pass_score_type']) && $options['quiz_pass_score_type'] != '') ? sanitize_text_field( $options['quiz_pass_score_type'] ) : 'percentage';

        $sql = "SELECT COUNT(*) AS res_count
                FROM {$reports_table}
                WHERE quiz_id=". $id ." AND `status` = 'finished' ";

        switch ( $quiz_pass_score_type ) {
            case 'point':
                if ($type == 'failed') {
                    $sql .= " AND `points` < {$quiz_pass_score}";
                } else {
                    $sql .= " AND `points` >= {$quiz_pass_score}";
                }
                break;
            
            case 'percentage':
            default:
                if ($type == 'failed') {
                    $sql .= " AND `score` < {$quiz_pass_score}";
                } else {
                    $sql .= " AND `score` >= {$quiz_pass_score}";
                }
                break;
        }


        $results = $wpdb->get_row($sql, 'ARRAY_A');

        return $results;
    }

    public function ays_quiz_failed_users_count_by_score_html( $id ){

        $results = $this->get_quiz_passed_and_failed_users_count_by_id( $id, 'failed' );

        $content_html = array();

        if($results === null){
            $content_html = "<p style='text-align: center;font-style:italic;'>" . __( "There are no results yet.", $this->plugin_name ) . "</p>";
            return $content_html;
        }

        $failed_users_count_by_score = (isset( $results['res_count'] ) && $results['res_count'] != '') ? sanitize_text_field( $results['res_count'] ) : 0;

        $content_html[] = "<span class='". $this->html_name_prefix ."failed-users-count-by-score-box' id='". $this->html_name_prefix ."failed-users-count-by-score-box-". $this->unique_id_in_class ."' data-id='". $this->unique_id ."'>";
            $content_html[] = $failed_users_count_by_score;
        $content_html[] = "</span>";

        $content_html = implode( '' , $content_html);

        return $content_html;
    }

    /*
    ==========================================
        Failed users count by score | End
    ==========================================
    */

    /*
    ==========================================
        Passed users count by score | Start
    ==========================================
    */

    public function ays_generate_passed_users_count_by_score_method( $attr ){

        $id = (isset($attr['id']) && $attr['id'] != '') ? absint( sanitize_text_field($attr['id']) ) : null;

        if (is_null($id) || $id == 0 ) {
            $passed_users_count_html = "<p class='wrong_shortcode_text' style='color:red;'>" . __('Wrong shortcode initialized', $this->plugin_name) . "</p>";
            return str_replace(array("\r\n", "\n", "\r"), "\n", $passed_users_count_html);
        }

        $unique_id = uniqid();
        $this->unique_id = $unique_id;
        $this->unique_id_in_class = $id . "-" . $unique_id;


        $passed_users_count_html = $this->ays_quiz_passed_users_count_by_score_html( $id );
        return str_replace(array("\r\n", "\n", "\r"), "\n", $passed_users_count_html);
    }

    public function ays_quiz_passed_users_count_by_score_html( $id ){

        $results = $this->get_quiz_passed_and_failed_users_count_by_id( $id, 'passed' );

        $content_html = array();

        if($results === null){
            $content_html = "<p style='text-align: center;font-style:italic;'>" . __( "There are no results yet.", $this->plugin_name ) . "</p>";
            return $content_html;
        }

        $failed_users_count_by_score = (isset( $results['res_count'] ) && $results['res_count'] != '') ? sanitize_text_field( $results['res_count'] ) : 0;

        $content_html[] = "<span class='". $this->html_name_prefix ."passed-users-count-by-score-box' id='". $this->html_name_prefix ."passed-users-count-by-score-box-". $this->unique_id_in_class ."' data-id='". $this->unique_id ."'>";
            $content_html[] = $failed_users_count_by_score;
        $content_html[] = "</span>";

        $content_html = implode( '' , $content_html);

        return $content_html;
    }

    /*
    ==========================================
        Passed users count by score | End
    ==========================================
    */


    /*
    ==========================================
        Passed quizzes count per user | Start
    ==========================================
    */
    public function get_user_passed_quizzes_count( $user_id ){
        global $wpdb;

        $reports_table = esc_sql( $wpdb->prefix . "aysquiz_reports" );

        if (is_null($user_id) || $user_id == 0 ) {
            return null;
        }

        $user_id = absint( $user_id );

        $sql = "SELECT COUNT(a.count) FROM ( SELECT COUNT(*) AS count FROM `{$reports_table}` WHERE `user_id` = {$user_id} GROUP BY `quiz_id` ) AS a";

        $results = $wpdb->get_var($sql);

        if ( ! empty( $results ) ) {
            $results = absint( $results );
        } else {
            $results = 0;
        }

        return $results;
    }

    public function ays_generate_user_passed_quizzes_count_method(){

        $unique_id = uniqid();
        $this->unique_id = $unique_id;
        $this->unique_id_in_class = $unique_id;

        $passed_quizzes_count_html = "";
        if(is_user_logged_in()){
            $passed_quizzes_count_html = $this->ays_generate_user_passed_quizzes_count_html();
        }
        return str_replace(array("\r\n", "\n", "\r"), "\n", $passed_quizzes_count_html);
    }

    public function ays_generate_user_passed_quizzes_count_html(){
        $user_id = get_current_user_id();

        $results = $this->get_user_passed_quizzes_count( $user_id );

        $content_html = array();

        if($results === null){
            $content_html = "";
            return $content_html;
        }

        $content_html[] = "<span class='". $this->html_name_prefix ."passed-quizzes-count-per-user' id='". $this->html_name_prefix ."passed-quizzes-count-per-user-". $this->unique_id_in_class ."' data-id='". $this->unique_id ."'>";
            $content_html[] = $results;
        $content_html[] = "</span>";

        $content_html = implode( '' , $content_html);

        return $content_html;
    }

    /*
    ==========================================
        Passed quizzes count per user | End
    ==========================================
    */

    /*
    ==========================================
        All passed quizzes count per user | Start
    ==========================================
    */
    public function get_user_all_passed_quizzes_count( $user_id ){
        global $wpdb;

        $reports_table = esc_sql( $wpdb->prefix . "aysquiz_reports" );

        if (is_null($user_id) || $user_id == 0 ) {
            return null;
        }

        $user_id = absint( $user_id );

        $sql = "SELECT SUM(a.count) FROM ( SELECT COUNT(*) AS count FROM `{$reports_table}` WHERE `user_id` = {$user_id} GROUP BY `quiz_id` ) AS a";

        $results = $wpdb->get_var($sql);

        if ( ! empty( $results ) ) {
            $results = absint( $results );
        } else {
            $results = 0;
        }

        return $results;
    }

    public function ays_generate_user_all_passed_quizzes_count_method(){

        $unique_id = uniqid();
        $this->unique_id = $unique_id;
        $this->unique_id_in_class = $unique_id;

        $all_passed_quizzes_count_html = "";
        if(is_user_logged_in()){
            $all_passed_quizzes_count_html = $this->ays_generate_user_all_passed_quizzes_count_html();
        }
        return str_replace(array("\r\n", "\n", "\r"), "\n", $all_passed_quizzes_count_html);
    }

    public function ays_generate_user_all_passed_quizzes_count_html(){
        $user_id = get_current_user_id();

        $results = $this->get_user_all_passed_quizzes_count( $user_id );

        $content_html = array();

        if( is_null( $results ) || $results == 0 ){
            $content_html = "";
            return $content_html;
        }

        $content_html[] = "<span class='". $this->html_name_prefix ."all-passed-quizzes-count-per-user' id='". $this->html_name_prefix ."all-passed-quizzes-count-per-user-". $this->unique_id_in_class ."' data-id='". $this->unique_id ."'>";
            $content_html[] = $results;
        $content_html[] = "</span>";

        $content_html = implode( '' , $content_html);

        return $content_html;
    }

    /*
    ==========================================
        All passed quizzes count per user | End
    ==========================================
    */

    /*
    ==========================================
        Show User First Name | Start
    ==========================================
    */

    public function get_user_profile_data(){

        /*
         * Quiz message variables for Start Page
         */

        $user_first_name        = '';
        $user_last_name         = '';
        $user_nickname          = '';
        $user_display_name      = '';
        $user_email             = '';
        $user_wordpress_roles   = '';
        $user_wordpress_website = '';

        $user_id = get_current_user_id();
        if($user_id != 0){
            $usermeta = get_user_meta( $user_id );
            if($usermeta !== null){
                $user_first_name = (isset($usermeta['first_name'][0]) && sanitize_text_field( $usermeta['first_name'][0] != '') ) ? sanitize_text_field( $usermeta['first_name'][0] ) : '';
                $user_last_name  = (isset($usermeta['last_name'][0]) && sanitize_text_field( $usermeta['last_name'][0] != '') ) ? sanitize_text_field( $usermeta['last_name'][0] ) : '';
                $user_nickname   = (isset($usermeta['nickname'][0]) && sanitize_text_field( $usermeta['nickname'][0] != '') ) ? sanitize_text_field( $usermeta['nickname'][0] ) : '';
            }

            $current_user_data = get_userdata( $user_id );
            if ( ! is_null( $current_user_data ) && $current_user_data ) {
                $user_display_name = ( isset( $current_user_data->data->display_name ) && $current_user_data->data->display_name != '' ) ? sanitize_text_field( $current_user_data->data->display_name ) : "";
                $user_email = ( isset( $current_user_data->data->user_email ) && $current_user_data->data->user_email != '' ) ? sanitize_text_field( $current_user_data->data->user_email ) : "";

                $user_wordpress_roles = ( isset( $current_user_data->roles ) && ! empty( $current_user_data->roles ) ) ? $current_user_data->roles : "";

                if ( !empty( $user_wordpress_roles ) && $user_wordpress_roles != "" ) {
                    if ( is_array( $user_wordpress_roles ) ) {
                        $user_wordpress_roles = implode(",", $user_wordpress_roles);
                    }
                }

                $user_wordpress_website_url = ( isset( $current_user_data->user_url ) && ! empty( $current_user_data->user_url ) ) ? sanitize_url($current_user_data->user_url) : "";

                if( !empty( $user_wordpress_website_url ) ){
                    $user_wordpress_website = "<a href='". esc_url( $user_wordpress_website_url ) ."' target='_blank' class='ays-quiz-user-website-link-a-tag'>". __( "Website", $this->plugin_name ) ."</a>";
                }
            }
        }

        $message_data = array(
            'user_first_name'           => $user_first_name,
            'user_last_name'            => $user_last_name,
            'user_nickname'             => $user_nickname,
            'user_display_name'         => $user_display_name,
            'user_email'                => $user_email,
            'user_wordpress_roles'      => $user_wordpress_roles,
            'user_wordpress_website'    => $user_wordpress_website,
        );

        return $message_data;
    }

    public function ays_generate_user_first_name_method(){

        $unique_id = uniqid();
        $this->unique_id = $unique_id;
        $this->unique_id_in_class = $unique_id;

        $user_first_name_html = "";
        if(is_user_logged_in()){
            $user_first_name_html = $this->ays_generate_user_first_name_html();
        }
        return str_replace(array("\r\n", "\n", "\r"), "\n", $user_first_name_html);
    }

    public function ays_generate_user_first_name_html(){

        $results = $this->get_user_profile_data();

        $content_html = array();
        
        if( is_null( $results ) || $results == 0 ){
            $content_html = "";
            return $content_html;
        }

        $user_first_name = (isset( $results['user_first_name'] ) && sanitize_text_field( $results['user_first_name'] ) != "") ? sanitize_text_field( $results['user_first_name'] ) : '';

        $content_html[] = "<span class='". $this->html_name_prefix ."user-first-name' id='". $this->html_name_prefix ."user-first-name-". $this->unique_id_in_class ."' data-id='". $this->unique_id ."'>";
            $content_html[] = $user_first_name;
        $content_html[] = "</span>";

        $content_html = implode( '' , $content_html);

        return $content_html;
    }

    /*
    ==========================================
        Show User First Name | End
    ==========================================
    */

    /*
    ==========================================
        Show User Last Name | Start
    ==========================================
    */

    public function ays_generate_user_last_name_method(){

        $unique_id = uniqid();
        $this->unique_id = $unique_id;
        $this->unique_id_in_class = $unique_id;

        $user_last_name_html = "";
        if(is_user_logged_in()){
            $user_last_name_html = $this->ays_generate_user_last_name_html();
        }
        return str_replace(array("\r\n", "\n", "\r"), "\n", $user_last_name_html);
    }

    public function ays_generate_user_last_name_html(){

        $results = $this->get_user_profile_data();

        $content_html = array();
        
        if( is_null( $results ) || $results == 0 ){
            $content_html = "";
            return $content_html;
        }

        $user_last_name = (isset( $results['user_last_name'] ) && sanitize_text_field( $results['user_last_name'] ) != "") ? sanitize_text_field( $results['user_last_name'] ) : '';

        $content_html[] = "<span class='". $this->html_name_prefix ."user-last-name' id='". $this->html_name_prefix ."user-last-name-". $this->unique_id_in_class ."' data-id='". $this->unique_id ."'>";
            $content_html[] = $user_last_name;
        $content_html[] = "</span>";

        $content_html = implode( '' , $content_html);

        return $content_html;
    }

    /*
    ==========================================
        Show User Last Name | End
    ==========================================
    */

    /*
    ==========================================
        Show User Nickname | Start
    ==========================================
    */

    public function ays_generate_user_nickname_method(){

        $unique_id = uniqid();
        $this->unique_id = $unique_id;
        $this->unique_id_in_class = $unique_id;

        $user_nickname_html = "";
        if(is_user_logged_in()){
            $user_nickname_html = $this->ays_generate_user_nickname_html();
        }
        return str_replace(array("\r\n", "\n", "\r"), "\n", $user_nickname_html);
    }

    public function ays_generate_user_nickname_html(){

        $results = $this->get_user_profile_data();

        $content_html = array();
        
        if( is_null( $results ) || $results == 0 ){
            $content_html = "";
            return $content_html;
        }

        $user_nickname = (isset( $results['user_nickname'] ) && sanitize_text_field( $results['user_nickname'] ) != "") ? sanitize_text_field( $results['user_nickname'] ) : '';

        $content_html[] = "<span class='". $this->html_name_prefix ."user-nickname' id='". $this->html_name_prefix ."user-nickname-". $this->unique_id_in_class ."' data-id='". $this->unique_id ."'>";
            $content_html[] = $user_nickname;
        $content_html[] = "</span>";

        $content_html = implode( '' , $content_html);

        return $content_html;
    }

    /*
    ==========================================
        Show User Nickname | End
    ==========================================
    */

    /*
    ==========================================
        Show User Display name | Start
    ==========================================
    */

    public function ays_generate_user_display_name_method(){

        $unique_id = uniqid();
        $this->unique_id = $unique_id;
        $this->unique_id_in_class = $unique_id;

        $user_display_name_html = "";
        if(is_user_logged_in()){
            $user_display_name_html = $this->ays_generate_user_display_name_html();
        }
        return str_replace(array("\r\n", "\n", "\r"), "\n", $user_display_name_html);
    }

    public function ays_generate_user_display_name_html(){

        $results = $this->get_user_profile_data();

        $content_html = array();
        
        if( is_null( $results ) || $results == 0 ){
            $content_html = "";
            return $content_html;
        }

        $user_display_name = (isset( $results['user_display_name'] ) && sanitize_text_field( $results['user_display_name'] ) != "") ? sanitize_text_field( $results['user_display_name'] ) : '';

        $content_html[] = "<span class='". $this->html_name_prefix ."user-display-name' id='". $this->html_name_prefix ."user-display-name-". $this->unique_id_in_class ."' data-id='". $this->unique_id ."'>";
            $content_html[] = $user_display_name;
        $content_html[] = "</span>";

        $content_html = implode( '' , $content_html);

        return $content_html;
    }

    /*
    ==========================================
        Show User Display name | End
    ==========================================
    */

    /*
    ==========================================
        Show User Email | Start
    ==========================================
    */

    public function ays_generate_user_email_method(){

        $unique_id = uniqid();
        $this->unique_id = $unique_id;
        $this->unique_id_in_class = $unique_id;

        $user_email_html = "";
        if(is_user_logged_in()){
            $user_email_html = $this->ays_generate_user_email_html();
        }
        return str_replace(array("\r\n", "\n", "\r"), "\n", $user_email_html);
    }

    public function ays_generate_user_email_html(){

        $results = $this->get_user_profile_data();

        $content_html = array();
        
        if( is_null( $results ) || $results == 0 ){
            $content_html = "";
            return $content_html;
        }

        $user_email = (isset( $results['user_email'] ) && sanitize_text_field( $results['user_email'] ) != "") ? sanitize_text_field( $results['user_email'] ) : '';

        $content_html[] = "<span class='". $this->html_name_prefix ."user-email' id='". $this->html_name_prefix ."user-email-". $this->unique_id_in_class ."' data-id='". $this->unique_id ."'>";
            $content_html[] = $user_email;
        $content_html[] = "</span>";

        $content_html = implode( '' , $content_html);

        return $content_html;
    }

    /*
    ==========================================
        Show User Email | End
    ==========================================
    */

    /*
    ==========================================
        Show user quiz duration | Start
    ==========================================
    */

    public function get_curent_user_quiz_duration(){
        global $wpdb;

        $user_id = get_current_user_id();

        $reports_table = esc_sql( $wpdb->prefix . "aysquiz_reports" );

        if (is_null($user_id) || $user_id == 0 ) {
            return null;
        }

        $user_id = absint( sanitize_text_field( $user_id ) );

        $sql = "SELECT SUM(`duration`) FROM `{$reports_table}` WHERE `user_id` = {$user_id}";

        $results = $wpdb->get_var($sql);

        if ( ! empty( $results ) ) {
            $results = absint( $results );
        } else {
            $results = 0;
        }

        return $results;
    }

    public function ays_generate_user_quiz_duration_method(){

        $unique_id = uniqid();
        $this->unique_id = $unique_id;
        $this->unique_id_in_class = $unique_id;

        $user_quiz_duration = "";
        if(is_user_logged_in()){
            $user_quiz_duration = $this->ays_generate_user_quiz_duration_html();
        }
        return str_replace(array("\r\n", "\n", "\r"), "\n", $user_quiz_duration);
    }

    public function ays_generate_user_quiz_duration_html(){

        $results = $this->get_curent_user_quiz_duration();

        $quiz_duration = "";
        if ( class_exists( "Quiz_Maker_Data" ) ) {
            if ( !is_null( $results ) && !empty( $results ) ) {
                $quiz_duration = Quiz_Maker_Data::secondsToWords($results);
            }
        }

        $content_html = array();
        
        if( is_null( $quiz_duration ) || $quiz_duration == "" ){
            $content_html = "";
            return $content_html;
        }

        $content_html[] = "<span class='". $this->html_name_prefix ."user-quiz-duration' id='". $this->html_name_prefix ."user-quiz-duration-". $this->unique_id_in_class ."' data-id='". $this->unique_id ."'>";
            $content_html[] = $quiz_duration;
        $content_html[] = "</span>";

        $content_html = implode( '' , $content_html);

        return $content_html;
    }

    /*
    ==========================================
        Show user quiz duration | End
    ==========================================
    */

    /*
    ==========================================
        Show quiz creation date | Start
    ==========================================
    */

    public function get_curent_quiz_creation_date( $id ){
        global $wpdb;

        $quizzes_table = esc_sql( $wpdb->prefix . "aysquiz_quizes" );

        if (is_null($id) || $id == 0 ) {
            return null;
        }

        $id = absint( $id );

        $sql = "SELECT `create_date` FROM `{$quizzes_table}` WHERE `id` = {$id}";

        $results = $wpdb->get_var($sql);

        if ( is_null( $results ) || empty( $results ) ) {
            $results = null;
        }

        return $results;
    }

    public function ays_generate_quiz_creation_date_method( $attr ){

        $id = (isset($attr['id']) && $attr['id'] != '') ? absint( sanitize_text_field($attr['id']) ) : null;

        if (is_null($id) || $id == 0 ) {
            $quiz_creation_date = "";
            return str_replace(array("\r\n", "\n", "\r"), "\n", $quiz_creation_date);
        }

        $unique_id = uniqid();
        $this->unique_id = $unique_id;
        $this->unique_id_in_class = $unique_id;

        $quiz_creation_date = "";
        if(is_user_logged_in()){
            $quiz_creation_date = $this->ays_generate_quiz_creation_date_html( $id );
        }
        return str_replace(array("\r\n", "\n", "\r"), "\n", $quiz_creation_date);
    }

    public function ays_generate_quiz_creation_date_html( $id ){

        $results = $this->get_curent_quiz_creation_date( $id );

        $content_html = array();
        
        if( is_null( $results ) || empty( $results ) ){
            $content_html = "";
            return $content_html;
        }

        $quiz_creation_date = (isset($results) && $results != '') ? sanitize_text_field( $results ) : "";
        if ( $quiz_creation_date != "" ) {
            $quiz_creation_date = date_i18n( get_option( 'date_format' ), strtotime( $quiz_creation_date ) );
        }

        $content_html[] = "<span class='". $this->html_name_prefix ."quiz-creation-date' id='". $this->html_name_prefix ."quiz-creation-date-". $this->unique_id_in_class ."' data-id='". $this->unique_id ."'>";
            $content_html[] = $quiz_creation_date;
        $content_html[] = "</span>";

        $content_html = implode( '' , $content_html);

        return $content_html;
    }

    /*
    ==========================================
        Show quiz creation date | End
    ==========================================
    */

    /*
    ==========================================
        Show current quiz author | Start
    ==========================================
    */

    public function get_curent_quiz_author( $id ){
        global $wpdb;

        $quizzes_table = esc_sql( $wpdb->prefix . "aysquiz_quizes" );

        if (is_null($id) || $id == 0 ) {
            return null;
        }

        $id = absint( $id );

        $sql = "SELECT `author_id` FROM `{$quizzes_table}` WHERE `id` = {$id}";

        $results = $wpdb->get_var($sql);

        if ( is_null( $results ) || empty( $results ) ) {
            $results = null;
        }

        return $results;
    }

    public function ays_generate_current_quiz_author_method( $attr ) {

        $id = (isset($attr['id']) && $attr['id'] != '') ? absint( sanitize_text_field($attr['id']) ) : null;

        if (is_null($id) || $id == 0 ) {
            $quiz_author = "";
            return str_replace(array("\r\n", "\n", "\r"), "\n", $quiz_author);
        }

        $unique_id = uniqid();
        $this->unique_id = $unique_id;
        $this->unique_id_in_class = $unique_id;

        $quiz_author = "";
        if(is_user_logged_in()){
            $quiz_author = $this->ays_generate_current_quiz_author_html( $id );
        }
        return str_replace(array("\r\n", "\n", "\r"), "\n", $quiz_author);
    }

    public function ays_generate_current_quiz_author_html( $id ) {

        $results = $this->get_curent_quiz_author( $id );

        $content_html = array();
        
        if( is_null( $results ) || empty( $results ) ){
            $content_html = "";
            return $content_html;
        }

        $author_id = (isset($results) && intval( $results ) != 0) ? intval( $results ) : 0;
        $author = null;
        if( $author_id != 0){
            $author = get_userdata( $author_id );
        }
        
        $quiz_author = __( "Unknown", $this->plugin_name );
        if( $author !== null ){
            $quiz_author = $author->data->display_name;
        }

        $content_html[] = "<span class='". $this->html_name_prefix ."current-quiz-author' id='". $this->html_name_prefix ."current-quiz-author-". $this->unique_id_in_class ."' data-id='". $this->unique_id ."'>";
            $content_html[] = $quiz_author;
        $content_html[] = "</span>";

        $content_html = implode( '' , $content_html);

        return $content_html;
    }

    /*
    ==========================================
        Show current quiz author | End
    ==========================================
    */

    /*
    ==========================================
        Show quiz questions count | Start
    ==========================================
    */

    public function ays_generate_questions_count_method( $attr ) {

        $id = (isset($attr['id']) && $attr['id'] != '') ? absint( sanitize_text_field($attr['id']) ) : null;

        if (is_null($id) || $id == 0 ) {
            $quiz_author = "";
            return str_replace(array("\r\n", "\n", "\r"), "\n", $quiz_author);
        }

        $unique_id = uniqid();
        $this->unique_id = $unique_id;
        $this->unique_id_in_class = $unique_id;

        $quiz_author = $this->ays_generate_questions_count_html( $id );

        return str_replace(array("\r\n", "\n", "\r"), "\n", $quiz_author);
    }

    public function ays_generate_questions_count_html( $id ) {

        $results = $this->get_quiz_questions_count( $id );

        $content_html = array();
        
        if( is_null( $results ) || empty( $results ) ){
            $content_html = "";
            return $content_html;
        }

        $content_html[] = "<span class='". $this->html_name_prefix ."questions-count' id='". $this->html_name_prefix ."questions-count-". $this->unique_id_in_class ."' data-id='". $this->unique_id ."'>";
            $content_html[] = $results;
        $content_html[] = "</span>";

        $content_html = implode( '' , $content_html);

        return $content_html;
    }

    public function get_quiz_questions_count( $id ){
        global $wpdb;

        $sql = "SELECT `question_ids`
                FROM `{$wpdb->prefix}aysquiz_quizes`
                WHERE `published`=1 AND `id`=" . absint( $id );

        $questions_str = $wpdb->get_row( $sql, 'ARRAY_A');

        $questions = "";
        if ( !empty( $questions_str ) ) {
            $question_ids_str = (isset( $questions_str['question_ids'] ) && $questions_str['question_ids'] != "") ? $questions_str['question_ids'] : "";
            $questions_arr = explode(',', $question_ids_str);

            if ( !empty( $questions_arr ) ) {
                $questions = count($questions_arr);
            }
        }
        return $questions;
    }

    /*
    ==========================================
        Show quiz questions count | End
    ==========================================
    */

    /*
    ==========================================
        Show quiz category title | Start
    ==========================================
    */

    public function ays_generate_category_title_method( $attr ) {

        $id = (isset($attr['id']) && $attr['id'] != '') ? absint( sanitize_text_field($attr['id']) ) : null;

        if (is_null($id) || $id == 0 ) {
            $quiz_category_title = "";
            return str_replace(array("\r\n", "\n", "\r"), "\n", $quiz_category_title);
        }

        $unique_id = uniqid();
        $this->unique_id = $unique_id;
        $this->unique_id_in_class = $unique_id;

        $quiz_category_title = $this->ays_generate_category_title_html( $id );

        return str_replace(array("\r\n", "\n", "\r"), "\n", $quiz_category_title);
    }

    public function ays_generate_category_title_html( $id ) {

        $quiz_data = self::get_quiz_by_id($id);

        if( is_null( $quiz_data ) || empty( $quiz_data ) ){
            $content_html = "";
            return $content_html;
        }

        $quiz_category_id = (isset($quiz_data['quiz_category_id']) && $quiz_data['quiz_category_id'] != '') ? absint( sanitize_text_field($quiz_data['quiz_category_id']) ) : "";

        if ( $quiz_category_id == "" || $quiz_category_id == 0 ) {
            $content_html = "";
            return $content_html;
        }

        $results = Quiz_Maker_Data::get_quiz_category_by_id($quiz_category_id);

        $content_html = array();
        
        if( is_null( $results ) || empty( $results ) ){
            $content_html = "";
            return $content_html;
        }

        $category_title = (isset($results['title']) && $results['title'] != '') ? sanitize_text_field($results['title']) : "";

        if ( $category_title == "" ) {
            $content_html = "";
            return $content_html;
        }

        $content_html[] = "<span class='". $this->html_name_prefix ."category-title' id='". $this->html_name_prefix ."category-title-". $this->unique_id_in_class ."' data-id='". $this->unique_id ."'>";
            $content_html[] = $category_title;
        $content_html[] = "</span>";

        $content_html = implode( '' , $content_html);

        return $content_html;
    }

    public static function get_quiz_by_id($id){
        global $wpdb;

        $sql = "SELECT *
                FROM {$wpdb->prefix}aysquiz_quizes
                WHERE id=" . absint($id);

        $quiz = $wpdb->get_row($sql, 'ARRAY_A');

        return $quiz;
    }

    /*
    ==========================================
        Show quiz category title | End
    ==========================================
    */

    /*
    ==========================================
        Show quiz category description | Start
    ==========================================
    */

    public function ays_generate_category_description_method( $attr ) {

        $id = (isset($attr['id']) && $attr['id'] != '') ? absint( sanitize_text_field($attr['id']) ) : null;

        if (is_null($id) || $id == 0 ) {
            $quiz_category_title = "";
            return str_replace(array("\r\n", "\n", "\r"), "\n", $quiz_category_title);
        }

        $unique_id = uniqid();
        $this->unique_id = $unique_id;
        $this->unique_id_in_class = $unique_id;

        $quiz_category_title = $this->ays_generate_category_description_html( $id );

        return str_replace(array("\r\n", "\n", "\r"), "\n", $quiz_category_title);
    }

    public function ays_generate_category_description_html( $id ) {

        $quiz_data = self::get_quiz_by_id($id);

        if( is_null( $quiz_data ) || empty( $quiz_data ) ){
            $content_html = "";
            return $content_html;
        }

        $quiz_category_id = (isset($quiz_data['quiz_category_id']) && $quiz_data['quiz_category_id'] != '') ? absint( sanitize_text_field($quiz_data['quiz_category_id']) ) : "";

        if ( $quiz_category_id == "" || $quiz_category_id == 0 ) {
            $content_html = "";
            return $content_html;
        }

        $results = Quiz_Maker_Data::get_quiz_category_by_id($quiz_category_id);

        $content_html = array();
        
        if( is_null( $results ) || empty( $results ) ){
            $content_html = "";
            return $content_html;
        }

        $category_description = (isset($results['description']) && $results['description'] != '') ? Quiz_Maker_Data::ays_autoembed($results['description']) : "";

        if ( $category_description == "" ) {
            $content_html = "";
            return $content_html;
        }

        $content_html[] = "<div class='". $this->html_name_prefix ."category-description' id='". $this->html_name_prefix ."category-description-". $this->unique_id_in_class ."' data-id='". $this->unique_id ."'>";
            $content_html[] = $category_description;
        $content_html[] = "</div>";

        $content_html = implode( '' , $content_html);

        return $content_html;
    }

    /*
    ==========================================
        Show quiz category description | End
    ==========================================
    */

    /*
    ==========================================
        Show quiz question category title | Start
    ==========================================
    */

    public function ays_generate_question_categories_title_method( $attr ) {

        $id = (isset($attr['id']) && $attr['id'] != '') ? absint( sanitize_text_field($attr['id']) ) : null;

        if (is_null($id) || $id == 0 ) {
            $quiz_question_category_title = "";
            return str_replace(array("\r\n", "\n", "\r"), "\n", $quiz_question_category_title);
        }

        $unique_id = uniqid();
        $this->unique_id = $unique_id;
        $this->unique_id_in_class = $unique_id;

        $quiz_question_category_title = $this->ays_generate_question_categories_title_html( $id );

        return str_replace(array("\r\n", "\n", "\r"), "\n", $quiz_question_category_title);
    }

    public function ays_generate_question_categories_title_html( $id ) {

        $results = Quiz_Maker_Data::get_question_category_by_id($id);

        $content_html = array();
        
        if( is_null( $results ) || empty( $results ) ){
            $content_html = "";
            return $content_html;
        }

        $question_category_title = (isset($results['title']) && $results['title'] != '') ? sanitize_text_field($results['title']) : "";

        if ( $question_category_title == "" ) {
            $content_html = "";
            return $content_html;
        }

        $content_html[] = "<span class='". $this->html_name_prefix ."question-category-title' id='". $this->html_name_prefix ."question-category-title-". $this->unique_id_in_class ."' data-id='". $this->unique_id ."'>";
            $content_html[] = $question_category_title;
        $content_html[] = "</span>";

        $content_html = implode( '' , $content_html);

        return $content_html;
    }

    /*
    ==========================================
        Show quiz question category title | End
    ==========================================
    */

     /*
    ==========================================
        Show quiz question category title | Start
    ==========================================
    */

    public function ays_generate_question_categories_description_method( $attr ) {

        $id = (isset($attr['id']) && $attr['id'] != '') ? absint( sanitize_text_field($attr['id']) ) : null;

        if (is_null($id) || $id == 0 ) {
            $quiz_question_category_description = "";
            return str_replace(array("\r\n", "\n", "\r"), "\n", $quiz_question_category_description);
        }

        $unique_id = uniqid();
        $this->unique_id = $unique_id;
        $this->unique_id_in_class = $unique_id;

        $quiz_question_category_description = $this->ays_generate_question_categories_description_html( $id );

        return str_replace(array("\r\n", "\n", "\r"), "\n", $quiz_question_category_description);
    }

    public function ays_generate_question_categories_description_html( $id ) {

        $results = Quiz_Maker_Data::get_question_category_by_id($id);

        $content_html = array();
        
        if( is_null( $results ) || empty( $results ) ){
            $content_html = "";
            return $content_html;
        }

        $question_category_description = (isset($results['description']) && $results['description'] != '') ? Quiz_Maker_Data::ays_autoembed($results['description']) : "";

        if ( $question_category_description == "" ) {
            $content_html = "";
            return $content_html;
        }

        $content_html[] = "<span class='". $this->html_name_prefix ."question-category-description' id='". $this->html_name_prefix ."question-category-description-". $this->unique_id_in_class ."' data-id='". $this->unique_id ."'>";
            $content_html[] = $question_category_description;
        $content_html[] = "</span>";

        $content_html = implode( '' , $content_html);

        return $content_html;
    }

    /*
    ==========================================
        Show quiz question category title | End
    ==========================================
    */

    /*
    ==========================================
        Show User Email | Start
    ==========================================
    */

    public function ays_generate_user_roles_method(){

        $unique_id = uniqid();
        $this->unique_id = $unique_id;
        $this->unique_id_in_class = $unique_id;

        $user_email_html = "";
        if(is_user_logged_in()){
            $user_email_html = $this->ays_generate_user_roles_html();
        }
        return str_replace(array("\r\n", "\n", "\r"), "\n", $user_email_html);
    }

    public function ays_generate_user_roles_html(){

        $results = $this->get_user_profile_data();

        $content_html = array();
        
        if( is_null( $results ) || $results == 0 ){
            $content_html = "";
            return $content_html;
        }

        $user_wordpress_roles = (isset( $results['user_wordpress_roles'] ) && sanitize_text_field( $results['user_wordpress_roles'] ) != "") ? sanitize_text_field( $results['user_wordpress_roles'] ) : '';

        $content_html[] = "<span class='". $this->html_name_prefix ."user-wordpress-roles' id='". $this->html_name_prefix ."user-wordpress-roles-". $this->unique_id_in_class ."' data-id='". $this->unique_id ."'>";
            $content_html[] = $user_wordpress_roles;
        $content_html[] = "</span>";

        $content_html = implode( '' , $content_html);

        return $content_html;
    }

    /*
    ==========================================
        Show User Email | End
    ==========================================
    */

    /*
    ==========================================
        Unread results count | Start
    ==========================================
    */

    public function ays_generate_unread_results_count_method( $attr ){

        $id = (isset($attr['id']) && $attr['id'] != '') ? absint( sanitize_text_field($attr['id']) ) : null;

        if (is_null($id) || $id == 0 ) {
            $passed_users_count_html = "<p class='wrong_shortcode_text' style='color:red;'>" . __('Wrong shortcode initialized', $this->plugin_name) . "</p>";
            return str_replace(array("\r\n", "\n", "\r"), "\n", $passed_users_count_html);
        }

        $unique_id = uniqid();
        $this->unique_id = $unique_id;
        $this->unique_id_in_class = $id . "-" . $unique_id;


        $passed_users_count_html = $this->ays_quiz_unread_results_count_html( $id );
        return str_replace(array("\r\n", "\n", "\r"), "\n", $passed_users_count_html);
    }

    public function get_quiz_read_unread_results_count_by_id($id, $read_unread = 0){
        global $wpdb;

        $read_unread = (isset( $read_unread ) && $read_unread != "") ? absint( $read_unread ) : 0;

        $reports_table = esc_sql( $wpdb->prefix . "aysquiz_reports" );

        if (is_null($id) || $id == 0 ) {
            return null;
        }

        $sql = "SELECT COUNT(*) AS res_count
                FROM {$reports_table}
                WHERE `quiz_id`= {$id} AND `read` = ".$read_unread;

        $quiz = $wpdb->get_row($sql, 'ARRAY_A');

        return $quiz;
    }

    public function ays_quiz_unread_results_count_html( $id ){

        $results = $this->get_quiz_read_unread_results_count_by_id( $id, 0 );

        $content_html = array();

        if($results === null){
            $content_html = "<p style='text-align: center;font-style:italic;'>" . __( "There are no results yet.", $this->plugin_name ) . "</p>";
            return $content_html;
        }

        $unread_results_count = (isset( $results['res_count'] ) && $results['res_count'] != '') ? sanitize_text_field( $results['res_count'] ) : 0;

        $content_html[] = "<span class='". $this->html_name_prefix ."unread-results-box' id='". $this->html_name_prefix ."unread-results-box-". $this->unique_id_in_class ."' data-id='". $this->unique_id ."'>";
            $content_html[] = $unread_results_count;
        $content_html[] = "</span>";

        $content_html = implode( '' , $content_html);

        return $content_html;
    }

    /*
    ==========================================
        Unread results count | End
    ==========================================
    */

    /*
    ==========================================
        Read results count | Start
    ==========================================
    */

    public function ays_generate_read_results_count_method( $attr ){

        $id = (isset($attr['id']) && $attr['id'] != '') ? absint( sanitize_text_field($attr['id']) ) : null;

        if (is_null($id) || $id == 0 ) {
            $read_results_count_html = "<p class='wrong_shortcode_text' style='color:red;'>" . __('Wrong shortcode initialized', $this->plugin_name) . "</p>";
            return str_replace(array("\r\n", "\n", "\r"), "\n", $read_results_count_html);
        }

        $unique_id = uniqid();
        $this->unique_id = $unique_id;
        $this->unique_id_in_class = $id . "-" . $unique_id;


        $read_results_count_html = $this->ays_quiz_read_results_count_html( $id );
        return str_replace(array("\r\n", "\n", "\r"), "\n", $read_results_count_html);
    }

    public function ays_quiz_read_results_count_html( $id ){

        $results = $this->get_quiz_read_unread_results_count_by_id( $id, 1 );

        $content_html = array();

        if($results === null){
            $content_html = "<p style='text-align: center;font-style:italic;'>" . __( "There are no results yet.", $this->plugin_name ) . "</p>";
            return $content_html;
        }

        $read_results_count = (isset( $results['res_count'] ) && $results['res_count'] != '') ? sanitize_text_field( $results['res_count'] ) : 0;

        if ( $read_results_count == 0 ) {
            $content_html = "";
            return $content_html;
        }

        $content_html[] = "<span class='". $this->html_name_prefix ."read-results-box' id='". $this->html_name_prefix ."read-results-box-". $this->unique_id_in_class ."' data-id='". $this->unique_id ."'>";
            $content_html[] = $read_results_count;
        $content_html[] = "</span>";

        $content_html = implode( '' , $content_html);

        return $content_html;
    }

    /*
    ==========================================
        Read results count | End
    ==========================================
    */

    /*
    ==========================================
        Quizzes count | Start
    ==========================================
    */

    public function ays_generate_quizzes_count_method(){

        $unique_id = uniqid();
        $this->unique_id = $unique_id;
        $this->unique_id_in_class = 'count' . "-" . $unique_id;

        $read_results_count_html = $this->ays_quiz_quizzes_count_html();
        return str_replace(array("\r\n", "\n", "\r"), "\n", $read_results_count_html);
    }

    public function ays_quiz_quizzes_count_html(){

        $results = $this->ays_quiz_get_quizzes_count();
        $content_html = array();

        if( is_null($results) || $results == 0){
            $content_html = "<p style='text-align: center;font-style:italic;'>" . __( "There are no results yet.", $this->plugin_name ) . "</p>";
            return $content_html;
        }

        $read_results_count = (isset( $results['res_count'] ) && $results['res_count'] != '') ? sanitize_text_field( $results['res_count'] ) : 0;

        if ( $read_results_count == 0 ) {
            $content_html = "";
            return $content_html;
        }

        $content_html[] = "<span class='". $this->html_name_prefix ."quizzes-count' id='". $this->html_name_prefix ."quizzes-count-". $this->unique_id_in_class ."' data-id='". $this->unique_id ."'>";
            $content_html[] = $read_results_count;
        $content_html[] = "</span>";

        $content_html = implode( '' , $content_html);

        return $content_html;
    }

    public static function ays_quiz_get_quizzes_count(){
        global $wpdb;

        $sql = "SELECT count(*) as res_count FROM {$wpdb->prefix}aysquiz_quizes WHERE `published` = 1";

        $quiz = $wpdb->get_row($sql, 'ARRAY_A');

        return $quiz;
    }

    /*
    ==========================================
        Quizzes count | End
    ==========================================
    */

    /*
    ==========================================
        Quiz categories count | Start
    ==========================================
    */

    public function ays_generate_quiz_categories_method(){

        $unique_id = uniqid();
        $this->unique_id = $unique_id;
        $this->unique_id_in_class = 'count' . "-" . $unique_id;

        $read_results_count_html = $this->ays_quiz_quiz_categories_count_html();
        return str_replace(array("\r\n", "\n", "\r"), "\n", $read_results_count_html);
    }

    public function ays_quiz_quiz_categories_count_html(){

        $results = $this->ays_quiz_get_quiz_categories_count();
        $content_html = array();

        if( is_null($results) || $results == 0){
            $content_html = "<p style='text-align: center;font-style:italic;'>" . __( "There are no results yet.", $this->plugin_name ) . "</p>";
            return $content_html;
        }

        $read_results_count = (isset( $results['res_count'] ) && $results['res_count'] != '') ? sanitize_text_field( $results['res_count'] ) : 0;

        if ( $read_results_count == 0 ) {
            $content_html = "";
            return $content_html;
        }

        $content_html[] = "<span class='". $this->html_name_prefix ."quiz-categories-count' id='". $this->html_name_prefix ."quiz-categories-". $this->unique_id_in_class ."' data-id='". $this->unique_id ."'>";
            $content_html[] = $read_results_count;
        $content_html[] = "</span>";

        $content_html = implode( '' , $content_html);

        return $content_html;
    }

    public static function ays_quiz_get_quiz_categories_count(){
        global $wpdb;

        $sql = "SELECT count(*) as res_count FROM {$wpdb->prefix}aysquiz_quizcategories";

        $quiz = $wpdb->get_row($sql, 'ARRAY_A');

        return $quiz;
    }

    /*
    ==========================================
        Quiz categories count | End
    ==========================================
    */

    /*
    ==========================================
        Show User Website | Start
    ==========================================
    */

    public function ays_generate_user_website_method(){

        $unique_id = uniqid();
        $this->unique_id = $unique_id;
        $this->unique_id_in_class = $unique_id;

        $user_email_html = "";
        if(is_user_logged_in()){
            $user_email_html = $this->ays_generate_user_website_html();
        }
        return str_replace(array("\r\n", "\n", "\r"), "\n", $user_email_html);
    }

    public function ays_generate_user_website_html(){

        $results = $this->get_user_profile_data();

        $content_html = array();
        
        if( is_null( $results ) || $results == 0 ){
            $content_html = "";
            return $content_html;
        }

        $user_wordpress_website = (isset( $results['user_wordpress_website'] ) && $results['user_wordpress_website'] ) != "" ? $results['user_wordpress_website'] : '';

        $content_html[] = "<span class='". $this->html_name_prefix ."user-wordpress-website' id='". $this->html_name_prefix ."user-wordpress-website-". $this->unique_id_in_class ."' data-id='". $this->unique_id ."'>";
            $content_html[] = $user_wordpress_website;
        $content_html[] = "</span>";

        $content_html = implode( '' , $content_html);

        return $content_html;
    }

    /*
    ==========================================
        Show User Website | End
    ==========================================
    */


}
