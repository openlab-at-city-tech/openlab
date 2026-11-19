<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$caption = get_post_meta($this->slide->ID, 'ml-slider_caption', true);
$caption = $caption ? $caption : '';
?>
<div class="row">
    <label class="mb-2">
        <?php esc_html_e('Please note that captions on videos can interfere with player controls. We recommend adding captions when "Auto play" is enabled, and "Show controls" and "Lazy load video" are both disabled.', 'ml-slider') ?>
    </label>
    <textarea class="wysiwyg-local-video" id="editor<?php 
        echo esc_attr($this->slide->ID); ?>" name="attachment[<?php 
        echo esc_attr($this->slide->ID); ?>][caption]" data-type="local_video"><?php 
        echo htmlspecialchars($caption); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
    </textarea>
</div>
