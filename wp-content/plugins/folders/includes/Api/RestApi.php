<?php
namespace Folders\Api;

defined( 'ABSPATH' ) || exit;

/**
 * REST API routes for folder management and settings.
 *
 * Registers the `folders-settings/v1` endpoints for creating, renaming,
 * deleting, locking and importing/exporting folders, plus the settings,
 * notification and support endpoints. Settings routes require
 * `manage_options`; the others require a logged-in user and rely on the
 * delegated handler for nonce and capability checks.
 */
class RestApi {

    /**
     * Register the `rest_api_init` hook.
     */
    public function __construct() {
        add_action( 'rest_api_init', array( $this, 'register_routes' ) );
    }

    /**
     * Register the folder management and settings routes under `folders-settings/v1`.
     *
     * @return void
     */
    public function register_routes() {

        register_rest_route( 'folders-settings/v1', '/save-general-settings', array(
            'methods'             => 'POST',
            'callback'            => array( $this, 'save_general_settings' ),
            'permission_callback' => function () {
                return current_user_can( 'manage_options' );
            },
        ) );

        register_rest_route( 'folders-settings/v1', '/save-folders-customization', array(
            'methods'             => 'POST',
            'callback'            => array( $this, 'save_folder_customization' ),
            'permission_callback' => function () {
                return current_user_can( 'manage_options' );
            },
        ) );

        register_rest_route( 'folders-settings/v1', '/hide-recommended-plugin-page', array(
            'methods'             => 'POST',
            'callback'            => array( $this, 'hide_recommended_plugin_page' ),
            'permission_callback' => function () {
                return current_user_can( 'manage_options' );
            },
        ) );

        $actions = [
            '/add-folder'           => 'add_folder',
            '/rename-folder'        => 'rename_folder',
            '/toggle-sticky-folder' => 'toggle_sticky_folder',
            '/lock-folder'          => 'lock_folder',
            '/default-folder'       => 'default_folder',
            '/add-child-folder'     => 'add_child_folder',
            '/delete-folder'        => 'delete_folder',
            '/delete-multiple-folder' => 'delete_multiple_folder',
            '/add-remove-star'      => 'add_remove_star',
            '/export-folders'       => 'export_folders',
            '/import-folders'       => 'import_folders',
            '/remove-folders-data'  => 'remove_folders_data',
            '/delete-folders-all-data'   => 'delete_folders_all_data',
            '/send-email-notifications'  => 'send_email_notifications',
            '/save-folder-state'     => 'save_folder_state',
            '/send-message'          => 'send_message',
            '/folders-feedback-form' => 'folders_feedback_form',
        ];

        foreach ($actions as $route => $action) {
            register_rest_route( 'folders-settings/v1', $route, array(
                'methods'             => 'POST',
                'callback'            => array( $this, $action ),
                'permission_callback' => function () {
                    return is_user_logged_in();
                },
            ) );
        }
    }

    /**
     * Submit the plugin deactivation feedback form.
     *
     * REST callback for `POST /folders-settings/v1/folders-feedback-form`.
     * Delegates to {@see \Folders\Folders\FoldersHelp::feedback_form()}.
     *
     * @param \WP_REST_Request $request Request object; all request parameters are passed through.
     * @return array|\WP_Error Result of the delegated call.
     */
    public function folders_feedback_form($request)
    {
        $params = $request->get_params();
        return \Folders\Folders\FoldersHelp::feedback_form($params);
    }

    /**
     * Send a message to the support team from the help widget.
     *
     * REST callback for `POST /folders-settings/v1/send-message`.
     * Delegates to {@see \Folders\Folders\FoldersHelp::send_message()}.
     *
     * @param \WP_REST_Request $request Request object; all request parameters are passed through.
     * @return array|\WP_Error Result of the delegated call.
     */
    public function send_message($request)
    {
        $params = $request->get_params();
        return \Folders\Folders\FoldersHelp::send_message($params);
    }

    /**
     * Save the expanded/collapsed state of a folder.
     *
     * REST callback for `POST /folders-settings/v1/save-folder-state`.
     * Delegates to {@see \Folders\Folders\Actions\FoldersState::save_folder_state()}.
     *
     * @param \WP_REST_Request $request Request object; all request parameters are passed through.
     * @return array|\WP_Error Result of the delegated call.
     */
    public function save_folder_state($request)
    {
        $params = $request->get_params();
        return \Folders\Folders\Actions\FoldersState::save_folder_state( $params );
    }

    /**
     * Send a test notification email.
     *
     * REST callback for `POST /folders-settings/v1/send-email-notifications`.
     * Delegates to {@see \Folders\Folders\Notifications::send_test_notifications()}.
     *
     * @param \WP_REST_Request $request Request object; all request parameters are passed through.
     * @return array|\WP_Error Result of the delegated call.
     */
    public function send_email_notifications($request)
    {
        $params = $request->get_params();
        return \Folders\Folders\Notifications::send_test_notifications( $params );
    }

    /**
     * Search users for the notification and permission user pickers.
     *
     * REST callback for `POST /folders-settings/v1/search-users`.
     * Delegates to {@see \Folders\Users\FoldersUsers::search_users()}.
     *
     * @param \WP_REST_Request $request Request object; all request parameters are passed through.
     * @return array|\WP_Error Result of the delegated call.
     */
    public function search_users($request)
    {
        $params = $request->get_params();
        return \Folders\Users\FoldersUsers::search_users( $params );
    }

    /**
     * Delete all folders and folder data.
     *
     * REST callback for `POST /folders-settings/v1/delete-folders-all-data`.
     * Delegates to {@see \Folders\Folders\Actions\FoldersCRUD::delete_all_folder()}.
     *
     * @param \WP_REST_Request $request Request object; all request parameters are passed through.
     * @return array|\WP_Error Result of the delegated call.
     */
    public function delete_folders_all_data($request)
    {
        $params = $request->get_params();
        return \Folders\Folders\Actions\FoldersCRUD::delete_all_folder( $params );
    }

    /**
     * Toggle removing folder data when the plugin is removed.
     *
     * REST callback for `POST /folders-settings/v1/remove-folders-data`.
     * Delegates to {@see \Folders\Admin\Settings::remove_folders_data()}.
     *
     * @param \WP_REST_Request $request Request object; all request parameters are passed through.
     * @return array|\WP_Error Result of the delegated call.
     */
    public function remove_folders_data( $request ) {
        $params = $request->get_params();
        return \Folders\Admin\Settings::remove_folders_data( $params );
    }

    /**
     * Import folders from an export file.
     *
     * REST callback for `POST /folders-settings/v1/import-folders`.
     * Delegates to {@see \Folders\Folders\FoldersImportExport::import_folders_data()}.
     *
     * @param \WP_REST_Request $request Request object; all request parameters are passed through.
     * @return array|\WP_Error Result of the delegated call.
     */
    public function import_folders($request)
    {
        $params = $request->get_params();
        return \Folders\Folders\FoldersImportExport::import_folders_data( $params );
    }

    /**
     * Export folders to a JSON file.
     *
     * REST callback for `POST /folders-settings/v1/export-folders`.
     * Delegates to {@see \Folders\Folders\FoldersImportExport::export_folders_data()}.
     *
     * @param \WP_REST_Request $request Request object; all request parameters are passed through.
     * @return array|\WP_Error Result of the delegated call.
     */
    public function export_folders($request)
    {
        $params = $request->get_params();
        return \Folders\Folders\FoldersImportExport::export_folders_data( $params );
    }

    /**
     * Delete several folders at once.
     *
     * REST callback for `POST /folders-settings/v1/delete-multiple-folder`.
     * Delegates to {@see \Folders\Folders\Actions\FoldersCRUD::delete_multiple_folder()}.
     *
     * @param \WP_REST_Request $request Request object; all request parameters are passed through.
     * @return array|\WP_Error Result of the delegated call.
     */
    public function delete_multiple_folder($request)
    {
        $params = $request->get_params();
        return \Folders\Folders\Actions\FoldersCRUD::delete_multiple_folder( $params );
    }

    /**
     * Delete a folder.
     *
     * REST callback for `POST /folders-settings/v1/delete-folder`.
     * Delegates to {@see \Folders\Folders\Actions\FoldersCRUD::delete_folder()}.
     *
     * @param \WP_REST_Request $request Request object; all request parameters are passed through.
     * @return array|\WP_Error Result of the delegated call.
     */
    public function delete_folder($request)
    {
        $params = $request->get_params();
        return \Folders\Folders\Actions\FoldersCRUD::delete_folder( $params );
    }

    /**
     * Create a subfolder under an existing folder.
     *
     * REST callback for `POST /folders-settings/v1/add-child-folder`.
     * Delegates to {@see \Folders\Folders\Actions\FoldersCRUD::add_child_folder()}.
     *
     * @param \WP_REST_Request $request Request object; all request parameters are passed through.
     * @return array|\WP_Error Result of the delegated call.
     */
    public function add_child_folder($request)
    {
        $params = $request->get_params();
        return \Folders\Folders\Actions\FoldersCRUD::add_child_folder( $params );
    }

    /**
     * Duplicate a folder.
     *
     * REST callback for `POST /folders-settings/v1/duplicate-folder`.
     * Delegates to {@see \Folders\Folders\Actions\DuplicateFolder::duplicate_folder()}.
     *
     * @param \WP_REST_Request $request Request object; all request parameters are passed through.
     * @return array|\WP_Error Result of the delegated call.
     */
    public function duplicate_folder($request)
    {
        $params = $request->get_params();
        return \Folders\Folders\Actions\DuplicateFolder::duplicate_folder( $params );
    }

    /**
     * Set or clear a folder as the default folder for a post type.
     *
     * REST callback for `POST /folders-settings/v1/default-folder`.
     * Delegates to {@see \Folders\Folders\Actions\FoldersCRUD::default_folder()}.
     *
     * @param \WP_REST_Request $request Request object; all request parameters are passed through.
     * @return array|\WP_Error Result of the delegated call.
     */
    public function default_folder($request)
    {
        $params = $request->get_params();
        return \Folders\Folders\Actions\FoldersCRUD::default_folder( $params );
    }

    /**
     * Add or remove a folder from favorites.
     *
     * REST callback for `POST /folders-settings/v1/add-remove-star`.
     * Delegates to {@see \Folders\Folders\Actions\FoldersCRUD::add_remove_star()}.
     *
     * @param \WP_REST_Request $request Request object; all request parameters are passed through.
     * @return array|\WP_Error Result of the delegated call.
     */
    public function add_remove_star($request)
    {
        $params = $request->get_params();
        return \Folders\Folders\Actions\FoldersCRUD::add_remove_star( $params );
    }

    /**
     * Lock or unlock a folder.
     *
     * REST callback for `POST /folders-settings/v1/lock-folder`.
     * Delegates to {@see \Folders\Folders\Actions\FoldersCRUD::lock_folder()}.
     *
     * @param \WP_REST_Request $request Request object; all request parameters are passed through.
     * @return array|\WP_Error Result of the delegated call.
     */
    public function lock_folder($request)
    {
        $params = $request->get_params();
        return \Folders\Folders\Actions\FoldersCRUD::lock_folder( $params );
    }

    /**
     * Pin or unpin a folder (sticky folder).
     *
     * REST callback for `POST /folders-settings/v1/toggle-sticky-folder`.
     * Delegates to {@see \Folders\Folders\Actions\FoldersCRUD::toggle_sticky_folder()}.
     *
     * @param \WP_REST_Request $request Request object; all request parameters are passed through.
     * @return array|\WP_Error Result of the delegated call.
     */
    public function toggle_sticky_folder($request)
    {
        $params = $request->get_params();
        return \Folders\Folders\Actions\FoldersCRUD::toggle_sticky_folder( $params );
    }

    /**
     * Rename a folder.
     *
     * REST callback for `POST /folders-settings/v1/rename-folder`.
     * Delegates to {@see \Folders\Folders\Actions\FoldersCRUD::rename_folder()}.
     *
     * @param \WP_REST_Request $request Request object; all request parameters are passed through.
     * @return array|\WP_Error Result of the delegated call.
     */
    public function rename_folder($request)
    {
        $params = $request->get_params();
        return \Folders\Folders\Actions\FoldersCRUD::rename_folder( $params );
    }

    /**
     * Create a new folder.
     *
     * REST callback for `POST /folders-settings/v1/add-folder`.
     * Delegates to {@see \Folders\Folders\Actions\FoldersCRUD::add_folder()}.
     *
     * @param \WP_REST_Request $request Request object; all request parameters are passed through.
     * @return array|\WP_Error Result of the delegated call.
     */
    public function add_folder($request)
    {
        $params = $request->get_params();
        return \Folders\Folders\Actions\FoldersCRUD::add_folder( $params );
    }

    /**
     * Hide the Recommended Plugins admin page.
     *
     * REST callback for `POST /folders-settings/v1/hide-recommended-plugin-page`.
     * Delegates to {@see \Folders\Admin\Settings::hide_recommended_plugin_page()}.
     *
     * @param \WP_REST_Request $request Request object; all request parameters are passed through.
     * @return array|\WP_Error Result of the delegated call.
     */
    public function hide_recommended_plugin_page( $request ) {
        $params = $request->get_params();
        return \Folders\Admin\Settings::hide_recommended_plugin_page( $params );
    }

    /**
     * Save the email notification settings.
     *
     * REST callback for (no route is registered for this callback).
     * Delegates to {@see \Folders\Admin\Settings::save_notifications()}.
     *
     * @param \WP_REST_Request $request Request object; all request parameters are passed through.
     * @return array|\WP_Error Result of the delegated call.
     */
    public function save_notifications( $request ) {
        $params = $request->get_params();
        return \Folders\Admin\Settings::save_notifications( $params );
    }

    /**
     * Save the general settings tab.
     *
     * REST callback for `POST /folders-settings/v1/save-general-settings`.
     * Delegates to {@see \Folders\Admin\Settings::save_general_settings()}.
     *
     * @param \WP_REST_Request $request Request object; all request parameters are passed through.
     * @return array|\WP_Error Result of the delegated call.
     */
    public function save_general_settings( $request ) {
        $params = $request->get_params();
        return \Folders\Admin\Settings::save_general_settings( $params );
    }

    /**
     * Save the folder customization settings.
     *
     * REST callback for `POST /folders-settings/v1/save-folders-customization`.
     * Delegates to {@see \Folders\Admin\Settings::save_folders_customization()}.
     *
     * @param \WP_REST_Request $request Request object; all request parameters are passed through.
     * @return array|\WP_Error Result of the delegated call.
     */
    public function save_folder_customization( $request ) {
        $params = $request->get_params();
        return \Folders\Admin\Settings::save_folders_customization( $params );
    }
}
