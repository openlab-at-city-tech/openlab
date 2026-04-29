<?php
/**
 * Template Page for the image browser with a exif data example
 *
 * Follow variables are useable :
 *
 *  $image : Contain all about the image
 *  $meta  : Contain the raw Meta data from the image
 *  $exif  : Contain the clean up Exif data from file
 *  $iptc  : Contain the clean up IPTC data from file
 *  $xmp   : Contain the clean up XMP data  from file
 *  $db    : Contain the clean up META data from the database (should be imported during upload)
 *
 * Please note : A Image resize or watermarking operation will remove all meta information, exif will in this case loaded from database
 *
 * You can check the content when you insert the tag <?php var_dump($variable) ?>
 * If you would like to show the timestamp of the image ,you can use <?php echo $exif['created_timestamp'] ?>
 */

?>
<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct access allowed' );}
?>
<?php if ( ! empty( $image ) ) : ?>

<div class="ngg-imagebrowser" id="<?php echo esc_attr( $image->anchor ); ?>" data-nextgen-gallery-id="<?php echo esc_attr( $displayed_gallery->id() ); ?>">

	<h3><?php echo \Imagely\NGG\Display\I18N::ngg_decode_sanitized_html_content( $image->alttext ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Using NGG's sanitized HTML content function ?></h3>

	<div class="pic"><?php echo $image->href_link; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Contains safe HTML link structure ?></div>
	<div class="ngg-imagebrowser-nav">
		<div class="back">
			<a class="ngg-browser-prev" id="ngg-prev-<?php echo esc_attr( $image->previous_pid ); ?>" href="<?php echo \Imagely\NGG\Util\Router::esc_url( $image->previous_image_link ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Using NGG's URL escaping function ?>">&#9668; <?php esc_html_e( 'Back', 'nggallery' ); ?></a>
		</div>
		<div class="next">
			<a class="ngg-browser-next" id="ngg-next-<?php echo esc_attr( $image->next_pid ); ?>" href="<?php echo \Imagely\NGG\Util\Router::esc_url( $image->next_image_link ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Using NGG's URL escaping function ?>"><?php esc_html_e( 'Next', 'nggallery' ); ?> &#9658;</a>
		</div>
		<div class="counter"><?php esc_html_e( 'Picture', 'nggallery' ); ?> <?php echo esc_html( $image->number ); ?> <?php esc_html_e( 'of', 'nggallery' ); ?> <?php echo esc_html( $image->total ); ?></div>
		<div class="ngg-imagebrowser-desc"><p><?php echo \Imagely\NGG\Display\I18N::ngg_decode_sanitized_html_content( $image->description ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Using NGG's sanitized HTML content function ?></p></div>
		<!-- Exif data -->
		<h3><?php esc_html_e( 'Meta data', 'nggallery' ); ?></h3>
		<table class="exif-data">
			<tbody>
			<tr>
				<th width="140"><?php esc_html_e( 'Camera / Type', 'nggallery' ); ?></th>
				<td>
				<?php
				// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
				echo esc_html( @$exif['camera'] );
				?>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Aperture', 'nggallery' ); ?></th>
				<td>
				<?php
				// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
				echo esc_html( @$exif['aperture'] );
				?>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Focal Length', 'nggallery' ); ?></th>
				<td>
				<?php
				// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
				echo esc_html( @$exif['focal_length'] );
				?>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Shutter speed', 'nggallery' ); ?></th>
				<td>
				<?php
				// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
				echo esc_html( @$exif['shutter_speed'] );
				?>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Date / Time', 'nggallery' ); ?></th>
				<td>
				<?php
				// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
				echo esc_html( @$exif['created_timestamp'] );
				?>
				</td>
			</tr>
			</tbody>
		</table>
	</div>

</div>

	<?php endif; ?>
