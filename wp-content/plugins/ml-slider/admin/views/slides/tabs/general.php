<?php if (!defined('ABSPATH')) die('No direct access.'); ?>

<div class="thumb-col-settings">
	<?php // Handle captions
	if( isset( $_GET['metaslider_add_sample_slides'] ) ){
		$slide_caption = (esc_html( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent vitae.' ));
	} else {
		$slide_caption = $this->cleanup_content_kses( $this->slide->post_excerpt );
	}

	$image_caption = (wp_filter_post_kses($attachment->post_excerpt));
	$image_description = (wp_filter_post_kses($attachment->post_content));

	// Deprecate inherit_image_caption by deleting it and setting the source as the image
	if (filter_var(get_post_meta($this->slide->ID, 'ml-slider_inherit_image_caption', true), FILTER_VALIDATE_BOOLEAN)) {
		update_post_meta($this->slide->ID, 'ml-slider_caption_source', 'image-caption');
		delete_post_meta($this->slide->ID, 'ml-slider_inherit_image_caption');
	}

	$caption_source = get_post_meta($this->slide->ID, 'ml-slider_caption_source', true); 
	
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $this->get_admin_slide_thumb();
	?>
	
	<div>
		<metaslider-caption
			image-caption="<?php echo esc_attr($image_caption); ?>"
			image-description="<?php echo esc_attr($image_description); ?>"
			override="<?php echo esc_attr($slide_caption); ?>"
			caption-source="<?php echo esc_attr($caption_source); ?>"></metaslider-caption>
	</div>
</div>
