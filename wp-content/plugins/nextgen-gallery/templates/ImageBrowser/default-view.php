<?php
/**
 * Image Browser Default View Template.
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
];
$gallery_id      = null;
if ( isset( $displayed_gallery->container_ids ) && ! empty( $displayed_gallery->container_ids ) ) {
	$gallery_id = reset( $displayed_gallery->container_ids );
}
$data_gallery_id   = $gallery_id ? $gallery_id : $displayed_gallery->id();
$data_gallery_name = '';
if ( isset( $gallery ) && ! empty( $gallery->title ) ) {
	$data_gallery_name = $gallery->title;
} elseif ( $gallery_id ) {
	$_ngg_ib_gallery = \Imagely\NGG\DataMappers\Gallery::get_instance()->find( intval( $gallery_id ) );
	if ( $_ngg_ib_gallery ) {
		$data_gallery_name = $_ngg_ib_gallery->title;
	}
}
?>
<?php $this->start_element( 'nextgen_gallery.gallery_container', 'container', $displayed_gallery ); ?>
	<div class='ngg-imagebrowser default-view'
		id='<?php print esc_attr( $anchor ); ?>'
		data-nextgen-gallery-id="<?php echo esc_attr( $data_gallery_id ); ?>"
		data-gallery-id="<?php echo esc_attr( $data_gallery_id ); ?>"
		<?php if ( $data_gallery_name ) : ?>
		data-gallery-name="<?php echo esc_attr( $data_gallery_name ); ?>"
		<?php endif; ?>>

		<h3><?php print \Imagely\NGG\Display\I18N::ngg_decode_sanitized_html_content( $image->alttext ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h3>

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
			data-image-name="<?php print esc_attr( $image->filename ?? '' ); ?>"
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
					<i class="fa fa-chevron-left" aria-hidden="true"></i>
				</a>
			</div>

			<div class='next'>
				<a class='ngg-browser-next'
					id='ngg-next-<?php print esc_attr( $next_pid ); ?>'
					href='<?php print esc_url( $next_image_link ); ?>'>
					<i class="fa fa-chevron-right" aria-hidden="true"></i>
				</a>
			</div>

			<div class='counter'>
				<?php esc_html_e( 'Image', 'nggallery' ); ?>
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

		</div>

		<div class='ngg-imagebrowser-desc'>
			<p><?php print \Imagely\NGG\Display\I18N::ngg_decode_sanitized_html_content( $image->description ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
		</div>

	</div>		</div>
	</div>
<?php $this->end_element(); ?>
