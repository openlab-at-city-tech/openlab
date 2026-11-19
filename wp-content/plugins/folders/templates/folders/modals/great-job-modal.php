<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
ob_start();
?>
<div class="folders-flex">
    <img class="max-h-100" src="<?php echo esc_url(FOLDERS_IMAGE_URL."upgrade-image.png") ?>" alt="" />
</div>
<?php
$before_title = ob_get_clean();
ob_start();
$upgradeURL = \Folders\Admin\License::get_pro_url();
?>
<div class="add-folder folders-wrap">
    <div class="folders-flex flex-col gap-6">
        <div class="folders-flex folders-sub-title flex-col gap-2.5 pt-4">
            <?php esc_html_e("You've successfully organized your files into folders for the first time.", 'folders'); ?>
        </div>
        <div class="upgrade-content">
            <div class="upgrade-content-title"><?php esc_html_e("Upgrade to Pro today", "folders"); ?></div>
            <ul>
                <li><?php esc_html_e("⚙️ Create Unlimited folders and subfolders", "folders"); ?></li>
                <li><?php esc_html_e("🗂️ Automatically filter posts, pages, media files based on author, date, type and more", "folders"); ?></li>
                <li><?php esc_html_e("🛠 Upload folders directly and download them as ZIP", "folders"); ?></li>
                <li><?php esc_html_e("🔒 Restrict users within their own folders", "folders"); ?></li>
            </ul>
        </div>
        <div class="folders-flex gap-2-5 folders-button-wrap">
            <a href="<?php echo esc_url($upgradeURL); ?>" target="_blank" class="primary-folder-button hide-great-job-modal-button form-submit-button">
                <?php esc_html_e('Upgrade to Pro', 'folders'); ?>
            </a>
            <button class="hide-great-job-modal-button"><?php esc_html_e('Close', 'folders'); ?></button>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
$args = [
    'id' => 'great-job-modal',
    'title' => esc_html__('Great job! 🎉', 'folders'),
    'content' => $content,
    'show_footer' => false,
    'before_title' => $before_title,
];
\Folders\Folders\FolderModals::render_modal($args);