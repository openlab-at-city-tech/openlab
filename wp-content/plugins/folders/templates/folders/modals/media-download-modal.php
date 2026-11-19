<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
$upgradeURL = \Folders\Admin\License::get_pro_url();
ob_start();
?>
<div class="add-folder folders-wrap">
    <div class="folders-flex flex-col gap-6">
        <div class="folders-flex flex-col gap-2.5">
            <div class="folders-flex flex-col gap-3">
                <div class="folders-field">
                    <label for="folder_name" class="folders-label"><?php esc_html_e("Download multiple files with one click by upgrading Folders!", 'folders'); ?></label>
                </div>
            </div>
        </div>
        <div class="folders-flex gap-2-5 folders-button-wrap">
            <a href="<?php echo esc_url( $upgradeURL ); ?>" target="_blank" class="primary-folder-button form-submit-button">
                <?php esc_html_e('Upgrade to Pro', 'folders'); ?>
            </a>
            <button class="hide-modal-button"><?php esc_html_e('Download files one by one', 'folders'); ?></button>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
$args = [
    'id' => 'media-download-modal',
    'title' => esc_html__('Get more out of Folders 🚀', 'folders'),
    'content' => $content,
    'show_footer' => false,
    'default_visible' => false,
];
\Folders\Folders\FolderModals::render_modal($args);