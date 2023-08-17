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
                                <a class="" href="https://www.blog2social.com" target="_blank">
                                    <img class="img-responsive b2s-img-logo" src="<?php echo esc_url(plugins_url('/assets/images/b2s@64.png', B2S_PLUGIN_FILE)); ?>" alt="logo">
                                </a>
                            </div> 
                            <div class="col-md-10 del-padding-left">
                                <div class="media-body">
                                    <?php if (!B2S_System::isblockedArea('B2S_MENU_ITEM_LOGO', B2S_PLUGIN_ADMIN)) { ?>
                                        <a href="https://www.blog2social.com" class="b2s-btn-logo" target="_blank"><?php esc_html_e("Blog2Social", "blog2social") ?></a> 
                                        <div class="b2s-sidebar-version padding-left-5"><?php echo ($b2sLastVersion !== false) ? esc_html__("Version", "blog2social") . ' ' . B2S_Util::getVersion($b2sLastVersion) : ''; ?> </div>
                                    <?php } ?>
                                    <?php if (!B2S_System::isblockedArea('B2S_MENU_ITEM_LICENSE', B2S_PLUGIN_ADMIN)) { ?> 
                                        <div class="b2s-sidebar-licence padding-left-5"><?php esc_html_e("License", "blog2social") ?>:
                                            <a href="admin.php?page=blog2social-premium" class="b2s-sidebar-btn-licence b2s-key-name">
                                                <?php
                                                $versionType = unserialize(B2S_PLUGIN_VERSION_TYPE);
                                                if (defined("B2S_PLUGIN_TRAIL_END") && strtotime(B2S_PLUGIN_TRAIL_END) > time()) {
                                                    echo 'FREE-TRIAL (' . esc_html($versionType[B2S_PLUGIN_USER_VERSION]) . ')';
                                                } else {
                                                    echo esc_html($versionType[B2S_PLUGIN_USER_VERSION]);
                                                }
                                                ?>
                                            </a>
                                            <?php
                                            if (B2S_PLUGIN_USER_VERSION == 0) {
                                                echo "<br>";
                                                if ((defined("B2S_PLUGIN_TRAIL_END") && strtotime(B2S_PLUGIN_TRAIL_END) < time()) || get_option('B2S_PLUGIN_DISABLE_TRAIL') == true) {
                                                    echo '<a class="btn-link b2s-free-link" target="_blank" href="' . esc_url(B2S_Tools::getSupportLink('affiliate')) . '">' . esc_html__('Upgrade to Premium', 'blog2social') . '</a>';
                                                } else {
                                                    echo '<a class="btn-link b2s-free-link" target="_blank" href="' . esc_url(B2S_Tools::getSupportLink('feature')) . '">' . esc_html__('Start your 30-day free Premium trial', 'blog2social') . '</a>';
                                                }
                                            }
                                            ?>
                                        </div>
                                        <?php if(defined('B2S_PLUGIN_ADDON_VIDEO') && !empty(B2S_PLUGIN_ADDON_VIDEO)){ ?>
                                        <div class="b2s-sidebar-video-addon padding-left-5">
                                            <?php esc_html_e("Addon", "blog2social") ?>: <a href="admin.php?page=blog2social-video" class="b2s-sidebar-btn-video-addon"><?php esc_html_e("Video", "blog2social") ?></a>
                                        </div>
                                    <?php }
                                    }?>
                                </div>                               
                            </div>
                        <?php } ?>           
                    </div>
                </div>
            </div>
            <div class="cleardfix"></div>
            <div class="col-md-12">
                <div class="row">
                    <hr>
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
                                <i class="glyphicon glyphicon-film glyphicon-success"></i> <a href="admin.php?page=blog2social-video" class="b2s-sidebar-menu-item <?php echo (($getPage == 'blog2social-video') ? ' b2s-text-bold' : '') ?>"><?php esc_html_e("Share Videos", "blog2social") ?> <span class="label label-success label-sm"><?php esc_html_e("NEW", "blog2social"); ?></span></a> 
                            </li>
                            <li class="b2s-list-margin-left-10">
                                <i class="glyphicon glyphicon-play glyphicon-success"></i> <a href="admin.php?page=blog2social-autopost" class="b2s-sidebar-menu-item <?php echo (($getPage == 'blog2social-autopost') ? ' b2s-text-bold' : '') ?>"><?php esc_html_e("Auto-Post", "blog2social") ?></a> 
                            </li>
                            <li class="b2s-list-margin-left-10">
                                <i class="glyphicon glyphicon-calendar glyphicon-success"></i> <a href="admin.php?page=blog2social-calendar" class="b2s-sidebar-menu-item <?php echo (($getPage == 'blog2social-calendar') ? ' b2s-text-bold' : '') ?>"><?php esc_html_e("Calendar", "blog2social") ?></a> 
                            </li>
                            <li class="b2s-list-margin-left-10">
                                <i class="glyphicon glyphicon-exclamation-sign glyphicon-success"></i> <a href="admin.php?page=blog2social-notice" class="b2s-sidebar-menu-item <?php echo (($getPage == 'blog2social-notice') ? ' b2s-text-bold' : '') ?>"><?php esc_html_e("Notifications", "blog2social") ?></a> 
                            </li>
                        </ul>
                        <hr>
                        <ul>
                            <?php if ((defined("B2S_PLUGIN_USER_VERSION") && B2S_PLUGIN_USER_VERSION >= 3 && (!defined("B2S_PLUGIN_TRAIL_END") || (defined("B2S_PLUGIN_TRAIL_END") && strtotime(B2S_PLUGIN_TRAIL_END) < time()))) || (defined('B2S_PLUGIN_PERMISSION_INSIGHTS') && B2S_PLUGIN_PERMISSION_INSIGHTS == 1)) { ?>
                                <li class="b2s-list-margin-left-10">
                                    <i class="glyphicon glyphicon-signal glyphicon-success"></i> <a href="admin.php?page=blog2social-metrics" class="b2s-sidebar-menu-item <?php echo (($getPage == 'blog2social-metrics') ? ' b2s-text-bold' : '') ?>"><?php esc_html_e("Social Media Metrics", "blog2social") ?> <span class="label label-success label-sm"><?php esc_html_e("BETA", "blog2social"); ?></span></a> 
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
            <?php if (!B2S_System::isblockedArea('B2S_MENU_MODUL_RATING', B2S_PLUGIN_ADMIN)) { ?> 
                <div class="col-md-12">
                    <div class="row">
                        <hr>
                        <div class="b2s-sidebar-head">
                            <div class="b2s-sidebar-head-text">
                                <span class="glyphicon glyphicon-star glyphicon-success"></span><span class="glyphicon glyphicon-star glyphicon-success"></span><span class="glyphicon glyphicon-star glyphicon-success"></span><span class="glyphicon glyphicon-star glyphicon-success"></span><span class="glyphicon glyphicon-star glyphicon-success"></span> 
                                <?php esc_html_e("Rate it!", "blog2social"); ?> 
                            </div>
                            <p><?php esc_html_e("If you like Blog2Social, we would be greatly delighted, if you could leave us a 5-star rating. If there's something you need assistance with, you can ask all your questions in the Blog2Social support community where you will receive help from our committed support team.", "blog2social"); ?></p>
                            <a target="_blank" href="https://wordpress.org/support/plugin/blog2social/reviews/" class="btn btn-success btn-block"><?php esc_html_e("RATE BLOG2SOCIAL", "blog2social") ?></a>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <div class="cleardfix"></div>
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


