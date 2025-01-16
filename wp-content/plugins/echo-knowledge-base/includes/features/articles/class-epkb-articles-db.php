<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Query article data in the database
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 */
class EPKB_Articles_DB {

	/**
	 * Get PUBLISHED articles related to a given category OR sub-category
	 *
	 * @param $kb_id
	 * @param $sub_or_category_id
	 * @param string $order_by
	 * @param int $nof_articles
	 * @param bool $include_children
	 * @param bool $all_articles
	 *
	 * @return array of matching articles or empty array
	 */
	function get_articles_by_sub_or_category( $kb_id, $sub_or_category_id, $order_by='date', $nof_articles=500, $include_children=false, $all_articles=true ) {
		/** @var $wpdb Wpdb */
		global $wpdb;
		
		if ( ! EPKB_Utilities::is_positive_int( $kb_id ) ) {
			EPKB_Logging::add_log( 'Invalid kb id', $kb_id );
			return array();
		}

		if ( ! EPKB_Utilities::is_positive_int($sub_or_category_id) ) {
			EPKB_Logging::add_log( 'Invalid category id', $sub_or_category_id );
			return array();
		}

		$order = $order_by == 'title' ? 'ASC' : 'DESC';

		$query_args = array(
			'post_type' => EPKB_KB_Handler::get_post_type( $kb_id ),
			'posts_per_page' => $nof_articles,
			'orderby' => $order_by,
			'order'=> $order,
			'tax_query' => array(
				array(
					'taxonomy' => EPKB_KB_Handler::get_category_taxonomy_name( $kb_id ),
					'terms' => $sub_or_category_id,
					'include_children' => $include_children
				)
			)
		);

		// OLD installation or Access Manager
		$query_args['post_status'] = array( 'publish' );
		$where_post_status_escaped = " p.post_status = 'publish' ";
		if ( EPKB_Utilities::is_amag_on() ) {
			$query_args['post_status'] = array( 'publish', 'private' );
			$where_post_status_escaped .= " OR p.post_status = 'private' ";

		// NEW installation:
		// WordPress uses 'publish' and corresponding 'private' posts by default in get_posts()
		// - in SQL for logged-in users select only those articles which they are allowed to see
		// - in SQL for NOT logged-in users select only 'publish' articles
		} else if ( is_user_logged_in() ) {

			// use 'publish' and 'private' articles (filter articles by corresponding access where display them)
			if ( $all_articles ) {
				$query_args['post_status'] = array( 'publish', 'private' );
				$where_post_status_escaped .= " OR p.post_status = 'private' ";
			} else {
				$where_post_status_escaped .= current_user_can( 'read_private_posts' )
				? " OR p.post_status = 'private' "
				: " OR ( p.post_status = 'private' AND p.post_author = " . esc_sql( get_current_user_id() ) . " ) ";
			}
		}

		$where_post_status_escaped = ' (' . $where_post_status_escaped . ') ';

		// Get only Published articles
		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config( $kb_id );
		if ( ! is_wp_error( $kb_config ) && EPKB_Utilities::is_wpml_enabled( $kb_config ) ) {
			$order_by = $order_by == 'title' ? 'post_title' : 'post_date';
			return $wpdb->get_results( $wpdb->prepare( " SELECT * " .
			                                " FROM $wpdb->posts p " .
			                                " WHERE p.ID in " .
			                                "   (SELECT object_id FROM $wpdb->term_relationships tr INNER JOIN $wpdb->term_taxonomy tt ON tt.term_taxonomy_id = tr.term_taxonomy_id " .
			                                "    WHERE tt.term_id = %d AND tt.taxonomy = %s) " .
			                                "   AND p.post_type = %s AND " . $where_post_status_escaped .
		                                    " ORDER BY " . esc_sql( $order_by ) . " " . esc_sql( $order ),
										$sub_or_category_id, EPKB_KB_Handler::get_category_taxonomy_name( $kb_id ), EPKB_KB_Handler::get_post_type( $kb_id ) ) );
		}

		return get_posts( $query_args );
	}

	/**
	 * Retrieve all KB articles but do not count articles in Trash
	 *
	 * @param $kb_id
	 * @return number of all posts
	 */
	static function get_count_of_all_kb_articles( $kb_id ) {
		/** @var $wpdb Wpdb */
		global $wpdb;

		$kb_id = EPKB_Utilities::sanitize_int( $kb_id, EPKB_KB_Config_DB::DEFAULT_KB_ID );

		// NEW users: in SQL select only those articles which the current user is allowed to see
		if ( EPKB_Utilities::is_amag_on() ) {
			$where_post_status_escaped = "post_status IN ('publish', 'private')";

		} else {
			$where_post_status_escaped = current_user_can( 'read_private_posts' )
					? "( post_status = 'publish' OR post_status = 'private' ) "
					: "( post_status = 'publish' OR ( post_status = 'private' AND post_author = " . esc_sql( get_current_user_id() ) . " ) )";

		}

		// parameters sanitized
		$posts = $wpdb->get_results( $wpdb->prepare( " SELECT * " .
									 " FROM $wpdb->posts " .
		                             " WHERE post_type = %s AND " . $where_post_status_escaped, EPKB_KB_Handler::get_post_type( $kb_id ) ) );
		if ( empty( $posts ) || ! is_array( $posts ) ) {
			return 0;
		}

		return count( $posts );
	}

	/**
	 * Retrieve all PUBLISHED articles that do not have either category or subcategory
	 *
	 * @param $kb_id
	 *
	 * @return array of posts
	 */
	function get_orphan_published_articles( $kb_id ) {
		/** @var $wpdb Wpdb */
		global $wpdb;

		// sanitize KB ID
		if ( ! EPKB_Utilities::is_positive_int( $kb_id ) ) {
			EPKB_Logging::add_log( 'Invalid kb id', $kb_id );
			return array();
		}

		// parameters sanitized
		$posts = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " .
		                             "   $wpdb->posts p LEFT JOIN " .
	                                 "   (SELECT object_id FROM $wpdb->term_relationships tr INNER JOIN $wpdb->term_taxonomy tt ON tt.term_taxonomy_id = tr.term_taxonomy_id " .
		                                        " WHERE tt.taxonomy = %s) AS ta " .
		                             " ON ta.object_id = p.ID " .
		                             " WHERE post_type = %s AND object_id IS NULL AND post_status in ('publish') ",
										EPKB_KB_Handler::get_category_taxonomy_name( $kb_id ), EPKB_KB_Handler::get_post_type( $kb_id ) ) );
		if ( empty( $posts ) || ! is_array( $posts ) ) {
			return array();
		}

		return $posts;
	}
}