<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
ob_start();
?>
    <div class="folders-flex justify-center">
        <img class="max-h-100 mx-auto" src="<?php echo esc_url(FOLDERS_IMAGE_URL."upgrade-image.png") ?>" alt="" />
    </div>
<?php
$before_title = ob_get_clean();
ob_start();
$upgradeURL = \Folders\Admin\License::get_pro_url();
?>
    <div class="add-folder folders-wrap">
        <form id="folders-feedback-form" class="folders-feedback-form" method="post">
            <div class="feedback-subtitle">
                <?php esc_html_e('Your feedback will help us improve the product, please tell us why did you decide to deactivate Folders :)', 'folders'); ?>
            </div>
            <div class="folders-flex flex-col gap-4 mb-5">
                <div class="folders-field">
                    <label class="sr-only" for="folder_email"><?php esc_html_e('Email address', 'folders'); ?></label>
                    <input type="text" name="email" autocomplete="off" maxlength="50" id="folder_email" value="<?php echo esc_attr(get_option('admin_email')) ?>" class="folders-input is-required" placeholder="<?php esc_html_e('Email address', 'folders'); ?>">
                </div>
                <div class="folders-field">
                    <label class="sr-only" for="folder_comment"><?php esc_html_e('Your Comments', 'folders'); ?></label>
                    <textarea name="message" autocomplete="off" maxlength="50" id="folder_comment" class="folders-input is-required" placeholder="<?php esc_html_e('Your Comments', 'folders'); ?>"></textarea>
                </div>
            </div>
            <div class="feedback-footer">
                Having any problem with the Folders plugins? <a id="folders-feedback-support-link" href="<?php echo esc_url($upgradeURL); ?>">Click here</a> to contact our support now.
            </div>
            <div class="folders-flex gap-2-5 folders-button-wrap justify-between">
                <button type="button" class="skip-deactivate-button"><?php esc_html_e('Skip & Deactivate', 'folders'); ?></button>
                <div class="folders-button-wrap folders-flex gap-2-5">
                    <button type="button" class="submit-folders-feedback" disabled>
                        <?php esc_html_e('Submit & Deactivate', 'folders'); ?>
                        <span class="folders-loader"></span>
                    </button>
                    <button type="button" class="primary-folder-button form-submit-button hide-modal-button"><?php esc_html_e('Cancel', 'folders'); ?></button>
                </div>
            </div>
            <input type="hidden" name="folder_feedback_nonce" value="<?php echo esc_attr(wp_create_nonce('folder_feedback_nonce')); ?>">
        </form>
    </div>
<?php
$content = ob_get_clean();
$args = [
    'id' => 'folders-feedback-modal',
    'title' => esc_html__('Quick feedback about Folders 🙏', 'folders'),
    'content' => $content,
    'show_footer' => false,
    'default_visible' => false,
    'title_class' => 'text-center'
];
\Folders\Folders\FolderModals::render_modal($args);