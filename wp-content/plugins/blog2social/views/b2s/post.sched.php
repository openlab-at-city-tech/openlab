<?php
wp_nonce_field('b2s_security_nonce', 'b2s_security_nonce');
/* Data */
require_once (B2S_PLUGIN_DIR . 'includes/B2S/Post/Filter.php');
require_once (B2S_PLUGIN_DIR . 'includes/Util.php');
require_once B2S_PLUGIN_DIR . 'includes/B2S/Settings/Item.php';
$b2sShowByDate = isset($_GET['b2sShowByDate']) ? (preg_match("#^[0-9\-.\]]+$#", trim(sanitize_text_field($_GET['b2sShowByDate']))) ? trim(sanitize_text_field($_GET['b2sShowByDate'])) : "") : ""; //YYYY-mm-dd
$b2sUserAuthId = isset($_GET['b2sUserAuthId']) ? (int) $_GET['b2sUserAuthId'] : 0;
$b2sPostBlogId = isset($_GET['b2sPostBlogId']) ? (int) $_GET['b2sPostBlogId'] : 0;
$b2sShowByNetwork = isset($_GET['b2sShowByNetwork']) ? (int) $_GET['b2sShowByNetwork'] : 0;
$options = new B2S_Options(B2S_PLUGIN_BLOG_USER_ID);
$optionUserTimeZone = $options->_getOption('user_time_zone');
$userTimeZone = ($optionUserTimeZone !== false) ? $optionUserTimeZone : get_option('timezone_string');
$userTimeZoneOffset = (empty($userTimeZone)) ? get_option('gmt_offset') : B2S_Util::getOffsetToUtcByTimeZone($userTimeZone);
$optionUserTimeFormat = $options->_getOption('user_time_format');
if ($optionUserTimeFormat == false) {
    $optionUserTimeFormat = (substr(B2S_LANGUAGE, 0, 2) == 'de') ? 0 : 1;
}
$optionPostFilters = $options->_getOption('post_filters');
$postsPerPage = (isset($optionPostFilters['postsPerPage']) && (int) $optionPostFilters['postsPerPage'] > 0) ? (int) $optionPostFilters['postsPerPage'] : 25;
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
                        <!--Posts from Wordpress Start-->
                        <!--Filter Start-->
                        <div class="b2s-post">
                            <div class="grid-body">
                                <div class="pull-right"><code id="b2s-user-time"><?php echo esc_attr(B2S_Util::getLocalDate($userTimeZoneOffset, substr(B2S_LANGUAGE, 0, 2))); ?> <?php echo ((substr(B2S_LANGUAGE, 0, 2) == 'de') ? esc_html__('Clock', 'blog2social') : '') ?></code></div>
                                <!-- Filter Post Start-->
                                <form class="b2sSortForm form-inline pull-left" action="#">
                                    <input id="b2sType" type="hidden" value="sched" name="b2sType">
                                    <input id="b2sShowByDate" type="hidden" value="<?php echo esc_attr($b2sShowByDate); ?>" name="b2sShowByDate">
                                    <input id="b2sUserAuthId" type="hidden" value="<?php echo esc_attr($b2sUserAuthId); ?>" name="b2sUserAuthId">
                                    <input id="b2sPostBlogId" type="hidden" value="<?php echo esc_attr($b2sPostBlogId); ?>" name="b2sPostBlogId">
                                    <input id="b2sShowByNetwork" type="hidden" value="<?php echo esc_attr($b2sShowByNetwork); ?>" name="b2sShowByNetwork">
                                    <input id="b2sPagination" type="hidden" value="1" name="b2sPagination">
                                    <?php
                                    $postFilter = new B2S_Post_Filter('sched');
                                    echo wp_kses($postFilter->getItemHtml('blog2social-sched'), array(
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
                                <!-- Filter Post Ende-->
                                <br/>
                            </div>       
                        </div>
                        <div class="clearfix"></div> 
                        <!--Filter End-->
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
                                    <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/html/footer.php'); ?> 
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade b2s-delete-sched-modal" tabindex="-1" role="dialog" aria-labelledby="b2s-delete-sched-modal" aria-hidden="true" data-backdrop="false"  style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name=".b2s-delete-sched-modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php esc_html_e('Delete entries from the scheduling', 'blog2social') ?></h4>
            </div>
            <div class="modal-body">
                <b><?php esc_html_e('You are sure, you want to delete entries from the scheduling?', 'blog2social') ?> </b>
                <br>
                (<?php esc_html_e('Number of entries', 'blog2social') ?>:  <span id="b2s-delete-confirm-post-count"></span>)
                <input type="hidden" value="" id="b2s-delete-confirm-post-id">
            </div>
            <div class="modal-footer">
                <button class="btn btn-default" data-dismiss="modal"><?php esc_html_e('NO', 'blog2social') ?></button>
                <button class="btn btn-danger b2s-sched-delete-confirm-btn"><?php esc_html_e('YES, delete', 'blog2social') ?></button>
            </div>
        </div>
    </div>
</div>

<div id="b2s-network-select-image" class="modal fade" role="dialog" aria-labelledby="b2s-network-select-image" aria-hidden="true" style="z-index: 1070">
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


<div id="b2s-post-ship-item-post-format-modal" class="modal fade" role="dialog" aria-labelledby="b2s-post-ship-item-post-format-modal" aria-hidden="true" data-backdrop="false" style="z-index: 1070">
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


<input type="hidden" id="b2sLang" value="<?php echo esc_attr(substr(B2S_LANGUAGE, 0, 2)); ?>">
<input type="hidden" id="b2sUserTimeFormat" value="<?php echo esc_attr($optionUserTimeFormat); ?>">
<input type="hidden" id="b2sJSTextAddPost" value="<?php echo esc_html_e("add post", "blog2social"); ?>">                    
<input type="hidden" id="b2sUserLang" value="<?php echo esc_attr(strtolower(substr(get_locale(), 0, 2))); ?>">
<input type='hidden' id="user_timezone" name="user_timezone" value="<?php echo esc_attr($userTimeZoneOffset); ?>">
<input type="hidden" id="user_version" name="user_version" value="<?php echo esc_attr(B2S_PLUGIN_USER_VERSION); ?>">
<input type="hidden" id="b2sDefaultNoImage" value="<?php echo esc_url(plugins_url('/assets/images/no-image.png', B2S_PLUGIN_FILE)); ?>">
<input type="hidden" id="b2sPostId" value="">
<input type="hidden" id="b2sInsertImageType" value="0">
<input type="hidden" id="isOgMetaChecked" value="<?php echo (isset($metaSettings['og_active']) ? (int) $metaSettings['og_active'] : 0); ?>">
<input type="hidden" id="isCardMetaChecked" value="<?php echo (isset($metaSettings['card_active']) ? (int) $metaSettings['card_active'] : 0); ?>">
<input type="hidden" id="b2sNotAllowGif" value="<?php echo esc_attr(implode(";", json_decode(B2S_PLUGIN_NETWORK_NOT_ALLOW_GIF, true))); ?>">
<input type="hidden" id="b2sAnimateGif" value='<?php echo esc_attr(B2S_PLUGIN_NETWORK_ANIMATE_GIF); ?>'>
<input type="hidden" id="ogMetaNetworks" value="<?php echo esc_attr(implode(';', json_decode(B2S_PLUGIN_NETWORK_META_TAGS, true)['og'])); ?>">
<input type="hidden" id="b2sEmojiTranslation" value='<?php echo esc_attr(json_encode(B2S_Tools::getEmojiTranslationList())); ?>'>

<script>
    var b2s_has_premium = <?php echo esc_attr(B2S_PLUGIN_USER_VERSION) > 0 ? "true" : "false"; ?>;
    var b2s_plugin_url = '<?php echo esc_url(B2S_PLUGIN_URL); ?>';
    var b2s_post_formats = <?php echo json_encode(array('post' => array(esc_html__('Link Post', 'blog2social'), esc_html__('Image Post', 'blog2social')), 'image' => array(esc_html__('Image with frame', 'blog2social'), esc_html__('Image cut out', 'blog2social')))); ?>;
    var b2s_is_calendar = true;
</script>