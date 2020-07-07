<?php

if (!function_exists('analyst_assets_path')) {
	/**
	 * Generates path to file in assets folder
	 *
	 * @param $file
	 * @return string
	 */
	function analyst_assets_path($file)
	{
		$path = sprintf('%s/assets/%s', realpath(__DIR__ . '/..'), trim($file, '/'));

		return wp_normalize_path($path);
	}
}


if (!function_exists('analyst_assets_url')) {
	/**
	 * Generates url to file in assets folder
	 *
	 * @param $file
	 * @return string
	 */
	function analyst_assets_url($file)
	{
		$absolutePath = analyst_assets_path($file);

		// We can always rely on WP_PLUGIN_DIR, because that's where
		// wordpress install it's plugin's. So we remove last segment
		// of that path to get the content dir AKA directly where
		// plugins are installed and make the magic...
		$contentDir = is_link(WP_PLUGIN_DIR) ?
			dirname(wp_normalize_path(readlink(WP_PLUGIN_DIR))) : dirname(wp_normalize_path(WP_PLUGIN_DIR));

		$relativePath = str_replace($contentDir, '', $absolutePath);

		return content_url(wp_normalize_path($relativePath));
	}
}

if (!function_exists('analyst_templates_path')) {
	/**
	 * Generates path to file in templates folder
	 *
	 * @param $file
	 * @return string
	 */
	function analyst_templates_path($file)
	{
		$path = sprintf('%s/templates/%s', realpath(__DIR__ . '/..'), trim($file, '/'));

		return wp_normalize_path($path);
	}
}

if (!function_exists('analyst_require_template')) {
	/**
	 * Require certain template with data
	 *
	 * @param $file
	 * @param array $data
	 */
	function analyst_require_template($file, $data = [])
	{
		// Extract data to current scope table
		extract($data);

		require analyst_templates_path($file);
	}
}

if (!function_exists('dd')) {
	/**
	 * Dump some data
	 */
	function dd()
	{
		// var_dump(func_get_args());
		die();
	}
}



// function sfsi_check_plugin_is_active($dir_slug, $option_name, $site_url)
// {


// 	// var_dump($plugin_list);
// 	$is_active_gallery_plugin = array();
// 	foreach ($plugin_list as $key => $plugin) {
// 		var_dump($plugin);
// 		$is_active_gallery_plugin[$key] = is_plugin_active($plugin_list);
// 	}
// 	if(in_array(true, $is_active_gallery_plugin)){
// 		return true;
// 	}
// }

// function sfsi_check_on_plugin_page($dir_slug, $option_name, $site_url="")
// {
//     var_dump('in helper');

// 	return is_plugin_active($dir_slug) && isset($_GET) && isset($_GET["page"]) && ($_GET['page']==$option_name);
// }


function sfsi_plugin_waiting_time($option)
{

	if (isset($option['show_banner']) && $option['show_banner'] == "no" || isset($option['timestamp']) && !empty($option['timestamp'])) {
		$sfsi_banner_timestamp = strtotime($option['timestamp']);
		$sfsi_show_banner_timestamp = $sfsi_banner_timestamp + (21 * 24 * 60 * 60);
		if (time() >= $sfsi_show_banner_timestamp) {
			return true;
		}
		return false;
	}
	return false;
}

function sfsi_wp_img_count()
{
	$query_img_args = array(
		'post_type' => 'attachment',
		'post_mime_type' => array(
			'jpg|jpeg|jpe' => 'image/jpeg',
			'gif' => 'image/gif',
			'png' => 'image/png',
		),
		'post_status' => 'inherit',
		'posts_per_page' => -1,
	);
	$query_img = new WP_Query($query_img_args);
	return $query_img->post_count;
}

function sfsi_check_pinterest_icon_placed()
{
	$sfsi_section1 		   =  unserialize(get_option('sfsi_section1_options', false));
	if ($sfsi_section1['sfsi_pinterest_display'] == 'yes') {
		return true;
	}
	return false;
}
