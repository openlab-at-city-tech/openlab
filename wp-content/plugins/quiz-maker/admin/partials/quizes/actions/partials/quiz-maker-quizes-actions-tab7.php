<div id="tab7" class="ays-quiz-tab-content <?php echo ($ays_quiz_tab == 'tab7') ? 'ays-quiz-tab-content-active' : ''; ?>">
    <div class="ays-quiz-display-flex-justify-between">
        <p class="ays-subtitle"><?php echo __('E-mail and Certificate settings',$this->plugin_name)?></p>
        <div class="ays-quiz-heading-box ays-quiz-unset-float ays-quiz-unset-margin">
            <div class="ays-quiz-wordpress-user-manual-box ays-quiz-wordpress-text-align">
                <a href="https://www.youtube.com/watch?v=LoQw1wxkj6k" target="_blank">
                    <?php echo __("How to create certifiication test - video", $this->plugin_name); ?>
                </a>
            </div>
        </div>
    </div>
    <hr/>

    <div class="form-group row ays_toggle_parent">
        <div class="col-sm-3">
            <label for="ays_enable_mail_user">
                <?php echo __('Send email to user',$this->plugin_name)?>
                <a  class="ays_help" data-toggle="tooltip" title="<?php echo __('Send mail to user after quiz completion.',$this->plugin_name)?>">
                    <i class="ays_fa ays_fa_info_circle"></i>
                </a>
            </label>
        </div>
        <div class="col-sm-1">
            <input type="checkbox" class="ays-enable-timerl ays_toggle_checkbox" id="ays_enable_mail_user"
                   name="ays_enable_user_mail"
                   value="on" <?php echo (isset($options['user_mail']) && $options['user_mail'] == 'on') ? 'checked' : ''; ?>/>
        </div>
        <div class="col-sm-8 ays_toggle_target ays_divider_left <?php echo (isset($options['user_mail']) && $options['user_mail'] == 'on') ? '' : 'display_none'; ?>">
            <div class="form-group row">
                <div class="col-sm-3">
                    <label for="ays_enable_send_mail_to_user_by_pass_score">
                        <?php echo __('Pass score', $this->plugin_name); ?>
                        <a  class="ays_help" data-toggle="tooltip" title="<?php echo __('If the option is enabled, then the user will receive the email only if he/she has passed the minimum score required. It will take the value of the general pass score of the quiz. Please specify it in the Result Settings tab.',$this->plugin_name); ?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-9">
                    <input type="checkbox" class="ays-enable-timerl" id="ays_enable_send_mail_to_user_by_pass_score" name="ays_enable_send_mail_to_user_by_pass_score" value="on" <?php echo ($enable_send_mail_to_user_by_pass_score) ? 'checked' : ''; ?>/>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-12">
                    <blockquote><?php echo __( 'Tick the checkbox, and the user will receive the email only if he/she has passed the minimum score required.', $this->plugin_name ); ?></blockquote>
                </div>
            </div>
            <hr>
            <div class="form-group row ays-quiz-result-message-vars-parent">
                <div class="col-sm-3">
                    <label for="ays_mail_message">
                        <?php echo __('Email message',$this->plugin_name)?>
                        <a  class="ays_help" data-toggle="tooltip" title="<?php echo __('Provide the message text for sending to the user by email. You can use Variables from General Settings page to insert user’s data. (name, score, date etc.)',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                    <p class="ays_quiz_small_hint_text_for_message_variables">
                        <span><?php echo __( "To see all Message Variables " , $this->plugin_name ); ?></span>
                        <a href="?page=quiz-maker-settings&ays_quiz_tab=tab4" target="_blank"><?php echo __( "click here" , $this->plugin_name ); ?></a>
                    </p>
                </div>
                <div class="col-sm-9">
                    <?php
                    echo $quiz_message_vars_html;
                    $content = wpautop(stripslashes((isset($options['mail_message'])) ? $options['mail_message'] : ''));
                    $editor_id = 'ays_mail_message';
                    $settings = array('editor_height' => $quiz_wp_editor_height, 'textarea_name' => 'ays_mail_message', 'editor_class' => 'ays-textarea', 'media_elements' => false);
                    wp_editor($content, $editor_id, $settings);
                    ?>
                </div>
            </div>
            <hr>
            <!-- AV -->
            <div class="form-group row">
                <div class="col-sm-3">
                    <label for="ays_send_results_user">
                        <?php echo __('Send results to user',$this->plugin_name)?>
                        <a  class="ays_help" data-toggle="tooltip" title="<?php echo __('Send results report to user after quiz completion.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-9">
                    <input type="checkbox" class="ays-enable-timerl" id="ays_send_results_user"
                   name="ays_send_results_user"
                   value="on" <?php echo $send_results_user ?>/>
                </div>
            </div>
            <hr/>
            <div class="form-group row">
                <div class="col-sm-3">
                    <label for="ays_send_interval_msg">
                        <?php echo __('Send interval message to user',$this->plugin_name)?>
                        <a  class="ays_help" data-toggle="tooltip" title="<?php echo __('Send interval message to user after quiz completion. Your specified interval messages will be sent to the user. You can provide them at the bottom of the Results Settings page.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-9">
                    <input type="checkbox" class="ays-enable-timerl" id="ays_send_interval_msg"
                           name="ays_send_interval_msg" value="on" <?php echo $send_interval_msg; ?>/>
                </div>
            </div>
            <hr/>
            <div class="form-group row">
                <div class="col-sm-3">
                    <label for="ays_test_email">
                        <?php echo __('Send email for testing',$this->plugin_name)?>
                        <a  class="ays_help" data-toggle="tooltip" title="<?php echo __('Send test Email and see how it will be displayed.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-9">
                    <div class="ays_send_test">
                        <input type="text" id="ays_test_email" name="ays_test_email" class="ays-text-input"
                               value="<?php echo $ays_super_admin_email; ?>">
                        <input type="hidden" name="ays_quiz_id_for_test" value="<?php echo $id; ?>">
                        <a href="javascript:void(0)" class="ays_test_mail_btn button button-primary"><?php echo __( "Send", $this->plugin_name ); ?></a>
                        <span id="ays_test_delivered_message" data-src="<?php echo AYS_QUIZ_ADMIN_URL . "/images/loaders/loading.gif" ?>" style="display: none;"></span>
                    </div>
                </div>
            </div>

        </div>
    </div> <!-- Send Mail To User -->
    <hr/>
    <div class="form-group row ays_toggle_parent">
        <div class="col-sm-3">
            <label for="ays_enable_certificate" <?php echo $ays_enable_certificate == false && $ays_enable_certificate_without_send == true ? 'style="color:#bbb;"' : ''; ?>>
                <?php echo __('Send certificate to user',$this->plugin_name)?>
                <a  class="ays_help" data-html="true" data-toggle="tooltip" title="<?php echo __("Send Certificate PDF file to user after quiz completion. Configure the PDF file’s content with the following options.", $this->plugin_name ); ?>">
                    <i class="ays_fa ays_fa_info_circle"></i>
                </a>
            </label>
            <hr>
            <label for="ays_enable_certificate_without_send" <?php echo $ays_enable_certificate == true && $ays_enable_certificate_without_send == false ? 'style="color:#bbb;"' : ''; ?>>
                <?php echo __('Generate certificate without sending to user',$this->plugin_name)?>
                <a  class="ays_help" data-html="true" data-toggle="tooltip" title="<?php echo __("Tick the option if you want to create the certificate without sending it to the user after the submission immediately. It will be stored on the Results page. You can make use of it whenever you need it. You can either use this option or the Send certificate with user.", $this->plugin_name ); ?>">
                    <i class="ays_fa ays_fa_info_circle"></i>
                </a>
            </label>
            <hr>
            <div class="ays_generate_cert_preview_wrap">
                <div class="ays_generate_cert_preview_button_wrap">
                    <button class="ays_generate_cert_preview button-primary" type="button"><?php echo __( 'Generate Certificate preview', $this->plugin_name ); ?></button>
                    <a class="ays_help" data-html="true" data-toggle="tooltip" title="<?php echo __("This is a just preview of the certificate and some message variables will not work on preview mode. Please be understanding.", $this->plugin_name ); ?>">
                        <i class="ays_fa ays_fa_info_circle"></i>
                    </a>
                </div>
                <div class="ays_generate_cert_preview_open"></div>
                <p class="ays_quiz_small_hint_text_for_message_variables" style="margin: 0;">
                    <span><?php echo __( "Download PDF API addon " , $this->plugin_name ); ?></span>
                    <a href="https://quiz-plugin.com/quiz-maker-pdfapi-addon.zip" download target="_blank"><?php echo __( "click here" , $this->plugin_name ); ?></a>
                    <!-- <a class="ays_help" data-html="true" data-toggle="tooltip" title="<?php echo __(".", $this->plugin_name ); ?>">
                        <i class="ays_fa ays_fa_info_circle"></i>
                    </a> -->
                </p>
                <div class="ays_quiz_small_hint_text_for_recommended"><?php echo __( "Recommended" , $this->plugin_name ); ?></div>
            </div>
        </div>
        <div class="col-sm-1">
            <div style="display:inline-block;margin-bottom:.5rem;">
                <input type="checkbox" class="ays-enable-timerl ays_toggle_checkbox" id="ays_enable_certificate"
                       name="ays_enable_certificate"
                       value="on" <?php echo $ays_enable_certificate ? 'checked' : ''; ?> <?php echo $ays_enable_certificate == false && $ays_enable_certificate_without_send == true ? 'disabled' : ''; ?>/>
            </div>
            <hr>
            <div style="display:inline-block;margin-bottom:.5rem;">
                <input type="checkbox" class="ays-enable-timerl ays_toggle_checkbox" id="ays_enable_certificate_without_send"
                       name="ays_enable_certificate_without_send"
                       value="on" <?php echo $ays_enable_certificate_without_send ? 'checked' : ''; ?> <?php echo $ays_enable_certificate == true && $ays_enable_certificate_without_send == false ? 'disabled' : ''; ?>/>
            </div>
        </div>
        <div class="col-sm-8 ays_divider_left ays_toggle_target <?php echo $ays_enable_certificate_without_send || $ays_enable_certificate ? "" : "display_none" ?>">
            <div class="form-group row">
                <div class="col-sm-12">
                    <label class="ays_quiz_loader">
                        <input type="radio" class="ays-enable-timer1" name="ays_quiz_certificate_pass_score_type" value="percentage" <?php echo ($quiz_certificate_pass_score_type == 'percentage') ? 'checked' : '' ?>/>
                        <span><?php echo __( "Percentage", $this->plugin_name ); ?></span>
                    </label>
                    <label class="ays_quiz_loader">
                        <input type="radio" class="ays-enable-timer1" name="ays_quiz_certificate_pass_score_type" value="point" <?php echo ($quiz_certificate_pass_score_type == 'point') ? 'checked' : ''; ?>/>
                        <span><?php echo __( "Points", $this->plugin_name ); ?></span>
                    </label>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-3" style="padding-right: 10px;">
                    <label for="ays_certificate_pass">
                        <?php echo __('Certificate pass score',$this->plugin_name)?>
                        <a  class="ays_help" data-toggle="tooltip" title="<?php echo __('Minimum score to receive a certificate (by percentage)',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-9">
                    <input type="number" id="ays_certificate_pass" name="ays_certificate_pass" class="ays-text-input"
                           value="<?php echo (isset($options['certificate_pass'])) ? $options['certificate_pass'] : 0 ?>">
                </div>
            </div>
            <hr>
            <div class="form-group row ays-quiz-result-message-vars-parent">
                <div class="col-sm-3">
                    <label for="ays_certificate_title">
                        <?php echo __('Certificate title',$this->plugin_name)?>
                        <a  class="ays_help" data-toggle="tooltip" title="<?php echo __('Title of certificate',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                    <p class="ays_quiz_small_hint_text_for_message_variables">
                        <span><?php echo __( "To see all Message Variables " , $this->plugin_name ); ?></span>
                        <a href="?page=quiz-maker-settings&ays_quiz_tab=tab4" target="_blank"><?php echo __( "click here" , $this->plugin_name ); ?></a>
                    </p>
                </div>
                <div class="col-sm-9">
                    <?php
                    echo $quiz_message_vars_html;
                    $content = $certificate_title;
                    $editor_id = 'ays_certificate_title';
                    $settings = array('editor_height' => $quiz_wp_editor_height, 'textarea_name' => 'ays_certificate_title', 'editor_class' => 'ays-textarea', 'media_elements' => false);
                    wp_editor($content, $editor_id, $settings);
                    ?>
                </div>
            </div>
            <hr>
            <div class="form-group row ays-quiz-result-message-vars-parent">
                <div class="col-sm-3">
                    <label for="ays_certificate_body">
                        <?php echo __('Certificate body',$this->plugin_name)?>
                        <a  class="ays_help" data-toggle="tooltip" title="<?php echo __('Specify the content of the certificate PDF file. You can copy Variables from General Settings and insert them here.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                    <p class="ays_quiz_small_hint_text_for_message_variables">
                        <span><?php echo __( "To see all Message Variables " , $this->plugin_name ); ?></span>
                        <a href="?page=quiz-maker-settings&ays_quiz_tab=tab4" target="_blank"><?php echo __( "click here" , $this->plugin_name ); ?></a>
                    </p>
                </div>
                <div class="col-sm-9">
                    <?php
                    echo $quiz_message_vars_html;
                    $content = $certificate_body;
                    $editor_id = 'ays_certificate_body';
                    $settings = array('editor_height' => $quiz_wp_editor_height, 'textarea_name' => 'ays_certificate_body', 'editor_class' => 'ays-textarea', 'media_elements' => false);
                    wp_editor($content, $editor_id, $settings);
                    ?>
                </div>
            </div>
            <hr>
            <div class="form-group row">
                <div class="col-sm-5">
                    <label for="">
                        <?php echo __('Certificate orientation', $this->plugin_name)?>
                        <a  class="ays_help" data-toggle="tooltip" title="<?php echo __('Specify the orientation of the certificate PDF file.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-7">
                    <div class="form-check form-check-inline checkbox_ays">
                        <input type="radio" id="ays_cert_orientation_l" name="ays_certificate_orientation"
                                value="l" <?php echo $certificate_orientation == 'l' ? 'checked' : ''; ?>/>
                        <label class="form-check-label" for="ays_cert_orientation_l"><?php echo __('Landscape',$this->plugin_name)?></label>
                    </div>
                    <div class="form-check form-check-inline checkbox_ays">
                        <input type="radio" id="ays_cert_orientation_p" name="ays_certificate_orientation"
                                value="p" <?php echo $certificate_orientation == 'p' ? 'checked' : ''; ?>/>
                        <label class="form-check-label" for="ays_cert_orientation_p"><?php echo __('Portrait',$this->plugin_name)?></label>
                    </div>
                </div>
            </div>
            <hr>
            <div class="form-group row">
                <div class="col-sm-5">
                    <label for="ays_certificate_body">
                        <?php echo __('Certificate background image', $this->plugin_name)?>
                        <a  class="ays_help" data-toggle="tooltip" title="<?php echo __('Specify the background image of the certificate PDF file.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                    <p class="ays_quiz_small_hint_text_for_not_recommended">
                        <span><?php echo __( "Please Note" , $this->plugin_name ); ?></span>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('In case the Certificate Background Image is not being generated, then, make sure you are not using a Security plugin. Because of that plugin and the fact that the permissions are closed, we are unable to take the file content and the issue arises for you. Please find the answers to your questions in the How to use page  (FAQs)',$this->plugin_name); ?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </p>
                </div>
                <div class="col-sm-7">
                    <div class="ays-image-wrap">
                        <a href="javascript:void(0)" class="ays-add-image add-certificate-image" style="<?php echo $certificate_image == '' ? '' : 'display:none'; ?>"><?php echo __( "Add Image", $this->plugin_name ); ?></a>
                        <input type="hidden" class="ays-image-path" id="ays_certificate_image" name="ays_certificate_image" value="<?php echo $certificate_image; ?>"/>
                        <div class="ays-image-container" style="<?php echo $certificate_image == '' ? 'display:none' : 'display:block'; ?>">
                            <span class="ays-edit-img">
                                <i class="ays_fa ays_fa_pencil_square_o"></i>
                            </span>
                            <span class="ays-remove-img"></span>
                            <img src="<?php echo $certificate_image; ?>" id="add-certificate-image"/>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="form-group row">
                <div class="col-sm-3">
                    <label for="">
                        <?php echo __('Certificate frame',$this->plugin_name)?>
                        <a  class="ays_help" data-toggle="tooltip" title="<?php echo __('Specify the frame of the certificate PDF file.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-9">
                    <div class="ays-certificate-frames">
                        <div class="ays-certificate-frame">
                            <input type="radio" name="ays_certificate_frame" value="none" <?php echo $certificate_frame == "none" ? "checked" : ""; ?> id="ays-certificate-frame-none">
                            <label for="ays-certificate-frame-none" style="width: 85%; height: 100px; display: flex; align-items: center; justify-content: center; border: 1px solid #787878;">
                                <span><?php echo __( "None", $this->plugin_name ); ?></span>
                            </label>
                        </div>
                    </div>
                    <hr>
                    <p class="ays-subtitle" style="text-align:center;"><?php echo __( "Horizontal", $this->plugin_name ); ?></p>
                    <div class="ays-certificate-frames">
                        <div class="ays-certificate-frame">
                            <input type="radio" name="ays_certificate_frame" value="default" <?php echo $certificate_frame == "default" ? "checked" : ""; ?> id="ays-certificate-frame-default">
                            <label for="ays-certificate-frame-default" style="width: 85%; height: 100px; display: flex; align-items: center; justify-content: center;">
                                <span style="display: flex;width: 90%;height: 90%;align-items: center;justify-content: center;border: 3px solid #787878;"><?php echo __( "Default", $this->plugin_name ); ?></span>
                            </label>
                        </div>
                        <?php
                        for($frame_ind = 1; $frame_ind <= 8; $frame_ind++):
                            $checked = $certificate_frame == "horizontal-".$frame_ind ? "checked" : "";
                            ?>
                            <div class="ays-certificate-frame">
                                <input type="radio" name="ays_certificate_frame" value="<?php echo "horizontal-".$frame_ind; ?>" <?php echo $checked; ?> id="ays-certificate-horizontal-frame-<?php echo $frame_ind; ?>">
                                <label for="ays-certificate-horizontal-frame-<?php echo $frame_ind; ?>" class="">
                                    <img src="<?php echo $certificate_frames_url . "horizontal-".$frame_ind.".png"; ?>" alt="">
                                </label>
                            </div>
                        <?php endfor; ?>
                    </div>
                    <hr>
                    <p class="ays-subtitle" style="text-align:center;"><?php echo __( "Vertical", $this->plugin_name ); ?></p>
                    <div class="ays-certificate-frames">
                        <?php
                        for($frame_ind = 1; $frame_ind <= 6; $frame_ind++):
                            $checked = $certificate_frame == "vertical-".$frame_ind ? "checked" : "";
                            ?>
                            <div class="ays-certificate-frame">
                                <input type="radio" name="ays_certificate_frame" value="<?php echo "vertical-".$frame_ind; ?>" <?php echo $checked; ?> id="ays-certificate-vertical-frame-<?php echo $frame_ind; ?>">
                                <label for="ays-certificate-vertical-frame-<?php echo $frame_ind; ?>" class="">
                                    <img src="<?php echo $certificate_frames_url . "vertical-".$frame_ind.".png"; ?>" alt="">
                                </label>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- Send Certificate To User -->
    <hr/>
    <div class="form-group row ays_toggle_parent">
        <div class="col-sm-3">
            <label for="ays_enable_mail_admin">
                <?php echo __('Send email to admin',$this->plugin_name)?>
                <a  class="ays_help" data-toggle="tooltip" title="<?php echo __('Every time when someone passes the Quiz, it sends an email with information about each Quiz result to the admin email from WordPress General Settings.',$this->plugin_name)?>">
                    <i class="ays_fa ays_fa_info_circle"></i>
                </a>
            </label>
        </div>
        <div class="col-sm-1">
            <input type="checkbox" class="ays-enable-timerl ays_toggle_checkbox" id="ays_enable_mail_admin"
                   name="ays_enable_admin_mail"
                   value="on" <?php echo (isset($options['admin_mail']) && $options['admin_mail'] == 'on') ? 'checked' : ''; ?>/>
        </div>
        <div class="col-sm-8 ays_toggle_target ays_divider_left" style="<?php echo (isset($options['admin_mail']) && $options['admin_mail'] == "on") ? "" : "display:none;" ?>">
            <div class="form-group row">
                <div class="col-sm-3">
                    <label for="ays_enable_send_mail_to_admin_by_pass_score">
                        <?php echo __('Pass score', $this->plugin_name); ?>
                        <a  class="ays_help" data-toggle="tooltip" title="<?php echo __('If the option is enabled, then the admin will receive the email only if the user has passed the minimum score required. It will take the value of the general pass score of the quiz. Please specify it in the Result Settings tab.',$this->plugin_name); ?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-9">
                    <input type="checkbox" class="ays-enable-timerl" id="ays_enable_send_mail_to_admin_by_pass_score" name="ays_enable_send_mail_to_admin_by_pass_score" value="on" <?php echo ($enable_send_mail_to_admin_by_pass_score) ? 'checked' : ''; ?>/>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-12">
                    <blockquote><?php echo __( 'Tick the checkbox, and admin will receive the email only if the user has passed the minimum score required.', $this->plugin_name ); ?></blockquote>
                </div>
            </div>
            <hr>
            <div class="form-group row">
                <div class="col-sm-3">
                    <label for="ays_send_mail_to_site_admin">
                        <?php echo __('Admin', $this->plugin_name)?>
                        <a  class="ays_help" data-toggle="tooltip" title="<?php echo __('Disable this feature, if you want to make it possible not to send emails to the registered Mail of the site Admin, but only to additional emails.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-1">
                    <input type="checkbox" class="ays-enable-timerl" id="ays_send_mail_to_site_admin"
                           name="ays_send_mail_to_site_admin" value="on" <?php echo $send_mail_to_site_admin; ?>/>
                </div>
                <div class="col-sm-8">
                    <input type="text" class="ays-text-input ays-enable-timerl" placeholder="<?php echo $ays_super_admin_email; ?>" disabled />
                </div>
            </div>
            <hr>
            <div class="form-group row">
                <div class="col-sm-3">
                    <label for="ays_additional_emails">
                        <?php echo __('Additional emails',$this->plugin_name)?>
                        <a  class="ays_help" data-toggle="tooltip" title="<?php echo __('Send quiz results to additional emails. Insert emails comma seperated.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-9">
                    <input type="text" class="ays-text-input" id="ays_additional_emails"
                           name="ays_additional_emails"
                           value="<?php echo $additional_emails; ?>" placeholder="example1@gmail.com, example2@gmail.com, ..."/>
                </div>
            </div>
            <hr>
            <div class="form-group row">
                <div class="col-sm-5">
                    <label for="ays_send_results_admin">
                        <?php echo __('Send Report table to admin',$this->plugin_name)?>
                        <a  class="ays_help" data-toggle="tooltip" title="<?php echo __('You can send results to the admin after the quiz is completed',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-7">
                    <input type="checkbox" class="ays-enable-timerl" id="ays_send_results_admin"
                           name="ays_send_results_admin" value="on" <?php echo $send_results_admin; ?>/>
                </div>
            </div>
            <hr/>
            <div class="form-group row">
                <div class="col-sm-5">
                    <label for="ays_send_interval_msg_to_admin">
                        <?php echo __('Send Interval message to admin',$this->plugin_name)?>
                        <a  class="ays_help" data-toggle="tooltip" title="<?php echo __('If this option is enabled then the admin will get the Email with Interval message.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-7">
                    <input type="checkbox" class="ays-enable-timerl" id="ays_send_interval_msg_to_admin"
                           name="ays_send_interval_msg_to_admin" value="on" <?php echo $send_interval_msg_to_admin; ?>/>
                </div>
            </div>
            <hr/>
            <div class="form-group row">
                <div class="col-sm-5">
                    <label for="ays_send_certificate_to_admin">
                        <?php echo __('Send Certificate to admin too',$this->plugin_name)?>
                        <a  class="ays_help" data-toggle="tooltip" title="<?php echo htmlentities(__('If this option is enabled then the admin will get the Email with an attached PDF file that gets the user. If the "Send Certificate To User" option is disabled admin does not get a certificate too.',$this->plugin_name)); ?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-7">
                    <input type="checkbox" class="ays-enable-timerl" id="ays_send_certificate_to_admin"
                           name="ays_send_certificate_to_admin" value="on" <?php echo $ays_send_certificate_to_admin ? 'checked' : ''; ?>/>
                </div>
            </div>
            <hr>
            <div class="form-group row">
                <div class="col-sm-5">
                    <label for="ays_use_subject_for_admin_email">
                        <?php echo __('Use subject for the admin email', $this->plugin_name); ?>
                        <a  class="ays_help" data-toggle="tooltip" title="<?php echo htmlentities(__('If this option is enabled then the admin will get the Email with a subject filled in the "Email configuration" section. Otherwise admin will get an email with the subject with this way "{quiz_name} - {user_name} - {score}".',$this->plugin_name)); ?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-7">
                    <input type="checkbox" class="ays-enable-timerl" id="ays_use_subject_for_admin_email"
                           name="ays_use_subject_for_admin_email" value="on" <?php echo $use_subject_for_admin_email ? 'checked' : ''; ?>/>
                </div>
            </div>
            <hr>
            <div class="form-group row ays-quiz-result-message-vars-parent">
                <div class="col-sm-3">
                    <label for="ays_mail_message_admin">
                        <?php echo __('Email message',$this->plugin_name)?>
                        <a  class="ays_help" data-toggle="tooltip" title="<?php echo __('Provide the message text for sending to the Admin by email. You can use Variables from General Settings page to insert data. (name, score, date etc.)',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                    <p class="ays_quiz_small_hint_text_for_message_variables">
                        <span><?php echo __( "To see all Message Variables " , $this->plugin_name ); ?></span>
                        <a href="?page=quiz-maker-settings&ays_quiz_tab=tab4" target="_blank"><?php echo __( "click here" , $this->plugin_name ); ?></a>
                    </p>
                </div>
                <div class="col-sm-9">
                    <?php
                    echo $quiz_message_vars_html;
                    $content = $mail_message_admin;
                    $editor_id = 'ays_mail_message_admin';
                    $settings = array('editor_height' => $quiz_wp_editor_height, 'textarea_name' => 'ays_mail_message_admin', 'editor_class' => 'ays-textarea', 'media_elements' => false);
                    wp_editor($content, $editor_id, $settings);
                    ?>
                </div>
            </div>
        </div>
    </div> <!-- Send Mail To Admin -->
    <hr/>
    <div class="form-group row">
        <div class="col-sm-4">
            <label>
                <?php echo __('Email configuration',$this->plugin_name)?>
                <a  class="ays_help" data-toggle="tooltip" title="<?php echo __('Attributes of Sending Mail',$this->plugin_name)?>">
                    <i class="ays_fa ays_fa_info_circle"></i>
                </a>
            </label>
        </div>
        <div class="col-sm-8 ays_divider_left" id="ays_email_configuration">
            <div class="form-group row">
                <div class="col-sm-3">
                    <label for="ays_email_configuration_from_email">
                        <?php echo __('From email',$this->plugin_name)?>
                        <a  class="ays_help" data-toggle="tooltip" title="<?php echo __('Specify from which email the results will be sent. If you leave it blank, it will take default value as quiz_maker@{your_site_url}',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-9">
                    <input type="text" class="ays-text-input" id="ays_email_configuration_from_email"
                           name="ays_email_configuration_from_email"
                           value="<?php echo $email_config_from_email; ?>"/>
                </div>
            </div>
            <hr/>
            <div class="form-group row">
                <div class="col-sm-3">
                    <label for="ays_email_configuration_from_name">
                        <?php echo __('From name',$this->plugin_name)?>
                        <a  class="ays_help" data-toggle="tooltip" title="<?php echo __('Specify from which name the results will be sent. It will take “Quiz Maker” if you don’t complete it.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-9">
                    <input type="text" class="ays-text-input" id="ays_email_configuration_from_name"
                           name="ays_email_configuration_from_name"
                           value="<?php echo $email_config_from_name; ?>"/>
                </div>
            </div>
            <hr/>
            <div class="form-group row">
                <div class="col-sm-3">
                    <label for="ays_email_configuration_from_subject">
                        <?php echo __('Subject',$this->plugin_name)?>
                        <a  class="ays_help" data-toggle="tooltip" title="<?php echo __('Provide the subject of the mail. It will take the quiz title if you don’t complete it.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-9">
                    <input type="text" class="ays-text-input" id="ays_email_configuration_from_subject"
                           name="ays_email_configuration_from_subject"
                           value="<?php echo $email_config_from_subject; ?>"/>
                </div>
            </div>
            <hr/>
            <div class="form-group row">
                <div class="col-sm-3">
                    <label for="ays_email_configuration_replyto_email">
                        <?php echo __('Reply to email',$this->plugin_name)?>
                        <a  class="ays_help" data-toggle="tooltip" title="<?php echo __('Specify which email will reply to the user. If you leave it blank, it will not have a default value and doesn’t specified.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-9">
                    <input type="text" class="ays-text-input" id="ays_email_configuration_replyto_email"
                           name="ays_email_config_replyto_email"
                           value="<?php echo $email_config_replyto_email; ?>"/>
                </div>
            </div>
            <hr/>
            <div class="form-group row">
                <div class="col-sm-3">
                    <label for="ays_email_configuration_replyto_name">
                        <?php echo __('Reply to name',$this->plugin_name)?>
                        <a  class="ays_help" data-toggle="tooltip" title="<?php echo __('Specify which name will have the Reply-To option. If you leave it blank, it will not have a default value and doesn’t specified.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-9">
                    <input type="text" class="ays-text-input" id="ays_email_configuration_replyto_name"
                           name="ays_email_config_replyto_name"
                           value="<?php echo $email_config_replyto_name; ?>"/>
                </div>
            </div>
        </div>
    </div> <!-- Email Configuration -->
</div>
