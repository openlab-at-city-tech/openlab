<?php
include_once('afg_libs.php');
if (false) {
    // TEMP: Enable update check on every request. Normally you don't need 
    // this! This is for testing only!
    set_site_transient('update_plugins', null);

    // TEMP: Show which variables are being requested when query plugin API
    add_filter('plugins_api_result', 'afg_aaa_result', 10, 3);
    function afg_aaa_result($res, $action, $args) {
        print_r($res);
        return $res;
    }
}

$api_url = 'http://www.ronakg.com/update_plugin/';
$package_type = 'stable';
$plugin_slug = basename(dirname(__FILE__));
$plugin_slug_file = 'index.php';

// Take over the update check
add_filter('pre_set_site_transient_update_plugins', 
    'afg_check_for_plugin_update');

define ('AFG_SSL_VERIFY', FALSE);
  
add_action('http_request_args', 'afg_ssl_verify', 10, 2);
function afg_ssl_verify($args, $url) {
    $args['sslverify'] = AFG_SSL_VERIFY;
    return $args;
}

function afg_check_for_plugin_update($checked_data) {
    global $api_url, $plugin_slug, $plugin_slug_file, $package_type;
	
    if (empty($checked_data->checked))
        return $checked_data;
	
    $request_args = array(
        'slug' => $plugin_slug,
        'version' => $checked_data->checked[$plugin_slug .'/'. $plugin_slug_file],
        'package_type' => $package_type,
    );

    $request_string = afg_prepare_request('basic_check', $request_args);
	
    // Start checking for an update
    $raw_response = wp_remote_post($api_url, $request_string);

    if (!is_wp_error($raw_response) && ($raw_response['response']['code'] == 200)) {
        $response = unserialize($raw_response['body']);

        if (is_object($response) && !empty($response)) // Feed the update data into WP updater
            $checked_data->response[$plugin_slug .'/'. $plugin_slug_file] = $response;
    }
	
    return $checked_data;
}

// Take over the Plugin info screen
add_filter('plugins_api', 'my_plugin_api_call', 10, 3);

function my_plugin_api_call($def, $action, $args) {
    global $plugin_slug, $api_url, $plugin_slug_file, $package_type;
	
    if ($args->slug != $plugin_slug)
        return false;
	
    // Get the current version
    $plugin_info = get_site_transient('update_plugins');
    $current_version = $plugin_info->checked[$plugin_slug .'/'. $plugin_slug_file];
    $args->version = $current_version;
    $args->package_type = $package_type;
	
    $request_string = afg_prepare_request($action, $args);
	
    $request = wp_remote_post($api_url, $request_string);
	
    if (is_wp_error($request)) {
        $res = new WP_Error('plugins_api_failed', __('An Unexpected HTTP Error occurred during the API request.</p> <p><a href="?" onclick="document.location.reload(); return false;">Try again</a>'), $request->get_error_message());
    } else {
        $res = unserialize($request['body']);
		
        if ($res === false)
            $res = new WP_Error('plugins_api_failed', __('An unknown error occurred'), $request['body']);
    }
	
    return $res;
}

function afg_prepare_request($action, $args) {
    global $wp_version;
    $site_url = site_url();

    $wp_info = array(
        'site-url' => $site_url,
        'version' => $wp_version,
    );

    return array(
        'body' => array(
            'action' => $action, 'request' => serialize($args),
            'api-key' => md5($site_url),
            'wp-info' => serialize($wp_info),
        ),
        'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo('url')
    );
}
?>
