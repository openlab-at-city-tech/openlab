<?php wp_nonce_field('b2s_security_nonce', 'b2s_security_nonce'); ?>
<div class="b2s-container">
    <div class="b2s-inbox">
        <div class="col-md-12 del-padding-left">
            <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/html/sidebar.php'); ?>
            <div class="col-md-9 del-padding-left del-padding-right">
                <!--Header|Start - Include-->
                <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/html/header.php'); ?>
                <!--Header|End-->
                <div class="clearfix"></div>
                <!--Content|Start-->
                <div class="panel panel-default">
                    <div class="panel-body">
                        <?php if (!B2S_System::isblockedArea('B2S_LICENSE_MODUL_EDIT', B2S_PLUGIN_ADMIN)) { ?> 
                            <h2 class="b2s-premium-h2"><?php esc_html_e('Your current license:', 'blog2social') ?>
                                <span class="b2s-key-name">
                                    <?php
                                    $versionType = unserialize(B2S_PLUGIN_VERSION_TYPE);
                                    if (defined("B2S_PLUGIN_TRAIL_END") && strtotime(B2S_PLUGIN_TRAIL_END) > time()) {
                                        echo 'FREE-TRIAL (' . esc_html($versionType[B2S_PLUGIN_USER_VERSION]) . ')';
                                    } else {
                                        echo esc_html($versionType[B2S_PLUGIN_USER_VERSION]);
                                    }
                                    ?>
                                </span>
                                <?php if (B2S_PLUGIN_USER_VERSION == 0 && !defined("B2S_PLUGIN_TRAIL_END") && !get_option('B2S_PLUGIN_DISABLE_TRAIL')) { ?>
                                    <a class="btn btn-sm btn-primary pull-right" href="<?php echo esc_url(B2S_Tools::getSupportLink('feature')); ?>" target="_blank">   <?php esc_html_e('Start your 30-day free Premium trial', 'blog2social') ?></a>
                                <?php } ?>  
                            </h2>
                            <?php if (defined("B2S_PLUGIN_TRAIL_END") && strtotime(B2S_PLUGIN_TRAIL_END) > time()) { ?>
                                <p> <span class="b2s-text-bold"><?php esc_html_e("End of Trial", "blog2social") ?></span>: <?php echo esc_html(B2S_Util::getCustomDateFormat(B2S_PLUGIN_TRAIL_END, trim(strtolower(substr(B2S_LANGUAGE, 0, 2))), false)); ?> 
                                    <a class="b2s-text-bold" href="<?php echo esc_url(B2S_Tools::getSupportLink('affiliate')); ?>" target="_blank">   <?php esc_html_e('Upgrade', 'blog2social') ?></a>
                                </p>
                                <br>
                            <?php } ?>
                            <p><?php esc_html_e('Use Blog2Social Premium for even smarter social media automation: schedule your posts automatically with the Best Time Manager, choose specific dates or schedule your posts recurringly. Keep track of your posts with the social media calendar. Publish posts to pages, groups and multiple accounts per network and much more.', 'blog2social') ?>
                                <a target="_blank" class="b2s-btn-link" href="<?php echo esc_url(B2S_Tools::getSupportLink('affiliate')); ?>"><?php esc_html_e('Get more information about the benefits of Blog2Social Premium', 'blog2social') ?></a></p>
                            <div class="clearfix"></div>
                            <br>
                            <div class="b2s-key-area">
                                <div id="b2s-license-user-area" class="col-md-4 col-sm-12 col-xs-12">
                                    <select id="b2s-license-user-select" class="form-control" data-placeholder="<?php esc_html_e('Select a user', 'blog2social'); ?>">
                                        <?php echo wp_kses(B2S_Tools::searchUser(wp_get_current_user()->display_name, B2S_PLUGIN_BLOG_USER_ID), array(
                                            'option' => array(
                                                'value' => array(),
                                                'selected' => array()
                                            )
                                        )); ?>
                                    </select>
                                    <input type="hidden" id="b2s-license-user" value="<?php echo esc_attr(get_current_user_id()); ?>">
                                    <input type="hidden" id="b2s-no-user-found" value="<?php esc_html_e('No User found', 'blog2social'); ?>">
                                </div>
                                <div class="input-group col-md-8 col-sm-12 col-xs-12">
                                    <input class="form-control input-sm b2s-key-area-input" placeholder="<?php esc_html_e('Enter license key and change your version', 'blog2social'); ?>" value="" type="text">
                                    <span class="input-group-btn">
                                        <button class="btn btn-primary btn-sm b2s-key-area-btn-submit"><?php esc_html_e('Activate License', 'blog2social'); ?></button>
                                    </span>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <br>
                            <hr class="b2s-premium-line">
                        <?php } ?>
                        <div class="clearfix"></div>
                        <?php if (!B2S_System::isblockedArea('B2S_LICENSE_MODUL_FEATURE', B2S_PLUGIN_ADMIN)) { ?>
                            <h2 class="b2s-premium-go-to-text">
                                <?php esc_html_e('Go Premium and get even smarter with social media automation', 'blog2social') ?>
                            </h2>
                            <div class="col-lg-10 col-lg-offset-1 col-xs-12 col-xs-offset-0">
                                <div class="row">
                                    <div class="col-md-3 col-hide-padding-left">
                                        <div class="thumbnail text-center">
                                            <img class="b2s-feature-img-with-90" src="<?php echo esc_url(plugins_url('/assets/images/features/automation.png', B2S_PLUGIN_FILE)); ?>" alt="Social Media Calendar">
                                        </div>
                                        <p class="text-center">
                                            <span class="b2s-text-bold"><?php esc_html_e('Auto Posting', 'blog2social') ?></span><br>
                                            <?php esc_html_e('Automatically share posts at the time of publishing or at any scheduled time.', 'blog2social') ?>
                                        </p>
                                    </div>
                                    <div class="col-md-3 col-hide-padding-left">
                                        <div class="thumbnail text-center">
                                            <img class="b2s-feature-img-with-90" src="<?php echo esc_url(plugins_url('/assets/images/features/socialmediaposts.png', B2S_PLUGIN_FILE)); ?>" alt="Social Media Posts">
                                        </div>
                                        <p class="text-center">
                                            <span class="b2s-text-bold"><?php esc_html_e('Social Media Posts', 'blog2social') ?></span><br>
                                            <?php esc_html_e('Create, schedule and share social media posts from any text, link, image, video or RSS feed.', 'blog2social') ?>
                                        </p>
                                    </div>
                                    <div class="col-md-3 col-hide-padding-left">
                                        <div class="thumbnail text-center">
                                            <img class="b2s-feature-img-with-90" src="<?php echo esc_url(plugins_url('/assets/images/features/video-function.png', B2S_PLUGIN_FILE)); ?>" alt="Pages & Groups">
                                        </div>
                                        <p class="text-center">
                                            <span class="b2s-text-bold"><?php esc_html_e('Publish and share Video files', 'blog2social') ?></span><br>
                                            <?php esc_html_e('Publish and share your video content straight from your media library.', 'blog2social') ?>
                                        </p>
                                    </div>
                                    <div class="col-md-3 col-hide-padding-left">
                                        <div class="thumbnail text-center">
                                            <img class="b2s-feature-img-with-90" src="<?php echo esc_url(plugins_url('/assets/images/features/best-time-scheduling.png', B2S_PLUGIN_FILE)); ?>" alt="Best Time Manager">
                                        </div>
                                        <p class="text-center">
                                            <span class="b2s-text-bold"><?php esc_html_e('Best Time Manager', 'blog2social') ?></span><br>
                                            <?php esc_html_e('Schedule your social media posts with pre-defined best-times or at your own time settings for auto-scheduling.', 'blog2social') ?>
                                        </p>
                                    </div>


                                   
                                </div>
                                <div class="clearfix"></div>
                                <div class="row">
                                    <div class="col-md-3 col-hide-padding-left">
                                        <div class="thumbnail text-center">
                                            <img class="b2s-feature-img-with-90" src="<?php echo esc_url(plugins_url('/assets/images/features/calendar.png', B2S_PLUGIN_FILE)); ?>" alt="Auto Posting">
                                        </div>
                                        <p class="text-center">
                                            <span class="b2s-text-bold"><?php esc_html_e('Social Media Calendar', 'blog2social') ?></span><br>
                                            <?php esc_html_e('Keep track of your scheduled social media posts. Add or edit your posts or change the date per drag & drop.', 'blog2social') ?>
                                        </p>
                                    </div>
                                    <div class="col-md-3 col-hide-padding-left">
                                        <div class="thumbnail text-center">
                                            <img class="b2s-feature-img-with-90" src="<?php echo esc_url(plugins_url('/assets/images/features/re-share-posts.png', B2S_PLUGIN_FILE)); ?>" alt="RSS Feed">
                                        </div>
                                        <p class="text-center">
                                            <span class="b2s-text-bold"><?php esc_html_e('Re-share Posts Automatically', 'blog2social') ?></span><br>
                                            <?php esc_html_e('Repeat your best posts automatically to revive your evergreen content from time to time.', 'blog2social') ?>
                                        </p>
                                    </div>
                                    <div class="col-md-3 col-hide-padding-left">
                                        <div class="thumbnail text-center">
                                            <img class="b2s-feature-img-with-90" src="<?php echo esc_url(plugins_url('/assets/images/features/images-posts-formats.png', B2S_PLUGIN_FILE)); ?>" alt="Content Curation">
                                        </div>
                                        <p class="text-center">
                                            <span class="b2s-text-bold"><?php esc_html_e('Select and edit images', 'blog2social') ?></span><br>
                                            <?php esc_html_e('Select individual or multiple images. Easily edit, crop, rotate or flip images to adapt the format for selected social platforms.', 'blog2social') ?>
                                        </p>
                                    </div>
                                    <div class="col-md-3  col-hide-padding-left">
                                        <div class="thumbnail text-center">
                                            <img class="b2s-feature-img-with-90" src="<?php echo esc_url(plugins_url('/assets/images/features/post-templates.png', B2S_PLUGIN_FILE)); ?>" alt="Media Library">
                                        </div>
                                        <p class="text-center">
                                            <span class="b2s-text-bold"><?php esc_html_e('Post Templates', 'blog2social') ?></span><br>
                                            <?php esc_html_e('Define a unique post structure to automatically customize your social media posts.', 'blog2social') ?>
                                        </p>
                                    </div>

                                </div>
                                <div class="clearfix"></div>
                                <div class="row">
                                   
                                    <div class="col-md-3 col-hide-padding-left">
                                        <div class="thumbnail text-center">
                                            <img class="b2s-feature-img-with-90" src="<?php echo esc_url(plugins_url('/assets/images/features/premiumnetworks.png', B2S_PLUGIN_FILE)); ?>" alt="Premium Networks">
                                        </div>
                                        <p class="text-center">
                                            <span class="b2s-text-bold"><?php esc_html_e('Premium networks', 'blog2social') ?></span><br>
                                            <?php esc_html_e('Share on more social networks, pages, groups, and multiple accounts per network.', 'blog2social') ?>
                                        </p>
                                    </div>
                                    <div class="col-md-3 col-hide-padding-left">
                                        <div class="thumbnail text-center">
                                            <img class="b2s-feature-img-with-90" src="<?php echo esc_url(plugins_url('/assets/images/features/metrics.png', B2S_PLUGIN_FILE)); ?>" alt="Tags">
                                        </div>
                                        <p class="text-center">
                                            <span class="b2s-text-bold"><?php esc_html_e('Social Media Metrics', 'blog2social') ?></span><br>
                                            <?php esc_html_e('Track the performance of your social media posts with link views, likes, shares and comments.', 'blog2social') ?>
                                        </p>
                                    </div>
                                    <div class="col-md-3 col-hide-padding-left">
                                        <div class="thumbnail text-center">
                                            <img class="b2s-feature-img-with-90" src="<?php echo esc_url(plugins_url('/assets/images/features/blog2social-team-management.png', B2S_PLUGIN_FILE)); ?>" alt="Support">
                                        </div>
                                        <p class="text-center">
                                            <span class="b2s-text-bold"><?php esc_html_e('Team-Management', 'blog2social') ?></span><br>
                                            <?php esc_html_e('Easily organize multiple WordPress users by assigning license keys, social accounts and settings.', 'blog2social') ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="row b2s-premium-btn-group">
                                    <a class="btn btn-primary" href="<?php echo esc_url(B2S_Tools::getSupportLink('affiliate')); ?>" target="_blank">   <?php esc_html_e('Show me plans and prices', 'blog2social') ?></a>
                                    <?php if(!get_option('B2S_PLUGIN_DISABLE_TRAIL')) { ?>
                                        <a class="btn btn-primary" href="<?php echo esc_url(B2S_Tools::getSupportLink('feature')); ?>" target="_blank">   <?php esc_html_e('Show all premium features', 'blog2social') ?></a>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <!--Content|End-->
            </div>
        </div>
    </div>
</div>
