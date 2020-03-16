<?php
    $actions = $this->settings_obj;

    if( isset( $_REQUEST['ays_submit'] ) ){
        $actions->store_data($_REQUEST);
    }
    if(isset($_GET['ays_quiz_tab'])){
        $ays_quiz_tab = $_GET['ays_quiz_tab'];
    }else{
        $ays_quiz_tab = 'tab1';
    }

    if(isset($_GET['action']) && $_GET['action'] == 'update_duration'){
        $actions->update_duration_data();
    }

    $data = $actions->get_data();
    $db_data = $actions->get_db_data();

    $paypal_client_id = isset($data['paypal_client_id']) ? $data['paypal_client_id'] : '' ;
    $paypal_payment_terms = isset($data['payment_terms']) ? $data['payment_terms'] : 'lifetime' ;

    global $wp_roles;
    $ays_users_roles = $wp_roles->role_names;
    $user_roles = json_decode($actions->ays_get_setting('user_roles'), true);
    $mailchimp_res = ($actions->ays_get_setting('mailchimp') === false) ? json_encode(array()) : $actions->ays_get_setting('mailchimp');
    $mailchimp = json_decode($mailchimp_res, true);
    $mailchimp_username = isset($mailchimp['username']) ? $mailchimp['username'] : '' ;
    $mailchimp_api_key = isset($mailchimp['apiKey']) ? $mailchimp['apiKey'] : '' ;

    $monitor_res     = ($actions->ays_get_setting('monitor') === false) ? json_encode(array()) : $actions->ays_get_setting('monitor');
    $monitor         = json_decode($monitor_res, true);
    $monitor_client  = isset($monitor['client']) ? $monitor['client'] : '';
    $monitor_api_key = isset($monitor['apiKey']) ? $monitor['apiKey'] : '';

    $zapier_res  = ($actions->ays_get_setting('zapier') === false) ? json_encode(array()) : $actions->ays_get_setting('zapier');
    $zapier      = json_decode($zapier_res, true);
    $zapier_hook = isset($zapier['hook']) ? $zapier['hook'] : '';

    $active_camp_res     = ($actions->ays_get_setting('active_camp') === false) ? json_encode(array()) : $actions->ays_get_setting('active_camp');
    $active_camp         = json_decode($active_camp_res, true);
    $active_camp_url     = isset($active_camp['url']) ? $active_camp['url'] : '';
    $active_camp_api_key = isset($active_camp['apiKey']) ? $active_camp['apiKey'] : '';

    $slack_res    = ($actions->ays_get_setting('slack') === false) ? json_encode(array()) : $actions->ays_get_setting('slack');
    $slack        = json_decode($slack_res, true);
    $slack_client = isset($slack['client']) ? $slack['client'] : '';
    $slack_secret = isset($slack['secret']) ? $slack['secret'] : '';
    $slack_token = isset($slack['token']) ? $slack['token'] : '';
    $slack_oauth  = !empty($_GET['oauth']) && $_GET['oauth'] == 'slack';
    if ($slack_oauth) {
        $slack_temp_code = !empty($_GET['code']) ? $_GET['code'] : "";
        $slack_client    = !empty($_GET['state']) ? $_GET['state'] : "";
        $ays_quiz_tab    = 'tab2';
    }

    // AV Leaderboard

    $leadboard_res = ($actions->ays_get_setting('leaderboard') === false) ? json_encode(array()) : $actions->ays_get_setting('leaderboard');
    $leadboard = json_decode($leadboard_res, true);

    $ind_leadboard_count = isset($leadboard['individual']['count']) ? $leadboard['individual']['count'] : '5' ;
    $ind_leadboard_width = isset($leadboard['individual']['width']) ? $leadboard['individual']['width'] : '0' ;
    $ind_leadboard_orderby = isset($leadboard['individual']['orderby']) ? $leadboard['individual']['orderby'] : 'id' ;
    $ind_leadboard_sort = isset($leadboard['individual']['sort']) ? $leadboard['individual']['sort'] : 'avg' ;
    $ind_leadboard_color = isset($leadboard['individual']['color']) ? $leadboard['individual']['color'] : '#99BB5A' ;

    $glob_leadboard_count = isset($leadboard['global']['count']) ? $leadboard['global']['count'] : '5' ;
    $glob_leadboard_width = isset($leadboard['global']['width']) ? $leadboard['global']['width'] : '0' ;
    $glob_leadboard_orderby = isset($leadboard['global']['orderby']) ? $leadboard['global']['orderby'] : 'id' ;
    $glob_leadboard_sort = isset($leadboard['global']['sort']) ? $leadboard['global']['sort'] : 'avg' ;
    $glob_leadboard_color = isset($leadboard['global']['color']) ? $leadboard['global']['color'] : '#99BB5A' ;

    //AV end
    $quizzes = $actions->get_reports_titles();
    $empry_dur_count = $actions->get_empty_duration_rows_count();

    $question_types = array(
        "radio" => __("Radio", $this->plugin_name),
        "checkbox" => __("Checkbox( Multiple )", $this->plugin_name),
        "select" => __("Dropdown", $this->plugin_name),
        "text" => __("Text", $this->plugin_name),
        "short_text" => __("Short Text", $this->plugin_name),
        "number" => __("Number", $this->plugin_name),
    );

    $options = ($actions->ays_get_setting('options') === false) ? array() : json_decode($actions->ays_get_setting('options'), true);
    $options['question_default_type'] = !isset($options['question_default_type']) ? 'radio' : $options['question_default_type'];
    $question_default_type = isset($options['question_default_type']) ? $options['question_default_type'] : '';

    $options['ays_show_result_report'] = !isset( $options['ays_show_result_report'] ) ? 'on' : $options['ays_show_result_report'];
    $show_result_report = ( isset( $options['ays_show_result_report'] ) && $options['ays_show_result_report'] == 'on' ) ? 'on' : 'off';
    $ays_answer_default_count = isset($options['ays_answer_default_count']) ? $options['ays_answer_default_count'] : '3';
    $right_answer_sound = isset($options['right_answer_sound']) ? $options['right_answer_sound'] : '';
    $wrong_answer_sound = isset($options['wrong_answer_sound']) ? $options['wrong_answer_sound'] : '';

?>
<div class="wrap" style="position:relative;">
    <div class="container-fluid">
        <form method="post" id="ays-quiz-settings-form">
            <input type="hidden" name="ays_quiz_tab" value="<?php echo $ays_quiz_tab; ?>">
            <h1 class="wp-heading-inline">
            <?php
                echo __('General Settings',$this->plugin_name);
            ?>
            </h1>
            <?php                
                if( isset( $_REQUEST['status'] ) ){
                    $actions->quiz_settings_notices($_REQUEST['status']);
                }
            ?>
            <hr/>
            <div class="ays-settings-wrapper">
            <div>
                <div class="nav-tab-wrapper" style="position:sticky; top:35px;">
                    <a href="#tab1" data-tab="tab1" class="nav-tab <?php echo ($ays_quiz_tab == 'tab1') ? 'nav-tab-active' : ''; ?>">
                        <?php echo __("General", $this->plugin_name);?>
                    </a>
                    <a href="#tab2" data-tab="tab2" class="nav-tab <?php echo ($ays_quiz_tab == 'tab2') ? 'nav-tab-active' : ''; ?>">
                        <?php echo __("Integrations", $this->plugin_name);?>
                    </a>
                    <a href="#tab3" data-tab="tab3" class="nav-tab <?php echo ($ays_quiz_tab == 'tab3') ? 'nav-tab-active' : ''; ?>">
                        <?php echo __("Shortcodes", $this->plugin_name);?>
                    </a>
                    <a href="#tab4" data-tab="tab4" class="nav-tab <?php echo ($ays_quiz_tab == 'tab4') ? 'nav-tab-active' : ''; ?>">
                        <?php echo __("Message variables", $this->plugin_name);?>
                    </a>
                </div>
            </div>
            <div class="ays-quiz-tabs-wrapper">
                <div id="tab1" class="ays-quiz-tab-content <?php echo ($ays_quiz_tab == 'tab1') ? 'ays-quiz-tab-content-active' : ''; ?>">
                    <p class="ays-subtitle"><?php echo __('General Settings',$this->plugin_name)?></p>
                    <hr/>
                    <fieldset>
                        <legend>
                            <strong style="font-size:30px;"><i class="ays_fa ays_fa_globe"></i></strong>
                            <h5><?php echo __('Who will have permission to Quiz menu',$this->plugin_name)?></h5>
                        </legend>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_user_roles">
                                    <?php echo __( "Select user role", $this->plugin_name ); ?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Ability to manage Quiz Maker plugin only for selected user roles.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <select name="ays_user_roles[]" id="ays_user_roles" multiple>
                                    <?php
                                        foreach($ays_users_roles as $role => $role_name){
                                            $selected = in_array($role, $user_roles) ? 'selected' : '';
                                            echo "<option ".$selected." value='".$role."'>".$role_name."</option>";
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <blockquote>
                            <?php echo __( "Ability to manage Quiz Maker plugin only for selected user roles.", $this->plugin_name ); ?>
                        </blockquote>
                    </fieldset>
                    <hr>
                    <fieldset>
                        <legend>
                            <strong style="font-size:30px;"><i class="ays_fa ays_fa_question_circle"></i></strong>
                            <h5><?php echo __('Default parameters for Quiz',$this->plugin_name)?></h5>
                        </legend>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_questions_default_type">
                                    <?php echo __( "Questions default type", $this->plugin_name ); ?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('You can choose default question type which will be selected in the Add new question page.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <select id="ays-type" name="ays_question_default_type">
                                    <option></option>
                                    <?php
                                        foreach($question_types as $type => $label):
                                        $selected = $question_default_type == $type ? "selected" : "";
                                    ?>
                                    <option value="<?php echo $type; ?>" <?php echo $selected; ?> ><?php echo $label; ?></option>
                                    <?php
                                        endforeach;
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_answer_default_count">
                                    <?php echo __( "Answer default count", $this->plugin_name ); ?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('You can write the default answer count which will be showing in the Add new question page (this will work only with radio, checkbox, and dropdown types).',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="number" name="ays_answer_default_count" id="ays_answer_default_count" min="2" class="ays-text-input" value="<?php echo $ays_answer_default_count; ?>">
                            </div>
                        </div>
                    </fieldset>
                    <hr>
                    <fieldset>
                        <legend>
                            <strong style="font-size:30px;"><i class="ays_fa ays_fa_music"></i></strong>
                            <h5><?php echo __('Quiz Right/Wrong answers sounds',$this->plugin_name)?></h5>
                        </legend>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_questions_default_type">
                                    <?php echo __( "Sounds for right/wrong answers", $this->plugin_name ); ?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('This option will work with Enable correct answers option.',$this->plugin_name); ?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <label for="ays_questions_default_type">
                                            <?php echo __( "Sounds for right answers", $this->plugin_name ); ?>
                                        </label>
                                        <div class="ays-bg-music-container">
                                            <a class="add-quiz-bg-music" href="javascript:void(0);"><?php echo __("Select sound", $this->plugin_name); ?></a>
                                            <audio controls src="<?php echo $right_answer_sound; ?>"></audio>
                                            <input type="hidden" name="ays_right_answer_sound" class="ays_quiz_bg_music" value="<?php echo $right_answer_sound; ?>">
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <label for="ays_questions_default_type">
                                            <?php echo __( "Sounds for wrong answers", $this->plugin_name ); ?>
                                        </label>
                                        <div class="ays-bg-music-container">
                                            <a class="add-quiz-bg-music" href="javascript:void(0);"><?php echo __("Select sound", $this->plugin_name); ?></a>
                                            <audio controls src="<?php echo $wrong_answer_sound; ?>"></audio>
                                            <input type="hidden" name="ays_wrong_answer_sound" class="ays_quiz_bg_music" value="<?php echo $wrong_answer_sound; ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <hr>
                    <fieldset>
                        <legend>
                            <strong style="font-size:30px;"><i class="ays_fa ays_fa_trash"></i></strong>
                            <h5><?php echo __('Erase Quiz data',$this->plugin_name)?></h5>
                        </legend>
                        <?php if( isset( $_GET['del_stat'] ) ): ?>
                        <blockquote style="border-color:#46b450;background: rgba(70, 180, 80, 0.2);">
                            <?php echo __("Results up to a ".$_GET['mcount']." month ago deleted successfully.", $this-plugin_name); ?>
                        </blockquote>
                        <hr>
                        <?php endif; ?>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_delete_results_by">
                                    <?php echo __( "Delete results older then 'X' the month", $this->plugin_name ); ?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Specify count of months and save changes. Attention! it will remove submissions older than specified months permanently.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="number" name="ays_delete_results_by" id="ays_delete_results_by" class="ays-text-input">
                            </div>
                        </div>
                    </fieldset>
                </div>
                <div id="tab2" class="ays-quiz-tab-content <?php echo ($ays_quiz_tab == 'tab2') ? 'ays-quiz-tab-content-active' : ''; ?>">
                    <p class="ays-subtitle"><?php echo __('Integrations',$this->plugin_name)?></p>
                    <hr/>
                    <fieldset>
                        <legend>
                            <img class="ays_integration_logo" src="<?php echo AYS_QUIZ_ADMIN_URL; ?>/images/integrations/mailchimp_logo.png" alt="">
                            <h5><?php echo __('MailChimp',$this->plugin_name)?></h5>
                        </legend>
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <div class="form-group row" aria-describedby="aaa">
                                    <div class="col-sm-3">
                                        <label for="ays_mailchimp_username">
                                            <?php echo __('MailChimp Username',$this->plugin_name)?>
                                        </label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" 
                                            class="ays-text-input" 
                                            id="ays_mailchimp_username" 
                                            name="ays_mailchimp_username"
                                            value="<?php echo $mailchimp_username; ?>"
                                        />
                                    </div>
                                </div>
                                <hr/>
                                <div class="form-group row" aria-describedby="aaa">
                                    <div class="col-sm-3">
                                        <label for="ays_mailchimp_api_key">
                                            <?php echo __('MailChimp API Key',$this->plugin_name)?>
                                        </label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" 
                                            class="ays-text-input" 
                                            id="ays_mailchimp_api_key" 
                                            name="ays_mailchimp_api_key"
                                            value="<?php echo $mailchimp_api_key; ?>"
                                        />
                                    </div>
                                </div>
                                <blockquote>
                                    <?php echo sprintf( __( "You can get your API key from your ", $this->plugin_name ) . "<a href='%s' target='_blank'> %s.</a>", "https://us20.admin.mailchimp.com/account/api/", "Account Extras menu" ); ?>
                                </blockquote>
                            </div>
                        </div>
                    </fieldset>
                    <hr/>
                    <fieldset>
                        <legend>
                            <img class="ays_integration_logo" src="<?php echo AYS_QUIZ_ADMIN_URL; ?>/images/integrations/paypal_logo.png" alt="">
                            <h5><?php echo __('PayPal',$this->plugin_name)?></h5>
                        </legend>
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <div class="form-group row" aria-describedby="aaa">
                                    <div class="col-sm-3">
                                        <label for="ays_paypal_client_id">
                                            <?php echo __('Paypal Client ID',$this->plugin_name)?>
                                        </label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" 
                                            class="ays-text-input" 
                                            id="ays_paypal_client_id" 
                                            name="ays_paypal_client_id"
                                            value="<?php echo $paypal_client_id; ?>"
                                        />
                                    </div>
                                </div>
                                <hr/>
                                <div class="form-group row" aria-describedby="aaa">
                                    <div class="col-sm-3">
                                        <label>
                                            <?php echo __('Payment terms',$this->plugin_name)?>
                                        </label>
                                    </div>
                                    <div class="col-sm-9">
                                        <label class="ays_quiz_loader" style="display:inline-block;">
                                            <input type="radio" name="ays_paypal_payment_terms" value="lifetime" <?php echo $paypal_payment_terms == "lifetime" ? "checked" : ""; ?>/>
                                            <span><?php echo __('Lifetime payment',$this->plugin_name)?></span>
                                        </label>
                                        <label class="ays_quiz_loader" style="display:inline-block;">
                                            <input type="radio" name="ays_paypal_payment_terms" value="onetime" <?php echo $paypal_payment_terms == "onetime" ? "checked" : ""; ?>/>
                                            <span><?php echo __('Onetime payment',$this->plugin_name)?></span>
                                        </label>
                                    </div>
                                </div>
                                <blockquote>
                                    <?php echo sprintf( __( "You can get your Client ID from ", $this->plugin_name ) . "<a href='%s' target='_blank'> %s.</a>", "https://developer.paypal.com/developer/applications", "Developer Paypal" ); ?>
                                </blockquote>
                            </div>
                        </div>
                    </fieldset>
                    <hr/>
                    <fieldset>
                        <legend>
                            <img class="ays_integration_logo" src="<?php echo AYS_QUIZ_ADMIN_URL; ?>/images/integrations/campaignmonitor_logo.png" alt="">
                            <h5><?php echo __('Campaign Monitor',$this->plugin_name)?></h5>
                        </legend>
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <div class="form-group row" aria-describedby="aaa">
                                    <div class="col-sm-3">
                                        <label for="ays_monitor_client">
                                            Campaign Monitor <?= __('Client ID', $this->plugin_name) ?>
                                        </label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text"
                                               class="ays-text-input"
                                               id="ays_monitor_client"
                                               name="ays_monitor_client"
                                               value="<?= $monitor_client; ?>"
                                        >
                                    </div>
                                </div>
                                <hr/>
                                <div class="form-group row" aria-describedby="aaa">
                                    <div class="col-sm-3">
                                        <label for="ays_monitor_api_key">
                                            Campaign Monitor <?= __('API Key', $this->plugin_name) ?>
                                        </label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text"
                                               class="ays-text-input"
                                               id="ays_monitor_api_key"
                                               name="ays_monitor_api_key"
                                               value="<?= $monitor_api_key; ?>"
                                        >
                                    </div>
                                </div>
                                <blockquote>
                                    <?= __("You can get your API key and Client ID from your Account Settings page.", $this->plugin_name); ?>
                                </blockquote>
                            </div>
                        </div>
                    </fieldset>
                    <hr/>
                    <fieldset>
                        <legend>
                            <img class="ays_integration_logo" src="<?php echo AYS_QUIZ_ADMIN_URL; ?>/images/integrations/zapier_logo.png" alt="">
                            <h5><?php echo __('Zapier',$this->plugin_name)?></h5>
                        </legend>
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <div class="form-group row" aria-describedby="aaa">
                                    <div class="col-sm-3">
                                        <label for="ays_zapier_hook">
                                            <?= __('Zapier Webhook URL', $this->plugin_name) ?>
                                        </label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text"
                                               class="ays-text-input"
                                               id="ays_zapier_hook"
                                               name="ays_zapier_hook"
                                               value="<?= $zapier_hook; ?>"
                                        >
                                    </div>
                                </div>
                                <blockquote>
                                    <?php echo sprintf(__("If you don’t have any ZAP created, go <a href='%s' target='_blank'> here...</a>.", $this->plugin_name), "https://zapier.com/app/editor/"); ?>
                                </blockquote>
                                <blockquote>
                                    <?= __("We will send you all data from quiz information form with the “AysQuiz” key by POST method.", $this->plugin_name); ?>
                                </blockquote>
                            </div>
                        </div>
                    </fieldset>
                    <hr/>
                    <fieldset>
                        <legend>
                            <img class="ays_integration_logo" src="<?php echo AYS_QUIZ_ADMIN_URL; ?>/images/integrations/activecampaign_logo.png" alt="">
                            <h5><?php echo __('ActiveCampaign',$this->plugin_name)?></h5>
                        </legend>
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <div class="form-group row" aria-describedby="aaa">
                                    <div class="col-sm-3">
                                        <label for="ays_active_camp_url">
                                            <?= __('API Access URL', $this->plugin_name) ?>
                                        </label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text"
                                               class="ays-text-input"
                                               id="ays_active_camp_url"
                                               name="ays_active_camp_url"
                                               value="<?= $active_camp_url; ?>"
                                        >
                                    </div>
                                </div>
                                <hr/>
                                <div class="form-group row" aria-describedby="aaa">
                                    <div class="col-sm-3">
                                        <label for="ays_active_camp_api_key">
                                            <?= __('API Access Key', $this->plugin_name) ?>
                                        </label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text"
                                               class="ays-text-input"
                                               id="ays_active_camp_api_key"
                                               name="ays_active_camp_api_key"
                                               value="<?= $active_camp_api_key; ?>"
                                        >
                                    </div>
                                </div>
                                <blockquote>
                                    <?= __("Your API URL and Key can be found in your account on the My Settings page under the “Developer” tab.", $this->plugin_name); ?>
                                </blockquote>
                            </div>
                        </div>
                    </fieldset>
                    <hr/>
                    <fieldset>
                        <legend>
                            <img class="ays_integration_logo" src="<?php echo AYS_QUIZ_ADMIN_URL; ?>/images/integrations/slack_logo.png" alt="">
                            <h5><?php echo __('Slack',$this->plugin_name)?></h5>
                        </legend>
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <?php if (!$slack_oauth): ?>
                                    <div class="form-group row" aria-describedby="aaa">
                                        <div class="col-sm-3">
                                            <button id="slackInstructionsPopOver" type="button" class="btn btn-info"
                                                    data-toggle="popover"
                                                    title="<?= __("Slack Integration Setup Instructions", $this->plugin_name) ?>"><?= __("Instructions", $this->plugin_name) ?></button>
                                            <div class="d-none" id="slackInstructions">
                                                <p><?= sprintf(__("1. You will need to " . "<a href='%s' target='_blank'>%s</a>" . " new Slack App.", $this->plugin_name), "https://api.slack.com/apps?new_app=1", "create"); ?></p>
                                                <p><?= __("2. Complete Project creation for get App credentials.", $this->plugin_name) ?></p>
                                                <p><?= __("3. Next, go to the Features > OAuth & Permissions > Redirect URLs section.", $this->plugin_name) ?></p>
                                                <p><?= __("4. Click Add a new Redirect URL.", $this->plugin_name) ?></p>
                                                <p><?= __("5. In the shown input field, put this value below", $this->plugin_name) ?></p>
                                                <p>
                                                    <code><?= ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443 ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "&oauth=slack" ?></code>
                                                </p>
                                                <p><?= __("6. Then click the Add button.", $this->plugin_name) ?></p>
                                                <p><?= __("7. Then click the Save URLs button.", $this->plugin_name) ?></p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <div class="form-group row" aria-describedby="aaa">
                                    <div class="col-sm-3">
                                        <label for="ays_slack_client">
                                            <?= __('App Client ID', $this->plugin_name) ?>
                                        </label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text"
                                               class="ays-text-input"
                                               id="ays_slack_client"
                                               name="ays_slack_client"
                                               value="<?= $slack_client; ?>"
                                        >
                                    </div>
                                </div>
                                <hr/>
                                <div class="form-group row" aria-describedby="aaa">
                                    <div class="col-sm-3">
                                        <label for="ays_slack_oauth">
                                            <?= __('Slack Authorization', $this->plugin_name) ?>
                                        </label>
                                    </div>
                                    <div class="col-sm-9">
                                        <?php if ($slack_oauth): ?>
                                            <span class="btn btn-success pointer-events-none">
                                                <?= __("Authorized", $this->plugin_name) ?></span>
                                        <?php else: ?>
                                            <button type="button" id="slackOAuth2"
                                                    class="btn btn-outline-secondary disabled">
                                                <?= __("Authorize", $this->plugin_name) ?>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <hr/>
                                <div class="form-group row" aria-describedby="aaa">
                                    <div class="col-sm-3">
                                        <label for="ays_slack_secret">
                                            <?= __('App Client Secret', $this->plugin_name) ?>
                                        </label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text"
                                               class="ays-text-input"
                                               id="ays_slack_secret"
                                               name="ays_slack_secret"
                                               value="<?= $slack_secret; ?>" <?= $slack_oauth ?: "readonly" ?>
                                        >
                                    </div>
                                </div>
                                <hr/>
                                <div class="form-group row" aria-describedby="aaa">
                                    <div class="col-sm-3">
                                        <label for="ays_slack_oauth">
                                            <?= __('App Access Token', $this->plugin_name) ?>
                                        </label>
                                    </div>
                                    <div class="col-sm-9">
                                        <?php if ($slack_oauth): ?>
                                            <button type="button"
                                                    data-code="<?= !empty($slack_temp_code) ? $slack_temp_code : "" ?>"
                                                    id="slackOAuthGetToken"
                                                    data-success="<?= __("Access granted", $this->plugin_name) ?>"
                                                    class="btn btn-outline-secondary disabled"><?= __("Get it", $this->plugin_name) ?></button>
                                        <?php else: ?>
                                            <button type="button"
                                                    class="btn btn-outline-secondary disabled"><?= __("Need Authorization", $this->plugin_name) ?>
                                            </button>
                                        <?php endif; ?>
                                        <input type="hidden" id="ays_slack_token" name="ays_slack_token" value="<?= $slack_token; ?>">
                                    </div>
                                </div>
                                <blockquote>
                                    <?= __("You can get your App Client ID and Client Secret from your App’s Basic Information page.", $this->plugin_name); ?>
                                </blockquote>
                            </div>
                        </div>
                    </fieldset>
                </div>
                <div id="tab3" class="ays-quiz-tab-content <?php echo ($ays_quiz_tab == 'tab3') ? 'ays-quiz-tab-content-active' : ''; ?>">
                    <p class="ays-subtitle"><?php echo __('Shortcodes',$this->plugin_name)?></p>
                    <hr>
                    <fieldset>
                        <legend>
                            <strong style="font-size:30px;">[ ]</strong>
                            <h5><?php echo __('Individual Leaderboard Settings',$this->plugin_name)?></h5>
                        </legend>
                        <blockquote>
                            <?php echo __( "It is designed for a particular quiz’s results.", $this->plugin_name ); ?>
                        </blockquote>
                        <br>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_invidLead">
                                    <?php echo __( "Individual Leaderboard shortcode", $this->plugin_name ); ?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('You can copy the shortcode and paste it to any post/page to see the list of the top user’s who passed this quiz.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="text" id="ays_invidLead" class="ays-text-input" onclick="this.setSelectionRange(0, this.value.length)" readonly="" value='[ays_quiz_leaderboard id="Your Quiz ID"]'>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_leadboard_count">
                                    <?php echo __('Users count',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('How many users’ results will be shown in the leaderboard.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="number"
                                    class="ays-text-input"                 
                                    id="ays_leadboard_count" 
                                    name="ays_leadboard_count"
                                    value="<?php echo $ind_leadboard_count; ?>"
                                />
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_leadboard_width">
                                    <?php echo __('Width',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('The width of the Leaderboard box. For 100% leave it blank.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="number"
                                    class="ays-text-input"                 
                                    id="ays_leadboard_width" 
                                    name="ays_leadboard_width"
                                    value="<?php echo $ind_leadboard_width; ?>"
                                />
                                <span style="display:block;" class="ays_quiz_small_hint_text"><?php echo __("For 100% leave blank", $this->plugin_name);?></span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label>
                                    <?php echo __('Users group by',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Select the way for grouping the results. If you want to make Leaderboard for logged in users, then choose ID. It will collect results by WP user ID. If you want to make Leaderboard for guests, then you need to choose Email and enable Information Form and Email, Name options from quiz settings. It will group results by emails and display guests Names.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <label class="ays_quiz_loader">
                                    <input type="radio" name="ays_leadboard_orderby" value="id" <?php echo $ind_leadboard_orderby == "id" ? "checked" : ""; ?> />
                                    <span><?php echo __( "ID", $this->plugin_name); ?></span>
                                </label>
                                <label class="ays_quiz_loader">
                                    <input type="radio" name="ays_leadboard_orderby" value="email" <?php echo $ind_leadboard_orderby == "email" ? "checked" : ""; ?> />
                                    <span><?php echo __( "Email", $this->plugin_name); ?></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label>
                                    <?php echo __('Show users result',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Show the users’ Average or Maximum results in the leaderboard.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <label class="ays_quiz_loader">
                                    <input type="radio" name="ays_leadboard_sort" value="avg" <?php echo $ind_leadboard_sort == "avg" ? "checked" : ""; ?> />
                                    <span><?php echo __( "AVG", $this->plugin_name); ?></span>
                                </label>
                                <label class="ays_quiz_loader">
                                    <input type="radio" name="ays_leadboard_sort" value="max" <?php echo $ind_leadboard_sort == "max" ? "checked" : ""; ?> />
                                    <span><?php echo __( "MAX", $this->plugin_name); ?></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_leadboard_color">
                                    <?php echo __('Color',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Top color of the leaderboard',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="text" id="ays_leadboard_color" name="ays_leadboard_color" data-alpha="true" value="<?php echo $ind_leadboard_color; ?>" />
                            </div>
                        </div>
                        <?php if($empry_dur_count > 0): ?>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label>
                                    <?php echo __('Update duration field for old results',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('This button needs to work only once. If you see 0 in the Duration column for some results, please click once to this button and it will regenerate duration for old results. It may happen if you update our plugin from the old version to the latest.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <a class="button" href="?page=<?php echo $_REQUEST['page']; ?>&action=update_duration&ays_quiz_tab=tab3"><?php echo __('Update duration old data', $this->plugin_name); ?></a>
                            </div>
                        </div>
                        <?php endif; ?>
                    </fieldset>
                    <hr>
                    <fieldset>
                        <legend>
                            <strong style="font-size:30px;">[ ]</strong>
                            <h5 class="ays-subtitle"><?php echo __('Global Leaderboard Settings',$this->plugin_name)?></h5>
                        </legend>
                        <blockquote>
                            <?php echo __( "It is designed for all quizzes results.", $this->plugin_name ); ?>
                        </blockquote>
                        <hr>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_globLead">
                                    <?php echo __( "Global Leaderboard shortcode", $this->plugin_name ); ?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('You can copy the shortcode and paste it to any post/page to see the list of the top user’s who passed any quiz.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="text" id="ays_globLead" class="ays-text-input" onclick="this.setSelectionRange(0, this.value.length)" readonly="" value='[ays_quiz_gleaderboard]'>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_gleadboard_count">
                                    <?php echo __('Users count',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('How many users’ results will be shown in the leaderboard.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="number"
                                    class="ays-text-input"                 
                                    id="ays_gleadboard_count" 
                                    name="ays_gleadboard_count"
                                    value="<?php echo $glob_leadboard_count; ?>"
                                />
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_gleadboard_width">
                                    <?php echo __('Width',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('The width of the Leaderboard box. It accepts only numeric values. For 100% leave it blank.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="number"
                                    class="ays-text-input"                 
                                    id="ays_gleadboard_width" 
                                    name="ays_gleadboard_width"
                                    value="<?php echo $glob_leadboard_width; ?>"
                                />
                                <span style="display:block;" class="ays_quiz_small_hint_text"><?php echo __("For 100% leave blank", $this->plugin_name);?></span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label>
                                    <?php echo __('Users group by',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Select the way for grouping the results. If you want to make Leaderboard for logged in users, then choose ID. It will collect results by WP user ID. If you want to make Leaderboard for guests, then you need to choose Email and enable Information Form and Email, Name options from quiz settings. It will group results by emails and display guests Names.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <label class="ays_quiz_loader">
                                    <input type="radio" name="ays_gleadboard_orderby" value="id" <?php echo $glob_leadboard_orderby == "id" ? "checked" : ""; ?> />
                                    <span><?php echo __( "ID", $this->plugin_name); ?></span>
                                </label>
                                <label class="ays_quiz_loader">
                                    <input type="radio" name="ays_gleadboard_orderby" value="email" <?php echo $glob_leadboard_orderby == "email" ? "checked" : ""; ?> />
                                    <span><?php echo __( "Email", $this->plugin_name); ?></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label>
                                    <?php echo __('Show users result',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Show the users’ Average or Maximum results in the leaderboard.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <label class="ays_quiz_loader">
                                    <input type="radio" name="ays_gleadboard_sort" value="avg" <?php echo $glob_leadboard_sort == "avg" ? "checked" : ""; ?> />
                                    <span><?php echo __( "AVG", $this->plugin_name); ?></span>
                                </label>
                                <label class="ays_quiz_loader">
                                    <input type="radio" name="ays_gleadboard_sort" value="max" <?php echo $glob_leadboard_sort == "max" ? "checked" : ""; ?> />
                                    <span><?php echo __( "MAX", $this->plugin_name); ?></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_gleadboard_color">
                                    <?php echo __('Color',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Top color of the leaderboard',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="text" id="ays_gleadboard_color" name="ays_gleadboard_color" data-alpha="true" value="<?php echo $glob_leadboard_color; ?>" />
                            </div>
                        </div>
                        <?php if($empry_dur_count > 0): ?>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label>
                                    <?php echo __('Update duration field for old results',$this->plugin_name)?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('This button needs to work only once. If you see 0 in the Duration column for some results, please click once to this button and it will regenerate duration for old results. It may happen if you update our plugin from the old version to the latest.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <a class="button" href="?page=<?php echo $_REQUEST['page']; ?>&action=update_duration&ays_quiz_tab=tab3"><?php echo __('Update duration old data', $this->plugin_name); ?></a>
                            </div>
                        </div>
                        <?php endif; ?>
                    </fieldset>
                    <hr/>
                    <fieldset>
                        <legend>
                            <strong style="font-size:30px;">[ ]</strong>
                            <h5><?php echo __('User Page Settings',$this->plugin_name)?></h5>
                        </legend>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_user_page">
                                    <?php echo __( "User Page shortcode", $this->plugin_name ); ?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('You can copy the shortcode and insert it to any post to show the current user’s results history.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="text" id="ays_user_page" class="ays-text-input" onclick="this.setSelectionRange(0, this.value.length)" readonly="" value='[ays_user_page]'>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="ays_show_result_report">
                                    <?php echo __( "Show result report in user page", $this->plugin_name ); ?>
                                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Disabling this option the details about the results won’t be shown.',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_info_circle"></i>
                                    </a>
                                </label>
                            </div>
                            <div class="col-sm-8">
                                <input type="checkbox" id="ays_show_result_report" class="ays-checkbox-input" name="ays_show_result_report" value="on" <?php echo ($show_result_report == 'on') ? 'checked' : ''; ?>/>
                            </div>
                        </div>
                    </fieldset>
                </div>
                <div id="tab4" class="ays-quiz-tab-content <?php echo ($ays_quiz_tab == 'tab4') ? 'ays-quiz-tab-content-active' : ''; ?>">
                    <p class="ays-subtitle">
                        <?php echo __('Message variables',$this->plugin_name)?>
                        <a class="ays_help" data-toggle="tooltip" data-html="true" title="<p style='margin-bottom:3px;'><?php echo __( 'You can copy these variables and paste them in the following options from the quiz settings', $this->plugin_name ); ?>:</p>
                            <p style='padding-left:10px;margin:0;'>- <?php echo __( 'Text for showing after quiz completion', $this->plugin_name ); ?></p>
                            <p style='padding-left:10px;margin:0;'>- <?php echo __( 'Mail Message', $this->plugin_name ); ?></p>
                            <p style='padding-left:10px;margin:0;'>- <?php echo __( 'Certificate title', $this->plugin_name ); ?></p>
                            <p style='padding-left:10px;margin:0;'>- <?php echo __( 'Certificate body', $this->plugin_name ); ?></p>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </p>
                    <blockquote>
                        <p><?php echo __( "You can copy these variables and paste them in the following options from the quiz settings", $this->plugin_name ); ?>:</p>
                        <p style="text-indent:10px;margin:0;">- <?php echo __( "Text for showing after quiz completion", $this->plugin_name ); ?></p>
                        <p style="text-indent:10px;margin:0;">- <?php echo __( "Mail Message", $this->plugin_name ); ?></p>
                        <p style="text-indent:10px;margin:0;">- <?php echo __( "Certificate title", $this->plugin_name ); ?></p>
                        <p style="text-indent:10px;margin:0;">- <?php echo __( "Certificate body", $this->plugin_name ); ?></p>
                    </blockquote>
                    <hr>
                    <div class="form-group row">
                        <div class="col-sm-12">
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%user_name%%"/>
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo __( "The name the user entered into information form", $this->plugin_name); ?>
                                </span>
                            </p>
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%user_email%%" />
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo __( "The E-mail the user entered into information form", $this->plugin_name); ?>
                                </span>
                            </p>
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%quiz_name%%" />
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo __( "The title of the quiz", $this->plugin_name); ?>
                                </span>
                            </p>
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%score%%" />
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo __( "The score of quiz which got the user", $this->plugin_name); ?>
                                </span>
                            </p>
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%user_points%%" />
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo __( "The points of quiz which got the user", $this->plugin_name); ?>
                                </span>
                            </p>
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%user_corrects_count%%" />
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo __( "The number of correct answers of the user", $this->plugin_name); ?>
                                </span>
                            </p>
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%questions_count%%" />
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo __( "The number of questions that the user must pass.", $this->plugin_name); ?>
                                </span>
                            </p>
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%max_points%%" />
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo __( "Maximum points which can get the user", $this->plugin_name); ?>
                                </span>
                            </p>
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%current_date%%" />
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo __( "The date of the passing quiz", $this->plugin_name); ?>
                                </span>
                            </p>
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%quiz_logo%%" />
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo __( "The quiz image which used for quiz start page", $this->plugin_name); ?>
                                </span>
                            </p>
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%interval_message%%" />
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo __( "The message which must display on the result page depending from score", $this->plugin_name); ?>
                                </span>
                            </p>
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%avg_score%%" />
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo __( "The average score of the quiz of all time", $this->plugin_name); ?>
                                </span>
                            </p>
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%avg_rate%%" />
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo __( "The average rate of the quiz of all time", $this->plugin_name); ?>
                                </span>
                            </p>
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%user_pass_time%%" />
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo __( "The time which spent that the user passed the quiz", $this->plugin_name); ?>
                                </span>
                            </p>
                            <p class="vmessage">
                                <strong>
                                    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%quiz_time%%" />
                                </strong>
                                <span> - </span>
                                <span style="font-size:18px;">
                                    <?php echo __( "The time which must spend the user to the quiz", $this->plugin_name); ?>
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            </div>
            <hr/>
            <div style="position:sticky;padding:15px 0px;bottom:0;">
            <?php
                wp_nonce_field('settings_action', 'settings_action');
                $other_attributes = array();
                submit_button(__('Save changes', $this->plugin_name), 'primary', 'ays_submit', true, $other_attributes);
            ?>
            </div>
        </form>
    </div>
</div>
