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
    if (count($slideshow_defaults) === 0) {
        $slideshow_defaults['smartphone'] = '320';
        $slideshow_defaults['tablet'] = '768';
        $slideshow_defaults['laptop'] = '1024';
        $slideshow_defaults['desktop'] = '1440';
    }
    $screen = array('smartphone', 'tablet', 'laptop', 'desktop');
    $default_sizes = array($slideshow_defaults['smartphone'], $slideshow_defaults['tablet'], $slideshow_defaults['laptop'], $slideshow_defaults['desktop']);
?>
<div class="row">
    <label style="margin-right: 20px;"><?php esc_html_e("Hide slide on:", "ml-slider"); ?></label>
    <?php
        $checked_slide = '';
        foreach ($screen as $key => $value) {
            $hideslide = get_post_meta($this->slide->ID, 'ml-slider_hide_slide_' . $value, true);
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
                <input type="checkbox" name="attachment[<?php echo esc_attr($this->slide->ID); ?>][hide_slide_<?php echo esc_attr($value); ?>]" class="mobile-checkbox tipsy-tooltip-top" title="<?php echo esc_attr($tooltip); ?>" aria-label="<?php echo esc_attr(sprintf(/* translators: %s: device name, e.g. Smartphone */ __('Hide video slide on %s', 'ml-slider'), metaslider_device_label($value))); ?>" <?php echo esc_attr($checked_slide); ?> />
                <span class="dashicons <?php echo esc_attr( 'dashicons-' . $value ); ?>"></span>
            </span>
    <?php } ?>
</div>
