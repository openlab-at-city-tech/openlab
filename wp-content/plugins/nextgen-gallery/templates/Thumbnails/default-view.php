<?php
$gallery_id = null;
if ( isset( $displayed_gallery->container_ids ) && ! empty( $displayed_gallery->container_ids ) ) {
	$gallery_id = reset( $displayed_gallery->container_ids );
}
$data_gallery_id = $gallery_id ? $gallery_id : $displayed_gallery_id;

// Resolve gallery name for GA4 tracking.
$data_gallery_name = '';
if ( isset( $gallery ) && ! empty( $gallery->title ) ) {
	$data_gallery_name = $gallery->title;
} elseif ( $gallery_id ) {
	$_ngg_gallery = \Imagely\NGG\DataMappers\Gallery::get_instance()->find( intval( $gallery_id ) );
	if ( $_ngg_gallery ) {
		$data_gallery_name = $_ngg_gallery->title;
	}
}

$this->start_element( 'nextgen_gallery.gallery_container', 'container', $displayed_gallery );
?>
<!-- default-view.php -->
<div
	class="ngg-galleryoverview default-view
	<?php
	if ( ! intval( $ajax_pagination ) ) {
		echo esc_attr( ' ngg-ajax-pagination-none' );}
	?>
	"
	id="ngg-gallery-<?php echo esc_attr( $displayed_gallery_id ); ?>-<?php echo esc_attr( $current_page ); ?>"
	data-nextgen-gallery-id="<?php echo esc_attr( (string) $data_gallery_id ); ?>"
	data-gallery-id="<?php echo esc_attr( (string) $data_gallery_id ); ?>"
	<?php if ( $data_gallery_name ) : ?>
	data-gallery-name="<?php echo esc_attr( $data_gallery_name ); ?>"
	<?php endif; ?>>

	<?php

	$this->start_element( 'nextgen_gallery.image_list_container', 'container', $images );

	?>
	<!-- Thumbnails -->
	<?php
	$image_count = count( $images );
	for ( $i = 0; $i < $image_count; $i++ ) :
		$image                   = $images[ $i ];
		$thumb_size              = $storage->get_image_dimensions( $image, $thumbnail_size_name );
		$show_tiktok_play_button = (
			! empty( $image->meta_data ) &&
			is_array( $image->meta_data ) &&
			! empty( $image->meta_data['imagely_tiktok_id'] ) &&
			! empty( $image->meta_data['imagely_tiktok_show_play_button'] )
		);

		// Ensure thumb_size has valid dimensions (prevent null array access)
		if ( ! is_array( $thumb_size ) || ! isset( $thumb_size['width'], $thumb_size['height'] ) ) {
			$thumb_size = array(
				'width'  => 150, // Default thumbnail width
				'height' => 150, // Default thumbnail height
			);
		}

		$style        = isset( $image->style ) ? $image->style : null;
		$column_class = 'ngg-' . $number_of_columns . '-columns';

		if ( isset( $image->hidden ) && $image->hidden ) {
			$style = 'style="display: none;"';
		} else {
			$style = null;
		}

			$this->start_element( 'nextgen_gallery.image_panel', 'item', $image );

		?>
			<div id="<?php echo esc_attr( 'ngg-image-' . $i ); ?>" class="ngg-gallery-thumbnail-box
								<?php
								if ( $number_of_columns > 0 && empty( $show_all_in_lightbox ) ) {
									echo esc_attr( $column_class ); }
								?>
			"
			<?php
			if ( $style ) {
						echo esc_attr( $style );}
			?>
>
				<?php

				$this->start_element( 'nextgen_gallery.image', 'item', $image );

				?>
		<div class="ngg-gallery-thumbnail">
		<a href="<?php echo esc_attr( $storage->get_image_url( $image, 'full', true ) ); ?>"
			title="<?php echo esc_attr( $image->description ); ?>"
			data-src="<?php echo esc_attr( $storage->get_image_url( $image ) ); ?>"
			data-thumbnail="<?php echo esc_attr( $storage->get_image_url( $image, 'thumb' ) ); ?>"
			data-image-id="<?php echo esc_attr( $image->{$image->id_field} ); ?>"
			data-image-name="<?php echo esc_attr( $image->filename ?? '' ); ?>"
			data-title="<?php echo esc_attr( $image->alttext ); ?>"
			data-description="<?php echo esc_attr( stripslashes( $image->description ?? '' ) ); ?>"
			data-image-slug="<?php echo esc_attr( $image->image_slug ); ?>"
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
				<?php echo $effect_code; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- effect_code is safe HTML attributes from display settings ?>>
				<?php if ( $show_tiktok_play_button || ! empty( $image->meta_data['video_link'] ) ) : ?>
					<span class="ngg-video-play-overlay" aria-hidden="true"></span>
				<?php endif; ?>
				<img
					title="<?php echo esc_attr( \Imagely\NGG\Display\I18N::ngg_plain_text_alt_title_attributes( $image->alttext ) ); ?>"
					alt="<?php echo esc_attr( \Imagely\NGG\Display\I18N::ngg_plain_text_alt_title_attributes( $image->alttext ) ); ?>"
					src="<?php echo esc_attr( $storage->get_image_url( $image, $thumbnail_size_name ) ); ?>"
					width="<?php echo esc_attr( $thumb_size['width'] ); ?>"
					height="<?php echo esc_attr( $thumb_size['height'] ); ?>"
					style="max-width:100%;"
				/>
			</a>
		</div>
				<?php

				$this->end_element();

				?>
			</div>
			<?php

			$this->end_element();

			?>

	<?php endfor ?>

	<br style="clear: both" />

	<?php

	$this->end_element();

	if ( ! empty( $slideshow_link ) ) :
		?>
	<div class="slideshowlink">
		<a href='<?php echo esc_attr( $slideshow_link ); ?>'><?php echo esc_html( $slideshow_link_text ); ?></a>

	</div>
	<?php endif ?>

	<?php if ( $pagination ) : ?>
	<!-- Pagination -->
		<?php echo wp_kses_post( $pagination ); ?>
	<?php else : ?>
	<div class="ngg-clear"></div>
	<?php endif ?>
</div>
<?php $this->end_element(); ?>
