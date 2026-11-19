<?php
/**
 * Validation functions.
 *
 * @package         Editoria11y
 */

namespace Editoria11y;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Validate API results.
 */
class Validate {

	/**
	 * Validate entity types.
	 *
	 * @param string $user_input Content type name.
	 */
	public static function entity_type( $user_input ) {
		$valid = array(
			'Front',
			'Page',
			'Home',
			'Attachment',
			'Post',
			'Category',
			'Tag',
			'Taxonomy',
			'Author',
			'Archive',
			'Search',
			'404',
		);
		return in_array( $user_input, $valid, true );
	}

	/**
	 * Clamp a pagination count param to a usable LIMIT.
	 *
	 * Negative values would render as `LIMIT -25` — a MySQL syntax error
	 * that nulls the whole payload — and huge values would let a single
	 * request buffer an unbounded slice of the table. Callers supply their
	 * own default (typically 25) before clamping.
	 *
	 * @param mixed $user_input Raw request param.
	 */
	public static function count( $user_input ): int {
		return min( 500, max( 1, intval( $user_input ) ) );
	}

	/**
	 * Clamp a pagination offset param (`OFFSET -5` is a syntax error).
	 *
	 * @param mixed $user_input Raw request param.
	 */
	public static function offset( $user_input ): int {
		return max( 0, intval( $user_input ) );
	}

	/**
	 * Validate filters and sorts.
	 *
	 * @param string $user_input Allowed field names.
	 */
	public static function sort( $user_input ) {
		$valid = array(
			'pid',
			'page_url',
			'page_title',
			'entity_type',
			'page_total',
			'dev_total',
			'result_key',
			'result_count',
			'dev_count',
			'created',
			'display_name',
			'dismissal_status',
			'post_modified',
			'post_status',
			'post_author',
		);
		return in_array( $user_input, $valid, true );
	}
}
