<?php
if (!defined('ABSPATH')) {
    exit;
}
/**
 * @phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
 */

$b2sLastVersion = get_option('b2s_plugin_version');
$customizeArea = B2S_System::customizeArea();
$getPage = (isset($_GET['page']) && !empty($_GET['page'])) ? sanitize_text_field(wp_unslash($_GET['page'])) : '';
?>

<!-- Sidebar|Start -Include-->
<div class="col-md-3 col-xs-12 del-padding-left del-padding-right b2s-sidebar hidden-xs hidden-sm b2s-margin-right-20">
    <!-- Definition of SVG Menu Items-->
    <svg width="0" height="0" style="display:none">
        <defs>
            <symbol id="i-dashboard" viewBox="0 0 24 24">
                <rect x="4" y="4" width="6" height="6" rx="1"></rect><rect x="14" y="4" width="6" height="6" rx="1"></rect>
                <rect x="4" y="14" width="6" height="6" rx="1"></rect><rect x="14" y="14" width="6" height="6" rx="1"></rect>
            </symbol>

            <symbol id="i-send" viewBox="0 0 24 24">
                <path d="M3.5 11.4 20.5 3.5l-6.1 17-3.1-6.1-7.8-3z"></path>
                <path d="m11.3 14.4 4.9-4.9"></path>
            </symbol>

            <symbol id="i-plus" viewBox="0 0 24 24"><path d="M12 6v12M6 12h12"></path></symbol>

            <symbol id="i-calendar" viewBox="0 0 24 24">
                <rect x="4" y="5.5" width="16" height="15" rx="2"></rect>
                <path d="M8 3.5v4M16 3.5v4M4 9.5h16"></path>
            </symbol>

            <symbol id="i-sparkle" viewBox="0 0 24 24">
                <path d="M12 3.5v4M12 16.5v4M3.5 12h4M16.5 12h4"></path>
                <path d="m6.1 6.1 2.8 2.8M15.1 15.1l2.8 2.8M17.9 6.1l-2.8 2.8M8.9 15.1l-2.8 2.8"></path>
                <circle cx="12" cy="12" r="2.2"></circle>
            </symbol>

            <symbol id="i-file" viewBox="0 0 24 24">
                <path d="M6 3.5h8l4 4v13H6z"></path><path d="M14 3.5v4h4"></path>
            </symbol>

            <symbol id="i-star" viewBox="0 0 24 24">
                <path d="m12 3.8 2.55 5.16 5.7.83-4.12 4.02.97 5.68L12 16.8l-5.1 2.69.97-5.68-4.12-4.02 5.7-.83z"></path>
            </symbol>

            <symbol id="i-check" viewBox="0 0 24 24"><path d="m5 12.5 4.1 4.1L19 6.8"></path></symbol>

            <symbol id="i-bell" viewBox="0 0 24 24">
                <path d="M6.5 17.5h11c-1.2-1.3-1.7-2.7-1.7-5.8 0-2.7-1.5-4.6-3.8-5.1V5.5a1 1 0 0 0-2 0v1.1c-2.3.5-3.8 2.4-3.8 5.1 0 3.1-.5 4.5-1.7 5.8z"></path>
                <path d="M10 20h4"></path>
            </symbol>

            <symbol id="i-bolt" viewBox="0 0 24 24">
                <path d="M13.5 2.8 5.8 13h5.5l-.8 8.2L18.2 11h-5.5z"></path>
            </symbol>

            <symbol id="i-repeat" viewBox="0 0 24 24">
                <path d="M7 7h10l-2.7-2.7M17 17H7l2.7 2.7"></path>
                <path d="M17 7a4 4 0 0 1 4 4M7 17a4 4 0 0 1-4-4"></path>
            </symbol>

            <symbol id="i-video" viewBox="0 0 24 24">
                <rect x="3.5" y="6.5" width="13" height="11" rx="2"></rect>
                <path d="m16.5 10 4-2v8l-4-2z"></path>
            </symbol>

            <symbol id="i-robot" viewBox="0 0 24 24">
                <rect x="5" y="7" width="14" height="13" rx="3"></rect>
                <path d="M12 3v4M8.5 13h.01M15.5 13h.01M9 17h6"></path>
                <path d="M5 11H3.5M20.5 11H19"></path>
            </symbol>

            <symbol id="i-chart" viewBox="0 0 24 24">
                <path d="M5 20V12M12 20V7M19 20V4"></path>
            </symbol>

            <symbol id="i-network" viewBox="0 0 24 24">
                <circle cx="6" cy="12" r="2.2"></circle><circle cx="18" cy="6" r="2.2"></circle><circle cx="18" cy="18" r="2.2"></circle>
                <path d="m8 11 7.8-4M8 13l7.8 4"></path>
            </symbol>

            <symbol id="i-settings" viewBox="0 0 24 24">
                <path d="M12 8.7a3.3 3.3 0 1 0 0 6.6 3.3 3.3 0 0 0 0-6.6z"></path>
                <path d="M12 3.5v2M18.01 5.99l-1.42 1.42M20.5 12h-2M18.01 18.01l-1.42-1.42M12 20.5v-2M5.99 18.01l1.42-1.42M3.5 12h2M5.99 5.99l1.42 1.42M12 5.5a6.5 6.5 0 1 1 0 13 6.5 6.5 0 0 1 0-13z"></path>
            </symbol>

            <symbol id="i-help" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="8.5"></circle><path d="M9.7 9.3a2.4 2.4 0 1 1 3.9 1.8c-1.1.8-1.6 1.2-1.6 2.6M12 17.2h.01"></path>
            </symbol>

            <symbol id="i-user" viewBox="0 0 24 24">
                <circle cx="12" cy="7.5" r="3"></circle><path d="M5.5 20c.4-3.5 2.5-5.5 6.5-5.5s6.1 2 6.5 5.5"></path>
            </symbol>

            <symbol id="i-license" viewBox="0 0 24 24">
                <rect x="3.5" y="5" width="17" height="14" rx="2"></rect>
                <path d="M3.5 9h17"></path>
            </symbol>

            <symbol id="i-logo" viewBox="0 0 24 24">
                <rect x="2" y="2" width="20" height="20" rx="6" fill="currentColor" stroke="none"></rect>
                <path d="M15.8 8.3a5.3 5.3 0 1 0 0 7.5M15.8 8.3v3.1h-3.1" fill="none" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path>
            </symbol>
        </defs>
    </svg>
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
                                    <img class="img-responsive b2s-img-logo" src="<?php echo esc_url(plugins_url('/assets/images/b2s_64.png', B2S_PLUGIN_FILE)); ?>" alt="logo">
                                </a>
                            </div> 
                            <div class="col-md-10 del-padding-left">
                                <div class="media-body">
                                    <?php if (!B2S_System::isblockedArea('B2S_MENU_ITEM_LOGO', B2S_PLUGIN_ADMIN)) { ?>
                                        <a href="admin.php?page=blog2social" class="b2s-btn-logo"><?php esc_html_e("Blog2Social", "blog2social") ?></a> 
                                        <div class="b2s-sidebar-version padding-left-5"><?php echo ($b2sLastVersion !== false) ? esc_html__("Version", "blog2social") . ' ' . esc_html(B2S_Util::getVersion($b2sLastVersion)) : ''; ?> </div>
                                    <?php } ?>
                                </div>                               
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <?php if (!B2S_System::isblockedArea('B2S_MENU_ITEM_LICENSE', B2S_PLUGIN_ADMIN)) { ?> 
                    <div class="row">
                        <div class="panel panel-default b2s-margin-right-10 b2s-margin-bottom-10 b2s-margin-top-8 license-card">
                            <div class="panel-body b2s-padding-5">
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
                                                echo '<a class="btn-link b2s-free-link padding-left-5" target="_blank" href="' . esc_url(B2S_Tools::getSupportLink('upgrade_version')) . '">' . esc_html__('Upgrade to Premium', 'blog2social') . '</a>';
                                            } else {
                                                echo '<br><a class="btn-link b2s-free-link padding-left-16" target="_blank" href="' . esc_url(B2S_Tools::getSupportLink('trial')) . '">' . esc_html__('Start your 30-day free Premium trial', 'blog2social') . '</a>';
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
                                    $cond = get_option('B2S_PLUGIN_USER_VERSION_' . B2S_PLUGIN_BLOG_USER_ID);
                                    //COND: All Network-Integration by licence
                                    if ($cond !== false && is_array($cond) && !empty($cond) && isset($cond['B2S_PLUGIN_LICENCE_CONDITION'])) {
                                        $licenceCond = $cond['B2S_PLUGIN_LICENCE_CONDITION'];
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
                                                <?php $linkRouting = ((defined("B2S_PLUGIN_TRAIL_END") && strtotime(B2S_PLUGIN_TRAIL_END) > time()) || (B2S_PLUGIN_USER_VERSION == 0)) ? 'upgrade_version' : 'addon_post_volume'; ?>
                                                <span class="b2s-span-float-right"><a target="_blank" href="<?php echo esc_url(B2S_Tools::getSupportLink($linkRouting)); ?>"><?php esc_html_e("Need more?", "blog2social") ?></a></span>
                                                <div class="clearfix"></div>
                                            </div>
                                            <?php
                                        }

                                        if (isset($licenceCond['open_daily_post_quota'])) {
                                            ?>                                       
                                            <input type="hidden" id="current_licence_open_daily_post_quota" name="current_licence_open_daily_post_quota" value="<?php echo esc_attr($licenceCond['open_daily_post_quota']); ?>" />
                                            <?php
                                            $dailyLimit = ((int) $licenceCond['open_daily_post_quota'] <= 0) ? '' : 'b2s-info-display-none';
                                            ?>
                                            <h3 class="b2s-h3 b2s-current-licence-open-daily-post-quota-sidebar-info b2s-color-red b2s-margin-0 b2s-text-underline <?php echo esc_html($dailyLimit); ?> b2s-text-bold"><?php echo esc_html(sprintf(
                                                // translators: %s post limit number
                                                __('Daily Limit of %d posts reached!', 'blog2social'), esc_html($licenceCond['total_daily_post_quota']))); ?></h3>
                                            <?php
                                        }
                                    }

                                    //Cond: Network ADD X-Integration
                                    if ($cond !== false && is_array($cond) && !empty($cond) && isset($cond['B2S_PLUGIN_NETWORK_CONDITION'][45]) && !empty($cond['B2S_PLUGIN_NETWORK_CONDITION'][45])) {
                                        $networkCond = $cond['B2S_PLUGIN_NETWORK_CONDITION'][45];
                                        $openNetCond = $networkCond->open_sched_post_quota;
                                        $totalNetCond = $networkCond->total_sched_post_quota;
                                        ?>
                                        <br><h3 class="b2s-h3 b2s-stats-h3"><?php esc_html_e("Your monthly X post volume", "blog2social") ?></h3>                                     

                                        <?php
                                        echo wp_kses(B2S_Notice::getPostStats($openNetCond, $totalNetCond), array(
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
                                            <span class="b2s-span-float-left"><span id="current_network_open_sched_post_quota" class="b2s-text-bold"><?php echo (int) $openNetCond ?></span> <?php esc_html_e("remaining from", "blog2social") ?> <?php echo (int) $totalNetCond; ?></span>
                                            <span class="b2s-span-float-right"><a target="_blank" href="<?php echo esc_url(B2S_Tools::getSupportLink('addon_network_integration')); ?>"><?php esc_html_e("Need more?", "blog2social") ?></a></span>
                                            <div class="clearfix"></div>
                                        </div>

                                        <?php
                                        if (isset($networkCond->open_daily_post_quota)) {
                                            ?>
                                            <input type="hidden" id="current_network_open_daily_post_quota" name="current_network_open_daily_post_quota" value="<?php echo esc_attr($networkCond->open_daily_post_quota); ?>" />
                                            <?php
                                            $dailyLimit = ((int) $networkCond->open_daily_post_quota <= 0) ? '' : 'b2s-info-display-none';
                                            ?>
                                            <h3 class="b2s-h3 b2s-current-network-open-daily-post-quota-sidebar-info b2s-color-red b2s-margin-0 b2s-text-underline <?php echo esc_html($dailyLimit); ?> b2s-text-bold"><?php echo esc_html(sprintf(
                                                // translators: %s is dayly post limit
                                                __('Daily Limit of %d X posts reached!', 'blog2social'), esc_html($networkCond->total_daily_post_quota))); ?></h3>
                                            <?php
                                        }
                                    }
                                    ?>
                                    <div class="clearfix"></div>                                   
                                </div>
                            </div>
                        </div>
                        <div class="b2s-ass-sidebar-account panel panel-default b2s-margin-right-10 b2s-margin-bottom-10 b2s-margin-top-8 license-card" style="display:none;">
                            <div class="b2s-ass-sidebar-account-container b2s-margin-right-10 b2s-margin-bottom-10">
                                <div class="panel-body b2s-padding-10">
                                    <div class="media d-flex align">
                                        <div class="align-self-center">
                                            <img class="float-left" style="margin-top:-4px;" src="<?php echo esc_url(plugins_url('/assets/images/ass/assistini-logo-face-small.png', B2S_PLUGIN_FILE)); ?>" alt="Assistini">
                                            <span class="b2s-sidebar-ass-title"><?php esc_html_e("Assistini AI", "blog2social") ?></span>
                                            <button id="b2s-sidebar-ship-ass-logout-btn" class="pull-right btn-link b2s-p-0"><?php esc_html_e("log out", "blog2social") ?></button>
                                            <hr class="b2s-margin-bottom-10">
                                            <div class="media-body b2s-font-size-11">
                                                <span id="b2s-sidebar-ship-ass-words" class="b2s-span-float-left"><span id="sidebar_ship_ass_words_open" class="b2s-text-bold">0</span> <?php esc_html_e(" / ", "blog2social") ?> <span id="sidebar_ship_ass_words_total" class="b2s-text-bold">0</span> <?php esc_html_e("words", "blog2social"); ?></span>
                                                <span id="b2s-sidebar-ship-ass-account" class="b2s-span-float-right"><a target="_blank" href="<?php echo esc_url(B2S_Tools::getSupportLink('ass_account')); ?>"><?php esc_html_e("Manage Account", "blog2social") ?></a></span>
                                                <div class="clearfix"></div>
                                            </div>
                                        </div>
                                    </div>
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
                        <div class="b2s-menu-section">
                            <div class="b2s-new-sidebar-head-text">
                                <?php esc_html_e("Create & Share", "blog2social") ?>
                            </div>
                            <div class="b2s-menu-group">
                                <div class="b2s-menu-parent b2s-menu-parent--open">
                                    <svg width="0" height="0" class="b2s-svg-menu-icon" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><use href="#i-send"></use></svg>
                                    <span class="b2s-menu-parent__label"><?php esc_html_e("Social Media Posts", "blog2social") ?></span>
                                    <span class="b2s-menu-parent__chevron"><?php echo "⌃"; ?></span>
                                </div>
                                <div class="b2s-submenu">
                                    <a href="admin.php?page=blog2social-post"  class="b2s-submenu-item <?php echo (($getPage == 'blog2social-post') ? 'b2s-menu-item--active' : '') ?>"><svg width="0" height="0" fill="none" class="b2s-svg-menu-icon" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><use href="#i-plus"></use></svg><?php esc_html_e("Share Posts", "blog2social") ?></a>
                                    <a href="admin.php?page=blog2social-sched" class="b2s-submenu-item <?php echo (($getPage == 'blog2social-sched') ? 'b2s-menu-item--active' : '') ?>"><svg width="0" height="0" fill="none" class="b2s-svg-menu-icon" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><use href="#i-calendar"></use></svg><?php esc_html_e("Scheduled Posts", "blog2social") ?></a>
                                    <a href="admin.php?page=blog2social-approve" class="b2s-submenu-item <?php echo (($getPage == 'blog2social-approve') ? 'b2s-menu-item--active' : '') ?>"><svg width="0" height="0" fill="none" class="b2s-svg-menu-icon" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><use href="#i-sparkle"></use></svg><?php esc_html_e("Instant Sharing", "blog2social") ?></a>
                                    <a href="admin.php?page=blog2social-draft-post" class="b2s-submenu-item <?php echo (($getPage == 'blog2social-draft-post') ? 'b2s-menu-item--active' : '') ?>"><svg width="0" height="0" fill="none" class="b2s-svg-menu-icon" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><use href="#i-file"></use></svg><?php esc_html_e("Drafts", "blog2social") ?></a>
                                    <a href="admin.php?page=blog2social-favorites" class="b2s-submenu-item <?php echo (($getPage == 'blog2social-favorites') ? 'b2s-menu-item--active' : '') ?>"><svg width="0" height="0" fill="none" class="b2s-svg-menu-icon" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><use href="#i-star"></use></svg><?php esc_html_e("Favorites", "blog2social") ?></a>
                                    <a href="admin.php?page=blog2social-publish" class="b2s-submenu-item <?php echo (($getPage == 'blog2social-publish') ? 'b2s-menu-item--active' : '') ?>"><svg width="0" height="0" fill="none" class="b2s-svg-menu-icon" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><use href="#i-check"></use></svg><?php esc_html_e("Shared Posts", "blog2social") ?></a>
                                    <a href="admin.php?page=blog2social-notice" class="b2s-submenu-item <?php echo (($getPage == 'blog2social-notice') ? 'b2s-menu-item--active' : '') ?>">
                                    <svg width="0" height="0" class="b2s-svg-menu-icon" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><use href="#i-bell"></use></svg>
                                    <?php
                                        global $wpdb;
                                        $sql = "SELECT COUNT(posts.`post_id`) FROM `{$wpdb->prefix}b2s_posts` posts WHERE (posts.`sched_date` = '0000-00-00 00:00:00' OR (posts.`sched_type` = 3 AND posts.`publish_date` != '0000-00-00 00:00:00')) AND posts.`post_for_approve`= 0  AND posts.`publish_error_code` != '' AND posts.`hide` = 0";
                                        //No unprepared User Input
                                        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
                                        $res = $wpdb->get_var($sql);
                                    ?>
                                    <span><?php esc_html_e("Notifications", "blog2social") ?></span>
                                    <?php echo ($res > 0 ?'<span class="badge badge--red">' . esc_html($res) . '</span>' : ''); ?>
                                    </a>
                                </div>
                            </div>
                            <a href="admin.php?page=blog2social-autopost" class="b2s-menu-item <?php echo (($getPage == 'blog2social-autopost') ? ' b2s-menu-item--active' : '') ?>">
                            <svg width="0" height="0" fill="none" class="b2s-svg-menu-icon" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><use href="#i-bolt"></use></svg>
                            <span class="b2s-menu-item__label"><?php esc_html_e("Auto Posting", "blog2social") ?></span>
                            </a>
                            <a href="admin.php?page=blog2social-repost" class="b2s-menu-item <?php echo (($getPage == 'blog2social-repost') ? ' b2s-menu-item--active' : '') ?>">
                            <svg width="0" height="0" fill="none" class="b2s-svg-menu-icon" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><use href="#i-repeat"></use></svg>
                            <span class="b2s-menu-item__label"><?php esc_html_e("Re-Sharer", "blog2social") ?></span>
                            </a>
                            <a href="admin.php?page=blog2social-calendar" class="b2s-menu-item <?php echo (($getPage == 'blog2social-calendar') ? ' b2s-menu-item--active' : '') ?>">
                            <svg width="0" height="0" fill="none" class="b2s-svg-menu-icon" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><use href="#i-calendar"></use></svg>
                            <span class="b2s-menu-item__label"><?php esc_html_e("Calendar", "blog2social") ?></span>
                            </a>
                            <a href="admin.php?page=blog2social-video" class="b2s-menu-item <?php echo (($getPage == 'blog2social-video') ? ' b2s-menu-item--active' : '') ?>">
                            <svg width="0" height="0" fill="none" class="b2s-svg-menu-icon" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><use href="#i-video"></use></svg>                            <span class="b2s-menu-item__label"><?php esc_html_e("Share Videos", "blog2social") ?></span>
                            </a>
                        </div>
                        <div class="b2s-menu-section">
                            <div class="b2s-new-sidebar-head-text"><?php esc_html_e("Analysis & Optimization", "blog2social") ?></div>
                            <a href="admin.php?page=blog2social-ai-content-creator" class="b2s-menu-item <?php echo (($getPage == 'blog2social-ai-content-creator') ? ' b2s-menu-item--active' : '') ?>">
                                <svg width="0" height="0" fill="none" class="b2s-svg-menu-icon" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><use href="#i-robot"></use></svg>
                                <span class="b2s-menu-item__label"><?php esc_html_e("AI Assistant", "blog2social") ?> </span>
                                <span class="badge badge--green"><?php esc_html_e("NEW", "blog2social"); ?></span>
                            </a>
                            <?php if ((defined("B2S_PLUGIN_USER_VERSION") && B2S_PLUGIN_USER_VERSION >= 3 && (!defined("B2S_PLUGIN_TRAIL_END") || (defined("B2S_PLUGIN_TRAIL_END") && strtotime(B2S_PLUGIN_TRAIL_END) < time()))) || (defined('B2S_PLUGIN_PERMISSION_INSIGHTS') && B2S_PLUGIN_PERMISSION_INSIGHTS == 1)) { ?>
                            <a href="admin.php?page=blog2social-metrics" class="b2s-menu-item b2s-menu-item--two-line">
                                <svg width="0" height="0" fill="none" class="b2s-svg-menu-icon" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><use href="#i-chart"></use></svg>
                                <span class="b2s-menu-item__label"><?php esc_html_e("Social Media Metrics", "blog2social") ?> </span>
                                <span class="badge badge--orange"><?php esc_html_e("BETA", "blog2social"); ?></span>
                            </a>    
                            <?php } ?>
                        </div>
                        <div class="b2s-menu-section">
                            <div class="b2s-new-sidebar-head-text"><?php esc_html_e("Administration & Settings", "blog2social") ?></div>
                            <a class="b2s-menu-item <?php echo (($getPage == 'blog2social') ? 'b2s-menu-item--active' : '') ?>" href="admin.php?page=blog2social">
                                <svg width="0" height="0" fill="none" class="b2s-svg-menu-icon" stroke="currentColor" stroke-width="1.6"><use href="#i-dashboard"></use></svg>
                                <span class="b2s-menu-item__label"><?php esc_html_e("Dashboard", "blog2social") ?></span>
                            </a>
                            <a href="admin.php?page=blog2social-network" class="b2s-menu-item <?php echo (($getPage == 'blog2social-network') ? ' b2s-menu-item--active' : '') ?>">
                                <svg width="0" height="0" fill="none" class="b2s-svg-menu-icon" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><use href="#i-network"></use></svg>
                                <span class="b2s-menu-item__label"><?php esc_html_e("Networks", "blog2social") ?></span>
                            </a>
                            <a href="admin.php?page=blog2social-settings" class="b2s-menu-item <?php echo (($getPage == 'blog2social-settings') ? ' b2s-menu-item--active' : '') ?>">
                                <svg width="0" height="0" fill="none" class="b2s-svg-menu-icon" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><use href="#i-settings"></use></svg>
                                <span class="b2s-menu-item__label"><?php esc_html_e("Settings", "blog2social") ?></span>
                            </a>
                            <a href="admin.php?page=blog2social-support" class="b2s-menu-item <?php echo (($getPage == 'blog2social-support') ? ' b2s-menu-item--active' : '') ?>">
                                <svg width="0" height="0" fill="none" class="b2s-svg-menu-icon" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><use href="#i-help"></use></svg>
                                <span class="b2s-menu-item__label"><?php esc_html_e("Help & Support", "blog2social") ?></span>
                            </a> 
                            <?php if (!B2S_System::isblockedArea('B2S_MENU_ITEM_PLANS', B2S_PLUGIN_ADMIN)) { ?> 
                                <a href="<?php echo esc_url(B2S_Tools::getSupportLink('login')); ?>" class="b2s-menu-item">
                                <svg width="0" height="0" fill="none" class="b2s-svg-menu-icon" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><use href="#i-robot"></use></svg>
                                <span class="b2s-menu-item__label"><?php esc_html_e("My Account", "blog2social") ?></span>
                                </a>
                            <?php } ?>                      
                            <?php if (!B2S_System::isblockedArea('B2S_MENU_ITEM_LICENSE', B2S_PLUGIN_ADMIN)) { ?> 
                                <a href="admin.php?page=blog2social-premium" class="b2s-menu-item b2s-menu-item--upgrade <?php echo (($getPage == 'blog2social-premium') ? ' b2s-menu-item--active' : '') ?>">
                                <svg width="0" height="0" fill="none" class="b2s-svg-menu-icon" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><use href="#i-license"></use></svg>
                                <span class="b2s-menu-item__label"><?php esc_html_e("Upgrade License", "blog2social") ?></span>
                                </a>
                            <?php } ?>
                        </div>
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
            <?php if (!B2S_System::isblockedArea('B2S_MENU_MODUL_NEWS_BLOG', B2S_PLUGIN_ADMIN)) { ?> 
                <div class="col-md-12">
                    <div class="row">
                        <br>
                        <hr>
                        <?php 
                            $entries= B2S_Notice::getBlogEntries(substr(B2S_LANGUAGE, 0, 2));
                            if(isset($entries) && !empty($entries)) { 
                            ?>
                            <div class="b2s-sidebar-head">
                                <div class="b2s-new-sidebar-head-text">
                                    <span class="glyphicon glyphicon-bullhorn glyphicon-success"></span> <?php esc_html_e("Blog2Social Blog News", "blog2social"); ?> 
                                </div>
                                <p> <ul><?php
                                    echo wp_kses($entries, array(
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
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<!-- Sidebar|End-->

