<?php
namespace Folders\Folders;

defined( 'ABSPATH' ) || exit;

/**
 * Outputs the modals used by the folder sidebar on list screens.
 */
class FolderModals {
    /**
     * Register the modal output and allowed-HTML filter hooks.
     */
    public function __construct() {
        add_action( 'admin_footer', array( $this, 'folders_modal' ) );
        add_filter( 'folders_modal_allowed_html', array( $this, 'modal_allowed_html' ) );
    }

    /**
     * Allow `<input>` elements in modal content passed through wp_kses().
     *
     * Hooked to the `folders_modal_allowed_html` filter.
     *
     * @param array $allowed_tags Allowed HTML tags and attributes.
     * @return array Allowed tags including `input`.
     */
    public function modal_allowed_html($allowed_tags) {
        if(!isset($allowed_tags['input'])) {
            $allowed_tags['input'] = array(
                'id' => true,
                'style' => true,
                'class' => true,
                'type' => true,
                'autocomplete' => true,
                'value' => true,
            );
        }
        return $allowed_tags;
    }

    /**
     * Output the folder sidebar modals in the admin footer.
     *
     * Includes the add, rename, delete, move, bulk action and download modals
     * on screens where folders are active, the rating modal on the second page
     * view, and the "great job" upgrade modal until it has been dismissed.
     *
     * @return void
     */
    public function folders_modal() {
        $is_active = \Folders\Folders\Settings::is_folders_active();
        if(!$is_active) {
            return;
        }
        include_once FOLDERS_TEMPLATE_DIR . 'folders/modals/add-sub-folder.php';
        include_once FOLDERS_TEMPLATE_DIR . 'folders/modals/add-folder.php';
        include_once FOLDERS_TEMPLATE_DIR . 'folders/modals/rename-folder.php';
        include_once FOLDERS_TEMPLATE_DIR . 'folders/modals/delete-folder.php';
        include_once FOLDERS_TEMPLATE_DIR . 'folders/modals/footer-loader.php';
        include_once FOLDERS_TEMPLATE_DIR . 'folders/modals/remove-post-from-folder.php';
        include_once FOLDERS_TEMPLATE_DIR . 'folders/modals/notifications.php';
        include_once FOLDERS_TEMPLATE_DIR . 'folders/modals/delete-multiple-folders.php';
        include_once FOLDERS_TEMPLATE_DIR . 'folders/modals/select-folders-modal.php';
        include_once FOLDERS_TEMPLATE_DIR . 'folders/modals/bulk-action-modal.php';
        include_once FOLDERS_TEMPLATE_DIR . 'folders/modals/media-download-modal.php';

        $page_views = intval(get_option("get_folders_page_views"));
        if ($page_views == 2) {
            include_once FOLDERS_TEMPLATE_DIR . 'folders/modals/rating-modal.php';
        }

        $upgrade_popup_status = get_option("show_folder_upgrade_popup");
        if ($upgrade_popup_status != "hide") {
            include_once FOLDERS_TEMPLATE_DIR . 'folders/modals/great-job-modal.php';
        }
    }

    /**
     * Render a generic folder modal.
     *
     * @param array $args Modal arguments, available to the template as `$args`.
     * @return void
     */
    public static function render_modal($args = []) {
        include FOLDERS_TEMPLATE_DIR . 'folders/modals/modal.php';
    }
}
