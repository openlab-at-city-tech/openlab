<?php
namespace Folders\Media;
/**
 * Class Folders Replace Media
 *
 * @author  : Premio <contact@premio.io>
 * @license : GPL2
 * */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Replace media files and rename files after their title.
 *
 * When "Replace media" is enabled, adds a Replace media screen, row action,
 * meta box and attachment field that upload a new file in place of an
 * existing attachment. Independently of that setting, adds the "Change file
 * name according to title" option. After a file is replaced or renamed,
 * URLs pointing to the old file (and its image sizes) are updated in post
 * content, options and meta tables, and a version query argument is added
 * to the attachment URLs so browsers load the new file. Also manages the
 * `MEDIA_TRASH` constant in wp-config.php for the Media Cleaning feature.
 */
class MediaReplace
{

    /**
     * Button Color
     *
     * @var    string $button_color Replacement Button Color
     * @since  1.0.0
     * @access public
     */
    public $button_color;

    /**
     * Is Replacement functionality enabled or not
     *
     * @var    string $is_enabled Replacement Functionality Status
     * @since  1.0.0
     * @access public
     */
    public $is_enabled = false;

    /**
     * Folders Upgrade Link
     *
     * @var    string $upgradeLink Upgrade Link
     * @since  1.0.0
     * @access public
     */
    public $upgradeLink;

    /**
     * Old file path
     *
     * @var    string $oldFilePath Old File Path
     * @since  1.0.0
     * @access public
     */
    public $oldFilePath;

    /**
     * Old file URL
     *
     * @var    string $oldFilePath Old File URL
     * @since  1.0.0
     * @access public
     */
    public $oldFileURL;

    /**
     * New file Path
     *
     * @var    string $newFilePath New File Path
     * @since  1.0.0
     * @access public
     */
    public $newFilePath;

    /**
     * New file URL
     *
     * @var    string $newFilePath New File URL
     * @since  1.0.0
     * @access public
     */
    public $newFileURL;

    /**
     * Mode for file Replacement
     *
     * @var    string $mode New File URL
     * @since  1.0.0
     * @access public
     */
    public $mode = "rename-file";

    /**
     * Old image Meta Array
     *
     * @var    array $oldImageMeta Old image Meta Array
     * @since  1.0.0
     * @access public
     */
    public $oldImageMeta;

    /**
     * New image Meta Array
     *
     * @var    array $newImageMeta New image Meta Array
     * @since  1.0.0
     * @access public
     */
    public $newImageMeta;

    /**
     * WordPress Upload Dir Path
     *
     * @var    string $uploadDir WordPress Upload Dir Path
     * @since  1.0.0
     * @access public
     */
    public $uploadDir;

    /**
     * Check if old file is image
     *
     * @var    string $isOldImage Old file status
     * @since  1.0.0
     * @access public
     */
    public $isOldImage = 0;

    /**
     * Check if uploaded file is image
     *
     * @var    string $isNewImage New file status
     * @since  1.0.0
     * @access public
     */
    public $isNewImage = 0;

    /**
     * Attachment ID for Replacement file
     *
     * @var    string $attachment_id Attachment ID
     * @since  1.0.0
     * @access public
     */
    public $attachment_id;

    /**
     * Collection of Replacement Items
     *
     * @var    array $replaceItems Replacement Items
     * @since  1.0.0
     * @access public
     */
    public $replaceItems = [];

    /**
     * Old File Path
     *
     * @var    array $old_file_path Old File Path
     * @since  1.0.0
     * @access public
     */
    public $old_file_path;

    /**
     * Old File URL
     *
     * @var    array $old_file_url Old File URL
     * @since  1.0.0
     * @access public
     */
    public $old_file_url;

    /**
     * New file path
     *
     * @var    array $new_file_path New file path
     * @since  1.0.0
     * @access public
     */
    public $new_file_path;

    /**
     * New file URL
     *
     * @var    array $new_file_url New file URL
     * @since  1.0.0
     * @access public
     */
    public $new_file_url;

    /**
     * Old Image Meta
     *
     * @var    array $old_image_meta Old Image Meta
     * @since  1.0.0
     * @access public
     */
    public $old_image_meta;

    /**
     * New Image Meta
     *
     * @var    array $new_image_meta New Image Meta
     * @since  1.0.0
     * @access public
     */
    public $new_image_meta;

    /**
     * Upload dir path
     *
     * @var    array $upload_dir Upload dir path
     * @since  1.0.0
     * @access public
     */
    public $upload_dir;

    /**
     * Old file image status
     *
     * @var    array $is_old_image Old file image status
     * @since  1.0.0
     * @access public
     */
    public $is_old_image = 0;

    /**
     * New file image status
     *
     * @var    array $is_new_image New file image status
     * @since  1.0.0
     * @access public
     */
    public $is_new_image = 0;

    /**
     * New file name
     *
     * @var    array $replace_media_title New file name
     * @since  1.0.0
     * @access public
     */
    public $replace_media_title;
    public $replace_items = array();

    /**
     * Read the replace media settings and register the hooks.
     *
     * Replace media hooks are only registered when the feature is enabled; the
     * rename-to-title, wp-config.php, notice and URL cache-busting hooks are
     * always registered.
     */
    function __construct()
    {

        $this->button_color = \Folders\Admin\Settings::get_field_settings('customization_settings', 'media_replace_button');
        $this->is_enabled = \Folders\Admin\Settings::get_field_settings('general_settings', 'folders_enable_replace_media');
        $this->replace_media_title = \Folders\Admin\Settings::get_field_settings('general_settings', 'replace_media_title');
        $this->upgradeLink = \Folders\Admin\License::get_pro_url();

        if ($this->is_enabled) {

            add_action('admin_menu', array($this, 'admin_menu'));

            add_filter('media_row_actions', array($this, 'add_media_action'), 10, 2);

            add_action('add_meta_boxes', function () {
                add_meta_box('folders-replace-box', esc_html__('Replace Media', 'folders'), array($this, 'replace_meta_box'), 'attachment', 'side', 'low');
            });
            add_filter('attachment_fields_to_edit', array($this, 'attachment_editor'), 10, 2);

            add_action('admin_enqueue_scripts', array($this, 'folders_admin_css_and_js'));

            add_action('admin_init', array($this, 'handle_folders_file_upload'));
        }

        /* to replace file name */
        add_action('add_meta_boxes', function () {
            add_meta_box('folders-replace-file-name', esc_html__('Change file name', 'folders'), array($this, 'change_file_name_box'), 'attachment', 'side', 'core');
        });

        add_filter('attachment_fields_to_edit', array($this, 'attachment_replace_name_with_title'), 10, 2);

        add_action('admin_head', array($this, 'premio_replace_file_CSS'));

        add_action('wp_enqueue_media', array($this, 'replace_media_file_script'));

        add_action('wp_ajax_premio_folder_replace_name_with_title', array($this, 'replace_name_with_title'));

        add_action('wp_ajax_premio_folder_update_wp_config', array($this, 'update_wp_config'));
        add_action('wp_ajax_premio_folder_update_wp_config_remove', array($this, 'update_wp_config_remove'));

        add_action('admin_notices', array($this, 'admin_premio_notices'));

        add_filter('wp_get_attachment_image_src', array($this, 'update_to_new_url'), 10, 4);

        add_filter('wp_prepare_attachment_for_js', array($this, 'prepare_attachment_for_js'), 10, 3);

    }

    /**
     * Add a cache-busting `ver` query argument to a replaced attachment's URLs in the media modal.
     *
     * Uses the `folders_file_replaced` post meta timestamp. Hooked to
     * `wp_prepare_attachment_for_js`.
     *
     * @since 2.8.4
     *
     * @param array|false $response   Attachment data prepared for JavaScript.
     * @param \WP_Post    $attachment Attachment post.
     * @param array|false $meta       Attachment metadata.
     * @return array|false Modified attachment data.
     */
    public function prepare_attachment_for_js($response, $attachment, $meta)
    {
        if ($response === false) {
            return $response;
        }

        $status = apply_filters('remove_folders_file_replaced_meta', false, $attachment->ID);
        if ($status) {
            return $response;
        }

        $refreshToken = get_post_meta($response['id'], "folders_file_replaced", true);
        if ($refreshToken !== false && !empty($refreshToken)) {
            if (isset($response['url']) && !empty($response['url'])) {
                $response['url'] = add_query_arg('ver', $refreshToken, $response['url']);
            }
            if (isset($response['sizes']) && is_array($response['sizes']) && !empty($response['sizes'])) {
                if (isset($response['sizes']['medium']['url'])) {
                    $response['sizes']['medium']['url'] = add_query_arg('ver', $refreshToken, $response['sizes']['medium']['url']);
                }
                foreach ($response['sizes'] as $size => $image) {
                    if (isset($image['url']) && !empty($image['url'])) {
                        $response['sizes'][$size]['url'] = add_query_arg('ver', $refreshToken, $response['sizes'][$size]['url']);
                    }
                }
            }
        }
        return $response;
    }

    /**
     * Add a cache-busting `ver` query argument to a replaced attachment's image URL.
     *
     * Hooked to `wp_get_attachment_image_src`.
     *
     * @since 2.8.4
     *
     * @param array|false  $image         Image data: URL, width, height, is-intermediate flag.
     * @param int          $attachment_id Attachment ID.
     * @param string|int[] $size          Requested image size.
     * @param bool         $icon          Whether the image should be treated as an icon.
     * @return array|false Modified image data.
     */
    public function update_to_new_url($image, $attachment_id, $size, $icon)
    {
        if ($image === false) {
            return $image;
        }

        $status = apply_filters('remove_folders_file_replaced_meta', false, $attachment_id);
        if ($status) {
            return $image;
        }

        $refreshToken = get_post_meta($attachment_id, "folders_file_replaced", true);
        if ($refreshToken !== false && !empty($refreshToken)) {
            $image[0] = add_query_arg('ver', $refreshToken, $image[0]);
        }
        return $image;
    }

    /**
     * Show the "File successfully replaced" notice after a file replacement.
     *
     * Displayed when the `premio_message=success` query argument is present.
     *
     * @since 2.6.3
     *
     * @return void
     */
    public function admin_premio_notices()
    {
        if (isset($_REQUEST['premio_message']) && $_REQUEST['premio_message'] == "success") { ?>
            <div class="notice notice-success is-dismissible">
                <p><b><?php esc_html_e('File successfully replaced', 'folders'); ?></b></p>
                <p><?php esc_html_e('The file has been successfully replaced using the file replacement feature', 'folders'); ?></p>
            </div>

            <style>
                .folders-undo-notification {
                    position: fixed;
                    right: -500px;
                    bottom: 25px;
                    width: 280px;
                    background: #fff;
                    padding: 15px;
                    -webkit-box-shadow: 0 3px 6px -4px rgb(0 0 0 / 12%), 0 6px 16px 0 rgb(0 0 0 / 8%), 0 9px 28px 8px rgb(0 0 0 / 5%);
                    box-shadow: 0 3px 6px -4px rgb(0 0 0 / 12%), 0 6px 16px 0 rgb(0 0 0 / 8%), 0 9px 28px 8px rgb(0 0 0 / 5%);
                    transition: all .25s linear;
                    z-index: 250010;
                }

                .folders-undo-body {
                    position: relative;
                    font-size: 13px;
                    padding: 0 0 5px 0;
                }

                .close-undo-box {
                    position: absolute;
                    right: -10px;
                    top: 0;
                    width: 16px;
                    height: 16px;
                    transition: all .25s linear;
                }

                .close-undo-box span {
                    display: block;
                    position: relative;
                    width: 16px;
                    height: 16px;
                    transition: all .2s linear;
                }

                .close-undo-box span:after, .close-undo-box span:before {
                    content: "";
                    position: absolute;
                    width: 12px;
                    height: 2px;
                    background-color: #333;
                    display: block;
                    border-radius: 2px;
                    transform: rotate(45deg);
                    top: 7px;
                    left: 2px;
                }

                .close-undo-box span:after {
                    transform: rotate(-45deg);
                }

                .folders-undo-header {
                    font-weight: 500;
                    font-size: 14px;
                    padding: 0 0 3px 0;
                    color: #014737;
                }

                .folders-undo-notification.success {
                    border-left: solid 3px #70C6A3;
                }

                html[dir="rtl"] .folders-undo-notification {
                    right: auto;
                    left: -500px
                }

                html[dir="rtl"] .folders-undo-notification.active {
                    left: 25px;
                }

                html[dir="rtl"] .folders-undo-notification.success {
                    border-left: none;
                    border-right: solid 3px #70C6A3;
                }

                html[dir="rtl"] .close-undo-box {
                    right: auto;
                    left: -10px;
                }
            </style>
            <div class="folders-undo-notification success" id="media-success">
                <div class="folders-undo-body">
                    <a href="#" class="close-undo-box"><span></span></a>
                    <div class="folders-undo-header"><?php esc_html_e('File successfully replaced', 'folders'); ?></div>
                    <div class="folders-undo-body" style="padding:0"><?php esc_html_e('The file has been successfully replaced using the file replacement feature', 'folders'); ?></div>
                </div>
            </div>
            <script>
                jQuery(document).ready(function () {
                    jQuery("#media-success").addClass("active");
                    setTimeout(function () {
                        jQuery("#media-success").removeClass("active");
                    }, 5000);

                    jQuery(document).on("click", ".close-undo-box", function () {
                        jQuery("#media-success").removeClass("active");
                    });
                });
            </script>
        <?php }
    }


    /**
     * AJAX handler that removes `define( 'MEDIA_TRASH', true );` from wp-config.php.
     *
     * Verifies the `remove_media_status_in_wp_config` nonce. Outputs a JSON
     * response whose `status` is 1 when the line was removed, 0 when it was not
     * found and -1 when wp-config.php could not be written, then exits.
     *
     * @return void
     */
    public function update_wp_config_remove()
    {
        $errorCounter = 0;
        $response = array();
        $response['status'] = 0;
        $response['message'] = "";
        $response['valid'] = 0;
        $postData = filter_input_array(INPUT_POST);
        if (!isset($postData['nonce']) || trim($postData['nonce']) == "") {
            $errorCounter++;
            $response['message'] = "Invalid request";
        } else {
            $nonce = sanitize_title($postData['nonce']);
            if (!wp_verify_nonce($nonce, 'remove_media_status_in_wp_config')) {
                $errorCounter++;
                $response['message'] = "Invalid request";
            }
        }
        if ($errorCounter == 0) {
            $response['status'] = 1;

            $is_defined = defined('MEDIA_TRASH');
            if (!($is_defined && MEDIA_TRASH)) {
                echo wp_json_encode($response);
                die;
            }

            try {
                $conf = ABSPATH . 'wp-config.php';

                $DELETE = "define( 'MEDIA_TRASH', true );";

                $data = file($conf);

                $out = array();
                $isExists = 0;
                foreach ($data as $line) {
                    if (trim($line) != $DELETE) {
                        $out[] = $line;
                    } else {
                        $isExists = 1;
                    }
                }

                $fp = fopen($conf, "w+");
                if ($fp === false) {
                    $response['status'] = -1;
                    echo wp_json_encode($response);
                    die;
                }
                if (!flock($fp, LOCK_EX)) {
                    $response['status'] = -1;
                    echo wp_json_encode($response);
                    die;
                }
                $written = false;
                foreach ($out as $line) {
                    $written = fwrite($fp, $line);
                }
                if (!$written) {
                    $response['status'] = -1;
                    echo wp_json_encode($response);
                    die;
                }
                flock($fp, LOCK_UN);
                fclose($fp);
                if ($isExists) {
                    delete_option('enable_media_trash_changed');
                }
                $response['status'] = $isExists;
                echo wp_json_encode($response);
                die;
            } catch (Exception $e) {
                $response['status'] = -1;
                echo wp_json_encode($response);
                die;
            }
        }

    }

    /**
     * AJAX handler that adds `define( 'MEDIA_TRASH', true );` to wp-config.php.
     *
     * Enables the media trash so the Media Cleaning feature can move files to
     * the trash instead of deleting them. The line is inserted above the
     * "That's all, stop editing!" comment. Verifies the
     * `add_media_status_in_wp_config` nonce and outputs a JSON response
     * (`status` -1 on failure), then exits.
     *
     * @since 2.6.3
     *
     * @return void
     */
    public function update_wp_config()
    {
        $errorCounter = 0;
        $response = array();
        $response['status'] = 0;
        $response['message'] = "";
        $response['valid'] = 0;
        $postData = filter_input_array(INPUT_POST);
        if (!isset($postData['nonce']) || trim($postData['nonce']) == "") {
            $errorCounter++;
            $response['message'] = "Invalid request";
        } else {
            $nonce = sanitize_title($postData['nonce']);
            if (!wp_verify_nonce($nonce, 'add_media_status_in_wp_config')) {
                $errorCounter++;
                $response['message'] = "Invalid request";
            }
        }
        if ($errorCounter == 0) {
            $response['status'] = 1;

            $is_defined = defined('MEDIA_TRASH');
            if ($is_defined && MEDIA_TRASH) {
                echo wp_json_encode($response);
                die;
            }

            try {
                $conf = ABSPATH . 'wp-config.php';
                $stream = fopen($conf, 'r+');
                if ($stream === false) {
                    $response['status'] = -1;
                    echo wp_json_encode($response);
                    die;
                }

                try {
                    if (!flock($stream, LOCK_EX)) {
                        $response['status'] = -1;
                        echo wp_json_encode($response);
                        die;
                    }
                    $stat = fstat($stream);

                    /* Find out the ideal position to write on */
                    $found = false;
                    $patterns = array(
                        array(
                            'regex' => '^\/\*\s*' . preg_quote("That's all, stop editing!") . '.*?\s*\*\/',
                            'where' => 'above'
                        )
                    );
                    $current = 0;
                    while (!feof($stream)) {
                        $line = fgets($stream); // Read line by line
                        if ($line === false) break; // No more lines
                        $prev = $current; // Previous position
                        $current = ftell($stream); // Current position
                        foreach ($patterns as $item) {
                            if (!preg_match('/' . $item['regex'] . '/', trim($line))) {
                                continue;
                            }
                            $found = true;
                            if ($item['where'] == 'above') {
                                fseek($stream, $prev);
                                $current = $prev;
                            }
                            break 2;
                        }
                    }

                    /* Check if the position is found */
                    if (!$found) {
                        $response['status'] = -1;
                        echo wp_json_encode($response);
                        die;
                    }

                    /* Write the constant definition line */
                    $new = "define( 'MEDIA_TRASH', true );" . PHP_EOL;
                    $rest = fread($stream, $stat['size'] - $current);
                    fseek($stream, $current);
                    $written = fwrite($stream, $new . $rest);

                    /* All done */
                    if ($written === false) {
                        $response['status'] = -1;
                        delete_option('enable_media_trash_changed');
                        add_option('enable_media_trash_changed', 1);
                        echo wp_json_encode($response);
                        die;
                    }
                    fclose($stream);
                } catch (Exception $e) {
                    fclose($stream);

                    $response['status'] = -1;
                    echo wp_json_encode($response);
                    die;
                }
            } catch (Exception $e) {
                $response['status'] = -1;
                echo wp_json_encode($response);
                die;
            }

            echo wp_json_encode($response);
            die;
        }
        echo wp_json_encode($response);
        die;
    }

    /**
     * AJAX handler that renames an attachment's file to match its title.
     *
     * Verifies the `change_attachment_title_{post_id}` nonce, then renames the
     * file via {@see change_file_name_with_title()} unless the title already
     * matches the file name. Outputs a JSON response and exits.
     *
     * @since 2.6.3
     *
     * @return void
     */
    public function replace_name_with_title()
    {
        $errorCounter = 0;
        $response = array();
        $response['status'] = 0;
        $response['message'] = "";
        $response['valid'] = 0;
        $postData = filter_input_array(INPUT_POST);
        if (!isset($postData['post_id']) || trim($postData['post_id']) == "") {
            $errorCounter++;
            $response['message'] = "Invalid request";
        } else if (!isset($postData['nonce']) || trim($postData['nonce']) == "") {
            $errorCounter++;
            $response['message'] = "Invalid request";
        } else if (!isset($postData['post_title']) || trim($postData['post_title']) == "") {
            $errorCounter++;
            $response['message'] = "Invalid request";
        } else {
            $nonce = sanitize_title($postData['nonce']);
            if (!wp_verify_nonce($nonce, 'change_attachment_title_' . $postData['post_id'])) {
                $errorCounter++;
                $response['message'] = "Invalid request";
            } else if (!current_user_can('edit_post', absint($postData['post_id']))) {
                $errorCounter++;
                $response['message'] = "Invalid request";
            }
        }
        if ($errorCounter == 0) {
            $response['status'] = 1;

            $post_id = absint($postData['post_id']);

            $post = get_post($post_id);

            $post_slug = sanitize_file_name(sanitize_text_field($_POST['post_title']));

            $attachment_url = $post->guid;
            $url = wp_get_attachment_url($post_id);
            if (!empty($url)) {
                $attachment_url = $url;
            }
            $file_parts = pathinfo($attachment_url);

            $db_file_name = $file_parts['basename'];
            $db_file_array = explode(".", $db_file_name);
            $db_file_name_ext = array_pop($db_file_array);
            $db_file_name = trim($db_file_name, $db_file_name_ext);
            $db_file_name = trim($db_file_name, ".");

            if (strtolower($db_file_name) == strtolower($post_slug)) {
                $response['valid'] = 0;
                $response['message'] = esc_html__("The title is same as the current filename", 'folders');
            } else {
                $response['valid'] = 1;
                $response['message'] = esc_html__("File name has been updated", 'folders');
                $this->change_file_name_with_title($post_id);
            }
        }
        echo wp_json_encode($response);
        exit;
    }

    /**
     * Rename an attachment's file to the sanitized title from `$_POST['post_title']`.
     *
     * Renames the file on disk (adding a numeric suffix if the name is taken),
     * updates the attachment record, regenerates the image sizes, replaces
     * references to the old URLs in the database and marks the attachment as
     * replaced for cache busting.
     *
     * @param int $post_id Attachment ID.
     * @return void
     */
    public function change_file_name_with_title($post_id = 0)
    {
        if (empty($post_id)) {
            return;
        }
        $post = get_post($post_id);
        if (empty($post)) {
            return;
        }
        if ($post->post_type != "attachment") {
            return;
        }
        $this->attachment_id = $post->ID;

        $attachment_id = $post->ID;
        $attachment = get_post($attachment_id);
        $attachment_meta = wp_get_attachment_metadata($attachment_id);
        $this->old_image_meta = $attachment_meta;
        $get_attached_file = get_attached_file($attachment_id);
        $file_name = basename($get_attached_file);

        $file_ext = explode(".", $file_name);
        $file_ext = array_pop($file_ext);
        $post_slug = sanitize_file_name(sanitize_text_field($_POST['post_title']));
        $new_file_name = $post_slug . "." . $file_ext;

        $wp_upload_path = wp_get_upload_dir();

        $base_path = $wp_upload_path['basedir'] . DIRECTORY_SEPARATOR;
        $baseurl = $wp_upload_path['baseurl'] . "/";

        $post_upload = "";

        $wp_attached_file = get_post_meta($attachment_id, "_wp_attached_file", true);
        if ($wp_attached_file !== false) {
            $old_file_name = explode("/", $wp_attached_file);
            array_pop($old_file_name);

            if (count($old_file_name) > 0) {
                $base_path .= implode(DIRECTORY_SEPARATOR, $old_file_name);
                $baseurl .= implode("/", $old_file_name);

                $post_upload = implode("/", $old_file_name);
            }
        }

        $upload_dir = array();
        $upload_dir['path'] = $base_path;
        $upload_dir['old_path'] = $base_path;
        $upload_dir['url'] = $baseurl;
        $upload_dir['old_url'] = $baseurl;

        $this->upload_dir = $upload_dir;

        $attachment_url = $attachment->guid;
        $url = wp_get_attachment_url($attachment_id);
        if (!empty($url)) {
            $attachment_url = $url;
        }
        $file_parts = pathinfo($attachment_url);
        $this->old_file_path = $base_path . DIRECTORY_SEPARATOR . $file_parts['basename'];
        if (isset($attachment_meta['file']) && !empty($attachment_meta['file'])) {
            $this->old_file_url = $wp_upload_path['baseurl'] . "/" . $attachment_meta['file'];
        } else {
            $this->old_file_url = wp_get_attachment_url($post_id);
        }

        if ($new_file_name != $file_name) {
            global $wpdb;

            $new_file_name = $this->checkForFileName($new_file_name, $upload_dir['path'] . DIRECTORY_SEPARATOR);

            $this->new_file_path = $upload_dir['path'] . DIRECTORY_SEPARATOR . $new_file_name;
            $this->new_file_url = $upload_dir['url'] . "/" . $new_file_name;
            if (file_exists($this->old_file_path)) {
                rename($this->old_file_path, $this->new_file_path);

                update_attached_file($post->ID, $this->new_file_path);

                $update_array = array();
                $update_array['ID'] = $post->ID;
                $update_array['post_title'] = sanitize_text_field($_REQUEST['post_title']);
                $update_array['post_name'] = sanitize_title($post_slug);
                $update_array['guid'] = $this->new_file_path; //wp_get_attachment_url($this->post_id);
                $update_array['post_mime_type'] = $post->post_mime_type;
                $post_id = wp_update_post($update_array, true);

                // update post doesn't update GUID on updates.
                $this->removeThumbImages();

                $metadata = wp_generate_attachment_metadata($post->ID, $this->new_file_path);
                wp_update_attachment_metadata($post->ID, $metadata);

                $this->new_image_meta = wp_get_attachment_metadata($attachment_id);

                update_post_meta($attachment_id, '_wp_attached_file', trim(trim($post_upload, "/") . "/" . $new_file_name, "/"));

                $this->searchAndReplace();

                delete_post_meta($attachment_id, "folders_file_replaced");
                add_post_meta($attachment_id, "folders_file_replaced", time(), true);
            }
        }
    }

    /**
     * Find a file name that does not exist yet in a directory.
     *
     * Appends "-1", "-2", and so on before the extension until the name is free.
     *
     * @since 2.6.3
     *
     * @param string $fileName Desired file name.
     * @param string $filePath Directory path with a trailing separator.
     * @param int    $postFix  Current numeric suffix; 0 for none.
     * @return string Available file name.
     */
    public function checkForFileName($fileName, $filePath, $postFix = 0)
    {
        $new_file_name = $fileName;
        if (!empty($postFix)) {
            $file_array = explode(".", $fileName);
            $file_ext = array_pop($file_array);
            $new_file_name = implode(".", $file_array) . "-" . $postFix . "." . $file_ext;
        }
        if (!file_exists($filePath . $new_file_name)) {
            return $new_file_name;
        }
        return $this->checkForFileName($fileName, $filePath, ($postFix + 1));
    }

    /**
     * Delete the generated image sizes of the old file (except SVG files).
     *
     * @since 2.6.3
     *
     * @return void
     */
    public function removeThumbImages()
    {
        if (!empty($this->old_image_meta) && isset($this->old_image_meta['sizes']) && !empty($this->upload_dir) && isset($this->upload_dir['path'])) {
            $path = $this->upload_dir['old_path'] . DIRECTORY_SEPARATOR;
            foreach ($this->old_image_meta['sizes'] as $image) {
                if (file_exists($path . $image['file']) && (!isset($image['mime-type']) || $image['mime-type'] != 'image/svg+xml')) {
                    @wp_delete_file($path . $image['file']);
                }
            }
        }
    }

    /**
     * Build the list of old-to-new URL replacements and apply them to the database.
     *
     * Includes the main file URL and each image size URL, mapping old sizes to
     * the matching new size or to the new full-size URL.
     *
     * @since 2.6.3
     *
     * @return void
     */
    public function searchAndReplace()
    {
        if (wp_attachment_is('image', $this->attachment_id)) {
            $this->is_new_image = 1;
        }
        if ($this->old_file_url != $this->new_file_url) {
            $replace = array(
                'search' => $this->old_file_url,
                'replace' => $this->new_file_url,
            );
            $this->replace_items[] = $replace;
        }

        $base_url = $this->upload_dir['url'];
        $base_url = trim($base_url, "/") . "/";
        $new_url = $this->new_file_url;

        if (isset($this->old_image_meta['sizes']) && !empty($this->old_image_meta['sizes'])) {
            if (!isset($this->new_image_meta['sizes']) || empty($this->new_image_meta['sizes'])) {
                foreach ($this->old_image_meta['sizes'] as $key => $image) {
                    $replace = array(
                        'search' => $base_url . $image['file'],
                        'replace' => $new_url,
                    );
                    $this->replace_items[] = $replace;
                }
            } else if (isset($this->new_image_meta['sizes']) && !empty($this->new_image_meta['sizes'])) {
                $new_size = $this->new_image_meta['sizes'];
                foreach ($this->old_image_meta['sizes'] as $key => $image) {
                    $new_replace_url = $new_url;
                    if (isset($new_size[$key])) {
                        $new_replace_url = $base_url . $new_size[$key]['file'];
                    }
                    $replace = array(
                        'search' => $base_url . $image['file'],
                        'replace' => $new_replace_url,
                    );
                    $this->replace_items[] = $replace;
                }
            }
        }

        if (!empty($this->replace_items)) {
            $replace_items = array();
            foreach ($this->replace_items as $args) {
                if ($args['search'] != $args['replace']) {
                    $replace_items[] = $args;
                }
            }
            $this->replace_items = $replace_items;
            $this->replaceURL();
        }
    }

    /**
     * Replace the collected URLs in post content, options and meta tables,
     * then clear caches when a cache-clearing helper is available.
     *
     * @since 2.6.3
     *
     * @return void
     */
    function replaceURL()
    {
        /* check in post content */
        $this->checkInPostContent();

        /* check in options */
        $this->checkInOptions();

        /* check in meta */
        $this->checkInMetaData();

        if (function_exists('folders_clear_all_caches')) {
            folders_clear_all_caches();
        }
    }

    /**
     * Replace the collected URLs in the `post_content` column of the posts table.
     *
     * @since 2.6.3
     *
     * @return void
     */
    function checkInPostContent()
    {
        global $wpdb;
        $post_table = $wpdb->prefix . "posts";
        if (!empty($this->replace_items)) {
            $query = "SELECT ID, post_content FROM {$post_table} WHERE post_content LIKE %s";
            $update_query = "UPDATE {$post_table} SET post_content = %s WHERE ID = %d";
            foreach ($this->replace_items as $args) {
                if ($args['search'] != $args['replace']) {
                    $sql_query = $wpdb->prepare($query, "%" . $args['search'] . "%");
                    $results = $wpdb->get_results($sql_query, ARRAY_A);
                    if (!empty($results)) {
                        foreach ($results as $row) {
                            $content = $this->findAndReplaceContent($row['post_content'], $args['search'], $args['replace']);
                            $update_post_query = $wpdb->prepare($update_query, $content, $row['ID']);
                            $result = $wpdb->query($update_post_query);
                        }
                    }
                }
            }
        }
    }

    /**
     * Replace a string inside content that may be plain text, serialized data or JSON.
     *
     * Serialized and JSON values are decoded, searched recursively (array keys
     * included) and encoded again in the same format. Forked from Enable Media
     * Replace.
     *
     * @since 2.6.3
     *
     * @param mixed  $content Content to search.
     * @param string $search  String to find.
     * @param string $replace Replacement string.
     * @param bool   $depth   False for the top-level call; true for recursive calls.
     * @return mixed Content with replacements applied.
     */
    function findAndReplaceContent($content, $search, $replace, $depth = false)
    {
        if ($depth === false && is_serialized($content)) {
            $unserialized = @unserialize(trim($content), array('allowed_classes' => false));
            if ($unserialized !== false || trim($content) === 'b:0;') {
                $content = $unserialized;
            }
        }

        // Checking for JSON Data
        $isJson = $this->isJSON($content);
        if ($isJson) {
            $content = json_decode($content);
        }

        // Replace content if content is String
        if (is_string($content)) {
            $content = str_replace($search, $replace, $content);
        } else if (is_wp_error($content)) {   // Return if error in data

        } else if (is_array($content)) {   // Replace content if content is Array
            foreach ($content as $index => $value) {
                $content[$index] = $this->findAndReplaceContent($value, $search, $replace, true);
                if (is_string($index)) {
                    $index_replaced = $this->findAndReplaceContent($index, $search, $replace, true);
                    if ($index_replaced !== $index)
                        $content = $this->changeArrayKey($content, array($index => $index_replaced));
                }
            }
        } else if (is_object($content)) {   // Replace content if content is Object
            foreach ($content as $key => $value) {
                $content->{$key} = $this->findAndReplaceContent($value, $search, $replace, true);
            }
        }

        if ($isJson && $depth === false) {
            $content = wp_json_encode($content, JSON_UNESCAPED_SLASHES);
        } else if ($depth === false && (is_array($content) || is_object($content))) {
            $content = maybe_serialize($content);
        }

        return $content;
    }

    /**
     * Check whether a string is JSON that decodes to something other than itself.
     *
     * Forked from Enable Media Replace.
     *
     * @since 2.6.3
     *
     * @param mixed $content Value to check.
     * @return bool True for a JSON string.
     */
    function isJSON($content)
    {
        if (is_array($content) || is_object($content))
            return false;

        $json = json_decode($content);
        return $json && $json != $content;
    }

    /**
     * Recursively rename array keys.
     *
     * @since 2.6.3
     *
     * @param array $array Array to update.
     * @param array $set   Map of old key => new key.
     * @return array Array with renamed keys.
     */
    function changeArrayKey($array, $set)
    {
        if (is_array($array) && is_array($set)) {
            $newArray = array();
            foreach ($array as $k => $v) {
                $key = array_key_exists($k, $set) ? $set[$k] : $k;
                $newArray[$key] = is_array($v) ? $this->changeArrayKey($v, $set) : $v;
            }
            return $newArray;
        }
        return $array;
    }

    /**
     * Replace the collected URLs in the options table.
     *
     * @since 2.6.3
     *
     * @return void
     */
    function checkInOptions()
    {
        global $wpdb;
        $post_table = $wpdb->prefix . "options";
        if (!empty($this->replace_items)) {
            $query = "SELECT option_id, option_value FROM {$post_table} WHERE option_value LIKE %s";
            $update_query = "UPDATE {$post_table} SET option_value = %s WHERE option_id = %d";
            foreach ($this->replace_items as $args) {
                if ($args['search'] != $args['replace']) {
                    $sql_query = $wpdb->prepare($query, "%" . $args['search'] . "%");
                    $results = $wpdb->get_results($sql_query, ARRAY_A);
                    if (!empty($results)) {
                        foreach ($results as $row) {
                            $content = $this->findAndReplaceContent($row['option_value'], $args['search'], $args['replace']);
                            $update_post_query = $wpdb->prepare($update_query, $content, $row['option_id']);
                            $result = $wpdb->query($update_post_query);
                        }
                    }
                }
            }
        }
    }

    /**
     * Replace the collected URLs in the user, term, post and comment meta tables.
     *
     * @since 2.6.3
     *
     * @return void
     */
    function checkInMetaData()
    {
        $tables = array(
            array(
                'table_name' => 'usermeta',
                'primary_key' => 'umeta_id',
                'search_key' => 'meta_value'
            ),
            array(
                'table_name' => 'termmeta',
                'primary_key' => 'meta_id',
                'search_key' => 'meta_value'
            ),
            array(
                'table_name' => 'postmeta',
                'primary_key' => 'meta_id',
                'search_key' => 'meta_value'
            ),
            array(
                'table_name' => 'commentmeta',
                'primary_key' => 'meta_id',
                'search_key' => 'meta_value'
            )
        );
        global $wpdb;
        foreach ($tables as $table) {
            $post_table = $wpdb->prefix . $table['table_name'];
            if (!empty($this->replace_items)) {
                $query = "SELECT {$table['primary_key']}, {$table['search_key']} FROM {$post_table} WHERE {$table['search_key']} LIKE %s";
                $update_query = "UPDATE {$post_table} SET {$table['search_key']} = %s WHERE {$table['primary_key']} = %d";
                foreach ($this->replace_items as $args) {
                    if ($args['search'] != $args['replace']) {
                        $sql_query = $wpdb->prepare($query, "%" . $args['search'] . "%");
                        $results = $wpdb->get_results($sql_query, ARRAY_A);
                        if (!empty($results)) {
                            foreach ($results as $row) {
                                $content = $this->findAndReplaceContent($row[$table['search_key']], $args['search'], $args['replace']);
                                $update_post_query = $wpdb->prepare($update_query, $content, $row[$table['primary_key']]);
                                $result = $wpdb->query($update_post_query);
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Enqueue the script for the "change file name according to title" option in the media modal.
     *
     * @since 2.6.3
     *
     * @return void
     */
    public function replace_media_file_script()
    {
        wp_enqueue_script('folders-media-replace-js', FOLDERS_PLUGIN_URL . 'dist/js/replace-file-name.js', array('jquery'), FOLDERS_VERSION, true);
        wp_localize_script('folders-media-replace-js', 'replace_media_options', array(
            'ajax_url' => admin_url("admin-ajax.php"),
        ));
    }

    /**
     * Output inline CSS for the replace media and rename fields in the media
     * modal, and hide the Replace media page from the Media menu.
     *
     * @return void
     */
    public function premio_replace_file_CSS()
    {
        echo '<style>
        .compat-field-replace_file_name th.label {display: none;}
        .compat-field-replace_file_name td.field {width: 100%; border-top: solid 1px #c0c0c0; padding:10px 0 0 0;margin: 0;float: none;}
        .compat-field-replace_file_name td.field label {width: 100%; display: block;padding:0 0 10px 0;}
        .compat-field-replace_file_name td.field label input[type="checkbox"] {margin: 0 4px 0 2px;}
        .compat-field-replace_file_name td.field button.update-name-with-title {display: none;}
        .compat-field-replace_file_name td.field button.update-name-with-title.show {display: inline-block;}
        
        .compat-field-folders th.label {width: 100%; text-align: left; padding: 0 0 10px 0; margin: 0; border-top: solid 1px #c0c0c0;float: none;}
        .compat-field-folders th.label .alignleft {float: none; text-align: left; font-weight: bold;}
        .compat-field-folders th.label br {display: none;}
        .compat-field-folders td.field {width: 100%; padding: 0; margin: 0;float: none;}
        .folders-undo-notification{position:fixed;right:-500px;bottom:25px;width:280px;background:#fff;padding:15px;-webkit-box-shadow:0 3px 6px -4px rgb(0 0 0 / 12%),0 6px 16px 0 rgb(0 0 0 / 8%),0 9px 28px 8px rgb(0 0 0 / 5%);box-shadow:0 3px 6px -4px rgb(0 0 0 / 12%),0 6px 16px 0 rgb(0 0 0 / 8%),0 9px 28px 8px rgb(0 0 0 / 5%);transition:all .25s linear;z-index:250010}.folders-undo-notification.active{right:25px}.folders-undo-header{font-weight:500;font-size:14px;padding:0 0 3px 0}.folders-undo-body{font-size:13px;padding:0 0 5px 0}.folders-undo-footer{text-align:right;padding:5px 0 0 0}.folders-undo-footer .undo-button{background:#1da1f4;border:none;color:#fff;padding:3px 10px;font-size:12px;border-radius:2px;cursor:pointer}.folders-undo-body{position:relative}.close-undo-box{position:absolute;right:-10px;top:0;width:16px;height:16px;transition:all .25s linear}.close-undo-box:hover{transform:rotate(180deg)}.close-undo-box span{display:block;position:relative;width:16px;height:16px;transition:all .2s linear}.close-undo-box span:after,.close-undo-box span:before{content:"";position:absolute;width:12px;height:2px;background-color:#333;display:block;border-radius:2px;transform:rotate(45deg);top:7px;left:2px}.close-undo-box span:after{transform:rotate(-45deg)}
        .folders-undo-notification.no .folders-undo-header { color: #dd0000; }
        .folders-undo-notification.yes .folders-undo-header { color: #014737; }
        .update-name-with-title .spinner {display: none; visibility: visible; margin-right: 0;}
        .update-name-with-title.in-progress .spinner {display: inline-block;}
        #menu-media a[href="upload.php?page=folders-replace-media"] {display: none !important;}
      </style>';
    }

    /**
     * Enqueue the styles and scripts for the Replace media screen.
     *
     * @since 2.6.3
     *
     * @param string $page Current admin page hook suffix.
     * @return void
     */
    public function folders_admin_css_and_js($page)
    {
        if ($page == "media_page_folders-replace-media" || $page == "admin_page_folders-replace-media") {
            $minified = "";

            wp_enqueue_style('folders-replace-media', FOLDERS_PLUGIN_URL . 'dist/css/replace-media.css', array(), FOLDERS_VERSION);
            wp_enqueue_style('folders-datetimepicker', FOLDERS_PLUGIN_URL . 'dist/css/datetimepicker.css', array(), FOLDERS_VERSION);

            wp_enqueue_script('folders-datetimepicker', FOLDERS_PLUGIN_URL . 'dist/js/datetimepicker.js', array(), FOLDERS_VERSION, true);
            wp_enqueue_script('folders-simpledropit', FOLDERS_PLUGIN_URL . 'dist/js/simpledropit.js', array(), FOLDERS_VERSION, true);
            wp_enqueue_script('folders-replace-media', FOLDERS_PLUGIN_URL . 'dist/js/replace-media.js', array(), FOLDERS_VERSION, true);

            $maxUploadSize = ini_get("upload_max_filesize");
            $maxUploadSize = str_replace(["K", "M", "G", "T", "P"], [" KB", " MB", " GB", " TB", " PB"], $maxUploadSize);
            $maxSize = sprintf(esc_html__("Maximum file size %1\$s", 'folders'), $maxUploadSize);
            wp_localize_script('folders-simpledropit', 'replace_settings', [
                'max_size' => $maxSize,
                'file_name' => esc_html__("File name", 'folders'),
                'file_size' => esc_html__("Size", 'folders'),
                'file_type' => esc_html__("Type", 'folders'),
                'dimension' => esc_html__("Dimension", 'folders'),
                'drag_file' => esc_html__("Drag and drop files here", 'folders'),
            ]);
//            wp_enqueue_script('jquery-ui-datepicker');
        }
    }

    /**
     * Register the hidden Replace media page under Media.
     *
     * @since 2.6.3
     *
     * @return void
     */
    public function admin_menu()
    {
        add_submenu_page('upload.php',
            esc_html__("Replace media", 'folders'),
            esc_html__("Replace media", 'folders'),
            'upload_files',
            'folders-replace-media',
            array($this, 'folders_replace_media')
        );
    }

    /**
     * Render the Replace media screen for the attachment in the request.
     *
     * Verifies the `folders-replace-media-{attachment_id}` nonce and exits with
     * an error message when the nonce or attachment is invalid.
     *
     * @since 2.6.3
     *
     * @return void
     */
    public function folders_replace_media()
    {
        global $plugin_page;
        $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : '';
        $attachment_id = isset($_GET['attachment_id']) ? sanitize_text_field($_GET['attachment_id']) : '';
        $nonce = isset($_GET['nonce']) ? sanitize_text_field($_GET['nonce']) : '';
        if (!wp_verify_nonce($nonce, "folders-replace-media-" . $attachment_id)) {
            echo 'Invalid Nonce';
            exit;
        }
        $attachment = get_post($attachment_id);
        if (empty($attachment) || !isset($attachment->guid)) {
            echo 'Invalid URL';
            exit;
        }
        $guid = $attachment->guid;
        $guid = explode(".", $guid);
        if ($guid == $attachment->guid) {
            echo 'Invalid URL';
            exit;
        }
        $form_action = $this->getMediaReplaceURL($attachment_id);
        include_once FOLDERS_TEMPLATE_DIR . 'folders/media-replace.php';
    }

    /**
     * Get the URL of the Replace media screen for an attachment, including its nonce.
     *
     * @since 2.6.3
     *
     * @param int $attach_id Attachment ID.
     * @return string Admin URL.
     */
    public function getMediaReplaceURL($attach_id)
    {
        $url = admin_url("upload.php");
        $url = add_query_arg(array(
            'page' => 'folders-replace-media',
            'action' => 'folders_replace_media',
            'attachment_id' => $attach_id,
            'nonce' => wp_create_nonce("folders-replace-media-" . $attach_id)
        ), $url);

        return $url;
    }

    /**
     * Add a "Replace media" row action in the Media Library list view.
     *
     * Only added for users with the `upload_files` capability.
     *
     * @since 2.6.3
     *
     * @param array    $actions Row actions.
     * @param \WP_Post $post    Attachment post.
     * @return array|null Modified row actions, or null when the feature is disabled.
     */
    public function add_media_action($actions, $post)
    {
        if (!$this->is_enabled) {
            return;
        }
        if (current_user_can("upload_files")) {
            $link = $this->getMediaReplaceURL($post->ID);
            $newaction['replace_media'] = '<a style="color: ' . esc_attr($this->button_color) . '" href="' . esc_url($link) . '" rel="permalink">' . esc_html__("Replace media", 'folders') . '</a>';
            return array_merge($actions, $newaction);
        }
        return $actions;
    }

    /**
     * Output the "Replace media" button in the attachment edit screen's meta box.
     *
     * Only shown for images and to users with the `upload_files` capability.
     *
     * @since 2.6.3
     *
     * @param \WP_Post $post Attachment post.
     * @return void
     */
    public function replace_meta_box($post)
    {
        if (current_user_can("upload_files")) {
            if (wp_attachment_is('image', $post->ID)) {
                $link = $this->getMediaReplaceURL($post->ID);
                echo "<p><a style='background: " . esc_attr($this->button_color) . "; border-color: " . esc_attr($this->button_color) . "; color:#ffffff;border-radius: 4px;' href='" . esc_url($link) . "' class='button-secondary'>" . esc_html__("Upload a new file", 'folders') . "</a></p><p>" . esc_html__("Click on the button to replace the file with another file", 'folders') . "</p>";
            }
        }
    }

    /**
     * Output the "Change file name according to title" checkbox meta box on the attachment edit screen.
     *
     * @since 2.6.3
     *
     * @param \WP_Post $post Attachment post.
     * @return void
     */
    public function change_file_name_box($post)
    {
        if (current_user_can("upload_files")) { ?>
            <p>
                <input type="hidden" name="premio_change_nonce"
                       value="<?php echo esc_attr(wp_create_nonce("premio_change_file_name_" . $post->ID)) ?>">
                <input type="hidden" name="premio_change_file_name" value="no">
                <label for="change_file_name"><input <?php checked($this->replace_media_title, "on") ?> type="checkbox"
                                                                                                        id="change_file_name"
                                                                                                        name="premio_change_file_name"
                                                                                                        value="yes"> <?php esc_html_e("Change file name according to title", 'folders') ?>
                </label>
            </p>
            <?php
        }
    }

    /**
     * Add the "Replace media" field to the attachment details in the media modal.
     *
     * Not shown on the full attachment edit screen, which uses the meta box instead.
     *
     * @since 2.6.3
     *
     * @param array    $form_fields Attachment form fields.
     * @param \WP_Post $post        Attachment post.
     * @return array Modified form fields.
     */
    public function attachment_editor($form_fields, $post)
    {
        $screen = null;
        if (function_exists('get_current_screen')) {
            $screen = get_current_screen();

            if (!is_null($screen) && $screen->id == 'attachment') // hide on edit attachment screen.
                return $form_fields;
        }

        if (current_user_can("upload_files")) {
            $link = $this->getMediaReplaceURL($post->ID);
            if (wp_attachment_is('image', $post->ID)) {
                $form_fields["folders"] = array(
                    "label" => esc_html__("Replace media", 'folders'),
                    "input" => "html",
                    "html" => "<a style='background: " . esc_attr($this->button_color) . "; border-color: " . esc_attr($this->button_color) . "; color:#ffffff;border-radius: 4px;' href='" . $link . "' class='button-secondary'>" . esc_html__("Upload a new file", 'folders') . "</a>", "helps" => esc_html__("Click on the button to replace the file with another file", 'folders')
                );
            } else {
                $form_fields["folders"] = [
                    "label" => esc_html__("Replace media", "folders"),
                    "input" => "html",
                    "html" => "<div style='border: solid 1px #c0c0c0; padding: 10px; border-radius: 2px; background: #ececec;'><a style='color: " . esc_attr($this->button_color) . "; font-weight: 500; border-radius: 4px;' target='_blank' href='" . esc_url($this->upgradeLink) . "' >" . esc_html__("Upgrade to Pro", "folders") . "</a> " . esc_html__("to replace media files other than images", "folders") . "</div>",
                    "helps" => esc_html__("Click on the button to replace the file with another file", "folders"),
                ];
            }
        }

        return $form_fields;
    }

    /**
     * Add the "change file name according to title" field to the attachment details in the media modal.
     *
     * Not shown on the full attachment edit screen, which uses the meta box instead.
     *
     * @since 2.6.3
     *
     * @param array    $form_fields Attachment form fields.
     * @param \WP_Post $post        Attachment post.
     * @return array Modified form fields.
     */
    public function attachment_replace_name_with_title($form_fields, $post)
    {
        $screen = null;
        if (function_exists('get_current_screen')) {
            $screen = get_current_screen();

            if (!is_null($screen) && $screen->id == 'attachment') // hide on edit attachment screen.
                return $form_fields;
        }

        if(current_user_can("upload_files") && current_user_can('edit_post', $post->ID)) {
            $form_fields["replace_file_name"] = array(
                "label" => esc_html__("Replace media", "folders"),
                "input" => "html",
                "html" => "<label for='attachment_title_" . esc_attr($post->ID) . "' data-post='" . esc_attr($post->ID) . "' data-nonce='" . wp_create_nonce('change_attachment_title_' . $post->ID) . "'><input id='attachment_title_" . esc_attr($post->ID) . "' type='checkbox' class='folder-replace-checkbox' value='" . esc_attr($post->ID) . "'>" . esc_html__("Update file name with title", 'folders') . "</label><a href='" . $this->upgradeLink . "' target='_blank' style='background: " . esc_attr($this->button_color) . "; border-color: " . esc_attr($this->button_color) . "; color:#ffffff;border-radius:4px;display:inline-flex;align-items:center;gap:6px;font-size:14px;' type='button' class='button update-name-with-title' >".'<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M1.7733 13.1104C1.39311 10.6392 1.01292 8.16797 0.632733 5.69672C0.548421 5.14891 1.17173 4.77528 1.61511 5.10784C2.79961 5.99622 3.98405 6.88453 5.16855 7.77291C5.55855 8.06541 6.11417 7.97022 6.38455 7.56459L9.34286 3.12709C9.65548 2.65816 10.3445 2.65816 10.6571 3.12709L13.6154 7.56459C13.8858 7.97022 14.4414 8.06534 14.8314 7.77291C16.0159 6.88453 17.2004 5.99622 18.3849 5.10784C18.8282 4.77528 19.4515 5.14891 19.3673 5.69672C18.9871 8.16797 18.6069 10.6392 18.2267 13.1104H1.7733Z" fill="currentColor"/> <path d="M17.369 17.2246H2.63125C2.1575 17.2246 1.77344 16.8405 1.77344 16.3668V14.4824H18.2269V16.3668C18.2268 16.8405 17.8428 17.2246 17.369 17.2246Z" fill="currentColor"/> </svg>'. esc_html__("Upgrade to Pro", "folders") . "</a>",
                "helps" => ""
            );
        }

        return $form_fields;
    }

    /**
     * Get an attachment's file size formatted as B, KB or MB (1 KB = 1000 bytes).
     *
     * @since 2.6.3
     *
     * @param int $attachment_id Attachment ID.
     * @return string Formatted file size.
     */
    public function getFileSize($attachment_id)
    {
//        $file = get_post_meta( $attachment_id, '_wp_attached_file', true );
//        echo "<pre>"; print_r($file); die;
//        echo get_attached_file( $attachment_id ); die;
        $size = filesize(get_attached_file($attachment_id));
        if ($size > 1000000) {
            $size = ($size / 1000000);
            return number_format((float)$size, 2, ".", ",") . " MB";
        } else if ($size > 1000) {
            $size = ($size / 1000);
            return number_format((float)$size, 2, ".", ",") . " KB";
        }
        return $size . " B";
    }

    /**
     * Replace an attachment's file with the file uploaded from the Replace media screen.
     *
     * Runs on `admin_init` when `$_FILES['new_media_file']` is present. Checks
     * the nonce, the user's permission to edit the attachment and upload files,
     * and the file type (sanitizing SVG files when possible). The new file keeps
     * the old file name with the new extension, the old file and its image
     * sizes are removed, metadata is regenerated, URLs are replaced in the
     * database, and the user is redirected to the attachment edit screen.
     *
     * @since 2.6.3
     *
     * @return void
     */
    public function handle_folders_file_upload()
    {
        global $wpdb;
        if (isset($_FILES['new_media_file'])) {
            if ($_FILES['new_media_file']['error'] == 0) {
                $attachment_id = isset($_GET['attachment_id']) ? sanitize_text_field($_GET['attachment_id']) : '';
                $nonce = isset($_GET['nonce']) ? sanitize_text_field($_GET['nonce']) : '';
                if (!wp_verify_nonce($nonce, "folders-replace-media-" . $attachment_id)) {
                    return;
                }
                $attachment = get_post($attachment_id);
                if (empty($attachment) || !isset($attachment->guid)) {
                    return;
                } 

                          
                // Security: Check if current user has permission to edit this attachment
                if (!current_user_can('edit_post', $attachment_id) || !current_user_can('upload_files')) {
                    wp_die(esc_html__("Sorry, you don't have permission to replace this media file.", 'folders'));
                }
 
                $attachment_url = $attachment->guid;
                $url = wp_get_attachment_url($attachment_id);
                if (!empty($url)) {
                    $attachment_url = $url;
                }
                $guid = explode(".", $attachment_url);
                $guid = array_pop($guid);
                if ($guid == $attachment->guid) {
                    return;
                }

                $replacement_option = "replace_only_file";

                $this->attachment_id = $attachment_id;

                $file = $_FILES['new_media_file'];
                $file_name = $file['name'];
                $file_ext = explode(".", $file_name);
                $file_ext = array_pop($file_ext);
                $ext = strtolower($file_ext);

                $wpmime = get_allowed_mime_types();

                if (!current_user_can("upload_files")) {
                    wp_die(esc_html__("You have not permission to upload files", 'folders'));
                }
                // Security: validate the real file type (extension + content), never trust the client-supplied MIME type.
                $wp_filetype = wp_check_filetype_and_ext($file['tmp_name'], $file_name, $wpmime);
                if (empty($wp_filetype['ext']) || empty($wp_filetype['type']) || !wp_match_mime_types($wp_filetype['type'], $wpmime)) {
                    wp_die(esc_html__("Sorry, this file type is not permitted for security reasons", 'folders'));
                }
                $ext = $file_ext = strtolower($wp_filetype['ext']);
                $file['type'] = $wp_filetype['type'];

                if (($file_ext == "svg" || $file['type'] == 'image/svg+xml') && function_exists('sanitizeSvgFileContent')) {
                    $status = sanitizeSvgFileContent($file['tmp_name']);
                    if (!$status) {
                        wp_die(esc_html__("Sorry, this file type is not permitted for security reasons", 'folders'));
                    }
                }

                if (wp_attachment_is('image', $attachment_id)) {
                    $this->is_old_image = 1;
                }
                $this->old_file_url = $attachment_url;
                $this->old_image_meta = wp_get_attachment_metadata($attachment_id);

                $new_file = $file['tmp_name'];

                $file_parts = pathinfo($attachment_url);

                $db_file_name = $file_parts['basename'];
                $db_file_array = explode(".", $db_file_name);
                array_pop($db_file_array);
                $db_file_name = implode(".", $db_file_array) . ".";
                $db_file_name .= $file_ext;

                $wp_upload_path = wp_get_upload_dir();

                $base_path = $old_path = $wp_upload_path['basedir'] . DIRECTORY_SEPARATOR;
                $baseurl = $old_url = $wp_upload_path['baseurl'] . "/";

                $post_upload = "";

                $wp_attached_file = get_post_meta($attachment_id, "_wp_attached_file", true);
                if ($wp_attached_file !== false) {
                    $old_file_name = explode("/", $wp_attached_file);
                    array_pop($old_file_name);

                    if (count($old_file_name) > 0) {
                        $old_path .= implode(DIRECTORY_SEPARATOR, $old_file_name);
                        $old_url .= implode("/", $old_file_name);
                    }

                    if (count($old_file_name) > 0) {
                        $baseurl .= implode(DIRECTORY_SEPARATOR, $old_file_name);
                        $base_path .= implode("/", $old_file_name);

                        $post_upload = implode("/", $old_file_name);
                    }
                }

                $upload_dir = array();
                $upload_dir['path'] = $base_path;
                $upload_dir['old_path'] = $old_path;
                $upload_dir['url'] = $baseurl;
                $upload_dir['old_url'] = $old_url;

                $this->upload_dir = $upload_dir;

                $this->old_file_path = $old_path . "/" . $file_parts['basename'];

                if (!is_dir($base_path)) {
                    wp_mkdir_p($base_path);
                }

                if (is_dir($base_path)) {

                    $file_array = explode(".", $file['name']);
                    array_pop($file_array);
                    $new_file_name = sanitize_title(implode(".", $file_array)) . "." . $file_ext;
                    if ($replacement_option == "replace_only_file") {
                        $new_file_name = $db_file_name;
                    }

                    if (strtolower($new_file_name) != strtolower($file_parts['basename'])) {
                        $new_file_name = $this->checkForFileName($new_file_name, $base_path . DIRECTORY_SEPARATOR);
                    }

                    $this->new_file_path = $base_path . DIRECTORY_SEPARATOR . $new_file_name;

                    $status = move_uploaded_file($new_file, $this->new_file_path);

                    $this->new_file_url = trim($baseurl, "/") . "/" . $new_file_name;

                    if ($status) {
                        $old_file_path = str_replace(array("/", DIRECTORY_SEPARATOR), array("", ""), $this->old_file_path);
                        $new_file_path = str_replace(array("/", DIRECTORY_SEPARATOR), array("", ""), $this->new_file_path);
                        if ($old_file_path != $new_file_path) {
                            if (file_exists($this->old_file_path)) {
                                @wp_delete_file($this->old_file_path);
                            }
                        }

                        update_attached_file($attachment->ID, $this->new_file_url);

                        $update_array = array();
                        $update_array['ID'] = $attachment->ID;
                        $update_array['guid'] = $this->new_file_url; //wp_get_attachment_url($this->post_id);
                        $update_array['post_mime_type'] = $file['type'];

                        $current_date = gmdate("Y-m-d H:i:s");
                        $current_date_gmt = date_i18n("Y-m-d H:i:s", strtotime($current_date));
                        $update_array['post_modified'] = $current_date;
                        $update_array['post_modified_gmt'] = $current_date_gmt;
                        $post_id = wp_update_post($update_array, true);

                        update_post_meta($attachment_id, '_wp_attached_file', trim(trim($post_upload, "/") . "/" . $new_file_name, "/"));

                        // update post doesn't update GUID on updates.
                        $wpdb->update($wpdb->posts, array('guid' => $this->new_file_url), array('ID' => $attachment->ID));

                        $this->removeThumbImages();

                        $metadata = wp_generate_attachment_metadata($attachment->ID, $this->new_file_path);
                        wp_update_attachment_metadata($attachment->ID, $metadata);

                        $this->new_image_meta = wp_get_attachment_metadata($attachment_id);
  
                        $this->searchAndReplace();

                        delete_post_meta($attachment_id, "folders_file_replaced");
                        add_post_meta($attachment_id, "folders_file_replaced", time(), true);

                        wp_redirect(admin_url("post.php?post=" . $attachment_id . "&action=edit&premio_message=success&image_update=1"));
                        exit;
                    } else {
                        wp_die("Error during uploading file");
                    }

                } else {
                    wp_die("Permission issue, Unable to create directory");
                }
            }
        }
    }

    /**
     * Validate Path
     *
     * Validates the given folder path.
     *
     * @param string $folderPath The folder path to be validated.
     *
     * @return void
     */
    public function validate_path($folderPath)
    {
        $this->validatePathLength($folderPath);

        $path = explode("/", $folderPath);

        if (isset($path[0]) && isset($path[1])) {
            $this->validatePathSegments($path);
            $this->validateDirectory($folderPath);
        } else {
            $this->pathNotValid();
        }
    }

    /**
     * Validate the length of a given path.
     *
     * @param string $path The path to be validated.
     *
     * @return void
     */
    private function validatePathLength($path)
    {
        if (strlen($path) != 7) {
            $this->pathNotValid();
        }
    }

    /**
     * Display error message when the path is not valid
     *
     * @access private
     * @return void
     * @since 2.8.4
     *
     */
    private function pathNotValid()
    {
        wp_die(esc_html__("Path is not valid", 'folders'));
    }

    /**
     * Validate Path Segments
     *
     * This method validates the path segments provided as input.
     * It checks if the first and second segments are numeric and have a specific length.
     * If the segments are not valid, the method calls the 'pathNotValid' method.
     *
     * @param array $segments The path segments to be validated.
     *
     * @return void
     */
    private function validatePathSegments($segments)
    {
        if (!is_numeric($segments[0]) || !is_numeric($segments[1])
            || strlen($segments[0]) != 4 || strlen($segments[1]) != 2) {
            $this->pathNotValid();
        }
    }

    /**
     * Validate Directory
     *
     * Checks if the provided directory path is valid by checking if it exists in the upload directory.
     *
     * @param string $folderPath The relative path of the directory to validate.
     *
     * @return void
     */
    private function validateDirectory($folderPath)
    {
        $wp_upload_path = wp_get_upload_dir();
        $baseDir = $wp_upload_path['basedir'] . DIRECTORY_SEPARATOR;
        $baseDir .= sanitize_text_field($folderPath);

        if (!is_dir($baseDir)) {
            $this->pathNotValid();
        }
    }

    /**
     * Check whether a string is a valid date in the given format.
     *
     * @since 2.6.3
     *
     * @param string $date   Date string.
     * @param string $format Expected format for DateTime::createFromFormat().
     * @return bool True when the date is valid and matches the format exactly.
     */
    function validate_date($date, $format = 'Y-m-d')
    {
        $d = \DateTime::createFromFormat($format, $date);
        // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
        return $d && $d->format($format) === $date;
    }
}

//$MediaReplace = new MediaReplace();
