<div class="tab-content <?php echo esc_attr(($setting_page == "folder-settings") ? "active" : "") ?>" id="folder-settings">
    <div class="accordion-content no-bp">
        <div class="accordion-left">
            <table class="form-table">
                <tboby>
                    <?php
                    $post_setting = apply_filters("check_for_folders_post_args", ["show_in_menu" => 1]);
                    $post_types = get_post_types( $post_setting, 'objects' );
                    $post_array = array("page", "post", "attachment");
                    foreach ( $post_types as $post_type ) {
                        if ( ! $post_type->show_ui) continue;
                        $is_checked = !in_array( $post_type->name, $options )?"hide-option":"";
                        $selected_id = (isset($default_folders[$post_type->name]))?$default_folders[$post_type->name]:"all";
                        $is_exists = WCP_Folders::check_for_setting($post_type->name, "default_folders");
                        $is_customized = WCP_Folders::check_for_setting($post_type->name, "folders_settings");
                        if(in_array($post_type->name, $post_array) || $is_customized === true){
                            ?>
                            <tr>
                                <td class="no-padding">
                                    <label label for="folders_<?php echo esc_attr($post_type->name); ?>" class="custom-checkbox">
                                        <input type="checkbox" class="folder-select sr-only" id="folders_<?php echo esc_attr($post_type->name); ?>" name="folders_settings[]" value="<?php echo esc_attr($post_type->name); ?>"<?php if ( in_array( $post_type->name, $options ) ) echo ' checked="checked"'; ?>/>
                                        <span></span>
                                    </label>
                                </td>
                                <td class="" width="260px">
                                    <label for="folders_<?php echo esc_attr($post_type->name); ?>" ><?php echo esc_html__( 'Use Folders with: ', 'folders')." ".esc_attr($post_type->label); ?></label>
                                </td>
                                <td class="default-folder">
                                    <label class="hide-show-option <?php echo esc_attr($is_checked) ?>" for="folders_for_<?php echo esc_attr($post_type->name); ?>" ><?php esc_html_e( 'Default folder: ', 'folders'); ?></label>
                                </td>
                                <td>
                                    <select class="hide-show-option <?php echo esc_attr($is_checked) ?>" id="folders_for_<?php echo esc_attr($post_type->name); ?>" name="default_folders[<?php echo esc_attr($post_type->name); ?>]" ?>
                                        <option value="">All <?php echo esc_attr($post_type->label) ?> Folder</option>
                                        <option value="-1" <?php echo ($selected_id == -1)?"selected":"" ?>>Unassigned <?php echo esc_attr($post_type->label) ?></option>
                                        <?php
                                        if(isset($terms_data[$post_type->name]) && !empty($terms_data[$post_type->name])) {
                                            foreach ($terms_data[$post_type->name] as $term) {
                                                if(empty($is_exists) || $is_exists === false) {
                                                    echo "<option class='pro-select-item' value='folders-pro'>" . esc_attr( $term->name ). " (Pro) 🔑</option>";
                                                } else {
                                                    $selected = ( $selected_id == $term->slug ) ? "selected" : "";
                                                    echo "<option " . esc_attr( $selected ) . " value='" . esc_attr( $term->slug ) . "'>" . esc_attr( $term->name ) . "</option>";
                                                }
                                            }
                                        } ?>
                                    </select>
                                </td>
                            </tr>
                            <?php if($post_type->name == "attachment") { ?>
                                <tr>
                                    <td style="padding: 15px 10px 15px 0px" colspan="4">
                                        <a class="upgrade-box-link" target="_blank" href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>" >
                                            <label for="" class="custom-checkbox send-user-to-pro">
                                                <input disabled type="checkbox" class="sr-only" name="customize_folders[show_media_details]" id="show_media_details" value="on" >
                                                <span></span>
                                            </label>
                                            <label for="" class="send-user-to-pro">
                                                <?php esc_html_e( 'Use Folders with: Plugins', 'folders'); ?>
                                                <button type="button" class="upgrade-link" ><?php esc_html_e("Upgrade to Pro", 'folders'); ?></button>
                                            </label>
                                        </a>
                                    </td>
                                </tr>
                            <?php }
                        } else {
                            $show_media_details = "off";
                            ?>
                            <tr>
                                <td style="padding: 15px 10px 15px 0px" colspan="4">
                                    <a class="upgrade-box-link" target="_blank" href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>" >
                                        <label for="" class="custom-checkbox send-user-to-pro">
                                            <input disabled type="checkbox" class="sr-only" name="customize_folders[show_media_details]" id="show_media_details" value="on" <?php checked($show_media_details, "on") ?>>
                                            <span></span>
                                        </label>
                                        <label for="" class="send-user-to-pro">
                                            <?php esc_html_e( 'Use Folders with: ', 'folders')." ".esc_attr($post_type->label); ?>
                                            <button type="button" class="upgrade-link" ><?php esc_html_e("Upgrade to Pro", 'folders'); ?></button>
                                        </label>
                                    </a>
                                </td>
                            </tr>
                        <?php }
                    } ?>
                    <?php
                    $show_in_page = !isset($customize_folders['use_shortcuts']) ? "yes" : $customize_folders['use_shortcuts'];
                    ?>

                    <tr>
                        <td class="no-padding">
                            <input type="hidden" name="customize_folders[use_shortcuts]" value="no">
                            <label for="use_shortcuts" class="custom-checkbox">
                                <input id="use_shortcuts" class="sr-only" <?php checked($show_in_page, "yes") ?> type="checkbox" name="customize_folders[use_shortcuts]" value="yes">
                                <span></span>
                            </label>
                        </td>
                        <td colspan="3">
                            <label for="use_shortcuts" ><?php esc_html_e('Use keyboard shortcuts to navigate faster', 'folders'); ?> <a href="#" class="view-shortcodes">(<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg> <?php esc_html_e('View shortcuts', 'folders'); ?>)</a>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 15px 10px 15px 0px" colspan="4">
                            <?php $dynamic_folders = "off"; ?>
                            <a class="upgrade-box-link" target="_blank" href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>" >
                                <label for="" class="custom-checkbox send-user-to-pro">
                                    <input disabled type="checkbox" class="sr-only" name="customize_folders[dynamic_folders]" id="dynamic_folders" value="on" <?php checked($dynamic_folders, "on") ?>>
                                    <span></span>
                                </label>
                                <label for="" class="send-user-to-pro">
                                    <?php esc_html_e("Dynamic Folders", "folders"); ?>
                                    <button type="button" class="upgrade-link" href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>"><?php esc_html_e("Upgrade to Pro", 'folders') ?></button>
                                                    <span class="html-tooltip dynamic ">
                                                        <span class="dashicons dashicons-editor-help"></span>
                                                        <span class="tooltip-text top" style="">
                                                            <?php esc_html_e("Automatically filter posts/pages/custom posts/media files based on author, date, file types & more", "folders") ?>
                                                            <img src="<?php echo esc_url(WCP_FOLDER_URL."assets/images/dynamic-folders.gif") ?>">
                                                        </span>
                                                    </span>
                                    <span class="recommanded"><?php esc_html_e("Recommended", "folders") ?></span>
                                </label>
                            </a>
                        </td>
                    </tr>
                    <?php
                    $show_in_page = !isset($customize_folders['use_folder_undo']) ? "yes" : $customize_folders['use_folder_undo'];
                    ?>
                    <tr>
                        <td class="no-padding">
                            <input type="hidden" name="customize_folders[use_folder_undo]" value="no">
                            <label for="use_folder_undo" class="custom-checkbox">
                                <input id="use_folder_undo" class="sr-only" <?php checked($show_in_page, "yes") ?> type="checkbox" name="customize_folders[use_folder_undo]" value="yes">
                                <span></span>
                            </label>
                        </td>
                        <td colspan="3">
                            <label for="use_folder_undo" ><?php esc_html_e('Use folders with Undo action after performing tasks', 'folders'); ?>
                                <span class="html-tooltip">
                                                        <span class="dashicons dashicons-editor-help"></span>
                                                        <span class="tooltip-text top height-auto" style="height:auto">
                                                            <?php esc_html_e("Undo any action on Folders. Undo move, copy, rename etc actions with this feature", "folders") ?>
                                                            <img src="<?php echo esc_url(WCP_FOLDER_URL."assets/images/undo-feature-folders.gif") ?>">
                                                        </span>
                                                    </span>
                                <span class="recommanded"><?php esc_html_e("Recommended", "folders") ?></span>
                            </label>
                        </td>
                    </tr>
                    <?php
                    $default_timeout = !isset($customize_folders['default_timeout']) ? "5" : $customize_folders['default_timeout'];
                    ?>
                    <tr class="timeout-settings <?php echo ($show_in_page == "yes") ? "active" : "" ?>">
                        <td style="padding: 10px 0;" colspan="4">
                            <label for="default_timeout" ><?php esc_html_e('Default timeout', 'folders'); ?></label>
                            <div class="seconds-box">
                                <input type="number" class="seconds-input" name="customize_folders[default_timeout]" value="<?php echo esc_attr($default_timeout) ?>" />
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 15px 10px 15px 0px" colspan="4">
                            <?php $replace_media_title = "off"; ?>
                            <a class="upgrade-box-link" target="_blank" href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>" >
                                <label for="" class="custom-checkbox send-user-to-pro">
                                    <input disabled type="checkbox" class="sr-only" id="enable_media_trash" value="off">
                                    <span></span>
                                </label>
                                <label for="" class="send-user-to-pro">
                                    <?php esc_html_e("Move files to trash by default before deleting", "folders"); ?>
                                    <span class="folder-tooltip" data-title="<?php esc_html_e("When enabled, files will be moved to trash to prevent mistakes, and then you can delete permanently from the trash", "folders") ?>"><span class="dashicons dashicons-editor-help"></span></span></label>
                                <button type="button" class="upgrade-link" href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>"><?php esc_html_e("Upgrade to Pro", 'folders') ?></button>
                                </label>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 15px 10px 15px 0px" colspan="4">
                            <?php $replace_media_title = "off"; ?>
                            <a class="upgrade-box-link" target="_blank" href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>" >
                                <label for="" class="custom-checkbox send-user-to-pro">
                                    <input disabled type="checkbox" class="sr-only" id="enable_media_trash" value="off">
                                    <span></span>
                                </label>
                                <label for="" class="send-user-to-pro">
                                    <?php esc_html_e("Max upload file size", "folders"); ?>
                                    <span class="folder-tooltip" data-title="<?php esc_html_e("Specify the maximum allowed file size for uploads. This setting helps increase the size of files that users can upload.  ", "folders") ?>"><span class="dashicons dashicons-editor-help"></span></span>
                                </label>
                                <button type="button" class="upgrade-link" href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>"><?php esc_html_e("Upgrade to Pro", 'folders') ?></button>
                                </label>
                            </a>
                        </td>
                    </tr>
                    <?php
                    $show_in_page = !isset($customize_folders['folders_media_cleaning']) ? "yes" : $customize_folders['folders_media_cleaning'];
                    ?>
                    <tr>
                        <td style="padding: 15px 10px 15px 0px" colspan="4">
                            <?php $replace_media_title = "off"; ?>
                            <a class="upgrade-box-link" target="_blank" href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>" >
                                <label for="" class="custom-checkbox send-user-to-pro">
                                    <input type="hidden" class="sr-only" name="customize_folders[folders_media_cleaning]" value="<?php echo esc_attr($show_in_page) ?>" />
                                    <input disabled type="checkbox" class="sr-only" id="folders_media_cleaning" value="off">
                                    <span></span>
                                </label>
                                <label for="" class="send-user-to-pro">
                                    <?php esc_html_e("Use Media Cleaning to clear unused media files", "folders"); ?>
                                    <span class="folder-tooltip" data-title="<?php esc_html_e("The Media Cleaning feature enables you to clean unused media files for your WordPress site and adds a Media Cleaning item under Media", "folders") ?>"><span class="dashicons dashicons-editor-help"></span></span>
                                    <button type="button" class="upgrade-link" href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>"><?php esc_html_e("Upgrade to Pro", 'folders') ?></button>
                                </label>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 15px 10px 15px 0px" colspan="4">
                            <input type="hidden" name="folders_settings1" value="folders">
                            <?php
                            $show_media_details = !isset($customize_folders['show_media_details']) ? "on" : $customize_folders['show_media_details'];
                            $show_media_details = "off";
                            ?>
                            <a class="upgrade-box-link" target="_blank" href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>" >
                                <label for="" class="custom-checkbox send-user-to-pro">
                                    <input disabled type="checkbox" class="sr-only" name="customize_folders[show_media_details]" id="show_media_details" value="on" <?php checked($show_media_details, "on") ?>>
                                    <span></span>
                                </label>
                                <label for="" class="send-user-to-pro">
                                    <?php esc_html_e("Show media details on hover", "folders"); ?>
                                    <button type="button" class="upgrade-link" href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>"><?php esc_html_e("Upgrade to Pro", 'folders'); ?></button>
                                                    <span class="html-tooltip bottom">
                                                        <span class="dashicons dashicons-editor-help"></span>
                                                        <span class="tooltip-text top" style="">
                                                            <?php esc_html_e("Show useful metadata including title, size, type, date, dimension & more on hover.", "folders"); ?>
                                                            <img src="<?php echo esc_url(WCP_FOLDER_URL."assets/images/folders-media.gif") ?>">
                                                        </span>
                                                    </span>
                                    <span class="recommanded"><?php esc_html_e("Recommended", "folders") ?></span>
                                </label>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <div class="">
                                <div class="">
                                    <?php
                                    $media_settings     = [
                                        'image_title'       => [
                                            "title"   => esc_html__("Title", "folders"),
                                            "default" => "on",
                                        ],
                                        'image_alt_text'    => [
                                            "title"   => esc_html__("Alternative Text", "folders"),
                                            "default" => "off",
                                        ],
                                        'image_file_url'    => [
                                            "title"   => esc_html__("File URL", "folders"),
                                            "default" => "off",
                                        ],
                                        'image_dimensions'  => [
                                            "title"   => esc_html__("Dimensions", "folders"),
                                            "default" => "on",
                                        ],
                                        'image_size'        => [
                                            "title"   => esc_html__("Size", "folders"),
                                            "default" => "off",
                                        ],
                                        'image_file_name'   => [
                                            "title"   => esc_html__("Filename", "folders"),
                                            "default" => "off",
                                        ],
                                        'image_type'        => [
                                            "title"   => esc_html__("Type", "folders"),
                                            "default" => "on",
                                        ],
                                        'image_date'        => [
                                            "title"   => esc_html__("Date", "folders"),
                                            "default" => "on",
                                        ],
                                        'image_uploaded_by' => [
                                            "title"   => esc_html__("Uploaded by", "folders"),
                                            "default" => "off",
                                        ],
                                    ];
                                    $media_col_settings = isset($customize_folders['media_col_settings']) && is_array($customize_folders['media_col_settings']) ? $customize_folders['media_col_settings'] : [
                                        "image_title",
                                        "image_dimensions",
                                        "image_type",
                                        "image_date",
                                    ];
                                    ?>
                                    <input type="hidden" name="customize_folders[media_col_settings][]" value="all">
                                    <div class="media-setting-box active send-user-to-pro" >
                                        <div class="normal-box">
                                            <select disabled multiple="multiple" name="customize_folders[media_col_settings][]" class="select2-box">
                                                <?php foreach ($media_settings as $key => $media) {
                                                    $selected = $media['default'];
                                                    ?>
                                                    <option <?php selected($selected, "on") ?> value="<?php echo esc_attr($key) ?>"><?php echo esc_attr($media['title']) ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <a class="upgrade-box" target="_blank" href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>" >
                                            <button type="button"><?php esc_html_e("Upgrade to Pro", 'folders'); ?></button>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php
                    $show_in_page = !isset($customize_folders['folders_enable_replace_media']) ? "yes" : $customize_folders['folders_enable_replace_media'];
                    ?>
                    <tr>
                        <td class="no-padding">
                            <input type="hidden" name="customize_folders[folders_enable_replace_media]" value="no">
                            <label for="folders_enable_replace_media" class="custom-checkbox">
                                <input id="folders_enable_replace_media" class="sr-only" <?php checked($show_in_page, "yes") ?> type="checkbox" name="customize_folders[folders_enable_replace_media]" value="yes">
                                <span></span>
                            </label>
                        </td>
                        <td colspan="3" class="enable-replace-media">
                            <label for="folders_enable_replace_media" ><?php esc_html_e('Enable Replace Media', 'folders'); ?>
                                <span class="html-tooltip no-position top">
                                                    <span class="dashicons dashicons-editor-help"></span>
                                                    <span class="tooltip-text top" style="">
                                                        <?php esc_html_e("The Replace Media feature will allow you to replace your media files throughout your website with the click of a button,  which means the file will be replaced for all your posts, pages, etc", "folders") ?>
                                                        <span class="new"><?php printf(esc_html__("%1\$sPro version ✨%2\$s includes updating all previous links of the file in the database, changing dates &  more", "folders"), "<a href='".esc_url($this->getFoldersUpgradeURL())."' target='_blank'>", "</a>") ?></span>
                                                    </span>
                                                </span>
                                <span class="recommanded"><?php esc_html_e("Recommended", "folders") ?></span>
                            </label>
                        </td>
                    </tr>
                    <?php
                    $show_in_page = !isset($customize_folders['show_folder_in_settings']) ? "no" : $customize_folders['show_folder_in_settings'];
                    ?>
                    <tr>
                        <td class="no-padding">
                            <input type="hidden" name="customize_folders[show_folder_in_settings]" value="no">
                            <label for="show_folder_in_settings" class="custom-checkbox">
                                <input id="show_folder_in_settings" class="sr-only" <?php checked($show_in_page, "yes") ?> type="checkbox" name="customize_folders[show_folder_in_settings]" value="yes">
                                <span></span>
                            </label>
                        </td>
                        <td colspan="3">
                            <label for="show_folder_in_settings" ><?php esc_html_e('Place the Folders settings page nested under "Settings"', 'folders'); ?></label>
                                            <span class="folder-tooltip" data-title="<?php esc_html_e("When this setting is enabled, you will be able to access the Folders settings page by clicking on 'Settings' in the WordPress dashboard and then selecting 'Folders' from the submenu.", "folders") ?>">
                                                <span class="dashicons dashicons-editor-help"></span>
                                            </span>
                        </td>
                    </tr>
                    <?php $val = get_option("folders_show_in_menu"); ?>
                    <input type="hidden" name="folders_show_in_menu" value="off" />
                    <tr>
                        <td width="20" class="no-padding">
                            <label for="folders_show_in_menu" class="custom-checkbox">
                                <input class="sr-only" type="checkbox" id="folders_show_in_menu" name="folders_show_in_menu" value="on" <?php checked($val, "on") ?>/>
                                <span></span>
                            </label>
                        </td>
                        <td colspan="3">
                            <label for="folders_show_in_menu" ><?php esc_html_e('Show the folders also in WordPress menu', 'folders'); ?></label>
                                            <span class="html-tooltip bottom">
                                                <span class="dashicons dashicons-editor-help"></span>
                                                <span class="tooltip-text top tooltip-image-height" style="">
                                                    <?php esc_html_e("Show folders separately on your WordPress sidebar for quick and easy access to folders of Media, Posts, Pages etc", "folders"); ?>
                                                    <img style="width: auto; margin: 0 auto" src="<?php echo esc_url(WCP_FOLDER_URL."assets/images/page-folders.png") ?>">
                                                </span>
                                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 15px 10px 15px 0px" colspan="4">
                            <?php $replace_media_title = "off"; ?>
                            <a class="upgrade-box-link" target="_blank" href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>" >
                                <label for="" class="custom-checkbox send-user-to-pro">
                                    <input disabled type="checkbox" class="sr-only" name="customize_folders[replace_media_title]" id="replace_media_title" value="on" <?php checked($replace_media_title, "on") ?>>
                                    <span></span>
                                </label>
                                <label for="" class="send-user-to-pro">
                                    <?php esc_html_e("Auto Rename file based on title", "folders"); ?>
                                    <span class="folder-tooltip" data-title="<?php esc_html_e("Replace the actual file name of media files with the title from the WordPress editor.", "folders") ?>"><span class="dashicons dashicons-editor-help"></span></span>
                                    <button type="button" class="upgrade-link" ><?php esc_html_e("Upgrade to Pro", 'folders') ?></button>
                                </label>
                            </a>
                        </td>
                    </tr>
                    <!-- Do not make changes here, Only for Free -->
                </tboby>
            </table>
            <input type="hidden" name="customize_folders[show_media_details]" value="off">
        </div>
        <div class="accordion-right">
            <div class="premio-help">
                <a href="https://premio.io/help/folders/?utm_source=pluginspage" target="_blank">
                    <div class="premio-help-btn">
                        <img src="<?php echo esc_url(WCP_FOLDER_URL."assets/images/premio-help.png") ?>" alt="Premio Help" class="Premio Help" />
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
                        <div class="visit-our"><a target="_blank" href="https://premio.io/help/folders/how-to-activate-folders-for-wordpress-com-media-library/">Enable Folders</a> for your Media Library</div>
                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="clear"></div>
        <div class="submit-button">
            <?php submit_button(); ?>
        </div>
    </div>
</div>