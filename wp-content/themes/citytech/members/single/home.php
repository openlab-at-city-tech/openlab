<?php 
function cuny_member_profile_header() { 
global $site_members_template, $ribbonclass, $user_ID, $bp;

$this_user_id = isset( $site_members_template->member->id ) ? $site_members_template->member->id : bp_displayed_user_id();

$account_type = xprofile_get_field_data( 'Account Type', $this_user_id );

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
    
                <?php global $profile_template ?>
                <?php /* Don't show fields corresponding to other account types */ ?>
                <?php if ( $account_type == $profile_template->group->name && bp_profile_group_has_fields() ) : ?>
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
	
	<h1 class="entry-title"><?php bp_displayed_user_fullname() ?>'s Profile</h1>
	
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
		<?php locate_template( array( 'members/single/messages/single.php' ), true ) ?>
	<?php } elseif ( bp_is_user_messages() ) { ?>
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
		<h3 id="bread-crumb">Friends</h3>
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
		 $get_group_args = array(
			'user_id'       => bp_displayed_user_id(),
			'show_hidden'   => false,
			'active_status' => 'all',
			'group_type'	=> $type
		);
		$groups = openlab_get_groups_of_user( $get_group_args );
		
	
	  //echo $ids;
	  if ( !empty( $groups['group_ids_sql'] ) && bp_has_groups( 'include='.$groups['group_ids_sql'].'&per_page=20' ) ) : 
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
                          	<?php $activity = !empty( $groups['activity'][bp_get_group_id()] ) ? $groups['activity'][bp_get_group_id()] : bp_get_group_description() ?>
                          	<?php echo $activity.' <a class="read-more" href="'.bp_get_group_permalink().'">(View More)</a>'; ?>
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
		// BLOGS
		global $bp, $wpdb;
		
		// bp_has_blogs() doesn't let us narrow our options enough
		// Get all group blog ids, so we can exclude them
		$gblogs = $wpdb->get_col( $wpdb->prepare( "SELECT DISTINCT meta_value FROM {$bp->groups->table_name_groupmeta} WHERE meta_key = 'wds_bp_group_site_id'" ) );
		
		$gblogs = implode( ',', $gblogs );
		
		$blogs_query = $wpdb->prepare( "
			SELECT b.blog_id, b.user_id as admin_user_id, u.user_email as admin_user_email, wb.domain, wb.path, bm.meta_value as last_activity, bm2.meta_value as name 
			FROM {$bp->blogs->table_name} b, {$bp->blogs->table_name_blogmeta} bm, {$bp->blogs->table_name_blogmeta} bm2, {$wpdb->base_prefix}blogs wb, {$wpdb->users} u 
			WHERE b.blog_id = wb.blog_id AND b.user_id = u.ID AND b.blog_id = bm.blog_id AND b.blog_id = bm2.blog_id AND b.user_id = {$bp->displayed_user->id} AND wb.archived = '0' AND wb.spam = 0 AND wb.mature = 0 AND wb.deleted = 0 AND wb.public = 1 AND bm.meta_key = 'last_activity' AND bm2.meta_key = 'name' AND b.blog_id NOT IN ({$gblogs}) LIMIT 3" );
		
		$myblogs = $wpdb->get_results( $blogs_query );
		
		?>
		
		
		
		<ul id="<?php echo $type ?>-activity-stream" class="activity-list item-list<?php echo $last ?>">
		<div class="ribbon-case"><span class="ribbon-fold"></span><h4 class="<?php echo $ribbonclass ?>"><?php echo $title ?></h4></div>    
		
		<?php if ( !empty( $myblogs ) ) : ?>
			<?php foreach( (array)$myblogs as $myblog ) : ?>
				<li>
					<a href="http://<?php echo trailingslashit( $myblog->domain . $myblog->path ) ?>"><?php echo $myblog->name ?></a>
				</li>
			<?php endforeach ?>
		<?php else : ?>
			 <?php if( bp_is_my_profile() ) : ?>
				 You haven't created or joined any sites yet.
			 <?php else : ?> 
				<?php echo $bp->displayed_user->fullname ?> hasn't created or joined any sites yet.
			<?php endif ?>		
		
		<?php endif ?>
		<?php /* if ( bp_has_blogs('user_id='.$bp->displayed_user->id.'&per_page=3&max=3') ) { 
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
		  } */ ?>
		</ul>
    <?php
	}
}

add_action('genesis_before_sidebar_widget_area', 'cuny_buddypress_member_actions');
function cuny_buddypress_member_actions() { 
global $bp, $user_ID, $user_identity, $userdata;
get_currentuserinfo();
//print_r($userdata);

		//check to see if the user is viewing their own profile while logged in
        //if so - profile edit controls are displayed
        if ( is_user_logged_in() && bp_is_my_profile() ){ ?>
        <h2 class="sidebar-title">My OpenLab</h2>
        <div id="item-buttons">
		<?php do_action( 'cuny_bp_profile_menus' ); 
		      $activity_num = 2; ?>
		</div><!-- #item-buttons -->
		<?php }else{ ?>
		<h2 class="sidebar-title">People</h2>
		<?php $activity_num = 4; ?>
		<?php } ?>
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
		
		<?php if ( bp_has_activities( 'per_page='.$activity_num.$friends_true ) ) : ?>
	
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