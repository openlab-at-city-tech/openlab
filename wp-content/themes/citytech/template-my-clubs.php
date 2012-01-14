<?php /* Template Name: My Clubs */

remove_action('genesis_loop', 'genesis_do_loop');
add_action('genesis_loop', 'cuny_my_clubs' );

function cuny_my_clubs() {
	echo cuny_profile_activty_block('club', 'My Clubs', ''); ?>
<?php }


function cuny_profile_activty_block($type,$title,$last) { 
	global $wpdb,$bp, $ribbonclass;
    //this is for filter by active/inactive status
    if ( !empty( $_GET['status'] ) ) {
    $sql="SELECT a.group_id,c.content FROM {$bp->groups->table_name_groupmeta} a, {$bp->groups->table_name_groupmeta} b, {$bp->activity->table_name} c where a.group_id=b.group_id and a.group_id=c.item_id and a.meta_key='wds_group_type' and a.meta_value='".$type."' and b.meta_key='openlab_group_active_status' and b.meta_value='".$_GET['status']."' and c.user_id=".$bp->loggedin_user->id." ORDER BY c.date_recorded desc";
    } else {
      $sql="SELECT a.group_id,b.content FROM {$bp->groups->table_name_groupmeta} a, {$bp->activity->table_name} b where a.group_id=b.item_id and a.meta_key='wds_group_type' and a.meta_value='".$type."' and b.user_id=".$bp->loggedin_user->id." ORDER BY b.date_recorded desc";
    }
	$ids="9999999";
	  $rs = $wpdb->get_results($sql);
	    
	  foreach ( (array)$rs as $r ){
		  $activity[]=$r->content;
		  $ids.= ",".$r->group_id;
	  }
	  
	  // So stupid. Gets rid of 9999999 group.
	$unique_group_count = count( array_unique( explode( ',', $ids ) ) ) - 1;
	
	// Hack to fix pagination
	add_filter( 'bp_groups_get_total_groups_sql', create_function( '', 'return "SELECT ' . $unique_group_count . ' AS value;";' ) );
	  
	  echo  '<h1 class="entry-title">'.$bp->loggedin_user->fullname.'&rsquo;s Profile</h1>';
      if ( !empty( $_GET['status'] ) ) {
	    $status = $_GET['status'];
	    $status = ucwords($status);
	    echo '<h3 id="bread-crumb">Clubs<span class="sep"> | </span>'.$status.'</h3>';
	  }else {
	    echo '<h3 id="bread-crumb">Clubs</h3>';
	  }
	  

	  if ( bp_has_groups( 'include='.$ids.'&per_page=3&max=3&show_hidden=true' ) ) : ?>
	  <div class="group-count"><?php cuny_groups_pagination_count("Clubs"); ?></div>
	  <div class="clearfloat"></div>
<ul id="club-list" class="item-list">
		<?php 
		$count = 1;
		while ( bp_groups() ) : bp_the_group(); 
			$group_id=bp_get_group_id();?>
			<li class="club<?php echo cuny_o_e_class($count) ?>">
				<div class="item-avatar alignleft">
					<a href="<?php bp_group_permalink() ?>"><?php echo bp_get_group_avatar(array( 'type' => 'full', 'width' => 100, 'height' => 100 )) ?></a>
				</div>
				<div class="item">
					<h2 class="item-title"><a href="<?php bp_group_permalink() ?>" title="<?php bp_group_name() ?>"><?php bp_group_name() ?></a></h2>
					<?php 
					$wds_faculty=groups_get_groupmeta($group_id, 'wds_faculty' );
					$wds_club_code=groups_get_groupmeta($group_id, 'wds_club_code' );
					$wds_semester=groups_get_groupmeta($group_id, 'wds_semester' );
		  			$wds_year=groups_get_groupmeta($group_id, 'wds_year' );
		  			$wds_departments=groups_get_groupmeta($group_id, 'wds_departments' );
					?>
                    <div class="info-line"><?php echo $wds_faculty; ?> | <?php echo $wds_departments;?> | <?php echo $wds_club_code;?><br /> <?php echo $wds_semester;?> <?php echo $wds_year;?></div>
					<?php
					     $len = strlen(bp_get_group_description());
					     if ($len > 135) {
						$this_description = substr(bp_get_group_description(),0,135);
						$this_description = str_replace("</p>","",$this_description);
						echo $this_description.'&hellip; (<a href="'.bp_get_group_permalink().'">View More</a>)</p>';
					     } else {
						bp_group_description();
					     }
					?>
				</div>
				
			</li>
			<?php if ( $count % 2 == 0 ) { echo '<hr style="clear:both;" />'; } ?>
			<?php $count++ ?>
		<?php endwhile; ?>
	</ul>

<?php else: ?>

	<div class="widget-error">
		<?php _e('There are no groups to display.', 'buddypress') ?>
	</div>

<?php endif; ?>

		<div class="pagination-links" id="group-dir-pag-top">
			<?php bp_groups_pagination_links() ?>
		</div><?php

}

add_action('genesis_before_sidebar_widget_area', 'cuny_buddypress_member_actions');
function cuny_buddypress_member_actions() { 
global $bp, $user_ID, $user_identity, $userdata;
get_currentuserinfo();
//print_r($userdata);

?>
	<h2 class="sidebar-title">My Open Lab</h2>
	<div id="item-buttons">
		<?php do_action( 'cuny_bp_profile_menus' ); ?>
	
	</div><!-- #item-buttons -->
	
	<?php
		global $members_template;
		
		// Not really sure where this function appears, so I'll make a cascade
		if ( isset( $members_template->member->user_id ) ) {
			$button_user_id = $members_template->member->user_id;
		} else if ( bp_displayed_user_id() ) {
			$button_user_id = bp_displayed_user_id();
		}
			       
		$is_friend = friends_check_friendship( $button_user_id, bp_loggedin_user_id() );
	?>
		
	<?php bp_add_friend_button( $button_user_id, bp_loggedin_user_id() ) ?>

		
<?php if ( !bp_is_user_messages() ) { ?>
	<?php if ( bp_is_user_friends() ) { ?>
		<?php $friends_true = "&scope=friends"; ?>
		<h4 class="sidebar-header">Recent Friend Activity</h4>
	<?php } else { ?>
		<?php $friends_true = NULL; ?>
		<h4 class="sidebar-header">Recent Account Activity</h4>
	<?php } ?>
		
		<?php if ( bp_has_activities( 'per_page=3'.$friends_true ) ) : ?>
	
			<ul id="activity-stream" class="activity-list item-list">
				<div>
				<?php while ( bp_activities() ) : bp_the_activity(); ?>
			
					<div class="activity-avatar">
						<a href="<?php bp_activity_user_link() ?>">
							<?php bp_activity_avatar( 'type=full&width=100&height=100' ) ?>
						</a>
					</div>
				
					<div class="activity-content">
					
						<div class="activity-header">
							<?php bp_activity_action() ?>
						</div>
				
						<?php if ( bp_activity_has_content() ) : ?>
							<div class="activity-inner">
								<?php bp_activity_content_body() ?>
							</div>
						<?php endif; ?>
				
						<?php do_action( 'bp_activity_entry_content' ) ?>
						
					</div>
					<hr style="clear:both" />
	
				<?php endwhile; ?>
				</div>
			</ul>
	
		<?php else : ?>
		<ul id="activity-stream" class="activity-list item-list">
			<div>
			<div id="message" class="info">
				<p><?php _e( 'No recent activity.', 'buddypress' ) ?></p>
			</div>
			</div>
		</ul>
<?php endif; ?>
<?php }
}

genesis();
