<?php
/** Start the engine **/
add_theme_support( 'bbpress' );
add_theme_support( 'post-thumbnails' );

//dequeue buddypress default styles
if ( !function_exists( 'bp_dtheme_enqueue_styles' ) ) :
	function bp_dtheme_enqueue_styles() {}
endif;

/**creating a library to organize functions**/
require_once( STYLESHEETPATH.'/lib/course-clone.php' );
require_once( STYLESHEETPATH.'/lib/header-funcs.php' );
require_once( STYLESHEETPATH.'/lib/post-types.php' );
require_once( STYLESHEETPATH.'/lib/menus.php' );
require_once( STYLESHEETPATH.'/lib/content-processing.php' );
require_once( STYLESHEETPATH.'/lib/nav.php' );
require_once( STYLESHEETPATH.'/lib/breadcrumbs.php' );
require_once( STYLESHEETPATH.'/lib/group-funcs.php' );
require_once( STYLESHEETPATH.'/lib/ajax-funcs.php' );
require_once( STYLESHEETPATH.'/lib/help-funcs.php' );
require_once( STYLESHEETPATH.'/lib/member-funcs.php' );
require_once( STYLESHEETPATH.'/lib/page-funcs.php' );
require_once( STYLESHEETPATH.'/lib/admin-funcs.php' );

/**js calls**/
function my_init_method() {
	if ( !is_admin() ) {
		wp_enqueue_script( 'jquery' );
		wp_register_script( 'jcarousellite', get_bloginfo( 'stylesheet_directory' ) . '/js/jcarousellite.js' );
		wp_enqueue_script( 'jcarousellite' );
		wp_register_script( 'easyaccordion', get_bloginfo( 'stylesheet_directory' ) . '/js/easyaccordion.js' );
		wp_enqueue_script( 'easyaccordion' );
		wp_register_script( 'utility', get_bloginfo( 'stylesheet_directory' ) . '/js/utility.js' );
		wp_enqueue_script( 'utility' );
		wp_enqueue_script( 'dtheme-ajax-js', BP_PLUGIN_URL . '/bp-themes/bp-default/_inc/global.js', array( 'jquery' ) );
	}
}

add_action( 'init', 'my_init_method' );

// Custom Login
function my_custom_logo() { ?>
	<style type="text/css">
		#login { margin: 50px auto 0 auto; width: 350px; }
		#login h1 a { background: url( <?php bloginfo( 'stylesheet_directory' ) ?>/images/logo.png ) center no-repeat; height:125px; width: 370px; }
		body { background: #fff }
	</style>
<?php }
add_action( 'login_head', 'my_custom_logo' );

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

function cuny_o_e_class( $num ) {
 return $num % 2 == 0 ? " even":" odd";
}

function cuny_third_end_class( $num ) {
 return $num % 3 == 0 ? " last":"";
}

function cuny_default_avatar( $url ) {
	return wds_add_default_member_avatar();
}
add_filter( 'bp_core_mysteryman_src', 'cuny_default_avatar' );

remove_all_actions( 'genesis_footer' );
//add_action( 'genesis_footer', 'cuny_creds_footer' );
function cuny_creds_footer() {
	echo '<span class="alignleft">� New York City College of Technology</span>';
	echo '<span class="alignright">City University of New York</span>';
}

remove_action( 'wp_footer', 'bp_core_admin_bar', 8 );

//before header mods
//add_action( 'genesis_before_header','cuny_bp_adminbar_menu' );
//cuny_bp_adminbar_menu function moved to cuny-sitewide-navi

add_action( 'bp_header','openlab_header_bar', 10 );
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
      if ( is_user_logged_in() ) {?>
      <div id="extra-border"></div>
      <ul class="nav" id="openlab-link">
          <li<?php if ( bp_is_my_profile() ) : ?> class="current-menu-item"<?php endif ?>>
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
		<option value="portfolios">Portfolios</option>
		</select>

		<input type="submit" name="search-submit" id="search-submit" value="<?php _e( 'Search', 'buddypress' ) ?>" />
		<?php wp_nonce_field( 'bp_search_form' ) ?>
	</form><!-- #search-form -->
<?php }

add_action( 'init','openlab_search_override',1 );
function openlab_search_override() {
	global $bp;
	if ( isset( $_POST['search-submit'] ) && $_POST['search-terms'] ) {
		if ( $_POST['search-which']=="members" ) {
			wp_redirect( $bp->root_domain.'/people/?search='.$_POST['search-terms'] );
			exit();
		} elseif ( $_POST['search-which']=="courses" ) {
			wp_redirect( $bp->root_domain.'/courses/?search='.$_POST['search-terms'] );
			exit();
		} elseif ( $_POST['search-which']=="projects" ) {
			wp_redirect( $bp->root_domain.'/projects/?search='.$_POST['search-terms'] );
			exit();
		} elseif ( $_POST['search-which']=="clubs" ) {
			wp_redirect( $bp->root_domain.'/clubs/?search='.$_POST['search-terms'] );
			exit();
		} elseif ( $_POST['search-which']=="portfolios" ) {
			wp_redirect( $bp->root_domain.'/portfolios/?search='.$_POST['search-terms'] );
			exit();
		}
	}
}

/*add_action( 'genesis_after_content', 'cuny_the_clear_div' );
function cuny_the_clear_div() {
	echo '<div style="clear:both;"></div>';
}*/

remove_filter( 'get_the_excerpt', 'wp_trim_excerpt' );
add_filter( 'get_the_excerpt', 'cuny_add_links_wp_trim_excerpt' );
function cuny_add_links_wp_trim_excerpt( $text ) {
	$raw_excerpt = $text;
	if ( '' == $text ) {
		$text = get_the_content( '' );

		$text = strip_shortcodes( $text );

		$text = apply_filters( 'the_content', $text );
		$text = str_replace( ']]>', ']]>', $text );
		$text = strip_tags( $text, '<a>' );
		$excerpt_length = apply_filters( 'excerpt_length', 55 );

		$excerpt_more = apply_filters( 'excerpt_more', ' ' . '[...]' );
		$words = preg_split( '/( <a.*?a> )|\n|\r|\t|\s/', $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE );
		if ( count( $words ) > $excerpt_length ) {
			array_pop( $words );
			$text = implode( ' ', $words );
			$text = $text . $excerpt_more;
		} else {
			$text = implode( ' ', $words );
		}
	}
	return apply_filters( 'new_wp_trim_excerpt', $text, $raw_excerpt );

}

//a variation on bp_groups_pagination_count() to match design
function cuny_groups_pagination_count( $group_name )
{
  global $bp, $groups_template;

	$start_num = intval( ( $groups_template->pag_page - 1 ) * $groups_template->pag_num ) + 1;
	$from_num = bp_core_number_format( $start_num );
	$to_num = bp_core_number_format( ( $start_num + ( $groups_template->pag_num - 1 ) > $groups_template->total_group_count ) ? $groups_template->total_group_count : $start_num + ( $groups_template->pag_num - 1 ) );
	$total = bp_core_number_format( $groups_template->total_group_count );

	echo sprintf( __( '%1$s to %2$s ( of %3$s '.$group_name.' )', 'buddypress' ), $from_num, $to_num, $total );
}
//a variation on bp_members_pagination_count() to match design
function cuny_members_pagination_count( $member_name )
{
	global $bp, $members_template;

		if ( empty( $members_template->type ) )
			$members_template->type = '';

		$start_num = intval( ( $members_template->pag_page - 1 ) * $members_template->pag_num ) + 1;
		$from_num  = bp_core_number_format( $start_num );
		$to_num    = bp_core_number_format( ( $start_num + ( $members_template->pag_num - 1 ) > $members_template->total_member_count ) ? $members_template->total_member_count : $start_num + ( $members_template->pag_num - 1 ) );
		$total     = bp_core_number_format( $members_template->total_member_count );

		$pag = sprintf( __( '%1$s to %2$s ( of %3$s members )', 'buddypress' ), $from_num, $to_num, $total );
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
		// Keep the following tabs as-is
		$keepers = array( 'members' );
		foreach ( $bp->bp_options_nav[$bp->current_item] as $key => $item ) {
			if ( 'home' == $key ) {
				$bp->bp_options_nav[$bp->current_item][$key]['name'] = 'Profile';
			} else if ( 'admin' == $key ) {
				$bp->bp_options_nav[$bp->current_item][$key]['name'] = 'Settings';
			} else if ( ! in_array( $key, $keepers ) ) {
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
	register_sidebar( array(
		'name' => __( 'Rotating Post Gallery Widdget', 'cuny' ),
		'description' => __( 'This is the widget for holding the Rotating Post Gallery Widget', 'cuny' ),
		'id' => 'pgw-gallery',
		'before_widget' => '<div id="pgw-gallery">',
		'after_widget'  => '</div>',
	) );
	//add widget for the Featured Widget - will be placed on the homepage under "In the Spotlight"
	register_sidebar( array(
		'name' => __( 'Featured Widget', 'cuny' ),
		'description' => __( 'This is the widget for holding the Featured Widget', 'cuny' ),
		'id' => 'cac-featured',
		'before_widget' => '<div id="cac-featured">',
		'after_widget'  => '</div>',
	) );
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
		'show_hidden'   => true,
		'group_type'	=> 'club',
		'get_activity'	=> true
	);
	$r = wp_parse_args( $args, $defaults );

	$select = $where = '';

	$select = "SELECT a.group_id FROM {$bp->groups->table_name_members} a";
	$where  = $wpdb->prepare( "WHERE a.is_confirmed = 1 AND a.is_banned = 0 AND a.user_id = %d", $r['user_id'] );

	if ( !$r['show_hidden'] ) {
		$select .= " JOIN {$bp->groups->table_name} c ON ( c.id = a.group_id ) ";
		$where  .= " AND c.status != 'hidden' ";
	}

	if ( 'all' != $r['group_type'] ) {
		// Sanitize
		$group_type = in_array( strtolower( $r['group_type'] ), array( 'club', 'project', 'course' ) ) ? strtolower( $r['group_type'] ) : 'club';

		$select .= " JOIN {$bp->groups->table_name_groupmeta} d ON ( a.group_id = d.group_id ) ";
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
			$activities = $wpdb->get_results( "SELECT id,item_id, content FROM {$bp->activity->table_name} WHERE component = 'groups' AND item_id IN ( {$retval['group_ids_sql']} ) ORDER BY id DESC" );

			// Now walk down the list and try to match with a group. Once one is found, remove
			// that group from the stack
			$group_activity_items = array();
			foreach ( ( array )$activities as $act ) {
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
		foreach ( $items as $key => $item ) {
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
		$matched_group_ids = $wpdb->get_col( $wpdb->prepare( "SELECT id FROM {$bp->groups->table_name} WHERE ( name LIKE '%%{$search_terms_sql}%%' OR description LIKE '%%{$search_terms_sql}%%' ) AND status != 'hidden'" ) );

		if ( !empty( $matched_group_ids ) ) {
			$in_sql = " AND a.group_id IN ( " . implode( ',', wp_parse_id_list( $matched_group_ids ) ) . " ) ";
		}
	}

	return $in_sql;
}

/**
 * Get blog avatar ( group avatar when available, otherwise user )
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

<div class="radio group-site">

	<h6><?php _e( 'Public', 'buddypress' ) ?></h6>
  <span id="search-setting-note">Note: These options will NOT block access to your site. It is up to search engines to honor your request.</span>
	<label for="blog-private1"><input id="blog-private1" type="radio" name="blog_public" value="1" <?php checked( '1', $blog_public ); ?> /><?php _e( 'Allow search engines to index this site. Your site will show up in web search results.' ); ?></label>

	<label for="blog-private0"><input id="blog-private0" type="radio" name="blog_public" value="0" <?php checked( '0', $blog_public ); ?> /><?php _e( 'Ask search engines not to index this site. Your site should not show up in web search results.' ); ?></label>

	<?php if ( !openlab_is_portfolio() && ( !isset( $_GET['type'] ) || 'portfolio' != $_GET['type'] ) ): ?>

		<h6><?php _e( 'Private', 'buddypress' ) ?></h6>
		<label for="blog-private-1"><input id="blog-private-1" type="radio" name="blog_public" value="-1" <?php checked( '-1', $blog_public ); ?>><?php _e( 'I would like my site to be visible only to registered users of City Tech OpenLab.','buddypress' ); ?></label>

		<label for="blog-private-2"><input id="blog-private-2" type="radio" name="blog_public" value="-2" <?php checked( '-2', $blog_public ); ?>><?php _e( 'I would like my site to be visible to registered users of this '.ucfirst( $group_type ) . '.' ); ?></label>

		<h6><?php _e( 'Hidden', 'buddypress' ) ?></h6>
		<label for="blog-private-3"><input id="blog-private-3" type="radio" name="blog_public" value="-3" <?php checked( '-3', $blog_public ); ?>><?php _e( 'I would like my site to be visible only to site administrators.' ); ?></label>

	<?php else : ?>

		<?php /* Portfolios */ ?>
		<h6>Private</h6>
		<label for="blog-private-1"><input id="blog-private-1" type="radio" name="blog_public" value="-1" <?php checked( '-1', $blog_public ); ?>><?php _e( 'I would like my site to be visible only to registered users of City Tech OpenLab.','buddypress' ); ?></label>

		<label for="blog-private-2"><input id="blog-private-2" type="radio" name="blog_public" value="-2" <?php checked( '-2', $blog_public ); ?>>I would like my site to be visible only to registered users that I have granted access.</label>
		<p class="description private-portfolio-gloss">Note: If you would like non-City Tech users to view your private site, you will need to make your site public.</p>

		<label for="blog-private-3"><input id="blog-private-3" type="radio" name="blog_public" value="-3" <?php checked( '-3', $blog_public ); ?>>I would like my site to be visible only to me.</label>

	<?php endif; ?>
</div>
	<?php
}

/**
 * Output the group subscription default settings
 *
 * This is a lazy way of fixing the fact that the BP Group Email Subscription
 * plugin doesn't actually display the correct default sub level ( even though it
 * does *save* the correct level )
 */
function openlab_default_subscription_settings_form() {
	if ( openlab_is_portfolio() || ( isset( $_GET['type'] ) && 'portfolio' == $_GET['type'] ) ) {
		return;
	}

	?>
	<hr>
	<h4 id="email-sub-defaults"><?php _e( 'Email Subscription Defaults', 'bp-ass' ); ?></h4>
	<p><?php _e( 'When new users join this group, their default email notification settings will be:', 'bp-ass' ); ?></p>
	<div class="radio email-sub">
		<label><input type="radio" name="ass-default-subscription" value="no" <?php ass_default_subscription_settings( 'no' ) ?> />
			<?php _e( 'No Email ( users will read this group on the web - good for any group - the default )', 'bp-ass' ) ?></label>
		<label><input type="radio" name="ass-default-subscription" value="sum" <?php ass_default_subscription_settings( 'sum' ) ?> />
			<?php _e( 'Weekly Summary Email ( the week\'s topics - good for large groups )', 'bp-ass' ) ?></label>
		<label><input type="radio" name="ass-default-subscription" value="dig" <?php ass_default_subscription_settings( 'dig' ) ?> />
			<?php _e( 'Daily Digest Email ( all daily activity bundles in one email - good for medium-size groups )', 'bp-ass' ) ?></label>
		<label><input type="radio" name="ass-default-subscription" value="sub" <?php ass_default_subscription_settings( 'sub' ) ?> />
			<?php _e( 'New Topics Email ( new topics are sent as they arrive, but not replies - good for small groups )', 'bp-ass' ) ?></label>
		<label><input type="radio" name="ass-default-subscription" value="supersub" <?php ass_default_subscription_settings( 'supersub' ) ?> />
			<?php _e( 'All Email ( send emails about everything - recommended only for working groups )', 'bp-ass' ) ?></label>
	</div>
	<hr />
	<?php
}
remove_action ( 'bp_after_group_settings_admin' ,'ass_default_subscription_settings_form' );
add_action ( 'bp_after_group_settings_admin' ,'openlab_default_subscription_settings_form' );

/**
 * Save the group default email setting
 *
 * We override the way that GES does it, because we want to save the value even
 * if it's 'no'. This should probably be fixed upstream
 */
function openlab_save_default_subscription( $group ) {
	global $bp, $_POST;

	if ( isset( $_POST['ass-default-subscription'] ) && $postval = $_POST['ass-default-subscription'] ) {
		groups_update_groupmeta( $group->id, 'ass_default_subscription', $postval );
	}
}
remove_action( 'groups_group_after_save', 'ass_save_default_subscription' );
add_action( 'groups_group_after_save', 'openlab_save_default_subscription' );

/**
 * Replace the BPGES notification setting with our own text
 */
function openlab_add_notice_to_notifications_page() {
?>
		<div id="group-email-settings">
			<table class="notification-settings zebra">
				<thead>
					<tr>
						<th class="icon">&nbsp;</th>
						<th class="title"><?php _e( 'Individual Group Email Settings', 'bp-ass' ); ?></th>
					</tr>
				</thead>

				<tbody>
					<tr>
						<td>&nbsp;</td>
						<td>
							<p><?php printf( 'To change the email notification settings for your groups, go to <a href="%s">My OpenLab</a> and click "Change" for each group.', bp_loggedin_user_domain() ); ?></p>

							<?php if ( get_option( 'ass-global-unsubscribe-link' ) == 'yes' ) : ?>
								<p><a href="<?php echo wp_nonce_url( add_query_arg( 'ass_unsubscribe', 'all' ), 'ass_unsubscribe_all' ); ?>"><?php _e( "Or set all your group's email options to No Email", 'bp-ass' ); ?></a></p>
							<?php endif; ?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
<?php
}
remove_action( 'bp_notification_settings', 'ass_add_notice_to_notifications_page', 9000 );
add_action( 'bp_notification_settings', 'openlab_add_notice_to_notifications_page', 9000 );


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

/**
 * Output the sidebar content for a single group
 */
function cuny_buddypress_group_actions() {
	if ( bp_has_groups() ) : while ( bp_groups() ) : bp_the_group(); ?>
		<div class="group-nav sidebar-widget">
			<?php echo openlab_group_visibility_flag() ?>
			<div id="item-buttons">
				<h2 class="sidebar-header"><?php echo openlab_get_group_type_label( 'case=upper' ) ?></h2>
				<ul>
					<?php bp_get_options_nav(); ?>
				</ul>
			</div><!-- #item-buttons -->
		</div>
	<?php do_action( 'bp_group_options_nav' ) ?>
	<?php endwhile; endif;
}

/**
 * Output the group visibility flag, shown above the right-hand nav
 */
function openlab_group_visibility_flag( $type = 'group' ) {
	static $group_buttons;

	if ( ! in_array( $type, array( 'group', 'site' ) ) ) {
		return;
	}

	if ( ! isset( $group_buttons ) ) {
		$group_buttons = array();
	}

	// We stash it so that we only have to do the calculation once
	if ( isset( $group_buttons[ $type ] ) ) {
		return $group_buttons[ $type ];
	}

	$group = groups_get_current_group();

	$site_url = openlab_get_group_site_url( $group->id );
	$site_id  = openlab_get_site_id_by_group_id( $group->id );

	if ( $site_url ) {
		// If we have a site URL but no ID, it's an external site, and is public
		if ( ! $site_id ) {
			$site_status = 1;
		} else {
			$site_status = get_blog_option( $site_id, 'blog_public' );
		}
	}

	$g_text = $s_text = '';
	$g_flag_type = $s_flag_type = 'down';
	$site_status = (float) $site_status;

	switch ( $site_status ) {

		// Public
		case 1 :
		case 0 :
			// If the group is also public, we use a single "up" flag
			if ( 'public' === $group->status ) {
				$g_text = 'Open';
				$g_flag_type = 'up';

			// Special case: groups without a site will show up as
			// $site_status = 0. They should get an up flag, since
			// "Private" applies to the entire group (the entire
			// group consisting of the profile)
			} else if ( ! $site_url ) {
				$g_text = 'Private';
				$g_flag_type = 'up';
			} else {
				$g_text = 'Private';
				$s_text = 'Open';
			}

			break;

		case -1 :
			$user_has_access = is_user_logged_in();

			if ( 'public' === $group->status ) {
				$g_text = 'Open';
			} else {
				$g_text = 'Private';
			}

			if ( $user_has_access ) {
				// If the group is public, show a single Public up flag
				if ( 'public' === $group->status ) {
					$g_flag_type = 'up';

				// For a private group, separate the flags
				} else {
					$s_text = 'Open';
				}
			} else {
				// Two separate flags
				if ( 'public' === $group->status ) {
					$s_text = 'Private';

				// Single "up" private flag
				} else {
					$g_flag_type = 'up';
				}
			}

			break;

		case -2 :
		case -3 :
			if ( 'public' === $group->status ) {
				$g_text = 'Open';
				$s_text = 'Private';
			} else {
				$g_text = 'Private';
				$g_flag_type = 'up';
			}

			break;
	}

	// Assemble the HTML
	$group_buttons['group'] = sprintf(
		'<div class="group-visibility-flag group-visibility-flag-group group-visibility-flag-%s group-visibility-flag-%s">%s</div>',
		strtolower( $g_text ),
		$g_flag_type,
		$g_text
	);

	// Only build the site button if there's something to build
	if ( ! empty( $s_text ) ) {
		$group_buttons['site'] = sprintf(
			'<div class="group-visibility-flag group-visibility-flag-site group-visibility-flag-%s group-visibility-flag-%s">%s</div>',
			strtolower( $s_text ),
			$s_flag_type,
			$s_text
		);
	}

	return isset( $group_buttons[ $type ] ) ? $group_buttons[ $type ] : '';
}

/**
 * Save the Account Type setting on the Account Settings screen.
 */
function openlab_save_account_type_on_settings() {
	if ( isset( $_POST['account_type'] ) ) {
		$types = array( 'Student', 'Alumni' );
		$account_type = in_array( $_POST['account_type'], $types ) ? $_POST['account_type'] : 'Student';
		$user_id = bp_displayed_user_id();
		$current_type = openlab_get_displayed_user_account_type();

		// Only students and alums can do this
		if ( in_array( $current_type, $types ) ) {
			xprofile_set_field_data( 'Account Type', bp_displayed_user_id(), $account_type );
		}
	}
}
add_action( 'bp_core_general_settings_after_save', 'openlab_save_account_type_on_settings' );

/**
>>>>>>> 1.3.x
 * Remove the 'hidden' class from hidden group leave buttons
 *
 * A crummy conflict with wp-ajax-edit-comments causes these items to be
 * hidden by jQuery. See b208c80 and #1004
 */
function openlab_remove_hidden_class_from_leave_group_button( $button ) {
	$button['wrapper_class'] = str_replace( ' hidden', '', $button['wrapper_class'] );
	return $button;
}
add_action( 'bp_get_group_join_button', 'openlab_remove_hidden_class_from_leave_group_button', 20 );

/**
 * Prints a status message regarding the group visibility.
 *
 * @global BP_Groups_Template $groups_template Groups template object
 * @param object $group Group to get status message for. Optional; defaults to current group.
 */
function openlab_group_status_message( $group = null ) {
	global $groups_template;

	if ( ! $group )
		$group =& $groups_template->group;

	$group_label = openlab_get_group_type_label( 'group_id=' . $group->id . '&case=upper' );

	$site_id = openlab_get_site_id_by_group_id( $group->id );
	$site_url = openlab_get_group_site_url( $group->id );

	if ( $site_url ) {
		// If we have a site URL but no ID, it's an external site, and is public
		if ( ! $site_id ) {
			$site_status = 1;
		} else {
			$site_status = get_blog_option( $site_id, 'blog_public' );
		}
	}

	$site_status = (float) $site_status;

	$message = '';

	switch ( $site_status ) {
		// Public
		case 1 :
		case 0 :
			if ( 'public' === $group->status ) {
				$message = 'This ' . $group_label . ' is OPEN.';
			} else if ( ! $site_url ) {
				// Special case: $site_status will be 0 when the
				// group does not have an associated site. When
				// this is the case, and the group is not
				// public, don't mention anything about the Site.
				$message = 'This ' . $group_label . ' is PRIVATE.';
			} else {
				$message = 'This ' . $group_label . ' Profile is PRIVATE, but the ' . $group_label . ' Site is OPEN to all visitors.';
			}

			break;

		case -1 :
			if ( 'public' === $group->status ) {
				$message = 'This ' . $group_label . ' Profile is OPEN, but only logged-in OpenLab members may view the ' . $group_label . ' Site.';
			} else {
				$message = 'This ' . $group_label . ' Profile is PRIVATE, but all logged-in OpenLab members may view the ' . $group_label . ' Site.';
			}

			break;

		case -2 :
		case -3 :
			if ( 'public' === $group->status ) {
				$message = 'This ' . $group_label . ' Profile is OPEN, but the ' . $group_label . ' Site is PRIVATE.';
			} else {
				$message = 'This ' . $group_label . ' is PRIVATE. You must be a member of the ' . $group_label . ' to view the ' . $group_label . ' Site.';
			}

			break;
	}

	echo $message;
}

/**
 * Modify the body class
 *
 * Invite New Members and Your Email Options fall under "Settings", so need
 * an appropriate body class.
 */
function openlab_group_admin_body_classes( $classes ) {
	if ( ! bp_is_group() ) {
		return $classes;
	}

	if ( in_array( bp_current_action(), array( 'invite-anyone', 'notifications' ) ) ) {
		$classes[] = 'group-admin';
	}

	return $classes;
}
add_filter( 'bp_get_the_body_class', 'openlab_group_admin_body_classes' );

/**
 * Get list of active semesters for use in course sidebar filter.
 */
function openlab_get_active_semesters() {
	global $wpdb, $bp;

	$tkey = 'openlab_active_semesters';
	$combos = get_transient( $tkey );

	if ( false === $combos ) {
		$sems = array( 'Winter', 'Spring', 'Summer', 'Fall' );
		$years = array();
		$this_year = date( 'Y' );
		for ( $i = 2011; $i <= $this_year; $i++ ) {
			$years[] = $i;
		}

		// Combos
		$combos = array();
		foreach ( $years as $year ) {
			foreach ( $sems as $sem ) {
				$combos[] = array(
					'year' => $year,
					'sem' => $sem,
					'option_value' => sprintf( '%s-%s', strtolower( $sem ), $year ),
					'option_label' => sprintf( '%s %s', $sem, $year ),
				);
			}
		}

		// Verify that the combos are all active
		foreach ( $combos as $ckey => $c ) {
			$active = (bool) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(gm1.id) FROM {$bp->groups->table_name_groupmeta} gm1 JOIN {$bp->groups->table_name_groupmeta} gm2 ON gm1.group_id = gm2.group_id WHERE gm1.meta_key = 'wds_semester' AND gm1.meta_value = %s AND gm2.meta_key = 'wds_year' AND gm2.meta_value = %s", $c['sem'], $c['year'] ) );

			if ( ! $active ) {
				unset( $combos[ $ckey ] );
			}
		}

		$combos = array_values( array_reverse( $combos ) );

		set_transient( $tkey, $combos );
		var_dump( 'Miss' );
	}

	return $combos;
}
