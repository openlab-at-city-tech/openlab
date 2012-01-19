<?php
/** Start the engine **/
require_once(TEMPLATEPATH.'/lib/init.php');
require_once(STYLESHEETPATH.'/marx_functions.php');

define('BP_DISABLE_ADMIN_BAR', true);

/** Add support with .wrap inside #inner */
add_theme_support( 'genesis-structural-wraps', array( 'header', 'nav', 'subnav', 'inner', 'footer-widgets', 'footer' ) );

remove_action('genesis_sidebar', 'genesis_do_sidebar');

add_action( 'widgets_init', 'cuny_remove_default_widget_areas', 11 );
function cuny_remove_default_widget_areas() {
	unregister_sidebar('sidebar');
	unregister_sidebar('sidebar-alt');
}
/** Add support for custom background **/
add_custom_background();
//add_theme_support( 'genesis-footer-widgets', 5 );

add_action( 'wp_print_styles', 'cuny_no_bp_default_styles', 100 );

// Enqueue Styles For Testimonials Page & sub-pages
add_action('wp_print_styles', 'wds_cuny_ie_styles');
function wds_cuny_ie_styles() { 
  if ( is_admin() )
    return;
    ?>
    
    <!--[if lte IE 7]>
      <link rel="stylesheet" href="<?php bloginfo( 'stylesheet_directory' ); ?>/css/ie7.css" type="text/css" media="screen" />
    <![endif]-->
    <!--[if IE 8]>
      <link rel="stylesheet" href="<?php bloginfo( 'stylesheet_directory' ); ?>/css/ie8.css" type="text/css" media="screen" />
    <![endif]-->
    <!--[if IE 9]>
      <link rel="stylesheet" href="<?php bloginfo( 'stylesheet_directory' ); ?>/css/ie9.css" type="text/css" media="screen" />
    <![endif]-->
    

    <?php }

function cuny_no_bp_default_styles() {
	wp_dequeue_style( 'gconnect-bp' );
	wp_dequeue_script('superfish');
	wp_dequeue_script('superfish-args');

	wp_enqueue_style( 'cuny-bp', get_stylesheet_directory_uri() . '/css/buddypress.css' );
	wp_dequeue_style( 'gconnect-adminbar' );
}

add_action( 'genesis_meta', 'cuny_google_font');
function cuny_google_font() {
	echo "<link href='http://fonts.googleapis.com/css?family=Arvo' rel='stylesheet' type='text/css'>";
}

function cuny_o_e_class($num){
 return $num % 2 == 0 ? " even":" odd";
}

function cuny_third_end_class($num){
 return $num % 3 == 0 ? " last":"";
}

function cuny_default_avatar( $url ) {
	return get_stylesheet_directory_uri() .'/images/avatar.jpg';
}
add_filter( 'bp_core_mysteryman_src', 'cuny_default_avatar' );

remove_action('genesis_before_loop' , 'genesis_do_breadcrumbs');
add_action('genesis_before_footer' , 'genesis_do_breadcrumbs', 5);

add_filter('genesis_breadcrumb_args', 'custom_breadcrumb_args');
function custom_breadcrumb_args($args) {
    $args['labels']['prefix'] = 'You are here:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
    $args['prefix']  = '<div id="breadcrumb-container"><div class="breadcrumb">';
    $args['suffix'] = '</div></div>';
    return $args;
}

remove_all_actions('genesis_footer');
//add_action('genesis_footer', 'cuny_creds_footer');
function cuny_creds_footer() {
	echo '<span class="alignleft">� New York City College of Technology</span>';
	echo '<span class="alignright">City University of New York</span>';
}

remove_action( 'wp_footer', 'bp_core_admin_bar', 8 );

//before header mods
add_action('genesis_before_header','cuny_bp_adminbar_menu');
//cuny_bp_adminbar_menu function moved to cuny-sitewide-navi

add_action('genesis_header','cuny_admin_bar', 10);
function cuny_admin_bar() { 

	cuny_site_wide_bp_search(); ?>
	<div class="clearfloat"></div>
	<?php //this adds the main menu, controlled through the WP menu interface
	$args = array(
				'theme_location' => 'main',
				'container' => '',
				'menu_class' => 'nav',
			);

	wp_nav_menu( $args );
	//do_action( 'cuny_bp_adminbar_menus' );
	if ( is_user_logged_in() ){?>
	<div id="extra-border"></div>
	<ul id="openlab-link">
		<li>
			<a href="<?php echo bp_loggedin_user_domain() ?>">My OpenLab</a>
		</li>
	</ul>
	<?php } ?>
		<div class="clearfloat"></div>
<?php }

add_action('genesis_after_content', 'cuny_the_clear_div');
function cuny_the_clear_div() {
	echo '<div style="clear:both;"></div>';
}

add_filter( 'wp_title', 'test', 10, 2 );
function test( $title ) {
	$find = " &#124; Groups &#124; ";
	$replace = " | ";
	$title = str_replace( $find , $replace, $title);
	return $title;
}

remove_filter('get_the_excerpt', 'wp_trim_excerpt');
add_filter('get_the_excerpt', 'cuny_add_links_wp_trim_excerpt');
function cuny_add_links_wp_trim_excerpt($text) {
	$raw_excerpt = $text;
	if ( '' == $text ) {
		$text = get_the_content('');

		$text = strip_shortcodes( $text );

		$text = apply_filters('the_content', $text);
		$text = str_replace(']]>', ']]>', $text);
		$text = strip_tags($text, '<a>');
		$excerpt_length = apply_filters('excerpt_length', 55);

		$excerpt_more = apply_filters('excerpt_more', ' ' . '[...]');
		$words = preg_split('/(<a.*?a>)|\n|\r|\t|\s/', $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE );
		if ( count($words) > $excerpt_length ) {
			array_pop($words);
			$text = implode(' ', $words);
			$text = $text . $excerpt_more;
		} else {
			$text = implode(' ', $words);
		}
	}
	return apply_filters('new_wp_trim_excerpt', $text, $raw_excerpt);

}
//
//    This function switches to the group site, gets the blog_public option and determines if the site is public
//    or not (private = value of "-2") and then, if it is private checks if the logged in user is registered on that blog and if so returns true
//    otherwise (if private and not a registered member) returns false
//
function wds_site_can_be_viewed() {
	global $user_ID;
	$blog_public = false;
	$group_id = bp_get_group_id(); 
	$wds_bp_group_site_id=groups_get_groupmeta($group_id, 'wds_bp_group_site_id' );
	if($wds_bp_group_site_id!=""){
		switch_to_blog($wds_bp_group_site_id);
		$blog_private = get_option('blog_public');
		if ($blog_private != "-2") {
			$blog_public = true;
		} else {
			$user_capabilities = get_user_meta($user_ID,'wp_' . $wds_bp_group_site_id . '_capabilities',true);
			if ($user_capabilities != "") {
				$blog_public = true;
			}
		}
		restore_current_blog();
	}
	return $blog_public;
		

}
//a variation on bp_groups_pagination_count() to match design
function cuny_groups_pagination_count($group_name)
{
  global $bp, $groups_template;

	$start_num = intval( ( $groups_template->pag_page - 1 ) * $groups_template->pag_num ) + 1;
	$from_num = bp_core_number_format( $start_num );
	$to_num = bp_core_number_format( ( $start_num + ( $groups_template->pag_num - 1 ) > $groups_template->total_group_count ) ? $groups_template->total_group_count : $start_num + ( $groups_template->pag_num - 1 ) );
	$total = bp_core_number_format( $groups_template->total_group_count );

	echo sprintf( __( '%1$s to %2$s (of %3$s '.$group_name.')', 'buddypress' ), $from_num, $to_num, $total );
}
//a variation on bp_members_pagination_count() to match design
function cuny_members_pagination_count($member_name)
{
	global $bp, $members_template;

		if ( empty( $members_template->type ) )
			$members_template->type = '';

		$start_num = intval( ( $members_template->pag_page - 1 ) * $members_template->pag_num ) + 1;
		$from_num  = bp_core_number_format( $start_num );
		$to_num    = bp_core_number_format( ( $start_num + ( $members_template->pag_num - 1 ) > $members_template->total_member_count ) ? $members_template->total_member_count : $start_num + ( $members_template->pag_num - 1 ) );
		$total     = bp_core_number_format( $members_template->total_member_count );

		$pag = sprintf( __( '%1$s to %2$s (of %3$s members)', 'buddypress' ), $from_num, $to_num, $total );
		echo $pag;
}

//a variation on bp_get_options_nav to match the design
//main change here at the moment - changing "home" to "profile"
function cuny_get_options_nav() {
	global $bp;

	// If we are looking at a member profile, then the we can use the current component as an
	// index. Otherwise we need to use the component's root_slug
	$component_index = !empty( $bp->displayed_user ) ? $bp->current_component : bp_get_root_slug( $bp->current_component );

	if ( !bp_is_single_item() ) {
		if ( !isset( $bp->bp_options_nav[$component_index] ) || count( $bp->bp_options_nav[$component_index] ) < 1 ) {
			return false;
		} else {
			$the_index = $component_index;
		}
	} else {
		if ( !isset( $bp->bp_options_nav[$bp->current_item] ) || count( $bp->bp_options_nav[$bp->current_item] ) < 1 ) {
			return false;
		} else {
			$the_index = $bp->current_item;
		}
	}

	// Loop through each navigation item
	foreach ( (array)$bp->bp_options_nav[$the_index] as $subnav_item ) {
		if ( !$subnav_item['user_has_access'] )
			continue;

		// If the current action or an action variable matches the nav item id, then add a highlight CSS class.
		if ( $subnav_item['slug'] == $bp->current_action ) {
			$selected = ' class="current selected"';
		} else {
			$selected = '';
		}
		
		if ($subnav_item['name'] == 'Home')
		{
			$subnav_item['name'] = 'Profile';
		}

		// List type depends on our current component
		$list_type = bp_is_group() ? 'groups' : 'personal';

		// echo out the final list item
		echo apply_filters( 'bp_get_options_nav_' . $subnav_item['css_id'], '<li id="' . $subnav_item['css_id'] . '-' . $list_type . '-li" ' . $selected . '><a id="' . $subnav_item['css_id'] . '" href="' . $subnav_item['link'] . '">' . $subnav_item['name'] . '</a></li>', $subnav_item );
	}
}//end cuny_get_options_nav

//custom menu locations for OpenLab
register_nav_menus( array(
	'main' => __('Main Menu', 'cuny'),
	'aboutmenu' => __('About Menu', 'cuny'),
	'helpmenu' => __('Help Menu', 'cuny')
) );

//custom widgets for OpenLab
function cuny_widgets_init() {
	//add widget for Rotating Post Gallery Widget - will be placed on the homepage
	register_sidebar(array(
		'name' => __('Rotating Post Gallery Widdget', 'cuny'),
		'description' => __('This is the widget for holding the Rotating Post Gallery Widget', 'cuny'),
		'id' => 'pgw-gallery',
		'before_widget' => '<div id="pgw-gallery">',
		'after_widget'  => '</div>',
	));
	//add widget for the Featured Widget - will be placed on the homepage under "In the Spotlight"
	register_sidebar(array(
		'name' => __('Featured Widget', 'cuny'),
		'description' => __('This is the widget for holding the Featured Widget', 'cuny'),
		'id' => 'cac-featured',
		'before_widget' => '<div id="cac-featured">',
		'after_widget'  => '</div>',
	));
}

add_action( 'widgets_init', 'cuny_widgets_init' );

/**
 * Don't show the Request Membership nav item
 */
function openlab_remove_request_membership_item() {
	return '';
}
add_action( 'bp_get_options_nav_request-membership', 'openlab_remove_request_membership_item', 99 );

function openlab_displayed_user_account_type() {
	echo openlab_get_displayed_user_account_type();
}
	function openlab_get_displayed_user_account_type() {
		return xprofile_get_field_data( 'Account Type', bp_displayed_user_id() );
	}

function openlab_current_user_ribbonclass() {
	$account_type = openlab_get_displayed_user_account_type();
	
	$ribbonclass = '';

	if ( $account_type == 'Faculty' )
		$ribbonclass = 'watermelon-ribbon';
	if ( $account_type == 'Student' )
		$ribbonclass = 'robin-egg-ribbon';
	if ( $account_type == 'Staff' )
		$ribbonclass = 'yellow-canary-ribbon';
	
	echo $ribbonclass;
}

/**
 * Don't show the New Topic link on the sidebar of a forum page
 */
add_action( 'wp_head', create_function( '', "remove_action( 'bp_group_header_actions', 'bp_group_new_topic_button' );" ), 999 );

function openlab_get_groups_of_user( $args = array() ) {
	global $bp, $wpdb;
	
	$retval = array(
		'group_ids'     => array(),
		'group_ids_sql' => '',
		'activity'	=> array()
	);
	
	$defaults = array(
		'user_id' 	=> bp_loggedin_user_id(),
		'active_status' => 'all',
		'show_hidden'   => true,
		'group_type'	=> 'club',
		'get_activity'	=> true
	);
	$r = wp_parse_args( $args, $defaults );
	
	$select = $where = '';
	
	$select = $wpdb->prepare( "SELECT a.group_id FROM {$bp->groups->table_name_members} a" );
	$where  = $wpdb->prepare( "WHERE a.is_confirmed = 1 AND a.is_banned = 0 AND a.user_id = %d", $r['user_id'] );
	
	if ( 'all' != $r['active_status'] ) {
		// For legacy reasons, not all active groups are marked 'active'
		if ( 'inactive' == $r['active_status'] ) {
			$select .= $wpdb->prepare( " JOIN {$bp->groups->table_name_groupmeta} b ON (a.group_id = b.group_id) " );
			$where  .= $wpdb->prepare( " AND b.meta_key = 'openlab_group_active_status' AND b.meta_value = %s ", $r['active_status'] );
		} else {
			// Gotta do a double query to calculate active groups (NOT IN 'inactive')
			$inactive_groups = $wpdb->get_col( $wpdb->prepare( "SELECT group_id FROM {$bp->groups->table_name_groupmeta} WHERE meta_key = 'openlab_group_active_status' AND meta_value = 'inactive'" ) );
			
			if ( !empty( $inactive_groups ) ) {
				$inactive_groups_sql = implode( ',', $inactive_groups );
				$where .= $wpdb->prepare( " AND a.group_id NOT IN ({$inactive_groups_sql}) " );
			}
		}
	}
	
	if ( !$r['show_hidden'] ) {
		$select .= $wpdb->prepare( " JOIN {$bp->groups->table_name} c ON (c.id = a.group_id) " );
		$where  .= $wpdb->prepare( " AND c.status != 'hidden' " );
	}
	
	if ( 'all' != $r['group_type'] ) {
		// Sanitize
		$group_type = in_array( strtolower( $r['group_type'] ), array( 'club', 'project', 'course' ) ) ? strtolower( $r['group_type'] ) : 'club';
		
		$select .= $wpdb->prepare( " JOIN {$bp->groups->table_name_groupmeta} d ON (a.group_id = d.group_id) " );
		$where  .= $wpdb->prepare( " AND d.meta_key = 'wds_group_type' AND d.meta_value = %s ", $group_type );
	}
	
	$sql = $select . ' ' . $where;

	$group_ids = $wpdb->get_col( $sql );

	$retval['group_ids'] = $group_ids;
	
	// Now that we have group ids, get the associated activity items and format the 
	// whole shebang in the proper way
	if ( !empty( $group_ids ) ) {
		$retval['group_ids_sql'] = implode( ',', $group_ids );		
	
		if ( $r['get_activity'] ) {
			// bp_has_activities() doesn't allow arrays of item_ids, so query manually
			$activities = $wpdb->get_results( $wpdb->prepare( "SELECT item_id, content FROM {$bp->activity->table_name} WHERE component = 'groups' AND item_id IN ({$retval['group_ids_sql']}) ORDER BY date_recorded DESC" ) );
			
			// Now walk down the list and try to match with a group. Once one is found, remove
			// that group from the stack
			$group_activity_items = array();
			foreach( (array)$activities as $act ) {
				if ( !empty( $act->content ) && in_array( $act->item_id, $group_ids ) && !isset( $group_activity_items[$act->item_id] ) ) {
					$group_activity_items[$act->item_id] = $act->content;
					$key = array_search( $act->item_id, $group_ids );
					unset( $group_ids[$key] );
				}
			}
			
			$retval['activity'] = $group_activity_items;
		}
	}
	
	return $retval;
}

?>
