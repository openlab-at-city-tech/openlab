<?php 
/**
Template Page for the single pic

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
<?php if (!defined ('ABSPATH')) die ('No direct access allowed'); ?><?php if (!empty ($image)) : ?>
<a href="<?php echo nextgen_esc_url($image->imageURL); ?>"
   title="<?php echo esc_attr($image->linktitle); ?>"
   <?php if(!empty($target)) { ?>target="<?php echo esc_attr($target); ?>"<?php } ?>
   <?php echo $image->thumbcode; ?>>
	<img class="<?php echo $image->classname; ?>"
         src="<?php echo nextgen_esc_url($image->thumbnailURL); ?>"
         alt="<?php echo esc_attr($image->alttext); ?>"
         title="<?php echo esc_attr($image->alttext); ?>"/>
</a>
<?php if (!empty ($image->description)) : ?><span><?php echo $image->description ?></span><?php endif; ?>
<?php endif; ?>
