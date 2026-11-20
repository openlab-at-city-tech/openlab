<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require(__DIR__ . '/shortcode.functions.php');
require(__DIR__ . '/shortcode.request.php');
require(__DIR__ . '/../request/request.dl.php');
require(__DIR__ . '/../request/request.cite.php');

/**
 * Handles the Zotpress bibliography shortcode.
 * 7.3.10: Refined to use $_GET and Zotpress_prep_ajax_request_vars() for processing.
 *
 * Used by: Shortcodes, zotpress.php
 *
 * @param arr $atts The shortcode attributes.
 *
 * @return str $zp_output The in-text shortcode HTML.
 */
function Zotpress_func( $atts ) {

    $zp_atts = shortcode_atts( array(

        'user_id' => false, // deprecated
        'userid' => false,
        'user' => false, // 7.4: unofficial
        'nickname' => false,
        'nick' => false,

        'author' => false,
        'authors' => false,
        'year' => false,
        'years' => false,

        'itemtype' => false, // for selecting by itemtype; assumes one type
        'item_type' => 'items',
        'data_type' => false, // deprecated
        'datatype' => 'items',

        'collection_id' => false,
        'collection' => false,
        'collections' => false,

        'item_key' => false,
        'item' => false,
        'items' => false,

        'inclusive' => 'yes',

        'tag_name' => false,
        'tag' => false,
        'tags' => false,

        'style' => false,
        'limit' => false,

        'sortby' => 'default',
        'order' => false,
        'sort' => false,

        'title' => 'no',

        'image' => false,
        'images' => false,
        'showimage' => 'no',

        'showtags' => 'no',

        'downloadable' => 'no',
        'download' => 'no',

        'shownotes' => false,
        'note' => false,
        'notes' => 'no',

        'abstract' => false,
        'abstracts' => 'no',

        'cite' => 'no',
        'citeable' => false,

        'metadata' => false,

        'target' => false,
		'urlwrap' => false,

		'highlight' => false,
		'forcenumber' => false,
		'forcenumbers' => false

    ), $atts );


    global $post, $wpdb;


    // +---------------------------+
    // | FORMAT & CLEAN PARAMETERS |
    // +---------------------------+

    // 3.9.10: Use the Zotpress_prep_ajax_request_vars() function on bib, lib
    $zpr = Zotpress_prep_ajax_request_vars($wpdb, $zp_atts);


    // +-------------+
    // | GET ACCOUNT |
    // +-------------+

    // CHECK: Is this needed? Isn't it already in zotpress.php?
    // CHECK: Yes, but it's not being called ... no idea why ...
    // Turn on/off minified versions if testing/live
    $minify = ''; if ( ZOTPRESS_LIVEMODE ) $minify = '.min';
	wp_enqueue_script( 'zotpress.shortcode.bib'.$minify.'.js' );

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
        $zpr["api_user_id"] = $zp_account->api_user_id;
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
        $zpr["api_user_id"] = $zp_account->api_user_id;
    } 
    elseif ( $zpr["nickname"] === false 
            && $zpr["api_user_id"] === false ) {
        
        if ( get_option("Zotpress_DefaultAccount") !== false ) {

            $zpr["api_user_id"] = get_option("Zotpress_DefaultAccount");

            $zp_account = $wpdb->get_row(
                $wpdb->prepare(
                    "
                    SELECT * FROM `".$wpdb->prefix."zotpress` 
                    WHERE `api_user_id`=%s
                    ",
                    array( $zpr["api_user_id"] )
                ), OBJECT
            );
        }
        else { // When all else fails ... assume one account 

            $zp_account = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."zotpress LIMIT 1", OBJECT);
            $zpr["api_user_id"] = $zp_account->api_user_id;
        }
    }
    
    // Generate instance id for shortcode
	$temp_item_key = is_array( $zpr['item_key'] ) ? implode( "-", $zpr['item_key']) : $zpr['item_key'];
	$temp_collection_id = is_array( $zpr['collection_id'] ) ? implode( "-", $zpr['collection_id']) : $zpr['collection_id'];
	$temp_tag_name = is_array( $zpr['tag_id'] ) ? implode( "-", $zpr['tag_id']) : $zpr['tag_id'];
	$temp_author = is_array( $zpr['author'] ) ? implode( "-", $zpr['author']) : $zpr['author'];
	$temp_year = is_array( $zpr['year'] ) ? implode( "-", $zpr['year']) : $zpr['year'];
	$temp_sortby = is_array( $zpr['sortby'] ) ? implode( "-", $zpr['sortby']) : $zpr['sortby'];

    // REVIEW: Added post ID
    // 7.3.10: REVIEW: Removed $nickname, $item_type
    // $instance_id = "zotpress-".md5($post->ID.$api_user_id.$nickname.$temp_author.$temp_year.$itemtype.$item_type.$temp_collection_id.$temp_item_key.$temp_tag_name.$style.$temp_sortby.$order.$limit.$showimage.$showtags.$downloadable.$shownotes.$citeable.$inclusive);
    $instance_id = "zotpress-".md5($post->ID.$zpr["api_user_id"].$temp_author.$temp_year.$zpr['itemtype'].$temp_collection_id.$temp_item_key.$temp_tag_name.$zpr['style'].$temp_sortby.$zpr['order'].$zpr['limit'].$zpr['showimage'].$zpr['showtags'].$zpr['downloadable'].$zpr['shownotes'].$zpr['citeable'].$zpr['inclusive']);

	// Prepare item key
	if ( $zpr['item_key'] 
            && gettype( $zpr['item_key'] ) != "string" ) 
        $zpr['item_key'] = implode( ",", $zpr['item_key'] );

	// Prepare collection
	if ( $zpr['collection_id'] 
            && gettype( $zpr['collection_id'] ) != "string" ) 
        $zpr['collection_id'] = implode( ",", $zpr['collection_id'] );

	// Prepare tags
	if ( $zpr['tag_id'] 
            && gettype( $zpr['tag_id'] ) != "string" ) 
        $zpr['tag_id'] = implode( ",", $zpr['tag_id'] );

    // Account for single or multiple years:
    if ( $zpr['year']
            && is_array($zpr['year']) )
        $zpr['year'] = implode( ",", $zpr['year'] );

    // Set up request vars
    $request_start = 0;
    $request_last = 0;
    $overwrite_last_request = false;

    // Set up Library vars
    $is_dropdown = false;
    $maxresults = 50;
    $maxperpage = 10;
    $maxtags = 100;

    // Set up Search vars
    $term = false;

    // Set up Update vars
    $update = false;

	$zp_output = '<div id="' . $instance_id . '"';
    $zp_output .= ' class="zp-Zotpress zp-Zotpress-Bib wp-block-group';
	if ( $zpr['forcenumber'] ) $zp_output .= " forcenumber";
    // 7.3.10: REVIEW: Removed the extra row:
    // <span class="ZP_ITEM_TYPE ZP_ATTR">'.$item_type.'</span>
	$zp_output .= '">

		<span class="ZP_API_USER_ID ZP_ATTR">'.$zpr["api_user_id"].'</span>
		<span class="ZP_ITEM_KEY ZP_ATTR">'.$zpr['item_key'].'</span>
		<span class="ZP_COLLECTION_ID ZP_ATTR">'.$zpr['collection_id'].'</span>
		<span class="ZP_TAG_ID ZP_ATTR">'.$zpr['tag_id'].'</span>
		<span class="ZP_AUTHOR ZP_ATTR">'.$zpr['author'].'</span>
		<span class="ZP_YEAR ZP_ATTR">'.$zpr['year'].'</span>
        <span class="ZP_ITEMTYPE ZP_ATTR">'.$zpr['itemtype'].'</span>
		<span class="ZP_INCLUSIVE ZP_ATTR">'.$zpr['inclusive'].'</span>
		<span class="ZP_STYLE ZP_ATTR">'.$zpr['style'].'</span>
		<span class="ZP_LIMIT ZP_ATTR">'.$zpr['limit'].'</span>
		<span class="ZP_SORTBY ZP_ATTR">'.$zpr['sortby'].'</span>
		<span class="ZP_ORDER ZP_ATTR">'.$zpr['order'].'</span>
		<span class="ZP_TITLE ZP_ATTR">'.$zpr['title'].'</span>
		<span class="ZP_SHOWIMAGE ZP_ATTR">'.$zpr['showimage'].'</span>
		<span class="ZP_SHOWTAGS ZP_ATTR">'.$zpr['showtags'].'</span>
		<span class="ZP_DOWNLOADABLE ZP_ATTR">'.$zpr['downloadable'].'</span>
		<span class="ZP_NOTES ZP_ATTR">'.$zpr['shownotes'].'</span>
		<span class="ZP_ABSTRACT ZP_ATTR">'.$zpr['showabstracts'].'</span>
		<span class="ZP_CITEABLE ZP_ATTR">'.$zpr['citeable'].'</span>
		<span class="ZP_TARGET ZP_ATTR">'.$zpr['target'].'</span>
		<span class="ZP_URLWRAP ZP_ATTR">'.$zpr['urlwrap'].'</span>
		<span class="ZP_FORCENUM ZP_ATTR">'.$zpr['forcenumber'].'</span>
        <span class="ZP_HIGHLIGHT ZP_ATTR">'.$zpr['highlight'].'</span>
        <span class="ZP_POSTID ZP_ATTR">'.$post->ID.'</span>
		<span class="ZOTPRESS_PLUGIN_URL ZP_ATTR">'.ZOTPRESS_PLUGIN_URL.'</span>

		<div class="zp-List loading">';


    // +--------------------------------+
    // | GENERATE SHORTCODE PLACEHOLDER |
    // +--------------------------------+

    if ( $zp_account === false )
    {
        $zp_output .= "\n<div id='".$instance_id."' class='zp-Zotpress'>Sorry, no citation(s) found for this account.</div>\n";
    }
    else // Make the first request via PHP for SEO purposes
    {
        $_GET['instance_id'] = $instance_id;
        $_GET['api_user_id'] = $zpr["api_user_id"];
        $_GET['item_key'] = $zpr['item_key'];
        $_GET['collection_id'] = $zpr['collection_id'];
        $_GET['tag_id'] = $zpr['tag_id'];
        $_GET['author'] = $zpr['author'];
        $_GET['year'] = $zpr['year'];
        $_GET['itemtype'] = $zpr['itemtype'];
        // $_GET['item_type'] = $item_type; // 7.3.10: REVIEW: Extra/repeated?
        $_GET['inclusive'] = $zpr['inclusive'];
        $_GET['style'] = $zpr['style'];
        $_GET['limit'] = $zpr['limit'];
        $_GET['sortby'] = $zpr['sortby'];
        $_GET['order'] = $zpr['order'];
        $_GET['title'] = $zpr['title'];
        $_GET['showimage'] = $zpr['showimage'];
        $_GET['showtags'] = $zpr['showtags'];
        $_GET['downloadable'] = $zpr['downloadable'];
        $_GET['shownotes'] = $zpr['shownotes'];
        $_GET['abstracts'] = $zpr['showabstracts'];
        $_GET['citeable'] = $zpr['citeable'];
        $_GET['target'] = $zpr['target'];
        $_GET['urlwrap'] = $zpr['urlwrap'];
        $_GET['forcenumber'] = $zpr['forcenumber'];
        $_GET['highlight'] = $zpr['highlight'];
        $_GET['request_start'] = $request_start;
        $_GET['request_last'] = $request_last;
        $_GET['is_dropdown'] = $is_dropdown;
        $_GET['maxresults'] = $maxresults;
        $_GET['maxperpage'] = $maxperpage;
        $_GET['maxtags'] = $maxtags;
        $_GET['term'] = $term;
        $_GET['update'] = $update;
        $_GET['overwrite_last_request'] = $overwrite_last_request;

        $zp_output .= "\n\t\t\t<div class=\"zp-SEO-Content\">\n";
        $zp_output .= Zotpress_shortcode_request( $zpr, true ); // Check catche first
        $zp_output .= "\n\t\t\t</div><!-- .zp-zp-SEO-Content -->\n";
    }
    $zp_output .= "\t\t</div><!-- .zp-List -->\n\t</div><!--.zp-Zotpress-->\n\n";

	// Indicate that shortcode is displayed
	$GLOBALS['zp_is_shortcode_displayed'] = true;

	return $zp_output;
}

?>