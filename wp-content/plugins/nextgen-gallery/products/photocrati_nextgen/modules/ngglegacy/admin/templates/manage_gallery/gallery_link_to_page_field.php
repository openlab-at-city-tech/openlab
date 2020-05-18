<span class="field gallery_link_to_page_field">
	<select id="gallery_link_to_page" name="pageid">
		<option <?php selected(0, $gallery->pageid ? $gallery->pageid : 0) ?>value="0"><?php _e('Not linked', 'nggallery'); ?></option>
		<?php foreach($pages as $page):?>
			<option <?php selected($page->ID, $gallery->pageid) ?> value="<?php echo esc_attr($page->ID) ?>">
				<?php echo esc_html($page->post_title)?>
			</option>
		<?php endforeach ?>
	</select>
</span>