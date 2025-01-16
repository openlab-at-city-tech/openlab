<?php
$b2sLastVersion = get_option('b2s_plugin_version');
$customizeArea = B2S_System::customizeArea();
$getPage = (isset($_GET['page']) && !empty($_GET['page'])) ? sanitize_text_field(wp_unslash($_GET['page'])) : '';
?>
<!-- Sidebar|Start -Include-->
<div class="col-md-3 col-xs-12 del-padding-left del-padding-right b2s-sidebar hidden-xs hidden-sm b2s-margin-right-20">
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="col-md-12 del-padding-right">
                <div class="row">
                    <div class="media"> 
                        <?php if (is_array($customizeArea) && isset($customizeArea['image_path']) && !empty($customizeArea['image_path'])) { ?>
                            <div class="col-md-12 del-padding-left">
                                <img class="img-responsive" src="<?php echo esc_url($customizeArea['image_path']); ?>" alt="logo">    
                            </div> 
                        <?php } else { ?>
                            <div class="col-md-2 del-padding-left">
                                <a class="" href="admin.php?page=blog2social">
                                    <img class="img-responsive b2s-img-logo" src="<?php echo esc_url(plugins_url('/assets/images/b2s@64.png', B2S_PLUGIN_FILE)); ?>" alt="logo">
                                </a>
                            </div> 
                            <div class="col-md-10 del-padding-left">
                                <div class="media-body">
                                    <?php if (!B2S_System::isblockedArea('B2S_MENU_ITEM_LOGO', B2S_PLUGIN_ADMIN)) { ?>
                                        <a href="admin.php?page=blog2social" class="b2s-btn-logo"><?php esc_html_e("Blog2Social", "blog2social") ?></a> 
                                        <div class="b2s-sidebar-version padding-left-5"><?php echo ($b2sLastVersion !== false) ? esc_html__("Version", "blog2social") . ' ' . B2S_Util::getVersion($b2sLastVersion) : ''; ?> </div>
                                    <?php } ?>
                                </div>                               
                            </div>
                        <?php } ?>
                    </div>
                </div>
                
                <?php if (!B2S_System::isblockedArea('B2S_MENU_ITEM_LICENSE', B2S_PLUGIN_ADMIN)) { ?> 
                    <div class="row">
                        <div class="panel panel-default b2s-margin-right-10 b2s-margin-bottom-10 b2s-margin-top-8">
                            <div class="panel-body b2s-padding-10">
                                <div class="media d-flex">
                                    <div class="align-self-center">
                                        <i class="glyphicon glyphicon-stats glyphicon-success float-left"></i>
                                        <span class="b2s-sidebar-licence"><?php esc_html_e("License", "blog2social") ?>:</span>
                                        <a href="admin.php?page=blog2social-premium" class="b2s-sidebar-btn-licence b2s-key-name">
                                            <?php
                                            $versionType = unserialize(B2S_PLUGIN_VERSION_TYPE);
                                            if (defined("B2S_PLUGIN_TRAIL_END") && strtotime(B2S_PLUGIN_TRAIL_END) > time()) {
                                                echo 'FREE-TRIAL (' . esc_html($versionType[B2S_PLUGIN_USER_VERSION]) . ')';
                                            } else {
                                                echo esc_html($versionType[B2S_PLUGIN_USER_VERSION]);
                                            }
                                            ?></a>
                                        <?php
                                        if (B2S_PLUGIN_USER_VERSION == 0) {
                                            if ((defined("B2S_PLUGIN_TRAIL_END") && strtotime(B2S_PLUGIN_TRAIL_END) < time()) || get_option('B2S_PLUGIN_DISABLE_TRAIL') == true) {
                                                echo '<a class="btn-link b2s-free-link padding-left-5" target="_blank" href="' . esc_url(B2S_Tools::getSupportLink('affiliate')) . '">' . esc_html__('Upgrade to Premium', 'blog2social') . '</a>';
                                            } else {
                                                echo '<br><a class="btn-link b2s-free-link padding-left-16" target="_blank" href="' . esc_url(B2S_Tools::getSupportLink('feature')) . '">' . esc_html__('Start your 30-day free Premium trial', 'blog2social') . '</a>';
                                            }
                                        }
                                        ?>
                                        <br>
                                        <?php if (defined('B2S_PLUGIN_ADDON_VIDEO') && !empty(B2S_PLUGIN_ADDON_VIDEO)) { ?>
                                            <div class="b2s-sidebar-video-addon padding-left-16">
                                                <?php esc_html_e("Addon", "blog2social") ?>: <a href="admin.php?page=blog2social-video" class="b2s-sidebar-btn-video-addon"><?php esc_html_e("Video", "blog2social") ?></a>
                                            </div>
                                        <?php } ?>
                                    </div>

                                    <?php
                                    $licenceCond = get_option('B2S_PLUGIN_USER_VERSION_' . B2S_PLUGIN_BLOG_USER_ID);
                                    if ($licenceCond !== false && is_array($licenceCond) && !empty($licenceCond) && isset($licenceCond['B2S_PLUGIN_LICENCE_CONDITION'])) {
                                        $licenceCond = $licenceCond['B2S_PLUGIN_LICENCE_CONDITION'];
                                        if (isset($licenceCond['open_daily_post_quota']) && isset($licenceCond['open_sched_post_quota'])) {
                                            ?>
                                            <hr class="b2s-margin-bottom-10">
                                            <?php
                                            if (B2S_PLUGIN_USER_VERSION > 0) {
                                                if (defined("B2S_PLUGIN_TRAIL_END") && strtotime(B2S_PLUGIN_TRAIL_END) > time()) {
                                                    ?>
                                                    <h3 class="b2s-h3 b2s-stats-h3"><?php esc_html_e("Your post volume", "blog2social") ?></h3>                                                                                                    
                                                <?php } else { ?>
                                                    <h3 class="b2s-h3 b2s-stats-h3"><?php esc_html_e("Your yearly post volume", "blog2social") ?></h3>                                                    
                                                <?php } ?>
                                            <?php } else { ?>
                                                <h3 class="b2s-h3 b2s-stats-h3"><?php esc_html_e("Your daily post volume", "blog2social") ?></h3>                                     
                                                <?php
                                            }
                                            $openCond = $licenceCond['open_daily_post_quota'];
                                            $totalCond = $licenceCond['total_daily_post_quota'];
                                            if (B2S_PLUGIN_USER_VERSION > 0) {
                                                $openCond = $licenceCond['open_sched_post_quota'];
                                                $totalCond = $licenceCond['total_sched_post_quota'];
                                            }

                                            echo wp_kses(B2S_Notice::getPostStats($openCond, $totalCond), array(
                                                'div' => array(
                                                    'class' => array(),
                                                    'style' => array()
                                                ),
                                                'a' => array(
                                                    'target' => array(),
                                                    'href' => array(),
                                                    'class' => array()
                                                ),
                                                'span' => array(
                                                    'class' => array()
                                                )
                                            ));
                                            ?>
                                            <div class="media-body b2s-font-size-11">
                                                <span class="b2s-span-float-left"><span id="current_licence_open_sched_post_quota" class="b2s-text-bold"><?php echo (int) $openCond ?></span> <?php esc_html_e("remaining from", "blog2social") ?> <?php echo (int) $totalCond; ?></span>
                                                <?php $linkRouting = ((defined("B2S_PLUGIN_TRAIL_END") && strtotime(B2S_PLUGIN_TRAIL_END) > time()) || (B2S_PLUGIN_USER_VERSION == 0)) ? 'affiliate' : 'addon_post_volume'; ?>
                                                <span class="b2s-span-float-right"><a target="_blank" href="<?php echo esc_url(B2S_Tools::getSupportLink($linkRouting)); ?>"><?php esc_html_e("Need more?", "blog2social") ?></a></span>
                                                <div class="clearfix"></div>
                                            </div>
                                            <?php
                                        }

                                        if (isset($licenceCond['open_daily_post_quota'])) {
                                            ?>                                       
                                            <input type="hidden" id="current_licence_open_daily_post_quota" name="current_licence_open_daily_post_quota" value="<?php echo $licenceCond['open_daily_post_quota']; ?>" />
                                            <?php
                                            $dailyLimit = ((int) $licenceCond['open_daily_post_quota'] <= 0) ? '' : 'b2s-info-display-none';
                                            ?>
                                            <h3 class="b2s-h3 b2s-current-licence-open-daily-post-quota-sidebar-info b2s-color-red b2s-text-underline <?php echo $dailyLimit; ?> b2s-text-bold"><?php echo sprintf(__('Daily Limit of %d posts reached!', 'blog2social'), esc_html($licenceCond['total_daily_post_quota'])); ?></h3>
                                            <?php
                                        }
                                    }
                                    ?>
                                    <div class="clearfix"></div>                                   
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>

            </div>
            <div class="clearfix"></div>
            <div class="col-md-12">
                <div class="row">
                    <div class="b2s-sidebar-head">
                        <div class="b2s-sidebar-head-text">
                            <?php esc_html_e("Post Management", "blog2social") ?>
                        </div>
                        <ul>
                            <li class="b2s-list-margin-left-10">
                                <i class="glyphicon glyphicon-th-list glyphicon-success"></i> <a href="admin.php?page=blog2social-post" class="b2s-sidebar-menu-item <?php echo (($getPage == 'blog2social-post') ? ' b2s-text-bold' : '') ?>"><?php esc_html_e("All Posts", "blog2social") ?></a> 
                            </li>
                            <li class="b2s-list-margin-left-10">
                                <i class="glyphicon glyphicon-star glyphicon-success"></i> <a href="admin.php?page=blog2social-favorites" class="b2s-sidebar-menu-item <?php echo (($getPage == 'blog2social-favorites') ? ' b2s-text-bold' : '') ?>"><?php esc_html_e("Favorites", "blog2social") ?></a> 
                            </li>
                            <li class="b2s-list-margin-left-10">
                                <i class="glyphicon glyphicon-asterisk glyphicon-success"></i> <a href="admin.php?page=blog2social-ai-content-creator" class="b2s-sidebar-menu-item"><?php esc_html_e("AI Assistant", "blog2social") ?> <span class="label label-success"><?php esc_html_e("NEW", "blog2social"); ?></span></a> 
                            </li>
                            <li class="b2s-list-margin-left-10">
                                <i class="glyphicon glyphicon-th-list glyphicon-success"></i> <a href="admin.php?page=blog2social-draft-post" class="b2s-sidebar-menu-item <?php echo (($getPage == 'blog2social-draft-post') ? ' b2s-text-bold' : '') ?>"><?php esc_html_e("Drafts", "blog2social") ?></a> 
                            </li>
                            <li class="b2s-list-margin-left-10">
                                <i class="glyphicon glyphicon-th-list glyphicon-success"></i> <a href="admin.php?page=blog2social-approve" class="b2s-sidebar-menu-item <?php echo (($getPage == 'blog2social-approve') ? ' b2s-text-bold' : '') ?>"><?php esc_html_e("Instant Sharing", "blog2social") ?></a> 
                            </li>
                            <li class="b2s-list-margin-left-10">
                                <i class="glyphicon glyphicon-time glyphicon-success"></i> <a href="admin.php?page=blog2social-sched" class="b2s-sidebar-menu-item <?php echo (($getPage == 'blog2social-sched') ? ' b2s-text-bold' : '') ?>"><?php esc_html_e("Scheduled Posts", "blog2social") ?></a> 
                            </li>
                            <li class="b2s-list-margin-left-10">
                                <i class="glyphicon glyphicon-th-list glyphicon-success"></i> <a href="admin.php?page=blog2social-publish" class="b2s-sidebar-menu-item <?php echo (($getPage == 'blog2social-publish') ? ' b2s-text-bold' : '') ?>"><?php esc_html_e("Shared Posts", "blog2social") ?></a> 
                            </li>
                            <li class="b2s-list-margin-left-10">
                                <i class="glyphicon glyphicon-random glyphicon-success"></i> <a href="admin.php?page=blog2social-repost" class="b2s-sidebar-menu-item <?php echo (($getPage == 'blog2social-repost') ? ' b2s-text-bold' : '') ?>"><?php esc_html_e("Re-Share Posts", "blog2social") ?></a> 
                            </li>
                            <li class="b2s-list-margin-left-10">
                                <i class="glyphicon glyphicon-film glyphicon-success"></i> <a href="admin.php?page=blog2social-video" class="b2s-sidebar-menu-item <?php echo (($getPage == 'blog2social-video') ? ' b2s-text-bold' : '') ?>"><?php esc_html_e("Share Videos", "blog2social") ?></a> 
                            </li>
                            <li class="b2s-list-margin-left-10">
                                <i class="glyphicon glyphicon-play glyphicon-success"></i> <a href="admin.php?page=blog2social-autopost" class="b2s-sidebar-menu-item <?php echo (($getPage == 'blog2social-autopost') ? ' b2s-text-bold' : '') ?>"><?php esc_html_e("Auto-Post", "blog2social") ?></a> 
                            </li>
                            <li class="b2s-list-margin-left-10">
                                <i class="glyphicon glyphicon-calendar glyphicon-success"></i> <a href="admin.php?page=blog2social-calendar" class="b2s-sidebar-menu-item <?php echo (($getPage == 'blog2social-calendar') ? ' b2s-text-bold' : '') ?>"><?php esc_html_e("Calendar", "blog2social") ?></a> 
                            </li>
                            <li class="b2s-list-margin-left-10">
                                <?php
                                global $wpdb;
                                $sql = "SELECT COUNT(posts.`post_id`) FROM `{$wpdb->prefix}b2s_posts` posts WHERE (posts.`sched_date` = '0000-00-00 00:00:00' OR (posts.`sched_type` = 3 AND posts.`publish_date` != '0000-00-00 00:00:00')) AND posts.`post_for_approve`= 0  AND posts.`publish_error_code` != '' AND posts.`hide` = 0";
                                $res = $wpdb->get_var($sql);
                                ?>
                                <i class="glyphicon glyphicon-exclamation-sign glyphicon-success"></i> <a href="admin.php?page=blog2social-notice" class="b2s-sidebar-menu-item <?php echo (($getPage == 'blog2social-notice') ? ' b2s-text-bold' : '') ?>"><?php esc_html_e("Notifications", "blog2social") ?></a> <?php echo ($res > 0 ? '<span class="label label-warning">' . esc_html($res) . '</span>' : "") ?>
                            </li>
                        </ul>
                        <hr>
                        <ul>
                            <?php if ((defined("B2S_PLUGIN_USER_VERSION") && B2S_PLUGIN_USER_VERSION >= 3 && (!defined("B2S_PLUGIN_TRAIL_END") || (defined("B2S_PLUGIN_TRAIL_END") && strtotime(B2S_PLUGIN_TRAIL_END) < time()))) || (defined('B2S_PLUGIN_PERMISSION_INSIGHTS') && B2S_PLUGIN_PERMISSION_INSIGHTS == 1)) { ?>
                                <li class="b2s-list-margin-left-10">
                                    <i class="glyphicon glyphicon-signal glyphicon-success"></i> <a href="admin.php?page=blog2social-metrics" class="b2s-sidebar-menu-item <?php echo (($getPage == 'blog2social-metrics') ? ' b2s-text-bold' : '') ?>"><?php esc_html_e("Social Media Metrics", "blog2social") ?> <span class="label label-success"><?php esc_html_e("BETA", "blog2social"); ?></span></a> 
                                </li>
                            <?php } ?>
                            <li class="b2s-list-margin-left-10">
                                <i class="glyphicon glyphicon-user glyphicon-success"></i> <a href="admin.php?page=blog2social-network" class="b2s-sidebar-menu-item <?php echo (($getPage == 'blog2social-network') ? ' b2s-text-bold' : '') ?>"><?php esc_html_e("Networks", "blog2social") ?></a> 
                            </li>
                            <li class="b2s-list-margin-left-10">
                                <i class="glyphicon glyphicon-cog glyphicon-success"></i> <a href="admin.php?page=blog2social-settings" class="b2s-sidebar-menu-item <?php echo (($getPage == 'blog2social-settings') ? ' b2s-text-bold' : '') ?>"><?php esc_html_e("Settings", "blog2social") ?></a> 
                            </li>
                            <li class="b2s-list-margin-left-10">
                                <i class="glyphicon glyphicon-question-sign glyphicon-success"></i> <a href="admin.php?page=blog2social-support" class="b2s-sidebar-menu-item <?php echo (($getPage == 'blog2social-support') ? ' b2s-text-bold' : '') ?>"><?php esc_html_e("Help & Support", "blog2social") ?></a> 
                            </li>
                            <?php if (!B2S_System::isblockedArea('B2S_MENU_ITEM_LICENSE', B2S_PLUGIN_ADMIN)) { ?> 
                                <li class="b2s-list-margin-left-10">
                                    <i class="glyphicon glyphicon-pencil glyphicon-success"></i> <a href="admin.php?page=blog2social-premium" class="b2s-sidebar-menu-item <?php echo (($getPage == 'blog2social-premium') ? ' b2s-text-bold' : '') ?>"><?php esc_html_e("Upgrade License", "blog2social") ?></a> 
                                </li>
                            <?php } ?>
                            <?php if (!B2S_System::isblockedArea('B2S_MENU_ITEM_PLANS', B2S_PLUGIN_ADMIN)) { ?> 
                                <li class="b2s-list-margin-left-10">
                                    <i class="glyphicon glyphicon-signal glyphicon-success"></i> <a href="<?php echo esc_url(B2S_Tools::getSupportLink('affiliate')); ?>" target="_blank" class="b2s-sidebar-menu-item"><?php esc_html_e("Plans & Prices", "blog2social") ?></a> 
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="cleardfix"></div>

            <?php
            if (!B2S_System::isblockedArea('B2S_MENU_MODUL_CAL_EVENT', B2S_PLUGIN_ADMIN)) {
                echo wp_kses(B2S_Notice::getCalEvent(substr(B2S_LANGUAGE, 0, 2)), array(
                    'div' => array(
                        'class' => array()
                    ),
                    'a' => array(
                        'target' => array(),
                        'href' => array(),
                        'class' => array()
                    ),
                    'img' => array(
                        'src' => array(),
                        'alt' => array(),
                        'class' => array()
                    ),
                    'span' => array(
                        'class' => array()
                    ),
                    'hr' => array(
                        'class' => array()
                    ),
                    'br' => array(
                        'class' => array()
                    ),
                    'ul' => array(
                        'class' => array()
                    ),
                    'li' => array(
                        'class' => array()
                    ),
                    'h4' => array(
                        'class' => array()
                    )
                ));
            }
            ?>

            <div class="clearfix"></div>
            <?php if (!B2S_System::isblockedArea('B2S_MENU_MODUL_RATING', B2S_PLUGIN_ADMIN)) { ?> 
                <div class="col-md-12">
                    <div class="row">
                        <hr>
                        <div class="b2s-sidebar-head">
                            <div class="b2s-sidebar-head-text">
                                <span class="glyphicon glyphicon-star glyphicon-success"></span><span class="glyphicon glyphicon-star glyphicon-success"></span><span class="glyphicon glyphicon-star glyphicon-success"></span><span class="glyphicon glyphicon-star glyphicon-success"></span><span class="glyphicon glyphicon-star glyphicon-success"></span> 
                                <?php esc_html_e("VALUE BLOG2SOCIAL", "blog2social"); ?> 
                            </div>
                            <p><?php esc_html_e("If you love the plugin and our service, please leave us a 5-star rating to help Blog2Social grow and improve.", "blog2social"); ?></p>
                            <a target="_blank" href="https://wordpress.org/support/plugin/blog2social/reviews/" class="btn btn-success btn-block"><?php esc_html_e("RATE BLOG2SOCIAL", "blog2social") ?></a>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <div class="clearfix"></div>
            <?php if (!B2S_System::isblockedArea('B2S_MENU_MODUL_NEWS_BLOG', B2S_PLUGIN_ADMIN)) { ?> 
                <div class="col-md-12">
                    <div class="row">
                        <br>
                        <hr>
                        <div class="b2s-sidebar-head">
                            <div class="b2s-sidebar-head-text">
                                <span class="glyphicon glyphicon-bullhorn glyphicon-success"></span> <?php esc_html_e("Blog2Social Blog News", "blog2social"); ?> 
                            </div>
                            <p> <ul><?php
                                echo wp_kses(B2S_Notice::getBlogEntries(substr(B2S_LANGUAGE, 0, 2)), array(
                                    'li' => array(),
                                    'div' => array(
                                        'class' => array()
                                    ),
                                    'a' => array(
                                        'target' => array(),
                                        'href' => array(),
                                        'class' => array()
                                    ),
                                    'img' => array(
                                        'src' => array(),
                                        'alt' => array(),
                                        'class' => array()
                                    ),
                                    'span' => array(
                                        'class' => array()
                                    )
                                ));
                                ?></ul></p>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<!-- Sidebar|End-->


