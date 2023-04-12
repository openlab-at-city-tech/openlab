<?php

namespace WeBWorK\Server;

/**
 * Data schema.
 *
 * CPTs, etc.
 *
 * @since 1.0.0
 */
class Schema {
	/**
	 * By the time this is fired, 'init' should be done.
	 */
	public function init() {
		$this->register_post_types();
		$this->register_taxonomies();
	}

	/**
	 * Register post type.
	 *
	 * The webwork_class object is a WP representation of a WW class object. The two are always in one-to-one
	 * correspondence. A webwork_class object is used as a bridge to a "primary" WP object, such as a site or a
	 * BuddyPress group.
	 *
	 * There are three critical pieces of postmeta for each WeBWorK class:
	 * - 'webwork_object_type' can be 'site', 'bp_group', etc.
	 * - 'webwork_object_id' is the numeric ID of the associated WP object. With 'object_type', is a unique identifier.
	 * - 'webwork_remote_class_url' is the URL of the course in WeBWorK.
	 *
	 * @since 1.0.0
	 */
	protected function register_post_types() {
		register_post_type(
			'webwork_question',
			array(
				'label'        => __( 'WebWorK Question', 'webworkqa' ),
				'labels'       => array(
					'name'               => __( 'WeBWorK Question', 'webworkqa' ),
					'singular_name'      => __( 'WeBWorK Question', 'webworkqa' ),
					'add_new_item'       => __( 'Add New WeBWorK Question', 'webworkqa' ),
					'edit_item'          => __( 'Edit WeBWorK Question', 'webworkqa' ),
					'new_item'           => __( 'New WeBWorK Question', 'webworkqa' ),
					'view_item'          => __( 'View WeBWorK Question', 'webworkqa' ),
					'search_items'       => __( 'Search WeBWorK Questions', 'webworkqa' ),
					'not_found'          => __( 'No WeBWorK Questions found', 'webworkqa' ),
					'not_found_in_trash' => __( 'No WeBWorK Questions found in Trash.', 'webworkqa' ),
				),
				'public'       => true, // todo This should be false
				'show_in_rest' => true,
				'rest_base'    => 'questions',
			)
		);

		register_post_type(
			'webwork_response',
			array(
				'label'        => __( 'WebWorK Response', 'webworkqa' ),
				'labels'       => array(
					'name'               => __( 'WeBWorK Responses', 'webworkqa' ),
					'singular_name'      => __( 'WeBWorK Response', 'webworkqa' ),
					'add_new_item'       => __( 'Add New WeBWorK Response', 'webworkqa' ),
					'edit_item'          => __( 'Edit WeBWorK Response', 'webworkqa' ),
					'new_item'           => __( 'New WeBWorK Response', 'webworkqa' ),
					'view_item'          => __( 'View WeBWorK Response', 'webworkqa' ),
					'search_items'       => __( 'Search WeBWorK Responses', 'webworkqa' ),
					'not_found'          => __( 'No WeBWorK Responses found', 'webworkqa' ),
					'not_found_in_trash' => __( 'No WeBWorK Responses found in Trash.', 'webworkqa' ),
				),
				'public'       => true, // todo This should be false
				'show_in_rest' => true,
				'rest_base'    => 'responses',
			)
		);
	}

	/**
	 * Register taxonomies.
	 *
	 * - webwork_problem_id (webwork_question)
	 * - webwork_problem_set (webwork_question)
	 * - webwork_course (webwork_question)
	 *
	 * @since 1.0.0
	 */
	public function register_taxonomies() {
		register_taxonomy(
			'webwork_problem_id',
			'webwork_question',
			array(
				'public' => false,
			)
		);

		register_taxonomy(
			'webwork_problem_set',
			'webwork_question',
			array(
				'public' => true,
			)
		);

		register_taxonomy(
			'webwork_course',
			'webwork_question',
			array(
				'public' => false,
			)
		);

		register_taxonomy(
			'webwork_subscribed_by',
			'webwork_question',
			array(
				'public' => false,
			)
		);
	}

	public function get_votes_schema() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();
		$table_prefix    = $wpdb->get_blog_prefix();

		$sql = "CREATE TABLE {$table_prefix}webwork_votes (
			id bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			user_id bigint(20) NOT NULL,
			item_id bigint(20) NOT NULL,
			value bigint(20),
			KEY item_id (item_id),
			KEY user_id (user_id),
			KEY value (value)
		) {$charset_collate};";

		return $sql;
	}
}
