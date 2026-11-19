<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
ob_start();
global $typenow;
if(!isset($post_type) || empty($post_type)){
    $post_type = $typenow;
}
$upgradeURL = \Folders\Admin\License::get_pro_url();
?>
    <div class="add-folder folders-wrap">
        <form id="unzip-folder-form" class="unzip-folder-form" method="post" autocomplete="off">
            <div class="folders-flex flex-col gap-6">
                <p><?php esc_html_e("Extract files from a ZIP archive into a new folder in your WordPress Media Library.", 'folders'); ?></p>
                <div class="folders-flex gap-2-5 folders-button-wrap">
                    <a target="_blank" href="<?php echo esc_url($upgradeURL); ?>" class="primary-folder-button form-submit-button">
                        <?php esc_html_e('Upgrade to Pro', 'folders'); ?>
                        <span class="folders-loader"></span>
                    </a>
                    <button class="hide-modal-button"><?php esc_html_e('Not now', 'folders'); ?></button>
                </div>
            </div>
        </form>
    </div>
<?php
$content = ob_get_clean();
$args = [
    'id' => 'zip-file-modal',
    'title' => esc_html__('Extract ZIP files with Folders Pro', 'folders'),
    'content' => $content,
    'show_footer' => false,
    'default_visible' => false,
];
\Folders\Folders\FolderModals::render_modal($args);