<?php
/**
 * Plugin Name: PublishPress Blocks
 * Plugin URI: https://publishpress.com/blocks/
 * Description: PublishPress Blocks has everything you need to build professional websites with the Gutenberg editor.
 * Version: 2.13.3
 * Tested up to: 6.0
 * Author: PublishPress
 * Author URI: https://publishpress.com/
 * License: GPL2
 * Text Domain: advanced-gutenberg
 * Domain Path: /languages
 */

/**
 * Copyright
 *
 * @copyright 2014-2020  Joomunited
 * @copyright 2020       Advanced Gutenberg. help@advancedgutenberg.com
 * @copyright 2020-2022  PublishPress. help@publishpress.com
 *
 *  Original development of this plugin was kindly funded by Joomunited
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

defined('ABSPATH') || die;

$includeFilebRelativePath = '/publishpress/publishpress-instance-protection/include.php';
if (file_exists(__DIR__ . '/vendor' . $includeFilebRelativePath)) {
    require_once __DIR__ . '/vendor' . $includeFilebRelativePath;
} else if (defined('PP_AUTHORS_VENDOR_PATH') && file_exists(PP_AUTHORS_VENDOR_PATH . $includeFilebRelativePath)) {
    require_once PP_AUTHORS_VENDOR_PATH . $includeFilebRelativePath;
}

if (class_exists('PublishPressInstanceProtection\\Config')) {
    $pluginCheckerConfig = new PublishPressInstanceProtection\Config();
    $pluginCheckerConfig->pluginSlug = 'advanced-gutenberg';
    $pluginCheckerConfig->pluginName = 'PublishPress Blocks';

    $pluginChecker = new PublishPressInstanceProtection\InstanceChecker($pluginCheckerConfig);
}

if (! defined('ADVANCED_GUTENBERG_LOADED')) {

    if (! defined('ADVANCED_GUTENBERG_VERSION')) {
        define('ADVANCED_GUTENBERG_VERSION', '2.13.3');
    }

    if (! defined('ADVANCED_GUTENBERG_PLUGIN')) {
        define('ADVANCED_GUTENBERG_PLUGIN', __FILE__);
    }

    // Code shared with Pro version
    require_once __DIR__ . '/init.php';

    // Vendor and Ask-for-Review
    if(
        file_exists(__DIR__ . '/vendor/autoload.php')
        && !defined('ADVANCED_GUTENBERG_VENDOR_LOADED')
        && is_admin()
        && !class_exists('PublishPress\WordPressReviews\ReviewsController')
    ) {
        require_once __DIR__ . '/vendor/autoload.php';
        define('ADVANCED_GUTENBERG_VENDOR_LOADED', true);

        // Ask for review
        if( file_exists(__DIR__ . '/review/review-request.php') ) {
            require_once __DIR__ . '/review/review-request.php';
        }
    }

    define('ADVANCED_GUTENBERG_LOADED', true);
}
