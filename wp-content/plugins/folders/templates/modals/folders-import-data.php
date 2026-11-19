<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
ob_start();
?>
<div class="flex flex-col gap-0 font-inter">
    <p class="mt-0! text-base"><?php esc_html_e('The following folders will be imported to your plugin', 'folders'); ?></p>
    <table id="folder-import-table" class="folder-import-table w-full text-left border-collapse">
        <thead>
            <tr>
                <th class="border p-2"><?php esc_html_e('Category', 'folders'); ?></th>
                <th class="border p-2 text-center"><?php esc_html_e('No. of Folders', 'folders'); ?></th>
                <th class="border p-2 text-center"><?php esc_html_e('No. of Subfolders', 'folders'); ?></th>
                <th class="border p-2 text-center"><?php esc_html_e('Total', 'folders'); ?></th>
            </tr>
        </thead>
        <tbody id="folder-import-tbody">
            <!-- Rows will be added dynamically here -->
        </tbody>
    </table>
    <div id="import_file_content" class="hidden"></div>
</div>
<?php
$content = ob_get_clean();
$args = [
    'id'                => 'folders-import-data-modal',
    'title'             => esc_html__('Import Folder Structure', 'folders'),
    'content'           => $content,
    'show_footer'       => true,
    'primary_button_text' => esc_html__('Import', 'folders'),
    'secondary_button_text' => esc_html__('Cancel', 'folders'),
    'primary_button_id' => 'import-folders-data',
    'secondary_button_classes' => 'hide-folders-modal'
];
\Folders\Admin\Modals::render_modal($args);