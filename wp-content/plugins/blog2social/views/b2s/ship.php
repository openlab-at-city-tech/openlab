<?php
wp_nonce_field('b2s_security_nonce', 'b2s_security_nonce');
require_once B2S_PLUGIN_DIR . 'includes/B2S/Ship/Navbar.php';
require_once B2S_PLUGIN_DIR . 'includes/B2S/Ship/Image.php';
require_once B2S_PLUGIN_DIR . 'includes/B2S/Ship/Portale.php';
require_once B2S_PLUGIN_DIR . 'includes/B2S/Settings/Item.php';
delete_option('B2S_PLUGIN_POST_META_TAGES_TWITTER_' . (int) $_GET['postId']);
delete_option('B2S_PLUGIN_POST_META_TAGES_OG_' . (int) $_GET['postId']);
delete_option('B2S_PLUGIN_POST_CONTENT_' . (int) $_GET['postId']);
B2S_Tools::checkUserBlogUrl();
$userLang = strtolower(substr(get_locale(), 0, 2));
$tosCrossPosting = unserialize(B2S_PLUGIN_NETWORK_CROSSPOSTING_LIMIT);
$postData = get_post((int) $_GET['postId']);
$selProfile = isset($_GET['profile']) ? (int) $_GET['profile'] : 0;
$selImg = (isset($_GET['img']) && !empty($_GET['img'])) ? base64_decode(sanitize_text_field($_GET['img'])) : '';
$isVideo = (isset($_GET['isVideo']) && (int) $_GET['isVideo'] == 1) ? true : false;
$exPostFormat = (isset($_GET['postFormat']) && $_GET['postFormat'] == '1') ? 1 : ((isset($_GET['postFormat']) && $_GET['postFormat'] == '2') ? 2 : 0);
$postUrl = (isset($_GET['b2sPostType']) && $_GET['b2sPostType'] == 'ex') ? (($exPostFormat == 0) ? $postData->guid : '') : (get_permalink($postData->ID) !== false ? get_permalink($postData->ID) : $postData->guid);
$postStatus = array('publish' => esc_html__('published', 'blog2social'), 'pending' => esc_html__('draft', 'blog2social'), 'future' => esc_html__('scheduled', 'blog2social'));
$options = new B2S_Options(B2S_PLUGIN_BLOG_USER_ID);
$optionUserTimeZone = $options->_getOption('user_time_zone');
$userTimeZone = ($optionUserTimeZone !== false) ? $optionUserTimeZone : get_option('timezone_string');
$userTimeZoneOffset = (empty($userTimeZone)) ? get_option('gmt_offset') : B2S_Util::getOffsetToUtcByTimeZone($userTimeZone);
$optionUserTimeFormat = $options->_getOption('user_time_format');
if ($optionUserTimeFormat == false) {
    $optionUserTimeFormat = (substr(B2S_LANGUAGE, 0, 2) == 'de') ? 0 : 1;
}
$isPremium = (B2S_PLUGIN_USER_VERSION == 0) ? '<span class="label label-success">' . esc_html__("SMART", "blog2social") . '</span>' : '';
$videoMeta = ($isVideo) ? wp_read_video_metadata(get_attached_file($postData->ID)) : null;
$selSchedDate = (isset($_GET['schedDate']) && !empty($_GET['schedDate'])) ? date("Y-m-d", (strtotime(sanitize_text_field($_GET['schedDate']) . ' ' . B2S_Util::getCustomLocaleDateTime($userTimeZoneOffset, 'H:i:s')) + 3600)) : ( (isset($_GET['schedDateTime']) && !empty($_GET['schedDateTime'])) ? date("Y-m-d H:i:s", strtotime(B2S_Util::getUTCForDate(sanitize_text_field($_GET['schedDateTime']), $userTimeZoneOffset * (-1)))) : '' );    //routing from calendar or curated content
$b2sGeneralOptions = get_option('B2S_PLUGIN_GENERAL_OPTIONS');
$isDraft = false;
if (isset($_GET['type']) && $_GET['type'] == 'draft' && isset($_GET['postId']) && (int) $_GET['postId'] > 0) {
    global $wpdb;
    if ($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}b2s_posts_drafts'") == $wpdb->prefix . 'b2s_posts_drafts') {
        $sql = $wpdb->prepare("SELECT data FROM `{$wpdb->prefix}b2s_posts_drafts` WHERE `blog_user_id` = %d AND `post_id` = %d AND `save_origin` = %d", (int) B2S_PLUGIN_BLOG_USER_ID, (int) $_GET['postId'], 0);
        $sqlResult = $wpdb->get_row($sql);
        $draftData = (isset($sqlResult->data) && !empty($sqlResult->data)) ? unserialize($sqlResult->data) : '';
        if (!empty($draftData)) {
            $isDraft = true;
        }
    }
}
$draftIncompleteModal = false;
?>
<div class="b2s-container">
    <div class="b2s-inbox">
        <!--Header|Start - Include-->
        <div class=" col-md-12 del-padding-left">
            <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/html/header.php'); ?>
        </div>
        <!--Header|End-->
        <div class="clearfix"></div>
        <!--Content|Start-->
        <div class="col-xs-12 col-md-9 del-padding-left">
            <div class="col-xs-12 del-padding-left hidden-xs">
                <div class="panel panel-group">
                    <div class="panel-body b2s-post-details">
                        <h3>
                            <?php
                            if (!$isVideo) {
                                echo esc_html_e('Social Media Scheduling & Sharing', 'blog2social');
                            } else {
                                echo esc_html_e('Video Sharing', 'blog2social');
                            }
                            ?>
                        </h3>
                        <?php if (!$isVideo) { ?>
                            <div class="info"><?php esc_html_e('Title', 'blog2social') ?>: <?php echo esc_html(B2S_Util::getTitleByLanguage($postData->post_title, $userLang)); ?></div>
                            <?php if (!isset($_GET['b2sPostType'])) { ?>
                                <p class="info hidden-xs"># <?php echo esc_html($postData->ID); ?>  | <?php echo (isset($postStatus[trim(strtolower($postData->post_status))]) ? $postStatus[trim(strtolower($postData->post_status))] : '' ) . ' ' . esc_html__('on blog', 'blog2social') . ': ' . B2S_Util::getCustomDateFormat($postData->post_date, substr(B2S_LANGUAGE, 0, 2)); ?></p>
                                <?php
                            }
                        } else {
                            $videoMeta = wp_read_video_metadata(get_attached_file($postData->ID));
                            ?>
                            <div class="info">
                                <video id="b2sVideoPreview" controls height="73" poster="#" autobuffer="true" autoplay="false" class="pull-left">
                                    <source src="<?php echo esc_html__(wp_get_attachment_url($postData->ID)); ?>" type="<?php echo esc_html__($postData->post_mime_type); ?>">
                                    <img src="<?php echo esc_url(plugins_url('/assets/images/video_default.png', B2S_PLUGIN_FILE)); ?>" alt="video" class="hidden-xs" height="73"/>
                                </video> 
                                <div class="add-padding-left pull-left">
                                    <b><?php esc_html_e('Video', 'blog2social') ?>:</b> <?php echo esc_html(B2S_Util::getTitleByLanguage($postData->post_title, $userLang)); ?><br>
                                    <b><?php echo esc_html__('Type', 'blog2social') ?> :</b> <?php
                                    echo esc_html__($postData->post_mime_type) . ' | ' .
                                    esc_html__('Size', 'blog2social') . ': ' . esc_html__(size_format($videoMeta['filesize'])) . ' | ' . esc_html__('Length', 'blog2social') .
                                    ':' . esc_html__($videoMeta['length']) . esc_html__('s', 'blog2social');
                                    ?>
                                    <br>
                                    <?php
                                    echo sprintf(esc_html__('Uploaded by %s on %s', 'blog2social'), get_the_author_meta('display_name', $postData->post_author), B2S_Util::getCustomDateFormat($postData->post_date, substr(B2S_LANGUAGE, 0, 2)))
                                    ?>
                                </div>
                            </div> 
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>            
            <?php if (defined("B2S_PLUGIN_NOTICE_SITE_URL") && B2S_PLUGIN_NOTICE_SITE_URL != false && !$isVideo) { ?>
                <div class="b2s-settings-user-sched-time-area col-xs-12 del-padding-left hidden-xs">
                    <button type="button" class="btn btn-link pull-left btn-xs  scroll-to-bottom"><span class="glyphicon glyphicon-chevron-down"></span> <?php esc_html_e('scroll to bottom', 'blog2social') ?> </button>
                    <div class="pull-right">
                        <?php if (B2S_PLUGIN_USER_VERSION > 0) { ?>
                            <a href="#" class="btn btn-primary btn-xs b2s-get-settings-sched-time-user">
                            <?php } else { ?>
                                <a href="#" class="btn btn-primary btn-xs b2s-btn-disabled b2sPreFeatureModalBtn" data-title="<?php esc_html_e('You want to load your time settings?', 'blog2social') ?>">
                                <?php } esc_html_e('Load My Times Settings', 'blog2social'); ?> <?php echo wp_kses($isPremium, array('span' => array('class' => array()))); ?></a>

                            <?php if (B2S_PLUGIN_USER_VERSION > 0) { ?>
                                <a href="#" class="btn btn-primary btn-xs b2s-get-settings-sched-time-default">
                                <?php } else { ?>
                                    <a href="#" class="btn btn-primary btn-xs b2s-btn-disabled b2s-get-settings-sched-time-open-modal b2sPreFeatureModalBtn" data-title="<?php esc_html_e('You want to schedule your posts and use the Best Time Scheduler?', 'blog2social') ?>">
                                    <?php } esc_html_e('Load Best Times', 'blog2social'); ?> <?php echo wp_kses($isPremium, array('span' => array('class' => array()))); ?></a>
                                <a href="#" class="btn btn-link btn-xs hidden-sm b2s-load-settings-sched-time-default-info b2sInfoSchedTimesModalBtn"><?php echo esc_html_e('Info', 'blog2social'); ?></a>
                                </div>
                                </div>
                            <?php } ?>


                            </div>
                            <?php require_once B2S_PLUGIN_DIR . 'views/b2s/html/sidebar.ship.php'; //NOTE Sidebar.ship.php ?>

                            <div class="clearfix"></div>

                            <div id="b2s-wrapper" class="b2s-wrapper-content"> 
                                <div id="b2s-sidebar-wrapper" class="sidebar-default">
                                    <ul class="sidebar-nav b2s-sidbar-wrapper-nav-ul">
                                        <li class="btn-toggle-menu">
                                            <div class="b2s-network-list">
                                                <div class="b2s-network-thumb">
                                                    <div class="toggelbutton">
                                                        <i class="glyphicon glyphicon-chevron-right btn-toggle-glyphicon"></i>
                                                    </div>
                                                    <div class="network-icon">
                                                        <i class="glyphicon glyphicon-user"></i>
                                                    </div>
                                                </div>
                                                <div class="b2s-network-details-header">
                                                    <?php
                                                    $navbar = new B2S_Ship_Navbar();
                                                    $mandantData = $navbar->getData();
                                                    ?>

                                                    <h3><?php // NOTE Video Accounts (Add more...) (right site) ?>
                                                        <?php if (!$isVideo) { ?>
                                                            <?php echo esc_html(count($mandantData['auth'])); ?> <?php esc_html_e('Social Accounts', 'blog2social') ?>
                                                        <?php } else { ?>
                                                            <?php esc_html_e('Video Accounts', 'blog2social') ?>
                                                        <?php } ?>
                                                    </h3>
                                                </div>
                                            </div>
                                        </li>

                                        <li class="sidebar-brand">
                                            <div class="form-group">
                                                <?php // Video Schedule
                                                echo wp_kses($navbar->getSelectMandantHtml($mandantData['mandanten']), array(
                                                    'select' => array(
                                                        'class' => array()
                                                    ),
                                                    'option' => array(
                                                        'value' => array(),
                                                        'selected' => array()
                                                    )
                                                ));
                                                ?>
                                            </div>
                                        </li>
                                        <li class="b2s-sidbar-network-auth-btn">
                                            <div class="b2s-network-list">
                                                <div class="b2s-network-thumb">
                                                    <i class="glyphicon glyphicon-plus"></i>
                                                </div>
                                                <div class="b2s-network-details">
                                                    <h4><?php esc_html_e('Add more...', 'blog2social') ?></h4>
                                                    <p>
                                                        <?php esc_html_e('Profiles | Pages | Groups', 'blog2social') ?>
                                                    </p>
                                                </div>
                                                <div class="b2s-network-status"></div>
                                            </div>
                                        </li>
                                        <?php
                                        $orderArray = array();
                                        //Relay HTML Data - since V4.8.0
                                        $relayAccountDataHtml = '';
                                        $relayAccountData = array();
                                        $tempDraftData = array();
                                        if ($isDraft && isset($draftData) && isset($draftData['b2s'])) {
                                            $tempDraftData = $draftData['b2s'];
                                        }
                                        foreach ($mandantData['auth'] as $k => $channelData) {
                                            if ($isDraft && isset($channelData->networkAuthId) && (int) $channelData->networkAuthId > 0 && isset($tempDraftData[$channelData->networkAuthId])) {
                                                unset($tempDraftData[$channelData->networkAuthId]);
                                            }
                                            echo wp_kses($navbar->getItemHtml($channelData, (($isDraft) ? $draftData : array()), $isVideo), array(
                                                'li' => array(
                                                    'class' => array(),
                                                    'data-mandant-id' => array(),
                                                    'data-mandant-default-id' => array(),
                                                ),
                                                'div' => array(
                                                    'class' => array(),
                                                    'onclick' => array(),
                                                    'data-instant-sharing' => array(),
                                                    'data-network-auth-id' => array(),
                                                    'data-network-type' => array(),
                                                    'data-network-kind' => array(),
                                                    'data-network-id' => array(),
                                                    'data-network-tos-group-id' => array(),
                                                    'data-network-display-name' => array(),
                                                    'data-meta-type' => array(),
                                                    'scheduler-days' => array(),
                                                ),
                                                'img' => array(
                                                    'alt' => array(),
                                                    'src' => array(),
                                                ),
                                                'h4' => array(),
                                                'p' => array(),
                                                'span' => array(
                                                    'class' => array(),
                                                    'data-network-auth-id' => array(),
                                                    'data-network-id' => array(),
                                                    'style' => array(),
                                                )
                                            ));
                                            $orderArray[] = $channelData->networkAuthId;
                                            //Relay HTML Data - since V4.8.0
                                            if ($channelData->networkId == 2 && !in_array($channelData->networkTypeId, $relayAccountData) && isset($channelData->networkUserName)) {
                                                $relayAccountDataHtml .= '<option data-user-type-id="' . $channelData->networkTypeId . '" value="' . $channelData->networkAuthId . '">' . $channelData->networkUserName . '</option>';
                                                array_push($relayAccountData, $channelData->networkTypeId);
                                                //check Client db b2s_posts_network_details
                                                global $wpdb;
                                                $networkDetailsIdSelect = $wpdb->get_col($wpdb->prepare("SELECT postNetworkDetails.id FROM {$wpdb->prefix}b2s_posts_network_details AS postNetworkDetails WHERE postNetworkDetails.network_auth_id = %s", $channelData->networkAuthId));
                                                if (!isset($networkDetailsIdSelect[0])) {
                                                    $wpdb->insert($wpdb->prefix . 'b2s_posts_network_details', array(
                                                        'network_id' => (int) $channelData->networkId,
                                                        'network_type' => 0,
                                                        'network_auth_id' => (int) $channelData->networkAuthId,
                                                        'network_display_name' => $channelData->networkUserName), array('%d', '%d', '%d', '%s'));
                                                }
                                            }
                                        }
                                        if (!empty($tempDraftData)) {
                                            $draftIncompleteModal = true;
                                        }
                                        ?>
                                        <li>
                                            <div class="b2s-network-list">
                                                <div class="b2s-network-thumb">
                                                    <i class="glyphicon glyphicon-save"></i>
                                                </div>
                                                <div class="b2s-network-details-header b2s-margin-top-8">

                                                    <a href="#" class="btn btn-primary btn-sm b2s-network-setting-save b2s-loading-area-save-profile-change">
                                                        <?php esc_html_e('Save network selection', 'blog2social') ?>
                                                    </a>
                                                    <a href="#" class="btn btn-link btn-sm hidden-sm b2s-network-setting-save b2s-network-setting-save-btn"><?php echo esc_html_e('Info', 'blog2social'); ?></a>
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="b2s-network-list">
                                                <div class="b2s-network-details-legend">
                                                    <span class="b2s-no-textwarp"><i class="glyphicon glyphicon-ok glyphicon-success"></i> <?php esc_html_e('network connected', 'blog2social'); ?></span>
                                                    <span class="b2s-no-textwarp"><i class="glyphicon glyphicon-danger glyphicon-ban-circle"></i> 
                                                        <?php
                                                        if (!$isVideo) {
                                                            esc_html_e('requires image', 'blog2social');
                                                        } else {
                                                            esc_html_e('video properties not supported', 'blog2social');
                                                        }
                                                        ?>
                                                    </span>
                                                    <span class="b2s-no-textwarp"><i class="glyphicon glyphicon-danger glyphicon-refresh"></i> <?php esc_html_e('refresh authorization', 'blog2social'); ?></span>
                                                </div>
                                            </div>

                                        </li>
                                    </ul>
                                    <input type="hidden" class="b2s-network-navbar-order" value='<?php echo esc_attr(json_encode($orderArray)) ?>'>
                                </div>

                                <div id="b2s-content-wrapper" class="b2s-content-wrapper-content-default">
                                    <div class="b2s-loading-area col-md-9 del-padding-left" style="display: none;">
                                        <div class="b2s-loader-impulse b2s-loader-impulse-md"></div>
                                        <div class="clearfix"></div>
                                        <small><?php esc_html_e('Loading...', 'blog2social') ?> .</small>
                                    </div>

                                    <?php if (defined("B2S_PLUGIN_NOTICE_SITE_URL") && B2S_PLUGIN_NOTICE_SITE_URL == false) { ?>
                                        <div class="b2s-info-blog-url-area">
                                            <div class="b2s-post-area col-md-9 del-padding-left">
                                                <div class="panel panel-group text-center">
                                                    <div class="panel-body" style="margin:15px;height:500px;background:url('<?php echo esc_url(plugins_url('/assets/images/no-network-selected.png', B2S_PLUGIN_FILE)); ?>') no-repeat;background-position:center;">
                                                        <div class="panel panel-no-shadow">
                                                            <div class="panel-body panel-no-padding">
                                                                <h4><br><p><?php esc_html_e('Notice: Please make sure, that your website address is reachable. The Social Networks do not allow postings from local installations.', 'blog2social') ?></p></h4>
                                                                <?php $settingsBlogUrl = get_option('siteurl') . ((substr(get_option('siteurl'), -1, 1) == '/') ? '' : '/') . 'wp-admin/options-general.php'; ?>
                                                                <a href="<?php echo esc_url($settingsBlogUrl); ?>" class="btn btn-primary"><?php esc_html_e('change website address', 'blog2social') ?></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    <?php } else { ?>

                                        <form id="b2sNetworkSent" method="post">
                                            <div class="b2s-post-area col-md-9 del-padding-left">
                                                <div class="b2s-empty-area" style="display:none;">
                                                    <div class="panel panel-group text-center">
                                                        <div class="panel-body" style="margin:15px;height:500px;background:url('<?php echo esc_url(plugins_url('/assets/images/no-network-selected.png', B2S_PLUGIN_FILE)); ?>') no-repeat;background-position:center;">
                                                            <div class="panel panel-no-shadow">
                                                                <div class="panel-body panel-no-padding">
                                                                    <h3><?php esc_html_e('First, connect or select network before posting', 'blog2social') ?></h3>
                                                                    <br>
                                                                    <a href="#" class="btn btn-primary btn-lg text-break b2s-network-list-modal-btn"><?php esc_html_e('connect', 'blog2social') ?></a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="b2s-post-list"></div>
                                                <div class="b2s-post-network-properties-error-list"></div>

                                                <div class="b2s-publish-area">
                                                    <button type="button" class="btn btn-link pull-left btn-xs scroll-to-top"><span class="glyphicon glyphicon-chevron-up"></span> <?php esc_html_e('scroll to top', 'blog2social') ?> </button>
                                                    <button type="submit" class="btn btn-success pull-right btn-lg b2s-submit-btn"><?php esc_html_e('Share', 'blog2social') ?></button>
                                                    <button type="button" class="btn btn-primary pull-right btn-lg b2s-draft-btn"><span class="b2s-loader-btn-ship b2s-loader-impulse"></span> <?php esc_html_e('Save as Draft', 'blog2social') ?></button>
                                                </div>
                                                <div class="navbar navbar-default navbar-fixed-bottom navbar-small b2s-footer-menu" style="display: block;">
                                                    <div class="b2s-publish-navbar-btn">
                                                        <button type="button" class="btn btn-primary btn-lg b2s-draft-btn-scroll"><span class="b2s-loader-btn-ship b2s-loader-impulse"></span> <?php esc_html_e('Save as Draft', 'blog2social') ?></button>
                                                        <button type="button" class="btn btn-success btn-lg b2s-submit-btn-scroll"><?php esc_html_e('Share', 'blog2social') ?></button>
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" id="is_video" name="is_video" value="<?php echo (int) esc_attr(sanitize_text_field($isVideo)); ?>">
                                            <input type="hidden" id="video_upload_url" name="video_upload_url" value="<?php echo (((int) $isVideo == 1) ? esc_html__(wp_get_attachment_url($postData->ID)) : ''); ?>">
                                            <input type="hidden" id="video_upload_size" name="video_upload_size" value="<?php echo (($videoMeta != null && isset($videoMeta['filesize'])) ? esc_attr($videoMeta['filesize']) : 0); ?>">
                                            <input type="hidden" id="publish_date" name="publish_date" value="">
                                            <input type="hidden" id="user_version" name="user_version" value="<?php echo esc_attr(B2S_PLUGIN_USER_VERSION); ?>">
                                            <input type="hidden" id="action" name="action" value="b2s_save_ship_data">
                                            <input type='hidden' id='post_id' name="post_id" value='<?php echo (int) esc_attr(sanitize_text_field($_GET['postId'])); ?>'>
                                            <input type='hidden' id='user_timezone' name="user_timezone" value="<?php echo esc_attr($userTimeZoneOffset); ?>">
                                            <input type='hidden' id='user_timezone_text' name="user_timezone_text" value="<?php echo esc_html_e('Time zone', 'blog2social') . ': (UTC ' . B2S_Util::humanReadableOffset($userTimeZoneOffset) . ') ' . $userTimeZone ?>">
                                            <input type='hidden' id="default_titel" name="default_titel" value="<?php echo esc_attr(addslashes(B2S_Util::getTitleByLanguage($postData->post_title, $userLang))); ?>">
                                            <input type="hidden" id="b2sChangeOgMeta" name="change_og_meta" value="0">
                                            <input type="hidden" id="b2sRelayAccountData" name="relay_account_data" value="<?php echo esc_attr(base64_encode($relayAccountDataHtml)); ?>">
                                            <input type="hidden" id="b2sRelayCount" name="relay_count" value="<?php echo (int) count($relayAccountData); ?>">
                                            <input type="hidden" id="b2sChangeCardMeta" name="change_card_meta" value="0">
                                            <input type="hidden" id="b2sNotAllowGif" value="<?php echo esc_attr(implode(";", json_decode(B2S_PLUGIN_NETWORK_NOT_ALLOW_GIF, true))); ?>">
                                            <input type="hidden" id="b2sAnimateGif" value='<?php echo esc_attr(B2S_PLUGIN_NETWORK_ANIMATE_GIF); ?>'>
                                            <input type="hidden" id="b2sEmojiTranslation" value='<?php echo esc_attr(json_encode(B2S_Tools::getEmojiTranslationList())); ?>'>

                                            <div class="b2s-reporting-btn-area col-md-9 del-padding-left" style="display: none;">
                                                <div class="panel panel-group">
                                                    <div class="panel-body">
                                                        <div class="pull-right">
                                                            <?php
                                                            $allPosts = get_option('siteurl') . ((substr(get_option('siteurl'), -1, 1) == '/') ? '' : '/') . 'wp-admin/admin.php?page=blog2social-post';
                                                            $videoPosts = get_option('siteurl') . ((substr(get_option('siteurl'), -1, 1) == '/') ? '' : '/') . 'wp-admin/admin.php?page=blog2social-video';
                                                            ?>

                                                            <a href="#" class="btn btn-link btn-sm hidden-sm del-padding-left b2s-info-btn b2s-re-share-info b2s-re-share-info-btn"><?php echo esc_html_e('Info', 'blog2social'); ?></a>
                                                            <?php if (B2S_PLUGIN_USER_VERSION > 0) { ?>
                                                                <button class="btn btn-primary b2s-re-share-btn"><?php esc_html_e('Re-share this post', 'blog2social') ?></button>
                                                            <?php } else { ?>
                                                                <a href="#" class="btn btn-primary b2s-btn-disabled b2sPreFeatureModalBtn" data-title="You want to re-share your blog post?"><?php esc_html_e('Re-share this post', 'blog2social') ?> <?php echo wp_kses($isPremium, array('span' => array('class' => array()))); ?></a>
                                                                <?php
                                                            }
                                                            if (!$isVideo) {
                                                                ?>                                                        
                                                                <a class="btn btn-primary" href="<?php echo esc_url($allPosts); ?>"><?php esc_html_e('Share new post on Social Media', 'blog2social') ?></a>
                                                            <?php } else { ?>
                                                                <a class="btn btn-primary" href="<?php echo esc_url($videoPosts); ?>"><?php esc_html_e('Share new video post', 'blog2social') ?></a>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    <?php } ?>
                                </div>
                            </div>
                            </div>
                            </div>
                            <?php
                            $noLegend = 1;
                            require_once (B2S_PLUGIN_DIR . 'views/b2s/html/footer.php');
                            ?>

                            <!-- B2S-Network -->
                            <div id="b2s-network-list-modal" class="modal fade" role="dialog" aria-labelledby="b2s-network-list-modal" aria-hidden="true" data-backdrop="false"  style="display:none;">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="b2s-modal-close close" data-modal-name="#b2s-network-list-modal">&times;</button>
                                            <h4 class="modal-title"><?php esc_html_e('Connect for', 'blog2social') ?>: <span class="b2s-network-list-modal-mandant"></span></h4>
                                        </div>
                                        <div class="modal-body">
                                            <?php
                                            $portale = new B2S_Ship_Portale();
                                            echo wp_kses($portale->getItemHtml($mandantData['portale'], $isVideo), array( //NOTE VIDEO
                                                'ul' => array(),
                                                'li' => array(
                                                    'data-mandant-default-id' => array(),
                                                ),
                                                'img' => array(
                                                    'class' => array(),
                                                    'src' => array(),
                                                    'alt' => array(),
                                                ),
                                                'span' => array(
                                                    'class' => array(),
                                                ),
                                                'button' => array(
                                                    'onclick' => array(),
                                                    'class' => array(),
                                                    'type' => array(),
                                                    'data-type' => array(),
                                                    'data-title' => array(),
                                                    'data-b2s-auth-url' => array(),
                                                ),
                                                'a' => array(
                                                    'href' => array(),
                                                    'data-auth-method' => array(),
                                                    'class' => array(),
                                                ),
                                                'div' => array(
                                                    'data-instant-sharing' => array(),
                                                )
                                            ));
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="b2s-re-share-info" class="modal fade" role="dialog" aria-labelledby="b2s-re-share-info" aria-hidden="true" data-backdrop="false"  style="display:none;">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="b2s-modal-close close" data-modal-name="#b2s-re-share-info">&times;</button>
                                            <h4 class="modal-title"><?php esc_html_e('Re-share this Post', 'blog2social') ?></h4>
                                        </div>
                                        <div class="modal-body">
                                            <?php esc_html_e('You can re-share your post for a different sharing purpose, or to share on a different choice of networks, profiles, pages or groups, or with different comments or images, or if you want to share your blog post images to image networks only, or re-share them at different times. You may vary your comments and images in order to produce more variations of your social media posts to share more often without sharing the same message over and over again. Whatever your choose to do for re-sharing your post, you can simply click "Re-share this post" and you will be led to the preview page where your can select your networks and edit your texts, comments or images according to your current sharing preferences.', 'blog2social') ?>
                                            <?php if (B2S_PLUGIN_USER_VERSION == 0) { ?>
                                                <hr>
                                                <h4><?php esc_html_e('You want re-share your blog post?', 'blog2social'); ?></h4>
                                                <?php esc_html_e('With Blog2Social Premium you can:', 'blog2social') ?>
                                                <br>
                                                <br>
                                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Post on pages and groups', 'blog2social') ?><br>
                                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Share on multiple profiles, pages and groups', 'blog2social') ?><br>
                                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Auto-post and auto-schedule new and updated blog posts', 'blog2social') ?><br>
                                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Schedule your posts at the best times on each network', 'blog2social') ?><br>
                                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Best Time Manager: use predefined best time scheduler to auto-schedule your social media posts', 'blog2social') ?><br>
                                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Schedule your post for one time, multiple times or recurrently', 'blog2social') ?><br>
                                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Schedule and re-share old posts', 'blog2social') ?><br>
                                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Select link format or image format for your posts', 'blog2social') ?><br>
                                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Select individual images per post', 'blog2social') ?><br>
                                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Reporting & calendar: keep track of your published and scheduled social media posts', 'blog2social') ?><br>
                                                <br>
                                                <a target="_blank" href="<?php echo esc_url(B2S_Tools::getSupportLink('affiliate')); ?>" class="btn btn-success center-block"><?php esc_html_e('Upgrade to SMART and above', 'blog2social') ?></a>
                                                <br>
                                                <center> <?php echo sprintf(__('or <a target="_blank" href="%s">start with free 30-days-trial of Blog2Social Premium</a> (no payment information needed)', 'blog2social'), esc_url('https://service.blog2social.com/trial')); ?> </center>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="b2s-network-setting-save" class="modal fade" role="dialog" aria-labelledby="b2s-network-setting-save" aria-hidden="true" data-backdrop="false"  style="display:none;">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="b2s-modal-close close" data-modal-name="#b2s-network-setting-save">&times;</button>
                                            <h4 class="modal-title"><?php esc_html_e('Save network selection', 'blog2social') ?></h4>
                                        </div>
                                        <div class="modal-body">
                                            <?php esc_html_e('You can save your current network selection. This network selection will be loaded automatically next time you open the social media post editor via "Site & Blog Content" ->"Share on Social Media" or "Social Media Posts" ->"Customize & Schedule".', 'blog2social') ?>
                                            <br><br>
                                            <?php esc_html_e('Your saved networks will be activated for your schedule (green checkmark) in the right side navigation. You can  select or deselect social network accounts at any time by clicking on them or connect new social networks on the "+ Add more" icon on top of the navigation bar.', 'blog2social') ?>
                                            <br><br>
                                            <?php esc_html_e('This allows you to adjust your network selection at any time and save it by clicking on "Save network selection".', 'blog2social') ?>
                                            <br><br>
                                            <span class="b2s-bold"><?php esc_html_e('Note: ', 'blog2social') ?></span><?php echo sprintf(__('To define and save more network selections for your posting purposes, you can use the option "Multiple Network collections" (Premium feature) to define <a href="%s" target="_blank">multiple network collections in the social networks section</a>.', 'blog2social'), esc_url(B2S_Tools::getSupportLink('network_mandant_collection'))); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div id="b2s-network-sched-post-info" class="modal fade" role="dialog" aria-labelledby="b2s-network-sched-post-info" aria-hidden="true" data-backdrop="false"  style="display:none;">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="b2s-modal-close close" data-modal-name="#b2s-network-sched-post-info">&times;</button>
                                            <h4 class="modal-title"><?php esc_html_e('Your blog post is not yet published on your Wordpress!', 'blog2social') ?></h4>
                                        </div>
                                        <div class="modal-body">
                                            <p><?php esc_html_e('At least one of your selected networks is set to "Share Now"', 'blog2social') ?></p>
                                            <br>
                                            <div class="clearfix"></div>
                                            <div class="col-md-6 del-padding-left">
                                                <button type="button" class="b2s-modal-close btn btn-success btn-block" data-modal-name="#b2s-network-sched-post-info"><?php esc_html_e('Schedule your post', 'blog2social') ?></button>
                                            </div>
                                            <div class="col-md-6 del-padding-right">
                                                <button type="button" class="b2s-modal-close btn btn-primary btn-block" data-modal-name="#b2s-network-sched-post-info" id="b2s-network-sched-post-info-ignore"><?php esc_html_e('Ignore & share', 'blog2social') ?></button>
                                            </div>
                                            <br>
                                            <br>

                                            <?php if (B2S_PLUGIN_USER_VERSION == 0) { ?>
                                                <hr>
                                                <h4><?php esc_html_e('You want to schedule your posts and use the Best Time Scheduler?', 'blog2social'); ?></h4>
                                                <?php esc_html_e('With Blog2Social Premium you can:', 'blog2social') ?>
                                                <br>
                                                <br>
                                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Post on pages and groups', 'blog2social') ?><br>
                                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Share on multiple profiles, pages and groups', 'blog2social') ?><br>
                                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Auto-post and auto-schedule new and updated blog posts', 'blog2social') ?><br>
                                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Schedule your posts at the best times on each network', 'blog2social') ?><br>
                                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Best Time Manager: use predefined best time scheduler to auto-schedule your social media posts', 'blog2social') ?><br>
                                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Schedule your post for one time, multiple times or recurrently', 'blog2social') ?><br>
                                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Schedule and re-share old posts', 'blog2social') ?><br>
                                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Select link format or image format for your posts', 'blog2social') ?><br>
                                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Select individual images per post', 'blog2social') ?><br>
                                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Reporting & calendar: keep track of your published and scheduled social media posts', 'blog2social') ?><br>
                                                <br>
                                                <a target="_blank" href="<?php echo esc_url(B2S_Tools::getSupportLink('affiliate')); ?>" class="btn btn-success center-block"><?php esc_html_e('Upgrade to SMART and above', 'blog2social') ?></a>
                                                <br>
                                                <center> <?php echo sprintf(__('or <a target="_blank" href="%s">start with free 30-days-trial of Blog2Social Premium</a> (no payment information needed)', 'blog2social'), esc_url('https://service.blog2social.com/trial')); ?> </center>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div id="b2s-sched-post-modal" class="modal fade" role="dialog" aria-labelledby="b2s-sched-post-modal" aria-hidden="true" data-backdrop="false"  style="display:none;">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="b2s-modal-close close" data-modal-name="#b2s-sched-post-modal">&times;</button>
                                            <h4 class="modal-title"><?php esc_html_e('Need to schedule your posts?', 'blog2social') ?></h4>
                                        </div>
                                        <div class="modal-body">
                                            <p><?php esc_html_e('Blog2Social Premium covers everything you need.', 'blog2social') ?></p>
                                            <br>
                                            <div class="clearfix"></div>
                                            <b><?php esc_html_e('Schedule for specific dates', 'blog2social') ?></b>
                                            <p><?php esc_html_e('You want to publish a post on a specific date? No problem! Just enter your desired date and you are ready to go!', 'blog2social') ?></p>
                                            <br>
                                            <b><?php esc_html_e('Schedule post recurrently', 'blog2social') ?></b>
                                            <p><?php esc_html_e('You have evergreen content you want to re-share from time to time in your timeline? Schedule your evergreen content to be shared once, multiple times or recurringly at specific times.', 'blog2social') ?></p>
                                            <br>
                                            <b><?php esc_html_e('Best Time Scheduler', 'blog2social') ?></b>
                                            <p><?php esc_html_e('Whenever you publish a post, only a fraction of your followers will actually see your post. Use the Blog2Social Best Times Scheduler to share your post at the best times for each social network. Get more outreach and extend the lifespan of your posts.', 'blog2social') ?></p>
                                            <br>
                                            <?php if (B2S_PLUGIN_USER_VERSION == 0) { ?>
                                                <hr>
                                                <?php esc_html_e('With Blog2Social Premium you can:', 'blog2social') ?>
                                                <br>
                                                <br>
                                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Post on pages and groups', 'blog2social') ?><br>
                                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Share on multiple profiles, pages and groups', 'blog2social') ?><br>
                                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Auto-post and auto-schedule new and updated blog posts', 'blog2social') ?><br>
                                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Schedule your posts at the best times on each network', 'blog2social') ?><br>
                                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Best Time Manager: use predefined best time scheduler to auto-schedule your social media posts', 'blog2social') ?><br>
                                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Schedule your post for one time, multiple times or recurrently', 'blog2social') ?><br>
                                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Schedule and re-share old posts', 'blog2social') ?><br>
                                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Select link format or image format for your posts', 'blog2social') ?><br>
                                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Select individual images per post', 'blog2social') ?><br>
                                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Reporting & calendar: keep track of your published and scheduled social media posts', 'blog2social') ?><br>
                                                <br>
                                                <a target="_blank" href="<?php echo esc_url(B2S_Tools::getSupportLink('affiliate')); ?>" class="btn btn-success center-block"><?php esc_html_e('Upgrade to SMART and above', 'blog2social') ?></a>
                                                <br>
                                                <center> <?php echo sprintf(__('or <a target="_blank" href="%s">start with free 30-days-trial of Blog2Social Premium</a> (no payment information needed)', 'blog2social'), esc_url('https://service.blog2social.com/trial')); ?> </center>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div id="b2s-network-select-image" class="modal fade" role="dialog" aria-labelledby="b2s-network-select-image" aria-hidden="true" data-backdrop="false"  style="display:none;">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="b2s-modal-close close" data-modal-name="#b2s-network-select-image">&times;</button>
                                            <h4 class="modal-title"><?php esc_html_e('Select image for', 'blog2social') ?> <span class="b2s-selected-network-for-image-info"></span></h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-xs-12">
                                                    <?php
                                                    $image = new B2S_Ship_Image();
                                                    if (!empty($selImg)) {
                                                        $image->setImageData(array(array($selImg)));
                                                    }
                                                    echo wp_kses($image->getItemHtml($postData->ID, $postData->post_content, $postUrl, $userLang), array(
                                                        'br' => array(),
                                                        'div' => array(
                                                            'class' => array(),
                                                            'style' => array()
                                                        ),
                                                        'span' => array(
                                                            'id' => array(),
                                                            'class' => array()
                                                        ),
                                                        'a' => array(
                                                            'target' => array(),
                                                            'href' => array()
                                                        ),
                                                        'i' => array(
                                                            'class' => array()
                                                        ),
                                                        'label' => array(
                                                            'for' => array()
                                                        ),
                                                        'img' => array(
                                                            'class' => array(),
                                                            'alt' => array(),
                                                            'src' => array()
                                                        ),
                                                        'input' => array(
                                                            'type' => array(),
                                                            'value' => array(),
                                                            'class' => array(),
                                                            'name' => array(),
                                                            'id' => array(),
                                                            'checked' => array(),
                                                        ),
                                                        'button' => array(
                                                            'class' => array(),
                                                            'data-post-id' => array(),
                                                            'data-network-id' => array(),
                                                            'data-network-auth-id' => array(),
                                                            'data-meta-type' => array(),
                                                            'data-image-count' => array(),
                                                            'style' => array(),
                                                        )
                                                    ));
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="modal fade b2s-publish-approve-modal" tabindex="-1" role="dialog" aria-labelledby="b2s-publish-approve-modal" aria-hidden="true" data-backdrop="false"  style="display:none;">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title"><?php esc_html_e('Do you want to mark this post as published ?', 'blog2social') ?> </h4>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" value="" id="b2s-approve-network-auth-id">
                                            <input type="hidden" value="" id="b2s-approve-post-id">
                                            <button class="btn btn-success b2s-approve-publish-confirm-btn"><?php esc_html_e('YES', 'blog2social') ?></button>
                                            <button class="btn btn-default" data-dismiss="modal"><?php esc_html_e('NO', 'blog2social') ?></button>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div id="b2s-tos-xing-group-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="b2s-tos-xing-group-modal" aria-hidden="true" data-backdrop="false"  style="display:none;">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="b2s-modal-close close" data-modal-name="#b2s-tos-xing-group-modal">&times;</button>
                                            <h4 class="modal-title"><?php esc_html_e('About Xing guidelines for crossposting in groups', 'blog2social') ?> </h4>
                                        </div>
                                        <div class="modal-body">
                                            <?php esc_html_e('Good to know: Xing allows publishing identical content once within one group.', 'blog2Social') ?>
                                            <br><a href="<?php echo esc_url(B2S_Tools::getSupportLink('network_tos_blog_082018')); ?>" target="_blank"><?php esc_html_e('Learn more about the Xing guidelines.', 'blog2social') ?></a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="b2s-tos-xing-group-max-count-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="b2s-tos-xing-group-max-count-modal" aria-hidden="true" data-backdrop="false"  style="display:none;">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="b2s-modal-close close" data-modal-name="#b2s-tos-xing-group-max-count-modal">&times;</button>
                                            <h4 class="modal-title"><?php esc_html_e('About Xing guidelines for crossposting in groups', 'blog2social') ?> </h4>
                                        </div>
                                        <div class="modal-body">
                                            <?php esc_html_e('Good to know: Xing allows crossposting of identical content in up to 3 different groups.', 'blog2Social') ?>
                                            <br><a href="<?php echo esc_url(B2S_Tools::getSupportLink('network_tos_blog_082018')); ?>" target="_blank"><?php esc_html_e('Learn more about the Xing guidelines.', 'blog2social') ?></a>
                                        </div>
                                    </div>
                                </div>
                            </div>



                            <div id="b2s-post-ship-item-post-format-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="b2s-post-ship-item-post-format-modal" aria-hidden="true" data-backdrop="false"  style="display:none;">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="b2s-modal-close close" data-modal-name="#b2s-post-ship-item-post-format-modal">&times;</button>
                                            <h4 class="modal-title"><?php esc_html_e('Choose your', 'blog2social') ?> <span id="b2s-post-ship-item-post-format-network-title"></span> <?php esc_html_e('Post Format', 'blog2social') ?>
                                                <?php if (B2S_PLUGIN_USER_VERSION >= 2) { ?>
                                                    <?php esc_html_e('for:', 'blog2social') ?> <span id="b2s-post-ship-item-post-format-network-display-name"></span>
                                                <?php } ?>
                                            </h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-xs-12">
                                                    <?php
                                                    $settingsItem = new B2S_Settings_Item();
                                                    echo wp_kses($settingsItem->getNetworkSettingsHtml(), array(
                                                        'div' => array(
                                                            'class' => array(),
                                                            'data-post-format-type' => array(),
                                                            'data-network-type' => array(),
                                                            'data-network-id' => array(),
                                                            'data-network-title' => array(),
                                                            'style' => array()
                                                        ),
                                                        'b' => array(),
                                                        'br' => array(),
                                                        'a' => array(
                                                            'target' => array(),
                                                            'href' => array()
                                                        ),
                                                        'hr' => array(),
                                                        'span' => array(
                                                            'class' => array()
                                                        ),
                                                        'label' => array(),
                                                        'input' => array(
                                                            'type' => array(),
                                                            'name' => array(),
                                                            'value' => array(),
                                                            'class' => array(),
                                                            'data-post-wp-type' => array(),
                                                            'data-post-format-type' => array(),
                                                            'data-network-type' => array(),
                                                            'data-network-id' => array(),
                                                            'data-post-format' => array()
                                                        ),
                                                        'img' => array(
                                                            'class' => array(),
                                                            'src' => array()
                                                        )
                                                    ));
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-xs-12">
                                                    <div class="text-center">
                                                        <br>
                                                        <div class="b2s-post-format-settings-info" data-network-id="1" style="display:none;">
                                                            <b><?php esc_html_e('Define the default settings for the custom post format for all of your Facebook accounts in the Blog2Social settings.', 'blog2social'); ?></b>
                                                        </div>
                                                        <div class="b2s-post-format-settings-info" data-network-id="2" style="display:none;">
                                                            <b><?php esc_html_e('Define the default settings for the custom post format for all of your Twitter accounts in the Blog2Social settings.', 'blog2social'); ?></b>
                                                        </div>
                                                        <div class="b2s-post-format-settings-info" data-network-id="3" style="display:none;">
                                                            <b><?php esc_html_e('Define the default settings for the custom post format for all of your LinkedIn accounts in the Blog2Social settings.', 'blog2social'); ?></b>
                                                        </div>
                                                        <div class="b2s-post-format-settings-info" data-network-id="12" style="display:none;">
                                                            <b><?php esc_html_e('Define the default settings for the custom post format for all of your Instagram accounts in the Blog2Social settings.', 'blog2social'); ?></b>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="b2s-save-draft-modal" class="modal fade" role="dialog" aria-labelledby="b2s-save-draft-modal" aria-hidden="true" data-backdrop="false"  style="display:none;">
                                <div class="modal-dialog modal-md">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="b2s-modal-close close" data-modal-name="#b2s-save-draft-modal">&times;</button>
                                            <h4 class="modal-title"><?php esc_html_e('Overwrite Draft', 'blog2social'); ?></h4>
                                        </div>
                                        <div class="modal-body">
                                            <b><?php esc_html_e('There is already a saved draft for this WordPress post or page. If you save a new draft it will overwrite the old draft.  Are you sure you want to overwrite your draft?', 'blog2social') ?></b>
                                        </div>
                                        <div class="modal-footer">
                                            <button class="btn btn-default" data-dismiss="modal"><?php esc_html_e('NO', 'blog2social') ?></button>
                                            <button class="btn btn-primary b2s-save-draft-confirm-btn"><?php esc_html_e('YES', 'blog2social') ?></button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal fade" id="b2sNetworkAddInstagramInfoModal" tabindex="-1" role="dialog" aria-labelledby="b2sNetworkAddInstagramInfoModal" aria-hidden="true" data-backdrop="false"  style="display:none;">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="b2s-modal-close close" data-modal-name="#b2sNetworkAddInstagramInfoModal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <h4 class="modal-title"><?php esc_html_e('Add Profile', 'blog2social') ?></h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <?php echo sprintf(__('When you connect Blog2Social with your Instagram account, you might get a notification from Instagram that a server from Germany in the Cologne area is trying to access your account. This is a general security notification due to the fact that the Blog2Social server is located in this area. This is an automatic process that is necessary to establish a connection to Instagram. Rest assured, that this is a common and regular security notice to keep your account safe. <a href="%s" target="_blank">More information: How to connect with Instagram.</a>.', 'blog2social'), esc_url(B2S_Tools::getSupportLink('instagram_auth_faq'))); ?>
                                                    <button class="btn btn-primary pull-right b2s-add-network-continue-btn"><?php esc_html_e('Continue', 'blog2social'); ?></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal fade" id="b2sNetworkAddInstagramBusinessInfoModal" tabindex="-1" role="dialog" aria-labelledby="b2sNetworkAddInstagramBusinessInfoModal" aria-hidden="true" data-backdrop="false"  style="display:none;">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="b2s-modal-close close" data-modal-name="#b2sNetworkAddInstagramBusinessInfoModal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <h4 class="modal-title"><?php esc_html_e('Connect Instagram Business Account', 'blog2social') ?></h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <?php esc_html_e('Please note: In order to connect your Instagram account to Blog2Social, please ensure the following:', 'blog2social') ?>
                                                    <br>
                                                    <?php esc_html_e('1. Your Instagram account is set to "Business" and not "Creator".', 'blog2social') ?>
                                                    <br>
                                                    <?php esc_html_e('2. Your Instagram account is linked to a Facebook page.', 'blog2social') ?>
                                                    <br>
                                                    <?php esc_html_e('3. Blog2Social has the permission to publish your posts.', 'blog2social') ?>
                                                    <br>
                                                    <br>
                                                    <?php echo sprintf(__('You will find more information and detailed instructions in the <a href="%s" target="_blank">Instagram Business guide</a>.', 'blog2social'), esc_url(B2S_Tools::getSupportLink('instagram_business_auth_faq'))); ?>
                                                    <button class="btn btn-primary pull-right b2s-add-network-continue-btn"><?php esc_html_e('Continue', 'blog2social'); ?></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal fade" id="b2sNetworkAddPageInfoModal" tabindex="-1" role="dialog" aria-labelledby="b2sNetworkAddPageInfoModal" aria-hidden="true" data-backdrop="false"  style="display:none;">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="b2s-modal-close close" data-modal-name="#b2sNetworkAddPageInfoModal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <h4 class="modal-title"><?php esc_html_e('Add Page', 'blog2social') ?></h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <?php echo sprintf(__('Please make sure to log in with your account which manages your pages and <a href="%s" target="_blank">follow this guide to select all your pages</a>.', 'blog2social'), esc_url(B2S_Tools::getSupportLink('fb_page_auth'))); ?>
                                                    <button class="btn btn-primary pull-right b2s-add-network-continue-btn"><?php esc_html_e('Continue', 'blog2social'); ?></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal fade" id="b2sNetworkAddGroupInfoModal" tabindex="-1" role="dialog" aria-labelledby="b2sNetworkAddGroupInfoModal" aria-hidden="true" data-backdrop="false"  style="display:none;">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="b2s-modal-close close" data-modal-name="#b2sNetworkAddGroupInfoModal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <h4 class="modal-title"><?php esc_html_e('Add Group', 'blog2social') ?></h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <?php echo sprintf(__('Please make sure to log in with your account which manages your groups and <a href="%s" target="_blank">follow this guide to select all your groups</a>.', 'blog2social'), esc_url(B2S_Tools::getSupportLink('fb_group_auth'))); ?>
                                                    <button class="btn btn-primary pull-right b2s-add-network-continue-btn"><?php esc_html_e('Continue', 'blog2social'); ?></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <input id="b2sOpenDraftIncompleteModal" type="hidden" value="<?php echo (($draftIncompleteModal) ? '1' : '0'); ?>">
                            <div class="modal fade" id="b2sDraftIncompleteModal" tabindex="-1" role="dialog" aria-labelledby="b2sDraftIncompleteModal" aria-hidden="true" data-backdrop="false"  style="display:none;">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="b2s-modal-close close" data-modal-name="#b2sDraftIncompleteModal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <h4 class="modal-title"><?php esc_html_e('Please note:', 'blog2social') ?></h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <?php esc_html_e('The scheduling for your social media post has changed as a selected social media network is no longer connected to Blog2Social. Please check the network connections under "Networks" and make sure that the required networks are connected.', 'blog2social'); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="b2sImageZoomModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="b2sImageZoomModal" aria-hidden="true" data-backdrop="false"  style="display:none;">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-body">
                                            <button type="button" class="btn btn-primary btn-circle b2sImageZoomModalClose b2s-modal-close close" data-modal-name="#b2sImageZoomModal" aria-label="Close"><i class="glyphicon glyphicon-remove"></i></button>
                                            <img id="b2sImageZoom">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="b2s_ship_calendar" style="display:block;"></div>
                            <br>
                            <script>
                                var b2s_calendar_locale = '<?php echo esc_js(strtolower(substr(get_locale(), 0, 2))); ?>';
                                var b2s_calendar_date = '<?php echo esc_js(B2S_Util::getbyIdentLocalDate($userTimeZoneOffset, "Y-m-d")); ?>';
                                var b2s_calendar_datetime = '<?php echo esc_js(B2S_Util::getbyIdentLocalDate($userTimeZoneOffset)); ?>';
                                var b2s_plugin_url = '<?php echo esc_url(B2S_PLUGIN_URL); ?>';
                                var b2s_cur_source_ship_calendar = new Array();
                            </script>


                            <input type="hidden" id="b2sLang" value="<?php echo esc_attr(substr(B2S_LANGUAGE, 0, 2)); ?>">
                            <input type="hidden" id="b2sUserTimeFormat" value="<?php echo esc_attr($optionUserTimeFormat); ?>">
                            <input type="hidden" id="b2sJSTextAddSchedule" value="<?php esc_html_e("add Schedule", "blog2social"); ?>">
                            <input type="hidden" id="b2sInsertImageType" value="0">
                            <input type="hidden" id="b2sUserLang" value="<?php echo esc_attr($userLang); ?>">
                            <input type="hidden" id="b2sPostId" value="<?php echo esc_attr($postData->ID); ?>">
                            <input type="hidden" id="selSchedDate" value="<?php echo esc_attr($selSchedDate); ?>">
                            <input type="hidden" id="selProfile" value="<?php echo esc_attr($selProfile); ?>">   
                            <input type="hidden" id="b2sPostType" value="<?php echo (isset($_GET['b2sPostType']) && sanitize_text_field($_GET['b2sPostType']) == 'ex') ? 'ex' : ''; ?>">
                            <input type="hidden" id="b2sDefault_url" name="default_url" value="<?php echo esc_attr((isset($_GET['b2sPostType']) && sanitize_text_field($_GET['b2sPostType']) == 'ex') ? (($exPostFormat == 0) ? $postData->guid : '') : (get_permalink($postData->ID) !== false ? get_permalink($postData->ID) : $postData->guid)); ?>">
                            <input type="hidden" id="b2sPortalImagePath" value="<?php echo esc_url(plugins_url('/assets/images/portale/', B2S_PLUGIN_FILE)); ?>">
                            <input type="hidden" id="b2sTosXingGroupCrosspostingLimit" value="<?php echo esc_attr($tosCrossPosting[19][2]); ?>">
                            <input type="hidden" id="b2sServerUrl" value="<?php echo esc_url(B2S_PLUGIN_SERVER_URL); ?>">
                            <input type="hidden" id="b2sTwitterOrginalPost" value="">
                            <input type="hidden" id="b2sJsTextLoading" value="<?php esc_html_e('Loading...', 'blog2social') ?>">
                            <input type="hidden" id="b2sJsTextPublish" value="<?php esc_html_e('published', 'blog2social') ?>">
                            <input type="hidden" id="b2sJsTextConnectionFail" value="<?php esc_html_e('The connection to the server failed. Please try again! You can find more information and solutions in the guide for server connection', 'blog2social') ?>">
                            <input type="hidden" id="b2sJsTextConnectionFailLink" value="<?php echo ($userLang == 'de') ? 'https://www.blog2social.com/de/faq/content/9/108/de/die-verbindung-zum-server-ist-fehlgeschlagen-bitte-versuche-es-erneut.html' : 'https://www.blog2social.com/en/faq/content/9/106/en/the-connection-to-the-server-failed-please-try-again.html'; ?>"> 
                            <input type="hidden" id="b2sJsTextConnectionFailLinkText" value="<?php esc_html_e('Give me more information', 'blog2social') ?>"> 
                            <input type="hidden" id="b2sSelectedNetworkAuthId" value="<?php echo (isset($_GET['network_auth_id']) && (int) $_GET['network_auth_id'] > 0) ? (int) esc_attr(sanitize_text_field($_GET['network_auth_id'])) : ''; ?>">
                            <input type="hidden" id="b2sMultiSelectedNetworkAuthId" value="<?php echo (isset($_GET['multi_network_auth_id']) && !empty($_GET['multi_network_auth_id'])) ? esc_attr(sanitize_text_field($_GET['multi_network_auth_id'])) : ''; ?>">
                            <input type="hidden" id="b2sDefaultNoImage" value="<?php echo esc_url(plugins_url('/assets/images/no-image.png', B2S_PLUGIN_FILE)); ?>">
                            <input type="hidden" id="isMetaChecked" value="<?php echo esc_attr($postData->ID); ?>">
                            <input type="hidden" id="isOgMetaChecked" value="<?php echo (isset($b2sGeneralOptions['og_active']) ? (int) $b2sGeneralOptions['og_active'] : 0); ?>">
                            <input type="hidden" id="ogMetaNetworks" value="<?php echo esc_attr(implode(';', json_decode(B2S_PLUGIN_NETWORK_META_TAGS, true)['og'])); ?>">
                            <input type="hidden" id="isCardMetaChecked" value="<?php echo (isset($b2sGeneralOptions['card_active']) ? (int) $b2sGeneralOptions['card_active'] : 0); ?>">
                            <input type="hidden" id="isLegacyMode" value="<?php echo (isset($b2sGeneralOptions['legacy_mode']) ? (int) $b2sGeneralOptions['legacy_mode'] : 0); ?>">
                            <input type="hidden" id="b2sIsDraft" value="<?php echo ($isDraft) ? '1' : '0' ?>">
                            <input type="hidden" id="b2sIsVideo" value="<?php echo ($isVideo) ? '1' : '0' ?>">
                            <input type="hidden" id="b2sExPostFormat" value="<?php echo esc_attr($exPostFormat); ?>">

                            <?php
                            echo wp_kses($settingsItem->setNetworkSettingsHtml(), array(
                                'input' => array(
                                    'type' => array(),
                                    'class' => array(),
                                    'value' => array(),
                                    'data-post-format-type' => array(),
                                    'data-network-id' => array(),
                                    'data-network-type' => array()
                                )
                            ));
                            ?>
                            <?php
                            if (trim(strtolower($postData->post_status)) == 'future' || !empty($selSchedDate)) {
                                $schedDate = (($postData->post_status) == 'future') ? $postData->post_date_gmt : $selSchedDate; // prio wp sched
                                ?>
                                <input type="hidden" id="b2sBlogPostSchedDate" value="<?php echo esc_attr(strtotime($schedDate)); ?>000"> <!--for milliseconds-->
                                <input type="hidden" id="b2sSchedPostInfoIgnore" value="0">
                            <?php }
                            ?>

                            <div id="b2s-network-editor-image-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="b2s-network-editor-image" aria-hidden="true" data-backdrop="false" style="display: none;">
                                <div class="modal-dialog modal-lg">
                                    <div class=" modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="b2s-modal-close close b2s-network-editor-image-btn-close"
                                                    data-modal-name="#b2s-network-editor-image-modal">&times;</button>
                                            <h4 class="modal-title"><?php esc_html_e('Edit image for', 'blog2social') ?>
                                                <span id="b2s-network-editor-image-network-account"></span>
                                            </h4>
                                        </div>
                                        <div id="b2s-network-editor-image-area" class="modal-body">
                                            <div id="b2s-network-editor-error-not-save" class="alert alert-danger">
                                                <span class="glyphicon glyphicon-remove"></span>  <?php esc_html_e('Image could not save in your library. Please make sure that you have enough permissions to save the image.', 'blog2social') ?>
                                            </div>
                                            <div id="b2s-network-editor-image-div">
                                                <img id="b2s-network-editor-image-src" src="" alt="">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <cropperoptions id="b2s-network-editor-image-option-area">
                                                <button class=" btn btn-primary b2s-network-editor-image-option" id="b2s-rot-left">&#8592</button>
                                                <button class=" btn btn-primary b2s-network-editor-image-option" id="b2s-rot-right">&#8594</button>
                                                <button class=" btn btn-primary b2s-network-editor-image-option" id="b2s-x-mirror">&#8596</button>
                                                <button class=" btn btn-primary b2s-network-editor-image-option" id="b2s-y-mirror">&#8597</button>
                                                <button class=" btn btn-success b2s-network-editor-image-option" id="b2s-network-editor-image-btn-save"><?php esc_html_e('Save', 'blog2social') ?></button>
                                            </cropperoptions>
                                            <input id="b2s-network-editor-image-rest-nonce" value="<?php echo esc_attr(wp_create_nonce('wp_rest')); ?>" type="hidden">
                                            <input id="b2s-network-editor-image-rest-endpoint" value="<?php echo esc_attr(rest_url()); ?>" type="hidden">
                                            <input id="b2s-network-editor-image-network-auth-id" value="" type="hidden">
                                            <input id="b2s-network-editor-image-network-id" value="" type="hidden">
                                            <input id="b2s-network-editor-image-name" value="" type="hidden">
                                            <input id="b2s-network-editor-image-network-count" value="" type="hidden">
                                        </div>
                                    </div>
                                </div>