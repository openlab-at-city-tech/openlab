<?php
/**
 * Plugin Name: OpenLab Attributions.
 * Plugin URI:  https://openlab.citytech.cuny.edu/
 * Description: Content attribution feature.
 * Author:      OpenLab
 * Author URI:  https://openlab.citytech.cuny.edu/
 * Text Domain: openlab-attributions
 * Domain Path: /languages
 * Version:     1.0.0
 */

namespace OpenLab\Attributions;

const ROOT_DIR  = __DIR__;
const ROOT_FILE = __FILE__;

require_once __DIR__ . '/src/helpers.php';
require_once __DIR__ . '/src/settings.php';
require_once __DIR__ . '/src/meta.php';
require_once __DIR__ . '/src/shortcode.php';
require_once __DIR__ . '/src/content.php';
require_once __DIR__ . '/src/media.php';
require_once __DIR__ . '/src/assets.php';

// Register shortcode hooks.
( new Shortcode\References() )->register();
