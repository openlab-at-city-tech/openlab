<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
ob_start();
?>
<div class="flex flex-col gap-0 font-inter">
    <div class="text-grey700 text-sm">
        <?php printf(esc_html__('Type %s to delete', 'folders'), "DELETE"); ?>
    </div>
    <div class="foldes-field my-1.5">
        <label for="delete-plugin-data-input" class="sr-only"><?php esc_html_e('Type DELETE to confirm', 'folders');  ?></label>
        <input autocomplete="off" id="delete-plugin-data-input" type="text" class="w-full border rounded-lg! border-solid border! border-grey300! px-3! h-10! focus-visible:border-grey500! hover:border-grey500!">
    </div>
    <div id="delete-error-message" class="text-sm text-[#D92D20] hidden"><?php printf(esc_html__('Please type %s and click on the "%s" button to confirm', 'folders'), 'DELETE', esc_html__('Delete', 'folders')); ?></div>
    <div id="delete-confirmation-message" class="text-sm text-[#D92D20] hidden font-semibold"><?php printf(esc_html__('This will delete all existing folders & settings', 'folders'), 'DELETE', esc_html__('Delete', 'folders')); ?></div>
    <input type="hidden" id="delete-plugin-data-nonce" value="<?php echo wp_create_nonce('delete-folders-plugin-data-manually') ?>">
</div>
<?php
$content = ob_get_clean();
$args = [
    'id'                => 'delete-folders-data-modal',
    'title'             => esc_html__('Are you sure?', 'folders'),
    'content'           => $content,
    'show_footer'       => true,
    'primary_button_text' => esc_html__('Delete', 'folders'),
    'primary_button_disabled' => true,
    'secondary_button_text' => esc_html__('Cancel', 'folders'),
    'primary_button_id' => 'delete-plugin-data-button',
    'secondary_button_classes' => 'hide-folders-modal',
    'disabled'          => true,
    'has_close_button'  => false,
];
\Folders\Admin\Modals::render_modal($args);