<span class="field gallery_create_page_field">
	<select name="parent_id" id="gallery_create_page_parent">
		<option value="0"><?php _e('Main Page (no parent)', 'nggallery'); ?></option>
		<?php foreach ($pages as $page): ?>
			<option value="<?php echo esc_attr($page->ID) ?>">
				<?php echo esc_html($page->post_title)?>
			</option>
		<?php endforeach ?>
	</select>
	<input type="submit" id="group" value="<?php _e('Add page', 'nggallery'); ?>" name="addnewpage" class="button-secondary action">
</span>