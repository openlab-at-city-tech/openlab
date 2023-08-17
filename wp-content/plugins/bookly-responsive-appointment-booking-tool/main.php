<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
/*
Plugin Name: Bookly
Plugin URI: https://www.booking-wp-plugin.com/?utm_source=bookly_admin&utm_medium=plugins_page&utm_campaign=plugins_page
Description: Bookly Plugin – is a great easy-to-use and easy-to-manage booking tool for service providers who think about their customers. The plugin supports a wide range of services provided by business and individuals who offer reservations through websites. Set up any reservation quickly, pleasantly and easily with Bookly!
Version: 21.8
Author: Bookly
Author URI: https://www.booking-wp-plugin.com/?utm_source=bookly_admin&utm_medium=plugins_page&utm_campaign=plugins_page
Text Domain: bookly
Domain Path: /languages
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

if ( version_compare( PHP_VERSION, '5.3.7', '<' ) ) {
    function bookly_php_outdated()
    {
        echo '<div class="updated"><h3>Bookly</h3><p>To install the plugin - <strong>PHP 5.3.7</strong> or higher is required.</p></div>';
    }

    add_action( is_network_admin() ? 'network_admin_notices' : 'admin_notices', 'bookly_php_outdated' );
} else {
    include_once __DIR__ . '/autoload.php';

    call_user_func( array( 'Bookly\Lib\Boot', 'up' ) );
}