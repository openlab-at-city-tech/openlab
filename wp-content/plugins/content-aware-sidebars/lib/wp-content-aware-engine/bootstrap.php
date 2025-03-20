<?php
/**
 * @package wp-content-aware-engine
 * @author Joachim Jensen <joachim@dev.institute>
 * @license GPLv3
 * @copyright 2023 by Joachim Jensen
 */

defined('ABSPATH') || exit;

/**
 * Version of this WPCA
 * @var string
 */
$this_wpca_version = '17.0';

/**
 * Class to make sure the latest
 * version of WPCA gets loaded
 *
 * @since 3.0
 */
if (!class_exists('WPCALoader')) {
    class WPCALoader
    {
        /**
         * Absolute paths and versions
         * @var array
         */
        private static $_paths = [];

        public function __construct()
        {
        }

        /**
         * Add path to loader
         *
         * @since 3.0
         * @param string  $path
         * @param string  $version
         */
        public static function add($path, $version)
        {
            self::$_paths[$path] = $version;
        }

        /**
         * Load file for newest version
         * and setup engine
         *
         * @since  3.0
         * @return void
         */
        public static function load()
        {
            //legacy version present, cannot continue
            if (class_exists('WPCACore')) {
                return;
            }

            uasort(self::$_paths, 'version_compare');
            foreach (array_reverse(self::$_paths, true) as $path => $version) {
                $file = $path . 'core.php';
                if (file_exists($file)) {
                    include $file;
                    define('WPCA_VERSION', $version);
                    WPCACore::init();
                    do_action('wpca/loaded');
                    break;
                }
            }
        }

        /**
         * Get all paths added to loader
         * Sorted if called after plugins_loaded
         *
         * @since  3.0
         * @return array
         */
        public static function debug()
        {
            return self::$_paths;
        }
    }
    //Hook as early as possible after plugins are loaded
    add_action(
        'plugins_loaded',
        ['WPCALoader','load'],
        defined('PHP_INT_MIN') ? PHP_INT_MIN : ~PHP_INT_MAX
    );
}
WPCALoader::add(plugin_dir_path(__FILE__), $this_wpca_version);
