<?php 
remove_action('genesis_loop', 'genesis_do_loop');
add_action('genesis_loop', 'cuny_group_single' );

function cuny_group_single() { ?>
	<?php if ( bp_has_groups() ) : while ( bp_groups() ) : bp_the_group(); 
		if( groups_get_groupmeta( bp_get_group_id(), 'wds_group_type' ) == 'course') {
			locate_template( array( 'groups/courses/single/home.php' ), true );
		} elseif( groups_get_groupmeta( bp_get_group_id(), 'wds_group_type' ) == 'project') {
			locate_template( array( 'groups/projects/single/home.php' ), true );
		} elseif( groups_get_groupmeta( bp_get_group_id(), 'wds_group_type' ) == 'club') {
			locate_template( array( 'groups/clubs/single/home.php' ), true );
		} else {
			?>
			
			<?php do_action( 'bp_before_group_home_content' ) ?>
			
			<div id="club-header">
				 <div id="club-header-avatar" class="alignleft">
					<a href="<?php bp_group_permalink() ?>" title="<?php bp_group_name() ?>">
						<?php bp_group_avatar('type=full&width=225&height=225') ?>
					</a>
					<?php if(1==2){?><p>Descriptive Tags associated with their profile, School, Etc, Tag, Tag, Tag, Tag, Tag, Tag, Tag</p><?php } ?>
				</div><!-- #club-header-avatar -->
			
				<div id="club-header-content" class="alignleft">
					<h2><a href="<?php bp_group_permalink() ?>" title="<?php bp_group_name() ?>"><?php bp_group_name() ?></a></h2>
					<span class="highlight"><?php bp_group_type() ?></span> <span class="activity"><?php printf( __( 'active %s', 'buddypress' ), bp_get_group_last_active() ) ?></span>
				
					<?php do_action( 'bp_before_group_header_meta' ) ?>
				
					<div id="item-meta">
						<?php bp_group_description() ?>
				
						<?php do_action( 'bp_group_header_meta' ) ?>
					</div>
				</div><!-- #item-header-content -->
			
				<?php do_action( 'bp_after_group_header' ) ?>
				
				<?php do_action( 'template_notices' ) ?>
				
			</div><!-- #item-header -->
			
			<div id="club-item-body">
				<?php do_action( 'bp_before_group_body' ) ?>
			
			
				<?php if ( !bp_group_is_visible() ) : ?>
					<?php /* The group is not visible, show the status message */ ?>
			
					<?php do_action( 'bp_before_group_status_message' ) ?>
			
					<div id="message" class="info">
						<p><?php bp_group_status_message() ?></p>
					</div>
			
					<?php do_action( 'bp_after_group_status_message' ) ?>
			
				<?php else : ?>
			
				<?php endif; ?>
			
				<?php if ( bp_group_is_visible() && bp_is_active( 'activity' ) ) : ?>
										
					<?php if ( bp_has_activities( 'per_page=3' ) ) : ?>
					
						<ul id="activity-stream" class="activity-list item-list">
							<div class="ribbon-case"><span class="ribbon-fold"></span><h4 class="robin-egg-ribbon">Recent Activity</h4></div>
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
							<div class="ribbon-case"><span class="ribbon-fold"></span><h4 class="robin-egg-ribbon">Recent Activity</h4></div>
							<div>
								<div id="message" class="info">
									<p><?php _e( 'Sorry, there was no activity found. Please try a different filter.', 'buddypress' ) ?></p>
								</div>
							</div>
						</ul>
					<?php endif; ?>
					
					<div class="info-group">
					
						<div class="ribbon-case"><span class="ribbon-fold"></span><h4 class="robin-egg-ribbon">Members</h4></div>
						<?php $member_arg = Array("exclude_admins_mods"=>false); ?>
						<?php if ( bp_group_has_members($member_arg) ) : ?>
						
						  <ul id="member-list">
							  <?php while ( bp_group_members() ) : bp_group_the_member(); ?>
								<li>
									<a href="<?php bp_group_member_domain() ?>">
										<?php bp_group_member_avatar_mini( 60, 60 ) ?>
									 </a>
								</li>
							  <?php endwhile; ?>
						  </ul>
						
						<?php else: ?>
						
						  <div id="message" class="info">
							<p>This group has no members.</p>
						  </div>
						
						<?php endif;?>
					</div>
					
				<?php elseif ( !bp_group_is_visible() ) : ?>
					<?php /* The group is not visible, show the status message */ ?>
			
					<?php do_action( 'bp_before_group_status_message' ) ?>
			
					<div id="message" class="info">
						<p><?php bp_group_status_message() ?></p>
					</div>
			
					<?php do_action( 'bp_after_group_status_message' ) ?>
			
				<?php else : ?>
					
					<?php locate_template( array( 'groups/single/wds-bp-action-logics.php' ), true ); ?>
				
				<?php endif; ?>
					
			
				<?php do_action( 'bp_after_group_body' ) ?>
			
			
				<?php do_action( 'bp_after_group_body' ) ?>
			</div><!-- #item-body -->
			
			<?php do_action( 'bp_after_group_home_content' ) ?>
			<?php 
		}
	endwhile; endif; ?>
<? }
		
add_action('genesis_before_sidebar_widget_area', 'cuny_buddypress_group_actions');
function cuny_buddypress_group_actions() { ?>
<?php if ( bp_has_groups() ) : while ( bp_groups() ) : bp_the_group(); ?>
		<div id="item-buttons">
			<h2 class="sidebar-header"><?php echo ucwords(groups_get_groupmeta( bp_get_group_id(), 'wds_group_type' )) ?></h2>
			<?php 
			do_action( 'bp_group_header_actions' ); ?>
			<ul>
				<?php cuny_get_options_nav();?>
			</ul>
			<?php do_action( 'bp_group_options_nav' ) ?>

		</div><!-- #item-buttons -->

<?php endwhile; endif; ?>
<?php }

add_filter( 'bp_get_options_nav_nav-invite-anyone', 'cuny_send_invite_fac_only');
function cuny_send_invite_fac_only( $subnav_item ) {
global $bp;
$account_type = xprofile_get_field_data( 'Account Type', $bp->loggedin_user->id);

	if ( $account_type != 'Student' )
		return $subnav_item;
}

genesis();