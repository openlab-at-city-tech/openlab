<?php
/**
 * API
 *
 * Registers any functionality to use/extend
 * the WordPress REST API.
 *
 * @package Easy_Custom_Sidebars
 * @author  Sunny Johal - Titanium Themes <support@titaniumthemes.com>
 */

namespace ECS\Api;

use ECS\Data;

/**
 * DELETE: Bulk Sidebar Delete Endpoint
 */
add_action(
	'rest_api_init',
	function() {
		register_rest_route(
			'easy-custom-sidebars/v1',
			'sidebar_instances',
			[
				'methods'             => 'DELETE',
				'callback'            => function () {
					return Data\delete_all_sidebars();
				},
				'permission_callback' => function() {
					return current_user_can( 'edit_theme_options' );
				},
			]
		);
	}
);

/**
 * GET: Registered Sidebars Endpoint
 */
add_action(
	'rest_api_init',
	function() {
		register_rest_route(
			'easy-custom-sidebars/v1',
			'/default-sidebars',
			[
				'methods'             => 'GET',
				'callback'            => function () {
					return Data\get_default_registered_sidebars();
				},
				'permission_callback' => function() {
					return current_user_can( 'edit_theme_options' );
				},
			]
		);
	}
);

/**
 * Add Attachment Details
 */
add_action(
	'rest_api_init',
	function() {
		register_rest_field(
			'sidebar_instance',
			'ecs_attachments',
			[
				'get_callback' => function( $post ) {
					return Data\get_sidebar_attachments( $post['id'] );
				},
			]
		);
	}
);

add_action(
	'rest_api_init',
	function() {
		register_rest_route(
			'easy-custom-sidebars/v1',
			'/attachments/(?P<id>\d+)',
			[
				'methods'             => 'GET',
				'callback'            => function ( $request ) {
					return Data\get_sidebar_attachments( $request->get_param( 'id' ), true );
				},
				'args'                => [
					'id' => [
						'validate_callback' => function ( $param, $request, $key ) {
							return is_numeric( $param );
						},
					],
				],
				'permission_callback' => function() {
					return current_user_can( 'edit_theme_options' );
				},
			]
		);
	}
);

/**
 * Page Templates Endpoint
 */
add_action(
	'rest_api_init',
	function() {
		register_rest_route(
			'easy-custom-sidebars/v1',
			'/page-templates',
			[
				'methods'             => 'GET',
				'callback'            => function () {
					return Data\get_page_templates();
				},
				'permission_callback' => function() {
					return current_user_can( 'edit_theme_options' );
				},
			]
		);
	}
);

/**
 * POST: Dismiss Admin WP Pointer
 */
add_action(
	'rest_api_init',
	function() {
		register_rest_route(
			'easy-custom-sidebars/v1',
			'/hide-pointer',
			[
				'methods'             => 'POST',
				'callback'            => function () {
					return update_option( 'ecs_show_admin_pointer', false );
				},
				'permission_callback' => function() {
					return current_user_can( 'edit_theme_options' );
				},
			]
		);
	}
);

/**
 * Add Taxonomies to the REST API.
 */
add_filter(
	'register_taxonomy_args',
	function ( $args, $taxonomy_name ) {
		$enabled_taxonomies = [
			'mp-event_category',
			'mp-event_tag',
			'product_cat',
			'product_tag',
		];

		if (
			in_array( $taxonomy_name, $enabled_taxonomies, true ) ||
			! empty( $args['public'] ) && 'post_format' !== $taxonomy_name
		) {
			$args['show_in_rest'] = true;
		}

		return $args;
	},
	10,
	2
);
