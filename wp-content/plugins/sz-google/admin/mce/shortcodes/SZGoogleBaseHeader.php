<?php

/**
 * Script to implement the HTML code shared with widgets 
 * in the function pop-up insert shortcodes via GUI
 *
 * @package SZGoogle
 * @subpackage Admin
 * @author Massimo Della Rovere
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined('SZ_PLUGIN_GOOGLE') or !SZ_PLUGIN_GOOGLE) die();

// Definition and initialization array that 
// will be used for creating automatic variables

$variables = array();

// Reading array creation and identification name 
// with the prefix conventional ID_ name_ VALUE_

foreach($array as $item=>$value) 
{
	$PREFIX_I = 'ID_'   .$item;
	$PREFIX_N = 'NAME_' .$item;
	$PREFIX_V = 'VALUE_'.$item;

	$variables[$PREFIX_I] = $PREFIX_I;
	$variables[$PREFIX_N] = $PREFIX_N;
	$variables[$PREFIX_V] = esc_attr(${$item});
}

// Extracting array to create variables with 
// specified name and value associated with the key

extract($variables,EXTR_OVERWRITE);

// Add style and Javascript plugin that is used to 
// manage the FORM both graphical and functional

function sz_google_ajax_load_scripts() 
{
	wp_enqueue_style('sz-google-style-admin',
		plugin_dir_url(SZ_PLUGIN_GOOGLE_MAIN).'admin/files/css/sz-google-style-admin.css',
		array(),SZ_PLUGIN_GOOGLE_VERSION
	);

	wp_enqueue_script('sz-google-javascript-widgets',
		plugin_dir_url(SZ_PLUGIN_GOOGLE_MAIN).'admin/files/js/jquery.szgoogle.widgets.js',
		array('jquery'),SZ_PLUGIN_GOOGLE_VERSION,false
	);

	wp_enqueue_script('tiny_mce_popup',
		includes_url('js/tinymce/tiny_mce_popup.js'),
		array('jquery'),SZ_PLUGIN_GOOGLE_VERSION,false
	);

	wp_enqueue_script('tiny_mce_component',
		plugin_dir_url(SZ_PLUGIN_GOOGLE_MAIN).'admin/mce/js/'.SZGOOGLE_AJAX_NAME.'.js',
		array('tiny_mce_popup'),SZ_PLUGIN_GOOGLE_VERSION,false
	);
}

// Add a class to the <BODY> section to indicate the 
// CSS rules and adapt some parts of the pop-up window

function sz_google_ajax_body_classes($classes) {
	return $classes.'SZMCE';
}

// I define the page title based on the value 
// specified in the description javascript call

function sz_google_ajax_title($admin_title,$title) {
	return $_GET['title'];
}

// I add filters and actions to customize,
// the standard ADMIN with plugin components

add_filter('admin_title','sz_google_ajax_title',99,2);
add_filter('admin_body_class','sz_google_ajax_body_classes'); 
add_action('admin_enqueue_scripts','sz_google_ajax_load_scripts');

if (!did_action('wp_enqueue_media')) wp_enqueue_media();

// Loading Header common part of the administration in 
// such a way to load the styles that are used to FORM

require(ABSPATH.'wp-admin/admin-header.php');

// Opening the FORM to contain the parameters that must be
// specified in the shortcode that we would go to compose OK

echo "<form id=\"MCE\" action=\"javascript:void(0);\" method=\"post\">\n";