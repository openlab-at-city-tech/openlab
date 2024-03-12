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
function zp_clean_param( $param )
{
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
function zp_get_year( $date, $yesnd=false )
{
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
function subval_sort( $item_arr, $sortby, $order )
{
	// Format sort order
	$order = strtolower($order) == "desc" ? SORT_DESC : SORT_ASC;

	// Author or date
	if ($sortby == "author" || $sortby == "date") {
     foreach ($item_arr as $key => $val)
   		{
   			$author[$key] = $val["author"];

   			$zpdate = ""; $zpdate = isset( $val["zpdate"] ) ? $val["zpdate"] : $val["date"];

   			$date[$key] = zp_date_format($zpdate);
   		}
 } elseif ($sortby == "title") {
     foreach ($item_arr as $key => $val)
   		{
   			$title[$key] = $val["title"];
   			$author[$key] = $val["author"];
   		}
 }

	// NOTE: array_multisort seems to be ignoring second sort for date->author
	if ($sortby == "author" && isset($author) && is_array($author)) {
     array_multisort( $author, $order, $date, $order, $item_arr );
 } elseif ($sortby == "date" && isset($date) && is_array($date)) {
     array_multisort( $date, $order, $author, SORT_ASC, $item_arr );
 } elseif ($sortby == "title" && isset($title) && is_array($title)) {
     array_multisort( $title, $order, $author, $order, $item_arr );
 }

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
function zp_date_format ($date)
{
	// Set up search lists
	$list_month_long = array ( "01" => "January", "02" => "February", "03" => "March", "04" => "April", "05" => "May", "06" => "June", "07" => "July", "08" => "August", "09" => "September", "10" => "October", "11" => "November", "12" => "December" );
	$list_month_short = array ( "01" => "Jan", "02" => "Feb", "03" => "Mar", "04" => "Apr", "05" => "May", "06" => "Jun", "07" => "Jul", "08" => "Aug", "09" => "Sept", "10" => "Oct", "11" => "Nov", "12" => "Dec" );


	// Check if it's a mm-mm dash
	if ( preg_match("/^[a-zA-Z]+[-][a-zA-Z]+[ ]\\d+\$/", $date ) == 1)
	{
		$temp1 = preg_split( "/-|\//", $date );
		$temp2 = preg_split( "[\s]", $temp1[1] );

		$date = $temp1[0]." ".$temp2[1];
	}

	// If it's already formatted with a dash or forward slash
	if (strpos( $date, "-" ) !== false || strpos( $date, "/" ) !== false) {
     // Break it up
     $temp = preg_split( "/-|\//", $date );
     // If year is last, switch it with first
     if (strlen( $temp[0] ) != 4) {
         // Just month and year
         if ( count( $temp ) == 2 )
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
     } elseif (isset($temp[2])) {
         // day is set
         $date_formatted = array(
     					"year" => $temp[0],
     					"month" => $temp[1],
     					"day" => $temp[2]
     				);
     } else
  			{
  				$date_formatted = array(
  					"year" => $temp[0],
  					"month" => $temp[1],
  					"day" => false
  				);
  			}
 } elseif (strpos( $date, "," )) {
     $date = trim( str_replace( ", ", ",", $date ) );
     $temp = preg_split( "/,| /", $date );
     // Convert month
     $month = array_search( $temp[0], $list_month_long );
     if ( !$month ) $month = array_search( $temp[0], $list_month_short );
     $date_formatted = array(
   			"year" => $temp[2],
   			"month" => $month,
   			"day" => $temp[1]
   		);
 } else
	{
		$date = trim( str_replace( "  ", "-", $date ) );
		$temp = explode ( " ", $date );

		// If there's at least two parts to the date
		if ( $temp !== [] )
		{
			// Check if name is first
			if (!is_numeric( $temp[0] )) {
       if (in_array( $temp[0], $list_month_long )) {
           $date_formatted = array(
      						"year" => $temp[1],
      						"month" => array_search( $temp[0], $list_month_long ),
      						"day" => false
      					);
       } elseif (in_array( $temp[0], $list_month_short )) {
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
   } elseif (count($temp) > 1) {
       if (in_array( $temp[1], $list_month_long )) {
           $date_formatted = array(
     							"year" => $temp[0],
     							"month" => array_search( $temp[1], $list_month_long ),
     							"day" => false
     						);
       } elseif (in_array( $temp[1], $list_month_short )) {
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
   } else // Only one part in the array
				{
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

	if ( !isset($date_formatted) ) $date_formatted = $date;

	return $date_formatted;
}


/**
 * Processes the WP AJAX Zotero request variables.
 * TODO: Is this necessary?
 *
 * Used by: shortcode.php
 *
 * @return string Array with the processed variables.
 */
function Zotpress_prep_ajax_request_vars()
{
	$zpr = array();

	$zpr["limit"] = 50; // max 100, 22 seconds
	$zpr["overwrite_request"] = false;
	$zpr["overwrite_last_request"] = false;

	// Deal with incoming variables
	$zpr["type"] = "basic"; if ( isset($_GET['type']) && $_GET['type'] != "" ) $zpr["type"] = $_GET['type'];
	$zpr["api_user_id"] = isset( $_GET['api_user_id'] ) ? $_GET['api_user_id'] : false;
	$zpr["item_type"] = "items"; if ( isset($_GET['item_type']) && $_GET['item_type'] != "" ) $zpr["item_type"] = $_GET['item_type'];
	$zpr["get_top"] = false; if ( isset($_GET['get_top']) ) $zpr["get_top"] = true;
	$zpr["sub"] = false;
	$zpr["is_dropdown"] = false; if ( isset($_GET['is_dropdown']) && $_GET['is_dropdown'] == "true" ) $zpr["is_dropdown"] = true;
	$zpr["update"] = false; if ( isset($_GET['update']) && $_GET['update'] == "true" ) $zpr["update"] = true;
	$zpr["updateneeded"] = false; if ( isset($_GET['updateneeded']) && $_GET['updateneeded'] == "true" ) $zpr["updateneeded"] = true;
	$zpr["request_update"] = false; if ( isset($_GET['request_update']) && $_GET['request_update'] == "true" ) $zpr["request_update"] = true;

	// instance id, item key, collection id, tag id
	$zpr["instance_id"] = false; if ( isset($_GET['instance_id']) ) $zpr["instance_id"] = $_GET['instance_id'];

	$zpr["item_key"] = false;
	if ( isset($_GET['item_key'])
			&& ( $_GET['item_key'] != "false" && $_GET['item_key'] !== false ) )
		$zpr["item_key"] = $_GET['item_key'];

	// REVIEW: Make sure item_key is formatting in the new style
	//
	// BIB FORMATS:
	// [zotpress item="GMGCJU34"]
	// [zotpress items="GMGCJU34,U9Z5JTKC"]
	if ( $zpr["item_key"] != false
			&& strpos( $zpr["item_key"], ":" ) == false )
	{
		$temp_reformatted = "";
		$temp_items = explode( ",", $zpr["item_key"] );

		foreach ( $temp_items as $item )
			$temp_reformatted .= "{".$zpr["api_user_id"].":".$item."},";

		$zpr["item_key"] = rtrim( $temp_reformatted, ',' );
	}

	$zpr["itemtype"] = false;
	if ( isset($_GET['itemtype'])
			&& ( $_GET['itemtype'] != "false" && $_GET['itemtype'] !== false && $_GET['itemtype'] !== '' ) )
		$zpr["itemtype"] = $_GET['itemtype'];

	$zpr["collection_id"] = false;
	if ( isset($_GET['collection_id'])
			&& ( $_GET['collection_id'] != "false" && $_GET['collection_id'] !== false && $_GET['collection_id'] !== '' ) )
		$zpr["collection_id"] = $_GET['collection_id'];

	$zpr["tag_id"] = false;
	if ( isset($_GET['tag_id'])
			&& ( $_GET['tag_id'] != "false" && $_GET['tag_id'] !== false && $_GET['tag_id'] !== '' ) )
	{
		$zpr["tag_id"] = $_GET['tag_id'];
		// $zpr["collection_id"] = false; // Can have tags in collection
	}

	// Author, year, style, limit, title
	$zpr["author"] = false; if ( isset($_GET['author']) && $_GET['author'] != "false" && $_GET['author'] != "" ) $zpr["author"] = $_GET['author'];
// TESTING: urldecode
	$zpr["author"] = urldecode($zpr["author"]);

	$zpr["year"] = false; if ( isset($_GET['year']) && $_GET['year'] != "false" && $_GET['year'] != "" ) $zpr["year"] = $_GET['year'];
	$zpr["style"] = zp_Get_Default_Style(); if ( isset($_GET['style']) && $_GET['style'] != "false" && $_GET['style'] != "" && $_GET['style'] != "default" ) $zpr["style"] = $_GET['style'];

	// NOTE: With PHP 8, watch for URL params that are strings but need to be ints
	if ( isset($_GET['limit'])
			&& (int) $_GET['limit'] != 0 )
	{
		$zpr["limit"] = (int) $_GET['limit'];
		$zpr["overwrite_request"] = true;
	}

	$zpr["title"] = false; if ( isset($_GET['title']) ) $zpr["title"] = $_GET['title'];

	// Max tags, max results
	$zpr["maxtags"] = false; if ( isset($_GET['maxtags']) ) $zpr["maxtags"] = (int) $_GET['maxtags'];
	$zpr["maxresults"] = false; if ( isset($_GET['maxresults']) ) $zpr["maxresults"] = (int) $_GET['maxresults'];

	// Term, filter
	$zpr["term"] = false; if ( isset($_GET['term']) ) $zpr["term"] = $_GET['term'];
	$zpr["filter"] = false; if ( isset($_GET['filter']) ) $zpr["filter"] = $_GET['filter'];

	// Sorty by, order
	$zpr["sortby"] = false;
	$zpr["order"] = false;
	$zpr["item_keys_order"] = array();

	// Lib toplevel
	// Set UNLESS there's a tag
	$zpr["toplevel"] = false;
	if ( isset($_GET['toplevel'])
			&& $zpr["tag_id"] === false )
	{
		$zpr["toplevel"] = $_GET['toplevel'];
		// $zpr["collection_id"] = false;
	}
	if ( $zpr["collection_id"] == "toplevel" )
		$zpr["collection_id"] = false;

	// SPECIAL SETTINGS

	if ( isset($_GET['sortby']) )
	{
		if ($_GET['sortby'] == "author") {
      $zpr["sortby"] = "creator";
      $zpr["order"] = "asc";
	} elseif ($_GET['sortby'] == "default") {
		$zpr["sortby"] = "default";
		// entry order
	} elseif ($_GET['sortby'] == "year") {
		$zpr["sortby"] = "date";
		$zpr["order"] = "desc";
	} elseif ($zpr["type"] == "intext" && $_GET['sortby'] == "default") {
		$zpr["sortby"] = "default";
	} else {
		$zpr["sortby"] = $_GET['sortby'];
	}
	}
	
	if ( isset($_GET['order'])
			&& ( strtolower($_GET['order']) == "asc" || strtolower($_GET['order']) == "desc" ) )
		$zpr["order"] = strtolower($_GET['order']);

	// Show images, show tags, downloadable, inclusive, notes, abstracts, citeable
	$zpr["showimage"] = false;
	if ( isset($_GET['showimage']) )
		if ( $_GET['showimage'] == "yes" || $_GET['showimage'] == "true"
				|| $_GET['showimage'] === true || $_GET['showimage'] == 1 )
			$zpr["showimage"] = true;
		elseif ( $_GET['showimage'] == "openlib" )
			$zpr["showimage"] = "openlib";

	$zpr["showtags"] = false;
	if ( isset($_GET['showtags'])
			&& ( $_GET['showtags'] == "yes" || $_GET['showtags'] == "true"
					|| $_GET['showtags'] === true || $_GET['showtags'] == 1 ) )
		$zpr["showtags"] = true;

	$zpr["downloadable"] = false;
	if ( isset($_GET['downloadable'])
			&& ( $_GET['downloadable'] == "yes" || $_GET['downloadable'] == "true" || $_GET['downloadable'] === true || $_GET['downloadable'] == 1 ) )
		$zpr["downloadable"] = true;

	$zpr["inclusive"] = false;
	if ( isset($_GET['inclusive'])
			&& ( $_GET['inclusive'] == "yes" || $_GET['inclusive'] == "true" || $_GET['inclusive'] === true || $_GET['inclusive'] == 1 ) )
		$zpr["inclusive"] = true;

	$zpr["shownotes"] = false;
	if ( isset($_GET['shownotes'])
			&& ( $_GET['shownotes'] == "yes" || $_GET['shownotes'] == "true" || $_GET['shownotes'] === true || $_GET['shownotes'] == 1 ) )
		$zpr["shownotes"] = true;

	$zpr["showabstracts"] = false;
	if ( isset($_GET['showabstracts'])
			&& ( $_GET['showabstracts'] == "yes" || $_GET['showabstracts'] == "true" || $_GET['showabstracts'] === true || $_GET['showabstracts'] == 1 ) )
		$zpr["showabstracts"] = true;

	$zpr["citeable"] = false;
	if ( isset($_GET['citeable'])
			&& ( $_GET['citeable'] == "yes" || $_GET['citeable'] == "true" || $_GET['citeable'] === true || $_GET['citeable'] == 1 ) )
		$zpr["citeable"] = true;

	// Target, urlwrap, forcenum
	$zpr["target"] = false;
	if ( isset($_GET['target'])
			&& ( $_GET['target'] == "yes" || $_GET['target'] == "true" || $_GET['target'] === true || $_GET['target'] == 1 ) )
		$zpr["target"] = true;

	$zpr["urlwrap"] = false;
	if ( isset($_GET['urlwrap']) && ( $_GET['urlwrap'] == "title" || $_GET['urlwrap'] == "image" ) )
		$zpr["urlwrap"] = $_GET['urlwrap'];

	$zpr["highlight"] = false;
	if ( isset($_GET['highlight'])
			&& $_GET['highlight'] !== ""
			&& $_GET['highlight'] !== false
			&& $_GET['highlight'] !== 0
		 	&& $_GET['highlight'] !== "false" ) $zpr["highlight"] = trim( htmlentities( $_GET['highlight'] ) );

	$zpr["forcenumber"] = false;
	if ( isset($_GET['forcenumber'])
			&& ( $_GET['forcenumber'] == "yes" || $_GET['forcenumber'] == "true" || $_GET['forcenumber'] === true || $_GET['forcenumber'] == 1 ) )
		$zpr["forcenumber"] = true;

	$zpr["request_start"] = 0; if ( isset($_GET['request_start']) ) $zpr["request_start"] = (int) $_GET['request_start'];
	$zpr["request_last"] = 0; if ( isset($_GET['request_last']) ) $zpr["request_last"] = (int) $_GET['request_last'];

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
function Zotpress_prep_request_URL( $wpdb, $zpr, $zp_request_queue, $api_user_id=false, $zp_request_data=false )
{
	$tempItemType = "";

	// Get account and $api_user_id
	if ($api_user_id) {
		$zp_account = zp_get_account ($wpdb, $api_user_id);
	} elseif ($zpr["api_user_id"]) {
		$zp_account = zp_get_account ($wpdb, $zpr["api_user_id"]);
		$api_user_id = $zpr["api_user_id"];
	} else {
		$zp_account = zp_get_account ($wpdb);
		$api_user_id = $zp_account[0]->api_user_id;
	}

	// Make sure account was founded (is synced)
	if ( count($zp_account) > 0 )
	{
		// Basic URL: User type, user id, item type
		$zp_import_url = "https://api.zotero.org/".$zp_account[0]->account_type."/".$api_user_id."/".$zpr["item_type"];

	    // Deal with item type Items
	    if ( $zpr['item_type'] == 'items' )
	    {
	    	// Account for single item with new style
	    	if (gettype( $zpr["item_key"] ) == "string"
					&& strlen($zpr["item_key"]) > 0
	    			&& $zpr["item_key"][0] == "{")
			{
				$zpr_temp = explode(':', $zpr["item_key"]);
				$zpr_temp = count($zpr_temp) > 1 ? $zpr_temp[1] : $zpr_temp[0];
				$zpr["item_key"] = rtrim( $zpr_temp, "}");
				// Account for page numbers
				if ( strpos( $zpr["item_key"], ',' ) )
				{
					$zpr_temp = explode(',', $zpr["item_key"]);
					$zpr["item_key"] = rtrim( $zpr_temp[0], "}");
				}
      			} elseif (gettype( $zpr["item_key"] ) == "array"
	    			&& count( $zpr["item_key"] ) == 1
	    			&& $zpr["item_key"][0][0] == "{")
				{
					// $zpr["item_key"] = rtrim( explode(':', $zpr["item_key"][0])[1] , "}");
					$zpr["item_key"] = ltrim( rtrim( $zpr["item_key"][0], "}" ), "{" );
					
					// Account for page numbers
					if ( strpos( $zpr["item_key"], ',' ) )
      				{
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
				)
			{
				if (isset( $zp_request_data["items"] )) {
					$zp_import_url .= "/" . $zp_request_data["items"];
				} elseif (gettype( $zpr["item_key"] ) == "array"
						&& count( $zpr["item_key"] ) == 1
						&& strpos( $zpr["item_key"][0], ',' ) == false) {
					$zp_import_url .= "/" . $zpr["item_key"][0];
				} elseif (gettype( $zpr["item_key"] ) == "string"
						&& ( strpos( $zpr["item_key"], "," ) === false
						&& strpos( $zpr["item_key"], ";" ) === false )) {
					$zp_import_url .= "/" . $zpr["item_key"];
				}
	    	}
			if ( $zpr["collection_id"] ) $zp_import_url .= "/" . $zpr["collection_id"];
			if ( $zpr["sub"] ) $zp_import_url .= "/" . $zpr["sub"];
			
			$zp_import_url .= "?";

			// Public key, if needed
			if (!is_null($zp_account[0]->public_key) && trim($zp_account[0]->public_key) != "")
				$zp_import_url .= "key=".$zp_account[0]->public_key."&";

			// Style
			$zp_import_url .= "style=".$zpr["style"];

			// Format, limit, etc.
			$zp_import_url .= "&format=json&include=data,bib&limit=".$zpr["limit"];

			// Sort and order
			if ( $zpr["sortby"] && $zpr["sortby"] != "default" )
			{
				$zp_import_url .= "&sort=".$zpr["sortby"];
				if ( $zpr["order"] ) $zp_import_url .= "&direction=".$zpr["order"];
			}

			// Start if multiple
			if ( $zpr["request_start"] != 0 ) $zp_import_url .= "&start=".$zpr["request_start"];

		// Multiple item keys
		// EVENTUAL TODO: Limited to 50 item keys at a time ... can I get around this?
		// TODO: Test this with a bib that has 50+ items
		// if ( $zpr["item_key"] && strpos( $zpr["item_key"],"," ) !== false ) $zp_import_url .= "&itemKey=" . $zpr["item_key"];
	    if ( $zp_request_data )
	    {
	        if ( substr_count($zp_request_data["items"], ",") >= 50 )
	    	{
				// Split items by comma
	    		$items = explode( ",", $zp_request_data["items"] );

	    		$requests = array();
	    		$request_items = array();

	    		foreach ( $items as $item ) {
	    			if ( count($request_items) < 50 ) {
	    				$request_items[] = $item;
	    			}
	    			else {
	    				$requests[] = $request_items;
	    				unset( $request_items );
	    			}
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
		{
			$zp_import_url .= "&itemType=" . urlencode( stripslashes( $zpr["itemtype"] ));
		}

		// Tag-specific
		if ( $zpr["tag_id"] )
		{
			if ( strpos($zpr["tag_id"], ",") !== false )
			{
				$temp = explode( ",", $zpr["tag_id"] );

				foreach ( $temp as $temp_tag )
				{
					$zp_import_url .= "&tag=" . urlencode( stripslashes( $temp_tag ));
				}
			}
			else
			{
				$zp_import_url .= "&tag=" . urlencode( stripslashes( $zpr["tag_id"] ));
			}
		}

		// Filtering: collections and tags take priority over authors and year
		// EVENTUAL TODO: Searching by two+ values is not supported on the Zotero side ...
		// For now, we get all and manually filter below
		$zp_author_or_year_multiple = false;

		if ($zpr["collection_id"] || $zpr["tag_id"]) {

			// Check if author or year is set
			if ( $zpr["year"] || $zpr["author"] )
			{
				// Check if author year is set and multiple
				if ( ( $zpr["author"] && strpos( $zpr["author"], "," ) !== false )
						|| ( $zpr["year"] && strpos( $zpr["year"], "," ) !== false ) )
				{
					$zp_author_or_year_multiple = $zpr["author"] && strpos( $zpr["author"], "," ) !== false ? "author" : "year";
				}
				else // Set but not multiple
				{
					$zp_import_url .= "&qmode=titleCreatorYear";
					if ( $zpr["author"] ) $zp_import_url .= "&q=".urlencode( $zpr["author"] );
					if ( $zpr["year"] && ! $zpr["author"] ) $zp_import_url .= "&q=".$zpr["year"];
				}
			}

		} elseif ($zpr["year"] || $zpr["author"]) {

			$zp_import_url .= "&qmode=titleCreatorYear";
			if ( $zpr["author"] )
			{
			// REVIEW: Deal with authors with multi-part last names
			// Replace plus signs with spaces
			if ( strpos( $zpr["author"], "+" ) !== -1 )
				$zpr["author"] = str_replace( '+', ' ', $zpr["author"] );

			if ( $zpr["inclusive"] === false )
			{
				$zp_authors = explode( ",", $zpr["author"] );
				$zp_import_url .= "&q=".urlencode( $zp_authors[0] );
				unset( $zp_authors[0] );
				$zpr["author"] = $zp_authors;
			}
			else // inclusive
			{
				$zp_import_url .= "&q=".urlencode( $zpr["author"] );
			}
		}
		// CHANGED (7.3): For some reason, urlencode will replace apostrophes
		// with &#039; and then encode that to %26%23039%3B
		// which breaks ... so let's replace with %27 manually
		$zp_import_url = str_replace("%26%23039%3B", "%27", $zp_import_url);
		// Deal with just year, no author
		if ( $zpr["year"] && ! $zpr["author"] ) $zp_import_url .= "&q=".$zpr["year"];
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
	if ($zp_request_queue
			&& array_key_exists($api_user_id, $zp_request_queue)) {
		// Assume items
		// if ( array_key_exists("requests", $zp_request_queue[$api_user_id])
		// 		&& count($zp_request_queue[$api_user_id]["requests"]) > 1 )
		// Multiple requests
		if (array_key_exists("requests", $zp_request_queue[$api_user_id])
   					&& count($zp_request_queue[$api_user_id]["requests"]) > 1) {
          	$item_keys = "";
          	foreach ( $zp_request_queue[$api_user_id]["requests"] as $num => $request ) {
				if ( $item_keys != "" ) $item_keys .= ",";
				$item_keys .= $request;
			}
          $zp_request_queue[$api_user_id]["requests"][$num] = $zp_import_url . "&itemKey=" . $item_keys;
      	} elseif (strpos( $zp_request_queue[$api_user_id]["items"], "," ) !== false) {
          	if ( is_array($zp_request_queue[$api_user_id]["items"]) )
     						$zp_request_queue[$api_user_id]["items"] = implode(",", $zp_request_queue[$api_user_id]["items"]);
          	$zp_request_queue[$api_user_id]["requests"] = array( $zp_import_url . "&itemKey=" . $zp_request_queue[$api_user_id]["items"] );
      	} else // one item
  				{
  					$zp_request_queue[$api_user_id]["requests"] = array( $zp_import_url );
  				}
		} elseif (! $zp_request_queue
						&& $api_user_id) {
			// Assume normal
			$zp_request_queue[$api_user_id]["requests"] = array( $zp_import_url );
		} else
		{
			// Assume broken or no requests
			$zp_request_queue = false;
			// Assume normal
			// $zp_request_queue[$api_user_id]["requests"] = array( $zp_import_url );
		}
	} // count($zp_account) > 0

	else // account not synced
	{
		$zp_request_queue = false;
	}

	return $zp_request_queue;

} // function Zotpress_prep_request_URL



?>
