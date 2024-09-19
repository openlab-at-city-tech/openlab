<?php

/* Load JS necessary for group invitation pages */

function invite_anyone_add_js() {
	if ( bp_is_current_action( BP_INVITE_ANYONE_SLUG ) || bp_is_action_variable( BP_INVITE_ANYONE_SLUG, 1 ) ) {

		$min = '-min';
		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			$min = '';
		}

		wp_enqueue_script(
			'invite-anyone-autocomplete-js',
			plugins_url() . "/invite-anyone/group-invites/jquery.autocomplete/jquery.autocomplete$min.js",
			array( 'jquery' ),
			BP_INVITE_ANYONE_VER,
			true
		);

		wp_register_script(
			'invite-anyone-js',
			plugins_url() . '/invite-anyone/group-invites/group-invites-js.js',
			array( 'invite-anyone-autocomplete-js' ),
			BP_INVITE_ANYONE_VER,
			true
		);
		wp_enqueue_script( 'invite-anyone-js' );

		// Add words that we need to use in JS to the end of the page
		// so they can be translated and still used.
		$params = apply_filters(
			'ia_get_js_strings',
			array(
				'unsent_invites' => __( 'Click &ldquo;Send Invites&rdquo; to finish sending your new invitations.', 'invite-anyone' ),
			)
		);
		wp_localize_script( 'invite-anyone-js', 'IA_js_strings', $params );

		$autocomplete_options = apply_filters(
			'ia_autocomplete_options',
			array(
				'minChars' => 2,
			)
		);
		wp_localize_script( 'invite-anyone-js', 'IA_autocomplete_options', $autocomplete_options );
	}
}
add_action( 'wp_head', 'invite_anyone_add_js', 1 );

function invite_anyone_add_group_invite_css() {
	if ( bp_is_current_action( BP_INVITE_ANYONE_SLUG ) || bp_is_action_variable( BP_INVITE_ANYONE_SLUG, 1 ) ) {
		$style_url  = plugins_url() . '/invite-anyone/group-invites/group-invites-css.css';
		$style_file = WP_PLUGIN_DIR . '/invite-anyone/group-invites/group-invites-css.css';

		if ( file_exists( $style_file ) ) {
			wp_register_style(
				'invite-anyone-group-invites-style',
				$style_url,
				array(),
				BP_INVITE_ANYONE_VER
			);

			wp_enqueue_style( 'invite-anyone-group-invites-style' );
		}
	}
}
add_action( 'wp_print_styles', 'invite_anyone_add_group_invite_css' );

function invite_anyone_add_old_css() {
	$plugins_url = plugins_url();
	?>
	<style type="text/css">

li a#nav-invite-anyone {
	padding: 0.55em 3.1em 0.55em 0px !important;
	margin-right: 10px;
	background: url(<?php echo esc_url( $plugins_url ) . '/invite-anyone/invite-anyone/invite_bullet.gif'; ?>) no-repeat 89% 52%;

}
	</style>
	<?php
}

class BP_Invite_Anyone extends BP_Group_Extension {

	public $enable_nav_item    = true;
	public $enable_create_step = true;
	public $enable_edit_item   = false;

	public function __construct() {
		global $bp;

		$this->has_caps = true;

		$args = [
			'slug'              => BP_INVITE_ANYONE_SLUG,
			'name'              => __( 'Send Invites', 'invite-anyone' ),
			'show_tab'          => $this->enable_nav_item(),
			'access'            => 'member',
			'nav_item_position' => 71,
			'screens'           => [
				'create' => [
					'enabled'  => $this->enable_create_step(),
					'position' => 42,
				],
			],
		];

		parent::init( $args );
	}

	/**
	 * Display the group tab.
	 *
	 * @param int $group_id Available only on BP 2.2+.
	 */
	public function display( $group_id = null ) {
		global $bp;

		if ( BP_INVITE_ANYONE_SLUG === $bp->current_action && isset( $bp->action_variables[0] ) && 'send' === $bp->action_variables[0] ) {
			if ( ! check_admin_referer( 'groups_send_invites', '_wpnonce_send_invites' ) ) {
				return false;
			}

			// Send the invites.
			groups_send_invites( $bp->loggedin_user->id, $bp->groups->current_group->id );

			do_action( 'groups_screen_group_invite', $bp->groups->current_group->id );

			// Hack to imitate bp_core_add_message, since bp_core_redirect is giving me such hell
			echo '<div id="message" class="updated"><p>' . esc_html__( 'Group invites sent.', 'invite-anyone' ) . '</p></div>';
		}

		invite_anyone_create_screen_content( 'invite' );
	}

	public function create_screen( $group_id = null ) {
		global $bp;

		/* If we're not at this step, go bye bye */
		if ( ! bp_is_group_creation_step( $this->slug ) ) {
			return false;
		}

		invite_anyone_create_screen_content( 'create' );

		wp_nonce_field( 'groups_create_save_' . $this->slug );
	}

	public function create_screen_save( $group_id = null ) {
		global $bp;

		/* Always check the referer */
		check_admin_referer( 'groups_create_save_' . $this->slug );

		/* Set method and save */
		if ( bp_group_has_invites() ) {
			$this->has_invites = true;
		}
		$this->method = 'create';
		$this->save( $group_id );
	}

	public function save( $group_id = null ) {
		global $bp;

		if ( null === $group_id ) {
			$group_id = bp_get_current_group_id();
		}

		/* Set error redirect based on save method */
		if ( 'create' === $this->method ) {
			$redirect_url = bp_groups_get_create_url( [ $this->slug ] );
		} else {
			$redirect_url = bp_get_group_manage_url(
				$group_id,
				bp_groups_get_path_chunks( [ $this->slug ], 'manage' )
			);
		}

		groups_send_invites( $bp->loggedin_user->id, $group_id );

		if ( $this->has_invites ) {
			bp_core_add_message( __( 'Group invites sent.', 'invite-anyone' ) );
		} else {
			bp_core_add_message( __( 'Group created successfully.', 'invite-anyone' ) );
		}
	}

	/**
	 * Should the group creation step be included?
	 *
	 * @since 1.2
	 */
	public function enable_create_step() {
		$options = invite_anyone_options();
		return ! empty( $options['group_invites_enable_create_step'] ) && 'yes' === $options['group_invites_enable_create_step'];
	}

	public function enable_nav_item() {
		global $bp;

		// Group-specific settings always override
		if ( ! bp_groups_user_can_send_invites() ) {
			return false;
		}

		if ( 'anyone' === invite_anyone_group_invite_access_test() ) {
			return true;
		} else {
			return false;
		}
	}

	public function widget_display() {}
}
bp_register_group_extension( 'BP_Invite_Anyone' );


function invite_anyone_catch_group_invites() {
	global $bp;

	if ( BP_INVITE_ANYONE_SLUG === $bp->current_action && isset( $bp->action_variables[0] ) && 'send' === $bp->action_variables[0] ) {
		if ( ! check_admin_referer( 'groups_send_invites', '_wpnonce_send_invites' ) ) {
			return false;
		}

		// Send the invites.
		$bp_version = defined( BP_VERSION ) ? BP_VERSION : '1.2';
		if ( version_compare( $bp_version, '5.0.0', '>=' ) ) {
			groups_send_invites(
				array(
					'user_id'  => bp_loggedin_user_id(),
					'group_id' => bp_get_current_group_id(),
				)
			);
		} else {
			groups_send_invites( $bp->loggedin_user->id, $bp->groups->current_group->id );
		}

		bp_core_add_message( __( 'Group invites sent.', 'invite-anyone' ) );

		do_action( 'groups_screen_group_invite', $bp->groups->current_group->id );

		$redirect_url = bp_get_group_url(
			groups_get_current_group(),
			bp_groups_get_path_chunks( [ BP_INVITE_ANYONE_SLUG ] )
		);

		bp_core_redirect( $redirect_url );
	}
}
add_action( 'wp', 'invite_anyone_catch_group_invites', 1 );

function invite_anyone_create_screen_content() {
	$template = function_exists( 'bp_locate_template' ) ? bp_locate_template( 'groups/single/invite-anyone.php', false ) : locate_template( 'groups/single/invite-anyone.php', false );

	if ( $template ) {
		include $template;
	} else {
		include_once 'templates/invite-anyone.php';
	}
}

/* Creates the list of members on the Sent Invite screen */
function bp_new_group_invite_member_list() {
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo bp_get_new_group_invite_member_list();
}

function bp_get_new_group_invite_member_list( $args = '' ) {
	global $bp;

	$defaults = array(
		'group_id'  => false,
		'separator' => 'li',
	);

	$r = wp_parse_args( $args, $defaults );

	$group_id  = $r['group_id'];
	$separator = $r['separator'];

	if ( ! $group_id ) {
		$group_id = isset( $bp->groups->new_group_id ) ? $bp->groups->new_group_id : $bp->groups->current_group->id;
	}

	$items = array();

	$friends = get_members_invite_list( $bp->loggedin_user->id, $group_id );

	if ( $friends ) {
		$invites = groups_get_invites_for_group( $bp->loggedin_user->id, $group_id );

		$friend_count = count( $friends );
		for ( $i = 0; $i < $friend_count; $i++ ) {
			$checked = '';
			if ( $invites ) {
				if ( in_array( (int) $friends[ $i ]['id'], $invites, true ) ) {
					$checked = ' checked="checked"';
				}
			}

			$items[] = '<' . $separator . '><input' . $checked . ' type="checkbox" name="friends[]" id="f-' . $friends[ $i ]['id'] . '" value="' . esc_html( $friends[ $i ]['id'] ) . '" /> <label for="f-' . $friends[ $i ]['id'] . '">' . $friends[ $i ]['full_name'] . '</label></' . $separator . '>';
		}
	}

	return implode( "\n", (array) $items );
}

/**
 * Fetch a list of site members eligible to be invited to a group.
 *
 * The list is essentially a list of everyone on the site, minus the logged in user and members
 * of the current group.
 *
 * @package Invite Anyone
 * @since 1.0
 *
 * @param int    $group_id     The group_id you want to exclude
 * @param string $search_terms If you want to search on username/display name
 * @param string $fields       Fields to retrieve. 'ID' or 'all'.
 * @return array $users An array of located users
 */
function invite_anyone_invite_query( $group_id = false, $search_terms = false, $fields = 'all' ) {
	// Get a list of group members to be excluded from the main query
	$group_members = array();
	$args          = array(
		'group_id'   => $group_id,
		'group_role' => array( 'member', 'mod', 'admin', 'banned' ),
	);
	if ( $search_terms ) {
		$args['search'] = $search_terms;
	}

	$gm            = groups_get_group_members( $args );
	$group_members = wp_list_pluck( $gm['members'], 'ID' );

	// Don't include the logged-in user, either
	$group_members[] = bp_loggedin_user_id();

	$fields = 'ID' === $fields ? 'ID' : 'all';

	// Now do a user query
	// Pass a null blog id so that the capabilities check is skipped. For BP blog_id doesn't
	// matter anyway
	$user_query = new Invite_Anyone_User_Query(
		array(
			'blog_id' => null,
			'exclude' => $group_members,
			'search'  => $search_terms,
			'fields'  => $fields,
			'orderby' => 'display_name',
		)
	);

	return $user_query->results;
}

/**
 * Extends the WP_User_Query class to make it easier for us to search across different fields
 *
 * @package Invite Anyone
 * @since 1.0.4
 */
class Invite_Anyone_User_Query extends WP_User_Query {
	public function __construct( $query = null ) {
		add_action( 'pre_user_query', array( &$this, 'filter_registered_users_only' ) );
		parent::__construct( $query );
	}

	/**
	 * BuddyPress has different user statuses.  We need to filter the user list so only registered users are shown.
	 */
	public function filter_registered_users_only( $query ) {
		$query->query_where .= ' AND user_status = 0';
	}

	/**
	 * @see WP_User_Query::get_search_sql()
	 */
	public function get_search_sql( $the_string, $cols, $wild = false ) {
		global $wpdb;

		// Always search all columns
		$cols = array(
			'user_email',
			'user_login',
			'user_nicename',
			'user_url',
			'display_name',
		);

		// Always do 'both' for trailing_wild
		$wild = 'both';

		$searches      = array();
		$leading_wild  = ( 'leading' === $wild || 'both' === $wild ) ? '%' : '';
		$trailing_wild = ( 'trailing' === $wild || 'both' === $wild ) ? '%' : '';

		if ( method_exists( $wpdb, 'esc_like' ) ) {
			$escaped_string = $wpdb->esc_like( $the_string );
		} else {
			$escaped_string = addcslashes( $the_string, '_%\\' );
		}

		$like_string = $leading_wild . $escaped_string . $trailing_wild;
		foreach ( $cols as $col ) {
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$searches[] = $wpdb->prepare( "$col LIKE %s", $like_string );
		}

		return ' AND (' . implode( ' OR ', $searches ) . ') AND user_status = 0';
	}
}

function get_members_invite_list( $user_id = false, $group_id = null ) {
	global $bp, $wpdb;

	if ( ! $group_id ) {
		$group_id = bp_get_current_group_id();
	}

	$users = invite_anyone_invite_query( $group_id, false, 'ID' );
	if ( $users ) {
		foreach ( (array) $users as $user_id ) {
			$display_name = bp_core_get_user_displayname( $user_id );

			if ( ! empty( $display_name ) ) {
				$friends[] = array(
					'id'        => $user_id,
					'full_name' => $display_name,
				);
			}
		}
	}

	if ( ! isset( $friends ) ) {
		return false;
	}

	return $friends;
}

function invite_anyone_ajax_invite_user() {
	global $bp;

	check_ajax_referer( 'groups_invite_uninvite_user' );

	if ( ! $_POST['friend_id'] || ! $_POST['friend_action'] || ! $_POST['group_id'] ) {
		return false;
	}

	$friend_action = sanitize_text_field( wp_unslash( $_POST['friend_action'] ) );
	$friend_id     = (int) $_POST['friend_id'];
	$group_id      = (int) $_POST['group_id'];

	if ( 'invite' === $friend_action ) {

		if ( ! groups_invite_user(
			array(
				'user_id'  => $friend_id,
				'group_id' => $group_id,
			)
		) ) {
			return false;
		}

		$user = new BP_Core_User( $friend_id );

		$group_slug = isset( $bp->groups->root_slug ) ? $bp->groups->root_slug : $bp->groups->slug;

		$referer         = wp_get_referer();
		$is_group_create = 0 === strpos( $referer, bp_groups_get_create_url() );
		if ( $is_group_create ) {
			$uninvite_url = add_query_arg( 'user_id', $user->id, bp_groups_get_create_url( [ 'invite-anyone' ] ) );
		} else {
			$uninvite_url = bp_get_group_url(
				groups_get_current_group(),
				bp_groups_get_path_chunks( [ BP_INVITE_ANYONE_SLUG, 'remove', $user->id ] )
			);
		}

		echo '<li id="uid-' . esc_attr( $user->id ) . '">';

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo bp_core_fetch_avatar( array( 'item_id' => $user->id ) );

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<h4>' . bp_core_get_userlink( $user->id ) . '</h4>';

		echo '<span class="activity">' . esc_html( $user->last_active ) . '</span>';
		echo '<div class="action">
				<a class="remove" href="' . esc_url( wp_nonce_url( $uninvite_url ) ) . '" id="uid-' . esc_attr( $user->id ) . '">' . esc_html__( 'Remove Invite', 'invite-anyone' ) . '</a>
			  </div>';
		echo '</li>';

	} elseif ( 'uninvite' === $friend_action ) {
		groups_uninvite_user( $friend_id, $group_id );
	}

	die();
}
add_action( 'wp_ajax_invite_anyone_groups_invite_user', 'invite_anyone_ajax_invite_user' );

function invite_anyone_ajax_autocomplete_results() {
	// phpcs:ignore WordPress.Security.NonceVerification
	$query = sanitize_text_field( wp_unslash( $_REQUEST['query'] ) );

	$return = array(
		'query'       => $query,
		'data'        => array(),
		'suggestions' => array(),
	);

	$users = invite_anyone_invite_query( bp_get_current_group_id(), $query );

	if ( $users ) {
		$suggestions = array();
		$data        = array();

		foreach ( $users as $user ) {
			$suggestions[] = $user->display_name . ' (' . $user->user_login . ')';
			$data[]        = $user->ID;
		}

		$return['suggestions'] = $suggestions;
		$return['data']        = $data;
	}

	die( wp_json_encode( $return ) );
}
add_action( 'wp_ajax_invite_anyone_autocomplete_ajax_handler', 'invite_anyone_ajax_autocomplete_results' );

function invite_anyone_remove_group_creation_invites( $a ) {

	foreach ( $a as $key => $value ) {
		if ( 'group-invites' === $key ) {
			unset( $a[ $key ] );
		}
	}
	return $a;
}

function invite_anyone_remove_invite_subnav() {
	global $bp;

	if ( 'friends' === invite_anyone_group_invite_access_test() ) {
		return;
	}

	if ( isset( $bp->groups->group_creation_steps['group-invites'] ) ) {
		unset( $bp->groups->group_creation_steps['group-invites'] );
	}

	// BP 1.5 / BP 1.2
	$parent_slug = isset( $bp->groups->root_slug ) && isset( $bp->groups->current_group->slug ) ? $bp->groups->current_group->slug : $bp->groups->slug;

	bp_core_remove_subnav_item( $parent_slug, 'send-invites' );
}
add_filter( 'groups_create_group_steps', 'invite_anyone_remove_group_creation_invites', 1 );
add_action( 'bp_setup_nav', 'invite_anyone_remove_invite_subnav', 15 );

/**
 * Determine access setting for a group/user pair.
 *
 * @param int $group_id Group ID. Default: current group ID.
 * @param int $user_id User ID. Default: current user ID.
 */
function invite_anyone_group_invite_access_test( $group_id = 0, $user_id = 0 ) {
	global $current_user, $bp;

	if ( empty( $group_id ) ) {
		$group_id = bp_is_group() ? bp_get_current_group_id() : 0;
	}

	if ( empty( $group_id ) && ! bp_is_group_create() ) {
		return 'noone';
	}

	if ( empty( $user_id ) ) {
		$user_id = bp_loggedin_user_id();
	}

	if ( empty( $user_id ) ) {
		return 'noone';
	}

	$iaoptions = invite_anyone_options();

	if ( bp_is_group_create() ) {
		if ( empty( $iaoptions['group_invites_can_group_admin'] ) || 'anyone' === $iaoptions['group_invites_can_group_admin'] || ! $iaoptions['group_invites_can_group_admin'] ) {
			return 'anyone';
		}
		if ( 'friends' === $iaoptions['group_invites_can_group_admin'] ) {
			return 'friends';
		}
		if ( 'noone' === $iaoptions['group_invites_can_group_admin'] ) {
			return 'noone';
		}
	}

	if ( ! groups_is_user_member( $user_id, $group_id ) ) {
		return 'noone';
	}

	if ( user_can( $user_id, 'bp_moderate' ) ) {
		if ( empty( $iaoptions['group_invites_can_admin'] ) || 'anyone' === $iaoptions['group_invites_can_admin'] || ! $iaoptions['group_invites_can_admin'] ) {
			return 'anyone';
		}
		if ( 'friends' === $iaoptions['group_invites_can_admin'] ) {
			return 'friends';
		}
		if ( 'noone' === $iaoptions['group_invites_can_admin'] ) {
			return 'noone';
		}
	} elseif ( groups_is_user_admin( $user_id, $group_id ) ) {
		if ( empty( $iaoptions['group_invites_can_group_admin'] ) || 'anyone' === $iaoptions['group_invites_can_group_admin'] || ! $iaoptions['group_invites_can_group_admin'] ) {
			return 'anyone';
		}
		if ( 'friends' === $iaoptions['group_invites_can_group_admin'] ) {
			return 'friends';
		}
		if ( 'noone' === $iaoptions['group_invites_can_group_admin'] ) {
			return 'noone';
		}
	} elseif ( groups_is_user_mod( $user_id, $group_id ) ) {
		if ( empty( $iaoptions['group_invites_can_group_mod'] ) || 'anyone' === $iaoptions['group_invites_can_group_mod'] || ! $iaoptions['group_invites_can_group_mod'] ) {
			return 'anyone';
		}
		if ( 'friends' === $iaoptions['group_invites_can_group_mod'] ) {
			return 'friends';
		}
		if ( 'noone' === $iaoptions['group_invites_can_group_mod'] ) {
			return 'noone';
		}
	} else {
		if ( empty( $iaoptions['group_invites_can_group_member'] ) || 'anyone' === $iaoptions['group_invites_can_group_member'] || ! $iaoptions['group_invites_can_group_member'] ) {
			return 'anyone';
		}
		if ( 'friends' === $iaoptions['group_invites_can_group_member'] ) {
			return 'friends';
		}
		if ( 'noone' === $iaoptions['group_invites_can_group_member'] ) {
			return 'noone';
		}
	}

	return 'noone';
}

function invite_anyone_group_invite_form_action() {
	$url = bp_get_group_url(
		groups_get_current_group(),
		bp_groups_get_path_chunks( [ BP_INVITE_ANYONE_SLUG, 'send' ] )
	);

	echo esc_url( $url );
}

/**
 * Catch the 'to' email address of sent email notifications, and hook message filter if necessary
 *
 * This function is necessary because the groups_notification_group_invites_message
 * filter doesn't receive easily parsable info about the invitee.
 *
 * @since 1.0.22
 */
function invite_anyone_group_invite_maybe_filter_invite_message( $to ) {
	if ( ! bp_is_active( 'friends' ) ) {
		return $to;
	}

	$invited_user = get_user_by( 'email', $to );

	$friendship_status = BP_Friends_Friendship::check_is_friend( bp_loggedin_user_id(), $invited_user->ID );
	if ( 'is_friend' !== $friendship_status ) {
		add_action( 'groups_notification_group_invites_message', 'invite_anyone_group_invite_email_message', 10, 6 );
	}

	return $to;
}
add_filter( 'groups_notification_group_invites_to', 'invite_anyone_group_invite_maybe_filter_invite_message' );

/**
 * Filter the invitation email notification text
 *
 * BP's invitation notification text assumes that the inviter and invitee are
 * friends. This isn't always the case with Invite Anyone. This function
 * detects whether a swap is necessary, and if so, makes it happen.
 *
 * @since 1.0.22
 */
function invite_anyone_group_invite_email_message( $message, $group, $inviter_name, $inviter_link, $invites_link, $group_link ) {
	// Make sure the check happens again fresh next time around
	remove_action( 'groups_notification_group_invites_message', 'invite_anyone_group_invite_email_message', 10, 6 );

	$message = sprintf(
		// translators: 1. Inviter name, 2. Group name, 3. Invites link, 4. Group link, 5. Inviter name, 6. Inviter link
		__(
			'%1$s has invited you to the group: "%2$s".

To view your group invites visit: %3$s

To view the group visit: %4$s

To view %5$s\'s profile visit: %6$s

---------------------
',
			'invite-anyone'
		),
		$inviter_name,
		$group->name,
		$invites_link,
		$group_link,
		$inviter_name,
		$inviter_link
	);

	return $message;
}

/**
 * Wrapper for wp_is_large_network() that supports non-MS.
 *
 * @since 1.1.2
 */
function invite_anyone_is_large_network() {
	if ( function_exists( 'wp_is_large_network' ) ) {
		$is_large_network = wp_is_large_network( 'users' );
		$count            = get_user_count();
	} else {
		global $wpdb;
		$count = get_transient( 'ia_user_count' );
		if ( false === $count ) {
			$count = $wpdb->get_var( "SELECT COUNT(ID) FROM $wpdb->users WHERE user_status = '0'" );
			set_transient( 'ia_user_count', $count, 60 * 60 * 24 );
		}
		$is_large_network = $count > 10000;
		return apply_filters( 'invite_anyone_is_large_network', $count > 10000, $count );
	}

	return apply_filters( 'invite_anyone_is_large_network', $is_large_network, $count );
}
