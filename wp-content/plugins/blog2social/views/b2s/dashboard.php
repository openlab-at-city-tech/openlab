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
                                        <a href="https://www.facebook.com/Blog2Social/" target="_blank" rel="nofollow"><img src="<?php echo esc_url(plugins_url('/assets/images/portale/1_flat.png', B2S_PLUGIN_FILE)); ?>" width="28" alt="Facebook"></a>
                                        <a href="https://twitter.com/Blog2Social" target="_blank" rel="nofollow"><img src="<?php echo esc_url(plugins_url('/assets/images/portale/2_flat.png', B2S_PLUGIN_FILE)); ?>" width="28" alt="Twitter"></a>
                                        <a href="https://www.linkedin.com/showcase/blog2social-com/" target="_blank" rel="nofollow"><img src="<?php echo esc_url(plugins_url('/assets/images/portale/3_flat.png', B2S_PLUGIN_FILE)); ?>" width="28" alt="Linkedin"></a>
                                        <a href="https://www.instagram.com/adenion_gmbh/" target="_blank" rel="nofollow"><img src="<?php echo esc_url(plugins_url('/assets/images/portale/12_flat.png', B2S_PLUGIN_FILE)); ?>" width="28" alt="Instagram"></a>
                                        <a href="https://www.pinterest.de/adeniongmbh/" target="_blank" rel="nofollow"><img src="<?php echo esc_url(plugins_url('/assets/images/portale/20_flat.png', B2S_PLUGIN_FILE)); ?>" width="28" alt="Pinterest"></a>
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
<input type="hidden" id="b2s-redirect-url-sched-post" value="<?php echo esc_url($b2sSiteUrl) . 'wp-admin/admin.php?page=blog2social-sched'; ?>"/>
<input type="hidden" id="isLegacyMode" value="<?php echo (isset($b2sGeneralOptions['legacy_mode']) ? (int) esc_attr($b2sGeneralOptions['legacy_mode']) : 0); ?>">
<input type="hidden" id="showFullCalenderText" value="<?php esc_html_e('show full calendar', 'blog2social'); ?>">

<div id="b2s-post-ship-item-post-format-modal" class="modal fade" role="dialog" aria-labelledby="b2s-post-ship-item-post-format-modal" aria-hidden="true" data-backdrop="false" style="display:none;z-index: 1070;">
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

<div id="b2s-network-select-image" class="modal fade" role="dialog" aria-labelledby="b2s-network-select-image" aria-hidden="true" data-backdrop="false" style="display:none;z-index: 1070;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name="#b2s-network-select-image">&times;</button>
                <h4 class="modal-title"><?php esc_html_e('Select image for', 'blog2social') ?> <span class="b2s-selected-network-for-image-info"></span></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="b2s-network-select-image-content"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="b2s-show-post-type-modal" class="modal fade" role="dialog" aria-labelledby="b2s-show-post-type-modal" aria-hidden="true" data-backdrop="false" style="display:none;z-index: 1070;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name="#b2s-show-post-type-modal">&times;</button>
                <h4 class="modal-title"><?php esc_html_e('What would you like to share?', 'blog2social') ?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 col-xs-12">
                        <div class="b2s-post-type-area text-center">
                            <div class="image">
                                <img class="img-width-150" src="<?php echo esc_url(plugins_url('/assets/images/b2s/blog-post-icon.png', B2S_PLUGIN_FILE)); ?>" alt="blog post">
                            </div>
                            <div class="text">
                                <?php esc_html_e("Share your WordPress posts, pages or products", "blog2social") ?>
                            </div>
                            <div class="action">
                                <button class="btn btn-primary" id="b2s-btn-select-blog-post"><?php esc_html_e("select", "blog2social"); ?></button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xs-12">
                        <div class="b2s-post-type-area text-center">
                            <div class="image">
                                <img class="img-width-150" src="<?php echo esc_url(plugins_url('/assets/images/b2s/content-curation-icon.png', B2S_PLUGIN_FILE)); ?>" alt="content curation">
                            </div>
                            <div class="text">
                                <?php esc_html_e("Create or share content from other sources", "blog2social") ?>
                            </div>
                            <div class="action">
                                <button class="btn btn-primary" id="b2s-btn-select-content-curation"><?php esc_html_e("select", "blog2social"); ?></button>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="b2sSelSchedDate" value="">

                </div>
            </div>
        </div>
    </div>
</div>


<div id="b2s-show-post-all-modal" class="modal fade" role="dialog" aria-labelledby="b2s-post-all-modal" aria-hidden="true" data-backdrop="false" style="display:none;z-index: 1070;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name="#b2s-show-post-all-modal">&times;</button>
                <h4 class="modal-title"><?php esc_html_e('Select a post', 'blog2social') ?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="b2s-all-post-content">
                            <div class="b2s-post">
                                <div class="grid-body">
                                    <div class="hidden-lg hidden-md hidden-sm filterShow"><a href="#" onclick="showFilter('show');return false;"><i class="glyphicon glyphicon-chevron-down"></i><?php esc_html_e('filter', 'blog2social') ?></a></div>
                                    <div class="hidden-lg hidden-md hidden-sm filterHide"><a href="#" onclick="showFilter('hide');return false;"><i class="glyphicon glyphicon-chevron-up"></i><?php esc_html_e('filter', 'blog2social') ?></a></div>
                                    <form class="b2sSortForm form-inline pull-left" action="#">
                                        <input id="b2sType" type="hidden" value="all" name="b2sType">
                                        <input id="b2sShowByDate" type="hidden" value="" name="b2sShowByDate">
                                        <input id="b2sPagination" type="hidden" value="1" name="b2sPagination">
                                        <?php
                                        $postFilter = new B2S_Post_Filter('all');
                                        echo wp_kses($postFilter->getItemHtml(), array(
                                                'div' => array(
                                                'class' => array()
                                            ),
                                            'input' => array(
                                                'id' => array(),
                                                'name' => array(),
                                                'class' => array(),
                                                'value' => array(),
                                                'type' => array(),
                                                'placeholder' => array(),
                                            ),
                                            'a' => array(
                                                'href' => array(),
                                                'id' => array(),
                                                'class' => array()
                                            ),
                                            'span' => array(
                                                'class' => array()
                                            ),
                                            'small' => array(),
                                            'select' => array(
                                                'id' => array(),
                                                'name' => array(),
                                                'class' => array()
                                            ),
                                            'option' => array(
                                                'value' => array()
                                            )
                                        ));
                                        ?>
                                    </form>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="b2s-loading-area" style="display:none">
                                <br>
                                <div class="b2s-loader-impulse b2s-loader-impulse-md"></div>
                                <div class="clearfix"></div>
                                <div class="text-center b2s-loader-text"><?php esc_html_e("Loading...", "blog2social"); ?></div>
                            </div>
                            <div class="clearfix"></div>
                            <br>
                            <ul class="list-group b2s-sort-result-item-area"></ul>
                            <br>
                            <nav class="b2s-sort-pagination-area text-center"></nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div id="b2s-sched-post-modal" class="modal fade" role="dialog" aria-labelledby="b2s-sched-post-modal" aria-hidden="true" data-backdrop="false" style="display:none;z-index: 1070;">
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

<div id="b2s-show-error-modal" class="modal fade" role="dialog" aria-labelledby="b2s-show-error-modal" aria-hidden="true" data-backdrop="false" style="display:none;z-index: 1070;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name="#b2s-show-error-modal">&times;</button>
                <h4 class="modal-title"><?php esc_html_e('Notification', 'blog2social') ?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="alert alert-danger b2s-error-text"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>