<?php

namespace Imagely\NGG\Util;

use Imagely\NGG\Settings\Settings;
use Imagely\NGG\Settings\GlobalSettings;

class Installer {

	protected static $_installers = [];

	/**
	 * Each product and module will register its own handler (a class, with an install() and uninstall() method)
	 * to be used for install/uninstall routines
	 *
	 * @param string        $name
	 * @param string|object $handler
	 */
	public static function add_handler( $name, $handler ) {
		self::$_installers[ $name ] = $handler;
	}

	/**
	 * Gets an instance of an installation handler
	 *
	 * @param $name
	 * @return mixed
	 */
	public static function get_handler_instance( $name ) {
		if ( isset( self::$_installers[ $name ] ) ) {
			$klass = self::$_installers[ $name ];
			return new $klass();
		} else {
			return null;
		}
	}

	/**
	 * @return array
	 */
	protected static function get_all_handlers() {
		return self::$_installers;
	}

	/**
	 * Uninstalls a product
	 *
	 * @param string $product
	 * @param bool   $hard
	 * @return bool
	 */
	public static function uninstall( $product, $hard = false ) {
		$handler = self::get_handler_instance( $product );

		if ( $handler && \method_exists( $handler, 'uninstall' ) ) {
			return $handler->uninstall( $hard );
		}

		if ( $handler && $hard ) {
			Settings::get_instance()->destroy();
			GlobalSettings::get_instance()->destroy();
		}

		return true;
	}

	public static function can_do_upgrade() {
		$proceed = false;

		// Proceed if no other process has started the installer routines.
		if ( ! ( $doing_upgrade = \get_option( 'ngg_doing_upgrade', false ) ) ) {
			\update_option( 'ngg_doing_upgrade', \time() );
			$proceed = true;
		}

		// Or, force proceeding if we have a stale ngg_doing_upgrade record.
		elseif ( $doing_upgrade === true or \time() - $doing_upgrade > 120 ) {
			\update_option( 'ngg_doing_upgrade', \time() );
			$proceed = true;
		}

		return $proceed;
	}

	public static function done_upgrade() {
		\delete_option( 'ngg_doing_upgrade' );
	}

	public static function update( $reset = false ) {
		$local_settings  = Settings::get_instance();
		$global_settings = GlobalSettings::get_instance();

		$do_upgrade = false;

		// TODO: remove this when POPE v1 compatibility is reached in Pro.
		if ( \C_NextGEN_Bootstrap::get_pro_api_version() < 4.0 ) {
			// Get last module list and current module list. Compare...
			$last_module_list    = self::_get_last_module_list( $reset );
			$current_module_list = self::_generate_module_info();

			$diff       = \array_diff( $current_module_list, $last_module_list );
			$do_upgrade = ( \count( $diff ) > 0 || \count( $last_module_list ) != \count( $current_module_list ) );
		}

		$ngg_version_setting = $local_settings->get( 'ngg_plugin_version', 0 );
		if ( ! $ngg_version_setting || $ngg_version_setting !== NGG_PLUGIN_VERSION ) {
			$do_upgrade = true;
		}

		// Allow NextGEN extensions to trigger this process.
		$do_upgrade = \apply_filters( 'ngg_do_install_or_setup_process', $do_upgrade );

		$can_upgrade = $do_upgrade && self::can_do_upgrade();

		if ( $can_upgrade && $do_upgrade ) {
			// Clear APC cache.
			if ( \function_exists( 'apc_clear_cache' ) ) {
				@\apc_clear_cache( 'opcode' );
				\apc_clear_cache();
			}

			// Attempt to reset the opcache. NextGEN 3.50+ and Pro 3.30+ moved, renamed, and deleted several files
			// and purging the opcache should help prevent fatal errors due to cached instructions.
			if ( \function_exists( 'opcache_reset' ) ) {
				\opcache_reset();
			}

			// Clear all of our transients.
			\wp_cache_flush();
			Transient::flush();

			// Remove all NGG created cron jobs.
			self::refresh_cron();

			// Other Pope applications might be loaded, and therefore all singletons should be destroyed, so that they
			// can be adapted as necessary. For now, we'll just assume that the factory is the only singleton that will
			// be used by other Pope applications.
			if ( class_exists( '\C_Component_Factory' ) ) {
				\C_Component_Factory::$_instances = [];
			}

			foreach ( self::get_all_handlers() as $handler_name => $handler_class ) {
				$handler = new $handler_class();
				if ( \method_exists( $handler, 'install' ) ) {
					$handler->install( $reset );
				}
			}

			// Record the current version; changes to this and setting are how updates are triggered.
			$local_settings->set( 'ngg_plugin_version', NGG_PLUGIN_VERSION );

			$global_settings->save();
			$local_settings->save();

			self::set_role_caps();
			\do_action( 'ngg_did_install_or_setup_process' );
		}

		// Update the module list, and remove the update flag.
		if ( $can_upgrade ) {
			if ( isset( $current_module_list ) ) {
				\update_option( 'pope_module_list', $current_module_list );
			}
			self::done_upgrade();
		}
	}

	public static function _get_last_module_list( $reset = false ) {
		if ( $reset ) {
			return [];
		}

		// First try getting the list from a single WP option, "pope_module_list".
		$retval = \get_option( 'pope_module_list', [] );
		if ( ! $retval ) {
			$local_settings = Settings::get_instance();
			$retval         = $local_settings->get( 'pope_module_list', [] );
			$local_settings->delete( 'pope_module_list' );
		}

		return $retval;
	}

	protected static function _generate_module_info() {
		$retval   = [];
		$registry = \C_Component_Registry::get_instance();
		$products = [ 'photocrati-nextgen' ];
		foreach ( $registry->get_product_list() as $product_id ) {
			if ( $product_id != 'photocrati-nextgen' ) {
				$products[] = $product_id;
			}
		}

		foreach ( $products as $product_id ) {
			foreach ( $registry->get_module_list( $product_id ) as $module_id ) {
				if ( ( $module = $registry->get_module( $module_id ) ) ) {
					$module_version = $module->module_version;
					$module_string  = "{$module_id}|{$module_version}";
					if ( ! \in_array( $module_string, $retval ) ) {
						$retval[] = $module_string;
					}
				}
			}
		}

		return $retval;
	}

	public static function refresh_cron() {
		if ( ! \extension_loaded( 'suhosin' ) ) {
			@\ini_set( 'memory_limit', -1 );
		}

		// Remove all cron jobs created by NextGEN Gallery.
		$cron = \_get_cron_array();
		if ( \is_array( $cron ) ) {
			foreach ( $cron as $timestamp => $job ) {
				if ( \is_array( $job ) ) {
					unset( $cron[ $timestamp ]['ngg_delete_expired_transients'] );
					if ( empty( $cron[ $timestamp ] ) ) {
						unset( $cron[ $timestamp ] );
					}
				}
			}
		}

		\_set_cron_array( $cron );
	}

	public static function set_role_caps() {
		// Set the capabilities for the administrator.
		$role = \get_role( 'administrator' );

		if ( ! $role ) {
			if ( ! class_exists( 'WP_Roles' ) ) {
				include_once ABSPATH . '/wp-includes/class-wp-roles.php';
			}
			$roles = new \WP_Roles();
			$roles->init_roles();
		}

		// We need this role, no other chance.
		$role = \get_role( 'administrator' );
		if ( ! $role ) {
			\update_option( 'ngg_init_check', __( 'Sorry, NextGEN Gallery works only with a role called administrator', 'nggallery' ) );
			return;
		}

		delete_option( 'ngg_init_check' );

		$capabilities = [
			'NextGEN Attach Interface',
			'NextGEN Change options',
			'NextGEN Change style',
			'NextGEN Edit album',
			'NextGEN Gallery overview',
			'NextGEN Manage gallery',
			'NextGEN Manage others gallery',
			'NextGEN Manage tags',
			'NextGEN Upload images',
			'NextGEN Use TinyMCE',
		];

		foreach ( $capabilities as $capability ) {
			$role->add_cap( $capability );
		}
	}
}
