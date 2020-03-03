<?php
defined('ABSPATH') or wp_die('Nope, not accessing this');
/* Free/Pro Class name change */
class WCP_Folders
{

    private static $instance;

    private static $license_key_data = null;

    private static $folders;

    public $tlfs = 0;

    private static $postIds;

    public function __construct()
    {
        spl_autoload_register(array($this, 'autoload'));
        add_action('init', array($this, 'create_folder_terms'), 15);
        add_action('admin_init', array($this, 'folders_register_settings'));
        add_action('admin_menu', array($this, 'admin_menu'), 10000);
        add_action('admin_enqueue_scripts', array($this, 'folders_admin_styles'));
        add_action('admin_enqueue_scripts', array($this, 'folders_admin_scripts'));
        add_filter('plugin_action_links_' . WCP_FOLDERS_PLUGIN_BASE, [$this, 'plugin_action_links']);
        add_action('admin_footer', array($this, 'admin_footer'));

        /* check for polygon media */
//        $polylang_options = get_option("polylang");
//        if(is_array($polylang_options) && isset($polylang_options['media_support']) && $polylang_options['media_support'] == 1) {
//            $polylang_options['media_support'] = 0;
//            update_option("polylang", $polylang_options);
//        }

        add_action('parse_tax_query', array($this, 'taxonomy_archive_exclude_children'));
        add_action('admin_footer', array($this, 'admin_footer_for_media'));

        /* Save Data */
        add_action('wp_ajax_wcp_add_new_folder', array($this, 'wcp_add_new_folder'));
        /* Update Data */
        add_action('wp_ajax_wcp_update_folder', array($this, 'wcp_update_folder'));
        /* Remove Data */
        add_action('wp_ajax_wcp_remove_folder', array($this, 'wcp_remove_folder'));
        /* Remove Multple Folder */
        add_action('wp_ajax_wcp_remove_muliple_folder', array($this, 'remove_muliple_folder'));
        /* Save State Data */
        add_action('wp_ajax_save_wcp_folder_state', array($this, 'save_wcp_folder_state'));
        /* Save State Data */
        add_action('wp_ajax_wcp_save_parent_data', array($this, 'wcp_save_parent_data'));
        /* Update Parent Data */
        add_action('wp_ajax_wcp_update_parent_information', array($this, 'wcp_update_parent_information'));
        /* Update Parent Data */
        add_action('wp_ajax_wcp_save_folder_order', array($this, 'wcp_save_folder_order'));
        /* Update Parent Data */
        add_action('wp_ajax_wcp_mark_un_mark_folder', array($this, 'wcp_mark_un_mark_folder'));
        /* Update Parent Data */
        add_action('wp_ajax_wcp_change_post_folder', array($this, 'wcp_change_post_folder'));
        /* Update Parent Data */
        add_action('wp_ajax_wcp_change_multiple_post_folder', array($this, 'wcp_change_multiple_post_folder'));
        /* Update width Data */
        add_action('wp_ajax_wcp_change_post_width', array($this, 'wcp_change_post_width'));
        /* Update width Data */
        add_action('wp_ajax_wcp_change_folder_display_status', array($this, 'wcp_change_folder_display_status'));
        /* Update width Data */
        add_action('wp_ajax_wcp_change_all_status', array($this, 'wcp_change_all_status'));
        self::$folders = 10;

        /* Send message on plugin deactivate */
        add_action( 'wp_ajax_folder_plugin_deactivate', array( $this, 'folder_plugin_deactivate' ) );
        /* Update Parent Data */
        add_action('wp_ajax_wcp_remove_post_folder', array($this, 'wcp_remove_post_folder'));
        /* Send message on owner */
        add_action( 'wp_ajax_wcp_folder_send_message_to_owner', array( $this, 'wcp_folder_send_message_to_owner' ) );
        /* Get default list */
        add_action( 'wp_ajax_wcp_get_default_list', array( $this, 'wcp_get_default_list' ) );
        /* Get default list */
        add_action( 'wp_ajax_get_folders_default_list', array( $this, 'get_folders_default_list' ) );
        /* Auto select folder for new page, post */
        add_action('new_to_auto-draft', array($this, 'new_to_auto_draft'), 10);
        /* for media */
        add_action('restrict_manage_posts', array($this, 'output_list_table_filters'), 10, 2);
        add_filter('pre_get_posts', array($this, 'filter_attachments_list'));
        add_action('wp_enqueue_media', array($this, 'output_backbone_view_filters'));
        add_filter('ajax_query_attachments_args', array($this, 'filter_attachments_grid'));
        add_filter('add_attachment', array($this, 'save_media_terms'));

        /* to filter un assigned items*/
        add_filter('pre_get_posts', array($this, 'filter_record_list'));
        add_filter('pre-upload-ui', array($this, 'show_dropdown_on_media_screen'));
        add_action('add_attachment', array($this, 'add_attachment_category'));



        $options = get_option("folders_settings");

        $options = is_array($options)?$options:array();

        if (in_array("post", $options)) {
            add_filter('manage_posts_columns', array($this, 'wcp_manage_columns_head'));
            add_action('manage_posts_custom_column', array($this, 'wcp_manage_columns_content'), 10, 2);

            add_filter( 'bulk_actions-edit-post', array($this, 'custom_bulk_action' ));
        }

        if (in_array("page", $options)) {
            add_filter('manage_page_posts_columns', array($this, 'wcp_manage_columns_head'));
            add_action('manage_page_posts_custom_column', array($this, 'wcp_manage_columns_content'), 10, 2);

            add_filter( 'bulk_actions-edit-page', array($this, 'custom_bulk_action' ));
        }

        if (in_array("attachment", $options)) {
            add_filter('manage_media_columns', array($this, 'wcp_manage_columns_head'));
            add_action('manage_media_custom_column', array($this, 'wcp_manage_columns_content'), 10, 2);

            add_filter( 'bulk_actions-edit-media', array($this, 'custom_bulk_action' ));
        }

        foreach ($options as $option) {
            if ($option != "post" && $option != "page" && $option != "attachment") {
                add_filter('manage_edit-'.$option.'_columns', array($this, 'wcp_manage_columns_head'), 99999);
                add_action('manage_'.$option.'_posts_custom_column', array($this, 'wcp_manage_columns_content'), 2, 2);
                add_filter( 'bulk_actions-edit-'.$option, array($this, 'custom_bulk_action' ));
            }
        }

        /* check for default folders */
        add_filter('pre_get_posts', array($this, 'check_for_default_folders'));

        add_action("wp_ajax_folder_update_status", array($this, 'folder_update_status'));

        /* load language files */
        add_action( 'plugins_loaded', array( $this, 'folders_text' ) );

        add_filter('get_terms', array( $this, 'get_terms_filter_without_trash'), 10, 3);

        add_filter('mla_media_modal_query_final_terms', array( $this, 'media_modal_query_final_terms'), 10, 3);
    }

    public function media_modal_query_final_terms($request) {
        if(isset($_REQUEST['action']) && $_REQUEST['action'] == "mla-query-attachments") {
            $query = isset($_REQUEST['query'])?$_REQUEST['query']:array();
            if(isset($query['media_folder']) && !empty($query['media_folder'])) {
                if($query['media_folder'] == -1) {
                    $tax_query = array(
                        'taxonomy'  => 'media_folder',
                        'operator'  => 'NOT EXISTS',
                    );
                    $request['tax_query'] = array( $tax_query );
                    $request = apply_filters( 'media_library_organizer_media_filter_attachments', $request, $_REQUEST );
                } else {
                    $request['media_folder'] = $query['media_folder'];
                }
            }
        }
        return $request;
    }

    public function get_terms_filter_without_trash($terms, $taxonomies, $args) {
        global $wpdb;
        if ( ! is_array($terms) && count($terms) < 1 ) {
            return $terms;
        }

        $post_table = $wpdb->prefix."posts";
        $term_table = $wpdb->prefix."term_relationships";
        foreach ( $terms as $key=>$term ) {
            if(isset($term->term_id)) {
                $result = $wpdb->get_var("SELECT COUNT(*) FROM {$post_table} p JOIN {$term_table} rl ON p.ID = rl.object_id WHERE rl.term_taxonomy_id = '{$term->term_id}' AND p.post_status != 'trash' LIMIT 1");
                if (intval($result) > 0) {
                    $terms[$key]->trash_count = intval($result);
                } else {
                    $terms[$key]->trash_count = 0;
                }
            }
        }
        return $terms;
    }

    public function custom_bulk_action($bulk_actions) {
        $bulk_actions['move_to_folder'] = __( 'Move to Folder', 'email_to_eric');
        return $bulk_actions;
    }

    public function folders_text() {
        load_plugin_textdomain("folders", FALSE, dirname(plugin_basename(__FILE__)).'/languages/');
    }

    public function admin_footer_for_media(){
        echo "<style>";
        $customize_folders = get_option('customize_folders');
        if(isset($customize_folders['dropdown_color']) && !empty($customize_folders['dropdown_color'])) {
            ?>
            #media-attachment-taxonomy-filter, .post-upload-ui .folder_for_media, select.media-select-folder { border-color: <?php echo esc_attr($customize_folders['dropdown_color']) ?>; color: <?php echo esc_attr($customize_folders['dropdown_color']) ?> }
            .folder_for_media option {color:#000000;}
            .folder_for_media option:first-child {
            font-weight: bold;
            }
            <?php
        }
        echo "</style>";
    }

    public function check_for_default_folders() {
        global $typenow, $current_screen;
        $isAjax = (defined('DOING_AJAX') && DOING_AJAX)?1:0;
        $options = get_option('folders_settings');
        $options = (empty($options) || !is_array($options))?array():$options;
        $post_status = filter_input(INPUT_GET, 'post_status', FILTER_SANITIZE_STRING);
        if(empty($post_status) && !$isAjax && in_array($typenow, $options) && (isset($current_screen->base) && ($current_screen->base == "edit" || ($current_screen->base == "upload")))) {

            $default_folders = get_option('default_folders');
            $default_folders = (empty($default_folders) || !is_array($default_folders))?array():$default_folders;

            $status = 1;
            if(isset($default_folders[$typenow]) && !empty($default_folders[$typenow])) {
                $type = self::get_custom_post_type($typenow);
                if($default_folders[$typenow] != -1) {
                    $term = get_term_by('slug', $default_folders[$typenow], $type);
                    if (empty($term) || !is_object($term)) {
                        $status = 0;
                    }
                }
            } else {
                $status = 0;
            }
            if($status) {
                if ($typenow == "attachment") {
                    $admin_url = admin_url("upload.php?post_type=attachment&media_folder=");
                    if (!isset($_REQUEST['media_folder'])) {
                        if (isset($default_folders[$typenow]) && !empty($default_folders[$typenow])) {
                            $admin_url .= $default_folders[$typenow];
                            ?>
                            <script>
                                window.location = '<?php echo $admin_url ?>';
                            </script>
                            <?php
                            exit;
                        }
                    }
                } else {
                    $admin_url = admin_url("edit.php?post_type=" . $typenow);
                    if (isset($_GET['s']) && !empty($_GET['s'])) {
                        $admin_url .= "&s=" . $_GET['s'];
                    }
                    $post_type = self::get_custom_post_type($typenow);
                    $admin_url .= "&{$post_type}=";
                    if (!isset($_REQUEST[$post_type])) {
                        if (isset($default_folders[$typenow]) && !empty($default_folders[$typenow])) {
                            $admin_url .= $default_folders[$typenow];
                            ?>
                            <script>
                                window.location = '<?php echo $admin_url ?>';
                            </script>
                            <?php
                            exit;
                        }
                    }
                }
            }
        }
    }

    public function folder_update_status() {
        if(!empty($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], 'folder_update_status')) {
            $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
            update_option("folder_update_message", 2);
            if($status == 1) {
                $url = 'https://go.premio.io/api/update.php?email='.$email.'&plugin=folders';
                $handle = curl_init();
                curl_setopt($handle, CURLOPT_URL, $url);
                curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($handle);
                curl_close($handle);
            }
        }
        echo "1";
        die;
    }

    public function add_attachment_category($post_ID)
    {
        if(self::is_for_this_post_type('attachment') || self::is_for_this_post_type('media')) {
            $folder_id = isset($_REQUEST["folder_for_media"]) ? $_REQUEST["folder_for_media"] : null;
            if ($folder_id !== null) {
                $folder_id = (int)$folder_id;
                $folder_id = self::sanitize_options($folder_id, "int");
                if ($folder_id > 0) {
                    $post_type = self::get_custom_post_type("attachment");
                    $term = get_term($folder_id);
                    if(!empty($term) && isset($term->slug)) {
                        wp_set_object_terms($post_ID, $term->slug, $post_type );
                    }
                }
            }
        }
    }

    public function show_dropdown_on_media_screen() {
        if(self::is_for_this_post_type('attachment')) {
            $post_type = self::get_custom_post_type('attachment');
            global $typenow, $current_screen;
            /* Free/Pro Class name change */
            if(!class_exists('WCP_Tree')) {
                $files = array(
                    'WCP_Tree' => WCP_DS . "includes" . WCP_DS . "tree.class.php"
                );

                foreach ($files as $file) {
                    if (file_exists(dirname(dirname(__FILE__)) . $file)) {
                        include_once dirname(dirname(__FILE__)) . $file;
                    }
                }
            }
            /* Free/Pro Class name change */
            $options = WCP_Tree::get_folder_option_data($post_type);?>
            <p class="attachments-category"><?php esc_html_e("Select a folder (Optional)", WCP_FOLDER) ?></p>
            <p class="attachments-category"><?php esc_html_e("First select the folder, and the upload the files", WCP_FOLDER) ?><br/></p>
            <p>
                <select name="folder_for_media" class="folder_for_media">
                    <option value="-1">- <?php esc_html_e('Unassigned', WCP_FOLDER) ?></option>
                    <?php echo $options ?>
                    <?php if($typenow == "attachment" && isset($current_screen->base) && $current_screen->base == "upload") {?>
                        <option value="add-folder"><?php esc_html_e('+ Create a New Folder', WCP_FOLDER) ?></option>
                    <?php } ?>
                </select>
            </p>
            <?php
        }
    }

    public function wcp_hide_folders()
    {
        $response = array();
        $response['status'] = 0;
        $response['error'] = 0;
        $response['data'] = array();
        $response['message'] = "";
        $postData = filter_input_array(INPUT_POST);
        $errorCounter = 0;
        if (!isset($postData['status']) || empty($postData['status'])) {
            $response['message'] = esc_attr__("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['type']) || empty($postData['type'])) {
            $response['message'] = esc_attr__("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] = esc_attr__("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else if ($postData['type'] == "page" && !current_user_can("edit_pages")) {
            $response['message'] = esc_attr__("You have not permission to update width", WCP_FOLDER);
            $errorCounter++;
        } else if ($postData['type'] != "page" && !current_user_can("edit_posts")) {
            $response['message'] = esc_attr__("You have not permission to update width", WCP_FOLDER);
            $errorCounter++;
        } else {
            $type = self::sanitize_options($postData['type']);
            $nonce = self::sanitize_options($postData['nonce']);
            if(!wp_verify_nonce($nonce, 'wcp_folder_nonce_'.$type)) {
                $response['message'] = esc_attr__("Your request is not valid", WCP_FOLDER);
                $errorCounter++;
            }
        }
        if ($errorCounter == 0) {
            $type = self::sanitize_options($postData['type']);
            $status = self::sanitize_options($postData['status']);
            $optionName = "wcp_folder_display_status_" . $type;
            update_option($optionName, $status);
            $response['status'] = 1;
        }
        echo json_encode($response);
        wp_die();
    }

    public function wcp_change_folder_display_status()
    {
        $response = array();
        $response['status'] = 0;
        $response['error'] = 0;
        $response['data'] = array();
        $response['message'] = "";
        $postData = filter_input_array(INPUT_POST);
        $errorCounter = 0;
        if (!isset($postData['status']) || empty($postData['status'])) {
            $response['message'] = esc_attr__("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['type']) || empty($postData['type'])) {
            $response['message'] = esc_attr__("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] = esc_attr__("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else if ($postData['type'] == "page" && !current_user_can("edit_pages")) {
            $response['message'] = esc_attr__("You have not permission to update width", WCP_FOLDER);
            $errorCounter++;
        } else if ($postData['type'] != "page" && !current_user_can("edit_posts")) {
            $response['message'] = esc_attr__("You have not permission to update width", WCP_FOLDER);
            $errorCounter++;
        } else {
            $type = self::sanitize_options($postData['type']);
            $nonce = self::sanitize_options($postData['nonce']);
            if(!wp_verify_nonce($nonce, 'wcp_folder_nonce_'.$type)) {
                $response['message'] = esc_attr__("Your request is not valid", WCP_FOLDER);
                $errorCounter++;
            }
        }
        if ($errorCounter == 0) {
            $type = self::sanitize_options($postData['type']);
            $width = self::sanitize_options($postData['status']);
            $optionName = "wcp_dynamic_display_status_" . $type;
            update_option($optionName, $width);
            $response['status'] = 1;
        }
        echo json_encode($response);
        wp_die();
    }

    public function wcp_remove_post_folder() {
        $response = array();
        $response['status'] = 0;
        $response['error'] = 0;
        $response['data'] = array();
        $response['message'] = "";
        $postData = filter_input_array(INPUT_POST);
        $errorCounter = 0;
        if (!isset($postData['post_id']) || empty($postData['post_id'])) {
            $response['message'] = esc_attr__("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['type']) || empty($postData['type'])) {
            $response['message'] = esc_attr__("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] = esc_attr__("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else if(!wp_verify_nonce($postData['nonce'], 'wcp_folder_nonce_'.$postData['type'])) {
            $response['message'] = esc_attr__("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        }
        if ($errorCounter == 0) {
            $type = self::sanitize_options($postData['type']);
            $post_id = self::sanitize_options($postData['post_id']);

            $post_id = explode(",", $post_id);

            $taxonomy = self::get_custom_post_type($type);

            foreach($post_id as $id) {
                if(!empty($id) && is_numeric($id) && $id > 0) {
                    wp_delete_object_term_relationships($id, $taxonomy);
                }
            }

            $response['status'] = 1;
        }
        echo json_encode($response);
        wp_die();
    }

    public function filter_record_list($query) {
        global $typenow;

        if($typenow == "attachment") {
            return;
        }

        if(!self::is_for_this_post_type($typenow)) {
            return $query;
        }

        $taxonomy = self::get_custom_post_type($typenow);

        if ( ! isset( $query->query['post_type'] ) ) {
            return $query;
        }

        if ( ! isset( $_REQUEST[$taxonomy] ) ) {
            return $query;
        }

        $term = sanitize_text_field( $_REQUEST[$taxonomy] );
        if ( $term != "-1" ) {
            return $query;
        }

        unset( $query->query_vars[$taxonomy] );

        $tax_query = array(
            'taxonomy'  => $taxonomy,
            'operator'  => 'NOT EXISTS',
        );

        $query->set( 'tax_query', array( $tax_query ) );
        $query->tax_query = new WP_Tax_Query( array( $tax_query ) );

        return $query;
    }

    public function wcp_get_default_list() {

        $postData = filter_input_array(INPUT_POST);

        $post_type = $postData['type'];

        $ttpsts = wp_count_posts($post_type)->inherit;

        $empty_items = self::get_tempt_posts($post_type);

        $post_type = self::get_custom_post_type($post_type);

        $taxonomies = self::get_terms_hierarchical($post_type);

        $response = array(
            'status' => 1,
            'total_items' => $ttpsts,
            'taxonomies' => $taxonomies,
            'empty_items' => $empty_items
        );
        echo json_encode($response);
        wp_die();
    }

    function get_folders_default_list() {
        $postData = filter_input_array(INPUT_POST);

        $post_type = $postData['type'];

        if($post_type != 'attachment') {
            $ttpsts = self::get_ttlpst($post_type);
        } else {
            $ttpsts = wp_count_posts($post_type)->inherit;
        }

        $empty_items = self::get_tempt_posts($post_type);

        $post_type = self::get_custom_post_type($post_type);

        $taxonomies = self::get_terms_hierarchical($post_type);

        $response = array(
            'status' => 1,
            'total_items' => $ttpsts,
            'empty_items' => $empty_items,
            'taxonomies' => $taxonomies
        );
        echo json_encode($response);
        die;

    }

    function save_media_terms( $post_id ) {
        if ( wp_is_post_revision( $post_id ) ) {
            return;
        }
        $post = get_post($post_id);
        if($post->post_type !== 'attachment') {
            return;
        }
        $post_type = self::get_custom_post_type('attachment');
        $selected_folder = get_option("selected_{$post_type}_folder");
        if($selected_folder != null && !empty($selected_folder)) {
            $terms = get_term($selected_folder);
            if(!empty($terms) && isset($terms->term_id)) {
                wp_set_post_terms($post_id, $terms->term_id, $post_type, false);
            }
        }
    }

    public function filter_attachments_grid( $args ) {
        $taxonomy = 'media_folder';
        if ( ! isset( $args[ $taxonomy ] ) ) {
            return $args;
        }
        $term = sanitize_text_field( $args[ $taxonomy ] );
        if ( $term != "-1" ) {
            return $args;
        }
        unset( $args[ $taxonomy ] );
        $args['tax_query'] = array(
            array(
                'taxonomy'  => $taxonomy,
                'operator'  => 'NOT EXISTS',
            ),
        );
        $args = apply_filters( 'media_library_organizer_media_filter_attachments_grid', $args );
        return $args;
    }

    public function get_tempt_posts($post_type = "")
    {
        global $wpdb;

        $post_table = $wpdb->prefix."posts";
        $term_table = $wpdb->prefix."term_relationships";
        $term_taxonomy_table = $wpdb->prefix."term_taxonomy";
        $taxonomy = self::get_custom_post_type($post_type);
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
                ) AND {$post_table}.post_type = '%s' AND (({$post_table}.post_status = 'publish' OR {$post_table}.post_status = 'future' OR {$post_table}.post_status = 'draft' OR {$post_table}.post_status = 'private'))";
        } else {
            $query = "SELECT COUNT(DISTINCT({$post_table}.ID)) AS total_records FROM {$post_table} WHERE 1=1  AND (
                  NOT EXISTS (
                                SELECT 1
                                FROM {$term_table}
                                INNER JOIN {$term_taxonomy_table}
                                ON {$term_taxonomy_table}.term_taxonomy_id = {$term_table}.term_taxonomy_id
                                WHERE {$term_taxonomy_table}.taxonomy = '%s'
                                AND {$term_table}.object_id = {$post_table}.ID
                            )
                ) AND {$post_table}.post_type = '%s' AND {$post_table}.post_status = 'inherit'";
        }

        $query = $wpdb->prepare($query, $taxonomy, $post_type);

        $tlrcds = $wpdb->get_var($query);

        if(!empty($tlrcds)) {
            return $tlrcds;
        } else {
            return 0;
        }
    }

    public function output_backbone_view_filters() {

        global $typenow, $current_screen;
        $isAjax = (defined('DOING_AJAX') && DOING_AJAX)?1:0;
        $options = get_option('folders_settings');
        $options = (empty($options) || !is_array($options))?array():$options;
        if(!$isAjax && in_array($typenow, $options) && (isset($current_screen->base) && ($current_screen->base == "edit" || ($current_screen->base == "upload")))) {

            $default_folders = get_option('default_folders');
            $default_folders = (empty($default_folders) || !is_array($default_folders))?array():$default_folders;

            $status = 1;
            if(isset($default_folders[$typenow]) && !empty($default_folders[$typenow])) {
                $type = self::get_custom_post_type($typenow);
                if($default_folders[$typenow] != -1) {
                    $term = get_term_by('slug', $default_folders[$typenow], $type);
                    if (empty($term) || !is_object($term)) {
                        $status = 0;
                    }
                }
            } else {
                $status = 0;
            }
            if($status) {
                if ($typenow == "attachment") {
                    $admin_url = admin_url("upload.php?post_type=attachment&media_folder=");
                    if (!isset($_REQUEST['media_folder'])) {
                        if (isset($default_folders[$typenow]) && !empty($default_folders[$typenow])) {
                            $admin_url .= $default_folders[$typenow];
                            ?>
                            <script>
                                window.location = '<?php echo $admin_url ?>';
                            </script>
                            <?php
                            exit;
                        }
                    }
                }
            }
        }

        /* Free/Pro URL Change */
        wp_enqueue_script( 'folders-media', WCP_FOLDER_URL.'assets/js/media.js', array( 'media-editor', 'media-views' ), WCP_FOLDER_VERSION, true );
        wp_localize_script( 'folders-media', 'folders_media_options', array(
            'terms'     => self::get_terms_hierarchical('media_folder'),
            'taxonomy'  => get_taxonomy('media_folder'),
            'ajax_url'  => admin_url("admin-ajax.php")
        ));
        /* Free/Pro URL Change */
        wp_enqueue_style( 'folders-media', WCP_FOLDER_URL . 'assets/css/media.css' , array(), WCP_FOLDER_VERSION);
    }

    public function get_terms_hierarchical( $taxonomy ) {
//        $terms = get_terms( array(
//            'taxonomy'      => $taxonomy,
//            'hide_empty'    => false,
//            'parent'        => 0,
//            'orderby' => 'meta_value_num',
//            'order' => 'ASC',
//            'update_count_callback' => '_update_generic_term_count',
//            'meta_query' => [[
//                'key' => 'wcp_custom_order',
//                'type' => 'NUMERIC',
//            ]]
//        ) );
//
//        if ( empty( $terms ) ) {
//            return false;
//        }
//
//        $hierarchy = _get_term_hierarchy( $taxonomy );
//
//        $hierarchical_terms = array();
//        if(!empty($terms)) {
//            foreach ($terms as $term) {
//                if(isset($term->term_id)) {
//                    $hierarchical_terms[] = $term;
//                    $hierarchical_terms = self::add_child_terms_recursive($taxonomy, $hierarchical_terms, $hierarchy, $term->term_id, 1);
//                }
//            }
//        }
//
//        return $hierarchical_terms;
        $terms = get_terms( array(
            'taxonomy'      => $taxonomy,
            'hide_empty' => false,
            'parent'   => 0,
            'orderby' => 'meta_value_num',
            'order' => 'ASC',
            'hierarchical' => false,
            'update_count_callback' => '_update_generic_term_count',
            'meta_query' => [[
                'key' => 'wcp_custom_order',
                'type' => 'NUMERIC',
            ]]
        ));
        $hierarchical_terms = array();
        if(!empty($terms)) {
            foreach ($terms as $term) {
                if(!empty($term) && isset($term->term_id)) {
                    $hierarchical_terms[] = $term;
                    $hierarchical_terms = self::get_child_terms($taxonomy, $hierarchical_terms, $term->term_id, "-");
                }
            }
        }
        return $hierarchical_terms;
    }

    public static function get_child_terms($taxonomy, $hierarchical_terms, $term_id, $separator = "-") {
        $terms = get_terms( array(
            'taxonomy'      => $taxonomy,
            'hide_empty'    => false,
            'parent'        => $term_id,
            'orderby' => 'meta_value_num',
            'order' => 'ASC',
            'hierarchical' => false,
            'update_count_callback' => '_update_generic_term_count',
            'meta_query' => [[
                'key' => 'wcp_custom_order',
                'type' => 'NUMERIC',
            ]]
        ));
        if(!empty($terms)) {
            foreach ($terms as $term) {
                if(isset($term->name)) {
                    $term->name = $separator . " " . $term->name;
                    $hierarchical_terms[] = $term;
                    $hierarchical_terms = self::get_child_terms($taxonomy, $hierarchical_terms, $term->term_id, $separator . "-");
                }
            }
        }

        return $hierarchical_terms;
    }

    private function add_child_terms_recursive( $taxonomy, $hierarchical_terms, $hierarchy, $current_term_id, $current_depth ) {

        if ( ! isset( $hierarchy[ $current_term_id ] ) ) {
            return $hierarchical_terms;
        }

        foreach ( $hierarchy[ $current_term_id ] as $child_term_id ) {

            $child_term = get_term( $child_term_id, $taxonomy );

            $child_term->name = str_pad( '', $current_depth, '-', STR_PAD_LEFT ) . ' ' . $child_term->name;

            $hierarchical_terms[] = $child_term;

            $hierarchical_terms = self::add_child_terms_recursive( $taxonomy, $hierarchical_terms, $hierarchy, $child_term_id, ( $current_depth + 1 ) );
        }

        return $hierarchical_terms;
    }

    public function filter_attachments_list( $query ) {

        if ( ! isset( $query->query['post_type'] ) ) {
            return $query;
        }

        if ( is_array( $query->query['post_type'] ) && ! in_array( 'attachment', $query->query['post_type'] ) ) {
            return $query;
        }
        if ( ! is_array( $query->query['post_type'] ) && strpos( $query->query['post_type'], 'attachment' ) === false ) {
            return $query;
        }

        if ( ! isset( $_REQUEST['media_folder'] ) ) {
            return $query;
        }

        $term = sanitize_text_field(wp_unslash($_REQUEST['media_folder']));
        if ( $term != "-1" ) {
            return $query;
        }

        unset( $query->query_vars['media_folder'] );

        $tax_query = array(
            'taxonomy'  => 'media_folder',
            'operator'  => 'NOT EXISTS',
        );

        $query->set( 'tax_query', array( $tax_query ) );
        $query->tax_query = new WP_Tax_Query( array( $tax_query ) );

        $query = apply_filters( 'media_library_organizer_media_filter_attachments', $query, $_REQUEST );

        return $query;

    }

    public function output_list_table_filters( $post_type, $view_name )
    {
        if ($post_type != 'attachment') {
            return;
        }

        if ($view_name != 'bar') {
            return;
        }

        $current_term = false;
        if ( isset( $_REQUEST['media_folder'] ) ) {
            $current_term = sanitize_text_field($_REQUEST['media_folder']);
        }

        wp_dropdown_categories( array(
            'show_option_all'   => esc_attr__( 'All Folders', WCP_FOLDER),
            'show_option_none'   => esc_attr__( '(Unassigned)', WCP_FOLDER),
            'option_none_value' => -1,
            'orderby'           => 'meta_value_num',
            'order'             => 'ASC',
            'show_count'        => true,
            'hide_empty'        => false,
            'update_count_callback' => '_update_generic_term_count',
            'echo'              => true,
            'selected'          => $current_term,
            'hierarchical'      => true,
            'name'              => 'media_folder',
            'id'                => '',
            'class'             => '',
            'taxonomy'          => 'media_folder',
            'value_field'       => 'slug',
            'meta_query' => [[
                'key' => 'wcp_custom_order',
                'type' => 'NUMERIC',
            ]]
        ) );

    }


    function new_to_auto_draft($post) {

        $post_type = $post->post_type;

        if(self::is_for_this_post_type($post_type) && !isset($_REQUEST["folder_for_media"])) {

            $post_type = self::get_custom_post_type($post_type);
            $selected_folder = get_option("selected_{$post_type}_folder");

            if($selected_folder != null && !empty($selected_folder)) {
                $terms = get_term($selected_folder);
                if(!empty($terms) && isset($terms->slug)) {
                    wp_set_object_terms($post->ID, $terms->slug, $post_type );
                }

            }
        }
    }

    public function wcp_folder_send_message_to_owner() {
        $response = array();
        $response['status'] = 0;
        $response['error'] = 0;
        $response['errors'] = array();
        $response['message'] = "";
        $errorArray = [];
        $errorMessage = esc_attr__("%s is required", WCP_FOLDER);
        $postData = filter_input_array(INPUT_POST);
        if(!isset($postData['textarea_text']) || trim($postData['textarea_text']) == "") {
            $error = array(
                "key"   => "textarea_text",
                "message" => esc_attr__("Please enter your message",WCP_FOLDER)
            );
            $errorArray[] = $error;
        }
        if(!isset($postData['user_email']) || trim($postData['user_email']) == "") {
            $error = array(
                "key"   => "user_email",
                "message" => sprintf($errorMessage,__("Email",WCP_FOLDER))
            );
            $errorArray[] = $error;
        } else if(!filter_var($postData['user_email'], FILTER_VALIDATE_EMAIL)) {
            $error = array(
                'key' => "user_email",
                "message" => "Email is not valid"
            );
            $errorArray[] = $error;
        }
        if(empty($errorArray)) {
            if(!isset($postData['folder_help_nonce']) || trim($postData['folder_help_nonce']) == "") {
                $error = array(
                    "key"   => "nonce",
                    "message" => esc_attr__("Your request is not valid", WCP_FOLDER)
                );
                $errorArray[] = $error;
            } else {
                if(!wp_verify_nonce($postData['folder_help_nonce'], 'wcp_folder_help_nonce')) {
                    $error = array(
                        "key"   => "nonce",
                        "message" => esc_attr__("Your request is not valid", WCP_FOLDER)
                    );
                    $errorArray[] = $error;
                }
            }
        }
        if(empty($errorArray)) {
            global $current_user;
            $text_message = self::sanitize_options($postData['textarea_text']);
            $email = self::sanitize_options($postData['user_email'],"email");
            $domain = site_url();
            $user_name = $current_user->first_name." ".$current_user->last_name;
            $subject = "Folder request: ".$domain;
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            $headers .= 'From: '.$user_name.' <'.$email.'>'.PHP_EOL ;
            $headers .= 'Reply-To: '.$user_name.' <'.$email.'>'.PHP_EOL ;
            $headers .= 'X-Mailer: PHP/' . phpversion();
            ob_start();
            ?>
            <table border="0" cellspacing="0" cellpadding="5">
                <tr>
                    <th>Domain</th>
                    <td><?php echo esc_attr($domain) ?></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td><?php echo esc_attr($email) ?></td>
                </tr>
                <tr>
                    <th>Message</th>
                    <td><?php echo esc_attr(nl2br($text_message)) ?></td>
                </tr>
            </table>
            <?php
            $message = ob_get_clean();
            $email_id = "gal@premio.io";
            $status = wp_mail($email_id, $subject, $message, $headers);
            if($status) {
                $response['status'] = 1;
            } else {
                $response['status'] = 0;
                $response['message'] = "Not able to send mail";
            }
        } else {
            $response['error'] = 1;
            $response['errors'] = $errorArray;
        }
        echo json_encode($response);
    }

    public function folder_plugin_deactivate() {
        global $current_user;
        $postData = filter_input_array(INPUT_POST);
        $errorCounter = 0;
        $response = array();
        $response['status'] = 0;
        $response['message'] = "";
        $response['valid'] = 1;
        if(!isset($postData['reason']) || empty($postData['reason'])) {
            $errorCounter++;
            $response['message'] = "Please provide reason";
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] = esc_attr__("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
            $response['valid'] = 0;
        } else {
            $nonce = self::sanitize_options($postData['nonce']);
            if(!wp_verify_nonce($nonce, 'wcp_folder_deactivate_nonce')) {
                $response['message'] = esc_attr__("Your request is not valid", WCP_FOLDER);
                $errorCounter++;
                $response['valid'] = 0;
            }
        }
        if($errorCounter == 0) {
            $reason = $postData['reason'];
            $email = "none@none.none";
            if (isset($postData['email_id']) && !empty($postData['email_id']) && filter_var($postData['email_id'], FILTER_VALIDATE_EMAIL)) {
                $email = $postData['email_id'];
            }
            $domain = site_url();
            $user_name = $current_user->first_name." ".$current_user->last_name;
            $subject = "Folders was removed from {$domain}";
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            $headers .= 'From: '.$user_name.' <'.$email.'>'.PHP_EOL ;
            $headers .= 'Reply-To: '.$user_name.' <'.$email.'>'.PHP_EOL ;
            $headers .= 'X-Mailer: PHP/' . phpversion();
            ob_start();
            ?>
            <table border="0" cellspacing="0" cellpadding="5">
                <tr>
                    <th>Plugin</th>
                    <td>Folders</td>
                </tr>
                <tr>
                    <th>Plugin Version</th>
                    <!-- Free Pro Version Change -->
                    <td><?php echo esc_attr(WCP_FOLDER_VERSION) ?></td>
                </tr>
                <tr>
                    <th>Domain</th>
                    <td><?php echo esc_attr($domain) ?></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td><?php echo esc_attr($email) ?></td>
                </tr>
                <tr>
                    <th>Comment</th>
                    <td><?php echo esc_attr(nl2br($reason)) ?></td>
                </tr>
                <tr>
                    <th>WordPress Version</th>
                    <td><?php echo esc_attr(get_bloginfo('version')) ?></td>
                </tr>
                <tr>
                    <th>PHP Version</th>
                    <td><?php echo esc_attr(PHP_VERSION) ?></td>
                </tr>
            </table>
            <?php
            $content = ob_get_clean();
            $email_id = "gal@premio.io";
            wp_mail($email_id, $subject, $content, $headers);
            $response['status'] = 1;
        }
        echo json_encode($response);
        wp_die();
    }

    public static function ttl_fldrs()
    {
        $post_types = get_option(WCP_FOLDER_VAR);
        $post_types = is_array($post_types)?$post_types:array();
        $total = 0;
        foreach ($post_types as $post_type) {
            $post_type = self::get_custom_post_type($post_type);
            $total += wp_count_terms($post_type);
        }
        return $total;
    }

    public function wcp_remove_post_item()
    {
        $response = array();
        $response['status'] = 0;
        $response['error'] = 0;
        $response['data'] = array();
        $response['message'] = "";
        $postData = filter_input_array(INPUT_POST);
        if (isset($postData['post_id']) && !empty($postData['post_id'])) {
            wp_delete_post($postData['post_id']);
            $response['status'] = 1;
        }
        echo json_encode($response);
        wp_die();
    }

    public function wcp_change_all_status()
    {
        $response = array();
        $response['status'] = 0;
        $response['error'] = 0;
        $response['data'] = array();
        $response['message'] = "";
        $postData = filter_input_array(INPUT_POST);
        $errorCounter = 0;
        if (!isset($postData['type']) || empty($postData['type'])) {
            $response['message'] = esc_attr__("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] = esc_attr__("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else  if (!current_user_can("manage_categories") || ($postData['type'] == "page" && !current_user_can("edit_pages"))) {
            $response['message'] = esc_attr__("You have not permission to update width", WCP_FOLDER);
            $errorCounter++;
        } else if (!current_user_can("manage_categories") || ($postData['type'] != "page" && !current_user_can("edit_posts"))) {
            $response['message'] = esc_attr__("You have not permission to update width", WCP_FOLDER);
            $errorCounter++;
        } else {
            $type = self::sanitize_options($postData['type']);
            $nonce = self::sanitize_options($postData['nonce']);
            if(!wp_verify_nonce($nonce, 'wcp_folder_nonce_'.$type)) {
                $response['message'] = esc_attr__("Your request is not valid", WCP_FOLDER);
                $errorCounter++;
            }
        }
        if ($errorCounter == 0) {
            if (isset($postData['folders']) || !empty($postData['folders'])) {
                $status = isset($postData['status']) ? $postData['status'] : 0;
                $status = self::sanitize_options($status);
                $folders = self::sanitize_options($postData['folders']);
                $folders = trim($folders, ",");
                $folders = explode(",", $folders);
                foreach ($folders as $folder) {
                    update_term_meta($folder, "is_active", $status);
                }
            }
            $response['status'] = 1;
        }
        echo json_encode($response);
        wp_die();
    }

    public function wcp_change_post_width()
    {
        $response = array();
        $response['status'] = 0;
        $response['error'] = 0;
        $response['data'] = array();
        $response['message'] = "";
        $postData = filter_input_array(INPUT_POST);
        $errorCounter = 0;
        if (!isset($postData['width']) || empty($postData['width'])) {
            $response['message'] = esc_attr__("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['type']) || empty($postData['type'])) {
            $response['message'] = esc_attr__("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] = esc_attr__("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else if ($postData['type'] == "page" && !current_user_can("edit_pages")) {
            $response['message'] = esc_attr__("You have not permission to update width", WCP_FOLDER);
            $errorCounter++;
        } else if ($postData['type'] != "page" && !current_user_can("edit_posts")) {
            $response['message'] = esc_attr__("You have not permission to update width", WCP_FOLDER);
            $errorCounter++;
        } else {
            $type = self::sanitize_options($postData['type']);
            $nonce = self::sanitize_options($postData['nonce']);
            if(!wp_verify_nonce($nonce, 'wcp_folder_nonce_'.$type)) {
                $response['message'] = esc_attr__("Your request is not valid", WCP_FOLDER);
                $errorCounter++;
            }
        }
        if ($errorCounter == 0) {
            $type = self::sanitize_options($postData['type']);
            $width = self::sanitize_options($postData['width'], "int");
            $optionName = "wcp_dynamic_width_for_" . $type;
            update_option($optionName, $width);
            $response['status'] = 1;
        }
        echo json_encode($response);
        wp_die();
    }

    public function wcp_change_multiple_post_folder()
    {
        $response = array();
        $response['status'] = 0;
        $response['error'] = 0;
        $response['data'] = array();
        $response['message'] = "";
        $postData = filter_input_array(INPUT_POST);
        $errorCounter = 0;
        if (!isset($postData['post_ids']) || empty($postData['post_ids'])) {
            $response['message'] = esc_attr__("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['folder_id']) || empty($postData['folder_id'])) {
            $response['message'] = esc_attr__("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['type']) || empty($postData['type'])) {
            $response['message'] = esc_attr__("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] = esc_attr__("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else if ($postData['type'] == "page" && !current_user_can("edit_pages")) {
            $response['message'] = esc_attr__("You have not permission to update folder", WCP_FOLDER);
            $errorCounter++;
        } else if ($postData['type'] != "page" && !current_user_can("edit_posts")) {
            $response['message'] = esc_attr__("You have not permission to update folder", WCP_FOLDER);
            $errorCounter++;
        } else {
            $folder_id = self::sanitize_options($postData['folder_id']);
            if(!wp_verify_nonce($postData['nonce'], 'wcp_folder_term_'.$folder_id)) {
                $response['message'] = esc_attr__("Your request is not valid", WCP_FOLDER);
                $errorCounter++;
            }
        }
        if ($errorCounter == 0) {
            $postID = self::sanitize_options($postData['post_ids']);
            $postID = trim($postID, ",");
            $folderID = self::sanitize_options($postData['folder_id']);
            $type = self::sanitize_options($postData['type']);
            $postArray = explode(",", $postID);
            $status = 0;
            if(isset($postData['status'])) {
                $status = self::sanitize_options($postData['status']);
            }
            $status = true;

            $taxonomy = "";
            if(isset($postData['taxonomy'])) {
                $taxonomy = self::sanitize_options($postData['taxonomy']);
            }
            if (is_array($postArray)) {
                $post_type = self::get_custom_post_type($type);
                foreach ($postArray as $post) {
                    $terms = get_the_terms($post, $post_type);
                    if (!empty($terms)) {
                        foreach ($terms as $term) {
                            if(!empty($taxonomy) && ($term->term_id == $taxonomy || $term->slug == $taxonomy)) {
                                wp_remove_object_terms($post, $term->term_id, $post_type);
                            }
                        }
                    }
                    wp_set_post_terms($post, $folderID, $post_type, $status);
                }
            }
            $response['status'] = 1;
        }
        echo json_encode($response);
        wp_die();
    }

    public function wcp_change_post_folder()
    {
        $response = array();
        $response['status'] = 0;
        $response['error'] = 0;
        $response['data'] = array();
        $response['message'] = "";
        $postData = filter_input_array(INPUT_POST);
        $errorCounter = 0;
        if (!isset($postData['post_id']) || empty($postData['post_id'])) {
            $errorCounter++;
            $response['message'] = esc_attr__("Your request is not valid", WCP_FOLDER);
        } else if (!isset($postData['folder_id']) || empty($postData['folder_id'])) {
            $errorCounter++;
            $response['message'] = esc_attr__("Your request is not valid", WCP_FOLDER);
        } else if (!isset($postData['type']) || empty($postData['type'])) {
            $errorCounter++;
            $response['message'] = esc_attr__("Your request is not valid", WCP_FOLDER);
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] = esc_attr__("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else if ($postData['type'] == "page" && !current_user_can("edit_pages")) {
            $response['message'] = esc_attr__("You have not permission to update folder", WCP_FOLDER);
            $errorCounter++;
        } else if ($postData['type'] != "page" && !current_user_can("edit_posts")) {
            $response['message'] = esc_attr__("You have not permission to update folder", WCP_FOLDER);
            $errorCounter++;
        } else {
            $term_id = self::sanitize_options($postData['folder_id']);
            if(!wp_verify_nonce($postData['nonce'], 'wcp_folder_term_'.$term_id)) {
                $response['message'] = esc_attr__("Your request is not valid", WCP_FOLDER);
                $errorCounter++;
            }
        }
        if ($errorCounter == 0) {
            $postID = self::sanitize_options($postData['post_id']);
            $folderID = self::sanitize_options($postData['folder_id']);
            $type = self::sanitize_options($postData['type']);
            $folder_post_type = self::get_custom_post_type($type);
            $status = 0;
            if(isset($postData['status'])) {
                $status = self::sanitize_options($postData['status']);
            }
            $status = ($status == 1)?true:false;
            $taxonomy = "";
            if(isset($postData['taxonomy'])) {
                $taxonomy = self::sanitize_options($postData['taxonomy']);
            }
            $terms = get_the_terms($postID, $folder_post_type);
            if (!empty($terms)) {
                foreach ($terms as $term) {
                    if(!empty($taxonomy) && ($term->term_id == $taxonomy || $term->slug == $taxonomy)) {
                        wp_remove_object_terms($postID, $term->term_id, $folder_post_type);
                    }
                }
            }
            wp_set_post_terms($postID, $folderID, $folder_post_type, true);
            $response['status'] = 1;
        }
        echo json_encode($response);
        wp_die();
    }

    public function wcp_mark_un_mark_folder()
    {
        $response = array();
        $response['status'] = 0;
        $response['error'] = 0;
        $response['data'] = array();
        $response['message'] = "";
        $postData = filter_input_array(INPUT_POST);
        $errorCounter = 0;
        if (!current_user_can("manage_categories")) {
            $response['message'] = esc_attr__("You have not permission to update folder", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['term_id']) || empty($postData['term_id'])) {
            $errorCounter++;
            $response['message'] = esc_attr__("Your request is not valid", WCP_FOLDER);
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] = esc_attr__("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else {
            $term_id = self::sanitize_options($postData['term_id']);
            if(!wp_verify_nonce($postData['nonce'], 'wcp_folder_highlight_term_'.$term_id)) {
                $response['message'] = esc_attr__("Your request is not valid", WCP_FOLDER);
                $errorCounter++;
            }
        }
        if ($errorCounter == 0) {
            $term_id = self::sanitize_options($postData['term_id']);
            $status = get_term_meta($term_id, "is_highlighted", true);
            if ($status == 1) {
                update_term_meta($term_id, "is_highlighted", 0);
                $status = 0;
            } else {
                update_term_meta($term_id, "is_highlighted", 1);
                $status = 1;
            }
            $response['marked'] = $status;
            $response['id'] = $postData['term_id'];
            $response['status'] = 1;
        }
        echo json_encode($response);
        wp_die();
    }

    public function wcp_save_folder_order()
    {
        $response = array();
        $response['status'] = 0;
        $response['error'] = 0;
        $response['data'] = array();
        $response['message'] = "";
        $postData = filter_input_array(INPUT_POST);
        $errorCounter = 0;
        if (!current_user_can("manage_categories")) {
            $response['message'] = esc_attr__("You have not permission to update folder order", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['term_ids']) || empty($postData['term_ids'])) {
            $errorCounter++;
            $response['message'] = esc_attr__("Your request is not valid", WCP_FOLDER);
        } else if (!isset($postData['type']) || empty($postData['type'])) {
            $errorCounter++;
            $response['message'] = esc_attr__("Your request is not valid", WCP_FOLDER);
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] = esc_attr__("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else {
            $type = self::sanitize_options($postData['type']);
            if(!wp_verify_nonce($postData['nonce'], 'wcp_folder_nonce_'.$type)) {
                $response['message'] = esc_attr__("Your request is not valid", WCP_FOLDER);
                $errorCounter++;
            }
        }
        if ($errorCounter == 0) {
            $termIds = self::sanitize_options(($postData['term_ids']));
            $type = self::sanitize_options($postData['type']);
            $termIds = trim($termIds, ",");
            $termArray = explode(",", $termIds);
            $order = 1;
            foreach ($termArray as $term) {
                if (!empty($term)) {
                    update_term_meta($term, "wcp_custom_order", $order);
                    $order++;
                }
            }
            $response['status'] = 1;
            $folder_type = self::get_custom_post_type($type);
            /* Free/Pro Class name change */
            $response['options'] = WCP_Tree::get_option_data_for_select($folder_type);
        }
        echo json_encode($response);
        wp_die();
    }

    public function save_wcp_folder_state()
    {
        $response = array();
        $response['status'] = 0;
        $response['error'] = 0;
        $response['data'] = array();
        $response['message'] = "";
        $postData = filter_input_array(INPUT_POST);
        $errorCounter = 0;
        if (!current_user_can("manage_categories")) {
            $response['message'] = esc_attr__("You have not permission to update folder", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['term_id']) || empty($postData['term_id'])) {
            $response['message'] = esc_attr__("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] = esc_attr__("Unable to create folder, Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else {
            $term_id = self::sanitize_options($postData['term_id']);
            if(!wp_verify_nonce($postData['nonce'], 'wcp_folder_term_'.$term_id)) {
                $response['message'] = esc_attr__("Your request is not valid", WCP_FOLDER);
                $errorCounter++;
            }
        }
        if ($errorCounter == 0) {
            $response['status'] = 1;
            $term_id = self::sanitize_options($postData['term_id']);
            $is_active = isset($postData['is_active'])?$postData['is_active']:0;
            $is_active = self::sanitize_options($is_active);
            if ($is_active == 1) {
                update_term_meta($term_id, "is_active", 1);
            } else {
                update_term_meta($term_id, "is_active", 0);
            }
        }
        echo json_encode($response);
        wp_die();
    }

    public function wcp_update_parent_information()
    {
        $response = array();
        $response['status'] = 0;
        $response['error'] = 0;
        $response['data'] = array();
        $response['message'] = "";
        $postData = filter_input_array(INPUT_POST);
        $errorCounter = 0;
        if (!current_user_can("manage_categories")) {
            $response['message'] = esc_attr__("You have not permission to update folder", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['term_id']) || empty($postData['term_id'])) {
            $response['message'] = esc_attr__("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['type']) || empty($postData['type'])) {
            $response['message'] = esc_attr__("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['parent_id'])) {
            $response['message'] = esc_attr__("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] = esc_attr__("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else {
            $term_id = self::sanitize_options($postData['term_id']);
            if(!wp_verify_nonce($postData['nonce'], 'wcp_folder_term_'.$term_id)) {
                $response['message'] = esc_attr__("Your request is not valid", WCP_FOLDER);
                $errorCounter++;
            }
        }
        if($errorCounter == 0) {
            $term_id = self::sanitize_options($postData['term_id']);
            $parent_id = self::sanitize_options($postData['parent_id']);
            $type = self::sanitize_options($postData['type']);
            $folder_type = self::get_custom_post_type($type);
            wp_update_term($term_id, $folder_type, array(
                'parent' => $parent_id
            ));
            update_term_meta($parent_id, "is_active", 1);
            $response['status'] = 1;
        }
        echo json_encode($response);
        wp_die();
    }

    public function wcp_save_parent_data()
    {
        $response = array();
        $response['status'] = 0;
        $response['error'] = 0;
        $response['data'] = array();
        $response['message'] = "";
        $postData = filter_input_array(INPUT_POST);
        $errorCounter = 0;
        if (!isset($postData['type']) || empty($postData['type'])) {
            $response['message'] = esc_attr__("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] = esc_attr__("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else {
            $type = self::sanitize_options($postData['type']);
            if(!wp_verify_nonce($postData['nonce'], 'wcp_folder_nonce_'.$type)) {
                $response['message'] = esc_attr__("Your request is not valid", WCP_FOLDER);
                $errorCounter++;
            }
        }
        if ($errorCounter == 0) {
            $type = self::sanitize_options($postData['type']);
            $optionName = $type . "_parent_status";
            $response['status'] = 1;
            $is_active = isset($postData['is_active'])?$postData['is_active']:0;
            $is_active = self::sanitize_options($is_active);
            if ($is_active == 1) {
                update_option($optionName, 1);
            } else {
                update_option($optionName, 0);
            }
        }
        echo json_encode($response);
        wp_die();
    }

    public function remove_muliple_folder(){
        $response = array();
        $response['status'] = 0;
        $response['error'] = 0;
        $response['data'] = array();
        $response['message'] = "";
        $postData = filter_input_array(INPUT_POST);
        $errorCounter = 0;
        if (!current_user_can("manage_categories")) {
            $error = esc_attr__("You have not permission to remove folder", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['term_id']) || empty($postData['term_id'])) {
            $error = esc_attr__("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['type']) || empty($postData['type'])) {
            $error = esc_attr__("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        }
        if ($errorCounter == 0) {
            $term_id = self::sanitize_options($postData['term_id']);
            $type = self::sanitize_options($postData['type']);
            if(!empty($term_id)) {
                $term_id = trim($term_id,",");
                $term_ids = explode(",", $term_id);
                if(is_array($term_ids) && count($term_ids) > 0) {
                    foreach ($term_ids as $term) {
                        self::remove_folder_child_items($term, $type);
                    }
                }
            }
            $is_active = 1;
            $folders = -1;
            $response['status'] = 1;
            if (!self::check_has_valid_key()) {
                $is_active = 0;
                $folders = self::ttl_fldrs();
            }
            $response['folders'] = $folders;
            $response['is_key_active'] = $is_active;
        } else {
            $response['error'] = 1;
            $response['message'] = $error;
        }
        echo json_encode($response);
        wp_die();
    }

    public function wcp_remove_folder()
    {
        $response = array();
        $response['status'] = 0;
        $response['error'] = 0;
        $response['data'] = array();
        $response['message'] = "";
        $postData = filter_input_array(INPUT_POST);
        $errorCounter = 0;
        if (!current_user_can("manage_categories")) {
            $error = esc_attr__("You have not permission to remove folder", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['term_id']) || empty($postData['term_id'])) {
            $error = esc_attr__("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['type']) || empty($postData['type'])) {
            $error = esc_attr__("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $error = esc_attr__("Unable to delete folder, Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else {
            $term_id = self::sanitize_options($postData['term_id']);
            if(!wp_verify_nonce($postData['nonce'], 'wcp_folder_delete_term_'.$term_id)) {
                $error = esc_attr__("Unable to delete folder, Your request is not valid", WCP_FOLDER);
                $errorCounter++;
            }
        }
        if ($errorCounter == 0) {
            $term_id = self::sanitize_options($postData['term_id']);
            $type = self::sanitize_options($postData['type']);
            self::remove_folder_child_items($term_id, $type);
            $response['status'] = 1;
            $is_active = 1;
            $folders = -1;
            if (!self::check_has_valid_key()) {
                $is_active = 0;
                $folders = self::ttl_fldrs();
            }
            $response['folders'] = $folders;
            $response['is_key_active'] = $is_active;
        } else {
            $response['error'] = 1;
            $response['message'] = $error;
        }
        echo json_encode($response);
        wp_die();
    }

    public function remove_folder_child_items($term_id, $post_type)
    {
        $folder_type = self::get_custom_post_type($post_type);
        $terms = get_terms($folder_type, array(
            'hide_empty' => false,
            'parent' => $term_id
        ));

        if (!empty($terms)) {
            foreach ($terms as $term) {
                self::remove_folder_child_items($term->term_id, $post_type);
            }
            wp_delete_term($term_id, $folder_type);
        } else {
            wp_delete_term($term_id, $folder_type);
        }
    }

    public function wcp_update_folder()
    {
        $response = array();
        $response['status'] = 0;
        $response['error'] = 0;
        $response['data'] = array();
        $response['message'] = "";
        $postData = $_REQUEST;
        $errorCounter = 0;
        if (!current_user_can("manage_categories")) {
            $error = esc_attr__("You have not permission to update folder", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['term_id']) || empty($postData['term_id'])) {
            $error = esc_attr__("Unable to rename folder, Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['name']) || empty($postData['name'])) {
            $error = esc_attr__("Folder name can no be empty", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['type']) || empty($postData['type'])) {
            $error = esc_attr__("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $error = esc_attr__("Unable to rename folder, Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else {
            $term_id = self::sanitize_options($postData['term_id']);
            if(!wp_verify_nonce($postData['nonce'], 'wcp_folder_rename_term_'.$term_id)) {
                $error = esc_attr__("Unable to rename folder, Your request is not valid", WCP_FOLDER);
                $errorCounter++;
            }
        }
        if ($errorCounter == 0) {
            $type = self::sanitize_options($postData['type']);
            $folder_type = self::get_custom_post_type($type);
            $name = self::sanitize_options($postData['name']);
            $term_id = self::sanitize_options($postData['term_id']);
            $result = wp_update_term(
                $term_id,
                $folder_type,
                array(
                    'name' => $name,
                )
            );
            if (!empty($result)) {
                $response['id'] = $result['term_id'];
                $response['status'] = 1;
                $response['term_title'] = $postData['name'];
            } else {
                $response['message'] = esc_attr__("Unable to rename folder", WCP_FOLDER);
            }
        } else {
            $response['error'] = 1;
            $response['message'] = $error;
        }
        echo json_encode($response);
        wp_die();
    }

    public function create_slug_from_string($str)
    {
        $a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'Ð', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', '?', '?', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', '?', '?', 'L', 'l', 'N', 'n', 'N', 'n', 'N', 'n', '?', 'O', 'o', 'O', 'o', 'O', 'o', 'Œ', 'œ', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'Š', 'š', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Ÿ', 'Z', 'z', 'Z', 'z', 'Ž', 'ž', '?', 'ƒ', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', '?', '?', '?', '?', '?', '?');
        $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
        return strtolower(preg_replace(array('/[^a-zA-Z0-9 -]/', '/[ -]+/', '/^-|-$/'), array('', '-', ''), str_replace($a, $b, $str)));
    }

    public static function sanitize_options($value, $type = "") {
        $value = stripslashes($value);
        if($type == "int") {
            $value = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
        } else if($type == "email") {
            $value = filter_var($value, FILTER_SANITIZE_EMAIL);
        } else {
            $value = filter_var($value, FILTER_SANITIZE_STRING);
        }
        return $value;
    }

    public function wcp_add_new_folder()
    {
        $response = array();
        $response['status'] = 0;
        $response['error'] = 0;
        $response['login'] = 1;
        $response['data'] = array();
        $response['message'] = "";
        $postData = $_REQUEST;
        $errorCounter = 0;
        if (!current_user_can("manage_categories")) {
            $error = esc_attr__("You have not permission to add folder", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['name']) || empty($postData['name'])) {
            $error = esc_attr__("Folder name can no be empty", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['type']) || empty($postData['type'])) {
            $error = esc_attr__("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['login'] = 0;
            $error = esc_attr__("Unable to create folder, Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else {
            $type = self::sanitize_options($postData['type']);
            if(!wp_verify_nonce($postData['nonce'], 'wcp_folder_nonce_'.$type)) {
                $response['login'] = 0;
                $error = esc_attr__("Unable to create folder, Your request is not valid", WCP_FOLDER);
                $errorCounter++;
            }
        }
        if ($errorCounter == 0) {
            $parent = isset($postData['parent_id']) && !empty($postData['parent_id']) ? $postData['parent_id'] : 0;
            $parent = self::sanitize_options($parent);
            $type = self::sanitize_options($postData['type']);
            $folder_type = self::get_custom_post_type($type);
            $term_name = self::sanitize_options($postData['name']);
            $term = term_exists($term_name, $folder_type, $parent);
            if (!(0 !== $term && null !== $term)) {
                $slug = self::create_slug_from_string($postData['name']) . "-" . time();
                $result = wp_insert_term(
                    $postData['name'], // the term
                    $folder_type, // the taxonomy
                    array(
                        'parent' => $parent,
                        'slug' => $slug
                    )
                );
                if (!empty($result)) {
                    $response['id'] = $result['term_id'];
                    $response['status'] = 1;
                    $order = isset($postData['order']) ? $postData['order'] : 0;
                    $order = self::sanitize_options($order);
                    update_term_meta($result['term_id'], "wcp_custom_order", $order);
                    if ($parent != 0) {
                        update_term_meta($parent, "is_active", 1);
                    }
                    $delete_nonce = wp_create_nonce('wcp_folder_delete_term_'.$result['term_id']);
                    $rename_nonce = wp_create_nonce('wcp_folder_rename_term_'.$result['term_id']);
                    $highlight_nonce = wp_create_nonce('wcp_folder_highlight_term_'.$result['term_id']);
                    $term_nonce = wp_create_nonce('wcp_folder_term_'.$result['term_id']);
                    /* Free/Pro URL Change */
                    $string = "<li data-nonce='{$term_nonce}' data-star='{$highlight_nonce}' data-rename='{$rename_nonce}' data-delete='{$delete_nonce}' data-slug='{$slug}' class='ui-state-default route' id='wcp_folder_{$result['term_id']}' data-folder-id='{$result['term_id']}'><h3 class='title' title='{$postData['name']}' id='title_{$result['term_id']}'><span class='ui-icon'><i class='wcp-icon folder-icon-folder'></i><img src='".esc_url(WCP_FOLDER_URL."assets/images/move-option.png")."' class='move-folder-icon' ><input type='checkbox' class='checkbox' value='{$result['term_id']}' /></span><span class='title-text'>{$postData['name']}</span> <span class='update-inline-record'></span><span class='star-icon'></span> </h3><span class='nav-icon'><i class='wcp-icon folder-icon-arrow_right'></i></span>	<ul class='space' id='space_{$result['term_id']}'>";
                    $string .= "</ul></li>";
                    $response['term_data'] = $string;
                    $response['parent_id'] = $parent;
                    $response['term_id'] = $result['term_id'];

                    $is_active = 1;
                    $folders = -1;
                    if (!self::check_has_valid_key()) {
                        $is_active = 0;
                        $folders = self::ttl_fldrs();
                    }
                    $response['is_key_active'] = $is_active;
                    $response['folders'] = $folders;
                } else {
                    $response['message'] = esc_attr__("Error during server request", WCP_FOLDER);
                }
            } else {
                $response['error'] = 1;
                $response['message'] = esc_attr__("Folder name already exists", WCP_FOLDER);
            }
        } else {
            $response['error'] = 1;
            $response['message'] = $error;
        }
        echo json_encode($response);
        wp_die();
    }

    public function is_for_this_post_type($post_type)
    {
        $post_types = get_option(WCP_FOLDER_VAR);
        $post_types = is_array($post_types)?$post_types:array();
        return in_array($post_type, $post_types);
    }

    public function is_active_for_screen()
    {

        global $typenow, $current_screen;

        $postData = filter_input_array(INPUT_POST);

        if ((isset($postData['action']) && $postData['action'] == 'inline-save') && (isset($postData['post_type']) && self::is_for_this_post_type($postData['post_type']))) {
            return true;
        }
        global $current_screen;

        if (self::is_for_this_post_type($typenow) && ('edit' == $current_screen->base || 'upload' == $current_screen->base)) {
            return true;
        }

        $post_types = get_option(WCP_FOLDER_VAR);
        $post_types = is_array($post_types)?$post_types:array();

        if(empty($typenow) && 'upload' == $current_screen->base ) {
            $typenow = "attachment";
            if (self::is_for_this_post_type($typenow)) {
                return true;
            }
        }
        return false;
    }

    public function is_add_update_screen()
    {
        global $current_screen;
        $current_type = $current_screen->base;
        $action = $current_screen->action;
        $post_types = get_option(WCP_FOLDER_VAR);
        $post_types = is_array($post_types)?$post_types:array();
        global $typenow;
        if (in_array($current_type, $post_types) && in_array($action, array("add", ""))) {
            $license_data = self::get_license_key_data();

            $is_active = 1;
            $folders = -1;
            if (!self::check_has_valid_key()) {
                $is_active = 0;
                $folders = self::ttl_fldrs();
            }
            $response['folders'] = $folders;
            $response['is_key_active'] = $is_active;
        }
    }

    public static function get_custom_post_type($post_type)
    {
        if ($post_type == "post") {
            return "post_folder";
        } else if ($post_type == "page") {
            return "folder";
        } else if ($post_type == "attachment") {
            return "media_folder";
        }
        return $post_type . '_folder';
    }

    public function admin_footer()
    {
        if (self::is_active_for_screen()) {
            global $typenow;

            self::set_default_values_if_not_exists();

            $ttpsts = self::get_ttlpst($typenow);

            $ttemp = self::get_tempt_posts($typenow);

            $folder_type = self::get_custom_post_type($typenow);
            /* Do not change: Free/Pro Class name change */
            $terms_data = WCP_Tree::get_full_tree_data($folder_type);
            $terms_html = WCP_Tree::get_option_data_for_select($folder_type);
            $form_html = WCP_Forms::get_form_html($terms_html);
            include_once dirname(dirname(__FILE__)) . WCP_DS . "/templates" . WCP_DS . "admin" . WCP_DS . "admin-content.php";
        }

        global $pagenow;
        if ( 'plugins.php' !== $pagenow ) {

        } else {
            include_once dirname(dirname(__FILE__)) . WCP_DS . "/templates" . WCP_DS . "admin" . WCP_DS . "folder-deactivate-form.php";
        }
    }

    public function get_ttlpst($post_type = "")
    {
        global $typenow;
        if ($post_type == "") {
            $post_type = $typenow;
        }
        if ($typenow == "attachment") {
            return wp_count_posts($post_type)->inherit;
        } else {
            return wp_count_posts($post_type)->publish + wp_count_posts($post_type)->draft + wp_count_posts($post_type)->future + wp_count_posts($post_type)->private;
        }
    }

    public function autoload()
    {
        $files = array(
            'WCP_Tree_View' => WCP_DS . "includes" . WCP_DS . "tree.class.php",
            'WCP_Form_View' => WCP_DS . "includes" . WCP_DS . "form.class.php",
        );

        foreach ($files as $file) {
            if (file_exists(dirname(dirname(__FILE__)) . $file)) {
                include_once dirname(dirname(__FILE__)) . $file;
            }
        }
    }

    public function create_folder_terms()
    {
        $options = get_option(WCP_FOLDER_VAR);
        $options = is_array($options)?$options:array();
        $old_plugin_status = 0;
        $posts = array();
        if (!empty($options)) {
            foreach ($options as $option) {
                if (!(strpos($option, 'folder4') === false) && $old_plugin_status == 0) {
                    $old_plugin_status = 1;
                }
                if (in_array($option, array("page", "post", "attachment"))) {
                    $posts[] = str_replace("folder4", "", $option);
                } else {
                    $posts[] = $option;
                }
            }
            if(!empty($posts)) {
                update_option(WCP_FOLDER_VAR, $posts);
            }
        }
        if ($old_plugin_status == 1) {
            update_option("folders_show_in_menu", "on");
            $old_plugin_var = get_option("folder_old_plugin_status");
            if (empty($old_plugin_var) || $old_plugin_var == null) {
                update_option("folder_old_plugin_status", "1");
            }
        }
        $posts = get_option(WCP_FOLDER_VAR);
        if (!empty($posts)) {
            foreach ($posts as $post_type) {
                $labels = array(
                    'name' => esc_html__('Folders', WCP_FOLDER),
                    'singular_name' => esc_html__('Folder', WCP_FOLDER),
                    'all_items' => esc_html__('All Folders', WCP_FOLDER),
                    'edit_item' => esc_html__('Edit Folder', WCP_FOLDER),
                    'update_item' => esc_html__('Update Folder', WCP_FOLDER),
                    'add_new_item' => esc_html__('Add New Folder', WCP_FOLDER),
                    'new_item_name' => esc_html__('Add Folder Name', WCP_FOLDER),
                    'menu_name' => esc_html__('Folders', WCP_FOLDER),
                    'search_items' => esc_html__('Search Folders', WCP_FOLDER),
                    'parent_item' => esc_html__('Parent Folder', WCP_FOLDER),
                );

                $args = array(
                    'label' => esc_html__('Folder', WCP_FOLDER),
                    'labels' => $labels,
                    'show_tagcloud' => false,
                    'hierarchical' => true,
                    'public' => false,
                    'show_ui' => true,
                    'show_in_menu' => false,
                    'show_in_rest' => true,
                    'show_admin_column' => true,
                    'update_count_callback' => '_update_generic_term_count',
                    'query_var' => true,
                    'rewrite' => false
                );

                $folder_post_type = self::get_custom_post_type($post_type);

                register_taxonomy(
                    $folder_post_type,
                    $post_type,
                    $args
                );
            }
        }

        $postData = filter_input_array(INPUT_POST);

        if(current_user_can("manage_categories")) {
            if (isset($postData['folders_show_in_menu']) && !empty($postData['folders_show_in_menu'])) {
                $show_menu = "off";
                if ($postData['folders_show_in_menu'] == "on") {
                    $show_menu = "on";
                }
                update_option("folders_show_in_menu", $show_menu);
            }

            if (isset($postData['folders_settings1'])) {
                $posts = array();
                if (isset($postData['folders_settings']) && is_array($postData['folders_settings'])) {
                    foreach ($postData['folders_settings'] as $key => $val) {
                        $posts[] = $val;
                    }
                }
                update_option("folders_settings", $posts);
            }

            if (isset($_POST['folders_settings1'])) {
                $posts = array();
                if (isset($_POST['default_folders']) && is_array($_POST['default_folders'])) {
                    foreach ($_POST['default_folders'] as $key => $val) {
                        $posts[$key] = $val;
                    }
                }
                update_option("default_folders", $posts);
            }

            if (isset($_POST['folders_settings1'])) {
                $posts = array();
                if (isset($_POST['customize_folders']) && is_array($_POST['customize_folders'])) {
                    foreach ($_POST['customize_folders'] as $key => $val) {
                        $posts[$key] = $val;
                    }
                }
                update_option("customize_folders", $posts);
            }
        }

        $old_version = get_option("folder_old_plugin_status");
        if($old_version !== false && $old_version == 1) {
            $tlfs = get_option("folder_old_plugin_folder_status");
            if($tlfs === false) {
                $total = self::ttl_fldrs();
                if($total <= 10) {
                    $total = 10;
                };
                update_option("folder_old_plugin_folder_status", $total);
                self::$folders = $total;
            } else {
                self::$folders = $tlfs;
            }
        }

        $tlfs = get_option("folder_old_plugin_folder_status");
        if($tlfs === false) {
            self::$folders = 10;
        } else {
            self::$folders = $tlfs;
        }
    }

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
    }

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

            $folder = $type == 'attachment' ? 'media' : $type;
            $upper = $type == 'attachment' ? 'Media' : ucwords(str_replace(array('-', '_'), ' ', $type));
            if ($type == 'page') {
                $tax_slug = 'folder';
            } else {
                $tax_slug = $folder . '_folder';
            }


            if ($type == 'attachment') {
                add_menu_page('Media Folders', 'Media Folders', 'publish_pages', "{$edit}?type=folder", false, 'dashicons-portfolio', "{$itemKey}.5");
            } else {
                add_menu_page($upper . ' Folders', "{$upper} Folders", 'publish_pages', "{$edit}?post_type={$type}&type=folder", false, 'dashicons-portfolio', "{$itemKey}.5");
            }
            $terms = get_terms($tax_slug, array(
                    'hide_empty' => true,
                    'parent'   => 0,
                    'orderby' => 'meta_value_num',
                    'order' => 'ASC',
                    'hierarchical' => false,
                    'meta_query' => [[
                        'key' => 'wcp_custom_order',
                        'type' => 'NUMERIC',
                    ]]
                )
            );

            if ($terms) {
                foreach ($terms as $term) {
                    if ($type == 'attachment') {
                        add_submenu_page("{$edit}?type=folder", $term->name, $term->name, 'publish_pages', "{$edit}?post_type=attachment&media_folder={$term->slug}", false);
                    } else {
                        add_submenu_page("{$edit}?post_type={$type}&type=folder", $term->name, $term->name, 'publish_pages', "{$edit}?post_type={$type}&{$tax_slug}={$term->slug}", false);
                    }
                }
            }
        }
    }

    function folders_admin_styles()
    {
        if (self::is_active_for_screen()) {
            wp_register_style('wcp-folders-fa', plugin_dir_url(dirname(__FILE__)) . 'assets/css/folder-icon.css', array(), WCP_FOLDER_VERSION);
            wp_enqueue_style('wcp-folders-fa');
            wp_register_style('wcp-folders-admin', plugin_dir_url(dirname(__FILE__)) . 'assets/css/design.css', array(), WCP_FOLDER_VERSION);
            wp_enqueue_style('wcp-folders-admin');
        }
        wp_register_style('wcp-css-handle', false);
        wp_enqueue_style('wcp-css-handle');
        $css = "
				.wcp-folder-upgrade-button {color: #FF5983; font-weight: bold;}
			";
        if (self::is_active_for_screen()) {
            global $typenow;
            $width = get_option("wcp_dynamic_width_for_" . $typenow);
            $width = esc_attr($width);
            $display_status = "wcp_dynamic_display_status_" . $typenow;
            $display_status = get_option($display_status);
            if($display_status != "hide") {
                if (!empty($width) && is_numeric($width)) {
                    $css .= ".wcp-content{width:{$width}px}";
                    if (function_exists('is_rtl') && is_rtl()) {
                        $css .= "html[dir='rtl']  body.wp-admin #wpcontent {padding-right:" . ($width + 20) . "px}";
                        $css .= "html[dir='rtl'] body.wp-admin #wpcontent {padding-left:0px}";
                    } else {
                        $css .= "body.wp-admin #wpcontent {padding-left:" . ($width + 20) . "px}";
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
        }
        wp_add_inline_style('wcp-css-handle', $css);

        if (self::is_active_for_screen()) {
            global $typenow;
            add_filter('views_edit-' . $typenow, array($this, 'wcp_check_for_child_folders'));
        }
    }

    function wcp_check_for_child_folders($content)
    {
        $termId = 0;
        global $typenow;
        $post_type = self::get_custom_post_type($typenow);
        if (isset($_GET[$post_type]) && !empty($_GET[$post_type])) {
            $term = $_GET[$post_type];
            $term = get_term_by("slug", $term, $post_type);
            if (!empty($term)) {
                $termId = $term->term_id;
            }
        }
        $terms = get_terms($post_type, array(
            'hide_empty' => false,
            'parent' => $termId,
            'orderby' => 'meta_value_num',
            'order' => 'ASC',
            'hierarchical' => false,
            'update_count_callback' => '_update_generic_term_count',
            'meta_query' => [[
                'key' => 'wcp_custom_order',
                'type' => 'NUMERIC',
            ]]
        ));
        $optionName = "wcp_folder_display_status_" . $typenow;
        $optionValue = get_option($optionName);
        $class = (!empty($optionValue) && $optionValue == "hide")?"":"active";
        echo '<div class="tree-structure-content '.$class.'"><div class="tree-structure" id="list-folder-' . $termId . '" data-id="' . $termId . '">';
        echo '<ul>';
        foreach ($terms as $term) {
            $status = get_term_meta($term->term_id, "is_highlighted", true);
            ?>
            <li class="grid-view" data-id="<?php echo $term->term_id ?>" id="folder_<?php echo $term->term_id ?>">
                <div class="folder-item is-folder" data-id="<?php echo $term->term_id ?>">
                    <a title='<?php echo $term->name ?>' id="folder_view_<?php echo $term->term_id ?>"
                       class="folder-view <?php echo ($status == 1) ? "is-high" : "" ?>"
                       data-id="<?php echo $term->term_id ?>">
                        <span class="folder item-name"><span id="wcp_folder_text_<?php echo $term->term_id ?>"
                                                             class="folder-title"><?php echo $term->name ?></span></span>
                        <!--<span class="folder-option"></span>-->
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
        if(!empty($content) && is_array($content)) {
            echo '<ul class="subsubsub">';
            foreach($content as $k=>$v) {
                echo "<li class='{$k}'>{$v}</li>";
            }
            echo '</ul>';
        }
    }

    function folders_admin_scripts()
    {
        if (self::is_active_for_screen()) {
            global $typenow;
            /* Free/Pro Version change */
            wp_register_script('wcp-folders-alert', plugin_dir_url(dirname(__FILE__)) . 'assets/js/livequery.min.js', array(), WCP_FOLDER_VERSION);
            wp_register_script('wcp-folders-custom', plugin_dir_url(dirname(__FILE__)) . 'assets/js/custom.js', array('jquery', 'jquery-ui-resizable', 'jquery-ui-draggable', 'jquery-ui-droppable', 'jquery-ui-sortable', 'backbone'), WCP_FOLDER_VERSION);

            if ($typenow == "attachment") {
                $admin_url = admin_url("upload.php?post_type=attachment&media_folder=");
            } else {
                $admin_url = admin_url("edit.php?post_type=" . $typenow);
                if (isset($_GET['s']) && !empty($_GET['s'])) {
                    $admin_url .= "&s=" . $_GET['s'];
                }
                $post_type = self::get_custom_post_type($typenow);
                $admin_url .= "&{$post_type}=";
            }

            $is_active = 1;
            $folders = -1;
            if (!self::check_has_valid_key()) {
                $is_active = 0;
                $folders = self::ttl_fldrs();
            }
            /* For free: upgrade URL, for Pro: Register Key URL */
            $register_url = admin_url("admin.php?page=wcp_folders_upgrade");

            $is_rtl = 0;
            if ( function_exists( 'is_rtl' ) && is_rtl() ) {
                $is_rtl = 1;
            }

            $can_manage_folder = current_user_can("manage_categories")?1:0;
            $width = get_option("wcp_dynamic_width_for_" . $typenow);
            $width = empty($width)||!is_numeric($width)?310:$width;
            $post_type = self::get_custom_post_type($typenow);
            $taxonomy_status = 0;
            $selected_taxonomy = "";
            if(!isset($_GET[$post_type]) || empty($_GET[$post_type])) {
                $taxonomy_status = 1;
            } else if(isset($_GET[$post_type]) && !empty($_GET[$post_type])) {
                $selected_taxonomy = $_GET[$post_type];
            }
            wp_localize_script('wcp-folders-custom', 'wcp_settings', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'upgrade_url' => admin_url('admin.php?page=wcp_folders_upgrade'),
                'post_type' => $typenow,
                'page_url' => $admin_url,
                'ajax_image' => plugin_dir_url(dirname(__FILE__)) . "assets/images/ajax-loader.gif",
                'is_key_active' => $is_active,
                'folders' => $folders,
                'register_url' => $register_url,
                'isRTL' => $is_rtl,
                'nonce' => wp_create_nonce('wcp_folder_nonce_'.$typenow),
                'can_manage_folder' => $can_manage_folder,
                'folder_width' => $width,
                'taxonomy_status' => $taxonomy_status,
                'selected_taxonomy' => $selected_taxonomy
            ));
            wp_enqueue_script('wcp-folders-alert');
            wp_enqueue_script('wcp-folders-custom');

        } else {
            self::is_add_update_screen();
        }
    }

    public function plugin_action_links($links)
    {
        array_unshift($links, '<a href="' . admin_url("admin.php?page=wcp_folders_settings") . '" >' . esc_html__('Settings', WCP_FOLDER) . '</a>');
        $links['need_help'] = '<a target="_blank" href="https://premio.io/help/folders/?utm_source=pluginspage" >'.__( 'Need help?', WCP_FOLDER).'</a>';

        /* PRO link for only for FREE*/
        $links['pro'] = '<a class="wcp-folder-upgrade-button" href="'.admin_url("admin.php?page=wcp_folders_upgrade").'" >'.__( 'Upgrade', WCP_FOLDER).'</a>';
        return $links;
    }

    public static function get_instance()
    {
        if (empty(self::$instance)) {
            /* Do not change Class name here */
            self::$instance = new WCP_Folders();
        }
        return self::$instance;
    }

    public function check_and_set_post_type() {
        $options = get_option(WCP_FOLDER_VAR);
        $old_plugin_status = 0;
        $post_array = array();
        if (!empty($options) && is_array($options)) {
            foreach ($options as $key=>$val) {
                if (!(strpos($key, 'folders4') === false) && $old_plugin_status == 0) {
                    $old_plugin_status = 1;
                }
                if (in_array($key, array("folders4page", "folders4post", "folders4attachment"))) {
                    $post_array[] = str_replace("folders4", "", $key);
                }
            }
        } else {
            $post_array = array("page", "post", "attachment");
        }
        if ($old_plugin_status == 1) {
            update_option("folders_show_in_menu", "on");
            $old_plugin_var = get_option("folder_old_plugin_status");
            if (empty($old_plugin_var) || $old_plugin_var == null) {
                update_option("folder_old_plugin_status", "1");
            }
            update_option(WCP_FOLDER_VAR, $post_array);
            self::set_default_values_if_not_exists();
        }
        if (!empty($post_array) && get_option(WCP_FOLDER_VAR) === false) {
            update_option(WCP_FOLDER_VAR, $post_array);
            update_option("folders_show_in_menu", "off");
        }
    }

    public static function activate()
    {
        update_option("folders_show_in_menu", "off");
        update_option("folder_redirect_status", 1);
    }

    public static function get_ttl_fldrs()
    {
        return self::ttl_fldrs();
    }

    function folders_register_settings()
    {
        register_setting('folders_settings', 'folders_settings1', 'folders_settings_validate');
        register_setting('default_folders', 'default_folders');
        register_setting('customize_folders', 'customize_folders');

        self::check_and_set_post_type();

        $option = get_option("folder_redirect_status");
        if ($option == 1) {
            update_option("folder_redirect_status", 2);
            wp_redirect(admin_url("admin.php?page=wcp_folders_settings"));
            exit;
        }
    }

    function wcp_manage_columns_head($defaults, $d = "")
    {
        global $typenow;
        $type = $typenow;
        if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'inline-save') {
            $type = self::sanitize_options($_REQUEST['post_type']);
        }

        $options = get_option("folders_settings");
        if (is_array($options) && in_array($type, $options)) {
            $columns = array(
                    'wcp_move' => '<div class="wcp-move-multiple wcp-col" title="' . esc_attr__('Move selected items', WCP_FOLDER) . '"><span class="dashicons dashicons-move"></span><div class="wcp-items"></div></div>',
                ) + $defaults;
            return $columns;
        }
        return $defaults;
    }

    function wcp_manage_columns_content($column_name, $post_ID)
    {
        $postIDs = self::$postIds;
        if(!is_array($postIDs)) {
            $postIDs = array();
        }
        if(!in_array($post_ID, $postIDs)) {
            $postIDs[] = $post_ID;
            self::$postIds = $postIDs;
            global $typenow;
            $type = $typenow;
            if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'inline-save') {
                $type = self::sanitize_options($_REQUEST['post_type']);
            }

            $options = get_option("folders_settings");
            if (is_array($options) && in_array($type, $options)) {
                if ($column_name == 'wcp_move') {
                    $title = get_the_title();
                    if (strlen($title) > 20) {
                        $title = substr($title, 0, 20) . "...";
                    }
                    echo "<div class='wcp-move-file' data-id='{$post_ID}'><span class='wcp-move dashicons dashicons-move' data-id='{$post_ID}'></span><span class='wcp-item' data-object-id='{$post_ID}'>" . $title . "</span></div>";
                }
            }
        }
    }

    function taxonomy_archive_exclude_children($query)
    {
        $options = get_option("folders_settings");
        if (!empty($options)) {
            $taxonomy_slugs = array();
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
    }

    public function admin_menu()
    {
        // Add menu item for settings page
        $page_title = esc_attr__('Folders', WCP_FOLDER);
        $menu_title = esc_attr__('Folders Settings', WCP_FOLDER);
        $capability = 'manage_options';
        $menu_slug = 'wcp_folders_settings';
        $callback = array($this, "wcp_folders_settings");
        $icon_url = 'dashicons-category';
        $position = 99;

        add_menu_page($page_title, $menu_title, $capability, $menu_slug, $callback, $icon_url, $position);

        /* Do not Change Free/Pro Change for menu */
        add_submenu_page(
            $menu_slug,
            esc_html__('Upgrade to Pro', WCP_FOLDER),
            esc_html__('Upgrade to Pro', WCP_FOLDER),
            'manage_options',
            'wcp_folders_upgrade',
            array($this, 'wcp_folders_upgrade_or_register')
        );

        self::check_and_set_post_type();

        $show_menu = get_option("folders_show_in_menu", true);
        if ($show_menu == "on") {
            self::create_menu_for_folders();
        }
    }

    public function wcp_folders_upgrade_or_register()
    {
        self::set_default_values_if_not_exists();
        include_once dirname(dirname(__FILE__)) . "/templates/admin/upgrade-to-pro.php";
    }

    public function wcp_folders_settings()
    {
        self::set_default_values_if_not_exists();
        /* Only in Free, Get Folders update confirmation popup */
        $is_shown = get_option("folder_update_message");
        if($is_shown === false) {
            include_once dirname(dirname(__FILE__)) . "/templates/admin/update.php";
        } else {
            $options = get_option('folders_settings');
            $options = (empty($options) || !is_array($options)) ?array():$options;
            $post_types = get_post_types( array( 'public' => true ), 'objects' );
            $terms_data = array();
            foreach ($post_types as $post_type) {
                if(in_array($post_type->name, $options)) {
                    $term = $post_type->name;
                    $term = self::get_custom_post_type($term);
                    $categories = self::get_terms_hierarchical($term);
                    $terms_data[$post_type->name] = $categories;
                } else {
                    $terms_data[$post_type->name] = array();
                }
            }
            $fonts = self::get_font_list();
            include_once dirname(dirname(__FILE__)) . "/templates/admin/general-settings.php";
        }
    }

    public static function get_font_list(){
        return array(
            // System fonts.
            '-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif;' => 'Default',
            'Arial' => 'Default',
            'Tahoma' => 'Default',
            'Verdana' => 'Default',
            'Helvetica' => 'Default',
            'Times New Roman' => 'Default',
            'Trebuchet MS' => 'Default',
            'Georgia' => 'Default',

            // Google Fonts (last update: 23/10/2018).
            'ABeeZee' => 'Google Fonts',
            'Abel' => 'Google Fonts',
            'Abhaya Libre' => 'Google Fonts',
            'Abril Fatface' => 'Google Fonts',
            'Aclonica' => 'Google Fonts',
            'Acme' => 'Google Fonts',
            'Actor' => 'Google Fonts',
            'Adamina' => 'Google Fonts',
            'Advent Pro' => 'Google Fonts',
            'Aguafina Script' => 'Google Fonts',
            'Akronim' => 'Google Fonts',
            'Aladin' => 'Google Fonts',
            'Aldrich' => 'Google Fonts',
            'Alef' => 'Google Fonts',
            'Alef Hebrew' => 'Google Fonts', // Hack for Google Early Access.
            'Alegreya' => 'Google Fonts',
            'Alegreya SC' => 'Google Fonts',
            'Alegreya Sans' => 'Google Fonts',
            'Alegreya Sans SC' => 'Google Fonts',
            'Alex Brush' => 'Google Fonts',
            'Alfa Slab One' => 'Google Fonts',
            'Alice' => 'Google Fonts',
            'Alike' => 'Google Fonts',
            'Alike Angular' => 'Google Fonts',
            'Allan' => 'Google Fonts',
            'Allerta' => 'Google Fonts',
            'Allerta Stencil' => 'Google Fonts',
            'Allura' => 'Google Fonts',
            'Almendra' => 'Google Fonts',
            'Almendra Display' => 'Google Fonts',
            'Almendra SC' => 'Google Fonts',
            'Amarante' => 'Google Fonts',
            'Amaranth' => 'Google Fonts',
            'Amatic SC' => 'Google Fonts',
            'Amethysta' => 'Google Fonts',
            'Amiko' => 'Google Fonts',
            'Amiri' => 'Google Fonts',
            'Amita' => 'Google Fonts',
            'Anaheim' => 'Google Fonts',
            'Andada' => 'Google Fonts',
            'Andika' => 'Google Fonts',
            'Angkor' => 'Google Fonts',
            'Annie Use Your Telescope' => 'Google Fonts',
            'Anonymous Pro' => 'Google Fonts',
            'Antic' => 'Google Fonts',
            'Antic Didone' => 'Google Fonts',
            'Antic Slab' => 'Google Fonts',
            'Anton' => 'Google Fonts',
            'Arapey' => 'Google Fonts',
            'Arbutus' => 'Google Fonts',
            'Arbutus Slab' => 'Google Fonts',
            'Architects Daughter' => 'Google Fonts',
            'Archivo' => 'Google Fonts',
            'Archivo Black' => 'Google Fonts',
            'Archivo Narrow' => 'Google Fonts',
            'Aref Ruqaa' => 'Google Fonts',
            'Arima Madurai' => 'Google Fonts',
            'Arimo' => 'Google Fonts',
            'Arizonia' => 'Google Fonts',
            'Armata' => 'Google Fonts',
            'Arsenal' => 'Google Fonts',
            'Artifika' => 'Google Fonts',
            'Arvo' => 'Google Fonts',
            'Arya' => 'Google Fonts',
            'Asap' => 'Google Fonts',
            'Asap Condensed' => 'Google Fonts',
            'Asar' => 'Google Fonts',
            'Asset' => 'Google Fonts',
            'Assistant' => 'Google Fonts',
            'Astloch' => 'Google Fonts',
            'Asul' => 'Google Fonts',
            'Athiti' => 'Google Fonts',
            'Atma' => 'Google Fonts',
            'Atomic Age' => 'Google Fonts',
            'Aubrey' => 'Google Fonts',
            'Audiowide' => 'Google Fonts',
            'Autour One' => 'Google Fonts',
            'Average' => 'Google Fonts',
            'Average Sans' => 'Google Fonts',
            'Averia Gruesa Libre' => 'Google Fonts',
            'Averia Libre' => 'Google Fonts',
            'Averia Sans Libre' => 'Google Fonts',
            'Averia Serif Libre' => 'Google Fonts',
            'Bad Script' => 'Google Fonts',
            'Bahiana' => 'Google Fonts',
            'Bai Jamjuree' => 'Google Fonts',
            'Baloo' => 'Google Fonts',
            'Baloo Bhai' => 'Google Fonts',
            'Baloo Bhaijaan' => 'Google Fonts',
            'Baloo Bhaina' => 'Google Fonts',
            'Baloo Chettan' => 'Google Fonts',
            'Baloo Da' => 'Google Fonts',
            'Baloo Paaji' => 'Google Fonts',
            'Baloo Tamma' => 'Google Fonts',
            'Baloo Tammudu' => 'Google Fonts',
            'Baloo Thambi' => 'Google Fonts',
            'Balthazar' => 'Google Fonts',
            'Bangers' => 'Google Fonts',
            'Barlow' => 'Google Fonts',
            'Barlow Condensed' => 'Google Fonts',
            'Barlow Semi Condensed' => 'Google Fonts',
            'Barrio' => 'Google Fonts',
            'Basic' => 'Google Fonts',
            'Battambang' => 'Google Fonts',
            'Baumans' => 'Google Fonts',
            'Bayon' => 'Google Fonts',
            'Belgrano' => 'Google Fonts',
            'Bellefair' => 'Google Fonts',
            'Belleza' => 'Google Fonts',
            'BenchNine' => 'Google Fonts',
            'Bentham' => 'Google Fonts',
            'Berkshire Swash' => 'Google Fonts',
            'Bevan' => 'Google Fonts',
            'Bigelow Rules' => 'Google Fonts',
            'Bigshot One' => 'Google Fonts',
            'Bilbo' => 'Google Fonts',
            'Bilbo Swash Caps' => 'Google Fonts',
            'BioRhyme' => 'Google Fonts',
            'BioRhyme Expanded' => 'Google Fonts',
            'Biryani' => 'Google Fonts',
            'Bitter' => 'Google Fonts',
            'Black And White Picture' => 'Google Fonts',
            'Black Han Sans' => 'Google Fonts',
            'Black Ops One' => 'Google Fonts',
            'Bokor' => 'Google Fonts',
            'Bonbon' => 'Google Fonts',
            'Boogaloo' => 'Google Fonts',
            'Bowlby One' => 'Google Fonts',
            'Bowlby One SC' => 'Google Fonts',
            'Brawler' => 'Google Fonts',
            'Bree Serif' => 'Google Fonts',
            'Bubblegum Sans' => 'Google Fonts',
            'Bubbler One' => 'Google Fonts',
            'Buda' => 'Google Fonts',
            'Buenard' => 'Google Fonts',
            'Bungee' => 'Google Fonts',
            'Bungee Hairline' => 'Google Fonts',
            'Bungee Inline' => 'Google Fonts',
            'Bungee Outline' => 'Google Fonts',
            'Bungee Shade' => 'Google Fonts',
            'Butcherman' => 'Google Fonts',
            'Butterfly Kids' => 'Google Fonts',
            'Cabin' => 'Google Fonts',
            'Cabin Condensed' => 'Google Fonts',
            'Cabin Sketch' => 'Google Fonts',
            'Caesar Dressing' => 'Google Fonts',
            'Cagliostro' => 'Google Fonts',
            'Cairo' => 'Google Fonts',
            'Calligraffitti' => 'Google Fonts',
            'Cambay' => 'Google Fonts',
            'Cambo' => 'Google Fonts',
            'Candal' => 'Google Fonts',
            'Cantarell' => 'Google Fonts',
            'Cantata One' => 'Google Fonts',
            'Cantora One' => 'Google Fonts',
            'Capriola' => 'Google Fonts',
            'Cardo' => 'Google Fonts',
            'Carme' => 'Google Fonts',
            'Carrois Gothic' => 'Google Fonts',
            'Carrois Gothic SC' => 'Google Fonts',
            'Carter One' => 'Google Fonts',
            'Catamaran' => 'Google Fonts',
            'Caudex' => 'Google Fonts',
            'Caveat' => 'Google Fonts',
            'Caveat Brush' => 'Google Fonts',
            'Cedarville Cursive' => 'Google Fonts',
            'Ceviche One' => 'Google Fonts',
            'Chakra Petch' => 'Google Fonts',
            'Changa' => 'Google Fonts',
            'Changa One' => 'Google Fonts',
            'Chango' => 'Google Fonts',
            'Charmonman' => 'Google Fonts',
            'Chathura' => 'Google Fonts',
            'Chau Philomene One' => 'Google Fonts',
            'Chela One' => 'Google Fonts',
            'Chelsea Market' => 'Google Fonts',
            'Chenla' => 'Google Fonts',
            'Cherry Cream Soda' => 'Google Fonts',
            'Cherry Swash' => 'Google Fonts',
            'Chewy' => 'Google Fonts',
            'Chicle' => 'Google Fonts',
            'Chivo' => 'Google Fonts',
            'Chonburi' => 'Google Fonts',
            'Cinzel' => 'Google Fonts',
            'Cinzel Decorative' => 'Google Fonts',
            'Clicker Script' => 'Google Fonts',
            'Coda' => 'Google Fonts',
            'Coda Caption' => 'Google Fonts',
            'Codystar' => 'Google Fonts',
            'Coiny' => 'Google Fonts',
            'Combo' => 'Google Fonts',
            'Comfortaa' => 'Google Fonts',
            'Coming Soon' => 'Google Fonts',
            'Concert One' => 'Google Fonts',
            'Condiment' => 'Google Fonts',
            'Content' => 'Google Fonts',
            'Contrail One' => 'Google Fonts',
            'Convergence' => 'Google Fonts',
            'Cookie' => 'Google Fonts',
            'Copse' => 'Google Fonts',
            'Corben' => 'Google Fonts',
            'Cormorant' => 'Google Fonts',
            'Cormorant Garamond' => 'Google Fonts',
            'Cormorant Infant' => 'Google Fonts',
            'Cormorant SC' => 'Google Fonts',
            'Cormorant Unicase' => 'Google Fonts',
            'Cormorant Upright' => 'Google Fonts',
            'Courgette' => 'Google Fonts',
            'Cousine' => 'Google Fonts',
            'Coustard' => 'Google Fonts',
            'Covered By Your Grace' => 'Google Fonts',
            'Crafty Girls' => 'Google Fonts',
            'Creepster' => 'Google Fonts',
            'Crete Round' => 'Google Fonts',
            'Crimson Text' => 'Google Fonts',
            'Croissant One' => 'Google Fonts',
            'Crushed' => 'Google Fonts',
            'Cuprum' => 'Google Fonts',
            'Cute Font' => 'Google Fonts',
            'Cutive' => 'Google Fonts',
            'Cutive Mono' => 'Google Fonts',
            'Damion' => 'Google Fonts',
            'Dancing Script' => 'Google Fonts',
            'Dangrek' => 'Google Fonts',
            'David Libre' => 'Google Fonts',
            'Dawning of a New Day' => 'Google Fonts',
            'Days One' => 'Google Fonts',
            'Dekko' => 'Google Fonts',
            'Delius' => 'Google Fonts',
            'Delius Swash Caps' => 'Google Fonts',
            'Delius Unicase' => 'Google Fonts',
            'Della Respira' => 'Google Fonts',
            'Denk One' => 'Google Fonts',
            'Devonshire' => 'Google Fonts',
            'Dhurjati' => 'Google Fonts',
            'Didact Gothic' => 'Google Fonts',
            'Diplomata' => 'Google Fonts',
            'Diplomata SC' => 'Google Fonts',
            'Do Hyeon' => 'Google Fonts',
            'Dokdo' => 'Google Fonts',
            'Domine' => 'Google Fonts',
            'Donegal One' => 'Google Fonts',
            'Doppio One' => 'Google Fonts',
            'Dorsa' => 'Google Fonts',
            'Dosis' => 'Google Fonts',
            'Dr Sugiyama' => 'Google Fonts',
            'Droid Arabic Kufi' => 'Google Fonts', // Hack for Google Early Access.
            'Droid Arabic Naskh' => 'Google Fonts', // Hack for Google Early Access.
            'Duru Sans' => 'Google Fonts',
            'Dynalight' => 'Google Fonts',
            'EB Garamond' => 'Google Fonts',
            'Eagle Lake' => 'Google Fonts',
            'East Sea Dokdo' => 'Google Fonts',
            'Eater' => 'Google Fonts',
            'Economica' => 'Google Fonts',
            'Eczar' => 'Google Fonts',
            'El Messiri' => 'Google Fonts',
            'Electrolize' => 'Google Fonts',
            'Elsie' => 'Google Fonts',
            'Elsie Swash Caps' => 'Google Fonts',
            'Emblema One' => 'Google Fonts',
            'Emilys Candy' => 'Google Fonts',
            'Encode Sans' => 'Google Fonts',
            'Encode Sans Condensed' => 'Google Fonts',
            'Encode Sans Expanded' => 'Google Fonts',
            'Encode Sans Semi Condensed' => 'Google Fonts',
            'Encode Sans Semi Expanded' => 'Google Fonts',
            'Engagement' => 'Google Fonts',
            'Englebert' => 'Google Fonts',
            'Enriqueta' => 'Google Fonts',
            'Erica One' => 'Google Fonts',
            'Esteban' => 'Google Fonts',
            'Euphoria Script' => 'Google Fonts',
            'Ewert' => 'Google Fonts',
            'Exo' => 'Google Fonts',
            'Exo 2' => 'Google Fonts',
            'Expletus Sans' => 'Google Fonts',
            'Fahkwang' => 'Google Fonts',
            'Fanwood Text' => 'Google Fonts',
            'Farsan' => 'Google Fonts',
            'Fascinate' => 'Google Fonts',
            'Fascinate Inline' => 'Google Fonts',
            'Faster One' => 'Google Fonts',
            'Fasthand' => 'Google Fonts',
            'Fauna One' => 'Google Fonts',
            'Faustina' => 'Google Fonts',
            'Federant' => 'Google Fonts',
            'Federo' => 'Google Fonts',
            'Felipa' => 'Google Fonts',
            'Fenix' => 'Google Fonts',
            'Finger Paint' => 'Google Fonts',
            'Fira Mono' => 'Google Fonts',
            'Fira Sans' => 'Google Fonts',
            'Fira Sans Condensed' => 'Google Fonts',
            'Fira Sans Extra Condensed' => 'Google Fonts',
            'Fjalla One' => 'Google Fonts',
            'Fjord One' => 'Google Fonts',
            'Flamenco' => 'Google Fonts',
            'Flavors' => 'Google Fonts',
            'Fondamento' => 'Google Fonts',
            'Fontdiner Swanky' => 'Google Fonts',
            'Forum' => 'Google Fonts',
            'Francois One' => 'Google Fonts',
            'Frank Ruhl Libre' => 'Google Fonts',
            'Freckle Face' => 'Google Fonts',
            'Fredericka the Great' => 'Google Fonts',
            'Fredoka One' => 'Google Fonts',
            'Freehand' => 'Google Fonts',
            'Fresca' => 'Google Fonts',
            'Frijole' => 'Google Fonts',
            'Fruktur' => 'Google Fonts',
            'Fugaz One' => 'Google Fonts',
            'GFS Didot' => 'Google Fonts',
            'GFS Neohellenic' => 'Google Fonts',
            'Gabriela' => 'Google Fonts',
            'Gaegu' => 'Google Fonts',
            'Gafata' => 'Google Fonts',
            'Galada' => 'Google Fonts',
            'Galdeano' => 'Google Fonts',
            'Galindo' => 'Google Fonts',
            'Gamja Flower' => 'Google Fonts',
            'Gentium Basic' => 'Google Fonts',
            'Gentium Book Basic' => 'Google Fonts',
            'Geo' => 'Google Fonts',
            'Geostar' => 'Google Fonts',
            'Geostar Fill' => 'Google Fonts',
            'Germania One' => 'Google Fonts',
            'Gidugu' => 'Google Fonts',
            'Gilda Display' => 'Google Fonts',
            'Give You Glory' => 'Google Fonts',
            'Glass Antiqua' => 'Google Fonts',
            'Glegoo' => 'Google Fonts',
            'Gloria Hallelujah' => 'Google Fonts',
            'Goblin One' => 'Google Fonts',
            'Gochi Hand' => 'Google Fonts',
            'Gorditas' => 'Google Fonts',
            'Gothic A1' => 'Google Fonts',
            'Goudy Bookletter 1911' => 'Google Fonts',
            'Graduate' => 'Google Fonts',
            'Grand Hotel' => 'Google Fonts',
            'Gravitas One' => 'Google Fonts',
            'Great Vibes' => 'Google Fonts',
            'Griffy' => 'Google Fonts',
            'Gruppo' => 'Google Fonts',
            'Gudea' => 'Google Fonts',
            'Gugi' => 'Google Fonts',
            'Gurajada' => 'Google Fonts',
            'Habibi' => 'Google Fonts',
            'Halant' => 'Google Fonts',
            'Hammersmith One' => 'Google Fonts',
            'Hanalei' => 'Google Fonts',
            'Hanalei Fill' => 'Google Fonts',
            'Handlee' => 'Google Fonts',
            'Hanuman' => 'Google Fonts',
            'Happy Monkey' => 'Google Fonts',
            'Harmattan' => 'Google Fonts',
            'Headland One' => 'Google Fonts',
            'Heebo' => 'Google Fonts',
            'Henny Penny' => 'Google Fonts',
            'Herr Von Muellerhoff' => 'Google Fonts',
            'Hi Melody' => 'Google Fonts',
            'Hind' => 'Google Fonts',
            'Hind Guntur' => 'Google Fonts',
            'Hind Madurai' => 'Google Fonts',
            'Hind Siliguri' => 'Google Fonts',
            'Hind Vadodara' => 'Google Fonts',
            'Holtwood One SC' => 'Google Fonts',
            'Homemade Apple' => 'Google Fonts',
            'Homenaje' => 'Google Fonts',
            'IBM Plex Mono' => 'Google Fonts',
            'IBM Plex Sans' => 'Google Fonts',
            'IBM Plex Sans Condensed' => 'Google Fonts',
            'IBM Plex Serif' => 'Google Fonts',
            'IM Fell DW Pica' => 'Google Fonts',
            'IM Fell DW Pica SC' => 'Google Fonts',
            'IM Fell Double Pica' => 'Google Fonts',
            'IM Fell Double Pica SC' => 'Google Fonts',
            'IM Fell English' => 'Google Fonts',
            'IM Fell English SC' => 'Google Fonts',
            'IM Fell French Canon' => 'Google Fonts',
            'IM Fell French Canon SC' => 'Google Fonts',
            'IM Fell Great Primer' => 'Google Fonts',
            'IM Fell Great Primer SC' => 'Google Fonts',
            'Iceberg' => 'Google Fonts',
            'Iceland' => 'Google Fonts',
            'Imprima' => 'Google Fonts',
            'Inconsolata' => 'Google Fonts',
            'Inder' => 'Google Fonts',
            'Indie Flower' => 'Google Fonts',
            'Inika' => 'Google Fonts',
            'Inknut Antiqua' => 'Google Fonts',
            'Irish Grover' => 'Google Fonts',
            'Istok Web' => 'Google Fonts',
            'Italiana' => 'Google Fonts',
            'Italianno' => 'Google Fonts',
            'Itim' => 'Google Fonts',
            'Jacques Francois' => 'Google Fonts',
            'Jacques Francois Shadow' => 'Google Fonts',
            'Jaldi' => 'Google Fonts',
            'Jim Nightshade' => 'Google Fonts',
            'Jockey One' => 'Google Fonts',
            'Jolly Lodger' => 'Google Fonts',
            'Jomhuria' => 'Google Fonts',
            'Josefin Sans' => 'Google Fonts',
            'Josefin Slab' => 'Google Fonts',
            'Joti One' => 'Google Fonts',
            'Jua' => 'Google Fonts',
            'Judson' => 'Google Fonts',
            'Julee' => 'Google Fonts',
            'Julius Sans One' => 'Google Fonts',
            'Junge' => 'Google Fonts',
            'Jura' => 'Google Fonts',
            'Just Another Hand' => 'Google Fonts',
            'Just Me Again Down Here' => 'Google Fonts',
            'K2D' => 'Google Fonts',
            'Kadwa' => 'Google Fonts',
            'Kalam' => 'Google Fonts',
            'Kameron' => 'Google Fonts',
            'Kanit' => 'Google Fonts',
            'Kantumruy' => 'Google Fonts',
            'Karla' => 'Google Fonts',
            'Karma' => 'Google Fonts',
            'Katibeh' => 'Google Fonts',
            'Kaushan Script' => 'Google Fonts',
            'Kavivanar' => 'Google Fonts',
            'Kavoon' => 'Google Fonts',
            'Kdam Thmor' => 'Google Fonts',
            'Keania One' => 'Google Fonts',
            'Kelly Slab' => 'Google Fonts',
            'Kenia' => 'Google Fonts',
            'Khand' => 'Google Fonts',
            'Khmer' => 'Google Fonts',
            'Khula' => 'Google Fonts',
            'Kirang Haerang' => 'Google Fonts',
            'Kite One' => 'Google Fonts',
            'Knewave' => 'Google Fonts',
            'KoHo' => 'Google Fonts',
            'Kodchasan' => 'Google Fonts',
            'Kosugi' => 'Google Fonts',
            'Kosugi Maru' => 'Google Fonts',
            'Kotta One' => 'Google Fonts',
            'Koulen' => 'Google Fonts',
            'Kranky' => 'Google Fonts',
            'Kreon' => 'Google Fonts',
            'Kristi' => 'Google Fonts',
            'Krona One' => 'Google Fonts',
            'Krub' => 'Google Fonts',
            'Kumar One' => 'Google Fonts',
            'Kumar One Outline' => 'Google Fonts',
            'Kurale' => 'Google Fonts',
            'La Belle Aurore' => 'Google Fonts',
            'Laila' => 'Google Fonts',
            'Lakki Reddy' => 'Google Fonts',
            'Lalezar' => 'Google Fonts',
            'Lancelot' => 'Google Fonts',
            'Lateef' => 'Google Fonts',
            'Lato' => 'Google Fonts',
            'League Script' => 'Google Fonts',
            'Leckerli One' => 'Google Fonts',
            'Ledger' => 'Google Fonts',
            'Lekton' => 'Google Fonts',
            'Lemon' => 'Google Fonts',
            'Lemonada' => 'Google Fonts',
            'Libre Barcode 128' => 'Google Fonts',
            'Libre Barcode 128 Text' => 'Google Fonts',
            'Libre Barcode 39' => 'Google Fonts',
            'Libre Barcode 39 Extended' => 'Google Fonts',
            'Libre Barcode 39 Extended Text' => 'Google Fonts',
            'Libre Barcode 39 Text' => 'Google Fonts',
            'Libre Baskerville' => 'Google Fonts',
            'Libre Franklin' => 'Google Fonts',
            'Life Savers' => 'Google Fonts',
            'Lilita One' => 'Google Fonts',
            'Lily Script One' => 'Google Fonts',
            'Limelight' => 'Google Fonts',
            'Linden Hill' => 'Google Fonts',
            'Lobster' => 'Google Fonts',
            'Lobster Two' => 'Google Fonts',
            'Londrina Outline' => 'Google Fonts',
            'Londrina Shadow' => 'Google Fonts',
            'Londrina Sketch' => 'Google Fonts',
            'Londrina Solid' => 'Google Fonts',
            'Lora' => 'Google Fonts',
            'Love Ya Like A Sister' => 'Google Fonts',
            'Loved by the King' => 'Google Fonts',
            'Lovers Quarrel' => 'Google Fonts',
            'Luckiest Guy' => 'Google Fonts',
            'Lusitana' => 'Google Fonts',
            'Lustria' => 'Google Fonts',
            'M PLUS 1p' => 'Google Fonts',
            'M PLUS Rounded 1c' => 'Google Fonts',
            'Macondo' => 'Google Fonts',
            'Macondo Swash Caps' => 'Google Fonts',
            'Mada' => 'Google Fonts',
            'Magra' => 'Google Fonts',
            'Maiden Orange' => 'Google Fonts',
            'Maitree' => 'Google Fonts',
            'Mako' => 'Google Fonts',
            'Mali' => 'Google Fonts',
            'Mallanna' => 'Google Fonts',
            'Mandali' => 'Google Fonts',
            'Manuale' => 'Google Fonts',
            'Marcellus' => 'Google Fonts',
            'Marcellus SC' => 'Google Fonts',
            'Marck Script' => 'Google Fonts',
            'Margarine' => 'Google Fonts',
            'Markazi Text' => 'Google Fonts',
            'Marko One' => 'Google Fonts',
            'Marmelad' => 'Google Fonts',
            'Martel' => 'Google Fonts',
            'Martel Sans' => 'Google Fonts',
            'Marvel' => 'Google Fonts',
            'Mate' => 'Google Fonts',
            'Mate SC' => 'Google Fonts',
            'Maven Pro' => 'Google Fonts',
            'McLaren' => 'Google Fonts',
            'Meddon' => 'Google Fonts',
            'MedievalSharp' => 'Google Fonts',
            'Medula One' => 'Google Fonts',
            'Meera Inimai' => 'Google Fonts',
            'Megrim' => 'Google Fonts',
            'Meie Script' => 'Google Fonts',
            'Merienda' => 'Google Fonts',
            'Merienda One' => 'Google Fonts',
            'Merriweather' => 'Google Fonts',
            'Merriweather Sans' => 'Google Fonts',
            'Metal' => 'Google Fonts',
            'Metal Mania' => 'Google Fonts',
            'Metamorphous' => 'Google Fonts',
            'Metrophobic' => 'Google Fonts',
            'Michroma' => 'Google Fonts',
            'Milonga' => 'Google Fonts',
            'Miltonian' => 'Google Fonts',
            'Miltonian Tattoo' => 'Google Fonts',
            'Mina' => 'Google Fonts',
            'Miniver' => 'Google Fonts',
            'Miriam Libre' => 'Google Fonts',
            'Mirza' => 'Google Fonts',
            'Miss Fajardose' => 'Google Fonts',
            'Mitr' => 'Google Fonts',
            'Modak' => 'Google Fonts',
            'Modern Antiqua' => 'Google Fonts',
            'Mogra' => 'Google Fonts',
            'Molengo' => 'Google Fonts',
            'Molle' => 'Google Fonts',
            'Monda' => 'Google Fonts',
            'Monofett' => 'Google Fonts',
            'Monoton' => 'Google Fonts',
            'Monsieur La Doulaise' => 'Google Fonts',
            'Montaga' => 'Google Fonts',
            'Montez' => 'Google Fonts',
            'Montserrat' => 'Google Fonts',
            'Montserrat Alternates' => 'Google Fonts',
            'Montserrat Subrayada' => 'Google Fonts',
            'Moul' => 'Google Fonts',
            'Moulpali' => 'Google Fonts',
            'Mountains of Christmas' => 'Google Fonts',
            'Mouse Memoirs' => 'Google Fonts',
            'Mr Bedfort' => 'Google Fonts',
            'Mr Dafoe' => 'Google Fonts',
            'Mr De Haviland' => 'Google Fonts',
            'Mrs Saint Delafield' => 'Google Fonts',
            'Mrs Sheppards' => 'Google Fonts',
            'Mukta' => 'Google Fonts',
            'Mukta Mahee' => 'Google Fonts',
            'Mukta Malar' => 'Google Fonts',
            'Mukta Vaani' => 'Google Fonts',
            'Muli' => 'Google Fonts',
            'Mystery Quest' => 'Google Fonts',
            'NTR' => 'Google Fonts',
            'Nanum Brush Script' => 'Google Fonts',
            'Nanum Gothic' => 'Google Fonts',
            'Nanum Gothic Coding' => 'Google Fonts',
            'Nanum Myeongjo' => 'Google Fonts',
            'Nanum Pen Script' => 'Google Fonts',
            'Neucha' => 'Google Fonts',
            'Neuton' => 'Google Fonts',
            'New Rocker' => 'Google Fonts',
            'News Cycle' => 'Google Fonts',
            'Niconne' => 'Google Fonts',
            'Niramit' => 'Google Fonts',
            'Nixie One' => 'Google Fonts',
            'Nobile' => 'Google Fonts',
            'Nokora' => 'Google Fonts',
            'Norican' => 'Google Fonts',
            'Nosifer' => 'Google Fonts',
            'Notable' => 'Google Fonts',
            'Nothing You Could Do' => 'Google Fonts',
            'Noticia Text' => 'Google Fonts',
            'Noto Kufi Arabic' => 'Google Fonts', // Hack for Google Early Access.
            'Noto Naskh Arabic' => 'Google Fonts', // Hack for Google Early Access.
            'Noto Sans' => 'Google Fonts',
            'Noto Sans Hebrew' => 'Google Fonts', // Hack for Google Early Access.
            'Noto Sans JP' => 'Google Fonts',
            'Noto Sans KR' => 'Google Fonts',
            'Noto Serif' => 'Google Fonts',
            'Noto Serif JP' => 'Google Fonts',
            'Noto Serif KR' => 'Google Fonts',
            'Nova Cut' => 'Google Fonts',
            'Nova Flat' => 'Google Fonts',
            'Nova Mono' => 'Google Fonts',
            'Nova Oval' => 'Google Fonts',
            'Nova Round' => 'Google Fonts',
            'Nova Script' => 'Google Fonts',
            'Nova Slim' => 'Google Fonts',
            'Nova Square' => 'Google Fonts',
            'Numans' => 'Google Fonts',
            'Nunito' => 'Google Fonts',
            'Nunito Sans' => 'Google Fonts',
            'Odor Mean Chey' => 'Google Fonts',
            'Offside' => 'Google Fonts',
            'Old Standard TT' => 'Google Fonts',
            'Oldenburg' => 'Google Fonts',
            'Oleo Script' => 'Google Fonts',
            'Oleo Script Swash Caps' => 'Google Fonts',
            'Open Sans' => 'Google Fonts',
            'Open Sans Condensed' => 'Google Fonts',
            'Open Sans Hebrew' => 'Google Fonts', // Hack for Google Early Access.
            'Open Sans Hebrew Condensed' => 'Google Fonts', // Hack for Google Early Access.
            'Oranienbaum' => 'Google Fonts',
            'Orbitron' => 'Google Fonts',
            'Oregano' => 'Google Fonts',
            'Orienta' => 'Google Fonts',
            'Original Surfer' => 'Google Fonts',
            'Oswald' => 'Google Fonts',
            'Over the Rainbow' => 'Google Fonts',
            'Overlock' => 'Google Fonts',
            'Overlock SC' => 'Google Fonts',
            'Overpass' => 'Google Fonts',
            'Overpass Mono' => 'Google Fonts',
            'Ovo' => 'Google Fonts',
            'Oxygen' => 'Google Fonts',
            'Oxygen Mono' => 'Google Fonts',
            'PT Mono' => 'Google Fonts',
            'PT Sans' => 'Google Fonts',
            'PT Sans Caption' => 'Google Fonts',
            'PT Sans Narrow' => 'Google Fonts',
            'PT Serif' => 'Google Fonts',
            'PT Serif Caption' => 'Google Fonts',
            'Pacifico' => 'Google Fonts',
            'Padauk' => 'Google Fonts',
            'Palanquin' => 'Google Fonts',
            'Palanquin Dark' => 'Google Fonts',
            'Pangolin' => 'Google Fonts',
            'Paprika' => 'Google Fonts',
            'Parisienne' => 'Google Fonts',
            'Passero One' => 'Google Fonts',
            'Passion One' => 'Google Fonts',
            'Pathway Gothic One' => 'Google Fonts',
            'Patrick Hand' => 'Google Fonts',
            'Patrick Hand SC' => 'Google Fonts',
            'Pattaya' => 'Google Fonts',
            'Patua One' => 'Google Fonts',
            'Pavanam' => 'Google Fonts',
            'Paytone One' => 'Google Fonts',
            'Peddana' => 'Google Fonts',
            'Peralta' => 'Google Fonts',
            'Permanent Marker' => 'Google Fonts',
            'Petit Formal Script' => 'Google Fonts',
            'Petrona' => 'Google Fonts',
            'Philosopher' => 'Google Fonts',
            'Piedra' => 'Google Fonts',
            'Pinyon Script' => 'Google Fonts',
            'Pirata One' => 'Google Fonts',
            'Plaster' => 'Google Fonts',
            'Play' => 'Google Fonts',
            'Playball' => 'Google Fonts',
            'Playfair Display' => 'Google Fonts',
            'Playfair Display SC' => 'Google Fonts',
            'Podkova' => 'Google Fonts',
            'Poiret One' => 'Google Fonts',
            'Poller One' => 'Google Fonts',
            'Poly' => 'Google Fonts',
            'Pompiere' => 'Google Fonts',
            'Pontano Sans' => 'Google Fonts',
            'Poor Story' => 'Google Fonts',
            'Poppins' => 'Google Fonts',
            'Port Lligat Sans' => 'Google Fonts',
            'Port Lligat Slab' => 'Google Fonts',
            'Pragati Narrow' => 'Google Fonts',
            'Prata' => 'Google Fonts',
            'Preahvihear' => 'Google Fonts',
            'Press Start 2P' => 'Google Fonts',
            'Pridi' => 'Google Fonts',
            'Princess Sofia' => 'Google Fonts',
            'Prociono' => 'Google Fonts',
            'Prompt' => 'Google Fonts',
            'Prosto One' => 'Google Fonts',
            'Proza Libre' => 'Google Fonts',
            'Puritan' => 'Google Fonts',
            'Purple Purse' => 'Google Fonts',
            'Quando' => 'Google Fonts',
            'Quantico' => 'Google Fonts',
            'Quattrocento' => 'Google Fonts',
            'Quattrocento Sans' => 'Google Fonts',
            'Questrial' => 'Google Fonts',
            'Quicksand' => 'Google Fonts',
            'Quintessential' => 'Google Fonts',
            'Qwigley' => 'Google Fonts',
            'Racing Sans One' => 'Google Fonts',
            'Radley' => 'Google Fonts',
            'Rajdhani' => 'Google Fonts',
            'Rakkas' => 'Google Fonts',
            'Raleway' => 'Google Fonts',
            'Raleway Dots' => 'Google Fonts',
            'Ramabhadra' => 'Google Fonts',
            'Ramaraja' => 'Google Fonts',
            'Rambla' => 'Google Fonts',
            'Rammetto One' => 'Google Fonts',
            'Ranchers' => 'Google Fonts',
            'Rancho' => 'Google Fonts',
            'Ranga' => 'Google Fonts',
            'Rasa' => 'Google Fonts',
            'Rationale' => 'Google Fonts',
            'Ravi Prakash' => 'Google Fonts',
            'Redressed' => 'Google Fonts',
            'Reem Kufi' => 'Google Fonts',
            'Reenie Beanie' => 'Google Fonts',
            'Revalia' => 'Google Fonts',
            'Rhodium Libre' => 'Google Fonts',
            'Ribeye' => 'Google Fonts',
            'Ribeye Marrow' => 'Google Fonts',
            'Righteous' => 'Google Fonts',
            'Risque' => 'Google Fonts',
            'Roboto' => 'Google Fonts',
            'Roboto Condensed' => 'Google Fonts',
            'Roboto Mono' => 'Google Fonts',
            'Roboto Slab' => 'Google Fonts',
            'Rochester' => 'Google Fonts',
            'Rock Salt' => 'Google Fonts',
            'Rokkitt' => 'Google Fonts',
            'Romanesco' => 'Google Fonts',
            'Ropa Sans' => 'Google Fonts',
            'Rosario' => 'Google Fonts',
            'Rosarivo' => 'Google Fonts',
            'Rouge Script' => 'Google Fonts',
            'Rozha One' => 'Google Fonts',
            'Rubik' => 'Google Fonts',
            'Rubik Mono One' => 'Google Fonts',
            'Ruda' => 'Google Fonts',
            'Rufina' => 'Google Fonts',
            'Ruge Boogie' => 'Google Fonts',
            'Ruluko' => 'Google Fonts',
            'Rum Raisin' => 'Google Fonts',
            'Ruslan Display' => 'Google Fonts',
            'Russo One' => 'Google Fonts',
            'Ruthie' => 'Google Fonts',
            'Rye' => 'Google Fonts',
            'Sacramento' => 'Google Fonts',
            'Sahitya' => 'Google Fonts',
            'Sail' => 'Google Fonts',
            'Saira' => 'Google Fonts',
            'Saira Condensed' => 'Google Fonts',
            'Saira Extra Condensed' => 'Google Fonts',
            'Saira Semi Condensed' => 'Google Fonts',
            'Salsa' => 'Google Fonts',
            'Sanchez' => 'Google Fonts',
            'Sancreek' => 'Google Fonts',
            'Sansita' => 'Google Fonts',
            'Sarala' => 'Google Fonts',
            'Sarina' => 'Google Fonts',
            'Sarpanch' => 'Google Fonts',
            'Satisfy' => 'Google Fonts',
            'Sawarabi Gothic' => 'Google Fonts',
            'Sawarabi Mincho' => 'Google Fonts',
            'Scada' => 'Google Fonts',
            'Scheherazade' => 'Google Fonts',
            'Schoolbell' => 'Google Fonts',
            'Scope One' => 'Google Fonts',
            'Seaweed Script' => 'Google Fonts',
            'Secular One' => 'Google Fonts',
            'Sedgwick Ave' => 'Google Fonts',
            'Sedgwick Ave Display' => 'Google Fonts',
            'Sevillana' => 'Google Fonts',
            'Seymour One' => 'Google Fonts',
            'Shadows Into Light' => 'Google Fonts',
            'Shadows Into Light Two' => 'Google Fonts',
            'Shanti' => 'Google Fonts',
            'Share' => 'Google Fonts',
            'Share Tech' => 'Google Fonts',
            'Share Tech Mono' => 'Google Fonts',
            'Shojumaru' => 'Google Fonts',
            'Short Stack' => 'Google Fonts',
            'Shrikhand' => 'Google Fonts',
            'Siemreap' => 'Google Fonts',
            'Sigmar One' => 'Google Fonts',
            'Signika' => 'Google Fonts',
            'Signika Negative' => 'Google Fonts',
            'Simonetta' => 'Google Fonts',
            'Sintony' => 'Google Fonts',
            'Sirin Stencil' => 'Google Fonts',
            'Six Caps' => 'Google Fonts',
            'Skranji' => 'Google Fonts',
            'Slabo 13px' => 'Google Fonts',
            'Slabo 27px' => 'Google Fonts',
            'Slackey' => 'Google Fonts',
            'Smokum' => 'Google Fonts',
            'Smythe' => 'Google Fonts',
            'Sniglet' => 'Google Fonts',
            'Snippet' => 'Google Fonts',
            'Snowburst One' => 'Google Fonts',
            'Sofadi One' => 'Google Fonts',
            'Sofia' => 'Google Fonts',
            'Song Myung' => 'Google Fonts',
            'Sonsie One' => 'Google Fonts',
            'Sorts Mill Goudy' => 'Google Fonts',
            'Source Code Pro' => 'Google Fonts',
            'Source Sans Pro' => 'Google Fonts',
            'Source Serif Pro' => 'Google Fonts',
            'Space Mono' => 'Google Fonts',
            'Special Elite' => 'Google Fonts',
            'Spectral' => 'Google Fonts',
            'Spectral SC' => 'Google Fonts',
            'Spicy Rice' => 'Google Fonts',
            'Spinnaker' => 'Google Fonts',
            'Spirax' => 'Google Fonts',
            'Squada One' => 'Google Fonts',
            'Sree Krushnadevaraya' => 'Google Fonts',
            'Sriracha' => 'Google Fonts',
            'Srisakdi' => 'Google Fonts',
            'Stalemate' => 'Google Fonts',
            'Stalinist One' => 'Google Fonts',
            'Stardos Stencil' => 'Google Fonts',
            'Stint Ultra Condensed' => 'Google Fonts',
            'Stint Ultra Expanded' => 'Google Fonts',
            'Stoke' => 'Google Fonts',
            'Strait' => 'Google Fonts',
            'Stylish' => 'Google Fonts',
            'Sue Ellen Francisco' => 'Google Fonts',
            'Suez One' => 'Google Fonts',
            'Sumana' => 'Google Fonts',
            'Sunflower' => 'Google Fonts',
            'Sunshiney' => 'Google Fonts',
            'Supermercado One' => 'Google Fonts',
            'Sura' => 'Google Fonts',
            'Suranna' => 'Google Fonts',
            'Suravaram' => 'Google Fonts',
            'Suwannaphum' => 'Google Fonts',
            'Swanky and Moo Moo' => 'Google Fonts',
            'Syncopate' => 'Google Fonts',
            'Tajawal' => 'Google Fonts',
            'Tangerine' => 'Google Fonts',
            'Taprom' => 'Google Fonts',
            'Tauri' => 'Google Fonts',
            'Taviraj' => 'Google Fonts',
            'Teko' => 'Google Fonts',
            'Telex' => 'Google Fonts',
            'Tenali Ramakrishna' => 'Google Fonts',
            'Tenor Sans' => 'Google Fonts',
            'Text Me One' => 'Google Fonts',
            'The Girl Next Door' => 'Google Fonts',
            'Tienne' => 'Google Fonts',
            'Tillana' => 'Google Fonts',
            'Timmana' => 'Google Fonts',
            'Tinos' => 'Google Fonts',
            'Titan One' => 'Google Fonts',
            'Titillium Web' => 'Google Fonts',
            'Trade Winds' => 'Google Fonts',
            'Trirong' => 'Google Fonts',
            'Trocchi' => 'Google Fonts',
            'Trochut' => 'Google Fonts',
            'Trykker' => 'Google Fonts',
            'Tulpen One' => 'Google Fonts',
            'Ubuntu' => 'Google Fonts',
            'Ubuntu Condensed' => 'Google Fonts',
            'Ubuntu Mono' => 'Google Fonts',
            'Ultra' => 'Google Fonts',
            'Uncial Antiqua' => 'Google Fonts',
            'Underdog' => 'Google Fonts',
            'Unica One' => 'Google Fonts',
            'UnifrakturCook' => 'Google Fonts',
            'UnifrakturMaguntia' => 'Google Fonts',
            'Unkempt' => 'Google Fonts',
            'Unlock' => 'Google Fonts',
            'Unna' => 'Google Fonts',
            'VT323' => 'Google Fonts',
            'Vampiro One' => 'Google Fonts',
            'Varela' => 'Google Fonts',
            'Varela Round' => 'Google Fonts',
            'Vast Shadow' => 'Google Fonts',
            'Vesper Libre' => 'Google Fonts',
            'Vibur' => 'Google Fonts',
            'Vidaloka' => 'Google Fonts',
            'Viga' => 'Google Fonts',
            'Voces' => 'Google Fonts',
            'Volkhov' => 'Google Fonts',
            'Vollkorn' => 'Google Fonts',
            'Vollkorn SC' => 'Google Fonts',
            'Voltaire' => 'Google Fonts',
            'Waiting for the Sunrise' => 'Google Fonts',
            'Wallpoet' => 'Google Fonts',
            'Walter Turncoat' => 'Google Fonts',
            'Warnes' => 'Google Fonts',
            'Wellfleet' => 'Google Fonts',
            'Wendy One' => 'Google Fonts',
            'Wire One' => 'Google Fonts',
            'Work Sans' => 'Google Fonts',
            'Yanone Kaffeesatz' => 'Google Fonts',
            'Yantramanav' => 'Google Fonts',
            'Yatra One' => 'Google Fonts',
            'Yellowtail' => 'Google Fonts',
            'Yeon Sung' => 'Google Fonts',
            'Yeseva One' => 'Google Fonts',
            'Yesteryear' => 'Google Fonts',
            'Yrsa' => 'Google Fonts',
            'Zeyada' => 'Google Fonts',
            'Zilla Slab' => 'Google Fonts',
            'Zilla Slab Highlight' => 'Google Fonts',
        );
    }

    public function set_default_values_if_not_exists()
    {
        $options = get_option(WCP_FOLDER_VAR);
        $options = empty($options) || !is_array($options) ? array() : $options;
        foreach ($options as $option) {
            $post_type = self::get_custom_post_type($option);
            $terms = get_terms($post_type, array(
                    'hide_empty' => false,
                )
            );
            if (!empty($terms)) {
                foreach ($terms as $term) {
                    $order = get_term_meta($term->term_id, "wcp_custom_order", true);
                    if (empty($order) || $order == null) {
                        update_term_meta($term->term_id, "wcp_custom_order", "1");
                    }
                }
            }
        }
    }

    /* Free and Pro major changes */
    public function check_has_valid_key()
    {
        /* Free/Pro: checking for key, for Free return 0, for Pro check for Key */
        return 0;
    }

    public function get_license_key_information($licenseKey)
    {
        return array();
    }

    public function get_license_key_data($licenseKey = '')
    {
        return array();
    }

    public function check_for_license_key()
    {
        return false;
    }
}