<?php if (!defined('ABSPATH')) {
die('No direct access.');
}

// Set by MetaSlide::add_pro_upsell_tabs() - rows Pro can't apply to this slide type
$unsupported_advanced_rows = isset($unsupported_advanced_rows) ? (array) $unsupported_advanced_rows : array();
?>
<?php if (!in_array('delay', $unsupported_advanced_rows, true)) : ?>
<div class="row delay advanced-setting">
    <div class="ms-switch-button">
        <label>
            <input type="checkbox" class="delay-slide mr-0" disabled> <span class="opacity-50"></span>
        </label>
    </div>
    <label class="delay-slide">
        <?php esc_html_e('Custom delay for this slide', 'ml-slider') ?><span class="dashicons dashicons-info tipsy-tooltip-top" original-title="<?php esc_attr_e(
            'Requires the Auto play setting to be enabled for this slideshow. When active on video slides, playback (by user or video autoplay) controls when the slideshow advances.', 
            'ml-slider'
        ) ?>" style="line-height: 1.2em;"></span>
        <?php
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo metaslider_upgrade_pro_small_btn(
            __( 'Custom delay is available in MetaSlider Slideshow Pro', 'ml-slider' )
        );
        ?> 
    </label>
</div>
<?php endif; ?>
<?php if (!in_array('repeat', $unsupported_advanced_rows, true)) : ?>
<div class="row repeat advanced-setting">
    <div class="ms-switch-button">
        <label>
            <input type="checkbox" class="repeat-slide mr-0" disabled> <span class="opacity-50"></span>
        </label>
    </div>
    <label class="repeat-slide">
        <?php esc_html_e('Repeat this slide', 'ml-slider') ?><span class="dashicons dashicons-info tipsy-tooltip-top" original-title="<?php esc_attr_e(
            'The slide is repeated after the specified number of original slides until the end of the slider. If multiple slides have this feature enabled, only the original (non-repeated) slides are considered when determining the positions of the repeated slides.', 
            'ml-slider'
        ) ?>" style="line-height: 1.2em;"></span>
        <?php 
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo metaslider_upgrade_pro_small_btn(
            __( 'Repeat slide is available in MetaSlider Slideshow Pro', 'ml-slider' )
        );
        ?> 
    </label>
</div>
<?php endif; ?>
<?php if (!in_array('first_loop', $unsupported_advanced_rows, true)) : ?>
<div class="row first_loop advanced-setting">
    <div class="ms-switch-button">
        <label>
            <input type="checkbox" class="first-loop-slide mr-0" disabled> <span class="opacity-50"></span>
        </label>
    </div>
    <label class="first-loop-slide">
        <?php esc_html_e('Show only in the first loop', 'ml-slider') ?><span class="dashicons dashicons-info tipsy-tooltip-top" original-title="<?php esc_attr_e(
            'The slide is only shown during the slideshow\'s first loop, then skipped on every following loop.',
            'ml-slider'
        ) ?>" style="line-height: 1.2em;"></span>
        <?php
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo metaslider_upgrade_pro_small_btn(
            __( 'Show only in the first loop is available in MetaSlider Slideshow Pro', 'ml-slider' )
        );
        ?>
    </label>
</div>
<?php endif; ?>
<?php if (!in_array('thumbnail', $unsupported_advanced_rows, true)) : ?>
<div class="row thumbnail advanced-setting">
    <div class="ms-switch-button">
        <label>
            <input type="checkbox" class="thumbnail-slide mr-0" disabled> <span class="opacity-50"></span>
        </label>
    </div>
    <label class="thumbnail-slide">
        <?php esc_html_e('Custom thumbnail', 'ml-slider') ?><span class="dashicons dashicons-info tipsy-tooltip-top" original-title="<?php esc_attr_e(
            'When Navigation is Thumbnails or Filmstrip, select a thumbnail to replace the default one.', 
            'ml-slider'
        ) ?>" style="line-height: 1.2em;"></span>
        <?php 
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo metaslider_upgrade_pro_small_btn(
            __( 'Custom thumbnail is available in MetaSlider Slideshow Pro', 'ml-slider' )
        );
        ?> 
    </label>
</div>
<?php endif; ?>
<?php if (!in_array('classes', $unsupported_advanced_rows, true)) : ?>
<div class="row classes advanced-setting">
    <div class="ms-switch-button">
        <label>
            <input type="checkbox" class="classes-slide mr-0" disabled> <span class="opacity-50"></span>
        </label>
    </div>
    <label class="classes-slide">
        <?php esc_html_e('Custom CSS classes', 'ml-slider') ?><span class="dashicons dashicons-info tipsy-tooltip-top" original-title="<?php esc_attr_e(
            'Add custom CSS classes separated with empty spaces to slide li tag.',
            'ml-slider'
        ) ?>" style="line-height: 1.2em;"></span>
        <?php 
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo metaslider_upgrade_pro_small_btn(
            __( 'Custom CSS classes is available in MetaSlider Slideshow Pro', 'ml-slider' )
        );
        ?> 
    </label>
</div><?php endif; ?>
