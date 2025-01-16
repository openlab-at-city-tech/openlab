<?php
/**
 * Plugin Name:       OpenLab Attributions
 * Plugin URI:        https://openlab.citytech.cuny.edu/
 * Description:       Add formatted attributions to post content.
 * Version:           2.1.4
 * Requires at least: 5.4
 * Requires PHP:      5.6
 * Author:            OpenLab
 * Author URI:        https://openlab.citytech.cuny.edu/
 * License:           GPL-3.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       openlab-attributions
 * Domain Path:       /languages
 */

namespace OpenLab\Attributions;

const ROOT_DIR  = __DIR__;
const ROOT_FILE = __FILE__;

require_once __DIR__ . '/src/helpers.php';
require_once __DIR__ . '/src/content.php';
require_once __DIR__ . '/src/meta.php';
require_once __DIR__ . '/src/assets.php';
