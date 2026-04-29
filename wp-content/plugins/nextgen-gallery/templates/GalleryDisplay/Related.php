<?php

use Imagely\NGG\DataTypes\LegacyImage;
use Imagely\NGG\Display\I18N;

/**
 * Related Gallery Display Template.
 *
 * @package Nextgen Gallery
 * @var LegacyImage[] $images
 */
?>
<div class="ngg-related-gallery">
	<?php foreach ( $images as $image ) { ?>
		<a href="<?php echo esc_attr( $image->imageURL ); ?>"
			title="<?php echo esc_attr( stripslashes( I18N::translate( $image->description ?? '', 'pic_' . $image->pid . '_description' ) ) ); ?>"
			<?php if ( ! empty( $image->meta_data['imagely_tiktok_play_url'] ) ) : ?>
				data-tiktok-play-url="<?php echo esc_attr( $image->meta_data['imagely_tiktok_play_url'] ); ?>"
			<?php endif; ?>
			<?php if ( ! empty( $image->meta_data['imagely_tiktok_share_url'] ) ) : ?>
				data-tiktok-share-url="<?php echo esc_attr( $image->meta_data['imagely_tiktok_share_url'] ); ?>"
			<?php endif; ?>
			<?php if ( ! empty( $image->meta_data['imagely_tiktok_embed_link'] ) ) : ?>
				data-tiktok-embed-url="<?php echo esc_attr( $image->meta_data['imagely_tiktok_embed_link'] ); ?>"
			<?php endif; ?>
			<?php if ( ! empty( $image->meta_data['video_link'] ) ) : ?>
				data-video-url="<?php echo esc_attr( $image->meta_data['video_link'] ); ?>"
			<?php endif; ?>
			<?php echo $image->get_thumbcode(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- thumbcode is safe HTML attributes from display settings ?>>
			<img title="<?php echo esc_attr( stripslashes( I18N::translate( $image->alttext ?? '', 'pic_' . $image->pid . '_alttext' ) ) ); ?>"
				alt="<?php echo esc_attr( stripslashes( I18N::translate( $image->alttext ?? '', 'pic_' . $image->pid . '_alttext' ) ) ); ?>"
				data-image-id="<?php echo esc_attr( $image->{$image->id_field} ); ?>"
				src="<?php echo esc_attr( $image->thumb_url ); ?>"/>
		</a>
	<?php } ?>
</div>
