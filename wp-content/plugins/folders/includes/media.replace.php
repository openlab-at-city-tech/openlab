<?php
/**
 * Class Folders Replace Media
 *
 * @author  : Premio <contact@premio.io>
 * @license : GPL2
 * */

if (! defined('ABSPATH')) {
    exit;
}

class folders_replace_media
{

    /**
     * Button Color
     *
     * @var    string    $buttonColor    Replacement Button Color
     * @since  1.0.0
     * @access public
     */
    public $buttonColor;

    /**
     * Is Replacement functionality enabled or not
     *
     * @var    string    $isEnabled    Replacement Functionality Status
     * @since  1.0.0
     * @access public
     */
    public $isEnabled = false;

    /**
     * Folders Upgrade Link
     *
     * @var    string    $upgradeLink    Upgrade Link
     * @since  1.0.0
     * @access public
     */
    public $upgradeLink;

    /**
     * Old file path
     *
     * @var    string    $oldFilePath    Old File Path
     * @since  1.0.0
     * @access public
     */
    public $oldFilePath;

    /**
     * Old file URL
     *
     * @var    string    $oldFilePath    Old File URL
     * @since  1.0.0
     * @access public
     */
    public $oldFileURL;

    /**
     * New file Path
     *
     * @var    string    $newFilePath    New File Path
     * @since  1.0.0
     * @access public
     */
    public $newFilePath;

    /**
     * New file URL
     *
     * @var    string    $newFilePath    New File URL
     * @since  1.0.0
     * @access public
     */
    public $newFileURL;

    /**
     * Mode for file Replacement
     *
     * @var    string    $mode    New File URL
     * @since  1.0.0
     * @access public
     */
    public $mode = "rename-file";

    /**
     * Old image Meta Array
     *
     * @var    array    $oldImageMeta    Old image Meta Array
     * @since  1.0.0
     * @access public
     */
    public $oldImageMeta;

    /**
     * New image Meta Array
     *
     * @var    array    $newImageMeta    New image Meta Array
     * @since  1.0.0
     * @access public
     */
    public $newImageMeta;

    /**
     * WordPress Upload Dir Path
     *
     * @var    string    $uploadDir    WordPress Upload Dir Path
     * @since  1.0.0
     * @access public
     */
    public $uploadDir;

    /**
     * Check if old file is image
     *
     * @var    string    $isOldImage    Old file status
     * @since  1.0.0
     * @access public
     */
    public $isOldImage = 0;

    /**
     * Check if uploaded file is image
     *
     * @var    string    $isNewImage    New file status
     * @since  1.0.0
     * @access public
     */
    public $isNewImage = 0;

    /**
     * Attachment ID for Replacement file
     *
     * @var    string    $attachment_id    Attachment ID
     * @since  1.0.0
     * @access public
     */
    public $attachment_id;

    /**
     * Collection of Replacement Items
     *
     * @var    array    $replaceItems    Replacement Items
     * @since  1.0.0
     * @access public
     */
    public $replaceItems = [];


    /**
     * Define the core functionality of the replacement functionality.
     *
     * Set Button Color
     * Check for Functionality is enabled or not
     * Show Replacement form, Success message
     *
     * @since 1.0.0
     */
    function __construct()
    {

        $customizeFolders = get_option('customize_folders');

        $this->buttonColor = isset($customizeFolders['media_replace_button']) ? $customizeFolders['media_replace_button'] : "#FA166B";

        $this->isEnabled = isset($customizeFolders['folders_enable_replace_media']) ? $customizeFolders['folders_enable_replace_media'] : "yes";

        $this->isEnabled = ($this->isEnabled == "yes") ? true : false;

        if ($this->isEnabled) {
            add_action('admin_menu', [$this, 'admin_menu']);

            add_filter('media_row_actions', [$this, 'add_media_action'], 10, 2);

            add_action(
                'add_meta_boxes',
                function () {
                    add_meta_box('folders-replace-box', esc_html__('Replace Media', 'folders'), [$this, 'replace_meta_box'], 'attachment', 'side', 'low');
                }
            );
            add_filter('attachment_fields_to_edit', [$this, 'attachment_editor'], 10, 2);

            add_action('admin_enqueue_scripts', [$this, 'folders_admin_css_and_js']);

            add_action('admin_init', [$this, 'handle_folders_file_upload']);
        }

        $customizeFolders = get_option("customize_folders");
        if (isset($customizeFolders['show_folder_in_settings']) && $customizeFolders['show_folder_in_settings'] == "yes") {
            $this->upgradeLink = admin_url("options-general.php?page=wcp_folders_settings&setting_page=upgrade-to-pro");
        } else {
            $this->upgradeLink = admin_url("admin.php?page=folders-upgrade-to-pro");
        }

        // to replace file name
        add_action(
            'add_meta_boxes',
            function () {
                add_meta_box('folders-replace-file-name', esc_html__('Change file name', 'folders'), [$this, 'change_file_name_box'], 'attachment', 'side', 'core');
            }
        );

        add_filter('attachment_fields_to_edit', [$this, 'attachment_replace_name_with_title'], 10, 2);

        add_action('admin_head', [$this, 'premio_replace_file_CSS']);

        add_action('wp_enqueue_media', [$this, 'replace_media_file_script']);

        add_action('admin_notices', [$this, 'admin_notices']);

    }//end __construct()


    /**
     * Add admin notice
     *
     * @since  2.6.3
     * @access public
     */
    public function admin_notices()
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
                    <a href="javascript:;" class="close-undo-box"><span></span></a>
                    <div class="folders-undo-header"><?php esc_html_e('File successfully replaced', 'folders'); ?></div>
                    <div class="folders-undo-body" style="padding:0"><?php esc_html_e('The file has been successfully replaced using the file replacement feature', 'folders'); ?></div>
                </div>
            </div>
            <script>
                jQuery(document).ready(function(){
                    jQuery("#media-success").addClass("active");
                    setTimeout(function(){
                        jQuery("#media-success").removeClass("active");
                    }, 5000);

                    jQuery(document).on("click", ".close-undo-box", function(){
                        jQuery("#media-success").removeClass("active");
                    });
                });
            </script>
        <?php }//end if

    }//end admin_notices()


    /**
     * Add Metabox to replace file name with title
     *
     * @since  2.6.3
     * @access public
     */
    public function change_file_name_box($post)
    {
        ?>
        <p class="upgrade-bottom">
            <label for="change_file_name"><input disabled type="checkbox" id="change_file_name" name="premio_change_file_name" value="yes"> <?php esc_html_e("Change file name according to title", "folders") ?></label>
        </p>
        <div class="upgrade-box">
            <a href="<?php echo esc_url($this->upgradeLink) ?>" target="_blank"><?php esc_html_e("Upgrade to Pro", "folders"); ?></a>
        </div>
        <?php

    }//end change_file_name_box()


    /**
     * Add Js and CSS files for replace file name with title
     *
     * @since  2.6.3
     * @access public
     */
    public function replace_media_file_script()
    {
        wp_enqueue_script('folders-replace-media', WCP_FOLDER_URL.'assets/js/replace-file-name.js', ['jquery'], WCP_FOLDER_VERSION, true);
        wp_localize_script(
            'folders-replace-media',
            'replace_media_options',
            [
                'ajax_url' => admin_url("admin-ajax.php"),
            ]
        );

    }//end replace_media_file_script()


    /**
     * Add inline CSS code for file replacement
     *
     * @since  2.6.3
     * @access public
     */
    public function premio_replace_file_CSS()
    {
        ?>
        <style>
            .compat-field-replace_file_name th.label {display: none;}
            .compat-field-replace_file_name td.field {width: 100%; border-top: solid 1px #c0c0c0; padding:10px 0 0 0;margin: 0;float: none;}
            .compat-field-replace_file_name td.field label {width: 100%; display: block;padding:0 0 10px 0;}
            .compat-field-replace_file_name td.field label input[type="checkbox"] {margin: 0 4px 0 2px;}
            .compat-field-replace_file_name td.field a.update-name-with-title {display: none;}
            .compat-field-replace_file_name td.field a.update-name-with-title.show {display: inline-block;}

            .compat-field-folders th.label {width: 100%; text-align: left; padding: 0 0 10px 0; margin: 0; border-top: solid 1px #c0c0c0;float: none;}
            .compat-field-folders th.label .alignleft {float: none; text-align: left; font-weight: bold;}
            .compat-field-folders th.label br {display: none;}
            .compat-field-folders td.field {width: 100%; padding: 0; margin: 0;float: none;}
            .folders-undo-notification{position:fixed;right:-500px;bottom:25px;width:280px;background:#fff;padding:15px;-webkit-box-shadow:0 3px 6px -4px rgb(0 0 0 / 12%),0 6px 16px 0 rgb(0 0 0 / 8%),0 9px 28px 8px rgb(0 0 0 / 5%);box-shadow:0 3px 6px -4px rgb(0 0 0 / 12%),0 6px 16px 0 rgb(0 0 0 / 8%),0 9px 28px 8px rgb(0 0 0 / 5%);transition:all .25s linear;z-index:250010}.folders-undo-notification.active{right:25px}.folders-undo-header{font-weight:500;font-size:14px;padding:0 0 3px 0}.folders-undo-body{font-size:13px;padding:0 0 5px 0}.folders-undo-footer{text-align:right;padding:5px 0 0 0}.folders-undo-footer .undo-button{background:#1da1f4;border:none;color:#fff;padding:3px 10px;font-size:12px;border-radius:2px;cursor:pointer}.folders-undo-body{position:relative}.close-undo-box{position:absolute;right:-10px;top:0;width:16px;height:16px;transition:all .25s linear}.close-undo-box:hover{transform:rotate(180deg)}.close-undo-box span{display:block;position:relative;width:16px;height:16px;transition:all .2s linear}.close-undo-box span:after,.close-undo-box span:before{content:"";position:absolute;width:12px;height:2px;background-color:#333;display:block;border-radius:2px;transform:rotate(45deg);top:7px;left:2px}.close-undo-box span:after{transform:rotate(-45deg)}
            .folders-undo-notification.no .folders-undo-header { color: #dd0000; }
            .folders-undo-notification.yes .folders-undo-header { color: #014737; }
            .update-name-with-title .spinner {display: none; visibility: visible; margin-right: 0;}
            .update-name-with-title.in-progress .spinner {display: inline-block;}

            #folders-replace-file-name .inside {position: relative;padding:0;margin:0}
            #folders-replace-file-name .inside p {padding: 1em; margin: 0;}
            #folders-replace-file-name .upgrade-box {position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.3); z-index: 1;display: none;}
            #folders-replace-file-name:hover .upgrade-box { display: block; }
            #folders-replace-file-name:hover p {filter: blur(1.2px);}
            #folders-replace-file-name:hover .upgrade-box a {display: inline-block; position: absolute; left: 0; right: 0; width: 100px; margin: 0 auto; top: 50%; padding: 5px 10px; text-decoration: none; background: #fa166b; color: #fff; border-radius: 4px; text-align: center; margin-top: -14px;}
        </style> <?php

    }//end premio_replace_file_CSS()


    /**
     * Replace file name with title
     *
     * @since  2.6.3
     * @access public
     */
    public function attachment_replace_name_with_title($formFields, $post)
    {
        $screen = null;
        if (function_exists('get_current_screen')) {
            $screen = get_current_screen();

            if (! is_null($screen) && $screen->id == 'attachment') {
                // hide on edit attachment screen.
                return $formFields;
            }
        }

        $formFields["replace_file_name"] = [
            "label" => esc_html__("Replace media", "folders"),
            "input" => "html",
            "html"  => "<label for='attachment_title_".esc_attr($post->ID)."' data-post='".esc_attr($post->ID)."' data-nonce='".wp_create_nonce('change_attachment_title_'.$post->ID)."'><input id='attachment_title_".esc_attr($post->ID)."' type='checkbox' class='folder-replace-checkbox' value='".esc_attr($post->ID)."'>".esc_html__("Update file name with title")."</label><a href='".$this->upgradeLink."' target='_blank' style='background: ".esc_attr($this->buttonColor)."; border-color: ".esc_attr($this->buttonColor)."; color:#ffffff' type='button' class='button update-name-with-title' >".esc_html__("Upgrade to Pro", "folders")."</a>",
            "helps" => "",
        ];

        return $formFields;

    }//end attachment_replace_name_with_title()


    /**
     * Add Js and CSS files for replace file screen
     *
     * @since  2.6.3
     * @access public
     */
    public function folders_admin_css_and_js($page)
    {
        if ($page == "media_page_folders-replace-media" || $page == "admin_page_folders-replace-media") {
            wp_enqueue_style('folders-media', plugin_dir_url(dirname(__FILE__)).'assets/css/replace-media.css', [], WCP_FOLDER_VERSION);
            wp_enqueue_script('folders-media', plugin_dir_url(dirname(__FILE__)).'assets/js/replace-media.js', [], WCP_FOLDER_VERSION);
        }

    }//end folders_admin_css_and_js()


    /**
     * Add file replace menu in admin
     *
     * @since  2.6.3
     * @access public
     * @return $string
     */
    public function admin_menu()
    {
        add_submenu_page(
            null,
            esc_html__("Replace media", "folders"),
            esc_html__("Replace media", "folders"),
            'upload_files',
            'folders-replace-media',
            [
                $this,
                'folders_replace_media',
            ]
        );

    }//end admin_menu()


    /**
     * Add file replacement screen
     *
     * @since  2.6.3
     * @access public
     * @return $string
     */
    public function folders_replace_media()
    {
        global $plugin_page;
        $action       = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : '';
        $attachment_id = isset($_GET['attachment_id']) ? sanitize_text_field($_GET['attachment_id']) : '';
        $nonce        = isset($_GET['nonce']) ? sanitize_text_field($_GET['nonce']) : '';
        if (!wp_verify_nonce($nonce, "folders-replace-media-".$attachment_id)) {
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
        include_once dirname(dirname(__FILE__)).WCP_DS."/templates".WCP_DS."admin".WCP_DS."media-replace.php";

    }//end folders_replace_media()


    /**
     * Add action for file replacement
     *
     * @since  2.6.3
     * @access public
     * @return $actions
     */
    public function add_media_action($actions, $post)
    {
        if (!$this->isEnabled) {
            return array_merge($actions);
        }

        if (wp_attachment_is('image', $post->ID)) {
            $link = $this->getMediaReplaceURL($post->ID);

            $newaction['replace_media'] = '<a style="color: '.esc_attr($this->buttonColor).'" href="'.esc_url($link).'" rel="permalink">'.esc_html__("Replace media", "folders").'</a>';

            return array_merge($actions, $newaction);
        } else {
            $link = $this->getMediaReplaceURL($post->ID);

            $newaction['replace_media'] = '<a style="color: '.esc_attr($this->buttonColor).'" target="_blank" href="'.esc_url($this->upgradeLink).'" rel="permalink">'.esc_html__("Replace Media 🔑", "folders").'</a>';

            return array_merge($actions, $newaction);
        }

        return $actions;

    }//end add_media_action()


    /**
     * Get URL for file Replacement
     *
     * @since  2.6.3
     * @access public
     * @return $url
     */
    public function getMediaReplaceURL($attach_id)
    {
        $url = admin_url("upload.php");
        $url = add_query_arg(
            [
                'page'         => 'folders-replace-media',
                'action'       => 'folders_replace_media',
                'attachment_id' => $attach_id,
                'nonce'        => wp_create_nonce("folders-replace-media-".$attach_id),
            ],
            $url
        );

        return $url;

    }//end getMediaReplaceURL()


    /**
     * Get file size
     *
     * @since  2.6.3
     * @access public
     * @return $size
     */
    public function replace_meta_box($post)
    {
        if (wp_attachment_is('image', $post->ID)) {
            $link = $this->getMediaReplaceURL($post->ID); ?>
            <p><a style='background: <?php echo esc_attr($this->buttonColor) ?>; border-color: <?php echo esc_attr($this->buttonColor) ?>; color:#ffffff' href='<?php echo esc_url($link) ?>' class='button-secondary'><?php esc_html_e("Upload a new file", "folders") ?></a></p><p><?php esc_html_e("Click on the button to replace the file with another file", "folders") ?></p>
        <?php } else { ?>
            <p><a style='color: <?php echo esc_attr($this->buttonColor) ?>; font-weight: 500' target='_blank' href='<?php echo esc_url($this->upgradeLink) ?>' ><?php esc_html_e("Upgrade to Pro", "folders") ?></a><?php esc_html_e("to replace any kind of files while uploading including pdf/svg/docx/etc & more.", "folders") ?></p>
        <?php }

    }//end replace_meta_box()


    /**
     * Get file size
     *
     * @since  2.6.3
     * @access public
     * @return $size
     */
    public function attachment_editor($formFields, $post)
    {
        $screen = null;
        if (function_exists('get_current_screen')) {
            $screen = get_current_screen();

            if (! is_null($screen) && $screen->id == 'attachment') {
                // hide on edit attachment screen.
                return $formFields;
            }
        }

        if (wp_attachment_is('image', $post->ID)) {
            $link = $this->getMediaReplaceURL($post->ID);
            $formFields["folders"] = [
                "label" => esc_html__("Replace media", "folders"),
                "input" => "html",
                "html"  => "<a style='background: ".esc_attr($this->buttonColor)."; border-color: ".esc_attr($this->buttonColor)."; color:#ffffff' href='".esc_url($link)."' class='button-secondary'>".esc_html__("Upload a new file", "folders")."</a>",
                "helps" => esc_html__("Click on the button to replace the file with another file", "folders"),
            ];
        } else {
            $formFields["folders"] = [
                "label" => esc_html__("Replace media", "folders"),
                "input" => "html",
                "html"  => "<div style='border: solid 1px #c0c0c0; padding: 10px; border-radius: 2px; background: #ececec;'><a style='color: ".esc_attr($this->buttonColor)."; font-weight: 500' target='_blank' href='".esc_url($this->upgradeLink)."' >".esc_html__("Upgrade to Pro", "folders")."</a> ".esc_html__("to replace media files other than images", "folders")."</div>",
                "helps" => esc_html__("Click on the button to replace the file with another file", "folders"),
            ];
        }

        return $formFields;

    }//end attachment_editor()


    /**
     * Get file size
     *
     * @since  2.6.3
     * @access public
     * @return $size
     */
    public function getFileSize($attachment_id)
    {
        $size = filesize(get_attached_file($attachment_id));
        if ($size > 1000000) {
            $size = ($size / 1000000);
            return number_format((float) $size, 2, ".", ",")." MB";
        } else if ($size > 1000) {
            $size = ($size / 1000);
            return number_format((float) $size, 2, ".", ",")." KB";
        }

        return $size." B";

    }//end getFileSize()


    /**
     * Upload file and Replace it
     *
     * @since  2.6.3
     * @access public
     */
    public function handle_folders_file_upload()
    {
        global $wpdb;
        if (isset($_FILES['new_media_file'])) {
            if ($_FILES['new_media_file']['error'] == 0) {
                $attachment_id = sanitize_text_field(filter_input(INPUT_GET, "attachment_id"));
                $nonce        = sanitize_text_field(filter_input(INPUT_GET, "nonce"));
                if (!wp_verify_nonce($nonce, "folders-replace-media-".$attachment_id)) {
                    return;
                }

                $attachment = get_post($attachment_id);
                if (empty($attachment) || !isset($attachment->guid)) {
                    return;
                }

                $attachment_url = $attachment->guid;
                $url            = wp_get_attachment_url($attachment_id);
                if (!empty($url)) {
                    $attachment_url = $url;
                }

                $guid = explode(".", $attachment_url);
                $guid = array_pop($guid);
                if ($guid == $attachment->guid) {
                    return;
                }

                $replacement_option = isset($_REQUEST['replacement_option']) ? sanitize_text_field($_REQUEST['replacement_option']) : "replace_only_file";

                $this->attachment_id = $attachment_id;

                $file      = $_FILES['new_media_file'];
                $file_name = $file['name'];
                $file_ext  = explode(".", $file_name);
                $file_ext  = array_pop($file_ext);

                if (wp_attachment_is('image', $attachment_id)) {
                    $this->isOldImage = 1;
                }

                $this->oldFileURL   = $attachment_url;
                $this->oldImageMeta = wp_get_attachment_metadata($attachment_id);

                $new_file = $file['tmp_name'];

                $file_parts = pathinfo($attachment_url);

                $db_file_name  = $file_parts['basename'];
                $db_file_array = explode(".", $db_file_name);
                array_pop($db_file_array);
                $db_file_name  = implode(".", $db_file_array).".";
                $db_file_name .= $file_ext;

                $wp_upload_path = wp_get_upload_dir();

                $base_path = $old_path = $wp_upload_path['basedir'].DIRECTORY_SEPARATOR;
                $baseurl   = $old_url = $wp_upload_path['baseurl']."/";

                $post_upload = "";

                $wp_attached_file = get_post_meta($attachment_id, "_wp_attached_file", true);
                if ($wp_attached_file !== false) {
                    $old_file_name = explode("/", $wp_attached_file);
                    array_pop($old_file_name);

                    if (count($old_file_name) > 0) {
                        $old_path .= implode(DIRECTORY_SEPARATOR, $old_file_name);
                        $old_url  .= implode("/", $old_file_name);
                    }

                    if ($replacement_option == "replace_file_with_name" && isset($_REQUEST['new_folder_option']) && $_REQUEST['new_folder_option'] && isset($_REQUEST['new_folder_path']) && !empty($_REQUEST['new_folder_path'])) {
                        $baseurl    .= sanitize_text_field($_REQUEST['new_folder_path']);
                        $base_path  .= str_replace("/", DIRECTORY_SEPARATOR, sanitize_text_field($_REQUEST['new_folder_path']));
                        $post_upload = sanitize_text_field($_REQUEST['new_folder_path']);
                    } else if (count($old_file_name) > 0) {
                        $baseurl    .= implode(DIRECTORY_SEPARATOR, $old_file_name);
                        $base_path  .= implode("/", $old_file_name);
                        $post_upload = implode("/", $old_file_name);
                    }
                }

                $uploadDir         = [];
                $uploadDir['path'] = $base_path;
                $uploadDir['old_path'] = $old_path;
                $uploadDir['url']      = $baseurl;
                $uploadDir['old_url']  = $old_url;

                $this->uploadDir = $uploadDir;

                $this->oldFilePath = $old_path."/".$file_parts['basename'];

                if (!is_dir($base_path)) {
                    mkdir($base_path, 755, true);
                }

                if (is_dir($base_path)) {
                    $file_array    = explode(".", $file['name']);
                    $file_ext      = array_pop($file_array);
                    $new_file_name = sanitize_title(implode(".", $file_array)).".".$file_ext;
                    if ($replacement_option == "replace_only_file") {
                        $new_file_name = $db_file_name;
                    }

                    if (strtolower($new_file_name) != strtolower($file_parts['basename'])) {
                        $new_file_name = $this->checkForFileName($new_file_name, $base_path.DIRECTORY_SEPARATOR);
                    }

                    $this->newFilePath = $base_path.DIRECTORY_SEPARATOR.$new_file_name;

                    $status = move_uploaded_file($new_file, $this->newFilePath);

                    $this->newFileURL = trim($baseurl, "/")."/".$new_file_name;

                    if ($status) {
                        $oldFilePath = str_replace(["/", DIRECTORY_SEPARATOR], ["", ""], $this->oldFilePath);
                        $newFilePath = str_replace(["/", DIRECTORY_SEPARATOR], ["", ""], $this->newFilePath);
                        if ($oldFilePath != $newFilePath) {
                            if (file_exists($this->oldFilePath)) {
                                @unlink($this->oldFilePath);
                            }
                        }

                        update_attached_file($attachment->ID, $this->newFileURL);

                        $update_array         = [];
                        $update_array['ID']   = $attachment->ID;
                        $update_array['guid'] = $this->newFileURL;
                        // wp_get_attachment_url($this->post_id);
                        $update_array['post_mime_type'] = $file['type'];

                        $current_date     = date("Y-m-d H:i:s");
                        $current_date_gmt = date_i18n("Y-m-d H:i:s", strtotime($current_date));
                        if (isset($_REQUEST['date_options']) && !empty($_REQUEST['date_options'])) {
                            if ($_REQUEST['date_options'] == "replace_date") {
                                $update_array['post_date']     = $current_date;
                                $update_array['post_date_gmt'] = $current_date_gmt;
                            } else if ($_REQUEST['date_options'] == "custom_date") {
                                $custom_date      = filter_input(INPUT_POST, "custom_date");
                                $custom_date_hour = filter_input(INPUT_POST, "custom_date_hour");
                                $custom_hour      = str_pad($custom_date_hour, 2, 0, STR_PAD_LEFT);
                                $custom_date_min  = filter_input(INPUT_POST, "custom_date_min");
                                $custom_minute    = str_pad($custom_date_min, 2, 0, STR_PAD_LEFT);
                                $custom_date      = date("Y-m-d H:i:s", strtotime($custom_date." ".$custom_hour.":".$custom_minute));
                                if ($custom_date !== false) {
                                    $datetime     = date("Y-m-d H:i:s", strtotime($custom_date));
                                    $datetime_gmt = date_i18n("Y-m-d H:i:s", strtotime($datetime));
                                    $update_array['post_date']     = $datetime;
                                    $update_array['post_date_gmt'] = $datetime_gmt;
                                }
                            }
                        }

                        $update_array['post_modified']     = $current_date;
                        $update_array['post_modified_gmt'] = $current_date_gmt;
                        $post_id = \wp_update_post($update_array, true);

                        update_post_meta($attachment_id, '_wp_attached_file', trim(trim($post_upload, "/")."/".$new_file_name, "/"));

                        // update post doesn't update GUID on updates.
                        $wpdb->update($wpdb->posts, ['guid' => $this->newFileURL], ['ID' => $attachment->ID]);

                        $this->removeThumbImages();

                        $metadata = wp_generate_attachment_metadata($attachment->ID, $this->newFilePath);
                        wp_update_attachment_metadata($attachment->ID, $metadata);

                        $this->newImageMeta = wp_get_attachment_metadata($attachment_id);

                        // update_post_meta( $attachment_id, '_wp_attached_file', trim(trim($post_upload, "/")."/".$new_file_name ), "/");
                        $this->searchAndReplace();
                        wp_redirect(admin_url("post.php?post=".esc_attr($attachment_id)."&action=edit&premio_message=success&image_update=1"));
                        exit;
                    } else {
                        wp_die("Error during uploading file");
                    }//end if
                } else {
                    wp_die("Permission issue, Unable to create directory");
                }//end if
            }//end if
        }//end if

    }//end handle_folders_file_upload()


    /**
     * Check for filename
     *
     * @since  2.6.3
     * @access public
     */
    public function checkForFileName($fileName, $filePath, $postFix=0)
    {
        $new_file_name = $fileName;
        if (!empty($postFix)) {
            $file_array    = explode(".", $fileName);
            $file_ext      = array_pop($file_array);
            $new_file_name = implode(".", $file_array)."-".$postFix.".".$file_ext;
        }

        if (!file_exists($filePath.$new_file_name)) {
            return $new_file_name;
        }

        return $this->checkForFileName($fileName, $filePath, ($postFix + 1));

    }//end checkForFileName()


    /**
     * Check and Remove Thumb image in wp-content
     *
     * @since  2.6.3
     * @access public
     */
    public function removeThumbImages()
    {
        if (!empty($this->oldImageMeta) && isset($this->oldImageMeta['sizes']) && !empty($this->uploadDir) && isset($this->uploadDir['path'])) {
            $path = $this->uploadDir['path'].DIRECTORY_SEPARATOR;
            foreach ($this->oldImageMeta['sizes'] as $image) {
                if (file_exists($path.$image['file'])) {
                    @unlink($path.$image['file']);
                }
            }
        }

    }//end removeThumbImages()


    /**
     * Search and Replace files in Database
     *
     * @since  2.6.3
     * @access public
     * @return $string
     */
    public function searchAndReplace()
    {
        if (wp_attachment_is('image', $this->attachment_id)) {
            $this->isNewImage = 1;
        }

        if ($this->oldFileURL != $this->newFileURL) {
            $replace = [
                'search'  => $this->oldFileURL,
                'replace' => $this->newFileURL,
            ];
            $this->replaceItems[] = $replace;
        }

        $base_url = $this->uploadDir['url'];
        $base_url = trim($base_url, "/")."/";
        $new_url  = $this->newFileURL;

        if (isset($this->oldImageMeta['sizes']) && !empty($this->oldImageMeta['sizes'])) {
            if (!isset($this->newImageMeta['sizes']) || empty($this->newImageMeta['sizes'])) {
                foreach ($this->oldImageMeta['sizes'] as $key => $image) {
                    $replace = [
                        'search'  => $base_url.$image['file'],
                        'replace' => $new_url,
                    ];
                    $this->replaceItems[] = $replace;
                }
            } else if (isset($this->newImageMeta['sizes']) && !empty($this->newImageMeta['sizes'])) {
                $new_size = $this->newImageMeta['sizes'];
                foreach ($this->oldImageMeta['sizes'] as $key => $image) {
                    $new_replace_url = $new_url;
                    if (isset($new_size[$key])) {
                        $new_replace_url = $base_url.$new_size[$key]['file'];
                    }

                    $replace = [
                        'search'  => $base_url.$image['file'],
                        'replace' => $new_replace_url,
                    ];
                    $this->replaceItems[] = $replace;
                }
            }//end if
        }//end if

        if (!empty($this->replaceItems)) {
            $replaceItems = [];
            foreach ($this->replaceItems as $args) {
                if ($args['search'] != $args['replace']) {
                    $replaceItems[] = $args;
                }
            }

            $this->replaceItems = $replaceItems;
            $this->replaceURL();
        }

    }//end searchAndReplace()


    /**
     * Replace URL in Database tables
     *
     * @since  2.6.3
     * @access public
     * @return $string
     */
    function replaceURL()
    {
        // check in post content
        $this->checkInPostContent();

        // check in options
        $this->checkInOptions();

        // check in meta
        $this->checkInMetaData();

        if (function_exists('folders_clear_all_caches')) {
            folders_clear_all_caches();
        }

    }//end replaceURL()


    /**
     * Checking image URLs in Post Content
     *
     * @since  2.6.3
     * @access public
     * @return $string
     */
    function checkInPostContent()
    {
        global $wpdb;
        $post_table = $wpdb->prefix."posts";
        if (!empty($this->replaceItems)) {
            $query        = "SELECT ID, post_content FROM ".$post_table." WHERE post_content LIKE %s";
            $update_query = "UPDATE ".$post_table." SET post_content = %s WHERE ID = %d";
            foreach ($this->replaceItems as $args) {
                if ($args['search'] != $args['replace']) {
                    $sql_query = $wpdb->prepare($query, "%".$args['search']."%");
                    $results   = $wpdb->get_results($sql_query, ARRAY_A);
                    if (!empty($results)) {
                        foreach ($results as $row) {
                            $content           = $this->findAndReplaceContent($row['post_content'], $args['search'], $args['replace']);
                            $update_post_query = $wpdb->prepare($update_query, $content, $row['ID']);
                            $result            = $wpdb->query($update_post_query);
                        }
                    }
                }
            }
        }

    }//end checkInPostContent()


    /**
     * Checking image URLs in MetaData
     *
     * @since  2.6.3
     * @access public
     * @return $string
     */
    function checkInOptions()
    {
        global $wpdb;
        $post_table = $wpdb->prefix."options";
        if (!empty($this->replaceItems)) {
            $query        = "SELECT option_id, option_value FROM ".$post_table." WHERE option_value LIKE %s";
            $update_query = "UPDATE ".$post_table." SET option_value = %s WHERE option_id = %d";
            foreach ($this->replaceItems as $args) {
                if ($args['search'] != $args['replace']) {
                    $sql_query = $wpdb->prepare($query, "%".$args['search']."%");
                    $results   = $wpdb->get_results($sql_query, ARRAY_A);
                    if (!empty($results)) {
                        foreach ($results as $row) {
                            $content           = $this->findAndReplaceContent($row['post_content'], $args['search'], $args['replace']);
                            $update_post_query = $wpdb->prepare($update_query, $content, $row['ID']);
                            $result            = $wpdb->query($update_post_query);
                        }
                    }
                }
            }
        }

    }//end checkInOptions()


    /**
     * Checking image URLs in MetaData
     *
     * @since  2.6.3
     * @access public
     * @return $string
     */
    function checkInMetaData()
    {
        $tables = [
            [
                'table_name'  => 'usermeta',
                'primary_key' => 'umeta_id',
                'search_key'  => 'meta_value',
            ],
            [
                'table_name'  => 'termmeta',
                'primary_key' => 'meta_id',
                'search_key'  => 'meta_value',
            ],
            [
                'table_name'  => 'postmeta',
                'primary_key' => 'meta_id',
                'search_key'  => 'meta_value',
            ],
            [
                'table_name'  => 'commentmeta',
                'primary_key' => 'meta_id',
                'search_key'  => 'meta_value',
            ],
        ];
        global $wpdb;
        foreach ($tables as $table) {
            $post_table = $wpdb->prefix.$table['table_name'];
            if (!empty($this->replaceItems)) {
                $query        = "SELECT ".esc_attr($table['primary_key']).", ".esc_attr($table['search_key'])." FROM ".esc_attr($post_table)." WHERE ".esc_attr($table['search_key'])." LIKE %s";
                $update_query = "UPDATE ".$post_table." SET ".esc_attr($table['search_key'])." = %s WHERE ".esc_attr($table['primary_key'])." = %d";
                foreach ($this->replaceItems as $args) {
                    if ($args['search'] != $args['replace']) {
                        $sql_query = $wpdb->prepare($query, "%".$args['search']."%");
                        $results   = $wpdb->get_results($sql_query, ARRAY_A);
                        if (!empty($results)) {
                            foreach ($results as $row) {
                                $content           = $this->findAndReplaceContent($row[$table['search_key']], $args['search'], $args['replace']);
                                $update_post_query = $wpdb->prepare($update_query, $content, $row[$table['primary_key']]);
                                $result            = $wpdb->query($update_post_query);
                            }
                        }
                    }
                }
            }
        }

    }//end checkInMetaData()


    /**
     * Checking for Array Key
     *
     * @since  2.6.3
     * @access public
     * @return $json
     * Forked from Enable Media Replace
     */
    function findAndReplaceContent($content, $search, $replace, $depth=false)
    {
        $content = maybe_unserialize($content);

        // Checking for JSON Data
        $isJson = $this->isJSON($content);
        if ($isJson) {
            $content = json_decode($content);
        }

        // Replace content if content is String
        if (is_string($content)) {
            $content = str_replace($search, $replace, $content);
        } else if (is_wp_error($content)) {
            // Return if error in data
        } else if (is_array($content)) {
            // Replace content if content is Array
            foreach ($content as $index => $value) {
                $content[$index] = $this->findAndReplaceContent($value, $search, $replace, true);
                if (is_string($index)) {
                    $index_replaced = $this->findAndReplaceContent($index, $search, $replace, true);
                    if ($index_replaced !== $index) {
                        $content = $this->changeArrayKey($content, [$index => $index_replaced]);
                    }
                }
            }
        } else if (is_object($content)) {
            // Replace content if content is Object
            foreach ($content as $key => $value) {
                $content->{$key} = $this->findAndReplaceContent($value, $search, $replace, true);
            }
        }//end if

        if ($isJson && $depth === false) {
            $content = json_encode($content, JSON_UNESCAPED_SLASHES);
        } else if ($depth === false && (is_array($content) || is_object($content))) {
            $content = maybe_serialize($content);
        }

        return $content;

    }//end findAndReplaceContent()


    /**
     * Checking for Array Key
     *
     * @since  2.6.3
     * @access public
     * @return $json
     */
    function changeArrayKey($array, $set)
    {
        if (is_array($array) && is_array($set)) {
            $newArray = [];
            foreach ($array as $k => $v) {
                $key            = array_key_exists($k, $set) ? $set[$k] : $k;
                $newArray[$key] = is_array($v) ? $this->changeArrayKey($v, $set) : $v;
            }

            return $newArray;
        }

        return $array;

    }//end changeArrayKey()


    /**
     * Check if it is JSON or not
     *
     * @since  2.6.3
     * @access public
     * Forked from Enable Media Replace
     * @return $json
     */
    function isJSON($content)
    {
        if (is_array($content) || is_object($content)) {
            return false;
        }

        $json = json_decode($content);
        return $json && $json != $content;

    }//end isJSON()


}//end class

$folders_replace_media = new folders_replace_media();
