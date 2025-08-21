<?php 

if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly 


function Zotpress_get_dl_AJAX()
{
	check_ajax_referer( 'zpDL_nonce_val', 'zpDL_nonce' );
	
	// Include WordPress
	require(dirname(__FILE__) . '/../../../../../wp-load.php');
	define('WP_USE_THEMES', false);

	// Include Request Functionality
	require(__DIR__ . '/request.class.php');
	require(__DIR__ . '/request.functions.php');

	// Content prep
	$zp_data_xml = false;

	// Download Key
	if ( isset($_GET['dlkey']) 
			&& preg_match("/^[a-zA-Z0-9]{3,40}$/", sanitize_text_field(wp_unslash($_GET['dlkey']))) )
		$zp_item_key = trim(urldecode(sanitize_text_field(wp_unslash($_GET['dlkey']))));
	else
		$zp_data_xml = "No key provided, or format incorrect.";

	// Api User ID
	if ( isset($_GET['api_user_id']) 
			&& preg_match("/^[a-zA-Z0-9]{3,15}$/", sanitize_text_field(wp_unslash($_GET['api_user_id']))) )
		$zp_api_user_id = trim(urldecode(sanitize_text_field(wp_unslash($_GET['api_user_id']))));
	else
		$zp_data_xml = "No API User ID provided, or format incorrect.";

	// Content type
	if ( isset($_GET['content_type']) 
			&& preg_match("/^[a-zA-Z0-9\/]{3,40}$/", sanitize_text_field(wp_unslash($_GET['content_type']))) )
		$zp_content_type = trim(urldecode(sanitize_text_field(wp_unslash($_GET['content_type']))));
	else
		$zp_data_xml = "No content type provided, or format incorrect.";


	if ( $zp_data_xml === false )
	{
		// Access WordPress db and filesystem
		global $wpdb, $wp_filesystem;

		if ( ! function_exists( 'WP_Filesystem' ) )
			require_once __DIR__ . '/wp-admin/includes/file.php';

		WP_Filesystem();

		// Get account
		$zp_account = zotpress_get_account ($wpdb, $zp_api_user_id);

		// 7.4: Not needed
		// Build import structure
		// $zp_import_filemeta = new ZotpressRequest();
		// $zp_import_filedata = new ZotpressRequest();

		// Build API URLs
		$zp_import_baseurl = "https://api.zotero.org/".$zp_account[0]->account_type."/".$zp_api_user_id."/items/".$zp_item_key;
		// $zp_import_meta_url = $zp_import_baseurl."?key=".$zp_account[0]->public_key;
		$zp_import_data_url = $zp_import_baseurl."/file/view?key=".$zp_account[0]->public_key;

		// 7.4: Not needed
		// // Read the external data
		// $zp_meta_xml = $zp_import_filemeta->get_request_contents( $zp_import_meta_url, true ); // Unsure about "true"
		// $zp_data_xml = $zp_import_filedata->get_request_contents( $zp_import_data_url, true ); // Unsure about "true"

		// Determine filename based on content type
		// $zp_filename = "download-".$zp_item_key.".";
		// if ( strpos( $zp_content_type, "pdf" ) ) $zp_filename .= "pdf";
		// else if ( strpos( $zp_content_type, "wordprocessingml.document" ) ) $zp_filename .= "docx";
		// else if ( strpos( $zp_content_type, "msword" ) ) $zp_filename .= "doc";
		// else if ( strpos( $zp_content_type, "latex" ) ) $zp_filename .= "latex";
		// else if ( strpos( $zp_content_type, "presentationml.presentation" ) ) $zp_filename .= "pptx";
		// else if ( strpos( $zp_content_type, "ms-powerpointtd" ) ) $zp_filename .= "ppt";
		// else if ( strpos( $zp_content_type, "rtf" ) ) $zp_filename .= "rtf";
		// else if ( strpos( $zp_content_type, "opendocument.text" ) ) $zp_filename .= "odt";
		// else if ( strpos( $zp_content_type, "opendocument.presentation" ) ) $zp_filename .= "odp";

		// 7.4: Not needed
		// if ( $zp_meta_xml !== false
		// 		&& $zp_data_xml !== false
		// 		&& strlen(trim($zp_meta_xml["json"])) > 0
		// 		&& strlen(trim($zp_data_xml["json"])) > 0 )
		// {
			// $zp_meta_xml_json = json_decode($zp_meta_xml["json"]);

			// $zp_meta_xml_arr[] = array( "filetype" => $zp_content_type, "filename" => $zp_meta_xml_json[0]->data->filename );
		
			// $zp_data_xml = wp_json_encode($zp_meta_xml_arr);
			// echo wp_kses_post($zp_data_xml);


			// 7.4 Update: Totally new approach
			header("Content-Type:".$zp_content_type);
			// 7.4 Update: Not worth the request
			// header("Content-Disposition:inline;filename=".$zp_meta_xml_json[0]->data->filename);
			header("Content-Disposition:inline;filename=download".explode('/', $zp_content_type)[1]);
			// @readfile($zp_import_data_url);
			$content = $wp_filesystem->get_contents( $zp_import_data_url );

			if ( $content !== false )
				var_dump( $content );
			else
				echo 'Could not read the file';


			// OLD ------------------------------------->
			// header("Content-Type:".$zp_content_type);
			// header("Content-Disposition:inline;filename=".$zp_meta_xml_json[0]->data->filename);
			// // echo $zp_data_xml["json"];
			// // 7.4: I'm shocked this works ... for both PCP and viewing
			// var_dump($zp_data_xml["json"]);
		// }
		// else {
		// 	$zp_data_xml = "No file found.";
		// }
	}
	else {
		// echo $zp_data_xml;
		// 7.4: The xml elements are a guess
		echo wp_kses(
			$zp_data_xml,
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
	}
	
	$wpdb->flush();

	exit();
}

add_action( 'wp_ajax_zpDLViaAJAX', 'Zotpress_get_dl_AJAX' );
add_action( 'wp_ajax_nopriv_zpDLViaAJAX', 'Zotpress_get_dl_AJAX' );

?>