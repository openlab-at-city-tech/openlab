<?php include "header.php";?>
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
				if (wds_site_can_be_viewed()) {
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
			<div class="one-half <?php echo $first_class; ?>">
				<div class="recent-discussions">
					<div class="recent-posts">
						<h4 class="group-activity-title">Recent Discussions</h4>
						<?php if ( bp_has_forum_topics('per_page=3') ) : ?>
							<ul>
								<?php while ( bp_forum_topics() ) : bp_the_forum_topic(); ?>
									<li>
									<h5><?php bp_the_topic_title() ?></h5>
                         <p>
						<?php
							$topic_id = bp_get_the_topic_id();
							$last_topic_post = $wpdb->get_results("SELECT post_id,topic_id,post_text FROM wp_bb_posts
													WHERE topic_id='$topic_id'
												   ORDER BY post_id DESC LIMIT 1","ARRAY_A");
							$last_topic_content = wds_content_excerpt(strip_tags($last_topic_post[0]['post_text']),135);
							echo $last_topic_content;
						?>

                        			<a href="<?php bp_the_topic_permalink();?>" class="read-more">See&nbsp;More</a>
									</li>
								<?php endwhile; ?>
							</ul></p>
							<div class="view-more"><a href="<?php site_url();?>/groups/<?php echo $group_slug; ?>/forum/">See More Course Discussion</a></div>
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
						<h4 class="group-activity-title">Recent Docs</h4>
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
						<div class="view-more"><p><a href="<?php site_url();?>/groups/<?php echo $group_slug; ?>/docs/">See More Docs</a></p></div>
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
			<div class="info-group">

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
			</div>

			<?php elseif ( !bp_group_is_visible() ) : ?>
				<?php
				//   check if blog (site) is NOT private (option blog_public Not = '_2"), in which
				//   case show site posts and comments even though this group is private
				//
					if (wds_site_can_be_viewed()) {
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
					if (wds_site_can_be_viewed()) {
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
		gconnect_locate_template( array( 'groups/single/wds-bp-action-logics.php' ), true );
	} ?>

	<?php do_action( 'bp_after_group_body' ) ?>

</div><!-- #single-course-body -->

<?php do_action( 'bp_after_group_home_content' ) ?>