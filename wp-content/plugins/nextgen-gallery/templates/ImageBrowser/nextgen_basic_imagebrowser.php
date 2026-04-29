<?php
/**
 * NextGen Basic Image Browser Template.
 *
 * @package Nextgen Gallery
 * @var View $this
 * @var C_Displayed_Gallery $displayed_gallery
 * @var \Imagely\NGG\DataMappers\Image $storage
 * @var \Imagely\NGG\DataTypes\Image $image
 * @var int $next_pid
 * @var int $number
 * @var int $previous_pid
 * @var int $total
 * @var string $anchor
 * @var string $next_image_link
 * @var string $previous_image_link
 * @var string $effect_code
 */

use Imagely\NGG\Display\I18N;
use Imagely\NGG\Display\View;

$template_params = [
	'index' => 0,
	'class' => 'pic',
	'image' => $image,
]; ?>
<?php $this->start_element( 'nextgen_gallery.gallery_container', 'container', $displayed_gallery ); ?>
	<div class='ngg-imagebrowser'
		id='<?php print esc_attr( $anchor ); ?>'
		data-nextgen-gallery-id="<?php print esc_attr( $displayed_gallery->id() ); ?>">

		<h3><?php print \Imagely\NGG\Display\I18N::ngg_decode_sanitized_html_content( $image->alttext ); // phpcs:ignore ?></h3>

		<?php $this->include_template( 'GalleryDisplay/ImageBefore', $template_params ); ?>

	<?php
	$show_tiktok_play_button = (
		! empty( $image->meta_data ) &&
		is_array( $image->meta_data ) &&
		! empty( $image->meta_data['imagely_tiktok_id'] ) &&
		! empty( $image->meta_data['imagely_tiktok_show_play_button'] )
	);
	?>
		<a href='<?php print esc_attr( $storage->get_image_url( $image ) ); ?>'
			title='<?php print esc_attr( $image->description ); ?>'
			data-src="<?php print esc_attr( $storage->get_image_url( $image ) ); ?>"
			data-thumbnail="<?php print esc_attr( $storage->get_image_url( $image, 'thumb' ) ); ?>"
			data-image-id="<?php print esc_attr( $image->{$image->id_field} ); ?>"
			data-title="<?php print esc_attr( $image->alttext ); ?>"
			data-description="<?php print esc_attr( stripslashes( $image->description ?? '' ) ); ?>"
			<?php if ( ! empty( $image->meta_data['imagely_tiktok_play_url'] ) ) : ?>
				data-tiktok-play-url="<?php print esc_attr( $image->meta_data['imagely_tiktok_play_url'] ); ?>"
			<?php endif; ?>
			<?php if ( ! empty( $image->meta_data['imagely_tiktok_share_url'] ) ) : ?>
				data-tiktok-share-url="<?php print esc_attr( $image->meta_data['imagely_tiktok_share_url'] ); ?>"
			<?php endif; ?>
			<?php if ( ! empty( $image->meta_data['imagely_tiktok_embed_link'] ) ) : ?>
				data-tiktok-embed-url="<?php print esc_attr( $image->meta_data['imagely_tiktok_embed_link'] ); ?>"
			<?php endif; ?>
			<?php if ( ! empty( $image->meta_data['video_link'] ) ) : ?>
				data-video-url="<?php print esc_attr( $image->meta_data['video_link'] ); ?>"
			<?php endif; ?>
			<?php echo $effect_code; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- effect_code is safe HTML attributes from display settings ?>>
		<?php if ( $show_tiktok_play_button || ! empty( $image->meta_data['video_link'] ) ) { ?>
			<span class="ngg-video-play-overlay" aria-hidden="true"></span>
		<?php } ?>
			<img title='<?php print esc_attr( \Imagely\NGG\Display\I18N::ngg_plain_text_alt_title_attributes( $image->alttext ) ); ?>'
				alt='<?php print esc_attr( \Imagely\NGG\Display\I18N::ngg_plain_text_alt_title_attributes( $image->alttext ) ); ?>'
				src='<?php print esc_attr( $storage->get_computed_image_url( $image ) ); ?>'/>
		</a>

		<?php $this->include_template( 'GalleryDisplay/ImageAfter', $template_params ); ?>

		<div class='ngg-imagebrowser-nav'>

			<div class='back'>
				<a class='ngg-browser-prev'
					id='ngg-prev-<?php print esc_attr( $previous_pid ); ?>'
					href='<?php print esc_url( $previous_image_link ); ?>'>
					&#9668; <?php esc_html_e( 'Back', 'nggallery' ); ?>
				</a>
			</div>

			<div class='next'>
				<a class='ngg-browser-next'
					id='ngg-next-<?php print esc_attr( $next_pid ); ?>'
					href='<?php print esc_url( $next_image_link ); ?>'>
					<?php esc_html_e( 'Next', 'nggallery' ); ?>
					&#9658;
				</a>
			</div>

			<div class='counter'>
				<?php esc_html_e( 'Picture', 'nggallery' ); ?>
				<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- absint() returns safe integer
				print absint( $number );
				?>
				<?php esc_html_e( 'of', 'nggallery' ); ?>
				<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- absint() returns safe integer
				print absint( $total );
				?>
			</div>

			<div class='ngg-imagebrowser-desc'>
				<p><?php print \Imagely\NGG\Display\I18N::ngg_decode_sanitized_html_content( $image->description ); // phpcs:ignore ?></p>
			</div>

		</div>
	</div>
<?php $this->end_element(); ?>
