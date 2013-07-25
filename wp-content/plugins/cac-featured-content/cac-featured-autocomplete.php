<?php

/**
 * Autocomplete functionality
 *
 * Functions for handling server-side part of groups autocomplete AJAX.
 * Organized into classes to avoid globals and namespace issues
 *
 * @author Dominic Giglio
 * @author Boone Gorges
 */

// require our helper class so we can use its static methods
require_once 'cac-featured-helper.php';

/**
 * Groups autocomplete class
 */
class CAC_Groups_Autocomplete {
	function __construct() {
		add_action( 'wp_ajax_cfcw_query_group', array( &$this, 'handler' ) );
	}

	// handler for the groups autocomplete ajax request
	function handler( $value = '' ) {
		if ( empty( $value ) )
			$q = isset( $_REQUEST['term'] ) ? urldecode( $_REQUEST['term'] ) : false;
		else
			$q = $value;

		$retval = array();

		if ( $q ) {
			$this->search_terms = $q;

			// Lame but necessary. 'search_terms' is too generous
			add_filter( 'bp_groups_get_paged_groups_sql', array( &$this, 'filter_group_sql' ) );
			if ( bp_has_groups() ) {
				while ( bp_groups() ) {
					bp_the_group();
					$retval[] = array(
						'label' => bp_get_group_name(),
						'value' => bp_get_group_slug()
					);
				}
			}
		}

		die( json_encode( $retval ) );
	}

	// filters the bp_has_groups query to do a proper LIKE
	function filter_group_sql( $sql ) {
		global $bp;

		$sqla = explode( 'WHERE', $sql );

		$new_sql = $sqla[0] . ' WHERE g.name LIKE "%' . $this->search_terms . '%" AND ' . $sqla[1];

		return $new_sql;
	}
} // end CAC_Groups_Autocomplete

/**
 * Members autocomplete class
 */
class CAC_Members_Autocomplete {
	function __construct() {
		add_action( 'wp_ajax_cfcw_query_member', array( &$this, 'handler' ) );
	}

	// handler for the members autocomplete ajax request
	function handler( $value = '' ) {

		if ( empty( $value ) )
			$q = isset( $_REQUEST['term'] ) ? urldecode( $_REQUEST['term'] ) : false;
		else
			$q = $value;

		if ( $q ) {
			$users = get_users('search=*' . $q . '*');
			$retval = array();
			foreach ( $users as $user ) {
				$retval[] = array(
					'label' => $user->display_name,
					'value' => $user->user_login
				);
			}
		}

		die( json_encode( $retval ) );
	}
} // end CAC_Members_Autocomplete

/**
 * Blogs autocomplete class
 */
class CAC_Blogs_Autocomplete {
	function __construct() {
		add_action( 'wp_ajax_cfcw_query_blog', array( &$this, 'handler' ) );
	}

	// handler for the blogs autocomplete ajax request
	function handler( $value = '' ) {

		global $wpdb;
		$retval = array();

		if ( empty( $value ) )
			$q = isset( $_REQUEST['term'] ) ? urldecode( $_REQUEST['term'] ) : false;
		else
			$q = $value;

		if ( $q ) {
			$blogs = $wpdb->get_results("
        SELECT domain
        FROM {$wpdb->blogs}
        WHERE domain like '%{$q}%'
        AND spam = '0'
        AND deleted = '0'
        AND archived = '0'
        AND blog_id != 1");

			foreach ( $blogs as $blog ) {
				$retval[] = array(
					'label' => $blog->domain,
					'value' => $blog->domain
				);
			}
		}

		die( json_encode( $retval ) );
	}
} // end CAC_Blogs_Autocomplete

/**
 * Posts autocomplete class
 */
class CAC_Posts_Autocomplete {
	function __construct() {
		add_action( 'wp_ajax_cfcw_query_post', array( &$this, 'handler' ) );
	}

	// handler for the posts autocomplete ajax request
	function handler( $value = '' ) {

		$cfcw_fields = get_option( 'widget_cac_featured_content_widget' );
		$num = urldecode( $_REQUEST['num'] );
		$retval = array();

		if ( empty( $value ) )
			$q = isset( $_REQUEST['term'] ) ? urldecode( $_REQUEST['term'] ) : false;
		else
			$q = $value;

		if ( $q ) {

			// if MS has been enabled and the user has entered a featured blog list its posts
			if ( is_multisite() && $cfcw_fields[$num]['featured_blog'] != "" ) {
				$blog = CAC_Featured_Content_Helper::get_blog_by_domain( $cfcw_fields[$num]['featured_blog'] );

				// only return results if we have a valid blog
				if ($blog) {
					
					switch_to_blog( $blog->blog_id );

					$query = new WP_Query("s={$q}&post_type=post&post_status=publish");

					foreach ($query->posts as $post) {
						$retval[] = array(
							'label' => $post->post_title,
							'value' => $post->post_name
						);
					}

					restore_current_blog();

				} else {
					// remind the user to enter a valid blog name first
					$retval[] = array(
						'label' => 'Please enter a valid blog name.',
						'value' => ''
					);
				}

			} else {
				// we must not be running MS so we'll just list the site's blog posts
				$query = new WP_Query("s={$q}&post_type=post&post_status=publish");
				
				foreach ($query->posts as $post) {
					$retval[] = array(
						'label' => $post->post_title,
						'value' => $post->post_name
					);
				}
			}
			
		}

		die( json_encode( $retval ) );
	}
} // end CAC_Posts_Autocomplete

// instantiate all the autocomplete classes
new CAC_Groups_Autocomplete;
new CAC_Members_Autocomplete;
new CAC_Blogs_Autocomplete;
new CAC_Posts_Autocomplete;

?>