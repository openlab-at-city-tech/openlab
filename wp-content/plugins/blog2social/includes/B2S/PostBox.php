<?php

class B2S_PostBox {

    private $b2sSiteUrl;
    private $postLang;
    private $userOption;

    public function __construct() {
        $this->b2sSiteUrl = get_option('siteurl') . ((substr(get_option('siteurl'), -1, 1) == '/') ? '' : '/');
        $this->postLang = strtolower(substr(get_locale(), 0, 2));
        $this->userOption = new B2S_Options(B2S_PLUGIN_BLOG_USER_ID);
    }

    public function getPostBox($postId = 0, $postType = 'post', $postStatus = '') {
        $autoPostImport = false;
        $autoPostActive = false;
        $selectedProfileId = -1;
        $selectedTwitterId = -1;
        $defaultProfile = 0;
        $defaultTwitter = 0;
        $bestTimesDefault = false;
        $lastPostDate = '---';
        $shareCount = 0;
        $optionAutoPost = $this->userOption->_getOption('auto_post');
        $assigned = false;
        if (isset($optionAutoPost['assignBy']) && (int) $optionAutoPost['assignBy'] > 0 && isset($optionAutoPost['assignProfile']) && (int) $optionAutoPost['assignProfile'] > 0) {
            $assignOptions = new B2S_Options($optionAutoPost['assignBy']);
            $newOptionAutoPost = $assignOptions->_getOption('auto_post');
            $newOptionAutoPost['profile'] = $optionAutoPost['assignProfile'];
            if (isset($optionAutoPost['assignTwitter']) && (int) $optionAutoPost['assignProfile'] > 0) {
                $newOptionAutoPost['twitter'] = $optionAutoPost['assignTwitter'];
            }
            $optionAutoPost = $newOptionAutoPost;
            $assigned = true;
        }

        $optionUserTimeZone = $this->userOption->_getOption('user_time_zone');
        $userTimeZone = ($optionUserTimeZone !== false) ? $optionUserTimeZone : get_option('timezone_string');
        $userTimeZoneOffset = (empty($userTimeZone)) ? get_option('gmt_offset') : B2S_Util::getOffsetToUtcByTimeZone($userTimeZone);
        $b2sHeartbeatFaqLink = '<a target="_blank" href="' . esc_url(B2S_Tools::getSupportLink('system')) . '">' . esc_html__('Please see FAQ', 'blog2social') . '</a>';
        $metaSettings = get_option('B2S_PLUGIN_GENERAL_OPTIONS');

        if ((int) $postId > 0) {
            global $wpdb;
            if ($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}b2s_posts'") == $wpdb->prefix . 'b2s_posts') {
                $lastPost = $wpdb->get_results($wpdb->prepare("SELECT publish_date FROM {$wpdb->prefix}b2s_posts WHERE post_id= %d AND hide = 0 ORDER BY publish_date DESC LIMIT 1", $postId));
                if (!empty($lastPost) && isset($lastPost[0]) && !empty($lastPost[0]->publish_date) && $lastPost[0]->publish_date != '0000-00-00 00:00:00') {
                    $lastPostDate = esc_html(B2S_Util::getCustomDateFormat($lastPost[0]->publish_date, substr(B2S_LANGUAGE, 0, 2)));
                }
                $posts = $wpdb->get_results($wpdb->prepare("SELECT count(id) as shareCount FROM {$wpdb->prefix}b2s_posts WHERE post_id= %d AND hide = 0", $postId));
                if (!empty($posts) && isset($posts[0]) && !empty($posts[0]->shareCount) && (int) $posts[0]->shareCount > 0) {
                    $shareCount = (int) $posts[0]->shareCount;
                }
            }
        }

        if (B2S_PLUGIN_USER_VERSION > 0) {
            if ($optionAutoPost !== false) {
                if (!isset($optionAutoPost['active']) || (isset($optionAutoPost['active']) && (int) $optionAutoPost['active'] == 1)) {
                    $state = ($postId == 0) ? 'publish' : (($postStatus != '' && ($postStatus == 'publish')) ? 'update' : 'publish');
                    if (is_array($optionAutoPost) && isset($optionAutoPost[$state])) {
                        if (in_array($postType, $optionAutoPost[$state])) {
                            $autoPostActive = true;
                        }
                    }
                }
            }
            if (isset($optionAutoPost['profile'])) {
//default from settings
                $defaultProfile = $optionAutoPost['profile'];
                if (isset($optionAutoPost['twitter']) && (int) $optionAutoPost['twitter'] > 0) {
                    $defaultTwitter = $optionAutoPost['twitter'];
                }
            }

            $result = json_decode(B2S_Api_Post::post(B2S_PLUGIN_API_ENDPOINT, array('action' => 'getProfileUserAuth', 'token' => B2S_PLUGIN_TOKEN)));
            if (isset($result->result) && (int) $result->result == 1 && isset($result->data) && !empty($result->data) && isset($result->data->mandant) && isset($result->data->auth) && !empty($result->data->mandant)) {


                /*
                 * since V7.0 Remove Video Networks
                 */
                if (!empty($result->data->auth)) {
                    $isVideoNetwork = unserialize(B2S_PLUGIN_NETWORK_SUPPORT_VIDEO);
                    foreach ($result->data->auth as $a => $auth) {
                        foreach ($auth as $u => $item) {
                            if (in_array($item->networkId, $isVideoNetwork)) {
                                if (!in_array($item->networkId, array(1, 2, 3, 6, 12, 38, 39))) {
                                    unset($result->data->auth->{$a[$u]});
                                }
                            }
                        }
                    }
                }

                if (!empty($result->data->auth)) {
                    $postOptions = get_option('B2S_PLUGIN_POST_OPTIONS_' . $postId);
                    if ($postOptions != false && isset($postOptions['auto_post_manuell']) && !empty($postOptions['auto_post_manuell']) && isset($postOptions['auto_post_manuell'][B2S_PLUGIN_BLOG_USER_ID]) && !empty($postOptions['auto_post_manuell'][B2S_PLUGIN_BLOG_USER_ID])) {
//selected at last post
                        if (isset($postOptions['auto_post_manuell'][B2S_PLUGIN_BLOG_USER_ID]['profile']) && (int) $postOptions['auto_post_manuell'][B2S_PLUGIN_BLOG_USER_ID]['profile'] > 0) {
                            $selectedProfileId = $postOptions['auto_post_manuell'][B2S_PLUGIN_BLOG_USER_ID]['profile'];
                            if (isset($postOptions['auto_post_manuell'][B2S_PLUGIN_BLOG_USER_ID]['twitter']) && (int) $postOptions['auto_post_manuell'][B2S_PLUGIN_BLOG_USER_ID]['twitter'] > 0) {
                                $selectedTwitterId = $postOptions['auto_post_manuell'][B2S_PLUGIN_BLOG_USER_ID]['twitter'];
                            }
                        }
                    }
                    if ($selectedProfileId < 0 && $defaultProfile >= 0) {
//default from settings
                        $selectedProfileId = $defaultProfile;
                        if ((int) $defaultTwitter > 0) {
                            $selectedTwitterId = $defaultTwitter;
                        }
                    }
                    if ($selectedProfileId < 0) {
//old
                        $profilOption = get_option('B2S_PLUGIN_SAVE_META_BOX_AUTO_SHARE_PROFILE_USER_' . B2S_PLUGIN_BLOG_USER_ID);
                        if ((int) $profilOption > 0) {
                            $selectedProfileId = (int) $profilOption;
                        }
                    }
                    $bestTimes = (isset($optionAutoPost['best_times']) && (int) $optionAutoPost['best_times'] > 0) ? true : false;
                    $bestTimesDefault = $bestTimes;
                    if ($postOptions != false && isset($postOptions[B2S_PLUGIN_BLOG_USER_ID]) && !empty($postOptions[B2S_PLUGIN_BLOG_USER_ID] && isset($postOptions[B2S_PLUGIN_BLOG_USER_ID]['best_times']))) {
                        $bestTimes = ((int) $postOptions[B2S_PLUGIN_BLOG_USER_ID]['best_times'] > 0) ? true : false;
                    }
                    $advancedOptions = $this->getAdvancedOptions($result->data->mandant, $result->data->auth, $selectedProfileId, $selectedTwitterId, $bestTimes, !$assigned);
                }
            }

//Auto-Post-Import - Check Conditions - show notice
            $autoPostData = $this->userOption->_getOption('auto_post_import');
            if ($autoPostData !== false && is_array($autoPostData)) {
                if (isset($autoPostData['active']) && (int) $autoPostData['active'] == 1) {
                    $autoPostImport = true;
                    if (isset($autoPostData['post_filter']) && (int) $autoPostData['post_filter'] == 1) {
                        if (isset($autoPostData['post_type']) && is_array($autoPostData['post_type']) && !empty($autoPostData['post_type'])) {
                            if (isset($autoPostData['post_type_state']) && (int) $autoPostData['post_type_state'] == 0) { //include
                                if (!in_array($postType, $autoPostData['post_type'])) {
                                    $autoPostImport = false;
                                }
                            } else { //exclude
                                if (in_array($postType, $autoPostData['post_type'])) {
                                    $autoPostImport = false;
                                }
                            }
                        }
                    }
                    $autoPostCon = $this->userOption->_getOption('auto_post_import_condition');
                    if ($autoPostCon !== false && is_array($autoPostCon) && isset($autoPostCon['count'])) {
                        $con = unserialize(B2S_PLUGIN_AUTO_POST_LIMIT);
                        if ($autoPostCon['count'] == $con[B2S_PLUGIN_USER_VERSION]) {
                            $autoPostImport = false;
                        }
                    }
                }
            }
        }

        $content = '<div class="b2s-post-meta-box">
                    <div id="b2s-server-connection-fail" class="b2s-info-error b2s-info-display-none"><button class="b2s-btn-close-meta-box b2s-close-icon" data-area-id="b2s-server-connection-fail" title="close notice"></button>' . esc_html__('The connection to the server failed. Please try again! You can find more information and solutions in the', 'blog2social') . '<a target="_blank" href="' . esc_url(B2S_Tools::getSupportLink('connection_guide')) . '"> ' . esc_html__('guide for server connection', 'blog2social') . '</a>.</div>
                    <div id="b2s-heartbeat-fail" class="b2s-info-error b2s-info-display-none"><button class="b2s-btn-close-meta-box b2s-close-icon" data-area-id="b2s-heartbeat-fail" title="close notice"></button>' . esc_html__('WordPress uses heartbeats by default, Blog2Social as well. Please enable heartbeats for using Blog2Social!', 'blog2social') . $b2sHeartbeatFaqLink . ' </div>
                    <div id="b2s-post-meta-box-state-no-publish-future-customize" class="b2s-info-error b2s-info-display-none"><button class="b2s-btn-close-meta-box b2s-close-icon" data-area-id="b2s-post-meta-box-state-no-publish-future-customize" title="close notice"></button>' . esc_html__('Your post is still on draft or pending status. Please make sure that your post is published or scheduled to be published on this blog. You can then auto-post or schedule and customize your social media posts with Blog2Social.', 'blog2social') . '</div>
                    <div id="b2s-post-meta-box-state-no-auth" class="b2s-info-error b2s-info-display-none"><button class="b2s-btn-close-meta-box b2s-close-icon" data-area-id="b2s-post-meta-box-state-no-auth" title="close notice"></button>' . esc_html__('There are no social network accounts assigned to your selected network collection. Please assign at least one social network account or select another network collection.', 'blog2social') . '<a href="' . esc_url($this->b2sSiteUrl . 'wp-admin/admin.php?page=blog2social-network') . '" target="_bank">' . esc_html__('Network settings', 'blog2social') . '</a></div>
                    <div id="b2s-post-meta-box-state-no-publish-future" class="b2s-info-error b2s-info-display-none"><button class="b2s-btn-close-meta-box b2s-close-icon" data-area-id="b2s-post-meta-box-state-no-publish-future" title="close notice"></button>' . esc_html__('Your post is still on draft or pending status. Please make sure that your post is published or scheduled to be published on this blog. You can then auto-post or schedule and customize your social media posts with Blog2Social.', 'blog2social') . '</div>
                    <div id="b2s-url-valid-warning" class="b2s-info-warning b2s-info-display-none"><button class="b2s-btn-close-meta-box b2s-close-icon" data-area-id="b2s-url-valid-warning" title="close notice"></button>' . esc_html__('Notice: Please make sure, that your website address is reachable. The Social Networks do not allow postings from local installations.', 'blog2social') . '</div>
                    <input type="hidden" id="b2s-redirect-url-customize" name="b2s-redirect-url-customize" value="' . esc_attr($this->b2sSiteUrl . 'wp-admin/admin.php?page=blog2social-ship&postId=') . '"/>
                    <input type="hidden" id="b2s-user-last-selected-profile-id" name="b2s-user-last-selected-profile-id" value="' . esc_attr(($selectedProfileId !== false ? (int) $selectedProfileId : 0)) . '" />
                    <input type="hidden" id="b2s-home-url" name="b2s-home-url" value="' . esc_attr(get_option('home')) . '"/>
                    <input type="hidden" id="b2sLang" name="b2s-user-lang" value="' . esc_attr(strtolower(substr(get_locale(), 0, 2))) . '">
                    <input type="hidden" id="b2sUserLang" name="b2s-user-lang" value="' . esc_attr(strtolower(substr(get_locale(), 0, 2))) . '">
                    <input type="hidden" id="b2sPostLang" name="b2s-post-lang" value="' . esc_attr(substr($this->postLang, 0, 2)) . '">
                    <input type="hidden" id="b2sPluginUrl" name="b2s-post-lang" value="' . esc_attr(B2S_PLUGIN_URL) . '">    
                    <input type="hidden" id="b2sBlogUserId" name="b2s-blog-user-id" value="' . esc_attr(B2S_PLUGIN_BLOG_USER_ID) . '">              
                    <input type="hidden" id="b2s-user-timezone" name="b2s-user-timezone" value="' . esc_attr($userTimeZoneOffset) . '"/>
                    <input type="hidden" id="b2s-post-status" name="b2s-post-status" value="' . esc_attr(trim(strtolower($postStatus))) . '"/>
                    <input type="hidden" id="b2s-post-meta-box-version" name="b2s-post-meta-box-version" value="' . esc_attr(B2S_PLUGIN_USER_VERSION) . '"/>
                    <input type="hidden" id="isOgMetaChecked" name="isOgMetaChecked" value="' . esc_attr((isset($metaSettings['og_active']) ? (int) $metaSettings['og_active'] : 0)) . '">
                    <input type="hidden" id="isCardMetaChecked" name="isCardMetaChecked" value="' . esc_attr((isset($metaSettings['card_active']) ? (int) $metaSettings['card_active'] : 0)) . '">
                    <input type="hidden" id="b2sAutoPostImportIsActive" name="autoPostImportIsActive" value="' . (($autoPostImport) ? 1 : 0) . '">

                    <h3 class="b2s-meta-box-headline">' . esc_html__('Custom Sharing & Scheduling', 'blog2social') . ' <a class="b2s-info-btn" data-modal-target="b2sInfoMetaBoxModalSched" href="#">' . esc_html__('Info', 'blog2social') . '</a></h3>
                    <a id="b2s-meta-box-btn-customize" class="b2s-btn b2s-btn-primary b2s-btn-sm b2s-center-block b2s-btn-margin-bottom-15" href="#">' . esc_html__('Customize & Schedule Social Media Posts', 'blog2social') . '</a>
                    <div class="b2s-post-box-content">
                    <h3 class="b2s-meta-box-headline">' . esc_html__('The Autoposter is', 'blog2social') . ' <span class="b2s-post-meta-box-active" style="color:green;' . ((!$autoPostActive) ? 'display:none;' : '') . '">' . esc_html__('activated', 'blog2social') . '</span><span class="b2s-post-meta-box-inactive" style="color:red;' . (($autoPostActive) ? 'display:none;' : '') . '">' . esc_html__('deactivated', 'blog2social') . '</span> <a class="b2s-info-btn" data-modal-target="b2sInfoMetaBoxModalAutoPost" href="#">' . esc_html__('Info', 'blog2social') . '</a></h3>
                    <div class="b2s-meta-box-share-info">
                    <div>' . esc_html__('Shared', 'blog2social') . ': <span class="b2s-meta-box-share-count">' . $shareCount . '</span> ' . esc_html__('times', 'blog2social') . '</div>
                    <span>' . esc_html__('Last shared', 'blog2social') . ': </span>
                    <span class="b2s-meta-box-last-post-date">' . $lastPostDate . '</span>
                    </div>';
        if (B2S_PLUGIN_USER_VERSION > 0) {
            $content .= '<div class="b2s-options-btn-area"><span class="b2s-options-btn" href="#">' . esc_html__('Advanced settings', 'blog2social') . ' <i class="glyphicon glyphicon-chevron-down"></i></span></div>
                    <div class="b2s-options" style="display:none;">
                    <br>
                    <input type="checkbox" class="b2s-enable-auto-post" id="b2s-enable-auto-post" name="b2s-enable-auto-post" value="1" ' . (($autoPostActive) ? 'checked' : '') . '><label for="b2s-enable-auto-post">' . esc_html__('enable Auto-Posting', 'blog2social') . '</label>
                    ' . ((isset($advancedOptions)) ? $advancedOptions : '') . '
                    <a href="#b2s-post-box-calendar-header" id="b2s-post-box-calendar-btn">' . esc_html__('show calendar', 'blog2social') . '</a>
                    <input type="hidden" name="b2s-profile-selected" value="' . ((isset($selectedProfileId)) ? esc_attr($selectedProfileId) : '-1') . '">
                    <input type="hidden" name="b2s-profile-default" value="' . ((isset($defaultProfile)) ? esc_attr($defaultProfile) : '-1') . '">
                    <input type="hidden" name="b2s-twitter-default" value="' . ((isset($defaultTwitter)) ? esc_attr($defaultTwitter) : '0') . '">
                    <input type="hidden" name="b2s-best-times-default" value="' . ((isset($bestTimesDefault)) ? esc_attr($bestTimesDefault) : '0') . '">
                    </div>';
        }
        $content .= '</div>
                    </div>';

        $content .= ' <div class="b2s-meta-box-modal" id="b2sInfoMetaBoxModalSched" aria-hidden="true" style="display:none;">
                        <div class="b2s-meta-box-modal-dialog">
                            <div class="b2s-meta-box-modal-header">
                                  <a href="#" class="b2s-meta-box-modal-btn-close" data-modal-target="b2sInfoMetaBoxModalSched" aria-hidden="true">×</a>
                              <h4 class="b2s-meta-box-modal-title">' . esc_html__('Blog2Social: Customize & Schedule Social Media Posts', 'blog2social') . '</h4>
                            </div>
                            <div class="b2s-meta-box-modal-body">
                              <p>' . esc_html__('Customize and schedule your social media posts on the one page preview for all your selected networks: tailor your posts with individual comments, #hashtags or @handles and schedule your posts for the best times to post, for multiple times or re-share recurrently for more visibility and engagement with your community.', 'blog2social') . '</p>
                            </div>
                        </div>
                    </div>';

        $content .= '<div class="b2s-meta-box-modal" id="b2sInfoMetaBoxModalAutoPost" aria-hidden="true" style="display:none;">
                        <div class="b2s-meta-box-modal-dialog">
                            <div class="b2s-meta-box-modal-header">
                                  <a href="#" class="b2s-meta-box-modal-btn-close" data-modal-target="b2sInfoMetaBoxModalAutoPost" aria-hidden="true">×</a>
                              <h4 class="b2s-meta-box-modal-title">' . esc_html__('Blog2Social: Social Media Auto-Posting', 'blog2social') . '</h4>
                            </div>
                            <div class="b2s-meta-box-modal-body">
                              <p>
                           ' . esc_html__('Share your blog posts with the Auto Poster: Your blog posts will be shared automatically on your social media channels as soon as you publish or update a new post. You can also choose to autopost scheduled blog posts as soon as they are published.', 'blog2social');
        $content .= ' ' . sprintf(__('<a target="_blank" href="%s">Learn how to set up auto posting for your blog posts</a>', 'blog2social'), esc_url(B2S_Tools::getSupportLink('auto_poster_m')));

        if (B2S_PLUGIN_USER_VERSION == 0) {
            $content .= '<hr>
                            <h4 class="b2s-meta-box-modal-h4">' . esc_html__('You want to auto-post your blog post?', 'blog2social') . '</h4>
                            ' . esc_html__('With Blog2Social Premium you can:', 'blog2social') . '
                                <br>
                                <br>
                                - ' . esc_html__('Post on pages and groups', 'blog2social') . '<br>
                                - ' . esc_html__('Share on multiple profiles, pages and groups', 'blog2social') . '<br>
                                - ' . esc_html__('Auto-post and auto-schedule new and updated blog posts', 'blog2social') . '<br>
                                - ' . esc_html__('Schedule your posts at the best times on each network', 'blog2social') . '<br>
                                - ' . esc_html__('Best Time Manager: use predefined best time scheduler to auto-schedule your social media posts', 'blog2social') . '<br>
                                - ' . esc_html__('Schedule your post for one time, multiple times or recurrently', 'blog2social') . '<br>
                                - ' . esc_html__('Schedule and re-share old posts', 'blog2social') . '<br>
                                - ' . esc_html__('Select link format or image format for your posts', 'blog2social') . '<br>
                                - ' . esc_html__('Select individual images per post', 'blog2social') . '<br>
                                - ' . esc_html__('Reporting & calendar: keep track of your published and scheduled social media posts', 'blog2social') . '<br>
                                <br>
                                <a target="_blank" href="' . esc_url(B2S_Tools::getSupportLink('affiliate')) . '" class="b2s-btn b2s-btn-success b2s-center-block b2s-btn-none-underline">' . esc_html__('Upgrade to SMART and above', 'blog2social') . '</a><br>

                                ' . ((!get_option('B2S_PLUGIN_DISABLE_TRAIL')) ? '<center>' . sprintf(__('or <a target="_blank" href="%s">start with free 30-days-trial of Blog2Social Premium</a> (no payment information needed)', 'blog2social'), esc_url('https://service.blog2social.com/trial')) . '</center>' : '');
        }
        $content .= '</p>
                            </div>
                        </div>
                      </div>
                   ';
        return $content;
    }

    public function getAdvancedOptions($mandant = array(), $auth = array(), $selectedProfileId = -1, $selectedTwitterId = -1, $bestTimes = false, $show = true) {
        $authContent = '';
        $content = '';
        if (!$show) {
            $content .= '<div class="panel panel-group b2s-info-assignd-by"><div class="panel-body">';
            $content .= '<span>' . esc_html__('A WordPress admin has defined the Auto-Poster settings for you. You can deactivate these settings for your profile in the Auto-Poster settings at any time.', 'blog2social') . '</span>';
            $content .= '</div>';
            $content .= '</div>';
            $content .= '<div style="display:none">';
        }
        $content .= '<br><div class="b2s-meta-box-auto-post-area"><label for="b2s-post-meta-box-profil-dropdown">' . esc_html__('Select network collection:', 'blog2social') . ' <div class="pull-right"><a class="b2s-info-btn" data-modal-target="b2sInfoNetworkModal" href="#">' . esc_html__('Info', 'blog2social') . '</a></div></label>
                    <div class="b2s-meta-box-modal" id="b2sInfoNetworkModal" aria-hidden="true" style="display:none;">
                        <div class="b2s-meta-box-modal-dialog">
                            <div class="b2s-meta-box-modal-header">
                                <a href="#" class="b2s-meta-box-modal-btn-close" data-modal-target="b2sInfoNetworkModal" aria-hidden="true">×</a>
                                <h4 class="b2s-meta-box-modal-title">' . esc_html__('Available networks for autoposting', 'blog2social') . '</h4>
                            </div>
                            <div class="b2s-meta-box-modal-body">
                                <div class="b2s-network-imgs">
                                    <img class="pull-left hidden-xs b2s-img-network" alt="' . esc_attr('Facebook') . '" src="' . esc_url(plugins_url('/assets/images/portale/1_flat.png', B2S_PLUGIN_FILE)) . '">
                                    <img class="pull-left hidden-xs b2s-img-network" alt="' . esc_attr('Twitter') . '" src="' . esc_url(plugins_url('/assets/images/portale/2_flat.png', B2S_PLUGIN_FILE)) . '">
                                    <img class="pull-left hidden-xs b2s-img-network" alt="' . esc_attr('LinkedIn') . '" src="' . esc_url(plugins_url('/assets/images/portale/3_flat.png', B2S_PLUGIN_FILE)) . '">
                                    <img class="pull-left hidden-xs b2s-img-network" alt="' . esc_attr('Tumblr') . '" src="' . esc_url(plugins_url('/assets/images/portale/4_flat.png', B2S_PLUGIN_FILE)) . '">
                                    <img class="pull-left hidden-xs b2s-img-network" alt="' . esc_attr('Pinterest') . '" src="' . esc_url(plugins_url('/assets/images/portale/6_flat.png', B2S_PLUGIN_FILE)) . '">
                                    <img class="pull-left hidden-xs b2s-img-network" alt="' . esc_attr('Flickr') . '" src="' . esc_url(plugins_url('/assets/images/portale/7_flat.png', B2S_PLUGIN_FILE)) . '">
                                    <img class="pull-left hidden-xs b2s-img-network" alt="' . esc_attr('Diigo') . '" src="' . esc_url(plugins_url('/assets/images/portale/9_flat.png', B2S_PLUGIN_FILE)) . '">
                                    <img class="pull-left hidden-xs b2s-img-network" alt="' . esc_attr('Medium') . '" src="' . esc_url(plugins_url('/assets/images/portale/11_flat.png', B2S_PLUGIN_FILE)) . '">
                                    <img class="pull-left hidden-xs b2s-img-network" alt="' . esc_attr('Instagram') . '" src="' . esc_url(plugins_url('/assets/images/portale/12_flat.png', B2S_PLUGIN_FILE)) . '">
                                    <img class="pull-left hidden-xs b2s-img-network" alt="' . esc_attr('Torial') . '" src="' . esc_url(plugins_url('/assets/images/portale/14_flat.png', B2S_PLUGIN_FILE)) . '">
                                    <img class="pull-left hidden-xs b2s-img-network" alt="' . esc_attr('Reddit') . '" src="' . esc_url(plugins_url('/assets/images/portale/15_flat.png', B2S_PLUGIN_FILE)) . '">
                                    <img class="pull-left hidden-xs b2s-img-network" alt="' . esc_attr('Bloglovin') . '" src="' . esc_url(plugins_url('/assets/images/portale/16_flat.png', B2S_PLUGIN_FILE)) . '">
                                    <img class="pull-left hidden-xs b2s-img-network" alt="' . esc_attr('VKontakte') . '" src="' . esc_url(plugins_url('/assets/images/portale/17_flat.png', B2S_PLUGIN_FILE)) . '">
                                    <img class="pull-left hidden-xs b2s-img-network" alt="' . esc_attr('XING') . '" src="' . esc_url(plugins_url('/assets/images/portale/19_flat.png', B2S_PLUGIN_FILE)) . '">
                                    <img class="pull-left hidden-xs b2s-img-network" alt="' . esc_attr('Google Business Profile') . '" src="' . esc_url(plugins_url('/assets/images/portale/18_flat.png', B2S_PLUGIN_FILE)) . '">
                                </div>
                                <br>
                                <p class="b2s-bold">' . sprintf(__('Under <a href="%s">Network Settings</a> you can define which network selection is used. <a href="%s" target="_blank">Create a network selection.</a>', 'blog2social'), 'admin.php?page=blog2social-network', esc_url(B2S_Tools::getSupportLink('network_grouping'))) . '</p>
                                <h4>' . esc_html__('Available networks', 'blog2social') . '</h4>
                                <span class="b2s-bold">' . esc_html('Facebook (Profile & Seiten)') . '</span><br>
                                <span class="b2s-bold">' . esc_html('Twitter (1 Profil)') . '</span><br>
                                <span class="b2s-bold">' . esc_html('LinkedIn') . '</span><br>
                                <span class="b2s-bold">' . esc_html('Tumblr') . '</span><br>
                                <span class="b2s-bold">' . esc_html('Pinterest') . '</span><br>
                                <span class="b2s-bold">' . esc_html('Flickr') . '</span><br>
                                <span class="b2s-bold">' . esc_html('Diigo') . '</span><br>
                                <span class="b2s-bold">' . esc_html('Medium') . '</span><br>
                                <span class="b2s-bold">' . esc_html('Instagram') . '</span><br>
                                <span class="b2s-bold">' . esc_html('Torial') . '</span><br>
                                <span class="b2s-bold">' . esc_html('Reddit') . '</span><br>
                                <span class="b2s-bold">' . esc_html('Bloglovin') . '</span><br>
                                <span class="b2s-bold">' . esc_html('VKontakte (Profile & Seiten)') . '</span><br>
                                <span class="b2s-bold">' . esc_html('XING (Profile & Seiten)') . '</span><br>
                                <span class="b2s-bold">' . esc_html('Google Business Profile') . '</span><br>
                            </div>
                        </div>
                    </div>
                <select class="b2s-w-100" id="b2s-post-meta-box-profil-dropdown" name="b2s-post-meta-box-profil-dropdown">';
        foreach ($mandant as $k => $m) {
            $content .= '<option value="' . esc_attr($m->id) . '" ' . (((int) $m->id == (int) $selectedProfileId) ? 'selected' : '') . '>' . esc_html((($m->id == 0) ? __($m->name, 'blog2social') : $m->name)) . '</option>';
            $profilData = (isset($auth->{$m->id}) && isset($auth->{$m->id}[0]) && !empty($auth->{$m->id}[0])) ? json_encode($auth->{$m->id}) : '';
            $authContent .= "<input type='hidden' id='b2s-post-meta-box-profil-data-" . esc_attr($m->id) . "' name='b2s-post-meta-box-profil-data-" . esc_attr($m->id) . "' value='" . base64_encode($profilData) . "'/>";
        }
        $content .= '</select></div>';
        $content .= $authContent;

//TOS Twitter 032018 - none multiple Accounts - User select once
        $content .= '<div class="b2s-meta-box-auto-post-twitter-profile"><label for="b2s-post-meta-box-profil-dropdown-twitter">' . esc_html__('Select Twitter profile:', 'blog2social') . '</label> <select class="b2s-w-100" id="b2s-post-meta-box-profil-dropdown-twitter" name="b2s-post-meta-box-profil-dropdown-twitter">';
        foreach ($mandant as $k => $m) {
            if ((isset($auth->{$m->id}) && isset($auth->{$m->id}[0]) && !empty($auth->{$m->id}[0]))) {
                foreach ($auth->{$m->id} as $key => $value) {
                    if ($value->networkId == 2) {
                        $content .= '<option data-mandant-id="' . esc_attr($m->id) . '" value="' . esc_attr($value->networkAuthId) . '"  ' . (((int) $value->networkAuthId == (int) $selectedTwitterId) ? 'selected' : 'disabled="disabled"') . '>' . esc_html($value->networkUserName) . '</option>';
                    }
                }
            }
        }
        $content .= '</select></div>';

//new V5.1.0 Seeding
        $bestTimeType = 0;  //0=default(best time), 1= special per account (seeding), 2= per network (old)
        $myBestTimeSettings = $this->userOption->_getOption('auth_sched_time');
        if (isset($myBestTimeSettings['time'])) {
            $bestTimeType = 1;
//old  
        } else {
            global $wpdb;
            if ($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}b2s_post_sched_settings'") == $wpdb->prefix . 'b2s_post_sched_settings') {
                $myBestTimeSettings = $wpdb->get_results($wpdb->prepare("SELECT network_id, network_type, sched_time FROM {$wpdb->prefix}b2s_post_sched_settings WHERE blog_user_id= %d", B2S_PLUGIN_BLOG_USER_ID));
                if (is_array($myBestTimeSettings) && !empty($myBestTimeSettings)) {
                    $bestTimeType = 2;
                } else {
//default
                    $myBestTimeSettings = B2S_Tools::getRandomBestTimeSettings();
                }
            }
        }

//Opt: Best Time Settings
        if (!empty($myBestTimeSettings) && is_array($myBestTimeSettings)) {
            $bestTimeSettings = array('type' => $bestTimeType, 'times' => $myBestTimeSettings);
            $content .= '<br>
                <div class="b2s-meta-box-auto-post-sched"><label for="b2s-post-meta-box-sched-select">' . esc_html__('When do you want to share your post on social media?', 'blog2social') . '</label>
                <select id="b2s-post-meta-box-sched-select" class="b2s-w-100" name="b2s-post-meta-box-sched-select">
                <option value="0" ' . ((!$bestTimes) ? 'selected' : '') . '>' . esc_html__('immediately after publishing', 'blog2social') . '</option>
                <option value="1" ' . (($bestTimes) ? 'selected' : '') . '>' . esc_html__('at best times', 'blog2social') . '</option>
                </select></div>';
            $content .= "<input id='b2s-post-meta-box-best-time-settings' class='post-format' name='b2s-post-meta-box-best-time-settings' value='" . json_encode($bestTimeSettings) . "' type='hidden'> ";
        }
        if (!$show) {
            $content .= '</div>';
        }

        return $content;
    }

    public function getVideoBox($postId = 0) {
        $content = '';
        $notice = '';
        $url = '';
        $canUseVideoAddon = (defined('B2S_PLUGIN_ADDON_VIDEO') && !empty(B2S_PLUGIN_ADDON_VIDEO)) ? true : false;
        if (B2S_PLUGIN_USER_VERSION > 0 && $canUseVideoAddon && isset(B2S_PLUGIN_ADDON_VIDEO['volume_open'])) {
            $enoughVolume = false;
            $volume = B2S_PLUGIN_ADDON_VIDEO['volume_open'];
            $videoMeta = wp_read_video_metadata(get_attached_file((int) $postId));
            if (isset($videoMeta['filesize']) && is_numeric($videoMeta['filesize'])) {
                if ($volume >= round($videoMeta['filesize'] / 1024)) {
                    $enoughVolume = true;
                }
            }
            if (!$enoughVolume) {
                $notice = esc_html__("You don't have enough data volume left. Please top-up your data to upload your video.", 'blog2social');
            } else {
                $url = esc_url("admin.php?page=blog2social-ship&isVideo=1&postId=" . esc_attr($postId));
            }
        } else {
            $notice = esc_html__('Unlock video add-on', 'blog2social');
        }

        if (!empty($notice)) {
            $content .= '<div id="b2s-meta-video-box-notice" class="b2s-info-warning"><button class="b2s-btn-close-meta-box b2s-close-icon" data-area-id="b2s-meta-video-box-notice" title="close notice"></button>' . $notice . '</div>';
        }
        $content .= '<button id="b2s-meta-video-box-btn-customize" data-url="' . $url . '" class="b2s-btn b2s-btn-primary b2s-btn-sm b2s-center-block b2s-btn-margin-bottom-15" ' . (!empty($notice) ? 'disabled' : '') . ' >' . esc_html__('Share on video networks', 'blog2social') . '</button>';
        return $content;
    }

    public function updateInfo($postId = 0) {
        //>= V6.1 Gutenberg update Infobox
        $autoPostActive = false;
        $lastPostDate = '---';
        $shareCount = 0;
        if ((int) $postId > 0) {
            $optionAutoPost = $this->userOption->_getOption('auto_post');
            $postStatus = get_post_status($postId);
            $postType = get_post_type($postId);
            if ($optionAutoPost !== false) {
                if (!isset($optionAutoPost['active']) || (isset($optionAutoPost['active']) && (int) $optionAutoPost['active'] == 1)) {
                    $state = ($postStatus != false && $postStatus != '' && ($postStatus == 'publish')) ? 'update' : 'publish';
                    if (is_array($optionAutoPost) && isset($optionAutoPost[$state])) {
                        if ($postType != false && in_array($postType, $optionAutoPost[$state])) {
                            $autoPostActive = true;
                        }
                    }
                }
            }

            global $wpdb;
            if ($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}b2s_posts'") == $wpdb->prefix . 'b2s_posts') {
                $lastPost = $wpdb->get_results($wpdb->prepare("SELECT publish_date FROM {$wpdb->prefix}b2s_posts WHERE post_id= %d ORDER BY publish_date DESC LIMIT 1", $postId));
                if (!empty($lastPost) && isset($lastPost[0]) && !empty($lastPost[0]->publish_date) && $lastPost[0]->publish_date != '0000-00-00 00:00:00') {
                    $lastPostDate = esc_html(B2S_Util::getCustomDateFormat($lastPost[0]->publish_date, substr(B2S_LANGUAGE, 0, 2)));
                }
                $posts = $wpdb->get_results($wpdb->prepare("SELECT count(id) as shareCount FROM {$wpdb->prefix}b2s_posts WHERE post_id= %d AND hide = 0", $postId));
                if (!empty($posts) && isset($posts[0]) && !empty($posts[0]->shareCount) && (int) $posts[0]->shareCount > 0) {
                    $shareCount = (int) $posts[0]->shareCount;
                }
            }
        }

        return array('active' => $autoPostActive, 'lastPostDate' => $lastPostDate, 'shareCount' => $shareCount);
    }

}
