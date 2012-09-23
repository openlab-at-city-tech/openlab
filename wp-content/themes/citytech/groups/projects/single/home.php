<?php do_action( 'bp_before_group_home_content' ) ?>
<?php
//
//     control the formatting of left and right side by use of variable $first_class.
//     when it is "first" it places it on left side, when it is "" it places it on right side
//
//     Initialize it to left side to start with
//
       $first_class = "first";
?>
<?php $group_slug = bp_get_group_slug(); 
$group_type = openlab_get_group_type( bp_get_current_group_id()); ?>
<h1 class="entry-title group-title"><?php echo bp_group_name(); ?> Profile</h1>
<?php if ( bp_is_group_home() ): ?>
<?php global $bp;
	  $group_id = $bp->groups->current_group->id; ?>
<div id="club-header">
	 <div id="club-header-avatar" class="alignleft">
		<a href="<?php bp_group_permalink() ?>" title="<?php bp_group_name() ?>">
			<?php bp_group_avatar('type=full&width=225') ?>
		</a>
         <?php if (is_user_logged_in() && $bp->is_item_admin): ?>
         <div id="group-action-wrapper">
					<div id="action-edit-group"><a href="<?php echo bp_group_permalink(). 'admin/edit-details/'; ?>">Edit Profile</a></div>
            		<div id="action-edit-avatar"><a href="<?php echo bp_group_permalink(). 'admin/group-avatar/'; ?>">Change Avatar</a></div>
         </div>
		<?php elseif (is_user_logged_in()): ?>
		<div id="group-action-wrapper">
				<?php do_action( 'bp_group_header_actions' ); ?>
        </div>
	 	<?php endif; ?>
		<?php /* <p>Descriptive Tags associated with their profile, School, Etc, Tag, Tag, Tag, Tag, Tag, Tag, Tag</p> */ ?>
	</div><!-- #club-header-avatar -->

	<div id="club-header-content" class="alignleft">
		<h2><a href="<?php bp_group_permalink() ?>" title="<?php bp_group_name() ?>"><?php bp_group_name() ?></a> <a href="<?php bp_group_permalink() ?>feed" class="rss"><img src="<?php bloginfo('stylesheet_directory') ?>/images/icon-RSS.png" alt="Subscribe To <?php bp_group_name() ?>'s Feeds"></a></h2>
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

<?php endif; ?>

<div id="club-item-body">

	<?php do_action( 'bp_before_group_body' ) ?>

	<?php if ( bp_is_group_home() ) { ?>

		<?php if ( !bp_group_is_visible() ) : ?>
			<?php /* The group is not visible, show the status message */ ?>

			<?php do_action( 'bp_before_group_status_message' ) ?>

			<div id="message" class="info">
				<p><?php bp_group_status_message() ?></p>
			</div>

			<?php do_action( 'bp_after_group_status_message' ) ?>

		<?php else : ?>

		<?php endif; ?>


		<?php if ( bp_group_is_visible() || !bp_is_active( 'activity' ) ) { ?>
			        <?php global $first_displayed; ?>
				<?php $first_displayed = false; ?>


		<?php if ( bp_group_is_visible() && bp_is_active( 'activity' )  ) : ?>
		    <?php if (wds_site_can_be_viewed()) { ?>
			<?php show_site_posts_and_comments() ?>

				<?php /* temporarily get rid of "Recent Activity" */ ?>
				<?php if ( "1" == "2" ) : ?>
				<?php //if ( bp_has_activities( 'per_page=3' ) ) : ?>
				<div>
					<ul id="activity-stream" class="activity-list item-list">
						<h4 class="group-activity-title">Recent Activity</h4>
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
										<?php
											$act_content = bp_get_activity_content_body();
											if (strlen($act_content) > 135) {
												$act_content = wds_content_excerpt($act_content,135) . " [...]";
											}
											echo $act_content;
										?>
									</div>
								<?php endif; ?>

								<?php do_action( 'bp_activity_entry_content' ) ?>

							</div>
							<hr style="clear:both" />

						<?php endwhile; ?>
						</div>
					</ul>
				</div>

				<?php else : ?>
<!--
				<div>
					<ul id="activity-stream" class="activity-list item-list">
						<div class="ribbon-case"><span class="ribbon-fold"></span><h4 class="robin-egg-ribbon">Recent Activity</h4></div>
						<div>
							<div id="message" class="info">
								<p><?php // _e( 'Sorry, there was no activity found. Please try a different filter.', 'buddypress' ) ?></p>
							</div>
						</div>
					</ul>
				</div>
-->
				<?php endif; ?>
<?php                  } ?>
	<?php
/*
		if ($first_displayed) {
			$first_class = "";
		} else {
			$first_class = "first";
		}
*/
	?>
    <div class="one-half <?php echo $first_class; ?>">
	<div id="recent-forum">
		<div class="recent-posts">
			<h4 class="group-activity-title">Recent Discussions<span class="view-more"><a class="read-more" href="<?php site_url();?>/groups/<?php echo $group_slug; ?>/forum/">See All</a></span></h4>
            <?php if ( bp_has_forum_topics('per_page=3') ) : ?>
            	<ul>
                	<?php while ( bp_forum_topics() ) : bp_the_forum_topic(); ?>
                    	<li>
						<h5><?php bp_the_topic_title() ?></h5>
			<p><?php
				$topic_id = bp_get_the_topic_id();
				$last_topic_post = $wpdb->get_results("SELECT post_id,topic_id,post_text FROM wp_bb_posts
								        WHERE topic_id='$topic_id'
								       ORDER BY post_id DESC LIMIT 1","ARRAY_A");
				$last_topic_content = wds_content_excerpt(strip_tags($last_topic_post[0]['post_text']),135);
				echo $last_topic_content;
			?>
                        <a href="<?php bp_the_topic_permalink();?>" class="read-more">See&nbsp;More</a></p>
                        </li>
                    <?php endwhile; ?>
                </ul>
                
            <?php else: ?>
            	<div id="message" class="info">
					<p><?php _e( 'Sorry, there were no discussion topics found.', 'buddypress' ) ?></p>
				</div>
            <?php endif;?>

		</div><!-- .recent-post -->
	</div>
   </div>
   <?php $first_class = ""; ?>
   <div class="one-half <?php echo $first_class; ?>">
		<div id="recent-docs">
		   <div class="recent-posts">
				<h4 class="group-activity-title">Recent Docs<span class="view-more"><a class="read-more" href="<?php site_url();?>/groups/<?php echo $group_slug; ?>/docs/">See All</a></span></h4>
<?php
//*********************************************************************
				$docs_arg = Array("posts_per_page"=>"3",
						  "post_type"=>"bp_doc",
						  "tax_query"=>
						  Array(Array("taxonomy"=>"bp_docs_associated_item",
							      "field"=>"slug",
							      "terms"=>$group_slug)));
				$query = new WP_Query( $docs_arg );
//				$query = new WP_Query( "posts_per_page=3&post_type=bp_doc&category_name=$group_slug" );
//				$query = new WP_Query( "posts_per_page=3&post_type=bp_doc&category_name=$group_id" );
				if($query->have_posts()){
				  echo '<ul>';
				  while ( $query->have_posts() ) : $query->the_post();
					  echo '<li>';
					  echo '<h5>';
					  the_title();
					  echo '</h5>';
					  ?>
					  <p><?php echo wds_content_excerpt(strip_tags($post->post_content), 135);?> <a href="<?php site_url();?>/groups/<?php echo $group_slug; ?>/docs/<?php echo $post->post_name; ?>" class="read-more">See&nbsp;More</a></p>
					  <?php
					  echo '</li>';
				  endwhile;
				  echo '</ul>';
				  ?>
				
				<?php
				}else{
					echo "<div><p>No Recent Docs</p></div>";
				}?>
                <?php
//*********************************************************************
			?>
		</div>
	</div>

	</div>
				<div id="members-list" class="info-group">

					<h4 class="group-activity-title activity-members-title">Members</h4>
					<?php $member_arg = Array("exclude_admins_mods"=>false); ?>
					<?php if ( bp_group_has_members($member_arg) ) : ?>

					  <ul id="member-list">
						  <?php while ( bp_group_members() ) : bp_group_the_member(); ?>
							<li>
								<a href="<?php echo bp_get_group_member_url() ?>">
									<?php bp_group_member_avatar_mini( 60, 60 ) ?>
								 </a>
							</li>
						  <?php endwhile; ?>
					  </ul>
					<?php bp_group_member_pagination(); ?>
					<?php else: ?>

					  <div id="message" class="info">
						<p>This group has no members.</p>
					  </div>

					<?php endif;?>
                    
                    <?php if ( $bp->is_item_admin || $bp->is_item_mod ): ?>
        				<div class="view-more"><a href="<?php site_url();?>/groups/<?php echo $group_slug; ?>/admin/manage-members/">See All</a></div>
        			<?php else: ?>
       					<div class="view-more"><a href="<?php site_url();?>/groups/<?php echo $group_slug; ?>/members/">See All</a></div>
       				 <?php endif; ?>
				</div>

			<?php elseif ( !bp_group_is_visible() ) : ?>
			<?php
				//   check if blog (site) is NOT private (option blog_public Not = '_2"), in which
				//   case show site posts and comments even though this group is private
				//

					show_site_posts_and_comments() ?>

				<?php /* The group is not visible, show the status message */ ?>

				<?php // do_action( 'bp_before_group_status_message' ) ?>
<!--
				<div id="message" class="info">
					<p><?php // bp_group_status_message() ?></p>
				</div>

				<?php // do_action( 'bp_after_group_status_message' ) ?>
-->
			<?php endif; ?>

		<?php  } else {  ?>
			<?php if ( !bp_group_is_visible() ) : ?>
			<?php
				//   check if blog (site) is NOT private (option blog_public Not = '_2"), in which
				//   case show site posts and comments even though this group is private
				//

					show_site_posts_and_comments() ?>

				<?php /* The group is not visible, show the status message */ ?>

				<?php // do_action( 'bp_before_group_status_message' ) ?>
<!--
				<div id="message" class="info">
					<p><?php // bp_group_status_message() ?></p>
				</div>
-->
				<?php // do_action( 'bp_after_group_status_message' ) ?>

			<?php endif; ?>

		<?php } ?>

	<?php } else {
		locate_template( array( 'groups/single/wds-bp-action-logics.php' ), true );
	} ?>

	<?php do_action( 'bp_after_group_body' ) ?>

</div><!-- #item-body -->

<?php do_action( 'bp_after_group_home_content' ) ?>