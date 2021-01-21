<?php
require_once (B2S_PLUGIN_DIR . 'includes/B2S/Stats.php');
?>
<div class="b2s-activity-search-content pull-right">
    <?php esc_html_e('Show activity starting from', 'blog2social'); ?> <input id="b2s-activity-date-picker" value="<?php echo (substr(B2S_LANGUAGE, 0, 2) == 'de') ? date('d.m.Y', strtotime("-1 week")) : date('Y-m-d', strtotime("-1 week")); ?>" data-language='<?php echo (substr(B2S_LANGUAGE, 0, 2) == 'de' ? 'de' : 'en'); ?>' />

</div>
<div class="clearfix"></div>

<div id="chart_div" data-text-scheduled="<?php esc_html_e('scheduled social media posts', 'blog2social'); ?>" data-text-published="<?php esc_html_e('published social media posts', 'blog2social'); ?>" data-language='<?php echo (substr(B2S_LANGUAGE, 0, 2) == 'de' ? 'de' : 'en'); ?>'>
    <div class="b2s-loading-area">
        <br>
        <div class="b2s-loader-impulse b2s-loader-impulse-md"></div>
        <div class="clearfix"></div>
    </div>
</div>
