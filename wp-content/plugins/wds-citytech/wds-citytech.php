<?php /*
 Plugin Name: WDS CityTech
 Plugin URI: http://citytech.webdevstudios.com
 Description: Custom Functionality for CityTech BuddyPress Site.
 Version: 1.0
 Author: WebDevStudios
 Author URI: http://webdevstudios.com
 */

include "wds-register.php";
include "wds-docs.php";

/**
 * Loading BP-specific stuff in the global scope will cause issues during activation and upgrades
 * Ensure that it's only loaded when BP is present.
 * See http://openlab.citytech.cuny.edu/redmine/issues/31
 */
function openlab_load_custom_bp_functions() {
	require ( dirname( __FILE__ ) . '/wds-citytech-bp.php' );
	require ( dirname( __FILE__ ) . '/includes/group-blogs.php' );
}
add_action( 'bp_init', 'openlab_load_custom_bp_functions' );

global $wpdb;
date_default_timezone_set('America/New_York');

function wds_add_default_member_avatar( $url = false ) {
	return WP_CONTENT_URL . "/img/bubbleavatar.jpg";
}
add_filter( 'bp_core_mysteryman_src', 'wds_add_default_member_avatar' );

function wds_default_signup_avatar ($img) {
	if ( false !== strpos( $img, 'mystery-man' ) ) {
		$img = "<img src='" . wds_add_default_member_avatar() . "' width='200' height='200'>";
	}

	return $img;
}
add_filter( 'bp_get_signup_avatar', 'wds_default_signup_avatar' );

//
//   This function creates an excerpt of the string passed to the length specified and
//   breaks on a word boundary
//
function wds_content_excerpt( $text, $text_length ) {
	return bp_create_excerpt( $text, $text_length );
}

/**
 * Following filter is to correct Forum display of time since a post was written
 */
function openlab_get_the_topic_post_time_since( $current_time ) {
	global $topic_template;

	return bp_core_time_since( $topic_template->post->post_time );
}
//add_filter( 'bp_get_the_topic_post_time_since', 'openlab_get_the_topic_post_time_since' );

/**
 * Filtering the member last active value
 */
function openlab_get_last_activity( $last_activity, $last_activity_date, $string ) {
	if ( !is_numeric( $last_activity_date ) )
		$last_activity_date = strtotime( $last_activity_date );

	if ( !$last_activity_date || empty( $last_activity_date ) )
		$last_active = __( 'not recently active', 'buddypress' );
	else
		$last_active = sprintf( $string, bp_core_time_since( $last_activity_date ) );

	return $last_active;
}
add_filter( 'bp_core_get_last_activity', 'openlab_get_last_activity', 10, 3 );

/**
 * Filtering group last active value
 */
function openlab_get_group_last_active( $last_active ) {
	global $groups_template;

	if ( empty( $group ) )
		$group =& $groups_template->group;

	$last_active = $group->last_activity;

	if ( !$last_active )
		$last_active = groups_get_groupmeta( $group->id, 'last_activity' );

	if ( empty( $last_active ) ) {
		return __( 'not yet active', 'buddypress' );
	} else {
		return bp_core_time_since( strtotime( $last_active ) );
	}
}
add_filter( 'bp_get_group_last_active', 'openlab_get_group_last_active' );

/**
 * Filtering activity value
 */
function openlab_activity_time_since( $text, $activity ) {
	return '<span class="time-since">' . bp_core_time_since( strtotime( $activity->date_recorded ) ) . '</span>';
}
add_filter( 'bp_activity_time_since', 'openlab_activity_time_since', 10, 2 );

add_action('bp_before_group_forum_topic_posts', 'wds_forum_topic_next_prev');
function wds_forum_topic_next_prev () {
    global $groups_template,
           $wpdb;
 $forum_id = groups_get_groupmeta( $groups_template->group->id, 'forum_id' );
 $topic_id = bp_get_the_topic_id();
 $group_slug = bp_get_group_slug();
 $next_topic = $wpdb->get_results("SELECT * FROM wp_bb_topics
				                 WHERE forum_id='$forum_id' AND topic_id > '$topic_id' AND topic_status='0'
						 ORDER BY topic_id ASC LIMIT 1","ARRAY_A");
 $next_topic_slug = isset( $next_topic[0]['topic_slug'] ) ? $next_topic[0]['topic_slug'] : '';
 //echo "<br />Next Topic ID: " . $next_topic[0]['topic_id'];
 $previous_topic = $wpdb->get_results("SELECT * FROM wp_bb_topics
				                 WHERE forum_id='$forum_id' AND topic_id < '$topic_id' AND topic_status='0'
						 ORDER BY topic_id DESC LIMIT 1","ARRAY_A");
 $previous_topic_slug = isset( $previous_topic[0]['topic_slug'] ) ? $previous_topic[0]['topic_slug'] : '';
 if ($previous_topic_slug != "") {
  echo "<a href='" . site_url() . "/groups/$group_slug/forum/topic/$previous_topic_slug'><<< Previous Topic &nbsp;&nbsp;&nbsp&nbsp;</a>";
 }
 if ($next_topic_slug != "") {
  echo "<a href='" . site_url() . "/groups/$group_slug/forum/topic/$next_topic_slug'> Next Topic >>></a>";
 }
/*
 echo "<br />Previous Topic ID: " . $previous_topic[0]['topic_id'];
 echo "<br />Next Topic / Previous Topic ";
 echo "<br />Forum ID: " . $forum_id;
 echo "<br />Topic ID: " . bp_get_the_topic_id();
*/
}

/**
 * On activation, copies the BP first/last name profile field data into the WP 'first_name' and
 * 'last_name' fields.
 *
 * @todo This should probably be moved to a different hook. This $last_user lookup is hackish and
 *       may fail in some edge cases. I believe the hook bp_activated_user is correct.
 */
add_action( 'bp_after_activation_page', 'wds_bp_complete_signup' );
function wds_bp_complete_signup(){
        global $bp,$wpdb,$user_ID;

       $last_user = $wpdb->get_results("SELECT * FROM wp_users ORDER BY ID DESC LIMIT 1","ARRAY_A");
//       echo "<br />Last User ID: " . $last_user[0]['ID'] . " Last Login name: " . $last_user[0]['user_login'];
	$user_id = $last_user[0]['ID'];
	$first_name= xprofile_get_field_data( 'First Name', $user_id);
	$last_name=  xprofile_get_field_data( 'Last Name', $user_id);
//	echo "<br />User ID: $user_id First : $first_name Last: $last_name";
	$update_user_first = update_user_meta($user_id,'first_name',$first_name);
	$update_user_last = update_user_meta($user_id,'last_name',$last_name);
}

//child theme privacy - if corresponding group is private or hidden restrict access to site
/*add_action('init','wds_check_blog_privacy');
function wds_check_blog_privacy(){
	global $bp, $wpdb, $blog_id, $user_ID;
	if($blog_id!=1){
		$wds_bp_group_id=get_option('wds_bp_group_id');
		if($wds_bp_group_id){
			$group = new BP_Groups_Group( $wds_bp_group_id );
			$status = $group->status;
			if($status!="public"){
				//check memeber
				if(!is_user_member_of_blog($user_ID, $blog_id)){
					echo "<center><img src='http://openlab.citytech.cuny.edu/wp-content/mu-plugins/css/images/cuny-sw-logo.png'><h1>";
					echo "This is a private website, ";
					if($user_ID==0){
						echo "please login to gain access.";
					}else{
						echo "you do not have access.";
					}
					echo "</h1></center>";
					exit();
				}
			}
		}
	}
}*/




//child theme menu filter to link to website
add_filter('wp_page_menu','my_page_menu_filter');
function my_page_menu_filter( $menu ) {
	global $bp, $wpdb;


	if (!(strpos($menu,"Home") === false)) {
	    $menu = str_replace("Site Home","Home",$menu);
	    $menu = str_replace("Home","Site Home",$menu);
	} else {
		$menu = str_replace('<div class="menu"><ul>','<div class="menu"><ul><li><a title="Site Home" href="' . site_url() . '">Site Home</a></li>',$menu);
	}
	$menu = str_replace("Site Site Home","Site Home",$menu);

	// Only say 'Home' on the ePortfolio theme
	// @todo: This will probably get extended to all sites
	$menu = str_replace( 'Site Home', 'Home', $menu );

	$wds_bp_group_id = $wpdb->get_var( $wpdb->prepare( "SELECT group_id FROM {$bp->groups->table_name_groupmeta} WHERE meta_key = 'wds_bp_group_site_id' AND meta_value = %d", get_current_blog_id() ) );

	if( $wds_bp_group_id  ){
		$group_type = ucfirst(groups_get_groupmeta($wds_bp_group_id, 'wds_group_type' ));
		$group = new BP_Groups_Group( $wds_bp_group_id, true );
		$menu = str_replace('<div class="menu"><ul>','<div class="menu"><ul><li id="group-profile-link"><a title="Site" href="' . bp_get_root_domain() . '/groups/'.$group->slug.'/">'.$group_type.' Profile</a></li>',$menu);
	}
	return $menu;
}

//child theme menu filter to link to website
add_filter( 'wp_nav_menu_items','cuny_add_group_menu_items' );
function cuny_add_group_menu_items($items) {
	if ( !bp_is_root_blog() ) {

		if((strpos($items,"Contact"))) {
		} else {
			$items = '<li><a title="Home" href="' . site_url() . '">Home</a></li>' . $items;
		}
		$items = cuny_group_menu_items() . $items;

	}

	return $items;
}
function cuny_group_menu_items() {
	global $bp, $wpdb;

	$wds_bp_group_id = $wpdb->get_var( $wpdb->prepare( "SELECT group_id FROM {$bp->groups->table_name_groupmeta} WHERE meta_key = 'wds_bp_group_site_id' AND meta_value = %d", get_current_blog_id() ) );

	if($wds_bp_group_id){
		$group_type=ucfirst(groups_get_groupmeta($wds_bp_group_id, 'wds_group_type' ));
		$group = new BP_Groups_Group( $wds_bp_group_id, true );

		$tab = '<li id="group-profile-link"><a title="Site" href="' . bp_get_root_domain() . '/groups/'.$group->slug.'/">'.$group_type.' Profile</a></li>';
		$tabs = $tab;
	} else {
		$tabs = '';
	}

	return $tabs;
}

//add breadcrumbs for buddypress pages
add_action('wp_footer','wds_footer_breadcrumbs');
function wds_footer_breadcrumbs(){
	global $bp,$bp_current;
	if( bp_is_group() ){
		$group_id=$bp->groups->current_group->id;
		$b2=$bp->groups->current_group->name;
		$group_type=groups_get_groupmeta($bp->groups->current_group->id, 'wds_group_type' );
		if($group_type=="course"){
			$b1='<a href="'.site_url().'/courses/">Courses</a>';
		}elseif($group_type=="project"){
			$b1='<a href="'.site_url().'/projects/">Projects</a>';
		}elseif($group_type=="club"){
			$b1='<a href="'.site_url().'/clubs/">Clubs</a>';
		}else{
			$b1='<a href="'.site_url().'/groups/">Groups</a>';
		}

	}
	if( !empty( $bp->displayed_user->id ) ){
		$account_type = xprofile_get_field_data( 'Account Type', $bp->displayed_user->id);
		if($account_type=="Staff"){
			$b1='<a href="'.site_url().'/people/">People</a> / <a href="'.site_url().'/people/staff/">Staff</a>';
		}elseif($account_type=="Faculty"){
			$b1='<a href="'.site_url().'/people/">People</a> / <a href="'.site_url().'/people/faculty/">Faculty</a>';
		}elseif($account_type=="Student"){
			$b1='<a href="'.site_url().'/people/">People</a> / <a href="'.site_url().'/people/students/">Students</a>';
		}else{
			$b1='<a href="'.site_url().'/people/">People</a>';
		}
		$last_name= xprofile_get_field_data( 'Last Name', $bp->displayed_user->id);
		$b2=ucfirst($bp->displayed_user->fullname);//.''.ucfirst($last_name)
	}
	if( bp_is_group() || !empty( $bp->displayed_user->id ) ){
		$breadcrumb='<div class="breadcrumb">You are here:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a title="View Home" href="http://openlab.citytech.cuny.edu/">Home</a> / '.$b1.' / '.$b2.'</div>';
		$breadcrumb=str_replace("'","\'",$breadcrumb);?>
    	<script>document.getElementById('breadcrumb-container').innerHTML='<?php echo $breadcrumb; ?>';</script>
    <?php
	}
}




//Filter bp members full name
//add_filter('bp_get_member_name', 'wds_bp_the_site_member_realname');
//add_filter('bp_member_name', 'wds_bp_the_site_member_realname');
//add_filter('bp_get_displayed_user_fullname', 'wds_bp_the_site_member_realname');
//add_filter('bp_displayed_user_fullname', 'wds_bp_the_site_member_realname');
//add_filter('bp_get_loggedin_user_fullname', 'wds_bp_the_site_member_realname' );
//add_filter('bp_loggedin_user_fullname', 'wds_bp_the_site_member_realname' );
function wds_bp_the_site_member_realname(){
	global $bp;
	global $members_template;
	$members_template->member->fullname = $members_template->member->display_name;
	$user_id=$members_template->member->id;
	$first_name= xprofile_get_field_data( 'Name', $user_id);
	$last_name= xprofile_get_field_data( 'Last Name', $user_id);
	return ucfirst($first_name)." ".ucfirst($last_name);
}


//filter names in activity
/*add_filter('bp_get_activity_action', 'wds_bp_the_site_member_realname_activity' );
add_filter('bp_get_activity_user_link', 'wds_bp_the_site_member_realname_activity' );
function wds_bp_the_site_member_realname_activity(){
	global $bp;
	global $activities_template;
	print_r($activities_template);
	$action = $activities_template->activity->action;
	echo "<hr><xmp>".$action."</xmp>";
	return $action;
	$user_id=$activities_template->activity->user_id;
	$first_name= xprofile_get_field_data( 'Name', $user_id);
	$last_name= xprofile_get_field_data( 'Last Name', $user_id);
	$activities_template->activity->user_nicename="rr";
	$link = bp_core_get_user_domain( $activities_template->activity->user_id, $activities_template->activity->user_nicename, $activities_template->activity->user_login );
	return "werwe";
}*/

//Default BP Avatar Full
if ( !defined( 'BP_AVATAR_FULL_WIDTH' ) )
define( 'BP_AVATAR_FULL_WIDTH', 225 );
if ( !defined( 'BP_AVATAR_FULL_HEIGHT' ) )
define( 'BP_AVATAR_FULL_HEIGHT', 225 );


/**
 * Don't let child blogs use bp-default or a child thereof
 *
 * @todo Why isn't this done by network disabling BP Default and its child themes?
 * @todo Why isn't BP_DISABLE_ADMIN_BAR defined somewhere like bp-custom.php?
 */
function wds_default_theme(){
	global $wpdb,$blog_id;
	if($blog_id>1){
		define('BP_DISABLE_ADMIN_BAR', true);
		$theme=get_option('template');
		if($theme=="bp-default"){
			switch_theme( "twentyten", "twentyten" );
			wp_redirect( home_url() );
			exit();
		}
	}
}
add_action( 'init', 'wds_default_theme' );

//register.php -hook for new div to show account type fields
add_action( 'bp_after_signup_profile_fields', 'wds__bp_after_signup_profile_fields' );
function wds__bp_after_signup_profile_fields(){?>
<div class="editfield"><div id="wds-account-type"></div></div>
<?php
}


add_action('wp_head', 'wds_registration_ajax' );
function wds_registration_ajax(){
	wp_print_scripts( array( 'sack' ));
	$sack='var isack = new sack("'.get_bloginfo( 'wpurl' ).'/wp-admin/admin-ajax.php");';
	$loading='<img src="'.get_bloginfo('template_directory').'/_inc/images/ajax-loader.gif">';?>
	<script type="text/javascript">
		//<![CDATA[

		//load register account type
		function wds_load_account_type(id,default_type){
			<?php echo $sack;?>
			//document.getElementById('save-pad').innerHTML='<?php echo $loading; ?>';
			if (default_type != "") {
			 selected_value = default_type;
			} else {
			   var select_box=document.getElementById(id);
			   var selected_index=select_box.selectedIndex;
			   var selected_value = select_box.options[selected_index].value;
			}

			if(selected_value!=""){
				document.getElementById('signup_submit').style.display='';
			}else{
				document.getElementById('signup_submit').style.display='none';
			}

			isack.execute = 1;
			isack.method = 'POST';
			isack.setVar( "action", "wds_load_account_type" );
			isack.setVar( "account_type", selected_value );
			isack.runAJAX();
			return true;
		}


		//]]>
	</script>
	<?php
}
add_action('bp_after_registration_submit_buttons' , 'wds_load_default_account_type');
function wds_load_default_account_type() {
 		    $return = '<script type="text/javascript">';

		    $account_type = isset( $_POST['field_7'] ) ? $_POST['field_7'] : '';
		    $type = '';
		    $selected_index = '';

		    if ($account_type == "Student" ) {
			$type = "Student";
			$selected_index = 1;
		    }
		    if ($account_type == "Faculty") {
			$type = "Faculty";
			$selected_index = 2;
		    }
		    if ($account_type == "Staff") {
			$type = "Staff";
			$selected_index = 3;
		    }

		    if ( $type && $selected_index ) {
			$return .=  'var select_box=document.getElementById(\'field_7\');';
			$return .=  'select_box.selectedIndex = ' . $selected_index . ';';
			$return .= "wds_load_account_type('field_7','$type');";
		    }
		    $return .= '</script>';
		    echo $return;

}

add_action('wp_ajax_wds_load_account_type', 'wds_load_account_type');
add_action('wp_ajax_nopriv_wds_load_account_type', 'wds_load_account_type');
function wds_load_account_type(){
	global $wpdb, $bp;
	$return='';
	$account_type = $_POST['account_type'];
	if($account_type){
		//get matching profile group_id
		$sql = "SELECT id FROM wp_bp_xprofile_groups where name='".$account_type."'";
		$posts = $wpdb->get_results($sql, OBJECT);
		if ($posts){
			foreach ($posts as $post):
				$group_id=$post->id;
			endforeach;
			$return.=wds_get_register_fields($group_id);
		}
	}else{
		$return="Please select an Account Type.";
	}
	$return=str_replace("'","\'",$return);
	die("document.getElementById('wds-account-type').innerHTML='$return'");
}

function wds_bp_profile_group_tabs() {
	global $bp, $group_name;
	if ( !$groups = wp_cache_get( 'xprofile_groups_inc_empty', 'bp' ) ) {
		$groups = BP_XProfile_Group::get( array( 'fetch_fields' => true ) );
		wp_cache_set( 'xprofile_groups_inc_empty', $groups, 'bp' );
	}
	if ( empty( $group_name ) )
		$group_name = bp_profile_group_name(false);

	for ( $i = 0; $i < count($groups); $i++ ) {
		if ( $group_name == $groups[$i]->name ) {
			$selected = ' class="current"';
		} else {
			$selected = '';
		}
		$account_type=bp_get_profile_field_data( 'field=Account Type' );
		if ( $groups[$i]->fields ){
			echo '<li' . $selected . '><a href="' . $bp->displayed_user->domain . $bp->profile->slug . '/edit/group/' . $groups[$i]->id . '">' . esc_attr( $groups[$i]->name ) . '</a></li>';
		}
	}
	do_action( 'xprofile_profile_group_tabs' );
}
//Group Stuff
add_action('wp_head', 'wds_groups_ajax');
function wds_groups_ajax(){
	global $bp;
	wp_print_scripts( array( 'sack' ));
	$sack='var isack = new sack("'.get_bloginfo( 'wpurl' ).'/wp-admin/admin-ajax.php");';
	$loading='<img src="'.get_bloginfo('template_directory').'/_inc/images/ajax-loader.gif">';?>
	<script type="text/javascript">
		//<![CDATA[
		function wds_load_group_type(id){
			<?php echo $sack;?>
			var select_box=document.getElementById(id);
			var selected_index=select_box.selectedIndex;
			var selected_value = select_box.options[selected_index].value;
			isack.execute = 1;
			isack.method = 'POST';
			isack.setVar( "action", "wds_load_group_type" );
			isack.setVar( "group_type", selected_value );
			isack.runAJAX();
			return true;
		}

		function wds_load_group_departments(id){
			<?php $group= bp_get_current_group_id();
			echo $sack;?>
			var schools="0";
			if(document.getElementById('school_tech').checked){
				schools=schools+","+document.getElementById('school_tech').value;
			}
			if(document.getElementById('school_studies').checked){
				schools=schools+","+document.getElementById('school_studies').value;
			}
			if(document.getElementById('school_arts').checked){
				schools=schools+","+document.getElementById('school_arts').value;
			}
			isack.execute = 1;
			isack.method = 'POST';
			isack.setVar( "action", "wds_load_group_departments" );
			isack.setVar( "schools", schools );
			isack.setVar( "group", "<?php echo $group;?>" );
			isack.runAJAX();
			return true;
		}
		//]]>
	</script>
	<?php
}

add_action('wp_ajax_wds_load_group_departments', 'wds_load_group_departments');
add_action('wp_ajax_nopriv_wds_load_group_departments', 'wds_load_group_departments');
function wds_load_group_departments(){
	global $wpdb, $bp;
	$group = $_POST['group'];
	$schools = $_POST['schools'];
	$schools=str_replace("0,","",$schools);
	$schools=explode(",",$schools);

	$departments_tech=array('Advertising Design and Graphic Arts','Architectural Technology','Computer Engineering Technology','Computer Systems Technology','Construction Management and Civil Engineering Technology','Electrical and Telecommunications Engineering Technology','Entertainment Technology','Environmental Control Technology','Mechanical Engineering Technology');
	$departments_studies=array('Business','Career and Technology Teacher Education','Dental Hygiene','Health Services Administration','Hospitality Management','Human Services','Law and Paralegal Studies','Nursing','Radiologic Technology and Medical Imaging','Restorative Dentistry','Vision Care Technology');
	$departments_arts=array('African-American Studies','Biological Sciences','Chemistry','English','Humanities','Library','Mathematics','Physics','Social Science');
	$departments=array();
	if(in_array("tech",$schools)){
		$departments=array_merge_recursive($departments, $departments_tech);
	}
	if(in_array("studies",$schools)){
		$departments=array_merge_recursive($departments, $departments_studies);
	}
	if(in_array("arts",$schools)){
		$departments=array_merge_recursive($departments, $departments_arts);
	}
	sort($departments);
	$wds_departments=groups_get_groupmeta($group, 'wds_departments' );
	$wds_departments=explode(",",$wds_departments);
	$return="<div style='height:100px;overflow:scroll;'>";
	foreach ($departments as $i => $value) {
		$checked="";
		if(in_array($value,$wds_departments)){
			$checked="checked";
		}
		$return.="<input type='checkbox' name='wds_departments[]' value='".$value."' ".$checked."> ".$value."<br>";
	}
	$return.="</div>";
	$return=str_replace("'","\'",$return);
	die("document.getElementById('departments_html').innerHTML='$return'");
}

add_action('init', 'wds_new_group_type');
function wds_new_group_type(){
  if( isset( $_GET['new'] ) && $_GET['new']=="true" && isset( $_GET['type'] ) ){
	  global $bp;
	  unset( $bp->groups->current_create_step );
	  unset( $bp->groups->completed_create_steps );

	  setcookie( 'bp_new_group_id', false, time() - 1000, COOKIEPATH );
	  setcookie( 'bp_completed_create_steps', false, time() - 1000, COOKIEPATH );
	  setcookie( 'wds_bp_group_type', $_GET['type'], time() + 20000, COOKIEPATH );
	  bp_core_redirect( $bp->root_domain . '/' . $bp->groups->slug . '/create/step/group-details/?type='.$_GET['type'] );
  }
}

add_action('wp_ajax_wds_load_group_type', 'wds_load_group_type');
add_action('wp_ajax_nopriv_wds_load_group_type', 'wds_load_group_type');
function wds_load_group_type($group_type){
	global $wpdb, $bp, $user_ID;
	$return='';
	if($group_type){
		$echo=true;
		$return='<input type="hidden" name="group_type" value="'.ucfirst($group_type).'">';
	}else{
		$group_type = $_POST['group_type'];
	}

	/**
	 * Active/inactive toggle
	 */
	if ( groups_get_groupmeta( bp_get_current_group_id(), 'openlab_group_active_status' ) == 'inactive' ) {
		$active_checked = '';
		$inactive_checked = ' checked="checked" ';
	} else {
		$inactive_checked = '';
		$active_checked = ' checked="checked" ';
	}

	$return .= '<p class="ol-tooltip">Will your ' . ucwords( $group_type ) . ' be in use this semester? This will help users find currently active '. ucwords( $group_type ) . 's.</p>';
	$return .= '<div id="active-toggle">* ';
	$return .= '<input type="radio" name="group_active_status" id="group_is_active" value="active" ' . $active_checked . ' /> <label for="group_is_active">Active</label>';
	$return .= '<input type="radio" name="group_active_status" id="group_is_inactive" value="inactive" ' . $inactive_checked . ' /> <label for="group_is_inactive">Inactive</label>';
	$return .= ' (required)';
	$return .= '</div>';

	// associated school/dept tooltip
	switch ( $group_type ) {
		case 'course' :
			$return .= '<p class="ol-tooltip">If your course is associated with one or more of the college’s schools or departments, please select from the checkboxes below.</p>';
			break;
		case 'project' :
			$return .= '<p class="ol-tooltip">Is your Project associated with one or more of the college\'s schools?</p>';
			break;
		case 'club' :
			$return .= '<p class="ol-tooltip">Is your Club associated with one or more of the college\'s schools?</p>';
			break;
	}

	$return.='<table>';
	$wds_group_school=groups_get_groupmeta(bp_get_current_group_id(), 'wds_group_school' );
	$wds_group_school=explode(",",$wds_group_school);
		$return.='<tr>';
            $return.='<td>School(s):';
            $return.='<td>';
			$checked="";
			if(bp_get_current_group_id() && in_array("tech",$wds_group_school)){
				$checked="checked";
			}

			if($group_type=="course"){
				$onclick='onclick="wds_load_group_departments();"';
			} else {
				$onclick = '';
			}
			$return.='<input type="checkbox" id="school_tech" name="wds_group_school[]" value="tech" '.$onclick.' '.$checked.'> Technology & Design ';
			$checked="";
			if(bp_get_current_group_id() &&in_array("studies",$wds_group_school)){
				$checked="checked";
			}
			$return.='<input type="checkbox" id="school_studies" name="wds_group_school[]" value="studies" '.$onclick.' '.$checked.'> Professional Studies ';
			$checked="";
			if(bp_get_current_group_id() &&in_array("arts",$wds_group_school)){
				$checked="checked";
			}
			$return.='<input type="checkbox" id="school_arts" name="wds_group_school[]" value="arts" '.$onclick.' '.$checked.'> Arts & Sciences ';
			$return.='</td>';

		$return.='</tr>';
	if($group_type=="course"){
		// For the love of Pete, it's not that hard to cast variables
		$wds_faculty = $wds_course_code = $wds_section_code = $wds_semester = $wds_year = $wds_course_html = '';

		if( !empty( $bp->groups->current_group->id ) ){
			$wds_faculty=groups_get_groupmeta($bp->groups->current_group->id, 'wds_faculty' );
			$wds_course_code=groups_get_groupmeta($bp->groups->current_group->id, 'wds_course_code' );
			$wds_section_code=groups_get_groupmeta($bp->groups->current_group->id, 'wds_section_code' );
			$wds_semester=groups_get_groupmeta($bp->groups->current_group->id, 'wds_semester' );
			$wds_year=groups_get_groupmeta($bp->groups->current_group->id, 'wds_year' );
			$wds_course_html=groups_get_groupmeta($bp->groups->current_group->id, 'wds_course_html' );
		}
        //$return.='<tr>';
           //$return.=' <td>Faculty:';
            //$return.='<td><input type="text" name="wds_faculty" value="'.$bp->loggedin_user->fullname.'"></td>';
        //$return.='</tr>';
		$last_name= xprofile_get_field_data( 'Last Name', $bp->loggedin_user->id);
		$return.='<input type="hidden" name="wds_faculty" value="'.$bp->loggedin_user->fullname.' '.$last_name.'">';

		$return.='<tr>';
            $return.='<td>Department(s):';
            $return.='<td id="departments_html"></td>';
        $return.='</tr>';

        	$return .= '<tr><td colspan="2"><p class="ol-tooltip">The following fields are not required, but including this information will make it easier for others to find your Course.</p></td></tr>';

		$return.='<tr>';
           $return.=' <td>Course Code:';
            $return.='<td><input type="text" name="wds_course_code" value="'.$wds_course_code.'"></td>';
        $return.='</tr>';
		$return.='<tr>';
            $return.='<td>Section Code:';
            $return.='<td><input type="text" name="wds_section_code" value="'.$wds_section_code.'"></td>';
        $return.='</tr>';
		$return.='<tr>';
            $return.='<td>Semester:';
            $return.='<td><select name="wds_semester">';
                $return.='<option value="">--select one--';
				$checked = $Spring = $Summer = $Fall = $Winter = "";

				if($wds_semester=="Spring"){
					$Spring="selected";
				}elseif($wds_semester=="Summer"){
					$Summer="selected";
				}elseif($wds_semester=="Fall"){
					$Fall="selected";
				}elseif($wds_semester=="Winter"){
					$Winter="selected";
				}
				$return.='<option value="Spring" '.$Spring.'>Spring';
                $return.='<option value="Summer" '.$Summer.'>Summer';
                $return.='<option value="Fall" '.$Fall.'>Fall';
                $return.='<option value="Winter" '.$Winter.'>Winter';
            $return.='</select></td>';
        $return.='</tr>';
		$return.='<tr>';
            $return.='<td>Year:';
            $return.='<td><input type="text" name="wds_year" value="'.$wds_year.'"></td>';
        $return.='</tr>';
		$return.='<tr>';
            $return.='<td>Additional Description/HTML:';
            $return.='<td><textarea name="wds_course_html">'.$wds_course_html.'</textarea></td>';
        $return.='</tr>';

	}elseif($group_type=="project"){

	}elseif($group_type=="club"){

	}else{
		$return="Please select a Group Type.";
	}
	$return.='</table>';
	if($group_type=="course"){
		$return.='<script>wds_load_group_departments();</script>';
	}
	if($echo){
		return $return;
	}else{
		$return=str_replace("'","\'",$return);
		die("document.getElementById('wds-group-type').innerHTML='$return'");
	}
}

add_action( 'bp_after_group_details_creation_step', 'wds_bp_group_meta');
add_action( 'bp_after_group_details_admin', 'wds_bp_group_meta');
function wds_bp_group_meta(){
	global $wpdb, $bp, $current_site, $base;

	$the_group_id = bp_is_group() ? bp_get_current_group_id() : 0;

	$group_type=groups_get_groupmeta($the_group_id, 'wds_group_type' );
	$group_school=groups_get_groupmeta($the_group_id, 'wds_group_school' );
	$group_project_type=groups_get_groupmeta($the_group_id, 'wds_group_project_type' );
	?>
    <div class="ct-group-meta">
      <?php
	  $type= isset( $_GET['type'] ) ? $_GET['type'] : groups_get_groupmeta( bp_get_new_group_id(), 'wds_group_type' );

	  if(!$type){
		  $type = isset( $_COOKIE["wds_bp_group_type"] ) ? $_COOKIE['wds_bp_group_type'] : '';
	  }

	  if(!$type || !in_array($type,array("club","project","course","school"))){
		  $type="group";
	  }
	  if($group_type!="group" && $group_type){
		  echo wds_load_group_type($group_type);?>
           <input type="hidden" name="group_type" value="<?php echo $group_type;?>" />
          <?php
		}elseif($type!="group"){
		  $group_type=$type;
		  echo wds_load_group_type($type);?>
           <input type="hidden" name="group_type" value="<?php echo $group_type;?>" />
          <?php
	  }else{?>
        <table>
        <tr>
        <td>Type:</td>
        <td><select id="group_type" name="group_type" onchange="wds_load_group_type('group_type');">
            <option value="" <?php if($group_type==""){echo "selected";}?>>--select one--
            <option value="club" <?php if($group_type=="club"){echo "selected";}?>>Club
            <option value="project" <?php if($group_type=="project"){echo "selected";}?>>Project
            <?php if(is_super_admin(get_current_user_id())){?><option value="course" <?php if($group_type=="course"){echo "selected";}?>>Course
            <option value="school" <?php if($group_type=="school"){echo "selected";}?>>School<?php } ?>
        </select></td>
        </tr>
        </table>
      <?php } ?>
      <div id="wds-group-type"></div>
      <?php //Copy Site
	  $wds_bp_group_site_id = openlab_get_site_id_by_group_id( $the_group_id );

	  if(!$wds_bp_group_site_id){
		$template="template-".strtolower($group_type);
		$blog_details = get_blog_details($template);
		?>
		<style type="text/css">
		.disabled-opt {
			opacity: .4;
		}
		</style>

		<script>
		function showHide(id)
		{
		  var style = document.getElementById(id).style
		   if (style.display == "none")
			style.display = "";
		   else
			style.display = "none";
		}

		jQuery(document).ready(function($){
			function new_old_switch( noo ) {
				var radioid = '#new_or_old_' + noo;
				$(radioid).prop('checked','checked');

				$('input.noo_radio').each(function(i,v) {
					var thisval = $(v).val();
					var thisid = '#noo_' + thisval + '_options';
					console.log($(thisid));
					if ( noo == thisval ) {
						$(thisid).removeClass('disabled-opt');
						$(thisid).find('input').each(function(index,element){
							$(element).removeProp('disabled').removeClass('disabled');
						});
						$(thisid).find('select').each(function(index,element){
							$(element).removeProp('disabled').removeClass('disabled');
						});
					} else {
						$(thisid).addClass('disabled-opt');
						$(thisid).find('input').each(function(index,element){
							$(element).prop('disabled','disabled').addClass('disabled');
						});
						$(thisid).find('select').each(function(index,element){
							$(element).prop('disabled','disabled').addClass('disabled');
						});
					}
				});

			}

			$('.noo_radio').click(function(el){
				var whichid = $(el.target).prop('id').split('_').pop();
				new_old_switch(whichid);
			});

			// setup
			new_old_switch( 'new' );
		},(jQuery));
		</script>

        	<input type="hidden" name="action" value="copy_blog" />
		<input type="hidden" name="source_blog" value="<?php echo $blog_details->blog_id; ?>" />

		<table class="form-table groupblog-setup">
			<?php if ( $group_type != "course" ) : ?>
				<?php $show_website = "none" ?>
				<tr class="form-field form-required">
					<th scope='row'>
						<input type="checkbox" name="wds_website_check" value="yes" onclick="showHide('wds-website');showHide('wds-website-existing');showHide('wds-website-tooltips');" /> Set up a site?
					</th>
				</tr>
			<?php else : ?>
		    		<?php $show_website = 'block' ?>
		    		<tr class="form-field form-required">
		    			<th>Site Details</th>
		    		</tr>
			<?php endif ?>

			<tr id="wds-website-tooltips" class="form-field form-required" style="display:<?php echo $show_website;?>"><td colspan="2">
				<?php switch ( $group_type ) :
					case 'course' : ?>
						<p class="ol-tooltip">Take a moment to consider the address for your site. You will not be able to change it once you've created it. If this Course site will be used again on the OpenLab, you may want to keep it simple. We recommend the following format:</p>

						<ul class="ol-tooltip">
							<li>FacultyLastNameCourseCode</li>
							<li>smithadv1100</li>
						</ul>

						<p class="ol-tooltip">If you plan to create a new course each semester, you may choose to add Semester and Year.</p>

						<ul class="ol-tooltip">
							<li>FacultyLastNameCourseCodeSemYear</li>
							<li>smithadv1100sp2012</li>
						</ul>

						<p class="ol-tooltip">If you teach multiple sections and plan to create additional course sites on the OpenLab, consider adding other identifying information to the URL.</p>

						<?php break;
					case 'project' : ?>
						<p class="ol-tooltip">Please take a moment to consider the address for your site. You will not be able to change it once you’ve created it.  If you are linking to an existing site, select from the drop-down menu.</p>

						<p class="ol-tooltip"><strong>Is this an ePortfolio?</strong> Since the ePortfolio is designed to be a Career Portfolio, choose a site address that will appear professional. We recommend one of the following formats (enter in the gray box below):</p>

						<ul class="ol-tooltip">
							<li>FirstNameLastName_eportfolio</li>
							<li>JaneSmith_eportfolio (Example)</li>
							<li>FirstInitialLastName_eportfolio</li>
							<li>JSmith_eportfolio (Example)</li>
						</ul>

						<?php break;
					case 'club' : ?>
						<p class="ol-tooltip">Please take a moment to consider the address for your site. You will not be able to change it once you’ve created it.  If you are linking to an existing site, select from the drop-down menu. </p>

						<?php break ?>

				<?php endswitch ?>
			</td></tr>

			<tr id="wds-website" class="form-field form-required" style="display:<?php echo $show_website;?>">
				<th valign="top" scope='row'>

					<input type="radio" class="noo_radio" name="new_or_old" id="new_or_old_new" value="new" />
					Create a new site:
				</th>

				<td id="noo_new_options">
				<?php
				if( constant( "VHOST" ) == 'yes' ) : ?>
					<input name="blog[domain]" type="text" title="<?php _e('Domain') ?>"/>.<?php echo $current_site->domain;?>
				<?php else:
					echo $current_site->domain . $current_site->path ?><input name="blog[domain]" type="text" title="<?php _e('Domain') ?>"/>
				<?php endif; ?>

				</td>
			</tr>

			<tr id="wds-website-existing" class="form-field form-required" style="display:<?php echo $show_website;?>">
				<th valign="top" scope='row'>
					<input type="radio" class="noo_radio" id="new_or_old_old" name="new_or_old" value="old" />
					Use an existing site:
				</th>

				<td id="noo_old_options">
					<?php $user_blogs = get_blogs_of_user( get_current_user_id() ) ?>

					<?php
						global $wpdb, $bp;
						$current_groupblogs = $wpdb->get_col( $wpdb->prepare( "SELECT meta_value FROM {$bp->groups->table_name_groupmeta} WHERE meta_key = 'wds_bp_group_site_id'" ) );

						foreach( $user_blogs as $ubid => $ub ) {
							if ( in_array( $ubid, $current_groupblogs ) ) {
								unset( $user_blogs[$ubid] );
							}
						}
						$user_blogs = array_values( $user_blogs );
					?>

					<select name="groupblog-blogid" id="groupblog-blogid">
						<option value="0">- Choose a site -</option>
						<?php

						foreach( (array)$user_blogs as $user_blog ) { ?>
							<option value="<?php echo $user_blog->userblog_id; ?>"><?php echo $user_blog->blogname; ?></option>
						<?php } ?>
					</select>
				</td>
			</tr>

			<tr id="wds-website-external" class="form-field form-required" style="display:<?php echo $show_website;?>">
				<th valign="top" scope='row'>
					<input type="radio" class="noo_radio" id="new_or_old_external" name="new_or_old" value="external" />
					Use an external site:
				</th>

				<td id="noo_external_options">
					<input type="text" name="external-site-url" id="external-site-url" />
				</td>
			</tr>


		</table>
   	<?php } else { ?>
   		<?php $blog_url = get_blog_option( $wds_bp_group_site_id, 'siteurl' ) ?>
   		<?php $blog_name = get_blog_option( $wds_bp_group_site_id, 'blogname' ) ?>

   		<p>This <?php echo $group_type ?> is currently associated with the site <strong><?php echo $blog_name ?></strong> (<?php echo $blog_url ?>).</p>
   	<?php } ?>
    </div>
    <?php
}



//Save Group Meta
add_action( 'groups_group_after_save', 'wds_bp_group_meta_save' );
function wds_bp_group_meta_save($group) {
	global $wpdb, $user_ID, $bp;

	if ( isset($_POST['group_type']) ) {
		groups_update_groupmeta( $group->id, 'wds_group_type', $_POST['group_type']);

		if ( 'course' == $_POST['group_type'] ) {
			$is_course = true;
		}
	}

	if ( isset($_POST['wds_faculty']) ) {
		groups_update_groupmeta( $group->id, 'wds_faculty', $_POST['wds_faculty']);
	}
	if ( isset($_POST['wds_group_school']) ) {
		$wds_group_school=implode(",",$_POST['wds_group_school']);
		groups_update_groupmeta( $group->id, 'wds_group_school', $wds_group_school);
	}
	if ( isset($_POST['wds_departments']) ) {
		$wds_departments=implode(",",$_POST['wds_departments']);
		groups_update_groupmeta( $group->id, 'wds_departments', $wds_departments);
	}
	if ( isset($_POST['wds_course_code']) ) {
		groups_update_groupmeta( $group->id, 'wds_course_code', $_POST['wds_course_code']);
	}
	if ( isset($_POST['wds_section_code']) ) {
		groups_update_groupmeta( $group->id, 'wds_section_code', $_POST['wds_section_code']);
	}
	if ( isset($_POST['wds_semester']) ) {
		groups_update_groupmeta( $group->id, 'wds_semester', $_POST['wds_semester']);
	}
	if ( isset($_POST['wds_year']) ) {
		groups_update_groupmeta( $group->id, 'wds_year', $_POST['wds_year']);
	}
	if ( isset($_POST['wds_course_html']) ) {
		groups_update_groupmeta( $group->id, 'wds_course_html', $_POST['wds_course_html']);
	}
	if ( isset($_POST['group_project_type']) ) {
		groups_update_groupmeta( $group->id, 'wds_group_project_type', $_POST['group_project_type']);
	}

	if ( isset( $_POST['group_active_status'] ) ) {
		$status = 'inactive' == $_POST['group_active_status'] ? 'inactive' : 'active';
		groups_update_groupmeta( $group->id, 'openlab_group_active_status', $status );
	}


	/*//WIKI
	if ( isset($_POST['wds_bp_docs_wiki']) && $_POST['wds_bp_docs_wiki']=="yes" ) {
		groups_update_groupmeta( $group->id, 'bpdocs', 'a:2:{s:12:"group-enable";s:1:"1";s:10:"can-create";s:6:"member";}');
	}*/

	// Site association. Non-courses have the option of not having associated sites (thus the
	// wds_website_check value).
	if ( isset( $_POST['wds_website_check'] ) || 'course' == groups_get_groupmeta( $group->id, 'wds_group_type' ) || !empty( $is_course ) ) {

		if ( isset( $_POST['new_or_old'] ) && 'new' == $_POST['new_or_old'] ) {

			// Create a new site
			ra_copy_blog_page($group->id);

		} elseif ( isset( $_POST['new_or_old'] ) && 'new' == $_POST['new_or_old'] && isset( $_POST['groupblog-blogid'] ) ) {

			// Associate an existing site
			groups_update_groupmeta( $group->id, 'wds_bp_group_site_id', (int)$_POST['groupblog-blogid'] );

		} elseif ( isset( $_POST['new_or_old'] ) && 'external' == $_POST['new_or_old'] && isset( $_POST['external-site-url'] ) ) {

			// External site

			// Some validation
			$url = openlab_validate_url( $_POST['external-site-url'] );
			groups_update_groupmeta( $group->id, 'external_site_url', $url );

			// Try to get a feed URL
			$feed_urls = openlab_find_feed_urls( $url );

			if ( isset( $feed_urls['type'] ) ) {
				groups_update_groupmeta( $group->id, 'external_site_type', $feed_urls['type'] );
			}

			if ( isset( $feed_urls['posts'] ) ) {
				groups_update_groupmeta( $group->id, 'external_site_posts_feed', $feed_urls['posts'] );
			}

			if ( isset( $feed_urls['comments'] ) ) {
				groups_update_groupmeta( $group->id, 'external_site_comments_feed', $feed_urls['comments'] );
			}
		}
	}
}

add_action("bp_group_options_nav","wds_bp_group_site_pages");
function wds_bp_group_site_pages(){
	global $bp;
	//print_r($bp);
	$site=site_url();
	$group_id=$bp->groups->current_group->id;

	$wds_bp_group_site_id=groups_get_groupmeta($group_id, 'wds_bp_group_site_id' );
	if($wds_bp_group_site_id!=""){
	  switch_to_blog($wds_bp_group_site_id);
	  $pages = get_pages(array('sort_order' => 'ASC','sort_column' => 'menu_order'));
	  echo "<ul class='website-links'>";

	  echo "<li id='site-link'><a href='".site_url()."'>".ucwords(groups_get_groupmeta( bp_get_group_id(), 'wds_group_type' ))." Site</a></li>";

	  // Only show the admin link to group members
	  if ( bp_group_is_member() ) {
		  echo "<li><a href='" . admin_url() . "'>Dashboard</a></li>";
	  }

	  echo '</ul>';
	  restore_current_blog();
	}
}

function wds_get_by_meta( $limit = null, $page = null, $user_id = false, $search_terms = false, $populate_extras = true, $meta_key = null, $meta_value = null ) {
	global $wpdb, $bp;

	if ( $limit && $page )
		$pag_sql = $wpdb->prepare( " LIMIT %d, %d", intval( ( $page - 1 ) * $limit), intval( $limit ) );
	else
		$pag_sql = '';

	if ( !is_user_logged_in() || ( !is_super_admin() && ( $user_id != $bp->loggedin_user->id ) ) )
		$hidden_sql = " AND g.status != 'hidden'";
	else
		$hidden_sql = '';

	if ( $search_terms ) {
		$search_terms = like_escape( $wpdb->escape( $search_terms ) );
		$search_sql = " AND ( g.name LIKE '%%{$search_terms}%%' OR g.description LIKE '%%{$search_terms}%%' )";
	} else {
		$search_sql = '';
	}

	if ( $user_id ) {
		$user_id = $wpdb->escape( $user_id );
		$paged_groups = $wpdb->get_results( "SELECT g.*, gm1.meta_value as total_member_count, gm2.meta_value as last_activity FROM {$bp->groups->table_name_groupmeta} gm1, {$bp->groups->table_name_groupmeta} gm2, {$bp->groups->table_name_groupmeta} gm3, {$bp->groups->table_name_members} m, {$bp->groups->table_name} g WHERE gm3.meta_key='$meta_key' AND gm3.meta_value='$meta_value' AND g.id = m.group_id AND g.id = gm1.group_id AND g.id = gm2.group_id AND g.id = gm3.group_id AND gm2.meta_key = 'last_activity' AND gm1.meta_key = 'total_member_count' {$hidden_sql} {$search_sql} AND m.user_id = {$user_id} AND m.is_confirmed = 1 AND m.is_banned = 0 ORDER BY g.name ASC {$pag_sql}" );
		$total_groups = $wpdb->get_var( "SELECT COUNT(DISTINCT m.group_id) FROM {$bp->groups->table_name_members} m LEFT JOIN {$bp->groups->table_name_groupmeta} gm ON m.group_id = gm.group_id INNER JOIN {$bp->groups->table_name} g ON m.group_id = g.id WHERE gm.meta_key = 'last_activity' {$hidden_sql} {$search_sql} AND m.user_id = {$user_id} AND m.is_confirmed = 1 AND m.is_banned = 0" );
	} else {
		$paged_groups = $wpdb->get_results( "SELECT g.*, gm1.meta_value as total_member_count, gm2.meta_value as last_activity FROM {$bp->groups->table_name_groupmeta} gm1, {$bp->groups->table_name_groupmeta} gm2, {$bp->groups->table_name_groupmeta} gm3, {$bp->groups->table_name} g WHERE gm3.meta_key='$meta_key' AND gm3.meta_value='$meta_value' AND g.id = gm1.group_id AND g.id = gm2.group_id AND g.id = gm3.group_id AND gm2.meta_key = 'last_activity' AND gm1.meta_key = 'total_member_count' {$hidden_sql} {$search_sql} ORDER BY g.name ASC {$pag_sql}" );
		$total_groups = $wpdb->get_var( "SELECT COUNT(DISTINCT g.id) FROM {$bp->groups->table_name_groupmeta} gm1, {$bp->groups->table_name_groupmeta} gm2, {$bp->groups->table_name_groupmeta} gm3, {$bp->groups->table_name} g WHERE gm3.meta_key='$meta_key' AND gm3.meta_value='$meta_value' AND g.id = gm1.group_id AND g.id = gm2.group_id AND g.id = gm3.group_id AND gm2.meta_key = 'last_activity' AND gm1.meta_key = 'total_member_count' {$hidden_sql} {$search_sql}" );
	}
//echo $total_groups;
	if ( !empty( $populate_extras ) ) {
		foreach ( (array)$paged_groups as $group ) $group_ids[] = $group->id;
		$group_ids = $wpdb->escape( join( ',', (array)$group_ids ) );
		$paged_groups = BP_Groups_Group::get_group_extras( $paged_groups, $group_ids, 'newest' );
	}

	return array( 'groups' => $paged_groups, 'total' => $total_groups );
}

//Copy the group blog template
function ra_copy_blog_page($group_id) {
	global $bp, $wpdb, $current_site, $user_email, $base, $user_ID;
	$blog = isset( $_POST['blog'] ) ? $_POST['blog'] : array();
	if( !empty( $blog['domain'] ) && $group_id){
	  $wpdb->dmtable = $wpdb->base_prefix . 'domain_mapping';
	  if(!defined('SUNRISE') || $wpdb->get_var("SHOW TABLES LIKE '{$wpdb->dmtable}'") != $wpdb->dmtable) {
		  $join = $where = '';
	  } else {
		  $join = "LEFT JOIN {$wpdb->dmtable} d ON d.blog_id = b.blog_id ";
		  $where = "AND d.domain IS NULL ";
	  }

	  $src_id = intval( $_POST['source_blog'] );

	  //$domain = sanitize_user( str_replace( '/', '', $blog[ 'domain' ] ) );
	  //$domain=str_replace(".","",$domain);
	  $domain = friendly_url($blog[ 'domain' ]);
	  $email = sanitize_email( $user_email );
	  $title = $_POST['group-name'];

	  if ( !$src_id) {
		  $msg = __('Select a source blog.');
	  } elseif ( empty($domain) || empty($email)) {
		  $msg = __('Missing blog address or email address.');
	  } elseif( !is_email( $email ) ) {
		  $msg = __('Invalid email address');
	  } else {
		  if( constant('VHOST') == 'yes' ) {
			  $newdomain = $domain.".".$current_site->domain;
			  $path = $base;
		  } else {
			  $newdomain = $current_site->domain;
			  $path = $base.$domain.'/';
		  }

		  $password = 'N/A';
		  $user_id = email_exists($email);
		  if( !$user_id ) {
			  $password = generate_random_password();
			  $user_id = wpmu_create_user( $domain, $password, $email );
			  if(false == $user_id) {
				  $msg = __('There was an error creating the user');
			  } else {
				  wp_new_user_notification($user_id, $password);
			  }
		  }
		  $wpdb->hide_errors();
		  $new_id = wpmu_create_blog($newdomain, $path, $title, $user_id , array( "public" => 1 ), $current_site->id);
		  $id=$new_id;
		  $wpdb->show_errors();
		  if( !is_wp_error($id) ) { //if it dont already exists then move over everything

			  $current_user = get_userdata( bp_loggedin_user_id() );

			  groups_update_groupmeta( $group_id, 'wds_bp_group_site_id', $id);
			  /*if( get_user_option( $user_id, 'primary_blog' ) == 1 )
				  update_user_option( $user_id, 'primary_blog', $id, true );*/
			  $content_mail = sprintf( __( "New site created by %1s\n\nAddress: http://%2s\nName: %3s"), $current_user->user_login , $newdomain.$path, stripslashes( $title ) );
			  wp_mail( get_site_option('admin_email'),  sprintf(__('[%s] New Blog Created'), $current_site->site_name), $content_mail, 'From: "Site Admin" <' . get_site_option( 'admin_email' ) . '>' );
			  wpmu_welcome_notification( $id, $user_id, $password, $title, array( "public" => 1 ) );
			  $msg = __('Site Created');
			  // now copy
			  $blogtables = $wpdb->base_prefix . $src_id . "_";
			  $newtables = $wpdb->base_prefix . $new_id . "_";
			  $query = "SHOW TABLES LIKE '{$blogtables}%'";
  //				var_dump($query);
			  $tables = $wpdb->get_results($query, ARRAY_A);
			  if($tables) {
				  reset($tables);
				  $create = array();
				  $data = array();
				  $len = strlen($blogtables);
				  $create_col = 'Create Table';
				  // add std wp tables to this array
				  $wptables = array($blogtables . 'links', $blogtables . 'postmeta', $blogtables . 'posts',
					  $blogtables . 'terms', $blogtables . 'term_taxonomy', $blogtables . 'term_relationships');
				  for($i = 0;$i < count($tables);$i++) {
					  $table = current($tables[$i]);
					  if(substr($table,0,$len) == $blogtables) {
						  if(!($table == $blogtables . 'options' || $table == $blogtables . 'comments')) {
							  $create[$table] = $wpdb->get_row("SHOW CREATE TABLE {$table}");
							  $data[$table] = $wpdb->get_results("SELECT * FROM {$table}", ARRAY_A);
						  }
					  }
				  }
  //					var_dump($create);
				  if($data) {
					  switch_to_blog($src_id);
					  $src_url = get_option('siteurl');
					  $option_query = "SELECT option_name, option_value FROM {$wpdb->options}";
					  restore_current_blog();
					  $new_url = get_blog_option($new_id, 'siteurl');
					  foreach($data as $k => $v) {
						  $table = str_replace($blogtables, $newtables, $k);
						  if(in_array($k, $wptables)) { // drop new blog table
							  $query = "DROP TABLE IF EXISTS {$table}";
							  $wpdb->query($query);
						  }
						  $key = (array) $create[$k];
						  $query = str_replace($blogtables, $newtables, $key[$create_col]);
						  $wpdb->query($query);
						  $is_post = ($k == $blogtables . 'posts');
						  if($v) {
							  foreach($v as $row) {
								  if($is_post) {
									  $row['guid'] = str_replace($src_url,$new_url,$row['guid']);
									  $row['post_content'] = str_replace($src_url,$new_url,$row['post_content']);
									  $row['post_author'] = $user_id;
								  }
								  $wpdb->insert($table, $row);
							  }
						  }
					  }
					  // copy media
					  $cp_base = ABSPATH . '/' . UPLOADBLOGSDIR . '/';
					  $cp_cmd = 'cp -r ' . $cp_base . $src_id . ' ' . $cp_base . $new_id;
					  exec($cp_cmd);
					  // update options
					  $skip_options = array('admin_email','blogname','blogdescription','cron','db_version','doing_cron',
						  'fileupload_url','home','nonce_salt','random_seed','rewrite_rules','secret','siteurl','upload_path',
						  'upload_url_path', "{$wpdb->base_prefix}{$src_id}_user_roles");
					  $options = $wpdb->get_results($option_query);
					  //new blog stuff
					  if($options) {
						  switch_to_blog($new_id);
						  update_option( "wds_bp_group_id", $group_id );
						  foreach($options as $o) {
  //								var_dump($o);
							  if(!in_array($o->option_name,$skip_options) && substr($o->option_name,0,6) != '_trans') {
								  update_option($o->option_name, maybe_unserialize($o->option_value));
							  }
						  }
						  if(version_compare( $GLOBALS['wp_version'], '2.8', '>')) {
							  set_transient('rewrite_rules', '');
						  } else {
							  update_option('rewrite_rules', '');
						  }

						  //creaTE UPLOAD DOCS PAGE
						  // Psyche!
						  /*
						  $args = array (
							  'post_title'	=>	'Upload Documents',
							  'post_content'	=>	'[lab-docs]',
							  'post_status'	=>	'publish',
							  'post_author'	=>	$user_ID,
							  'post_type'		=>	'page'
						  );
						  wp_insert_post( $args );
						  */

						  restore_current_blog();
						  $msg = __('Blog Copied');
					  }
				  }
			  }
		  } else {
			  $msg = $id->get_error_message();
		  }
	  }
	}
}

/**
 * On group creation, go back to see if a blog was created. If so, match its privacy setting.
 *
 * @see http://openlab.citytech.cuny.edu/redmine/issues/318
 */
function openlab_sync_blog_privacy_at_group_creation() {
	global $bp;

	$group_id = isset( $bp->groups->new_group_id ) ? $bp->groups->new_group_id : '';

	if ( $group_id && $site_id = groups_get_groupmeta( $group_id, 'wds_bp_group_site_id' ) ) {
		$group = groups_get_group( array( 'group_id' => $group_id ) );

		if ( 'private' == $group->status || 'hidden' == $group->status ) {
			update_blog_option( $site_id, 'blog_public', '-2' );
		}
	}
}
add_action( 'groups_create_group_step_save_group-settings', 'openlab_sync_blog_privacy_at_group_creation' );

            //this is a function for sanitizing the website name
			//source http://cubiq.org/the-perfect-php-clean-url-generator
			function friendly_url($str, $replace=array(), $delimiter='-') {
              	if( !empty($replace) ) {
              		$str = str_replace((array)$replace, ' ', $str);
              	}

              	$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
              	$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
              	$clean = strtolower(trim($clean, '-'));
              	$clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);

              	return $clean;
              }

/**
 * Don't let anyone access the Create A Site page
 *
 * @see http://openlab.citytech.cuny.edu/redmine/issues/160
 */
function openlab_redirect_from_site_creation() {
	if ( bp_is_create_blog() ) {
		bp_core_redirect( bp_get_root_domain() );
	}
}
add_action( 'bp_actions', 'openlab_redirect_from_site_creation' );

/**
 * Load custom language file for BP Group Documents
 */
load_textdomain( 'bp-group-documents', WP_CONTENT_DIR . '/languages/buddypress-group-documents-en_CAC.mo' );

/**
 * Allow super admins to change user type on Dashboard
 */
class OpenLab_Change_User_Type {
	function init() {
		static $instance;

		if ( !is_super_admin() ) {
			return;
		}

		if ( empty( $instance ) ) {
			$instance = new OpenLab_Change_User_Type;
		}
	}

	function __construct() {
		add_action( 'show_user_profile', array( $this, 'markup' ) );
		add_action( 'edit_user_profile', array( $this, 'markup' ) );

		add_action( 'personal_options_update', array( $this, 'save' ) );
		add_action( 'edit_user_profile_update', array( $this, 'save' ) );
	}

	function markup( $user ) {
		$account_type = xprofile_get_field_data( 'Account Type', $user->ID );

		$field_id = xprofile_get_field_id_from_name( 'Account Type' );
		$field = new BP_XProfile_Field( $field_id );
		$options = $field->get_children();

		?>

		<h3>OpenLab Account Type</h3>

		<table class="form-table">
			<tr>
				<th>
					<label for="openlab_account_type">Account Type</label>
				</th>

				<td>
					<?php foreach( $options as $option ) : ?>
						<input type="radio" name="openlab_account_type" value="<?php echo $option->name ?>" <?php checked( $account_type, $option->name ) ?>> <?php echo $option->name ?><br />
					<?php endforeach ?>
				</td>
			</tr>
		</table>

		<?php
	}

	function save( $user_id ) {
		if ( isset( $_POST['openlab_account_type'] ) ) {
			xprofile_set_field_data( 'Account Type', $user_id, $_POST['openlab_account_type'] );
		}
	}

}
add_action( 'admin_init', array( 'OpenLab_Change_User_Type', 'init' ) );

/**
 * Only allow the site's faculty admin to see full names on the Dashboard
 *
 * See http://openlab.citytech.cuny.edu/redmine/issues/165
 */
function openlab_hide_fn_ln( $check, $object, $meta_key, $single ) {
	global $wpdb, $bp;

	if ( is_admin() && in_array( $meta_key, array( 'first_name', 'last_name' ) ) ) {

		// Faculty only
		$account_type = xprofile_get_field_data( 'Account Type', get_current_user_id() );
		if ( 'faculty' != strtolower( $account_type ) ) {
			return '';
		}

		// Make sure it's the right faculty member
		$group_id = $wpdb->get_var( $wpdb->prepare( "SELECT group_id FROM {$bp->groups->table_name_groupmeta} WHERE meta_key = 'wds_bp_group_site_id' AND meta_value = '%d' LIMIT 1", get_current_blog_id() ) );

		if ( !empty( $group_id ) && !groups_is_user_admin( get_current_user_id(), (int)$group_id ) ) {
			return '';
		}

		// Make sure it's a course
		$group_type = groups_get_groupmeta( $group_id, 'wds_group_type' );

		if ( 'course' != strtolower( $group_type ) ) {
			return '';
		}
	}

	return $check;
}
//add_filter( 'get_user_metadata', 'openlab_hide_fn_ln', 9999, 4 );

/**
 * No access redirects should happen from wp-login.php
 */
add_filter( 'bp_no_access_mode', create_function( '', 'return 2;' ) );

/**
 * Don't auto-link items in profiles
 * Hooked to bp_screens so that it gets fired late enough
 */
add_action( 'bp_screens', create_function( '', "remove_filter( 'bp_get_the_profile_field_value', 'xprofile_filter_link_profile_data', 9, 2 );" ) );


//Change "Group" to something else
class buddypress_Translation_Mangler {
 /*
  * Filter the translation string before it is displayed.
  *
  * This function will choke if we try to load it when not viewing a group page or in a group loop
  * So we bail in cases where neither of those things is present, by checking $groups_template
  */
 function filter_gettext($translation, $text, $domain) {
   global $bp, $groups_template;

   $group_id = 0;
   if ( !bp_is_group_create() ) {
	   if ( !empty( $groups_template->group->id ) ) {
		$group_id = $groups_template->group->id;
	   } else if ( !empty( $bp->groups->current_group->id ) ) {
		$group_id = $bp->groups->current_group->id;
	   }
   }

   if ( $group_id ) {
	$grouptype = groups_get_groupmeta( $group_id, 'wds_group_type' );
   } else if ( isset( $_GET['type'] ) ) {
   	$grouptype = $_GET['type'];
   } else {
   	return $translation;
   }

   $uc_grouptype = ucfirst($grouptype);
   $translations = &get_translations_for_domain( 'buddypress' );
   switch($text){
	case "Forum":
		return $translations->translate( "Discussion" );
		break;
	case "Group Forum":
		return $translations->translate( "$uc_grouptype Discussion" );
		break;
	case "Group Forum Directory":
		return $translations->translate( "" );
		break;
	case "Group Forums Directory":
		return $translations->translate( "Group Discussions Directory" );
		break;
	case "Join Group":
		return $translations->translate( "Join Now!" );
		break;
	case "You successfully joined the group.":
		return $translations->translate( "You successfully joined!" );
		break;
	case "Recent Discussion":
		return $translations->translate( "Recent Forum Discussion" );
		break;
	case "This is a hidden group and only invited members can join.":
		return $translations->translate( "This is a hidden " . $grouptype . " and only invited members can join." );
		break;
	case "This is a private group and you must request group membership in order to join.":
		return $translations->translate( "This is a private " . $grouptype . " and you must request " . $grouptype . " membership in order to join." );
		break;
	case "This is a private group. To join you must be a registered site member and request group membership.":
		return $translations->translate( "This is a private " . $grouptype . ". To join you must be a registered site member and request " . $grouptype . " membership." );
		break;
	case "This is a private group. Your membership request is awaiting approval from the group administrator.":
		return $translations->translate( "This is a private " . $grouptype . ". Your membership request is awaiting approval from the " . $grouptype . " administrator." );
		break;
	case "said ":
		return $translations->translate( "" );
		break;
	case "Create a Group":
		return $translations->translate( "Create a " . $uc_grouptype );
		break;
  }
  return $translation;
 }
}

function openlab_launch_translator() {
	add_filter('gettext', array('buddypress_Translation_Mangler', 'filter_gettext'), 10, 4);
}
add_action( 'bp_setup_globals', 'openlab_launch_translator' );

/**
 * When a user attempts to visit a blog, check to see if the user is a member of the
 * blog's associated group. If so, ensure that the member has access.
 *
 * This function should be deprecated when a more elegant solution is found.
 * See http://openlab.citytech.cuny.edu/redmine/issues/317 for more discussion.
 */
function openlab_sync_blog_members_to_group() {
	global $wpdb, $bp;

	// No need to continue if the user is not logged in, if this is not an admin page, or if
	// the current blog is not private
	$blog_public = get_option( 'blog_public' );
	if ( !is_user_logged_in() || !is_admin() || (int)$blog_public < 0 ) {
		return;
	}

	$user_id = get_current_user_id();
	$userdata = get_userdata( $user_id );

	// Is the user already a member of the blog?
	if ( empty( $userdata->caps ) ) {

		// Is this blog associated with a group?
		$group_id = $wpdb->get_var( $wpdb->prepare( "SELECT group_id FROM {$bp->groups->table_name_groupmeta} WHERE meta_key = 'wds_bp_group_site_id' AND meta_value = %d", get_current_blog_id() ) );

		if ( $group_id ) {

			// Is this user a member of the group?
			if ( groups_is_user_member( $user_id, $group_id ) ) {

				// Figure out the status
				if ( groups_is_user_admin( $user_id, $group_id ) ) {
					$status = 'administrator';
				} else if ( groups_is_user_mod( $user_id, $group_id ) ) {
					$status = 'editor';
				} else {
					$status = 'author';
				}

				// Add the user to the blog
				add_user_to_blog( get_current_blog_id(), $user_id, $status );

				// Redirect to avoid errors
				echo '<script type="text/javascript">window.location="' . $_SERVER['REQUEST_URI'] . '";</script>';
			}
		}
	}
}
//add_action( 'init', 'openlab_sync_blog_members_to_group', 999 ); // make sure BP is loaded

/**
 * Interfere in the comment posting process to allow for duplicates on the same post
 *
 * Borrowed from http://www.strangerstudios.com/blog/2010/10/duplicate-comment-detected-it-looks-as-though-you%E2%80%99ve-already-said-that/
 * See http://openlab.citytech.cuny.edu/redmine/issues/351
 */
function openlab_enable_duplicate_comments_preprocess_comment($comment_data) {
	if ( is_user_logged_in() ) {
		//add some random content to comment to keep dupe checker from finding it
		$random = md5(time());
		$comment_data['comment_content'] .= "disabledupes{" . $random . "}disabledupes";
	}

	return $comment_data;
}
add_filter('preprocess_comment', 'openlab_enable_duplicate_comments_preprocess_comment');

/**
 * Strips disabledupes string from comments. See previous function.
 */
function openlab_enable_duplicate_comments_comment_post($comment_id) {
	global $wpdb;

	if ( is_user_logged_in() ) {

		//remove the random content
		$comment_content = $wpdb->get_var("SELECT comment_content FROM $wpdb->comments WHERE comment_ID = '$comment_id' LIMIT 1");
		$comment_content = preg_replace("/disabledupes\{.*\}disabledupes/", "", $comment_content);
		$wpdb->query("UPDATE $wpdb->comments SET comment_content = '" . $wpdb->escape($comment_content) . "' WHERE comment_ID = '$comment_id' LIMIT 1");
	}
}
add_action('comment_post', 'openlab_enable_duplicate_comments_comment_post', 1);

/**
 * Adds the URL of the user profile to the New User Registration admin emails
 *
 * See http://openlab.citytech.cuny.edu/redmine/issues/334
 */
function openlab_newuser_notify_siteadmin( $message ) {

	// Due to WP lameness, we have to hack around to get the username
	preg_match( "|New User: (.*)|", $message, $matches );

	if ( !empty( $matches ) ) {
		$user = get_user_by( 'login', $matches[1] );
		$profile_url = bp_core_get_user_domain( $user->ID );

		if ( $profile_url ) {
			$message_a = explode( 'Remote IP', $message );
			$message = $message_a[0] . 'Profile URL: ' . $profile_url . "\n" . 'Remote IP' . $message_a[1];
		}
	}

	return $message;
}
add_filter( 'newuser_notify_siteadmin', 'openlab_newuser_notify_siteadmin' );

?>