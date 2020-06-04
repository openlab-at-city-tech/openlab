<?php
/* unserialize all saved option for  section 4 options */
$option6 =  unserialize(get_option('sfsi_section6_options', false));
$option4 =  unserialize(get_option('sfsi_section4_options', false));
$option2 =  unserialize(get_option('sfsi_section2_options', false));
if (!isset($option4['sfsi_facebook_mypageCounts'])) {
    $option4['sfsi_facebook_mypageCounts'] = '';
}

/*
 * Sanitize, escape and validate values
 */
$option4['sfsi_display_counts']         = (isset($option4['sfsi_display_counts'])) ? sanitize_text_field($option4['sfsi_display_counts']) : '';
$option4['sfsi_email_countsFrom']         = (isset($option4['sfsi_email_countsFrom'])) ? sanitize_text_field($option4['sfsi_email_countsFrom']) : '';
$option4['sfsi_email_manualCounts']     = (isset($option4['sfsi_email_manualCounts'])) ? intval($option4['sfsi_email_manualCounts']) : '';
$option4['sfsi_rss_countsDisplay']         = (isset($option4['sfsi_rss_countsDisplay'])) ? sanitize_text_field($option4['sfsi_rss_countsDisplay']) : '';
$option4['sfsi_rss_manualCounts']         = (isset($option4['sfsi_rss_manualCounts'])) ? intval($option4['sfsi_rss_manualCounts']) : '';
$option4['sfsi_email_countsDisplay']     = (isset($option4['sfsi_email_countsDisplay'])) ? sanitize_text_field($option4['sfsi_email_countsDisplay']) : '';

$option4['sfsi_facebook_countsDisplay'] = (isset($option4['sfsi_facebook_countsDisplay']))
    ? sanitize_text_field($option4['sfsi_facebook_countsDisplay'])
    : '';
$option4['sfsi_facebook_countsFrom']     = (isset($option4['sfsi_facebook_countsFrom']))
    ? sanitize_text_field($option4['sfsi_facebook_countsFrom'])
    : '';
$option4['sfsi_facebook_mypageCounts']     = (isset($option4['sfsi_facebook_mypageCounts']))
    ? sfsi_sanitize_field($option4['sfsi_facebook_mypageCounts'])
    : '';
$option4['sfsi_facebook_manualCounts']     = (isset($option4['sfsi_facebook_manualCounts']))
    ? intval($option4['sfsi_facebook_manualCounts'])
    : '';
$option4['sfsi_twitter_countsDisplay']     = (isset($option4['sfsi_twitter_countsDisplay']))
    ? sanitize_text_field($option4['sfsi_twitter_countsDisplay'])
    : '';
$option4['sfsi_twitter_countsFrom']     = (isset($option4['sfsi_twitter_countsFrom']))
    ? sanitize_text_field($option4['sfsi_twitter_countsFrom'])
    : '';
$option4['sfsi_twitter_manualCounts']     = (isset($option4['sfsi_twitter_manualCounts']))
    ? intval($option4['sfsi_twitter_manualCounts'])
    : '';
$option4['tw_consumer_key']             = (isset($option4['tw_consumer_key']))
    ? sfsi_sanitize_field($option4['tw_consumer_key'])
    : '';
$option4['tw_consumer_secret']             = (isset($option4['tw_consumer_secret']))
    ? sfsi_sanitize_field($option4['tw_consumer_secret'])
    : '';
$option4['tw_oauth_access_token']         = (isset($option4['tw_oauth_access_token']))
    ? sfsi_sanitize_field($option4['tw_oauth_access_token'])
    : '';
$option4['tw_oauth_access_token_secret'] = (isset($option4['tw_oauth_access_token_secret']))
    ? sfsi_sanitize_field($option4['tw_oauth_access_token_secret'])
    : '';
$option4['sfsi_youtube_countsDisplay']     = (isset($option4['sfsi_youtube_countsDisplay']))
    ? sanitize_text_field($option4['sfsi_youtube_countsDisplay'])
    : '';
$option4['sfsi_youtube_countsFrom']     = (isset($option4['sfsi_youtube_countsFrom']))
    ? sanitize_text_field($option4['sfsi_youtube_countsFrom'])
    : '';
// $option4['sfsi_youtubeusernameorid'] 	= 	(isset($option4['sfsi_youtubeusernameorid']))
// 												? sanitize_text_field($option4['sfsi_youtubeusernameorid'])
// 												: '';
$option4['sfsi_youtube_manualCounts']     = (isset($option4['sfsi_youtube_manualCounts']))
    ? intval($option4['sfsi_youtube_manualCounts'])
    : '';

$option4['sfsi_instagram_manualCounts'] = (isset($option4['sfsi_instagram_manualCounts']))
    ? intval($option4['sfsi_instagram_manualCounts'])
    : '';
$option4['sfsi_instagram_User']         = (isset($option4['sfsi_instagram_User']))
    ? sfsi_sanitize_field($option4['sfsi_instagram_User'])
    : '';
$option4['sfsi_instagram_clientid']     = (isset($option4['sfsi_instagram_clientid']))
    ? sfsi_sanitize_field($option4['sfsi_instagram_clientid'])
    : '';
$option4['sfsi_instagram_appurl']       = (isset($option4['sfsi_instagram_appurl']))
    ? sfsi_sanitize_field($option4['sfsi_instagram_appurl'])
    : '';
$option4['sfsi_instagram_token']        = (isset($option4['sfsi_instagram_token']))
    ? sfsi_sanitize_field($option4['sfsi_instagram_token'])
    : '';
$option4['sfsi_instagram_countsFrom']     = (isset($option4['sfsi_instagram_countsFrom']))
    ? sanitize_text_field($option4['sfsi_instagram_countsFrom'])
    : '';
$option4['sfsi_instagram_countsDisplay'] = (isset($option4['sfsi_instagram_countsDisplay']))
    ? sanitize_text_field($option4['sfsi_instagram_countsDisplay'])
    : '';
$option4['sfsi_pinterest_manualCounts'] = (isset($option4['sfsi_pinterest_manualCounts']))
    ? intval($option4['sfsi_pinterest_manualCounts'])
    : '';
$option4['sfsi_linkedIn_manualCounts']     = (isset($option4['sfsi_linkedIn_manualCounts']))
    ? intval($option4['sfsi_linkedIn_manualCounts'])
    : '';

$option4['sfsi_telegram_countsDisplay']         = (isset($option4['sfsi_telegram_countsDisplay'])) ? sanitize_text_field($option4['sfsi_telegram_countsDisplay']) : '';
$option4['sfsi_telegram_manualCounts']         = (isset($option4['sfsi_telegram_manualCounts'])) ? intval($option4['sfsi_telegram_manualCounts']) : '';

$option4['sfsi_vk_countsDisplay']         = (isset($option4['sfsi_vk_countsDisplay'])) ? sanitize_text_field($option4['sfsi_vk_countsDisplay']) : '';
$option4['sfsi_vk_manualCounts']         = (isset($option4['sfsi_vk_manualCounts'])) ? intval($option4['sfsi_vk_manualCounts']) : '';

$option4['sfsi_ok_countsDisplay']         = (isset($option4['sfsi_ok_countsDisplay'])) ? sanitize_text_field($option4['sfsi_ok_countsDisplay']) : '';
$option4['sfsi_ok_manualCounts']         = (isset($option4['sfsi_ok_manualCounts'])) ? intval($option4['sfsi_ok_manualCounts']) : '';

$option4['sfsi_weibo_countsDisplay']         = (isset($option4['sfsi_weibo_countsDisplay'])) ? sanitize_text_field($option4['sfsi_weibo_countsDisplay']) : '';
$option4['sfsi_weibo_manualCounts']         = (isset($option4['sfsi_weibo_manualCounts'])) ? intval($option4['sfsi_weibo_manualCounts']) : '';

$option4['sfsi_wechat_countsDisplay']         = (isset($option4['sfsi_wechat_countsDisplay'])) ? sanitize_text_field($option4['sfsi_wechat_countsDisplay']) : '';
$option4['sfsi_wechat_manualCounts']         = (isset($option4['sfsi_wechat_manualCounts'])) ? intval($option4['sfsi_wechat_manualCounts']) : '';
$option4['sfsi_round_counts']         = (isset($option4['sfsi_round_counts'])) ? sanitize_text_field($option4['sfsi_round_counts']) : '';
$option4['sfsi_original_counts']         = (isset($option4['sfsi_original_counts'])) ? sanitize_text_field($option4['sfsi_original_counts']) : '';
$option4['sfsi_responsive_share_count']         = (isset($option4['sfsi_responsive_share_count'])) ? sanitize_text_field($option4['sfsi_responsive_share_count']) : '';

if (isset($option4['sfsi_youtube_user']) && !empty($option4['sfsi_youtube_user'])) {
    $option4['sfsi_youtube_user']       = sfsi_sanitize_field($option4['sfsi_youtube_user']);
} else {
    if (isset($option2['sfsi_youtubeusernameorid']) && "name" == $option2['sfsi_youtubeusernameorid']  && !empty($option2['sfsi_youtubeusernameorid'])) {
        $option4['sfsi_youtube_user']   = isset($option2['sfsi_ytube_user']) && !empty($option2['sfsi_ytube_user']) ? $option2['sfsi_ytube_user'] : '';
    }
}

if (isset($option4['sfsi_youtube_channelId']) && !empty($option4['sfsi_youtube_channelId'])) {
    $option4['sfsi_youtube_channelId']       = sfsi_sanitize_field($option4['sfsi_youtube_channelId']);
} else {
    if ("id" == $option2['sfsi_youtubeusernameorid'] && isset($option2['sfsi_youtubeusernameorid']) && !empty($option2['sfsi_youtubeusernameorid'])) {
        $option4['sfsi_youtube_channelId']   = isset($option2['sfsi_ytube_chnlid']) && !empty($option2['sfsi_ytube_chnlid']) ? $option2['sfsi_ytube_chnlid'] : '';
    }
}

/* fetch counts for admin sections */
$counts = sfsi_getCounts();
/* check for email icon display */
$email_image = "email.png";

if (isset($option2['sfsi_rss_icons']) && !empty($option2['sfsi_rss_icons']) && $option2['sfsi_rss_icons'] == "sfsi") {
    $email_image = "sf_arow_icn.png";
}
$hide = "display:none;";

?>

<!-- Section 4 "Do you want to display "counts" next to your icons?" main div Start -->
<div class="tab4">
    <p>It’s a psychological fact that people like to follow other people (as explained well in Robert Cialdini’s book “<a href="http://www.amazon.com/Influence-Psychology-Persuasion-Revised-Edition/dp/006124189X" target="_blank" class="lit_txt">Influence</a>”), so when they see that your site has already a good number of Facebook likes, it’s more likely that they will subscribe/like/share your site than if it had 0.</p>
    <p>Therefore, you can select to display the count next to your main icons, which will look like this (example for round icons):</p>

    <!-- sample icons -->
    <ul class="like_icon">
        <li class="rss_section">
            <a href="#" title="RSS">
                <img src="<?php echo SFSI_PLUGURL ?>images/rss.png" alt="RSS" />
            </a>
            <span>12k</span>
        </li>
        <li class="email_section">
            <a href="#" title="Email">
                <img src="<?php echo SFSI_PLUGURL ?>images/<?php echo $email_image; ?>" alt="Email" class="icon_img" />
            </a>
            <span>12k</span>
        </li>
        <li class="facebook_section">
            <a href="#" title="Facebook">
                <img src="<?php echo SFSI_PLUGURL ?>images/facebook.png" alt="Facebook" />
            </a>
            <span>12k</span>
        </li>
        <li class="twitter_section">
            <a href="#" title="Twitter">
                <img src="<?php echo SFSI_PLUGURL ?>images/twitter.png" alt="Twitter" />
            </a>
            <span>12k</span>
        </li>
        <!--         <li class="share_section">
            <a href="#" title="Share">
                <img src="<?php //echo SFSI_PLUGURL 
                            ?>images/share.png" alt="Share" />
            </a>
            <span>12k</span>
        </li> -->
        <li class="youtube_section">
            <a href="#" title="YouTube">
                <img src="<?php echo SFSI_PLUGURL ?>images/youtube.png" alt="YouTube" />
            </a>
            <span>12k</span>
        </li>
        <li class="pinterest_section">
            <a href="#" title="Pinterest">
                <img src="<?php echo SFSI_PLUGURL ?>images/pinterest.png" alt="Pinterest" />
            </a>
            <span>12k</span>
        </li>
        <li class="linkedin_section">
            <a href="#" title="Linked In">
                <img src="<?php echo SFSI_PLUGURL ?>images/linked_in.png" alt="Linked In" />
            </a>
            <span>12k</span>
        </li>
        <li class="instagram_section">
            <a href="#" title="Instagram">
                <img src="<?php echo SFSI_PLUGURL ?>images/instagram.png" alt="instagram" />
            </a>
            <span>12k</span>
        </li>
        <li class="telegram_section">
            <a href="#" title="telegram">
                <img src="<?php echo SFSI_PLUGURL ?>images/icons_theme/default/default_telegram.png" height="50px" alt="telegram" />
            </a>
            <span>12k</span>
        </li>
        <li class="vk_section">
            <a href="#" title="vk">
                <img src="<?php echo SFSI_PLUGURL ?>images/icons_theme/default/default_vk.png" height="50px" alt="vk" />
            </a>
            <span>12k</span>
        </li>
        <li class="ok_section">
            <a href="#" title="ok">
                <img src="<?php echo SFSI_PLUGURL ?>images/icons_theme/default/default_ok.png" height="50px" alt="ok" />
            </a>
            <span>12k</span>
        </li>
        <li class="weibo_section">
            <a href="#" title="weibo">
                <img src="<?php echo SFSI_PLUGURL ?>images/icons_theme/default/default_weibo.png" height="50px" alt="weibo" />
            </a>
            <span>12k</span>
        </li>
        <li class="wechat_section">
            <a href="#" title="wechat">
                <img src="<?php echo SFSI_PLUGURL ?>images/icons_theme/default/default_wechat.png" height="50px" alt="wechat" />
            </a>
            <span>12k</span>
        </li>
    </ul>
    <!-- END sample icons -->

    <p>Of course, if you start at 0, you shoot yourself in the foot with that. So we suggest that you only turn this feature on once you have a good number of followers/likes/shares (min. of 20 – no worries if it’s not too many, it should just not be 0). <b>New:</b> In the Premium Plugin you can define a threshold (min. number of counts) when it will automatically switch to showing the counts. <a style="cursor:pointer" class="pop-up" data-id="sfsi_quickpay-overlay" onclick="sfsi_open_quick_checkout(event)" class="sfisi_font_bold" target="_blank">Go Premium</a>.</p>
    <h4>Enough waffling. So do you want to display counts?</h4>

    <!-- show/hide counts for icons section  START -->
    <ul class="enough_waffling">
        <li>
            <input name="sfsi_display_counts" <?php echo ($option4['sfsi_display_counts'] == 'yes') ?  'checked="true"' : ''; ?> type="radio" value="yes" class="styled" /><label>Yes</label>
        </li>
        <li>
            <input name="sfsi_display_counts" <?php echo ($option4['sfsi_display_counts'] == 'no') ?  'checked="true"' : ''; ?> type="radio" value="no" class="styled" /><label>No</label>
        </li>
    </ul>
    <!-- END  show/hide counts for icons section -->



    <!-- show/hide counts for all icons section  START -->

    <div class="count_sections" style="display:none">
        <?php
         $socialObj = new sfsi_SocialHelper();
         $current_url = sfsi_get_current_page_url();
         $fb_data = $socialObj->sfsi_get_fb($current_url);
         $check_fb_count_more_than_one = $fb_data>0 || $socialObj->sfsi_get_pinterest($current_url)>0;
         
        if (is_ssl() && $check_fb_count_more_than_one) {
            ?>
            <div class="sfsi_new_prmium_follw sfsi_banner_body sfsi_warning_banner">
                <div>
                    <p style="margin-bottom: 12px !important;"><b>Important:</b> Your website used to be on http (before you enabled an SSL certificate to switch to https). We found share counts for your URLs on http which usually get lost
                        after switch to https (because Facebook etc. provide the counts per url, and an url on https is a different url then one on http). <b>We found a solution for that</b> so that your share counts on http and https will
                        be aggregated and your full number of share counts is restored. It is implemented in the Premium Plugin - <a href="https://www.ultimatelysocial.com/usm-premium/?utm_source=usmi_settings_page&utm_campaign=share_count_recovery_notification&utm_medium=link" style="cursor:pointer; color: #12a252 !important;border-bottom: 1px solid #222222;text-decoration: none;font-weight: bold;font-family: unset;" data-id="sfsi_quickpay-overlay" target="_blank">
                            <b>Get it now.</b>
                        </a>
                    </p>
                </div>
                <small class="sfsi_banner_dismiss">Dismiss</small>
            </div>
        <?php
        }
        ?>
        <h4 style="display: inline-block;">Please specify which counts should be shown:</h4>

        <!-- RSS ICON COUNT SECTION-->
        <div class="specify_counts rss_section">
            <div class="radio_section">
                <input name="sfsi_rss_countsDisplay" <?php echo ($option4['sfsi_rss_countsDisplay'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />
            </div>
            <div class="social_icon_like">
                <ul class="like_icon">
                    <li>
                        <a title="RSS">
                            <img src="<?php echo SFSI_PLUGURL ?>images/rss.png" alt="RSS" />
                            <span><?php echo $counts['rss_count']; ?></span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="listing">
                <ul>
                    <li>We cannot track this. So enter the figure here: <input name="sfsi_rss_manualCounts" type="text" class="input" value="<?php echo ($option4['sfsi_rss_manualCounts'] != '') ?  $option4['sfsi_rss_manualCounts'] : ''; ?>" /></li>
                </ul>
            </div>
        </div>
        <!-- END RSS ICON COUNT SECTION-->

        <!-- EMAIL ICON COUNT SECTION-->
        <div class="specify_counts email_section">
            <div class="radio_section">
                <input name="sfsi_email_countsDisplay" <?php echo ($option4['sfsi_email_countsDisplay'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />
            </div>
            <div class="social_icon_like">
                <ul class="like_icon">
                    <li>
                        <a title="Email">
                            <img src="<?php echo SFSI_PLUGURL ?>images/<?php echo $email_image; ?>" alt="Email" />
                            <span><?php echo $counts['email_count']; ?></span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="listing">
                <ul>
                    <li><input name="sfsi_email_countsFrom" <?php echo ($option4['sfsi_email_countsFrom'] == 'source') ?  'checked="true"' : ''; ?> type="radio" value="source" class="styled" />Retrieve the number of subscribers automatically</li>
                    <li><input name="sfsi_email_countsFrom" <?php echo ($option4['sfsi_email_countsFrom'] == 'manual') ?  'checked="true"' : ''; ?> type="radio" value="manual" class="styled" />Enter the figure manually:<input name="sfsi_email_manualCounts" type="text" class="input" value="<?php echo ($option4['sfsi_email_manualCounts'] != '') ?  $option4['sfsi_email_manualCounts'] : ''; ?>" style="<?php echo ($option4['sfsi_email_countsFrom'] == 'source') ?  'display:none;' : ''; ?>" /></li>
                </ul>
            </div>
        </div>
        <!--END  EMAIL  ICON COUNT SECTION-->

        <!-- FACEBOOK ICON COUNT SECTION-->
        <div class="specify_counts facebook_section">
            <div class="radio_section">
                <input name="sfsi_facebook_countsDisplay" <?php echo ($option4['sfsi_facebook_countsDisplay'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />
            </div>
            <div class="social_icon_like">
                <ul class="like_icon">
                    <li><a title="Facebook"><img src="<?php echo SFSI_PLUGURL ?>images/facebook.png" alt="Facebook" /><span><?php echo $counts['fb_count']; ?></span></a></li>
                </ul>
            </div>
            <div class="listing">
                <ul class="sfsi_fb_popup_contain">
                    <li>
                        <input name="sfsi_facebook_countsFrom" <?php echo ($option4['sfsi_facebook_countsFrom'] == 'likes') ?  'checked="true"' : ''; ?> type="radio" value="likes" class="styled" />Retrieve the number of likes <strong>of your blog</strong>
                        <div class="sfsi_prem_fbpgiddesc">
                            <div class="sfsi_prem_fbpgidwpr" style="<?php echo ($option4['sfsi_facebook_countsFrom'] == 'likes' || $option4['sfsi_facebook_countsFrom'] == 'followers' || $option4['sfsi_facebook_countsFrom'] == 'manual') ?  'display:none;' : ''; ?>"></div>

                        </div>
                    </li>
                    <li>
                        <input name="sfsi_facebook_countsFrom" <?php echo ($option4['sfsi_facebook_countsFrom'] == 'mypage') ?  'checked="true"' : ''; ?> type="radio" value="mypage" class="styled" />
                        Retrieve the number of likes <strong>of your Facebook page</strong><br>
                        <div class="sfsi_fbpgiddesc sfsi_fbpaget">
                            <div class="sfsi_fbpgidwpr sfsi_count" style="<?php echo ($option4['sfsi_facebook_countsFrom'] == 'likes' || $option4['sfsi_facebook_countsFrom'] == 'followers' || $option4['sfsi_facebook_countsFrom'] == 'manual') ?  'display:none;' : ''; ?>">Facebook page ID:</div>
                            <input name="sfsi_facebook_mypageCounts" type="text" class="input mypginpt" value="<?php echo ($option4['sfsi_facebook_mypageCounts'] != '') ?  $option4['sfsi_facebook_mypageCounts'] : ''; ?>" style="<?php echo ($option4['sfsi_facebook_countsFrom'] == 'likes' || $option4['sfsi_facebook_countsFrom'] == 'followers' || $option4['sfsi_facebook_countsFrom'] == 'manual') ?  'display:none;' : ''; ?>" />
                            <div class="sfsi_fbpgidwpr sfsi_fbpgiddesc sfsi_facebook_count" style="<?php echo ($option4['sfsi_facebook_countsFrom'] == 'likes' || $option4['sfsi_facebook_countsFrom'] == 'followers' || $option4['sfsi_facebook_countsFrom'] == 'manual') ?  'display:none;' : ''; ?>">(You'll find it at the bottom of the <code>
                                    <<</code>About <code>>>
                                </code>-tab on your facebook page)</div>
                        </div>
                    </li>
                    <li>
                        <input name="sfsi_facebook_countsFrom" <?php echo ($option4['sfsi_facebook_countsFrom'] == 'manual') ?  'checked="true"' : ''; ?> type="radio" value="manual" class="styled" />Enter the figure manually:<input name="sfsi_facebook_manualCounts" type="text" class="input" value="<?php echo ($option4['sfsi_facebook_manualCounts'] != '') ?  $option4['sfsi_facebook_manualCounts'] : ''; ?>" style="<?php echo ($option4['sfsi_facebook_countsFrom'] == 'likes' || $option4['sfsi_facebook_countsFrom'] == 'followers' || $option4['sfsi_facebook_countsFrom'] == 'mypage') ?  'display:none;' : ''; ?>" />
                    </li>
                </ul>
                <div class="sfsi_facebook_pagedeasc" style="<?php echo (isset($option4['sfsi_facebook_countsFrom']) && $option4['sfsi_facebook_countsFrom'] == 'manual') ?  'display:none;' : ''; ?>">
                    <p class="sfsi_shared_premium"><b>Note:</b> This plugin uses <u>one</u> API shared by all users of this plugin. There is a limit (set by Facebook) how often this API can get the counts per day, so it may happen that it returns “0 counts” later in the day.<br><br>Therefore we implemented a solution as part of our Premium Plugin where you can <b>easily</b> set up your own API in a few steps, which will fix this problem.<br><br><a style="cursor:pointer;border-bottom: 1px solid #12a252;color: #12a252 !important" class="pop-up" data-id="sfsi_quickpay-overlay" onclick="sfsi_open_quick_checkout(event)" style="border-bottom: 1px solid #12a252;color: #12a252 !important" class="sfisi_font_bold" target="_blank">Go premium now<a href="https://www.ultimatelysocial.com/usm-premium/?utm_source=usmi_settings_page&utm_campaign=facebook_counts&utm_medium=banner" class="sfsi_font_inherit" style="color: #12a252 !important" target="_blank"> or learn more</a>
                    </p>
                </div>

            </div>
        </div>
        <!-- END FACEBOOK ICON COUNT SECTION-->

        <!-- TWITTER ICON COUNT SECTION-->
        <div class="specify_counts twitter_section">
            <div class="radio_section">
                <input name="sfsi_twitter_countsDisplay" <?php echo ($option4['sfsi_twitter_countsDisplay'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />
            </div>
            <div class="social_icon_like">
                <ul class="like_icon">
                    <li>
                        <a title="Twitter">
                            <img src="<?php echo SFSI_PLUGURL ?>images/twitter.png" alt="Twitter" />
                            <span><?php echo $counts['twitter_count']; ?></span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="listing">
                <ul>
                    <li>
                        <input name="sfsi_twitter_countsFrom" <?php echo ($option4['sfsi_twitter_countsFrom'] == 'source') ?  'checked="true"' : ''; ?> type="radio" value="source" class="styled" />Retrieve the number of Twitter followers
                    </li>

                    <li class="SFSI_tglli">
                        <ul class="SFSI_lsngfrm">
                            <li class="tw_follow_options" style="<?php echo ($option4['sfsi_twitter_countsFrom'] == 'manual') ?  'display:none;' : ''; ?>">
                                <label>Enter Consumer Key:</label>
                                <input name="tw_consumer_key" class="input_facebook" type="text" value="<?php echo (isset($option4['tw_consumer_key']) && $option4['tw_consumer_key'] != '') ?  $option4['tw_consumer_key'] : ''; ?>" />
                            </li>
                            <li class="tw_follow_options" style="<?php echo ($option4['sfsi_twitter_countsFrom'] == 'manual') ?  'display:none;' : ''; ?>">
                                <label>Enter Consumer Secret:</label>
                                <input name="tw_consumer_secret" class="input_facebook" type="text" value="<?php echo (isset($option4['tw_consumer_secret']) && $option4['tw_consumer_secret'] != '') ?  $option4['tw_consumer_secret'] : ''; ?>" />
                            </li>
                            <li class="tw_follow_options" style="<?php echo ($option4['sfsi_twitter_countsFrom'] == 'manual') ?  'display:none;' : ''; ?>">
                                <label>Enter Access Token:</label>
                                <input name="tw_oauth_access_token" class="input_facebook" type="text" value="<?php echo (isset($option4['tw_oauth_access_token']) && $option4['tw_oauth_access_token'] != '') ?  $option4['tw_oauth_access_token'] : ''; ?>" />
                            </li>
                            <li class="tw_follow_options" style="<?php echo ($option4['sfsi_twitter_countsFrom'] == 'manual') ?  'display:none;' : ''; ?>">
                                <label>Enter Access Token Secret:</label>
                                <input name="tw_oauth_access_token_secret" class="input_facebook" type="text" value="<?php echo (isset($option4['tw_oauth_access_token_secret']) && $option4['tw_oauth_access_token_secret'] != '') ?  $option4['tw_oauth_access_token_secret'] : ''; ?>" />
                            </li>
                        </ul>
                        <ul class="SFSI_instructions">
                            <li class="tw_follow_options" style="<?php echo ($option4['sfsi_twitter_countsFrom'] == 'manual') ?  'display:none;' : ''; ?>">Please make sure you have entered the <b>Username</b> for <b>"Follow me on Twitter:"</b> in twitter settings under question number 2.</li>
                            <li class="tw_follow_options" style="<?php echo ($option4['sfsi_twitter_countsFrom'] == 'manual') ?  'display:none;' : ''; ?>">
                                <!-- <h3> --><b>To get this information : </b><!-- </h3> -->
                            </li>
                            <li class="tw_follow_options" style="<?php echo ($option4['sfsi_twitter_countsFrom'] == 'manual') ?  'display:none;' : ''; ?>">1: Go to <a href="http://apps.twitter.com" target="_blank">apps.twitter.com</a></li>
                            <li class="tw_follow_options" style="<?php echo ($option4['sfsi_twitter_countsFrom'] == 'manual') ?  'display:none;' : ''; ?>">2: Click on "Create new app"</li>
                            <li class="tw_follow_options" style="<?php echo ($option4['sfsi_twitter_countsFrom'] == 'manual') ?  'display:none;' : ''; ?>">3: Enter a random Name , Descriptions and Website URL (including the "http://", e.g. http://dummysitename.com)</li>
                            <li class="tw_follow_options" style="<?php echo ($option4['sfsi_twitter_countsFrom'] == 'manual') ?  'display:none;' : ''; ?>">4: Go to "Keys and Access Tokens" tab and click on "Generate Token" in the "Token actions" section at the bottom</li>
                            <li class="tw_follow_options" style="<?php echo ($option4['sfsi_twitter_countsFrom'] == 'manual') ?  'display:none;' : ''; ?>">5: Then click on "Test OAuth" at the top right and you will see the 4 token key</li>
                        </ul>
                    </li>
                    <li>
                        <input name="sfsi_twitter_countsFrom" <?php echo ($option4['sfsi_twitter_countsFrom'] == 'manual') ?  'checked="true"' : ''; ?> type="radio" value="manual" class="styled" />Enter the figure manually:<input name="sfsi_twitter_manualCounts" type="text" class="input" value="<?php echo ($option4['sfsi_twitter_manualCounts'] != '') ?  $option4['sfsi_twitter_manualCounts'] : ''; ?>" style="<?php echo ($option4['sfsi_twitter_countsFrom'] == 'source') ?  'display:none;' : ''; ?>" />
                    </li>
                </ul>
            </div>
        </div>
        <!--END TWITTER ICON COUNT SECTION-->
        <!-- LINKEDIN ICON COUNT SECTION-->
        <div class="specify_counts linkedin_section">
            <div class="radio_section"><input name="sfsi_linkedIn_countsDisplay" <?php echo ($option4['sfsi_linkedIn_countsDisplay'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" /></div>
            <div class="social_icon_like">
                <ul class="like_icon">
                    <li>
                        <a title="Linked in">
                            <img src="<?php echo SFSI_PLUGURL ?>images/linked_in.png" alt="Linked in" />
                            <span><?php echo $counts['linkedIn_count']; ?></span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="listing">
                <ul>

                    <?php /*?><li><input name="sfsi_linkedIn_countsFrom" <?php echo ($option4['sfsi_linkedIn_countsFrom']=='follower') ?  'checked="true"' : '' ;?>  type="radio" value="follower" class="styled"  />Retrieve the number of Linkedin followers</li>
            <li class="SFSI_tglli">
                <ul class="SFSI_lsngfrm">
                    
                    <li class="linkedIn_options" style="<?php echo ($option4['sfsi_linkedIn_countsFrom']=='manual') ?  'display:none;' : '' ;?>"><label>Enter Company Name </label><input name="ln_company" class="input_facebook" type="text" value="<?php echo (isset($option4['ln_company']) && $option4['ln_company']!='') ?  $option4['ln_company'] : '' ;?>"  /> </li>
                    <li  class="linkedIn_options" style="<?php echo ($option4['sfsi_linkedIn_countsFrom']=='manual') ?  'display:none;' : '' ;?>"><label>Enter API Key </label><input name="ln_api_key" class="input_facebook" type="text" value="<?php echo (isset($option4['ln_api_key']) && $option4['ln_api_key']!='') ?  $option4['ln_api_key'] : '' ;?>"  /> </li>
                    <li  class="linkedIn_options" style="<?php echo ($option4['sfsi_linkedIn_countsFrom']=='manual') ?  'display:none;' : '' ;?>"><label>Enter Secret Key </label><input name="ln_secret_key" class="input_facebook" type="text" value="<?php echo (isset($option4['ln_secret_key']) && $option4['ln_secret_key']!='') ?  $option4['ln_secret_key'] : '' ;?>"  /> </li>
                    <li  class="linkedIn_options" style="<?php echo ($option4['sfsi_linkedIn_countsFrom']=='manual') ?  'display:none;' : '' ;?>" ><label>Enter OAuth User Token</label> <input name="ln_oAuth_user_token" class="input_facebook" type="text" value="<?php echo (isset($option4['ln_oAuth_user_token']) && $option4['ln_oAuth_user_token']!='') ?  $option4['ln_oAuth_user_token'] : '' ;?>"  /> </li>
                </ul>
                <ul class="SFSI_instructions">
                    <li class="linkedIn_options" style="<?php echo ($option4['sfsi_linkedIn_countsFrom']=='manual') ?  'display:none;' : '' ;?>"><h3>To get the API key for LinkedIn:</h3></li>
                    <li class="linkedIn_options" style="<?php echo ($option4['sfsi_linkedIn_countsFrom']=='manual') ?  'display:none;' : '' ;?>">1: Go to <a href="https://developer.linkedin.com/" target="_blank">www.developer.linkedin.com</a>, mouse over “Support” and select “API keys”</li>
                    <li class="linkedIn_options" style="<?php echo ($option4['sfsi_linkedIn_countsFrom']=='manual') ?  'display:none;' : '' ;?>">2: Then login with your Linkedin account and create a new application</li>
                    <li class="linkedIn_options" style="<?php echo ($option4['sfsi_linkedIn_countsFrom']=='manual') ?  'display:none;' : '' ;?>">3: Fill the required boxes in the form with random data, accept the Terms and add the application</li>
                    <li class="linkedIn_options" style="<?php echo ($option4['sfsi_linkedIn_countsFrom']=='manual') ?  'display:none;' : '' ;?>">4: In the next step you will see the required API information</li>
                    <li class="linkedIn_options" style="<?php echo ($option4['sfsi_linkedIn_countsFrom']=='manual') ?  'display:none;' : '' ;?>">When you enter this key into the plugin for the first time, it may take some time until the correct follower count is displayed on your website.</li>
                </ul>    
             </li><?php */ ?>


                    <li><input name="sfsi_linkedIn_countsFrom" <?php echo ($option4['sfsi_linkedIn_countsFrom'] == 'manual' || $option4['sfsi_linkedIn_countsFrom'] == 'follower') ?  'checked="true"' : ''; ?> type="radio" value="manual" class="styled" />Enter the figure manually:<input name="sfsi_linkedIn_manualCounts" type="text" class="input" value="<?php echo ($option4['sfsi_linkedIn_manualCounts'] != '') ?  $option4['sfsi_linkedIn_manualCounts'] : ''; ?>" style="<?php echo ($option4['sfsi_linkedIn_countsFrom'] == 'follower') ?  'display:none;' : ''; ?>" /></li>
                </ul>
            </div>
        </div>
        <!-- END LINKEDIN ICON COUNT SECTION-->

        <!-- YOUTUBE ICON COUNT SECTION-->
        <div class="specify_counts youtube_section">
            <div class="radio_section"><input name="sfsi_youtube_countsDisplay" <?php echo ($option4['sfsi_youtube_countsDisplay'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" /></div>
            <div class="social_icon_like">
                <ul class="like_icon">
                    <li><a title="YouTube"><img src="<?php echo SFSI_PLUGURL ?>images/youtube.png" alt="YouTube" /><span><?php echo $counts['youtube_count']; ?></span></a></li>
                </ul>
            </div>
            <div class="listing">
                <ul>
                    <li><input name="sfsi_youtube_countsFrom" type="radio" value="subscriber" <?php echo ($option4['sfsi_youtube_countsFrom'] == 'subscriber') ?  'checked="true"' : ''; ?> class="styled" />Retrieve the number of Subscribers</li>
                    <li class="youtube_options" style="<?php echo (!isset($option4['sfsi_youtube_countsFrom']) || empty($option4['sfsi_youtube_countsFrom']) || $option4['sfsi_youtube_countsFrom'] == 'manual') ?  'display:none;' : ''; ?>">
                        <div>
                            <label>Enter Youtube User name:</label>
                            <input name="sfsi_youtube_user" class="input_facebook" type="text" value="<?php echo (isset($option4['sfsi_youtube_user']) && $option4['sfsi_youtube_user'] != '') ?  $option4['sfsi_youtube_user'] : ''; ?>" />
                        </div>
                    </li>
                    <li class="youtube_options" style="<?php echo (!isset($option4['sfsi_youtube_countsFrom']) || empty($option4['sfsi_youtube_countsFrom']) || $option4['sfsi_youtube_countsFrom'] == 'manual') ?  'display:none;' : ''; ?>">
                        <div>
                            <label>Enter Youtube Channel Id:</label>
                            <input name="sfsi_youtube_channelId" class="input_facebook" type="text" value="<?php echo (isset($option4['sfsi_youtube_channelId']) && $option4['sfsi_youtube_channelId'] != '') ?  $option4['sfsi_youtube_channelId'] : ''; ?>" />
                        </div>

                    </li>
                    <li>
                        <input name="sfsi_youtube_countsFrom" type="radio" value="manual" <?php echo ($option4['sfsi_youtube_countsFrom'] == 'manual') ?  'checked="true"' : ''; ?> class="styled" />Enter the figure manually:<input name="sfsi_youtube_manualCounts" type="text" class="input" value="<?php echo ($option4['sfsi_youtube_manualCounts'] != '') ?  $option4['sfsi_youtube_manualCounts'] : ''; ?>" style="<?php echo ($option4['sfsi_youtube_countsFrom'] == 'subscriber') ?  'display:none;' : ''; ?>" />
                    </li>
                </ul>
            </div>
        </div>
        <!-- END YOUTUBE ICON COUNT SECTION-->

        <!-- PINIT ICON COUNT SECTION-->
        <div class="specify_counts pinterest_section">
            <div class="radio_section"><input name="sfsi_pinterest_countsDisplay" <?php echo ($option4['sfsi_pinterest_countsDisplay'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" /></div>
            <div class="social_icon_like">
                <ul class="like_icon">
                    <li><a title="Pinterest"><img src="<?php echo SFSI_PLUGURL ?>images/pinterest.png" alt="Pinterest" /><span><?php echo $counts['pin_count']; ?></span></a></li>
                </ul>
            </div>
            <div class="listing">
                <ul>
                    <li><input name="sfsi_pinterest_countsFrom" <?php echo ($option4['sfsi_pinterest_countsFrom'] == 'pins') ?  'checked="true"' : ''; ?> type="radio" value="pins" class="styled" />Retrieve the number of Pins</li>
                    <li><input name="sfsi_pinterest_countsFrom" <?php echo ($option4['sfsi_pinterest_countsFrom'] == 'manual') ?  'checked="true"' : ''; ?> type="radio" value="manual" class="styled" /><label class="high_prb">Enter the figure manually:</label><input name="sfsi_pinterest_manualCounts" type="text" class="input" value="<?php echo ($option4['sfsi_pinterest_manualCounts'] != '') ?  $option4['sfsi_pinterest_manualCounts'] : ''; ?>" style="<?php echo ($option4['sfsi_pinterest_countsFrom'] == 'pins') ?  'display:none;' : ''; ?>" /></li>
                </ul>
            </div>
            <div class="sfsi_new_prmium_follw" style="margin-top: 38px;">
                <p><b>New: </b>In the Premium Plugin you can also automatically show the number of PINs from your Pinterest account, or of a specific board, or the number of your Pinterest followers. <a style="cursor:pointer" class="pop-up" data-id="sfsi_quickpay-overlay" onclick="sfsi_open_quick_checkout(event)" class="sfisi_font_bold" target="_blank">Go premium now</a><a href="https://www.ultimatelysocial.com/usm-premium/?utm_source=usmi_settings_page&utm_campaign=more_pinterest_counts&utm_medium=banner" class="sfsi_font_inherit" target="_blank"> or learn more.</a></p>
            </div>
        </div>


        <!-- END PINIT ICON COUNT SECTION-->

        <!-- INSTAGRAM ICON COUNT SECTION-->
        <div class="specify_counts instagram_section">
            <div class="radio_section"><input name="sfsi_instagram_countsDisplay" <?php echo ($option4['sfsi_instagram_countsDisplay'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" /></div>
            <div class="social_icon_like">
                <ul class="like_icon">
                    <li><a title="Instagram"><img src="<?php echo SFSI_PLUGURL ?>images/instagram.png" alt="instagram" /><span><?php echo $counts['instagram_count']; ?></span></a></li>
                </ul>
            </div>
            <div class="listing">
                <ul>
                    <li>
                        <input name="sfsi_instagram_countsFrom" <?php echo ($option4['sfsi_instagram_countsFrom'] == 'followers') ?  'checked="true"' : ''; ?> type="radio" value="followers" class="styled" />Retrieve the number of Instagram followers
                    </li>
                    <li class="instagram_userLi" style="<?php echo ($option4['sfsi_instagram_countsFrom'] == 'manual') ?  'display:none;' : ''; ?>">
                        <div class="sfsi_instagramFields">
                            <div>
                                <label>Enter Instagram User name:</label>
                                <input name="sfsi_instagram_User" class="input_facebook" type="text" value="<?php echo (isset($option4['sfsi_instagram_User']) && $option4['sfsi_instagram_User'] != '') ?  $option4['sfsi_instagram_User'] : ''; ?>" />
                            </div>
                            <div>
                                <label>Enter Instagram Client Id:</label>
                                <input name="sfsi_instagram_clientid" class="input_facebook" type="text" value="<?php echo (isset($option4['sfsi_instagram_clientid']) && $option4['sfsi_instagram_clientid'] != '') ?  $option4['sfsi_instagram_clientid'] : ''; ?>" />
                            </div>
                            <div>
                                <label>Enter Instagram Redirect Url:</label>
                                <input name="sfsi_instagram_appurl" class="input_facebook" type="text" value="<?php echo (isset($option4['sfsi_instagram_appurl']) && $option4['sfsi_instagram_appurl'] != '') ?  $option4['sfsi_instagram_appurl'] : ''; ?>" />
                            </div>
                            <div class="sfsi_tokenGenerateButton">
                                <p>For generate your app token you need to enter your "Client Id" and "Redirect Url".</p>
                                <a href="javascript:">
                                    Generate Token
                                </a>
                            </div>
                            <div>
                                <label>Enter Instagram Token:</label>
                                <input name="sfsi_instagram_token" class="input_facebook" type="text" value="<?php echo (isset($option4['sfsi_instagram_token']) && $option4['sfsi_instagram_token'] != '') ?  $option4['sfsi_instagram_token'] : ''; ?>" />
                            </div>
                        </div>
                        <div class="sfsi_instagramInstruction">
                            <p>To complete the process please follow these steps:</p>
                            <ol>
                                <li>Go to <a href="https://www.instagram.com/developer" target="_blank">https://www.instagram.com/developer</a></li>
                                <li>Login and then click on “Register Your Application” to get to the “Manage Clients” section. On there click on the “Register a new client” button</li>
                                <li>Fill out the form and make sure that the “Redirect url” is valid and uncheck the “Disable Implicit oAuth” under the security tab.</li>
                                <li>Then click on “Register” button.</li>
                                <li>Copy the “Client id” and “Redirect url” you entered into the plugin. Also enter your Instagram User name. After that click on the “Generate token” button.</li>
                                <li>Authorize your app to access your account info by clicking the “Authorize” button'.</li>
                                <li>Now you will be redirected to the redirect url (which you entered during app creation) and find your access token at the end of this url(For example: http://your-website.com/#access_token=< your access token>)</li>
                                <li>Copy the access token, paste it into the plugin and click on “Save”.</li>
                            </ol>
                        </div>
                    </li>
                    <li>
                        <input name="sfsi_instagram_countsFrom" <?php echo ($option4['sfsi_instagram_countsFrom'] == 'manual') ?  'checked="true"' : ''; ?> type="radio" value="manual" class="styled" /><label class="high_prb">Enter the figure manually:</label><input name="sfsi_instagram_manualCounts" type="text" class="input" value="<?php echo ($option4['sfsi_instagram_manualCounts'] != '') ?  $option4['sfsi_instagram_manualCounts'] : ''; ?>" style="<?php echo ($option4['sfsi_instagram_countsFrom'] == 'followers') ?  'display:none;' : ''; ?>" />
                    </li>

                </ul>
            </div>
        </div>
        <!-- END INSTAGRAM ICON COUNT SECTION-->

        <!-- telegram ICON COUNT SECTION-->
        <div class="specify_counts telegram_section">
            <div class="radio_section">
                <input name="sfsi_telegram_countsDisplay" <?php echo ($option4['sfsi_telegram_countsDisplay'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />
            </div>
            <div class="social_icon_like">
                <ul class="like_icon">
                    <li>
                        <a title="telegram">
                            <img src="<?php echo SFSI_PLUGURL ?>images/icons_theme/default/default_telegram.png" height="50px" alt="telegram" />
                            <span><?php echo $counts['telegram_count']; ?></span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="listing">
                <ul>
                    <li>We cannot track this. So enter the figure here: <input name="sfsi_telegram_manualCounts" type="text" class="input" value="<?php echo ($option4['sfsi_telegram_manualCounts'] != '') ?  $option4['sfsi_telegram_manualCounts'] : ''; ?>" /></li>
                </ul>
            </div>
        </div>
        <!-- END telegram ICON COUNT SECTION-->

        <!-- vk ICON COUNT SECTION-->
        <div class="specify_counts vk_section">
            <div class="radio_section">
                <input name="sfsi_vk_countsDisplay" <?php echo ($option4['sfsi_vk_countsDisplay'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />
            </div>
            <div class="social_icon_like">
                <ul class="like_icon">
                    <li>
                        <a title="vk">
                            <img src="<?php echo SFSI_PLUGURL ?>images/icons_theme/default/default_vk.png" height="50px" alt="vk" />
                            <span><?php echo $counts['vk_count']; ?></span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="listing">
                <ul>
                    <li>We cannot track this. So enter the figure here: <input name="sfsi_vk_manualCounts" type="text" class="input" value="<?php echo ($option4['sfsi_vk_manualCounts'] != '') ?  $option4['sfsi_vk_manualCounts'] : ''; ?>" /></li>
                </ul>
            </div>
        </div>
        <!-- END vk ICON COUNT SECTION-->

        <!-- ok ICON COUNT SECTION-->
        <div class="specify_counts ok_section">
            <div class="radio_section">
                <input name="sfsi_ok_countsDisplay" <?php echo ($option4['sfsi_ok_countsDisplay'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />
            </div>
            <div class="social_icon_like">
                <ul class="like_icon">
                    <li>
                        <a title="ok">
                            <img src="<?php echo SFSI_PLUGURL ?>images/icons_theme/default/default_ok.png" height="50px" alt="ok" />
                            <span><?php echo $counts['ok_count']; ?></span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="listing">
                <ul>
                    <li>We cannot track this. So enter the figure here: <input name="sfsi_ok_manualCounts" type="text" class="input" value="<?php echo ($option4['sfsi_ok_manualCounts'] != '') ?  $option4['sfsi_ok_manualCounts'] : ''; ?>" /></li>
                </ul>
            </div>
        </div>
        <!-- END ok ICON COUNT SECTION-->

        <!-- weibo ICON COUNT SECTION-->
        <div class="specify_counts weibo_section">
            <div class="radio_section">
                <input name="sfsi_weibo_countsDisplay" <?php echo ($option4['sfsi_weibo_countsDisplay'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />
            </div>
            <div class="social_icon_like">
                <ul class="like_icon">
                    <li>
                        <a title="weibo">
                            <img src="<?php echo SFSI_PLUGURL ?>images/icons_theme/default/default_weibo.png" height="50px" alt="weibo" />
                            <span><?php echo $counts['weibo_count']; ?></span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="listing">
                <ul>
                    <li>We cannot track this. So enter the figure here: <input name="sfsi_weibo_manualCounts" type="text" class="input" value="<?php echo ($option4['sfsi_weibo_manualCounts'] != '') ?  $option4['sfsi_weibo_manualCounts'] : ''; ?>" /></li>
                </ul>
            </div>
        </div>
        <!-- END weibo ICON COUNT SECTION-->

        <!-- wechat ICON COUNT SECTION-->
        <div class="specify_counts wechat_section">
            <div class="radio_section">
                <input name="sfsi_wechat_countsDisplay" <?php echo ($option4['sfsi_wechat_countsDisplay'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />
            </div>
            <div class="social_icon_like">
                <ul class="like_icon">
                    <li>
                        <a title="wechat">
                            <img src="<?php echo SFSI_PLUGURL ?>images/icons_theme/default/default_wechat.png" height="50px" alt="wechat" />
                            <span><?php echo $counts['wechat_count']; ?></span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="listing">
                <ul>
                    <li>We cannot track this. So enter the figure here: <input name="sfsi_wechat_manualCounts" type="text" class="input" value="<?php echo ($option4['sfsi_wechat_manualCounts'] != '') ?  $option4['sfsi_wechat_manualCounts'] : ''; ?>" /></li>
                </ul>
            </div>
        </div>
        <!-- END wechat ICON COUNT SECTION-->

        <h4 style="clear: both;">For which icons do you want to show the counts?</h4>
        <!-- wechat ICON COUNT SECTION-->
        <div class="specify_counts" style="border-top: 0px solid #eaebee;padding-top: 0px;">
            <div class="radio_section">                
                <input name="sfsi_round_counts" <?php echo ($option4['sfsi_round_counts'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />
            </div>
            <div class="listing">
                <ul>
                    <li>Round icons</li>
                </ul>
            </div>
        </div>
        <div class="specify_counts" style="border-top: 0px solid #eaebee;padding-top: 0px;">
            <div class="radio_section">
                <input name="sfsi_original_counts" <?php echo ($option4['sfsi_original_counts'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />
            </div>
            <div class="listing">
                <ul>
                    <li>Original icons</li>
                </ul>
            </div>
        </div>
        <div class="specify_counts" style="border-top: 0px solid #eaebee;padding-top: 0px;">
            <div class="radio_section">
                <input name="sfsi_responsive_share_count" <?php echo ($option4['sfsi_responsive_share_count'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />
            </div>
            <div class="listing">
                <ul>
                    <li>Responsive icons</li>
                </ul>
            </div>
        </div>
        <!-- END wechat ICON COUNT SECTION-->

    </div>
    <!-- END show/hide counts for all icons section -->



    <?php sfsi_ask_for_help(4); ?>
    <!-- SAVE BUTTON SECTION   -->
    <div class="save_button">
        <img src="<?php echo SFSI_PLUGURL ?>images/ajax-loader.gif" class="loader-img" />
        <?php $nonce = wp_create_nonce("update_step4"); ?>
        <a href="javascript:;" id="sfsi_save4" title="Save" data-nonce="<?php echo $nonce; ?>">Save</a>
    </div>
    <!-- END SAVE BUTTON SECTION   -->

    <a class="sfsiColbtn closeSec" href="javascript:;">Collapse area</a>
    <label class="closeSec"></label>

    <!-- ERROR AND SUCCESS MESSAGE AREA-->
    <p class="red_txt errorMsg" style="display:none"> </p>
    <p class="green_txt sucMsg" style="display:none"> </p>
    <div class="clear"></div>
</div>
<!-- END Section 4 "Do you want to display "counts" next to your icons?"  -->