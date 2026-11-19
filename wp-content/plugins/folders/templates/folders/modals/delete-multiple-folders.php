<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
ob_start();
global $typenow;
if(!isset($post_type) || empty($post_type)){
    $post_type = $typenow;
}
?>
<div class="add-folder folders-wrap">
    <form id="delete-multiple-folder-form" class="delete-folder-form" method="post">
        <div class="folders-flex flex-col gap-6">
            <div class="font-14">
                <?php esc_html_e('Items in the folder will not be deleted.', 'folders'); ?>
            </div>
            <div class="folders-flex gap-2-5 folders-button-wrap">
                <button class="primary-folder-button form-submit-button">
                    <?php esc_html_e('Yes, Delete it!', 'folders'); ?>
                    <span class="folders-loader"></span>
                </button>
                <button class="hide-modal-button"><?php esc_html_e('No, Keep it', 'folders'); ?></button>
            </div>
        </div>
        <input type="hidden" name="type" class="form-action-type" value="<?php echo esc_attr($post_type); ?>">
        <input type="hidden" name="folder_id" id="delete_folder_ids" value="">
        <input type="hidden" name="nonce" value="<?php echo esc_attr(wp_create_nonce('premio_folders_delete_multiple_folders')); ?>">
    </form>
</div>
<?php
$content = ob_get_clean();
$args = [
    'id' => 'delete-multiple-folder-modal',
    'title' => esc_html__('Are you sure you want to delete the selected folders?', 'folders'),
    'content' => $content,
    'show_footer' => false,
    'default_visible' => false,
];
\Folders\Folders\FolderModals::render_modal($args);