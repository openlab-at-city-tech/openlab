<?php
/**
Template Page for the album overview

Follow variables are useable :

    $album     	 : Contain information about the first album
    $albums    	 : Contain information about all albums
	$galleries   : Contain all galleries inside this album
	$pagination  : Contain the pagination content

 You can check the content when you insert the tag <?php var_dump($variable) ?>
 If you would like to show the timestamp of the image ,you can use <?php echo $exif['created_timestamp'] ?>
**/
?>
<?php if (!defined ('ABSPATH')) die ('No direct access allowed'); ?><?php if (!empty ($galleries)) : ?>

<div class="ngg-albumoverview">

	<!-- List of galleries -->
	<?php foreach ($galleries as $gallery) : ?>

	<div class="ngg-album-compact">
		<div class="ngg-album-compactbox">
			<div class="ngg-album-link">
                <?php if ($open_gallery_in_lightbox && $gallery->entity_type == 'gallery') { ?>
                    <a <?php echo $gallery->displayed_gallery->effect_code; ?>
                       href="<?php echo esc_attr($gallery->previewpic_fullsized_url); ?>"
                       data-fullsize="<?php echo esc_attr($gallery->previewpic_fullsized_url); ?>"
                       data-src="<?php echo esc_attr($gallery->previewpic_fullsized_url); ?>"
                       data-thumbnail="<?php echo esc_attr($gallery->previewurl); ?>"
                       data-title="<?php echo esc_attr($gallery->previewpic_image->alttext); ?>"
                       data-description="<?php echo esc_attr(stripslashes($gallery->previewpic_image->description)); ?>"
                       data-image-id="<?php echo esc_attr($gallery->previewpic); ?>">
                        <img class="Thumb"
                             alt="<?php echo esc_attr($gallery->title); ?>"
                             src="<?php echo nextgen_esc_url($gallery->previewurl); ?>"/>
                    </a>
                <?php } else { ?>
                    <a class="Link" href="<?php echo nextgen_esc_url($gallery->pagelink); ?>">
                        <img class="Thumb"
                             alt="<?php echo esc_attr($gallery->title); ?>"
                             src="<?php echo nextgen_esc_url($gallery->previewurl); ?>"/>
                    </a>
                <?php } ?>
			</div>
		</div>
        <?php if (!empty($image_gen_params)) {
            $max_width = 'style="max-width: ' . ($image_gen_params['width'] + 20) . 'px"';
        } else {
            $max_width = '';
        } ?>
        <h4>
            <a class="ngg-album-desc"
               title="<?php echo esc_attr($gallery->title) ?>"
               href="<?php echo nextgen_esc_url($gallery->pagelink) ?>"
               <?php echo $max_width; ?>>
                <?php echo $gallery->title ?>
            </a>
        </h4>
		<p class="ngg-album-gallery-image-counter">
			<?php if (isset($gallery->counter) && $gallery->counter > 0) { ?>
				<strong><?php echo $gallery->counter; ?></strong>&nbsp;<?php _e('Photos', 'nggallery'); ?>
			<?php } else { ?>
				&nbsp;
			<?php } ?>
		</p>
	</div>

 	<?php endforeach; ?>

	<!-- Pagination -->
    <br class="ngg-clear"/>
 	<?php echo $pagination ?>
</div>

<?php endif; ?>
