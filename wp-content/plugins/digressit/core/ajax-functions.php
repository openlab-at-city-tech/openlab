<?php
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'ajax-functions.php' == basename($_SERVER['SCRIPT_FILENAME'])):
	die (':)');
endif;

add_action('init', 'ajax_flush_rewrite_rules', 0 );
add_filter('query_vars', 'ajax_query_vars', 0);
add_action('generate_rewrite_rules', 'ajax_add_rewrite_rules', 0 );
add_action('template_redirect', 'ajax_template' );

//add_action('wp_ajax_my_action', 'my_action_callback');
//add_action('wp_ajax_nopriv_my_action', 'my_action_callback');



/**
 * Flush your rewrite rules if you want pretty permalinks
 */
function ajax_flush_rewrite_rules() {
	global $wp_rewrite;
	$wp_rewrite->flush_rules();
}

/**
 * Create some extra variables to accept when passed through the url
 */
function ajax_query_vars( $query_vars ) {
	$myvars = array( 'inc_ajax' );
	$query_vars = array_merge( $query_vars, $myvars );
	return $query_vars;
}


/**
 * Create a rewrite rule if you want pretty permalinks
 */
function ajax_add_rewrite_rules( $wp_rewrite ) {

	$wp_rewrite->add_rewrite_tag( "%inc_ajax%", "([^/]+)", "inc_ajax=" );

	$urls = array( "ajax/%inc_ajax%" );
	foreach( $urls as $url ) {
		$rule = $wp_rewrite->generate_rewrite_rules($url, EP_NONE, false, false, false, false, false);
		$wp_rewrite->rules = array_merge( $rule, $wp_rewrite->rules );
	}
	return $wp_rewrite;
}

/**
 * Let's echo out the content we are looking to dynamically grab before we load any template files
 */
function ajax_template() {
	global $wp, $wpdb;
	global $current_user;

	if(isset( $wp->query_vars['inc_ajax'] ) && !empty($wp->query_vars['inc_ajax'] ) ):

		$request_action = str_replace('-','_',$wp->query_vars['inc_ajax'])."_ajax"; 
		$request_params = $_REQUEST;

		$comment_id = $request_params['comment_id'];
		$status = null;
		$message = null;

		status_header( 200 );	
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Content-type: application/json');

		if(function_exists($request_action)){
			if(has_action('public_ajax_function', $request_action)) {
				if(has_action('public_ajax_function', $request_action)) {
					call_user_func($request_action, $request_params);
				}
			}
		
		    if( is_user_logged_in()){
				if(has_action('authenticated_ajax_function', $request_action)) {
					call_user_func($request_action, $request_params);
				}
			}
			else{  //user is not logged in
				if(has_action('unauthenticated_ajax_function', $request_action)) {
					call_user_func($request_action, $request_params);
				}
			}
			die();
		}

	endif;
}
?>