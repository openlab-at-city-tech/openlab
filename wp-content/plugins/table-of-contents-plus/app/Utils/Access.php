<?php
namespace AIOSEO\TableOfContents\Utils;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Access {
	/**
	 * Capabilities for our users.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $capabilities = [
		'aioseo_table_of_contents_about_us_page'
	];

	/**
	 * Roles we check capabilities against.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $roles = [
		'superadmin'    => 'superadmin',
		'administrator' => 'administrator'
	];

	/**
	 * Whether or not we are updating roles.
	 *
	 * @since 1.1.0
	 *
	 * @var bool
	 */
	private $isUpdatingRoles = false;

	/**
	 * Adds capabilities into WordPress for the current user.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function addCapabilities() {
		foreach ( $this->roles as $wpRole => $role ) {
			$roleObject = get_role( $wpRole );
			if ( ! is_object( $roleObject ) ) {
				continue;
			}

			if ( current_user_can( 'edit_posts' ) || $this->isAdmin() ) {
				foreach ( $this->capabilities as $cap ) {
					$roleObject->add_cap( $cap );
				}
			}
		}

		$this->removeCapabilities();
	}

	/**
	 * Removes capabilities for any unknown role.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function removeCapabilities() {
		$this->isUpdatingRoles = true;

		// Clear out capabilities for unknown roles.
		$wpRoles  = wp_roles();
		$allRoles = $wpRoles->roles;
		foreach ( $allRoles as $key => $wpRole ) {
			$checkRole = is_multisite() ? 'superadmin' : 'administrator';
			if ( $checkRole === $key ) {
				continue;
			}

			if ( in_array( $key, $this->roles, true ) ) {
				continue;
			}

			$role = get_role( $key );
			if ( empty( $role ) ) {
				continue;
			}

			if ( $this->isAdmin( $key ) ) {
				continue;
			}

			foreach ( $this->capabilities as $capability ) {
				if ( $role->has_cap( $capability ) ) {
					$role->remove_cap( $capability );
				}
			}
		}
	}

	/**
	 * Checks if the current user has the capability.
	 *
	 * @since 1.0.0
	 *
	 * @param  string      $capability The capability to check against.
	 * @param  string|null $checkRole  A role to check against.
	 * @return bool                    Whether or not the user has this capability.
	 */
	public function hasCapability( $capability, $checkRole = null ) {
		// Only admins have access.
		if ( $this->isAdmin( $checkRole ) ) {
			return true;
		}

		if (
			(
				$this->can( 'publish_posts', $checkRole ) ||
				$this->can( 'edit_posts', $checkRole )
			) &&
			false !== strpos( $capability, 'aioseo_table_of_contents_' )
		) {
			return true;
		}

		return false;
	}

	/**
	 * Gets all the capabilities for the current user.
	 *
	 * @since 1.0.0
	 *
	 * @param  string|null $role A role to check against.
	 * @return array             An array of capabilities.
	 */
	public function getAllCapabilities( $role = null ) {
		$capabilities = [];
		foreach ( $this->capabilities as $cap ) {
			$capabilities[ $cap ] = $this->hasCapability( $cap, $role );
		}

		return $capabilities;
	}

	/**
	 * If the current user is an admin, or superadmin, they have access to all caps regardless.
	 *
	 * @since 1.0.0
	 *
	 * @param  string|null $role The role to check admin privileges if we have one.
	 * @return bool              Whether not the user/role is an admin.
	 */
	public function isAdmin( $role = null ) {
		if ( $role ) {
			if ( ( is_multisite() && 'superadmin' === $role ) || 'administrator' === $role ) {
				return true;
			}

			return false;
		}

		if ( ( is_multisite() && current_user_can( 'superadmin' ) ) || current_user_can( 'administrator' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Whether the current user (or role) can manage the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @param  string|null $checkRole The role to check.
	 * @return bool                   Whether the user/role can manage the plugin.
	 */
	public function canManage( $checkRole = null ) {
		return $this->isAdmin( $checkRole );
	}

	/**
	 * Check if the passed in role can publish posts.
	 *
	 * @since 1.0.0
	 *
	 * @param  string  $capability The capability to check against.
	 * @param  string  $role       The role to check.
	 * @return boolean             True if the role can publish.
	 */
	protected function can( $capability, $role ) {
		if ( empty( $role ) ) {
			return current_user_can( $capability );
		}

		$wpRoles  = wp_roles();
		$allRoles = $wpRoles->roles;
		foreach ( $allRoles as $key => $wpRole ) {
			if ( $key === $role ) {
				$r = get_role( $key );
				if ( $r->has_cap( $capability ) ) {
					return true;
				}
			}
		}

		return false;
	}
}