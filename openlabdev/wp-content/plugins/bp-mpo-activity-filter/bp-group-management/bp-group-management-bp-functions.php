<?php

require_once( dirname(__FILE__) . '/bp-group-management-aux.php' );

function bp_group_management_admin_screen() {
	global $wpdb;

	do_action( 'bp_gm_action' );
	
	switch( $_GET['action'] ) {
		case "settings":
			bp_group_management_settings();
			break;
		case "edit":
			bp_group_management_admin_edit();
			break;
		
		case "delete":
			bp_group_management_admin_delete();
			break;
	
		default:
			bp_group_management_admin_main();
	}
}


/* Creates the main group listing page (Dashboard > BuddyPress > Group Management) */
function bp_group_management_admin_main() {
            	

	/* Group delete requests are sent back to the main page. This handles group deletions */
	if( $_GET['group_action'] == 'delete' ) {
		if ( !check_admin_referer( 'bp-group-management-action_group_delete' ) )
				return false;
				
		if ( !bp_group_management_delete_group( $_GET['id'] ) ) { ?>
			<div id="message" class="updated fade"><p><?php _e('Sorry, there was an error.', 'bp-group-management'); ?></p></div>
		<?php } else { ?>
			<div id="message" class="updated fade"><p><?php _e('Group deleted.', 'bp-group-management'); ?></p></div>
		<?php
			do_action( 'groups_group_deleted', $bp->groups->current_group->id );
		}
	}

	?>

          <div class="wrap bp-gm-wrap">
          	
          	
            <h2><?php _e( 'Group Management', 'bp-group-management' ) ?></h2>
            <br />
            <?php 
            	if ( !$options = get_option( 'bp_gm_settings' ) )
            		$per_page = 10;
            	else
            		$per_page = $options['groups_per_page'];
            		
            	$args = array( 'type' => 'alphabetical', 'per_page' => $per_page );
            	
            	if ( $_GET['order'] == 'name' )
            		$args['type'] = 'alphabetical';
            	else if ( $_GET['order'] == 'group_id' )
            		$args['type'] = 'newest';
            	else if ( $_GET['order'] == 'popular' )
            		$args['type'] = 'popular';
            	
            	if ( $_GET['grpage'] )
            		$args['page'] = $_GET['grpage'];
            	else 
            		$args['page'] = 1;
            
            	if( bp_has_groups( $args ) ) : 
            		global $groups_template;
            ?>
            
            <div class="tablenav">
    			<div class="tablenav-pages">
					<span class="displaying-num" id="group-dir-count">
						<?php bp_groups_pagination_count() ?>
					</span>

					<span class="page-numbers" id="group-dir-pag">
						<?php bp_group_management_pagination_links() ?>
					</span>

				</div>
			</div>
            
            
            
            <table width="100%" cellpadding="3" cellspacing="3" class="widefat">
			<thead>
				<tr>
					<th scope="col" class="check-column"></th>
            		<th scope="col" class="bp-gm-group-id-header"><a href="admin.php?page=bp-group-management/bp-group-management-bp-functions.php&amp;order=group_id"><?php _e( 'Group ID', 'bp-group-management' ) ?></a></th>
            		
					<th scope="col"><?php _e( 'Group avatar', 'bp-group-management' ); ?></th>
            		<th scope="col"><a href="admin.php?page=bp-group-management/bp-group-management-bp-functions.php&amp;order=name"><?php _e( 'Group Name', 'bp-group-management' ) ?></a></th>
            		<th scope="col"><?php _e( 'Group type', 'bp-group-management' ); ?></th>
            		<th scope="col"><a href="admin.php?page=bp-group-management/bp-group-management-bp-functions.php&amp;order=group_id"><?php _e( 'Date Created', 'bp-group-management' ) ?></a></th>
            		<th scope="col"><a href="admin.php?page=bp-group-management/bp-group-management-bp-functions.php&amp;order=popular"><?php _e( 'Number of Members', 'bp-group-management' ) ?></a></th>
            		
            		<?php do_action( 'bp_gm_group_column_header' ); ?>
            	</tr>
            </thead>
            
			<tbody id="the-list">
            	<?php while( bp_groups() ) : bp_the_group(); ?> 
   					<?php 
   						if ( !$group )
    						$group =& $groups_template->group;
            		?>	
            		<tr>
            			<th scope="row" class="check-column">
							
						</th>
						
						<th scope="row"  class="bp-gm-group-id">
							<?php bp_group_id(); ?>
						</th>
						
						
						<td scope="row" class="bp-gm-avatar">
  							 <a href="admin.php?page=bp-group-management/bp-group-management-bp-functions.php&action=edit&id=<?php bp_group_id() ?>" class="edit"><?php bp_group_avatar( 'width=35&height=35' ); ?></a>
 						</td>
						
						<td scope="row">
							<?php bp_group_name(); ?>
									<br/>
									<?php
									$controlActions	= array();
									$controlActions[]	= '<a href="admin.php?page=bp-group-management/bp-group-management-bp-functions.php&amp;action=edit&amp;id=' . bp_get_group_id() . '" class="edit">' . __('Members', 'bp-group-management' ) . '</a>';								
									
									
									$controlActions[]	= '<a class="delete" href="admin.php?page=bp-group-management/bp-group-management-bp-functions.php&amp;action=delete&amp;id=' . bp_get_group_id() . '">' . __("Delete") . '</a>';
									
									$controlActions[]   = '<a href="' . bp_get_group_permalink( ) . 'admin">' . __( 'Admin', 'bp-group-management' ) . '</a>'; 
									
									$controlActions[]	= "<a href='" . bp_get_group_permalink() ."' rel='permalink'>" . __('Visit', 'bp-group-management') . '</a>';
									
									$controlActions = apply_filters( 'bp_gm_group_action_links', $controlActions );
									
									?>
									
									<?php if (count($controlActions)) : ?>
									<div class="row-actions">
										<?php echo implode(' | ', $controlActions); ?>
									</div>
									<?php endif; ?>

							
						</td>
						
						<td scope="row">
							<?php bp_group_type(); ?>
						</td>
						
						<td scope="row">
							<?php echo $group->date_created; ?>
						</td>
						
						<td scope="row">
							<?php bp_group_total_members(); ?>
						</td>
						
						<?php do_action( 'bp_gm_group_column_data' ); ?>
						
						
            		</tr>
            	<?php endwhile; ?>
            
            </tbody>
         	</table>
         	
         	<div class="tablenav">
    			<div class="tablenav-pages">

					<span class="displaying-num" id="group-dir-count">
						<?php bp_groups_pagination_count() ?>
					</span>

					<span class="page-numbers" id="group-dir-pag">
						<?php bp_group_management_pagination_links() ?>
					</span>

				</div>
			</div>

            	<?php else: ?>
            	You don't have any groups to manage.
            	
            	<?php endif; ?>
        
        <a class="button" id="bp-gm-settings-link" href="admin.php?page=bp-group-management/bp-group-management-bp-functions.php&action=settings"><?php _e( 'Plugin settings', 'bp-group-management' ); ?></a>
        </div>

<?php
	
}

function bp_group_management_admin_edit() {
?>
	<div class="wrap">
<?php

	$id = (int)$_GET['id'];
	$group = new BP_Groups_Group( $id, true );
	
	switch( $_GET['member_action'] ) {
		case "kick":
			if ( !check_admin_referer( 'bp-group-management-action_kick' ) )
				return false;

			if ( !bp_group_management_ban_member( $_GET['member_id'], $id ) ) { ?>
				<div id="message" class="updated fade"><p><?php _e('Sorry, there was an error.', 'bp-group-management'); ?></p></div>';
			<?php } else { ?>
				<div id="message" class="updated fade"><p><?php _e('Member kicked and banned', 'bp-group-management') ?></p></div>
			<?php }

			do_action( 'groups_banned_member', $_GET['member_id'], $id );
			
			break;
		
		case "unkick":
			if ( !check_admin_referer( 'bp-group-management-action_unkick' ) )
				return false;

			if ( !bp_group_management_unban_member( $_GET['member_id'], $id ) ) { ?>
				<div id="message" class="updated fade"><p><?php _e('Sorry, there was an error.', 'bp-group-management'); ?></p></div>
			<?php } else { ?>
				<div id="message" class="updated fade"><p><?php _e('Member unbanned', 'bp-group-management'); ?></p></div>
			<?php }

			do_action( 'groups_banned_member', $_GET['member_id'], $id );
			
			break;
		
		case "demote":
			if ( !check_admin_referer( 'bp-group-management-action_demote' ) )
				return false;

			if ( !groups_demote_member( $_GET['member_id'], $id ) ) { ?>
				<div id="message" class="updated fade"><p><?php _e('Sorry, there was an error.', 'bp-group-management'); ?></p></div>
			<?php } else { ?>
				<div id="message" class="updated fade"><p><?php _e('Member demoted', 'bp-group-management'); ?></p></div>
			<?php }

			do_action( 'groups_demoted_member', $_GET['member_id'], $id );
			
			break;
		
		case "mod":
			if ( !check_admin_referer( 'bp-group-management-action_mod' ) )
				return false;
			
			if ( !bp_group_management_promote_member( $_GET['member_id'], $id, 'mod' ) ) { ?>
				<div id="message" class="updated fade"><p><?php _e('Sorry, there was an error.', 'bp-group-management'); ?></p></div>
			<?php } else { ?>
				<div id="message" class="updated fade"><p><?php _e('Member promoted to moderator', 'bp-group-management'); ?></p></div>
			<?php }

			do_action( 'groups_promoted_member', $_GET['member_id'], $id );
			
			break;
		
		case "admin":
			if ( !check_admin_referer( 'bp-group-management-action_admin' ) )
				return false;
				
			if ( !bp_group_management_promote_member( $_GET['member_id'], $id, 'admin' ) ) { ?>
				<div id="message" class="updated fade"><p><?php _e('Sorry, there was an error.', 'bp-group-management'); ?></p></div>
			<?php } else { ?>
				<div id="message" class="updated fade"><p><?php _e('Member promoted to admin', 'bp-group-management'); ?></p></div>
			<?php }
			
			break;	
		
		case "add":
			if ( !check_admin_referer( 'bp-group-management-action_add' ) )
				return false;
			
			if ( !bp_group_management_join_group( $id, $_GET['member_id'] ) ) { ?>
				<div id="message" class="updated fade"><p><?php _e('Sorry, there was an error.', 'bp-group-management'); ?></p></div>
			<?php } else { ?>
				<div id="message" class="updated fade"><p><?php _e('User added to group', 'bp-group-management'); ?></p></div>
			<?php }
			
			break;
		
		default :
			do_action( 'bp_gm_member_action', $group, $id, $_GET['member_action'] );
			
			break;
	}
?>

	
	    <h2><?php _e( 'Group Management', 'bp-group-management' ) ?> : <?php echo bp_get_group_name( $group ); ?></h2>
	    
	    <a href="admin.php?page=bp-group-management/bp-group-management-bp-functions.php">&larr; <?php _e( 'Group index', 'bp-group-management' ) ?></a>
	    		
		<div class="bp-gm-group-actions">
	    <h3><?php _e( 'Group actions', 'bp-group-management' ); ?></h3>   
	    
	    <?php bp_group_management_group_action_buttons( $id, $group ) ?>

	    </div>

		
		<div class="bp-gm-group-members">
		
		
		<?php if ( bp_group_has_members( 'group_id=' . $id . '&exclude_admins_mods=0&exclude_banned=0' ) ) { ?>
	    <h3><?php _e( 'Manage current and banned group members', 'bp-group-management' ) ?></h3>
	    
			<?php if ( bp_group_member_needs_pagination() ) : ?>

				<div class="pagination no-ajax">

					<div id="member-count" class="pag-count">
						<?php bp_group_member_pagination_count() ?>
					</div>

					<div id="member-admin-pagination" class="pagination-links">
						<?php bp_group_member_admin_pagination() ?>
					</div>

				</div>

			<?php endif; ?>

			<ul id="members-list" class="item-list single-line">
				<?php while ( bp_group_members() ) : bp_group_the_member(); ?>

					<?php if ( bp_get_group_member_is_banned() ) : ?>

						<li class="banned-user">
							<?php bp_group_member_avatar_mini() ?>
							<?php
								$unkicklink = "admin.php?page=bp-group-management/bp-group-management-bp-functions.php&amp;action=edit&amp;id=" . $id . "&amp;member_id=" . bp_get_group_member_id() . "&amp;member_action=unkick";
								$unkicklink = ( function_exists('wp_nonce_url') ) ? wp_nonce_url($unkicklink, 'bp-group-management-action_unkick') : $unkicklink;
							?>
							<?php bp_group_member_link() ?> <?php _e( '(banned)', 'bp-group-management') ?> <span class="small"> - <a href="<?php echo $unkicklink; ?>" class="confirm" title="<?php _e( 'Remove Ban', 'bp-group-management' ) ?>"><?php _e( 'Remove Ban', 'bp-group-management' ); ?></a>

					<?php else : ?>

						<li>
							<?php bp_group_member_avatar_mini() ?> 
							
							<?php
								$kicklink = "admin.php?page=bp-group-management/bp-group-management-bp-functions.php&amp;action=edit&amp;id=" . $id . "&amp;member_id=" . bp_get_group_member_id() . "&amp;member_action=kick";
								$kicklink = ( function_exists('wp_nonce_url') ) ? wp_nonce_url($kicklink, 'bp-group-management-action_kick') : $kicklink;

								$modlink = "admin.php?page=bp-group-management/bp-group-management-bp-functions.php&amp;action=edit&amp;id=" . $id . "&amp;member_id=" . bp_get_group_member_id() . "&amp;member_action=mod";
								$modlink = ( function_exists('wp_nonce_url') ) ? wp_nonce_url($modlink, 'bp-group-management-action_mod') : $modlink;
								
								$demotelink = "admin.php?page=bp-group-management/bp-group-management-bp-functions.php&amp;action=edit&amp;id=" . $id . "&amp;member_id=" . bp_get_group_member_id() . "&amp;member_action=demote";
								$demotelink = ( function_exists('wp_nonce_url') ) ? wp_nonce_url($demotelink, 'bp-group-management-action_demote') : $demotelink;
								
								$adminlink = "admin.php?page=bp-group-management/bp-group-management-bp-functions.php&amp;action=edit&amp;id=" . $id . "&amp;member_id=" . bp_get_group_member_id() . "&amp;member_action=admin";
								$adminlink = ( function_exists('wp_nonce_url') ) ? wp_nonce_url($adminlink, 'bp-group-management-action_admin') : $adminlink;
								
							?>
							<strong><?php bp_group_member_link() ?></strong>
							<span class="small"> - 
								<a href="<?php echo $kicklink; ?>" class="confirm" title="<?php _e( 'Kick and ban this member', 'bp-group-management' ); ?>"><?php _e( 'Kick &amp; Ban', 'bp-group-management' ); ?></a> | 
								<?php if ( groups_is_user_admin( bp_get_group_member_id(), $id ) ) : ?>
									<a href="<?php echo $demotelink; ?>" class="confirm" title="<?php _e( 'Demote to Member', 'bp-group-management' ); ?>"><?php _e( 'Demote to Member', 'bp-group-management' ); ?></a>								
								<?php elseif ( groups_is_user_mod( bp_get_group_member_id(), $id ) ) : ?>
									<a href="<?php echo $demotelink; ?>" class="confirm" title="<?php _e( 'Demote to Member', 'bp-group-management' ); ?>"><?php _e( 'Demote to Member', 'bp-group-management' ); ?></a> | <a href="<?php echo $adminlink; ?>" class="confirm" title="<?php _e( 'Promote to Admin', 'bp-group-management' ); ?>"><?php _e( 'Promote to Admin', 'bp-group-management' ); ?></a></span>
								<?php else : ?>
									<a href="<?php echo $modlink; ?>" class="confirm" title="<?php _e( 'Promote to Moderator', 'bp-group-management' ); ?>"><?php _e( 'Promote to Moderator', 'bp-group-management' ); ?></a> | <a href="<?php echo $adminlink; ?>" class="confirm" title="<?php _e( 'Promote to Admin', 'bp-group-management' ); ?>"><?php _e( 'Promote to Admin', 'bp-group-management' ); ?></a></span>								
								<?php endif; ?>

					<?php endif; ?>

							<?php do_action( 'bp_group_manage_members_admin_item' ); ?>
						</li>

				<?php endwhile; ?>
			</ul>


	
		<?php } ?>

		</div>
		
		<?php bp_group_management_add_member_list( $id ); ?>
		
		<?php do_action( 'bp_gm_more_group_actions' ); ?>
		
	</div>
<?php
}


function bp_group_management_add_member_list( $id ) { 
	global $wpdb;
	
	$settings = get_option( 'bp_gm_settings' );
	if ( !$per_page = $settings['members_per_page'] )
		$per_page = 50;
	
	?>
	
	<div class="bp-gm-add-members">
		<h3><?php _e('Add members to group', 'bp-group-management') ?></h3>
		<ul>
		<?php
			if ( !$members ) {
				$query = "SELECT `ID` FROM {$wpdb->users}";
				$members = $wpdb->get_results( $query, ARRAY_A );
			}
			
			foreach ( $members as $key => $m ) {
				if( groups_is_user_member( $m['ID'], $id ) )
					unset($members[$key]);
				
				if( groups_is_user_banned( $m['ID'], $id ) )
					unset($members[$key]);
			}
			
			$members = array_values( $members );
			
						
			if ( $_GET['members_page'] )
				$start = ( $_GET['members_page'] - 1 ) * $per_page;
			else
				$start = 0;
				
			//print "<pre>";
			//print_r($members);
			
			$pag_links = paginate_links( array(
				'base' => add_query_arg( 'members_page', '%#%' ), 
				'format' => '',
				'total' => ceil(count($members) / $per_page),
				'current' => $_GET['members_page'],
				'show_all' => false,
				'prev_next' => true,
				'prev_text' => '&larr;',
				'next_text' => '&rarr;',
				'mid_size' => 4,
				'type' => 'list',
				));
			
			echo '<div class="tablenav"> <div class="tablenav-pages">';
			echo $pag_links;
			echo '</div></div>';
						
			for( $i = $start; $i < $start + $per_page; $i++ ) {
				
				if( !$members[$i] )
					exit;
			
				$addlink = "admin.php?page=bp-group-management/bp-group-management-bp-functions.php&amp;action=edit&amp;id=" . $id . "&amp;member_id=" . $members[$i]['ID'] . "&amp;member_action=add";
				
				if ( $_GET['members_page'] )
					$addlink .= "&amp;members_page=" . $_GET['members_page'];
				
				$addlink = ( function_exists('wp_nonce_url') ) ? wp_nonce_url($addlink, 'bp-group-management-action_add') : $addlink;
			?>
			<ul>
				<strong><a href="<?php echo $addlink; ?>"><?php _e( 'Add', 'bp-group-management' ) ?></a></strong> - <?php echo bp_core_get_userlink($members[$i]['ID']); ?>
			</ul>
			
			
						
			<?php }
		?>
		</ul>
		</div>		
		
		
			<?php do_action( 'bp_gm_more_group_actions' ); ?>
			
		<div style="clear: both;"> </div>	
			
		<a class="button" id="bp-gm-settings-link" href="admin.php?page=bp-group-management/bp-group-management-bp-functions.php&action=settings">Plugin settings</a>
	</div>
<?php 
}


function bp_group_management_admin_delete() {
	
	$id = (int)$_GET['id'];
	$group = new BP_Groups_Group( $id, true );
	$deletelink = "admin.php?page=bp-group-management/bp-group-management-bp-functions.php&amp;group_action=delete&amp;id=" . $id;
	$deletelink = ( function_exists('wp_nonce_url') ) ? wp_nonce_url($deletelink, 'bp-group-management-action_group_delete') : $deletelink;
	$backlink = "admin.php?page=bp-group-management/bp-group-management-bp-functions.php&amp;action=edit&amp;id=" . $id;

?>
	
	<div class="wrap">
	 	<h2><?php _e( 'Group Management', 'bp-group-management' ) ?> : <?php echo bp_get_group_name( $group ); ?></h2>
	 	  
	    <a href="admin.php?page=bp-group-management/bp-group-management-bp-functions.php">&larr; <?php _e( 'Group index', 'bp-group-management' ) ?></a>
	    		
		<div class="bp-gm-group-actions">
	    <h3><?php _e( 'Group actions', 'bp-group-management' ); ?></h3>   
	    
	    <?php bp_group_management_group_action_buttons( $id, $group ) ?>

	    </div>

	 	
	 	
	 	
	 	<h3><?php _e( 'Deleting the group', 'bp-group-management' ); ?> <?php echo '"' . bp_get_group_name( $group ) . '"'; ?></h3>
	 	<p><?php _e( 'You are about to delete the group', 'bp-group-management' ) ?> <em><?php echo bp_get_group_name( $group ); ?></em>. <strong><?php _e( 'This action cannot be undone.', 'bp-group-management' ) ?></strong></p>
	 	
	 	<p><a class="button-primary action" href="<?php echo $deletelink; ?>"><?php _e( 'Delete Group', 'bp-group-management' ) ?></a> 
	 	<a class="button-secondary action" href="<?php echo $backlink; ?>"><?php _e('Oops, I changed my mind', 'bp-group-management') ?></a></p>
	</div>
<?php
}




/* These functions handle the settings page */



function bp_group_management_settings() {
?>
	 <div class="wrap bp-gm-wrap">
            <h2><?php _e( 'Group Management Settings', 'bp-group-management' ) ?></h2>
            <a href="admin.php?page=bp-group-management/bp-group-management-bp-functions.php">&larr; <?php _e( 'Group index', 'bp-group-management' ) ?></a>
            <form action="options.php" method="post">
            <?php settings_fields( 'bp_gm_settings' ); ?>
            <?php do_settings_sections( 'bp_group_management' ); ?>
            
            <input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />

            
            </form>
     </div>
<?php
}


function bp_group_management_settings_setup() {
	register_setting( 'bp_gm_settings', 'bp_gm_settings', 'bp_group_management_settings_check' );
	
	add_settings_section('bp_gm_settings_main', __('Main Settings', 'bp-group-management'), 'bp_group_management_settings_main_content', 'bp_group_management');
	
	add_settings_field('bp_gm_settings_members_per_page', __('Number of members to display per page', 'bp-group-management'), 'bp_group_management_settings_members_per_page', 'bp_group_management', 'bp_gm_settings_main');
	
	add_settings_field('bp_gm_settings_groups_per_page', __('Number of groups to display per page', 'bp-group-management'), 'bp_group_management_settings_groups_per_page', 'bp_group_management', 'bp_gm_settings_main');
}
add_action('admin_init', 'bp_group_management_settings_setup');


function bp_group_management_settings_main_content() {
}

function bp_group_management_settings_members_per_page() {
	$options = get_option( 'bp_gm_settings' );
	echo "<input id='bp_gm_settings_members_per_page' name='bp_gm_settings[members_per_page]' size='40' type='text' value='{$options['members_per_page']}' />";
}

function bp_group_management_settings_groups_per_page() {
	$options = get_option( 'bp_gm_settings' );
	echo "<input id='bp_gm_settings_groups_per_page' name='bp_gm_settings[groups_per_page]' size='40' type='text' value='{$options['groups_per_page']}' />";
}

function bp_group_management_settings_check($input) {
	$newinput['members_per_page'] = trim($input['members_per_page']);
	$newinput['groups_per_page'] = trim($input['groups_per_page']);
	return $newinput;
}


function bp_group_management_admin_add() {
	$plugin_page = add_submenu_page( 'bp-general-settings', __('Group Management','bp-group-management'), __('Group Management','bp-group-management'), 'manage_options', __FILE__, 'bp_group_management_admin_screen' );
	add_action('admin_print_styles-' . $plugin_page, 'bp_group_management_css');
}
add_action( is_multisite() && function_exists( 'is_network_admin' ) ? 'network_admin_menu' : 'admin_menu', 'bp_group_management_admin_add', 70 );


function bp_group_management_css() {
	wp_enqueue_style( 'bp-group-management-css' );
}





?>
