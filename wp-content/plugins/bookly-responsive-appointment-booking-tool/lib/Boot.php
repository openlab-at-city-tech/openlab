<?php
namespace Bookly\Lib;

/**
 * Class Boot
 * @package Bookly\Lib
 */
class Boot
{
    /**
     * Boot up.
     */
    public static function up()
    {
        $main_file = self::mainFile();
        $plugin    = self::pluginClass();

        // Register activation/deactivation hooks.
        register_activation_hook( $main_file, array( $plugin, 'activate' ) );
        register_deactivation_hook( $main_file, array( $plugin, 'deactivate' ) );
        register_uninstall_hook( $main_file, array( $plugin, 'uninstall' ) );

        // Run plugin.
        add_action( 'plugins_loaded', function () use ( $plugin ) {
            $plugin::run();
        }, 8, 1 );
    }

    /**
     * Get path to plugin main file.
     *
     * @return string
     */
    public static function mainFile()
    {
        return dirname( __DIR__ ) . '/main.php';
    }

    /**
     * Get plugin class.
     *
     * @return Base\Plugin
     */
    public static function pluginClass()
    {
        return strtok( __NAMESPACE__, '\\' ) . '\Lib\Plugin';
    }
}