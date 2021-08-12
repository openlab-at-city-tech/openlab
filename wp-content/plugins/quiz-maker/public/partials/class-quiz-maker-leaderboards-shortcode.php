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

    // Leaderboard shortcode
    public function ays_generate_leaderboard_list($attr){
        // AV Leaderboard
        // ob_start();
        global $wpdb;

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


        $default_ind_leadboard_columns = array(
            'pos'        => 'pos',
            'name'       => 'name',
            'duration'   => 'duration',
            'score'      => 'score',
            'point'      => '',
        );

        $leadboard['individual']['ind_leadboard_columns'] = ! isset( $leadboard['individual']['ind_leadboard_columns'] ) ? $default_ind_leadboard_columns : $leadboard['individual']['ind_leadboard_columns'];
        $ind_leadboard_columns = (isset( $leadboard['individual']['ind_leadboard_columns'] ) && !empty($leadboard['individual']['ind_leadboard_columns']) ) ? $leadboard['individual']['ind_leadboard_columns'] : array();
        $ind_leadboard_columns_order = (isset( $leadboard['individual']['ind_leadboard_columns_order'] ) && !empty($leadboard['individual']['ind_leadboard_columns_order']) ) ? $leadboard['individual']['ind_leadboard_columns_order'] : $default_ind_leadboard_columns;

        $default_ind_leadboard_header_value = array(
            "pos"        => "<div class='ays_lb_pos'>" . __( "Pos.", $this->plugin_name ) . "</div>",
            "name"       => "<div class='ays_lb_user'>" . __( "Name", $this->plugin_name ) . "</div>",
            "score"      => "<div class='ays_lb_score'>" . __( "Score", $this->plugin_name ) . "</div>",
            "duration"   => "<div class='ays_lb_duration'>" . __( "Duration", $this->plugin_name ) . "</div>",
            "points"     => "<div class='ays_lb_points'>" . __( "Points", $this->plugin_name ) . "</div>",
        );

        $id = (isset($attr['id'])) ? absint(intval($attr['id'])) : null;
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
            }

            $result = $wpdb->get_results($sql, 'ARRAY_A');
            if (!empty($result)) {
                $c = 1;
                $content = '';

                $content .= '
                <style>
                    '. $ind_leadboard_suctom_css .'
                </style>';

                $content .= "<div class='ays_lb_container'>
                <ul class='ays_lb_ul' style='width: ".$ind_leadboard_width.";'>
                    <li class='ays_lb_li' style='background: ".$ind_leadboard_color.";'>";

                foreach ($ind_leadboard_columns_order as $key => $value) {
                     if (isset($ind_leadboard_columns[$value])) {
                        if ($value == '') {
                            continue;
                        }
                        $content .= $default_ind_leadboard_header_value[$value];
                    }
                }

                $content .= "</li>";

                foreach ($result as $val) {
                    $score = round($val['avg_score'], 2);
                    $user_id = intval($val['user_id']);
                    $duration = (isset($val['dur_avg']) && $val['dur_avg'] != '') ? round(floatval($val['dur_avg']), 2) : '0';

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
                    }

                    $ays_default_html_order = array(
                        "pos"        => "<div class='ays_lb_pos'>$c</div>",
                        "name"       => "<div class='ays_lb_user'>$user_name</div>",
                        "score"      => "<div class='ays_lb_score'>$score %</div>",
                        "duration"   => "<div class='ays_lb_duration'>$duration s</div>",
                        "points"     => "<div class='ays_lb_points'>$points</div>",
                    );

                    $content .= "<li class='ays_lb_li'>";
                    foreach ($ind_leadboard_columns_order as $key => $value) {
                        if (isset($ind_leadboard_columns[$value])) {
                            if ($value == '') {
                                continue;
                            }
                            $content .= $ays_default_html_order[$value];
                        }
                    }

                    $content .= "</li>";
                    $c++;
                }
                $content .= "</ul>
                </div>";
                // echo $content;
                return str_replace(array("\r\n", "\n", "\r"), '', $content);
            }else{
                $content = "<div class='ays_lb_container'>
                    <ul class='ays_lb_ul' style='width: ".$ind_leadboard_width."px;'>
                        <li class='ays_lb_li' style='background: ".$ind_leadboard_color.";'>";
                foreach ($ind_leadboard_columns_order as $key => $value) {
                    if (isset($ind_leadboard_columns[$value])) {
                        if ($value == '') {
                            continue;
                        }
                        $content .= $default_ind_leadboard_header_value[$value];
                    }
                }
                $content .= "</li>";

                $content .= "<li class='ays_not_data'>" . __("There is no data yet", $this->plugin_name) . "</li>
                    </ul>
                </div>";
                // echo $content;
                return str_replace(array("\r\n", "\n", "\r"), '', $content);
            }
        }
        // echo $content;
        return str_replace(array("\r\n", "\n", "\r"), '', $content);
    }

    public function ays_generate_gleaderboard_list($attr){
        // ob_start();
        global $wpdb;
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

        $default_glob_leadboard_header_value = array(
            "pos"        => "<div class='ays_lb_pos ays_glb_pos'>" . __( "Pos.", $this->plugin_name ) . "</div>",
            "name"       => "<div class='ays_lb_user ays_glb_user'>" . __( "Name", $this->plugin_name ) . "</div>",
            "score"      => "<div class='ays_lb_score ays_glb_score'>" . __( "Score", $this->plugin_name ) . "</div>",
            "duration"   => "<div class='ays_lb_duration ays_glb_duration'>" . __( "Duration", $this->plugin_name ) . "</div>",
            "points"     => "<div class='ays_lb_points ays_glb_points'>" . __( "Points", $this->plugin_name ) . "</div>",
        );

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
                        GROUP BY user_id
                        ORDER BY sum_points DESC, dur_sum
                        LIMIT ".$glob_leadboard_count;

            }else{
                $sql = "SELECT DISTINCT a.user_id, a.score AS avg_score, a.points AS avg_points, MIN(a.duration) AS dur_avg, a.user_name, a.options
                        FROM (
                                SELECT user_id as ue, ".strtoupper($glob_leadboard_sort)."(CAST(`score` AS DECIMAL(10,0))) AS new_score
                                FROM {$wpdb->prefix}aysquiz_reports
                                WHERE user_id != 0
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
                             ) AS a
                        ON e.ue = a.user_email AND e.new_score = a.score
                        GROUP BY a.user_email
                        ORDER BY e.new_score DESC, dur_avg
                        LIMIT ".$glob_leadboard_count;
            }
        }

        $result = $wpdb->get_results($sql, 'ARRAY_A');

        $c = 1;
        $content = '';

        $content .= '
        <style>
            '. $glob_leadboard_suctom_css .'
        </style>';

        $content .= "<div class='ays_lb_container ays_glb_container'>
        <ul class='ays_lb_ul ays_glb_ul' style='width: ".$glob_leadboard_width.";'>
            <li class='ays_lb_li ays_glb_li' style='background: ".$glob_leadboard_color.";'>";
        foreach ($glob_leadboard_columns_order as $key => $value) {
             if (isset($glob_leadboard_columns[$value])) {
                if ($value == '') {
                    continue;
                }
                $content .= $default_glob_leadboard_header_value[$value];
            }
        }

        $content .="</li>";

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
                }

                $ays_default_html_order = array(
                    "pos"        => "<div class='ays_lb_pos ays_glb_pos'>$c</div>",
                    "name"       => "<div class='ays_lb_user ays_glb_user'>$user_name</div>",
                    "score"      => "<div class='ays_lb_score ays_glb_score'>$score %</div>",
                    "duration"   => "<div class='ays_lb_duration ays_glb_duration'>$duration s</div>",
                    "points"     => "<div class='ays_lb_points ays_glb_points'>$points</div>",
                );

                $content .= "<li class='ays_lb_li'>";
                foreach ($glob_leadboard_columns_order as $key => $value) {
                    if (isset($glob_leadboard_columns[$value])) {
                        if ($value == '') {
                            continue;
                        }
                        $content .= $ays_default_html_order[$value];
                    }
                }

                $content .= "</li>";
                $c++;
            }
        }else{
            $content .= "<li class='ays_not_data'>" . __("There is no data yet", $this->plugin_name) . "</li>";
        }

        $content .= "</ul>
        </div>";

        // echo $content;

        return str_replace(array("\r\n", "\n", "\r"), '', $content);
    }

    public function ays_generate_global_quiz_cat_leaderboard_list($attr){
        // ob_start();
        global $wpdb;
        $quiz_settings = $this->settings;
        $leadboard_res = ($quiz_settings->ays_get_setting('leaderboard') === false) ? json_encode(array()) : $quiz_settings->ays_get_setting('leaderboard');
        $quiz_settings_options = ($quiz_settings->ays_get_setting('options') === false) ? json_encode(array()) : $quiz_settings->ays_get_setting('options');

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

        $leadboard['global_quiz_cat']['glob_quiz_cat_leadboard_columns'] = ! isset( $leadboard['global_quiz_cat']['glob_quiz_cat_leadboard_columns'] ) ? $default_glob_leadboard_columns : $leadboard['global_quiz_cat']['glob_quiz_cat_leadboard_columns'];
        $glob_quiz_cat_leadboard_columns = (isset( $leadboard['global_quiz_cat']['glob_quiz_cat_leadboard_columns'] ) && !empty($leadboard['global_quiz_cat']['glob_quiz_cat_leadboard_columns']) ) ? $leadboard['global_quiz_cat']['glob_quiz_cat_leadboard_columns'] : array();
        $glob_quiz_cat_leadboard_columns_order = (isset( $leadboard['global_quiz_cat']['glob_quiz_cat_leadboard_columns'] ) && !empty($leadboard['global_quiz_cat']['glob_quiz_cat_leadboard_columns']) ) ? $leadboard['global_quiz_cat']['glob_quiz_cat_leadboard_columns'] : $default_glob_leadboard_columns;

        $default_glob_quiz_cat_leadboard_header_value = array(
            "pos"        => "<div class='ays_lb_pos ays_glb_pos'>" . __( "Pos.", $this->plugin_name ) . "</div>",
            "name"       => "<div class='ays_lb_user ays_glb_user'>" . __( "Name", $this->plugin_name ) . "</div>",
            "score"      => "<div class='ays_lb_score ays_glb_score'>" . __( "Score", $this->plugin_name ) . "</div>",
            "duration"   => "<div class='ays_lb_duration ays_glb_duration'>" . __( "Duration", $this->plugin_name ) . "</div>",
            "points"     => "<div class='ays_lb_points ays_glb_points'>" . __( "Points", $this->plugin_name ) . "</div>",
        );

        $id = (isset($attr['id'])) ? absint(intval($attr['id'])) : null;
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
                                ".strtoupper($glob_quiz_cat_leadboard_sort)."(CAST(points AS DECIMAL(10))) AS avg_points
                            FROM {$wpdb->prefix}aysquiz_reports
                            WHERE user_id != 0 AND quiz_id IN({$quiz_ids})
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
                            GROUP BY user_id
                            ORDER BY sum_points DESC, dur_sum
                            LIMIT ".$glob_quiz_cat_leadboard_count;

                }else{
                    $sql = "SELECT DISTINCT a.user_id, a.score AS avg_score, a.points AS avg_points, MIN(a.duration) AS dur_avg, a.user_name, a.options
                            FROM (
                                    SELECT user_id as ue, ".strtoupper($glob_quiz_cat_leadboard_sort)."(CAST(`score` AS DECIMAL(10,0))) AS new_score
                                    FROM {$wpdb->prefix}aysquiz_reports
                                    WHERE user_id != 0 AND quiz_id IN({$quiz_ids})
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
                            GROUP BY user_email
                            ORDER BY sum_points DESC, dur_sum
                            LIMIT ".$glob_quiz_cat_leadboard_count;
                }else{
                    $sql = "SELECT DISTINCT a.user_email, a.score AS avg_score, a.points AS avg_points, MIN(a.duration) AS dur_avg, a.user_id, a.user_name, a.options
                            FROM (
                                    SELECT user_email as ue, ".strtoupper($glob_quiz_cat_leadboard_sort)."(CAST(`score` AS DECIMAL(10,0))) AS new_score
                                    FROM {$wpdb->prefix}aysquiz_reports
                                    WHERE !(user_email='' OR user_email IS NULL) AND quiz_id IN({$quiz_ids})
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
                                 ) AS a
                            ON e.ue = a.user_email AND e.new_score = a.score
                            GROUP BY a.user_email
                            ORDER BY e.new_score DESC, dur_avg
                            LIMIT ".$glob_quiz_cat_leadboard_count;
                }
            }

            $result = $wpdb->get_results($sql, 'ARRAY_A');

            $c = 1;
            $content = '';

            $content .= '
            <style>
                '. $glob_quiz_cat_leadboard_cuctom_css .'
            </style>';

            $content .= "<div class='ays_lb_container ays_glb_container'>
            <ul class='ays_lb_ul ays_glb_ul' style='width: ".$glob_quiz_cat_leadboard_width.";'>
                <li class='ays_lb_li ays_glb_li' style='background: ".$glob_quiz_cat_leadboard_color.";'>";
            foreach ($glob_quiz_cat_leadboard_columns_order as $key => $value) {
                 if (isset($glob_quiz_cat_leadboard_columns[$value])) {
                    if ($value == '') {
                        continue;
                    }
                    $content .= $default_glob_quiz_cat_leadboard_header_value[$value];
                }
            }

            $content .= "</li>";

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
                    }

                    $ays_default_html_order = array(
                        "pos"        => "<div class='ays_lb_pos ays_glb_pos'>$c</div>",
                        "name"       => "<div class='ays_lb_user ays_glb_user'>$user_name</div>",
                        "score"      => "<div class='ays_lb_score ays_glb_score'>$score %</div>",
                        "duration"   => "<div class='ays_lb_duration ays_glb_duration'>$duration s</div>",
                        "points"     => "<div class='ays_lb_points ays_glb_points'>$points</div>",
                    );

                    $content .= "<li class='ays_lb_li'>";
                    foreach ($glob_quiz_cat_leadboard_columns_order as $key => $value) {
                        if (isset($glob_quiz_cat_leadboard_columns_order[$value])) {
                            if ($value == '') {
                                continue;
                            }
                            $content .= $ays_default_html_order[$value];
                        }
                    }

                    $content .= "</li>";
                    $c++;
                }
            }else{
                $content .= "<li class='ays_not_data'>" . __("There is no data yet", $this->plugin_name) . "</li>";
            }

            $content .= "</ul>
            </div>";

            // echo $content;

            return str_replace(array("\r\n", "\n", "\r"), '', $content);
        }
    }

}
