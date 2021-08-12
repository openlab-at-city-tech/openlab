<!--Header-->
<?php
$curPageTitle = get_admin_page_title();
$wpUserData = wp_get_current_user();
$meta = B2S_Meta::getInstance();
$generalOptions = get_option('B2S_PLUGIN_GENERAL_OPTIONS');
$b2sActive = $meta->is_b2s_active();
$showYoast = ($_GET['page'] == 'blog2social-settings' && $meta->is_yoast_seo_active() && $b2sActive) ? 'block' : 'none';
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
?>
<h1><?php echo (!empty($curPageTitle) ? $curPageTitle : ((isset($getPages[$_GET['page']]) && !empty($getPages[$_GET['page']])) ? $getPages[$_GET['page']] : '' )); ?></h1> 

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
        <span class="glyphicon glyphicon-remove glyphicon-danger"></span> <?php esc_html_e('The connection to the server failed. Try again!', 'blog2social'); ?>
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
<div class="panel panel-group b2s-auto-posting" style="display: <?php echo $autoPostLimit; ?>;">
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

<div class="panel panel-group b2s-meta-tags-yoast b2s-meta-tags-success" style="display:<?php echo $showYoast; ?>;">
    <div class="panel-body">
        <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('You have both Yoast SEO and Blog2Social Meta Tags active. Please make sure that you use only one plugin to set social meta tags so that the networks can show the link preview of your post correctly.', 'blog2social'); ?><br><?php echo sprintf(__('Get more information in the <a href="%s" target="_blank">social meta tag guide</a>.', 'blog2social'), B2S_Tools::getSupportLink('open_graph_tags')) ?>
    </div>
</div>

<div class="panel panel-group b2s-meta-tags-aioseop b2s-meta-tags-danger" style="display:<?php echo $showAioseop; ?>;">
    <div class="panel-body">
        <span class="glyphicon glyphicon-remove glyphicon-danger"></span> <?php esc_html_e('You currently have both Blog2Social Social Meta Tags and All in One SEO Pack plugins active. To make sure that your Social Meta Tags are set correctly, please deactivate All in One Seo Social Meta settings. If they are already deactivated, you can ignore this message.', 'blog2social'); ?>
    </div>
</div>

<div class="panel panel-group b2s-meta-tags-webdados b2s-meta-tags-danger" style="display:<?php echo $showWebdaos; ?>;">
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
<div class="panel panel-group b2s-network-auth-info b2s-left-border-success b2s-post-edit-success" style="display:none;">
    <div class="panel-body">
        <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('This post was edited successfully.', 'blog2social'); ?>
    </div>
</div>
<?php if (isset($_GET['origin']) && $_GET['origin'] == 'save_post' && isset($_GET['postStatus'])) { ?>
    <div class="panel panel-group b2s-network-auth-info">
        <div class="panel-body">
            <span class="glyphicon glyphicon-ok glyphicon-success"></span>
            <?php
            if ($_GET['postStatus'] == 'future') {
                echo esc_html__('Post was scheduled successfully on your blog!', 'blog2social');
            } else {
                echo esc_html__('Post is published successfully on your blog!', 'blog2social');
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
        <span class="glyphicon glyphicon-remove glyphicon-danger"></span> <?php echo esc_html__('Your limit for your quota of posts in your queue has been reached. Please delete posts from your queue before you add more', 'blog2social') . ((B2S_PLUGIN_USER_VERSION < 3) ? esc_html__(' or upgade your Blog2Social license to extend your quota.', 'blog2social') : '.'); ?>
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
    <?php if (B2S_PLUGIN_USER_VERSION == 0 && !defined("B2S_PLUGIN_TRAIL_END") && !get_option('B2S_PLUGIN_DISABLE_TRAIL') && !get_option('B2S_HIDE_PREMIUM_MESSAGE') && (isset($_GET['page']) && in_array($_GET['page'], array("blog2social", "blog2social-post", "blog2social-sched", "blog2social-publish", "blog2social-calendar")))) { ?>
        <div class="panel panel-group b2s-trail-premium-info-area b2s-notice">
            <div class="panel-body">
                <div class="b2s-hide-premium-message b2s-close"><i class="glyphicon glyphicon-remove"></i></div>
                <h2 style="margin-top:0;font-size:20px;"><?php esc_html_e('Start your free 30-day-Premium-trial', 'blog2social'); ?></h2>
                <p>
                    <?php esc_html_e('Check out Blog2Social Premium with more awesome features for scheduling and sharing (e.g. auto-posting, best time scheduling, social media calendar) 30-days for free. The trial is free of charge, without any obligations, no automatic subscription. Basic features of the Free Version are free forever.', 'blog2social'); ?>
                </p>
                <p class="b2s-notice-buttons">
                    <a href="#" class="b2s-text-underline b2s-trial-modal-btn">
                        <?php esc_html_e('Yes, I want to test Blog2Social Premium 30 days for free', 'blog2social'); ?>
                    </a>
                </p>
            </div>
        </div>
    <?php } ?>

    <?php if (defined("B2S_PLUGIN_TRAIL_END") && strtotime(B2S_PLUGIN_TRAIL_END) > strtotime(gmdate('Y-m-d H:i:s')) && !get_option('B2S_HIDE_TRAIL_MESSAGE') && (isset($_GET['page']) && in_array($_GET['page'], array("blog2social", "blog2social-post", "blog2social-sched", "blog2social-publish", "blog2social-calendar")))) { ?>
        <div class="panel panel-group b2s-trail-premium-info-area b2s-notice">
            <div class="panel-body">
                <div class="b2s-hide-trail-message b2s-close"><i class="glyphicon glyphicon-remove"></i></div>
                <h2 style="margin-top:0;font-size:20px;">
                    <?php esc_html_e('Your free Blog2Social Premium trial version is activated for ', 'blog2social'); ?>
                    <?php
                    $days = B2S_Util::getTrialRemainingDays(B2S_PLUGIN_TRAIL_END, date_default_timezone_get());
                    echo $days > 0 ? ("<span style='color:#79B232'>" . $days . "</span>" . esc_html__(' Days', 'blog2social')) : "<span style='color:#f33'>" . esc_html__(' today', 'blog2social') . "</span>";
                    ?>
                </h2>
                <p>
                    <?php echo esc_html_e('Blog2Social PREMIUM can do so much for you: Auto-publish your blog post on autopilot, automatically schedule your social media posts with the Best Time Manager. Select images and post formats (link post or image post) for each social community. Upload and select any image for sharing. Save multiple combinations of networks for different sharing purposes. Start from only $6.58 per month to benefit from PREMIUM features.', 'blog2social'); ?>
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
                    <?php echo esc_html_e('Blog2Social PREMIUM can do so much for you: Auto-publish your blog post on autopilot, automatically schedule your social media posts with the Best Time Manager. Select images and post formats (link post or image post) for each social community. Upload and select any image for sharing. Save multiple combinations of networks for different sharing purposes. Start from only $6.58 per month to benefit from PREMIUM features.', 'blog2social'); ?>
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

<!--Header-->

<!-- B2S-Trial -->
<div id="b2s-trial-modal" class="modal fade" role="dialog" aria-labelledby="b2s-trial-modal" aria-hidden="true" data-backdrop="false" style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name="#b2s-trial-modal">&times;</button>
                <h4 class="modal-title"><?php esc_html_e('Test Blog2Social PREMIUM 30 days for free', 'blog2social'); ?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="alert alert-danger b2s-trail-modal-fail" style="display:none;">
                            <?php esc_html_e('The free trial can not be started. This blog has been already registered for the free trial.', 'blog2social'); ?>
                        </div>
                        <div class="form-group col-xs-12">
                            <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Social Media Auto-Posting', 'blog2social'); ?><br>
                            <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Post on pages and groups', 'blog2social'); ?><br>
                            <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Share on multiple accounts per network', 'blog2social'); ?><br>
                            <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Best Time Scheduler: Schedule once, multiple times or recurringly.', 'blog2social'); ?><br>
                            <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Reporting with links to all published social media posts', 'blog2social'); ?><br>
                        </div>
                        <div class="form-group col-xs-12">
                            <label for="trial_email"><?php esc_html_e('E-Mail', 'blog2social'); ?></label>
                            <input id="trial_email" class="form-control" type="email" value="<?php echo $wpUserData->user_email; ?>" name="trial_email">
                        </div>
                        <div class="form-group col-xs-12  col-md-6">
                            <label for="trial_vorname"><?php esc_html_e('First Name', 'blog2social'); ?></label>
                            <input id="trial_vorname" class="form-control" type="text" value="<?php echo $wpUserData->user_firstname; ?>" name="trial_vorname">
                        </div>
                        <div class="form-group col-xs-12  col-md-6">
                            <label for="trial_nachname"><?php esc_html_e('Last Name', 'blog2social'); ?></label>
                            <input id="trial_nachname" class="form-control" type="text" value="<?php echo $wpUserData->user_lastname; ?>" name="trial_nachname">
                        </div>
                        <div class="col-xs-12">
                            <p>
                                <?php
                                echo sprintf(__('By creating an account, you agree to Blog2Social\'s <a target="_blank" href="%s">Conditions of Use</a>', 'blog2social'), esc_url(B2S_Tools::getSupportLink('term')));
                                echo sprintf(__('and <a target="_blank" href="%s">Privacy Notice</a>', 'blog2social'), esc_url(B2S_Tools::getSupportLink('privacy_policy')));
                                ?>
                            </p>
                            <br>
                        </div>
                        <div class="col-xs-12">
                            <div class="pull-left">
                                <span class="glyphicon glyphicon-info-sign glyphicon-primary"></span>  <?php esc_html_e('No credit card required', 'blog2social'); ?>
                            </div>
                            <div class="pull-right">
                                <input type="hidden" name="trial_url" id="trial_url" value="<?php echo get_option('home'); ?>" />
                                <input class="btn btn-success pull-right b2s-trail-btn-start" type="submit" value="<?php esc_html_e('Get Started', 'blog2social'); ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
                <h4 class="modal-title"> <img src="<?php echo plugins_url('/assets/images/b2s@32.png', B2S_PLUGIN_FILE); ?>" alt="blog2social"> <?php esc_html_e('We updated our Privacy Policy', 'blog2social') ?></h4>
            </div>
            <div class="modal-body b2s-scroll-modal-body b2s-modal-privacy-policy-scroll-content">
                <p>
                    <?php
                    if ($b2sPrivacyPolicy !== false) {
                        echo utf8_encode($b2sPrivacyPolicy);
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

