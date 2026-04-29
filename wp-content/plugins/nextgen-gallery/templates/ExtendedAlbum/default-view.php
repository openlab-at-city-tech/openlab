<?php
/**
 * Template for Extended Album displays.
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
				$anchor = $gallery->displayed_gallery->effect_code . "
                      href='" . \Imagely\NGG\Util\Router::esc_url( $gallery->pagelink ) . "'
                      data-src='" . esc_attr( $gallery->previewpic_fullsized_url ) . "'
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
			} else {
				$anchor = "class='gallery_link' href='" . \Imagely\NGG\Util\Router::esc_url( $gallery->pagelink ) . "'";
			}
			?>
			<div class="ngg-album">
				<div class="ngg-albumcontent">
					<?php $this->start_element( 'nextgen_gallery.image', 'item', $gallery ); ?>
						<div class="ngg-thumbnail">
							<a <?php echo wp_kses_post( $anchor ); ?>>
								<img class="Thumb"
									alt="<?php echo esc_attr( $gallery->title ); ?>"
									src="<?php echo \Imagely\NGG\Util\Router::esc_url( $gallery->previewurl ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"/>
							</a>
						</div>
					<?php $this->end_element(); ?>
					<div class="ngg-albumtitle">
						<a <?php echo wp_kses_post( $anchor ); ?>><?php print wp_kses( $gallery->title, \Imagely\NGG\Display\I18N::get_kses_allowed_html() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
					</div>
					<div class="ngg-description">
						<p>
						<?php
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_kses() with allowed HTML tags provides safe output
						print wp_kses( $gallery->galdesc ?? '', \Imagely\NGG\Display\I18N::get_kses_allowed_html() );
						?>
						</p>
						<?php if ( isset( $gallery->counter ) && $gallery->counter > 0 ) { ?>
							<p class="ngg-album-gallery-image-counter">
								<strong><?php echo absint( $gallery->counter ); ?></strong>&nbsp;<?php esc_html_e( 'Photos', 'nggallery' ); ?></p>
						<?php } ?>
					</div>
				</div>
			</div>
		<?php } ?>
		<?php echo wp_kses_post( $pagination ); ?>
	</div>
<?php $this->end_element(); ?>
