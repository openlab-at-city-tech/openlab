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
	<div class="ngg-albumoverview default-view">
		<?php
		foreach ( $galleries as $gallery ) {
			if ( $open_gallery_in_lightbox && 'gallery' === $gallery->entity_type ) {
				$anchor = $gallery->displayed_gallery->effect_code . " href='" . \Imagely\NGG\Util\Router::esc_url( $gallery->pagelink ) . "'";
				if ( ! isset( $gallery->no_previewpic ) ) {
					$anchor .= "data-src='" . esc_attr( $gallery->previewpic_fullsized_url ) . "'
                            data-fullsize='" . esc_attr( $gallery->previewpic_fullsized_url ) . "'
                            data-thumbnail='" . esc_attr( $gallery->previewurl ) . "'
                            data-title='" . esc_attr( $gallery->previewpic_image->alttext ) . "'
                            data-description='" . esc_attr( stripslashes( $gallery->previewpic_image->description ?? '' ) ) . "'
                            data-image-id='" . esc_attr( $gallery->previewpic ) . "'";
					if ( ! empty( $gallery->previewpic_image->meta_data['imagely_tiktok_play_url'] ) ) {
						$anchor .= " data-tiktok-play-url='" . esc_attr( $gallery->previewpic_image->meta_data['imagely_tiktok_play_url'] ) . "'";
					}
					if ( ! empty( $gallery->previewpic_image->meta_data['imagely_tiktok_share_url'] ) ) {
						$anchor .= " data-tiktok-share-url='" . esc_attr( $gallery->previewpic_image->meta_data['imagely_tiktok_share_url'] ) . "'";
					}
					if ( ! empty( $gallery->previewpic_image->meta_data['imagely_tiktok_embed_link'] ) ) {
						$anchor .= " data-tiktok-embed-url='" . esc_attr( $gallery->previewpic_image->meta_data['imagely_tiktok_embed_link'] ) . "'";
					}
					if ( ! empty( $gallery->previewpic_image->meta_data['video_link'] ) ) {
						$anchor .= " data-video-url='" . esc_attr( $gallery->previewpic_image->meta_data['video_link'] ) . "'";
					}
				}
			} else {
				$anchor = "title='" . esc_attr( $gallery->title ) . "' href='" . \Imagely\NGG\Util\Router::esc_url( $gallery->pagelink ) . "'";
			}
			?>
			<div class="ngg-album-compact">
				<div class="ngg-album-compactbox">
					<div class="ngg-album-link">
						<?php $this->start_element( 'nextgen_gallery.image', 'item', $gallery ); ?>
							<?php if ( ! isset( $gallery->no_previewpic ) ) { ?>
								<a <?php echo wp_kses_post( $anchor ); ?>>
									<img class="Thumb"
										alt="<?php echo esc_attr( $gallery->title ); ?>"
										src="<?php echo \Imagely\NGG\Util\Router::esc_url( $gallery->previewurl ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"/>
								</a>
							<?php } ?>
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
					echo wp_kses_post( $anchor );
					echo wp_kses_post( $max_width );
					?>
					>
						<?php
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_kses() with allowed HTML tags provides safe output
						print wp_kses( $gallery->title, \Imagely\NGG\Display\I18N::get_kses_allowed_html() );
						?>
					</a>
				</h4>
				<p class="ngg-album-gallery-image-counter">
					<?php if ( isset( $gallery->counter ) && $gallery->counter > 0 ) { ?>
						<strong><?php echo absint( $gallery->counter ); ?></strong>&nbsp;<?php esc_html_e( 'Photos', 'nggallery' ); ?>
					<?php } else { ?>
						&nbsp;
					<?php } ?>
				</p>
			</div>
		<?php } ?>
		<br class="ngg-clear"/>
		<?php echo wp_kses_post( $pagination ); ?>
	</div>
<?php $this->end_element(); ?>
