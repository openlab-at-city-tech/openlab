<tr>
	<td>
		<label for="<?php echo esc_attr( $display_type_name ); ?>_gallery_display_type"
				class="tooltip"
				title="<?php echo esc_attr( $gallery_display_type_help ); ?>">
			<?php echo esc_html( $gallery_display_type_label ); ?>
		</label>
	</td>
	<td>
		<select
			style="width: 400px"
			id="<?php echo esc_attr( $display_type_name ); ?>_gallery_display_type"
			name="<?php echo esc_attr( $display_type_name ); ?>[gallery_display_type]">
			<?php foreach ( $display_types as $display_type ) : ?>
			<option value="<?php echo esc_attr( $display_type->name ); ?>"
				<?php selected( $display_type->name, $gallery_display_type ); ?>>
				<?php echo esc_html( $display_type->title ); ?>
			</option>
			<?php endforeach ?>
		</select>
	</td>
</tr>