<?php
/**
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
		id='<?php print $anchor; ?>'
		data-nextgen-gallery-id="<?php print $displayed_gallery->id(); ?>">

		<h3><?php print esc_attr( $image->alttext ); ?></h3>

		<?php $this->include_template( 'GalleryDisplay/ImageBefore', $template_params ); ?>

		<a href='<?php print esc_attr( $storage->get_image_url( $image ) ); ?>'
			title='<?php print esc_attr( $image->description ); ?>'
			data-src="<?php print esc_attr( $storage->get_image_url( $image ) ); ?>"
			data-thumbnail="<?php print esc_attr( $storage->get_image_url( $image, 'thumb' ) ); ?>"
			data-image-id="<?php print esc_attr( $image->{$image->id_field} ); ?>"
			data-title="<?php print esc_attr( $image->alttext ); ?>"
			data-description="<?php print esc_attr( stripslashes( $image->description ) ); ?>"
			<?php print $effect_code; ?>>
			<img title='<?php print esc_attr( $image->alttext ); ?>'
				alt='<?php print esc_attr( $image->alttext ); ?>'
				src='<?php print esc_attr( $storage->get_image_url( $image ) ); ?>'/>
		</a>

		<?php $this->include_template( 'GalleryDisplay/ImageAfter', $template_params ); ?>

		<div class='ngg-imagebrowser-nav'>

			<div class='back'>
				<a class='ngg-browser-prev'
					id='ngg-prev-<?php print $previous_pid; ?>'
					href='<?php print $previous_image_link; ?>'>
					&#9668; <?php _e( 'Back', 'nggallery' ); ?>
				</a>
			</div>

			<div class='next'>
				<a class='ngg-browser-next'
					id='ngg-next-<?php print $next_pid; ?>'
					href='<?php print $next_image_link; ?>'>
					<?php _e( 'Next', 'nggallery' ); ?>
					&#9658;
				</a>
			</div>

			<div class='counter'>
				<?php print __( 'Picture', 'nggallery' ); ?>
				<?php print $number; ?>
				<?php print __( 'of', 'nggallery' ); ?>
				<?php print $total; ?>
			</div>

			<div class='ngg-imagebrowser-desc'>
				<p><?php print wp_kses( $image->description, I18N::get_kses_allowed_html() ); ?></p>
			</div>

		</div>
	</div>
<?php $this->end_element(); ?>