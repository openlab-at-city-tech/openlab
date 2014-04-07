<?php

	/*
	 * Advanced tab content
	 */
	 
	if ( ! defined( 'ABSPATH' ) ) { exit; }
	
	// get current options
	global $gdeoptions, $healthy, $wp_version;
	$g = $gdeoptions;
	
?>

<form action="" method="post">
<?php wp_nonce_field('update-adv-opts', '_advanced'); ?>

	<?php gde_help_link( GDE_ADVOPT_URL, 'right' ); ?>
	<h3><?php _e('Plugin Behavior', 'gde'); ?></h3>
	
	<table class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row"><?php _e('Editor Integration', 'gde'); ?></th>
				<td>
<?php
	gde_opts_checkbox( 'ed_disable', __('Disable all editor integration', 'gde'), '', 1 );
	gde_opts_checkbox( 'ed_embed_sc', __('Insert shortcode from Media Library by default', 'gde'), 'ed-embed', 1 );
	gde_opts_checkbox( 'ed_extend_upload', __('Allow uploads of all supported media types', 'gde'), 'ed-upload', 1 );
?>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Maximum File Size', 'gde'); ?></th>
				<td>
<?php
	gde_profile_text( $g['file_maxsize'], 'file_maxsize', '', 3 );
	echo " " . __('MB', 'gde') ."<br/>";
?>
				<span class="gde-fnote"><?php _e( "Very large files (typically 8-12MB) aren't supported by Google Doc Viewer", 'gde' ); ?></span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Error Handling', 'gde'); ?></th>
				<td>
<?php
	gde_opts_checkbox( 'error_display', __('Show error messages inline (otherwise, they appear in HTML comments)', 'gde'), '', 1 );
	gde_opts_checkbox( 'error_check', __('Check for errors before loading viewer', 'gde'), '', 1 );
	if ( GDE_DX_LOGGING > 0 ) {
		gde_opts_checkbox( 'error_log', __('Enable extended diagnostic logging <em>(manually enabled)</em>', 'gde'), '', 0, true );
	} else {
		gde_opts_checkbox( 'error_log', __('Enable extended diagnostic logging', 'gde'), '', 0 );

		$tmp = __('clear log', 'gde'); // not implemented yet
	}
	if ( gde_log_available() ) {
		//$url = GDE_PLUGIN_URL . 'libs/lib-service.php?viewlog=all';
		echo '<span style="vertical-align:middle;">&nbsp;&nbsp; <a href="#viewlog" class="gde-viewlog" id="log-2">' . 
		__('show log', 'gde') . '</a>';
	}
?>
				</td>
			</tr>
			<tr valign="top" style="display:none;">
				<th scope="row"><?php _e('Version Notifications', 'gde'); ?></th>
				<td>
					<input type="hidden" name="beta_check" value="no">
					<span class="gde-fnote" id="beta-h"></span>
				</td>
			</tr>
		</tbody>
	</table>
	
	<h3><?php _e('Google Analytics', 'gde'); ?></h3>
		
		<?php _e('To use Google Analytics integration, the GA tracking code must already be installed on your site.', 'gde'); ?>
		<a href="https://developers.google.com/analytics/devguides/collection/gajs/asyncTracking" target="_blank"><?php _e('More Info', 'gde'); ?></a>
		
		<table class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row"><?php _e('Event Tracking', 'gde'); ?></th>
				<td>
					<select name="ga_enable" id="ga_enable">
<?php
	gde_profile_option( $g['ga_enable'], 'yes', __('Enabled', 'gde'), __('Track events in Google Analytics', 'gde') );
	gde_profile_option( $g['ga_enable'], 'compat', __('Enabled (Compatibility Mode)', 'gde'), __('Track events using older GDE format (< 2.5)', 'gde') );
	gde_profile_option( $g['ga_enable'], 'no', __('None', 'gde'), __('Disable Google Analytics integration', 'gde') );
?>
					</select><br/>
					<span class="gde-fnote" id="ga-h"></span>
				</td>
			</tr>
			<tr valign="top" id="ga-cat">
				<th scope="row"><?php _e('Category', 'gde'); ?></th>
				<td>
<?php
	gde_profile_text( $g['ga_category'], 'ga_category', '', 35 );
?>
				</td>
			</tr>
			<tr valign="top" id="ga-label">
				<th scope="row"><?php _e('Label', 'gde'); ?></th>
				<td>
					<select name="ga_label" id="ga_label">
<?php
	gde_profile_option( $g['ga_label'], 'url', __('Document URL', 'gde') );
	gde_profile_option( $g['ga_label'], 'file', __('Document Filename', 'gde') );
?>
					</select>
				</td>
			</tr>
		</tbody>
		</table>
	
	<p class="gde-submit">
		<input id="adv-submit" class="button-primary" type="submit" value="<?php _e('Save Changes', 'gde'); ?>" name="submit">
	</p>
	
</form>

<br/>
<form action="" method="post" id="gde-backup">

	<h3><?php _e('Backup and Import', 'gde'); ?></h3>

<?php
	if ( ! $healthy ) {
		echo "<p>" . gde_show_error( __('Unable to load profile settings. Please re-activate GDE and if the problem persists, request help using the "Support" tab.', 'gde') ) . "</p>\n";
	} else {
?>

	<p><?php _e('Download a file to your computer containing your profiles, settings, or both, for backup or migration purposes.', 'gde'); ?></p>
	
	<p>
		<input type="radio" value="all" name="type" id="backup-all" checked="checked"><label for="backup-all"> <?php _e('All Profiles and Settings', 'gde'); ?></label> &nbsp;&nbsp; 
		<input type="radio" value="profiles" name="type" id="backup-pro"><label for="backup-pro"> <?php _e('Profiles', 'gde'); ?></label> &nbsp;&nbsp; 
		<input type="radio" value="settings" name="type" id="backup-set"><label for="backup-set"> <?php _e('Settings', 'gde'); ?></label>
	</p>
	
	<p class="submit" style="padding-top: 0 !important;">
		<input type="submit" value="<?php _e('Download Export File', 'gde'); ?>" class="button-secondary" id="export-submit" name="submit">
	</p>
</form>

<form enctype="multipart/form-data" action="" method="post" id="gde-import">
<?php wp_nonce_field('import-opts', '_advanced_import'); ?>

	<p>
		<label for="upload"><?php _e('To import, choose a file from your computer:', 'gde'); ?></label>
		<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo wp_max_upload_size(); ?>" />
		<input type="file" id="upload" name="import" size="25" />
	</p>
	
	<p class="submit" style="padding-top: 0 !important;">
		<input type="submit" name="submit" id="import-submit" class="button" value="<?php _e('Upload File and Import', 'gde'); ?>"  />
	</p>

<?php
	}
?>

</form>
