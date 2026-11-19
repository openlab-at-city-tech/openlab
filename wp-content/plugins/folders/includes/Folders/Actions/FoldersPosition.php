<?php
namespace Folders\Folders\Actions;

defined( 'ABSPATH' ) || exit;

/**
 * Saves folder hierarchy and ordering after drag and drop in the sidebar.
 */
class FoldersPosition {

    /**
     * Move a folder to a new parent and save the order of its siblings.
     *
     * @param array $params {
     *     Request parameters.
     *
     *     @type string     $nonce     Nonce for the `folder_nonce_{folder_id}` action.
     *     @type int|string $folder_id ID of the moved folder.
     *     @type int|string $parent_id New parent ID; "#" or 0 for root.
     *     @type string     $post_type Post type the folder belongs to.
     *     @type array      $siblings  Folder IDs at the new level, in display order.
     * }
     * @return array|\WP_Error Success array, or WP_Error on invalid request.
     */
    public static function save_folder_position($params)
    {
        $nonce = isset( $params['nonce'] ) ? sanitize_text_field( $params['nonce'] ) : '';
        $folder_id = isset( $params['folder_id'] ) ? sanitize_text_field( $params['folder_id'] ) : '';
        $parent_id = isset( $params['parent_id'] ) ? sanitize_text_field( $params['parent_id'] ) : '';
        $post_type = isset( $params['post_type'] ) ? sanitize_text_field( $params['post_type'] ) : '';
        $siblings  = isset( $params['siblings'] ) ? map_deep( $params['siblings'], 'sanitize_text_field' ) : [];

        if(empty($folder_id) || empty($post_type) || empty($nonce) || !wp_verify_nonce($nonce, 'folder_nonce_' . $folder_id)) {
            return new \WP_Error( 'error', esc_html__('Invalid request', 'folders'), array( 'status' => 403 ) );
        }
        if (!\Folders\Folders\Settings::current_user_can_manage_folders()) {
            return new \WP_Error( 'error', esc_html__('Invalid request', 'folders'), array( 'status' => 403 ) );
        }

        $folder_type = \Folders\Folders\Settings::get_folder_post_type($post_type);

        if($parent_id == '#') {
            $parent_id = 0;
        }

        wp_update_term(
            $folder_id,
            $folder_type,
            ['parent' => $parent_id]
        );

        foreach ($siblings as $key=>$sibling) {
            if(is_numeric($sibling)) {
                update_term_meta(intval($sibling), 'wcp_custom_order', $key);
            }
        }

        return array(
            'success'       => true,
            'message'       => esc_html__('Folders updated successfully', 'folders'),
            'folder_id'     => $folder_id
        );
    }
}
