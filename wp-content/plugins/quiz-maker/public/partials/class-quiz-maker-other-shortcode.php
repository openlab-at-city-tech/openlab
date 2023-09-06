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
class Ays_Quiz_Maker_Other_Shortcodes
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

    private $html_class_prefix = 'ays-quiz-other-shortcodes-';
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

        add_shortcode('ays_quiz_points_count', array($this, 'ays_quiz_points_count_method'));
    }

        public function ays_quiz_points_count_method( $attr ){

        $id = (isset($attr['id']) && $attr['id'] != '') ? absint( sanitize_text_field($attr['id']) ) : null;

        $unique_id = uniqid();
        $this->unique_id = $unique_id;
        $this->unique_id_in_class = $id . "-" . $unique_id;


        $passed_users_count_html = $this->ays_quiz_points_count_html( $attr );
        return str_replace(array("\r\n", "\n", "\r"), "\n", $passed_users_count_html);
    }

    public function  ays_quiz_points_count_html( $attr ){

        $results = $this->get_quiz_passed_users_points( $attr );

        $content_html = array();

        if($results === null){
            $content_html = "<p style='text-align: center;font-style:italic;'>" . __( "There are no results yet.", $this->plugin_name ) . "</p>";
            return $content_html;
        }

        $failed_users_count_by_score = (isset( $results ) && $results != '') ? sanitize_text_field( $results ) : 0;

        $content_html[] = "<span class='". $this->html_name_prefix ."points-count' id='". $this->html_name_prefix ."points-count". $this->unique_id_in_class ."' data-id='". $this->unique_id ."'>";
            $content_html[] = $failed_users_count_by_score;
        $content_html[] = "</span>";

        $content_html = implode( '' , $content_html);

        return $content_html;
    }

    public function get_quiz_passed_users_points( $attr ) {
        global $wpdb;

        $reports_table = esc_sql( $wpdb->prefix . "aysquiz_reports" );
        $quizzes_table = esc_sql( $wpdb->prefix . "aysquiz_quizes" );

        $id = (isset($attr['id']) && $attr['id'] != '') ? sanitize_text_field($attr['id']) : '';
        $get_points_mode = (isset($attr['mode']) && $attr['mode'] != '') ? sanitize_text_field($attr['mode']) : '';

        if( !empty( $id ) ){
            $sql = "SELECT * FROM {$quizzes_table} WHERE `id` IN(" . $id . ")";
            $quiz_data = $wpdb->get_results($sql, 'ARRAY_A');

            $sum_of_quizes_points = 0;
            $quiz_max_points = 0;
            $user_id = get_current_user_id();

            foreach ($quiz_data as $quiz_key => $quiz) {
                $sql = "SELECT points AS points
                FROM {$reports_table}
                WHERE quiz_id=". $quiz['id'] ." AND `status` = 'finished' AND `user_id` = ". $user_id . " ";

                $results = $wpdb->get_results($sql, 'ARRAY_A');
                if($get_points_mode == "best") {
                    $quiz_max_points = $this->getMaxPoint($results);
                    $sum_of_quizes_points += $quiz_max_points;
                }
                
                else{
                    foreach ($results as $key => $point) {
                        $sum_of_quizes_points += $point['points'];
                    }
                }

            };
            return $sum_of_quizes_points;
        }
        else if( empty( $id ) ) {
            $sum_of_quizes_points = 0;
            $quiz_max_points = 0;
            $user_id = get_current_user_id();
            $all_quiz_ids_sql = "SELECT id FROM {$quizzes_table}";
            $all_quiz_ids = $wpdb->get_results($all_quiz_ids_sql, 'ARRAY_A');
            
            foreach ($all_quiz_ids as $quiz_ids => $quiz_id) {
                $sql = "SELECT points AS points
                    FROM {$reports_table}
                    WHERE quiz_id=". $quiz_id['id'] ." AND `status` = 'finished' AND `user_id` = ". $user_id . " ";

                $results = $wpdb->get_results($sql, 'ARRAY_A');
                if($get_points_mode == "best") {
                    $quiz_max_points = $this->getMaxPoint($results);
                    $sum_of_quizes_points += $quiz_max_points;
                }
                    
                else{
                    foreach ($results as $key => $point) {
                        $sum_of_quizes_points += $point['points'];
                    }
                }

            }
            return $sum_of_quizes_points;
        }
    }

    public function getMaxPoint( $array ) {
        $max = 0;
        foreach( $array as $k => $v ){
            $max = max( array( $max, $v['points'] ) );
        }
        return $max;
    }

}
