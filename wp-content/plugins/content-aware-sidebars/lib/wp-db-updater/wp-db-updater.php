<?php
/**
 * @package wp-db-updater
 * @version 2.0
 * @author Joachim Jensen <jv@intox.dk>
 * @license GPLv3
 * @copyright 2018 by Joachim Jensen
 */

if (!class_exists('WP_DB_Updater')) {
    class WP_DB_Updater
    {

        /**
         * Required capability to run updates
         */
        const CAPABILITY  = 'update_plugins';

        /**
         * Key where installed_version is stored
         * @var string
         */
        protected $version_key;

        /**
         * Skip update procedures for new installations
         *
         * @var boolean
         */
        protected $skip_new;

        /**
         * Version in database
         * @var string
         */
        protected $installed_version;

        /**
         * New version of plugin
         * @var string
         */
        protected $plugin_version;

        /**
         * Versions to be installed
         * @var array
         */
        protected $versions = array();

        /**
         * @since 1.0
         * @param string  $version_key
         * @param string  $plugin_version
         * @param boolean $skip_new
         */
        public function __construct($version_key, $plugin_version, $skip_new = false)
        {
            $this->version_key = $version_key;
            $this->plugin_version = $plugin_version;
            $this->skip_new = $skip_new;

            if (is_admin()) {
                add_action('wp_loaded', array($this,'run'));
            }
        }

        /**
         * Run updates
         *
         * @since  1.0
         * @return void
         */
        public function run()
        {
            if (!current_user_can(self::CAPABILITY)) {
                return;
            }

            if (!($this->is_new_install() && $this->skip_new)) {
                uksort($this->versions, 'version_compare');

                //Run update installations
                foreach ($this->versions as $version => $callbacks) {
                    do_action($this->get_action_name($version));
                    $this->set_installed_version($version);
                }
            }

            //If no update exist for current version, just set it
            if (!$this->is_version_installed($this->plugin_version)) {
                $this->set_installed_version($this->plugin_version);
            }
        }

        /**
         * Register version upgrade callback to queue
         *
         * @since  1.0
         * @param  string  $version
         * @param  string  $callback
         * @return void
         */
        public function register_version_update($version, $callback)
        {
            if (!$this->is_version_installable($version)) {
                return;
            }

            if (!isset($this->versions[$version])) {
                $this->versions[$version] = array();
            }

            $this->versions[$version][] = $callback;
            add_action($this->get_action_name($version), $callback);
        }

        /**
         * Unregister version upgrade callback from queue
         *
         * @since  2.0
         * @param  string  $version
         * @param  string  $callback
         * @return void
         */
        public function unregister_version_update($version, $callback)
        {
            if (!isset($this->versions[$version])) {
                return;
            }

            $pos = array_search($callback, $this->versions[$version]);

            if ($pos === false) {
                return;
            }

            unset($this->versions[$version][$pos]);
            remove_action($this->get_action_name($version), $callback);
        }

        /**
         * Get installed version locally
         * Fetches installed version from database
         * on first use
         *
         * @since  1.0
         * @return string
         */
        protected function get_installed_version()
        {
            return $this->installed_version != null ? $this->installed_version : $this->fetch_installed_version();
        }

        /**
         * Fetch installed version from database
         * and refresh locally
         *
         * @since  1.0
         * @return string
         */
        protected function fetch_installed_version()
        {
            return ($this->installed_version = get_option($this->version_key, '0'));
        }

        /**
         * Set installed version locally and in db
         *
         * @since 1.0
         * @param string  $version
         */
        protected function set_installed_version($version)
        {
            $this->installed_version = $version;
            $this->sync_installed_version();
        }

        /**
         * Sync local installed version with db
         *
         * @since  1.0
         * @return void
         */
        protected function sync_installed_version()
        {
            update_option($this->version_key, $this->installed_version);
        }

        /**
         * Is version already installed in db
         *
         * @since  1.0
         * @param  string  $version
         * @return boolean
         */
        protected function is_version_installed($version)
        {
            return version_compare($this->get_installed_version(), $version, '>=');
        }

        /**
         * Is version uninstalled and below or equal to latest uninstalled version
         *
         * @since  2.0
         * @param  string  $version
         * @return boolean
         */
        protected function is_version_installable($version)
        {
            return !$this->is_version_installed($version) && version_compare($this->plugin_version, $version, '>=');
        }

        /**
         * Is this a new installation of the plugin
         *
         * @since  2.0
         * @return boolean
         */
        protected function is_new_install()
        {
            return $this->get_installed_version() == '0';
        }

        /**
         * @since  2.0
         * @param  string  $version
         * @return string
         */
        protected function get_action_name($version)
        {
            return __CLASS__.'/'.$this->version_key.'/'.$version;
        }
    }
}
