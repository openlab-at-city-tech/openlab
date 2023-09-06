<div id="tab6" class="ays-quiz-tab-content <?php echo ($ays_quiz_tab == 'tab6') ? 'ays-quiz-tab-content-active' : ''; ?>">
    <p class="ays-subtitle"><?php echo __('User Information',$this->plugin_name)?></p>
    <hr/>
    <div class="form-group row ays-quiz-result-message-vars-parent">
        <div class="col-sm-4">
            <label for="ays_form_title">
                <?php echo __('Information Form title',$this->plugin_name)?>
                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Description of the Information Form which will be shown at the top of the Form Fields.',$this->plugin_name)?>">
                    <i class="ays_fa ays_fa_info_circle"></i>
                </a>
            </label>
        </div>
        <div class="col-sm-8" style="border-left: 1px solid #ccc">
            <?php
            echo $quiz_message_vars_information_form_html;
            $content = wpautop(stripslashes((isset($options['form_title'])) ? $options['form_title'] : ''));
            $editor_id = 'ays_form_title';
            $settings = array('editor_height' => $quiz_wp_editor_height, 'textarea_name' => 'ays_form_title', 'editor_class' => 'ays-textarea', 'media_elements' => false);
            wp_editor($content, $editor_id, $settings);
            ?>
        </div>
    </div> <!-- Information Form title -->
    <hr>
    <div class="form-group row ays_toggle_parent">
        <div class="col-sm-2">
            <label for="ays_information_form">
                <?php echo __('Information Form',$this->plugin_name)?>
                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Data form for the user personal information. You can choose when the Information Form will be shown for completion.',$this->plugin_name)?>">
                    <i class="ays_fa ays_fa_info_circle"></i>
                </a>
            </label>
        </div>
        <div class="col-sm-2">
            <div class="information_form_settings">
                <select class="ays_toggle_select" name="ays_information_form" data-hide="disable" id="ays_information_form">
                    <option value="after" <?php echo (isset($options['information_form']) && $options['information_form'] == 'after') ? 'selected' : ''; ?>>
                        <?php echo __('After Quiz',$this->plugin_name)?>
                    </option>
                    <option value="before" <?php echo (isset($options['information_form']) && $options['information_form'] == 'before') ? 'selected' : ''; ?>>
                        <?php echo __('Before Quiz',$this->plugin_name)?>
                    </option>
                    <option value="disable" <?php echo (isset($options['information_form']) && $options['information_form'] == 'disable') ? 'selected' : ''; ?>>
                        <?php echo __('Disable',$this->plugin_name)?>
                    </option>
                </select>
            </div>
        </div>
        <div class="col-sm-8 ays_divider_left ays_toggle_target_inverse <?php echo (!isset($options['information_form']) || $options['information_form'] == "disable") ? '' : 'display_none'; ?>">
            <label for="ays_allow_collecting_logged_in_users_data" style="margin-right:20px;">
                <?php echo __('Allow collecting information of logged in users',$this->plugin_name)?>
                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Allow collecting information from logged in users. Email and name of users will be stored in the database. Email and Certificate options will be work for these users.', $this->plugin_name)?>">
                    <i class="ays_fa ays_fa_info_circle"></i>
                </a>
            </label>
            <input type="checkbox" id="ays_allow_collecting_logged_in_users_data" name="ays_allow_collecting_logged_in_users_data" value="on" <?php echo $allow_collecting_logged_in_users_data ? "checked" : ""; ?>>
        </div>
        <div class="col-sm-8 ays_divider_left ays_toggle_target <?php echo (!isset($options['information_form']) || $options['information_form'] == "disable") ? 'display_none' : ''; ?>">
            <p class="ays_required_field_title"><?php echo __('Form Fields',$this->plugin_name)?></p>
            <div class="form-group row">
                <div class="col-sm-12">
                    <blockquote><?php echo __( 'Double-click on the Information Form fields to drop them to the Active Fields and vice versa (from Active Fields back to Available Fields), Or you can drag and drop the fields from the Available Fields to the Active Fields.', $this->plugin_name ); ?></blockquote>
                </div>
            </div>
            <hr>
            <div class="checkbox_carousel">
                <div class="form_fields_wrap">
                    <p class="ays-subtitle" style="text-align:center;"><?php echo __( "Available fields", $this->plugin_name ); ?></p>
                    <ul id="form_available_fields" class="checkbox_carousel_body form_available_fields">
                        <?php
                        foreach ($custom_fields_passive as $slug => $attribute) {
                            $checked = (in_array(strval($attribute['id']), $quiz_attributes_checked)) ? 'checked' : '';
                            $attr_name = "ays_quiz_attributes_passive[]";
                            $attr_name_required = "ays_required_field[]";
                            $attr_value = $attribute['id'];
                            if(in_array($slug, $default_attributes)){
                                $attr_name = $slug;
                                $attr_value = "off";
                            }

                            if ( $attr_name == "ays_form_name" || $attr_name == "ays_form_email" || $attr_name == "ays_form_phone" ) {
                                // $attribute['name'] .= " " . __( "(Default)", $this->plugin_name );
                                $attribute['name'] .= "<p class='ays_quiz_small_hint_text_for_message_variables' style='margin:0;'>" . __( "(Default)", $this->plugin_name ) . "</p>";
                            }
                            ?>
                            <li class="checkbox_ays ui-state-default">
                                <input type="hidden" name="ays_quiz_attributes_passive_order[]" value="<?php echo $slug; ?>">
                                <div class="form-check form-check-inline">
                                    <input type="hidden" class="form-check-input" data-id="<?php echo $attr_value; ?>" id="<?php echo $slug; ?>" name="<?php echo $attr_name; ?>"
                                           value="<?php echo $attr_value; ?>"/>
                                    <label class="form-check-label" for="<?php echo $slug; ?>"><?php echo $attribute['name']; ?></label>
                                </div>
                                <div class="custom_field_type">
                                    <span class="ays_quiz_small_hint_text"><?php echo $attribute['type']; ?></span>
                                </div>
                                <div class="form-check form-check-inline custom_field_required display_none">
                                    <input type="checkbox" class="form-check-input" id="<?php echo $slug; ?>_required" name="<?php echo $attr_name_required; ?>"
                                           value="<?php echo $slug; ?>" <?php echo $attribute['required']; ?> />
                                    <label class="form-check-label" for="<?php echo $slug; ?>_required"><?php echo __( "Required", $this->plugin_name ); ?></label>
                                </div>
                            </li>
                        <?php
                        }
                        ?>
                    </ul>
                </div>
                <div class="form_fields_wrap">
                    <p class="ays-subtitle" style="text-align:center;"><?php echo __( "Active fields", $this->plugin_name ); ?></p>
                    <ul id="form_fields" class="checkbox_carousel_body form_fields">
                        <?php
                        foreach ($custom_fields_active as $slug => $attribute) {
                            $checked = (in_array(strval($attribute['id']), $quiz_attributes_checked)) ? 'checked' : '';
                            $attr_name = "ays_quiz_attributes[]";
                            $attr_name_required = "ays_required_field[]";
                            $attr_value = $attribute['id'];
                            if(in_array($slug, $default_attributes)){
                                $attr_name = $slug;
                                $attr_value = "on";
                            }
                            
                            if ( $attr_name == "ays_form_name" || $attr_name == "ays_form_email" || $attr_name == "ays_form_phone" ) {
                                $attribute['name'] .= "<p class='ays_quiz_small_hint_text_for_message_variables' style='margin:0;'>" . __( "(Default)", $this->plugin_name ) . "</p>";
                            }
                            ?>
                            <li class="checkbox_ays ui-state-highlight">
                                <input type="hidden" name="ays_quiz_attributes_active_order[]" value="<?php echo $slug; ?>">
                                <div class="form-check form-check-inline">
                                    <input type="hidden" class="form-check-input" data-id="<?php echo $attr_value; ?>" id="<?php echo $slug; ?>" name="<?php echo $attr_name; ?>"
                                           value="<?php echo $attr_value; ?>"/>
                                    <label class="form-check-label" for="<?php echo $slug; ?>"><?php echo $attribute['name']; ?></label>
                                </div>
                                <div class="custom_field_type">
                                    <span class="ays_quiz_small_hint_text"><?php echo $attribute['type']; ?></span>
                                </div>
                                <div class="form-check form-check-inline custom_field_required">
                                    <input type="checkbox" class="form-check-input" id="<?php echo $slug; ?>_required" name="<?php echo $attr_name_required; ?>"
                                           value="<?php echo $slug; ?>" <?php echo $attribute['required']; ?> />
                                    <label class="form-check-label" for="<?php echo $slug; ?>_required"><?php echo __( "Required", $this->plugin_name ); ?></label>
                                </div>
                            </li>
                        <?php
                        }
                        ?>
                    </ul>
                </div>
            </div>
            <div style="clear: both;"></div>
        </div>
    </div> <!-- Information Form -->
    <hr/>
    <div class="form-group row">
        <div class="col-sm-4">
            <label>
                <?php echo __('Add custom fields',$this->plugin_name)?>
                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('You can add form custom fields from “Custom fields” page in Quiz Maker menu.  (text, textarea, checkbox, select, URL etc.)',$this->plugin_name)?>">
                    <i class="ays_fa ays_fa_info_circle"></i>
                </a>
            </label>
        </div>
        <div class="col-sm-8 ays_divider_left">
            <blockquote>
                <?php echo __("For creating custom fields click ", $this->plugin_name); ?>
                <a href="?page=<?php echo $this->plugin_name; ?>-quiz-attributes" ><?php echo __("here", $this->plugin_name); ?></a>
            </blockquote>
        </div>
    </div> <!-- Add custom fields -->
    <hr/>
    <div class="form-group row">
        <div class="col-sm-4">
            <label for="ays_show_information_form">
                <?php echo __('Show Information Form to logged-in users',$this->plugin_name); ?>
                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('Enable the option if you want to show the Information Form to logged-in users as well. If the option is disabled, then logged-in users will not see the Information Form before or after the quiz, but the system will collect the Name and Email info from their WP accounts and store in the Name and Email fields in the database.',$this->plugin_name)?>">
                    <i class="ays_fa ays_fa_info_circle"></i>
                </a>
            </label>
        </div>
        <div class="col-sm-8 ays_divider_left">
            <div class="information_form_settings">
                <input type="checkbox" id="ays_show_information_form" name="ays_show_information_form" value="on" <?php echo $show_information_form ? "checked" : ""; ?>>
            </div>
        </div>
    </div>
    <hr/>
    <div class="form-group row">
        <div class="col-sm-4">
            <label for="ays_autofill_user_data">
                <?php echo __('Autofill logged-in user data',$this->plugin_name); ?>
                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('After enabling this option, logged in  user’s name and email will be autofilled in Information Form.',$this->plugin_name); ?>">
                    <i class="ays_fa ays_fa_info_circle"></i>
                </a>
            </label>
        </div>
        <div class="col-sm-8 ays_divider_left">
            <div class="information_form_settings">
                <input type="checkbox" id="ays_autofill_user_data" name="ays_autofill_user_data" value="on" <?php echo $autofill_user_data ? "checked" : ""; ?>>
            </div>
        </div>
    </div> <!-- Autofill logged in user data -->
    <hr>
    <div class="form-group row">
        <div class="col-sm-4">
            <label for="ays_display_fields_labels">
                <?php echo __('Display form fields with labels',$this->plugin_name); ?>
                <a class="ays_help" data-toggle="tooltip" title="<?php echo esc_attr( __('Show labels of form fields on the top of each field. Texts of labels will be taken from the "Fields placeholder" section on the General setting page.',$this->plugin_name) ); ?>">
                    <i class="ays_fa ays_fa_info_circle"></i>
                </a>
            </label>
        </div>
        <div class="col-sm-8">
            <div class="information_form_settings">
                <input type="checkbox" id="ays_display_fields_labels" name="ays_display_fields_labels" value="on" <?php echo $display_fields_labels ? "checked" : ""; ?>>
            </div>
        </div>
    </div>
</div>
