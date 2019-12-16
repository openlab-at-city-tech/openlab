<?php
/**
 * Plugin Name: Admin Commenters Comments Count
 * Version:     1.9.2
 * Plugin URI:  http://coffee2code.com/wp-plugins/admin-commenters-comments-count/
 * Author:      Scott Reilly
 * Author URI:  http://coffee2code.com/
 * Text Domain: admin-commenters-comments-count
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Description: Displays a count of each commenter's total number of comments (linked to those comments) next to their name on any admin page.
 *
 * Compatible with WordPress 4.6 through 5.3+.
 *
 * =>> Read the accompanying readme.txt file for instructions and documentation.
 * =>> Also, visit the plugin's homepage for additional information and updates.
 * =>> Or visit: https://wordpress.org/plugins/admin-commenters-comments-count/
 *
 * @package Admin_Commenters_Comments_Count
 * @author  Scott Reilly
 * @version 1.9.2
 */

/*
 * TODO:
 * - When a comment gets approved/unapproved via comment action links, update commenter's count accordingly
 * - Allow admin to manually group commenters with different email addresses (allows grouping a person who
 *   may be using multiple email addresses, or maybe admin prefers to group people per organization). The reported
 *   counts would be for the group and not the individual. The link to see the emails would search for all of the
 *   email addresses in the group. Via filter maybe?
 * - Add sortability to 'Comments' column in user table
 * - Consider inserting commenter bomment bubble via 'comment_row_actions' hook like Akismet does, though that
 *   requires introducing a JS dependency.
 *
 */

/*
	Copyright (c) 2009-2020 by Scott Reilly (aka coffee2code)

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

defined( 'ABSPATH' ) or die();

if ( ! class_exists( 'c2c_AdminCommentersCommentsCount' ) ) :

class c2c_AdminCommentersCommentsCount {

	/**
	 * The field name.
	 *
	 * Changing this requires changing .css and .js files.
	 *
	 * @var string
	 * @access private
	 */
	private static $field       = 'commenters_count';

	/**
	 * The field title.
	 *
	 * @var string
	 * @access private
	 */
	private static $field_title = '';

	/**
	 * Memoized array of commenter counts.
	 *
	 * @var string
	 * @access private
	 *
	 * @since 1.7
	 */
	private static $memoized    = array();

	/**
	 * Prevent instantiation.
	 *
	 * @since 1.8
	 */
	private function __construct() {}

	/**
	 * Prevent unserializing an instance.
	 *
	 * @since 1.8
	 */
	private function __wakeup() {}

	/**
	 * Returns version of the plugin.
	 *
	 * @since 1.1.4
	 */
	public static function version() {
		return '1.9.2';
	}

	/**
	 * Initializer
	 */
	public static function init() {
		// Set translatable and filterable strings
		self::$field_title = __( 'Comments', 'admin-commenters-comments-count' );

		// Load textdomain.
		load_plugin_textdomain( 'admin-commenters-comments-count' );

		// Register hooks
		add_filter( 'comment_author',             array( __CLASS__, 'comment_author'          )        );
		add_filter( 'get_comment_author_link',    array( __CLASS__, 'get_comment_author_link' )        );
		add_action( 'admin_enqueue_scripts',      array( __CLASS__, 'enqueue_admin_css'       )        );
		add_filter( 'manage_users_columns',       array( __CLASS__, 'add_user_column'         )        );
		add_filter( 'manage_users_custom_column', array( __CLASS__, 'handle_column_data'      ), 10, 3 );

		// Disable Akismet's version of this functionality.
		add_filter( 'akismet_show_user_comments_approved', '__return_false' );
	}

	/**
	 * Resets cached data.
	 *
	 * @since 1.7
	 */
	public static function reset_cache() {
		self::$memoized = array();
	}

	/**
	 * Enqueues stylesheets if the user has admin expert mode activated.
	 *
	 * @since 1.3
	 */
	public static function enqueue_admin_css() {
		wp_enqueue_style( __CLASS__ . '_admin', plugins_url( 'assets/admin.css', __FILE__ ), array(), self::version() );
	}

	/**
	 * Adds a column for the count of user comments.
	 *
	 * @since 1.4
	 *
	 * @param  array $users_columns Array of user column titles.
	 *
	 * @return array The $posts_columns array with the user comments count column's title added.
	 */
	public static function add_user_column( $users_columns ) {
		$users_columns[ self::$field ] = self::$field_title;

		return $users_columns;
	}

	/**
	 * Outputs a linked count of the user's comments.
	 *
	 * @since 1.4
	 *
	 * @param string $output      Custom column output. Default empty.
	 * @param string $column_name Column name.
	 * @param int    $user_id     ID of the currently-listed user.
	 */
	public static function handle_column_data( $output, $column_name, $user_id ) {
		if ( self::$field != $column_name ) {
			return $output;
		}

		$user = get_user_by( 'id', $user_id );

		list( $comment_count, $pending_count ) = self::get_comments_count( 'comment_author_email', $user->user_email, 'comment', $user->ID );

		$msg = sprintf( _n( '%d comment', '%d comments', $comment_count, 'admin-commenters-comments-count' ), $comment_count );

		return self::get_comments_bubble( $user->user_email, $comment_count, $pending_count, $msg, false );
	}

	/**
	 * Gets the count of comments and pending comments for the given user.
	 *
	 * @since 1.4
	 *
	 * @param  string $field   The comment field value to search. One of: comment_author, comment_author_email, comment_author_IP, comment_author_url, user_id
	 * @param  string $value   The value of the field to check for.
	 * @param  string $type    Optional. comment_type value. Default 'comment'.
	 * @param  int    $user_id Optional. The user ID. This is searched for as an OR to the $field search. If set, forces $type to be 'comment'. Default 0.
	 *
	 * @return array  Array of comment count and pending count.
	*/
	public static function get_comments_count( $field, $value, $type = 'comment', $user_id = 0 ) {
		global $wpdb;

		if ( ! in_array( $field, array( 'comment_author', 'comment_author_email', 'comment_author_IP', 'comment_author_url', 'user_id' ) ) ) {
			$field = 'comment_author_email';
		}

		// Check if count has already been memoized.
		$memoize_key = "{$field}_{$value}_{$type}_" . (int) $user_id;
		if ( isset( self::$memoized[ $memoize_key ] ) && self::$memoized[ $memoize_key ] ) {
			return self::$memoized[ $memoize_key ];
		}

		// Query for counts.
		if ( $user_id && 'user_id' != $field ) {
			$query = "SELECT COUNT(*) FROM {$wpdb->comments} WHERE ( {$field} = %s OR user_id = %d ) AND comment_approved = %d";
			$comment_count = $wpdb->get_var( $wpdb->prepare( $query, $value, $user_id, 1 ) );
			$pending_count = $wpdb->get_var( $wpdb->prepare( $query, $value, $user_id, 0 ) );
		} elseif ( 'comment' == $type ) {
			$query = "SELECT COUNT(*) FROM {$wpdb->comments} WHERE {$field} = %s AND comment_approved = %d";
			$comment_count = $wpdb->get_var( $wpdb->prepare( $query, $value, 1 ) );
			$pending_count = $wpdb->get_var( $wpdb->prepare( $query, $value, 0 ) );
		} elseif ( 'pingback' == $type || 'trackback' == $type ) {
			$query = "SELECT COUNT(*) FROM {$wpdb->comments} WHERE comment_author_url LIKE %s AND comment_type = %s AND comment_approved = %d";
			$comment_count = $wpdb->get_var( $wpdb->prepare( $query, $wpdb->esc_like( $value ) . '%', $type, 1 ) );
			$pending_count = $wpdb->get_var( $wpdb->prepare( $query, $wpdb->esc_like( $value ) . '%', $type, 0 ) );
		} else {
			$comment_count = $pending_count = 0;
		}

		// Return value is array of comment count and pending count.
		$return = array( (int) $comment_count, (int) $pending_count );

		// Memoize the counts being returned.
		self::$memoized[ $memoize_key ] = $return;

		return $return;
	}

	/**
	 * Returns the URL for the admin listing of comments for the given email
	 * address.
	 *
	 * @since 1.4
	 *
	 * @param  string $email Email address.
	 *
	 * @return string
	 */
	public static function get_comments_url( $email ) {
		return add_query_arg( 's', urlencode( $email ), admin_url( 'edit-comments.php' ) );
	}

	/**
	 * Returns the comment author link for the specified author along with the
	 * markup indicating the number of times the comment author has commented
	 * on the site.  The comment count links to a listing of all of that
	 * person's comments.
	 *
	 * Commenters are identified by the email address they provided when
	 * commenting.
	 *
	 * @param string $author_name Name of the comment author.
	 *
	 * @return string Comment author link plus linked comment count markup.
	 */
	public static function comment_author( $author_name ) {
		if ( ! is_admin() ) {
			return $author_name;
		}

		global $comment;
		$type = get_comment_type();

		if ( 'comment' == $type ) {
			$author_email = $comment->comment_author_email;
			$author_name  = $comment->comment_author;
			if ( ! $author_email ) {
				$field = 'comment_author';
				$value = $author_name;
			} else {
				$field = 'comment_author_email';
				$value = $author_email;
			}
			list( $comment_count, $pending_count ) = self::get_comments_count( $field, $value, $type );
			$msg = sprintf( _n( '%d comment', '%d comments', $comment_count, 'admin-commenters-comments-count' ), $comment_count );
		} elseif ( 'pingback' == $type || 'trackback' == $type ) {
			$author_url = $comment->comment_author_url;
			// Want to get the root domain and not use the exact pingback/trackback source link
			$parsed_url = parse_url( $author_url );
			$author_url = $parsed_url['scheme'] . '://' . $parsed_url['host'];
			list( $comment_count, $pending_count ) = self::get_comments_count( 'comment_author_url', $author_url, $type );
			$author_email = $author_url;
			/* Translators: sorry, but I'm not supplying explicit translation strings for all possible other comment types.
			   You can at least expect '%d trackback', '%d trackbacks', '%d pingback' and '%d pingbacks' */
			$msg = sprintf( _n( '%d %s', '%d %ss', $comment_count, 'admin-commenters-comments-count' ), $comment_count, $type );
		} else {
			return $author_name;
		}

		// If appearing on the dashboard, then don't need to break out of
		// pre-existing <strong> tags.
		$screen = get_current_screen();
		$is_dashboard = $screen && 'dashboard' == $screen->id;

		$html = $is_dashboard ? '' : '</strong>';

		$html .= self::get_comments_bubble( $author_email, $comment_count, $pending_count, $msg );

		$html .= $is_dashboard ? '' : '<strong>';
		$html .= $author_name;

		return $html;
	}

	/**
	 * Returns the markup for the comments bubble for the given email address with
	 * with the provided counts.
	 *
	 * @since 1.8
	 *
	 * @param string $author_email       Comment author email address.
	 * @param int    $comment_count      The number of comments for the email
	 *                                   address.
	 * @param int    $pending_count      The number of pending comments for the
	 *                                   email address.
	 * @param string $msg.               String to use as title attribute for
	 *                                   comment bubble.
	 * @param bool.  $no_comments_bubble Should the comment bubble be shown if the
	 *                                   email address has no comments?
	 *
	 * @return string
	 */
	public static function get_comments_bubble( $author_email, $comment_count, $pending_count, $msg = '', $no_comments_bubble = true ) {
		$html = '';

		$url = ( $comment_count + $pending_count ) > 0 ? self::get_comments_url( $author_email ) : '#';

		$comment_str = sprintf(
			_n( '%s comment', '%s comments', $comment_count, 'admin-commenters-comments-count' ),
			number_format_i18n( $comment_count )
		);

		$pending_class = $pending_count ? '' : ' author-com-count-no-pending';

		if ( ! $no_comments_bubble && ! $comment_count && ! $pending_count ) {
			return sprintf( '<span aria-hidden="true">—</span><span class="screen-reader-text">%s</span>',
				__( 'No comments', 'admin-commenters-comments-count' )
			);
		}

		$html .= '<span class="column-response"><span class="post-com-count-wrapper post-and-author-com-count-wrapper author-com-count' . $pending_class . "\">\n";

		$comments_number = number_format_i18n( $comment_count );

		if ( $comment_count ) {
			$html .= sprintf(
				'<a href="%s" title="%s" class="post-com-count post-com-count-approved">
					<span class="comment-count-approved" aria-hidden="true">%s</span>
					<span class="screen-reader-text">%s</span>
				</a>',
				esc_url( add_query_arg( 'comment_status', 'approved', $url ) ),
				esc_attr( $msg ),
				$comments_number,
				$comment_str
			);
		} else {
			$html .= sprintf(
				'<span class="post-com-count post-com-count-no-comments" title="%s"><span class="comment-count comment-count-no-comments" aria-hidden="true">%s</span><span class="screen-reader-text">%s</span></span>',
				esc_attr( $msg ),
				$comments_number,
				$pending_count ? __( 'No approved comments', 'admin-commenters-comments-count' ) : __( 'No comments', 'admin-commenters-comments-count' )
			);
		}

		$pending_comments_number = number_format_i18n( $pending_count );
		$pending_phrase = sprintf( _n( '%s pending comment', '%s pending comments', $pending_count, 'admin-commenters-comments-count' ), $pending_comments_number );
		if ( $pending_count ) {
			$html .= sprintf(
				'<a href="%s" class="post-com-count post-com-count-pending"><span class="comment-count-pending" aria-hidden="true">%s</span><span class="screen-reader-text">%s</span></a>',
				esc_url( add_query_arg( 'comment_status', 'moderated', $url ) ),
				$pending_comments_number,
				$pending_phrase
			);
		} else {
			$html .= sprintf(
				'<span class="post-com-count post-com-count-pending post-com-count-no-pending"><span class="comment-count comment-count-no-pending" aria-hidden="true">%s</span><span class="screen-reader-text">%s</span></span>',
				$pending_comments_number,
				$comment_count ? __( 'No pending comments', 'admin-commenters-comments-count' ) : __( 'No comments', 'admin-commenters-comments-count' )
			);
		}

		$html .= "</span></span>";

		return $html;
	}

	/**
	 * Filter for WP's get_comment_author_link() that returns the value of
	 * comment_author() when in the admin.
	 *
	 * @param string $author_link Author link
	 *
	 * @return string Modified author link
	 */
	public static function get_comment_author_link( $author_link ) {
		if ( ! is_admin() ) {
			return $author_link;
		}

		return self::comment_author( get_comment_author() );
	}
} // end c2c_AdminCommentersCommentsCount

add_action( 'plugins_loaded', array( 'c2c_AdminCommentersCommentsCount', 'init' ) );

endif; // end if !class_exists()
