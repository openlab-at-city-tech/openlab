<?php
add_filter('genesis_pre_get_option_site_layout', 'cuny_home_layout');
function cuny_home_layout($opt) {
    $opt = 'full-width-content';
    return $opt;
}

remove_action('genesis_loop', 'genesis_do_loop');
add_action('genesis_loop', 'cuny_build_homepage' );

function cuny_build_homepage() {
	echo '<div id="home-left">';
		echo '<div id="cuny_openlab_jump_start">';
			cuny_home_login();
			cuny_home_support();
			cuny_home_new_members();
		echo '</div>';
		echo '<div class="box-1" id="whos-online">';
			echo '<h3 class="title">Who\'s Online?</h3>';
		    cuny_whos_online('faculty');
		    cuny_whos_online('student');
		    cuny_whos_online('staff');
		echo '</div>';
	echo '</div>';
	
	echo '<div id="home-right">';
		cuny_home_four_square();
	echo '</div>';
}

function cuny_home_login() {
	echo '<div id="open-lab-login" class="box-1">';
		echo '<h3 class="title">Log in to OpenLab</h3>';
		 if ( is_user_logged_in() ) : ?>

		<?php do_action( 'bp_before_sidebar_me' ) ?>

		<div id="sidebar-me">
			<a class="alignleft" href="<?php echo bp_loggedin_user_domain() ?>">
				<?php bp_loggedin_user_avatar( 'type=thumb&width=40&height=40' ) ?>
			</a>

			<h4><?php echo bp_core_get_userlink( bp_loggedin_user_id() ); ?></h4>
			<a class="button logout" href="<?php echo wp_logout_url( bp_get_root_domain() ) ?>"><?php _e( 'Log Out', 'buddypress' ) ?></a>

			<?php do_action( 'bp_sidebar_me' ) ?>
		</div>

		<?php do_action( 'bp_after_sidebar_me' ) ?>

		<?php if ( function_exists( 'bp_message_get_notices' ) ) : ?>
			<?php bp_message_get_notices(); /* Site wide notices to all users */ ?>
		<?php endif; ?>

	<?php else : ?>

		<?php do_action( 'bp_before_sidebar_login_form' ) ?>

		<form name="login-form" id="sidebar-login-form" class="standard-form" action="<?php echo site_url( 'wp-login.php', 'login_post' ) ?>" method="post">
			<label><?php _e( 'Username', 'buddypress' ) ?>
			<input type="text" name="log" id="sidebar-user-login" class="input" value="<?php echo esc_attr(stripslashes($user_login)); ?>" tabindex="97" /></label>

			<label><?php _e( 'Password', 'buddypress' ) ?>
			<input type="password" name="pwd" id="sidebar-user-pass" class="input" value="" tabindex="98" /></label>

			<div><input name="rememberme" type="checkbox" id="sidebar-rememberme" value="forever" tabindex="99" /> <?php _e( 'Keep me logged in', 'buddypress' ) ?>
			<input type="submit" name="wp-submit" id="sidebar-wp-submit" value="<?php _e('Log In'); ?>" tabindex="100" /></div>

			<?php do_action( 'bp_sidebar_login_form' ) ?>
			<input type="hidden" name="testcookie" value="1" />
		</form>
		<a class="forgot-password-link" href="<?php echo site_url('wp-login.php?action=lostpassword', 'login') ?>">Forgot Password?</a>

		<p>
		<?php _e( 'Need an account? <b><a href="'.site_url().'/register/">Sign Up</a></b> to become a member of the New York City College of Technology OpenLab community.', 'buddypress' ) ?>
		</p>
		<?php do_action( 'bp_after_sidebar_login_form' ) ?>

	<?php endif;
	echo '</div>';
}
function cuny_home_support() {
	echo '<div id="need-support" class="box-1">';
		echo '<h3 class="title">Need Help?</h3>';
		echo "<p>Visit our <a href='".site_url()."/support/help/'>help page</a>, or check out our <a href='".site_url()."/support/faq/'>FAQ</a>. If you still have questions or can't find what you're looking for, you can also <a href='".site_url()."/support/contact-us/'>contact us</a>.</p>";
	echo '</div>';
}
function cuny_home_new_members() {
	global $wpdb, $bp;
	echo '<div id="new-members" class="box-1 last">';
		echo '<h3 class="title">New OpenLab Members</h3>';
		if ( bp_has_members( 'type=newest&max=5' ) ) :
			$avatar_args = array (
				'type' => 'full',
				'width' => 163,
				'height' => 163,
				'class' => 'avatar',
				'id' => false,
				'alt' => __( 'Member avatar', 'buddypress' )
			);
			echo '<div id="home-new-member-wrap"><ul>';
				while ( bp_members() ) : bp_the_member(); 
					$user_id=bp_get_member_user_id();
					$firstname = xprofile_get_field_data( 'Name' , $user_id);
//					$lastname = xprofile_get_field_data( 'Last Name' , $user_id);?>
					<li class="home-new-member">
		        		<div class="home-new-member-avatar">
								<a href="<?php bp_member_permalink() ?>"><?php bp_member_avatar($avatar_args) ?></a>
						</div>
		                <div class="home-new-member-info">
		                    <?php echo "<h2 class='green-title'>" . $firstname ."</h2>"; ?>
		                    <div class="registered"><?php bp_member_registered() ?></div>
		                </div>
		            </li>
	        	<?php endwhile;
        	echo '</ul></div>';
        	echo '<div class="new-member-navigation">';
			echo '<button class="prev">&lt;&lt;</button>';
			echo '<button class="next">&gt;&gt;</button>';
			echo '</div>';
		endif;
	echo '</div>';
}

function cuny_whos_online( $type ) {
global $wpdb, $bp;
	$avatar_args = array (
			'type' => 'full',
			'width' => 75,
			'height' => 75,
			'class' => 'avatar',
			'id' => false,
			'alt' => __( 'Member avatar', 'buddypress' )
		);

	//$sql="SELECT user_id FROM {$bp->profile->table_name_data} where field_id=7 and value='".$type."' limit 6";
	//if($_GET['test']=="yes"){
		$sql="SELECT a.user_id FROM {$bp->profile->table_name_data} a, wp_usermeta b where a.field_id=7 and a.value='".$type."' and a.user_id=b.user_id and b.meta_key='last_activity' and DATE_ADD( b.meta_value, INTERVAL 50 DAY ) >= UTC_TIMESTAMP() order by b.meta_value desc limit 6";
		//echo $sql;
	//}
	$rs = $wpdb->get_results( $sql );
	$ids="9999999";
	if ( $type == "faculty" ) {
		$title = "Faculty";
		$class = "watermelon-ribbon";
	}else if ( $type == "student" ) {
		$title = "Students";
		$class = "robin-egg-ribbon";
	} else if ( $type == "staff" ) {
		$title = "Staff";
		$class = "yellow-canary-ribbon";
	}
	foreach ( (array)$rs as $r ) $ids.= ",".$r->user_id;
	if ( bp_has_members( 'type=active&include=' . $ids ) ) : 
		$x+=1;?>
			<div class="avatar-block">
				<div class="ribbon-case"><span class="ribbon-fold"></span><h4 class="<?php echo $class ?>"><?php echo $title ?></h4></div>
				<?php while ( bp_members() ) : bp_the_member(); ?>
					<div class="cuny-member">
						<div class="item-avatar">
							<a href="<?php bp_member_permalink() ?>"><?php bp_member_avatar($avatar_args) ?></a>
						</div>
						<div class="cuny-member-info">
							<a href="<?php bp_member_permalink() ?>"><?php bp_member_name() ?></a><br />
							<?php bp_member_last_active() ?>
						</div>
					</div>
				<?php endwhile; ?>
					<div style="clear:both"></div>
			</div>
		<?php endif;

}

function cuny_home_square($type,$last){
	global $wpdb, $bp;
	$ids="9999999";
	if($type!="blog"){
	 //$rs = $wpdb->get_results( "SELECT group_id FROM {$bp->groups->table_name_groupmeta} where meta_key='wds_group_type' and meta_value='".$type."' ORDER BY RAND() LIMIT 1" );
	  //$sql="SELECT a.group_id,b.content FROM {$bp->groups->table_name_groupmeta} a, {$bp->activity->table_name} b where a.group_id=b.item_id and a.meta_key='wds_group_type' and a.meta_value='".ucfirst($type)."' or a.group_id=b.item_id and a.meta_key='wds_group_type' and a.meta_value='".strtolower($type)."' ORDER BY b.date_recorded desc LIMIT 1";
	   $sql="SELECT a.group_id,b.content FROM {$bp->groups->table_name_groupmeta} a, {$bp->activity->table_name} b, {$bp->groups->table_name} c where a.group_id=c.id and c.status='public' and a.group_id=b.item_id and a.meta_key='wds_group_type' and a.meta_value='".ucfirst($type)."' or a.group_id=c.id and c.status='public' and a.group_id=b.item_id and a.meta_key='wds_group_type' and a.meta_value='".strtolower($type)."' ORDER BY b.date_recorded desc LIMIT 1";
	  //echo $sql;
	  $rs = $wpdb->get_results($sql);
	  foreach ( (array)$rs as $r ){
		  $activity=$r->content;
		  $ids.= ",".$r->group_id;
	  }
	  //echo $ids;
	  
	  if ( bp_has_groups( 'include='.$ids.'&per_page=1&max=1' ) ) : 
		  while ( bp_groups() ) : bp_the_group();
		global $groups_template;
		$group = $groups_template->group;
 
			 echo '<div class="box-1 '.$last.'" >';
			  echo '<h3 class="title"><a href="'.site_url().'/'.strtolower($type).'s/">'.ucfirst($type).'s</a></h3>';
			  echo '<h2 class="green-title"><a href="'.bp_get_group_permalink().'">'.bp_get_group_name().'</a></h2>';
			  ?>
              <div class="byline"><?php printf( __( 'active %s ago', 'buddypress' ), bp_get_group_last_active() ) ?></div>
              <?php
			  //echo '<div class="byline">Author Name | Date</div>';
			  if(!$activity){
				 $activity=stripslashes($group->description); 
			  }
			  
			  echo substr($activity, 0, 135).'&hellip; (<a href="'.bp_get_group_permalink().'">View More</a>)';
			  echo '</div>';
		  endwhile; 
	  endif;
	}else{
		if ( bp_has_blogs( 'max=1' ) ) :
			while ( bp_blogs() ) : bp_the_blog();
			global $blogs_template;
				echo '<div class="box-1 '.$last.'" >';
				echo '<h3 class="title"><a href="'.site_url().'/sites/">Sites</a></h3>';
				echo '<h2 class="green-title"><a href="'.bp_get_blog_permalink().'">'.bp_get_blog_name().'</a></h2>';
				//echo '<div class="byline">Author Name | Date</div>';
				?>
                <div class="byline"><?php bp_blog_last_active() ?></div>
                <?php
             	switch_to_blog($blogs_template->blog->blog_id);
             	global $post;
				$query = new WP_Query( array('posts_per_page' => 1) );
				// For some reason, posts_per_page broke.
				$postcount = 0;

				  while ( $query->have_posts() ) : $query->the_post();
					if ( $postcount > 0 ) 
						break;

					 echo '<p>'.substr(strip_tags($post->post_content),  0, 135).'&hellip; (<a href="'.bp_get_blog_permalink().'">View More</a>)</p>';

					$postcount++;
				endwhile;	

				restore_current_blog();

				echo '</div>';
			endwhile;
		endif;	
	}
}

function cuny_home_four_square() {
	cuny_home_square('course','');
	cuny_home_square('club','last');
	cuny_home_square('project','');
	cuny_home_square('blog','last');
}

genesis();