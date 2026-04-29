<?php
/**
 * Slideshow Display Template.
 *
 * @package Nextgen Gallery
 * @var C_Displayed_Gallery $displayed_gallery
 * @var \Imagely\NGG\DataStorage\Manager $storage
 * @var array $images
 * @var bool $show_thumbnail_link
 * @var int $current_page
 * @var int $gallery_height
 * @var int $gallery_width
 * @var string $anchor
 * @var string $displayed_gallery_id
 * @var string $effect_code
 * @var string $placeholder
 * @var string $thumbnail_link
 * @var string $thumbnail_link_text
 */ ?>
<?php
$_slide_gallery_id = null;
if ( isset( $displayed_gallery->container_ids ) && ! empty( $displayed_gallery->container_ids ) ) {
	$_slide_gallery_id = reset( $displayed_gallery->container_ids );
}
$_slide_data_gallery_id = $_slide_gallery_id ? $_slide_gallery_id : $displayed_gallery_id;

$_slide_gallery_name = '';
if ( isset( $gallery ) && ! empty( $gallery->title ) ) {
	$_slide_gallery_name = $gallery->title;
} elseif ( $_slide_gallery_id ) {
	$_slide_ngg_gallery = \Imagely\NGG\DataMappers\Gallery::get_instance()->find( intval( $_slide_gallery_id ) );
	if ( $_slide_ngg_gallery ) {
		$_slide_gallery_name = $_slide_ngg_gallery->title;
	}
}
$this->start_element( 'nextgen_gallery.gallery_container', 'container', $displayed_gallery ); ?>

<div class="ngg-galleryoverview ngg-slideshow"
	id="<?php echo esc_attr( $anchor ); ?>"
	data-nextgen-gallery-id="<?php echo esc_attr( $_slide_data_gallery_id ); ?>"
	data-gallery-id="<?php echo esc_attr( $displayed_gallery_id ); ?>"
	<?php if ( $_slide_gallery_name ) : ?>
	data-gallery-name="<?php echo esc_attr( $_slide_gallery_name ); ?>"
	<?php endif; ?>
	style="max-width: <?php echo esc_attr( $gallery_width ); ?>px;
			max-height: <?php echo esc_attr( $gallery_height ); ?>px;
			display: none;">

	<?php
	$image_count = count( $images );
	for ( $i = 0; $i < $image_count; $i++ ) {
		$image                   = $images[ $i ];
		$show_tiktok_play_button = (
			! empty( $image->meta_data ) &&
			is_array( $image->meta_data ) &&
			! empty( $image->meta_data['imagely_tiktok_id'] ) &&
			! empty( $image->meta_data['imagely_tiktok_show_play_button'] )
		);
		$image->style            = 'style="height:' . esc_attr( $gallery_height ) . 'px"';
		$template_params         = [
			'index' => $i,
			'class' => 'ngg-gallery-slideshow-image',
		];
		$template_params         = array_merge( get_defined_vars(), $template_params );

		$this->start_element( 'nextgen_gallery.image', 'item', $image );
		?>

		<a href="<?php echo esc_attr( $storage->get_image_url( $image ) ); ?>"
			title="<?php echo esc_attr( $image->description ); ?>"
			data-src="<?php echo esc_attr( $storage->get_image_url( $image ) ); ?>"
			data-thumbnail="<?php echo esc_attr( $storage->get_image_url( $image, 'thumb' ) ); ?>"
			data-image-id="<?php echo esc_attr( $image->{$image->id_field} ); ?>"
			data-image-name="<?php echo esc_attr( $image->filename ?? '' ); ?>"
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
			<?php echo $effect_code; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- effect_code is safe HTML attributes from display settings ?>>
			<?php if ( $show_tiktok_play_button || ! empty( $image->meta_data['video_link'] ) ) : ?>
				<span class="ngg-video-play-overlay" aria-hidden="true"></span>
			<?php endif; ?>

			<img data-image-id='<?php echo esc_attr( $image->pid ); ?>'
				title="<?php echo esc_attr( \Imagely\NGG\Display\I18N::ngg_plain_text_alt_title_attributes( $image->description ) ); ?>"
				alt="<?php echo esc_attr( \Imagely\NGG\Display\I18N::ngg_plain_text_alt_title_attributes( $image->alttext ) ); ?>"
				src="<?php echo esc_attr( $storage->get_computed_image_url( $image, 'full' ) ); ?>"
				style="max-height: <?php echo esc_attr( $gallery_height - 20 ); ?>px;"/>
		</a>

		<?php $this->end_element(); } ?>
</div>

<?php if ( $show_thumbnail_link ) { ?>
		<!-- Thumbnails Link -->
	<div class="slideshowlink" style="max-width: <?php echo esc_attr( $gallery_width ); ?>px;">
		<a href='<?php echo esc_attr( $thumbnail_link ); ?>'><?php echo esc_html( $thumbnail_link_text ); ?></a>
	</div>
<?php } ?>

<?php $this->end_element(); ?>
