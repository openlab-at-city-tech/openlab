<?php
    if (!defined('ABSPATH')) {
        die('No direct access.');
    }
    $slideshow_defaults = array();
    if (is_multisite() && $settings = get_site_option('metaslider_default_settings')) {
        $slideshow_defaults = $settings;
    }
    if ($settings = get_option('metaslider_default_settings')) {
        $slideshow_defaults = $settings;
    }
    if(!isset($slideshow_defaults['smartphone'])){
        $slideshow_defaults['smartphone'] = 320;
    }
    if(!isset($slideshow_defaults['tablet'])){
        $slideshow_defaults['tablet'] = 768;
    }
    if(!isset($slideshow_defaults['laptop'])){
        $slideshow_defaults['laptop'] = 1024;
    }
    if(!isset($slideshow_defaults['desktop'])){
        $slideshow_defaults['desktop'] = 1440;
    }
    $screen = array('smartphone', 'tablet', 'laptop', 'desktop');
    $default_sizes = array($slideshow_defaults['smartphone'], $slideshow_defaults['tablet'], $slideshow_defaults['laptop'], $slideshow_defaults['desktop']);
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

            if($key == 3){
                $tooltip = sprintf( 
                    __( 
                        'When enabled this setting will hide the slide on screen widths equal to or greater than %spx', 
                        'ml-slider'
                    ), 
                    $default_sizes[$key] 
                );
            } else {
                $maxkey = $key + 1;
                $max_width = $default_sizes[$maxkey] - 1;
                $tooltip = sprintf( 
                    __( 
                        'When enabled this setting will hide the slide on screen widths of %1$spx to %2$spx', 
                        'ml-slider'
                    ), 
                    $default_sizes[$key],
                    $max_width
                );
            }
    ?>
            <span class="mobile-checkbox-wrap">
                <input type="checkbox" name="attachment[<?php echo esc_attr($slide_id); ?>][hide_slide_<?php echo esc_attr($value); ?>]" class="mobile-checkbox tipsy-tooltip-top" title="<?php echo esc_attr($tooltip); ?>" <?php echo esc_attr($checked_slide); ?> />
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
            
            if($key == 3){
                $tooltip = sprintf( 
                    __( 
                        'When enabled this setting will hide the caption on screen widths equal to or greater than %spx', 
                        'ml-slider'
                    ), 
                    $default_sizes[$key] 
                );
            } else {
                $maxkey = $key + 1;
                $max_width = $default_sizes[$maxkey] - 1;
                $tooltip = sprintf( 
                    __( 
                        'When enabled this setting will hide the caption on screen widths of %1$spx to %2$spx', 
                        'ml-slider'
                    ), 
                    $default_sizes[$key],
                    $max_width
                );
            }
    ?>
            <span class="mobile-checkbox-wrap">
                <input type="checkbox" name="attachment[<?php echo esc_attr($slide_id); ?>][hide_caption_<?php echo esc_attr($value); ?>]" class="mobile-checkbox tipsy-tooltip-top" title="<?php echo esc_attr($tooltip); ?>" <?php echo esc_attr($checked_caption); ?> />
                <span class="dashicons <?php echo esc_attr( 'dashicons-' . $value ); ?> "></span>
            </span>
    <?php } ?>
</div>
