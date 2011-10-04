<?php
/**
 * Group API
 *
 * http://codex.buddypress.org/developer-docs/group-extension-api/
 */
class BP_Groupblog_Extension extends BP_Group_Extension {	
	
	//var $enable_nav_item = $this->enable_nav_item();
		  
	function bp_groupblog_extension() {
		global $bp;
	
		$this->name = __( 'Group Blog', 'groupblog' );
		$this->slug = 'group-blog';
		
		$this->enable_create_step = true;
		$this->create_step_position = 15;
		
		$this->enable_edit_item = true;
		
		$this->nav_item_name = 'Blog';
		$this->nav_item_position = 30;
		$this->enable_nav_item = false;
		$this->template_file = 'groupblog/blog';
	}
	
	function create_screen() {
		global $bp, $groupblog_create_screen;
		
		if ( !bp_is_group_creation_step( $this->slug ) )
			return false;
					
		$groupblog_create_screen = true;
						
		bp_groupblog_signup_blog();
		
		echo '<input type="hidden" name="groupblog-group-id" value="' . $bp->groups->current_group->id . '" />';
		echo '<input type="hidden" name="groupblog-create-save" value="groupblog-create-save" />';
							
		wp_nonce_field( 'groups_create_save_' . $this->slug );
	}

	function create_screen_save() {	
	}

	function edit_screen() {
		global $bp;
		
		if ( !bp_is_group_admin_screen( $this->slug ) )
			return false;
				  											
		bp_groupblog_signup_blog();
									
	}

	function edit_screen_save() {
	}
	
	function display() {
	}
	
	function widget_display() {
	}

	/*
	function enable_nav_item() {
		global $bp;
	
		if ( groups_get_groupmeta( $bp->groups->current_group->id, 'groupblog_enable_blog' ) )
			return true;
		else
			return false;
	}
	*/
	
}
bp_register_group_extension( 'BP_Groupblog_Extension' );

?>