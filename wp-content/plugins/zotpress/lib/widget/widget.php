<?php


/**
 * Adds a Widget Meta Box to the editor pages.
 * We call it "Zotpress Reference."
 */


function Zotpress_add_meta_box()
{
    $zp_default_cpt = "post,page";
    if (get_option("Zotpress_DefaultCPT"))
        $zp_default_cpt = get_option("Zotpress_DefaultCPT");
    $zp_default_cpt = explode(",",$zp_default_cpt);

    foreach ($zp_default_cpt as $post_type )
    {
        add_meta_box(
            'ZotpressMetaBox',
            __( 'Zotpress Reference', 'zotpress' ),
            'ZotpressMetaBox_callback',
            $post_type,
            'side',
            'high'
            // array(
            //     '__back_compat_meta_box' => true,
            // )
        );
    }
}
add_action('admin_init', 'Zotpress_add_meta_box', 1); // backwards compatible

function ZotpressMetaBox_callback() { require( dirname(__FILE__) . '/widget.metabox.php'); }



// Set up Widget Metabox AJAX search
function Zotpress_widget_metabox_AJAX_search()
{
	global $wpdb;

	// Determine account based on passed account
    if ($_GET['api_user_id'] && is_numeric($_GET['api_user_id'])) {
        $zp_account = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."zotpress WHERE api_user_id='".$_GET['api_user_id']."'", OBJECT);
        $zp_api_user_id = $zp_account->api_user_id;
        $zp_nickname = $zp_account->nickname;
    } elseif (get_option("Zotpress_DefaultAccount")) {
        $zp_api_user_id = get_option("Zotpress_DefaultAccount");
        $zp_account = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."zotpress WHERE api_user_id='".$zp_api_user_id."'", OBJECT);
        $zp_nickname = $zp_account->nickname;
    } else
  		{
  			$zp_account = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."zotpress LIMIT 1", OBJECT);
  			$zp_api_user_id = $zp_account->api_user_id;
              $zp_nickname = $zp_account->nickname;
  		}

	$zpSearch = array();

	// Include relevant classes and functions
	include( dirname(__FILE__) . '/../request/request.class.php' );
	include( dirname(__FILE__) . '/../request/request.functions.php' );

	// Set up Zotpress request
	$zp_import_contents = new ZotpressRequest();

	// Get account
	$zp_account = zp_get_account ($wpdb, $zp_api_user_id);

	// Format Zotero request URL
	// e.g., https://api.zotero.org/users/#####/items?key=###&format=json&q=###&limit=25
	$zp_import_url = "https://api.zotero.org/".$zp_account[0]->account_type."/".$zp_account[0]->api_user_id."/items?";
	if (!is_null($zp_account[0]->public_key) && trim($zp_account[0]->public_key) != "")
		$zp_import_url .= "key=".$zp_account[0]->public_key."&";
	$zp_import_url .= "format=json&q=".urlencode($_GET['term'])."&limit=10&itemType=-attachment+||+note";

	// Read the external data
	$zp_xml = $zp_import_contents->get_request_contents( $zp_import_url, true ); // Unsure about "true"
	$zpResultJSON = json_decode( $zp_xml["json"] );

	if ( count($zpResultJSON) > 0 )
	{
		foreach ( $zpResultJSON as $zpResult )
		{
			// Deal with author(s)
			$author = "N/A";
			if ( property_exists($zpResult->data, 'creators') && $zpResult->data->creators !== null )
			{
				$author = "";
				foreach ( $zpResult->data->creators as $i => $creator)
				{
					if ( property_exists($creator, 'name') && $creator->name !== null )
						$author .= $creator->name;
					else
						$author .= $creator->lastName;

					if ( $i != count($zpResult->data->creators)-1 ) $author .= ', ';
				}
			}

			// Deal with label
			// e.g., (year). title
			$label = " (";
			if ( property_exists($zpResult->data, 'date') && $zpResult->data->date !== null && trim($zpResult->data->date) != "" ) $label .= $zpResult->data->date; else $label .= "n.d.";
			$label .= "). ";
			$title = "Untitled."; if ( property_exists($zpResult->data, 'title') && $zpResult->data->title !== null && trim($zpResult->data->title) != "" ) $title = $zpResult->data->title . ".";

			// If no author, use title
			if ( trim($author) == "" )
			{
				$author = $title;
				$title = "";
			}
			$label .= $title;

			$zpSearch[] = array( "api_user_id" => $zp_api_user_id, "nickname" => $zp_nickname, "author" => $author, "label" => $label, "value" => $zpResult->key);
		}
	}

	unset($zp_import_contents);
	unset($zp_import_url);
	unset($zp_xml);


	$response = json_encode($zpSearch);
	echo $response;

	unset($zp_api_user_id);
	unset($zp_account);
	$wpdb->flush();

	exit();
}
add_action( 'wp_ajax_zpWidgetMetabox-submit', 'Zotpress_widget_metabox_AJAX_search' );



// Set relevant admin-level Widget Metabox scripts
function Zotpress_zpWidgetMetabox_scripts_css($hook)
{
    // Turn on/off minified versions if testing/live
    $minify = ''; if ( ZOTPRESS_LIVEMODE ) $minify = '.min';

    if ( in_array( $hook, array('post.php', 'post-new.php') ) )
    {
        wp_enqueue_script( 'jquery.livequery.min.js', ZOTPRESS_PLUGIN_URL . 'js/jquery.livequery.min.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-position', 'jquery-ui-tabs', 'jquery-ui-autocomplete' ) );
        wp_enqueue_script( 'zotpress.widget.metabox'.$minify.'.js', ZOTPRESS_PLUGIN_URL . 'js/zotpress.widget.metabox'.$minify.'.js', array( 'jquery', 'jquery-form', 'json2' ) );

		wp_localize_script(
			'zotpress.widget.metabox'.$minify.'.js',
			'zpWidgetMetabox',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'zpWidgetMetabox_nonce' => wp_create_nonce( 'zpWidgetMetabox_nonce_val' ),
				'action' => 'zpWidgetMetabox-submit',
                'txt_typetosearch' => __( 'Type to search', 'zotpress' ),
                'txt_pages' => __( 'Page(s)', 'zotpress' ),
                'txt_itemkey' => __( 'Item Key', 'zotpress' ),
                'txt_account' => __( 'Account', 'zotpress' )
			)
		);
    }
}
add_action( 'admin_enqueue_scripts', 'Zotpress_zpWidgetMetabox_scripts_css' );



/**
* Metabox styles
*/
function Zotpress_admin_post_styles()
{
    // Turn on/off minified versions if testing/live
    $minify = ''; if ( ZOTPRESS_LIVEMODE ) $minify = '.min';

    wp_register_style('zotpress.metabox'.$minify.'.css', ZOTPRESS_PLUGIN_URL . 'css/zotpress.metabox'.$minify.'.css', array(), rand(111,9999), 'all');
    wp_enqueue_style('zotpress.metabox'.$minify.'.css');

    wp_enqueue_style('jquery-ui-tabs', ZOTPRESS_PLUGIN_URL . 'css/smoothness/jquery-ui-1.8.11.custom'.$minify.'.css', array(), rand(111,9999), 'all');
}
add_action('admin_print_styles-post.php', 'Zotpress_admin_post_styles');
add_action('admin_print_styles-post-new.php', 'Zotpress_admin_post_styles');


?>
