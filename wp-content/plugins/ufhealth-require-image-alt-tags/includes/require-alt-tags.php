<?php
/**
 * Add plugin functionality for requiring alt tags.
 *
 * @since   1.0
 *
 * @package UFHealth\require_image_alt_tags
 */

namespace UFHealth\require_image_alt_tags;

add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\action_admin_enqueue_scripts' );

/**
 * Enqueue necessary admin scripts.
 *
 * @since 1.0
 */
function action_admin_enqueue_scripts() {

	$site_id = is_multisite() ? get_current_blog_id() : 0;

	/**
	 * Filter the screen IDs in where the script should be displayed
	 *
	 * This filter allows us to limit or expand to multiple content types or other screens based on the the current site.
	 *
	 * @since 1.0
	 *
	 * @param array $screens Array of screen IDs (post, page, etc).
	 * @param int   $site_id The current site ID.
	 */
	$screens = apply_filters( 'ufh_replace_alt_tags_screen_ids', array( 'post', 'page' ), $site_id );

	if ( in_array( get_current_screen()->id, $screens, true ) ) {

		$min = '.min';
		$src = '';

		if ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) {

			$min = '';
			$src = 'src/';

		}

		wp_register_script( 'ufhealth_require_alt_tags', UFHEALTH_REQUIRE_IMAGE_ALT_TAGS_URL . 'assets/js/' . $src . 'ufhealth-require-image-alt-tags' . $min . '.js', array( 'jquery' ), UFHEALTH_REQUIRE_IMAGE_ALT_TAGS_VERSION, true );
		wp_register_style( 'ufhealth_require_alt_tags', UFHEALTH_REQUIRE_IMAGE_ALT_TAGS_URL . 'assets/css/ufhealth-require-image-alt-tags' . $min . '.css', array(), UFHEALTH_REQUIRE_IMAGE_ALT_TAGS_VERSION );

		wp_enqueue_script( 'ufhealth_require_alt_tags' );
		wp_enqueue_style( 'ufhealth_require_alt_tags' );

		/**
		 * Filter the disclaimer copy shown when attempting to insert an image without ALT text.
		 *
		 * @since 1.1.3
		 *
		 * @param string $disclaimer_copy The copy shown in the warning box.
		 */
		$disclaimer_copy = apply_filters( 'ufhealth_alt_tag_disclaimer', esc_html__( 'Please include an ‘Alt Text’ before proceeding with inserting your image.', 'ufhealth-require-image-alt-tags' ) );

		wp_localize_script(
			'ufhealth_require_alt_tags',
			'ufhTagsCopy',
			array(
				'txt'        => esc_html__( 'The following image(s) are missing alt text', 'ufhealth-require-image-alt-tags' ),
				'editTxt'    => esc_html__( 'You must enter alt text for the image', 'ufhealth-require-image-alt-tags' ),
				'disclaimer' => $disclaimer_copy,
			)
		);

	}
}

add_filter( 'manage_media_columns', __NAMESPACE__ . '\filter_manage_media_columns' );

/**
 * Filter manage_media_columns
 *
 * Adds a column to the media table to show images missing ALT text.
 *
 * @since 1.1
 *
 * @param array $columns Array of media table columns.
 *
 * @return array Filtered array of media table columns
 */
function filter_manage_media_columns( $columns ) {

	$columns['alttext'] = esc_html__( 'Alt Text', 'ufhealth-require-image-alt-tags' );

	return $columns;

}

add_action( 'manage_media_custom_column', __NAMESPACE__ . '\action_manage_media_custom_column', 10, 3 );

/**
 * Filter manage_users_custom_column
 *
 * Filters the display output of custom columns in the Users list table.
 *
 * @since 1.0
 *
 * @param string $column_name Name of the custom column.
 * @param int    $post_id     Attachment ID.
 */
function action_manage_media_custom_column( $column_name, $post_id ) {

	if ( 'alttext' === $column_name && wp_attachment_is_image( $post_id ) ) {

		$alt_text = get_post_meta( $post_id, '_wp_attachment_image_alt', true );

		if ( empty( $alt_text ) ) {
			printf( '<span style="color: red;">%s</span>', esc_html__( 'Missing', 'ufhealth-require-image-alt-tags' ) );
		}
	}
}
