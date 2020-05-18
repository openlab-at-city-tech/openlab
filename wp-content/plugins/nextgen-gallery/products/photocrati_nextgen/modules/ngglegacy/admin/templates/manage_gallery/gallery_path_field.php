<span class="field gallery_path_field">
	<input
		type="text"
		name="path"
		id="gallery_path"
		value="<?php echo esc_attr(preg_replace('#[/\\\]+#', DIRECTORY_SEPARATOR, $gallery->path )); ?>"
        <?php if (is_multisite()) echo "disabled"; ?>
		/>
</span>