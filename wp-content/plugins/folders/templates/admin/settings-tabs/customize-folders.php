<div class="tab-content <?php echo esc_attr(($setting_page == "customize-folders") ? "active" : "") ?>" id="customize-folders">
    <div class="accordion-content">
        <div class="accordion-left">
            <table class="form-table">
                <?php
                $colors        = [
                    "#FA166B",
                    "#0073AA",
                    "#484848",
                ];
                $color         = !isset($customize_folders['new_folder_color'])||empty($customize_folders['new_folder_color']) ? "#FA166B" : $customize_folders['new_folder_color'];
                $setting_color = WCP_Folders::check_for_setting("new_folder_color", "customize_folders");
                ?>
                <tr>
                    <td width="255px" class="no-padding">
                        <a class="upgrade-box-link" target="_blank" href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>" >
                            <label for="new_folder_color" ><b>"New Folder"</b> button color <button type="button" class="upgrade-link" ><?php esc_html_e("Upgrade to Pro", 'folders'); ?></button></label>
                        </a>
                    </td>
                    <td>
                        <ul class="color-list">
                            <?php $field_name = "new_folder_color"; foreach ($colors as $key => $value) { ?>
                                <li>
                                    <label class="color-checkbox <?php echo ($color == $value) ? "active" : "" ?>" for="<?php echo esc_attr($field_name)."-".esc_attr($key) ?>">
                                        <input type="radio" id="<?php echo esc_attr($field_name)."-".esc_attr($key) ?>" name="customize_folders[<?php echo esc_attr($field_name) ?>]" class="sr-only checkbox-color" value="<?php echo esc_attr($value) ?>" <?php checked($color, $value) ?> />
                                        <span style="background: <?php echo esc_attr($value) ?>"></span>
                                    </label>
                                </li>
                            <?php } $key = 3; ?>
                            <?php if ($setting_color !== false && $setting_color != "#FA166B") { ?>
                                <li>
                                    <label class="color-checkbox <?php echo ($color == $setting_color) ? "active" : "" ?>" for="<?php echo esc_attr($field_name)."-".esc_attr($key) ?>">
                                        <input type="radio" id="<?php echo esc_attr($field_name)."-".esc_attr($key) ?>" name="customize_folders[<?php echo esc_attr($field_name) ?>]" class="sr-only checkbox-color" value="<?php echo esc_attr($setting_color) ?>" <?php checked($color, $setting_color) ?> />
                                        <span style="background: <?php echo esc_attr($setting_color) ?>"></span>
                                    </label>
                                </li>
                            <?php } ?>
                            <li>
                                <a class="upgrade-box-link d-block" target="_blank" href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>" >
                                    <span class="color-box"><span class="gradient"></span> <span class="color-box-area"><?php echo esc_html_e("Custom", "folders") ?></span></span>
                                    <span class="upgrade-link"><?php echo esc_html_e("Upgrade to Pro", "folders") ?></span>
                                </a>
                            </li>
                        </ul>
                    </td>
                    <td rowspan="4" >

                    </td>
                </tr>
                <?php
                $color         = !isset($customize_folders['bulk_organize_button_color'])||empty($customize_folders['bulk_organize_button_color']) ? "#FA166B" : $customize_folders['bulk_organize_button_color'];
                $setting_color = WCP_Folders::check_for_setting("bulk_organize_button_color", "customize_folders");
                ?>
                <tr>
                    <td class="no-padding">
                        <a class="upgrade-box-link" target="_blank" href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>" >
                            <label for="bulk_organize_button_color" ><b>"Bulk Organize"</b> button color <button type="button" class="upgrade-link" ><?php esc_html_e("Upgrade to Pro", 'folders'); ?></button></label>
                        </a>
                    </td>
                    <td>
                        <ul class="color-list">
                            <?php $field_name = "bulk_organize_button_color"; foreach ($colors as $key => $value) { ?>
                                <li>
                                    <label class="color-checkbox <?php echo ($color == $value) ? "active" : "" ?>" for="<?php echo esc_attr($field_name)."-".esc_attr($key) ?>">
                                        <input type="radio" id="<?php echo esc_attr($field_name)."-".esc_attr($key) ?>" name="customize_folders[<?php echo esc_attr($field_name) ?>]" class="sr-only checkbox-color" value="<?php echo esc_attr($value) ?>" <?php checked($color, $value) ?> />
                                        <span style="background: <?php echo esc_attr($value) ?>"></span>
                                    </label>
                                </li>
                            <?php } $key = 3; ?>
                            <?php if ($setting_color !== false && $setting_color != "#FA166B") { ?>
                                <li>
                                    <label class="color-checkbox <?php echo ($color == $setting_color) ? "active" : "" ?>" for="<?php echo esc_attr($field_name)."-".esc_attr($key) ?>">
                                        <input type="radio" id="<?php echo esc_attr($field_name)."-".esc_attr($key) ?>" name="customize_folders[<?php echo esc_attr($field_name) ?>]" class="sr-only checkbox-color" value="<?php echo esc_attr($setting_color) ?>" <?php checked($color, $setting_color) ?> />
                                        <span style="background: <?php echo esc_attr($setting_color) ?>"></span>
                                    </label>
                                </li>
                            <?php } ?>
                            <li>
                                <a class="upgrade-box-link d-block" target="_blank" href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>" >
                                    <span class="color-box"><span class="gradient"></span> <span class="color-box-area"><?php echo esc_html_e("Custom", "folders") ?></span></span>
                                    <span class="upgrade-link"><?php echo esc_html_e("Upgrade to Pro", "folders") ?></span>
                                </a>
                            </li>
                        </ul>
                    </td>
                </tr>
                <?php
                $color         = !isset($customize_folders['media_replace_button'])||empty($customize_folders['media_replace_button']) ? "#FA166B" : $customize_folders['media_replace_button'];
                $setting_color = WCP_Folders::check_for_setting("media_replace_button", "customize_folders");
                ?>
                <tr>
                    <td class="no-padding">
                        <a class="upgrade-box-link" target="_blank" href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>" >
                            <label for="media_replace_button" ><b>"Replace File"</b> media library button <button type="button" class="upgrade-link" ><?php esc_html_e("Upgrade to Pro", 'folders'); ?></button></label>
                        </a>
                    </td>
                    <td>
                        <ul class="color-list">
                            <?php $field_name = "media_replace_button"; foreach ($colors as $key => $value) { ?>
                                <li>
                                    <label class="color-checkbox <?php echo ($color == $value) ? "active" : "" ?>" for="<?php echo esc_attr($field_name)."-".esc_attr($key) ?>">
                                        <input type="radio" id="<?php echo esc_attr($field_name)."-".esc_attr($key) ?>" name="customize_folders[<?php echo esc_attr($field_name) ?>]" class="sr-only checkbox-color" value="<?php echo esc_attr($value) ?>" <?php checked($color, $value) ?> />
                                        <span style="background: <?php echo esc_attr($value) ?>"></span>
                                    </label>
                                </li>
                            <?php } $key = 3; ?>
                            <?php if ($setting_color !== false && $setting_color != "#FA166B") { ?>
                                <li>
                                    <label class="color-checkbox <?php echo ($color == $setting_color) ? "active" : "" ?>" for="<?php echo esc_attr($field_name)."-".esc_attr($key) ?>">
                                        <input type="radio" id="<?php echo esc_attr($field_name)."-".esc_attr($key) ?>" name="customize_folders[<?php echo esc_attr($field_name) ?>]" class="sr-only checkbox-color" value="<?php echo esc_attr($setting_color) ?>" <?php checked($color, $setting_color) ?> />
                                        <span style="background: <?php echo esc_attr($setting_color) ?>"></span>
                                    </label>
                                </li>
                            <?php } ?>
                            <li>
                                <a class="upgrade-box-link d-block" target="_blank" href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>" >
                                    <span class="color-box"><span class="gradient"></span> <span class="color-box-area"><?php echo esc_html_e("Custom", "folders") ?></span></span>
                                    <span class="upgrade-link"><?php echo esc_html_e("Upgrade to Pro", "folders") ?></span>
                                </a>
                            </li>
                        </ul>
                    </td>
                </tr>
                <?php
                $color         = !isset($customize_folders['dropdown_color'])||empty($customize_folders['dropdown_color']) ? "#484848" : $customize_folders['dropdown_color'];
                $setting_color = WCP_Folders::check_for_setting("dropdown_color", "customize_folders");
                ?>
                <tr>
                    <td class="no-padding">
                        <a class="upgrade-box-link" target="_blank" href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>" >
                            <label for="dropdown_color" ><?php echo esc_html_e("Dropdown color", "folders") ?> <button type="button" class="upgrade-link" ><?php esc_html_e("Upgrade to Pro", 'folders'); ?></button></label>
                        </a>
                    </td>
                    <td>
                        <ul class="color-list">
                            <?php $field_name = "dropdown_color"; foreach ($colors as $key => $value) { ?>
                                <li>
                                    <label class="color-checkbox <?php echo ($color == $value) ? "active" : "" ?>" for="<?php echo esc_attr($field_name)."-".esc_attr($key) ?>">
                                        <input type="radio" id="<?php echo esc_attr($field_name)."-".esc_attr($key) ?>" name="customize_folders[<?php echo esc_attr($field_name) ?>]" class="sr-only checkbox-color" value="<?php echo esc_attr($value) ?>" <?php checked($color, $value) ?> />
                                        <span style="background: <?php echo esc_attr($value) ?>"></span>
                                    </label>
                                </li>
                            <?php } $key = 3; ?>
                            <?php if ($setting_color !== false && $setting_color != "#484848") { ?>
                                <li>
                                    <label class="color-checkbox <?php echo ($color == $setting_color) ? "active" : "" ?>" for="<?php echo esc_attr($field_name)."-".esc_attr($key) ?>">
                                        <input type="radio" id="<?php echo esc_attr($field_name)."-".esc_attr($key) ?>" name="customize_folders[<?php echo esc_attr($field_name) ?>]" class="sr-only checkbox-color" value="<?php echo esc_attr($setting_color) ?>" <?php checked($color, $setting_color) ?> />
                                        <span style="background: <?php echo esc_attr($setting_color) ?>"></span>
                                    </label>
                                </li>
                            <?php } ?>
                            <li>
                                <a class="upgrade-box-link d-block" target="_blank" href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>" >
                                    <span class="color-box"><span class="gradient"></span> <span class="color-box-area"><?php echo esc_html_e("Custom", "folders") ?></span></span>
                                    <span class="upgrade-link"><?php echo esc_html_e("Upgrade to Pro", "folders") ?></span>
                                </a>
                            </li>
                        </ul>
                    </td>
                </tr>
                <?php
                $color         = !isset($customize_folders['folder_bg_color'])||empty($customize_folders['folder_bg_color']) ? "#FA166B" : $customize_folders['folder_bg_color'];
                $setting_color = WCP_Folders::check_for_setting("folder_bg_color", "customize_folders");
                ?>
                <tr>
                    <td class="no-padding">
                        <a class="upgrade-box-link" target="_blank" href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>" >
                            <label for="folder_bg_color" ><?php echo esc_html_e("Folders background color", "folders") ?> <button type="button" class="upgrade-link" ><?php esc_html_e("Upgrade to Pro", 'folders'); ?></button></label>
                        </a>
                    </td>
                    <td>
                        <ul class="color-list">
                            <?php $field_name = "folder_bg_color"; foreach ($colors as $key => $value) { ?>
                                <li>
                                    <label class="color-checkbox <?php echo ($color == $value) ? "active" : "" ?>" for="<?php echo esc_attr($field_name)."-".esc_attr($key) ?>">
                                        <input type="radio" id="<?php echo esc_attr($field_name)."-".esc_attr($key) ?>" name="customize_folders[<?php echo esc_attr($field_name) ?>]" class="sr-only checkbox-color" value="<?php echo esc_attr($value) ?>" <?php checked($color, $value) ?> />
                                        <span style="background: <?php echo esc_attr($value) ?>"></span>
                                    </label>
                                </li>
                            <?php } $key = 3; ?>
                            <?php if ($setting_color !== false && $setting_color != "#FA166B") { ?>
                                <li>
                                    <label class="color-checkbox <?php echo ($color == $setting_color) ? "active" : "" ?>" for="<?php echo esc_attr($field_name)."-".esc_attr($key) ?>">
                                        <input type="radio" id="<?php echo esc_attr($field_name)."-".esc_attr($key) ?>" name="customize_folders[<?php echo esc_attr($field_name) ?>]" class="sr-only checkbox-color" value="<?php echo esc_attr($setting_color) ?>" <?php checked($color, $setting_color) ?> />
                                        <span style="background: <?php echo esc_attr($setting_color) ?>"></span>
                                    </label>
                                </li>
                            <?php } ?>
                            <li>
                                <a class="upgrade-box-link d-block" target="_blank" href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>" >
                                    <span class="color-box"><span class="gradient"></span> <span class="color-box-area"><?php echo esc_html_e("Custom", "folders") ?></span></span>
                                    <span class="upgrade-link"><?php echo esc_html_e("Upgrade to Pro", "folders") ?></span>
                                </a>
                            </li>
                        </ul>
                    </td>
                </tr>
                <tr class="default-icon-color">
                    <td class="no-padding">
                        <label for="enable_horizontal_scroll" >
                            <?php esc_html_e("Default icon color", 'folders'); ?>
                            <span class="folder-tooltip" data-title="<?php esc_html_e("You can set the default icon color for Folders from here. Each folder and subfolder can have a different color, which you can change in the folder settings.", "folders"); ?>"><span class="dashicons dashicons-editor-help"></span></span>
                        </label>
                    </td>
                    <?php
                    $default_icon_color = isset($customize_folders['default_icon_color'])?$customize_folders['default_icon_color']:"#334155";
                    $colors = ["#334155", "#86cd91", "#1E88E5", "#ff6060"];
                    ?>
                    <td colspan="2">
                        <div class="folder-colors">
                            <div class="folder-default-colors">
                                <?php foreach($colors as $key=>$color) {
                                    ?>
                                    <div class="folder-default-color" id="default-color-<?php echo esc_attr($key) ?>" data-id="<?php echo esc_attr($key) ?>">
                                        <input <?php checked($color, $default_icon_color) ?> id="icon-color-<?php echo esc_attr($key) ?>" name="customize_folders[default_icon_color]" class="sr-only" value="<?php echo esc_attr($color) ?>" type="radio" />
                                        <label for="icon-color-<?php echo esc_attr($key) ?>" style="background: <?php echo esc_attr($color) ?>"></label>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="clear"></div>
                        <a href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>" class="custom-icon-color-link"><?php esc_html_e("Add more colors (Pro)", "folders"); ?></a>
                    </td>
                </tr>
                <?php
                $font         = !isset($customize_folders['folder_font'])||empty($customize_folders['folder_font']) ? "" : $customize_folders['folder_font'];
                $setting_font = WCP_Folders::check_for_setting("folder_font", "customize_folders");
                $index        = 0;
                ?>
                <tr>
                    <td class="no-padding">
                        <label for="folder_font" >
                            <?php if ($setting_font !== false && $setting_font != "" && !in_array($setting_font, ["Arial", "Tahoma", "Verdana", "Helvetica", "Times New Roman", "Trebuchet MS", "Georgia", "System Stack"])) {
                                esc_html_e("Folders font", 'folders');
                            } else { ?>
                                <a class="upgrade-box-link" target="_blank" href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>" >
                                    <?php esc_html_e('Folders font', 'folders'); ?> <button type="button" class="upgrade-link" ><?php esc_html_e("Upgrade to Pro", 'folders'); ?></button>
                                </a>
                            <?php } ?>
                        </label>
                    </td>
                    <td colspan="2">
                        <select name="customize_folders[folder_font]" id="folder_font" >
                            <?php $group = '';
                            foreach ($fonts as $key => $value) :
                                $title = $key;
                                if ($index == 0) {
                                    $key = "";
                                }

                                $index++;
                                if ($value != $group) {
                                    echo '<optgroup label="'.esc_attr($value).'">';
                                    $group = $value;
                                }

                                if (($setting_font !== false && $setting_font != "" && !in_array($setting_font, ["Arial", "Tahoma", "Verdana", "Helvetica", "Times New Roman", "Trebuchet MS", "Georgia"])) || $value != "Google Fonts") { ?>
                                    <option value="<?php echo esc_attr($key); ?>" <?php selected($font, $key); ?>><?php echo esc_attr($title); ?></option>
                                <?php } else { ?>
                                    <option class="pro-select-item" value="folders-pro"><?php echo esc_attr($title); ?> (Pro) 🔑</option>
                                <?php } ?>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <?php
                $size        = ! isset($customize_folders['folder_size']) || empty($customize_folders['folder_size']) ? "16" : $customize_folders['folder_size'];
                $folder_size = WCP_Folders::check_for_setting("folder_size", "customize_folders");
                ?>
                <tr>
                    <td class="no-padding">
                        <label for="folder_size" >
                            <?php if ($folder_size === false || intval($folder_size) === 16) { ?>
                                <a class="upgrade-box-link" target="_blank" href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>" >
                                    <?php esc_html_e('Folders size', 'folders'); ?> <button type="button" class="upgrade-link" ><?php esc_html_e("Upgrade to Pro", 'folders'); ?></button>
                                </a>
                            <?php } else { ?>
                                <?php esc_html_e("Folders size", 'folders'); ?>
                            <?php } ?>
                        </label>
                    </td>
                    <td colspan="2">
                        <?php
                        if ($folder_size === false || intval($folder_size) == 16) {
                            $sizes = [
                                "folders-pro"      => "Small (Pro) 🔑",
                                "16"               => "Medium",
                                "folders-pro-item" => "Large (Pro) 🔑",
                                "folders-item-pro" => "Custom (Pro) 🔑",
                            ];
                            $size  = 16;
                        } else {
                            $sizes = [
                                "12" => "Small",
                                "16" => "Medium",
                                "20" => "Large",
                            ];
                        }
                        ?>
                        <select name="customize_folders[folder_size]" id="folder_size" >
                            <?php
                            foreach ($sizes as $key => $value) {
                                $selected = ($key == $size) ? "selected" : "";
                                echo "<option ".esc_attr($selected)." value='".esc_attr($key)."'>".esc_attr($value)."</option>";
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <?php $enable_horizontal_scroll = ! isset($customize_folders['enable_horizontal_scroll']) || empty($customize_folders['enable_horizontal_scroll']) ? "on" : $customize_folders['enable_horizontal_scroll']; ?>
                <tr>
                    <td class="no-padding">
                        <label for="enable_horizontal_scroll" >
                            <?php esc_html_e("Enable Horizontal Scroll", 'folders'); ?> <span class="folder-tooltip" data-title="<?php esc_html_e("When a folder has too much text or you have many levels of sub-folders, a horizontal scroll bar will appear on the bottom to scroll & view Folder names", "folders") ?>"><span class="dashicons dashicons-editor-help"></span></span>
                        </label>
                    </td>
                    <td colspan="2">
                        <input type="hidden" name="customize_folders[enable_horizontal_scroll]" value="off" />
                        <div class="inline-checkbox">
                            <label class="folder-switch " for="enable_horizontal_scroll">
                                <input type="checkbox" class="sr-only normal-input" name="customize_folders[enable_horizontal_scroll]" id="enable_horizontal_scroll" value="on" <?php checked($enable_horizontal_scroll, "on") ?>>
                                <div class="folder-slider normal round"></div>
                            </label>
                        </div>
                    </td>
                </tr>
                <?php
                $show_in_page = isset($customize_folders['show_in_page']) ? $customize_folders['show_in_page'] : "hide";
                $show_folder  = WCP_Folders::check_for_setting("show_in_page", "customize_folders");
                if (empty($show_in_page)) {
                    $show_in_page = "hide";
                }
                ?>
                <tr>
                    <td colspan="3" style="padding: 15px 20px 15px 0">
                        <input type="hidden" name="customize_folders[show_in_page]" value="hide">
                        <?php if ($show_folder === false || $show_folder === "hide") { ?>
                            <a class="upgrade-box-link" target="_blank" href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>" >
                                <label for="" class="custom-checkbox send-user-to-pro">
                                    <input disabled type="checkbox" class="sr-only" name="customize_folders[show_in_page]" id="show_in_page" value="on" <?php checked($show_in_page, "show") ?>>
                                    <span></span>
                                </label>
                                <label for="" class="send-user-to-pro">
                                    <?php esc_html_e("Show Folders in upper position", "folders"); ?>
                                    <span class="folder-tooltip" data-title="<?php esc_html_e("The list of your folders will also appear at the top of the page, e.g. under 'Media library'.", "folders"); ?>">
                                                        <span class="dashicons dashicons-editor-help"></span>
                                                    </span>
                                    <button type="button" class="upgrade-link" href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>"><?php esc_html_e("Upgrade to Pro", 'folders'); ?></button>
                                </label>
                            </a>
                        <?php } else { ?>
                            <div class="custom-checkbox">
                                <input id="show_folders" class="sr-only" <?php checked($show_in_page, "show") ?> type="checkbox" name="customize_folders[show_in_page]" value="show">
                                <span></span>
                            </div>
                            <label for="show_folders">
                                <?php esc_html_e("Show Folders in upper position", 'folders'); ?>
                                <span class="folder-tooltip" data-title="<?php esc_html_e("The list of your folders will also appear at the top of the page, e.g. under 'Media library'.", "folders"); ?>">
                                                    <span class="dashicons dashicons-editor-help"></span>
                                                </span>
                            </label>
                        <?php } ?>
                    </td>
                </tr>
            </table>
        </div>
        <div class="accordion-right">
            <div class="preview-text">
                Preview
                <div class="preview-text-info"><?php esc_html_e("See the full functionality on your media library, posts, pages, and custom posts", 'folders'); ?></div>
            </div>
            <div class="preview-inner-box">
                <div class="preview-box">
                    <div class="wcp-custom-form">
                        <div class="form-title">
                            <?php esc_html_e("Folders", 'folders'); ?>
                            <a href="javascript:;" class="add-new-folder" id="add-new-folder">
                                <span class="create_new_folder"><i class="pfolder-add-folder"></i></span>
                                <span><?php esc_html_e("New Folder", 'folders'); ?></span>
                            </a>
                            <div class="clear"></div>
                        </div>
                        <div class="form-options">
                            <ul>
                                <li>
                                    <div class="custom-checkbox">
                                        <input type="checkbox" class="sr-only" >
                                        <span></span>
                                    </div>
                                </li>
                                <li>
                                    <a href="javascript:;" id="inline-update"><span class="icon pfolder-edit-folder"><span class="path2"></span></span> <span class="text"><?php esc_html_e("Rename", 'folders'); ?></span> </a>
                                </li>
                                <li>
                                    <a href="javascript:;" id="inline-remove"><span class="icon pfolder-remove"></span> <span class="text"><?php esc_html_e("Delete", 'folders'); ?></span> </a>
                                </li>
                                <li class="last">
                                    <a href="javascript:;" id="expand-collapse-list" data-tooltip="Expand"><span class="icon pfolder-arrow-down"></span></a>
                                </li>
                                <li class="last">
                                    <a href="javascript:;" ><span class="icon pfolder-arrow-sort"></span></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="shadow-box">
                        <div class="header-posts">
                            <a href="javascript:;" class="all-posts active-item-link"><?php esc_html_e("All Files", 'folders'); ?> <span class="total-count">215</span></a>
                        </div>
                        <div class="un-categorised-items  ui-droppable">
                            <a href="javascript:;" class="un-categorized-posts"><?php esc_html_e("Unassigned Files", 'folders'); ?> <span class="total-count total-empty">191</span> </a>
                        </div>
                        <div class="separator"></div>
                        <ul class="folder-list">
                            <li><a href="javascript:;"><i class="wcp-icon pfolder-folder-close"></i> <span><?php esc_html_e("Folder 1", 'folders'); ?></span><span class="total-count">20</span><span class="clear"></span></a></li>
                            <li><a href="javascript:;"><i class="wcp-icon pfolder-folder-close"></i> <span><?php esc_html_e("Folder 2", 'folders'); ?></span><span class="total-count">13</span><span class="clear"></span></a></li>
                            <li><a href="javascript:;"><i class="wcp-icon pfolder-folder-close"></i> <span><?php esc_html_e("Folder 3", 'folders'); ?></span><span class="total-count">5</span><span class="clear"></span></a></li>
                        </ul>
                        <div class="separator"></div>
                        <div class="media-buttons">
                            <select class="media-select">
                                <option><?php esc_html_e("All Files", 'folders'); ?></option>
                                <option><?php esc_html_e("Folder 1", 'folders'); ?></option>
                                <option><?php esc_html_e("Folder 2", 'folders'); ?></option>
                                <option><?php esc_html_e("Folder 3", 'folders'); ?></option>
                            </select>
                            <button type="button" class="button organize-button"><?php esc_html_e("Bulk Organize", 'folders'); ?></button>
                            <div style="clear: both;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="clear"></div>
        <?php submit_button(); ?>
    </div>
</div>