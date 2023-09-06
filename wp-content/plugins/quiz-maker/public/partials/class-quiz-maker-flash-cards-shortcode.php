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
class Quiz_Maker_Flash_Cards
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

        add_shortcode('ays_quiz_flash_card', array($this, 'ays_quiz_flash_card_method'));

        $this->settings = new Quiz_Maker_Settings_Actions($this->plugin_name);
    }

    public function enqueue_scripts() {
        wp_enqueue_script( $this->plugin_name . '-flash-cards-public', AYS_QUIZ_PUBLIC_URL . '/js/flash-cards/flash-cards-public.js', array('jquery'), $this->version, true);
    }

    public function enqueue_styles(){
        wp_enqueue_style($this->plugin_name . '-animate-min', AYS_QUIZ_PUBLIC_URL . '/css/animate.css', array(), $this->version, 'all');
    }

    public function group_by($key, $data){
        $result = array();

        foreach($data as $k => $val) {
            if(array_key_exists($key, $val)){
                $result[$val[$key]][] = $val;
            }else{
                $result[""][] = $val;
            }
        }
        return $result;
    }

    public function ays_all_results_html($attr){
        global $wpdb;

        $quiz_settings = $this->settings;
        $quiz_settings_options = ($quiz_settings->ays_get_setting('options') === false) ? json_encode(array()) : $quiz_settings->ays_get_setting('options');
        $quiz_set_option = json_decode(stripcslashes($quiz_settings_options), true);

        $question_table =  $wpdb->prefix.'aysquiz_questions';
        $quizes_table = $wpdb->prefix.'aysquiz_quizes';
        $answer_table =  $wpdb->prefix.'aysquiz_answers';

        $ays_quiz_question_by = (isset($attr['by']) && $attr['by'] != '') ? sanitize_text_field($attr['by']) : 'quiz';
        $ays_quiz_flash_card_id = (isset($attr['id']) && $attr['id'] != '') ? sanitize_text_field($attr['id']) : '';

        //Flash Card Width
        $ays_quiz_flash_card_width = (isset($quiz_set_option['quiz_flash_card_width']) && $quiz_set_option['quiz_flash_card_width'] != '') ? intval($quiz_set_option['quiz_flash_card_width']) : '100%';

        //Flash Card Color
        $ays_quiz_flash_card_color = (isset($quiz_set_option['quiz_flash_card_color']) && $quiz_set_option['quiz_flash_card_color'] != '') ? $quiz_set_option['quiz_flash_card_color'] : '#ffffff';

        //Randomize Flash Card
        $quiz_set_option['quiz_flash_card_randomize'] = (isset($quiz_set_option['quiz_flash_card_randomize']) && $quiz_set_option['quiz_flash_card_randomize'] == 'on') ? sanitize_text_field($quiz_set_option['quiz_flash_card_randomize']) : 'off';
        $ays_quiz_flash_card_randomize = (isset($quiz_set_option['quiz_flash_card_randomize']) && $quiz_set_option['quiz_flash_card_randomize'] == 'on') ? true : false;

        //Flash Card Introduction
        $ays_quiz_flash_card_enable_introduction = (isset($quiz_set_option['quiz_flash_card_enable_introduction']) && $quiz_set_option['quiz_flash_card_enable_introduction'] == 'on') ? 'on' : 'off';

        $ays_quiz_flash_card_introduction_content = (isset($quiz_set_option['quiz_flash_card_introduction']) && $quiz_set_option['quiz_flash_card_introduction'] != '') ? Quiz_Maker_Data::ays_autoembed( htmlspecialchars_decode( $quiz_set_option['quiz_flash_card_introduction'] ) ) : '' ;

        $question_id_sql = "SELECT id,question_ids FROM {$quizes_table} WHERE id IN ({$ays_quiz_flash_card_id}) AND published = 1 GROUP BY id";
        $question_ids = $wpdb->get_results($question_id_sql,'ARRAY_A');

        $ays_question_id = array();
        foreach ($question_ids as $key => $question_id) {
            $ays_quest_id = (isset($question_id['question_ids']) && $question_id['question_ids'] != '') ? sanitize_text_field($question_id['question_ids']) : '';
            $ays_quiz_id = (isset($question_id['id']) && $question_id['id'] != '') ? sanitize_text_field($question_id['id']) : '';
            if( $ays_quest_id == '' ){
                continue;
            }
            $ays_question_id[$ays_quiz_id] = $ays_quest_id;
        }

        $fc_width = '';
        if($ays_quiz_flash_card_width !== '100%'){
            $fc_width = 'px';
        }else {
            $fc_width = '';
        }

        $content = '';

        $content .= '<style>';

            $content .= '.ays_quiz_flash_card_content{';
                $content .= 'width:'.$ays_quiz_flash_card_width.$fc_width .';';
                $content .= 'margin: auto;';
            $content .= '}';
            $content .= '.ays_quiz_flash_card, .ays_quiz_flash_card_introduction {';
                $content .= 'background-color:'.$ays_quiz_flash_card_color.'!important';
            $content .= '}';

        $content .= '</style>';

        $content .= '<div class="ays_quiz_flash_card_main_container">';
        $count = 0;
        switch($ays_quiz_question_by) {
            case 'category':
                $sql = "SELECT q.id,q.question,q.category_id,q.question_image,q.explanation,q.type,a.answer,a.image
                        FROM {$answer_table} AS a
                        LEFT JOIN {$question_table} AS q
                            ON a.question_id = q.id
                        WHERE q.category_id IN ({$ays_quiz_flash_card_id})
                            AND correct = 1
                        GROUP BY q.id
                        ORDER BY q.category_id";

                $results = $wpdb->get_results($sql,'ARRAY_A');

                $group_by_cats = $this->group_by('category_id',$results);

                if( !empty( $group_by_cats ) ){
                    foreach ($group_by_cats as $cat_key => $group_by_cat) {
                        $content .= '<div style="margin-bottom:20px;" class="ays_quiz_flash_card_content" data-index="0">';
                            $content .= '<div class="ays_quiz_flash_card_container_'.$cat_key.'">';
                                if($ays_quiz_flash_card_randomize){
                                    shuffle($group_by_cat);
                                }
                                foreach ($group_by_cat as $key => $result) {
                                    $question_count = count($group_by_cat);
                                    $count++;
                                    $ays_question_id                = (isset($result['id']) && $result['id'] != '') ? absint($result['id']) : null;
                                    $ays_quiz_question              = (isset($result['question']) && $result['question'] != '') ? Quiz_Maker_Data::ays_autoembed( $result['question'] ) : '';
                                    $ays_question_type              = (isset($result['type']) && $result['type'] != '') ? stripslashes( sanitize_text_field( $result['type'] ) ) : 'radio';
                                    if ( $ays_question_type == "checkbox" ) {
                                        $q_sql = "SELECT answer FROM {$answer_table} WHERE question_id = {$ays_question_id} AND correct = 1";
                                        $q_results = $wpdb->get_col($q_sql);
                                        if ( !empty( $q_results ) ) {
                                            $ays_quiz_answer = implode( ", " , $q_results);
                                        } else {
                                            $ays_quiz_answer = (isset($result['answer']) && $result['answer'] != '') ? stripslashes( $result['answer'] ) : '';
                                        }

                                    } else {
                                        $ays_quiz_answer = (isset($result['answer']) && $result['answer'] != '') ? stripslashes( $result['answer'] ) : '';
                                    }
                                    $ays_quiz_answer_image          = (isset($result['image']) && $result['image'] != '') ? $result['image'] : '';
                                    $ays_quiz_question_image        = (isset($result['question_image']) && $result['question_image'] != '') ? $result['question_image'] : '';
                                    $ays_quiz_question_explanation  = (isset($result['explanation']) && $result['explanation'] != '') ? Quiz_Maker_Data::ays_autoembed($result['explanation']) : '';

                                    $flag_introduction_class = "";
                                    if($ays_quiz_flash_card_enable_introduction === 'on' && $key == 0) {
                                        $flag_introduction_class = "display_none";
                                    }

                                    $content .= '<div class="ays_quiz_flash_card '. $flag_introduction_class .'">';
                                        $content .= '<a class="ays-quiz-flash-card-rotate">';
                                            $content .= '<image src="'.AYS_QUIZ_PUBLIC_URL.'/images/circle-of-two-clockwise-arrows-rotation.svg" class="ays-quiz-rotating-circular-arrow">';
                                        $content .= '</a>';
                                        $content .= '<div class="ays_quiz_front active">';
                                            $content .= '<div class="ays_quiz_fc_qa">';
                                                $content .= '<div>'.$ays_quiz_question.'</div>';
                                                if($ays_quiz_question_image != ''){
                                                    $content .= '<div class="ays_quiz_fc_img">';
                                                        $content .= '<img src="'.$ays_quiz_question_image.'" width="200" style="height: auto;">';
                                                    $content .= '</div>';
                                                }
                                            $content .= '</div>';

                                        $content .= '</div>';
                                        $content .= '<div class="ays_quiz_back" style="display: none;">';
                                            $content .= '<div class="ays_quiz_fc_qa">';
                                                $content .= '<div>'.$ays_quiz_answer.'</div>';
                                                if($ays_quiz_answer_image != ''){
                                                    $content .= '<div class="ays_quiz_fc_img">';
                                                        $content .= '<img src="'.$ays_quiz_answer_image.'" width="200" style="height: auto;">';
                                                    $content .= '</div>';
                                                }
                                            $content .= '</div>';
                                            if($ays_quiz_question_explanation != ''){
                                                $content .= '<div class="ays_quiz_fc_explanation">';
                                                    $content .= '<div style="text-align:left;margin:5px;">';
                                                            $content .= '<p style="font-weight:bold;">'. __( "Question Explanation:", $this->plugin_name ) .'</p>';
                                                            $content .= '<p>';
                                                                $content .= $ays_quiz_question_explanation;
                                                            $content .='</p>';
                                                    $content .= '</div>';
                                                $content .= '</div>';
                                            }
                                        $content .= '</div>';
                                        $content .= '<div class="ays_quiz_current_page">';
                                            $content .= '<span>'. ++$key .'/'. $question_count .'</span>';
                                        $content .= '</div>';
                                    $content .= '</div>';
                                }
                                if($ays_quiz_flash_card_enable_introduction === 'on') {
                                    $content .= '<div class="ays_quiz_flash_card_introduction">';
                                        $content .= '<div class="ays_quiz_flash_card_introduction_content">';
                                            $content .= $ays_quiz_flash_card_introduction_content;
                                        $content .= '</div>';
                                    $content .= '</div>';
                                }
                            $content .= '</div>';
                            if($ays_quiz_flash_card_enable_introduction === 'on') {
                                $content .= '<div class="ays_quiz_fc_start_btn_content">';
                                    $content .= '<a class="ays_quiz_fc_start_btn start">'. __( "Start", $this->plugin_name ) .'</a>';
                                $content .= '</div>';
                            }
                            if ($question_count != 1) {
                                $flag_introduction_class = "";
                                if($ays_quiz_flash_card_enable_introduction === 'on') {
                                    $flag_introduction_class = "display_none";
                                }

                                $content .= '<div class="ays_quiz_fc_next_btn_content '. $flag_introduction_class .'">';
                                    $content .= '<a class="ays_quiz_fc_next_prev_btn prev">'. __( "Prev", $this->plugin_name ) .'</a>';
                                    $content .= '<a class="ays_quiz_fc_next_prev_btn next">'. __( "Next", $this->plugin_name ) .'</a>';
                                    $content .= '<input type="hidden" value="'.$cat_key.'" class="quiz_id" />';
                                $content .= '</div>';
                            }
                        $content .= '</div>';
                    }
                }else{
                    $content .= '<div>'. __( 'There are no questions', $this->plugin_name ) .'</div>';
                }
            break;
            case 'quiz':
            default:
                if( !empty( $ays_question_id ) ){
                    foreach ($ays_question_id as $quiz_key => $ays_q_id) {
                        $sql = "SELECT q.id,q.question,q.question_image,q.explanation,q.type,a.answer,a.image
                                FROM {$answer_table} AS a
                                LEFT JOIN {$question_table} AS q
                                    ON a.question_id = q.id
                                WHERE q.id IN ({$ays_q_id})
                                    AND correct = 1
                                GROUP BY q.id";
                        $results = $wpdb->get_results($sql,'ARRAY_A');
                        $content .= '<div style="margin-bottom:20px;" class="ays_quiz_flash_card_content" data-index="0">';
                            $content .= '<div class="ays_quiz_flash_card_container_'.$quiz_key.'">';
                                if($ays_quiz_flash_card_randomize){
                                    shuffle($results);
                                }
                                foreach ($results as $key => $result) {
                                    $question_count = count($results);
                                    $count++;
                                    $ays_question_id                = (isset($result['id']) && $result['id'] != '') ? absint($result['id']) : null;
                                    $ays_quiz_question              = (isset($result['question']) && $result['question'] != '') ? Quiz_Maker_Data::ays_autoembed($result['question']) : '';
                                    $ays_question_type              = (isset($result['type']) && $result['type'] != '') ? stripslashes( sanitize_text_field( $result['type'] ) ) : 'radio';
                                    $ays_quiz_answer_image_html = "";
                                    if ( $ays_question_type == "checkbox" ) {
                                        $q_sql = "SELECT image,answer FROM {$answer_table} WHERE question_id = {$ays_question_id} AND correct = 1";
                                        $q_results = $wpdb->get_results($q_sql, "ARRAY_A");

                                        if ( !empty( $q_results ) ) {
                                            $ays_quiz_answer_arr = array();
                                            $ays_quiz_answer_img_arr = array();
                                            foreach ($q_results as $res_key => $res_data) {

                                                if(isset($res_data['answer']) && $res_data['answer'] != ''){
                                                    $ays_quiz_answer_arr[] = $res_data['answer'];
                                                }

                                                if(isset($res_data['image']) && $res_data['image'] != ''){
                                                    $ays_quiz_answer_img_arr[] = '
                                                    <div class="ays_quiz_fc_img">
                                                        <img src="'.$res_data['image'].'" width="200" style="height: auto;">
                                                    </div>';
                                                }
                                            }
                                            $ays_quiz_answer = implode( ", " , $ays_quiz_answer_arr);
                                            $ays_quiz_answer_image_html = implode( "" , $ays_quiz_answer_img_arr);
                                        } else {
                                            $ays_quiz_answer = (isset($result['answer']) && $result['answer'] != '') ? stripslashes( $result['answer'] ) : '';
                                            $ays_quiz_answer_image = (isset($result['image']) && $result['image'] != '') ? $result['image'] : '';

                                            if( $ays_quiz_answer_image != "" ){
                                                $ays_quiz_answer_image_html = '
                                                    <div class="ays_quiz_fc_img">
                                                        <img src="'.$ays_quiz_answer_image.'" width="200" style="height: auto;">
                                                    </div>';
                                            }
                                        }

                                    } else {
                                        $ays_quiz_answer = (isset($result['answer']) && $result['answer'] != '') ? stripslashes( $result['answer'] ) : '';
                                        $ays_quiz_answer_image = (isset($result['image']) && $result['image'] != '') ? $result['image'] : '';

                                        if( $ays_quiz_answer_image != "" ){
                                            $ays_quiz_answer_image_html = '
                                                <div class="ays_quiz_fc_img">
                                                    <img src="'.$ays_quiz_answer_image.'" width="200" style="height: auto;">
                                                </div>';
                                        }
                                    }
                                    $ays_quiz_question_image        = (isset($result['question_image']) && $result['question_image'] != '') ? $result['question_image'] : '';
                                    $ays_quiz_question_explanation  = (isset($result['explanation']) && $result['explanation'] != '') ? Quiz_Maker_Data::ays_autoembed($result['explanation']) : '';

                                    $flag_introduction_class = "";
                                    if($ays_quiz_flash_card_enable_introduction === 'on' && $key == 0) {
                                        $flag_introduction_class = "display_none";
                                    }

                                    $content .= '<div class="ays_quiz_flash_card '. $flag_introduction_class .'">';
                                        $content .= '<a class="ays-quiz-flash-card-rotate">';
                                            $content .= '<img src="'.AYS_QUIZ_PUBLIC_URL.'/images/circle-of-two-clockwise-arrows-rotation.svg" class="ays-quiz-rotating-circular-arrow">';
                                        $content .= '</a>';

                                        $content .= '<div class="ays_quiz_front active">';
                                            $content .= '<div class="ays_quiz_fc_qa">';
                                                $content .= '<p>'.$ays_quiz_question.'</p>';
                                                if($ays_quiz_question_image != ''){
                                                    $content .= '<div class="ays_quiz_fc_img">';
                                                        $content .= '<img src="'.$ays_quiz_question_image.'" width="200" style="height: auto;">';
                                                    $content .= '</div>';
                                                }
                                            $content .= '</div>';
                                        $content .= '</div>';
                                        $content .= '<div class="ays_quiz_back" style="display: none;">';
                                            $content .= '<div class="ays_quiz_fc_qa">';
                                                $content .= '<p>'.$ays_quiz_answer.'</p>';
                                                if($ays_quiz_answer_image_html != ''){
                                                    $content .= $ays_quiz_answer_image_html;
                                                }
                                                if($ays_quiz_question_explanation != ''){
                                                    $content .= '<div class="ays_quiz_fc_explanation">';
                                                        $content .= '<div style="text-align:left;margin:5px;">';
                                                                $content .= '<p style="font-weight:bold;">'. __( "Question Explanation:", $this->plugin_name ) .'</p>';
                                                                $content .= '<p>';
                                                                    $content .= $ays_quiz_question_explanation;
                                                                $content .='</p>';
                                                        $content .= '</div>';
                                                    $content .= '</div>';
                                                }
                                            $content .= '</div>';
                                        $content .= '</div>';
                                        $content .= '<div class="ays_quiz_current_page">';
                                            $content .= '<span>'. ++$key .'/'. $question_count .'</span>';
                                        $content .= '</div>';
                                    $content .= '</div>';
                                }
                                if($ays_quiz_flash_card_enable_introduction === 'on') {
                                    $content .= '<div class="ays_quiz_flash_card_introduction">';
                                        $content .= '<div class="ays_quiz_flash_card_introduction_content">';
                                            $content .= $ays_quiz_flash_card_introduction_content;
                                        $content .= '</div>';
                                    $content .= '</div>';
                                }
                            $content .= '</div>';
                            if($ays_quiz_flash_card_enable_introduction === 'on') {
                                $content .= '<div class="ays_quiz_fc_start_btn_content">';
                                    $content .= '<a class="ays_quiz_fc_start_btn start">'. __( "Start", $this->plugin_name ) .'</a>';
                                $content .= '</div>';
                            }
                            if ($question_count != 1) {

                                $flag_introduction_class = "";
                                if($ays_quiz_flash_card_enable_introduction === 'on') {
                                    $flag_introduction_class = "display_none";
                                }

                                $content .= '<div class="ays_quiz_fc_next_btn_content '. $flag_introduction_class .'">';
                                    $content .= '<a class="ays_quiz_fc_next_prev_btn prev">'. __( "Prev", $this->plugin_name ) .'</a>';
                                    $content .= '<a class="ays_quiz_fc_next_prev_btn next">'. __( "Next", $this->plugin_name ) .'</a>';
                                    $content .= '<input type="hidden" value="'.$quiz_key.'" class="quiz_id" />';
                                $content .= '</div>';
                            }
                        $content .= '</div>';
                    }
                }else{
                    $content .= '<div>'. __( 'There are no questions', $this->plugin_name ) .'</div>';
                }
            break;
        }
        $content .= '</div>';

        return $content;
    }

    public function ays_quiz_flash_card_method($attr) {

        $this->enqueue_scripts();
        $this->enqueue_styles();

        $all_results_html = $this->ays_all_results_html($attr);
        $all_results_html = Quiz_Maker_Data::ays_quiz_translate_content( $all_results_html );

        // echo $all_results_html;
        return str_replace(array("\r\n", "\n", "\r"), "\n", $all_results_html);
    }
}
