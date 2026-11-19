<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
ob_start();
$hasValidKey = \Folders\Admin\License::is_license_active();
$upgradeURL = \Folders\Admin\License::get_pro_url();
global $typenow;
if(!isset($post_type) || empty($post_type)){
    $post_type = $typenow;
}
?>
<div class="add-folder folders-wrap">
    <form id="rename-folder-form" class="rename-folder-form" method="post">
        <div class="folders-flex flex-col gap-6">
            <div class="folders-flex flex-col gap-2.5">
                <div class="folders-flex flex-col gap-3">
                    <div class="folders-field">
                        <label for="rename_folder_name" class="folders-label"><?php esc_html_e("Enter your folder's name", 'folders'); ?></label>
                        <input type="text" name="folder_name" maxlength="50" id="rename_folder_name" class="folders-input is-required" placeholder="<?php esc_attr_e('Folder Name', 'folders'); ?>" >
                    </div>
                </div>
            </div>
            <div class="folders-flex gap-2-5 folders-button-wrap">
                <button class="primary-folder-button form-submit-button">
                    <?php esc_html_e('Submit', 'folders'); ?>
                    <span class="folders-loader"></span>
                </button>
                <button class="hide-modal-button"><?php esc_html_e('Cancel', 'folders'); ?></button>
            </div>
        </div>
        <input type="hidden" name="type" class="form-action-type" value="<?php echo esc_attr($post_type); ?>">
        <input type="hidden" name="folder_id" id="rename_folder_id" value="">
        <input type="hidden" name="parent_id" id="rename_parent_id" value="">
        <input type="hidden" name="nonce" value="<?php echo esc_attr(wp_create_nonce('premio_folders_rename_folder')); ?>">
    </form>
</div>
<?php
$content = ob_get_clean();
$args = [
    'id' => 'rename-folder-modal',
    'title' => esc_html__('Rename Folder', 'folders'),
    'content' => $content,
    'show_footer' => false,
    'default_visible' => false,
];
\Folders\Folders\FolderModals::render_modal($args);