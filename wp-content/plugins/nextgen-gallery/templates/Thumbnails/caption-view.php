<?php $this->start_element( 'nextgen_gallery.gallery_container', 'container', $displayed_gallery ); ?>

<div class="ngg-galleryoverview caption-view 
<?php
if ( ! intval( $ajax_pagination ) ) {
	echo ' ngg-ajax-pagination-none';}
?>
"
	id="ngg-gallery-<?php echo esc_attr( $displayed_gallery_id ); ?>-<?php echo esc_attr( $current_page ); ?>">

	<div class="ngg-caption-view-wrapper">
		<?php $this->start_element( 'nextgen_gallery.image_list_container', 'container', $images ); ?>
			<?php
			for ( $i = 0; $i < count( $images ); $i++ ) {
				$image        = $images[ $i ];
				$thumb_size   = $storage->get_image_dimensions( $image, $thumbnail_size_name );
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
											echo $column_class; }
										?>
					" 
					<?php
					if ( $style ) {
										echo $style;}
					?>
>
						<?php $this->start_element( 'nextgen_gallery.image', 'item', $image ); ?>
							<div class="ngg-gallery-thumbnail">
								<a href="<?php echo esc_attr( $storage->get_image_url( $image, 'full', true ) ); ?>"
									title="<?php echo esc_attr( $image->description ); ?>"
									data-src="<?php echo esc_attr( $storage->get_image_url( $image ) ); ?>"
									data-thumbnail="<?php echo esc_attr( $storage->get_image_url( $image, 'thumb' ) ); ?>"
									data-image-id="<?php echo esc_attr( $image->{$image->id_field} ); ?>"
									data-title="<?php echo esc_attr( $image->alttext ); ?>"
									data-description="<?php echo esc_attr( stripslashes( $image->description ) ); ?>"
									data-image-slug="<?php echo esc_attr( $image->image_slug ); ?>"
									<?php echo $effect_code; ?>>
									<img title="<?php echo esc_attr( $image->alttext ); ?>"
										alt="<?php echo esc_attr( $image->alttext ); ?>"
										src="<?php echo esc_attr( $storage->get_image_url( $image, $thumbnail_size_name ) ); ?>"
										width="<?php echo esc_attr( $thumb_size['width'] ); ?>"
										height="<?php echo esc_attr( $thumb_size['height'] ); ?>"
										style="max-width:100%;"/>
								</a>
								<?php if ( ! isset( $image->hidden ) || ! $image->hidden ) { ?>
									<span style="max-width: <?php print esc_attr( $thumb_size['width'] ); ?>px">
										<?php print $image->description; ?>
									</span>
								<?php } ?>
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
		<?php echo $pagination; ?>
	<?php } ?>
</div>

<?php $this->end_element(); ?>