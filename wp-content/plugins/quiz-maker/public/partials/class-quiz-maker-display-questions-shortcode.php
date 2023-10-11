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

        add_shortcode('ays_display_questions', array($this, 'ays_generate_display_questions_method'));

        $this->settings = new Quiz_Maker_Settings_Actions($this->plugin_name);
    }

    public function ays_get_questions_by_quiz_id( $attr ){
        global $wpdb;

        $quizes_table    = esc_sql( $wpdb->prefix . "aysquiz_quizes" );
        $questions_table = esc_sql( $wpdb->prefix . "aysquiz_questions" );
        $answers_table   = esc_sql( $wpdb->prefix . "aysquiz_answers" );

        $results            = array();
        $data               = array();
        $question_ids       = '';
        $display_questions  = 'quiz';

        $id = (isset($attr['id']) && sanitize_text_field( $attr['id'] ) != '') ? absint( intval( sanitize_text_field($attr['id']) ) ) : null;

        $quiz_enable_question_answers = (isset($attr['quiz_enable_question_answers']) && $attr['quiz_enable_question_answers'] !== false) ? $attr['quiz_enable_question_answers'] : false;

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
            $sql = "SELECT question_ids FROM {$quizes_table} WHERE published = 1 AND id = " . $id;
            $question_ids = $wpdb->get_var( esc_sql($sql) );
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
            case 'random':
                $questions_orderby = " ORDER BY id DESC";
                break;
            default:
                $questions_orderby = " ORDER BY id ASC";
                break;
        }

        $sql = "SELECT question,id FROM {$questions_table} WHERE id IN (". $question_ids .") AND published = 1" . $questions_orderby;

        $results = $wpdb->get_results( esc_sql($sql), 'ARRAY_A');

        if ( empty( $results ) || is_null( $results ) ) {
            return null;
        } else {
            foreach ($results as $key => $value) {
                $question_id = (isset( $value['id'] ) && $value['id'] != "") ? $value['id'] : "";

                if ( $question_id == "" ) {
                    continue;
                }
                $question_text = (isset( $value['question'] ) && $value['question'] != "") ? $value['question'] : "";

                if ( !in_array( $question_id, $data ) ) {
                    if ( $question_text != "" ) {
                        $data[$question_id]['question'] = $question_text;
                    }
                }

            }
        }

        if ( $quiz_enable_question_answers ) {
            $sql = "SELECT answer, question_id FROM {$answers_table} WHERE question_id IN (". $question_ids .")";
            $answers_data = $wpdb->get_results( esc_sql($sql), 'ARRAY_A');

            if ( !is_null( $answers_data ) && !empty( $answers_data ) ) {
                foreach ($answers_data as $key => $answer) {
                    $question_id = (isset( $answer['question_id'] ) && $answer['question_id'] != "") ? $answer['question_id'] : "";

                    if ( $question_id == "" ) {
                        continue;
                    }
                    $answer_text = (isset( $answer['answer'] ) && $answer['answer'] != "") ? $answer['answer'] : "";

                    if ( !in_array( $question_id, $data ) ) {
                        if ( $answer_text != "" ) {
                            $data[$question_id]['answers'][] = $answer_text;
                        }
                    }

                }
            }
        }

        if ( $display_questions_orderby == "random" ) {
            shuffle( $data );
        }

        return $data;

    }

    public function ays_display_questions_html( $attr ){

        $display_questions_html = array();

        $quiz_settings = $this->settings;
        $quiz_settings_options = ($quiz_settings->ays_get_setting('options') === false) ? json_encode(array()) : $quiz_settings->ays_get_setting('options');
        $options = json_decode(stripcslashes($quiz_settings_options), true);

        // Enable question answers
        $options['quiz_enable_question_answers'] = isset($options['quiz_enable_question_answers']) ? esc_attr( $options['quiz_enable_question_answers'] ) : 'off';
        $quiz_enable_question_answers = (isset($options['quiz_enable_question_answers']) && esc_attr( $options['quiz_enable_question_answers'] ) == "on") ? true : false;
        
        $attr['quiz_enable_question_answers'] = $quiz_enable_question_answers;

        $results = $this->ays_get_questions_by_quiz_id( $attr );

        if( $results === null ){
            $display_questions_html = "<p style='text-align: center;font-style:italic;'>" . __( "There are no questions atteched yet.", $this->plugin_name ) . "</p>";
            return $display_questions_html;
        }
        
        $display_questions_html[] = "<div class='ays-quiz-display-questions-container'>";
        
        foreach ($results as $question_id => $data) {
            if ( isset($data['question']) && $data['question'] != '' ) {

                $display_questions_html[] = "<div class='ays-quiz-display-question-box'>";
                    $display_questions_html[] = "<div class='ays-quiz-display-question-row'>";
                        $display_questions_html[] = Quiz_Maker_Data::ays_autoembed( $data['question'] );
                    $display_questions_html[] = "</div>";

                if ( $quiz_enable_question_answers && isset($data['answers']) && !empty($data['answers']) ) {
                    $display_questions_html[] = "<div class='ays-quiz-display-answer-row'>";

                    foreach ($data['answers'] as $key => $answer) {

                        if (strpos($answer, "%%%") !== false) {
                            $answer_arr = explode("%%%", $answer);
                            $answer = (isset( $answer_arr[0] ) && $answer_arr[0] != "") ? $answer_arr[0] : $answer;
                        }

                        $display_questions_html[] = "<div class='ays-quiz-display-answer'>";
                            $display_questions_html[] = stripcslashes($answer);
                        $display_questions_html[] = "</div>";
                    }

                    $display_questions_html[] = "</div>";
                }

                $display_questions_html[] = "</div>";
                
            }
        }
        
        $display_questions_html[] = "<style>";
        $display_questions_html[] = "
            .ays-quiz-display-questions-container .ays-quiz-display-answer-row {
                padding-left: 20px;
            }

        ";

        $display_questions_html[] = "</style>";
        $display_questions_html[] = "</div>";

        $display_questions_html = implode( '', $display_questions_html );

        return $display_questions_html;
    }

    public function ays_generate_display_questions_method( $attr ) {

        $id = (isset($attr['id']) && $attr['id'] != '') ? absint(intval($attr['id'])) : null;

        if (is_null($id)) {
            $display_questions_html = "<p class='wrong_shortcode_text' style='color:red;'>" . __('Wrong shortcode initialized', $this->plugin_name) . "</p>";
            return str_replace(array("\r\n", "\n", "\r"), '', $display_questions_html);
        }

        $display_questions_html = $this->ays_display_questions_html( $attr );
        $display_questions_html = Quiz_Maker_Data::ays_quiz_translate_content( $display_questions_html );

        return str_replace(array("\r\n", "\n", "\r"), '', $display_questions_html);
    }
}
