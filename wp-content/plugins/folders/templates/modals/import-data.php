<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
ob_start();
$plugin_info = \Folders\Folders\FoldersImport::get_plugin_information();
$is_exists = \Folders\Folders\FoldersImport::$is_exists;
    if($is_exists) {
        $autoLoad = false;
        $page = isset($_GET['page']) ? sanitize_text_field($_GET['page']) : '';
        if($page == 'wcp_folders_settings') {
            $option = get_option("folder_redirect_status");
            if($option == 2) {
                $autoLoad = true;
                update_option("folder_redirect_status", 3);
            }
        }
        ?>
        <div class="flex flex-col gap-0 font-inter pb-5 new-import-message">
            <div class="text-sm text-grey900"><?php esc_html_e("We've detected that you use another folders plugin. Would you like the Folders plugin to import your current folders? Keep in mind you can always do it in Folders Settings -> Import", 'folders'); ?></div>
        </div>
        <div class="relative overflow-x-auto import-data">
            <table class="w-full text-sm text-left rtl:text-right text-body border-1 border-grey300 plugin-import-table">
                <tbody>
                    <?php foreach ($plugin_info as $slug => $plugin) {
                        if ($plugin['is_exists']) { ?>
                            <tr class="border-b border-grey300 border-1 border-grey300 other-plugins-<?php echo esc_attr($slug) ?>"
                                data-plugin="<?php echo esc_attr($slug) ?>"
                                data-plugin-name="<?php echo esc_attr($plugin['name']) ?>"
                                data-nonce="<?php echo esc_attr(wp_create_nonce("import_data_from_" . $slug)) ?>"
                                data-folders="<?php echo esc_attr($plugin['total_folders']) ?>"
                                data-attachments="<?php echo esc_attr($plugin['total_attachments']) ?>"
                                >
                                <th scope="row" class="px-4 py-3 text-base whitespace-nowrap font-semibold border-1 border-grey300 w-55 whitespace-normal!" width="200px">
                                    <?php echo esc_attr($plugin['name']) ?>
                                </th>
                                <td class="px-4 py-3 border-1 border-grey300">
                                    <div class="import-message flex w-full pb-2 text-grey700"><?php printf(esc_html__("%1\$s folder%2\$s and %3\$s attachment%4\$s", 'folders'), esc_attr($plugin['total_folders']), ($plugin['total_folders'] > 1) ? esc_html__("s", 'folders') : "", esc_attr($plugin['total_attachments']), ($plugin['total_attachments'] > 1) ? esc_html__("s", 'folders') : "") ?></div>
                                    <div class="flex gap-1.5">
                                        <button type="button" class="flex items-center gap-1.5 primary-folder-button px-2 py-1 text-sm! text-grey bg-white border border-grey300 rounded-md hover:bg-grey200! import-folder-data">
                                            <?php esc_html_e("Import", 'folders'); ?>
                                            <span class="folders-loader border-dark w-3.5! h-3.5!"></span>
                                        </button>
                                        <button type="button" class="flex items-center gap-1.5 primary-folder-button px-2 py-1 text-sm! text-grey bg-red-100/50 border border-red-100 rounded-md hover:bg-red-100! remove-folder-data">
                                            <?php esc_html_e("Delete plugin data", 'folders'); ?>
                                            <span class="folders-loader w-4! h-4!"></span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php }
                    } ?>
                </tbody>
            </table>
        </div>
    <?php
    $content = ob_get_clean();
    $args = [
        'id'                => 'import-other-plugins-data-modal',
        'title'             => esc_html__('Import data', 'folders'),
        'content'           => $content,
        'show_footer'       => true,
        'secondary_button_text' => esc_html__('Close', 'folders'),
        'secondary_button_classes' => 'hide-folders-modal',
        'default_visible'   => $autoLoad,
    ];
    \Folders\Admin\Modals::render_modal($args);
}