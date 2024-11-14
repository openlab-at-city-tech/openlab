<?php
/**
 * Plugin Name:       OpenLab EXIF tools
 * Plugin URI:        https://openlab.citytech.cuny.edu/
 * Description:
 * Version:           1.0.0-alpha
 * Requires at least: 6.0
 * Requires PHP:      7.3
 * Author:            OpenLab at City Tech
 * Author URI:        https://openlab.citytech.cuny.edu/
 * License:           GPL-3.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       openlab-modules
 * Domain Path:       /languages
 *
 * @package openlab-modules
 */

namespace OpenLab\EXIF;

const ROOT_DIR  = __DIR__;
const ROOT_FILE = __FILE__;

require ROOT_DIR . '/vendor/autoload.php';

add_action(
	'plugins_loaded',
	function () {
		App::init();
	}
);
