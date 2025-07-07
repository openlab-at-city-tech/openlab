<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 


/**
 * Handles the Zotpress in-text shortcode.
 * 7.3.10: Refined to use $_GET and Zotpress_prep_ajax_request_vars() for processing.
 *
 * Used by: Shortcodes, zotpress.php
 *
 * @param arr $atts The shortcode attributes.
 *
 * @return str $zp_output The in-text shortcode HTML.
 */
function Zotpress_zotpressInTextBib ($atts)
{
    /*
    *   RELIES ON THESE GLOBAL VARIABLES:
    *
    *   $GLOBALS['zp_shortcode_instances'][$post->ID] {instantiated previously}
    *
    */

    $atts = shortcode_atts(array(
    // extract(shortcode_atts(array(
        'style' => false,
        'sortby' => "default",
        'sort' => false,
        'order' => false,

        'image' => false,
        'images' => false,
        'showimage' => "no",
        'showtags' => "no",

        'title' => "no",

        'download' => "no",
        'downloadable' => false,
        'notes' => false,
        'abstract' => false,
        'abstracts' => false,
        'cite' => false,
        'citeable' => false,

        'target' => false,
		'urlwrap' => false,

		'highlight' => false,
        'forcenumber' => false,
        'forcenumbers' => false

    ), $atts);

    // array_push($_GET, shortcode_atts(array(
    //     'style' => false,
    //     'sortby' => "default",
    //     'sort' => false,
    //     'order' => false,

    //     'image' => false,
    //     'images' => false,
    //     'showimage' => "no",
    //     'showtags' => "no",

    //     'title' => "no",

    //     'download' => "no",
    //     'downloadable' => false,
    //     'notes' => false,
    //     'abstract' => false,
    //     'abstracts' => false,
    //     'cite' => false,
    //     'citeable' => false,

    //     'target' => false,
	// 	'urlwrap' => false,

	// 	'highlight' => false,
    //     'forcenumber' => false,
    //     'forcenumbers' => false

    // ), $atts));


    global $post, $wpdb;


    // +---------------------------+
    // | FORMAT & CLEAN PARAMETERS |
    // +---------------------------+

    // 3.9.10: Use the Zotpress_prep_ajax_request_vars() function on bib, lib
    $zpr = Zotpress_prep_ajax_request_vars($wpdb, $atts);

    // FORMAT PARAMETERS
    // $style = str_replace('"','',html_entity_decode($zpr['style']));
    // $sortby = str_replace('"','',html_entity_decode($zpr['sortby']));
    $style = $zpr['style'];
    $sortby = strtolower($zpr['sortby']);

    // if ($order) {
    //     $order = str_replace('"','',html_entity_decode($zpr['order']));
    // } elseif ($sort) {
    //     $order = str_replace('"','',html_entity_decode($zpr['sort']));
    // } else $order = "asc";
    $order = strtolower($zpr['order']);

    // Show image
    // if ($showimage) $showimage = str_replace('"','',html_entity_decode($zpr['showimage']));
    // if ($image) $showimage = str_replace('"','',html_entity_decode($zpr['image']));
    // if ($images) $showimage = str_replace('"','',html_entity_decode($zpr['images']));

    // if ($showimage == "yes" || $showimage == "true" || $showimage === true) {
    //     $showimage = true;
    // } elseif ($showimage === "openlib") {
    //     $showimage = "openlib";
    // } else $showimage = false;
    $showimage = $zpr['showimage'];

    // Show tags
    // $showtags = $showtags == "yes" || $showtags == "true" || $showtags === true;
    $showtags = $zpr["showtags"];

    // $title = str_replace('"','',html_entity_decode($zpr['title']));
    $title = $zpr['title'];

    // if ($download) {
    //     $download = str_replace('"','',html_entity_decode($zpr['download']));
    // } elseif ($downloadable) {
    //     $download = str_replace('"','',html_entity_decode($zpr['downloadable']));
    // }
    // if ($downloadable) {
        // $downloadable = str_replace('"','',html_entity_decode($zpr['downloadable']));
    // }
    // $download = $download == "yes" || $download == "true" || $download === true;
    $downloadable = $zpr['downloadable'];

    // $shownotes = str_replace('"','',html_entity_decode($zpr['shownotes']));
    $shownotes = $zpr['shownotes'];

    // if ($abstracts) {
    //     $abstracts = str_replace('"','',html_entity_decode($zpr['abstracts']));
    // } elseif ($abstract) {
    //     $abstracts = str_replace('"','',html_entity_decode($zpr['abstract']));
    // }
    $abstracts = $zpr['showabstracts'];

    // if ($citeable) {
    //     $citeable = str_replace('"','',html_entity_decode($zpr['citeable']));
    // } elseif ($cite) {
    //     $citeable = str_replace('"','',html_entity_decode($zpr['cite']));
    // }
    $citeable = $zpr["citeable"];

    // if ($target == "new" || $target == "yes" || $target == "_blank" || $target == "true" || $target === true) $target = true;
    // else $target = false;
    $target = $zpr["target"];

    // $urlwrap = $urlwrap == "title" || $urlwrap == "image" ? str_replace('"','',html_entity_decode($zpr['urlwrap'])) : false;
    $urlwrap = $zpr['urlwrap'];

    // $highlight = $highlight ? str_replace('"','',html_entity_decode($zpr['highlight'])) : false;
    $highlight = $zpr["highlight"];

    // if ($forcenumber == "yes" || $forcenumber == "true" || $forcenumber === true)
    //     $forcenumber = true;
    // if ($forcenumbers == "yes" || $forcenumbers == "true" || $forcenumbers === true)
    //     $forcenumber = true;
    $forcenumber = $zpr["request_start"];

    // Set up request vars
    $request_start = 0;
    $request_last = 0;
    $overwrite_last_request = false;
    $update = false;

    // Set up item key
	$item_key = "";

	// Get in-text items
	if ( isset( $GLOBALS['zp_shortcode_instances'][$post->ID] ) ) {

        // Handle the possible formats of item/s for in-text
    	
    	// IN-TEXT FORMATS:
    	// [zotpressInText item="NCXAA92F"]
    	// [zotpressInText item="{NCXAA92F}"]
    	// [zotpressInText item="{NCXAA92F,10-15}"]
    	// [zotpressInText items="{NCXAA92F,10-15},{55MKF89B,1578},{3ITTIXHP}"]
    	// [zotpressInText items="{000001:NCXAA92F,10-15},{000003:3ITTIXHP}"]
    	// So no multiples without curlies or non-curlies in multiples

		foreach ( $GLOBALS['zp_shortcode_instances'][$post->ID] as $intextitem ) {

            // REVIEW: Actually, let's just remove pages
            $intextitem["items"] = preg_replace( "/(((,))+([\w\d-]+(})+))++/", "}", $intextitem["items"] );

            // Add separator if not the start
			if ( $item_key != "" )
                $item_key .= ";";

            // Add to the item key
			$item_key .= $intextitem["items"];
		}
	}

    // Generate instance id for shortcode
    $instance_id = "zotpress-".md5($item_key.$style.$sortby.$order.$title.$showimage.$showtags.$downloadable.$shownotes.$abstracts.$citeable.$target.$urlwrap.$forcenumber.$highlight.$post->ID);

    // GENERATE IN-TEXT BIB STRUCTURE
	$zp_output = "\n<div id='zp-InTextBib-".$instance_id."'";
    $zp_output .= " class='zp-Zotpress zp-Zotpress-InTextBib wp-block-group";
	if ( $forcenumber ) $zp_output .= " forcenumber";
	$zp_output .= " zp-Post-".$post->ID."'>";
	$zp_output .= '
		<span class="ZP_ITEM_KEY ZP_ATTR">'.$item_key.'</span>
		<span class="ZP_STYLE ZP_ATTR">'.$style.'</span>
		<span class="ZP_SORTBY ZP_ATTR">'.$sortby.'</span>
		<span class="ZP_ORDER ZP_ATTR">'.$order.'</span>
		<span class="ZP_TITLE ZP_ATTR">'.$title.'</span>
		<span class="ZP_SHOWIMAGE ZP_ATTR">'.$showimage.'</span>
		<span class="ZP_SHOWTAGS ZP_ATTR">'.$showtags.'</span>
		<span class="ZP_DOWNLOADABLE ZP_ATTR">'.$downloadable.'</span>
		<span class="ZP_NOTES ZP_ATTR">'.$shownotes.'</span>
		<span class="ZP_ABSTRACT ZP_ATTR">'.$abstracts.'</span>
		<span class="ZP_CITEABLE ZP_ATTR">'.$citeable.'</span>
		<span class="ZP_TARGET ZP_ATTR">'.$target.'</span>
		<span class="ZP_URLWRAP ZP_ATTR">'.$urlwrap.'</span>
		<span class="ZP_FORCENUM ZP_ATTR">'.$forcenumber.'</span>
		<span class="ZP_HIGHLIGHT ZP_ATTR">'.$highlight.'</span>
		<span class="ZP_POSTID ZP_ATTR">'.$post->ID.'</span>';

        // <span class="ZP_API_USER_ID ZP_ATTR">'.$api_user_id.'</span>
		// <span class="ZOTPRESS_PLUGIN_URL ZP_ATTR">'.ZOTPRESS_PLUGIN_URL.'</span>'

    // $zp_output .= "<div class='zp-List loading'></div><!-- .zp-List --></div><!--.zp-Zotpress-->\n\n";
    $zp_output .= "<div class='zp-List loading'>";

    $_GET['instance_id'] = $instance_id;
    // $_GET['api_user_id'] = $api_user_id;
    $_GET['item_key'] = $item_key;
    // $_GET['collection_id'] = $collection_id;
    // $_GET['tag_id'] = $tag_id;
    // $_GET['author'] = $author;
    // $_GET['year'] = $year;
    // $_GET['item_type'] = $item_type;
    // $_GET['inclusive'] = $inclusive;
    // $_GET['style'] = $style;
    // // $_GET['limit'] = $limit;
    // $_GET['sortby'] = $sortby;
    // $_GET['order'] = $order;
    // $_GET['title'] = $title;
    // $_GET['showimage'] = $showimage;
    // $_GET['showtags'] = $showtags;
    // $_GET['downloadable'] = $downloadable;
    // $_GET['shownotes'] = $shownotes;
    // $_GET['abstracts'] = $abstracts;
    // $_GET['citeable'] = $citeable;
    // $_GET['target'] = $target;
    // $_GET['urlwrap'] = $urlwrap;
    // $_GET['forcenumber'] = $forcenumber;
    // $_GET['highlight'] = $highlight;

    $_GET['request_start'] = $request_start;
    $_GET['request_last'] = $request_last;
    // $_GET['is_dropdown'] = $is_dropdown;
    // $_GET['maxresults'] = $maxresults;
    // $_GET['maxperpage'] = $maxperpage;
    // $_GET['maxtags'] = $maxtags;
    // $_GET['term'] = $term;
    $_GET['update'] = $update;
    $_GET['overwrite_last_request'] = $overwrite_last_request;

    $zp_output .= "\n<div class=\"zp-SEO-Content\">";
    $zp_output .= Zotpress_shortcode_request( $zpr, true ); // Check catche first
    $zp_output .= "</div><!-- .zp-zp-SEO-Content -->\n";

    $zp_output .= "</div><!-- .zp-List --></div><!--.zp-Zotpress-->\n\n";

	// Show theme scripts
	$GLOBALS['zp_is_shortcode_displayed'] = true;

	return $zp_output;
}

?>
