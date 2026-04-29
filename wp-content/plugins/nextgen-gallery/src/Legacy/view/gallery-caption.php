<?php
/**
 * Template Page for the gallery overview
 *
 * Follow variables are useable :
 *
 *  $gallery     : Contain all about the gallery
 *  $images      : Contain all images, path, title
 *  $pagination  : Contain the pagination content
 *
 * You can check the content when you insert the tag <?php var_dump($variable) ?>
 * If you would like to show the timestamp of the image ,you can use <?php echo $exif['created_timestamp'] ?>
 */

?>
<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct access allowed' );}
?>
<?php if ( ! empty( $gallery ) ) : ?>

<div class="ngg-galleryoverview ngg-template-caption" id="<?php echo esc_attr( $gallery->anchor ); ?>">

		<?php if ( $gallery->show_slideshow ) { ?>
	<!-- Slideshow link -->
	<div class="slideshowlink">
		<a class="slideshowlink" href="
			<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- \Imagely\NGG\Util\Router::esc_url() provides safe URL escaping
			echo \Imagely\NGG\Util\Router::esc_url( $gallery->slideshow_link );
			?>
		">
			<?php echo esc_html( $gallery->slideshow_link_text ); ?>
		</a>
	</div>
<?php } ?>

	<!-- Thumbnails -->
		<?php $i = 0; ?>
		<?php foreach ( $images as $image ) : ?>

	<div id="ngg-image-<?php echo esc_attr( $image->pid ); ?>" class="ngg-gallery-thumbnail-box" 
									<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $image->style contains safe CSS style attributes
									echo $image->style;
									?>
	>
		<div class="ngg-gallery-thumbnail" >
			<a href="
			<?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- \Imagely\NGG\Util\Router::esc_url() provides safe URL escaping
			echo \Imagely\NGG\Util\Router::esc_url( $image->imageURL );
			?>
			"
				title="<?php echo esc_attr( $image->description ); ?>"
				<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $image->thumbcode contains safe HTML attributes for thumbnail
				echo $image->thumbcode;
				?>
				>
				<?php
				if ( ! $image->hidden ) {
					$image_alttext = \Imagely\NGG\Display\I18N::ngg_plain_text_alt_title_attributes( $image->alttext );
					?>
				<img title="<?php echo esc_attr( $image_alttext ); ?>" alt="<?php echo esc_attr( $image_alttext ); ?>" src="
										<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- \Imagely\NGG\Util\Router::esc_url() provides safe URL escaping
										echo \Imagely\NGG\Util\Router::esc_url( $image->thumbnailURL );
										?>
				" 
					<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $image->size contains safe HTML size attributes
					echo $image->size;
					?>
				/>
				<?php } ?>
			</a>
			<span>
			<?php
			if ( ! $image->hidden ) {
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- \Imagely\NGG\Display\I18N::ngg_decode_sanitized_html_content() returns safe HTML
				echo \Imagely\NGG\Display\I18N::ngg_decode_sanitized_html_content( $image->caption );
			}
			?>
			</span>
		</div>
	</div>
			<?php
			if ( $image->hidden ) {
				continue;}
			?>
			<?php
			++$i;
			if ( $gallery->columns > 0 && $i % $gallery->columns == 0 ) {
				?>
	<br style="clear: both" />
		<?php } ?>
	<?php endforeach; ?>	<!-- Pagination -->
		<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $pagination contains safe HTML for pagination display
		echo $pagination;
		?>

</div>

	<?php endif; ?>
