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
    <form id="add-sub-folder-form" class="add-folder-form" method="post" autocomplete="off">
        <div class="folders-flex flex-col gap-6">
            <div class="folders-flex flex-col gap-2.5">
                <div class="folders-flex flex-col gap-3" id="add-sub-folders-wrap">
                    <div class="folders-field">
                        <label for="sub_folder_name" class="folders-label"><?php esc_html_e("Enter your folder's name", 'folders'); ?></label>
                        <input type="text" name="folder_name[]" maxlength="50" id="sub_folder_name" class="folders-input is-required" placeholder="<?php esc_attr_e('Folder Name', 'folders'); ?>" >
                    </div>
                </div>
                <div>
                    <button type="button" id="add-sub-folder-input" class="add-folder-button"><?php esc_html_e('+ Add Another Folder', 'folders'); ?></button>
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
        <input type="hidden" name="parent_id" id="sub_folder_parent_id" value="0">
        <input type="hidden" name="nonce" value="<?php echo esc_attr(wp_create_nonce('premio_folders_add_sub_folder')); ?>">
    </form>
    <div class="hidden" id="sub-folder-field-wrap">
        <div class="folders-field relative">
            <label for="sub_folder_name___count__" class="sr-only"><?php esc_html_e("Enter your folder's name", 'folders'); ?></label>
            <input type="text" name="folder_name[]" maxlength="50" autocomplete="off" id="sub_folder_name___count__" class="folders-input is-required" placeholder="<?php esc_attr_e('Folder Name', 'folders'); ?>" >
            <button type="button" class="remove-folder-record">
                <span class="pfolder-trash font-14"></span>
                <span class="sr-only"><?php esc_html_e('Remove', 'folders'); ?></span>
            </button>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
$args = [
    'id' => 'add-sub-folder-modal',
    'title' => esc_html__('Add Sub Folder', 'folders'),
    'content' => $content,
    'show_footer' => false,
    'default_visible' => false,
];
\Folders\Folders\FolderModals::render_modal($args);