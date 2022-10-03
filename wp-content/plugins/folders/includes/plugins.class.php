<?php
/**
 * Class Folders Plugins import/export
 *
 * @author  : Premio <contact@premio.io>
 * @license : GPL2
 * */

if (! defined('ABSPATH')) {
    exit;
}

// Free/Pro Class name change
class WCP_Folder_Plugins
{

    /**
     * Collection of Plugins to import Data
     *
     * @var    array    $plugins    Collection of Plugins
     * @since  1.0.0
     * @access public
     */
    public $plugins = [];

    /**
     * Collection of Post types to Import
     *
     * @var    array    $postTypes    Post types to import
     * @since  1.0.0
     * @access public
     */
    public $postTypes = [];

    /**
     * Check is there any data to import
     *
     * @var    integer    $isExists    0/1
     * @since  1.0.0
     * @access public
     */
    public $isExists = 0;


    /**
     * Define the core functionality of the import data functionality.
     *
     * Import data from other plugins
     * Remove data from other plugins
     *
     * @since 1.0.0
     */
    public function __construct()
    {

        // Import plugin data
        add_action('wp_ajax_wcp_import_plugin_folders_data', [$this, 'import_plugin_folders_data']);
        add_action('wp_ajax_wcp_remove_plugin_folders_data', [$this, 'remove_plugin_folders_data']);

    }//end __construct()


    /**
     * Remove data from other plugins
     *
     * @since  1.0.0
     * @access public
     * @return $response
     */
    public function remove_plugin_folders_data()
    {
        global $wpdb;
        $postData = filter_input_array(INPUT_POST);

        $plugin   = isset($postData['plugin']) ? sanitize_text_field($postData['plugin']) : "";
        $nonce    = isset($postData['nonce']) ? sanitize_text_field($postData['nonce']) : "";
        $response = [];
        $response['status']         = 0;
        $response['message']        = esc_html__("Invalid request", "folders");
        $response['data']           = [];
        $response['data']['plugin'] = $plugin;
        if (wp_verify_nonce($nonce, "import_data_from_".$plugin)) {
            $this->get_plugin_information();
            $folders     = isset($this->plugins[$plugin]['folders']) ? $this->plugins[$plugin]['folders'] : [];
            $attachments = isset($this->plugins[$plugin]['attachments']) ? $this->plugins[$plugin]['attachments'] : [];

            if ($plugin != 'filebird' && $plugin != 'real-media-library') {
                $deleted = [];

                foreach ($folders as $folder) {
                    $term_id = intval($folder->term_id);

                    if ($term_id) {
                        $deleted[$term_id]['term_relationships'] = $wpdb->delete($wpdb->prefix.'term_relationships', ['term_taxonomy_id' => $term_id]);
                        $deleted[$term_id]['term_taxonomy']      = $wpdb->delete($wpdb->prefix.'term_taxonomy', ['term_id' => $term_id]);
                        $deleted[$term_id]['terms'] = $wpdb->delete($wpdb->prefix.'terms', ['term_id' => $term_id]);

                        if ($plugin === 'folders') {
                            $deleted[$term_id]['termmeta'] = $wpdb->delete($wpdb->prefix.'termmeta', ['term_id' => $term_id]);
                        }
                    }
                }
            } else {
                if (count($folders)) {
                    if ($plugin === 'filebird') {
                        $wpdb->query('DELETE FROM '.$wpdb->prefix.'fbv');
                    }

                    if ($plugin === 'real-media-library') {
                        $wpdb->query('DELETE FROM '.$wpdb->prefix.'realmedialibrary');

                        $wpdb->query('DELETE FROM '.$wpdb->prefix.'realmedialibrary_meta');
                    }
                }

                if (count($attachments)) {
                    if ($plugin === 'filebird') {
                        $wpdb->query('DELETE FROM '.$wpdb->prefix.'fbv_attachment_folder');
                    }

                    if ($plugin === 'real-media-library') {
                        $wpdb->query('DELETE FROM '.$wpdb->prefix.'realmedialibrary_posts');
                    }
                }
            }//end if

            $response['status'] = 1;
        }//end if

        echo json_encode($response);
        exit;

    }//end remove_plugin_folders_data()


    /**
     * Import data from other plugins
     *
     * @since  1.0.0
     * @access public
     * @return $response
     */
    public function import_plugin_folders_data()
    {
        $postData = filter_input_array(INPUT_POST);

        $plugin   = isset($postData['plugin']) ? sanitize_text_field($postData['plugin']) : "";
        $nonce    = isset($postData['nonce']) ? sanitize_text_field($postData['nonce']) : "";
        $response = [];
        $response['status']         = 0;
        $response['message']        = esc_html__("Invalid request", "folders");
        $response['data']           = [];
        $response['data']['plugin'] = $plugin;
        if (wp_verify_nonce($nonce, "import_data_from_".$plugin)) {
            $this->get_plugin_information();
            $folders     = isset($this->plugins[$plugin]['folders']) ? $this->plugins[$plugin]['folders'] : [];
            $attachments = isset($this->plugins[$plugin]['attachments']) ? $this->plugins[$plugin]['attachments'] : [];

            $categoryByID        = [];
            $foldersImported     = [];
            $attachmentsImported = [];

            if ($plugin != 'filebird' && $plugin != 'real-media-library') {
                foreach ($folders as $folder) {
                    $folder_id = $folder->term_id;
                    $parent    = intval($folder->parent);

                    $taxonomy = 'media_folder';

                    foreach ($this->postTypes as $post_type) {
                        if (strpos($folder->taxonomy, $post_type) !== false) {
                            if ($post_type == "post") {
                                $taxonomy = "post_folder";
                            } else if ($post_type == "page") {
                                $taxonomy = "folder";
                            } else if ($post_type == "attachment") {
                                $taxonomy = "media_folder";
                            } else {
                                $taxonomy = $post_type.'_folder';
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

                    $arg      = [
                        'hide_empty'            => false,
                        'parent'                => $parent,
                        'hierarchical'          => false,
                        'update_count_callback' => '_update_generic_term_count',
                    ];
                    $terms    = get_terms($taxonomy, $arg);
                    $position = count($terms);

                    if ($plugin == 'mediamatic' || $plugin == 'happyfiles') {
                        $meta_key = "";
                        if ($plugin == 'mediamatic') {
                            $meta_key = "folder_position";
                        } else if ($plugin == 'happyfiles') {
                            $meta_key = "happyfiles_position";
                        }

                        if (!empty($meta_key)) {
                            $folder_position = get_term_meta($new_term['term_id'], $meta_key, true);
                            if (empty($folder_position)) {
                                $position = intval($folder_position);
                            }
                        }
                    }

                    update_term_meta($new_term['term_id'], 'wcp_custom_order', $position);

                    $foldersImported[] = $new_term;

                    $categoryByID[$folder_id] = [
                        'term_id' => $new_term['term_id'],
                        'parent'  => $parent,
                        'name'    => $folder->name,
                    ];
                }//end foreach

                // STEP: Assign plugin categories to HF categories
                foreach ($attachments as $attachment) {
                    $hf_category_id = isset($categoryByID[$attachment->term_taxonomy_id]['term_id']) ? intval($categoryByID[$attachment->term_taxonomy_id]['term_id']) : 0;
                    $attachment_id  = isset($attachment->object_id) ? intval($attachment->object_id) : 0;

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
                        $taxonomy = $post_type.'_folder';
                    }

                    $term_ids   = wp_get_object_terms($attachment_id, $taxonomy, ['fields' => 'ids']);
                    $term_ids[] = $hf_category_id;

                    $term_set = wp_set_object_terms($attachment_id, $term_ids, $taxonomy);

                    if (is_wp_error($term_set)) {
                        continue;
                    }

                    $attachmentsImported[] = [
                        'cat_id'   => $hf_category_id,
                        'term_ids' => $term_ids,
                        'set'      => $term_set,
                    ];
                }//end foreach
            } else {
                foreach ($folders as $folder) {
                    $parent   = intval($folder->parent);
                    $parentID = 0;

                    if ($parent && isset($categoryByID[$parent]['term_id'])) {
                        $parentID = $categoryByID[$parent]['term_id'];
                    }

                    $new_term = wp_insert_term($folder->name, "media_folder", ['parent' => $parentID]);

                    if (is_wp_error($new_term)) {
                        continue;
                    }

                    $taxonomy = 'media_folder';

                    $arg      = [
                        'hide_empty'            => false,
                        'parent'                => $parentID,
                        'hierarchical'          => false,
                        'update_count_callback' => '_update_generic_term_count',
                    ];
                    $terms    = get_terms($taxonomy, $arg);
                    $position = count($terms);

                    update_term_meta($new_term['term_id'], 'wcp_custom_order', intval($position));

                    $foldersImported[] = $new_term;

                    $categoryByID[$folder->id] = [
                        'name'    => $folder->name,
                        'parent'  => $parent,
                        'term_id' => $new_term['term_id'],
                    ];
                }//end foreach

                foreach ($attachments as $attachment) {
                    $hf_category_id = isset($categoryByID[$attachment->folder_id]['term_id']) ? intval($categoryByID[$attachment->folder_id]['term_id']) : 0;
                    $attachment_id  = isset($attachment->attachment_id) ? intval($attachment->attachment_id) : 0;

                    if (! $hf_category_id || ! $attachment_id) {
                        continue;
                    }

                    $term_ids   = wp_get_object_terms($attachment_id, "media_folder", ['fields' => 'ids']);
                    $term_ids[] = $hf_category_id;

                    $term_set = wp_set_object_terms($attachment_id, $term_ids, "media_folder");

                    if (is_wp_error($term_set)) {
                        continue;
                    }

                    $attachmentsImported[] = [
                        'cat_id'   => $hf_category_id,
                        'term_ids' => $term_ids,
                        'set'      => $term_set,
                    ];
                }//end foreach
            }//end if

            $response['status']           = 1;
            $response['data']['imported'] = count($foldersImported);
            $response['data']['attachments'] = count($attachmentsImported);
            $response['data']['plugin']      = $plugin;
            $response['message'] = sprintf(esc_html__('%s folders imported and %s attachments categorized.', 'folders'), count($foldersImported), count($attachmentsImported));
        }//end if

        echo json_encode($response);
        exit;

    }//end import_plugin_folders_data()


    /**
     * Get installed Plugins list
     *
     * @since  1.0.0
     * @access public
     * @return $plugins
     */
    public function get_plugin_information()
    {
        $this->get_other_plugins_data();
        return $this->plugins;

    }//end get_plugin_information()


    /**
     * Get installed Plugins list to Import data
     *
     * @since  1.0.0
     * @access public
     * @return $plugins
     */
    public function get_other_plugins_data()
    {
        if (!empty($this->plugins)) {
            return $this->plugins;
        }

        $this->plugins   = [
            // FileBird
            'filebird'               => [
                'name'              => 'FileBird (v4)',
                'taxonomy'          => 'filebird',
                'folders'           => [],
                'attachments'       => [],
                'total_folders'     => 0,
                'total_attachments' => 0,
                'isExists'          => 0,
            ],
            'enhanced-media-library' => [
                // Enhanced Media Library
                'name'              => 'Enhanced Media Library',
                'taxonomy'          => 'media_category',
                'folders'           => [],
                'attachments'       => [],
                'total_folders'     => 0,
                'total_attachments' => 0,
                'isExists'          => 0,
            ],
            'wicked-folders'         => [
                // Enhanced Media Library
                'name'              => 'Wicked Folders',
                'taxonomy'          => 'wf_attachment_folders',
                'folders'           => [],
                'attachments'       => [],
                'total_folders'     => 0,
                'total_attachments' => 0,
                'isExists'          => 0,
            ],
            'real-media-library'     => [
                // Real Media Library
                'name'              => 'Real Media Library (by DevOwl)',
                'taxonomy'          => 'rml',
            // has custom DB table
                'folders'           => [],
                'attachments'       => [],
                'total_folders'     => 0,
                'total_attachments' => 0,
                'isExists'          => 0,
            ],
            'wp-media-folder'        => [
                // Real Media Library
                'name'              => 'WP Media Folder (by JoomUnited)',
                'taxonomy'          => 'wpmf-category',
                'folders'           => [],
                'attachments'       => [],
                'total_folders'     => 0,
                'total_attachments' => 0,
                'isExists'          => 0,
            ],
            'mediamatic'             => [
                // Mediamatic
                'name'              => 'WordPress Media Library Folders | Mediamatic',
                'taxonomy'          => 'mediamatic_wpfolder',
                'folders'           => [],
                'attachments'       => [],
                'total_folders'     => 0,
                'total_attachments' => 0,
                'isExists'          => 0,
            ],
            'happyfiles'             => [
                // HappyFiles
                'name'              => 'HappyFiles',
                'taxonomy'          => 'happyfiles_category',
                'folders'           => [],
                'attachments'       => [],
                'total_folders'     => 0,
                'total_attachments' => 0,
                'isExists'          => 0,
            ],
        ];
        $postTypes       = get_post_types([]);
        $this->postTypes = array_keys($postTypes);

        foreach ($this->plugins as $slug => $plugin_data) {
            $taxonomy = $plugin_data['taxonomy'];

            if ($slug === 'wicked-folders') {
                // Run for all registered post types
                $folders = [];

                foreach ($this->postTypes as $post_type) {
                    $wicked_folders = $this->get_plugin_folders('wf_'.$post_type.'_folders', $slug);

                    if (is_array($wicked_folders)) {
                        $folders = array_merge($folders, $wicked_folders);
                    }
                }
            } else {
                $folders = $this->get_plugin_folders($taxonomy, $slug);
            }

            if (in_array($taxonomy, ['filebird', 'rml'])) {
                $folders = is_array($folders) && count($folders) ? $this->map_plugin_folders($taxonomy, $folders) : [];
            }

            $this->plugins[$slug]['folders'] = $folders;

            $attachments = is_array($folders) && count($folders) ? $this->get_plugin_attachments($taxonomy, $folders) : [];

            if (in_array($taxonomy, ['filebird', 'rml'])) {
                $attachments = $this->map_plugin_attachments($taxonomy, $attachments);
            }

            $this->plugins[$slug]['attachments'] = $attachments;
        }//end foreach

        foreach ($this->plugins as $key => $plugin) {
            $folders = isset($plugin['folders'])&&is_array($plugin['folders']) ? $plugin['folders'] : [];
            $this->plugins[$key]['total_folders'] = count($folders);

            $attachments = isset($plugin['attachments'])&&is_array($plugin['attachments']) ? $plugin['attachments'] : [];
            $this->plugins[$key]['total_attachments'] = count($attachments);

            if (count($folders) > 0 || count($attachments) > 0) {
                $this->plugins[$key]['isExists'] = 1;
                $this->isExists = 1;
            }
        }

    }//end get_other_plugins_data()


    /**
     * Get Folders Data From Imported Plugins
     *
     * @since  1.0.0
     * @access public
     * @return $taxonomy
     */
    public function get_plugin_folders($taxonomy, $slug)
    {
        global $wpdb;

        // FileBird has its own db table
        if ($taxonomy === 'filebird') {
            $filebird_folders_table = $wpdb->prefix.'fbv';

            // Get FileBird folders (order by 'parent' to create parent categories first)
            if ($wpdb->get_var("SHOW TABLES LIKE '$filebird_folders_table'") == $filebird_folders_table) {
                return $wpdb->get_results("SELECT * FROM $filebird_folders_table ORDER BY parent ASC");
            }
        }

        // Real Media Library has its own db table
        else if ($taxonomy === 'rml') {
            $rml_folders_table = $wpdb->prefix.'realmedialibrary';

            // Get FileBird folders (order by 'parent' to create parent categories first)
            if ($wpdb->get_var("SHOW TABLES LIKE '$rml_folders_table'") == $rml_folders_table) {
                return $wpdb->get_results("SELECT * FROM $rml_folders_table ORDER BY parent ASC");
            }
        }

        // Default: Plugins with custom taxonomy terms
        else {
            $query   = "SELECT * FROM ".$wpdb->term_taxonomy."
					LEFT JOIN  ".$wpdb->terms."
					ON  ".$wpdb->term_taxonomy.".term_id =  ".$wpdb->terms.".term_id
					WHERE ".$wpdb->term_taxonomy.".taxonomy = '%d'
					ORDER BY parent ASC";
            $query   = $wpdb->prepare($query, $taxonomy);
            $folders = $wpdb->get_results($query);

            // WP Media Folder (JoomUnited): Remove root folder
            if ($slug === 'wp-media-folder') {
                foreach ($folders as $index => $folder) {
                    if ($folder->slug === 'wp-media-folder-root') {
                        unset($folders[$index]);
                    }
                }
            }

            return array_values($folders);
        }

    }//end get_plugin_folders()


    /**
     * Get Folders Data From Imported Plugins
     *
     * @since  1.0.0
     * @access public
     * @return $taxonomy
     */
    public function map_plugin_folders($taxonomy, $folders)
    {
        $mapped_folders = [];

        foreach ($folders as $folder) {
            // FileBird, Real Media Library
            if ($taxonomy === 'filebird' || $taxonomy === 'rml') {
                $folder_object = new \stdClass();

                $folder_object->name     = $folder->name;
                $folder_object->id       = intval($folder->id);
                $folder_object->parent   = intval($folder->parent);
                $folder_object->position = intval($folder->ord);

                $mapped_folders[] = $folder_object;
            }
        }

        return $mapped_folders;

    }//end map_plugin_folders()


    /**
     * Save Folders and it's attachment Data From Imported Plugins
     *
     * @since  1.0.0
     * @access public
     * @return $files
     */
    public function map_plugin_attachments($taxonomy, $attachments)
    {
        $mapped_attachments = [];

        foreach ($attachments as $folder) {
            // FileBird
            if ($taxonomy === 'filebird') {
                $folder_object = new \stdClass();

                $folder_object->folder_id     = intval($folder->folder_id);
                $folder_object->attachment_id = intval($folder->attachment_id);

                $mapped_attachments[] = $folder_object;
            }

            // Real Media Library
            if ($taxonomy === 'rml') {
                $folder_object = new \stdClass();

                $folder_object->folder_id     = intval($folder->fid);
                $folder_object->attachment_id = intval($folder->attachment);

                $mapped_attachments[] = $folder_object;
            }
        }//end foreach

        return $mapped_attachments;

    }//end map_plugin_attachments()


    /**
     * Get Folders and it's attachment Data From Imported Plugins
     *
     * @since  1.0.0
     * @access public
     * @return $files
     */
    public function get_plugin_attachments($taxonomy, $folders)
    {
        global $wpdb;

        // FileBird has its own db table
        if ($taxonomy === 'filebird') {
            $filebird_attachments_table = $wpdb->prefix.'fbv_attachment_folder';

            // Get FileBird attachments (order by 'folder_id')
            if ($wpdb->get_var("SHOW TABLES LIKE '$filebird_attachments_table'") == $filebird_attachments_table) {
                return $wpdb->get_results("SELECT * FROM $filebird_attachments_table ORDER BY folder_id ASC");
            }
        }

        // Real Media Library has its own db table
        else if ($taxonomy === 'rml') {
            $rml_attachments_table = $wpdb->prefix.'realmedialibrary_posts';

            // Get FileBird folders (order by 'parent' to create parent categories first)
            if ($wpdb->get_var("SHOW TABLES LIKE '$rml_attachments_table'") == $rml_attachments_table) {
                return $wpdb->get_results("SELECT * FROM $rml_attachments_table ORDER BY fid ASC");
            }
        }

        // Default: Plugins with custom taxonomy terms
        else {
            $query = "SELECT  ".$wpdb->term_relationships.".object_id,
				".$wpdb->term_relationships.".term_taxonomy_id
				FROM ".$wpdb->term_relationships."
				WHERE ".$wpdb->term_relationships.".term_taxonomy_id IN (%s)";
            $query = $wpdb->prepare($query, implode(',', array_column($folders, 'term_id')));
            return $wpdb->get_results($query);
        }

    }//end get_plugin_attachments()


}//end class

$WCP_Folder_Plugins = new WCP_Folder_Plugins();
