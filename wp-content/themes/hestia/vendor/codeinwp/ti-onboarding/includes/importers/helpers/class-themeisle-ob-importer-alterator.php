<?php
/**
 * Importer alterator.
 *
 * Used to alter import content.
 *
 * Author:  Andrei Baicus <andrei@themeisle.com>
 * On:      21/06/2018
 *
 * @package    themeisle-onboarding
 */

/**
 * Class Themeisle_OB_Importer_Alterator
 */
class Themeisle_OB_Importer_Alterator {

	/**
	 * Post map. Holds post type / count.
	 *
	 * @var array
	 */
	private $post_map = array();

	/**
	 * Post types that will be ignored if there are more than 2 already on the site.
	 *
	 * @var array
	 */
	private $filtered_post_types = array(
		'post',
		'product',
	);

	/**
	 * Themeisle_OB_Importer_Alterator constructor.
	 */
	public function __construct() {
		$this->count_posts_by_post_type();
		add_filter( 'wxr_importer.pre_process.post', array( $this, 'skip_posts' ), 10, 4 );
		add_filter( 'wxr_importer.pre_process.term', array( $this, 'skip_terms' ), 10, 2 );
		add_filter( 'wp_insert_post_data', array( $this, 'encode_post_content' ), 10, 2 );
	}

	/**
	 * Encode post content to UTF8 for possible issues with locales.
	 *
	 * @param array $data    post data
	 * @param array $postarr post array
	 *
	 * @return array
	 */
	public function encode_post_content( $data, $postarr ) {
		$data['post_content'] = utf8_encode( $data['post_content'] );

		return $data;
	}

	/**
	 * Skip posts if there are more than 2 already.
	 *
	 * @param array $data     post data.
	 * @param array $meta     meta.
	 * @param array $comments comments.
	 * @param array $terms    terms.
	 *
	 * @return array
	 */
	public function skip_posts( $data, $meta, $comments, $terms ) {
		if ( ! array_key_exists( $data['post_type'], $this->post_map ) ) {
			return $data;
		}
		if ( $this->post_map[ $data['post_type'] ] <= 2 ) {
			return $data;
		}

		return array();
	}

	/**
	 * Skips terms for post types that were skipped.
	 *
	 * @param array $data term data.
	 * @param array $meta meta.s
	 *
	 * @return array
	 */
	public function skip_terms( $data, $meta ) {
		foreach ( $this->filtered_post_types as $post_type ) {
			if ( ! $this->post_map[ $post_type ] <= 2 ) {
				continue;
			}
			if ( $this->is_taxonomy_assigned_to_post_type( $post_type, $data['taxonomy'] ) ) {
				return array();
			}
		}

		return $data;
	}

	/**
	 * Checks if taxonomy is assigned to post type.
	 *
	 * @param string $post_type post type slug.
	 * @param string $taxonomy  taxonomy slug.
	 *
	 * @return bool
	 */
	private function is_taxonomy_assigned_to_post_type( $post_type, $taxonomy ) {
		$taxonomies = get_object_taxonomies( $post_type );

		return in_array( $taxonomy, $taxonomies );
	}

	/**
	 * Count excluded post types.
	 */
	private function count_posts_by_post_type() {
		foreach ( $this->filtered_post_types as $post_type ) {
			$args      = array(
				'post_type' => $post_type,
			);
			$the_query = new WP_Query( $args );

			$this->post_map[ $post_type ] = absint( $the_query->found_posts );
		}
	}
}
