<?php

namespace Imagely\NGG\REST\Admin;

/**
 * REST API controller for Roles and Capabilities management
 */
class RolesCapabilities extends \WP_REST_Controller {

	public function __construct() {
		$this->namespace = 'imagely/v1';
		$this->rest_base = 'roles-capabilities';
	}

	public function register_routes() {
		\register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			[
				[
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_roles_capabilities' ],
					'permission_callback' => [ $this, 'get_items_permissions_check' ],
				],
				[
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => [ $this, 'update_roles_capabilities' ],
					'permission_callback' => [ $this, 'update_items_permissions_check' ],
				],
			]
		);
	}

	/**
	 * Check if user can view roles and capabilities
	 */
	public function get_items_permissions_check( $request ) {
		return \Imagely\NGG\Admin\App::can_access_roles_settings();
	}

	/**
	 * Check if user can update roles and capabilities
	 */
	public function update_items_permissions_check( $request ) {
		return \Imagely\NGG\Admin\App::can_access_roles_settings();
	}

	/**
	 * Get current roles and capabilities configuration
	 */
	public function get_roles_capabilities( $request ) {
		$capabilities = [
			'general'          => [
				'name'         => __( 'Main NextGEN Gallery overview', 'nggallery' ),
				'capability'   => 'NextGEN Gallery overview',
				'current_role' => $this->ngg_get_role( 'NextGEN Gallery overview' ),
			],
			'tinymce'          => [
				'name'         => __( 'Use TinyMCE Button / Upload tab', 'nggallery' ),
				'capability'   => 'NextGEN Use TinyMCE',
				'current_role' => $this->ngg_get_role( 'NextGEN Use TinyMCE' ),
			],
			'add_gallery'      => [
				'name'         => __( 'Add gallery / Upload images', 'nggallery' ),
				'capability'   => 'NextGEN Upload images',
				'current_role' => $this->ngg_get_role( 'NextGEN Upload images' ),
			],
			'manage_gallery'   => [
				'name'         => __( 'Manage gallery', 'nggallery' ),
				'capability'   => 'NextGEN Manage gallery',
				'current_role' => $this->ngg_get_role( 'NextGEN Manage gallery' ),
			],
			'manage_others'    => [
				'name'         => __( 'Manage others gallery', 'nggallery' ),
				'capability'   => 'NextGEN Manage others gallery',
				'current_role' => $this->ngg_get_role( 'NextGEN Manage others gallery' ),
			],
			'manage_tags'      => [
				'name'         => __( 'Manage tags', 'nggallery' ),
				'capability'   => 'NextGEN Manage tags',
				'current_role' => $this->ngg_get_role( 'NextGEN Manage tags' ),
			],
			'edit_album'       => [
				'name'         => __( 'Edit Album', 'nggallery' ),
				'capability'   => 'NextGEN Edit album',
				'current_role' => $this->ngg_get_role( 'NextGEN Edit album' ),
			],
			'change_style'     => [
				'name'         => __( 'Change style', 'nggallery' ),
				'capability'   => 'NextGEN Change style',
				'current_role' => $this->ngg_get_role( 'NextGEN Change style' ),
			],
			'change_options'   => [
				'name'         => __( 'Change options', 'nggallery' ),
				'capability'   => 'NextGEN Change options',
				'current_role' => $this->ngg_get_role( 'NextGEN Change options' ),
			],
			'attach_interface' => [
				'name'         => __( 'NextGEN Attach Interface', 'nggallery' ),
				'capability'   => 'NextGEN Attach Interface',
				'current_role' => $this->ngg_get_role( 'NextGEN Attach Interface' ),
			],
		];

		// Get available WordPress roles
		$roles    = [];
		$wp_roles = wp_roles()->roles;
		foreach ( $wp_roles as $role_key => $role_data ) {
			$roles[ $role_key ] = $role_data['name'];
		}

		return new \WP_REST_Response(
			[
				'capabilities' => $capabilities,
				'roles'        => $roles,
			]
		);
	}

	/**
	 * Update roles and capabilities configuration
	 */
	public function update_roles_capabilities( $request ) {
		$params = $request->get_json_params();

		if ( empty( $params ) || ! is_array( $params ) ) {
			return new \WP_Error( 'invalid_data', __( 'Invalid data provided', 'nggallery' ), [ 'status' => 400 ] );
		}

		// Validate and sanitize the data
		$valid_capabilities = [
			'general'          => 'NextGEN Gallery overview',
			'tinymce'          => 'NextGEN Use TinyMCE',
			'add_gallery'      => 'NextGEN Upload images',
			'manage_gallery'   => 'NextGEN Manage gallery',
			'manage_others'    => 'NextGEN Manage others gallery',
			'manage_tags'      => 'NextGEN Manage tags',
			'edit_album'       => 'NextGEN Edit album',
			'change_style'     => 'NextGEN Change style',
			'change_options'   => 'NextGEN Change options',
			'attach_interface' => 'NextGEN Attach Interface',
		];

		$wp_roles    = wp_roles()->roles;
		$valid_roles = array_keys( $wp_roles );

		// Update each capability
		foreach ( $valid_capabilities as $key => $capability ) {
			if ( isset( $params[ $key ] ) ) {
				$role = sanitize_text_field( $params[ $key ] );

				// Validate that the role exists
				if ( in_array( $role, $valid_roles, true ) ) {
					$this->ngg_set_capability( $role, $capability );
				}
			}
		}

		return new \WP_REST_Response(
			[
				'success' => true,
				'message' => __( 'Roles and capabilities updated successfully', 'nggallery' ),
			]
		);
	}

	/**
	 * Get the lowest role that has a specific capability
	 * (Copied from roles.php)
	 */
	private function ngg_get_role( $capability ) {
		$check_order = $this->ngg_get_sorted_roles();

		$args = array_slice( func_get_args(), 1 );
		$args = array_merge( [ $capability ], $args );

		foreach ( $check_order as $check_role ) {
			if ( empty( $check_role ) ) {
				return false;
			}

			if ( call_user_func_array( [ &$check_role, 'has_cap' ], $args ) ) {
				return $check_role->name;
			}
		}
		return false;
	}

	/**
	 * Set capability for a role and all higher roles
	 * (Copied from roles.php)
	 */
	private function ngg_set_capability( $lowest_role, $capability ) {
		$check_order = $this->ngg_get_sorted_roles();

		$add_capability = false;

		foreach ( $check_order as $the_role ) {
			$role = $the_role->name;

			if ( $lowest_role == $role ) {
				$add_capability = true;
			}

			// If you rename the roles, then please use a role manager plugin.
			if ( empty( $the_role ) ) {
				continue;
			}

			$add_capability ? $the_role->add_cap( $capability ) : $the_role->remove_cap( $capability );
		}
	}

	/**
	 * Get sorted roles by user level
	 * (Copied from roles.php)
	 */
	private function ngg_get_sorted_roles() {
		global $wp_roles;
		$roles  = $wp_roles->role_objects;
		$sorted = [];

		if ( class_exists( 'RoleManager' ) ) {
			foreach ( $roles as $role_key => $role_name ) {
				$role = get_role( $role_key );
				if ( empty( $role ) ) {
					continue;
				}
				$role_user_level            = array_reduce( array_keys( $role->capabilities ), [ 'WP_User', 'level_reduction' ], 0 );
				$sorted[ $role_user_level ] = $role;
			}
			$sorted = array_values( $sorted );
		} else {
			$role_order = [ 'subscriber', 'contributor', 'author', 'editor', 'administrator' ];
			foreach ( $role_order as $role_key ) {
				$sorted[ $role_key ] = get_role( $role_key );
			}
		}
		return $sorted;
	}
}
