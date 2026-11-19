<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
ob_start();
$hasValidKey = \Folders\Admin\License::is_license_active();
$upgradeURL = \Folders\Admin\License::get_pro_url();
global $typenow;
?>
<div class="folders-wrap">
    <p style="margin-bottom: 1.5rem;"><?php esc_html_e('Hey, it looks like you want to move the file to "Unassigned Files." Do you want to move the file from the current folder only or from all the folders where the file exists?', 'folders'); ?></p>
</div>
<?php
$content = ob_get_clean();
$args = [
    'id' => 'remove-post-from-folder-modal',
    'title' => esc_html__('Confirm your change', 'folders'),
    'content' => $content,
    'show_footer' => true,
    'default_visible' => false,
    'primary_button_text' => esc_html__('Just from this folder', 'folders'),
    'secondary_button_text' => esc_html__('From all folders', 'folders'),
    'primary_button_id' => 'remove-post-from-current-folder',
    'secondary_button_id' => 'remove-post-from-all-folders',
];
\Folders\Folders\FolderModals::render_modal($args);