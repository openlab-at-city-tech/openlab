<?php
$controller = new \Imagely\NGG\DisplayType\Controller();
$storage    = \Imagely\NGG\DataStorage\Manager::get_instance();

$effect_code = $controller->get_effect_code( $gallery->displayed_gallery );
$settings    = $gallery->displayed_gallery->get_entity()->display_settings;

echo wp_kses_post( $settings['widget_setting_before_widget'] )
	. wp_kses_post( $settings['widget_setting_before_title'] )
	. wp_kses_post( $settings['widget_setting_title'] )
	. wp_kses_post( $settings['widget_setting_after_title'] );
?>
<?php // keep the following a/img on the same line ?>
<div class="ngg-widget entry-content">
	<?php foreach ( $images as $image ) { ?>
		<a href="<?php echo esc_attr( $storage->get_image_url( $image, 'full', true ) ); ?>"
			title="<?php echo esc_attr( $image->description ); ?>"
			data-image-id='<?php echo esc_attr( $image->pid ); ?>'
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
			<?php echo $effect_code; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- effect_code is safe HTML attributes from display settings ?>
			><img title="<?php echo esc_attr( \Imagely\NGG\Display\I18N::ngg_plain_text_alt_title_attributes( $image->alttext ) ); ?>"
				alt="<?php echo esc_attr( \Imagely\NGG\Display\I18N::ngg_plain_text_alt_title_attributes( $image->alttext ) ); ?>"
				src="<?php echo esc_attr( $storage->get_image_url( $image, $settings['image_type'], true ) ); ?>"
				width="<?php echo esc_attr( $settings['image_width'] ); ?>"
				height="<?php echo esc_attr( $settings['image_height'] ); ?>"
			/></a>
	<?php } ?>
</div>

<?php echo wp_kses_post( $settings['widget_setting_after_widget'] ); ?>
