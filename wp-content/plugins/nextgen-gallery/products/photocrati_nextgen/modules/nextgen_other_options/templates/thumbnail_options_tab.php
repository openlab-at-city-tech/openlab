<table class="thumbnail_options">
	<tr>
		<td>
			<label for="thumbnail_dimensions_width"
					title="<?php echo esc_attr( $thumbnail_dimensions_help ); ?>"
						class="tooltip">
				<?php echo esc_html( $thumbnail_dimensions_label ); ?>
			</label>
		</td>
		<td colspan="2">
		<?php
			$thumbnails_template_width_value  = $thumbnail_dimensions_width;
			$thumbnails_template_height_value = $thumbnail_dimensions_height;
			$thumbnails_template_width_id     = 'thumbnail_dimensions_width';
			$thumbnails_template_height_id    = 'thumbnail_dimensions_height';
			$thumbnails_template_width_name   = 'thumbnail_settings[thumbwidth]';
			$thumbnails_template_height_name  = 'thumbnail_settings[thumbheight]';
			require implode(
				DIRECTORY_SEPARATOR,
				[
					rtrim( NGGALLERY_ABSPATH, '/\\' ),
					'admin',
					'thumbnails-template.php',
				]
			);
			?>
		</td>
	</tr>
	<tr>
		<td>
			<label 	for="thumbnail_crop"
					title="<?php echo esc_attr( $thumbnail_crop_help ); ?>"
						class="tooltip">
				<?php echo esc_html( $thumbnail_crop_label ); ?>
			</label>
		</td>
		<td colspan="2">
			<input type="radio"
					id="thumbnail_crop"
					name="thumbnail_settings[thumbfix]"
					value="1"
				<?php checked( 1, $thumbnail_crop ); ?>/>
			<label for="thumbnail_crop"><?php esc_html_e( 'Yes', 'nggallery' ); ?></label>
			&nbsp;
			<input type="radio"
					id="thumbnail_crop_no"
					name="thumbnail_settings[thumbfix]"
					value="0"
				<?php checked( 0, $thumbnail_crop ); ?>/>
			<label for="thumbnail_crop_no"><?php esc_html_e( 'No', 'nggallery' ); ?></label>
		</td>
	</tr>
	<?php
	/*
	<tr>
		<td>
			<label for="thumbnail_quality">
				<?php echo_h($thumbnail_quality_label) ?>
			</label>
		</td>
		<td colspan="2">
			<select name="thumbnail_settings[thumbquality]" id="thumbnail_quality">
			<?php for($i=100; $i>50; $i--): ?>
				<option
					<?php selected($i, $thumbnail_quality) ?>
					value="<?php echo_h($i)?>"><?php echo_h($i) ?>%</option>
			<?php endfor ?>
			</select>
			<p class="description"><?php echo_h($thumbnail_quality_help)?></p>
		</td>
	</tr>
	*/
	?>
	<tr>
		<td>
			<label 	for="size_list"
					title="<?php echo esc_attr( $size_list_help ); ?>"
						class="tooltip">
				<?php echo esc_html( $size_list_label ); ?>
			</label>
		</td>
		<td colspan="2">
		<?php
		if ( $size_list != null && is_array( $size_list ) ) {
			?>
			<select class="select2 thumbnail_dimensions" name="size_settings[thumbnail_dimensions][]" id="thumbnail_dimensions" multiple="multiple">
			<?php
			foreach ( $size_list as $size ) {
				?>
				<option
				<?php selected( $size, $size ); ?>
					value="<?php echo esc_attr( $size ); ?>"><?php echo esc_html( $size ); ?></option>
				<?php
			}
			?>
			</select>
			<?php
		} else {
			echo '<i>No default sizes present.</i>';
		}
		?>
		</td>
	</tr>
</table>
