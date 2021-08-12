<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://ays-pro.com/
 * @since      1.0.0
 *
 * @package    Quiz_Maker
 * @subpackage Quiz_Maker/includes
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Quiz_Maker
 * @subpackage Quiz_Maker/includes
 * @author     AYS Pro LLC <info@ays-pro.com>
 */
class Quiz_Maker_Data {

    public static function get_quiz_by_id($id){
        global $wpdb;

        $sql = "SELECT *
                FROM {$wpdb->prefix}aysquiz_quizes
                WHERE id=" . $id;

        $quiz = $wpdb->get_row($sql, 'ARRAY_A');

        return $quiz;
    }

    public static function get_quiz_category_by_id($id){
        global $wpdb;

        $sql = "SELECT *
                FROM {$wpdb->prefix}aysquiz_quizcategories
                WHERE id=" . $id;

        $category = $wpdb->get_row($sql, 'ARRAY_A');

        return $category;
    }

    public static function get_question_category_by_id($id){
        global $wpdb;

        $sql = "SELECT *
                FROM {$wpdb->prefix}aysquiz_categories
                WHERE id=" . $id;

        $category = $wpdb->get_row($sql, 'ARRAY_A');

        return $category;
    }

    public static function get_quiz_tackers_count($id){
        global $wpdb;

        $sql = "SELECT COUNT(*)
                FROM {$wpdb->prefix}aysquiz_reports
                WHERE quiz_id=" . $id;

        $count = intval($wpdb->get_var($sql));

        return $count;
    }

    public static function get_quiz_results_count_by_id($id){
        global $wpdb;

        $sql = "SELECT COUNT(*) AS res_count
                FROM {$wpdb->prefix}aysquiz_reports
                WHERE quiz_id=". $id ." AND `status` = 'finished' ";

        $quiz = $wpdb->get_row($sql, 'ARRAY_A');

        return $quiz;
    }

//    public static function get_limit_user_count_by_id($quiz_id, $user_id){
//        global $wpdb;
//        $sql = "SELECT COUNT(*)
//                FROM `{$wpdb->prefix}aysquiz_reports`
//                WHERE `user_id` = $user_id
//                  AND `quiz_id` = $quiz_id";
//        $result = intval($wpdb->get_var($sql));
//        return $result;
//    }
//
//    public static function get_limit_user_count_by_ip($id){
//        global $wpdb;
//        $user_ip = self::get_user_ip();
//        $sql = "SELECT COUNT(*)
//                FROM `{$wpdb->prefix}aysquiz_reports`
//                WHERE `user_ip` = '$user_ip'
//                  AND `quiz_id` = $id";
//        $result = $wpdb->get_var($sql);
//        return $result;
//    }

    public static function get_quiz_attributes_by_id($id, $array_a = false){
        global $wpdb;
        $quiz = self::get_quiz_by_id($id);
        $options = json_decode($quiz['options']);
        $quiz_attrs = isset($options->quiz_attributes) ? $options->quiz_attributes : array();
        $quiz_attributes = implode(',', $quiz_attrs);
        if (!empty($quiz_attributes)) {
            $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_attributes WHERE `id` in ($quiz_attributes) AND published = 1";
            if($array_a){
                $results = $wpdb->get_results($sql, "ARRAY_A");
            }else{
                $results = $wpdb->get_results($sql);
            }
            return $results;
        }
        return array();
    }

    public static function get_quiz_all_attributes(){
        global $wpdb;
        $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_attributes";
        $result = $wpdb->get_results($sql,'ARRAY_A');
        return $result;
    }

    public static function get_quiz_question_by_id($id){

        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_questions WHERE id = " . $id;

        $results = $wpdb->get_row($sql, "ARRAY_A");

        return $results;

    }

    public static function get_quiz_questions_by_ids($ids){

        global $wpdb;

        $results = array();
        if(!empty($ids)){
            $ids = implode(",", $ids);
            $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_questions WHERE id IN (" . $ids . ")";

            $results = $wpdb->get_results($sql, "ARRAY_A");

        }

        return $results;

    }

    public static function get_answers_with_question_id($id){
        global $wpdb;

        $sql = "SELECT *
                FROM {$wpdb->prefix}aysquiz_answers
                WHERE question_id=" . $id . "
                ORDER BY ordering";

        $answer = $wpdb->get_results($sql, 'ARRAY_A');

        return $answer;
    }

    public static function get_quiz_questions_count($id){
        global $wpdb;

        $sql = "SELECT `question_ids`
                FROM {$wpdb->prefix}aysquiz_quizes
                WHERE id=" . $id;

        $questions_str = $wpdb->get_row($sql, 'ARRAY_A');
        $questions = explode(',', $questions_str['question_ids']);
        return $questions;
    }

    public static function sort_array_keys_by_array($array, $orderArray) {
        $ordered = array();
        foreach ($orderArray as $key) {
            if (array_key_exists('ays-question-'.$key, $array)) {
                $ordered['ays-question-'.$key] = $array['ays-question-'.$key];
                unset($array['ays-question-'.$key]);
            }
        }
        return $ordered + $array;
    }

    public static function sort_array_keys_by_array_for_id_keys($array, $orderArray) {
        $ordered = array();
        foreach ($orderArray as $key) {
            if (array_key_exists($key, $array)) {
                $ordered[$key] = $array[$key];
                unset($array[$key]);
            }
        }
        return $ordered + $array;
    }

    public static function replace_message_variables($content, $data){
        foreach($data as $variable => $value){
            $content = str_replace("%%".$variable."%%", $value, $content);
        }
        return $content;
    }

    public static function get_answers_max_weight($question_id, $has_multiple){
        global $wpdb;
        $answers_table = $wpdb->prefix . "aysquiz_answers";
        $question_id = absint(intval($question_id));
        $answer_id = absint(intval($answer_id));
        $query_part = "";
        $sql = "SELECT MAX(weight) FROM {$answers_table} WHERE question_id={$question_id}";
        if($has_multiple){
            $sql = "SELECT SUM(weight) FROM {$answers_table} WHERE question_id={$question_id} AND weight > 0";
        }
        $checks = $wpdb->get_var($sql);
        $answer_weight = floatval($checks);

        return $answer_weight;
    }

    public static function ays_report_mail_content($last_results, $where, $send_results){
        global $wpdb;
        $questions_table = $wpdb->prefix . "aysquiz_questions";
        $send = null;
        $send_info = null;

        if($where == 'admin' && $send_results === null){
            $send = false;
            $send_info = true;
        }elseif($where == 'user' && $send_results){
            $send = true;
            $send_info = true;
        }elseif($where == 'user' && !$send_results){
            $send = false;
            $send_info = false;
        }elseif($where == 'admin' && $send_results){
            $send = true;
            $send_info = true;
        }elseif($where == 'admin' && !$send_results){
            $send = false;
            $send_info = true;
        }

        $last_result = $last_results;
        $data_result = $last_results['answered'];

        $quiz_calc_method = $last_results['calc_method'];
        $all_quiz_points = $last_results['max_points'];
        $user_points_score = $last_results['answered']['correctness'];
        $user_points_scored = $last_results['user_points'];

        $duration = self::get_time_difference($last_result['start_date'], $last_result['end_date']);

        $result_attributes = $last_results['attributes_information'];

        $last_result['user_name'] = empty($last_result['user_name']) || $last_result['user_name'] == '' ? '' : $last_result['user_name'];

        $last_result['user_email'] = empty($last_result['user_email']) || $last_result['user_email'] == '' ? '' : $last_result['user_email'];

        $last_result['user_phone'] = empty($last_result['user_phone']) || $last_result['user_phone'] == '' ? '' : $last_result['user_phone'];

        $ays_rtl_styles = '';
        $td_value_html = '';
        if($send_info){
            if ($last_result['user_name'] != '') {
                $td_value_html .= "
                <tr>
                    <td style='font-weight: 600; border: 1px solid #ccc;padding: 10px 11px 9px 6px;'>".__('Name', AYS_QUIZ_NAME)."</td>
                    <td style='border: 1px solid #ccc;text-align: center;padding: 10px 11px 9px 6px;' colspan='3'>" . $last_result['user_name'] . "</td>
                </tr>";
            }

            if ($last_result['user_email'] != '') {
                $td_value_html .= "
                <tr>
                    <td style='font-weight: 600; border: 1px solid #ccc;padding: 10px 11px 9px 6px;'>".__('Email', AYS_QUIZ_NAME)."</td>
                    <td style='border: 1px solid #ccc;text-align: center;padding: 10px 11px 9px 6px;' colspan='3'>" . $last_result['user_email'] . "</td>
                </tr>";
            }

            if ($last_result['user_phone'] != '') {
                $td_value_html .= "
                <tr>
                    <td style='font-weight: 600; border: 1px solid #ccc;padding: 10px 11px 9px 6px;'>".__('Phone', AYS_QUIZ_NAME)."</td>
                    <td style='border: 1px solid #ccc;text-align: center;padding: 10px 11px 9px 6px;' colspan='3'>" . $last_result['user_phone'] . "</td>
                </tr>";
            }

            foreach ($result_attributes as $attribute => $value) {
                $value = empty($value) || $value == '' ? ' - ' : $value;
                $td_value_html .= "<tr><td style='font-weight: 600; border: 1px solid #ccc;padding: 10px 11px 9px 6px;'>" . $attribute . "</td><td style='border: 1px solid #ccc;text-align: center;padding: 10px 11px 9px 6px;' colspan='3'>" . $value . "</td></tr>";
            }

            $td_value_html .= " <tr>
                    <td style='font-weight: 600; border: 1px solid #ccc;padding: 10px 11px 9px 6px;'>".__('Duration', AYS_QUIZ_NAME)."</td>
                    <td style='border: 1px solid #ccc;text-align: center;padding: 10px 11px 9px 6px;' colspan='3'>" . $duration . " </td>
               </tr>";
        }
        if ($quiz_calc_method == 'by_correctness') {

            if($send_info){
                $td_value_html .= " <tr>
                        <td style='font-weight: 600; border: 1px solid #ccc;padding: 10px 11px 9px 6px;'>".__('Score', AYS_QUIZ_NAME)."</td>
                        <td style='border: 1px solid #ccc;text-align: center;padding: 10px 11px 9px 6px;' colspan='3'>" . $last_result['score'] . " %</td>
                   </tr>";
            }
            if($send){
                $index = 1;
                foreach ($data_result['correctness'] as $key => $option) {
                    if (strpos($key, 'question_id_') !== false) {
                        $question_id = absint(intval(explode('_', $key)[2]));
                        $question = $wpdb->get_row("SELECT * FROM {$questions_table} WHERE id={$question_id}", "ARRAY_A");
                        $qoptions = isset($question['options']) && $question['options'] != '' ? json_decode($question['options'], true) : array();
                        $use_html = isset($qoptions['use_html']) && $qoptions['use_html'] == 'on' ? true : false;
                        $correct_answers = self::get_correct_answers($question_id);

                        $is_text_type = self::question_is_text_type($question_id);
                        $text_type = self::text_answer_is($question_id);
                        $not_multiple_text_types = array("number", "date");

                        if($is_text_type){
                            $user_answered = self::get_user_text_answered((object)$data_result['user_answered'], $key);
                        }else{
                            $user_answered = self::get_user_answered((object)$data_result['user_answered'], $key);
                        }

                        $not_influence_to_score = isset($question['not_influence_to_score']) && $question['not_influence_to_score'] == 'on' ? true : false;
                        if ( $not_influence_to_score ) {
                            $not_influance_check_td = ' colspan="2" ';
                        }else{
                            $not_influance_check_td = '';
                        }

                        if(is_array($user_answered)){
                            $user_answered = $user_answered['message'];
                        }

                        $td_value_html .= '<tr>
                            <td style="border: 1px solid #ccc;padding: 10px 11px 9px 6px;">
                                <strong>'.__('Question',AYS_QUIZ_NAME).' ' . $index . ':</strong>
                                <br/>' . (strip_shortcodes(stripslashes($question["question"]))) . '
                            </td>';

                        $status_class = 'error';
                        $correct_answers_status_class = 'success';
                        if ($option == true) {
                            $status_class = 'success';
                        }

                        if ($not_influence_to_score) {
                            $status_class = 'no_status';
                            $correct_answers_status_class = 'no_status';
                        }

                        if($is_text_type && ! in_array($text_type, $not_multiple_text_types)){
                            $c_answers = explode('%%%', $correct_answers);
                            $c_answer = $c_answers[0];
                            foreach($c_answers as $c_ans){
                                if(strtolower(trim($user_answered)) == strtolower(trim($c_ans))){
                                    $c_answer = $c_ans;
                                    break;
                                }
                            }
                            $td_value_html .= '<td style="border: 1px solid #ccc;padding: 10px 11px 9px 6px;"><strong>'.__('Correct answer',AYS_QUIZ_NAME).':</strong><br/>';
                            $td_value_html .= '<p class="success">' . htmlentities(do_shortcode(stripslashes($c_answer))) . '<br></p>';
                            $td_value_html .= '</td>';
                        }else{
                            if($text_type == 'date'){
                                $correct_answers = date( 'm/d/Y', strtotime( $correct_answers ) );
                            }
                            $correct_answer_content = htmlentities( stripslashes( $correct_answers ) );
                            if($use_html){
                                $correct_answer_content = stripslashes( $correct_answers );
                            }

                            $td_value_html .= '<td style="border: 1px solid #ccc;padding: 10px 11px 9px 6px;"><strong>'.__('Correct answer',AYS_QUIZ_NAME).':</strong><br/>
                                <p class="'.$correct_answers_status_class.'">' . $correct_answer_content . '<br></p>
                            </td>';
                        }

                        if($text_type == 'date'){
                            if(Quiz_Maker_Admin::validateDate($user_answered, 'Y-m-d')){
                                $user_answered = date( 'm/d/Y', strtotime( $user_answered ) );
                            }
                        }
                        $user_answer_content = htmlentities( stripslashes( $user_answered ) );
                        if($use_html){
                            $user_answer_content = stripslashes( $user_answered );
                        }

                        $td_value_html .= '<td style="border: 1px solid #ccc;padding: 10px 11px 9px 6px;" '.$not_influance_check_td.'><strong>'.__('User answered',AYS_QUIZ_NAME).':</strong><br/>
                            <p class="'.$status_class.'">' . $user_answer_content . '</p>
                        </td>';

                        if (! $not_influence_to_score) {
                            if ($option == true) {
                                $td_value_html .= '<td style="border: 1px solid #ccc;padding: 10px 11px 9px 6px;">
                                    <p class="success" style="font-weight: 600; color:green;">'.__('Success',AYS_QUIZ_NAME).'!</p>
                                </td>';
                            } else {
                                $td_value_html .= '<td style="border: 1px solid #ccc;padding: 10px 11px 9px 6px;">
                                    <p class="error" style="font-weight: 600; color:red;">'.__('Fail',AYS_QUIZ_NAME).'!</p>
                                </td>';
                            }
                        }

                        $td_value_html .= '</tr>';

                        if(isset($question['explanation']) && $question['explanation'] != ''){
                            $td_value_html .= '<tr>
                                <td style="border: 1px solid #ccc;padding: 10px 11px 9px 6px;"><strong>'.__('Question',AYS_QUIZ_NAME).' '. $index .' '. __('explanation',AYS_QUIZ_NAME) .':</strong></td>
                                <td colspan="3" style="border: 1px solid #ccc;padding: 10px 11px 9px 6px;"><p>' . (do_shortcode(stripslashes($question["explanation"]))) . '</p></td>
                            </tr>';
                        }

                        $index++;
                    }
                }
            }
        }elseif($quiz_calc_method == 'by_points'){

            if($send_info){
                $td_value_html .= " <tr>
                        <td style='font-weight: 600; border: 1px solid #ccc;padding: 10px 11px 9px 6px;'>".__('Score', AYS_QUIZ_NAME)."</td>
                        <td style='border: 1px solid #ccc;text-align: center;padding: 10px 11px 9px 6px;' colspan='2'> ".$user_points_scored." / " . $all_quiz_points . " </td>
                   </tr>";
            }
            if($send){
                $index = 1;
                foreach ($data_result['correctness'] as $key => $option) {
                    if (strpos($key, 'question_id_') !== false) {
                        $question_id = absint(intval(explode('_', $key)[2]));
                        $question = $wpdb->get_row("SELECT * FROM {$questions_table} WHERE id={$question_id}", "ARRAY_A");
                        $correct_answers = self::get_correct_answers($question_id);

                        $is_text_type = self::question_is_text_type($question_id);
                        $text_type = self::text_answer_is($question_id);
                        $not_multiple_text_types = array("number", "date");

                        if($is_text_type){
                            $user_answered = self::get_user_text_answered((object)$data_result['user_answered'], $key);
                        }else{
                            $user_answered = self::get_user_answered((object)$data_result['user_answered'], $key);
                        }

                        $ans_point = $option;
                        $ans_point_class = 'success';
                        if(is_array($user_answered)){
                            $user_answered = $user_answered['message'];
                            $ans_point = '-';
                            $ans_point_class = 'error';
                        }

                        $td_value_html .= '<tr>
                            <td style="border: 1px solid #ccc;padding: 10px 11px 9px 6px;"><strong>'.__('Question',AYS_QUIZ_NAME).' ' . $index . ':</strong><br/>' . (do_shortcode(stripslashes($question["question"]))) . '</td>
                            <td style="border: 1px solid #ccc;padding: 10px 11px 9px 6px;"><strong>'.__('User answer',AYS_QUIZ_NAME).':</strong><br/><p class="'.$ans_point_class.'">' . htmlentities(do_shortcode(stripslashes($user_answered))) . '</p></td>
                            <td style="border: 1px solid #ccc;padding: 10px 11px 9px 6px;"><strong>'.__('Point',AYS_QUIZ_NAME).':</strong><br/><p class="'.$ans_point_class.'" style="font-weight: 600; text-align:center;">'.$ans_point.'</p></td>
                        </tr>';

                        if(isset($question['explanation']) && $question['explanation'] != ''){
                            $td_value_html .= '<tr>
                                <td style="border: 1px solid #ccc;padding: 10px 11px 9px 6px;"><strong>'.__('Question',AYS_QUIZ_NAME).' '. $index .' '. __('explanation',AYS_QUIZ_NAME) .' :</strong></td>
                                <td colspan="3" style="border: 1px solid #ccc;padding: 10px 11px 9px 6px;"><p>' . (do_shortcode(stripslashes($question["explanation"]))) . '</p></td>
                            </tr>';
                        }
                        $index++;
                    }
                }
            }
        }

        $ays_rtl_flag = false;
        if ($last_results['rtl_direction'] == 'on') {
            $ays_rtl_styles .= 'text-align:right; direction:rtl;';
            $ays_rtl_flag = true;
        }else{
            $ays_rtl_styles .= '';
        }

        if ($last_results['rtl_direction'] == 'on' && ! $ays_rtl_flag) {
            $ays_rtl_styles .= 'text-align:right; direction:rtl;';
        }else{
            $ays_rtl_styles .= '';
        }

        $message_content = '<!doctype html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
            <meta http-equiv="X-UA-Compatible" content="ie=edge">
            <title>Document</title>
        </head>
        <body style="' . $ays_rtl_styles . '">
            <div>
                <h1>%%quiz_title%%</h1>
                <table style="border-collapse: collapse; width: 100%;">
                        %%attribute_values%%
                </table>
            </div>
        </body>
        </html>';

        $message_content = str_replace('%%quiz_title%%', stripslashes($quiz['title']), $message_content);
        $message_content = str_replace('%%attribute_values%%', $td_value_html, $message_content);

        return $message_content;
    }

    public static function get_user_answered($user_choice, $key){
        global $wpdb;
        $answers_table = $wpdb->prefix . "aysquiz_answers";
        $choices = $user_choice->$key;

        if($choices == ''){
            return array(
                'message' => __( "The question was not answered.", AYS_QUIZ_NAME ),
                'status' => false
            );
        }

        $text = array();
        if (is_array($choices)) {
            foreach ($choices as $choice) {
                $result = $wpdb->get_row("SELECT answer FROM {$answers_table} WHERE id={$choice}", 'ARRAY_A');
                $text[] = $result['answer'];
            }
            $text = implode(', ', $text);
        } else {
            if ($choices == '')  $choices = 0;
            $result = $wpdb->get_row("SELECT answer FROM {$answers_table} WHERE id={$choices}", 'ARRAY_A');
            $text = $result['answer'];
        }
        return $text;
    }

    public static function get_user_answered_images($user_choice, $key){
        global $wpdb;
        $answers_table = $wpdb->prefix . "aysquiz_answers";
        $choices = $user_choice->$key;

        if($choices == ''){
            return '';
        }

        $text = array();
        if (is_array($choices)) {
            foreach ($choices as $choice) {
                $result = $wpdb->get_row("SELECT image FROM {$answers_table} WHERE id={$choice}", 'ARRAY_A');
                if(isset($result['image']) && $result['image'] != ''){
                    $text[] = "<img src='". $result['image'] ."' alt='Answer image'>";
                }
            }
            $text = '<br>' . implode('<br>', $text);
        } else {
            $result = $wpdb->get_row("SELECT image FROM {$answers_table} WHERE id={$choices}", 'ARRAY_A');
            if(isset($result['image']) && $result['image'] != ''){
                $text = "<br><img src='". $result['image'] ."' alt='Answer image'>";
            }else{
                $text = '';
            }
        }
        return $text;
    }

    public static function get_user_text_answered($user_choice, $key){
        if($user_choice->$key == ""){
            $choices = __( "The user has not answered this question.", AYS_QUIZ_NAME );
        }else{
            $choices = trim($user_choice->$key);
        }

        return $choices;
    }

    public static function get_correct_answers($id){
        global $wpdb;
        $answers_table = $wpdb->prefix . "aysquiz_answers";
        $correct_answers = $wpdb->get_results("SELECT answer FROM {$answers_table} WHERE correct=1 AND question_id={$id}");
        $text = "";
        foreach ($correct_answers as $key => $correct_answer) {
            if ($key == (count($correct_answers) - 1))
                $text .= $correct_answer->answer;
            else
                $text .= $correct_answer->answer . ',';
        }
        return $text;
    }

    public static function get_correct_answer_images($id){
        global $wpdb;
        $answers_table = $wpdb->prefix . "aysquiz_answers";
        $correct_answers = $wpdb->get_results("SELECT image FROM {$answers_table} WHERE correct=1 AND question_id={$id}");
        $text = "";
        foreach ($correct_answers as $key => $correct_answer) {
            if ($correct_answer->image){
                $text .= "<img src='". $correct_answer->image ."' alt='Answer image'>";
            }
        }
        return $text;
    }

    public static function get_correct_answer_keyword($question_id, $answer_id){
        global $wpdb;
        $answers_table = $wpdb->prefix . "aysquiz_answers";

        $sql = "SELECT keyword FROM {$answers_table} WHERE question_id={$question_id} AND id={$answer_id}";
        $answered_keyword = $wpdb->get_var($sql);

        if (is_null($answered_keyword)) {
            return 'A';
        }

        return $answered_keyword;
    }

    public static function check_answer_correctness($question_id, $answer_id, $calc_method){
        global $wpdb;
        $answers_table = $wpdb->prefix . "aysquiz_answers";
        $question_id = absint(intval($question_id));
        $answer_id = absint(intval($answer_id));
        $checks = $wpdb->get_row("SELECT * FROM {$answers_table} WHERE question_id={$question_id} AND id={$answer_id}", "ARRAY_A");
        $answer_weight = floatval($checks['weight']);
        $answer = false;
        switch($calc_method){
            case "by_correctness":
                if (absint(intval($checks["correct"])) == 1)
                    $answer = true;
                else
                    $answer = false;
            break;
            case "by_points":
                $answer = $answer_weight;
            break;
            default:
                if (absint(intval($checks["correct"])) == 1)
                    $answer = true;
                else
                    $answer = false;
            break;
        }

        return $answer;
    }

    public static function check_text_answer_correctness($question_id, $answer, $calc_method){
        global $wpdb;
        $answers_table = $wpdb->prefix . "aysquiz_answers";
        $question_id = absint(intval($question_id));
        $checks = $wpdb->get_row("SELECT COUNT(*) AS qanak, answer, weight FROM {$answers_table} WHERE question_id={$question_id}", ARRAY_A);
        $correct_answers = $checks['answer'];
        $answer_weight = floatval($checks['weight']);
        $answer_res = false;
        $text_type = self::text_answer_is($question_id);
        $correct = false;
        if($text_type == 'date'){
            // if(Quiz_Maker_Admin::validateDate($answer, 'Y-m-d')){
            if(date('Y-m-d', strtotime($correct_answers)) == date('Y-m-d', strtotime($answer))){
                $correct = true;
            }
            // }
        }elseif($text_type != 'number'){
            $correct_answers = explode('%%%', $correct_answers);
            foreach($correct_answers as $c){
                if(mb_strtolower(trim($c), 'UTF-8') == mb_strtolower(trim($answer), 'UTF-8')){
                    $correct = true;
                    break;
                }
            }
        }else{
            if($correct_answers == strtolower(trim($answer))){
                $correct = true;
            }
        }

        switch($calc_method){
            case "by_correctness":
                if($correct)
                    $answer_res = true;
                else
                    $answer_res = false;
            break;
            case "by_points":
                if($correct)
                    $answer_res = $answer_weight;
                else
                    $answer_res = 0;
            break;
            default:
                if($correct)
                    $answer_res = true;
                else
                    $answer_res = false;
            break;
        }
        return $answer_res;
    }

    public static function count_multiple_correct_answers($question_id){
        global $wpdb;
        $answers_table = $wpdb->prefix . "aysquiz_answers";
        $question_id = absint(intval($question_id));

        $get_answers = $wpdb->get_var("SELECT COUNT(*) FROM {$answers_table} WHERE question_id={$question_id} AND correct=1");
        return $get_answers;
    }

    public static function has_multiple_correct_answers($question_id){
        global $wpdb;
        $answers_table = $wpdb->prefix . "aysquiz_answers";
        $question_id = absint(intval($question_id));

        $get_answers = $wpdb->get_var("SELECT COUNT(*) FROM {$answers_table} WHERE question_id={$question_id} AND correct=1");

        if (intval($get_answers) > 1) {
            return true;
        }
        return false;
    }

    public static function has_text_answer($question_id){
        global $wpdb;
        $questions_table = $wpdb->prefix . "aysquiz_questions";
        $question_id = absint(intval($question_id));
        $text_types = array('text', 'short_text', 'number', 'date');
        $get_answers = $wpdb->get_var("SELECT type FROM {$questions_table} WHERE id={$question_id}");
        if (in_array($get_answers, $text_types)) {
            return true;
        }
        return false;
    }

    public static function is_checkbox_answer($question_id){
        global $wpdb;
        $questions_table = $wpdb->prefix . "aysquiz_questions";
        $question_id = absint(intval($question_id));
        $get_answers = $wpdb->get_var("SELECT type FROM {$questions_table} WHERE id={$question_id}");
        if ($get_answers == 'checkbox') {
            return true;
        }
        return false;
    }

    public static function is_question_not_influence($question_id){
        global $wpdb;
        $questions_table = $wpdb->prefix . "aysquiz_questions";
        $question_id = absint(intval($question_id));

        $question = $wpdb->get_row("SELECT * FROM {$questions_table} WHERE id={$question_id};", "ARRAY_A");
        $question['not_influence_to_score'] = ! isset($question['not_influence_to_score']) ? 'off' : $question['not_influence_to_score'];
        if(isset($question['not_influence_to_score']) && $question['not_influence_to_score'] == 'on'){
            return true;
        }
        return false;
    }

    public static function is_question_type_a_custom($question_id){
        global $wpdb;
        $questions_table = $wpdb->prefix . "aysquiz_questions";
        $question_id = absint(intval($question_id));
        $custom_types = array("custom");
        $question_type = $wpdb->get_var("SELECT type FROM {$questions_table} WHERE id={$question_id};");
        if($question_type == ''){
            $question_type = 'radio';
        }

        if(in_array($question_type, $custom_types)){
            return true;
        }
        return false;
    }

    public static function in_question_use_html($question_id){
        global $wpdb;
        $questions_table = $wpdb->prefix . "aysquiz_questions";
        $question_id = absint(intval($question_id));

        $question = $wpdb->get_row("SELECT * FROM {$questions_table} WHERE id={$question_id};", "ARRAY_A");
        $options = ! isset($question['options']) ? array() : json_decode($question['options'], true);
        if(isset($options['use_html']) && $options['use_html'] == 'on'){
            return true;
        }
        return false;
    }

    public static function text_answer_is($question_id){
        global $wpdb;
        $questions_table = $wpdb->prefix . "aysquiz_questions";
        $question_id = absint(intval($question_id));

        $text_types = array('text', 'short_text', 'number', 'date');
        $get_answers = $wpdb->get_var("SELECT type FROM {$questions_table} WHERE id={$question_id}");

        if (in_array($get_answers, $text_types)) {
            return $get_answers;
        }
        return false;
    }

    public static function question_is_text_type($question_id){
        global $wpdb;
        $questions_table = $wpdb->prefix . "aysquiz_questions";
        $question_id = absint(intval($question_id));
        $text_types = array('text', 'number', 'short_text', 'date');
        $get_answers = $wpdb->get_var("SELECT type FROM {$questions_table} WHERE id={$question_id}");
        if (in_array($get_answers, $text_types)) {
            return true;
        }
        return false;
    }

    public static function ays_get_image_thumbnauil($ans_img){
        global $wpdb;
        $query = "SELECT * FROM `".$wpdb->prefix."posts` WHERE `post_type` = 'attachment' AND `guid` = '".$ans_img."'";
        $result_img =  $wpdb->get_row( $query, "ARRAY_A" );
        $url_img = wp_get_attachment_image_src($result_img['ID'], 'medium');
        if($url_img === false){
           $new_img = $ans_img;
        }else{
           $new_img = $url_img[0];
        }
        return $new_img;
    }

    public static function get_question_weight($id){
        global $wpdb;
        $sql = "SELECT weight FROM {$wpdb->prefix}aysquiz_questions WHERE id = $id";
        $result = $wpdb->get_var($sql);
        return floatval($result);
    }

    public static function hex2rgba($color, $opacity = false){

        $default = 'rgb(0,0,0)';

        //Return default if no color provided
        if (empty($color))
            return $default;

        //Sanitize $color if "#" is provided
        if ($color[0] == '#') {
            $color = substr($color, 1);
        }else{
            return $color;
        }

        //Check if color has 6 or 3 characters and get values
        if (strlen($color) == 6) {
            $hex = array($color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5]);
        } elseif (strlen($color) == 3) {
            $hex = array($color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2]);
        } else {
            return $default;
        }

        //Convert hexadec to rgb
        $rgb = array_map('hexdec', $hex);

        //Check if opacity is set(rgba or rgb)
        if ($opacity) {
            if (abs($opacity) > 1)
                $opacity = 1.0;
            $output = 'rgba(' . implode(",", $rgb) . ',' . $opacity . ')';
        } else {
            $output = 'rgb(' . implode(",", $rgb) . ')';
        }

        //Return rgb(a) color string
        return $output;
    }

    public static function secondsToWords($seconds){
        $ret = "";

        /*** get the days ***/
        $days = intval(intval($seconds) / (3600 * 24));
        if ($days > 0) {
            $ret .= "$days days ";
        }

        /*** get the hours ***/
        $hours = (intval($seconds) / 3600) % 24;
        if ($hours > 0) {
            $ret .= "$hours hours ";
        }

        /*** get the minutes ***/
        $minutes = (intval($seconds) / 60) % 60;
        if ($minutes > 0) {
            $ret .= "$minutes minutes ";
        }

        /*** get the seconds ***/
        $seconds = intval($seconds) % 60;
        if ($seconds > 0) {
            $ret .= "$seconds seconds";
        }

        return $ret;
    }

    public static function ays_get_count_of_rates($id){
        global $wpdb;
        $sql = "SELECT COUNT(`id`) AS count FROM {$wpdb->prefix}aysquiz_rates WHERE quiz_id= $id";
        $result = $wpdb->get_var($sql);
        return $result;
    }

    public static function ays_get_count_of_reviews($start, $limit, $quiz_id){
        global $wpdb;
        $sql = "SELECT COUNT(`id`) AS count FROM {$wpdb->prefix}aysquiz_rates WHERE (review<>'' OR options<>'') AND quiz_id = $quiz_id ORDER BY id DESC LIMIT $start, $limit";
        $result = $wpdb->get_var($sql);
        return $result;
    }

    public static function ays_set_rate_id_of_result($id, $last_result_id ){
        global $wpdb;
        $results_table = $wpdb->prefix . 'aysquiz_reports';
        $sql = "SELECT * FROM $results_table WHERE id = ".intval($last_result_id);
        $report_result = $wpdb->get_row($sql, ARRAY_A);

        $options = json_decode($report_result['options'], true);
        $options['rate_id'] = $id;
        $results = $wpdb->update(
            $results_table,
            array( 'options' => json_encode($options) ),
            array( 'id' => intval($last_result_id) ),
            array( '%s' ),
            array( '%d' )
        );
        if($results !== false){
            return true;
        }
        return false;
    }

    public static function ays_get_average_of_scores($id){
        global $wpdb;
        $sql = "SELECT AVG(`score`) FROM {$wpdb->prefix}aysquiz_reports WHERE quiz_id= $id";
        $result = round($wpdb->get_var($sql));
        return $result;
    }

    public static function ays_get_average_of_rates($id){
        global $wpdb;
        $sql = "SELECT AVG(`score`) AS avg_score FROM {$wpdb->prefix}aysquiz_rates WHERE quiz_id= $id";
        $result = $wpdb->get_var($sql);
        return $result;
    }

    public static function ays_get_reasons_of_rates($start, $limit, $quiz_id){
        global $wpdb;
        $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_rates WHERE quiz_id=$quiz_id AND (review<>'' OR options<>'') ORDER BY id DESC LIMIT $start, $limit";
        $result = $wpdb->get_results($sql, "ARRAY_A");
        return $result;
    }

    public static function ays_get_full_reasons_of_rates($start, $limit, $quiz_id, $zuyga){
        $quiz_rate_reasons = self::ays_get_reasons_of_rates($start, $limit, $quiz_id);
        $quiz_rate_html = "";
        foreach($quiz_rate_reasons as $key => $reasons){
            $user_name = !empty($reasons['user_name']) ? "<span>".$reasons['user_name']."</span>" : '';
            $reason = $reasons['review'];
            if(intval($reasons['user_id']) != 0){
                $user_img = esc_url( get_avatar_url( intval($reasons['user_id']) ) );
            }else{
                $user_img = "https://ssl.gstatic.com/accounts/ui/avatar_2x.png";
            }
            $score = $reasons['score'];
            $commented = date('M j, Y', strtotime($reasons['rate_date']));
            if($zuyga == 1){
                $row_reverse = ($key % 2 == 0) ? 'row_reverse' : '';
            }else{
                $row_reverse = ($key % 2 == 0) ? '' : 'row_reverse';
            }
            $quiz_rate_html .= "<div class='quiz_rate_reasons'>
                  <div class='rate_comment_row $row_reverse'>
                    <div class='rate_comment_user'>
                        <div class='thumbnail'>
                            <img class='img-responsive user-photo' src='".$user_img."'>
                        </div>
                    </div>
                    <div class='rate_comment'>
                        <div class='panel panel-default'>
                            <div class='panel-heading'>
                                <i class='ays_fa ays_fa_user'></i> <strong>$user_name</strong><br/>
                                <i class='ays_fa ays_fa_clock_o'></i> $commented<br/>
                                ".__("Rated", AYS_QUIZ_NAME)." <i class='ays_fa ays_fa_star'></i> $score
                            </div>
                            <div class='panel-body'><div>". stripslashes($reason) ."</div></div>
                        </div>
                    </div>
                </div>
            </div>";
        }
        return $quiz_rate_html;
    }

    public static function get_user_by_ip($id, $quiz_pass_score){
        global $wpdb;
        $user_ip = self::get_user_ip();
        $sql = "SELECT COUNT(*)
                FROM `{$wpdb->prefix}aysquiz_reports`
                WHERE `user_ip` = '$user_ip'
                  AND `quiz_id` = $id
                  AND CAST(`score` AS DECIMAL(10,0)) >= $quiz_pass_score";
        $result = $wpdb->get_var($sql);
        return $result;
    }

    public static function get_limit_user_by_id($quiz_id, $user_id, $quiz_pass_score){
        global $wpdb;
        $sql = "SELECT COUNT(*)
                FROM `{$wpdb->prefix}aysquiz_reports`
                WHERE `user_id` = $user_id
                  AND `quiz_id` = $quiz_id
                  AND CAST(`score` AS DECIMAL(10,0)) >= $quiz_pass_score";
        $result = intval($wpdb->get_var($sql));
        return $result;
    }

    public static function get_user_ip(){
        $ipaddress = '';
        if (getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        elseif (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if (getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if (getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if (getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if (getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

    public static function get_time_difference($strStart, $strEnd){
        $dteStart = new DateTime($strStart);
        $dteEnd = new DateTime($strEnd);
        $texts = array(
            'year' => __( "year", AYS_QUIZ_NAME ),
            'years' => __( "years", AYS_QUIZ_NAME ),
            'month' => __( "month", AYS_QUIZ_NAME ),
            'months' => __( "months", AYS_QUIZ_NAME ),
            'day' => __( "day", AYS_QUIZ_NAME ),
            'days' => __( "days", AYS_QUIZ_NAME ),
            'hour' => __( "hour", AYS_QUIZ_NAME ),
            'hours' => __( "hours", AYS_QUIZ_NAME ),
            'minute' => __( "minute", AYS_QUIZ_NAME ),
            'minutes' => __( "minutes", AYS_QUIZ_NAME ),
            'second' => __( "second", AYS_QUIZ_NAME ),
            'seconds' => __( "seconds", AYS_QUIZ_NAME ),
        );
        $interval = $dteStart->diff($dteEnd);
        $return = '';

        if ($v = $interval->y >= 1) $return .= $interval->y ." ". $texts[self::pluralize_new($interval->y, 'year')] . ' ';
        if ($v = $interval->m >= 1) $return .= $interval->m ." ". $texts[self::pluralize_new($interval->m, 'month')] . ' ';
        if ($v = $interval->d >= 1) $return .= $interval->d ." ". $texts[self::pluralize_new($interval->d, 'day')] . ' ';
        if ($v = $interval->h >= 1) $return .= $interval->h ." ". $texts[self::pluralize_new($interval->h, 'hour')] . ' ';
        if ($v = $interval->i >= 1) $return .= $interval->i ." ". $texts[self::pluralize_new($interval->i, 'minute')] . ' ';

        $return .= $interval->s ." ". $texts[self::pluralize_new($interval->s, 'second')];

        return $return;
    }

    public static function pluralize($count, $text){
        return $count . (($count == 1) ? (" $text") : (" ${text}s"));
    }

    public static function pluralize_new($count, $text){
        return ($count == 1) ? $text."" : $text."s";
    }

    public static function ays_quiz_rate( $id ) {
        global $wpdb;
        if($id === '' || $id === null){
            $reason = __("No rate provided", AYS_QUIZ_NAME);
            $output = array(
                "review" => $reason,
            );
        }else{
            $rate = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}aysquiz_rates WHERE id={$id}", "ARRAY_A");
            $output = array();
            if($rate !== null){
                $review = $rate['review'];
                $reason = stripslashes($review);
                if($reason == ''){
                    $reason = __("No review provided", AYS_QUIZ_NAME);
                }
                $score = $rate['score'];
                $output = array(
                    "score" => $score,
                    "review" => $reason,
                );
            }else{
                $reason = __("No rate provided", AYS_QUIZ_NAME);
                $output = array(
                    "review" => $reason,
                );
            }
        }
        return $output;
    }

    public static function ays_autoembed( $content ) {
        global $wp_embed;
        $content = stripslashes( wpautop( $content ) );
        $content = $wp_embed->autoembed( $content );
        if ( strpos( $content, '[embed]' ) !== false ) {
            $content = $wp_embed->run_shortcode( $content );
        }
        $content = do_shortcode( $content );
        return $content;
    }

    public static function get_questions_categories($q_ids){
        global $wpdb;

        if($q_ids == ''){
            return array();
        }
        $sql = "SELECT DISTINCT c.id, c.title
                FROM {$wpdb->prefix}aysquiz_categories c
                JOIN {$wpdb->prefix}aysquiz_questions q
                ON c.id = q.category_id
                WHERE q.id IN ({$q_ids})";

        $result = $wpdb->get_results($sql, 'ARRAY_A');
        $cats = array();

        foreach($result as $res){
            $cats[$res['id']] = $res['title'];
        }

        return $cats;
    }

    public static function ays_set_quiz_texts( $plugin_name, $settings ){

        /*
         * Get Quiz buttons texts from database
         */

        $settings_buttons_texts = $settings->ays_get_setting('buttons_texts');
        if($settings_buttons_texts){
            $settings_buttons_texts = json_decode(stripcslashes($settings_buttons_texts), true);
        }else{
            $settings_buttons_texts = array();
        }

        $ays_start_button           = (isset($settings_buttons_texts['start_button']) && $settings_buttons_texts['start_button'] != '') ? $settings_buttons_texts['start_button'] : 'Start' ;
        $ays_next_button            = (isset($settings_buttons_texts['next_button']) && $settings_buttons_texts['next_button'] != '') ? $settings_buttons_texts['next_button'] : 'Next' ;
        $ays_previous_button        = (isset($settings_buttons_texts['previous_button']) && $settings_buttons_texts['previous_button'] != '') ? $settings_buttons_texts['previous_button'] : 'Prev' ;
        $ays_clear_button           = (isset($settings_buttons_texts['clear_button']) && $settings_buttons_texts['clear_button'] != '') ? $settings_buttons_texts['clear_button'] : 'Clear' ;
        $ays_finish_button          = (isset($settings_buttons_texts['finish_button']) && $settings_buttons_texts['finish_button'] != '') ? $settings_buttons_texts['finish_button'] : 'Finish' ;
        $ays_see_result_button      = (isset($settings_buttons_texts['see_result_button']) && $settings_buttons_texts['see_result_button'] != '') ? $settings_buttons_texts['see_result_button'] : 'See Result' ;
        $ays_restart_quiz_button    = (isset($settings_buttons_texts['restart_quiz_button']) && $settings_buttons_texts['restart_quiz_button'] != '') ? $settings_buttons_texts['restart_quiz_button'] : 'Restart quiz' ;
        $ays_send_feedback_button   = (isset($settings_buttons_texts['send_feedback_button']) && $settings_buttons_texts['send_feedback_button'] != '') ? $settings_buttons_texts['send_feedback_button'] : 'Send feedback' ;
        $ays_load_more_button       = (isset($settings_buttons_texts['load_more_button']) && $settings_buttons_texts['load_more_button'] != '') ? $settings_buttons_texts['load_more_button'] : 'Load more' ;
        $ays_exit_button            = (isset($settings_buttons_texts['exit_button']) && $settings_buttons_texts['exit_button'] != '') ? $settings_buttons_texts['exit_button'] : 'Exit' ;
        $ays_check_button           = (isset($settings_buttons_texts['check_button']) && $settings_buttons_texts['check_button'] != '') ? $settings_buttons_texts['check_button'] : 'Check' ;
        $ays_login_button           = (isset($settings_buttons_texts['login_button']) && $settings_buttons_texts['login_button'] != '') ? $settings_buttons_texts['login_button'] : 'Log In' ;

        if ($ays_start_button === 'Start') {
            $ays_start_button_text = __('Start', $plugin_name);
        }else{
            $ays_start_button_text = $ays_start_button;
        }

        if ($ays_next_button === 'Next') {
            $ays_next_button_text = __('Next', $plugin_name);
        }else{
            $ays_next_button_text = $ays_next_button;
        }

        if ($ays_previous_button === 'Prev') {
            $ays_previous_button_text = __('Prev', $plugin_name);
        }else{
            $ays_previous_button_text = $ays_previous_button;
        }

        if ($ays_clear_button === 'Clear') {
            $ays_clear_button_text = __('Clear', $plugin_name);
        }else{
            $ays_clear_button_text = $ays_clear_button;
        }

        if ($ays_finish_button === 'Finish') {
            $ays_finish_button_text = __('Finish', $plugin_name);
        }else{
            $ays_finish_button_text = $ays_finish_button;
        }

        if ($ays_see_result_button === 'See Result') {
            $ays_see_result_button_text = __('See Result', $plugin_name);
        }else{
            $ays_see_result_button_text = $ays_see_result_button;
        }

        if ($ays_restart_quiz_button === 'Restart quiz') {
            $ays_restart_quiz_button_text = __('Restart quiz', $plugin_name);
        }else{
            $ays_restart_quiz_button_text = $ays_restart_quiz_button;
        }

        if ($ays_send_feedback_button === 'Send feedback') {
            $ays_send_feedback_button_text = __('Send feedback', $plugin_name);
        }else{
            $ays_send_feedback_button_text = $ays_send_feedback_button;
        }

        if ($ays_load_more_button === 'Load more') {
            $ays_load_more_button_text = __('Load more', $plugin_name);
        }else{
            $ays_load_more_button_text = $ays_load_more_button;
        }

        if ($ays_exit_button === 'Exit') {
            $ays_exit_button_text = __('Exit', $plugin_name);
        }else{
            $ays_exit_button_text = $ays_exit_button;
        }

        if ($ays_check_button === 'Check') {
            $ays_check_button_text = __('Check', $plugin_name);
        }else{
            $ays_check_button_text = $ays_check_button;
        }

        if ($ays_login_button === 'Log In') {
            $ays_login_button_text = __('Log In', $plugin_name);
        }else{
            $ays_login_button_text = $ays_login_button;
        }

        $texts = array(
            'startButton'        => $ays_start_button_text,
            'nextButton'         => $ays_next_button_text,
            'previousButton'     => $ays_previous_button_text,
            'clearButton'        => $ays_clear_button_text,
            'finishButton'       => $ays_finish_button_text,
            'seeResultButton'    => $ays_see_result_button_text,
            'restartQuizButton'  => $ays_restart_quiz_button_text,
            'sendFeedbackButton' => $ays_send_feedback_button_text,
            'loadMoreButton'     => $ays_load_more_button_text,
            'exitButton'         => $ays_exit_button_text,
            'checkButton'        => $ays_check_button_text,
            'loginButton'        => $ays_login_button_text,
        );

        return $texts;
    }

    public static function ays_version_compare($version1, $operator, $version2) {

        $_fv = intval ( trim ( str_replace ( '.', '', $version1 ) ) );
        $_sv = intval ( trim ( str_replace ( '.', '', $version2 ) ) );

        if (strlen ( $_fv ) > strlen ( $_sv )) {
            $_sv = str_pad ( $_sv, strlen ( $_fv ), 0 );
        }

        if (strlen ( $_fv ) < strlen ( $_sv )) {
            $_fv = str_pad ( $_fv, strlen ( $_sv ), 0 );
        }

        return version_compare ( ( string ) $_fv, ( string ) $_sv, $operator );
    }

    public static function ays_get_average_score_by_category($id){
       global $wpdb;
        $quizes_table = $wpdb->prefix . 'aysquiz_quizes';
        $quizes_questions_table = $wpdb->prefix . 'aysquiz_questions';
        $quizes_questions_cat_table = $wpdb->prefix . 'aysquiz_categories';
        $sql = "SELECT question_ids FROM {$quizes_table} WHERE id = ".$id;
        $results = $wpdb->get_var( $sql);
        $questions_ids = array();
        $questions_counts = array();
        $questions_cat_list = array();
        if($results != ''){
            $results = explode("," , $results);
            foreach ($results as $key){
                $questions_ids[$key] = 0;
                $questions_counts[$key] = 0;

                $sql = "SELECT q.category_id, c.title
                        FROM {$quizes_questions_table} AS q
                        JOIN {$quizes_questions_cat_table} AS c
                            ON q.category_id = c.id
                        WHERE q.id = {$key}; ";
                $questions_cat_list[$key] = $wpdb->get_row( $sql);
            }
        }

        $quizes_reports_table = $wpdb->prefix . 'aysquiz_reports';
        $sql = "SELECT options
                FROM {$quizes_reports_table}
                WHERE quiz_id =".$id;
        $report = $wpdb->get_results( $sql, ARRAY_A );
        if(! empty($report)){
            foreach ($report as $key){
                $report = json_decode($key["options"]);
                $questions = $report->correctness;
                foreach ($questions as $i => $v){
                    $q = (int) substr($i ,12);
                    if(isset($questions_ids[$q])) {
                        if ($v) {
                            $questions_ids[$q]++;
                        }

                        $questions_counts[$q]++;
                    }
                }
            }
        }

        $q_cat_list = array();
        $q_cat_title = array();
        foreach ($questions_cat_list as $key_id => $val ) {
            $val_arr = (array) $val;
            if(isset($val_arr['category_id'])){
                if (!array_key_exists($val_arr['category_id'], $q_cat_list)) {
                    $q_cat_list[$val_arr['category_id']][] = $key_id;
                    $q_cat_title[$val_arr['category_id']] = $val_arr['title'];
                }else{
                    $q_cat_list[$val_arr['category_id']][] = $key_id;
                    $q_cat_title[$val_arr['category_id']] = $val_arr['title'];
                }
            }
        }

        $q_cat_lists = array('percent'=>'', 'cat_name'=>'');
        $q_cats_lists = array();
        foreach ($q_cat_list as $key1 => $value1) {
            $sum_min = 0;
            $sum_max = 0;
            foreach ($value1 as $key2 => $value2) {
                $sum_min += $questions_ids[$value2];
                $sum_max += $questions_counts[$value2];
            }
            if($sum_max == 0){
                $persentage = 0;
            }else{
                $persentage = round(($sum_min*100)/$sum_max, 1);
            }

            $passed_users_count = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_reports WHERE quiz_id=".$id;
            $passed_users_count_res = $wpdb->get_var($passed_users_count);
            $avg_score_by_cat = "0%";
            if ($passed_users_count_res > 0) {
                $avg_score_by_cat = round( $persentage, 1 ) . '%';
            }
            $q_cat_lists['percent'] = $avg_score_by_cat;
            $q_cat_lists['cat_name'] = $q_cat_title[$key1];
            $q_cats_lists[] = $q_cat_lists;

        }

        $avg_category = '';
        foreach ($q_cats_lists as $key => $q_cats_list) {
            $avg_category .= '<p class="">
                                <strong class="">'.$q_cats_list['cat_name']  .':</strong>
                                <span class="">'.$q_cats_list['percent'].'</span>
                             </p>';
        }
        return $avg_category;
    }

    public static function get_question_categories(){
        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_categories";

        $result = $wpdb->get_results($sql, 'ARRAY_A');

        return $result;
    }

    public static function get_listtables_title_length( $listtable_name ) {
        global $wpdb;

        $settings_table = $wpdb->prefix . "aysquiz_settings";
        $sql = "SELECT meta_value FROM ".$settings_table." WHERE meta_key = 'options'";
        $result = $wpdb->get_var($sql);
        $options = ($result == "") ? array() : json_decode($result, true);

        $listtable_title_length = 5;
        if(! empty($options) ){
            switch ( $listtable_name ) {
                case 'questions':
                    $listtable_title_length = (isset($options['question_title_length']) && intval($options['question_title_length']) != 0) ? absint(intval($options['question_title_length'])) : 5;
                    break;
                case 'quizzes':
                    $listtable_title_length = (isset($options['quizzes_title_length']) && intval($options['quizzes_title_length']) != 0) ? absint(intval($options['quizzes_title_length'])) : 5;
                    break;
                case 'results':
                    $listtable_title_length = (isset($options['results_title_length']) && intval($options['results_title_length']) != 0) ? absint(intval($options['results_title_length'])) : 5;
                    break;
                default:
                    $listtable_title_length = 5;
                    break;
            }
            return $listtable_title_length;
        }
        return $listtable_title_length;
    }

    /*
    ==========================================
       Google Sheets start
    ==========================================
    */

    public static function GetGoogleAccessToken( $client_id, $redirect_uri, $client_secret, $code ){
        $url = 'https://www.googleapis.com/oauth2/v4/token';

        $curlPost = 'client_id=' . $client_id . '&redirect_uri=' . $redirect_uri . '&client_secret=' . $client_secret . '&code='. $code . '&grant_type=authorization_code';
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $curlPost,
//            CURLOPT_HTTPHEADER => array(
//                "response_type: webapplications",
//                "Content-Type: application/json"
//            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        $new_response = json_decode($response, true);
        $http_code = curl_getinfo( $curl, CURLINFO_HTTP_CODE );

        curl_close($curl);

        if($http_code != 200){
            throw new Exception( __( 'Error: Failed to receieve access token', AYS_QUIZ_NAME ) );
        }

        return $new_response;
    }

    public static function GetGoogleUserProfileInfo( $access_token ){
        $url = 'https://www.googleapis.com/oauth2/v2/userinfo?fields=name,email,gender,id,picture';

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => NULL,
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer ". $access_token,
                "response_type: webapplications",
                "Content-Type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        $new_response = json_decode($response, true);
        $http_code = curl_getinfo( $curl, CURLINFO_HTTP_CODE );

        curl_close($curl);

        if($http_code != 200){
            throw new Exception( __( 'Error: Failed to get user information', AYS_QUIZ_NAME ) );
        }

        return $new_response;
    }

    public static function GetGoogleUserToken_RefreshToken( $client_id, $redirect_uri, $client_secret, $code ){
//        $url = 'https://www.googleapis.com/oauth2/v4/token';
        $url = 'https://accounts.google.com/o/oauth2/token';

        $curl = curl_init();
        $curlPost = array(
            'grant_type' => 'authorization_code',
            'client_id' => $client_id,
            'code' => $code,
            'client_secret' => $client_secret,
            'redirect_uri' => $redirect_uri,
            'scope' => 'https://www.googleapis.com/auth/spreadsheets'
        );

        $curlPost = http_build_query( $curlPost );

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $curlPost,
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/x-www-form-urlencoded"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        $new_response = json_decode($response, true);
        $http_code = curl_getinfo( $curl, CURLINFO_HTTP_CODE );

        curl_close($curl);

        if($http_code != 200){
            throw new Exception( __( 'Error: Failed to get token', AYS_QUIZ_NAME ) );
        }

        return $new_response;
    }

    // Google sheet get refreshed token
    public static function ays_get_refreshed_token( $data ){
        error_reporting(0);
        if (empty($data)) {
            return array(
                'Code' => 0
            );
        }
        $token = isset($data['refresh_token']) && $data['refresh_token'] != '' ? $data['refresh_token'] : '';
        $client_id = isset($data['client_id']) && $data['client_id'] != '' ? $data['client_id'] : '';
        $client_secret = isset($data['client_secret']) && $data['client_secret'] != '' ? $data['client_secret'] : '';

        $url = "https://accounts.google.com/o/oauth2/token?grant_type=refresh_token&refresh_token=".$token."&client_id=".$client_id."&client_secret=".$client_secret."&scope=https://www.googleapis.com/auth/spreadsheets";
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => NULL,
            CURLOPT_HTTPHEADER => array(
                "response_type: webapplications",
                "Content-Type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        $new_response = json_decode($response, true);
        curl_close($curl);
        $new_access_token = $new_response['access_token'];

        if ($err) {
            return "cURL Error #: " . $err;
        } else {
            return $new_access_token;
        }
    }

    // Create Google sheet
    public static function ays_get_google_sheet_id( $data ) {
		error_reporting(0);
		if (empty($data)) {
			return array(
				'Code' => 0
			);
        }
        $new_token = '';
        $get_this_quiz = array();
        $question = '';
        $refresh_token = isset($data['refresh_token']) && $data['refresh_token'] != '' ? $data['refresh_token'] : '';
        $quiz_title    = isset($data['quiz_title']) && $data['quiz_title'] != '' ? $data['quiz_title'] : '';
        $custom_fields = isset($data['custom_fields']) && !empty($data['custom_fields']) ? $data['custom_fields'] : array();
        if($refresh_token != ''){
            $new_token = self::ays_get_refreshed_token($data);
        }

        $url = "https://sheets.googleapis.com/v4/spreadsheets?access_token=".$new_token;

        // Add to sheet resent values
        $properties = array(
            "properties" => array(
                "title" => $quiz_title
            ),
            "sheets" => array(
                "data" => array(
                    "rowData" => array(
                        "values" => array(
                            array(
                                "userEnteredValue" => array(
                                    "stringValue" => 'User',
                                )
                            ),
                            array(
                                "userEnteredValue" => array(
                                    "stringValue" => "User IP"
                                )
                            ),
                            array(
                                "userEnteredValue" => array(
                                    "stringValue" => "Start Date"
                                )
                            ),
                            array(
                                "userEnteredValue" => array(
                                    "stringValue" => "End Date"
                                )
                            ),
                            array(
                                "userEnteredValue" => array(
                                    "stringValue" => "Score"
                                )
                            ),
                            array(
                                "userEnteredValue" => array(
                                    "stringValue" => "Points"
                                )
                            ),
                            array(
                                "userEnteredValue" => array(
                                    "stringValue" => "Duration"
                                )
                            )
                        )
                    )
                )
            )
        );

        foreach( $custom_fields as $slug => $name ) {
            $properties['sheets']['data']['rowData']['values'][] = array(
                "userEnteredValue" => array(
                    "stringValue" => $name
                )
            );
        }

        $url = "https://sheets.googleapis.com/v4/spreadsheets?access_token=".$new_token;
        $properties = json_encode($properties);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $properties,
            CURLOPT_HTTPHEADER => array(
                "response_type: webapplications",
                "Content-Type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        $google_sheet_values = json_decode($response, true);
        curl_close($curl);
        $spreadsheet_id = $google_sheet_values['spreadsheetId'];

        if ($err) {
            return "cURL Error #: " . $err;
        } else {
            return $spreadsheet_id;
        }
	}

    // Update Google sheet
    public static function ays_update_google_spreadsheet( $data ) {
		error_reporting(0);
		if (empty($data)) {
			return array(
				'Code' => 0
			);
        }
        $new_token = '';
        $get_this_quiz = array();
        $question = '';
        $refresh_token = isset($data['refresh_token']) && $data['refresh_token'] != '' ? $data['refresh_token'] : '';
        $quiz_title    = isset($data['quiz_title']) && $data['quiz_title'] != '' ? $data['quiz_title'] : '';
        $sheet_id      = isset($data['sheet_id']) && $data['sheet_id'] != '' ? $data['sheet_id'] : '';
        $custom_fields = isset($data['custom_fields']) && !empty($data['custom_fields']) ? $data['custom_fields'] : array();

        if( $sheet_id == '' ){
			return array(
				'Code' => 0
			);
        }

        if($refresh_token != ''){
            $new_token = self::ays_get_refreshed_token($data);
        }

        // Add to sheet resent values
        $properties = array(
            "valueInputOption" => "RAW",
            "data" => array(

            )
        );

        $titles_for_ranges = array(
            "User",
            "User IP",
            "Start Date",
            "End Date",
            "Score",
            "Points",
            "Duration",
        );

        foreach( $custom_fields as $slug => $name ) {
            $titles_for_ranges[] = $name;
        }

        $ranges = self::ays_quiz_generate_keyword_array( 100 );
        foreach( $titles_for_ranges as $key => $name ) {
            $properties['data'][] = array(
                "range" => $ranges[$key] . "1",
                "values" => array(
                    array(
                        $name
                    )
                )
            );
        }
//        var_dump($properties['data']);
//        die();

        $url = "https://sheets.googleapis.com/v4/spreadsheets/" . $sheet_id . "/values:batchUpdate";
        $properties = json_encode($properties);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $properties,
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer " . $new_token,
                "response_type: webapplications",
                "Content-Type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        $google_sheet_values = json_decode($response, true);
        curl_close($curl);
//        $spreadsheet_id = $google_sheet_values['spreadsheetId'];

        if ($err) {
            return "cURL Error #: " . $err;
        } else {
            return true;
        }
	}

    public static function get_quiz_sheet_id($id){

        global $wpdb;

        $sql = "SELECT options FROM {$wpdb->prefix}aysquiz_quizes WHERE id = " . $id;

        $results = $wpdb->get_var( $sql );

        $options = json_decode( $results, true );

        $spreadsheet_id = isset( $options['spreadsheet_id'] ) && $options['spreadsheet_id'] != '' ? $options['spreadsheet_id'] : null;

        return $spreadsheet_id;
    }

    public static function delete_quiz_sheet_ids(){

        global $wpdb;

        $table = $wpdb->prefix . 'aysquiz_quizes';

        $sql = "SELECT id, options FROM {$table}";

        $results = $wpdb->get_results( $sql, "ARRAY_A" );

        foreach( $results as $key => $result ){
            $id = intval( $result['id'] );
            $options = json_decode( $result['options'], true );

            if( array_key_exists( 'enable_google_sheets', $options ) ){
                unset( $options['enable_google_sheets'] );
            }else{
                continue;
            }

            if( array_key_exists( 'spreadsheet_id', $options ) ){
                unset( $options['spreadsheet_id'] );
            }

            $options = json_encode( $options );

            $wpdb->update(
                $table,
                array( 'options' => $options ),
                array( 'id' => $id ),
                array( '%s' ),
                array( '%d' )
            );
        }

        return true;
    }

    /*
    ==========================================
       Google Sheets end
    ==========================================
    */

    public static function get_question_bank_categories($q_ids){
        global $wpdb;

        if($q_ids == ''){
            return array();
        }
        $sql = "SELECT DISTINCT c.id, c.title
                FROM {$wpdb->prefix}aysquiz_categories c
                JOIN {$wpdb->prefix}aysquiz_questions q
                ON c.id = q.category_id
                WHERE q.id IN ({$q_ids})";

        $result = $wpdb->get_results($sql, 'ARRAY_A');
        $cats = array();

        foreach($result as $res){
            $cats[$res['id']] = $res['title'];
        }

        return $cats;
    }

    public static function ays_shuffle_assoc($list) {
        if (!is_array($list)) return $list;

        $keys = array_keys($list);
        shuffle($keys);
        $random = array();
        foreach ($keys as $key) {
            $random[$key] = $list[$key];
        }
        return $random;
    }

    public static function get_question_ids_ordering_by_categories( $attr ){
        if (!is_array($attr)) return false;

        $arr_questions = ( isset($attr['arr_questions']) && ! empty($attr['arr_questions']) ) ? $attr['arr_questions'] : array();
        $question_bank_categories = ( isset($attr['question_bank_categories']) && ! empty($attr['question_bank_categories']) ) ? $attr['question_bank_categories'] : array();
        $quests = ( isset($attr['quests']) && ! empty($attr['quests']) ) ? $attr['quests'] : array();
        $randomize_questions = isset($attr['randomize_questions']) ? $attr['randomize_questions'] : false;

        $question_bank_questions = array();
        $question_bank_cats = array();
        $quiz_questions_ids = array();

        foreach($arr_questions as $key => $val){
            $question_bank_questions[$val] = $quests[$val];
            if(isset($question_bank_categories[$quests[$val]['category_id']])){
                $question_bank_cats[$quests[$val]['category_id']][] = strval($val);
            }
        }

        if ($randomize_questions) {
            $question_bank_cats = Quiz_Maker_Data::ays_shuffle_assoc($question_bank_cats);
            foreach ($question_bank_cats as $key => $value) {
                shuffle($question_bank_cats[$key]);
            }
        }
        $arr_questions = array();
        foreach($question_bank_cats as $key => $value){
            $arr_questions = array_merge($arr_questions, $value);
        }

        $quiz_questions_ids = implode(',', $arr_questions);

        return $quiz_questions_ids;
    }

    public static function ays_quiz_is_enable_question_max_length( $question_id, $question_type = 'text' ){
        global $wpdb;
        $questions_table = $wpdb->prefix . "aysquiz_questions";
        $question_id = absint(intval($question_id));

        $question = $wpdb->get_row("SELECT * FROM {$questions_table} WHERE id={$question_id};", "ARRAY_A");
        $options = ! isset($question['options']) ? array() : json_decode($question['options'], true);

        $res = false;
        switch ( $question_type ) {
            case 'number':
                if(isset($options['enable_question_number_max_length']) && sanitize_text_field( $options['enable_question_number_max_length'] ) == 'on'){
                    $res = true;
                }
            break;
            default:
                if(isset($options['enable_question_text_max_length']) && sanitize_text_field( $options['enable_question_text_max_length'] ) == 'on'){
                    $res = true;
                }
            break;
        }

        return $res;
    }

    public static function ays_quiz_get_question_max_length_array( $question_id ){
        global $wpdb;
        $questions_table = $wpdb->prefix . "aysquiz_questions";
        $question_id = absint(intval($question_id));

        $question = $wpdb->get_row("SELECT * FROM {$questions_table} WHERE id={$question_id};", "ARRAY_A");
        $options = ! isset($question['options']) ? array() : json_decode($question['options'], true);

        $res = array();

        // Maximum length of a text field
        $options['enable_question_text_max_length'] = isset($options['enable_question_text_max_length']) ? sanitize_text_field( $options['enable_question_text_max_length'] ) : 'off';
        $res['enable_question_text_max_length'] = (isset($options['enable_question_text_max_length']) && sanitize_text_field( $options['enable_question_text_max_length'] ) == 'on') ? true : false;

        // Length
        $res['question_text_max_length'] = ( isset($options['question_text_max_length']) && sanitize_text_field( $options['question_text_max_length'] ) != '' ) ? absint( intval( sanitize_text_field( $options['question_text_max_length'] ) ) ) : '';

        // Limit by
        $res['question_limit_text_type'] = ( isset($options['question_limit_text_type']) && sanitize_text_field( $options['question_limit_text_type'] ) != '' ) ? sanitize_text_field( $options['question_limit_text_type'] ) : 'characters';

        // Show the counter-message
        $options['question_enable_text_message'] = isset($options['question_enable_text_message']) ? sanitize_text_field( $options['question_enable_text_message'] ) : 'off';
        $res['question_enable_text_message'] = (isset($options['question_enable_text_message']) && $options['question_enable_text_message'] == 'on') ? true : false;

        // Maximum length of a number field
        $options['enable_question_number_max_length'] = isset($options['enable_question_number_max_length']) ? sanitize_text_field( $options['enable_question_number_max_length'] ) : 'off';
        $res['enable_question_number_max_length'] = (isset($options['enable_question_number_max_length']) && sanitize_text_field( $options['enable_question_number_max_length'] ) == 'on') ? true : false;

        // Length
        $res['question_number_max_length'] = ( isset($options['question_number_max_length']) && sanitize_text_field( $options['question_number_max_length'] ) != '' ) ? intval( sanitize_text_field( $options['question_number_max_length'] ) ) : '';

        return $res;
    }

    public static function ays_quiz_is_elementor(){
        if( isset( $_GET['action'] ) && $_GET['action'] == 'elementor' ){
            $is_elementor = true;
        }elseif( isset( $_REQUEST['elementor-preview'] ) && $_REQUEST['elementor-preview'] != '' ){
            $is_elementor = true;
        }else{
            $is_elementor = false;
        }
        return $is_elementor;
    }

    public static function ays_answer_numbering( $numbering ){
        $keyword_arr = array();
        switch ($numbering) {
            case '1.':

                $char_min_val = '1';
                $char_max_val = '100';
                for($x = $char_min_val; $x <= $char_max_val; $x++){
                    $keyword_arr[] = $x .".";
                }

                break;
            case '1)':

                $char_min_val = '1';
                $char_max_val = '100';
                for($x = $char_min_val; $x <= $char_max_val; $x++){
                    $keyword_arr[] = $x .")";
                }

                break;
            case 'A.':

                $char_min_val = 'A';
                $char_max_val = 'Z';
                for($x = $char_min_val; $x <= $char_max_val; $x++){
                    $keyword_arr[] = $x .".";
                }

                break;
            case 'A)':

                $char_min_val = 'A';
                $char_max_val = 'Z';
                for($x = $char_min_val; $x <= $char_max_val; $x++){
                    $keyword_arr[] = $x .")";
                }

                break;
            case 'a.':
                $char_min_val = 'a';
                $char_max_val = 'z';
                for($x = $char_min_val; $x <= $char_max_val; $x++){
                    $keyword_arr[] = $x .".";
                }

                break;
            case 'a)':

                $char_min_val = 'a';
                $char_max_val = 'z';
                for($x = $char_min_val; $x <= $char_max_val; $x++){
                    $keyword_arr[] = $x .")";
                }

                break;

            default:

                break;
        }

        return $keyword_arr;
    }

    public static function ays_quiz_generate_keyword_array( $max_val ) {
        if (is_null($max_val) || $max_val == '') {
            $max_val = 6; //'F';
        }
        $max_val = absint(intval($max_val)) - 1;

        $keyword_arr = array();
        $letters = range('A', 'Z');

        if($max_val <= 25){
            $max_alpha_val = $letters[$max_val];
        }
        elseif($max_val > 25){
          $dividend = ($max_val + 1);
          $max_alpha_val = '';
          $modulo;
          while ($dividend > 0){
            $modulo = ($dividend - 1) % 26;
            $max_alpha_val = $letters[$modulo] . $max_alpha_val;
            $dividend = floor((($dividend - $modulo) / 26));
          }
        }

        $keyword_arr = self::ays_quiz_create_columns_array( $max_alpha_val );

        return $keyword_arr;

    }

    public static function ays_quiz_create_columns_array($end_column, $first_letters = '') {
        $columns = array();
        $letters = range('A', 'Z');
        $length = strlen($end_column);

        // Iterate over 26 letters.
        foreach ($letters as $letter) {
            // Paste the $first_letters before the next.
            $column = $first_letters . $letter;

            // Add the column to the final array.
            $columns[] = $column;

            // If it was the end column that was added, return the columns.
            if ($column == $end_column)
                return $columns;
        }

        // Add the column children.
        foreach ($columns as $column) {
            // Don't itterate if the $end_column was already set in a previous itteration.
            // Stop iterating if you've reached the maximum character length.
            if (!in_array($end_column, $columns) && strlen($column) < $length) {
              $new_columns = self::ays_quiz_create_columns_array($end_column, $column);
              // Merge the new columns which were created with the final columns array.
              $columns = array_merge($columns, $new_columns);
            }
        }

        return $columns;
    }

    public static function ays_get_author_quizzes( $author_id ){
        global $wpdb;

        $sql = "SELECT *
                FROM {$wpdb->prefix}aysquiz_quizes
                WHERE author_id=" . $author_id;

        $quizzes = array();
        $res = $wpdb->get_results($sql, 'ARRAY_A');

        foreach( $res as $key => $value ){
            $quizzes[ $value['id'] ] = $value;
        }

        return $quizzes;
    }

    public static function ays_get_author_quizzes_ids( $author_id ){
        global $wpdb;

        $sql = "SELECT *
                FROM {$wpdb->prefix}aysquiz_quizes
                WHERE author_id=" . $author_id;

        $quizzes = array();
        $res = $wpdb->get_results($sql, 'ARRAY_A');

        foreach( $res as $key => $value ){
            $quizzes[] = $value['id'];
        }

        return $quizzes;
    }

    public static function ays_quiz_set_cookie($attr){
        $cookie_name = $attr['name'].$attr['id'];
        $cookie_value = $attr['title'];
        $cookie_value = isset( $attr['attempts_count'] ) ? $attr['attempts_count'] : 1;
        self::ays_quiz_remove_cookie( $attr );
        $cookie_expiration =  current_time('timestamp') + (1 * 365 * 24 * 60 * 60);
        setcookie($cookie_name, $cookie_value, $cookie_expiration, '/');
    }

    public static function ays_quiz_remove_cookie($attr){
        $cookie_name = $attr['name'].$attr['id'];
        if(isset($_COOKIE[$cookie_name])){
            unset($_COOKIE[$cookie_name]);
            $cookie_expiration =  current_time('timestamp') - 1;
            setcookie($cookie_name, null, $cookie_expiration, '/');
        }
    }

    public static function ays_quiz_check_cookie($attr){
        $cookie_name = $attr['name'].$attr['id'];
        if(isset($_COOKIE[$cookie_name])){
            if( isset( $attr['increase_count'] ) && $attr['increase_count'] == true ){
                $attr['attempts_count'] = intval( $_COOKIE[$cookie_name] ) + 1;
                self::ays_quiz_set_cookie( $attr );
            }
            return true;
        }
        return false;
    }

    public static function get_limit_cookie_count($attr){
        $cookie_name = $attr['name'].$attr['id'];
        if(isset($_COOKIE[$cookie_name])){
            return intval( $_COOKIE[ $cookie_name ] );
        }
        return false;
    }

    public static function closetags($html) {
        preg_match_all('#<(?!meta|img|br|hr|input\b)\b([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $html, $result);
        $openedtags = $result[1];
        preg_match_all('#</([a-z]+)>#iU', $html, $result);
        $closedtags = $result[1];
        $len_opened = count($openedtags);
        if (count($closedtags) == $len_opened) {
            return $html;
        }
        $openedtags = array_reverse($openedtags);
        for ($i=0; $i < $len_opened; $i++) {
            if (!in_array($openedtags[$i], $closedtags)) {
                $html .= '</'.$openedtags[$i].'>';
            } else {
                unset($closedtags[array_search($openedtags[$i], $closedtags)]);
            }
        }
        return $html;
    }

    // Get images
    public static function ays_get_images_from_url( $image_url ){
        static $counter = 0;
        $counter++;
        $filename = current_time("Y-m-d") . "-" . time() . $counter;
        $uploaddir = wp_upload_dir();
        $uploadfile = $uploaddir['path'] . '/' . $filename;
        $body = "";
        $request  = wp_remote_get( $image_url );
        $body = wp_remote_retrieve_body( $request );
        if($body != ""){
            $savefile = fopen($uploadfile, 'w');
            fwrite($savefile, $body);
            fclose($savefile);
            $file_mime_type = isset($request['headers']['content-type']) ? $request['headers']['content-type'] : "";
            $attachment = array(
                'post_mime_type' => $file_mime_type,
                'post_title' => $filename,
                'post_content' => '',
                'post_status' => 'inherit'
            );

            $attach_id = wp_insert_attachment( $attachment, $uploadfile );

            $imagenew = get_post( $attach_id );
            $fullsizepath = get_attached_file( $imagenew->ID );
            $attach_data = wp_generate_attachment_metadata( $attach_id, $fullsizepath );
            wp_update_attachment_metadata( $attach_id, $attach_data );
            $attach_url = wp_get_attachment_image_url($attach_id);
            return $attach_url;
        }
        return $image_url;
    }

    public static function ays_delete_report_certificate( $item_id, $type = 'report' ){
        if( absint( $item_id ) > 0 ){
            global $wpdb;
            if( $type == 'report' ){
                $sql = "SELECT options FROM ". $wpdb->prefix ."aysquiz_reports WHERE id=". $item_id;
                $item = $wpdb->get_row( $sql, "ARRAY_A" );
                $options = (isset($item['options']) && $item['options'] != '') ? json_decode($item['options'], true) : array();
                $cert_file_name = isset($options['cert_file_name']) && $options['cert_file_name'] != '' ? $options['cert_file_name'] : '';
                $cert_file_url = isset($options['cert_file_url']) && $options['cert_file_url'] != '' ? $options['cert_file_url'] : '';
                $cert_file_path = isset($options['cert_file_path']) && $options['cert_file_path'] != '' ? $options['cert_file_path'] : '';
                if(file_exists($cert_file_path)){
                    unlink( $cert_file_path );
                }
            }elseif( $type == 'quiz' ){
                $sql = "SELECT options FROM ". $wpdb->prefix ."aysquiz_reports WHERE quiz_id=". $item_id;
                $items = $wpdb->get_results( $sql, "ARRAY_A" );
                foreach( $items as $item_key => $item ){
                    $options = (isset($item['options']) && $item['options'] != '') ? json_decode($item['options'], true) : array();
                    $cert_file_name = isset($options['cert_file_name']) && $options['cert_file_name'] != '' ? $options['cert_file_name'] : '';
                    $cert_file_url = isset($options['cert_file_url']) && $options['cert_file_url'] != '' ? $options['cert_file_url'] : '';
                    $cert_file_path = isset($options['cert_file_path']) && $options['cert_file_path'] != '' ? $options['cert_file_path'] : '';
                    if(file_exists($cert_file_path)){
                        unlink( $cert_file_path );
                    }
                }
            }
        }
    }

    public static function quiz_maker_capabilities_for_editing(){
        global $wpdb;
        $sql = "SELECT meta_value FROM {$wpdb->prefix}aysquiz_settings WHERE `meta_key` = 'options'";
        $result = $wpdb->get_var($sql);

        $capability = false;
        if($result !== null){
            $options = json_decode($result, true);

            // User roles to change quiz
            $ays_user_roles = (isset($options['user_roles_to_change_quiz']) && !empty( $options['user_roles_to_change_quiz'] ) ) ? $options['user_roles_to_change_quiz'] : array('administrator');
            if(is_user_logged_in()){
                $current_user = wp_get_current_user();
                $current_user_roles = $current_user->roles;
                $ishmar = 0;
                foreach($current_user_roles as $r){
                    if(in_array($r, $ays_user_roles)){
                        $ishmar++;
                    }
                }
                if($ishmar > 0){
                    $capability = true;
                }
            }
        }
        return $capability;
    }
}
