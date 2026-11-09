<?php
if (!defined('ABSPATH')) {
    exit;
}
/**
 * @phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
 */

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
                <h1 class="b2s-page-title"><?php esc_html_e('Share a new video post', 'blog2social'); ?></h1>
                <p class="b2s-page-subtitle b2s-color-grey"><?php esc_html_e('Share video links directly to your social media networks', 'blog2social'); ?></p>
                <h1 id="b2s-curation-title-video" class="b2s-curation-title" style="display: none;"><?php esc_html_e('Share New Video Post', 'blog2social'); ?></h1>
                <!--Navbar|Start, since new navbar the navbar is skipped for video-->  
                <input type="hidden" id="b2s-curation-post-format" value="0">
                <!--Navbar|End-->
                <div class="clearfix"></div>
                <!--Compose Area|Start-->
                <div class="b2s-compose-outer">
                    <div class="b2s-compose-col-main">
                        <div class="panel panel-default b2s-compose-panel">
                            <div class="panel-body">

                                <!-- Alert messages (JS-controlled) -->
                                <div id="b2s-curation-no-review-info" class="alert alert-danger" style="display:none;">
                                    <span class="glyphicon glyphicon-remove glyphicon-danger"></span> <?php esc_html_e('No link preview available. Please check your link.', 'blog2social'); ?>
                                </div>
                                <div id="b2s-curation-no-auth-info" class="alert alert-danger" style="display:none;">
                                    <span class="glyphicon glyphicon-remove glyphicon-danger"></span> <?php esc_html_e('No connected networks. Please make sure to connect at least one social media account.', 'blog2social'); ?>
                                </div>
                                <div id="b2s-curation-no-data-info" class="alert alert-danger" style="display:none;">
                                    <span class="glyphicon glyphicon-remove glyphicon-danger"></span> <?php esc_html_e('Invalid data. Please check your data.', 'blog2social'); ?>
                                </div>
                                <div id="b2s-curation-saved-draft-info" class="alert alert-success" style="display:none;">
                                    <span class="glyphicon glyphicon-success glyphicon-ok"></span> <?php esc_html_e('Saved as draft.', 'blog2social'); ?>
                                </div>

                                <!-- Compose header row: avatar + label -->
                                <div class="b2s-compose-header">
                                    <div class="b2s-compose-header-left">
                                        <img class="b2s-compose-avatar img-circle" src="<?php echo esc_url(plugins_url('/assets/images/b2s_icon.png', B2S_PLUGIN_FILE)); ?>" alt="Blog2Social">
                                        <span class="b2s-compose-header-label"><?php esc_html_e('Enter a video link to share on your social media networks', 'blog2social'); ?></span>
                                    </div>
                                </div>

                                <form id="b2s-curation-post-form" method="post">

                                    <!-- Loading spinner -->
                                    <div class="b2s-loading-area" style="display:none;">
                                        <br>
                                        <div class="b2s-loader-impulse b2s-loader-impulse-md"></div>
                                        <div class="clearfix"></div>
                                        <div class="text-center b2s-loader-text"><?php esc_html_e("Load data...", "blog2social"); ?></div>
                                    </div>

                                    <!-- Video link input area -->
                                    <div class="b2s-curation-link-area">
                                        <div class="b2s-curation-input-area">
                                            <small id="b2s-curation-input-url-help" class="form-text b2s-color-text-red"><?php esc_html_e("Please enter a valid link", "blog2social"); ?></small>
                                            <input type="text" class="form-control" id="b2s-curation-input-url" value="" placeholder="<?php esc_attr_e("Enter video link", "blog2social"); ?>">
                                            <button class="btn btn-primary b2s-btn-curation-continue" type="button"><?php esc_html_e("continue", "blog2social"); ?></button>
                                        </div>
                                        <div class="b2s-curation-result-area">
                                            <input type="hidden" name="b2s_user_timezone" value="<?php echo esc_attr($userTimeZoneOffset); ?>">
                                            <div class="b2s-curation-preview-area"></div>
                                        </div>
                                    </div>

                                    <!-- Compose textarea for video post text (used by video.js as #b2s-post-curation-comment) -->
                                    <div class="b2s-compose-unified-input" style="display:none;">
                                        <div class="b2s-compose-textarea-wrap">
                                            <textarea
                                                id="b2s-compose-video-textarea"
                                                class="form-control b2s-compose-main-textarea b2s-post-item-details-item-message-input"
                                                placeholder="<?php esc_attr_e('Write your video post text...', 'blog2social'); ?>"
                                                rows="2"
                                            ></textarea>
                                            <button type="button" class="b2s-compose-emoji-inline-btn b2s-post-item-details-item-message-emoji-btn" title="<?php esc_attr_e('Emoji', 'blog2social'); ?>">
                                                <img src="<?php echo esc_url(plugins_url('/assets/images/b2s-emoji.png', B2S_PLUGIN_FILE)); ?>" width="16" height="16" alt="Emoji">
                                            </button>
                                        </div>
                                    </div>
                                    <div id="b2s-error-text-empty" class="b2s-compose-error-msg" style="display:none;">
                                        <span class="glyphicon glyphicon-exclamation-sign"></span> <?php esc_html_e('Please enter a message.', 'blog2social'); ?>
                                    </div>
                                    <div class="b2s-compose-toolbar" style="display:none;">
                                        <button type="button" id="b2s-compose-settings-toggle" class="btn btn-default btn-sm b2s-compose-toolbar-btn b2s-compose-settings-toggle-btn">
                                            <i class="glyphicon glyphicon-cog"></i>
                                            <span id="b2s-compose-settings-toggle-label"><?php esc_html_e('Time and format settings', 'blog2social'); ?></span>
                                            <i class="glyphicon glyphicon-chevron-down b2s-compose-settings-toggle-icon"></i>
                                        </button>
                                        <div class="b2s-compose-toolbar-spacer"></div>
                                        <button type="button" id="b2s-btn-curation-customize" class="btn btn-default btn-sm b2s-compose-toolbar-btn b2s-btn-curation-customize" disabled="disabled"><?php esc_html_e('Customize', 'blog2social'); ?></button>
                                        <button type="button" id="b2s-btn-curation-share" class="btn btn-success btn-sm b2s-compose-toolbar-btn b2s-btn-curation-share" disabled="disabled"><?php esc_html_e('Share', 'blog2social'); ?></button>
                                    </div>

                                    <!-- Settings panel (starts expanded, Bootstrap-collapsible via cog button) -->
                                    <div id="b2s-compose-settings-panel" style="display:none;">
                                        <div class="b2s-compose-send-options">
                                            <div class="b2s-curation-settings-area" style="display:none;"></div>
                                        </div>
                                    </div>

                                    <input type="hidden" id="b2s-draft-id" value="" name="b2s-draft-id">
                                    <textarea id="b2s-post-curation-comment-dummy" style="display:none;"></textarea>

                                </form>

                                <!-- Re-share area (JS-controlled) -->
                                <div class="row b2s-curation-post-list-area">
                                    <div class="b2s-curation-post-list"></div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <!--Compose Area|End-->

                <!-- Hidden inputs for JS -->
                <input type="hidden" id="b2sSelSchedDate" value="">
                <input type="hidden" id="b2sServerUrl" value="<?php echo esc_attr(B2S_PLUGIN_SERVER_URL); ?>">
                <input type="hidden" id="b2sJsTextPublish" value="<?php esc_attr_e('published', 'blog2social'); ?>">
                <input type="hidden" id="b2sEmojiTranslation" value='<?php echo esc_attr(json_encode(B2S_Tools::getEmojiTranslationList())); ?>'>
                <input type="hidden" id="b2sDefaultNoImage" value="<?php echo esc_url(plugins_url('/assets/images/no-image.png', B2S_PLUGIN_FILE)); ?>">
                <input type="hidden" id="b2sMaxSchedDate" value="<?php echo esc_attr(wp_date('Y-m-d', strtotime("+ 3 years"), new DateTimeZone(date_default_timezone_get()))); ?>">
                <input type="hidden" id="b2s_user_version" value="<?php echo esc_attr(B2S_PLUGIN_USER_VERSION); ?>">

                <div class="panel panel-default b2s-panel-video">
                    <div class="panel-body">
                        <div class="clearfix"></div>
                        <div class="b2s-video-upload-drag-drop" >
                            <h3><?php esc_html_e("Upload a video or select a video from your media library to share to your networks.", 'blog2social') ?> <span class="label label-success"><?php esc_html_e("ADDON", "blog2social"); ?></span></h3>                                            
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

                                <?php if ($canUseVideoAddon) { ?> 
                                    <h4 class="b2s-video-upload-data-volume-title"><?php esc_html_e("Data Volume", 'blog2social') ?></h4>                                                                         
                                    <div class="row">
                                        <?php if (isset(B2S_PLUGIN_ADDON_VIDEO['volume_open']) && isset(B2S_PLUGIN_ADDON_VIDEO['volume_total'])) { ?>
                                            <div class="col-md-3">                                     
                                                <div class="b2s-progress-bar" 
                                                     data-percent="<?php echo esc_attr(B2S_Util::getUsedPercentOfXy(B2S_PLUGIN_ADDON_VIDEO['volume_open'], B2S_PLUGIN_ADDON_VIDEO['volume_total'])); ?>" 
                                                     data-custom-text="<?php echo esc_attr(sprintf(
                                                        // translators: %s kb video left of %s kb video total
                                                        __('You still have<br><b>%1$s</b><br>of %2$s left', 'blog2social'), B2S_Util::getRemainingVideoVolume(B2S_PLUGIN_ADDON_VIDEO['volume_open']), B2S_Util::convertKbToGb(B2S_PLUGIN_ADDON_VIDEO['volume_total']))) ?>" 
                                                     data-duration="2000">
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <div class="col-md-9">
                                            <?php if (isset(B2S_PLUGIN_ADDON_VIDEO['is_trial']) && (int) B2S_PLUGIN_ADDON_VIDEO['is_trial'] == 1) { ?>
                                                <h4><?php esc_html_e("Your free trial for the Video-Addon is valid until", 'blog2social') ?> <?php echo esc_html(B2S_Util::getCustomDateFormat(B2S_PLUGIN_ADDON_VIDEO['trial_end_date'] . ' 00:00:00', substr(B2S_LANGUAGE, 0, 2), false)) ?></h4>                                            
                                            <?php } else { ?>
                                                <h4><?php esc_html_e("You used the Video-Addon", 'blog2social') ?></h4>                                            
                                            <?php } ?>
                                            <?php esc_html_e("You can always upgrade your current data volume.", "blog2social"); ?>
                                            <br>
                                            <br>
                                            <?php if (B2S_PLUGIN_USER_VERSION != 0) { ?>
                                                <a class="btn btn-success" href="<?php echo esc_url(B2S_Tools::getSupportLink('addon_video')); ?>" target="_blank"><?php esc_html_e('Top-up your current data volume now!', 'blog2social') ?></a>
                                            <?php } else { ?>
                                                <a class="btn btn-success" href="<?php echo esc_url(B2S_Tools::getSupportLink('upgrade_version')); ?>" target="_blank"><?php esc_html_e('Get your Premium license and Video-Addon now!', 'blog2social') ?></a>
                                            <?php } ?>
                                        </div>
                                    </div>
                                <?php } else { ?>
                                    <?php if (!defined('B2S_PLUGIN_ADDON_VIDEO_TRIAL_END_DATE')) {
                                        ?>
                                        <h4><?php esc_html_e("Try the new video post function for free now (limited time only)", 'blog2social') ?></h4>                                            
                                        <?php esc_html_e("Publish and share your videos on video platforms and social media networks with Blog2Social!", "blog2social"); ?>
                                        <br><br>
                                        <span class="b2s-text-bold"><?php esc_html_e("What's included in the video-post trial?", "blog2social"); ?></span>
                                        <ul class="list-group">
                                            <li class="list-group-item b2s-video-premium-benefits">
                                                - <?php esc_html_e("Publish and share your video files on: YouTube, TikTok, Vimeo, Instagram, Pinterest, Facebook, and X", "blog2social"); ?>
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
                                        <?php
                                        }
                                    } else {
                                        ?>
                                        <h4 class="b2s-video-upload-data-volume-title"><?php esc_html_e("Data Volume", 'blog2social') ?></h4>                                            
                                        <div class="row">
                                            <div class="col-md-3">                                     
                                                <div class="b2s-progress-bar" data-percent="0" data-custom-text="<?php esc_attr_e("You have 0GB <br>of 0GB left", "blog2social") ?>" data-duration="2000"></div>
                                            </div>
                                            <div class="col-md-9">
                                                <h4><?php esc_html_e("You have reached the limit of your free video trial period", 'blog2social') ?></h4>                                            
                                                <?php esc_html_e("Thank you for joining the trial period of the new Blog2Social Video-Addon.", "blog2social"); ?>
                                                <br>
                                                <br>
                                                <?php if (B2S_PLUGIN_USER_VERSION != 0) { ?>
                                                    <a class="btn btn-success" href="<?php echo esc_url(B2S_Tools::getSupportLink('addon_video')); ?>" target="_blank"><?php esc_html_e('Get the Video-Addon now!', 'blog2social') ?></a>
                                                <?php } else { ?>
                                                    <a class="btn btn-success" href="<?php echo esc_url(B2S_Tools::getSupportLink('upgrade_version')); ?>" target="_blank"><?php esc_html_e('Get your Premium license and Video-Addon now!', 'blog2social') ?></a>
                                            <?php } ?>
                                            </div>
                                        </div>
                                    <?php } ?>

                                <?php } ?>

                            </div> 
                            <div class="b2s-video-upload-file-container">
                                    <?php if (current_user_can('upload_files')) { ?>
                                    <input type="file" name="file" id="b2s-video-upload-file" accept="<?php echo esc_html(implode(',', unserialize(B2S_PLUGIN_ALLOW_VIDEO_MIME_TYPE)), 'blog2social') ?>">
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
                        
                                    <!-- Always-visible: search + toggle button -->
                                    <div class="b2s-wp-filter-bar">
                                        <div class="b2s-wp-filter-bar-left">
                                            <input type="text" class="form-control" id="b2sSortPostTitle" name="b2sSortPostTitle" placeholder="<?php esc_attr_e('Search by title...', 'blog2social'); ?>">
                                            <button type="button" class="btn btn-default btn-sm" id="b2s-wp-filter-toggle">
                                                <i class="glyphicon glyphicon-filter"></i>
                                                <span id="b2s-wp-filter-toggle-label" data-show="<?php esc_attr_e('Show filters', 'blog2social'); ?>" data-hide="<?php esc_attr_e('Hide filters', 'blog2social'); ?>"><?php esc_html_e('Show filters', 'blog2social'); ?></span>
                                            </button>
                                        </div>
                                    </div>
                                    <!-- Filter Post Start-->
                                    <div id="b2s-wp-filter-panel" style="display:none;">
                                        <form class="b2sSortForm form-inline" action="#">
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
                                    </div>
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

<?php

    $modalNames = array("b2sPreFeatureScheduleModal");
    include (B2S_PLUGIN_DIR . 'views/b2s/partials/general-modal.php');

?>

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