<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
ob_start();
global $typenow;
if(!isset($post_type) || empty($post_type)){
    $post_type = $typenow;
}
$folders = \Folders\Folders\FoldersTree::get_data_by_post_type($post_type);
?>
<div class="add-folder folders-wrap">
    <form id="move-items-to-folder-form" class="add-folder-form" method="post">
        <div class="folders-flex flex-col gap-6">
            <div class="folders-flex flex-col gap-2.5">
                <div class="folders-flex flex-col gap-3" id="add-folders-wrap">
                    <div class="folders-field">
                        <label for="move_folder_id" class="folders-label"><?php esc_html_e("Select folder", 'folders'); ?></label>
                        <select name="move_folder_id" id="move_folder_id" class="folders-select folders-input folders-select-list">
                            <option value=""><?php esc_html_e("Select folder", 'folders'); ?></option>
                            <?php foreach ($folders as $folder) { ?>
                                <option value="<?php echo esc_attr($folder['id']); ?>"><?php echo esc_html($folder['text']); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="folders-flex gap-2-5 folders-button-wrap">
                <button class="primary-folder-button form-submit-button" id="move-to-folder-button">
                    <?php esc_html_e('Move to folder', 'folders'); ?>
                    <span class="folders-loader"></span>
                </button>
                <button class="hide-modal-button"><?php esc_html_e('Cancel', 'folders'); ?></button>
            </div>
        </div>
        <input type="hidden" name="type" class="form-action-type" value="<?php echo esc_attr($post_type); ?>">
        <input type="hidden" name="post_ids" id="post_ids">
        <input type="hidden" name="nonce" value="<?php echo esc_attr(wp_create_nonce('premio_folders_move_to_folder')); ?>">
    </form>
</div>
<?php
$content = ob_get_clean();
$args = [
    'id' => 'move-to-folder-modal',
    'title' => esc_html__('Move to folder', 'folders'),
    'content' => $content,
    'show_footer' => false
];
\Folders\Folders\FolderModals::render_modal($args);