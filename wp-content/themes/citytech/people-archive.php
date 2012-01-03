<?php /* Template Name: People Archive */

remove_action('genesis_post_title', 'genesis_do_post_title');
add_action('genesis_post_title', 'cuny_members_title' );
function cuny_members_title() {
	global $wp_query;
	$post_obj = $wp_query->get_queried_object();
	echo '<h1 class="entry-title">'.$post_obj->post_title.' In Our Community</h1>';
}

remove_action('genesis_post_content', 'genesis_do_post_content');
add_action('genesis_post_content', 'cuny_members_index' );
function cuny_members_index() {
	global $wp_query;
	$post_obj = $wp_query->get_queried_object();
	$type=$post_obj->post_title;
	echo '<div id="people-listing">';
		if ( $type=="People" ) {
		  echo '<div class="ribbon-case"><span class="ribbon-fold"></span><h4 class="watermelon-ribbon">Faculty</h4></div>';
		  cuny_list_members( 'Faculty', 'more' );
		  echo '<div class="ribbon-case"><span class="ribbon-fold"></span><h4 class="robin-egg-ribbon">Students</h4></div>';
		  cuny_list_members( 'Student', 'more' );
		  echo '<div class="ribbon-case"><span class="ribbon-fold"></span><h4 class="yellow-canary-ribbon">Staff</h4></div>';
		  cuny_list_members( 'Staff', 'more' );
		}else{
			if ( $type == "Students" ) { $type = "Student"; }
			cuny_list_members( $type, 'page' );
		}
	echo '</div>';
}
//
//     New parameter "view" - 'more' - tells it to format a "View More" link for that member type
//                            'page' - tells it to perform normal member pagination so they can 'page' through the members
//
function cuny_list_members( $type, $view) {
global $wpdb, $bp, $members_template;
	switch ($type) {
		case "Faculty":
			$link_type = "faculty";
			break;
		case "Student":
		case "Students":
			$link_type = "student";
			break;
		case "Staff":
			$link_type = "staff";
			break;
		default:
		        $link_type = "faculty";

	}
	$display_type = $type;
	if ($display_type == "Student") {
		$display_type = "Students";
	}

	$search_terms = '';
	if(!empty($_POST['people_search'])){
		$search_terms="search_terms=".$_POST['people_search']."&";
	}
	if(!empty($_GET['search'])){
		$search_terms="search_terms=".$_GET['search']."&";
	}		
	$avatar_args = array (
			'type' => 'full',
			'width' => 75,
			'height' => 75,
			'class' => 'avatar',
			'id' => false,
			'alt' => __( 'Member avatar', 'buddypress' )
		);



	$rs = $wpdb->get_results( "SELECT user_id FROM {$bp->profile->table_name_data} where field_id=7 and value='".$type."'" );
	$ids="9999999";
	foreach ( (array)$rs as $r ) $ids.= ",".$r->user_id;
	if ( bp_has_members( $search_terms.'include=' . $ids ) ) : ?>
	<?php	if($view == "page") { ?>
			<div id="pag-top" class="pagination">
		
				<div class="pag-count" id="member-dir-count-top">
					<?php bp_members_pagination_count() ?>
				</div>
		
				<div class="pagination-links" id="member-dir-pag-top">
					<?php bp_members_pagination_links() ?>
				</div>
		
			</div>

	<?php	} ?>
			<div class="avatar-block">
				<?php while ( bp_members() ) : bp_the_member(); 
					$registered=$members_template->member->user_registered;?>
					<div class="person-block">
						<div class="item-avatar">
							<a href="<?php bp_member_permalink() ?>"><?php bp_member_avatar($avatar_args) ?></a>
						</div>
						<div class="cuny-member-info">
							<a class="member-name" href="<?php bp_member_permalink() ?>"><?php bp_member_name() ?></a>
							<span class="member-since-line">Member since <?php echo $registered; ?></span>
                            <?php if ( bp_get_member_latest_update() ) : ?>
								<span class="update"><?php bp_member_latest_update( 'length=10' ) ?></span>
							<?php endif; ?>
						</div>
					</div>
				<?php endwhile; ?>
			</div>
			<?php	if ($view == "more") { ?>
					<div class="view-more"><a href="<?php site_url(); ?>/people/<?php echo $link_type; ?>"> View All <?php echo $display_type; ?></a></div>
					<div class="clear"><p>&nbsp;</p></div>
			<?php	}  ?>
			<?php	if($view == "page") { ?>
					<div id="pag-top" class="pagination">
				
						<div class="pag-count" id="member-dir-count-top">
							<?php bp_members_pagination_count() ?>
						</div>
				
						<div class="pagination-links" id="member-dir-pag-top">
							<?php bp_members_pagination_links() ?>
						</div>
				
					</div>
		
			<?php	} ?>

		<?php else: 
			if($type=="Student"){
				$type="students";
			}?>

			<div class="widget-error">
				<p><?php _e( 'No '.strtolower($type).' were found.', 'buddypress' ) ?></p>
			</div>

		<?php endif;

}


add_action('genesis_before_sidebar_widget_area', 'cuny_buddypress_courses_actions');
function cuny_buddypress_courses_actions() { ?>
  <div class="archive-search">
  <form method="post">
  <input type="text" name="people_search" value="<?php echo $_POST['people_search'];?>" />
  <input type="submit" name="people_search_go" value="Search" />
  </form>
  </div>
<?php
}
genesis();