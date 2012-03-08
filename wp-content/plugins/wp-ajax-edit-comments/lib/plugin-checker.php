<?php
if (!headers_sent()) { header('Content-Type: text/html'); }
define('WP_INSTALLING', true);
$root = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
if (file_exists($root.'/wp-load.php')) {
		// WP 2.6
		require_once($root.'/wp-load.php');
} else {
		// Before 2.6
		require_once($root.'/wp-config.php');
}
$plugin = 'ajax-edit-comments/wp-ajax-edit-comments.php';
// Validate plugin filename
if ( !validate_file($plugin) && '.php' == substr($plugin, -4) && file_exists(WP_PLUGIN_DIR . '/' . $plugin)) {
	include_once(WP_PLUGIN_DIR . '/' . $plugin);
	//* Begin Localization Code */
	//* Localization Code */
	load_plugin_textdomain('ajaxEdit', false, 'ajax-edit-comments/languages');
}
unset($plugin);
include_once(ABSPATH . 'wp-includes/pluggable.php');
do_action( 'plugins_loaded' );
?>