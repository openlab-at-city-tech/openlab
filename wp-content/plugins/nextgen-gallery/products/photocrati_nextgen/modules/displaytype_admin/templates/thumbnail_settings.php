<tr id='tr_<?php print esc_attr( "{$display_type_name}_{$name}" ); ?>' class='<?php print ! empty( $hidden ) ? 'hidden' : ''; ?>'>
	<td>
		<label for="<?php print esc_attr( "{$display_type_name}_{$name}" ); ?>"
				<?php
				if ( ! empty( $text ) ) {
					?>
					title='<?php print esc_attr( $text ); ?>'<?php } ?>
				<?php
				if ( ! empty( $text ) ) {
					?>
					class='tooltip'<?php } ?>>
			<?php print $label; ?>
		</label>
	</td>
	<td>
		<?php
		$thumbnails_template_width_value  = $thumbnail_width;
		$thumbnails_template_height_value = $thumbnail_height;
		$thumbnails_template_id           = $display_type_name . '_thumbnail_dimensions';
		$thumbnails_template_width_id     = $display_type_name . '_thumbnail_width';
		$thumbnails_template_height_id    = $display_type_name . '_thumbnail_height';
		$thumbnails_template_name         = $display_type_name . '_thumbnail_dimensions';
		$thumbnails_template_width_name   = $display_type_name . '[thumbnail_width]';
		$thumbnails_template_height_name  = $display_type_name . '[thumbnail_height]';
		require NGGALLERY_ABSPATH . implode( DIRECTORY_SEPARATOR, [ 'admin', 'thumbnails-template.php' ] );
		?>
	</td>
</tr>