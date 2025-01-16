<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Query categories data in the database
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 */
class EPKB_Categories_DB {

	/**
	 * Get all top-level categories
	 *
	 * @param $kb_id
	 * @param bool $hide_empty
	 * @return array or empty array on error
	 */
	public static function get_top_level_categories( $kb_id, $hide_empty=false ) {

		if ( ! EPKB_Utilities::is_positive_int( $kb_id ) ) {
			EPKB_Logging::add_log( 'Invalid kb id', $kb_id );
			return array();
		}

		$args = array(
			'parent'     => '0',
			'hide_empty' => $hide_empty, // whether to return categories without articles
			'taxonomy'   => EPKB_KB_Handler::get_category_taxonomy_name( $kb_id )
		);

		$terms = get_terms( $args );
		if ( is_wp_error( $terms ) ) {
			EPKB_Logging::add_log( 'cannot get terms for kb_id', $kb_id, $terms );
			return array();
		} else if ( empty( $terms ) || ! is_array( $terms ) ) {
			return array();
		}

		return array_values( $terms );   // rearrange array keys
	}

	public static function get_sub_categories( $category_seq_data, $category_id, $levels=1 ) {
		foreach ( $category_seq_data as $sub_category_id => $sub_category_seq_data ) {
			if ( $sub_category_id == $category_id ) {
				return $sub_category_seq_data;
			} else if ( ! empty( $sub_category_seq_data ) ) {
				if ( $levels < 6 ) {
					$sub_category_seq_data = self::get_sub_categories( $sub_category_seq_data, $category_id, $levels + 1 );
					if ( is_array( $sub_category_seq_data ) ) {
						return $sub_category_seq_data;
					}
				}
			}
		}

		return false;
	}

	/**
	 * Get all categories that belong to given parent
	 *
	 * @param $kb_id
	 * @param int $parent_id is parent category we use to find children
	 * @param bool $hide_empty
	 * @return array or empty array on error
	 */
	public static function get_child_categories( $kb_id, $parent_id, $hide_empty=false ) {

		if ( ! EPKB_Utilities::is_positive_int( $kb_id ) ) {
			EPKB_Logging::add_log( 'Invalid kb id', $kb_id );
			return array();
		}

		if ( ! EPKB_Utilities::is_positive_int( $parent_id ) ) {
			EPKB_Logging::add_log( 'Invalid parent id', $parent_id );
			return array();
		}

		$args = array(
			'child_of'      => $parent_id,
			'parent'        => $parent_id,
			'hide_empty'    => $hide_empty,
			'taxonomy'   => EPKB_KB_Handler::get_category_taxonomy_name( $kb_id )
		);

		$terms = get_terms( $args );
		if ( is_wp_error( $terms ) ) {
			EPKB_Logging::add_log( 'failed to get terms for kb_id: ' . $kb_id . ', parent_id: ' . $parent_id, $terms );
			return array();
		}

		if ( empty( $terms ) || ! is_array( $terms ) ) {
			return array();
		}

		return array_values( $terms );
	}

	/**
	 * Count articles in category and sub-category
	 *
	 * @param $kb_id
	 * @param $category_id
	 * @return int
	 */
	public static function get_category_count( $kb_id, $category_id ) {

		$article_db = new EPKB_Articles_DB();

		$articles = $article_db->get_articles_by_sub_or_category( $kb_id, $category_id, 'date', -1, true, false );

		return count( $articles );
	}
}