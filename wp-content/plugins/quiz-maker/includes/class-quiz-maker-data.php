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
    public static function get_question_category_id_by_question_id($id){
        global $wpdb;

        $sql = "SELECT category_id
                FROM {$wpdb->prefix}aysquiz_questions
                WHERE id=" . $id;

        $category = $wpdb->get_var($sql);

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

    public static function get_quiz_question_title_by_id($id){

        global $wpdb;

        $sql = "SELECT question FROM {$wpdb->prefix}aysquiz_questions WHERE id = " . $id;

        $results = $wpdb->get_var($sql);

        return $results;

    }

    public static function get_quiz_questions_by_ids($ids){

        global $wpdb;

        $results = array();
        if(!empty($ids)){
            $ids = implode(",", $ids);
            $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_questions WHERE id IN (" . $ids . ") AND `published` = 1";

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

    public static function get_question_answers_weight( $id ) {
        global $wpdb;

        $sql = "SELECT answers_weight
                FROM {$wpdb->prefix}aysquiz_questions
                WHERE id=" . $id . "";

        return absint( $wpdb->get_var( $sql ) );
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
        $enable_top_keywords = false;
        $enable_personality_result = false;
        foreach($data as $variable => $value){
            if($variable == 'top_keywords_count' || $variable == 'top_keywords_percentage'){
                $enable_top_keywords = true;
                continue;
            }
            
            if($variable == 'personality_result_by_question_ids'){
                $enable_personality_result = true;
                continue;
            }
            if( !is_array( $value ) ){
                $content = str_replace("%%".$variable."%%", $value, $content);
            }
        }

        if($enable_top_keywords){
            preg_match_all('/%%top_keywords_count_(\d+)%%/', $content, $resultCount);
            preg_match_all('/%%top_keywords_percentage_(\d+)%%/', $content, $resultPercentage);

            if (!empty($resultCount[1])) {

                foreach ($resultCount[1] as $k => $v) {
                    $number = absint($v);
                    $countMessage = '';
                    if ($number == 0) {
                        for($i=0;$i<count($data['top_keywords_count']);$i++){
                            if($data['top_keywords_count'][$i]['keyword_text'] !== null && $data['top_keywords_count'][$i]['keyword_text'] != ''){
                                $countMessage .= $data['top_keywords_count'][$i]['keyword_text'] . ' <span class="ays-quiz-top-keywords-span">— (' . $data['top_keywords_count'][$i]['keyword_count'] . ")</span><br/>";
                            }
                        }
                    }else{
                        for($i=0;$i<$number;$i++){
                            if($data['top_keywords_count'][$i]['keyword_text'] !== null && $data['top_keywords_count'][$i]['keyword_text'] != ''){
                                $countMessage .= $data['top_keywords_count'][$i]['keyword_text'] . ' <span class="ays-quiz-top-keywords-span">— (' . $data['top_keywords_count'][$i]['keyword_count'] . ")</span><br/>";
                            }
                        }
                    }

                    $content = str_replace("%%top_keywords_count_".$number."%%", $countMessage, $content);
                }
            }

            if (!empty($resultPercentage[1])) {
                foreach ($resultPercentage[1] as $k => $v) {
                    $number = absint($v);
                    $percentageMessage = '';
                    if ($number == 0) {

                        for($i=0;$i<count($data['top_keywords_percentage']);$i++){
                            if( $data['top_keywords_percentage'][$i]['keyword_text'] !== null && $data['top_keywords_percentage'][$i]['keyword_text'] != ''){
                                $percentageMessage .= $data['top_keywords_percentage'][$i]['keyword_text'] . ' <span class="ays-quiz-top-keywords-span">— (' . $data['top_keywords_percentage'][$i]['keyword_percentage'] . '%' . ")</span><br/>";
                            }
                        }
                    }else{

                        for($i=0;$i<$number;$i++){
                            if( $data['top_keywords_percentage'][$i]['keyword_text'] !== null && $data['top_keywords_percentage'][$i]['keyword_text'] != ''){
                                $percentageMessage .= $data['top_keywords_percentage'][$i]['keyword_text'] . ' <span class="ays-quiz-top-keywords-span">— (' . $data['top_keywords_percentage'][$i]['keyword_percentage'] . '%' . ")</span><br/>";
                            }
                        }
                    }
                    $content = str_replace("%%top_keywords_percentage_".$number."%%", $percentageMessage, $content);
                }
            }
        }

        if($enable_personality_result){
            preg_match_all('/%%personality_result_by_question_ids_([0-9\,]+)%%/', $content, $resultCount);

            if (!empty($resultCount[1]) && isset( $resultCount[1][0] ) && !empty($resultCount[1][0]) ) {

                $question_cat_ids_str = isset( $resultCount[1][0] ) && !empty( $resultCount[1][0] ) ? $resultCount[1][0] : "";
                $question_cat_ids = isset( $question_cat_ids_str ) && !empty( $question_cat_ids_str ) ? explode(",", $question_cat_ids_str) : array();

                $personality_data = isset( $data['personality_result_by_question_ids'] ) && !empty($data['personality_result_by_question_ids']) ? $data['personality_result_by_question_ids'] : array();

                $points_keywords_arr = isset( $personality_data['points_keywords_arr'] ) && !empty( $personality_data['points_keywords_arr'] ) ? $personality_data['points_keywords_arr'] : array();
                $all_questions_id_arr = isset( $personality_data['all_questions_id_arr'] ) && !empty( $personality_data['all_questions_id_arr'] ) ? $personality_data['all_questions_id_arr'] : array();
                $quiz_id = isset( $personality_data['quiz_id'] ) && !empty( $personality_data['quiz_id'] ) ? $personality_data['quiz_id'] : "";
                $assign_keywords_texts = isset( $personality_data['assign_keywords_texts'] ) && !empty( $personality_data['assign_keywords_texts'] ) ? $personality_data['assign_keywords_texts'] : array();
                $apply_points_to_keywords = isset( $personality_data['apply_points_to_keywords'] ) && $personality_data['apply_points_to_keywords'] == true ? $personality_data['apply_points_to_keywords'] : false;

                $quiz_options = array(
                    'apply_points_to_keywords' => $apply_points_to_keywords,
                );

                $personality_result_data_html = self::personality_result_data_by_user_answer( $points_keywords_arr, $all_questions_id_arr, $quiz_id, $assign_keywords_texts, $question_cat_ids, $quiz_options);


                $content = str_replace("%%personality_result_by_question_ids_".$question_cat_ids_str."%%", $personality_result_data_html, $content);
            }
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

    public static function get_answers_fill_in_blank_max_weight($question_id, $answer_ids){
        global $wpdb;
        $answers_table = $wpdb->prefix . "aysquiz_answers";
        $question_id = absint(sanitize_text_field($question_id));
        $answer_id_arr = isset( $answer_ids[$question_id] ) && !empty( $answer_ids[$question_id] ) ? $answer_ids[$question_id] : array();

        if( empty( $answer_id_arr ) ){
            return 0;
        }

        if( is_string( $answer_id_arr ) ){
            $answer_id_arr = array( $answer_id_arr );
        }

        $answer_ids_str = implode(',', $answer_id_arr);

        $query_part = "";
        $sql = "SELECT SUM(weight) FROM {$answers_table} WHERE question_id={$question_id} AND weight > 0 AND id IN({$answer_ids_str})";

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

        // Display score option
        $final_display_score = (isset($last_result['final_display_score']) && $last_result['final_display_score'] != "") ? $last_result['final_display_score'] : $last_result['score'] . " %";

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
                        <td style='border: 1px solid #ccc;text-align: center;padding: 10px 11px 9px 6px;' colspan='3'>" . $final_display_score . "</td>
                   </tr>";
            }
            if($send){
                $index = 1;
                foreach ($data_result['correctness'] as $key => $option) {
                    if (strpos($key, 'question_id_') !== false) {
                        $question_id = absint(intval(explode('_', $key)[2]));
                        $question = $wpdb->get_row("SELECT * FROM {$questions_table} WHERE id={$question_id}", "ARRAY_A");
                        $question_type = isset($question['type']) && $question['type'] != '' ? sanitize_text_field( $question['type'] ) : 'radio';
                        $answers_array = self::get_answers_with_question_id($question_id);
                        $qoptions = isset($question['options']) && $question['options'] != '' ? json_decode($question['options'], true) : array();
                        $use_html = isset($qoptions['use_html']) && $qoptions['use_html'] == 'on' ? true : false;
                        $correct_answers = self::get_correct_answers($question_id);
                        $correct_answers_image = self::get_correct_answer_images($question_id);

                        $question_title = isset( $question["question"] ) && $question["question"] != '' ? strip_shortcodes(stripslashes($question["question"])) : '';

                        

                        $is_text_type = self::question_is_text_type($question_id);
                        $is_matching_type = self::is_matching_answer( $question_id );
                        $text_type = self::text_answer_is($question_id);
                        $not_multiple_text_types = array("number", "date");
                        $answer_incorrect_matches = isset($qoptions['answer_incorrect_matches']) && !empty( $qoptions['answer_incorrect_matches'] ) ? $qoptions['answer_incorrect_matches'] : array();

                        if($is_text_type){
                            $user_answered = self::get_user_text_answered((object)$data_result['user_answered'], $key);
                            $user_answered_images = '';
                        }elseif( $question_type == 'fill_in_blank' ){
                            $user_answered = self::get_user_fill_in_blank_answered((object)$data_result['user_answered'], $key);
                            $user_answered_images = '';
                        }elseif( $is_matching_type ){
                            $user_answered = self::get_user_matching_answered((object)$data_result['user_answered'], $key, $answer_incorrect_matches);
                            $correct_answers = self::get_correct_answers_for_matching_type($question_id);
                            $user_answered_images = '';
                        }else{
                            $user_answered = self::get_user_answered((object)$data_result['user_answered'], $key);
                            $user_answered_images = self::get_user_answered_images((object)$data_result['user_answered'], $key);
                        }

                        $not_influence_to_score = isset($question['not_influence_to_score']) && $question['not_influence_to_score'] == 'on' ? true : false;
                        if ( $not_influence_to_score ) {
                            $not_influance_check_td = ' colspan="2" ';
                        }else{
                            $not_influance_check_td = '';
                        }

                        if(!$is_matching_type && is_array($user_answered) && isset( $user_answered['message'] )){
                            $user_answered = $user_answered['message'];
                        } elseif ( $question_type == 'fill_in_blank' ) {
                            $fill_in_blank_question_title_user_answer = $question_title;
                            foreach ($answers_array as $answer_key => $answer_data) {
                                $slug = isset($answer_data["slug"]) && $answer_data["slug"] != '' ? stripslashes(htmlentities($answer_data["slug"], ENT_QUOTES)) : '';
                                $answer_id = (isset($answer_data['id']) && $answer_data['id'] != '') ? $answer_data["id"] : "";
                                $user_answer = (isset($user_answered[$answer_id]) && $user_answered[$answer_id] != '') ? $user_answered[$answer_id] : "";
                                $corect_answer = (isset($answer_data['answer']) && $answer_data['answer'] != '') ? $answer_data["answer"] : "";

                                if( $slug == "" ){
                                    continue;
                                }

                                if(mb_strtolower(trim($user_answer)) == mb_strtolower(trim($corect_answer))){
                                    $answer_html = "<span style='color: #73AF55;font-weight:700;'>". $user_answer ."</span>";
                                } elseif( $user_answer == "" ){
                                    $answer_html = "<span style='color: #D06079;font-weight: 700;'>". "—" ."</span>";
                                } else {
                                    $answer_html = "<span style='color: #D06079;font-weight: 700;'>". $user_answer ."</span>";
                                }


                                $fill_in_blank_question_title_user_answer = str_replace( $slug ,$answer_html, $fill_in_blank_question_title_user_answer);
                            }

                            $user_answered = stripslashes( $fill_in_blank_question_title_user_answer );
                        }

                        $question_image = isset( $question["question_image"] ) && $question["question_image"] != '' ? $question["question_image"] : '';
                        $td_value_html .= '<tr>
                            <td style="border: 1px solid #ccc;padding: 10px 11px 9px 6px;">
                                <strong>'.__('Question', AYS_QUIZ_NAME).' ' . $index . ':</strong>
                                <br/>';

                        $td_value_html .= strip_shortcodes( stripslashes( $question["question"] ) );

                        if( $question_image != '' ){
                            $td_value_html .= '<div><img style="max-width: 300px;max-height:300px; width: auto; height:auto;" src="' . $question_image . '"></div>';
                        }

                        $td_value_html .= '</td>';

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
                                if(mb_strtolower(trim($user_answered)) == mb_strtolower(trim($c_ans))){
                                    $c_answer = $c_ans;
                                    break;
                                }
                            }
                            $td_value_html .= '<td style="border: 1px solid #ccc;padding: 10px 11px 9px 6px;"><strong>'.__('Correct answer',AYS_QUIZ_NAME).':</strong><br/>';
                            $td_value_html .= '<div class="success">' . htmlspecialchars_decode(do_shortcode(stripslashes($c_answer))) . '<br></div>';
                            $td_value_html .= '</td>';
                        } elseif( $question_type == "fill_in_blank" ){

                            $fill_in_blank_question_title_correct = $question_title;

                            foreach ($answers_array as $answer_key => $answer_data) {
                                $slug = isset($answer_data["slug"]) && $answer_data["slug"] != '' ? stripslashes(htmlentities($answer_data["slug"], ENT_QUOTES)) : '';
                                $answer_id = (isset($answer_data['id']) && $answer_data['id'] != '') ? $answer_data["id"] : "";
                                $corect_answer = (isset($answer_data['answer']) && $answer_data['answer'] != '') ? $answer_data["answer"] : "";

                                if( $slug == "" ){
                                    continue;
                                }

                                $answer_html = "<span style='color: #73AF55;font-weight:700;'>". $corect_answer ."</span>";

                                $fill_in_blank_question_title_correct = str_replace( $slug ,$answer_html, $fill_in_blank_question_title_correct);
                            }

                            $td_value_html .= '<td style="border: 1px solid #ccc;padding: 10px 11px 9px 6px;"><strong>'.__('Correct answer',AYS_QUIZ_NAME).':</strong><br/>';
                            $td_value_html .= '<div class="success">' . htmlspecialchars_decode(do_shortcode(stripslashes($fill_in_blank_question_title_correct))) . '<br></div>';
                            $td_value_html .= '</td>';

                        } elseif( $is_matching_type ) {
                            $correct_answer_content = '';
                            foreach ( $correct_answers as $key => $_correct_answer ) {
                                if($use_html){
                                    $correct_answer_content .= stripslashes( $_correct_answer ) . '<br>';
                                }else{
                                    $correct_answer_content .= htmlspecialchars_decode( stripslashes( $_correct_answer ) ) . '<br>';
                                }
                            }

                            $td_value_html .= '<td style="border: 1px solid #ccc;padding: 10px 11px 9px 6px;"><strong>'.__('Correct answer',AYS_QUIZ_NAME).':</strong><br/>';
                            $td_value_html .= '<div class="'.$correct_answers_status_class.'">' . $correct_answer_content . '<br>';

                            if( $correct_answers_image != '' ){
                                $td_value_html .= '<div>' . $correct_answers_image . '</div>';
                            }

                            $td_value_html .= '</div>';
                            $td_value_html .= '</td>';

                        } else{
                            if($text_type == 'date'){
                                $correct_answers = date( 'm/d/Y', strtotime( $correct_answers ) );
                            }
                            $correct_answer_content = htmlspecialchars_decode( stripslashes( $correct_answers ) );
                            if($use_html){
                                $correct_answer_content = stripslashes( $correct_answers );
                            }

                            $td_value_html .= '<td style="border: 1px solid #ccc;padding: 10px 11px 9px 6px;"><strong>'.__('Correct answer',AYS_QUIZ_NAME).':</strong><br/>';
                            $td_value_html .= '<div class="'.$correct_answers_status_class.'">' . $correct_answer_content . '<br>';

                            if( $correct_answers_image != '' ){
                                $td_value_html .= '<div>' . $correct_answers_image . '</div>';
                            }

                            $td_value_html .= '</div>';
                            $td_value_html .= '</td>';
                        }

                        if($text_type == 'date'){
                            if(Quiz_Maker_Admin::validateDate($user_answered, 'Y-m-d')){
                                $user_answered = date( 'm/d/Y', strtotime( $user_answered ) );
                            }
                        }

                        if( $is_matching_type ) {
                            $user_answer_content = '';
                            foreach ( $user_answered as $key => $_user_answered ) {
                                if($use_html){
                                    $user_answer_content .= stripslashes( $_user_answered['answer'] ) . '<br>';
                                }else{
                                    $user_answer_content .= htmlspecialchars_decode( stripslashes( $_user_answered['answer'] ) ) . '<br>';
                                }
                            }
                        }else{
                            $user_answer_content = htmlspecialchars_decode( stripslashes( $user_answered ) );
                            if($use_html){
                                $user_answer_content = stripslashes( $user_answered );
                            }
                        }

                        $td_value_html .= '<td style="border: 1px solid #ccc;padding: 10px 11px 9px 6px;" '.$not_influance_check_td.'><strong>'.__('User answered',AYS_QUIZ_NAME).':</strong><br/>';
                        $td_value_html .= '<div class="'.$status_class.'">' . $user_answer_content . '<br>';

                        if( $user_answered_images != '' ){
                            $td_value_html .= '<div>' . $user_answered_images . '</div>';
                        }

                        $td_value_html .= '</div>';
                        $td_value_html .= '</td>';

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
                        $question_type = isset($question['type']) && $question['type'] != '' ? sanitize_text_field( $question['type'] ) : 'radio';
                        $answers_array = self::get_answers_with_question_id($question_id);
                        $correct_answers = self::get_correct_answers($question_id);

                        $question_title = isset( $question["question"] ) && $question["question"] != '' ? strip_shortcodes(stripslashes($question["question"])) : '';

                        $is_text_type = self::question_is_text_type($question_id);
                        $text_type = self::text_answer_is($question_id);
                        $not_multiple_text_types = array("number", "date");

                        if($is_text_type){
                            $user_answered = self::get_user_text_answered((object)$data_result['user_answered'], $key);
                            $user_answered_images = '';
                        }elseif( $question_type == 'fill_in_blank' ){
                            $user_answered = self::get_user_fill_in_blank_answered((object)$data_result['user_answered'], $key);
                            $user_answered_images = '';
                        }else{
                            $user_answered = self::get_user_answered((object)$data_result['user_answered'], $key);
                            $user_answered_images = self::get_user_answered_images((object)$data_result['user_answered'], $key);
                        }

                        $ans_point = $option;
                        $ans_point_class = 'success';
                        if(is_array($user_answered) && isset( $user_answered['message'] )){
                            $user_answered = $user_answered['message'];
                            $ans_point = '-';
                            $ans_point_class = 'error';
                        } elseif ( $question_type == 'fill_in_blank' ) {
                            $fill_in_blank_question_title_user_answer = $question_title;
                            foreach ($answers_array as $answer_key => $answer_data) {
                                $slug = isset($answer_data["slug"]) && $answer_data["slug"] != '' ? stripslashes(htmlentities($answer_data["slug"], ENT_QUOTES)) : '';
                                $answer_id = (isset($answer_data['id']) && $answer_data['id'] != '') ? $answer_data["id"] : "";
                                $user_answer = (isset($user_answered[$answer_id]) && $user_answered[$answer_id] != '') ? $user_answered[$answer_id] : "";
                                $corect_answer = (isset($answer_data['answer']) && $answer_data['answer'] != '') ? $answer_data["answer"] : "";

                                if( $slug == "" ){
                                    continue;
                                }

                                if(mb_strtolower(trim($user_answer)) == mb_strtolower(trim($corect_answer))){
                                    $answer_html = "<span style='color: #73AF55;font-weight:700;'>". $user_answer ."</span>";
                                } elseif( $user_answer == "" ){
                                    $answer_html = "<span style='color: #D06079;font-weight: 700;'>". "—" ."</span>";
                                } else {
                                    $answer_html = "<span style='color: #D06079;font-weight: 700;'>". $user_answer ."</span>";
                                }


                                $fill_in_blank_question_title_user_answer = str_replace( $slug ,$answer_html, $fill_in_blank_question_title_user_answer);
                            }

                            $user_answered = stripslashes( $fill_in_blank_question_title_user_answer );
                        }

                        $question_image = isset( $question["question_image"] ) && $question["question_image"] != '' ? $question["question_image"] : '';
                        $td_value_html .= '<tr>';
                        $td_value_html .= '<td style="border: 1px solid #ccc;padding: 10px 11px 9px 6px;"><strong>'.__('Question',AYS_QUIZ_NAME).' ' . $index . ':</strong><br/>' . (do_shortcode(stripslashes($question["question"])));

                        if( $question_image != '' ){
                            $td_value_html .= '<div><img style="max-width: 300px;max-height:300px; width: auto; height:auto;" src="' . $question_image . '"></div>';
                        }

                        $td_value_html .= '</td>';

                        $td_value_html .= '<td style="border: 1px solid #ccc;padding: 10px 11px 9px 6px;"><strong>'.__('User answer',AYS_QUIZ_NAME).':</strong><br/><div class="'.$ans_point_class.'">' . htmlspecialchars_decode(do_shortcode(stripslashes($user_answered)));

                        if( $user_answered_images != '' ){
                            $td_value_html .= '<div>' . $user_answered_images . '</div>';
                        }

                        $td_value_html .= '</div></td>';

                        $td_value_html .= '<td style="border: 1px solid #ccc;padding: 10px 11px 9px 6px;"><strong>'.__('Point',AYS_QUIZ_NAME).':</strong><br/><p class="'.$ans_point_class.'" style="font-weight: 600; text-align:center;">'.$ans_point.'</p></td>';
                        $td_value_html .= '</tr>';

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

        if( is_object($choices) ){
            return array(
                'message' => __( "Something went wrong.", AYS_QUIZ_NAME ),
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

    public static function get_user_matching_answered($user_choice, $key, $incorrect_matches){
        global $wpdb;
        $answers_table = $wpdb->prefix . "aysquiz_answers";
        $choices = $user_choice->$key;
        $text = array();

        foreach ( $choices as $answer_id => $choice ) {
            if ( $choice === '' ) {
                $text[] = array(
                    'answer' => __( "The user has not answered.", AYS_QUIZ_NAME ),
                    'correct' => false
                );
            } elseif ( isset( $incorrect_matches[ $choice ] ) && !empty( $incorrect_matches[ $choice ] ) ) {
                $text[] = array(
                    'answer' => trim( $incorrect_matches[ $choice ] ),
                    'correct' => false
                );
            } else {
                $result = $wpdb->get_var("SELECT options FROM {$answers_table} WHERE id={$answer_id}");
                $answer_options = ! empty( $result ) ? json_decode( $result, true ) : array();
                if ( ! $answer_options ) {
                    $answer_options = array();
                }
                $match = isset( $answer_options['correct_match'] ) && $answer_options['correct_match'] ? $answer_options['correct_match'] : '';

                $user_answerd_value = $wpdb->get_row("SELECT `answer`,`options` FROM {$answers_table} WHERE id={$choice}", "ARRAY_A");

                $user_answer_options = ! empty( $user_answerd_value['options'] ) ? json_decode( $user_answerd_value['options'], true ) : array();
                $user_answer = isset( $user_answerd_value['answer'] ) && $user_answerd_value['answer'] != "" ? esc_attr( $user_answerd_value['answer'] ) : "";
                if ( ! $user_answer_options ) {
                    $user_answer_options = array();
                }
                $user_match = isset( $user_answer_options['correct_match'] ) && $user_answer_options['correct_match'] ? $user_answer_options['correct_match'] : '';
                $if_correct = false;
                if( $user_match == $match ){
                    $if_correct = true;
                    $user_answer = $user_match;
                }

                $text[] = array(
                    'answer' => trim( $user_answer ),
                    'correct' => $if_correct,
                );
            }
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

        if( is_object($choices) ){
            return "";
        }

        $text = array();
        if (is_array($choices)) {
            foreach ($choices as $choice) {
                $result = $wpdb->get_row("SELECT image FROM {$answers_table} WHERE id={$choice}", 'ARRAY_A');
                if(isset($result['image']) && $result['image'] != ''){
                    $text[] = "<img style='max-width: 300px;max-height:300px; width: auto; height:auto;' src='". $result['image'] ."' alt='Answer image'>";
                }
            }
            $text = '' . implode('<br>', $text);
        } else {
            $result = $wpdb->get_row("SELECT image FROM {$answers_table} WHERE id={$choices}", 'ARRAY_A');
            if(isset($result['image']) && $result['image'] != ''){
                $text = "<img style='max-width: 300px;max-height:300px; width: auto; height:auto;' src='". $result['image'] ."' alt='Answer image'>";
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

    public static function get_user_fill_in_blank_answered($user_choice, $key){

        if($user_choice->$key == "" || empty($user_choice->$key)){
            $choices = array(
                'message' => __( "The user has not answered this question.", AYS_QUIZ_NAME ),
                'status' => false
            );
        }else{
            $choices = (array)$user_choice->$key;
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

    public static function get_correct_answers_for_matching_type($id){
        global $wpdb;
        $answers_table = $wpdb->prefix . "aysquiz_answers";
        $results = $wpdb->get_results("SELECT options FROM {$answers_table} WHERE question_id={$id}");
        $answers = array();
        foreach ( $results as $result ) {
            $answer_options = isset( $result->options ) && ! empty( $result->options ) ? $result->options : '';
            $answer_options = json_decode( $answer_options, true );
            if ( ! $answer_options ) {
                $answer_options = array();
            }

            $match = isset( $answer_options['correct_match'] ) && $answer_options['correct_match'] ? $answer_options['correct_match'] : '';
            $answers[] = $match;
        }

        return $answers;
    }

    public static function get_correct_answer_images($id){
        global $wpdb;
        $answers_table = $wpdb->prefix . "aysquiz_answers";
        $correct_answers = $wpdb->get_results("SELECT image FROM {$answers_table} WHERE correct=1 AND question_id={$id}");
        $text = "";
        foreach ($correct_answers as $key => $correct_answer) {
            if ($correct_answer->image){
                $text .= "<img style='max-width: 300px;max-height:300px; width: auto; height:auto;' src='". $correct_answer->image ."' alt='Answer image'>";
            }
        }
        return $text;
    }

    public static function get_correct_answer_keyword($question_id, $answer_id){
        global $wpdb;
        $answers_table = $wpdb->prefix . "aysquiz_answers";

        $question_id = absint( sanitize_text_field( $question_id ) );
        $answer_id = absint( sanitize_text_field( $answer_id ) );

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

    public static function check_text_answer_correctness($question_id, $answer, $answer_id, $calc_method, $options = array()){
        global $wpdb;
        $answers_table = $wpdb->prefix . "aysquiz_answers";
        $question_id = absint(intval($question_id));

        $sql_answer_id = "";
        if( isset( $answer_id ) && !empty( $answer_id ) ){
            $sql_answer_id = " AND `id` = " . absint( sanitize_text_field( $answer_id ) );
        }

        $checks = $wpdb->get_row("SELECT COUNT(*) AS qanak, answer, weight FROM {$answers_table} WHERE question_id={$question_id}" . $sql_answer_id, ARRAY_A);

        $checks['answer'] = (isset( $checks['answer'] ) && $checks['answer'] != "") ? $checks['answer'] : "";
        
        $correct_answers = $checks['answer'];

        // Disable strip slashes for answers
        $options['quiz_disable_answer_stripslashes'] = isset($options['quiz_disable_answer_stripslashes']) ? sanitize_text_field( $options['quiz_disable_answer_stripslashes'] ) : 'off';
        $quiz_disable_answer_stripslashes = (isset($options['quiz_disable_answer_stripslashes']) && $options['quiz_disable_answer_stripslashes'] == 'on') ? true : false;

        if ( !$quiz_disable_answer_stripslashes ) {
            $answer = stripslashes($answer);
        }
        
        $answer_weight = floatval($checks['weight']);
        $answer_res = false;
        $text_type = self::text_answer_is($question_id);
        $correct = false;

        // Enable case sensitive text
        $options['enable_case_sensitive_text'] = isset($options['enable_case_sensitive_text']) ? sanitize_text_field( $options['enable_case_sensitive_text'] ) : 'off';
        $enable_case_sensitive_text = (isset($options['enable_case_sensitive_text']) && sanitize_text_field( $options['enable_case_sensitive_text'] ) == 'on') ? true : false;

        if( $text_type == 'text' || $text_type == 'short_text' ){
            if ( $enable_case_sensitive_text ) {
                $correct_answers = $checks['answer'];
            }
        }

        if($text_type == 'date'){
            // if(Quiz_Maker_Admin::validateDate($answer, 'Y-m-d')){
            if(date('Y-m-d', strtotime($correct_answers)) == date('Y-m-d', strtotime($answer))){
                $correct = true;
            }
            // }
        }elseif($text_type != 'number'){
            $correct_answers = explode('%%%', $correct_answers);
            foreach($correct_answers as $c){
                if ($enable_case_sensitive_text) {
                    if(trim($c) === trim($answer)){
                        $correct = true;
                        break;
                    }
                } else {
                    if( mb_strtolower(trim($c), 'UTF-8') === mb_strtolower(trim($answer), 'UTF-8') ){
                        $correct = true;
                        break;
                    }
                }
            }
        }else{
            if($correct_answers == mb_strtolower(trim($answer))){
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

    public static function is_matching_answer($question_id){
        global $wpdb;
        $questions_table = $wpdb->prefix . "aysquiz_questions";
        $question_id = absint( $question_id );
        $get_answers = $wpdb->get_var("SELECT type FROM {$questions_table} WHERE id={$question_id}");

        if ( $get_answers === 'matching' ) {
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

    public static function is_fill_in_blank_answer($question_id){
        global $wpdb;
        $questions_table = $wpdb->prefix . "aysquiz_questions";
        $question_id = absint(intval($question_id));
        $get_answers = $wpdb->get_var("SELECT type FROM {$questions_table} WHERE id={$question_id}");
        if ($get_answers == 'fill_in_blank') {
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

    public static function get_question_max_weight_by_keyword($question_id, $keyword){
        global $wpdb;
        $sql = "SELECT keyword, MAX(weight) AS max_weight FROM {$wpdb->prefix}aysquiz_answers WHERE question_id = $question_id AND keyword IN ( $keyword )  GROUP BY keyword";
        $result = $wpdb->get_results($sql, "ARRAY_A");
        return $result;
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
        $seconds = absint($seconds);

        /*** get the days ***/
        $days = (int)($seconds / 86400);
        if ($days > 0) {
            $ret .= sprintf( __('%s days', AYS_QUIZ_NAME ), $days ) . " ";
        }

        /*** get the hours ***/
        $hours = (int)(($seconds - ($days * 86400)) / 3600);
        if ($hours > 0) {
            $ret .= sprintf( __('%s hours', AYS_QUIZ_NAME ), $hours ) . " ";
        }

        /*** get the minutes ***/
        $minutes = (int)(($seconds - $days * 86400 - $hours * 3600) / 60);
        if ($minutes > 0) {
            $ret .= sprintf( __('%s minutes', AYS_QUIZ_NAME ), $minutes ) . " ";
        }

        /*** get the seconds ***/
        $seconds = (int)($seconds - ($days * 86400) - ($hours * 3600) - ($minutes * 60));
        if ($seconds > 0) {
            $ret .= sprintf( __('%s seconds', AYS_QUIZ_NAME ), $seconds );
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

        if ( isset( $report_result['options'] ) && $report_result['options'] != "" ) {
            $options = json_decode($report_result['options'], true);
        }else {
            $options = array();
        }
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

        $avg_result = $wpdb->get_var($sql);
        if ( is_null( $avg_result ) || empty( $avg_result ) ) {
            $avg_result = 0;
        }

        $result = round($avg_result);
        return $result;
    }

    public static function ays_get_average_of_points_by_user($id, $user_id){
        global $wpdb;

        $id      = absint( $id );
        $user_id = absint( $user_id );

        $sql = "SELECT AVG(`points`) FROM {$wpdb->prefix}aysquiz_reports WHERE `quiz_id` = {$id} AND `user_id` = ".$user_id;
        $avg_result = $wpdb->get_var($sql);
        
        if ( is_null( $avg_result ) || empty( $avg_result ) ) {
            $avg_result = 0;
        }

        $result = round( $avg_result, 2 );

        return $result;
    }

    public static function ays_get_average_of_rates($id){
        global $wpdb;
        $sql = "SELECT AVG(`score`) AS avg_score FROM {$wpdb->prefix}aysquiz_rates WHERE quiz_id= $id";
        $result = $wpdb->get_var($sql);

        if ( is_null( $result ) || empty( $result ) ) {
            $result = 0;
        }

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
                            <div class='panel-body'><div>". stripslashes(nl2br($reason)) ."</div></div>
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
        return $count . (($count == 1) ? (" $text") : (" {$text}s"));
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

        if ( is_null( $content ) ) {
            return $content;
        }

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

    public static function get_questions_categories_data($q_ids, $q_cat_ids){
        global $wpdb;

        if($q_ids == ''){
            return array();
        }
        $sql = "SELECT DISTINCT c.id, c.title, c.description
                FROM {$wpdb->prefix}aysquiz_categories c
                JOIN {$wpdb->prefix}aysquiz_questions q
                ON c.id = q.category_id
                WHERE q.id IN ({$q_ids}) AND q.category_id IN ({$q_cat_ids})";

        $result = $wpdb->get_results($sql, 'ARRAY_A');
        $cats = array();

        foreach($result as $res){
            $cats[$res['id']] = array(
                'title' => $res['title'],
                'description' => $res['description'],
            );
        }

        return $cats;
    }

    public static function get_questions_tags($q_ids){
        global $wpdb;

        if($q_ids == ''){
            return array();
        }

        $sql = "SELECT qt.id, qt.title
                FROM {$wpdb->prefix}aysquiz_question_tags AS qt
                JOIN {$wpdb->prefix}aysquiz_questions AS q
                ON (find_in_set(qt.id,q.tag_id)>0)
                WHERE q.id IN ({$q_ids})";

        $result = $wpdb->get_results($sql, 'ARRAY_A');

        $cats = array();

        foreach($result as $res){
            if (!in_array( $res['id'] , $cats)) {
                $cats[$res['id']] = $res['title'];
            }
        }

        return $cats;
    }

    public static function ays_set_quiz_texts( $plugin_name, $settings ){

        /*
         * Get Quiz buttons texts from database
         */

        $settings_buttons_texts = $settings->ays_get_setting('buttons_texts');
        if($settings_buttons_texts){
            $settings_buttons_texts = json_decode( $settings_buttons_texts, true, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES );
        }else{
            $settings_buttons_texts = array();
        }

        $ays_start_button           = (isset($settings_buttons_texts['start_button']) && $settings_buttons_texts['start_button'] != '') ? esc_attr( stripslashes($settings_buttons_texts['start_button'])) : 'Start' ;
        $ays_next_button            = (isset($settings_buttons_texts['next_button']) && $settings_buttons_texts['next_button'] != '') ? esc_attr( stripslashes($settings_buttons_texts['next_button'])) : 'Next' ;
        $ays_previous_button        = (isset($settings_buttons_texts['previous_button']) && $settings_buttons_texts['previous_button'] != '') ? esc_attr( stripslashes($settings_buttons_texts['previous_button'])) : 'Prev' ;
        $ays_clear_button           = (isset($settings_buttons_texts['clear_button']) && $settings_buttons_texts['clear_button'] != '') ? esc_attr( stripslashes($settings_buttons_texts['clear_button'])) : 'Clear' ;
        $ays_finish_button          = (isset($settings_buttons_texts['finish_button']) && $settings_buttons_texts['finish_button'] != '') ? esc_attr( stripslashes($settings_buttons_texts['finish_button'])) : 'Finish' ;
        $ays_see_result_button      = (isset($settings_buttons_texts['see_result_button']) && $settings_buttons_texts['see_result_button'] != '') ? esc_attr( stripslashes($settings_buttons_texts['see_result_button'])) : 'See Result' ;
        $ays_restart_quiz_button    = (isset($settings_buttons_texts['restart_quiz_button']) && $settings_buttons_texts['restart_quiz_button'] != '') ? esc_attr( stripslashes($settings_buttons_texts['restart_quiz_button'])) : 'Restart quiz' ;
        $ays_send_feedback_button   = (isset($settings_buttons_texts['send_feedback_button']) && $settings_buttons_texts['send_feedback_button'] != '') ? esc_attr( stripslashes($settings_buttons_texts['send_feedback_button'])) : 'Send feedback' ;
        $ays_load_more_button       = (isset($settings_buttons_texts['load_more_button']) && $settings_buttons_texts['load_more_button'] != '') ? esc_attr( stripslashes($settings_buttons_texts['load_more_button'])) : 'Load more' ;
        $ays_exit_button            = (isset($settings_buttons_texts['exit_button']) && $settings_buttons_texts['exit_button'] != '') ? esc_attr( stripslashes($settings_buttons_texts['exit_button'])) : 'Exit' ;
        $ays_check_button           = (isset($settings_buttons_texts['check_button']) && $settings_buttons_texts['check_button'] != '') ? esc_attr( stripslashes($settings_buttons_texts['check_button'])) : 'Check' ;
        $ays_login_button           = (isset($settings_buttons_texts['login_button']) && $settings_buttons_texts['login_button'] != '') ? esc_attr( stripslashes($settings_buttons_texts['login_button'])) : 'Log In' ;

        $ays_chain_quiz_button    = (isset($settings_buttons_texts['chain_quiz_button']) && $settings_buttons_texts['chain_quiz_button'] != '') ? esc_attr( stripslashes($settings_buttons_texts['chain_quiz_button'])) : 'Next quiz' ;
        $ays_chain_quiz_see_result_button    = (isset($settings_buttons_texts['chain_quiz_see_result_button']) && $settings_buttons_texts['chain_quiz_see_result_button'] != '') ? esc_attr( stripslashes($settings_buttons_texts['chain_quiz_see_result_button'])) : 'See Result' ;

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

        if ($ays_chain_quiz_button === 'Next quiz') {
            $ays_chain_quiz_button_text = __('Next quiz', $plugin_name);
        }else{
            $ays_chain_quiz_button_text = $ays_chain_quiz_button;
        }

        if ($ays_chain_quiz_see_result_button === 'See Result') {
            $ays_chain_quiz_see_result_button_text = __('See Result', $plugin_name);
        }else{
            $ays_chain_quiz_see_result_button_text = $ays_chain_quiz_see_result_button;
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
            'nextChainQuiz'      => $ays_chain_quiz_button_text,
            'seeResultChainQuiz' => $ays_chain_quiz_see_result_button_text,
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

        return version_compare ( (string)$_fv, (string)$_sv, $operator );
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

        $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_categories ORDER BY {$wpdb->prefix}aysquiz_categories.title ASC";

        $result = $wpdb->get_results($sql, 'ARRAY_A');

        return $result;
    }

    public static function get_question_tags( $flag = false ){
        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_question_tags";

        if ( $flag ) {
            $sql .= " WHERE `status` = 'published' ";
        }
        $sql .= " ORDER BY `title` ASC ";

        $result = $wpdb->get_results($sql, 'ARRAY_A');

        return $result;
    }

    public static function get_question_tag_ids(){
        global $wpdb;

        $sql = "SELECT tag_ids FROM {$wpdb->prefix}aysquiz_question";

        $result = $wpdb->get_results($sql, 'ARRAY_A');

        return $result;
    }

    public static function get_actual_reports_count(){
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_question_reports WHERE resolved = 0";
        $result = $wpdb->get_var($sql);

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
                case 'question_categories':
                    $listtable_title_length = (isset($options['question_categories_title_length']) && intval($options['question_categories_title_length']) != 0) ? absint(sanitize_text_field($options['question_categories_title_length'])) : 5;
                    break;
                case 'quiz_categories':
                    $listtable_title_length = (isset($options['quiz_categories_title_length']) && intval($options['quiz_categories_title_length']) != 0) ? absint(sanitize_text_field($options['quiz_categories_title_length'])) : 5;
                    break;
                case 'quiz_reviews':
                    $listtable_title_length = (isset($options['quiz_reviews_title_length']) && intval($options['quiz_reviews_title_length']) != 0) ? absint(sanitize_text_field($options['quiz_reviews_title_length'])) : 5;
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
            // CURLOPT_HTTPHEADER => array(
            //    "response_type: webapplications",
            //    "Content-Type: application/json"
            // ),
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
        // $url = 'https://www.googleapis.com/oauth2/v4/token';
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
            $question_bank_questions[$val] = (isset( $quests[$val] ) && !empty($quests[$val])) ? $quests[$val] : array();
            if ( empty($question_bank_questions[$val]) ) {
                continue;
            }
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

        // Minimum length of a number field
        $options['enable_question_number_min_length'] = isset($options['enable_question_number_min_length']) ? sanitize_text_field( $options['enable_question_number_min_length'] ) : 'off';
        $res['enable_question_number_min_length'] = (isset($options['enable_question_number_min_length']) && sanitize_text_field( $options['enable_question_number_min_length'] ) == 'on') ? true : false;

        // Length
        $res['question_number_min_length'] = ( isset($options['question_number_min_length']) && sanitize_text_field( $options['question_number_min_length'] ) != '' ) ? intval( sanitize_text_field( $options['question_number_min_length'] ) ) : '';

        // Show error message
        $options['enable_question_number_error_message'] = isset($options['enable_question_number_error_message']) ? sanitize_text_field( $options['enable_question_number_error_message'] ) : 'off';
        $res['enable_question_number_error_message'] = (isset($options['enable_question_number_error_message']) && sanitize_text_field( $options['enable_question_number_error_message'] ) == 'on') ? true : false;

        // Message
        $res['question_number_error_message'] = ( isset($options['question_number_error_message']) && sanitize_text_field( $options['question_number_error_message'] ) != '' ) ? stripslashes( sanitize_text_field( $options['question_number_error_message'] ) ) : '';

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

        if ( ! $is_elementor ) {
            $is_elementor = ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'elementor_ajax' ) ? true : false;
        }

        return $is_elementor;
    }

    public static function ays_quiz_is_editor(){
        $is_editor = false;
        if( isset( $_GET['action'] ) && ( $_GET['action'] == 'add' || $_GET['action'] == 'edit' ) ){
            if ( isset( $_GET['post'] ) && absint( $_GET['post'] ) > 0 ) {
                $is_editor = true;
            }
        }

        return $is_editor;
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

    public static function ays_question_numbering( $numbering , $total ){
        $keyword_arr = array();
        switch ($numbering) {
            case '1.':
                $char_min_val = '1';
                $char_max_val = $total;
                for($x = $char_min_val; $x <= $char_max_val; $x++){
                    $keyword_arr[] = $x .".";
                }

                break;
            case '1)':
                $char_min_val = '1';
                $char_max_val = $total;
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

    public static function ays_quiz_set_cookie($attr, $flag = true){
        if( $flag ){
            $cookie_name = $attr['name'].$attr['id'];
        } else {
            $cookie_name = $attr['name'];
        }
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

    public static function ays_quiz_check_cookie($attr, $flag = true){
        if( $flag ){
            $cookie_name = $attr['name'].$attr['id'];
        } else {
            $cookie_name = $attr['name'];
        }
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
        $extension = pathinfo($image_url, PATHINFO_EXTENSION);

        $extension_end = "";
        if ( $extension && $extension != "" ) {
            $extension_end = ".".$extension;
        }

        $filename = current_time("Y-m-d") . "-" . time() . $counter . $extension_end;
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
            $attach_url = wp_get_attachment_image_url($attach_id, 'full');
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
        }else{
            $ays_user_roles = array('administrator');
        }

        if(is_user_logged_in()){
            $current_user = wp_get_current_user();
            $current_user_roles = $current_user->roles;
            if( empty( $current_user_roles ) ){
                if( array_key_exists( 'administrator', $current_user->caps ) ){
                    return true;
                }
            }
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

        return $capability;
    }

    public static function ays_quiz_is_exists_needle_tag( $str, $needle ) {

        $exists_flag = false;

        if ( isset( $str ) && ! is_null( $str ) && $str != '' ) {

            if ( isset( $needle ) && ! is_null( $needle ) && $needle != '' ) {

                $is_exists_needle = strpos( $str, $needle );

                if ( $is_exists_needle !== false ) {
                    $exists_flag = true;
                }
            }
        }

        return $exists_flag;
    }

    public static function convertFromCP1252( $string ) {
        $search = array(
            // '&',
            // '<',
            // '>',
            // '"',
            '×',
            '·',
            chr(212),
            chr(213),
            chr(210),
            chr(211),
            chr(209),
            chr(208),
            chr(201),
            chr(145),
            chr(146),
            chr(147),
            chr(148),
            chr(151),
            chr(150),
            chr(133),
            '′',
            chr(194),
        );
         $replace = array(
            // '&amp;',
            // '&lt;',
            // '&gt;',
            // '&quot;',
            '&#215;',
            '&#183;',
            '&#8216;',
            '&#8217;',
            '&#8220;',
            '&#8221;',
            '&#8211;',
            '&#8212;',
            '&#8230;',
            '&#8216;',
            '&#8217;',
            '&#8220;',
            '&#8221;',
            '&#8211;',
            '&#8212;',
            '&#8230;',
            '&#8242;',
            ''
        );
        // return str_replace($search, $replace, $string);
        $string = preg_replace('/_x([0-9a-fA-F]{4})_/', '&#x$1;', $string);
        return $string;
    }

    public static function ays_set_quiz_fields_placeholders_texts(){

        /*
         * Get Quiz fields placeholders from database
         */

        $settings_placeholders_texts = Quiz_Maker_Settings_Actions::ays_get_setting('fields_placeholders');
        if($settings_placeholders_texts){
            // $settings_placeholders_texts = json_decode(stripcslashes($settings_placeholders_texts), true);
            $settings_placeholders_texts = json_decode( $settings_placeholders_texts, true, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES );
        }else{
            $settings_placeholders_texts = array();
        }

        $quiz_fields_placeholder_name  = (isset($settings_placeholders_texts['quiz_fields_placeholder_name']) && $settings_placeholders_texts['quiz_fields_placeholder_name'] != '') ? stripslashes( esc_attr( $settings_placeholders_texts['quiz_fields_placeholder_name'] ) ) : 'Name';

        $quiz_fields_placeholder_eamil = (isset($settings_placeholders_texts['quiz_fields_placeholder_eamil']) && $settings_placeholders_texts['quiz_fields_placeholder_eamil'] != '') ? stripslashes( esc_attr( $settings_placeholders_texts['quiz_fields_placeholder_eamil'] ) ) : 'Email';

        $quiz_fields_placeholder_phone = (isset($settings_placeholders_texts['quiz_fields_placeholder_phone']) && $settings_placeholders_texts['quiz_fields_placeholder_phone'] != '') ? stripslashes( esc_attr( $settings_placeholders_texts['quiz_fields_placeholder_phone'] ) ) : 'Phone Number';

        $quiz_fields_label_name  = (isset($settings_placeholders_texts['quiz_fields_label_name']) && $settings_placeholders_texts['quiz_fields_label_name'] != '') ? stripslashes( esc_attr( $settings_placeholders_texts['quiz_fields_label_name'] ) ) : 'Name';

        $quiz_fields_label_eamil = (isset($settings_placeholders_texts['quiz_fields_label_eamil']) && $settings_placeholders_texts['quiz_fields_label_eamil'] != '') ? stripslashes( esc_attr( $settings_placeholders_texts['quiz_fields_label_eamil'] ) ) : 'Email';

        $quiz_fields_label_phone = (isset($settings_placeholders_texts['quiz_fields_label_phone']) && $settings_placeholders_texts['quiz_fields_label_phone'] != '') ? stripslashes( esc_attr( $settings_placeholders_texts['quiz_fields_label_phone'] ) ) : 'Phone Number';


        if ($quiz_fields_placeholder_name === 'Name') {
            $quiz_fields_placeholder_name_text = __('Name', AYS_QUIZ_NAME);
        }else{
            $quiz_fields_placeholder_name_text = $quiz_fields_placeholder_name;
        }

        if ($quiz_fields_placeholder_eamil === 'Email') {
            $quiz_fields_placeholder_eamil_text = __('Email', AYS_QUIZ_NAME);
        }else{
            $quiz_fields_placeholder_eamil_text = $quiz_fields_placeholder_eamil;
        }

        if ($quiz_fields_placeholder_phone === 'Phone Number') {
            $quiz_fields_placeholder_phone_text = __('Phone Number', AYS_QUIZ_NAME);
        }else{
            $quiz_fields_placeholder_phone_text = $quiz_fields_placeholder_phone;
        }

        if ($quiz_fields_label_name === 'Name') {
            $quiz_fields_label_name_text = __('Name', AYS_QUIZ_NAME);
        }else{
            $quiz_fields_label_name_text = $quiz_fields_label_name;
        }

        if ($quiz_fields_label_eamil === 'Email') {
            $quiz_fields_label_eamil_text = __('Email', AYS_QUIZ_NAME);
        }else{
            $quiz_fields_label_eamil_text = $quiz_fields_label_eamil;
        }

        if ($quiz_fields_label_phone === 'Phone Number') {
            $quiz_fields_label_phone_text = __('Phone Number', AYS_QUIZ_NAME);
        }else{
            $quiz_fields_label_phone_text = $quiz_fields_label_phone;
        }

        $texts = array(
            'namePlaceholder'       => $quiz_fields_placeholder_name_text,
            'emailPlaceholder'      => $quiz_fields_placeholder_eamil_text,
            'phonePlaceholder'      => $quiz_fields_placeholder_phone_text,
            'nameLabel'             => $quiz_fields_label_name_text,
            'emailLabel'            => $quiz_fields_label_eamil_text,
            'phoneLabel'            => $quiz_fields_label_phone_text,
        );

        return $texts;
    }

    public static function ays_set_quiz_message_variables_data( $id, $quiz ){

        /*
         * Quiz message variables for Start Page
         */

        // Quiz options 
        $options = ( json_decode($quiz['options'], true) != null ) ? json_decode($quiz['options'], true) : array();

        // General Setting's Options
        $quiz_settings = new Quiz_Maker_Settings_Actions( AYS_QUIZ_NAME );
        $general_settings_options = ($quiz_settings->ays_get_setting('options') === false) ? json_encode(array()) : $quiz_settings->ays_get_setting('options');
        $settings_options = json_decode(stripcslashes($general_settings_options), true);

        // Do not store IP adressess 
        $disable_user_ip = (isset($settings_options['disable_user_ip']) && $settings_options['disable_user_ip'] == 'on') ? true : false;

        // Quiz Timer
        $quiz_timer = (isset( $options['timer'] ) && intval($options['timer']) != 0 && $options['timer'] != "") ? absint( sanitize_text_field( $options['timer'] ) ) : 100;

        // Quiz title
        $quiz_title = (isset( $quiz['title'] ) && $quiz['title'] != "") ? stripslashes($quiz['title']) : "";

        // Quiz create date
        $quiz_creation_date = (isset( $quiz['create_date'] ) && $quiz['create_date'] != '') ? sanitize_text_field( $quiz['create_date'] ) : "";
        if( $quiz_creation_date != "" ){
            $quiz_creation_date = date_i18n( get_option( 'date_format' ), strtotime( $quiz_creation_date ) );
        }

        // Quiz Author ID
        $quiz_current_author = (isset( $quiz['author_id'] ) && $quiz['author_id'] != '') ? absint( sanitize_text_field( $quiz['author_id'] ) ) : "";
        // if ( isset( $options['author'] ) && is_string($options['author']) ) {
        //     $quiz_current_author_data = (isset( $options['author'] ) && $options['author'] != '') ? json_decode($options['author'], true) : array();
        // } else {
        //     $options_author = isset( $options['author'] ) ? (array)$options['author'] : array();
        //     $quiz_current_author_data = (is_array( $options_author ) && empty( $options_author )) ? $options_author : array();
        // }

        if($disable_user_ip){
            $user_ip = '';
        }else{
            $user_ip = self::get_user_ip();
        }

        $current_user_ip = $user_ip;

        $question_ids = self::get_quiz_questions_count($id);

        $questions_count = 0;
        if ( ! empty( $question_ids ) ) {
            $questions_count = count($question_ids);
        }

        $user_first_name = '';
        $user_last_name = '';
        $user_nickname = '';
        $user_display_name = '';
        $user_wordpress_email = '';
        $user_wordpress_roles = '';
        $user_id = get_current_user_id();
        if($user_id != 0){
            $usermeta = get_user_meta( $user_id );
            if($usermeta !== null){
                $user_first_name = (isset($usermeta['first_name'][0]) && sanitize_text_field( $usermeta['first_name'][0] != '') ) ? sanitize_text_field( $usermeta['first_name'][0] ) : '';
                $user_last_name  = (isset($usermeta['last_name'][0]) && sanitize_text_field( $usermeta['last_name'][0] != '') ) ? sanitize_text_field( $usermeta['last_name'][0] ) : '';
                $user_nickname   = (isset($usermeta['nickname'][0]) && sanitize_text_field( $usermeta['nickname'][0] != '') ) ? sanitize_text_field( $usermeta['nickname'][0] ) : '';
            }

            $current_user_data = get_userdata( $user_id );
            if ( ! is_null( $current_user_data ) && $current_user_data ) {
                $user_display_name = ( isset( $current_user_data->data->display_name ) && $current_user_data->data->display_name != '' ) ? sanitize_text_field( $current_user_data->data->display_name ) : "";

                $user_wordpress_email = ( isset( $current_user_data->data->user_email ) && $current_user_data->data->user_email != '' ) ? sanitize_text_field( $current_user_data->data->user_email ) : "";

                $user_wordpress_roles = ( isset( $current_user_data->roles ) && ! empty( $current_user_data->roles ) ) ? $current_user_data->roles : "";

                if ( !empty( $user_wordpress_roles ) && $user_wordpress_roles != "" ) {
                    if ( is_array( $user_wordpress_roles ) ) {
                        $user_wordpress_roles = implode(",", $user_wordpress_roles);
                    }
                }
            }
        }

        $current_quiz_author = __( "Unknown", AYS_QUIZ_NAME );
        $current_quiz_author_email = "";

        $super_admin_email = get_option('admin_email');

        if( !is_null( $quiz_current_author ) && $quiz_current_author > 0 ){
            $current_quiz_user_data = get_userdata( $quiz_current_author );
            if ( ! is_null( $current_quiz_user_data ) && $current_quiz_user_data ) {
                $current_quiz_author = ( isset( $current_quiz_user_data->data->display_name ) && $current_quiz_user_data->data->display_name != '' ) ? sanitize_text_field( $current_quiz_user_data->data->display_name ) : "";
                $current_quiz_author_email = ( isset( $current_quiz_user_data->data->user_email ) && $current_quiz_user_data->data->user_email != '' ) ? sanitize_text_field( $current_quiz_user_data->data->user_email ) : "";
            }
        }

        $message_data = array(
            'quiz_name'             => $quiz_title,
            'time'                  => self::secondsToWords($quiz_timer),
            'user_first_name'       => $user_first_name,
            'user_last_name'        => $user_last_name,
            'questions_count'       => $questions_count,
            'user_nickname'         => $user_nickname,
            'user_display_name'     => $user_display_name,
            'user_wordpress_email'  => $user_wordpress_email,
            'user_wordpress_roles'  => $user_wordpress_roles,

            'quiz_creation_date'            => $quiz_creation_date,
            'current_quiz_author'           => $current_quiz_author,
            'current_user_ip'               => $current_user_ip,
            'current_quiz_author_email'     => $current_quiz_author_email,
            'admin_email'                   => $super_admin_email,


        );

        return $message_data;
    }

    public static function ays_quiz_get_active_coupon( $quiz_id, $options ) {
        global $wpdb;

        $quizes_table = esc_sql( $wpdb->prefix . 'aysquiz_quizes' );

        //Get Quiz Coupon
        //Enable Bulk Coupon
        $options->quiz_enable_coupon = isset($options->quiz_enable_coupon) ? sanitize_text_field($options->quiz_enable_coupon) : 'off';
        $quiz_enable_coupon = (isset($options->quiz_enable_coupon) && $options->quiz_enable_coupon == 'on') ? true : false;

        //Active/Inactive coupons
        $active_inactive_coupons = (isset($options->quiz_coupons_array) && $options->quiz_coupons_array != '') ? $options->quiz_coupons_array : array();

        if(!empty($active_inactive_coupons)){

            $quiz_active_coupons = (isset( $active_inactive_coupons->quiz_active_coupons) && !empty( $active_inactive_coupons->quiz_active_coupons)) ?  $active_inactive_coupons->quiz_active_coupons : array();

            $quiz_inactive_coupons = (isset( $active_inactive_coupons->quiz_inactive_coupons) && !empty( $active_inactive_coupons->quiz_inactive_coupons)) ?  $active_inactive_coupons->quiz_inactive_coupons : array();

        }

        $active_coupons_array = (array)$quiz_active_coupons;
        $inactive_coupons_array = (array)$quiz_inactive_coupons;
        $active_inactive_coupons_array = (array)$active_inactive_coupons;

        //Shuffle Active Coupons
        $active_coupon = '';

        if($quiz_enable_coupon && ! empty( $active_coupons_array ) ){

            $active_coupon_key = array_rand($active_coupons_array);
            $active_coupon = $active_coupons_array[$active_coupon_key];

            if(isset($active_coupons_array[$active_coupon_key])){
                unset($active_coupons_array[$active_coupon_key]);
            }

            $inactive_coupons_array[] = $active_coupon;


            $active_inactive_coupons_array['quiz_active_coupons'] = $active_coupons_array;
            $active_inactive_coupons_array['quiz_inactive_coupons'] = $inactive_coupons_array;

            $options->quiz_coupons_array = $active_inactive_coupons_array;

            if(isset($options->quiz_coupons_array)){

                $quiz_result = $wpdb->update(
                    $quizes_table,
                    array(
                        'options' => json_encode( $options ),
                    ),
                    array( 'id' => $quiz_id ),
                    array( '%s' ),
                    array( '%d' )
                );
            }
        }

        return $active_coupon;
    }

    public static function get_chained_quiz_by_id($id){
        global $wpdb;

        $sql = "SELECT *
                FROM {$wpdb->prefix}aysquiz_chainedquizzes
                WHERE id=" . $id;

        $quiz = $wpdb->get_row($sql, 'ARRAY_A');

        return $quiz;
    }

    public static function ays_color_inverse( $color ){
        $color = str_replace( '#', '', $color );
        if ( strlen( $color ) != 6 ){
            return '#000000';
        }

        $rgb = '';
        for ( $x = 0; $x < 3; $x++ ){
            $c = 255 - hexdec( substr( $color, ( 2 * $x ), 2 ) );
            $c = ( $c < 0 ) ? 0 : dechex( $c );
            $rgb .= ( strlen( $c ) < 2 ) ? '0' . $c : $c;
        }

        return '#'.$rgb;
    }

    public static function get_custom_fields_for_shortcodes(){
        global $wpdb;

        $attribute_table = $wpdb->prefix ."aysquiz_attributes";

        $sql = "SELECT * FROM {$attribute_table} WHERE published=1";
        $results = $wpdb->get_results( $sql, 'ARRAY_A' );

        $custom_fields = array();

        foreach ($results as $key => $result) {
            //Custom fields name
            $custom_fields_name = (isset($result['name']) && $result['name'] != '') ? sanitize_text_field(stripslashes($result['name'])) : '';

            $result_attr_options = (isset($result['attr_options']) && $result['attr_options'] != '') ? $result['attr_options'] : '';

            $attr_options = array();
            if ( $result_attr_options && is_string($result_attr_options) && $result_attr_options != "" ) {
                $attr_options = json_decode($result_attr_options, true);
            }

            //show custom fields
            $attr_options['show_custom_fields'] = (isset($attr_options['show_custom_fields']) && $attr_options['show_custom_fields'] == 'on' ) ? 'on' : 'off';
            $show_custom_fields = (isset($attr_options['show_custom_fields']) && $attr_options['show_custom_fields'] == 'on' ) ? true : false;

            //show custom fields user page
            $attr_options['show_custom_fields_user_page'] = (isset($attr_options['show_custom_fields_user_page']) && $attr_options['show_custom_fields_user_page'] == 'on' ) ? 'on' : 'off';
            $show_custom_fields_user_page = (isset($attr_options['show_custom_fields_user_page']) && $attr_options['show_custom_fields_user_page'] == 'on' ) ? true : false;

            //show custom fields user results
            $attr_options['show_custom_fields_user_results'] = (isset($attr_options['show_custom_fields_user_results']) && $attr_options['show_custom_fields_user_results'] == 'on' ) ? 'on' : 'off';
            $show_custom_fields_user_results = (isset($attr_options['show_custom_fields_user_results']) && $attr_options['show_custom_fields_user_results'] == 'on' ) ? true : false;

            //show custom fields quiz results
            $attr_options['show_custom_fields_quiz_results'] = (isset($attr_options['show_custom_fields_quiz_results']) && $attr_options['show_custom_fields_quiz_results'] == 'on' ) ? 'on' : 'off';
            $show_custom_fields_quiz_results = (isset($attr_options['show_custom_fields_quiz_results']) && $attr_options['show_custom_fields_quiz_results'] == 'on' ) ? true : false;

            //Show Custom Fields Individual Leaderboard
            $attr_options['show_custom_fields_individual_leaderboard'] = isset($attr_options['show_custom_fields_individual_leaderboard']) ? sanitize_text_field($attr_options['show_custom_fields_individual_leaderboard']) : 'off';
            $individual_leaderboard = (isset($attr_options['show_custom_fields_individual_leaderboard']) && $attr_options['show_custom_fields_individual_leaderboard'] == 'on') ? true : false;

            //Show Custom Fields Leaderboard By Quiz Category
            $attr_options['show_custom_fields_leaderboard_by_quiz_cat'] = isset($attr_options['show_custom_fields_leaderboard_by_quiz_cat']) ? sanitize_text_field($attr_options['show_custom_fields_leaderboard_by_quiz_cat']) : 'off';
            $leaderboard_by_quiz_cat = (isset($attr_options['show_custom_fields_leaderboard_by_quiz_cat']) && $attr_options['show_custom_fields_leaderboard_by_quiz_cat'] == 'on') ? true : false;

            if ( function_exists("mb_strtolower") ) {
                $lowercase_custom_field = mb_strtolower(sanitize_text_field($custom_fields_name));
            } else {
                $lowercase_custom_field = strtolower(sanitize_text_field($custom_fields_name));
            }

            $new_custom_field_string = str_replace(' ', "_", $lowercase_custom_field);

            //add to the new array
            if($show_custom_fields){
                $show_custom_fields_user_page ? $custom_fields['user_page'][$new_custom_field_string] = $custom_fields_name : '';

                $show_custom_fields_user_results ?  $custom_fields['user_results'][$new_custom_field_string] = $custom_fields_name : '';

                $show_custom_fields_quiz_results ? $custom_fields['quiz_results'][$new_custom_field_string] = $custom_fields_name : '';

                $individual_leaderboard ? $custom_fields['individual_leaderboard'][$new_custom_field_string] = $custom_fields_name : '';
                
                $leaderboard_by_quiz_cat ? $custom_fields['leaderboard_by_quiz_cat'][$new_custom_field_string] = $custom_fields_name : '';
            }
        }
        return $custom_fields;
    }

    /**
     * Initialize the processing timer.
     *
     * @since 2.6.0
     */
    public static function init_process_times( $process_times ) {
        $process_times['started'] = time();
        $process_times['limit']   = intval( ini_get( 'max_execution_time' ) );
        if ( empty( $process_times['limit'] ) ) {
            $process_times['limit'] = 60;
        }
    }

    /**
     * Check if the process timer is out of time.
     *
     * @since 2.6.0
     */
    public static function out_of_timer() {
        $process_times['current_time'] = time();

        $process_times['ticks']   = $process_times['current_time'] - $process_times['started'];
        $process_times['percent'] = ( $process_times['ticks'] / $process_times['limit'] ) * 100;

        // If we are over 80% of the allowed processing time or over 10 seconds then finish up and return.
        if ( ( $process_times['percent'] >= 80 ) || ( $process_times['ticks'] > 10 ) ) {
            return true;
        }

        return false;
    }

    /**
     * Remove the processing transient for instance.
     *
     * @since 2.6.0
     *
     * @param string $transient_key Transient key to identify transient.
     */
    public static function remove_transient( $transient_key = '' ) {
        if ( ! empty( $transient_key ) ) {
            $options_key = 'aysquiz_' . $transient_key;
            $options_key = str_replace( '-', '_', $options_key );
            return delete_option( $options_key );
        }
    }

    /**
     * Get the processing transient for instance.
     *
     * @since 2.6.0
     *
     * @param string $transient_key Transient key to identify transient.
     * @return mixed transient data.
     */
    public static function get_transient( $transient_key = '' ) {
        if ( ! empty( $transient_key ) ) {
            $options_key = 'aysquiz_' . $transient_key;
            $options_key = str_replace( '-', '_', $options_key );
            return get_option( $options_key );
        }
    }

    /**
     * Set the processing transient for instance.
     *
     * @since 3.1.0
     *
     * @param string $transient_key Transient key to identify transient.
     * @param array  $transient_data Array for transient data.
     */
    public static function set_option_cache( $transient_key = '', $transient_data = '' ) {
        if ( ! empty( $transient_key ) ) {
            $options_key = 'aysquiz_' . $transient_key;
            $options_key = str_replace( '-', '_', $options_key );

            if ( ! empty( $transient_data ) ) {
                update_option( $options_key, $transient_data );
            } else {
                delete_option( $options_key );
            }
        }
    }

    public static function ays_quiz_if_current_user_created( $db_table = "aysquiz_questions" ) {
        global $wpdb;

        $db_table_name = esc_sql( $wpdb->prefix . $db_table );

        $current_user = get_current_user_id();
        $sql = "SELECT * FROM ". $db_table_name ." WHERE `author_id` = ".$current_user." ";

        $result = $wpdb->get_results( $sql, 'ARRAY_A' );

        return $result;
    }

    // Retrieves the attachment full size from the file URL
    public static function ays_quiz_question_get_image_full_size_url_by_url( $image_url ) {
        global $wpdb;

        if ( !empty( $image_url ) ) {

            $re = '/-\d+[Xx]\d+\./';
            $subst = '.';

            $full_site_question_image = preg_replace($re, $subst, $image_url, 1);

            $attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $full_site_question_image ));
            if ( !is_null( $attachment ) && !empty( $attachment ) ) {
                $image_url = $full_site_question_image;
            }
        }

        return $image_url; 
    }

    // Retrieves the attachment ID from the file URL
    public static function ays_quiz_get_image_id_by_url( $image_url ) {
        global $wpdb;

        $image_alt_text = "";
        if ( !empty( $image_url ) ) {

            $re = '/-\d+[Xx]\d+\./';
            $subst = '.';

            $image_url = preg_replace($re, $subst, $image_url, 1);

            $attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url ));
            if ( !is_null( $attachment ) && !empty( $attachment ) ) {

                $image_id = (isset( $attachment[0] ) && $attachment[0] != "") ? absint(  $attachment[0] ) : "";
                if ( $image_id != "" ) {
                    $image_alt_text = self::ays_quiz_get_image_alt_text_by_id( $image_id );
                }
            }
        }

        return $image_alt_text; 
    }

    public static function ays_quiz_get_image_alt_text_by_id( $image_id ) {

        $image_data = "";
        if ( $image_id != "" ) {

            $result = get_post_meta($image_id, '_wp_attachment_image_alt', TRUE);
            if ( $result && $result != "" ) {
                $image_data = esc_attr( $result );
            }
        }

        return $image_data; 
    }

    public static function get_published_questions_by($key, $value) {
        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_questions WHERE {$key} = {$value};";

        $results = $wpdb->get_row( $sql, 'ARRAY_A' );

        return $results;
    }

    public static function get_published_questions_count($ids) {
        global $wpdb;

        if ( $ids == "" || empty($ids) || is_null($ids) ) {
            return array();
        }

        $sql = "SELECT COUNT(*) as res_count FROM {$wpdb->prefix}aysquiz_questions WHERE id IN({$ids}) AND published = 1;";

        $results = $wpdb->get_row( $sql, 'ARRAY_A' );

        return $results;
    }

    public static function get_published_questions_id_arr($ids) {
        global $wpdb;

        $sql = "SELECT id FROM {$wpdb->prefix}aysquiz_questions WHERE id IN({$ids}) AND published = 1 ORDER BY find_in_set(id,'".$ids."');";

        $results = $wpdb->get_results( $sql, 'ARRAY_A' );

        if ( !is_null( $results ) && !empty($results) ) {
            $new_question_ids = array();
            foreach ($results as $key => $value) {
                $published_question_id = (isset( $value['id'] ) && intval( $value['id'] ) > 0 && $value['id'] != "") ? $value['id'] : null;

                if ( !is_null( $published_question_id ) ) {
                    $new_question_ids[] = $published_question_id;
                }
            }

            if ( !empty( $new_question_ids ) ) {
                $results = $new_question_ids;
            } else {
                $results = explode(",", $ids);
            }
        } else {
            $results = explode(",", $ids);
        }

        return $results;
    }

    public static function ays_quiz_if_quiz_trashed($id) {
        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_quizes WHERE id = {$id} AND published = 2;";

        $results = $wpdb->get_results( $sql, 'ARRAY_A' );

        return $results;
    }

    public static function ays_quiz_ays_quiz_get_quizzes(){
        global $wpdb;
        $quiz_table = esc_sql( $wpdb->prefix . "aysquiz_quizes" );

        $sql = "SELECT id,title
                FROM {$quiz_table} WHERE `published` = 1";

        $quizzes = $wpdb->get_results( $sql , "ARRAY_A" );

        return $quizzes;
    }

    // Keywords max, point, percent 
    public static function keyword_data_by_user_answer( $answered_keywords, $all_questions_id_arr, $quiz_id ){
        global $wpdb;

        $quiz_answer_table = esc_sql( $wpdb->prefix."aysquiz_answers" );
        $quiz_quizzes_table = esc_sql( $wpdb->prefix."aysquiz_quizes" );

        $question_ids = (isset( $all_questions_id_arr ) && !empty($all_questions_id_arr) ) ? implode( ",", $all_questions_id_arr ) : '';
        $question_ids_arr = (isset( $all_questions_id_arr ) && !empty($all_questions_id_arr) ) ? $all_questions_id_arr : array();
        // $question_ids_arr = $question_ids != "" ? explode(',', $question_ids) : array();

        //Get keywords from answer table By question ids
        if($question_ids != ''){
            $sql = "SELECT `keyword` FROM {$quiz_answer_table} WHERE `question_id` IN ({$question_ids}) GROUP BY `keyword`";
            $results = $wpdb->get_results( $sql, 'ARRAY_A' );
        }

        // Get all keywords count in one array by keywords 
        $keywords = array();
        $keyword_multiple = array();
        if( is_array( $answered_keywords ) && !empty( $answered_keywords ) ){
            foreach ($answered_keywords as $key => $answered_keyword) {
                $keywords_multiple = array();
                $has_multiple = Quiz_Maker_Data::has_multiple_correct_answers($key);
                
                if($has_multiple){
                    $is_Keyword = array_key_exists( 'keyword', $answered_keywords[$key] );
                    $is_point = array_key_exists( 'point', $answered_keywords[$key] );
                    if(! $is_Keyword && ! $is_point ){
                        foreach ($answered_keywords[$key] as $keywords_key => $keywords_value) {
                            $m_keyword = ( isset( $keywords_value['keyword'] ) && $keywords_value['keyword'] != '' ) ? sanitize_text_field( $keywords_value['keyword'] ) : '';
                            $m_point = ( isset( $keywords_value['point'] ) && $keywords_value['point'] != '' ) ? sanitize_text_field( $keywords_value['point'] ) : '';
                            $keywords_multiple[$m_keyword][] = $m_point;
                        }
                        $keyword_multiple[] = $keywords_multiple;
                    }else{
                        $keyword = ( isset( $answered_keyword['keyword'] ) && $answered_keyword['keyword'] != '' ) ? sanitize_text_field( $answered_keyword['keyword'] ) : '';
                        $point = ( isset( $answered_keyword['point'] ) && $answered_keyword['point'] != '' ) ? intval( $answered_keyword['point'] ) : 0;

                        $keywords[$keyword][] = $point;
                    }             
                }else{
                    $keyword = ( isset( $answered_keyword['keyword'] ) && $answered_keyword['keyword'] != '' ) ? sanitize_text_field( $answered_keyword['keyword'] ) : '';
                    $point = ( isset( $answered_keyword['point'] ) && $answered_keyword['point'] != '' ) ? intval( $answered_keyword['point'] ) : 0;
                    $keywords[$keyword][] = $point;                    
                }
            }
            
        }
        
        $keywords_data = array();
        $quiz_keywords_arr = array();
        foreach ($results as $key => $result) {
            $quiz_keywords = ( isset( $result['keyword'] ) && $result['keyword'] != '' ) ? stripslashes( $result['keyword'] ) : '';
            if(!empty($keyword_multiple)){
                foreach ($keyword_multiple as $key => $keyword_mult) {
                    if($keyword_mult[$quiz_keywords] != ''){
                        $m_point = $keyword_mult[$quiz_keywords];
                        $keywords[$quiz_keywords] = $m_point;
                    }
                    
                }
            }

            if( array_key_exists( $quiz_keywords, $keywords ) ){
                $keywords_data[$quiz_keywords]['user_keyword_point'] = array_sum($keywords[$quiz_keywords]);
            }else{
                $keywords_data[$quiz_keywords]['user_keyword_point'] = 0;
            }

            $quiz_keywords_arr[] = "'".$quiz_keywords."'";
        }

        $quiz_keywords_str = implode( ',', $quiz_keywords_arr );

        $questions_keyword_data = array();
        foreach ($question_ids_arr as $qustion_key => $qustion_id_value) {

            $current_question_keyword_data = self::get_question_max_weight_by_keyword( $qustion_id_value, $quiz_keywords_str );
            if( !empty( $current_question_keyword_data ) ){
                foreach ($current_question_keyword_data as $current_question_keyword_data_key => $current_question_keyword_data_value) {
                    $current_question_keyword = ( isset( $current_question_keyword_data_value['keyword'] ) && $current_question_keyword_data_value['keyword'] != '' ) ? stripslashes( $current_question_keyword_data_value['keyword'] ) : '';
                    $current_question_keyword_max_point = ( isset( $current_question_keyword_data_value['max_weight'] ) && $current_question_keyword_data_value['max_weight'] != '' ) ? intval( $current_question_keyword_data_value['max_weight'] ) : 0;

                    if( !isset($questions_keyword_data[$current_question_keyword]) ){
                        $questions_keyword_data[$current_question_keyword] = $current_question_keyword_max_point;
                    } else {
                        $questions_keyword_data[$current_question_keyword] += $current_question_keyword_max_point;
                    }

                }
            }
        }

        //Get Max Value of each keyword
        $keyword_max_value_sql = "SELECT `keyword`, SUM(`weight`) AS 'count' FROM {$quiz_answer_table} WHERE `keyword` IN ({$quiz_keywords_str}) AND `question_id` IN ({$question_ids}) GROUP BY `keyword`";
        $keyword_max_value_results = $wpdb->get_results( $keyword_max_value_sql, 'ARRAY_A' );

        foreach ($keyword_max_value_results as $key => $keyword_max_value_result) {
            $keyword_max_value_result_keyword = ( isset( $keyword_max_value_result['keyword'] ) && $keyword_max_value_result['keyword'] != '' ) ? stripslashes( $keyword_max_value_result['keyword'] ) : '';

            $keyword_max_value_result_count = ( isset( $keyword_max_value_result['count'] ) && $keyword_max_value_result['count'] != '' ) ? absint( $keyword_max_value_result['count'] ) : 0;

            if($keyword_max_value_result_count > 0){
                $answered_percent_each_keywords = ($keywords_data[$keyword_max_value_result_keyword]['user_keyword_point'] * 100) / $keyword_max_value_result_count;
            }else{
                $answered_percent_each_keywords = 0;
            }

            $keywords_data[$keyword_max_value_result_keyword]['max_point_keyword'] = $keyword_max_value_result_count;
            $keywords_data[$keyword_max_value_result_keyword]['user_keyword_percentage'] = round($answered_percent_each_keywords, 2);            
            $keywords_data[$keyword_max_value_result_keyword]['user_can_get_max_point_keyword'] = isset( $questions_keyword_data[$keyword_max_value_result_keyword] ) && $questions_keyword_data[$keyword_max_value_result_keyword] != "" ? $questions_keyword_data[$keyword_max_value_result_keyword] : 0;            
        }

        return $keywords_data;
    }
 
    public static function personality_result_data_by_user_answer( $answered_keywords, $all_questions_id_arr, $quiz_id, $assign_keywords_texts, $question_cat_ids, $options ){
        global $wpdb;

        $quiz_answer_table = esc_sql( $wpdb->prefix."aysquiz_answers" );
        $quiz_quizzes_table = esc_sql( $wpdb->prefix."aysquiz_quizes" );
        $quiz_questions_table = esc_sql( $wpdb->prefix."aysquiz_questions" );


        $question_ids = (isset( $all_questions_id_arr ) && !empty($all_questions_id_arr) ) ? implode( ",", $all_questions_id_arr ) : '';
        $question_ids_arr = (isset( $all_questions_id_arr ) && !empty($all_questions_id_arr) ) ? $all_questions_id_arr : array();


        $apply_points_to_keywords = (isset( $options['apply_points_to_keywords'] ) && $options['apply_points_to_keywords'] == true ) ? true : false;

        $assign_keywords_text = array();
        foreach ($assign_keywords_texts as $key => $value) {
            if( !array_key_exists( $value->assign_top_keyword, $assign_keywords_text) ){
                $assign_keywords_text[$value->assign_top_keyword] = $value->assign_top_keyword_text;
            }
        }

        $question_cat_results = array();
        //Get keywords from answer table By question ids
        if($question_ids != ''){
            $sql = "SELECT `keyword` FROM {$quiz_answer_table} WHERE `question_id` IN ({$question_ids}) GROUP BY `keyword`";
            $results = $wpdb->get_results( $sql, 'ARRAY_A' );

            $question_cat_ids_str = implode(',', $question_cat_ids);
            $question_cat_results = self::get_questions_categories_data($question_ids, $question_cat_ids_str);
        }

        // Get all keywords count in one array by keywords 
        $keywords = array();
        $keyword_multiple = array();
        if( is_array( $answered_keywords ) && !empty( $answered_keywords ) ){
            foreach ($answered_keywords as $key => $answered_keyword) {
                $keywords_multiple = array();
                $has_multiple = Quiz_Maker_Data::has_multiple_correct_answers($key);

                $question_id_key = $key;

                $answered_keyword_cat_id = isset($answered_keyword['cat_id']) && $answered_keyword['cat_id'] != "" ? $answered_keyword['cat_id'] : 0;
                if( !in_array( $answered_keyword_cat_id, $question_cat_ids ) ){
                    continue;
                }

                $answered_keyword_keyword = isset($answered_keyword['keyword']) && $answered_keyword['keyword'] != "" ? $answered_keyword['keyword'] : "";
                $answered_keyword_point = isset($answered_keyword['point']) && $answered_keyword['point'] != "" ? $answered_keyword['point'] : 0;


                if( $apply_points_to_keywords ){
                    if( isset( $question_cat_results ) && is_array($question_cat_results) && array_key_exists( $answered_keyword_cat_id, $question_cat_results ) ){
                        if( isset( $question_cat_results[$answered_keyword_cat_id]['keywords'] ) && is_array( $question_cat_results[$answered_keyword_cat_id]['keywords'] ) && !array_key_exists( $answered_keyword_keyword, $question_cat_results[$answered_keyword_cat_id]['keywords'] ) ){
                            $question_cat_results[$answered_keyword_cat_id]['keywords'][$answered_keyword_keyword] = $answered_keyword_point;
                        } else {
                            $question_cat_results[$answered_keyword_cat_id]['keywords'][$answered_keyword_keyword] += $answered_keyword_point;
                        }
                    }
                } else {
                    if( isset( $question_cat_results ) && !empty($question_cat_results) ){
                        if( isset( $question_cat_results ) && is_array( $question_cat_results ) && array_key_exists( $answered_keyword_cat_id, $question_cat_results ) ){
                            if( isset( $question_cat_results[$answered_keyword_cat_id]['keywords'] ) && is_array( $question_cat_results[$answered_keyword_cat_id]['keywords'] ) && !array_key_exists( $answered_keyword_keyword, $question_cat_results[$answered_keyword_cat_id]['keywords'] ) ){
                                $question_cat_results[$answered_keyword_cat_id]['keywords'][$answered_keyword_keyword] = 1;
                            } else {
                                $question_cat_results[$answered_keyword_cat_id]['keywords'][$answered_keyword_keyword] += 1;
                            }
                        }
                    }
                }
                
                // if($has_multiple){
                //     $is_Keyword = array_key_exists( 'keyword', $answered_keywords[$key] );
                //     $is_point = array_key_exists( 'point', $answered_keywords[$key] );
                //     if(! $is_Keyword && ! $is_point ){
                //         foreach ($answered_keywords[$key] as $keywords_key => $keywords_value) {
                //             $m_keyword = ( isset( $keywords_value['keyword'] ) && $keywords_value['keyword'] != '' ) ? sanitize_text_field( $keywords_value['keyword'] ) : '';
                //             $m_point = ( isset( $keywords_value['point'] ) && $keywords_value['point'] != '' ) ? sanitize_text_field( $keywords_value['point'] ) : '';
                //             $keywords_multiple[$m_keyword][] = $m_point;
                //         }
                //         $keyword_multiple[] = $keywords_multiple;
                //     }else{
                //         $keyword = ( isset( $answered_keyword['keyword'] ) && $answered_keyword['keyword'] != '' ) ? sanitize_text_field( $answered_keyword['keyword'] ) : '';
                //         $point = ( isset( $answered_keyword['point'] ) && $answered_keyword['point'] != '' ) ? intval( $answered_keyword['point'] ) : 0;

                //         $keywords[$keyword][] = $point;
                //     }             
                // }else{
                //     $keyword = ( isset( $answered_keyword['keyword'] ) && $answered_keyword['keyword'] != '' ) ? sanitize_text_field( $answered_keyword['keyword'] ) : '';
                //     $point = ( isset( $answered_keyword['point'] ) && $answered_keyword['point'] != '' ) ? intval( $answered_keyword['point'] ) : 0;
                //     $keywords[$keyword][] = $point;                    
                // }
            }
        }

        $category_color = array(
            "ays-quiz-personality-result-box-purple",
            "ays-quiz-personality-result-box-yellow",
            "ays-quiz-personality-result-box-green",
            "ays-quiz-personality-result-box-red",
            "ays-quiz-personality-result-box-blue",
        );

        $mayer_html = "";
        $category_color_index = 0;
        foreach ($question_cat_results as $key_cat_id => $mayer_data) {
            
            $cat_title = isset( $mayer_data['title'] ) && $mayer_data['title'] != "" ? esc_attr( $mayer_data['title'] ) : "";
            $cat_description = isset( $mayer_data['description'] ) && $mayer_data['description'] != "" ? self::ays_autoembed( $mayer_data['description'] ) : "";
            $cat_keywords_arr = isset( $mayer_data['keywords'] ) &&  !empty( $mayer_data['keywords'] ) ? $mayer_data['keywords'] : array();

            $keyword_text_1 = "";
            $keyword_text_2 = "";

            $keyword_percentage_1 = "";
            $keyword_percentage_2 = "";

            if( !empty( $cat_keywords_arr ) ){
                // arsort($cat_keywords_arr);
                $two_biggest_keywords_arr = array();
                foreach ($cat_keywords_arr as $key => $value) {
                    if (count($two_biggest_keywords_arr) < 2) {
                        $two_biggest_keywords_arr[$key] = $value;
                    } else {
                        $min_largest = min($two_biggest_keywords_arr);
                        if ($value > $min_largest) {
                            $min_key = array_search($min_largest, $two_biggest_keywords_arr);
                            unset($two_biggest_keywords_arr[$min_key]);
                            $two_biggest_keywords_arr[$key] = $value;
                        }
                    }
                }
                $cat_keywords_arr_count = count($cat_keywords_arr);
                $keyword_index_flag = 0;
                if( $cat_keywords_arr_count == 1 ){
                    $cat_keywords_arr_sum = array_sum($cat_keywords_arr);

                    foreach ($cat_keywords_arr as $_keyword => $_keyword_value) {
                        $keyword_text_2 = isset( $assign_keywords_text[$_keyword] ) && $assign_keywords_text[$_keyword] != "" ? esc_attr( $assign_keywords_text[$_keyword] ) : $_keyword;

                        if( $cat_keywords_arr_sum > 0 ){
                            $keyword_percentage_2 = round ( ( $_keyword_value / $cat_keywords_arr_sum ) * 100 );
                        } else {
                            $keyword_percentage_2 = 0;
                        }

                        $keyword_percentage_2 = $keyword_percentage_2 . "%";
                    }

                } else {
                    $break_flag = false;
                    $cat_keywords_arr_sum = 0;
                    $cat_keywords_arr_sum_index = 0;
                    foreach ($two_biggest_keywords_arr as $__keyword => $__keyword_value) {

                        $cat_keywords_arr_sum += $__keyword_value;

                        if( $cat_keywords_arr_sum_index == 1 ){
                            break;
                        }
                        $cat_keywords_arr_sum_index++;

                    }

                    foreach ($two_biggest_keywords_arr as $_keyword => $_keyword_value) {
                        switch ($keyword_index_flag) {
                            case 0:
                                $keyword_text_1 = isset( $assign_keywords_text[$_keyword] ) && $assign_keywords_text[$_keyword] != "" ? esc_attr( $assign_keywords_text[$_keyword] ) : $_keyword;
                                if( $cat_keywords_arr_sum > 0 ){
                                    $keyword_percentage_1 = round( ( $_keyword_value / $cat_keywords_arr_sum ) * 100 );
                                } else {
                                    $keyword_percentage_1 = 0;
                                }
                                $keyword_percentage_1 = $keyword_percentage_1 . "%";
                                break;
                            case 1:
                                $keyword_text_2 = isset( $assign_keywords_text[$_keyword] ) && $assign_keywords_text[$_keyword] != "" ? esc_attr( $assign_keywords_text[$_keyword] ) : $_keyword;
                                if( $cat_keywords_arr_sum > 0 ){
                                    $keyword_percentage_2 = round( ( $_keyword_value / $cat_keywords_arr_sum ) * 100 );
                                } else {
                                    $keyword_percentage_2 = 0;
                                }
                                $keyword_percentage_2 = $keyword_percentage_2 . "%";
                                break;
                            
                            default:
                                $break_flag = true;
                                break;
                        }

                        if( $break_flag ){
                            break;
                        }

                        $keyword_index_flag++;
                    }
                }
            } else {
                continue;
            }

            $category_color_class = isset($category_color[ $category_color_index ]) && $category_color[ $category_color_index ] != "" ? $category_color[ $category_color_index ] : 'ays-quiz-personality-result-box-purple';

            $is_second_bigger = ( $keyword_percentage_1 < $keyword_percentage_2 ) ? true : false;
            $white_percent_text_class = 'ays-quiz-personality-result-text-white';
            $purple_percent_text_class = 'ays-quiz-personality-result-text-dark-purple';
            $result_bar_width = $keyword_percentage_1;
            $result_bar_position_class = '';
            if ($is_second_bigger) {
                $result_bar_width = $keyword_percentage_2;
                $white_percent_text_class = 'ays-quiz-personality-result-text-dark-purple';
                $purple_percent_text_class = 'ays-quiz-personality-result-text-white';
                $result_bar_position_class = 'ays-quiz-personality-result-progress-end';
            }

            $mayer_html .= '
            <div class="ays-quiz-personality-result-box '. $category_color_class .'">
                <div class="ays-quiz-personality-result-title">'. $cat_title .'</div>
                <div class="ays-quiz-personality-result-description">'. $cat_description .'</div>
                <div class="ays-quiz-personality-result-progress '. $result_bar_position_class. '">
                    <div class="ays-quiz-personality-result-bar" style="width: '.$result_bar_width.';"></div>
                    <div class="ays-quiz-personality-result-percentages">
                        <div class="ays-quiz-personality-result-text-percentage '.$white_percent_text_class.'">'. $keyword_percentage_1 .'</div>
                        <div class="ays-quiz-personality-result-text-percentage '.$purple_percent_text_class.'">'. $keyword_percentage_2 .'</div>
                    </div>
                </div>
                <div class="ays-quiz-personality-result-keyword-box">
                    <div class="">'. $keyword_text_1 .'</div>
                    <div class="ays-quiz-personality-result-keyword-text-color">'. $keyword_text_2 .'</div>
                </div>
            </div>
            ';

            $category_color_index++;
            if( $category_color_index > 5 ){
                $category_color_index = 0;
            }
        }

        return $mayer_html;
    }

    // =============================================================
    // ====================  PayPal And Stripe  ====================
    // ========================    START    ========================

    /*
     * Quiz payments validations
     * */
    public static function add_quiz_payment_usermeta( $payment, $user_id, $quiz_id, $purchase_date = '', $purchased = false ){
        $opts = json_encode( array(
            'quizId' => $quiz_id,
            'purchased' => $purchased,
            'purchaseDate' => $purchase_date
        ) );

        add_user_meta( $user_id, "quiz_" . $payment . "_purchase", $opts );
    }

    public static function get_quiz_payment_usermeta( $payment, $user_id, $quiz_id ){

        $current_usermeta = get_user_meta( $user_id, "quiz_" . $payment . "_purchase" );
        $quiz_payment_usermeta = false;
        if( ! empty( $current_usermeta ) ) {
            foreach ($current_usermeta as $usermeta) {
                if ($quiz_id == json_decode($usermeta, true)['quizId']) {
                    $quiz_payment_usermeta = json_decode($usermeta, true);
                    break;
                }
            }
        }

        return $quiz_payment_usermeta;
    }

    public static function user_paid_postpay( $quiz_id, $payment ){
        global $wpdb;

        $connection = true;
        $session_key = 'ays_quiz_' . $payment . '_purchased_item';
        if( isset( $_SESSION[ $session_key ] ) && isset(  $_SESSION[ $session_key ][ $quiz_id ] ) ){
            if( isset(  $_SESSION[ $session_key ][ $quiz_id ]['order_id'] ) ){
                $sql = "SELECT status FROM ". $wpdb->prefix ."aysquiz_orders WHERE id=". absint( $_SESSION[ $session_key ][ $quiz_id ]['order_id'] );
                $order_status = $wpdb->get_var( $sql );
                if( $order_status != 'finished'){
                    $_SESSION[ $session_key ][ $quiz_id ]['status'] = $order_status;
                    $connection = false;
                }
            }
        }

        return $connection;
    }

    public static function is_not_logged_in_user_paid( $quiz_id, $payment, $args ){
        $session_key = 'ays_quiz_' . $payment . '_purchase';
        if( isset( $_SESSION[ $session_key ] ) && isset( $_SESSION[ $session_key ][ $quiz_id ] ) ){
            if( $_SESSION[ $session_key ][ $quiz_id ] == true ){
                if ( $payment == "paypal" && isset( $_SESSION[ "ays_quiz_paypal_purchased_item" ][ $quiz_id ]["status"] ) && $_SESSION[ "ays_quiz_paypal_purchased_item" ][ $quiz_id ]["status"] == "started") {
                    $connection = true;
                } else {
                    $connection = false;
                }
            }else{
                $connection = true;
            }
        }else{
            $_SESSION[ $session_key ][ $quiz_id ] = false;
            $connection = true;
        }

        return $connection;
    }

    public static function is_logged_in_user_paid( $user_id, $quiz_id, $payment, $args ){
        $current_usermeta = get_user_meta( $user_id, "quiz_" . $payment . "_purchase" );
        if( ! empty( $current_usermeta ) ) {
            $quiz_payment_usermeta = self::get_quiz_payment_usermeta( $payment, $user_id, $quiz_id );

            if ( $quiz_payment_usermeta !== false ) {
                if( isset( $quiz_payment_usermeta['purchaseDate'] ) && Quiz_Maker_Admin::validateDate( $quiz_payment_usermeta['purchaseDate'], 'Y-m-d H:i:s' ) ) {

                    $payment_subscribtion_duration = isset( $args['subsctiptionDuration'] ) ? $args['subsctiptionDuration'] : '';
                    $payment_subscribtion_duration_by = isset( $args['subsctiptionDurationBy'] ) ? $args['subsctiptionDurationBy'] : '';

                    $subscribtion_expires = strtotime($quiz_payment_usermeta['purchaseDate'] . ' +' . $payment_subscribtion_duration . ' ' . $payment_subscribtion_duration_by);
                    if (current_time('timestamp') > $subscribtion_expires) {
                        $connection = true;
                    } else {
                        if ( isset( $quiz_payment_usermeta['purchased'] ) && $quiz_payment_usermeta['purchased'] == true) {
                            $connection = false;
                        } else {
                            $connection = true;
                        }
                    }
                }else{
                    if (isset( $quiz_payment_usermeta['purchased'] ) && $quiz_payment_usermeta['purchased'] == true) {
                        $connection = false;
                    } else {
                        $connection = true;
                    }
                }
            } else {
                self::add_quiz_payment_usermeta( $payment, $user_id, $quiz_id );
                $connection = true;
            }
        } else {
            self::add_quiz_payment_usermeta( $payment, $user_id, $quiz_id );
            $connection = true;
        }

        return $connection;
    }

    public static function get_payment_connection( $payment, $type, $payment_term, $quiz_id, $args ){

        $session_key = 'ays_quiz_' . $payment . '_purchase';
        $current_user = wp_get_current_user();
        $connection = false;
        $payment_subscribtion_duration = isset( $args['subsctiptionDuration'] ) ? $args['subsctiptionDuration'] : '';
        $payment_subscribtion_duration_by = isset( $args['subsctiptionDurationBy'] ) ? $args['subsctiptionDurationBy'] : '';

        if( is_user_logged_in() ){
            switch( $payment_term ) {
                case "onetime":
                    if($type == 'prepay'){
                        $connection = self::is_not_logged_in_user_paid( $quiz_id, $payment, array() );
                    }elseif ($type == 'postpay') {
                        $connection = self::user_paid_postpay( $quiz_id, $payment );
                    }
                break;
                case "subscribtion":
                    if($type == 'prepay'){
                        $connection = self::is_logged_in_user_paid( $current_user->data->ID, $quiz_id, $payment, array(
                            'subsctiptionDuration' => $payment_subscribtion_duration,
                            'subsctiptionDurationBy' => $payment_subscribtion_duration_by,
                        ) );
                    }elseif ($type == 'postpay') {
                        $connection = self::user_paid_postpay( $quiz_id, $payment );
                    }
                break;
                case "lifetime":
                default:
                    if($type == 'prepay'){
                        $connection = self::is_logged_in_user_paid( $current_user->data->ID, $quiz_id, $payment, array() );
                    }elseif ($type == 'postpay') {
                        $connection = self::user_paid_postpay( $quiz_id, $payment );
                    }
                break;
            }
        }else{
            $connection = self::is_not_logged_in_user_paid( $quiz_id, $payment, array() );
        }

        return $connection;
    }

    // =============================================================
    // ====================  PayPal And Stripe  ====================
    // =========================    End    =========================

    public static function ays_quiz_generate_message_vars_html( $quiz_message_vars ) {
        $content = array();
        $var_counter = 0; 

        $content[] = '<div class="ays-quiz-message-vars-box">';
            $content[] = '<div class="ays-quiz-message-vars-icon">';
                $content[] = '<div>';
                    $content[] = '<i class="ays_fa ays_fa_link"></i>';
                $content[] = '</div>';
                $content[] = '<div>';
                    $content[] = '<span>'. __("Message Variables" , AYS_QUIZ_NAME) .'</span>';
                    $content[] = '<a class="ays_help" data-toggle="tooltip" data-html="true" title="'. __("Insert your preferred message variable into the editor by clicking." , AYS_QUIZ_NAME) .'">';
                        $content[] = '<i class="ays_fa ays_fa_info_circle"></i>';
                    $content[] = '</a>';
                $content[] = '</div>';
            $content[] = '</div>';
            $content[] = '<div class="ays-quiz-message-vars-data">';
                foreach($quiz_message_vars as $var => $var_name){
                    $var_counter++;
                    $content[] = '<label class="ays-quiz-message-vars-each-data-label">';
                        $content[] = '<input type="radio" class="ays-quiz-message-vars-each-data-checker" hidden id="ays_quiz_message_var_count_'. $var_counter .'" name="ays_quiz_message_var_count">';
                        $content[] = '<div class="ays-quiz-message-vars-each-data">';
                            $content[] = '<input type="hidden" class="ays-quiz-message-vars-each-var" value="'. $var .'">';
                            $content[] = '<span>'. $var_name .'</span>';
                        $content[] = '</div>';
                    $content[] = '</label>';
                }
            $content[] = '</div>';
        $content[] = '</div>';

        $content = implode( '', $content );

        return $content;
    }

    /**
     * Is server side render request.
     * @return bool
     */
    public static function isServerSideRenderRequest() {
        return (
            defined( 'REST_REQUEST' ) &&
            REST_REQUEST &&
            isset( $_GET['context'] ) &&
            ( 'edit' === $_GET['context'] )
        );
    }

    public static function ays_quiz_translate_content($content) {
        $in = str_replace("\n", "-ays-quiz-break-line-", $content);
        $out = preg_replace_callback("/\[:(.*?)\[:]/", function($part){
            $language_slug = explode('-', get_bloginfo("language"))[0];
            preg_match("/\[\:".$language_slug."\](.*?)\[\:/is", $part[0], $out);
            return (is_array($out) && isset($out[1])) ? $out[1] : $part[0];
        }, $in);
        $out = str_replace("-ays-quiz-break-line-", "\n", $out);
        return $out;
    }

    public static function get_quiz_results_count_by_id_for_quiz_demo($id, $data) {

        if ( is_null($id) || $id <= 0 ) {
            return $data;
        }

        $res_count =  (isset( $data['res_count'] ) && $data['res_count'] != "") ? absint( $data['res_count'] ) : 0; 

        switch ( $id ) {
            // Free
            case 4:
                $res_count += 29381;
                break;
            case 10:
                $res_count += 4763;
                break;
            case 7:
                $res_count += 3284;
                break;
            case 9:
                $res_count += 2362;
                break;
            case 8:
                $res_count += 2593;
                break;
            case 6:
                $res_count += 718;
                break;
            case 55:
                $res_count += 325;
                break;
            case 29:
                $res_count += 1949;
                break;
            case 24:
                $res_count += 834;
                break;
            case 27:
                $res_count += 589;
                break;
            case 30:
                $res_count += 562;
                break;
            case 31:
                $res_count += 446;
                break;

            // PRO
            case 19:
                $res_count += 9259;
                break;
            case 57:
                $res_count += 362;
                break;
            case 38:
                $res_count += 1883;
                break;
            case 36:
                $res_count += 3084;
                break;
            case 22:
                $res_count += 11;
                break;
            case 25:
                $res_count += 939;
                break;
            case 33:
                $res_count += 333;
                break;
            case 32:
                $res_count += 5463;
                break;
            case 41:
                $res_count += 723;
                break;
            case 59:
                $res_count += 176;
                break;

            // Free

            // PRO
            case 56:
            case 46:
            case 35:
            case 51:
            case 47:
            case 48:
            case 50:
            case 49:
            case 60:
            default:
                break;
        }

        $data['res_count'] = $res_count + 1273;

        return $data;
    }

    public static function ays_quiz_get_image_full_size_url_by_url( $image_url ) {
        global $wpdb;

        $image_full_size = "";
        if ( !empty( $image_url ) ) {

            $re = '/-\d+[Xx]\d+\./';
            $subst = '.';

            $image_url = preg_replace($re, $subst, $image_url, 1);

            $attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url ));
            if ( !is_null( $attachment ) && !empty( $attachment ) ) {

                $image_id = (isset( $attachment[0] ) && $attachment[0] != "") ? absint(  $attachment[0] ) : "";
                if ( $image_id != "" ) {
                    $image_full_size = self::ays_quiz_get_image_full_size_by_id( $image_id );
                }
            } 
            elseif( !is_null( $attachment ) && empty( $attachment ) ){
                $image_full_size = $image_url;
            }
        }

        return $image_full_size; 
    }

    public static function ays_quiz_get_image_full_size_by_id( $image_id ) {

        $image_data = "";
        if ( $image_id != "" ) {

            $result = wp_get_attachment_image_src( $image_id, 'full' );
            if ( !is_null( $result ) && $result && !empty($result) ) {
                $image_data = (isset( $result[0] ) && $result[0] != "") ? esc_url($result[0]) : "";
            }
        }

        return $image_data; 
    }

    public static function ays_quiz_current_result_by_category($options, $correctness, $question_ids, $calc_method){
        $quests = array();
        $questions_cats = array();
        $quiz_questions_ids = $question_ids;
        $question_bank_by_categories1 = array();
        $calculate_score = $calc_method;
        $display_score = $calc_method;

        $questions_categories = Quiz_Maker_Data::get_questions_categories( implode( ',', $quiz_questions_ids ) );
        $quest_s = Quiz_Maker_Data::get_quiz_questions_by_ids($quiz_questions_ids);
        foreach($quest_s as $quest){
            $quests[$quest['id']] = $quest;
        }

        foreach($quiz_questions_ids as $key => $question_id){
            $questions_cats[$quests[$question_id]['category_id']][$question_id] = null;

            $is_checkbox = Quiz_Maker_Data::is_checkbox_answer($question_id);
            $is_matching_answer = Quiz_Maker_Data::is_matching_answer($question_id);
            $is_fill_in_blank = Quiz_Maker_Data::is_fill_in_blank_answer($question_id);
            $answers_weight = Quiz_Maker_Data::get_question_answers_weight($question_id);

            if( $is_fill_in_blank ){

                $key_value = "question_id_" . $question_id;
                $questions_answers_ids_arr_data = isset( $options->user_answered->$key_value ) && !empty( $options->user_answered->$key_value ) ? array_keys( (array)$options->user_answered->$key_value) : array();

                $questions_answers_ids_arr = array();
                if( !empty( $questions_answers_ids_arr_data ) ){
                    $questions_answers_ids_arr[ $question_id ] = $questions_answers_ids_arr_data;
                }

                $answer_max_weights[$question_id] = Quiz_Maker_Data::get_answers_fill_in_blank_max_weight($question_id, $questions_answers_ids_arr);
            } elseif( $is_matching_answer ) {
                $answer_max_weights[$question_id] = $answers_weight;
            } else {
                $answer_max_weights[$question_id] = Quiz_Maker_Data::get_answers_max_weight($question_id, $is_checkbox);
            }

        }

        $keywords_arr = array();
        $points_keywords_arr = array();

        $new_correctness = array();
        $quiz_weight_correctness = array();
        $quiz_weight_points = array();
        $corrects_count = 0;
        $corrects_count_by_cats = array();
        foreach($questions_cats as $cat_id => &$q_ids){
            $corrects_count_by_cats[$cat_id] = 0;
            foreach($correctness as $question_id => $item){
                if( array_key_exists( strval($question_id), $q_ids ) ){
                    switch($calculate_score){
                        case "by_correctness":
                            if($item){
                                $corrects_count_by_cats[$cat_id]++;
                            }
                        break;
                        case "by_points":
                            if($item == floatval($answer_max_weights[$question_id])){
                                $corrects_count_by_cats[$cat_id]++;
                            }
                        break;
                        default:
                            if($item){
                                $corrects_count_by_cats[$cat_id]++;
                            }
                        break;
                    }
                }
            }
        }

        foreach($correctness as $question_id => $item){
            $question_weight = Quiz_Maker_Data::get_question_weight($question_id);
            $new_correctness[strval($question_id)] = $question_weight * floatval($item);
            $quiz_weight_points[strval($question_id)] = $question_weight * floatval($answer_max_weights[$question_id]);
            $quiz_weight_correctness[strval($question_id)] = $question_weight;
            switch($calculate_score){
                case "by_correctness":
                    if($item){
                        $corrects_count++;
                    }
                break;
                case "by_points":
                    if($item == floatval($answer_max_weights[$question_id])){
                        $corrects_count++;
                    }
                break;
                default:
                    if($item){
                        $corrects_count++;
                    }
                break;
            }
        }

        $quiz_weight_new_correctness_by_cats = array();
        $quiz_weight_correctness_by_cats = array();
        $quiz_weight_points_by_cats = array();

        $questions_count_by_cats = array();
        foreach($questions_cats as $cat_id => &$q_ids){
            foreach($q_ids as $q_id => &$val){
                $val = array_key_exists($q_id, $new_correctness) ? $new_correctness[$q_id] : false;
                $quiz_weight_new_correctness_by_cats[$cat_id][$q_id] = $val;
                if( Quiz_Maker_Data::is_question_not_influence($q_id) ){
                    continue;
                }

                if ( isset( $quiz_weight_correctness[$q_id] ) && sanitize_text_field( $quiz_weight_correctness[$q_id] ) != '' ) {
                    $quiz_weight_correctness_by_cats[$cat_id][$q_id] = $quiz_weight_correctness[$q_id];
                }
                if ( isset( $quiz_weight_points[$q_id] ) && sanitize_text_field( $quiz_weight_points[$q_id] ) != '' ) {
                    $quiz_weight_points_by_cats[$cat_id][$q_id] = $quiz_weight_points[$q_id];
                }

            }
            $questions_count_by_cats[$cat_id] = count($q_ids);
        }

        $final_score_by_cats = array();
        $quiz_weight_cats = array();
        $correct_answered_count_cats = array();
        $correct_answered_count_cats_arr_length = array();
        $cat_score_is_decimal = false;
        $final_score_is_decimal = false;

        foreach($quiz_weight_new_correctness_by_cats as $cat_id => $q_ids){

            if ( ! isset( $quiz_weight_correctness_by_cats[$cat_id] ) ) {
                continue;
            }
            $quiz_weight_correctness_by_cats[$cat_id] = array_filter($quiz_weight_correctness_by_cats[$cat_id], "strlen");

            switch($calculate_score){
                case "by_correctness":
                    $quiz_weight_cat = array_sum($quiz_weight_correctness_by_cats[$cat_id]);
                    $quiz_weight_cats[$cat_id] = array_sum($quiz_weight_correctness_by_cats[$cat_id]);
                break;
                case "by_points":
                    $quiz_weight_cat = array_sum($quiz_weight_points_by_cats[$cat_id]);
                    $quiz_weight_cats[$cat_id] = array_sum($quiz_weight_points_by_cats[$cat_id]);
                break;
                default:
                    $quiz_weight_cat = array_sum($quiz_weight_correctness_by_cats[$cat_id]);
                    $quiz_weight_cats[$cat_id] = array_sum($quiz_weight_correctness_by_cats[$cat_id]);
                break;
            }

            // $correct_answered_count_cat = array_sum($q_ids);
            $correct_answered_count_cats[$cat_id] = array_sum($q_ids);
            $correct_answered_count_cats_arr_length[$cat_id] = count($quiz_weight_correctness_by_cats[$cat_id]);

            if(floatval($quiz_weight_cat) == 0){
                $final_score_by_cats[$cat_id] = floatval(0);
            }else{
                // $final_score_by_cats[$cat_id] = floatval(floor(($correct_answered_count_cat / $quiz_weight_cat) * 100));
                //$final_score_by_cats[$cat_id] = floatval(floor((intval($correct_answered_count_cats[$cat_id]) / floatval($quiz_weight_cat) ) * 100));
                $final_score_by_cats[$cat_id] = floatval(floor((($correct_answered_count_cats[$cat_id]) / floatval($quiz_weight_cat) ) * 100));
                $final_score_by_cats[$cat_id] = round($final_score_by_cats[$cat_id], 2);
            }
        }

        switch($calculate_score){
            case "by_correctness":
                $quiz_weight = array_sum($quiz_weight_correctness);
            break;
            case "by_points":
                $quiz_weight = array_sum($quiz_weight_points);
            break;
            default:
                $quiz_weight = array_sum($quiz_weight_correctness);
            break;
        }

        $score_by_cats = array();
        foreach($final_score_by_cats as $cat_id => $cat_score){
            switch($display_score){
                case "by_correctness":
                    $score_by_cats[$cat_id] = array(
                        'score' => $corrects_count_by_cats[$cat_id] . " / " . $questions_count_by_cats[$cat_id],
                        'avg_cat_score' => "",
                        'categoryName' => $questions_categories[$cat_id],
                    );
                break;
                case "by_points":
                    $correct_answered_count_cats_1 = 0;
                    $correct_answered_count_cats_2 = 0;
                    if( $correct_answered_count_cats_arr_length[$cat_id] == 0 ){
                        $correct_answered_count_cats_1 = 0;
                    }else {
                        $correct_answered_count_cats_1 = floatval($correct_answered_count_cats[$cat_id] / $correct_answered_count_cats_arr_length[$cat_id]);
                        $correct_answered_count_cats_1 = round( $correct_answered_count_cats_1, 2 );
                    }

                    if( $quiz_weight_cats[$cat_id] == 0 ){
                        $correct_answered_count_cats_2 = 0;
                    }else {
                        $correct_answered_count_cats_2 = floatval($quiz_weight_cats[$cat_id] / $correct_answered_count_cats_arr_length[$cat_id]);
                        $correct_answered_count_cats_2 = round( $correct_answered_count_cats_2, 2 );
                    }

                    $score_by_cats[$cat_id] = array(
                        // 'score' => $correct_answered_count_cat[$cat_id] . " / " . $quiz_weight_cats[$cat_id],
                        'score' => $correct_answered_count_cats[$cat_id] . " / " . $quiz_weight_cats[$cat_id],
                        'avg_cat_score' => $correct_answered_count_cats_1 . " / " . $correct_answered_count_cats_2,
                        'categoryName' => $questions_categories[$cat_id],
                    );
                break;
                case "by_percentage":
                    $score_by_cats[$cat_id] = array(
                        'score' => $cat_score . "%",
                        'avg_cat_score' => "",
                        'categoryName' => $questions_categories[$cat_id],
                    );
                break;
                default:
                    $score_by_cats[$cat_id] = array(
                        'score' => $cat_score . "%",
                        'avg_cat_score' => "",
                        'categoryName' => $questions_categories[$cat_id],
                    );
                break;
            }
        }

        if(empty($score_by_cats)){
            $result_score_by_categories = '';
            $avg_result_score_by_categories = '';
        }else{
            $result_score_by_categories = '<div class="ays_result_by_cats">';
            foreach($score_by_cats as $cat_id => $cat){

                $categoryName = isset($cat['categoryName']) && $cat['categoryName'] != "" ? esc_attr( $cat['categoryName'] ) : " — ";
                $categoryScore = isset($cat['score']) && $cat['score'] != "" ? esc_attr( $cat['score'] ) : "";

                $result_score_by_categories .= '<div class="ays_result_by_cat">
                    <strong class="ays_result_by_cat_name">'. $categoryName .':</strong>
                    <span class="ays_result_by_cat_score">'. $categoryScore .'</span>
                </div>';
            }
            $result_score_by_categories .= '</div>';
            $result_score_by_categories = str_replace(array("\r\n", "\n", "\r"), "", $result_score_by_categories);
        }

        return $result_score_by_categories;

    }


    public static function ays_quiz_current_result_by_tag($options, $correctness, $question_ids, $calc_method, $display_score){
        $quests = array();
        $questions_cats = array();
        $quiz_questions_ids = $question_ids;
        $question_bank_by_categories1 = array();
        $calculate_score = $calc_method;
        // $display_score = $calc_method;

        $questions_categories = Quiz_Maker_Data::get_questions_tags( implode( ',', $quiz_questions_ids ) );

        $quest_s = Quiz_Maker_Data::get_quiz_questions_by_ids($quiz_questions_ids);
        foreach($quest_s as $quest){
            $question_tag_ids = isset( $quest['tag_id'] ) && $quest['tag_id'] != "" ? sanitize_text_field( $quest['tag_id'] ) : "";
            if ( empty( $question_tag_ids ) ) {
               continue;
            }

            $question_tag_ids_arr = explode(",", $question_tag_ids);

            $quests[$quest['id']] = $question_tag_ids_arr;
        }

        foreach($quiz_questions_ids as $key => $question_id){
            if( isset( $quests[$question_id] ) && !empty($quests[$question_id]) ){
                foreach ($quests[$question_id] as $__key => $__tag_id) {
                    $questions_cats[$__tag_id][$question_id] = null;
                }
            }

            $is_checkbox = Quiz_Maker_Data::is_checkbox_answer($question_id);
            $is_matching_answer = Quiz_Maker_Data::is_matching_answer($question_id);
            $is_fill_in_blank = Quiz_Maker_Data::is_fill_in_blank_answer($question_id);
            $answers_weight = Quiz_Maker_Data::get_question_answers_weight($question_id);

            if( $is_fill_in_blank ){

                $key_value = "question_id_" . $question_id;
                $questions_answers_ids_arr_data = isset( $options->user_answered->$key_value ) && !empty( $options->user_answered->$key_value ) ? array_keys( (array)$options->user_answered->$key_value) : array();

                $questions_answers_ids_arr = array();
                if( !empty( $questions_answers_ids_arr_data ) ){
                    $questions_answers_ids_arr[ $question_id ] = $questions_answers_ids_arr_data;
                }

                $answer_max_weights[$question_id] = Quiz_Maker_Data::get_answers_fill_in_blank_max_weight($question_id, $questions_answers_ids_arr);
            } elseif( $is_matching_answer ) {
                $answer_max_weights[$question_id] = $answers_weight;
            } else {
                $answer_max_weights[$question_id] = Quiz_Maker_Data::get_answers_max_weight($question_id, $is_checkbox);
            }

        }

        $keywords_arr = array();
        $points_keywords_arr = array();

        $new_correctness = array();
        $quiz_weight_correctness = array();
        $quiz_weight_points = array();
        $corrects_count = 0;
        $corrects_count_by_cats = array();
        foreach($questions_cats as $cat_id => &$q_ids){
            $corrects_count_by_cats[$cat_id] = 0;
            foreach($correctness as $question_id => $item){
                if( array_key_exists( strval($question_id), $q_ids ) ){
                    switch($calculate_score){
                        case "by_correctness":
                            if($item){
                                $corrects_count_by_cats[$cat_id]++;
                            }
                        break;
                        case "by_points":
                            if($item == floatval($answer_max_weights[$question_id])){
                                $corrects_count_by_cats[$cat_id]++;
                            }
                        break;
                        default:
                            if($item){
                                $corrects_count_by_cats[$cat_id]++;
                            }
                        break;
                    }
                }
            }
        }

        foreach($correctness as $question_id => $item){
            $question_weight = Quiz_Maker_Data::get_question_weight($question_id);
            $new_correctness[strval($question_id)] = $question_weight * floatval($item);
            $quiz_weight_points[strval($question_id)] = $question_weight * floatval($answer_max_weights[$question_id]);
            $quiz_weight_correctness[strval($question_id)] = $question_weight;
            switch($calculate_score){
                case "by_correctness":
                    if($item){
                        $corrects_count++;
                    }
                break;
                case "by_points":
                    if($item == floatval($answer_max_weights[$question_id])){
                        $corrects_count++;
                    }
                break;
                default:
                    if($item){
                        $corrects_count++;
                    }
                break;
            }
        }

        $quiz_weight_new_correctness_by_cats = array();
        $quiz_weight_correctness_by_cats = array();
        $quiz_weight_points_by_cats = array();

        $questions_count_by_cats = array();
        foreach($questions_cats as $cat_id => &$q_ids){
            foreach($q_ids as $q_id => &$val){
                $val = array_key_exists($q_id, $new_correctness) ? $new_correctness[$q_id] : false;
                $quiz_weight_new_correctness_by_cats[$cat_id][$q_id] = $val;
                if( Quiz_Maker_Data::is_question_not_influence($q_id) ){
                    continue;
                }

                if ( isset( $quiz_weight_correctness[$q_id] ) && sanitize_text_field( $quiz_weight_correctness[$q_id] ) != '' ) {
                    $quiz_weight_correctness_by_cats[$cat_id][$q_id] = $quiz_weight_correctness[$q_id];
                }
                if ( isset( $quiz_weight_points[$q_id] ) && sanitize_text_field( $quiz_weight_points[$q_id] ) != '' ) {
                    $quiz_weight_points_by_cats[$cat_id][$q_id] = $quiz_weight_points[$q_id];
                }

            }
            $questions_count_by_cats[$cat_id] = count($q_ids);
        }

        $final_score_by_cats = array();
        $quiz_weight_cats = array();
        $correct_answered_count_cats = array();
        $correct_answered_count_cats_arr_length = array();
        $cat_score_is_decimal = false;
        $final_score_is_decimal = false;

        foreach($quiz_weight_new_correctness_by_cats as $cat_id => $q_ids){

            if ( ! isset( $quiz_weight_correctness_by_cats[$cat_id] ) ) {
                continue;
            }
            $quiz_weight_correctness_by_cats[$cat_id] = array_filter($quiz_weight_correctness_by_cats[$cat_id], "strlen");

            switch($calculate_score){
                case "by_correctness":
                    $quiz_weight_cat = array_sum($quiz_weight_correctness_by_cats[$cat_id]);
                    $quiz_weight_cats[$cat_id] = array_sum($quiz_weight_correctness_by_cats[$cat_id]);
                break;
                case "by_points":
                    $quiz_weight_cat = array_sum($quiz_weight_points_by_cats[$cat_id]);
                    $quiz_weight_cats[$cat_id] = array_sum($quiz_weight_points_by_cats[$cat_id]);
                break;
                default:
                    $quiz_weight_cat = array_sum($quiz_weight_correctness_by_cats[$cat_id]);
                    $quiz_weight_cats[$cat_id] = array_sum($quiz_weight_correctness_by_cats[$cat_id]);
                break;
            }

            // $correct_answered_count_cat = array_sum($q_ids);
            $correct_answered_count_cats[$cat_id] = array_sum($q_ids);
            $correct_answered_count_cats_arr_length[$cat_id] = count($quiz_weight_correctness_by_cats[$cat_id]);

            if(floatval($quiz_weight_cat) == 0){
                $final_score_by_cats[$cat_id] = floatval(0);
            }else{
                // $final_score_by_cats[$cat_id] = floatval(floor(($correct_answered_count_cat / $quiz_weight_cat) * 100));
                //$final_score_by_cats[$cat_id] = floatval(floor((intval($correct_answered_count_cats[$cat_id]) / floatval($quiz_weight_cat) ) * 100));
                $final_score_by_cats[$cat_id] = floatval(floor((($correct_answered_count_cats[$cat_id]) / floatval($quiz_weight_cat) ) * 100));
                $final_score_by_cats[$cat_id] = round($final_score_by_cats[$cat_id], 2);
            }
        }

        switch($calculate_score){
            case "by_correctness":
                $quiz_weight = array_sum($quiz_weight_correctness);
            break;
            case "by_points":
                $quiz_weight = array_sum($quiz_weight_points);
            break;
            default:
                $quiz_weight = array_sum($quiz_weight_correctness);
            break;
        }

        $score_by_cats = array();
        foreach($final_score_by_cats as $cat_id => $cat_score){
            switch($display_score){
                case "by_correctness":
                    $score_by_cats[$cat_id] = array(
                        'score' => $corrects_count_by_cats[$cat_id] . " / " . $questions_count_by_cats[$cat_id],
                        'avg_cat_score' => "",
                        'categoryName' => $questions_categories[$cat_id],
                    );
                break;
                case "by_points":
                    $correct_answered_count_cats_1 = 0;
                    $correct_answered_count_cats_2 = 0;
                    if( $correct_answered_count_cats_arr_length[$cat_id] == 0 ){
                        $correct_answered_count_cats_1 = 0;
                    }else {
                        $correct_answered_count_cats_1 = floatval($correct_answered_count_cats[$cat_id] / $correct_answered_count_cats_arr_length[$cat_id]);
                        $correct_answered_count_cats_1 = round( $correct_answered_count_cats_1, 2 );
                    }

                    if( $quiz_weight_cats[$cat_id] == 0 ){
                        $correct_answered_count_cats_2 = 0;
                    }else {
                        $correct_answered_count_cats_2 = floatval($quiz_weight_cats[$cat_id] / $correct_answered_count_cats_arr_length[$cat_id]);
                        $correct_answered_count_cats_2 = round( $correct_answered_count_cats_2, 2 );
                    }

                    $score_by_cats[$cat_id] = array(
                        // 'score' => $correct_answered_count_cat[$cat_id] . " / " . $quiz_weight_cats[$cat_id],
                        'score' => $correct_answered_count_cats[$cat_id] . " / " . $quiz_weight_cats[$cat_id],
                        'avg_cat_score' => $correct_answered_count_cats_1 . " / " . $correct_answered_count_cats_2,
                        'categoryName' => $questions_categories[$cat_id],
                    );
                break;
                case "by_percentage":
                    $score_by_cats[$cat_id] = array(
                        'score' => $cat_score . "%",
                        'avg_cat_score' => "",
                        'categoryName' => $questions_categories[$cat_id],
                    );
                break;
                default:
                    $score_by_cats[$cat_id] = array(
                        'score' => $cat_score . "%",
                        'avg_cat_score' => "",
                        'categoryName' => $questions_categories[$cat_id],
                    );
                break;
            }
        }

        if(empty($score_by_cats)){
            $result_score_by_categories = '';
            $avg_result_score_by_categories = '';
        }else{
            $result_score_by_categories = '<div class="ays_result_by_cats">';
            foreach($score_by_cats as $cat_id => $cat){

                $categoryName = isset($cat['categoryName']) && $cat['categoryName'] != "" ? esc_attr( $cat['categoryName'] ) : " — ";
                $categoryScore = isset($cat['score']) && $cat['score'] != "" ? esc_attr( $cat['score'] ) : "";

                $result_score_by_categories .= '<div class="ays_result_by_cat">
                    <strong class="ays_result_by_cat_name">'. $categoryName .':</strong>
                    <span class="ays_result_by_cat_score">'. $categoryScore .'</span>
                </div>';
            }
            $result_score_by_categories .= '</div>';
            $result_score_by_categories = str_replace(array("\r\n", "\n", "\r"), "", $result_score_by_categories);
        }

        return $result_score_by_categories;

    }

}
