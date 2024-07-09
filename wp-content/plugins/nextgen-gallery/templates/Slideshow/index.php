<?php
/**
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
<?php $this->start_element( 'nextgen_gallery.gallery_container', 'container', $displayed_gallery ); ?>

<div class="ngg-galleryoverview ngg-slideshow"
	id="<?php echo esc_attr( $anchor ); ?>"
	data-gallery-id="<?php print esc_attr( $displayed_gallery_id ); ?>"
	style="max-width: <?php echo esc_attr( $gallery_width ); ?>px;
			max-height: <?php echo esc_attr( $gallery_height ); ?>px;
			display: none;">

	<?php
	for ( $i = 0; $i < count( $images ); $i++ ) {
		$image           = $images[ $i ];
		$image->style    = 'style="height:' . esc_attr( $gallery_height ) . 'px"';
		$template_params = [
			'index' => $i,
			'class' => 'ngg-gallery-slideshow-image',
		];
		$template_params = array_merge( get_defined_vars(), $template_params );

		$this->start_element( 'nextgen_gallery.image', 'item', $image );
		?>

		<a href="<?php echo esc_attr( $storage->get_image_url( $image ) ); ?>"
			title="<?php echo esc_attr( $image->description ); ?>"
			data-src="<?php echo esc_attr( $storage->get_image_url( $image ) ); ?>"
			data-thumbnail="<?php echo esc_attr( $storage->get_image_url( $image, 'thumb' ) ); ?>"
			data-image-id="<?php echo esc_attr( $image->{$image->id_field} ); ?>"
			data-title="<?php echo esc_attr( $image->alttext ); ?>"
			data-description="<?php echo esc_attr( stripslashes( $image->description ) ); ?>"
			<?php echo $effect_code; ?>>

			<img data-image-id='<?php echo esc_attr( $image->pid ); ?>'
				title="<?php echo esc_attr( $image->description ); ?>"
				alt="<?php echo esc_attr( $image->alttext ); ?>"
				src="<?php echo esc_attr( $storage->get_image_url( $image, 'full' ) ); ?>"
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