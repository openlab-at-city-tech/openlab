<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
ob_start();
?>
    <div class="flex flex-col gap-3 font-inter text-base">
        <?php esc_html_e("If you hide the recommended plugins page from your menu, it won't appear there again. Are you sure you'd like to do it?", 'folders'); ?>
    </div>
    <input type="hidden" id="hide-recommended-plugin-page-nonce" value="<?php echo esc_attr( wp_create_nonce( 'hide_recommended_plugin_page_nonce' ) ); ?>">
<?php
$content = ob_get_clean();
$args = [
    'id' => 'recommended-plugins-modal',
    'title' => esc_html__('Are you sure?', 'folders'),
    'content' => $content,
    'show_footer' => true,
    'default_visible' => false,
    'primary_button_text' => esc_html__('Hide it', 'folders'),
    'secondary_button_text' => esc_html__('Keep it', 'folders'),
    'primary_button_id' => 'hide-recommended-plugin-page',
    'secondary_button_classes' => 'hide-folders-modal',
];
\Folders\Admin\Modals::render_modal($args);