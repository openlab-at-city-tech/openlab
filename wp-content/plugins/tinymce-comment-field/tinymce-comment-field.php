<?php
/*
  Plugin Name: TinyMCE Comment Field - WYSIWYG
  Plugin URI: https://wordpress.org/plugins/tinymce-comment-field/
  Description: This plugin turns the comment field from a primitive into a WYSIWYG editor, using the internal TinyMCE library bundled with WordPress.
  Version: 1.9.6
  Author: Stefan Helmer
  Author URI: http://www.eracer.de
 */
!defined('ABSPATH') and exit;

define('TMCECF_PLUGIN', plugin_basename(__FILE__));
define('TMCECF_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('TMCECF_PLUGIN_URL', plugin_dir_url(__FILE__));
define('TMCECF_PLUGIN_RELATIVE_DIR', dirname(plugin_basename(__FILE__)));
define('TMCECF_PLUGIN_FILE', __FILE__);

require_once('classes/class-buttons.php');
require_once('classes/class-tgm.php');
require_once('controller/class-plugin-controller.php');
require_once('controller/class-titan-controller.php');
require_once('controller/class-metabox-controller.php');
require_once('controller/class-editor-controller.php');
require_once('controller/class-comment-controller.php');
require_once('controller/class-dummy-controller.php');
require_once('manager/class.plugin-manager.php');

TMCECF_PluginController::init();