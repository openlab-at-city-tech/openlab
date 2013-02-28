<?php
/** Start the engine **/
add_theme_support( 'bbpress' );

//dequeue buddypress default styles
if ( !function_exists( 'bp_dtheme_enqueue_styles' ) ) :
    function bp_dtheme_enqueue_styles() {}
endif;

//require_once(TEMPLATEPATH.'/lib/init.php');
require_once(STYLESHEETPATH.'/marx_functions.php');

/**creating a library to organize functions**/
require_once(STYLESHEETPATH.'/lib/header-funcs.php');
require_once(STYLESHEETPATH.'/lib/post-types.php');
require_once(STYLESHEETPATH.'/lib/menus.php');
require_once(STYLESHEETPATH.'/lib/content-processing.php');
require_once(STYLESHEETPATH.'/lib/nav.php');
require_once(STYLESHEETPATH.'/lib/breadcrumbs.php');
require_once(STYLESHEETPATH.'/lib/group-funcs.php');
require_once(STYLESHEETPATH.'/lib/ajax-funcs.php');

/**
 * Custom template loader for my-{grouptype}
 */
function openlab_mygroups_template_loader( $template ) {
	if ( is_page() ) {
		switch ( get_query_var( 'pagename' ) ) {
			case 'my-courses' :
			case 'my-clubs' :
			case 'my-projects' :
				bp_core_load_template( 'groups/index' );
				break;
		}
	}

	return $template;
}
add_filter( 'template_include', 'openlab_mygroups_template_loader' );

function cuny_o_e_class($num){
 return $num % 2 == 0 ? " even":" odd";
}

function cuny_third_end_class($num){
 return $num % 3 == 0 ? " last":"";
}

function cuny_default_avatar( $url ) {
	return wds_add_default_member_avatar();
}
add_filter( 'bp_core_mysteryman_src', 'cuny_default_avatar' );

remove_all_actions('genesis_footer');
//add_action('genesis_footer', 'cuny_creds_footer');
function cuny_creds_footer() {
	echo '<span class="alignleft">� New York City College of Technology</span>';
	echo '<span class="alignright">City University of New York</span>';
}

remove_action( 'wp_footer', 'bp_core_admin_bar', 8 );

//before header mods
//add_action('genesis_before_header','cuny_bp_adminbar_menu');
//cuny_bp_adminbar_menu function moved to cuny-sitewide-navi

add_action('bp_header','openlab_header_bar', 10);
function openlab_header_bar() { ?>
	
	<div id="header-wrap">
      <div id="title-area">
          <h1 id="title"><a href="<?php echo home_url(); ?>" title="<?php _ex( 'Home', 'Home page banner link title', 'buddypress' ); ?>"><?php bp_site_name(); ?></a></h1>
      </div>
  
      <?php openlab_site_wide_bp_search(); ?>
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
	</div><!--#wrap-->
<?php }

//openlab search function
function openlab_site_wide_bp_search() { ?>
	<form action="<?php echo bp_search_form_action() ?>" method="post" id="search-form">
		<input type="text" id="search-terms" name="search-terms" value="" />
		<?php //echo bp_search_form_type_select() ?>
        <select style="width: auto" id="search-which" name="search-which">
        <option value="members">People</option>
        <option value="courses">Courses</option>
        <option value="projects">Projects</option>
        <option value="clubs">Clubs</option>
        </select>

		<input type="submit" name="search-submit" id="search-submit" value="<?php _e( 'Search', 'buddypress' ) ?>" />
		<?php wp_nonce_field( 'bp_search_form' ) ?>
	</form><!-- #search-form -->
<?php }

add_action('init','openlab_search_override',1);
function openlab_search_override(){
    global $bp;
	if(isset($_POST['search-submit']) && $_POST['search-terms']){
		if($_POST['search-which']=="members"){
			wp_redirect($bp->root_domain.'/people/?search='.$_POST['search-terms']);
			exit();
		}elseif($_POST['search-which']=="courses"){
			wp_redirect($bp->root_domain.'/courses/?search='.$_POST['search-terms']);
			exit();
		}elseif($_POST['search-which']=="projects"){
			wp_redirect($bp->root_domain.'/projects/?search='.$_POST['search-terms']);
			exit();
		}elseif($_POST['search-which']=="clubs"){
			wp_redirect($bp->root_domain.'/clubs/?search='.$_POST['search-terms']);
			exit();
		}
	}
}

/*add_action('genesis_after_content', 'cuny_the_clear_div');
function cuny_the_clear_div() {
	echo '<div style="clear:both;"></div>';
}*/

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


/**
 * Reach into the item nav menu and remove stuff as necessary
 *
 * Hooked to bp_screens at 1 because apparently BP is broken??
 */
function openlab_modify_options_nav() {
	global $bp;

	if ( bp_is_group() && openlab_is_portfolio() ) {
		foreach( $bp->bp_options_nav[$bp->current_item] as $key => $item ) {
			if ( 'home' == $key ) {
				$bp->bp_options_nav[$bp->current_item][$key]['name'] = 'Profile';
			} else if ( 'admin' == $key ) {
				$bp->bp_options_nav[$bp->current_item][$key]['name'] = 'Settings';
			} else {
				unset( $bp->bp_options_nav[$bp->current_item][$key] );
			}

		}
	}

        if ( bp_is_group() ) {
                $bp->bp_options_nav[ bp_get_current_group_slug() ]['admin']['position'] = 15;
                return;
        }
}
add_action( 'bp_screens', 'openlab_modify_options_nav', 1 );

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

/**
 * Don't show the Join Group button on the sidebar of a portfolio
 */
function openlab_no_join_on_portfolios() {
	global $bp;
	
	if ( openlab_is_portfolio() ) {
		remove_action( 'bp_group_header_actions', 'bp_group_join_button' );
	}
	
	//fix for files, docs, and membership pages in group profile - hiding join button
	if ($bp->current_action == 'files' || $bp->current_action == 'docs' || $bp->current_action == 'invite-anyone' || $bp->current_action == 'notifications' )
		{
			remove_action( 'bp_group_header_actions', 'bp_group_join_button' );
		}
}
add_action( 'wp_head', 'openlab_no_join_on_portfolios', 999 );

function openlab_get_groups_of_user( $args = array() ) {
	global $bp, $wpdb;

	$retval = array(
		'group_ids'     => array(),
		'group_ids_sql' => '',
		'activity'	=> array()
	);

	$defaults = array(
		'user_id' 	=> bp_loggedin_user_id(),
		'show_hidden'   => true,
		'group_type'	=> 'club',
		'get_activity'	=> true
	);
	$r = wp_parse_args( $args, $defaults );

	$select = $where = '';

	$select = $wpdb->prepare( "SELECT a.group_id FROM {$bp->groups->table_name_members} a" );
	$where  = $wpdb->prepare( "WHERE a.is_confirmed = 1 AND a.is_banned = 0 AND a.user_id = %d", $r['user_id'] );

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
			$activities = $wpdb->get_results( $wpdb->prepare( "SELECT id,item_id, content FROM {$bp->activity->table_name} WHERE component = 'groups' AND item_id IN ({$retval['group_ids_sql']}) ORDER BY id DESC" ) );

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

/**
 * Ensure that external links in the help menu get the external-link glyph
 */
function openlab_help_menu_external_glyph( $items, $args ) {
	if ( false !== strpos( $args->theme_location, 'about' ) ) {
		foreach( $items as $key => $item ) {
			if ( false === strpos( $item->url, bp_get_root_domain() ) ) {
				$items[$key]->classes[] = 'external-link';
			}
		}
	}
	return $items;
}
add_filter( 'wp_nav_menu_objects', 'openlab_help_menu_external_glyph', 10, 2 );

/**
 * Pagination links in group directories cannot contain the 's' URL parameter for search
 */
function openlab_group_pagination_search_key( $pag ) {
	if ( false !== strpos( $pag, 'grpage' ) ) {
		$pag = remove_query_arg( 's', $pag );
	}

	return $pag;
}
add_filter( 'paginate_links', 'openlab_group_pagination_search_key' );

/**
 * Utility function for getting the IN sql corresponding to search terms in a groups directory
 */
function openlab_get_groups_in_sql( $search_terms ) {
	global $wpdb, $bp;

	// Due to the incredibly crappy way this was originally built, I will implement search by
	// using a separate query + IN
	$in_sql = '';
	if ( !empty( $search_terms ) ) {
		$search_terms_sql = like_escape( $search_terms );

		// Don't get hidden groups. Important to keep counts in line with bp_has_groups()
		$matched_group_ids = $wpdb->get_col( $wpdb->prepare( "SELECT id FROM {$bp->groups->table_name} WHERE (name LIKE '%%{$search_terms_sql}%%' OR description LIKE '%%{$search_terms_sql}%%') AND status != 'hidden'" ) );

		if ( !empty( $matched_group_ids ) ) {
			$in_sql = " AND a.group_id IN (" . implode(',', wp_parse_id_list( $matched_group_ids ) ) . ") ";
		}
	}

	return $in_sql;
}

/**
 * Get blog avatar (group avatar when available, otherwise user)
 */
function openlab_get_blog_avatar( $args = array() ) {
	global $blogs_template;

	$group_id = openlab_get_group_id_by_blog_id( $blogs_template->blog->blog_id );

	if ( $group_id ) {
		$args['object']  = 'group';
		$args['item_id'] = $group_id;
		return bp_core_fetch_avatar( $args );
	} else {
		return bp_get_blog_avatar( $args );
	}
}

/**
 * Add the group type to the Previous Step button during group creation
 *
 * @see http://openlab.citytech.cuny.edu/redmine/issues/397
 */
function openlab_previous_step_type( $url ) {
	if ( !empty( $_GET['type'] ) ) {
		$url = add_query_arg( 'type', $_GET['type'], $url );
	}

	return $url;
}
add_filter( 'bp_get_group_creation_previous_link', 'openlab_previous_step_type' );

/**
 * Markup for groupblog privacy settings
 */
function openlab_site_privacy_settings_markup( $site_id = 0 ) {
	global $blogname, $current_site;

	if ( !$site_id ) {
		$site_id = get_current_blog_id();
	}

	$blog_name   = get_blog_option( $site_id, 'blogname' );
	$blog_public = get_blog_option( $site_id, 'blog_public' );
	$group_type  = openlab_get_current_group_type( 'case=upper' );
?>

<div class="radio">

	<em><?php _e('Public', 'buddypress') ?></em>

	<label for="blog-private1"><input id="blog-private1" type="radio" name="blog_public" value="1" <?php checked( '1', $blog_public ); ?> /> <?php _e('Allow search engines to index this site. Your site will show up in web search results.'); ?></label>

	<label for="blog-private0"><input id="blog-private0" type="radio" name="blog_public" value="0" <?php checked( '0', $blog_public ); ?> /> <?php _e('Ask search engines not to index this site. Your site should not show up in web search results.<br /><em>Note: This option will NOT block access to your site. It is up to search engines to honor your request.</em>'); ?></label>

	<?php if ( !openlab_is_portfolio() && ( !isset( $_GET['type'] ) || 'portfolio' != $_GET['type'] ) ): ?>

		<em><?php _e('<em>Private</em>', 'buddypress') ?></em>
		<label for="blog-private-1"><input id="blog-private-1" type="radio" name="blog_public" value="-1" <?php checked( '-1', $blog_public ); ?>> <?php _e('I would like my site to be visible only to registered users of City Tech OpenLab.','buddypress'); ?></label>

		<label for="blog-private-2"><input id="blog-private-2" type="radio" name="blog_public" value="-2" <?php checked('-2', $blog_public ); ?>> <?php _e('I would like my site to be visible to registered users of this '.ucfirst($group_type)); ?></label>

		<em><?php _e('<em>Hidden</em>', 'buddypress') ?></em>
		<label for="blog-private-3"><input id="blog-private-3" type="radio" name="blog_public" value="-3" <?php checked('-3', $blog_public ); ?>><?php _e('I would like my site to be visible only to site administrators.'); ?></label>

	<?php else : ?>

		<?php /* Portfolios */ ?>
		<em>Hidden</em>
		<label for="blog-private-2"><input id="blog-private-2" type="radio" name="blog_public" value="-2" <?php checked( '-2', $blog_public ) ?>> I would like my site to be visible only to members of my Access List.</label>

	<?php endif; ?>
</div>
	<?php
}

/**
 * Output the group subscription default settings
 *
 * This is a lazy way of fixing the fact that the BP Group Email Subscription
 * plugin doesn't actually display the correct default sub level (even though it
 * does *save* the correct level)
 */
function openlab_default_subscription_settings_form() {
	if ( openlab_is_portfolio() || ( isset( $_GET['type'] ) && 'portfolio' == $_GET['type'] ) ) {
		return;
	}

	$stored_setting = ass_get_default_subscription();
	if ( !$stored_setting ) {
		$stored_setting = 'supersub';
	}

	?>
	<h4><?php _e('Email Subscription Defaults', 'bp-ass'); ?></h4>
	<p><?php _e('When new users join this group, their default email notification settings will be:', 'bp-ass'); ?></p>
	<div class="radio">
		<label><input type="radio" name="ass-default-subscription" value="no" <?php checked( $stored_setting, 'no' ) ?> />
			<?php _e( 'No Email (users will read this group on the web - good for any group - the default)', 'bp-ass' ) ?></label>
		<label><input type="radio" name="ass-default-subscription" value="sum" <?php checked( $stored_setting, 'sum' ) ?> />
			<?php _e( 'Weekly Summary Email (the week\'s topics - good for large groups)', 'bp-ass' ) ?></label>
		<label><input type="radio" name="ass-default-subscription" value="dig" <?php checked( $stored_setting, 'dig' ) ?> />
			<?php _e( 'Daily Digest Email (all daily activity bundles in one email - good for medium-size groups)', 'bp-ass' ) ?></label>
		<label><input type="radio" name="ass-default-subscription" value="sub" <?php checked( $stored_setting, 'sub' ) ?> />
			<?php _e( 'New Topics Email (new topics are sent as they arrive, but not replies - good for small groups)', 'bp-ass' ) ?></label>
		<label><input type="radio" name="ass-default-subscription" value="supersub" <?php checked( $stored_setting, 'supersub' ) ?> />
			<?php _e( 'All Email (send emails about everything - recommended only for working groups)', 'bp-ass' ) ?></label>
	</div>
	<hr />
	<?php
}
remove_action ( 'bp_after_group_settings_admin' ,'ass_default_subscription_settings_form' );
add_action ( 'bp_after_group_settings_admin' ,'openlab_default_subscription_settings_form' );

/**
 * Filter the output of the Add Friend/Cancel Friendship button
 */
function openlab_filter_friendship_button( $button ) {
	if ( $button['id'] == 'not_friends' || $button['id'] == 'is_friend' || $button['id'] == 'pending' ) {
		$button['link_text'] = 'Friend';
	}
	return $button;
}
add_filter( 'bp_get_add_friend_button', 'openlab_filter_friendship_button' );
?>
