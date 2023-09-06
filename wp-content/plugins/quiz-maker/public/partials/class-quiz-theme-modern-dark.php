<?php


/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://ays-pro.com/
 * @since      1.0.0
 *
 * @package    Quiz_Maker
 * @subpackage Quiz_Maker/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Quiz_Maker
 * @subpackage Quiz_Maker/includes
 * @author     AYS Pro LLC <info@ays-pro.com>
 */
class Quiz_Theme_Modern_Dark extends Quiz_Maker_Public{

    protected $plugin_name;

    protected $version;

    protected $theme_name;

    public $settings;

    public $buttons_texts;

    public function __construct($plugin_name, $plugin_version, $theme_name, $settings, $buttons_texts) {
        $this->version = $plugin_version;
        $this->plugin_name = $plugin_name;
        $this->theme_name = $theme_name;
        $this->settings = $settings;
        $this->buttons_texts = $buttons_texts;

        $this->define_theme_styles();
        $this->define_theme_scripts();
    }

    protected function define_theme_styles(){
        wp_enqueue_style($this->plugin_name.'modern_dark_css',dirname(plugin_dir_url(__FILE__)) . '/css/theme_modern_dark.css', array(), time(), 'all');
    }
    
    protected function define_theme_scripts(){
        wp_enqueue_script(
            $this->plugin_name.'-modern_dark_js',
            dirname(plugin_dir_url(__FILE__)) . '/js/theme_modern_dark.js',
            array('jquery'),
            $this->version,
            false
        );
    }

    public function ays_generate_quiz($quiz){
        
        $quiz_id = $quiz->quizID;
        $arr_questions = $quiz->questions;
        $questions_count = $quiz->questionsCount;
        $options = $quiz->quizOptions;
        $questions = "";
        $questions = $this->get_quiz_questions($arr_questions, $quiz_id, $options, false);

        if (isset($quiz->quizParts['cat_selective_start_page']) && $quiz->quizParts['cat_selective_start_page'] != "") {
            return $quiz->quizParts['cat_selective_start_page'];
        }
        
        if($quiz->quizParts['main_content_middle_part'] == ""){
            $quiz->quizParts['main_content_middle_part'] = $questions;
        }
        $additional_css = "
            <style>
                #ays-quiz-container-".$quiz_id." .ays-field{
                    background:" . Quiz_Maker_Data::hex2rgba($quiz->quizColors['Color'], '0.3') . " ;
                }
                #ays-quiz-container-".$quiz_id." .ays_checked_answer{
                    background:" . Quiz_Maker_Data::hex2rgba($quiz->quizColors['Color'], '0.5') . ";
                }
                #ays-quiz-container-".$quiz_id." section.ays_quiz_timer_container {
                     background-color:" . Quiz_Maker_Data::hex2rgba($quiz->quizColors['Color'], '0.3') . " ;
                }
                #ays-quiz-container-".$quiz_id." .ays-modern-dark-question{
                     background:" . Quiz_Maker_Data::hex2rgba($quiz->quizColors['Color'], '0.3') . " ;
                     word-break: break-all;
                }
                #ays-quiz-container-".$quiz_id." .action-button{
                    background-color:" . Quiz_Maker_Data::hex2rgba($quiz->quizColors['Color'], '0.4') . " ;
                }
                #ays-quiz-container-" . $quiz_id . " #ays_finish_quiz_" . $quiz_id . " .action-button:hover,
                #ays-quiz-container-" . $quiz_id . " #ays_finish_quiz_" . $quiz_id . " .action-button:focus {
                    box-shadow: 0 0 0 2px " . $quiz->quizColors['buttonsTextColor'] . ";
                    background-color:" . Quiz_Maker_Data::hex2rgba($quiz->quizColors['Color'], '0.7') . "
                }
            </style>";
        if ( !is_null( $this->aysQuizUserExportDataArray ) ) {

            $additional_css .= "<script>";
            $additional_css .= "
                    if(typeof aysQuizUserExportDataArray === 'undefined'){
                        var aysQuizUserExportDataArray = [];
                    }
                    aysQuizUserExportDataArray['".$quiz_id."']  = '" . base64_encode(json_encode( $this->aysQuizUserExportDataArray )) . "';";
            $additional_css .= "</script>";
        }
        
        $quiz->quizParts['quiz_additional_styles'] = $additional_css;
        $container = implode(" ", $quiz->quizParts);
        
        return $container;
    }

    public function ays_default_answer_html($question_id, $quiz_id, $answers, $options){
        $answer_container = '';
        $show_answers_numbering = $options['show_answers_numbering'];
        $numering_type = Quiz_Maker_Data::ays_answer_numbering($show_answers_numbering);

        $quiz_enable_keyboard_navigation = (isset($options['quiz_enable_keyboard_navigation']) && $options['quiz_enable_keyboard_navigation'] == 'on') ? true : false;
        $attributes_for_keyboard = "";
        $class_for_keyboard = "";
        $class_label_for_keyboard = "";
        if($quiz_enable_keyboard_navigation){
            $class_for_keyboard = "ays-quiz-keyboard-active";
            $attributes_for_keyboard = "tabindex='0'";
            $class_label_for_keyboard = "ays-quiz-keyboard-label";
        }

        $answer_container_script    = '';
        $answer_container_script_html = '';
        $script_data_arr = array();
        $question_answer = array();
        if ( $options["questionType"] == 'checkbox' ) {

            $enable_max_selection_number = ( isset( $options['enable_max_selection_number'] ) && $options["enable_max_selection_number"] == 'on' ) ? true : false;
            $max_selection_number        = ( isset( $options["max_selection_number"] ) && $options["max_selection_number"] != '' ) ? absint($options["max_selection_number"]) : '';

            $enable_min_selection_number = ( isset( $options['enable_min_selection_number'] ) && $options["enable_min_selection_number"] == 'on' ) ? true : false;
            $min_selection_number        = ( isset( $options["min_selection_number"] ) && $options["min_selection_number"] != '' ) ? absint($options["min_selection_number"]) : '';

            if ( ( $enable_max_selection_number && ! empty( $max_selection_number ) && $max_selection_number != 0 ) || ( $enable_min_selection_number && ! empty( $min_selection_number ) && $min_selection_number != 0 ) ) {

                $script_data_arr['enable_max_selection_number'] = $enable_max_selection_number;
                $script_data_arr['max_selection_number'] = $max_selection_number;
                $script_data_arr['enable_min_selection_number'] = $enable_min_selection_number;
                $script_data_arr['min_selection_number'] = $min_selection_number;
            }
        }

        foreach ($answers as $key => $answer) {
            $answer_image = "";
            if(isset($answer['image']) && $answer['image'] != ''){
//                $ans_img = $this->ays_get_image_thumbnauil($answer['image']);
                $ans_img = $answer['image'];
                $answer_image_alt_text = Quiz_Maker_Data::ays_quiz_get_image_id_by_url($ans_img);
                
                $answer_image = "<img src='{$ans_img}' alt='". $answer_image_alt_text ."' class='ays-answer-image'>";
            }

            $numering_value = "";
            if( isset( $numering_type[$key] ) && $numering_type[$key] != '' ){
                $numering_value = $numering_type[$key] . " ";
            }

            if($options['useHTML']){

                $answer_content = $answer["answer"];

                $answer_content = stripslashes( $answer_content );
                $answer_content = do_shortcode( $answer_content );
            }else{
                $answer_content = do_shortcode(htmlspecialchars(stripslashes($answer["answer"])));
            }

            $question_answer[ $answer["id"] ] = htmlspecialchars_decode(stripslashes($answer["correct"]), ENT_QUOTES);

            $correct_answer_flag = 'ays_answer_image_class';
            if( $answer["correct"] == 1 ){
                $correct_answer_flag = 'ays_anser_image_class';
            }

            $answer_container .= "
            <div class='ays-field ays_list_view_item ".$class_for_keyboard."' ".$attributes_for_keyboard.">
                <input type='hidden' name='ays_answer_correct[]' value='{$answer["correct"]}'/>

                <input type='{$options["questionType"]}' name='ays_questions[ays-question-{$question_id}]' id='ays-answer-{$answer["id"]}-{$quiz_id}' value='{$answer["id"]}'/>

                    <label for='ays-answer-{$answer["id"]}-{$quiz_id}' class='".$class_label_for_keyboard."'>                        
                        <i class='ays_fa ays_fa_square_o answer-icon'></i>
                        " . $numering_value . $answer_content . "
                    </label>
                    <label for='ays-answer-{$answer["id"]}-{$quiz_id}' class='{$correct_answer_flag}'>{$answer_image}</label>
            </div>";

        }

        $script_data_arr['question_answer'] = $question_answer;

        $answer_container_script_html .= '<script>';
        $answer_container_script_html .= "
            if(typeof window.quizOptions_$quiz_id === 'undefined'){
                window.quizOptions_$quiz_id = [];
            }
            window.quizOptions_".$quiz_id."['".$question_id."'] = '" . base64_encode(json_encode($script_data_arr)) . "';";
        $answer_container_script_html .= '</script>';

        $answer_container .= $answer_container_script_html;

        return $answer_container;
    }
}

?>
