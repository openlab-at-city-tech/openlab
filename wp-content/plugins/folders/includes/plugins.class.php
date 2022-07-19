<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/* Free/Pro Class name change */
class WCP_Folder_Plugins {

    public $plugins = array();
    public $post_types = array();
    public $is_exists = 0;

    public function __construct() {

        /* Import plugin data */
        add_action( 'wp_ajax_wcp_import_plugin_folders_data', array($this, 'import_plugin_folders_data'));
        add_action( 'wp_ajax_wcp_remove_plugin_folders_data', array($this, 'remove_plugin_folders_data'));
    }


    public function remove_plugin_folders_data(){
        global $wpdb;
        $postData = filter_input_array(INPUT_POST);

        $plugin = isset($postData['plugin']) ? $postData['plugin'] : "";
        $nonce = isset($postData['nonce']) ? $postData['nonce'] : "";
        $response = array();
        $response['status'] = 0;
        $response['message'] = esc_html__("Invalid request", "folders");
        $response['data'] = array();
        $response['data']['plugin'] = $plugin;
        if (wp_verify_nonce($nonce, "import_data_from_" . $plugin)) {
            $this->get_plugin_information();
            $folders = isset($this->plugins[$plugin]['folders']) ? $this->plugins[$plugin]['folders'] : array();
            $attachments = isset($this->plugins[$plugin]['attachments']) ? $this->plugins[$plugin]['attachments'] : array();

            if($plugin != 'filebird' && $plugin != 'real-media-library') {
                $deleted = [];

                foreach ( $folders as $folder ) {
                    $term_id = intval( $folder->term_id );

                    if ( $term_id ) {
                        $deleted[$term_id]['term_relationships'] = $wpdb->delete( $wpdb->prefix . 'term_relationships', ['term_taxonomy_id' => $term_id] );
                        $deleted[$term_id]['term_taxonomy'] = $wpdb->delete( $wpdb->prefix . 'term_taxonomy', ['term_id' => $term_id] );
                        $deleted[$term_id]['terms'] = $wpdb->delete( $wpdb->prefix . 'terms', ['term_id' => $term_id] );

                        if ( $plugin === 'folders' ) {
                            $deleted[$term_id]['termmeta'] = $wpdb->delete( $wpdb->prefix . 'termmeta', ['term_id' => $term_id] );
                        }
                    }
                }
            } else {

                if ( count( $folders ) ) {
                    if ( $plugin === 'filebird' ) {
                        $wpdb->query( 'DELETE FROM ' . $wpdb->prefix . 'fbv' );
                    }

                    if ( $plugin === 'real-media-library' ) {
                        $wpdb->query( 'DELETE FROM ' . $wpdb->prefix . 'realmedialibrary' );

                        $wpdb->query( 'DELETE FROM ' . $wpdb->prefix . 'realmedialibrary_meta' );
                    }
                }

                if ( count( $attachments ) ) {
                    if ( $plugin === 'filebird' ) {
                        $wpdb->query( 'DELETE FROM ' . $wpdb->prefix . 'fbv_attachment_folder' );
                    }

                    if ( $plugin === 'real-media-library' ) {
                        $wpdb->query( 'DELETE FROM ' . $wpdb->prefix . 'realmedialibrary_posts' );
                    }
                }
            }
            $response['status'] = 1;

        }
        echo json_encode($response);
        exit;
    }

    public function import_plugin_folders_data() {
        $postData = filter_input_array(INPUT_POST);

        $plugin = isset($postData['plugin']) ?$postData['plugin']:"";
        $nonce = isset($postData['nonce']) ?$postData['nonce']:"";
        $response = array();
        $response['status'] = 0;
        $response['message'] = esc_html__("Invalid request", "folders");
        $response['data'] = array();
        $response['data']['plugin'] = $plugin;
        if(wp_verify_nonce($nonce, "import_data_from_".$plugin)) {
            $this->get_plugin_information();
            $folders = isset($this->plugins[$plugin]['folders']) ? $this->plugins[$plugin]['folders'] : array();
            $attachments = isset($this->plugins[$plugin]['attachments']) ? $this->plugins[$plugin]['attachments'] : array();

            $categoryByID = array();
            $foldersImported = array();
            $attachmentsImported = array();

            if($plugin != 'filebird' && $plugin != 'real-media-library') {

                foreach ($folders as $folder) {
                    $folder_id = $folder->term_id;
                    $parent = intval($folder->parent);

                    $taxonomy = 'media_folder';

                    foreach ($this->post_types as $post_type) {
                        if (strpos($folder->taxonomy, $post_type) !== false) {
                            if ($post_type == "post") {
                                $taxonomy = "post_folder";
                            } else if ($post_type == "page") {
                                $taxonomy = "folder";
                            } else if ($post_type == "attachment") {
                                $taxonomy = "media_folder";
                            } else {
                                $taxonomy = $post_type . '_folder';
                            }
                        }
                    }

                    if ($parent && isset($categoryByID[$parent]['term_id'])) {
                        $parent = intval($categoryByID[$parent]['term_id']);
                    }

                    $new_term = wp_insert_term($folder->name, $taxonomy, ['parent' => $parent]);

                    if (is_wp_error($new_term)) {
                        continue;
                    }

                    $arg = array(
                        'hide_empty' => false,
                        'parent'   => $parent,
                        'hierarchical' => false,
                        'update_count_callback' => '_update_generic_term_count',
                    );
                    $terms = get_terms( $taxonomy, $arg);
                    $position = count($terms);

                    if($plugin == 'mediamatic' || $plugin == 'happyfiles') {
                        $meta_key = "";
                        if($plugin == 'mediamatic') {
                            $meta_key = "folder_position";
                        } else if($plugin == 'happyfiles') {
                            $meta_key = "happyfiles_position";
                        }
                        if(!empty($meta_key)) {
                            $folder_position = get_term_meta($new_term['term_id'], $meta_key, true);
                            if(empty($folder_position)) {
                                $position = intval($folder_position);
                            }
                        }
                    }

                    update_term_meta($new_term['term_id'], 'wcp_custom_order', $position);

                    $foldersImported[] = $new_term;

                    $categoryByID[$folder_id] = [
                        'term_id' => $new_term['term_id'],
                        'parent' => $parent,
                        'name' => $folder->name,
                    ];
                }

                // STEP: Assign plugin categories to HF categories
                foreach ($attachments as $attachment) {
                    $hf_category_id = isset($categoryByID[$attachment->term_taxonomy_id]['term_id']) ? intval($categoryByID[$attachment->term_taxonomy_id]['term_id']) : 0;
                    $attachment_id = isset($attachment->object_id) ? intval($attachment->object_id) : 0;

                    if (!$hf_category_id || !$attachment_id) {
                        continue;
                    }

                    // Get attachment taxonomy by post type
                    $post_type = get_post_type($attachment_id);
                    if ($post_type == "post") {
                        $taxonomy = "post_folder";
                    } else if ($post_type == "page") {
                        $taxonomy = "folder";
                    } else if ($post_type == "attachment") {
                        $taxonomy = "media_folder";
                    } else {
                        $taxonomy = $post_type . '_folder';
                    }

                    $term_ids = wp_get_object_terms($attachment_id, $taxonomy, ['fields' => 'ids']);
                    $term_ids[] = $hf_category_id;

                    $term_set = wp_set_object_terms($attachment_id, $term_ids, $taxonomy);

                    if (is_wp_error($term_set)) {
                        continue;
                    }

                    $attachmentsImported[] = [
                        'cat_id' => $hf_category_id,
                        'term_ids' => $term_ids,
                        'set' => $term_set,
                    ];
                }
            } else {
                foreach ( $folders as $folder ) {
                    $parent = intval( $folder->parent );
                    $parentID = 0;

                    if ( $parent && isset( $categoryByID[$parent]['term_id'] ) ) {
                        $parentID = $categoryByID[$parent]['term_id'];
                    }

                    $new_term = wp_insert_term( $folder->name, "media_folder", ['parent' => $parentID] );

                    if ( is_wp_error( $new_term ) ) {
                        continue;
                    }

                    $taxonomy = 'media_folder';

                    $arg = array(
                        'hide_empty' => false,
                        'parent'   => $parentID,
                        'hierarchical' => false,
                        'update_count_callback' => '_update_generic_term_count',
                    );
                    $terms = get_terms( $taxonomy, $arg);
                    $position = count($terms);


                    update_term_meta( $new_term['term_id'], 'wcp_custom_order', intval( $position ) );

                    $foldersImported[] = $new_term;

                    $categoryByID[$folder->id] = [
                        'name'    => $folder->name,
                        'parent'  => $parent,
                        'term_id' => $new_term['term_id'],
                    ];
                }

                foreach ( $attachments as $attachment ) {
                    $hf_category_id = isset( $categoryByID[$attachment->folder_id]['term_id'] ) ? intval( $categoryByID[$attachment->folder_id]['term_id'] ) : 0;
                    $attachment_id = isset( $attachment->attachment_id ) ? intval( $attachment->attachment_id ) : 0;

                    if ( ! $hf_category_id || ! $attachment_id ) {
                        continue;
                    }

                    $term_ids = wp_get_object_terms( $attachment_id, "media_folder", ['fields' => 'ids'] );
                    $term_ids[] = $hf_category_id;

                    $term_set = wp_set_object_terms( $attachment_id, $term_ids, "media_folder" );

                    if ( is_wp_error( $term_set ) ) {
                        continue;
                    }

                    $attachmentsImported[] = [
                        'cat_id'   => $hf_category_id,
                        'term_ids' => $term_ids,
                        'set'      => $term_set,
                    ];
                }
            }

            $response['status'] = 1;
            $response['data']['imported'] = count($foldersImported);
            $response['data']['attachments'] = count($attachmentsImported);
            $response['data']['plugin'] = $plugin;
            $response['message'] = sprintf(esc_html__( '%s folders imported and %s attachments categorized.', 'folders'), count($foldersImported), count($attachmentsImported));
        }
        echo json_encode($response);
        exit;
    }

    public function get_plugin_information() {
        $this->get_other_plugins_data();
        return $this->plugins;
    }

    public function get_other_plugins_data() {
        if(!empty($this->plugins)) {
            return $this->plugins;
        }
        $this->plugins = array(
            // FileBird
            'filebird' => array(
                'name'        => 'FileBird (v4)',
                'taxonomy'    => 'filebird',  // has custom DB table
                'folders'     => array(),
                'attachments' => array(),
                'total_folders' => 0,
                'total_attachments' => 0,
                'is_exists'   => 0
            ),
            'enhanced-media-library' => array(
                // Enhanced Media Library
                'name'        => 'Enhanced Media Library',
                'taxonomy'    => 'media_category',
                'folders'     => array(),
                'attachments' => array(),
                'total_folders' => 0,
                'total_attachments' => 0,
                'is_exists'   => 0
            ),
            'wicked-folders' => array(
                // Enhanced Media Library
                'name'        => 'Wicked Folders',
                'taxonomy'    => 'wf_attachment_folders',
                'folders'     => array(),
                'attachments' => array(),
                'total_folders' => 0,
                'total_attachments' => 0,
                'is_exists'   => 0
            ),
            'real-media-library' => array(
                // Real Media Library
                'name'        => 'Real Media Library (by DevOwl)',
                'taxonomy'    => 'rml', // has custom DB table
                'folders'     => array(),
                'attachments' => array(),
                'total_folders' => 0,
                'total_attachments' => 0,
                'is_exists'   => 0
            ),
            'wp-media-folder' => array(
                // Real Media Library
                'name'        => 'WP Media Folder (by JoomUnited)',
                'taxonomy'    => 'wpmf-category',
                'folders'     => array(),
                'attachments' => array(),
                'total_folders' => 0,
                'total_attachments' => 0,
                'is_exists'   => 0
            ),
            'mediamatic' => array(
                // Mediamatic
                'name'        => 'WordPress Media Library Folders | Mediamatic',
                'taxonomy'    => 'mediamatic_wpfolder',
                'folders'     => array(),
                'attachments' => array(),
                'total_folders' => 0,
                'total_attachments' => 0,
                'is_exists'   => 0
            ),
            'happyfiles' => array(
                // HappyFiles
                'name'        => 'HappyFiles',
                'taxonomy'    => 'happyfiles_category',
                'folders'     => array(),
                'attachments' => array(),
                'total_folders' => 0,
                'total_attachments' => 0,
                'is_exists'   => 0
            )
        );
        $post_types = get_post_types(array());
        $this->post_types = array_keys($post_types);

        foreach ($this->plugins as $slug => $plugin_data ) {
            $taxonomy = $plugin_data['taxonomy'];

            if ( $slug === 'wicked-folders' ) {
                // Run for all registered post types
                $folders = [];

                foreach ( $this->post_types as $post_type ) {
                    $wicked_folders = $this->get_plugin_folders( 'wf_' . $post_type . '_folders', $slug );

                    if ( is_array( $wicked_folders ) ) {
                        $folders = array_merge( $folders, $wicked_folders );
                    }
                }
            }

            else {
                $folders = $this->get_plugin_folders( $taxonomy, $slug );
            }

            if ( in_array( $taxonomy, ['filebird', 'rml'] ) ) {
                $folders = is_array( $folders ) && count( $folders ) ? $this->map_plugin_folders( $taxonomy, $folders ) : [];
            }

            $this->plugins[$slug]['folders'] = $folders;

            $attachments = is_array( $folders ) && count( $folders ) ? $this->get_plugin_attachments( $taxonomy, $folders ) : [];

            if ( in_array( $taxonomy, ['filebird', 'rml'] ) ) {
                $attachments = $this->map_plugin_attachments( $taxonomy, $attachments );
            }

            $this->plugins[$slug]['attachments'] = $attachments;
        }

        foreach ($this->plugins as $key=>$plugin) {
            $folders = isset($plugin['folders'])&&is_array($plugin['folders'])?$plugin['folders']:array();
            $this->plugins[$key]['total_folders'] = count($folders);

            $attachments = isset($plugin['attachments'])&&is_array($plugin['attachments'])?$plugin['attachments']:array();
            $this->plugins[$key]['total_attachments'] = count($attachments);

            if(count($folders) > 0 || count($attachments)>0) {
                $this->plugins[$key]['is_exists'] = 1;
                $this->is_exists = 1;
            }
        }
    }

    public function get_plugin_folders( $taxonomy, $slug ) {
        global $wpdb;

        // FileBird has its own db table
        if ( $taxonomy === 'filebird' ) {
            $filebird_folders_table = $wpdb->prefix . 'fbv';

            // Get FileBird folders (order by 'parent' to create parent categories first)
            if ( $wpdb->get_var( "SHOW TABLES LIKE '$filebird_folders_table'") == $filebird_folders_table ) {
                return $wpdb->get_results( "SELECT * FROM $filebird_folders_table ORDER BY parent ASC" );
            }
        }

        // Real Media Library has its own db table
        else if ( $taxonomy === 'rml' ) {
            $rml_folders_table = $wpdb->prefix . 'realmedialibrary';

            // Get FileBird folders (order by 'parent' to create parent categories first)
            if ( $wpdb->get_var( "SHOW TABLES LIKE '$rml_folders_table'") == $rml_folders_table ) {
                return $wpdb->get_results( "SELECT * FROM $rml_folders_table ORDER BY parent ASC" );
            }
        }

        // Default: Plugins with custom taxonomy terms
        else {
            $folders = $wpdb->get_results(
                "SELECT * FROM " . $wpdb->term_taxonomy . "
					LEFT JOIN  " . $wpdb->terms . "
					ON  " . $wpdb->term_taxonomy . ".term_id =  " . $wpdb->terms . ".term_id
					WHERE " . $wpdb->term_taxonomy . ".taxonomy = '" . $taxonomy . "'
					ORDER BY parent ASC"
            );

            // WP Media Folder (JoomUnited): Remove root folder
            if ( $slug === 'wp-media-folder' ) {
                foreach ( $folders as $index => $folder ) {
                    if ( $folder->slug === 'wp-media-folder-root' ) {
                        unset( $folders[$index] );
                    }
                }
            }

            return array_values( $folders );
        }
    }

    public function map_plugin_folders( $taxonomy, $folders ) {
        $mapped_folders = [];

        foreach ( $folders as $folder ) {

            // FileBird, Real Media Library
            if ( $taxonomy === 'filebird' || $taxonomy === 'rml' ) {
                $folder_object = new \stdClass();

                $folder_object->name = $folder->name;
                $folder_object->id = intval( $folder->id );
                $folder_object->parent = intval( $folder->parent );
                $folder_object->position = intval( $folder->ord );

                $mapped_folders[] = $folder_object;
            }

        }

        return $mapped_folders;
    }

    public function map_plugin_attachments( $taxonomy, $attachments ) {
        $mapped_attachments = [];

        foreach ( $attachments as $folder ) {

            // FileBird
            if ( $taxonomy === 'filebird' ) {
                $folder_object = new \stdClass();

                $folder_object->folder_id = intval( $folder->folder_id );
                $folder_object->attachment_id = intval( $folder->attachment_id );

                $mapped_attachments[] = $folder_object;
            }

            // Real Media Library
            if ( $taxonomy === 'rml' ) {
                $folder_object = new \stdClass();

                $folder_object->folder_id = intval( $folder->fid );
                $folder_object->attachment_id = intval( $folder->attachment );

                $mapped_attachments[] = $folder_object;
            }

        }

        return $mapped_attachments;
    }

    public function get_plugin_attachments( $taxonomy, $folders ) {
        global $wpdb;

        // FileBird has its own db table
        if ( $taxonomy === 'filebird' ) {
            $filebird_attachments_table = $wpdb->prefix . 'fbv_attachment_folder';

            // Get FileBird attachments (order by 'folder_id')
            if ( $wpdb->get_var( "SHOW TABLES LIKE '$filebird_attachments_table'") == $filebird_attachments_table ) {
                return $wpdb->get_results( "SELECT * FROM $filebird_attachments_table ORDER BY folder_id ASC" );
            }
        }

        // Real Media Library has its own db table
        else if ( $taxonomy === 'rml' ) {
            $rml_attachments_table = $wpdb->prefix . 'realmedialibrary_posts';

            // Get FileBird folders (order by 'parent' to create parent categories first)
            if ( $wpdb->get_var( "SHOW TABLES LIKE '$rml_attachments_table'") == $rml_attachments_table ) {
                return $wpdb->get_results( "SELECT * FROM $rml_attachments_table ORDER BY fid ASC" );
            }
        }

        // Default: Plugins with custom taxonomy terms
        else {
            return $wpdb->get_results(
                "SELECT  " . $wpdb->term_relationships . ".object_id,
				" . $wpdb->term_relationships . ".term_taxonomy_id
				FROM " . $wpdb->term_relationships . "
				WHERE " . $wpdb->term_relationships . ".term_taxonomy_id IN (" . implode( ',', array_column( $folders, 'term_id' ) ) . ")"
            );
        }
    }
}
$WCP_Folder_Plugins = new WCP_Folder_Plugins();