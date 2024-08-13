<?php
/**
 * Template for Compact Album displays.
 *
 * @var \Imagely\NGG\DataTypes\DisplayedGallery $displayed_gallery
 * @var array $galleries
 * @var bool $open_gallery_in_lightbox
 * @var string $pagination
 * @package NextGEN Gallery
 */

?>
<?php $this->start_element( 'nextgen_gallery.gallery_container', 'container', $displayed_gallery ); ?>
	<div class="ngg-albumoverview">
		<?php
		foreach ( $galleries as $gallery ) {
			if ( $open_gallery_in_lightbox && 'gallery' === $gallery->entity_type ) {
				$anchor = $gallery->displayed_gallery->effect_code . "
                      href='" . \Imagely\NGG\Util\Router::esc_url( $gallery->pagelink ) . "'
                      data-src='" . esc_attr( $gallery->previewpic_fullsized_url ) . "'
                      data-fullsize='" . esc_attr( $gallery->previewpic_fullsized_url ) . "'
                      data-thumbnail='" . esc_attr( $gallery->previewurl ) . "'
                      data-title='" . esc_attr( $gallery->previewpic_image->alttext ) . "'
                      data-description='" . esc_attr( stripslashes( $gallery->previewpic_image->description ) ) . "'
                      data-image-id='" . esc_attr( $gallery->previewpic ) . "'";
			} else {
				$anchor = "title='" . esc_attr( $gallery->title ) . "' href='" . \Imagely\NGG\Util\Router::esc_url( $gallery->pagelink ) . "'";
			}
			?>
			<div class="ngg-album-compact">
				<div class="ngg-album-compactbox">
					<div class="ngg-album-link">
						<?php $this->start_element( 'nextgen_gallery.image', 'item', $gallery ); ?>
							<a <?php echo $anchor; ?>>
								<img class="Thumb"
									alt="<?php echo esc_attr( $gallery->title ); ?>"
									src="<?php echo \Imagely\NGG\Util\Router::esc_url( $gallery->previewurl ); ?>"/>
							</a>
						<?php $this->end_element(); ?>
					</div>
				</div>
				<?php
				if ( ! empty( $image_gen_params ) ) {
					$max_width = 'style="max-width: ' . ( $image_gen_params['width'] + 20 ) . 'px"';
				} else {
					$max_width = '';
				}
				?>
				<h4>
					<a class='ngg-album-desc'
						<?php
						echo $anchor;
						echo $max_width;
						?>
					>
						<?php print wp_kses( $gallery->title, \Imagely\NGG\Display\I18N::get_kses_allowed_html() ); ?>
					</a>
				</h4>
				<p class="ngg-album-gallery-image-counter">
					<?php if ( isset( $gallery->counter ) && $gallery->counter > 0 ) { ?>
						<strong><?php echo $gallery->counter; ?></strong>&nbsp;<?php _e( 'Photos', 'nggallery' ); ?>
					<?php } else { ?>
						&nbsp;
					<?php } ?>
				</p>
			</div>
		<?php } ?>
		<br class="ngg-clear"/>
		<?php echo $pagination; ?>
	</div>
<?php $this->end_element(); ?>
