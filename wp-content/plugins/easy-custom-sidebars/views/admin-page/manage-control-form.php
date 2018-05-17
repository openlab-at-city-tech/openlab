<?php 
/**
 * Manage Control Form
 * 
 * A form to allow the user to quickly select another
 * sidebar to edit. 
 * 
 * @package     Easy_Custom_Sidebars
 * @author      Sunny Johal - Titanium Themes <support@titaniumthemes.com>
 * @license     GPL-2.0+
 * @copyright   Copyright (c) 2015, Titanium Themes
 * @version     1.0.9
 * 
 */
?>
<div class="manage-sidebars manage-menus">
	<form autocomplete="off" id="" action="" method="get" enctype="multipart/form-data">
		<?php if ( $this->is_edit_screen() ) : ?>
			<input type="hidden" name="page" value="<?php echo $this->plugin_slug; ?>">
			<input name="action" type="hidden" value="edit">
			<label class="selected-menu" for="sidebar"><?php _e( 'Select a sidebar to edit:', 'easy-custom-sidebars' ); ?></label>
			<select autocomplete="off" name="sidebar" id="sidebar">
				<?php foreach ( $this->custom_sidebars as $custom_sidebar_id => $custom_sidebar_name ) : ?>
					<option value="<?php echo $custom_sidebar_id; ?>" <?php if ( $custom_sidebar_id == $this->sidebar_selected_id ) : ?>selected<?php endif; ?>><?php echo $custom_sidebar_name; ?></option>
				<?php endforeach; ?>
				<?php submit_button( __( 'Select', 'easy-custom-sidebars' ), 'secondary', '', false ); ?>
			</select>
			<span class="add-new-menu-action">
				or <a href="<?php echo $this->create_url; ?>"><?php _e( 'create a new sidebar', 'easy-custom-sidebars' ); ?></a>	
			</span>
		<?php elseif ( $this->is_create_screen() ) : ?>
			<label><?php _e( 'Create a new Sidebar.', 'easy-custom-sidebars' ); ?></label>
		<?php endif ?>
	</form>	
</div><!-- END .manage-controls -->
