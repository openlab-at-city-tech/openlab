<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
$basicSettings = \Folders\Admin\FormFields::get_basic_settings();
$advanceSettings = \Folders\Admin\FormFields::get_advance_settings();
$hasValidKey = \Folders\Admin\License::is_license_active();
$upgradeURL = \Folders\Admin\License::get_pro_url();
$post_setting = apply_filters("check_for_folders_post_args", []);
$post_types = get_post_types($post_setting, 'objects');
$post_array = array("page", "post", "attachment");
$folders_settings = get_option('folders_settings', ['page', 'post', 'attachment']);
$options = is_array($folders_settings) ? $folders_settings : [];
$default_folders = get_option('default_folders', []);
$general_settings = \Folders\Admin\Settings::get_field_settings('general_settings');
$active_tab = apply_filters( 'folders_active_tab', 'general-settings' );
if(isset($post_types['shop_order'])) {
    unset($post_types['shop_order']);
}
?>
<div id="general-settings" class="folders-tab <?php echo esc_attr($active_tab == 'general-settings' ? 'active' : ''); ?>">
    <form id="general-settings-form" class="folder-form" method="post" action="">
        <div class="flex flex-col gap-3 mb-5">
            <div class="folders-field-wrap flex flex-col md:flex-row items-center gap-3">
                <div class="sm:min-w-65 flex items-center gap-1.5 w-full md:w-auto">
                    <label for="folders_post_type" class="text-sm text-grey"><?php esc_html_e("Use folders with", 'folders') ?></label>
                </div>
                <div class="flex-1 min-h-9.5 w-full md:w-auto">
                    <select class="hidden" id="folders_post_type" multiple name="folders_settings[]">
                        <?php foreach ($post_types as $post_type) {
                            if (!$post_type->show_ui) continue;
                            $selected = in_array($post_type->name, $options) ? "selected" : "";

                            if (in_array($post_type->name, $post_array)) {
                                echo "<option " . esc_attr($selected) . " value='" . esc_attr($post_type->name) . "' >" . esc_attr($post_type->label) . "</option>";
                            } else {
                                echo "<option class='pro-select-item' value='folders-pro' >" . esc_attr($post_type->label) . "</option>";
                            }
                            ?>

                        <?php }
                        echo "<option class='pro-select-item' value='folders-pro' class='pro-select-item' value='folders-pro'>" . esc_html__("Plugins", 'folders') . "</option>";
                        ?>
                    </select>
                </div>
            </div>
            <?php
            foreach ($post_types as $post_type) {
                if (!$post_type->show_ui) continue;
                $value = isset($default_folders[$post_type->name]) ? $default_folders[$post_type->name] : "";
                $hidden = in_array($post_type->name, $options) ? "" : "hidden";
                $folders = \Folders\Folders\FoldersTree::get_data_by_post_type($post_type->name);
                ?>
                <div class="folders-field-wrap flex items-center gap-3 flex-col md:flex-row default-folders <?php echo esc_attr($hidden) ?>" id="default-folder-for-<?php echo esc_attr($post_type->name); ?>" >
                    <div class="sm:min-w-65 flex items-center gap-1.5 w-full md:w-auto">
                        <label for="folders_for_<?php echo esc_attr($post_type->name); ?>" class="text-sm text-grey"><?php printf(esc_html__("Default Folder for %s", 'folders'), $post_type->label) ?></label>
                        <a target="_blank" href="#" class="custom-folder-link" id="link-for-<?php echo esc_attr($post_type->name); ?>">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false"><path d="M19.5 4.5h-7V6h4.44l-5.97 5.97 1.06 1.06L18 7.06v4.44h1.5v-7Zm-13 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-3H17v3a.5.5 0 0 1-.5.5h-10a.5.5 0 0 1-.5-.5v-10a.5.5 0 0 1 .5-.5h3V5.5h-3Z"></path></svg>
                        </a>
                    </div>
                    <div class="flex-1 min-h-9.5 w-full md:w-auto">
                        <?php
                        $all_items_link = \Folders\Folders\FoldersTree::get_term_url($post_type->name, '');
                        $unassigned_items_link = \Folders\Folders\FoldersTree::get_term_url($post_type->name, '-1');
                        ?>
                        <select data-folder="<?php echo esc_attr($post_type->name); ?>" class="folder-post-select hidden" id="folders_for_<?php echo esc_attr($post_type->name); ?>" name="default_folders[<?php echo esc_attr($post_type->name); ?>]">
                            <option value="" data-url="<?php echo esc_url($all_items_link) ?>"><?php printf(esc_html__("All %1\$s Folder", 'folders'), esc_attr($post_type->label)) ?></option>
                            <option value="-1" <?php selected($value, '-1') ?> data-url="<?php echo esc_url($unassigned_items_link) ?>" ><?php printf(esc_html__("Unassigned %1\$s", 'folders'), esc_attr($post_type->label)) ?></option>
                            <?php foreach ($folders as $folder) { ?>
                                <option class='pro-select-item' value='folders-pro'><?php echo esc_html($folder['text']) ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            <?php } ?>
            <?php
            $hidden = in_array('folders4plugins', $options) ? "" : "hidden";
            $all_items_link = \Folders\Folders\FoldersTree::get_term_url('folders4plugins', '');
            $unassigned_items_link = \Folders\Folders\FoldersTree::get_term_url('folders4plugins', '-1');
            $value = isset($default_folders['folders4plugins']) ? $default_folders['folders4plugins'] : "";
            ?>
            <div class="folders-field-wrap flex items-center gap-3 flex-col md:flex-row default-folders <?php echo esc_attr($hidden) ?>" id="default-folder-for-folders4plugins" >
                <div class="sm:min-w-65 flex items-center gap-1.5 w-full md:w-auto">
                    <label for="folders_for_folders4plugins" class="text-sm text-grey"><?php printf(esc_html__("Default Folder for %s", 'folders'), "Plugins") ?></label>
                    <a target="_blank" href="#" class="custom-folder-link" id="link-for-folders4plugins">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false"><path d="M19.5 4.5h-7V6h4.44l-5.97 5.97 1.06 1.06L18 7.06v4.44h1.5v-7Zm-13 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-3H17v3a.5.5 0 0 1-.5.5h-10a.5.5 0 0 1-.5-.5v-10a.5.5 0 0 1 .5-.5h3V5.5h-3Z"></path></svg>
                    </a>
                </div>
                <div class="flex-1 min-h-9.5 w-full md:w-auto">
                    <select data-folder="folders4plugins" class="folder-post-select hidden" id="folders_for_folders4plugins" name="default_folders[folders4plugins]">
                        <option value="" data-url="<?php echo esc_url($all_items_link) ?>"><?php printf(esc_html__("All %1\$s Folder", 'folders'), "Plugins") ?></option>
                        <option value="-1" <?php selected($value, '-1') ?> data-url="<?php echo esc_url($unassigned_items_link) ?>" ><?php printf(esc_html__("Unassigned %1\$s", 'folders'), "Plugins") ?></option>
                    </select>
                </div>
            </div>
        </div>
        <div class="folders-divider my-4 h-px bg-grey200"></div>
        <div class="flex flex-col gap-3">
            <?php
            foreach ($basicSettings as $key => $setting) { ?>
                <div id="setting-<?php echo esc_attr($setting['id']) ?>" class="folder-single-field-items folder-checkbox-field folder-flex folder-gap-8 folder-flex-row folder-align-center <?php echo esc_attr($setting['field_class']) ?>">
                    <?php do_action('folders_field_label', $setting); ?>
                    <div class="folder-field-wrap">
                        <?php do_action('folders_field_prefix_settings', $setting, 'no'); ?>
                        <?php do_action('folders_field_input', $setting, $general_settings[$key], $hasValidKey, $upgradeURL); ?>
                        <?php do_action('folders_field_postfix_settings', $setting, 'no'); ?>
                    </div>
                </div>
                <?php do_action('folders_field_after_'.$key, $setting, $general_settings, $hasValidKey, $upgradeURL); ?>
            <?php } ?>
        </div>
        <div class="folders-divider my-4 h-px bg-grey200"></div>
        <div class="folders-title text-grey text-base font-inter pb-5 font-semibold">Advanced options</div>
        <div class="flex flex-col gap-3">
            <?php foreach ($advanceSettings as $key => $setting) { ?>
                <div id="setting-<?php echo esc_attr($setting['id']) ?>" class="folder-single-field-items folder-checkbox-field folder-flex folder-gap-8 folder-flex-row folder-align-center <?php echo esc_attr($setting['field_class']) ?>">
                    <?php do_action('folders_field_label', $setting); ?>
                    <div class="folder-field-wrap">

                        <?php do_action('folders_field_prefix_settings', $setting, 'no'); ?>
                        <?php do_action('folders_field_input', $setting, $general_settings[$key], $hasValidKey, $upgradeURL); ?>
                        <?php do_action('folders_field_postfix_settings', $setting, 'no'); ?>
                    </div>

                    <?php do_action('folders_field_after_'.$key, $setting, $general_settings, false, ''); ?>
                </div>
            <?php } ?>
        </div>
        <input type="hidden" name="action" value="save_folders_general_settings">
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('save_folders_general_settings') ?>">
    </form>
</div>
