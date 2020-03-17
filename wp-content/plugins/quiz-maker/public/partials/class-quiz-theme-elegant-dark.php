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
class Quiz_Theme_Elegant_Dark extends Quiz_Maker_Public{

    protected $plugin_name;

    protected $version;

    protected $theme_name;

    public function __construct($plugin_name, $plugin_version, $theme_name) {
        $this->version = $plugin_version;
        $this->plugin_name = $plugin_name;
        $this->theme_name = $theme_name;
        $this->define_theme_styles();
//        $this->define_theme_scripts();
    }

    protected function define_theme_styles(){
        wp_enqueue_style($this->plugin_name.'elegant_dark_css', dirname(plugin_dir_url(__FILE__)) . '/css/theme_elegant_dark.css', array(), false, 'all');
    }
    
    protected function define_theme_scripts(){
        wp_enqueue_script(
            $this->plugin_name.'-elegant_dark_js',
            dirname(plugin_dir_url(__FILE__)) . '/js/theme_elegant_dark.js',
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
                #ays-quiz-container-" . $quiz_id . " #ays_finish_quiz_" . $quiz_id . " div.step {                
                    background-color: " . $this->hex2rgba($quiz->quizColors['Color'], '0.2') . ";
                    border: 1px solid " . $this->hex2rgba($quiz->quizColors['Color'], '0.8') . ";
                }
            </style>";
        
        $quiz->quizParts['quiz_additional_styles'] = $additional_css;
        
        $container = implode("", $quiz->quizParts);
        
        return $container;
        
    }

    public function ays_default_answer_html($question_id, $quiz_id, $answers, $options){
        $answer_container = "";
        foreach ($answers as $answer) {
            $answer_image_style = "";
            if($options['answersViewClass'] == 'grid'){
                $answer_image_style = " style='width:100%!important;' ";
            }            
            $answer_image = "";
            if(isset($answer['image']) && $answer['image'] != ''){
//                $ans_img = $this->ays_get_image_thumbnauil($answer['image']);
                $ans_img = $answer['image'];
                $answer_image = "<img src='{$ans_img}' alt='answer_image' $answer_image_style class='ays-answer-image'>";
            }

            if($answer_image == ""){
                $answer_label_class = "";
                $answer_img_label_class = " ays_position_initial ";
            }else{
                if($options['answersViewClass'] == 'grid'){
                    $answer_label_class = " ays_empty_before_content ";
                }else{
                    $answer_label_class = "";
                }
                $answer_img_label_class = " ays_answer_caption ays_without_after_content ";
            }

            if($options['useHTML']){
                $answer_content = do_shortcode((stripslashes($answer["answer"])));
            }else{
                $answer_content = do_shortcode(htmlspecialchars(stripslashes($answer["answer"])));
            }

            $answer_container .= "
            <div class='ays-field ays_".$options['answersViewClass']."_view_item'>
                <input type='hidden' name='ays_answer_correct[]' value='{$answer["correct"]}'/>

                <input type='{$options["questionType"]}' name='ays_questions[ays-question-{$question_id}]' id='ays-answer-{$answer["id"]}-{$quiz_id}' value='{$answer["id"]}'/>

                    <label for='ays-answer-{$answer["id"]}-{$quiz_id}' class='$answer_label_class $answer_img_label_class'>
                        " . $answer_content . "
                    </label>
                    <label for='ays-answer-{$answer["id"]}-{$quiz_id}' class='ays_answer_image ays_empty_before_content'>{$answer_image}</label>

            </div>";

        }
        return $answer_container;
    }
}

?>
