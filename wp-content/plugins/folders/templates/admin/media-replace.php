<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if (!current_user_can('upload_files'))
    wp_die(esc_html__('You do not have permission to upload files.', 'enable-media-replace'));

global $wpdb;

$attachment_id = intval(sanitize_text_field($_GET['attachment_id']));
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
$current_date = date_i18n('d/m/Y', strtotime($attachment->post_date) );
$file_type = get_post_mime_type($attachment_id);

$maxUploadSize = ini_get("upload_max_filesize");
$maxUploadSize = str_replace(["K", "M", "G", "T", "P"],[" KB", " MB", " GB", " TB", " PB"], $maxUploadSize);

/*
 * Forked from Enable Media Replace
 *
 * */
?>

<div class="wrap">
    <h2><?php esc_html_e("Replace Media", "folders"); ?></h2>
    <form enctype="multipart/form-data" method="POST" action="">
        <div class="replace-media-page">
            <div class="replace-title"><?php esc_html_e("Replace Your File", "folders") ?></div>
            <p><?php esc_html_e("Upload a new file instead of the current one", "folders") ?></p>
            <input type="hidden" name="attachment_id" value="<?php echo esc_attr__($attachment_id) ?>"/>
            <input type="hidden" name="ext" id="file_ext" value="<?php echo esc_attr__($ext) ?>"/>
            <div class="media-top-box">
                <div class="current-image-box">
                    <div class="preview-box">
                        <?php if (wp_attachment_is('image', $attachment_id)) { ?>
                            <?php if(!empty($url)) { ?>
                                <img src="<?php echo esc_url($url) ?>" />
                                <span class="file-dimension"><?php esc_html_e("Dimension: ", "folders"); ?><?php echo esc_attr($image_meta['width']." x ".$image_meta['height']) ?></span>
                                <!--<div class="img-overlay default">
                                    <span class="file-name"><?php /*esc_html_e("File name: ", "folders"); */?><?php /*echo esc_attr($file_name) */?></span>
                                    <?php /*if($file_type) { */?>
                                        <span class="file-size"><?php /*esc_html_e("Type: ", "folders"); */?><?php /*echo esc_attr($file_type) */?></span>
                                    <?php /*} */?>
                                    <span class="file-size"><?php /*esc_html_e("Dimension: ", "folders"); */?><?php /*echo esc_attr($image_meta['width']." x ".$image_meta['height']) */?></span>
                                    <span class="file-date"><?php /*esc_html_e("Date: ", "folders"); */?><?php /*echo esc_attr($current_date) */?></span>
                                </div>-->
                                <div class="upgrade-link-btn">
                                    <a href="<?php echo esc_url($this->upgradeLink) ?>" target="_blank"><?php esc_html_e("Upgrade to Pro", "folders"); ?></a> to compare size
                                </div>
                            <?php } ?>
                        <?php } else { ?>
                            <div class="upgrade-link-btn">
                                <a href="<?php echo esc_url($this->upgradeLink) ?>" target="_blank"><?php esc_html_e("Upgrade to Pro", "folders"); ?></a> to compare size
                            </div>
                            <span class="dashicons dashicons-media-document"></span>
                        <?php } ?>
                    </div>
                </div>
                <div class="img-separator">
                    <svg width="57" height="58" viewBox="0 0 57 58" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="1.55556" y="2.55556" width="24.8889" height="24.8889" stroke="#B8B9BF" stroke-width="3.11111"/>
                        <rect x="29.5556" y="30.5556" width="24.8889" height="24.8889" stroke="#F0EFF2" stroke-width="3.11111"/>
                        <path d="M54.4446 22.7778C54.4446 16.5556 56.0002 4.11111 37.3335 5.66667M37.3335 5.66667L40.4446 1M37.3335 5.66667L40.4446 10.3333" stroke="#B8B9BF" stroke-width="3.11111"/>
                        <path d="M1.59787 35.2222C1.59787 41.4444 0.0423174 53.8889 18.709 52.3333M18.709 52.3333L15.5979 57M18.709 52.3333L15.5979 47.6667" stroke="#B8B9BF" stroke-width="3.11111"/>
                    </svg>
                </div>
                <div class="new-image-box">
                    <div class="preview-box">
                        <div class="container" >
                            <div class="container" >
                                <input type="file" name="new_media_file" id="media_file">
                            </div>
                        </div>
                    </div>
                    <div class="file-size"></div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="file-type-message">
                <?php esc_html_e("For replacing a file the file extension should be the same ex. .png files can not be changed by .pdf file. Make sure you uploaded same types of file.", "folders"); ?>
            </div>
            <div class="file-type warning replace-message">
                <span class="dashicons dashicons-warning"></span> <?php esc_html_e("Replacement file is not the same filetype. This might cause unexpected issues", "folders"); ?>
            </div>

            <div class="pro-file-feature">
                <div class="pro-file-content">
                    <div class="replace-name-settings">
                        <div class="replace-name-settings-left">
                            <label for="replacement_option" class="replace-file-title"><?php esc_html_e("Replace Name", "folders"); ?></label>
                            <label for="replacement_option" class="replace-desc"><?php esc_html_e("Also replace file name with new file name and update all links", "folders"); ?></label>
                        </div>
                        <div class="replace-name-settings-right">
                            <div class="inline-switch">
                                <input type="hidden" name="replacement_option" value="replace_only_file" class="sr-only">
                                <input type="checkbox" disabled name="replacement_option" id="replacement_option" value="replace_file_with_name" class="sr-only">
                                <label for="replacement_option" class="inline-checkbox"></label>
                            </div>
                        </div>
                    </div>

                    <div class="replace-name-settings">
                        <div class="replace-name-settings-left">
                            <label class="replace-file-title"><?php esc_html_e("Replace Date", "folders"); ?></label>
                            <label class="replace-desc"><?php esc_html_e("Also replace file date with", "folders"); ?></label>
                        </div>
                    </div>

                    <div class="date-options">
                        <div class="inline-radio">
                            <input class="sr-only" type="hidden" name="date_options" value="keep_date" >
                            <input class="sr-only" type="radio" name="date_options" disabled value="replace_date" id="replace_date">
                            <label for="replace_date"><?php printf(esc_html__("Use Today's Date (%s)", "folders"), date("m/d/Y")); ?></label>
                        </div>
                        <div class="inline-radio">
                            <input class="sr-only" type="radio" checked name="date_options" disabled value="keep_date" id="keep_date">
                            <label for="keep_date"><?php esc_html_e("Keep Old Date", "folders"); ?></label>
                        </div>
                        <div class="inline-radio">
                            <input class="sr-only" type="radio" name="date_options" disabled value="custom_date" id="select_custom_date">
                            <label for="select_custom_date"><?php esc_html_e("Replace the date with", "folders"); ?></label>
                        </div>
                        <div class="custom-date" id="custom-date">
                            <label for="custom_date"><?php esc_html_e("Custom date", "folders"); ?></label>
                            <input type="text" class="media-date" name="custom_date" value="<?php echo date("m/d/Y H:i") ?>" id="custom_date">
                            <label for="custom_date" class="cal-button">
                                <svg width="12" height="13" viewBox="0 0 12 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M11 1H9V0H8V1H4V0H3V1H1C0.45 1 0 1.45 0 2V12C0 12.55 0.45 13 1 13H11C11.55 13 12 12.55 12 12V2C12 1.45 11.55 1 11 1ZM11 12H1V5H11V12ZM11 4H1V2H3V3H4V2H8V3H9V2H11V4Z" fill="#B6B6B6"/>
                                </svg>
                            </label>
                            <!--</span><span class="inline-block">@</span><span class="inline"><input type="text" name="custom_date_hour" class="media-time"  value="<?php /*echo date("m") */?>" id="custom_date_hour"></span><span class="inline-block">:</span><span class="inline"><input type="text" class="media-time" name="custom_date_min" value="<?php /*echo date("i") */?>" id="custom_date_min">-->
                        </div>
                        <div class="custom-date" id="custom-path">
                            <input type="hidden" name="new_folder_option" value="0">
                            <div class="inline-checkbox">
                                <input type="checkbox" class="sr-only" disabled id="new_folder_option" name="new_folder_option" value="1">
                                <label for="new_folder_option">
                                    <span>
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M17.5303 9.53033C17.8232 9.23744 17.8232 8.76256 17.5303 8.46967C17.2374 8.17678 16.7626 8.17678 16.4697 8.46967L17.5303 9.53033ZM9.99998 16L9.46965 16.5304C9.76255 16.8232 10.2374 16.8232 10.5303 16.5303L9.99998 16ZM7.53027 12.4697C7.23737 12.1768 6.7625 12.1768 6.46961 12.4697C6.17671 12.7626 6.17672 13.2374 6.46961 13.5303L7.53027 12.4697ZM16.4697 8.46967L9.46965 15.4697L10.5303 16.5303L17.5303 9.53033L16.4697 8.46967ZM6.46961 13.5303L9.46965 16.5304L10.5303 15.4697L7.53027 12.4697L6.46961 13.5303Z" />
                                        </svg>
                                    </span>
                                    <?php esc_html_e("Put new upload in updated folder", "folders"); ?>
                                </label>
                            </div>
                            <span class="inline"><input disabled type="text" class="media-date" name="new_folder_path" value="<?php echo date("Y/m") ?>" id="new_folder_path"></span>
                        </div>
                    </div>
                </div>
                <div class="pro-file-popup">
                    <a href="<?php echo esc_url($this->upgradeLink) ?>" target="_blank"><?php esc_html_e("Upgrade to Pro", "folders"); ?></a>
                </div>
            </div>

            <div class="replace-media-buttons">
                <button type="submit" class="button button-primary" disabled><?php esc_html_e("Replace File", "folders") ?></button>
                <button type="button" class="button button-secondary" onclick="history.back();"><?php esc_html_e("Cancel", "folders") ?></button>
            </div>
        </div>
    </form>
</div>
