<!--Header-->
<?php
$curPageTitle = get_admin_page_title();
$wpUserData = wp_get_current_user();
$meta = B2S_Meta::getInstance();
$generalOptions = get_option('B2S_PLUGIN_GENERAL_OPTIONS');
$b2sActive = $meta->is_b2s_active();
$showYoast = (sanitize_text_field(wp_unslash($_GET['page'])) == 'blog2social-settings' && $meta->is_yoast_seo_active() && $b2sActive) ? 'block' : 'none';
$showAioseop = ($meta->is_aioseop_active() && $b2sActive) ? 'block' : 'none';
$showWebdaos = ($meta->is_webdados_active() && $b2sActive) ? 'block' : 'none';
$getPages = unserialize(B2S_PLUGIN_PAGE_TITLE);
$options = new B2S_Options(B2S_PLUGIN_BLOG_USER_ID);
$autoPostLimit = 'none';
$autoPostCon = $options->_getOption('auto_post_import_condition');
if ($autoPostCon !== false && is_array($autoPostCon) && isset($autoPostCon['count']) && isset($autoPostCon['last_call_date'])) {
    $optionUserTimeZone = $options->_getOption('user_time_zone');
    $userTimeZone = ($optionUserTimeZone !== false) ? $optionUserTimeZone : get_option('timezone_string');
    $userTimeZoneOffset = (empty($userTimeZone)) ? get_option('gmt_offset') : B2S_Util::getOffsetToUtcByTimeZone($userTimeZone);
    $current_utc_datetime = gmdate('Y-m-d H:i:s');
    $current_user_date = date('Y-m-d', strtotime(B2S_Util::getUTCForDate($current_utc_datetime, $userTimeZoneOffset)));
    $con = unserialize(B2S_PLUGIN_AUTO_POST_LIMIT);
    $autoPostLimit = ($autoPostCon['count'] == $con[B2S_PLUGIN_USER_VERSION] && $current_user_date == $autoPostCon['last_call_date']) ? 'block' : 'none';
}
$b2sLastVersion = get_option('b2s_plugin_version');
$showPrivacyPolicy = false;
$b2sPrivacyPolicy = false;
if (!B2S_System::isblockedArea('B2S_USER_POLICY', B2S_PLUGIN_ADMIN)) {
    $b2sPrivacyPolicy = get_option('B2S_PLUGIN_PRIVACY_POLICY_USER_ACCEPT_' . B2S_PLUGIN_BLOG_USER_ID);
    if ($b2sPrivacyPolicy !== false) {
        $b2sPrivacyPolicy = unserialize(base64_decode($b2sPrivacyPolicy));
        if (is_array($b2sPrivacyPolicy) && $b2sPrivacyPolicy !== false && isset($b2sPrivacyPolicy[substr(B2S_LANGUAGE, 0, 2)])) {
            $showPrivacyPolicy = true;
            $b2sPrivacyPolicy = $b2sPrivacyPolicy[substr(B2S_LANGUAGE, 0, 2)];
        }
    }
}

$optionMetricsBanner = true;
if ((defined("B2S_PLUGIN_USER_VERSION") && B2S_PLUGIN_USER_VERSION >= 3 && (!defined("B2S_PLUGIN_TRAIL_END") || (defined("B2S_PLUGIN_TRAIL_END") && strtotime(B2S_PLUGIN_TRAIL_END) < time()))) || (defined('B2S_PLUGIN_PERMISSION_INSIGHTS') && B2S_PLUGIN_PERMISSION_INSIGHTS == 1)) {
    $optionMetricsBanner = $options->_getOption('metrics_banner');
    if ($optionMetricsBanner == false) {
        $optionMetricsBanner = false;
    }
}

$hide7DayTrail = $options->_getOption('hide_7_day_trail');
$hideFinalTrailModal = $options->_getOption('hide_final_trail');
?>

<div class="b2s-support-area hidden-md hidden-lg">
    <a href="admin.php?page=blog2social-support" class="btn btn-primary btn-block"> <?php esc_html_e('Help & Support', 'blog2social'); ?></a>
</div>

<!--Info System-->
<?php if (version_compare(phpversion(), '5.5.3', '<')) { ?>
    <div class="panel panel-group">
        <div class="panel-body">
            <span class="glyphicon glyphicon-remove glyphicon-danger"></span> <?php esc_html_e("To use all features of Blog2Social, PHP version 5.5.3 or higher is required. Our support assists you as of PHP version 5.5.3. See also:", "blog2social"); ?>
            <a href="admin.php?page=blog2social-support#b2s-support-check-system"><?php esc_html_e('Blog2Social Troubleshooting-Tool', 'blog2social'); ?></a>
        </div>
    </div>
<?php } ?>
<div class="panel panel-group b2s-heartbeat-fail" style="display: none;">
    <div class="panel-body">
        <span class="glyphicon glyphicon-remove glyphicon-danger"></span> <?php esc_html_e('WordPress uses heartbeats by default, Blog2Social as well. Please enable heartbeats for using Blog2Social! See also:', 'blog2social'); ?>
        <a href="admin.php?page=blog2social-support#b2s-support-check-system"><?php esc_html_e('Blog2Social Troubleshooting-Tool', 'blog2social'); ?></a>
    </div>
</div>
<div class="panel panel-group b2s-server-connection-fail" style="display: none;">
    <div class="panel-body">
        <span class="glyphicon glyphicon-remove glyphicon-danger"></span> 
        <?php echo sprintf(__('The connection to the server failed. Please try again! You can find more information and solutions in the <a href="%s" target="_blank">guide for server connection</a>.', 'blog2social'), esc_url(B2S_Tools::getSupportLink('connection_guide'))); ?>

    </div>
</div>
<div class="panel panel-group b2s-nonce-check-fail" style="display: none;">
    <div class="panel-body">
        <span class="glyphicon glyphicon-remove glyphicon-danger"></span> <?php esc_html_e('WordPress session timeout: For security reasons, WordPress will let your session expire automatically if your site has been inactive for a while. Please reload this page to go on with your current action.', 'blog2social'); ?>
    </div>
</div>

<!--Info Mail Update -->
<div class="panel panel-group b2s-network-auth-info b2s-left-border-success b2s-mail-update-success" style="display:none;">
    <div class="panel-body">
        <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Thank you. You\'ll now receive the blog updates from Blog2Social.', 'blog2social'); ?>
    </div>
</div>

<!--Info Auto Post-->
<div class="panel panel-group b2s-auto-posting" style="display: <?php echo esc_attr($autoPostLimit); ?>;">
    <div class="panel-body">
        <span class="glyphicon glyphicon-remove glyphicon-danger"></span> <?php esc_html_e('Autoposter limit has been reached', 'blog2social') ?> <br> <?php esc_html_e('Your daily limit for posting automatically has been reached.', 'blog2social'); ?>
    </div>
</div>

<!--Info Meta Tags -->
<div class="panel panel-group b2s-clear-meta-tags b2s-clear-meta-tags-success" style="display:none;">
    <div class="panel-body">
        <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('You have deleted all meta data for posts and pages successfully.', 'blog2social'); ?>
    </div>
</div>
<div class="panel panel-group b2s-clear-meta-tags b2s-clear-meta-tags-error" style="display:none;">
    <div class="panel-body">
        <span class="glyphicon glyphicon-remove glyphicon-danger"></span> <?php esc_html_e('The page and post meta data could not be removed.', 'blog2social'); ?>
    </div>
</div>

<div class="panel panel-group b2s-meta-tags-yoast b2s-meta-tags-success" style="display:<?php echo esc_attr($showYoast); ?>;">
    <div class="panel-body">
        <span class="glyphicon glyphicon-remove glyphicon-danger"></span> <?php esc_html_e('How to use plugin settings for meta tags', 'blog2social'); ?>
        <br>
        <?php esc_html_e('Please make sure that you only use one plugin for setting meta tags so that the networks can display the link preview of your post correctly.', 'blog2social'); ?>
        <br>
        <?php echo sprintf(__('You will find a checklist for setting Open Graph tags in the <a href="%s" target="_blank">Open Graph Tag guide</a>.', 'blog2social'), esc_url(B2S_Tools::getSupportLink('yoast_warning_og_guide'))); ?>
    </div>
</div>

<div class="panel panel-group b2s-meta-tags-aioseop b2s-meta-tags-danger" style="display:<?php echo esc_attr($showAioseop); ?>;">
    <div class="panel-body">
        <span class="glyphicon glyphicon-remove glyphicon-danger"></span> <?php esc_html_e('You currently have both Blog2Social Social Meta Tags and All in One SEO Pack plugins active. To make sure that your Social Meta Tags are set correctly, please deactivate All in One Seo Social Meta settings. If they are already deactivated, you can ignore this message.', 'blog2social'); ?>
    </div>
</div>

<div class="panel panel-group b2s-meta-tags-webdados b2s-meta-tags-danger" style="display:<?php echo esc_attr($showWebdaos); ?>;">
    <div class="panel-body">
        <span class="glyphicon glyphicon-remove glyphicon-danger"></span> <?php esc_html_e('Blog2Social has detected another plugin that is setting Social Meta tags for your blog posts. To ensure that your Social Meta tags are set correctly for your social media posts shared with Blog2Social, please deactivate the Facebook Open Graph and Twitter Card Tags settings in your other plugins.', 'blog2social'); ?>
    </div>
</div>

<!--Info-Post-->
<div class="panel panel-group b2s-network-auth-info b2s-left-border-danger b2s-post-remove-fail" style="display: none;">
    <div class="panel-body">
        <span class="glyphicon glyphicon-remove glyphicon-danger"></span> <?php esc_html_e('This entry could not be removed. It\'s not yours!', 'blog2social'); ?>
    </div>
</div>
<div class="panel panel-group b2s-network-auth-info b2s-left-border-success b2s-post-remove-success" style="display:none;">
    <div class="panel-body">
        <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('This entry was removed successfully.', 'blog2social'); ?>
    </div>
</div>

<?php if (isset($_GET['origin']) && sanitize_text_field(wp_unslash($_GET['origin'])) == 'publish_post' && isset($_GET['deletePostStatus']) && isset($_GET["deletedPostsNumber"])) { ?>

    <div class="panel panel-group b2s-network-auth-info b2s-left-border-success b2s-all-posts-delete-success">
        <div class="panel-body">
            <?php
            if (sanitize_text_field(wp_unslash($_GET['deletePostStatus'])) == 'success') {

                if ((int) $_GET['deletedPostsNumber'] == 0) {
                    esc_html_e('No posts found', 'blog2social');
                } else {
                    echo sprintf(__('Deleted %s posts', 'blog2social'), (int) $_GET['deletedPostsNumber']);
                }
            } else {
                esc_html_e('Posts could not be deleted.', 'blog2social');
            }
            ?>
        </div>
    </div>


<?php } ?>
<div class="panel panel-group b2s-network-auth-info b2s-left-border-success b2s-post-edit-success" style="display:none;">
    <div class="panel-body">
        <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('This post was edited successfully.', 'blog2social'); ?>
    </div>
</div>
<?php if (isset($_GET['origin']) && sanitize_text_field(wp_unslash($_GET['origin'])) == 'save_post' && isset($_GET['postStatus'])) { ?>
    <div class="panel panel-group b2s-network-auth-info">
        <div class="panel-body">
            <span class="glyphicon glyphicon-ok glyphicon-success"></span>
            <?php
            if (sanitize_text_field(wp_unslash($_GET['postStatus'])) == 'future') {
                esc_html_e('Post was scheduled successfully on your blog!', 'blog2social');
            } else {
                esc_html_e('Post is published successfully on your blog!', 'blog2social');
            }
            ?>
        </div>
    </div>
<?php } ?>

<div class="b2s-trail-tracking" style="display: none;">
    <img height="1" width="1" style="border-style:none;" id="b2s-trail-tracking-src" alt="b2s-trail-tracking"/>
</div>
<div class="panel panel-group b2s-network-auth-info b2s-left-border-success b2s-post-draft-saved-success" style="display: none;">
    <div class="panel-body">
        <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Saved as draft', 'blog2social'); ?>
        <button class="close b2s-network-auth-info-close"><span aria-hidden="true">×</span></button>
    </div>
</div>
<div class="panel panel-group b2s-network-auth-info b2s-left-border-danger b2s-post-draft-saved-fail" style="display: none;">
    <div class="panel-body">
        <span class="glyphicon glyphicon-remove glyphicon-danger"></span> <?php esc_html_e('Could not save draft', 'blog2social'); ?>
        <button class="close b2s-network-auth-info-close"><span aria-hidden="true">×</span></button>
    </div>
</div>

<!--Info-Network-->
<div class="panel panel-group b2s-network-auth-info b2s-left-border-success b2s-network-auth-success" style="display: none">
    <div class="panel-body">
        <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Your authorization was successful.', 'blog2social'); ?>
    </div>
</div>
<div class="panel panel-group b2s-network-auth-info b2s-left-border-success b2s-network-add-mandant-success" style="display: none;">
    <div class="panel-body">
        <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Your profile was saved successful.', 'blog2social'); ?>
    </div>
</div>
<div class="panel panel-group b2s-network-auth-info b2s-left-border-danger b2s-network-add-mandant-error" style="display: none;">
    <div class="panel-body">
        <span class="glyphicon glyphicon-remove glyphicon-danger"></span> <?php esc_html_e('Your profile could not be saved.', 'blog2social'); ?>
    </div>
</div>
<div class="panel panel-group b2s-network-auth-info b2s-left-border-danger b2s-network-remove-fail" style="display: none;">
    <div class="panel-body">
        <span class="glyphicon glyphicon-remove glyphicon-danger"></span> <?php esc_html_e('Your authorization could not be removed.', 'blog2social'); ?>
    </div>
</div>
<div class="panel panel-group b2s-network-auth-info b2s-left-border-success b2s-network-remove-success" style="display:none;">
    <div class="panel-body">
        <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Your authorization has been removed successfully.', 'blog2social'); ?>
    </div>
</div>
<div class="panel panel-group b2s-network-auth-info b2s-left-border-success b2s-feedback-success" style="display:none;">
    <div class="panel-body">
        <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Thank you! Your feedback has been received.', 'blog2social'); ?>
    </div>
</div>
<div class="panel panel-group b2s-network-auth-info b2s-left-border-danger b2s-feedback-fail" style="display:none;">
    <div class="panel-body">
        <span class="glyphicon glyphicon-remove glyphicon-danger"></span> <?php esc_html_e('Your feedback could not be delivered.', 'blog2social'); ?>
    </div>
</div>

<!-- user apps -->
<div class="panel panel-group b2s-user-app-alert b2s-user-apps-generic-error" style="display: none;">
    <div class="panel-body">
        <span class="glyphicon glyphicon-remove glyphicon-danger"></span> <?php esc_html_e('Your app could not be saved. Please try again.', 'blog2social'); ?></a>
    </div>
</div>
<div class="panel panel-group b2s-user-app-alert b2s-user-apps-permission-premium" style="display: none;">
    <div class="panel-body">
        <span class="glyphicon glyphicon-remove glyphicon-danger"></span> <?php echo sprintf(__('To connect more Twitter apps with your Twitter accounts, please upgrade your current Blog2Social license or get a Twitter app add-on to your current license <a href="%s">Login with your Blog2Social account and continue to booking.</a>', esc_url(B2S_Tools::getSupportLink('addon_apps'))), 'blog2social'); ?></a>
    </div>
</div>
<div class="panel panel-group b2s-user-app-alert b2s-user-apps-permission-free" style="display: none;">
    <div class="panel-body">
        <span class="glyphicon glyphicon-remove glyphicon-danger"></span> <?php echo sprintf(__('You have no more open app slots for this network. <a href="%s">Upgrade to a premium license to purchase additional slots.</a>', esc_url(B2S_Tools::getSupportLink('affiliate'))), 'blog2social'); ?></a>
    </div>
</div>
<div class="panel panel-group b2s-user-app-alert b2s-user-apps-success" style="display: none;">
    <div class="panel-body">
        <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Your app was saved successfully.', 'blog2social'); ?></a>
    </div>
</div>
<div class="panel panel-group b2s-user-app-alert b2s-user-apps-edit-success" style="display: none;">
    <div class="panel-body">
        <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Your app was updated successfully.', 'blog2social'); ?></a>
    </div>
</div>
<div class="panel panel-group b2s-user-app-alert b2s-user-apps-delete-success" style="display: none;">
    <div class="panel-body">
        <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Your app has been removed successfully.', 'blog2social'); ?></a>
    </div>
</div>



<!-- Info-Settings-->
<div class="panel panel-group b2s-network-auth-info b2s-left-border-success b2s-settings-user-success" style="display:<?php echo (isset($_GET['b2s-settings-user-success']) ? 'block' : 'none'); ?>;">
    <div class="panel-body">
        <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Your settings were successfully saved.', 'blog2social'); ?>
    </div>
</div>
<div class="panel panel-group b2s-network-auth-info b2s-left-border-danger b2s-settings-user-error" style="display:none;">
    <div class="panel-body">
        <span class="glyphicon glyphicon-remove glyphicon-danger"></span> <?php esc_html_e('Your settings could not be saved.', 'blog2social'); ?>
    </div>
</div>
<div class="panel panel-group b2s-network-auth-info b2s-left-border-danger b2s-settings-user-error-no-auth-selected" style="display:none;">
    <div class="panel-body">
        <span class="glyphicon glyphicon-remove glyphicon-danger"></span> <?php esc_html_e('Your settings could not be saved, because you have auto-posting enabled but no social networks selected.', 'blog2social'); ?>
    </div>
</div>
<div class="panel panel-group b2s-network-auth-info b2s-left-border-success b2s-ship-settings-save" style="display: none;">
    <div class="panel-body">
        <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Your settings were successfully saved.', 'blog2social'); ?>
    </div>
</div>

<!-- Info Repost -->
<div class="panel panel-group b2s-left-border-danger b2s-re-post-no-content" style="display: none;">
    <div class="panel-body">
        <span class="glyphicon glyphicon-remove glyphicon-danger"></span> <?php esc_html_e('No posts found. Please try again with different filter options.', 'blog2social'); ?>
    </div>
</div>
<div class="panel panel-group b2s-left-border-danger b2s-re-post-content-in-queue" style="display: none;">
    <div class="panel-body">
        <span class="glyphicon glyphicon-remove glyphicon-danger"></span> <?php esc_html_e('The posts you tried to add are already in your sharing queue. If you want to re-schedule them, please delete the posts before adding them again.', 'blog2social'); ?>
    </div>
</div>
<div class="panel panel-group b2s-left-border-danger b2s-re-post-limit-error" style="display: none;">
    <div class="panel-body">
        <span class="glyphicon glyphicon-remove glyphicon-danger"></span> <?php esc_html_e('Your limit for your quota of posts in your queue has been reached. Please delete posts from your queue before you add more', 'blog2social') . ((B2S_PLUGIN_USER_VERSION < 3) ? esc_html__(' or upgade your Blog2Social license to extend your quota.', 'blog2social') : '.'); ?>
    </div>
</div>

<!--Rating-->
<?php
if (!B2S_System::isblockedArea('B2S_MENU_MODUL_RATING', B2S_PLUGIN_ADMIN)) {
    if (B2S_Rating::is_visible()) {
        ?>
        <div class="panel panel-group b2s-notice">
            <div class="panel-body">
                <h2 style="margin-top:0;font-size:20px;"><?php esc_html_e('RATE IT!', 'blog2social'); ?></h2>
                <p> <?php echo sprintf(__("Hi, we noticed you just shared your %s. blog post with Blog2Social - that's awesome! Could you please do us a favor and give it a 5-star rating on WordPress? Just to help us spread the word and boost our motivation.", 'blog2social'), B2S_Rating::count()); ?>
                </p>
                <p class="b2s-notice-buttons">
                    <a href="https://wordpress.org/support/plugin/blog2social/reviews/" class="b2s-allow-rating b2s-text-underline" target="_blank">
                        <?php esc_html_e('Ok, you deserve it', 'blog2social'); ?>
                    </a>
                    <a href="#" class="b2s-hide-rating b2s-text-underline" target="_blank">
                        <?php esc_html_e('Nope, maybe later', 'blog2social'); ?>
                    </a>
                    <a href="#" class="b2s-hide-rating-forever b2s-text-underline" target="_blank">
                        <?php esc_html_e('I already did it', 'blog2social'); ?>
                    </a>
                </p>
            </div>
        </div>
    <?php } ?>
<?php } ?>

<?php if (!B2S_System::isblockedArea('', B2S_PLUGIN_ADMIN, true)) { ?>
    <!--Info-Trail-->
    <?php
    if (B2S_PLUGIN_USER_VERSION == 0 && !defined("B2S_PLUGIN_TRAIL_END") && !get_option('B2S_PLUGIN_DISABLE_TRAIL') && !get_option('B2S_HIDE_PREMIUM_MESSAGE') && (isset($_GET['page']) && in_array($_GET['page'], array("blog2social", "blog2social-post", "blog2social-sched", "blog2social-publish", "blog2social-calendar")))) {
        $optionsOnboarding = new B2S_Options(B2S_PLUGIN_BLOG_USER_ID, "B2S_PLUGIN_ONBOARDING");
        $onboardingval = $optionsOnboarding->_getOption('onboarding_active');
        if ($onboardingval != 1) {
            ?>
            <div class="panel panel-group b2s-trail-premium-info-area b2s-notice">
                <div class="panel-body">
                    <div class="b2s-hide-premium-message b2s-close"><i class="glyphicon glyphicon-remove"></i></div>
                    <h2 style="margin-top:0;font-size:20px;"><?php esc_html_e('Start your free 30-day-Premium-trial', 'blog2social'); ?></h2>
                    <p>
                        <?php esc_html_e('Check out Blog2Social Premium with more awesome features for scheduling and sharing (e.g. auto-posting, best time scheduling, social media calendar) 30-days for free. The trial is free of charge, without any obligations, no automatic subscription. Basic features of the Free Version are free forever.', 'blog2social'); ?>
                    </p>
                    <p class="b2s-notice-buttons">
                        <a href="<?php echo esc_url(B2S_Tools::getSupportLink('feature')); ?>" target="_blank" class="b2s-text-underline">
                            <?php esc_html_e('Yes, I want to test Blog2Social Premium 30 days for free', 'blog2social'); ?>
                        </a>
                    </p>
                </div>
            </div>
            <?php
        }
    }
    ?>

    <?php if (defined("B2S_PLUGIN_TRAIL_END") && strtotime(B2S_PLUGIN_TRAIL_END) > strtotime(gmdate('Y-m-d H:i:s')) && !get_option('B2S_HIDE_TRAIL_MESSAGE') && (isset($_GET['page']) && in_array($_GET['page'], array("blog2social", "blog2social-post", "blog2social-sched", "blog2social-publish", "blog2social-calendar")))) { ?>
        <div class="panel panel-group b2s-trail-premium-info-area b2s-notice">
            <div class="panel-body">
                <div class="b2s-hide-trail-message b2s-close"><i class="glyphicon glyphicon-remove"></i></div>
                <h2 style="margin-top:0;font-size:20px;">
                    <?php esc_html_e('Your free Blog2Social Premium trial version is activated for ', 'blog2social'); ?>
                    <?php
                    $days = B2S_Util::getTrialRemainingDays(B2S_PLUGIN_TRAIL_END, date_default_timezone_get());
                    echo $days > 0 ? ("<span style='color:#79B232'>" . esc_html($days) . "</span>" . esc_html__(' Days', 'blog2social')) : "<span style='color:#f33'>" . esc_html__(' today', 'blog2social') . "</span>";
                    ?>
                </h2>
                <p>
                    <?php esc_html_e('Blog2Social PREMIUM can do so much for you: Auto-publish your blog post on autopilot, automatically schedule your social media posts with the Best Time Manager. Select images and post formats (link post or image post) for each social community. Upload and select any image for sharing. Save multiple combinations of networks for different sharing purposes. Start from only $6.58 per month to benefit from PREMIUM features.', 'blog2social'); ?>
                </p>
                <p class="b2s-notice-buttons">
                    <a target="_blank" class="b2s-text-underline" href="<?php echo esc_url(B2S_Tools::getSupportLink('affiliate')); ?>"><?php esc_html_e('Upgrade to PREMIUM', 'blog2social'); ?></a>
                    <a href="#" class="b2s-hide-trail-message b2s-text-underline"><?php esc_html_e('I need some more time to decide', 'blog2social'); ?></a>
                </p>
            </div>
        </div>
    <?php } ?>

    <?php if (defined("B2S_PLUGIN_TRAIL_END") && strtotime(B2S_PLUGIN_TRAIL_END) < strtotime(gmdate('Y-m-d H:i:s')) && !get_option('B2S_HIDE_TRAIL_ENDED') && (isset($_GET['page']) && in_array($_GET['page'], array("blog2social", "blog2social-post", "blog2social-sched", "blog2social-publish", "blog2social-calendar")))) { ?>
        <div class="panel panel-group b2s-trail-premium-info-area b2s-notice">
            <div class="panel-body">
                <div class="b2s-hide-trail-ended-modal b2s-close"><i class="glyphicon glyphicon-remove"></i></div>
                <h2 style="margin-top:0;font-size:20px;">
                    <?php esc_html_e('Your free trial of Blog2Social PREMIUM has ended.', 'blog2social'); ?>
                    <?php esc_html_e('We hope you liked Blog2Social Premium.', 'blog2social'); ?>
                </h2>
                <p>
                    <?php esc_html_e('Blog2Social PREMIUM can do so much for you: Auto-publish your blog post on autopilot, automatically schedule your social media posts with the Best Time Manager. Select images and post formats (link post or image post) for each social community. Upload and select any image for sharing. Save multiple combinations of networks for different sharing purposes. Start from only $6.58 per month to benefit from PREMIUM features.', 'blog2social'); ?>
                </p>
                <p class="b2s-notice-buttons">
                    <a target="_blank" class="b2s-text-underline" href="<?php echo esc_url(B2S_Tools::getSupportLink('affiliate')); ?>"><?php esc_html_e('Yes, I want to upgrade to Blog2Social Premium', 'blog2social'); ?></a>
                    <a href="#" class="b2s-text-underline b2s-hide-trail-ended-modal"><?php esc_html_e('I need some more time to decide', 'blog2social'); ?></a>
                    <a href="#" class="b2s-text-underline b2s-show-feedback-modal"><?php esc_html_e('Did you miss something? Tell us!', 'blog2social'); ?></a>
                </p>
            </div>
        </div>
    <?php } ?>
<?php } ?>

<?php if (defined("B2S_PLUGIN_TRAIL_END") && strtotime(B2S_PLUGIN_TRAIL_END) <= strtotime(gmdate('Y-m-d H:i:s')) && $hide7DayTrail) { ?>
    <div class="panel panel-group b2s-notice">
        <div class="panel-body">
            <h2 style="margin-top:0;font-size:20px;"><?php esc_html_e('Your free Premium trial ends soon. ', 'blog2social'); ?></h2>
            <p> <?php esc_html_e("Keep your current settings and access to more automated scheduling and sharing options and upgrade to Blog2Social Premium.", 'blog2social'); ?>
            </p>
            <p class="b2s-notice-buttons">
                <a href="<?php echo esc_url(B2S_Tools::getSupportLink('b2s_premium_upgrade')); ?>" class="b2s-allow-rating b2s-text-underline" target="_blank">
                    <?php esc_html_e('Upgrade to Blog2Social Premium now.', 'blog2social'); ?>
                </a>
                <a href="<?php echo esc_url(B2S_Tools::getSupportLink('b2s_license_advice')); ?>" class="b2s-hide-rating b2s-text-underline" target="_blank">
                    <?php esc_html_e('I need advice on the right license.', 'blog2social'); ?>
                </a>
            </p>
        </div>
    </div>
<?php } ?>
<!--Header-->


<?php
if (isset($_GET['page']) && !empty($_GET['page']) && !in_array($_GET['page'], unserialize(B2S_PLUGIN_REMOVE_PAGE_TITLE))) {
    ?> <h1>
        <?php
        if ((isset($getPages[$_GET['page']]) && !empty($getPages[$_GET['page']]))) {
            echo wp_kses($getPages[sanitize_text_field(wp_unslash($_GET['page']))], array('span' => array('class' => array()), 'a' => array('href' => array(), 'target' => array(), 'class' => array()), 'button' => array('class' => array())));
        } else if (!empty($curPageTitle)) {
            echo esc_html($curPageTitle);
        }
        ?>
    </h1>
    <?php
}
?>

<!-- B2S-Key-Info-->
<div class="modal fade" id="b2sInfoKeyModal" tabindex="-1" role="dialog" aria-labelledby="b2sInfoKeyModal" aria-hidden="true" data-backdrop="false" style="display:none;">
    <div class="modal-dialog b2s-modal-info-key-area">
        <div class="modal-content">
            <div class="modal-body">
                <!--Info-Key-->
                <div class="b2s-key-area-success" style="display: none;">
                    <div class="col-md-12 text-center">
                        <div class="row">
                            <div class="col-md-12">
                                <span class="glyphicon glyphicon-ok b2s-glyphicon-xl glyphicon-success"></span>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="row">
                            <div class="col-md-12">
                                <?php esc_html_e('The license has been successfully activated.', 'blog2social'); ?>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <div class="b2s-key-area-fail" style="display: none;">
                    <div class="col-md-12 text-center">
                        <div class="row">
                            <div class="col-md-12">
                                <span class="glyphicon glyphicon-info-sign b2s-glyphicon-xl glyphicon-danger"></span>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="row">
                            <div class="col-md-12">
                                <?php esc_html_e('Your entered License Key is invalid. Please contact support!', 'blog2social'); ?>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <div class="b2s-key-area-fail-max-use" style="display: none;">
                    <div class="col-md-12 text-center">
                        <div class="row">
                            <div class="col-md-12">
                                <span class="glyphicon glyphicon-info-sign b2s-glyphicon-xl glyphicon-danger"></span>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="row">
                            <div class="col-md-12">
                                <?php esc_html_e('Your license key has reached the maximum number of users.', 'blog2social'); ?>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <div class="b2s-key-area-fail-no-token" style="display: none;">
                    <div class="col-md-12 text-center">
                        <div class="row">
                            <div class="col-md-12">
                                <span class="glyphicon glyphicon-info-sign b2s-glyphicon-xl glyphicon-danger"></span>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="row">
                            <div class="col-md-12">
                                <?php esc_html_e('Something went wrong on our side. Please contact support!', 'blog2social'); ?>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <br>
                <div class="text-center">
                    <button type="button" class="b2s-modal-close btn btn-primary" data-modal-name="#b2sInfoKeyModal" aria-label="Close"><?php esc_html_e("OK", "blog2social"); ?></button>
                </div>
            </div>
        </div>
    </div>
</div>


<input id="b2sUserAcceptPrivacyPolicy" type="hidden" value="<?php echo (($showPrivacyPolicy) ? 'true' : 'false'); ?>">
<!-- B2S-Privacy-Policy-Info-->
<div class="modal fade" id="b2sModalPrivacyPolicy" tabindex="-1" role="dialog" aria-labelledby="b2sModalPrivacyPolicy" aria-hidden="true" data-backdrop="false" style="display:none;">
    <div class="modal-dialog b2s-scroll-modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"> <img src="<?php echo esc_url(plugins_url('/assets/images/b2s@32.png', B2S_PLUGIN_FILE)); ?>" alt="blog2social"> <?php esc_html_e('We updated our Privacy Policy', 'blog2social') ?></h4>
            </div>
            <div class="modal-body b2s-scroll-modal-body b2s-modal-privacy-policy-scroll-content">
                <p>
                    <?php
                    if ($b2sPrivacyPolicy !== false) {
                        echo mb_convert_encoding($b2sPrivacyPolicy, 'UTF-8');
                    }
                    ?> 
                </p>
            </div>
            <div class="modal-footer">
                <a href="#" class="b2s-scroll-modal-down" address="true"></a>
                <br>
                <div class="pull-left btn-padding"><?php esc_html_e('Blog2Social is a service of Adenion GmbH', 'blog2social'); ?></div>
                <button class="btn btn-success b2s-modal-privacy-policy-accept-btn"><?php esc_html_e('I agree to the Adenion Privacy Policy', 'blog2social'); ?></button>
                <section class="b2s-scroll-modal-end"></section>
            </div>
        </div>
    </div>
</div>

<input type="hidden" id="b2s-metrics-banner-show" value="<?php echo (($optionMetricsBanner) ? 1 : 0); ?>">
<div class="modal fade" id="b2s-metrics-banner-modal" tabindex="-1" role="dialog" aria-labelledby="b2s-metrics-banner-modal" aria-hidden="true" data-backdrop="false" style="display:none; z-index: 1070;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body text-center" style="background-color: #f4f4f4;">
                <button type="button" class="close b2s-metrics-banner-close" data-dismiss="modal">&times;</button>
                <img src="<?php echo esc_url(plugins_url('/assets/images/metrics/social-symbols.png', B2S_PLUGIN_FILE)); ?>" style="width: 80px; float: right; margin-left: -65px;" alt="blog2social">
                <br>
                <h3><?php esc_html_e('Social Media Metrics', 'blog2social') . ' <span class="label label-success label-sm">' . esc_html__("BETA", "blog2social") . '</span>' ?></h3>
                <br>
                <?php esc_html_e('You can now track the performance of your post directly in Blog2Social, and you can test it exclusively and for free!', 'blog2social'); ?>
                <br>
                <br>
                <?php esc_html_e('Benefit from the new Social Media Metrics and use the analysis of your social media posts for your further social media strategy.', 'blog2social'); ?>
                <br>
                <br>
                <img src="<?php echo esc_url(plugins_url('/assets/images/metrics/banner.png', B2S_PLUGIN_FILE)); ?>" alt="blog2social">
                <br>
                <br>
                <a href="admin.php?page=blog2social-metrics&metrics_banner=1" class="btn btn-lg btn-success"><?php esc_html_e('Start your free trial for Social Media Metrics', 'blog2social'); ?></a>
                <br>
                <br>
                <span class="b2s-bold"><?php esc_html_e('We hope you enjoy analysing your posts!', 'blog2social'); ?></span>
            </div>
        </div>
    </div>
</div>

<?php if (defined("B2S_PLUGIN_TRAIL_END") && strtotime(date('Y-m-d H:i:s', (strtotime('-7 day', strtotime(B2S_PLUGIN_TRAIL_END))))) < strtotime(gmdate('Y-m-d H:i:s')) && strtotime(B2S_PLUGIN_TRAIL_END) >= strtotime(gmdate('Y-m-d H:i:s')) && !$hide7DayTrail) { ?>
    <?php
    $now = time();
    $your_date = strtotime(B2S_PLUGIN_TRAIL_END);
    $datediff = $your_date - $now;
    $trial_days = round($datediff / (60 * 60 * 24));
    ?>
    <div class="modal fade" id="b2s-trial-seven-day-modal" tabindex="-1" role="dialog" aria-labelledby="b2s-trial-seven-day-modal" aria-hidden="true" data-backdrop="false" style="display:none; z-index: 1070;">
        <div class="modal-dialog">
            <div class="modal-content modal-lg">
                <div class="modal-body" style="background-color: #f4f4f4;">
                    <button type="button" class="close b2s-trial-seven-day-modal-close" data-dismiss="modal">&times;</button>
                    <br>
                    <div class="row b2s-d-flex">
                        <div class="col-md-6 col-sm-12">
                            <h2 class="b2s-bold"><?php echo sprintf(__('Your test licence expires in %d days.', 'blog2social'), esc_html($trial_days)); ?></h2>
                            <span><?php esc_html_e('While you\'re enjoying the trial, there is so much more that Blog2Social has to offer. Please upgrade to keep access to your premium features and benefit from:', 'blog2social'); ?></span>
                            <br>
                            <ul class="b2s-header-trial-modal-list">
                                <li class="b2s-trial-modal-list"><?php esc_html_e('Advanced scheduling capabilities for optimal posting times across multiple channels', 'blog2social'); ?></li>
                                <li class="b2s-trial-modal-list"><?php esc_html_e('Automated posting and resharing to save time and maintain a consistent content stream', 'blog2social'); ?></li>
                                <li class="b2s-trial-modal-list"><?php esc_html_e('Customizable templates for optimized social media presentation', 'blog2social'); ?></li>
                                <li class="b2s-trial-modal-list"><?php esc_html_e('Flexible user and account settings and add-ons for efficient social media management', 'blog2social'); ?></li>
                            </ul>
                            <span><?php esc_html_e('These exclusive features can help you reach more people in less time. Unlock the full power of Blog2Social for your social media success.', 'blog2social'); ?></span>
                            <br>
                            <br>
                            <span><?php esc_html_e('Find the perfect plan to suit your needs.', 'blog2social'); ?></span>
                            <br>
                            <br>
                            <a href="<?php echo esc_url(B2S_Tools::getSupportLink('b2s_premium_upgrade')); ?>" class="btn b2s-btn-success-new" target="_blank"><?php esc_html_e('Upgrade Blog2Social', 'blog2social'); ?></a>
                            <br>
                            <button type="button" class="btn btn-link b2s-continue-trial-btn"><small><?php esc_html_e('permanently hide', 'blog2social'); ?></small></button>
                            <br>
                        </div>
                        <div class="col-md-6 col-sm-12 b2s-p-0 hidden-xs hidden-sm b2s-header-trial-image-container">
                            <img class="b2s-header-trial-image" src="<?php echo esc_url(plugins_url('/assets/images/b2s/trial.png', B2S_PLUGIN_FILE)); ?>" alt="blog2social">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>

    <?php if (defined("B2S_PLUGIN_TRAIL_END") && strtotime(B2S_PLUGIN_TRAIL_END) < strtotime(gmdate('Y-m-d H:i:s')) && defined('B2S_PLUGIN_USER_VERSION') && B2S_PLUGIN_USER_VERSION == 0 && !$hideFinalTrailModal) { ?>
        <div class="modal fade" id="b2s-final-trail-modal" tabindex="-1" role="dialog" aria-labelledby="b2s-final-trail-modal" aria-hidden="true" data-backdrop="false" style="display:none; z-index: 1070;">
            <div class="modal-dialog">
                <div class="modal-content modal-lg">
                    <div class="modal-body" style="background-color: #f4f4f4;">
                        <button type="button" class="close b2s-final-trail-modal-close" data-dismiss="modal">&times;</button>
                        <br>
                        <div class="row b2s-d-flex">
                            <div class="col-md-6 col-sm-12">
                                <h2 class="b2s-bold"><?php esc_html_e('Your test licence has expired.', 'blog2social'); ?></h2>
                                <span><?php esc_html_e('While you enjoyed the trial, there is so much more that Blog2Social has to offer. Please upgrade now to regain access to your premium features and benefit from:', 'blog2social'); ?></span>
                                <br>
                                <ul class="b2s-header-trial-modal-list">
                                    <li class="b2s-trial-modal-list"><?php esc_html_e('Advanced scheduling capabilities for optimal posting times across multiple channels', 'blog2social'); ?></li>
                                    <li class="b2s-trial-modal-list"><?php esc_html_e('Automated posting and resharing to save time and maintain a consistent content stream', 'blog2social'); ?></li>
                                    <li class="b2s-trial-modal-list"><?php esc_html_e('Customizable templates for optimized social media presentation', 'blog2social'); ?></li>
                                    <li class="b2s-trial-modal-list"><?php esc_html_e('Flexible user and account settings and add-ons for efficient social media management', 'blog2social'); ?></li>
                                </ul>
                                <span><?php esc_html_e('These exclusive features can help you reach more people in less time. Unlock the full power of Blog2Social for your social media success.', 'blog2social'); ?></span>
                                <br>
                                <br>
                                <span><?php esc_html_e('Find the perfect plan to suit your needs.', 'blog2social'); ?></span>
                                <br>
                                <br>
                                <a href="<?php echo esc_url(B2S_Tools::getSupportLink('b2s_premium_upgrade')); ?>" class="btn b2s-btn-success-new" target="_blank"><?php esc_html_e('Upgrade Blog2Social', 'blog2social'); ?></a>
                                <br>
                                <button type="button" class="btn btn-link b2s-hide-final-trial-btn"><small><?php esc_html_e('permanently hide', 'blog2social'); ?></small></button>
                                <br>
                            </div>
                            <div class="col-md-6 col-sm-12 b2s-p-0 hidden-xs hidden-sm b2s-header-trial-image-container">
                                <img class="b2s-header-trial-image" src="<?php echo esc_url(plugins_url('/assets/images/b2s/trial.png', B2S_PLUGIN_FILE)); ?>" alt="blog2social">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>

        <div class="modal fade b2sAssAuthModal" class="b2sAssAuthModal" tabindex="-1" role="dialog" aria-labelledby="b2sAssAuthModal" aria-hidden="true" data-backdrop="false"  style="display:none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header b2s-modal-border-none">
                        <img class="pull-left b2s-ass-img-logo" src="<?php echo esc_url(plugins_url('/assets/images/ass/assistini-logo.png', B2S_PLUGIN_FILE)); ?>" alt="Assistini"> 
                        <button type="button" class="b2s-modal-close close b2s-padding-15" data-modal-name=".b2sAssAuthModal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="b2s-stepwizard">
                                <div class="b2s-stepwizard-row setup-panel">
                                    <div class="b2s-stepwizard-step b2s-ass-stepwizard-step">
                                        <a href="#step-1" type="button" class="btn btn-danger b2s-ass-color b2s-stepwizard-btn-circle" disabled="disabled">1</a>
                                        <p><?php esc_html_e('Enter email address', 'blog2social') ?></p>
                                    </div>
                                    <div class="b2s-stepwizard-step b2s-ass-stepwizard-step">
                                        <a href="#step-2" type="button" class="btn btn-default b2s-stepwizard-btn-circle" disabled="disabled">2</a>
                                        <p><?php esc_html_e('Verify email address', 'blog2social') ?></p>
                                    </div>
                                    <div class="b2s-stepwizard-step">
                                        <a href="#step-3" type="button" class="btn btn-default b2s-stepwizard-btn-circle" disabled="disabled">3</a>
                                        <p><?php esc_html_e('Ready to go!', 'blog2social') ?></p>
                                    </div>
                                </div>
                            </div>    
                        </div>
                        <div class="row">
                            <div class="b2s-ass-auth-step-1-content">
                                <h2><?php esc_html_e('Connect AI assistant', 'blog2social') ?></h2>
                                <p><?php esc_html_e('Connect with the AI assistant to get started.', 'blog2social') ?></p>
                                <br>
                                <p><b><?php esc_html_e('Use this email for verification', 'blog2social') ?></b></p>
                                <div>
                                    <input type="radio" value="0" class="b2s-ass-auth-email-option" id="b2s-ass-auth-email-own" data-auth-email="<?php echo $this->blogUserData->user_email; ?>" name="b2s-ass-auth-email" checked />
                                    <label for="b2s-ass-auth-email-own"> <?php echo $this->blogUserData->user_email; ?></label>
                                    <br>
                                    <input type="radio" value="1" class="b2s-ass-auth-email-option" id="b2s-ass-auth-email-other" name="b2s-ass-auth-email">
                                    <label for="b2s-ass-auth-email-other"> <?php esc_html_e('Use different email for verification', 'blog2social') ?></label>
                                </div>
                                <div class="pull-right"> 
                                    <button id="b2s-ass-auth-step1-btn" data-url="<?php echo B2S_PLUGIN_SERVER_URL . '/auth/assistini.php?b2s_token=' . B2S_PLUGIN_TOKEN . '&sprache=' . substr(B2S_LANGUAGE, 0, 2) ?>" data-auth-title="<?php echo esc_attr('Assistini Authorization', 'blog2social'); ?>" class="btn b2s-ass-btn"><?php esc_html_e('Send verification code', 'blog2social') ?></button>
                                </div>
                            </div>
                            <div class="b2s-ass-auth-step-3-content" style="display:none;">
                                <h2><?php esc_html_e('Connection established', 'blog2social') ?></h2>
                                <p><?php esc_html_e('Congrats! You can now start using Assistini AI to optimize your social media posts.', 'blog2social') ?></p>
                                <br>
                                <div class="pull-right"> 
                                    <button id="b2s-ass-auth-step3-btn" class="btn b2s-ass-btn"><?php esc_html_e('Start now', 'blog2social') ?></button>
                                </div>
                            </div>
                        </div>                

                    </div>
                </div>

            </div>
        </div>

<div class="modal fade" id="b2sAssSettingsModal" class="b2sAssSettingsModal" tabindex="-1" role="dialog" aria-labelledby="b2sAssSettingsModal" aria-hidden="true" data-backdrop="false" style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header b2s-modal-border-none">
                <img class="pull-left b2s-ass-img-logo" src="<?php echo esc_url(plugins_url('/assets/images/ass/assistini-logo.png', B2S_PLUGIN_FILE)); ?>" alt="Assistini"> 
                <button type="button" class="b2s-modal-close close b2s-padding-15" data-modal-name="#b2sAssSettingsModal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body b2s-pt-0">
                <div class="b2s-d-flex b2s-pb-2">
                    <img class="b2s-pr-1 b2s-ass-img-settings" src="<?php echo esc_url(plugins_url('/assets/images/ass/pr-automatisierung.png', B2S_PLUGIN_FILE)); ?>" alt="Assistini Settings">
                    <div>
                        <h2><?php esc_html_e('AI Settings', 'blog2social') ?></h2>
                        <p><?php esc_html_e('Set up your AI assistant.', 'blog2social') ?></p>
                        <div class="b2s-pt-1">
                            <input type="checkbox" id="b2s-ass-settings-checkbox-1" />
                            <label for="b2s-ass-settings-checkbox-1"><?php esc_html_e('Apply post templates', 'blog2social') ?></label>
                            <br>
                            <input type="checkbox" id="b2s-ass-settings-checkbox-2" />
                            <label for="b2s-ass-settings-checkbox-2"><?php esc_html_e('Exclude emojis', 'blog2social') ?></label>
                            <br>
                            <input type="checkbox" id="b2s-ass-settings-checkbox-3" />
                            <label for="b2s-ass-settings-checkbox-3"><?php esc_html_e('Generate Hashtags', 'blog2social') ?></label>
                        </div>
                    </div>
                </div>
                <div class="b2s-d-flex-end"> 
                    <button id="b2s-ass-settings-save-btn" class="btn b2s-ass-btn"><?php esc_html_e('Save', 'blog2social') ?></button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="b2sAssLimitModal" class="b2sAssLimitModal" tabindex="-1" role="dialog" aria-labelledby="b2sAssLimitModal" aria-hidden="true" data-backdrop="false" style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header b2s-modal-border-none">
                <img class="pull-left b2s-ass-img-logo" src="<?php echo esc_url(plugins_url('/assets/images/ass/assistini-logo.png', B2S_PLUGIN_FILE)); ?>" alt="Assistini"> 
                <button type="button" class="b2s-modal-close close b2s-padding-15" data-modal-name="#b2sAssLimitModal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body b2s-pt-0">
                <div class="b2s-d-flex text-center b2s-pb-1">
                    <h2 class="b2s-text-bold"><?php esc_html_e('You reached the maximum of generated words for your plan.', 'blog2social') ?></h2>
                </div>
                <div class="text-center b2s-pb-1"> 
                    <a href="https://app.assistini.com/?screen=Plan" target="_blank" id="b2s-ass-limit-upgrade-btn" class="btn b2s-ass-btn b2s-w-80"><?php esc_html_e('Upgrade your plan', 'blog2social') ?></a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="b2sAssNoContentModal" class="b2sAssNoContentModal" tabindex="-1" role="dialog" aria-labelledby="b2sAssNoContentModal" aria-hidden="true" data-backdrop="false" style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header b2s-modal-border-none">
                <img class="pull-left b2s-ass-img-logo" src="<?php echo esc_url(plugins_url('/assets/images/ass/assistini-logo.png', B2S_PLUGIN_FILE)); ?>" alt="Assistini"> 
                <button type="button" class="b2s-modal-close close b2s-padding-15" data-modal-name="#b2sAssNoContentModal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body b2s-pt-0">
                <div class="b2s-d-flex b2s-justify-content-center b2s-pb-1">
                    <h2 class="b2s-text-bold"><?php esc_html_e('The content of your Wordpress post is empty!', 'blog2social') ?></h2>
                </div>
                <div class="b2s-d-flex text-center b2s-pb-1">
                    <h4 class="alert alert-warning"><?php esc_html_e('Enter a content of your Wordpress post. Alternatively, you can enter content directly into the  input field. Assistini AI will then create suggestions for your social media post.', 'blog2social') ?></h4>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="b2sAssNoContentVideoModal" class="b2sAssNoContentVideoModal" tabindex="-1" role="dialog" aria-labelledby="b2sAssNoContentVideoModal" aria-hidden="true" data-backdrop="false" style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header b2s-modal-border-none">
                <img class="pull-left b2s-ass-img-logo" src="<?php echo esc_url(plugins_url('/assets/images/ass/assistini-logo.png', B2S_PLUGIN_FILE)); ?>" alt="Assistini"> 
                <button type="button" class="b2s-modal-close close b2s-padding-15" data-modal-name="#b2sAssNoContentVideoModal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body b2s-pt-0">
                <div class="b2s-d-flex b2s-justify-content-center b2s-pb-1">
                    <h2 class="b2s-text-bold"><?php esc_html_e('Your video description is empty!', 'blog2social') ?></h2>
                </div>
                <div class="b2s-d-flex text-center b2s-pb-1">
                    <h4 class="alert alert-warning"><?php esc_html_e('Enter a description for your video in the Media Library. Alternatively, you can enter content directly into the  input field. Assistini AI will then create suggestions for your social media post.', 'blog2social') ?></h4>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="b2sAssDefaultErrorModal" class="b2sAssDefaultErrorModal" tabindex="-1" role="dialog" aria-labelledby="b2sAssDefaultErrorModal" aria-hidden="true" data-backdrop="false" style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header b2s-modal-border-none">
                <img class="pull-left b2s-ass-img-logo" src="<?php echo esc_url(plugins_url('/assets/images/ass/assistini-logo.png', B2S_PLUGIN_FILE)); ?>" alt="Assistini"> 
                <button type="button" class="b2s-modal-close close b2s-padding-15" data-modal-name="#b2sAssDefaultErrorModal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body b2s-pt-0">
                <div class="b2s-d-flex text-center b2s-pb-1">
                    <h2 class="b2s-text-bold"><?php esc_html_e('An error has occurred. Please re-authorize and try again.', 'blog2social') ?></h2>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="b2sAssNoTextErrorModal" tabindex="-1" role="dialog" aria-labelledby="b2sAssNoTextErrorModal" aria-hidden="true" data-backdrop="false" style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header b2s-modal-border-none b2s-pb-0">
                <button type="button" class="b2s-modal-close close b2s-padding-15 b2s-pb-0" data-modal-name="#b2sAssNoTextErrorModal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body b2s-pt-0">
                <div class="b2s-d-flex text-center b2s-pb-1">
                    <h2 class="b2s-text-bold"><?php esc_html_e('Please enter a text that you want to re-generate.', 'blog2social') ?></h2>
                </div>
            </div>
        </div>
    </div>
</div>