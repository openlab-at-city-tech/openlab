<?php
/**
 * Plugin Name: Table of Contents Plus
 * Plugin URI:  https://aioseo.com/
 * Description: A powerful yet user friendly plugin that automatically creates a table of contents. Can also output a sitemap listing all pages and categories.
 * Author:      All in One SEO Team
 * Author URI:  https://aioseo.com
 * Version:     202609.1
 * Requires at least: 5.7
 * Requires PHP: 7.4
 * Text Domain: table-of-contents-plus
 * Domain Path: /languages
 * License:     GPL-2.0+
 *
 * Table of Contents Plus is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Table of Contents Plus is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Table of Contents Plus. If not, see <https://www.gnu.org/licenses/>.
 *
 * @author    All in One SEO
 * @license   GPL-2.0+
 * @package   AIOSEO\TableOfContents
 * @copyright Copyright (c) 2026, All in One SEO
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'AIOSEO_TABLE_OF_CONTENTS_PHP_VERSION_DIR' ) ) {
	define( 'AIOSEO_TABLE_OF_CONTENTS_PHP_VERSION_DIR', basename( dirname( __FILE__ ) ) );
}

require_once dirname( __FILE__ ) . '/app/init/init.php';

// Check if this plugin should be disabled.
if ( aioseo_table_of_contents_is_plugin_disabled() ) {
	return;
}

require_once dirname( __FILE__ ) . '/app/init/notices.php';

// We require PHP 7.4 or higher for the whole plugin to work.
if ( version_compare( PHP_VERSION, '7.4', '<' ) ) {
	add_action( 'admin_notices', 'aioseo_plugin_php_notice' );

	// Do not process the plugin code further.
	return;
}

// We require WP 5.7+ for the whole plugin to work.
global $wp_version; // phpcs:ignore Squiz.NamingConventions.ValidVariableName
if ( version_compare( $wp_version, '5.7', '<' ) ) { // phpcs:ignore Squiz.NamingConventions.ValidVariableName
	add_action( 'admin_notices', 'aioseo_plugin_wordpress_notice' );

	// Do not process the plugin code further.
	return;
}

// Plugin constants.
if ( ! defined( 'AIOSEO_TABLE_OF_CONTENTS_DIR' ) ) {
	define( 'AIOSEO_TABLE_OF_CONTENTS_DIR', __DIR__ );
}
if ( ! defined( 'AIOSEO_TABLE_OF_CONTENTS_FILE' ) ) {
	define( 'AIOSEO_TABLE_OF_CONTENTS_FILE', __FILE__ );
}

// Define the class and the function.
require_once dirname( __FILE__ ) . '/app/TableOfContents.php';

aioseoTableOfContents();