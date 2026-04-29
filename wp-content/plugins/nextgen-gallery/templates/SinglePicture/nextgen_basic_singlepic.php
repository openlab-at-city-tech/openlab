<?php

if ( ! empty( $image ) ) {

	$this->start_element( 'nextgen_gallery.gallery_container', 'container', $displayed_gallery );

	$image_size = $storage->get_original_dimensions( $image );

	if ( null === $image_size ) {
		$image_size['width']  = $image->meta_data['width'];
		$image_size['height'] = $image->meta_data['height'];
	}

	$image_ratio = $image_size['width'] / $image_size['height'];

	$width  = isset( $settings['width'] ) ? $settings['width'] : null;
	$height = isset( $settings['height'] ) ? $settings['height'] : null;

	$width  = intval( $width );
	$height = intval( $height );

	if ( $width > 0 && $height > 0 ) {
		// check image aspect ratio, avoid distortions
		$aspect_ratio = $width / $height;
		if ( $image_ratio > $aspect_ratio ) {
			if ( $image_size['width'] > $width ) {
				$height = (int) round( $width / $image_ratio );
			}
		} elseif ( $image_size['height'] > $height ) {
			$width = (int) round( $height * $image_ratio );
		}

		// Ensure that height is always null, or else the image won't be responsive correctly
		$height = null;
	} elseif ( $height > 0 ) {
		$width = (int) round( $height * $image_ratio );
		// Ensure that height is always null, or else the image won't be responsive correctly
		$height = null;
	}

	$style = null;

	if ( $width ) {
		$style .= 'max-width: ' . $width . 'px';
	}

	if ( $height ) {
		$style .= 'max-height: ' . $height . 'px';
	}

	$this->start_element( 'nextgen_gallery.image_panel', 'item', $image );

	?>
	<div class="ngg-gallery-singlepic-image <?php echo esc_attr( $settings['float'] ); ?>" style="<?php echo esc_attr( $style ); ?>">
		<?php $this->start_element( 'nextgen_gallery.image', 'item', $image ); ?>
		<?php
		$show_tiktok_play_button = (
			! empty( $image->meta_data ) &&
			is_array( $image->meta_data ) &&
			! empty( $image->meta_data['imagely_tiktok_id'] ) &&
			! empty( $image->meta_data['imagely_tiktok_show_play_button'] )
		);
		?>
		<a href="<?php echo esc_url( $settings['link'] ); ?>"
			title="<?php echo esc_attr( $image->description ); ?>"
			data-src="<?php echo esc_attr( $storage->get_image_url( $image ) ); ?>"
			data-thumbnail="<?php echo esc_attr( $storage->get_image_url( $image, 'thumb' ) ); ?>"
			data-image-id="<?php echo esc_attr( $image->{$image->id_field} ); ?>"
			data-title="<?php echo esc_attr( $image->alttext ); ?>"
			data-description="<?php echo esc_attr( stripslashes( $image->description ?? '' ) ); ?>"
			<?php if ( ! empty( $image->meta_data['imagely_tiktok_play_url'] ) ) : ?>
				data-tiktok-play-url="<?php echo esc_attr( $image->meta_data['imagely_tiktok_play_url'] ); ?>"
			<?php endif; ?>
			<?php if ( ! empty( $image->meta_data['imagely_tiktok_share_url'] ) ) : ?>
				data-tiktok-share-url="<?php echo esc_attr( $image->meta_data['imagely_tiktok_share_url'] ); ?>"
			<?php endif; ?>
			<?php if ( ! empty( $image->meta_data['imagely_tiktok_embed_link'] ) ) : ?>
				data-tiktok-embed-url="<?php echo esc_attr( $image->meta_data['imagely_tiktok_embed_link'] ); ?>"
			<?php endif; ?>
			<?php if ( ! empty( $image->meta_data['video_link'] ) ) : ?>
				data-video-url="<?php echo esc_attr( $image->meta_data['video_link'] ); ?>"
			<?php endif; ?>
			target='<?php echo esc_attr( $target ); ?>'
			<?php echo $effect_code; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- effect_code is safe HTML attributes from display settings ?>>
			<?php if ( $show_tiktok_play_button || ! empty( $image->meta_data['video_link'] ) ) { ?>
				<span class="ngg-video-play-overlay" aria-hidden="true"></span>
			<?php } ?>
			<img class="ngg-singlepic"
				src="<?php echo esc_attr( $thumbnail_url ); ?>"
				alt="<?php echo esc_attr( \Imagely\NGG\Display\I18N::ngg_plain_text_alt_title_attributes( $image->alttext ) ); ?>"
				title="<?php echo esc_attr( \Imagely\NGG\Display\I18N::ngg_plain_text_alt_title_attributes( $image->alttext ) ); ?>"
				<?php
				if ( $width ) {
					?>
					width="<?php echo esc_attr( $width ); ?>" <?php } ?>
				<?php if ( $height ) { ?>
					height="<?php echo esc_attr( $height ); ?>"
				<?php } ?>
			/>
		</a>
		<?php $this->end_element(); ?>
	</div>
	<?php
	if ( ! is_null( $inner_content ) ) {
		?>
		<span><?php echo wp_kses_post( $inner_content ); ?></span><?php } ?>
	<?php
	$this->end_element();

	$this->end_element();
} else {
	?>
	<p>No image found</p>
	<?php
}
