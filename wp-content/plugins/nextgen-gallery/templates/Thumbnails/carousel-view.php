<?php
$_carousel_gallery_id = null;
if ( isset( $displayed_gallery->container_ids ) && ! empty( $displayed_gallery->container_ids ) ) {
	$_carousel_gallery_id = reset( $displayed_gallery->container_ids );
}
$_carousel_data_gallery_id = $_carousel_gallery_id ? $_carousel_gallery_id : $displayed_gallery_id;

$_carousel_gallery_name = '';
if ( isset( $gallery ) && ! empty( $gallery->title ) ) {
	$_carousel_gallery_name = $gallery->title;
} elseif ( $_carousel_gallery_id ) {
	$_ngg_carousel_gallery = \Imagely\NGG\DataMappers\Gallery::get_instance()->find( intval( $_carousel_gallery_id ) );
	if ( $_ngg_carousel_gallery ) {
		$_carousel_gallery_name = $_ngg_carousel_gallery->title;
	}
}
?>
<?php $this->start_element( 'nextgen_gallery.gallery_container', 'container', $displayed_gallery ); ?>

<div class="ngg-galleryoverview carousel-view
<?php
if ( ! intval( $ajax_pagination ) ) {
	echo esc_attr( ' ngg-ajax-pagination-none' );}
?>
"
	id="ngg-gallery-<?php echo esc_attr( $displayed_gallery_id ); ?>-<?php echo esc_attr( $current_page ); ?>"
	data-nextgen-gallery-id="<?php echo esc_attr( $_carousel_data_gallery_id ); ?>"
	data-gallery-id="<?php echo esc_attr( $_carousel_data_gallery_id ); ?>"
	<?php if ( $_carousel_gallery_name ) : ?>
	data-gallery-name="<?php echo esc_attr( $_carousel_gallery_name ); ?>"
	<?php endif; ?>>

	<div class="ngg-basic-thumbnails-carousel">
		<?php
		$image_size                      = $storage->get_image_dimensions( $current_image, 'full' );
		$show_tiktok_play_button_current = (
			! empty( $current_image->meta_data ) &&
			is_array( $current_image->meta_data ) &&
			! empty( $current_image->meta_data['imagely_tiktok_id'] ) &&
			! empty( $current_image->meta_data['imagely_tiktok_show_play_button'] )
		);
		?>
		<a href="<?php echo esc_attr( $storage->get_image_url( $current_image, 'full', true ) ); ?>"
			title="<?php echo esc_attr( $current_image->description ); ?>"
			data-src="<?php echo esc_attr( $storage->get_image_url( $current_image ) ); ?>"
			data-thumbnail="<?php echo esc_attr( $storage->get_image_url( $current_image, 'thumb' ) ); ?>"
			data-image-id="<?php echo esc_attr( $current_image->{$current_image->id_field} ); ?>"
			data-image-name="<?php echo esc_attr( $current_image->filename ?? '' ); ?>"
			data-title="<?php echo esc_attr( $current_image->alttext ); ?>"
			data-description="<?php echo esc_attr( stripslashes( $current_image->description ?? '' ) ); ?>"
			data-image-slug="<?php echo esc_attr( $current_image->image_slug ); ?>"
			<?php if ( ! empty( $current_image->meta_data['imagely_tiktok_play_url'] ) ) : ?>
				data-tiktok-play-url="<?php echo esc_attr( $current_image->meta_data['imagely_tiktok_play_url'] ); ?>"
			<?php endif; ?>
			<?php if ( ! empty( $current_image->meta_data['imagely_tiktok_share_url'] ) ) : ?>
				data-tiktok-share-url="<?php echo esc_attr( $current_image->meta_data['imagely_tiktok_share_url'] ); ?>"
			<?php endif; ?>
			<?php if ( ! empty( $current_image->meta_data['imagely_tiktok_embed_link'] ) ) : ?>
				data-tiktok-embed-url="<?php echo esc_attr( $current_image->meta_data['imagely_tiktok_embed_link'] ); ?>"
			<?php endif; ?>
			<?php if ( ! empty( $current_image->meta_data['video_link'] ) ) : ?>
				data-video-url="<?php echo esc_attr( $current_image->meta_data['video_link'] ); ?>"
			<?php endif; ?>
			<?php echo $effect_code; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- effect_code is safe HTML attributes from display settings ?>>
			<?php if ( $show_tiktok_play_button_current ) : ?>
				<span class="ngg-video-play-overlay" aria-hidden="true"></span>
			<?php endif; ?>
			<img title="<?php echo esc_attr( \Imagely\NGG\Display\I18N::ngg_plain_text_alt_title_attributes( $current_image->alttext ) ); ?>"
				alt="<?php echo esc_attr( \Imagely\NGG\Display\I18N::ngg_plain_text_alt_title_attributes( $current_image->alttext ) ); ?>"
				src="<?php echo esc_attr( $storage->get_image_url( $current_image, 'full' ) ); ?>"
				width="<?php echo esc_attr( $image_size['width'] ); ?>"
				height="<?php echo esc_attr( $image_size['height'] ); ?>"
				style="max-width: <?php print esc_attr( $image_size['width'] ); ?>px;"/>
		</a>
	</div>

	<div class="ngg-basic-thumbnails-carousel-list">
		<?php $this->start_element( 'nextgen_gallery.image_list_container', 'container', $images ); ?>
			<?php

			$application = \Imagely\NGG\Util\Router::get_instance()->get_routed_app();
			$controller  = new \Imagely\NGG\DisplayTypes\Thumbnails();

			$image_count = count( $images );
			for ( $i = 0; $i < $image_count; $i++ ) {
				$image                   = $images[ $i ];
				$thumb_size              = $storage->get_image_dimensions( $image, $thumbnail_size_name );
				$show_tiktok_play_button = (
					! empty( $image->meta_data ) &&
					is_array( $image->meta_data ) &&
					! empty( $image->meta_data['imagely_tiktok_id'] ) &&
					! empty( $image->meta_data['imagely_tiktok_show_play_button'] )
				);
				$style                   = isset( $image->style ) ? $image->style : null;

				if ( isset( $image->hidden ) && $image->hidden ) {
					$style = 'style="display: none;"';
				} else {
					$style = null;
				}

				$this->start_element( 'nextgen_gallery.image_panel', 'item', $image );
				?>
					<div id="<?php echo esc_attr( 'ngg-image-' . $i ); ?>"
						class="ngg-basic-thumbnails-carousel-thumbnail"
						<?php
						if ( $style ) {
							echo esc_attr( $style );}
						?>
						>
						<?php $this->start_element( 'nextgen_gallery.image', 'item', $image ); ?>
						<?php $href = $controller->set_param_for( $application->get_routed_url( true ), 'pid', $image->image_slug ); ?>
						<div class="ngg-gallery-thumbnail">
							<a href="<?php echo esc_attr( $href ); ?>"
								title="<?php echo esc_attr( $image->description ); ?>"
								data-image-id="<?php echo esc_attr( $image->{$image->id_field} ); ?>"
								data-image-name="<?php echo esc_attr( $image->filename ?? '' ); ?>"
								data-title="<?php echo esc_attr( $image->alttext ); ?>">
								<?php if ( $show_tiktok_play_button || ! empty( $image->meta_data['video_link'] ) ) { ?>
									<span class="ngg-video-play-overlay" aria-hidden="true"></span>
								<?php } ?>
								<img title="<?php echo esc_attr( \Imagely\NGG\Display\I18N::ngg_plain_text_alt_title_attributes( $image->alttext ) ); ?>"
										alt="<?php echo esc_attr( \Imagely\NGG\Display\I18N::ngg_plain_text_alt_title_attributes( $image->alttext ) ); ?>"
										src="<?php echo esc_attr( $storage->get_image_url( $image, $thumbnail_size_name ) ); ?>"
										width="<?php echo esc_attr( $thumb_size['width'] ); ?>"
										height="<?php echo esc_attr( $thumb_size['height'] ); ?>"/>
								</a>
							</div>
						<?php $this->end_element(); ?>
					</div>
				<?php $this->end_element(); ?>
			<?php } ?>
		<?php $this->end_element(); ?>
	</div>

	<?php if ( ! empty( $slideshow_link ) ) { ?>
		<div class="slideshowlink">
			<a href='<?php echo esc_attr( $slideshow_link ); ?>'><?php echo esc_html( $slideshow_link_text ); ?></a>
		</div>
	<?php } ?>

	<?php if ( $pagination ) { ?>
		<?php echo wp_kses_post( $pagination ); ?>
	<?php } ?>
</div>

<?php $this->end_element(); ?>
