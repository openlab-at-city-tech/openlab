<?php
wp_nonce_field('b2s_security_nonce', 'b2s_security_nonce');
/* Data */
require_once (B2S_PLUGIN_DIR . 'includes/B2S/Calendar/Filter.php');
require_once (B2S_PLUGIN_DIR . 'includes/B2S/Post/Filter.php');
require_once (B2S_PLUGIN_DIR . 'includes/B2S/Ship/Image.php');
require_once (B2S_PLUGIN_DIR . 'includes/B2S/Settings/Item.php');
require_once (B2S_PLUGIN_DIR . 'includes/Util.php');

$options = new B2S_Options(B2S_PLUGIN_BLOG_USER_ID);
$optionUserTimeZone = $options->_getOption('user_time_zone');
$userTimeZone = ($optionUserTimeZone !== false) ? $optionUserTimeZone : get_option('timezone_string');
$userTimeZoneOffset = (empty($userTimeZone)) ? get_option('gmt_offset') : B2S_Util::getOffsetToUtcByTimeZone($userTimeZone);
$optionUserTimeFormat = $options->_getOption('user_time_format');
if($optionUserTimeFormat == false) {
    $optionUserTimeFormat = (substr(B2S_LANGUAGE, 0, 2) == 'de') ? 0 : 1;
}
$metaSettings = get_option('B2S_PLUGIN_GENERAL_OPTIONS');
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
                <!--Navbar|Start-->
                <div class="panel panel-default">
                    <div class="panel-body">
                         <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/html/post.navbar.php'); ?>
                    </div>
                </div>
                <!--Navbar|End-->
                <div class="clearfix"></div>
                <!--Content|Start-->
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div>
                            <div class="grid-body">
                                <div class="clearfix"></div>
                                <div class="col-md-12 b2s-calendar-filter form-inline del-padding-left del-padding-right">
                                    <div class="b2s-calendar-filter-network-legend-text">
                                        <?php esc_html_e('Sort by network', 'blog2social'); ?>
                                        <select id="b2s-calendar-filter-status" class="form-control pull-right">
                                            <option selected value="0"><?php esc_html_e('show all', 'blog2social'); ?></option>
                                            <option value="1"><?php esc_html_e('published', 'blog2social'); ?></option>
                                            <option value="2"><?php esc_html_e('scheduled', 'blog2social'); ?></option>
                                        </select>
                                    </div>
                                    <div class="clearfix"></div>
                                    <?php
                                    $filter = new B2S_Calendar_Filter();
                                    $filterNetwork = $filter->getNetworkHtml();
                                    if (!empty($filterNetwork)) {
                                        ?>
                                        <div class="b2s-calendar-filter-network-list hidden-xs">
                                            <?php echo wp_kses($filterNetwork, array(
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
                                            'checked' => array(),
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
                                        'label' => array(),
                                        'select' => array(
                                            'id' => array(),
                                            'name' => array(),
                                            'class' => array()
                                        ),
                                        'option' => array(
                                            'value' => array()
                                        ),
                                        'img' => array(
                                            'class' => array(),
                                            'alt' => array(),
                                            'src' => array()
                                        )
                                    )); ?>
                                        </div>
                                        <div class="b2s-calendar-filter-network-account-list"></div>
                                    <?php }
                                    ?>
                                </div>  
                                <div class="clearfix"></div><hr>
                                <div class="b2s-loading-area">
                                    <br>
                                    <div class="b2s-loader-impulse b2s-loader-impulse-md"></div>
                                    <div class="clearfix"></div>
                                    <div class="text-center b2s-loader-text"><?php esc_html_e("Loading...", "blog2social"); ?></div>
                                </div>
                                <div id='b2s_calendar'></div>
                                <br>
                                <script>
                                    var b2s_calendar_locale = '<?php echo esc_js(strtolower(substr(get_locale(), 0, 2))); ?>';
                                    var b2s_calendar_date = '<?php echo esc_js(B2S_Util::getbyIdentLocalDate($userTimeZoneOffset, "Y-m-d")); ?>';
                                    var b2s_calendar_datetime = '<?php echo esc_js(B2S_Util::getbyIdentLocalDate($userTimeZoneOffset)); ?>';
                                    var b2s_has_premium = <?php echo esc_js(((B2S_PLUGIN_USER_VERSION > 0) ? "true" : "false")); ?>;
                                    var b2s_plugin_url = '<?php echo esc_url(B2S_PLUGIN_URL); ?>';
                                    var b2s_calendar_formats = <?php echo json_encode(array('post' => array(esc_html__('Link Post', 'blog2social'), esc_html__('Image Post', 'blog2social')), 'image' => array(esc_html__('Image with frame', 'blog2social'), esc_html__('Image cut out', 'blog2social')))); ?>;
                                    var b2s_is_calendar = true;
                                </script>
                            </div>
                        </div>
                        <?php
                        $noLegendCalender=1;
                        require_once (B2S_PLUGIN_DIR . 'views/b2s/html/footer.php');
                        ?> 
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<input type="hidden" id="b2sLang" value="<?php echo esc_attr(substr(B2S_LANGUAGE, 0, 2)); ?>">
<input type="hidden" id="b2sUserTimeFormat" value="<?php echo esc_attr($optionUserTimeFormat); ?>">
<input type="hidden" id="b2sJSTextAddPost" value="<?php esc_html_e("add post", "blog2social"); ?>">                    
<input type="hidden" id="b2sUserLang" value="<?php echo esc_attr(strtolower(substr(get_locale(), 0, 2))); ?>">
<input type='hidden' id="user_timezone" name="user_timezone" value="<?php echo esc_attr($userTimeZoneOffset); ?>">
<input type="hidden" id="user_version" name="user_version" value="<?php echo esc_attr(B2S_PLUGIN_USER_VERSION); ?>">
<input type="hidden" id="b2sDefaultNoImage" value="<?php echo esc_url(plugins_url('/assets/images/no-image.png', B2S_PLUGIN_FILE)); ?>">
<input type="hidden" id="b2sPostId" value="">
<input type="hidden" id="b2sInsertImageType" value="0">
<input type="hidden" id="isOgMetaChecked" value="<?php echo (isset($metaSettings['og_active']) ? (int) $metaSettings['og_active'] : 0); ?>">
<input type="hidden" id="isCardMetaChecked" value="<?php echo (isset($metaSettings['card_active']) ? (int) $metaSettings['card_active'] : 0); ?>">
<input type="hidden" id="b2sRedirectUrlContentCuration" value="<?php echo esc_url(get_option('siteurl') . ((substr(get_option('siteurl'), -1, 1) == '/') ? '' : '/') . 'wp-admin/admin.php?page=blog2social-curation'); ?>">
<input type="hidden" id="b2sNotAllowGif" value="<?php echo esc_attr(implode(";", json_decode(B2S_PLUGIN_NETWORK_NOT_ALLOW_GIF, true))); ?>">
<input type="hidden" id="b2sAnimateGif" value='<?php echo esc_attr(B2S_PLUGIN_NETWORK_ANIMATE_GIF); ?>'>
<input type="hidden" id="ogMetaNetworks" value="<?php echo esc_attr(implode(';', json_decode(B2S_PLUGIN_NETWORK_META_TAGS, true)['og'])); ?>">
<input type="hidden" id="b2sEmojiTranslation" value='<?php echo esc_attr(json_encode(B2S_Tools::getEmojiTranslationList())); ?>'>
<!--Routing from dashboard-->
<input type="hidden" id="b2s_rfd" value="<?php echo (isset($_GET['rfd'])) ? 1 : 0; ?>">
<input type="hidden" id="b2s_rfd_b2s_id" value="<?php echo (isset($_GET['b2s_id'])) ? esc_attr(sanitize_text_field(wp_unslash($_GET['b2s_id']))) : ""; ?>">

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

<div id="b2sImageZoomModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="b2sImageZoomModal" aria-hidden="true" data-backdrop="false"  style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="btn btn-primary btn-circle b2sImageZoomModalClose b2s-modal-close close" data-modal-name="#b2sImageZoomModal" aria-label="Close"><i class="glyphicon glyphicon-remove"></i></button>
                <img id="b2sImageZoom">
            </div>
        </div>
    </div>
</div>