<?php
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
<input type="hidden" id="b2sRedirectUrlContentCuration" value="<?php echo esc_attr(get_option('siteurl') . ((substr(get_option('siteurl'), -1, 1) == '/') ? '' : '/') . 'wp-admin/admin.php?page=blog2social-curation'); ?>">
<input type="hidden" id="b2sNotAllowGif" value="<?php echo esc_attr(implode(";", json_decode(B2S_PLUGIN_NETWORK_NOT_ALLOW_GIF, true))); ?>">
<input type="hidden" id="b2sAnimateGif" value='<?php echo esc_attr(B2S_PLUGIN_NETWORK_ANIMATE_GIF); ?>'>
<input type="hidden" id="ogMetaNetworks" value="<?php echo esc_attr(implode(';', json_decode(B2S_PLUGIN_NETWORK_META_TAGS, true)['og'])); ?>">
<input type="hidden" id="b2sEmojiTranslation" value='<?php echo esc_attr(json_encode(B2S_Tools::getEmojiTranslationList())); ?>'>
<!--Routing from dashboard-->
<input type="hidden" id="b2s_rfd" value="<?php echo (isset($_GET['rfd'])) ? 1 : 0; ?>">
<input type="hidden" id="b2s_rfd_b2s_id" value="<?php echo (isset($_GET['b2s_id'])) ? esc_attr(sanitize_text_field($_GET['b2s_id'])) : ""; ?>">

<div class="col-md-12 b2s-calendar-filter form-inline del-padding-left del-padding-right">
    <div class="b2s-calendar-filter-network-legend-text">
        <?php esc_html_e('Sort by network', 'blog2social'); ?>
        <select id="b2s-calendar-filter-status" class="form-control pull-right">
            <option selected value="0"><?php esc_html_e('show all', 'blog2social'); ?></option>
            <option value="1"><?php esc_html_e('published', 'blog2social'); ?></option>
            <option value="2"><?php esc_html_e('scheduled', 'blog2social'); ?></option>
        </select>
    </div>
    <br>
    <div class="clearfix"></div>
    <?php
    $filter = new B2S_Calendar_Filter();
    $filterNetwork = $filter->getNetworkHtml();
    if (!empty($filterNetwork)) {
        ?>
        <div class="b2s-calendar-filter-network-list hidden-xs">
            <?php echo wp_kses($filterNetwork, array(
                'label' => array(),
                'input' => array(
                    'type' => array(),
                    'class' => array(),
                    'name' => array(),
                    'value' => array(),
                    'checked' => array()
                ),
                'span' => array(),
                'img' => array(
                    'class' => array(),
                    'alt' => array(),
                    'src' => array()
                )
            )) ?>
        </div>
        <div class="b2s-calendar-filter-network-account-list"></div>
    <?php }
    ?>
</div>
<br>
<div class="b2s-widget-calendar"></div>
<script>
    var b2s_calendar_locale = '<?php echo esc_js(strtolower(substr(get_locale(), 0, 2))); ?>';
    var b2s_calendar_date = '<?php echo esc_js(B2S_Util::getbyIdentLocalDate($userTimeZoneOffset, "Y-m-d")); ?>';
    var b2s_calendar_datetime = '<?php echo esc_js(B2S_Util::getbyIdentLocalDate($userTimeZoneOffset)); ?>';
    var b2s_has_premium = <?php echo B2S_PLUGIN_USER_VERSION > 0 ? "true" : "false"; ?>;
    var b2s_plugin_url = '<?php echo esc_url(B2S_PLUGIN_URL); ?>';
    var b2s_calendar_formats = <?php echo json_encode(array('post' => array(esc_html__('Link Post', 'blog2social'), esc_html__('Image Post', 'blog2social')), 'image' => array(esc_html__('Image with frame', 'blog2social'), esc_html__('Image cut out', 'blog2social')))); ?>;
    var b2s_is_calendar = true;
</script>