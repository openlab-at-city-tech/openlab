<?php
/**
Template Page for the image browser with a exif data example

Follow variables are useable :

	$image : Contain all about the image
	$meta  : Contain the raw Meta data from the image
	$exif  : Contain the clean up Exif data from file
	$iptc  : Contain the clean up IPTC data from file
	$xmp   : Contain the clean up XMP data  from file
	$db    : Contain the clean up META data from the database (should be imported during upload)

Please note : A Image resize or watermarking operation will remove all meta information, exif will in this case loaded from database

You can check the content when you insert the tag <?php var_dump($variable) ?>
If you would like to show the timestamp of the image ,you can use <?php echo $exif['created_timestamp'] ?>
 **/

?>
<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct access allowed' );}
?>
<?php if ( ! empty( $image ) ) : ?>

<div class="ngg-imagebrowser" id="<?php echo $image->anchor; ?>" data-nextgen-gallery-id="<?php echo $displayed_gallery->id(); ?>">

	<h3><?php echo $image->alttext; ?></h3>

	<div class="pic"><?php echo $image->href_link; ?></div>
	<div class="ngg-imagebrowser-nav"> 
		<div class="back">
			<a class="ngg-browser-prev" id="ngg-prev-<?php echo $image->previous_pid; ?>" href="<?php echo \Imagely\NGG\Util\Router::esc_url( $image->previous_image_link ); ?>">&#9668; <?php _e( 'Back', 'nggallery' ); ?></a>
		</div>
		<div class="next">
			<a class="ngg-browser-next" id="ngg-next-<?php echo $image->next_pid; ?>" href="<?php echo \Imagely\NGG\Util\Router::esc_url( $image->next_image_link ); ?>"><?php _e( 'Next', 'nggallery' ); ?> &#9658;</a>
		</div>
		<div class="counter"><?php _e( 'Picture', 'nggallery' ); ?> <?php echo $image->number; ?> <?php _e( 'of', 'nggallery' ); ?> <?php echo $image->total; ?></div>
		<div class="ngg-imagebrowser-desc"><p><?php echo $image->description; ?></p></div>
		<!-- Exif data -->
		<h3><?php _e( 'Meta data', 'nggallery' ); ?></h3>
		<table class="exif-data">
			<tbody>
			<tr>
				<th width="140"><?php _e( 'Camera / Type', 'nggallery' ); ?></th>
				<td><?php echo @$exif['camera']; ?></td>
			</tr>
			<tr>
				<th><?php _e( 'Aperture', 'nggallery' ); ?></th>
				<td><?php echo @$exif['aperture']; ?></td>
			</tr>
			<tr>
				<th><?php _e( 'Focal Length', 'nggallery' ); ?></th>
				<td><?php echo @$exif['focal_length']; ?></td>
			</tr>
			<tr>
				<th><?php _e( 'Shutter speed', 'nggallery' ); ?></th>
				<td><?php echo @$exif['shutter_speed']; ?></td>
			</tr>
			<tr>
				<th><?php _e( 'Date / Time', 'nggallery' ); ?></th>
				<td><?php echo @$exif['created_timestamp']; ?></td>
			</tr>
			</tbody>
		</table>
	</div>	

</div>	

	<?php endif; ?>