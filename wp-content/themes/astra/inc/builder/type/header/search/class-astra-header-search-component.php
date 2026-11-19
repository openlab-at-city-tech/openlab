<?php
/**
 * Search for Astra theme.
 *
 * @package     astra-builder
 * @link        https://wpastra.com/
 * @since       3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'ASTRA_HEADER_SEARCH_DIR', ASTRA_THEME_DIR . 'inc/builder/type/header/search' );
define( 'ASTRA_HEADER_SEARCH_URI', ASTRA_THEME_URI . 'inc/builder/type/header/search' );

/**
 * Heading Initial Setup
 *
 * @since 3.0.0
 */
class Astra_Header_Search_Component {
	/**
	 * Constructor function that initializes required actions and hooks
	 */
	public function __construct() {

		// @codingStandardsIgnoreStart WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
		require_once ASTRA_HEADER_SEARCH_DIR . '/class-astra-header-search-component-loader.php';

		// Include front end files.
		if ( ! is_admin() || Astra_Builder_Customizer::astra_collect_customizer_builder_data() ) {
			require_once ASTRA_HEADER_SEARCH_DIR . '/dynamic-css/dynamic.css.php';
		}
		// @codingStandardsIgnoreEnd WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound

		add_filter( 'rest_post_query', array( $this, 'astra_update_rest_post_query' ), 10, 2 );
	}

	/**
	 * Post types that are enabled for live search and publicly viewable.
	 *
	 * Mirrors the allow-list the enqueue path builds for the live-search script, so
	 * the REST filter cannot be asked for a post type the site owner never enabled.
	 *
	 * @since 4.13.12
	 * @return array
	 */
	public static function get_live_search_post_types() {
		// Both surfaces store a map of post type => 0|1, so union the enabled keys instead of merging the arrays, where a 0 from one surface would drop a type the other has enabled.
		$options = array(
			astra_get_option( 'live-search-post-types', array() ),
			astra_get_option( 'ast-search-live-search-post-types', array() ),
		);

		$allowed = array();

		foreach ( $options as $enabled ) {
			foreach ( (array) $enabled as $post_type => $value ) {
				if ( ! $value || ! post_type_exists( $post_type ) ) {
					continue;
				}
				if ( ! is_post_type_viewable( $post_type ) ) {
					continue;
				}
				$allowed[] = $post_type;
			}
		}

		return array_unique( $allowed );
	}

	/**
	 * Update REST Post Query for live search.
	 *
	 * @since 4.4.0
	 * @param array $args Query args.
	 * @param array $request Request args.
	 * @return array
	 */
	public function astra_update_rest_post_query( $args, $request ) {
		if (
			isset( $request['post_type'] )
			&&
			is_string( $request['post_type'] )
			&&
			( strpos( $request['post_type'], 'ast_queried' ) !== false )
		) {
			$search_post_types = explode( ':', sanitize_text_field( $request['post_type'] ) );

			// Restrict to the post types actually enabled for live search, else any show_in_rest type could be pulled through /wp/v2/posts.
			$search_post_types = array_values( array_intersect( $search_post_types, self::get_live_search_post_types() ) );

			if ( empty( $search_post_types ) ) {
				return $args;
			}

			$live_search_args = array(
				'posts_per_page' => ! empty( $args['posts_per_page'] ) ? $args['posts_per_page'] : 10,
				'post_type'      => $search_post_types,
				'paged'          => 1,
				's'              => ! empty( $args['s'] ) ? $args['s'] : '',
			);

			// Merge over the core-built args instead of replacing them, so query vars set by other rest_post_query callbacks are not discarded.
			$args = array_merge( $args, $live_search_args );

			// The REST controller always sends orderby=date, which would override the relevance ordering WP_Query applies to search queries.
			unset( $args['orderby'], $args['order'] );

			if ( in_array( 'product', $search_post_types ) ) {
				// Added product visibility checks, excluding hidden or shop-only visibility types.
				$args['tax_query'][] = array(
					'taxonomy' => 'product_visibility',
					'field'    => 'slug',
					'terms'    => array( 'exclude-from-search' ),
					'operator' => 'NOT IN',
				);
			}

			/**
			 * Filters the query args used for the live search results.
			 *
			 * Live search runs through the REST API, where plugins that hook into the
			 * main search query cannot reach it. This gives them a place to do so.
			 *
			 * @since 4.13.11
			 * @param array           $args    Query args.
			 * @param WP_REST_Request $request Request object.
			 *
			 * @psalm-suppress TooManyArguments
			 */
			$args = apply_filters( 'astra_live_search_query_args', $args, $request );
		}

		return $args;
	}
}

/**
 *  Kicking this off by creating an object.
 */
new Astra_Header_Search_Component();
