<?php

/**
 * Adds 'local environment' tab
 */
function cuny_local_env_flag() {
	if ( defined( 'IS_LOCAL_ENV' ) && IS_LOCAL_ENV ) {
		?>

		<style type="text/css">
			#local-env-flag {
				position: fixed;
				left: 0;
				top: 5px;
				width: 150px;
				padding: 10px 15px;
				text-align: center;
				background: #600;
				color: #fff;
				font-size: 1em;
				line-height: 1.8em;
				border: 2px solid #666;
				z-index: 1000;
				opacity: 0.7;
			}
		</style>

		<div id="local-env-flag">
			LOCAL ENVIRONMENT
		</div>

		<?php
	}
}
add_action( 'wp_footer', 'cuny_local_env_flag' );
add_action( 'admin_footer', 'cuny_local_env_flag' );

add_action('wp_enqueue_scripts','wds_jquery');
function wds_jquery() {
		wp_enqueue_script('jquery');
}

add_action('wp_print_styles', 'cuny_site_wide_navi_styles');
function cuny_site_wide_navi_styles() {
	global $blog_id;
	$sw_navi_styles = WPMU_PLUGIN_URL . '/css/sw-navi.css';

	if ( $blog_id == 1 )
		return;

	wp_register_style( 'SW_Navi_styles', $sw_navi_styles );
	wp_enqueue_style( 'SW_Navi_styles' );
}

add_action('wp_head', 'cuny_login_popup_script');
function cuny_login_popup_script() {
	?>
	<script type="text/javascript">
	jQuery(document).ready(function(){
		var cpl = jQuery('#cuny-popup-login');
		jQuery("#popup-login-link").show();
		jQuery(cpl).hide();
		
		jQuery("#popup-login-link").click(function(){
			if ( 'none' == jQuery(cpl).css('display') ) {
				jQuery(cpl).show();
				jQuery("#sidebar-user-login").focus();
			} else {
				jQuery(cpl).hide();
			}
			
			return false;
		});

		jQuery(".close-popup-login").click(function(){
			jQuery(cpl).hide();
		});
	});
	</script>
	<?php

}

add_action( 'wp_head', 'cuny_site_wide_google_font');
function cuny_site_wide_google_font() {
	echo "<link href='http://fonts.googleapis.com/css?family=Arvo' rel='stylesheet' type='text/css'>";
}

add_action('cuny_bp_profile_menus', 'cuny_bp_profile_menu');
function cuny_bp_profile_menu() {
	 global $bp;
	 //print_r($bp);
	 	if ( !is_user_logged_in() )
		return;

	      //echo '<pre>';
	      	//print_r($bp);
	      //echo '</pre>';
	 ?>
<ul class="main-nav">

	<li class="sq-bullet <?php if ( strpos($_SERVER['REQUEST_URI'],"members")
			                      &&
		              !strpos($_SERVER['REQUEST_URI'],"friends")
			                      &&
		              !strpos($_SERVER['REQUEST_URI'],"messages")) {
		                    echo ' selected-page'; }
		    ?>" id="bp-adminbar-account-menu"><a href="<?php echo bp_loggedin_user_domain() ?>">Profile</a>
    	<ul>
        <?php
        //check to see if the user is viewing their own profile while logged in
        //if so - profile edit controls are displayed
        if ( is_user_logged_in() && bp_is_my_profile() ){
        $link = $bp->loggedin_user->domain."settings/"; ?>
		<li><a id="bp-admin-settings" href="<?php echo bp_displayed_user_domain() . bp_get_settings_slug(); ?>">Settings</a>
			<ul>
		<li><a href="<?php echo $bp->loggedin_user->domain; ?>settings/general">General</a></li>
		<li><a href="<?php echo $bp->loggedin_user->domain; ?>settings/notifications">Notifications</a></li>
		<li><a href="<?php echo bp_displayed_user_domain() . bp_get_settings_slug() . '/delete-account/'; ?>">Delete Account</a></li>
			</ul>
		<?php echo '</li>'; ?>
		<li><a href="<?php echo bp_displayed_user_domain(). 'profile/edit/'; ?>">Edit Profile</a></li>
		<li><a href="<?php echo bp_displayed_user_domain(). 'profile/change-avatar/'; ?>">Change Avatar</a></li>
		<li><a href="<?php echo bp_displayed_user_domain(). 'invite-anyone/'; ?>">Send Invites</a>
			<ul>
				<li><a href="<?php echo bp_displayed_user_domain(). 'invite-anyone/sent-invites/'; ?>">Sent</a></li>
			</ul>		
		</li>
		<?php }//if user_logged_in ?>
        </ul>

    </li>
	
	<li class="sq-bullet <?php if ( is_page('my-courses') ) { echo ' selected-page'; } ?>"><a href="<?php echo $bp->root_domain ?>/my-courses/">Courses</a><ul>
<?php
        /*if ( !$friend_ids = wp_cache_get( 'cuny_course_ids_' . $bp->loggedin_user->id, 'bp' ) ) {
            $course_info = wds_get_by_meta( 5, null, $bp->loggedin_user->id, false, false, 'wds_group_type', 'Course');
            wp_cache_set( 'cuny_course_ids_' . $bp->loggedin_user->id, $course_info, 'bp' );
	      }

	      $course_info = isset( $course_info['groups'] ) ? $course_info['groups'] : array();
	       if(count( $course_info )>0){
	      	for ( $i = 0; $i < count( $course_info ); $i++ ) {
	      		echo '<li>';
	      			$groups_slug = groups_get_group(array( 'group_id' => $course_info[$i]->id))->slug;
	      			$groups_name = groups_get_group(array( 'group_id' => $course_info[$i]->id))->name;
	      			echo '<a href="' . $bp->root_domain .'/groups/' . $groups_slug .'">' . $groups_name .'</a>';
	      		echo '</li>';
	      	}
		 }else{
			 echo "<li>You do not have any courses.</li>";
		  }*/ ?>
          
          <li class="active-submenu"><a href="<?php echo bp_displayed_user_domain(). 'my-courses/'; ?>">Active</a> | </li>
          <li class="active-submenu"><a href="<?php echo bp_displayed_user_domain(). 'my-courses/'; ?>">Inactive</a> | </li>
          <li class="active-submenu"><a href="<?php echo bp_displayed_user_domain(). 'my-courses/'; ?>">All</a></li>
		
		  <li>	
	      <?php $faculty = xprofile_get_field_data( 'Account Type', get_current_user_id() );
		  if ( is_super_admin( get_current_user_id() ) || $faculty == "Faculty" ) {
			  ?>
			 <a href="<?php echo bp_get_root_domain() . '/' . BP_GROUPS_SLUG . '/create/step/group-details/?type=course&new=true' ?>"><?php _e( '+ Create a Course', 'buddypress' ) ?></a>
	      <?php } ?>
          </ul></li>

	<li class="sq-bullet <?php if ( is_page('my-projects') ) { echo ' selected-page'; } ?>"><a href="<?php echo $bp->root_domain ?>/my-projects/">Projects</a><ul>
<?php
        /*if ( !$project_ids = wp_cache_get( 'cuny_project_ids_' . $bp->loggedin_user->id, 'bp' ) ) {
            $project_info = wds_get_by_meta( 5, null, $bp->loggedin_user->id, false, false, 'wds_group_type', 'Project');
            wp_cache_set( 'cuny_project_ids_' . $bp->loggedin_user->id, $project_ids, 'bp' );
		}

	      $project_info = isset( $project_info['groups'] ) ? $project_info['groups'] : array();
	      if(count( $project_info )>0){
		  //print_r($project_info);
	      	for ( $i = 0; $i < count( $project_info ); $i++ ) {
	      		echo '<li>';
	      			$project_slug = groups_get_group(array( 'group_id' => $project_info[$i]->id))->slug;
	      			$project_name = groups_get_group(array( 'group_id' => $project_info[$i]->id))->name;
	      			echo '<a href="' . $bp->root_domain .'/groups/' . $project_slug .'">' . $project_name .'</a>';
	      		echo '</li>';
	      	}
	      }else{
			 echo "<li>You do not have any projects.</li>";
		  }
	      */ ?>
      
	      <li class="active-submenu"><a href="<?php echo bp_get_root_domain() . '/projects/' ?>"><?php _e( 'Active', 'buddypress' ) ?></a> | </li>
	      <li class="active-submenu"><a href="<?php echo bp_get_root_domain() . '/projects/' ?>"><?php _e( 'Inactive', 'buddypress' ) ?></a> | </li>
	      <li class="active-submenu"><a href="<?php echo bp_get_root_domain() . '/projects/' ?>"><?php _e( 'All', 'buddypress' ) ?></a></li>
          <li><a href="<?php echo bp_get_root_domain() . '/' . BP_GROUPS_SLUG . '/create/step/group-details/?type=project&new=true' ?>">+ <?php _e( 'Create a Project', 'buddypress' ) ?></a></li>
	      </ul></li>
	<li class="sq-bullet <?php if ( is_page('my-clubs') ) { echo ' selected-page'; } ?>"><a href="<?php echo $bp->root_domain ?>/my-clubs/">Clubs</a><ul>
<?php
        /*if ( !$friend_ids = wp_cache_get( 'cuny_course_ids_' . $bp->loggedin_user->id, 'bp' ) ) {
            $course_info = wds_get_by_meta( 5, null, $bp->loggedin_user->id, false, false, 'wds_group_type', 'club');
            wp_cache_set( 'cuny_course_ids_' . $bp->loggedin_user->id, $course_info, 'bp' );
		}

	      $course_info = isset( $course_info['groups'] ) ? $course_info['groups'] : array();
	      if(count( $course_info )>0){
	      	for ( $i = 0; $i < count( $course_info ); $i++ ) {
	      		echo '<li>';
	      			$groups_slug = groups_get_group(array( 'group_id' => $course_info[$i]->id))->slug;
	      			$groups_name = groups_get_group(array( 'group_id' => $course_info[$i]->id))->name;
	      			echo '<a href="' . $bp->root_domain .'/groups/' . $groups_slug .'">' . $groups_name .'</a>';
	      		echo '</li>';
	      	}
	      }else{
				echo "<li>You do not have any clubs.</li>";
		  }*/
	      ?>
	      
	      <li class="active-submenu"><a href="<?php echo bp_get_root_domain() . '/clubs/' ?>"><?php _e( 'Active', 'buddypress' ) ?></a> | </li>
	      <li class="active-submenu"><a href="<?php echo bp_get_root_domain() . '/clubs/' ?>"><?php _e( 'Inactive', 'buddypress' ) ?></a> | </li>
	      <li class="active-submenu"><a href="<?php echo bp_get_root_domain() . '/clubs/' ?>"><?php _e( 'All', 'buddypress' ) ?></a></li>
          <li><a href="<?php echo bp_get_root_domain() . '/' . BP_GROUPS_SLUG . '/create/step/group-details/?type=club&new=true' ?>">+ <?php _e( 'Create a Club', 'buddypress' ) ?></a></li>
	      </ul></li>
	      
	      <?php if ( is_super_admin( get_current_user_id() ) || $faculty == "Faculty" ) { ?>
			 <li class="sq-bullet <?php if ( is_page('my-sites') ) { echo ' selected-page'; } ?>"><a href="<?php echo $bp->root_domain ?>/my-sites/">Sites</a>
    	<ul>
        	<?php /*if ( bp_has_blogs('user_id='.$bp->loggedin_user->id) ) :
			  while ( bp_blogs() ) : bp_the_blog(); ?>
			  	<li>
	      			<a href="<?php bp_blog_permalink() ?>"><?php bp_blog_name() ?></a>
	      		<ul><li><a href="<?php bp_blog_permalink() ?>wp-admin">Dashboard</a></li>
	      			<li><a href="<?php bp_blog_permalink() ?>wp-admin/post-new.php">New Post</a></li>
	      			<li><a href="<?php bp_blog_permalink() ?>wp-admin/edit.php">Manage Posts</a></li>
	      			<li><a href="<?php bp_blog_permalink() ?>wp-admin/edit-comments.php">Manage Comments</a></li></ul></li>
			  <?php endwhile;
			endif; */?>
        </ul>
    </li>
    	 <li class="sq-bullet <?php if ( strpos($_SERVER['REQUEST_URI'],"friends") ) { echo ' selected-page'; } ?>"><a href="<?php echo $bp->loggedin_user->domain . $bp->friends->slug ?>">Friends</a>
<!--	<ul> -->
<?php
/*
        if ( !$friend_ids = wp_cache_get( 'friends_friend_ids_' . $bp->loggedin_user->id, 'bp' ) ) {
            $friend_ids = BP_Friends_Friendship::get_random_friends( $bp->loggedin_user->id );
            wp_cache_set( 'friends_friend_ids_' . $bp->loggedin_user->id, $friend_ids, 'bp' );
	      }

*/
?>
            <?php //if ( $friend_ids ) { ?>



              <?php //for ( $i = 0; $i < count( $friend_ids ); $i++ ) { ?>
<!--
                <li>
                  <?php //echo bp_core_get_userlink($friend_ids[$i]) ?>
                </li>
-->
              <?php //} ?>


            <?php //} else { ?>
<!--
		      <li><?php //bp_word_or_name( __( "You haven't connected with any friends.", 'buddypress' ), __( "%s hasn't created any friend connections yet.", 'buddypress' ) ) ?></li>
			  <hr />
              <li><a href="<?php //echo bp_get_root_domain() . '/people/' ?>">+ <?php //_e( 'Add a Friend', 'buddypress' ) ?></a></li>
-->
          <?php //} ?>

<!-- </ul> -->
	</li>
	      <?php } ?>
	      
	<li class="sq-bullet <?php if ( strpos($_SERVER['REQUEST_URI'],"messages") ) { echo ' selected-page'; } ?>"><a href="<?php echo bp_loggedin_user_domain() ?>messages/">Messages</a>
    	<ul>
    	<?php 	if ( $notifications = bp_core_get_notifications_for_user( $bp->loggedin_user->id ) ) { ?>
		<?php echo '<li>Notices <span>(' . count( $notifications ) ?>)</span><ul><?php

			if ( $notifications ) {
				$counter = 0;
				for ( $i = 0; $i < count($notifications); $i++ ) {
					$alt = ( 0 == $counter % 2 ) ? ' class="alt"' : ''; ?>

					<li<?php echo $alt ?>><?php echo $notifications[$i] ?></li>

					<?php $counter++;
				}
				?> </ul></li>
			<?php } else { ?>

				<li><a href="<?php echo $bp->loggedin_user->domain ?>"><?php _e( 'Notices(0)', 'buddypress' ); ?></a></li>

			<?php
			} ?>

		<?php
			} else { ?>
			<li><a href="<?php echo $bp->loggedin_user->domain ?>"><?php _e( 'Notices(0)', 'buddypress' ); ?></a></li>
			<?php } ?>
        <?php
        	/*$sub_counter = 0;
		foreach( (array)$bp->bp_options_nav['messages'] as $subnav_item ) {
			$link = bp_displayed_user_id() ? str_replace( $bp->displayed_user->domain, $bp->loggedin_user->domain, $subnav_item['link'] ) : $subnav_item['link'];
			$name = bp_displayed_user_id() ? str_replace( $bp->displayed_user->userdata->user_login, $bp->loggedin_user->userdata->user_login, $subnav_item['name'] ) : $subnav_item['name'];
			$alt = ( 0 == $sub_counter % 2 ) ? ' class="alt"' : '';
			echo '<li' . $alt . '><a id="bp-admin-' . $subnav_item['css_id'] . '" href="' . $link . '">' . $name . '</a></li>';
			$sub_counter++;
		}*/
		?>
		<li><a href="<?php echo bp_displayed_user_domain(). 'messages/inbox/'; ?>">Inbox</a></li>
			<li><a href="<?php echo bp_displayed_user_domain(). 'messages/inbox/'; ?>">&nbsp;&nbsp;Unread</a></li>
			<li><a href="<?php echo bp_displayed_user_domain(). 'messages/inbox/'; ?>">&nbsp;&nbsp;Read</a></li>
		<li><a href="<?php echo bp_displayed_user_domain(). 'messages/sentbox/'; ?>">Sent</a></li>
		<li><a href="<?php echo bp_displayed_user_domain(). 'messages/compose/'; ?>">Compose</a></li>
		<li><a href="<?php echo bp_displayed_user_domain(). 'messages/trash/'; ?>">Trash</a></li>
        </ul>
    </li>

	</ul></li>
	</ul>
<?php }

add_action('init','wds_search_override',1);
function wds_search_override(){
	if(isset($_POST['search-submit']) && $_POST['search-terms']){
		if($_POST['search-which']=="members"){
			wp_redirect('http://openlab.citytech.cuny.edu/people/?search='.$_POST['search-terms']);
			exit();
		}elseif($_POST['search-which']=="courses"){
			wp_redirect('http://openlab.citytech.cuny.edu/courses/?search='.$_POST['search-terms']);
			exit();
		}elseif($_POST['search-which']=="projects"){
			wp_redirect('http://openlab.citytech.cuny.edu/projects/?search='.$_POST['search-terms']);
			exit();
		}elseif($_POST['search-which']=="clubs"){
			wp_redirect('http://openlab.citytech.cuny.edu/clubs/?search='.$_POST['search-terms']);
			exit();
		}
	}
}

function cuny_site_wide_bp_search() { ?>
	<form action="<?php echo bp_search_form_action() ?>" method="post" id="search-form">
		<input type="text" id="search-terms" name="search-terms" value="" />
		<?php //echo bp_search_form_type_select() ?>
        <select style="width: auto" id="search-which" name="search-which">
        <option value="members">People</option>
        <option value="courses">Courses</option>
        <option value="projects">Projects</option>
        <option value="clubs">Clubs</option>
        <option value="blogs">Sites</option>
        </select>

		<input type="submit" name="search-submit" id="search-submit" value="<?php _e( 'Search', 'buddypress' ) ?>" />
		<?php wp_nonce_field( 'bp_search_form' ) ?>
	</form><!-- #search-form -->
<?php }


add_action('wp_footer', 'cuny_site_wide_header');
function cuny_site_wide_header() {
	global $blog_id;

	if ( $blog_id == 1 )
		return;


?>

<div id="cuny-sw-header">
	<div id="cuny-sw-header-wrap">
	<?php switch_to_blog(1) ?>
		<a href="<?php echo get_bloginfo('url') ?>" id="cuny-sw-logo"></a>
	<?php restore_current_blog() ?>
		<div class="alignright">
		<div>
		<ul class="cuny-navi">
			<?php cuny_bp_adminbar_menu(); ?>
		</ul>
		</div>
		</div>
	</div>
</div>
<?php }

function cuny_bp_adminbar_menu(){ ?>
	<div id="wp-admin-bar">
    	<ul id="wp-admin-bar-menus">
        	<?php //the admin bar items are in "reverse" order due to the right float ?>
        	<li id="login-logout" class="sub-menu user-links admin-bar-last">
            	<?php if ( is_user_logged_in() ) { ?>
                	<a href="<?php echo wp_logout_url( bp_get_root_domain() ) ?>"><?php _e( 'Log Out', 'buddypress' ) ?></a>
                <?php } else { ?>
                	<a href="<?php echo wp_login_url( bp_get_root_domain() ) ?>"><?php _e( 'Log In', 'buddypress' ) ?></a>
                <?php } ?>
            </li>
            <?php cuny_myopenlab_menu(); ?>
        	<li id="openlab-menu" class="sub-menu"><span class="bold">Open</span>Lab
            <?php //switch to the root site to get the wp-nav menu
                  switch_to_blog(1) ?>	
            <?php $args = array(
				'theme_location' => 'main',
				'container' => '',
				'menu_class' => 'nav',
			);
			//main menu for top bar
			wp_nav_menu( $args ); ?>
			<?php restore_current_blog();  ?>
            </li><!--openlab-menu-->
            <li class="clearfloat"></li>
        </ul><!--wp-admin-bar-menus--> 
    </div><!--wp-admin-bar-->
<?php }//end cuny_adminbar_menu

//myopenlab menu function
function cuny_myopenlab_menu(){
    global $bp; ?>
        	<?php if ( is_user_logged_in() ) { ?>
        	<li id="myopenlab-menu" class="sub-menu">My OpenLab          
			<ul id="my-bar">
            	<li><a href="<?php echo $bp->loggedin_user->domain; ?>">My Profile</a></li>
                <li><a href="<?php echo bp_get_root_domain(); ?>/my-courses">My Courses</a></li>
                <li><a href="<?php echo bp_get_root_domain(); ?>/my-projects">My Projects</a></li>
                <li><a href="<?php echo bp_get_root_domain(); ?>/my-clubs">My Clubs</a></li>
                <li><a href="<?php echo bp_get_root_domain(); ?>/my-blogs">My Blogs</a></li>
                <li><a href="<?php echo $bp->loggedin_user->domain; ?>/friends">My Friends</a></li>
                <li><a href="<?php echo $bp->loggedin_user->domain; ?>/messages">My Messages</a></li>
            </ul><!--my-bar-->
            </li><!--myopenlab-menu-->
            <?php } else { ?>
            	<li id="register" class="sub-menu user-links">
            		<a href="<?php site_url(); ?>/register/">Register</a>
           		</li>
            <?php } ?>

<?php }//header mods

//we may be able to deprecate this function - need to look into it
function cuny_site_wide_navi($args = '') {
global $bp, $wpdb;

switch_to_blog(1);
	$site=site_url();
restore_current_blog();
$departments_tech=array('Advertising Design and Graphic Arts','Architectural Technology','Computer Engineering Technology','Computer Systems Technology','Construction Management and Civil Engineering Technology','Electrical and Telecommunications Engineering Technology','Entertainment Technology','Environmental Control Technology','Mechanical Engineering Technology');
$departments_studies=array('Business','Career and Technology Teacher Education','Dental Hygiene','Health Services Administration','Hospitality Management','Human Services','Law and Paralegal Studies','Nursing','Radiologic Technology and Medical Imaging','Restorative Dentistry','Vision Care Technology');
$departments_arts=array('African-American Studies','Biological Sciences','Chemistry','English','Humanities','Library','Mathematics','Physics','Social Science');

$pos = strpos($site,"openlabdev");
if (!($pos === false)) {
		echo "<div style='text-align:center;width:300px;background-color:red;color:white;font-weight:bold;'>T E S T&nbsp;&nbsp;&nbsp;&nbsp;S I T E</div>";
}
?>

<ul class="menu" id="menu-main-menu"><li class="menu-item<?php if ( is_home() ) { echo ' selected-page'; } ?>"><a href="<?php echo $site;?>">Home</a></li>
	<li id="menu-item-people" class="menu-item<?php if ( is_page('people') ) { echo ' selected-page'; } ?>"><a href="<?php echo $site;?>/people/">People</a>
	<ul class="sub-menu">
		<li class="menu-item"><a href="<?php echo $site;?>/people/faculty/">Faculty</a></li>
		<li class="menu-item"><a href="<?php echo $site;?>/people/students/">Students</a></li>
		<li class="menu-item"><a href="<?php echo $site;?>/people/staff/">Staff</a></li>
	</ul>
	</li>
	<li class="menu-item<?php if ( is_page('courses') ) { echo ' selected-page'; } ?>" id="menu-item-40"><a href="<?php echo $site;?>/courses/">Courses</a>
    	<ul class="sub-menu">
   			<li ><a href="<?php echo $site."/courses/?school=tech"; ?>">School of Technology &amp; Design</a>
   				<ul class="sub-menu">
    			<?php foreach ($departments_tech as $i => $value) {?>
					<li><a href="<?php echo $site."/courses/?school=tech"; ?>&department=<?php echo str_replace(" ","-",strtolower($value)); ?>"><?php echo $value; ?></a></li>
				<?php }?>
                </ul>
            </li>
            <li id="menu-item-91"><a href="<?php echo $site."/courses/?school=studies"; ?>">School of Professional Studies</a>
   				<ul class="sub-menu">
    			<?php foreach ($departments_studies as $i => $value) {?>
					<li><a href="<?php echo $site."/courses/?school=studies"; ?>&department=<?php echo str_replace(" ","-",strtolower($value)); ?>"><?php echo $value; ?></a></li>
				<?php }?>
                </ul>
            </li>
            <li id="menu-item-93"><a href="<?php echo $site."/courses/?school=arts"; ?>">School of Arts &amp; Sciences</a>
   				<ul class="sub-menu">
    			<?php foreach ($departments_arts as $i => $value) {?>
					<li><a href="<?php echo $site."/courses/?school=arts"; ?>&department=<?php echo str_replace(" ","-",strtolower($value)); ?>"><?php echo $value; ?></a></li>
				<?php }?>
                </ul>
            </li>
    	</ul>
    </li>
	<li  id="menu-item-projects" class="menu-item<?php if ( is_page('projects') ) { echo ' selected-page'; } ?>"><a href="<?php echo $site;?>/projects/">Projects</a></li>
	<li id="menu-item-clubs" class="menu-item<?php if ( is_page('clubs') ) { echo ' selected-page'; } ?>"><a href="<?php echo $site;?>/clubs/">Clubs</a></li>
	<li id="menu-item-sites" class="menu-item<?php if ( is_page('all-sites') ) { echo ' selected-page'; } ?>"><a href="<?php echo $site;?>/all-sites/">Sites</a></li>
	<li id="menu-item-help" class="menu-item<?php if ( is_page('help') ) { echo ' selected-page'; } ?>"><a href="<?php echo $site;?>/support/help">Help</a>
	<ul class="sub-menu">
		<li class="menu-item"><a href="<?php echo $site;?>/support/about-city-tech-elab/">About City Tech OpenLab</a></li>
		<li class="menu-item"><a href="<?php echo $site;?>/support/contact-us/">Contact Us</a></li>
		<li class="menu-item"><a href="<?php echo $site;?>/support/privacy-policy/">Privacy Policy</a></li>
		<li class="menu-item"><a href="<?php echo $site;?>/support/terms-of-service/">Terms of Service</a></li>
		<li class="menu-item"><a href="<?php echo $site;?>/support/image-credits/">Image Credits</a></li>
		<li class="menu-item"><a href="<?php echo $site;?>/support/help/">Help</a></li>
		<li class="menu-item"><a href="<?php echo $site;?>/support/faq/">FAQ</a></li>
	</ul>
	</li>
	<?php if($bp->loggedin_user->id){ ?>
		<li class="menu-item"><a href="<?php echo wp_logout_url( home_url() ) ?>">Log Out</a></li>
	<?php }else { ?>
		<li class="menu-item"><a id="popup-login-link" href="#">Log In</a>
			<div id="cuny-popup-login" class="popup-login-wrap" style="display:none">
				<div class="popup-login-content">

						<form name="login-form" id="sidebar-login-form" class="standard-form" action="<?php echo site_url( 'wp-login.php', 'login_post' ) ?>" method="post">
							<label><?php _e( 'Username', 'buddypress' ) ?>
							<?php $user_login = '' ?>
							<input type="text" name="log" id="sidebar-user-login" class="input" value="<?php echo esc_attr(stripslashes($user_login)); ?>" tabindex="1" /></label>

							<label><?php _e( 'Password', 'buddypress' ) ?>
							<input type="password" name="pwd" id="sidebar-user-pass" class="input" value="" tabindex="2" /></label>

							<div><input name="rememberme" type="checkbox" id="sidebar-rememberme" value="forever" tabindex="3" /> <?php _e( 'Keep me logged in', 'buddypress' ) ?>
							<input type="submit" name="wp-submit" id="sidebar-wp-submit" value="<?php _e('Log In'); ?>" tabindex="4" /></div>

							<?php do_action( 'bp_sidebar_login_form' ) ?>
							<input type="hidden" name="testcookie" value="1" />
						</form>
						<a class="forgot-password-link" href="<?php echo site_url('wp-login.php?action=lostpassword', 'login') ?>">Forgot Password?</a>
				</div>
			</div></li>
	<?php } ?>
</ul>

<?php }

//adds the profile sidebar to the add <group> pages

add_action('genesis_before_sidebar_widget_area', 'add_group_sidebar');
function add_group_sidebar()
{
  global $bp;
  $component =  $bp->current_component;
  $action =  $bp->current_action;
  
  if ($component == "groups" && $action = "create")
  { ?>
     <h2 class="sidebar-title">My Open Lab</h2>
     <div id="item-buttons"><?php do_action( 'cuny_bp_profile_menus' ); ?></div>
  <?php }
}

add_action('wp_footer', 'cuny_site_wide_footer');
function cuny_site_wide_footer() {
global $blog_id;
switch_to_blog(1);
$site=site_url();
restore_current_blog();
?>

<div id="cuny-sw-footer">
<div class="footer-widgets" id="footer-widgets"><div class="wrap"><div class="footer-widgets-1 widget-area"><div class="widget widget_text" id="text-4"><div class="widget-wrap">
	<div class="textwidget"><a href="http://www.citytech.cuny.edu/" target="_blank"><img src="<?php echo $site;?>/wp-content/themes/citytech/images/ctnyc-seal.png" alt="Ney York City College of Technology" border="0" /></a></div>
		</div></div>
</div><div class="footer-widgets-2 widget-area"><div class="widget widget_text" id="text-3"><div class="widget-wrap"><h4 class="widgettitle">About OpenLab</h4>
			<div class="textwidget"><p>OpenLab is an open-source, digital platform designed to support teaching and learning at New York City College of Technology (NYCCT), and to promote student and faculty engagement in the intellectual and social life of the college community.</p></div>
		</div></div>
</div><div class="footer-widgets-3 widget-area"><div class="widget menupages" id="menu-pages-4"><div class="widget-wrap"><h4 class="widgettitle">Support</h4>
<a href="<?php echo $site;?>/support/help/">Help</a> | <a href="<?php echo $site;?>/support/contact-us/">Contact Us</a> | <a href="<?php echo $site;?>/support/privacy-policy/">Privacy Policy</a> | <a href="<?php echo $site;?>/support/terms-of-service/">Terms of Service</a> | <a href="<?php echo $site;?>/support/image-credits/">Credits</a></div></div>
</div><div class="footer-widgets-4 widget-area"><div class="widget widget_text" id="text-6"><div class="widget-wrap"><h4 class="widgettitle">Share</h4>
			<div class="textwidget"><ul class="nav"><li class="rss"><a href="<?php echo $site."/activity/feed/" ?>">RSS</a></li>
            <li>
            <!-- Place this tag in your head or just before your close body tag -->
<script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>

<!-- Place this tag where you want the +1 button to render -->
<g:plusone size="small"></g:plusone>
            </li>
            </ul></div>
		</div></div>
</div>
<div class="footer-widgets-5 widget-area"><div class="widget widget_text" id="text-7"><div class="widget-wrap"><div class="textwidget"><a href="http://www.cuny.edu/" target="_blank"><img alt="City University of New York" src="<?php echo $site;?>/wp-content/uploads/2011/05/cuny-box.png" /></a></div>
		</div></div>
</div></div><!-- end .wrap --></div>
<div class="footer" id="footer"><div class="wrap"><span class="alignleft">&copy; <a href="http://www.citytech.cuny.edu/" target="_blank">New York City College of Technology</a></span><span class="alignright"><a href="http://www.cuny.edu" target="_blank">City University of New York</a></span></div><!-- end .wrap --></div>
</div>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', '47613263']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
<?php }

remove_action( 'init', 'maybe_add_existing_user_to_blog' );
add_action( 'init', 'maybe_add_existing_user_to_blog', 90 );
