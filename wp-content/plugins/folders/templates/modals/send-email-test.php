<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
ob_start();
?>
<div class="flex flex-col gap-0 font-inter">
    <div class="text-grey700 text-sm">
        <?php esc_html_e('We have sent a test email, please confirm if you received it.', 'folders'); ?>
    </div>
</div>
<?php
$content = ob_get_clean();
ob_start();
?>
<div class="inline-flex">
    <div class="email-steps hidden" id="email-send-loader"><?php printf(esc_html__("Resend in %s seconds", 'folders'), '<span>20</span>') ?></div>
    <div class="email-steps hidden" id="sending-email"><?php esc_html_e("Sending...", 'folders') ?></div>
    <button class="email-steps hidden text-primary text-sm! font-semibold! p-0! m-0!" id="resend-email"><?php esc_html_e('Send again', 'folders'); ?></button>
</div>
<?php
$footer = ob_get_clean();
$args = [
    'id'                => 'send-test-email-modal',
    'title'             => esc_html__('Email is sent', 'folders'),
    'content'           => $content,
    'show_footer'       => true,
    'primary_button_text' => esc_html__('I got the email', 'folders'),
    'secondary_button_text' => esc_html__('Change email', 'folders'),
    'secondary_button_id' => 'focus-on-email-field',
    'primary_button_classes' => 'hide-folders-modal',
    'footer_sidebar'    => $footer,
];
\Folders\Admin\Modals::render_modal($args);