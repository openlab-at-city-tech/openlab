<?php
namespace Folders\Folders;

defined( 'ABSPATH' ) || exit;

/**
 * Adds the drag handle column and "Move to Folder" bulk action to list screens.
 *
 * The column lets users drag one or more items onto a folder in the sidebar.
 */
class FoldersColumn {

    public static $postIds = [];

    /**
     * Register the `admin_init` hook.
     */
    public function __construct() {
        add_action('admin_init', [$this, 'admin_init']);
    }

    /**
     * Register the column and bulk action hooks for every folder-enabled post type.
     *
     * @return void
     */
    public function admin_init() {
        $folders_settings = \Folders\Folders\Settings::get_settings();
        $folders_settings = !is_array($folders_settings) ? [] : $folders_settings;

        if (in_array("post", $folders_settings)) {
            add_filter('manage_posts_columns', [$this, 'folders_column_head']);
            add_action('manage_posts_custom_column', [$this, 'folders_column_content'], 10, 2);
            add_filter('bulk_actions-edit-post', [$this, 'folders_bulk_action']);
        }

        if (in_array("page", $folders_settings)) {
            add_filter('manage_page_posts_columns', [$this, 'folders_column_head']);
            add_action('manage_page_posts_custom_column', [$this, 'folders_column_content'], 10, 2);
            add_filter('bulk_actions-edit-page', [$this, 'folders_bulk_action']);
        }

        if (in_array("attachment", $folders_settings)) {
            add_filter('manage_media_columns', [$this, 'folders_column_head']);
            add_action('manage_media_custom_column', [$this, 'folders_column_content'], 10, 2);
            add_filter('bulk_actions-upload', array($this, 'folders_bulk_action' ));
        }

        foreach ($folders_settings as $option) {
            if ($option != "post" && $option != "page" && $option != "attachment") {
                add_filter('manage_edit-' . $option . '_columns', [$this, 'folders_column_head'], 99999);
                add_action('manage_' . $option . '_posts_custom_column', [$this, 'folders_column_content'], 2, 2);
                add_filter('bulk_actions-edit-' . $option, [$this, 'folders_bulk_action']);
            }
        }
    }

    /**
     * Add the "move items" column as the first column of the list table.
     *
     * Skipped on the media trash view.
     *
     * @param array $columns List table columns.
     * @return array Modified columns.
     */
    public function folders_column_head($columns) {
        global $typenow;
        if (($typenow == "attachment" || $typenow == "media") && (isset($_REQUEST['attachment-filter']) && $_REQUEST['attachment-filter'] == "trash")) {
            return $columns;
        }

        global $typenow;
        $type = $typenow;
        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'inline-save') {
            $type = esc_attr(sanitize_text_field($_REQUEST['post_type']));
        }

        $options = \Folders\Folders\Settings::get_settings();
        if (is_array($options) && in_array($type, $options)) {
            $columns = ([
                    'folders_item_move' => '<div class="folders-move-multiple wcp-col" title="' . esc_html__('Move selected items', 'folders') . '"><span class="dashicons dashicons-move"></span><div class="wcp-items"></div></div>',
                ] + $columns);
            return $columns;
        }

        return $columns;
    }

    /**
     * Output the drag handle and truncated title for an item in the move column.
     *
     * Each post is rendered only once per request.
     *
     * @param string $column_name Column being rendered.
     * @param int    $post_id     Post ID.
     * @return void
     */
    public function folders_column_content($column_name, $post_id) {
        $postIDs = self::$postIds;
        if (!is_array($postIDs)) {
            $postIDs = [];
        }

        if (!in_array($post_id, $postIDs)) {
            $postIDs[] = $post_id;
            self::$postIds = $postIDs;
            global $typenow;
            $type = $typenow;
            if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'inline-save') {
                $type = esc_attr(sanitize_text_field($_REQUEST['post_type']));
            }

            $options = \Folders\Folders\Settings::get_settings();
            if (is_array($options) && in_array($type, $options)) {
                if ($column_name == 'folders_item_move') {
                    $title = get_the_title();
                    if (strlen($title) > 20) {
                        $title = substr($title, 0, 20) . "...";
                    }

                    echo "<div class='folders-move-file' data-id='{$post_id}'><span class='folder-item-move dashicons dashicons-move' data-id='{$post_id}'></span><span class='folder-item-title' data-object-id='{$post_id}'>" . esc_attr($title) . "</span></div>";
                }
            }
        }//end if
    }

    /**
     * Add the "Move to Folder" bulk action.
     *
     * @param array $bulk_actions Registered bulk actions.
     * @return array Modified bulk actions.
     */
    public function folders_bulk_action($bulk_actions) {
        $bulk_actions['move_to_folder'] = esc_html__('Move to Folder', 'folders');
        return $bulk_actions;
    }
}
