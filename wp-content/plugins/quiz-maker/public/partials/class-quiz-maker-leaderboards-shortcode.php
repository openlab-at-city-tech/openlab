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
class Quiz_Maker_Leaderboards_Shortcode
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

        add_shortcode('ays_quiz_leaderboard', array($this, 'ays_generate_leaderboard_list'));
        add_shortcode('ays_quiz_gleaderboard', array($this, 'ays_generate_gleaderboard_list'));
        add_shortcode('ays_quiz_cat_gleaderboard', array($this, 'ays_generate_global_quiz_cat_leaderboard_list'));

        $this->settings = new Quiz_Maker_Settings_Actions($this->plugin_name);
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles(){

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Quiz_Maker_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Quiz_Maker_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->plugin_name . '-dataTable-min', AYS_QUIZ_PUBLIC_URL . '/css/quiz-maker-dataTables.min.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Quiz_Maker_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Quiz_Maker_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script( $this->plugin_name . '-datatable-min', AYS_QUIZ_PUBLIC_URL . '/js/quiz-maker-datatable.min.js', array('jquery'), $this->version, true);
        wp_enqueue_script( $this->plugin_name . '-leaderboards-public', AYS_QUIZ_PUBLIC_URL . '/js/partials/quiz-maker-leaderboards-public.js', array('jquery'), $this->version, true);
        wp_localize_script( $this->plugin_name . '-datatable-min', 'quizLangLeaderboardDataTableObj', array(
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

            "all"                   => __( "All", $this->plugin_name ),
        ) );
    }

    // Leaderboard shortcode
    public function ays_generate_leaderboard_list($attr){
        // AV Leaderboard
        // ob_start();
        global $wpdb;

        $this->enqueue_styles();
        $this->enqueue_scripts();

        $quiz_settings = $this->settings;
        $leadboard_res = ($quiz_settings->ays_get_setting('leaderboard') === false) ? json_encode(array()) : $quiz_settings->ays_get_setting('leaderboard');
        $quiz_settings_options = ($quiz_settings->ays_get_setting('options') === false) ? json_encode(array()) : $quiz_settings->ays_get_setting('options');

        $custom_fields = Quiz_Maker_Data::get_custom_fields_for_shortcodes();

        // Individual Leaderboard
        $individual_leaderboard_custom_fields = isset($custom_fields['individual_leaderboard']) && !empty($custom_fields['individual_leaderboard']) ? $custom_fields['individual_leaderboard'] : array();

        $leadboard = json_decode($leadboard_res, true);

        // $ind_leadboard_count = isset($leadboard['individual']['count']) ? $leadboard['individual']['count'] : '5' ;
        // $ind_leadboard_width = isset($leadboard['individual']['width']) ? $leadboard['individual']['width'] : '0' ;
        // $ind_leadboard_width = intval($ind_leadboard_width) == 0 ? '100%' : $ind_leadboard_width ."px";

        $ind_leadboard_count = isset($leadboard['individual']['count']) ? $leadboard['individual']['count'] : '5' ;
        $ind_leadboard_width = isset($leadboard['individual']['width']) ? $leadboard['individual']['width'] : '0' ;
        $ind_leadboard_width = intval($ind_leadboard_width) == 0 ? '100%' : $ind_leadboard_width ."px";
        $ind_leadboard_orderby = isset($leadboard['individual']['orderby']) ? $leadboard['individual']['orderby'] : 'id' ;
        $ind_leadboard_sort = isset($leadboard['individual']['sort']) ? $leadboard['individual']['sort'] : 'avg' ;
        $ind_leadboard_color = isset($leadboard['individual']['color']) ? $leadboard['individual']['color'] : '#99BB5A' ;
        $ind_leadboard_suctom_css = (isset($leadboard['individual']['leadboard_custom_css']) && $leadboard['individual']['leadboard_custom_css'] != '') ? $leadboard['individual']['leadboard_custom_css'] : '';
        $ind_leadboard_points_display = (isset($leadboard['individual']['leadboard_points_display']) && $leadboard['individual']['leadboard_points_display'] != '') ? $leadboard['individual']['leadboard_points_display'] : 'without_max_point';


        $default_ind_leadboard_columns = array(
            'pos'        => 'pos',
            'name'       => 'name',
            'duration'   => 'duration',
            'score'      => 'score',
            'point'      => '',
        );

        if( !empty($individual_leaderboard_custom_fields) ){
            foreach ($individual_leaderboard_custom_fields as $custom_field_key => $custom_field) {
                $default_ind_leadboard_columns[$custom_field_key] = $custom_field_key;
            }
        }

        $leadboard['individual']['ind_leadboard_columns'] = ! isset( $leadboard['individual']['ind_leadboard_columns'] ) ? $default_ind_leadboard_columns : $leadboard['individual']['ind_leadboard_columns'];
        $ind_leadboard_columns = (isset( $leadboard['individual']['ind_leadboard_columns'] ) && !empty($leadboard['individual']['ind_leadboard_columns']) ) ? $leadboard['individual']['ind_leadboard_columns'] : array();
        $ind_leadboard_columns_order = (isset( $leadboard['individual']['ind_leadboard_columns_order'] ) && !empty($leadboard['individual']['ind_leadboard_columns_order']) ) ? $leadboard['individual']['ind_leadboard_columns_order'] : $default_ind_leadboard_columns;

        // Enable pagination
        $leadboard['individual']['leadboard_enable_pagination'] = isset($leadboard['individual']['leadboard_enable_pagination']) ? sanitize_text_field( $leadboard['individual']['leadboard_enable_pagination'] ) : 'on';
        $leadboard_enable_pagination = (isset($leadboard['individual']['leadboard_enable_pagination']) && sanitize_text_field( $leadboard['individual']['leadboard_enable_pagination'] ) == "on") ? true : false;

        // Enable User Avatar
        $leadboard['individual']['leadboard_enable_user_avatar'] = isset($leadboard['individual']['leadboard_enable_user_avatar']) ? sanitize_text_field( $leadboard['individual']['leadboard_enable_user_avatar'] ) : 'off';
        $leadboard_enable_user_avatar = (isset($leadboard['individual']['leadboard_enable_user_avatar']) && sanitize_text_field( $leadboard['individual']['leadboard_enable_user_avatar'] ) == "on") ? true : false;

        $enable_pagination_class = '';
        if ( $leadboard_enable_pagination ) {
            $enable_pagination_class = 'ays-quiz-individual-leaderboard-pagination';
        }

        $default_ind_leadboard_header_value = array(
            "pos"        => "<th class='ays_lb_pos'>" . __( "Pos.", $this->plugin_name ) . "</th>",
            "name"       => "<th class='ays_lb_user'>" . __( "Name", $this->plugin_name ) . "</th>",
            "score"      => "<th class='ays_lb_score'>" . __( "Score", $this->plugin_name ) . "</th>",
            "duration"   => "<th class='ays_lb_duration'>" . __( "Duration", $this->plugin_name ) . "</th>",
            "points"     => "<th class='ays_lb_points'>" . __( "Points", $this->plugin_name ) . "</th>",
        );

        if( !empty($individual_leaderboard_custom_fields) ){
            foreach ($individual_leaderboard_custom_fields as $custom_field_key => $custom_field_value) {
                $default_ind_leadboard_header_value[$custom_field_key] = "<th class='ays_lb_custom_fields'>" .$custom_field_value. "</th>";
            }
        }

        $id = (isset($attr['id'])) ? absint(intval($attr['id'])) : null;

        $date_from = (isset($attr['from'])) ? $attr['from'] : '';
        $date_to   = (isset($attr['to'])) ? $attr['to'] : '';

        $lb_date_attr = '';
        if( Quiz_Maker_Admin::validateDate($date_from, 'Y-m-d H:i:s') &&
                Quiz_Maker_Admin::validateDate($date_to, 'Y-m-d H:i:s') ){
            $lb_date_attr = " AND start_date BETWEEN '{$date_from}' AND '{$date_to}'";
        }

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_quizes WHERE id =".$id;
        $x = intval($wpdb->get_var($sql));
        $duration_avg = $ind_leadboard_sort == 'avg' ? strtoupper($ind_leadboard_sort) : '';
        if ($x === 0) {
            return '[ays_quiz_leaderboard id="'.$id.'"]';
        }else{
            if($ind_leadboard_orderby == 'id'){
                if($ind_leadboard_sort == 'avg'){
                    $sql = "SELECT
                                quiz_id,
                                user_id,
                                ".$duration_avg."(CAST(duration AS DECIMAL(10))) AS dur_avg,
                                ".strtoupper($ind_leadboard_sort)."(CAST(score AS DECIMAL(10))) AS avg_score,
                                ".strtoupper($ind_leadboard_sort)."(CAST(points AS DECIMAL(10))) AS avg_points,
                                MAX(CAST(max_points AS DECIMAL(10))) AS max_points,
                                (SELECT options
                                    FROM {$wpdb->prefix}aysquiz_reports
                                    WHERE quiz_id = {$id} AND user_id != 0
                                    {$lb_date_attr}
                                    ORDER BY {$wpdb->prefix}aysquiz_reports.id DESC
                                    LIMIT 1
                                ) AS options
                            FROM {$wpdb->prefix}aysquiz_reports
                            WHERE quiz_id = {$id} AND user_id != 0
                            {$lb_date_attr}
                            GROUP BY user_id
                            ORDER BY avg_score DESC, dur_avg
                            LIMIT ".$ind_leadboard_count;
                }else{
                    $sql = "SELECT DISTINCT a.user_id, a.score AS avg_score, a.points AS avg_points, MAX(a.max_points) AS max_points, MIN(a.duration) AS dur_avg, a.user_name, a.options
                            FROM (
                                    SELECT user_id as ue, ".strtoupper($ind_leadboard_sort)."(CAST(`score` AS DECIMAL(10,0))) AS new_score
                                    FROM {$wpdb->prefix}aysquiz_reports
                                    WHERE quiz_id = {$id} AND user_id != 0
                                    {$lb_date_attr}
                                    GROUP BY ue
                                 ) AS e
                            JOIN (
                                    SELECT
                                        user_id,
                                        user_name,
                                        CAST(`score` AS DECIMAL(10,0)) AS score,
                                        CAST(`duration` AS DECIMAL(10,0)) AS duration,
                                        CAST(`points` AS DECIMAL(10)) AS points,
                                        CAST(`max_points` AS DECIMAL(10)) AS max_points,
                                        options
                                    FROM {$wpdb->prefix}aysquiz_reports
                                    WHERE quiz_id = {$id} AND user_id != 0
                                    {$lb_date_attr}
                                 ) AS a
                            ON e.ue = a.user_id AND e.new_score = a.score
                            GROUP BY a.user_id
                            ORDER BY e.new_score DESC, dur_avg
                            LIMIT ".$ind_leadboard_count;
                }
            }elseif($ind_leadboard_orderby == 'email'){
                if($ind_leadboard_sort == 'avg'){
                    $sql = "SELECT
                                user_id,
                                user_name,
                                user_email,
                                ".$duration_avg."(CAST(duration AS DECIMAL(10))) AS dur_avg,
                                ".strtoupper($ind_leadboard_sort)."(CAST(score AS DECIMAL(10))) AS avg_score,
                                ".strtoupper($ind_leadboard_sort)."(CAST(points AS DECIMAL(10))) AS avg_points,
                                MAX(CAST(max_points AS DECIMAL(10))) AS max_points,
                                options
                            FROM {$wpdb->prefix}aysquiz_reports
                            WHERE quiz_id = {$id} AND !(user_email='' OR user_email IS NULL)
                            {$lb_date_attr}
                            GROUP BY user_email
                            ORDER BY avg_score DESC, dur_avg
                            LIMIT ".$ind_leadboard_count;
                }else{
                    $sql = "SELECT DISTINCT a.user_email, a.score AS avg_score, a.points AS avg_points, MAX(a.max_points) AS max_points, MIN(a.duration) AS dur_avg, a.user_id, a.user_name, a.options
                            FROM (
                                    SELECT user_email as ue, ".strtoupper($ind_leadboard_sort)."(CAST(`score` AS DECIMAL(10,0))) AS new_score
                                    FROM {$wpdb->prefix}aysquiz_reports
                                    WHERE quiz_id = {$id} AND !(user_email='' OR user_email IS NULL)
                                    GROUP BY ue
                                    {$lb_date_attr}
                                 ) AS e
                            JOIN (
                                    SELECT
                                        user_email,
                                        user_id,
                                        user_name,
                                        CAST(`score` AS DECIMAL(10,0)) AS score,
                                        CAST(`duration` AS DECIMAL(10,0)) AS duration,
                                        CAST(`points` AS DECIMAL(10)) AS points,
                                        CAST(`max_points` AS DECIMAL(10)) AS max_points,
                                        options
                                    FROM {$wpdb->prefix}aysquiz_reports
                                    WHERE quiz_id = {$id}
                                    {$lb_date_attr}
                                 ) AS a
                            ON e.ue = a.user_email AND e.new_score = a.score
                            GROUP BY a.user_email
                            ORDER BY e.new_score DESC, dur_avg
                            LIMIT ".$ind_leadboard_count;
                }
            }elseif($ind_leadboard_orderby == 'no_grouping'){
                // if($ind_leadboard_sort == 'no_grouping'){

                    $sql = "SELECT
                                user_id,
                                user_name,
                                CAST(duration AS DECIMAL(10)) AS dur_avg,
                                CAST(score AS DECIMAL(10)) AS avg_score,
                                CAST(points AS DECIMAL(10)) AS avg_points,
                                CAST(max_points AS DECIMAL(10)) AS max_points,
                                options
                            FROM {$wpdb->prefix}aysquiz_reports
                            WHERE quiz_id = {$id}
                            {$lb_date_attr}
                            ORDER BY avg_score DESC, dur_avg
                            LIMIT ".$ind_leadboard_count;
                // }
            }

            $result = $wpdb->get_results($sql, 'ARRAY_A');
            if (!empty($result)) {
                $c = 1;
                $content = '';

                $content .= '
                <style>
                    '. $ind_leadboard_suctom_css .'
                </style>';

                $content .= "<div class='ays_lb_container ays-leaderboard-main-container'>
                <table class='ays_lb_ul ". $enable_pagination_class ."' style='width: ".$ind_leadboard_width.";'>
                    <thead>
                        <tr class='ays_lb_li' style='background: ".$ind_leadboard_color.";'>";

                foreach ($ind_leadboard_columns_order as $key => $value) {
                     if (isset($ind_leadboard_columns[$value])) {
                        if ($value == '') {
                            continue;
                        }
                        if ( ! isset( $default_ind_leadboard_header_value[$value] ) ) {
                            continue;
                        }

                        $content .= $default_ind_leadboard_header_value[$value];
                    }
                }

                $content .=
                        "</tr>
                    </thead>
                    <tbody>";

                foreach ($result as $val) {
                    $score = round($val['avg_score'], 2);
                    $user_id = intval($val['user_id']);
                    $duration = (isset($val['dur_avg']) && $val['dur_avg'] != '') ? round(floatval($val['dur_avg']), 2) : '0';
                    $user_avatar = "";

                    switch ( $ind_leadboard_points_display ) {
                        case 'with_max_point':
                            $avg_points = (isset($val['avg_points']) && $val['avg_points'] != '') ? round(floatval($val['avg_points']), 2) : 0;
                            $max_points = (isset($val['max_points']) && $val['max_points'] != '') ? round(floatval($val['max_points']), 2) : 0;
                            $points = $avg_points . " / " . $max_points;
                            break;
                        case 'without_max_point':
                        default:
                            $points = (isset($val['avg_points']) && $val['avg_points'] != '') ? round(floatval($val['avg_points']), 2) : 0;
                            break;
                    }

                    if ($user_id == 0) {
                        $user_name = isset($val['user_name']) && $val['user_name']!= '' ? $val['user_name'] : __('Guest', $this->plugin_name);
                    }else{
                        $user_name = (isset($val['user_name']) && $val['user_name'] != '') ? $val['user_name'] : '';
                        if($user_name == ''){
                            $user = get_userdata( $user_id );
                            if($user !== false){
                                $user_name = $user->data->display_name ? $user->data->display_name : $user->user_login;
                            }else{
                                continue;
                            }
                        }

                        if ( $leadboard_enable_user_avatar && !is_null( $user_id ) && $user_id > 0 ) {
                            $user_avatar_arg = array(
                                'size' => 20
                            );

                            $user_avatar_url = get_avatar_url( $user_id, $user_avatar_arg );
                            if ( !is_null( $user_avatar_url ) && !empty( $user_avatar_url ) ) {
                                $user_avatar = '<img src="'. $user_avatar_url .'" class="ays-lb-user-avatar">';
                            }
                        }
                    }

                    $duration_for_ordering = $duration;
                    $duration = Quiz_Maker_Data::secondsToWords($duration);
                    if ($duration == '') {
                        $duration = '0 ' . __( 'second' , $this->plugin_name );
                    }

                    $ays_default_html_order = array(
                        "pos"        => "<td class='ays_lb_pos'>$c</td>",
                        "name"       => "<td class='ays_lb_user'><span class='ays-lb-user-avatar-row'>$user_avatar</span><span>$user_name</span></td>",
                        "score"      => "<td class='ays_lb_score'>$score %</td>",
                        "duration"   => "<td class='ays_lb_duration' data-order='". $duration_for_ordering ."'>$duration</td>",
                        "points"     => "<td class='ays_lb_points'>$points</td>",
                    );

                    $attribute_options = (isset($val['options']) && $val['options'] != '') ? json_decode( $val['options'], true ) : '';

                    if($ind_leadboard_orderby == 'email'){
                        if($ind_leadboard_sort == 'avg'){

                            $custom_fields_user_email = (isset($val['user_email']) && $val['user_email'] != '') ? sanitize_email( $val['user_email'] ) : '';

                            if ( $custom_fields_user_email != "" ) {
                                $sql2 = "SELECT options
                                        FROM {$wpdb->prefix}aysquiz_reports
                                        WHERE quiz_id = {$id} AND user_email = '{$custom_fields_user_email}'
                                        {$lb_date_attr}
                                        ORDER BY {$wpdb->prefix}aysquiz_reports.id DESC
                                        LIMIT 1";
                                $result2 = $wpdb->get_row($sql2, "ARRAY_A");

                                $attribute_options = (isset($result2['options']) && $result2['options'] != '') ? json_decode( $result2['options'], true ) : '';
                            }
                        }
                    } 
                    elseif ( $ind_leadboard_orderby == 'id' ) {
                        if($ind_leadboard_sort == 'avg'){

                            if ( isset( $user_id ) && $user_id > 0 ) {
                                $sql2 = "SELECT options
                                        FROM {$wpdb->prefix}aysquiz_reports
                                        WHERE user_id = {$user_id} AND quiz_id = {$id}
                                        {$lb_date_attr}
                                        ORDER BY {$wpdb->prefix}aysquiz_reports.id DESC
                                        LIMIT 1";
                                $result2 = $wpdb->get_row($sql2, "ARRAY_A");

                                $attribute_options = (isset($result2['options']) && $result2['options'] != '') ? json_decode( $result2['options'], true ) : '';
                            }
                        }
                    }


                    $attribute_info = array();
                    if($attribute_options != ''){
                        $attribute_info = (isset($attribute_options['attributes_information']) && !empty( $attribute_options['attributes_information'] )) ? $attribute_options['attributes_information'] : array();
                    }

                    if( !empty($individual_leaderboard_custom_fields) ){
                        foreach ($individual_leaderboard_custom_fields as $custom_field_key => $custom_field_value) {
                            if(isset( $attribute_info[$custom_field_value] ) && $attribute_info[$custom_field_value] != ''){
                                $ays_default_html_order[$custom_field_key] = "<td class='ays_lb_custom_fields'>" .$attribute_info[$custom_field_value]. "</td>";
                            }else{
                                $ays_default_html_order[$custom_field_key] = "<td class='ays_lb_custom_fields'></td>";
                            }
                        }
                    }

                    $content .= "<tr class='ays_lb_li'>";
                    foreach ($ind_leadboard_columns_order as $key => $value) {
                        if (isset($ind_leadboard_columns[$value])) {
                            if ($value == '') {
                                continue;
                            }
                            if ( ! isset( $ays_default_html_order[$value] ) ) {
                                continue;
                            }

                            $content .= $ays_default_html_order[$value];
                        }
                    }

                    $content .= "</tr>";
                    $c++;
                }
                $content .= "</tbody>
                    </table>
                </div>";
                $content = Quiz_Maker_Data::ays_quiz_translate_content( $content );
                // echo $content;

                return str_replace(array("\r\n", "\n", "\r"), '', $content);
            }else{
                $content = "
                <div class='ays_lb_container ays-leaderboard-main-container'>
                    <table class='ays_lb_ul' style='width: ".$ind_leadboard_width."px;'>
                        <table class='ays_lb_ul' style='width: ".$ind_leadboard_width."px;'>
                            <tr class='ays_lb_li' style='background: ".$ind_leadboard_color.";'>";

                foreach ($ind_leadboard_columns_order as $key => $value) {
                    if (isset($ind_leadboard_columns[$value])) {
                        if ($value == '') {
                            continue;
                        }
                        $content .= $default_ind_leadboard_header_value[$value];
                    }
                }
                $content .= "</tr>";

                $content .= "<tr>";
                    $content .= "<td class='ays_not_data'>" . __("There is no data yet", $this->plugin_name) . "</td>";
                $content .= "</tr>
                    </table>
                </div>";
                $content = Quiz_Maker_Data::ays_quiz_translate_content( $content );
                // echo $content;

                return str_replace(array("\r\n", "\n", "\r"), '', $content);
            }
        }
        // echo $content;
        $content = Quiz_Maker_Data::ays_quiz_translate_content( $content );

        return str_replace(array("\r\n", "\n", "\r"), '', $content);
    }

    public function ays_generate_gleaderboard_list($attr){
        // ob_start();
        global $wpdb;

        $this->enqueue_styles();
        $this->enqueue_scripts();

        $current_user_id = get_current_user_id();
        $current_user = get_userdata( $current_user_id );
        $current_user_email = '';
        if( $current_user ){
            $current_user_email = $current_user->data->user_email ? $current_user->data->user_email : '';
        }

        $quiz_settings = $this->settings;
        $leadboard_res = ($quiz_settings->ays_get_setting('leaderboard') === false) ? json_encode(array()) : $quiz_settings->ays_get_setting('leaderboard');
        $quiz_settings_options = ($quiz_settings->ays_get_setting('options') === false) ? json_encode(array()) : $quiz_settings->ays_get_setting('options');

        $leadboard = json_decode($leadboard_res, true);

        $glob_leadboard_count = isset($leadboard['global']['count']) ? $leadboard['global']['count'] : '5' ;
        $glob_leadboard_width = isset($leadboard['global']['width']) ? $leadboard['global']['width'] : '0' ;
        $glob_leadboard_width = intval($glob_leadboard_width) == 0 ? '100%' : $glob_leadboard_width ."px";
        $glob_leadboard_orderby = isset($leadboard['global']['orderby']) ? $leadboard['global']['orderby'] : 'id' ;
        $glob_leadboard_sort = isset($leadboard['global']['sort']) ? $leadboard['global']['sort'] : 'avg' ;
        $glob_leadboard_color = isset($leadboard['global']['color']) ? $leadboard['global']['color'] : '#99BB5A' ;
        $glob_leadboard_suctom_css = (isset($leadboard['global']['gleadboard_custom_css']) && $leadboard['global']['gleadboard_custom_css'] != '') ? $leadboard['global']['gleadboard_custom_css'] : '';
        $duration_avg = $glob_leadboard_sort == 'avg' ? strtoupper($glob_leadboard_sort) : '';
        $duration_sum = $glob_leadboard_sort == 'sum' ? strtoupper($glob_leadboard_sort) : '';

        $default_glob_leadboard_columns = array(
            'pos'         => 'pos',
            'name'        => 'name',
            'duration'    => 'duration',
            'score'       => 'score',
            'points'      => '',
        );

        $leadboard['global']['glob_leadboard_columns'] = ! isset( $leadboard['global']['glob_leadboard_columns'] ) ? $default_glob_leadboard_columns : $leadboard['global']['glob_leadboard_columns'];
        $glob_leadboard_columns = (isset( $leadboard['global']['glob_leadboard_columns'] ) && !empty($leadboard['global']['glob_leadboard_columns']) ) ? $leadboard['global']['glob_leadboard_columns'] : array();
        $glob_leadboard_columns_order = (isset( $leadboard['global']['glob_leadboard_columns_order'] ) && !empty($leadboard['global']['glob_leadboard_columns_order']) ) ? $leadboard['global']['glob_leadboard_columns_order'] : $default_glob_leadboard_columns;

        // Enable pagination
        $glob_leadboard['global']['leadboard_enable_pagination'] = isset($leadboard['global']['leadboard_enable_pagination']) ? sanitize_text_field( $leadboard['global']['leadboard_enable_pagination'] ) : 'on';
        $glob_leadboard_enable_pagination = (isset($leadboard['global']['leadboard_enable_pagination']) && sanitize_text_field( $leadboard['global']['leadboard_enable_pagination'] ) == "on") ? true : false;

        // Enable User Avatar
        $leadboard['global']['leadboard_enable_user_avatar'] = isset($leadboard['global']['leadboard_enable_user_avatar']) ? sanitize_text_field( $leadboard['global']['leadboard_enable_user_avatar'] ) : 'off';
        $glob_leadboard_enable_user_avatar = (isset($leadboard['global']['leadboard_enable_user_avatar']) && sanitize_text_field( $leadboard['global']['leadboard_enable_user_avatar'] ) == "on") ? true : false;

        $enable_pagination_class = '';
        if ( $glob_leadboard_enable_pagination ) {
            $enable_pagination_class = 'ays-quiz-global-leaderboard-pagination';
        }

        $default_glob_leadboard_header_value = array(
            "pos"        => "<th class='ays_lb_pos ays_glb_pos'>" . __( "Pos.", $this->plugin_name ) . "</th>",
            "name"       => "<th class='ays_lb_user ays_glb_user'>" . __( "Name", $this->plugin_name ) . "</th>",
            "score"      => "<th class='ays_lb_score ays_glb_score'>" . __( "Score", $this->plugin_name ) . "</th>",
            "duration"   => "<th class='ays_lb_duration ays_glb_duration'>" . __( "Duration", $this->plugin_name ) . "</th>",
            "points"     => "<th class='ays_lb_points ays_glb_points'>" . __( "Points", $this->plugin_name ) . "</th>",
        );

        $date_from = (isset($attr['from'])) ? $attr['from'] : '';
        $date_to   = (isset($attr['to'])) ? $attr['to'] : '';

        $lb_date_attr = '';
        $lb_where_date_attr = '';
        if( Quiz_Maker_Admin::validateDate($date_from, 'Y-m-d H:i:s') &&
                Quiz_Maker_Admin::validateDate($date_to, 'Y-m-d H:i:s') ){
            $lb_date_attr = " AND start_date BETWEEN '{$date_from}' AND '{$date_to}'";
            $lb_where_date_attr = " WHERE start_date BETWEEN '{$date_from}' AND '{$date_to}'";
        }

        if($glob_leadboard_orderby == 'id'){
            if($glob_leadboard_sort == 'avg'){
                $sql = "SELECT
                            quiz_id,
                            user_id,
                            ".$duration_avg."(CAST(duration AS DECIMAL(10))) AS dur_avg,
                            ".strtoupper($glob_leadboard_sort)."(CAST(`score` AS DECIMAL(10))) AS avg_score,
                            ".strtoupper($glob_leadboard_sort)."(CAST(points AS DECIMAL(10))) AS avg_points
                        FROM {$wpdb->prefix}aysquiz_reports
                        WHERE user_id != 0
                        {$lb_date_attr}
                        GROUP BY user_id
                        ORDER BY avg_score DESC, dur_avg
                        LIMIT ".$glob_leadboard_count;
            }elseif($glob_leadboard_sort == 'sum' ){
                 $sql = "SELECT
                            quiz_id,
                            user_id,
                            ".$duration_sum."(CAST(duration AS DECIMAL(10))) AS dur_sum,
                            AVG(CAST(`score` AS DECIMAL(10))) AS sum_score,
                            ".strtoupper($glob_leadboard_sort)."(points) AS sum_points
                        FROM {$wpdb->prefix}aysquiz_reports
                        WHERE user_id != 0
                        {$lb_date_attr}
                        GROUP BY user_id
                        ORDER BY sum_points DESC, dur_sum
                        LIMIT ".$glob_leadboard_count;

            }else{
                $sql = "SELECT DISTINCT a.user_id, a.score AS avg_score, a.points AS avg_points, MIN(a.duration) AS dur_avg, a.user_name, a.options
                        FROM (
                                SELECT user_id as ue, ".strtoupper($glob_leadboard_sort)."(CAST(`score` AS DECIMAL(10,0))) AS new_score
                                FROM {$wpdb->prefix}aysquiz_reports
                                WHERE user_id != 0
                                {$lb_date_attr}
                                GROUP BY ue
                             ) AS e
                        JOIN (
                                SELECT
                                    user_id,
                                    user_name,
                                    CAST(`score` AS DECIMAL(10,0)) AS score,
                                    CAST(`duration` AS DECIMAL(10,0)) AS duration,
                                    CAST(`points` AS DECIMAL(10)) AS points,
                                    options
                                FROM {$wpdb->prefix}aysquiz_reports
                                {$lb_where_date_attr}
                             ) AS a
                        ON e.ue = a.user_id AND e.new_score = a.score
                        GROUP BY a.user_id
                        ORDER BY e.new_score DESC, dur_avg
                        LIMIT ".$glob_leadboard_count;
            }
        }elseif($glob_leadboard_orderby == 'email'){
            if($glob_leadboard_sort == 'avg'){
                $sql = "SELECT
                            user_id,
                            user_name,
                            user_email,
                            ".$duration_avg."(CAST(duration AS DECIMAL(10))) AS dur_avg,
                            ".strtoupper($glob_leadboard_sort)."(CAST(`score` AS DECIMAL(10))) AS avg_score,
                            ".strtoupper($glob_leadboard_sort)."(CAST(points AS DECIMAL(10))) AS avg_points,
                            options
                        FROM {$wpdb->prefix}aysquiz_reports
                        WHERE !(user_email='' OR user_email IS NULL)
                        {$lb_date_attr}
                        GROUP BY user_email
                        ORDER BY avg_score DESC, dur_avg
                        LIMIT ".$glob_leadboard_count;
            }elseif($glob_leadboard_sort == 'sum'){
                $sql = "SELECT
                            user_id,
                            user_name,
                            user_email,
                            ".$duration_sum."(CAST(duration AS DECIMAL(10))) AS dur_sum,
                            AVG(CAST(`score` AS DECIMAL(10))) AS sum_score,
                            ".strtoupper($glob_leadboard_sort)."(points) AS sum_points,
                            options
                        FROM {$wpdb->prefix}aysquiz_reports
                        WHERE !(user_email='' OR user_email IS NULL)
                        {$lb_date_attr}
                        GROUP BY user_email
                        ORDER BY sum_points DESC, dur_sum
                        LIMIT ".$glob_leadboard_count;
            }else{
                $sql = "SELECT DISTINCT a.user_email, a.score AS avg_score, a.points AS avg_points, MIN(a.duration) AS dur_avg, a.user_id, a.user_name, a.options
                        FROM (
                                SELECT user_email as ue, ".strtoupper($glob_leadboard_sort)."(CAST(`score` AS DECIMAL(10,0))) AS new_score
                                FROM {$wpdb->prefix}aysquiz_reports
                                WHERE !(user_email='' OR user_email IS NULL)
                                GROUP BY ue
                             ) AS e
                        JOIN (
                                SELECT
                                    user_email,
                                    user_id,
                                    user_name,
                                    CAST(`score` AS DECIMAL(10,0)) AS score,
                                    CAST(`duration` AS DECIMAL(10,0)) AS duration,
                                    CAST(`points` AS DECIMAL(10)) AS points,
                                    options
                                FROM {$wpdb->prefix}aysquiz_reports
                                {$lb_where_date_attr}
                             ) AS a
                        ON e.ue = a.user_email AND e.new_score = a.score
                        GROUP BY a.user_email
                        ORDER BY e.new_score DESC, dur_avg
                        LIMIT ".$glob_leadboard_count;
            }
        }

        $result = $wpdb->get_results($sql, 'ARRAY_A');

        if ( empty( $result ) ) {
            $enable_pagination_class = '';
        }

        $c = 1;
        $content = '';

        $content .= '
        <style>
            .ays_glb_container table.ays_glb_ul tr.ays_glb_li th {
                color: '. $this->color_inverse( $glob_leadboard_color ) .';
            }

            '. $glob_leadboard_suctom_css .'
        </style>';

        $content .= "<div class='ays_lb_container ays_glb_container ays-leaderboard-main-container'>
        <table class='ays_lb_ul ays_glb_ul ". $enable_pagination_class ."' style='width: ".$glob_leadboard_width.";'>
            <thead>
                <tr class='ays_lb_li ays_glb_li' style='background: ".$glob_leadboard_color.";'>";

        foreach ($glob_leadboard_columns_order as $key => $value) {
             if (isset($glob_leadboard_columns[$value])) {
                if ($value == '') {
                    continue;
                }
                $content .= $default_glob_leadboard_header_value[$value];
            }
        }

        $content .=
                "</tr>
            </thead>
            <tbody>";

        $dur = 'dur_avg';
        $point = 'avg_points';
        $scr = 'avg_score';
        if($glob_leadboard_sort == 'sum'){
            $dur = 'dur_sum';
            $point = 'sum_points';
            $scr = 'sum_score';
        }

        if (!empty($result)) {
            foreach ($result as $val) {
                $score = round($val[$scr], 2);
                $user_id = intval($val['user_id']);
                $duration = (isset($val[$dur]) && $val[$dur] != '') ? round(floatval($val[$dur]), 2) : '0';
                $points = (isset($val[$point]) && $val[$point] != '') ? round(floatval($val[$point]), 2) : '0';
                $user_avatar = "";

                $user_email = '';
                if( isset( $val['user_email'] ) && $val['user_email'] != '' ){
                    $user_email = $val['user_email'];
                }

                if ($user_id == 0) {
                    $user_name = (isset($val['user_name']) && $val['user_name'] != '') ? $val['user_name'] : __('Guest', $this->plugin_name);
                }else{
                    $user_name = (isset($val['user_name']) && $val['user_name'] != '') ? $val['user_name'] : '';
                    if($user_name == ''){
                        $user = get_userdata( $user_id );
                        if($user !== false){
                            $user_name = $user->data->display_name ? $user->data->display_name : $user->user_login;
                        }else{
                            continue;
                        }
                    }

                    if ( $glob_leadboard_enable_user_avatar && !is_null( $user_id ) && $user_id > 0 ) {
                        $user_avatar_arg = array(
                            'size' => 20
                        );

                        $user_avatar_url = get_avatar_url( $user_id, $user_avatar_arg );
                        if ( !is_null( $user_avatar_url ) && !empty( $user_avatar_url ) ) {
                            $user_avatar = '<img src="'. $user_avatar_url .'" class="ays-lb-user-avatar">';
                        }
                    }
                }

                $duration_for_ordering = $duration;
                $duration = Quiz_Maker_Data::secondsToWords($duration);
                if ($duration == '') {
                    $duration = '0 ' . __( 'second' , $this->plugin_name );
                }

                if( $glob_leadboard_orderby == 'id' ){
                    if($current_user_id == $user_id){
                        $user_position_color = 'background:'.$glob_leadboard_color.'; opacity: 0.5; color:'. $this->color_inverse( $glob_leadboard_color ) .';';
                    }else{
                        $user_position_color = '';
                    }
                }elseif($glob_leadboard_orderby == 'email'){
                    if($current_user_email == $user_email){
                        $user_position_color = 'background:'.$glob_leadboard_color.'; opacity: 0.5; color:'. $this->color_inverse( $glob_leadboard_color ) .';';
                    }else{
                        $user_position_color = '';
                    }
                }

                $ays_default_html_order = array(
                    "pos"        => "<td class='ays_lb_pos ays_glb_pos'>$c</td>",
                    "name"       => "<td class='ays_lb_user ays_glb_user'><span class='ays-lb-user-avatar-row'>$user_avatar</span><span>$user_name</span></td>",
                    "score"      => "<td class='ays_lb_score ays_glb_score'>$score %</td>",
                    "duration"   => "<td class='ays_lb_duration ays_glb_duration' data-order='". $duration_for_ordering ."'>$duration</td>",
                    "points"     => "<td class='ays_lb_points ays_glb_points'>$points</td>",
                );

                $content .= "<tr class='ays_lb_li' style='".$user_position_color."'>";
                foreach ($glob_leadboard_columns_order as $key => $value) {
                    if (isset($glob_leadboard_columns[$value])) {
                        if ($value == '') {
                            continue;
                        }
                        $content .= $ays_default_html_order[$value];
                    }
                }

                $content .= "</tr>";
                $c++;
            }
        }else{
            $content .= "<tr>";
                $content .= "<td class='ays_not_data'>" . __("There is no data yet", $this->plugin_name) . "</td>";
            $content .= "</tr>";
        }

        $content .= "</tbody>
            </table>
        </div>";

        // echo $content;
        $content = Quiz_Maker_Data::ays_quiz_translate_content( $content );

        return str_replace(array("\r\n", "\n", "\r"), '', $content);
    }

    public function ays_generate_global_quiz_cat_leaderboard_list($attr){
        // ob_start();
        global $wpdb;

        $this->enqueue_styles();
        $this->enqueue_scripts();

        $quiz_settings = $this->settings;
        $leadboard_res = ($quiz_settings->ays_get_setting('leaderboard') === false) ? json_encode(array()) : $quiz_settings->ays_get_setting('leaderboard');
        $quiz_settings_options = ($quiz_settings->ays_get_setting('options') === false) ? json_encode(array()) : $quiz_settings->ays_get_setting('options');

        $custom_fields = Quiz_Maker_Data::get_custom_fields_for_shortcodes();

        // Leaderboard By Quiz Category
        $leaderboard_by_quiz_cat = isset($custom_fields['leaderboard_by_quiz_cat']) && !empty($custom_fields['leaderboard_by_quiz_cat']) ? $custom_fields['leaderboard_by_quiz_cat'] : array();

        $leadboard = json_decode($leadboard_res, true);

        $glob_quiz_cat_leadboard_count = isset($leadboard['global_quiz_cat']['count']) ? $leadboard['global_quiz_cat']['count'] : '5' ;
        $glob_quiz_cat_leadboard_width = isset($leadboard['global_quiz_cat']['width']) ? $leadboard['global_quiz_cat']['width'] : '0' ;
        $glob_quiz_cat_leadboard_width = intval($glob_quiz_cat_leadboard_width) == 0 ? '100%' : $glob_quiz_cat_leadboard_width ."px";
        $glob_quiz_cat_leadboard_orderby = isset($leadboard['global_quiz_cat']['orderby']) ? $leadboard['global_quiz_cat']['orderby'] : 'id' ;
        $glob_quiz_cat_leadboard_sort = isset($leadboard['global_quiz_cat']['sort']) ? $leadboard['global_quiz_cat']['sort'] : 'avg' ;
        $glob_quiz_cat_leadboard_color = isset($leadboard['global_quiz_cat']['color']) ? $leadboard['global_quiz_cat']['color'] : '#99BB5A' ;
        $glob_quiz_cat_leadboard_cuctom_css = (isset($leadboard['global_quiz_cat']['gleadboard_custom_css']) && $leadboard['global_quiz_cat']['gleadboard_custom_css'] != '') ? $leadboard['global_quiz_cat']['gleadboard_custom_css'] : '';
        $duration_avg = $glob_quiz_cat_leadboard_sort == 'avg' ? strtoupper($glob_quiz_cat_leadboard_sort) : '';
        $duration_sum = $glob_quiz_cat_leadboard_sort == 'sum' ? strtoupper($glob_quiz_cat_leadboard_sort) : '';

        $default_glob_leadboard_columns = array(
            'pos'         => 'pos',
            'name'        => 'name',
            'duration'    => 'duration',
            'score'       => 'score',
            'points'      => '',
        );

        if( !empty($leaderboard_by_quiz_cat) ){
            foreach ($leaderboard_by_quiz_cat as $custom_field_key => $custom_field) {
                $default_glob_leadboard_columns[$custom_field_key] = $custom_field_key;
            }
        }

        $leadboard['global_quiz_cat']['glob_quiz_cat_leadboard_columns'] = ! isset( $leadboard['global_quiz_cat']['glob_quiz_cat_leadboard_columns'] ) ? $default_glob_leadboard_columns : $leadboard['global_quiz_cat']['glob_quiz_cat_leadboard_columns'];
        $glob_quiz_cat_leadboard_columns = (isset( $leadboard['global_quiz_cat']['glob_quiz_cat_leadboard_columns'] ) && !empty($leadboard['global_quiz_cat']['glob_quiz_cat_leadboard_columns']) ) ? $leadboard['global_quiz_cat']['glob_quiz_cat_leadboard_columns'] : array();
        $glob_quiz_cat_leadboard_columns_order = (isset( $leadboard['global_quiz_cat']['glob_quiz_cat_leadboard_columns'] ) && !empty($leadboard['global_quiz_cat']['glob_quiz_cat_leadboard_columns']) ) ? $leadboard['global_quiz_cat']['glob_quiz_cat_leadboard_columns'] : $default_glob_leadboard_columns;

        // Enable pagination
        $leadboard['global_quiz_cat']['leadboard_enable_pagination'] = isset($leadboard['global_quiz_cat']['leadboard_enable_pagination']) ? sanitize_text_field( $leadboard['global_quiz_cat']['leadboard_enable_pagination'] ) : 'on';
        $glob_quiz_cat_leadboard_enable_pagination = (isset($leadboard['global_quiz_cat']['leadboard_enable_pagination']) && sanitize_text_field( $leadboard['global_quiz_cat']['leadboard_enable_pagination'] ) == "on") ? true : false;

        // Enable User Avatar
        $leadboard['global_quiz_cat']['leadboard_enable_user_avatar'] = isset($leadboard['global_quiz_cat']['leadboard_enable_user_avatar']) ? sanitize_text_field( $leadboard['global_quiz_cat']['leadboard_enable_user_avatar'] ) : 'off';
        $glob_quiz_cat_leadboard_enable_user_avatar = (isset($leadboard['global_quiz_cat']['leadboard_enable_user_avatar']) && sanitize_text_field( $leadboard['global_quiz_cat']['leadboard_enable_user_avatar'] ) == "on") ? true : false;

        $enable_pagination_class = '';
        if ( $glob_quiz_cat_leadboard_enable_pagination ) {
            $enable_pagination_class = 'ays-quiz-global-quiz-category-leaderboard-pagination';
        }

        $default_glob_quiz_cat_leadboard_header_value = array(
            "pos"        => "<th class='ays_lb_pos ays_glb_pos'>" . __( "Pos.", $this->plugin_name ) . "</th>",
            "name"       => "<th class='ays_lb_user ays_glb_user'>" . __( "Name", $this->plugin_name ) . "</th>",
            "score"      => "<th class='ays_lb_score ays_glb_score'>" . __( "Score", $this->plugin_name ) . "</th>",
            "duration"   => "<th class='ays_lb_duration ays_glb_duration'>" . __( "Duration", $this->plugin_name ) . "</th>",
            "points"     => "<th class='ays_lb_points ays_glb_points'>" . __( "Points", $this->plugin_name ) . "</th>",
        );

        if( !empty($leaderboard_by_quiz_cat) ){
            foreach ($leaderboard_by_quiz_cat as $custom_field_key => $custom_field_value) {
                $default_glob_quiz_cat_leadboard_header_value[$custom_field_key] = "<th class='ays_lb_custom_fields ays_glb_custom_fields'>" .$custom_field_value. "</th>";
            }
        }

        $id = (isset($attr['id'])) ? absint(intval($attr['id'])) : null;

        $date_from = (isset($attr['from'])) ? $attr['from'] : '';
        $date_to   = (isset($attr['to'])) ? $attr['to'] : '';

        $lb_date_attr = '';
        $lb_where_date_attr = '';
        if( Quiz_Maker_Admin::validateDate($date_from, 'Y-m-d H:i:s') &&
                Quiz_Maker_Admin::validateDate($date_to, 'Y-m-d H:i:s') ){
            $lb_date_attr       = " AND start_date BETWEEN '{$date_from}' AND '{$date_to}'";
            $lb_where_date_attr = " WHERE start_date BETWEEN '{$date_from}' AND '{$date_to}'";
        }

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_quizcategories WHERE id =".$id;
        $x = intval($wpdb->get_var($sql));



        $sql = "SELECT id FROM {$wpdb->prefix}aysquiz_quizes WHERE quiz_category_id =".$id;
        $quiz_id_cat =  $wpdb->get_col($sql);

        if((isset($quiz_id_cat) &&  !empty($quiz_id_cat)) ?  $quiz_ids = implode(',', $quiz_id_cat) :  $quiz_ids = 0);

        $duration_avg = $glob_quiz_cat_leadboard_sort == 'avg' ? strtoupper($glob_quiz_cat_leadboard_sort) : '';
        if ($x === 0) {
            return '[ays_quiz_cat_leaderboard id="'.$id.'"]';
        }else{
            if($glob_quiz_cat_leadboard_orderby == 'id'){
                if($glob_quiz_cat_leadboard_sort == 'avg'){
                    $sql = "SELECT
                                quiz_id,
                                user_id,
                                ".$duration_avg."(CAST(duration AS DECIMAL(10))) AS dur_avg,
                                ".strtoupper($glob_quiz_cat_leadboard_sort)."(CAST(`score` AS DECIMAL(10))) AS avg_score,
                                ".strtoupper($glob_quiz_cat_leadboard_sort)."(CAST(points AS DECIMAL(10))) AS avg_points,
                                (SELECT options
                                    FROM {$wpdb->prefix}aysquiz_reports
                                    WHERE quiz_id IN({$quiz_ids}) AND user_id != 0
                                    {$lb_date_attr}
                                    ORDER BY {$wpdb->prefix}aysquiz_reports.id DESC
                                    LIMIT 1
                                ) AS options
                            FROM {$wpdb->prefix}aysquiz_reports
                            WHERE user_id != 0 AND quiz_id IN({$quiz_ids})
                            {$lb_date_attr}
                            GROUP BY user_id
                            ORDER BY avg_score DESC, dur_avg
                            LIMIT ".$glob_quiz_cat_leadboard_count;
                }elseif($glob_quiz_cat_leadboard_sort == 'sum' ){
                     $sql = "SELECT
                                quiz_id,
                                user_id,
                                ".$duration_sum."(CAST(duration AS DECIMAL(10))) AS dur_sum,
                                AVG(CAST(`score` AS DECIMAL(10))) AS sum_score,
                                ".strtoupper($glob_quiz_cat_leadboard_sort)."(points) AS sum_points
                            FROM {$wpdb->prefix}aysquiz_reports
                            WHERE user_id != 0 AND quiz_id  IN({$quiz_ids})
                            {$lb_date_attr}
                            GROUP BY user_id
                            ORDER BY sum_points DESC, dur_sum
                            LIMIT ".$glob_quiz_cat_leadboard_count;

                }else{
                    $sql = "SELECT DISTINCT a.user_id, a.score AS avg_score, a.points AS avg_points, MIN(a.duration) AS dur_avg, a.user_name, a.options
                            FROM (
                                    SELECT user_id as ue, ".strtoupper($glob_quiz_cat_leadboard_sort)."(CAST(`score` AS DECIMAL(10,0))) AS new_score
                                    FROM {$wpdb->prefix}aysquiz_reports
                                    WHERE user_id != 0 AND quiz_id IN({$quiz_ids})
                                    {$lb_date_attr}
                                    GROUP BY ue
                                 ) AS e
                            JOIN (
                                    SELECT
                                        user_id,
                                        user_name,
                                        CAST(`score` AS DECIMAL(10,0)) AS score,
                                        CAST(`duration` AS DECIMAL(10,0)) AS duration,
                                        CAST(`points` AS DECIMAL(10)) AS points,
                                        options
                                    FROM {$wpdb->prefix}aysquiz_reports
                                    {$lb_where_date_attr}
                                 ) AS a
                            ON e.ue = a.user_id AND e.new_score = a.score
                            GROUP BY a.user_id
                            ORDER BY e.new_score DESC, dur_avg
                            LIMIT ".$glob_quiz_cat_leadboard_count;
                }
            }elseif($glob_quiz_cat_leadboard_orderby == 'email'){
                if($glob_quiz_cat_leadboard_sort == 'avg'){
                    $sql = "SELECT
                                user_id,
                                user_name,
                                user_email,
                                ".$duration_avg."(CAST(duration AS DECIMAL(10))) AS dur_avg,
                                ".strtoupper($glob_quiz_cat_leadboard_sort)."(CAST(`score` AS DECIMAL(10))) AS avg_score,
                                ".strtoupper($glob_quiz_cat_leadboard_sort)."(CAST(points AS DECIMAL(10))) AS avg_points,
                                options
                            FROM {$wpdb->prefix}aysquiz_reports
                            WHERE !(user_email='' OR user_email IS NULL) AND quiz_id IN({$quiz_ids})
                            {$lb_date_attr}
                            GROUP BY user_email
                            ORDER BY avg_score DESC, dur_avg
                            LIMIT ".$glob_quiz_cat_leadboard_count;
                }elseif($glob_quiz_cat_leadboard_sort == 'sum'){
                    $sql = "SELECT
                                user_id,
                                user_name,
                                user_email,
                                ".$duration_sum."(CAST(duration AS DECIMAL(10))) AS dur_sum,
                                AVG(CAST(`score` AS DECIMAL(10))) AS sum_score,
                                ".strtoupper($glob_quiz_cat_leadboard_sort)."(points) AS sum_points,
                                options
                            FROM {$wpdb->prefix}aysquiz_reports
                            WHERE !(user_email='' OR user_email IS NULL) AND quiz_id IN({$quiz_ids})
                            {$lb_date_attr}
                            GROUP BY user_email
                            ORDER BY sum_points DESC, dur_sum
                            LIMIT ".$glob_quiz_cat_leadboard_count;
                }else{
                    $sql = "SELECT DISTINCT a.user_email, a.score AS avg_score, a.points AS avg_points, MIN(a.duration) AS dur_avg, a.user_id, a.user_name, a.options
                            FROM (
                                    SELECT user_email as ue, ".strtoupper($glob_quiz_cat_leadboard_sort)."(CAST(`score` AS DECIMAL(10,0))) AS new_score
                                    FROM {$wpdb->prefix}aysquiz_reports
                                    WHERE !(user_email='' OR user_email IS NULL) AND quiz_id IN({$quiz_ids})
                                    {$lb_date_attr}
                                    GROUP BY ue
                                 ) AS e
                            JOIN (
                                    SELECT
                                        user_email,
                                        user_id,
                                        user_name,
                                        CAST(`score` AS DECIMAL(10,0)) AS score,
                                        CAST(`duration` AS DECIMAL(10,0)) AS duration,
                                        CAST(`points` AS DECIMAL(10)) AS points,
                                        options
                                    FROM {$wpdb->prefix}aysquiz_reports
                                    {$lb_where_date_attr}
                                 ) AS a
                            ON e.ue = a.user_email AND e.new_score = a.score
                            GROUP BY a.user_email
                            ORDER BY e.new_score DESC, dur_avg
                            LIMIT ".$glob_quiz_cat_leadboard_count;
                }
            }

            $result = $wpdb->get_results($sql, 'ARRAY_A');

            if ( empty( $result ) ) {
                $enable_pagination_class = '';
            }

            $c = 1;
            $content = '';

            $content .= '
            <style>
                '. $glob_quiz_cat_leadboard_cuctom_css .'
            </style>';

            $content .= "<div class='ays_lb_container ays_glb_container ays-leaderboard-main-container'>
            <table class='ays_lb_ul ays_glb_ul ". $enable_pagination_class ."' style='width: ".$glob_quiz_cat_leadboard_width.";'>
                <thead>
                    <tr class='ays_lb_li ays_glb_li' style='background: ".$glob_quiz_cat_leadboard_color.";'>";

            foreach ($glob_quiz_cat_leadboard_columns_order as $key => $value) {
                 if (isset($glob_quiz_cat_leadboard_columns[$value])) {
                    if ($value == '') {
                        continue;
                    }
                    if ( ! isset( $default_glob_quiz_cat_leadboard_header_value[$value] ) ) {
                        continue;
                    }

                    $content .= $default_glob_quiz_cat_leadboard_header_value[$value];
                }
            }

            $content .=
                    "</tr>
                </thead>
                <tbody>";

            $dur = 'dur_avg';
            $point = 'avg_points';
            $scr = 'avg_score';
            if($glob_quiz_cat_leadboard_sort == 'sum'){
                $dur = 'dur_sum';
                $point = 'sum_points';
                $scr = 'sum_score';
            }

            if (!empty($result)) {
                foreach ($result as $val) {
                    $score = round($val[$scr], 2);
                    $user_id = intval($val['user_id']);
                    $duration = (isset($val[$dur]) && $val[$dur] != '') ? round(floatval($val[$dur]), 2) : '0';
                    $points = (isset($val[$point]) && $val[$point] != '') ? round(floatval($val[$point]), 2) : '0';
                    $user_avatar = "";

                    if ($user_id == 0) {
                        $user_name = (isset($val['user_name']) && $val['user_name'] != '') ? $val['user_name'] : __('Guest', $this->plugin_name);
                    }else{
                        $user_name = (isset($val['user_name']) && $val['user_name'] != '') ? $val['user_name'] : '';
                        if($user_name == ''){
                            $user = get_userdata( $user_id );
                            if($user !== false){
                                $user_name = $user->data->display_name ? $user->data->display_name : $user->user_login;
                            }else{
                                continue;
                            }
                        }

                        if ( $glob_quiz_cat_leadboard_enable_user_avatar && !is_null( $user_id ) && $user_id > 0 ) {
                            $user_avatar_arg = array(
                                'size' => 20
                            );

                            $user_avatar_url = get_avatar_url( $user_id, $user_avatar_arg );
                            if ( !is_null( $user_avatar_url ) && !empty( $user_avatar_url ) ) {
                                $user_avatar = '<img src="'. $user_avatar_url .'" class="ays-lb-user-avatar">';
                            }
                        }
                    }

                    $duration_for_ordering = $duration;
                    $duration = Quiz_Maker_Data::secondsToWords($duration);
                    if ($duration == '') {
                        $duration = '0 ' . __( 'second' , $this->plugin_name );
                    }

                    $ays_default_html_order = array(
                        "pos"        => "<td class='ays_lb_pos ays_glb_pos'>$c</td>",
                        "name"       => "<td class='ays_lb_user ays_glb_user'><span class='ays-lb-user-avatar-row'>$user_avatar</span><span>$user_name</span></td>",
                        "score"      => "<td class='ays_lb_score ays_glb_score'>$score %</td>",
                        "duration"   => "<td class='ays_lb_duration ays_glb_duration' data-order='". $duration_for_ordering ."'>$duration</td>",
                        "points"     => "<td class='ays_lb_points ays_glb_points'>$points</td>",
                    );

                    $attribute_options = (isset($val['options']) && $val['options'] != '') ? json_decode( $val['options'], true ) : '';

                    if($glob_quiz_cat_leadboard_orderby == 'email'){
                        if($glob_quiz_cat_leadboard_sort == 'avg' || $glob_quiz_cat_leadboard_sort == 'sum'){

                            $custom_fields_user_email = (isset($val['user_email']) && $val['user_email'] != '') ? sanitize_email( $val['user_email'] ) : '';

                            if ( $custom_fields_user_email != "" ) {
                                $sql2 = "SELECT options
                                        FROM {$wpdb->prefix}aysquiz_reports
                                        WHERE quiz_id IN({$quiz_ids}) AND user_email = '{$custom_fields_user_email}'
                                        {$lb_date_attr}
                                        ORDER BY {$wpdb->prefix}aysquiz_reports.id DESC
                                        LIMIT 1";
                                $result2 = $wpdb->get_row($sql2, "ARRAY_A");

                                $attribute_options = (isset($result2['options']) && $result2['options'] != '') ? json_decode( $result2['options'], true ) : '';
                            }
                        }
                    } elseif ( $glob_quiz_cat_leadboard_orderby == 'id' ) {
                        if($glob_quiz_cat_leadboard_sort == 'avg' || $glob_quiz_cat_leadboard_sort == 'sum'){

                            if ( isset( $user_id ) && $user_id > 0 ) {
                                $sql2 = "SELECT options
                                        FROM {$wpdb->prefix}aysquiz_reports
                                        WHERE user_id = {$user_id} AND quiz_id IN({$quiz_ids})
                                        {$lb_date_attr}
                                        ORDER BY {$wpdb->prefix}aysquiz_reports.id DESC
                                        LIMIT 1";
                                $result2 = $wpdb->get_row($sql2, "ARRAY_A");

                                $attribute_options = (isset($result2['options']) && $result2['options'] != '') ? json_decode( $result2['options'], true ) : '';
                            }
                        }
                    }

                    $attribute_info = array();
                    if($attribute_options != ''){
                        $attribute_info = (isset($attribute_options['attributes_information']) && !empty( $attribute_options['attributes_information'] )) ? $attribute_options['attributes_information'] : array();
                    }

                    if( !empty($leaderboard_by_quiz_cat) ){
                        foreach ($leaderboard_by_quiz_cat as $custom_field_key => $custom_field_value) {
                            if(isset( $attribute_info[$custom_field_value] ) && $attribute_info[$custom_field_value] != ''){
                                $ays_default_html_order[$custom_field_key] = "<td class='ays_glb_custom_fields ays_lb_custom_fields'>" .$attribute_info[$custom_field_value]. "</td>";
                            }else{
                                $ays_default_html_order[$custom_field_key] = "<td class='ays_glb_custom_fields ays_lb_custom_fields'></td>";
                            }
                        }
                    }

                    $content .= "<tr class='ays_lb_li'>";
                    foreach ($glob_quiz_cat_leadboard_columns_order as $key => $value) {
                        if (isset($glob_quiz_cat_leadboard_columns_order[$value])) {
                            if ($value == '') {
                                continue;
                            }
                            if ( ! isset( $ays_default_html_order[$value] ) ) {
                                continue;
                            }
                            
                            $content .= $ays_default_html_order[$value];
                        }
                    }

                    $content .= "</tr>";
                    $c++;
                }
            }else{
                $content .= "<tr>";
                    $content .= "<td class='ays_not_data'>" . __("There is no data yet", $this->plugin_name) . "</td>";
                $content .= "</tr>";
            }

            $content .= "</tbody>
                </table>
            </div>";

            // echo $content;
            $content = Quiz_Maker_Data::ays_quiz_translate_content( $content );

            return str_replace(array("\r\n", "\n", "\r"), '', $content);
        }
    }

    public function color_inverse($color){
        $color = str_replace('#', '', $color);
        if (strlen($color) != 6){ return '000000'; }
        $rgb = '';
        for ($x=0; $x < 3; $x++){
            $c = 255 - hexdec(substr($color,(2*$x),2));
            $c = ($c < 0) ? 0 : dechex($c);
            $rgb .= (strlen($c) < 2) ? '0'.$c : $c;
        }
        return '#'.$rgb;
    }
}
