<?php
/**
 * Template Page for the album overview (extended)
 *
 * Follow variables are useable :
 *
 *  $album       : Contain information about the first album
 *  $albums      : Contain information about all albums
 *  $galleries   : Contain all galleries inside this album
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
<?php if ( ! empty( $galleries ) ) : ?>

<div class="ngg-albumoverview">	
	<!-- List of galleries -->
		<?php foreach ( $galleries as $gallery ) : ?>

	<div class="ngg-album">
		<div class="ngg-albumtitle"><a href="
			<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- \Imagely\NGG\Util\Router::esc_url() provides safe URL escaping
			echo \Imagely\NGG\Util\Router::esc_url( $gallery->pagelink );
			?>
		"><?php echo esc_html( $gallery->title ); ?></a></div>
			<div class="ngg-albumcontent">
				<div class="ngg-thumbnail">
					<a href="
					<?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- \Imagely\NGG\Util\Router::esc_url() provides safe URL escaping
					echo \Imagely\NGG\Util\Router::esc_url( $gallery->pagelink );
					?>
					"><img class="Thumb" alt="<?php echo esc_attr( $gallery->title ); ?>" src="
					<?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- \Imagely\NGG\Util\Router::esc_url() provides safe URL escaping
					echo \Imagely\NGG\Util\Router::esc_url( $gallery->previewurl );
					?>
					"/></a>
				</div>
				<div class="ngg-description">
				<p><?php echo wp_kses_post( $gallery->galdesc ?? '' ); ?></p>
				<?php // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
				if ( @$gallery->counter > 0 ) :
					?>
				<p class="ngg-album-gallery-image-counter"><strong><?php echo esc_html( $gallery->counter ); ?></strong>&nbsp;<?php esc_html_e( 'Photos', 'nggallery' ); ?></p>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<?php endforeach; ?>
	
	<!-- Pagination -->
		<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $pagination contains safe HTML for pagination display
		echo $pagination;
		?>
	
</div>

	<?php endif; ?>
