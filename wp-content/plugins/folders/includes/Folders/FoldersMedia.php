<?php
namespace Folders\Folders;

defined( 'ABSPATH' ) || exit;

/**
 * Folder support inside the WordPress media modal and Media Library grid.
 *
 * Loads the folder sidebar and filters into the media modal (for example in
 * the post editor), filters grid-mode attachment queries by folder, and
 * assigns newly uploaded files to the selected folder.
 */
class FoldersMedia {

    /**
     * Register the media modal, attachment query and upload hooks.
     */
    public function __construct() {
        add_action('wp_enqueue_media', [$this, 'output_backbone_view_filters']);

        add_filter('ajax_query_attachments_args', [$this, 'filter_attachments_grid']);
        add_filter('add_attachment', [$this, 'save_media_terms']);
    }

    /**
     * Load the folder UI for the media modal.
     *
     * On the Media Library, first redirects to the last opened or default media
     * folder (same rules as {@see \Folders\Folders\DefaultFolder}). On the Media
     * Library it then loads the media folder filter scripts; on other screens
     * (for example the post editor, but not the Plugins screen) it also renders
     * the folder sidebar and enqueues and localizes everything the media modal
     * needs. Only runs when folders are enabled for media. Hooked to
     * `wp_enqueue_media`.
     *
     * @return void
     */
    public function output_backbone_view_filters()
    {
        if (!\Folders\Folders\Settings::check_for_folder('attachment')) {
            return;
        }
        // Themes and plugins also load the media modal for visitors and for
        // users who cannot browse the Media Library (for example on profile
        // or front-end forms). The folder panel is of no use to them, so skip
        // building it.
        if (!is_user_logged_in() || !current_user_can('upload_files')) {
            return;
        }
        global $typenow, $current_screen;
        if($typenow == 'dlm_download') {
            return;
        }
        $isAjax = (defined('DOING_AJAX') && DOING_AJAX) ? 1 : 0;
        $options = get_option('folders_settings');
        $options = (empty($options) || !is_array($options)) ? [] : $options;
        $last_status = get_option("last_folder_status_for" . $typenow);
        if (!$isAjax && (in_array($typenow, $options) || !empty($last_status)) && (isset($current_screen->base) && ($current_screen->base == "edit" || ($current_screen->base == "upload")))) {
            $default_folders = get_option('default_folders');
            $default_folders = (empty($default_folders) || !is_array($default_folders)) ? [] : $default_folders;

            if (!empty($last_status)) {
                $status = 1;
                if ($last_status != "-1" && $last_status != "all") {
                    $type = \Folders\Folders\Settings::get_folder_post_type($typenow);
                    $term = get_term_by('slug', $last_status, $type);
                    if (empty($term) || !is_object($term)) {
                        $status = 0;
                    }
                }

                delete_option("last_folder_status_for" . $typenow);
                if ($last_status == "all") {
                    $last_status = "";
                }

                if ($status) {
                    if ($typenow == "attachment") {
                        if (!isset($_REQUEST['media_folder'])) {
                            ?>
                            <script>
                                window.location = '<?php echo esc_url(admin_url("upload.php")) . "?post_type=attachment&media_folder=" . $last_status ?>';
                            </script>
                            <?php
                            exit;
                        }
                    }
                }
            }//end if

            $status = 1;
            if (isset($default_folders[$typenow]) && !empty($default_folders[$typenow])) {
                $type = \Folders\Folders\Settings::get_folder_post_type($typenow);
                if ($default_folders[$typenow] != -1) {
                    $term = get_term_by('slug', $default_folders[$typenow], $type);
                    if (empty($term) || !is_object($term)) {
                        $status = 0;
                    }
                }
            } else {
                $status = 0;
            }

            if ($status) {
                if ($typenow == "attachment") {
                    $admin_url = admin_url("upload.php?post_type=attachment&media_folder=");
                    if (!isset($_REQUEST['media_folder'])) {
                        if (isset($default_folders[$typenow]) && !empty($default_folders[$typenow])) {
                            ?>
                            <script>
                                window.location = '<?php echo esc_url(admin_url("upload.php")) . "?post_type=attachment&media_folder=" . esc_attr($default_folders[$typenow]) ?>';
                            </script>
                            <?php
                            exit;
                        }
                    }
                }
            }
        }//end if

        if (!(\Folders\Folders\Settings::get_folder_post_type('attachment') || \Folders\Folders\Settings::get_folder_post_type('media'))) {
            return;
        }

        if ($typenow == "attachment") {

            self::add_media_scripts();


        } else if (!\Folders\Folders\Settings::is_folders_active('attachment') && \Folders\Folders\Settings::get_folder_post_type('attachment')) {
            global $current_screen;

            $status = apply_filters("check_media_status_for_folders", true);
            if (!$status) {
                return;
            }

            if (!isset($current_screen->base) || $current_screen->base != "plugins") {
                remove_filter("terms_clauses", "TO_apply_order_filter");

                $post_type = 'attachment';
                $folder_type = \Folders\Folders\Settings::get_folder_post_type($post_type);
                $folders = \Folders\Folders\FoldersTree::get_folders($folder_type, $post_type);
                $folders_tree = \Folders\Folders\FoldersTree::get_data_by_post_type($post_type);
                $show_on_top = \Folders\Admin\Settings::get_field_settings('general_settings', 'new_folder_placement') == 'top';
                $is_new_folder_default = \Folders\Admin\Settings::get_field_settings('general_settings', 'open_new_folder_by_default') == 1;
                $new_folder_placement = \Folders\Admin\Settings::get_field_settings('general_settings', 'new_folder_placement');
                $force_sorting = \Folders\Admin\Settings::get_field_settings('general_settings', 'force_sorting');
                $use_folder_undo = \Folders\Admin\Settings::get_field_settings('general_settings', 'use_folder_undo');
                $default_timeout = \Folders\Admin\Settings::get_field_settings('general_settings', 'default_timeout');
                $folder_colors = \Folders\Admin\Settings::get_field_settings('customization_settings', 'folder_colors');
                $show_in_page= \Folders\Admin\Settings::get_field_settings('customization_settings', 'show_in_page');
                $hasValidKey = \Folders\Admin\License::is_license_active();
                $upgradeURL = \Folders\Admin\License::get_pro_url();
                $total_items = \Folders\Folders\Actions\FoldersItems::total_items($post_type);
                $empty_items = \Folders\Folders\Actions\FoldersItems::total_unassigned_items($post_type);

                $dynamic_folder = "";
                if (isset($_GET['dynamic_folder']) && !empty($_GET['dynamic_folder'])) {
                    $dynamic_folder = sanitize_text_field($_GET['dynamic_folder']);
                    $dynamic_folder = rtrim($dynamic_folder, "_anchor");
                }
                $current_url = '';
                $admin_url = '';
                wp_dequeue_script("jquery-jstree");
                // CMS Tree Page View Conflict
                $folder_post_type = 'attachment';
                $post_type = 'attachment';
                ob_start();
                include_once FOLDERS_TEMPLATE_DIR . "folders" . WCP_DS . "sidebar.php";
                $form_content = ob_get_clean();
                wp_enqueue_script('folders-overlayscrollbars', FOLDERS_PLUGIN_URL . 'dist/js/overlayscrollbars.js', [], FOLDERS_VERSION, false);
                wp_enqueue_style('folders-overlayscrollbars', FOLDERS_PLUGIN_URL . 'dist/css/overlayscrollbars.css', [], FOLDERS_VERSION);
                wp_enqueue_script('folders-tree', FOLDERS_PLUGIN_URL . 'dist/js/jstree.js', [], FOLDERS_VERSION, true);
                wp_enqueue_script('folders-jquery-touch', FOLDERS_PLUGIN_URL . 'dist/js/touch.js', ['jquery'], FOLDERS_VERSION, false);
                wp_enqueue_script('folders-spectrum', FOLDERS_PLUGIN_URL . 'dist/js/spectrum.js', ['jquery'], FOLDERS_VERSION, false);
                wp_enqueue_script('folders-media', FOLDERS_PLUGIN_URL . 'dist/js/media-folders.js', ['media-editor', 'media-views', 'jquery', 'jquery-ui-resizable', 'jquery-ui-draggable', 'jquery-ui-droppable', 'jquery-ui-sortable', 'jquery-ui-tooltip', 'backbone'], FOLDERS_VERSION, true);
                wp_localize_script(
                        'folders-media',
                        'folders_settings',
                        [
                            'folders' => $folders,
                            'folders_tree' => \Folders\Folders\FoldersTree::slim_tree($folders_tree),
                            'ajax_url' => admin_url('admin-ajax.php'),
                            'rest_url' => get_rest_url(null, 'folders-settings/v1/'),
                            'rest_nonce' => wp_create_nonce('wp_rest'),
                            'has_valid_key' => $hasValidKey,
                            'upgrade_url' => $upgradeURL,
                            'js_strings' => FoldersAssets::js_strings(),
                            'pro_icon' => FoldersAssets::get_pro_icon(),
                            'post_type' => $folder_post_type,
                            'nonce' => wp_create_nonce("folder_nonce_" . $folder_post_type),
                            'folder_type' => $folder_type,
                            'custom_type' => $folder_type,
                            'show_on_top' => $show_on_top,
                            'new_folder_placement' => $new_folder_placement,
                            'is_new_folder_default' => $is_new_folder_default,
                            'folder_colors' => $folder_colors,
                            'show_in_page' => $show_in_page,
                            'user_access' => \Folders\Folders\UserFolders::get_user_role(),
                            'total_items' => $total_items,
                            'empty_items' => $empty_items,
                            'dynamic_folder' => $dynamic_folder,
                            'page_url' => $admin_url,
                            'force_sorting' => $force_sorting,
                            'use_folder_undo' => $use_folder_undo,
                            'default_timeout' => $default_timeout,
                            'max_upload_size' => wp_max_upload_size(),
                            'max_upload_size_message' => esc_html__('File is too large. Max size is', 'folders') . size_format(wp_max_upload_size()),
                            'folders_modal' => self::get_form_data('attachment'),
                            'form_content' => $form_content,
                            'is_for_media' => 1,
                            'isRTL' => is_rtl() ? 1 : 0,
                        ]
                );
                // Free/Pro URL Change
                wp_enqueue_style('folders-tree', FOLDERS_PLUGIN_URL . 'dist/css/jstree.css', [], FOLDERS_VERSION);
                wp_enqueue_style('folders-spectrum', FOLDERS_PLUGIN_URL . 'dist/css/spectrum.css', [], FOLDERS_VERSION);
                wp_enqueue_style('folders-media-folders', FOLDERS_PLUGIN_URL . 'dist/css/media-folders.css', [], FOLDERS_VERSION);
                wp_enqueue_style('folder-icon', FOLDERS_PLUGIN_URL . 'dist/css/folders-font.css', [], FOLDERS_VERSION);

                $folder_font = \Folders\Admin\Settings::get_field_settings('customization_settings', 'folder_font');
                if(!empty($folder_font)) {
                    $fontList = \Folders\Admin\DefaultSettings::get_font_list();
                    if(isset($fontList[$folder_font]) && $fontList[$folder_font] == 'Google Fonts') {
                        wp_enqueue_style( 'folders-google-fonts', 'https://fonts.googleapis.com/css2?family=' . str_replace(' ', '+', $folder_font) . ':wght@300;400;500;600;700&display=swap', array(), FOLDERS_VERSION );
                    }
                } else {
                    $folder_font = 'Inter';
                    wp_enqueue_style( 'folders-google-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap', array(), FOLDERS_VERSION );
                }
                $custom_css = \Folders\Folders\FoldersAssets::get_custom_css();
                wp_add_inline_style( 'folders-media-folders', $custom_css );
                self::add_media_scripts();

            }//end if
        }//end if

    }//end output_backbone_view_filters()

    /**
     * Enqueue and localize the media folder filter script and styles.
     *
     * Passes the folder list, taxonomy, REST URL, nonce and translated strings
     * to JavaScript as `folders_media_options`.
     *
     * @return void
     */
    public static function add_media_scripts()
    {
// Free/Pro URL Change
        $is_active = 1;

        $hasChild = 0;
        $hasStars = 0;

        $post_type = 'attachment';
        $folders = \Folders\Folders\FoldersTree::get_data_by_post_type($post_type);
        wp_enqueue_script('folders-media-file', FOLDERS_PLUGIN_URL . 'dist/js/media.js', ['media-editor', 'media-views'], FOLDERS_VERSION, true);
        wp_localize_script(
                'folders-media-file',
                'folders_media_options',
                [
                        'terms' => \Folders\Folders\FoldersTree::slim_tree($folders),
                        'taxonomy' => get_taxonomy('media_folder'),
                        'ajax_url' => admin_url("admin-ajax.php"),
                        'post_type' => $post_type,
                        'rest_url'      => get_rest_url( null, 'folders-settings/v1/' ),
                        'rest_nonce'    => wp_create_nonce( 'wp_rest' ),
                        'activate_url' => \Folders\Admin\License::get_pro_url(),
                        'nonce'         => wp_create_nonce("folder_nonce_".$post_type),
                        'is_key_active' => $is_active,
                        'hasStars' => $hasStars,
                        'dynamic_folders' => '',
                        'hasChildren' => $hasChild,
                        'user_access' => \Folders\Folders\UserFolders::get_user_role(),
                        'is_for_media' => 1,
                        'isRTL' => is_rtl() ? 1 : 0,
                        'lang' => [
                            "folder_helps" => esc_html__("Folders helps you upload SVG files too!", 'folders'),
                            "pro_message" => esc_html__("With Folders pro version, you are now experiencing added SVG support on WordPress without needing additional plugins!", 'folders'),
                            "dismiss_message" => esc_html__("Dismiss message", 'folders'),
                            "activate_msg" => esc_html__("WordPress doesn't allow you to upload SVG files, upgrade to Folders Pro and experience added SVG file upload support!", 'folders'),
                            "activate_key" => esc_html__("Upgrade now", 'folders'),
                            'create_new_folder' => esc_html__( '+ Create a New Folder', 'folders' ),
                        ]
                ]
        );
        // Free/Pro URL Change
        wp_enqueue_style('folders-media', FOLDERS_PLUGIN_URL . 'dist/css/media.css', [], FOLDERS_VERSION);
    }

    /**
     * Render the folder modals used inside the media modal.
     *
     * @param string $post_type Post type the modals are for. Available to the templates.
     * @return string Modal HTML.
     */
    public static function get_form_data($post_type = 'attachment') {
        ob_start();
        include_once FOLDERS_TEMPLATE_DIR . 'folders/modals/add-sub-folder.php';
        include_once FOLDERS_TEMPLATE_DIR . 'folders/modals/add-folder.php';
        include_once FOLDERS_TEMPLATE_DIR . 'folders/modals/rename-folder.php';
        include_once FOLDERS_TEMPLATE_DIR . 'folders/modals/delete-folder.php';
        include_once FOLDERS_TEMPLATE_DIR . 'folders/modals/footer-loader.php';
        include_once FOLDERS_TEMPLATE_DIR . 'folders/modals/remove-post-from-folder.php';
        include_once FOLDERS_TEMPLATE_DIR . 'folders/modals/notifications.php';
        include_once FOLDERS_TEMPLATE_DIR . 'folders/modals/delete-multiple-folders.php';
        include_once FOLDERS_TEMPLATE_DIR . 'folders/modals/select-folders-modal.php';
        include_once FOLDERS_TEMPLATE_DIR . 'folders/modals/bulk-action-modal.php';

        $shortcut_status = \Folders\Admin\Settings::get_field_settings('general_settings', 'use_shortcuts');
        if($shortcut_status) {
            include_once FOLDERS_TEMPLATE_DIR . 'folders/modals/shortcut.php';
        }
        return ob_get_clean();
    }

    /**
     * Filter grid-mode Media Library queries by the selected folder.
     *
     * Replaces the `media_folder` query argument with a tax query for that
     * folder only (not its subfolders), or, for "-1", attachments that are not
     * in any folder. Hooked to `ajax_query_attachments_args`.
     *
     * @param array $args WP_Query arguments for the attachments request.
     * @return array Modified query arguments.
     */
    public function filter_attachments_grid($args)
    {
        $taxonomy = 'media_folder';

        // Some plugins answer the attachments request with their own handler
        // and keep only the query arguments they know - Media Library
        // Assistant does this whenever its Media Library enhancements are on.
        // They still run this filter, so take the selected folder from the
        // original request when it has been dropped.
        if (!isset($args[$taxonomy]) && wp_doing_ajax() && isset($_REQUEST['query']) && is_array($_REQUEST['query'])
            && isset($_REQUEST['query'][$taxonomy]) && is_scalar($_REQUEST['query'][$taxonomy])) {
            $args[$taxonomy] = sanitize_text_field(wp_unslash($_REQUEST['query'][$taxonomy]));
        }

        if (!isset($args[$taxonomy])) {
            return $args;
        }

        $term = sanitize_text_field($args[$taxonomy]);
        if ($term === "") {
            return $args;
        }

        if ($term != "-1") {
            if(empty($args[$taxonomy])) {
                return $args;
            }
            unset($args[$taxonomy]);
            $args['tax_query'] = isset($args['tax_query']) ? $args['tax_query'] : [];
            $args['tax_query'][] = [
                    'taxonomy'         => $taxonomy,
                    'field'            => is_numeric($term) ? 'term_id' : 'slug',
                    'terms'            => $term,
                    'include_children' => false,
            ];
            return $args;
        }

        unset($args[$taxonomy]);
        $args['tax_query'] = [
                [
                        'taxonomy' => $taxonomy,
                        'operator' => 'NOT EXISTS',
                ],
        ];
        $args = apply_filters('media_library_organizer_media_filter_attachments_grid', $args);
        return $args;

    }//end filter_attachments_grid()

    /**
     * Assign a newly uploaded attachment to the currently selected media folder.
     *
     * The folder is read from the `selected_media_folder_folder` option.
     * Hooked to `add_attachment`.
     *
     * @param int $post_id Attachment ID.
     * @return void
     */
    function save_media_terms($post_id)
    {
        if (wp_is_post_revision($post_id)) {
            return;
        }

        $post = get_post($post_id);
        if ($post->post_type !== 'attachment') {
            return;
        }

        if (!\Folders\Folders\Settings::check_for_folder('attachment')) {
            return;
        }

        $post_type = \Folders\Folders\Settings::get_folder_post_type('attachment');
        $selected_folder = get_option("selected_{$post_type}_folder");
        if ($selected_folder != null && !empty($selected_folder)) {
            $terms = get_term($selected_folder);
            if (!empty($terms) && isset($terms->term_id)) {
                wp_set_post_terms($post_id, $terms->term_id, $post_type, false);
            }
        }

    }//end save_media_terms()
}
