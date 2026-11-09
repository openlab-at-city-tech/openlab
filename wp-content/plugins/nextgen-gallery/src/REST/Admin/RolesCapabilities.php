<?php

namespace Imagely\NGG\REST\Admin;

/**
 * REST API controller for Roles and Capabilities management
 */
class RolesCapabilities extends \WP_REST_Controller {

	/**
	 * The standard WordPress roles this screen manages, ordered from lowest to highest.
	 *
	 * Capabilities are assigned by walking this order, so a role outside it has no
	 * position to assign from.
	 */
	const STANDARD_ROLES = [ 'subscriber', 'contributor', 'author', 'editor', 'administrator' ];

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

		// Get the standard roles, highest first to match the order they were offered in before.
		$roles     = [];
		$available = $this->ngg_get_sorted_roles();
		foreach ( array_reverse( array_keys( $available ) ) as $role_key ) {
			$roles[ $role_key ] = wp_roles()->roles[ $role_key ]['name'];
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

		// Only the standard roles can be assigned, and only those that exist on this site.
		$valid_roles = array_keys( $this->ngg_get_sorted_roles() );

		$applied = 0;
		$skipped = [];

		// Update each capability
		foreach ( $valid_capabilities as $key => $capability ) {
			if ( isset( $params[ $key ] ) ) {
				$role = sanitize_text_field( $params[ $key ] );

				if ( in_array( $role, $valid_roles, true ) ) {
					$this->ngg_set_capability( $role, $capability );
					++$applied;
				} else {
					$skipped[] = $key;
				}
			}
		}

		// Reporting success for a request that changed nothing would leave the screen
		// showing values it never stored.
		if ( 0 === $applied && ! empty( $skipped ) ) {
			return new \WP_Error(
				'invalid_role',
				__( 'Those roles cannot be assigned, so nothing was saved. Only the standard WordPress roles are supported.', 'nggallery' ),
				[
					'status'  => 400,
					'skipped' => $skipped,
				]
			);
		}

		return new \WP_REST_Response(
			[
				'success' => true,
				'message' => __( 'Roles and capabilities updated successfully', 'nggallery' ),
				'skipped' => $skipped,
			]
		);
	}

	/**
	 * Get the lowest role that has a specific capability
	 */
	private function ngg_get_role( $capability ) {
		foreach ( $this->ngg_get_sorted_roles() as $role_key => $check_role ) {
			if ( $check_role->has_cap( $capability ) ) {
				return $role_key;
			}
		}

		return false;
	}

	/**
	 * Set capability for a role and all higher roles
	 */
	private function ngg_set_capability( $lowest_role, $capability ) {
		$check_order = $this->ngg_get_sorted_roles();

		// Without a position in the order there is nothing to assign from, and walking
		// anyway would strip the capability from every role including administrator.
		if ( ! isset( $check_order[ $lowest_role ] ) ) {
			return;
		}

		$add_capability = false;

		foreach ( $check_order as $role_key => $the_role ) {
			if ( $lowest_role === $role_key ) {
				$add_capability = true;
			}

			$add_capability ? $the_role->add_cap( $capability ) : $the_role->remove_cap( $capability );
		}
	}

	/**
	 * Get the standard roles present on this site, ordered from lowest to highest.
	 */
	private function ngg_get_sorted_roles() {
		$sorted = [];

		foreach ( self::STANDARD_ROLES as $role_key ) {
			$role = get_role( $role_key );

			// A site can delete a standard role. Leaving a null in place here would break
			// every caller, so it is left out of the order entirely.
			if ( $role instanceof \WP_Role ) {
				$sorted[ $role_key ] = $role;
			}
		}

		return $sorted;
	}
}
