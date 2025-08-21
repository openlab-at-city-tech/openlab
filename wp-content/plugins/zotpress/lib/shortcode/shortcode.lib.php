<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 


require(__DIR__ . '/shortcode.class.lib.php');

function Zotpress_zotpressLib( $atts )
{
    $atts = shortcode_atts( array(
    // extract( shortcode_atts( array(

        'user_id' => false, // deprecated
        'userid' => false,
        'nickname' => false,
        'nick' => false,

		'type' => false, // dropdown, searchbar
		'searchby' => false, // searchbar only - all [default], collections, items, tags
		'minlength' => 3, // searchbar only - 3 [default]
		'maxresults' => 50,
		'maxperpage' => 10,
		'maxtags' => 100, // dropdown only

		'sortby' => 'default',
		'order' => 'asc',

        'collection_id' => false,
        'collection' => false,
        'collections' => false, // only single for now, though

		'style' => false,
		'cite' => false,
		'citeable' => false,
		'download' => false,
		'downloadable' => false,
		'showimage' => false,
		'showimages' => false,
		'showtags' => false, // not implemented
		'abstract' => false, // not implemented
		'notes' => false, // not implemented
		'forcenumber' => false, // not implemented

        'toplevel' => 'toplevel',

		'target' => false,
		'urlwrap' => false,

        'browsebar' => true // added 7.3.1

    ), $atts );
    // ), $atts, "zotpress"));


    // +---------------------------+
    // | FORMAT & CLEAN PARAMETERS |
    // +---------------------------+

    global $wpdb;

    // 7.3.11: Use the Zotpress_prep_ajax_request_vars() function
    $zpr = Zotpress_prep_ajax_request_vars($wpdb, $atts, true);

    // // Filter by account
    // if ($user_id) {
    //     $api_user_id = str_replace('"','',html_entity_decode($user_id));
    // } elseif ($userid) {
    //     $api_user_id = str_replace('"','',html_entity_decode($userid));
    // } else $api_user_id = false;

    // if ($nickname) $nickname = str_replace('"','',html_entity_decode($nickname));
    // if ($nick) $nickname = str_replace('"','',html_entity_decode($nick));


	// // Type of display
	// $type = $type ? str_replace('"','',html_entity_decode($type)) : "dropdown";

    // // Filter by collection
    // if ($collection_id) {
    //     $collection_id = zotpress_clean_param( $collection_id );
    // } elseif ($collection) {
    //     $collection_id = zotpress_clean_param( $collection );
    // } elseif ($collections) {
    //     $collection_id = zotpress_clean_param( $collections );
    // } elseif (isset($_GET['collection_id'])
    //         && preg_match("/^[a-zA-Z0-9]+$/", $_GET['collection_id'])) {
    //     $collection_id = zotpress_clean_param( $_GET['collection_id'] );
    // } elseif (isset($_GET['subcollection_id'])
    //         && preg_match("/^[a-zA-Z0-9]+$/", $_GET['subcollection_id'])) {
    //     $collection_id = zotpress_clean_param( $_GET['subcollection_id'] );
    // }

	// // Filters
	// if ( $searchby ) $searchby = str_replace('"','',html_entity_decode($searchby));

	// // Style
	// if ( $style ) $style = str_replace('"','',html_entity_decode($style));

	// // Min length
	// if ( $minlength ) $minlength = str_replace('"','',html_entity_decode($minlength));

	// // Max results
	// if ( $maxresults ) $maxresults = str_replace('"','',html_entity_decode($maxresults));

	// // Max per page
	// if ( $maxperpage ) $maxperpage = str_replace('"','',html_entity_decode($maxperpage));

	// // Max tags
	// if ( $maxtags ) $maxtags = str_replace('"','',html_entity_decode($maxtags));

	// // Sortby
	// if ( $sortby ) $sortby = str_replace('"','',html_entity_decode($sortby));

	// // Order
	// if ( $order ) $order = str_replace('"','',html_entity_decode($order));

	// // Citeable
	// if ( $cite ) $cite = str_replace('"','',html_entity_decode($cite));
	// if ( $citeable ) $cite = str_replace('"','',html_entity_decode($citeable));

	// // Downloadable
	// if ( $download ) $download = str_replace('"','',html_entity_decode($download));
	// if ( $downloadable ) $download = str_replace('"','',html_entity_decode($downloadable));

    // // Show tags
    // if ( $showtags ) $showtags = str_replace('"','',html_entity_decode($showtags));
    // if ( strpos( $searchby, "tags" ) !== false ) $showtags = true;

	// // Show image
	// if ( $showimages ) $showimage = str_replace('"','',html_entity_decode($showimages));
	// if ( $showimage ) $showimage = str_replace('"','',html_entity_decode($showimage));

    // if ( $urlwrap ) $urlwrap = str_replace('"','',html_entity_decode($urlwrap));

    // if ( $toplevel ) $toplevel = str_replace('"','',html_entity_decode($toplevel));

    // $target = $target && $target != "no";

    // if ( $browsebar ) $browsebar = str_replace('"','',html_entity_decode($browsebar));


	// Get API User ID

	// global $wpdb;

    // if ( $nickname !== false )
    // {
    //     // $zp_account = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."zotpress WHERE nickname='".$nickname."'", OBJECT);
    //     $zp_account = $wpdb->get_row(
    //         $wpdb->prepare(
    //             "
    //             SELECT * FROM ".$wpdb->prefix."zotpress 
    //             WHERE nickname='%s'
    //             ",
    //             array( $nickname )
    //         ), OBJECT
    //     );
    //     if ( is_null($zp_account) ): echo "<p>Sorry, but the selected Zotpress nickname can't be found.</p>"; return false; endif;
    //     $api_user_id = $zp_account->api_user_id;
    // } 
    // elseif ( $api_user_id !== false )
    // {
    //     // $zp_account = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."zotpress WHERE api_user_id='".$api_user_id."'", OBJECT);
    //     $zp_account = $wpdb->get_row(
    //         $wpdb->prepare(
    //             "
    //             SELECT * FROM ".$wpdb->prefix."zotpress 
    //             WHERE api_user_id='%s'
    //             ",
    //             array( $api_user_id )
    //         ), OBJECT
    //     );
    //     if ( is_null($zp_account) ): echo $api_user_id."<p>Sorry, but the selected Zotpress account can't be found.</p>"; return false; endif;
    //     $api_user_id = $zp_account->api_user_id;
    // } 
    // elseif ( $api_user_id === false 
    //         && $nickname === false )
    // {
    //     if ( get_option("Zotpress_DefaultAccount") !== false )
    //     {
    //         $api_user_id = get_option("Zotpress_DefaultAccount");
    //         // $zp_account = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."zotpress WHERE api_user_id ='".$api_user_id."'", OBJECT);
    //         $zp_account = $wpdb->get_row(
    //             $wpdb->prepare(
    //                 "
    //                 SELECT * FROM ".$wpdb->prefix."zotpress 
    //                 WHERE api_user_id='%s'
    //                 ",
    //                 array( $api_user_id )
    //             ), OBJECT
    //         );
    //     }
    //     else // When all else fails ... assume one account
    //     {
    //         $zp_account = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."zotpress LIMIT 1", OBJECT);
    //         $api_user_id = $zp_account->api_user_id;
    //     }
    // }
    $zp_account = false;

    if ( $zpr['nickname'] !== false ) {

        $zp_account = $wpdb->get_row(
            $wpdb->prepare(
                "
                SELECT * FROM `".$wpdb->prefix."zotpress` 
                WHERE `nickname`=%s
                ",
                array( $zpr['nickname'] )
            ), OBJECT
        );
        if ( is_null($zp_account) ):
            return "<p>Sorry, but the selected Zotpress nickname can't be found.</p>";
        endif;
        $api_user_id = $zp_account->api_user_id;
    } 
    elseif ( $zpr["api_user_id"] !== false ) {

        $zp_account = $wpdb->get_row(
            $wpdb->prepare(
                "
                SELECT * FROM `".$wpdb->prefix."zotpress` 
                WHERE `api_user_id`=%s
                ",
                array( $zpr["api_user_id"] )
            ), OBJECT
        );
        if ( is_null($zp_account) ):
            return "<p>Sorry, but the selected Zotpress account can't be found.</p>";
        endif;
        $api_user_id = $zp_account->api_user_id;
    } 
    elseif ( $zpr["nickname"] === false 
            && $zpr["api_user_id"] === false ) {
        
        if ( get_option("Zotpress_DefaultAccount") !== false ) {

            $api_user_id = get_option("Zotpress_DefaultAccount");

            $zp_account = $wpdb->get_row(
                $wpdb->prepare(
                    "
                    SELECT * FROM `".$wpdb->prefix."zotpress` 
                    WHERE `api_user_id`=%s
                    ",
                    array( $api_user_id )
                ), OBJECT
            );
        }
        else { // When all else fails ... assume one account 

            $zp_account = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."zotpress LIMIT 1", OBJECT);
            $api_user_id = $zp_account->api_user_id;
        }
    }


	// Use Browse class
	$zpLib = new zotpressLib;

	$zpLib->setAccount($zp_account);
	$zpLib->setType($zpr['type']);
	if ( $zpr['searchby'] ) $zpLib->setFilters($zpr['searchby']);
	$zpLib->setMinLength($zpr['minlength']);
	$zpLib->setMaxResults($zpr['maxresults']);
	$zpLib->setMaxPerPage($zpr['maxperpage']);
	$zpLib->setTag($zpr['tag_id']);
	$zpLib->setMaxTags($zpr['maxtags']);
	$zpLib->setStyle($zpr['style']);
	$zpLib->setSortBy($zpr['sortby']);
    $zpLib->setOrder($zpr['order']);
    $zpLib->setCollection($zpr['collection_id']);
	$zpLib->setCiteable($zpr['citeable']);
	$zpLib->setDownloadable($zpr['downloadable']);
    $zpLib->setShowTags($zpr['showtags']);
	$zpLib->setShowImage($zpr['showimage']);
	$zpLib->setURLWrap($zpr['urlwrap']);
    $zpLib->setTopLevel($zpr['toplevel']);
    $zpLib->setTarget($zpr['target']);
    $zpLib->setBrowseBar($zpr['browsebar']);

	// Show theme scripts
    $GLOBALS['zp_is_shortcode_displayed'] = true;

	return $zpLib->getLib();
}


?>