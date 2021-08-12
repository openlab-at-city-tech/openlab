<?php  
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

	function nggallery_wpmu_setup()  {	
	
	//to be sure
	if ( !is_super_admin() )
 		die('You are not allowed to call this page.');

	// Get current settings		 
	$ngg_options = C_NextGen_Global_Settings::get_instance();

    $messagetext = [];

	if ( isset($_POST['updateoption']) ) {	
		check_admin_referer('ngg_wpmu_settings');

		// Set all options other than the gallerypath
		$valid_options = [
			'wpmuRoles', 'wpmuQuotaCheck', 'wpmuZipUpload', 'wpmuImportFolder', 'wpmuRoles'
		];
		foreach ($valid_options as $option_name) {
			$option_value = isset($_POST[$option_name]) ? intval($_POST[$option_name]) : 0;
			$ngg_options->set($option_name, $option_value);
		}

		if (isset($_POST['gallerypath'])) {
			$new_gallerypath = trailingslashit($_POST['gallerypath']);
			$fs               = C_Fs::get_instance();
			$root             = $fs->get_document_root('galleries');
			if ($root[0] != DIRECTORY_SEPARATOR) $root = DIRECTORY_SEPARATOR . $root;
			$gallery_abspath  = $fs->get_absolute_path($fs->join_paths($root, $new_gallerypath));
			if ($gallery_abspath[0] != DIRECTORY_SEPARATOR) $gallery_abspath = DIRECTORY_SEPARATOR.$gallery_abspath;
			if (strpos($gallery_abspath, $root) === FALSE) {
				$messagetext[] = sprintf(__("Gallery path must be located in %s.", 'nggallery'), $root);
			}
			else if (preg_match('\.+[/\\]', $new_gallerypath)) {
				$messagetext[] = __("Gallery path cannot include relative paths.", 'nggallery');
			}
			else {
				$ngg_options->set('gallerypath', $new_gallerypath);
				$ngg_options->save();
			}
		}
		$ngg_options->save();
		$messagetext[] = __('Updated successfully.','nggallery');
		
		$messagetext = implode(" ", $messagetext);
	}
	
	// message windows
	if( !empty($messagetext) ) { echo '<!-- Last Action --><div id="message" class="updated fade"><p>'.$messagetext.'</p></div>'; }
	
	?>

	<div class="wrap">
		<h2><?php _e('Network Options','nggallery'); ?></h2>
		<form name="generaloptions" method="post">
		<?php wp_nonce_field('ngg_wpmu_settings') ?>
			<table class="form-table">
				<tr valign="top">
					<th align="left"><?php _e('Gallery path','nggallery') ?></th>
					<td><input type="text" size="50" name="gallerypath" value="<?php echo $ngg_options->get('gallerypath'); ?>" /><br />
					<?php _e('This is the default path for all blogs. With the placeholder %BLOG_ID% you can organize the folder structure better.','nggallery') ?>
                    <?php echo str_replace('%s', '<code>wp-content/uploads/sites/%BLOG_ID%/nggallery/</code>', __('The default setting should be %s', 'nggallery')); ?>
                    </td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Enable upload quota check','nggallery') ?>:</th>
					<td><input name="wpmuQuotaCheck" type="checkbox" value="1" <?php checked('1', $ngg_options->get('wpmuQuotaCheck')); ?> />
					<?php _e('Should work if the gallery is bellow the blog.dir','nggallery') ?>
					</td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Enable zip upload option','nggallery') ?>:</th>
					<td><input name="wpmuZipUpload" type="checkbox" value="1" <?php checked('1', $ngg_options->get('wpmuZipUpload')); ?> />
					<?php _e('Allow users to upload zip folders.','nggallery') ?>
					</td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Enable import function','nggallery') ?>:</th>
					<td><input name="wpmuImportFolder" type="checkbox" value="1" <?php checked('1', $ngg_options->get('wpmuImportFolder')); ?> />
					<?php _e('Allow users to import images folders from the server.','nggallery') ?>
					</td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Enable roles/capabilities','nggallery') ?>:</th>
					<td><input name="wpmuRoles" type="checkbox" value="1" <?php checked('1', $ngg_options->get('wpmuRoles')); ?> />
					<?php _e('Allow users to change the roles for other blog authors.','nggallery') ?>
					</td>
				</tr>
			</table> 				
			<div class="submit"><input type="submit" name="updateoption" value="<?php _e('Update') ;?>"/></div>
		</form>	
	</div>	

	<?php
}