<?php
/**
 * Contains classes and functions designed for use in template files.
 *
 * @author Paul Gibbs <paul@byotos.com>
 * @package Achievements
 * @subpackage templatetags
 *
 * $Id: achievements-templatetags.php 1012 2011-10-07 18:50:04Z DJPaul $
 */

/**
 * The template loop class for Achievements
 *
 * @since 2.0
 * @package Achievements 
 * @subpackage templatetags
 */
class DPA_Achievement_Template {
	
	/**
	 * The current position in the loop
	 *
	 * @access public
	 * @var integer
	 * @see DPA_Achievement_Template
	 * @since 2.0
	 */
	var $current_achievement = -1;

	/**
	 * An array containing all the Achievement objects, fetched from the database
	 *
	 * @access public
	 * @var array
	 * @see DPA_Achievement_Template
	 * @see DPA_Achievement::get()
	 * @since 2.0
	 */
	var $achievements;

	/**
	 * The current Achievement in the loop
	 *
	 * @access public
	 * @var DPA_Achievement
	 * @see DPA_Achievement_Template
	 * @since 2.0
	 */
	var $achievement;

	/**
	 * The number of Achievements to loop through (affected by pagination)
	 *
	 * @access public
	 * @var integer
	 * @see DPA_Achievement_Template
	 * @since 2.0
	*/
	var $achievement_count;

	/**
	 * The total number of Achievements to loop through (NOT affected by pagination)
	 *
	 * @access public
	 * @var integer
	 * @see DPA_Achievement_Template
	 * @since 2.0
	 */
	var $total_achievement_count;

	/**
	 * Are we currently in the Achievements loops?
	 *
	 * @access public
	 * @var bool
	 * @see DPA_Achievement_Template
	 * @since 2.0
	 */
	var $in_the_loop;

	/**
	 * What number page are we on for pagination?
	 *
	 * @access public
	 * @var integer
	 * @see DPA_Achievement_Template
	 * @since 2.0
	 */
	var $pag_page;

	/**
	 * How many Achievements are we showing per-page for pagination?
	 *
	 * @access public
	 * @var integer
	 * @see DPA_Achievement_Template
	 * @since 2.0
	 */
	var $pag_num;

	/**
	 * Retrieve paginated links for Achievement pages
	 *
	 * @access public
	 * @var array|string String of page links or array of page links
	 * @see DPA_Achievement_Template
	 * @see paginate_links()
	 * @since 2.0
	 */
	var $pag_links;

	/**
	 * Constructor
	 *
	 * The Achievements template tag loop. It will retrieve results per the criteria passed from the database, apply pagination (if required)
	 * and populate the class variables required for the template tag loop to work in the page templates.
	 *
	 * @access public
	 * @global object $bp BuddyPress global settings
	 * @param integer $user_id = '0'. User ID
	 * @param string $type = 'all'. Either: all | active | inactive | unlocked | locked | single | active_by_action | eventcount
	 * @param integer $page = '1'. Which page of results are we on, for pagination? Also, $page = 1 without a per_page will result in no pagination being applied to the results
	 * @param integer $per_page = '20'. How many Achievements per page, for pagination?
	 * @param integer $max = '0'. How many Achievements to return as a maximum?
	 * @param string $slug = ''. The slug of an Achievement. Only required when $type is 'single'
	 * @param string $action = ''. Only get Achievements with this action. Only required when $type is 'active_by_action' and $user_id != 0 and $populate_extras is true
	 * @param string $search_terms = ''. Search query
	 * @param bool $populate_extras = true. If true, fetch additional information about when or if $user_id had unlocked each Achievement
	 * @param bool $skip_detail_page_result = true. On a single Achievement page, dpa_setup_nav() will setup the Achievement template objects appropiately. Set this to skip that prefetched value.
	 * @see DPA_Achievement::get()
	 * @see dpa_setup_nav()
	 * @since 2.0
	 * @todo Wrap all these arguments into an array, $args. Change bools to strings to better describe what variable is used for.
	 * @uses DPA_Achievement_Template
	 */
	function DPA_Achievement_Template( $user_id, $type, $page, $per_page, $max, $slug, $action, $search_terms, $populate_extras, $skip_detail_page_result ) {
		global $bp;

		$this->pag_page = !empty( $_REQUEST['xpage'] ) ? intval( $_REQUEST['xpage'] ) : $page;
		$this->pag_num  = !empty( $_GET['num'] )       ? intval( $_GET['num'] )       : $per_page;

		$args = array( 'limit' => $max, 'page' => $this->pag_page, 'per_page' => $this->pag_num, 'populate_extras' => $populate_extras, 'search_terms' => $search_terms, 'skip_detail_page_result' => $skip_detail_page_result, 'slug' => $slug, 'action' => $action, 'type' => $type, 'user_id' => $user_id );
		$this->achievements = DPA_Achievement::get( $args );

		// Item Requests
		if ( !$max || $max >= (int)$this->achievements['total'] )
			$this->total_achievement_count = (int)$this->achievements['total'];
		else
			$this->total_achievement_count = (int)$max;

		$this->achievements = $this->achievements['achievements'];

		if ( $max ) {
			if ( $max >= count( $this->achievements ) )
				$this->achievement_count = count( $this->achievements );
			else
				$this->achievement_count = (int)$max;
		} else {
			$this->achievement_count = count( $this->achievements );
		}

		if ( (int)$this->total_achievement_count && (int)$this->pag_num ) {
			$this->pag_links = paginate_links( array(
				'base' => add_query_arg( array( 'xpage' => '%#%', 'num' => $this->pag_num, 's' => $search_terms ) ),
				'format' => '',
				'total' => ceil( (int)$this->total_achievement_count / (int)$this->pag_num ),
				'current' => (int)$this->pag_page,
				'prev_text' => '&larr;',
				'next_text' => '&rarr;',
				'mid_size' => 1
			) );
		}
	}

	/**
	 * Returns true if we have Achievements matching the retrieval critera
	 *
	 * @access public
	 * @since 2.0
	 * @return bool
	 */
	function has_achievements() {
		if ( $this->achievement_count )
			return true;

		return false;
	}

	/**
	 * Returns the next Achievement object in the template tag loop
	 *
	 * @access public
	 * @since 2.0
	 * @uses DPA_Achievement
	 * @return DPA_Achievement
	 */
	function next_achievement() {
		$this->current_achievement++;
		$this->achievement = $this->achievements[$this->current_achievement];

		return $this->achievement;
	}

	/**
	 * Rewinds the template tag loop back to the beginning
	 *
	 * @access public
	 * @since 2.0
	 */
	function rewind_achievements() {
		$this->current_achievement = -1;
		if ( $this->achievement_count > 0 ) {
			$this->achievement = $this->achievements[0];
		}
	}

	/**
	 * Returns true if there is another Achievement to go through the template tag loop
	 *
	 * @access public
	 * @since 2.0
	 * @return bool
	 */
	function user_achievements() {
		if ( $this->current_achievement + 1 < $this->achievement_count ) {
			return true;
		} elseif ( $this->current_achievement + 1 == $this->achievement_count ) {
			do_action( 'achievements_loop_end' );
			// Do some cleaning up after the loop
			$this->rewind_achievements();
		}

		$this->in_the_loop = false;
		return false;
	}

	/**
	 * Sets the current Achievement in the template tag loop
	 *
	 * @access public
	 * @global object $bp BuddyPress global settings
	 * @since 2.0
	 */
	function the_achievement() {
		global $bp;

		$this->in_the_loop = true;
		$this->achievement = $this->next_achievement();

		if ( 0 == $this->current_achievement ) // loop has just started
			do_action( 'achievements_loop_start' );
	}
}

/**
 * The main template tag function that starts it all. Take criteria, queries the databases and paginates it
 *
 * @since 2.0
 * @param array|string $args See DPA_Achievement_Template
 * @uses DPA_Achievement_Template
 * @global DPA_Achievement_Template $achievements_template Achievements template tag object
 * @global object $bp BuddyPress global settings
 * @return bool Did any Achievements match critera?
 */
function dpa_has_achievements( $args = '' ) {
	global $achievements_template, $bp;

	$search_terms = '';
	$slug = '';
	$type = 'all';
	$user_id = 0;

	// User filtering
	if ( !empty( $bp->displayed_user->id ) )
		$user_id = $bp->displayed_user->id;
	elseif ( !empty( $bp->loggedin_user->id ) )
		$user_id = $bp->loggedin_user->id;

	// Type
	if ( $bp->is_single_item ) {
		// This might be redundant.
		$type = 'single';
		$slug = $bp->achievements->current_achievement->slug;
	}

	if ( dpa_is_member_my_achievements_page() && 'all' == $type )
		$type = 'newest';

	if ( !empty( $_REQUEST['s'] ) )
		$search_terms = stripslashes( $_REQUEST['s'] );

	$defaults = array(
		'skip_detail_page_result' => true,
		'max' => 0,
		'page' => 1,
		'per_page' => 20,
		'populate_extras' => true,
		'search_terms' => $search_terms,  //Pass search terms to return only matching Achievements
		'slug' => $slug,  //Pass an Achievement slug to only return that Achievement
		'type' => $type,
		'user_id' => $user_id,
		'action' => ''
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r );

	if ( 'single' != $type && dpa_is_member_my_achievements_page() || dpa_is_achievements_component() && !dpa_is_member_my_achievements_page() && $bp->loggedin_user->id && ( !empty( $_COOKIE['bp-achievements-scope'] ) && 'personal' == stripslashes( $_COOKIE['bp-achievements-scope'] ) ) )
		$type = 'unlocked';

	$achievements_template = new DPA_Achievement_Template( (int)$user_id, $type, (int)$page, (int)$per_page, (int)$max, $slug, $action, $search_terms, (bool)$populate_extras, (bool)$skip_detail_page_result );
	return apply_filters( 'dpa_has_achievements', $achievements_template->has_achievements() );
}

/**
 * Sets the current Achievement in the template tag loop
 *
 * @since 2.0
 * @global DPA_Achievement_Template $achievements_template Achievements template tag object
 */
function dpa_the_achievement() {
	global $achievements_template;

	return $achievements_template->the_achievement();
}

/**
 * Returns true if there is another Achievement to go through the template tag loop
 *
 * @since 2.0
 * @global DPA_Achievement_Template $achievements_template Achievements template tag object
 * @return bool
 */
function dpa_achievements() {
	global $achievements_template;

	return $achievements_template->user_achievements();
}

/**
 * Template tag version of dpa_get_achievements_pagination_links()
 *
 * @uses dpa_get_achievements_pagination_links()
 * @since 2.0
 */
function dpa_achievements_pagination_links() {
	echo dpa_get_achievements_pagination_links();
}
	/**
	* Pagination links for template tag loop
	*
	* @since 2.0
	* @global DPA_Achievement_Template $achievements_template Achievements template tag object
	* @return string
	*/
	function dpa_get_achievements_pagination_links() {
		global $achievements_template;

		return apply_filters( 'dpa_get_achievement_pagination_links', $achievements_template->pag_links );
	}

/**
 * Pagination link text for template tag loop
 *
 * @global DPA_Achievement_Template $achievements_template Achievements template tag object
 * @global object $bp BuddyPress global settings
 * @since 2.0
 */
function dpa_achievements_pagination_count() {
	global $achievements_template, $bp;

	$start_num = intval( ( $achievements_template->pag_page - 1 ) * $achievements_template->pag_num ) + 1;
	$from_num = bp_core_number_format( $start_num );
	$to_num = bp_core_number_format( ( $start_num + ( $achievements_template->pag_num - 1 ) > $achievements_template->total_achievement_count ) ? $achievements_template->total_achievement_count : $start_num + ( $achievements_template->pag_num - 1 ) );
	$total = bp_core_number_format( $achievements_template->total_achievement_count );

	$pagination = sprintf( __( 'Viewing Achievements %1$s to %2$s (of %3$s Achievements)', 'dpa' ), $from_num, $to_num, $total );

	if ( !empty( $_REQUEST['search_terms'] ) && 'false' != $_REQUEST['search_terms'] )
		$pagination .= sprintf( __( ' matching &ldquo;%s&rdquo;', 'dpa' ), apply_filters( 'dpa_get_achievements_search_query', stripslashes( $_REQUEST['search_terms'] ) ) );

	echo $pagination;
	?> &nbsp; <span class="ajax-loader"></span><?php
}

/**
 * Template tag version of dpa_get_achievement_id()
 *
 * @uses dpa_get_achievement_id()
 * @since 2.0
 */
function dpa_achievement_id() {
	echo dpa_get_achievement_id();
}
	/**
	 * Returns Achievement ID
	 *
	 * @since 2.0
	 * @global DPA_Achievement_Template $achievements_template Achievements template tag object
	 * @return string
	 */
	function dpa_get_achievement_id() {
		global $achievements_template;

		return apply_filters( 'dpa_get_achievement_id', (int)$achievements_template->achievement->id );
	}

/**
 * Template tag version of dpa_get_achievement_name()
 *
 * @uses dpa_get_achievement_name()
 * @since 2.0
 */
function dpa_achievement_name() {
	echo dpa_get_achievement_name();
}
	/**
	 * Returns Achievement name
	 *
	 * @since 2.0
	 * @global DPA_Achievement_Template $achievements_template Achievements template tag object
	 * @return string
	 */
	function dpa_get_achievement_name() {
		global $achievements_template;

		return apply_filters( 'dpa_get_achievement_name', $achievements_template->achievement->name );
	}

/**
 * Template tag version of dpa_get_achievement_slug()
 *
 * @uses dpa_get_achievement_slug()
 * @since 2.0
 */
function dpa_achievement_slug() {
	echo dpa_get_achievement_slug();
}
	/**
	 * Returns Achievement slug
	 *
	 * @since 2.0
	 * @global DPA_Achievement_Template $achievements_template Achievements template tag object
	 * @return string
	 */
	function dpa_get_achievement_slug() {
		global $achievements_template;

		return apply_filters( 'dpa_get_achievement_slug', $achievements_template->achievement->slug );
	}

/**
 * Template tag version of dpa_get_achievement_slug_permalink()
 *
 * @uses dpa_get_achievement_slug_permalink()
 * @since 2.0
 */
function dpa_achievement_slug_permalink() {
	echo dpa_get_achievement_slug_permalink();
}
	/**
	 * Returns Achievement permalink
	 *
	 * @since 2.0
	 * @global DPA_Achievement_Template $achievements_template Achievements template tag object
	 * @return string
	 */
	function dpa_get_achievement_slug_permalink() {
		global $achievements_template;

		return apply_filters( 'dpa_get_achievement_slug_permalink', dpa_get_achievements_permalink() . '/' . $achievements_template->achievement->slug . '/' );
	}

/**
 * Template tag version of dpa_get_achievement_description_excerpt()
 *
 * @uses dpa_get_achievement_description_excerpt()
 * @since 2.0
 */
function dpa_achievement_description_excerpt() {
	echo dpa_get_achievement_description_excerpt();
}
	/**
	 * Returns Achievement description excerpt
	 *
	 * @since 2.0
	 * @global DPA_Achievement_Template $achievements_template Achievements template tag object
	 * @return string
	*/
	function dpa_get_achievement_description_excerpt() {
		global $achievements_template;

		return apply_filters( 'dpa_get_achievement_description', bp_create_excerpt( $achievements_template->achievement->description ) );
	}

/**
 * Template tag version of dpa_get_achievement_description()
 *
 * @uses dpa_get_achievement_description()
 * @since 2.0
 */
function dpa_achievement_description() {
	echo dpa_get_achievement_description();
}
	/**
	 * Returns Achievement description
	 *
	 * @since 2.0
	 * @global DPA_Achievement_Template $achievements_template Achievements template tag object
	 * @return string
	 */
	function dpa_get_achievement_description() {
		global $achievements_template;

		return apply_filters( 'dpa_get_achievement_description', $achievements_template->achievement->description );
	}

/**
 * Template tag version of dpa_achievement_points()
 *
 * @uses dpa_get_achievement_points()
 * @since 2.0
 */
function dpa_achievement_points() {
	echo bp_core_number_format( dpa_get_achievement_points() );
}
	/**
	 * Returns Achievement points
	 *
	 * @since 2.0
	 * @global DPA_Achievement_Template $achievements_template Achievements template tag object
	 * @return string
	 */
	function dpa_get_achievement_points() {
		global $achievements_template;

		return apply_filters( 'dpa_get_achievement_points', (int)$achievements_template->achievement->points );
	}

/**
 * Returns Achievement picture ID
 *
 * @since 2.0
 * @global DPA_Achievement_Template $achievements_template Achievements template tag object
 * @return string Achievement picture id (WP Media Library)
 */
function dpa_get_achievement_picture_id() {
	global $achievements_template;

	return apply_filters( 'dpa_get_achievement_picture_id', (int)$achievements_template->achievement->picture_id );
}

/**
 * Returns Achievement site ID (only used in multisite installs)
 *
 * @since 2.0
 * @global DPA_Achievement_Template $achievements_template Achievements template tag object
 */
function dpa_get_achievement_site_id() {
	global $achievements_template;

	return apply_filters( 'dpa_get_achievement_site_id', $achievements_template->achievement->site_id );
}

/**
 * Returns Achievement group ID
 *
 * @since 2.0
 * @global DPA_Achievement_Template $achievements_template Achievements template tag object
 */
function dpa_get_achievement_group_id() {
	global $achievements_template;

	return apply_filters( 'dpa_get_achievement_group_id', (int)$achievements_template->achievement->group_id );
}

/**
 * Does this Achievement picture need to be thumbnail sized?
 *
 * @global DPA_Achievement_Template $achievements_template Achievements template tag object
 * @return bool
 */
function dpa_get_achievement_picture_is_thumbnail() {
	global $achievements_template;

	return apply_filters( 'dpa_get_achievement_picture_is_thumbnail', ( dpa_is_create_achievement_page() || dpa_is_directory_page() || ( $achievements_template->in_the_loop && ( !dpa_is_achievement_edit_page() && !dpa_is_achievement_change_picture_page() && !dpa_is_achievement_delete_page() && !dpa_is_achievement_unlocked_by_page() && !dpa_is_achievement_activity_page() && !dpa_is_achievement_grant_page() ) ) ) );
}

/**
 * Template tag version of dpa_get_achievement_picture()
 *
 * @uses dpa_get_achievement_picture()
 * @param string $size Optional; set to "thumb" to fetch thumbnail-sized picture, and "activitystream" for a thumbnail-sized picture with width/height style tags.
 * @since 2.0
 */
function dpa_achievement_picture( $size='thumb' ) {
	echo dpa_get_achievement_picture( $size );
}
	/**
	 * Returns Achievement's picture; takes into account size of image required
	 *
	 * @since 2.0
	 * @global DPA_Achievement_Template $achievements_template Achievements template tag object
	 * @global int $blog_id Site ID
	 * @global object $bp BuddyPress global settings
	 * @param string $size Optional; set to "thumb" to fetch thumbnail-sized picture, and "activitystream" for a thumbnail-sized picture with width/height style tags.
	 * @return string HTML image tag
	 */
	function dpa_get_achievement_picture( $size='' ) {
		global $achievements_template, $blog_id, $bp;

		$achievement_slug = dpa_get_achievement_slug();
		$achievement_id = dpa_get_achievement_id();

		if ( 'thumb' == $size || 'activitystream' == $size )
			$is_thumbnail = true;
		else
			$is_thumbnail = dpa_get_achievement_picture_is_thumbnail();

		if ( ( $picture_id = dpa_get_achievement_picture_id() ) < 1 ) {
			if ( empty( $bp->grav_default->user ) )
				$default_grav = 'wavatar';
			elseif ( 'mystery' == $bp->grav_default->user )
				$default_grav = apply_filters( 'bp_core_mysteryman_src', BP_PLUGIN_URL . '/bp-core/images/mystery-man.jpg' );
			else
				$default_grav = $bp->grav_default->user;

			if ( 'thumb' == $size )
				$grav_size = apply_filters( 'dpa_get_achievement_picture_gravatar_width', BP_AVATAR_THUMB_WIDTH, 'thumb' );
			elseif ( 'activitystream' == $size )
				$grav_size = apply_filters( 'dpa_get_achievement_picture_gravatar_width', 20, 'activitystream' );
			else
				$grav_size = apply_filters( 'dpa_get_achievement_picture_gravatar_width', BP_AVATAR_FULL_WIDTH, 'full' );

			$email = apply_filters( 'bp_core_gravatar_email', $achievement_slug . '@' . $bp->root_domain, $achievement_id, 'achievements' );

			if ( is_ssl() )
				$host = 'https://secure.gravatar.com/avatar/';
			else
				$host = 'http://www.gravatar.com/avatar/';

			$avatar_url = apply_filters( 'bp_gravatar_url', $host ) . md5( $email ) . '?d=' . $default_grav . '&amp;s=' . $grav_size;

		} else {
			if ( $cached_urls = wp_cache_get( 'dpa_achievement_picture_urls', 'dpa' ) && isset( $cached_urls ) &&
					 is_array( $cached_urls ) && isset( $cached_urls[$picture_id] ) && $cached_urls[$picture_id] ) {
				$avatar_url = $cached_urls[$picture_id];

			} else {
				if ( $is_nonroot_multisite = ( is_multisite() && BP_ROOT_BLOG != $blog_id ) )
					switch_to_blog( BP_ROOT_BLOG );  // Necessary evil

				list( $avatar_url, $avatar_width, $avatar_height, $is_intermediate ) = image_downsize( $picture_id, 'large' );

				if ( $is_nonroot_multisite )
					restore_current_blog();

				if ( !is_array( $cached_urls ) )
					$cached_urls = array( $picture_id => $avatar_url );
				else
					$cached_urls[$picture_id] = $avatar_url;

				$grav_size = 0;
				wp_cache_set( 'dpa_achievement_picture_urls', $cached_urls, 'dpa' );
			}
		}

		$style = '';
		if ( 'activitystream' == $size && ( 'mystery' == $bp->grav_default->user || $picture_id > 0 ) )
			$style = 'width="20" height="20"';

		if ( $is_thumbnail )
			$picture_type = "avatar-thumbnail";
		else
			$picture_type = "avatar-full";

		$url = '<img src="' . esc_url( $avatar_url ) . '" alt="' . esc_attr( dpa_get_achievement_name() ) . '" title="' . esc_attr( dpa_get_achievement_name() ) . '" ' . $style . ' class="avatar' . esc_attr( ' achievement-' . $achievement_slug . '-avatar ' ) . $picture_type . '" />';
		return apply_filters( 'dpa_get_achievement_picture', $url, $achievement_id, $picture_id, $grav_size, $style );
	}

/**
 * Template tag version of dpa_get_achievement_picture_width()
 *
 * @uses dpa_get_achievement_picture_width()
 * @since 2.0
*/
function dpa_achievement_picture_width() {
	echo dpa_get_achievement_picture_width();
}
	/**
	 * Returns width of the Achievement's picture
	 *
	 * @since 2.0
	 * @global DPA_Achievement_Template $achievements_template Achievements template tag object
	 * @return int
	 */
	function dpa_get_achievement_picture_width() {
		$is_thumbnail = dpa_get_achievement_picture_is_thumbnail();
		$picture_id = dpa_get_achievement_picture_id();

		if ( $picture_id < 1 ) {
			if ( $is_thumbnail )
				$width = apply_filters( 'dpa_get_achievement_picture_gravatar_width', BP_AVATAR_THUMB_WIDTH, 'thumb' );
			else
				$width = apply_filters( 'dpa_get_achievement_picture_gravatar_width', BP_AVATAR_FULL_WIDTH, 'full' );

		} else {
			if ( $is_thumbnail ) {
				$width = apply_filters( 'dpa_get_achievement_picture_width', BP_AVATAR_THUMB_WIDTH, 'thumb' );

			} else {
				list( $url, $width, $height ) = wp_get_attachment_image_src( $picture_id, 'full' );
				$width = apply_filters( 'dpa_get_achievement_picture_width', $width, 'full' );
			}
		}

		return apply_filters( 'dpa_get_achievement_picture_width', $width );
	}

/**
 * Is the current Achievement active?
 *
 * @since 2.0
 * @global DPA_Achievement_Template $achievements_template Achievements template tag object
 * @return bool
 */
function dpa_get_achievement_is_active() {
	global $achievements_template;

	return apply_filters( 'dpa_get_achievement_is_active', (bool) $achievements_template->achievement->is_active );
}

/**
 * Is the current Achievement active but hidden?
 *
 * @since 2.0
 * @global DPA_Achievement_Template $achievements_template Achievements template tag object
 * @return bool
 */
function dpa_get_achievement_is_hidden() {
	global $achievements_template;

	return apply_filters( 'dpa_get_achievement_is_active', dpa_get_achievement_is_active() && 2 == $achievements_template->achievement->is_active );
}

/**
 * Template tag version of dpa_get_achievement_unlocked_date()
 *
 * @uses dpa_get_achievement_unlocked_date()
 * @since 2.0
 */
function dpa_achievement_unlocked_date() {
	echo dpa_get_achievement_unlocked_date();
}
	/**
	 * Returns the date of when this Achievement was unlocked. This may be null if the user hasn't unlocked it
	 *
	 * @since 2.0
	 * @global DPA_Achievement_Template $achievements_template Achievements template tag object
	 * @return string|null Achievement Unlock Date
	 */
	function dpa_get_achievement_unlocked_date() {
		global $achievements_template;

		return apply_filters( 'dpa_get_achievement_unlocked_date', mysql2date( get_option( 'date_format' ), $achievements_template->achievement->achieved_at ) );
	}

/**
 * Template tag version of dpa_get_achievement_unlocked_ago()
 *
 * @uses dpa_get_achievement_unlocked_ago()
 * @since 2.0
*/
function dpa_achievement_unlocked_ago() {
	echo dpa_get_achievement_unlocked_ago();
}
	/**
	 * Returns a Human-readable representation of when this Achievement was unlocked, i.e. "four days ago"
	 *
	 * @since 2.0
	 * @global DPA_Achievement_Template $achievements_template Achievements template tag object
	 * @return string
	 */
	function dpa_get_achievement_unlocked_ago() {
		global $achievements_template;

		return apply_filters( 'dpa_get_achievement_unlocked_date', sprintf( __( ' %s ago', 'dpa' ), bp_core_time_since( $achievements_template->achievement->achieved_at ) ) );
	}

/**
 * Template tag version of dpa_get_achievement_action_count()
 *
 * @uses dpa_get_achievement_action_count()
 * @since 2.0
 */
function dpa_achievement_action_count() {
	echo dpa_get_achievement_action_count();
}
	/**
	 * Returns Achievement's Action count (how many times this Achievement has to be met before it is unlocked)
	 *
	 * @since 2.0
	 * @global DPA_Achievement_Template $achievements_template Achievements template tag object
	 * @return string
	 */
	function dpa_get_achievement_action_count() {
		global $achievements_template;

		return apply_filters( 'dpa_get_achievement_action_count', (int)$achievements_template->achievement->action_count );
	}

/**
 * Has this Achievement been unlocked by the user?
 *
 * @since 2.0
 * @global DPA_Achievement_Template $achievements_template Achievements template tag object
 * @return bool
 */
function dpa_is_achievement_unlocked() {
	global $achievements_template;
	
	if ( !bp_loggedin_user_id() )
		return;

	return ( !is_null( $achievements_template->achievement->achieved_at ) );
}

/**
 * Is this Achievement a "badge" type? (i.e. associated with no action)
 *
 * @since 2.0
 * @global DPA_Achievement_Template $achievements_template Achievements template tag object
 * @return bool
 */
function dpa_is_achievement_a_badge() {
	global $achievements_template;

	return apply_filters( 'dpa_get_is_achievement_a_badge', ( -1 == $achievements_template->achievement->action_id ) ) ;
}

/**
 * Template tag version of dpa_get_achievement_type()
 *
 * @since 2.0
 * @uses dpa_get_achievement_type()
 * @return bool
 */
function dpa_achievement_type() {
	echo dpa_get_achievement_type();
}
	/**
	 * Return the human-readable type of this Achievement
	 *
	 * @since 2.0
	 * @return string
	 */
	function dpa_get_achievement_type() {
		if ( dpa_is_achievement_a_badge() ) {
			if ( dpa_get_achievement_is_hidden() )
				$type = __( "Award, hidden", 'dpa' );
			else
				$type = __( "Award", 'dpa' );

		} else {
			if ( dpa_get_achievement_is_hidden() )
				$type = __( "Event, hidden", 'dpa' );
			else
				$type = __( "Event", 'dpa' );
		}

		return apply_filters( 'dpa_get_achievement_type', $type );
	}

/**
 * Template tag version of dpa_get_achievement_counter()
 *
 * @since 2.0
 * @uses dpa_get_achievement_counter()
 * @return bool
 */
function dpa_achievement_counter() {
	echo dpa_get_achievement_counter();
}
	/**
	 * Return the counter of this Achievement. Used to count progress towards an Achievement that happens a certain number of a repeated Action.
	 * If an Achievement is a "badge" type, or if the Achievement only requires an Action to happen once, no value is stored.
	 *
	 * @since 2.0
   * @global object $bp BuddyPress global settings
	 * @return bool 
	 */
	function dpa_get_achievement_counter() {
		global $bp;

		if ( !empty( $bp->displayed_user->id ) )
			$user_id = $bp->displayed_user->id;
		elseif ( !empty( $bp->loggedin_user->id ) )
			$user_id = $bp->loggedin_user->id;
		else
			$user_id = 0;

		$counters = get_user_meta( $user_id, 'achievements_counters', true );
		return apply_filters( 'dpa_get_achievement_counter', !empty( $counters[dpa_get_achievement_id()] ) ? $counters[dpa_get_achievement_id()] : 0 );
	}

/**
 * Template tag version of dpa_get_achievement_progress_bar_width()
 *
 * @uses dpa_get_achievement_progress_bar_width()
 * @since 2.0
 */
function dpa_achievement_progress_bar_width() {
	echo dpa_get_achievement_progress_bar_width();
}
	/**
	 * Returns the width of the progress bar for Achievements that are not "badge" type and which require
	 * an Achievement's Action to happen multiple times.
	 *
	 * @since 2.0
   * @global object $bp BuddyPress global settings
	 * @return int
	 */
	function dpa_get_achievement_progress_bar_width() {
		global $bp;

		if ( !$bp->displayed_user->id && !$bp->loggedin_user->id )
			return 0;

		if ( dpa_is_achievement_unlocked() ) {
			$percentage = 1;

		} else {
			$achievement_count = dpa_get_achievement_action_count();
			if ( $achievement_count <= 1 ) {
				$percentage = 0;

			} else {
				if ( !$counter = dpa_get_achievement_counter() )
					$percentage = 0;
				else {
					$percentage = min( $counter / $achievement_count, 1 );
				}
			}
		}

		return apply_filters( 'dpa_get_achievement_progress_bar_width', intval( dpa_get_achievement_picture_width() * $percentage ), $percentage );
	}

/**
 * Adds form validation errors to the add/edit pages
 *
 * @since 2.0
 * @global WP_Error $achievements_errors Achievement creation error object
 * @see DPA_Achievement::validate_achievement_details()
 * @param string $form_field Name of the form field
 */
function dpa_addedit_warning( $form_field ) {
	global $achievements_errors;

	if ( !is_wp_error( $achievements_errors ) || empty( $achievements_errors->errors[$form_field] ) )
		return;

	foreach ( (array) $achievements_errors->errors[$form_field] as $error ) : ?>
	 	<p class="error"><?php echo apply_filters( 'dpa_addedit_warning', $error ) ?>&nbsp;</p>
	<?php
	endforeach;
}

/**
 * Template tag version of dpa_get_addedit_value()
 *
 * @uses dpa_get_addedit_value()
 * @param string $form_field Name of the form field
 * @since 2.0
 */
function dpa_addedit_value( $form_field ) {
	echo dpa_get_addedit_value( $form_field );
}
	/**
	 * Returns form fields' values. As used on the create/edit pages when the form submission
	 * fails validation, the page reloads with its previous values.
	 *
	 * @since 2.0
	 * @global object $bp BuddyPress global settings
	 * @param string $form_field Name of the form field
	 * @return string
	 */
	function dpa_get_addedit_value( $form_field ) {
		global $bp;

		$value = '';

		if ( 'is_active' == $form_field && $bp->achievements->current_achievement->is_active ) {
			$value = 'checked="checked"';

		} elseif ( 'is_hidden' == $form_field ) {
			if ( 2 == $bp->achievements->current_achievement->is_active )
				$value = 'checked="checked"';

		} else {
			$value = $bp->achievements->current_achievement->{$form_field};
		}

		return apply_filters( 'dpa_get_addedit_value', $value );
	}

/**
 * Template tag version of dpa_get_addedit_achievement_type()
 *
 * @uses dpa_get_addedit_achievement_type()
 * @since 2.0
*/
function dpa_addedit_achievement_type() {
	echo dpa_get_addedit_achievement_type();
}
	/**
	 * Returns the HTML for the radio buttons on the create/edit pages
	 *
	 * @since 2.0
   * @global object $bp BuddyPress global settings
	 * @return string
	 */
	function dpa_get_addedit_achievement_type() {
		global $bp;

		if ( !empty( $_POST['achievement_type'] ) ) {
			if ( 'badge' == stripslashes( $_POST['achievement_type'] ) ) {
				$badge = ' checked="checked"';
				$event = '';
			} else {
				$badge = '';
				$event = ' checked="checked"';
			}

		} else {
			if ( $bp->achievements->current_achievement->action_id < 1 ) {
				$badge = ' checked="checked"';
				$event = '';
			} else {
				$badge = '';
				$event = ' checked="checked"';
			}
		}

		return apply_filters( 'dpa_get_addedit_achievement_type', '
		<label for="type_badge"><input type="radio" name="achievement_type" id="type_badge" value="badge"' . $badge . ' />' . __( 'Award', 'dpa' ) . '</label>
		<label for="type_event"><input type="radio" name="achievement_type" id="type_event" value="event"' . $event . ' />' . __( 'Event', 'dpa' ) . '</label>' );
	}

/**
 * Template tag version of dpa_get_addedit_achievement_type_value()
 * p.s. Possibly the worse function name ever.
 *
 * @uses dpa_get_addedit_achievement_type_value()
 * @since 2.0
 */
function dpa_addedit_achievement_type_value() {
	echo dpa_get_addedit_achievement_type_value();
}
	/**
	 * Returns the CSS classes of the "Award when...", "happens when...", "number of times..." boxes
	 * on the create/edit Achievement pages. This is done so that, depending on what type of Achievement
	 * is selected, the irrelevant form fields are hidden on page load.
	 *
	 * @since 2.0
	 * @global object $bp BuddyPress global settings
	 * @return string
	 */
	function dpa_get_addedit_achievement_type_value() {
		global $bp;

		if ( !empty( $_POST['achievement_type'] ) )
			if ( 'badge' == stripslashes( $_POST['achievement_type'] ) )
				return 'initially_hidden';
			else
				return '';

		if ( $bp->achievements->current_achievement->action_id < 1 )
			return 'initially_hidden';

		return '';
	}

/**
 * Template tag version of dpa_get_addedit_action_groups()
 *
 * @uses dpa_get_addedit_action_groups()
 * @since 2.0
 */
function dpa_addedit_action_groups() {
	echo dpa_get_addedit_action_groups();
}
	/**
	 * Returns the HTML for the groups dropdown box on the create/edit Achievement pages. From experience with Welcome Pack,
	 * BuddyPress' groups loop uses too much memory on large databases when we only need two pieces of data per record.
	 *
	 * @global object $bp BuddyPress global settings
	 * @global wpdb $wpdb WordPress database object
	 * @return string
	 * @since 2.0
	 */
	function dpa_get_addedit_action_groups() {
		global $bp, $wpdb;

		$groups = $wpdb->get_results( $wpdb->prepare( "SELECT id, name FROM {$bp->groups->table_name} ORDER BY name ASC" ) );
		$current_group_id = $bp->achievements->current_achievement->group_id;
		$selected = ( $current_group_id < 1 ) ? 'selected="selected"' : '';
		$options = array( sprintf( '<option value="%1$d"%2$s>%3$s</option>', apply_filters( 'dpa_get_achievement_group_id', -1 ), $selected, __( '(All groups)', 'dpa' ) ) );

		foreach ( $groups as $group ) {
			$selected = ( $current_group_id == $group->id ) ? 'selected="selected"' : '';
			$options[] = sprintf( '<option value="%1$d"%2$s>%3$s</option>', apply_filters( 'dpa_get_achievement_group_id', (int)$group->id ), $selected, apply_filters( 'bp_get_group_name', $group->name ) );
		}

		return implode( '', apply_filters( 'dpa_get_addedit_action_multisites', $options ) );
	}

/**
 * Template tag version of dpa_get_grant_achievement_userlist()
 *
 * @uses dpa_get_grant_achievement_userlist()
 * @since 2.0
 */
function dpa_grant_achievement_userlist() {
	echo dpa_get_grant_achievement_userlist();
}
	/**
	 * Returns the HTML for the users dropdown box on the grant Achievement page. From experience with Welcome Pack,
	 * BuddyPress' members loop uses too much memory on large databases when we only need two pieces of data per record.
	 *
	 * @global object $bp BuddyPress global settings
	 * @global wpdb $wpdb WordPress database object
	 * @return string
	 * @since 2.0
	 */
	function dpa_get_grant_achievement_userlist() {
		global $bp, $wpdb;

		$options = array();

		if ( is_multisite() )
			$column = "spam";
		else
			$column = "user_status";

		$members = $wpdb->get_results( $wpdb->prepare( "SELECT ID, display_name FROM {$wpdb->users} WHERE {$column} = 0 ORDER BY display_name ASC" ) );
		foreach ( $members as $member )
			$options[] = sprintf( '<li><input type="checkbox" name="members[]" id="m-%1$d" value="%2$d" />%3$s', apply_filters( 'bp_get_member_user_id', $member->ID ), apply_filters( 'bp_get_member_user_id', $member->ID ), apply_filters( 'bp_get_member_user_nicename', $member->display_name ) );

		return implode( "\n", apply_filters( 'dpa_get_achievement_grant_userlist', $options ) );
	}

/**
 * Template tag version of dpa_get_addedit_action_multisites()
 *
 * @uses dpa_get_addedit_action_multisites()
 * @since 2.0
 */
function dpa_addedit_action_multisites() {
	echo dpa_get_addedit_action_multisites();
}
	/**
	 * Returns the HTML for the multisites dropdown box on the create/edit Achievement pages
	 *
	 * @since 2.0
	 * @global unknown $blogs_template
	 * @global object $bp BuddyPress global settings
	 * @global wpdb $wpdb WordPress database object
	 * @return string
	 */
	function dpa_get_addedit_action_multisites() {
		global $blogs_template, $bp, $wpdb;

		$current_site_id = $bp->achievements->current_achievement->site_id;
		$selected = ( $current_site_id < 1 ) ? 'selected="selected"' : '';
		$list = array( sprintf( '<option value="%1$d"%2$s>%3$s</option>', apply_filters( 'dpa_get_achievement_site_id', -1 ), $selected, __( '(All sites)', 'dpa' ) ) );

		$sites = $wpdb->get_col( $wpdb->prepare( "SELECT blog_id as id FROM {$wpdb->blogs} WHERE site_id = %d", $wpdb->siteid ) );
		foreach ( $sites as $site_id ) {
			$selected = ( $current_site_id == $site_id ) ? 'selected="selected"' : '';
			$list[] = sprintf( '<option value="%1$d"%2$s>%3$s</option>', apply_filters( 'dpa_get_achievement_site_id', (int) $site_id ), $selected, apply_filters( 'bp_get_blog_name', get_blog_option( $site_id, 'blogname' ) ) );
		}

		return implode( '', apply_filters( 'dpa_get_addedit_action_multisites', $list ) );
	}

/**
 * Template tag version of dpa_get_addedit_action_descriptions()
 *
 * @uses dpa_get_addedit_action_descriptions()
 * @since 2.0
 */
function dpa_addedit_action_descriptions() {
	echo dpa_get_addedit_action_descriptions();
}
	/**
	 * Returns the HTML for the actions dropdown box on the create/edit Achievement pages
	 *
	 * @since 2.0
   * @global object $bp BuddyPress global settings
	 * @return string
	 * @todo Sort the option group labels alphabetically.
	 */
	function dpa_get_addedit_action_descriptions() {
		global $bp;

		$temp_actions = array();
		$current_action_id = $bp->achievements->current_achievement->action_id;

		// Get the actions
		$actions = dpa_get_actions();
		foreach ( $actions as $action ) {
			if ( !isset( $temp_actions[$action->category] ) )
				$temp_actions[$action->category] = array( array( 'id' => $action->id, 'name' => $action->name, 'description' => $action->description, 'is_group_action' => (bool)$action->is_group_action ) );
			else
				$temp_actions[$action->category][] = array( 'id' => $action->id, 'name' => $action->name, 'description' => $action->description, 'is_group_action' => (bool)$action->is_group_action );
		}
		$actions = $temp_actions;

		// Get the option group titles
		$values = array( sprintf( '<option value="0">%s</option>', __( '----', 'dpa' ) ) );
		foreach ( $actions as $category => $category_actions ) {
			$category_is_active = true;

			switch ( $category ) {
				// BP/WP core
				case 'activitystream':
					$category_name = __( 'Activity Streams', 'dpa' );
					$category_is_active = bp_is_active( 'activity' );
				break;

				case 'blog':
					$category_name = __( 'Site', 'dpa' );
				break;

				case 'forum':
					$category_name = __( 'Forums', 'dpa' );
					$category_is_active = bp_is_active( 'forums' );
				break;

				case 'groups':
					$category_name = __( 'Groups', 'dpa' );
					$category_is_active = bp_is_active( 'groups' );
				break;

				case 'members':
					$category_name = __( 'Members', 'dpa' );
					$category_is_active = bp_is_active( 'friends' );
				break;

				case 'messaging':
					$category_name = __( 'Private Messaging', 'dpa' );
					$category_is_active = bp_is_active( 'messages' );
				break;

				case 'multisite':
					$category_name = __( 'WordPress Multisite', 'dpa' );
					$category_is_active = is_multisite();
				break;

				case 'profile':
					$category_name = __( 'Profile &amp; Account', 'dpa' );
					$category_is_active = bp_is_active( 'xprofile' );
				break;

				// Plugins
				case 'achievements':
					$category_name = __( 'Plugin: Achievements', 'dpa' );
				break;

				case 'bpmoderation':
					require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
					$category_is_active = is_plugin_active( 'bp-moderation/bpModLoader.php' );
					$category_name = __( 'Plugin: BuddyPress Moderation', 'dpa' );
				break;

				case 'bpprivacy':
					$category_name = __( "Plugin: BP-Privacy", 'dpa' );
					$category_is_active = defined( 'BP_PRIVACY_IS_INSTALLED' );
				break;

				case 'buddypresslinks':
					$category_name = __( 'Plugin: BuddyPress Links', 'dpa' );
					$category_is_active = is_plugin_active( 'buddypress-links/buddypress-links.php' );
				break;

				case 'buddystream':
					$category_name = __( 'Plugin: BuddyStream', 'dpa' );
					$category_is_active = defined( 'BP_BUDDYSTREAM_IS_INSTALLED' );
				break;

				case 'inviteanyone':
					$category_name = __( 'Plugin: Invite Anyone', 'dpa' );
					$category_is_active = defined( 'BP_INVITE_ANYONE_VER' );
				break;

				case 'eventpress':
					$category_name = __( 'Plugin: EventPress', 'dpa' );
					$category_is_active = defined( 'EP_BP' );
				break;

				case 'jes':
					$category_name = __( 'Plugin: Jet Event System for BuddyPress', 'dpa' );
					$category_is_active = defined( 'JES_EVENTS_VERSION' );
				break;

				default:
					$category_name = __( 'Other', 'dpa' );
				break;
			}

			// Make the <option> elements
			if ( !$category_is_active = apply_filters( 'dpa_get_addedit_action_descriptions_category_is_active', $category_is_active, $category ) )
				continue;

			$values[] = sprintf( '<optgroup label="%s">', apply_filters( 'dpa_get_addedit_action_descriptions_category_name', $category_name, $category ) );

			foreach ( $category_actions as $category_action ) {
				$selected = '';
				$class = '';

				if ( $current_action_id == $category_action['id'] )
					$selected = ' selected="selected"';					

				if ( $category_action['is_group_action'] )
					$class = 'class="group"';

				$values[] = sprintf( '<option %1$s value="%2$d"%3$s>%4$s</option>', $class, apply_filters( 'dpa_get_achievement_action_id', (int)$category_action['id'] ), $selected, apply_filters( 'dpa_get_addedit_action_description', $category_action['description'] ) );
			}

			$values[] = '</optgroup>';
		}

		return implode( '', apply_filters( 'dpa_get_addedit_action_descriptions', $values, $actions ) );
	}

/**
 * Template tag version of dpa_get_total_achievement_count()
 *
 * @uses dpa_get_total_achievement_count()
 * @since 2.0
 */
function dpa_total_achievement_count() {
	echo dpa_get_total_achievement_count();
}
	/**
	 * Returns count of how many (active) Achievements there are.
	 *
	 * @since 2.0
	 * @return string
	 */
	function dpa_get_total_achievement_count() {
		return apply_filters( 'dpa_get_total_achievement_count', dpa_get_total_achievements_count() );
	}

/**
 * Template tag version of dpa_get_total_achievement_count_for_user()
 *
 * @param int $user_id
 * @uses dpa_get_total_achievement_count_for_user()
 * @since 2.0
 */
function dpa_total_achievement_count_for_user( $user_id = false ) {
	echo dpa_get_total_achievement_count_for_user( $user_id );
}
	/**
	 * Returns count of how many (activate) Achievement the current user has unlocked.
	 *
	 * @since 2.0
	 * @return string
	 */
	function dpa_get_total_achievement_count_for_user( $user_id = false ) {
		return apply_filters( 'dpa_get_total_achievement_count_for_user', dpa_get_total_achievements_count_for_user( $user_id ) );
	}

/**
 * Template tag version of dpa_get_member_achievements_score()
 *
 * @param int $user_id
 * @uses dpa_get_member_achievements_score()
 * @since 2.0
 */
function dpa_member_achievements_score( $user_id = false ) {
	echo dpa_get_member_achievements_score( $user_id );
}
	/**
	 * Returns user's Achievement Score
	 *
	 * @since 2.0
	 * @global object $bp BuddyPress global settings
	 * @return string
	 */
	function dpa_get_member_achievements_score( $user_id = false ) {
		global $bp;

		if ( !$user_id )
			$user_id = $bp->loggedin_user->id;

		return apply_filters( 'dpa_get_member_achievements_score', (int)get_user_meta( $user_id, 'achievements_points', true ) );
	}

/**
 * Template tag version of dpa_get_achievements_quickadmin()
 *
 * @uses dpa_get_achievements_quickadmin()
 * @since 2.0
 */
function dpa_achievements_quickadmin() {
	echo dpa_get_achievements_quickadmin();
}
	/**
	 * Returns the on-hover Quick Admin controls which appear for admin users on the Directory pages
	 *
	 * @since 2.0
   * @global object $bp BuddyPress global settings
	 * @return string
	 */
	function dpa_get_achievements_quickadmin() {
		global $bp;

		$items = array();

		if ( !dpa_is_directory_page() )
			return apply_filters( 'dpa_get_achievements_quickadmin', '', $items );

		$url = dpa_get_achievement_slug_permalink();

		if ( dpa_permission_can_user_change_picture() )
    	$items[] = sprintf( '<a href="%1$s">%2$s</a>', $url . DPA_SLUG_ACHIEVEMENT_CHANGE_PICTURE, __( 'Change Picture', 'dpa' ) );

		if ( dpa_permission_can_user_delete() )
    	$items[] = sprintf( '<a href="%1$s">%2$s</a>', $url . DPA_SLUG_ACHIEVEMENT_DELETE, __( 'Delete', 'dpa' ) );

		if ( dpa_permission_can_user_edit() )
    	$items[] = sprintf( '<a href="%1$s">%2$s</a>', $url . DPA_SLUG_ACHIEVEMENT_EDIT, __( 'Edit', 'dpa' ) );

		if ( dpa_permission_can_user_grant() )
    	$items[] = sprintf( '<a href="%1$s">%2$s</a>', $url . DPA_SLUG_ACHIEVEMENT_GRANT, __( 'Give', 'dpa' ) );

		if ( !$items )
			return apply_filters( 'dpa_get_achievements_quickadmin', '', $items );

		return apply_filters( 'dpa_get_achievements_quickadmin', '<span>' . implode( '</span> | <span>', $items ) . '</span>', $items );
	}

/**
 * Template tag version of dpa_get_achievements_permalink()
 *
 * @uses dpa_get_achievements_permalink()
 * @since 2.0
 */
function dpa_achievements_permalink() {
	echo dpa_get_achievements_permalink();
}
	/**
	 * Returns the main Achievements permalink (to the Directory)
	 *
	 * @since 2.0
	 * @see dpa_get_achievement_slug_permalink()
	 * @return string
	 */
	function dpa_get_achievements_permalink() {
		global $bp;

		return apply_filters( 'dpa_get_achievements_permalink', trailingslashit( bp_get_root_domain() ) . bp_get_root_slug( $bp->achievements->slug ) );
	}

/**
 * Template tag version of dpa_get_achievement_directory_class()
 *
 * @uses dpa_get_achievement_directory_class()
 * @since 2.0
 */
function dpa_achievement_directory_class() {
	echo dpa_get_achievement_directory_class();
}
	/**
	 * Returns the CSS classes for each Achievement in the Directory
	 *
	 * @since 2.0
	 * @return string
	 */
	function dpa_get_achievement_directory_class() {
		$class = esc_attr( "achievement achievement-" . dpa_get_achievement_slug() );

		if ( dpa_is_achievement_unlocked() )
			$class .= " unlocked";

		if ( !dpa_get_achievement_is_active() )
			$class .= " inactive";

		if ( dpa_get_achievement_is_hidden() )
			$class .= " active-but-hidden";

		return apply_filters( 'dpa_get_achievement_directory_class', $class );
	}
	
/**
 * Search form template tag for the Directory
 *
 * @since 2.0
 */
function dpa_directory_achievements_search_form() {
	$search_value = '';
	if ( !empty( $_REQUEST['s'] ) )
	 	$search_value = stripslashes( $_REQUEST['s'] );
?>
	<form action="" method="get" id="search-achievements-form">
		<label><input type="text" name="s" placeholder="<?php echo esc_attr( __( 'Search Achievements', 'dpa' ) ) ?>" id="achievements_search" value="<?php echo esc_attr( $search_value ) ?>" /></label>
		<input type="submit" id="achievements_search_submit" name="achievements_search_submit" value="<?php _e( 'Search', 'dpa' ) ?>" />
	</form>
<?php
}

/**
 * Template tag version of dpa_get_achievement_unlocked_count()
 *
 * @since 2.0
 * @uses dpa_get_achievement_unlocked_count()
 * @param int $achievement_id Achievement ID
 */
function dpa_achievement_unlocked_count( $achievement_id = 0 ) {
	echo dpa_get_achievement_unlocked_count( $achievement_id );
}
	/**
	 * How many times this Achievement has been unlocked?
	 *
	 * @since 2.0
   * @param int $achievement_id Achievement ID
	 * @return int
	 */
	function dpa_get_achievement_unlocked_count( $achievement_id = 0 ) {
		if ( !$achievement_id )
			$achievement_id = dpa_get_achievement_id();

		$achievement_meta = dpa_get_achievements_meta();
		if ( isset( $achievement_meta[$achievement_id] ) && $achievement_meta[$achievement_id]['no_of_unlocks'] ) {
			return apply_filters( 'dpa_get_achievement_unlocked_count', (int)$achievement_meta[$achievement_id]['no_of_unlocks'] );

		} else {
			return apply_filters( 'dpa_get_achievement_unlocked_count', 0 );
		}
	}

/**
 * Template tag version of dpa_get_achievement_progress_bar_alt_text()
 *
 * @since 2.0
 */
function dpa_achievement_progress_bar_alt_text() {
	echo dpa_get_achievement_progress_bar_alt_text();
}
	/**
	 * Returns the 'title' text for a user's Achievement progress bar, for Achievements with an Action Count > 0
	 *
	 * @since 2.0
	 * @see dpa_get_achievement_progress_bar_width()
	 * @global object $bp BuddyPress global settings
	 * @return string
	 */
	function dpa_get_achievement_progress_bar_alt_text() {
		global $bp;

		if ( !$bp->displayed_user->id && !$bp->loggedin_user->id )
			return '';

		if ( dpa_is_achievement_unlocked() ) {
			return __( "You've unlocked this Achievement! Way to go!", 'dpa' );

		} else {
			$achievement_count = dpa_get_achievement_action_count();

			if ( $achievement_count > 1 ) {
				if ( !$counter = dpa_get_achievement_counter() )
					$percentage = 0;
				else
					$percentage = min( ceil( ( $counter / $achievement_count ) * 100 ), 100 );

				if ( 100 == $percentage )
					return sprintf( __( "You've very nearly unlocked this Achievement, keep going!", 'dpa' ), $percentage );
				else
					return sprintf( __( "You're about %d%% of the way towards unlocking this Achievement, keep going!", 'dpa' ), $percentage );

			} else {
				return '';
			}
		}
	}

/**
 * Pagination text for the change picture page
 *
 * @global WP_Query $wp_query WordPress query object
 * @since 2.0
 */
function dpa_change_picture_pagination_count() {
	global $wp_query;

	$start_num = intval( ( dpa_change_picture_get_page() - 1 ) * $wp_query->query_vars['posts_per_page'] ) + 1;
	$from_num = bp_core_number_format( $start_num );
	$to_num = bp_core_number_format( ( $start_num + ( $wp_query->query_vars['posts_per_page'] - 1 ) > $wp_query->found_posts ) ? $wp_query->found_posts : $start_num + ( $wp_query->query_vars['posts_per_page'] - 1 ) );
	$total = bp_core_number_format( $wp_query->found_posts );

	printf( __( 'Viewing picture %1$s to %2$s (of %3$s pictures)', 'dpa' ), $from_num, $to_num, $total );
}

/**
 * Pagination links for the change picture page
 *
 * @global WP_Query $wp_query WordPress query object
 * @since 2.0
 */
function dpa_change_picture_pagination() {
	global $wp_query;

	$pag_page = !empty( $_REQUEST['cp_page'] ) ? intval( $_REQUEST['cp_page'] ) : 1;
	$pag_links = paginate_links( array(
		'base' => add_query_arg( array( 'cp_page' => '%#%' ) ),
		'format' => '',
		'total' => ceil( $wp_query->found_posts / $wp_query->query_vars['posts_per_page'] ),
		'current' => $pag_page,
		'prev_text' => '&larr;',
		'next_text' => '&rarr;'
	) );

	echo $pag_links;
}

/**
 * Returns the current page number; used for the pagination on the change picture page
 *
 * @return int
 * @see dpa_change_picture_pagination_count()
 * @since 2.0
 */
function dpa_change_picture_get_page() {
	if ( empty( $_GET['cp_page'] ) || !$page = (int)$_GET['cp_page'] )
		$page = 1;

	return $page;
}

/**
 * Are there any pictures in the WP Media Library for the change picture page?
 *
 * @global WP_Query $wp_query WordPress query object
 * @since 2.0
 * @return bool
 */
function dpa_change_picture_has_pictures() {
	global $wp_query;

	return ( $wp_query->found_posts );
}

/**
 * Are there lots of pictures in the media library, and has no search string been set in the wp-admin?
 *
 * @global WP_Query $wp_query WordPress query object
 * @return bool
 * @since 2.0
 */
function dpa_change_picture_has_manylots() {
	global $wp_query;

	$settings = get_blog_option( BP_ROOT_BLOG, 'achievements' );

	return ( ceil( $wp_query->found_posts / $wp_query->query_vars['posts_per_page'] ) > 3 && ( !isset( $settings['mediakeywords'] ) || !$settings['mediakeywords'] ) );
}

/**
 * Get query strings for Change Picture page
 *
 * @return string
 * @since 2.0
 */
function dpa_change_picture_get_query() {
	$settings = get_blog_option( BP_ROOT_BLOG, 'achievements' );

	$search_term = '';
	if ( !empty( $settings['mediakeywords'] ) )
		$search_term = '&s=' . $settings['mediakeywords'];

	return apply_filters( 'dpa_change_picture_get_query', 'post_type=attachment&post_status=publish,inherit&post_mime_type=image/jpeg,image/jpg,image/gif,image/png&posts_per_page=16&paged=' . dpa_change_picture_get_page() . $search_term );
}

/**
 * Template tag version of dpa_get_achievement_activity_feed_link()
 *
 * @since 2.0
 * @uses dpa_get_achievement_activity_feed_link()
 */
function dpa_achievement_activity_feed_link() {
	echo dpa_get_achievement_activity_feed_link();
}
	/**
	 * Returns the link of an Achievement's activity stream RSS feed.
	 *
	 * @since 2.0
	 * @global object $bp BuddyPress global settings
	 * @return string
	 */
	function dpa_get_achievement_activity_feed_link() {
		global $bp;

		return apply_filters( 'dpa_get_achievement_activity_feed_link', trailingslashit( bp_get_root_domain() ) . bp_get_root_slug( $bp->achievements->slug ) . '/' . apply_filters( 'dpa_get_achievement_slug', $bp->achievements->current_achievement->slug ) . '/' . DPA_SLUG_ACHIEVEMENT_ACTIVITY_RSS . '/' );
	}

/**
 * Template tag version of dpa_member_get_achievements_link()
 *
 * @since 2.0
 * @uses dpa_member_get_achievements_button()
 */
function dpa_member_achievements_link() {
	echo dpa_member_get_achievements_link();
}
	/**
	 * Returns a button to the current member's Achievements page; used on the Achievement "unlocked by" screen.
	 * Use only in the members' template loop.
	 *
	 * @global object $bp BuddyPress global settings
	 * @global BP_Core_Members_Template $members_template
	 * @return string
	 * @since 2.0
	 */
	function dpa_member_get_achievements_link() {
		global $bp, $members_template;
	
		$button = '<p><div class="generic-button" id="view-achievements-button">';
		$button .= '<a href="' . esc_url( bp_get_member_permalink() . DPA_SLUG ) . '">' . __( "View Achievements", 'dpa' ) . '</a>';
		$button .= '</div></p>';

		return apply_filters( 'dpa_member_get_achievements_button', $button );
	}

// Use these to check the type of page

/**
 * Is this the change picture page?
 *
 * @since 2.0
 * @global object $bp BuddyPress global settings
 * @return bool
 */
function dpa_is_achievement_change_picture_page() {
	global $bp;

	return apply_filters( 'dpa_is_achievement_change_picture_page', ( bp_is_current_component( $bp->achievements->slug ) && $bp->current_action == DPA_SLUG_ACHIEVEMENT_CHANGE_PICTURE ) );
}

/**
 * Is this the home (activity stream) page?
 *
 * @since 2.0
 * @global object $bp BuddyPress global settings
 * @return bool
 */
function dpa_is_achievement_activity_page() {
	global $bp;

	return apply_filters( 'dpa_is_achievement_activity_page', ( bp_is_current_component( $bp->achievements->slug ) && $bp->current_action == DPA_SLUG_ACHIEVEMENT_ACTIVITY ) );
}

/**
 * Is this the "this Achievement is unlocked by" page?
 *
 * @since 2.0
 * @global object $bp BuddyPress global settings
 * @return bool
 */
function dpa_is_achievement_unlocked_by_page() {
	global $bp;

	return apply_filters( 'dpa_is_achievement_unlocked_by_page', ( bp_is_current_component( $bp->achievements->slug ) && $bp->current_action == DPA_SLUG_ACHIEVEMENT_UNLOCKED_BY ) );
}

/**
 * Is this the delete Achievement page?
 *
 * @since 2.0
 * @global object $bp BuddyPress global settings
 * @return bool
 */
function dpa_is_achievement_delete_page() {
	global $bp;

	return apply_filters( 'dpa_is_achievement_delete_page', ( bp_is_current_component( $bp->achievements->slug ) && $bp->current_action == DPA_SLUG_ACHIEVEMENT_DELETE ) );
}

/**
 * Is this the edit Achievement page?
 *
 * @since 2.0
 * @global object $bp BuddyPress global settings
 * @return bool
 */
function dpa_is_achievement_edit_page() {
	global $bp;

	return apply_filters( 'dpa_is_achievement_edit_page', ( bp_is_current_component( $bp->achievements->slug ) && $bp->current_action == DPA_SLUG_ACHIEVEMENT_EDIT ) );
}

/**
 * Is this the grant Achievement page?
 *
 * @since 2.0
 * @global object $bp BuddyPress global settings
 * @return bool
 */
function dpa_is_achievement_grant_page() {
	global $bp;

	return apply_filters( 'dpa_is_achievement_grant_page', ( bp_is_current_component( $bp->achievements->slug ) && $bp->current_action == DPA_SLUG_ACHIEVEMENT_GRANT ) );
}

/**
 * Is this the create Achievement page?
 *
 * @since 2.0
 * @global object $bp BuddyPress global settings
 * @return bool
 */
function dpa_is_create_achievement_page() {
	global $bp;

	return apply_filters( 'dpa_is_create_achievement_page', ( bp_is_current_component( $bp->achievements->slug ) && $bp->current_action == DPA_SLUG_CREATE ) );
}

/**
 * Is this the user's "my achievements" page?
 *
 * @since 2.0
 * @global object $bp BuddyPress global settings
 * @return bool
 */
function dpa_is_member_my_achievements_page() {
	global $bp;

	return apply_filters( 'dpa_is_member_my_achievements_page', ( bp_is_current_component( $bp->achievements->slug ) && $bp->current_action == DPA_SLUG_MY_ACHIEVEMENTS ) );
}

/**
 * Is this the Achievements Directory page?
 *
 * @global object $bp BuddyPress global settings
 * @global bool $is_member_page If we are under anything with a members slug
 * @return bool
 * @since 2.0
 */
function dpa_is_directory_page() {
	global $bp, $is_member_page;

	return apply_filters( 'dpa_is_directory_page', ( bp_is_current_component( $bp->achievements->slug ) && ( bp_is_directory() || ( bp_is_current_component( $bp->achievements->slug ) && !$bp->current_action && !$bp->current_item && !$is_member_page ) ) ) );
}

/**
 * Is this an Achievements page?
 *
 * @since 2.0
 * @global object $bp BuddyPress global settings
 * @return bool
 */
function dpa_is_achievements_component() {
	global $bp;

	return apply_filters( 'dpa_is_achievements_component', bp_is_current_component( $bp->achievements->slug ) );
}

/**
 * Is this a page about a single Achievement?
 *
 * @since 2.0
 * @global object $bp BuddyPress global settings
 * @return bool
 */
function dpa_is_achievement_single() {
	global $bp;

	return apply_filters( 'dpa_is_achievement_single', ( bp_is_current_component( $bp->achievements->slug ) && $bp->is_single_item ) );
}


/* Use these to check user permissions */

/**
 * Does the user have the capability to create Achievements?
 *
 * @since 2.0
 * @global object $bp BuddyPress global settings
 * @return bool
 */
function dpa_permission_can_user_create() {
	global $bp;

	if ( !$bp->loggedin_user->id )
		return false;

	if ( $bp->loggedin_user->is_super_admin )
		return true;

	return apply_filters( 'dpa_permission_can_user_create', current_user_can( 'achievements_create' ) );
}

/**
 * Does the user have the capability to edit Achievements?
 *
 * @since 2.0
 * @global object $bp BuddyPress global settings
 * @return bool
 */
function dpa_permission_can_user_edit() {
	global $bp;

	if ( !$bp->loggedin_user->id )
		return false;

	if ( $bp->loggedin_user->is_super_admin )
		return true;

	return apply_filters( 'dpa_permission_can_user_edit', current_user_can( 'achievements_edit' ) );
}

/**
 * Does the user have the capability to remove Achievements from other users?
 *
 * @since 2.0
 * @global object $bp BuddyPress global settings
 * @return bool
 */
function dpa_permission_can_user_remove() {
	global $bp;

	if ( !$bp->loggedin_user->id )
		return false;

	if ( $bp->loggedin_user->is_super_admin )
		return true;

	return apply_filters( 'dpa_permission_can_user_remove', current_user_can( 'achievements_remove' ) );
}

/**
 * Does the user have the capability to grant Achievements to other users?
 *
 * @since 2.0
 * @global object $bp BuddyPress global settings
 * @return bool
 */
function dpa_permission_can_user_grant() {
	global $bp;

	if ( !$bp->loggedin_user->id )
		return false;

	if ( $bp->loggedin_user->is_super_admin )
		return true;

	return apply_filters( 'dpa_permission_can_user_grant', current_user_can( 'achievements_grant' ) );
}

/**
 * Does the user have the capability to delete Achievements?
 *
 * @since 2.0
 * @global object $bp BuddyPress global settings
 * @return bool
 */
function dpa_permission_can_user_delete() {
	global $bp;

	if ( !$bp->loggedin_user->id )
		return false;

	if ( $bp->loggedin_user->is_super_admin )
		return true;

	return apply_filters( 'dpa_permission_can_user_delete', current_user_can( 'achievements_delete' ) );
}

/**
 * Does the user have the capability to change an Achievement's picture?
 *
 * @since 2.0
 * @global object $bp BuddyPress global settings
 * @return bool
 */
function dpa_permission_can_user_change_picture() {
	global $bp;

	if ( !$bp->loggedin_user->id )
		return false;

	if ( $bp->loggedin_user->is_super_admin )
		return true;

	return apply_filters( 'dpa_permission_can_user_change_picture', current_user_can( 'achievements_change_picture' ) );
}
?>