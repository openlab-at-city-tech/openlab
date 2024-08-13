<?php

require(__DIR__ . '/shortcode.functions.php');
require(__DIR__ . '/shortcode.request.php');

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

    extract( shortcode_atts( array(

        'user_id' => false, // deprecated
        'userid' => false,
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

    ), $atts, 'zotpress'));

    // array_push($_GET, shortcode_atts(array(

    //     'user_id' => false, // deprecated
    //     'userid' => false,
    //     'nickname' => false,
    //     'nick' => false,

    //     'author' => false,
    //     'authors' => false,
    //     'year' => false,
    //     'years' => false,

    //     'itemtype' => false, // for selecting by itemtype; assumes one type
    //     'item_type' => 'items',
    //     'data_type' => false, // deprecated
    //     'datatype' => 'items',

    //     'collection_id' => false,
    //     'collection' => false,
    //     'collections' => false,

    //     'item_key' => false,
    //     'item' => false,
    //     'items' => false,

    //     'inclusive' => 'yes',

    //     'tag_name' => false,
    //     'tag' => false,
    //     'tags' => false,

    //     'style' => false,
    //     'limit' => false,

    //     'sortby' => 'default',
    //     'order' => false,
    //     'sort' => false,

    //     'title' => 'no',

    //     'image' => false,
    //     'images' => false,
    //     'showimage' => 'no',

    //     'showtags' => 'no',

    //     'downloadable' => 'no',
    //     'download' => 'no',

    //     'shownotes' => false,
    //     'note' => false,
    //     'notes' => 'no',

    //     'abstract' => false,
    //     'abstracts' => 'no',

    //     'cite' => 'no',
    //     'citeable' => false,

    //     'metadata' => false, // CHECK: Unused?

    //     'target' => false,
	// 	'urlwrap' => false,

	// 	'highlight' => false,
	// 	'forcenumber' => false,
	// 	'forcenumbers' => false

    // ), $atts));


    global $wpdb;
    global $post;


    // +---------------------------+
    // | FORMAT & CLEAN PARAMETERS |
    // +---------------------------+

    // 3.9.10: Use the Zotpress_prep_ajax_request_vars() function on bib, lib
    $zpr = Zotpress_prep_ajax_request_vars($atts);

    // Filter by account
    // if ($user_id) {
    //     $api_user_id = zp_clean_param( $user_id );
    // } elseif ($userid) {
    //     $api_user_id = zp_clean_param( $userid );
    // } else $api_user_id = false;

    // if ($nickname) $nickname = zp_clean_param( $nickname );
    // if ($nick) $nickname = zp_clean_param( $nick );

    // Filter by author
    // $author = zp_clean_param( $author );
    // if ($authors) $author = zp_clean_param( $authors );

    // // Filter by year
    // if ($year) {
    //     $year = zp_clean_param( $year );
    // } elseif ($years) {
    //     $year = zp_clean_param( $years );
    // } elseif (strpos($year, ",") > 0) {
    //     $year = explode(",", $year);
    // } else $year = "";

    // // Filter by itemtype
    // // TODO: Allow for multiple itemtypes in one shortcode?
    // $itemtype = zp_clean_param( $itemtype );
    // if ( $itemtype !== false )
    // {
    //     // Make sure it's one of the accepted types
    //     $officialItemTypes = array(
    //         'book',
    //         'bookSection',
    //         'journalArticle',
    //         'conferencePaper',
    //         'thesis',
    //         'report',
    //         'encyclopediaArticle',
    //         'newspaperArticle',
    //         'magazineArticle',
    //         'presentation',
    //         'interview',
    //         'dictionaryEntry',
    //         'document',
    //         'manuscript',
    //         'patent',
    //         'map',
    //         'blogPost',
    //         'webpage',
    //         'artwork',
    //         'film',
    //         'audioRecording',
    //         'statute',
    //         'bill',
    //         'case',
    //         'hearing',
    //         'forumPost',
    //         'letter',
    //         'email',
    //         'instantMessage',
    //         'software',
    //         'podcast',
    //         'radioBroadcast',
    //         'tvBroadcast',
    //         'videoRecording',
    //         'attachment',
    //         'note',
    //         'preprint'
    //     );

    //     $itemtypeCheck = false;

    //     foreach ($officialItemTypes as $type)
    //         if ( $itemtype == $type ) $itemtypeCheck = true;

    //     if ( !$itemtypeCheck )
    //         $itemtype = false; // Default is no itemtype filter
    // }

    // // Format with datatype and content
    // if ($item_type) {
    //     $item_type = zp_clean_param( $item_type );
    // } elseif ($data_type) {
    //     $item_type = zp_clean_param( $data_type );
    // } else $item_type = zp_clean_param( $datatype );

    // // Filter by collection
    // $collection_id = false;
    // if ($collection_id) {
    //     $collection_id = zp_clean_param( $collection_id );
    // } elseif ($collection) {
    //     $collection_id = zp_clean_param( $collection );
    // } elseif ($collections) {
    //     $collection_id = zp_clean_param( $collections );
    // }
	// $collection_id = str_replace(" ", "", $collection_id );

    // if (strpos($collection_id, ",") > 0) $collection_id = explode(",", $collection_id);
    // if ($item_type == "collections" && isset($_GET['zpcollection']) ) $collection_id = htmlentities( urldecode( $_GET['zpcollection'] ) );

    // // Filter by tag
    // $tag_id = false;
    // if ($tag_name) {
    //     $tag_id = zp_clean_param( $tag_name );
    // } elseif ($tags) {
    //     $tag_id = zp_clean_param( $tags );
    // } else $tag_id = zp_clean_param( $tag );

    // $tag_id = str_replace("+", "", $tag_id);
    // if (strpos($tag_id, ",") > 0) $tag_id = explode(",", $tag_id);
    // if ($item_type == "tags" && isset($_GET['zptag']) ) $tag_id = htmlentities( urldecode( $_GET['zptag'] ) );

    // // Filter by itemkey
    // if ($item_key) $item_key = zp_clean_param( $item_key );
    // if ($items) $item_key = zp_clean_param( $items );
    // if ($item) $item_key = zp_clean_param( $item );
    // if (strpos($item_key, ", ") > 0) $item_key = str_replace(', ',',',html_entity_decode($item_key)); // remove spces after commas
    // // if (strpos($item_key, ",") > 0) $item_key = explode(",", $item_key); // ? break at commas?
	// $item_key = str_replace(" ", "", $item_key ); // remove any spaces

	// Inclusive (for multiple authors)
    // $inclusive = $inclusive == "yes" || $inclusive == "true" || $inclusive === true;

    // Format style
    // $style = zp_clean_param( $style );

    // Limit
    // $limit = (int) zp_clean_param( $limit );

    // // Order / sort
    // $sortby = zp_clean_param( $sortby );

    // if ($order) {
    //     $order = strtolower(zp_clean_param( $order ));
    // } elseif ($sort) {
    //     $order = strtolower(zp_clean_param( $sort ));
    // }
    // if ($order === false) $order = "asc";

    // // Show title
	// // Sorting by secondary sort
    // $title = zp_clean_param( $title );
    // if ($title == "yes" || $title == "true" || $title === true) {
    //     $title = "year";
    // } elseif ($title == "no" || $title == "false") {
    //     $title = false;
    // }

    // // Show image
    // if ($showimage) $showimage = zp_clean_param( $showimage );
    // if ($image) $showimage = zp_clean_param( $image );
    // if ($images) $showimage = zp_clean_param( $images );

    // if ($showimage == "yes" || $showimage == "true" || $showimage === true) {
    //     $showimage = true;
    // } elseif ($showimage === "openlib") {
    //     $showimage = "openlib";
    // } else $showimage = false;

    // // Show tags
    // $showtags = $showtags == "yes" || $showtags == "true" || $showtags === true;

    // // Show download link
    // if ($download == "yes" || $download == "true" || $download === true
    //         || $downloadable == "yes" || $downloadable == "true" || $downloadable === true)
    //     $downloadable = true; else $downloadable = false;

    // // Show notes
    // if ($shownotes) {
    //     $shownotes = zp_clean_param( $shownotes );
    // } elseif ($notes) {
    //     $shownotes = zp_clean_param( $notes );
    // } elseif ($note) {
    //     $shownotes = zp_clean_param( $note );
    // }

    // $shownotes = $notes == "yes" || $notes == "true" || $notes === true;

    // // Show abstracts
    // if ($abstracts) $abstracts = zp_clean_param( $abstracts );
    // if ($abstract) $abstracts = zp_clean_param( $abstract );

    // $abstracts = $abstracts == "yes" || $abstracts == "true" || $abstracts === true;

    // // Show cite link
    // if ($cite) $citeable = zp_clean_param( $cite );
    // if ($citeable) $citeable = zp_clean_param( $citeable );

    // $citeable = $citeable == "yes" || $citeable == "true" || $citeable === true;

    // CHECK: Unused?
    // if ( ! preg_match("/^[0-9a-zA-Z]+$/", $metadata) ) $metadata = false;

	// // URL attributes
    // if ($target == "yes" || $target == "_blank" || $target == "new" || $target == "true" || $target === true)
    // $target = true; else $target = false;

    // $urlwrap = $urlwrap == "title" || $urlwrap == "image" ? zp_clean_param( $urlwrap ) : false;

    // $highlight = $highlight ? zp_clean_param( $highlight ) : false;

    // if ( $forcenumber == "yes" || $forcenumber == "true" || $forcenumber === true
    //         || $forcenumbers == "yes" || $forcenumbers == "true" || $forcenumbers === true )
    //     $forcenumber = true; else $forcenumber = false;


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
                SELECT * FROM ".$wpdb->prefix."zotpress 
                WHERE nickname='%s'
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
                SELECT * FROM ".$wpdb->prefix."zotpress 
                WHERE api_user_id='%s'
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
                    SELECT * FROM ".$wpdb->prefix."zotpress 
                    WHERE api_user_id='%s'
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
    $instance_id = "zotpress-".md5($post->ID.$api_user_id.$temp_author.$temp_year.$zpr['itemtype'].$temp_collection_id.$temp_item_key.$temp_tag_name.$zpr['style'].$temp_sortby.$zpr['order'].$zpr['limit'].$zpr['showimage'].$zpr['showtags'].$zpr['downloadable'].$zpr['shownotes'].$zpr['citeable'].$zpr['inclusive']);

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
    // <span class="ZP_ITEM_TYPE" style="display: none;">'.$item_type.'</span>
	$zp_output .= '">

		<span class="ZP_API_USER_ID" style="display: none;">'.$api_user_id.'</span>
		<span class="ZP_ITEM_KEY" style="display: none;">'.$zpr['item_key'].'</span>
		<span class="ZP_COLLECTION_ID" style="display: none;">'.$zpr['collection_id'].'</span>
		<span class="ZP_TAG_ID" style="display: none;">'.$zpr['tag_id'].'</span>
		<span class="ZP_AUTHOR" style="display: none;">'.$zpr['author'].'</span>
		<span class="ZP_YEAR" style="display: none;">'.$zpr['year'].'</span>
        <span class="ZP_ITEMTYPE" style="display: none;">'.$zpr['itemtype'].'</span>
		<span class="ZP_INCLUSIVE" style="display: none;">'.$zpr['inclusive'].'</span>
		<span class="ZP_STYLE" style="display: none;">'.$zpr['style'].'</span>
		<span class="ZP_LIMIT" style="display: none;">'.$zpr['limit'].'</span>
		<span class="ZP_SORTBY" style="display: none;">'.$zpr['sortby'].'</span>
		<span class="ZP_ORDER" style="display: none;">'.$zpr['order'].'</span>
		<span class="ZP_TITLE" style="display: none;">'.$zpr['title'].'</span>
		<span class="ZP_SHOWIMAGE" style="display: none;">'.$zpr['showimage'].'</span>
		<span class="ZP_SHOWTAGS" style="display: none;">'.$zpr['showtags'].'</span>
		<span class="ZP_DOWNLOADABLE" style="display: none;">'.$zpr['downloadable'].'</span>
		<span class="ZP_NOTES" style="display: none;">'.$zpr['shownotes'].'</span>
		<span class="ZP_ABSTRACT" style="display: none;">'.$zpr['showabstracts'].'</span>
		<span class="ZP_CITEABLE" style="display: none;">'.$zpr['citeable'].'</span>
		<span class="ZP_TARGET" style="display: none;">'.$zpr['target'].'</span>
		<span class="ZP_URLWRAP" style="display: none;">'.$zpr['urlwrap'].'</span>
		<span class="ZP_FORCENUM" style="display: none;">'.$zpr['forcenumber'].'</span>
        <span class="ZP_HIGHLIGHT" style="display: none;">'.$zpr['highlight'].'</span>
        <span class="ZP_POSTID" style="display: none;">'.$post->ID.'</span>
		<span class="ZOTPRESS_PLUGIN_URL" style="display:none;">'.ZOTPRESS_PLUGIN_URL.'</span>

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
        $_GET['api_user_id'] = $api_user_id;
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
        $zp_output .= Zotpress_shortcode_request( true ); // Check catche first
        $zp_output .= "\n\t\t\t</div><!-- .zp-zp-SEO-Content -->\n";
    }
    $zp_output .= "\t\t</div><!-- .zp-List -->\n\t</div><!--.zp-Zotpress-->\n\n";

	// Indicate that shortcode is displayed
	$GLOBALS['zp_is_shortcode_displayed'] = true;

	return $zp_output;
}

?>