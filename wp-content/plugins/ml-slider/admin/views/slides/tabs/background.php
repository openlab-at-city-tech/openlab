<?php
/**
 * Image-slide "Image Styles" tab (image filter + corners/border/shadow/opacity/transform).
 *
 * Rendered by MetaSlider_Image_Styles::add_tab(); expects $slide_id (int),
 * $meta (array) and $preset_labels (array) in scope. Live preview is handled
 * by a delegated listener printed in the admin footer.
 *
 * @package MetaSlider
 */

if (! defined('ABSPATH')) {
    die('No direct access.');
}

/**
 * Render an info tooltip icon (matches the rest of the plugin).
 *
 * @param string $text Tooltip text.
 * @return void
 */
$ms_tip = function ($text) {
    echo '<span class="dashicons dashicons-info tipsy-tooltip-top" title="' . esc_attr($text) . '" style="line-height:1.2em;"></span>';
};

$name = 'attachment[' . esc_attr($slide_id) . ']';
?>
<div class="ms-slide-background-tab">

    <div class="row has-right-field">
        <label>
            <?php esc_html_e('Filter', 'ml-slider'); ?>
            <?php $ms_tip(__('Apply a one-click look. Default uses the slideshow filter.', 'ml-slider')); ?>
        </label>
        <select class="ms-filter-preset" name="<?php echo $name; // phpcs:ignore ?>[filter_preset]">
            <?php foreach ($preset_labels as $key => $label) : ?>
                <option value="<?php echo esc_attr($key); ?>" <?php selected($meta['filter_preset'], $key); ?>><?php echo esc_html($label); ?></option>
            <?php endforeach; ?>
        </select>
    </div>


    <div class="row has-right-field">
        <label><?php esc_html_e('Rounded corners', 'ml-slider'); ?> <?php $ms_tip(__('Round the image corners (px). Leave blank to use the slideshow default.', 'ml-slider')); ?></label>
        <span><input type="number" min="0" max="200" step="1" style="width:90px;" aria-label="<?php esc_attr_e('Rounded corners', 'ml-slider'); ?>" name="<?php echo $name; // phpcs:ignore ?>[corner_radius]" value="<?php echo esc_attr($meta['corner_radius']); ?>" placeholder="<?php esc_attr_e('Default', 'ml-slider'); ?>" /> px</span>
    </div>

    <div class="row has-right-field">
        <label><?php esc_html_e('Border', 'ml-slider'); ?> <?php $ms_tip(__('Border width, style and colour. Leave width blank to use the slideshow default; 0 = no border. If a filter is applied above, it may alter how this colour actually renders.', 'ml-slider')); ?></label>
        <span style="display:inline-flex;align-items:center;gap:10px;flex-wrap:wrap;justify-content:flex-end;">
            <span style="white-space:nowrap;"><input type="number" min="0" max="50" step="1" style="width:90px;" title="<?php esc_attr_e('Width (px)', 'ml-slider'); ?>" aria-label="<?php esc_attr_e('Border width', 'ml-slider'); ?>" name="<?php echo $name; // phpcs:ignore ?>[border_width]" value="<?php echo esc_attr($meta['border_width']); ?>" placeholder="<?php esc_attr_e('Default', 'ml-slider'); ?>" /> <?php esc_html_e('px', 'ml-slider'); ?></span>
            <select title="<?php esc_attr_e('Style', 'ml-slider'); ?>" name="<?php echo $name; // phpcs:ignore ?>[border_style]">
                <?php foreach (array('default' => __('Default', 'ml-slider'), 'none' => __('None', 'ml-slider'), 'solid' => __('Solid', 'ml-slider'), 'dashed' => __('Dashed', 'ml-slider'), 'dotted' => __('Dotted', 'ml-slider'), 'double' => __('Double', 'ml-slider')) as $bk => $bl) : ?>
                    <option value="<?php echo esc_attr($bk); ?>" <?php selected($meta['border_style'], $bk); ?>><?php echo esc_html($bl); ?></option>
                <?php endforeach; ?>
            </select>
            <div class="ms-color-tooltip-wrapper">
                <input class="border_color colorpicker" type="text" data-alpha-enabled="true" title="<?php esc_attr_e('Colour', 'ml-slider'); ?>" aria-label="<?php esc_attr_e('Border colour', 'ml-slider'); ?>" name="<?php echo $name; // phpcs:ignore ?>[border_color]" value="<?php echo esc_attr($meta['border_color']); ?>" placeholder="<?php esc_attr_e('Default', 'ml-slider'); ?>" />
            </div>
        </span>
    </div>

    <div class="row has-right-field">
        <label><?php esc_html_e('Box shadow', 'ml-slider'); ?> <?php $ms_tip(__('Add a drop shadow to the image. Default uses the slideshow setting.', 'ml-slider')); ?></label>
        <select name="<?php echo $name; // phpcs:ignore ?>[box_shadow]">
            <?php foreach (array('default' => __('Default', 'ml-slider'), 'none' => __('None', 'ml-slider'), 'light' => __('Light', 'ml-slider'), 'medium' => __('Medium', 'ml-slider'), 'heavy' => __('Heavy', 'ml-slider')) as $sk => $sl) : ?>
                <option value="<?php echo esc_attr($sk); ?>" <?php selected($meta['box_shadow'], $sk); ?>><?php echo esc_html($sl); ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="row has-right-field">
        <label><?php esc_html_e('Opacity', 'ml-slider'); ?> <?php $ms_tip(__('Image transparency (%). 100 = fully visible. Leave blank to use the slideshow default.', 'ml-slider')); ?></label>
        <span><input type="number" min="0" max="100" step="1" style="width:90px;" aria-label="<?php esc_attr_e('Opacity', 'ml-slider'); ?>" name="<?php echo $name; // phpcs:ignore ?>[opacity]" value="<?php echo esc_attr($meta['opacity']); ?>" placeholder="<?php esc_attr_e('Default', 'ml-slider'); ?>" /> %</span>
    </div>

    <div class="row has-right-field">
        <label><?php esc_html_e('Rotate', 'ml-slider'); ?> <?php $ms_tip(__('Rotate the image. Default uses the slideshow setting.', 'ml-slider')); ?></label>
        <select name="<?php echo $name; // phpcs:ignore ?>[rotate]">
            <?php foreach (array('default' => __('Default', 'ml-slider'), '0' => __('None', 'ml-slider'), '90' => '90°', '180' => '180°', '270' => '270°') as $rk => $rl) : ?>
                <option value="<?php echo esc_attr($rk); ?>" <?php selected($meta['rotate'], $rk); ?>><?php echo esc_html($rl); ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="row has-right-field">
        <label><?php esc_html_e('Flip', 'ml-slider'); ?> <?php $ms_tip(__('Mirror the image horizontally and/or vertically. Default uses the slideshow setting.', 'ml-slider')); ?></label>
        <select name="<?php echo $name; // phpcs:ignore ?>[flip]">
            <?php foreach (array('default' => __('Default', 'ml-slider'), 'none' => __('None', 'ml-slider'), 'h' => __('Horizontal', 'ml-slider'), 'v' => __('Vertical', 'ml-slider'), 'both' => __('Both', 'ml-slider')) as $fk => $fl) : ?>
                <option value="<?php echo esc_attr($fk); ?>" <?php selected($meta['flip'], $fk); ?>><?php echo esc_html($fl); ?></option>
            <?php endforeach; ?>
        </select>
    </div>

</div>
