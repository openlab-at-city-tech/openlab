<?php
namespace Folders\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Admin menu registration.
 *
 * Registers the Folders settings menu (top-level or under Settings), the
 * Recommended Plugins, Upgrade to Pro and Media Cleaning submenus, optional
 * per-post-type folder menus, and the plugin action links.
 */
class Menu {

    /**
     * Register admin menu, admin footer and plugin action link hooks.
     */
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'register_admin_menu' ) );
        add_action( 'in_admin_footer', array( $this, 'register_admin_footer' ) );
        add_filter('plugin_action_links_' . FOLDERS_PLUGIN_BASE, [$this, 'plugin_action_links']);
    }

    /**
     * Add "Settings" and "Need help?" links to the plugin row on the Plugins screen.
     *
     * @param array $links Existing plugin action links.
     * @return array Modified action links.
     */
    public function plugin_action_links($links)
    {
        $settingsURL = \Folders\Admin\Settings::get_setting_page_url();
        array_unshift($links, '<a href="' . esc_url($settingsURL). '" >' . esc_html__('Settings', 'folders') . '</a>');
        $links['need_help'] = '<a target="_blank" href="https://wordpress.org/support/plugin/folders/" >' . __('Need help?', 'folders') . '</a>';
        return $links;
    }

    /**
     * Output the settings modals and plugin footer on Folders admin pages.
     *
     * Hooked to `in_admin_footer`; fires the `folders_settings_modal` and
     * `folders_footer` actions.
     *
     * @param string $page Unused; the page is read from `$_GET['page']`.
     * @return void
     */
    public function register_admin_footer($page) {
        $page = isset($_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
        if ( in_array( $page, [ 'wcp_folders_settings', 'folders-license-settings', 'recommended-folder-plugins', 'premio-folders-settings' ], true ) ) {
            do_action( 'folders_settings_modal' );
            do_action( 'folders_footer' );
        }
    }

    /**
     * Register the Folders admin menu and submenus.
     *
     * Adds the settings page under Settings or as a top-level menu depending on
     * the "show folder in settings" option, plus the Recommended Plugins,
     * Upgrade to Pro and Media Cleaning pages, and the per-post-type folder menus
     * when "show folders in menu" is enabled.
     *
     * @return void
     */
    public function register_admin_menu() {
        $foldersInSettings = \Folders\Admin\Settings::get_field_settings( 'general_settings', 'show_folder_in_settings' );
        $menu_slug         = 'wcp_folders_settings';

        if ( 1 == $foldersInSettings ) {
            add_options_page(
                esc_html__( 'Folders Settings', 'folders' ),
                esc_html__( 'Folders Settings', 'folders' ),
                'manage_options',
                'premio-folders-settings',
                array( $this, 'settings_page' )
            );
        } else {
            add_menu_page(
                esc_html__( 'Folders Settings', 'folders' ),
                esc_html__( 'Folders Settings', 'folders' ),
                'manage_options',
                $menu_slug,
                array( $this, 'settings_page' ),
                'dashicons-category',
                80
            );

            $recommended_plugin = get_option( 'hide_folder_recommended_plugin' );
            if ( false === $recommended_plugin ) {
                add_submenu_page(
                    $menu_slug,
                    esc_html__( 'Recommended Plugins', 'folders'),
                    esc_html__( 'Recommended Plugins', 'folders'),
                    'manage_options',
                    'recommended-folder-plugins',
                    [
                        $this,
                        'recommended_plugins',
                    ]
                );
            }

            $menuTitle = esc_html__( 'Upgrade to Pro', 'folders');

            add_submenu_page(
                $menu_slug,
                $menuTitle,
                $menuTitle,
                'manage_options',
                'folders-upgrade-to-pro',
                [
                    $this,
                    'upgrade_page',
                ]
            );
        }

        $status = get_option('hide_folders_media_cleaning_menu', 'no');
        if($status == 'no') {
            add_submenu_page(
                "upload.php",
                esc_html__('Media Cleaning', 'folders'),
                esc_html__('Media Cleaning', 'folders'),
                'manage_options',
                'folders-media-cleaning',
                [
                    $this,
                    'media_cleaning',
                ]
            );
        }

        $status = \Folders\Admin\Settings::get_field_settings( 'general_settings', 'folders_show_in_menu' );
        if($status) {
            self::create_menu_for_folders();
        }
    }

    /**
     * Render the media cleaning page.
     *
     * @return void
     */
    public function media_cleaning() {
        include_once FOLDERS_TEMPLATE_DIR . 'folders/media-cleaning.php';
    }

    /**
     * Add a top-level admin menu for each folder-enabled post type.
     *
     * Each menu links to the post type's list screen and gets a submenu entry
     * per top-level folder.
     *
     * @return void
     */
    private static function create_menu_for_folders() {
        global $menu;

        $folder_types = \Folders\Folders\Settings::get_settings();
        if (empty($folder_types)) {
            return;
        }

        foreach ($folder_types as $type) {
            if ($type == "folders4plugins") {
                continue;
            }
            $itemKey = self::searchForId($type, $menu);
            switch (true) {
                case ($type == 'attachment'):
                    $itemKey = 10;
                    $edit = 'upload.php';
                    break;
                case ($type === 'post'):
                    $edit = 'edit.php';
                    $itemKey = 5;
                    break;
                default:
                    $edit = 'edit.php';
                    break;
            }

            $folder = ($type == 'attachment') ? 'media' : $type;
            $upper = ($type == 'attachment') ? 'Media' : ucwords(str_replace(['-', '_'], ' ', $type));

            if ($type == 'page') {
                $tax_slug = 'folder';
            } else if ($type == 'attachment' || $type == 'media') {
                $tax_slug = 'media_folder';
            } else {
                $tax_slug = $folder . '_folder';
            }

            $hide_empty = true;
            if ($type == 'attachment') {
                $hide_empty = false;
                add_menu_page('Media Folders', 'Media Folders', 'publish_pages', "{$edit}?post_type=attachment&media_folder=", false, 'dashicons-portfolio', "{$itemKey}.5");
            } else {
                add_menu_page($upper . ' Folders', "{$upper} Folders", 'publish_pages', "{$edit}?post_type={$type}&type=folder", false, 'dashicons-portfolio', "{$itemKey}.5");
            }

            $terms = get_terms(
                [
                    'taxonomy' => $tax_slug,
                    'hide_empty' => $hide_empty,
                    'parent' => 0,
                    'orderby' => 'meta_value_num',
                    'order' => 'ASC',
                    'hierarchical' => false,
                    'meta_query' => [
                        [
                            'key' => 'wcp_custom_order',
                            'type' => 'NUMERIC',
                        ],
                    ],
                ]
            );

            if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
                foreach ($terms as $term) {
                    if (isset($term->trash_count) && !empty($term->trash_count)) {
                        if ($type == 'attachment') {
                            add_submenu_page("{$edit}?type=folder", $term->name, $term->name, 'publish_pages', "{$edit}?post_type=attachment&media_folder={$term->slug}", false);
                        } else {
                            add_submenu_page("{$edit}?post_type={$type}&type=folder", $term->name, $term->name, 'publish_pages', "{$edit}?post_type={$type}&{$tax_slug}={$term->slug}", false);
                        }
                    }
                }
            }
        }
    }

    /**
     * Find the position of a post type's entry in the global admin menu.
     *
     * Compares the value after "=" in each menu item's slug (for example
     * `edit.php?post_type=product`) with the given post type.
     *
     * @param string $id   Post type to look for.
     * @param array  $menu Global `$menu` array.
     * @return int|string|null Menu position key, or null when not found.
     */
    private static function searchForId($id, $menu)
    {
        if ($menu) {
            foreach ($menu as $key => $val) {
                if (array_key_exists(2, $val)) {
                    $stripVal = explode('=', $val[2]);
                }

                if (array_key_exists(1, $stripVal)) {
                    $stripVal = $stripVal[1];
                }

                if ($stripVal === $id) {
                    return $key;
                }
            }
        }

    }//end searchForId()

    /**
     * Render the upgrade-to-pro page.
     *
     * @return void
     */
    public function upgrade_page() {
        include_once FOLDERS_TEMPLATE_DIR . 'plans/upgrade-to-pro.php';
    }

    /**
     * Render the Folders settings page.
     *
     * Shows the email sign-up screen when it has not been dismissed yet, the
     * plans page for the "manage-folders-plan" tab, and otherwise the settings
     * screen. The intro video modal is shown once, on the first visit.
     *
     * @return void
     */
    public function settings_page() {
        $isShown = \Folders\Folders\FoldersSignup::check_modal_status();
        if ($isShown) {
            include_once FOLDERS_TEMPLATE_DIR . 'folders/email-signup.php';
            return;
        }
        $tab = filter_input(INPUT_GET, 'tab');
        if($tab == 'manage-folders-plan') {
            include_once FOLDERS_TEMPLATE_DIR . 'plans/upgrade-to-pro.php';
            return;
        }
        include_once FOLDERS_TEMPLATE_DIR . 'folders-settings.php';

        $option = get_option("folder_intro_box");
        if ($option == "show") {
            update_option('folder_intro_box', 'hide');
            include_once FOLDERS_TEMPLATE_DIR . 'modals/folders-video.php';
        }
    }

    /**
     * Render the Recommended Plugins page.
     *
     * @return void
     */
    public function recommended_plugins() {
        include_once FOLDERS_TEMPLATE_DIR . 'recommended-plugins.php';
    }
}
