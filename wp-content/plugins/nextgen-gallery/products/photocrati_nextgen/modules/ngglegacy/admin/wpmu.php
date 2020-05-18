<?php  
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

	function nggallery_wpmu_setup()  {	
	
	//to be sure
	if ( !is_super_admin() )
 		die('You are not allowed to call this page.');

    $messagetext = '';

	// get the options
	$ngg_options = get_site_option('ngg_options');

	if ( isset($_POST['updateoption']) ) {	
		check_admin_referer('ngg_wpmu_settings');
		// get the hidden option fields, taken from WP core
		if ( $_POST['page_options'] )	
			$options = explode(',', stripslashes($_POST['page_options']));
		if ($options) {
			foreach ($options as $option) {
				$option = trim($option);
				$value = isset($_POST[$option]) ? trim($_POST[$option]) : false;
		//		$value = sanitize_option($option, $value); // This does strip slashes on those that need it
				$ngg_options[$option] = $value;
			}
		}

        // the path should always end with a slash	
        $ngg_options['gallerypath']    = trailingslashit($ngg_options['gallerypath']);
		$fs               = C_Fs::get_instance();
		$root             = $fs->get_document_root('galleries');
		$gallery_abspath = $fs->get_absolute_path($fs->join_paths($root, $ngg_options['gallerypath']));
		if ($gallery_abspath[0] != DIRECTORY_SEPARATOR) $gallery_abspath = DIRECTORY_SEPARATOR.$gallery_abspath;
		if (strpos($gallery_abspath, $root) === FALSE) {
            $messagetext = sprintf(__("Gallery path must be located in %s", 'nggallery'), $root);
            $storage = C_Gallery_Storage::get_instance();
            $ngg_options['gallerypath'] = implode(DIRECTORY_SEPARATOR, array('wp-content', 'uploads', 'sites', '%BLOG_ID%', 'nggallery')).DIRECTORY_SEPARATOR;
            unset($storage);
	    }
	    else {
			$messagetext = __('Updated successfully','nggallery');
        }
		update_site_option('ngg_options', $ngg_options);
	}		

    // Show donation message only one time.
    if (isset ( $_GET['hideSupportInfo']) ) {
    	$ngg_options['hideSupportInfo'] = true;
    	update_site_option('ngg_options', $ngg_options);			
    }
	
	// message windows
	if( !empty($messagetext) ) { echo '<!-- Last Action --><div id="message" class="updated fade"><p>'.$messagetext.'</p></div>'; }
	
	?>

	<div class="wrap">
		<h2><?php _e('Network Options','nggallery'); ?></h2>
		<form name="generaloptions" method="post">
		<?php wp_nonce_field('ngg_wpmu_settings') ?>
		<input type="hidden" name="page_options" value="gallerypath,wpmuQuotaCheck,wpmuZipUpload,wpmuImportFolder,wpmuStyle,wpmuRoles,wpmuCSSfile" />
			<table class="form-table">
				<tr valign="top">
					<th align="left"><?php _e('Gallery path','nggallery') ?></th>
					<td><input type="text" size="50" name="gallerypath" value="<?php echo $ngg_options['gallerypath']; ?>" /><br />
					<?php _e('This is the default path for all blogs. With the placeholder %BLOG_ID% you can organize the folder structure better.','nggallery') ?>
                    <?php echo str_replace('%s', '<code>wp-content/uploads/sites/%BLOG_ID%/nggallery/</code>', __('The default setting should be %s', 'nggallery')); ?>
                    </td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Enable upload quota check','nggallery') ?>:</th>
					<td><input name="wpmuQuotaCheck" type="checkbox" value="1" <?php checked('1', $ngg_options['wpmuQuotaCheck']); ?> />
					<?php _e('Should work if the gallery is bellow the blog.dir','nggallery') ?>
					</td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Enable zip upload option','nggallery') ?>:</th>
					<td><input name="wpmuZipUpload" type="checkbox" value="1" <?php checked('1', $ngg_options['wpmuZipUpload']); ?> />
					<?php _e('Allow users to upload zip folders.','nggallery') ?>
					</td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Enable import function','nggallery') ?>:</th>
					<td><input name="wpmuImportFolder" type="checkbox" value="1" <?php checked('1', $ngg_options['wpmuImportFolder']); ?> />
					<?php _e('Allow users to import images folders from the server.','nggallery') ?>
					</td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Enable style selection','nggallery') ?>:</th>
					<td><input name="wpmuStyle" type="checkbox" value="1" <?php checked('1', $ngg_options['wpmuStyle']); ?> />
					<?php _e('Allow users to choose a style for the gallery.','nggallery') ?>
					</td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Enable roles/capabilities','nggallery') ?>:</th>
					<td><input name="wpmuRoles" type="checkbox" value="1" <?php checked('1', $ngg_options['wpmuRoles']); ?> />
					<?php _e('Allow users to change the roles for other blog authors.','nggallery') ?>
					</td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Default style','nggallery') ?>:</th>
					<td>
					<select name="wpmuCSSfile">
					<?php
                        // $csslist = ngg_get_cssfiles();
                        $csslist = C_NextGen_Style_Manager::get_instance()->find_all_stylesheets();
						foreach ($csslist as $key => $a_cssfile) {
							$css_name = $a_cssfile['name'];
							if ($key == $ngg_options['wpmuCSSfile']) {
								$selected = " selected='selected'";
							}
							else $selected = '';
							$css_name = esc_attr($css_name);
							echo "\n\t<option value=\"{$key}\" {$selected}>{$css_name}</option>";
						}
					?>
					</select><br />
					<?php _e('Choose the default style for the galleries.','nggallery') ?>
					</td>
				</tr>
			</table> 				
			<div class="submit"><input type="submit" name="updateoption" value="<?php _e('Update') ;?>"/></div>
		</form>	
	</div>	

	<?php
}