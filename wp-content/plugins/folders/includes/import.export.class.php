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

/**
 * Class Folders_Import_Export
 *
 * This class is responsible for managing import/export related to folders.
 */
class Folders_Import_Export
{
    /**
     * Class constructor.
     *
     * This method is called when an instance of the class is created. It is used to initialize the object.
     *
     * @return void
     */

    public $default_settings = null;


    public function __construct() {

        //Export folders
        add_action('wp_ajax_folders_export', [$this, 'folders_export']);
        add_action('wp_ajax_folders_import', [$this, 'folders_import']);

    }

    /**
     * Export folders data
     *
     * @since  1.0.0
     * @access public
     * @return $response
     */
    public function folders_export()
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
            $nonce = WCP_Folders::sanitize_options($postData['nonce']);
            if (!wp_verify_nonce($nonce, 'wcp_folders_export')) {
                $response['message'] = esc_html__("Your request is not valid", 'folders');
                $errorCounter++;
            }
        }

        if ($errorCounter == 0) {
            $filename = 'export_form' . time() . '.csv';
            $data_rows = array();
            $header_row = array(
                'id',
                'Post type',
                'Title',
                'parent',
                'children'
            );

            $post_setting = apply_filters("check_for_folders_post_args", ["show_in_menu" => 1]);
            $posts =[];
            $post_types = get_post_types( $post_setting, 'objects' );
            foreach($post_types as $post =>$value) {
                $folder_type = WCP_Folders::get_custom_post_type($post);
                $post_folders = self::get_terms_hierarchical_download($folder_type);
                if(in_array($post, ['page', 'post', 'media']) || count($post_folders) > 0) {
                    $posts[] = [
                        'post_type' => $post,
                        'post_title' => $value->label,
                        'folders' => $post_folders
                    ];
                }
            }
            $response['data'] = $posts;
        }
        echo wp_json_encode($response);
        die;

    }//end folders_export()

    /**
     * Get folders by hierarchy
     *
     * @since  1.0.0
     * @access public
     */
    public static function get_terms_hierarchical_download($taxonomy)
    {
        $customize_folders   = get_option('customize_folders');
        $foldersByUser = isset($customize_folders['folders_by_users']) && $customize_folders['folders_by_users'] == "on" ? true : false;
        $folder_by_user = 0;
        if ($foldersByUser) {
            $user_id        = get_current_user_id();
            $folder_by_user = $user_id;
            if (function_exists("wp_get_current_user")) {
                $user       = wp_get_current_user();
                $user_roles = (array) $user->roles;
                $user_roles = !is_array($user_roles) ? [] : $user_roles;
                if (in_array("administrator", $user_roles)) {
                    $folder_by_user = 0;
                }
            }
        }

        $args = [
            'taxonomy'              => $taxonomy,
            'hide_empty'            => false,
            'parent'                => 0,
            'orderby'               => 'meta_value_num',
            'order'                 => 'ASC',
            'hierarchical'          => false,
            'update_count_callback' => '_update_generic_term_count',
        ];

        if ($folder_by_user) {
            $args['meta_query'] = [
                [
                    'key'  => 'wcp_custom_order',
                    'type' => 'NUMERIC',
                ],
                [
                    'key'   => 'created_by',
                    'type'  => '=',
                    'value' => $folder_by_user,
                ],
            ];
        } else {
            $args['meta_query'] = [
                [
                    'key'  => 'wcp_custom_order',
                    'type' => 'NUMERIC',
                ],
            ];
        }

        $terms = get_terms($args);
        $hierarchical_terms = [];
        if (!empty($terms)) {
            foreach ($terms as $term) {
                if (!empty($term) && isset($term->term_id)) {
                    $folder_info       = get_term_meta($term->term_id, "folder_info", true);
                    $folder_info = shortcode_atts([
                        'is_sticky' => 0,
                        'is_high'   => 0,
                        'is_locked' => 0,
                        'is_active' => 0,
                        'has_color' => ''
                    ], $folder_info);

                    $main_term = ['name' => $term->name,'properties' => $folder_info , 'children' => []];
                    $main_term['children']   = self::get_child_terms_download($taxonomy,$main_term['children'], $term->term_id, "-", $folder_by_user);
                    array_push($hierarchical_terms,$main_term);
                }
            }
        }
        return $hierarchical_terms;

    }//end get_terms_hierarchical()

    /**
     * Get child folders
     *
     * @since  1.0.0
     * @access public
     */
    public static function get_child_terms_download($taxonomy,$main_term, $term_id, $separator="-", $folder_by_user=0)
    {
        $args = [
            'taxonomy'              => $taxonomy,
            'hide_empty'            => false,
            'parent'                => $term_id,
            'orderby'               => 'meta_value_num',
            'order'                 => 'ASC',
            'hierarchical'          => false,
            'update_count_callback' => '_update_generic_term_count',
        ];
        if ($folder_by_user) {
            $args['meta_query'] = [
                [
                    'key'  => 'wcp_custom_order',
                    'type' => 'NUMERIC',
                ],
                [
                    'key'   => 'created_by',
                    'type'  => '=',
                    'value' => $folder_by_user,
                ],
            ];
        } else {
            $args['meta_query'] = [
                [
                    'key'  => 'wcp_custom_order',
                    'type' => 'NUMERIC',
                ],
            ];
        }


        $terms = get_terms($args);
        $hierarchical_terms_1 = [];
        if (!empty($terms)) {
            foreach ($terms as $term) {
                if (isset($term->name)) {
                    $main_term['name'] = $term->name;
                    $folder_info       = get_term_meta($term->term_id, "folder_info", true);
                    $folder_info = shortcode_atts([
                        'is_sticky' => 0,
                        'is_high'   => 0,
                        'is_locked' => 0,
                        'is_active' => 0,
                        'has_color' => ''
                    ], $folder_info);
                    $main_term['properties'] = $folder_info;
                    $main_term['children'] = [];
                    $main_term['children'] = self::get_child_terms_download($taxonomy,$main_term['children'],$term->term_id, $separator."-");
                    array_push($hierarchical_terms_1,$main_term);
                }
            }
        }

        return $hierarchical_terms_1;

    }//end get_child_terms()

    /**
     * Import folders data
     *
     * @since  1.0.0
     * @access public
     * @return $response
     */
    public function folders_import()
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
            $nonce = WCP_Folders::sanitize_options($postData['nonce']);
            if (!wp_verify_nonce($nonce, 'wcp_folders_import')) {
                $response['message'] = esc_html__("Your request is not valid", 'folders');
                $errorCounter++;
            }
        }

        $success = true;
        if ($errorCounter == 0) {
            $fileContent = WCP_Folders::sanitize_options($postData['uploaded_data']);
            $fileContent = json_decode($fileContent,true);
            foreach($fileContent as $content) {
                $post_type = $content['post_type'];
                $folders = $content['folders'];
                $folder_type = WCP_Folders::get_custom_post_type($post_type);
                $success = self::insert_imported_folders($folders,$folder_type);
            }
        }
        if($success) {
            $response['status']  = 1;
            $response['error']   = 0;
            $response['message'] = "Data imported successfully";
        }
        echo wp_json_encode($response);
        die;
    }//end folders_import()

    public function insert_imported_folders($folders,$folder_type) {
        $success = true;
        foreach($folders as $folder) {
            $folder_name = trim($folder['name']);

            $term_id = term_exists($folder_name, $folder_type, 0);
            if(empty($term_id)) {
                $slug = WCP_Folders::create_slug_from_string($folder_name) . "-" . time();
                $result = wp_insert_term(
                    $folder_name,
                    $folder_type,
                    [
                        'parent' => 0,
                        'slug' => $slug,
                    ]
                );
                if(!is_wp_error($result)) {
                    if(isset($result['term_id']) && isset($folder['properties']) && is_array($folder['properties']) && !empty($folder['properties'])) {
                        add_term_meta($result['term_id'], "folder_info", $folder['properties']);
                    }
                    if(isset($folder['children']) && !empty($folder['children'])) {
                        $success = self::insert_imported_folders_child($folder['children'],$folder_type,0);
                    }
                }
            } else {
                if($term_id && isset($folder['properties']) && is_array($folder['properties']) && !empty($folder['properties'])) {
                    add_term_meta($term_id, "folder_info", $folder['properties']);
                }
                if(isset($folder['children']) && !empty($folder['children'])) {
                    $success = self::insert_imported_folders_child($folder['children'],$folder_type,0);
                }
            }
        }
        return 1;
    }

    public function insert_imported_folders_child($folders,$folder_type,$parent_id) {
        foreach($folders as $folder) {
            $folder_name = trim($folder['name']);
            $term_id = term_exists($folder_name, $folder_type, 0);
            if(empty($term_id)) {
                $slug = WCP_Folders::create_slug_from_string($folder_name) . "-" . time();
                $result = wp_insert_term(
                    $folder_name,
                    $folder_type,
                    [
                        'parent' => $parent_id,
                        'slug' => $slug,
                    ]
                );
                if(!is_wp_error($result)) {
                    if(isset($result['term_id']) && isset($folder['properties']) && is_array($folder['properties']) && !empty($folder['properties'])) {
                        add_term_meta($result['term_id'], "folder_info", $folder['properties']);
                    }
                    if(isset($folder['children']) && !empty($folder['children'])) {
                        $success = self::insert_imported_folders_child($folder['children'],$folder_type,0);
                    }
                }
            } else {
                if($term_id && isset($folder['properties']) && is_array($folder['properties']) && !empty($folder['properties'])) {
                    add_term_meta($result['term_id'], "folder_info", $folder['properties']);
                }
                if(isset($folder['children']) && !empty($folder['children'])) {
                    $success = self::insert_imported_folders_child($folder['children'],$folder_type,0);
                }
            }
        }
        return 1;
    }

}
if(class_exists("Folders_Import_Export")) {
    $Folders_Import_Export = new Folders_Import_Export();
}
