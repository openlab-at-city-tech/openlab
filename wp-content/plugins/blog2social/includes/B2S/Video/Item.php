<?php

class B2S_Video_Item {

    public function __construct() {
        
    }

    public function getSingleVideoItemHtml($attachment_id = 0) {
        $postData = get_post((int) $attachment_id);
        $videoMeta = wp_read_video_metadata(get_attached_file((int) $attachment_id));
        $videoUrl = wp_get_attachment_url((int) $attachment_id);

        $videoAddonDetails = false;
        if (defined('B2S_PLUGIN_ADDON_VIDEO')) {
            if (!empty(B2S_PLUGIN_ADDON_VIDEO)) {
                $videoAddonDetails = B2S_PLUGIN_ADDON_VIDEO;
            }
        }

        $notice = '';
        $shareVideoBtn = '<button class="b2s-share-video-file btn btn-primary" disabled><i class="glyphicon glyphicon-ban-circle"></i> ' . esc_html__('Share on video networks', 'blog2social') . '</button>';

        if ($videoAddonDetails !== false) {
            if (isset($videoAddonDetails['volume_open']) && ($videoAddonDetails['volume_open'] >= round($videoMeta['filesize'] / 1024))) {
                $shareVideoBtn = '<a class="b2s-share-video-file btn btn-primary" href="admin.php?page=blog2social-ship&isVideo=1&postId=' . esc_attr((int) $attachment_id) . '" data-file-url="' . esc_attr($postData->guid) . '" data-attachment-id="' . esc_attr((int) $attachment_id) . '">' . esc_html__('Share on video networks', 'blog2social') . '</a>';
            } else {
                $notice = '<span class="glyphicon glyphicon-warning-sign"></span> <b>' . esc_html__('Video size exceeds your data volume to share on networks', 'blog2social') . '</b></br>';
            }
        }

        return '<li class="list-group-item b2s-video-upload-list-last-trigger ' . (!empty($notice) ? 'b2s-label-danger-border-left' : '') . '" data-attachment-id="' . esc_attr((int) $attachment_id) . '">
                            <div class="media">
                                <img class="post-img-5 pull-left hidden-xs" src="' . esc_url(plugins_url('/assets/images/b2s/video-icon.png', B2S_PLUGIN_FILE)) . '" alt="posttype">
                                                <div class="media-body">
                                                    <div class="pull-left media-nav">' . $notice . '
                                                            <strong><a target="_blank" href="' . esc_url($postData->guid) . '">' . $postData->post_title . '</a></strong>
                                                                <span class="info hidden-xs">(' . esc_html__('Format', 'blog2social') . ': ' . esc_html($postData->post_mime_type) . ', ' .
                esc_html__('Size', 'blog2social') . ': ' . esc_html(size_format($videoMeta['filesize'])) . ', ' . esc_html__('Length', 'blog2social') .
                ':' . esc_html($videoMeta['length']) . esc_html__('s', 'blog2social') . ')</span>
                                                        <span class="pull-right">
                                                            ' . $shareVideoBtn . '
                                                            <button class="b2s-show-video-uploads btn btn-primary" disabled data-file-url="' . esc_attr($videoUrl) . '" data-attachment-id="' . esc_attr((int) $attachment_id) . '"><i class="glyphicon glyphicon-ban-circle"></i> ' . esc_html__('Details', 'blog2social') . '</button>
                                                        </span>
                                                        <p class="info hidden-xs">' . sprintf(esc_html__('uploaded by %s on %s', 'blog2social'), get_the_author_meta('display_name', $postData->post_author), B2S_Util::getCustomDateFormat($postData->post_date, substr(B2S_LANGUAGE, 0, 2))) . '</p>
                                                    </div>
                                                     <div class="pull-left">
                                                        <div class="b2s-post-video-upload-area" data-attachment-id="' . esc_attr((int) $attachment_id) . '"></div>
                                                    </div>
                                                </div>                                     
                            </div>
                        </li>';
    }

    public function getVideoUploadDataHtml($attachment_id = 0) {
        global $wpdb;
        $content = '';
        $addNotAdminPosts = (!B2S_PLUGIN_ADMIN) ? (" AND `{$wpdb->prefix}b2s_posts`.blog_user_id =" . B2S_PLUGIN_BLOG_USER_ID) : '';
        $sqlData = $wpdb->prepare("SELECT `{$wpdb->prefix}b2s_posts`.`id`,`{$wpdb->prefix}b2s_posts`.`blog_user_id`,`publish_date`,`publish_link`,`publish_error_code`,`{$wpdb->prefix}b2s_posts`.`sched_date`,`{$wpdb->prefix}b2s_posts`.`sched_date_utc`,`post_format`,`hook_action`,`upload_video_token`,`{$wpdb->prefix}b2s_posts_network_details`.`network_id`,`{$wpdb->prefix}b2s_posts_network_details`.`network_type`, `{$wpdb->prefix}b2s_posts_network_details`.`network_auth_id`, `{$wpdb->prefix}b2s_posts_network_details`.`network_display_name` FROM `{$wpdb->prefix}b2s_posts` LEFT JOIN `{$wpdb->prefix}b2s_posts_network_details` ON `{$wpdb->prefix}b2s_posts`.`network_details_id` = `{$wpdb->prefix}b2s_posts_network_details`.`id`  WHERE `{$wpdb->prefix}b2s_posts`.`hide` = 0 AND `{$wpdb->prefix}b2s_posts`.`publish_error_code` = '' $addNotAdminPosts  AND `{$wpdb->prefix}b2s_posts`.`post_id` = %d ORDER BY `{$wpdb->prefix}b2s_posts`.`publish_date` DESC", $attachment_id);
        $result = $wpdb->get_results($sqlData);

        if (!empty($result) && is_array($result)) {
            $networkType = unserialize(B2S_PLUGIN_NETWORK_TYPE);
            $networkName = unserialize(B2S_PLUGIN_NETWORK);
            $networkErrorCode = unserialize(B2S_PLUGIN_NETWORK_ERROR);
            $content = '<div class="row"><div class="col-md-12"><ul class="list-group">';
            $content .= '<li class="list-group-item"><label class="checkbox-inline checkbox-all-label"><input class="checkbox-all" data-attachment-id="' . esc_attr($attachment_id) . '" name="selected-checkbox-all" value="" type="checkbox"> ' . esc_html__('select all', 'blog2social') . '</label></li>';
            foreach ($result as $var) {

                $publishLink = (!empty($var->publish_link)) ? '<a target="_blank" href="' . esc_url($var->publish_link) . '">' . esc_html__('show', 'blog2social') . '</a> | ' : '';
                $reshareLink = (!empty($var->publish_link)) ? '<a href="' . esc_url('admin.php?page=blog2social-curation&type=link&url=' . urlencode($var->publish_link)) . '">' . esc_html__('Share on social media', 'blog2social') . '</a> | ' : '';

                $error = '';
                if (!empty($var->publish_error_code)) {
                    $errorCode = isset($networkErrorCode[trim($var->publish_error_code)]) ? $var->publish_error_code : 'DEFAULT';
                    $error = '<span class="network-text-info text-danger hidden-xs"> <i class="glyphicon glyphicon-remove-circle glyphicon-danger"></i> ' . $networkErrorCode[$errorCode] . $add . '</span>';
                }
                $publishDate = (($var->publish_date != "0000-00-00 00:00:00") && (int) $var->hook_action == 0) ? B2S_Util::getCustomDateFormat($var->publish_date, substr(B2S_LANGUAGE, 0, 2)) : '';
                
                if($var->sched_date== "0000-00-00 00:00:00"){
                    $schedDate = false;
                } else {
                    $schedDate = true;
                }

                if(!$schedDate){
                    if(empty($publishDate)){
                        $publishText = (empty($publishDate)) ? __('uploading in progress by %s', 'blog2social') : __('uploaded by %s', 'blog2social');
                    } else {
                        $publishText = __('uploaded by %s', 'blog2social');
                    }
                } 

                if($schedDate && gmdate('Y-m-d H:i:s') < $var->sched_date_utc){
                    $publishText = __( 'The video was scheduled by %s.', 'blog2social'). " ". esc_html(B2S_Util::getCustomDateFormat($var->sched_date, substr(B2S_LANGUAGE, 0, 2)));
                } else if($schedDate && gmdate('Y-m-d H:i:s') >= $var->sched_date_utc){
                    $publishText = __('uploaded by %s', 'blog2social');
                }

                //special Case
                if ($var->hook_action == 7 && $var->network_id == 36) {
                    $publishText = __('Your video has been posted to TikTok and is now available to be released in the Tiktok mobile app.', 'blog2social');
                }

                $userInfoName = get_the_author_meta('display_name', $var->blog_user_id);
                $content .= ' <li class="list-group-item b2s-post-video-upload-area-li" data-attachment-id="' . esc_attr($var->id) . '">
                                    <div class="media">';

                if (!empty($publishDate)) {
                    $content .= '<input class="checkboxes pull-left checkbox-item" data-attachment-id="' . esc_attr($attachment_id) . '" name="selected-checkbox-item" value="' . esc_attr($var->id) . '" type="checkbox">';
                } else {
                    $content .= '<div class="checbox-item-empty"></div>';
                }

                if (!empty($var->publish_link)) {
                    $content .= '<a class="pull-left" target="_blank" href="' . esc_url($var->publish_link) . '"><img class="pull-left hidden-xs" src="' . esc_url(plugins_url('/assets/images/portale/' . $var->network_id . '_flat.png', B2S_PLUGIN_FILE)) . '" alt="posttype"></a>';
                } else {
                    $content .= '<img class="pull-left hidden-xs" src="' . esc_url(plugins_url('/assets/images/portale/' . $var->network_id . '_flat.png', B2S_PLUGIN_FILE)) . '" alt="posttype">';
                }

                $content .= '<div class="media-body">
                                            <strong>' . esc_html($networkName[$var->network_id]) . '</strong> <span class="info">(' . esc_html($networkType[$var->network_type]) . esc_html((!empty($var->network_display_name) ? (': ' . $var->network_display_name) : '')) . ')</span> ' . $error . '
                                            <div class="info">' . sprintf(esc_html($publishText), '<a href="' . esc_url(get_author_posts_url($var->blog_user_id)) . '">' . esc_html((!empty($userInfoName) ? $userInfoName : '-')) . '</a>') . ' ' . esc_html($publishDate);

                $content .= '</div><p class="info">' . $publishLink;
                if ($var->network_id == 32 || $var->network_id == 35) {
                    $content .= $reshareLink;
                }


                if ((int) $var->hook_action == 0) {
                    $content .= (B2S_PLUGIN_USER_VERSION > 0) ? '<a href="#" class="b2s-post-video-upload-area-drop-btn" data-attachment-id="' . esc_attr($var->id) . '">' : '<a href="#" class="b2sPreFeatureModalBtn" data-title="' . esc_attr__('You want to delete a publish post entry?', 'blog2social') . '">';
                    $content .= esc_html__('delete from reporting', 'blog2social') . '</a> ';
                }

                if (!empty($error)) {
                    $content .= '| <a href="admin.php?page=blog2social-ship&isVideo=1&postId=' . esc_attr($attachment_id) . '&network_auth_id=' . esc_attr($var->network_auth_id) . '">' . esc_html__('re-share', 'blog2social') . '</a>';
                }

                $content .= '</p>
                        </div>
                        </div>
                                </li>';
            }
            $content .= '<li class="list-group-item"><label class="checkbox-inline checkbox-all-label-btn"><span class="glyphicon glyphicon glyphicon-trash "></span> ';
            $content .= B2S_PLUGIN_USER_VERSION > 0 ? '<a class="checkbox-post-video-upload-all-btn" data-attachment-id="' . esc_attr($attachment_id) . '" href="#">' : '<a href="#" class="b2sPreFeatureModalBtn" data-title="' . esc_attr__('You want to delete a publish post entry?', 'blog2social') . '">';
            $content .= esc_html__('delete from reporting', 'blog2social') . '</a></label></li>';
            $content .= '</ul></div></div>';
            return $content;
        }

        return false;
    }

}
