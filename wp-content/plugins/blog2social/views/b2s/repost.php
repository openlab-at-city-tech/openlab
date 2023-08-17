<?php
wp_nonce_field('b2s_security_nonce', 'b2s_security_nonce');
require_once B2S_PLUGIN_DIR . 'includes/B2S/RePost/Item.php';
$rePostItem = new B2S_RePost_Item();

require_once (B2S_PLUGIN_DIR . 'includes/Util.php');
require_once B2S_PLUGIN_DIR . 'includes/B2S/Settings/Item.php';
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
    <div class=" b2s-inbox col-md-12 del-padding-left">
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
                    <div class="col-md-12">
                        <div class="row b2s-loading-area width-100" style="display: none;">
                            <div class="b2s-loader-impulse b2s-loader-impulse-md"></div>
                            <div class="text-center b2s-loader-text"><?php esc_html_e("Loading...", "blog2social"); ?></div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="row b2s-repost-options-area">
                        <?php echo wp_kses($rePostItem->getRePostOptionsHtml(), array(
                            'br' => array(),
                            'h4' => array(),
                            'h3' => array(
                                'class' => array()
                            ),
                            'span' => array(
                                'class' => array(),
                                'style' => array()
                            ),
                            'div' => array(
                                'class' => array(),
                                'style' => array()
                            ),
                            'i' => array(
                                'class' => array()
                            ),
                            'form' => array(
                                'id' => array(),
                                'class' => array()
                            ),
                            'select' => array(
                                'name' => array(),
                                'class' => array(),
                                'id' => array(),
                                'data-placeholder' => array(),
                                'multiple' => array(),
                            ),
                            'option' => array(
                                'data-limit' => array(),
                                'value' => array(),
                                'data-mandant-id' => array(),
                                'selected' => array()
                            ),
                            'input' => array(
                                'type' => array(),
                                'id' => array(),
                                'class' => array(),
                                'name' => array(),
                                'value' => array(),
                                'checked' => array(),
                                'style' => array(),
                                'placeholder' => array(),
                                'min' => array(),
                                'max' => array(),
                            ),
                            'label' => array(
                                'for' => array(),
                                'class' => array(),
                            ),
                            'a' => array(
                                'class' => array(),
                                'href' => array(),
                                'target' => array()
                            )
                        )); ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="row b2s-repost-queue-area">
                        <?php echo wp_kses($rePostItem->getRePostQueueHtml(), array(
                            'a' => array(
                                'class' => array(),
                                'href' => array(),
                                'target' => array(),
                                'data-post-id' => array()
                            ),
                            'div' => array(
                                'class' => array(),
                                'style' => array(),
                                'id' => array(),
                                'data-post-id' => array()
                            ),
                            'i' => array(
                                'class' => array()
                            ),
                            'span' => array(
                                'class' => array(),
                                'data-post-id' => array()
                            ),
                            'button' => array(
                                'type' => array(),
                                'class' => array(),
                                'style' => array(),
                            ),
                            'ul' => array(),
                            'li' => array(
                                'class' => array(),
                                'data-type' => array(),
                            ),
                            'input' => array(
                                'data-blog-post-id' => array(),
                                'class' => array(),
                                'name' => array(),
                                'value' => array(),
                                'type' => array(),
                            ),
                            'img' => array(
                                'class' => array(),
                                'alt' => array(),
                                'src' => array(),
                            ),
                            'button' => array(
                                'type' => array(),
                                'class' => array(),
                                'data-post-id' => array(),
                            ),
                            'strong' => array(),
                            'p' => array(
                                'class' => array()
                            )
                        )); ?>
                        </div>
                        <script>
                            var b2s_calendar_locale = '<?php echo esc_js(strtolower(substr(get_locale(), 0, 2))); ?>';
                            var b2s_calendar_date = '<?php echo esc_js(B2S_Util::getbyIdentLocalDate($userTimeZoneOffset, "Y-m-d")); ?>';
                            var b2s_calendar_datetime = '<?php echo esc_js(B2S_Util::getbyIdentLocalDate($userTimeZoneOffset)); ?>';
                            var b2s_has_premium = <?php echo B2S_PLUGIN_USER_VERSION > 0 ? "true" : "false"; ?>;
                            var b2s_plugin_url = '<?php echo esc_url(B2S_PLUGIN_URL); ?>';
                            var b2s_calendar_formats = <?php echo json_encode(array('post' => array(esc_html__('Link Post', 'blog2social'), esc_html__('Image Post', 'blog2social')), 'image' => array(esc_html__('Image with frame', 'blog2social'), esc_html__('Image cut out', 'blog2social')))); ?>;
                            var b2s_is_calendar = true;
                        </script>
                    </div>
                    <div class="col-md-12">
                        <?php
                        $noLegend = 1;
                        require_once (B2S_PLUGIN_DIR . 'views/b2s/html/footer.php');
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<input type="hidden" id="b2sUserVersion" value="<?php echo esc_attr(B2S_PLUGIN_USER_VERSION); ?>" />
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
    var b2s_has_premium = <?php echo B2S_PLUGIN_USER_VERSION > 0 ? "true" : "false"; ?>;
    var b2s_plugin_url = '<?php echo esc_url(B2S_PLUGIN_URL); ?>';
    var b2s_post_formats = <?php echo json_encode(array('post' => array(esc_html__('Link Post', 'blog2social'), esc_html__('Image Post', 'blog2social')), 'image' => array(esc_html__('Image with frame', 'blog2social'), esc_html__('Image cut out','blog2social')))); ?>;
    var b2s_is_calendar = true;
</script>

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
                <button class="btn btn-danger b2s-sched-delete-confirm-multi-btn" style="display:none;"><?php esc_html_e('YES, delete', 'blog2social') ?></button>
            </div>
        </div>
    </div>
</div>

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

<div id="b2s-network-select-image" class="modal fade" role="dialog" aria-labelledby="b2s-network-select-image" aria-hidden="true" style="display:none;z-index: 1070">
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

<div class="modal fade" id="b2sTwitterInfoModal" tabindex="-1" role="dialog" aria-labelledby="b2sTwitterInfoModal" aria-hidden="true" data-backdrop="false"  style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name="#b2sTwitterInfoModal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php esc_html_e('Select Twitter profile:', 'blog2social') ?></h4>
            </div>
            <div class="modal-body">
                <?php esc_html_e('To comply with the Twitter TOS and to avoid duplicate posts, autoposts will be sent to your primary Twitter profile.', 'blog2social') ?> <a target="_blank" href="<?php echo esc_url(B2S_Tools::getSupportLink('network_tos_faq_032018')) ?>"><?php esc_html_e('More information', 'blog2social') ?></a>
            </div>
        </div>
    </div>
</div>

<div id="b2sInfoNetworkModal" class="modal fade" role="dialog" aria-labelledby="b2s-network-select-image" aria-hidden="true" data-backdrop="false"  style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name="#b2sInfoNetworkModal">&times;</button>
                <h4 class="modal-title"><?php esc_html_e('Select image', 'blog2social') ?></h4>
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
                <img class="pull-right hidden-xs b2s-network-info-img-disabled" alt="<?php esc_attr_e('Google Business Profile') ?>" src="<?php echo esc_url(plugins_url('/assets/images/portale/18_flat.png', B2S_PLUGIN_FILE)) ?>">
                <img class="pull-right hidden-xs b2s-network-info-img-disabled" alt="<?php esc_attr_e('Medium') ?>" src="<?php echo esc_url(plugins_url('/assets/images/portale/11_flat.png', B2S_PLUGIN_FILE)) ?>">
                <img class="pull-right hidden-xs b2s-network-info-img-disabled" alt="<?php esc_attr_e('Tumblr') ?>" src="<?php echo esc_url(plugins_url('/assets/images/portale/4_flat.png', B2S_PLUGIN_FILE)) ?>">
                <img class="pull-right hidden-xs b2s-network-info-img-disabled" alt="<?php esc_attr_e('Torial') ?>" src="<?php echo esc_url(plugins_url('/assets/images/portale/14_flat.png', B2S_PLUGIN_FILE)) ?>">
                <img class="pull-right hidden-xs b2s-network-info-img-disabled" alt="<?php esc_attr_e('Bloglovin') ?>" src="<?php echo esc_url(plugins_url('/assets/images/portale/16_flat.png', B2S_PLUGIN_FILE)) ?>">
            </div>
            <p class="b2s-bold"><?php echo sprintf(__('Under <a href="%s">Network Settings</a> you define which network selection is used. <a href="%s" target="_blank">To create a network grouping.</a>', 'blog2social'), 'admin.php?page=blog2social-network', esc_url(B2S_Tools::getSupportLink('network_grouping'))) ?></p>
            <h4><?php esc_html_e('Available networks', 'blog2social') ?></h4>
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
            <p class="b2s-bold"><?php esc_attr_e('Imgur') ?></p>
        </div>
    </div>
</div>