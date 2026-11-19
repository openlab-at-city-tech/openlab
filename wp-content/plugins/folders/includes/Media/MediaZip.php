<?php

namespace Folders\Media;

defined( 'ABSPATH' ) || exit;

/**
 * Extract an uploaded ZIP attachment into the Media Library.
 *
 * Adds an "Extract ZIP" button to the Attachment details modal. Extraction
 * runs in batches over two REST routes so the modal can show progress:
 *
 * 1. `unzip-folder`       Validates the ZIP, creates the folder tree (ZIP name
 *                         as a root folder, then its inner directories) and
 *                         stores a job in a transient.
 * 2. `unzip-folder-batch` Imports the next files of the job for a few seconds
 *                         and returns the counters. Called until `done`.
 *
 * Each file is streamed out of the archive into a temp file and passed to
 * media_handle_sideload(), so the normal WordPress upload rules apply: allowed
 * MIME types for the current user, real file-type check, sanitized and unique
 * file names, year/month upload folders and image sub-sizes. Nothing from the
 * archive is ever written to disk under its own path.
 */
class MediaZip
{
    /**
     * Transient prefix for unzip jobs.
     */
    const JOB_PREFIX = 'folders_unzip_job_';

    /**
     * Seconds to spend importing files in one batch request.
     */
    const BATCH_SECONDS = 8;

    /**
     * Nonce action used by the confirmation form.
     */
    const NONCE_ACTION = 'unzip_folder_file';

    /**
     * Whether the script and modals were already enqueued on this request.
     *
     * @var bool
     */
    private $scripts_enqueued = false;

    public function __construct()
    {
        add_action('wp_enqueue_media', [$this, 'enqueue_scripts']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_list_scripts']);
        add_action('print_media_templates', [$this, 'print_media_templates']);
        add_filter('media_row_actions', [$this, 'media_row_actions'], 10, 2);
    }

    /**
     * Add an "Unzip" link after "Edit" for ZIP files in the Media list view.
     *
     * @param string[] $actions Row action links keyed by action name.
     * @param \WP_Post $post    Attachment.
     * @return string[]
     */
    public function media_row_actions($actions, $post)
    {
        if (empty($post->ID) || !$this->can_show_unzip($post->ID)) {
            return $actions;
        }

        $link = sprintf(
            '<a href="%1$s" target="_blank" class="folders-media-zip-button" data-id="%2$d" aria-label="%3$s">%4$s</a>',
            '#',
            (int)$post->ID,
            /* translators: %s: file title. */
            esc_attr(sprintf(__('Unzip &#8220;%s&#8221;', 'folders'), _draft_or_post_title($post))),
            esc_html__('Unzip', 'folders')
        );


        $new_actions = [];
        foreach ($actions as $key => $action) {
            $new_actions[$key] = $action;
            if ('edit' === $key) {
                $new_actions['folders_unzip'] = $link;
            }
        }
        if (!isset($new_actions['folders_unzip'])) {
            $new_actions['folders_unzip'] = $link;
        }

        return $new_actions;
    }

    /**
     * Whether the current user may unzip this attachment and it is a ZIP file.
     *
     * @param int $attachment_id Attachment ID.
     * @return bool
     */
    private function can_show_unzip($attachment_id)
    {
        if (!current_user_can('upload_files') || !current_user_can('edit_post', $attachment_id)) {
            return false;
        }


        // Same condition as enqueue_scripts(): without it the link would do nothing.
        if (!\Folders\Folders\Settings::check_for_folder('attachment')) {
            return false;
        }

        $file = get_attached_file($attachment_id);
        return !empty($file) && 'zip' === strtolower(pathinfo($file, PATHINFO_EXTENSION));
    }


    /**
     * Load the unzip script and modals on the Media list view (upload.php?mode=list),
     * which does not call wp_enqueue_media().
     *
     * @param string $hook_suffix Current admin page.
     * @return void
     */
    public function enqueue_list_scripts($hook_suffix)
    {
        if ('upload.php' === $hook_suffix) {
            $this->enqueue_scripts();
        }
    }


    /**
     * Load the script that injects the button into the attachment details view.
     *
     * @return void
     */
    public function enqueue_scripts()
    {
        // Runs from both wp_enqueue_media and admin_enqueue_scripts on upload.php.
        // A second run would localize empty modal_html (include_once) over the first.
        if ($this->scripts_enqueued) {
            return;
        }

        $user_access = \Folders\Folders\UserFolders::get_user_role();
        if ($user_access == 'no-access' || $user_access == 'view-only') {
            return;
        }
        if (!\Folders\Folders\Settings::check_for_folder('attachment')) {
            return;
        }

        ob_start();
        include_once FOLDERS_PLUGIN_DIR . 'templates/folders/zip-files/confirmaion.php';
        $content = ob_get_clean();
        $this->scripts_enqueued = true;

        wp_enqueue_style('folders-media-zip-css', FOLDERS_PLUGIN_URL . 'dist/css/media-zip.css', [], FOLDERS_VERSION);
        wp_enqueue_script('folders-media-zip-js', FOLDERS_PLUGIN_URL . 'dist/js/media-zip.js', ['jquery', 'media-views'], FOLDERS_VERSION, true);
        wp_localize_script('folders-media-zip-js', 'folders_media_zip', [
            'modal_html' => $content
        ]);

        $custom_css = ":root { --folders-media-zip-btn-color: " . esc_attr(\Folders\Admin\Settings::get_field_settings('customization_settings', 'new_folder_color')) . "; }";
        wp_add_inline_style('folders-media-zip-css', $custom_css);
    }

    /**
     * Print the Backbone template for the button.
     *
     * @return void
     */
    public function print_media_templates()
    {
        ?>
        <script type="text/html" id="tmpl-folders-media-zip-action">
            <# if ( 'zip' === data.subtype ) { #>
            <div>
                <a href="#" target="_blank" type="button" class="folders-media-zip-pro-btn" data-id="{{ data.id }}" disabled>
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1.7733 13.1104C1.39311 10.6392 1.01292 8.16797 0.632733 5.69672C0.548421 5.14891 1.17173 4.77528 1.61511 5.10784C2.79961 5.99622 3.98405 6.88453 5.16855 7.77291C5.55855 8.06541 6.11417 7.97022 6.38455 7.56459L9.34286 3.12709C9.65548 2.65816 10.3445 2.65816 10.6571 3.12709L13.6154 7.56459C13.8858 7.97022 14.4414 8.06534 14.8314 7.77291C16.0159 6.88453 17.2004 5.99622 18.3849 5.10784C18.8282 4.77528 19.4515 5.14891 19.3673 5.69672C18.9871 8.16797 18.6069 10.6392 18.2267 13.1104H1.7733Z" fill="currentColor"/>
                        <path d="M17.369 17.2246H2.63125C2.1575 17.2246 1.77344 16.8405 1.77344 16.3668V14.4824H18.2269V16.3668C18.2268 16.8405 17.8428 17.2246 17.369 17.2246Z" fill="currentColor"/>
                    </svg>
                    <?php esc_html_e('Unzip File', 'folders'); ?>
                </a>
            </div>
            <# } #>
        </script>
        <?php
    }
}
