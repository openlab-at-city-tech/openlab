<?php  // phpcs:ignore
/**
 * Validation functions.
 *
 * @package         Editoria11y
 */

/**
 * Validate API results.
 */
class Editoria11y_Validate {

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
			'result_key',
			'result_count',
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
