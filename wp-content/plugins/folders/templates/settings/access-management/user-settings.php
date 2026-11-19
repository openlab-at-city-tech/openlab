<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
?>
<div id="user-options" class="hidden">
    <input type="hidden" id="update-user-settings">
    <label for="user-search" class="sr-only"><?php esc_html_e('Search by user', 'folders'); ?></label>
    <div class="flex items-center gap-2 my-4 px-3 border-grey300 rounded-lg border-1 h-10 hover:border-grey500 focus-within:border-grey500">
        <div class="flex items-center w-4 h-4">
            <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M15.833 15.834L12.208 12.209M14.1663 7.50065C14.1663 11.1825 11.1816 14.1673 7.49967 14.1673C3.81778 14.1673 0.833008 11.1825 0.833008 7.50065C0.833008 3.81875 3.81778 0.833984 7.49967 0.833984C11.1816 0.833984 14.1663 3.81875 14.1663 7.50065Z" stroke="#717680" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <input type="text" id="user-search" class="border-none! pl-0! outline-none! flex-1 min-h-9.5!" placeholder="<?php esc_attr_e('Search by user', 'folders'); ?>">
    </div>
    <div class="users-permissions hidden" id="users-list">
        <div class="user-roles-list">
            <div class="flex gap-3 justify-between">
                <div class="text-grey500 text-sm uppercase"><?php esc_html_e('Users', 'folders'); ?></div>
                <div class="text-grey500 text-sm uppercase"><?php esc_html_e('Permission', 'folders'); ?></div>
            </div>
            <div id="users-search-list">

            </div>
        </div>
    </div>
    <div class="users-permissions text-grey600 text-sm" id="users-message">
        <?php echo sprintf(esc_html__("Please search for a user to edit Folders access for specific users, or %1\$s", "folders"), "<a href='#' id='load-all-users' class='text-primary!'>" . esc_html__("load all users", "folders") . "</a>") ?>
    </div>
    <div class="users-permissions hidden" id="no-user-message">
        <?php esc_html_e('No results found', 'folders'); ?>
    </div>
    <div class="users-permissions hidden" id="users-loading">
        <div class="flex justify-center">
            <div class="user-ajax-loader"></div>
        </div>
    </div>
</div>
