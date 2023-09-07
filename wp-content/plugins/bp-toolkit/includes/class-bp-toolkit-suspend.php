<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * The class that looks after the Toolkit's Suspend function
 *
 * @since      1.0.0
 * @package    BP_Toolkit
 * @subpackage BP_Toolkit/includes
 * @author     Ben Roberts
 */
class BPTK_Suspend {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $bp_toolkit The ID of this plugin.
	 */
	private $bp_toolkit;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $bp_toolkit The name of this plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $bp_toolkit, $version ) {

		$this->bp_toolkit = $bp_toolkit;
		$this->version    = $version;

		add_action( 'bp_init', array( $this, 'toggle_suspension' ) );
		if ( class_exists( 'Youzify' ) ) {
			add_action( 'youzify_after_header_cover_head_content', array( $this, 'add_profile_suspend_button' ) );
		} else {
			add_action( 'bp_member_header_actions', array( $this, 'add_profile_suspend_button' ) );
		}
		add_action( 'bp_directory_members_actions', array( $this, 'add_list_suspend_button' ) );
		add_filter( 'authenticate', array( $this, 'prevent_login' ), 40 );
		add_filter( 'views_users', array( $this, 'create_view' ), 40 );
		add_action( 'pre_get_users', array( $this, 'filter_suspended_users' ), 20 );
		add_action( 'bp_template_redirect', array( $this, 'redirect_on_suspended_users' ) );
		add_filter( 'bp_after_has_members_parse_args', array( $this, 'exclude_suspended_users' ), 999 );
		add_filter( 'user_row_actions', array( $this, 'row_actions' ), 10, 2 );
		add_filter( 'bp_activity_get', array( $this, 'filter_comments' ) );

		add_filter( 'bp_activity_set_public_scope_args', array( $this, 'override_scope' ), 99 );
		add_filter( 'bp_activity_set_friends_scope_args', array( $this, 'override_scope' ), 99 );
		add_filter( 'bp_activity_set_groups_scope_args', array( $this, 'override_scope' ), 99 );
		add_filter( 'bp_activity_set_mentions_scope_args', array( $this, 'override_scope' ), 99 );
		add_filter( 'bp_activity_set_following_scope_args', array( $this, 'override_scope' ), 99 );
		add_filter( 'bp_after_has_activities_parse_args', array( $this, 'filter_activities' ), 99 );

		add_filter( 'comments_array', array( $this, 'filter_wp_comments' ) );
		add_filter( 'comment_text', array( $this, 'filter_wp_comment_content' ), 10, 3 );

		if ( bp_is_active( 'document' ) ) {
			add_filter( 'bp_document_get_join_sql_document', array( $this, 'filter_documents' ), 100 );
			add_filter( 'bp_document_get_join_sql_folder', array( $this, 'filter_folders' ), 100 );
		}
	}

	/**
	 * Add suspend Button to Member profiles.
	 * @since 1.0
	 */
	public function add_profile_suspend_button() {
		if ( ! is_user_logged_in() ) {
			return;
		}
		$user_id    = get_current_user_id();
		$member_id  = bp_displayed_user_id();
		$status     = $this->is_suspended( $member_id );
		$options    = get_option( 'suspend_section' );
		$placements = isset( $options['bptk_suspend_placement'] ) ? (array) $options['bptk_suspend_placement'] : [];
		if ( in_array( 'profiles', $placements ) ) {
			return;
		}

		if ( bp_is_my_profile() || user_can( $member_id, BPTK_ADMIN_CAP ) || ! user_can( $user_id, 'suspend_users' ) ) {
			return;
		}

		if ( class_exists( 'Youzify' ) ) {
			if ( empty( $status ) || $status == 0 ) {
				echo '<li style="cursor: pointer;" class="bptk-suspend-profile youzify-name"><a href="' . $this->bptk_suspend_link( $member_id ) . '" class="activity-button"><i class="fa fa-lock" aria-hidden="true"></i><span>' . __( 'Suspend User',
						'bp-toolkit' ) . '</span></a></li>';
			} else {
				echo '<li style="cursor: pointer;" class="bptk-suspend-profile youzify-name"><a href="' . $this->bptk_unsuspend_link( $member_id ) . '" class="activity-button"><i class="fa fa-unlock" aria-hidden="true"></i><span>' . __( 'Unsuspend User',
						'bp-toolkit' ) . '</span></a></li>';
			}

			return;
		}

		if ( empty( $status ) || $status == 0 ) {
			if ( bp_get_theme_package_id() == 'nouveau' ) {
				echo '<li class="generic-button bptk-suspend-profile"><a href="' . $this->bptk_suspend_link( $member_id ) . '" class="activity-button">' . __( 'Suspend',
						'bp-toolkit' ) . '</a></li>';
			} elseif ( class_exists( 'Youzify' ) ) {
				echo '<li style="cursor: pointer;" class="bptk-suspend-profile youzify-name"><a href="' . $this->bptk_suspend_link( $member_id ) . '" class="activity-button"><i class="fa fa-lock" aria-hidden="true"></i><span>' . __( 'Suspend User',
						'bp-toolkit' ) . '</span></a></li>';
			} else {
				echo '<div class="generic-button bptk-suspend-profile"><a href="' . $this->bptk_suspend_link( $member_id ) . '" class="activity-button">' . __( 'Suspend',
						'bp-toolkit' ) . '</a></div>';
			}
		} else {
			if ( bp_get_theme_package_id() == 'nouveau' ) {
				echo '<li class="generic-button bptk-suspend-profile"><a href="' . $this->bptk_unsuspend_link( $member_id ) . '" class="activity-button">' . __( 'Unsuspend',
						'bp-toolkit' ) . '</a></li>';
			} elseif ( class_exists( 'Youzify' ) ) {
				echo '<li style="cursor: pointer;" class="bptk-suspend-profile youzify-name"><a href="' . $this->bptk_unsuspend_link( $member_id ) . '" class="activity-button"><i class="fa fa-unlock" aria-hidden="true"></i><span>' . __( 'Unsuspend User',
						'bp-toolkit' ) . '</span></a></li>';
			} else {
				echo '<div class="generic-button bptk-suspend-profile"><a href="' . $this->bptk_unsuspend_link( $member_id ) . '" class="activity-button">' . __( 'Unsuspend',
						'bp-toolkit' ) . '</a></div>';
			}
		}
	}

	/**
	 * Add suspend Button to Member lists.
	 * @since 1.0
	 */
	public function add_list_suspend_button() {
		if ( ! is_user_logged_in() ) {
			return;
		}
		$user_id    = get_current_user_id();
		$member_id  = bp_get_member_user_id();
		$status     = $this->is_suspended( $member_id );
		$options    = get_option( 'suspend_section' );
		$placements = isset( $options['bptk_suspend_placement'] ) ? (array) $options['bptk_suspend_placement'] : [];
		if ( in_array( 'directory', $placements ) ) {
			return;
		}

		if ( class_exists( 'Youzify' ) ) {

			if ( ! user_can( $user_id, BPTK_ADMIN_CAP ) ) {
				return;
			} elseif ( user_can( $member_id, BPTK_ADMIN_CAP ) ) {
				echo '<div style="opacity: 50%; pointer-events: none;" class="generic-button bptk-suspend-list"><a href="" class="activity-button">' . __( 'Suspend',
						'bp-toolkit' ) . '</a></div>';

				return;
			}

			if ( empty( $status ) || $status == 0 ) {
				echo '<div class="generic-button bptk-suspend-list"><a href="' . $this->bptk_suspend_link( $member_id ) . '" class="activity-button">' . __( 'Suspend',
						'bp-toolkit' ) . '</a></div>';
			} else {
				echo '<div class="generic-button bptk-suspend-list"><a href="' . $this->bptk_unsuspend_link( $member_id ) . '" class="activity-button">' . __( 'Unsuspend',
						'bp-toolkit' ) . '</a></div>';
			}

			return;
		}

		if ( $user_id == $member_id || user_can( $member_id, BPTK_ADMIN_CAP ) || ! user_can( $user_id,
				BPTK_ADMIN_CAP ) ) {
			return;
		}

		if ( empty( $status ) || $status == 0 ) {
			if ( bp_get_theme_package_id() == 'nouveau' ) {
				echo '<li class="generic-button bptk-suspend-list"><a href="' . $this->bptk_suspend_link( $member_id ) . '" class="activity-button">' . __( 'Suspend',
						'bp-toolkit' ) . '</a></li>';
			} else {
				echo '<div class="generic-button bptk-suspend-list"><a href="' . $this->bptk_suspend_link( $member_id ) . '" class="activity-button">' . __( 'Suspend',
						'bp-toolkit' ) . '</a></div>';
			}
		} else {
			if ( bp_get_theme_package_id() == 'nouveau' ) {
				echo '<li class="generic-button bptk-suspend-list"><a href="' . $this->bptk_unsuspend_link( $member_id ) . '" class="activity-button">' . __( 'Unsuspend',
						'bp-toolkit' ) . '</a></li>';
			} else {
				echo '<div class="generic-button bptk-suspend-list"><a href="' . $this->bptk_unsuspend_link( $member_id ) . '" class="activity-button">' . __( 'Unsuspend',
						'bp-toolkit' ) . '</a></div>';
			}
		}
	}

	/**
	 * Constructs link to pass when blocking.
	 * @since 1.0
	 */
	public function bptk_suspend_link( $member_id = 0 ) {
		return apply_filters( 'bptk_suspend_link', add_query_arg( array(
			'action' => 'suspend',
			'member' => $member_id,
			'token'  => wp_create_nonce( 'suspend-' . $member_id )
		) ), $member_id );
	}

	/**
	 * Constructs link to pass when unblocking.
	 * @since 1.0
	 */
	public function bptk_unsuspend_link( $member_id = 0 ) {
		return apply_filters( 'bptk_unsuspend_link', add_query_arg( array(
			'action' => 'unsuspend',
			'member' => $member_id,
			'token'  => wp_create_nonce( 'unsuspend-' . $member_id )
		) ), $member_id );
	}

	/**
	 * Create a new row action for the users table.
	 *
	 * @since 2.0.0
	 *
	 */
	public function row_actions( $actions, $user_object ) {

		if ( user_can( $user_object->ID, BPTK_ADMIN_CAP ) ) {
			return $actions;
		}

		if ( $this->is_suspended( $user_object->ID ) ) {
			$actions['suspend'] = '<a href="' . $this->bptk_unsuspend_link( $user_object->ID ) . '" class="activity-button">' . __( 'Unsuspend',
					'bp-toolkit' ) . '</a>';
		} else {
			$actions['suspend'] = '<a href="' . $this->bptk_suspend_link( $user_object->ID ) . '" class="activity-button">' . __( 'Suspend',
					'bp-toolkit' ) . '</a>';
		}

		return $actions;
	}

	/**
	 * Suspends member.
	 * @since 1.0
	 */
	public function suspend( $member_id = 0 ) {

		update_user_meta( $member_id, 'bptk_suspend', 1 );

		if ( $this->is_suspended( $member_id ) == 1 ) {
			$this->destroy_sessions( $member_id );
		}
	}

	/**
	 * Unsuspends member.
	 * @since 1.0
	 */
	public function unsuspend( $member_id = 0 ) {

		update_user_meta( $member_id, 'bptk_suspend', 0 );
	}

	/**
	 * Returns whether member is suspended.
	 * @since 1.0
	 */
	public function is_suspended( $member_id = 0 ) {

		$status = get_user_meta( $member_id, 'bptk_suspend', true );

		if ( $status == 0 || empty( $status ) ) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Returns all suspended members.
	 *
	 * @since 2.1.0
	 */
	public function get_suspended_users() {

		$args    = array(
			'meta_query' => array(
				array(
					'key'     => 'bptk_suspend',
					'value'   => '1',
					'compare' => '='
				)
			)
		);
		$members = get_users( $args );

		return $members;
	}

	/**
	 * Counts all suspended members.
	 *
	 * @since 2.1.0
	 */
	public function count_suspended_users() {

		$count = count( $this->get_suspended_users() );

		return $count;
	}

	/**
	 * This is where the magic happens. Suspend/unsuspend depending
	 * on what arguments were passed via our links above.
	 * @since 1.0
	 */
	public function toggle_suspension() {
		if ( ! is_user_logged_in() ) {
			return;
		}
		if ( ! isset( $_REQUEST['action'] ) || ! isset( $_REQUEST['member'] ) || ! isset( $_REQUEST['token'] ) ) {
			return;
		}
		switch ( $_REQUEST['action'] ) {
			case 'suspend' :
				if ( wp_verify_nonce( $_REQUEST['token'], 'suspend-' . $_REQUEST['member'] ) ) {

					bptk_suspend_member( $_REQUEST['member'] );

					bp_core_add_message( __( 'User successfully suspended', 'bp-toolkit' ) );
				}
				break;
			case 'unsuspend' :
				if ( wp_verify_nonce( $_REQUEST['token'], 'unsuspend-' . $_REQUEST['member'] ) ) {

					bptk_unsuspend_member( $_REQUEST['member'] );

					bp_core_add_message( __( 'User successfully unsuspended', 'bp-toolkit' ) );
				}
				break;
		}

		wp_safe_redirect( wp_get_referer() );
		exit();
	}

	/**
	 * Stop a suspended member from logging in, and display an error.
	 * @since 1.0
	 */
	public function prevent_login( $member = null ) {

		if ( ! bptk_prevent_login_enabled() ) {
			return $member;
		}

		// If login already failed, get out
		if ( is_wp_error( $member ) || empty( $member->ID ) ) {
			return $member;
		}

		// Set the user id.
		$member_id = (int) $member->ID;

		// If the user is blocked, set the wp-login.php error message.
		if ( $this->is_suspended( $member_id ) ) {

			$options         = get_option( 'suspend_section' );
			$default_message = __( 'This account has been suspended. Please contact an administrator.', 'bp-toolkit' );
			// Set the message, or a default message if none specified.
			$message = isset( $options['bptk_suspend_login_message'] ) ? $options['bptk_suspend_login_message'] : $default_message;

			// Set an error object to short-circuit the authentication process.
			$member = new WP_Error( 'bptk_suspended_user', $message );
		}

		return apply_filters( 'prevent_login', $member, $member_id );
	}

	/**
	 * Destroys all the user sessions for the specified user.
	 * @since 1.0
	 */
	public static function destroy_sessions( $member_id = 0 ) {
		if ( ! bptk_prevent_login_enabled() ) {
			return;
		}

		// Bail if no member id.
		if ( empty( $member_id ) ) {
			return;
		}

		// Get the user's sessions object and destroy all sessions.
		WP_Session_Tokens::get_instance( $member_id )->destroy_all();
	}

	/**
	 * Add a view to users table to select suspended members.
	 *
	 * @since 2.1.0
	 */
	public function create_view( $views ) {

		$count = $this->count_suspended_users();

		if ( is_network_admin() ) {
			$base_url = network_admin_url( 'users.php' );
		} else {
			$base_url = bp_get_admin_url( 'users.php' );
		}

		$url  = add_query_arg( 'suspended', 'true', $base_url );
		$text = sprintf( _x( 'Suspended %s', 'suspended users', 'bp-toolkit' ),
			'<span class="count">(' . number_format_i18n( $count ) . ')</span>' );

		$views['suspended'] = sprintf( '<a href="%1$s">%2$s</a>', esc_url( $url ), $text );

		return $views;
	}

	/**
	 * Filters the users list to only show suspended members.
	 *
	 * @since 2.1.0
	 */
	public function filter_suspended_users( $query ) {

		if ( ! function_exists( 'get_current_screen' ) ) {
			return;
		}

		// Only apply on the Users screen in the dashboard
		$screen = get_current_screen();
		if ( ! $screen || $screen->id !== 'users' ) {
			return;
		}

		if ( isset( $_REQUEST['suspended'] ) ) {
			$query->set( 'meta_key', 'bptk_suspend' );
			$query->set( 'meta_value', '1' );
		}
	}

	/**
	 * Redirect visitor if they try to access suspended profile.
	 *
	 * @since 3.0.0
	 *
	 */
	public function redirect_on_suspended_users() {
		if ( ! bptk_prevent_login_enabled() ) {
			return;
		}

		// Never redirect admins
		if ( current_user_can( 'manage_options' ) ) {
			return;
		}

		$redirect = false;

		if ( bp_is_user() ) {

			// Get Suspended Users.
			$suspended_users = bptk_get_moderated_list( 'member' );
			if ( ! empty( $suspended_users ) ) {
				$redirect = in_array( bp_displayed_user_id(), $suspended_users );
			}
		}

		if ( $redirect ) {
			global $wp_query;
			$wp_query->set_404();
			status_header( 404 );
			get_template_part( 404 );
			exit();
		}

	}

	/**
	 * Exclude suspended users from directories and widgets.
	 *
	 * @since 3.0.0
	 *
	 */
	public function exclude_suspended_users( $args ) {

		// Never prevent admins from viewing suspended members
		if ( current_user_can( 'manage_options' ) ) {
			return $args;
		}

		// Do not exclude in admin.
		if ( ( is_admin() && ! defined( 'DOING_AJAX' ) ) || bp_is_user_friend_requests() ) {
			return $args;
		}

		// Get Suspended Users.
		$suspended_users = bptk_get_moderated_list( 'member' );

		if ( ! empty( $suspended_users ) ) {

			$excluded = isset( $args['exclude'] ) ? $args['exclude'] : array();

			if ( ! is_array( $excluded ) ) {
				$excluded = explode( ',', $excluded );
			}

			$excluded = array_merge( $excluded, $suspended_users );

			$args['exclude'] = $excluded;

		}

		return $args;
	}

	/**
	 * Remove activities if suspended.
	 *
	 * @param array $args Activities.
	 *
	 * @return array
	 * @since 3.0
	 *
	 */
	public function filter_activities( $args ) {

		// Never prevent admins from viewing suspended member's activities
		if ( current_user_can( 'manage_options' ) ) {
			return $args;
		}

		// Get Suspended Users.
		$suspended_users = bptk_get_moderated_list( 'member' );

		if ( ! empty( $suspended_users ) ) {

			$filter_query         = empty( $args['filter_query'] ) ? array() : $args['filter_query'];
			$filter_query[]       = array(
				'column'  => 'user_id',
				'compare' => 'NOT IN',
				'value'   => $suspended_users,
			);
			$args['filter_query'] = $filter_query;

		}

		return $args;
	}

	/**
	 * Filter activities, taking into account BuddyBoss's scope precedence policy.
	 *
	 * @param $r array Scope arguments
	 *
	 * @return array
	 * @since 3.1.2
	 *
	 */
	public function override_scope( $r ) {

		// Never prevent admins from viewing suspended member's avtivities
		if ( current_user_can( 'manage_options' ) ) {
			return $r;
		}

		// Get Suspended Users.
		$suspended_users = bptk_get_moderated_list( 'member' );

		if ( ! empty( $suspended_users ) ) {

			$filter_query      = empty( $r['filter_query'] ) ? array() : $r['filter_query'];
			$filter_query[]    = array(
				'column'  => 'user_id',
				'compare' => 'NOT IN',
				'value'   => $suspended_users
			);
			$r['filter_query'] = $filter_query;

		}

		return $r;
	}

	/**
	 * Prepare a list of suspended users, and pass to our comment looping function.
	 *
	 * @param array $results [activities, total].
	 *
	 * @return array
	 * @since 3.0
	 *
	 */
	public function filter_comments( $results ) {

		// Logged-in check
		if ( ! is_user_logged_in() ) {
			return $results;
		}

		// Never prevent admins from viewing suspended member's avtivities
		if ( current_user_can( 'manage_options' ) ) {
			return $results;
		}

		// Get Suspended Users.
		$suspended_users = bptk_get_moderated_list( 'member' );

		// Get parent activities
		$activities = $results['activities'];

		// If neither side is blocking, enable all comments
		if ( empty( $suspended_users ) ) {
			return $results;
		}

		// Loop through each parent activity
		foreach ( $activities as $key => $activity ) {

			// If the activity doesn't have any comments, move on
			if ( empty( $activity->children ) ) {
				continue;
			}

			// If it does, call our looping function
			$activities[ $key ]->children = $this->filter_looped_comments( $activities[ $key ]->children,
				$suspended_users );
		}
		$results['activities'] = $activities;

		return $results;
	}

	/**
	 * Loop through each comment in tree, and remove if suspended.
	 *
	 * @param array $comments comments.
	 * @param array $suspended_users suspended user ids.
	 *
	 * @return array
	 * @since 3.0
	 *
	 */
	private function filter_looped_comments( $comments, $suspended_users ) {

		// If empty, return
		if ( empty( $comments ) ) {
			return $comments;
		}

		// If not empty, hide the comment if author is in our list, or see if it has a child
		foreach ( $comments as $key => $comment ) {
			if ( in_array( $comment->user_id, $suspended_users ) ) {
				unset( $comments[ $key ] );
				continue;
			}

			// Next, if the comment has another comment below it, restart the magic
			if ( ! empty( $comments[ $key ]->children ) ) {
				$comments[ $key ]->children = $this->filter_looped_comments( $comments[ $key ]->children,
					$suspended_users );
			}
		}

		return $comments;
	}

	/**
	 * Remove documents if suspended.
	 *
	 * @param string $join_sql_document
	 *
	 * @return string
	 * @since 3.1.6
	 */
	public function filter_documents( $join_sql_document ) {

		// Never prevent admins from viewing suspended member's documents
		if ( current_user_can( 'manage_options' ) ) {
			return $join_sql_document;
		}

		// Get Suspended Users.
		$suspended_users = bptk_get_moderated_list( 'member' );

		if ( ! empty( $suspended_users ) ) {

			$suspended_prepped    = implode( ',', wp_parse_id_list( $suspended_users ) );
			$join_sql_document .= "AND d.user_id NOT IN ({$suspended_prepped})";

		}

		return $join_sql_document;
	}

	/**
	 * Remove folders if suspended.
	 *
	 * @param string $join_sql_folder
	 *
	 * @return string
	 * @since 3.1.6
	 */
	public function filter_folders( $join_sql_folder ) {

		// Never prevent admins from viewing suspended member's folders
		if ( current_user_can( 'manage_options' ) ) {
			return $join_sql_folder;
		}

		// Get Suspended Users.
		$suspended_users = bptk_get_moderated_list( 'member' );

		if ( ! empty( $suspended_users ) ) {

			$suspended_prepped    = implode( ',', wp_parse_id_list( $suspended_users ) );
			$join_sql_folder .= "AND f.user_id NOT IN ({$suspended_prepped})";

		}

		return $join_sql_folder;
	}

	/**
	 * Remove comments if suspended.
	 *
	 * @param $comments
	 *
	 * @return mixed
	 */
	public function filter_wp_comments( $comments ) {
		// Never prevent admins from viewing suspended member's comments
		if ( current_user_can( 'manage_options' ) ) {
			return $comments;
		}

		// Get Suspended Users.
		$suspended_users = bptk_get_moderated_list( 'member' );

		if ( ! empty( $suspended_users ) ) {
			foreach ( $comments as $comment => $data ) {
				if ( in_array( intval( $data->user_id ), $suspended_users ) && 0 == $data->comment_parent ) {
					unset( $comments[ $comment ] );
				}
			}
		}

		return $comments;
	}

	/**
	 * Remove comment replies if suspended.
	 *
	 * @param $comment_text
	 * @param $commentObject
	 * @param $args
	 *
	 * @return mixed|string
	 */
	public function filter_wp_comment_content( $comment_text, $commentObject, $args ) {
		// Never prevent admins from viewing suspended member's comments
		if ( current_user_can( 'manage_options' ) ) {
			return $comment_text;
		}

		// Get Suspended Users.
		$suspended_users = bptk_get_moderated_list( 'member' );

		if ( ! empty( $suspended_users ) ) {
			if ( in_array( intval( $commentObject->user_id ), $suspended_users ) ) {
				return '<strong class="bptk-blocked-reply-notice">' . esc_html__( 'This comment is currently unavailable',
						'bp-toolkit' ) . '</strong>';
			}
		}

		return $comment_text;
	}
}
