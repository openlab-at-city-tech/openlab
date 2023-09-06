<div id="tab4" class="ays-quiz-tab-content <?php echo ($ays_quiz_tab == 'tab4') ? 'ays-quiz-tab-content-active' : ''; ?>">
    <p class="ays-subtitle"><?php echo __('Quiz results settings',$this->plugin_name)?></p>
    <hr/>
    <div class="form-group row">
        <div class="col-sm-4">
            <label for="ays_calculate_score">
                <?php echo __('Calculate the score',$this->plugin_name)?>
                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Calculate the score of results by the selected method. You can only choose one of these two options.',$this->plugin_name)?>">
                    <i class="ays_fa ays_fa_info_circle"></i>
                </a>
            </label>
        </div>
        <div class="col-sm-5">
            <label class="ays_quiz_loader">
                <input type="radio" class="ays-enable-timer1" name="ays_calculate_score" value="by_correctness" <?php echo ($calculate_score == 'by_correctness') ? 'checked' : '' ?>/>
                <span style="margin-right:5px;"><?php echo __( "By correctness", $this->plugin_name ); ?></span>
                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('It will calculate the score based on correct answers of the question. It will store the score by percentage. You can use Variables (General Settings) to show the quantity of the questions answered right.',$this->plugin_name)?>">
                    <i class="ays_fa ays_fa_info_circle"></i>
                </a>
            </label>
            <label class="ays_quiz_loader">
                <input type="radio" class="ays-enable-timer1" name="ays_calculate_score" value="by_points" <?php echo ($calculate_score == 'by_points') ? 'checked' : '' ?>/>
                <span style="margin-right:5px;"><?php echo __( "By weight / points", $this->plugin_name ); ?></span>
                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('It will calculate the score based on Answers points and Questions points. Again you can use Variables to show the user’s score at the end of the quiz. If you choose this option the features connected with correctness will be disabled.',$this->plugin_name)?>">
                    <i class="ays_fa ays_fa_info_circle"></i>
                </a>
            </label>
        </div>
        <div class="col-sm-3">
            <div class="ays-quiz-heading-box ays-quiz-unset-float ays-quiz-unset-margin">
                <div class="ays-quiz-wordpress-user-manual-box ays-quiz-wordpress-text-align">
                    <a href="https://www.youtube.com/watch?v=DHolVT3O0Zk" target="_blank">
                        <?php echo __("How to create scored quiz - video", $this->plugin_name); ?>
                    </a>
                </div>
            </div>
        </div>
    </div> <!-- Calculate the score -->
    <hr/>
    <div class="form-group row ays_toggle_parent">
        <div class="col-sm-4">
            <label for="ays_redirect_after_submit">
                <?php echo __('Redirect after submission',$this->plugin_name); ?>
                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Redirect to custom URL after user submit the form.',$this->plugin_name); ?>">
                    <i class="ays_fa ays_fa_info_circle"></i>
                </a>
            </label>
        </div>
        <div class="col-sm-1">
            <input type="checkbox" class="ays-enable-timer1 ays_toggle_checkbox" id="ays_redirect_after_submit"
                   name="ays_redirect_after_submit"
                   value="on" <?php echo $redirect_after_submit ? 'checked' : '' ?>/>
        </div>
        <div class="col-sm-7 ays_toggle_target ays_divider_left <?php echo $redirect_after_submit ? '' : 'display_none'; ?>">
            <div class="form-group row">
                <div class="col-sm-4">
                    <label for="ays_submit_redirect_url">
                        <?php echo __('Redirect URL',$this->plugin_name)?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('The URL for redirecting after the user submits the form.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-8">
                    <input type="text" class="ays-text-input" id="ays_submit_redirect_url"
                        name="ays_submit_redirect_url"
                        value="<?php echo $submit_redirect_url; ?>"/>
                    <span style="display:block;" class="ays_quiz_small_hint_text">
                        <?php echo 
                            sprintf( __("Add '%s' phrase at the end of the url.%s Use %s line in your code, by replacing the 'if_false' with a value that will be returned in case something goes wrong.", $this->plugin_name),
                            "<strong class='ays-quiz-shortcode-box' onClick='selectElementContents(this)' class='ays_help' data-toggle='tooltip' title='" . __('Copy', $this->plugin_name) . "'>[uniquecode]</strong>",
                            "<br>",
                            "<strong class='ays-quiz-shortcode-box' onClick='selectElementContents(this)' class='ays_help' data-toggle='tooltip' title='" . __('Copy', $this->plugin_name) . "'>apply_filters('ays_quiz_get_submission_results', 'if_false')</strong>"
                            ); 
                        ?>
                    </span>

                </div>
            </div>
            <hr/>
            <div class="form-group row">
                <div class="col-sm-4">
                    <label for="ays_submit_redirect_delay">
                        <?php echo __('Redirect delay (sec)', $this->plugin_name)?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('The redirection delay in seconds after the user submits the form. Value should be greater than 0.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-8">
                    <input type="number" class="ays-text-input" id="ays_submit_redirect_delay"
                        name="ays_submit_redirect_delay"
                        value="<?php echo $submit_redirect_delay; ?>"/>
                </div>
            </div>
            <hr/>
            <div class="form-group row">
                <div class="col-sm-4">
                    <label for="ays_quiz_message_before_redirect_timer">
                        <?php echo __('Message before redirect timer',$this->plugin_name); ?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo esc_attr( __('Write a message to display before the timer. For example, "You will be redirected in 00:30".',$this->plugin_name) ); ?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-8">
                    <input type="text" class="ays-text-input" id="ays_quiz_message_before_redirect_timer" name="ays_quiz_message_before_redirect_timer" value="<?php echo $quiz_message_before_redirect_timer; ?>"/>
                </div>
            </div>
        </div>
    </div> <!-- Redirect after submit -->
    <hr/>
    <div class="form-group row ays_toggle_parent">
        <div class="col-sm-4">
            <label for="ays_enable_exit_button">
                <?php echo __('Enable Exit button',$this->plugin_name); ?>
                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Exit button will be displayed in the finish page and must redirect the user to a custom URL.',$this->plugin_name); ?>">
                    <i class="ays_fa ays_fa_info_circle"></i>
                </a>
            </label>
        </div>
        <div class="col-sm-1">
            <input type="checkbox" class="ays-enable-timer1 ays_toggle_checkbox" id="ays_enable_exit_button"
                   name="ays_enable_exit_button"
                   value="on" <?php echo $enable_exit_button ? 'checked' : '' ?>/>
        </div>
        <div class="col-sm-7 ays_toggle_target ays_divider_left <?php echo $enable_exit_button ? '' : 'display_none'; ?>">
            <div class="form-group row">
                <div class="col-sm-4">
                    <label for="ays_exit_redirect_url">
                        <?php echo __('Redirect URL',$this->plugin_name)?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('The custom URL address for EXIT button in finish page.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-8">
                    <input type="text" class="ays-text-input" id="ays_exit_redirect_url"
                        name="ays_exit_redirect_url"
                        value="<?php echo $exit_redirect_url; ?>"/>
                </div>
            </div>
        </div>
    </div> <!-- Enable EXIT button -->
    <hr/>
    <div class="form-group row">
        <div class="col-sm-4">
            <label for="ays_hide_score">
                <?php echo __('Hide score',$this->plugin_name)?>
                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Disable to show the user score with percentage on the finish page. If you want to show points or correct answers count, you need to tick this option and use Variables (General Settings) in the “Text for showing after quiz completion” option.',$this->plugin_name)?>">
                    <i class="ays_fa ays_fa_info_circle"></i>
                </a>
            </label>
        </div>
        <div class="col-sm-8">
            <input type="checkbox" class="ays-enable-timer1" id="ays_hide_score"
                   name="ays_hide_score"
                   value="on" <?php echo (isset($options['hide_score']) && $options['hide_score'] == 'on') ? 'checked' : '' ?>/>
        </div>
    </div> <!-- Hide Score -->
    <hr/>
    <div class="form-group row">
        <div class="col-sm-4">
            <label>
                <?php echo __('Display score',$this->plugin_name)?>
                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('How to display score of result',$this->plugin_name)?>">
                    <i class="ays_fa ays_fa_info_circle"></i>
                </a>
            </label>
        </div>
        <div class="col-sm-8">
            <label class="ays_quiz_loader">
                <input type="radio" class="ays-enable-timer1" name="ays_display_score" value="by_percentage" <?php echo ($display_score == 'by_percentage') ? 'checked' : '' ?>/>
                <span><?php echo __( "By percentage", $this->plugin_name ); ?></span>
            </label>
            <label class="ays_quiz_loader">
                <input type="radio" class="ays-enable-timer1" name="ays_display_score" value="by_correctness" <?php echo ($display_score == 'by_correctness') ? 'checked' : '' ?>/>
                <span><?php echo __( "By correct answers count", $this->plugin_name ); ?></span>
            </label>
            <label class="ays_quiz_loader">
                <input type="radio" class="ays-enable-timer1" name="ays_display_score" value="by_points" <?php echo ($display_score == 'by_points') ? 'checked' : '' ?>/>
                <span><?php echo __( "By weight/points", $this->plugin_name ); ?></span>
            </label>
        </div>
    </div> <!-- Display score -->
    <hr/>
    <div class="form-group row">
        <div class="col-sm-4">
            <label for="ays_enable_bar_option">
                <?php echo __('Enable progress bar',$this->plugin_name); ?>
                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Show score via progressbar',$this->plugin_name); ?>">
                    <i class="ays_fa ays_fa_info_circle"></i>
                </a>
            </label>
        </div>
        <div class="col-sm-8">
            <input type="checkbox" class="ays-enable-timer1" id="ays_enable_bar_option"
                   name="ays_enable_progress_bar"
                   value="on" <?php echo (isset($options['enable_progress_bar']) && $options['enable_progress_bar'] == 'on') ? 'checked' : '' ?>/>
        </div>
    </div> <!-- Enable progressbar -->
    <hr/>
    <div class="form-group row">
        <div class="col-sm-4">
            <label for="ays_enable_restart_button">
                <?php echo __('Enable restart button',$this->plugin_name)?>
                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Show the restart button at the end of the quiz for restarting the quiz and pass it again.',$this->plugin_name)?>">
                    <i class="ays_fa ays_fa_info_circle"></i>
                </a>
            </label>
        </div>
        <div class="col-sm-8">
            <input type="checkbox" class="ays-enable-timer1" id="ays_enable_restart_button"
                   name="ays_enable_restart_button"
                   value="on" <?php echo ($enable_restart_button) ? 'checked' : '' ?>/>
        </div>
    </div> <!-- Enable restart button -->
    <hr/>
    <div class="form-group row ays_toggle_parent">
        <div class="col-sm-4">
            <label for="ays_questions_result_option">
                <?php echo __('Show question results on the results page',$this->plugin_name); ?>
                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Show all questions with right and wrong answers after quiz.',$this->plugin_name); ?>">
                    <i class="ays_fa ays_fa_info_circle"></i>
                </a>
            </label>
        </div>
        <div class="col-sm-1">
            <input type="checkbox" class="ays-enable-timer1 ays_toggle_checkbox" id="ays_questions_result_option" name="ays_enable_questions_result" value="on" <?php echo ($enable_questions_result) ? 'checked' : '' ?>/>
        </div>
        <div class="col-sm-7 ays_toggle_target ays_divider_left <?php echo $enable_questions_result ? '' : 'display_none'; ?>">
            <div class="form-group row">
                <div class="col-sm-4">
                    <label for="ays_hide_correct_answers">
                        <?php echo __('Hide correct answers',$this->plugin_name)?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('After enabling this option, the user whose chosen answer to the question is wrong will not see the right one.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-8">
                    <input type="checkbox" class="ays-enable-timer1" id="ays_hide_correct_answers" name="ays_hide_correct_answers" value="on" <?php echo ($hide_correct_answers) ? 'checked' : '' ?>/>
                </div>
            </div>
            <hr>
            <div class="form-group row">
                <div class="col-sm-4">
                    <label for="ays_quiz_show_wrong_answers_first">
                        <?php echo __('Show wrong answers first',$this->plugin_name); ?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Tick the checkbox if you want to show the wrongly answered questions by the particular user in the first place on the result page.',$this->plugin_name); ?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-8">
                    <input type="checkbox" class="ays-enable-timer1" id="ays_quiz_show_wrong_answers_first" name="ays_quiz_show_wrong_answers_first" value="on" <?php echo ($quiz_show_wrong_answers_first) ? 'checked' : ''; ?> />
                </div>
            </div>
            <hr>
            <div class="form-group row">
                <div class="col-sm-4">
                    <label for="ays_quiz_show_only_wrong_answers">
                        <?php echo __('Show only wrong answers',$this->plugin_name); ?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Tick this option if you want to see only the wrong answers on the quiz results page.',$this->plugin_name); ?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-8">
                    <input type="checkbox" class="ays-enable-timer1" id="ays_quiz_show_only_wrong_answers" name="ays_quiz_show_only_wrong_answers" value="on" <?php echo ($quiz_show_only_wrong_answers) ? 'checked' : ''; ?> />
                </div>
            </div>
            <hr>
            <div class="form-group row">
                <div class="col-sm-4">
                    <label for="ays_quiz_enable_results_toggle">
                        <?php echo __('Enable the Show/Hide toggle',$this->plugin_name); ?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('If you enable this option, a toggle will be displayed by which you can show/hide the results of the quiz questions on the Front-end.',$this->plugin_name); ?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-8">
                    <input type="checkbox" class="ays-enable-timer1" id="ays_quiz_enable_results_toggle" name="ays_quiz_enable_results_toggle" value="on" <?php echo ($quiz_enable_results_toggle) ? 'checked' : ''; ?> />
                </div>
            </div>
        </div>
    </div> <!-- Show all questions result in finish page -->
    <hr/>
    <div class="form-group row">
        <div class="col-sm-4">
            <label for="ays_average_statistical_option">
                <?php echo __('Show the statistical average',$this->plugin_name); ?>
                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Show average score according to all results of the quiz',$this->plugin_name); ?>">
                    <i class="ays_fa ays_fa_info_circle"></i>
                </a>
            </label>
        </div>
        <div class="col-sm-8">
            <input type="checkbox" class="ays-enable-timer1" id="ays_average_statistical_option"
                   name="ays_enable_average_statistical"
                   value="on" <?php echo (isset($options['enable_average_statistical']) && $options['enable_average_statistical'] == 'on') ? 'checked' : '' ?>/>
        </div>
    </div> <!-- Show the Average statistical -->
    <hr/>
    <div class="form-group row ays_toggle_parent">
        <div class="col-sm-4">
            <label for="ays_social_buttons">
                <?php echo __('Show the Social buttons',$this->plugin_name)?>
                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Display social buttons for sharing quiz page URL. LinkedIn, Facebook, Twitter.',$this->plugin_name)?>">
                    <i class="ays_fa ays_fa_info_circle"></i>
                </a>
            </label>
        </div>
        <div class="col-sm-1">
            <input type="checkbox" class="ays-enable-timer1 ays_toggle_checkbox" id="ays_social_buttons" name="ays_social_buttons" value="on" <?php echo ( $enable_social_buttons ) ? 'checked' : '' ?>/>
        </div>
        <div class="col-sm-7 ays_toggle_target ays_divider_left <?php echo $enable_social_buttons ? '' : 'display_none'; ?>">
           <div class="form-group row ays-quiz-result-message-vars-parent">
                <div class="col-sm-4">
                    <label>
                        <?php echo __('Heading for share buttons',$this->plugin_name)?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Text that will be displayed over share buttons.',$this->plugin_name); ?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-8">
                    <?php
                        echo $quiz_message_vars_html;
                        $content = $social_buttons_heading;
                        $editor_id = 'ays_social_buttons_heading';
                        $settings = array('editor_height' => $quiz_wp_editor_height, 'textarea_name' => 'ays_social_buttons_heading', 'editor_class' => 'ays-textarea', 'media_elements' => false);
                        wp_editor($content, $editor_id, $settings);
                    ?>
                </div>
            </div>
            <hr/>
            <div class="form-group row">
                <div class="col-sm-4">
                    <label for="ays_quiz_enable_linkedin_share_button">
                        <i class="ays_fa ays_fa_linkedin_square"></i>
                        <?php echo __('Linkedin button',$this->plugin_name)?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Display LinkedIn social button so that the users can share the page on which your quiz is posted.',$this->plugin_name); ?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-8">
                    <input type="checkbox" class="ays-enable-timer1" id="ays_quiz_enable_linkedin_share_button" name="ays_quiz_enable_linkedin_share_button" value="on" <?php echo ( $quiz_enable_linkedin_share_button ) ? 'checked' : ''; ?>/>
                </div>
            </div>
            <hr/>
            <div class="form-group row">
                <div class="col-sm-4">
                    <label for="ays_quiz_enable_facebook_share_button">
                        <i class="ays_fa ays_fa_facebook_square"></i>
                        <?php echo __('Facebook button',$this->plugin_name)?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Display Facebook social button so that the users can share the page on which your quiz is posted.',$this->plugin_name); ?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-8">
                    <input type="checkbox" class="ays-enable-timer1" id="ays_quiz_enable_facebook_share_button" name="ays_quiz_enable_facebook_share_button" value="on" <?php echo ( $quiz_enable_facebook_share_button ) ? 'checked' : ''; ?>/>
                </div>
            </div>
            <hr/>
            <div class="form-group row">
                <div class="col-sm-4">
                    <label for="ays_quiz_enable_twitter_share_button">
                        <i class="ays_fa ays_fa_twitter_square"></i>
                        <?php echo __('Twitter button',$this->plugin_name)?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Display Twitter social button so that the users can share the page on which your quiz is posted.',$this->plugin_name); ?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-8">
                    <input type="checkbox" class="ays-enable-timer1" id="ays_quiz_enable_twitter_share_button" name="ays_quiz_enable_twitter_share_button" value="on" <?php echo ( $quiz_enable_twitter_share_button ) ? 'checked' : ''; ?>/>
                </div>
            </div>
            <hr/>
            <div class="form-group row">
                <div class="col-sm-4">
                    <label for="ays_quiz_enable_vkontakte_share_button">
                        <i class="ays_fa ays_fa_vk"></i>
                        <?php echo __('VKontakte button',$this->plugin_name)?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Display VKontakte social button so that the users can share the page on which your quiz is posted.',$this->plugin_name); ?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-8">
                    <input type="checkbox" class="ays-enable-timer1" id="ays_quiz_enable_vkontakte_share_button" name="ays_quiz_enable_vkontakte_share_button" value="on" <?php echo ( $quiz_enable_vkontakte_share_button ) ? 'checked' : ''; ?>/>
                </div>
            </div>
        </div>
    </div> <!-- Show the Social buttons -->
    <hr/>
    <div class="form-group row ays_toggle_parent ays-quiz-result-message-vars-parent">
        <div class="col-sm-4">
            <label for="ays_enable_social_links">
                <?php echo __('Enable Social Media links',$this->plugin_name)?>
                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Display social media links at the end of the quiz to allow users to visit your pages in the Social media.',$this->plugin_name)?>">
                    <i class="ays_fa ays_fa_info_circle"></i>
                </a>
            </label>
        </div>
        <div class="col-sm-1">
            <input type="checkbox" class="ays-enable-timer1 ays_toggle_checkbox" id="ays_enable_social_links"
                   name="ays_enable_social_links"
                   value="on" <?php echo $enable_social_links ? 'checked' : '' ?>/>
        </div>
        <div class="col-sm-7 ays_toggle_target ays_divider_left <?php echo $enable_social_links ? '' : 'display_none' ?>">
            <div class="form-group row">
                <div class="col-sm-4">
                    <label>
                        <?php echo __('Heading for social media links',$this->plugin_name)?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Text that will be displayed over social media links.',$this->plugin_name); ?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-8">
                    <?php
                        echo $quiz_message_vars_html;
                        $content = $social_links_heading;
                        $editor_id = 'ays_social_links_heading';
                        $settings = array('editor_height' => $quiz_wp_editor_height, 'textarea_name' => 'ays_social_links_heading', 'editor_class' => 'ays-textarea', 'media_elements' => false);
                        wp_editor($content, $editor_id, $settings);
                    ?>
                </div>
            </div>
            <hr/>
            <div class="form-group row">
                <div class="col-sm-4">
                    <label for="ays_linkedin_link">
                        <i class="ays_fa ays_fa_linkedin_square"></i>
                        <?php echo __('Linkedin link',$this->plugin_name)?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Linkedin profile or page link for showing after quiz finish.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-8">
                    <input type="text" class="ays-text-input" id="ays_linkedin_link" name="ays_social_links[ays_linkedin_link]"
                        value="<?php echo $linkedin_link; ?>" />
                </div>
            </div>
            <hr/>
            <div class="form-group row">
                <div class="col-sm-4">
                    <label for="ays_facebook_link">
                        <i class="ays_fa ays_fa_facebook_square"></i>
                        <?php echo __('Facebook link',$this->plugin_name)?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Facebook profile or page link for showing after quiz finish.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-8">
                    <input type="text" class="ays-text-input" id="ays_facebook_link" name="ays_social_links[ays_facebook_link]"
                        value="<?php echo $facebook_link; ?>" />
                </div>
            </div>
            <hr/>
            <div class="form-group row">
                <div class="col-sm-4">
                    <label for="ays_twitter_link">
                        <i class="ays_fa ays_fa_twitter_square"></i>
                        <?php echo __('Twitter link',$this->plugin_name)?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Twitter profile or page link for showing after quiz finish.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-8">
                    <input type="text" class="ays-text-input" id="ays_twitter_link" name="ays_social_links[ays_twitter_link]"
                        value="<?php echo $twitter_link; ?>" />
                </div>
            </div>
            <hr/>
            <div class="form-group row">
                <div class="col-sm-4">
                    <label for="ays_vkontakte_link">
                        <i class="ays_fa ays_fa_vk"></i>
                        <?php echo __('VKontakte link',$this->plugin_name)?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('VKontakte profile or page link for showing after quiz finish.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-8">
                    <input type="text" class="ays-text-input" id="ays_vkontakte_link" name="ays_social_links[ays_vkontakte_link]"
                        value="<?php echo $vkontakte_link; ?>" />
                </div>
            </div>
            <hr/>
            <div class="form-group row">
                <div class="col-sm-4">
                    <label for="instagram_link">
                        <i class="ays_fa ays_fa_instagram_square"></i>
                        <?php echo __('Instagram link',$this->plugin_name)?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Instagram profile or page link for showing after quiz finish.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-8">
                    <input type="text" class="ays-text-input" id="instagram_link" name="ays_social_links[ays_instagram_link]"
                        value="<?php echo $instagram_link; ?>" />
                </div>
            </div>
            <hr/>
            <div class="form-group row">
                <div class="col-sm-4">
                    <label for="youtube_link">
                        <i class="ays_fa ays_fa_youtube_play"></i>
                        <?php echo __('Youtube link',$this->plugin_name)?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Youtube profile or page link for showing after quiz finish.',$this->plugin_name);?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-8">
                    <input type="text" class="ays-text-input" id="youtube_link" name="ays_social_links[ays_youtube_link]"
                        value="<?php echo $youtube_link; ?>" />
                </div>
            </div>
            <hr/>
            <div class="form-group row">
                <div class="col-sm-4">
                    <label for="behance_link">
                        <i class="ays_fa ays_fa_behance"></i>
                        <?php echo __('Behance link',$this->plugin_name)?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Behance profile or page link for showing after quiz finish.',$this->plugin_name);?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-8">
                    <input type="text" class="ays-text-input" id="behance_link" name="ays_social_links[ays_behance_link]"
                        value="<?php echo $behance_link; ?>" />
                </div>
            </div>
        </div>
    </div> <!-- Enable Social Media links -->
    <hr/>
    <div class="form-group row ays_toggle_parent">
        <div class="col-sm-4" style="padding-right: 0px;">
            <label for="ays_enable_quiz_rate">
                <?php echo __('Enable quiz assessment',$this->plugin_name); ?>
                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Comment and rate the quiz with up to 5 stars at the end of the quiz.',$this->plugin_name); ?>">
                    <i class="ays_fa ays_fa_info_circle"></i>
                </a>
            </label>
        </div>
        <div class="col-sm-1">
            <input type="checkbox" id="ays_enable_quiz_rate" class="ays_toggle_checkbox"
                   name="ays_enable_quiz_rate"
                   value="on" <?php echo ($enable_quiz_rate == 'on') ? 'checked' : ''; ?>/>
        </div>
        <div class="col-sm-7 ays_toggle_target ays_divider_left <?php echo ($enable_quiz_rate == 'on') ? '' : 'display_none' ?>">
            <div class="form-group row">
                <div class="col-sm-4" style="padding-right: 0px;">
                    <label for="ays_enable_rate_comments">
                        <?php echo __('Show the last 5 reviews',$this->plugin_name); ?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Show last 5 reviews after rating the quiz',$this->plugin_name); ?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-8">
                    <input type="checkbox" id="ays_enable_rate_comments"
                           name="ays_enable_rate_comments"
                           value="on" <?php echo ($enable_rate_comments == 'on') ? 'checked' : ''; ?>/>
                </div>
            </div>
            <hr>
            <div class="form-group row">
                <div class="col-sm-4">
                    <label for="ays_quiz_make_responses_anonymous">
                        <?php echo __('Make responses anonymous',$this->plugin_name); ?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Collect anonymous responses no matter the quiz taker is a logged-in user or guest.',$this->plugin_name); ?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-8">
                    <input type="checkbox" name="ays_quiz_make_responses_anonymous" id="ays_quiz_make_responses_anonymous"
                           <?php echo ($quiz_make_responses_anonymous) ? 'checked' : ''; ?>/>
                </div>
            </div>
            <hr>
            <div class="form-group row">
                <div class="col-sm-4">
                    <label for="ays_quiz_enable_user_cհoosing_anonymous_assessment">
                        <?php echo __("Enable users' anonymous assessment",$this->plugin_name); ?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('By ticking this option, the users can choose to leave a rating anonymously.',$this->plugin_name); ?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-8">
                    <input type="checkbox" name="ays_quiz_enable_user_cհoosing_anonymous_assessment" id="ays_quiz_enable_user_cհoosing_anonymous_assessment"
                           <?php echo ($quiz_enable_user_cհoosing_anonymous_assessment) ? 'checked' : ''; ?>/>
                </div>
            </div>
            <hr>
            <div class="form-group row">
                <div class="col-sm-4">
                    <label for="ays_quiz_make_all_review_link">
                        <?php echo __('Display all reviews button',$this->plugin_name); ?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Tick the option, and the quiz taker will have the opportunity to see all feedbacks written by others.',$this->plugin_name); ?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-8">
                    <input type="checkbox" name="ays_quiz_make_all_review_link" id="ays_quiz_make_all_review_link" <?php echo ($quiz_make_all_review_link) ? 'checked' : ''; ?>/>
                </div>
            </div>
            <hr>
            <div class="form-group row">
                <div class="col-sm-4">
                    <label for="ays_quiz_review_enable_comment_field">
                        <?php echo __('Enable Comment Field',$this->plugin_name); ?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('By disabling this option, the comment field will be hidden and you can only rate with stars. This option is enabled by default.',$this->plugin_name); ?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-8">
                    <input type="checkbox" name="ays_quiz_review_enable_comment_field" id="ays_quiz_review_enable_comment_field" <?php echo ($quiz_review_enable_comment_field) ? 'checked' : ''; ?>/>
                </div>
            </div>
            <hr>
            <div class="form-group row">
                <div class="col-sm-4">
                    <label for="ays_quiz_make_review_required">
                        <?php echo __('Make the review field required',$this->plugin_name); ?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo esc_attr( __("If this option is enabled, the users can't send a feedback without writing a review.",$this->plugin_name) ); ?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-8">
                    <input type="checkbox" name="ays_quiz_make_review_required" id="ays_quiz_make_review_required" <?php echo ($quiz_make_review_required) ? 'checked' : ''; ?>/>
                </div>
            </div>
            <hr/>
            <div class="form-group row">
                <div class="col-sm-4">
                    <label for="ays_quiz_review_placeholder_text">
                        <?php echo __('Placeholder text',$this->plugin_name); ?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo esc_attr( __('Write your custom placeholder for the Rating form.',$this->plugin_name) ); ?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-8">
                    <input type="text" class="ays-text-input" id="ays_quiz_review_placeholder_text" name="ays_quiz_review_placeholder_text" value="<?php echo $quiz_review_placeholder_text; ?>"/>
                </div>
            </div>
            <hr>
            <div class="form-group row">
                <div class="col-sm-4" style="padding-right: 0px;">
                    <label for="ays_rate_form_title">
                        <?php echo __('Rating form title',$this->plugin_name)?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Text which will notify user that he can submit a feedback',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-8">
                    <?php
                    $content = stripslashes(wpautop($rate_form_title));
                    $editor_id = 'ays_rate_form_title';
                    $settings = array('editor_height' => $quiz_wp_editor_height, 'textarea_name' => 'ays_rate_form_title', 'editor_class' => 'ays-textarea', 'media_elements' => false);
                    wp_editor($content, $editor_id, $settings);
                    ?>
                </div>
            </div>
            <hr>
            <div class="form-group row">
                <div class="col-sm-4" style="padding-right: 0px;">
                    <label for="ays_quiz_review_thank_you_message">
                        <?php echo __('Thank you message',$this->plugin_name)?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('By enabling this option, the text written in the editor is displayed when the user writes a review for the quiz.',$this->plugin_name); ?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-8">
                    <?php
                    $content = $quiz_review_thank_you_message;
                    $editor_id = 'ays_quiz_review_thank_you_message';
                    $settings = array('editor_height' => $quiz_wp_editor_height, 'textarea_name' => 'ays_quiz_review_thank_you_message', 'editor_class' => 'ays-textarea', 'media_elements' => false);
                    wp_editor($content, $editor_id, $settings);
                    ?>
                </div>
            </div>
            <hr>
            <div class="form-group row">
                <div class="col-sm-4" style="padding-right: 0px;">
                    <label for="ays_show_rate_after_rate">
                        <?php echo __('Show average of rating after the rate',$this->plugin_name)?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Show average of rating after the rate. It will show how many votes in total and average.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-8">
                    <input type="checkbox" id="ays_show_rate_after_rate"
                           name="ays_show_rate_after_rate"
                           value="on" <?php echo ($show_rate_after_rate == 'on') ? 'checked' : ''; ?>/>
                </div>
            </div>
        </div>
    </div> <!-- Enable Quiz assessment -->
    <hr/>
    <!--
    <div class="form-group row ays_toggle_parent">
        <div class="col-sm-4">
            <label for="ays_enable_negative_mark">
                <?php //echo __('Negative Mark',$this->plugin_name)?>
                <a class="ays_help" data-toggle="tooltip" title="<?php //echo __('Total correct marks, Negative marks in different columns in result sheet.',$this->plugin_name)?>">
                    <i class="ays_fa ays_fa_info_circle"></i>
                </a>
            </label>
        </div>
        <div class="col-sm-1">
            <input type="checkbox" class="ays-enable-timer1 ays_toggle_checkbox" id="ays_enable_negative_mark"
                   name="ays_enable_negative_mark"
                   value="on" <?php //echo ($enable_negative_mark) ? 'checked' : '' ?>/>
        </div>
        <div class="col-sm-7 ays_toggle_target ays_divider_left <?php //echo ($enable_negative_mark) ? '' : 'display_none' ?>">
            <div class="form-group row">
                <div class="col-sm-4">
                    <label for="ays_negative_mark_point">
                        <?php //echo __('Weight/Point',$this->plugin_name)?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php //echo __('Set the negative mark which you want to deduct from the total points of the user in case of one wrong selected answer.',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-8">
                    <input type="number" class="ays-text-input" id="ays_negative_mark_point" name="ays_negative_mark_point" value="<?php //echo $negative_mark_point; ?>" step=".01">
                </div>
            </div>
        </div>
    </div> Negative mark
    <hr/>
    -->
    <div class="form-group row">
        <div class="col-sm-4">
            <label>
                <?php echo __('Quiz loader icon',$this->plugin_name); ?>
                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Choose the design of the loader on the finish page after submitting. It will inherit the Quiz Text color from the Styles tab.',$this->plugin_name); ?>">
                    <i class="ays_fa ays_fa_info_circle"></i>
                </a>
            </label>
        </div>
        <div class="col-sm-8 ays_toggle_loader_parent">
            <label class="ays_quiz_loader">
                <input name="ays_quiz_loader" class="ays_toggle_loader_radio" data-flag="false" data-type="loader" type="radio" value="default" <?php echo ($quiz_loader == 'default') ? 'checked' : ''; ?>>
                <div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>
            </label>
            <label class="ays_quiz_loader">
                <input name="ays_quiz_loader" class="ays_toggle_loader_radio" data-flag="false" data-type="loader" type="radio" value="circle" <?php echo ($quiz_loader == 'circle') ? 'checked' : ''; ?>>
                <div class="lds-circle"></div>
            </label>
            <label class="ays_quiz_loader">
                <input name="ays_quiz_loader" class="ays_toggle_loader_radio" data-flag="false" data-type="loader" type="radio" value="dual_ring" <?php echo ($quiz_loader == 'dual_ring') ? 'checked' : ''; ?>>
                <div class="lds-dual-ring"></div>
            </label>
            <label class="ays_quiz_loader">
                <input name="ays_quiz_loader" class="ays_toggle_loader_radio" data-flag="false" data-type="loader" type="radio" value="facebook" <?php echo ($quiz_loader == 'facebook') ? 'checked' : ''; ?>>
                <div class="lds-facebook"><div></div><div></div><div></div></div>
            </label>
            <label class="ays_quiz_loader">
                <input name="ays_quiz_loader" class="ays_toggle_loader_radio" data-flag="false" data-type="loader" type="radio" value="hourglass" <?php echo ($quiz_loader == 'hourglass') ? 'checked' : ''; ?>>
                <div class="lds-hourglass"></div>
            </label>
            <label class="ays_quiz_loader">
                <input name="ays_quiz_loader" class="ays_toggle_loader_radio" data-flag="false" data-type="loader" type="radio" value="ripple" <?php echo ($quiz_loader == 'ripple') ? 'checked' : ''; ?>>
                <div class="lds-ripple"><div></div><div></div></div>
            </label>
            <label class="ays_quiz_loader">
                <input name="ays_quiz_loader" class="ays_toggle_loader_radio" data-flag="true" data-type="text" type="radio" value="text" <?php echo ($quiz_loader == 'text') ? 'checked' : ''; ?>>
                <div class="ays_quiz_loader_text">
                    <?php echo __( "Text" , $this->plugin_name ); ?>
                </div>
                <div class="ays_toggle_loader_target <?php echo ($quiz_loader == 'text') ? '' : 'display_none' ?>" data-type="text">
                    <input type="text" class="ays-text-input" data-type="text" id="ays_quiz_loader_text_value" name="ays_quiz_loader_text_value" value="<?php echo $quiz_loader_text_value; ?>">
                </div>
            </label>
            <label class="ays_quiz_loader">
                <input name="ays_quiz_loader" class="ays_toggle_loader_radio" data-flag="true" data-type="gif" type="radio" value="custom_gif" <?php echo ($quiz_loader == 'custom_gif') ? 'checked' : ''; ?>>
                <div class="ays_quiz_loader_custom_gif">
                    <?php echo __( "Gif" , $this->plugin_name ); ?>
                </div>
                <div class="ays_toggle_loader_target ays-image-wrap <?php echo ($quiz_loader == 'custom_gif') ? '' : 'display_none' ?>" data-type="gif">
                    <a href="javascript:void(0)" style="<?php echo ($quiz_loader_custom_gif == '') ? 'display:inline-block' : 'display:none'; ?>" class="ays-add-image add_quiz_loader_custom_gif"><?php echo __('Add Gif', $this->plugin_name); ?></a>
                    <input type="hidden" class="ays-image-path" id="ays_quiz_loader_custom_gif" name="ays_quiz_loader_custom_gif" value="<?php echo $quiz_loader_custom_gif; ?>"/>
                    <div class="ays-image-container ays-quiz-loader-custom-gif-container" style="<?php echo ($quiz_loader_custom_gif == '') ? 'display:none' : 'display:block'; ?>">
                        <span class="ays-edit-img ays-edit-quiz-loader-custom-gif">
                            <i class="ays_fa ays_fa_pencil_square_o"></i>
                        </span>
                        <span class="ays-remove-img ays-remove-quiz-loader-custom-gif"></span>
                        <img  src="<?php echo $quiz_loader_custom_gif; ?>" class="img_quiz_loader_custom_gif"/>
                    </div>
                </div>
                <div class="ays_toggle_loader_target ays_gif_loader_width_container <?php echo ($quiz_loader == 'custom_gif') ? 'display_flex' : 'display_none'; ?>" data-type="gif" style="margin: 10px;">
                    <div>
                        <label for='ays_quiz_loader_custom_gif_width'>
                            <?php echo __('Width (px)', $this->plugin_name); ?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Custom Gif width in pixels. It accepts only numeric values.',$this->plugin_name); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div style="margin-left: 5px;">
                        <input type="number" class="ays-text-input" id='ays_quiz_loader_custom_gif_width' name='ays_quiz_loader_custom_gif_width' value="<?php echo ( $quiz_loader_custom_gif_width ); ?>"/>
                    </div>
                </div>
            </label>
        </div>
    </div> <!-- Select quiz loader -->
    <hr/>
    <div class="form-group row ays-quiz-result-message-vars-parent">
        <div class="col-sm-4">
            <label for="ays_final_result_text">
                <?php echo __('Result message',$this->plugin_name)?>
                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('The message will be displayed after submitting the quiz. You can use Variables (General Settings) to insert user data here. If you want to show results with points or with the number of correct answers, you need to use correspondent variables and enable the “Hide score” option.',$this->plugin_name)?>">
                    <i class="ays_fa ays_fa_info_circle"></i>
                </a>
            </label>
            <p class="ays_quiz_small_hint_text_for_message_variables">
                <span><?php echo __( "To see all Message Variables " , $this->plugin_name ); ?></span>
                <a href="?page=quiz-maker-settings&ays_quiz_tab=tab4" target="_blank"><?php echo __( "click here" , $this->plugin_name ); ?></a>
            </p>
        </div>
        <div class="col-sm-8">
            <?php
            echo $quiz_message_vars_html;
            $content = stripslashes(wpautop((isset($options['final_result_text'])) ? $options['final_result_text'] : ''));
            $editor_id = 'ays_final_result_text';
            $settings = array('editor_height' => $quiz_wp_editor_height, 'textarea_name' => 'ays_final_result_text', 'editor_class' => 'ays-textarea', 'media_elements' => false);
            wp_editor($content, $editor_id, $settings);
            ?>
        </div>
    </div> <!-- Result message -->
    <hr/>
    <div class="form-group row">
        <div class="col-sm-4">
            <label class="form-check-label" for="ays-pass-score">
                <?php echo __("Pass Score (%)", $this->plugin_name) ?>
                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Set the minimum score to pass the quiz in percentage. Please note to give a value to it above 0, otherwise, the Quiz pass message and Quiz fail message options will not work.',$this->plugin_name); ?>">
                    <i class="ays_fa ays_fa_info_circle"></i>
                </a>
            </label>
        </div>
        <div class="col-sm-8">
            <div class="form-group row">
                <div class="col-sm-12">
                    <label class="ays_quiz_loader">
                        <input type="radio" class="ays-enable-timer1" name="ays_quiz_pass_score_type" value="percentage" <?php echo ($quiz_pass_score_type == 'percentage') ? 'checked' : '' ?>/>
                        <span><?php echo __( "Percentage", $this->plugin_name ); ?></span>
                    </label>
                    <label class="ays_quiz_loader">
                        <input type="radio" class="ays-enable-timer1" name="ays_quiz_pass_score_type" value="point" <?php echo ($quiz_pass_score_type == 'point') ? 'checked' : ''; ?>/>
                        <span><?php echo __( "Points", $this->plugin_name ); ?></span>
                    </label>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-12">
                    <input type="number" class="ays-text-input" id='ays-pass-score' name='ays_pass_score' value="<?php echo $pass_score; ?>"/>
                </div>
            </div>
            <hr/>
            <div class="form-group row ays-quiz-result-message-vars-parent">
                <div class="col-sm-4">
                    <label class="form-check-label" for="ays_pass_score_message">
                        <?php echo __("Quiz pass message", $this->plugin_name) ?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('The message in the case of the user passes the quiz',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                    <p class="ays_quiz_small_hint_text_for_message_variables">
                        <span><?php echo __( "To see all Message Variables " , $this->plugin_name ); ?></span>
                        <a href="?page=quiz-maker-settings&ays_quiz_tab=tab4" target="_blank"><?php echo __( "click here" , $this->plugin_name ); ?></a>
                    </p>
                </div>
                <div class="col-sm-8">
                    <div class="editor">
                        <?php
                        echo $quiz_message_vars_html;
                        $editor_id = 'ays_pass_score_message';
                        $settings  = array(
                            'editor_height'  => $quiz_wp_editor_height,
                            'textarea_name'  => 'ays_pass_score_message',
                            'editor_class'   => 'ays-textarea',
                            'media_elements' => false
                        );
                        wp_editor($pass_score_message, $editor_id, $settings);
                        ?>
                    </div>
                </div>
            </div> <!-- Pass message -->
            <hr/>
            <div class="form-group row ays-quiz-result-message-vars-parent">
                <div class="col-sm-4">
                    <label class="form-check-label" for="ays_fail_score_message">
                        <?php echo __("Quiz fail message", $this->plugin_name) ?>
                        <a class="ays_help" data-toggle="tooltip" title="<?php echo __('The message in the case of the user fails the quiz',$this->plugin_name)?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                    <p class="ays_quiz_small_hint_text_for_message_variables">
                        <span><?php echo __( "To see all Message Variables " , $this->plugin_name ); ?></span>
                        <a href="?page=quiz-maker-settings&ays_quiz_tab=tab4" target="_blank"><?php echo __( "click here" , $this->plugin_name ); ?></a>
                    </p>
                </div>
                <div class="col-sm-8">
                    <div class="editor">
                        <?php
                        echo $quiz_message_vars_html;
                        $editor_id = 'ays_fail_score_message';
                        $settings  = array(
                            'editor_height'  => $quiz_wp_editor_height,
                            'textarea_name'  => 'ays_fail_score_message',
                            'editor_class'   => 'ays-textarea',
                            'media_elements' => false
                        );
                        wp_editor($fail_score_message, $editor_id, $settings);
                        ?>
                    </div>
                </div>
            </div> <!-- Fail message -->
        </div>
    </div> <!-- Pass score -->
    <hr/>
    <div class="form-group row">
        <div class="col-sm-4">
            <label for="ays_disable_store_data">
                <?php echo __('Disable data storing in database',$this->plugin_name)?>
                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Disable data storing in the database, and results will not be displayed on the \'Results\' page. (not recommended)',$this->plugin_name)?>">
                    <i class="ays_fa ays_fa_info_circle"></i>
                </a>
            </label>
        </div>
        <div class="col-sm-8">
            <input type="checkbox" class="ays-enable-timer1" id="ays_disable_store_data"
                   name="ays_disable_store_data"
                   value="on" <?php echo $disable_store_data ? 'checked' : '' ?>/>
        </div>
    </div> <!-- Disable data storing in database -->
    <hr/>
    <div class="form-group row">
        <div class="col-sm-4">
            <label for="ays_checkbox_score_by">
                <?php echo __('Strong calculation of checkbox answers score',$this->plugin_name)?>
                <a class="ays_help" data-html="true" data-toggle="tooltip" title="<?php echo "<ul style='list-style-type:disc;padding-left: 20px;'><li>".__("If this option is enabled then our system will calculate checkbox's answer as 1 or 0.",$this->plugin_name). "</li><li>".__("If the user has one wrong answer he/she will get 0 points.",$this->plugin_name). "</li><li>".__("If the option is disabled, the system will calculate the answer as a percentage.",$this->plugin_name). "</li><li>".__("It means if you answer 2 of 3 correct answers then you will get 2/3 points.",$this->plugin_name)."</li></ul>"; ?>">
                    <i class="ays_fa ays_fa_info_circle"></i>
                </a>
            </label>
        </div>
        <div class="col-sm-8">
            <input type="checkbox" class="ays-enable-timer1" id="ays_checkbox_score_by"
                   name="ays_checkbox_score_by"
                   value="on" <?php echo $checkbox_score_by ? 'checked' : '' ?>/>
        </div>
    </div> <!-- Strong calculation of checkbox answers score -->
    <hr/>
    <div class="form-group row">
        <div class="col-sm-4">
            <label for="ays_show_interval_message">
                <?php echo __('Show interval message',$this->plugin_name)?>
                <a class="ays_help" data-html="true" data-toggle="tooltip" title="<?php echo __("Show an interval message after quiz completion in the finish page.",$this->plugin_name); ?>">
                    <i class="ays_fa ays_fa_info_circle"></i>
                </a>
            </label>
        </div>
        <div class="col-sm-8">
            <input type="checkbox" class="ays-enable-timer1" id="ays_show_interval_message"
                   name="ays_show_interval_message"
                   value="on" <?php echo $show_interval_message ? 'checked' : '' ?>/>
        </div>
    </div> <!-- Show interval message -->
    <hr/>
    <div class="ays-quiz-heading-box ays-quiz-unset-float">
        <div class="ays-quiz-wordpress-user-manual-box ays-quiz-wordpress-text-align">
            <a href="https://www.youtube.com/watch?v=PQSOjFUG1Fg" target="_blank">
                <?php echo __("How intervals feature works - video", $this->plugin_name); ?>
            </a>
        </div>
    </div>
    <div class='form-group row ays-field-dashboard ays_toggle_parent'>
        <div class="col-sm-4">
            <label for="ays-answers-table"><?php echo __('Intervals', $this->plugin_name); ?>
                <a href="javascript:void(0)" class="ays-add-interval">
                    <i class="ays_fa ays_fa_plus_square" aria-hidden="true"></i>
                </a>
                <a class="ays_help" style="font-size:15px;" data-toggle="tooltip" title="<?php echo __('Set different messages based on the user’s score. The message will be displayed on the result page of the quiz.',$this->plugin_name)?>">
                    <i class="ays_fa ays_fa_info_circle"></i>
                </a>
            </label>
        </div>
        <div class="col-sm-8">
            <label class="ays_quiz_loader">
                <input type="radio" class="ays-enable-timer1 ays_table_by ays_intervals_display_by" data-flag="false" name="ays_display_score_by" value="by_percentage" <?php echo ($display_score_by == 'by_percentage') ? 'checked' : ''; ?>>
                <span><?php echo __( "By percentage", $this->plugin_name ); ?></span>
            </label>
            <label class="ays_quiz_loader">
                <input type="radio" class="ays-enable-timer1 ays_table_by ays_intervals_display_by" data-flag="false" name="ays_display_score_by" value="by_points" <?php echo ($display_score_by == 'by_points') ? 'checked' : ''; ?>>
                <span><?php echo __( "By points", $this->plugin_name ); ?></span>
            </label>
            <label class="ays_quiz_loader">
                <input type="radio" class="ays-enable-timer1 ays_table_by ays_intervals_display_by" data-flag="true" name="ays_display_score_by" value="by_keywords" <?php echo ($display_score_by == 'by_keywords') ? 'checked' : ''; ?>>
                <span><?php echo __( "By keywords", $this->plugin_name ); ?></span>
            </label>
            <a class="ays_help" style="font-size:15px;" data-toggle="tooltip" data-html="true"
                title="<?php
                    echo __('Choose your preferred method of calculation.',$this->plugin_name) .
                    "<ul style='list-style-type: circle;padding-left: 20px;'>".
                        "<li>". __('By percentage - If this option is enabled, you need to assign values to Min and Max fields by percentage and write a correspondent message and attach an image for each interval separately. You need to cover the 0-100 range with as many intervals as you want.',$this->plugin_name) ."</li>".
                        "<li>". __('By points - If this option is enabled, you need to assign values to Min and Max fields by points and write a correspondent message and attach an image for each interval separately. There is no limitation to that.',$this->plugin_name) ."</li>".
                        "<li>". __('By keywords - If this option is enabled, you need to select the keywords, which you have already assigned to your answers and write a correspondent message and attach an image for each interval separately. It will be calculated based on the majority of the selected answers of the user.',$this->plugin_name) ."</li>".
                    "</ul>";
                ?>">
                <i class="ays_fa ays_fa_info_circle"></i>
            </a>
        </div>
        <div class="col-sm-12 ays_toggle_target <?php echo ($display_score_by == 'by_keywords') ? '' : 'display_none' ?>">
            <hr>
            <div class="form-group row">
                <div class="col-sm-4">
                    <label for="ays_apply_points_to_keywords">
                        <?php echo __('Apply points to keywords',$this->plugin_name)?>
                        <a class="ays_help" data-html="true" data-toggle="tooltip" title="<?php echo __("Tick the checkbox and the system will take the value provided in the Weights/Points column for the certain keyword. For instance, you can give 2 values to the A keyword for a certain answer instead of one default value. In the end, the system will sum up the points of each keyword and will show the keyword-based message which got the highest total points.",$this->plugin_name); ?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-8">
                    <input type="checkbox" class="ays-enable-timer1" id="ays_apply_points_to_keywords"
                           name="ays_apply_points_to_keywords"
                           value="on" <?php echo $apply_points_to_keywords ? 'checked' : '' ?>/>
                </div>
            </div> <!-- Show interval message -->
            <hr>
            <div class="form-group row">
                <div class="col-sm-4">
                    <label for="ays_quiz_equal_keywords_text">
                        <?php echo __('Equal keywords text',$this->plugin_name)?>
                        <a class="ays_help" data-html="true" data-toggle="tooltip" title="<?php echo __("Enable this option, and the text written in the input will be displayed in case the user got keywords with equal points.",$this->plugin_name); ?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-8">
                    <?php
                        $content = $quiz_equal_keywords_text;
                        $editor_id = 'ays_quiz_equal_keywords_text';
                        $settings = array('editor_height' => $quiz_wp_editor_height, 'textarea_name' => 'ays_quiz_equal_keywords_text', 'editor_class' => 'ays-textarea', 'media_elements' => false);
                        wp_editor($content, $editor_id, $settings);
                    ?>
                </div>
            </div> <!-- Show interval message -->
        </div>
    </div>
    <div class='ays-field-dashboard ays-table-wrap'>
        <style>
            #woo-icon {
                display: inline-block;
                margin-right: 5px;
            }
            #woo-icon::before {
                font-family: WooCommerce!important;
                content: '\e03d';
                font-size: 18px;
                line-height: 1;
            }
        </style>
        <table class="ays-intervals-table <?php echo $wc_for_js; ?>">
            <thead>
            <tr class="ui-state-default">
                <th><?php echo __('Ordering', $this->plugin_name); ?></th>
                <th class="ays_interval_min_row <?php echo ($display_score_by == 'by_keywords') ? 'display_none' : ''; ?>"><?php echo __('Min', $this->plugin_name); ?></th>
                <th class="ays_interval_max_row <?php echo ($display_score_by == 'by_keywords') ? 'display_none' : ''; ?>"><?php echo __('Max', $this->plugin_name); ?></th>
                <th class="ays_keywords_row <?php echo ($display_score_by == 'by_keywords') ? '' : 'display_none'; ?>"><?php echo __('Keyword', $this->plugin_name); ?></th>
                <th><?php echo __('Text', $this->plugin_name); ?></th>
                <?php if ($quiz_intervals_wc): ?>
                <th><span id='woo-icon'></span><?php echo __('WooCommerce Product', $this->plugin_name); ?></th>
                <?php endif; ?>
                <th><?php echo __('Image', $this->plugin_name); ?></th>
                <th class="ays_actions_row"><?php echo __('Actions', $this->plugin_name); ?></th>
            </tr>
            </thead>
<!--            <tbody>-->
            <?php
                $woo_selected_products = array();
                foreach ($quiz_intervals as $key => $quiz_interval) {
                    $className = "";
                    if (($key + 1) % 2 == 0) {
                        $className = "even";
                    }

                    $quiz_interval_text  = __( 'Add', $this->plugin_name );
                    $quiz_interval_index = $key + 1;

                    if (isset($quiz_interval['interval_min']) && !empty($quiz_interval['interval_max']) || isset($quiz_interval['interval_keyword'])) {
                        ?>
                        <tbody>
                            <tr class="ays-interval-row ui-state-default <?php echo $className; ?>">
                            <td class="ays-sort">
                                <i class="ays_fa ays_fa_arrows" aria-hidden="true"></i>
                            </td>
                            <td class="ays_interval_min_row <?php echo ($display_score_by == 'by_keywords') ? 'display_none' : ''; ?>">
                                <input type="number" name="interval_min[]"
                                       value="<?php echo $quiz_interval['interval_min'] ?>" class="interval_min <?php echo ($display_score_by != 'by_percentage') ? 'ays_point_by' : ''; ?>">
                            </td>
                            <td class="ays_interval_max_row <?php echo ($display_score_by == 'by_keywords') ? 'display_none' : ''; ?>">
                                <input type="number" name="interval_max[]"
                                       value="<?php echo $quiz_interval['interval_max'] ?>" class="interval_max <?php echo ($display_score_by != 'by_percentage') ? 'ays_point_by' : ''; ?>">
                            </td>
                            <td class="ays_keywords_row <?php echo ($display_score_by == 'by_keywords') ? '' : 'display_none'; ?>">
                                <select name="interval_keyword[]" class="ays_quiz_keywords">
                                <?php
                                    $keyword_content = '';
                                    foreach ($keyword_arr as $key_arr => $answer_keyword) {
                                        $selected = '';
                                        if(isset($quiz_interval['interval_keyword']) && $quiz_interval['interval_keyword'] == $answer_keyword){
                                            $selected = 'selected';
                                        }
                                        $keyword_content .= '<option value="'.$answer_keyword.'" '. $selected .'>'.$answer_keyword.'</option>';
                                    }
                                    echo $keyword_content;
                                ?>
                                </select>
                            </td>
                            <td>
                                <textarea type="text" name="interval_text[]" class="interval_text"><?php echo stripslashes(htmlentities($quiz_interval['interval_text'])) ?></textarea>
                            </td>
                            <?php if ($quiz_intervals_wc): ?>
                            <?php
                                $selected_product = "";
                                $product_ids = array();
                                if(isset($quiz_interval['interval_wproduct'])){
                                    $prod_id = $quiz_interval['interval_wproduct'];
                                    $woo_selected_products[] = $prod_id;

                                    $product_ids[$key] = isset($prod_id) && $prod_id != '' ? explode(',' , $prod_id) : array();
                                    if(!empty($product_ids)){
                                        $product = $this->ays_get_woocommerce_product( $product_ids );
                                    }

                                    if(!empty($product)){
                                        foreach($product as $_key => $_value){
                                            $selected_product .= "<option selected data-nkar='". $_value->image ."' value='". $_value->ID ."'>". $_value->post_title ."</option>";
                                        }
                                    }
                                }
                                ?>
                                <td class="ays_wproducts_row">
                                    <select name="interval_wproduct[<?php echo $key; ?>][]" class="interval_wproduct" multiple="multiple">
                                        <option></option>
                                        <?php echo $selected_product; ?>
                                    </select>
                                </td>
                            <?php endif; ?>
                            <td class="ays-interval-image-td">
                                <label class='ays-label' for='ays-answer'>
                                    <a href="javascript:void(0)" class="add-answer-image add-interval-image" <?php echo (is_null($quiz_interval['interval_image']) || $quiz_interval['interval_image'] == '') ? "style=display:block;" : "style=display:none" ?>>
                                        <?php echo $quiz_interval_text; ?>
                                    </a>
                                </label>
                                <div class="ays-answer-image-container ays-interval-image-container" <?php echo (is_null($quiz_interval['interval_image']) || $quiz_interval['interval_image'] == '') ? "style=display:none; " : "style=display:block" ?>>
                                    <span class="ays-remove-answer-img"></span>
                                    <img src="<?php echo $quiz_interval['interval_image']; ?>" class="ays-answer-img"
                                         style="width: 100%;"/>
                                    <input type="hidden" name="interval_image[]" class="ays-answer-image"
                                           value="<?php echo $quiz_interval['interval_image']; ?>"/>
                                </div>
                            </td>
                            <td class="ays_actions_row">
                                <a href="javascript:void(0)" class="ays-more-interval"
                                   data-id="<?php echo $key; ?>"   title="<?php echo __('More',$this->plugin_name)?>">
                                    <i class="ays_fa ays_fa_angle_down" aria-hidden="true"></i>
                                </a>
                                <a href="javascript:void(0)" class="ays-more-interval"
                                   data-id="<?php echo $key; ?>"   >
                                    <i class="ays_fa ays_fa_angle_up" aria-hidden="true"></i>
                                </a>
                                <a href="javascript:void(0)" class="ays-delete-interval"
                                   data-id="<?php echo $key; ?>" title="<?php echo __('Delete',$this->plugin_name)?>">
                                    <i class="ays_fa ays_fa_minus_square" aria-hidden="true"></i>
                                </a>
                            </td>
                            </tr>
                            <tr class="ays-interval-hidden-row <?php echo $className; ?>" data-expanded="false">
                                <td colspan="8" class="hiddenRow ays_interval_redirect_td">
                                    <p class="ays-subtitle"><?php echo __('Redirect',$this->plugin_name)?></p>
                                    <hr>
                                    <div class="form-group row ays_interval_redirect_url_container">
                                        <div class="col-sm-3">
                                            <label for="ays_interval_redirect_url_<?php echo $quiz_interval_index; ?>">
                                                <?php echo __('Redirect URL',$this->plugin_name)?>
                                                <!-- <a class="ays_help" data-toggle="tooltip" title="<?php echo __('The URL for redirecting after the user submits the form.',$this->plugin_name)?>">
                                                    <i class="ays_fa ays_fa_info_circle"></i>
                                                </a> -->
                                            </label>
                                        </div>
                                        <div class="col-sm-9">
                                            <input type="text" class="ays-text-input" id="ays_interval_redirect_url_<?php echo $quiz_interval_index; ?>"
                                                name="interval_redirect_url[]"
                                                value="<?php echo isset( $quiz_interval['interval_redirect_url'] ) && $quiz_interval['interval_redirect_url'] != '' ? $quiz_interval['interval_redirect_url'] : ''; ?>"/>
                                        </div>
                                    </div>
                                    <hr/>
                                    <div class="form-group row ays_interval_redirect_delay_container ">
                                        <div class="col-sm-3">
                                            <label for="ays_interval_redirect_delay_<?php echo $quiz_interval_index; ?>">
                                                <?php echo __('Redirect delay (sec)', $this->plugin_name)?>
                                               <!--  <a class="ays_help" data-toggle="tooltip" title="<?php echo __('The redirection delay in seconds after the user submits the form. Value should be greater than 0.',$this->plugin_name)?>">
                                                    <i class="ays_fa ays_fa_info_circle"></i>
                                                </a> -->
                                            </label>
                                        </div>
                                        <div class="col-sm-9">
                                            <input type="number" class="ays-text-input" id="ays_interval_redirect_delay_<?php echo $quiz_interval_index; ?>"
                                                name="interval_redirect_delay[]"
                                                value="<?php echo isset( $quiz_interval['interval_redirect_delay'] ) && $quiz_interval['interval_redirect_delay'] != '' ? $quiz_interval['interval_redirect_delay'] : ''; ?>"/>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        <?php
                    } else {
                        $className = "";
                        if (($key + 1) % 2 == 0) {
                            $className = "even";
                        }
                        ?>
                        <tbody>
                            <tr class="ays-interval-row ui-state-default <?php echo $className; ?>">
                                <td class="ays-sort">
                                    <i class="ays_fa ays_fa_arrows" aria-hidden="true"></i>
                                </td>
                                <td class="ays_interval_min_row">
                                    <input type="number" name="interval_min[]" value="" class="interval_min">
                                </td>
                                <td class="ays_interval_max_row">
                                    <input type="number" name="interval_max[]" value="" class="interval_max">
                                </td>
                                <td>
                                    <textarea type="text" name="interval_text[]" class="interval__text"></textarea>
                                </td>
                                <?php if ($quiz_intervals_wc): ?>
                                    <td>
                                        <select name="interval_wproduct[<?php echo $key?>][]" class="interval_wproduct" multiple="multiple">
                                            <option></option>
                                        </select>
                                    </td>
                                <?php endif; ?>
                                <td class="ays-interval-image-td">
                                    <label class='ays-label' for='ays-answer'>
                                        <a href="javascript:void(0)" class="add-answer-image add-interval-image" style=display:block;>
                                            <?php echo $quiz_interval_text; ?>
                                        </a>
                                    </label>
                                    <div class="ays-answer-image-container ays-interval-image-container"
                                         style=display:none;>
                                        <span class="ays-remove-answer-img"></span>
                                        <img src="" class="ays-answer-img" style="width: 100%;"/>
                                        <input type="hidden" name="interval_image[]" class="ays-answer-image" value=""/>
                                    </div>
                                </td>
                                <td class="ays_actions_row">
                                    <a href="javascript:void(0)" class="ays-more-interval"
                                       data-id="<?php echo $key; ?>"   title="<?php echo __('More',$this->plugin_name)?>">
                                        <i class="ays_fa ays_fa_angle_down" aria-hidden="true"></i>
                                    </a>
                                    <a href="javascript:void(0)" class="ays-more-interval"
                                       data-id="<?php echo $key; ?>"   >
                                        <i class="ays_fa ays_fa_angle_up" aria-hidden="true"></i>
                                    </a>
                                    <a href="javascript:void(0)" class="ays-delete-interval"
                                       data-id="<?php echo $key; ?>">
                                        <i class="ays_fa ays_fa_minus_square" aria-hidden="true"></i>
                                    </a>
                                </td>
                            </tr>
                            <tr class="ays-interval-hidden-row <?php echo $className; ?>" data-expanded="false">
                                <td colspan="8" class="hiddenRow ays_interval_redirect_td">
                                    <p class="ays-subtitle"><?php echo __('Redirect',$this->plugin_name)?></p>
                                    <hr>
                                    <div class="form-group row ays_interval_redirect_url_container">
                                        <div class="col-sm-3">
                                            <label for="ays_interval_redirect_url_<?php echo $quiz_interval_index; ?>">
                                                <?php echo __('Redirect URL',$this->plugin_name)?>
                                                <!-- <a class="ays_help" data-toggle="tooltip" title="<?php echo __('The URL for redirecting after the user submits the form.',$this->plugin_name)?>">
                                                    <i class="ays_fa ays_fa_info_circle"></i>
                                                </a> -->
                                            </label>
                                        </div>
                                        <div class="col-sm-9">
                                            <input type="text" class="ays-text-input" id="ays_interval_redirect_url_<?php echo $quiz_interval_index; ?>"
                                                name="interval_redirect_url[]"
                                                value=""/>
                                        </div>
                                    </div>
                                    <hr/>
                                    <div class="form-group row ays_interval_redirect_delay_container ">
                                        <div class="col-sm-3">
                                            <label for="ays_interval_redirect_delay_<?php echo $quiz_interval_index; ?>">
                                                <?php echo __('Redirect delay (sec)', $this->plugin_name)?>
                                               <!--  <a class="ays_help" data-toggle="tooltip" title="<?php echo __('The redirection delay in seconds after the user submits the form. Value should be greater than 0.',$this->plugin_name)?>">
                                                    <i class="ays_fa ays_fa_info_circle"></i>
                                                </a> -->
                                            </label>
                                        </div>
                                        <div class="col-sm-9">
                                            <input type="number" class="ays-text-input" id="ays_interval_redirect_delay_<?php echo $quiz_interval_index; ?>"
                                                name="interval_redirect_delay[]"
                                                value=""/>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        <?php
                    }
                }
            ?>

<!--            </tbody>-->
        </table>
        <input type="hidden" id="ays_woo_selected_prods" value="<?php echo implode(",", $woo_selected_products); ?>">
        <input type="hidden" id="ays_quiz_interval_redirect_count" value="<?php echo count( $quiz_intervals ); ?>">
        <div class="form-group row" style="margin:0;">
            <div class="col-sm-12">
                <label for="ays-answers-table"><?php echo __('Intervals', $this->plugin_name); ?>
                    <a href="javascript:void(0)" class="ays-add-interval">
                        <i class="ays_fa ays_fa_plus_square" aria-hidden="true"></i>
                    </a>
                </label>
            </div>
        </div>
    </div> <!-- Intervals -->
    <div class="ays-quiz-heading-box ays-quiz-unset-float ays-quiz-unset-margin">
        <div class="ays-quiz-wordpress-user-manual-box ays-quiz-wordpress-text-align">
            <a href="https://www.youtube.com/watch?v=lUbLHe9mEZ0" target="_blank">
                <?php echo __("How to create personality quiz - video", $this->plugin_name); ?>
            </a>
        </div>
    </div>
    <?php
    if( in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))) ) {
    ?>
        <div class="ays-quiz-heading-box ays-quiz-unset-float ays-quiz-unset-margin">
            <div class="ays-quiz-wordpress-user-manual-box ays-quiz-wordpress-text-align">
                <a href="https://www.youtube.com/watch?v=BeYNME9TZsQ" target="_blank">
                    <?php echo __("How to create WooCommerce quiz - video", $this->plugin_name); ?>
                </a>
            </div>
        </div>
    <?php
    }
    ?>
    <hr/>
    <!-- Top Keywords -->
    <div class="form-group  ays_toggle_parent">
        <div class="row">
            <div class="col-sm-4">
                <label for="ays_enable_top_keywords">
                    <?php echo __('Assign message to each keyword',$this->plugin_name); ?>
                    <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Show the question results based on keywords on the resultes page with specified texts for each keyword.',$this->plugin_name); ?>">
                        <i class="ays_fa ays_fa_info_circle"></i>
                    </a>
                </label>
                <p class="ays_quiz_small_hint_text_for_message_variables">
                    <span><?php echo __( "To see all Message Variables " , $this->plugin_name ); ?></span>
                    <a href="?page=quiz-maker-settings&ays_quiz_tab=tab4" target="_blank"><?php echo __( "click here" , $this->plugin_name ); ?></a>
                </p>
            </div>
            <div class="col-sm-1">
                <input type="checkbox" class="ays-enable-timer1 ays_toggle_checkbox" id="ays_enable_top_keywords" name="ays_enable_top_keywords" value="on" <?php echo ($enable_top_keywords) ? 'checked' : '' ?>/>
            </div>
        </div>

        <div class="form-group row ays_toggle_target <?php echo $enable_top_keywords ? '' : 'display_none' ?>">
            <div class="col-sm-4">
            </div>
            <div class="col-sm-8">
                <p class="vmessage">
                    <strong>
                        <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%top_keywords_count_{count}%%" />
                    </strong>
                </p>
                <p class="vmessage">
                    <strong>
                        <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="%%top_keywords_percentage_{count}%%" />
                    </strong>
                </p>
            </div>
        </div>
        <div class="ays_toggle_target <?php echo $enable_top_keywords ? '' : 'display_none' ?>">
            <div class="col-sm-4">
                <label for="ays-answers-table"><?php echo __('Top Keywords', $this->plugin_name); ?>
                    <a href="javascript:void(0)" class="ays-add-top-keyword">
                        <i class="ays_fa ays_fa_plus_square" aria-hidden="true"></i>
                    </a>
                    <a class="ays_help" style="font-size:15px;" data-toggle="tooltip" title="<?php echo __('Set different messages based on the user’s score. The message will be displayed on the result page of the quiz.',$this->plugin_name)?>">
                        <i class="ays_fa ays_fa_info_circle"></i>
                    </a>
                </label>
            </div>
            <div class='ays-field-dashboard ays-table-wrap'>

                <table class="ays-top-keywords-table">
                    <thead>
                    <tr class="ui-state-default">
                        <th><?php echo __('Ordering', $this->plugin_name); ?></th>
                        <th class="ays_top_keywords_row"><?php echo __('Keyword', $this->plugin_name); ?></th>
                        <th><?php echo __('Text', $this->plugin_name); ?></th>
                        <th><?php echo __('Actions', $this->plugin_name); ?></th>
                    </tr>
                    </thead>
                    <?php
                        foreach ($quiz_top_keywords as $key => $quiz_top_keyword) {
                            $className = "";
                            if (($key + 1) % 2 == 0) {
                                $className = "even";
                            }
                            $quiz_interval_text = 'Add';

                            if (isset($quiz_top_keyword['assign_top_keyword'])) {
                                ?>
                                <tbody>                    <!--<tbody>-->
                                    <tr class="ays-top-keyword-row ui-state-default <?php echo $className; ?>">
                                        <td class="ays-top-keywords-sort">
                                            <i class="ays_fa ays_fa_arrows" aria-hidden="true"></i>
                                        </td>
                                        <td class="ays_top_keywords_row ">
                                            <select name="assign_top_keyword[]" class="ays_quiz_top_keywords">
                                            <?php
                                                $keyword_content = '';
                                                foreach ($keyword_arr as $key_arr => $answer_keyword) {
                                                    $selected = '';
                                                    if(isset($quiz_top_keyword['assign_top_keyword']) && $quiz_top_keyword['assign_top_keyword'] == $answer_keyword){
                                                        $selected = 'selected';
                                                    }
                                                    $keyword_content .= '<option value="'.$answer_keyword.'" '. $selected .'>'.$answer_keyword.'</option>';
                                                }
                                                echo $keyword_content;
                                            ?>
                                            </select>
                                        </td>
                                        <td>
                                            <textarea type="text" name="assign_top_keyword_text[]" class="top_keyword_text"><?php echo stripslashes(htmlentities($quiz_top_keyword['assign_top_keyword_text'])) ?></textarea>
                                        </td>


                                        <td>
                                            <a href="javascript:void(0)" class="ays-delete-top-keyword"
                                               data-id="<?php echo $key; ?>" title="<?php echo __('Delete',$this->plugin_name)?>">
                                                <i class="ays_fa ays_fa_minus_square" aria-hidden="true"></i>
                                            </a>
                                        </td>
                                    </tr>
                                </tbody> <!--            </tbody>-->
                                <?php
                            }
                        }
                    ?>
                </table>
            </div> <!-- Intervals -->
        </div>
    </div> <!-- Show all questions result in finish page -->

    <hr/>
    <!-- Bulk Coupon start -->
    <div class="form-group row ays_toggle_parent">
        <div class="col-sm-4">
            <label for="ays_quiz_enable_coupon">
                <?php echo __('Enable quiz coupons',$this->plugin_name)?>
                <a class="ays_help" data-html="true" data-toggle="tooltip" title="<?php echo __("Enable coupon receiving after finishing the quiz. For showing the coupons, you have to use the %%quiz_coupon%% message variable from General Settings>Message variables.",$this->plugin_name); ?>">
                    <i class="ays_fa ays_fa_info_circle"></i>
                </a>
            </label>
        </div>
        <div class="col-sm-1">
            <input type="checkbox" class="ays-enable-timer1 ays_toggle_checkbox" id="ays_quiz_enable_coupon" name="ays_quiz_enable_coupon" value="on" <?php echo $quiz_enable_coupon ? 'checked' : '' ?> />
        </div>
        <div class="col-sm-7 ays_toggle_target ays_divider_left <?php echo $quiz_enable_coupon ? '' : 'display_none'; ?>">
            <div class="ays-quiz-coupon-example-export">
                <a href="<?php echo AYS_QUIZ_ADMIN_URL;?>/partials/quizes/export_file/quiz_maker_coupon_data_example.csv" download="quiz_maker_coupon_data_example.csv" type="button" class="button button-small ays-quiz-export-coupon-data-example">
                    <img src="<?php echo AYS_QUIZ_ADMIN_URL; ?>/images/icons/export_example.svg" style='width:20px;'>
                    <?=__("Export exapmle", $this->plugin_name)?>
                </a>
            </div>
            <!-- Import Coupon CSV Start -->
            <div class="form-group row">
                <div class="col-sm-4">
                    <label for="ays_quiz_coupon_csv_import_file">
                        <?= sprintf(__("Choose .csv file to import", $this->plugin_name) , "<strong>.csv</strong>")?>
                        <a class="ays_help" data-html="true" data-toggle="tooltip" title="<?php echo __("Click the Choose file button and select your .csv file to import. The coupon list should be written properly (see the Export example).",$this->plugin_name); ?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </label>
                </div>
                <div class="col-sm-8 row" id="ays_quiz_import_coupon_csv_form">
                    <div>
                        <input type="file" accept=".csv" name="ays_quiz_coupon_csv_import_file" id="ays_quiz_coupon_csv_import_file">
                    </div>
                    <div>
                        <button type="button" class="button button-primary ays-quiz-coupon-csv-import-action" data-type="csv" disabled> <?= __('Import', $this->plugin_name)?> </button>
                    </div>
                </div>
            </div>
            <!-- Import Coupon CSV End -->
            <hr/>
            <!-- Bulk Coupon start -->
            <div class="form-group row">
                <div class="col-sm-4">
                    <label for="ays_quiz_coupon_csv_imported_coupons">
                        <?php echo __("Coupons list", $this->plugin_name); ?>
                    </label>
                </div>
                <div class="col-sm-8 row ays-quiz-active-inactive-coupons">
                    <div class="col-sm-6">
                        <p class="ays-quiz-imported-coupons-active-inactive">
                            <?php echo __('Active',$this->plugin_name)?>
                        </p>
                        <ul id="ays_quiz_coupons_active">
                            <?php
                                if(!empty($quiz_active_coupons)):
                                    foreach ($quiz_active_coupons as $key => $quiz_active_coupon):
                            ?>
                                        <li>
                                            <?php echo $quiz_active_coupon; ?>
                                            <input type="hidden" value="<?php echo $quiz_active_coupon; ?>" name="ays_quiz_coupons_active[]"/>
                                        </li>
                            <?php
                                    endforeach;
                                endif;
                            ?>
                        </ul>
                    </div>
                    <div class="col-sm-6">
                        <p class="ays-quiz-imported-coupons-active-inactive">
                            <?php echo __('Inactive',$this->plugin_name)?>
                        </p>
                        <ul id="ays_quiz_coupons_inactive">
                            <?php
                                if(!empty($quiz_inactive_coupons)):
                                    foreach ($quiz_inactive_coupons as $key => $quiz_inactive_coupon):
                            ?>
                                        <li>
                                            <?php echo $quiz_inactive_coupon; ?>
                                            <input type="hidden" value="<?php echo $quiz_inactive_coupon; ?>" name="ays_quiz_coupons_inactive[]"/>
                                        </li>
                            <?php
                                    endforeach;
                                endif;
                            ?>
                        </ul>
                    </div>
                </div>
                <div class="ays-quiz-question-import-modal-error-message display_none_not_important">
                </div>
            </div>
            <!-- Bulk Coupon End -->
        </div>
    </div>
    <!-- Bulk Coupon End -->

</div>
