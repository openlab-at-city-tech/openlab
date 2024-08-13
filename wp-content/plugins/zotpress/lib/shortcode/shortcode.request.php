<?php

 /**
  * Processes the Zotero request. Accepts WP AJAX or PHP calls.
  *
  * Used by: shortcode.php
  *
  * @param bol $is_ajax Whether it's an WP AJAX call.
  *
  * @return str JSON with: (a) meta about request, and (b) all data for this request.
  */
function Zotpress_shortcode_request( $checkcache = false )
{
	$is_ajax = isset($_GET['zpShortcode_nonce']);

	if ( $is_ajax )
		check_ajax_referer( 'zpShortcode_nonce_val', 'zpShortcode_nonce' );

	// Set up database
	global $wpdb;
	global $post;

	// Prep request vars
	$zpr = Zotpress_prep_ajax_request_vars();

	// Include relevant classes and functions
	include( dirname(__FILE__) . '/../request/request.class.php' );
	include( dirname(__FILE__) . '/../request/request.functions.php' );

	// Set up request queue (for items)
	// Structure: [api_user_id] => [items], [requests]
	$zp_request_queue = array();

	// Set up Zotpress request
	$zp_import_contents = new ZotpressRequest();

	// Set up request meta
	$zp_request_meta = array( "request_last" => (int) $zpr["request_last"], "request_next" => 0 );

	// Set up data variable
	$zp_all_the_data = array();



	// +---------------------------+
	// | Format Zotero request URL |
	// +---------------------------+

	// Account for items + collection_id
	if ( $zpr["item_type"] == "items"
			&& $zpr["collection_id"] !== false )
	{
		$zpr["item_type"] = "collections";
		$zpr["sub"] = "items";
		$zpr["get_top"] = false;
	}

	// Account for items + zp_tag_id
	if ( $zpr["item_type"] == "items"
			&& $zpr["tag_id"] !== false )
		$zpr["get_top"] = false;

	// Account for collection_id + get_top
	if ( $zpr["get_top"]
			&& $zpr["collection_id"] !== false )
	{
		$zpr["get_top"] = false;
		$zpr["sub"] = "collections";
	}

	// Account for tag display - let's limit it
	if ( $zpr["is_dropdown"] === true
			&& $zpr["item_type"] == "tags" )
	{
		$zpr["sortby"] = "numItems"; // title
		$zpr["order"] = "desc"; // asc
		$zpr["limit"] = 100; if ( $zpr["maxtags"] ) $zpr["limit"] = $zpr["maxtags"];
		$zpr["overwrite_last_request"] = $zpr["limit"]; // QUESTION: Unsure
		$zpr["overwrite_request"] = true;

		// REVIEW: we don't want tags within a collection,
		// accounting for Library Dropdown browse bar (7.3.1.1)
		// $zpr["get_top"] = false;
		// $zpr["collection_id"] = false;
	}

	// Account for $zpr["maxresults"]
	// TODO: The Help says this is only for the searchbar ..........
	// TEST: Isn't this only for the library? 
	if ( $zpr["is_dropdown"] === true
			&&  $zpr["maxresults"] )
	{
		// If 50 or less, set as limit
		if ( (int) $zpr["maxresults"] <= 50 )
		{
			$zpr["limit"] = $zpr["maxresults"];
			$zpr["overwrite_request"] = true;
		}

		// If over 50, then overwrite last_request
		else
		{
			$zpr["overwrite_last_request"] = $zpr["maxresults"];
		}
	}


	// Handle the possible formats of item/s for bib and in-text
	// REVIEW: Actually, removed page numbers (if any) in shortcode.intext.php
	//
	// IN-TEXT FORMATS:
	// [zotpressInText item="NCXAA92F"]
	// [zotpressInText item="{NCXAA92F}"]
	// [zotpressInText item="{NCXAA92F,10-15}"]
	// [zotpressInText items="{NCXAA92F,10-15},{55MKF89B,1578},{3ITTIXHP}"]
	// [zotpressInText items="{000001:NCXAA92F,10-15},{000003:3ITTIXHP}"]
	// [zotpressInText items="{000001:NCXAA92F,10-15},{000003:3ITTIXHP,(33,36)}"]
	// So no multiples without curlies or non-curlies in multiples
	//
	// BIB FORMATS:
	// [zotpress item="GMGCJU34"]
	// [zotpress items="GMGCJU34,U9Z5JTKC"]
	// [zotpress item="{000001:XH4BS8MA},{000001:CN73PTWE},{000003:CZR96TX9}"]
	// BASICALLY: Create a list of unique item keys per API User ID
	// REVIEW: Deal with everything!!!!!!! In the new format
	if ( $zpr["item_key"]
			&& strpos( $zpr["item_key"], ":" ) !== false )
	{
		$zp_items = preg_split( "/((;)|(,))+/", $zpr["item_key"] );

     	foreach ( $zp_items as $id => $zp_item_data )
		{
			$zp_item = explode( ":", substr( $zp_item_data, 1, -1 ) );

			// Create items queue for API User ID, if it doesn't exist
			if ( ! array_key_exists( $zp_item[0], $zp_request_queue ) )
				$zp_request_queue[$zp_item[0]]["items"] = "";

			// Add item if not in queue
			if ( strpos( $zp_request_queue[$zp_item[0]]["items"], $zp_item[1] ) === false )
			{
				$temp_item_key = $zp_item[1];

				if ( strlen( $zp_request_queue[$zp_item[0]]["items"] ) != 0 )
					$temp_item_key = ",".$temp_item_key;

				$zp_request_queue[$zp_item[0]]["items"] .= $temp_item_key;
			}
		}
	}



	// +--------------------+
	// | Build request URLs |
	// +--------------------+

	if ( $zp_request_queue !== [] )
	{
		// REVIEW: Does setting $zp_request_queue here overwrite it for each account?
		foreach ( $zp_request_queue as $api_user_id => $zp_request_data )
			$zp_request_queue = Zotpress_prep_request_URL( $wpdb, $zpr, $zp_request_queue, $api_user_id, $zp_request_data );
	}
	else
	{
		$zp_request_queue = Zotpress_prep_request_URL( $wpdb, $zpr, $zp_request_queue );
	}




	// +---------+
	// | TESTING |
	// +---------+

	// var_dump($zp_request_queue);exit;

    // if ( $zpr["request_start"] == 50 ) {
    //    var_dump("shortcode.request.php TESTING: ");
    //    print_r($_GET); var_dump("<br /><br />url: ".$zp_import_url);
    //    var_dump(" AFTER \n\n");
    // }






	// +------------------+
	// | Request the data |
	// +------------------+

	$zp_request = array();
	$zp_error = false;
	$zp_usecache = false;
	$zp_updateneeded = false;

	// Account for missing/unsynced accounts or no requests
	if ( $zp_request_queue === false )
		$zp_error = "Zotpress account not found.";

	if ( ! $zp_error )
	{
		foreach ( $zp_request_queue as $zp_request_account )
		{
			// Multiple/more than one request
			if ( count($zp_request_account["requests"]) > 1 )
			{
				foreach ( $zp_request_account["requests"] as $zp_request_url )
				{
					// Check the cache with PHP
					if ( $checkcache && ! $zpr["request_update"] )
					{
						$zp_checkcache = $zp_import_contents->get_request_cache( $zp_request_url, $zpr["update"] );
						$zp_checkcache_json = json_decode( $zp_checkcache['json'] );

						if ( gettype($zp_checkcache_json) != 'array'
								&& property_exists($zp_checkcache_json, 'status')
								&& $zp_checkcache_json->status == 'No Cache' )
						{
							// $zp_usecache = false;
						}
						else // Continue as normal with cache
						{
							$zp_imported = $zp_checkcache;
							$zp_usecache = true;
						}
					}
					else // Otherwise, assume JS Ajax
					{
						$zp_imported = $zp_import_contents->get_request_contents( $zp_request_url, $zpr["update"] );

						if ( $zp_imported["updateneeded"] )
							$zp_updateneeded = true;
					}

					// Stop and let JS Ajax take over
					if ( $checkcache && ! $zp_usecache )
						continue;

					// Deal with possible errors
					if ( gettype($zp_imported) == "string"
					 		&& substr($zp_imported, 0, 5) == "Error" )
					{
						$zp_error = substr($zp_imported, 7, -1);
						continue;
					}

					// Create all-requests json if doesn't exists
					if ( empty($zp_request) )
						$zp_request = $zp_imported;

					// Add to existing all-requests json
					$zp_request["json"] = rtrim($zp_request["json"], "]") . "," . $zp_imported["json"] . "]";
				}
			}

			// Just one request
			else
			{
				// First, check the cache with PHP
				if ( $checkcache 
						&& ! $zpr["request_update"] )
				{
					$zp_checkcache = $zp_import_contents->get_request_cache( $zp_request_account["requests"][0], $zpr["update"] );
					$zp_checkcache_json = json_decode( $zp_checkcache['json'], false );

					if ( gettype($zp_checkcache_json) != 'array'
							&& property_exists($zp_checkcache_json, 'status')
							&& $zp_checkcache_json->status == 'No Cache' )
					{
						// $zp_usecache = false;
					}
					else // Continue as normal with cache
					{
						$zp_imported = $zp_checkcache;
						$zp_usecache = true;
					}

					// if ( $zp_checkcache["updateneeded"] )
					// 	$zp_updateneeded = true;

				}
				else if ( $zpr["request_update"] )
				{
					$zp_imported = $zp_import_contents->get_request_update( $zp_request_account["requests"][0], $zpr["update"] );
				}
				else // Otherwise, assume JS AJAX
				{
					$zp_imported = $zp_import_contents->get_request_contents( $zp_request_account["requests"][0], $zpr["update"] );

					if ( $zp_imported["updateneeded"] )
						$zp_updateneeded = true;
				}

				// Stop and let JS Ajax take over
				if ( ( $checkcache 
						&& ! $zp_usecache ) )
					continue;

				// Deal with possible error
				if ( gettype($zp_imported) == "string"
				 		&& substr($zp_imported, 0, 5) == "Error" )
				{
        			$zp_error = substr($zp_imported, 7, -1);
				}

				// Create all-requests json if doesn't exists
				else
				{
					if ( empty($zp_request) )
					{
						$zp_request = $zp_imported;
					}

					else // Add to existing all-requests json
					{
						$zp_request["json"] = rtrim($zp_request["json"], "]") . "," . ltrim($zp_imported["json"], "[") . "]";
					}
				}

				if ( $zp_request["json"] == "Not found" )
					$zp_error = $zp_request["json"];
				
    			// } elseif ( empty($zp_request) ) {
			    //     $zp_request = $zp_imported;
			    // } else // Add to existing all-requests json
				// {
				// 	$zp_request["json"] = rtrim($zp_request["json"], "]") . "," . ltrim($zp_imported["json"], "[") . "]";
				// }
			} // Just one request
		} // Request the data (foreach)
	} // If no error

	// Fix formatting quirk
	if ( ( ! $checkcache && ! $zp_error )
			|| ( $checkcache && $zp_usecache && ! $zp_error ) )
		$zp_request["json"] = str_replace("}}]]", "}}]", $zp_request["json"]);

	if ( ( ! $checkcache && ( ! $zp_error && $zp_request["json"] != "0" ) )
	 		|| ( $checkcache && $zp_usecache && ( ! $zp_error && $zp_request["json"] != "0" ) ) )
	{
		// Decode the JSONs
		// Thanks to Adnreea Onica @ StackOverflow
		$temp_headers = json_decode( $zp_request["headers"] );
		// $temp_headers = json_encode( (array)$zp_request["headers"] );
		// $temp_headers = json_decode( str_replace('\u0000*\u0000','', $temp_headers) );

		// $temp_data = json_encode( (array)$zp_request["json"] );
		// $temp_data = json_decode( str_replace('\u0000*\u0000','', $temp_data) );
		$temp_data = json_decode( $zp_request["json"] );

		// Figure out if there's multiple requests and how many
		// if ( $zpr["request_start"] == 0
		// 		&& ( property_exists($temp_headers, 'link')
		// 				&& $temp_headers->link !== null )
		// 		&& strpos( $temp_headers->link, 'rel="last"' ) !== false )

		if ( $zpr["request_start"] == 0
				&& isset( $temp_headers->link )
				&& strpos( $temp_headers->link, 'rel="last"' ) !== false )
		{
			$temp_link = explode( ";", $temp_headers->link );
			$temp_link = explode( "start=", $temp_link[1] );
			$temp_link = explode( "&", $temp_link[1] );

			// // FIX: Accounted for limit ...?
			// if ( $zpr["limit"] ) {
			// 	$zp_request_meta["request_last"] = (int) $zpr["limit"];
			// }
			// else {
				$zp_request_meta["request_last"] = (int) $temp_link[0];
			// }
		}
		
		// Figure out the next starting position for the next request, if any
		// 7.3.3: Changed from >= to >
		if ( $zp_request_meta["request_last"] >= ($zpr["request_start"] + $zpr["limit"]) ) {

			// 7.3.9: Only if next is greater than limit
			$zp_request_meta["request_next"] = $zpr["request_start"] + $zpr["limit"];
		}

		// Overwrite request if limit
		// 7.3.3: Fix for collections?
		if ( ( $zpr["item_type"] == "items" || $zpr["item_type"] == "collections" )
				&& $zpr["overwrite_request"] === true )
		{
			$zp_request_meta["request_next"] = 0;
			$zp_request_meta["request_last"] = 0;
		}

		// Overwrite last_request
		if ( $zpr["overwrite_last_request"] )
		{
			// Make sure it's less than the total available items
			if ( isset( $temp_headers->{"total-results"} )
					&& $temp_headers->{"total-results"} < $zpr["overwrite_last_request"] )
				$zpr["overwrite_last_request"] = (int) (ceil( (int) $temp_headers->{"total-results"} / $zpr["limit"] ) - 1) * $zpr["limit"];
			else
				$zpr["overwrite_last_request"] = (int) ceil( $zpr["overwrite_last_request"] / $zpr["limit"] ) * $zpr["limit"];

			$zp_request_meta["request_last"] = $zpr["overwrite_last_request"];
		}


		// +-----------------+
		// | Format the data |
		// +-----------------+

		if ( count($temp_data) > 0 )
		{
			// If single, place the object into an array
			if ( gettype($temp_data) == "object" )
			{
				$temp = $temp_data;
				$temp_data = array();
				$temp_data[0] = $temp;
			}

			// Set up conditional vars
			if ( $zpr["shownotes"] ) $zp_notes_num = 1;
			if ( $zpr["showimage"] ) $zp_showimage_keys = "";

			// Get individual items
			foreach ( $temp_data as $item )
			{
				// Set target for links
				$zp_target_output = "";
				if ( $zpr["target"] )
					$zp_target_output = "target='_blank' ";

				// Author filtering: skip non-matching authors
				// TODO: Breaking with multi name
				// EVENTUAL TODO: Zotero API 3 searches title and author, so wrong authors can appear
				if ( $zpr["author"]
						&& count($item->data->creators) > 0 )
				{
					$zp_authors_check = false;

					// 7.3.10: CHECK: Have to replace the apostrophe entity ...
					$zpr["author"] = str_replace('#039;', "'", $zpr["author"]);
					// var_dump($zpr["author"]);

					// Deal with multiple authors
					if ( gettype($zpr["author"]) != "array"
							&& strpos($zpr["author"], ",") !== false )
					{
						$zp_authors = explode( ",", $zpr["author"] );

						foreach ( $zp_authors as $author )
							if ( zp_check_author_continue( $item, $author ) === true )
								$zp_authors_check = true;
					}

					// Single author or inclusive
					else
					{
						if ( $zpr["inclusive"] === false )
						{
							// 7.3.10: CHECK: Why is this assuming 1? Setting to 0:
							$author_exists_count = 0;

							// 7.3.10: CHECK: Not always an array ... 
							if ( is_array($zpr["author"]) ) {

								foreach ( $zpr["author"] as $author )
									if ( zp_check_author_continue( $item, $author ) === true )
										$author_exists_count++;
								
								// if ( $author_exists_count == count($zpr["author"]) +1 )
								if ( $author_exists_count == count($zpr["author"]) )
									$zp_authors_check = true;
							}
							else { // Just a string/single author
								// var_dump($item,$zpr["author"]);exit;
								if ( zp_check_author_continue( $item, $zpr["author"] ) === true )
									$zp_authors_check = true;
							}
							
						}
						else // inclusive and single
						{
							if ( zp_check_author_continue( $item, $zpr["author"] ) === true )
								$zp_authors_check = true;
						}

						// } elseif (zp_check_author_continue( $item, $zpr["author"] ) === true) {
						// 	$zp_authors_check = true;
						// }
					}

					// var_dump("HUM");exit;

					if ( $zp_authors_check === false )
						continue;
				} // author

				// Year filtering: skip non-matching years
				if ( $zpr["year"]
						&& property_exists($item->meta, "parsedDate") )
				{
					// multiple
					if ( strpos($zpr["year"], ",") !== false )
					{
				        $zp_years_check = false;
				        $zp_years = explode( ",", $zpr["year"] );

				        foreach ( $zp_years as $year )
						 	if ( zp_get_year( $item->meta->parsedDate ) == $year )
								$zp_years_check = true;

				        if ( ! $zp_years_check )
							continue;
					}
					else // single
					{
						if ( zp_get_year( $item->meta->parsedDate ) != $zpr["year"] )
							continue;
					}
				     // } elseif (zp_get_year( $item->meta->parsedDate ) != $zpr["year"]) {
				     //     continue;
				     // }
				}

				// Skip non-matching years for author-year pairs
				// if ( $zpr["year"] && $zpr["author"] && (property_exists($item->meta, 'parsedDate') && $item->meta->parsedDate !== null) && zp_get_year( $item->meta->parsedDate ) != $zpr["year"] )
				if ( $zpr["year"]
						&& $zpr["author"]
						&& isset($item->meta->parsedDate) )
					continue;

				// Add item key for show image
				if ( $zpr["showimage"] ) $zp_showimage_keys .= " ".$item->key;

				// Modify style based on language
				// Languages: jp
				if ( isset($item->data->language)
						// && $item->data->language !== null
						// && $item->data->language != ""
						&& $item->data->language == "ja" )
				{
					// Change ", and " to comma
					$item->bib = str_ireplace(", and ", ", ", $item->bib);

					// Remove "In "
					$item->bib = str_ireplace("In ", "", $item->bib);
				}

				// Hyperlink or URL Wrap
				if ( isset($item->data->url)
					// && $item->data->url !== null
					&& strlen($item->data->url) > 0 )
				{
					if ( $zpr["urlwrap"]
						&& $zpr["urlwrap"] == "title"
						&& $item->data->title )
					{
						// First: Get rid of text URL if it appears as text in the citation:
						// REVIEW: Does this account for all citation styles?
						/* chicago-author-date */ $item->bib = str_ireplace( htmlentities($item->data->url."."), "", $item->bib ); // Note the period
						/* APA */ $item->bib = str_ireplace( htmlentities($item->data->url), "", $item->bib );
						$item->bib = str_ireplace( " Retrieved from ", "", $item->bib );
						$item->bib = str_ireplace( " Available from: ", "", $item->bib );


						// Next, get rid of double space characters (two space characters next to each other):
						$item->bib = preg_replace( '/&#xA0;/', ' ', preg_replace( '/[[:blank:]]+/', ' ', $item->bib ) );
						$item->data->title = preg_replace( '/&#xA0;/', ' ', preg_replace( '/[[:blank:]]+/', ' ', $item->data->title ) );


						// Next, replace space entities with real spaces:
						$item->bib = str_ireplace("&nbsp;", " ", $item->bib );
						$item->data->title = str_ireplace("&nbsp;", " ", $item->data->title );


						// Next, replace entity quotes:
						$item->bib = str_ireplace( "&ldquo;", "&quot;",
										str_ireplace( "&rdquo;", "&quot;",
												htmlentities(
													html_entity_decode( $item->bib, ENT_QUOTES, "UTF-8" ),
													ENT_QUOTES,
													"UTF-8"
												)
											)
									);
						$item->data->title = str_ireplace( "&ldquo;", "&quot;",
										str_ireplace( "&rdquo;", "&quot;",
												htmlentities(
													html_entity_decode( $item->data->title, ENT_QUOTES, "UTF-8" ),
													ENT_QUOTES,
													"UTF-8"
												)
											)
									);


						// Next, replace special Word characters:
						// Thanks to Walter Tross @ Stack Overflow; CC BY-SA 3.0: https://creativecommons.org/licenses/by-sa/3.0/
						$chr_map = array(
							"\xC2\x82" => "'",			// U+0082U+201A single low-9 quotation mark
							"\xC2\x84" => '"',			// U+0084U+201E double low-9 quotation mark
							"\xC2\x8B" => "'",			// U+008BU+2039 single left-pointing angle quotation mark
							"\xC2\x91" => "'",			// U+0091U+2018 left single quotation mark
							"\xC2\x92" => "'",			// U+0092U+2019 right single quotation mark
							"\xC2\x93" => '"',			// U+0093U+201C left double quotation mark
							"\xC2\x94" => '"',			// U+0094U+201D right double quotation mark
							"\xC2\x9B" => "'",			// U+009BU+203A single right-pointing angle quotation mark
							"\xC2\xAB" => '"',			// U+00AB left-pointing double angle quotation mark
							"\xC2\xBB" => '"',			// U+00BB right-pointing double angle quotation mark
							"\xE2\x80\x98" => "'",	// U+2018 left single quotation mark
							"\xE2\x80\x99" => "'",	// U+2019 right single quotation mark
							"\xE2\x80\x9A" => "'",	// U+201A single low-9 quotation mark
							"\xE2\x80\x9B" => "'",	// U+201B single high-reversed-9 quotation mark
							"\xE2\x80\x9C" => '"',	// U+201C left double quotation mark
							"\xE2\x80\x9D" => '"',	// U+201D right double quotation mark
							"\xE2\x80\x9E" => '"',	// U+201E double low-9 quotation mark
							"\xE2\x80\x9F" => '"',	// U+201F double high-reversed-9 quotation mark
							"\xE2\x80\xB9" => "'",	// U+2039 single left-pointing angle quotation mark
							"\xE2\x80\xBA" => "'"	// U+203A single right-pointing angle quotation mark
						);
						$chr = array_keys( $chr_map );
						$rpl = array_values( $chr_map );
						$item->bib = str_ireplace( $chr, $rpl, html_entity_decode( $item->bib, ENT_QUOTES, "UTF-8" ) );
						$item->data->title = str_ireplace( $chr, $rpl, html_entity_decode( $item->data->title, ENT_QUOTES, "UTF-8" ) );

						// Re-encode for foreign characters, but don't encode quotes:
						$item->bib = htmlentities( $item->bib, ENT_NOQUOTES, "UTF-8" );
						$item->data->title = htmlentities( $item->data->title, ENT_NOQUOTES, "UTF-8" );


						// Next, prep title:
						// $item->data->title = htmlentities( $item->data->title, ENT_COMPAT, "UTF-8" );


						// If wrapping title, wrap it:
						$item->bib = str_ireplace(
								$item->data->title,
								"<a ".$zp_target_output."href='".$item->data->url."'>".$item->data->title."</a>",
								$item->bib
							);

						// Finally, revert bib entities:
						$item->bib = html_entity_decode( $item->bib, ENT_QUOTES, "UTF-8" );
						$item->data->title = html_entity_decode( $item->data->title, ENT_QUOTES, "UTF-8" );

					}
					else // Just hyperlink the URL text
					{
						$item->bib = str_ireplace(
								htmlentities($item->data->url),
								"<a ".$zp_target_output."href='".$item->data->url."'>".$item->data->url."</a>",
								$item->bib
							);
					}
				} // Hyperlink or URL wrap

				// Hyperlink DOIs
				if ( isset($item->data->DOI)
						// && $item->data->DOI !== null
						&& strlen($item->data->DOI) > 0 )
				{
					// Styles without http
					if ( strpos( $item->bib, "doi:" ) !== false
                            && strpos( $item->bib, "doi.org" ) == false )
					{
         				$item->bib = str_ireplace(
   								"doi:" . $item->data->DOI,
   								"<a ".$zp_target_output."href='http://doi.org/".$item->data->DOI."'>http://doi.org/".$item->data->DOI."</a>",
   								$item->bib
   							);
     				}
					// Styles with http
					elseif ( strpos( $item->bib, "http://doi.org/" ) !== false
                            && strpos( $item->bib, "</a>" ) == false )
					{
         				$item->bib = str_ireplace(
   								"http://doi.org/" . $item->data->DOI,
   								"<a ".$zp_target_output."href='http://doi.org/".$item->data->DOI."'>http://doi.org/".$item->data->DOI."</a>",
   								$item->bib
   							);
					}
					// HTTPS format
     				elseif ( strpos( $item->bib, "https://doi.org/" ) !== false
                            && strpos( $item->bib, "</a>" ) == false )
					{
         				$item->bib = str_ireplace(
   								"https://doi.org/" . $item->data->DOI,
   								"<a ".$zp_target_output."href='https://doi.org/".$item->data->DOI."'>https://doi.org/".$item->data->DOI."</a>",
   								$item->bib
   							);
 					}
				}

				// Cite link (RIS)
				if ( $zpr["citeable"] ) {

					// REVIEW: Why is this needed? Why is api_user_id empty sometimes?
					$tempUserId = '';
					if ( isset($api_user_id) )
						$tempUserId = $api_user_id;
					else 
						$tempUserId = $zpr["api_user_id"];
					
					$item->bib = preg_replace( '~(.*)' . preg_quote('</div>', '~') . '(.*?)~', '$1' . " <a title='Cite in RIS Format' class='zp-CiteRIS' href='".ZOTPRESS_PLUGIN_URL."lib/request/request.cite.php?api_user_id=".$tempUserId."&amp;item_key=".$item->key."'>Cite</a> </div>" . '$2', $item->bib, 1 );
					// $item->bib = preg_replace( '~(.*)' . preg_quote('</div>', '~') . '(.*?)~', '$1' . " <a title='Cite in RIS Format' class='zp-CiteRIS' href='".ZOTPRESS_PLUGIN_URL."lib/request/request.cite.php?api_user_id=".$zpr["api_user_id"]."&amp;item_key=".$item->key."'>Cite</a> </div>" . '$2', $item->bib, 1 );
				}

				// Highlight text
				if ( $zpr["highlight"] )
					$item->bib = str_ireplace( $zpr["highlight"], "<strong>".$zpr["highlight"]."</strong>", $item->bib );

				// Downloads, notes
				if ( $zpr["downloadable"]
						|| $zpr["shownotes"] )
				{
					// Check if item has children that could be downloads
					if ( $item->meta->numChildren > 0 )
					{
						// REVIEW: Why is this needed? Why is api_user_id empty sometimes?
						$tempUserId = '';
						if ( isset($api_user_id) )
							$tempUserId = $api_user_id;
						else 
							$tempUserId = $zpr["api_user_id"];
						
						// Get the user's account
						$zp_account = zp_get_account ($wpdb, $tempUserId);

						$zp_child_url = "https://api.zotero.org/".$zp_account[0]->account_type."/".$tempUserId."/items";
						$zp_child_url .= "/".$item->key."/children?";
						if (!is_null($zp_account[0]->public_key) && trim($zp_account[0]->public_key) != "")
							$zp_child_url .= "key=".$zp_account[0]->public_key."&";
						$zp_child_url .= "&format=json&include=data";

						// Get data
						$zp_import_child = new ZotpressRequest();
						$zp_child_request = $zp_import_child->get_request_contents( $zp_child_url, $zpr["update"] );
						$zp_children = json_decode( $zp_child_request["json"] );

						// If the item changes and no longer exists on
						// the Zotero side, it will return an error message
						if ( ! is_null($zp_children)
								&& $zp_children != "Item not found" )
						{
							$zp_download_meta = false;
							$zp_notes_meta = array();

							foreach ( $zp_children as $zp_child )
							{
								// Check for downloads
								if ( $zpr["downloadable"] )
								{
									// Check for downloadable file (attached)
									if ( isset($zp_child->data->linkMode)
	                                    && ( ( $zp_child->data->linkMode == "imported_file"
	                                			|| $zp_child->data->linkMode == "imported_url" )
	                            			&& preg_match('(pdf|doc|docx|ppt|pptx|latex|rtf|odt|odp)', $zp_child->data->filename) === 1 ) )
									{
             							$zp_download_meta = array (
   												"dlkey" => $zp_child->key,
   												"contentType" => $zp_child->data->contentType
   											);

							            // Display download link if file exists
							            if ( $zp_download_meta !== [] ) {
   											$item->bib = preg_replace('~(.*)' . preg_quote( '</div>', '~') . '(.*?)~', '$1' . " <a title='Download' class='zp-DownloadURL' href='".ZOTPRESS_PLUGIN_URL."lib/request/request.dl.php?api_user_id=".$tempUserId."&amp;dlkey=".$zp_download_meta["dlkey"]."&amp;content_type=".$zp_download_meta["contentType"]."'>Download</a></div>" . '$2', $item->bib, 1 );
										}
     								}

									// Check for link to downloadable file (third-party)
									else if ( isset($zp_child->data->linkMode)
										&& ( $zp_child->data->linkMode == "linked_url"
												&& preg_match('(pdf|doc|docx|ppt|pptx|latex|rtf|odt|odp)', $zp_child->data->url) === 1 ) )
									{
										$item->bib = preg_replace('~(.*)' . preg_quote( '</div>', '~') . '(.*?)~', '$1' . " <a title='Download' class='zp-DownloadURL' href='".$zp_child->data->url."'>Download</a></div>" . '$2', $item->bib, 1 );
							        }
								}

								// Check for notes
								if ( $zpr["shownotes"]
										&& ( isset($zp_child->data->itemType)
												&& $zp_child->data->itemType == "note" ) )
								{
									$zp_notes_meta[count($zp_notes_meta)] = $zp_child->data->note;
								}
							}

							// // Display download link if file exists
							// if ( $zp_download_meta )
							// 	$item->bib = preg_replace('~(.*)' . preg_quote( '</div>', '~') . '(.*?)~', '$1' . " <a title='Download' class='zp-DownloadURL' href='".ZOTPRESS_PLUGIN_URL."lib/request/request.dl.php?api_user_id=".$zpr["api_user_id"]."&amp;dlkey=".$zp_download_meta["dlkey"]."&amp;content_type=".$zp_download_meta["contentType"]."'>Download</a></div>" . '$2', $item->bib, 1 );

							// Display notes, if any
							if ( $zp_notes_meta !== [] )
							{
								$temp_notes = "<li id=\"zp-Note-".$item->key."\">\n";

								if ( count($zp_notes_meta) == 1 )
								{
									$temp_notes .= $zp_notes_meta[0]."\n";
								}
								else // multiple
								{
									$temp_notes .= "<ul class='zp-Citation-Item-Notes'>\n";

									foreach ($zp_notes_meta as $zp_note_meta)
										$temp_notes .= "<li class='zp-Citation-note'>" . $zp_note_meta . "\n</li>\n";

									$temp_notes .= "\n</ul><!-- .zp-Citation-Item-Notes -->\n\n";
								}

								// Add to item
								$item->notes = $temp_notes . "</li>\n";

								// Add note reference to citation
								$note_class = "zp-Notes-Reference"; if ( is_admin_bar_showing() ) $note_class .= " zp-Admin-Bar-Showing";
								$item->bib = preg_replace('~(.*)' . preg_quote('</div>', '~') . '(.*?)~', '$1' . " <sup class=\"".$note_class."\"><a href=\"#zp-Note-".$item->key."\">".$zp_notes_num."</a></sup> </div>" . '$2', $item->bib, 1);
								$zp_notes_num++;
							}
						} // Children exist; not "Item not found"
					} // Check if item has children
				} // $zpr["downloadable"]

				$zp_all_the_data[] = $item;

			} // foreach item

			// Show tags
			if ( $zpr["showtags"] )
			{
				// Decode JSON format
				$zp_tags = json_decode( $zp_request["tags"] );

				// Just add to the data; let the front-end handle the display
				// i.e., add to item.data.tags
				foreach ( $zp_all_the_data as $id => $data )
				{
					// Tags are connected to item key
					if ( property_exists( $zp_tags, $data->key ) )
						$zp_all_the_data[$id]->data->tags = $zp_tags->{$data->key};
				}
			}


			// Show images
			if ( $zpr["showimage"] )
			{
				// Get images for all item keys from zpdb, if they exist
				// $zp_images = $wpdb->get_results(
				// 	"
				// 	SELECT * FROM ".$wpdb->prefix."zotpress_zoteroItemImages
				// 	WHERE ".$wpdb->prefix."zotpress_zoteroItemImages.item_key IN ('".str_replace( " ", "', '", trim($zp_showimage_keys) )."')
				// 	"
				// );
				// $zp_temp_img_keys = str_replace( " ", "', '", trim($zp_showimage_keys) );
				$zp_temp_img_keys = explode(" ", trim($zp_showimage_keys) );
				$zp_temp_img_keys_count = count($zp_temp_img_keys);
				$zp_temp_img_ph = array_fill(0, $zp_temp_img_keys_count, "'%s'");
				$zp_temp_img_ph = implode(",", $zp_temp_img_ph);

				$zp_images = $wpdb->get_results(
					$wpdb->prepare(
						"
						SELECT * FROM ".$wpdb->prefix."zotpress_zoteroItemImages
						WHERE ".$wpdb->prefix."zotpress_zoteroItemImages.item_key IN (".$zp_temp_img_ph.")
						",
						$zp_temp_img_keys
					)
				);

				if ( count($zp_images) > 0 )
				{
					foreach ( $zp_images as $image )
					{
						$zp_thumbnail = wp_get_attachment_image_src($image->image);

						foreach ( $zp_all_the_data as $id => $data )
						{
							if ( $data->key == $image->item_key)
							{
								$zp_all_the_data[$id]->image = $zp_thumbnail;

								// URL Wrap for images
								if ( $zpr["urlwrap"] && $zpr["urlwrap"] == "image" && $zp_all_the_data[$id]->data->url != "" )
								{
									// Get rid of default URL listing
									// TODO: Does this account for all citation styles?
									$zp_all_the_data[$id]->bib = str_replace( htmlentities($zp_all_the_data[$id]->data->url), "", $zp_all_the_data[$id]->bib );
									$zp_all_the_data[$id]->bib = str_replace( " Retrieved from ", "", $zp_all_the_data[$id]->bib );
									$zp_all_the_data[$id]->bib = str_replace( " Available from: ", "", $zp_all_the_data[$id]->bib );
								}
							}
						}
					}
				} // If images found in zpdb

				// Check open lib next
				// REVIEW: Will break if Open Library is down!
				if ( $zpr["showimage"] === "openlib" )
				{
					$zp_showimage_keys = explode( ",", $zp_showimage_keys );

					foreach ( $zp_all_the_data as $id => $data )
					{
						if ( ! in_array( $data->key,  $zp_showimage_keys )
								&& ( isset($data->data->ISBN) && $data->data->ISBN != "" ) )
						{
							$openlib_url = "http://covers.openlibrary.org/b/isbn/".$data->data->ISBN."-M.jpg";

							// First, get the headers
							$openlib_headers = @get_headers( $openlib_url );

							// And make sure Open Library / the source is online
							if ( $openlib_headers[0] == "HTTP/1.1 302 Found" )
							{
								$zp_all_the_data[$id]->image = array( $openlib_url );

								// URL Wrap for images
								if ( $zpr["urlwrap"]
										&& $zpr["urlwrap"] == 'image'
										&& $zp_all_the_data[$id]->data->url != '' )
								{
									// Get rid of default URL listing
									// TODO: Does this account for all citation styles?
									$zp_all_the_data[$id]->bib = str_replace( htmlentities($zp_all_the_data[$id]->data->url), "", $zp_all_the_data[$id]->bib );
									$zp_all_the_data[$id]->bib = str_replace( " Retrieved from ", "", $zp_all_the_data[$id]->bib );
									$zp_all_the_data[$id]->bib = str_replace( " Available from: ", "", $zp_all_the_data[$id]->bib );
								}
							}
						}
					}
				}
			}

			// Re-sort with order of entry if bib and default sort
			if ( $zpr["item_type"] == "items"
					&& $zpr["sortby"] == "default"
				 	&& count($zpr["item_keys_order"]) > 0 )
			{
				$temp_arr = array();

				foreach ( $zpr["item_keys_order"] as $temp_key )
				{
					foreach ( $zp_all_the_data as $temp_data )
					{
						if ( $temp_data->key == $temp_key ) $temp_arr[] = $temp_data;
					}
				}

				$zp_all_the_data = $temp_arr;
			}


			// Remove extra meta data:
			foreach ( $zp_all_the_data as $id => $data )
			{
				unset($zp_all_the_data[$id]->meta->createdByUser);
			}

		} // if there's data (more than 0)
	}

	else // No results
	{
		// $zp_all_the_data = ""; // Necessary?
	}



	// +----------------------------+
	// | Finish and output the data |
	// +----------------------------+

	unset($zp_import_contents);
	unset($zp_import_url);
	unset($zp_xml);
	unset($api_user_id);
	unset($zp_account);

	$wpdb->flush();

	// Deal with cache scenarios:
	// 1. Used the cache
	// 2. Didn't check (why???)
	// TEST: Why are these checks needed? There's no "else" ... removing for now
	if ( ( $checkcache && $zp_usecache )
	 	|| ( ! $checkcache ) )
	{
		$zp_output = '';

		if ( $zp_usecache )
		{
			// Indicate new (just cached) or cached used:
			$zp_request_meta['used_cache'] = true;
		}

		if ( count($zp_all_the_data) > 0
		 		&& $zp_all_the_data != "" )
		{
			$zp_json_encoded = json_encode(
				array (
					"status" => "success",
					"updateneeded" => $zp_updateneeded,
					"instance" => $zpr["instance_id"],
					"meta" => $zp_request_meta,
					"data" => $zp_all_the_data
				)
			);

			if ( $is_ajax ) // JS:
			{
				echo $zp_json_encoded;

				exit(); // REVIEW: Causing to break if error
			}
			else // PHP:
			{
				// TEST: Is this right?
				$zp_output = "\t\t\t\t";

				if ( $zp_updateneeded )
					$zp_output .= '<span class="ZP_UPDATENEEDED" style="display: none;">true</span>';

				// $zp_output .= '<span class="ZP_USED_CACHE" style="display: none;">true</span>';
				$zp_output .= '<span class="ZP_JSON" style="display: none;">'.rawurlencode($zp_json_encoded).'</span>';
				$zp_output .= "\n\n";

				foreach ( $zp_all_the_data as $zp_citation )
				{
					// QUESTION: Why is it a string?
					// $zp_citation = json_decode($zp_citation);

					$zp_output .= "\t\t\t\t";
					$zp_output .= '<div id="zp-ID-'.$post->ID.'-'.$zp_citation->library->id.'-'.$zp_citation->key.'"';

					$tempItemDate = "0000-00-00";
					$tempItemYear = "0000";
					$tempItemType = $zp_citation->data->itemType;
					if ( property_exists($zp_citation->meta, "parsedDate")) {
						$tempItemDate = $zp_citation->meta->parsedDate;
						$tempItemYear = substr($zp_citation->meta->parsedDate, 0, 4);
					}

					$tempAuthor = "";
					if ( property_exists($zp_citation->meta, "creatorSummary"))
						$tempAuthor = str_replace(' ', '-', $zp_citation->meta->creatorSummary);

					$zp_output .= " data-zp-author-date='".$tempAuthor."-".$tempItemDate."'";
					$zp_output .= " data-zp-date-author='".$tempItemDate."-".$tempAuthor."'";
					$zp_output .= " data-zp-date='".$tempItemDate."'";
					$zp_output .= " data-zp-year='".$tempItemYear."'";
					$zp_output .= " data-zp-itemtype='".$tempItemType."'";
					$zp_output .= ' class="zp-Entry zpSearchResultsItem">';
					$zp_output .= "\n";
					$zp_output .= $zp_citation->bib;
					$zp_output .= "\n\t\t\t\t";
					$zp_output .= '</div><!-- .zp-Entry .zpSearchResultsItem -->';
				}
				return $zp_output;
			}
		}
		else // No data or error:
		{
			if ( ! isset($zpr["instance_id"]) )
				$zpr["instance_id"] = false;

			// Indicate problem or no cache:
			$zp_request_meta['used_cache'] = false;

			if ( $is_ajax )
				if ( $zp_error )
					$zp_output = json_encode(
						array (
							"status" => "error",
							"instance" => $zpr["instance_id"],
							"meta" => $zp_request_meta,
							"data" => $zp_error
						)
					);
				else // catchall, likely just no items for request
					$zp_output = json_encode(
						array (
							"status" => "empty",
							"instance" => $zpr["instance_id"],
							"meta" => $zp_request_meta,
							"data" => "0"
						)
					);

			echo $zp_output;

			if ( $is_ajax )
				exit(); // REVIEW: Causing to break if error
		}
	}
}
add_action( 'wp_ajax_zpRetrieveViaShortcode', 'Zotpress_shortcode_request' );
add_action( 'wp_ajax_nopriv_zpRetrieveViaShortcode', 'Zotpress_shortcode_request' );

?>
