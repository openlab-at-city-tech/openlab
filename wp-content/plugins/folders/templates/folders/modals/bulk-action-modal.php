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
    <div class="folders-wrap">
        <form id="bulk-action-folders-form" class="bulk-action-folders-form" method="post">
            <div class="folders-flex flex-col gap-6">
                <div class="folders-flex flex-col gap-2.5">
                    <div class="folders-flex flex-col gap-3">
                        <div class="folders-field">
                            <label for="bulk-action-folders-select" class="folders-label sr-only"><?php esc_html_e("Select folder", 'folders'); ?></label>
                            <select id="bulk-action-folders-select" name="folder_id" class="folders-input is-required">
                                <option value=""><?php esc_html_e('Select Folder', 'folders'); ?></option>
                                <option value="-1"><?php esc_html_e('Unassigned Folder', 'folders'); ?></option>
                            </select>
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
            <input type="hidden" name="post_type" value="<?php echo esc_attr($post_type); ?>">
            <input type="hidden" name="post_ids" id="bulk_action_page_ids" value="">
            <input type="hidden" name="nonce" value="<?php echo esc_attr(wp_create_nonce('premio_folders_bulk_action_'.esc_attr($post_type))); ?>">
        </form>
    </div>
<?php
$content = ob_get_clean();
$args = [
    'id' => 'bulk-action-folders-modal',
    'title' => esc_html__('Select Folder', 'folders'),
    'content' => $content,
    'show_footer' => false,
    'default_visible' => false,
];
\Folders\Folders\FolderModals::render_modal($args);