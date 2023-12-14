<tr>
	<td>
		<label for='<?php echo esc_attr( $display_type_name ); ?>_template'
				class='tooltip'
				title="<?php echo esc_attr( $template_text ); ?>">
			<?php echo esc_html( $template_label ); ?>
			<div class="deprecated">
				<i class="fa fa-exclamation-circle" aria-hidden="true"></i>
				<strong>Deprecated:</strong> For more information, please see our <a target='_blank' href='https://www.imagely.com/docs/legacy-templates-deprecation'>documentation</a>
			</div>
		</label>
	</td>
	<td>
		<div class='ngg_settings_template_wrapper'>
			<select name='<?php echo esc_attr( $display_type_name ); ?>[template]'
					id='<?php echo esc_attr( $display_type_name ); ?>_template>'
					class='ngg_thumbnail_template ngg_settings_template'>
				<option></option>
				<?php foreach ( $templates as $file => $label ) : ?>
					<?php if ( $file && $label ) : ?>
					<option value="<?php echo esc_attr( $file ); ?>" <?php selected( $chosen_file, $file, true ); ?>><?php echo esc_html( $label ); ?></option>
					<?php endif ?>
				<?php endforeach ?>
			</select>
		</div>
	</td>
</tr>
