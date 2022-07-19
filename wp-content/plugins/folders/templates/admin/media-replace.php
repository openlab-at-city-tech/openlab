<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if (!current_user_can('upload_files'))
    wp_die(esc_html__('You do not have permission to upload files.', 'enable-media-replace'));

global $wpdb;

$attachment_id = intval($_GET['attachment_id']);
$attachment = get_post($attachment_id);

$size = 0;
if(isset($attachment->guid)) {
    $size = $this->getFileSize($attachment_id);
}
//$url = wp_get_attachment_url($attachment_id); die;
$guid = $attachment->guid;
$url = wp_get_attachment_url($attachment_id);
if(!empty($url)) {
    $guid = $url;
}
$guid = explode(".", $guid);
$ext = array_pop($guid);
$image_meta = wp_get_attachment_metadata($attachment_id);
$thumb = wp_get_attachment_image_src($attachment_id, 'thumbnail');
$source_type = get_post_mime_type($attachment_id);
$url = "";
if(isset($thumb[0])) {
    $url = $thumb[0];
}
$file_parts = pathinfo($attachment->guid);
$file_name = $file_parts['basename'];

$customize_folders = get_option("customize_folders");
if(isset($customize_folders['show_folder_in_settings']) && $customize_folders['show_folder_in_settings'] == "yes") {
	$upgradeURL = admin_url("options-general.php?page=wcp_folders_settings&setting_page=upgrade-to-pro");
} else {
	$upgradeURL = admin_url("admin.php?page=folders-upgrade-to-pro");
}
$current_date = date_i18n('d/M/Y H:i', strtotime($attachment->post_date) )
?>
<div class="wrap">
    <h2><?php esc_html_e("Replace Media", "folders"); ?></h2>
    <form enctype="multipart/form-data" method="POST" action="<?php echo esc_url($form_action) ?>">
        <div class="replace-media-page">
            <p><b><?php esc_html_e("Current File", "folders") ?></b>: <?php echo esc_attr($file_name) ?></p>
            <p><?php esc_html_e("Upload a new file instead of the current one", "folders") ?></p>
            <p><?php printf(__('Maximum file size: <strong>%s</strong>', 'enable-media-replace'), size_format(wp_max_upload_size())) ?></p>
            <input type="hidden" name="attachment_id" value="<?php echo esc_attr__($attachment_id) ?>"/>
            <input type="hidden" name="ext" id="file_ext" value="<?php echo esc_attr__($ext) ?>"/>
            <div class="upload-media-box">

            </div>
            <div class="">
                <div class="current-image-box">
                    <div class="file-option"><?php esc_html_e("Current File", "folders") ?></div>
                    <div class="preview-box">
                        <?php if (wp_attachment_is('image', $attachment_id)) { ?>
                            <?php if(!empty($url)) { ?>
                                <img src="<?php echo esc_url($url) ?>" />
                                <span class="image-size"><?php echo esc_attr($image_meta['width']." PX x ".$image_meta['height'])." PX" ?></span>
                            <?php } ?>
                        <?php } else { ?>
                                <span class="dashicons dashicons-media-document"></span>
                        <?php } ?>
                    </div>
                    <?php if(!empty($size)) { ?>
                        <div class="file-size"><a target="_blank" href="<?php echo esc_url($upgradeURL) ?>"><?php esc_html_e("Upgrade to Pro", "folders") ?></a> <?php esc_html_e("to compare file size", "folders") ?></div>
                    <?php } ?>
                </div>
                <div class="new-image-box">
                    <div class="file-option"><?php esc_html_e("New File", "folders") ?></div>
                    <div class="preview-box">
                        <div class="container" >
                            <div class="container" >
                                <input type="file" name="new_media_file" id="media_file">

                                <!-- Drag and Drop container-->
                                <div class="upload-area"  id="upload-file">
                                    <div class="drag-and-drop-title">
                                        <span><?php echo esc_html_e("Click here to upload file", "folders") ?></span>
                                    </div>
                                    <div class="upgrade-btn-box">
                                        <a target="_blank" href="<?php echo esc_url($upgradeURL) ?>"><?php esc_html_e("Upgrade to Pro", "folders") ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="file-size hide-it"><a target="_blank" href="<?php echo esc_url($upgradeURL) ?>"><?php esc_html_e("Upgrade to Pro", "folders") ?></a> <?php esc_html_e("to compare file size", "folders") ?></div>
                </div>
                <div class="clearfix"></div>
            </div>

            <div class="file-type warning replace-message">
		        <?php esc_html_e("Replacement file is not the same filetype. This might cause unexpected issues", "folders"); ?>
            </div>

            <div class="media-bottom-box pro">
                <div class="media-bottom-pro">
                    <div class="media-bottom-box-left">
                        <div class="media-setting">
                            <div class="media-title"><?php esc_html_e("Replacement Options", "folders") ?></div>
                            <div class="media-form">
                                <div class="media-option">
                                    <label for="replace_only_file"><input type="radio" checked name="replacement_option" value="replace_only_file" id="replace_only_file" /> <?php esc_html_e("Just replace the file", "folders") ?></label>
                                </div>
                                <div class="media-note">
                                    <?php esc_html_e("Note: This option requires you to upload a file of the same type (png) as the one you are replacing. The name of the attachment will stay the same (image.png) no matter what the file you upload is called.", "folders"); ?>
                                </div>
                                <div class="media-option">
                                    <label for="replace_file_with_name"><input type="radio" name="replacement_option" value="replace_file_with_name" id="replace_file_with_name" /> <?php esc_html_e("Replace the file, use new file name and update all links", "folders") ?></label>
                                </div>
                                <div class="media-note">
                                    <?php esc_html_e("Note: If you check this option, the name and type of the file you are about to upload will replace the old file. All links pointing to the current file (image.png) will be updated to point to the new file name. (If any other websites link to the file directly, those links will no longer work. Be careful.)", "folders"); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="media-bottom-box-right">
                        <div class="media-setting">
                            <div class="media-title"><?php esc_html_e("Date Options", "folders") ?></div>
                            <div class="media-form">
                                <div class="media-note">
                                    <?php esc_html_e("When replacing the media, do you want to:", "folders"); ?>
                                </div>
                                <ul>
                                    <li>
                                        <label for="replace_date"><input type="radio" name="date_options" value="replace_date" id="replace_date" /> <?php esc_html_e("Replace the date", "folders") ?></label>
                                    </li>
                                    <li>
                                        <label for="keep_date"><input type="radio" checked name="date_options" value="keep_date" id="keep_date" /> <?php esc_html_e("Keep the date", "folders") ?> <small>(<?php echo esc_attr($current_date) ?>)</small></label>
                                    </li>
                                    <li>
                                        <label for="select_custom_date"><input type="radio" name="date_options" value="custom_date" id="select_custom_date" /> <?php esc_html_e("Set a Custom Date", "folders") ?></label>
                                    </li>
                                </ul>
                                <div class="custom-date" id="custom-date">
                                    <label for="custom_date"><?php esc_html_e("Custom date", "folders"); ?></label>
                                    <span class="inline"><input type="text" class="media-date" name="custom_date" value="<?php echo date("F j, Y") ?>" id="custom_date"></span><span class="inline-block">@</span><span class="inline"><input type="text" name="custom_date_hour" class="media-time"  value="<?php echo date("m") ?>" id="custom_date_hour"></span><span class="inline-block">:</span><span class="inline"><input type="text" class="media-time" name="custom_date_min" value="<?php echo date("i") ?>" id="custom_date_min"></span>
                                </div>
                                <div class="custom-date" id="custom-path">
                                    <input type="hidden" name="new_folder_option" value="0">
                                    <label for="new_folder_option"><input type="checkbox" id="new_folder_option" name="new_folder_option" value="1"><?php esc_html_e("Put new Upload in Updated Folder", "folders"); ?></label>
                                    <span class="inline"><input type="text" class="media-date" name="new_folder_path" value="<?php echo date("Y/m") ?>" id="new_folder_path"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="media-bottom-pro-button">
                    <div class="pro-button">
                        <a target="_blank" href="<?php echo esc_url($upgradeURL) ?>"><?php esc_html_e("Upgrade to Pro", "folders") ?></a>
                    </div>
                </div>
            </div>

            <div class="replace-media-buttons">
                <button type="submit" class="button button-primary" disabled><?php echo esc_html_e("Replace File", "folders") ?></button>
                <button type="button" class="button button-secondary" onclick="history.back();"><?php echo esc_html_e("Cancel", "folders") ?></button>
            </div>
        </div>
    </form>
</div>
