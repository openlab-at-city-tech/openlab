<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
$fontList = \Folders\Admin\DefaultSettings::get_font_list();
$hasValidKey = \Folders\Admin\License::is_license_active();
$settings = \Folders\Admin\Settings::get_field_settings('customization_settings');
$settings['folder_size'] = 16;
$fontSizes = \Folders\Admin\DefaultSettings::get_pro_font_size();
$setting_font = '';
$font = '';
$upgradeURL = \Folders\Admin\License::get_pro_url();
$active_tab = apply_filters( 'folders_active_tab', 'general-settings' );
?>
<div id="customize-folders" class="folders-tab <?php echo esc_attr($active_tab == 'customize-folders' ? 'active' : ''); ?>">
    <form id="customize-folders-form" class="folder-form" method="post" action="">
        <div class="flex flex-col sm:flex-row gap-6">
            <div class="flex-1">
                <div class="flex flex-col gap-6">
                    <div class="flex flex-col gap-3">
                        <div class="flex flex-col md:flex-row gap-3 sm:justify-between sm:items-center">
                            <div>
                                <label for="folders-pro-font" class="text-base text-grey900"><?php esc_html_e('Folders font', 'folders'); ?></label>
                            </div>
                            <div class="flex-1 max-w-60">
                                <select id="folders-pro-font" class="folders-font" name="customization_settings[folder_font]" <?php echo $hasValidKey ? '' : 'aria-describedby="folders-appearance-plan-note"'; ?>>
                                    <?php
                                    $group = '';
                                    $index = 0;
                                    foreach ($fontList as $key => $value) :
                                        $title = $key;
                                        if ($index == 0) {
                                            $key = "";
                                        }

                                        $index++;
                                        if ($value != $group) {
                                            echo '<optgroup label="' . esc_attr($value) . '">';
                                            $group = $value;
                                        }

                                        if (in_array($setting_font, ["Arial", "Tahoma", "Verdana", "Helvetica", "Times New Roman", "Trebuchet MS", "Georgia"]) || $value != "Google Fonts") { ?>
                                            <option <?php selected($settings['folder_font'], $key) ?> value="<?php echo esc_attr($key); ?>" <?php selected($font, $key); ?>><?php echo esc_attr($title); ?></option>
                                        <?php } else { ?>
                                            <option class="pro-select-item" value="folders-pro"><?php echo esc_attr($title); ?></option>
                                        <?php } ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="flex flex-col md:flex-row gap-3 sm:justify-between sm:items-center">
                            <div>
                                <label for="folders-size" class="text-base text-grey900"><?php esc_html_e('Folders size', 'folders'); ?></label>
                            </div>
                            <div class="flex-1 max-w-60">
                                <select id="folders-size" class="folders-size" name="customization_settings[folder_size]" data-minimum-results-for-search="Infinity" <?php echo $hasValidKey ? '' : 'aria-describedby="folders-appearance-plan-note"'; ?>>
                                    <?php foreach ($fontSizes as $key => $value) :
                                        $key = $key == 16 ? 16 : 'folders-pro';
                                        ?>
                                        <option <?php selected($settings['folder_size'], $key) ?> value="<?php echo esc_attr($key); ?>" <?php selected($font, $key); ?>>
                                            <?php echo esc_attr($value); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="<?php echo esc_attr($settings['folder_size'] == 'custom' ? 'flex': 'hidden') ?> gap-3 justify-between items-center" id="custom-font-size-wrap">
                            <div class="opacity-0">
                                <label for="folders-custom-size" class="text-base text-grey900"><?php esc_html_e('Custom size', 'folders'); ?></label>
                            </div>
                            <div class="flex-1 max-w-60">
                                <input class="w-full m-0! flex" name="customization_settings[custom_font_size]" value="<?php echo esc_attr($settings['custom_font_size']) ?>" id="folders-custom-size" type="hidden" min="10" max="24" step="1" >
                                <div class="flex gap-3 items-center">
                                    <div class="text-sx text-grey900">A</div>
                                    <div class="flex-1">
                                        <div id="slider-range-max"></div>
                                    </div>
                                    <div class="text-2xl text-grey900">A</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                    $args = [
                        'id' => 'new_folder_color',
                        'name' => 'new_folder_color',
                        'value' => $settings['new_folder_color'],
                        'title' => sprintf(esc_html__('"%s" button color', 'folders'), "<b>".esc_html__('New Folder', 'folders')."</b>"),
                        'is_pro' => true,
                        'is_valid' => $hasValidKey,
                        'upgrade_url' => $upgradeURL
                    ];
                    do_action('premio_folders_custom_color', $args);

                    $args = [
                        'id' => 'bulk_organize_button_color',
                        'name' => 'bulk_organize_button_color',
                        'value' => $settings['bulk_organize_button_color'],
                        'title' => sprintf(esc_html__('"%s" button color', 'folders'), "<b>".esc_html__('Bulk Organize', 'folders')."</b>"),
                        'is_pro' => true,
                        'is_valid' => $hasValidKey,
                        'upgrade_url' => $upgradeURL
                    ];
                    do_action('premio_folders_custom_color', $args);

                    $args = [
                        'id' => 'media_replace_button',
                        'name' => 'media_replace_button',
                        'value' => $settings['media_replace_button'],
                        'title' => sprintf(esc_html__('"%s" button color', 'folders'), "<b>".esc_html__('Replace File', 'folders')."</b>"),
                        'is_pro' => true,
                        'is_valid' => $hasValidKey,
                        'upgrade_url' => $upgradeURL
                    ];
                    do_action('premio_folders_custom_color', $args);

                    $args = [
                        'id' => 'dropdown_color',
                        'name' => 'dropdown_color',
                        'value' => $settings['dropdown_color'],
                        'title' => esc_html__('Dropdown color', 'folders'),
                        'is_pro' => true,
                        'is_valid' => $hasValidKey,
                        'upgrade_url' => $upgradeURL
                    ];
                    do_action('premio_folders_custom_color', $args);

                    $args = [
                        'id' => 'folder_bg_color',
                        'name' => 'folder_bg_color',
                        'value' => $settings['folder_bg_color'],
                        'title' => esc_html__('Folders background color', 'folders'),
                        'is_pro' => true,
                        'is_valid' => $hasValidKey,
                        'upgrade_url' => $upgradeURL
                    ];
                    do_action('premio_folders_custom_color', $args);

                    $args = [
                        'id' => 'folder_bg_color',
                        'name' => 'folder_bg_color',
                        'folder_colors' => $settings['folder_colors'],
                        'default_icon_color' => $settings['default_icon_color'],
                        'title' => esc_html__('Default icon color', 'folders'),
                        'is_pro' => true,
                        'is_valid' => $hasValidKey,
                        'upgrade_url' => $upgradeURL
                    ];
                    do_action('premio_folders_color_settings', $args);
                    ?>
                </div>
            </div>
            <div>
                <div class="w-full sm:w-70">
                    <div class="grey900 text-base font-normal pb-1"><?php esc_html_e('Preview', 'folders'); ?></div>
                    <div class="grey900 text-xs pb-2.5"><?php esc_html_e('See the full functionality on your media library, posts, pages, and custom posts', 'folders'); ?></div>
                    <div class="rounded-lg border-1 border-grey300 w-full p-3 outline-[#FEE4E2]! outline-4! outline-offset-0! folders-font-family">
                        <div class="flex flex-col gap-3">
                            <div class="flex items-center justify-between">
                                <div class="text-xl font-semibold text-grey900"><?php esc_html_e('Folders', 'folders'); ?></div>
                                <div>
                                    <button type="button" class="folders-new-folder-bg-color py-2 px-3 flex items-center gap-1.5 rounded-lg text-white! text-sm! font-semibold!">
                                        <span class="text-sm!"><i class="pfolder-add-folder"></i></span>
                                        <span class=""><?php esc_html_e('New Folder', 'folders'); ?></span>
                                    </button>
                                </div>
                            </div>
                            <div class="relative flex items-center gap-2">
                                <button type="button" class="border-1 border-grey300 rounded-lg py-1.5 px-2.5 text-sm! font-normal! inline-flex items-center gap-2">
                                    <i class="pfolder-arrow-down text-[6px]"></i>
                                    <?php esc_html_e('Expand', 'folders'); ?>
                                </button>
                                <button type="button" class="border-1 border-grey300 rounded-lg py-1.5 px-2.5 text-sm! font-normal! inline-flex items-center gap-2">
                                    <i class="pfolder-arrow-sort text-sm"></i>
                                    <?php esc_html_e('Sort', 'folders'); ?>
                                </button>
                            </div>
                            <div class="border-1 border-grey300 rounded-lg flex items-center gap-2 py-2 px-3">
                                <div class="w-4 h-4">
                                    <svg class="w-4 h-4" width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M15.833 15.834L12.208 12.209M14.1663 7.50065C14.1663 11.1825 11.1816 14.1673 7.49967 14.1673C3.81778 14.1673 0.833008 11.1825 0.833008 7.50065C0.833008 3.81875 3.81778 0.833984 7.49967 0.833984C11.1816 0.833984 14.1663 3.81875 14.1663 7.50065Z" stroke="#717680" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                <div class="flex-1 text-grey500 text-sm">
                                    <?php esc_html_e('Search', 'folders'); ?>
                                </div>
                            </div>
                            <div class="flex flex-col folders-font-family">
                                <div class="flex items-center justify-between gap-2 px-2 py-1.5 rounded folders-folder-bg-color cursor-pointer">
                                    <div class="folders-font-size folders-font-family text-white font-normal"><?php esc_html_e('All Files', 'folders'); ?></div>
                                    <div class="text-white text-xs">215</div>
                                </div>
                                <div class="flex items-center justify-between gap-2 px-2 py-1.5 rounded cursor-pointer">
                                    <div class="folders-font-size folders-font-family text-grey900 font-normal"><?php esc_html_e('Unassigned Files', 'folders'); ?></div>
                                    <div class="text-grey900 text-xs">191</div>
                                </div>
                            </div>
                            <div class="h-px w-full bg-grey300"></div>
                            <div class="flex flex-col">
                                <div class="flex items-center justify-between gap-2 px-2 py-1.5 rounded">
                                    <div class="folders-font-size folders-font-family text-grey900 font-normal flex items-center gap-1 cursor-pointer">
                                        <i class="pfolder-folders-close folders-icon-color"></i>
                                        <?php esc_html_e('Folder 1', 'folders'); ?>
                                    </div>
                                    <div class="text-grey900 text-xs">20</div>
                                </div>
                                <div class="flex items-center justify-between gap-2 px-2 py-1.5 rounded cursor-pointer">
                                    <div class="folders-font-size folders-font-family text-grey900 font-normal flex items-center gap-1">
                                        <i class="pfolder-folders-close folders-icon-color"></i>
                                        <?php esc_html_e('Folder 2', 'folders'); ?>
                                    </div>
                                    <div class="text-grey900 text-xs">13</div>
                                </div>
                                <div class="flex items-center justify-between gap-2 px-2 py-1.5 rounded cursor-pointer">
                                    <div class="folders-font-size folders-font-family text-grey900 font-normal flex items-center gap-1">
                                        <i class="pfolder-folders-close folders-icon-color"></i>
                                        <?php esc_html_e('Folder 3', 'folders'); ?>
                                    </div>
                                    <div class="text-grey900 text-xs">5</div>
                                </div>
                            </div>
                            <div class="h-px w-full bg-grey300"></div>
                            <div class="flex items-center justify-between gap-2 px-2 py-1.5 rounded">
                                <div class="flex-1">
                                    <select class="w-full h-9 rounded border-1 folders-dropdown-color text-sm text-grey900">
                                        <option selected><?php esc_html_e('All Files', 'folders'); ?></option>
                                        <option><?php esc_html_e('Folder 1', 'folders'); ?></option>
                                        <option><?php esc_html_e('Folder 2', 'folders'); ?></option>
                                        <option><?php esc_html_e('Folder 3', 'folders'); ?></option>
                                    </select>
                                </div>
                                <div class="flex-1">
                                    <button type="button" class="folders-bulk-organize-bg-color py-2 px-3 flex items-center gap-1.5 rounded text-white! text-sm!">
                                        <span class=""><?php esc_html_e('Bulk Organize', 'folders'); ?></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="h-px bg-grey300 w-full my-6"></div>
        <div class="flex flex-col gap-4">
            <div class="font-semibold text-base text-grey900"><?php esc_attr_e('Advanced options', 'folders'); ?></div>
            <?php
            $fields = [
                'enable_horizontal_scroll'           => [
                    'label'          => esc_html__( 'Enable Horizontal Scroll', 'folders'),
                    'name'           => 'customization_settings[enable_horizontal_scroll]',
                    'type'           => 'checkbox',
                    'id'             => 'enable_horizontal_scroll',
                    'value'          => 1,
                    'has_tooltip'    => true,
                    'tooltip'        => esc_html__( 'When a folder has too much text or you have many levels of sub-folders, a horizontal scroll bar will appear on the bottom to scroll & view Folder names.', 'folders'),
                    'is_recommended' => false,
                    'label_class'    => '',
                    'is_pro'         => false,
                    'field_class'    => '',
                ],
                'show_in_page'           => [
                        'label'          => esc_html__( 'Show Folders in Upper Position', 'folders'),
                        'name'           => 'customization_settings[show_in_page]',
                        'type'           => 'checkbox',
                        'id'             => 'show_in_page',
                        'value'          => 1,
                        'has_tooltip'    => true,
                        'tooltip'        => esc_html__( "The list of your folders will also appear at the top of the page, e.g. under 'Media library'.", 'folders'),
                        'is_recommended' => false,
                        'label_class'    => '',
                        'is_pro'         => true,
                        'field_class'    => '',
                ]
            ];
            foreach ($fields as $key => $setting) { ?>
                <div id="setting-<?php echo esc_attr($setting['id']) ?>" class="folder-single-field-items folder-checkbox-field folder-flex folder-gap-8 folder-flex-row folder-align-center <?php echo esc_attr($setting['field_class']) ?>">
                    <?php do_action('folders_field_label', $setting); ?>
                    <div class="folder-field-wrap">
                        <?php do_action('folders_field_prefix_settings', $setting, 'no'); ?>
                        <?php do_action('folders_field_input', $setting, $settings[$key], $hasValidKey, $upgradeURL); ?>
                        <?php do_action('folders_field_postfix_settings', $setting, 'no'); ?>
                    </div>
                    <?php do_action('folders_field_after_'.$key, $setting, $settings, false, ''); ?>
                </div>
            <?php }
            ?>
        </div>
        <input type="hidden" name="action" value="save_folders_customization">
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('save_folders_customization') ?>">
    </form>
</div>
