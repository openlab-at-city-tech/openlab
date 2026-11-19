<?php

namespace Folders\Media;

use Folders\Folders\Actions\FoldersCRUD;

/**
 * Uploads files dropped onto the folder sidebar, including whole folders.
 *
 * When a folder is dragged in from the computer, its directory structure
 * (taken from the browser's `full_path`) is recreated as media folders and
 * each file is placed in the matching folder.
 */
class MediaUploadFile
{

    private static $first_folder = 0;
    private static $newFolders = [];

    /**
     * Upload one file to the Media Library and place it in the folder matching its path.
     *
     * Validates the file type against the allowed MIME types and the maximum
     * upload size. Requires the `upload_files` capability.
     *
     * @param array $params {
     *     Request parameters. The file itself is read from `$_FILES['media_file']`.
     *
     *     @type string $nonce           Nonce for the `folder_nonce_{post_type}` action.
     *     @type string $post_type       Must be "attachment".
     *     @type string $file_id         Client-side ID of the file, echoed back in the response.
     *     @type string $media_file_name File name.
     * }
     * @return array Result array with `success`, `file_id`, `message` and, on success,
     *               the created `attachment`.
     */
    public static function upload_file( $params ) {
        $nonce       = isset( $params['nonce'] ) ? sanitize_text_field( $params['nonce'] ) : '';
        $type        = isset( $params['post_type'] ) ? sanitize_text_field( $params['post_type'] ) : '';
        $file_id     = isset( $params['file_id'] ) ? sanitize_text_field( $params['file_id'] ) : '';

        if (empty($nonce) || empty($type) || empty($file_id)) {
            return array('success' => false, 'file_id' => $file_id, 'message' => esc_html__('Invalid request', 'folders'));
        }

        if ( ! wp_verify_nonce( $nonce, 'folder_nonce_'.$type ) ) {
            return array('success' => false, 'file_id' => $file_id,'message' => esc_html__('Invalid request', 'folders'));
        }

        if (!current_user_can("upload_files")) {
            return array('success' => false,'message' => esc_html__("You’re not allowed to upload files", 'folders'));
        }

        if ($type !== 'attachment') {
            return array('success' => false, 'file_id' => $file_id,'message' => esc_html__('Invalid request', 'folders'));
        }

        $folder_type = \Folders\Folders\Settings::get_folder_post_type($type);

        $file_name = isset( $params['media_file_name'] ) ? sanitize_text_field( $params['media_file_name'] ) : '';

        if(empty($file_name) || !isset($_FILES["media_file"])){
            return array('success' => false, 'file_id' => $file_id, 'message' => esc_html__("You’re not allowed to upload files", 'folders'));
        }

        $file = $_FILES["media_file"];

        if (!isset($file['name']) || !isset($file['full_path']) || !isset($file['type']) || !isset($file['tmp_name']) || !isset($file['error']) || !isset($file['size'])) {
            return array('success' => false, 'file_id' => $file_id, 'message' => esc_html__('Invalid file', 'folders'));
        }

        $wpmime = get_allowed_mime_types();

        $file_name = sanitize_file_name( $file['name'] );

        if ($file_name == '.DS_Store' || $file_name == 'DS_Store' || empty($file_name)) {
            return array('success' => false, 'file_id' => $file_id, 'message' => esc_html__('Invalid file', 'folders'));
        }

        $wp_filetype = wp_check_filetype_and_ext( $file['tmp_name'], $file_name, $wpmime );
        if ( ! $wp_filetype['ext'] || ! $wp_filetype['type'] || ! wp_match_mime_types( $wp_filetype['type'], $wpmime ) ) {
            return array('success' => false, 'file_id' => $file_id, 'message' => esc_html__('This file type is not supported.', 'folders'));
        }

        $max_upload_size = wp_max_upload_size();
        if ( $file['size'] > $max_upload_size ) {
            return array('success' => false, 'file_id' => $file_id, 'message' => esc_html__('File is too large. Max size is', 'folders') . size_format( $max_upload_size ) . '.' );
        }

        $full_path = $file['full_path'];
        
        $folder_name = rtrim($full_path, $file_name);

        $folder_id = self::check_for_folder($folder_name, 0);

        $upload_data = wp_upload_bits($_FILES['media_file']['name'], null, file_get_contents($_FILES['media_file']['tmp_name']));

        if (empty($upload_data)) {
            return array('success' => false, 'file_id' => $file_id, 'message' => esc_html__('Upload error', 'folders'));
        }

        $file_path = $upload_data['file'];
        $file_name = basename($file_path);
        $file_type = wp_check_filetype($file_name, null);
        $attachment_title = sanitize_file_name(pathinfo($file_name, PATHINFO_FILENAME));

        $attachment = [
            'guid' => $upload_data['url'],
            'post_mime_type' => $file_type['type'],
            'post_title' => $attachment_title,
            'post_content' => '',
            'post_status' => 'inherit',
        ];

        $attach_id = wp_insert_attachment($attachment, $file_path, 0);

        if (!$attach_id) {
            return array('success' => false, 'file_id' => $file_id, 'message' => esc_html__('Upload error', 'folders'));
        }

        $attachment['id'] = $attach_id;

        include_once ABSPATH . 'wp-admin/includes/image.php';
        $attach_data = wp_generate_attachment_metadata($attach_id, $file_path);
        wp_update_attachment_metadata($attach_id, $attach_data);

        if (!empty($folder_id)) {
            wp_set_post_terms($attach_id, $folder_id, $folder_type, false);
        }

        return array(
            'success' => true,
            'file_id' => $file_id,
            'message' => esc_html__('File uploaded successfully', 'folders'),
            'attachment' => $attachment,
        );
    }


    /**
     * Find or create the nested media folders for a relative directory path.
     *
     * Each path segment is matched against existing folders under the current
     * parent; missing folders are created. Newly created folders are recorded
     * in {@see $newFolders}.
     *
     * @param string     $folder_name Directory path such as "Photos/2024/".
     * @param int|string $parent_id   ID of the folder to start from; 0 for root.
     * @return int|string ID of the deepest folder, or `$parent_id` when the path is empty.
     */
    private static function check_for_folder($folder_name, $parent_id)
    {
        $folder_type = \Folders\Folders\Settings::get_folder_post_type('attachment');
        $user_id = get_current_user_id();
        $folder_name = explode("/", $folder_name);
        if (!empty($folder_name) && count($folder_name)) {
            foreach ($folder_name as $folder) {
                $folder = sanitize_text_field($folder);
                $term = term_exists($folder, $folder_type, $parent_id);
                if (!empty($term) && isset($term['term_id']) && !empty($term['term_id'])) {
                    if (empty(self::$first_folder)) {
                        self::$first_folder = $term['term_id'];
                    }

                    $term_user = get_term_meta($term['term_id'], "created_by", true);
                    if ($term_user == $user_id) {
                        $parent_id = $term['term_id'];
                    }
                } else {
                    $folder = trim($folder);
                    $slug = FoldersCRUD::create_slug_from_string($folder) . "-" . time() . "-" . $user_id;

                    $result = wp_insert_term(
                        urldecode($folder),
                        // the term
                        $folder_type,
                        // the taxonomy
                        [
                            'parent' => $parent_id,
                            'slug' => $slug,
                        ]
                    );

                    if (!empty($result) && !is_wp_error($result)) {
                        add_term_meta($result['term_id'], "created_by", $user_id);
                        $terms = get_terms(
                            [
                                'taxonomy' => $folder_type,
                                'hide_empty' => false,
                                'parent' => $parent_id,
                                'hierarchical' => false,
                                'update_count_callback' => '_update_generic_term_count',
                            ]
                        );
                        $order = (!empty($terms) && !is_wp_error($terms)) ? (count($terms) + 1) : 1;
                        update_term_meta($result['term_id'], "wcp_custom_order", $order);

                        if (empty(self::$first_folder)) {
                            self::$first_folder = $result['term_id'];
                        }

                        if (!in_array($parent_id, self::$newFolders)) {
                            $term_nonce = wp_create_nonce('wcp_folder_term_' . $result['term_id']);

                            $folder_item = [];
                            $folder_item['parent_id'] = empty($parent) ? "#" : $parent;
                            $folder_item['slug'] = $slug;
                            $folder_item['nonce'] = $term_nonce;
                            $folder_item['term_id'] = $result['term_id'];
                            $folder_item['title'] = $folder;
                            $folder_item['is_sticky'] = 0;
                            $folder_item['is_active'] = 0;
                            $folder_item['is_high'] = 0;
                            $folder_item['is_locked'] = 0;
                            $folder_item['has_color'] = '';
                            self::$newFolders[] = $folder_item;
                        }

                        $parent_id = $result['term_id'];
                    }//end if
                }//end if
            }//end foreach
        }//end if

        return $parent_id;

    }

}