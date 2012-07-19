<?php
/**
 * Holds all database access classes and functions
 *
 * @author Paul Gibbs <paul@byotos.com>
 * @package Achievements
 * @subpackage classes
 *
 * $Id: achievements-classes.php 1005 2011-10-04 20:19:19Z DJPaul $
 */

/**
 * Database access class
 *
 * @since 2.0
 * @package Achievements
 * @subpackage classes
 */
class DPA_Achievement {

	/**
	 * Achievement ID
	 *
	 * @access public
	 * @var integer
	 * @see DPA_Achievement()
	 * @since 2.0
	 */
	var $id;

	/**
	 * Achievement Action ID
	 *
	 * @access public
	 * @var integer
	 * @see DPA_Achievement()
	 * @since 2.0
	 */
	var $action_id;

	/**
	 * Achievement picture ID
	 *
	 * @access public
	 * @var integer
	 * @see DPA_Achievement()
	 * @since 2.0
	 */
	var $picture_id;

	/**
	 * Achievement Action count
	 *
	 * @access public
	 * @var integer
	 * @see DPA_Achievement()
	 * @since 2.0
	 */
	var $action_count;

	/**
	 * Achievement name
	 *
	 * @access public
	 * @var string
	 * @see DPA_Achievement()
	 * @since 2.0
	 */
	var $name;

	/**
	 * Achievement description
	 *
	 * @access public
	 * @var string
	 * @see DPA_Achievement()
	 * @since 2.0
	 */
	var $description;

	/**
	 * Achievement points
	 *
	 * @access public
	 * @var integer
	 * @see DPA_Achievement()
	 * @since 2.0
	 */
	var $points;

	/**
	 * Is Achievement active?
	 *
	 * If this is 0, it is inactive and hidden from all listings and searches (admins can see it, however).
	 * If this is 1, it is active and everyone can see it.
	 * If this is 2, it is active but hidden from the directory and searches (admins can see it, however).
	 *
	 * @access public
	 * @var bool
	 * @see DPA_Achievement()
	 * @since 2.0
	 */
	var $is_active;

	/**
	 * Achievement slug
	 *
	 * @access public
	 * @var string
	 * @see DPA_Achievement()
	 * @since 2.0
	 */
	var $slug;

	/**
	 * Timestamp of when/if Achievement has been unlocked
	 *
	 * @access public
	 * @var string
	 * @see DPA_Achievement()
	 * @since 2.0
	 */
	var $achieved_at;

	/**
	 * Achievement site ID; only applicable on multisite installs
	 *
	 * @access public
	 * @var integer
	 * @see DPA_Achievement()
	 * @since 2.0
	 */
	var $site_id;

	/**
	 * Achievement group ID, for group Achievements only
	 *
	 * @access public
	 * @var integer
	 * @see DPA_Achievement()
	 * @since 2.0
	 */
	var $group_id;

	/**
	 * Constructor
	 *
	 * @access public
	 * @global object $bp BuddyPress global settings
	 * @param string|array $args See populate()
	 * @see DPA_Achievement::populate()
	 * @since 2.0
	 * @todo Change bools to strings to better describe what variable is used for.
	 */
	function dpa_achievement( $args='' ) {
		global $bp;

		$defaults = array(
			'id' => 0,
			'slug' => '',
			'populate_extras' => true,
			'user_id' => 0
		);

		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_SKIP );

		if ( !$user_id )
			$user_id = $bp->loggedin_user->id;

		if ( $slug )
			$this->populate( $slug, $populate_extras, 0, $user_id );
		elseif ( $id )
			$this->populate( '', $populate_extras, $id, $user_id );
	}

	/**
	 * Retrieves information about this Achievement from the database and populate the class
	 *
	 * @access public
	 * @global object $bp BuddyPress global settings
	 * @global wpdb $wpdb WordPress database object
	 * @param string $slug Achievement slug
	 * @param bool $populate_extras Retrieve information about if/when the current user has unlocked this Achievement
	 * @param id $slug Optional; Achievement ID
	 * @param id $user_id Optional; User ID to retrieve $populate_extras information for
	 * @since 2.0
	 * @todo Change bools to strings to better describe what variable is used for.
	 */
	function populate( $slug, $populate_extras, $id=0, $user_id=0 ) {
		global $bp, $wpdb;

		if ( !$user_id )
			$user_id = $bp->loggedin_user->id;

		if ( !$id )
			$achievement = $this->get( array( 'type' => 'single', 'slug' => $slug, 'populate_extras' => $populate_extras, 'user_id' => $user_id ) );
		else
			$achievement = $this->get( array( 'type' => 'single', 'id' => $id, 'populate_extras' => $populate_extras, 'user_id' => $user_id ) );

		if ( !$achievement || empty( $achievement['achievements'] ) )
			return;

		$achievement = $achievement['achievements'][0];

		if ( !isset( $achievement->achieved_at ) )
			$achievement->achieved_at = null;

		$this->id = $achievement->id;
		$this->action_id = $achievement->action_id;
		$this->picture_id = $achievement->picture_id;
		$this->action_count = $achievement->action_count;
		$this->name = $achievement->name;
		$this->description = $achievement->description;
		$this->points = $achievement->points;
		$this->is_active = $achievement->is_active;
		$this->slug = $achievement->slug;
		$this->achieved_at = $achievement->achieved_at;
		$this->site_id = $achievement->site_id;
		$this->group_id = $achievement->group_id;
	}

	/**
	 * Validates class variables and saves to database
	 *
	 * @access public
	 * @global object $bp BuddyPress global settings
	 * @global wpdb $wpdb WordPress database object
	 * @param DPA_Achievement $old_achievement A copy of the Achievement which is about to be saved, for comparision purposes
	 * @return bool Whether the achievement was saved successfully or not.
	 * @since 2.0
	 * @uses WP_Error
	 */
	function save( $old_achievement=null ) {
		global $bp, $wpdb;

		$errors = new WP_Error();

		if ( $this->id )
			$this->id = apply_filters( 'dpa_achievement_id_before_save', (int)$this->id, $this );

		$this->action_id    = apply_filters( 'dpa_achievement_action_id_before_save', (int)$this->action_id, $this );
		$this->picture_id   = apply_filters( 'dpa_achievement_picture_id_before_save', (int)$this->picture_id, $this );
		$this->action_count = apply_filters( 'dpa_achievement_action_count_before_save', (int)$this->action_count, $this );
		$this->name         = stripslashes( apply_filters( 'dpa_achievement_name_before_save', $this->name, $this ) );
		$this->description  = stripslashes( apply_filters( 'dpa_achievement_description_before_save', $this->description, $this ) );
		$this->points       = apply_filters( 'dpa_achievement_points_before_save', (int)$this->points, $this );
		$this->is_active    = apply_filters( 'dpa_achievement_is_active_before_save', (int)$this->is_active, $this );
		$this->slug         = apply_filters( 'dpa_achievement_slug_before_save', $this->slug, $this );
		$this->site_id      = apply_filters( 'dpa_achievement_site_id_before_save', (int)$this->site_id, $this );
		$this->group_id     = apply_filters( 'dpa_achievement_group_id_before_save', (int)$this->group_id, $this );

		DPA_Achievement::validate_achievement_details( $this, $old_achievement, $errors );
		do_action( 'dpa_achievement_before_save', $this, $old_achievement, $errors );

		if ( $errors->get_error_code() )
			return $errors;

		if ( $this->id ) {
			$result = $wpdb->query( $wpdb->prepare( "UPDATE {$bp->achievements->table_achievements} SET action_id = %d, picture_id = %d, action_count = %d, name = %s, description = %s, points = %d, is_active = %d, slug = %s, site_id = %d, group_id = %d WHERE id = %d LIMIT 1", $this->action_id, $this->picture_id, $this->action_count, $this->name, $this->description, $this->points, $this->is_active, $this->slug, $this->site_id, $this->group_id, $this->id ) );

		} else {
			// Save
			$result = $wpdb->insert( $bp->achievements->table_achievements, array( 'action_id' => $this->action_id, 'picture_id' => $this->picture_id, 'action_count' => $this->action_count, 'name' => $this->name, 'description' => $this->description, 'points' => $this->points, 'is_active' => $this->is_active, 'slug' => $this->slug, 'site_id' => $this->site_id, 'group_id' => $this->group_id ), array( '%d', '%d', '%d', '%s', '%s', '%d', '%d', '%s', '%d', '%d' ) );
			$this->id = $wpdb->insert_id;
		}

		// Remove active actions cache
		wp_cache_delete( 'dpa_active_actions', 'dpa' );

		do_action( 'dpa_achievement_after_save', $this, $old_achievement );
		return $result;
	}


	// Static Functions

	/**
	 * Validates the class variables
	 *
	 * @access public
	 * @global object $bp BuddyPress global settings
	 * @global wpdb $wpdb WordPress database object
	 * @param DPA_Achievement $achievement The Achievement to validate
	 * @param DPA_Achievement $old_achievement A copy of the Achievement which is about to be saved, for comparision purposes
	 * @param WP_Error $errors Holds any errors (by ref)
	 * @since 2.0
	 * @static
	 * @uses WP_Error
	 */
	function validate_achievement_details( $achievement, $old_achievement, &$errors ) {
		global $bp, $wpdb;

		$readonly_properties = array( 'id', 'is_active', 'action_count', 'action_id', 'achieved_at', 'site_id', 'group_id' );
		foreach ( $achievement as $property => $value ) {
			if ( in_array( $property, $readonly_properties ) )
				continue;

			if ( empty( $value ) )
				if ( is_int( $value ) )
					$errors->add( $property, __( "This can't be zero.", 'dpa' ) );
				else
					$errors->add( $property, __( "This can't be blank.", 'dpa' ) );
		}

		$valid_action_ids = array( -1 );  // Badge
		$actions = dpa_get_actions();
		foreach ( $actions as $action )
			$valid_action_ids[] = $action->id;

		if ( !in_array( $achievement->action_id, $valid_action_ids ) )
			$errors->add( 'action_id', __( "Choose an event.", 'dpa' ) );

		if ( $achievement->action_count < 0 )
			$errors->add( 'action_count', __( "This needs to be at least one.", 'dpa' ) );

		if ( strlen( $achievement->name ) > 200 )
			$errors->add( 'name', __( "This needs to be less than two hundred characters long.", 'dpa' ) );

		if ( $this->achievement_name_exists( $achievement->name ) )
			$errors->add( 'name', __( "The Achievement's name must be unique; this one is already in use.", 'dpa' ) );

		if ( empty( $achievement->description ) )
			$errors->add( 'description', __( "Missing Achievement description.", 'dpa' ) );

		if ( strlen( $achievement->slug ) > 200 )
			$errors->add( 'slug', __( "This needs to be less than two hundred characters long.", 'dpa' ) );

		$illegal_names = array_unique( array_merge( (array)get_site_option( "illegal_names" ), apply_filters( 'validate_achievement_details_slug', array( DPA_SLUG, DPA_SLUG_CREATE, DPA_SLUG_MY_ACHIEVEMENTS, DPA_SLUG_ACHIEVEMENT_EDIT, DPA_SLUG_ACHIEVEMENT_DELETE, DPA_SLUG_ACHIEVEMENT_CHANGE_PICTURE, DPA_SLUG_ACHIEVEMENT_UNLOCKED_BY, DPA_SLUG_ACHIEVEMENT_GRANT ) ) ) );
		if ( $achievement->slug && is_array( $illegal_names ) && in_array( $achievement->slug, $illegal_names ) )
			$errors->add( 'slug', __( "This slug conflicts with something important; please try another.", 'dpa' ) );

		if ( $this->achievement_slug_exists( $achievement->slug ) )
			$errors->add( 'slug', __( "The slug must be unique; this one is already in use.", 'dpa' ) );
	}

	/**
	 * Remove the Achievement from the database
	 *
	 * @access public
	 * @global object $bp BuddyPress global settings
	 * @global wpdb $wpdb WordPress database object
	 * @param integer|string $id Achievement ID or slug
	 * @return integer The Achievement ID which was just deleted
	 * @since 2.0
	 * @static
	 */
	function delete( $slug ) {
		global $bp, $wpdb;

		if ( !$id = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$bp->achievements->table_achievements} WHERE slug = %s LIMIT 1", $slug ) ) )
			return false;

		$wpdb->query( $wpdb->prepare( "DELETE FROM {$bp->achievements->table_unlocked} WHERE achievement_id = %d", $id ) );
		$wpdb->query( $wpdb->prepare( "DELETE FROM {$bp->achievements->table_achievements} WHERE id = %d", $id ) );

		do_action( 'dpa_achievement_deleted', $id );

		return $id;
	}

	/**
	 * Get the Achievement from the database
	 *
	 * @access public
	 * @global object $bp BuddyPress global settings
	 * @global wpdb $wpdb WordPress database object
	 * @param string|array $args See DPA_Achievement_Template::DPA_Achievement_Template() inline documentation for explanation of this argument
	 * @return array The Achievements found matching the criteria (and the total number of, for pagination)
	 * @see DPA_Achievement_Template::DPA_Achievement_Template
	 * @since 2.0
	 * @static
	 */
	function get( $args ) {
		global $bp, $wpdb;

		$defaults = array(
			'skip_detail_page_result' => true,
			'limit' => 0,  // limit
			'page' => 1,  // page 1 without a per_page will result in no pagination.
			'per_page' => 20, // results per page
			'populate_extras' => true,
			'search_terms' => '',
			'id' => '', // only type=single
			'slug' => '',  // only type=single
			'type' => 'all',  // all | active | inactive | unlocked | locked | single (and for member profile page - active_by_action | alphabetical | eventcount | points | newest)
			'user_id' => 0,  // for type=unlocked and populate_extras
			'action' => '' // for type=active_by_action and user_id and populate_extras, and type=unlocked with all of the "order by" filters
		);

		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_SKIP );

		// Single page may have already had a DB query
		if ( !$skip_detail_page_result && $bp->achievements->current_achievement->id )
			return array( 'total' => 1, 'achievements' => array( $bp->achievements->current_achievement ) );

		$select_vals = 'id, a.action_id, a.picture_id, a.action_count, a.name, a.description, a.points, a.is_active, a.slug, a.site_id, a.group_id';
		$extras = '';

		if ( $populate_extras && $user_id && 'locked' != $type ) {
			$extras .= sprintf( "LEFT JOIN {$bp->achievements->table_unlocked} as u ON a.id = u.achievement_id AND u.user_id = %d ", $user_id );
			$select_vals .= ', achieved_at';
		}

		switch ( $type ) {
			case 'unlocked':
				$populate_extras = true;
				$sql = $wpdb->prepare( "SELECT a.{$select_vals}, achieved_at FROM {$bp->achievements->table_achievements} as a, {$bp->achievements->table_unlocked} as u WHERE a.id = u.achievement_id AND u.user_id = %d AND (is_active = 1 OR is_active = 2)", $user_id );
				$sql_total_count = $wpdb->prepare( "SELECT COUNT(a.id) FROM {$bp->achievements->table_achievements} as a, {$bp->achievements->table_unlocked} as u WHERE a.id = u.achievement_id AND u.user_id = %d AND (is_active = 1 OR is_active = 2)", $user_id );
			break;

			case 'locked':
				$populate_extras = false;
				$sql = $wpdb->prepare( "SELECT a.{$select_vals}, achieved_at FROM {$bp->achievements->table_achievements} as a LEFT JOIN {$bp->achievements->table_unlocked} as u ON a.id = u.achievement_id AND u.user_id = %d WHERE is_active = 1 AND achieved_at IS NULL", $user_id );
				$sql_total_count = $wpdb->prepare( "SELECT COUNT(a.id) FROM {$bp->achievements->table_achievements} as a LEFT JOIN {$bp->achievements->table_unlocked} as u ON a.id = u.achievement_id AND u.user_id = %d WHERE is_active = 1 AND achieved_at IS NULL", $user_id );
			break;

			case 'active_by_action':
				$sql = $wpdb->prepare( "SELECT a.{$select_vals} FROM {$bp->achievements->table_actions} as action, {$bp->achievements->table_achievements} as a {$extras}WHERE (is_active = 1 OR is_active = 2) AND action.id = a.action_id AND action.name = %s", $action );
				$sql_total_count = $wpdb->prepare( "SELECT COUNT(a.id) FROM {$bp->achievements->table_actions} as action, {$bp->achievements->table_achievements} as a {$extras}WHERE (is_active = 1 OR is_active = 2) AND action.id = a.action_id AND action.name = %s", $action );
			break;

			case 'active':
				$sql = $wpdb->prepare( "SELECT a.{$select_vals} FROM {$bp->achievements->table_achievements} as a {$extras}WHERE is_active = 1" );
				$sql_total_count = $wpdb->prepare( "SELECT COUNT(a.id) FROM {$bp->achievements->table_achievements} as a {$extras}WHERE is_active = 1" );
			break;

			case 'inactive':
				$sql = $wpdb->prepare( "SELECT a.{$select_vals} FROM {$bp->achievements->table_achievements} as a {$extras}WHERE is_active = 0" );
				$sql_total_count = $wpdb->prepare( "SELECT COUNT(a.id) FROM {$bp->achievements->table_achievements} as a {$extras}WHERE is_active = 0" );
			break;

			case 'single':
				if ( !$id )
					$sql = $wpdb->prepare( "SELECT a.{$select_vals} FROM {$bp->achievements->table_achievements} as a {$extras}WHERE slug = %s", $slug );
				else
					$sql = $wpdb->prepare( "SELECT a.{$select_vals} FROM {$bp->achievements->table_achievements} as a {$extras}WHERE id = %d", $id );

				$sql_total_count = '';
			break;

			default:
			case 'all':
			case 'eventcount':
			case 'newest':
			case 'points':
				$sql = $wpdb->prepare( "SELECT a.{$select_vals} FROM {$bp->achievements->table_achievements} as a {$extras}" );
				$sql_total_count = $wpdb->prepare( "SELECT COUNT(a.id) FROM {$bp->achievements->table_achievements} as a {$extras}" );
			break;
		}

		$sql = apply_filters( 'dpa_achievement_get_type_sql', $sql, $limit, $page, $per_page, $populate_extras, $search_terms, $id, $slug, $type, $user_id, $action );
		$sql_total_count = apply_filters( 'dpa_achievement_get_type_count_sql', $sql_total_count, $limit, $page, $per_page, $populate_extras, $search_terms, $id, $slug, $type, $user_id, $action );

		if ( $search_terms ) {
			$search_terms = like_escape( $wpdb->escape( $search_terms ) );

			if ( in_array( $type, array( 'all', 'alphabetical', 'eventcount', 'newest', 'points' ) ) )
				$search_sql = " WHERE";
			else
				$search_sql = " AND";

			$search_sql .= " ( name LIKE '%%{$search_terms}%%' OR description LIKE '%%{$search_terms}%%' )";
			$sql .= $search_sql;
			$sql_total_count .= $search_sql;
		}

		// Filters
		if ( 'single' != $type ) {
			// Only admins see inactive Achievements in the Directory.
			if ( !dpa_permission_can_user_edit() ) {
				if ( $search_terms ) {
					$admin_sql = $wpdb->prepare( " AND is_active=1" );
					$sql .= $admin_sql;
					$sql_total_count .= $admin_sql;

				} elseif ( in_array( $type, array( 'all', 'alphabetical', 'eventcount', 'newest', 'points' ) ) ) {
					$admin_sql .= " WHERE is_active=1";
					$sql .= $admin_sql;
					$sql_total_count .= $admin_sql;
				}
			}

			if ( 'unlocked' == $type && 'newest' == $action )
				$sql .= " ORDER BY u.achieved_at DESC";
			elseif ( 'newest' == $type || 'newest' == $action )
				$sql .= " ORDER BY a.id DESC";
			elseif ( 'points' == $type || 'points' == $action )
				$sql .= " ORDER BY a.points DESC";
			elseif ( 'alphabetical' == $type || 'alphabetical' == $action )
				$sql .= " ORDER BY a.name ASC";
			elseif ( 'eventcount' == $type || 'eventcount' == $action )
				$sql .= " ORDER BY a.action_count DESC";
			elseif ( 'unlocked' == $type )  // This must be the penultimate item
				$sql .= " ORDER BY u.achieved_at DESC";
			else                            // This must be the last item
				$sql .= " ORDER BY a.name ASC";

			$sql = apply_filters( 'dpa_achievement_get_order_by_sql', $sql, $limit, $page, $per_page, $populate_extras, $search_terms, $id, $slug, $type, $user_id, $action );

		} else {
			$limit = 1;
		}

		if ( 'active_by_action' != $type ) {
			if ( $per_page && $page && !$limit ) {
				$sql .= $wpdb->prepare( " LIMIT %d, %d", intval( ( $page - 1 ) * $per_page ), intval( $per_page ) );
			} elseif ( $limit ) {
				$sql .= " LIMIT {$limit}";
				$sql_total_count .= " LIMIT {$limit}";
			}
		}

		$results = $wpdb->get_results( $sql );

		if ( 'single' == $type )
			$total_count = 1;
		else
			$total_count = $wpdb->get_var( $sql_total_count );

		return array( 'total' => $total_count, 'achievements' => $results );
	}

	/**
	 * Does this Achievement slug exist?
	 *
	 * @access public
	 * @global object $bp BuddyPress global settings
	 * @global wpdb $wpdb WordPress database object
	 * @param string $slug Achievement slug
	 * @return bool
	 * @since 2.0
	 * @static
	 */
	function achievement_slug_exists( $slug ) {
		global $bp, $wpdb;

		if ( !$slug )
			return false;

		if ( dpa_is_achievement_edit_page() || dpa_is_achievement_change_picture_page() )
			$result = $wpdb->get_var( $wpdb->prepare( "SELECT name FROM {$bp->achievements->table_achievements} WHERE slug = %s AND id != %d LIMIT 1", $slug, $bp->achievements->current_achievement->id ) );
		else
			$result = $wpdb->get_var( $wpdb->prepare( "SELECT name FROM {$bp->achievements->table_achievements} WHERE slug = %s LIMIT 1", $slug ) );

		return $result;
	}

	/**
	 * Does this Achievement name exist?
	 *
	 * @access public
	 * @global object $bp BuddyPress global settings
	 * @global wpdb $wpdb WordPress database object
	 * @param string $name Achievement name
	 * @return bool
	 * @since 2.0
	 * @static
	 */
	function achievement_name_exists( $name ) {
		global $bp, $wpdb;

		if ( !$name )
			return false;

		if ( dpa_is_achievement_edit_page() || dpa_is_achievement_change_picture_page() )
			return $wpdb->get_var( $wpdb->prepare( "SELECT name FROM {$bp->achievements->table_achievements} WHERE name = %s AND id != %d LIMIT 1", $name, $bp->achievements->current_achievement->id ) );
		else
			return $wpdb->get_var( $wpdb->prepare( "SELECT name FROM {$bp->achievements->table_achievements} WHERE name = %s LIMIT 1", $name ) );
	}
}

/**
 * Increment the user's points, usually after unlocking an Achievement
 *
 * @global object $bp BuddyPress global settings
 * @param int $new_points
 * @param int $user_id Optional
 * @since 2.0
 */
function dpa_points_increment( $new_points, $user_id=0 ) {
	global $bp;

	if ( !$user_id )
		$user_id = $bp->loggedin_user->id;

	$points = get_user_meta( $user_id, 'achievements_points', true );
	update_user_meta( $user_id, 'achievements_points', apply_filters( 'dpa_points_increment', $points + $new_points, $user_id ) );

	do_action( 'dpa_points_incremented', $new_points, $user_id );
}

/**
 * Returns the users with the highest point totals
 *
 * @global wpdb $wpdb WordPress database object
 * @param int $limit Number of results to return. Defaults to 5.
 * @return array
 * @since 2.0
 */
function dpa_points_get_high_scorers( $limit=5 ) {
	global $wpdb;

	if ( !$high_scorers = wp_cache_get( 'dpa_high_scorers_' . $limit, 'dpa' ) ) {
		$high_scorers = $wpdb->get_results( $wpdb->prepare( "SELECT CAST(meta_value AS UNSIGNED INTEGER) as points, user_id as id FROM $wpdb->usermeta WHERE meta_key = %s ORDER BY points DESC LIMIT %d", 'achievements_points', $limit ) );
		wp_cache_set( 'dpa_high_scorers_' . $limit, $high_scorers, 'dpa' );
	}

	return apply_filters( 'dpa_points_get_high_scorers', $high_scorers, $limit );
}

/**
 * Unlock the Achievement for a user
 *
 * @global DPA_Achievement_Template $achievements_template Achievements template tag object
 * @global object $bp BuddyPress global settings
 * @global wpdb $wpdb WordPress database object
 * @param int $user_id User ID
 * @param various $achievement Either an instance of DPA_Achievement, or a MySQL result (stdClass).
 * @see DPA_Achievement
 * @since 2.0
 */
function dpa_unlock_achievement( $user_id=0, $achievement=null ) {
	global $achievements_template, $bp, $wpdb;

	if ( !$user_id )
		$user_id = $bp->loggedin_user->id;

	if ( !$achievement )
		$achievement = $achievements_template->achievement;

	$wpdb->insert( $bp->achievements->table_unlocked, array( 'achievement_id' => $achievement->id, 'user_id' => $user_id, 'achieved_at' => bp_core_current_time() ), array( '%d', '%d', '%s' ) );
}

/**
 * Get count of how many (active) Achievements there are.
 * Only users with edit permission can see hidden Achievements.
 *
 * @global object $bp BuddyPress global settings
 * @global wpdb $wpdb WordPress database object
 * @since 2.0
 */
function dpa_get_total_achievements_count() {
	global $bp, $wpdb;

	if ( !$count = wp_cache_get( 'dpa_get_total_achievements_count', 'dpa' ) ) {
		$admin_sql = '';

		if ( !dpa_permission_can_user_edit() )
			$admin_sql = $wpdb->prepare( "WHERE is_active=1" );

		$count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(id) FROM {$bp->achievements->table_achievements} {$admin_sql}" ) );
		wp_cache_set( 'dpa_get_total_achievements_count', $count, 'dpa' );
	}

	return $count;
}

/**
 * Get count of how many (active) Achievements the specified user has.
 * Only users with edit permission can see hidden Achievements.
 *
 * @global object $bp BuddyPress global settings
 * @global wpdb $wpdb WordPress database object
 * @param int $user_id
 * @since 2.0
 */
function dpa_get_total_achievements_count_for_user( $user_id = false ) {
	global $bp, $wpdb;

	if ( !$user_id )
		$user_id = ( $bp->displayed_user->id ) ? $bp->displayed_user->id : $bp->loggedin_user->id;

	if ( !$count = wp_cache_get( 'dpa_get_total_achievements_count_for_user_' . $user_id, 'bp' ) ) {
		$admin_sql = $wpdb->prepare( "AND (is_active = 1 OR is_active = 2)" );
		$count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(a.id) FROM {$bp->achievements->table_achievements} as a, {$bp->achievements->table_unlocked} as u WHERE a.id = u.achievement_id AND u.user_id = %d {$admin_sql}", $user_id ) );
		wp_cache_set( 'dpa_get_total_achievements_count_for_user_' . $user_id, $count, 'bp' );
	}

	return $count;
}

/**
 * Get count of how many people have unlocked the specified Achievement. This is cached
 * in the central achievement_meta site option as 'no_of_unlocks'. Most of the time you
 * will use want to use dpa_get_achievement_unlocked_count() to access that meta, and not this function.
 *
 * @global object $bp BuddyPress global settings
 * @global wpdb $wpdb WordPress database object
 * @param int $achievement_id Achievement ID
 * @return int
 * @see dpa_get_achievement_unlocked_count()
 * @since 2.0.3
 */
function dpa_get_total_achievement_unlocked_count( $achievement_id ) {
	global $bp, $wpdb;

	return $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(u.id) FROM {$bp->achievements->table_achievements} as a, {$bp->achievements->table_unlocked} as u WHERE a.id = %d AND a.id = u.achievement_id", $achievement_id ) );
}

/**
 * Remove the achievement unlock records from the database, such as when a user is marked as a spammer or their account is deleted.
 *
 * @global object $bp BuddyPress global settings
 * @global wpdb $wpdb WordPress database object
 * @param integer $user_id User ID
 * @since 2.0.3
 */
function dpa_delete_achievement_unlocks_for_user( $user_id ) {
	global $bp, $wpdb;

	$wpdb->query( $wpdb->prepare( "DELETE FROM {$bp->achievements->table_unlocked} WHERE user_id = %d", $user_id ) );
}
?>