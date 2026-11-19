<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
$active_tab = apply_filters( 'folders_active_tab', 'general-settings' );
$plugin_info = \Folders\Folders\FoldersImport::get_plugin_information();
$is_exists = \Folders\Folders\FoldersImport::$is_exists;
?>
<div id="tools-and-maintenance" class="folders-tab <?php echo esc_attr($active_tab == 'tools-and-maintenance' ? 'active' : ''); ?>">
    <div class="flex gap-6 flex-col">
        <div class="flex sm:items-center gap-2 justify-start sm:justify-between flex-col sm:flex-row">
            <div class="flex gap-0.5 flex-col justify-start">
                <div class="text-base font-semibold text-grey900"><?php esc_html_e('Folders Import / Export', 'folders'); ?></div>
                <div class="text-sm text-[#717680]"><?php esc_html_e('Export or import folder structure within Folders plugin.', 'folders'); ?></div>
            </div>
            <div class="flex items-center gap-2">
                <button data-nonce="<?php echo wp_create_nonce('premio_folders_export') ?>" id="export-folders" type="button" class="import-export-button group form-button inline-flex whitespace-nowrap items-center gap-2 px-3.5 py-1.5 h-10 rounded-lg border border-solid border-[#D5D7DA]! text-base! font-semibold! text-grey900! hover:text-primary! hover:border-primary!">
                    <span class="folders-loader w-4! h-4! "></span>
                    <span class="w-4 h-4 inline-flex items-center default-icon">
                        <svg class="w-4 h-4" width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path class="group-hover:stroke-primary" d="M15.75 10.75V14.0833C15.75 14.5254 15.5744 14.9493 15.2618 15.2618C14.9493 15.5744 14.5254 15.75 14.0833 15.75H2.41667C1.97464 15.75 1.55072 15.5744 1.23816 15.2618C0.925595 14.9493 0.75 14.5254 0.75 14.0833V10.75M4.08333 6.58333L8.25 10.75M8.25 10.75L12.4167 6.58333M8.25 10.75V0.75" stroke="#717680" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <?php esc_html_e('Export', 'folders'); ?>
                </button>
                <label for="import_folders_file" type="button" class="group inline-flex whitespace-nowrap cursor-pointer items-center gap-2 px-3.5 py-1.5 h-10 rounded-lg border border-solid border-[#D5D7DA]! text-base! font-semibold! text-grey900! hover:text-primary! hover:border-primary!">
                    <input type="file" class="sr-only" name="importing_file" id="import_folders_file" accept="application/json" data-nonce="<?php echo wp_create_nonce('premio_import_folders') ?>">
                    <span class="w-4 h-4 inline-flex items-center">
                        <svg class="w-4 h-4" width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path class="group-hover:stroke-primary" d="M15.75 10.75V14.0833C15.75 14.5254 15.5744 14.9493 15.2618 15.2618C14.9493 15.5744 14.5254 15.75 14.0833 15.75H2.41667C1.97464 15.75 1.55072 15.5744 1.23816 15.2618C0.925595 14.9493 0.75 14.5254 0.75 14.0833V10.75M12.4167 4.91667L8.25 0.75M8.25 0.75L4.08333 4.91667M8.25 0.75V10.75" stroke="#717680" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <?php esc_html_e('Import', 'folders'); ?>
                </label>
            </div>
        </div>
        <?php if ($is_exists) { ?>
            <div id="import-external-plugins" class="flex sm:items-center gap-2 p-4 bg-[#F5FAFF] rounded-lg justify-start sm:justify-between flex-col sm:flex-row">
                <div class="flex gap-2 flex-col items-center justify-start sm:items-start">
                    <div class="flex gap-2 items-center">
                        <div class="text-base font-semibold text-grey900"><?php esc_html_e('External folders detected', 'folders'); ?></div>
                        <div class="bg-white inline-flex! text-[#2E90FA] text-xs rounded-2xl px-2 py-0.5"><?php esc_html_e('Recommended', 'folders'); ?></div>
                    </div>
                    <div class="text-sm text-[#717680]">

                        <b><?php esc_html_e('External folders found.', 'folders'); ?></b>
                        <?php esc_html_e('Click import to start importing external folders.', 'folders'); ?><?php ?>
                    </div>
                </div>
                <div class="flex items-center">
                    <a id="import-detected-folders" href="#" class="group inline-flex whitespace-nowrap items-center gap-2 px-3.5 py-1.5 h-10 rounded-lg border border-solid bg-white! border-[#D5D7DA]! text-base! font-semibold! text-grey900! hover:text-primary! hover:border-primary!">
                        <?php esc_html_e('Import Detected Folders', 'folders'); ?>
                    </a>
                </div>
            </div>
        <?php } ?>
        <div id="import-external-plugins-wrap" class="flex sm:items-center gap-2 p-4 bg-[#F5FAFF] rounded-lg justify-start sm:justify-between flex-col sm:flex-row <?php echo esc_attr($is_exists ? 'hidden': '') ?>">
            <div class="flex gap-2">
                <div class="gap-2 hidden sm:inline-flex">
                    <svg class="w-5 h-5" width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path class="group-hover:stroke-primary" d="M11 15V11M11 7H11.01M21 11C21 16.5228 16.5228 21 11 21C5.47715 21 1 16.5228 1 11C1 5.47715 5.47715 1 11 1C16.5228 1 21 5.47715 21 11Z" stroke="#2E90FA" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="flex-1 flex gap-0.5 flex-col justify-start">
                    <div class="text-base font-semibold text-grey900"><?php esc_html_e('No external folders detected for import?', 'folders'); ?></div>
                    <div class="text-sm text-[#535862]"><?php esc_html_e('Please contact us if you have external folders that were not detected.', 'folders'); ?></div>
                </div>
            </div>
            <div class="flex items-center">
                <a target="_blank" href="https://premio.io/contact/" class="group inline-flex whitespace-nowrap items-center gap-2 px-3.5 py-1.5 h-10 rounded-lg border border-solid bg-white! border-[#D5D7DA]! text-base! font-semibold! text-grey900! hover:text-primary! hover:border-primary!">
                    <span class="w-4 h-4 inline-flex items-center">
                        <svg class="w-4 h-4" width="19" height="15" viewBox="0 0 19 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path class="group-hover:stroke-primary" d="M17.4167 2.41667C17.4167 1.5 16.6667 0.75 15.75 0.75H2.41667C1.5 0.75 0.75 1.5 0.75 2.41667M17.4167 2.41667V12.4167C17.4167 13.3333 16.6667 14.0833 15.75 14.0833H2.41667C1.5 14.0833 0.75 13.3333 0.75 12.4167V2.41667M17.4167 2.41667L9.08333 8.25L0.75 2.41667" stroke="#717680" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <?php esc_html_e('Contact Us', 'folders'); ?>
                </a>
            </div>
        </div>
    </div>
    <div class="h-px w-full bg-[#E9EAEB] my-6"></div>
    <div class="flex flex-col gap-6">
        <div class="flex items-center gap-2">
            <div>
                <svg class="w-5 h-auto" width="23" height="21" viewBox="0 0 23 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path class="group-hover:stroke-primary" d="M11.448 7.10274V11.1027M11.448 15.1027H11.458M9.73802 1.96274L1.26802 16.1027C1.09339 16.4052 1.00099 16.748 1.00001 17.0973C0.99903 17.4465 1.08951 17.7899 1.26245 18.0933C1.43538 18.3967 1.68474 18.6495 1.98573 18.8266C2.28671 19.0037 2.62882 19.0989 2.97802 19.1027H19.918C20.2672 19.0989 20.6093 19.0037 20.9103 18.8266C21.2113 18.6495 21.4607 18.3967 21.6336 18.0933C21.8065 17.7899 21.897 17.4465 21.896 17.0973C21.8951 16.748 21.8027 16.4052 21.628 16.1027L13.158 1.96274C12.9797 1.66885 12.7287 1.42586 12.4292 1.25723C12.1297 1.08859 11.7918 1 11.448 1C11.1043 1 10.7663 1.08859 10.4668 1.25723C10.1673 1.42586 9.91629 1.66885 9.73802 1.96274Z" stroke="#F97066" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="text-base text-[#d92d20] font-semibold"><?php esc_html_e('Danger Zone', 'folders'); ?></div>
        </div>
        <div class="flex sm:items-center gap-2 justify-start sm:justify-between flex-col sm:flex-row">
            <div class="flex flex-col gap-0.5 max-w-160">
                <div class="flex gap-2 items-center">
                <div id="delete-plugin-data-upon-deletion-label" class="text-base font-semibold text-[#912018]"><?php esc_html_e('Delete plugin data upon deletion', 'folders'); ?></div>
                    <div class="bg-[#FEF3F2] hidden sm:inline-flex! text-[#B42318] text-xs rounded-2xl px-2 py-0.5"><?php esc_html_e('Not recommended', 'folders'); ?></div>
                </div>
                <div class="text-[#717680] text-sm">
                    <?php esc_html_e('Delete all folders when the plugin is removed. This feature will remove all existing folders created by the plugin upon deletion.', 'folders'); ?>
                </div>
            </div>
            <?php $status = \Folders\Admin\Settings::get_field_settings( 'advanced_settings', 'remove_folders_when_removed' ); ?>
            <div class="flex items-center">
                <label for="delete-plugin-data-upon-deletion" class="relative inline-flex items-center cursor-pointer">
                    <input <?php checked($status, 1) ?> id="delete-plugin-data-upon-deletion" type="checkbox" value="1" class="sr-only peer" aria-labelledby="delete-plugin-data-upon-deletion-label">
                    <span class="w-10 h-6 border border-[#D92D20] bg-white rounded-full peer peer-checked:after:left-[20px] peer-checked:after:bg-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-[#D92D20] after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#D92D20]"></span>
                </label>
            </div>
        </div>
        <div class="flex sm:items-center gap-2 justify-start sm:justify-between flex-col sm:flex-row">
            <div class="flex flex-col gap-0.5">
                <div class="text-base font-semibold text-[#912018]">
                    <?php esc_html_e('Manual Data Removal', 'folders'); ?>
                </div>
                <div class="text-[#717680] text-sm">
                    <?php esc_html_e('Delete all folders data manually This feature will remove all existing folders created by the plugin. Use this feature with caution.', 'folders'); ?>
                </div>
            </div>
            <div class="flex items-center">
                <button id="delete-plugin-data" type="button" class="inline-flex whitespace-nowrap items-center gap-2 px-3.5 py-1.5 h-10 rounded-lg border border-solid bg-white! border-[#D92D20]! text-base! font-semibold! text-[#D92D20]! hover:bg-[#D92D20]! hover:text-white!">
                    <span class="h-4 inline-flex items-center">
                        <svg class="w-auto h-4" width="17" height="19" viewBox="0 0 17 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M0.75 4.08333H2.41667M2.41667 4.08333H15.75M2.41667 4.08333V15.75C2.41667 16.192 2.59226 16.6159 2.90482 16.9285C3.21738 17.2411 3.64131 17.4167 4.08333 17.4167H12.4167C12.8587 17.4167 13.2826 17.2411 13.5952 16.9285C13.9077 16.6159 14.0833 16.192 14.0833 15.75V4.08333H2.41667ZM4.91667 4.08333V2.41667C4.91667 1.97464 5.09226 1.55072 5.40482 1.23816C5.71738 0.925595 6.14131 0.75 6.58333 0.75H9.91667C10.3587 0.75 10.7826 0.925595 11.0952 1.23816C11.4077 1.55072 11.5833 1.97464 11.5833 2.41667V4.08333" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <?php esc_html_e('Delete Now', 'folders'); ?>
                </button>
            </div>
        </div>
    </div>
</div>
