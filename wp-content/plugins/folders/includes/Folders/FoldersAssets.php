<?php
namespace Folders\Folders;

defined( 'ABSPATH' ) || exit;

/**
 * Admin assets for the folder sidebar, media screens and plugin pages.
 *
 * Enqueues and localizes the scripts and styles for the folder sidebar on
 * list screens, the Media Library and upload screens, the Media Cleaning
 * page and the plans page, and styles the "Upgrade to Pro" menu item.
 */
class FoldersAssets {

    /**
     * Register the asset and admin head hooks.
     */
    public function __construct() {
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_folders_scripts' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_media_scripts' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_folders_styles' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'media_cleaning_assets' ) );

        add_action( 'admin_enqueue_scripts', array( $this, 'plan_folders_scripts' ) );
        add_action( 'admin_head', array( $this, 'admin_head' ) );
    }

    /**
     * Output inline CSS that styles the last submenu item ("Upgrade to Pro") of
     * the Folders settings menu as a button.
     *
     * @return void
     */
    public function admin_head()
    {
        ?>
        <style>
            #adminmenu .toplevel_page_wcp_folders_settings > ul > li:last-child {
                padding: 5px 10px;
            }
            #adminmenu .toplevel_page_wcp_folders_settings > ul > li:last-child a {
                display: flex;
                background-color: #B78DEB;
                border-radius: 6px;
                font-size: 12px;
                gap: 4px;
                padding: 4px 8px;
                color: #ffffff;
                align-items: center;
                transition: all 0.2s linear;
                font-weight: normal;
                box-shadow: 0px 6px 8px 0px #B78DEB3D;
                justify-content: center;
            }
            #adminmenu .toplevel_page_wcp_folders_settings > ul > li:last-child a:hover, #adminmenu .toplevel_page_wcp_folders_settings > ul > li:last-child a:focus, #adminmenu .toplevel_page_wcp_folders_settings > ul > li:last-child a.current {
                box-shadow: 0px 6px 8px 0px #B78DEB3D;
                color: #ffffff !important;
                background-color: #9565d0;
                font-weight: normal;
            }
            #adminmenu .toplevel_page_wcp_folders_settings > ul > li:last-child a span {
                flex: 0 0 16px;
                height: 16px;
                background-color: #c5a4ef;
                border-radius: 4px;
                padding: 2px;
                display: inline-flex;
                transition: all 0.2s linear;
            }
            #adminmenu .toplevel_page_wcp_folders_settings > ul > li:last-child a:hover span {
                background-color: #B78DEB;
            }
            #adminmenu .toplevel_page_wcp_folders_settings > ul > li:last-child a span svg {
                width: 100%;
                height: 100%;
            }
        </style>
        <?php
    }

    /**
     * Enqueue the pricing table styles and slider script on the plans pages.
     *
     * Loaded on the Upgrade to Pro page and on the "manage-folders-plan" tab of
     * the settings page.
     *
     * @param string $page Current admin page hook suffix.
     * @return void
     */
    public function plan_folders_scripts($page)
    {
        $status = 1;
        if($page !== 'folders-settings_page_folders-upgrade-to-pro' && $page != 'settings_page_premio-folders-settings') {
            $status = 0;
        }



        $tab = filter_input(INPUT_GET, 'tab');
        $is_manage_plan_tab = in_array( $page, [ 'settings_page_premio-folders-settings', 'toplevel_page_wcp_folders_settings' ], true ) && $tab === 'manage-folders-plan';

        if(!$status && !$is_manage_plan_tab ) {
            $status = 0;
        }

        if(!$status) {
            return;
        }

        wp_enqueue_style('folders-pricing-table', FOLDERS_PLUGIN_URL . 'dist/css/pricing-table.css', [], FOLDERS_VERSION);
        wp_enqueue_script('folders-slick', FOLDERS_PLUGIN_URL . 'dist/js/slick.js', ['jquery'], FOLDERS_VERSION, true);
    }

    /**
     * Enqueue and localize the Media Cleaning assets.
     *
     * Loaded on the Media Cleaning page and on the Media Library trash view.
     *
     * @param string $page Current admin page hook suffix.
     * @return void
     */
    public static function media_cleaning_assets($page) {
        if (!($page == "media_page_folders-media-cleaning" || ($page == "upload.php" && isset($_GET['attachment-filter']) && $_GET['attachment-filter'] == "trash"))) {
            return;
        }
        wp_enqueue_style('folders-folders-media', FOLDERS_PLUGIN_URL . 'dist/css/media-clean.css', [], FOLDERS_VERSION);
        wp_enqueue_script('folders-media-cleaning', FOLDERS_PLUGIN_URL . 'dist/js/media-cleaning.js', ['jquery'], FOLDERS_VERSION, false);
        wp_localize_script(
            'folders-media-cleaning',
            'folders_settings',
            [
                'ajax_url'          => admin_url( 'admin-ajax.php' ),
                'rest_url'          => get_rest_url( null, 'folders-media-cleaning/v1/' ),
                'rest_nonce'        => wp_create_nonce( 'wp_rest' ),
                'nonce'             => wp_create_nonce("remove_multiple_scanned_files"),
                'trash_enabled'     => (defined("MEDIA_TRASH") && MEDIA_TRASH == true) ? 1 : 0,
                'button_text'       => (defined("MEDIA_TRASH") && MEDIA_TRASH == true) ? esc_html__("Move to Trash", 'folders') : esc_html__("Delete Permanently", 'folders'),
                'steps'             => self::folder_scan_steps(),
                'step'              => esc_html__("Step", 'folders'),
                'isRTL'             => is_rtl() ? 1 : 0,
                'is_for_media'      => 0,
            ]
        );
    }

    /**
     * Get the steps shown in the Media Cleaning scan progress.
     *
     * @return array[] Steps, each with a translated `title` and an `action_name`.
     */
    public static function folder_scan_steps() {
        return [
            [
                'title' => esc_html__('Cleaning Old Data', 'folders'),
                'action_name' => 'cleaning_data'
            ],
            [
                'title' => esc_html__('Scanning Post Content', 'folders'),
                'action_name' => 'content_scan'
            ],
            [
                'title' => esc_html__('Scanning Categories', 'folders'),
                'action_name' => 'category_scan'
            ],
            [
                'title' => esc_html__('Scanning ACF Content', 'folders'),
                'action_name' => 'content_acf_scan'
            ],
            [
                'title' => esc_html__('Scanning Media', 'folders'),
                'action_name' => 'media_scan'
            ]
        ];
    }

    /**
     * Enqueue the folder picker for the Media Library and "Add New Media" screens.
     *
     * Renders the "add media folder" form and passes it to the script together
     * with the REST URL, nonces and translated strings. Only runs when folders
     * are enabled for media.
     *
     * @param string $hook Current admin page hook suffix.
     * @return void
     */
    public function enqueue_media_scripts($hook)
    {
        if($hook !== 'media-new.php' && $hook !== 'upload.php') {
            return;
        }
        if (!\Folders\Folders\Settings::check_for_folder('attachment')) {
            return;
        }
        wp_enqueue_style('folders-new-media', FOLDERS_PLUGIN_URL . 'dist/css/media.css', [], FOLDERS_VERSION);
        wp_enqueue_script('folders-new-media', FOLDERS_PLUGIN_URL . 'dist/js/new-media.js', ['jquery'], FOLDERS_VERSION, true);
        $hide_add_folder = true;
        ob_start();
        include_once FOLDERS_TEMPLATE_DIR . 'folders/modals/add-media-folder.php';
        $form_html = ob_get_clean();

        $hasValidKey = \Folders\Admin\License::is_license_active();
        $upgradeURL = \Folders\Admin\License::get_pro_url();

        wp_localize_script(
            'folders-new-media',
            'new_media_folders_settings',
            [
                'ajax_url'      => admin_url( 'admin-ajax.php' ),
                'rest_url'      => get_rest_url( null, 'folders-settings/v1/' ),
                'rest_nonce'    => wp_create_nonce( 'wp_rest' ),
                'has_valid_key' => $hasValidKey,
                'upgrade_url'   => $upgradeURL,
                'js_strings'    => self::js_strings(),
                'form_html'     => $form_html,
                'post_type'     => 'attachment',
                'nonce'         => wp_create_nonce("folder_nonce_attachment"),
            ]
        );
    }

    /**
     * Enqueue and localize the folder sidebar scripts on screens where folders are active.
     *
     * Passes the folder list and tree, item counts, URLs, nonces, settings and
     * translated strings to JavaScript as `folders_settings`. Also counts page
     * views of the post, page and media list screens, which decides when the
     * rating modal is shown.
     *
     * @return void
     */
    public function enqueue_folders_scripts() {
        $is_active = \Folders\Folders\Settings::is_folders_active();
        if(!$is_active) {
            return;
        }
        global $typenow;
        $post_type = $typenow;
        $folder_type = \Folders\Folders\Settings::get_folder_post_type($typenow);
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

        wp_enqueue_script('folders-folderstree', FOLDERS_PLUGIN_URL . 'dist/js/jstree.js', ['jquery'], FOLDERS_VERSION, true);
        wp_enqueue_script('folders-overlayscrollbars', FOLDERS_PLUGIN_URL . 'dist/js/overlayscrollbars.js', ['jquery'], FOLDERS_VERSION, false);
        wp_enqueue_script('folders-spectrum', FOLDERS_PLUGIN_URL . 'dist/js/spectrum.js', ['jquery'], FOLDERS_VERSION, false);
        wp_enqueue_script('folders-app', FOLDERS_PLUGIN_URL . 'dist/js/folders.js', ['jquery', 'jquery-ui-resizable', 'jquery-ui-draggable', 'jquery-ui-droppable', 'jquery-ui-sortable', 'jquery-ui-tooltip','backbone'], FOLDERS_VERSION, true);
        $hasValidKey = \Folders\Admin\License::is_license_active();
        $upgradeURL = \Folders\Admin\License::get_pro_url();
        $total_items = \Folders\Folders\Actions\FoldersItems::total_items($typenow);
        $empty_items = \Folders\Folders\Actions\FoldersItems::total_unassigned_items($typenow);

        if ($typenow == "attachment") {
            $admin_url = admin_url("upload.php?post_type=attachment&media_folder=");
            global $current_user;
            if (isset($current_user->ID)) {
                $userMode = get_user_option('media_library_mode', get_current_user_id()) ? get_user_option('media_library_mode', get_current_user_id()) : 'grid';
                if ($userMode == "list") {
                    $admin_url = admin_url("upload.php?post_type=attachment");
                    $search = filter_input(INPUT_GET, "s");
                    if (!empty($search)) {
                        $admin_url .= "&s=" . esc_attr($search);
                    }

                    if (!empty($post_status)) {
                        $admin_url .= "&post_status=" . sanitize_text_field($post_status);
                    }

                    if (isset($_REQUEST['paged']) && !empty($_REQUEST['paged']) && is_numeric($_REQUEST['paged'])) {
                        $paged = (int)sanitize_text_field($_REQUEST['paged']);
                        if (!empty($paged)) {
                            $admin_url .= "&paged=" . esc_attr($paged);
                        }
                    }

                    $admin_url .= "&" . esc_attr($folder_type) . "=";
                }
            }
        } else {
            $admin_url = admin_url("edit.php?post_type=" . $typenow);
            if (isset($_GET['s']) && !empty($_GET['s'])) {
                $admin_url .= "&s=" . urlencode(sanitize_text_field($_GET['s']));
            }

            if (!empty($post_status)) {
                $admin_url .= "&post_status=" . sanitize_text_field($post_status);
            }


            if (isset($_REQUEST['paged']) && !empty($_REQUEST['paged']) && is_numeric($_REQUEST['paged'])) {
                $paged = (int)sanitize_text_field($_REQUEST['paged']);
                if (!empty($paged)) {
                    $admin_url .= "&paged=" . esc_attr($paged);
                }
            }

            $admin_url .= "&{$folder_type}=";
        }


        $current_url = $admin_url;
        if (isset($_GET[$folder_type]) && !empty($_GET[$folder_type])) {
            $current_url .= sanitize_text_field($_GET[$folder_type]);
        }

        $dynamic_folder = "";
        if (isset($_GET['dynamic_folder']) && !empty($_GET['dynamic_folder'])) {
            $dynamic_folder = sanitize_text_field($_GET['dynamic_folder']);
            $dynamic_folder = rtrim($dynamic_folder, "_anchor");
        }

        wp_localize_script(
            'folders-app',
            'folders_settings',
            [
                'folders'       => $folders,
                'folders_tree' => \Folders\Folders\FoldersTree::slim_tree($folders_tree),
                'ajax_url'      => admin_url( 'admin-ajax.php' ),
                'rest_url'      => get_rest_url( null, 'folders-settings/v1/' ),
                'rest_nonce'    => wp_create_nonce( 'wp_rest' ),
                'has_valid_key' => $hasValidKey,
                'upgrade_url'   => $upgradeURL,
                'js_strings'    => self::js_strings(),
                'pro_icon'      => self::get_pro_icon(),
                'post_type'     => $typenow,
                'nonce'         => wp_create_nonce("folder_nonce_".$typenow),
                'folder_type'   => $folder_type,
                'custom_type'   => $folder_type,
                'show_on_top'   => $show_on_top,
                'new_folder_placement'   => $new_folder_placement,
                'is_new_folder_default' => $is_new_folder_default,
                'folder_colors' => $folder_colors,
                'show_in_page' => $show_in_page,
                'user_access'   => \Folders\Folders\UserFolders::get_user_role(),
                'total_items'   => $total_items,
                'empty_items'   => $empty_items,
                'current_url'   => $current_url,
                'dynamic_folder' => $dynamic_folder,
                'page_url'      => $admin_url,
                'force_sorting' => $force_sorting,
                'use_folder_undo' => $use_folder_undo,
                'default_timeout' => $default_timeout,
                'max_upload_size' => wp_max_upload_size(),
                'max_upload_size_message' => esc_html__('File is too large. Max size is', 'folders') . size_format( wp_max_upload_size() ) . '.',
                'isRTL'           => is_rtl() ? 1 : 0,
                'is_for_media' => 0,
            ]
        );

        $page_views = get_option("get_folders_page_views");
        if($page_views != -1 && $page_views != 3 && $page_views != 4) {
            $page_views = ($page_views)?intval($page_views):0;
            if ($typenow == "post" && count($_GET) == 0) {
                $page_views++;
            } else if ($typenow == "page" && count($_GET) == 1) {
                $page_views++;
            } else if ($typenow == "attachment" && count($_GET) == 0) {
                $page_views++;
            }
            if($page_views == 1) {
                add_option("get_folders_page_views", $page_views);
            } else {
                update_option("get_folders_page_views", $page_views);
            }
        }
    }

    /**
     * Enqueue the folder sidebar styles, the selected font and the customization CSS.
     *
     * @return void
     */
    public function enqueue_folders_styles() {
        $is_active = \Folders\Folders\Settings::is_folders_active();
        if($is_active) {
            wp_enqueue_style( 'folders-fonts', FOLDERS_PLUGIN_URL . 'dist/css/folders-font.css', array(), FOLDERS_VERSION );
            wp_enqueue_style('folders-folderstree', FOLDERS_PLUGIN_URL . 'dist/css/jstree.css', [], FOLDERS_VERSION);
            wp_enqueue_style('folders-overlayscrollbars', FOLDERS_PLUGIN_URL . 'dist/css/overlayscrollbars.css', [], FOLDERS_VERSION);
            wp_enqueue_style('folders-app', FOLDERS_PLUGIN_URL . 'dist/css/folders.css', [], FOLDERS_VERSION);
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

            wp_add_inline_style( 'folders-app', self::get_custom_css());
        }
    }

    /**
     * Build the CSS custom properties for the folder sidebar.
     *
     * Covers button, background, hover and icon colors, the saved sidebar width
     * for the current post type, and the font size and family from the
     * customization settings.
     *
     * @return string CSS rule for `:root`.
     */
    public static function get_custom_css()
    {
        $settings = \Folders\Admin\Settings::get_field_settings('customization_settings');
        $folder_size = $settings['folder_size'] == 'custom' ? $settings['custom_font_size'] : $settings['folder_size'];

        $bg_color = $settings['folder_bg_color'] ?? '#FA166B';
        $hover_color = $bg_color;
        if ( preg_match( '/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $bg_color ) ) {
            $hex = ltrim( $bg_color, '#' );
            if ( strlen( $hex ) == 3 ) {
                $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
            }
            $r = hexdec( substr( $hex, 0, 2 ) );
            $g = hexdec( substr( $hex, 2, 2 ) );
            $b = hexdec( substr( $hex, 4, 2 ) );
            $hover_color = "rgba($r, $g, $b, 0.08)";
        }

        global $typenow;

        $width = get_option('wcp_dynamic_width_for_'.$typenow);
        if(empty($width) || !is_numeric($width)) {
            $width = 284;
        }
        $folder_font = $settings['folder_font'];
        if(empty($folder_font)) {
            $folder_font = 'Inter';
        }
        $width = intval($width);
        if($width > 500) {
            $width = 500;
        }
        $custom_css = ':root {';
        $custom_css .= '--add-folder-button-color: ' . esc_attr($settings['new_folder_color'] ? $settings['new_folder_color'] : '#FA166B') . ';';
        $custom_css .= '--add-folder-dropdown-border-color: ' . esc_attr($settings['dropdown_color'] ? $settings['dropdown_color'] : '#484848') . ';';
        $custom_css .= '--folders-background-color: ' . esc_attr($bg_color) . ';';
        $custom_css .= '--folders-sidebar-width: ' . esc_attr($width) . 'px;';
        $custom_css .= '--folders-sidebar-content: ' . esc_attr($width + 20) . 'px;';
        $custom_css .= '--folders-default-color: ' . esc_attr($settings['default_icon_color'] ? $settings['default_icon_color'] : '#334155') . ';';
        $custom_css .= '--folders-background-hover-color: ' . esc_attr($hover_color) . ';';
        $custom_css .= '--folders-bulk-organize-bg-color: ' . esc_attr($settings['bulk_organize_button_color'] ? $settings['bulk_organize_button_color'] : '#FA166B') . ';';
        $custom_css .= '--folders-font-size: ' . esc_attr($folder_size ? $folder_size : '16') . 'px;';
        $custom_css .= '--folders-font-family: "' . esc_attr($folder_font) . '", sans-serif;';
        if (is_plugin_active('admin-site-enhancements/admin-site-enhancements.php') && defined('ASENHA_VERSION')) {
            $options = get_option('admin_site_enhancements');
            if (isset($options['wider_admin_menu']) && $options['wider_admin_menu'] && isset($options['admin_menu_width']) && !empty($options['admin_menu_width'])) {
                $admin_width = str_replace('px','', $options['admin_menu_width']);
                $custom_css .= '--folders-sidebar-space: ' . esc_attr($admin_width) . 'px;';
            }
        }
        $custom_css .= '}';
        return $custom_css;
    }

    /**
     * Get the translated strings used by the folder sidebar JavaScript.
     *
     * @return array<string, string> Strings keyed by identifier.
     */
    public static function js_strings()
    {
        return [
            'add_new_folder' => esc_html__( 'New Sub-folder', 'folders' ),
            'new_folder' => esc_html__( 'New Folder', 'folders' ),
            'activate' => esc_html__( '(Activate)', 'folders' ),
            'rename' => esc_html__( 'Rename', 'folders' ),
            'icon_color' => esc_html__( 'Icon Color', 'folders' ),
            'sticky_folder' => esc_html__( 'Sticky Folder', 'folders' ),
            'remove_sticky_folder' => esc_html__( 'Remove Sticky Folder', 'folders' ),
            'add_star' => esc_html__( 'Add Star', 'folders' ),
            'remove_star' => esc_html__( 'Remove Star', 'folders' ),
            'lock_folder' => esc_html__( 'Lock Folder', 'folders' ),
            'unlock_folder' => esc_html__( 'Unlock Folder', 'folders' ),
            'duplicate_folder' => esc_html__( 'Duplicate Folder', 'folders' ),
            'download_folder' => esc_html__( 'Download Folder', 'folders' ),
            'remove_default_folder' => esc_html__( 'Remove default folder', 'folders' ),
            'open_by_default' => esc_html__( 'Open this folder by default', 'folders' ),
            'cut' => esc_html__( 'Cut', 'folders' ),
            'copy' => esc_html__( 'Copy', 'folders' ),
            'delete' => esc_html__( 'Delete', 'folders' ),
            'paste' => esc_html__( 'Paste', 'folders' ),
            'required_error_message' => esc_html__( 'This field is required.', 'folders' ),
            'empty_file_name' => esc_html__( 'Folder name must contain at least one letter or number.', 'folders' ),
            'invalid_file_name' => esc_html__( 'Please use only letters, numbers, dashes, underscores, and hashes.', 'folders' ),
            'ONE_ITEM' => esc_html__( 'One item', 'folders' ),
            'SELECT_ITEMS' => esc_html__( 'Select items', 'folders' ),
            'ITEMS' => esc_html__( 'Items', 'folders' ),
            'SELECTED' => esc_html__( 'Selected', 'folders' ),
            'remove_icon_color' => esc_html__( 'Remove icon color', 'folders' ),
            'error' => esc_html__( 'Something went wrong', 'folders' ),
            'no_folder_selected' => esc_html__( 'Select folders to delete', 'folders' ),
            'locked_folders_delete_message' => esc_html__( "Locked folders can't be deleted", 'folders' ),
            'select_item' => esc_html__( 'Please select items to move to folder', 'folders' ),
            'select_folder' => esc_html__( 'Select folder', 'folders' ),
            'lock_all_folders' =>  'Use this to lock a folder\'s position so it cannot be moved\n\nClick to lock all folders',
            'unlock_all_folders' => 'Use this to unlock a folder\'s position so it cannot be moved\n\nClick to Unlock all folders',
            'deleted_message' => esc_html__( 'Successfully deleted!', 'folders' ),
            'success_message' => esc_html__( 'Successfully updated!', 'folders' ),
            'lock_seletec_folders' => esc_html__( 'Lock selected folders', 'folders' ),
            'unlock_seletec_folders' => esc_html__( 'Unlock selected folders', 'folders' ),
            'bulk_organize'       =>  esc_html__( 'Bulk organize', 'folders' ),
            'drag_and_drop'       =>  esc_html__( 'Drag and drop your media files to the relevant folders', 'folders' ),
            'select_all'       =>  esc_html__( 'Select all', 'folders' ),
            'move_selected_files'       =>  esc_html__( 'Move selected folders', 'folders' ),
            'uploading_files'       =>  esc_html__( 'Uploading files', 'folders' ),
            'unassigned_folders'       =>  esc_html__( 'Unassigned', 'folders' ),
            'create_new_folder' => esc_html__( '+ Create a New Folder', 'folders' ),
        ];
    }

    /**
     * Get the crown icon shown next to Pro features.
     *
     * @return string Inline SVG markup.
     */
    public static function get_pro_icon()
    {
        return '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M1.7733 13.1104C1.39311 10.6392 1.01292 8.16797 0.632733 5.69672C0.548421 5.14891 1.17173 4.77528 1.61511 5.10784C2.79961 5.99622 3.98405 6.88453 5.16855 7.77291C5.55855 8.06541 6.11417 7.97022 6.38455 7.56459L9.34286 3.12709C9.65548 2.65816 10.3445 2.65816 10.6571 3.12709L13.6154 7.56459C13.8858 7.97022 14.4414 8.06534 14.8314 7.77291C16.0159 6.88453 17.2004 5.99622 18.3849 5.10784C18.8282 4.77528 19.4515 5.14891 19.3673 5.69672C18.9871 8.16797 18.6069 10.6392 18.2267 13.1104H1.7733Z" fill="#FFB743"/>
            <path d="M17.369 17.2246H2.63125C2.1575 17.2246 1.77344 16.8405 1.77344 16.3668V14.4824H18.2269V16.3668C18.2268 16.8405 17.8428 17.2246 17.369 17.2246Z" fill="#FFB743"/>
            </svg>';
    }
}
