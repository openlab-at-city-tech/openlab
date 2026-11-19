<?php
namespace Folders\Media;

defined( 'ABSPATH' ) || exit;

/**
 * Folder controls on the media upload and Media Library list screens.
 *
 * Adds a folder selector above the uploader, assigns uploaded files to the
 * chosen folder, adds a folder filter dropdown to the list view and a
 * "Download" bulk action.
 */
class MediaDropdown {

    /**
     * Register the upload, list table filter and bulk action hooks.
     */
    public function __construct() {
        add_action('restrict_manage_posts', [$this, 'output_list_table_filters'], 10, 2);
        add_filter('bulk_actions-upload', array( $this,'bulk_download_action') );
        add_filter('pre-upload-ui', [$this, 'show_dropdown_on_media_screen']);
        add_action('add_attachment', [$this, 'add_attachment_category']);
    }

    /**
     * Assign a newly uploaded attachment to the folder chosen in the uploader.
     *
     * Reads the folder ID from `$_POST['folder_for_media']` and clears that
     * folder's cached item count. Hooked to `add_attachment`.
     *
     * @param int $post_ID Attachment ID.
     * @return void
     */
    public function add_attachment_category($post_ID)
    {
        if (!\Folders\Folders\Settings::check_for_folder('attachment')) {
            return;
        }
        $folder_id = filter_input( INPUT_POST, 'folder_for_media', FILTER_SANITIZE_NUMBER_INT );
        if(empty($folder_id) || $folder_id == -1 || $folder_id == 'add-folder') {
            return;
        }
        $folder_type = \Folders\Folders\Settings::get_folder_post_type('attachment');
        // Assigning the folder clears the cached counts (see Actions\FoldersCache).
        wp_set_object_terms($post_ID, intval($folder_id), $folder_type);
    }

    /**
     * Output the "Select a folder" dropdown above the media uploader.
     *
     * Hooked to `pre-upload-ui`.
     *
     * @return void
     */
    public function show_dropdown_on_media_screen()
    {
        if (!\Folders\Folders\Settings::check_for_folder('attachment')) {
            return;
        }
        global $typenow, $current_screen;
        $folders = \Folders\Folders\FoldersTree::get_data_by_post_type('attachment', '- ');
        $request = sanitize_text_field($_SERVER['REQUEST_URI']);
        $request = strpos($request, "post.php");
        ?>
            <div class="new-media-folders-form">
                <div class="attachments-category text-semibold text-sm"><?php esc_html_e("Select a folder (Optional)", 'folders') ?></div>
                <div class="attachments-category"><?php esc_html_e("First select the folder, and then upload the files", 'folders') ?></div>
                <div>
                <label class="sr-only" for="folder_for_media"><?php esc_html_e("Select a folder", 'folders') ?></label>
                <select name="folder_for_media" class="folder_for_media" id="folder_for_media">
                    <option value="-1">- <?php esc_html_e('Unassigned', 'folders') ?></option>
                    <?php foreach($folders as $folder) { ?>
                        <option value="<?php echo esc_attr($folder['id']); ?>"><?php echo esc_html($folder['text']); ?></option>
                    <?php } ?>
                    <?php if (($typenow == "attachment" && isset($current_screen->base) && $current_screen->base == "upload") || ($request !== false  || \Folders\Folders\Settings::check_for_folder('attachment'))) { ?>
                        <option value="add-folder"><?php esc_html_e('+ Create a New Folder', 'folders') ?></option>
                    <?php } ?>
                </select>
                </div>
            </div>
        <?php
    }

    /**
     * Add the "Download" bulk action to the Media Library list view.
     *
     * @param array $bulk_actions Registered bulk actions.
     * @return array Modified bulk actions.
     */
    public function bulk_download_action($bulk_actions)
    {
        if (!\Folders\Folders\Settings::check_for_folder('attachment')) {
            return $bulk_actions;
        }

        $bulk_actions['folders_download'] = esc_html__('Download', 'folders');
        return $bulk_actions;
    }

    /**
     * Output the folder filter dropdown in the Media Library list view toolbar.
     *
     * Hooked to `restrict_manage_posts`; only runs for attachments in the "bar" location.
     *
     * @param string $post_type Post type of the list table.
     * @param string $view_name Location of the filter controls ("top" or "bar").
     * @return void
     */
    public function output_list_table_filters($post_type, $view_name = "")
    {
        if ($post_type != 'attachment') {
            return;
        }

        if ($view_name != 'bar') {
            return;
        }

        if (!\Folders\Folders\Settings::check_for_folder('attachment')) {
            return;
        }

        $current_term = false;
        if (isset($_REQUEST['media_folder'])) {
            $current_term = sanitize_text_field($_REQUEST['media_folder']);
        }

        wp_dropdown_categories(
            [
                'show_option_all' => esc_html__('All Folders', 'folders'),
                'show_option_none' => esc_html__('(Unassigned)', 'folders'),
                'option_none_value' => -1,
                'orderby' => 'meta_value_num',
                'order' => 'ASC',
                'show_count' => true,
                'hide_empty' => false,
                'update_count_callback' => '_update_generic_term_count',
                'echo' => true,
                'selected' => $current_term,
                'hierarchical' => true,
                'name' => 'media_folder',
                'id' => '',
                'class' => '',
                'taxonomy' => 'media_folder',
                'value_field' => 'slug',
                'meta_query' => [
                    [
                        'key' => 'wcp_custom_order',
                        'type' => 'NUMERIC',
                    ],
                ],
            ]
        );

    }
}
