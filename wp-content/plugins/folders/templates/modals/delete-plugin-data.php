<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
ob_start();
?>
<div class="flex flex-col gap-0 font-inter">
    <div id="delete-plugins-data-message"></div>
</div>
<?php
$content = ob_get_clean();
$args = [
    'id'                => 'delete-plugin-data-modal',
    'title'             => esc_html__('Are you sure?', 'folders'),
    'content'           => $content,
    'show_footer'       => true,
    'primary_button_text' => esc_html__('Delete', 'folders'),
    'secondary_button_text' => esc_html__('Cancel', 'folders'),
    'primary_button_id' => 'delete-other-plugin-data-button',
    'secondary_button_classes' => 'hide-folders-modal',
    'has_close_button'  => true,
];
\Folders\Admin\Modals::render_modal($args);