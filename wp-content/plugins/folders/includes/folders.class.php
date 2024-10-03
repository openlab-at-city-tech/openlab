<?php
/**
 * Class Folders Main
 *
 * @author  : Premio <contact@premio.io>
 * @license : GPL2
 * */

if (! defined('ABSPATH')) {
    exit;
}

// Free/Pro Class name change
class WCP_Folders
{

    /**
     * Instance of Class
     *
     * @var    object    $instance    Instance of Class
     * @since  1.0.0
     * @access public
     */
    private static $instance;

    /**
     * License key data
     *
     * @var    array    $license_key_data    License key data
     * @since  1.0.0
     * @access public
     */
    private static $license_key_data = null;

    /**
     * Folders data
     *
     * @var    object    $folders    Folders data
     * @since  1.0.0
     * @access public
     */
    private static $folders;

    /**
     * Total number of folders
     *
     * @var    integer    $tlfs    total number of folders
     * @since  1.0.0
     * @access public
     */
    public $tlfs = 0;

    /**
     * Collection on post ids
     *
     * @var    array    $postIds    collection on post ids
     * @since  1.0.0
     * @access public
     */
    private static $postIds;

    /**
     * Folders Settings
     *
     * @var    array    $folderSettings    Folders Settings
     * @since  1.0.0
     * @access public
     */
    private static $folderSettings = false;


    /**
     * Folders Settings
     *
     * @var    array    $folders_settings    Folders Settings
     * @since  1.0.0
     * @access public
     */
    var $folders_settings = false;


    /**
     * Define the core functionality of the import data functionality.
     *
     * Add/Update folders settings
     * Add/Update/Remove/List of folders
     * Add/Update/Remove/List posts to folders
     * Mark/Unmark folders
     * Make Sticky/Unsticky folders
     * Add content to folders
     * Update folder sidebar width
     * Filter taxonomies for folders
     * Show drag buttons to post/page table
     *
     * @since 1.0.0
     */
    public function __construct()
    {

        spl_autoload_register([$this, 'autoload']);
        add_action('init', [$this, 'create_folder_terms'], 15);
        add_action('admin_init', [$this, 'folders_register_settings']);
        add_action('admin_menu', [$this, 'admin_menu'], 10000);
        add_action('admin_enqueue_scripts', [$this, 'folders_admin_styles']);
        add_action('admin_enqueue_scripts', [$this, 'folders_admin_scripts']);
        add_filter('plugin_action_links_'.WCP_FOLDERS_PLUGIN_BASE, [$this, 'plugin_action_links']);
        add_action('admin_footer', [$this, 'admin_footer']);

        add_action('parse_tax_query', [$this, 'taxonomy_archive_exclude_children']);
        add_action('admin_footer', [$this, 'admin_footer_for_media']);

        // Save Data
        add_action('wp_ajax_wcp_add_new_folder', [$this, 'wcp_add_new_folder']);
        // Update Data
        add_action('wp_ajax_wcp_update_folder', [$this, 'wcp_update_folder']);
        // Remove Data
        add_action('wp_ajax_wcp_remove_folder', [$this, 'wcp_remove_folder']);
        // Remove Multple Folder
        add_action('wp_ajax_wcp_remove_muliple_folder', [$this, 'remove_muliple_folder']);
        // Save State Data
        add_action('wp_ajax_save_wcp_folder_state', [$this, 'save_wcp_folder_state']);
        // Save State Data
        add_action('wp_ajax_wcp_save_parent_data', [$this, 'wcp_save_parent_data']);
        // Update Parent Data
        add_action('wp_ajax_wcp_update_parent_information', [$this, 'wcp_update_parent_information']);
        // Update Parent Data
        add_action('wp_ajax_wcp_save_folder_order', [$this, 'wcp_save_folder_order']);
        // Update Parent Data
        add_action('wp_ajax_wcp_mark_un_mark_folder', [$this, 'wcp_mark_un_mark_folder']);
        // Update Parent Data
        add_action('wp_ajax_wcp_make_sticky_folder', [$this, 'wcp_make_sticky_folder']);
        // Update Parent Data
        add_action('wp_ajax_wcp_change_post_folder', [$this, 'wcp_change_post_folder']);
        // Update Parent Data
        add_action('wp_ajax_wcp_change_multiple_post_folder', [$this, 'wcp_change_multiple_post_folder']);
        // Update width Data
        add_action('wp_ajax_wcp_change_post_width', [$this, 'wcp_change_post_width']);
        // Update width Data
        add_action('wp_ajax_wcp_change_folder_display_status', [$this, 'wcp_change_folder_display_status']);
        // Update width Data
        add_action('wp_ajax_wcp_change_all_status', [$this, 'wcp_change_all_status']);
        // Update width Data
        add_action('wp_ajax_save_folder_last_status', [$this, 'save_folder_last_status']);
        // Update width Data
        add_action('wp_ajax_wcp_folders_by_order', [$this, 'wcp_folders_by_order']);
        // Update width Data
        add_action('wp_ajax_wcp_remove_all_folders_data', [$this, 'remove_all_folders_data']);
        // Update folders Status
        add_action('wp_ajax_wcp_update_folders_uninstall_status', [$this, 'update_folders_uninstall_status']);
        // Undo Functionality
        add_action('wp_ajax_wcp_undo_folder_changes', [$this, 'wcp_undo_folder_changes']);
        self::$folders = 10;

        // Send message on plugin deactivate
        add_action('wp_ajax_folder_plugin_deactivate', [ $this, 'folder_plugin_deactivate' ]);
        // Update Parent Data
        add_action('wp_ajax_wcp_remove_post_folder', [$this, 'wcp_remove_post_folder']);
        // Change folder color
        add_action('wp_ajax_wcp_change_color_folder', [$this, 'wcp_change_color_folder']);
        // Send message on owner
        add_action('wp_ajax_wcp_folder_send_message_to_owner', [ $this, 'wcp_folder_send_message_to_owner' ]);
        // Get default list
        add_action('wp_ajax_premio_check_for_other_folders', [$this, 'premio_check_for_other_folders']);
        // Send message on owner
        add_action('wp_ajax_wcp_get_default_list', [ $this, 'wcp_get_default_list' ]);
        // Get default list
        add_action('wp_ajax_get_folders_default_list', [ $this, 'get_folders_default_list' ]);
        // Auto select folder for new page, post
        add_action('new_to_auto-draft', [$this, 'new_to_auto_draft'], 10);
        // for media
        add_action('restrict_manage_posts', [$this, 'output_list_table_filters'], 10, 2);
        add_filter('pre_get_posts', [$this, 'filter_attachments_list']);
        add_action('wp_enqueue_media', [$this, 'output_backbone_view_filters']);
        add_filter('ajax_query_attachments_args', [$this, 'filter_attachments_grid']);
        add_filter('add_attachment', [$this, 'save_media_terms']);

        // to filter un assigned items
        add_filter('pre_get_posts', [$this, 'filter_record_list']);
        add_filter('pre-upload-ui', [$this, 'show_dropdown_on_media_screen']);
        add_action('add_attachment', [$this, 'add_attachment_category']);

        $options = get_option("folders_settings");

        $options = is_array($options) ? $options : [];

        if (in_array("post", $options)) {
            add_filter('manage_posts_columns', [$this, 'wcp_manage_columns_head']);
            add_action('manage_posts_custom_column', [$this, 'wcp_manage_columns_content'], 10, 2);
            add_filter('bulk_actions-edit-post', [$this, 'custom_bulk_action' ]);
        }

        if (in_array("page", $options)) {
            add_filter('manage_page_posts_columns', [$this, 'wcp_manage_columns_head']);
            add_action('manage_page_posts_custom_column', [$this, 'wcp_manage_columns_content'], 10, 2);
            add_filter('bulk_actions-edit-page', [$this, 'custom_bulk_action' ]);
        }

        if (in_array("attachment", $options)) {
            add_filter('manage_media_columns', [$this, 'wcp_manage_columns_head']);
            add_action('manage_media_custom_column', [$this, 'wcp_manage_columns_content'], 10, 2);
            // add_filter('bulk_actions-edit-media', array($this, 'custom_bulk_action' ));
        }

        foreach ($options as $option) {
            if ($option != "post" && $option != "page" && $option != "attachment") {
                add_filter('manage_edit-'.$option.'_columns', [$this, 'wcp_manage_columns_head'], 99999);
                add_action('manage_'.$option.'_posts_custom_column', [$this, 'wcp_manage_columns_content'], 2, 2);
                add_filter('bulk_actions-edit-'.$option, [$this, 'custom_bulk_action' ]);
            }
        }

        // check for default folders
        add_filter('pre_get_posts', [$this, 'check_for_default_folders']);
        add_filter('folders_count_where_query', [$this, 'folders_count_where_query']);
        add_filter('folders_count_join_query', [$this, 'folders_count_join_query']);

        add_action("wp_ajax_folder_update_status", [$this, 'folder_update_status']);

        // load language files
        add_action('plugins_loaded', [ $this, 'folders_text' ]);

        add_action("wp_ajax_folder_update_popup_status", [$this, 'folder_update_popup_status']);

        add_action("wp_ajax_premio_hide_child_popup", [$this, 'premio_hide_child_popup']);

        add_action("wp_ajax_wcp_update_folders_import_status", [$this, 'update_folders_import_status']);

        add_filter('get_terms', [ $this, 'get_terms_filter_without_trash'], 10, 3);

        add_filter('mla_media_modal_query_final_terms', [ $this, 'media_modal_query_final_terms'], 10, 3);

        // reset count when post/page updated
        add_action('deleted_term_relationships', [$this, 'update_folder_term_relationships'], 10, 3);

        add_action('added_term_relationship', [$this, 'update_folder_new_term_relationships'], 10, 3);

        add_action('set_object_terms', [$this, 'set_object_terms_for_folders'], 10, 6);

        /*
         * To Remove Attachment
         * */

        add_action('wp_trash_post', [$this, "wcp_delete_post"]);
        add_action('before_delete_post', [$this, "wcp_delete_post"]);
        add_action('save_post', [$this, "save_post"], 10, 3);

        /*
         * Hide Folder CTA
         * */
        add_action('wp_ajax_hide_folders_cta', [$this, 'hide_folders_cta']);
        add_action('wp_ajax_hide_folder_color_pop_up', [$this, 'hide_folder_color_pop_up']);

        add_action("manage_posts_extra_tablenav", [$this, "manage_posts_extra_fields"]);

        /* Mailpoet Whitelist JS/CSS */
        add_filter("mailpoet_conflict_resolver_whitelist_style", [$this, 'mailpoet_conflict_whitelist_style']);
        add_filter("mailpoet_conflict_resolver_whitelist_script", [$this, 'mailpoet_conflict_whitelist_script']);

        add_action("admin_head", [$this, "admin_head"]);

        // Remove Media Library for Dokan for frontend users
        add_filter("check_media_status_for_folders", [$this, "check_media_status_for_folders"]);

        add_action("admin_init", [$this, "check_for_signup_status"]);
        add_action("admin_init", [$this, "change_menu_text"]);
        add_action( 'admin_init', [$this, 'process_request'], 1 );

    }//end __construct()

    public function process_request()
    {
        // Check for filter: Remove if filter by folders
        global $_REQUEST;
        $isMediaFilter = isset($_REQUEST['action']) && $_REQUEST['action'] == 'query-attachments';
        $hasMediaFilter = isset($_REQUEST['query']['media_folder']) && !empty($_REQUEST['query']['media_folder']);
        if($isMediaFilter && $hasMediaFilter && defined('MLA_PLUGIN_PATH')) {
            if(isset($_REQUEST['query']['s']) && is_array($_REQUEST['query']['s'])) {
                unset($_REQUEST['query']['s']);
            }
            remove_action( 'admin_init', 'MLAModal_Ajax::mla_admin_init_action' );
        }
    }

    public function change_menu_text()
    {
        global $submenu;
        if(isset($submenu['wcp_folders_settings'])) {
            $totalItems = count($submenu['wcp_folders_settings'])-1;
            if(isset($submenu['wcp_folders_settings'][$totalItems][0])) {
                $submenu['wcp_folders_settings'][$totalItems][0] = '<span><svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M13.0518 4.01946C12.9266 3.91499 12.7747 3.84781 12.6132 3.82557C12.4517 3.80333 12.2872 3.82693 12.1385 3.89367L9.3713 5.12414L7.76349 2.22571C7.68664 2.09039 7.5753 1.97785 7.44081 1.89956C7.30632 1.82127 7.15348 1.78003 6.99786 1.78003C6.84224 1.78003 6.6894 1.82127 6.55491 1.89956C6.42042 1.97785 6.30908 2.09039 6.23224 2.22571L4.62442 5.12414L1.85724 3.89367C1.70822 3.82703 1.54352 3.8034 1.38178 3.82545C1.22003 3.84751 1.06768 3.91437 0.941941 4.01849C0.816207 4.1226 0.722106 4.25982 0.670275 4.41461C0.618444 4.56941 0.610951 4.73562 0.648642 4.89446L2.0377 10.8171C2.06427 10.9318 2.11383 11.0399 2.18339 11.1348C2.25295 11.2297 2.34107 11.3096 2.44239 11.3695C2.57957 11.4516 2.73642 11.495 2.8963 11.4952C2.97402 11.4951 3.05133 11.484 3.12599 11.4624C5.65792 10.7624 8.33233 10.7624 10.8643 11.4624C11.0955 11.5232 11.3413 11.4898 11.5479 11.3695C11.6498 11.3103 11.7384 11.2307 11.8081 11.1357C11.8777 11.0406 11.9269 10.9321 11.9525 10.8171L13.3471 4.89446C13.3843 4.73558 13.3764 4.56945 13.3243 4.41482C13.2721 4.2602 13.1777 4.12326 13.0518 4.01946V4.01946Z" fill="white"/>
</svg></span> '.esc_html__( 'Upgrade to Pro' , 'folders');
            }
        }
    }

    function check_for_signup_status() {
        if(!defined("DOING_AJAX")) {
            $option = get_option("folder_redirect_status");
            if ($option == 1) {
                update_option("folder_redirect_status", 2);
                wp_redirect(admin_url("admin.php?page=wcp_folders_settings"));
                exit;
            }

            $page = filter_input(INPUT_GET, 'page');
            if ($page == "recommended-folder-plugins" || $page == "folders-upgrade-to-pro") {
                $is_shown = get_option("folder_update_message");
                if ($is_shown === false) {
                    wp_redirect(admin_url("admin.php?page=wcp_folders_settings"));
                    exit;
                }
            }
        }
    }


    function check_media_status_for_folders($status) {
        if(defined("DOKAN_INC_DIR") && !is_admin()) {
            return false;
        }
        return $status;
    }


    function admin_head() {
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
                #adminmenu .toplevel_page_wcp_folders_settings > ul > li:last-child a:hover, #adminmenu .toplevel_page_wcp_folders_settings > ul > li:last-child a.current {
                    box-shadow: 0px 6px 8px 0px #B78DEB3D;
                    color: #ffffff;
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
     * Add folders script to mailpoet's conflict list
     *
     * @since  2.8.7
     * @access public
     * @return $scripts
     */
    public function mailpoet_conflict_whitelist_script($scripts) {
        $scripts[] = "folders";
        $scripts[] = "folders-pro";
        return $scripts;
    }


    /**
     * Add folders styles to mailpoet's conflict list
     *
     * @since  2.8.7
     * @access public
     * @return $styles
     */
    public function mailpoet_conflict_whitelist_style($styles) {
        $styles[] = "folders";
        $styles[] = "folders-pro";
        return $styles;
    }

    /**
     * Update User Profile fields
     *
     * @since  2.8.5
     * @access public
     */
    function save_extra_user_profile_fields( $user_id ) {
        if ( empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'update-user_' . $user_id ) ) {
            return;
        }

        if (!current_user_can( 'edit_user', $user_id ) ) {
            return;
        }

        if(isset($_POST['folders_access_role'])) {
            $setting = get_user_meta($user_id, "folders_access_role", true);
            if($setting === false) {
                add_user_meta($user_id, "folders_access_role", sanitize_text_field($_POST['folders_access_role']));
            } else {
                update_user_meta($user_id, "folders_access_role", sanitize_text_field($_POST['folders_access_role']));
            }
        }
    }

    /**
     * Fetch User Profile fields
     *
     * @since  2.8.5
     * @access public
     */
    function extra_user_profile_fields($user) {
        $userRoles = $this->get_user_roles();
        $userRole = get_user_meta($user->ID, "folders_access_role", true);
        if($userRole === false || empty($userRole)) {
            $userRole = "default";
        }
        ?>
        <h3><?php esc_html_e("Folders", "folders"); ?></h3>

        <table class="form-table">
            <tr>
                <th><label for="folders_access_role"><?php esc_html_e("Folder Access"); ?></label></th>
                <td>
                    <select class="regular-text" id="folders_access_role" name="folders_access_role">
                        <?php foreach($userRoles as $key=>$role) { ?>
                            <option <?php selected($userRole, $key) ?> value="<?php echo esc_attr($key) ?>"><?php echo esc_attr($role) ?></option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
        </table>
    <?php }

    /**
     * Add extra params in search
     *
     * @since  1.0.0
     * @access public
     * @return $response
     */
    function manage_posts_extra_fields($which) {
        global $typenow;
        if($which == "top" && !empty($typenow)) {
            if ($this->folders_settings === false) {
                $this->folders_settings = get_option('folders_settings');
                $this->folders_settings = $this->folders_settings;
                $this->folders_settings = is_array($this->folders_settings) ? $this->folders_settings : [];
            }
            if(in_array($typenow, $this->folders_settings)) {
                $folder_type = self::get_custom_post_type($typenow);
                if(isset($_REQUEST[$folder_type]) && !empty($_REQUEST[$folder_type])) {
                    $folder = sanitize_text_field($_REQUEST[$folder_type]);
                    echo "<input type='hidden' name='".esc_attr($folder_type)."' value='".esc_attr($folder)."' />";
                } else if(isset($_REQUEST['ajax_action']) && $_REQUEST['ajax_action'] == "premio_dynamic_folders" && isset($_REQUEST['dynamic_folder'])) {
                    $dynamic_folder = sanitize_text_field($_REQUEST['dynamic_folder']);
                    echo "<input type='hidden' name='ajax_action' value='premio_dynamic_folders' />";
                    echo "<input type='hidden' name='dynamic_folder' value='".esc_attr($dynamic_folder)."' />";
                }
            }
        }
    }

    /**
     * Hide CTA button text
     *
     * @since  1.0.0
     * @access public
     * @return $response
     */
    public function hide_folder_color_pop_up()
    {
        $response            = [];
        $response['status']  = 0;
        $response['error']   = 0;
        $response['data']    = [];
        $response['message'] = "";
        $postData            = filter_input_array(INPUT_POST);
        $errorCounter        = 0;
        if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] = esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else {
            $nonce = self::sanitize_options($postData['nonce']);
            if (!wp_verify_nonce($nonce, 'hide_folder_color_pop_up')) {
                $response['message'] = esc_html__("Your request is not valid", 'folders');
                $errorCounter++;
            }
        }

        if ($errorCounter == 0) {
            $response['status'] = 1;
            add_option("hide_folder_color_pop_up", "yes");
        }

        echo wp_json_encode($response);
        die;

    }//end hide_folder_color_pop_up()

    /**
     * Hide CTA button text
     *
     * @since  1.0.0
     * @access public
     * @return $response
     */
    public function hide_folders_cta()
    {
        $response            = [];
        $response['status']  = 0;
        $response['error']   = 0;
        $response['data']    = [];
        $response['message'] = "";
        $postData            = filter_input_array(INPUT_POST);
        $errorCounter        = 0;
        if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] = esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else {
            $nonce = self::sanitize_options($postData['nonce']);
            if (!wp_verify_nonce($nonce, 'hide_folders_cta')) {
                $response['message'] = esc_html__("Your request is not valid", 'folders');
                $errorCounter++;
            }
        }

        if ($errorCounter == 0) {
            $response['status'] = 1;
            add_option("hide_folders_cta", "yes");
        }

        echo wp_json_encode($response);
        die;

    }//end hide_folders_cta()


    /**
     * Delete post hook, clear data on post delete
     *
     * @since  1.0.0
     * @access public
     * @return $status
     */
    public function wcp_delete_post($postID)
    {
        delete_transient("premio_folders_without_trash");

    }//end wcp_delete_post()


    /**
     * Remove cache when page, past added
     *
     * @since  1.0.0
     * @access public
     * @return $status
     */
    public function save_post($post_id, $post, $update)
    {
        delete_transient("premio_folders_without_trash");

    }//end save_post()


    /**
     * Set folders data for post/page ids
     *
     * @since  1.0.0
     * @access public
     * @return $post
     */
    public function set_object_terms_for_folders($object_id, $terms, $tt_ids, $taxonomy, $append, $old_tt_ids)
    {
        if (!empty($object_id)) {
            $trash_folders = $initial_trash_folders = get_transient("premio_folders_without_trash");
            if (!empty($tt_ids) && is_array($tt_ids)) {
                foreach ($tt_ids as $term_id) {
                    $term = get_term($term_id, $taxonomy);
                    if (isset($term->term_taxonomy_id) && isset($trash_folders[$term->term_taxonomy_id])) {
                        unset($trash_folders[$term->term_taxonomy_id]);
                    }
                }
            }

            if (!empty($old_tt_ids) && is_array($old_tt_ids)) {
                foreach ($old_tt_ids as $term_id) {
                    $term = get_term($term_id, $taxonomy);
                    if (isset($term->term_taxonomy_id) && isset($trash_folders[$term->term_taxonomy_id])) {
                        unset($trash_folders[$term->term_taxonomy_id]);
                    }
                }
            }

            if ($initial_trash_folders != $trash_folders) {
                delete_transient("premio_folders_without_trash");
                set_transient("premio_folders_without_trash", $trash_folders, (3 * DAY_IN_SECONDS));
            }
        }//end if

    }//end set_object_terms_for_folders()


    /**
     * Check for folders settings
     *
     * @since  1.0.0
     * @access public
     * @return $options
     */
    public static function check_for_setting($key, $setting, $default="")
    {
        if (self::$folderSettings === false) {
            $options = get_option("premio_folder_options");
            if ($options === false || !is_array($options)) {
                $options = [];
            }

            self::$folderSettings = $options;
        }

        if ($setting == "folders_settings") {
            if (isset(self::$folderSettings[$setting]) && is_array(self::$folderSettings[$setting])) {
                return in_array($key, self::$folderSettings[$setting]);
            }
        } else {
            if (isset(self::$folderSettings[$setting][$key])) {
                return self::$folderSettings[$setting][$key];
            }
        }

        return false;

    }//end check_for_setting()


    /**
     * Send message to owner on Uninstall
     *
     * @since  1.0.0
     * @access public
     * @return $response
     */
    public function update_folders_uninstall_status()
    {
        $response            = [];
        $response['status']  = 0;
        $response['error']   = 0;
        $response['data']    = [];
        $response['message'] = "";
        $postData            = filter_input_array(INPUT_POST);
        $errorCounter        = 0;

        if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] = esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else {
            $nonce = self::sanitize_options($postData['nonce']);
            if (!wp_verify_nonce($nonce, 'wcp_folders_uninstall_status')) {
                $response['message'] = esc_html__("Your request is not valid", 'folders');
                $errorCounter++;
            }
        }

        if ($errorCounter == 0) {
            $status            = isset($postData['status']) ? $postData['status'] : "";
            $status            = ($status == "on") ? "on" : "off";
            $customize_folders = get_option('customize_folders');
            $customize_folders['remove_folders_when_removed'] = $status;
            update_option("customize_folders", $customize_folders);
            $response['status'] = 1;
        }

        echo wp_json_encode($response);
        die;

    }//end update_folders_uninstall_status()


    /**
     * Remove folders data
     *
     * @since  1.0.0
     * @access public
     * @return $response
     */
    public function remove_all_folders_data()
    {
        $response            = [];
        $response['status']  = 0;
        $response['error']   = 0;
        $response['data']    = [];
        $response['message'] = "";
        $postData            = filter_input_array(INPUT_POST);
        $errorCounter        = 0;

        if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] = esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else {
            $type  = self::sanitize_options($postData['type']);
            $nonce = self::sanitize_options($postData['nonce']);
            if (!wp_verify_nonce($nonce, 'remove_folders_data')) {
                $response['message'] = esc_html__("Your request is not valid", 'folders');
                $errorCounter++;
            }
        }

        if ($errorCounter == 0) {
            self::$folders = 0;
            self::remove_folder_by_taxonomy("media_folder");
            self::remove_folder_by_taxonomy("folder");
            self::remove_folder_by_taxonomy("post_folder");
            $post_types = get_post_types([], 'objects');
            $post_array = [
                "page",
                "post",
                "attachment",
            ];
            foreach ($post_types as $post_type) {
                if (!in_array($post_type->name, $post_array)) {
                    self::remove_folder_by_taxonomy($post_type->name.'_folder');
                }
            }

            delete_option('default_folders');
            $response['status'] = 1;
            $response['data']   = [
                'items' => self::$folders,
            ];
        }//end if

        echo wp_json_encode($response);
        die;

    }//end remove_all_folders_data()


    /**
     * Remove folders from poge/post
     *
     * @since  1.0.0
     * @access public
     * @return $folders
     */
    public static function remove_folder_by_taxonomy($taxonomy)
    {
        global $wpdb;
        $query   = "SELECT * FROM ".$wpdb->term_taxonomy."
					LEFT JOIN  ".$wpdb->terms."
					ON  ".$wpdb->term_taxonomy.".term_id =  ".$wpdb->terms.".term_id
					WHERE ".$wpdb->term_taxonomy.".taxonomy = '%d'
					ORDER BY parent ASC";
        $query   = $wpdb->prepare($query, $taxonomy);
        $folders = $wpdb->get_results($query);
        $folders = array_values($folders);
        foreach ($folders as $folder) {
            $term_id = intval($folder->term_id);
            if ($term_id) {
                $wpdb->delete($wpdb->prefix.'term_relationships', ['term_taxonomy_id' => $term_id]);
                $wpdb->delete($wpdb->prefix.'term_taxonomy', ['term_id' => $term_id]);
                $wpdb->delete($wpdb->prefix.'terms', ['term_id' => $term_id]);
                $wpdb->delete($wpdb->prefix.'termmeta', ['term_id' => $term_id]);
                self::$folders++;
            }
        }

    }//end remove_folder_by_taxonomy()


    /**
     * Convent Hex color code to RGB code
     *
     * @since  1.0.0
     * @access public
     * @return $rgb
     */
    public static function hexToRgb($hex, $alpha=false)
    {
        $hex      = str_replace('#', '', $hex);
        $length   = strlen($hex);
        $rgb['r'] = hexdec($length == 6 ? substr($hex, 0, 2) : ($length == 3 ? str_repeat(substr($hex, 0, 1), 2) : 0));
        $rgb['g'] = hexdec($length == 6 ? substr($hex, 2, 2) : ($length == 3 ? str_repeat(substr($hex, 1, 1), 2) : 0));
        $rgb['b'] = hexdec($length == 6 ? substr($hex, 4, 2) : ($length == 3 ? str_repeat(substr($hex, 2, 1), 2) : 0));
        if ($alpha) {
            $rgb['a'] = $alpha;
        }

        return $rgb;

    }//end hexToRgb()


    /**
     * Get folders by folder order
     *
     * @since  1.0.0
     * @access public
     * @return $folders
     */
    public function wcp_folders_by_order()
    {
        $response            = [];
        $response['status']  = 0;
        $response['error']   = 0;
        $response['data']    = [];
        $response['message'] = "";
        $postData            = filter_input_array(INPUT_POST);
        $errorCounter        = 0;

        if (!isset($postData['order']) || empty($postData['order'])) {
            $response['message'] = esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!isset($postData['type']) || empty($postData['type'])) {
            $response['message'] = esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] = esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if ($postData['type'] == "page" && !current_user_can("edit_pages")) {
            $response['message'] = esc_html__("You have not permission to update width", 'folders');
            $errorCounter++;
        } else if ($postData['type'] != "page" && !current_user_can("edit_posts")) {
            $response['message'] = esc_html__("You have not permission to update width", 'folders');
            $errorCounter++;
        } else {
            $type  = self::sanitize_options($postData['type']);
            $nonce = self::sanitize_options($postData['nonce']);
            if (!wp_verify_nonce($nonce, 'wcp_folder_nonce_'.$type)) {
                $response['message'] = esc_html__("Your request is not valid", 'folders');
                $errorCounter++;
            }
        }//end if

        if ($errorCounter == 0) {
            $response['status'] = 1;

            $order_field = $postData['order'];

            $order_by = "";
            $order    = "ASC";

            if ($order_field == "a-z" || $order_field == "z-a") {
                $order_by = 'title';
                if ($order_field == "z-a") {
                    $order = "DESC";
                }
            } else if ($order_field == "n-o" || $order_field == "o-n") {
                $order_by = 'ID';
                if ($order_field == "o-n") {
                    $order = "ASC";
                } else {
                    $order = "DESC";
                }
            }

            if (empty($order_by)) {
                $order = "";
            }

            $folder_type = self::get_custom_post_type($postData['type']);
            // Do not change: Free/Pro Class name change
            $tree_data = WCP_Tree::get_full_tree_data($folder_type, $order_by, $order);

            $response['data'] = $tree_data['string'];
            $taxonomies       = [];
            if ($postData['type'] == "attachment") {
                $taxonomies = self::get_terms_hierarchical($folder_type);
            }

            $response['terms'] = $taxonomies;
        }//end if

        echo wp_json_encode($response);
        die;

    }//end wcp_folders_by_order()


    /**
     * Save selected post for page/post/media
     *
     * @since  1.0.0
     * @access public
     * @return $folders
     */
    public function save_folder_last_status()
    {
        $postData = filter_input_array(INPUT_POST);
        $error    = 0;
        if (!isset($postData['post_id']) || empty($postData['post_id'])) {
            $response['message'] = esc_html__("Your request is not valid", 'folders');
            $error = 1;
        } else if (!isset($postData['post_type']) || empty($postData['post_type'])) {
            $response['message'] = esc_html__("Your request is not valid", 'folders');
            $error = 1;
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] = esc_html__("Your request is not valid", 'folders');
            $error = 1;
        } else if ($postData['post_type'] == "page" && !current_user_can("edit_pages")) {
            $response['message'] = esc_html__("You have not permission to update width", 'folders');
            $error = 1;
        } else if ($postData['post_type'] != "page" && !current_user_can("edit_posts")) {
            $response['message'] = esc_html__("You have not permission to update width", 'folders');
            $error = 1;
        }

        if ($error == 0) {
            $post_type = filter_input(INPUT_POST, 'post_type');
            $post_id   = filter_input(INPUT_POST, 'post_id');
            if (!empty($post_type) && !empty($post_id)) {
                delete_option("last_folder_status_for".$post_type);
                add_option("last_folder_status_for".$post_type, $post_id);
            }
        }

    }//end save_folder_last_status()


    /**
     * Save selected post for page/post/media
     *
     * @since  1.0.0
     * @access public
     * @return $folders
     */
    public function media_modal_query_final_terms($request)
    {
        $action = $this->getRequestVar("action");
        if ($action == "mla-query-attachments") {
            $query = $this->getRequestVar("query");
            if (isset($query['media_folder']) && !empty($query['media_folder'])) {
                if ($query['media_folder'] == -1) {
                    $tax_query            = [
                        'taxonomy' => 'media_folder',
                        'operator' => 'NOT EXISTS',
                    ];
                    $request['tax_query'] = [$tax_query];
                    $request = apply_filters('media_library_organizer_media_filter_attachments', $request, $_REQUEST);
                } else {
                    $request['media_folder'] = $query['media_folder'];
                }
            }
        }

        return $request;

    }//end media_modal_query_final_terms()


    /**
     * Filter folders without trash count
     *
     * @since  1.0.0
     * @access public
     */
    public function get_terms_filter_without_trash($terms, $taxonomies, $args) {
        $isForFolders = 0;
        if(!empty($taxonomies) && is_array($taxonomies) && count($taxonomies)){
            foreach ($taxonomies as $taxonomy) {
                if (in_array($taxonomy, array("media_folder", "folder", "post_folder"))) {
                    $isForFolders = 1;
                } else {
                    $folder = substr($taxonomy, -7);
                    if ($folder == "_folder") {
                        $isForFolders = 1;
                    }
                }
            }
        }

        if($isForFolders) {
            global $wpdb;
            if (!is_array($terms) && count($terms) < 1) {
                return $terms;
            }

            $trash_folders = $initial_trash_folders = get_transient("premio_folders_without_trash");

            if ($trash_folders === false) {
                $trash_folders = array();
                $initial_trash_folders = array();
            }

            $post_table = $wpdb->prefix . "posts";
            $term_table = $wpdb->prefix . "term_relationships";
            $options = get_option('folders_settings');
            $option_array = array();
            if (!empty($options)) {
                foreach ($options as $option) {
                    $option_array[] = self::get_custom_post_type($option);
                }
            }
            foreach ($terms as $key => $term) {
                if (isset($term->term_id) && isset($term->taxonomy) && !empty($term->taxonomy) && in_array($term->taxonomy, $option_array)) {
                    $trash_count = null;
                    if (has_filter("premio_folder_item_in_taxonomy")) {
                        $post_type = "";
                        $taxonomy = $term->taxonomy;

                        if ($taxonomy == "post_folder") {
                            $post_type = "post";
                        } else if ($taxonomy == "folder") {
                            $post_type = "page";
                        } else if ($taxonomy == "media_folder") {
                            $post_type = "attachment";
                        } else {
                            $post_type = trim($taxonomy, "'_folder'");
                        }
                        $arg = array(
                            'post_type' => $post_type,
                            'taxonomy' => $taxonomy,
                        );
                        $trash_count = apply_filters("premio_folder_item_in_taxonomy", $term->term_id, $arg);
                    } else {
                        if ($trash_count == null && isset($trash_folders[$term->term_taxonomy_id])) {
                            $trash_count = $trash_folders[$term->term_taxonomy_id];
                        } else if ($trash_count == null) {

                            if ($trash_count === null) {
                                $query = "SELECT COUNT(DISTINCT(p.ID)) 
                                    FROM {$post_table} p 
                                        JOIN {$term_table} rl ON p.ID = rl.object_id 
                                        WHERE rl.term_taxonomy_id = '{$term->term_taxonomy_id}' 
                                          AND p.post_status != 'trash' 
                                          AND p.post_status != 'auto-draft' 
                                        LIMIT 1";
                                $result = $wpdb->get_var($query);
                                if (intval($result) > 0) {
                                    $trash_count = intval($result);
                                } else {
                                    $trash_count = 0;
                                }
                            }
                        }
                    }
                    if ($trash_count === null) {
                        $trash_count = 0;
                    }
                    $terms[$key]->trash_count = $trash_count;
                    $trash_folders[$term->term_taxonomy_id] = $trash_count;
                }
            }

            if (!empty($terms) && $initial_trash_folders != $trash_folders) {
                delete_transient("premio_folders_without_trash");
                set_transient("premio_folders_without_trash", $trash_folders, 3 * DAY_IN_SECONDS);
            }
        }
        return $terms;
    }//end get_terms_filter_without_trash()


    /**
     * Add Checkbox to page/post lists
     *
     * @since  1.0.0
     * @access public
     * @return $folders
     */
    public function custom_bulk_action($bulk_actions)
    {
        $bulk_actions['move_to_folder'] = __('Move to Folder', 'email_to_eric');
        return $bulk_actions;

    }//end custom_bulk_action()


    /**
     * Folders domain for language translation
     *
     * @since  1.0.0
     * @access public
     * @return $folders
     */
    public function folders_text()
    {
        load_plugin_textdomain("folders", false, dirname(dirname(plugin_basename(__FILE__))).'/languages/');

    }//end folders_text()


    /**
     * Add custom CSS to footer for media button
     *
     * @since  1.0.0
     * @access public
     * @return $folders
     */
    public function admin_footer_for_media()
    {
        $customize_folders = get_option('customize_folders');
        if (isset($customize_folders['dropdown_color']) && !empty($customize_folders['dropdown_color'])) {
            ?>
            <style>
            #media-attachment-taxonomy-filter, .post-upload-ui .folder_for_media, select.media-select-folder {
                border-color: <?php echo esc_attr($customize_folders['dropdown_color']) ?>;
                color: <?php echo esc_attr($customize_folders['dropdown_color']) ?>
            }
            .folder_for_media option {
                color:#000000;
            }
            .folder_for_media option:first-child {
                font-weight: bold;
            }
            </style>
            <?php
        }

    }//end admin_footer_for_media()


    /**
     * Check for default page/post/media folder on load
     *
     * @since  1.0.0
     * @access public
     * @return $folders
     */
    public function check_for_default_folders()
    {
        global $typenow, $current_screen;
        $isAjax      = (defined('DOING_AJAX') && DOING_AJAX) ? 1 : 0;
        $options     = get_option('folders_settings');
        $options     = (empty($options) || !is_array($options)) ? [] : $options;
        $post_status = filter_input(INPUT_GET, 'post_status');
        $last_status = get_option("last_folder_status_for".$typenow);
        if (empty($post_status) && !$isAjax && (in_array($typenow, $options) || !empty($last_status)) && (isset($current_screen->base) && ($current_screen->base == "edit" || ($current_screen->base == "upload")))) {
            $requests = filter_input_array(INPUT_GET);
            $requests = empty($requests)||!is_array($requests) ? [] : $requests;

            if ($typenow == "attachment") {
                if (count($requests) > 0) {
                    return;
                }
            } else if ($typenow == "post") {
                if (count($requests) > 0) {
                    return;
                }
            } else {
                if (count($requests) > 1) {
                    return;
                }
            }

            if (!empty($last_status)) {
                $status = 1;
                if ($last_status != "-1" && $last_status != "all") {
                    $type = self::get_custom_post_type($typenow);
                    $term = get_term_by('slug', $last_status, $type);
                    if (empty($term) || !is_object($term)) {
                        $status = 0;
                    }
                }

                delete_option("last_folder_status_for".$typenow);
                if ($last_status == "all") {
                    $last_status = "";
                }

                if ($status) {
                    if ($typenow == "attachment") {
                        if (!isset($_REQUEST['media_folder'])) {
                            ?>
                            <script>
                                window.location = '<?php echo esc_url(admin_url())."upload.php?post_type=attachment&media_folder=".esc_attr($last_status) ?>';
                            </script>
                            <?php
                            exit;
                        }
                    } else {
                        $post_type = self::get_custom_post_type($typenow);
                        if (!isset($_REQUEST[$post_type])) {
                            ?>
                            <script>
                                window.location = '<?php echo esc_url(admin_url())."edit.php?post_type=".esc_attr($typenow)."&".esc_attr($post_type)."=".esc_attr($last_status) ?>';
                            </script>
                            <?php
                            exit;
                        }
                    }//end if
                }//end if
            }//end if

            $default_folders = get_option('default_folders');
            $default_folders = (empty($default_folders) || !is_array($default_folders)) ? [] : $default_folders;

            $status = 1;
            if (isset($default_folders[$typenow]) && !empty($default_folders[$typenow])) {
                $type = self::get_custom_post_type($typenow);
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
                    if (!isset($_REQUEST['media_folder'])) {
                        if (isset($default_folders[$typenow]) && !empty($default_folders[$typenow])) {
                            ?>
                            <script>
                                window.location = '<?php echo esc_url(admin_url())."upload.php?post_type=attachment&media_folder=".esc_attr($default_folders[$typenow]) ?>';
                            </script>
                            <?php
                            exit;
                        }
                    }
                } else {
                    $search = filter_input(INPUT_GET, "s");
                    if (!empty($search)) {
                        $search = esc_attr($search);
                    } else {
                        $search = "";
                    }

                    $post_type = self::get_custom_post_type($typenow);
                    if (!isset($_REQUEST[$post_type])) {
                        if (isset($default_folders[$typenow]) && !empty($default_folders[$typenow])) {
                            ?>
                            <script>
                                window.location = '<?php echo esc_url(admin_url())."edit.php?post_type=".esc_attr($typenow)."&".esc_attr($post_type)."=".esc_attr($default_folders[$typenow])."&s=".esc_attr($search) ?>';
                            </script>
                            <?php
                            exit;
                        }
                    }
                }//end if
            }//end if
        }//end if

    }//end check_for_default_folders()


    /**
     * Signup for folders news
     *
     * @since  1.0.0
     * @access public
     * @return $folders
     */
    public function folder_update_status()
    {
        $response           = [];
        $response['status'] = 0;
        $nonce = filter_input(INPUT_POST, 'nonce');
        if (!empty($nonce) && wp_verify_nonce($nonce, 'folder_update_status')) {
            $status = filter_input(INPUT_POST, 'status');
            $email  = filter_input(INPUT_POST, 'email');
            update_option("folder_update_message", 2);
            if ($status == 1) {
                $url = 'https://premioapps.com/premio/signup/email.php';
                $apiParams = [
                    'plugin' => 'folders',
                    'email'  => $email,
                ];

                // Signup Email for Folders
                $apiResponse = wp_safe_remote_post($url, ['body' => $apiParams, 'timeout' => 15, 'sslverify' => true]);

                if (is_wp_error($apiResponse)) {
                    wp_safe_remote_post($url, ['body' => $apiParams, 'timeout' => 15, 'sslverify' => false]);
                }

                $response['status'] = 1;
            }
        }//end if

        echo wp_json_encode($response);
        die;

    }//end folder_update_status()


    /**
     * Add category to media
     *
     * @since  1.0.0
     * @access public
     * @return $folders
     */
    public function add_attachment_category($post_ID)
    {
        if (self::is_for_this_post_type('attachment') || self::is_for_this_post_type('media')) {
            $folder_id = null;
            if (isset($_REQUEST["folder_for_media"])) {
                $folder_id = $this->getRequestVar("folder_for_media");
            }

            if ($folder_id !== null) {
                $folder_id = (int) $folder_id;
                $folder_id = self::sanitize_options($folder_id, "int");
                if ($folder_id > 0) {
                    $post_type = self::get_custom_post_type("attachment");
                    $term      = get_term($folder_id);
                    if (!empty($term) && isset($term->slug)) {
                        wp_set_object_terms($post_ID, $term->slug, $post_type);

                        $trash_folders = $initial_trash_folders = get_transient("premio_folders_without_trash");
                        if ($trash_folders === false) {
                            $trash_folders         = [];
                            $initial_trash_folders = [];
                        }

                        if (isset($term->term_taxonomy_id) && isset($trash_folders[$term->term_taxonomy_id])) {
                            unset($trash_folders[$term->term_taxonomy_id]);
                        }

                        if ($initial_trash_folders != $trash_folders) {
                            delete_transient("premio_folders_without_trash");
                            set_transient("premio_folders_without_trash", $trash_folders, (3 * DAY_IN_SECONDS));
                        }
                    }
                }//end if
            }//end if
        }//end if

    }//end add_attachment_category()


    /**
     * Get Dropdown data for media popup
     *
     * @since  1.0.0
     * @access public
     * @return $folders
     */
    public function show_dropdown_on_media_screen()
    {
        if (self::is_for_this_post_type('attachment')) {
            $post_type = self::get_custom_post_type('attachment');
            global $typenow, $current_screen;
            // Free/Pro Class name change
            if (!class_exists('WCP_Tree')) {
                $files = [
                    'WCP_Tree' => WCP_DS."includes".WCP_DS."tree.class.php",
                ];

                foreach ($files as $file) {
                    if (file_exists(dirname(dirname(__FILE__)).$file)) {
                        include_once dirname(dirname(__FILE__)).$file;
                    }
                }
            }

            // Free/Pro Class name change
            $options = WCP_Tree::get_folder_option_data($post_type);?>
            <p class="attachments-category"><?php esc_html_e("Select a folder (Optional)", 'folders'); ?></p>
            <p class="attachments-category"><?php esc_html_e("First select the folder, and then upload the files", 'folders'); ?><br/></p>
            <p>
                <?php
                $request = sanitize_text_field($_SERVER['REQUEST_URI']);
                $request = strpos($request, "post.php");
                ?>
                <select name="folder_for_media" class="folder_for_media">
                    <option value="-1">- <?php esc_html_e('Unassigned', 'folders'); ?></option>
                    <?php echo ($options) ?>
                    <?php if (($typenow == "attachment" && isset($current_screen->base) && $current_screen->base == "upload") || ($request !== false) || self::is_for_this_post_type('attachment') || self::is_for_this_post_type('media')) {?>
                        <option value="add-folder"><?php esc_html_e('+ Create a New Folder', 'folders'); ?></option>
                    <?php } ?>
                </select>
            </p>
            <?php
        }//end if

    }//end show_dropdown_on_media_screen()


    /**
     * Hide folders
     *
     * @since  1.0.0
     * @access public
     * @return $folders
     */
    public function wcp_hide_folders()
    {
        $response            = [];
        $response['status']  = 0;
        $response['error']   = 0;
        $response['data']    = [];
        $response['message'] = "";
        $postData            = filter_input_array(INPUT_POST);
        $errorCounter        = 0;

        if (!isset($postData['status']) || empty($postData['status'])) {
            $response['message'] = esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!isset($postData['type']) || empty($postData['type'])) {
            $response['message'] = esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] = esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if ($postData['type'] == "page" && !current_user_can("edit_pages")) {
            $response['message'] = esc_html__("You have not permission to update width", 'folders');
            $errorCounter++;
        } else if ($postData['type'] != "page" && !current_user_can("edit_posts")) {
            $response['message'] = esc_html__("You have not permission to update width", 'folders');
            $errorCounter++;
        } else {
            $type  = self::sanitize_options($postData['type']);
            $nonce = self::sanitize_options($postData['nonce']);
            if (!wp_verify_nonce($nonce, 'wcp_folder_nonce_'.$type)) {
                $response['message'] = esc_html__("Your request is not valid", 'folders');
                $errorCounter++;
            }
        }//end if

        if ($errorCounter == 0) {
            $type       = self::sanitize_options($postData['type']);
            $status     = self::sanitize_options($postData['status']);
            $optionName = "wcp_folder_display_status_".$type;
            update_option($optionName, $status);
            $response['status'] = 1;
        }

        echo wp_json_encode($response);
        wp_die();

    }//end wcp_hide_folders()


    /**
     * Change status for folders
     *
     * @since  1.0.0
     * @access public
     * @return $folders
     */
    public function wcp_change_folder_display_status()
    {
        $response            = [];
        $response['status']  = 0;
        $response['error']   = 0;
        $response['data']    = [];
        $response['message'] = "";
        $postData            = filter_input_array(INPUT_POST);
        $errorCounter        = 0;
        if (!isset($postData['status']) || empty($postData['status'])) {
            $response['message'] = esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!isset($postData['type']) || empty($postData['type'])) {
            $response['message'] = esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] = esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if ($postData['type'] == "page" && !current_user_can("edit_pages")) {
            $response['message'] = esc_html__("You have not permission to update width", 'folders');
            $errorCounter++;
        } else if ($postData['type'] != "page" && !current_user_can("edit_posts")) {
            $response['message'] = esc_html__("You have not permission to update width", 'folders');
            $errorCounter++;
        } else {
            $type  = self::sanitize_options($postData['type']);
            $nonce = self::sanitize_options($postData['nonce']);
            if (!wp_verify_nonce($nonce, 'wcp_folder_nonce_'.$type)) {
                $response['message'] = esc_html__("Your request is not valid", 'folders');
                $errorCounter++;
            }
        }//end if

        if ($errorCounter == 0) {
            $type       = self::sanitize_options($postData['type']);
            $width      = self::sanitize_options($postData['status']);
            $optionName = "wcp_dynamic_display_status_".$type;
            update_option($optionName, $width);
            $response['status'] = 1;
        }

        echo wp_json_encode($response);
        wp_die();

    }//end wcp_change_folder_display_status()


    /**
     * Check for folders for page/post/media id
     *
     * @since  1.0.0
     * @access public
     * @return $folders
     */
    public function premio_check_for_other_folders()
    {
        $response            = [];
        $response['status']  = 0;
        $response['error']   = 0;
        $response['data']    = [];
        $response['message'] = "";
        $postData            = filter_input_array(INPUT_POST);
        $errorCounter        = 0;
        if (!isset($postData['post_id']) || empty($postData['post_id'])) {
            $response['message'] = esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!isset($postData['type']) || empty($postData['type'])) {
            $response['message'] = esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] = esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!wp_verify_nonce($postData['nonce'], 'wcp_folder_nonce_'.$postData['type'])) {
            $response['message'] = esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!current_user_can("manage_categories")) {
            $response['message'] = esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        }

        if ($errorCounter == 0) {
            $folderUndoSettings = [];
            $type    = self::sanitize_options($postData['type']);
            $post_id = self::sanitize_options($postData['post_id']);

            $post_id = explode(",", $post_id);

            $taxonomy = self::get_custom_post_type($type);

            foreach ($post_id as $id) {
                $terms = get_the_terms($id, $taxonomy);
                if (!empty($terms) && is_array($terms)) {
                    foreach ($terms as $term) {
                        if ($term->term_id != $postData['taxonomy']) {
                               $response['status']          = -1;
                               $response['data']['post_id'] = $postData['post_id'];
                               echo wp_json_encode($response);
                               wp_die();
                        }
                    }
                }
            }

            $this->wcp_remove_post_folder();
        }//end if

        echo wp_json_encode($response);
        wp_die();

    }//end premio_check_for_other_folders()

    public function wcp_change_color_folder()
    {
        $response            = [];
        $response['status']  = 0;
        $response['error']   = 0;
        $response['data']    = [];
        $response['message'] = "";
        $response['id']      = [];

        $postData            = filter_input_array(INPUT_POST);
        $errorCounter        = 0;
        if (!current_user_can("manage_categories")) {
            $response['message'] = esc_html__("You have not permission to update folder", 'folders');
            $errorCounter++;
        } else if (!isset($postData['term_id']) || empty($postData['term_id'])) {
            $errorCounter++;
            $response['message'] = esc_html__("Your request is not valid", 'folders');
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] = esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else {
            $term_id = self::sanitize_options($postData['term_id']);
            if (!wp_verify_nonce($postData['nonce'], 'wcp_folder_term_'.$term_id)) {
                $response['message'] = esc_html__("Your request is not valid", 'folders');
                $errorCounter++;
            }
        }

        if ($errorCounter == 0) {
            $term_id = self::sanitize_options($postData['term_id']);
            $getColor = self::sanitize_options($postData['color']);

            $folder_info    = get_term_meta($term_id, "folder_info", true);

            if ($folder_info) {
                $folder_info['has_color'] = $getColor;
                update_term_meta($term_id, "folder_info", $folder_info);
            } else {
                $folder_info = [];
                $folder_info['has_color'] = $getColor;
                add_term_meta($term_id, "folder_info", $folder_info);
            }
            $response['id'] = $term_id;
            $response['status'] = 1;
        }

        echo wp_json_encode($response);
        wp_die();

    }//end wcp_change_color_folder()

    /**
     * Remove Folder
     *
     * @since  1.0.0
     * @access public
     * @return $folders
     */
    public function wcp_remove_post_folder()
    {
        $response            = [];
        $response['status']  = 0;
        $response['error']   = 0;
        $response['data']    = [];
        $response['message'] = "";
        $postData            = filter_input_array(INPUT_POST);
        $errorCounter        = 0;
        if (!isset($postData['post_id']) || empty($postData['post_id'])) {
            $response['message'] = esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!isset($postData['type']) || empty($postData['type'])) {
            $response['message'] = esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] = esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!wp_verify_nonce($postData['nonce'], 'wcp_folder_nonce_'.$postData['type'])) {
            $response['message'] = esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!current_user_can("manage_categories")) {
            $response['message'] = esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        }

        if ($errorCounter == 0) {
            $folderUndoSettings = [];
            $type    = self::sanitize_options($postData['type']);
            $post_id = self::sanitize_options($postData['post_id']);

            $post_id = explode(",", $post_id);

            $taxonomy = self::get_custom_post_type($type);

            $trash_folders = $initial_trash_folders = get_transient("premio_folders_without_trash");
            if ($trash_folders === false) {
                $trash_folders         = [];
                $initial_trash_folders = [];
            }

            foreach ($post_id as $id) {
                if (!empty($id) && is_numeric($id) && $id > 0) {
                    $terms      = get_the_terms($id, $taxonomy);
                    $post_terms = [
                        'post_id' => $id,
                        'terms'   => $terms,
                    ];
                    if (!empty($terms) && count($terms) > 0) {
                        foreach ($terms as $term) {
                            if (isset($term->term_taxonomy_id) && isset($trash_folders[$term->term_taxonomy_id])) {
                                unset($trash_folders[$term->term_taxonomy_id]);
                            }
                        }
                    }

                    $folderUndoSettings[] = $post_terms;
                    if (isset($postData['remove_from']) && $postData['remove_from'] == "current" && isset($postData['remove_from']) && $postData['remove_from'] == "current" && isset($postData['active_folder']) && is_numeric($postData['active_folder'])) {
                        wp_remove_object_terms($id, intval($postData['active_folder']), $taxonomy);
                    } else {
                        wp_delete_object_term_relationships($id, $taxonomy);
                    }
                }//end if
            }//end foreach

            delete_transient("folder_undo_settings");
            set_transient("folder_undo_settings", $folderUndoSettings, DAY_IN_SECONDS);

            if ($initial_trash_folders != $trash_folders) {
                delete_transient("premio_folders_without_trash");
                set_transient("premio_folders_without_trash", $trash_folders, (3 * DAY_IN_SECONDS));
            }

            $response['status'] = 1;
        }//end if

        echo wp_json_encode($response);
        wp_die();

    }//end wcp_remove_post_folder()


    /**
     * Filter records by selected categories
     *
     * @since  1.0.0
     * @access public
     * @return $folders
     */
    public function filter_record_list($query)
    {
        global $typenow;

        if ($typenow == "attachment") {
            return;
        }

        if (!self::is_for_this_post_type($typenow)) {
            return $query;
        }

        $taxonomy = self::get_custom_post_type($typenow);

        if (! isset($query->query['post_type'])) {
            return $query;
        }

        if (! isset($_REQUEST[$taxonomy])) {
            return $query;
        }

        $term = sanitize_text_field($_REQUEST[$taxonomy]);
        if ($term != "-1") {
            return $query;
        }

        unset($query->query_vars[$taxonomy]);

        $tax_query = [
            'taxonomy' => $taxonomy,
            'operator' => 'NOT EXISTS',
        ];

        $query->set('tax_query', [ $tax_query ]);
        $query->tax_query = new WP_Tax_Query([ $tax_query ]);

        return $query;

    }//end filter_record_list()


    /**
     * Get folders list
     *
     * @since  1.0.0
     * @access public
     * @return $folders
     */
    public function wcp_get_default_list()
    {

        $postData = filter_input_array(INPUT_POST);

        $post_type = $postData['type'];

        $ttpsts = $this->get_ttlpst($post_type);

        $empty_items = self::get_tempt_posts($post_type);

        $post_type = self::get_custom_post_type($post_type);

        $taxonomies = self::get_terms_hierarchical($post_type);

        $response = [
            'status'      => 1,
            'total_items' => $ttpsts,
            'taxonomies'  => $taxonomies,
            'empty_items' => $empty_items,
        ];
        echo wp_json_encode($response);
        wp_die();

    }//end wcp_get_default_list()


    /**
     * Get folders list
     *
     * @since  1.0.0
     * @access public
     * @return $folders
     */
    function get_folders_default_list()
    {
        $postData = filter_input_array(INPUT_POST);

        $post_type = $postData['type'];

        $ttpsts = $this->get_ttlpst($post_type);

        $empty_items = self::get_tempt_posts($post_type);

        $post_type = self::get_custom_post_type($post_type);

        $taxonomies = self::get_terms_hierarchical($post_type);

        $response = [
            'status'      => 1,
            'total_items' => $ttpsts,
            'empty_items' => $empty_items,
            'taxonomies'  => $taxonomies,
        ];
        echo wp_json_encode($response);
        die;

    }//end get_folders_default_list()


    /**
     * Save folders data for page/post id
     *
     * @since  1.0.0
     * @access public
     * @return $folders
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

        $post_type       = self::get_custom_post_type('attachment');
        $selected_folder = get_option("selected_".esc_attr($post_type)."_folder");
        if ($selected_folder != null && !empty($selected_folder)) {
            $terms = get_term($selected_folder);
            if (!empty($terms) && isset($terms->term_id)) {
                wp_set_post_terms($post_id, $terms->term_id, $post_type, false);
            }
        }

    }//end save_media_terms()


    /**
     * Filter attachment data by folder
     *
     * @since  1.0.0
     * @access public
     * @return $folders
     */
    public function filter_attachments_grid($args)
    {
        $taxonomy = 'media_folder';
        if (! isset($args[$taxonomy])) {
            return $args;
        }

        $term = sanitize_text_field($args[$taxonomy]);
        if ($term != "-1") {
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
     * Returns total folders
     *
     * @since  1.0.0
     * @access public
     */
    public function get_tempt_posts($post_type="")
    {
        global $wpdb;

        $post_table = $wpdb->prefix."posts";
        $term_table = $wpdb->prefix."term_relationships";
        $term_taxonomy_table = $wpdb->prefix."term_taxonomy";
        $term_meta = $wpdb->prefix."termmeta";
        $taxonomy = self::get_custom_post_type($post_type);
        $tlrcds = null;
        if(has_filter("premio_folder_un_categorized_items")) {
            $tlrcds = apply_filters("premio_folder_un_categorized_items", $post_type, $taxonomy);
        }
        if($tlrcds === null) {
            $user_filter = false;

            if(!$user_filter) {
                if ($post_type != "attachment") {
                    $query = "SELECT COUNT(DISTINCT({$post_table}.ID)) AS total_records FROM {$post_table} WHERE 1=1  AND (
                                NOT EXISTS (
                                    SELECT 1
                                    FROM {$term_table}
                                    INNER JOIN {$term_taxonomy_table}
                                    ON {$term_taxonomy_table}.term_taxonomy_id = {$term_table}.term_taxonomy_id
                                    WHERE {$term_taxonomy_table}.taxonomy = '%s'
                                    AND {$term_table}.object_id = {$post_table}.ID
                                )
                             ) AND {$post_table}.post_type = '%s' AND (({$post_table}.post_status = 'publish' OR {$post_table}.post_status = 'future' OR {$post_table}.post_status = 'draft' OR {$post_table}.post_status = 'private' OR {$post_table}.post_status = 'pending'))";
                    $query = $wpdb->prepare($query, $taxonomy, $post_type);
                } else {
                    $select = "SELECT COUNT(DISTINCT(P.ID)) AS total_records FROM {$post_table} AS P";
                    $where = ["post_type = 'attachment' "];
                    $where[] = "(post_status = 'inherit' OR post_status = 'private')";
                    $where[] = "(NOT EXISTS (
                                        SELECT 1
                                        FROM {$term_table}
                                        INNER JOIN {$term_taxonomy_table}
                                        ON {$term_taxonomy_table}.term_taxonomy_id = {$term_table}.term_taxonomy_id
                                        WHERE {$term_taxonomy_table}.taxonomy = '%s'
                                        AND {$term_table}.object_id = P.ID
                                    )
                                )";

                    $join = apply_filters( 'folders_count_join_query', "" );
                    $where = apply_filters( 'folders_count_where_query', $where );

                    $query = $select . $join . " WHERE ".implode( ' AND ', $where );

                    $query = $wpdb->prepare($query, $taxonomy);
                }
            } else {
                if ($post_type != "attachment") {
                    $query = "SELECT COUNT(DISTINCT({$post_table}.ID)) AS total_records FROM {$post_table} WHERE 1=1  AND (
                                NOT EXISTS (
                                    SELECT 1
                                    FROM {$term_table}
                                    INNER JOIN {$term_taxonomy_table}
                                    ON {$term_taxonomy_table}.term_taxonomy_id = {$term_table}.term_taxonomy_id
                                    INNER JOIN {$term_meta}
                                    ON {$term_meta}.term_id = {$term_table}.term_taxonomy_id 
                                    WHERE {$term_taxonomy_table}.taxonomy = '%s'
                                    AND {$term_table}.object_id = {$post_table}.ID
                                )
                             ) AND {$post_table}.post_type = '%s' AND (({$post_table}.post_status = 'publish' OR {$post_table}.post_status = 'future' OR {$post_table}.post_status = 'draft' OR {$post_table}.post_status = 'private' OR {$post_table}.post_status = 'pending'))";
                    $query = $wpdb->prepare($query, $taxonomy, $post_type);
                } else {
                    $select = "SELECT COUNT(DISTINCT(P.ID)) AS total_records FROM {$post_table} AS P";
                    $where = ["post_type = 'attachment' "];
                    $where[] = "(post_status = 'inherit' OR post_status = 'private')";
                    $where[] = "(
                                NOT EXISTS (
                                        SELECT 1
                                        FROM {$term_table}
                                        INNER JOIN {$term_taxonomy_table}
                                        ON {$term_taxonomy_table}.term_taxonomy_id = {$term_table}.term_taxonomy_id
                                        INNER JOIN {$term_meta}
                                        ON {$term_meta}.term_id = {$term_table}.term_taxonomy_id
                                        WHERE {$term_taxonomy_table}.taxonomy = '%s'
                                        AND {$term_table}.object_id = P.ID
                                    )
                                )";

                    $join = apply_filters( 'folders_count_join_query', "" );
                    $where = apply_filters( 'folders_count_where_query', $where );

                    $query = $select . $join . " WHERE ".implode( ' AND ', $where );

                    $query = $wpdb->prepare($query, $taxonomy);
                }
            }

            $tlrcds = $wpdb->get_var($query);
        }

        if(!empty($tlrcds)) {
            return $tlrcds;
        } else {
            return 0;
        }

    }//end get_tempt_posts()


    /**
     * Filter input data
     *
     * @since  1.0.0
     * @access public
     * @return $folders
     */
    public function getRequestVar($var)
    {
        $response = filter_input(INPUT_POST, $var);
        if (empty($response)) {
            $response = filter_input(INPUT_GET, $var);
        }

        return $response;

    }//end getRequestVar()


    /**
     * Redirect to default folder
     *
     * @since  1.0.0
     * @access public
     * @return $folders
     */
    public function output_backbone_view_filters()
    {

        global $typenow, $current_screen;
        $isAjax      = (defined('DOING_AJAX') && DOING_AJAX) ? 1 : 0;
        $options     = get_option('folders_settings');
        $options     = (empty($options) || !is_array($options)) ? [] : $options;
        $last_status = get_option("last_folder_status_for".$typenow);
        if (!$isAjax && (in_array($typenow, $options) || !empty($last_status)) && (isset($current_screen->base) && ($current_screen->base == "edit" || ($current_screen->base == "upload")))) {
            $default_folders = get_option('default_folders');
            $default_folders = (empty($default_folders) || !is_array($default_folders)) ? [] : $default_folders;

            if (!empty($last_status)) {
                $status = 1;
                if ($last_status != "-1" && $last_status != "all") {
                    $type = self::get_custom_post_type($typenow);
                    $term = get_term_by('slug', $last_status, $type);
                    if (empty($term) || !is_object($term)) {
                        $status = 0;
                    }
                }

                delete_option("last_folder_status_for".$typenow);
                if ($last_status == "all") {
                    $last_status = "";
                }

                if ($status) {
                    if ($typenow == "attachment") {
                        if (!isset($_REQUEST['media_folder'])) {
                            ?>
                            <script>
                                window.location = '<?php echo esc_url(admin_url("upload.php?post_type=attachment"))."&media_folder=".esc_attr($last_status) ?>';
                            </script>
                            <?php
                            exit;
                        }
                    }
                }
            }//end if

            $status = 1;
            if (isset($default_folders[$typenow]) && !empty($default_folders[$typenow])) {
                $type = self::get_custom_post_type($typenow);
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
                    if (!isset($_REQUEST['media_folder'])) {
                        if (isset($default_folders[$typenow]) && !empty($default_folders[$typenow])) {
                            ?>
                            <script>
                                window.location = '<?php echo esc_url(admin_url("upload.php?post_type=attachment"))."&media_folder=".esc_attr($default_folders[$typenow]) ?>';
                            </script>
                            <?php
                            exit;
                        }
                    }
                }
            }
        }//end if

        if (!(self::is_for_this_post_type('attachment') || self::is_for_this_post_type('media'))) {
            return;
        }

        if ($typenow == "attachment") {
            // Free/Pro URL Change
            global $typenow;
            $is_active = 1;
            $folders   = -1;

            $hasStars = self::check_for_setting("has_stars", "general");
            $hasChild = self::check_for_setting("has_child", "general");
            $hasChild = empty($hasChild) ? 0 : 1;
            $hasStars = empty($hasStars) ? 0 : 1;
            // Free/Pro URL Change
            wp_enqueue_script('folders-media', WCP_FOLDER_URL.'assets/js/media.js', [ 'media-editor', 'media-views' ], WCP_FOLDER_VERSION, true);
            wp_localize_script(
                'folders-media',
                'folders_media_options',
                [
                    'terms'         => self::get_terms_hierarchical('media_folder'),
                    'taxonomy'      => get_taxonomy('media_folder'),
                    'ajax_url'      => admin_url("admin-ajax.php"),
                    'activate_url'  => $this->getFoldersUpgradeURL(),
                    'nonce'         => wp_create_nonce('wcp_folder_nonce_attachment'),
                    'is_key_active' => $is_active,
                    'hasStars'      => $hasStars,
                    'hasChildren'   => $hasChild,
                    'lang'          => [
                        "pro_message"   => esc_html__("WordPress doesn't allow you to upload SVG files, upgrade to Folders Pro and experience added SVG file upload support!", "folders"),
                        "activate_key"  => esc_html__("Upgrade now!", "folders"),
                    ]
                ]
            );
            // Free/Pro URL Change
            wp_enqueue_style('folders-media', WCP_FOLDER_URL.'assets/css/media.css', [], WCP_FOLDER_VERSION);
        } else if (!self::is_active_for_screen() && self::is_for_this_post_type('attachment')) {

            $status = apply_filters("check_media_status_for_folders", true);
            if(!$status){
                return;
            }

            // Free/Pro URL Change
            global $current_screen;
            if (!isset($current_screen->base) || $current_screen->base != "plugins") {
                $is_active = 1;
                $folders   = -1;

                remove_filter("terms_clauses", "TO_apply_order_filter");

                // Free/Pro URL Change
                $is_rtl = 0;
                if (function_exists('is_rtl') && is_rtl()) {
                    $is_rtl = 1;
                }

                $can_manage_folder = current_user_can("manage_categories") ? 1 : 0;
                $width           = 275;
                $taxonomy_status = 0;
                $selected_taxonomy = "";
                $show_in_page      = false;
                $admin_url         = admin_url("upload.php?post_type=attachment&media_folder=");

                $taxonomies = self::get_terms_hierarchical('media_folder');

                $folder_settings = [];
                foreach ($taxonomies as $taxonomy) {
                    $folder_info    = get_term_meta($taxonomy->term_id, "folder_info", true);
                    $folder_info = shortcode_atts([
                        'is_sticky' => 0,
                        'is_high'   => 0,
                        'is_locked' => 0,
                        'is_active' => 0,
                        'has_color' => ''
                    ], $folder_info);

                    $folder_settings[] = [
                        'folder_id'    => $taxonomy->term_id,
                        'is_sticky'    => intval($folder_info['is_sticky']),
                        'is_locked'    => intval($folder_info['is_locked']),
                        'is_active'    => intval($folder_info['is_active']),
                        'is_high'      => intval($folder_info['is_high']),
                        'has_color'    => $folder_info['has_color'],
                        'nonce'        => wp_create_nonce('wcp_folder_term_'.$taxonomy->term_id),
                        'is_deleted'   => 0,
                        'slug'         => $taxonomy->slug,
                        'folder_count' => intval($taxonomy->trash_count),
                    ];
                }

                $hasStars = self::check_for_setting("has_stars", "general");
                $hasChild = self::check_for_setting("has_child", "general");
                $hasChild = empty($hasChild) ? 0 : 1;
                $hasStars = empty($hasStars) ? 0 : 1;

                $customize_folders = get_option('customize_folders');
                $customize_folders = (empty($customize_folders)||!is_array($customize_folders))?[]:$customize_folders;
                $use_folder_undo   = !isset($customize_folders['use_folder_undo']) ? "yes" : $customize_folders['use_folder_undo'];
                $defaultTimeout    = !isset($customize_folders['default_timeout']) ? 5 : intval($customize_folders['default_timeout']);
                if (empty($defaultTimeout) || !is_numeric($defaultTimeout) || $defaultTimeout < 0) {
                    $defaultTimeout = 5;
                }

                $defaultTimeout = ($defaultTimeout * 1000);

                $default_folders = get_option("default_folders");
                $default_folder  = "";
                if (isset($default_folders["attachment"])) {
                    $default_folder = $default_folders["attachment"];
                }

                $use_shortcuts = !isset($customize_folders['use_shortcuts']) ? "yes" : $customize_folders['use_shortcuts'];

                ob_start();
                include_once dirname(dirname(__FILE__)).WCP_DS."/templates".WCP_DS."admin".WCP_DS."modals.php";
                $form_content = ob_get_clean();

                $lang = $this->js_strings();
                wp_dequeue_script("jquery-jstree");
                // CMS Tree Page View Conflict
                wp_enqueue_script('folders-overlayscrollbars', WCP_FOLDER_URL.'assets/js/jquery.overlayscrollbars.min.js', [], WCP_FOLDER_VERSION, true);
                wp_enqueue_script('folders-tree', WCP_FOLDER_URL.'assets/js/jstree.min.js', [], WCP_FOLDER_VERSION, true);
                wp_enqueue_script('wcp-folders-media', WCP_FOLDER_URL.'assets/js/page-post-media.min.js', ['jquery', 'jquery-ui-resizable', 'jquery-ui-draggable', 'jquery-ui-droppable', 'jquery-ui-sortable', 'backbone'], WCP_FOLDER_VERSION, true);
                wp_enqueue_script('wcp-jquery-touch', plugin_dir_url(dirname(__FILE__)).'assets/js/jquery.ui.touch-punch.min.js', ['jquery'], WCP_FOLDER_VERSION, true);
                wp_localize_script(
                    'wcp-folders-media',
                    'folders_media_options',
                    [
                        'terms'             => $taxonomies,
                        'taxonomy'          => get_taxonomy('media_folder'),
                        'ajax_url'          => admin_url("admin-ajax.php"),
                        'media_page_url'    => admin_url("upload.php?media_folder="),
                        'activate_url'      => $this->getFoldersUpgradeURL(),
                        'nonce'             => wp_create_nonce('wcp_folder_nonce_attachment'),
                        'is_key_active'     => $is_active,
                        'folders'           => $folders,
                        'upgrade_url'       => $this->getFoldersUpgradeURL(),
                        'post_type'         => 'attachment',
                        'page_url'          => $admin_url,
                        'current_url'       => "",
                        'ajax_image'        => plugin_dir_url(dirname(__FILE__))."assets/images/ajax-loader.gif",
                        'register_url'      => "",
                        'isRTL'             => $is_rtl,
                        'can_manage_folder' => $can_manage_folder,
                        'folder_width'      => $width,
                        'taxonomy_status'   => $taxonomy_status,
                        'selected_taxonomy' => $selected_taxonomy,
                        'show_in_page'      => $show_in_page,
                        'svg_file'          => WCP_FOLDER_URL.'assets/images/pin.png',
                        'folder_settings'   => $folder_settings,
                        'hasStars'          => $hasStars,
                        'hasChildren'       => $hasChild,
                        'useFolderUndo'     => $use_folder_undo,
                        'defaultTimeout'    => $defaultTimeout,
                        'default_folder'    => $default_folder,
                        'use_shortcuts'     => $use_shortcuts,
                        'lang'              => $lang,
                        'selected_colors'   => $this->selected_colors(),
                        'form_content'      => $form_content
                    ]
                );
                // Free/Pro URL Change
                wp_enqueue_style('folders-jstree', WCP_FOLDER_URL.'assets/css/jstree.min.css', [], WCP_FOLDER_VERSION);
                wp_enqueue_style('folder-overlayscrollbars', WCP_FOLDER_URL.'assets/css/overlayscrollbars.min.css', [], WCP_FOLDER_VERSION);
                wp_enqueue_style('folder-folders', WCP_FOLDER_URL.'assets/css/folders.min.css', [], WCP_FOLDER_VERSION);
                wp_enqueue_style('folders-media', WCP_FOLDER_URL.'assets/css/page-post-media.min.css', [], WCP_FOLDER_VERSION);
                wp_enqueue_style('folder-icon', WCP_FOLDER_URL.'assets/css/folder-icon.css', [], WCP_FOLDER_VERSION);
                $width    = 275;
                $string   = "";
                $css_text = "";
                $customize_folders = get_option('customize_folders');
                $customize_folders = is_array($customize_folders)?$customize_folders:[];
                if (!isset($customize_folders['new_folder_color']) || empty($customize_folders['new_folder_color'])) {
                    $customize_folders['new_folder_color'] = "#FA166B";
                }

                $css_text .= ".media-frame a.add-new-folder { background-color: ".esc_attr($customize_folders['new_folder_color'])."; border-color: ".esc_attr($customize_folders['new_folder_color'])."}";
                $css_text .= ".wcp-hide-show-buttons .toggle-buttons { background-color: ".esc_attr($customize_folders['new_folder_color'])."; }";
                $css_text .= ".folders-toggle-button span { background-color: ".esc_attr($customize_folders['new_folder_color'])."; }";
                $css_text .= ".ui-resizable-handle.ui-resizable-e:before, .ui-resizable-handle.ui-resizable-w:before {border-color: ".esc_attr($customize_folders['new_folder_color'])." !important}";

                if (!isset($customize_folders['folder_bg_color']) || empty($customize_folders['folder_bg_color'])) {
                    $customize_folders['folder_bg_color'] = "#FA166B";
                }
                if (!isset($customize_folders['default_icon_color']) || empty($customize_folders['default_icon_color'])) {
                    $customize_folders['default_icon_color'] = "#334155";
                }

                $rgbColor  = self::hexToRgb($customize_folders['folder_bg_color']);
                $css_text .= "body:not(.no-hover-css) #custom-scroll-menu .jstree-hovered:not(.jstree-clicked), body:not(.no-hover-css) #custom-scroll-menu .jstree-hovered:not(.jstree-clicked):hover { background: rgba(".esc_attr($rgbColor['r']).",".esc_attr($rgbColor['g']).",".esc_attr($rgbColor['b']).", 0.08) !important; color: #333333;}";
                $css_text .= ".dynamic-menu li.color-folder:hover { background: rgba(".$rgbColor['r'].",".$rgbColor['g'].",".$rgbColor['b'].", 0.08) !important; }";
                $css_text .= "body:not(.no-hover-css) .dynamic-menu li.color-folder a:hover { background: transparent !important;}";
                $css_text .= "body:not(.no-hover-css) #custom-scroll-menu .jstree-clicked, body:not(.no-hover-css) #custom-scroll-menu .jstree-clicked:not(.jstree-clicked):focus, #custom-scroll-menu .jstree-clicked, #custom-scroll-menu .jstree-clicked:hover { background: ".esc_attr($customize_folders['folder_bg_color'])." !important; color: #ffffff !important; }";
                $css_text .= "body:not(.no-hover-css) #custom-scroll-menu .jstree-clicked .folder-actions { background: ".esc_attr($customize_folders['folder_bg_color'])." !important; color: #ffffff !important; }";
                $css_text .= "#custom-scroll-menu .jstree-hovered.wcp-drop-hover, #custom-scroll-menu .jstree-hovered.wcp-drop-hover:hover, #custom-scroll-menu .jstree-clicked.wcp-drop-hover, #custom-scroll-menu .jstree-clicked.wcp-drop-hover:hover, body #custom-scroll-menu  *.drag-in >, body #custom-scroll-menu  *.drag-in > a:hover { background: ".esc_attr($customize_folders['folder_bg_color'])." !important; color: #ffffff !important; }";
                $css_text .= ".drag-bot > a { border-bottom: solid 2px ".esc_attr($customize_folders['folder_bg_color'])."}";
                $css_text .= ".drag-up > a { border-top: solid 2px ".esc_attr($customize_folders['folder_bg_color'])."}";
                $css_text .= "body:not(.no-hover-css) #custom-scroll-menu *.drag-in > a.jstree-hovered, body:not(.no-hover-css) #custom-scroll-menu *.drag-in > a.jstree-hovered:hover {background: ".esc_attr($customize_folders['folder_bg_color'])." !important; color: #fff !important;}";
                $css_text .= ".orange-bg > span, .jstree-clicked, .header-posts a.active-item, .un-categorised-items.active-item, .sticky-folders ul li a.active-item { background-color: ".esc_attr($customize_folders['folder_bg_color'])." !important; color: #ffffff !important; }";
                $css_text .= "body:not(.no-hover-css) .wcp-container .route .title:hover, body:not(.no-hover-css) .header-posts a:hover, body:not(.no-hover-css) .un-categorised-items:hover, body:not(.no-hover-css) .sticky-folders ul li a:hover { background: rgba(".esc_attr($rgbColor['r'].",".$rgbColor['g'].",".$rgbColor['b'].", 0.08")."); color: #333333;}";
                $css_text .= ".wcp-drop-hover {background-color: ".esc_attr($customize_folders['folder_bg_color'])." !important; color: #ffffff; }";
                $css_text .= "#custom-menu .route .nav-icon .wcp-icon {color: ".esc_attr($customize_folders['folder_bg_color'])." !important;}";
                $css_text .= ".mCS-3d.mCSB_scrollTools .mCSB_dragger .mCSB_dragger_bar {background-color: ".esc_attr($customize_folders['folder_bg_color'])." !important;}";
                $css_text .= ".os-theme-dark>.os-scrollbar>.os-scrollbar-track>.os-scrollbar-handle {background-color: ".esc_attr($customize_folders['folder_bg_color'])." !important;}";
                $css_text .= "body:not(.no-hover-css) .jstree-hovered {background: rgba(".esc_attr($rgbColor['r'].",".$rgbColor['g'].",".$rgbColor['b'].", 0.08").") }";
                $css_text .= ".jstree-default .jstree-clicked { background-color:".esc_attr($customize_folders['folder_bg_color'])."}";
                $css_text .= ".jstree-node.drag-in > a.jstree-anchor.jstree-hovered { background-color: ".esc_attr($customize_folders['folder_bg_color'])."; color: #ffffff; }";
                $css_text .= "#custom-scroll-menu .jstree-hovered:not(.jstree-clicked) .pfolder-folder-close { color: ".esc_attr($customize_folders['folder_bg_color'])."; }";
                $css_text   .= ".pfolder-folder-close {color: ".esc_attr($customize_folders['default_icon_color'])."}";
                if (!isset($customize_folders['bulk_organize_button_color']) || empty($customize_folders['bulk_organize_button_color'])) {
                    $customize_folders['bulk_organize_button_color'] = "#FA166B";
                }

                $css_text .= "button.button.organize-button { background-color: ".esc_attr($customize_folders['bulk_organize_button_color'])."; border-color: ".esc_attr($customize_folders['bulk_organize_button_color'])."; }";
                $css_text .= "button.button.organize-button:hover { background-color: ".esc_attr($customize_folders['bulk_organize_button_color'])."; border-color: ".esc_attr($customize_folders['bulk_organize_button_color'])."; }";

                $font_family = "";
                if (isset($customize_folders['folder_font']) && !empty($customize_folders['folder_font'])) {
                    $folder_fonts = self::get_font_list();
                    $font_family  = $customize_folders['folder_font'];
                    if (isset($folder_fonts[$font_family])) {
                        $css_text .= ".wcp-container, .folder-popup-form { font-family: ".esc_attr($font_family)." !important; }";
                    }

                    if ($folder_fonts[$font_family] == "Default") {
                        $font_family = "";
                    }
                }

                if (isset($customize_folders['folder_size']) && !empty($customize_folders['folder_size'])) {
                    $css_text .= ".wcp-container .route span.title-text, .header-posts a, .un-categorised-items a, .sticky-title { font-size: ".esc_attr($customize_folders['folder_size'])."px; }";
                }

                if (!empty($font_family)) {
                    wp_enqueue_style('custom-google-fonts', 'https://fonts.googleapis.com/css?family='.urlencode($font_family), false);
                }

                wp_add_inline_style('folders-media', $css_text);
            }//end if
        }//end if

    }//end output_backbone_view_filters()


    /**
     * Get folders by hierarchy
     *
     * @since  1.0.0
     * @access public
     * @return $folders
     */
    public function get_terms_hierarchical($taxonomy)
    {
        $terms = get_terms(
            [
                'taxonomy'              => $taxonomy,
                'hide_empty'            => false,
                'parent'                => 0,
                'orderby'               => 'meta_value_num',
                'order'                 => 'ASC',
                'hierarchical'          => false,
                'update_count_callback' => '_update_generic_term_count',
                'meta_query'            => [
                    [
                        'key'  => 'wcp_custom_order',
                        'type' => 'NUMERIC',
                    ],
                ],
            ]
        );
        $hierarchical_terms = [];
        if (!empty($terms)) {
            foreach ($terms as $term) {
                if (!empty($term) && isset($term->term_id)) {
                    $term->term_name      = $term->name;
                    $hierarchical_terms[] = $term;
                    $hierarchical_terms   = self::get_child_terms($taxonomy, $hierarchical_terms, $term->term_id, "-");
                }
            }
        }

        return $hierarchical_terms;

    }//end get_terms_hierarchical()


    /**
     * Get Child folders
     *
     * @since  1.0.0
     * @access public
     * @return $folders
     */
    public static function get_child_terms($taxonomy, $hierarchical_terms, $term_id, $separator="-")
    {
        $terms = get_terms(
            [
                'taxonomy'              => $taxonomy,
                'hide_empty'            => false,
                'parent'                => $term_id,
                'orderby'               => 'meta_value_num',
                'order'                 => 'ASC',
                'hierarchical'          => false,
                'update_count_callback' => '_update_generic_term_count',
                'meta_query'            => [
                    [
                        'key'  => 'wcp_custom_order',
                        'type' => 'NUMERIC',
                    ],
                ],
            ]
        );
        if (!empty($terms)) {
            foreach ($terms as $term) {
                if (isset($term->name)) {
                    $term->name           = $separator." ".$term->name;
                    $term->term_name      = trim($term->name, "-");
                    $hierarchical_terms[] = $term;
                    $hierarchical_terms   = self::get_child_terms($taxonomy, $hierarchical_terms, $term->term_id, $separator."-");
                }
            }
        }

        return $hierarchical_terms;

    }//end get_child_terms()


    /**
     * Update folders data for page, post, media
     *
     * @since  1.0.0
     * @access public
     * @return $folders
     */
    public function update_folder_new_term_relationships($object_id="", $term_ids=[], $taxonomy="")
    {
        if (is_array($term_ids) && !empty($term_ids)) {
            $trash_folders = $initial_trash_folders = get_transient("premio_folders_without_trash");
            if ($trash_folders === false) {
                $trash_folders         = [];
                $initial_trash_folders = [];
            }

            foreach ($term_ids as $term_id) {
                $term = get_term($term_id, $taxonomy);
                if (isset($term->term_taxonomy_id) && isset($trash_folders[$term->term_taxonomy_id])) {
                    unset($trash_folders[$term->term_taxonomy_id]);
                }
            }

            if ($initial_trash_folders != $trash_folders) {
                delete_transient("premio_folders_without_trash");
                set_transient("premio_folders_without_trash", $trash_folders, (3 * DAY_IN_SECONDS));
            }
        }

    }//end update_folder_new_term_relationships()


    /**
     * Update folders data for page, post, media
     *
     * @since  1.0.0
     * @access public
     * @return $folders
     */
    public function update_folder_term_relationships($object_id="", $term_ids=[], $taxonomy="")
    {
        if (is_array($term_ids) && !empty($term_ids)) {
            $trash_folders = $initial_trash_folders = get_transient("premio_folders_without_trash");
            if ($trash_folders === false) {
                $trash_folders         = [];
                $initial_trash_folders = [];
            }

            foreach ($term_ids as $term_id) {
                $term = get_term($term_id, $taxonomy);
                if (isset($term->term_taxonomy_id) && isset($trash_folders[$term_id])) {
                    unset($trash_folders[$term->term_taxonomy_id]);
                }
            }

            if ($initial_trash_folders != $trash_folders) {
                delete_transient("premio_folders_without_trash");
                set_transient("premio_folders_without_trash", $trash_folders, (3 * DAY_IN_SECONDS));
            }
        }

    }//end update_folder_term_relationships()


    /**
     * Get child folders
     *
     * @since  1.0.0
     * @access public
     * @return $folders
     */
    private function add_child_terms_recursive($taxonomy, $hierarchical_terms, $hierarchy, $current_term_id, $current_depth)
    {

        if (! isset($hierarchy[$current_term_id])) {
            return $hierarchical_terms;
        }

        foreach ($hierarchy[$current_term_id] as $child_term_id) {
            $child_term = get_term($child_term_id, $taxonomy);

            $child_term->name = str_pad('', $current_depth, '-', STR_PAD_LEFT).' '.$child_term->name;

            $hierarchical_terms[] = $child_term;

            $hierarchical_terms = self::add_child_terms_recursive($taxonomy, $hierarchical_terms, $hierarchy, $child_term_id, ( $current_depth + 1 ));
        }

        return $hierarchical_terms;

    }//end add_child_terms_recursive()


    /**
     * Filter data for attachments
     *
     * @since  1.0.0
     * @access public
     * @return $folders
     */
    public function filter_attachments_list($query)
    {

        if (! isset($query->query['post_type'])) {
            return $query;
        }

        if (is_array($query->query['post_type']) && ! in_array('attachment', $query->query['post_type'])) {
            return $query;
        }

        if (! is_array($query->query['post_type']) && strpos($query->query['post_type'], 'attachment') === false) {
            return $query;
        }

        if (! isset($_REQUEST['media_folder'])) {
            return $query;
        }

        $term = sanitize_text_field(wp_unslash($_REQUEST['media_folder']));
        if ($term != "-1") {
            return $query;
        }

        unset($query->query_vars['media_folder']);

        $tax_query = [
            'taxonomy' => 'media_folder',
            'operator' => 'NOT EXISTS',
        ];

        $query->set('tax_query', [ $tax_query ]);
        $query->tax_query = new WP_Tax_Query([ $tax_query ]);

        $query = apply_filters('media_library_organizer_media_filter_attachments', $query, $_REQUEST);

        return $query;

    }//end filter_attachments_list()


    /**
     * Filter data for attachments
     *
     * @since  1.0.0
     * @access public
     * @return $folders
     */
    public function output_list_table_filters($post_type, $view_name = "")
    {
        if ($post_type != 'attachment') {
            return;
        }

        if ($view_name != 'bar') {
            return;
        }

        if (!self::is_for_this_post_type('attachment')) {
            return;
        }

        $current_term = false;
        if (isset($_REQUEST['media_folder'])) {
            $current_term = sanitize_text_field($_REQUEST['media_folder']);
        }

        wp_dropdown_categories(
            [
                'show_option_all'       => esc_html__('All Folders', 'folders'),
                'show_option_none'      => esc_html__('(Unassigned)', 'folders'),
                'option_none_value'     => -1,
                'orderby'               => 'meta_value_num',
                'order'                 => 'ASC',
                'show_count'            => true,
                'hide_empty'            => false,
                'update_count_callback' => '_update_generic_term_count',
                'echo'                  => true,
                'selected'              => $current_term,
                'hierarchical'          => true,
                'name'                  => 'media_folder',
                'id'                    => '',
                'class'                 => '',
                'taxonomy'              => 'media_folder',
                'value_field'           => 'slug',
                'meta_query'            => [
                    [
                        'key'  => 'wcp_custom_order',
                        'type' => 'NUMERIC',
                    ],
                ],
            ]
        );

    }//end output_list_table_filters()


    /**
     * Update folder relationship with page, post, media
     *
     * @since  1.0.0
     * @access public
     * @return $folders
     */
    function new_to_auto_draft($post)
    {

        $post_type = $post->post_type;

        if (self::is_for_this_post_type($post_type) && !isset($_REQUEST["folder_for_media"])) {
            $post_type       = self::get_custom_post_type($post_type);
            $selected_folder = get_option("selected_".esc_attr($post_type)."_folder");

            if ($selected_folder != null && !empty($selected_folder)) {
                $terms = get_term($selected_folder);
                if (!empty($terms) && isset($terms->slug)) {
                    wp_set_object_terms($post->ID, $terms->slug, $post_type);
                }
            }
        }

    }//end new_to_auto_draft()


    /**
     * Send message to owner for Help
     *
     * @since  1.0.0
     * @access public
     * @return $response
     */
    public function wcp_folder_send_message_to_owner()
    {
        if (current_user_can('manage_options')) {
            $response            = [];
            $response['status']  = 0;
            $response['error']   = 0;
            $response['errors']  = [];
            $response['message'] = "";
            $errorArray          = [];
            $errorMessage        = esc_html__("%\$s is required", 'folders');
            $postData            = filter_input_array(INPUT_POST);
            if (!isset($postData['textarea_text']) || trim($postData['textarea_text']) == "") {
                $error        = [
                    "key"     => "textarea_text",
                    "message" => esc_html__("Please enter your message", 'folders'),
                ];
                $errorArray[] = $error;
            }

            if (!isset($postData['user_email']) || trim($postData['user_email']) == "") {
                $error        = [
                    "key"     => "user_email",
                    "message" => sprintf($errorMessage, __("Email", 'folders')),
                ];
                $errorArray[] = $error;
            } else if (!filter_var($postData['user_email'], FILTER_VALIDATE_EMAIL)) {
                $error        = [
                    'key'     => "user_email",
                    "message" => "Email is not valid",
                ];
                $errorArray[] = $error;
            }

            if (empty($errorArray)) {
                if (!isset($postData['folder_help_nonce']) || trim($postData['folder_help_nonce']) == "") {
                    $error        = [
                        "key"     => "nonce",
                        "message" => esc_html__("Your request is not valid", 'folders'),
                    ];
                    $errorArray[] = $error;
                } else {
                    if (!wp_verify_nonce($postData['folder_help_nonce'], 'wcp_folder_help_nonce')) {
                        $error        = [
                            "key"     => "nonce",
                            "message" => esc_html__("Your request is not valid", 'folders'),
                        ];
                        $errorArray[] = $error;
                    }
                }
            }

            if (empty($errorArray)) {
                $text_message = self::sanitize_options($postData['textarea_text']);
                $email        = self::sanitize_options($postData['user_email'], "email");
                $domain       = site_url();
                $current_user = wp_get_current_user();
                $user_name    = $current_user->first_name." ".$current_user->last_name;

                $response['status'] = 1;

                // sending message to Crisp
                $post_message = [];

                $message_data          = [];
                $message_data['key']   = "Plugin";
                $message_data['value'] = "Folders";
                $post_message[]        = $message_data;

                $message_data          = [];
                $message_data['key']   = "Domain";
                $message_data['value'] = $domain;
                $post_message[]        = $message_data;

                $message_data          = [];
                $message_data['key']   = "Email";
                $message_data['value'] = $email;
                $post_message[]        = $message_data;

                $message_data          = [];
                $message_data['key']   = "Message";
                $message_data['value'] = $text_message;
                $post_message[]        = $message_data;

                $api_params = [
                    'domain'  => $domain,
                    'email'   => $email,
                    'url'     => site_url(),
                    'name'    => $user_name,
                    'message' => $post_message,
                    'plugin'  => "Folders",
                    'type'    => "Need Help",
                ];

                // Sending message to Crisp API
                $crisp_response = wp_safe_remote_post("https://go.premio.io/crisp/crisp-send-message.php", ['body' => $api_params, 'timeout' => 15, 'sslverify' => true]);

                if (is_wp_error($crisp_response)) {
                    wp_safe_remote_post("https://go.premio.io/crisp/crisp-send-message.php", ['body' => $api_params, 'timeout' => 15, 'sslverify' => false]);
                }
            } else {
                $response['error']  = 1;
                $response['errors'] = $errorArray;
            }//end if

            echo wp_json_encode($response);
        }//end if

    }//end wcp_folder_send_message_to_owner()


    /**
     * Send message to owner when folder is deactivated
     *
     * @since  1.0.0
     * @access public
     * @return $response
     */
    public function folder_plugin_deactivate()
    {
        if (current_user_can('manage_options')) {
            $postData            = filter_input_array(INPUT_POST);
            $errorCounter        = 0;
            $response            = [];
            $response['status']  = 0;
            $response['message'] = "";
            $response['valid']   = 1;
            if (!isset($postData['reason']) || empty($postData['reason'])) {
                $errorCounter++;
                $response['message'] = "Please provide reason";
            } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
                $response['message'] = esc_html__("Your request is not valid", 'folders');
                $errorCounter++;
                $response['valid'] = 0;
            } else {
                $nonce = self::sanitize_options($postData['nonce']);
                if (!wp_verify_nonce($nonce, 'wcp_folder_deactivate_nonce')) {
                    $response['message'] = esc_html__("Your request is not valid", 'folders');
                    $errorCounter++;
                    $response['valid'] = 0;
                }
            }

            if ($errorCounter == 0) {
                $reason = $postData['reason'];
                $email  = "none@none.none";
                if (isset($postData['email_id']) && !empty($postData['email_id']) && filter_var($postData['email_id'], FILTER_VALIDATE_EMAIL)) {
                    $email = $postData['email_id'];
                }

                $domain       = site_url();
                $current_user = wp_get_current_user();
                $user_name    = $current_user->first_name." ".$current_user->last_name;

                $response['status'] = 1;

                // sending message to Crisp
                $post_message = [];

                $message_data          = [];
                $message_data['key']   = "Plugin";
                $message_data['value'] = "Folders";
                $post_message[]        = $message_data;

                $message_data          = [];
                $message_data['key']   = "Plugin Version";
                $message_data['value'] = WCP_FOLDER_VERSION;
                $post_message[]        = $message_data;

                $message_data          = [];
                $message_data['key']   = "Domain";
                $message_data['value'] = $domain;
                $post_message[]        = $message_data;

                $message_data          = [];
                $message_data['key']   = "Email";
                $message_data['value'] = $email;
                $post_message[]        = $message_data;

                $message_data          = [];
                $message_data['key']   = "WordPress Version";
                $message_data['value'] = esc_attr(get_bloginfo('version'));
                $post_message[]        = $message_data;

                $message_data          = [];
                $message_data['key']   = "PHP Version";
                $message_data['value'] = PHP_VERSION;
                $post_message[]        = $message_data;

                $message_data          = [];
                $message_data['key']   = "Message";
                $message_data['value'] = $reason;
                $post_message[]        = $message_data;

                $api_params = [
                    'domain'  => $domain,
                    'email'   => $email,
                    'url'     => site_url(),
                    'name'    => $user_name,
                    'message' => $post_message,
                    'plugin'  => "Folders",
                    'type'    => "Uninstall",
                ];

                // Sending message to Crisp API
                $crisp_response = wp_safe_remote_post("https://go.premio.io/crisp/crisp-send-message.php", ['body' => $api_params, 'timeout' => 15, 'sslverify' => true]);

                if (is_wp_error($crisp_response)) {
                    wp_safe_remote_post("https://go.premio.io/crisp/crisp-send-message.php", ['body' => $api_params, 'timeout' => 15, 'sslverify' => false]);
                }
            }//end if

            echo wp_json_encode($response);
            wp_die();
        }//end if

    }//end folder_plugin_deactivate()


    /**
     * Returns total folders
     *
     * @since  1.0.0
     * @access public
     * @return $total
     */
    public static function ttl_fldrs()
    {
        $post_types = get_option('folders_settings');
        $post_types = is_array($post_types) ? $post_types : [];
        $total      = 0;
        foreach ($post_types as $post_type) {
            $post_type = self::get_custom_post_type($post_type);
            $total    += wp_count_terms($post_type);
        }

        return $total;

    }//end ttl_fldrs()


    /**
     * Remove post
     *
     * @since  1.0.0
     * @access public
     * @return $response
     */
    public function wcp_remove_post_item()
    {
        $response            = [];
        $response['status']  = 0;
        $response['error']   = 0;
        $response['data']    = [];
        $response['message'] = "";
        $postData            = filter_input_array(INPUT_POST);
        if (isset($postData['post_id']) && !empty($postData['post_id'])) {
            wp_delete_post($postData['post_id']);
            $response['status'] = 1;
        }

        echo wp_json_encode($response);
        wp_die();

    }//end wcp_remove_post_item()


    /**
     * Mark all folders
     *
     * @since  1.0.0
     * @access public
     * @return $response
     */
    public function wcp_change_all_status()
    {
        $response            = [];
        $response['status']  = 0;
        $response['error']   = 0;
        $response['data']    = [];
        $response['message'] = "";
        $postData            = filter_input_array(INPUT_POST);
        $errorCounter        = 0;
        if (!isset($postData['type']) || empty($postData['type'])) {
            $response['message'] = esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] = esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!current_user_can("manage_categories") || ($postData['type'] == "page" && !current_user_can("edit_pages"))) {
            $response['message'] = esc_html__("You have not permission to update width", 'folders');
            $errorCounter++;
        } else if (!current_user_can("manage_categories") || ($postData['type'] != "page" && !current_user_can("edit_posts"))) {
            $response['message'] = esc_html__("You have not permission to update width", 'folders');
            $errorCounter++;
        } else {
            $type  = self::sanitize_options($postData['type']);
            $nonce = self::sanitize_options($postData['nonce']);
            if (!wp_verify_nonce($nonce, 'wcp_folder_nonce_'.$type)) {
                $response['message'] = esc_html__("Your request is not valid", 'folders');
                $errorCounter++;
            }
        }

        if ($errorCounter == 0) {
            if (isset($postData['folders']) || !empty($postData['folders'])) {
                $status  = isset($postData['status']) ? $postData['status'] : 0;
                $status  = self::sanitize_options($status);
                $folders = self::sanitize_options($postData['folders']);
                $folders = trim($folders, ",");
                $folders = explode(",", $folders);
                foreach ($folders as $folder) {
                    update_term_meta($folder, "is_active", $status);
                }
            }

            $response['status'] = 1;
        }

        echo wp_json_encode($response);
        wp_die();

    }//end wcp_change_all_status()


    /**
     * Change folder width
     *
     * @since  1.0.0
     * @access public
     * @return $response
     */
    public function wcp_change_post_width()
    {
        $response            = [];
        $response['status']  = 0;
        $response['error']   = 0;
        $response['data']    = [];
        $response['message'] = "";
        $postData            = filter_input_array(INPUT_POST);
        $errorCounter        = 0;
        if (!isset($postData['width']) || empty($postData['width'])) {
            $response['message'] = esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!isset($postData['type']) || empty($postData['type'])) {
            $response['message'] = esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] = esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if ($postData['type'] == "page" && !current_user_can("edit_pages")) {
            $response['message'] = esc_html__("You have not permission to update width", 'folders');
            $errorCounter++;
        } else if ($postData['type'] != "page" && !current_user_can("edit_posts")) {
            $response['message'] = esc_html__("You have not permission to update width", 'folders');
            $errorCounter++;
        } else {
            $type  = self::sanitize_options($postData['type']);
            $nonce = self::sanitize_options($postData['nonce']);
            if (!wp_verify_nonce($nonce, 'wcp_folder_nonce_'.$type)) {
                $response['message'] = esc_html__("Your request is not valid", 'folders');
                $errorCounter++;
            }
        }//end if

        if ($errorCounter == 0) {
            $type       = self::sanitize_options($postData['type']);
            $width      = self::sanitize_options($postData['width'], "int");
            $optionName = "wcp_dynamic_width_for_".$type;
            update_option($optionName, $width);
            $response['status'] = 1;
        }

        echo wp_json_encode($response);
        wp_die();

    }//end wcp_change_post_width()


    /**
     * Assign folder to multiple posts, pages, attachments
     *
     * @since  1.0.0
     * @access public
     * @return $response
     */
    public function wcp_change_multiple_post_folder()
    {
        $response            = [];
        $response['status']  = 0;
        $response['error']   = 0;
        $response['data']    = [];
        $response['message'] = "";
        $postData            = filter_input_array(INPUT_POST);
        $errorCounter        = 0;
        if (!isset($postData['post_ids']) || empty($postData['post_ids'])) {
            $response['message'] = esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!isset($postData['folder_id']) || empty($postData['folder_id'])) {
            $response['message'] = esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!isset($postData['type']) || empty($postData['type'])) {
            $response['message'] = esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] = esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if ($postData['type'] == "page" && !current_user_can("edit_pages")) {
            $response['message'] = esc_html__("You have not permission to update folder", 'folders');
            $errorCounter++;
        } else if ($postData['type'] != "page" && !current_user_can("edit_posts")) {
            $response['message'] = esc_html__("You have not permission to update folder", 'folders');
            $errorCounter++;
        } else {
            $folder_id = self::sanitize_options($postData['folder_id']);
            if (!wp_verify_nonce($postData['nonce'], 'wcp_folder_term_'.$folder_id)) {
                $response['message'] = esc_html__("Your request is not valid", 'folders');
                $errorCounter++;
            }
        }//end if

        if ($errorCounter == 0) {
            $folderUndoSettings = [];
            $postID    = self::sanitize_options($postData['post_ids']);
            $postID    = trim($postID, ",");
            $folderID  = self::sanitize_options($postData['folder_id']);
            $type      = self::sanitize_options($postData['type']);
            $postArray = explode(",", $postID);
            $status    = 0;
            if (isset($postData['status'])) {
                $status = self::sanitize_options($postData['status']);
            }

            $status = true;

            $taxonomy = "";
            if (isset($postData['taxonomy'])) {
                $taxonomy = self::sanitize_options($postData['taxonomy']);
            }

            if (is_array($postArray)) {
                $post_type = self::get_custom_post_type($type);
                foreach ($postArray as $post) {
                    $terms      = get_the_terms($post, $post_type);
                    $post_terms = [
                        'post_id' => $post,
                        'terms'   => $terms,
                    ];
                    $folderUndoSettings[] = $post_terms;
                    if (!empty($terms)) {
                        foreach ($terms as $term) {
                            if (!empty($taxonomy) && ($term->term_id == $taxonomy || $term->slug == $taxonomy)) {
                                wp_remove_object_terms($post, $term->term_id, $post_type);
                            }
                        }
                    }

                    wp_set_post_terms($post, $folderID, $post_type, $status);
                }
            }

            $response['status'] = 1;
            delete_transient("folder_undo_settings");
            delete_transient("premio_folders_without_trash");
            set_transient("folder_undo_settings", $folderUndoSettings, DAY_IN_SECONDS);

            if(!get_option("show_folder_upgrade_popup")) {
                add_option("show_folder_upgrade_popup", "hide");
            }
        }//end if

        echo wp_json_encode($response);
        wp_die();

    }//end wcp_change_multiple_post_folder()


    /**
     * Undo folder changes
     *
     * @since  1.0.0
     * @access public
     * @return $response
     */
    public function wcp_undo_folder_changes()
    {
        $response            = [];
        $response['status']  = 0;
        $response['error']   = 0;
        $response['data']    = [];
        $response['message'] = "";
        $postData            = filter_input_array(INPUT_POST);
        $errorCounter        = 0;
        if (!isset($postData['post_type']) || empty($postData['post_type'])) {
            $response['message'] = esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] = esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else {
            if (!wp_verify_nonce($postData['nonce'], 'wcp_folder_nonce_'.$postData['post_type'])) {
                $response['message'] = esc_html__("Your request is not valid", 'folders');
                $errorCounter++;
            }
        }

        if ($errorCounter == 0) {
            $response['status']   = 1;
            $folder_undo_settings = get_transient("folder_undo_settings");
            $type      = self::sanitize_options($postData['post_type']);
            $post_type = self::get_custom_post_type($type);
            if (!empty($folder_undo_settings) && is_array($folder_undo_settings)) {
                $trash_folders = $initial_trash_folders = get_transient("premio_folders_without_trash");
                if ($trash_folders === false) {
                    $trash_folders         = [];
                    $initial_trash_folders = [];
                }

                foreach ($folder_undo_settings as $item) {
                    $terms = get_the_terms($item['post_id'], $post_type);
                    if (!empty($terms)) {
                        foreach ($terms as $term) {
                            wp_remove_object_terms($item['post_id'], $term->term_id, $post_type);
                            if (isset($trash_folders[$term->term_taxonomy_id])) {
                                unset($trash_folders[$term->term_taxonomy_id]);
                            }
                        }
                    }

                    if (!empty($item['terms']) && is_array($item['terms'])) {
                        foreach ($item['terms'] as $term) {
                            wp_set_post_terms($item['post_id'], $term->term_id, $post_type, true);
                            if (isset($trash_folders[$term->term_taxonomy_id])) {
                                unset($trash_folders[$term->term_taxonomy_id]);
                            }
                        }
                    }
                }

                if (!empty($terms) && $initial_trash_folders != $trash_folders) {
                    delete_transient("premio_folders_without_trash");
                    set_transient("premio_folders_without_trash", $trash_folders, (3 * DAY_IN_SECONDS));
                }
            }//end if
        }//end if

        echo wp_json_encode($response);
        die;

    }//end wcp_undo_folder_changes()


    /**
     * Change post, page, attachment folder
     *
     * @since  1.0.0
     * @access public
     * @return $response
     */
    public function wcp_change_post_folder()
    {
        $response            = [];
        $response['status']  = 0;
        $response['error']   = 0;
        $response['data']    = [];
        $response['message'] = "";
        $postData            = filter_input_array(INPUT_POST);
        $errorCounter        = 0;
        if (!isset($postData['post_id']) || empty($postData['post_id'])) {
            $errorCounter++;
            $response['message'] = esc_html__("Your request is not valid", 'folders');
        } else if (!isset($postData['folder_id']) || empty($postData['folder_id'])) {
            $errorCounter++;
            $response['message'] = esc_html__("Your request is not valid", 'folders');
        } else if (!isset($postData['type']) || empty($postData['type'])) {
            $errorCounter++;
            $response['message'] = esc_html__("Your request is not valid", 'folders');
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] = esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if ($postData['type'] == "page" && !current_user_can("edit_pages")) {
            $response['message'] = esc_html__("You have not permission to update folder", 'folders');
            $errorCounter++;
        } else if ($postData['type'] != "page" && !current_user_can("edit_posts")) {
            $response['message'] = esc_html__("You have not permission to update folder", 'folders');
            $errorCounter++;
        } else {
            $term_id = self::sanitize_options($postData['folder_id']);
            if (!wp_verify_nonce($postData['nonce'], 'wcp_folder_term_'.$term_id)) {
                $response['message'] = esc_html__("Your request is not valid", 'folders');
                $errorCounter++;
            }
        }//end if

        if ($errorCounter == 0) {
            $postID   = self::sanitize_options($postData['post_id']);
            $folderID = self::sanitize_options($postData['folder_id']);
            $type     = self::sanitize_options($postData['type']);
            $folder_post_type = self::get_custom_post_type($type);
            $status           = 0;
            if (isset($postData['status'])) {
                $status = self::sanitize_options($postData['status']);
            }

            $status   = ($status == 1) ? true : false;
            $taxonomy = "";
            if (isset($postData['taxonomy'])) {
                $taxonomy = self::sanitize_options($postData['taxonomy']);
            }

            $terms = get_the_terms($postID, $folder_post_type);
            if (!empty($terms)) {
                foreach ($terms as $term) {
                    if (!empty($taxonomy) && ($term->term_id == $taxonomy || $term->slug == $taxonomy)) {
                        wp_remove_object_terms($postID, $term->term_id, $folder_post_type);
                    }
                }
            }

            wp_set_post_terms($postID, $folderID, $folder_post_type, true);
            $response['status'] = 1;
        }//end if

        echo wp_json_encode($response);
        wp_die();

    }//end wcp_change_post_folder()


    /**
     * Mark/Unmark folder
     *
     * @since  1.0.0
     * @access public
     * @return $response
     */
    public function wcp_mark_un_mark_folder()
    {
        $response            = [];
        $response['status']  = 0;
        $response['error']   = 0;
        $response['data']    = [];
        $response['message'] = "";
        $postData            = filter_input_array(INPUT_POST);
        $errorCounter        = 0;
        if (!current_user_can("manage_categories")) {
            $response['message'] = esc_html__("You have not permission to update folder", 'folders');
            $errorCounter++;
        } else if (!isset($postData['term_id']) || empty($postData['term_id'])) {
            $errorCounter++;
            $response['message'] = esc_html__("Your request is not valid", 'folders');
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] = esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else {
            $term_id = self::sanitize_options($postData['term_id']);
            if (!wp_verify_nonce($postData['nonce'], 'wcp_folder_term_'.$term_id)) {
                $response['message'] = esc_html__("Your request is not valid", 'folders');
                $errorCounter++;
            }
        }

        if ($errorCounter == 0) {
            $term_id = self::sanitize_options($postData['term_id']);
            $folder_info    = get_term_meta($term_id, "folder_info", true);

            $status = intval(isset($folder_info['is_high'])?$folder_info['is_high']:0);
            $status = ($status) ? 0 : 1;

            if ($folder_info) {
                $folder_info['is_high'] = $status;
                update_term_meta($term_id, "folder_info", $folder_info);
            } else {
                $folder_info = [];
                $folder_info['is_high'] = $status;
                add_term_meta($term_id, "folder_info", $folder_info);
            }

            $response['marked'] = $status;
            $response['id']     = $term_id;
            $response['status'] = 1;
        }

        echo wp_json_encode($response);
        wp_die();

    }//end wcp_mark_un_mark_folder()


    /**
     * Make folder Sticky
     *
     * @since  1.0.0
     * @access public
     * @return $response
     */
    public function wcp_make_sticky_folder()
    {
        $response            = [];
        $response['status']  = 0;
        $response['error']   = 0;
        $response['data']    = [];
        $response['message'] = "";
        $postData            = filter_input_array(INPUT_POST);
        $errorCounter        = 0;
        if (!current_user_can("manage_categories")) {
            $response['message'] = esc_html__("You have not permission to update folder", 'folders');
            $errorCounter++;
        } else if (!isset($postData['term_id']) || empty($postData['term_id'])) {
            $errorCounter++;
            $response['message'] = esc_html__("Your request is not valid", 'folders');
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] = esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else {
            $term_id = self::sanitize_options($postData['term_id']);
            if (!wp_verify_nonce($postData['nonce'], 'wcp_folder_term_'.$term_id)) {
                $response['message'] = esc_html__("Your request is not valid", 'folders');
                $errorCounter++;
            }
        }

        if ($errorCounter == 0) {
            $term_id = self::sanitize_options($postData['term_id']);
            $folder_info    = get_term_meta($term_id, "folder_info", true);

            $status = intval(isset($folder_info['is_sticky'])?$folder_info['is_sticky']:0);
            $status = ($status) ? 0 : 1;

            if ($folder_info) {
                $folder_info['is_sticky'] = $status;
                update_term_meta($term_id, "folder_info", $folder_info);
            } else {
                $folder_info = [];
                $folder_info['is_sticky'] = $status;
                add_term_meta($term_id, "folder_info", $folder_info);
            }

            $response['is_folder_sticky'] = $status;
            $response['id']     = $postData['term_id'];
            $response['status'] = 1;
        }

        echo wp_json_encode($response);
        wp_die();

    }//end wcp_make_sticky_folder()


    /**
     * Save folder order
     *
     * @since  1.0.0
     * @access public
     * @return $response
     */
    public function wcp_save_folder_order()
    {
        $response            = [];
        $response['status']  = 0;
        $response['error']   = 0;
        $response['data']    = [];
        $response['message'] = "";
        $postData            = filter_input_array(INPUT_POST);
        $errorCounter        = 0;
        if (!current_user_can("manage_categories")) {
            $response['message'] = esc_html__("You have not permission to update folder order", 'folders');
            $errorCounter++;
        } else if (!isset($postData['term_ids']) || empty($postData['term_ids'])) {
            $errorCounter++;
            $response['message'] = esc_html__("Your request is not valid", 'folders');
        } else if (!isset($postData['type']) || empty($postData['type'])) {
            $errorCounter++;
            $response['message'] = esc_html__("Your request is not valid", 'folders');
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] = esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else {
            $type = self::sanitize_options($postData['type']);
            if (!wp_verify_nonce($postData['nonce'], 'wcp_folder_nonce_'.$type)) {
                $response['message'] = esc_html__("Your request is not valid", 'folders');
                $errorCounter++;
            }
        }

        if ($errorCounter == 0) {
            $termIds   = self::sanitize_options(($postData['term_ids']));
            $type      = self::sanitize_options($postData['type']);
            $termIds   = trim($termIds, ",");
            $termArray = explode(",", $termIds);
            $order     = 1;
            foreach ($termArray as $term) {
                if (!empty($term)) {
                    update_term_meta($term, "wcp_custom_order", $order);
                    $order++;
                }
            }

            $term_id     = self::sanitize_options($postData['term_id']);
            $parent_id   = self::sanitize_options($postData['parent_id']);
            $type        = self::sanitize_options($postData['type']);
            $folder_type = self::get_custom_post_type($type);
            wp_update_term(
                $term_id,
                $folder_type,
                ['parent' => $parent_id]
            );

            if ($parent_id != "#" || !empty($parent_id)) {
                update_term_meta($parent_id, "is_active", 1);
            }

            $response['status'] = 1;
            $folder_type        = self::get_custom_post_type($type);
            // Free/Pro Class name change
            $response['options'] = WCP_Tree::get_option_data_for_select($folder_type);
        }//end if

        echo wp_json_encode($response);
        wp_die();

    }//end wcp_save_folder_order()


    /**
     * Make folder State open/closed
     *
     * @since  1.0.0
     * @access public
     * @return $response
     */
    public function save_wcp_folder_state()
    {
        $response            = [];
        $response['status']  = 0;
        $response['error']   = 0;
        $response['data']    = [];
        $response['message'] = "";
        $postData            = filter_input_array(INPUT_POST);
        $errorCounter        = 0;
        if (!current_user_can("manage_categories")) {
            $response['message'] = esc_html__("You have not permission to update folder", 'folders');
            $errorCounter++;
        } else if (!isset($postData['term_id']) || empty($postData['term_id'])) {
            $response['message'] = esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] = esc_html__("Unable to create folder, Your request is not valid", 'folders');
            $errorCounter++;
        } else {
            $term_id = self::sanitize_options($postData['term_id']);
            if (!wp_verify_nonce($postData['nonce'], 'wcp_folder_term_'.$term_id)) {
                $response['message'] = esc_html__("Your request is not valid", 'folders');
                $errorCounter++;
            }
        }

        if ($errorCounter == 0) {
            $response['status'] = 1;
            $term_id            = self::sanitize_options($postData['term_id']);
            $is_active          = isset($postData['is_active']) ? $postData['is_active'] : 0;
            $is_active          = self::sanitize_options($is_active);
            $folder_info    = get_term_meta($term_id, "folder_info", true);

            if ($folder_info) {
                $folder_info['is_active'] = $is_active;
                update_term_meta($term_id, "folder_info", $folder_info);
            } else {
                $folder_info = [];
                $folder_info['is_active'] = $is_active;
                add_term_meta($term_id, "folder_info", $folder_info);
            }
        }

        echo wp_json_encode($response);
        wp_die();

    }//end save_wcp_folder_state()


    /**
     * Save parent folder information with order
     *
     * @since  1.0.0
     * @access public
     * @return $response
     */
    public function wcp_update_parent_information()
    {
        $response            = [];
        $response['status']  = 0;
        $response['error']   = 0;
        $response['data']    = [];
        $response['message'] = "";
        $postData            = filter_input_array(INPUT_POST);
        $errorCounter        = 0;
        if (!current_user_can("manage_categories")) {
            $response['message'] = esc_html__("You have not permission to update folder", 'folders');
            $errorCounter++;
        } else if (!isset($postData['term_id']) || empty($postData['term_id'])) {
            $response['message'] = esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!isset($postData['type']) || empty($postData['type'])) {
            $response['message'] = esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!isset($postData['parent_id'])) {
            $response['message'] = esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] = esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else {
            $term_id = self::sanitize_options($postData['term_id']);
            if (!wp_verify_nonce($postData['nonce'], 'wcp_folder_term_'.$term_id)) {
                $response['message'] = esc_html__("Your request is not valid", 'folders');
                $errorCounter++;
            }
        }//end if

        if ($errorCounter == 0) {
            $term_id     = self::sanitize_options($postData['term_id']);
            $parent_id   = self::sanitize_options($postData['parent_id']);
            $type        = self::sanitize_options($postData['type']);
            $folder_type = self::get_custom_post_type($type);
            wp_update_term(
                $term_id,
                $folder_type,
                ['parent' => $parent_id]
            );
            update_term_meta($parent_id, "is_active", 1);
            $response['status'] = 1;
        }

        echo wp_json_encode($response);
        wp_die();

    }//end wcp_update_parent_information()


    /**
     * Save parent folder data
     *
     * @since  1.0.0
     * @access public
     * @return $response
     */
    public function wcp_save_parent_data()
    {
        $response            = [];
        $response['status']  = 0;
        $response['error']   = 0;
        $response['data']    = [];
        $response['message'] = "";
        $postData            = filter_input_array(INPUT_POST);
        $errorCounter        = 0;
        if (!isset($postData['type']) || empty($postData['type'])) {
            $response['message'] = esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] = esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!current_user_can("manage_categories")) {
            $response['message'] = esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else {
            $type = self::sanitize_options($postData['type']);
            if (!wp_verify_nonce($postData['nonce'], 'wcp_folder_nonce_'.$type)) {
                $response['message'] = esc_html__("Your request is not valid", 'folders');
                $errorCounter++;
            }
        }

        if ($errorCounter == 0) {
            $type       = self::sanitize_options($postData['type']);
            $optionName = $type."_parent_status";
            $response['status'] = 1;
            $is_active          = isset($postData['is_active']) ? $postData['is_active'] : 0;
            $is_active          = self::sanitize_options($is_active);
            if ($is_active == 1) {
                update_option($optionName, 1);
            } else {
                update_option($optionName, 0);
            }
        }

        echo wp_json_encode($response);
        wp_die();

    }//end wcp_save_parent_data()


    /**
     * Remove multiple folders
     *
     * @since  1.0.0
     * @access public
     * @return $response
     */
    public function remove_muliple_folder()
    {
        $response            = [];
        $response['status']  = 0;
        $response['error']   = 0;
        $response['data']    = [];
        $response['message'] = "";
        $postData            = filter_input_array(INPUT_POST);
        $errorCounter        = 0;
        $error = "";
        if (!current_user_can("manage_categories")) {
            $error = esc_html__("You have not permission to remove folder", 'folders');
            $errorCounter++;
        } else if (!isset($postData['term_id']) || empty($postData['term_id'])) {
            $error = esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!isset($postData['type']) || empty($postData['type'])) {
            $error = esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] = esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else {
            $type = self::sanitize_options($postData['type']);
            if (!wp_verify_nonce($postData['nonce'], 'wcp_folder_nonce_'.$type)) {
                $error = esc_html__("Your request is not valid", 'folders');
                $errorCounter++;
            }
        }

        if ($errorCounter == 0) {
            $term_id = self::sanitize_options($postData['term_id']);
            $type    = self::sanitize_options($postData['type']);
            $response['term_ids'] = [];
            if (!empty($term_id)) {
                $term_id  = trim($term_id, ",");
                $term_ids = explode(",", $term_id);
                if (is_array($term_ids) && count($term_ids) > 0) {
                    foreach ($term_ids as $term) {
                        self::remove_folder_child_items($term, $type);
                    }

                    $response['term_ids'] = $term_ids;
                }
            }

            $is_active          = 1;
            $folders            = -1;
            $response['status'] = 1;
            $response['folders']       = $folders;
            $response['is_key_active'] = $is_active;
        } else {
            $response['error']   = 1;
            $response['message'] = $error;
        }//end if

        echo wp_json_encode($response);
        wp_die();

    }//end remove_muliple_folder()


    /**
     * Remove folder
     *
     * @since  1.0.0
     * @access public
     * @return $response
     */
    public function wcp_remove_folder()
    {
        $response            = [];
        $response['status']  = 0;
        $response['error']   = 0;
        $response['data']    = [];
        $response['message'] = "";
        $postData            = filter_input_array(INPUT_POST);
        $errorCounter        = 0;
        if (!current_user_can("manage_categories")) {
            $error = esc_html__("You have not permission to remove folder", 'folders');
            $errorCounter++;
        } else if (!isset($postData['term_id']) || empty($postData['term_id'])) {
            $error = esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!isset($postData['type']) || empty($postData['type'])) {
            $error = esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $error = esc_html__("Unable to delete folder, Your request is not valid", 'folders');
            $errorCounter++;
        } else {
            $term_id = self::sanitize_options($postData['term_id']);
            if (!wp_verify_nonce($postData['nonce'], 'wcp_folder_term_'.$term_id)) {
                $error = esc_html__("Unable to delete folder, Your request is not valid", 'folders');
                $errorCounter++;
            }
        }

        if ($errorCounter == 0) {
            $term_id = self::sanitize_options($postData['term_id']);
            $type    = self::sanitize_options($postData['type']);
            self::remove_folder_child_items($term_id, $type);
            $response['status'] = 1;
            $is_active          = 1;
            $folders            = -1;
            $response['folders']       = $folders;
            $response['term_id']       = $term_id;
            $response['is_key_active'] = $is_active;
        } else {
            $response['error']   = 1;
            $response['message'] = $error;
        }

        echo wp_json_encode($response);
        wp_die();

    }//end wcp_remove_folder()


    /**
     * Remove folders for page, post, attachment
     *
     * @since  1.0.0
     * @access public
     * @return $response
     */
    public function remove_folder_child_items($term_id, $post_type)
    {
        $folder_type = self::get_custom_post_type($post_type);
        $terms       = get_terms(
            [
                'taxonomy'   => $folder_type,
                'hide_empty' => false,
                'parent'     => $term_id,
            ]
        );

        if (!empty($terms)) {
            foreach ($terms as $term) {
                self::remove_folder_child_items($term->term_id, $post_type);
            }

            wp_delete_term($term_id, $folder_type);
        } else {
            wp_delete_term($term_id, $folder_type);
        }

    }//end remove_folder_child_items()


    /**
     * Update Folder
     *
     * @since  1.0.0
     * @access public
     * @return $response
     */
    public function wcp_update_folder()
    {
        $response            = [];
        $response['status']  = 0;
        $response['error']   = 0;
        $response['data']    = [];
        $response['message'] = "";
        $postData            = filter_input_array(INPUT_POST);
        $errorCounter        = 0;
        if (!current_user_can("manage_categories")) {
            $error = esc_html__("You have not permission to update folder", 'folders');
            $errorCounter++;
        } else if (!isset($postData['term_id']) || empty($postData['term_id'])) {
            $error = esc_html__("Unable to rename folder, Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!isset($postData['name']) || empty($postData['name'])) {
            $error = esc_html__("Folder name can no be empty", 'folders');
            $errorCounter++;
        } else if (!isset($postData['type']) || empty($postData['type'])) {
            $error = esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $error = esc_html__("Unable to rename folder, Your request is not valid", 'folders');
            $errorCounter++;
        } else {
            $term_id = self::sanitize_options($postData['term_id']);
            if (!wp_verify_nonce($postData['nonce'], 'wcp_folder_term_'.$term_id)) {
                $error = esc_html__("Unable to rename folder, Your request is not valid", 'folders');
                $errorCounter++;
            }
        }//end if

        if ($errorCounter == 0) {
            $type        = self::sanitize_options($postData['type']);
            $folder_type = self::get_custom_post_type($type);
            $name        = self::sanitize_options($postData['name']);
            $term_id     = self::sanitize_options($postData['term_id']);
            $result      = wp_update_term(
                $term_id,
                $folder_type,
                ['name' => $name]
            );
            if (!empty($result)) {
                $term_nonce         = wp_create_nonce('wcp_folder_term_'.$result['term_id']);
                $response['id']     = $result['term_id'];
                $response['slug']   = $result['slug'];
                $response['status'] = 1;
                $response['term_title'] = $postData['name'];
                $response['nonce']      = $term_nonce;
            } else {
                $response['message'] = esc_html__("Unable to rename folder", 'folders');
            }
        } else {
            $response['error']   = 1;
            $response['message'] = $error;
        }//end if

        echo wp_json_encode($response);
        wp_die();

    }//end wcp_update_folder()


    /**
     * Create slug from string
     *
     * @since  1.0.0
     * @access public
     * @return $slug
     */
    public static function create_slug_from_string($str)
    {
        $a = [
            'À',
            'Á',
            'Â',
            'Ã',
            'Ä',
            'Å',
            'Æ',
            'Ç',
            'È',
            'É',
            'Ê',
            'Ë',
            'Ì',
            'Í',
            'Î',
            'Ï',
            'Ð',
            'Ñ',
            'Ò',
            'Ó',
            'Ô',
            'Õ',
            'Ö',
            'Ø',
            'Ù',
            'Ú',
            'Û',
            'Ü',
            'Ý',
            'ß',
            'à',
            'á',
            'â',
            'ã',
            'ä',
            'å',
            'æ',
            'ç',
            'è',
            'é',
            'ê',
            'ë',
            'ì',
            'í',
            'î',
            'ï',
            'ñ',
            'ò',
            'ó',
            'ô',
            'õ',
            'ö',
            'ø',
            'ù',
            'ú',
            'û',
            'ü',
            'ý',
            'ÿ',
            'A',
            'a',
            'A',
            'a',
            'A',
            'a',
            'C',
            'c',
            'C',
            'c',
            'C',
            'c',
            'C',
            'c',
            'D',
            'd',
            'Ð',
            'd',
            'E',
            'e',
            'E',
            'e',
            'E',
            'e',
            'E',
            'e',
            'E',
            'e',
            'G',
            'g',
            'G',
            'g',
            'G',
            'g',
            'G',
            'g',
            'H',
            'h',
            'H',
            'h',
            'I',
            'i',
            'I',
            'i',
            'I',
            'i',
            'I',
            'i',
            'I',
            'i',
            '?',
            '?',
            'J',
            'j',
            'K',
            'k',
            'L',
            'l',
            'L',
            'l',
            'L',
            'l',
            '?',
            '?',
            'L',
            'l',
            'N',
            'n',
            'N',
            'n',
            'N',
            'n',
            '?',
            'O',
            'o',
            'O',
            'o',
            'O',
            'o',
            'Œ',
            'œ',
            'R',
            'r',
            'R',
            'r',
            'R',
            'r',
            'S',
            's',
            'S',
            's',
            'S',
            's',
            'Š',
            'š',
            'T',
            't',
            'T',
            't',
            'T',
            't',
            'U',
            'u',
            'U',
            'u',
            'U',
            'u',
            'U',
            'u',
            'U',
            'u',
            'U',
            'u',
            'W',
            'w',
            'Y',
            'y',
            'Ÿ',
            'Z',
            'z',
            'Z',
            'z',
            'Ž',
            'ž',
            '?',
            'ƒ',
            'O',
            'o',
            'U',
            'u',
            'A',
            'a',
            'I',
            'i',
            'O',
            'o',
            'U',
            'u',
            'U',
            'u',
            'U',
            'u',
            'U',
            'u',
            'U',
            'u',
            '?',
            '?',
            '?',
            '?',
            '?',
            '?',
        ];
        $b = [
            'A',
            'A',
            'A',
            'A',
            'A',
            'A',
            'AE',
            'C',
            'E',
            'E',
            'E',
            'E',
            'I',
            'I',
            'I',
            'I',
            'D',
            'N',
            'O',
            'O',
            'O',
            'O',
            'O',
            'O',
            'U',
            'U',
            'U',
            'U',
            'Y',
            's',
            'a',
            'a',
            'a',
            'a',
            'a',
            'a',
            'ae',
            'c',
            'e',
            'e',
            'e',
            'e',
            'i',
            'i',
            'i',
            'i',
            'n',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'u',
            'u',
            'u',
            'u',
            'y',
            'y',
            'A',
            'a',
            'A',
            'a',
            'A',
            'a',
            'C',
            'c',
            'C',
            'c',
            'C',
            'c',
            'C',
            'c',
            'D',
            'd',
            'D',
            'd',
            'E',
            'e',
            'E',
            'e',
            'E',
            'e',
            'E',
            'e',
            'E',
            'e',
            'G',
            'g',
            'G',
            'g',
            'G',
            'g',
            'G',
            'g',
            'H',
            'h',
            'H',
            'h',
            'I',
            'i',
            'I',
            'i',
            'I',
            'i',
            'I',
            'i',
            'I',
            'i',
            'IJ',
            'ij',
            'J',
            'j',
            'K',
            'k',
            'L',
            'l',
            'L',
            'l',
            'L',
            'l',
            'L',
            'l',
            'l',
            'l',
            'N',
            'n',
            'N',
            'n',
            'N',
            'n',
            'n',
            'O',
            'o',
            'O',
            'o',
            'O',
            'o',
            'OE',
            'oe',
            'R',
            'r',
            'R',
            'r',
            'R',
            'r',
            'S',
            's',
            'S',
            's',
            'S',
            's',
            'S',
            's',
            'T',
            't',
            'T',
            't',
            'T',
            't',
            'U',
            'u',
            'U',
            'u',
            'U',
            'u',
            'U',
            'u',
            'U',
            'u',
            'U',
            'u',
            'W',
            'w',
            'Y',
            'y',
            'Y',
            'Z',
            'z',
            'Z',
            'z',
            'Z',
            'z',
            's',
            'f',
            'O',
            'o',
            'U',
            'u',
            'A',
            'a',
            'I',
            'i',
            'O',
            'o',
            'U',
            'u',
            'U',
            'u',
            'U',
            'u',
            'U',
            'u',
            'U',
            'u',
            'A',
            'a',
            'AE',
            'ae',
            'O',
            'o',
        ];
        return strtolower(preg_replace(['/[^a-zA-Z0-9 -]/', '/[ -]+/', '/^-|-$/'], ['', '-', ''], str_replace($a, $b, $str)));

    }//end create_slug_from_string()


    /**
     * Sanitize input options
     *
     * @since  1.0.0
     * @access public
     * @return $value
     */
    public static function sanitize_options($value, $type="")
    {
        $value = stripslashes($value);
        if ($type == "int") {
            $value = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
        } else if ($type == "email") {
            $value = filter_var($value, FILTER_SANITIZE_EMAIL);
        } else {
            $value = sanitize_text_field($value);
        }

        return $value;

    }//end sanitize_options()


    /**
     * Add new folder
     *
     * @since  1.0.0
     * @access public
     * @return $response
     */
    public function wcp_add_new_folder()
    {
        $response            = [];
        $response['status']  = 0;
        $response['error']   = 0;
        $response['login']   = 1;
        $response['data']    = [];
        $response['message'] = "";
        $response['message2'] = "";
        $postData     = filter_input_array(INPUT_POST);
        $errorCounter = 0;
        $error        = esc_html__("Your request is not valid", 'folders');
        if (!current_user_can("manage_categories")) {
            $error = esc_html__("You have not permission to add folder", 'folders');
            $errorCounter++;
        } else if (!isset($postData['name']) || empty($postData['name'])) {
            $error = esc_html__("Folder name can no be empty", 'folders');
            $errorCounter++;
        } else if (!isset($postData['type']) || empty($postData['type'])) {
            $error = esc_html__("Your request is not valid", 'folders');
            $errorCounter++;
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['login'] = 0;
            $error = esc_html__("Unable to create folder, Your request is not valid", 'folders');
            $errorCounter++;
        } else {
            $type = self::sanitize_options($postData['type']);
            if (!wp_verify_nonce($postData['nonce'], 'wcp_folder_nonce_'.$type)) {
                $response['login'] = 0;
                $error = esc_html__("Unable to create folder, Your request is not valid", 'folders');
                $errorCounter++;
            }
        }//end if

        if ($errorCounter == 0) {
            $parent      = isset($postData['parent_id']) && !empty($postData['parent_id']) ? $postData['parent_id'] : 0;
            $parent      = self::sanitize_options($parent);
            $type        = self::sanitize_options($postData['type']);
            $folder_type = self::get_custom_post_type($type);
            $term_name   = self::sanitize_options($postData['name']);
            $term        = term_exists($term_name, $folder_type, $parent);
            $user_id     = get_current_user_id();
            if (!empty($term) && isset($term['term_id']) && !empty($term['term_id'])) {
                $term_user = get_term_meta($term['term_id'], "created_by", true);
                if ($term_user == $user_id) {
                    $response['error']   = 1;
                    $response['message'] = esc_html__("Folder name already exists", 'folders');
                    echo wp_json_encode($response);
                    wp_die();
                }
            }

            $folders      = $postData['name'];
            $folders      = explode(",", $folders);
            $foldersArray = [];
            //$order        = isset($postData['order']) ? $postData['order'] : 0;
            //$order        = self::sanitize_options($order);

            $is_active       = 1;
            $created_folders = 0;

            foreach ($folders as $key => $folder) {
                $term = term_exists($folder, $folder_type, $parent);
                if (!empty($term) && isset($term['term_id']) && !empty($term['term_id'])) {
                    $term_user = get_term_meta($term['term_id'], "created_by", true);
                    if ($term_user == $user_id) {
                        continue;
                    }
                }

                $folder = trim($folder);
                $slug   = self::create_slug_from_string($folder)."-".time()."-".$user_id;

                $result = wp_insert_term(
                    urldecode($folder),
                    // the term
                    $folder_type,
                    // the taxonomy
                    [
                        'parent' => $parent,
                        'slug'   => $slug,
                    ]
                );

                if (!empty($result)) {
                    $created_folders++;
                    $response['id']     = $result['term_id'];
                    $response['status'] = 1;
                    $term       = get_term($result['term_id'], $folder_type);
                    $order      = $key+1;
                    $term_nonce = wp_create_nonce('wcp_folder_term_'.$term->term_id);

                    $folder_item = [];
                    $folder_item['parent_id']    = $parent;
                    $folder_item['slug']         = $term->slug;
                    $folder_item['nonce']        = $term_nonce;
                    $folder_item['term_id']      = $result['term_id'];
                    $folder_item['title']        = $folder;
                    $folder_item['parent_id']    = empty($postData['parent_id']) ? "0" : $postData['parent_id'];
                    $folder_item['is_sticky']    = 0;
                    $folder_item['is_high']      = 0;
                    $folder_item['is_locked']    = 0;
                    $folder_item['folder_count'] = 0;
                    $folder_item['has_color']    ='';

                    add_term_meta($result['term_id'], "created_by", $user_id);

                    update_term_meta($result['term_id'], "wcp_custom_order", $order);
                    if ($parent != 0) {
                        update_term_meta($parent, "is_active", 1);
                    }

                    if (isset($postData['is_duplicate']) && $postData['is_duplicate'] == true) {
                        if (isset($postData['duplicate_from']) && !empty($postData['duplicate_from'])) {
                            $term_id = $postData['duplicate_from'];

                            $term_data = get_term($term_id, $folder_type);
                            if (!empty($term_data)) {

                                $folder_info    = get_term_meta($term_id, "folder_info", true);
                                $folder_info = shortcode_atts([
                                    'is_sticky' => 0,
                                    'is_high'   => 0,
                                    'is_locked' => 0,
                                    'is_active' => 0,
                                    'has_color' => ''
                                ], $folder_info);

                                $folder_item['is_active'] = intval($folder_info['is_active']);
                                $folder_item['is_high']   = intval($folder_info['is_high']);
                                $folder_item['is_locked'] = intval($folder_info['is_locked']);
                                $folder_item['is_sticky'] = intval($folder_info['is_sticky']);
                                $folder_item['has_color'] = $folder_info['has_color'];

                                add_term_meta($term->term_id, "folder_info", $folder_info);

                                $postArray = get_posts(
                                    [
                                        'posts_per_page' => -1,
                                        'post_type'      => $type,
                                        'tax_query'      => [
                                            [
                                                'taxonomy' => $folder_type,
                                                'field'    => 'term_id',
                                                'terms'    => $term_id,
                                            ],
                                        ],
                                    ]
                                );
                                if (!empty($postArray)) {
                                    foreach ($postArray as $p) {
                                        wp_set_post_terms($p->ID, $term->term_id, $folder_type, true);
                                    }

                                    $folder_item['folder_count'] = count($postArray);
                                }
                            }//end if
                        }//end if
                    }//end if

                    $foldersArray[] = $folder_item;
                }//end if

                $folders = $postData['folders'];
                if(is_array($folders) && count($folders) > 0) {
                    foreach($folders as $folder) {
                        $order++;
                        update_term_meta($folder, "wcp_custom_order", $order);
                    }
                }

                if (!empty($foldersArray)) {
                    $response['is_key_active'] = $is_active;
                    $response['folders']       = $created_folders;
                    $response['parent_id']     = empty($parent) ? "#" : $parent;

                    $response['status'] = 1;
                    $response['data']   = $foldersArray;
                }
            }//end foreach
        } else {
            $response['error']   = 1;
            $response['message'] = $error;
        }//end if

        echo wp_json_encode($response);
        wp_die();

    }//end wcp_add_new_folder()


    /**
     * Check is folder active for post, page, attachment
     *
     * @since  1.0.0
     * @access public
     * @return $post_type
     */
    public function is_for_this_post_type($post_type)
    {
        $post_types = get_option('folders_settings');
        $post_types = is_array($post_types) ? $post_types : [];
        return in_array($post_type, $post_types);

    }//end is_for_this_post_type()


    /**
     * Check is folder active for current screen
     *
     * @since  1.0.0
     * @access public
     * @return $status
     */
    public function is_active_for_screen()
    {
        global $typenow, $current_screen;

        $postData = filter_input_array(INPUT_POST);

        global $current_screen;

        if (self::is_for_this_post_type($typenow) && ('edit' == $current_screen->base || 'upload' == $current_screen->base)) {
            return true;
        }

        $post_types = get_option('folders_settings');
        $post_types = is_array($post_types) ? $post_types : [];

        if (empty($typenow) && (isset($current_screen->base) && 'upload' == $current_screen->base)) {
            $typenow = "attachment";
            if (self::is_for_this_post_type($typenow)) {
                return true;
            }
        }

        return false;

    }//end is_active_for_screen()


    /**
     * Check is post update screen
     *
     * @since  1.0.0
     * @access public
     * @return $status
     */
    public function is_add_update_screen()
    {
        global $current_screen;
        $current_type = $current_screen->base;
        $action       = $current_screen->action;
        $post_types   = get_option('folders_settings');
        $post_types   = is_array($post_types) ? $post_types : [];
        global $typenow;
        if (in_array($current_type, $post_types) && in_array($action, ["add", ""])) {
            $license_data = self::get_license_key_data();

            $is_active = 1;
            $folders   = -1;
            $response['folders']       = $folders;
            $response['is_key_active'] = $is_active;
        }

    }//end is_add_update_screen()


    /**
     * Get folder type for page, post, attachment
     *
     * @since  1.0.0
     * @access public
     * @return $status
     */
    public static function get_custom_post_type($post_type)
    {
        if ($post_type == "post") {
            return "post_folder";
        } else if ($post_type == "page") {
            return "folder";
        } else if ($post_type == "attachment") {
            return "media_folder";
        }

        return $post_type.'_folder';

    }//end get_custom_post_type()

    /**
     * Combine all folders setting to one meta
     *
     * @since  1.0.0
     * @access public
     */
    public function migrate_folders_settings() {
        global $wpdb;
        $status = get_option('folders_term_meta_migrated');

        /* Checking for Thrive data */
        if($status == "yes") {
            $status = get_option('folders_checked_for_thrive_conflict');
            if($status != "yes") {
                add_option("folders_checked_for_thrive_conflict", "yes");
                $tbl_termmeta = $wpdb->prefix . 'termmeta';

                $query = "SELECT meta_id, term_id, meta_key, meta_value
                    FROM {$tbl_termmeta}
                    WHERE meta_key = 'is_locked'";

                $results = $wpdb->get_results($query);

                if (!empty($results)) {
                    foreach ($results as $result) {
                        $folder_data = get_term_meta($result->term_id, 'folder_info', true);
                        if(isset($folder_data['is_active'])) {
                            add_term_meta($result->term_id, 'is_active', $folder_data['is_active'], true);
                        }
                    }
                }
            }
        }
        if($status != 'yes') {
            add_option("folders_term_meta_migrated", "yes");
            add_option("folders_checked_for_thrive_conflict", "yes");
            $tbl_termmeta = $wpdb->prefix . 'termmeta';

            $query = "SELECT meta_id, term_id, meta_key, meta_value
                    FROM {$tbl_termmeta}
                    WHERE meta_key = 'is_locked'";

            $results = $wpdb->get_results($query);

            $folder_setting = [];
            if (!empty($results)) {
                foreach ($results as $result) {
                    $folder_setting[$result->term_id]['is_locked'] = $result->meta_value;
                    delete_term_meta($result->term_id, 'is_locked');
                }
            }
            $query = "SELECT meta_id, term_id, meta_key, meta_value
                    FROM {$tbl_termmeta}
                    WHERE meta_key = 'is_folder_sticky'";

            $results = $wpdb->get_results($query);

            if (!empty($results)) {
                foreach ($results as $result) {
                    $folder_setting[$result->term_id]['is_sticky'] = $result->meta_value;
                    delete_term_meta($result->term_id, 'is_folder_sticky');
                }
            }
            $query = "SELECT meta_id, term_id, meta_key, meta_value
                    FROM {$tbl_termmeta}
                    WHERE meta_key = 'is_highlighted'";

            $results = $wpdb->get_results($query);

            if (!empty($results)) {
                foreach ($results as $result) {
                    $folder_setting[$result->term_id]['is_high'] = $result->meta_value;
                    delete_term_meta($result->term_id, 'is_highlighted');
                }
            }
            $query = "SELECT meta_id, term_id, meta_key, meta_value
                    FROM {$tbl_termmeta}
                    WHERE meta_key = 'is_active'";

            $results = $wpdb->get_results($query);

            if (!empty($results)) {
                foreach ($results as $result) {
                    $folder_setting[$result->term_id]['is_active'] = $result->meta_value;
                    //delete_term_meta($result->term_id, 'is_active');
                }
            }

            if (!empty($folder_setting)) {
                foreach ($folder_setting as $term_id => $setting) {
                    update_term_meta($term_id, 'folder_info', $setting);
                }
            }
        }
    }

    /**
     * Add folders data to footer
     *
     * @since  1.0.0
     * @access public
     * @return $html
     */
    public function admin_footer()
    {
        if (self::is_active_for_screen()) {
            $this->migrate_folders_settings();
            global $typenow;

            self::set_default_values_if_not_exists();

            $ttpsts = self::get_ttlpst($typenow);

            $ttemp = self::get_tempt_posts($typenow);

            $folder_type = self::get_custom_post_type($typenow);
            // Do not change: Free/Pro Class name change
            $tree_data     = WCP_Tree::get_full_tree_data($folder_type);
            $terms_data    = $tree_data['string'];
            $sticky_string = $tree_data['sticky_string'];
            $terms_html    = WCP_Tree::get_option_data_for_select($folder_type);
            $form_html     = WCP_Forms::get_form_html($terms_html);
            include_once dirname(dirname(__FILE__)).WCP_DS."/templates".WCP_DS."admin".WCP_DS."admin-content.php";
        }

        global $pagenow;
        if ('plugins.php' !== $pagenow) {
        } else {
            if (current_user_can('manage_options')) {
                include_once dirname(dirname(__FILE__)).WCP_DS."/templates".WCP_DS."admin".WCP_DS."folder-deactivate-form.php";
            }
        }

    }//end admin_footer()


    /**
     * Get total posts
     *
     * @since  1.0.0
     * @access public
     * @return $item_count
     */
    public function get_ttlpst($post_type="")
    {
        global $typenow;
        if (empty($post_type)) {
            $post_type = $typenow;
        }
        $item_count = null;
        if(has_filter("premio_folder_all_categorized_items")) {
            $item_count = apply_filters("premio_folder_all_categorized_items", $post_type);
        }
        if($item_count === null) {
            if ($post_type == "attachment") {
                global $wpdb;

                $select = "SELECT COUNT(ID) FROM ".$wpdb->posts." as P ";

                $where = ["post_type = 'attachment' "];
                $where[] = "(post_status = 'inherit' OR post_status = 'private')";

                $join = apply_filters( 'folders_count_join_query', "" );
                $where = apply_filters( 'folders_count_where_query', $where );

                $query = $select . $join . " WHERE ".implode( ' AND ', $where );

                $item_count = $wpdb->get_var($query);
            } else {
                $item_count = wp_count_posts($post_type)->publish + wp_count_posts($post_type)->draft + wp_count_posts($post_type)->future + wp_count_posts($post_type)->private + wp_count_posts($post_type)->pending;
            }
        }
        return $item_count;
    }//end get_ttlpst()

    /**
     * Condition for Media hooks for Elementor, BuddyPress
     *
     * @since  2.9
     * @access public
     */
    public function folders_count_where_query($where) {
        global $wpdb;
        if(class_exists( 'Better_Messages' )) {
            $where[] = " BMPM.post_id IS NULL ";
        }
        if(class_exists( 'buddypress' )) {
            $where[] = " bb_mt1.post_id IS NULL ";
            $where[] = " bb_mt2.post_id IS NULL ";
            $where[] = " bb_mt3.post_id IS NULL ";
        }
        if ( function_exists( '_is_elementor_installed' ) ) {
            $where[] = " ELPM.post_id IS NULL ";
        }
        if(class_exists("Web_Stories_Compatibility")) {
            $query = "SELECT t.term_id
                FROM ".$wpdb->terms." AS t
                INNER JOIN ".$wpdb->term_taxonomy." AS tt
                ON t.term_id = tt.term_id
                WHERE tt.taxonomy IN ('web_story_media_source')
                AND t.slug IN ('poster-generation', 'source-video', 'source-image', 'page-template')";
            $results = $wpdb->get_results($query);
            if(!empty($results)) {
                $termsIds = [];
                foreach($results as $result) {
                    if(isset($result->term_id) && !empty($result->term_id)) {
                        $termsIds[] = $result->term_id;
                    }
                }

                if(!empty($termsIds)) {
                    $termsIds = implode(",", $termsIds);
                    $where[] = "(P.ID NOT IN (SELECT object_id
                                    FROM ".$wpdb->term_relationships."
                                    WHERE term_taxonomy_id IN ($termsIds)))";
                }
            }
        }
        if(class_exists("youzify_media")) {
            $term = get_term_by( 'slug', 'youzify_media', 'category' );
            if(isset($term->term_id)) {
                $where[] = "( P.ID NOT IN (
                    SELECT object_id
                    FROM ".$wpdb->term_relationships."
                    WHERE term_taxonomy_id IN (".$term->term_id.") ) )";
            }
        }

        /* W3 Cache */
        if(defined("W3TC")) {
            $where[] = "( WTCPM.post_id IS NULL )";
        }

        /* Jetbrain Video */
        if(defined("JETPACK_VIDEOPRESS_NAME")) {
            $where[] = " JBV.post_id IS NULL ";
        }
        return $where;
    }

    /**
     * Folders Query for Media
     *
     * @since  2.9
     * @access public
     */
    public function folders_count_join_query($join) {
        global $wpdb;
        if(class_exists( 'Better_Messages' )) {
            $join .= " LEFT JOIN " . $wpdb->postmeta . " AS BMPM
                        ON ( P.ID = BMPM.post_id
                        AND BMPM.meta_key = 'bp-better-messages-attachment') ";
        }
        if(class_exists( 'buddypress' )) {
            $join .= " LEFT JOIN ".$wpdb->postmeta." AS bb_mt1
                        ON (P.ID = bb_mt1.post_id
                        AND bb_mt1.meta_key = 'bp_media_upload' ) ";

            $join .= " LEFT JOIN ".$wpdb->postmeta." AS bb_mt2
                        ON (P.ID = bb_mt2.post_id
                        AND bb_mt2.meta_key = 'bp_document_upload' ) ";

            $join .= " LEFT JOIN ".$wpdb->postmeta." AS bb_mt3
                        ON (P.ID = bb_mt3.post_id
                        AND bb_mt3.meta_key = 'bp_video_upload' ) ";

        }
        if ( function_exists( '_is_elementor_installed' ) ) {
            $join .= " LEFT JOIN ".$wpdb->postmeta." AS ELPM
                        ON ( P.ID = ELPM.post_id
                        AND ELPM.meta_key = '_elementor_is_screenshot') ";
        }
        if(defined("W3TC")) {
            $join .= "LEFT JOIN ".$wpdb->postmeta." AS WTCPM
                ON ( P.ID = WTCPM.post_id
                AND WTCPM.meta_key = 'w3tc_imageservice_file' )";
        }
        if(defined("JETPACK_VIDEOPRESS_NAME")) {
            $join .= " LEFT JOIN ".$wpdb->postmeta." AS JBV
                        ON ( P.ID = JBV.post_id
                        AND JBV.meta_key = 'videopress_poster_image') ";
        }
        return $join;
    }

    /**
     * Autoload folders librarires
     *
     * @since  1.0.0
     * @access public
     * @return $files
     */
    public function autoload()
    {
        $files = [
            'WCP_Tree_View'       => WCP_DS."includes".WCP_DS."tree.class.php",
            'WCP_Form_View'       => WCP_DS."includes".WCP_DS."form.class.php",
            'WCP_Folder_WPML'     => WCP_DS."includes".WCP_DS."class-wpml.php",
            'WCP_Folder_PolyLang' => WCP_DS."includes".WCP_DS."class-polylang.php",
            'Folders_Notifications' => WCP_DS."includes".WCP_DS."notifications.class.php",
            'Folders_Import_Export' => WCP_DS."includes".WCP_DS."import.export.class.php",
        ];

        foreach ($files as $file) {
            if (file_exists(dirname(dirname(__FILE__)).$file)) {
                include_once dirname(dirname(__FILE__)).$file;
            }
        }

    }//end autoload()


    /**
     * Create folder taxonomies
     *
     * @since  1.0.0
     * @access public
     * @return $taxonomies
     */
    public function create_folder_terms()
    {
        $options           = get_option('folders_settings');
        $options           = is_array($options) ? $options : [];
        $old_plugin_status = 0;
        $posts = [];
        if (!empty($options)) {
            foreach ($options as $option) {
                if (!(strpos($option, 'folder4') === false) && $old_plugin_status == 0) {
                    $old_plugin_status = 1;
                }

                if (in_array($option, ["page", "post", "attachment"])) {
                    $posts[] = str_replace("folder4", "", $option);
                } else {
                    $posts[] = $option;
                }
            }

            if (!empty($posts)) {
                update_option('folders_settings', $posts);
            }
        }

        if ($old_plugin_status == 1) {
            update_option("folders_show_in_menu", "on");
            $old_plugin_var = get_option("folder_old_plugin_status");
            if (empty($old_plugin_var) || $old_plugin_var == null) {
                update_option("folder_old_plugin_status", "1");
            }
        }

        $posts = get_option('folders_settings');
        if (!empty($posts)) {
            foreach ($posts as $post_type) {
                $labels = [
                    'name'          => esc_html__('Folders', 'folders'),
                    'singular_name' => esc_html__('Folder', 'folders'),
                    'all_items'     => esc_html__('All Folders', 'folders'),
                    'edit_item'     => esc_html__('Edit Folder', 'folders'),
                    'update_item'   => esc_html__('Update Folder', 'folders'),
                    'add_new_item'  => esc_html__('Add New Folder', 'folders'),
                    'new_item_name' => esc_html__('Add folder name', 'folders'),
                    'menu_name'     => esc_html__('Folders', 'folders'),
                    'search_items'  => esc_html__('Search Folders', 'folders'),
                    'parent_item'   => esc_html__('Parent Folder', 'folders'),
                ];

                $args = [
                    'label'                 => esc_html__('Folder', 'folders'),
                    'labels'                => $labels,
                    'show_tagcloud'         => false,
                    'hierarchical'          => true,
                    'public'                => false,
                    'show_ui'               => true,
                    'show_in_menu'          => false,
                    'show_in_rest'          => true,
                    'show_admin_column'     => true,
                    'update_count_callback' => '_update_post_term_count',
                // 'update_count_callback' => '_update_generic_term_count',
                    'query_var'             => true,
                    'rewrite'               => false,
                    'capabilities'          => [
                        'manage_terms' => 'manage_categories',
                        'edit_terms'   => 'manage_categories',
                        'delete_terms' => 'manage_categories',
                        'assign_terms' => 'manage_categories',
                    ],
                ];

                $folder_post_type = self::get_custom_post_type($post_type);

                register_taxonomy(
                    $folder_post_type,
                    $post_type,
                    $args
                );
            }//end foreach
        }//end if

        $postData = filter_input_array(INPUT_POST);

        if (current_user_can("manage_categories") && isset($postData['folder_nonce'])) {
            if (wp_verify_nonce($postData['folder_nonce'], "folder_settings")) {
                if (isset($postData['folders_show_in_menu']) && !empty($postData['folders_show_in_menu'])) {
                    $show_menu = "off";
                    if ($postData['folders_show_in_menu'] == "on") {
                        $show_menu = "on";
                    }

                    update_option("folders_show_in_menu", $show_menu);
                }

                if (isset($postData['folders_settings1'])) {
                    $posts = [];
                    if (isset($postData['folders_settings']) && is_array($postData['folders_settings'])) {
                        foreach ($postData['folders_settings'] as $key => $val) {
                            $posts[] = $val;
                        }
                    }

                    update_option("folders_settings", $posts);
                }

                $postData = filter_input_array(INPUT_POST);

                if (isset($postData['folders_settings1'])) {
                    $posts = [];
                    if (isset($postData['default_folders']) && is_array($postData['default_folders'])) {
                        foreach ($postData['default_folders'] as $key => $val) {
                            $posts[$key] = $val;
                        }
                    }

                    update_option("default_folders", $posts);
                }

                if (isset($postData['customize_folders'])) {
                    $posts = [];
                    if (isset($postData['customize_folders']) && is_array($postData['customize_folders'])) {
                        foreach ($postData['customize_folders'] as $key => $val) {
                            $posts[$key] = $val;
                        }
                    }

                    update_option("customize_folders", $posts);
                }

                $setting_page = $this->getFolderSettingsURL();
                if (!empty($setting_page)) {
                    $page         = filter_input(INPUT_POST, 'tab_page');
                    $type         = filter_input(INPUT_GET, 'setting_page');
                    $type         = empty($type) ? "" : "&setting_page=".esc_attr($type);
                    $setting_page = $setting_page.$type;
                    if (!empty($page)) {
                        $setting_page .= "&setting_page=".esc_attr($page);
                    }

                    wp_redirect($setting_page."&note=1");
                    exit;
                } else {
                    $folder_page = filter_input(INPUT_POST, 'folder_page');
                    if (!empty($folder_page)) {
                        wp_redirect($folder_page);
                        exit;
                    }
                }
            }//end if
        }//end if

        // $old_version = get_option("folder_old_plugin_status");
        // if($old_version !== false && $old_version == 1) {
        // $tlfs = get_option("folder_old_plugin_folder_status");
        // if($tlfs === false) {
        // $total = self::ttl_fldrs();
        // if($total <= 10) {
        // $total = 10;
        // };
        // update_option("folder_old_plugin_folder_status", $total);
        // self::$folders = $total;
        // } else {
        // self::$folders = $tlfs;
        // }
        // }
        //
        // $tlfs = get_option("folder_old_plugin_folder_status");
        // if($tlfs === false) {
        // self::$folders = 10;
        // } else {
        // self::$folders = $tlfs;
        // }

    }//end create_folder_terms()


    /**
     * Search for data from list
     *
     * @since  1.0.0
     * @access public
     * @return $key
     */
    function searchForId($id, $menu)
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
     * Create setting menu for folders
     *
     * @since  1.0.0
     * @access public
     */
    function create_menu_for_folders()
    {
        global $menu;
        self::check_and_set_post_type();

        $folder_types = get_option("folders_settings");
        if (empty($folder_types)) {
            return;
        }

        foreach ($folder_types as $type) {
            $itemKey = self::searchForId($type, $menu);
            switch (true) {
            case ($type == 'attachment'):
                $itemKey = 10;
                $edit    = 'upload.php';
                break;
            case ($type === 'post'):
                $edit    = 'edit.php';
                $itemKey = 5;
                break;
            default:
                $edit = 'edit.php';
                break;
            }

            $folder = $type == 'attachment' ? 'media' : $type;
            $upper  = $type == 'attachment' ? 'Media' : ucwords(str_replace(['-', '_'], ' ', $type));
            if ($type == 'page') {
                $tax_slug = 'folder';
            } else {
                $tax_slug = $folder.'_folder';
            }

            $hide_empty = true;
            if ($type == 'attachment') {
                $hide_empty = false;
                add_menu_page('Media Folders', 'Media Folders', 'publish_pages', "{$edit}?post_type=attachment&media_folder=", false, 'dashicons-portfolio', "{$itemKey}.5");
            } else {
                add_menu_page($upper.' Folders', "{$upper} Folders", 'publish_pages', "{$edit}?post_type={$type}&type=folder", false, 'dashicons-portfolio', "{$itemKey}.5");
            }

            $terms = get_terms(
                [
                    'taxonomy'     => $tax_slug,
                    'hide_empty'   => $hide_empty,
                    'parent'       => 0,
                    'orderby'      => 'meta_value_num',
                    'order'        => 'ASC',
                    'hierarchical' => false,
                    'meta_query'   => [
                        [
                            'key'  => 'wcp_custom_order',
                            'type' => 'NUMERIC',
                        ],
                    ],
                ]
            );

            if ($terms) {
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
        }//end foreach

    }//end create_menu_for_folders()


    /**
     * Add Folder Styles
     *
     * @since  1.0.0
     * @access public
     */
    function folders_admin_styles($page)
    {
//        echo $page; die;
        if($page == "folders-settings_page_folders-upgrade-to-pro" || ($page == "settings_page_wcp_folders_settings" && isset($_GET['setting_page']) && $_GET['setting_page'] == 'upgrade-to-pro')) {
            wp_enqueue_style('folder-pricing-table', plugin_dir_url(dirname(__FILE__)).'assets/css/pricing-table.css', [], WCP_FOLDER_VERSION);
            $queryArgs = [
                'family' => 'Poppins:wght@400;500;600;700&display=swap',
                'subset' => 'latin,latin-ext',
            ];
            wp_enqueue_style('google-poppins-fonts', add_query_arg($queryArgs, "//fonts.googleapis.com/css2"), [], WCP_FOLDER_VERSION);
        } else if ($page == "toplevel_page_wcp_folders_settings" || $page == "settings_page_wcp_folders_settings") {
            wp_enqueue_style('folder-settings', plugin_dir_url(dirname(__FILE__)).'assets/css/settings.css', [], WCP_FOLDER_VERSION);

            wp_enqueue_style('folders-icon', plugin_dir_url(dirname(__FILE__)).'assets/css/folder-icon.css', [], WCP_FOLDER_VERSION);
            wp_enqueue_style('folders-spectrum', plugin_dir_url(dirname(__FILE__)).'assets/css/spectrum.min.css', [], WCP_FOLDER_VERSION);
        } else if ($page == "folders-settings_page_folders-upgrade-to-pro" || $page == "toplevel_page_wcp_folders_settings" || $page == "settings_page_wcp_folders_settings" || ($page == "settings_page_wcp_folders_settings" && isset($_GET['setting_page']) && $_GET['setting_page'] == "upgrade-to-pro")) {
            wp_enqueue_style('wcp-select2', plugin_dir_url(dirname(__FILE__)).'assets/css/select2.min.css', [], WCP_FOLDER_VERSION);
            if ($page == "folders-settings_page_folders-upgrade-to-pro" || ($page == "settings_page_wcp_folders_settings" && isset($_GET['setting_page']) && $_GET['setting_page'] == "upgrade-to-pro")) {
                wp_enqueue_style('wcp-admin-setting', plugin_dir_url(dirname(__FILE__)).'assets/css/admin-setting.css', [], WCP_FOLDER_VERSION);
            }
        }

        if (self::is_active_for_screen()) {
            wp_enqueue_style('wcp-folders-fa', plugin_dir_url(dirname(__FILE__)).'assets/css/folder-icon.css', [], WCP_FOLDER_VERSION);
            wp_enqueue_style('wcp-folders-admin', plugin_dir_url(dirname(__FILE__)).'assets/css/design.min.css', [], WCP_FOLDER_VERSION);
            wp_enqueue_style('wcp-folders-jstree', plugin_dir_url(dirname(__FILE__)).'assets/css/jstree.min.css', [], WCP_FOLDER_VERSION);
            wp_enqueue_style('folder-overlayscrollbars', WCP_FOLDER_URL.'assets/css/overlayscrollbars.min.css', [], WCP_FOLDER_VERSION);
            wp_enqueue_style('wcp-folders-css', plugin_dir_url(dirname(__FILE__)).'assets/css/folders.min.css', [], WCP_FOLDER_VERSION);
        }

        if ($page == "media_page_folders-media-cleaning") {
            wp_enqueue_style('wcp-folders-media', plugin_dir_url(dirname(__FILE__)).'assets/css/media-clean.css', [], WCP_FOLDER_VERSION);
        }

        wp_register_style('wcp-css-handle', false);
        wp_enqueue_style('wcp-css-handle');
        $css = "
				.wcp-folder-upgrade-button {color: #FF5983; font-weight: bold; display: inline-block;border: solid 1px #FF5983;border-radius: 4px;padding: 0 5px;}
			";
        if (self::is_active_for_screen()) {
            global $typenow;
            $width = get_option("wcp_dynamic_width_for_".$typenow);
            $width = esc_attr($width);
            $width = intval($width);
            $width = empty($width)||!is_numeric($width) ? 280 : $width;
            if ($width > 1200) {
                $width = 280;
            }

            $width          = intval($width);
            $display_status = "wcp_dynamic_display_status_".$typenow;
            $display_status = get_option($display_status);
            if ($display_status != "hide") {
                if (!empty($width) && is_numeric($width)) {
                    if (function_exists('is_rtl') && is_rtl()) {
                        $css .= "html[dir='rtl']  body.wp-admin #wpcontent {padding-right:".($width + 20)."px}";
                        $css .= "html[dir='rtl'] body.wp-admin #wpcontent {padding-left:0px}";
                    } else {
                        $css .= "body.wp-admin #wpcontent {padding-left:".($width + 20)."px}";
                    }
                }
            } else {
                if (function_exists('is_rtl') && is_rtl()) {
                    $css .= "html[dir='rtl']  body.wp-admin #wpcontent {padding-right:20px}";
                    $css .= "html[dir='rtl'] body.wp-admin #wpcontent {padding-left:0px}";
                } else {
                    $css .= "body.wp-admin #wpcontent {padding-left:20px}";
                }
            }

            if (!empty($width) && is_numeric($width)) {
                if ($width > 1200) {
                    $width = 280;
                }

                $width = intval($width);
                $css  .= ".wcp-content {width: ".esc_attr($width)."px}";
            }

            global $typenow;
            $post_type = self::get_custom_post_type($typenow);
            $css      .= "body:not(.woocommerce-page) .wp-list-table th#taxonomy-".esc_attr($post_type)." { width: 130px !important; } @media screen and (max-width: 1180px) { body:not(.woocommerce-page) .wp-list-table th#taxonomy-".esc_attr($post_type)." { width: 90px !important; }} @media screen and (max-width: 960px) { body:not(.woocommerce-page) .wp-list-table th#taxonomy-".esc_attr($post_type)." { width: auto !important; }}";
        }//end if

        wp_add_inline_style('wcp-css-handle', $css);

        if (self::is_active_for_screen()) {
            global $typenow;
            add_filter('views_edit-'.$typenow, [$this, 'wcp_check_for_child_folders']);
        }

    }//end folders_admin_styles()


    /**
     * Get Child folder information
     *
     * @since  1.0.0
     * @access public
     * @return $folders
     */
    function wcp_check_for_child_folders($content)
    {
        $termId = 0;
        global $typenow;
        $post_type = self::get_custom_post_type($typenow);
        $term      = filter_input(INPUT_GET, $post_type);
        if (!empty($term)) {
            $term = get_term_by("slug", $term, $post_type);
            if (!empty($term)) {
                $termId = $term->term_id;
            }
        }

        $terms       = get_terms(
            [
                'taxonomy'              => $post_type,
                'hide_empty'            => false,
                'parent'                => $termId,
                'orderby'               => 'meta_value_num',
                'order'                 => 'ASC',
                'hierarchical'          => false,
                'update_count_callback' => '_update_generic_term_count',
                'meta_query'            => [
                    [
                        'key'  => 'wcp_custom_order',
                        'type' => 'NUMERIC',
                    ],
                ],
            ]
        );
        $optionName  = "wcp_folder_display_status_".$typenow;
        $optionValue = get_option($optionName);
        $class       = (!empty($optionValue) && $optionValue == "hide") ? "" : "active";
        $customize_folders = get_option('customize_folders');
        $show_in_page      = isset($customize_folders['show_in_page']) ? $customize_folders['show_in_page'] : "hide";
        if (empty($show_in_page)) {
            $show_in_page = "hide";
        }

        if ($show_in_page == "show") {
            echo '<div class="tree-structure-content '.esc_attr($class).'"><div class="tree-structure" id="list-folder-'.esc_attr($termId).'" data-id="'.esc_attr($termId).'">';
            echo '<ul>';
            foreach ($terms as $term) {
                ?>
                <li class="grid-view" data-id="<?php echo esc_attr($term->term_id) ?>" id="folder_<?php echo esc_attr($term->term_id) ?>">
                    <div class="folder-item is-folder" data-id="<?php echo esc_attr($term->term_id) ?>">
                        <a title='<?php echo esc_attr($term->name) ?>' id="folder_view_<?php echo esc_attr($term->term_id) ?>" class="folder-view" data-id="<?php echo esc_attr($term->term_id) ?>">
                            <span class="folder item-name"><span id="wcp_folder_text_<?php echo esc_attr($term->term_id) ?>" class="folder-title"><?php echo esc_attr($term->name) ?></span></span>
                        </a>
                    </div>
                </li>
                <?php
            }

            echo '</ul>';
            echo '<div class="clear clearfix"></div>';
            echo '</div>';
            echo '<div class="folders-toggle-button"><span></span></div>';
            echo '</div>';
        }//end if

        $allowedTags = [
            'a'       => [
                'href'   => [],
                'title'  => [],
                'target' => [],
                'class'  => []
            ],
            "span"    => [
                 'class' => []
            ]
        ];

        if(isset($content['mine'])) {
            unset($content['mine']);
        }
        if (!empty($content) && is_array($content)) {
            echo '<ul class="subsubsub">';
            foreach ($content as $k => $v) {
                echo "<li class='".esc_attr($k)."'>".wp_kses($v, $allowedTags)."</li>";
            }

            echo '</ul>';
        }

    }//end wcp_check_for_child_folders()


    /**
     * Add Folder Scripts to admin
     *
     * @since  1.0.0
     * @access public
     * @return $folders
     */
    function folders_admin_scripts($hook)
    {
        if ($hook == "toplevel_page_wcp_folders_settings" || $hook == "settings_page_wcp_folders_settings") {
            wp_enqueue_script('folders-spectrum', plugin_dir_url(dirname(__FILE__)).'assets/js/spectrum.min.js', ['jquery'], WCP_FOLDER_VERSION, true);
        }

        if ($hook == "folders-settings_page_folders-upgrade-to-pro" || $hook == "toplevel_page_wcp_folders_settings" || $hook == "settings_page_wcp_folders_settings" || ($hook == "settings_page_wcp_folders_settings" && isset($_GET['setting_page']) && $_GET['setting_page'] == "upgrade-to-pro")) {
            wp_enqueue_script('folders-select2', plugin_dir_url(dirname(__FILE__)).'assets/js/select2.min.js', ['jquery'], WCP_FOLDER_VERSION, true);
        }

        if ($hook == "folders-settings_page_folders-upgrade-to-pro" || ($hook == "settings_page_wcp_folders_settings" && isset($_GET['setting_page']) && $_GET['setting_page'] == "upgrade-to-pro")) {
            wp_enqueue_script('folders-slick', plugin_dir_url(dirname(__FILE__)).'assets/js/slick.min.js', ['jquery'], WCP_FOLDER_VERSION, true);
        }

        $isShown = get_option("folder_update_message");
        if ($isShown === false) {
            wp_enqueue_script('folders-mailcheck-js', plugin_dir_url(dirname(__FILE__)).'assets/js/mailcheck.js', ['jquery'], WCP_FOLDER_VERSION, true);
        }

        if (self::is_active_for_screen()) {
            remove_filter("terms_clauses", "TO_apply_order_filter");

            global $typenow;
            // Free/Pro Version change
            wp_dequeue_script("jquery-jstree");
            wp_enqueue_script('wcp-folders-jstree', plugin_dir_url(dirname(__FILE__)).'assets/js/jstree.min.js', ['jquery'], WCP_FOLDER_VERSION, true);
            wp_enqueue_script('folders-overlayscrollbars', WCP_FOLDER_URL.'assets/js/jquery.overlayscrollbars.min.js', [], WCP_FOLDER_VERSION, true);
            wp_enqueue_script('wcp-folders-custom', plugin_dir_url(dirname(__FILE__)).'assets/js/folders.min.js', ['jquery', 'jquery-ui-resizable', 'jquery-ui-draggable', 'jquery-ui-droppable', 'jquery-ui-sortable', 'backbone'], WCP_FOLDER_VERSION, true);
            wp_enqueue_script('wcp-jquery-touch', plugin_dir_url(dirname(__FILE__)).'assets/js/jquery.ui.touch-punch.min.js', ['jquery'], WCP_FOLDER_VERSION, true);

            $post_type = self::get_custom_post_type($typenow);

            if(in_array($typenow, ["page", "post", "attachment"])) {
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

            $post_status = "";
            if (isset($_GET['post_status']) && !empty($_GET['post_status'])) {
                $post_status = folders_sanitize_text('post_status', 'get');
            }

            if ($typenow == "attachment") {
                $admin_url = admin_url("upload.php?post_type=attachment&media_folder=");
                global $current_user;
                if (isset($current_user->ID)) {
                    $userMode = get_user_option('media_library_mode', get_current_user_id()) ? get_user_option('media_library_mode', get_current_user_id()) : 'grid';
                    if($userMode == "list") {
                        $admin_url = admin_url("upload.php?post_type=attachment");
                        $search    = filter_input(INPUT_GET, "s");
                        if (!empty($search)) {
                            $admin_url .= "&s=".esc_attr($search);
                        }

                        if (!empty($post_status)) {
                            $admin_url .= "&post_status=".sanitize_text_field($post_status);
                        }

                        if(isset($_REQUEST['paged']) && !empty($_REQUEST['paged']) && is_numeric($_REQUEST['paged'])) {
                            $paged = (int)sanitize_text_field($_REQUEST['paged']);
                            if(!empty($paged)) {
                                $admin_url .= "&paged=".esc_attr($paged);
                            }
                        }

                        $admin_url .= "&".esc_attr($post_type)."=";
                    }
                }
            } else {
                $admin_url = admin_url("edit.php?post_type=".$typenow);
                $search    = filter_input(INPUT_GET, "s");
                if (!empty($search)) {
                    $admin_url .= "&s=".esc_attr($search);
                }

                if (!empty($post_status)) {
                    $admin_url .= "&post_status=".sanitize_text_field($post_status);
                }

                if(isset($_REQUEST['paged']) && !empty($_REQUEST['paged']) && is_numeric($_REQUEST['paged'])) {
                    $paged = (int)sanitize_text_field($_REQUEST['paged']);
                    if(!empty($paged)) {
                        $admin_url .= "&paged=".esc_attr($paged);
                    }
                }

                $admin_url .= "&".esc_attr($post_type)."=";
            }

            $current_url = $admin_url;
            $post_value  = filter_input(INPUT_GET, $post_type);
            if (!empty($post_value)) {
                $current_url .= esc_attr($post_value);
            }

            $is_active = 1;
            $folders   = -1;
            // For free: upgrade URL, for Pro: Register Key URL
            $register_url = $this->getFoldersUpgradeURL();

            $is_rtl = 0;
            if (function_exists('is_rtl') && is_rtl()) {
                $is_rtl = 1;
            }

            $can_manage_folder = current_user_can("manage_categories") ? 1 : 0;
            $width = get_option("wcp_dynamic_width_for_".$typenow);
            $width = intval($width);
            $width = empty($width)||!is_numeric($width) ? 280 : $width;
            if ($width > 1200) {
                $width = 280;
            }

            $post_type         = self::get_custom_post_type($typenow);
            $taxonomy_status   = 0;
            $selected_taxonomy = "";
            $post_value        = filter_input(INPUT_GET, $post_type);
            if (empty($post_value)) {
                $taxonomy_status = 1;
            } else {
                $selected_taxonomy = esc_attr($post_value);

                $term = get_term_by('slug', $selected_taxonomy, $post_type);
                if (!empty($term) && is_object($term)) {
                    $selected_taxonomy = $term->term_id;
                } else {
                    $selected_taxonomy = "";
                }
            }

            $customize_folders = get_option('customize_folders');
            $show_in_page      = isset($customize_folders['show_in_page']) ? $customize_folders['show_in_page'] : "hide";
            if (empty($show_in_page)) {
                $show_in_page = "hide";
            }

            $taxonomies      = self::get_terms_hierarchical($post_type);
            $use_folder_undo = !isset($customize_folders['use_folder_undo']) ? "yes" : $customize_folders['use_folder_undo'];
            $defaultTimeout  = !isset($customize_folders['default_timeout']) ? 5 : intval($customize_folders['default_timeout']);
            if (empty($defaultTimeout) || !is_numeric($defaultTimeout) || $defaultTimeout < 0) {
                $defaultTimeout = 5;
            }

            $defaultTimeout = ($defaultTimeout * 1000);

            $folder_settings = [];
            foreach ($taxonomies as $taxonomy) {
                $folder_info       = get_term_meta($taxonomy->term_id, "folder_info", true);
                $folder_info = shortcode_atts([
                    'is_sticky' => 0,
                    'is_high'   => 0,
                    'is_locked' => 0,
                    'is_active' => 0,
                    'has_color' => ''
                ], $folder_info);

                $folder_settings[] = [
                    'folder_id'    => $taxonomy->term_id,
                    'is_sticky'    => intval($folder_info['is_sticky']),
                    'is_high'      => intval($folder_info['is_high']),
                    'is_locked'    => intval($folder_info['is_locked']),
                    'is_active'    => intval($folder_info['is_active']),
                    'has_color'    => $folder_info['has_color'],
                    'nonce'        => wp_create_nonce('wcp_folder_term_'.$taxonomy->term_id),
                    'is_deleted'   => 0,
                    'slug'         => $taxonomy->slug,
                    'folder_count' => intval($taxonomy->trash_count),
                ];
            }

            $default_folders = get_option("default_folders");
            $default_folder  = "";
            if (isset($default_folders["attachment"])) {
                $default_folder = $default_folders["attachment"];
            }

            $use_shortcuts = !isset($customize_folders['use_shortcuts']) ? "yes" : $customize_folders['use_shortcuts'];

            $currentPage = filter_input(INPUT_GET, 'paged');
            if (!empty($currentPage)) {
                $currentPage = intval($currentPage);
            } else {
                $currentPage = 1;
            }

            $lang = $this->js_strings();

            $hasStars = self::check_for_setting("has_stars", "general");
            $hasChild = self::check_for_setting("has_child", "general");
            $hasChild = empty($hasChild) ? 0 : 1;
            $hasStars = empty($hasStars) ? 0 : 1;
            $colors = $this->selected_colors();

            wp_localize_script(
                'wcp-folders-custom',
                'wcp_settings',
                [
                    'ajax_url'          => admin_url('admin-ajax.php'),
                    'upgrade_url'       => $this->getFoldersUpgradeURL(),
                    'post_type'         => $typenow,
                    'custom_type'       => $post_type,
                    'page_url'          => $admin_url,
                    'current_url'       => $current_url,
                    'ajax_image'        => plugin_dir_url(dirname(__FILE__))."assets/images/ajax-loader.gif",
                    'is_key_active'     => $is_active,
                    'folders'           => $folders,
                    'register_url'      => $register_url,
                    'isRTL'             => $is_rtl,
                    'nonce'             => wp_create_nonce('wcp_folder_nonce_'.$typenow),
                    'can_manage_folder' => $can_manage_folder,
                    'folder_width'      => $width,
                    'taxonomy_status'   => $taxonomy_status,
                    'selected_taxonomy' => $selected_taxonomy,
                    'show_in_page'      => $show_in_page,
                    'svg_file'          => WCP_FOLDER_URL.'assets/images/pin.png',
                    'taxonomies'        => $taxonomies,
                    'folder_settings'   => $folder_settings,
                    'hasStars'          => $hasStars,
                    'hasChildren'       => $hasChild,
                    'currentPage'       => $currentPage,
                    'useFolderUndo'     => $use_folder_undo,
                    'defaultTimeout'    => $defaultTimeout,
                    'default_folder'    => $default_folder,
                    'use_shortcuts'     => $use_shortcuts,
                    'post_status'       => $post_status,
                    'lang'              => $lang,
                    'review_nonce'      => wp_create_nonce("folders_review_box"),
                    'review_box_nonce'  => wp_create_nonce("folders_review_box_message"),
                    'selected_colors'   => $colors
                ]
            );
        } else {
            self::is_add_update_screen();
        }//end if

        if ($hook == "media-new.php") {
            if (self::is_for_this_post_type('attachment') || self::is_for_this_post_type('media')) {
                wp_enqueue_style('folders-media', WCP_FOLDER_URL.'assets/css/new-media.css', [], WCP_FOLDER_VERSION);
                $is_active = 1;
                $folders   = -1;

                $hasStars = self::check_for_setting("has_stars", "general");
                $hasChild = self::check_for_setting("has_child", "general");
                $hasChild = empty($hasChild) ? 0 : 1;
                $hasStars = empty($hasStars) ? 0 : 1;

                wp_enqueue_script('wcp-folders-add-new-media', plugin_dir_url(dirname(__FILE__)).'assets/js/new-media.js', ['jquery'], WCP_FOLDER_VERSION, true);
                wp_localize_script(
                    'wcp-folders-add-new-media',
                    'folders_media_options',
                    [
                        'terms'         => self::get_terms_hierarchical('media_folder'),
                        'taxonomy'      => get_taxonomy('media_folder'),
                        'ajax_url'      => admin_url("admin-ajax.php"),
                        'activate_url'  => $this->getFoldersUpgradeURL(),
                        'nonce'         => wp_create_nonce('wcp_folder_nonce_attachment'),
                        'is_key_active' => $is_active,
                        'folders'       => $folders,
                        'hasStars'      => $hasStars,
                        'hasChildren'   => $hasChild,
                        'lang'          => [
                            "pro_message"   => esc_html__("WordPress doesn't allow you to upload SVG files, upgrade to Folders Pro and experience added SVG file upload support!", "folders"),
                            "activate_key"  => esc_html__("Upgrade now!", "folders"),
                        ],
                    ]
                );
            }//end if
        }//end if

    }//end folders_admin_scripts()


    /**
     * Translated strings for javascript
     *
     * @since  1.0.0
     * @access public
     */
    public function js_strings()
    {
        return [
            "ALL_FOLDERS"                => esc_html__("All Folders", "folders"),
            "NEW_FOLDER"                 => esc_html__("New folder", "folders"),
            "CHANGE_COLOR"               => esc_html__("Icon Color","folders"),
            "REMOVE_COLOR"               => esc_html__("Remove Icon Color","folders"),
            "ADD_CUSTOM_COLORS"          => esc_html__("Add more colors (Pro)", "folders"),
            "NEW_SUB_FOLDER"             => esc_html__("New Sub-folder", "folders"),
            "RENAME"                     => esc_html__("Rename", "folders"),
            "REMOVE_STICKY_FOLDER"       => esc_html__("Remove Sticky Folder", "folders"),
            "STICKY_FOLDER"              => esc_html__("Sticky Folder", "folders"),
            "REMOVE_STAR"                => esc_html__("Remove Star", "folders"),
            "ADD_STAR"                   => esc_html__("Add Star", "folders"),
            "LOCK_FOLDER"                => esc_html__("Lock Folder", "folders"),
            "UNLOCK_FOLDER"              => esc_html__("Unlock Folder", "folders"),
            "DUPLICATE_FOLDER"           => esc_html__("Duplicate folder", "folders"),
            "DOWNLOAD_ZIP"               => esc_html__("Download Zip", "folders"),
            "OPEN_THIS_FOLDER"           => esc_html__("Open this folder by default", "folders"),
            "REMOVE_THIS_FOLDER"         => esc_html__("Remove default folder", "folders"),
            "CUT"                        => esc_html__("Cut", "folders"),
            "COPY"                       => esc_html__("Copy", "folders"),
            "PASTE"                      => esc_html__("Paste", "folders"),
            "DELETE"                     => esc_html__("Delete", "folders"),
            "BULK_ORGANIZE"              => esc_html__("Bulk Organize", "folders"),
            "DRAG_AND_DROP"              => esc_html__("Drag and drop your media files to the relevant folders", "folders"),
            "SELECT_ALL"                 => esc_html__("Select All", "folders"),
            "MOVE_SELECTED_FILES"        => esc_html__("Move Selected files to:", "folders"),
            "UPLOADING_FILES"            => esc_html__("Uploading files", "folders"),
            "SELECT_ITEMS_TO_MOVE"       => esc_html__("Please select items to move in folder", "folders"),
            "LOADING_FILES"              => esc_html__("Loading...", "folders"),
            "SELECT_FOLDER"              => esc_html__("Select Folder", "folders"),
            "UNASSIGNED"                 => esc_html__("(Unassigned)", "folders"),
            "SELECT_ITEMS"               => esc_html__("Select Items to move", "folders"),
            "ONE_ITEM"                   => esc_html__("1 Item", "folders"),
            "ITEMS"                      => esc_html__("Items", "folders"),
            "SELECTED"                   => esc_html__("Selected", "folders"),
            "MOVE_TO_FOLDER"             => esc_html__("Move to Folder", "folders"),
            "DELETE_FOLDER_MESSAGE"      => esc_html__("Are you sure you want to delete the selected folder?", "folders"),
            "ITEM_NOT_DELETED"           => esc_html__("Items in the folder will not be deleted.", "folders"),
            "DELETE_FOLDERS_MESSAGE"     => esc_html__("Are you sure you want to delete the selected folders?", "folders"),
            "ITEMS_NOT_DELETED"          => esc_html__("Items in the selected folders will not be deleted.", "folders"),
            "SELECT_AT_LEAST_ONE_FOLDER" => esc_html__("Please select at least one folder to delete", "folders"),
            "YES_DELETE_IT"              => esc_html__("Yes, Delete it!", "folders"),
            "SUBMIT"                     => esc_html__("Submit", "folders"),
            "EXPAND"                     => esc_html__("Expand", "folders"),
            "COLLAPSE"                   => esc_html__("Collapse", "folders"),
            "DUPLICATING_FOLDER"         => esc_html__("Duplicating to a new folder", "folders"),
            "ADD_NEW_FOLDER"             => esc_html__("Add a new folder", "folders"),
            "ACTIVATE"                   => [
                "REMOVE_STAR"      => esc_html__("Remove Star (Activate)", "folders"),
                "ADD_STAR"         => esc_html__("Add a Star (Activate)", "folders"),
                "STICKY_FOLDER"    => esc_html__("Sticky Folder (Activate)", "folders"),
                "NEW_SUB_FOLDER"   => esc_html__("New Sub-folder (Activate)", "folders"),
                "LOCK_FOLDER"      => esc_html__("Lock Folder (Activate)", "folders"),
                "DUPLICATE_FOLDER" => esc_html__("Duplicate folder (Activate)", "folders"),
                "DOWNLOAD_ZIP"     => esc_html__("Download Zip (Activate)", "folders"),
            ],
            "PRO"                   => [
                "ADD_STAR"         => esc_html__("Add a Star (Pro)", "folders"),
                "REMOVE_STAR"      => esc_html__("Remove Star (Pro)", "folders"),
                "STICKY_FOLDER"    => esc_html__("Sticky Folder (Pro)", "folders"),
                "NEW_SUB_FOLDER"   => esc_html__("New Sub-folder (Pro)", "folders"),
                "LOCK_FOLDER"      => esc_html__("Lock Folder (Pro)", "folders"),
                "DUPLICATE_FOLDER" => esc_html__("Duplicate folder (Pro)", "folders"),
                "DOWNLOAD_ZIP"     => esc_html__("Download Zip (Pro)", "folders"),
                "OPEN_THIS_FOLDER" => esc_html__("Open this folder by default (Pro)", "folders"),
            ],
        ];

    }//end js_strings()

    /**
     * Selected colors for folders
     *
     * @since  2.9.8
     * @access public
     */
    public function selected_colors() {
        $customize_folders = get_option('customize_folders');
        $defaultColors = ["#202020", "#86cd91", "#1E88E5", "#ff6060"];
        $colors = isset($customize_folders['folder_colors'])?$customize_folders['folder_colors']:$defaultColors;
        return $colors;
    }//end selected_colors()


    /**
     * Redirect to folders settings page on Plugin activation
     *
     * @since  1.0.0
     * @access public
     */
    public function plugin_action_links($links)
    {
        array_unshift($links, '<a href="'.admin_url("admin.php?page=wcp_folders_settings").'" >'.esc_html__('Settings', 'folders').'</a>');
        $links['need_help'] = '<a target="_blank" href="https://premio.io/help/folders/?utm_source=pluginspage" >'.__('Need help?', 'folders').'</a>';

        // PRO link for only for FREE
        $links['pro'] = '<a class="wcp-folder-upgrade-button" href="'.$this->getFoldersUpgradeURL().'" >'.__('Upgrade', 'folders').'</a>';
        return $links;

    }//end plugin_action_links()


    /**
     * Create instance of Folder class
     *
     * @since  1.0.0
     * @access public
     * @return $folders
     */
    public static function get_instance()
    {
        if (empty(self::$instance)) {
            // Do not change Class name here
            self::$instance = new WCP_Folders();
        }

        return self::$instance;

    }//end get_instance()


    /**
     * Will check for empty folder order
     *
     * @since  1.0.0
     * @access public
     */
    public function check_and_set_post_type()
    {
        $options           = get_option('folders_settings');
        $old_plugin_status = 0;
        $post_array        = [];
        if (!empty($options) && is_array($options)) {
            foreach ($options as $key => $val) {
                if (!(strpos($key, 'folders4') === false) && $old_plugin_status == 0) {
                    $old_plugin_status = 1;
                }

                if (in_array($key, ["folders4page", "folders4post", "folders4attachment"])) {
                    $post_array[] = str_replace("folders4", "", $key);
                }
            }
        } else {
            $post_array = [
                "page",
                "post",
                "attachment",
            ];
        }

        if ($old_plugin_status == 1) {
            update_option("folders_show_in_menu", "on");
            $old_plugin_var = get_option("folder_old_plugin_status");
            if (empty($old_plugin_var) || $old_plugin_var == null) {
                update_option("folder_old_plugin_status", "1");
            }

            update_option('folders_settings', $post_array);
            self::set_default_values_if_not_exists();
        }

        if (!empty($post_array) && get_option('folders_settings') === false) {
            update_option('folders_settings', $post_array);
            update_option("folders_show_in_menu", "off");
        }

    }//end check_and_set_post_type()


    /**
     * Migrate data for old versions
     *
     * @since  1.0.0
     * @access public
     */
    public static function activate()
    {
        premio_folders_plugin_check_for_setting();
        $folder_setting = get_option("folders_settings");
        if ($folder_setting === false) {
            add_option("wcp_folder_version_267", 1);
        }

        update_option("folders_show_in_menu", "off");
        $option = get_option("folder_redirect_status");
        if ($option === false) {
            add_option("folder_intro_box", "show");
        }

        if(WCP_FOLDER_VERSION == "3.0") {
            $hide_folder_color_pop_up = get_option("hide_folder_color_pop_up");
            if(!($hide_folder_color_pop_up)) {
                add_option("hide_folder_color_pop_up", "yes");
            } else {
                update_option("hide_folder_color_pop_up", "yes");
            }
        }

        if(!defined("DOING_AJAX")) {
            delete_option("folder_redirect_status");
            add_option("folder_redirect_status", 1);
        }

    }//end activate()


    /**
     * Remove folder data on deactivation
     *
     * @since  1.0.0
     * @access public
     */
    public static function deactivate()
    {
        $customize_folders = get_option('customize_folders');
        $DS      = DIRECTORY_SEPARATOR;
        $dirName = ABSPATH."wp-content{$DS}plugins{$DS}folders-pro{$DS}";
        $is_pro  = get_option("folders_pro_is_in_process");
        if (!is_dir($dirName) && $is_pro === false && isset($customize_folders['remove_folders_when_removed']) && $customize_folders['remove_folders_when_removed'] == "on") {
            self::$folders = 0;
            self::remove_folder_by_taxonomy("media_folder");
            self::remove_folder_by_taxonomy("folder");
            self::remove_folder_by_taxonomy("post_folder");
            $post_types = get_post_types([], 'objects');
            $post_array = [
                "page",
                "post",
                "attachment",
            ];
            foreach ($post_types as $post_type) {
                if (!in_array($post_type->name, $post_array)) {
                    self::remove_folder_by_taxonomy($post_type->name.'_folder');
                }
            }

            delete_option('customize_folders');
            delete_option('default_folders');
            delete_option('folders_show_in_menu');
            delete_option('folder_redirect_status');
            delete_option('folders_settings');
            delete_option('premio_folder_options');
            delete_option('folders_settings_updated');
        }//end if

    }//end deactivate()


    /**
     * Register folder settings
     *
     * @since  1.0.0
     * @access public
     */
    function folders_register_settings()
    {
        register_setting('folders_settings', 'folders_settings1', 'folders_settings_validate');
        register_setting('default_folders', 'default_folders');
        register_setting('customize_folders', 'customize_folders');

        self::check_and_set_post_type();

        $getData = filter_input_array(INPUT_GET);
        if (isset($getData['hide_menu']) && $getData['hide_menu'] == "scan-files" && isset($getData['nonce'])) {
            if (current_user_can('manage_options')) {
                $nonce = $getData['nonce'];
                if (wp_verify_nonce($nonce, "folders-scan-files")) {
                    $customize_folders = get_option('customize_folders');
                    $customize_folders['folders_media_cleaning'] = "no";
                    update_option("customize_folders", $customize_folders);
                    wp_redirect(admin_url("upload.php"));
                    exit;
                }
            }
        }
    }//end folders_register_settings()


    /**
     * Folders upgrade URL
     *
     * @since  1.0.0
     * @access public
     * @return $url
     */
    function getFoldersUpgradeURL()
    {
        $customize_folders = get_option("customize_folders");
        if (isset($customize_folders['show_folder_in_settings']) && $customize_folders['show_folder_in_settings'] == "yes") {
            return admin_url("options-general.php?page=wcp_folders_settings&setting_page=upgrade-to-pro");
        } else {
            return admin_url("admin.php?page=folders-upgrade-to-pro");
        }

    }//end getFoldersUpgradeURL()


    /**
     * Returns folders settings URL
     *
     * @since  1.0.0
     * @access public
     * @return $url
     */
    function getFolderSettingsURL()
    {
        $customize_folders = get_option("customize_folders");
        if (isset($customize_folders['show_folder_in_settings']) && $customize_folders['show_folder_in_settings'] == "yes") {
            return admin_url("options-general.php?page=wcp_folders_settings");
        } else {
            return admin_url("admin.php?page=wcp_folders_settings");
        }

    }//end getFolderSettingsURL()


    /**
     * Checking folders setting is inside wordpress setting menu
     *
     * @since  1.0.0
     * @access public
     */
    function isFoldersInSettings()
    {
        $customize_folders = get_option("customize_folders");
        if (isset($customize_folders['show_folder_in_settings']) && $customize_folders['show_folder_in_settings'] == "yes") {
            return true;
        }

        return false;

    }//end isFoldersInSettings()


    /**
     * Add Checkbox to table head for page, post, attachments
     *
     * @since  1.0.0
     * @access public
     * @return $defaults
     */
    function wcp_manage_columns_head($defaults, $d="")
    {
        global $typenow;
        $type   = $typenow;
        $action = $this->getRequestVar("action");
        if ($action == 'inline-save') {
            $post_type = $this->getRequestVar('post_type');
            $type      = self::sanitize_options($post_type);
        }

        $options = get_option("folders_settings");
        if (is_array($options) && in_array($type, $options)) {
            $columns = ([
                'wcp_move' => '<div class="wcp-move-multiple wcp-col" title="'.esc_html__('Move selected items', 'folders').'"><span class="dashicons dashicons-move"></span><div class="wcp-items"></div></div>',
            ] + $defaults);
            return $columns;
        }

        return $defaults;

    }//end wcp_manage_columns_head()


    /**
     * Add Checkbox to table body content for page, post, attachments
     *
     * @since  1.0.0
     * @access public
     * @return $column_name
     */
    function wcp_manage_columns_content($column_name, $post_ID)
    {
        $postIDs = self::$postIds;
        if (!is_array($postIDs)) {
            $postIDs = [];
        }

        if (!in_array($post_ID, $postIDs)) {
            $postIDs[]     = $post_ID;
            self::$postIds = $postIDs;
            global $typenow;
            $type   = $typenow;
            $action = $this->getRequestVar('action');
            if ($action == 'inline-save') {
                $post_type = $this->getRequestVar('post_type');
                $type      = self::sanitize_options($post_type);
            }

            $options = get_option("folders_settings");
            if (is_array($options) && in_array($type, $options)) {
                if ($column_name == 'wcp_move') {
                    $title = get_the_title();
                    if (strlen($title) > 20) {
                        $title = substr($title, 0, 20)."...";
                    }

                    echo "<div class='wcp-move-file' data-id='".esc_attr($post_ID)."'><span class='wcp-move dashicons dashicons-move' data-id='".esc_attr($post_ID)."'></span><span class='wcp-item' data-object-id='".esc_attr($post_ID)."'>".esc_attr($title)."</span></div>";
                }
            }
        }//end if

    }//end wcp_manage_columns_content()


    /**
     * Exclude empty folders
     *
     * @since  1.0.0
     * @access public
     * @return $query
     */
    function taxonomy_archive_exclude_children($query)
    {
        $options = get_option("folders_settings");
        if (!empty($options)) {
            $taxonomy_slugs = [];
            foreach ($options as $option) {
                $taxonomy_slugs[] = self::get_custom_post_type($option);
            }

            if (!empty($taxonomy_slugs)) {
                $i = 0;
                foreach ($query->tax_query->queries as $tax_query_item) {
                    if (empty($taxonomy_slugs) || (isset($tax_query_item['taxonomy']) && in_array($tax_query_item['taxonomy'], $taxonomy_slugs))) {
                        $query->tax_query->queries[$i]['include_children'] = 0;
                    }
                }
            }
        }

    }//end taxonomy_archive_exclude_children()


    /**
     * Add folder settins to WP menu
     *
     * @since  1.0.0
     * @access public
     */
    public function admin_menu()
    {
        $customize_folders = get_option("customize_folders");
        if (isset($customize_folders['show_folder_in_settings']) && $customize_folders['show_folder_in_settings'] == "yes") {
            add_options_page(
                esc_html__('Folders Settings', 'folders'),
                esc_html__('Folders Settings', 'folders'),
                'manage_options',
                'wcp_folders_settings',
                [
                    $this,
                    'wcp_folders_settings',
                ]
            );
        } else {
            $menu_slug = 'wcp_folders_settings';

            // Add menu item for settings page
            $page_title = esc_html__('Folders', 'folders');
            $menu_title = esc_html__('Folders Settings', 'folders');
            $capability = 'manage_options';
            $callback   = [
                $this,
                "wcp_folders_settings",
            ];
            $icon_url   = 'dashicons-category';
            $position   = 99;
            add_menu_page($page_title, $menu_title, $capability, $menu_slug, $callback, $icon_url, $position);

            $getData = filter_input_array(INPUT_GET);
            if (isset($getData['hide_folder_recommended_plugin']) && isset($getData['nonce'])) {
                if (current_user_can('manage_options')) {
                    $nonce = $getData['nonce'];
                    if (wp_verify_nonce($nonce, "folder_recommended_plugin")) {
                        update_option('hide_folder_recommended_plugin', "1");
                    }
                }
            }

            $recommended_plugin = get_option("hide_folder_recommended_plugin");
            if ($recommended_plugin === false) {
                add_submenu_page(
                    $menu_slug,
                    esc_html__('Recommended Plugins', 'folders'),
                    esc_html__('Recommended Plugins', 'folders'),
                    'manage_options',
                    'recommended-folder-plugins',
                    [
                        $this,
                        'recommended_plugins',
                    ]
                );
            }

            // Do not Change Free/Pro Change for menu
            add_submenu_page(
                $menu_slug,
                esc_html__('Upgrade to Pro', 'folders'),
                esc_html__('Upgrade to Pro', 'folders'),
                'manage_options',
                'folders-upgrade-to-pro',
                [
                    $this,
                    'wcp_folders_upgrade_or_register',
                ]
            );

            // Add menu for media cleaning
            $show_in_page = !isset($customize_folders['folders_media_cleaning']) ? "yes" : $customize_folders['folders_media_cleaning'];

            if ($show_in_page == "yes") {
                add_submenu_page(
                    "upload.php",
                    esc_html__('Media Cleaning', 'folders'),
                    esc_html__('Media Cleaning', 'folders'),
                    'manage_options',
                    'folders-media-cleaning',
                    [
                        $this,
                        'wcp_folders_media_cleaning',
                    ]
                );
            }
        }//end if

        self::check_and_set_post_type();

        $show_menu = get_option("folders_show_in_menu", true);
        if ($show_menu == "on") {
            self::create_menu_for_folders();
        }

    }//end admin_menu()


    /**
     * Media Cleaner Page
     *
     * @since  1.0.0
     * @access public
     * @return $html
     */
    public function wcp_folders_media_cleaning()
    {
        include_once dirname(dirname(__FILE__))."/templates/admin/media-cleaning.php";

    }//end wcp_folders_media_cleaning()


    /**
     * Recommended plugins Page
     *
     * @since  1.0.0
     * @access public
     * @return $html
     */
    public function recommended_plugins()
    {
        include_once dirname(dirname(__FILE__))."/templates/admin/recommended-plugins.php";

    }//end recommended_plugins()


    /**
     * Folders Upgrade Page
     *
     * @since  1.0.0
     * @access public
     * @return $html
     */
    public function wcp_folders_upgrade_or_register()
    {
        self::set_default_values_if_not_exists();
        include_once dirname(dirname(__FILE__))."/templates/admin/upgrade-to-pro.php";

    }//end wcp_folders_upgrade_or_register()


    /**
     * Folders Settings Page
     *
     * @since  1.0.0
     * @access public
     * @return $html
     */
    public function wcp_folders_settings()
    {
        self::set_default_values_if_not_exists();
        // Only in Free, Get Folders update confirmation popup
        $is_shown = get_option("folder_update_message");
        if ($is_shown === false) {
            include_once dirname(dirname(__FILE__))."/templates/admin/update.php";
        } else {
            $setting_page = filter_input(INPUT_GET, 'setting_page');
            if (empty($setting_page)) {
                $setting_page = "folder-settings";
            }
            if($setting_page == "upgrade-to-pro") {
                $hasBackButton = true;
                include_once dirname(dirname(__FILE__)) . "/templates/admin/upgrade-to-pro.php";
            } else {
                $options = get_option('folders_settings');
                $options = (empty($options) || !is_array($options)) ? [] : $options;
                $post_types = get_post_types(['public' => true], 'objects');
                $terms_data = [];
                foreach ($post_types as $post_type) {
                    if (in_array($post_type->name, $options)) {
                        $term = $post_type->name;
                        $term = self::get_custom_post_type($term);
                        $categories = self::get_terms_hierarchical($term);
                        $terms_data[$post_type->name] = $categories;
                    } else {
                        $terms_data[$post_type->name] = [];
                    }
                }

                $fonts = self::get_font_list();

                $plugins = new WCP_Folder_Plugins();
                $plugin_info = $plugins->get_plugin_information();
                $is_plugin_exists = $plugins->is_exists;
                $settingURL = $this->getFolderSettingsURL();

                $setting_page = in_array($setting_page, ["folder-settings", "customize-folders", "folders-import", "upgrade-to-pro", "folders-by-user","notification-settings"]) ? $setting_page : "folder-settings";
                $isInSettings = $this->isFoldersInSettings();

                include_once dirname(dirname(__FILE__)) . "/templates/admin/general-settings.php";

                $option = get_option("folder_intro_box");
                if ($option == "show") {
                    include_once dirname(dirname(__FILE__)) . "/templates/admin/folder-popup.php";
                }
            }
        }//end if

    }//end wcp_folders_settings()


    /**
     * Font List
     *
     * @since  1.0.0
     * @access public
     * @return $fonts
     */
    public static function get_font_list()
    {
        return [
            // System fonts.
            'Default'                        => 'Default',
            "System Stack"                   => 'Default',
            'Arial'                          => 'Default',
            'Tahoma'                         => 'Default',
            'Verdana'                        => 'Default',
            'Helvetica'                      => 'Default',
            'Times New Roman'                => 'Default',
            'Trebuchet MS'                   => 'Default',
            'Georgia'                        => 'Default',

            // Google Fonts (last update: 23/10/2018).
            'ABeeZee'                        => 'Google Fonts',
            'Abel'                           => 'Google Fonts',
            'Abhaya Libre'                   => 'Google Fonts',
            'Abril Fatface'                  => 'Google Fonts',
            'Aclonica'                       => 'Google Fonts',
            'Acme'                           => 'Google Fonts',
            'Actor'                          => 'Google Fonts',
            'Adamina'                        => 'Google Fonts',
            'Advent Pro'                     => 'Google Fonts',
            'Aguafina Script'                => 'Google Fonts',
            'Akronim'                        => 'Google Fonts',
            'Aladin'                         => 'Google Fonts',
            'Aldrich'                        => 'Google Fonts',
            'Alef'                           => 'Google Fonts',
            'Alef Hebrew'                    => 'Google Fonts',
        // Hack for Google Early Access.
            'Alegreya'                       => 'Google Fonts',
            'Alegreya SC'                    => 'Google Fonts',
            'Alegreya Sans'                  => 'Google Fonts',
            'Alegreya Sans SC'               => 'Google Fonts',
            'Alex Brush'                     => 'Google Fonts',
            'Alfa Slab One'                  => 'Google Fonts',
            'Alice'                          => 'Google Fonts',
            'Alike'                          => 'Google Fonts',
            'Alike Angular'                  => 'Google Fonts',
            'Allan'                          => 'Google Fonts',
            'Allerta'                        => 'Google Fonts',
            'Allerta Stencil'                => 'Google Fonts',
            'Allura'                         => 'Google Fonts',
            'Almendra'                       => 'Google Fonts',
            'Almendra Display'               => 'Google Fonts',
            'Almendra SC'                    => 'Google Fonts',
            'Amarante'                       => 'Google Fonts',
            'Amaranth'                       => 'Google Fonts',
            'Amatic SC'                      => 'Google Fonts',
            'Amethysta'                      => 'Google Fonts',
            'Amiko'                          => 'Google Fonts',
            'Amiri'                          => 'Google Fonts',
            'Amita'                          => 'Google Fonts',
            'Anaheim'                        => 'Google Fonts',
            'Andada'                         => 'Google Fonts',
            'Andika'                         => 'Google Fonts',
            'Angkor'                         => 'Google Fonts',
            'Annie Use Your Telescope'       => 'Google Fonts',
            'Anonymous Pro'                  => 'Google Fonts',
            'Antic'                          => 'Google Fonts',
            'Antic Didone'                   => 'Google Fonts',
            'Antic Slab'                     => 'Google Fonts',
            'Anton'                          => 'Google Fonts',
            'Arapey'                         => 'Google Fonts',
            'Arbutus'                        => 'Google Fonts',
            'Arbutus Slab'                   => 'Google Fonts',
            'Architects Daughter'            => 'Google Fonts',
            'Archivo'                        => 'Google Fonts',
            'Archivo Black'                  => 'Google Fonts',
            'Archivo Narrow'                 => 'Google Fonts',
            'Aref Ruqaa'                     => 'Google Fonts',
            'Arima Madurai'                  => 'Google Fonts',
            'Arimo'                          => 'Google Fonts',
            'Arizonia'                       => 'Google Fonts',
            'Armata'                         => 'Google Fonts',
            'Arsenal'                        => 'Google Fonts',
            'Artifika'                       => 'Google Fonts',
            'Arvo'                           => 'Google Fonts',
            'Arya'                           => 'Google Fonts',
            'Asap'                           => 'Google Fonts',
            'Asap Condensed'                 => 'Google Fonts',
            'Asar'                           => 'Google Fonts',
            'Asset'                          => 'Google Fonts',
            'Assistant'                      => 'Google Fonts',
            'Astloch'                        => 'Google Fonts',
            'Asul'                           => 'Google Fonts',
            'Athiti'                         => 'Google Fonts',
            'Atma'                           => 'Google Fonts',
            'Atomic Age'                     => 'Google Fonts',
            'Aubrey'                         => 'Google Fonts',
            'Audiowide'                      => 'Google Fonts',
            'Autour One'                     => 'Google Fonts',
            'Average'                        => 'Google Fonts',
            'Average Sans'                   => 'Google Fonts',
            'Averia Gruesa Libre'            => 'Google Fonts',
            'Averia Libre'                   => 'Google Fonts',
            'Averia Sans Libre'              => 'Google Fonts',
            'Averia Serif Libre'             => 'Google Fonts',
            'Bad Script'                     => 'Google Fonts',
            'Bahiana'                        => 'Google Fonts',
            'Bai Jamjuree'                   => 'Google Fonts',
            'Baloo'                          => 'Google Fonts',
            'Baloo Bhai'                     => 'Google Fonts',
            'Baloo Bhaijaan'                 => 'Google Fonts',
            'Baloo Bhaina'                   => 'Google Fonts',
            'Baloo Chettan'                  => 'Google Fonts',
            'Baloo Da'                       => 'Google Fonts',
            'Baloo Paaji'                    => 'Google Fonts',
            'Baloo Tamma'                    => 'Google Fonts',
            'Baloo Tammudu'                  => 'Google Fonts',
            'Baloo Thambi'                   => 'Google Fonts',
            'Balthazar'                      => 'Google Fonts',
            'Bangers'                        => 'Google Fonts',
            'Barlow'                         => 'Google Fonts',
            'Barlow Condensed'               => 'Google Fonts',
            'Barlow Semi Condensed'          => 'Google Fonts',
            'Barrio'                         => 'Google Fonts',
            'Basic'                          => 'Google Fonts',
            'Battambang'                     => 'Google Fonts',
            'Baumans'                        => 'Google Fonts',
            'Bayon'                          => 'Google Fonts',
            'Belgrano'                       => 'Google Fonts',
            'Bellefair'                      => 'Google Fonts',
            'Belleza'                        => 'Google Fonts',
            'BenchNine'                      => 'Google Fonts',
            'Bentham'                        => 'Google Fonts',
            'Berkshire Swash'                => 'Google Fonts',
            'Bevan'                          => 'Google Fonts',
            'Bigelow Rules'                  => 'Google Fonts',
            'Bigshot One'                    => 'Google Fonts',
            'Bilbo'                          => 'Google Fonts',
            'Bilbo Swash Caps'               => 'Google Fonts',
            'BioRhyme'                       => 'Google Fonts',
            'BioRhyme Expanded'              => 'Google Fonts',
            'Biryani'                        => 'Google Fonts',
            'Bitter'                         => 'Google Fonts',
            'Black And White Picture'        => 'Google Fonts',
            'Black Han Sans'                 => 'Google Fonts',
            'Black Ops One'                  => 'Google Fonts',
            'Bokor'                          => 'Google Fonts',
            'Bonbon'                         => 'Google Fonts',
            'Boogaloo'                       => 'Google Fonts',
            'Bowlby One'                     => 'Google Fonts',
            'Bowlby One SC'                  => 'Google Fonts',
            'Brawler'                        => 'Google Fonts',
            'Bree Serif'                     => 'Google Fonts',
            'Bubblegum Sans'                 => 'Google Fonts',
            'Bubbler One'                    => 'Google Fonts',
            'Buda'                           => 'Google Fonts',
            'Buenard'                        => 'Google Fonts',
            'Bungee'                         => 'Google Fonts',
            'Bungee Hairline'                => 'Google Fonts',
            'Bungee Inline'                  => 'Google Fonts',
            'Bungee Outline'                 => 'Google Fonts',
            'Bungee Shade'                   => 'Google Fonts',
            'Butcherman'                     => 'Google Fonts',
            'Butterfly Kids'                 => 'Google Fonts',
            'Cabin'                          => 'Google Fonts',
            'Cabin Condensed'                => 'Google Fonts',
            'Cabin Sketch'                   => 'Google Fonts',
            'Caesar Dressing'                => 'Google Fonts',
            'Cagliostro'                     => 'Google Fonts',
            'Cairo'                          => 'Google Fonts',
            'Calligraffitti'                 => 'Google Fonts',
            'Cambay'                         => 'Google Fonts',
            'Cambo'                          => 'Google Fonts',
            'Candal'                         => 'Google Fonts',
            'Cantarell'                      => 'Google Fonts',
            'Cantata One'                    => 'Google Fonts',
            'Cantora One'                    => 'Google Fonts',
            'Capriola'                       => 'Google Fonts',
            'Cardo'                          => 'Google Fonts',
            'Carme'                          => 'Google Fonts',
            'Carrois Gothic'                 => 'Google Fonts',
            'Carrois Gothic SC'              => 'Google Fonts',
            'Carter One'                     => 'Google Fonts',
            'Catamaran'                      => 'Google Fonts',
            'Caudex'                         => 'Google Fonts',
            'Caveat'                         => 'Google Fonts',
            'Caveat Brush'                   => 'Google Fonts',
            'Cedarville Cursive'             => 'Google Fonts',
            'Ceviche One'                    => 'Google Fonts',
            'Chakra Petch'                   => 'Google Fonts',
            'Changa'                         => 'Google Fonts',
            'Changa One'                     => 'Google Fonts',
            'Chango'                         => 'Google Fonts',
            'Charmonman'                     => 'Google Fonts',
            'Chathura'                       => 'Google Fonts',
            'Chau Philomene One'             => 'Google Fonts',
            'Chela One'                      => 'Google Fonts',
            'Chelsea Market'                 => 'Google Fonts',
            'Chenla'                         => 'Google Fonts',
            'Cherry Cream Soda'              => 'Google Fonts',
            'Cherry Swash'                   => 'Google Fonts',
            'Chewy'                          => 'Google Fonts',
            'Chicle'                         => 'Google Fonts',
            'Chivo'                          => 'Google Fonts',
            'Chonburi'                       => 'Google Fonts',
            'Cinzel'                         => 'Google Fonts',
            'Cinzel Decorative'              => 'Google Fonts',
            'Clicker Script'                 => 'Google Fonts',
            'Coda'                           => 'Google Fonts',
            'Coda Caption'                   => 'Google Fonts',
            'Codystar'                       => 'Google Fonts',
            'Coiny'                          => 'Google Fonts',
            'Combo'                          => 'Google Fonts',
            'Comfortaa'                      => 'Google Fonts',
            'Coming Soon'                    => 'Google Fonts',
            'Concert One'                    => 'Google Fonts',
            'Condiment'                      => 'Google Fonts',
            'Content'                        => 'Google Fonts',
            'Contrail One'                   => 'Google Fonts',
            'Convergence'                    => 'Google Fonts',
            'Cookie'                         => 'Google Fonts',
            'Copse'                          => 'Google Fonts',
            'Corben'                         => 'Google Fonts',
            'Cormorant'                      => 'Google Fonts',
            'Cormorant Garamond'             => 'Google Fonts',
            'Cormorant Infant'               => 'Google Fonts',
            'Cormorant SC'                   => 'Google Fonts',
            'Cormorant Unicase'              => 'Google Fonts',
            'Cormorant Upright'              => 'Google Fonts',
            'Courgette'                      => 'Google Fonts',
            'Cousine'                        => 'Google Fonts',
            'Coustard'                       => 'Google Fonts',
            'Covered By Your Grace'          => 'Google Fonts',
            'Crafty Girls'                   => 'Google Fonts',
            'Creepster'                      => 'Google Fonts',
            'Crete Round'                    => 'Google Fonts',
            'Crimson Text'                   => 'Google Fonts',
            'Croissant One'                  => 'Google Fonts',
            'Crushed'                        => 'Google Fonts',
            'Cuprum'                         => 'Google Fonts',
            'Cute Font'                      => 'Google Fonts',
            'Cutive'                         => 'Google Fonts',
            'Cutive Mono'                    => 'Google Fonts',
            'Damion'                         => 'Google Fonts',
            'Dancing Script'                 => 'Google Fonts',
            'Dangrek'                        => 'Google Fonts',
            'David Libre'                    => 'Google Fonts',
            'Dawning of a New Day'           => 'Google Fonts',
            'Days One'                       => 'Google Fonts',
            'Dekko'                          => 'Google Fonts',
            'Delius'                         => 'Google Fonts',
            'Delius Swash Caps'              => 'Google Fonts',
            'Delius Unicase'                 => 'Google Fonts',
            'Della Respira'                  => 'Google Fonts',
            'Denk One'                       => 'Google Fonts',
            'Devonshire'                     => 'Google Fonts',
            'Dhurjati'                       => 'Google Fonts',
            'Didact Gothic'                  => 'Google Fonts',
            'Diplomata'                      => 'Google Fonts',
            'Diplomata SC'                   => 'Google Fonts',
            'Do Hyeon'                       => 'Google Fonts',
            'Dokdo'                          => 'Google Fonts',
            'Domine'                         => 'Google Fonts',
            'Donegal One'                    => 'Google Fonts',
            'Doppio One'                     => 'Google Fonts',
            'Dorsa'                          => 'Google Fonts',
            'Dosis'                          => 'Google Fonts',
            'Dr Sugiyama'                    => 'Google Fonts',
            'Droid Arabic Kufi'              => 'Google Fonts',
        // Hack for Google Early Access.
            'Droid Arabic Naskh'             => 'Google Fonts',
        // Hack for Google Early Access.
            'Duru Sans'                      => 'Google Fonts',
            'Dynalight'                      => 'Google Fonts',
            'EB Garamond'                    => 'Google Fonts',
            'Eagle Lake'                     => 'Google Fonts',
            'East Sea Dokdo'                 => 'Google Fonts',
            'Eater'                          => 'Google Fonts',
            'Economica'                      => 'Google Fonts',
            'Eczar'                          => 'Google Fonts',
            'El Messiri'                     => 'Google Fonts',
            'Electrolize'                    => 'Google Fonts',
            'Elsie'                          => 'Google Fonts',
            'Elsie Swash Caps'               => 'Google Fonts',
            'Emblema One'                    => 'Google Fonts',
            'Emilys Candy'                   => 'Google Fonts',
            'Encode Sans'                    => 'Google Fonts',
            'Encode Sans Condensed'          => 'Google Fonts',
            'Encode Sans Expanded'           => 'Google Fonts',
            'Encode Sans Semi Condensed'     => 'Google Fonts',
            'Encode Sans Semi Expanded'      => 'Google Fonts',
            'Engagement'                     => 'Google Fonts',
            'Englebert'                      => 'Google Fonts',
            'Enriqueta'                      => 'Google Fonts',
            'Erica One'                      => 'Google Fonts',
            'Esteban'                        => 'Google Fonts',
            'Euphoria Script'                => 'Google Fonts',
            'Ewert'                          => 'Google Fonts',
            'Exo'                            => 'Google Fonts',
            'Exo 2'                          => 'Google Fonts',
            'Expletus Sans'                  => 'Google Fonts',
            'Fahkwang'                       => 'Google Fonts',
            'Fanwood Text'                   => 'Google Fonts',
            'Farsan'                         => 'Google Fonts',
            'Fascinate'                      => 'Google Fonts',
            'Fascinate Inline'               => 'Google Fonts',
            'Faster One'                     => 'Google Fonts',
            'Fasthand'                       => 'Google Fonts',
            'Fauna One'                      => 'Google Fonts',
            'Faustina'                       => 'Google Fonts',
            'Federant'                       => 'Google Fonts',
            'Federo'                         => 'Google Fonts',
            'Felipa'                         => 'Google Fonts',
            'Fenix'                          => 'Google Fonts',
            'Finger Paint'                   => 'Google Fonts',
            'Fira Mono'                      => 'Google Fonts',
            'Fira Sans'                      => 'Google Fonts',
            'Fira Sans Condensed'            => 'Google Fonts',
            'Fira Sans Extra Condensed'      => 'Google Fonts',
            'Fjalla One'                     => 'Google Fonts',
            'Fjord One'                      => 'Google Fonts',
            'Flamenco'                       => 'Google Fonts',
            'Flavors'                        => 'Google Fonts',
            'Fondamento'                     => 'Google Fonts',
            'Fontdiner Swanky'               => 'Google Fonts',
            'Forum'                          => 'Google Fonts',
            'Francois One'                   => 'Google Fonts',
            'Frank Ruhl Libre'               => 'Google Fonts',
            'Freckle Face'                   => 'Google Fonts',
            'Fredericka the Great'           => 'Google Fonts',
            'Fredoka One'                    => 'Google Fonts',
            'Freehand'                       => 'Google Fonts',
            'Fresca'                         => 'Google Fonts',
            'Frijole'                        => 'Google Fonts',
            'Fruktur'                        => 'Google Fonts',
            'Fugaz One'                      => 'Google Fonts',
            'GFS Didot'                      => 'Google Fonts',
            'GFS Neohellenic'                => 'Google Fonts',
            'Gabriela'                       => 'Google Fonts',
            'Gaegu'                          => 'Google Fonts',
            'Gafata'                         => 'Google Fonts',
            'Galada'                         => 'Google Fonts',
            'Galdeano'                       => 'Google Fonts',
            'Galindo'                        => 'Google Fonts',
            'Gamja Flower'                   => 'Google Fonts',
            'Gentium Basic'                  => 'Google Fonts',
            'Gentium Book Basic'             => 'Google Fonts',
            'Geo'                            => 'Google Fonts',
            'Geostar'                        => 'Google Fonts',
            'Geostar Fill'                   => 'Google Fonts',
            'Germania One'                   => 'Google Fonts',
            'Gidugu'                         => 'Google Fonts',
            'Gilda Display'                  => 'Google Fonts',
            'Give You Glory'                 => 'Google Fonts',
            'Glass Antiqua'                  => 'Google Fonts',
            'Glegoo'                         => 'Google Fonts',
            'Gloria Hallelujah'              => 'Google Fonts',
            'Goblin One'                     => 'Google Fonts',
            'Gochi Hand'                     => 'Google Fonts',
            'Gorditas'                       => 'Google Fonts',
            'Gothic A1'                      => 'Google Fonts',
            'Goudy Bookletter 1911'          => 'Google Fonts',
            'Graduate'                       => 'Google Fonts',
            'Grand Hotel'                    => 'Google Fonts',
            'Gravitas One'                   => 'Google Fonts',
            'Great Vibes'                    => 'Google Fonts',
            'Griffy'                         => 'Google Fonts',
            'Gruppo'                         => 'Google Fonts',
            'Gudea'                          => 'Google Fonts',
            'Gugi'                           => 'Google Fonts',
            'Gurajada'                       => 'Google Fonts',
            'Habibi'                         => 'Google Fonts',
            'Halant'                         => 'Google Fonts',
            'Hammersmith One'                => 'Google Fonts',
            'Hanalei'                        => 'Google Fonts',
            'Hanalei Fill'                   => 'Google Fonts',
            'Handlee'                        => 'Google Fonts',
            'Hanuman'                        => 'Google Fonts',
            'Happy Monkey'                   => 'Google Fonts',
            'Harmattan'                      => 'Google Fonts',
            'Headland One'                   => 'Google Fonts',
            'Heebo'                          => 'Google Fonts',
            'Henny Penny'                    => 'Google Fonts',
            'Herr Von Muellerhoff'           => 'Google Fonts',
            'Hi Melody'                      => 'Google Fonts',
            'Hind'                           => 'Google Fonts',
            'Hind Guntur'                    => 'Google Fonts',
            'Hind Madurai'                   => 'Google Fonts',
            'Hind Siliguri'                  => 'Google Fonts',
            'Hind Vadodara'                  => 'Google Fonts',
            'Holtwood One SC'                => 'Google Fonts',
            'Homemade Apple'                 => 'Google Fonts',
            'Homenaje'                       => 'Google Fonts',
            'IBM Plex Mono'                  => 'Google Fonts',
            'IBM Plex Sans'                  => 'Google Fonts',
            'IBM Plex Sans Condensed'        => 'Google Fonts',
            'IBM Plex Serif'                 => 'Google Fonts',
            'IM Fell DW Pica'                => 'Google Fonts',
            'IM Fell DW Pica SC'             => 'Google Fonts',
            'IM Fell Double Pica'            => 'Google Fonts',
            'IM Fell Double Pica SC'         => 'Google Fonts',
            'IM Fell English'                => 'Google Fonts',
            'IM Fell English SC'             => 'Google Fonts',
            'IM Fell French Canon'           => 'Google Fonts',
            'IM Fell French Canon SC'        => 'Google Fonts',
            'IM Fell Great Primer'           => 'Google Fonts',
            'IM Fell Great Primer SC'        => 'Google Fonts',
            'Iceberg'                        => 'Google Fonts',
            'Iceland'                        => 'Google Fonts',
            'Imprima'                        => 'Google Fonts',
            'Inconsolata'                    => 'Google Fonts',
            'Inder'                          => 'Google Fonts',
            'Indie Flower'                   => 'Google Fonts',
            'Inika'                          => 'Google Fonts',
            'Inknut Antiqua'                 => 'Google Fonts',
            'Irish Grover'                   => 'Google Fonts',
            'Istok Web'                      => 'Google Fonts',
            'Italiana'                       => 'Google Fonts',
            'Italianno'                      => 'Google Fonts',
            'Itim'                           => 'Google Fonts',
            'Jacques Francois'               => 'Google Fonts',
            'Jacques Francois Shadow'        => 'Google Fonts',
            'Jaldi'                          => 'Google Fonts',
            'Jim Nightshade'                 => 'Google Fonts',
            'Jockey One'                     => 'Google Fonts',
            'Jolly Lodger'                   => 'Google Fonts',
            'Jomhuria'                       => 'Google Fonts',
            'Josefin Sans'                   => 'Google Fonts',
            'Josefin Slab'                   => 'Google Fonts',
            'Joti One'                       => 'Google Fonts',
            'Jua'                            => 'Google Fonts',
            'Judson'                         => 'Google Fonts',
            'Julee'                          => 'Google Fonts',
            'Julius Sans One'                => 'Google Fonts',
            'Junge'                          => 'Google Fonts',
            'Jura'                           => 'Google Fonts',
            'Just Another Hand'              => 'Google Fonts',
            'Just Me Again Down Here'        => 'Google Fonts',
            'K2D'                            => 'Google Fonts',
            'Kadwa'                          => 'Google Fonts',
            'Kalam'                          => 'Google Fonts',
            'Kameron'                        => 'Google Fonts',
            'Kanit'                          => 'Google Fonts',
            'Kantumruy'                      => 'Google Fonts',
            'Karla'                          => 'Google Fonts',
            'Karma'                          => 'Google Fonts',
            'Katibeh'                        => 'Google Fonts',
            'Kaushan Script'                 => 'Google Fonts',
            'Kavivanar'                      => 'Google Fonts',
            'Kavoon'                         => 'Google Fonts',
            'Kdam Thmor'                     => 'Google Fonts',
            'Keania One'                     => 'Google Fonts',
            'Kelly Slab'                     => 'Google Fonts',
            'Kenia'                          => 'Google Fonts',
            'Khand'                          => 'Google Fonts',
            'Khmer'                          => 'Google Fonts',
            'Khula'                          => 'Google Fonts',
            'Kirang Haerang'                 => 'Google Fonts',
            'Kite One'                       => 'Google Fonts',
            'Knewave'                        => 'Google Fonts',
            'KoHo'                           => 'Google Fonts',
            'Kodchasan'                      => 'Google Fonts',
            'Kosugi'                         => 'Google Fonts',
            'Kosugi Maru'                    => 'Google Fonts',
            'Kotta One'                      => 'Google Fonts',
            'Koulen'                         => 'Google Fonts',
            'Kranky'                         => 'Google Fonts',
            'Kreon'                          => 'Google Fonts',
            'Kristi'                         => 'Google Fonts',
            'Krona One'                      => 'Google Fonts',
            'Krub'                           => 'Google Fonts',
            'Kumar One'                      => 'Google Fonts',
            'Kumar One Outline'              => 'Google Fonts',
            'Kurale'                         => 'Google Fonts',
            'La Belle Aurore'                => 'Google Fonts',
            'Laila'                          => 'Google Fonts',
            'Lakki Reddy'                    => 'Google Fonts',
            'Lalezar'                        => 'Google Fonts',
            'Lancelot'                       => 'Google Fonts',
            'Lateef'                         => 'Google Fonts',
            'Lato'                           => 'Google Fonts',
            'League Script'                  => 'Google Fonts',
            'Leckerli One'                   => 'Google Fonts',
            'Ledger'                         => 'Google Fonts',
            'Lekton'                         => 'Google Fonts',
            'Lemon'                          => 'Google Fonts',
            'Lemonada'                       => 'Google Fonts',
            'Libre Barcode 128'              => 'Google Fonts',
            'Libre Barcode 128 Text'         => 'Google Fonts',
            'Libre Barcode 39'               => 'Google Fonts',
            'Libre Barcode 39 Extended'      => 'Google Fonts',
            'Libre Barcode 39 Extended Text' => 'Google Fonts',
            'Libre Barcode 39 Text'          => 'Google Fonts',
            'Libre Baskerville'              => 'Google Fonts',
            'Libre Franklin'                 => 'Google Fonts',
            'Life Savers'                    => 'Google Fonts',
            'Lilita One'                     => 'Google Fonts',
            'Lily Script One'                => 'Google Fonts',
            'Limelight'                      => 'Google Fonts',
            'Linden Hill'                    => 'Google Fonts',
            'Lobster'                        => 'Google Fonts',
            'Lobster Two'                    => 'Google Fonts',
            'Londrina Outline'               => 'Google Fonts',
            'Londrina Shadow'                => 'Google Fonts',
            'Londrina Sketch'                => 'Google Fonts',
            'Londrina Solid'                 => 'Google Fonts',
            'Lora'                           => 'Google Fonts',
            'Love Ya Like A Sister'          => 'Google Fonts',
            'Loved by the King'              => 'Google Fonts',
            'Lovers Quarrel'                 => 'Google Fonts',
            'Luckiest Guy'                   => 'Google Fonts',
            'Lusitana'                       => 'Google Fonts',
            'Lustria'                        => 'Google Fonts',
            'M PLUS 1p'                      => 'Google Fonts',
            'M PLUS Rounded 1c'              => 'Google Fonts',
            'Macondo'                        => 'Google Fonts',
            'Macondo Swash Caps'             => 'Google Fonts',
            'Mada'                           => 'Google Fonts',
            'Magra'                          => 'Google Fonts',
            'Maiden Orange'                  => 'Google Fonts',
            'Maitree'                        => 'Google Fonts',
            'Mako'                           => 'Google Fonts',
            'Mali'                           => 'Google Fonts',
            'Mallanna'                       => 'Google Fonts',
            'Mandali'                        => 'Google Fonts',
            'Manuale'                        => 'Google Fonts',
            'Marcellus'                      => 'Google Fonts',
            'Marcellus SC'                   => 'Google Fonts',
            'Marck Script'                   => 'Google Fonts',
            'Margarine'                      => 'Google Fonts',
            'Markazi Text'                   => 'Google Fonts',
            'Marko One'                      => 'Google Fonts',
            'Marmelad'                       => 'Google Fonts',
            'Martel'                         => 'Google Fonts',
            'Martel Sans'                    => 'Google Fonts',
            'Marvel'                         => 'Google Fonts',
            'Mate'                           => 'Google Fonts',
            'Mate SC'                        => 'Google Fonts',
            'Maven Pro'                      => 'Google Fonts',
            'McLaren'                        => 'Google Fonts',
            'Meddon'                         => 'Google Fonts',
            'MedievalSharp'                  => 'Google Fonts',
            'Medula One'                     => 'Google Fonts',
            'Meera Inimai'                   => 'Google Fonts',
            'Megrim'                         => 'Google Fonts',
            'Meie Script'                    => 'Google Fonts',
            'Merienda'                       => 'Google Fonts',
            'Merienda One'                   => 'Google Fonts',
            'Merriweather'                   => 'Google Fonts',
            'Merriweather Sans'              => 'Google Fonts',
            'Metal'                          => 'Google Fonts',
            'Metal Mania'                    => 'Google Fonts',
            'Metamorphous'                   => 'Google Fonts',
            'Metrophobic'                    => 'Google Fonts',
            'Michroma'                       => 'Google Fonts',
            'Milonga'                        => 'Google Fonts',
            'Miltonian'                      => 'Google Fonts',
            'Miltonian Tattoo'               => 'Google Fonts',
            'Mina'                           => 'Google Fonts',
            'Miniver'                        => 'Google Fonts',
            'Miriam Libre'                   => 'Google Fonts',
            'Mirza'                          => 'Google Fonts',
            'Miss Fajardose'                 => 'Google Fonts',
            'Mitr'                           => 'Google Fonts',
            'Modak'                          => 'Google Fonts',
            'Modern Antiqua'                 => 'Google Fonts',
            'Mogra'                          => 'Google Fonts',
            'Molengo'                        => 'Google Fonts',
            'Molle'                          => 'Google Fonts',
            'Monda'                          => 'Google Fonts',
            'Monofett'                       => 'Google Fonts',
            'Monoton'                        => 'Google Fonts',
            'Monsieur La Doulaise'           => 'Google Fonts',
            'Montaga'                        => 'Google Fonts',
            'Montez'                         => 'Google Fonts',
            'Montserrat'                     => 'Google Fonts',
            'Montserrat Alternates'          => 'Google Fonts',
            'Montserrat Subrayada'           => 'Google Fonts',
            'Moul'                           => 'Google Fonts',
            'Moulpali'                       => 'Google Fonts',
            'Mountains of Christmas'         => 'Google Fonts',
            'Mouse Memoirs'                  => 'Google Fonts',
            'Mr Bedfort'                     => 'Google Fonts',
            'Mr Dafoe'                       => 'Google Fonts',
            'Mr De Haviland'                 => 'Google Fonts',
            'Mrs Saint Delafield'            => 'Google Fonts',
            'Mrs Sheppards'                  => 'Google Fonts',
            'Mukta'                          => 'Google Fonts',
            'Mukta Mahee'                    => 'Google Fonts',
            'Mukta Malar'                    => 'Google Fonts',
            'Mukta Vaani'                    => 'Google Fonts',
            'Muli'                           => 'Google Fonts',
            'Mystery Quest'                  => 'Google Fonts',
            'NTR'                            => 'Google Fonts',
            'Nanum Brush Script'             => 'Google Fonts',
            'Nanum Gothic'                   => 'Google Fonts',
            'Nanum Gothic Coding'            => 'Google Fonts',
            'Nanum Myeongjo'                 => 'Google Fonts',
            'Nanum Pen Script'               => 'Google Fonts',
            'Neucha'                         => 'Google Fonts',
            'Neuton'                         => 'Google Fonts',
            'New Rocker'                     => 'Google Fonts',
            'News Cycle'                     => 'Google Fonts',
            'Niconne'                        => 'Google Fonts',
            'Niramit'                        => 'Google Fonts',
            'Nixie One'                      => 'Google Fonts',
            'Nobile'                         => 'Google Fonts',
            'Nokora'                         => 'Google Fonts',
            'Norican'                        => 'Google Fonts',
            'Nosifer'                        => 'Google Fonts',
            'Notable'                        => 'Google Fonts',
            'Nothing You Could Do'           => 'Google Fonts',
            'Noticia Text'                   => 'Google Fonts',
            'Noto Kufi Arabic'               => 'Google Fonts',
        // Hack for Google Early Access.
            'Noto Naskh Arabic'              => 'Google Fonts',
        // Hack for Google Early Access.
            'Noto Sans'                      => 'Google Fonts',
            'Noto Sans Hebrew'               => 'Google Fonts',
        // Hack for Google Early Access.
            'Noto Sans JP'                   => 'Google Fonts',
            'Noto Sans KR'                   => 'Google Fonts',
            'Noto Serif'                     => 'Google Fonts',
            'Noto Serif JP'                  => 'Google Fonts',
            'Noto Serif KR'                  => 'Google Fonts',
            'Nova Cut'                       => 'Google Fonts',
            'Nova Flat'                      => 'Google Fonts',
            'Nova Mono'                      => 'Google Fonts',
            'Nova Oval'                      => 'Google Fonts',
            'Nova Round'                     => 'Google Fonts',
            'Nova Script'                    => 'Google Fonts',
            'Nova Slim'                      => 'Google Fonts',
            'Nova Square'                    => 'Google Fonts',
            'Numans'                         => 'Google Fonts',
            'Nunito'                         => 'Google Fonts',
            'Nunito Sans'                    => 'Google Fonts',
            'Odor Mean Chey'                 => 'Google Fonts',
            'Offside'                        => 'Google Fonts',
            'Old Standard TT'                => 'Google Fonts',
            'Oldenburg'                      => 'Google Fonts',
            'Oleo Script'                    => 'Google Fonts',
            'Oleo Script Swash Caps'         => 'Google Fonts',
            'Open Sans'                      => 'Google Fonts',
            'Open Sans Condensed'            => 'Google Fonts',
            'Open Sans Hebrew'               => 'Google Fonts',
        // Hack for Google Early Access.
            'Open Sans Hebrew Condensed'     => 'Google Fonts',
        // Hack for Google Early Access.
            'Oranienbaum'                    => 'Google Fonts',
            'Orbitron'                       => 'Google Fonts',
            'Oregano'                        => 'Google Fonts',
            'Orienta'                        => 'Google Fonts',
            'Original Surfer'                => 'Google Fonts',
            'Oswald'                         => 'Google Fonts',
            'Over the Rainbow'               => 'Google Fonts',
            'Overlock'                       => 'Google Fonts',
            'Overlock SC'                    => 'Google Fonts',
            'Overpass'                       => 'Google Fonts',
            'Overpass Mono'                  => 'Google Fonts',
            'Ovo'                            => 'Google Fonts',
            'Oxygen'                         => 'Google Fonts',
            'Oxygen Mono'                    => 'Google Fonts',
            'PT Mono'                        => 'Google Fonts',
            'PT Sans'                        => 'Google Fonts',
            'PT Sans Caption'                => 'Google Fonts',
            'PT Sans Narrow'                 => 'Google Fonts',
            'PT Serif'                       => 'Google Fonts',
            'PT Serif Caption'               => 'Google Fonts',
            'Pacifico'                       => 'Google Fonts',
            'Padauk'                         => 'Google Fonts',
            'Palanquin'                      => 'Google Fonts',
            'Palanquin Dark'                 => 'Google Fonts',
            'Pangolin'                       => 'Google Fonts',
            'Paprika'                        => 'Google Fonts',
            'Parisienne'                     => 'Google Fonts',
            'Passero One'                    => 'Google Fonts',
            'Passion One'                    => 'Google Fonts',
            'Pathway Gothic One'             => 'Google Fonts',
            'Patrick Hand'                   => 'Google Fonts',
            'Patrick Hand SC'                => 'Google Fonts',
            'Pattaya'                        => 'Google Fonts',
            'Patua One'                      => 'Google Fonts',
            'Pavanam'                        => 'Google Fonts',
            'Paytone One'                    => 'Google Fonts',
            'Peddana'                        => 'Google Fonts',
            'Peralta'                        => 'Google Fonts',
            'Permanent Marker'               => 'Google Fonts',
            'Petit Formal Script'            => 'Google Fonts',
            'Petrona'                        => 'Google Fonts',
            'Philosopher'                    => 'Google Fonts',
            'Piedra'                         => 'Google Fonts',
            'Pinyon Script'                  => 'Google Fonts',
            'Pirata One'                     => 'Google Fonts',
            'Plaster'                        => 'Google Fonts',
            'Play'                           => 'Google Fonts',
            'Playball'                       => 'Google Fonts',
            'Playfair Display'               => 'Google Fonts',
            'Playfair Display SC'            => 'Google Fonts',
            'Podkova'                        => 'Google Fonts',
            'Poiret One'                     => 'Google Fonts',
            'Poller One'                     => 'Google Fonts',
            'Poly'                           => 'Google Fonts',
            'Pompiere'                       => 'Google Fonts',
            'Pontano Sans'                   => 'Google Fonts',
            'Poor Story'                     => 'Google Fonts',
            'Poppins'                        => 'Google Fonts',
            'Port Lligat Sans'               => 'Google Fonts',
            'Port Lligat Slab'               => 'Google Fonts',
            'Pragati Narrow'                 => 'Google Fonts',
            'Prata'                          => 'Google Fonts',
            'Preahvihear'                    => 'Google Fonts',
            'Press Start 2P'                 => 'Google Fonts',
            'Pridi'                          => 'Google Fonts',
            'Princess Sofia'                 => 'Google Fonts',
            'Prociono'                       => 'Google Fonts',
            'Prompt'                         => 'Google Fonts',
            'Prosto One'                     => 'Google Fonts',
            'Proza Libre'                    => 'Google Fonts',
            'Puritan'                        => 'Google Fonts',
            'Purple Purse'                   => 'Google Fonts',
            'Quando'                         => 'Google Fonts',
            'Quantico'                       => 'Google Fonts',
            'Quattrocento'                   => 'Google Fonts',
            'Quattrocento Sans'              => 'Google Fonts',
            'Questrial'                      => 'Google Fonts',
            'Quicksand'                      => 'Google Fonts',
            'Quintessential'                 => 'Google Fonts',
            'Qwigley'                        => 'Google Fonts',
            'Racing Sans One'                => 'Google Fonts',
            'Radley'                         => 'Google Fonts',
            'Rajdhani'                       => 'Google Fonts',
            'Rakkas'                         => 'Google Fonts',
            'Raleway'                        => 'Google Fonts',
            'Raleway Dots'                   => 'Google Fonts',
            'Ramabhadra'                     => 'Google Fonts',
            'Ramaraja'                       => 'Google Fonts',
            'Rambla'                         => 'Google Fonts',
            'Rammetto One'                   => 'Google Fonts',
            'Ranchers'                       => 'Google Fonts',
            'Rancho'                         => 'Google Fonts',
            'Ranga'                          => 'Google Fonts',
            'Rasa'                           => 'Google Fonts',
            'Rationale'                      => 'Google Fonts',
            'Ravi Prakash'                   => 'Google Fonts',
            'Redressed'                      => 'Google Fonts',
            'Reem Kufi'                      => 'Google Fonts',
            'Reenie Beanie'                  => 'Google Fonts',
            'Revalia'                        => 'Google Fonts',
            'Rhodium Libre'                  => 'Google Fonts',
            'Ribeye'                         => 'Google Fonts',
            'Ribeye Marrow'                  => 'Google Fonts',
            'Righteous'                      => 'Google Fonts',
            'Risque'                         => 'Google Fonts',
            'Roboto'                         => 'Google Fonts',
            'Roboto Condensed'               => 'Google Fonts',
            'Roboto Mono'                    => 'Google Fonts',
            'Roboto Slab'                    => 'Google Fonts',
            'Rochester'                      => 'Google Fonts',
            'Rock Salt'                      => 'Google Fonts',
            'Rokkitt'                        => 'Google Fonts',
            'Romanesco'                      => 'Google Fonts',
            'Ropa Sans'                      => 'Google Fonts',
            'Rosario'                        => 'Google Fonts',
            'Rosarivo'                       => 'Google Fonts',
            'Rouge Script'                   => 'Google Fonts',
            'Rozha One'                      => 'Google Fonts',
            'Rubik'                          => 'Google Fonts',
            'Rubik Mono One'                 => 'Google Fonts',
            'Ruda'                           => 'Google Fonts',
            'Rufina'                         => 'Google Fonts',
            'Ruge Boogie'                    => 'Google Fonts',
            'Ruluko'                         => 'Google Fonts',
            'Rum Raisin'                     => 'Google Fonts',
            'Ruslan Display'                 => 'Google Fonts',
            'Russo One'                      => 'Google Fonts',
            'Ruthie'                         => 'Google Fonts',
            'Rye'                            => 'Google Fonts',
            'Sacramento'                     => 'Google Fonts',
            'Sahitya'                        => 'Google Fonts',
            'Sail'                           => 'Google Fonts',
            'Saira'                          => 'Google Fonts',
            'Saira Condensed'                => 'Google Fonts',
            'Saira Extra Condensed'          => 'Google Fonts',
            'Saira Semi Condensed'           => 'Google Fonts',
            'Salsa'                          => 'Google Fonts',
            'Sanchez'                        => 'Google Fonts',
            'Sancreek'                       => 'Google Fonts',
            'Sansita'                        => 'Google Fonts',
            'Sarala'                         => 'Google Fonts',
            'Sarina'                         => 'Google Fonts',
            'Sarpanch'                       => 'Google Fonts',
            'Satisfy'                        => 'Google Fonts',
            'Sawarabi Gothic'                => 'Google Fonts',
            'Sawarabi Mincho'                => 'Google Fonts',
            'Scada'                          => 'Google Fonts',
            'Scheherazade'                   => 'Google Fonts',
            'Schoolbell'                     => 'Google Fonts',
            'Scope One'                      => 'Google Fonts',
            'Seaweed Script'                 => 'Google Fonts',
            'Secular One'                    => 'Google Fonts',
            'Sedgwick Ave'                   => 'Google Fonts',
            'Sedgwick Ave Display'           => 'Google Fonts',
            'Sevillana'                      => 'Google Fonts',
            'Seymour One'                    => 'Google Fonts',
            'Shadows Into Light'             => 'Google Fonts',
            'Shadows Into Light Two'         => 'Google Fonts',
            'Shanti'                         => 'Google Fonts',
            'Share'                          => 'Google Fonts',
            'Share Tech'                     => 'Google Fonts',
            'Share Tech Mono'                => 'Google Fonts',
            'Shojumaru'                      => 'Google Fonts',
            'Short Stack'                    => 'Google Fonts',
            'Shrikhand'                      => 'Google Fonts',
            'Siemreap'                       => 'Google Fonts',
            'Sigmar One'                     => 'Google Fonts',
            'Signika'                        => 'Google Fonts',
            'Signika Negative'               => 'Google Fonts',
            'Simonetta'                      => 'Google Fonts',
            'Sintony'                        => 'Google Fonts',
            'Sirin Stencil'                  => 'Google Fonts',
            'Six Caps'                       => 'Google Fonts',
            'Skranji'                        => 'Google Fonts',
            'Slabo 13px'                     => 'Google Fonts',
            'Slabo 27px'                     => 'Google Fonts',
            'Slackey'                        => 'Google Fonts',
            'Smokum'                         => 'Google Fonts',
            'Smythe'                         => 'Google Fonts',
            'Sniglet'                        => 'Google Fonts',
            'Snippet'                        => 'Google Fonts',
            'Snowburst One'                  => 'Google Fonts',
            'Sofadi One'                     => 'Google Fonts',
            'Sofia'                          => 'Google Fonts',
            'Song Myung'                     => 'Google Fonts',
            'Sonsie One'                     => 'Google Fonts',
            'Sorts Mill Goudy'               => 'Google Fonts',
            'Source Code Pro'                => 'Google Fonts',
            'Source Sans Pro'                => 'Google Fonts',
            'Source Serif Pro'               => 'Google Fonts',
            'Space Mono'                     => 'Google Fonts',
            'Special Elite'                  => 'Google Fonts',
            'Spectral'                       => 'Google Fonts',
            'Spectral SC'                    => 'Google Fonts',
            'Spicy Rice'                     => 'Google Fonts',
            'Spinnaker'                      => 'Google Fonts',
            'Spirax'                         => 'Google Fonts',
            'Squada One'                     => 'Google Fonts',
            'Sree Krushnadevaraya'           => 'Google Fonts',
            'Sriracha'                       => 'Google Fonts',
            'Srisakdi'                       => 'Google Fonts',
            'Stalemate'                      => 'Google Fonts',
            'Stalinist One'                  => 'Google Fonts',
            'Stardos Stencil'                => 'Google Fonts',
            'Stint Ultra Condensed'          => 'Google Fonts',
            'Stint Ultra Expanded'           => 'Google Fonts',
            'Stoke'                          => 'Google Fonts',
            'Strait'                         => 'Google Fonts',
            'Stylish'                        => 'Google Fonts',
            'Sue Ellen Francisco'            => 'Google Fonts',
            'Suez One'                       => 'Google Fonts',
            'Sumana'                         => 'Google Fonts',
            'Sunflower'                      => 'Google Fonts',
            'Sunshiney'                      => 'Google Fonts',
            'Supermercado One'               => 'Google Fonts',
            'Sura'                           => 'Google Fonts',
            'Suranna'                        => 'Google Fonts',
            'Suravaram'                      => 'Google Fonts',
            'Suwannaphum'                    => 'Google Fonts',
            'Swanky and Moo Moo'             => 'Google Fonts',
            'Syncopate'                      => 'Google Fonts',
            'Tajawal'                        => 'Google Fonts',
            'Tangerine'                      => 'Google Fonts',
            'Taprom'                         => 'Google Fonts',
            'Tauri'                          => 'Google Fonts',
            'Taviraj'                        => 'Google Fonts',
            'Teko'                           => 'Google Fonts',
            'Telex'                          => 'Google Fonts',
            'Tenali Ramakrishna'             => 'Google Fonts',
            'Tenor Sans'                     => 'Google Fonts',
            'Text Me One'                    => 'Google Fonts',
            'The Girl Next Door'             => 'Google Fonts',
            'Tienne'                         => 'Google Fonts',
            'Tillana'                        => 'Google Fonts',
            'Timmana'                        => 'Google Fonts',
            'Tinos'                          => 'Google Fonts',
            'Titan One'                      => 'Google Fonts',
            'Titillium Web'                  => 'Google Fonts',
            'Trade Winds'                    => 'Google Fonts',
            'Trirong'                        => 'Google Fonts',
            'Trocchi'                        => 'Google Fonts',
            'Trochut'                        => 'Google Fonts',
            'Trykker'                        => 'Google Fonts',
            'Tulpen One'                     => 'Google Fonts',
            'Ubuntu'                         => 'Google Fonts',
            'Ubuntu Condensed'               => 'Google Fonts',
            'Ubuntu Mono'                    => 'Google Fonts',
            'Ultra'                          => 'Google Fonts',
            'Uncial Antiqua'                 => 'Google Fonts',
            'Underdog'                       => 'Google Fonts',
            'Unica One'                      => 'Google Fonts',
            'UnifrakturCook'                 => 'Google Fonts',
            'UnifrakturMaguntia'             => 'Google Fonts',
            'Unkempt'                        => 'Google Fonts',
            'Unlock'                         => 'Google Fonts',
            'Unna'                           => 'Google Fonts',
            'VT323'                          => 'Google Fonts',
            'Vampiro One'                    => 'Google Fonts',
            'Varela'                         => 'Google Fonts',
            'Varela Round'                   => 'Google Fonts',
            'Vast Shadow'                    => 'Google Fonts',
            'Vesper Libre'                   => 'Google Fonts',
            'Vibur'                          => 'Google Fonts',
            'Vidaloka'                       => 'Google Fonts',
            'Viga'                           => 'Google Fonts',
            'Voces'                          => 'Google Fonts',
            'Volkhov'                        => 'Google Fonts',
            'Vollkorn'                       => 'Google Fonts',
            'Vollkorn SC'                    => 'Google Fonts',
            'Voltaire'                       => 'Google Fonts',
            'Waiting for the Sunrise'        => 'Google Fonts',
            'Wallpoet'                       => 'Google Fonts',
            'Walter Turncoat'                => 'Google Fonts',
            'Warnes'                         => 'Google Fonts',
            'Wellfleet'                      => 'Google Fonts',
            'Wendy One'                      => 'Google Fonts',
            'Wire One'                       => 'Google Fonts',
            'Work Sans'                      => 'Google Fonts',
            'Yanone Kaffeesatz'              => 'Google Fonts',
            'Yantramanav'                    => 'Google Fonts',
            'Yatra One'                      => 'Google Fonts',
            'Yellowtail'                     => 'Google Fonts',
            'Yeon Sung'                      => 'Google Fonts',
            'Yeseva One'                     => 'Google Fonts',
            'Yesteryear'                     => 'Google Fonts',
            'Yrsa'                           => 'Google Fonts',
            'Zeyada'                         => 'Google Fonts',
            'Zilla Slab'                     => 'Google Fonts',
            'Zilla Slab Highlight'           => 'Google Fonts',
        ];

    }//end get_font_list()


    /**
     * Set Default folder order if not exists
     *
     * @since  1.0.0
     * @access public
     * @return $fonts
     */
    public function set_default_values_if_not_exists()
    {
        if (is_admin() || (defined('DOING_AJAX') && DOING_AJAX)) {
            $options = get_option('folders_settings');
            $options = empty($options) || !is_array($options) ? [] : $options;
            foreach ($options as $option) {
                $post_type = self::get_custom_post_type($option);
                $terms     = get_terms(
                    [
                        'taxonomy'   => $post_type,
                        'hide_empty' => false,
                        'meta_query' => [
                            [
                                'key'     => 'wcp_custom_order',
                                'compare' => 'NOT EXISTS',
                            ],
                        ],
                    ]
                );
                if (!empty($terms)) {
                    foreach ($terms as $term) {
                        $order = get_term_meta($term->term_id, "wcp_custom_order", true);
                        if (empty($order) || $order == null) {
                            update_term_meta($term->term_id, "wcp_custom_order", "1");
                        }
                    }
                }
            }//end foreach
        }//end if

    }//end set_default_values_if_not_exists()


    // Free and Pro major changes


    /**
     * Hide Folder intro Popup
     *
     * @since  1.0.0
     * @access public
     */
    public function premio_hide_child_popup()
    {
        $post_type = $this->getRequestVar('post_type');
        $nonce     = $this->getRequestVar('nonce');
        if (!empty($post_type) && wp_verify_nonce($nonce, 'wcp_folder_nonce_'.$post_type)) {
            $status = $this->getRequestVar('status');
            $status = ($status == 1) ? 1 : 0;
            if ($status) {
                 add_option("premio_hide_child_popup", 1);
            } else {
                 delete_option("premio_hide_child_popup");
            }
        }

        echo esc_attr("1");
        die;

    }//end premio_hide_child_popup()


    /**
     * Hide Folder intro Popup
     *
     * @since  1.0.0
     * @access public
     */
    public function folder_update_popup_status()
    {
        $nonce = $this->getRequestVar('nonce');
        if (!empty($nonce) && wp_verify_nonce($nonce, 'folder_update_popup_status')) {
            update_option("folder_intro_box", "hide");
        }

        echo esc_attr("1");
        die;

    }//end folder_update_popup_status()


    /**
     * Hide Folder import Popup
     *
     * @since  1.0.0
     * @access public
     */
    public function update_folders_import_status()
    {
        $nonce = $this->getRequestVar('nonce');
        if (!empty($nonce) && wp_verify_nonce($nonce, 'folders_import_3rd_party_data')) {
            update_option("folder_redirect_status", "3");
        }

        echo esc_attr("1");
        die;

    }//end update_folders_import_status()


    /**
     * Check folders has valid key
     *
     * @since  1.0.0
     * @access public
     */
    public function check_has_valid_key()
    {
        // Free/Pro: checking for key, for Free return 0, for Pro check for Key
        return 0;

    }//end check_has_valid_key()


    /**
     * Get license information
     *
     * @since  1.0.0
     * @access public
     */
    public function get_license_key_information($licenseKey)
    {
        return [];

    }//end get_license_key_information()


    /**
     * Get license key data
     *
     * @since  1.0.0
     * @access public
     */
    public function get_license_key_data($licenseKey='')
    {
        return [];

    }//end get_license_key_data()


    /**
     * Check for license key data
     *
     * @since  1.0.0
     * @access public
     */
    public function check_for_license_key()
    {
        return false;

    }//end check_for_license_key()


}//end class


if(!function_exists("folders_sanitize_text")) {
    function folders_sanitize_text($key ,$type = "post") {
        if($type == "post") {
            $string = filter_input(INPUT_POST, $key);
        } else {
            $string = filter_input(INPUT_GET, $key);
        }
        $string = stripslashes($string);
        $str = preg_replace('/\x00|<[^>]*>?/', '', $string);
        return str_replace(["'", '"'], ['&#39;', '&#34;'], $str);
    }
}
