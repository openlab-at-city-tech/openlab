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
		<input type='text'
				id='<?php print esc_attr( "{$display_type_name}_{$name}" ); ?>'
				name='<?php print esc_attr( "{$display_type_name}[{$name}]" ); ?>'
				class='<?php print esc_attr( "{$display_type_name}_{$name}" ); ?> nextgen_settings_field_colorpicker'
				value='<?php print esc_attr( $value ); ?>'
				autocomplete="off"/>
	</td>
</tr>