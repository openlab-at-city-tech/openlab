<?php
wp_nonce_field('b2s_security_nonce', 'b2s_security_nonce');
$b2sSiteUrl = get_option('siteurl') . ((substr(get_option('siteurl'), -1, 1) == '/') ? '' : '/');
$b2sGeneralOptions = get_option('B2S_PLUGIN_GENERAL_OPTIONS');
?>
<div class="b2s-container">
    <div class="b2s-inbox">
        <div class="col-md-12 del-padding-left">
            <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/html/sidebar.php'); ?>
            <div class="col-md-9 del-padding-left del-padding-right">
                <!--Header|Start - Include-->
                <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/html/header.php'); ?>
                <!--Header|End-->
                <div class="clearfix"></div>

                <div class="row">
                    <div class="col-md-12 del-padding-left del-padding-right">
                        <div class="col-md-8 col-sm-12">
                            <?php if (!B2S_System::isblockedArea('B2S_DASHBOARD_MODUL_NEWS', B2S_PLUGIN_ADMIN)) { ?>
                                <div class="panel panel-default b2s-panel-height-300">
                                    <div class="panel-body">
                                        <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/widgets/content.php'); ?>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="col-md-4 hidden-sm">
                            <div class="panel panel-default b2s-panel-height-300">
                                <div class="panel-body">
                                    <div class="b2s-cal-sched-dashboard-loader b2s-loader-impulse b2s-loader-impulse-md"></div>
                                    <div id="b2s-cal-sched-dashboard"></div>
                                    <div class="text-center">
                                        <a href="admin.php?page=blog2social-calendar" class="btn btn-link b2s-color-green b2s-font-bold"><?php esc_html_e("Open calendar", "blog2social"); ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 del-padding-left del-padding-right">
                        <div class="col-md-3 col-sm-6 hidden-xs">
                            <div class="panel panel-default">
                                <div class="panel-header text-center b2s-panel-header-addon b2s-font-color-green b2s-font-size-24 b2s-padding-top-20">
                                    <span class="glyphicon glyphicon-star glyphicon-success"></span>
                                </div>
                                <div class="panel-body b2s-panel-body-dashboard-premium b2s-panel-bg-body">
                                    <h3 class="b2s-font-color-green text-center b2s-font-bold b2s-margin-top-5">
                                        <?php esc_html_e("Upgrade Blog2Social", "blog2social"); ?></h3>
                                    <p>
                                    <ul class="b2s-list-dashboard">
                                        <li><?php esc_html_e("20+ Social Platforms", "blog2social"); ?></li>
                                        <li><?php esc_html_e("Auto-Posting", "blog2social"); ?></li>
                                        <li><?php esc_html_e("Best Time Manager", "blog2social"); ?></li>
                                        <li><?php esc_html_e("Social Media Calendar", "blog2social"); ?></li>
                                        <li><?php esc_html_e("Customizable Post Templates", "blog2social"); ?></li>
                                        <li><?php esc_html_e("Automatic Resharing", "blog2social"); ?></li>
                                    </ul>
                                    </p>
                                </div>
                                <div class="panel-footer text-center b2s-panel-bg-footer">
                                    <?php if ((int) B2S_PLUGIN_USER_VERSION >= 3 && (!defined("B2S_PLUGIN_TRAIL_END") || (defined("B2S_PLUGIN_TRAIL_END") && strtotime(B2S_PLUGIN_TRAIL_END) < time()))) { ?>
                                        <a href="#" class="btn b2s-font-bold b2s-btn-dashboard-filled b2s-dashboard-premium-enterprise-version-btn"><?php esc_html_e("Upgrade now", "blog2social"); ?></a>
                                    <?php } else { ?>
                                        <a target="_blank" href="<?php echo B2S_Tools::getSupportLink('pricing'); ?>" target="_blank" class="btn b2s-font-bold b2s-btn-dashboard-filled"><?php esc_html_e("Upgrade now", "blog2social"); ?></a>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-6 hidden-xs">
                            <div class="panel panel-default">
                                <div class="panel-header pull-right b2s-panel-header-addon b2s-font-color-white">
                                    <?php esc_html_e("Addon", "blog2social"); ?></div>

                                <div class="panel-body text-center b2s-panel-body-dashboard">
                                    <h3 class="b2s-font-color-green b2s-font-bold b2s-padding-top-36"><?php esc_html_e("Add Video Posting", "blog2social"); ?></h3>
                                    <p><?php esc_html_e("Share your video files on YouTube, Vimeo, TikTok, Facebook, Instagram, Pinterest and X (Twitter). Get 25 GB data volume, valid for one year, top-up at any time. ", "blog2social"); ?></p>
                                    <p><a target="_blank" href="<?php echo esc_url(B2S_Tools::getSupportLink('dashboard-video-posting-addon-info')); ?>"><?php esc_html_e("Discover the Video Posting feature", "blog2social") ?></a></p>
                                </div>
                                <div class="panel-footer text-center">
                                    <a target="_blank" href="<?php echo B2S_Tools::getSupportLink('addon_video'); ?>" class="btn b2s-font-bold b2s-btn-dashboard-outline"><?php esc_html_e("Buy now", "blog2social"); ?></a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-6 hidden-xs">
                            <div class="panel panel-default">
                                <div class="panel-header pull-right b2s-panel-header-addon b2s-font-color-white">
                                    <?php esc_html_e("Addon", "blog2social"); ?></div>

                                <div class="panel-body text-center b2s-panel-body-dashboard">
                                    <h3 class="b2s-font-color-green b2s-font-bold b2s-padding-top-36"><?php esc_html_e("Add Licenses User", "blog2social"); ?></h3>
                                    <p><?php esc_html_e("Add more users to suit your growing social media demands - whether you're a business, team, or managing clients.", "blog2social"); ?></p>
                                </div>
                                <div class="panel-footer text-center">
                                    <?php if ((int) B2S_PLUGIN_USER_VERSION <= 1 || ((int) B2S_PLUGIN_USER_VERSION >= 3 && defined("B2S_PLUGIN_TRAIL_END") && strtotime(B2S_PLUGIN_TRAIL_END) > time())) { ?>
                                        <a href="#" class="btn b2s-font-bold b2s-btn-dashboard-outline b2s-dashboard-addon-add-user-btn"><?php esc_html_e("Buy now", "blog2social"); ?></a>
                                    <?php } else { ?>
                                        <a target="_blank" href="<?php echo B2S_Tools::getSupportLink('addon_user_licence'); ?>" class="btn b2s-font-bold b2s-btn-dashboard-outline"><?php esc_html_e("Buy now", "blog2social"); ?></a>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-6 hidden-xs">
                            <div class="panel panel-default">
                                <div class="panel-header pull-right b2s-panel-header-addon b2s-font-color-white">
                                    <?php esc_html_e("Addon", "blog2social"); ?></div>

                                <div class="panel-body text-center b2s-panel-body-dashboard">
                                    <h3 class="b2s-font-color-green b2s-font-bold b2s-padding-top-36"><?php esc_html_e("Add Social Media Accounts", "blog2social"); ?></h3>
                                    <p><?php esc_html_e("Expand your social media management with extra accounts per network!", "blog2social"); ?></p>
                                </div>
                                <div class="panel-footer text-center">
                                    <?php if ((int) B2S_PLUGIN_USER_VERSION <= 1 || ((int) B2S_PLUGIN_USER_VERSION >= 3 && defined("B2S_PLUGIN_TRAIL_END") && strtotime(B2S_PLUGIN_TRAIL_END) > time())) { ?>
                                        <a href="#" class="btn b2s-font-bold b2s-btn-dashboard-outline b2s-dashboard-addon-add-social-account-btn"><?php esc_html_e("Buy now", "blog2social"); ?></a>
                                    <?php } else { ?>
                                        <a target="_blank" href="<?php echo B2S_Tools::getSupportLink('addon_social_account'); ?>" class="btn b2s-font-bold b2s-btn-dashboard-outline"><?php esc_html_e("Buy now", "blog2social"); ?></a>                                
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="panel panel-default">
                    <div class="panel-body">
                        <h3 class="b2s-font-bold"><?php esc_html_e("Your Activity", "blog2social"); ?></h3>  

                        <div class="row">
                            <div class="col-md-6 col-sm-12 add-margin-top-10">
                                <h4><?php esc_html_e("Last shared on Social Media", "blog2social"); ?></h4>

                                <div class="b2s-dashboard-activity-publish-loader b2s-loader-impulse b2s-loader-impulse-md"></div>

                                <div class="b2s-dashboard-activity-publish-case-1" style="display: none;">
                                    <a href="admin.php?page=blog2social-post"><?php esc_html_e("Share your first post now!", "blog2social"); ?></a>
                                </div>

                                <ul class="b2s-timeline b2s-dashboard-activity-publish" style="display: none;"></ul>

                            </div>

                            <div class="col-md-6 col-sm-12 add-margin-top-10">

                                <div class="b2s-dashboard-activity-sched-loader b2s-loader-impulse b2s-loader-impulse-md"></div>

                                <div class="b2s-dashboard-activity-sched-case-1" style="display: none;">
                                    <h4><?php esc_html_e("Next scheduled posts", "blog2social"); ?></h4>
                                    <br>
                                    <a href="admin.php?page=blog2social-post"><?php esc_html_e("Schedule your first post now!", "blog2social"); ?></a>
                                </div>

                                <div class="b2s-dashboard-activity-sched-case-2" style="display: none;">
                                    <h4><?php esc_html_e("Start your 30-day free trial with Blog2Social!", "blog2social"); ?></h4>                               
                                    <p><?php esc_html_e("Streamline your social media management across 20+ platforms with advanced features:", "blog2social"); ?></p>
                                    <ul class="b2s-list-dashboard b2s-color-black">
                                        <li><?php esc_html_e("Advanced scheduling capabilities for optimal posting times across multiple channels", "blog2social"); ?></li>
                                        <li><?php esc_html_e("Automated posting and resharing to save time and maintain a consistent content stream", "blog2social"); ?></li>
                                        <li><?php esc_html_e("Customizable templates for optimized social media presentation", "blog2social"); ?></li>
                                        <li><?php esc_html_e("Flexible user and account settings and add-ons for efficient social media management", "blog2social"); ?></li>
                                    </ul>
                                    <p><?php esc_html_e("These exclusive features can help you reach more people in less time.", "blog2social"); ?></p>
                                    <br>
                                    <a target="_blank" href="<?php echo B2S_Tools::getSupportLink('trial'); ?>" class="btn b2s-font-bold b2s-btn-dashboard-filled"><?php esc_html_e("Start your free trial now", "blog2social"); ?></a>
                                </div>

                                <div class="b2s-dashboard-activity-sched-case-3" style="display: none;">
                                    <h4><?php esc_html_e("Next scheduled posts", "blog2social"); ?></h4>
                                    <br>
                                    <a class="b2s-dashboard-trial-expired-btn" href="#"><?php esc_html_e("Schedule your first post now!", "blog2social"); ?></a>
                                </div>

                                <div class="b2s-dashboard-activity-sched-case-4" style="display: none;">
                                    <h4><?php esc_html_e("Next scheduled posts", "blog2social"); ?></h4>
                                    <ul class="b2s-timeline b2s-dashboard-activity-sched"></ul>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="clearfix"></div>
                <?php if (!B2S_System::isblockedArea('B2S_DASHBOARD_MODUL_NEWS_EMAIL ', B2S_PLUGIN_ADMIN)) { ?>
                    <div class="panel panel-default b2s-panel-dashboard-footer hidden-xs hidden-sm">
                        <div class="panel-body">
                            <div class="col-md-8">
                                <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/widgets/newsletter.php'); ?>
                            </div>
                            <div class="col-md-4">
                                <div class="pull-right">
                                    <div class="form-inline">
                                        <label class="b2s-text-xl b2s-color-white"><?php esc_html_e("Follow us", "blog2social") ?></label>
                                        <a href="https://www.facebook.com/Blog2Social/" target="_blank" rel="nofollow"><img src="<?php echo esc_url(plugins_url('/assets/images/portale/1_flat.png', B2S_PLUGIN_FILE)); ?>" width="28" alt="Facebook"></a>
                                        <a href="https://www.instagram.com/blog2social/" target="_blank" rel="nofollow"><img src="<?php echo esc_url(plugins_url('/assets/images/portale/12_flat.png', B2S_PLUGIN_FILE)); ?>" width="28" alt="Instagram"></a>
                                        <a href="https://twitter.com/Blog2Social" target="_blank" rel="nofollow"><img src="<?php echo esc_url(plugins_url('/assets/images/portale/2_flat.png', B2S_PLUGIN_FILE)); ?>" width="28" alt="Twitter"></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/html/sidebar.php'); ?>
        </div>
    </div>
    <div class="col-md-12">
        <?php
        $noLegend = 1;
        require_once (B2S_PLUGIN_DIR . 'views/b2s/html/footer.php');
        ?>
    </div>
</div>

<input type="hidden" id="b2sLang" value="<?php echo esc_attr(substr(B2S_LANGUAGE, 0, 2)); ?>">
<input type="hidden" id="b2s-redirect-url-sched-post" value="<?php echo esc_url($b2sSiteUrl) . 'wp-admin/admin.php?page=blog2social-sched'; ?>"/>
<input type="hidden" id="isLegacyMode" value="<?php echo (isset($b2sGeneralOptions['legacy_mode']) ? (int) esc_attr($b2sGeneralOptions['legacy_mode']) : 0); ?>">


<div id="b2s-dashboard-premium-addon-add-user-modal" class="modal fade" role="dialog" aria-labelledby="b2s-dashboard-premium-addon-add-user-modal" aria-hidden="true" data-backdrop="false" style="display:none;z-index: 1070;">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name="#b2s-dashboard-premium-addon-add-user-modal">&times;</button>
                <h3 class="modal-title b2s-color-green"><?php esc_html_e('Expand your capabilities by adding more users!', 'blog2social') ?></h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12">
                        <p><?php esc_html_e('With a Pro or Business License, you\'ll already have more users included, and you can easily add even more to suit your needs - whether for your business, team, or clients.', 'blog2social') ?></p><!-- comment -->
                        <br>
                        <p class="b2s-text-bold"><?php esc_html_e('Upgrade now to unlock additional users and powerful features for your social media management!', 'blog2social') ?></p>
                        <br>
                        <a target="_blank" href="<?php echo B2S_Tools::getSupportLink('pricing'); ?>" target="_blank" class="btn b2s-font-bold b2s-btn-dashboard-filled"><?php esc_html_e("Upgrade now", "blog2social"); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="b2s-dashboard-premium-addon-add-social-account-modal" class="modal fade" role="dialog" aria-labelledby="b2s-dashboard-premium-addon-add-social-account-modal" aria-hidden="true" data-backdrop="false" style="display:none;z-index: 1070;">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name="#b2s-dashboard-premium-addon-add-social-account-modal">&times;</button>
                <h3 class="modal-title b2s-color-green"><?php esc_html_e('Unlock more social media accounts with an upgrade!', 'blog2social') ?></h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12">
                        <p><?php esc_html_e('With a Pro or Business License, you\'ll start with additional accounts per network included in your license and have the flexibility to add even more as needed—ideal for businesses, teams, or clients. And you will get more powerful features for your social media management. ', 'blog2social') ?></p>
                        <br>
                        <p class="b2s-text-bold"><?php esc_html_e('Upgrade now to maximize your social media impact!', 'blog2social') ?></p>
                        <br>
                        <a target="_blank" href="<?php echo B2S_Tools::getSupportLink('pricing'); ?>" target="_blank" class="btn b2s-font-bold b2s-btn-dashboard-filled"><?php esc_html_e("Upgrade now", "blog2social"); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<div id="b2s-dashboard-premium-enterprise-version-modal" class="modal fade" role="dialog" aria-labelledby="b2s-dashboard-premium-enterprise-version-modal" aria-hidden="true" data-backdrop="false" style="display:none;z-index: 1070;">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name="#b2s-dashboard-premium-enterprise-version-modal">&times;</button>
                <h3 class="modal-title b2s-color-green"><?php esc_html_e('Expand your Blog2Social experience!', 'blog2social') ?></h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12">
                        <p><?php esc_html_e('Since you are already on our Business version, we would love to introduce you to the flexibility and enhanced features available with an individual plan just for you. Benefit from additional options that go beyond our regular licenses.', 'blog2social') ?></p>
                        <p class="b2s-text-bold"><?php esc_html_e('Our sales team is here to help you explore all the options and create a personalized plan tailored just for you.', 'blog2social') ?></p>
                        <p><?php esc_html_e('Contact us today to unlock the full potential of your Blog2Social experience for your business!', 'blog2social') ?></p>
                        <p class="b2s-text-bold"><?php esc_html_e('Email', 'blog2social') ?>: customer-service@blog2social.com</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>