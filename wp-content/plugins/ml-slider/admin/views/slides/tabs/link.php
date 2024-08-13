<?php 
if ( ! defined( 'ABSPATH' ) ) {
 die( 'No direct access.' );
} 
?>
<div class="row alt-link mb-2">
    <div class="mb-2">
        <label>
            <?php esc_html_e( 'Image Link URL', 'ml-slider' ) ?>
            <span class="dashicons dashicons-info tipsy-tooltip-top" title="<?php esc_attr_e('When visitors click on your image slide, they will be taken to this URL.', 'ml-slider') ?>" style="line-height: 1.2em;"></span>
        </label>
    </div>
    <div class="row has-right-checkbox mb-0">
        <div>
            <input class="url" data-lpignore="true" type="text" name="attachment[<?php echo esc_attr($slide_id); ?>][url]" placeholder="<?php echo esc_attr("URL", "ml-slider"); ?>" value="<?php echo esc_url($url); ?>" />
        </div>
        <div class="input-label">
            <label>
                <?php 
                esc_html_e( 'New window', 'ml-slider' ); 
                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                echo $this->info_tooltip( __(
                    'Open link in a new window', 
                    'ml-slider'
                ) );
                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                echo $this->switch_button( 
                    'attachment[' . esc_attr( $slide_id ) . '][new_window]', 
                    (bool) $target,
                    array(
                        'autocomplete' => 'off',
                        'tabindex' => '0' 
                    ),
                    'mr-0 ml-2'
                );
                ?>
            </label>
        </div>
    </div>
</div>
<div class="row alt-link">
    <div class="mb-1 link-alt-label">
        <label>
            <?php esc_html_e( 'Image Link Alt Text', 'ml-slider' ); ?>
            <span class="dashicons dashicons-info tipsy-tooltip-top" title="<?php esc_attr_e('This text is used by search engines and visitors using screen readers. Adding Alt text for links is highly recommended.', 'ml-slider') ?>"></span>
        </label>
    </div>
    <input type="text" size="50" name="attachment[<?php echo esc_attr($slide_id); ?>][link-alt]" placeholder="<?php echo esc_attr("Enter text here", "ml-slider"); ?>" value="<?php echo esc_attr($link_alt); ?>">
</div>
