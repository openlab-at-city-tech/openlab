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
class Quiz_Maker_Display_Questions
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

        add_shortcode('ays_display_questions', array($this, 'ays_generate_display_questions_method'));
    }

    public function ays_get_questions_by_quiz_id( $attr ){
        global $wpdb;

        $quizes_table    = esc_sql( $wpdb->prefix . "aysquiz_quizes" );
        $questions_table = esc_sql( $wpdb->prefix . "aysquiz_questions" );

        $results      = array();
        $question_ids = '';
        $display_questions = 'quiz';

        $id = (isset($attr['id']) && sanitize_text_field( $attr['id'] ) != '') ? absint( sanitize_text_field( $attr['id'] ) ) : null;

        $display_questions_by = (isset($attr['by']) && sanitize_text_field( $attr['by'] ) != '') ? stripcslashes( sanitize_text_field( $attr['by'] ) ) : 'quiz';

        $display_questions_orderby = (isset($attr['orderby']) && sanitize_text_field( $attr['orderby'] ) != '') ? stripcslashes( sanitize_text_field( $attr['orderby'] ) ) : 'ASC';

        switch ( $display_questions_by ) {
            case 'quiz':
                $display_questions = 'quiz';
                break;
            case 'category':
                $display_questions = 'category';
                break;
            default:
                $display_questions = 'quiz';
                break;
        }

        if ( is_null( $id ) ) {
            return null;
        }

        if ( $display_questions == 'quiz' ) {
            $sql = "SELECT question_ids FROM {$quizes_table} WHERE id = " . $id;
            $question_ids = $wpdb->get_var( $sql );
        } elseif ( $display_questions == 'category' ) {
            $sql = "SELECT id FROM {$questions_table} WHERE category_id = " . $id;
            $question_ids_arr = $wpdb->get_col( esc_sql($sql) );

            if( ! empty( $question_ids_arr ) ) {
                $question_ids = implode( ',', $question_ids_arr );
            } else {
                $question_ids = '';
            }
        }

        if ( empty( $question_ids ) || is_null( $question_ids ) ) {
            return null;
        }

        $questions_orderby = '';
        switch ( $display_questions_orderby ) {
            case 'ASC':
                $questions_orderby = " ORDER BY id ASC";
                break;
            case 'DESC':
                $questions_orderby = " ORDER BY id DESC";
                break;
            case 'default':
                $questions_orderby = " ORDER BY FIELD(id, ". $question_ids .")";
                break;
            default:
                $questions_orderby = " ORDER BY id ASC";
                break;
        }

        $sql = "SELECT question FROM {$questions_table} WHERE id IN (". $question_ids .") AND published = 1" . $questions_orderby;

        $results = $wpdb->get_results( ($sql), 'ARRAY_A');

        if ( empty( $results ) || is_null( $results ) ) {
            return null;
        }

        return $results;

    }

    public function ays_display_questions_html( $attr ){

        $display_questions_html = array();

        $results = $this->ays_get_questions_by_quiz_id( $attr );

        if( $results === null ){
            $display_questions_html = "<p style='text-align: center;font-style:italic;'>" . __( "There are no questions atteched yet.", $this->plugin_name ) . "</p>";
            return $display_questions_html;
        }

        $display_questions_html[] = "<div class='ays-quiz-display-questions-container'>";

        foreach ($results as $key => $question) {
            if ( isset($question['question']) && $question['question'] != '' ) {

                $display_questions_html[] = "<div class='ays-quiz-display-question-box'>";
                    $display_questions_html[] = Quiz_Maker_Data::ays_autoembed( $question['question'] );
                $display_questions_html[] = "</div>";

            }
        }

        $display_questions_html[] = "</div>";

        $display_questions_html = implode( '', $display_questions_html );

        return $display_questions_html;
    }

    public function ays_generate_display_questions_method( $attr ) {

        $id = (isset($attr['id']) && $attr['id'] != '') ? absint(intval($attr['id'])) : null;

        if (is_null($id)) {
            $content = "<p class='wrong_shortcode_text' style='color:red;'>" . __('Wrong shortcode initialized', $this->plugin_name) . "</p>";
            return str_replace(array("\r\n", "\n", "\r"), '', $content);
        }

        $display_questions_html = $this->ays_display_questions_html( $attr );
        // echo $display_questions_html;

        return str_replace(array("\r\n", "\n", "\r"), "\n", $display_questions_html);
    }
}
