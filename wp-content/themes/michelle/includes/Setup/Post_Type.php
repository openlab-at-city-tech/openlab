<?php
/**
 * Post type features class.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since  1.0.0
 */

namespace WebManDesign\Michelle\Setup;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class Post_Type {

	/**
	 * Soft cached post types supporting specific features.
	 *
	 * @since   1.0.0
	 * @access  private
	 * @var     array
	 */
	private static $feature_post_types = array();

	/**
	 * Returns array of post types supporting specific feature.
	 *
	 * @since  1.0.0
	 *
	 * @param  string $feature
	 * @param  array  $post_types  Preset post types supporting the feature.
	 *
	 * @return  array
	 */
	public static function get_feature( string $feature, array $post_types = array() ): array {

		// Variables

			// Soft cache feature post types array so we don't call `get_post_types()` multiple times.
			if ( empty( self::$feature_post_types ) ) {

				$public = get_post_types( array(
					'public' => true,
				) );

				$not_public = get_post_types( array(
					'public'   => false,
					'_builtin' => false,
				) );

				self::$feature_post_types = array(
					'public'     => array_filter( $public ),
					'not_public' => array_filter( $not_public + array( 'attachment' ) ),

					'continue_reading' => array( 'post', 'page' ),
					'entry_meta'       => array( 'post' ),
					'post_navigation'  => array( 'post', 'attachment' ),
				);

			}


		// Processing

			if (
				empty( $post_types )
				&& isset( self::$feature_post_types[ $feature ] )
			) {
				$post_types = self::$feature_post_types[ $feature ];
			}


		// Output

			/**
			 * Filters the array of post types supporting specific feature.
			 *
			 * @since  1.0.0
			 *
			 * @param  array  $post_types
			 * @param  string $feature     Post type feature, such as 'post_navigation', 'continue_reading', 'entry_meta'.
			 */
			return (array) apply_filters( 'michelle/setup/post_type/get_feature', $post_types, $feature );

	} // /get_feature

}
