<div class="tab-content <?php echo esc_attr(($setting_page == "folder-settings") ? "active" : "") ?>"
     id="folder-settings">
    <div class="settings-content">
        <div class="settings-content-left">
            <div class="form-fields">
                <div class="form-field">
                    <div class="form-label">
                        <label class="form-field-title" for="folders_post_type"><?php esc_html_e("Use folders with", "folders") ?></label>
                    </div>
                    <div class="form-input">
                        <?php
                        $post_setting = apply_filters("check_for_folders_post_args", []);
                        $post_types = get_post_types($post_setting, 'objects');
                        $post_array = array("page", "post", "attachment");
                        ?>
                        <select id="folders_post_type" multiple name="folders_settings[]">
                            <?php
                            foreach ( $post_types as $post_type ) {
                                if ( ! $post_type->show_ui) continue;
                                $is_checked = !in_array( $post_type->name, $options )?"hide-option":"";
                                $selected_id = (isset($default_folders[$post_type->name]))?$default_folders[$post_type->name]:"all";
                                $is_exists = WCP_Folders::check_for_setting($post_type->name, "default_folders");
                                $is_customized = WCP_Folders::check_for_setting($post_type->name, "folders_settings");
                                $selected = in_array( $post_type->name, $options )?'selected':'';
                                if(in_array($post_type->name, $post_array) || $is_customized === true){
                                    echo "<option " . esc_attr($selected) . " value='" . esc_attr($post_type->name) . "' >" . esc_attr($post_type->label) . "</option>";
                                } else {
                                    echo "<option class='pro-select-item' value='folders-pro' >" . esc_attr($post_type->label) . " (Upgrade to Pro) 🔑</option>";
                                }
                            }
                            echo "<option class='pro-select-item' value='folders-pro'>" . esc_html__("Plugins", "folders") . " (Upgrade to Pro) 🔑</option>";
                            ?>
                        </select>
                    </div>
                    <input type="hidden" name="folders_settings1" value="folders">
                </div>

                <div class="folder-post-types">
                    <?php
                    $default_folders = get_option('default_folders');
                    $default_folders = (empty($default_folders) || !is_array($default_folders)) ? [] : $default_folders;
                    foreach ($post_types as $post_type) {
                        if (!$post_type->show_ui) continue;
                        $is_checked = !in_array($post_type->name, $options) ? "hide-option" : "";
                        $selected_id = (isset($default_folders[$post_type->name])) ? $default_folders[$post_type->name] : "all";
                        $is_exists = WCP_Folders::check_for_setting($post_type->name, "default_folders");
                        $is_customized = WCP_Folders::check_for_setting($post_type->name, "folders_settings");
                        $checked = in_array($post_type->name, $options) ? "checked" : "";
                        if (in_array($post_type->name, $post_array) || $is_customized === true) {
                            $default_folder_link = $this->get_default_folder_link($post_type->name, $default_folders);
                            ?>
                            <div class="folder-post-type hide-show-option <?php echo esc_attr($is_checked) ?>" id="folders-for-<?php echo esc_attr($post_type->name); ?>">
                                <div class="form-field">
                                    <div class="form-label folder-label-wrap">
                                        <label class="folder-label" for="folders_for_<?php echo esc_attr($post_type->name); ?>"><?php echo esc_attr($post_type->label); ?> <?php esc_html_e("default folder", "folders") ?></label>
                                        <a target="_blank" href="<?php echo esc_url($default_folder_link) ?>" class="custom-folder-link" id="link-for-<?php echo esc_attr($post_type->name); ?>">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false"><path d="M19.5 4.5h-7V6h4.44l-5.97 5.97 1.06 1.06L18 7.06v4.44h1.5v-7Zm-13 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-3H17v3a.5.5 0 0 1-.5.5h-10a.5.5 0 0 1-.5-.5v-10a.5.5 0 0 1 .5-.5h3V5.5h-3Z"></path></svg>
                                        </a>
                                    </div>
                                    <div class="form-input">
                                        <select data-folder="<?php echo esc_attr($post_type->name); ?>" class="folder-post-select" id="folders_for_<?php echo esc_attr($post_type->name); ?>" name="default_folders[<?php echo esc_attr($post_type->name); ?>]">
                                            <option value="" data-url="<?php echo esc_url($this->get_taxonomy_link($post_type->name, '')) ?>"><?php printf(esc_html__("All %1\$s Folder", 'folders'), esc_attr($post_type->label)) ?></option>
                                            <option value="-1" data-url="<?php echo esc_url($this->get_taxonomy_link($post_type->name, -1)) ?>" <?php echo ($selected_id == -1) ? "selected" : "" ?>><?php printf(esc_html__("Unassigned %1\$s", 'folders'), esc_attr($post_type->label)) ?></option>
                                            <?php
                                            if (isset($terms_data[$post_type->name]) && !empty($terms_data[$post_type->name])) {
                                                foreach ($terms_data[$post_type->name] as $term) {
                                                    if (empty($is_exists) || $is_exists === false) {
                                                        echo "<option class='pro-select-item' value='folders-pro'>" . esc_attr($term->name) . " (Upgrade to Pro) 🔑</option>";
                                                    } else {
                                                        $selected = ($selected_id == $term->slug) ? "selected" : "";
                                                        echo "<option " . esc_attr($selected) . " data-url='".esc_url($this->get_taxonomy_link($post_type->name, $term->slug))."' value='" . esc_attr($term->slug) . "'>" . esc_attr($term->name) . "</option>";
                                                    }
                                                }
                                            } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        <?php }
                    }
                    $is_checked = !in_array("folders4plugins", $options) ? "hide-option" : "";
                    $selected_id = (isset($default_folders["folders4plugins"])) ? $default_folders["folders4plugins"] : "all";
                    $is_exists = WCP_Folders::check_for_setting("folders4plugins", "default_folders");
                    $is_customized = WCP_Folders::check_for_setting("folders4plugins", "folders_settings");
                    $checked = in_array("folders4plugins", $options) ? "checked" : "";
                    ?>
                    <div class="folder-post-type hide-show-option <?php echo esc_attr($is_checked) ?>" id="folders-for-folders4plugins">
                        <div class="form-field">
                            <div class="form-label">
                                <label class="folder-label" for="folders_for_folders4plugins"><?php esc_html_e("Default folder", "folders") ?></label>
                            </div>
                            <div class="form-input">
                                <select class="folder-post-select" id="folders_for_folders4plugins" name="default_folders[folders4plugins]" ?>
                                    <option value=""><?php printf(esc_html__("All %1\$s Folder", 'folders'), esc_html__("Plugins", "folders")) ?></option>
                                    <option value="-1" <?php echo ($selected_id == -1) ? "selected" : "" ?>><?php printf(esc_html__("Unassigned %1\$s", 'folders'), esc_html__("Plugins", "folders")) ?></option>
                                    <?php
                                    if (isset($terms_data["folders4plugins"]) && !empty($terms_data["folders4plugins"])) {
                                        foreach ($terms_data["folders4plugins"] as $term) {
                                            echo "<option class='pro-select-item' value='folders-pro'>" . esc_attr($term->title) . " (Pro) 🔑</option>";
                                        }
                                    } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            $customize_folders = get_option('customize_folders', []);
            $max_upload_size = wp_max_upload_size();
            $max_upload_size = $max_upload_size / 1024 / 1024;
            $default = [
                'use_shortcuts'                 => 'yes',
                'dynamic_folders'               => 'on',
                'use_folder_undo'               => 'yes',
                'default_timeout'               => 5,
                'use_max_upload_size'           => 'no',
                'max_upload_size'               => $max_upload_size,
                'enable_media_trash'            => 'off',
                'folders_enable_replace_media'  => 'yes',
                'folders_media_cleaning'        => 'yes',
                'show_media_details'            => 'on',
                'show_folder_in_settings'       => 'no',
                'folders_show_in_menu'          => 'no',
                'replace_media_title'           => 'on',
                'force_sorting'                 => 'off',
                'media_col_settings'            => [
                    'image_title',
                    'image_dimensions',
                    'image_type',
                    'image_date'
                ],
                'folder_colors'                 => ['#334155', '#86cd91', '#1E88E5', '#ff6060', '#49E670', '#ffdb5e', '#ff95ee'],
                'default_icon_color'            => '#334155',
                'folder_font'                   => '',
                'folder_size'                   => 16,
                'folder_custom_font_size'       => 16,
                'enable_horizontal_scroll'      => 'on',
                'show_in_page'                  => 'hide',
                'folders_by_users'              => 'off',
                'new_folder_color'              => '#FA166B',
                'media_replace_button'          => '#FA166B',
                'dropdown_color'                => '#484848',
                'bulk_organize_button_color'    => '#FA166B',
                'folder_bg_color'               => '#FA166B',
                'dynamic_folders_for_admin_only'    => 'off'
            ];
            $customize_folders = shortcode_atts($default, $customize_folders);
            $basicSettings = [
                'use_shortcuts' => [
                    'label'             => esc_html__('Use keyboard shortcuts to navigate faster', 'folders'),
                    'name'              => 'customize_folders[use_shortcuts]',
                    'type'              => 'checkbox',
                    'id'                => 'use_shortcuts',
                    'value'             => 'yes',
                    'has_tooltip'       => false,
                    'tooltip'           => '',
                    'is_recommended'    => false,
                    'label_class'       => 'inline-flex',
                    'is_pro'            => false,
                    'field_class'       => '',
                ],
                'dynamic_folders' => [
                    'label'             => esc_html__('Dynamic Folders', 'folders'),
                    'name'              => 'customize_folders[dynamic_folders]',
                    'type'              => 'checkbox',
                    'id'                => 'dynamic_folders',
                    'value'             => 'on',
                    'has_tooltip'       => true,
                    'tooltip'           => esc_html__('Automatically filter posts/pages/custom posts/media files based on author, date, file types & more', 'folders'),
                    'tooltip_image'     => WCP_FOLDER_URL . "assets/images/dynamic-folders.gif",
                    'is_recommended'    => true,
                    'label_class'       => '',
                    'is_pro'            => true,
                    'field_class'       => '',
                ],
                'use_folder_undo' => [
                    'label'             => esc_html__('Use folders with Undo action after performing tasks', 'folders'),
                    'name'              => 'customize_folders[use_folder_undo]',
                    'type'              => 'checkbox',
                    'id'                => 'use_folder_undo',
                    'value'             => 'yes',
                    'has_tooltip'       => true,
                    'tooltip'           => esc_html__('Undo any action on Folders. Undo move, copy, rename etc actions with this feature', 'folders'),
                    'tooltip_image'     => WCP_FOLDER_URL . "assets/images/undo-feature-folders.gif",
                    'is_recommended'    => true,
                    'label_class'       => '',
                    'is_pro'            => false,
                    'field_class'       => '',
                ],
                'default_timeout' => [
                    'label'             => esc_html__('Default timeout', 'folders'),
                    'name'              => 'customize_folders[default_timeout]',
                    'type'              => 'timeout',
                    'id'                => 'default_timeout',
                    'value'             => '5',
                    'has_tooltip'       => false,
                    'is_recommended'    => false,
                    'label_class'       => '',
                    'is_pro'            => false,
                    'field_class'       => 'timeout-settings '.esc_attr($customize_folders['use_folder_undo'] == 'yes' ? 'active' : ''),
                ],
                'folders_enable_replace_media' => [
                    'label'             => esc_html__('Enable Replace Media', 'folders'),
                    'name'              => 'customize_folders[folders_enable_replace_media]',
                    'type'              => 'checkbox',
                    'id'                => 'folders_enable_replace_media',
                    'value'             => 'yes',
                    'has_tooltip'       => true,
                    'tooltip'           => esc_html__('The Replace Media feature will allow you to replace your media files throughout your website with the click of a button,  which means the file will be replaced for all your posts, pages, etc', 'folders'),
                    'is_recommended'    => true,
                    'label_class'       => '',
                    'is_pro'            => false,
                    'field_class'       => '',
                ],
                'show_media_details' => [
                    'label'             => esc_html__('Show media details on hover', 'folders'),
                    'name'              => 'customize_folders[show_media_details]',
                    'type'              => 'checkbox',
                    'id'                => 'show_media_details',
                    'value'             => 'on',
                    'has_tooltip'       => true,
                    'tooltip'           => esc_html__('Show useful metadata including title, size, type, date, dimension & more on hover.', 'folders'),
                    'tooltip_image'     => WCP_FOLDER_URL . "assets/images/folders-media.gif",
                    'is_recommended'    => true,
                    'label_class'       => '',
                    'is_pro'            => true,
                    'field_class'       => '',
                ],
            ];
            $advanceSettings = [
                'use_max_upload_size' => [
                    'label'             => esc_html__('Max Upload File Size', 'folders'),
                    'name'              => 'customize_folders[use_max_upload_size]',
                    'type'              => 'checkbox',
                    'id'                => 'use_max_upload_size',
                    'value'             => 'yes',
                    'has_tooltip'       => true,
                    'tooltip'           => esc_html__('Specify the maximum allowed file size for uploads. This setting helps increase the size of files that users can upload.', 'folders'),
                    'is_recommended'    => false,
                    'label_class'       => '',
                    'is_pro'            => true,
                    'field_class'       => '',
                ],
                'enable_media_trash' => [
                    'label'             => esc_html__('Move files to trash by default before deleting', 'folders'),
                    'name'              => 'customize_folders[enable_media_trash]',
                    'type'              => 'checkbox',
                    'id'                => 'enable_media_trash',
                    'value'             => 'on',
                    'has_tooltip'       => true,
                    'tooltip'           => esc_html__('When enabled, files will be moved to trash to prevent mistakes, and then you can delete permanently from the trash', 'folders'),
                    'is_recommended'    => false,
                    'label_class'       => '',
                    'is_pro'            => true,
                    'field_class'       => '',
                ],
                'folders_media_cleaning' => [
                    'label'             => esc_html__('Use Media Cleaning to clear unused media files', 'folders'),
                    'name'              => 'customize_folders[folders_media_cleaning]',
                    'type'              => 'checkbox',
                    'id'                => 'folders_media_cleaning',
                    'value'             => 'yes',
                    'has_tooltip'       => true,
                    'tooltip'           => esc_html__('The Media Cleaning feature enables you to clean unused media files for your WordPress site and adds a Media Cleaning item under Media', 'folders'),
                    'is_recommended'    => false,
                    'label_class'       => '',
                    'is_pro'            => true,
                    'field_class'       => '',
                ],
                'replace_media_title' => [
                    'label'             => esc_html__('Auto Rename file based on title', 'folders'),
                    'name'              => 'customize_folders[replace_media_title]',
                    'type'              => 'checkbox',
                    'id'                => 'replace_media_title',
                    'value'             => 'on',
                    'has_tooltip'       => true,
                    'tooltip'           => esc_html__("Replace the actual file name of media files with the title from the WordPress editor.", 'folders'),
                    'is_recommended'    => false,
                    'label_class'       => '',
                    'is_pro'            => true,
                    'field_class'       => '',
                ],
                'force_sorting' => [
                    'label'             => esc_html__('Force sorting every time when a new folder is added', 'folders'),
                    'name'              => 'customize_folders[force_sorting]',
                    'type'              => 'checkbox',
                    'id'                => 'force_sorting',
                    'value'             => 'on',
                    'has_tooltip'       => true,
                    'tooltip'           => esc_html__("If unchecked, the new folder will be added at the top. If checked, the new folder will be placed according to your selected sorting order. For example, if sorting is set to A to Z, the new folder will be added in alphabetical order.", 'folders'),
                    'is_recommended'    => false,
                    'label_class'       => '',
                    'is_pro'            => false,
                    'field_class'       => '',
                ],
                'show_folder_in_settings' => [
                    'label'             => esc_html__('Place the Folders settings page nested under "Settings"', 'folders'),
                    'name'              => 'customize_folders[show_folder_in_settings]',
                    'type'              => 'checkbox',
                    'id'                => 'show_folder_in_settings',
                    'value'             => 'yes',
                    'has_tooltip'       => true,
                    'tooltip'           => esc_html__("When this setting is enabled, you will be able to access the Folders settings page by clicking on 'Settings' in the WordPress dashboard and then selecting 'Folders' from the submenu.", 'folders'),
                    'is_recommended'    => false,
                    'label_class'       => '',
                    'is_pro'            => false,
                    'field_class'       => '',
                ],
                'folders_show_in_menu' => [
                    'label'             => esc_html__('Show the folders also in WordPress menu', 'folders'),
                    'name'              => 'customize_folders[folders_show_in_menu]',
                    'type'              => 'checkbox',
                    'id'                => 'folders_show_in_menu',
                    'value'             => 'yes',
                    'has_tooltip'       => true,
                    'tooltip'           => esc_html__("Show folders separately on your WordPress sidebar for quick and easy access to folders of Media, Posts, Pages etc", 'folders'),
                    'tooltip_image'     => WCP_FOLDER_URL . "assets/images/page-folders.png",
                    'is_recommended'    => false,
                    'label_class'       => '',
                    'is_pro'            => false,
                    'field_class'       => '',
                ],
            ];
            ?>
            <div class="form-field-title">
                <?php esc_html_e("Recommended", "folders") ?>
            </div>
            <div class="form-fields">
                <?php foreach ($basicSettings as $key => $setting) { ?>
                    <div class="form-field <?php echo esc_attr($setting['field_class']) ?>" id="setting-<?php echo esc_attr($key) ?>">
                        <?php do_action('folders_field_prefix_settings', $setting, 'no'); ?>
                        <?php do_action('folders_field_label', $setting); ?>
                        <?php do_action('folders_field_input', $setting, $customize_folders[$key], false, $this->getFoldersUpgradeURL()); ?>
                        <?php do_action('folders_field_postfix_settings', $setting, 'no'); ?>
                    </div>
                    <?php do_action('folders_field_after_'.$key, $setting, $customize_folders, false, $this->getFoldersUpgradeURL()); ?>
                <?php } ?>
            </div>
            <div class="form-field-title">
                <?php esc_html_e("Advanced options", "folders") ?>
            </div>
            <div class="form-fields">
                <?php foreach ($advanceSettings as $key => $setting) { ?>
                    <div class="form-field <?php echo esc_attr($setting['field_class']) ?>" id="setting-<?php echo esc_attr($key) ?>">
                        <?php do_action('folders_field_prefix_settings', $setting, 'no'); ?>
                        <?php do_action('folders_field_label', $setting); ?>
                        <?php do_action('folders_field_input', $setting, $customize_folders[$key], false, $this->getFoldersUpgradeURL()); ?>
                        <?php do_action('folders_field_postfix_settings', $setting, 'no'); ?>
                    </div>
                    <?php do_action('folders_field_after', $setting, $customize_folders); ?>
                <?php } ?>
            </div>
            <div class="submit-button sr-only">
                <?php submit_button(); ?>
            </div>
        </div>
        <div class="settings-content-right">
            <div class="premio-help">
                <a href="https://premio.io/help/folders/?utm_source=pluginspage" target="_blank">
                    <div class="premio-help-btn">
                        <img src="<?php echo esc_url(WCP_FOLDER_URL . "assets/images/premio-help.png") ?>" alt="Premio Help" class="Premio Help"/>
                        <div class="need-help">Need Help?</div>
                        <div class="visit-our">Visit our</div>
                        <div class="knowledge-base">knowledge base</div>
                    </div>
                </a>
            </div>
            <?php if ($wp_status == "yes") { ?>
                <div class="premio-help">
                    <div class="premio-help-btn wp-folder-user">
                        <div class="folder-help-icon"><span class="dashicons dashicons-wordpress"></span></div>
                        <div class="need-help"><?php esc_html_e("WordPress.com User?", "folders") ?></div>
                        <div class="visit-our">
                            <a target="_blank" href="https://premio.io/help/folders/how-to-activate-folders-for-wordpress-com-media-library/"><?php esc_html_e("Enable Folders", "folders"); ?></a>
                            <?php esc_html_e(" for your Media Library", "folders"); ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>