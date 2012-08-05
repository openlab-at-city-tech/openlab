<?php
/** Start the engine **/
require_once(TEMPLATEPATH.'/lib/init.php');
require_once(STYLESHEETPATH.'/marx_functions.php');

/**this is for the post type declarations - they are done on the function side instead of through
a plugin, to make git tracking easier**/
require_once(STYLESHEETPATH.'/lib/post-types.php');
require_once(STYLESHEETPATH.'/lib/menus.php');
require_once(STYLESHEETPATH.'/lib/content-processing.php');

/**
 * Don't use the Genesis genesis_meta action to load the stylesheet
 *
 * Instead, load it as the very last item in the document head, so that we can override plugin
 * styles.
 *
 * We're manually outputting the <link> tag instead of enqueuing, because we must ensure that we
 * come last, last, last.
 *
 * Kids, do not try this at home!
 *
 * @link http://openlab.citytech.cuny.edu/redmine/issues/422
 */
remove_action( 'genesis_meta', 'genesis_load_stylesheet' );
function openlab_load_stylesheet() {
	echo '<link rel="stylesheet" href="' . get_bloginfo( 'stylesheet_url' ) . '" type="text/css" media="screen" />';
}
add_action( 'wp_head', 'openlab_load_stylesheet', 999999 );

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
add_theme_support( 'custom-background', array() );
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

/**
 * Enqueue our front-end scripts
 */
function openlab_enqueue_frontend_scripts() {
	if ( ( bp_is_group_create() && bp_is_action_variable( 'group-details', 1 ) ) ||
             ( bp_is_group_admin_page() && bp_is_action_variable( 'edit-details', 0 ) ) ) {
		wp_enqueue_script( 'openlab-group-create', get_stylesheet_directory_uri() . '/js/group-create.js', array( 'jquery' ) );
	}

        if ( bp_is_register_page() ) {
                wp_enqueue_script( 'openlab-registration', get_stylesheet_directory_uri() . '/js/register.js', array( 'jquery' ) );
        }

}
add_action( 'wp_enqueue_scripts', 'openlab_enqueue_frontend_scripts' );

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
	return wds_add_default_member_avatar();
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
//add_action('genesis_before_header','cuny_bp_adminbar_menu');
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
	if ( openlab_is_portfolio() ) {
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

<br/>

	<strong><?php _e('Public', 'buddypress') ?></strong>
	<label for="blog-private1"><input id="blog-private1" type="radio" name="blog_public" value="1" <?php checked( '1', $blog_public ); ?> /> <?php _e('Allow search engines to index this site. Your site will show up in web search results.'); ?></label>
<br/>
	<label for="blog-private0"><input id="blog-private0" type="radio" name="blog_public" value="0" <?php checked( '0', $blog_public ); ?> /> <?php _e('Ask search engines not to index this site. Your site should not show up in web search results.
Note: This option will NOT block access to your site. It is up to search engines to honor your request.'); ?></label>
<br /><br />
<strong><?php _e('Private', 'buddypress') ?></strong>
	<label for="blog-private-1"><input id="blog-private-1" type="radio" name="blog_public" value="-1" <?php checked( '1', $blog_public ); ?> /> <?php _e('I would like my site to be visible only to registered users of City Tech OpenLab.','buddypress'); ?><?php echo esc_attr( $current_site->site_name ) ?></label>
<br/>
	<label for="blog-private-2"><input id="blog-private-2" type="radio" name="blog_public" value="-2" <?php checked('-2', $blog_public ); ?> /> <?php _e('I would like my site to be visible to registered users of this '.$group_type); ?></label>
<br /><br />
<strong><?php _e('Hidden', 'buddypress') ?></strong>
	<label for="blog-private-3"><input id="blog-private-3" type="radio" name="blog_public" value="-3" <?php checked('-3', $blog_public ); ?> /><?php _e('I would like my site to be visible only to site administrators.'); ?></label>

</div>
	<?php
}

?>
