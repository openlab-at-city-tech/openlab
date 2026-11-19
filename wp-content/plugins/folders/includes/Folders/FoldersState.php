<?php
namespace Folders\Folders;

defined( 'ABSPATH' ) || exit;

/**
 * Folder sidebar visibility and width per post type.
 *
 * Stores whether the sidebar is shown or hidden and its width in the
 * `wcp_dynamic_display_status_{post_type}` and `wcp_dynamic_width_for_{post_type}`
 * options, and adds a body class when it is hidden.
 */
class FoldersState {

    /**
     * Register the `admin_body_class` filter.
     */
    public function __construct() {
        add_filter('admin_body_class', array( $this, 'admin_body_class' ));
    }

    /**
     * Add the `folders-hidden` body class when the sidebar is hidden for the
     * current post type.
     *
     * @param string $body_class Space-separated admin body classes.
     * @return string Modified body classes.
     */
    public function admin_body_class($body_class) {
        global $typenow;
        if(!empty($typenow)) {
            $option = "wcp_dynamic_display_status_" . $typenow;
            $status = get_option($option);
            if($status == 'hide') {
                $body_class .= ' folders-hidden ';
            }
        }
        return $body_class;
    }

    /**
     * Save whether the folder sidebar is shown or hidden for a post type.
     *
     * @param array $params Request parameters with `nonce` (for `folder_nonce_{post_type}`),
     *                      `post_type` and `status` ("show" or "hide").
     * @return array|\WP_Error Success array, or WP_Error on invalid request.
     */
    public static function folders_state($params)
    {
        $nonce = isset( $params['nonce'] ) ? sanitize_text_field( $params['nonce'] ) : '';
        $post_type = isset( $params['post_type'] ) ? sanitize_text_field( $params['post_type'] ) : '';

        if(empty($post_type) || empty($nonce) || !wp_verify_nonce($nonce, 'folder_nonce_'.$post_type)) {
            return new \WP_Error( 'error', esc_html__('Invalid request', 'folders'), array( 'status' => 403 ) );
        }
        if (!\Folders\Folders\Settings::current_user_can_use_folders($post_type)) {
            return new \WP_Error( 'error', esc_html__('Invalid request', 'folders'), array( 'status' => 403 ) );
        }

        $status = isset( $params['status'] ) ? sanitize_text_field( $params['status'] ) : '';

        $status = ($status == 'show') ? 'show' : 'hide';

        $option = "wcp_dynamic_display_status_" . $post_type;
        update_option($option, $status) or add_option($option, $status);

        return array(
            'success' => true
        );
    }

    /**
     * Save the folder sidebar width and visibility for a post type.
     *
     * @param array $params Request parameters with `nonce` (for `folder_nonce_{post_type}`),
     *                      `post_type`, `menu_width` (pixels) and `status` ("show" or "hide").
     * @return array|\WP_Error Success array, or WP_Error on invalid request.
     */
    public static function folders_sidebar_size($params)
    {
        $nonce = isset( $params['nonce'] ) ? sanitize_text_field( $params['nonce'] ) : '';
        $post_type = isset( $params['post_type'] ) ? sanitize_text_field( $params['post_type'] ) : '';
        $menu_width = isset( $params['menu_width'] ) ? intval(sanitize_text_field( $params['menu_width'] )) : '';

        if(empty($post_type) || empty($nonce) || empty($menu_width) || !is_numeric($menu_width) || !wp_verify_nonce($nonce, 'folder_nonce_'.$post_type)) {
            return new \WP_Error( 'error', esc_html__('Invalid request', 'folders'), array( 'status' => 403 ) );
        }
        if (!\Folders\Folders\Settings::current_user_can_use_folders($post_type)) {
            return new \WP_Error( 'error', esc_html__('Invalid request', 'folders'), array( 'status' => 403 ) );
        }

        $status = isset( $params['status'] ) ? sanitize_text_field( $params['status'] ) : '';

        $status = ($status == 'show') ? 'show' : 'hide';

        $option = "wcp_dynamic_display_status_" . $post_type;
        update_option($option, $status) or add_option($option, $status);

        $option = "wcp_dynamic_width_for_" . $post_type;
        update_option($option, $menu_width) or add_option($option, $menu_width);

        return array(
            'success' => true
        );
    }
}
