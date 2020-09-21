<table class="image_options">
	<?php 
	if (!is_multisite() || get_current_blog_id() == 1) {
	?>
	<tr>
		<td class="column1">
			<label 	for="gallery_path"
					title="<?php echo esc_attr($gallery_path_help); ?>"
               		class="tooltip">
					<?php esc_html_e($gallery_path_label)?>
			</label>
		</td>
		<td colspan="2">
			<input
				id="gallery_path"
				type="text"
				name="image_options[gallerypath]"
                data-original-value='<?php echo esc_attr($gallery_path); ?>'
				value="<?php echo esc_attr($gallery_path) ?>"/>
            <?php if ($gallery_path_error_state) { ?>
                <br/>
                <div class="error">
                    <p>
                        <?php print $gallery_path_error_message; ?>
                    </p>
                </div>
            <?php } ?>
		</td>
	</tr>
	<?php 
	}
	?>
	<tr>
		<td>
			<label 	for="delete_images" 
					title="<?php echo esc_attr($delete_image_files_help); ?>"
               		class="tooltip">
					<?php esc_html_e($delete_image_files_label) ?>
			</label>
		</td>
		<td colspan="2">
			<p class="description">
                <input type="radio"
                       id="delete_images"
                       name="image_options[deleteImg]"
                       value="1"
                       <?php checked(1, $delete_image_files); ?>/>
                <label for="delete_images"><?php _e('Yes'); ?></label>
                &nbsp;
                <input type="radio"
                       id="delete_images_no"
                       name="image_options[deleteImg]"
                       value="0"
                       <?php checked(0, $delete_image_files); ?>/>
                <label for="delete_images_no"><?php _e('No'); ?></label>
			</p>
		</td>
	</tr>
	<tr>
		<td>
			<label 	for="automatic_resize" 
					title="<?php echo esc_attr($automatic_resize_help); ?>"
               		class="tooltip">
					<?php esc_html_e($automatic_resize_label)?>
			</label>
		</td>
		<td colspan="2">
			<p class="description">
                <input type="radio"
                       id="automatic_resize"
                       name="image_options[imgAutoResize]"
                       value="1"
                    <?php checked(1, $automatic_resize ? 1 : 0); ?>/>
                <label for="automatic_resize"><?php _e('Yes'); ?></label>
                &nbsp;
                <input type="radio"
                       id="automatic_resize_no"
                       name="image_options[imgAutoResize]"
                       value="0"
                    <?php checked(0, $automatic_resize ? 1 : 0); ?>/>
                <label for="automatic_resize_no"><?php _e('No'); ?></label>
            </p>
		</td>
	</tr>
	<tr>
		<td>
			<label 	for="resize_images" 
					title="<?php echo esc_attr($resize_images_help); ?>"
               		class="tooltip">
					<?php esc_html_e($resize_images_label) ?>
			</label>
		</td>
		<td class="column2" style="white-space: nowrap">
			<label for="image_width"><?php esc_html_e($resized_image_width_label)?></label>
			<input
				type="text"
				id="image_width"
				maxlength="4"
				name="image_options[imgWidth]"
				value="<?php echo esc_attr($resized_image_width) ?>"
			/>
			&nbsp;<label for="image_height"><?php esc_html_e($resized_image_height_label)?></label>
			<input
				type="text"
				id="image_height"
				maxlength="4"
				name="image_options[imgHeight]"
				value="<?php echo esc_attr($resized_image_height) ?>"
			/>
		</td>
		<td>
			<div class="column_wrapper">
				<label for="image_quality"><?php esc_html_e($resized_image_quality_label)?></label>
				<select id="image_quality" name="image_options[imgQuality]">
				<?php for($i=100; $i>50; $i--): ?>
					<option
						<?php selected($i, $resized_image_quality) ?>
						value="<?php echo esc_attr($i)?>"><?php esc_html_e($i) ?>%</option>
				<?php endfor ?>
				</select>
			</div>
		</td>
	</tr>
	<tr>
		<td>
			<label for="backup_images">
				<?php esc_html_e($backup_images_label)?>
			</label>
		</td>
		<td colspan="2">
			<input
				id="backup_images_yes"
				name="image_options[imgBackup]"
				value="1"
				type="radio"
				<?php checked(1, $backup_images ? 1 : 0)?>
			/>
			<label for="backup_images_yes">
				<?php esc_html_e($backup_images_yes_label)?>
			</label>
			&nbsp;
			<input
				id="backup_images_no"
				name="image_options[imgBackup]"
				value="0"
				type="radio"
				<?php checked(0, $backup_images ? 1 : 0)?>
			/>
			<label for="backup_images_no">
				<?php esc_html_e($backup_images_no_label)?>
			</label>
		</td>
	</tr>
	<tr id="sorting_options_row">
		<td class="column1">
			<label for="image_sorting_order">
				<?php esc_html_e($sorting_order_label) ?>
			</label>
		</td>
		<td>
			<select name="image_options[galSort]" id="image_sorting_order">
				<?php foreach ($sorting_order_options as $label => $value): ?>
				<option value="<?php echo esc_attr($value) ?>" <?php selected($value, $sorting_order)?>>
					<?php esc_html_e($label) ?>
				</option>
				<?php endforeach ?>
			</select>
		</td>
		<td class="column3">
			<label for="image_sorting_direction">
				<?php esc_html_e($sorting_direction_label) ?>
			</label>
			<select name="image_options[galSortDir]" id="image_sorting_direction">
			<?php foreach ($sorting_direction_options as $label => $value): ?>
			<option value="<?php echo esc_attr($value) ?>" <?php selected($value, $sorting_direction)?>>
				<?php esc_html_e($label) ?>
			</option>
			<?php endforeach ?>
			</select>
		</td>
	</tr>
	<tr>
		<td>
			<label 	for="show_related_images" 
					title="<?php echo esc_attr($show_related_images_help); ?>"
               		class="tooltip">
					<?php esc_html_e($show_related_images_label)?>
			</label>
		</td>
		<td colspan="2">
			<p class="description">
                <input type="radio"
                       id="show_related_images"
                       name="image_options[activateTags]"
                       value="1"
                       <?php checked(1, $show_related_images); ?>/>
                <label for="show_related_images"><?php _e('Yes'); ?></label>
                &nbsp;
                <input type="radio"
                       id="show_related_images_no"
                       name="image_options[activateTags]"
                       value="0"
                       <?php checked(0, $show_related_images); ?>/>
                <label for="show_related_images_no"><?php _e('No'); ?></label>
			</p>
		</td>
	</tr>
	<tr>
		<td colspan="3">
			<a
				href="#"
				class="nextgen_advanced_toggle_link"
				id="related_images_advanced_toggle"
				rel="related_images_advanced_form"
				hidden_label="<?php echo esc_attr($related_images_hidden_label)?>"
				active_label="<?php echo esc_attr($related_images_active_label)?>"
			><?php esc_html_e($related_images_hidden_label) ?></a>
		</td>
	</tr>
	<tbody id="related_images_advanced_form" class="hidden">
		<tr>
			<td>
				<label for="match_related_images">
					<?php esc_html_e($match_related_images_label) ?>
				</label>
			</td>
			<td>
				<select id="match_related_images" name="image_options[appendType]">
				<?php foreach ($match_related_image_options as $label => $value): ?>
					<option
						value="<?php echo esc_attr($value)?>"
						<?php selected($value, $match_related_images)?>
					>
					<?php esc_html_e($label) ?>
					</option>
				<?php endforeach ?>
				</select>
			</td>
			<td class="column3">
				<label for="max_related_images">
					<?php esc_html_e($max_related_images_label)?>
				</label>
				<input
					id="max_related_images"
					type="text"
					name="image_options[maxImages]"
					value="<?php echo esc_attr($max_related_images)?>"
				/>
			</td>
		</tr>
		<tr>
			<td>
				<label for="related_images_heading">
					<?php esc_html_e($related_images_heading_label) ?>
				</label>
			</td>
			<td colspan="2">
				<input id="related_images_heading" type="text" name="image_options[relatedHeading]"
							 value="<?php echo esc_attr($related_images_heading)?>"/>
			</td>
		</tr>
	</tbody>
</table>
