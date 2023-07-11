<?php
wp_nonce_field('b2s_security_nonce', 'b2s_security_nonce');
/* Data */
$userLang = strtolower(substr(get_locale(), 0, 2));
require_once (B2S_PLUGIN_DIR . 'includes/B2S/Post/Filter.php');
require_once(B2S_PLUGIN_DIR . 'includes/Options.php');
$options = new B2S_Options(B2S_PLUGIN_BLOG_USER_ID);
$optionUserTimeZone = $options->_getOption('user_time_zone');
$userTimeZone = ($optionUserTimeZone !== false) ? $optionUserTimeZone : get_option('timezone_string');
$userTimeZoneOffset = (empty($userTimeZone)) ? get_option('gmt_offset') : B2S_Util::getOffsetToUtcByTimeZone($userTimeZone);
$optionPostFilters = $options->_getOption('post_filters');
$postsPerPage = (isset($optionPostFilters['postsPerPage']) && (int) $optionPostFilters['postsPerPage'] > 0) ? (int) $optionPostFilters['postsPerPage'] : 25;
$canUseVideoAddon = (defined('B2S_PLUGIN_ADDON_VIDEO') && !empty(B2S_PLUGIN_ADDON_VIDEO)) ? true : false;
?>
<div class="b2s-container">
    <div class="b2s-inbox">
        <div class="col-md-12 del-padding-left">
            <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/html/sidebar.php'); ?>
            <div class="col-md-9 del-padding-left del-padding-right">
                <!--Header|Start - Include-->
                <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/html/header.php'); ?>
                <!--Header|End-->
                <h1 id="b2s-curation-title-video" class="b2s-curation-title" style="display: none;"><?php esc_html_e('Share New Video Post', 'blog2social'); ?></h1>
                <p id="b2s-curation-subtitle-video" class="b2s-bold b2s-color-grey b2s-curation-subtitle" style="display: none;"><?php esc_html_e('Add a video link, upload a video or select a video from your media library to share it on your social media accounts.', 'blog2social'); ?></p>
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
                        <h3><?php esc_html_e("Insert a video link to share on social media, e.g. from Youtube or Vimeo.", 'blog2social') ?> <span class="label label-success"><?php esc_html_e("FREE", "blog2social"); ?></span></h3>
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

                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="clearfix"></div>
                        <div class="b2s-video-upload-drag-drop" >
                            <h3><?php esc_html_e("Upload a video or select a video from your media library to share to your networks.", 'blog2social') ?> <span class="label label-success"><?php esc_html_e("ADDON", "blog2social"); ?></h3>                                            
                            <div id="b2s-video-upload-success" class="alert alert-success b2s-video-upload-success">
                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('Your video file has successfully been added to the media library!', 'blog2social'); ?>
                            </div>                          
                            <div id="b2s-video-upload-error-invalid-file" class="alert alert-danger b2s-video-upload-error">
                                <span class="glyphicon glyphicon-remove glyphicon-danger"></span> <?php esc_html_e('Your video file could not be uploaded. Please check your video!', 'blog2social'); ?>
                            </div>
                            <div id="b2s-video-upload-error-invalid-type" class="alert alert-danger b2s-video-upload-error">
                                <span class="glyphicon glyphicon-remove glyphicon-danger"></span> <?php esc_html_e('Please upload your video in a supported format!', 'blog2social'); ?>
                            </div>
                            <div id="b2s-video-upload-error-trial-invalid-data" class="alert alert-danger b2s-video-upload-activate-trial-error">
                                <span class="glyphicon glyphicon-remove glyphicon-danger"></span> <?php esc_html_e('An unknown error has occurred.  Please try again or contact our support.', 'blog2social'); ?>
                            </div>
                            <div id="b2s-video-upload-error-trial-has-trial" class="alert alert-info b2s-video-upload-activate-trial-error">
                                <span class="glyphicon glyphicon-info-sign glyphicon-info"></span> <?php esc_html_e('Your trial has already been activated.', 'blog2social'); ?>
                            </div>


                            <div class="alert alert-status">
                                <div class="pull-right hidden-xs">
                                    <a class="btn btn-success b2s-video-upload-feedback-btn"><?php esc_html_e("Feedback", "blog2social"); ?></a>
                                </div>
                                <?php if (!defined('B2S_PLUGIN_ADDON_VIDEO_TRIAL_END_DATE')) { ?>
                                    <h4><?php esc_html_e("Try the new video post function for free now (limited time only)", 'blog2social') ?></h4>                                            
                                    <?php esc_html_e("Publish and share your videos on video platforms and social media networks with Blog2Social!", "blog2social"); ?>
                                    <br><br>
                                    <span class="b2s-text-bold"><?php esc_html_e("What's included in the video-post trial?", "blog2social"); ?></span>
                                    <ul class="list-group">
                                        <li class="list-group-item b2s-video-premium-benefits">
                                            - <?php esc_html_e("Publish and share your video files on: YouTube, TikTok, Vimeo, Instagram, Pinterest, Facebook, and Twitter", "blog2social"); ?>
                                        </li>
                                        <li class="list-group-item b2s-video-premium-benefits">
                                            - <?php esc_html_e("Upload 1 video of up to 250 MB per day", "blog2social"); ?>
                                        </li>
                                        <li class="list-group-item b2s-video-premium-benefits">
                                            - <?php esc_html_e("Upload and share up to 2,5 GB of video content during your 30 days trial period", "blog2social"); ?>
                                        </li>
                                    </ul>
                                    <br>
                                    <?php if (B2S_PLUGIN_USER_VERSION == 0) { ?>
                                        <a class="btn btn-success" target="_blank" href="<?php echo esc_url(B2S_Tools::getSupportLink('addon_video_trial')); ?>" target="_blank"><?php esc_html_e('Start your 30-day free Premium trial with Video-Addon', 'blog2social') ?></a>
                                    <?php } else { ?>
                                        <button class="btn btn-success b2s-video-upload-btn-trial" data-loading-text="<?php echo esc_attr(esc_html_e('Activate, please wait...', 'blog2social')) ?>"><?php esc_html_e('Activate your free video-trial now', 'blog2social') ?></button>
                                    <?php } ?>

                                <?php } else { ?>
                                    <h4 class="b2s-video-upload-data-volume-title"><?php esc_html_e("Data Volume", 'blog2social') ?></h4>                                            
                                    <?php if (!$canUseVideoAddon) { ?>
                                        <div class="row">
                                            <div class="col-md-3">                                     
                                                <div class="b2s-progress-bar" data-percent="0" data-custom-text="<?php echo esc_attr("You have 0GB <br>of 0GB left", "blog2social") ?>" data-duration="2000"></div>
                                            </div>
                                            <div class="col-md-9">
                                                <h4><?php esc_html_e("You have reached the limit of your free video trial period", 'blog2social') ?></h4>                                            
                                                <?php esc_html_e("Thank you for joining the trial period of the new Blog2Social Video-Addon.", "blog2social"); ?>
                                                <br>
                                                <br>
                                                <?php if (B2S_PLUGIN_USER_VERSION != 0) { ?>
                                                    <a class="btn btn-success" href="<?php echo esc_url(B2S_Tools::getSupportLink('affiliate')); ?>" target="_blank"><?php esc_html_e('Get the Video-Addon now!', 'blog2social') ?></a>
                                                <?php } else { ?>
                                                    <a class="btn btn-success" href="<?php echo esc_url(B2S_Tools::getSupportLink('affiliate')); ?>" target="_blank"><?php esc_html_e('Get your Premium license and Video-Addon now!', 'blog2social') ?></a>
                                                <?php } ?>
                                            </div>
                                        </div>

                                    <?php } else { ?>
                                        <div class="row">
                                            <?php if (isset(B2S_PLUGIN_ADDON_VIDEO['volume_open']) && isset(B2S_PLUGIN_ADDON_VIDEO['volume_total'])) { ?>
                                                <div class="col-md-3">                                     
                                                    <div class="b2s-progress-bar" 
                                                         data-percent="<?php echo esc_attr(B2S_Util::getUsedPercentOfXy(B2S_PLUGIN_ADDON_VIDEO['volume_open'], B2S_PLUGIN_ADDON_VIDEO['volume_total'])); ?>" 
                                                         data-custom-text="<?php echo esc_attr(sprintf(__('You still have<br><b>%s</b><br>of %s left', 'blog2social'), B2S_Util::convertKbToGb(B2S_PLUGIN_ADDON_VIDEO['volume_open']), B2S_Util::convertKbToGb(B2S_PLUGIN_ADDON_VIDEO['volume_total']))) ?>" 
                                                         data-duration="2000">
                                                    </div>
                                                </div>
                                            <?php } ?>
                                            <div class="col-md-9">
                                                <?php if (isset(B2S_PLUGIN_ADDON_VIDEO['is_trial']) && (int) B2S_PLUGIN_ADDON_VIDEO['is_trial'] == 1) { ?>
                                                    <h4><?php esc_html_e("Your free trial for the Video-Addon is valid until", 'blog2social') ?> <?php echo B2S_Util::getCustomDateFormat(B2S_PLUGIN_ADDON_VIDEO['trial_end_date'] . ' 00:00:00', substr(B2S_LANGUAGE, 0, 2), false) ?></h4>                                            
                                                <?php } else { ?>
                                                    <h4><?php esc_html_e("You used the Video-Addon", 'blog2social') ?></h4>                                            
                                                <?php } ?>
                                                <?php esc_html_e("You can always upgrade your current data volume.", "blog2social"); ?>
                                                <br>
                                                <br>
                                                <?php if (B2S_PLUGIN_USER_VERSION != 0) { ?>
                                                    <a class="btn btn-success" href="<?php echo esc_url(B2S_Tools::getSupportLink('affiliate')); ?>" target="_blank"><?php esc_html_e('Top-up your current data volume now!', 'blog2social') ?></a>
                                                <?php } else { ?>
                                                    <a class="btn btn-success" href="<?php echo esc_url(B2S_Tools::getSupportLink('affiliate')); ?>" target="_blank"><?php esc_html_e('Get your Premium license and Video-Addon now!', 'blog2social') ?></a>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    <?php } ?>

                                <?php } ?>
                            </div> 
                            <div class="b2s-video-upload-file-container">
                                <?php if (current_user_can('upload_files')) { ?>
                                    <input type="file" name="file" id="b2s-video-upload-file">
                                    <div class="b2s-video-upload-file-area"  id="b2s-video-upload-file-area">
                                        <h4 class="b2s-video-upload-title"><?php esc_html_e("Drop your video file here or click to select it from your device.", 'blog2social') ?></h4>
                                    </div>
                                <?php } else { ?>
                                    <div id="b2s-video-upload-user-permission" class="alert alert-danger">
                                        <span class="glyphicon glyphicon-remove glyphicon-danger"></span> <?php esc_html_e('The video upload failed. Please check your video file!', 'blog2social'); ?>
                                    </div>
                                <?php } ?>
                                <div class="container b2s-video-upload-progress-area" style="display: none;">
                                    <div class="row">
                                        <div class="col-sm-1">
                                            <img class="pull-left hidden-xs" src="<?php echo esc_url(plugins_url('/assets/images/video.png', B2S_PLUGIN_FILE)) ?>" alt="posttype">
                                        </div>
                                        <div class="col-sm-11">
                                            <p class="b2s-video-upload-file-name"></p>
                                            <div class="progress">
                                                <div class="progress-bar"></div>
                                            </div> 
                                        </div>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <br>
                                <div class="b2s-video-upload-list">
                                    <form class="b2sSortForm form-inline pull-left" action="#">
                                        <input id="b2sType" type="hidden" value="video" name="b2sType">
                                        <input id="b2sPagination" type="hidden" value="1" name="b2sPagination">
                                        <?php
                                        $postFilter = new B2S_Post_Filter('video');
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
                                    <div class="clearfix"></div>
                                    <div class="b2s-sort-area">
                                        <div class="b2s-loading-area" style="display:none">
                                            <br>
                                            <div class="b2s-loader-impulse b2s-loader-impulse-md"></div>
                                            <div class="clearfix"></div>
                                            <div class="text-center b2s-loader-text"><?php esc_html_e("Loading...", "blog2social"); ?></div>
                                        </div>
                                        <div class="row b2s-sort-result-area">
                                            <div class="col-md-12">
                                                <ul class="list-group b2s-sort-result-item-area"></ul>
                                                <br>
                                                <nav class="b2s-sort-pagination-area text-center">
                                                    <div class="btn-group btn-group-sm pull-right b2s-post-per-page-area hidden-xs" role="group">
                                                        <button type="button" class="btn <?php echo ((int) $postsPerPage == 25) ? "btn-primary" : "btn-default" ?> b2s-post-per-page" data-post-per-page="25">25</button>
                                                        <button type="button" class="btn <?php echo ((int) $postsPerPage == 50) ? "btn-primary" : "btn-default" ?> b2s-post-per-page" data-post-per-page="50">50</button>
                                                        <button type="button" class="btn <?php echo ((int) $postsPerPage == 100) ? "btn-primary" : "btn-default" ?> b2s-post-per-page" data-post-per-page="100">100</button>
                                                    </div>
                                                    <div class="b2s-sort-pagination-content"></div>
                                                </nav>
                                            </div>
                                        </div>
                                    </div>
                                </div> 
                            </div>
                            <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/html/footer.php'); ?> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!--video url curation -->
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


<div class="modal fade b2s-video-upload-feedback-modal" tabindex="-1" role="dialog" aria-labelledby="b2s-video-upload-feedback-modal" aria-hidden="true" data-backdrop="false"  style="display:none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name=".b2s-video-upload-feedback-modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php esc_html_e('Feedback', 'blog2social'); ?></h4>
            </div>
            <div class="modal-body">
                <iframe src="<?php echo esc_url(B2S_Tools::getSupportLink('video_upload_feedback')); ?>" width="100%" height="500px"></iframe>
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



<div class="modal fade b2s-delete-video-upload-modal" tabindex="-1" role="dialog" aria-labelledby="b2s-video-upload-modal" aria-hidden="true" data-backdrop="false"  style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name=".b2s-delete-video-upload-modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php esc_html_e('Delete entries from the reporting', 'blog2social') ?></h4>
            </div>
            <div class="modal-body">
                <b><?php esc_html_e('You are sure, you want to delete entries from the reporting?', 'blog2social') ?></b>
                <br>
                (<?php esc_html_e('Number of entries', 'blog2social') ?>:  <span id="b2s-delete-confirm-attachment-count"></span>) 
                <input type="hidden" value="" id="b2s-delete-confirm-attachment-id">
            </div>
            <div class="modal-footer">
                <button class="btn btn-default" data-dismiss="modal"><?php esc_html_e('NO', 'blog2social') ?></button>
                <button class="btn btn-danger b2s-video-upload-delete-confirm-btn"><?php esc_html_e('YES, delete', 'blog2social') ?></button>
            </div>
        </div>
    </div>
</div>

<input type="hidden" id="b2sLang" value="<?php echo esc_attr(substr(B2S_LANGUAGE, 0, 2)); ?>">
<input type="hidden" id="b2sUserLang" value="<?php echo esc_attr(strtolower(substr(get_locale(), 0, 2))); ?>">
<input type="hidden" id="b2sUserCanUseVideoAddon" value="<?php echo esc_attr($canUseVideoAddon); ?>">