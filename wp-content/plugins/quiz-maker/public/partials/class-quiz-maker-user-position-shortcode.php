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
class Quiz_Maker_Leaderboard_Position_Shortcode{

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

        add_shortcode('ays_user_leaderboard_position', array($this, 'ays_generate_user_position_on_leaderboard'));

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


    }

    // Leaderboard position shortcode
    public function ays_generate_user_position_on_leaderboard($attr){
        global $wpdb;

        $this->enqueue_styles();
        $this->enqueue_scripts();

        $quiz_settings = $this->settings;
        $leadboard_res = ($quiz_settings->ays_get_setting('leaderboard') === false) ? json_encode(array()) : $quiz_settings->ays_get_setting('leaderboard');
        $quiz_settings_options = ($quiz_settings->ays_get_setting('options') === false) ? json_encode(array()) : $quiz_settings->ays_get_setting('options');

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

        $leadboard['individual']['ind_leadboard_columns'] = ! isset( $leadboard['individual']['ind_leadboard_columns'] ) ? $default_ind_leadboard_columns : $leadboard['individual']['ind_leadboard_columns'];
        $ind_leadboard_columns = (isset( $leadboard['individual']['ind_leadboard_columns'] ) && !empty($leadboard['individual']['ind_leadboard_columns']) ) ? $leadboard['individual']['ind_leadboard_columns'] : array();
        $ind_leadboard_columns_order = (isset( $leadboard['individual']['ind_leadboard_columns_order'] ) && !empty($leadboard['individual']['ind_leadboard_columns_order']) ) ? $leadboard['individual']['ind_leadboard_columns_order'] : $default_ind_leadboard_columns;

        // Enable pagination
        $leadboard['individual']['leadboard_enable_pagination'] = isset($leadboard['individual']['leadboard_enable_pagination']) ? sanitize_text_field( $leadboard['individual']['leadboard_enable_pagination'] ) : 'on';
        $leadboard_enable_pagination = (isset($leadboard['individual']['leadboard_enable_pagination']) && sanitize_text_field( $leadboard['individual']['leadboard_enable_pagination'] ) == "on") ? true : false;

        $id = (isset($attr['id'])) ? absint(intval($attr['id'])) : null;

        // $lb_date_attr = '';
        // if( Quiz_Maker_Admin::validateDate($date_from, 'Y-m-d H:i:s') &&
        //         Quiz_Maker_Admin::validateDate($date_to, 'Y-m-d H:i:s') ){
        //     $lb_date_attr = " AND start_date BETWEEN '{$date_from}' AND '{$date_to}'";
        // }

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
                                MAX(CAST(max_points AS DECIMAL(10))) AS max_points
                            FROM {$wpdb->prefix}aysquiz_reports
                            WHERE quiz_id = {$id} AND user_id != 0
                            GROUP BY user_id
                            ORDER BY avg_score DESC, dur_avg
                            LIMIT ".$ind_leadboard_count;
                }else{
                    $sql = "SELECT DISTINCT a.user_id, a.score AS avg_score, a.points AS avg_points, MAX(a.max_points) AS max_points, MIN(a.duration) AS dur_avg, a.user_name, a.options
                            FROM (
                                    SELECT user_id as ue, ".strtoupper($ind_leadboard_sort)."(CAST(`score` AS DECIMAL(10,0))) AS new_score
                                    FROM {$wpdb->prefix}aysquiz_reports
                                    WHERE quiz_id = {$id} AND user_id != 0
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
                                ".$duration_avg."(CAST(duration AS DECIMAL(10))) AS dur_avg,
                                ".strtoupper($ind_leadboard_sort)."(CAST(score AS DECIMAL(10))) AS avg_score,
                                ".strtoupper($ind_leadboard_sort)."(CAST(points AS DECIMAL(10))) AS avg_points,
                                MAX(CAST(max_points AS DECIMAL(10))) AS max_points
                            FROM {$wpdb->prefix}aysquiz_reports
                            WHERE quiz_id = {$id} AND !(user_email='' OR user_email IS NULL)
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
                                CAST(max_points AS DECIMAL(10)) AS max_points
                            FROM {$wpdb->prefix}aysquiz_reports
                            WHERE quiz_id = {$id}
                            ORDER BY avg_score DESC, dur_avg
                            LIMIT ".$ind_leadboard_count;
                // }
            }
            $result = $wpdb->get_results($sql, 'ARRAY_A');

            if (!empty($result)) {
                array_unshift($result,"");

                unset($result[0]);

                $current_user_id = get_current_user_id();

                $user_id_pos = array();
                foreach ($result as $pos => $user) {

                    $user_id = isset($user['user_id']) && $user['user_id'] != '' ? intval($user['user_id']) : null;

                    if($user_id == null){
                        return;
                    }

                    if ( !in_array($user_id, $user_id_pos) ) {
                        $user_id_pos[$user_id] = $pos;
                    }

                }

                if($current_user_id != 0){
                    if(array_key_exists( $current_user_id, $user_id_pos )){
                        $content = "<span class='ays-quiz-generate-user-position-on-leaderboard'>".$user_id_pos[$current_user_id]."</span>";
                    }else{
                        // $content = "<span>" . __("You haven't got a position on the leaderboard.", $this->plugin_name) . "</span>";
                        $content = "<span class='ays-quiz-generate-user-position-on-leaderboard'>-</span>";
                    }
                }else{
                    // $content = "<span>" . __("Please log in to see your position on the leaderboard.", $this->plugin_name) . "</span>";
                    $content = "<span class='ays-quiz-generate-user-position-on-leaderboard'>-</span>";
                }

                return str_replace(array("\r\n", "\n", "\r"), '', $content);

            }else{
                // $content = "<span>" . __("There is no data yet.", $this->plugin_name) . "</span>";
                $content = "<span class='ays-quiz-generate-user-position-on-leaderboard'>-</span>";

                return str_replace(array("\r\n", "\n", "\r"), '', $content);
            }
        }
    }
}
