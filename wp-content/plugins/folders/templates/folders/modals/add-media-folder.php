<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
ob_start();
$hasValidKey = \Folders\Admin\License::is_license_active();
$upgradeURL = \Folders\Admin\License::get_pro_url();
global $typenow;
$post_type = 'attachment';
?>
    <div class="add-folder folders-wrap">
        <form id="add-media-folder-form" class="add-folder-form" method="post" autocomplete="off">
            <div class="folders-flex flex-col gap-6">
                <div class="folders-flex flex-col gap-2.5">
                    <div class="folders-flex flex-col gap-3" id="add-folders-wrap">
                        <div class="folders-field">
                            <label for="folder_name" class="folders-label"><?php esc_html_e("Enter your folder's name", 'folders'); ?></label>
                            <input type="text" name="folder_name[]" autocomplete="off" maxlength="50" id="folder_name" class="folders-input is-required" placeholder="<?php esc_attr_e('Folder Name', 'folders'); ?>" >
                        </div>
                    </div>
                </div>
                <?php if ( !$hasValidKey ) { ?>
                    <div class="folders-flex gap-2-5 justify-end pro-tip items-center">
                        <div>
                            <?php printf(esc_html__('%s to unlock 20+ powerful features and enjoy priority support 🚀', 'folders'), "<b>".esc_html__('Upgrade to Pro', 'folders')."</b>"); ?>
                        </div>
                        <div>
                            <a href="<?php echo esc_url($upgradeURL) ?>" target="_blank"><?php esc_html_e('Upgrade Now', 'folders'); ?></a>
                        </div>
                    </div>
                <?php } ?>
                <div class="folders-flex gap-2-5 folders-button-wrap">
                    <button type="submit" class="primary-folder-button form-submit-button">
                        <?php esc_html_e('Submit', 'folders'); ?>
                        <span class="folders-loader"></span>
                    </button>
                    <button type="button" class="hide-modal-button"><?php esc_html_e('Cancel', 'folders'); ?></button>
                </div>
            </div>
            <input type="hidden" name="type" class="form-action-type" value="<?php echo esc_attr($post_type); ?>">
            <input type="hidden" name="is_duplicate" value="0">
            <input type="hidden" name="siblings" value="">
            <input type="hidden" name="parent_id" value="">
            <input type="hidden" name="nonce" value="<?php echo esc_attr(wp_create_nonce('prm_folders_add_folder')); ?>">
        </form>
    </div>
<?php
$content = ob_get_clean();
$args = [
    'id' => 'add-media-folder-modal',
    'title' => esc_html__('Add a New Folder', 'folders'),
    'content' => $content,
    'show_footer' => false,
    'default_visible' => false,
];
\Folders\Folders\FolderModals::render_modal($args);