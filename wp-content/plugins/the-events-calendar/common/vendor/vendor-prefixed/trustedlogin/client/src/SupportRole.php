<?php
/**
 * Class SupportRole
 *
 * @package TEC\Common\TrustedLogin\SupportRole
 *
 * @copyright 2021 Katz Web Services, Inc.
 */

namespace TEC\Common\TrustedLogin;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class SupportRole
 */
final class SupportRole {

	/**
	 * The capability that is added to the Support Role to indicate that it was created by TrustedLogin.
	 *
	 * @since 1.6.0
	 */
	const CAPABILITY_FLAG = 'trustedlogin_{ns}_support_role';

	/**
	 * Config instance.
	 *
	 * @var Config $config
	 */
	private $config;

	/**
	 * Logging instance.
	 *
	 * @var Logging $logging
	 */
	private $logging;

	/**
	 * The namespaced name of the new Role to be created for Support Agents.
	 *
	 * @example '{vendor/namespace}-support'
	 *
	 * @var string
	 */
	private $role_name;

	/**
	 * Capabilities that are not allowed for users created by TrustedLogin.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	public static $prevented_caps = array(
		'create_users',
		'delete_users',
		'edit_users',
		'list_users',
		'promote_users',
		'delete_site',
		'remove_users',
	);

	/**
	 * Roles that cannot be deleted by TrustedLogin.
	 *
	 * @since 1.6.0
	 *
	 * @var array
	 */
	private static $protected_roles = array(
		'administrator',
		'editor',
		'author',
		'contributor',
		'subscriber',
		'wpseo_editor',
		'wpseo_manager',
		'shop_manager',
		'shop_accountant',
		'shop_worker',
		'shop_vendor',
		'customer',
	);

	/**
	 * SupportUser constructor.
	 *
	 * @param Config  $config  Config instance.
	 * @param Logging $logging Logging instance.
	 */
	public function __construct( Config $config, Logging $logging ) {
		$this->config    = $config;
		$this->logging   = $logging;
		$this->role_name = $this->set_name();
	}

	/**
	 * Get the name (slug) of the role that should be cloned for the TL support role
	 *
	 * @return string
	 */
	public function get_cloned_name() {

		$roles = $this->config->get_setting( 'role', 'editor' );

		// TODO: Support multiple roles.
		$role = is_array( $roles ) ? array_key_first( $roles ) : $roles;

		return (string) $role;
	}

	/**
	 * Get the name (slug) of the role that should be created for the TL support role.
	 *
	 * @return string
	 */
	public function get_name() {

		if ( $this->config->get_setting( 'clone_role' ) ) {
			return (string) $this->role_name;
		}

		return (string) $this->config->get_setting( 'role' );
	}

	/**
	 * Sets the name of the role that should be created for the TL support role.
	 *
	 * @return string Sanitized with {@uses Utils::sanitize_with_dashes()}.
	 */
	private function set_name() {

		// If we're not cloning a role, return the existing role name.
		if ( ! $this->config->get_setting( 'clone_role' ) ) {
			$role_name = (string) $this->config->get_setting( 'role' );

			return Utils::sanitize_with_dashes( $role_name );
		}

		$default = $this->config->ns() . '-support';

		$role_name = apply_filters(
			'trustedlogin/' . $this->config->ns() . '/support_role',
			$default,
			$this
		);

		if ( ! is_string( $role_name ) ) {
			$role_name = $default;
		}

		return Utils::sanitize_with_dashes( $role_name );
	}

	/**
	 * Returns the Support Role, creating it if it doesn't already exist.
	 *
	 * @since 1.6.0
	 *
	 * @return \WP_Role|\WP_Error Role, if successful. WP_Error if failure.
	 */
	public function get() {

		// If cloning a role, create and return it.
		if ( $this->config->get_setting( 'clone_role' ) ) {
			return $this->create();
		}

		// Otherwise, confirm and return the existing role.
		$role_slug = $this->config->get_setting( 'role' );

		$role = get_role( $role_slug );

		if ( is_null( $role ) ) {
			$error = new \WP_Error( 'role_does_not_exist', 'Error: the role does not exist: ' . $role_slug );

			$this->logging->log( $error->get_error_message(), __METHOD__, 'error' );

			return $error;
		}

		return $role;
	}

	/**
	 * Returns the custom capability name that will be added to the role to indicate that it was created by TrustedLogin.
	 *
	 * @param string $ns The namespace of the vendor.
	 *
	 * @return string
	 */
	public static function get_capability_flag( $ns ) {
		return str_replace( '{ns}', $ns, self::CAPABILITY_FLAG );
	}

	/**
	 * Creates the custom Support Role if it doesn't already exist
	 *
	 * @since 1.0.0
	 * @since 1.0.0 removed excluded_caps from generated role.
	 *
	 * @param string $new_role_slug    The slug for the new role (optional). Default: {@see SupportRole::get_name()}.
	 * @param string $clone_role_slug  The slug for the role to clone (optional). Default: {@see SupportRole::get_cloned_name()}.
	 *
	 * @return \WP_Role|\WP_Error Created/pre-existing role, if successful. WP_Error if failure.
	 */
	public function create( $new_role_slug = '', $clone_role_slug = '' ) {

		if ( empty( $new_role_slug ) ) {
			$new_role_slug = $this->get_name();
		}

		if ( ! is_string( $new_role_slug ) ) {
			return new \WP_Error( 'new_role_slug_not_string', 'The slug for the new support role must be a string.' );
		}

		if ( empty( $clone_role_slug ) ) {
			$clone_role_slug = $this->get_cloned_name();
		}

		if ( ! is_string( $clone_role_slug ) ) {
			return new \WP_Error( 'cloned_role_slug_not_string', 'The slug for the cloned support role must be a string.' );
		}

		$role_exists = get_role( $new_role_slug );

		if ( $role_exists ) {
			$this->logging->log( 'Not creating user role; it already exists', __METHOD__, 'notice' );
			return $role_exists;
		}

		$this->logging->log( 'New role slug: ' . $new_role_slug . ', Clone role slug: ' . $clone_role_slug, __METHOD__, 'debug' );

		$old_role = get_role( $clone_role_slug );

		if ( empty( $old_role ) ) {
			return new \WP_Error( 'role_does_not_exist', 'Error: the role to clone does not exist: ' . $clone_role_slug );
		}

		$capabilities = $old_role->capabilities;

		$add_caps = $this->config->get_setting( 'caps/add' );

		foreach ( (array) $add_caps as $add_cap => $reason ) {
			$capabilities[ $add_cap ] = true;
		}

		// These roles should never be assigned to TrustedLogin roles.
		foreach ( self::$prevented_caps as $prevented_cap ) {
			unset( $capabilities[ $prevented_cap ] );
		}

		/**
		 * Modify the display name of the created support role.
		 *
		 * @param string $role_display_name The display name of the role.
		 * @param SupportRole $support_role The SupportRole object.
		 */
		$role_display_name = apply_filters(
			'trustedlogin/' . $this->config->ns() . '/support_role/display_name',
			// translators: %s is replaced with the name of the software developer (e.g. "Acme Widgets").
			sprintf( esc_html__( '%s Support', 'trustedlogin' ), $this->config->get_setting( 'vendor/title' ) ),
			$this
		);

		/**
		 * Add a flag to declare that this role was created by TrustedLogin.
		 *
		 * @used-by SupportRole::delete()
		 */
		$capabilities[ self::get_capability_flag( $this->config->ns() ) ] = true;

		$new_role = add_role( $new_role_slug, $role_display_name, $capabilities );

		if ( ! $new_role ) {
			return new \WP_Error(
				'add_role_failed',
				'Error: the role was not created using add_role()',
				compact(
					'new_role_slug',
					'capabilities',
					'role_display_name'
				)
			);
		}

		$remove_caps = $this->config->get_setting( 'caps/remove' );

		if ( ! empty( $remove_caps ) ) {
			foreach ( $remove_caps as $remove_cap => $description ) {
				$new_role->remove_cap( $remove_cap );
				$this->logging->log( 'Capability ' . $remove_cap . ' removed from role.', __METHOD__, 'info' );
			}
		}

		return $new_role;
	}

	/**
	 * Deletes the Support Role if it exists and was created by TrustedLogin.
	 *
	 * @return bool|null Null: Role wasn't found; True: Removing role succeeded; False: Role wasn't deleted successfully.
	 */
	public function delete() {

		$role_to_delete = get_role( $this->get_name() );

		if ( ! $role_to_delete ) {
			return null;
		}

		$capability_flag = self::get_capability_flag( $this->config->ns() );

		// Don't delete roles that weren't created by TrustedLogin.
		if ( ! $role_to_delete->has_cap( $capability_flag ) ) {
			$this->logging->log( 'Role ' . $this->get_name() . ' is missing the CAPABILITY_FLAG. It is not possible to determine that it was created by TrustedLogin; it will not be removed.', __METHOD__, 'error' );

			return false;
		}

		// Sanity check: don't ever, for any reason, delete protected roles.
		if ( in_array( $this->get_name(), self::$protected_roles, true ) ) {
			$this->logging->log( 'Role ' . $this->get_name() . ' is protected and cannot be removed.', __METHOD__, 'error' );

			return false;
		}

		// Returns void; no way to tell if successful...
		remove_role( $this->get_name() );

		// So we manually check if it was removed successfully.
		if ( get_role( $this->get_name() ) ) {
			$this->logging->log( 'Role ' . $this->get_name() . ' was not removed successfully.', __METHOD__, 'error' );

			return false;
		}

		$this->logging->log( 'Role ' . $this->get_name() . ' removed.', __METHOD__, 'info' );

		return true;
	}
}
