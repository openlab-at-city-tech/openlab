<?php

use Imagely\NGG\DataTypes\LegacyImage;
use Imagely\NGG\Display\I18N;

/**
 * @var LegacyImage[] $images
 */
?>
<div class="ngg-related-gallery">
	<?php foreach ( $images as $image ) { ?>
		<a href="<?php echo esc_attr( $image->imageURL ); ?>"
			title="<?php echo esc_attr( stripslashes( I18N::translate( $image->description, 'pic_' . $image->pid . '_description' ) ) ); ?>"
			<?php echo $image->get_thumbcode(); ?>>
			<img title="<?php echo esc_attr( stripslashes( I18N::translate( $image->alttext, 'pic_' . $image->pid . '_alttext' ) ) ); ?>"
				alt="<?php echo esc_attr( stripslashes( I18N::translate( $image->alttext, 'pic_' . $image->pid . '_alttext' ) ) ); ?>"
				data-image-id="<?php echo esc_attr( $image->{$image->id_field} ); ?>"
				src="<?php echo esc_attr( $image->thumbURL ); ?>"/>
		</a>
	<?php } ?>
</div>