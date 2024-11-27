<?php
$customizeArea = B2S_System::customizeArea();
$isVideo = (isset($_GET['isVideo']) && (int) $_GET['isVideo'] == 1) ? true : false;
?>
<!-- Sidebar|Start -Include-->
<div class="col-md-3 del-padding-left hidden-xs hidden-sm">
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
                                    if (!$isVideo) {

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
                                    }
                                    ?>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>

                        <div class="b2s-ass-sidebar-account panel panel-default b2s-margin-right-10 b2s-margin-bottom-10" style="display:none;">
                            <div class="panel-body b2s-padding-10">
                                <div class="media d-flex align">
                                    <div class="align-self-center">
                                        <img class="float-left" style="margin-top:-4px;" src="<?php echo esc_url(plugins_url('/assets/images/ass/assistini-logo-face-small.png', B2S_PLUGIN_FILE)); ?>" alt="Assistini">
                                        <span class="b2s-sidebar-ass-title"><?php esc_html_e("Assistini AI", "blog2social") ?></span>
                                        <button id="b2s-sidebar-ship-ass-logout-btn" class="pull-right btn-link b2s-p-0"><?php esc_html_e("log out", "blog2social") ?></button>
                                        <hr class="b2s-margin-bottom-10">
                                        <div class="media-body b2s-font-size-11">
                                            <span id="b2s-sidebar-ship-ass-words" class="b2s-span-float-left"><span id="sidebar_ship_ass_words_open" class="b2s-text-bold">0</span> <?php esc_html_e("remaining from", "blog2social") ?> <span id="sidebar_ship_ass_words_total" class="b2s-text-bold">0</span> <?php esc_html_e("words", "blog2social"); ?></span>
                                            <span id="b2s-sidebar-ship-ass-account" class="b2s-span-float-right"><a target="_blank" href="<?php echo esc_url(B2S_Tools::getSupportLink('ass_account')); ?>"><?php esc_html_e("Manage Account", "blog2social") ?></a></span>
                                            <div class="clearfix"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                <?php } ?>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>