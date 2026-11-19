<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 


function zotpress_strip_quotes($string)
{
    // Strip quotes and decode
    // 7.4.5: Vulnerability reported in use of html_entity_decode()
    // which converts not just quotes but all html entities; maybe remove for now
    // $string = str_replace("”", "", str_replace('"','', html_entity_decode( $string )));
    $string = str_replace("”", "", str_replace('"','', $string));
    return $string;
}

function Zotpress_zotpressInText ($atts)
{
    /*
    *   GLOBAL VARIABLES
    *
    *   $GLOBALS['zp_shortcode_instances'] {instantiated in zotpress.php}
    *
    */

    // extract(shortcode_atts(array(
    $atts = shortcode_atts(array(

        'item' => false,
        'items' => false,

        'pages' => false,
        'format' => "(%a%, %d%, %p%)",
		'brackets' => false,
        'etal' => false, // default (false), yes, no
        'separator' => false, // default (comma), semicolon
        'and' => false, // ampersand [default], and, comma, comma-amp, comma-and

        'userid' => false,
        'api_user_id' => false,
        'nickname' => false,
        'nick' => false

    ), $atts);

    global $post, $wpdb;


    // +---------------------------+
    // | FORMAT & CLEAN PARAMETERS |
    // +---------------------------+

    if ( $atts['items'] )
        $atts['items'] = zotpress_strip_quotes( str_replace(" ", "", $atts['items'] ));
    elseif ( $atts['item'] )
        $atts['items'] = zotpress_strip_quotes( str_replace(" ", "", $atts['item'] ));

    // 7.4.3 via 3.9.10: Use the Zotpress_prep_ajax_request_vars() function on intext
    $zpr = Zotpress_prep_ajax_request_vars($wpdb, $atts, false, true);

    // 7.4.5: Bug and vulnerability? Now uses $zpr
    // $items = $atts['items'];
    $items = $zpr['item_key'];

    $pages = $zpr['pages']; // 7.4.3: Is this used?
    $format = $zpr['format'];
    $brackets = $zpr['brackets'];
    $etal = $zpr['etal'];
    $separator = $zpr['separator'];
    $and = $zpr['and'];

    $api_user_id = $zpr['api_user_id'];
    $nickname = $zpr['nickname'];

    // Turn on/off minified versions if testing/live
    $minify = ''; if ( ZOTPRESS_LIVEMODE ) $minify = '.min';

	wp_enqueue_script( 'zotpress.shortcode.intext'.$minify.'.js' );

    $zp_account = false;

    if ( $nickname !== false )
    {
        $zp_account = $wpdb->get_row(
            $wpdb->prepare(
                "
                SELECT * FROM `".$wpdb->prefix."zotpress` 
                WHERE `nickname`=%s
                ",
                array( $nickname )
            ), OBJECT
        );

        if ( $zp_account !== null )
            $api_user_id = $zp_account->api_user_id;
    }
    elseif ( $api_user_id !== false )
    {
        $zp_account = $wpdb->get_row(
            $wpdb->prepare(
                "
                SELECT * FROM `".$wpdb->prefix."zotpress` 
                WHERE `api_user_id`=%s
                ",
                array( $api_user_id )
            ), OBJECT
        );

        if ( $zp_account !== null )
            $api_user_id = $zp_account->api_user_id;
    }
    elseif ( $api_user_id === false 
            && $nickname === false )
    {
        if ( get_option("Zotpress_DefaultAccount") !== false )
        {
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
        else // When all else fails ... assume one account
        {
            $zp_account = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."zotpress LIMIT 1", OBJECT);
            $api_user_id = $zp_account->api_user_id;
        }
    }


    // Format in-text items:
    // Handle the possible formats of item/s for in-text
    //
    // IN-TEXT FORMATS:
    // [zotpressInText item="NCXAA92F"]
    // [zotpressInText item="{NCXAA92F}"]
    // [zotpressInText item="{NCXAA92F,10-15}"]
    // [zotpressInText items="{NCXAA92F,10-15},{55MKF89B,1578},{3ITTIXHP}"]
    // [zotpressInText items="{000001:NCXAA92F,10-15},{000003:3ITTIXHP}"]
    // So no multiples without curlies or non-curlies in multiples

    $all_page_instances = array();
    $all_page_instances_str = "";
    // var_dump("tessst",$items);

    // // Add `ppp` in front of pages, so we can ignore pages later
	// $intextitem["items"] = preg_replace( "/((?=[^}]),(?=[^{]))+/", ",ppp", $intextitem["items"] );
    // REVIEW: Actually, let's just remove pages
    // $items = preg_replace( "/(((,))+([\w\d-]+(})+))++/", "}", $items );
    // REVIEW: Actually, we need them for now, remove later
    // $items = preg_replace( "/((?=[^}]),(?=[^{]))+/", ",ppp", $items );

    // Add api_user_id if not there
    // if ( strpos( $items, $api_user_id ) === false ) // WRONG: assumes default/global api_user_id rather than the one for this shortcode
    if ( strpos( $items, ":" ) === false )
    {
        if ( strpos( $items, "{" ) !== false ) {
            $items = str_replace( "{", "{".$api_user_id.":", $items );
        } elseif ( strpos( $items, "," ) !== false ) {
            $items = "{".$api_user_id.":" . str_replace( ",", "},{".$api_user_id.":", $items )."}";
        } else // assume unformatted and single, so place at front
        {
            $items = "{".$api_user_id.":".$items."}";
        }
    }
    // 7.4.5: Account for {:____}
    else if ( strpos($items, "{:") !== false )
    {
        // Add the userid in
        $items = str_replace( "{:", "{".$api_user_id.":", $items );
        // $api_user_id
        // $newstr = substr_replace($oldstr, $str_to_insert, $pos, 0);
    }

    // Determine page instances and where
    $temp_items = explode( "},{", $items );
    $all_np = true;

    foreach ( $temp_items as $id => $item )
    {
        // if ( strpos( $item, "," ) !== false ) // assume page/s
        // if ( preg_match( '/,(.)+\}/', $item, $match ) == 1 )
        if ( preg_match( '/,(.)+/', $item, $match ) == 1 )
        {
            $temp_arr_page_ins = [];

            // First, check for multiple, non-contiguous page numbers in parentheses
            // NOTE: array_filter will filter out anything that is "false," including 0
            if ( preg_match( '/(.)+/', $match[0], $matchm ) == 1 )
                $temp_arr_page_ins = array_filter( explode(',', str_replace('(', '', str_replace(')', '', $match[0]))) );
            else
                $temp_arr_page_ins = $match[0];

            // Then go through all and format
            foreach ( $temp_arr_page_ins as $pid => $page_ins )
            {
                if ( $page_ins == '' )
                    continue;
                
                $temp_arr_page_ins[$pid] = str_replace( "}", "", str_replace( ",", "", $page_ins ) );
                // $all_np = false;
            }
            $all_page_instances[$id] = $temp_arr_page_ins;

            if ( strlen($all_page_instances_str) > 0 )
                $all_page_instances_str = $all_page_instances_str . '--';
            $all_page_instances_str = $all_page_instances_str . join('++', $temp_arr_page_ins);

            // $all_page_instances[$id] = str_replace( "}", "", str_replace( ",", "", $match[0] ) );
            $all_np = false;
        }
        else
        {
            $all_page_instances[$id] = "np";

            if ( strlen($all_page_instances_str) > 0 )
                $all_page_instances_str = $all_page_instances_str . '--';
            $all_page_instances_str = $all_page_instances_str . 'np';
        }
    }

    // REVIEW: Replace ndashes and mdashes with dashes (7.3)
    $items = str_replace("–", "-", str_replace("–", "-", $items));

    // Remove pages from item key/s
    $items = preg_replace( "/(((,))+([\w\d-]+(})+))++/", "}", $items );
    $items = preg_replace( "/,\([\w\d-]+,+[\w\d-]+\)}/", "}", $items );
    unset($temp_items);


    // Generate instance id for shortcode
    // REVIEW: Changed for new item format
    // e.g., zp-ID--66010-FKNL6ECC-_-66010-FZF9BN8L--wp406
    // $instance_id = "zp-ID-".$api_user_id."-" . str_replace( " ", "_", str_replace( "&", "_", str_replace( "+", "_", str_replace( "/", "_", str_replace( "{", "-", str_replace( "}", "-", str_replace( ",", "_", $items ) ) ) ) ) ) ) ."-".$post->ID;
    // 7.4.5: Escape to fix the vulnerability
    $instance_id = esc_attr(sanitize_text_field(wp_strip_all_tags("zp-InText-zp-ID-" . str_replace( " ", "_", str_replace( "&", "_", str_replace( "+", "_", str_replace( "/", "_", str_replace( "{", "-", str_replace( "}", "-", str_replace( ":", "-", str_replace( ",", "_", $items ) ) ) ) ) ) ) ) ."-wp".$post->ID)));

    // Set up array for this post, if it doesn't exist
	if ( ! isset( $GLOBALS['zp_shortcode_instances'][$post->ID] ) )
		$GLOBALS['zp_shortcode_instances'][$post->ID] = array();

    // Determine if all items are np
    if ( $all_np )
        $all_page_instances_str = "np";

    // Then, add the instance to the array
    // REVIEW: Don't need api_user_id ... or maybe need multiple?
    // $GLOBALS['zp_shortcode_instances'][$post->ID][] = array( "instance_id" => $instance_id, "api_user_id" =>$api_user_id, "items" => $items );
    $GLOBALS['zp_shortcode_instances'][$post->ID][] = array(
        "instance_id" => $instance_id,
        "items" => $items,
        "page_instances" => $all_page_instances
    );

    // Show theme scripts
	$GLOBALS['zp_is_shortcode_displayed'] = true;

    // Output attributes and loading
    // REVIEW: Changed for new format
    // return '<span id="zp-InText-'.$instance_id."-".count($GLOBALS['zp_shortcode_instances'][$post->ID]).'"
	// 				class="zp-InText-Citation loading"
	// 				rel="{ \'api_user_id\': \''.$api_user_id.'\', \'pages\': \''.$pages.'\', \'items\': \''.$items.'\', \'format\': \''.$format.'\', \'brackets\': \''.$brackets.'\', \'etal\': \''.$etal.'\', \'separator\': \''.$separator.'\', \'and\': \''.$and.'\' }"></span>';
    // $output = '<span class="'.$instance_id.' zp-InText-Citation loading" rel="{ \'pages\': \''.join("--", $all_page_instances).'\', \'items\': \''.$items.'\', \'format\': \''.$format.'\', \'brackets\': \''.$brackets.'\', \'etal\': \''.$etal.'\', \'separator\': \''.$separator.'\', \'and\': \''.$and.'\' }"></span>';
    // 7.4.5: Fixing vulnerability
    $output = '<span class="'.esc_attr(sanitize_text_field(wp_strip_all_tags($instance_id))).' zp-InText-Citation loading" rel="{ \'pages\': \''.esc_attr(sanitize_text_field(wp_strip_all_tags($all_page_instances_str))).'\', \'items\': \''.esc_attr(sanitize_text_field(wp_strip_all_tags($items))).'\', \'format\': \''.$format.'\', \'brackets\': \''.$brackets.'\', \'etal\': \''.$etal.'\', \'separator\': \''.$separator.'\', \'and\': \''.$and.'\' }"></span>';

    return $output;
}

?>