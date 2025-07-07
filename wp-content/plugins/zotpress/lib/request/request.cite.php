<?php

if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly


function Zotpress_get_cite_AJAX()
{
	check_ajax_referer( 'zpCite_nonce_val', 'zpCite_nonce' );
		
	// Include WordPress
	require(dirname(__FILE__) . '/../../../../../wp-load.php');
	define('WP_USE_THEMES', false);

	// Include Request Functionality
	require(__DIR__ . '/request.class.php');
	require(__DIR__ . '/request.functions.php');

	// Content prep
	$zp_xml = false;

	// item key
	if ( isset($_GET['item_key']) 
			&& preg_match("/^[a-zA-Z0-9]+$/", sanitize_text_field(wp_unslash($_GET['item_key']))) )
		$zp_item_key = trim(urldecode(sanitize_text_field(wp_unslash($_GET['item_key']))));
	else
		$zp_xml = "No item key provided.";

	// Api User ID
	if ( isset($_GET['api_user_id']) 
			&& preg_match("/^[a-zA-Z0-9]+$/", sanitize_text_field(wp_unslash($_GET['api_user_id']))) )
		$zp_api_user_id = trim(urldecode(sanitize_text_field(wp_unslash($_GET['api_user_id']))));
	else
		$zp_xml = "No API User ID provided.";


	if ( $zp_xml === false )
	{
		// Access WordPress db and filesystem
		global $wpdb, $wp_filesystem;

		if ( ! function_exists( 'WP_Filesystem' ) )
			require_once __DIR__ . '/wp-admin/includes/file.php';

		WP_Filesystem();

		// Get account
		$zp_account = zotpress_get_account ($wpdb, $zp_api_user_id);

		// 7.4 Update: Not needed
		// Build import structure
		// $zp_import_contents = new ZotpressRequest();
		$zp_import_url = "https://api.zotero.org/".$zp_account[0]->account_type."/".$zp_api_user_id."/items/".$zp_item_key."?format=ris&key=".$zp_account[0]->public_key;

		// 7.4 Update: Totally new approach
		header("Content-Type: application/x-research-info-systems");
		header('Content-Disposition: attachment; filename="itemkey-'.$zp_item_key.'.ris"');
		header('Content-Description: Cite with RIS');
		// @readfile($zp_import_url);
		$content = $wp_filesystem->get_contents( $zp_import_url );
		// 7.4.1: Trying sanitize
		$content = sanitize_textarea_field( $content );

		if ( $content !== false )
			// 7.4.1: Replacing var_dump after sanitizing
			echo $content;
		else
			echo 'Could not read the file';

		// // Read the external data
		// $zp_xml = $zp_import_contents->get_request_contents( $zp_import_url, true, 'ris' );

		// if ( $zp_xml !== false
		// 		&& strlen(trim($zp_xml["json"])) > 0 )
		// {
		// 	header('Content-Type: application/x-research-info-systems');
		// 	header('Content-Disposition: attachment; filename="itemkey-'.$zp_item_key.'.ris"');
		// 	header('Content-Description: Cite with RIS');
		// 	// echo $zp_xml["json"];
		// 	// 7.4: Shocked this works
		// 	var_dump($zp_xml["json"]);
		// }
		// else
		// {
		// 	echo "No RIS file found.";
		// }
	}
	else
	{
		// 7.4 Update: Going back to:
		// echo $zp_xml;
		// 7.4: The xml elements are a guess
		echo wp_kses(
			$zp_xml,
			array(
				'result' => array(
					'success' => array(),
					'item_key' => array(),
					'reset' => array(),
					'cpt' => array(),
					'api_user_id' => array(),
					'public_key' => array(),
					'total_accounts' => array(),
					'cache_cleared' => array(),
					'citation_id' => array(),
					'account' => array(),
					'accounts' => array(),
					'style' => array(),
				),
				'citation' => array(
					'id' => array(),
					'class' => array(),
					'style' => array(),
					'name' => array(),
				),
				'account' => array(
					'id' => array(),
					'type' => array(),
				),
				'errors' => array(),
			)
		);
		// // 7.4: Shocked this works
		// var_dump($zp_xml);
	}
}

add_action( 'wp_ajax_zpCiteViaAJAX', 'Zotpress_get_cite_AJAX' );
add_action( 'wp_ajax_nopriv_zpCiteViaAJAX', 'Zotpress_get_cite_AJAX' );

?>