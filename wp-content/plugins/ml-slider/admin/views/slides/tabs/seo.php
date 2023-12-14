<?php if (!defined('ABSPATH')) {
die('No direct access.');
} ?>
<div class="row mb-2 can-inherit title<?php echo esc_attr($inherit_image_title_class); ?>">
    <div class="mb-1 image-title-label">
        <label><?php esc_html_e("Image Title Text", "ml-slider"); ?></label>
        <div class="input-label right">
            <label class="small" title="<?php esc_attr_e("Enable this to inherit the image title", "ml-slider"); ?>">
                <?php 
                esc_html_e( 'Use the image title', 'ml-slider' ); 
                ?> <?php 
                echo $this->switch_button( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    'attachment[' . esc_attr( $slide_id ) . '][inherit_image_title]',
                    $inherit_image_title_check, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    array(
                        'autocomplete' => 'off',
                        'class' => 'js-inherit-from-image' 
                    ),
                    'mr-0 ml-1'
                );
                ?> 
            </label>
        </div>
    </div>
    <div class="default"><?php echo $image_title ? esc_html($image_title) : "<span class='no-content'>&nbsp;</span>"; ?></div>
    <input tabindex="0" type="text" size="50" name="attachment[<?php echo esc_attr($slide_id); ?>][title]" value="<?php echo esc_attr($title); ?>">
</div>
<div class="row can-inherit alt<?php echo esc_attr($inherit_image_alt_class); ?>">
    <div class="mb-1 image-alt-label">
        <label><?php esc_html_e("Image Alt Text", "ml-slider"); ?></label>
        <div class="input-label right">
            <label class="small" title="<?php esc_attr_e('Enable this to inherit the image alt text', 'ml-slider'); ?>">
                <?php 
                esc_html_e( 'Use the image alt text', 'ml-slider' ); 
                ?> <?php 
                echo $this->switch_button( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    'attachment[' . esc_attr( $slide_id ) . '][inherit_image_alt]', 
                    $inherit_image_alt_check, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    array(
                        'autocomplete' => 'off',
                        'class' => 'js-inherit-from-image' 
                    ),
                    'mr-0 ml-1'
                );
                ?> 
            </label>
        </div>
    </div>
    <div class="default"><?php echo $image_alt ? esc_html($image_alt) : "<span class='no-content'>&nbsp;</span>"; ?></div>
    <input tabindex="0" type="text" size="50" name="attachment[<?php echo esc_attr($slide_id); ?>][alt]" value="<?php echo esc_attr($alt); ?>">
</div>
