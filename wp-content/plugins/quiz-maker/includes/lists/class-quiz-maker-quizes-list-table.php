<?php
ob_start();
class Quizes_List_Table extends WP_List_Table{
    private $plugin_name;
    /** Class constructor */
    public function __construct($plugin_name) {
        global $status, $page;
        $this->plugin_name = $plugin_name;
        parent::__construct( array(
            'singular' => __( 'Quiz', $this->plugin_name ), //singular name of the listed records
            'plural'   => __( 'Quizzes', $this->plugin_name ), //plural name of the listed records
            'ajax'     => false //does this table support ajax?
        ) );
        add_action( 'admin_notices', array( $this, 'quiz_notices' ) );

    }

    /**
     * Override of table nav to avoid breaking with bulk actions & according nonce field
     */
    public function display_tablenav( $which ) {
        ?>
        <div class="tablenav <?php echo esc_attr( $which ); ?>">
            
            <div class="alignleft actions">
                <?php  $this->bulk_actions( $which ); ?>
            </div>
            <?php
            $this->extra_tablenav( $which );
            $this->pagination( $which );
            ?>
            <br class="clear" />
        </div>
        <?php
    }

    /**
     * Disables the views for 'side' context as there's not enough free space in the UI
     * Only displays them on screen/browser refresh. Else we'd have to do this via an AJAX DB update.
     *
     * @see WP_List_Table::extra_tablenav()
     */
    public function extra_tablenav($which) {
        ?>
        
        <?php
    }
    
    protected function get_views() {
        $published_count = $this->published_quizzes_count();
        $unpublished_count = $this->unpublished_quizzes_count();
        $all_count = $this->all_record_count();
        $selected_all = "";
        $selected_0 = "";
        $selected_1 = "";
        if(isset($_GET['fstatus'])){
            switch($_GET['fstatus']){
                case "0":
                    $selected_0 = " style='font-weight:bold;' ";
                    break;
                case "1":
                    $selected_1 = " style='font-weight:bold;' ";
                    break;
                default:
                    $selected_all = " style='font-weight:bold;' ";
                    break;
            }
        }else{
            $selected_all = " style='font-weight:bold;' ";
        }
        $status_links = array(
            "all" => "<a ".$selected_all." href='?page=".esc_attr( $_REQUEST['page'] )."'>All (".$all_count.")</a>",
            "published" => "<a ".$selected_1." href='?page=".esc_attr( $_REQUEST['page'] )."&fstatus=1'>Published (".$published_count.")</a>",
            "unpublished"   => "<a ".$selected_0." href='?page=".esc_attr( $_REQUEST['page'] )."&fstatus=0'>Unpublished (".$unpublished_count.")</a>"
        );
        return $status_links;
    }

    /**
     * Retrieve customers data from the database
     *
     * @param int $per_page
     * @param int $page_number
     *
     * @return mixed
     */
    public static function get_quizes( $per_page = 20, $page_number = 1 ) {

        global $wpdb;
        
        $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_quizes";
        
        if( isset( $_REQUEST['fstatus'] ) ){
            $fstatus = $_REQUEST['fstatus'];
            if($fstatus !== null){
                $sql .= " WHERE published = ".$fstatus." ";
            }
        }

        if ( ! empty( $_REQUEST['orderby'] ) ) {
            $sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
            $sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' DESC';
        }else{
            $sql .= ' ORDER BY ordering DESC';
        }
        $sql .= " LIMIT $per_page";
        $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;


        $result = $wpdb->get_results( $sql, 'ARRAY_A' );

        return $result;
    }

    public function get_published_questions(){
        global $wpdb;
        $sql = "SELECT q.id, q.question, q.type, q.create_date, q.options, c.title
                FROM {$wpdb->prefix}aysquiz_questions AS q
                LEFT JOIN {$wpdb->prefix}aysquiz_categories AS c
                ON q.category_id=c.id
                WHERE q.published = 1 
                ORDER BY q.id DESC;";

        $results = $wpdb->get_results( $sql, 'ARRAY_A' );

        return $results;

    }

    public function get_quiz_categories(){
        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_quizcategories";

        $result = $wpdb->get_results($sql, 'ARRAY_A');

        return $result;
    }

    public function get_question_categories(){
        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_categories";

        $result = $wpdb->get_results($sql, 'ARRAY_A');

        return $result;
    }

    public function get_question_bank_categories($q_ids){
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

    public function get_published_questions_by($key, $value) {
        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_questions WHERE {$key} = {$value};";

        $results = $wpdb->get_row( $sql, 'ARRAY_A' );

        return $results;

    }

    public static function get_quiz_by_id( $id ){
        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_quizes WHERE id=" . absint( intval( $id ) );

        $result = $wpdb->get_row($sql, 'ARRAY_A');

        return $result;
    }
    
    public function get_al_attributes(){
        global $wpdb;
        $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_attributes";
        $result = $wpdb->get_results($sql,'ARRAY_A');
        return $result;
    }
    
    public function add_or_edit_quizes($data){
        global $wpdb;
        $quiz_table = $wpdb->prefix . 'aysquiz_quizes';
        $ays_change_type = (isset($data['ays_change_type']))?$data['ays_change_type']:'';

        if( isset($data["quiz_action"]) && wp_verify_nonce( $data["quiz_action"],'quiz_action' ) ){
            $id                         = ( $data["id"] != NULL ) ? absint( intval( $data["id"] ) ) : null;
            $max_id                     = $this->get_max_id();
            $title                      = stripslashes(sanitize_text_field( $data['ays_quiz_title'] ));
            $description                = stripslashes(( $data['ays_quiz_description'] ));
            $quiz_category_id           = absint( intval( $data['ays_quiz_category'] ) );
            $question_ids               = sanitize_text_field( $data['ays_added_questions'] );
            $published                  = absint( intval( $data['ays_publish'] ) );
            $ordering                   = ( $max_id != NULL ) ? ( $max_id + 1 ) : 1;
            $image                      = $data['ays_quiz_image'];
            
            if(isset($data['ays_enable_restriction_pass']) && $data['ays_enable_restriction_pass'] == "on"){
                $ays_enable_logged_users = "on";
            }elseif(isset($data['ays_enable_logged_users']) && $data['ays_enable_logged_users'] == "on"){
                $ays_enable_logged_users = "on";
            }else{
                $ays_enable_logged_users = "off";
            }
            
            $ays_form_name              = !isset($data['ays_form_name']) ? "off" : $data['ays_form_name'];
            $ays_form_email             = !isset($data['ays_form_email']) ? "off" : $data['ays_form_email'];
            $ays_form_phone             = !isset($data['ays_form_phone']) ? "off" : $data['ays_form_phone'];
            $enable_correction          = !isset($data['ays_enable_correction']) ? "off" : $data['ays_enable_correction'];
            $enable_progressbar         = !isset($data['ays_enable_progress_bar']) ? "off" : $data['ays_enable_progress_bar'];
            $enable_questions_result    = !isset($data['ays_enable_questions_result']) ? "off" : $data['ays_enable_questions_result'];
            $enable_random_questions    = !isset($data['ays_enable_randomize_questions']) ? "off" : $data['ays_enable_randomize_questions'];
            $enable_random_answers      = !isset($data['ays_enable_randomize_answers']) ? "off" : $data['ays_enable_randomize_answers'];
            $enable_questions_counter   = !isset($data['ays_enable_questions_counter']) ? "off" : $data['ays_enable_questions_counter'];
            $enable_restriction_pass    = !isset($data['ays_enable_restriction_pass']) ? "off" : $data['ays_enable_restriction_pass'];
            $limit_users                = !isset($data['ays_limit_users']) ? "off" : $data['ays_limit_users'];
            $enable_rtl                 = !isset($data['ays_enable_rtl_direction']) ? "off" : $data['ays_enable_rtl_direction'];
            $question_bank              = !isset($data['ays_enable_question_bank']) ? "off" : $data['ays_enable_question_bank'];
            $live_progressbar           = !isset($data['ays_enable_live_progress_bar']) ? "off" : $data['ays_enable_live_progress_bar'];
            $percent_view               = !isset($data['ays_enable_percent_view']) ? "off" : $data['ays_enable_percent_view'];
            $avarage_statistical        = !isset($data['ays_enable_average_statistical']) ? "off" : $data['ays_enable_average_statistical'];
            $next_button                = !isset($data['ays_enable_next_button']) ? "off" : $data['ays_enable_next_button'];
            $prev_button                = !isset($data['ays_enable_previous_button']) ? "off" : $data['ays_enable_previous_button'];
            $enable_arrows              = !isset($data['ays_enable_arrows']) ? "off" : $data['ays_enable_arrows'];
            $quiz_theme                 = !isset($data['ays_quiz_theme']) ? null : $data['ays_quiz_theme'];
            $social_buttons             = !isset($data['ays_social_buttons']) ? "off" : $data['ays_social_buttons'];
            $enable_logged_users_mas    = !isset($data['ays_enable_logged_users_message']) ? "" : stripslashes($data['ays_enable_logged_users_message']);
            $enable_pass_count          = !isset($data['ays_enable_pass_count']) ? "off" : $data['ays_enable_pass_count'];
            $hide_score                 = !isset($data['ays_hide_score']) ? "off" : $data['ays_hide_score'];
            $enable_smtp                = !isset($data['ays_enable_smtp']) ? "off" : $data['ays_enable_smtp'];
            $enable_certificate         = !isset($data['ays_enable_certificate']) ? "off" : $data['ays_enable_certificate'];
            $question_count_per_page    = !isset($data['ays_question_count_per_page']) ? null : $data['ays_question_count_per_page'];
            $rate_form_title            = !isset($data['ays_rate_form_title'])?'':$data['ays_rate_form_title'];
            $quiz_box_shadow_color      = !isset($data['ays_quiz_box_shadow_color'])?'':$data['ays_quiz_box_shadow_color'];
            $quiz_border_radius         = !isset($data['ays_quiz_border_radius'])?'':$data['ays_quiz_border_radius'];
            $quiz_bg_image              = !isset($data['ays_quiz_bg_image'])?'':$data['ays_quiz_bg_image'];
            $quiz_border_width          = !isset($data['ays_quiz_border_width'])?'':$data['ays_quiz_border_width'];
            $quiz_border_style          = !isset($data['ays_quiz_border_style'])?'':$data['ays_quiz_border_style'];
            $quiz_border_color          = !isset($data['ays_quiz_border_color'])?'':$data['ays_quiz_border_color'];
            
            $paypal_amount              = !isset($data['ays_paypal_amount'])?null:$data['ays_paypal_amount'];
            $paypal_currency            = !isset($data['ays_paypal_currency'])?null:$data['ays_paypal_currency'];
            
            // MailChimp
            $mailchimp_list             = !isset($data['ays_mailchimp_list'])?"":$data['ays_mailchimp_list'];
			// Campaign Monitor
			$monitor_list   = !isset($data['ays_monitor_list']) ? "" : $data['ays_monitor_list'];
			$enable_monitor = isset($data['ays_enable_monitor']) && $data['ays_enable_monitor'] == "on" ? "on" : "off";
			// Slack
			$slack_conversation = !isset($data['ays_slack_conversation']) ? "" : $data['ays_slack_conversation'];
			$enable_slack       = isset($data['ays_enable_slack']) && $data['ays_enable_slack'] == "on" ? "on" : "off";
			// ActiveCampaign
			$active_camp_list       = !isset($data['ays_active_camp_list']) ? "" : $data['ays_active_camp_list'];
			$active_camp_automation = !isset($data['ays_active_camp_automation']) ? "" : $data['ays_active_camp_automation'];
			$enable_active_camp     = isset($data['ays_enable_active_camp']) && $data['ays_enable_active_camp'] == "on" ? "on" : "off";
			//Zapier
			$enable_zapier = isset($data['ays_enable_zapier']) && $data['ays_enable_zapier'] == "on" ? "on" : "off";
            
            
            $quiz_loader                = !isset($data['ays_quiz_loader'])?'':$data['ays_quiz_loader'];
            
            $quiz_create_date           = !isset($data['ays_quiz_ctrate_date']) ? '0000-00-00 00:00:00' : $data['ays_quiz_ctrate_date'];
            $quest_animation            = !isset($data['ays_quest_animation']) ? 'shake' : $data['ays_quest_animation'];
            $author                     = isset($data['ays_quiz_author'])?stripslashes($data['ays_quiz_author']):'';
            $author                     = json_decode($author, true);
            
            $enable_bg_music            = (isset($data['ays_enable_bg_music']) && $data['ays_enable_bg_music'] == "on") ? "on" : "off";
            $quiz_bg_music              = (isset($data['ays_quiz_bg_music']) && $data['ays_quiz_bg_music'] != "") ? $data['ays_quiz_bg_music'] : "";
            $limit_user_roles           = !isset($data['ays_users_roles']) ? array() : $data['ays_users_roles'];
            $answers_font_size          = (isset($data['ays_answers_font_size']) && $data['ays_answers_font_size'] != "") ? $data['ays_answers_font_size'] : "";
            
            $checkbox_score_by          = (isset($data['ays_checkbox_score_by']) && $data['ays_checkbox_score_by'] == "on") ? "on" : "off";

            $show_create_date = (isset($data['ays_show_create_date']) && $data['ays_show_create_date'] == "on") ? "on" : "off";
            $show_author = (isset($data['ays_show_author']) && $data['ays_show_author'] == "on") ? "on" : "off";
            $enable_early_finish = (isset($data['ays_enable_early_finish']) && $data['ays_enable_early_finish'] == "on") ? "on" : "off";
            $answers_rw_texts = isset($data['ays_answers_rw_texts']) ? $data['ays_answers_rw_texts'] : 'on_passing';
            $disable_store_data = (isset($data['ays_disable_store_data']) && $data['ays_disable_store_data'] == "on") ? "on" : "off";
            
            // Background gradient
            $enable_background_gradient = ( isset( $data['ays_enable_background_gradient'] ) && $data['ays_enable_background_gradient'] == 'on' ) ? 'on' : 'off';
            $quiz_background_gradient_color_1 = !isset($data['ays_background_gradient_color_1']) ? '' : $data['ays_background_gradient_color_1'];
            $quiz_background_gradient_color_2 = !isset($data['ays_background_gradient_color_2']) ? '' : $data['ays_background_gradient_color_2'];
            $quiz_gradient_direction = !isset($data['ays_quiz_gradient_direction']) ? '' : $data['ays_quiz_gradient_direction'];
            
            
            // Schedule quiz
			$active_date_check = (isset($data['active_date_check']) && $data['active_date_check'] == "on") ? 'on' : 'off';
			$activeInterval = isset($data['ays-active']) ? $data['ays-active'] : "";
			$deactiveInterval = isset($data['ays-deactive']) ? $data['ays-deactive'] : "";
			$active_date_message = stripslashes($data['active_date_message']);
            
            //aray start            
            $email_config_from_email = (isset($data['ays_email_configuration_from_email']) && $data['ays_email_configuration_from_email'] != '') ? $data['ays_email_configuration_from_email'] : "";
            $email_config_from_name = (isset($data['ays_email_configuration_from_name']) && $data['ays_email_configuration_from_name'] != '') ? $data['ays_email_configuration_from_name'] : "";
            $email_config_from_subject = (isset($data['ays_email_configuration_from_subject']) && $data['ays_email_configuration_from_subject'] != '') ? $data['ays_email_configuration_from_subject'] : "";

            if(isset($data['ays_additional_emails'])) {
                $additional_emails = "";
                if(!empty($data['ays_additional_emails'])) {
                    $additional_emails_arr = explode(",", $data['ays_additional_emails']);
                    foreach($additional_emails_arr as $email) {
                        $email = stripslashes(trim($email));
                        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                          $additional_emails .= $email.", ";
                        }
                    }
                    $additional_emails = substr($additional_emails, 0, -2);
                }
            }
            //aray end
            
            // Calculate the score
            $calculate_score = isset($data['ays_calculate_score']) ? $data['ays_calculate_score'] : 'by_correctness';
            
//            if($calculate_score == 'by_points'){
//                $enable_correction = 'off';
//                $enable_questions_result = 'off';
//            }
            
            // Redirect after submit
            $redirect_after_submit = ( isset( $data['ays_redirect_after_submit'] ) && $data['ays_redirect_after_submit'] == 'on' ) ? 'on' : 'off';
            $submit_redirect_url = !isset($data['ays_submit_redirect_url']) ? '' : $data['ays_submit_redirect_url'];
            $submit_redirect_delay = !isset($data['ays_submit_redirect_delay']) ? '' : $data['ays_submit_redirect_delay'];

            // Progress bar
            $progress_bar_style = (isset($data['ays_progress_bar_style']) && $data['ays_progress_bar_style'] != "") ? $data['ays_progress_bar_style'] : 'first';

            // EXIT button in finish page            
            $enable_exit_button = (isset($data['ays_enable_exit_button']) && $data['ays_enable_exit_button'] == 'on') ? "on" : "off";
            $exit_redirect_url = isset($data['ays_exit_redirect_url']) ? $data['ays_exit_redirect_url'] : '';

            // Question image sizing
            $image_sizing = (isset($data['ays_image_sizing']) && $data['ays_image_sizing'] != "") ? $data['ays_image_sizing'] : 'cover';

            // Quiz background image position
            $quiz_bg_image_position = (isset($data['ays_quiz_bg_image_position']) && $data['ays_quiz_bg_image_position'] != "") ? $data['ays_quiz_bg_image_position'] : 'center center';

            // Custom class for quiz container
            $custom_class = (isset($data['ays_custom_class']) && $data['ays_custom_class'] != "") ? $data['ays_custom_class'] : '';

            // Social Media links
            $enable_social_links = (isset($data['ays_enable_social_links']) && $data['ays_enable_social_links'] == "on") ? 'on' : 'off';
            $ays_social_links = (isset($data['ays_social_links'])) ? $data['ays_social_links'] : array(
                'linkedin_link' => '',
                'facebook_link' => '',
                'twitter_link' => ''
            );
            
            $linkedin_link = isset($ays_social_links['ays_linkedin_link']) && $ays_social_links['ays_linkedin_link'] != '' ? $ays_social_links['ays_linkedin_link'] : '';
            $facebook_link = isset($ays_social_links['ays_facebook_link']) && $ays_social_links['ays_facebook_link'] != '' ? $ays_social_links['ays_facebook_link'] : '';
            $twitter_link = isset($ays_social_links['ays_twitter_link']) && $ays_social_links['ays_twitter_link'] != '' ? $ays_social_links['ays_twitter_link'] : '';
            $social_links = array(
                'linkedin_link' => $linkedin_link,
                'facebook_link' => $facebook_link,
                'twitter_link' => $twitter_link
            );

            // Show quiz head information. Quiz title and description            
            $show_quiz_title = (isset($data['ays_show_quiz_title']) && $data['ays_show_quiz_title'] == "on") ? 'on' : 'off';
            $show_quiz_desc = (isset($data['ays_show_quiz_desc']) && $data['ays_show_quiz_desc'] == "on") ? 'on' : 'off';

            // Show login form for not logged in users
            $show_login_form = (isset($data['ays_show_login_form']) && $data['ays_show_login_form'] == "on") ? 'on' : 'off';

            // Quiz container max-width for mobile
            $mobile_max_width = (isset($data['ays_mobile_max_width']) && $data['ays_mobile_max_width'] != "") ? $data['ays_mobile_max_width'] : '';

            // Limit users by option
            $limit_users_by = (isset($data['ays_limit_users_by']) && $data['ays_limit_users_by'] != '') ? $data['ays_limit_users_by'] : 'ip';

            //Send results to user
            $send_results_user = isset($data['ays_send_results_user']) && $data['ays_send_results_user'] == 'on' ? 'on' : 'off';

            //Send interval message to user
            $send_interval_msg = isset($data['ays_send_interval_msg']) && $data['ays_send_interval_msg'] == 'on' ? 'on' : 'off';
            
            // Question bank by categories
            $question_bank_type = (isset($data['ays_question_bank_type']) && $data['ays_question_bank_type'] != "") ? $data['ays_question_bank_type'] : "general";
            $questions_bank_cat_count = (isset($data['ays_questions_bank_cat_count']) && $data['ays_questions_bank_cat_count'] != "") ? $data['ays_questions_bank_cat_count'] : array();

            // Limitation tackers of quiz
            $enable_tackers_count = (isset($data['ays_enable_tackers_count']) && $data['ays_enable_tackers_count'] == 'on') ? 'on' : 'off';
            $tackers_count = (isset($data['ays_tackers_count']) && $data['ays_tackers_count'] != '') ? $data['ays_tackers_count'] : '';
            
            // Right/wrong answer text showing time option
            $explanation_time = (isset($data['ays_explanation_time']) && $data['ays_explanation_time'] != '') ? $data['ays_explanation_time'] : '4';

            // Enable claer answer button
            $enable_clear_answer = (isset($data['ays_enable_clear_answer']) && $data['ays_enable_clear_answer'] == "on") ? 'on' : 'off';

            //Send results to admin
            $send_results_admin = (isset($data['ays_send_results_admin']) && $data['ays_send_results_admin'] == 'on') ? 'on' : 'off';

            //Send interval message to admin
            $send_interval_msg_to_admin = (isset($data['ays_send_interval_msg_to_admin']) && $data['ays_send_interval_msg_to_admin'] == 'on') ? 'on' : 'off';

            // Show quiz category
            $show_category = (isset($data['ays_show_category']) && $data['ays_show_category'] == "on") ? 'on' : 'off';

            // Show question category
            $show_question_category = (isset($data['ays_show_question_category']) && $data['ays_show_question_category'] == "on") ? 'on' : 'off';

            // Answers padding option
            $answers_padding = (isset($data['ays_answers_padding']) && $data['ays_answers_padding'] != '') ? $data['ays_answers_padding'] : '10';

            // Answers margin option
            $answers_margin = (isset($data['ays_answers_margin']) && $data['ays_answers_margin'] != '') ? $data['ays_answers_margin'] : '10';

            // Answers border options
            $answers_border = (isset($data['ays_answers_border']) && $data['ays_answers_border'] == 'on') ? 'on' : 'off';
            $answers_border_width = (isset($data['ays_answers_border_width']) && $data['ays_answers_border_width'] != '') ? $data['ays_answers_border_width'] : '1';
            $answers_border_style = (isset($data['ays_answers_border_style']) && $data['ays_answers_border_style'] != '') ? $data['ays_answers_border_style'] : 'solid';
            $answers_border_color = (isset($data['ays_answers_border_color']) && $data['ays_answers_border_color'] != '') ? $data['ays_answers_border_color'] : '';

            $answers_box_shadow = (isset($data['ays_answers_box_shadow']) && $data['ays_answers_box_shadow'] == 'on') ? 'on' : 'off';
            $answers_box_shadow_color = (isset($data['ays_answers_box_shadow_color']) && $data['ays_answers_box_shadow_color'] != '') ? $data['ays_answers_box_shadow_color'] : '#000';

            // Answers image options
            $ans_img_height = (isset($data['ays_ans_img_height']) && $data['ays_ans_img_height'] != '') ? $data['ays_ans_img_height'] : '0';
            $ans_img_caption_style = (isset($data['ays_ans_img_caption_style']) && $data['ays_ans_img_caption_style'] != '') ? $data['ays_ans_img_caption_style'] : 'outside';
            $ans_img_caption_position = (isset($data['ays_ans_img_caption_position']) && $data['ays_ans_img_caption_position'] != '') ? $data['ays_ans_img_caption_position'] : 'bottom';

            // Show answers caption
            $show_answers_caption = (isset($data['ays_show_answers_caption']) && $data['ays_show_answers_caption'] == 'on') ? 'on' : 'off';

            // Answers right/wrong answers icons
            $ans_right_wrong_icon = (isset($data['ays_ans_right_wrong_icon']) && $data['ays_ans_right_wrong_icon'] != '') ? $data['ays_ans_right_wrong_icon'] : 'default';

            // Show interval message
            $show_interval_message = (isset($data['ays_show_interval_message']) && $data['ays_show_interval_message'] == 'on') ? 'on' : 'off';

            // Display score option
            $display_score = (isset($data['ays_display_score']) && $data['ays_display_score'] != "") ? $data['ays_display_score'] : 'by_percentage';

            // Right / Wrong answers sound option
            $enable_rw_asnwers_sounds = (isset($data['ays_enable_rw_asnwers_sounds']) && $data['ays_enable_rw_asnwers_sounds'] == "on") ? 'on' : 'off';

            
            $options = array(
                'color'                         => sanitize_text_field( $data['ays_quiz_color'] ),
                'bg_color'                      => sanitize_text_field( $data['ays_quiz_bg_color'] ),
                'text_color'                    => sanitize_text_field( $data['ays_quiz_text_color'] ),
                'height'                        => absint( intval( $data['ays_quiz_height'] ) ),
                'width'                         => absint( intval( $data['ays_quiz_width'] ) ),
                'enable_logged_users'           => $ays_enable_logged_users,
                'information_form'              => $data['ays_information_form'],
                'form_name'                     => $ays_form_name,
                'form_email'                    => $ays_form_email,
                'form_phone'                    => $ays_form_phone,
                'image_width'                   => $data['ays_image_width'],
                'image_height'                  => $data['ays_image_height'],
                'enable_correction'             => $enable_correction,
                'enable_progress_bar'           => $enable_progressbar,
                'enable_questions_result'       => $enable_questions_result,
                'randomize_questions'           => $enable_random_questions,
                'randomize_answers'             => $enable_random_answers,
                'enable_questions_counter'      => $enable_questions_counter,
                'enable_restriction_pass'       => $enable_restriction_pass,
                'restriction_pass_message'      => $data['restriction_pass_message'],
                'user_role'                     => $limit_user_roles,
                'custom_css'                    => stripslashes($data['ays_custom_css']),
                'limit_users'                   => $limit_users,
                'limitation_message'            => $data['ays_limitation_message'],
                'redirect_url'                  => $data['ays_redirect_url'],
                'redirection_delay'             => intval($data['ays_redirection_delay']),
                'answers_view'                  => $data['ays_answers_view'],
                'enable_rtl_direction'          => $enable_rtl,
                'enable_logged_users_message'   => $enable_logged_users_mas,
                'questions_count'               => $data['ays_questions_count'],
                'enable_question_bank'          => $question_bank,
                'enable_live_progress_bar'      => $live_progressbar,
                'enable_percent_view'           => $percent_view,
                'enable_average_statistical'    => $avarage_statistical,
                'enable_next_button'            => $next_button,
                'enable_previous_button'        => $prev_button,
                'enable_arrows'                 => $enable_arrows,
                'timer_text'                    => $data['ays_timer_text'],
                'quiz_theme'                    => $quiz_theme,
                'enable_social_buttons'         => $social_buttons,
                'final_result_text'             => stripslashes($data['ays_final_result_text']),
                'enable_pass_count'             => $enable_pass_count,
                'hide_score'                    => $hide_score,
                'rate_form_title'               => $rate_form_title,
                'box_shadow_color'              => $quiz_box_shadow_color,
                'quiz_border_radius'            => $quiz_border_radius,
                'quiz_bg_image'                 => $quiz_bg_image,
                'quiz_border_width'             => $quiz_border_width,
                'quiz_border_style'             => $quiz_border_style,
                'quiz_border_color'             => $quiz_border_color,
                'quiz_loader'                   => $quiz_loader,
                'create_date'                   => $quiz_create_date,
                'author'                        => $author,
                'quest_animation'               => $quest_animation,
                'enable_bg_music'               => $enable_bg_music,
                'quiz_bg_music'                 => $quiz_bg_music,
                'answers_font_size'             => $answers_font_size,
                'show_create_date'              => $show_create_date,
                'show_author'                   => $show_author,
                'enable_early_finish'           => $enable_early_finish,
                'answers_rw_texts'              => $answers_rw_texts,
                'disable_store_data'            => $disable_store_data,
                'enable_background_gradient'    => $enable_background_gradient,
                'background_gradient_color_1'   => $quiz_background_gradient_color_1,
                'background_gradient_color_2'   => $quiz_background_gradient_color_2,
                'quiz_gradient_direction'       => $quiz_gradient_direction,
                'redirect_after_submit'         => $redirect_after_submit,
                'submit_redirect_url'           => $submit_redirect_url,
                'submit_redirect_delay'         => $submit_redirect_delay,
                'progress_bar_style'            => $progress_bar_style,
                'enable_exit_button'            => $enable_exit_button,
                'exit_redirect_url'             => $exit_redirect_url,
                'image_sizing'                  => $image_sizing,
                'quiz_bg_image_position'        => $quiz_bg_image_position,
                'custom_class'                  => $custom_class,
                'enable_social_links'           => $enable_social_links,
                'social_links'                  => $social_links,
                'show_quiz_title'               => $show_quiz_title,
                'show_quiz_desc'                => $show_quiz_desc,
                'show_login_form'               => $show_login_form,
                'mobile_max_width'              => $mobile_max_width,
                'limit_users_by'                => $limit_users_by,
				'explanation_time'              => $explanation_time,
				'enable_clear_answer'           => $enable_clear_answer,
				'show_category'                 => $show_category,
				'show_question_category'        => $show_question_category,
                'answers_padding'               => $answers_padding,
                'answers_border'                => $answers_border,
                'answers_border_width'          => $answers_border_width,
                'answers_border_style'          => $answers_border_style,
                'answers_border_color'          => $answers_border_color,
                'ans_img_height'                => $ans_img_height,
                'ans_img_caption_style'         => $ans_img_caption_style,
                'ans_img_caption_position'      => $ans_img_caption_position,
                'answers_box_shadow'            => $answers_box_shadow,
                'answers_box_shadow_color'      => $answers_box_shadow_color,
                'show_answers_caption'          => $show_answers_caption,
                'answers_margin'                => $answers_margin,
                'ans_right_wrong_icon'          => $ans_right_wrong_icon,
                'display_score'                 => $display_score,
                'enable_rw_asnwers_sounds'      => $enable_rw_asnwers_sounds,

                
                'question_count_per_page'       => $question_count_per_page,
                'question_count_per_page_number'=> $data['ays_question_count_per_page_number'],
                'mail_message'                  => $data['ays_mail_message'],
                'enable_certificate'            => $enable_certificate,
                'certificate_pass'              => $data['ays_certificate_pass'],
                'enable_smtp'                   => $enable_smtp,
                'smtp_username'                 => $data['ays_smtp_username'],
                'smtp_password'                 => $data['ays_smtp_password'],
                'smtp_host'                     => $data['ays_smtp_host'],
                'smtp_secure'                   => $data['ays_smtp_secure'],
                'smtp_port'                     => $data['ays_smtp_port'],
                'additional_emails'             => $additional_emails, //aray
                'email_config_from_email'       => $email_config_from_email, //aray
                'email_config_from_name'        => $email_config_from_name, //aray
                'email_config_from_subject'     => $email_config_from_subject, //aray
                'form_title'                    => stripslashes($data['ays_form_title']),
                'certificate_title'             => stripslashes($data['ays_certificate_title']),
                'certificate_body'              => stripslashes($data['ays_certificate_body']),
                'mailchimp_list'                => $mailchimp_list,
				'active_date_check'             => $active_date_check,
				'activeInterval'                => $activeInterval,
				'deactiveInterval'              => $deactiveInterval,
				'active_date_message'           => $active_date_message,
                'checkbox_score_by'             => $checkbox_score_by,
                'calculate_score'               => $calculate_score,
				'send_results_user'             => $send_results_user,
				'send_interval_msg'             => $send_interval_msg,
                'question_bank_type'            => $question_bank_type,
                'questions_bank_cat_count'      => $questions_bank_cat_count,
                'enable_tackers_count'          => $enable_tackers_count,
                'tackers_count'                 => $tackers_count,
                'send_results_admin'            => $send_results_admin,
                'send_interval_msg_to_admin'    => $send_interval_msg_to_admin,
                'show_interval_message'         => $show_interval_message,
                
                'paypal_amount'                 => $paypal_amount,
                'paypal_currency'               => $paypal_currency,
				'enable_monitor'                => $enable_monitor,
				'monitor_list'                  => $monitor_list,
				'active_camp_list'              => $active_camp_list,
				'enable_slack'                  => $enable_slack,
				'slack_conversation'            => $slack_conversation,
				'active_camp_automation'        => $active_camp_automation,
				'enable_active_camp'            => $enable_active_camp,
				'enable_zapier'                 => $enable_zapier,
            );
            
            $options['quiz_attributes'] = $data['ays_quiz_attributes'];
            $options['required_fields'] = !isset($data['ays_required_field']) ? null : $data['ays_required_field'];
            if( isset( $data['ays_enable_timer'] ) && $data['ays_enable_timer'] == 'on' ){
                $options['enable_timer'] = 'on';
            }else{                
                $options['enable_timer'] = 'off';
            }
            
            if( isset( $data['ays_quiz_timer'] ) && $data['ays_quiz_timer'] != 0 ){
                $options['timer'] = absint( intval( $data['ays_quiz_timer'] ) );
            }else{
                $options['timer'] = 100;
            }
            $options['enable_quiz_rate'] = ( isset( $data['ays_enable_quiz_rate'] ) && $data['ays_enable_quiz_rate'] == 'on' ) ? 'on' : 'off';
            $options['enable_rate_avg'] = ( isset( $data['ays_enable_rate_avg'] ) && $data['ays_enable_rate_avg'] == 'on' ) ? 'on' : 'off';
            $options['enable_box_shadow'] = ( isset( $data['ays_enable_box_shadow'] ) && $data['ays_enable_box_shadow'] == 'on' ) ? 'on' : 'off';
            $options['enable_border'] = ( isset( $data['ays_enable_border'] ) && $data['ays_enable_border'] == 'on' ) ? 'on' : 'off';
            $options['quiz_timer_in_title'] = ( isset( $data['ays_quiz_timer_in_title'] ) && $data['ays_quiz_timer_in_title'] == 'on' ) ? 'on' : 'off';
            
            $options['enable_rate_comments'] = ( isset( $data['ays_enable_rate_comments'] ) && $data['ays_enable_rate_comments'] == 'on' ) ? 'on' : 'off';
            
            $options['enable_restart_button'] = ( isset( $data['ays_enable_restart_button'] ) && $data['ays_enable_restart_button'] == 'on' ) ? 'on' : 'off';
            
            $options['autofill_user_data'] = ( isset( $data['ays_autofill_user_data'] ) && $data['ays_autofill_user_data'] == 'on' ) ? 'on' : 'off';
            
            $options['enable_copy_protection'] = ( isset( $data['ays_enable_copy_protection'] ) && $data['ays_enable_copy_protection'] == 'on' ) ? 'on' : 'off';
            
            if( isset( $data['ays_enable_paypal'] ) && $data['ays_enable_paypal'] == 'on' ){
                $options['enable_paypal'] = 'on';
            }else{
                $options['enable_paypal'] = 'off';
            }
            
            $options['enable_mailchimp'] = ( isset( $data['ays_enable_mailchimp'] ) && $data['ays_enable_mailchimp'] == 'on' ) ? 'on' : 'off';
            
            if( isset( $data['ays_enable_restriction_pass'] ) && $data['ays_enable_restriction_pass'] == 'on' ){
                $options['ays_enable_restriction_pass'] = 'on';
                $options['enable_logged_users'] = 'on';
            }else{
                $options['ays_enable_restriction_pass'] = 'off';
            }
            
            if( isset( $data['ays_enable_user_mail'] ) && $data['ays_enable_user_mail'] == 'on' ){
                $options['user_mail'] = 'on';
            }
            if( isset( $data['ays_enable_admin_mail'] ) && $data['ays_enable_admin_mail'] == 'on' ){
                $options['admin_mail'] = 'on';
            }
            if( !isset( $data['ays_limit_users'] )){
                $options['limit_users'] = 'off';
            }
            if(isset($data['ays_enable_result']) && $data['ays_enable_result']=='on'){
                if( isset( $data['ays_quiz_result_text'] ) && $data['ays_quiz_result_text'] != '' ){
                    $options['result_text'] = $data['ays_quiz_result_text'];
                    $options['enable_result'] = 'on';
                }
            }else{
                $options['result_text'] = $data['ays_quiz_result_text'];
                $options['enable_result'] = 'off';
            }
            
            // Add post for quiz
            $add_post_for_quiz = (isset($data['ays_add_post_for_quiz']) && $data['ays_add_post_for_quiz'] == 'on') ? 'on' : 'off';
            $add_postcat_for_quiz =  isset($data['ays_add_postcat_for_quiz']) ? $data['ays_add_postcat_for_quiz'] : array();


            $intervals_max = $data['interval_max'];
            $intervals_min = $data['interval_min'];
            $intervals_texts = $data['interval_text'];
            $interval_images = $data['interval_image'];
			$interval_wproduct = $data['interval_wproduct'];
            $intervals = array();
            for($i = 0; $i < count($intervals_max); $i++){
                $intervals[$i] = array();
                $intervals[$i]['interval_min'] = $intervals_min[$i];
                $intervals[$i]['interval_max'] = $intervals_max[$i];
                $intervals[$i]['interval_text'] = stripslashes($intervals_texts[$i]);
                $intervals[$i]['interval_image'] = $interval_images[$i];
				$intervals[$i]['interval_wproduct'] = absint($interval_wproduct[$i]);
            }
            $quiz_intervals = json_encode($intervals);

            if($id == 0) {
                $quiz_result = $wpdb->insert(
                    $quiz_table,
                    array(
                        'title'             => $title,
                        'description'       => $description,
                        'quiz_image'        => $image,
                        'quiz_category_id'  => $quiz_category_id,
                        'question_ids'      => $question_ids,
                        'published'         => $published,
                        'ordering'          => $ordering,
                        'options'           => json_encode($options),
                        'intervals'         => $quiz_intervals
                    ),
                    array(
                        '%s',
                        '%s',
                        '%s',
                        '%d',
                        '%s',
                        '%d',
                        '%d',
                        '%s',
                        '%s'

                    )
                );
                $message = 'created';
                /* 
                ========================================== 
                    Adding post for quiz and inserting
                    quiz shortcode into post
                ========================================== 
                */
                $quiz_insert_id = $wpdb->insert_id;
                
                if($add_post_for_quiz == "on"){
                    global $user_ID;
                    
                    $post_content = '[ays_quiz id="'.$quiz_insert_id.'"]';
                    
                    if ( Quiz_Maker_Admin::is_active_gutenberg() ) {
                        $post_content = '<!-- wp:quiz-maker/quiz {"metaFieldValue":'.$quiz_insert_id.',"shortcode":"[ays_quiz id='.$quiz_insert_id.']"} -->
                        <div class="ays-quiz-gutenberg" class="wp-block-quiz-maker-quiz">[ays_quiz id="'.$quiz_insert_id.'"]</div>
                        <!-- /wp:quiz-maker/quiz -->';
                    }
                    
                    $new_post = array(
                        'post_title' => $title,
                        'post_content' => $post_content,
                        'post_status' => 'publish',
                        'post_date' => current_time( 'mysql' ),
                        'post_author' => $user_ID,
                        'post_type' => 'post',
                        'post_category' => $add_postcat_for_quiz
                    );
                    $post_id = wp_insert_post($new_post);
                    if(! empty($image)){
                        $sql = "SELECT ID FROM ".$wpdb->prefix."posts WHERE post_type = 'attachment' AND guid = '".$image."'";
                        $attachment_id = intval($wpdb->get_var($sql));
                        if($attachment_id !== 0){
                            $featured_image = set_post_thumbnail($post_id, $attachment_id);
                        }
                    }
                    $quiz_post_result = $wpdb->update(
                        $quiz_table,
                        array( 'post_id' => $post_id ),
                        array( 'id' => $quiz_insert_id ),
                        array( '%d' ),
                        array( '%d' )
                    );
                    
                }
                /* 
                ========================================== 
                    Creating post end 
                ========================================== 
                */

            }else{
                $quiz_result = $wpdb->update(
                    $quiz_table,
                    array(
                        'title'             => $title,
                        'description'       => $description,
                        'quiz_image'        => $image,
                        'quiz_category_id'  => $quiz_category_id,
                        'question_ids'      => $question_ids,
                        'published'         => $published,
                        'options'           => json_encode($options),
                        'intervals'         => $quiz_intervals
                    ),
                    array( 'id' => $id ),
                    array(
                        '%s',
                        '%s',
                        '%s',
                        '%d',
                        '%s',
                        '%d',
                        '%s',
                        '%s'
                    ),
                    array( '%d' )
                );
                $message = 'updated';
            }
            
            $ays_quiz_tab = isset($data['ays_quiz_tab']) ? $data['ays_quiz_tab'] : 'tab1';
            if( $quiz_result >= 0 ){
                if($ays_change_type != ''){
                    if($id == null){
                        $url = esc_url_raw( add_query_arg( array(
                            "action"    => "edit",
                            "quiz"      => $quiz_insert_id,
                            "ays_quiz_tab"  => $ays_quiz_tab,
                            "status"    => $message
                        ) ) );
                    }else{
                        $url = esc_url_raw( remove_query_arg(false) ) . '&ays_quiz_tab='.$ays_quiz_tab.'&status=' . $message;
                    }
                    wp_redirect( $url );
                }else{
                    $url = esc_url_raw( remove_query_arg( array('action', 'question') ) ) . '&status=' . $message;
                    wp_redirect( $url );
                }
            }
        }
    }

    private function get_max_id() {
        global $wpdb;
        $quiz_table = $wpdb->prefix . 'aysquiz_quizes';

        $sql = "SELECT max(id) FROM {$quiz_table}";

        $result = $wpdb->get_var($sql);

        return $result;
    }

    /**
     * Delete a customer record.
     *
     * @param int $id customer ID
     */
    public static function delete_quizes( $id ) {
        global $wpdb;
        $reports_table = $wpdb->prefix . "aysquiz_reports";
        $wpdb->delete(
            "{$wpdb->prefix}aysquiz_quizes",
            array( 'id' => $id ),
            array( '%d' )
        );
        $wpdb->delete(
            $reports_table,
            array( 'quiz_id' => $id ),
            array( '%d' )
        );
    }


    /**
     * Returns the count of records in the database.
     *
     * @return null|string
     */
    public static function record_count() {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_quizes";
        
        if( isset( $_REQUEST['fstatus'] ) ){
            $fstatus = $_REQUEST['fstatus'];
            if($fstatus !== null){
                $sql .= " WHERE published = ".$fstatus." ";
            }
        }
        
        return $wpdb->get_var( $sql );
    }
    
    public static function all_record_count() {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_quizes";

        return $wpdb->get_var( $sql );
    }

    public static function published_questions_record_count() {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_questions WHERE published=1";

        return $wpdb->get_var( $sql );
    }
    
    public static function published_quizzes_count() {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_quizes WHERE published=1";

        return $wpdb->get_var( $sql );
    }
    
    public static function unpublished_quizzes_count() {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_quizes WHERE published=0";

        return $wpdb->get_var( $sql );
    }

    public static function get_quiz_pass_count($id) {
        global $wpdb;
        $quiz_id = intval($id);
        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_reports WHERE quiz_id=".$quiz_id;

        return $wpdb->get_var( $sql );
    }

    public function duplicate_quizzes( $id ){
        global $wpdb;
        $quizzes_table = $wpdb->prefix."aysquiz_quizes";
        $quiz = $this->get_quiz_by_id($id);
        
        $user_id = get_current_user_id();
        $user = get_userdata($user_id);
        $author = array(
            'id' => $user->ID,
            'name' => $user->data->display_name
        );
        
        $max_id = $this->get_max_id();
        $ordering = ( $max_id != NULL ) ? ( $max_id + 1 ) : 1;
        
        $options = json_decode($quiz['options'], true);
        
        $options['create_date'] = current_time( 'mysql' );
        $options['author'] = $author;
        
        $result = $wpdb->insert(
            $quizzes_table,            
            array(
                'title'             => "Copy - ".$quiz['title'],
                'description'       => $quiz['description'],
                'quiz_image'        => $quiz['quiz_image'],
                'quiz_category_id'  => intval($quiz['quiz_category_id']),
                'question_ids'      => $quiz['question_ids'],
                'published'         => intval($quiz['published']),
                'ordering'          => $ordering,
                'options'           => json_encode($options)
            ),
            array(
                '%s',
                '%s',
                '%s',
                '%d',
                '%s',
                '%d',
                '%d',
                '%s'
            )
        );
        if( $result >= 0 ){
            $message = "duplicated";
            $url = esc_url_raw( remove_query_arg(array('action', 'question')  ) ) . '&status=' . $message;
            wp_redirect( $url );
        }
        
    }


    /** Text displayed when no customer data is available */
    public function no_items() {
        echo __( 'There are no quizzes yet.', $this->plugin_name );
    }


    /**
     * Render a column when no column specific method exist.
     *
     * @param array $item
     * @param string $column_name
     *
     * @return mixed
     */
    public function column_default( $item, $column_name ) {
        switch ( $column_name ) {
            case 'title':
            case 'quiz_category_id':
            case 'shortcode':
            case 'code_include':
            case 'items_count':
            case 'create_date':
            case 'completed_count':
            case 'id':
                return $item[ $column_name ];
                break;
            default:
                return print_r( $item, true ); //Show the whole array for troubleshooting purposes
        }
    }

    /**
     * Render the bulk edit checkbox
     *
     * @param array $item
     *
     * @return string
     */
    function column_cb( $item ) {
        return sprintf(
            '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
        );
    }


    /**
     * Method for name column
     *
     * @param array $item an array of DB data
     *
     * @return string
     */
    function column_title( $item ) {
        $delete_nonce = wp_create_nonce( $this->plugin_name . '-delete-quiz' );

        $restitle = Quiz_Maker_Admin::ays_restriction_string("word",stripcslashes($item['title']), 5);
        
        $title = sprintf( '<a href="?page=%s&action=%s&quiz=%d">%s</a>', esc_attr( $_REQUEST['page'] ), 'edit', absint( $item['id'] ), $restitle);
        $actions = array(
            'edit' => sprintf( '<a href="?page=%s&action=%s&quiz=%d">'. __('Edit', $this->plugin_name) .'</a>', esc_attr( $_REQUEST['page'] ), 'edit', absint( $item['id'] ) ),
            'duplicate' => sprintf( '<a href="?page=%s&action=%s&quiz=%d">'. __('Duplicate', $this->plugin_name) .'</a>', esc_attr( $_REQUEST['page'] ), 'duplicate', absint( $item['id'] ) ),
            'delete' => sprintf( '<a class="ays_confirm_del" data-message="%s" href="?page=%s&action=%s&quiz=%s&_wpnonce=%s">'. __('Delete', $this->plugin_name) .'</a>', $restitle, esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $delete_nonce )
        );

        return $title . $this->row_actions( $actions );
    }

    function column_quiz_category_id( $item ) {
        global $wpdb;
        $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_quizcategories WHERE id=" . absint( intval( $item['quiz_category_id'] ) );

        $result = $wpdb->get_row($sql, 'ARRAY_A');

        return $result['title'];
    }

    function column_code_include( $item ) {
        $shortcode = htmlentities('[ays_quiz id="'.$item["id"].'"]');
        return '<input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="<?php echo do_shortcode('.$shortcode.'); ?>" style="max-width:100%;" />';
    }

    function scolumn_published( $item ) {
        switch( $item['published'] ) {
            case "1":
                return '<span class="ays-publish-status"><i class="ays_fa ays_fa_check_square_o" aria-hidden="true"></i> '. __('Published',$this->plugin_name) . '</span>';
                break;
            case "0":
                return '<span class="ays-publish-status"><i class="ays_fa ays_fa_square_o" aria-hidden="true"></i> '. __('Unpublished',$this->plugin_name) . '</span>';
                break;
        }
    }

    function column_shortcode( $item ) {
        $shortcode = htmlentities('[ays_quiz id="'.$item["id"].'"]');
        return '<input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="'.$shortcode.'" />';
    }

    function column_create_date( $item ) {
        
        $options = json_decode($item['options'], true);
        $date = isset($options['create_date']) && $options['create_date'] != '' ? $options['create_date'] : "0000-00-00 00:00:00";
        if(isset($options['author'])){
            if(is_array($options['author'])){
                $author = $options['author'];
            }else{
                $author = json_decode($options['author'], true);
            }
        }else{
            $author = array("name"=>"Unknown");
        }
        $text = "";
        if(Quiz_Maker_Admin::validateDate($date)){
            $text .= "<p><b>Date:</b> ".$date."</p>";
        }
        if($author['name'] !== "Unknown"){
            $text .= "<p><b>Author:</b> ".$author['name']."</p>";
        }
        return $text;
    }

    function column_completed_count( $item ) {
        $id = $item['id'];
        $passed_count = $this->get_quiz_pass_count($id);
        $text = "<p style='text-align:center;font-size:14px;'>".$passed_count."</p>";
        return $text;
    }

    function column_items_count( $item ) {
        global $wpdb;
        if(empty($item['question_ids'])){
            $count = 0;
        }else{
            $count = explode(',', $item['question_ids']);
            $count = count($count);
        }
        return "<p style='text-align:center;font-size:14px;'>" . $count . "</p>";
    }

    /**
     *  Associative array of columns
     *
     * @return array
     */
    function get_columns() {
        $columns = array(
            'cb'                => '<input type="checkbox" />',
            'title'             => __( 'Title', $this->plugin_name ),
            'quiz_category_id'  => __( 'Category', $this->plugin_name ),
            'shortcode'         => __( 'Shortcode', $this->plugin_name ),
            'code_include'      => __( 'Code include', $this->plugin_name ),
            'items_count'       => __( 'Count', $this->plugin_name ),
            'create_date'       => __( 'Created', $this->plugin_name ),
            'completed_count'   => __( 'Completed count', $this->plugin_name ),
            'id'                => __( 'ID', $this->plugin_name ),
        );

        return $columns;
    }


    /**
     * Columns to make sortable.
     *
     * @return array
     */
    public function get_sortable_columns() {
        $sortable_columns = array(
            'title'         => array( 'title', true ),
            'quiz_category_id'      => array( 'quiz_category_id', true ),
//            'published'     => array( 'published', true ),
            'id'            => array( 'id', true ),
        );

        return $sortable_columns;
    }

    /**
     * Returns an associative array containing the bulk action
     *
     * @return array
     */
    public function get_bulk_actions() {
        $actions = array(
            'bulk-delete' => __('Delete', $this->plugin_name)
        );

        return $actions;
    }



    /**
     * Handles data query and filter, sorting, and pagination.
     */
    public function prepare_items() {

        $this->_column_headers = $this->get_column_info();

        /** Process bulk action */
        $this->process_bulk_action();

        $per_page     = $this->get_items_per_page( 'quizes_per_page', 20 );
        $current_page = $this->get_pagenum();
        $total_items  = self::record_count();

        $this->set_pagination_args( array(
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page'    => $per_page, //WE have to determine how many items to show on a page
            'total_pages' => ceil( $total_items / $per_page )
        ) );

        $this->items = self::get_quizes( $per_page, $current_page );
    }

    public function process_bulk_action() {
        //Detect when a bulk action is being triggered...
        $message = 'deleted';
        if ( 'delete' === $this->current_action() ) {

            // In our file that handles the request, verify the nonce.
            $nonce = esc_attr( $_REQUEST['_wpnonce'] );

            if ( ! wp_verify_nonce( $nonce, $this->plugin_name . '-delete-quiz' ) ) {
                die( 'Go get a life script kiddies' );
            }
            else {
                self::delete_quizes( absint( $_GET['quiz'] ) );

                // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
                // add_query_arg() return the current url

                $url = esc_url_raw( remove_query_arg(array('action', 'quiz', '_wpnonce')  ) ) . '&status=' . $message;
                wp_redirect( $url );
            }

        }

        // If the delete bulk action is triggered
        if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
            || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
        ) {

            $delete_ids = esc_sql( $_POST['bulk-delete'] );

            // loop over the array of record IDs and delete them
            foreach ( $delete_ids as $id ) {
                self::delete_quizes( $id );

            }

            // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
            // add_query_arg() return the current url

            $url = esc_url_raw( remove_query_arg(array('action', 'quiz', '_wpnonce')  ) ) . '&status=' . $message;
            wp_redirect( $url );
        }
    }

    public function quiz_notices(){
        $status = (isset($_REQUEST['status'])) ? sanitize_text_field( $_REQUEST['status'] ) : '';

        if ( empty( $status ) )
            return;

        if ( 'created' == $status )
            $updated_message = esc_html( __( 'Quiz created.', $this->plugin_name ) );
        elseif ( 'updated' == $status )
            $updated_message = esc_html( __( 'Quiz saved.', $this->plugin_name ) );
        elseif ( 'duplicated' == $status )
            $updated_message = esc_html( __( 'Quiz duplicated.', $this->plugin_name ) );
        elseif ( 'deleted' == $status )
            $updated_message = esc_html( __( 'Quiz deleted.', $this->plugin_name ) );

        if ( empty( $updated_message ) )
            return;

        ?>
        <div class="notice notice-success is-dismissible">
            <p> <?php echo $updated_message; ?> </p>
        </div>
        <?php
    }
}
