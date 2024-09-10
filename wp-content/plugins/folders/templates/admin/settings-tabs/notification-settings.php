<?php
if($setting_page == "notification-settings") {
    $notification_setting = get_option('folders_notification_settings');
    $notification_setting = apply_filters('check_for_folders_notification_settings', $notification_setting);
    ?>
    <script>
        (function (factory) {
            "use strict";
            if(typeof define === 'function' && define.amd) {
                define(['jquery'], factory);
            }
            else if(typeof module !== 'undefined' && module.exports) {
                module.exports = factory(require('jquery'));
            }
            else {
                factory(jQuery);
            }
        }(function ($, undefined) {
            <?php if ($setting_page == "notification-settings") { ?>
            $(document).ready(function(){
                $(".notification-select:not(#remove_users-options)").select2();
                $("#remove_users-options").select2({
                    tags: false,
                    multiple: true,
                    minimumInputLength: 2,
                    minimumResultsForSearch: 10,
                    placeholder: "Search User",
                    ajax: {
                        url: "<?php echo esc_url(admin_url("admin-ajax.php")) ?>",
                        dataType: "json",
                        type: "POST",
                        quietMillis: 50,
                        data: function (params) {
                            var queryParameters = {
                                action: 'folders_search_for_users',
                                search: params.term,
                                nonce: "<?php echo esc_attr(wp_create_nonce("search_folder_user")) ?>",
                                paged: 0
                            }
                            return queryParameters;
                        },
                        processResults: function (result) {
                            console.log(result);
                            if(result.status) {
                                return {
                                    results: jQuery.map(result.data, function (item) {
                                        return {
                                            text: item.display_name,
                                            id: item.id
                                        }
                                    })
                                };
                            } else {
                                return {
                                    results: jQuery.map(data, function (item) {
                                        return {
                                            text: "No results are found",
                                            id: 0,
                                            disabled: true
                                        }
                                    })
                                };
                            }
                        }
                    }
                });
            });
            <?php } ?>
        }));
    </script>
    <div class="tab-content <?php echo esc_attr(($setting_page == "notification-settings") ? "active" : "") ?>" id="notification-settings">
        <div class="accordion-content no-bp">
            <?php $allow_notification = "on" ?>
            <div class="notification-setting folder-user-settings <?php echo esc_attr(($allow_notification == "on")?"active":"") ?>">
                <div class="note-settings">
                    <div class="notification-emails">
                        <label>
                            <?php esc_html_e("Notification Email", "folders"); ?> <span class="folder-tooltip" data-title="Notification Will be sent to this email"><span class="dashicons dashicons-editor-help"></span></span>
                        </label>
                        <?php
                        $default_emails = [""];
                        $notification_emails = (isset($notification_setting['notification_email']) && is_array($notification_setting['notification_email'])) ? $notification_setting['notification_email'] : $default_emails ?>
                        <?php
                        foreach ($notification_emails as $email) { ?>
                            <div class="notification-email">
                                <div class="email-info">
                                    <input type="email" required name="notification_setting[notification_email][]" value="<?php echo esc_attr($email) ?>" />
                                </div>
                                <div class="email-test-button">
                                    <a href="javascript:;" class="send-test-email"><?php esc_html_e("Test", "folders"); ?></a>
                                </div>
                                <div class="email-remove-button">
                                    <a href="javascript:;" class="remove-email"><?php esc_html_e("Remove", "folders"); ?></a>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="notification-email add-email-button">
                            <a href="javascript:;" class="add-email-notification" ><?php esc_html_e("+ Add another email", "folders"); ?></a>
                        </div>
                    </div>
                    <div class="mail-settings">
                        <?php if(isset($notification_setting['mail_setting']) && !empty($notification_setting['mail_setting'])) {
                            foreach($notification_setting['mail_setting'] as $key => $setting) {
                                $setting['status'] = "on";
                                ?>
                                <div class="mail-setting">
                                    <div class="mail-checkbox">
                                        <input type="hidden" name="notification_setting[mail_setting][<?php echo esc_attr($key) ?>][status]" value="off">
                                        <label class="custom-checkbox" for="notification_setting_<?php echo esc_attr($key) ?>">
                                            <input id="notification_setting_<?php echo esc_attr($key) ?>" class="sr-only mail-options" type="checkbox" name="notification_setting[mail_setting][<?php echo esc_attr($key) ?>][status]" value="on" <?php checked($setting['status'], "on") ?> />
                                            <span></span>
                                        </label>
                                        <label for="notification_setting_<?php echo esc_attr($key) ?>"><?php echo esc_attr($setting['title']) ?></label>
                                    </div>
                                    <div class="mail-posts <?php echo esc_attr(($setting['status'] == "on")?"active":"")    ?>">
                                        <?php if($key != "remove_users") { ?>
                                            <div class="mail-field">
                                                <!--<label><?php /*esc_html_e("Select Post types"); */?></label>-->
                                                <div class="mail-field-input">
                                                    <select id="<?php echo esc_attr($key) ?>-options" multiple class="notification-select" name="notification_setting[mail_setting][<?php echo esc_attr($key) ?>][post_type][]">
                                                        <?php if(isset($setting['default']) && is_array($setting['default'])) {
                                                            $setting['post_type'] = isset($setting['post_type']) ? $setting['post_type'] : "";
                                                            $post_type =  !is_array($setting['post_type'])?[]:$setting['post_type'];
                                                            foreach ($setting['default'] as $key=>$label) {
                                                                ?>
                                                                <option selected value="<?php echo esc_attr($key) ?>"><?php echo esc_attr($label) ?></option>
                                                            <?php } ?>
                                                        <?php  }  ?>
                                                    </select>
                                                </div>
                                            </div>
                                        <?php } else { ?>
                                            <select id="<?php echo esc_attr($key) ?>-options" multiple class="notification-select" name="notification_setting[mail_setting][<?php echo esc_attr($key) ?>][users][]">
                                                <?php if(isset($setting['users']) && is_array($setting['users'])) {
                                                    foreach ($setting['users'] as $use_id) {
                                                        $user_data = get_user_by("id", $use_id);
                                                        if(!empty($user_data) && isset($user_data->data->ID)) {
                                                            ?>
                                                            <option selected="selected" value="<?php echo esc_attr($use_id) ?>"><?php echo esc_attr($user_data->data->display_name) ?></option>
                                                        <?php }
                                                    }
                                                }  ?>
                                            </select>
                                        <?php } ?>

                                        <?php if(isset($setting['email']) && 0) { ?>
                                            <div class="mail-field">
                                                <label><?php esc_html_e("Mail Subject"); ?></label>
                                                <div class="mail-field-input">
                                                    <input type="text" name="notification_setting[mail_setting][<?php echo esc_attr($key) ?>][email][subject]" value="<?php echo esc_attr($setting['email']['subject']) ?>">
                                                </div>
                                            </div>
                                            <div class="mail-field">
                                                <label><?php esc_html_e("Mail Content"); ?></label>
                                                <div class="mail-field-input">
                                                    <textarea name="notification_setting[mail_setting][<?php echo esc_attr($key) ?>][email][content]"><?php echo esc_attr($setting['email']['content']) ?></textarea>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <?php if(isset($setting['help']) && 0) { ?>
                                            <div class="mail-field">
                                                <?php echo nl2br(esc_attr($setting['help'])) ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php }
                        } ?>
                    </div>
                    <div class="submit-button">
                        <p>
                            <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
                        </p>
                    </div>
                </div>
                <div class="pro-feature-popup">
                    <div class="pro-feature-content" style="top: 30%">
                        <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M16.0002 2.66663C12.3335 2.66663 9.3335 5.66663 9.3335 9.33329V12H12.0002V9.33329C12.0002 7.13329 13.8002 5.33329 16.0002 5.33329C18.2002 5.33329 20.0002 7.13329 20.0002 9.33329V12H22.6668V9.33329C22.6668 5.66663 19.6668 2.66663 16.0002 2.66663Z" fill="#424242"></path>
                            <path d="M24.0002 29.3333H8.00016C6.5335 29.3333 5.3335 28.1333 5.3335 26.6667V14.6667C5.3335 13.2 6.5335 12 8.00016 12H24.0002C25.4668 12 26.6668 13.2 26.6668 14.6667V26.6667C26.6668 28.1333 25.4668 29.3333 24.0002 29.3333Z" fill="#FB8C00"></path>
                            <path d="M16 22.6666C17.1046 22.6666 18 21.7712 18 20.6666C18 19.5621 17.1046 18.6666 16 18.6666C14.8954 18.6666 14 19.5621 14 20.6666C14 21.7712 14.8954 22.6666 16 22.6666Z" fill="#C76E00"></path>
                        </svg>
                        <div class="pro-user-title"><?php esc_html_e("Get notified whenever changes are made to Pages, Posts, Plugins, or Media Files", "folders") ?></div>
                        <div class="pro-user-desc"></div>
                        <a href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>" target="_blank">
                            <svg width="18" height="15" viewBox="0 0 18 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M17.9998 3.75C17.9998 4.44031 17.4401 5 16.7498 5C16.7421 5 16.7356 4.99603 16.7278 4.99594L15.1491 13.6803C15.0623 14.1531 14.6498 14.5 14.1654 14.5H3.83418C3.35105 14.5 2.93668 14.1544 2.85043 13.6791L1.27199 4.99688C1.26418 4.99688 1.25762 5 1.22168 5C0.531367 5 -0.0283203 4.44031 -0.0283203 3.75C-0.0283203 3.05969 0.559492 2.5 1.22168 2.5C1.88387 2.5 2.47168 3.05969 2.47168 3.75C2.47168 4.03119 2.36165 4.27781 2.2049 4.48656L5.00584 6.72719C5.50302 7.125 6.24021 6.96294 6.5249 6.39344L8.3249 2.79344C7.97168 2.57313 7.72168 2.19813 7.72168 1.75C7.72168 1.05969 8.30918 0.5 8.9998 0.5C9.69043 0.5 10.2217 1.05969 10.2217 1.75C10.2217 2.19813 9.97284 2.57313 9.61855 2.79375L11.4186 6.39375C11.7033 6.96313 12.4407 7.125 12.9376 6.7275L15.7386 4.48688C15.6092 4.27813 15.4998 4.00313 15.4998 3.75C15.4998 3.05938 16.0592 2.5 16.7498 2.5C17.4404 2.5 17.9998 3.05938 17.9998 3.75Z" fill="white"></path>
                            </svg>
                            <?php esc_html_e("Upgrade to Pro", "folders") ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
