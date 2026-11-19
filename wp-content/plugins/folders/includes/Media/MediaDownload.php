<?php
namespace Folders\Media;

use ZipArchive;

defined( 'ABSPATH' ) || exit;

/**
 * Downloads selected media files as a ZIP archive.
 *
 * Used by the "Download" bulk action in the Media Library. Archives are
 * written to `uploads/folders-temp/` under a random name and are deleted
 * about an hour after they were created.
 */
class MediaDownload {

    private static $temp_folders = 'folders-temp';

    /**
     * Upload sub-directories that hold temporary download archives.
     *
     * `folders-files` is used by {@see \Folders\Media\DownloadFolder}.
     */
    private static $archive_folders = ['folders-temp', 'folders-files'];

    /**
     * Cron hook that deletes expired download archives.
     */
    const CLEANUP_HOOK = 'folders_cleanup_download_archives';

    /**
     * Seconds a download archive is kept before it is deleted.
     */
    const ARCHIVE_LIFETIME = HOUR_IN_SECONDS;

    /**
     * Register the cron callback that deletes expired download archives.
     */
    public function __construct() {
        add_action(self::CLEANUP_HOOK, [__CLASS__, 'cleanup_expired_archives']);
    }

    /**
     * Delete expired download archives from the uploads directory.
     *
     * Runs on the cleanup cron event. Schedules itself again while archives
     * that are not old enough yet remain.
     *
     * @param bool $delete_all Optional. Delete every archive regardless of age. Default false.
     * @return void
     */
    public static function cleanup_expired_archives($delete_all = false) {
        $upload_dir = wp_upload_dir();
        $remaining  = 0;
        foreach (self::$archive_folders as $folder) {
            $dir = trailingslashit($upload_dir['basedir']) . $folder;
            self::cleanup_old_files($dir, '', $delete_all === true);
            $files = file_exists($dir) ? glob($dir . '/*.zip') : [];
            $remaining += is_array($files) ? count($files) : 0;
        }

        if ($remaining > 0) {
            self::schedule_cleanup();
        }
    }

    /**
     * Schedule the cleanup of download archives once they have expired.
     *
     * @return void
     */
    public static function schedule_cleanup() {
        if (!wp_next_scheduled(self::CLEANUP_HOOK)) {
            wp_schedule_single_event(time() + self::ARCHIVE_LIFETIME + MINUTE_IN_SECONDS, self::CLEANUP_HOOK);
        }
    }

    /**
     * Create a download directory if needed and stop its contents being listed.
     *
     * @param string $dir Absolute directory path.
     * @return bool Whether the directory exists.
     */
    public static function prepare_directory($dir) {
        if (!file_exists($dir)) {
            wp_mkdir_p($dir);
        }
        if (!is_dir($dir)) {
            return false;
        }

        $index_file = trailingslashit($dir) . 'index.php';
        if (!file_exists($index_file)) {
            @file_put_contents($index_file, "<?php\n// Silence is golden.\n");
        }

        return true;
    }


    /**
     * Create a ZIP archive of the selected attachments.
     *
     * Requires the `upload_files` capability.
     *
     * @param array $params {
     *     Request parameters.
     *
     *     @type string $nonce          Nonce for the `folder_nonce_{post_type}` action.
     *     @type string $post_type      Must be "attachment".
     *     @type array  $attachment_ids IDs of the attachments to include.
     * }
     * @return array Result array with `success`, and `download_url` and `file_name` in `data`
     *               on success or a `message` on failure.
     */
    public static function download_media_items($params) {

        $nonce       = isset( $params['nonce'] ) ? sanitize_text_field( $params['nonce'] ) : '';
        $type        = isset( $params['post_type'] ) ? sanitize_text_field( $params['post_type'] ) : '';
        $media_ids   = isset( $params['attachment_ids'] ) ? array_map('sanitize_text_field', (array)$params['attachment_ids']) : [];

        if (empty($nonce) || empty($type) || empty($media_ids)) {
            return array('success' => false, 'message' => esc_html__('Invalid request', 'folders'));
        }

        if ( ! wp_verify_nonce( $nonce, 'folder_nonce_'.$type ) ) {
            return array('success' => false, 'message' => esc_html__('Invalid request', 'folders'));
        }

        if (!current_user_can("upload_files")) {
            return array('success' => false, 'message' => esc_html__('You have not permission to download files', 'folders'));
        }

        if ($type !== 'attachment') {
            return array('success' => false, 'message' => esc_html__('Invalid request', 'folders'));
        }

        $zip = new ZipArchive();
        $upload_dir = wp_upload_dir();
        $temp_dir = trailingslashit($upload_dir['basedir']) . self::$temp_folders;
        if (!self::prepare_directory($temp_dir)) {
            return array('success' => false, 'message' => esc_html__('Error during downloading file', 'folders'));
        }

        // Clean up expired zip files before creating new one
        self::cleanup_old_files($temp_dir);

        // Generate unique filename
        $zip_name = self::generate_unique_filename();
        $zip_path = $temp_dir . '/' . $zip_name;

        if ($zip->open($zip_path, ZipArchive::CREATE) !== true) {
            return array('success' => false, 'message' => esc_html__('Error during downloading file', 'folders'));
        }

        $added = 0;
        foreach ($media_ids as $id) {
            $id = absint($id);
            // Only attachments the current user is allowed to see.
            if (!$id || get_post_type($id) !== 'attachment' || !current_user_can('read_post', $id)) {
                continue;
            }
            $file = get_attached_file($id);
            if (!empty($file) && file_exists($file)) {
                if ($zip->addFile($file, basename($file))) {
                    $added++;
                }
            }
        }

        $zip->close();

        if (!$added) {
            if (file_exists($zip_path)) {
                @unlink($zip_path);
            }
            return array('success' => false, 'message' => esc_html__('Error during downloading file', 'folders'));
        }

        self::schedule_cleanup();

        $download_url = trailingslashit($upload_dir['baseurl']) . self::$temp_folders . '/' . $zip_name;
        return array('success' => true, 'data' => array('download_url' => $download_url, 'file_name' => $zip_name));
    }

    /**
     * Generate a unique file name for a download archive.
     *
     * @return string File name like `download_{timestamp}_{random}.zip`.
     */
    private static function generate_unique_filename() {
        return 'download_' . time() . '_' . wp_generate_password(8, false) . '.zip';
    }

    /**
     * Delete old ZIP archives from a temporary download folder.
     *
     * Archives older than one hour are deleted, so a download another user
     * has just started is left alone.
     *
     * @param string $temp_dir     Absolute path of the temporary folder.
     * @param string $exclude_file Optional. File name to keep.
     * @param bool   $delete_all   Optional. Delete every archive regardless of age. Default false.
     * @return void
     */
    public static function cleanup_old_files($temp_dir, $exclude_file = '', $delete_all = false) {
        if (!file_exists($temp_dir)) {
            return;
        }

        $files = glob($temp_dir . '/*.zip');
        if (empty($files) || !is_array($files)) {
            return;
        }

        foreach ($files as $file) {
            if (is_file($file)) {
                // Skip the file we want to exclude (if any)
                if ($exclude_file && basename($file) === $exclude_file) {
                    continue;
                }
                // Delete expired files, or every file when asked to
                if ($delete_all || (time() - filemtime($file)) > self::ARCHIVE_LIFETIME) {
                    @unlink($file);
                }
            }
        }
    }

}
