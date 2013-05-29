<?php
/**
* Group single page
*
*/

/**begin layout**/
get_header(); ?>

	<div id="content" class="hfeed">
    	<?php cuny_group_single(); ?>
    </div><!--content-->

    <div id="sidebar" class="sidebar widget-area">
	<?php cuny_buddypress_group_actions(); ?>
    </div>

<?php get_footer();
/**end layout**/

function cuny_group_single() { ?>
	<?php if ( bp_has_groups() ) : while ( bp_groups() ) : bp_the_group();
		if ( $group_type == "cheese") {
		//if ( $group_type = openlab_get_group_type( bp_get_current_group_id() )) {
			locate_template( array( 'groups/' . $group_type . 's/single/home.php' ), true );
		} else {
			?>
			<?php do_action( 'bp_before_group_home_content' ) ?>

			<?php $group_slug = bp_get_group_slug();
			$group_type = openlab_get_group_type( bp_get_current_group_id()); ?>

			<?php //group page vars
				  global $bp, $wpdb;
				  $group_id = $bp->groups->current_group->id;
				  $group_name = $bp->groups->current_group->name;
				  $group_description = $bp->groups->current_group->description;
				  $faculty_id = $bp->groups->current_group->admins[0]->user_id;
				  $first_name= ucfirst(xprofile_get_field_data( 'First Name', $faculty_id));
				  $last_name= ucfirst(xprofile_get_field_data( 'Last Name', $faculty_id));
				  $group_type = openlab_get_group_type( bp_get_current_group_id());
				  $section = groups_get_groupmeta($group_id, 'wds_section_code');
				  $html = groups_get_groupmeta($group_id, 'wds_course_html');?>

			<h1 class="entry-title group-title"><?php echo bp_group_name(); ?> Profile</h1>
			<?php if ( bp_is_group_home() ): ?>
			<div id="<?php echo $group_type; ?>-header" class="group-header">
            	<?php if ($group_type == 'portfolio'): ?>
					<?php if (strpos(bp_get_group_name(),'ePortfolio'))
                          {
                              $profile = "ePortfolio";
                          } else {
                              $profile = "Portfolio";
                          } ?>
                    <h4 class="profile-header"><?php echo $profile; ?> Profile</h4>
                <?php else: ?>
            		<h4 class="profile-header"><?php echo ucfirst($group_type); ?> Profile</h4>
                <?php endif; ?>
				 <div id="<?php echo $group_type; ?>-header-avatar" class="alignleft group-header-avatar">
                    <a href="<?php bp_group_permalink() ?>" title="<?php bp_group_name() ?>">
                        <?php bp_group_avatar('type=full&width=225') ?>
                    </a>
                     <?php if (is_user_logged_in() && $bp->is_item_admin): ?>
                     <div id="group-action-wrapper">
                                <div id="action-edit-group"><a href="<?php echo bp_group_permalink(). 'admin/edit-details/'; ?>">Edit Profile</a></div>
                                <div id="action-edit-avatar"><a href="<?php echo bp_group_permalink(). 'admin/group-avatar/'; ?>">Change Avatar</a></div>
                     </div>
                    <?php elseif (is_user_logged_in() && $group_type != 'portfolio'): ?>
                    <div id="group-action-wrapper">
                            <?php do_action( 'bp_group_header_actions' ); ?>
                    </div>
                    <?php endif; ?>
                </div><!-- #<?php echo $group_type; ?>-header-avatar -->

				<div id="<?php echo $group_type; ?>-header-content" class="alignleft group-header-content">
                    <h2 class="<?php echo $group_type; ?>-title"><?php bp_group_name() ?>
                        <?php if ($group_type != 'portfolio' && $group_type != 'club'): ?>
                            <a href="<?php bp_group_permalink() ?>/feed" class="rss"><img src="<?php bloginfo('stylesheet_directory') ?>/images/icon-RSS.png" alt="Subscribe To <?php bp_group_name() ?>'s Feeds"></a>
                        <?php endif; ?>
                    </h2>
                    
                        <?php if ($group_type == "portfolio"): ?>
                    <div class="portfolio-displayname"><span class="highlight"><?php echo bp_core_get_userlink( openlab_get_user_id_from_portfolio_group_id( bp_get_group_id() ) ); ?></span></div>
                        <?php else: ?>
                            <div class="info-line"><span class="highlight"><?php bp_group_type() ?></span> <span class="activity"><?php printf( __( 'active %s', 'buddypress' ), bp_get_group_last_active() ) ?></span></div>
                        <?php endif; ?>

              <?php do_action( 'bp_before_group_header_meta' ) ?>

              <?php if ($group_type == "course"): ?>
                  <div class="course-byline">
                    <span class="faculty-name"><b>Faculty:</b> <?php echo $first_name . " " . $last_name; ?></span>
                    <?php
                    $wds_course_code=groups_get_groupmeta($group_id, 'wds_course_code' );
                    $wds_semester=groups_get_groupmeta($group_id, 'wds_semester' );
                    $wds_year=groups_get_groupmeta($group_id, 'wds_year' );
                    $wds_departments=groups_get_groupmeta($group_id, 'wds_departments' );
                    ?>
                    <div class="info-line" style="margin-top:2px;"><?php echo $wds_course_code;?> | <?php echo $wds_departments;?> | <?php echo $wds_semester;?> <?php echo $wds_year;?></div>

                </div><!-- .course-byline -->
                <?php //do_action( 'bp_before_group_header_meta' ) ?>
                <div class="course-description">
                    <?php echo apply_filters('the_content', $group_description ); ?>
                </div>
                <?php //do_action( 'bp_group_header_meta' ) ?>

                <?php if ($html): ?>
                <div class="course-html-block">
                    <?php echo $html; ?>
                </div>
                <?php endif; //end courses block ?>

			<?php else: ?>

            	<div id="item-meta">
					<?php bp_group_description() ?>

                    <?php do_action( 'bp_group_header_meta' ) ?>
                </div>

            <?php endif; ?>
		</div><!-- .header-content -->

		<?php do_action( 'bp_after_group_header' ) ?>
		<?php do_action( 'template_notices' ) ?>

       <div class="clear"></div>
       </div><!--<?php echo $group_type; ?>-header -->

            <?php endif; ?>

            <div id="single-course-body">
<?php
//
//     control the formatting of left and right side by use of variable $first_class.
//     when it is "first" it places it on left side, when it is "" it places it on right side
//
//     Initialize it to left side to start with
//
       $first_class = "first";
?>
	<?php $group_slug = bp_get_group_slug(); ?>
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


			<?php if ( bp_group_is_visible() && bp_is_active( 'activity' ) ) : ?>
			<?php
				if ( wds_site_can_be_viewed() ) {
				     show_site_posts_and_comments();
				}
/*
				if ($first_displayed) {
					$first_class = "";
				} else {
					$first_class = "first";
				}
*/
			?>

            <?php if ($group_type != "portfolio"): ?>
			<div class="one-half <?php echo $first_class; ?>">
				<div class="recent-discussions">
					<div class="recent-posts">
						<h4 class="group-activity-title">Recent Discussions</h4>
						<?php if ( bp_has_forum_topics('per_page=3') ) : ?>
							<ul>
								<?php while ( bp_forum_topics() ) : bp_the_forum_topic(); ?>
									<li>
									<h5><?php bp_the_topic_title() ?></h5>

						<?php
							$topic_id = bp_get_the_topic_id();
							$last_topic_post = $wpdb->get_results("SELECT post_id,topic_id,post_text FROM wp_bb_posts
													WHERE topic_id='$topic_id'
												   ORDER BY post_id DESC LIMIT 1","ARRAY_A");
							$last_topic_content = wds_content_excerpt(strip_tags($last_topic_post[0]['post_text']),135);
							echo $last_topic_content;
						?></p>

                        			<a href="<?php bp_the_topic_permalink();?>" class="read-more">See More</a><p>
									</li>
								<?php endwhile; ?>
							</ul>
							<div class="view-more"><a href="<?php site_url();?>/groups/<?php echo $group_slug; ?>/forum/">See All</a></div>
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
							<a href="<?php echo bp_group_member_domain() ?>">
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
        				<div class="view-more"><a class="read-more" href="<?php site_url();?>/groups/<?php echo $group_slug; ?>/admin/manage-members/">See All</a></div>
				<?php else: ?>
                    <div class="view-more"><a class="read-more" href="<?php site_url();?>/groups/<?php echo $group_slug; ?>/members/">See All</a></div>
                <?php endif; ?>

			</div>

            <?php endif; //end of if $group != 'portfolio' ?>

			<?php elseif ( !bp_group_is_visible() ) : ?>
				<?php
				//   check if blog (site) is NOT private (option blog_public Not = '_2"), in which
				//   case show site posts and comments even though this group is private
				//
					if ( wds_site_can_be_viewed() ) {
						show_site_posts_and_comments();
						echo "<div class='clear'></div>";
					}
				?>
				<?php /* The group is not visible, show the status message */ ?>

				<?php // do_action( 'bp_before_group_status_message' ) ?>
<!--
				<div id="message" class="info">
					<p><?php // bp_group_status_message() ?></p>
				</div>
-->
				<?php // do_action( 'bp_after_group_status_message' ) ?>

			<?php endif; ?>

		<?php  } else {  ?>

			<?php if ( !bp_group_is_visible() ) : ?>
				<?php
				//   check if blog (site) is NOT private (option blog_public Not = '_2"), in which
				//   case show site posts and comments even though this group is private
				//
					if ( wds_site_can_be_viewed() ) {
						show_site_posts_and_comments();
						echo "<div class='clear'></div>";
					}
				?>

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

</div><!-- #single-course-body -->

<?php do_action( 'bp_after_group_home_content' ) ?>

	<?php	}
	endwhile; endif; ?>
<?php }

function cuny_buddypress_group_actions() {
global $bp;

?>
<?php if ( bp_has_groups() ) : while ( bp_groups() ) : bp_the_group(); ?>
		<div id="item-buttons">
			<h2 class="sidebar-header"><?php echo ucwords(groups_get_groupmeta( bp_get_group_id(), 'wds_group_type' )) ?></h2>
			<?php if ( !openlab_is_portfolio() || openlab_is_my_portfolio() ) : ?>
		    <ul>
				<?php bp_get_options_nav(); ?>
			</ul>
			<?php endif ?>



		</div><!-- #item-buttons -->
<?php do_action( 'bp_group_options_nav' ) ?>
<?php endwhile; endif; ?>
<?php }

add_filter( 'bp_get_options_nav_nav-invite-anyone', 'cuny_send_invite_fac_only');
function cuny_send_invite_fac_only( $subnav_item ) {
global $bp;
$account_type = xprofile_get_field_data( 'Account Type', $bp->loggedin_user->id);

	if ( $account_type != 'Student' )
		return $subnav_item;
}
