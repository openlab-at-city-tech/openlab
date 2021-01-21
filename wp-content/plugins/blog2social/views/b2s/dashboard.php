<?php
wp_nonce_field('b2s_security_nonce', 'b2s_security_nonce');
$b2sSiteUrl = get_option('siteurl') . ((substr(get_option('siteurl'), -1, 1) == '/') ? '' : '/');
$b2sGeneralOptions = get_option('B2S_PLUGIN_GENERAL_OPTIONS');
?>
<div class="b2s-container">
    <div class="b2s-inbox">
        <div class="col-md-12 del-padding-left">
            <div class="col-md-9 del-padding-left del-padding-right">
                <!--Header|Start - Include-->
                <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/html/header.php'); ?>
                <!--Header|End-->
                <div class="clearfix"></div>
                <?php if (!B2S_System::isblockedArea('B2S_DASHBOARD_MODUL_NEWS', B2S_PLUGIN_ADMIN)) { ?>
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/widgets/content.php'); ?>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                <?php } ?>
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="b2s-dashboard-h4"><?php esc_html_e("Your Activity", "blog2social"); ?></h4>  
                            </div>
                            <div class="col-md-6 add-margin-top-10">
                                <div class="btn-group pull-right">
                                    <button type="button" class="btn btn-primary" data-toggle="tab" href="#tab1"><?php esc_html_e("Calendar", "blog2social"); ?></button>
                                    <button type="button" class="btn btn-primary" data-toggle="tab" href="#tab2"><?php esc_html_e("List", "blog2social"); ?></button>
                                    <button type="button" class="btn btn-primary" data-toggle="tab" href="#tab3"><?php esc_html_e("Chart", "blog2social"); ?></button>
                                </div>
                            </div>
                        </div>
                        <div class="tab-content">
                            <div id="tab1" class="tab-pane active fade in">
                                <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/widgets/calendar.php'); ?>
                            </div>
                            <div id="tab2" class="tab-pane fade">
                                <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/widgets/posts.php'); ?>
                            </div>
                            <div id="tab3" class="tab-pane fade">
                                <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/widgets/activity.php'); ?>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <?php if (!B2S_System::isblockedArea('B2S_DASHBOARD_MODUL_NEWS_EMAIL ', B2S_PLUGIN_ADMIN)) { ?>
                    <div class="panel panel-default hidden-xs hidden-sm">
                        <div class="panel-body">
                            <div class="col-md-8">
                                <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/widgets/newsletter.php'); ?>
                            </div>
                            <div class="col-md-4">
                                <div class="pull-right">
                                    <div class="form-inline">
                                        <label class="b2s-text-xl b2s-color-grey"><?php esc_html_e("Follow us", "blog2social") ?></label>
                                        <a href="https://www.facebook.com/Blog2Social/" target="_blank" rel="nofollow"><img src="<?php echo plugins_url('/assets/images/portale/1_flat.png', B2S_PLUGIN_FILE); ?>" width="28" alt="Facebook"></a>
                                        <a href="https://twitter.com/Blog2Social" target="_blank" rel="nofollow"><img src="<?php echo plugins_url('/assets/images/portale/2_flat.png', B2S_PLUGIN_FILE); ?>" width="28" alt="Twitter"></a>
                                        <a href="https://www.linkedin.com/showcase/blog2social-com/" target="_blank" rel="nofollow"><img src="<?php echo plugins_url('/assets/images/portale/3_flat.png', B2S_PLUGIN_FILE); ?>" width="28" alt="Linkedin"></a>
                                        <a href="https://www.instagram.com/adenion_gmbh/" target="_blank" rel="nofollow"><img src="<?php echo plugins_url('/assets/images/portale/12_flat.png', B2S_PLUGIN_FILE); ?>" width="28" alt="Instagram"></a>
                                        <a href="https://www.pinterest.de/adeniongmbh/" target="_blank" rel="nofollow"><img src="<?php echo plugins_url('/assets/images/portale/20_flat.png', B2S_PLUGIN_FILE); ?>" width="28" alt="Pinterest"></a>
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
<input type="hidden" id="b2s-redirect-url-sched-post" value="<?php echo $b2sSiteUrl . 'wp-admin/admin.php?page=blog2social-sched'; ?>"/>
<input type="hidden" id="isLegacyMode" value="<?php echo (isset($b2sGeneralOptions['legacy_mode']) ? (int) esc_attr($b2sGeneralOptions['legacy_mode']) : 0); ?>">
<input type="hidden" id="showFullCalenderText" value="<?php esc_html_e('show full calendar', 'blog2social'); ?>">
