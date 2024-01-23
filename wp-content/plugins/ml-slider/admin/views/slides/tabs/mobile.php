<?php
    if (!defined('ABSPATH')) {
        die('No direct access.');
    }
    $screen = array('smartphone', 'tablet', 'laptop', 'desktop');
    $settings = get_post_meta($this->slider->ID, 'ml-slider_settings', true);
    $hide_css = 'display: none';
    if (isset($settings['type']) && $settings['type'] == 'flex') {
        $hide_css = 'display: block';
    }
?>
<div class="row flex-setting" style="<?php echo esc_attr($hide_css); ?>">
    <label style="margin-right: 20px;"><?php esc_html_e("Hide slide on:", "ml-slider"); ?></label>
    <?php
        $checked_slide = '';
        foreach ($screen as $key => $value) {
            $hideslide = get_post_meta($slide_id, 'ml-slider_hide_slide_' . $value, true);
            if (!empty($hideslide)) {
                $checked_slide = 'checked = "checked"';
            } else {
                $checked_slide = '';
            }
    ?>
            <span class="mobile-checkbox-wrap">
                <input type="checkbox" name="attachment[<?php echo esc_attr($slide_id); ?>][hide_slide_<?php echo esc_attr($value); ?>]" class="mobile-checkbox" <?php echo esc_attr($checked_slide); ?> />
                <span class="dashicons <?php echo esc_attr( 'dashicons-' . $value ); ?>"></span>
            </span>
    <?php } ?>
</div>
<div class="row">
    <label style="margin-right: 4px;"><?php esc_html_e("Hide caption on:", "ml-slider"); ?></label>
    <?php
        $checked_caption = '';
        foreach ($screen as $key => $value) {
            $hidecaption = get_post_meta($slide_id, 'ml-slider_hide_caption_' . $value, true);
            if (!empty($hidecaption)) {
                $checked_caption = 'checked = "checked"';
            } else {
                $checked_caption = '';
            }      
    ?>
            <span class="mobile-checkbox-wrap">
                <input type="checkbox" name="attachment[<?php echo esc_attr($slide_id); ?>][hide_caption_<?php echo esc_attr($value); ?>]" class="mobile-checkbox" <?php echo esc_attr($checked_caption); ?> />
                <span class="dashicons <?php echo esc_attr( 'dashicons-' . $value ); ?>"></span>
            </span>
    <?php } ?>
</div>
