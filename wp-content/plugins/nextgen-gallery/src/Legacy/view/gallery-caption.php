<?php
/**
Template Page for the gallery overview

Follow variables are useable :

	$gallery     : Contain all about the gallery
	$images      : Contain all images, path, title
	$pagination  : Contain the pagination content

You can check the content when you insert the tag <?php var_dump($variable) ?>
If you would like to show the timestamp of the image ,you can use <?php echo $exif['created_timestamp'] ?>
 **/
?>
<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct access allowed' );}
?>
<?php if ( ! empty( $gallery ) ) : ?>

<div class="ngg-galleryoverview ngg-template-caption" id="<?php echo $gallery->anchor; ?>">

		<?php if ( $gallery->show_slideshow ) { ?>
	<!-- Slideshow link -->
	<div class="slideshowlink">
		<a class="slideshowlink" href="<?php echo \Imagely\NGG\Util\Router::esc_url( $gallery->slideshow_link ); ?>">
			<?php echo $gallery->slideshow_link_text; ?>
		</a>
	</div>
<?php } ?>

	<!-- Thumbnails -->
		<?php $i = 0; ?>
		<?php foreach ( $images as $image ) : ?>
	
	<div id="ngg-image-<?php echo $image->pid; ?>" class="ngg-gallery-thumbnail-box" <?php echo $image->style; ?> >
		<div class="ngg-gallery-thumbnail" >
			<a href="<?php echo \Imagely\NGG\Util\Router::esc_url( $image->imageURL ); ?>"
				title="<?php echo esc_attr( $image->description ); ?>"
				<?php echo $image->thumbcode; ?> >
				<?php if ( ! $image->hidden ) { ?>
				<img title="<?php echo esc_attr( $image->alttext ); ?>" alt="<?php echo esc_attr( $image->alttext ); ?>" src="<?php echo \Imagely\NGG\Util\Router::esc_url( $image->thumbnailURL ); ?>" <?php echo $image->size; ?> />
				<?php } ?>
			</a>
			<span>
			<?php
			if ( ! $image->hidden ) {
				echo $image->caption; }
			?>
			</span>
		</div>
	</div>
			<?php
			if ( $image->hidden ) {
				continue;}
			?>
			<?php if ( $gallery->columns > 0 && ++$i % $gallery->columns == 0 ) { ?>
	<br style="clear: both" />
	<?php } ?>
	<?php endforeach; ?>
	
	<!-- Pagination -->
		<?php echo $pagination; ?>
	
</div>

	<?php endif; ?>
