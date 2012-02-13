<?php
    
	function cets_embedGmaps_menu() {
	  add_options_page('Google Maps Embed Options', 'Google Maps', 8, 'cets_embedGmaps_options', 'cets_embedGmaps_options');
	}
	
	function cets_embedGmaps_options() {
	  echo '<div class="wrap">';
	  echo '<form method="post" action="options.php">';
	 settings_fields( 'cets_embedGmaps-group' );

	  ?>
	  <h2>Google Maps Embedding Options.</h2>
	  <p>These settings can be over-ridden on a map-by-map basis when maps are inserted.</p>
	  <table class="form-table">
		
		<tr valign="top">
		<th scope="row">Default Height</th>
		<td><input type="text" name="cets_embedGmaps_height" value="<?php echo get_option('cets_embedGmaps_height'); ?>" /> <span class="setting-description">Recommended default: 425</span> </td>
		</tr>
		<tr valign="top">
		<th scope="row">Default Width</th>
		<td><input type="text" name="cets_embedGmaps_width" value="<?php echo get_option('cets_embedGmaps_width'); ?>" /> <span class="setting-description">Recommended default: 350</span> </td>
		</tr>
		
		<tr valign="top">
		<th scope="row">Default Margin Height</th>
		<td><input type="text" name="cets_embedGmaps_marginheight" value="<?php echo get_option('cets_embedGmaps_marginheight'); ?>" /> <span class="setting-description">Recommended default: 0</span> </td>
		</tr>
		<tr valign="top">
		<th scope="row">Default Margin Width</th>
		<td><input type="text" name="cets_embedGmaps_marginwidth" value="<?php echo get_option('cets_embedGmaps_marginwidth'); ?>" /> <span class="setting-description">Recommended default: 0</span> </td>
		</tr>
		<tr valign="top">
		<th scope="row">Default Frame Border</th>
		<td><input type="text" name="cets_embedGmaps_frameborder" value="<?php echo get_option('cets_embedGmaps_frameborder'); ?>" /> <span class="setting-description">Recommended default: 0</span> </td>
		</tr>
		<tr valign="top">
		<th scope="row">Default Scrolling</th>
		<td><select name="cets_embedGmaps_scrolling">
			<option value="no"<?php if (get_option('cets_embedGmaps_scrolling') == 'no') echo ' selected'; ?>>no</option>
			<option value="yes"<?php if (get_option('cets_embedGmaps_scrolling') == 'yes') echo ' selected'; ?>>yes</option>
			<option value="auto"<?php if (get_option('cets_embedGmaps_scrolling') == 'auto') echo ' selected'; ?>>auto</option>
		</select> <span class="setting-description">Recommended default: no</span> </td>
		</tr>
		

	  </table>
	  
	  <input type="hidden" name="action" value="update" />
	  <input type="hidden" name="page_options" value="cets_embedGmaps_width,cets_embedGmaps_height,cets_embedGmaps_marginwidth,cets_embedGmaps_marginheight,cets_embedGmaps_frameborder,cets_embedGmaps_scrolling" />
	  <p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>


	  
	  </form>
	  </div>
	  <?php
	}


function register_cets_embedGmaps_settings() { // whitelist options
  	register_setting( 'cets_embedGmaps-group', 'cets_embedGmaps_width' );
 	register_setting( 'cets_embedGmaps-group', 'cets_embedGmaps_height' );
	register_setting( 'cets_embedGmaps-group', 'cets_embedGmaps_marginwidth' );
 	register_setting( 'cets_embedGmaps-group', 'cets_embedGmaps_marginheight' );
	register_setting( 'cets_embedGmaps-group', 'cets_embedGmaps_frameborder' );
 	register_setting( 'cets_embedGmaps-group', 'cets_embedGmaps_scrolling' );
}

if ( is_admin() ){
	add_action('admin_menu', 'cets_EmbedGmaps_menu');
	add_action( 'admin_init', 'register_cets_EmbedGmaps_settings' );


}
	
?>