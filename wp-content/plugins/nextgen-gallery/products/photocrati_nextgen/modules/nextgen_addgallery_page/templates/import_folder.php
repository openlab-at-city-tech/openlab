<p><?php esc_html_e( 'Select a folder to import.', 'nggallery' ); ?></p>
<div id="file_browser">
</div>
<p>
	<label for="import_gallery_title">
		Gallery title
		<input type="text" name="import_gallery_title" id="import_gallery_title" placeholder="<?php esc_attr_e( 'Leave blank for folder name', 'nggallery' ); ?>"/>
	</label>
</p>
<p>
	<input type="checkbox" id="import_keep_location" name="keep_location" value="on" /> <label for="import_keep_location"> <?php esc_html_e( 'Keep images in original location. Caution: If you keep images in the original folder and later delete the gallery, the images in that folder might be deleted depending on your settings.', 'nggallery' ); ?></label><br/><br/>
	<input type="button" id="import_button" name="import_folder" value="<?php esc_attr_e( 'Import Folder', 'nggallery' ); ?>" class="button-primary"/>
</p>
<script type="text/javascript">
	var selected_folder = null;
	jQuery(function($){
		// Only run this function once!
		if (typeof($(window).data('ready')) == 'undefined')
			$(window).data('ready', true);
		else return;

		// Post params
		var browse_params = {
			action: 'browser_folder',
			nonce: '<?php echo esc_js( $browse_nonce ); ?>'
		};
		browse_params.action = 'browse_folder';

		// Render file browser
		$('#file_browser').fileTree({
			root:           '/',
			script:         photocrati_ajax.url,
			post_params:    browse_params
		}, function(file){
			selected_folder = file;
			$('#file_browser a').each(function(){
				$(this).removeClass('selected_folder');
			})
			$('#file_browser a[rel="'+file+'"]').addClass('selected_folder');
			file = file.split("/");
			file.pop();
			file = '/'+file.pop();
			$('#import_button').val("Import "+file);
		});

		// Import the folder
		$('#import_button').on('click',function(e){
			e.preventDefault();

			// Show progress bar
			var progress_bar =  $.nggProgressBar({
				title: '<?php esc_html_e( 'Importing gallery', 'nggallery' ); ?>',
				infinite: true,
				starting_value: '<?php esc_html_e( 'In Progress...', 'nggallery' ); ?>'
			});

			// Start importing process
			var post_params = {
				nonce: '<?php echo esc_js( $import_nonce ); ?>',
				action: "import_folder",
				folder: selected_folder,
				keep_location: $('#import_keep_location').is(":checked") ? 'on' : 'off',
				gallery_title: $('#import_gallery_title').val()
			}

			$.post(photocrati_ajax.url, post_params, function(response){
				if (typeof(response) != 'object') response = JSON.parse(response);
				if (typeof(response.error) == 'string') {
					progress_bar.set(response.error);
					progress_bar.close(4000);
				}
				else {
					<?php
					$manage_url_base = esc_js( admin_url( 'admin.php?page=nggallery-manage-gallery&mode=edit&gid=' ) );
					// translators: {count} is replaced by JS with the number of imported images, {manage_url} is replaced with the gallery edit URL.
					$message = __( 'Done! Successfully imported {count} images. <a href="{manage_url}" target="_blank">Manage gallery</a>', 'nggallery' );
					// translators: {warning} is replaced by JS with the list of files that could not be imported.
					$warning_message = __( 'Some files were skipped: {warning}', 'nggallery' );
					?>
					var manage_url_base = '<?php echo $manage_url_base; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped with esc_js() when assigned above. ?>';
					var message = '<?php echo wp_kses_post( $message ); ?>';
					message = message.replace('{count}', response.image_ids.length);
					message = message.replace('{manage_url}', manage_url_base + response.gallery_id);

					// A folder where some files imported and others failed comes back with a
					// warning rather than an error - the import partly succeeded, so the count
					// still stands, but the dropped names have to be shown or the drop is
					// invisible. Mirrors the ZIP endpoint's reader in upload_images.php.
					if (typeof(response.warning) == 'string') {
						var warning_message = '<?php echo wp_kses_post( $warning_message ); ?>';
						message = message + "<br/>" + warning_message.replace('{warning}', response.warning);
					}

					progress_bar.close(100);
					$.gritter.add({
						title: '<?php esc_html_e( 'Upload complete', 'nggallery' ); ?>',
						text: message,
						sticky: true
					});
				}
			});
		})
	});
</script>
