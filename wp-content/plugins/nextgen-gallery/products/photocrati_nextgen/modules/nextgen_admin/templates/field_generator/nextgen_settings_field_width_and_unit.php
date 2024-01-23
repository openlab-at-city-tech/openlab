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
		<input type='number'
				id='<?php print esc_attr( "{$display_type_name}_{$name}" ); ?>'
				name='<?php print esc_attr( "{$display_type_name}[{$name}]" ); ?>'
				class='<?php print esc_attr( "{$display_type_name}[{$name}]" ); ?> nextgen_settings_field_width_and_unit'
				<?php
				if ( ! empty( $placeholder ) ) {
					?>
					placeholder='<?php print esc_attr( $placeholder ); ?>'<?php } ?>
				value='<?php print esc_attr( $value ); ?>'/>

		<select id="<?php print esc_attr( "{$display_type_name}_{$unit_name}" ); ?>"
				name="<?php print esc_attr( "{$display_type_name}[{$unit_name}]" ); ?>"
				class="<?php print esc_attr( "{$display_type_name}_{$unit_name}" ); ?> nextgen_settings_field_width_and_unit">
			<?php foreach ( $options as $key => $val ) { ?>
				<option value='<?php print $key; ?>' <?php selected( $key, $unit_value ); ?>><?php print $val; ?></option>
			<?php } ?>
		</select>
	</td>
</tr>