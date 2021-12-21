<?php
/**
 * API
 *
 * Registers any functionality to use/extend
 * the WordPress REST API.
 *
 * @package easy-google-fonts
 * @author  Sunny Johal - Titanium Themes <support@titaniumthemes.com>
 */

namespace EGF\Api;

use EGF\Data;

/**
 * DELETE: Bulk Font Controls Delete Endpoint
 */
add_action(
	'rest_api_init',
	function() {
		register_rest_route(
			'easy-google-fonts/v1',
			'font_controls',
			[
				'methods'             => 'DELETE',
				'callback'            => function () {
					return Data\delete_all_font_controls();
				},
				'permission_callback' => function() {
					return current_user_can( 'edit_theme_options' );
				},
			]
		);
	}
);

/**
 * Option API Endpoints
 */
add_action(
	'rest_api_init',
	function() {
		register_rest_route(
			'easy-google-fonts/v1',
			'api_key',
			[
				[
					'methods'             => 'GET',
					'callback'            => function () {
						return get_option( 'tt-font-google-api-key', '' );
					},
					'permission_callback' => function() {
						return current_user_can( 'edit_theme_options' );
					},
				],
				[
					'methods'             => 'POST',
					'callback'            => function ( $request ) {
						return update_option( 'tt-font-google-api-key', $request->get_param( 'api_key' ) );
					},
					'permission_callback' => function() {
						return current_user_can( 'edit_theme_options' );
					},
				],
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
			'easy-google-fonts/v1',
			'/hide-pointer',
			[
				'methods'             => 'POST',
				'callback'            => function () {
					return update_option( 'egf_show_admin_pointer', false );
				},
				'permission_callback' => function() {
					return current_user_can( 'edit_theme_options' );
				},
			]
		);
	}
);
