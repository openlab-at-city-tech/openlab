<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
ob_start();
?>
<div class="flex flex-col gap-0 font-inter">
    <div class="text-grey700 text-sm">
        <?php printf(esc_html__('Folders will remove all created folders once you remove the plugin. We recommend you %s if you plan to use Folders in future.', 'folders'), "<b>".esc_html__('not to use this feature', 'folders')."</b>"); ?>
    </div>
    <input type="hidden" id="remove-plugin-data-nonce" value="<?php echo wp_create_nonce('remove-plugin-data-on-deactivate') ?>">
</div>
<?php
$content = ob_get_clean();
$args = [
    'id'                => 'remove-plugin-data-modal',
    'title'             => esc_html__('Are you sure?', 'folders'),
    'content'           => $content,
    'show_footer'       => true,
    'primary_button_text' => esc_html__('I want to delete anyway', 'folders'),
    'secondary_button_text' => esc_html__('Cancel', 'folders'),
    'primary_button_id' => 'remove-plugin-data',
    'secondary_button_classes' => 'hide-remove-plugin-data-modal',
    'disabled'          => true,
    'has_close_button'  => false,
];
\Folders\Admin\Modals::render_modal($args);