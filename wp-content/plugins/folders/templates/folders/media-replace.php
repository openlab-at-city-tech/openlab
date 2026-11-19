<?php
if (!defined('ABSPATH')) exit;

if (!current_user_can('upload_files'))
    wp_die(esc_html__('You do not have permission to upload files.', 'folders'));

global $wpdb;

$attachment_id = intval(sanitize_text_field($_GET['attachment_id']));
$attachment = get_post($attachment_id); 
$size = 0;
if (isset($attachment->guid)) {
    $size = $this->getFileSize($attachment_id);
}
//$url = wp_get_attachment_url($attachment_id); die;
$guid = $attachment->guid;
$url = wp_get_attachment_url($attachment_id);
if (!empty($url)) {
    $guid = $url;
}
$guid = explode(".", $guid);
$ext = array_pop($guid);
$image_meta = wp_get_attachment_metadata($attachment_id);
$thumb = wp_get_attachment_image_src($attachment_id, 'thumbnail');
$source_type = get_post_mime_type($attachment_id);
$url = "";
if (isset($thumb[0])) {
    $url = $thumb[0];
}
$file_parts = pathinfo($attachment->guid);
$file_name = $file_parts['basename'];
$current_date = date_i18n('d/m/Y', strtotime($attachment->post_date));
$file_type = get_post_mime_type($attachment_id);

$maxUploadSize = ini_get("upload_max_filesize");
$maxUploadSize = str_replace(["K", "M", "G", "T", "P"], [" KB", " MB", " GB", " TB", " PB"], $maxUploadSize);
$upgradeURL = \Folders\Admin\License::get_pro_url();

/*
 * Forked from Enable Media Replace
 *
 * */
?>

<div class="wrap">
    <h2><?php esc_html_e("Replace Media", 'folders'); ?></h2>
    <form enctype="multipart/form-data" method="POST" action="">
        <div class="replace-media-page">
            <div class="replace-title"><?php esc_html_e("Replace Your File", 'folders') ?></div>
            <p><?php esc_html_e("Upload a new file instead of the current one", 'folders') ?></p>
            <input type="hidden" name="attachment_id" value="<?php echo esc_attr($attachment_id) ?>"/>
            <input type="hidden" name="ext" id="file_ext" value="<?php echo esc_attr($ext) ?>"/>
            <div class="media-top-box">
                <div class="current-image-box">
                    <div class="preview-box">
                        <?php if (wp_attachment_is('image', $attachment_id)) { ?>
                            <?php if (!empty($url)) { ?>
                                <img src="<?php echo esc_url($url) ?>"/>
                                <div class="img-overlay">
                                    <span class="file-name"><?php esc_html_e("File name: ", 'folders'); ?><?php echo esc_attr($file_name) ?></span>
                                    <?php if ($file_type) { ?>
                                        <span class="file-size"><?php esc_html_e("Type: ", 'folders'); ?><?php echo esc_attr($file_type) ?></span>
                                    <?php } ?>
                                    <span class="file-size"><?php esc_html_e("Dimension: ", 'folders'); ?><?php echo esc_attr($image_meta['width'] . " x " . $image_meta['height']) ?></span>
                                    <span class="file-date"><?php esc_html_e("Date: ", 'folders'); ?><?php echo esc_attr($current_date) ?></span>
                                    <span class="file-date"><?php esc_html_e("Size: ", 'folders'); ?><?php echo esc_attr($size) ?></span>
                                </div>
                            <?php } ?>
                        <?php } else { ?>
                            <div class="img-overlay">
                                <span class="file-name"><?php esc_html_e("File name: ", 'folders'); ?><?php echo esc_attr($file_name) ?></span>
                                <?php if ($file_type) { ?>
                                    <span class="file-size"><?php esc_html_e("Type: ", 'folders'); ?><?php echo esc_attr($file_type) ?></span>
                                <?php } ?>
                                <span class="file-date"><?php esc_html_e("Date: ", 'folders'); ?><?php echo esc_attr($current_date) ?></span>
                                <span class="file-date"><?php esc_html_e("Size: ", 'folders'); ?><?php echo esc_attr($size) ?></span>
                            </div>
                            <span class="dashicons dashicons-media-document"></span>
                        <?php } ?>
                    </div>
                </div>
                <div class="img-separator">
                    <svg width="57" height="58" viewBox="0 0 57 58" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="1.55556" y="2.55556" width="24.8889" height="24.8889" stroke="#B8B9BF"
                              stroke-width="3.11111"/>
                        <rect x="29.5556" y="30.5556" width="24.8889" height="24.8889" stroke="#F0EFF2"
                              stroke-width="3.11111"/>
                        <path d="M54.4446 22.7778C54.4446 16.5556 56.0002 4.11111 37.3335 5.66667M37.3335 5.66667L40.4446 1M37.3335 5.66667L40.4446 10.3333"
                              stroke="#B8B9BF" stroke-width="3.11111"/>
                        <path d="M1.59787 35.2222C1.59787 41.4444 0.0423174 53.8889 18.709 52.3333M18.709 52.3333L15.5979 57M18.709 52.3333L15.5979 47.6667"
                              stroke="#B8B9BF" stroke-width="3.11111"/>
                    </svg>
                </div>
                <div class="new-image-box">
                    <div class="preview-box">
                        <div class="container">
                            <div class="container image-preview" id="image-preview">
                                <input type="file" name="new_media_file" id="media_file">
                                <label class="sd-label">
                                    <div class="upload-overlay">
                                        <svg width="28" height="24" viewBox="0 0 28 24" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path d="M2 16.1667V17.6667C2 20.0599 3.9401 22 6.33333 22H21.6667C24.0599 22 26 20.0599 26 17.6667V16.1667M14 17.8333V2M14 2L20.8571 8.66667M14 2L7.14286 8.66667"
                                                  stroke="#E6386C" stroke-width="2.16667"
                                                  stroke-linejoin="round"></path>
                                        </svg>
                                        <span class="upload-title"><?php esc_html_e("Drag and drop files here", 'folders') ?></span>
                                        <span class="upload-size"><?php esc_html_e("Maximum file size 40 MB", 'folders') ?></span>
                                    </div>
                                </label>
                                <div class="img-overlay" id="img-overlay">
                                    <div id="file-name">File name: <span></span></div>
                                    <div id="file-type">Type: <span></span></div>
                                    <div id="file-dimension">Dimension: <span></span></div>
                                    <div id="file-size">Size: <span></span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="file-size"></div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="file-type-message">
                <?php esc_html_e("For replacing a file the file extension should be the same ex. .png files can not be changed by .pdf file. Make sure you uploaded same types of file.", 'folders'); ?>
            </div>
            <div class="file-type warning replace-message">
                <span class="dashicons dashicons-warning"></span> <?php esc_html_e("Replacement file is not the same filetype. This might cause unexpected issues", 'folders'); ?>
            </div>

            <div class="pro-feature-wrap">
                <div class="replace-name-settings">
                    <div class="replace-name-settings-left">
                        <label for="replacement_option" class="replace-file-title"><?php esc_html_e("Replace Name", 'folders'); ?></label>
                        <label for="replacement_option" class="replace-desc"><?php esc_html_e("Also replace file name with new file name and update all links", 'folders'); ?></label>
                    </div>
                    <div class="replace-name-settings-right">
                        <div class="inline-switch">
                            <input type="hidden" name="replacement_option" value="replace_only_file" class="sr-only">
                            <input type="checkbox" name="replacement_option" id="replacement_option" disabled value="replace_file_with_name" class="sr-only">
                            <label for="replacement_option" class="inline-checkbox"></label>
                        </div>
                    </div>
                </div>

                <!-- Replace Title wrap -->
                <div class="replace-name-settings">
                    <div class="replace-name-settings-left">
                        <label for="replacement_title_option" class="replace-file-title"><?php esc_html_e("Replace Title", 'folders'); ?></label>
                        <label for="replacement_title_option" class="replace-desc"><?php esc_html_e("Also replace file title with new file name", 'folders'); ?></label>
                    </div>
                    <div class="replace-name-settings-right">
                        <div class="inline-switch">
                            <input type="hidden" name="replacement_title_option" value="0" class="sr-only">
                            <input type="checkbox" name="replacement_title_option" id="replacement_title_option" disabled value="1" class="sr-only">
                            <label for="replacement_title_option" class="inline-checkbox"></label>
                        </div>
                    </div>
                </div>
                <!-- Replace Title wrap end -->
                <div class="replace-name-settings">
                    <div class="replace-name-settings-left">
                        <label class="replace-file-title"><?php esc_html_e("Replace Date", 'folders'); ?></label>
                        <label class="replace-desc"><?php esc_html_e("Also replace file date with", 'folders'); ?></label>
                    </div>
                </div>

                <div class="date-options">
                    <div class="inline-radio">
                        <input class="sr-only" disabled type="radio" name="date_options" value="replace_date" id="replace_date">
                        <label for="replace_date"><?php printf(esc_html__("Use Today's Date (%s)", 'folders'), esc_attr(gmdate("m/d/Y"))); ?></label>
                    </div>
                    <div class="inline-radio">
                        <input class="sr-only" type="radio" checked name="date_options" value="keep_date" id="keep_date">
                        <label for="keep_date"><?php esc_html_e("Keep Old Date", 'folders'); ?></label>
                    </div>
                    <div class="inline-radio">
                        <input class="sr-only" disabled type="radio" name="date_options" value="custom_date" id="select_custom_date">
                        <label for="select_custom_date"><?php esc_html_e("Replace the date with", 'folders'); ?></label>
                    </div>
                    <div class="custom-date" id="custom-date">
                        <label for="custom_date"><?php esc_html_e("Custom date", 'folders'); ?></label>
                        <input type="text" class="media-date" name="custom_date" disabled value="<?php echo esc_attr(gmdate("m/d/Y H:i")) ?>" id="custom_date">
                        <label for="custom_date" class="cal-button">
                            <svg width="12" height="13" viewBox="0 0 12 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M11 1H9V0H8V1H4V0H3V1H1C0.45 1 0 1.45 0 2V12C0 12.55 0.45 13 1 13H11C11.55 13 12 12.55 12 12V2C12 1.45 11.55 1 11 1ZM11 12H1V5H11V12ZM11 4H1V2H3V3H4V2H8V3H9V2H11V4Z" fill="#B6B6B6"/>
                            </svg>
                        </label>
                        <!--</span><span class="inline-block">@</span><span class="inline"><input type="text" name="custom_date_hour" class="media-time"  value="<?php /*echo date("m") */ ?>" id="custom_date_hour"></span><span class="inline-block">:</span><span class="inline"><input type="text" class="media-time" name="custom_date_min" value="<?php /*echo date("i") */ ?>" id="custom_date_min">-->
                    </div>
                    <div class="custom-date" id="custom-path">
                        <input type="hidden" name="new_folder_option" value="0">
                        <div class="inline-checkbox">
                            <input type="checkbox" class="sr-only" id="new_folder_option" name="new_folder_option" disabled value="1">
                            <label for="new_folder_option">
                                <span>
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path d="M17.5303 9.53033C17.8232 9.23744 17.8232 8.76256 17.5303 8.46967C17.2374 8.17678 16.7626 8.17678 16.4697 8.46967L17.5303 9.53033ZM9.99998 16L9.46965 16.5304C9.76255 16.8232 10.2374 16.8232 10.5303 16.5303L9.99998 16ZM7.53027 12.4697C7.23737 12.1768 6.7625 12.1768 6.46961 12.4697C6.17671 12.7626 6.17672 13.2374 6.46961 13.5303L7.53027 12.4697ZM16.4697 8.46967L9.46965 15.4697L10.5303 16.5303L17.5303 9.53033L16.4697 8.46967ZM6.46961 13.5303L9.46965 16.5304L10.5303 15.4697L7.53027 12.4697L6.46961 13.5303Z"/>
                                    </svg>
                                </span>
                                <?php esc_html_e("Put new upload in updated folder", 'folders'); ?>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="pro-feature-overlay">
                    <div>
                        <div>
                            <svg width="47" height="47" viewBox="0 0 47 47" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_1876_1166)">
                                    <path d="M23.4987 0C16.4722 0.00862891 10.7782 5.70261 10.7695 12.7292V20.5625C10.7695 21.1033 11.208 21.5417 11.7487 21.5417H15.6654C16.2062 21.5417 16.6446 21.1033 16.6446 20.5625V12.7292C16.6445 8.94368 19.7133 5.875 23.4987 5.875C27.2842 5.875 30.3529 8.94368 30.3529 12.7292V20.5625C30.3529 21.1033 30.7913 21.5417 31.3321 21.5417H35.2487C35.7895 21.5417 36.2279 21.1033 36.2279 20.5625V12.7292C36.2193 5.70261 30.5252 0.00862891 23.4987 0Z" fill="black"/>
                                    <path d="M11.7513 19.584H35.2513C37.9551 19.584 40.1471 21.7759 40.1471 24.4798V42.1048C40.1471 44.8088 37.9551 47.0007 35.2513 47.0007H11.7513C9.04739 47.0007 6.85547 44.8088 6.85547 42.1049V24.4799C6.85547 21.7759 9.04739 19.584 11.7513 19.584Z" fill="#FFB743"/>
                                    <path d="M28.3971 30.354C28.4085 27.6501 26.2258 25.4489 23.522 25.4375C20.8181 25.4262 18.6169 27.6088 18.6055 30.3127C18.5976 32.1817 19.6545 33.892 21.3295 34.7211L20.5735 40.0086C20.4978 40.544 20.8705 41.0394 21.406 41.1152C21.4513 41.1216 21.4971 41.1248 21.5429 41.1248H25.4596C26.0004 41.1303 26.4432 40.6964 26.4486 40.1556C26.4491 40.1058 26.4459 40.0559 26.4387 40.0066L25.6828 34.7191C27.3373 33.8911 28.3864 32.204 28.3971 30.354Z" fill="black"/>
                                </g>
                                <defs>
                                    <clipPath id="clip0_1876_1166">
                                        <rect width="47" height="47" fill="white"/>
                                    </clipPath>
                                </defs>
                            </svg>
                        </div>
                        <div class="replace-title"><?php esc_html_e('Media replacement', 'folders'); ?></div>
                        <div class="replace-pro-desc"><?php esc_html_e('Replace images without breaking existing links on your pages or posts.', 'folders'); ?></div>
                        <div class="replace-pro-button">
                            <a href="<?php echo esc_url($upgradeURL) ?>" target="_blank" class="">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1.7733 13.1104C1.39311 10.6392 1.01292 8.16797 0.632733 5.69672C0.548421 5.14891 1.17173 4.77528 1.61511 5.10784C2.79961 5.99622 3.98405 6.88453 5.16855 7.77291C5.55855 8.06541 6.11417 7.97022 6.38455 7.56459L9.34286 3.12709C9.65548 2.65816 10.3445 2.65816 10.6571 3.12709L13.6154 7.56459C13.8858 7.97022 14.4414 8.06534 14.8314 7.77291C16.0159 6.88453 17.2004 5.99622 18.3849 5.10784C18.8282 4.77528 19.4515 5.14891 19.3673 5.69672C18.9871 8.16797 18.6069 10.6392 18.2267 13.1104H1.7733Z" fill="currentColor"/>
                                    <path d="M17.369 17.2246H2.63125C2.1575 17.2246 1.77344 16.8405 1.77344 16.3668V14.4824H18.2269V16.3668C18.2268 16.8405 17.8428 17.2246 17.369 17.2246Z" fill="currentColor"/>
                                </svg>
                                <span class=""><?php esc_html_e('Upgrade to Pro', 'folders'); ?></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="replace-media-buttons">
                <button type="submit" class="button button-primary" disabled><?php esc_html_e("Replace File", 'folders') ?></button>
                <button type="button" class="button button-secondary" onclick="history.back();"><?php esc_html_e("Cancel", 'folders') ?></button>
            </div>
        </div>
    </form>
</div>
