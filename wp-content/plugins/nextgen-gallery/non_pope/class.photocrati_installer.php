<?php

/**
 * Class C_Gallery_Display_Installer
 *
 * This is a class added to 2.0.68 for compatiblity reasons, and can be removed after NextGEN Pro 2.2 is released
 */
class C_Gallery_Display_Installer
{
	static $_proxy = NULL;

	function get_proxy()
	{
		if (!self::$_proxy) {
			self::$_proxy = new C_Display_Type_Installer;
		}
		return self::$_proxy;
	}

	function install($reset=FALSE)
	{
		$this->get_proxy()->install($reset);
	}

	function uninstall()
	{
		$this->get_proxy()->uninstall();
	}


	function __call($method, $args)
	{
		$klass = new ReflectionMethod($this->get_proxy(), $method);
		return $klass->invokeArgs($this->get_proxy(), $args);
	}
}

if (!class_exists('C_Photocrati_Installer'))
{
	class C_Photocrati_Installer
	{
		static $_instance = NULL;

        /**
         * @return C_Photocrati_Installer
         */
		static function get_instance()
		{
			if (is_null(self::$_instance)) {
				$klass = get_class();
				self::$_instance = new $klass();
			}
			return self::$_instance;
		}

		/**
		 * Each product and module will register it's own handler (a class, with an install() and uninstall() method)
		 * to be used for install/uninstall routines
		 * @param $name
		 * @param $handler
		 */
		static function add_handler($name, $handler)
		{
			self::get_instance()->_installers[$name] = $handler;
		}

		/**
		 * Gets an instance of an installation handler
		 * @param $name
		 * @return mixed
		 */
		static function get_handler_instance($name)
		{
			$installers = $handler = self::get_instance()->_installers;
			if (isset($installers[$name])) {
				$klass = $installers[$name];
				return new $klass;
			}
			else return NULL;
		}


		/**
		 * Uninstalls a product
		 * @param $product
		 * @param bool $hard
		 * @return mixed
		 */
		static function uninstall($product, $hard=FALSE)
		{
			$handler = self::get_handler_instance($product);
			if ($handler && method_exists($handler, 'uninstall')) return $handler->uninstall($hard);

			if ($handler && $hard) {
				C_NextGen_Settings::get_instance()->destroy();
                C_NextGen_Global_Settings::get_instance()->destroy();
			}
		}

        static function can_do_upgrade()
        {
            $proceed = FALSE;

            // Proceed if no other process has started the installer routines
            if (!($doing_upgrade = get_option('ngg_doing_upgrade', FALSE))) {
                update_option('ngg_doing_upgrade', time());
                $proceed = TRUE;
            }

            // Or, force proceeding if we have a stale ngg_doing_upgrade record
            elseif ($doing_upgrade === TRUE OR time() - $doing_upgrade > 120) {
                update_option('ngg_doing_upgrade', time());
                $proceed = TRUE;
            }
            return $proceed;
        }

        static function done_upgrade()
        {
            delete_option('ngg_doing_upgrade');
        }

		static function update($reset=FALSE)
		{
			$local_settings     = C_NextGen_Settings::get_instance();
            $global_settings    = C_NextGen_Global_Settings::get_instance();

            // Somehow some installations are missing several default settings
            // Because imgWidth is essential to know we do a 'soft' reset here
            // by filling in any missing options from the default settings
			$settings_installer = new C_NextGen_Settings_Installer();
			if (!$global_settings->gallerypath) {
				$global_settings->reset();
                $settings_installer->install_global_settings();
                $global_settings->save();
			}
            if (!$local_settings->imgWidth) {
                $local_settings->reset();
                $settings_installer->install_local_settings();
                $local_settings->save();
            }

            // This is a specific hack/work-around/fix and can probably be removed sometime after 2.0.20's release
            //
            // NextGen 2x was not multisite compatible until 2.0.18. Users that upgraded before this
            // will have nearly all of their settings stored globally (network wide) in wp_sitemeta. If
            // pope_module_list (which should always be a local setting) exists site-wide we wipe the current
            // global ngg_options and restore from defaults. This should only ever run once.
            if (is_multisite() && isset($global_settings->pope_module_list))
            {
                // Setting this to TRUE will wipe current settings for display types, but also
                // allows the display type installer to run correctly
                $reset = TRUE;

                $settings_installer = new C_NextGen_Settings_Installer();
                $global_defaults = $settings_installer->get_global_defaults();

                // Preserve the network options we honor by restoring them after calling $global_settings->reset()
                $global_settings_to_keep = array();
                foreach ($global_defaults as $key => $val) {
                    $global_settings_to_keep[$key] = $global_settings->$key;
                }

                // Resets internal options to an empty array
                $global_settings->reset();

                // Restore the defaults, then our saved values. This must be done again later because
                // we've set $reset to TRUE.
                $settings_installer->install_global_settings();
                foreach ($global_settings_to_keep as $key => $val) {
                    $global_settings->$key = $val;
                }
            }

			// Get last module list and current module list. Compare...
            $last_module_list = self::_get_last_module_list($reset);
			$current_module_list = self::_generate_module_info();
			$diff = array_diff($current_module_list, $last_module_list);
			$do_upgrade = (count($diff)>0 || count($last_module_list) != count($current_module_list));
			$can_upgrade = $do_upgrade ? self::can_do_upgrade() : FALSE;
			if ($can_upgrade && !$diff) $diff = $current_module_list;

			if ($can_upgrade && $do_upgrade) {

                // Clear APC cache
                if (function_exists('apc_clear_cache')) {
                    @apc_clear_cache('opcode');
                    apc_clear_cache();
                }

				// Clear all of our transients
				wp_cache_flush();
                C_Photocrati_Transient_Manager::flush();

				// Remove all NGG created cron jobs
				self::refresh_cron();

				// Delete auto-update cache
				update_option('photocrati_auto_update_admin_update_list', null);
				update_option('photocrati_auto_update_admin_check_date', '');

				// Other Pope applications might be loaded, and therefore
				// all singletons should be destroyed, so that they can be
				// adapted as necessary. For now, we'll just assume that the factory
				// is the only singleton that will be used by other Pope applications
				C_Component_Factory::$_instances = array();

				foreach ($diff as $module_name) {
					$parts = explode('|', $module_name);
					if (($handler = self::get_handler_instance(array_shift($parts)))) {
						if (method_exists($handler, 'install'))
                            $handler->install($reset);
					}
				}

                // NOTE & TODO: if the above section that declares $global_settings_to_keep is removed this should also
                // Since a hard-reset of the settings was forced we must again re-apply our previously saved values
                if (isset($global_settings_to_keep)) {
                    foreach ($global_settings_to_keep as $key => $val) {
                        $global_settings->$key = $val;
                    }
                }

				// Save any changes settings
				$global_settings->save();
				$local_settings->save();

				// Set role capabilities
                C_NextGEN_Bootstrap::set_role_caps();
            }

            // Another workaround to an issue caused by NextGen's lack of multisite compatibility. It's possible
            // the string substitation wasn't performed, so if a '%' symbol exists in gallerypath we reset it. It's
            // another db call, but again this should only ever run once.
            //
            // Remove this when removing the above reset-global-settings code
            if (strpos($local_settings->gallerypath, '%'))
            {
                $settings_installer = new C_NextGen_Settings_Installer();
                $local_settings->gallerypath = $settings_installer->gallerypath_replace($global_settings->gallerypath);
                $local_settings->save();
            }

			// Update the module list, and remove the update flag
			if ($can_upgrade)
			{
				update_option('pope_module_list', $current_module_list);
				self::done_upgrade();
			}
		}

        static function _get_last_module_list($reset=FALSE)
        {
            // Return empty array to reset
            if ($reset) return array();

            // First try getting the list from a single WP option, "pope_module_list"
            $retval = get_option('pope_module_list', array());
            if (!$retval) {
                $local_settings     = C_NextGen_Settings::get_instance();
                $retval = $local_settings->get('pope_module_list', array());
                $local_settings->delete('pope_module_list');
            }

            return $retval;
        }

		static function _generate_module_info()
		{
			$retval = array();
			$registry = C_Component_Registry::get_instance();
			$products  = array('photocrati-nextgen');
			foreach ($registry->get_product_list() as $product_id) {
				if ($product_id != 'photocrati-nextgen') $products[] = $product_id;
			}

			foreach ($products as $product_id) {
				foreach ($registry->get_module_list($product_id) as $module_id) {
					if (($module = $registry->get_module($module_id))) {
						$module_version = $module->module_version;
						$module_string = "{$module_id}|{$module_version}";
						if (!in_array($module_string, $retval)) $retval[] = $module_string;
					}
				}
			}

			return $retval;
		}

		static function refresh_cron()
		{
            if (!extension_loaded('suhosin')) @ini_set('memory_limit', -1);

			// Remove all cron jobs created by NextGEN Gallery
			$cron = _get_cron_array();
			if (is_array($cron)) {
				foreach ($cron as $timestamp => $job) {
					if (is_array($job)) {
						unset($cron[$timestamp]['ngg_delete_expired_transients']);
						if (empty($cron[$timestamp])) {
							unset($cron[$timestamp]);
						}
					}
				}
			}
			_set_cron_array($cron);
		}
	}
}
