<!--Footer Start-->
<div class="b2s-footer">
    <div class="pull-left hidden-xs <?php echo isset($noLegend) ? 'hide' : ''; ?>">
        <?php if (!B2S_System::isblockedArea('B2S_MENU_FOOTER', B2S_PLUGIN_ADMIN)) { ?>
            <small> © <?php echo date('Y'); ?> <a target="_blank" href="https://www.adenion.de" rel="nofollow">Adenion GmbH</a> | <a target="_blank" href="<?php echo esc_url(B2S_Tools::getSupportLink('privacy_policy')); ?>" rel="nofollow"><?php esc_html_e("Privacy Policy", "blog2social") ?></a> | <a target="_blank" href="<?php echo esc_url(B2S_Tools::getSupportLink('term')); ?>" rel="nofollow"><?php esc_html_e("Terms", "blog2social") ?></a> | <?php esc_html_e("We never store your data from your social media profiles", "blog2social") ?></small>
        <?php } ?>
    </div>
    <div class="pull-right hidden-xs <?php echo isset($noLegend) || isset($noLegendCalender) ? 'hide' : ''; ?>">
        <?php if ($_GET['page'] != 'blog2social-video') { ?>
            <small>
                <img class="img-width-9" src="<?php echo esc_url(plugins_url('/assets/images/b2s/video-icon.png', B2S_PLUGIN_FILE)); ?>" alt="video">  <?php esc_html_e('Video', 'blog2social') ?> 
                <img class="img-width-9" src="<?php echo esc_url(plugins_url('/assets/images/b2s/post-icon.png', B2S_PLUGIN_FILE)); ?>" alt="beitrag">  <?php esc_html_e('Post', 'blog2social') ?> 
                <img class="img-width-9" src="<?php echo esc_url(plugins_url('/assets/images/b2s/job-icon.png', B2S_PLUGIN_FILE)); ?>" alt="job"> <?php esc_html_e('Job', 'blog2social') ?>
                <img class="img-width-9" src="<?php echo esc_url(plugins_url('/assets/images/b2s/event-icon.png', B2S_PLUGIN_FILE)); ?>" alt="event"> <?php esc_html_e('Event', 'blog2social') ?>
                <img class="img-width-9" src="<?php echo esc_url(plugins_url('/assets/images/b2s/product-icon.png', B2S_PLUGIN_FILE)); ?>" alt="product"> <?php esc_html_e('Product', 'blog2social') ?>
            </small>
        <?php } ?>
    </div>
</div>
<!--Footer Ende-->
<?php if ($_GET['page'] != 'blog2social-calendar') { ?>

    <div class="modal fade" id="b2sPreFeatureModal" tabindex="-1" role="dialog" aria-labelledby="b2sPreFeatureModal" aria-hidden="true" data-backdrop="false"  style="display:none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="b2s-modal-close close" data-modal-name="#b2sPreFeatureModal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><?php esc_html_e("Upgrade to Blog2Social for Premium", "blog2social") ?></h4>
                </div>
                <div class="modal-body">
                    <?php esc_html_e("With Blog2Social Premium you can:", "blog2social") ?>
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
                </div>            
            </div>
        </div>
    </div>

    <div class="modal fade" id="b2sProFeatureModal" tabindex="-1" role="dialog" aria-labelledby="b2sProFeatureModal" aria-hidden="true" data-backdrop="false"  style="display:none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="b2s-modal-close close" data-modal-name="#b2sProFeatureModal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><?php esc_html_e('Upgrade to Blog2Social PREMIUM PRO', 'blog2social') ?></h4>
                </div>
                <div class="modal-body create-network-profile">
                    <b><?php esc_html_e('You can select different combinations of networks and save them for different sharing purposes.', 'blog2social') ?></b>
                    <br>
                    <br>
                    <?php esc_html_e('Blog2Social Premium PRO allows you to save your preferred social network accounts into network collections for a faster future access. You can assign individual names for each network collection so you can easily access them for your next social sharing activitiy. Use specific network collections for recurring sharing purposes or campaigns, e.g. for initial sharing of new blog posts, for re-sharing evergreen content or for sharing images or videos. Bundle your preferred social network accounts into a network collection for a faster future access. Assign a name to each network collection so you can easily access them for your next social sharing activitiy. You can also connect multiple profiles, pages and groups per network in one network collection.', 'blog2social') ?>
                    <br>
                    <br>
                    <?php esc_html_e('With Blog2Social PREMIUM PRO you can also:', 'blog2social') ?>
                    <br>
                    <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Post on LinkedIn pages, XING pages and groups, as well as Facebook pages and groups', 'blog2social') ?><br>
                    <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Auto-post and auto-schedule new and updated blog posts', 'blog2social') ?><br>
                    <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Schedule your posts at the best times on each network: for one time, multiple times or recurrently', 'blog2social') ?><br>
                    <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Select link format or image format for your posts', 'blog2social') ?><br>  
                    <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Select individual images per post', 'blog2social') ?><br>  
                    <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Reporting and calendar: keep track of your published and scheduled social media posts', 'blog2social') ?><br>
                    <br>
                    <a target="_blank" href="<?php echo esc_url(B2S_Tools::getSupportLink('affiliate')); ?>" class="btn btn-success center-block"><?php esc_html_e('Upgrade to PRO and above', 'blog2social') ?></a>
                    <br>
                    <center> <?php echo sprintf(__('or <a target="_blank" href="%s">start with free 30-days-trial of Blog2Social Premium</a> (no payment information needed)', 'blog2social'), esc_url('https://service.blog2social.com/trial')); ?> </center>
                </div>
                <div class="modal-body auth-network">
                    <b><?php esc_html_e('Activate Blog2Social PREMIUM PRO.', 'blog2social') ?></b>
                    <br>
                    <?php esc_html_e('With Blog2Social Premium PRO you can connect Facebook, Linkedin, Xing and VK pages as well as Facebook, XING and VK groups.', 'blog2social') ?>
                    <br>
                    <br>
                    <?php esc_html_e('Also included:', 'blog2social') ?>
                    <br>
                    <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Social media auto-posting and auto-scheduling', 'blog2social') ?><br>
                    <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Posting to social media pages and groups in Facebook', 'blog2social') ?><br>
                    <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Sharing on multiple accounts per network', 'blog2social') ?><br>
                    <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Best Time Scheduler: schedule once, multiple times or recurringly', 'blog2social') ?><br>  
                    <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Reporting with links to already published posts', 'blog2social') ?><br>  
                    <br>
                    <a target="_blank" href="<?php echo esc_url(B2S_Tools::getSupportLink('affiliate')); ?>" class="btn btn-success center-block"><?php esc_html_e('Upgrade to PRO and above', 'blog2social') ?></a>
                    <br>
                    <center> <?php echo sprintf(__('or <a target="_blank" href="%s">start with free 30-days-trial of Blog2Social Premium</a> (no payment information needed)', 'blog2social'), esc_url('https://service.blog2social.com/trial')); ?> </center>
                </div>              
                <div class="modal-body multi-image">
                    <b><?php esc_html_e('Activate Blog2Social PREMIUM PRO.', 'blog2social') ?></b>
                    <br>
                    <?php esc_html_e('With Blog2Social Premium PRO you can post multiple images.', 'blog2social') ?>
                    <br>
                    <br>
                    <?php esc_html_e('Also included:', 'blog2social') ?>
                    <br>
                    <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Social media auto-posting and auto-scheduling', 'blog2social') ?><br>
                    <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Posting to social media pages and groups in Facebook', 'blog2social') ?><br>
                    <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Sharing on multiple accounts per network', 'blog2social') ?><br>
                    <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Best Time Scheduler: schedule once, multiple times or recurringly', 'blog2social') ?><br>  
                    <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Reporting with links to already published posts', 'blog2social') ?><br>  
                    <br>
                    <a target="_blank" href="<?php echo esc_url(B2S_Tools::getSupportLink('affiliate')); ?>" class="btn btn-success center-block"><?php esc_html_e('Upgrade to PRO and above', 'blog2social') ?></a>
                    <br>
                    <center> <?php echo sprintf(__('or <a target="_blank" href="%s">start with free 30-days-trial of Blog2Social Premium</a> (no payment information needed)', 'blog2social'), esc_url('https://service.blog2social.com/trial')); ?> </center>
                </div>              
            </div>
        </div>
    </div>

    <div class="modal fade" id="b2sBusinessFeatureModal" tabindex="-1" role="dialog" aria-labelledby="b2sBusinessFeatureModal" aria-hidden="true" data-backdrop="false"  style="display:none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="b2s-modal-close close" data-modal-name="#b2sBusinessFeatureModal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><?php esc_html_e('Upgrade to Blog2Social PREMIUM BUSINESS', 'blog2social') ?></h4>
                </div>
                <div class="modal-body auth-network">
                    <b><?php esc_html_e('Activate Blog2Social PREMIUM BUSINESS.', 'blog2social') ?></b>
                    <br>
                    <?php esc_html_e('With Blog2Social Premium BUSINESS you can connect pages in LinkedIn and XING as well as XING groups and Telegram.', 'blog2social') ?>
                    <br>
                    <br>
                    <?php esc_html_e('Also included:', 'blog2social') ?>
                    <br>
                    <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Social media auto-posting and auto-scheduling', 'blog2social') ?><br>
                    <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Posting to social media pages and groups in Facebook', 'blog2social') ?><br>
                    <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Sharing on multiple accounts per network', 'blog2social') ?><br>
                    <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Best Time Scheduler: schedule once, multiple times or recurringly', 'blog2social') ?><br>  
                    <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Reporting with links to already published posts', 'blog2social') ?><br>  
                    <br>
                    <a target="_blank" href="<?php echo esc_url(B2S_Tools::getSupportLink('affiliate')); ?>" class="btn btn-success center-block"><?php esc_html_e('Upgrade to BUSINESS', 'blog2social') ?></a>
                    <br>
                    <center> <?php echo sprintf(__('or <a target="_blank" href="%s">start with free 30-days-trial of Blog2Social Premium</a> (no payment information needed)', 'blog2social'), esc_url('https://service.blog2social.com/trial')); ?> </center>
                </div>             
            </div>
        </div>
    </div>

    <div class="modal fade" id="b2sInfoFormatModal" tabindex="-1" role="dialog" aria-labelledby="b2sInfoFormatModal" aria-hidden="true" data-backdrop="false"  style="display:none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="b2s-modal-close close" data-modal-name="#b2sInfoFormatModal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><?php esc_html_e('Select a custom post format for your social media posts (PREMIUM feature)', 'blog2social') ?></h4>
                </div>
                <div class="modal-body">        
                    <div class="row">
                        <div class="col-md-12">
                            <b><?php esc_html_e('Define your preferred post format for sharing your social media content on Twitter, LinkedIn, or Facebook.', 'blog2social') ?></b>
                            <div class="clearfix"></div>
                            <div class="col-md-6">
                                <br>
                                <b>1) <?php esc_html_e('Link Post', 'blog2social') ?></b><br>
                                <?php esc_html_e('A link post will display the title of the original post, the link address, the first one or two lines of the article, and the original image linked to the post. Clicking the image will direct the user to the linked website.', 'blog2social'); ?>
                            </div>
                            <div class="col-md-6">
                                <br>
                                <b>2) <?php esc_html_e('Image Post', 'blog2social') ?></b><br>
                                <?php esc_html_e('An image post will display the cover image of the linked website or post and add it to the library of the selected social media networks. Blog2Social will automatically include a link to the website in the text field of the social media post. You can customize the link for each network.', 'blog2social')."<br><br>".esc_html('Selecting an individual post format for your social media posts is only one of Blog2Social’s Premium features. Here are some more things you can do with Blog2Social Premium:', 'blog2social'); ?>
                                <br>
                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Select frames or crop, flip and rotate images', 'blog2social') ?><br>
                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Create your own custom social media post templates', 'blog2social') ?><br>
                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Auto-post new and updated blog posts', 'blog2social') ?><br>
                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Create and share social media posts from any other content', 'blog2social') ?><br>
                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Auto-schedule your posts with the Best Time Manager', 'blog2social') ?><br>
                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Publish and share videos to your social media networks', 'blog2social') ?><br>
                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('and many more!', 'blog2social') ?><br>

                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                       


                        <?php if (B2S_PLUGIN_USER_VERSION == 0) {
                            ?>
                            <div class="col-md-12"> 
                               
                                <a target="_blank" href="<?php echo esc_url(B2S_Tools::getSupportLink('affiliate')); ?>" class="btn btn-success center-block"><?php esc_html_e('Upgrade to SMART or above ', 'blog2social') ?></a>
                                <br>
                                <center> <?php echo sprintf(__('or <a target="_blank" href="%s">start with free 30-days-trial of Blog2Social Premium</a> (no payment information needed)', 'blog2social'), esc_url('https://service.blog2social.com/trial')); ?> </center>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="b2sTrailFeedbackModal" tabindex="-1" role="dialog" aria-labelledby="b2sTrailFeedbackModal" aria-hidden="true" data-backdrop="false"  style="display:none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="b2s-modal-close close" data-modal-name="#b2sTrailFeedbackModal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><?php esc_html_e('Did you miss something?', 'blog2social') ?></h4>
                </div>
                <div class="modal-body">
                    <?php esc_html_e('Help us make Blog2Social even better!', 'blog2social') ?>
                    <textarea id="b2s-trial_message" class="form-control"></textarea>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary b2s-send-trail-feedback"><?php esc_html_e('submit', 'blog2social') ?></button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="b2sInfoSchedTimesModal" tabindex="-1" role="dialog" aria-labelledby="b2sInfoSchedTimesModal" aria-hidden="true" data-backdrop="false"  style="display:none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="b2s-modal-close close" data-modal-name="#b2sInfoSchedTimesModal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><?php esc_html_e('Best Time Manager', 'blog2social') ?></h4>
                </div>
                <div class="modal-body">
                    <?php esc_html_e('Blog2Social provides you with a pre-configured time-scheme to automatically schedule your social media posts for the best times to share on each social network based on recent research. Click Load Best Times in the preview editor to schedule your posts automatically for the best times to post on each social network.', 'blog2social') ?>
                    <br>
                    <?php esc_html_e('You can also configure your own individual time settings for each of your social media connections to post your content on social media when your followers are online. By configuring an individual cross-posting schedule for all your networks you can set up an effective social media posting plan to reach as many followers as possible.', 'blog2social') ?>
                    <br>
                    <?php esc_html_e('Click Load My Time Settings in the preview editor to schedule your posts automatically for your individually chosen best times.', 'blog2social') ?>
                    <br>
                    <?php esc_html_e('You can always edit the predefined times in the preview editor for any post or network and save your new settings as default for future use.', 'blog2social') ?>
                    <br>
                    <a href="<?php echo esc_url(B2S_Tools::getSupportLink('userTimeSettings')); ?>" target="_blank"><?php esc_html_e('Learn how to set up and apply individual best times to your social media scheduling and auto-poster.', 'blog2social') ?></a>
                    <br>
                    <br>
                    <?php if (B2S_PLUGIN_USER_VERSION == 0) { ?>
                        <h4><?php esc_html_e('You want to schedule your posts and use the Best Time Scheduler?', 'blog2social'); ?></h4>
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


    <div class="modal fade" id="b2sInfoPostRelayModal" tabindex="-1" role="dialog" aria-labelledby="b2sInfoPostRelayModal" aria-hidden="true" data-backdrop="false"  style="display:none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="b2s-modal-close close" data-modal-name="#b2sInfoPostRelayModal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><?php esc_html_e('Why Retweets?', 'blog2social') ?></h4>
                </div>
                <div class="modal-body">
                    <?php esc_html_e('Retweets are the recommended way to reshare the same Tweets across Twitter accounts in accordance with Twitter new rules. You can now schedule multiple Retweets for an original Tweet that you are planning right from your WordPress.', 'blog2social') ?>
                    <br>
                    <br>
                    <?php esc_html_e('If Retweets are enabled, every Original-Tweet you schedule in this step will be retweeted by the selected Twitter accounts. If, for example, 3 Original-Tweets are scheduled, every single Tweet will trigger a Retweet for the selected Twitter accounts.', 'blog2social') ?>
                    <br>
                    <br>
                    <?php if (B2S_PLUGIN_USER_VERSION == 0) { ?>
                        <h4><?php esc_html_e('Would you like to retweet?', 'blog2social'); ?></h4>
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

    <div class="modal fade" id="b2sInfoContentTwitterModal" tabindex="-1" role="dialog" aria-labelledby="b2sInfoContentTwitterModal" aria-hidden="true" data-backdrop="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="b2s-modal-close close" data-modal-name="#b2sInfoContentTwitterModal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><?php esc_html_e('Define Twitter post content', 'blog2social') ?></h4>
                </div>
                <div class="modal-body">
                    <?php esc_html_e('Select the content that will be automatically pre-filled in your Twitter posts. If you have ticked the box "include WordPress tags as hashtags in my post", hashtags are automatically added in the drop-down menu.', 'blog2social') ?>
                    <br>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="b2sInfoAutoPosterMModal" tabindex="-1" role="dialog" aria-labelledby="b2sInfoAutoPosterMModal" aria-hidden="true" data-backdrop="false"  style="display:none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><?php esc_html_e('Blog2Social: Social Media Auto-Posting', 'blog2social') ?></h4>
                </div>
                <div class="modal-body">
                    <p><?php
                        echo esc_html__('Share your blog posts with the Auto Poster: Your blog posts will be shared automatically on your social media channels as soon as you publish or update a new post. You can also choose to autopost scheduled blog posts as soon as they are published.', 'blog2social');
                        echo ' ' . sprintf(__('<a target="_blank" href="%s">Learn how to set up auto posting for your blog posts</a>', 'blog2social'), esc_url(B2S_Tools::getSupportLink('auto_poster_m')));

                        if (B2S_PLUGIN_USER_VERSION == 0) {
                            ?>
                            <br>
                        <hr>               
                        <h4><?php esc_html_e('You want to auto-post your blog post?', 'blog2social'); ?></h4>
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

    <div class="modal fade" id="b2sInfoAutoPosterAModal" tabindex="-1" role="dialog" aria-labelledby="b2sInfoAutoPosterAModal" aria-hidden="true" data-backdrop="false"  style="display:none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><?php esc_html_e('Blog2Social: Social Media Auto-Posting', 'blog2social') ?></h4>
                </div>
                <div class="modal-body">
                    <p><?php
                        echo esc_html__('Share imported posts with the Auto Poster: Posts that you import via RSS feeds and plugins can be shared automatically on your social media channels.', 'blog2social');
                        echo ' ' . sprintf(__('<a target="_blank" href="%s">Learn how to set up auto posting for imported posts</a>', 'blog2social'), esc_url(B2S_Tools::getSupportLink('auto_poster_a')));

                        if (B2S_PLUGIN_USER_VERSION == 0) {
                            ?>
                        <hr>               
                        <h4><?php esc_html_e('You want to auto-post your blog post?', 'blog2social'); ?></h4>
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

    <div class="modal fade" id="b2sInfoAssignAutoPost" tabindex="-1" role="dialog" aria-labelledby="b2sInfoAssignAutoPost" aria-hidden="true" data-backdrop="false"  style="display:none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="b2s-modal-close close" data-modal-name="#b2sInfoAssignAutoPost" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><?php esc_html_e('Transfer Auto-Poster settings to other users (Business):', 'blog2social') ?></h4>
                </div>
                <div class="modal-body">
                    <?php esc_html_e('With Blog2Social you can transfer the Auto-Poster settings as a WordPress-administrator to other users if they have activated the same Blog2Social-Business license. This way, you can also assign social media accounts to other users, so they can auto-post without setting up these connections in each user account. Within these settings, you can also decide whether newly published or updated content from other users should be automatically shared. Users with an assigned Auto-Poster setting and an assigned social-media-network group will then share content automatically how you selected the content to be shared automatically.', 'blog2social') ?>
                    <br>
                    <?php echo sprintf(__('You will get more information on how to assign the Auto-Poster settings in the <a target="_blank" href="%s">Auto-Poster guide</a>.', 'blog2social'), esc_url(B2S_Tools::getSupportLink('auto_post_assign'))) ?>
                    <br>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="b2sInfoRePosterModal" tabindex="-1" role="dialog" aria-labelledby="b2sInfoRePosterModal" aria-hidden="true" data-backdrop="false"  style="display:none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><?php esc_html_e('Blog2Social: Re-Share Posts', 'blog2social') ?></h4>
                </div>
                <div class="modal-body">
                    <p><?php
                        echo esc_html__('Keep your social media feed updated automatically with your best content and save valuable time by reviving your evergreen content regularly. Automate your resharing process with Blog2Social, so you can use your time to create new content and interact with your community.', 'blog2social');
                        echo ' ' . sprintf(__('<a target="_blank" href="%s">More information</a>', 'blog2social'), esc_url(B2S_Tools::getSupportLink('re_post')));

                        if (B2S_PLUGIN_USER_VERSION == 0) {
                            ?>
                            <br>
                        <hr>               
                        <h4><?php esc_html_e('You want to auto-post your blog post?', 'blog2social'); ?></h4>
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

<?php } ?>

<div class="modal fade" id="b2s-info-meta-tag-modal" tabindex="-1" role="dialog" aria-labelledby="b2s-info-meta-tag-modal" aria-hidden="true" data-backdrop="false" style="display:none; z-index: 1070;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name="#b2s-info-meta-tag-modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <div class="meta-title modal-meta-content" data-meta-origin="settings" style="display: none;">
                        <?php esc_html_e('Social Meta Tags Settings', 'blog2social') ?>
                    </div>
                    <div class="meta-title modal-meta-content" data-meta-origin="ship" style="display: none;">
                        <?php esc_html_e('Important information about editing the meta tags', 'blog2social'); ?>
                    </div>
                </h4>
            </div>
            <div class="modal-body">
                <div class="meta-body modal-meta-content" data-meta-type="og" data-meta-origin="settings" style="display: none;">
                    <?php esc_html_e('Facebook has changed its policy for posting link posts via plugins or web applications. Facebook does no longer display the featured or selected image for your blog post, but only images defined in the Open Graph (OG) Meta Tags of your blog post. If you have not defined any OG Meta Tags, Facebook displays a random image from your blog post or blog site. If you have defined an image in your blog post OG Meta Tags that does not meet the image size requirements, Facebook also does not displayed your selected image, but a random image. Please make sure that your image meets the image size requirements for Facebook.', 'blog2social') ?>
                    <br>
                    <?php esc_html_e('With Blog2Social you can select a featured image or any image you select to be displayed with your link post. Blog2Social will automatically write the required parameter in the OG Meta Tags of your blog post, so that your selected image will be displayed with your link post. If you don\'t want Blog2Social to do that, because you have defined your own OG meta tags, please uncheck this box. Please note that you cannot select a specific image for your link post without OG meta tags.', 'blog2social') ?>
                </div>
                <div class="meta-body modal-meta-content" data-meta-type="card" data-meta-origin="settings" style="display: none;">
                    <?php esc_html_e('Twitter has changed its policy for posting link posts via plugins or web applications. Twitter does no longer display the featured or selected image for your blog post, but only images defined in the Twitter Card Meta Tags of your blog post. If you have not defined any Twitter Card Meta Tags, Twitter displays a random image from your blog post or blog site. If you have defined an image in your blog post Twitter Card Meta Tags that does not meet the image size requirements, Twitter displays a white space for the image of your link post. Please make sure that your image meets the image size requirements for Twitter.', 'blog2social') ?>
                    <br>
                    <?php esc_html_e('With Blog2Social you can select a featured image or any image you select to be displayed with your link post. Blog2Social will automatically write the required parameter in the Twitter Card meta tags of your blog post, so that your selected image will be displayed with your link post. If you don\'t want Blog2Social to do that, because you have defined your own Twitter Card meta tags, please uncheck this box. Please note that you cannot select a specific image for your link post without Twitter Card meta tags.', 'blog2social') ?>
                </div>
                <div class="meta-body modal-meta-content" data-meta-type="oEmbed" data-meta-origin="settings" style="display: none;">
                    <?php esc_html_e('To display your link preview, LinkedIn uses the image set in the oEmbed tags in meta data of your post. WordPress automatically sets your featured image as your preferred image in the oEmbed tags. If you would like to change your image on LinkedIn without changing your featured image, you can uncheck the “Add oEmbed tags” box.', 'blog2social') ?>
                    <br>
                    <?php esc_html_e('If LinkedIn can’t find the oEmbed tag in your data, it will use the OG (Open Graph) meta tags instead.', 'blog2social') ?>
                    <br>
                    <?php esc_html_e('With Blog2Social you can select a featured image or any image you select to be displayed with your link post. Blog2Social will automatically write the required parameter in the OG Meta Tags of your post, so that your selected image will be displayed with your link post. We recommend an image size between 667x523 and 1000x1000 Pixels. Please make sure that the "Add Open Graph meta tags" box is checked, if you uncheck the oEmbed tags. If both settings are unchecked, make sure to use another plugin to set your OG tags, otherwise the social networks will display no image or a random image in your post.', 'blog2social') ?>
                </div>
                <div class="meta-body modal-meta-content" data-meta-type="og" data-meta-origin="ship" style="display: none;">
                    <?php esc_html_e('What are meta tags?', 'blog2social') ?>
                    <br>
                    <?php esc_html_e('With the help of the meta tags you can decide, how the preview of your link post looks like on social media. You can edit the following fields to change the look:', 'blog2social') ?>
                    <br>
                    <br>
                    <b>- <?php esc_html_e('Image', 'blog2social') ?></b><br>
                    <b>- <?php esc_html_e('Title', 'blog2social') ?></b><br>
                    <b>- <?php esc_html_e('Description', 'blog2social') ?></b><br>
                    <br>
                    <?php esc_html_e('Blog2Social automatically writes this information into the Open Graph (OG) tags as the image, title and description of your WordPress post.', 'blog2social') ?>
                    <br>
                    <br>
                    <?php esc_html_e('Please note:', 'blog2social') ?><br>
                    <br>
                    <?php echo sprintf(__('If you use other plugins for setting meta tags, such as Yoast SEO, the tags you customized with Blog2Social will be overwritten by the other plugins. To allow Blog2Social to apply and share your changes, please make sure you have <a target="_blank" href="%s">activated meta tag settings for Blog2Social only</a> and disable all meta tag settings in your other plugins.', 'blog2social'), esc_url('admin.php?page=blog2social-settings')); ?>
                    <br>
                    <br>
                    <?php esc_html_e('If this post has been previously shared or scheduled, your current changes will also affect the appearance of previously shared or scheduled posts, as the networks will always pull the latest information from the open graph meta tags and automatically update any existing posts.', 'blog2social') ?><br>
                    <br>
                    <br>
                    <?php echo sprintf(__('Your changes will not be applied to your previously shared social media posts if you have manually disabled the meta tag options in your <a target="_blank" href="%s">Blog2Social settings</a>.', 'blog2social'), esc_url('admin.php?page=blog2social-settings')); ?>
                    <br>
                    <br>
                    <?php echo sprintf(__('For more information on how to set meta tags correctly, you can take a look into the <a target="_blank" href="%s">meta tag checklist</a>.', 'blog2social'), esc_url(B2S_Tools::getSupportLink('yoast_warning_og_guide'))); ?>
                </div>
                <div class="meta-body modal-meta-content" data-meta-type="card" data-meta-origin="ship" style="display: none;">
                    <?php esc_html_e('What are Twitter Cards?', 'blog2social') ?>
                    <br>
                    <?php esc_html_e('The Twitter Cards define the look of your preview of your link post on Twitter. By editing the Twitter Card tags you can change the following parameters to change the look:', 'blog2social') ?>
                    <br>
                    <br>
                    <b>- <?php esc_html_e('Image', 'blog2social') ?></b><br>
                    <b>- <?php esc_html_e('Title', 'blog2social') ?></b><br>
                    <b>- <?php esc_html_e('Description', 'blog2social') ?></b><br>
                    <br>
                    <?php esc_html_e('Blog2Social automatically writes this information into the Twitter Card tags as the image, title and description of your WordPress post.', 'blog2social') ?>
                    <br>
                    <br>
                    <?php esc_html_e('Please note:', 'blog2social') ?><br>
                    <br>
                    <?php echo sprintf(__('If you use other plugins for setting Twitter Cards, such as Yoast SEO, the tags you customized with Blog2Social will be overwritten by the other plugins. To allow Blog2Social to apply your changes, please make sure you have <a target="_blank" href="%s">activated Twitter Card settings for Blog2Social only</a> and disable all Twitter Card settings in your other  plugins.', 'blog2social'), esc_url('admin.php?page=blog2social-settings')); ?>
                    <br>
                    <br>
                    <?php esc_html_e('If this post was previously shared or scheduled, your current changes will also affect the look of previously shared or scheduled posts, as Twitter will always pull the most up-to-date information from the Twitter Card tags. If this post has already been shared, it may take up to 7 days for Twitter to update your current changes.', 'blog2social') ?><br>
                    <br>
                    <br>
                    <?php echo sprintf(__('Your changes will not affect your previously shared social media posts if you have manually disabled the meta tag options in your <a target="_blank" href="%s">Blog2Social settings</a>.', 'blog2social'), esc_url('admin.php?page=blog2social-settings')); ?>
                    <br>
                    <br>
                    <?php echo sprintf(__('For more information on how to set meta tags correctly, you can take a look into the <a target="_blank" href="%s">Twitter Card guide</a>.', 'blog2social'), esc_url(B2S_Tools::getSupportLink('twitter_card_guide'))); ?>
                </div>

                <?php if (B2S_PLUGIN_USER_VERSION == 0) {
                    ?>
                    <br>
                    <hr>               
                    <h4><?php esc_html_e('You want to change the image, title and description for your post?', 'blog2social'); ?></h4>
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
                    <center>
                        <?php echo sprintf(__('or <a target="_blank" href="%s">start with free 30-days-trial of Blog2Social Premium</a> (no payment information needed)', 'blog2social'), esc_url('https://service.blog2social.com/trial')); ?>
                    </center>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="b2s-info-change-meta-tag-modal" tabindex="-1" role="dialog" aria-labelledby="b2s-info-change-meta-tag-modal" aria-hidden="true" data-backdrop="false" style="display:none; z-index: 1070;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name="#b2s-info-change-meta-tag-modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <?php esc_html_e('Change image, title and description for your post on this network', 'blog2social'); ?>
                </h4>
            </div>
            <div class="modal-body">
                <div class="isLinkPost meta-text" style="display:none;">
                    <?php esc_html_e('You are currently sharing this post as image post. Changes to title and description Meta Tag parameters will only be supported for link post formats. Please change your post format to link post to make individual changes to the title and description for your post preview.', 'blog2social'); ?>
                </div>
                <div class="isOgMetaChecked meta-text" style="display:none;">
                    <?php echo sprintf(__('Your changes will have no effect on your social media posts on Facebook, if you have manually unchecked the Meta Tag options for Facebook in your Blog2Social <a target="_blank" href="%s">settings</a>', 'blog2social'), esc_url('admin.php?page=blog2social-settings')); ?>
                </div>
                <div class="isCardMetaChecked meta-text"  style="display:none;">
                    <?php echo sprintf(__('Your changes will have no effect on your social media posts on Twitter, if you have manually unchecked the Meta Tag options for Twitter in your Blog2Social <a target="_blank" href="%s">settings</a>', 'blog2social'), esc_url('admin.php?page=blog2social-settings')); ?>
                </div>
            </div>
        </div>
    </div>
</div>
