<?php

defined('ABSPATH') or die('Nope, not accessing this');

class WCP_Folders
{

    private static $instance;

    public $license_key_data = null;

    private static $folders;

    public $total_folders = 0;

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
        add_action('parse_tax_query', array($this, 'taxonomy_archive_exclude_children'));

        /* Save Data */
        add_action('wp_ajax_wcp_add_new_folder', array($this, 'wcp_add_new_folder'));

        /* Update Data */
        add_action('wp_ajax_wcp_update_folder', array($this, 'wcp_update_folder'));

        /* Remove Data */
        add_action('wp_ajax_wcp_remove_folder', array($this, 'wcp_remove_folder'));

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

        /* Update Parent Data */
        add_action('wp_ajax_wcp_remove_post_folder', array($this, 'wcp_remove_post_folder'));

        /* Update width Data */
        add_action('wp_ajax_wcp_change_post_width', array($this, 'wcp_change_post_width'));

        /* Update width Data */
        add_action('wp_ajax_wcp_change_folder_display_status', array($this, 'wcp_change_folder_display_status'));


        /* Update width Data */
        add_action('wp_ajax_wcp_change_all_status', array($this, 'wcp_change_all_status'));

        self::$folders = 10;

        /* Send message on plugin deactivate */
        add_action( 'wp_ajax_folder_plugin_deactivate', array( $this, 'folder_plugin_deactivate' ) );

        /* Send message on owner */
        add_action( 'wp_ajax_wcp_folder_send_message_to_owner', array( $this, 'wcp_folder_send_message_to_owner' ) );

        /* Get default list */
        add_action( 'wp_ajax_wcp_get_default_list', array( $this, 'wcp_get_default_list' ) );

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

        /**/
        add_filter('pre-upload-ui', array($this, 'show_dropdown_on_media_screen'));

        add_action('add_attachment', array($this, 'add_attachment_category'));

        $options = get_option("folders_settings");

        $options = is_array($options)?$options:array();

        if (in_array("post", $options)) {
            add_filter('manage_posts_columns', array($this, 'wcp_manage_columns_head'));
            add_action('manage_posts_custom_column', array($this, 'wcp_manage_columns_content'), 10, 2);
        }

        if (in_array("page", $options)) {
            add_filter('manage_page_posts_columns', array($this, 'wcp_manage_columns_head'));
            add_action('manage_page_posts_custom_column', array($this, 'wcp_manage_columns_content'), 10, 2);
        }

        if (in_array("attachment", $options)) {
            add_filter('manage_media_columns', array($this, 'wcp_manage_columns_head'));
            add_action('manage_media_custom_column', array($this, 'wcp_manage_columns_content'), 10, 2);
        }

        foreach ($options as $option) {
            if ($option != "post" && $option != "page" && $option != "attachment") {
                add_filter('manage_edit-'.$option.'_columns', array($this, 'wcp_manage_columns_head'), 99999);
                add_action('manage_'.$option.'_posts_custom_column', array($this, 'wcp_manage_columns_content'), 2, 2);
            }
        }
    }

    public function add_attachment_category($post_ID)
    {
        if(self::is_for_this_post_type('attachment') || self::is_for_this_post_type('media')) {
            $folder_id = isset($_REQUEST["folder_for_media"]) ? $_REQUEST["folder_for_media"] : null;
            if (!is_null($folder_id)) {
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
            $options = WCP_Tree::get_folder_option_data($post_type);;
            echo '<p class="attachments-category">'.esc_html__("Select a folder (Optional)", WCP_FOLDER).'<br/></p>
	        <p>
	            <select name="folder_for_media" class="folder_for_media"><option value="-1">- '.esc_html__('Uncategorized', WCP_FOLDER).'</option>'.$options.'</select>
	        </p>';
        }
    }

    public function wcp_hide_folders()
    {
        $response = array();
        $response['status'] = 0;
        $response['error'] = 0;
        $response['data'] = array();
        $response['message'] = "";
        $postData = $_POST;
        $errorCounter = 0;
        if (!isset($postData['status']) || empty($postData['status'])) {
            $response['message'] = __("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['type']) || empty($postData['type'])) {
            $response['message'] = __("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] = __("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else if ($postData['type'] == "page" && !current_user_can("edit_pages")) {
            $response['message'] = __("You have not permission to update width", WCP_FOLDER);
            $errorCounter++;
        } else if ($postData['type'] != "page" && !current_user_can("edit_posts")) {
            $response['message'] = __("You have not permission to update width", WCP_FOLDER);
            $errorCounter++;
        } else {
            $type = self::sanitize_options($postData['type']);
            $nonce = self::sanitize_options($postData['nonce']);
            if(!wp_verify_nonce($nonce, 'wcp_folder_nonce_'.$type)) {
                $response['message'] = __("Your request is not valid", WCP_FOLDER);
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
        die;
    }

    public function wcp_change_folder_display_status()
    {
        $response = array();
        $response['status'] = 0;
        $response['error'] = 0;
        $response['data'] = array();
        $response['message'] = "";
        $postData = $_POST;
        $errorCounter = 0;
        if (!isset($postData['status']) || empty($postData['status'])) {
            $response['message'] = __("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['type']) || empty($postData['type'])) {
            $response['message'] = __("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] = __("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else if ($postData['type'] == "page" && !current_user_can("edit_pages")) {
            $response['message'] = __("You have not permission to update width", WCP_FOLDER);
            $errorCounter++;
        } else if ($postData['type'] != "page" && !current_user_can("edit_posts")) {
            $response['message'] = __("You have not permission to update width", WCP_FOLDER);
            $errorCounter++;
        } else {
            $type = self::sanitize_options($postData['type']);
            $nonce = self::sanitize_options($postData['nonce']);
            if(!wp_verify_nonce($nonce, 'wcp_folder_nonce_'.$type)) {
                $response['message'] = __("Your request is not valid", WCP_FOLDER);
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
        die;
    }

    public function wcp_remove_post_folder() {
        $response = array();
        $response['status'] = 0;
        $response['error'] = 0;
        $response['data'] = array();
        $response['message'] = "";
        $postData = $_POST;
        $errorCounter = 0;
        if (!isset($postData['post_id']) || empty($postData['post_id'])) {
            $response['message'] = __("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['type']) || empty($postData['type'])) {
            $response['message'] = __("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] = __("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else if(!wp_verify_nonce($postData['nonce'], 'wcp_folder_nonce_'.$postData['type'])) {
            $response['message'] = __("Your request is not valid", WCP_FOLDER);
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
        die;
//        wp_delete_object_term_relationships
    }

    public function wcp_get_default_list() {
        $post_type = 'attachment';

        $total_posts = wp_count_posts($post_type)->inherit;;

        $empty_items = self::get_total_empty_posts('attachment');

        $post_type = self::get_custom_post_type($post_type);
        $terms_data = WCP_Tree::get_full_tree_data($post_type);
        $taxonomies = self::get_terms_hierarchical('media_folder');

        $response = array(
            'status' => 1,
            'data' => $terms_data,
            'total_items' => $total_posts,
            'taxonomies' => $taxonomies,
            'empty_items' => $empty_items
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

    public function output_backbone_view_filters() {
        wp_enqueue_script( 'folders-media', WCP_FOLDER_URL.'assets/js/media.js', array( 'media-editor', 'media-views' ), WCP_FOLDER_VERSION, true );
        wp_localize_script( 'folders-media', 'folders_media_options', array(
            'terms'     => self::get_terms_hierarchical('media_folder'),
            'taxonomy'  => get_taxonomy('media_folder')
        ));
        wp_enqueue_style( 'folders-media', WCP_FOLDER_URL . 'assets/css/media.css' , array(), WCP_FOLDER_VERSION);
    }

    public function get_terms_hierarchical( $taxonomy ) {
        $terms = get_terms( array(
            'taxonomy'      => $taxonomy,
            'hide_empty'    => false,
            'parent'        => 0,
        ) );

        if ( empty( $terms ) ) {
            return false;
        }

        $hierarchy = _get_term_hierarchy( $taxonomy );

        $hierarchical_terms = array();
        foreach ( $terms as $term ) {
            $hierarchical_terms[] = $term;
            $hierarchical_terms = self::add_child_terms_recursive( $taxonomy, $hierarchical_terms, $hierarchy, $term->term_id, 1 );
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

        $term = sanitize_text_field( $_REQUEST['media_folder'] );
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
            'show_option_all'   => __( 'All Folders', WCP_FOLDER ),
            'show_option_none'   => __( '(Unassigned)', WCP_FOLDER ),
            'option_none_value' => -1,
            'orderby'           => 'name',
            'order'             => 'ASC',
            'show_count'        => true,
            'hide_empty'        => false,
            'echo'              => true,
            'selected'          => $current_term,
            'hierarchical'      => true,
            'name'              => 'media_folder',
            'id'                => '',
            'class'             => '',
            'taxonomy'          => 'media_folder',
            'value_field'       => 'slug',
        ) );

    }


    function new_to_auto_draft($post) {

        $post_type = $post->post_type;

        if(self::is_for_this_post_type($post_type)) {

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
        $errorMessage = __("%s is required", WCP_FOLDER);
        $postData = $_POST;
        if(!isset($postData['textarea_text']) || trim($postData['textarea_text']) == "") {
            $error = array(
                "key"   => "textarea_text",
                "message" => __("Please enter your message",WCP_FOLDER)
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
                    "message" => __("Your request is not valid", WCP_FOLDER)
                );
                $errorArray[] = $error;
            } else {
                if(!wp_verify_nonce($postData['folder_help_nonce'], 'wcp_folder_help_nonce')) {
                    $error = array(
                        "key"   => "nonce",
                        "message" => __("Your request is not valid", WCP_FOLDER)
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
                    <td><?php echo $domain ?></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td><?php echo $email ?></td>
                </tr>
                <tr>
                    <th>Message</th>
                    <td><?php echo nl2br($text_message) ?></td>
                </tr>
            </table>
            <?php
            $message = ob_get_clean();
            $to = "contact+fromwp@premio.io";
            $status = wp_mail($to, $subject, $message, $headers);
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
        $postData = $_POST;
        $errorCounter = 0;
        $response = array();
        $response['status'] = 0;
        $response['message'] = "";
        $response['valid'] = 1;
        if(!isset($postData['reason']) || empty($postData['reason'])) {
            $errorCounter++;
            $response['message'] = "Please provide reason";
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] = __("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
            $response['valid'] = 0;
        } else {
            $nonce = self::sanitize_options($postData['nonce']);
            if(!wp_verify_nonce($nonce, 'wcp_folder_deactivate_nonce')) {
                $response['message'] = __("Your request is not valid", WCP_FOLDER);
                $errorCounter++;
                $response['valid'] = 0;
            }
        }
        if($errorCounter == 0) {
            $reason = $postData['reason'];
            $email = get_option( 'admin_email' );
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
                    <td><?php echo WCP_FOLDER_VERSION ?></td>
                </tr>
                <tr>
                    <th>Domain</th>
                    <td><?php echo $domain ?></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td><?php echo $email ?></td>
                </tr>
                <tr>
                    <th>Comment</th>
                    <td><?php echo nl2br($reason) ?></td>
                </tr>
                <tr>
                    <th>WordPress Version</th>
                    <td><?php echo get_bloginfo('version') ?></td>
                </tr>
                <tr>
                    <th>PHP Version</th>
                    <td><?php echo PHP_VERSION ?></td>
                </tr>
            </table>
            <?php
            $content = ob_get_clean();
            $to = "contact+removed@premio.io";
            wp_mail($to, $subject, $content, $headers);
            $response['status'] = 1;
        }
        echo json_encode($response);
        die;
    }

    public function check_has_valid_key()
    {
        return 0;
    }

    public static function total_term_folders()
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

    public function wcp_remove_post_item()
    {
        $response = array();
        $response['status'] = 0;
        $response['error'] = 0;
        $response['data'] = array();
        $response['message'] = "";
        $postData = $_POST;
        if (isset($postData['post_id']) && !empty($postData['post_id'])) {
            wp_delete_post($postData['post_id']);
            $response['status'] = 1;
        }
        echo json_encode($response);
        die;
    }

    public function wcp_change_all_status()
    {
        $response = array();
        $response['status'] = 0;
        $response['error'] = 0;
        $response['data'] = array();
        $response['message'] = "";
        $postData = $_POST;
        $errorCounter = 0;
        if (!isset($postData['type']) || empty($postData['type'])) {
            $response['message'] = __("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] = __("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else  if (!current_user_can("manage_categories") || ($postData['type'] == "page" && !current_user_can("edit_pages"))) {
            $response['message'] = __("You have not permission to update width", WCP_FOLDER);
            $errorCounter++;
        } else if (!current_user_can("manage_categories") || ($postData['type'] != "page" && !current_user_can("edit_posts"))) {
            $response['message'] = __("You have not permission to update width", WCP_FOLDER);
            $errorCounter++;
        } else {
            $type = self::sanitize_options($postData['type']);
            $nonce = self::sanitize_options($postData['nonce']);
            if(!wp_verify_nonce($nonce, 'wcp_folder_nonce_'.$type)) {
                $response['message'] = __("Your request is not valid", WCP_FOLDER);
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
        die;
    }

    public function wcp_change_post_width()
    {
        $response = array();
        $response['status'] = 0;
        $response['error'] = 0;
        $response['data'] = array();
        $response['message'] = "";
        $postData = $_POST;
        $errorCounter = 0;
        if (!isset($postData['width']) || empty($postData['width'])) {
            $response['message'] = __("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['type']) || empty($postData['type'])) {
            $response['message'] = __("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] = __("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else if ($postData['type'] == "page" && !current_user_can("edit_pages")) {
            $response['message'] = __("You have not permission to update width", WCP_FOLDER);
            $errorCounter++;
        } else if ($postData['type'] != "page" && !current_user_can("edit_posts")) {
            $response['message'] = __("You have not permission to update width", WCP_FOLDER);
            $errorCounter++;
        } else {
            $type = self::sanitize_options($postData['type']);
            $nonce = self::sanitize_options($postData['nonce']);
            if(!wp_verify_nonce($nonce, 'wcp_folder_nonce_'.$type)) {
                $response['message'] = __("Your request is not valid", WCP_FOLDER);
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
        die;
    }

    public function wcp_change_multiple_post_folder()
    {
        $response = array();
        $response['status'] = 0;
        $response['error'] = 0;
        $response['data'] = array();
        $response['message'] = "";
        $postData = $_POST;
        $errorCounter = 0;
        if (!isset($postData['post_ids']) || empty($postData['post_ids'])) {
            $response['message'] = __("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['folder_id']) || empty($postData['folder_id'])) {
            $response['message'] = __("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['type']) || empty($postData['type'])) {
            $response['message'] = __("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] = __("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else if ($postData['type'] == "page" && !current_user_can("edit_pages")) {
            $response['message'] = __("You have not permission to update folder", WCP_FOLDER);
            $errorCounter++;
        } else if ($postData['type'] != "page" && !current_user_can("edit_posts")) {
            $response['message'] = __("You have not permission to update folder", WCP_FOLDER);
            $errorCounter++;
        } else {
            $folder_id = self::sanitize_options($postData['folder_id']);
            if(!wp_verify_nonce($postData['nonce'], 'wcp_folder_term_'.$folder_id)) {
                $response['message'] = __("Your request is not valid", WCP_FOLDER);
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
                foreach ($postArray as $post) {
                    $post_type = self::get_custom_post_type($type);
                    if(!empty($taxonomy)) {
                        wp_remove_object_terms($post, $taxonomy, $post_type);
                    }
                    wp_set_post_terms($post, $folderID, $post_type, $status);
                }
            }
            $response['status'] = 1;
        }
        echo json_encode($response);
        die;
    }

    public function wcp_change_post_folder()
    {
        $response = array();
        $response['status'] = 0;
        $response['error'] = 0;
        $response['data'] = array();
        $response['message'] = "";
        $postData = $_POST;
        $errorCounter = 0;
        if (!isset($postData['post_id']) || empty($postData['post_id'])) {
            $errorCounter++;
            $response['message'] = __("Your request is not valid", WCP_FOLDER);
        } else if (!isset($postData['folder_id']) || empty($postData['folder_id'])) {
            $errorCounter++;
            $response['message'] = __("Your request is not valid", WCP_FOLDER);
        } else if (!isset($postData['type']) || empty($postData['type'])) {
            $errorCounter++;
            $response['message'] = __("Your request is not valid", WCP_FOLDER);
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] = __("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else if ($postData['type'] == "page" && !current_user_can("edit_pages")) {
            $response['message'] = __("You have not permission to update folder", WCP_FOLDER);
            $errorCounter++;
        } else if ($postData['type'] != "page" && !current_user_can("edit_posts")) {
            $response['message'] = __("You have not permission to update folder", WCP_FOLDER);
            $errorCounter++;
        } else {
            $term_id = self::sanitize_options($postData['folder_id']);
            if(!wp_verify_nonce($postData['nonce'], 'wcp_folder_term_'.$term_id)) {
                $response['message'] = __("Your request is not valid", WCP_FOLDER);
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
        die;
    }

    public function wcp_mark_un_mark_folder()
    {
        $response = array();
        $response['status'] = 0;
        $response['error'] = 0;
        $response['data'] = array();
        $response['message'] = "";
        $postData = $_POST;
        $errorCounter = 0;
        if (!current_user_can("manage_categories")) {
            $response['message'] = __("You have not permission to update folder", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['term_id']) || empty($postData['term_id'])) {
            $errorCounter++;
            $response['message'] = __("Your request is not valid", WCP_FOLDER);
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] = __("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else {
            $term_id = self::sanitize_options($postData['term_id']);
            if(!wp_verify_nonce($postData['nonce'], 'wcp_folder_highlight_term_'.$term_id)) {
                $response['message'] = __("Your request is not valid", WCP_FOLDER);
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
        die;
    }

    public function wcp_save_folder_order()
    {
        $response = array();
        $response['status'] = 0;
        $response['error'] = 0;
        $response['data'] = array();
        $response['message'] = "";
        $postData = $_POST;
        $errorCounter = 0;
        if (!current_user_can("manage_categories")) {
            $response['message'] = __("You have not permission to update folder order", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['term_ids']) || empty($postData['term_ids'])) {
            $errorCounter++;
            $response['message'] = __("Your request is not valid", WCP_FOLDER);
        } else if (!isset($postData['type']) || empty($postData['type'])) {
            $errorCounter++;
            $response['message'] = __("Your request is not valid", WCP_FOLDER);
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] = __("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else {
            $type = self::sanitize_options($postData['type']);
            if(!wp_verify_nonce($postData['nonce'], 'wcp_folder_nonce_'.$type)) {
                $response['message'] = __("Your request is not valid", WCP_FOLDER);
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
            $response['options'] = WCP_Tree::get_option_data_for_select($folder_type);

        }
        echo json_encode($response);
        die;
    }

    public function save_wcp_folder_state()
    {
        $response = array();
        $response['status'] = 0;
        $response['error'] = 0;
        $response['data'] = array();
        $response['message'] = "";
        $postData = $_POST;
        $errorCounter = 0;
        if (!current_user_can("manage_categories")) {
            $response['message'] = __("You have not permission to update folder", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['term_id']) || empty($postData['term_id'])) {
            $response['message'] = __("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] = "Unable to create folder, Your request is not valid";
            $errorCounter++;
        } else {
            $term_id = self::sanitize_options($postData['term_id']);
            if(!wp_verify_nonce($postData['nonce'], 'wcp_folder_term_'.$term_id)) {
                $response['message'] = __("Your request is not valid", WCP_FOLDER);
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
        die;
    }

    public function wcp_update_parent_information()
    {
        $response = array();
        $response['status'] = 0;
        $response['error'] = 0;
        $response['data'] = array();
        $response['message'] = "";
        $postData = $_POST;
        $errorCounter = 0;
        if (!current_user_can("manage_categories")) {
            $response['message'] = __("You have not permission to update folder", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['term_id']) || empty($postData['term_id'])) {
            $response['message'] = __("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['type']) || empty($postData['type'])) {
            $response['message'] = __("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['parent_id'])) {
            $response['message'] = __("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] = __("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else {
            $term_id = self::sanitize_options($postData['term_id']);
            if(!wp_verify_nonce($postData['nonce'], 'wcp_folder_term_'.$term_id)) {
                $response['message'] = __("Your request is not valid", WCP_FOLDER);
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
        die;
    }

    public function wcp_save_parent_data()
    {
        $response = array();
        $response['status'] = 0;
        $response['error'] = 0;
        $response['data'] = array();
        $response['message'] = "";
        $postData = $_POST;
        $errorCounter = 0;
        if (!isset($postData['type']) || empty($postData['type'])) {
            $response['message'] = __("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['message'] = __("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else {
            $type = self::sanitize_options($postData['type']);
            if(!wp_verify_nonce($postData['nonce'], 'wcp_folder_nonce_'.$type)) {
                $response['message'] = __("Your request is not valid", WCP_FOLDER);
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
        die;
    }

    public function wcp_remove_folder()
    {
        $response = array();
        $response['status'] = 0;
        $response['error'] = 0;
        $response['data'] = array();
        $response['message'] = "";
        $postData = $_POST;
        $errorCounter = 0;
        if (!current_user_can("manage_categories")) {
            $error = __("You have not permission to remove folder", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['term_id']) || empty($postData['term_id'])) {
            $error = __("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['type']) || empty($postData['type'])) {
            $error = __("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $error = "Unable to delete folder, Your request is not valid";
            $errorCounter++;
        } else {
            $term_id = self::sanitize_options($postData['term_id']);
            if(!wp_verify_nonce($postData['nonce'], 'wcp_folder_delete_term_'.$term_id)) {
                $error = "Unable to delete folder, Your request is not valid";
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
                $folders = self::total_term_folders();
            }
            $response['folders'] = $folders;
            $response['is_key_active'] = $is_active;
        } else {
            $response['error'] = 1;
            $response['message'] = $error;
        }
        echo json_encode($response);
        die;
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
            $error = __("You have not permission to update folder", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['term_id']) || empty($postData['term_id'])) {
            $error = __("Unable to rename folder, Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['name']) || empty($postData['name'])) {
            $error = __("Folder name can no be empty", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['type']) || empty($postData['type'])) {
            $error = __("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $error = __("Unable to rename folder, Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else {
            $term_id = self::sanitize_options($postData['term_id']);
            if(!wp_verify_nonce($postData['nonce'], 'wcp_folder_rename_term_'.$term_id)) {
                $error = _("Unable to rename folder, Your request is not valid", WCP_FOLDER);
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
                $response['message'] = __("Unable to rename folder", WCP_FOLDER);
            }
        } else {
            $response['error'] = 1;
            $response['message'] = $error;
        }
        echo json_encode($response);
        die;
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
            $value = sanitize_email($value);
        } else {
            $value = sanitize_text_field($value);
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
            $error = __("You have not permission to add folder", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['name']) || empty($postData['name'])) {
            $error = __("Folder name can no be empty", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['type']) || empty($postData['type'])) {
            $error = __("Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else if (!isset($postData['nonce']) || empty($postData['nonce'])) {
            $response['login'] = 0;
            $error = __("Unable to create folder, Your request is not valid", WCP_FOLDER);
            $errorCounter++;
        } else {
            $type = self::sanitize_options($postData['type']);
            if(!wp_verify_nonce($postData['nonce'], 'wcp_folder_nonce_'.$type)) {
                $response['login'] = 0;
                $error = __("Unable to create folder, Your request is not valid", WCP_FOLDER);
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
                    $string = "<li data-nonce='{$term_nonce}' data-star='{$highlight_nonce}' data-rename='{$rename_nonce}' data-delete='{$delete_nonce}' data-slug='{$result['term_id']}' class='ui-state-default route' id='wcp_folder_{$result['term_id']}' data-folder-id='{$result['term_id']}'><h3 class='title' id='title_{$result['term_id']}'><span class='title-text'>{$postData['name']}</span> <span class='update-inline-record'></span><span class='star-icon'></span> </h3><span class='nav-icon'><i class='wcp-icon folder-icon-arrow_right'></i></span><span class='ui-icon'><i class='wcp-icon folder-icon-folder'></i></span>	<ul class='space' id='space_{$result['term_id']}'>";
                    $string .= "</ul></li>";
                    $response['term_data'] = $string;
                    $response['parent_id'] = $parent;

                    $is_active = 1;
                    $folders = -1;
                    if (!self::check_has_valid_key()) {
                        $is_active = 0;
                        $folders = self::total_term_folders();
                    }
                    $response['is_key_active'] = $is_active;
                    $response['folders'] = $folders;
                } else {
                    $response['message'] = __("Error during server request", WCP_FOLDER);
                }
            } else {
                $response['error'] = 1;
                $response['message'] = __("Folder name is already exists", WCP_FOLDER);
            }
        } else {
            $response['error'] = 1;
            $response['message'] = $error;
        }
        echo json_encode($response);
        die;
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

        if ((isset($_POST['action']) && $_POST['action'] == 'inline-save') && (isset($_POST['post_type']) && self::is_for_this_post_type($_POST['post_type']))) {
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
                $folders = self::total_term_folders();
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

            $total_posts = self::get_total_posts($typenow);

            $total_empty = self::get_total_empty_posts($typenow);

            $folder_type = self::get_custom_post_type($typenow);
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

    public function get_total_posts($post_type = "")
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

    public function get_total_empty_posts($post_type = "")
    {
        $taxonomy = self::get_custom_post_type($post_type);
        $args = array(
            'posts_per_page' => -1,
            'post_type' => $post_type,
            'post_status' => 'inherit'
        );
        if ($post_type != "attachment") {
            $args['post_status'] = array('publish', 'draft', 'future', 'private');
        }
        $args['tax_query'] = array(
            array(
                'taxonomy'  => $taxonomy,
                'operator'  => 'NOT EXISTS',
            ),
        );
        $result = get_posts($args);

        return (count($result));
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
                    'name' => __('Folders', WCP_FOLDER),
                    'singular_name' => __('Folder', WCP_FOLDER),
                    'all_items' => __('All Folders', WCP_FOLDER),
                    'edit_item' => __('Edit Folder', WCP_FOLDER),
                    'update_item' => __('Update Folder', WCP_FOLDER),
                    'add_new_item' => __('Add New Folder', WCP_FOLDER),
                    'new_item_name' => __('Add Folder Name', WCP_FOLDER),
                    'menu_name' => __('Folders', WCP_FOLDER),
                    'search_items' => __('Search Folders', WCP_FOLDER),
                    'parent_item' => __('Parent Folder', WCP_FOLDER),
                );

                $args = array(
                    'label' => __('Folder'),
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
                    'rewrite' => false,
                );

                $folder_post_type = self::get_custom_post_type($post_type);

                register_taxonomy(
                    $folder_post_type,
                    $post_type,
                    $args
                );
            }
        }

        if(current_user_can("manage_categories")) {
            if (isset($_POST['folders_show_in_menu']) && !empty($_POST['folders_show_in_menu'])) {
                $show_menu = "off";
                if ($_POST['folders_show_in_menu'] == "on") {
                    $show_menu = "on";
                }
                update_option("folders_show_in_menu", $show_menu);
            }

            if (isset($_POST['folders_settings1'])) {
                $posts = array();
                if (isset($_POST['folders_settings']) && is_array($_POST['folders_settings'])) {
                    foreach ($_POST['folders_settings'] as $key => $val) {
                        $posts[] = $val;
                    }
                }
                update_option("folders_settings", $posts);
            }
        }

        $old_version = get_option("folder_old_plugin_status");
        if($old_version !== false && $old_version == 1) {
            $total_folders = get_option("folder_old_plugin_folder_status");
            if($total_folders === false) {
                $total = self::total_term_folders();
                if($total <= 10) {
                    $total = 10;
                };
                update_option("folder_old_plugin_folder_status", $total);
                self::$folders = $total;
            } else {
                self::$folders = $total_folders;
            }
        }

        $total_folders = get_option("folder_old_plugin_folder_status");
        if($total_folders === false) {
            self::$folders = 10;
        } else {
            self::$folders = $total_folders;
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
        echo '<div class="tree-structure" id="list-folder-' . $termId . '" data-id="' . $termId . '">';
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
            wp_register_script('wcp-folders-alert', plugin_dir_url(dirname(__FILE__)) . 'assets/js/sweetalert.all.min.js', array(), WCP_FOLDER_VERSION);
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
                $folders = self::total_term_folders();
            }
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
        array_unshift($links, '<a href="' . admin_url("admin.php?page=wcp_folders_settings") . '" >' . __('Settings', WCP_FOLDER) . '</a>');
        $links['pro'] = '<a class="wcp-folder-upgrade-button" href="'.admin_url("admin.php?page=wcp_folders_upgrade").'" >'.__( 'Upgrade', WCP_FOLDER ).'</a>';
        return $links;
    }

    public static function get_instance()
    {
        if (empty(self::$instance)) {
            self::$instance = new WCP_Folders();
        }
        return self::$instance;
    }

    public function check_and_set_post_type() {
        $options = get_option(WCP_FOLDER_VAR);
        $options = is_array($options)?$options:array();
        $old_plugin_status = 0;
        $post_array = array();
        if (!empty($options)) {
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

    public static function get_total_term_folders()
    {
        return self::total_term_folders();
    }

    function folders_register_settings()
    {
        register_setting('folders_settings', 'folders_settings1', 'folders_settings_validate');

        self::check_and_set_post_type();



        $option = get_option("folder_redirect_status", true);
        if ($option == 1) {
            update_option("folder_redirect_status", 0);
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

//        echo "<pre>"; print_r($defaults);
        if (is_array($options) && in_array($type, $options)) {
            $columns = array(
                    'wcp_move' => '<div class="wcp-move-multiple wcp-col" title="' . __('Move selected items', WCP_FOLDER) . '"><span class="dashicons dashicons-move"></span><div class="wcp-items"></div></div>',
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
        $page_title = __('Folders', WCP_FOLDER);
        $menu_title = __('Folders Settings', WCP_FOLDER);
        $capability = 'manage_options';
        $menu_slug = 'wcp_folders_settings';
        $callback = array($this, "wcp_folders_settings");
        $icon_url = 'dashicons-category';
        $position = 99;

        add_menu_page($page_title, $menu_title, $capability, $menu_slug, $callback, $icon_url, $position);

        add_submenu_page(
            $menu_slug,
            __('Upgrade to Pro', WCP_FOLDER),
            __('Upgrade to Pro', WCP_FOLDER),
            'manage_options',
            'wcp_folders_upgrade',
            array($this, 'wcp_folders_upgrade')
        );

        self::check_and_set_post_type();

        $show_menu = get_option("folders_show_in_menu", true);
        if ($show_menu == "on") {
            self::create_menu_for_folders();
        }
    }

    public function wcp_folders_upgrade()
    {
        self::set_default_values_if_not_exists();
        include_once dirname(dirname(__FILE__)) . "/templates/admin/upgrade-to-pro.php";
    }

    public function wcp_folders_settings()
    {
        self::set_default_values_if_not_exists();
        include_once dirname(dirname(__FILE__)) . "/templates/admin/general-settings.php";
    }

    public function set_default_values_if_not_exists()
    {
        $options = get_option(WCP_FOLDER_VAR);
        $options = is_array($options)?$options:array();
        foreach ($options as $option) {
            $post_type = self::get_custom_post_type($option);
            $terms = get_terms($post_type, array(
                    'hide_empty' => false,
                )
            );
            foreach ($terms as $term) {
                $order = get_term_meta($term->term_id, "wcp_custom_order", true);
                if (empty($order) || $order == null) {
                    update_term_meta($term->term_id, "wcp_custom_order", "1");
                }

            }
        }
    }
}