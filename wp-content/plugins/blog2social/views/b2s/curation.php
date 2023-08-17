<?php
wp_nonce_field('b2s_security_nonce', 'b2s_security_nonce');
/* Data */
$userLang = strtolower(substr(get_locale(), 0, 2));
$options = new B2S_Options(B2S_PLUGIN_BLOG_USER_ID);
$optionUserTimeZone = $options->_getOption('user_time_zone');
$userTimeZone = ($optionUserTimeZone !== false) ? $optionUserTimeZone : get_option('timezone_string');
$userTimeZoneOffset = (empty($userTimeZone)) ? get_option('gmt_offset') : B2S_Util::getOffsetToUtcByTimeZone($userTimeZone);
$selSchedDate = (isset($_GET['schedDate']) && !empty($_GET['schedDate'])) ? date("Y-m-d H:i:s", (strtotime(sanitize_text_field(wp_unslash($_GET['schedDate'])) . ' ' . gmdate('H:i:s')))) : "";    //routing from calendar
?>
<div class="b2s-container">
    <div class="b2s-inbox">
        <div class="col-md-12 del-padding-left">
            <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/html/sidebar.php'); ?>
            <div class="col-md-9 del-padding-left del-padding-right">
                <!--Header|Start - Include-->
                <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/html/header.php'); ?>
                <!--Header|End-->
                <h1 id="b2s-curation-title-link" class="b2s-curation-title" style="display: none;"><?php esc_html_e('Share New Link Post', 'blog2social'); ?></h1>
                <h1 id="b2s-curation-title-text" class="b2s-curation-title" style="display: none;"><?php esc_html_e('Share New Text Post', 'blog2social'); ?></h1>
                <h1 id="b2s-curation-title-image" class="b2s-curation-title" style="display: none;"><?php esc_html_e('Share New Image Post', 'blog2social'); ?></h1>
                <h1 id="b2s-curation-title-video" class="b2s-curation-title" style="display: none;"><?php esc_html_e('Share New Video Post', 'blog2social'); ?></h1>
                <p id="b2s-curation-subtitle-link" class="b2s-bold b2s-color-grey b2s-curation-subtitle" style="display: none;"><?php esc_html_e('Add a link you’d like to share on your social networks. You can also share a video link, for example from YouTube or Vimeo (also see Video Post).', 'blog2social'); ?></p>
                <p id="b2s-curation-subtitle-text" class="b2s-bold b2s-color-grey b2s-curation-subtitle" style="display: none;"><?php esc_html_e('Add a comment or text you like to share on your social media accounts.', 'blog2social'); ?></p>
                <p id="b2s-curation-subtitle-image" class="b2s-bold b2s-color-grey b2s-curation-subtitle" style="display: none;"><?php esc_html_e('Upload an image or select an image from your media library and share the image with a short description (comment) on your social media accounts.', 'blog2social'); ?></p>
                <p id="b2s-curation-subtitle-video" class="b2s-bold b2s-color-grey b2s-curation-subtitle" style="display: none;"><?php esc_html_e('Add a video link, for example from YouTube or Vimeo, you like to share on your social media accounts.', 'blog2social'); ?></p>
                <br>
                
                <!--Navbar|Start-->
                <div class="panel panel-default">
                    <div class="panel-body">
                         <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/html/post.navbar.php'); ?>
                    </div>
                </div>
                <input type="hidden" id="b2s-curation-post-format" value="0">
                <!--Navbar|End-->
                <div class="clearfix"></div>
                <!--Content|Start-->
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="clearfix"></div>
                        <div id="b2s-curation-no-review-info" class="alert alert-danger">
                            <span class="glyphicon glyphicon-remove glyphicon-danger"></span> <?php esc_html_e('No link preview available. Please check your link.', 'blog2social'); ?>

                        </div>
                        <div id="b2s-curation-no-auth-info" class="alert alert-danger">
                            <span class="glyphicon glyphicon-remove glyphicon-danger"></span> <?php esc_html_e('No connected networks. Please make sure to connect at least one social media account.', 'blog2social'); ?>
                        </div>
                        <div id="b2s-curation-no-data-info" class="alert alert-danger">
                            <span class="glyphicon glyphicon-remove glyphicon-danger"></span> <?php esc_html_e('Invalid data. Please check your data.', 'blog2social'); ?>
                        </div>
                        <div id="b2s-curation-saved-draft-info" class="alert alert-success">
                            <span class="glyphicon glyphicon-success glyphicon-ok"></span> <?php esc_html_e('Saved as draft.', 'blog2social'); ?>
                        </div>
                        <div class="b2s-curation-area">
                            <form id="b2s-curation-post-form" method="post">
                                <div class="b2s-loading-area" style="display:none">
                                    <br>
                                    <div class="b2s-loader-impulse b2s-loader-impulse-md"></div>
                                    <div class="clearfix"></div>
                                    <div class="text-center b2s-loader-text"><?php esc_html_e("Load data...", "blog2social"); ?></div>
                                </div>
                                <div class="b2s-curation-link-area">
                                    <div class="b2s-curation-input-area">
                                        <div class="col-md-12">
                                            <div class="row form-group">
                                                <p class="b2s-curation-input-area-info-header-text"> <?php esc_html_e("Enter a link you want to share on your social media networks. You can also share a video link, for example from YouTube or from Vimeo (also see Video Post).", "blog2social"); ?></p>
                                                <p class="b2s-curation-input-area-info-header-text-video"> <?php esc_html_e("Enter a video link you want to share on your social media networks, for example from YouTube or from Vimeo.", "blog2social"); ?></p>
                                                <small id="b2s-curation-input-url-help" class="form-text text-muted b2s-color-text-red"><?php esc_html_e("Please enter a valid link", "blog2social") ?></small>
                                                <input type="email" class="form-control" id="b2s-curation-input-url" value="" placeholder="<?php esc_html_e("Enter link", "blog2social"); ?>">
                                                <div class="clearfix"></div>
                                                <div class="b2s-curation-input-area-btn">
                                                    <button class="btn btn-primary b2s-btn-curation-continue"><?php esc_html_e("continue", "blog2social"); ?></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="b2s-curation-result-area">
                                        <div class="col-md-12">
                                            <input type="hidden" id="b2s_user_timezone" name="b2s_user_timezone" value="<?php echo esc_attr($userTimeZoneOffset) ?>">
                                            <div class="b2s-curation-preview-area"></div>
                                            <div class="clearfix"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row b2s-curation-image-area">
                                    <div class="col-md-12">
                                        <input type="hidden" id="b2s_user_timezone" name="b2s_user_timezone" value="<?php echo esc_attr($userTimeZoneOffset) ?>">
                                        <div class="b2s-curation-form-area">
                                            <div class="col-xs-12 col-sm-5 col-lg-3">
                                                <button class="btn btn-primary btn-circle b2s-image-remove-btn" style="display:none;" type="button"><i class="glyphicon glyphicon-trash"></i></button>
                                                <img src="<?php echo esc_url(plugins_url('/assets/images/no-image.png', B2S_PLUGIN_FILE)); ?>" class="b2s-post-item-details-url-image center-block img-responsive">
                                                <input type="hidden" class="b2s-image-url-hidden-field form-control" value="" name="image_url">
                                                <input type="hidden" class="b2s-image-id-hidden-field form-control" value="" name="image_id">
                                                <div class="clearfix"></div>
                                                <button class="btn btn-link btn-xs center-block b2s-select-image-modal-open"><?php esc_html_e('Change image', 'blog2social'); ?></button>
                                            </div>
                                            <div class="col-xs-12 col-sm-7 col-lg-9">
                                                <div class="b2s-post-item-details-item-message-area">
                                                    <textarea id="b2s-post-curation-comment-image" class="form-control b2s-post-item-details-item-message-input" name="comment_image" placeholder="<?php esc_html_e('Write something...', 'blog2social'); ?>"></textarea>
                                                    <button type="button" class="btn btn-sm b2s-post-item-details-item-message-emoji-btn"><img src="<?php echo esc_url(plugins_url('/assets/images/b2s-emoji.png', B2S_PLUGIN_FILE)); ?>"/></button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>                                    
                                </div>
                                <div class="row b2s-curation-text-area">
                                    <div class="col-md-12">
                                        <input type="hidden" id="b2s_user_timezone" name="b2s_user_timezone" value="<?php echo esc_attr($userTimeZoneOffset) ?>">
                                        <div class="b2s-curation-form-area">
                                            <div class="col-xs-12 col-sm-12 col-lg-12" style="padding: 0px;padding-top: 20px;">
                                                <div class="b2s-post-item-details-item-message-area">
                                                    <textarea id="b2s-post-curation-comment-text" class="form-control b2s-post-item-details-item-message-input" name="comment_text" placeholder="<?php esc_html_e('Write something...', 'blog2social'); ?>"></textarea>
                                                    <button type="button" class="btn btn-sm b2s-post-item-details-item-message-emoji-btn"><img src="<?php echo esc_url(plugins_url('/assets/images/b2s-emoji.png', B2S_PLUGIN_FILE)); ?>"/></button>
                                                </div>
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>                                    
                                    </div>
                                </div>
                                <div class="b2s-curation-settings-area"></div>
                                <input type="hidden" id="b2s-draft-id" value="" name="b2s-draft-id">
                                <textarea id="b2s-post-curation-comment-dummy" style="display:none;"></textarea>
                            </form>
                            <div class="row b2s-curation-post-list-area">
                                <div class="b2s-curation-post-list"></div>
                                <div class="col-md-12">
                                    <div class="pull-right">
                                        <button class="btn btn-primary b2s-re-share-btn"><?php esc_html_e('Re-share this post', 'blog2social') ?></button>
                                        <a class="btn btn-primary" href="admin.php?page=blog2social-curation"><?php esc_html_e('Create a new post', 'blog2social') ?></a>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" id="b2sSelSchedDate" value="<?php echo esc_attr((($selSchedDate != "") ? strtotime($selSchedDate) . '000' : '')); ?>">
                            <input type="hidden" id="b2sServerUrl" value="<?php echo esc_attr(B2S_PLUGIN_SERVER_URL); ?>">
                            <input type="hidden" id="b2sJsTextPublish" value="<?php esc_html_e('published', 'blog2social') ?>">
                            <input type="hidden" id="b2sEmojiTranslation" value='<?php echo esc_attr(json_encode(B2S_Tools::getEmojiTranslationList())); ?>'>
                            <input type="hidden" id="b2sDefaultNoImage" value="<?php echo esc_url(plugins_url('/assets/images/no-image.png', B2S_PLUGIN_FILE)); ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade b2s-publish-approve-modal" tabindex="-1" role="dialog" aria-labelledby="b2s-publish-approve-modal" aria-hidden="true" data-backdrop="false"  style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><?php esc_html_e('Do you want to mark this post as published ?', 'blog2social') ?> </h4>
            </div>
            <div class="modal-body">
                <input type="hidden" value="" id="b2s-approve-network-auth-id">
                <input type="hidden" value="" id="b2s-approve-post-id">
                <button class="btn btn-success b2s-approve-publish-confirm-btn"><?php esc_html_e('YES', 'blog2social') ?></button>
                <button class="btn btn-default" data-dismiss="modal"><?php esc_html_e('NO', 'blog2social') ?></button>
            </div>
        </div>
    </div>
</div>



<div id="b2s-sched-post-modal" class="modal fade" role="dialog" aria-labelledby="b2s-sched-post-modal" aria-hidden="true" data-backdrop="false"  style="display:none;">
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

<div class="modal fade" id="b2sInfoCCModal" tabindex="-1" role="dialog" aria-labelledby="b2sInfoCCModal" aria-hidden="true" data-backdrop="false"  style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="b2s-modal-header-image"><?php esc_html_e('You want to create image posts with any image from your media library?', 'blog2social'); ?></h4>
                <h4 class="modal-title" id="b2s-modal-header-text"><?php esc_html_e('You want to create text posts?', 'blog2social'); ?></h4>
            </div>
            <div class="modal-body">
                <p><?php
                    if (B2S_PLUGIN_USER_VERSION <= 1) {
                        ?>
                    
                    <?php esc_html_e('With Blog2Social you can share your WordPress posts and pages as well as create your own social media posts to share any content based on text, links, images, or video links, or even third-party content from any sources. This enables you to manage all your social media content in one place directly from your WordPress dashboard. Schedule and share link posts, text posts, image posts, and video posts (video links, for example from Youtube) and provide your followers with the best content-mix on your social media networks.', 'blog2social') ?>
                    <br>
                    <br>
                    <p class="b2s-bold"><?php esc_html_e('Unlock Blog2Social Premium Pro to create and share image posts, video links, and text posts from any source.', 'blog2social') ?></p>
                    <br>
                    <?php esc_html_e('Share image posts:', 'blog2social') ?>
                    <br>
                    <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Grab more attention for your content with photos, videos, or infographics.', 'blog2social') ?><br>
                    <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Share images to get them into the Google image search to further increase your outreach and traffic from search engines.', 'blog2social') ?><br>
                    <br>
                    <?php esc_html_e('Share text posts:', 'blog2social') ?>
                    <br>
                    <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Share pure text messages and personal comments with your followers and readers.', 'blog2social') ?><br>
                    <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Use hashtags, @mentions, or emojis to share your feelings.', 'blog2social') ?><br>
                    <br>
                    <?php echo sprintf(__('Learn more about how to share social media posts in the <a href="%s" target="_blank">social media posts guide</a>.', 'blog2social'), esc_url(B2S_Tools::getSupportLink('cc_info_faq'))); ?>
                    <br>
                    <a target="_blank" href="<?php echo esc_url(B2S_Tools::getSupportLink('affiliate')); ?>" class="btn btn-success center-block"><?php esc_html_e('Upgrade to PRO and above', 'blog2social') ?></a>
                    <br>
                    <center> <?php echo sprintf(__('or <a target="_blank" href="%s">start with free 30-days-trial of Blog2Social Premium</a> (no payment information needed)', 'blog2social'), esc_url('https://service.blog2social.com/trial')); ?> </center>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="b2sTextPostInfoModal" tabindex="-1" role="dialog" aria-labelledby="b2sTextPostInfoModal" aria-hidden="true" data-backdrop="false"  style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" ><?php esc_html_e('Social Media Posts', 'blog2social'); ?></h4>
            </div>
            <div class="modal-body">
                <div class="b2s-network-imgs">
                    <img class="pull-left hidden-xs b2s-network-info-img" alt="<?php esc_attr_e('Facebook') ?>" src="<?php echo esc_url(plugins_url('/assets/images/portale/1_flat.png', B2S_PLUGIN_FILE)) ?>">
                    <img class="pull-left hidden-xs b2s-network-info-img" alt="<?php esc_attr_e('Twitter') ?>" src="<?php echo esc_url(plugins_url('/assets/images/portale/2_flat.png', B2S_PLUGIN_FILE)) ?>">
                    <img class="pull-left hidden-xs b2s-network-info-img" alt="<?php esc_attr_e('LinkedIn') ?>" src="<?php echo esc_url(plugins_url('/assets/images/portale/3_flat.png', B2S_PLUGIN_FILE)) ?>">
                    <img class="pull-left hidden-xs b2s-network-info-img" alt="<?php esc_attr_e('VKontakte') ?>" src="<?php echo esc_url(plugins_url('/assets/images/portale/17_flat.png', B2S_PLUGIN_FILE)) ?>">
                    <img class="pull-left hidden-xs b2s-network-info-img" alt="<?php esc_attr_e('XING') ?>" src="<?php echo esc_url(plugins_url('/assets/images/portale/19_flat.png', B2S_PLUGIN_FILE)) ?>">
                    <img class="pull-left hidden-xs b2s-network-info-img" alt="<?php esc_attr_e('Google Business Profile') ?>" src="<?php echo esc_url(plugins_url('/assets/images/portale/18_flat.png', B2S_PLUGIN_FILE)) ?>">
                    <img class="pull-left hidden-xs b2s-network-info-img" alt="<?php esc_attr_e('Telegram') ?>" src="<?php echo esc_url(plugins_url('/assets/images/portale/24_flat.png', B2S_PLUGIN_FILE)) ?>">
                    <img class="pull-left hidden-xs b2s-network-info-img" alt="<?php esc_attr_e('Tumblr') ?>" src="<?php echo esc_url(plugins_url('/assets/images/portale/4_flat.png', B2S_PLUGIN_FILE)) ?>">
                    <img class="pull-left hidden-xs b2s-network-info-img" alt="<?php esc_attr_e('Torial') ?>" src="<?php echo esc_url(plugins_url('/assets/images/portale/14_flat.png', B2S_PLUGIN_FILE)) ?>">
                    <img class="pull-left hidden-xs b2s-network-info-img" alt="<?php esc_attr_e('Bloglovin') ?>" src="<?php echo esc_url(plugins_url('/assets/images/portale/16_flat.png', B2S_PLUGIN_FILE)) ?>">
                    <img class="pull-left hidden-xs b2s-network-info-img" alt="<?php esc_attr_e('Blogger') ?>" src="<?php echo esc_url(plugins_url('/assets/images/portale/25_flat.png', B2S_PLUGIN_FILE)) ?>">
                    
                    <img class="pull-right hidden-xs b2s-network-info-img-disabled" alt="<?php esc_attr_e('Flickr') ?>" src="<?php echo esc_url(plugins_url('/assets/images/portale/7_flat.png', B2S_PLUGIN_FILE)) ?>">
                    <img class="pull-right hidden-xs b2s-network-info-img-disabled" alt="<?php esc_attr_e('Diigo') ?>" src="<?php echo esc_url(plugins_url('/assets/images/portale/9_flat.png', B2S_PLUGIN_FILE)) ?>">
                    <img class="pull-right hidden-xs b2s-network-info-img-disabled" alt="<?php esc_attr_e('Pinterest') ?>" src="<?php echo esc_url(plugins_url('/assets/images/portale/6_flat.png', B2S_PLUGIN_FILE)) ?>">
                    <img class="pull-right hidden-xs b2s-network-info-img-disabled" alt="<?php esc_attr_e('Instagram') ?>" src="<?php echo esc_url(plugins_url('/assets/images/portale/12_flat.png', B2S_PLUGIN_FILE)) ?>">
                    <img class="pull-right hidden-xs b2s-network-info-img-disabled" alt="<?php esc_attr_e('Reddit') ?>" src="<?php echo esc_url(plugins_url('/assets/images/portale/15_flat.png', B2S_PLUGIN_FILE)) ?>">
                    <img class="pull-right hidden-xs b2s-network-info-img-disabled" alt="<?php esc_attr_e('Medium') ?>" src="<?php echo esc_url(plugins_url('/assets/images/portale/11_flat.png', B2S_PLUGIN_FILE)) ?>">
                </div>
                <br>
                <p><?php esc_html_e('Text posts enable you to share pure text messages and personal comments with your followers and readers. You can also customize your posts with individual hashtags, @mentions, or emojis.', 'blog2social') ?></p>
                <h4><?php esc_html_e('You can share a text post on the following networks:', 'blog2social') ?></h4>
                <p class="b2s-bold"><?php esc_attr_e('Facebook (Profile & Seiten)') ?></p>
                <p class="b2s-bold"><?php esc_attr_e('Twitter (1 Profil)') ?></p>
                <p class="b2s-bold"><?php esc_attr_e('LinkedIn') ?></p>
                <p class="b2s-bold"><?php esc_attr_e('VKontakte (Profile & Seiten)') ?></p>
                <p class="b2s-bold"><?php esc_attr_e('XING (Profile & Seiten)') ?></p>
                <p class="b2s-bold"><?php esc_attr_e('Google Business Profile') ?></p>
                <p class="b2s-bold"><?php esc_attr_e('Telegram') ?></p>
                <p class="b2s-bold"><?php esc_attr_e('Torial') ?></p>
                <p class="b2s-bold"><?php esc_attr_e('Tumblr') ?></p>
                <p class="b2s-bold"><?php esc_attr_e('Bloglovin') ?></p>
                <p class="b2s-bold"><?php esc_attr_e('Blogger') ?></p>
                <br>
                <p><?php echo sprintf(__('Get more information on how to share a text post with hashtags, @mentions and emojis in the <a href="%s" target="_blank">social media posts guide</a>.', 'blog2social'), esc_url(B2S_Tools::getSupportLink('cc_text_post_info'))); ?></p>
                <br>
                <p><?php echo sprintf(__('In the <a href="%s">Network Settings</a> you can define one or more network selections for your posts.', 'blog2social'), 'admin.php?page=blog2social-network'); ?>
                    <br>
                <?php echo sprintf(__('More information on how to create a network selection in the guide <a href="%s" target="_blank">"How can I save a specific selection of networks?"</a>', 'blog2social'), esc_url(B2S_Tools::getSupportLink('network_grouping'))); ?></p>
            </div>
        </div>
    </div>
</div>

<div id="b2s-network-select-image" class="modal fade" role="dialog" aria-labelledby="b2s-network-select-image" aria-hidden="true" data-backdrop="false"  style="display:none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name="#b2s-network-select-image">&times;</button>
                <h4 class="modal-title"><?php esc_html_e('Select image', 'blog2social') ?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12">
                        <?php
                        require_once B2S_PLUGIN_DIR . 'includes/B2S/Ship/Image.php';
                        $image = new B2S_Ship_Image('curation');
                        echo wp_kses($image->getItemHtml(0, '', '', $userLang), array(
                            'div' => array(
                                'class' => array(),
                                'style' => array(),
                            ),
                            'span' => array(
                                'id' => array(),
                            ),
                            'a' => array(
                                'target' => array(),
                                'href' => array(),
                            ),
                            'i' => array(
                                'class' => array(),
                            ),
                            'label' => array(
                                'for' => array(),
                            ),
                            'img' => array(
                                'class' => array(),
                                'alt' => array(),
                                'src' => array(),
                            ),
                            'input' => array(
                                'class' => array(),
                                'type' => array(),
                                'value' => array(),
                                'id' => array(),
                                'name' => array(),
                                'checked' => array(),
                            ),
                            'br' => array(),
                            'button' => array(
                                'class' => array(),
                                'data-network-id' => array(),
                                'data-post-id' => array(),
                                'data-network-auth-id' => array(),
                                'data-meta-type' => array(),
                                'data-image-count' => array(),
                                'style' => array()
                            )
                        ));
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="b2sInfoNetworkModal" class="modal fade" role="dialog" aria-labelledby="b2s-network-select-image" aria-hidden="true" data-backdrop="false"  style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name="#b2sInfoNetworkModal">&times;</button>
                <h4 class="modal-title"><?php esc_html_e('Social Media Posts', 'blog2social') ?></h4>
            </div>
            <div class="modal-body">
                <div class="b2s-network-imgs">
                    <img class="pull-left hidden-xs b2s-network-info-img" alt="<?php esc_attr_e('Facebook') ?>" src="<?php echo esc_url(plugins_url('/assets/images/portale/1_flat.png', B2S_PLUGIN_FILE)) ?>">
                    <img class="pull-left hidden-xs b2s-network-info-img" alt="<?php esc_attr_e('Twitter') ?>" src="<?php echo esc_url(plugins_url('/assets/images/portale/2_flat.png', B2S_PLUGIN_FILE)) ?>">
                    <img class="pull-left hidden-xs b2s-network-info-img" alt="<?php esc_attr_e('LinkedIn') ?>" src="<?php echo esc_url(plugins_url('/assets/images/portale/3_flat.png', B2S_PLUGIN_FILE)) ?>">
                    <img class="pull-left hidden-xs b2s-network-info-img" alt="<?php esc_attr_e('Pinterest') ?>" src="<?php echo esc_url(plugins_url('/assets/images/portale/6_flat.png', B2S_PLUGIN_FILE)) ?>">
                    <img class="pull-left hidden-xs b2s-network-info-img" alt="<?php esc_attr_e('Flickr') ?>" src="<?php echo esc_url(plugins_url('/assets/images/portale/7_flat.png', B2S_PLUGIN_FILE)) ?>">
                    <img class="pull-left hidden-xs b2s-network-info-img" alt="<?php esc_attr_e('Diigo') ?>" src="<?php echo esc_url(plugins_url('/assets/images/portale/9_flat.png', B2S_PLUGIN_FILE)) ?>">
                    <img class="pull-left hidden-xs b2s-network-info-img" alt="<?php esc_attr_e('Instagram') ?>" src="<?php echo esc_url(plugins_url('/assets/images/portale/12_flat.png', B2S_PLUGIN_FILE)) ?>">
                    <img class="pull-left hidden-xs b2s-network-info-img" alt="<?php esc_attr_e('Reddit') ?>" src="<?php echo esc_url(plugins_url('/assets/images/portale/15_flat.png', B2S_PLUGIN_FILE)) ?>">
                    <img class="pull-left hidden-xs b2s-network-info-img" alt="<?php esc_attr_e('VKontakte') ?>" src="<?php echo esc_url(plugins_url('/assets/images/portale/17_flat.png', B2S_PLUGIN_FILE)) ?>">
                    <img class="pull-left hidden-xs b2s-network-info-img" alt="<?php esc_attr_e('XING') ?>" src="<?php echo esc_url(plugins_url('/assets/images/portale/19_flat.png', B2S_PLUGIN_FILE)) ?>">
                    <img class="pull-left hidden-xs b2s-network-info-img" alt="<?php esc_attr_e('Google Business Profile') ?>" src="<?php echo esc_url(plugins_url('/assets/images/portale/18_flat.png', B2S_PLUGIN_FILE)) ?>">
                    <img class="pull-left hidden-xs b2s-network-info-img" alt="<?php esc_attr_e('Telegram') ?>" src="<?php echo esc_url(plugins_url('/assets/images/portale/24_flat.png', B2S_PLUGIN_FILE)) ?>">
                    <img class="pull-left hidden-xs b2s-network-info-img" alt="<?php esc_attr_e('Tumblr') ?>" src="<?php echo esc_url(plugins_url('/assets/images/portale/4_flat.png', B2S_PLUGIN_FILE)) ?>">
                    <img class="pull-left hidden-xs b2s-network-info-img" alt="<?php esc_attr_e('Torial') ?>" src="<?php echo esc_url(plugins_url('/assets/images/portale/14_flat.png', B2S_PLUGIN_FILE)) ?>">
                    <img class="pull-left hidden-xs b2s-network-info-img" alt="<?php esc_attr_e('Bloglovin') ?>" src="<?php echo esc_url(plugins_url('/assets/images/portale/16_flat.png', B2S_PLUGIN_FILE)) ?>">
                    <img class="pull-left hidden-xs b2s-network-info-img" alt="<?php esc_attr_e('Blogger') ?>" src="<?php echo esc_url(plugins_url('/assets/images/portale/25_flat.png', B2S_PLUGIN_FILE)) ?>">

                    <img class="pull-right hidden-xs b2s-network-info-img-disabled" alt="<?php esc_attr_e('Medium') ?>" src="<?php echo esc_url(plugins_url('/assets/images/portale/11_flat.png', B2S_PLUGIN_FILE)) ?>">                    
                </div>
                <br>
                <p><?php esc_html_e('With Blog2Social you can share social media posts consisting of text, links, images, and videos automatically with your followers and readers and customize your posts with individual hashtags, @mentions, or emojis.', 'blog2social') ?></p>
                <h4><?php esc_html_e('You can select to share your post as a link post, video link post, or an image post on the following social networks:', 'blog2social') ?></h4>
                <p class="b2s-bold"><?php esc_attr_e('Facebook (Profile & Seiten)') ?></p>
                <p class="b2s-bold"><?php esc_attr_e('Twitter (1 Profil)') ?></p>
                <p class="b2s-bold"><?php esc_attr_e('LinkedIn') ?></p>
                <p class="b2s-bold"><?php esc_attr_e('Pinterest') ?></p>
                <p class="b2s-bold"><?php esc_attr_e('Flickr') ?></p>
                <p class="b2s-bold"><?php esc_attr_e('Diigo') ?></p>
                <p class="b2s-bold"><?php esc_attr_e('Instagram') ?></p>
                <p class="b2s-bold"><?php esc_attr_e('Reddit') ?></p>
                <p class="b2s-bold"><?php esc_attr_e('VKontakte (Profile & Seiten)') ?></p>
                <p class="b2s-bold"><?php esc_attr_e('XING (Profile & Seiten)') ?></p>
                <p class="b2s-bold"><?php esc_attr_e('Google Business Profile') ?></p>
                <p class="b2s-bold"><?php esc_attr_e('Telegram') ?></p>
                <p class="b2s-bold"><?php esc_attr_e('Tumblr') ?></p>
                <p class="b2s-bold"><?php esc_attr_e('Torial') ?></p>
                <p class="b2s-bold"><?php esc_attr_e('Bloglovin') ?></p>
                <p class="b2s-bold"><?php esc_attr_e('Blogger') ?></p>
                <br>
                <p><?php echo sprintf(__('In the <a href="%s">Network Settings</a> you can define network selections to select and save your most-used networks for specific posts or campaigns.', 'blog2social'), 'admin.php?page=blog2social-network'); ?>
                    <br>
                <?php echo sprintf(__('More information on how to create a network selection in the guide <a href="%s" target="_blank">"How can I save a specific selection of networks?"</a>', 'blog2social'), esc_url(B2S_Tools::getSupportLink('network_grouping'))); ?></p>
            </div>
        </div>
    </div>
</div>