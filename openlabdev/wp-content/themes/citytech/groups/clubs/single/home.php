<?php include "header.php"; ?>
<div id="club-item-body">
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
				<?php if ( bp_group_is_visible() && bp_is_active( 'activity' ) ) :
				//gconnect_locate_template( array( 'groups/single/activity.php' ), true );
				?>
				<?php if (wds_site_can_be_viewed()) { ?>

					<div class="one-half first">
						<?php show_site_posts(); ?>
					</div>
					<div class="one-half">
						<?php show_site_comments(); ?>
					</div>
					<?php
						$first_class = "first";
					?>
<!--   temporarily removed announcements
		<div id="recent-announcement">
			<div class="recent-posts">
				<div class="ribbon-case">
					<span class="ribbon-fold"></span>
					<h4 class="robin-egg-ribbon">Announcements</h4>
				</div>
-->
				<?php
/*				global $wpdb;
					$query2 = new WP_Query( array('posts_per_page' => 3) );
					if($query){
					  echo '<ul>';
					  while ( $query2->have_posts() ) : $query2->the_post();
	
						  echo '<li>';
						  echo '<h5>';
						  the_title();
						  echo '</h5>';
						  $read_more = "";
						  if (strlen($post->post_content) > 135) {
							$read_more = "(Read More)";
						  }
						  ?>
						  <p><?php echo wds_content_excerpt(strip_tags($post->post_content), 135);?> <a href="<?php the_permalink();?>" class="read-more"><?php echo $read_more; ?></a></p>
						  <?php
						  echo '</li>';
					  endwhile;
					  echo '</ul>';
*/
				?>
<!--					<div class="view-more"><a href="<?php //echo site_url();?>">View Recent Announcements</a></div> -->
					  <?php
//					}else{?>
<!--
						<div id="message" class="info"> 
							<p>
-->							
							<?php // _e( 'Sorry, no announcements exist.', 'buddypress' ) ?></p>
<!--						</div> -->
					<?php  // }?>
<!--
			</div> .recent-post -->
<!--
		</div>
-->
				<?php } ?>
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
				<div class="ribbon-case">
					<span class="ribbon-fold"></span>
					<h4 class="robin-egg-ribbon">Recent Discussions</h4>
				</div>
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
				?>
							(<a href="<?php bp_the_topic_permalink();?>" class="read-more">Read More</a>)
							</li>
						<?php endwhile; ?>
					</ul>
					<div class="view-more"><a href="<?php site_url();?>/groups/<?php echo $group_slug; ?>/forum/">View More Club Discussion</a></div>
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
			<div class="ribbon-case">
				<span class="ribbon-fold"></span>
				<h4 class="robin-egg-ribbon">Recent Docs</h4>
			</div>
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
					  <p><?php echo wds_content_excerpt(strip_tags($post->post_content), 135);?> (<a href="<?php site_url();?>/groups/<?php echo $group_slug; ?>/docs/<?php echo $post->post_name; ?>" class="read-more">Read More</a>)</p>
					  <?php
					  echo '</li>';
				  endwhile;
				  echo '</ul>';
				  ?>
				<div class="view-more"><p><a href="<?php site_url();?>/groups/<?php echo $group_slug; ?>/docs/">View More Docs</a></p></div>
				<?php
				}else{
					echo '<div id="message" class="info"><p>No Recent Docs</p></div>';
				}?>
				<?php
	//*********************************************************************
			?>
		  </div>
		</div>
	</div>		
	<div class="info-group">
		<div class="recent-posts">
			<div class="ribbon-case">
				<span class="ribbon-fold"></span>
				<h4 class="robin-egg-ribbon">Members</h4>
			</div>
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
		</div>
  	</div> 
				
			<?php elseif ( !bp_group_is_visible() ) : ?>
			<?php
				//   check if blog (site) is NOT private (option blog_public Not = '_2"), in which
				//   case show site posts and comments even though this group is private
				//
				
					if (wds_site_can_be_viewed()) {
					?>
					<div class="one-half first">
					<?php
						show_site_posts();
					?>
					</div>
					<div class="one-half">
					<?php
						show_site_comments();
					?>
					</div>
					<?php
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
					?>
					<div class="one-half first">
					<?php
						show_site_posts();
					?>
					</div>
					<div class="one-half">
					<?php
						show_site_comments();
					?>
					</div>
					<?php
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
	
</div><!-- #item-body -->

<?php do_action( 'bp_after_group_home_content' ) ?>
<?php
function show_site_posts() {
	global $first_displayed;
		$group_id = bp_get_group_id(); 
		$wds_bp_group_site_id=groups_get_groupmeta($group_id, 'wds_bp_group_site_id' );
		if ( $wds_bp_group_site_id != "") {
				$first_displayed = true;
?>	
		<div id="recent-blogs">
			<div class="recent-posts">
				<div class="ribbon-case">
					<span class="ribbon-fold"></span>
					<h4 class="robin-egg-ribbon">Recent Site Posts</h4>
				</div>
				<?php global $wpdb;
				if($wds_bp_group_site_id!=""){
					switch_to_blog($wds_bp_group_site_id);
					$query = new WP_Query( array('posts_per_page' => 3) );
					if($query){
					  echo '<ul>';
					  while ( $query->have_posts() ) : $query->the_post();
						  echo '<li>';
						  echo '<h5>';
						  the_title();
						  echo '</h5>';
						  $read_more = "";
						  if (strlen($post->post_content) > 135) {
							$read_more = "(Read More)";
						  }
						  ?>
						  <p><?php echo wds_content_excerpt(strip_tags($post->post_content), 135);?> <a href="<?php the_permalink();?>" class="read-more"><?php echo $read_more;?></a></p>
						  <?php
						  echo '</li>';
					  endwhile;
					  echo '</ul>';
					  ?>
						<div class="view-more"><a href="<?php echo site_url();?>">View More Blog Activities</a></div>
					  <?php
					}else{?>
						<div id="message" class="info">
							<p><?php _e( 'Sorry, no blog posts exist.', 'buddypress' ) ?></p>
						</div>
					<?php }?>
					
					<?php
					restore_current_blog();
				}else{
					?>
						<div id="message" class="info">
							<p><?php _e( 'Sorry, no blog posts exist.', 'buddypress' ) ?></p>
						</div>
					<?php
				}?>
			</div><!-- .recent-post -->
		</div>
		<?php } ?>
<?php	
}
?>
<?php
function show_site_comments() {
		$group_id = bp_get_group_id(); 
		$wds_bp_group_site_id=groups_get_groupmeta($group_id, 'wds_bp_group_site_id' );
		if ($wds_bp_group_site_id!="") { ?>
		<div id="recent-site-comments">
			<div class="recent-posts">
				<div class="ribbon-case">
					<span class="ribbon-fold"></span>
					<h4 class="robin-egg-ribbon">Recent Site Comments</h4>
				</div>
				<?php global $wpdb,
				 $post;
	
					switch_to_blog($wds_bp_group_site_id);
					  echo '<ul>';
							$comment_args = Array("status"=>"approve",
										  "number"=>"3");
							$comments = get_comments($comment_args);
							$comments_found = false;
							foreach($comments as $comment) :
								if($comment->comment_ID == "1") {
									continue;
								}
								$comments_found = true;
								$post_id = $comment->comment_post_ID;
								$permalink = get_permalink($post_id);
								echo "<li>";
								echo wds_content_excerpt($comment->comment_content,135);
								echo "(<a href='$permalink'>Read More</a>)";
								echo "<br />&nbsp;<br />";
								echo "</li>";
							endforeach;
							if (!$comments_found) {
								echo "<li>";
								echo "&nbsp;&nbsp;&nbsp;No Comments Found";
								echo "</li>";
							}
					  echo '</ul>';
					  echo '<p>&nbsp;</p>';
					restore_current_blog();
				?>
			</div><!-- .recent-post -->
		</div>
		<?php } ?>
<?php	
}
?>