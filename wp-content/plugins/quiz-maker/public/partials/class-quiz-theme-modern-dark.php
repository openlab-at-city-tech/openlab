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

    public function __construct($plugin_name,$plugin_version,$theme_name) {
        $this->version = $plugin_version;
        $this->plugin_name = $plugin_name;
        $this->theme_name = $theme_name;
        $this->define_theme_styles();
        $this->define_theme_scripts();
    }

    protected function define_theme_styles(){
        wp_enqueue_style($this->plugin_name.'modern_dark_css',dirname(plugin_dir_url(__FILE__)) . '/css/theme_modern_dark.css', array(), false, 'all');
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
        
        if($quiz->quizParts['main_content_middle_part'] == ""){
            $quiz->quizParts['main_content_middle_part'] = $questions;
        }
        $additional_css = "
            <style>
                #ays-quiz-container-".$quiz_id." .ays-field{
                    background:" . $this->hex2rgba($quiz->quizColors['Color'], '0.3') . " ;
                }
                #ays-quiz-container-".$quiz_id." .ays_checked_answer{
                    background:" . $this->hex2rgba($quiz->quizColors['Color'], '0.5') . ";
                }
                #ays-quiz-container-".$quiz_id." section.ays_quiz_timer_container {
                     background-color:" . $this->hex2rgba($quiz->quizColors['Color'], '0.3') . " ;
                }
                #ays-quiz-container-".$quiz_id." .ays-modern-dark-question{
                     background:" . $this->hex2rgba($quiz->quizColors['Color'], '0.3') . " ;
                     word-break: break-all;
                }
                #ays-quiz-container-".$quiz_id." .action-button{
                    background-color:" . $this->hex2rgba($quiz->quizColors['Color'], '0.4') . " ;
                }
                #ays-quiz-container-" . $quiz_id . " #ays_finish_quiz_" . $quiz_id . " .action-button:hover,
                #ays-quiz-container-" . $quiz_id . " #ays_finish_quiz_" . $quiz_id . " .action-button:focus {
                    box-shadow: 0 0 0 2px " . $quiz->quizColors['textColor'] . ";
                    background-color:" . $this->hex2rgba($quiz->quizColors['Color'], '0.7') . " 
                }
            </style>";
        
        $quiz->quizParts['quiz_additional_styles'] = $additional_css;
        $container = implode(" ", $quiz->quizParts);
        
        return $container;
    }

    public function ays_default_answer_html($question_id, $quiz_id, $answers, $options){
        $answer_container = '';
        foreach ($answers as $answer) {
            $answer_image = "";
            if(isset($answer['image']) && $answer['image'] != ''){
//                $ans_img = $this->ays_get_image_thumbnauil($answer['image']);
                $ans_img = $answer['image'];
                $answer_image = "<img src='{$ans_img}' alt='answer_image' class='ays-answer-image'>";
            }

            if($options['useHTML']){
                $answer_content = do_shortcode((stripslashes($answer["answer"])));
            }else{
                $answer_content = do_shortcode(htmlspecialchars(stripslashes($answer["answer"])));
            }

            $answer_container .= "
            <div class='ays-field'>
                <input type='hidden' name='ays_answer_correct[]' value='{$answer["correct"]}'/>

                <input type='{$options["questionType"]}' name='ays_questions[ays-question-{$question_id}]' id='ays-answer-{$answer["id"]}-{$quiz_id}' value='{$answer["id"]}'/>

                    <label for='ays-answer-{$answer["id"]}-{$quiz_id}'>                        
                        <i class='ays_fa ays_fa_square_o answer-icon'></i>
                        " . $answer_content . "
                    </label>
                    <label for='ays-answer-{$answer["id"]}-{$quiz_id}'>{$answer_image}</label>

            </div>";

        }
        return $answer_container;
    }
}

?>
