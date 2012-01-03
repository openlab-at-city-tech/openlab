<?php


//show blog and pages on menu
class WDS_Group_Extension extends BP_Group_Extension {

	var $enable_nav_item = true;
	var $enable_create_step = false;
	function wds_group_extension() {
		global $bp;
		$group_id=$bp->groups->current_group->id;
		$wds_bp_group_site_id=groups_get_groupmeta($group_id, 'wds_bp_group_site_id' );
		if($wds_bp_group_site_id!=""){
		  $this->name = 'Activity';
		  $this->slug = 'activity';
  		  $this->nav_item_position = 10;
		}
	}

	function create_screen() {
		if ( !bp_is_group_creation_step( $this->slug ) )
			return false;
		wp_nonce_field( 'groups_create_save_' . $this->slug );
	}

	function create_screen_save() {
		global $bp;

		check_admin_referer( 'groups_create_save_' . $this->slug );

		groups_update_groupmeta( $bp->groups->new_group_id, 'my_meta_name', 'value' );
	}

	function edit_screen() {
		if ( !bp_is_group_admin_screen( $this->slug ) )
			return false; ?>

		<h2><?php echo esc_attr( $this->name ) ?></h2>
        <?php
		wp_nonce_field( 'groups_edit_save_' . $this->slug );
	}

	function edit_screen_save() {
		global $bp;

		if ( !isset( $_POST['save'] ) )
			return false;

		check_admin_referer( 'groups_edit_save_' . $this->slug );

		/* Insert your edit screen save code here */

		/* To post an error/success message to the screen, use the following */
		if ( !$success )
			bp_core_add_message( __( 'There was an error saving, please try again', 'buddypress' ), 'error' );
		else
			bp_core_add_message( __( 'Settings saved successfully', 'buddypress' ) );

		bp_core_redirect( bp_get_group_permalink( $bp->groups->current_group ) . '/admin/' . $this->slug );
	}

	function display() {
		global $bp;
		gconnect_locate_template( array( 'groups/single/group-header.php' ), true );
		gconnect_locate_template( array( 'groups/single/activity.php' ), true );

		/*$group_id=$bp->groups->current_group->id;
		$wds_bp_group_site_id=groups_get_groupmeta($group_id, 'wds_bp_group_site_id' );
		if($wds_bp_group_site_id!=""){
		  switch_to_blog($wds_bp_group_site_id);
		  $pages = get_pages();
		  ?>
		  <div role="navigation" id="subnav" class="item-list-tabs no-ajax">
			  <ul>
				 <?php foreach ($pages as $pagg) {?>
					<li class="current"><a href="?page=<?php echo $pagg->ID;?>"><?php echo $pagg->post_title;?></a></li>
				  <?php }?>
			  </ul>
		  </div>
		  <?php
		  if($_GET['page']){
			  $id=$_GET['page'];
			  $post = get_post($id);
			  echo $post->post_content;
		  }
		  restore_current_blog();
		}*/
	}

	function widget_display() { ?>
		<div class=&quot;info-group&quot;>
			<h4><?php echo esc_attr( $this->name ) ?></h4>
		</div>
		<?php
	}

}
//bp_register_group_extension( 'WDS_Group_Extension' );

//Change "Group" to something else
class buddypress_Translation_Mangler {
 /*
  * Filter the translation string before it is displayed.
  * 
  * This function will choke if we try to load it when not viewing a group page or in a group loop
  * So we bail in cases where neither of those things is present, by checking $groups_template
  */
 function filter_gettext($translation, $text, $domain) {
   global $groups_template;
   
   if ( empty( $groups_template->group ) ) {
   	return $translation;
   }
   
   $group_id = bp_get_group_id();
   $grouptype = groups_get_groupmeta( $group_id, 'wds_group_type' );
   $uc_grouptype = ucfirst($grouptype);
   $translations = &get_translations_for_domain( 'buddypress' );
   switch($text){
	case "Forum":
     return $translations->translate( "Discussion" );
     break;
	case "Group Forum":
     return $translations->translate( "$uc_grouptype Discussion" );
     break;
	case "Group Forum Directory":
     return $translations->translate( "" );
     break;
	case "Group Forums Directory":
     return $translations->translate( "Group Discussions Directory" );
     break;
	case "Join Group":
     return $translations->translate( "Join Now!" );
     break;
	case "You successfully joined the group.":
     return $translations->translate( "You successfully joined!" );
     break;
	case "Recent Discussion":
     return $translations->translate( "Recent Forum Discussion" );
     break;
    case "This is a hidden group and only invited members can join.":
     return $translations->translate( "This is a hidden " . $grouptype . " and only invited members can join." );
     break;
    case "This is a private group and you must request group membership in order to join.":
     return $translations->translate( "This is a private " . $grouptype . " and you must request " . $grouptype . " membership in order to join." );
     break;
    case "This is a private group. To join you must be a registered site member and request group membership.":
     return $translations->translate( "This is a private " . $grouptype . ". To join you must be a registered site member and request " . $grouptype . " membership." );
     break;
    case "This is a private group. Your membership request is awaiting approval from the group administrator.":
     return $translations->translate( "This is a private " . $grouptype . ". Your membership request is awaiting approval from the " . $grouptype . " administrator." );
     break;
    case "said ":
     return $translations->translate( "" );
     break;
  }
  return $translation;
 }
}
add_filter('gettext', array('buddypress_Translation_Mangler', 'filter_gettext'), 10, 4);


/**
 * Add members to wpms website if attached to bp group and they are a group member
 *
 * @todo With an updated of BP Groupblog, this should not be necssary. As it is, it adds a lot of
 *       overhead, and should be rewritten to avoid PHP warnings.
 */
add_action('init','wds_add_group_members_2_blog');
function wds_add_group_members_2_blog(){
	global $wpdb, $user_ID, $bp;
	if ( bp_get_current_group_id() ) {
	     $group_id = $bp->groups->current_group->id;
	     $blog_id = groups_get_groupmeta($group_id, 'wds_bp_group_site_id' );
	}
	if($user_ID!=0 && !empty( $group_id ) && !empty( $blog_id ) ){
		switch_to_blog($blog_id);
		if(!is_user_member_of_blog($blog_id)){
		      $sql="SELECT user_title FROM {$bp->groups->table_name}_members WHERE group_id = $group_id and user_id=$user_ID AND is_confirmed='1'";
		      $rs = $wpdb->get_results( $sql );
		      if ( count( $rs ) > 0 ) {
			      foreach( $rs as $r ) {
				      $user_title = $r->user_title;
			      }
			      if($user_title=="Group Admin"){
				      $role="administrator";
			      }elseif($user_title=="Group Mod"){
				      $role="editor";
			      }else{
				      $role="author";
			      }
			      add_user_to_blog( $blog_id, $user_ID, $role );
		      }
		}
		restore_current_blog();
	}
}

/**
 * Allow super admins to edit any BuddyPress Doc
 */
function openlab_allow_super_admins_to_edit_bp_docs( $user_can, $action ) {
	global $bp;
	
	if ( 'edit' == $action ) {
		if ( is_super_admin() || bp_loggedin_user_id() == get_the_author_meta( 'ID' ) || $user_can ) {
			$user_can = true;
			$bp->bp_docs->current_user_can[$action] = 'yes';
		} else {
			$user_can = false;
			$bp->bp_docs->current_user_can[$action] = 'no';
		}
	}
	
	return $user_can;
}
add_filter( 'bp_docs_current_user_can', 'openlab_allow_super_admins_to_edit_bp_docs', 10, 2 );


?>