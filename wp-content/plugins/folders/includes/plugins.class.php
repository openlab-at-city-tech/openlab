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
     * @var    array    $post_types    Post types to import
     * @since  1.0.0
     * @access public
     */
    public $post_types = [];

    /**
     * Check is there any data to import
     *
     * @var    integer    $is_exists    0/1
     * @since  1.0.0
     * @access public
     */
    public $is_exists = 0;


    /**
     * Define the core functionality of the import data functionality.
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

            if ($plugin != 'filebird' && $plugin != 'real-media-library' && $plugin != 'catfolders') {
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

                    if ($plugin === 'catfolders') {
                        $wpdb->query('DELETE FROM '.$wpdb->prefix.'catfolders');
                    }
                }

                if (count($attachments)) {
                    if ($plugin === 'filebird') {
                        $wpdb->query('DELETE FROM '.$wpdb->prefix.'fbv_attachment_folder');
                    }

                    if ($plugin === 'real-media-library') {
                        $wpdb->query('DELETE FROM '.$wpdb->prefix.'realmedialibrary_posts');
                    }

                    if ($plugin === 'catfolders') {
                        $wpdb->query('DELETE FROM '.$wpdb->prefix.'catfolders_posts');
                    }
                }
            }//end if

            $response['status'] = 1;
        }//end if

        echo wp_json_encode($response);
        exit;

    }//end remove_plugin_folders_data()


    public function filter_categories_by_term_id($attachments)
    {
        $termsData = [];
        foreach ($attachments as $attachment) {
            $termsData[$attachment->folder_id][] = $attachment->attachment_id;
        }

        return $termsData;

    }//end filter_categories_by_term_id()


    public function import_plugin_folders_data()
    {

        global $wpdb;

        $postData = filter_input_array(INPUT_POST);

        $paged         = isset($postData['paged']) && is_numeric($postData['paged']) && $postData['paged'] > 0 ? intval(sanitize_text_field($postData['paged'])) : 1;
        $attachedItems = isset($postData['attached']) && is_numeric($postData['attached']) && $postData['attached'] > 0 ? intval(sanitize_text_field($postData['attached'])) : 0;
        $startFrom     = (10 * ($paged - 1));
        $endFolder     = (10 * $paged);
        $totalFolders  = 0;

        $plugin = isset($postData['plugin']) ? $postData['plugin'] : "";
        $nonce  = isset($postData['nonce']) ? $postData['nonce'] : "";

        $dataSet            = [];
        $response           = [];
        $response['status'] = 0;
        $response['message']        = esc_html__("Invalid request", "folders");
        $response['data']           = [];
        $response['data']['plugin'] = $plugin;
        if (wp_verify_nonce($nonce, "import_data_from_".$plugin)) {
            $this->get_plugin_information();
            $folders      = isset($this->plugins[$plugin]['folders']) ? $this->plugins[$plugin]['folders'] : [];
            $totalFolders = count($folders);
            $attachments  = isset($this->plugins[$plugin]['attachments']) ? $this->plugins[$plugin]['attachments'] : [];

            $categoryByID        = [];
            $foldersImported     = [];
            $attachmentsImported = [];
            if ($plugin != 'filebird' && $plugin != 'real-media-library' && $plugin != "catfolders") {
                $currentFolder = -1;
                foreach ($folders as $folder) {
                    $currentFolder++;
                    $folder_id = $folder->term_id;
                    $parent    = intval($folder->parent);

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
                                $taxonomy = $post_type.'_folder';
                            }
                        }
                    }

                    if ($parent && isset($categoryByID[$parent]['term_id'])) {
                        $parent = intval($categoryByID[$parent]['term_id']);
                    }

                    $new_term = wp_insert_term($folder->name, $taxonomy, ['parent' => $parent]);

                    if (is_wp_error($new_term)) {
                        if (isset($new_term->errors['term_exists']) && $new_term->error_data['term_exists']) {
                            $termId   = $new_term->error_data['term_exists'];
                            $termData = get_term($termId, "", ARRAY_A);
                            if (!empty($termData)) {
                                $new_term = $termData;
                            } else {
                                continue;
                            }
                        } else {
                            continue;
                        }
                    }

                    $arg      = [
                        'taxonomy'              => $taxonomy,
                        'hide_empty'            => false,
                        'parent'                => $parent,
                        'hierarchical'          => false,
                        'update_count_callback' => '_update_generic_term_count',
                    ];
                    $terms    = get_terms($arg);
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

                    $newTermID     = $new_term['term_id'];
                    $folderItems   = 0;
                    $failedItems   = 0;
                    $query         = "SELECT object_id FROM ".$wpdb->term_relationships." WHERE term_taxonomy_id = %d AND object_id != 1";
                    $query         = $wpdb->prepare($query, $folder->term_taxonomy_id);
                    $results       = $wpdb->get_results($query);
                    $found_results = count($results);
                    if ($currentFolder >= $startFrom && $currentFolder < $endFolder) {
                        if (count($results)) {
                            foreach ($results as $result) {
                                $term_set = wp_set_object_terms($result->object_id, $newTermID, $taxonomy, true);

                                if (is_wp_error($term_set)) {
                                    $failedItems++;
                                    continue;
                                }

                                $folderItems++;

                                $attachmentsImported[] = [
                                    'cat_id'   => $newTermID,
                                    'term_ids' => $result->object_id,
                                    'set'      => $term_set,
                                ];
                            }
                        }

                        $dataSet[] = [
                            'id'      => $newTermID,
                            'old_id'  => $folder->term_taxonomy_id,
                            'name'    => $folder->name,
                            'items'   => $folderItems,
                            'failed'  => $failedItems,
                            'results' => $found_results,
                        ];
                    }//end if
                }//end foreach
            } else {
                $attachments   = $this->filter_categories_by_term_id($attachments);
                $currentFolder = -1;
                foreach ($folders as $folder) {
                    $currentFolder++;
                    $parent = intval($folder->parent);
                    if ($parent == -1) {
                        $parent = 0;
                    }

                    $parentID = 0;

                    if ($parent && isset($categoryByID[$parent]['term_id'])) {
                        $parentID = $categoryByID[$parent]['term_id'];
                    }

                    $new_term = wp_insert_term($folder->name, "media_folder", ['parent' => $parentID]);

                    if (is_wp_error($new_term)) {
                        if (isset($new_term->errors['term_exists']) && $new_term->error_data['term_exists']) {
                            $termId   = $new_term->error_data['term_exists'];
                            $termData = get_term($termId, "", ARRAY_A);
                            if (!empty($termData)) {
                                $new_term = $termData;
                            } else {
                                continue;
                            }
                        } else {
                            continue;
                        }
                    }

                    $taxonomy = 'media_folder';

                    $arg      = [
                        'taxonomy'              => $taxonomy,
                        'hide_empty'            => false,
                        'parent'                => $parentID,
                        'hierarchical'          => false,
                        'update_count_callback' => '_update_generic_term_count',
                    ];
                    $terms    = get_terms($arg);
                    $position = count($terms);

                    update_term_meta($new_term['term_id'], 'wcp_custom_order', intval($position));

                    $foldersImported[] = $new_term;

                    $categoryByID[$folder->id] = [
                        'name'    => $folder->name,
                        'parent'  => $parent,
                        'term_id' => $new_term['term_id'],
                    ];

                    $newTermID     = $new_term['term_id'];
                    $folderItems   = 0;
                    $failedItems   = 0;
                    $found_results = 0;
                    if ($currentFolder >= $startFrom && $currentFolder < $endFolder) {
                        if (isset($attachments[$folder->id]) && count($attachments[$folder->id]) > 0) {
                            foreach ($attachments[$folder->id] as $result) {
                                $term_set = wp_set_object_terms($result, $newTermID, $taxonomy, true);

                                if (is_wp_error($term_set)) {
                                    $failedItems++;
                                    continue;
                                }

                                $folderItems++;

                                $attachmentsImported[] = [
                                    'cat_id'   => $newTermID,
                                    'term_ids' => $result->object_id,
                                    'set'      => $term_set,
                                ];
                            }

                            $dataSet[] = [
                                'id'      => $newTermID,
                                'old_id'  => $folder->id,
                                'name'    => $folder->name,
                                'items'   => $folderItems,
                                'failed'  => $failedItems,
                                'results' => $found_results,
                            ];
                        }//end if
                    }//end if
                }//end foreach
            }//end if

            delete_transient("premio_folders_without_trash");

            $totalPages         = ceil($totalFolders / 10);
            $response['status'] = 1;
            $response['data']['imported']    = count($foldersImported);
            $response['data']['attachments'] = (count($attachmentsImported) + $attachedItems);
            $response['data']['data_set']    = $dataSet;
            $response['data']['folders']     = $totalFolders;
            $response['data']['pages']       = $totalPages;
            $response['data']['current']     = $paged;
            $response['data']['plugin']      = $plugin;
            $response['message'] = sprintf(esc_html__("%1\$s folders imported and %2\$s attachments categorized.", 'folders'), count($foldersImported), (count($attachmentsImported) + $attachedItems));
        }//end if

        echo wp_json_encode($response);
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

        $this->plugins    = [
            // FileBird
            'filebird'               => [
                'name'              => 'FileBird (v4)',
                'taxonomy'          => 'filebird',
                // has custom DB table
                'folders'           => [],
                'attachments'       => [],
                'total_folders'     => 0,
                'total_attachments' => 0,
                'is_exists'         => 0,
            ],
            'enhanced-media-library' => [
                // Enhanced Media Library
                'name'              => 'Enhanced Media Library',
                'taxonomy'          => 'media_category',
                'folders'           => [],
                'attachments'       => [],
                'total_folders'     => 0,
                'total_attachments' => 0,
                'is_exists'         => 0,
            ],
            'wicked-folders'         => [
                // Wicked Folders
                'name'              => 'Wicked Folders',
                'taxonomy'          => 'wf_attachment_folders',
                'folders'           => [],
                'attachments'       => [],
                'total_folders'     => 0,
                'total_attachments' => 0,
                'is_exists'         => 0,
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
                'is_exists'         => 0,
            ],
            'wp-media-folder'        => [
                // WP Media Folder
                'name'              => 'WP Media Folder (by JoomUnited)',
                'taxonomy'          => 'wpmf-category',
                'folders'           => [],
                'attachments'       => [],
                'total_folders'     => 0,
                'total_attachments' => 0,
                'is_exists'         => 0,
            ],
            'mediamatic'             => [
                // Mediamatic
                'name'              => 'WordPress Media Library Folders | Mediamatic',
                'taxonomy'          => 'mediamatic_wpfolder',
                'folders'           => [],
                'attachments'       => [],
                'total_folders'     => 0,
                'total_attachments' => 0,
                'is_exists'         => 0,
            ],
            'happyfiles'             => [
                // HappyFiles
                'name'              => 'HappyFiles',
                'taxonomy'          => 'happyfiles_category',
                'folders'           => [],
                'attachments'       => [],
                'total_folders'     => 0,
                'total_attachments' => 0,
                'is_exists'         => 0,
            ],
            'catfolders'             => [
                'name'              => 'CatFolders Lite - WP Media Folders',
                'taxonomy'          => 'catfolders',
                'folders'           => [],
                'attachments'       => [],
                'total_folders'     => 0,
                'total_attachments' => 0,
                'is_exists'         => 0,
            ]
        ];
        $DS      = DIRECTORY_SEPARATOR;
        $dirName = ABSPATH."wp-content{$DS}plugins{$DS}wp-media-library-categories{$DS}";
        if (is_dir($dirName) && class_exists('wpMediaLibraryCategories')) {
            $settings = get_option("wpmlc_settings");
            $category = isset($settings['wpmediacategory_taxonomy'])&&!empty($settings['wpmediacategory_taxonomy'])?$settings['wpmediacategory_taxonomy']:'category';
            $this->plugins['media_library_categories'] = [
                'name'              => 'Media Library Categories',
                'taxonomy'          => esc_sql($category),
                'folders'           => [],
                'attachments'       => [],
                'total_folders'     => 0,
                'total_attachments' => 0,
                'is_exists'         => 0,
            ];
        }


        $post_types       = get_post_types([]);
        $this->post_types = array_keys($post_types);

        foreach ($this->plugins as $slug => $plugin_data) {
            $taxonomy = $plugin_data['taxonomy'];

            if ($slug === 'wicked-folders') {
                // Run for all registered post types
                $folders = [];

                foreach ($this->post_types as $post_type) {
                    $wicked_folders = $this->get_plugin_folders('wf_'.$post_type.'_folders', $slug);

                    if (is_array($wicked_folders)) {
                        $folders = array_merge($folders, $wicked_folders);
                    }
                }
            } else {
                $folders = $this->get_plugin_folders($taxonomy, $slug);
            }

            if (in_array($taxonomy, ['filebird', 'rml','catfolders'])) {
                $folders = is_array($folders) && count($folders) ? $this->map_plugin_folders($taxonomy, $folders) : [];
            }

            $this->plugins[$slug]['folders'] = $folders;

            $attachments = is_array($folders) && count($folders) ? $this->get_plugin_attachments($taxonomy, $folders) : [];

            if (in_array($taxonomy, ['filebird', 'rml','catfolders'])) {
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
                $this->plugins[$key]['is_exists'] = 1;
                $this->is_exists = 1;
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
            } else {
                $taxonomy = "nt_wmc_folder";

                $query   = "SELECT * FROM ".$wpdb->term_taxonomy."
					LEFT JOIN  ".$wpdb->terms."
					ON  ".$wpdb->term_taxonomy.".term_id =  ".$wpdb->terms.".term_id
					WHERE ".$wpdb->term_taxonomy.".taxonomy = '%s'
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
        } else if ($taxonomy === 'catfolders') {
            $filebird_folders_table = $wpdb->prefix.'catfolders';

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
					WHERE ".$wpdb->term_taxonomy.".taxonomy = '%s'
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
            if ($taxonomy === 'filebird' || $taxonomy === 'rml' || $taxonomy === "catfolders") {
                $folderObj = new \stdClass();

                if($taxonomy == "catfolders") {
                    $folderObj->name     = $folder->title;
                } else {
                    $folderObj->name     = $folder->name;
                }
                $folderObj->id       = intval($folder->id);
                $folderObj->parent   = intval($folder->parent);
                $folderObj->position = intval($folder->ord);

                $mapped_folders[] = $folderObj;
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
                $folderObj = new \stdClass();

                $folderObj->folder_id     = intval($folder->folder_id);
                $folderObj->attachment_id = intval($folder->attachment_id);

                $mapped_attachments[] = $folderObj;
            }

            // Real Media Library
            if ($taxonomy === 'rml') {
                $folderObj = new \stdClass();

                $folderObj->folder_id     = intval($folder->fid);
                $folderObj->attachment_id = intval($folder->attachment);

                $mapped_attachments[] = $folderObj;
            }

            if($taxonomy === "catfolders") {
                $folderObj = new \stdClass();

                $folderObj->folder_id     = intval($folder->folder_id);
                $folderObj->attachment_id = intval($folder->post_id);

                $mapped_attachments[] = $folderObj;
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
            $filebirdTable = $wpdb->prefix.'fbv_attachment_folder';

            // Get FileBird attachments
            if ($wpdb->get_var("SHOW TABLES LIKE '{$filebirdTable}'") == $filebirdTable) {
                return $wpdb->get_results("SELECT * FROM {$filebirdTable} ORDER BY folder_id ASC");
            }
        }

        else if ($taxonomy === 'catfolders') {
            $filebirdTable = $wpdb->prefix.'catfolders_posts';

            // Get FileBird attachments
            if ($wpdb->get_var("SHOW TABLES LIKE '{$filebirdTable}'") == $filebirdTable) {
                return $wpdb->get_results("SELECT * FROM {$filebirdTable} ORDER BY folder_id ASC");
            }
        }

        // Real Media Library has its own db table
        else if ($taxonomy === 'rml') {
            $rmlTable = $wpdb->prefix.'realmedialibrary_posts';

            // Get Data from Real Media Library DB Table
            if ($wpdb->get_var("SHOW TABLES LIKE '{$rmlTable}'") == $rmlTable) {
                return $wpdb->get_results("SELECT * FROM {$rmlTable} ORDER BY fid ASC");
            }
        }

        // Default: Plugins with custom taxonomy terms
        else {
            return $wpdb->get_results(
                "SELECT  TR.object_id,
                        TR.term_taxonomy_id
				FROM ".$wpdb->term_relationships." AS TR
				WHERE TR.object_id != 1 && TR.term_taxonomy_id IN (".implode(',', array_column($folders, 'term_taxonomy_id')).")"
            );
        }

    }//end get_plugin_attachments()


}//end class

$WCP_Folder_Plugins = new WCP_Folder_Plugins();
