<?php if (!defined('ABSPATH')) die('No direct access.'); ?>

<?php // Handle captions
	if( isset( $_GET['metaslider_add_sample_slides'] ) ){
		$slide_caption = (esc_html( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent vitae.' ));
	} else {
		$slide_caption = (wp_filter_post_kses($this->slide->post_excerpt));
	}

	$image_caption = (wp_filter_post_kses($attachment->post_excerpt));
	$image_description = (wp_filter_post_kses($attachment->post_content));

	// Deprecate inherit_image_caption by deleting it and setting the source as the image
	if (filter_var(get_post_meta($this->slide->ID, 'ml-slider_inherit_image_caption', true), FILTER_VALIDATE_BOOLEAN)) {
		update_post_meta($this->slide->ID, 'ml-slider_caption_source', 'image-caption');
		delete_post_meta($this->slide->ID, 'ml-slider_inherit_image_caption');
	}


	$caption_source = get_post_meta($this->slide->ID, 'ml-slider_caption_source', true); ?>
	<metaslider-caption
		image-caption='<?php echo esc_attr($image_caption); ?>'
		image-description='<?php echo esc_attr($image_description); ?>'
		override='<?php echo esc_attr($slide_caption); ?>'
		caption-source='<?php echo esc_attr($caption_source); ?>'></metaslider-caption>

<?php // Handle URL and target
	$url = get_post_meta($slide_id, 'ml-slider_url', true);
	$target = get_post_meta($slide_id, 'ml-slider_new_window', true);
?>
<div class="row has-right-checkbox">
	<input class="url" data-lpignore="true" type="text" name="attachment[<?php echo esc_attr($slide_id); ?>][url]" placeholder="<?php echo esc_attr("URL", "ml-slider"); ?>" value="<?php echo esc_url($url); ?>" />
	<div class="input-label right new_window">
		<label><?php esc_html_e("Open in a new window", "ml-slider"); ?> <input autocomplete="off" tabindex="0" type="checkbox" name="attachment[<?php echo esc_attr($slide_id); ?>][new_window]" <?php echo $target ? 'checked=checked' : ''; ?> /></label>
	</div>
</div>
