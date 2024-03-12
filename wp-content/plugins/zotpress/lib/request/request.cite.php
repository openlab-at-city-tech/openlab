<?php

	// Include WordPress
	// require('../../../../../wp-load.php');
	require(dirname(__FILE__) . '/../../../../../wp-load.php');
	define('WP_USE_THEMES', false);

	// Include Request Functionality
	require(__DIR__ . '/request.class.php');
	require(__DIR__ . '/request.functions.php');

	// Content prep
	$zp_xml = false;


	// item key
	if (isset($_GET['item_key']) && preg_match("/^[a-zA-Z0-9]+$/", $_GET['item_key']))
		$zp_item_key = trim(urldecode($_GET['item_key']));
	else
		$zp_xml = "No item key provided.";

	// Api User ID
	if (isset($_GET['api_user_id']) && preg_match("/^[a-zA-Z0-9]+$/", $_GET['api_user_id']))
		$zp_api_user_id = trim(urldecode($_GET['api_user_id']));
	else
		$zp_xml = "No API User ID provided.";


	// Get cite data from Zotero
	if ($zp_xml === false)
	{
		// Access WordPress db
		global $wpdb;

		// Get account
		$zp_account = zp_get_account ($wpdb, $zp_api_user_id);

		// Build import structure
		$zp_import_contents = new ZotpressRequest();
		$zp_import_url = "https://api.zotero.org/".$zp_account[0]->account_type."/".$zp_api_user_id."/items/".$zp_item_key."?format=ris&key=".$zp_account[0]->public_key;

		// Read the external data
        $zp_xml = $zp_import_contents->get_request_contents( $zp_import_url, true, 'ris' );

		if ( $zp_xml !== false
				&& strlen(trim($zp_xml["json"])) > 0 )
		{
			header('Content-Type: application/x-research-info-systems');
			header('Content-Disposition: attachment; filename="itemkey-'.$zp_item_key.'.ris"');
			header('Content-Description: Cite with RIS');
			echo $zp_xml["json"];
		}
		else
		{
			echo "No RIS file found.";
		}
	}
	else
	{
		echo $zp_xml;
	}
?>
