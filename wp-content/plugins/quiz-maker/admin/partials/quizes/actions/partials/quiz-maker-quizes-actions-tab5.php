<div id="tab5" class="ays-quiz-tab-content <?php echo ($ays_quiz_tab == 'tab5') ? 'ays-quiz-tab-content-active' : ''; ?>">
    <p class="ays-subtitle"><?php echo __('Limitation of Users',$this->plugin_name)?></p>
    <hr/>
    <div class="form-group row ays_toggle_parent">
        <div class="col-sm-3">
            <label for="ays_limit_users">
                <?php echo __('Maximum number of attempts per user',$this->plugin_name)?>
                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('After enabling this option, you can manage the counts of the attempts per user for passing the quiz.',$this->plugin_name)?>">
                    <i class="ays_fa ays_fa_info_circle"></i>
                </a>
            </label>
        </div>
        <div class="col-sm-1">
            <input type="checkbox" class="ays-enable-timer1 ays_toggle_checkbox" id="ays_limit_users" name="ays_limit_users"
                   value="on" <?php echo (isset($options['limit_users']) && $options['limit_users'] == 'on') ? 'checked' : ''; ?>/>
        </div>
        <div class="col-sm-8 ays_toggle_target ays_divider_left <?php echo (isset($options['limit_users']) && $options['limit_users'] == "on") ? "" : "display_none" ?>">
            <div class="ays-limitation-options">
                <!-- Limitation by -->
                <div class="form-group row">
                    <div class="col-sm-3">
                        <label for="ays_limitation_message">
                            <?php echo __('Detects users by',$this->plugin_name)?>
                            <a class="ays_help" style="font-size:15px;" data-toggle="tooltip" data-html="true"
                                title="<?php
                                    echo __('Choose the method of detection of the user:',$this->plugin_name) .
                                    "<ul style='list-style-type: circle;padding-left: 20px;'>".
                                        "<li>". __('By IP - Detect the users by their IP addresses and limit them. It will work both for guests and registered users. Note: in general, IP is not a static variable, it is constantly changing when the user changes his location/ WIFI/ Internet provider.',$this->plugin_name) ."</li>".
                                        "<li>". __('By User ID - Detect the users by their WP User IDs and limit them. It will work only for registered users. Recommended using this method to get more reliable results.',$this->plugin_name) ."</li>".
                                        "<li>". __('By Cookie - Detect the users by their browser cookies and limit them.  It will work both for guests and registered users.',$this->plugin_name) ."</li>".
                                        "<li>". __('By Cookie and IP - Detect the users both by their browser cookies and IP addresses and limit them. It will work both for guests and registered users.',$this->plugin_name) ."</li>".
                                    "</ul>";
                                ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-9">
                        <div class="form-check form-check-inline checkbox_ays">
                            <input type="radio" id="ays_limit_users_by_ip" class="form-check-input" name="ays_limit_users_by" value="ip" <?php echo ($limit_users_by == 'ip') ? 'checked' : ''; ?>/>
                            <label class="form-check-label" for="ays_limit_users_by_ip"><?php echo __('IP',$this->plugin_name)?></label>
                        </div>
                        <div class="form-check form-check-inline checkbox_ays">
                            <input type="radio" id="ays_limit_users_by_user_id" class="form-check-input" name="ays_limit_users_by" value="user_id" <?php echo ($limit_users_by == 'user_id') ? 'checked' : ''; ?>/>
                            <label class="form-check-label" for="ays_limit_users_by_user_id"><?php echo __('User ID',$this->plugin_name)?></label>
                        </div>
                        <div class="form-check form-check-inline checkbox_ays">
                            <input type="radio" id="ays_limit_users_by_cookie" class="form-check-input" name="ays_limit_users_by" value="cookie" <?php echo ($limit_users_by == 'cookie') ? 'checked' : ''; ?>/>
                            <label class="form-check-label" for="ays_limit_users_by_cookie"><?php echo __('Cookie',$this->plugin_name)?></label>
                        </div>
                        <div class="form-check form-check-inline checkbox_ays">
                            <input type="radio" id="ays_limit_users_by_ip_cookie" class="form-check-input" name="ays_limit_users_by" value="ip_cookie" <?php echo ($limit_users_by == 'ip_cookie') ? 'checked' : ''; ?>/>
                            <label class="form-check-label" for="ays_limit_users_by_ip_cookie"><?php echo __('IP and Cookie',$this->plugin_name)?></label>
                        </div>
                    </div>
                </div>
                <hr/>
                <!-- Limitation count -->
                <div class="form-group row">
                    <div class="col-sm-3">
                        <label for="ays_quiz_max_pass_count">
                            <?php echo __('Attempts count:',$this->plugin_name)?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Specify the count of the attempts per user for passing the quiz.',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-9">
                        <input type="number" class="ays-text-input" id="ays_quiz_max_pass_count" name="ays_quiz_max_pass_count" value="<?php echo $quiz_max_pass_count; ?>"/>
                    </div>
                </div>
                <hr/>
                <!-- Limitation pass score -->
                <div class="form-group row">
                    <div class="col-sm-3">
                        <label for="ays_quiz_pass_score">
                            <?php echo __('Pass score for attempt restriction',$this->plugin_name)?> (%)
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Select the passing score(in percentage), and the attempt of the user will be detected only under that given condition. For example: If we give 40% value to it and assign 5 to the Attempts count option, the user can pass the quiz with getting more than 40% score in 5 times, but will have a chance to pass the quiz with getting under the 40% score as to how much as he/she wants.',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-9">
                        <input type="number" class="ays-text-input" id="ays_quiz_pass_score" name="ays_quiz_pass_score" value="<?php echo $quiz_pass_score; ?>"/>
                    </div>
                </div>
                <hr/>
                <!-- Limit count by user role -->
                <div class="form-group row">
                    <div class="col-sm-3">
                        <label for="ays_limit_count_by_user_role">
                            <?php echo __( 'Attempts count for each user role', $this->plugin_name ); ?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Limit the count of the attempts for each user role for passing the quiz. To have this option work you need to enable Only for selected user role option.', $this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-9">
                        <input type="number" name="ays_limit_count_by_user_role" id="ays_limit_count_by_user_role" class="ays-text-input" value="<?php echo $limit_attempts_count_by_user_role; ?>"/>
                    </div>
                </div>
                <hr>
                <!-- Limitation message -->
                <div class="form-group row ays-quiz-result-message-vars-parent">
                    <div class="col-sm-3">
                        <label for="ays_limitation_message">
                            <?php echo __('Message',$this->plugin_name)?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Write the message for those who have already passed the quiz under the given conditions.',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-9">
                        <?php
                        echo $quiz_message_vars_limitation_message_html;
                        $content = wpautop(stripslashes((isset($options['limitation_message'])) ? $options['limitation_message'] : ''));
                        $editor_id = 'ays_limitation_message';
                        $settings = array('editor_height' => $quiz_wp_editor_height, 'textarea_name' => 'ays_limitation_message', 'editor_class' => 'ays-textarea', 'media_elements' => false);
                        wp_editor($content, $editor_id, $settings);
                        ?>
                    </div>
                </div>
                <hr/>
                <!-- Limitation redirect url -->
                <div class="form-group row">
                    <div class="col-sm-3">
                        <label for="ays_redirect_url">
                            <?php echo __('Redirect URL',$this->plugin_name)?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Leave a current page to go to the link provided',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-9">
                        <input type="text" name="ays_redirect_url" id="ays_redirect_url"
                               class="ays-text-input"
                               value="<?php echo isset($options['redirect_url']) ? $options['redirect_url'] : ''; ?>"/>
                    </div>
                </div>
                <hr/>
                <!-- Limitation redirect delay -->
                <div class="form-group row">
                    <div class="col-sm-3">
                        <label for="ays_redirection_delay">
                            <?php echo __('Redirect delay',$this->plugin_name)?>(s)
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Leave current page and go to the link provided after X second',$this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-9">
                        <input type="number" name="ays_redirection_delay" id="ays_redirection_delay"
                               class="ays-text-input"
                               value="<?php echo isset($options['redirection_delay']) ? $options['redirection_delay'] : 0; ?>"/>
                    </div>
                </div>
                <hr/>
                <!-- Turn on extra security check -->
                <div class="form-group row">
                    <div class="col-sm-3">
                        <label for="ays_turn_on_extra_security_check">
                            <?php echo __( 'Turn on extra security check', $this->plugin_name ); ?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('When the attempt limit of the quiz has reached, and a user tries to open your quiz in more than one tab concurrently, the results of their additional attempt will not be stored.', $this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-9">
                        <input type="checkbox" name="ays_turn_on_extra_security_check" id="ays_turn_on_extra_security_check" class="" value="on" <?php echo $turn_on_extra_security_check ? 'checked' : ''; ?> />
                    </div>
                </div>
                <hr/>
                <!-- Hide attempts limitation notice -->
                <div class="form-group row">
                    <div class="col-sm-3">
                        <label for="ays_hide_limit_attempts_notice">
                            <?php echo __( 'Hide attempts limitation notice', $this->plugin_name ); ?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Hide the remaining attempts count warning when the limitation is activated.', $this->plugin_name)?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-9">
                        <input type="checkbox" name="ays_hide_limit_attempts_notice" id="ays_hide_limit_attempts_notice" class="" value="on" <?php echo $hide_limit_attempts_notice ? 'checked' : ''; ?> />
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- Limit Users to pass quiz only once -->
    <hr/>
    <div class="form-group row ays_toggle_parent ays-quiz-result-message-vars-parent">
        <div class="col-sm-3">
            <label for="ays_enable_logged_users">
                <?php echo __('Only for logged in users',$this->plugin_name)?>
                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('After enabling this option, only logged in users will be able to pass the quiz.',$this->plugin_name)?>">
                    <i class="ays_fa ays_fa_info_circle"></i>
                </a>
            </label>
        </div>
        <div class="col-sm-1">
            <input type="checkbox" class="ays-enable-timer1 ays_toggle_checkbox" id="ays_enable_logged_users"
                   name="ays_enable_logged_users" <?php echo (isset($options['enable_restriction_pass']) && $options['enable_restriction_pass'] == 'on') || (isset($options['enable_restriction_pass_users']) && $options['enable_restriction_pass_users'] == 'on') ? 'disabled' : ''; ?>
                   value="on" <?php echo (((isset($options['enable_logged_users']) && $options['enable_logged_users'] == 'on')) || (isset($options['enable_restriction_pass']) && $options['enable_restriction_pass'] == 'on') || (isset($options['enable_restriction_pass_users']) && $options['enable_restriction_pass_users'] == 'on')) ? 'checked' : ''; ?>/>
        </div>
        <div class="col-sm-8 ays_toggle_target ays_divider_left <?php echo ((isset($options['enable_logged_users']) && $options['enable_logged_users'] == 'on')) ? '' : 'display_none' ?>"
             id="ays_logged_in_users_div" >
            <div class="form-group row">
                <div class="col-sm-2">
                    <label for="ays_logged_in_message">
                        <?php echo __('Message',$this->plugin_name)?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Message for those who haven’t logged in',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-10">
                    <?php
                    echo $quiz_message_vars_logged_in_users_html;
                    $content = wpautop(stripslashes((isset($options['enable_logged_users_message'])) ? $options['enable_logged_users_message'] : ''));
                    $editor_id = 'ays_logged_in_message';
                    $settings = array('editor_height' => $quiz_wp_editor_height, 'textarea_name' => 'ays_enable_logged_users_message', 'editor_class' => 'ays-textarea', 'media_elements' => false);
                    wp_editor($content, $editor_id, $settings);
                    ?>
                </div>
            </div>
            <hr/>
            <div class="form-group row">
                <div class="col-sm-3">
                    <label for="ays_show_login_form">
                        <?php echo __('Show Login form',$this->plugin_name)?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Show the Login form at the bottom of the message for not logged in users.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-9">
                    <input type="checkbox" class="ays-enable-timer1" id="ays_show_login_form" name="ays_show_login_form" value="on" <?php echo $show_login_form ? 'checked' : ''; ?>/>
                </div>
            </div>
        </div>
    </div> <!-- Only for logged in users -->
    <hr/>
    <div class="form-group row ays_toggle_parent">
        <div class="col-sm-3">
            <label for="ays_enable_restriction_pass">
                <?php echo __('Only for selected user role',$this->plugin_name)?>
                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Quiz is available only for the roles mentioned in the list.',$this->plugin_name)?>">
                    <i class="ays_fa ays_fa_info_circle"></i>
                </a>
            </label>
        </div>
        <div class="col-sm-1">
            <input type="checkbox" class="ays-enable-timer1 ays_toggle_checkbox" id="ays_enable_restriction_pass"
                   name="ays_enable_restriction_pass"
                   value="on" <?php echo (isset($options['enable_restriction_pass']) && $options['enable_restriction_pass'] == 'on') ? 'checked' : ''; ?>/>
        </div>
        <div class="col-sm-8 ays_toggle_target ays_divider_left <?php echo (isset($options['enable_restriction_pass']) && $options['enable_restriction_pass'] == 'on') ? '' : 'display_none'; ?>">
            <div class="form-group row">
                <div class="col-sm-2">
                    <label for="ays_users_roles">
                        <?php echo __('User role',$this->plugin_name)?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Role of the user on the website.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-10">
                    <select name="ays_users_roles[]" id="ays_users_roles" multiple>
                        <?php
                        foreach ($ays_users_roles as $key => $user_role) {
                            $selected_role = "";
                            if(isset($options['user_role'])){
                                if(is_array($options['user_role'])){
                                    if(in_array($user_role['name'], $options['user_role'])){
                                        $selected_role = 'selected';
                                    }else{
                                        $selected_role = '';
                                    }
                                }else{
                                    if($options['user_role'] == $user_role['name']){
                                        $selected_role = 'selected';
                                    }else{
                                        $selected_role = '';
                                    }
                                }
                            }
                            echo "<option value='" . $user_role['name'] . "' " . $selected_role . ">" . $user_role['name'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-2">
                    <label for="restriction_pass_message">
                        <?php echo __('Message',$this->plugin_name)?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Message for the users who aren’t included in the above-mentioned list.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-10">
                    <?php
                    $content = wpautop(stripslashes((isset($options['restriction_pass_message'])) ? $options['restriction_pass_message'] : ''));
                    $editor_id = 'restriction_pass_message';
                    $settings = array('editor_height' => $quiz_wp_editor_height, 'textarea_name' => 'restriction_pass_message', 'editor_class' => 'ays-textarea', 'media_elements' => false);
                    wp_editor($content, $editor_id, $settings);
                    ?>
                </div>
            </div>
        </div>
    </div> <!-- Only for selected user role -->
    <hr/>  <!-- AV Access Only selected users -->
    <div class="form-group row ays_toggle_parent">
        <div class="col-sm-3">
            <label for="ays_enable_restriction_pass_users">
                <?php echo __('Access only selected users',$this->plugin_name)?>
                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Quiz is available only for the users mentioned in the list.',$this->plugin_name)?>">
                    <i class="ays_fa ays_fa_info_circle"></i>
                </a>
            </label>
        </div>
        <div class="col-sm-1">
            <input type="checkbox" class="ays-enable-timer1 ays_toggle_checkbox" id="ays_enable_restriction_pass_users"
                   name="ays_enable_restriction_pass_users"
                   value="on" <?php echo (isset($options['enable_restriction_pass_users']) && $options['enable_restriction_pass_users'] == 'on') ? 'checked' : ''; ?>/>
        </div>
        <div class="col-sm-8 ays_toggle_target ays_divider_left <?php echo (isset($options['enable_restriction_pass_users']) && $options['enable_restriction_pass_users'] == 'on') ? '' : 'display_none'; ?>">
            <div class="form-group row">
                <div class="col-sm-2">
                    <label for="ays_users_roles">
                        <?php echo __('Users',$this->plugin_name)?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Users on the website.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-10">
                    <select id="ays_quiz_users_sel" name="ays_users_search[]" multiple>
                        <?php
                        foreach ($ays_users_search as $key => $users_search) {
                            $user_search = $users_search;
                            $selected_users = "";
                            if(isset($options['ays_users_search'])){
                                if(is_array($options['ays_users_search'])){
                                    if(in_array($user_search['ID'], $options['ays_users_search'])){
                                        echo "<option value='" . $user_search['ID'] . "' selected>" . $user_search['display_name'] . "</option>";
                                    }else{
                                        echo "";
                                    }
                                }else{
                                    if($options['ays_users_search'] == $user_search['ID']){
                                        echo "<option value='" . $user_search['ID'] . "' selected>" . $user_search['display_name'] . "</option>";
                                    }else{
                                        echo "";
                                    }
                                }
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-2">
                    <label for="restriction_pass_users_message">
                        <?php echo __('Message',$this->plugin_name)?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Message for the users who aren’t included in the above-mentioned list.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-10">
                    <?php
                    $content = wpautop(stripslashes((isset($options['restriction_pass_users_message'])) ? $options['restriction_pass_users_message'] : ''));
                    $editor_id = 'restriction_pass_users_message';
                    $settings = array('editor_height' => $quiz_wp_editor_height, 'textarea_name' => 'restriction_pass_users_message', 'editor_class' => 'ays-textarea', 'media_elements' => false);
                    wp_editor($content, $editor_id, $settings);
                    ?>
                </div>
            </div>
        </div>
    </div> <!-- Access Only selected users -->
    <hr>
    <div class="form-group row ays_toggle_parent">
        <div class="col-sm-3">
            <label for="ays_enable_tackers_count">
                <?php echo __('Limitation count of takers', $this->plugin_name)?>
                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('You can choose how many users can pass the quiz.',$this->plugin_name)?>">
                    <i class="ays_fa ays_fa_info_circle"></i>
                </a>
            </label>
        </div>
        <div class="col-sm-1">
            <input type="checkbox" class="ays-enable-timer1 ays_toggle_checkbox" id="ays_enable_tackers_count"
                   name="ays_enable_tackers_count" value="on" <?php echo $enable_tackers_count ? 'checked' : ''; ?>/>
        </div>
        <div class="col-sm-8 ays_toggle_target ays_divider_left <?php echo $enable_tackers_count ? '' : 'display_none'; ?>">
            <div class="form-group row">
                <div class="col-sm-2">
                    <label for="ays_tackers_count">
                        <?php echo __('Count',$this->plugin_name)?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('The number of users who can pass the quiz.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-10">
                    <input type="number" name="ays_tackers_count" id="ays_tackers_count" class="ays-enable-timerl ays-text-input"
                           value="<?php echo $tackers_count; ?>">
                </div>
            </div>
            <hr>
            <div class="form-group row">
                <div class="col-sm-2">
                    <label for="ays_quiz_tackers_message">
                        <?php echo __('Message',$this->plugin_name); ?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Show the message when the quiz is already taken by the required count of takers.',$this->plugin_name); ?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-10">
                    <?php
                    $editor_id = 'ays_quiz_tackers_message';
                    $settings = array('editor_height' => $quiz_wp_editor_height, 'textarea_name' => 'ays_quiz_tackers_message', 'editor_class' => 'ays-textarea', 'media_elements' => false);
                    wp_editor($quiz_tackers_message, $editor_id, $settings);
                    ?>
                </div>
            </div>
        </div>
    </div> <!-- Limitation count of takers -->
    <hr>
    <div class="form-group row ays_toggle_parent">
        <div class="col-sm-3">
            <label for="ays_enable_password">
                <?php echo __('Password for passing quiz', $this->plugin_name)?>
                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('You can choose a password for users to pass the quiz.',$this->plugin_name)?>">
                    <i class="ays_fa ays_fa_info_circle"></i>
                </a>
            </label>
        </div>
        <div class="col-sm-1">
            <input type="checkbox" class="ays-enable-timer1 ays_toggle_checkbox" id="ays_enable_password"
                   name="ays_enable_password" value="on" <?php echo $enable_password ? 'checked' : ''; ?>/>
        </div>
        
        <div class="col-sm-12 ays_toggle_target <?php echo $enable_password ? '' : 'display_none'; ?>">
            <div class="form-group row">
                <div class="col-sm-6">
                    <label class="ays_quiz_loader" for="ays_psw_quiz">
                        <input type="radio" class="ays_generate_password_quiz_class" id="ays_psw_quiz" name='ays_psw_quiz' value='general' <?php echo $ays_passwords_quiz == 'general' ? 'checked' : ''; ?>>
                        <?php echo __('General', $this->plugin_name) ?>
                    </label>
                    <label class="ays_quiz_loader" for="ays_generate_password_quiz">
                        <input type="radio" class="ays_generate_password_quiz_class" id="ays_generate_password_quiz" name="ays_psw_quiz" value="generated_password" <?php echo $ays_passwords_quiz == 'generated_password' ? 'checked' : ''; ?>>
                        <?php echo __('Generated Passwords', $this->plugin_name) ?>
                    </label>
                </div>

                <div class="col-sm-6 ays_psw_quiz_import_type_box">
                    <label class="ays_quiz_loader" for="ays_psw_quiz_import_type_default">
                        <input type="radio" class="ays_generate_password_quiz_class_import_type" id="ays_psw_quiz_import_type_default" name='ays_quiz_password_import_type' value='default' <?php echo $quiz_password_import_type == 'default' ? 'checked' : ''; ?> <?php echo $ays_passwords_quiz == 'general' ? 'disabled' : ''; ?>>
                        <?php echo __('Default', $this->plugin_name) ?>
                    </label>
                    <label class="ays_quiz_loader" for="ays_generate_password_quiz_file_upload">
                        <input type="radio" class="ays_generate_password_quiz_class_import_type" id="ays_generate_password_quiz_file_upload" name="ays_quiz_password_import_type" value="file_upload" <?php echo $quiz_password_import_type == 'file_upload' ? 'checked' : ''; ?> <?php echo $ays_passwords_quiz == 'general' ? 'disabled' : ''; ?>>
                        <?php echo __('File upload', $this->plugin_name) ?>
                    </label>
                    <label class="ays_quiz_loader" for="ays_generate_password_quiz_clipboard">
                        <input type="radio" class="ays_generate_password_quiz_class_import_type" id="ays_generate_password_quiz_clipboard" name="ays_quiz_password_import_type" value="clipboard" <?php echo $quiz_password_import_type == 'clipboard' ? 'checked' : ''; ?> <?php echo $ays_passwords_quiz == 'general' ? 'disabled' : ''; ?>>
                        <?php echo __('Clipboard', $this->plugin_name) ?>
                    </label>
                </div>
            </div>
            <hr>
            <div class="form-group row <?php echo $ays_passwords_quiz == 'generated_password' ? 'display_none' : '';?>" id="ays_psw_content_quiz">
               <div class="col-sm-12">
                   <div class="form-group row">
                       <div class="col-sm-3">
                            <label for="ays_password_quiz">
                                <?php echo __('Password',$this->plugin_name)?>
                                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Password for users who can pass the quiz.',$this->plugin_name)?>">
                                    <i class="ays_fa ays_fa_info_circle"></i>
                                </a>
                            </label>
                        </div>
                        <div class="col-sm-9">
                            <input type="text" name="ays_password_quiz" id="ays_password_quiz" class="ays-enable-timer ays-text-input"
                                   value="<?php echo $password_quiz; ?>">
                        </div>
                    </div>
                    <hr>
                    <div class="form-group row">
                        <div class="col-sm-3">
                            <label for="ays_quiz_enable_password_visibility">
                                <?php echo __('Enable toggle password visibility',$this->plugin_name); ?>
                                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Tick the option, and it will let you enable and disable password visibility in a password input field.',$this->plugin_name); ?>">
                                    <i class="ays_fa ays_fa_info_circle"></i>
                                </a>
                            </label>
                        </div>
                        <div class="col-sm-9">
                            <input type="checkbox" class="ays-enable-timer1" id="ays_quiz_enable_password_visibility" name="ays_quiz_enable_password_visibility" value="on" <?php echo $quiz_enable_password_visibility ? 'checked' : ''; ?>/>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group row">
                        <div class="col-sm-3">
                            <label for="ays_quiz_password_message">
                                <?php echo __('Message',$this->plugin_name); ?>
                                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Users will see this message before entering the password for passing the quiz.',$this->plugin_name); ?>">
                                    <i class="ays_fa ays_fa_info_circle"></i>
                                </a>
                            </label>
                        </div>
                        <div class="col-sm-9">
                            <?php
                            $content = $quiz_password_message;
                            $editor_id = 'ays_quiz_password_message';
                            $settings = array('editor_height' => $quiz_wp_editor_height, 'textarea_name' => 'ays_quiz_password_message', 'editor_class' => 'ays-textarea', 'media_elements' => false);
                            wp_editor($content, $editor_id, $settings);
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group row <?php echo $ays_passwords_quiz == 'general' ? 'display_none' : '';?>" id="ays_generate_psw_content_quiz">
                <div class="col-sm-12">
                    <div class="form-group row ays_quiz_generate_password_type_box <?php echo $quiz_password_import_type == 'default' ? '' : 'display_none';?>" data-type="default">
                       <div class="col-sm-3">
                            <label for="ays_password_count_quiz">
                                <?php echo __('Passwords Count',$this->plugin_name)?>
                                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Select your preferred count of passwords and the system will generate it for you. You can copy the password(s) from the Created column by clicking on the copy button.',$this->plugin_name)?>">
                                    <i class="ays_fa ays_fa_info_circle"></i>
                                </a>
                            </label>
                        </div>
                        <div class="col-sm-9" style="display:flex;">
                            <input type="text" name="ays_password_count_quiz" id="ays_password_count_quiz" class="ays-enable-timer ays-text-input" value="" style="margin-right: 5px;">
                            <input type="button" id="ays_generate_password_submit_quiz" name="ays_generate_password_submit_quiz" value="<?php echo __( "Submit", $this->plugin_name ); ?>" class="ays_genreted_password_count button">
                        </div>
                    </div> <!-- Passwords Count -->

                    <div class="form-group row ays_quiz_generate_password_type_box <?php echo $quiz_password_import_type == 'file_upload' ? '' : 'display_none';?>" data-type="file_upload">
                       <div class="col-sm-3">
                            <label for="ays_quiz_password_csv_txt_import_file">
                                <?php echo __("Choose .csv, .txt file to import", $this->plugin_name); ?>
                                <a class="ays_help" data-html="true" data-toggle="tooltip" title="<?php echo __("Click on the Choose file button and select your .csv or .txt file to import. The password list should be written properly (see the Export example).",$this->plugin_name); ?>">
                                    <i class="ays_fa ays_fa_info_circle"></i>
                                </a>
                            </label>
                        </div>
                        <div class="col-sm-7" style="display:flex;">
                            <div>
                                <input type="file" accept=".csv, .txt" name="ays_quiz_password_csv_txt_import_file" id="ays_quiz_password_csv_txt_import_file">
                            </div>
                            <div>
                                <button type="button" class="button button-primary ays-quiz-password-csv-txt-import-action" data-type="csv" disabled> <?php echo __('Import', $this->plugin_name); ?> </button>
                            </div>
                            <div class="ays-quiz-password-csv-txt-import-error-message">
                                
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="ays-quiz-coupon-example-export">
                                <a href="<?php echo AYS_QUIZ_ADMIN_URL;?>/partials/quizes/export_file/quiz_maker_password_data_example.csv" download="quiz_maker_password_data_example.csv" type="button" class="button button-small ays-quiz-export-coupon-data-example">
                                    <img src="<?php echo AYS_QUIZ_ADMIN_URL; ?>/images/icons/export_example.svg" style='width:20px;'>
                                    <?=__("CSV", $this->plugin_name)?>
                                </a>
                                <a href="<?php echo AYS_QUIZ_ADMIN_URL;?>/partials/quizes/export_file/quiz_maker_password_data_example.txt" download="quiz_maker_password_data_example.txt" type="button" class="button button-small ays-quiz-export-coupon-data-example" style="margin-left: 10px;">
                                    <img src="<?php echo AYS_QUIZ_ADMIN_URL; ?>/images/icons/export_example.svg" style='width:20px;'>
                                    <?=__("TXT", $this->plugin_name)?>
                                </a>
                            </div>
                        </div>
                    </div> <!-- File upload -->

                    <div class="form-group row ays_quiz_generate_password_type_box <?php echo $quiz_password_import_type == 'clipboard' ? '' : 'display_none';?>" data-type="clipboard">
                       <div class="col-sm-3">
                            <label for="ays_password_count_quiz">
                                <?php echo __('Clipboard',$this->plugin_name)?>
                                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Write the passwords in the Clipboard field under one another and click on the Submit button. By this, the passwords will automatically be implemented in the Active Passwords field.',$this->plugin_name)?>">
                                    <i class="ays_fa ays_fa_info_circle"></i>
                                </a>
                            </label>
                        </div>
                        <div class="col-sm-6" style="display:block;">
                            <textarea class="ays-textarea" id="ays_quiz_generate_password_type_clipboard_textarea"></textarea>
                            <div><span style="color: red;"><?php echo __("Important:") ?></span> <span><?php echo __("One line per key") ?></span></div>
                        </div>
                        <div class="col-sm-3" style="display: flex; justify-content: center; align-items: center;">
                            <input type="button" value="<?php echo __( "Submit", $this->plugin_name ); ?>" class="ays_quiz_generate_password_type_clipboard_textarea_button button">
                        </div>
                    </div> <!-- Clipboard -->

                    <div id="ays_generated_password" class="table-responsive form-group d-flex row">
                        <div class="col-sm-4">
                            <p>
                               <?php echo __('Created',$this->plugin_name)?>
                                <a class="ays_gen_psw_copy_all" id="ays_gen_psw_copy_all">
                                    <i class="fa fa-clipboard" aria-hidden="true"></i>
                                </a>
                            </p>
                            <ul class="ays_created" id="ays_generated_psw">
                                <?php
                                    if(!empty($created_passwords)){
                                        $created_passwords_content = '';
                                        foreach ($created_passwords as $key => $created_password) {
                                            $created_passwords_content .= '<li>';
                                                $created_passwords_content .= '<span class="created_psw">'.$created_password.'</span>';
                                                $created_passwords_content .= '<a class="ays_gen_psw_copy"><i class="fa fa-clipboard" aria-hidden="true"></i></a>';
                                                $created_passwords_content .= '<input type="hidden" name="ays_generated_psw[]" value="'.$created_password.'" class="ays_generated_psw">';
                                            $created_passwords_content .= '</li>';
                                        }
                                        echo $created_passwords_content;
                                    }
                                ?>
                            </ul>
                        </div>
                        <div class="col-sm-4">
                            <p><?php echo __('Active',$this->plugin_name)?></p>
                            <ul class="ays_active">
                                <?php
                                    if(!empty($active_passwords)){
                                        $active_passwords_content = '';

                                        foreach ($active_passwords as $key => $active_password_value) {
                                            $active_passwords_content .= '<li>';
                                                $active_passwords_content .= '<span class="created_psw">'.$active_password_value.'</span>';
                                                $active_passwords_content .= '<a class="ays_gen_psw_move_to_used"><i class="fa fa-clipboard" aria-hidden="true"></i></a>';
                                                $active_passwords_content .= '<input type="hidden" name="ays_active_gen_psw[]" value="'.$active_password_value.'" class="ays_active_gen_psw">';
                                            $active_passwords_content .= '</li>';
                                        }
                                        echo $active_passwords_content;
                                    }
                                ?>
                            </ul>
                        </div>
                        <div class="col-sm-4">
                            <p><?php echo __('Used',$this->plugin_name)?></p>
                            <ul class="ays_used">
                                <?php
                                    if(!empty($used_passwords)){
                                        $used_passwords_content = '';
                                        foreach ($used_passwords as $key => $used_password_value) {
                                            $user_info = $used_password_value;

                                            if ( $used_password_value != "" ) {
                                                $used_passwords_content .= '<li>';
                                                    $used_passwords_content .= '<span class="created_psw">'.$used_password_value.'</span>';
                                                    $used_passwords_content .= '<input type="hidden" name="ays_used_psw[]" value="'.$used_password_value.'" class="ays_used_psw">';
                                                $used_passwords_content .= '</li>';
                                            }


                                        }
                                        echo $used_passwords_content;
                                    }
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- Password for quiz -->
</div>
