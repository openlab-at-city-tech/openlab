<tr>
	<td>
		<label for="<?php echo esc_attr( "{$display_type_name}_{$name}" ); ?>"
				<?php
				if ( ! empty( $text ) ) {
					?>
					title='<?php echo esc_attr( $text ); ?>'<?php } ?>
				<?php
				if ( ! empty( $text ) ) {
					?>
					class='tooltip'<?php } ?>>
			<?php echo esc_html( $label ); ?>
		</label>
	</td>
	<td>
		<input type="radio"
				id="<?php echo esc_attr( $display_type_name . '_' . $name ); ?>"
				name="<?php echo esc_attr( $display_type_name . '[' . $name . ']' ); ?>"
				class="<?php echo esc_attr( $display_type_name . '_' . $name ); ?>"
				value="1"
			<?php checked( 1, $value ); ?>/>
		<label for="<?php echo esc_attr( $display_type_name . '_' . $name ); ?>"><?php esc_html_e( 'Yes', 'nggallery' ); ?></label>
		&nbsp;
		<input type="radio"
				id="<?php echo esc_attr( $display_type_name . '_' . $name ); ?>_no"
				name="<?php echo esc_attr( $display_type_name . '[' . $name . ']' ); ?>"
				class="<?php echo esc_attr( $display_type_name . '_' . $name ); ?>"
				value="0"
			<?php checked( 0, $value ); ?>/>
		<label for="<?php echo esc_attr( $display_type_name . '_' . $name ); ?>_no"><?php esc_html_e( 'No', 'nggallery' ); ?></label>
	</td>
</tr>
