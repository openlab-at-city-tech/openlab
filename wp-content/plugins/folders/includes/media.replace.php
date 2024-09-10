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

class folders_replace_media {

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
     * Mode for file Replacement
     *
     * @var    string    $mode    New File URL
     * @since  1.0.0
     * @access public
     */
    public $mode = "rename-file";

    /**
     * file title for Replacement
     *
     * @var    string    $replace_media_title    New File Title
     * @since  1.0.0
     * @access public
     */
    public $replace_media_title = "";

    /**
     * Attachment ID for Replacement file
     *
     * @var    string    $attachment_id    Attachment ID
     * @since  1.0.0
     * @access public
     */
    public $attachment_id;

    /**
     * Old File Path
     *
     * @var    array    $old_file_path    Old File Path
     * @since  1.0.0
     * @access public
     */
    public $old_file_path;

    /**
     * Old File URL
     *
     * @var    array    $old_file_url    Old File URL
     * @since  1.0.0
     * @access public
     */
    public $old_file_url;

    /**
     * New file path
     *
     * @var    array    $new_file_path    New file path
     * @since  1.0.0
     * @access public
     */
    public $new_file_path;

    /**
     * New file URL
     *
     * @var    array    $new_file_url    New file URL
     * @since  1.0.0
     * @access public
     */
    public $new_file_url;

    /**
     * Old Image Meta
     *
     * @var    array    $old_image_meta    Old Image Meta
     * @since  1.0.0
     * @access public
     */
    public $old_image_meta;

    /**
     * New Image Meta
     *
     * @var    array    $new_image_meta    New Image Meta
     * @since  1.0.0
     * @access public
     */
    public $new_image_meta;

    /**
     * Upload dir path
     *
     * @var    array    $upload_dir    Upload dir path
     * @since  1.0.0
     * @access public
     */
    public $upload_dir;

    /**
     * Old file image status
     *
     * @var    array    $is_old_image    Old file image status
     * @since  1.0.0
     * @access public
     */
    public $is_old_image = 0;

    /**
     * New file image status
     *
     * @var    array    $is_new_image    New file image status
     * @since  1.0.0
     * @access public
     */
    public $is_new_image = 0;


    /**
     * Define the core functionality of the replacement functionality.
     *
     * Set Button Color
     * Check for Functionality is enabled or not
     * Show Replacement form, Success message
     *
     * @since 1.0.0
     */
    function __construct() {

        add_action("init", array($this,"init"));

        $customize_folders = get_option('customize_folders');

        $this->buttonColor = isset($customize_folders['media_replace_button'])?$customize_folders['media_replace_button']:"#FA166B";

        $this->isEnabled = isset($customize_folders['folders_enable_replace_media'])?$customize_folders['folders_enable_replace_media']:"yes";

        $this->replace_media_title = isset($customize_folders['replace_media_title'])?$customize_folders['replace_media_title']:"off";

        $this->isEnabled = ($this->isEnabled == "yes")?true:false;

        if (isset($customize_folders['show_folder_in_settings']) && $customize_folders['show_folder_in_settings'] == "yes") {
            $this->upgradeLink = admin_url("options-general.php?page=wcp_folders_settings&setting_page=upgrade-to-pro");
        } else {
            $this->upgradeLink = admin_url("admin.php?page=folders-upgrade-to-pro");
        }

        if($this->isEnabled) {

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

        add_action('edit_attachment', array($this, 'change_file_name_on_update' ));

        add_filter('attachment_fields_to_edit', array($this, 'attachment_replace_name_with_title'), 10, 2);

        add_action('admin_head', array($this,  'premio_replace_file_CSS'));

        add_action('wp_enqueue_media', array($this, 'replace_media_file_script'));

        add_action('wp_ajax_premio_folder_replace_name_with_title', array($this, 'replace_name_with_title'));

        add_action('wp_ajax_premio_folder_update_wp_config', array($this, 'update_wp_config'));

        add_action('admin_notices', array($this, 'admin_premio_notices'));

        add_filter('wp_get_attachment_image_src', array($this, 'update_to_new_url'), 10, 4);

        add_filter('wp_prepare_attachment_for_js', array($this, 'prepare_attachment_for_js'), 10, 3);

    }

    /**
     * Add admin init
     *
     * @since  2.6.3
     * @access public
     */
    public function init() {
        if(isset($_GET['enable_trash']) && !empty($_GET['enable_trash'])) {
            $nonce = sanitize_text_field($_GET['enable_trash']);
            if(wp_verify_nonce($nonce, "folders_enable_media_trash")) {
                $customize_folders = get_option('customize_folders');
                $customize_folders['enable_media_trash'] = "on";
                update_option("customize_folders", $customize_folders);
                wp_redirect(admin_url("upload.php?page=folders-media-cleaning"));
                exit;
            }
        }
    }

    /**
     * Update Cached file URL
     *
     * @since 2.8.4
     * @access public
     *
     */
    public function prepare_attachment_for_js($response, $attachment, $meta) {
        if ($response === false) {
            return $response;
        }

        $refreshToken = get_post_meta($response['id'], "folders_file_replaced", true);
        if($refreshToken !== false && !empty($refreshToken)) {
            $response['url'] = add_query_arg('ver', $refreshToken, $response['url']);
            if(isset($response['sizes']['medium']['url']) && !empty($response['sizes']['medium']['url'])) {
                $response['sizes']['medium']['url'] = add_query_arg('ver', $refreshToken, $response['sizes']['medium']['url']);
            }
        }
        return $response;
    }

    /**
     * Update Cached file URL
     *
     * @since 2.8.4
     * @access public
     *
     */
    public function update_to_new_url($image, $attachment_id, $size, $icon) {
        if ($image === false)
            return $image;

        $refreshToken = get_post_meta($attachment_id, "folders_file_replaced", true);
        if($refreshToken !== false && !empty($refreshToken)) {
            $image[0] = add_query_arg('ver', $refreshToken, $image[0]);
        }
        return $image;
    }

    /**
     * Show media details on hover
     *
     * @since 2.6.3
     * @access public
     *
     */
    public function admin_premio_notices() {
        if(isset($_REQUEST['premio_message']) && $_REQUEST['premio_message'] == "success") { ?>
            <div class="notice notice-success is-dismissible">
                <p><b><?php esc_html_e( 'File successfully replaced', 'folders' ); ?></b></p>
                <p><?php esc_html_e( 'The file has been successfully replaced using the file replacement feature', 'folders' ); ?></p>
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
                    <div class="folders-undo-header"><?php esc_html_e( 'File successfully replaced', 'folders' ); ?></div>
                    <div class="folders-undo-body" style="padding:0"><?php esc_html_e( 'The file has been successfully replaced using the file replacement feature', 'folders' ); ?></div>
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
        <?php }
    }

    /**
     * Admin notice for to show WP_TRASH functionality
     *
     * @since 2.6.3
     * @access public
     *
     */
    public function admin_notices() {
        $is_defined = defined( 'MEDIA_TRASH' );
        if ( !($is_defined && MEDIA_TRASH )) {
            global $current_screen;
            if ((isset($current_screen->base) && $current_screen->base == "upload") || (isset($_REQUEST['page']) && ($_REQUEST['page'] == "wcp_folders_settings" || $_REQUEST['page'] == "folders-media-cleaning"))) { ?>
                <style>
                    .media-trash-notice{padding:0 !important;margin:15px 15px 0}.media-trash-notice-message{padding:15px 10px 0}.media-trash-notice a{text-decoration:none}.media-trash-notice-footer{text-align:right;padding:10px 15px;background:#f1f1f1;margin:15px 0 0 0}.spinner.trash-spinner{display:none}.spinner.trash-spinner.animate{display:inline-block;opacity:.7;visibility:visible;margin-right:0}
                </style>
                <div class="notice notice-info premio-notice media-trash-notice">
                    <div class="media-trash-notice-message">
                        To enable Trash functionality in Media, Add the following one line <code>define( 'MEDIA_TRASH', true );</code> in <a href="https://wordpress.org/support/article/editing-wp-config-php/" rel="noopener noreferrer" target="_blank">wp-config.php</a> file just before the line that says "That’s all, stop editing!"
                    </div>
                    <div class="media-trash-notice-footer">
                        <a class="button button-primary" href="#">Automatically write this line<span class="spinner trash-spinner"></span></a>
                    </div>
                </div>

                <style>
                    .folder-popup-form{position:fixed;width:100%;height:100%;background:rgba(0,0,0,.5);top:0;left:0;z-index:10001;display:none}.popup-form-content{background:#fff;min-height:100px;width:400px;text-align:center;margin:0 auto;position:absolute;left:0;right:0;top:50%;transform:translate(0,-50%);-webkit-transform:translate(0,-50%);-moz-transform:translate(0,-50%);-o-transform:translate(0,-50%);-ms-transform:translate(0,-50%);padding:20px;-webkit-border-radius:4px;-moz-border-radius:4px;border-radius:4px;color:#484848}.popup-form-data{position:relative}.close-popup-button{position:absolute;right:-10px;top:-10px;width:20px;height:20px}.close-popup-button a{display:block;position:relative;width:20px;height:20px;color:#333;padding:2px;box-sizing:border-box}.close-popup-button a span{display:block;position:relative;width:16px;height:16px;transition:all .2s linear}.close-popup-button a:hover span{transform:rotate(180deg)}.close-popup-button a span:after,.close-popup-button a span:before{content:"";position:absolute;width:12px;height:2px;background-color:#333;display:block;border-radius:2px;transform:rotate(45deg);top:7px;left:2px}.close-popup-button a span:after{transform:rotate(-45deg)}.add-update-folder-title{display:block;position:relative;max-width:100%;margin:0;padding:0 0 15px 0;color:#595959;text-align:center;text-transform:none;word-wrap:break-word;font-weight:700;font-size:22px;line-height:26px}.add-update-folder-title:after{content:"";position:absolute;top:100%;width:70px;height:2px;background:#3085d6;left:0;right:0;margin:0 auto}.folder-form-buttons{display:flex}.folder-form-buttons a:not(.inline-button),.folder-form-buttons button{display:inline-flex;padding:0;text-decoration:none;margin:10px 3px;border-radius:4px;border:solid 1px #1da1f4;line-height:34px;font-weight:700;font-size:14px;box-sizing:border-box;height:36px;cursor:pointer;flex:1;justify-content:center}.form-cancel-btn,a.form-cancel-btn:hover{background-color:#fff;color:#3085d6;outline:0}.form-submit-btn{background-color:#3085d6;color:#fff;outline:0}.form-submit-btn.disabled{color:#a7aaad!important;background:#f6f7f7!important;border-color:#dcdcde!important;box-shadow:none!important;text-shadow:none!important;cursor:default}.folder-note{padding:20px 0;line-height:20px}#folder-trash-message .popup-form-content{width:460px}.folder-note a{text-decoration:none;display:inline-block}
                </style>
                <?php if(isset($_REQUEST['mode']) && $_REQUEST['mode'] == "list" && isset($_REQUEST['attachment-filter']) && $_REQUEST['attachment-filter'] == "trash") { ?>

                    <div class="folder-popup-form" id="folder-trash-message" style="display: block;">
                        <div class="popup-form-content">
                            <div class="popup-form-data">
                                <div class="close-popup-button">
                                    <a class="" href="javascript:;"><span></span></a>
                                </div>
                                <form action="" method="post" id="save-folder-form">
                                    <div class="add-update-folder-title">Rewrite wp-config.php to enable Trash</div>
                                    <div class="folder-note">
                                        To enable Trash functionality in Media, Add the following one line <code>define( 'MEDIA_TRASH', true );</code> in <a href="https://wordpress.org/support/article/editing-wp-config-php/" rel="noopener noreferrer" target="_blank">wp-config.php</a> file just before the line that says "That’s all, stop editing!"
                                    </div>
                                    <div class="folder-form-buttons">
                                        <a href="javascript:;" class="form-cancel-btn">I'll do it manually</a>
                                        <button type="submit" class="form-submit-btn write-in-config-file">Automatically write this line</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="folder-popup-form" id="wp-config-update-notice" style="display: none;">
                        <form action="" method="post" id="bulk-folder-form">
                            <div class="popup-form-content">
                                <div class="popup-form-data">
                                    <div class="close-popup-button">
                                        <a class="" href="javascript:;"><span></span></a>
                                    </div>
                                    <div class="add-update-folder-title">
                                        Something went wrong
                                    </div>
                                    <div class="folder-form-message" style="padding: 25px 10px;">
                                        We couldn’t write to the file automatically. Please add the line manually to your wp-config.php. You need to modify your wp-config.php file and just before the line that says "That’s all, stop editing!", add this line:<code>define( 'MEDIA_TRASH', true );</code>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                <?php } ?>

                <script>
                    (function($) {
                        "use strict";
                        $(document).ready(function(){
                            $(document).on("click", ".media-trash-notice-footer a:not(.disabled), .write-in-config-file:not(.disabled)", function(e){
                                $(this).addClass("disabled");
                                $(".trash-spinner").addClass("animate");
                                e.preventDefault();
                                $.ajax({
                                    url: "<?php echo esc_url(admin_url("admin-ajax.php")) ?>",
                                    data: {
                                        action: "premio_folder_update_wp_config",
                                        nonce: "<?php echo esc_attr(wp_create_nonce("add_media_status_in_wp_config")) ?>"
                                    },
                                    type: 'post',
                                    dataType: "json",
                                    success: function(response) {
                                        if(response.status == 1) {
                                            setTimeout(function(){
                                                window.location.reload();
                                            }, 4000);
                                            $("#folder-trash-message").remove();
                                        } else {
                                            $(".trash-spinner").removeClass("animate");
                                            $(".media-trash-notice-footer a").removeClass("disabled");
                                            $("#wp-config-update-notice").show();
                                            $("#folder-trash-message").remove();
                                        }
                                    }
                                });
                            });

                            $(document).on("click", "#folder-trash-message", function(){
                                $("#folder-trash-message").hide();
                            });

                            $(document).on("click", "#folder-trash-message .popup-form-content", function(e){
                                e.stopPropagation();
                            });

                            <?php if(isset($_REQUEST['mode']) && $_REQUEST['mode'] == "list" && isset($_REQUEST['attachment-filter']) && $_REQUEST['attachment-filter'] == "trash") { ?>
                            $(document).on("click", ".close-popup-button, .form-cancel-btn", function(e){
                                e.preventDefault();
                                $("#folder-trash-message").remove();
                                $(".folder-popup-form").remove();
                            });
                            <?php } ?>
                        });
                    })(jQuery);
                </script>
            <?php }
        }

        $isScanPage = (isset($_REQUEST['page']) && $_REQUEST['page'] == "folders-media-cleaning" && (isset($_REQUEST['scan']) && $_REQUEST['scan'] == 1))?true:false;
        $isFilterPage = (isset($_REQUEST['attachment-filter']) && $_REQUEST['attachment-filter'] == "trash")?true:false;

        if ($isScanPage || $isFilterPage) { ?>
            <style>
                .media-notice {
                    margin: 15px 15px 2px;
                }
                .media-folder-notice {
                    display: flex;
                    align-items: baseline;
                }
                .media-folder-notice-left {
                    flex: 0 0 35px;
                    color: #d63638;
                }
                .media-folder-notice-right {
                    flex: 1;
                    font-size: 14px;
                }
                .media-folder-notice-right p {
                    font-size: 14px;
                }
                .media-folder-notice span.dashicons.dashicons-info-outline {
                    padding: 4px;
                    background-color: #fff2f2;
                    border-radius: 50%;
                }
                .media-folder-notice b {
                    font-weight: 600;
                }
            </style>
            <div class="notice notice-error media-notice">
                <div class="media-folder-notice">
                    <div class="media-folder-notice-left">
                        <span class="dashicons dashicons-info-outline"></span>
                    </div>
                    <div class="media-folder-notice-right">
                        <p>Please <b>be very careful before deleting</b> any files. <b>Take back up</b>, and make sure you test the website <b>before permanently deleting</b>. Some actively used files can still show up as unused files when searching. You <b>are responsible</b> for any damage if you delete anything important. So, please be careful 🙏</p>
                    </div>
                </div>
            </div>
        <?php }
    }

    /**
     * Update config.php file to save WP_TRASH functionality
     *
     * @since 2.6.3
     * @access public
     *
     */
    public function update_wp_config() {
        $errorCounter = 0;
        $response = [];
        $response['status'] = 0;
        $response['message'] = "";
        $response['valid'] = 0;
        $postData = filter_input_array(INPUT_POST);
        if (!isset($postData['nonce']) || trim($postData['nonce']) == "") {
            $errorCounter++;
            $response['message'] = "Invalid request";
        } else {
            $nonce = sanitize_title($postData['nonce']);
            if(!wp_verify_nonce($nonce, 'add_media_status_in_wp_config')) {
                $errorCounter++;
                $response['message'] = "Invalid request";
            }
        }
        if($errorCounter == 0) {
            $response['status'] = 1;

            $is_defined = defined( 'MEDIA_TRASH' );
            if ( $is_defined && MEDIA_TRASH ) {
                echo wp_json_encode($response);
                die;
            }

            try {
                $conf = ABSPATH . 'wp-config.php';
                $stream = fopen( $conf, 'r+' );
                if ( $stream === false )  {
                    $response['status'] = -1;
                    echo wp_json_encode($response); die;
                }

                try {
                    if ( !flock( $stream, LOCK_EX ) ) {
                        $response['status'] = -1;
                        echo wp_json_encode($response); die;
                    }
                    $stat = fstat( $stream );

                    /* Find out the ideal position to write on */
                    $found = false;
                    $patterns = array (
                        array (
                            'regex' => '^\/\*\s*' . preg_quote( "That's all, stop editing!" ) . '.*?\s*\*\/',
                            'where' => 'above'
                        )
                    );
                    $current = 0;
                    while ( !feof( $stream ) ) {
                        $line = fgets( $stream ); // Read line by line
                        if ( $line === false ) break; // No more lines
                        $prev = $current; // Previous position
                        $current = ftell( $stream ); // Current position
                        foreach ( $patterns as $item ) {
                            if ( !preg_match( '/'.$item['regex'].'/', trim( $line ) ) ) {
                                continue;
                            }
                            $found = true;
                            if ( $item['where'] == 'above' ) {
                                fseek( $stream, $prev );
                                $current = $prev;
                            }
                            break 2;
                        }
                    }

                    /* Check if the position is found */
                    if ( !$found ) {
                        $response['status'] = -1;
                        echo wp_json_encode($response); die;
                    }

                    /* Write the constant definition line */
                    $new = "define( 'MEDIA_TRASH', true );" . PHP_EOL;
                    $rest = fread( $stream, $stat['size'] - $current );
                    fseek( $stream, $current );
                    $written = fwrite( $stream, $new . $rest );

                    /* All done */
                    if ( $written === false ) {
                        $response['status'] = -1;
                        echo wp_json_encode($response); die;
                    }
                    fclose( $stream );
                }
                catch ( Exception $e ) {
                    fclose( $stream );

                    $response['status'] = -1;
                    echo wp_json_encode($response); die;
                }
            }
            catch ( Exception $e ) {
                $response['status'] = -1;
                echo wp_json_encode($response); die;
            }

            echo wp_json_encode($response); die;
        }
        echo wp_json_encode($response); die;
    }

    /**
     * Replace file name with title
     *
     * @since 2.6.3
     * @access public
     *
     */
    public function replace_name_with_title() {
        $errorCounter = 0;
        $response = [];
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
            if(!wp_verify_nonce($nonce, 'change_attachment_title_'.$postData['post_id'])) {
                $errorCounter++;
                $response['message'] = "Invalid request";
            }
        }
        if($errorCounter == 0) {
            $response['status'] = 1;

            $post_id = $postData['post_id'];

            $post = get_post($post_id);

            $post_slug = sanitize_file_name(sanitize_text_field($_POST['post_title']));

            $attachment_url = $post->guid;
            $url = wp_get_attachment_url($post_id);
            if(!empty($url)) {
                $attachment_url = $url;
            }
            $file_parts = pathinfo($attachment_url);

            $db_file_name = $file_parts['basename'];
            $db_file_array = explode(".", $db_file_name);
            $db_file_name_ext = array_pop($db_file_array);
            $db_file_name = trim($db_file_name, $db_file_name_ext);
            $db_file_name = trim($db_file_name, ".");

            if(strtolower($db_file_name) == strtolower($post_slug)) {
                $response['valid'] = 0;
                $response['message'] = esc_html__("The title is same as the current filename", "folders");
            } else {
                $response['valid'] = 1;
                $response['message'] = esc_html__("File name has been updated", "folders");
                $this->change_file_name_with_title($post_id);
            }
        }
        echo wp_json_encode($response);
        exit;
    }

    /**
     * Add Js and CSS files for replace file name with title
     *
     * @since 2.6.3
     * @access public
     *
     */
    public function replace_media_file_script() {
        wp_enqueue_script('folders-media-replace-js', WCP_FOLDER_URL . 'assets/js/replace-file-name.js', array('jquery'), WCP_FOLDER_VERSION, true);
        wp_localize_script('folders-media-replace-js', 'replace_media_options', array(
            'ajax_url' => admin_url("admin-ajax.php"),
        ));
    }

    public function premio_replace_file_CSS() {
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
      </style>';
    }

    public function change_file_name_with_title($post_id = 0) {
        if(empty($post_id)) {
            return;
        }
        $post = get_post($post_id);
        if(empty($post)) {
            return;
        }
        if($post->post_type != "attachment") {
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
        $new_file_name = $post_slug.".".$file_ext;

        $wp_upload_path = wp_get_upload_dir();

        $base_path = $wp_upload_path['basedir'].DIRECTORY_SEPARATOR;
        $baseurl = $wp_upload_path['baseurl']."/";

        $post_upload = "";

        $wp_attached_file = get_post_meta($attachment_id, "_wp_attached_file", true);
        if($wp_attached_file !== false) {
            $old_file_name = explode("/", $wp_attached_file);
            array_pop($old_file_name);

            if(count($old_file_name) > 0) {
                $base_path .= implode(DIRECTORY_SEPARATOR, $old_file_name);
                $baseurl .= implode("/", $old_file_name);

                $post_upload = implode("/", $old_file_name);
            }
        }

        $upload_dir = [];
        $upload_dir['path'] = $base_path;
        $upload_dir['old_path'] = $base_path;
        $upload_dir['url'] = $baseurl;
        $upload_dir['old_url'] = $baseurl;

        $this->upload_dir = $upload_dir;

        $attachment_url = $attachment->guid;
        $url = wp_get_attachment_url($attachment_id);
        if(!empty($url)) {
            $attachment_url = $url;
        }
        $file_parts = pathinfo($attachment_url);
        $this->old_file_path = $base_path . DIRECTORY_SEPARATOR . $file_parts['basename'];
        if(isset($attachment_meta['file']) && !empty($attachment_meta['file'])) {
            $this->old_file_url = $wp_upload_path['baseurl'] . "/" . $attachment_meta['file'];
        } else {
            $this->old_file_url = wp_get_attachment_url($post_id);
        }

        if($new_file_name != $file_name) {
            global $wpdb;

            $new_file_name = $this->checkForFileName($new_file_name, $upload_dir['path'].DIRECTORY_SEPARATOR);

            $this->new_file_path = $upload_dir['path'].DIRECTORY_SEPARATOR.$new_file_name;
            $this->new_file_url = $upload_dir['url']."/".$new_file_name;
            if(file_exists($this->old_file_path)) {
                rename($this->old_file_path, $this->new_file_path);

                update_attached_file($post->ID, $this->new_file_path);

                $update_array = [];
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

                update_post_meta( $attachment_id, '_wp_attached_file', trim(trim($post_upload, "/")."/".$new_file_name , "/"));

                $this->searchAndReplace();

                delete_post_meta($attachment_id, "folders_file_replaced");
                add_post_meta( $attachment_id, "folders_file_replaced", time(), true);
            }
        }
    }

    /**
     * Replace file name with title
     *
     * @since 2.6.3
     * @access public
     *
     */
    public function change_file_name_on_update($post_id = 0) {
        global $post;
        if(!isset($_REQUEST['premio_change_nonce']) || !isset($_REQUEST['premio_change_file_name'])) {
            return;
        }
        if(!wp_verify_nonce($_REQUEST['premio_change_nonce'], 'premio_change_file_name_'.$post->ID)) {
            return;
        }
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        $premio_change_file_name = sanitize_text_field($_REQUEST['premio_change_file_name']);

        if($premio_change_file_name == "yes") {
            if($post->post_type == "attachment") {

                unset($_REQUEST['premio_change_nonce']);
                unset($_REQUEST['premio_change_file_name']);

                $this->change_file_name_with_title($post->ID);
            }
        }
    }

    /**
     * Add Js and CSS files for replace file screen
     *
     * @since 2.6.3
     * @access public
     *
     */
    public function folders_admin_css_and_js($page) {
        if($page == "media_page_folders-replace-media" || $page == "admin_page_folders-replace-media") {
            wp_enqueue_style('folders-replace-media', plugin_dir_url(dirname(__FILE__)) . 'assets/css/replace-media.css', [], WCP_FOLDER_VERSION);

            wp_enqueue_script('folders-simpledropit', plugin_dir_url(dirname(__FILE__)) . 'assets/js/simpledropit.min.js', [], WCP_FOLDER_VERSION, true);
            wp_enqueue_script('folders-replace-media', plugin_dir_url(dirname(__FILE__)) . 'assets/js/replace-media.js', [], WCP_FOLDER_VERSION, true);
            $maxUploadSize = ini_get("upload_max_filesize");
            $maxUploadSize = str_replace(["K", "M", "G", "T", "P"],[" KB", " MB", " GB", " TB", " PB"], $maxUploadSize);
            $maxSize = sprintf(esc_html__("Maximum file size %1\$s", "folders"), $maxUploadSize);
            wp_localize_script('folders-simpledropit','replace_settings', [
                'max_size' => $maxSize,
                'file_name' => esc_html__("File name", 'folders'),
                'file_size' => esc_html__("Size", 'folders'),
                'file_type' => esc_html__("Type", 'folders'),
                'dimension' => esc_html__("Dimension", 'folders'),
                'drag_file' => esc_html__("Drag and drop files here", 'folders')
            ]);
//            wp_enqueue_script('jquery-ui-datepicker');
        }
    }

    /**
     * Add file replace menu in admin
     *
     * @since 2.6.3
     * @access public
     * @return $string
     *
     */
    public function admin_menu() {
        add_submenu_page("",
            esc_html__("Replace media", "folders"),
            esc_html__("Replace media", "folders"),
            'upload_files',
            'folders-replace-media',
            array($this, 'folders_replace_media')
        );
    }

    /**
     * Add file replacement screen
     *
     * @since 2.6.3
     * @access public
     * @return $string
     *
     */
    public function folders_replace_media() {
        global $plugin_page;
        $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : '';
        $attachment_id = isset($_GET['attachment_id']) ? sanitize_text_field($_GET['attachment_id']) : '';
        $nonce = isset($_GET['nonce']) ? sanitize_text_field($_GET['nonce']) : '';
        if (!wp_verify_nonce($nonce, "folders-replace-media-".$attachment_id)) {
            echo 'Invalid Nonce';
            exit;
        }
        $attachment = get_post($attachment_id);
        if(empty($attachment) || !isset($attachment->guid)) {
            echo 'Invalid URL';
            exit;
        }
        $guid = $attachment->guid;
        $guid = explode(".", $guid);
        if($guid == $attachment->guid) {
            echo 'Invalid URL';
            exit;
        }
        $form_action = $this->getMediaReplaceURL($attachment_id);
        include_once dirname(dirname(__FILE__)) . WCP_DS . "/templates" . WCP_DS . "admin" . WCP_DS . "media-replace.php";
    }

    /**
     * Add action for file replacement
     *
     * @since 2.6.3
     * @access public
     * @return $actions
     *
     */
    public function add_media_action($actions, $post) {
        if (!$this->isEnabled) {
            return array_merge($actions);
        }

        if(current_user_can("upload_files")) {
            if (wp_attachment_is('image', $post->ID)) {
                $link = $this->getMediaReplaceURL($post->ID);
                $newaction['replace_media'] = '<a style="color: ' . esc_attr($this->buttonColor) . '" href="' . esc_url($link) . '" rel="permalink">' . esc_html__("Replace media", "folders") . '</a>';
                return array_merge($actions, $newaction);
            } else {

                $newaction['replace_media'] = '<a style="color: ' . esc_attr($this->buttonColor) . '" target="_blank" href="' . esc_url($this->upgradeLink) . '" rel="permalink">' . esc_html__("Replace Media 🔑", "folders") . '</a>';
                return array_merge($actions, $newaction);
            }
        }

        return $actions;
    }

    /**
     * Get URL for file Replacement
     *
     * @since 2.6.3
     * @access public
     * @return $url
     *
     */
    public function getMediaReplaceURL($attach_id) {
        $url = admin_url( "upload.php");
        $url = add_query_arg(array(
            'page' => 'folders-replace-media',
            'action' => 'folders_replace_media',
            'attachment_id' => $attach_id,
            'nonce' => wp_create_nonce("folders-replace-media-".$attach_id)
        ), $url);

        return $url;
    }

    /**
     * Update Metabox to Replace file name with title
     *
     * @since 2.6.3
     * @access public
     * @return $size
     *
     */
    public function replace_meta_box($post) {
        if(current_user_can("upload_files")) {
            if (wp_attachment_is('image', $post->ID)) {
                $link = $this->getMediaReplaceURL($post->ID); ?>
                <p><a style='background: <?php echo esc_attr($this->buttonColor) ?>; border-color: <?php echo esc_attr($this->buttonColor) ?>; color:#ffffff' href='<?php echo esc_url($link) ?>' class='button-secondary'><?php esc_html_e("Upload a new file", "folders") ?></a></p><p><?php esc_html_e("Click on the button to replace the file with another file", "folders") ?></p>
            <?php } else { ?>
                <p><a style='color: <?php echo esc_attr($this->buttonColor) ?>; font-weight: 500' target='_blank' href='<?php echo esc_url($this->upgradeLink) ?>' ><?php esc_html_e("Upgrade to Pro", "folders") ?></a><?php esc_html_e("to replace any kind of files while uploading including pdf/svg/docx/etc & more.", "folders") ?></p>
            <?php }
        }
    }

    /**
     * Add Metabox to replace file name with title
     *
     * @since 2.6.3
     * @access public
     *
     */
    public function change_file_name_box($post) {
        if(current_user_can("upload_files")) { ?>
            <p class="upgrade-bottom">
                <label for="change_file_name"><input disabled type="checkbox" id="change_file_name" name="premio_change_file_name" value="yes"> <?php esc_html_e("Change file name according to title", "folders") ?></label>
            </p>
            <div class="upgrade-box">
                <a href="<?php echo esc_url($this->upgradeLink) ?>" target="_blank"><?php esc_html_e("Upgrade to Pro", "folders"); ?></a>
            </div>
            <?php
        }
    }

    /**
     * Add metabox in media edit page
     *
     * @since 2.6.3
     * @access public
     * @return $size
     *
     */
    public function attachment_editor($form_fields, $post)
    {
        $screen = null;
        if (function_exists('get_current_screen'))
        {
            $screen = get_current_screen();

            if(! is_null($screen) && $screen->id == 'attachment') // hide on edit attachment screen.
                return $form_fields;
        }

        if(current_user_can("upload_files")) {
            if (wp_attachment_is('image', $post->ID)) {
                $link = $this->getMediaReplaceURL($post->ID);
                $form_fields["folders"] = [
                    "label" => esc_html__("Replace media", "folders"),
                    "input" => "html",
                    "html" => "<a style='background: " . esc_attr($this->buttonColor) . "; border-color: " . esc_attr($this->buttonColor) . "; color:#ffffff' href='" . esc_url($link) . "' class='button-secondary'>" . esc_html__("Upload a new file", "folders") . "</a>",
                    "helps" => esc_html__("Click on the button to replace the file with another file", "folders"),
                ];
            } else {
                $form_fields["folders"] = [
                    "label" => esc_html__("Replace media", "folders"),
                    "input" => "html",
                    "html" => "<div style='border: solid 1px #c0c0c0; padding: 10px; border-radius: 2px; background: #ececec;'><a style='color: " . esc_attr($this->buttonColor) . "; font-weight: 500' target='_blank' href='" . esc_url($this->upgradeLink) . "' >" . esc_html__("Upgrade to Pro", "folders") . "</a> " . esc_html__("to replace media files other than images", "folders") . "</div>",
                    "helps" => esc_html__("Click on the button to replace the file with another file", "folders"),
                ];
            }
        }

        return $form_fields;
    }

    /**
     * Replace filenanme with title
     *
     * @since 2.6.3
     * @access public
     * @return $size
     *
     */
    public function attachment_replace_name_with_title($form_fields, $post)
    {
        $screen = null;
        if (function_exists('get_current_screen'))
        {
            $screen = get_current_screen();

            if(! is_null($screen) && $screen->id == 'attachment') // hide on edit attachment screen.
                return $form_fields;
        }

        if(current_user_can("upload_files")) {
            $form_fields["replace_file_name"] = array(
                "label" => esc_html__("Replace media", "folders"),
                "input" => "html",
                "html" => "<label for='attachment_title_" . esc_attr($post->ID) . "' data-post='" . esc_attr($post->ID) . "' data-nonce='" . wp_create_nonce('change_attachment_title_' . $post->ID) . "'><input id='attachment_title_" . esc_attr($post->ID) . "' type='checkbox' class='folder-replace-checkbox' value='" . esc_attr($post->ID) . "'>" . esc_html__("Update file name with title") . "</label><a href='" . $this->upgradeLink . "' target='_blank' style='background: " . esc_attr($this->buttonColor) . "; border-color: " . esc_attr($this->buttonColor) . "; color:#ffffff' type='button' class='button update-name-with-title' >" . esc_html__("Upgrade to Pro", "folders") . "</a>",
                "helps" => ""
            );
        }

        return $form_fields;
    }

    /**
     * Get file size
     *
     * @since 2.6.3
     * @access public
     * @return $size
     *
     */
    public function getFileSize($attachment_id) {
        $size = filesize( get_attached_file( $attachment_id ));
        if($size > 1000000) {
            $size = ($size/1000000);
            return number_format((float)$size, 2, ".", ",")." MB";
        } else if($size > 1000) {
            $size = ($size/1000);
            return number_format((float)$size, 2, ".", ",")." KB";
        }
        return $size." B";
    }

    /**
     * Check for valid date
     *
     * @since 2.6.3
     * @access public
     *
     */

    function validate_date($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
        return $d && $d->format($format) === $date;
    }

    /**
     * Upload file and Replace it
     *
     * @since 2.6.3
     * @access public
     *
     */
    public function handle_folders_file_upload() {
        global $wpdb;
        if(isset($_FILES['new_media_file'])) {
            if($_FILES['new_media_file']['error'] == 0) {
                $attachment_id = isset($_GET['attachment_id']) ? sanitize_text_field($_GET['attachment_id']) : '';
                $nonce = isset($_GET['nonce']) ? sanitize_text_field($_GET['nonce']) : '';
                if (!wp_verify_nonce($nonce, "folders-replace-media-" . $attachment_id)) {
                    return;
                }
                $attachment = get_post($attachment_id);
                if (empty($attachment) || !isset($attachment->guid)) {
                    return;
                }
                $attachment_url = $attachment->guid;
                $url = wp_get_attachment_url($attachment_id);
                if(!empty($url)) {
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

                if(!isset($wpmime[$ext]) && !in_array($file['type'], $wpmime)) {
                    wp_die(esc_html__("Sorry, this file type is not permitted for security reasons", "folders"));
                }

                if(!in_array($ext, ['jpg', 'png', 'jpeg', 'gif', 'svg'])) {
                    wp_die(esc_html__("Sorry, this file type is not permitted for security reasons", "folders"));
                }

                if(!in_array($file['type'], ['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml'])) {
                    wp_die(esc_html__("Sorry, this file type is not permitted for security reasons", "folders"));
                }

                if($file_ext == "svg" || $file['type'] == 'image/svg+xml') {
                    $status = sanitizeSvgFileContent($file['tmp_name']);
                    if(!$status) {
                        wp_die(esc_html__("Sorry, this file type is not permitted for security reasons", "folders"));
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
                $db_file_name = implode(".", $db_file_array).".";
                $db_file_name .= $file_ext;

                $wp_upload_path = wp_get_upload_dir();

                $base_path = $old_path = $wp_upload_path['basedir'].DIRECTORY_SEPARATOR;
                $baseurl = $old_url = $wp_upload_path['baseurl']."/";

                $post_upload = "";

                $wp_attached_file = get_post_meta($attachment_id, "_wp_attached_file", true);
                if($wp_attached_file !== false) {
                    $old_file_name = explode("/", $wp_attached_file);
                    array_pop($old_file_name);

                    if(count($old_file_name) > 0) {
                        $old_path .= implode(DIRECTORY_SEPARATOR, $old_file_name);
                        $old_url .= implode("/", $old_file_name);
                    }

                    if(count($old_file_name) > 0) {
                        $baseurl .= implode(DIRECTORY_SEPARATOR, $old_file_name);
                        $base_path .= implode("/", $old_file_name);
                        $post_upload = implode("/", $old_file_name);
                    }
                }

                $upload_dir = [];
                $upload_dir['path'] = $base_path;
                $upload_dir['old_path'] = $old_path;
                $upload_dir['url'] = $baseurl;
                $upload_dir['old_url'] = $old_url;

                $this->upload_dir = $upload_dir;

                $this->old_file_path = $old_path . "/" . $file_parts['basename'];

                if (!is_dir($base_path)) {
                    mkdir($base_path, 755, true);
                }

                if (is_dir($base_path)) {

                    $file_array = explode(".", $file['name']);
                    $file_ext = array_pop($file_array);
                    $new_file_name = sanitize_title(implode(".", $file_array)).".".$file_ext;

                    if($replacement_option == "replace_only_file") {
                        $new_file_name = $db_file_name;
                    }

                    if(strtolower($new_file_name) != strtolower($file_parts['basename'])) {
                        $new_file_name = $this->checkForFileName($new_file_name, $base_path . DIRECTORY_SEPARATOR);
                    }

                    $this->new_file_path = $base_path . DIRECTORY_SEPARATOR . $new_file_name;

                    $status = move_uploaded_file($new_file, $this->new_file_path);

                    $this->new_file_url = trim($baseurl, "/")."/".$new_file_name;

                    if ($status) {
                        $old_file_path = str_replace(array("/",DIRECTORY_SEPARATOR), array("", ""), $this->old_file_path);
                        $new_file_path = str_replace(array("/",DIRECTORY_SEPARATOR), array("", ""), $this->new_file_path);
                        if($old_file_path != $new_file_path) {
                            if(file_exists($this->old_file_path)) {
                                @wp_delete_file($this->old_file_path);
                            }
                        }

                        update_attached_file($attachment->ID, $this->new_file_url);

                        $update_array = [];
                        $update_array['ID'] = $attachment->ID;
                        $update_array['guid'] = $this->new_file_url; //wp_get_attachment_url($this->post_id);
                        $update_array['post_mime_type'] = $file['type'];

                        $current_date = date("Y-m-d H:i:s");
                        $current_date_gmt = date_i18n("Y-m-d H:i:s", strtotime($current_date));

                        $update_array['post_modified'] = $current_date;
                        $update_array['post_modified_gmt'] = $current_date_gmt;
                        $post_id = wp_update_post($update_array, true);

                        update_post_meta( $attachment_id, '_wp_attached_file', trim(trim($post_upload, "/")."/".$new_file_name , "/"));

                        // update post doesn't update GUID on updates.
                        $wpdb->update($wpdb->posts, array('guid' => $this->new_file_url), array('ID' => $attachment->ID));

                        $this->removeThumbImages();

                        $metadata = wp_generate_attachment_metadata($attachment->ID, $this->new_file_path);
                        wp_update_attachment_metadata($attachment->ID, $metadata);

                        $this->new_image_meta = wp_get_attachment_metadata($attachment_id);

//                        update_post_meta( $attachment_id, '_wp_attached_file', trim(trim($post_upload, "/")."/".$new_file_name ), "/");

                        $this->searchAndReplace();

                        delete_post_meta($attachment_id, "folders_file_replaced");
                        add_post_meta( $attachment_id, "folders_file_replaced", time(), true);

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
     * Check for filename
     *
     * @since 2.6.3
     * @access public
     *
     */
    public function checkForFileName($fileName, $filePath, $postFix = 0) {
        $new_file_name = $fileName;
        if(!empty($postFix)) {
            $file_array = explode(".", $fileName);
            $file_ext = array_pop($file_array);
            $new_file_name = implode(".", $file_array)."-".$postFix.".".$file_ext;
        }
        if(!file_exists($filePath.$new_file_name)) {
            return $new_file_name;
        }
        return $this->checkForFileName($fileName, $filePath, ($postFix+1));
    }

    public $replace_items = [];

    /**
     * Check and Remove Thumb image in wp-content
     *
     * @since 2.6.3
     * @access public
     *
     */
    public function removeThumbImages() {
        if(!empty($this->old_image_meta) && isset($this->old_image_meta['sizes']) && !empty($this->upload_dir) && isset($this->upload_dir['path'])) {
            $path = $this->upload_dir['old_path'].DIRECTORY_SEPARATOR;
            foreach ($this->old_image_meta['sizes'] as $image) {
                if(file_exists($path.$image['file']) && (!isset($image['mime-type']) || $image['mime-type'] != 'image/svg+xml')) {
                    @wp_delete_file($path . $image['file']);
                }
            }
        }
    }

    /**
     * Search and Replace files in Database
     *
     * @since 2.6.3
     * @access public
     * @return $string
     *
     */
    public function searchAndReplace() {
        if (wp_attachment_is('image', $this->attachment_id)) {
            $this->is_new_image = 1;
        }
        if($this->old_file_url != $this->new_file_url) {
            $replace = array(
                'search' => $this->old_file_url,
                'replace' => $this->new_file_url,
            );
            $this->replace_items[] = $replace;
        }

        $base_url = $this->upload_dir['url'];
        $base_url = trim($base_url, "/")."/";
        $new_url = $this->new_file_url;

        if(isset($this->old_image_meta['sizes']) && !empty($this->old_image_meta['sizes'])) {
            if(!isset($this->new_image_meta['sizes']) || empty($this->new_image_meta['sizes'])) {
                foreach ($this->old_image_meta['sizes'] as $key=>$image) {
                    $replace = array(
                        'search' => $base_url.$image['file'],
                        'replace' => $new_url,
                    );
                    $this->replace_items[] = $replace;
                }
            } else if(isset($this->new_image_meta['sizes']) && !empty($this->new_image_meta['sizes'])) {
                $new_size = $this->new_image_meta['sizes'];
                foreach ($this->old_image_meta['sizes'] as $key=>$image) {
                    $new_replace_url = $new_url;
                    if(isset($new_size[$key])) {
                        $new_replace_url = $base_url.$new_size[$key]['file'];
                    }
                    $replace = array(
                        'search' => $base_url.$image['file'],
                        'replace' => $new_replace_url,
                    );
                    $this->replace_items[] = $replace;
                }
            }
        }

        if(!empty($this->replace_items)) {
            $replace_items = [];
            foreach($this->replace_items as $args) {
                if($args['search'] != $args['replace']) {
                    $replace_items[] = $args;
                }
            }
            $this->replace_items = $replace_items;
            $this->replaceURL();
        }
    }

    /**
     * Replace URL in Database tables
     *
     * @since 2.6.3
     * @access public
     * @return $string
     *
     */
    function replaceURL() {
        /* check in post content */
        $this->checkInPostContent();

        /* check in options */
        $this->checkInOptions();

        /* check in meta */
        $this->checkInMetaData();

        if(function_exists('folders_pro_clear_all_caches')) {
            folders_pro_clear_all_caches();
        }
    }

    /**
     * Checking image URLs in Post Content
     *
     * @since 2.6.3
     * @access public
     * @return $string
     *
     */
    function checkInPostContent() {
        global $wpdb;
        $post_table = $wpdb->prefix."posts";
        if(!empty($this->replace_items)) {
            $query = "SELECT ID, post_content FROM {$post_table} WHERE post_content LIKE %s";
            $update_query = "UPDATE {$post_table} SET post_content = %s WHERE ID = %d";
            foreach ($this->replace_items as $args) {
                if($args['search'] != $args['replace']) {
                    $sql_query = $wpdb->prepare($query, "%".$args['search']."%");
                    $results = $wpdb->get_results($sql_query, ARRAY_A );
                    if(!empty($results)) {
                        foreach ($results AS $row) {
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
     * Checking image URLs in MetaData
     *
     * @since 2.6.3
     * @access public
     * @return $string
     *
     */
    function checkInOptions() {
        global $wpdb;
        $post_table = $wpdb->prefix."options";
        if(!empty($this->replace_items)) {
            $query = "SELECT option_id, option_value FROM {$post_table} WHERE option_value LIKE %s";
            $update_query = "UPDATE {$post_table} SET option_value = %s WHERE option_id = %d";
            foreach ($this->replace_items as $args) {
                if($args['search'] != $args['replace']) {
                    $sql_query = $wpdb->prepare($query, "%".$args['search']."%");
                    $results = $wpdb->get_results($sql_query, ARRAY_A );
                    if(!empty($results)) {
                        foreach ($results AS $row) {
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
     * Checking image URLs in MetaData
     *
     * @since 2.6.3
     * @access public
     * @return $string
     *
     */
    function checkInMetaData() {
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
     * Checking for Array Key
     *
     * @since 2.6.3
     * @access public
     * @return $json
     * Forked from Enable Media Replace
     *
     */
    function findAndReplaceContent($content, $search, $replace, $depth = false) {
        $content = maybe_unserialize($content);

        // Checking for JSON Data
        $isJson = $this->isJSON($content);
        if ($isJson) {
            $content = json_decode($content);
        }

        // Replace content if content is String
        if (is_string($content)) {
            $content = str_replace($search, $replace, $content);
        }
        else if(is_wp_error($content)) {   // Return if error in data

        }
        else if(is_array($content)) {   // Replace content if content is Array
            foreach($content as $index => $value) {
                $content[$index] = $this->findAndReplaceContent($value, $search, $replace, true);
                if (is_string($index))  {
                    $index_replaced = $this->findAndReplaceContent($index, $search, $replace, true);
                    if ($index_replaced !== $index)
                        $content = $this->changeArrayKey($content, array($index => $index_replaced));
                }
            }
        }
        else if(is_object($content)) {   // Replace content if content is Object
            foreach($content as $key => $value) {
                $content->{$key} = $this->findAndReplaceContent($value, $search, $replace, true);
            }
        }

        if ($isJson && $depth === false) {
            $content = wp_json_encode($content, JSON_UNESCAPED_SLASHES);
        }
        else if($depth === false && (is_array($content) || is_object($content))) {
            $content = maybe_serialize($content);
        }

        return $content;
    }

    /**
     * Checking for Array Key
     *
     * @since 2.6.3
     * @access public
     * @return $json
     *
     */
    function changeArrayKey($array, $set) {
        if (is_array($array) && is_array($set)) {
            $newArray = [];
            foreach ($array as $k => $v) {
                $key = array_key_exists( $k, $set) ? $set[$k] : $k;
                $newArray[$key] = is_array($v) ? $this->changeArrayKey($v, $set) : $v;
            }
            return $newArray;
        }
        return $array;
    }

    /**
     * Check if it is JSON or not
     *
     * @since 2.6.3
     * @access public
     * Forked from Enable Media Replace
     * @return $json
     *
     */
    function isJSON($content)
    {
        if (is_array($content) || is_object($content))
            return false;

        $json = json_decode($content);
        return $json && $json != $content;
    }
}
$folders_replace_media = new folders_replace_media();
