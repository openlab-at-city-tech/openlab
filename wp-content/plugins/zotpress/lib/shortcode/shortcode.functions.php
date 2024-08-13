<?php


/**
 * Removes all extra quotations.
 *
 * Used by: Shortcodes
 *
 * @param str $param The shortcode parameter to clean.
 *
 * @return str $clean_param The clean parameter.
 */
function zp_clean_param( $param ) {

	// Thanks to Emerson@StackOverflow
	$search = array(
	    '&#8220;', // 1. Left Double Quotation Mark “
		'“',
	    '&#8221;', // 2. Right Double Quotation Mark ”
		'”',
	    '&#8216;', // 3. Left Single Quotation Mark ‘
		'‘',
	    '&#8217;', // 4. Right Single Quotation Mark ’
		'’',
		// NOTE: We need apostrophes (single quotes) for tags and ...?
	    // '&#039;',  // 5. Normal Single Quotation Mark '
	    '&amp;',   // 6. Ampersand &
	    '&quot;',  // 7. Normal Double Qoute
	    '&lt;',    // 8. Less Than <
	    '&gt;'     // 9. Greater Than >
	);

	// Fix the String
	$clean_param = htmlspecialchars( $param, ENT_QUOTES );

	return str_replace( $search, "", $clean_param );
}


/**
 * Gets the year from a date.
 *
 * Used by: In-Text Shortcode, In-Text Bibliography Shortcode
 *
 * @param str $date The date to search in.
 * @param bol $yesnd Return with a "n.d." if no year found.
 *
 * @return str $date_return The year found or blank/n.d. if not found.
 */
function zp_get_year( $date, $yesnd=false ) {

	$date_return = false;

	preg_match_all( '/(\d{4})/', $date, $matches );

	if (is_null($matches[0][0]))
		$date_return = $yesnd === true ? "n.d." : "";
	else
		$date_return = $matches[0][0];

	return $date_return;
}


/**
 * Sorts by a secondary value.
 *
 * Used by: Bibliography Shortcode, In-Text Bibliography Shortcode
 *
 * @param arr $item_arr The date to format.
 * @param str $sortby What attribute to sort by.
 * @param str $order What is the order (direction of the sort): ASC, DESC.
 *
 * @return arr $item_arr The newly sorted array of items.
 */
function subval_sort( $item_arr, $sortby, $order ) {

	// Format sort order
	$order = strtolower($order) == "desc" ? SORT_DESC : SORT_ASC;

	// Author or date
	if ( $sortby == "author"
			|| $sortby == "date" ) {
	
    	 foreach ( $item_arr as $key => $val ) {

   			$author[$key] = $val["author"];

   			$zpdate = ""; $zpdate = isset( $val["zpdate"] ) ? $val["zpdate"] : $val["date"];

   			$date[$key] = zp_date_format($zpdate);
		}

	} elseif ( $sortby == "title" ) {

		foreach ( $item_arr as $key => $val ) {

			$title[$key] = $val["title"];
			$author[$key] = $val["author"];
		}
	}

	// NOTE: array_multisort seems to be ignoring second sort for date->author
	if ( $sortby == "author" && isset($author) && is_array($author) )
		array_multisort( $author, $order, $date, $order, $item_arr );
	elseif ( $sortby == "date" && isset($date) && is_array($date) )
		array_multisort( $date, $order, $author, SORT_ASC, $item_arr );
	elseif ( $sortby == "title" && isset($title) && is_array($title) )
		array_multisort( $title, $order, $author, $order, $item_arr );

	return $item_arr;
}


/**
 * Reformats the date in a standard format: yyyy-mm-dd.
 *
 * Can read the following:
 *  - yyyy/mm/dd, mm/dd/yyyy
 *  - the dash equivalents of the above
 *  - mmmm dd, yyyy
 *  - yyyy mmmm, yyyy mmm (and the reverse)
 *  - mm-mm yyyy
 *
 * Used by: subval_sort
 *
 * @param str $date The date to format.
 *
 * @return str The formatted date, or the original if formatting fails.
 */
function zp_date_format ($date) {

	// Set up search lists
	$list_month_long = array ( "01" => "January", "02" => "February", "03" => "March", "04" => "April", "05" => "May", "06" => "June", "07" => "July", "08" => "August", "09" => "September", "10" => "October", "11" => "November", "12" => "December" );
	$list_month_short = array ( "01" => "Jan", "02" => "Feb", "03" => "Mar", "04" => "Apr", "05" => "May", "06" => "Jun", "07" => "Jul", "08" => "Aug", "09" => "Sept", "10" => "Oct", "11" => "Nov", "12" => "Dec" );


	// Check if it's a mm-mm dash
	if ( preg_match("/^[a-zA-Z]+[-][a-zA-Z]+[ ]\\d+\$/", $date ) == 1) {

		$temp1 = preg_split( "/-|\//", $date );
		$temp2 = preg_split( "[\s]", $temp1[1] );

		$date = $temp1[0]." ".$temp2[1];
	}

	// If it's already formatted with a dash or forward slash
	if ( strpos( $date, "-" ) !== false
			|| strpos( $date, "/" ) !== false ) {

		// Break it up
		$temp = preg_split( "/-|\//", $date );

		// If year is last, switch it with first
		if ( strlen($temp[0]) != 4 ) {

			// Just month and year
			if ( count($temp) == 2 )
				$date_formatted = array(
					"year" => $temp[1],
					"month" => $temp[0],
					"day" => false
				);
			// Assuming mm dd yyyy
			else
				$date_formatted = array(
					"year" => $temp[2],
					"month" => $temp[0],
					"day" => $temp[1]
				);
			
    	 } elseif ( isset($temp[2]) ) {

         	// day is set
         	$date_formatted = array(
				"year" => $temp[0],
				"month" => $temp[1],
				"day" => $temp[2]
			);

     	} else {

			$date_formatted = array(
				"year" => $temp[0],
				"month" => $temp[1],
				"day" => false
			);
		}

 	} elseif ( strpos( $date, "," ) ) {

		$date = trim( str_replace( ", ", ",", $date ) );
		$temp = preg_split( "/,| /", $date );

		// Convert month
		$month = array_search( $temp[0], $list_month_long );
		if ( ! $month )
			$month = array_search( $temp[0], $list_month_short );
		
		$date_formatted = array(
			"year" => $temp[2],
			"month" => $month,
			"day" => $temp[1]
		);

 	} else {

		$date = trim( str_replace( "  ", "-", $date ) );
		$temp = explode ( " ", $date );

		// If there's at least two parts to the date
		if ( $temp !== [] ) {

			// Check if name is first
			if ( ! is_numeric($temp[0]) ) {

				if ( in_array($temp[0], $list_month_long) ) {

					$date_formatted = array(
						"year" => $temp[1],
						"month" => array_search( $temp[0], $list_month_long ),
						"day" => false
					);

				} elseif ( in_array($temp[0], $list_month_short) ) {

					$date_formatted = array(
						"year" => $temp[1],
						"month" => array_search( $temp[0], $list_month_short ),
						"day" => false
					);

				} else // Not a recognizable month word
				
					$date_formatted = array(
						"year" => $temp[0], // $temp[1]
						"month" => false,
						"day" => false
					);

			} elseif ( count($temp) > 1 ) {

				if ( in_array($temp[1], $list_month_long) ) {

					$date_formatted = array(
						"year" => $temp[0],
						"month" => array_search( $temp[1], $list_month_long ),
						"day" => false
					);

				} elseif ( in_array($temp[1], $list_month_short) ) {

					$date_formatted = array(
						"year" => $temp[0],
						"month" => array_search( $temp[1], $list_month_short ),
						"day" => false
					);

				} else // Not a recognizable month word

					$date_formatted = array(
						"year" => $temp[0],
						"month" => false,
						"day" => false
					);

		   } else { // Only one part in the array

				$date_formatted = array(
					"year" => $temp[0],
					"month" => false,
					"day" => false
				);
			}
		}

		// Otherwise, assume year
		else
		{
			$date_formatted = array(
				"year" => $temp[0],
				"month" => false,
				"day" => false
			);
		}
	}

	// Format date in standard form: yyyy-mm-dd
	$date_formatted = implode( "-", array_filter( $date_formatted ) );

	if ( ! isset($date_formatted) )
		$date_formatted = $date;

	return $date_formatted;
}


/**
 * Processes the WP AJAX Zotero request variables.
 * We need to make sure user input is checked.
 * For complex strings inc. non-English chars: strip_tags()
 * For all else: sanitize_text_field()
 *
 * Used by: shortcode.php, shortcode.intextbib.php
 *
 * @return string Array with the processed variables.
 */
function Zotpress_prep_ajax_request_vars($atts=false) {

	$zpr = array();

	// Deal with empty atts, assume $_GET
	if ( $atts === false )
		$atts = $_GET;

	$zpr["api_user_id"] = false;
	if ( isset ($atts['api_user_id']) )
		$zpr["api_user_id"] = zp_clean_param(sanitize_text_field(wp_strip_all_tags( $atts['api_user_id'] )));
	elseif ( isset($atts['user_id']) )
		$zpr["api_user_id"] = zp_clean_param(sanitize_text_field(wp_strip_all_tags( $atts['user_id'] )));
	elseif ( isset($atts['userid']) )
		$zpr["api_user_id"] = zp_clean_param(sanitize_text_field(wp_strip_all_tags( $atts['userid'] )));
	
	$zpr['nickname'] = false;
	if ( isset($atts['nickname']) )
		$zpr["nickname"] = zp_clean_param(zp_clean_param( $atts['nickname'] ));
    if ( isset($atts['nick']) )
		$zpr["nickname"] = zp_clean_param(zp_clean_param( $atts['nick'] ));

	$zpr["limit"] = 50; // max 100, 22 seconds
	$zpr["overwrite_request"] = false;
	$zpr["overwrite_last_request"] = false;

	// Deal with incoming variables
	// 7.3.9: Added sanitize_text_field()
	// 7.3.10: Added wp_strip_all_tags()
	$zpr["type"] = "basic";
	if ( isset($atts['type']) 
			&& $atts['type'] != "" )
		$zpr["type"] = sanitize_text_field($atts['type']);
	
	$zpr["item_type"] = "items";
	if ( isset($atts['item_type']) 
			&& $atts['item_type'] != "" )
		$zpr["item_type"] = sanitize_text_field(wp_strip_all_tags($atts['item_type']));
	
	$zpr["get_top"] = false;
	if ( isset($atts['get_top']) ) $zpr["get_top"] = true;
	
	$zpr["sub"] = false;
	
	$zpr["is_dropdown"] = false;
	if ( isset($atts['is_dropdown']) 
			&& $atts['is_dropdown'] == "true" )
		$zpr["is_dropdown"] = true;
	
	$zpr["update"] = false;
	if ( isset($atts['update']) 
			&& $atts['update'] == "true" )
		$zpr["update"] = true;
	
	$zpr["updateneeded"] = false;
	if ( isset($atts['updateneeded']) 
			&& $atts['updateneeded'] == "true" )
		$zpr["updateneeded"] = true;

	$zpr["request_update"] = false;
	if ( isset($atts['request_update']) 
			&& $atts['request_update'] == "true" )
		$zpr["request_update"] = true;

	// instance id, item key, collection id, tag id
	$zpr["instance_id"] = false;
	if ( isset($atts['instance_id']) )
		$zpr["instance_id"] = sanitize_text_field(wp_strip_all_tags($atts['instance_id']));

	$zpr["item_key"] = false;
	if ( isset($atts['item_key'])
			&& ( $atts['item_key'] != "false" && $atts['item_key'] !== false ) )
		$zpr["item_key"] = zp_clean_param(sanitize_text_field(wp_strip_all_tags($atts['item_key'])));
	elseif ( isset($atts['items'])
			&& ( $atts['items'] != "false" && $atts['items'] !== false ) )
		$zpr["item_key"] = zp_clean_param(sanitize_text_field(wp_strip_all_tags($atts['items'])));
	elseif ( isset($atts['item'])
			&& ( $atts['item'] != "false" && $atts['item'] !== false ) )
		$zpr["item_key"] = zp_clean_param(sanitize_text_field(wp_strip_all_tags($atts['item'])));

	// Deal with multiple items
	if ( strpos($zpr["item_key"], ", ") > 0 )
		$zpr["item_key"] = str_replace(', ', ',', $zpr["item_key"]); // remove spces after commas
	// $item_key = str_replace(" ", "", $item_key ); // remove any spaces ... not needed?

			
	// REVIEW: Make sure item_key is formatting in the new style
	//
	// BIB FORMATS:
	// [zotpress item="GMGCJU34"]
	// [zotpress items="GMGCJU34,U9Z5JTKC"]
	if ( $zpr["item_key"] != false
			&& strpos( $zpr["item_key"], ":" ) == false ) {
		
		$temp_reformatted = "";
		$temp_items = explode( ",", $zpr["item_key"] );

		foreach ( $temp_items as $item )
			$temp_reformatted .= "{".$zpr["api_user_id"].":".$item."},";

		$zpr["item_key"] = rtrim( $temp_reformatted, ',' );
	}

	$zpr["itemtype"] = false;
	if ( isset($atts['itemtype'])
			&& ( $atts['itemtype'] != "false" && $atts['itemtype'] !== false && $atts['itemtype'] !== '' ) )
		$zpr["itemtype"] = zp_clean_param(sanitize_text_field(wp_strip_all_tags( $atts['itemtype'] )));
	elseif ( isset($atts['item_type'])
			&& ( $atts['item_type'] != "false" && $atts['item_type'] !== false && $atts['item_type'] !== '' ) )
		$zpr["itemtype"] = zp_clean_param(sanitize_text_field(wp_strip_all_tags( $atts['item_type'] )));
	elseif ( isset($atts['data_type'])
			&& ( $atts['data_type'] != "false" && $atts['data_type'] !== false && $atts['data_type'] !== '' ) )
		$zpr["itemtype"] = zp_clean_param(sanitize_text_field(wp_strip_all_tags( $atts['data_type'] )));
	
	// Filter by itemtype
    // TODO: Allow for multiple itemtypes in one shortcode?
    if ( $zpr["itemtype"] !== false ) {

        // Make sure it's one of the accepted types
        $officialItemTypes = array(
            'book',
            'bookSection',
            'journalArticle',
            'conferencePaper',
            'thesis',
            'report',
            'encyclopediaArticle',
            'newspaperArticle',
            'magazineArticle',
            'presentation',
            'interview',
            'dictionaryEntry',
            'document',
            'manuscript',
            'patent',
            'map',
            'blogPost',
            'webpage',
            'artwork',
            'film',
            'audioRecording',
            'statute',
            'bill',
            'case',
            'hearing',
            'forumPost',
            'letter',
            'email',
            'instantMessage',
            'software',
            'podcast',
            'radioBroadcast',
            'tvBroadcast',
            'videoRecording',
            'attachment',
            'note',
            'preprint'
        );

        $itemtypeCheck = false;

        foreach ( $officialItemTypes as $type )
            if ( $zpr["itemtype"] == $type )
				$itemtypeCheck = true;

        if ( ! $itemtypeCheck )
			$zpr["itemtype"] = false; // Default is no itemtype filter
    }

	$zpr["collection_id"] = false;
	if ( isset($atts['collection_id'])
			&& ( $atts['collection_id'] != "false" && $atts['collection_id'] !== false && $atts['collection_id'] !== '' ) )
		$zpr["collection_id"] = sanitize_text_field(wp_strip_all_tags($atts['collection_id']));
	elseif ( isset($atts['collection'])
			&& ( $atts['collection'] != "false" && $atts['collection'] !== false && $atts['collection'] !== '' ) )
		$zpr["collection_id"] = sanitize_text_field(wp_strip_all_tags($atts['collection']));
	elseif ( isset($atts['collections'])
			&& ( $atts['collections'] != "false" && $atts['collections'] !== false && $atts['collections'] !== '' ) )
		$zpr["collection_id"] = sanitize_text_field(wp_strip_all_tags($atts['collections']));
	elseif ( isset($atts['zpcollection'])
			&& ( $atts['zpcollection'] != "false" && $atts['zpcollection'] !== false && $atts['zpcollection'] !== '' ) )
		$zpr["collection_id"] = sanitize_text_field(wp_strip_all_tags($atts['zpcollection']));
	// $collection_id = str_replace(" ", "", $collection_id );

	// 7.3.10: Originally in shortcode.php but doesn't make sense here ...
	// // Deal with multiple collection IDs
	// if ( strpos($zpr["collection_id"], ",") > 0 )
	// 	$zpr["collection_id"] = explode(",", $zpr["collection_id"]);

	// Deal with collection item type
	// CHECK: Is this still used?
	// if ( $zpr["itemtype"] == "collections" 
	// 		&& isset($_GET['zpcollection']) )
	// 	$collection_id = htmlentities( urldecode( $_GET['zpcollection'] ) );


	$zpr["tag_id"] = false;
	if ( isset($atts['tag_id'])
			&& ( $atts['tag_id'] != "false" && $atts['tag_id'] !== false && $atts['tag_id'] !== '' ) )
		$zpr["tag_id"] = sanitize_text_field(wp_strip_all_tags($atts['tag_id']));
	elseif ( isset($atts['tag_name'])
			&& ( $atts['tag_name'] != "false" && $atts['tag_name'] !== false && $atts['tag_name'] !== '' ) )
		$zpr["tag_id"] = sanitize_text_field(wp_strip_all_tags($atts['tag_name']));
	elseif ( isset($atts['tags'])
			&& ( $atts['tags'] != "false" && $atts['tags'] !== false && $atts['tags'] !== '' ) )
		$zpr["tag_id"] = sanitize_text_field(wp_strip_all_tags($atts['tags']));
	elseif ( isset($atts['tag'])
			&& ( $atts['tag'] != "false" && $atts['tag'] !== false && $atts['tag'] !== '' ) )
		$zpr["tag_id"] = sanitize_text_field(wp_strip_all_tags($atts['tag']));
	elseif ( isset($atts['zptag'])
			&& ( $atts['zptag'] != "false" && $atts['zptag'] !== false && $atts['zptag'] !== '' ) )
		$zpr["tag_id"] = sanitize_text_field(wp_strip_all_tags($atts['zptag']));

	// Deal with plus sign as space
	$zpr["tag_id"] = str_replace("+", "", $zpr["tag_id"]);

	// 7.3.10: Originally in shortcode.php but doesn't make sense here ...
	// // Deal with multiple tags
	// if ( strpos($zpr["tag_id"], ",") > 0 )
	// 	$zpr["tag_id"] = explode(",", $zpr["tag_id"]);
	// if ($item_type == "tags" && isset($_GET['zptag']) ) $tag_id = htmlentities( urldecode( $_GET['zptag'] ) );
	
	// Author/s, year, style, limit, title
	$zpr["author"] = false;
	if ( isset($atts['author']) 
			&& $atts['author'] != "false" && $atts['author'] != "" )
		$zpr["author"] = zp_clean_param(sanitize_text_field(wp_strip_all_tags($atts['author'])));
	elseif ( isset($atts['authors']) 
			&& $atts['authors'] != "false" && $atts['authors'] != "" )
		$zpr["author"] = zp_clean_param(sanitize_text_field(wp_strip_all_tags($atts['authors'])));

	// TESTING: urldecode
	// CHECK: Is this a test ..? Removing for now ...
	// $zpr["author"] = urldecode($zpr["author"]);
	$zpr["author"] = htmlspecialchars($zpr["author"]);

	
	$zpr["year"] = false;
	if ( isset($atts['year']) 
			&& $atts['year'] != "false" && $atts['year'] != "" )
		$zpr["year"] = zp_clean_param(sanitize_text_field(wp_strip_all_tags( $atts['year'] )));
	elseif ( isset($atts['years']) 
			&& $atts['years'] != "false" && $atts['years'] != "" )
		$zpr["year"] = zp_clean_param(sanitize_text_field(wp_strip_all_tags( $atts['years'] )));
	
	// Deal with multiple years:
	if ( strpos($zpr["year"], ",") > 0 )
		$zpr["year"] = explode(",", $zpr["year"]);
	
	$zpr["style"] = zp_Get_Default_Style();
	if ( isset($atts['style']) 
			&& $atts['style'] != "false" && $atts['style'] != "" && $atts['style'] != "default" )
		$zpr["style"] = sanitize_text_field(wp_strip_all_tags($atts['style']));

	// NOTE: With PHP 8, watch for URL params that are strings but need to be ints
	// 7.3.10: CHECK: Ignore if 50
	if ( isset($atts['limit'])
			&& (int) $atts['limit'] != 0
			&& (int) $atts['limit'] != 50 ) {

		$zpr["limit"] = (int) sanitize_text_field(wp_strip_all_tags($atts['limit']));
		$zpr["overwrite_request"] = true;
	}

	// Title
	$zpr["title"] = false;
	if ( isset($atts['title']) )
		if ( in_array( strtolower($atts["title"]), array("yes", "true", "year", "years") ) )
			$zpr["title"] = "year";
		elseif ( in_array( strtolower($atts["title"]), array("itemtype", "item_type", "itemtypes", "item_types", "type") ) )
			$zpr["title"] = "itemtype";
		else
			$zpr["title"] = false;

	// Max tags, max results
	$zpr["maxtags"] = false;
	if ( isset($atts['maxtags']) )
		$zpr["maxtags"] = (int) sanitize_text_field(wp_strip_all_tags($atts['maxtags']));

	$zpr["maxresults"] = false;
	if ( isset($atts['maxresults']) )
		$zpr["maxresults"] = (int) sanitize_text_field(wp_strip_all_tags($atts['maxresults']));

	// Term, filter
	$zpr["term"] = false;
	if ( isset($atts['term']) )
		$zpr["term"] = strip_tags($atts['term']);

	$zpr["filter"] = false;
	if ( isset($atts['filter']) )
		$zpr["filter"] = sanitize_text_field(wp_strip_all_tags($atts['filter']));

	// Sorty by, order
	$zpr["sortby"] = false;
	$zpr["order"] = false;
	$zpr["item_keys_order"] = array();

	// Lib toplevel
	// Set UNLESS there's a tag
	$zpr["toplevel"] = false;
	if ( isset($atts['toplevel'])
			&& $zpr["tag_id"] === false ) {

		$zpr["toplevel"] = sanitize_text_field(wp_strip_all_tags($atts['toplevel']));
		// $zpr["collection_id"] = false;
	}
	if ( $zpr["collection_id"] == "toplevel" )
		$zpr["collection_id"] = false;

	// SPECIAL SETTINGS
	if ( isset($atts['sortby']) ) {

		if ( $atts['sortby'] == "author" ) {
			$zpr["sortby"] = "creator";
			$zpr["order"] = "asc";
		} elseif ( $atts['sortby'] == "default" ) {
			$zpr["sortby"] = "default";
			// entry order
		} elseif ( $atts['sortby'] == "year" ) {
			$zpr["sortby"] = "date";
			$zpr["order"] = "desc";
		} elseif ( $zpr["type"] == "intext" 
				&& $atts['sortby'] == "default" ) {
			$zpr["sortby"] = "default";
		} else {
			$zpr["sortby"] = strtolower(sanitize_text_field(wp_strip_all_tags($atts['sortby'])));
		}
	}
	
	if ( isset($atts['order'])
			&& ( strtolower($atts['order']) == "asc" || strtolower($atts['order']) == "desc" ) )
		$zpr["order"] = strtolower(sanitize_text_field(wp_strip_all_tags($atts['order'])));
	elseif ( isset($atts['sort'])
			&& ( strtolower($atts['sort']) == "asc" || strtolower($atts['sort']) == "desc" ) )
		$zpr["order"] = strtolower(sanitize_text_field(wp_strip_all_tags($atts['sort'])));

	// Show images, show tags, downloadable, inclusive, notes, abstracts, citeable
	$zpr["showimage"] = false;
	if ( isset($atts['showimage']) )
		$zpr["showimage"] = sanitize_text_field(wp_strip_all_tags($atts['showimage']));
	elseif ( isset($atts['image']) )
		$zpr["showimage"] = sanitize_text_field(wp_strip_all_tags($atts['image']));
	elseif ( isset($atts['images']) )
		$zpr["showimage"] = sanitize_text_field(wp_strip_all_tags($atts['images']));

	// Set value
	// if ( isset($_GET['showimage']) )
		if ( $zpr['showimage'] == "yes" || $zpr['showimage'] == "true"
				|| $zpr['showimage'] === true || $zpr['showimage'] == 1 )
			$zpr["showimage"] = true;
		elseif ( $zpr['showimage'] == "openlib" )
			$zpr["showimage"] = "openlib";
		else
		$zpr["showimage"] = false;

	$zpr["showtags"] = false;
	if ( isset($atts['showtags'])
			&& ( $atts['showtags'] == "yes" || $atts['showtags'] == "true"
				|| $atts['showtags'] === true || $atts['showtags'] == 1 ) )
		$zpr["showtags"] = true;

	$zpr["downloadable"] = false;
	if ( isset($atts['downloadable'])
			&& ( $atts['downloadable'] == "yes" || $atts['downloadable'] == "true" || $atts['downloadable'] === true || $atts['downloadable'] == 1 ) )
		$zpr["downloadable"] = true;
	elseif ( isset($atts['download'])
			&& ( $atts['download'] == "yes" || $atts['download'] == "true" || $atts['download'] === true || $atts['download'] == 1 ) )
		$zpr["downloadable"] = true;

	$zpr["inclusive"] = false;
	if ( isset($atts['inclusive'])
			&& ( $atts['inclusive'] == "yes" || $atts['inclusive'] == "true" || $atts['inclusive'] === true || $atts['inclusive'] == 1 ) )
		$zpr["inclusive"] = true;

	$zpr["shownotes"] = false;
	if ( isset($atts['shownotes'])
			&& ( $atts['shownotes'] == "yes" || $atts['shownotes'] == "true" || $atts['shownotes'] === true || $atts['shownotes'] == 1 ) )
		$zpr["shownotes"] = true;
	elseif ( isset($atts['notes'])
			&& ( $atts['notes'] == "yes" || $atts['notes'] == "true" || $atts['notes'] === true || $atts['notes'] == 1 ) )
		$zpr["shownotes"] = true;
	elseif ( isset($atts['note'])
			&& ( $atts['note'] == "yes" || $atts['note'] == "true" || $atts['note'] === true || $atts['note'] == 1 ) )
		$zpr["shownotes"] = true;

	$zpr["showabstracts"] = false;
	if ( isset($atts['showabstracts'])
			&& ( $atts['showabstracts'] == "yes" || $atts['showabstracts'] == "true" || $atts['showabstracts'] === true || $atts['showabstracts'] == 1 ) )
		$zpr["showabstracts"] = true;
	elseif ( isset($atts['abstracts'])
			&& ( $atts['abstracts'] == "yes" || $atts['abstracts'] == "true" || $atts['abstracts'] === true || $atts['abstracts'] == 1 ) )
		$zpr["showabstracts"] = true;
	elseif ( isset($atts['abstract'])
			&& ( $atts['abstract'] == "yes" || $atts['abstract'] == "true" || $atts['abstract'] === true || $atts['abstract'] == 1 ) )
		$zpr["showabstracts"] = true;

	$zpr["citeable"] = false;
	if ( isset($atts['citeable'])
			&& ( $atts['citeable'] == "yes" || $atts['citeable'] == "true" || $atts['citeable'] === true || $atts['citeable'] == 1 ) )
		$zpr["citeable"] = true;
	elseif ( isset($atts['cite'])
			&& ( $atts['cite'] == "yes" || $atts['cite'] == "true" || $atts['cite'] === true || $atts['cite'] == 1 ) )
		$zpr["citeable"] = true;

	// Target, urlwrap, forcenum
	$zpr["target"] = false;
	if ( isset($atts['target'])
			&& ( $atts['target'] == "yes" || $atts['target'] == "true" 
					|| $atts['target'] === true || $atts['target'] == 1
					|| $atts['target'] == "_blank" || $atts['target'] == "new" ) )
		$zpr["target"] = true;

	$zpr["urlwrap"] = false;
	if ( isset($atts['urlwrap']) 
			&& ( $atts['urlwrap'] == "title" || $atts['urlwrap'] == "image" ) )
		$zpr["urlwrap"] = sanitize_text_field(wp_strip_all_tags($atts['urlwrap']));

	// CHECK: Is htmlentities() and trim() needed anymore?
	$zpr["highlight"] = false;
	if ( isset($atts['highlight'])
			&& $atts['highlight'] !== ""
			&& $atts['highlight'] !== false
			&& $atts['highlight'] !== 0
		 	&& $atts['highlight'] !== "false" )
		$zpr["highlight"] = trim( htmlentities( zp_clean_param( sanitize_text_field(wp_strip_all_tags( $atts['highlight'] ))) ));

	$zpr["forcenumber"] = false;
	if ( isset($atts['forcenumber'])
			&& ( $atts['forcenumber'] == "yes" || $atts['forcenumber'] == "true" || $atts['forcenumber'] === true || $atts['forcenumber'] == 1 ) )
		$zpr["forcenumber"] = true;

	$zpr["request_start"] = 0;
	if ( isset($atts['request_start']) )
		$zpr["request_start"] = (int) sanitize_text_field(wp_strip_all_tags($atts['request_start']));
	
	$zpr["request_last"] = 0;
	if ( isset($atts['request_last']) ) 
		$zpr["request_last"] = (int) sanitize_text_field(wp_strip_all_tags($atts['request_last']));

	return $zpr;

} // function Zotpress_prep_ajax_request_vars



/**
 * Preps and formats the Zotero API request URL.
 *
 * Handles all possible Zotpress parameters for bibliography
 * shortcodes. Per user account.
 *
 * @param obj $wpdb WP DB object.
 * @param arr $zpr Holds all params for request.
 * @param arr $zp_request_queue Holds all requests for all accounts.
 * @param str $api_user_id Optional. API user ID.
 *
 * @return arr $zp_request_queue The new request queue of formatted URLs.
 */
function Zotpress_prep_request_URL( $wpdb, $zpr, $zp_request_queue, $api_user_id=false, $zp_request_data=false ) {
	
	$tempItemType = "";

	// Get account and $api_user_id
	if ( $api_user_id ) {
		$zp_account = zp_get_account( $wpdb, $api_user_id );
	} elseif ( $zpr["api_user_id"] ) {
		$zp_account = zp_get_account( $wpdb, $zpr["api_user_id"] );
		$api_user_id = $zpr["api_user_id"];
	} else {
		$zp_account = zp_get_account( $wpdb );
		$api_user_id = $zp_account[0]->api_user_id;
	}

	// Make sure account was founded (is synced)
	if ( count($zp_account) > 0 ) {

		// Basic URL: User type, user id, item type
		$zp_import_url = "https://api.zotero.org/".$zp_account[0]->account_type."/".$api_user_id."/".$zpr["item_type"];

	    // Deal with item type Items
	    if ( $zpr['item_type'] == 'items' ) {

	    	// Account for single item with new style
	    	if ( gettype( $zpr["item_key"] ) == "string"
					&& strlen($zpr["item_key"]) > 0
	    			&& $zpr["item_key"][0] == "{" ) {
			
				$zpr_temp = explode(':', $zpr["item_key"]);
				$zpr_temp = count($zpr_temp) > 1 ? $zpr_temp[1] : $zpr_temp[0];
				$zpr["item_key"] = rtrim( $zpr_temp, "}");

				// Account for page numbers
				if ( strpos( $zpr["item_key"], ',' ) ) {

					$zpr_temp = explode(',', $zpr["item_key"]);
					$zpr["item_key"] = rtrim( $zpr_temp[0], "}");
				}
			}
			elseif ( gettype( $zpr["item_key"] ) == "array"
					&& count( $zpr["item_key"] ) == 1
					&& $zpr["item_key"][0][0] == "{" ) {

				// $zpr["item_key"] = rtrim( explode(':', $zpr["item_key"][0])[1] , "}");
				$zpr["item_key"] = ltrim( rtrim( $zpr["item_key"][0], "}" ), "{" );
				
				// Account for page numbers
				if ( strpos( $zpr["item_key"], ',' ) ) {

					$zpr_temp = explode(',', $zpr["item_key"]);
					$zpr["item_key"] = rtrim( $zpr_temp[0], "}" );
				}
			}
		} // item type Items

		// Top
		if ( $zpr["get_top"] ) $zp_import_url .= "/top";

		// Single item key
		if ( ! empty( $zp_request_queue )
				&& ( ! isset( $zp_request_queue[$api_user_id]["items"] )
					|| strpos($zp_request_queue[$api_user_id]["items"], ',') == false )
			) {
			
			if (isset( $zp_request_data["items"] )) {

				$zp_import_url .= "/" . $zp_request_data["items"];
			}
			elseif (gettype( $zpr["item_key"] ) == "array"
					&& count( $zpr["item_key"] ) == 1
					&& strpos( $zpr["item_key"][0], ',' ) == false) {

				$zp_import_url .= "/" . $zpr["item_key"][0];
			}
			elseif (gettype( $zpr["item_key"] ) == "string"
					&& ( strpos( $zpr["item_key"], "," ) === false
					&& strpos( $zpr["item_key"], ";" ) === false )) {

				$zp_import_url .= "/" . $zpr["item_key"];
			}
		}

		if ( $zpr["collection_id"] )
			$zp_import_url .= "/" . $zpr["collection_id"];

		if ( $zpr["sub"] )
			$zp_import_url .= "/" . $zpr["sub"];
		
		$zp_import_url .= "?";

		// Public key, if needed
		if ( ! is_null($zp_account[0]->public_key) 
				&& trim($zp_account[0]->public_key) != "" )
			$zp_import_url .= "key=".$zp_account[0]->public_key."&";

		// Style
		$zp_import_url .= "style=".$zpr["style"];

		// Format, limit, etc.
		$zp_import_url .= "&format=json&include=data,bib&limit=".$zpr["limit"];

		// Sort and order
		if ( $zpr["sortby"]
				// && ( $zpr["sortby"] != "default" &&  ) ) {
				&& ! in_array( $zpr["sortby"], array("default", false, "false") ) ) {

			$zp_import_url .= "&sort=".$zpr["sortby"];

			if ( $zpr["order"] ) $zp_import_url .= "&direction=".$zpr["order"];
		}

		// Start if multiple
		if ( $zpr["request_start"] != 0 )
			$zp_import_url .= "&start=".$zpr["request_start"];

		// Multiple item keys
		// EVENTUAL TODO: Limited to 50 item keys at a time ... can I get around this?
		// TODO: Test this with a bib that has 50+ items
		// if ( $zpr["item_key"] && strpos( $zpr["item_key"],"," ) !== false ) $zp_import_url .= "&itemKey=" . $zpr["item_key"];
	    if ( $zp_request_data ) {

	        if ( substr_count($zp_request_data["items"], ",") >= 50 ) {

				// Split items by comma
	    		$items = explode( ",", $zp_request_data["items"] );

	    		$requests = array();
	    		$request_items = array();

	    		foreach ( $items as $item ) {

					// Thanks to Tomas Risberg and @ericcorbett2
					if ( is_countable($request_items) ) {

						if ( count($request_items) < 50 )
							$request_items[] = $item;
					}
					else {

						$requests[] = $request_items;
						unset( $request_items );
					}
					// Old code:
	    			// if ( count($request_items) < 50 ) {
	    			// 	$request_items[] = $item;
	    			// }
	    			// else {
	    			// 	$requests[] = $request_items;
	    			// 	unset( $request_items );
	    			// }
	    		}

	    		$zp_request_queue[$api_user_id]["requests"] = $requests;
	    	}
	    	else {
	            // TODO: Is this necessary?
	    		// $zp_request_queue[$api_user_id]["requests"] = explode( ",", $zp_request_data["items"] );
	    	}
	    }

		// Itemtype-specific
		if ( $zpr["itemtype"] )
			$zp_import_url .= "&itemType=" . urlencode( stripslashes( $zpr["itemtype"] ));

		// Tag-specific
		if ( $zpr["tag_id"] ) {

			if ( strpos($zpr["tag_id"], ",") !== false ) {

				$temp = explode( ",", $zpr["tag_id"] );

				foreach ( $temp as $temp_tag )
					$zp_import_url .= "&tag=" . urlencode( stripslashes( $temp_tag ));
			}
			else {

				$zp_import_url .= "&tag=" . urlencode( stripslashes( $zpr["tag_id"] ));
			}
		}

		// Filtering: collections and tags take priority over authors and year
		// EVENTUAL TODO: Searching by two+ values is not supported on the Zotero side ...
		// For now, we get all and manually filter below
		$zp_author_or_year_multiple = false;

		if ( $zpr["collection_id"]
				|| $zpr["tag_id"] ) {

			// Check if author or year is set
			if ( $zpr["year"]
					|| $zpr["author"] ) {

				// Check if author year is set and multiple
				if ( ( $zpr["author"] && strpos( $zpr["author"], "," ) !== false )
						|| ( $zpr["year"] && strpos( $zpr["year"], "," ) !== false ) ) {

					$zp_author_or_year_multiple = $zpr["author"] && strpos( $zpr["author"], "," ) !== false ? "author" : "year";
				}
				else { // Set but not multiple 

					$zp_import_url .= "&qmode=titleCreatorYear";
					// if ( $zpr["author"] ) $zp_import_url .= "&q=".urlencode( $zpr["author"] );
					if ( $zpr["author"] ) $zp_import_url .= "&q=".$zpr["author"];
					if ( $zpr["year"] && ! $zpr["author"] ) $zp_import_url .= "&q=".$zpr["year"];
				}
			}
		}
		elseif ( $zpr["year"]
				|| $zpr["author"] ) {

			$zp_import_url .= "&qmode=titleCreatorYear";

			if ( $zpr["author"] ) {

				// REVIEW: Deal with authors with multi-part last names
				// Replace plus signs with spaces
				if ( strpos( $zpr["author"], "+" ) !== -1 )
					$zpr["author"] = str_replace( '+', ' ', $zpr["author"] );

				if ( $zpr["inclusive"] === false ) {

					$zp_authors = explode( ",", $zpr["author"] );
					// $zp_import_url .= "&q=".urlencode( $zp_authors[0] );
					$zp_import_url .= "&q=".($zp_authors[0]);
					unset( $zp_authors[0] );
					$zpr["author"] = $zp_authors;
				}
				else { // inclusive
				
					// $zp_import_url .= "&q=".urlencode( $zpr["author"] );
					$zp_import_url .= "&q=".$zpr["author"];
				}
			}

			// CHANGED (7.3): For some reason, urlencode will replace apostrophes
			// with &#039; and then encode that to %26%23039%3B
			// which breaks ... so let's replace with %27 manually
			$zp_import_url = str_replace("%26%23039%3B", "%27", $zp_import_url);

			// Deal with just year, no author
			if ( $zpr["year"] 
					&& ! $zpr["author"] ) {

				if ( is_array($zpr["year"]) )
					$zpr["year"] = implode(",", $zpr["year"]);

				$zp_import_url .= "&q=".$zpr["year"];
			}
		}

		// Avoid attachments and notes, if not using itemtype filtering
		if ( ! $zpr["itemtype"]
				&& $zpr["item_type"] == "items"
				|| ( $zpr["sub"] && $zpr["sub"] == "items" ) )
			$zp_import_url .= "&itemType=-attachment+||+note";

		// Deal with possible term
		if ( $zpr["term"] )
			if ( $zpr["filter"] && $zpr["filter"] == "tag")
				$zp_import_url .= "&tag=".urlencode( $wpdb->esc_like($zpr["term"]) );
			else
				$zp_import_url .= "&q=".urlencode( $wpdb->esc_like($zpr["term"]) );


		// DEAL WITH MULTIPLE REQUESTS
		// if ( count($zp_request_queue) > 0 )
		if ( $zp_request_queue
				&& array_key_exists($api_user_id, $zp_request_queue) ) {
			
			// Assume items
			// if ( array_key_exists("requests", $zp_request_queue[$api_user_id])
			// 		&& count($zp_request_queue[$api_user_id]["requests"]) > 1 )
			// Multiple requests
			if ( array_key_exists("requests", $zp_request_queue[$api_user_id] )
					&& count($zp_request_queue[$api_user_id]["requests"]) > 1 ) {
				
				$item_keys = "";
				foreach ( $zp_request_queue[$api_user_id]["requests"] as $num => $request ) {

					if ( $item_keys != "" ) $item_keys .= ",";
					$item_keys .= $request;
				}
				$zp_request_queue[$api_user_id]["requests"][$num] = $zp_import_url . "&itemKey=" . $item_keys;
			}
			elseif ( strpos( $zp_request_queue[$api_user_id]["items"], "," ) !== false ) {

				if ( is_array($zp_request_queue[$api_user_id]["items"]) )
					$zp_request_queue[$api_user_id]["items"] = implode(",", $zp_request_queue[$api_user_id]["items"]);

				$zp_request_queue[$api_user_id]["requests"] = array( $zp_import_url . "&itemKey=" . $zp_request_queue[$api_user_id]["items"] );
			}
			else { // one item
			
				$zp_request_queue[$api_user_id]["requests"] = array( $zp_import_url );
			}
		}
		elseif ( ! $zp_request_queue
			&& $api_user_id) {

			// Assume normal
			$zp_request_queue[$api_user_id]["requests"] = array( $zp_import_url );
		}
		else {

			// Assume broken or no requests
			$zp_request_queue = false;
			// Assume normal
			// $zp_request_queue[$api_user_id]["requests"] = array( $zp_import_url );
		}
	} // count($zp_account) > 0

	else { // account not synced
	
		$zp_request_queue = false;
	}

	return $zp_request_queue;

} // function Zotpress_prep_request_URL



?>
