<?php
/**
 * Plugin Name: GP Media Library
 * Description: Upload files from a Gravity Forms File Upload field to the WordPress media library.
 * Plugin URI: https://gravitywiz.com/documentation/gravity-forms-media-library/
 * Version: 1.2.17
 * Author: Gravity Wiz
 * Author URI: http://gravitywiz.com/
 * License: GPL2
 * Perk: True
 * Text Domain: gp-media-library
 * Domain Path: /languages
 */

define( 'GP_MEDIA_LIBRARY_VERSION', '1.2.17' );

require 'includes/class-gp-bootstrap.php';

$gp_media_library_bootstrap = new GP_Bootstrap( 'class-gp-media-library.php', __FILE__ );
