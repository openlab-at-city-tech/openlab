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
				
				<script type="text/javascript">
				jQuery(document).ready(function($){
				  jQuery('#cbgroups').click(function() { jQuery('.cbawardholder').hide(); jQuery('#Groups').fadeIn("slow"); return false; })
				  jQuery('#cbfriend').click(function() { jQuery('.cbawardholder').hide(); jQuery('#Friends').fadeIn("slow"); return false; })
				  jQuery('#cbupdate').click(function() { jQuery('.cbawardholder').hide(); jQuery('#PostingUpdate').fadeIn("slow"); return false; })
				  jQuery('#cpreply').click(function() { jQuery('.cbawardholder').hide(); jQuery('#Replies').fadeIn("slow"); return false; })
				  jQuery('#cpgtopic').click(function() { jQuery('.cbawardholder').hide(); jQuery('#GroupForumTopic').fadeIn("slow"); return false; })
				  jQuery('#cpgreply').click(function() { jQuery('.cbawardholder').hide(); jQuery('#GroupForumReplies').fadeIn("slow"); return false; })
				  jQuery('#cpcomment').click(function() { jQuery('.cbawardholder').hide(); jQuery('#Comments').fadeIn("slow"); return false; })
				  jQuery('#cpbpost').click(function() { jQuery('.cbawardholder').hide(); jQuery('#Blogger').fadeIn("slow"); return false; })
				  jQuery('#cbbcpost').click(function() { jQuery('.cbawardholder').hide(); jQuery('#BloggerCat').fadeIn("slow"); return false; })
				  jQuery('#cbdonation').click(function() { jQuery('.cbawardholder').hide(); jQuery('#Donation').fadeIn("slow"); return false; })
				  jQuery('#cblogin').click(function() { jQuery('.cbawardholder').hide(); jQuery('#Loggingin').fadeIn("slow"); return false; })
				  jQuery('#cbpoints').click(function() { jQuery('.cbawardholder').hide(); jQuery('#PointLevels').fadeIn("slow"); return false; })
				  jQuery('#cbspf').click(function() { jQuery('.cbawardholder').hide(); jQuery('#SPFForum').fadeIn("slow"); return false; })		
				});			
				</script>				
				
				<div id="cpbpawards">
				
				<?php
				global $wpdb, $bp;
				define('BBCPDB', $wpdb->prefix . 'bp_activity');
				define('BBCPPOSTS', $wpdb->prefix . 'posts');
				define('CUBEPTS3', $wpdb->prefix . 'cp');
				
				$bp_cp_xgroups_created = $wpdb->get_var("SELECT COUNT(*) FROM ".BBCPDB." WHERE type='created_group' AND user_id = ".$bp->displayed_user->id);
				if(function_exists('bp_get_total_friend_count')){
					$bp_cp_total_friends = bp_get_total_friend_count( bp_displayed_user_id() );
				} else {
					$bp_cp_total_friends = 0;	
				}
				$cp_bp_buddypress_update_results = $wpdb->get_var("SELECT COUNT(*) FROM ".BBCPDB." WHERE type='activity_update' AND user_id = ".$bp->displayed_user->id);
				$cp_bp_buddypress_updatereply_results = $wpdb->get_var("SELECT COUNT(*) FROM ".BBCPDB." WHERE type='activity_comment' AND user_id = ".$bp->displayed_user->id);
				$cp_bp_buddypress_forumtopic_results = $wpdb->get_var("SELECT COUNT(*) FROM ".BBCPDB." WHERE type='new_forum_topic' AND user_id = ".$bp->displayed_user->id);
				$cp_bp_buddypress_forumreply_results = $wpdb->get_var("SELECT COUNT(*) FROM ".BBCPDB." WHERE type='new_forum_post' AND user_id = ".$bp->displayed_user->id);			
				$cp_bp_buddypress_blogposts_results = (int) $wpdb->get_var('SELECT COUNT(*) FROM `'.$wpdb->prefix.'posts` where `post_type`=\'post\' and `post_status`=\'publish\' and `post_author`='.$bp->displayed_user->id);
				$bp_award_bloggercatselector_cp_bp = get_option('bp_award_bloggercatselector_cp_bp');
				$bp_award_bloggercatselector2_cp_bp = get_option('bp_award_bloggercatselector2_cp_bp');
				$bp_award_bloggercatselector3_cp_bp = get_option('bp_award_bloggercatselector3_cp_bp');
				$bp_award_bloggercatselector4_cp_bp = get_option('bp_award_bloggercatselector4_cp_bp');
				$bp_award_bloggercatselector5_cp_bp = get_option('bp_award_bloggercatselector5_cp_bp');
				$cp_bp_buddypress_blogpostscat1_results = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts LEFT JOIN $wpdb->term_relationships ON($wpdb->posts.ID = $wpdb->term_relationships.object_id) LEFT JOIN $wpdb->term_taxonomy ON($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id) WHERE $wpdb->term_taxonomy.term_id = '".$bp_award_bloggercatselector_cp_bp."' AND $wpdb->term_taxonomy.taxonomy = 'category' AND $wpdb->posts.post_status = 'publish' AND post_author = '".$bp->displayed_user->id."'");
				$cp_bp_buddypress_blogpostscat2_results = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts LEFT JOIN $wpdb->term_relationships ON($wpdb->posts.ID = $wpdb->term_relationships.object_id) LEFT JOIN $wpdb->term_taxonomy ON($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id) WHERE $wpdb->term_taxonomy.term_id = '".$bp_award_bloggercatselector2_cp_bp."' AND $wpdb->term_taxonomy.taxonomy = 'category' AND $wpdb->posts.post_status = 'publish' AND post_author = '".$bp->displayed_user->id."'");
				$cp_bp_buddypress_blogpostscat3_results = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts LEFT JOIN $wpdb->term_relationships ON($wpdb->posts.ID = $wpdb->term_relationships.object_id) LEFT JOIN $wpdb->term_taxonomy ON($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id) WHERE $wpdb->term_taxonomy.term_id = '".$bp_award_bloggercatselector3_cp_bp."' AND $wpdb->term_taxonomy.taxonomy = 'category' AND $wpdb->posts.post_status = 'publish' AND post_author = '".$bp->displayed_user->id."'");
				$cp_bp_buddypress_blogpostscat4_results = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts LEFT JOIN $wpdb->term_relationships ON($wpdb->posts.ID = $wpdb->term_relationships.object_id) LEFT JOIN $wpdb->term_taxonomy ON($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id) WHERE $wpdb->term_taxonomy.term_id = '".$bp_award_bloggercatselector4_cp_bp."' AND $wpdb->term_taxonomy.taxonomy = 'category' AND $wpdb->posts.post_status = 'publish' AND post_author = '".$bp->displayed_user->id."'");
				$cp_bp_buddypress_blogpostscat5_results = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts LEFT JOIN $wpdb->term_relationships ON($wpdb->posts.ID = $wpdb->term_relationships.object_id) LEFT JOIN $wpdb->term_taxonomy ON($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id) WHERE $wpdb->term_taxonomy.term_id = '".$bp_award_bloggercatselector5_cp_bp."' AND $wpdb->term_taxonomy.taxonomy = 'category' AND $wpdb->posts.post_status = 'publish' AND post_author = '".$bp->displayed_user->id."'");
				$cp_bp_blog_comments_results = (int) $wpdb->get_var('SELECT COUNT(comment_ID) FROM `'.$wpdb->prefix.'comments` where `user_id`='.$bp->displayed_user->id);
				// Need both for grab data from pre-cubepoints 3.0
				$cp_bp_donation_results = $wpdb->get_var("SELECT COUNT(*) FROM ".CUBEPTS3." WHERE type='donate_to' AND uid = ".$bp->displayed_user->id);
				$cp_bp_dailylogin_results = $wpdb->get_var("SELECT COUNT(*) FROM ".CUBEPTS3." WHERE type='dailypoints' AND uid = ".$bp->displayed_user->id);
				
				// Check if CubePoints 2 database exists, if it does a query on it.
				if($wpdb->get_var("SHOW TABLES LIKE '".CP_DB."'") != CP_DB || (int) get_option('cp_db_version') < 1.3) {
					define('CUBEPTS2', $wpdb->prefix . 'cubepoints');
					$cp_bp_donation_resultscp2 = $wpdb->get_var("SELECT COUNT(*) FROM ".CUBEPTS2." WHERE type='donate' AND uid = ".$bp->displayed_user->id);
					$cp_bp_dailylogin_resultscp2 = $wpdb->get_var("SELECT COUNT(*) FROM ".CUBEPTS2." WHERE type='login' AND uid = ".$bp->displayed_user->id);
				} else { // User Installed CubePoints 3.0 and never had Cubepoints 2
					$cp_bp_donation_resultscp2 = 0;
					$cp_bp_dailylogin_resultscp2 = 0;
				}
				
				$cp_bp_donation_results = $cp_bp_donation_results + $cp_bp_donation_resultscp2;
				$cp_bp_dailylogin_results = $cp_bp_dailylogin_results + $cp_bp_dailylogin_resultscp2;
				
				$cb_bp_pointtotalaward_points = cp_displayPoints($bp->displayed_user->id,1,$return,0,$format);
				if(get_option('bp_spf_support_onoff_cp_bp')) {
					// Simple Press Forum Support
					if ( function_exists ( 'sf_get_member_item' ) ) {
						$cp_bp_spf_postcount = sf_get_member_item($bp->displayed_user->id, 'posts');
					}
				}
				
				// NOT WORKING YET do_action( 'log_verifications_screen', 'cb_bp_awards_remove_screen_notifications' );				
				
				echo '<p align="center"><a name="cbawards"></a>';
				echo '<a href="#cbawards" id="cbgroups" class="cbawardslnk">'.__( 'Groups', 'cp_buddypress' ).'</a> ';
				echo '<a href="#cbawards" id="cbfriend" class="cbawardslnk">'.__('Friends','cp_buddypress').'</a> ';
				echo '<a href="#cbawards" id="cbupdate" class="cbawardslnk">'.__('Posting Updates','cp_buddypress').'</a> ';
				echo '<a href="#cbawards" id="cpreply" class="cbawardslnk">'.__('Replies','cp_buddypress').'</a> ';
				echo '<a href="#cbawards" id="cpgtopic" class="cbawardslnk">'.__('Group Forum Topics','cp_buddypress').'</a><br /><br />';
				echo '<a href="#cbawards" id="cpgreply" class="cbawardslnk">'.__('Group Forum Replies','cp_buddypress').'</a> ';
				echo '<a href="#cbawards" id="cpcomment" class="cbawardslnk">'.__('Comments','cp_buddypress').'</a> ';
				echo '<a href="#cbawards" id="cpbpost" class="cbawardslnk">'.__('Blog Posts','cp_buddypress').'</a> ';
				echo '<a href="#cbawards" id="cbbcpost" class="cbawardslnk">'.__('Blog Posts in a Category','cp_buddypress').'</a><br /><br />';
				echo '<a href="#cbawards" id="cbdonation" class="cbawardslnk">'.__('Donations','cp_buddypress').'</a> ';
				echo '<a href="#cbawards" id="cblogin" class="cbawardslnk">'.__('Logging In','cp_buddypress').'</a> ';
				echo '<a href="#cbawards" id="cbpoints" class="cbawardslnk">'.__('Points','cp_buddypress').'</a> ';
				if(get_option('bp_spf_support_onoff_cp_bp')) { echo '<a href="#cbawards" id="cbspf" class="cbawardslnk">'.__('Main Forum','cp_buddypress').'</a> ';}
				echo '</p><br />';
				
				// Group Awards
				echo '<div id="Groups" style="display:block;" class="cbawardholder">';
				if (get_option('bp_award_groupvalue_cp_bp') > 0 ) { $bp_cp_groupcounttotal++; }
				if (get_option('bp_award_group2value_cp_bp') > 0 ) { $bp_cp_groupcounttotal++; }
				if (get_option('bp_award_group3value_cp_bp') > 0 ) { $bp_cp_groupcounttotal++; }
				if (get_option('bp_award_group4value_cp_bp') > 0 ) { $bp_cp_groupcounttotal++; }
				if (get_option('bp_award_group5value_cp_bp') > 0 ) { $bp_cp_groupcounttotal++; }
				if ($bp_cp_xgroups_created >= get_option('bp_award_groupvalue_cp_bp') && get_option('bp_award_groupvalue_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_groupimg_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_grouptitle_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_groupcountearn++;
				} else { $bp_cp_groupcountearn = 0; }
				if ($bp_cp_xgroups_created >= get_option('bp_award_group2value_cp_bp') && get_option('bp_award_group2value_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_group2img_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_group2title_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_groupcountearn++;
				}
				if ($bp_cp_xgroups_created >= get_option('bp_award_group3value_cp_bp') && get_option('bp_award_group3value_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_group3img_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_group3title_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_groupcountearn++;
				}
				if ($bp_cp_xgroups_created >= get_option('bp_award_group4value_cp_bp') && get_option('bp_award_group4value_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_group4img_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_group4title_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_groupcountearn++;
				}
				if ($bp_cp_xgroups_created >= get_option('bp_award_group5value_cp_bp') && get_option('bp_award_group5value_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_group5img_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_group5title_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_groupcountearn++;
				}
				echo '<p class="cbbpimgawards" align="center">'.$bp_cp_groupcountearn.' '.__( 'of', 'cp_buddypress' ).' '.$bp_cp_groupcounttotal.' '.__( 'unlocked', 'cp_buddypress' ).'</p>';
				echo '</div>';
				// Friends Awards
				echo '<div id="Friends" style="display:none;" class="cbawardholder">';
				if (get_option('bp_award_friendvalue_cp_bp') > 0 ) { $bp_cp_friendtotal++; }
				if (get_option('bp_award_friend2value_cp_bp') > 0 ) { $bp_cp_friendtotal++; }
				if (get_option('bp_award_friend3value_cp_bp') > 0 ) { $bp_cp_friendtotal++; }
				if (get_option('bp_award_friend4value_cp_bp') > 0 ) { $bp_cp_friendtotal++; }
				if (get_option('bp_award_friend5value_cp_bp') > 0 ) { $bp_cp_friendtotal++; }
				if ($bp_cp_total_friends >= get_option('bp_award_friendvalue_cp_bp') && get_option('bp_award_friendvalue_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_friendimg_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_friendtitle_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_friendearn++;
				} else { $bp_cp_friendearn = 0; }
				if ($bp_cp_total_friends >= get_option('bp_award_friend2value_cp_bp') && get_option('bp_award_friend2value_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_friend2img_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_friend2title_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_friendearn++;					
				}
				if ($bp_cp_total_friends >= get_option('bp_award_friend3value_cp_bp') && get_option('bp_award_friend3value_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_friend3img_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_friend3title_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_friendearn++;
				}
				if ($bp_cp_total_friends >= get_option('bp_award_friend4value_cp_bp') && get_option('bp_award_friend4value_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_friend4img_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_friend4title_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_friendearn++;					
				}
				if ($bp_cp_total_friends >= get_option('bp_award_friend5value_cp_bp') && get_option('bp_award_friend5value_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_friend5img_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_friend5title_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_friendearn++;					
				}
				echo '<p class="cbbpimgawards" align="center">'.$bp_cp_friendearn.' '.__( 'of', 'cp_buddypress' ).' '.$bp_cp_friendtotal.' '.__( 'unlocked', 'cp_buddypress' ).'</p>';
				echo '</div>';
				// Update Awards			
				echo '<div id="PostingUpdate" style="display:none;" class="cbawardholder">';
				if (get_option('bp_award_updatevalue_cp_bp') > 0 ) { $bp_cp_updatetotal++; }
				if (get_option('bp_award_update2value_cp_bp') > 0 ) { $bp_cp_updatetotal++; }
				if (get_option('bp_award_update3value_cp_bp') > 0 ) { $bp_cp_updatetotal++; }
				if (get_option('bp_award_update4value_cp_bp') > 0 ) { $bp_cp_updatetotal++; }
				if (get_option('bp_award_update5value_cp_bp') > 0 ) { $bp_cp_updatetotal++; }
				if ($cp_bp_buddypress_update_results >= get_option('bp_award_updatevalue_cp_bp') && get_option('bp_award_updatevalue_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_updateimg_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_updatetitle_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_updateearn++;
				} else { $bp_cp_updateearn = 0; }
				if ($cp_bp_buddypress_update_results >= get_option('bp_award_update2value_cp_bp') && get_option('bp_award_update2value_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_update2img_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_update2title_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_updateearn++;
				}
				if ($cp_bp_buddypress_update_results >= get_option('bp_award_update3value_cp_bp') && get_option('bp_award_update3value_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_update3img_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_update3title_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_updateearn++;
				}
				if ($cp_bp_buddypress_update_results >= get_option('bp_award_update4value_cp_bp') && get_option('bp_award_update4value_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_update4img_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_update4title_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_updateearn++;
				}
				if ($cp_bp_buddypress_update_results >= get_option('bp_award_update5value_cp_bp') && get_option('bp_award_update5value_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_update5img_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_update5title_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_updateearn++;
				}
				echo '<p class="cbbpimgawards" align="center">'.$bp_cp_updateearn.' '.__( 'of', 'cp_buddypress' ).' '.$bp_cp_updatetotal.' '.__( 'unlocked', 'cp_buddypress' ).'</p>';
				echo '</div>';
				// Replies Awards
				echo '<div id="Replies" style="display:none;" class="cbawardholder">';
				if (get_option('bp_award_replyvalue_cp_bp') > 0 ) { $bp_cp_repliestotal++; }
				if (get_option('bp_award_reply2value_cp_bp') > 0 ) { $bp_cp_repliestotal++; }
				if (get_option('bp_award_reply3value_cp_bp') > 0 ) { $bp_cp_repliestotal++; }
				if (get_option('bp_award_reply4value_cp_bp') > 0 ) { $bp_cp_repliestotal++; }
				if (get_option('bp_award_reply5value_cp_bp') > 0 ) { $bp_cp_repliestotal++; }
				if ($cp_bp_buddypress_updatereply_results >= get_option('bp_award_replyvalue_cp_bp') && get_option('bp_award_replyvalue_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_replyimg_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_replytitle_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_repliesearn++;
				} else { $bp_cp_repliesearn = 0; }
				if ($cp_bp_buddypress_updatereply_results >= get_option('bp_award_reply2value_cp_bp') && get_option('bp_award_reply2value_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_reply2img_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_reply2title_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_repliesearn++;
				}
				if ($cp_bp_buddypress_updatereply_results >= get_option('bp_award_reply3value_cp_bp') && get_option('bp_award_reply3value_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_reply3img_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_reply3title_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_repliesearn++;
				}
				if ($cp_bp_buddypress_updatereply_results >= get_option('bp_award_reply4value_cp_bp') && get_option('bp_award_reply4value_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_reply4img_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_reply4title_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_repliesearn++;
				}
				if ($cp_bp_buddypress_updatereply_results >= get_option('bp_award_reply5value_cp_bp') && get_option('bp_award_reply5value_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_reply5img_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_reply5title_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_repliesearn++;
				}
				echo '<p class="cbbpimgawards" align="center">'.$bp_cp_repliesearn.' '.__( 'of', 'cp_buddypress' ).' '.$bp_cp_repliestotal.' '.__( 'unlocked', 'cp_buddypress' ).'</p>';
				echo '</div>';
				// Forum Topic Awards
				echo '<div id="GroupForumTopic" style="display:none;" class="cbawardholder">';
				if (get_option('bp_award_forumtopicvalue_cp_bp') > 0 ) { $bp_cp_gforumtopictotal++; }
				if (get_option('bp_award_forumtopic2value_cp_bp') > 0 ) { $bp_cp_gforumtopictotal++; }
				if (get_option('bp_award_forumtopic3value_cp_bp') > 0 ) { $bp_cp_gforumtopictotal++; }
				if (get_option('bp_award_forumtopic4value_cp_bp') > 0 ) { $bp_cp_gforumtopictotal++; }
				if (get_option('bp_award_forumtopic5value_cp_bp') > 0 ) { $bp_cp_gforumtopictotal++; }
				if ($cp_bp_buddypress_forumtopic_results >= get_option('bp_award_forumtopicvalue_cp_bp') && get_option('bp_award_forumtopicvalue_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_forumtopicimg_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_forumtopictitle_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_gforumtopicearn++;
				} else { $bp_cp_gforumtopicearn = 0; }
				if ($cp_bp_buddypress_forumtopic_results >= get_option('bp_award_forumtopic2value_cp_bp') && get_option('bp_award_forumtopic2value_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_forumtopic2img_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_forumtopic2title_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_gforumtopicearn++;
				}
				if ($cp_bp_buddypress_forumtopic_results >= get_option('bp_award_forumtopic3value_cp_bp') && get_option('bp_award_forumtopic3value_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_forumtopic3img_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_forumtopic3title_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_gforumtopicearn++;
				}
				if ($cp_bp_buddypress_forumtopic_results >= get_option('bp_award_forumtopic4value_cp_bp') && get_option('bp_award_forumtopic4value_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_forumtopic4img_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_forumtopic4title_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_gforumtopicearn++;
				}
				if ($cp_bp_buddypress_forumtopic_results >= get_option('bp_award_forumtopic5value_cp_bp') && get_option('bp_award_forumtopic5value_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_forumtopic5img_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_forumtopic5title_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_gforumtopicearn++;
				}
				echo '<p class="cbbpimgawards" align="center">'.$bp_cp_gforumtopicearn.' '.__( 'of', 'cp_buddypress' ).' '.$bp_cp_gforumtopictotal.' '.__( 'unlocked', 'cp_buddypress' ).'</p>';
				echo '</div>';
				// Forum Replies Awards
				echo '<div id="GroupForumReplies" style="display:none;" class="cbawardholder">';
				if (get_option('bp_award_forumreplyvalue_cp_bp') > 0 ) { $bp_cp_gforumreplytotal++; }
				if (get_option('bp_award_forumreply2value_cp_bp') > 0 ) { $bp_cp_gforumreplytotal++; }
				if (get_option('bp_award_forumreply3value_cp_bp') > 0 ) { $bp_cp_gforumreplytotal++; }
				if (get_option('bp_award_forumreply4value_cp_bp') > 0 ) { $bp_cp_gforumreplytotal++; }
				if (get_option('bp_award_forumreply5value_cp_bp') > 0 ) { $bp_cp_gforumreplytotal++; }
				if ($cp_bp_buddypress_forumreply_results >= get_option('bp_award_forumreplyvalue_cp_bp') && get_option('bp_award_forumreplyvalue_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_forumreplyimg_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_forumreplytitle_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_gforumreplyearn++;
				} else { $bp_cp_gforumreplyearn = 0; }
				if ($cp_bp_buddypress_forumreply_results >= get_option('bp_award_forumreply2value_cp_bp') && get_option('bp_award_forumreply2value_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_forumreply2img_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_forumreply2title_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_gforumreplyearn++;
				}
				if ($cp_bp_buddypress_forumreply_results >= get_option('bp_award_forumreply3value_cp_bp') && get_option('bp_award_forumreply3value_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_forumreply3img_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_forumreply3title_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_gforumreplyearn++;
				}
				if ($cp_bp_buddypress_forumreply_results >= get_option('bp_award_forumreply4value_cp_bp') && get_option('bp_award_forumreply4value_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_forumreply4img_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_forumreply4title_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_gforumreplyearn++;
				}
				if ($cp_bp_buddypress_forumreply_results >= get_option('bp_award_forumreply5value_cp_bp') && get_option('bp_award_forumreply5value_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_forumreply5img_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_forumreply5title_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_gforumreplyearn++;
				}
				echo '<p class="cbbpimgawards" align="center">'.$bp_cp_gforumreplyearn.' '.__( 'of', 'cp_buddypress' ).' '.$bp_cp_gforumreplytotal.' '.__( 'unlocked', 'cp_buddypress' ).'</p>';
				echo '</div>';
				// Blog Comments Awards
				echo '<div id="Comments" style="display:none;" class="cbawardholder">';
				if (get_option('bp_award_blogcommentvalue_cp_bp') > 0 ) { $bp_cp_blogcommenttotal++; }
				if (get_option('bp_award_blogcomment2value_cp_bp') > 0 ) { $bp_cp_blogcommenttotal++; }
				if (get_option('bp_award_blogcomment3value_cp_bp') > 0 ) { $bp_cp_blogcommenttotal++; }
				if (get_option('bp_award_blogcomment4value_cp_bp') > 0 ) { $bp_cp_blogcommenttotal++; }
				if (get_option('bp_award_blogcomment5value_cp_bp') > 0 ) { $bp_cp_blogcommenttotal++; }
				if ($cp_bp_blog_comments_results >= get_option('bp_award_blogcommentvalue_cp_bp') && get_option('bp_award_blogcommentvalue_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_blogcommentimg_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_blogcommenttitle_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_blogcommentearn++;
				} else { $bp_cp_blogcommentearn = 0; }
				if ($cp_bp_blog_comments_results >= get_option('bp_award_blogcomment2value_cp_bp') && get_option('bp_award_blogcomment2value_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_blogcomment2img_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_blogcomment2title_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_blogcommentearn++;
				}
				if ($cp_bp_blog_comments_results >= get_option('bp_award_blogcomment3value_cp_bp') && get_option('bp_award_blogcomment3value_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_blogcomment3img_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_blogcomment3title_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_blogcommentearn++;
				}
				if ($cp_bp_blog_comments_results >= get_option('bp_award_blogcomment4value_cp_bp') && get_option('bp_award_blogcomment4value_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_blogcomment4img_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_blogcomment4title_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_blogcommentearn++;
				}
				if ($cp_bp_blog_comments_results >= get_option('bp_award_blogcomment5value_cp_bp') && get_option('bp_award_blogcomment5value_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_blogcomment5img_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_blogcomment5title_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_blogcommentearn++;
				}
				echo '<p class="cbbpimgawards" align="center">'.$bp_cp_blogcommentearn.' '.__( 'of', 'cp_buddypress' ).' '.$bp_cp_blogcommenttotal.' '.__( 'unlocked', 'cp_buddypress' ).'</p>';
				echo '</div>';
				// Blog Posts Awards
				echo '<div id="Blogger" style="display:none;" class="cbawardholder">';
				if (get_option('bp_award_bloggervalue_cp_bp') > 0 ) { $bp_cp_bloggertotal++; }
				if (get_option('bp_award_blogger2value_cp_bp') > 0 ) { $bp_cp_bloggertotal++; }
				if (get_option('bp_award_blogger3value_cp_bp') > 0 ) { $bp_cp_bloggertotal++; }
				if (get_option('bp_award_blogger4value_cp_bp') > 0 ) { $bp_cp_bloggertotal++; }
				if (get_option('bp_award_blogger5value_cp_bp') > 0 ) { $bp_cp_bloggertotal++; }
				if ($cp_bp_buddypress_blogposts_results >= get_option('bp_award_bloggervalue_cp_bp') && get_option('bp_award_bloggervalue_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_bloggerimg_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_bloggertitle_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_bloggerearn++;
				} else { $bp_cp_bloggerearn = 0; }
				if ($cp_bp_buddypress_blogposts_results >= get_option('bp_award_blogger2value_cp_bp') && get_option('bp_award_blogger2value_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_blogger2img_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_blogger2title_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_bloggerearn++;
				}
				if ($cp_bp_buddypress_blogposts_results >= get_option('bp_award_blogger3value_cp_bp') && get_option('bp_award_blogger3value_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_blogger3img_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_blogger3title_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_bloggerearn++;
				}
				if ($cp_bp_buddypress_blogposts_results >= get_option('bp_award_blogger4value_cp_bp') && get_option('bp_award_blogger4value_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_blogger4img_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_blogger4title_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_bloggerearn++;
				}
				if ($cp_bp_buddypress_blogposts_results >= get_option('bp_award_blogger5value_cp_bp') && get_option('bp_award_blogger5value_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_blogger5img_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_blogger5title_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_bloggerearn++;
				}
				echo '<p class="cbbpimgawards" align="center">'.$bp_cp_bloggerearn.' '.__( 'of', 'cp_buddypress' ).' '.$bp_cp_bloggertotal.' '.__( 'unlocked', 'cp_buddypress' ).'</p>';
				echo '</div>';
				// Blogger per category Awards
				echo '<div id="BloggerCat" style="display:none;" class="cbawardholder">';
				if (get_option('bp_award_bloggercatvalue_cp_bp') > 0 ) { $bp_cp_blogcattotal++; }
				if (get_option('bp_award_bloggercat2value_cp_bp') > 0 ) { $bp_cp_blogcattotal++; }
				if (get_option('bp_award_bloggercat3value_cp_bp') > 0 ) { $bp_cp_blogcattotal++; }
				if (get_option('bp_award_bloggercat4value_cp_bp') > 0 ) { $bp_cp_blogcattotal++; }
				if (get_option('bp_award_bloggercat5value_cp_bp') > 0 ) { $bp_cp_blogcattotal++; }
				if ($cp_bp_buddypress_blogpostscat1_results >= get_option('bp_award_bloggercatvalue_cp_bp') && get_option('bp_award_bloggercatvalue_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_bloggercatimg_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_bloggercattitle_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_blogcatearn++;
				} else { $bp_cp_blogcatearn = 0; }
				if ($cp_bp_buddypress_blogpostscat2_results >= get_option('bp_award_bloggercat2value_cp_bp') && get_option('bp_award_bloggercat2value_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_bloggercat2img_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_bloggercat2title_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_blogcatearn++;
				}
				if ($cp_bp_buddypress_blogpostscat3_results >= get_option('bp_award_bloggercat3value_cp_bp') && get_option('bp_award_bloggercat3value_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_bloggercat3img_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_bloggercat3title_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_blogcatearn++;
				}
				if ($cp_bp_buddypress_blogpostscat4_results >= get_option('bp_award_bloggercat4value_cp_bp') && get_option('bp_award_bloggercat4value_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_bloggercat4img_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_bloggercat4title_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_blogcatearn++;
				}
				if ($cp_bp_buddypress_blogpostscat5_results >= get_option('bp_award_bloggercat5value_cp_bp') && get_option('bp_award_bloggercat5value_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_bloggercat5img_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_bloggercat5title_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_blogcatearn++;
				}
				echo '<p class="cbbpimgawards" align="center">'.$bp_cp_blogcatearn.' '.__( 'of', 'cp_buddypress' ).' '.$bp_cp_blogcattotal.' '.__( 'unlocked', 'cp_buddypress' ).'</p>';
				echo '</div>';
				// Donation Awards
				echo '<div id="Donation" style="display:none;" class="cbawardholder">';
				if (get_option('bp_award_donationsvalue_cp_bp') > 0 ) { $bp_cp_donationtotal++; }
				if (get_option('bp_award_donations2value_cp_bp') > 0 ) { $bp_cp_donationtotal++; }
				if (get_option('bp_award_donations3value_cp_bp') > 0 ) { $bp_cp_donationtotal++; }
				if (get_option('bp_award_donations4value_cp_bp') > 0 ) { $bp_cp_donationtotal++; }
				if (get_option('bp_award_donations5value_cp_bp') > 0 ) { $bp_cp_donationtotal++; }
				if ($cp_bp_donation_results >= get_option('bp_award_donationsvalue_cp_bp') && get_option('bp_award_donationsvalue_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_donationsimg_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_donationstitle_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_donationearn++;
				} else { $bp_cp_donationearn = 0; }
				if ($cp_bp_donation_results >= get_option('bp_award_donations2value_cp_bp') && get_option('bp_award_donations2value_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_donations2img_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_donations2title_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_donationearn++;
				}
				if ($cp_bp_donation_results >= get_option('bp_award_donations3value_cp_bp') && get_option('bp_award_donations3value_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_donations3img_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_donations3title_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_donationearn++;
				}
				if ($cp_bp_donation_results >= get_option('bp_award_donations4value_cp_bp') && get_option('bp_award_donations4value_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_donations4img_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_donations4title_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_donationearn++;
				}
				if ($cp_bp_donation_results >= get_option('bp_award_donations5value_cp_bp') && get_option('bp_award_donations5value_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_donations5img_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_donations5title_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_donationearn++;
				}
				echo '<p class="cbbpimgawards" align="center">'.$bp_cp_donationearn.' '.__( 'of', 'cp_buddypress' ).' '.$bp_cp_donationtotal.' '.__( 'unlocked', 'cp_buddypress' ).'</p>';
				echo '</div>';
				/* Daily Login */
				echo '<div id="Loggingin" style="display:none;" class="cbawardholder">';
				if (get_option('bp_award_dailyloginvalue_cp_bp') > 0 ) { $bp_cp_dailylogintotal++; }
				if (get_option('bp_award_dailylogin2value_cp_bp') > 0 ) { $bp_cp_dailylogintotal++; }
				if (get_option('bp_award_dailylogin3value_cp_bp') > 0 ) { $bp_cp_dailylogintotal++; }
				if (get_option('bp_award_dailylogin4value_cp_bp') > 0 ) { $bp_cp_dailylogintotal++; }
				if (get_option('bp_award_dailylogin5value_cp_bp') > 0 ) { $bp_cp_dailylogintotal++; }
				if ($cp_bp_dailylogin_results >= get_option('bp_award_dailyloginvalue_cp_bp') && get_option('bp_award_dailyloginvalue_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_dailyloginimg_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_dailylogintitle_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_dailyloginearn++;
				} else { $bp_cp_dailyloginearn = 0; }
				if ($cp_bp_dailylogin_results >= get_option('bp_award_dailylogin2value_cp_bp') && get_option('bp_award_dailylogin2value_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_dailylogin2img_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_dailylogin2title_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_dailyloginearn++;
				}
				if ($cp_bp_dailylogin_results >= get_option('bp_award_dailylogin3value_cp_bp') && get_option('bp_award_dailylogin3value_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_dailylogin3img_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_dailylogin3title_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_dailyloginearn++;
				}
				if ($cp_bp_dailylogin_results >= get_option('bp_award_dailylogin4value_cp_bp') && get_option('bp_award_dailylogin4value_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_dailylogin4img_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_dailylogin4title_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_dailyloginearn++;
				}
				if ($cp_bp_dailylogin_results >= get_option('bp_award_dailylogin5value_cp_bp') && get_option('bp_award_dailylogin5value_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_dailylogin5img_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_dailylogin5title_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_dailyloginearn++;
				}
				echo '<p class="cbbpimgawards" align="center">'.$bp_cp_dailyloginearn.' '.__( 'of', 'cp_buddypress' ).' '.$bp_cp_dailylogintotal.' '.__( 'unlocked', 'cp_buddypress' ).'</p>';
				echo '</div>';
				/* Point Levels */
				echo '<div id="PointLevels" style="display:none;" class="cbawardholder">';				
				if (get_option('bp_award_points_1value_cp_bp') > 0 ) { $bp_cp_pointstotal++; }
				if (get_option('bp_award_points_2value_cp_bp') > 0 ) { $bp_cp_pointstotal++; }
				if (get_option('bp_award_points_3value_cp_bp') > 0 ) { $bp_cp_pointstotal++; }
				if (get_option('bp_award_points_4value_cp_bp') > 0 ) { $bp_cp_pointstotal++; }
				if (get_option('bp_award_points_5value_cp_bp') > 0 ) { $bp_cp_pointstotal++; }
				if (get_option('bp_award_points_6value_cp_bp') > 0 ) { $bp_cp_pointstotal++; }
				if (get_option('bp_award_points_7value_cp_bp') > 0 ) { $bp_cp_pointstotal++; }
				if (get_option('bp_award_points_8value_cp_bp') > 0 ) { $bp_cp_pointstotal++; }
				if (get_option('bp_award_points_9value_cp_bp') > 0 ) { $bp_cp_pointstotal++; }
				if (get_option('bp_award_points_10value_cp_bp') > 0 ) { $bp_cp_pointstotal++; }
				if ($cb_bp_pointtotalaward_points >= get_option('bp_award_points_1value_cp_bp') && get_option('bp_award_points_1value_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_points_1img_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_points_1title_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_pointsearn++;
				} else { $bp_cp_pointsearn = 0; }
				if ($cb_bp_pointtotalaward_points >= get_option('bp_award_points_2value_cp_bp') && get_option('bp_award_points_2value_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_points_2img_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_points_2title_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_pointsearn++;
				}
				if ($cb_bp_pointtotalaward_points >= get_option('bp_award_points_3value_cp_bp') && get_option('bp_award_points_3value_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_points_3img_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_points_3title_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_pointsearn++;
				}
				if ($cb_bp_pointtotalaward_points >= get_option('bp_award_points_4value_cp_bp') && get_option('bp_award_points_4value_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_points_4img_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_points_4title_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_pointsearn++;
				}
				if ($cb_bp_pointtotalaward_points >= get_option('bp_award_points_5value_cp_bp') && get_option('bp_award_points_5value_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_points_5img_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_points_5title_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_pointsearn++;
				}
				if ($cb_bp_pointtotalaward_points >= get_option('bp_award_points_6value_cp_bp') && get_option('bp_award_points_6value_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_points_6img_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_points_6title_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_pointsearn++;
				}
				if ($cb_bp_pointtotalaward_points >= get_option('bp_award_points_7value_cp_bp') && get_option('bp_award_points_7value_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_points_7img_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_points_7title_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_pointsearn++;
				}
				if ($cb_bp_pointtotalaward_points >= get_option('bp_award_points_8value_cp_bp') && get_option('bp_award_points_8value_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_points_8img_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_points_8title_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_pointsearn++;
				}
				if ($cb_bp_pointtotalaward_points >= get_option('bp_award_points_9value_cp_bp') && get_option('bp_award_points_9value_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_points_9img_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_points_9title_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_pointsearn++;
				}
				if ($cb_bp_pointtotalaward_points >= get_option('bp_award_points_10value_cp_bp') && get_option('bp_award_points_10value_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_points_10img_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_points_10title_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_pointsearn++;
				}
				echo '<p class="cbbpimgawards" align="center">'.$bp_cp_pointsearn.' '.__( 'of', 'cp_buddypress' ).' '.$bp_cp_pointstotal.' '.__( 'unlocked', 'cp_buddypress' ).'</p>';
				echo '</div>';
			
				// Simple Press Forum Support
				echo '<div id="SPFForum" style="display:none;" class="cbawardholder">';
				if(get_option('bp_spf_support_onoff_cp_bp')) {
				if (get_option('bp_award_spf_forumvalue_cp_bp') > 0 ) { $bp_cp_spfpoststotal++; }
				if (get_option('bp_award_spf_forum2value_cp_bp') > 0 ) { $bp_cp_spfpoststotal++; }
				if (get_option('bp_award_spf_forum3value_cp_bp') > 0 ) { $bp_cp_spfpoststotal++; }
				if (get_option('bp_award_spf_forum4value_cp_bp') > 0 ) { $bp_cp_spfpoststotal++; }
				if (get_option('bp_award_spf_forum5value_cp_bp') > 0 ) { $bp_cp_spfpoststotal++; }
				if ($cp_bp_spf_postcount >= get_option('bp_award_spf_forumvalue_cp_bp') && get_option('bp_award_spf_forumvalue_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_spf_forumimg_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_spf_forumtitle_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_spfpostsearned++;
				} else { $bp_cp_spfpostsearned = 0; }
				if ($cp_bp_spf_postcount >= get_option('bp_award_spf_forum2value_cp_bp') && get_option('bp_award_spf_forum2value_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_spf_forum2img_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_spf_forum2title_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_spfpostsearned++;
				}
				if ($cp_bp_spf_postcount >= get_option('bp_award_spf_forum3value_cp_bp') && get_option('bp_award_spf_forum3value_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_spf_forum3img_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_spf_forum3title_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_spfpostsearned++;
				}
				if ($cp_bp_spf_postcount >= get_option('bp_award_spf_forum4value_cp_bp') && get_option('bp_award_spf_forum4value_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_spf_forum4img_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_spf_forum4title_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_spfpostsearned++;
				}
				if ($cp_bp_spf_postcount >= get_option('bp_award_spf_forum5value_cp_bp') && get_option('bp_award_spf_forum5value_cp_bp') != 0) {
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_spf_forum5img_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_spf_forum5title_cp_bp').'</p>';
					$bp_cp_total_awards++;
					$bp_cp_spfpostsearned++;
				}
				echo '<p class="cbbpimgawards" align="center">'.$bp_cp_spfpostsearned.' '.__( 'of', 'cp_buddypress' ).' '.$bp_cp_spfpoststotal.' '.__( 'unlocked', 'cp_buddypress' ).'</p>';
			}
				echo '</div>';
				
				/* Add up Total Awards Possible if value is not zero */
					if (get_option('bp_award_groupvalue_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }
					if (get_option('bp_award_group2value_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }
					if (get_option('bp_award_group3value_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }
					if (get_option('bp_award_group4value_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }
					if (get_option('bp_award_group5value_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }
					
					if (get_option('bp_award_friendvalue_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }
					if (get_option('bp_award_friend2value_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }
					if (get_option('bp_award_friend3value_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }
					if (get_option('bp_award_friend4value_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }
					if (get_option('bp_award_friend5value_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }

					if (get_option('bp_award_updatevalue_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }
					if (get_option('bp_award_update2value_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }
					if (get_option('bp_award_update3value_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }
					if (get_option('bp_award_update4value_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }
					if (get_option('bp_award_update5value_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }

					if (get_option('bp_award_replyvalue_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }
					if (get_option('bp_award_reply2value_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }
					if (get_option('bp_award_reply3value_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }
					if (get_option('bp_award_reply4value_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }
					if (get_option('bp_award_reply5value_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }

					if (get_option('bp_award_forumtopicvalue_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }
					if (get_option('bp_award_forumtopic2value_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }
					if (get_option('bp_award_forumtopic3value_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }
					if (get_option('bp_award_forumtopic4value_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }
					if (get_option('bp_award_forumtopic5value_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }

					if (get_option('bp_award_forumreplyvalue_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }
					if (get_option('bp_award_forumreply2value_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }
					if (get_option('bp_award_forumreply3value_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }
					if (get_option('bp_award_forumreply4value_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }
					if (get_option('bp_award_forumreply5value_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }

					if (get_option('bp_award_blogcommentvalue_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }
					if (get_option('bp_award_blogcomment2value_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }
					if (get_option('bp_award_blogcomment3value_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }
					if (get_option('bp_award_blogcomment4value_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }
					if (get_option('bp_award_blogcomment5value_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }

					if (get_option('bp_award_bloggervalue_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }
					if (get_option('bp_award_blogger2value_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }
					if (get_option('bp_award_blogger3value_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }
					if (get_option('bp_award_blogger4value_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }
					if (get_option('bp_award_blogger5value_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }

					if (get_option('bp_award_bloggercatvalue_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }
					if (get_option('bp_award_bloggercat2value_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }
					if (get_option('bp_award_bloggercat3value_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }
					if (get_option('bp_award_bloggercat4value_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }
					if (get_option('bp_award_bloggercat5value_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }

					if (get_option('bp_award_donationsvalue_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }
					if (get_option('bp_award_donations2value_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }
					if (get_option('bp_award_donations3value_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }
					if (get_option('bp_award_donations4value_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }
					if (get_option('bp_award_donations5value_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }

					if (get_option('bp_award_dailyloginvalue_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }
					if (get_option('bp_award_dailylogin2value_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }
					if (get_option('bp_award_dailylogin3value_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }
					if (get_option('bp_award_dailylogin4value_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }
					if (get_option('bp_award_dailylogin5value_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }

					if (get_option('bp_award_points_1value_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }
					if (get_option('bp_award_points_2value_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }
					if (get_option('bp_award_points_3value_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }
					if (get_option('bp_award_points_4value_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }
					if (get_option('bp_award_points_5value_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }
					if (get_option('bp_award_points_6value_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }
					if (get_option('bp_award_points_7value_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }
					if (get_option('bp_award_points_8value_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }
					if (get_option('bp_award_points_9value_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }
					if (get_option('bp_award_points_10value_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }

				if(get_option('bp_spf_support_onoff_cp_bp')) {
					if (get_option('bp_award_spf_forumvalue_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }
					if (get_option('bp_award_spf_forum2value_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }
					if (get_option('bp_award_spf_forum3value_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }
					if (get_option('bp_award_spf_forum4value_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }
					if (get_option('bp_award_spf_forum5value_cp_bp') > 0 ) { $bp_cp_total_awardsNO++; }
				}

				$bp_cp_total_awardsNO++;
				$bp_cp_total_awardsmod = $bp_cp_total_awards + 1;
				
				if ( $bp_cp_total_awardsmod == $bp_cp_total_awardsNO ) {
					$bp_cp_total_awards++;
					echo '<p class="cbbpimgawards"><img src="'.get_option('bp_award_earnedall_img_cp_bp').'" class="cbbpimgawards" />'.get_option('bp_award_earnedall_title_cp_bp').'</p>';
				}
				echo '<p class="cbbpimgawards" align="center">'.$bp_cp_total_awards.' '.get_option('bp_awards_menutitle_cp_bp').' '.__( 'Earned', 'cp_buddypress' ).'';
				echo '<br />'.$bp_cp_total_awardsNO.' '.get_option('bp_awards_menutitle_cp_bp').' '.__( 'Total', 'cp_buddypress' ).'</p>';
				?>
				
				</div>		
			</div><!-- #item-body -->
		</div><!-- .padder -->
	</div><!-- #content -->
	
<?php get_sidebar('buddypress') ?>

<?php get_footer('buddypress') ?>