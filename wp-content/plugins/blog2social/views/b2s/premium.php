<?php wp_nonce_field('b2s_security_nonce', 'b2s_security_nonce'); ?>
<div class="b2s-container">
    <div class="b2s-inbox">
        <div class="col-md-12 del-padding-left">
            <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/html/sidebar.php'); ?>
            <div class="col-md-9 del-padding-left del-padding-right">
                <!--Header|End-->
                <div class="clearfix"></div>
                <!--Content|Start-->
                <div class="panel panel-default">
                    <div class="panel-body">
                        <!--Header|Start - Include-->
                        <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/html/header.php'); ?>
                        <?php if (!B2S_System::isblockedArea('B2S_LICENSE_MODUL_EDIT', B2S_PLUGIN_ADMIN)) { ?> 
                            <h3><?php esc_html_e('Your current license:', 'blog2social') ?>
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
                            </h3>
                            <?php if (defined("B2S_PLUGIN_TRAIL_END") && strtotime(B2S_PLUGIN_TRAIL_END) > time()) { ?>
                                <p> <span class="b2s-text-bold"><?php esc_html_e("End of Trial", "blog2social") ?></span>: <?php echo esc_html(B2S_Util::getCustomDateFormat(B2S_PLUGIN_TRAIL_END, trim(strtolower(substr(B2S_LANGUAGE, 0, 2))), false)); ?> 
                                    <a class="b2s-text-bold" href="<?php echo esc_url(B2S_Tools::getSupportLink('affiliate')); ?>" target="_blank">   <?php esc_html_e('Upgrade', 'blog2social') ?></a>
                                </p>
                                <br>
                            <?php } ?>
                            <div class="clearfix"></div>
                            <br>
                            <div class="b2s-key-area">
                                <div id="b2s-license-user-area" class="col-lg-4 col-md-5 col-sm-6 col-xs-12">
                                    <label class="b2s-font-bold"><?php esc_html_e('Select Team Member', 'blog2social'); ?></label>                          
                                    <select id="b2s-license-user-select" class="form-control" data-placeholder="<?php esc_html_e('Select a team member', 'blog2social'); ?>">
                                        <?php if (B2S_PLUGIN_USER_VERSION <= 2) { ?>
                                            <option value="<?php echo esc_attr(get_current_user_id()); ?>" selected ><?php echo esc_attr(wp_get_current_user()->display_name) ?> (<?php esc_html_e('Email', 'blog2social') . ': ' ?> <?php echo esc_attr(wp_get_current_user()->user_email) ?>)</option>
                                            <?php
                                        } else {
                                            echo wp_kses(B2S_Tools::searchUser(wp_get_current_user()->display_name, B2S_PLUGIN_BLOG_USER_ID), array(
                                                'option' => array(
                                                    'value' => array(),
                                                    'selected' => array()
                                                )
                                            ));
                                        }
                                        ?>
                                    </select>
                                </div>

                                <input type="hidden" id="b2s-license-user" value="<?php echo esc_attr(get_current_user_id()); ?>">
                                <input type="hidden" id="b2s-user-current-version-id" value="<?php echo esc_attr(B2S_PLUGIN_USER_VERSION); ?>">
                                <input type="hidden" id="b2s-no-user-found" value="<?php esc_attr_e('No team member found', 'blog2social'); ?>">

                                <div class="col-lg-4 col-md-5 col-sm-6 col-xs-12">
                                    <div class="input-group">
                                        <label class="b2s-font-bold"><?php esc_html_e('Activate License', 'blog2social'); ?></label>
                                        <input class="form-control input-sm b2s-key-area-input" value="" type="text">
                                        <span class="input-group-btn">
                                            <button class="btn b2s-font-bold btn-sm b2s-key-area-btn-submit"><?php esc_html_e('Submit', 'blog2social'); ?></button>
                                        </span>
                                    </div>
                                    <a class="pull-left" target='_blank' href="<?php echo esc_url(B2S_Tools::getSupportLink('faq_license_key')); ?>"><?php esc_html_e('Where do I find my license key?', 'blog2social'); ?></a>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <br>
                        <?php } ?>

                        <div class="clearfix"></div>

                        <?php if (!B2S_System::isblockedArea('B2S_LICENSE_MODUL_FEATURE', B2S_PLUGIN_ADMIN)) { ?>
                            <h2>
                                <?php esc_html_e('Unlock More Automation Power - Upgrade Now for Maximum Productivity!', 'blog2social') ?>
                            </h2>
                            <br>
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-3 col-sm-6 col-xs-12 del-padding-left">
                                        <div>
                                            <img class="b2s-img-with-32" src="<?php echo esc_url(plugins_url('/assets/images/features/automatic-icon.png', B2S_PLUGIN_FILE)); ?>" alt="Social Media Calendar">
                                            <span class="b2s-text-middle b2s-text-bold"><?php esc_html_e('Auto-Posting & Scheduling', 'blog2social') ?></span><br>
                                        </div>
                                        <p class="b2s-padding-top-8">
                                            <?php esc_html_e('Your content on autopilot. Consistent posting, zero effort.', 'blog2social') ?>
                                        </p>
                                    </div>
                                    <div class="col-md-3 col-sm-6 col-xs-12 del-padding-left">
                                        <div>
                                            <img class="b2s-img-with-32" src="<?php echo esc_url(plugins_url('/assets/images/features/resharer-icon.png', B2S_PLUGIN_FILE)); ?>" alt="Social Media Calendar">
                                            <span class="b2s-text-middle b2s-text-bold"><?php esc_html_e('Re-Share Automatically', 'blog2social') ?></span><br>
                                        </div>
                                        <p class="b2s-padding-top-8">
                                            <?php esc_html_e('Maintain an active timeline by automatically resharing your best content.', 'blog2social') ?>
                                        </p>
                                    </div>
                                    <div class="col-md-3 col-sm-6 col-xs-12 del-padding-left">
                                        <div>
                                            <img class="b2s-img-with-32" src="<?php echo esc_url(plugins_url('/assets/images/features/lamp-icon.png', B2S_PLUGIN_FILE)); ?>" alt="Social Media Calendar">
                                            <span class="b2s-text-middle b2s-text-bold"><?php esc_html_e('Custom Post Templates', 'blog2social') ?></span><br>
                                        </div>
                                        <p class="b2s-padding-top-8">
                                            <?php esc_html_e('Tailor network-specific content structures to your audience', 'blog2social') ?>
                                        </p>
                                    </div>
                                    <div class="col-md-3 col-sm-6 col-xs-12 del-padding-left">
                                        <div>
                                            <img class="b2s-img-with-32" src="<?php echo esc_url(plugins_url('/assets/images/features/calendar-icon.png', B2S_PLUGIN_FILE)); ?>" alt="Social Media Calendar">
                                            <span class="b2s-text-middle b2s-text-bold"><?php esc_html_e('Social Media Calendar', 'blog2social') ?></span><br>
                                        </div>
                                        <p class="b2s-padding-top-8">
                                            <?php esc_html_e('View scheduled posts at a glance. Easily rearrange with drag-and-drop.', 'blog2social') ?>
                                        </p>
                                    </div>

                                </div>
                                <div class="clearfix"></div>
                                <br>
                                <br>
                                <div class="row">
                                    <div class="col-md-3 col-sm-6 col-xs-12 del-padding-left">
                                        <div>
                                            <img class="b2s-img-with-32" src="<?php echo esc_url(plugins_url('/assets/images/features/emoji-icon.png', B2S_PLUGIN_FILE)); ?>" alt="Social Media Calendar">
                                            <span class="b2s-text-middle b2s-text-bold"><?php esc_html_e('Best Time Manager', 'blog2social') ?></span><br>
                                        </div>
                                        <p class="b2s-padding-top-8">
                                            <?php esc_html_e('Auto-post at peak engagement times across platforms.', 'blog2social') ?>
                                        </p>
                                    </div>
                                    <div class="col-md-3 col-sm-6 col-xs-12 del-padding-left">
                                        <div>
                                            <img class="b2s-img-with-32" src="<?php echo esc_url(plugins_url('/assets/images/features/megafon-icon.png', B2S_PLUGIN_FILE)); ?>" alt="Social Media Calendar">
                                            <span class="b2s-text-middle b2s-text-bold"><?php esc_html_e('Multiple Accounts', 'blog2social') ?></span><br>
                                        </div>
                                        <p class="b2s-padding-top-8">
                                            <?php esc_html_e('Activate more social media accounts to expand your reach.', 'blog2social') ?>
                                        </p>
                                    </div>
                                    <div class="col-md-3 col-sm-6 col-xs-12 del-padding-left">
                                        <div>
                                            <img class="b2s-img-with-32" src="<?php echo esc_url(plugins_url('/assets/images/features/group-icon.png', B2S_PLUGIN_FILE)); ?>" alt="Social Media Calendar">
                                            <span class="b2s-text-middle b2s-text-bold"><?php esc_html_e('Scalable Licenses', 'blog2social') ?></span><br>
                                        </div>
                                        <p class="b2s-padding-top-8">
                                            <?php esc_html_e('Add users and accounts as needed.', 'blog2social') ?>
                                        </p>
                                    </div>

                                </div>
                                <div class="clearfix"></div>
                                <div class="row b2s-premium-btn-group">
                                    <a class="btn b2s-btn-lg b2s-btn-premium-filled b2s-font-bold" href="<?php echo esc_url(B2S_Tools::getSupportLink('affiliate')); ?>" target="_blank">   <?php esc_html_e('Upgrade license', 'blog2social') ?></a>                                  
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



<div id="b2s-premium-info-team-management-modal" class="modal fade" role="dialog" aria-labelledby="b2s-premium-info-team-management-modal" aria-hidden="true" data-backdrop="false" style="display:none;z-index: 1070;">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name="#b2s-premium-info-team-management-modal">&times;</button>
                <h3 class="modal-title b2s-font-bold b2s-color-green"><?php esc_html_e('Unlock Team Management and Boost Your Productivity!', 'blog2social') ?></h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12">
                        <p><?php esc_html_e('With the Team Management feature, you can assign licenses to multiple users, making collaboration easier and more efficient. Plus, by upgrading, you\'ll unlock access to more social networks, allowing you to manage and schedule across a wider range of platforms.', 'blog2social') ?></p>
                        <br>
                        <p class="b2s-text-bold"><?php esc_html_e('Upgrade to Business to enhance your team\'s productivity and expand your social media reach.', 'blog2social') ?></p>
                        <br>
                        <a href="<?php echo esc_url(B2S_Tools::getSupportLink('pricing')); ?>" target="_blank" class="btn b2s-font-bold b2s-btn-premium-filled"><?php esc_html_e("Discover plans", "blog2social"); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
