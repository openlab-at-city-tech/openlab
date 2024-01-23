<tr>
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
		<select
				id="<?php print $display_type_name . '_' . $name; ?>"
				name="<?php print $display_type_name . '[' . $name . ']'; ?>"
				class="<?php print $display_type_name . '_' . $name; ?>">
			<?php foreach ( $options as $key => $val ) { ?>
				<option value='<?php print $key; ?>' <?php selected( $key, $value ); ?>><?php print $val; ?></option>
			<?php } ?>
		</select>
	</td>
</tr>
