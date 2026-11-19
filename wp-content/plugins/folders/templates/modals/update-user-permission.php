<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
ob_start();
?>
<div class="flex flex-col gap-0 font-inter">
    <div class="text-grey700 text-sm">
        <div class="pb-2"><?php esc_html_e('The modified permission overrides existing WordPress permission for the user. Would you like Folders to make changes to the WordPress permissions for this user?', 'folders'); ?></div>
        <div>
            <?php esc_html_e('You can reset your user permissions by selecting the “Default” option for the user permissions dropdown', 'folders'); ?>
            <div class="relative group inline-flex items-center gap-1 align-middle">
                <div class="text-grey cursor-pointer w-4 h-4">
                    <svg class="w-4 h-4" width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9.33333 12.6667V9.33333M9.33333 6H9.34167M17.6667 9.33333C17.6667 13.9357 13.9357 17.6667 9.33333 17.6667C4.73096 17.6667 1 13.9357 1 9.33333C1 4.73096 4.73096 1 9.33333 1C13.9357 1 17.6667 4.73096 17.6667 9.33333Z" stroke="#A4A7AE" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </div>
                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-80 p-3 bg-white text-sm text-grey rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 border border-grey200 font-normal leading-normal">
                    <div class="absolute -bottom-1.5 left-1/2 -translate-x-1/2 w-3 h-3 bg-white border-b border-r border-grey200 rotate-45"></div>
                    <div class="flex flex-col gap-2">
                        <div class="font-inter text-center">
                            <?php esc_html_e( 'Changing the permission will provide additional permission to the user than their role provides', 'folders' ); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" id="update-user-permission-nonce" value="<?php echo wp_create_nonce('folders-update-user-permission-nonce') ?>">
</div>
<?php
$content = ob_get_clean();
$args = [
    'id'                => 'user-permission-modal',
    'title'             => esc_html__('Are you sure?', 'folders'),
    'content'           => $content,
    'show_footer'       => true,
    'primary_button_text' => esc_html__('Update permission', 'folders'),
    'secondary_button_text' => esc_html__('Cancel', 'folders'),
    'primary_button_id' => 'update-user-permission-button',
    'secondary_button_classes' => 'hide-folders-modal',
    'disabled'          => true,
    'has_close_button'  => false,
];
\Folders\Admin\Modals::render_modal($args);