<?php
global $ays_quiz_db_version;
$ays_quiz_db_version = '20.9.2';
/**
 * Fired during plugin activation
 *
 * @link       http://ays-pro.com/
 * @since      1.0.0
 *
 * @package    Quiz_Maker
 * @subpackage Quiz_Maker/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Quiz_Maker
 * @subpackage Quiz_Maker/includes
 * @author     AYS Pro LLC <info@ays-pro.com>
 */
class Quiz_Maker_Activator {

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    private static function activate() {
        global $wpdb;
        global $ays_quiz_db_version;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $installed_ver = get_option( "ays_quiz_db_version" );
        $quiz_categories_table      =   $wpdb->prefix . 'aysquiz_quizcategories';
        $quizes_table               =   $wpdb->prefix . 'aysquiz_quizes';
        $questions_table            =   $wpdb->prefix . 'aysquiz_questions';
        $question_categories_table  =   $wpdb->prefix . 'aysquiz_categories';
        $answers_table              =   $wpdb->prefix . 'aysquiz_answers';
        $reports_table              =   $wpdb->prefix . 'aysquiz_reports';
        $rates_table                =   $wpdb->prefix . 'aysquiz_rates';
        $themes_table               =   $wpdb->prefix . 'aysquiz_themes';
        $orders_table               =   $wpdb->prefix . 'aysquiz_orders';
        $attributes_table           =   $wpdb->prefix . 'aysquiz_attributes';
        $settings_table             =   $wpdb->prefix . 'aysquiz_settings';
        $charset_collate = $wpdb->get_charset_collate();

        if($installed_ver != $ays_quiz_db_version)  {
            $sql="CREATE TABLE `".$quiz_categories_table."` (
                `id` INT(16) UNSIGNED NOT NULL AUTO_INCREMENT,
                `title` VARCHAR(256) NOT NULL,
                `description` TEXT  NOT NULL,
                `published` TINYINT(1) UNSIGNED NOT NULL,
                PRIMARY KEY (`id`)
            )$charset_collate;";
            dbDelta( $sql );

            $sql="CREATE TABLE `".$quizes_table."` (
                `id` INT(16) UNSIGNED NOT NULL AUTO_INCREMENT,
                `post_id` INT(16) UNSIGNED DEFAULT NULL,
                `title` VARCHAR(256) NOT NULL,
                `description` TEXT NOT NULL,
                `quiz_image` TEXT DEFAULT NULL,
                `quiz_category_id` INT(11) UNSIGNED NOT NULL,
                `question_ids` TEXT NOT NULL,
                `ordering` INT(16) NOT NULL,
                `published` TINYINT UNSIGNED NOT NULL,
                `options` TEXT NULL DEFAULT NULL,
                `intervals` TEXT NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
            )$charset_collate;";
            dbDelta( $sql );

            $sql="CREATE TABLE `".$questions_table."` (
                `id` INT(16) UNSIGNED NOT NULL AUTO_INCREMENT,
                `category_id` INT(16) UNSIGNED NOT NULL,
                `question` TEXT NOT NULL,
                `question_image` TEXT NULL DEFAULT NULL,
                `wrong_answer_text` TEXT DEFAULT NULL, 
                `right_answer_text` TEXT DEFAULT NULL, 
                `question_hint` TEXT DEFAULT NULL, 
                `explanation` TEXT DEFAULT NULL,
                `user_explanation` TEXT NULL DEFAULT NULL,
                `type` VARCHAR(256) NOT NULL,
                `published` TINYINT UNSIGNED NOT NULL,
                `create_date` DATETIME DEFAULT NULL,
                `not_influence_to_score` TEXT DEFAULT NULL,
                `weight` DOUBLE DEFAULT 1,
                `options` TEXT DEFAULT NULL,
                PRIMARY KEY (`id`)
            )$charset_collate;";
            dbDelta( $sql );

            $sql="CREATE TABLE `".$question_categories_table."` (
                `id` INT(16) UNSIGNED NOT NULL AUTO_INCREMENT,
                `title` VARCHAR(256) NOT NULL,
                `description` TEXT NOT NULL,
                `published` TINYINT UNSIGNED NOT NULL,
                PRIMARY KEY (`id`)
            )$charset_collate;";
            dbDelta( $sql );

            $sql="CREATE TABLE `".$attributes_table."` (
                `id` INT(16) UNSIGNED NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(256) NOT NULL,
                `type` VARCHAR(256) NOT NULL,
                `slug` VARCHAR(256) NOT NULL,
                `options` TEXT DEFAULT NULL,
                `published` TINYINT UNSIGNED NOT NULL,
                PRIMARY KEY (`id`)
            )$charset_collate;";
            dbDelta( $sql );

            $sql="CREATE TABLE `".$answers_table."` (
                `id` INT(150) UNSIGNED NOT NULL AUTO_INCREMENT,
                `question_id` INT(11) UNSIGNED NOT NULL,
                `answer` TEXT NOT NULL,
                `image` TEXT NULL DEFAULT NULL,
                `correct` TINYINT(1) NOT NULL,
                `ordering` INT(11) NOT NULL,
                `weight` DOUBLE DEFAULT 0,
                `placeholder` TEXT DEFAULT NULL,
                PRIMARY KEY (`id`)
            )$charset_collate;";
            dbDelta( $sql );

            $sql="CREATE TABLE `".$reports_table."` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `quiz_id` INT(11) NOT NULL,
                `user_id` INT(11) NOT NULL,
                `user_ip` VARCHAR(256) NOT NULL,
                `user_name` TEXT NULL DEFAULT NULL,
                `user_email` TEXT NULL DEFAULT NULL,
                `user_phone` TEXT NULL DEFAULT NULL,
                `start_date` DATETIME NOT NULL,
                `end_date` DATETIME NOT NULL,
                `duration` VARCHAR(256) NOT NULL,
                `score` VARCHAR(256) NOT NULL,
                `points` VARCHAR(256) NOT NULL,
                `max_points` VARCHAR(256) NOT NULL,
                `corrects_count` VARCHAR(256) NOT NULL,
                `questions_count` VARCHAR(256) NOT NULL,
                `user_explanation` TEXT NULL DEFAULT NULL,
                `options` TEXT NOT NULL,
                `read` tinyint(3) NOT NULL DEFAULT 0,
                PRIMARY KEY (`id`)
            )$charset_collate;";
            dbDelta( $sql );

            $sql="CREATE TABLE `".$rates_table."` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `quiz_id` INT(11) NOT NULL,
                `user_id` INT(11) NOT NULL,
                `report_id` INT(11) NOT NULL,
                `user_ip` VARCHAR(256) NOT NULL,
                `user_name` TEXT NULL DEFAULT NULL,
                `user_email` TEXT NULL DEFAULT NULL,
                `user_phone` TEXT NULL DEFAULT NULL,
                `rate_date` DATETIME NOT NULL,
                `score` VARCHAR(256) NOT NULL,
                `review` TEXT NOT NULL,
                `options` TEXT NOT NULL,
                PRIMARY KEY (`id`)
            )$charset_collate;";
            dbDelta( $sql );

            $sql="CREATE TABLE `".$orders_table."` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `order_id` TEXT NOT NULL,
                `quiz_id` INT(11) NOT NULL,
                `user_id` INT(11) NOT NULL,
                `order_full_name` TEXT NULL DEFAULT NULL,
                `order_email` TEXT NULL DEFAULT NULL,
                `amount` TEXT NOT NULL,
                `payment_date` DATETIME NOT NULL,
                `options` TEXT DEFAULT NULL,
                PRIMARY KEY (`id`)
            )$charset_collate;";
            dbDelta( $sql );

            $sql="CREATE TABLE `".$settings_table."` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `meta_key` TEXT NULL DEFAULT NULL,
                `meta_value` TEXT NULL DEFAULT NULL,
                `note` TEXT NULL DEFAULT NULL,
                `options` TEXT NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
            )$charset_collate;";
            dbDelta( $sql );

            $sql="CREATE TABLE `".$themes_table."` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `title` VARCHAR(255) NOT NULL,
                `border_radius` VARCHAR(255) NOT NULL,
                `show_result_presentage` INT(11) NOT NULL,
                `show_result_answers` INT(11) NOT NULL,
                `buttons_color` VARCHAR(255) NOT NULL,
                `buttons_bg_color` VARCHAR(255) NOT NULL,
                `buttons_hover_color` VARCHAR(255) NOT NULL,
                `buttons_hover_bg_color` VARCHAR(255) NOT NULL,
                `quiz_title_color` VARCHAR(255) NOT NULL,
                `quiz_description_color` VARCHAR(255) NOT NULL,
                `question_color` VARCHAR(255) NOT NULL,
                `question_bg_color` VARCHAR(255) NOT NULL,
                `question_answer_color` VARCHAR(255) NOT NULL,
                `question_answer_bg_color` VARCHAR(255) NOT NULL,
                `question_answer_hover_color` VARCHAR(255) NOT NULL,
                `question_answer_hover_bg_color` VARCHAR(255) NOT NULL,
                `question_correct_answer_bg_color` VARCHAR(255) NOT NULL,
                `question_incorrect_answer_bg_color` VARCHAR(255) NOT NULL,
                `pagination_bg_color` VARCHAR(255) NOT NULL,
                `pagination_color` VARCHAR(255) NOT NULL,
                PRIMARY KEY (`id`)
            )$charset_collate;";
            dbDelta( $sql );

            $sql = "SELECT * FROM INFORMATION_SCHEMA.STATISTICS 
                    WHERE TABLE_NAME = '".$quizes_table."'
                    AND INDEX_NAME = 'FK_quiz_category' 
                    AND INDEX_SCHEMA = '".DB_NAME."';";
            
            $quiz_cat_const = $wpdb->get_row($sql, ARRAY_A);
            
            $sql = "SELECT * FROM INFORMATION_SCHEMA.STATISTICS 
                    WHERE TABLE_NAME = '".$questions_table."'
                    AND INDEX_NAME = 'FK_question_category' 
                    AND INDEX_SCHEMA = '".DB_NAME."';";
            
            $ques_cat_const = $wpdb->get_row($sql, ARRAY_A);
            
            $sql = "SELECT * FROM INFORMATION_SCHEMA.STATISTICS 
                    WHERE TABLE_NAME = '".$answers_table."'
                    AND INDEX_NAME = 'FK_answer_question' 
                    AND INDEX_SCHEMA = '".DB_NAME."';";
            
            $answ_que_const = $wpdb->get_row($sql, ARRAY_A);

            if (! empty( $quiz_cat_const )) {
                $sql = "ALTER TABLE `{$quizes_table}`
                        DROP INDEX `FK_quiz_category`";
                $wpdb->query($sql);
            }

            if (! empty( $ques_cat_const )) {
                $sql = "ALTER TABLE `{$questions_table}`
                        DROP INDEX `FK_question_category`";
                $wpdb->query($sql);
            }

            if (! empty( $answ_que_const )) {
                $sql = "ALTER TABLE `{$answers_table}`
                        DROP INDEX `FK_answer_question`";
                $wpdb->query($sql);
            }
            
            update_option( 'ays_quiz_db_version', $ays_quiz_db_version );
            
            $quiz_categories = $wpdb->get_var( "SELECT COUNT(*) FROM " . $quiz_categories_table );            
            if( intval($quiz_categories) == 0 ){
                $wpdb->query("TRUNCATE TABLE `{$quiz_categories_table}`");
                $wpdb->insert( $quiz_categories_table, array( 
                    'title' => 'Uncategorized', 
                    'description' => '', 
                    'published' => 1 
                ) );
            }

            $question_categories = $wpdb->get_var( "SELECT COUNT(*) FROM " . $question_categories_table );            
            if( intval($question_categories) == 0 ){
                $wpdb->query("TRUNCATE TABLE `{$question_categories_table}`");
                $wpdb->insert( $question_categories_table, array( 
                    'title' => 'Uncategorized', 
                    'description' => '', 
                    'published' => 1 
                ) );
            }
            $themes = $wpdb->get_var( "SELECT COUNT(*) FROM " . $themes_table . " WHERE `title`='Default'" );
            if( intval($themes) == 0 )
                $wpdb->insert( $themes_table, array( 
                    'title' => 'Default', 
                    'border_radius' => '4', 
                    'show_result_presentage' => 1, 
                    'show_result_answers' => 1, 
                    'buttons_color' => '#ffffff', 
                    'buttons_bg_color' => '#70b1f2', 
                    'buttons_hover_color' => '#ffffff', 
                    'buttons_hover_bg_color' => '#4797e7', 
                    'quiz_title_color' => '#000000', 
                    'quiz_description_color' => '#000000', 
                    'question_color' => '#ffffff', 
                    'question_bg_color' => '#70b1f2', 
                    'question_answer_color' => '#7a7575', 
                    'question_answer_bg_color' => '#efefef', 
                    'question_answer_hover_color' => '#7a7575', 
                    'question_answer_hover_bg_color' => '#d6d2c9', 
                    'question_correct_answer_bg_color' => '#4fed24', 
                    'question_incorrect_answer_bg_color' => '#ed3324', 
                    'pagination_bg_color' => '#efefef', 
                    'pagination_color' => '#70b1f2' 
                ) );

            $questions = $wpdb->get_var("SELECT COUNT(*) FROM " . $questions_table);
            $quizes = $wpdb->get_var("SELECT COUNT(*) FROM " . $quizes_table);
            if (intval($questions) == 0 && intval($quizes) == 0) {
                $questions_arrays = array(
                    array(
                        array(
                            array('category_id' => 1, 'question' => 'Who is in the picture?', 'question_image' => 'https://freedemo.ays-pro.com/wp-content/uploads/2018/07/416x416.jpg', 'type' => 'radio', 'published' => 1),
                            array('question_id' => 1, 'answer' => 'Mark Zuckerberg', 'correct' => 1, 'ordering' => 1),
                            array('question_id' => 1, 'answer' => 'Bill Gates', 'correct' => 0, 'ordering' => 2),
                            array('question_id' => 1, 'answer' => 'Elon Musk', 'correct' => 0, 'ordering' => 3),
                            array('question_id' => 1, 'answer' => 'Winston Churchill', 'correct' => 0, 'ordering' => 3),
                        ),
                        array(
                            array('category_id' => 1, 'question' => 'Do you know him?', 'question_image' => 'https://freedemo.ays-pro.com/wp-content/uploads/2018/07/416x416-1.jpg', 'type' => 'radio', 'published' => 1),
                            array('question_id' => 1, 'answer' => 'Mark Zuckerberg', 'correct' => 0, 'ordering' => 1),
                            array('question_id' => 1, 'answer' => 'Steve Jobs', 'correct' => 0, 'ordering' => 2),
                            array('question_id' => 1, 'answer' => 'Elon Musk', 'correct' => 1, 'ordering' => 3),
                            array('question_id' => 1, 'answer' => 'Robert De Niro', 'correct' => 0, 'ordering' => 3),
                        ),
                        array(
                            array('category_id' => 1, 'question' => 'Select right answer?', 'question_image' => 'https://freedemo.ays-pro.com/wp-content/uploads/2018/07/416x416-2.jpg', 'type' => 'radio', 'published' => 1),
                            array('question_id' => 1, 'answer' => 'Elon Musk', 'correct' => 0, 'ordering' => 1),
                            array('question_id' => 1, 'answer' => 'Bill Gates', 'correct' => 1, 'ordering' => 2),
                            array('question_id' => 1, 'answer' => 'Steve Jobs', 'correct' => 0, 'ordering' => 3),
                            array('question_id' => 1, 'answer' => 'Clint Eastwood', 'correct' => 0, 'ordering' => 3),
                        ),
                    ),
                    array(
                        array(
                            array('category_id' => 1, 'question' => '5*40', 'question_image' => '', 'type' => 'radio', 'published' => 1),
                            array('question_id' => 1, 'answer' => '300', 'correct' => 0, 'ordering' => 1),
                            array('question_id' => 1, 'answer' => '100', 'correct' => 0, 'ordering' => 2),
                            array('question_id' => 1, 'answer' => '200', 'correct' => 1, 'ordering' => 3),
                        ),
                        array(
                            array('category_id' => 1, 'question' => '10+20', 'question_image' => '', 'type' => 'radio', 'published' => 1),
                            array('question_id' => 1, 'answer' => '30', 'correct' => 1, 'ordering' => 1),
                            array('question_id' => 1, 'answer' => '40', 'correct' => 0, 'ordering' => 2),
                            array('question_id' => 1, 'answer' => '50', 'correct' => 0, 'ordering' => 3),
                        ),
                        array(
                            array('category_id' => 1, 'question' => '150/3', 'question_image' => '', 'type' => 'radio', 'published' => 1),
                            array('question_id' => 1, 'answer' => '60', 'correct' => 0, 'ordering' => 1),
                            array('question_id' => 1, 'answer' => '50', 'correct' => 1, 'ordering' => 2),
                            array('question_id' => 1, 'answer' => '100', 'correct' => 0, 'ordering' => 3),
                        ),

                    ),
                );

                $quizes_array = array(
                    array(
                        'title' => 'CELEBRITIES',
                        'description' => 'How well you know the celebrities',
                        'quiz_category_id' => 1,
                        'question_ids' => 1,
                        'ordering' => 1,
                        'published' => 1,
                        'options' => '{"color":"#fff195","bg_color":"#fff","text_color":"#000","height":400,"width":400,"enable_logged_users":"off","information_form":"disable","form_name":null,"form_email":null,"form_phone":null,"image_width":"","image_height":"","enable_correction":"off","enable_progress_bar":"on","enable_questions_result":"off","randomize_questions":"off","randomize_answers":"on","enable_questions_counter":"on","enable_restriction_pass":"off","restriction_pass_message":"","user_role":"","custom_css":"","limit_users":"off","limitation_message":"","redirect_url":"","redirection_delay":0,"answers_view":"list","enable_rtl_direction":"off","enable_logged_users_message":"","questions_count":"","enable_question_bank":"off","enable_live_progress_bar":"on","enable_percent_view":"on","enable_average_statistical":"on","enable_next_button":"on","enable_previous_button":"off","enable_arrows":"off","timer_text":"","quiz_theme":"rect_light","enable_social_buttons":"on","result_text":"","enable_pass_count":"on","hide_score":"off","required_fields":null,"enable_timer":"off","timer":0,"create_date":"'.current_time( 'mysql' ).'","author":{"name":"Unknown"}}'
                    ),
                    array(
                        'title' => 'MATHEMATIC QUIZ',
                        'description' => 'Math quiz helps us to increase our knowledge',
                        'quiz_category_id' => 1,
                        'question_ids' => 2,
                        'ordering' => 1,
                        'published' => 1,
                        'options' => '{"color":"#27AE60","bg_color":"#fff","text_color":"#515151","height":400,"width":500,"enable_logged_users":"off","information_form":"disable","form_name":null,"form_email":null,"form_phone":null,"image_width":"","image_height":"","enable_correction":"off","enable_progress_bar":"off","enable_questions_result":"off","randomize_questions":"off","randomize_answers":"off","enable_questions_counter":"on","enable_restriction_pass":"off","restriction_pass_message":"","user_role":"","custom_css":"","limit_users":"off","limitation_message":"","redirect_url":"","redirection_delay":0,"answers_view":"list","enable_rtl_direction":"off","enable_logged_users_message":"","questions_count":"","enable_question_bank":"off","enable_live_progress_bar":"off","enable_percent_view":"off","enable_average_statistical":"off","enable_next_button":"off","enable_previous_button":"off","enable_arrows":"off","timer_text":"","quiz_theme":"classic_light","enable_social_buttons":"off","result_text":"","enable_pass_count":"on","hide_score":"off","required_fields":null,"enable_timer":"off","timer":0,"create_date":"'.current_time( 'mysql' ).'","author":{"name":"Unknown"}}'
                    )
                );

                foreach ($quizes_array as $key=>$quiz){
                    $questions_ids = '';
                    foreach ($questions_arrays[$key] as $question_array){
                        $wpdb->insert($questions_table,$question_array[0]);
                        array_shift($question_array);
                        $question_id = $wpdb->insert_id;
                        $questions_ids.=$question_id.',';

                        foreach ($question_array as $answer){
                            $answer['question_id'] = $question_id;
                            $wpdb->insert($answers_table, $answer);
                        }
                    }
                    $questions_ids = rtrim($questions_ids,",");;
                    $quiz['question_ids'] = $questions_ids;
                    $result = $wpdb->insert($quizes_table, $quiz);
                    unset($questions_ids);
                }
            }
        }
        
        $metas = array(
            "user_roles",
            "mailchimp",
            "monitor",
            "slack",
            "active_camp",
            "zapier",
            "leaderboard",
            "options"
        );
        
        foreach($metas as $meta_key){
            $meta_val = "";
            if($meta_key == "user_roles"){
                $meta_val = json_encode(array('administrator'));
            }
            $sql = "SELECT COUNT(*) FROM `".$settings_table."` WHERE `meta_key` = '".$meta_key."'";
            $result = $wpdb->get_var($sql);
            if(intval($result) == 0){
                $result = $wpdb->insert(
                    $settings_table,
                    array(
                        'meta_key'    => $meta_key,
                        'meta_value'  => $meta_val,
                        'note'        => "",
                        'options'     => ""
                    ),
                    array( '%s', '%s', '%s', '%s' )
                );
            }
        }
        
    }

    public static function ays_quiz_update_db_check() {
        global $ays_quiz_db_version;
        if ( get_site_option( 'ays_quiz_db_version' ) != $ays_quiz_db_version ) {
            self::activate();
        }
    }
}
