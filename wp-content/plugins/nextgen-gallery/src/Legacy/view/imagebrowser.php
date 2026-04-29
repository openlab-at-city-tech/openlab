<?php
/**
 * Template Page for the image browser
 *
 * Follow variables are useable :
 *
 *  $image : Contain all about the image
 *  $meta  : Contain the raw Meta data from the image
 *  $exif  : Contain the clean up Exif data
 *  $iptc  : Contain the clean up IPTC data
 *  $xmp   : Contain the clean up XMP data
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

	<h3>
	<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- \Imagely\NGG\Display\I18N::ngg_decode_sanitized_html_content() returns safe HTML
	echo \Imagely\NGG\Display\I18N::ngg_decode_sanitized_html_content( $image->alttext );
	?>
	</h3>

	<div class="pic">
	<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $image->href_link contains safe HTML link for image display
	echo $image->href_link;
	?>
	</div>
	<div class="ngg-imagebrowser-nav">
		<div class="back">
			<a class="ngg-browser-prev" id="ngg-prev-<?php echo esc_attr( $image->previous_pid ); ?>" href="
																<?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- \Imagely\NGG\Util\Router::esc_url() provides safe URL escaping
																echo \Imagely\NGG\Util\Router::esc_url( $image->previous_image_link );
																?>
			">&#9668; <?php esc_html_e( 'Back', 'nggallery' ); ?></a>
		</div>
		<div class="next">
			<a class="ngg-browser-next" id="ngg-next-<?php echo esc_attr( $image->next_pid ); ?>" href="
																<?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- \Imagely\NGG\Util\Router::esc_url() provides safe URL escaping
																echo \Imagely\NGG\Util\Router::esc_url( $image->next_image_link );
																?>
			"><?php esc_html_e( 'Next', 'nggallery' ); ?> &#9658;</a>
		</div>
		<div class="counter"><?php esc_html_e( 'Picture', 'nggallery' ); ?> <?php echo esc_html( $image->number ); ?> <?php esc_html_e( 'of', 'nggallery' ); ?> <?php echo esc_html( $image->total ); ?></div>
		<div class="ngg-imagebrowser-desc"><p>
		<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- \Imagely\NGG\Display\I18N::ngg_decode_sanitized_html_content() returns safe HTML
		echo \Imagely\NGG\Display\I18N::ngg_decode_sanitized_html_content( $image->description );
		?>
		</p></div>
	</div>

</div>

	<?php endif; ?>
