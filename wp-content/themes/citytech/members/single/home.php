<?php 
function cuny_member_profile_header() { 
global $site_members_template, $ribbonclass, $user_ID, $bp;
$account_type = xprofile_get_field_data( 'Account Type', $site_members_template->member->id);

//
//     whenever profile is viewed, update user meta for first name and last name so this shows up
//     in the back end on users display so teachers see the students full name
//
$name_member_id = bp_displayed_user_id();
$first_name= xprofile_get_field_data( 'First Name', $name_member_id);
$last_name= xprofile_get_field_data( 'Last Name', $name_member_id);
$update_user_first = update_user_meta($name_member_id,'first_name',$first_name);
$update_user_last = update_user_meta($name_member_id,'last_name',$last_name);
if ( $account_type == 'Faculty' )
	$ribbonclass = 'watermelon-ribbon';
if ( $account_type == 'Student' )
	$ribbonclass = 'robin-egg-ribbon';
if ( $account_type == 'Staff' )
	$ribbonclass = 'yellow-canary-ribbon';
?>
 <div class="ribbon-case"><span class="ribbon-fold"></span><h4 class="<?php echo $ribbonclass ?>"><?php echo $account_type ?> Profile</h4></div>
<div id="member-header">
<?php do_action( 'bp_before_member_header' ) ?>
	
	<div id="member-header-avatar" class="alignleft">
		<a href="<?php bp_user_link() ?>">
			<?php bp_displayed_user_avatar( 'type=full&width=225' ) ?>
		</a>
		<!--<p>Some descriptive tags about the student...</p>-->
	</div><!-- #item-header-avatar -->
	
	<div id="member-header-content" class="alignleft">

		<h2 class="member-name-title fn"><?php bp_displayed_user_fullname() ?><?php //echo " ".$last_name;?></h2>
		<span class="activity"><?php bp_last_activity( bp_displayed_user_id() ) ?></span>
		
        
        
		<?php do_action( 'bp_before_member_header_meta' ) ?>
	
		<div id="item-meta">
			<?php if ( function_exists( 'bp_activity_latest_update' ) ) : ?>
				<div id="latest-update">
					<?php bp_activity_latest_update( bp_displayed_user_id() ) ?>
				</div>
			<?php endif; ?>
<!--
			<?php if ( bp_has_groups() ) : ?>		
				<div id="areas-of-activty" class="item-list">
				<strong>All groups:</strong> 
				<?php while ( bp_groups() ) : bp_the_group(); ?>
				
					<a href="<?php bp_group_permalink() ?>"><?php bp_group_name() ?></a>, 
			
				<?php endwhile; ?>
				</div>		
			<?php endif; ?>
-->			

	
			<?php do_action( 'bp_profile_header_meta' ) ?>
	
		</div><!-- #item-meta -->
        
        <div class="profile-fields">
        	<?php if ( bp_has_profile() ) : /* global $profile_template; echo '<pre>'; print_r( $profile_template ); echo '</pre>'; */ ?>
			<?php while ( bp_profile_groups() ) : bp_the_profile_group(); ?>
    
                <?php if ( bp_profile_group_has_fields() ) : ?>
    					<table class="profile-fields">
                            <?php while ( bp_profile_fields() ) : bp_the_profile_field(); ?>

                                <?php if ( bp_field_has_data() ) : 
					if( bp_get_the_profile_field_name() != "Name"
				                &&
						bp_get_the_profile_field_name() != "Account Type"
						&&
						bp_get_the_profile_field_name() != "First Name"
						&&
						bp_get_the_profile_field_name()!="Last Name" ) : ?>

						<tr>
							<td class="label" nowrap="nowrap">
								<?php bp_the_profile_field_name() ?>
							</td>
	    
							<td>
							    <?php bp_the_profile_field_value() ?>
							</td>
    						</tr>
					<?php endif;
				endif; ?>
    
                             <?php endwhile; ?>
                        </table>
                 <?php endif; ?>
    
            <?php endwhile; ?>
    		<?php endif; ?>
        </div>
        
	</div><!-- #item-header-content -->
	
	<?php do_action( 'bp_after_member_header' ) ?>

</div><!-- #item-header -->
<?php }

remove_action('genesis_loop', 'genesis_do_loop');
add_action('genesis_loop', 'cuny_student_profile' );

function cuny_student_profile() {
global $bp, $ribbonclass;
?>
<?php do_action( 'bp_before_member_home_content' ) ?>
	<?php if ( bp_is_user_activity() || 'public' == bp_current_action() ) { ?>
		<?php cuny_member_profile_header(); ?>
	<?php } ?>


<div id="member-item-body">
	<?php if ( bp_is_user_blogs() ) { ?>
		<?php do_action( 'bp_before_member_blogs_content' ) ?>
		
		<div class="blogs myblogs">
			<?php locate_template( array( 'blogs/blogs-loop.php' ), true ) ?>
		</div><!-- .blogs.myblogs -->
		
		<?php do_action( 'bp_after_member_blogs_content' ) ?>
	
		<?php do_action( 'bp_before_member_body' ) ?>

	<?php } elseif ( 'view' == bp_current_action() ) { ?>
		<h1 class="entry-title">My Messages</h1>
		<?php locate_template( array( 'members/single/messages/single.php' ), true ) ?>
	<?php } elseif ( bp_is_user_messages() ) { ?>
		<h1 class="entry-title">My Messages</h1>
		<?php locate_template( array( 'members/single/messages.php' ), true ) ?>
	<?php } elseif ( bp_is_user_groups() ) { ?>
		<?php locate_template( array( 'members/single/groups.php' ), true ) ?>
	<?php } elseif ( 'edit' == bp_current_action() ) { ?>
		 <?php locate_template( array( 'members/single/profile/edit.php' ), true ); ?>
	<?php } elseif ( 'change-avatar' == bp_current_action() ) { ?>
		<?php locate_template( array( 'members/single/profile/change-avatar.php' ), true ) ?>
	<?php } elseif ( 'requests' == bp_current_action() ) { ?>
		<?php locate_template( array( 'members/single/friends/requests.php' ), true ) ?>
	<?php } elseif ( bp_is_user_friends() ) { ?>

		<?php do_action( 'bp_before_member_friends_content' ) ?>
	
		<div class="members friends">
			<?php locate_template( array( 'members/members-loop.php' ), true ) ?>
		</div><!-- .members.friends -->
	
		<?php do_action( 'bp_after_member_friends_content' ) ?>

	<?php } else { ?>
		
		
		<?php echo cuny_profile_activty_block('course', 'My Courses', ''); ?>
		<?php echo cuny_profile_activty_block('project', 'My Projects', ' last'); ?>
		<?php echo cuny_profile_activty_block('blog', 'My Sites', ''); ?>
		<?php echo cuny_profile_activty_block('club', 'My Clubs', ' last'); ?>
<?php		   
        if ( !$friend_ids = wp_cache_get( 'friends_friend_ids_' . $bp->displayed_user->id, 'bp' ) ) {
            $friend_ids = BP_Friends_Friendship::get_random_friends( $bp->displayed_user->id );
            wp_cache_set( 'friends_friend_ids_' . $bp->displayed_user->id, $friend_ids, 'bp' );
	      } ?>
    
        <div class="info-group">
            <div class="ribbon-case"><span class="ribbon-fold"></span><h4 class="<?php echo $ribbonclass ?>"><?php bp_word_or_name( __( "My Friends", 'buddypress' ), __( "%s's Friends", 'buddypress' ) ) ?></h4></div>
    
            <?php if ( $friend_ids ) { ?>
    
                <ul id="member-list">
    
              <?php for ( $i = 0; $i < count( $friend_ids ); $i++ ) { ?>
    
                    <li>
                      <a href="<?php echo bp_core_get_user_domain( $friend_ids[$i] ) ?>"><?php echo bp_core_fetch_avatar( array( 'item_id' => $friend_ids[$i], 'type' => 'thumb' ) ) ?></a>
                      <h5><?php // echo bp_core_get_userlink($friend_ids[$i]) ?></h5>
                  	</li>
  
              <?php } ?>
  
              </ul>
  				<span><a href="<?php echo $bp->displayed_user->domain . $bp->friends->slug ?>"><?php _e('View More Friends', 'buddypress') ?> &rarr;</a></span>
          <?php } else { ?>
  
              <div id="message" class="info">
                  <p><?php bp_word_or_name( __( "You haven't added any friend connections yet.", 'buddypress' ), __( "%s hasn't created any friend connections yet.", 'buddypress' ) ) ?></p>
              </div>
  
          <?php } ?>
          <div class="clear"></div>
      </div>
		
	<?php } ?>
	
	<?php do_action( 'bp_after_member_body' ) ?>
	
</div><!-- #item-body -->

<?php do_action( 'bp_after_memeber_home_content' ) ?>

<?php }


function cuny_profile_activty_block($type,$title,$last) { 
	global $wpdb,$bp, $ribbonclass;
	//echo $type."<hr>";
	$ids="9999999";
	$groups_found = Array();
	if($type!="blog"){
	  //$sql="SELECT group_id FROM {$bp->groups->table_name_groupmeta} where meta_key='wds_group_type' and meta_value='".$type."' ORDER BY RAND() LIMIT 3";
	  $sql="SELECT a.group_id,b.content FROM {$bp->groups->table_name_groupmeta} a, {$bp->activity->table_name} b where a.group_id=b.item_id and a.meta_key='wds_group_type' and a.meta_value='".$type."' and b.user_id=".$bp->displayed_user->id." ORDER BY b.date_recorded desc";
//	  $sql="SELECT a.group_id,b.content FROM {$bp->groups->table_name_groupmeta} a, {$bp->activity->table_name} b where a.group_id=b.item_id and a.meta_key='wds_group_type' and a.meta_value='".$type."' and b.user_id=".$bp->displayed_user->id." ORDER BY b.date_recorded desc LIMIT 3";
	  $rs = $wpdb->get_results($sql);
	  foreach ( (array)$rs as $r ){
		  $activity[]=$r->content;
		  if (!in_array($r->group_id,$groups_found)) {
			$ids.= ",".$r->group_id;
			$groups_found[] = $r->group_id;
		  }
	  }
//    now check to see if they are a "member" of the group that isn't in the activity (like they created it)
//    if so, and it isn't already in the list of groups found, then add it for use in the bp_has_groups loop
//
 	  $sql_mbr="SELECT a.group_id,b.group_id FROM {$bp->groups->table_name_groupmeta} a, {$bp->groups->table_name_members} b
	    	    WHERE a.group_id=b.group_id and a.meta_key='wds_group_type' and a.meta_value='".$type."' and b.user_id=".$bp->displayed_user->id.
		    " AND is_confirmed='1' AND is_banned='0' ORDER BY b.id desc";
/*
 	  $sql_mbr="SELECT a.group_id,b.group_id FROM {$bp->groups->table_name_groupmeta} a, {$bp->groups->table_name_members} b
	    	    WHERE a.group_id=b.group_id and a.meta_key='wds_group_type' and a.meta_value='".$type."' and b.user_id=".$bp->displayed_user->id.
		    " AND is_confirmed='1' AND is_banned='0' ORDER BY b.id desc LIMIT 3";
*/
	  $rs_mbr = $wpdb->get_results($sql_mbr);
	  foreach ( (array)$rs_mbr as $r_mbr ){
	      if (!in_array($r_mbr->group_id,$groups_found)) {
		   $activity[]="";
		   $ids.= ",".$r_mbr->group_id;
		   $groups_found[] = $r_mbr->group_id;
	      }
	   }
/*
	if ($_GET['test'] == "hvl") {
		echo "<br />Displayed ID = " . $bp->displayed_user->id;
		echo "<br />Groups = " . $ids;
	}
*/
	  //echo $ids;
	  if ( bp_has_groups( 'include='.$ids.'&per_page=20' ) ) : 
//	  if ( bp_has_groups( 'include='.$ids.'&per_page=3&max=3' ) ) : 
		 ?>
		 <ul id="<?php echo $type ?>-activity-stream" class="activity-list item-list<?php echo $last ?>">
          <div class="ribbon-case"><span class="ribbon-fold"></span><h4 class="<?php echo $ribbonclass ?>"><?php echo $title ?></h4></div>
		 <?php $x=0; 
		 while ( bp_groups() ) : bp_the_group();
		 ?>
              
              <div>
                  <div class="activity-avatar">
      				<?php echo bp_get_group_avatar();?>
                  </div>
              
                  <div class="activity-content">
                  
                      <div class="activity-header">
                          <?php echo bp_get_group_name();?>
                      </div>
              
                          <div class="activity-inner">
                          	<?php echo $activity[$x].' <a class="read-more" href="'.bp_get_group_permalink().'">(View More)</a>'; ?>
                          </div>
                      
                  </div>
                  <hr style="clear:both" />
      
              </div>
             
              <?php $x+=1;
//
//    Only show 3 items max
//
			if ($x == 3) {
				break;
			}
		  endwhile; ?>
          </ul>
	  <?php else : ?>
	   <ul id="<?php echo $type ?>-activity-stream" class="activity-list item-list<?php echo $last ?>">
          <div class="ribbon-case"><span class="ribbon-fold"></span><h4 class="<?php echo $ribbonclass ?>"><?php echo $title ?></h4></div>   
              <div>
                <?php if($type!="course"){
				  if($bp->loggedin_user->id==$bp->displayed_user->id){?>
					  You aren't participating in any <?php echo $type; ?>s on the OpenLab yet. Why not <a href="<?php echo site_url();?>/groups/create/step/group-details/?type=<?php echo $type; ?>&new=true">create a <?php echo $type; ?></a>?
				   <?php }else{ 
					  echo $bp->displayed_user->fullname;?>
					  hasn't created or joined any <?php echo $type ?>s yet.
				   <?php }
				}else{
					if($bp->loggedin_user->id==$bp->displayed_user->id){?>
                    	You haven't created any courses yet.
					<?php }else{ 
					  echo $bp->displayed_user->fullname;?>
					  hasn't joined any <?php echo $type ?>s yet.
				   <?php } 
				}?>
              </div>
        </ul>
		<?php
		endif;
	} else {
	?>
		<ul id="<?php echo $type ?>-activity-stream" class="activity-list item-list<?php echo $last ?>">
          <div class="ribbon-case"><span class="ribbon-fold"></span><h4 class="<?php echo $ribbonclass ?>"><?php echo $title ?></h4></div>    
          <?php if ( bp_has_blogs('user_id='.$bp->displayed_user->id.'&per_page=3&max=3') ) { 
			while ( bp_blogs() ) : bp_the_blog();
			  echo '<li>';?>
				  <a href="<?php bp_blog_permalink() ?>"><?php bp_blog_name() ?></a>
			  <?php echo '</li>';
			endwhile; 
		  }else{
			 if($bp->loggedin_user->id==$bp->displayed_user->id){?>
					 You haven't created or joined any sites yet.
			 <?php }else{ 
                echo $bp->displayed_user->fullname;?>
                hasn't created or joined any sites yet.
             <?php } 
		  }?>
        </ul>
    <?php
	}
}

add_action('genesis_before_sidebar_widget_area', 'cuny_buddypress_member_actions');
function cuny_buddypress_member_actions() { 
global $bp, $user_ID, $user_identity, $userdata;
get_currentuserinfo();
//print_r($userdata);

?>

	<div id="item-buttons">

			<?php // do_action( 'bp_member_header_actions' ); ?>
		<?php if ( bp_is_user_messages() ) { ?>
			<h4 class="sidebar-header">Check My Messages</h4>
			<ul>
				<li id="inbox-personal-li"><a id="inbox" href="<?php echo bp_loggedin_user_domain() ?>messages/inbox/">Inbox</a><ul>
					<li id="inbox-personal-li"><a id="inbox" href="<?php echo bp_loggedin_user_domain() ?>messages/inbox/?status=unread">Unread</a></li>
					<li id="inbox-personal-li"><a id="inbox" href="<?php echo bp_loggedin_user_domain() ?>messages/inbox/?status=read">Read</a></li>
				</ul></li>
				<li id="sentbox-personal-li"><a id="sentbox" href="<?php echo bp_loggedin_user_domain() ?>messages/sentbox/">Sent Messages</a></li>
				<li id="compose-personal-li"><a id="compose" href="<?php echo bp_loggedin_user_domain() ?>messages/compose/">Compose</a></li>
			</ul>
			<?php } else { ?>
		
			<hr />
			<ul>
				<?php if ( is_super_admin( get_current_user_id() ) ) { ?>
						<li><a href="<?php echo $bp->displayed_user->domain ?>profile/edit">Edit Profile Info</a></li>
				<?php } ?>
				<?php if( $bp->displayed_user->id == $user_ID) : ?>			
					<li><a href="<?php echo bp_loggedin_user_domain() ?>profile/edit">Edit Profile Info</a></li>
					<li><a href="<?php echo bp_loggedin_user_domain() ?>profile/change-avatar">Change Avatar</a></li>						
					<li><a href="<?php echo bp_loggedin_user_domain() ?>settings/general">Settings</a></li>				
				<?php endif; ?>
			</ul>
			<hr />
		<?php } ?>
	
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
		
		<?php if ( bp_has_activities( 'per_page=4'.$friends_true ) ) : ?>
	
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

genesis(); ?>