<?php 
/**
 * Manage Control Form
 * 
 * A form to allow the user to quickly select another
 * form to edit. 
 * 
 * @package   Easy_Google_Fonts
 * @author    Sunny Johal - Titanium Themes <support@titaniumthemes.com>
 * @license   GPL-2.0+
 * @link      http://wordpress.org/plugins/easy-google-fonts/
 * @copyright Copyright (c) 2016, Titanium Themes
 * @version   1.4.4
 * 
 */
?>

<div class="manage-controls">
	<form autocomplete="off" id="" action="" method="get" enctype="multipart/form-data">
		<?php if ( $this->is_edit_screen() ) : ?>
			<input type="hidden" name="page" value="<?php echo $this->plugin_slug; ?>">
			<input name="action" type="hidden" value="edit">
			<label class="selected-control" for="control"><?php _e( 'Select a font control to edit:', $this->plugin_slug ); ?></label>
			<select autocomplete="off" name="control" id="control">
				<?php foreach ( $this->custom_controls as $custom_control_id => $custom_control_name ) : ?>
					<option value="<?php echo $custom_control_id; ?>" <?php if( $custom_control_id == $this->control_selected_id ) : ?>selected<?php endif; ?>><?php echo $custom_control_name; ?></option>
				<?php endforeach; ?>
			</select>
			<?php submit_button( __( 'Select', $this->plugin_slug ), 'secondary', '', false ); ?>
			<span class="add-new-control-action">
				or <a href="<?php echo $this->create_url; ?>"><?php _e( 'create a new font control', $this->plugin_slug ); ?></a>		
			</span><!-- /add-new-control-action -->					
		<?php elseif ( $this->is_create_screen() ) : ?>
			<label><?php _e( 'Create a new Font Control. ', $this->plugin_slug ); ?></label>
		<?php endif; ?>
	</form>	
</div><!-- END .manage-controls -->
