<span class="field gallery_preview_image_field">
	<select name="previewpic" id="gallery_preview_image">
		<option <?php selected( 0, $gallery->previewpic ? $gallery->previewpic : 0 ); ?> value="0"><?php esc_html_e( 'No picture', 'nggallery' ); ?></option>
		<?php foreach ( $images as $id => $filename ) : ?>
			<option <?php selected( $id, $gallery->previewpic ); ?> value="<?php echo esc_attr( $id ); ?>">
				<?php echo esc_html( $filename ); ?>
			</option>
		<?php endforeach ?>
	</select>
</span>