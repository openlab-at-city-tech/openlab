<?php
namespace Folders\Api;

defined( 'ABSPATH' ) || exit;

/**
 * REST API routes for folder operations.
 *
 * Registers the `folders-settings/v1` endpoints used by the folder sidebar
 * (moving items, sorting, colors, copying, downloads, uploads and so on).
 * Every route is POST-only and requires a logged-in user; each callback
 * passes the request parameters to the class that does the work, which
 * checks the nonce and capabilities.
 */
class FoldersRestApi {

    /**
     * Register the `rest_api_init` hook.
     */
    public function __construct() {
        add_action( 'rest_api_init', array( $this, 'register_routes' ) );
    }

    /**
     * Register the folder operation routes under `folders-settings/v1`.
     *
     * @return void
     */
    public function register_routes() {
        $actions = [
            '/save-folder-position' => 'save_folder_position',
            '/save-folder-items'    => 'save_folder_items',
            '/get-folder-items'     => 'get_folder_items',
            '/save-last-folder-state'    => 'save_last_folder_state',
            '/save-dynamic-folder-state' => 'save_dynamic_folder_state',
            '/check-for-other-folders'   => 'check_for_other_folders',
            '/remove-post-from-folders'  => 'remove_post_from_folders',
            '/sort-folder-data'     => 'sort_folder_data',
            '/copy-folder'           => 'copy_folder',
            '/update-folder-color'   => 'update_folder_color',
            '/import-plugins-data'   => 'import_plugins_data',
            '/delete-plugins-data'   => 'delete_plugins_data',
            '/lock-unlock-all-folders' => 'lock_unlock_all_folders',
            '/folders-state' => 'folders_state',
            '/folders-sidebar-size' => 'folders_sidebar_size',
            '/undo-folders-changes' => 'undo_folders_changes',
            '/bulk-folder-action' => 'bulk_folder_action',
            '/upload-folder-file' => 'upload_folder_file',
            '/download-folder' => 'download_folder',
            '/download-media-items' => 'download_media_items',
        ];

        // Dynamic folders are only available when their handler class is part of the plugin.
        if ( ! class_exists( '\Folders\Folders\DynamicFolders' ) ) {
            unset( $actions['/save-dynamic-folder-state'] );
        }

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
     * Download the selected media items as a ZIP archive.
     *
     * REST callback for `POST /folders-settings/v1/download-media-items`.
     * Delegates to {@see \Folders\Media\MediaDownload::download_media_items()}.
     *
     * @param \WP_REST_Request $request Request object; all request parameters are passed through.
     * @return array|\WP_Error Result of the delegated call.
     */
    public function download_media_items($request)
    {
        $params = $request->get_params();
        return \Folders\Media\MediaDownload::download_media_items( $params );
    }

    /**
     * Download all media in a folder as a ZIP archive.
     *
     * REST callback for `POST /folders-settings/v1/download-folder`.
     * Delegates to {@see \Folders\Media\DownloadFolder::download_folder()}.
     *
     * @param \WP_REST_Request $request Request object; all request parameters are passed through.
     * @return array|\WP_Error Result of the delegated call.
     */
    public function download_folder($request)
    {
        $params = $request->get_params();
        return \Folders\Media\DownloadFolder::download_folder( $params );
    }

    /**
     * Upload a file directly into a folder.
     *
     * REST callback for `POST /folders-settings/v1/upload-folder-file`.
     * Delegates to {@see \Folders\Media\MediaUploadFile::upload_file()}.
     *
     * @param \WP_REST_Request $request Request object; all request parameters are passed through.
     * @return array|\WP_Error Result of the delegated call.
     */
    public function upload_folder_file($request)
    {
        $params = $request->get_params();
        return \Folders\Media\MediaUploadFile::upload_file( $params );
    }

    /**
     * Undo the last change made to folder contents.
     *
     * REST callback for `POST /folders-settings/v1/undo-folders-changes`.
     * Delegates to {@see \Folders\Folders\Actions\FoldersItems::undo_folders_changes()}.
     *
     * @param \WP_REST_Request $request Request object; all request parameters are passed through.
     * @return array|\WP_Error Result of the delegated call.
     */
    public function undo_folders_changes($request)
    {
        $params = $request->get_params();
        return \Folders\Folders\Actions\FoldersItems::undo_folders_changes($params);
    }

    /**
     * Save the folder sidebar width and visibility for a post type.
     *
     * REST callback for `POST /folders-settings/v1/folders-sidebar-size`.
     * Delegates to {@see \Folders\Folders\FoldersState::folders_sidebar_size()}.
     *
     * @param \WP_REST_Request $request Request object; all request parameters are passed through.
     * @return array|\WP_Error Result of the delegated call.
     */
    public function folders_sidebar_size($request)
    {
        $params = $request->get_params();
        return \Folders\Folders\FoldersState::folders_sidebar_size($params);
    }

    /**
     * Save whether the folder sidebar is shown or hidden for a post type.
     *
     * REST callback for `POST /folders-settings/v1/folders-state`.
     * Delegates to {@see \Folders\Folders\FoldersState::folders_state()}.
     *
     * @param \WP_REST_Request $request Request object; all request parameters are passed through.
     * @return array|\WP_Error Result of the delegated call.
     */
    public function folders_state($request)
    {
        $params = $request->get_params();
        return \Folders\Folders\FoldersState::folders_state($params);
    }

    /**
     * Lock or unlock all folders at once.
     *
     * REST callback for `POST /folders-settings/v1/lock-unlock-all-folders`.
     * Delegates to {@see \Folders\Folders\Actions\FoldersCRUD::lock_unlock_all_folders()}.
     *
     * @param \WP_REST_Request $request Request object; all request parameters are passed through.
     * @return array|\WP_Error Result of the delegated call.
     */
    public function lock_unlock_all_folders($request)
    {
        $params = $request->get_params();
        return \Folders\Folders\Actions\FoldersCRUD::lock_unlock_all_folders($params);
    }

    /**
     * Delete folder data created by another folder plugin.
     *
     * REST callback for `POST /folders-settings/v1/delete-plugins-data`.
     * Delegates to {@see \Folders\Folders\FoldersImport::remove_plugin_folders_data()}.
     *
     * @param \WP_REST_Request $request Request object; all request parameters are passed through.
     * @return array|\WP_Error Result of the delegated call.
     */
    public function delete_plugins_data($request)
    {
        $params = $request->get_params();
        return \Folders\Folders\FoldersImport::remove_plugin_folders_data($params);
    }

    /**
     * Import folders from another folder plugin.
     *
     * REST callback for `POST /folders-settings/v1/import-plugins-data`.
     * Delegates to {@see \Folders\Folders\FoldersImport::import_plugin_folders_data()}.
     *
     * @param \WP_REST_Request $request Request object; all request parameters are passed through.
     * @return array|\WP_Error Result of the delegated call.
     */
    public function import_plugins_data($request)
    {
        $params = $request->get_params();
        return \Folders\Folders\FoldersImport::import_plugin_folders_data($params);
    }

    /**
     * Copy a folder and its items.
     *
     * REST callback for `POST /folders-settings/v1/copy-folder`.
     * Delegates to {@see \Folders\Folders\Actions\CopyFolder::copy_folder()}.
     *
     * @param \WP_REST_Request $request Request object; all request parameters are passed through.
     * @return array|\WP_Error Result of the delegated call.
     */
    public function copy_folder($request)
    {
        $params = $request->get_params();
        return \Folders\Folders\Actions\CopyFolder::copy_folder( $params );
    }

    /**
     * Change a folder's color.
     *
     * REST callback for `POST /folders-settings/v1/update-folder-color`.
     * Delegates to {@see \Folders\Folders\Actions\FoldersCRUD::update_folder_color()}.
     *
     * @param \WP_REST_Request $request Request object; all request parameters are passed through.
     * @return array|\WP_Error Result of the delegated call.
     */
    public function update_folder_color($request)
    {
        $params = $request->get_params();
        return \Folders\Folders\Actions\FoldersCRUD::update_folder_color( $params );
    }

    /**
     * Sort the folder list (A-Z, Z-A, newest or oldest) and save the choice.
     *
     * REST callback for `POST /folders-settings/v1/sort-folder-data`.
     * Delegates to {@see \Folders\Folders\Actions\FoldersItems::sort_folder_data()}.
     *
     * @param \WP_REST_Request $request Request object; all request parameters are passed through.
     * @return array|\WP_Error Result of the delegated call.
     */
    public function sort_folder_data($request)
    {
        $params = $request->get_params();
        return \Folders\Folders\Actions\FoldersItems::sort_folder_data( $params );
    }

    /**
     * Remove items from the current folder or from all folders.
     *
     * REST callback for `POST /folders-settings/v1/remove-post-from-folders`.
     * Delegates to {@see \Folders\Folders\Actions\FoldersItems::remove_post_from_folders()}.
     *
     * @param \WP_REST_Request $request Request object; all request parameters are passed through.
     * @return array|\WP_Error Result of the delegated call.
     */
    public function remove_post_from_folders($request)
    {
        $params = $request->get_params();
        return \Folders\Folders\Actions\FoldersItems::remove_post_from_folders( $params );
    }

    /**
     * Remove items from a folder after checking whether they also belong to other folders.
     *
     * REST callback for `POST /folders-settings/v1/check-for-other-folders`.
     * Delegates to {@see \Folders\Folders\Actions\FoldersItems::check_for_other_folders()}.
     *
     * @param \WP_REST_Request $request Request object; all request parameters are passed through.
     * @return array|\WP_Error Result of the delegated call.
     */
    public function check_for_other_folders($request)
    {
        $params = $request->get_params();
        return \Folders\Folders\Actions\FoldersItems::check_for_other_folders( $params );
    }

    /**
     * Save the expanded/collapsed state of dynamic folders.
     *
     * REST callback for `POST /folders-settings/v1/save-dynamic-folder-state`.
     * Delegates to {@see \Folders\Folders\DynamicFolders::save_dynamic_folder_state()}.
     *
     * @param \WP_REST_Request $request Request object; all request parameters are passed through.
     * @return array|\WP_Error Result of the delegated call.
     */
    public function save_dynamic_folder_state($request)
    {
        $params = $request->get_params();
        return \Folders\Folders\DynamicFolders::save_dynamic_folder_state( $params );
    }

    /**
     * Remember the last opened folder for a post type.
     *
     * REST callback for `POST /folders-settings/v1/save-last-folder-state`.
     * Delegates to {@see \Folders\Folders\Actions\FoldersItems::save_last_folder_state()}.
     *
     * @param \WP_REST_Request $request Request object; all request parameters are passed through.
     * @return array|\WP_Error Result of the delegated call.
     */
    public function save_last_folder_state($request)
    {
        $params = $request->get_params();
        return \Folders\Folders\Actions\FoldersItems::save_last_folder_state( $params );
    }

    /**
     * Get the folder list, folder tree and item counts for a post type.
     *
     * REST callback for `POST /folders-settings/v1/get-folder-items`.
     * Delegates to {@see \Folders\Folders\Actions\FoldersItems::get_folder_items()}.
     *
     * @param \WP_REST_Request $request Request object; all request parameters are passed through.
     * @return array|\WP_Error Result of the delegated call.
     */
    public function get_folder_items($request)
    {
        $params = $request->get_params();
        return \Folders\Folders\Actions\FoldersItems::get_folder_items( $params );
    }

    /**
     * Assign posts to a folder.
     *
     * REST callback for `POST /folders-settings/v1/save-folder-items`.
     * Delegates to {@see \Folders\Folders\Actions\FoldersItems::save_folder_items()}.
     *
     * @param \WP_REST_Request $request Request object; all request parameters are passed through.
     * @return array|\WP_Error Result of the delegated call.
     */
    public function save_folder_items($request)
    {
        $params = $request->get_params();
        return \Folders\Folders\Actions\FoldersItems::save_folder_items( $params );
    }

    /**
     * Move the selected items to a folder, or unassign them, from the bulk actions menu.
     *
     * REST callback for `POST /folders-settings/v1/bulk-folder-action`.
     * Delegates to {@see \Folders\Folders\Actions\FoldersItems::bulk_folder_action()}.
     *
     * @param \WP_REST_Request $request Request object; all request parameters are passed through.
     * @return array|\WP_Error Result of the delegated call.
     */
    public function bulk_folder_action($request)
    {
        $params = $request->get_params();
        return \Folders\Folders\Actions\FoldersItems::bulk_folder_action($params);
    }

    /**
     * Save the order and hierarchy of folders after drag and drop.
     *
     * REST callback for `POST /folders-settings/v1/save-folder-position`.
     * Delegates to {@see \Folders\Folders\Actions\FoldersPosition::save_folder_position()}.
     *
     * @param \WP_REST_Request $request Request object; all request parameters are passed through.
     * @return array|\WP_Error Result of the delegated call.
     */
    public function save_folder_position($request)
    {
        $params = $request->get_params();
        return \Folders\Folders\Actions\FoldersPosition::save_folder_position( $params );
    }
}
