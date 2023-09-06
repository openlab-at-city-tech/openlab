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
class Ays_Quiz_Maker_Most_Popular_Shortcodes_Public
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

    private $html_class_prefix = 'ays-quiz-most-popular-shortcodes-';
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

        add_shortcode('ays_quiz_most_popular', array($this, 'ays_generate_most_popular_method'));
    }

    /*
    ==========================================
        Most Popular Quiz | Start
    ==========================================
    */

    public function ays_generate_most_popular_method( $attr ){

        $unique_id = uniqid();
        $this->unique_id = $unique_id;
        $this->unique_id_in_class = "-" . $unique_id;

        $quiz_most_popular_html = $this->ays_quiz_most_popular_html( $attr );
        $quiz_most_popular_html = Quiz_Maker_Data::ays_quiz_translate_content( $quiz_most_popular_html );
        
        return str_replace(array("\r\n", "\n", "\r"), "\n", $quiz_most_popular_html);
    }

    public function ays_quiz_most_popular_html( $attr ){

        $quiz_count = (isset( $attr['count'] ) && $attr['count'] != "") ? absint( sanitize_text_field( $attr['count'] ) ) : ""; 

        $quiz_ids = $this->ays_quiz_get_most_popular_quiz_id( $quiz_count );

        $content_html = array();

        if( is_null( $quiz_ids ) || empty( $quiz_ids ) ){
            $content_html = "<p style='text-align: center;font-style:italic;'>" . __( "There are no results yet.", $this->plugin_name ) . "</p>";
            return $content_html;
        }

        if ( ! empty( $quiz_ids ) ) {
            $content_html[] = "<div class='ays-quiz-most-popular-container'>";

            foreach ($quiz_ids as $key => $quiz_id) {

                $shortcode = "[ays_quiz id='".$quiz_id."']";

                $content_html[] = do_shortcode( $shortcode );
            }

            $content_html[] = "</div>";
        }

        $content_html = implode( '' , $content_html);

        return $content_html;
    }

    public function ays_quiz_get_most_popular_quiz_id( $quiz_count ) {
        global $wpdb;

        $reports_table = esc_sql( $wpdb->prefix . "aysquiz_reports" );
        $quizzes_table = esc_sql( $wpdb->prefix . "aysquiz_quizes" );

        $quiz_ids = array();

        $most_popular_quiz_count = (isset( $quiz_count ) && $quiz_count != "") ? absint( sanitize_text_field( $quiz_count ) ) : "";

        $sql = "SELECT COUNT(*) AS `res_count`, `quiz_id` 
                FROM `{$reports_table}` 
                GROUP BY `quiz_id` 
                ORDER BY `res_count` 
                DESC";

        $results = $wpdb->get_results($sql, 'ARRAY_A');

        if ( ! is_null( $results ) && ! empty( $results ) ) {

            foreach ($results as $key => $value) {

                $id = ( isset( $value['quiz_id'] ) && $value['quiz_id'] != "" && absint( $value['quiz_id'] ) > 0 ) ? absint( sanitize_text_field( $value['quiz_id'] ) ) : null;

                if ( ! is_null( $id ) ) {

                    $sql = "SELECT * FROM `{$quizzes_table}` WHERE `published` = 1 AND `id`=" . $id;

                    $quiz_data = $wpdb->get_row($sql, 'ARRAY_A');

                    if ( $most_popular_quiz_count == "" ) {
                        if ( ! is_null( $quiz_data ) && $quiz_data > 0 ) {
                            $quiz_ids[] = $id;
                            break;
                        }
                    } else {
                        if ( count( $quiz_ids ) >= $most_popular_quiz_count && $most_popular_quiz_count != 0 ) {
                            break;
                        } elseif($most_popular_quiz_count == 0){
                            $quiz_ids[] = $id;
                            break;
                        } else {
                            if ( ! is_null( $quiz_data ) && $quiz_data > 0 ) {
                                if ( ! in_array($id, $quiz_ids) ) {
                                    $quiz_ids[] = $id;
                                }
                            }
                        }
                    }
                }

                
            }
        }

        return $quiz_ids;
    }

    /*
    ==========================================
        Most Popular Quiz | End
    ==========================================
    */

}
