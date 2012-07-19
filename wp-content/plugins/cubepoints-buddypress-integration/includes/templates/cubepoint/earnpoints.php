<?php get_header('buddypress') ?>

	<div id="content">
		<div class="padder">

			<div id="item-header">
				<?php locate_template( array( 'members/single/member-header.php' ), true ) ?>
			</div>

			<div id="item-nav">
				<div class="item-list-tabs no-ajax" id="object-nav">
					<ul>
						<?php bp_get_displayed_user_nav() ?>
					</ul>
				</div>
			</div>

			<div id="item-body">

				<div class="item-list-tabs no-ajax" id="subnav">
					<ul>
						<?php bp_get_options_nav() ?>
					</ul>
				</div>
		
				<div id="cpbpleft">

				<p><strong><?php echo get_option('bp_earnpointstitle_cp_bp'); ?></strong></p>
				<?php 
				
					echo '<strong>'; 
					_e('Community','cp_buddypress');
					echo '</strong><br /><br />';
					
					if (get_option('bp_update_post_add_cp_bp') > 0) {
					echo get_option('bp_update_post_add_cp_bp');
					_e(' Points - Update','cp_buddypress');
					echo '<br />';
					}
					
					if (get_option('bp_update_comment_add_cp_bp') > 0) {
					echo get_option('bp_update_comment_add_cp_bp');
					_e(' Points - Leaving a reply','cp_buddypress');
					echo '<br />';
					}					
					
					if (get_option('bp_create_group_add_cp_bp') > 0) {
					echo get_option('bp_create_group_add_cp_bp');
					_e(' Points - Creating a group','cp_buddypress');
					echo '<br />';
					}
					
					if (get_option('bp_group_avatar_add_cp_bp') > 0) {
					echo get_option('bp_group_avatar_add_cp_bp');
					_e(' Points - Uploading a group avatar','cp_buddypress');
					echo '<br />';
					}					
					
					if (get_option('bp_join_group_add_cp_bp') > 0) {
					echo get_option('bp_join_group_add_cp_bp');
					_e(' Points - Joining a group','cp_buddypress');
					echo '<br />';
					}

					if (get_option('bp_leave_group_add_cp_bp') > 0) {
					echo get_option('bp_leave_group_add_cp_bp');
					_e(' Points - Leaving a group','cp_buddypress');
					echo '<br />';
					}

					if (get_option('bp_update_group_add_cp_bp') > 0) {
					echo get_option('bp_update_group_add_cp_bp');
					_e(' Points - Group Update or Reply','cp_buddypress');
					echo '<br />';
					}

					if (get_option('bp_friend_add_cp_bp') > 0) {
					echo get_option('bp_friend_add_cp_bp');
					_e(' Points - Completed Friend Request','cp_buddypress');
					echo '<br />';
					}

					if (get_option('bp_forum_new_topic_add_cp_bp') > 0) {
					echo get_option('bp_forum_new_topic_add_cp_bp');
					_e(' Points - New Group Forum Topic','cp_buddypress');
					echo '<br />';
					}

					if (get_option('bp_forum_new_post_add_cp_bp') > 0) {
					echo get_option('bp_forum_new_post_add_cp_bp');
					_e(' Points - New Group Forum Post','cp_buddypress');
					echo '<br />';
					}

					if (get_option('bp_avatar_add_cp_bp') > 0) {
					echo get_option('bp_avatar_add_cp_bp');
					_e(' Points - Avatar Uploaded','cp_buddypress');
					echo '<br />';
					}

					if (get_option('bp_pm_cp_bp') > 0) {
					echo get_option('bp_pm_cp_bp');
					_e(' Points - Message Sent','cp_buddypress');
					echo '<br />';
					}
					
					if (get_option('bp_bplink_add_cp_bp') > 0) {
					echo get_option('bp_bplink_add_cp_bp');
					_e(' Points - Link Created','cp_buddypress');
					echo '<br />';
					}
					
					if (get_option('bp_bplink_vote_add_cp_bp') > 0) {
					echo get_option('bp_bplink_vote_add_cp_bp');
					_e(' Points - Link Voted','cp_buddypress');
					echo '<br />';
					}
					
					if (get_option('bp_bplink_comment_add_cp_bp') > 0) {
					echo get_option('bp_bplink_comment_add_cp_bp');
					_e(' Points - Link Comment','cp_buddypress');
					echo '<br />';
					}
					
					if (get_option('bp_gift_given_cp_bp') > 0) {
					echo get_option('bp_gift_given_cp_bp');
					_e(' Points - Gift Given','cp_buddypress');
					echo '<br />';
					}

					if (get_option('bp_gallery_upload_cp_bp') > 0) {
					echo get_option('bp_gallery_upload_cp_bp');
					_e(' Points - Gallery Upload','cp_buddypress');
					echo '<br />';
					}				
					
					echo '<br /><strong>';
					_e('Blog Activity','cp_buddypress');
					echo '</strong><br /><br />';
				
					if (get_option('cp_comment_points') > 0) {
					echo get_option('cp_comment_points');
					_e(' Points - Blog Comment','cp_buddypress');
					echo '<br />';
					}

					if (get_option('cp_post_points') > 0) {
					echo get_option('cp_post_points');
					_e(' Points - Blog Post','cp_buddypress');
					echo '<br />';
					}
					
					echo '<br /><strong>'; 
					_e('Misc','cp_buddypress');
					echo '</strong><br /><br />';

					if (get_option('cp_reg_points') > 0) {
					echo get_option('cp_reg_points');
					_e(' Points - Becoming a Member','cp_buddypress');
					echo '<br />';
					}
					
					if (get_option('cp_daily_points') > 0) {
					echo get_option('cp_daily_points');
					_e(' Points - Daily Login','cp_buddypress');
					echo '<br />';
					}				
				?>				
				</div>
				
				<div id="cpbpright">
				
				<?php echo get_option('bp_earnpoints_extra_cp_bp'); ?>
	
				</div>
			</div><!-- #item-body -->
		</div><!-- .padder -->
	</div><!-- #content -->
	
<?php get_sidebar('buddypress') ?>

<?php get_footer('buddypress') ?>
