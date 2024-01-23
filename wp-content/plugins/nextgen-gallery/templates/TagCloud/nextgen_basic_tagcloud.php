<div class='ngg-tagcloud' id="gallery_<?php echo esc_attr( $displayed_gallery_id ); ?>">
	<?php if ( $tagcloud ) : ?>
		<?php print $tagcloud; ?>
	<?php else : ?>
		No images have been tagged.
	<?php endif ?>
</div>