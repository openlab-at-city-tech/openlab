jQuery(document).ready(function()
{
	///////////////////////////////
	//							 //
	//   ZOTPRESS BIBLIOGRAPHY   //
	//							 //
	///////////////////////////////

	// TO REVIEW AFTER CUSTOMIZING FOR ASPIRE LAB:
	// - Use of the JSON cache

	if ( jQuery(".zp-Zotpress-Bib").length > 0 )
	{
		var zp_collections = {};
		var zp_totalItems = 0;

		// Capture the metadata for each bibliography:
		jQuery(".zp-Zotpress-Bib").each( function( index, instance )
		{
			var $instance = jQuery(instance);
      		var zp_params = {};

			zp_params.zpItemkey = false; if ( jQuery(".ZP_ITEM_KEY", $instance).text().trim().length > 0 ) zp_params.zpItemkey = jQuery(".ZP_ITEM_KEY", $instance).text();
			zp_params.zpItemType = false; if ( jQuery(".ZP_ITEMTYPE", $instance).text().trim().length > 0 ) zp_params.zpItemType = jQuery(".ZP_ITEMTYPE", $instance).text();
			zp_params.zpCollectionId = false; if ( jQuery(".ZP_COLLECTION_ID", $instance).text().trim().length > 0 ) zp_params.zpCollectionId = jQuery(".ZP_COLLECTION_ID", $instance).text();
			zp_params.zpTagId = false; if ( jQuery(".ZP_TAG_ID", $instance).text().trim().length > 0 ) zp_params.zpTagId = jQuery(".ZP_TAG_ID", $instance).text();
			zp_params.zpAuthor = false; if ( jQuery(".ZP_AUTHOR", $instance).text().trim().length > 0 ) zp_params.zpAuthor = jQuery(".ZP_AUTHOR", $instance).text();
			zp_params.zpYear = false; if ( jQuery(".ZP_YEAR", $instance).text().trim().length > 0 ) zp_params.zpYear = jQuery(".ZP_YEAR", $instance).text();
			zp_params.zpStyle = false; if ( jQuery(".ZP_STYLE", $instance).text().trim().length > 0 ) zp_params.zpStyle = jQuery(".ZP_STYLE", $instance).text();
			zp_params.zpLimit = false; if ( jQuery(".ZP_LIMIT", $instance).text().trim().length > 0 ) zp_params.zpLimit = jQuery(".ZP_LIMIT", $instance).text();
			zp_params.zpTitle = false; if ( jQuery(".ZP_TITLE", $instance).text().trim().length > 0 ) zp_params.zpTitle = jQuery(".ZP_TITLE", $instance).text();

			zp_params.zpShowImages = false; if ( jQuery(".ZP_SHOWIMAGE", $instance).text().trim().length > 0 ) zp_params.zpShowImages = jQuery(".ZP_SHOWIMAGE", $instance).text().trim();
			zp_params.zpShowTags = false; if ( jQuery(".ZP_SHOWTAGS", $instance).text().trim().length > 0 ) zp_params.zpShowTags = true;
			zp_params.zpDownloadable = false; if ( jQuery(".ZP_DOWNLOADABLE", $instance).text().trim().length > 0 ) zp_params.zpDownloadable = true;
			zp_params.zpInclusive = false; if ( jQuery(".ZP_INCLUSIVE", $instance).text().trim().length > 0 ) zp_params.zpInclusive = true;
			zp_params.zpShowNotes = false; if ( jQuery(".ZP_NOTES", $instance).text().trim().length > 0 ) zp_params.zpShowNotes = true;
			zp_params.zpShowAbstracts = false; if ( jQuery(".ZP_ABSTRACT", $instance).text().trim().length > 0 ) zp_params.zpShowAbstracts = true;
			zp_params.zpCiteable = false; if ( jQuery(".ZP_CITEABLE", $instance).text().trim().length > 0 ) zp_params.zpCiteable = true;
			zp_params.zpTarget = false; if ( jQuery(".ZP_TARGET", $instance).text().trim().length > 0 ) zp_params.zpTarget = true;
			zp_params.zpURLWrap = false; if ( jQuery(".ZP_URLWRAP", $instance).text().trim().length > 0 ) zp_params.zpURLWrap = jQuery(".ZP_URLWRAP", $instance).text();
			zp_params.zpHighlight = false; if ( jQuery(".ZP_HIGHLIGHT", $instance).text().trim().length > 0 ) zp_params.zpHighlight = jQuery(".ZP_HIGHLIGHT", $instance).text();
			
			// zp_params.zpUsedCache = false; if ( jQuery(".ZP_USED_CACHE", $instance).text().trim().length > 0 && jQuery(".ZP_USED_CACHE", $instance).text() == "true" ) zp_params.zpUsedCache = true;
			zp_params.zpUpdateNeeded = false; if ( jQuery(".ZP_UPDATENEEDED", $instance).text().trim().length > 0 && jQuery(".ZP_UPDATENEEDED", $instance).text() == "true" ) zp_params.zpUpdateNeeded = true;
			zp_params.zpJSON = false; if ( jQuery(".ZP_JSON", $instance).text().trim().length > 0 ) zp_params.zpJSON = jQuery(".ZP_JSON", $instance).text().trim();

			zp_params.zpForceNumsCount = 1;
			zp_params.zpBibIndex = index;

			// TEST:
			// SUPER IMPORTANT LOGIC
			$fromScratch = true;

			if ( ! jQuery(".ZP_JSON", $instance).text().trim().length > 0 )
			{
				// zp_get_items ( 0, 0, $instance, zp_params, false );
				$fromScratch = false;
			}


			// else // Use cache
			// {
				// First, get encoded/serialized JSON data from PHP:
				var zp_items = JSON.parse(decodeURIComponent(zp_params.zpJSON));
				
				// Then (re)format:
				zp_bib_reformat( $instance, zp_items, zp_params );
				console.log('---');

				// Second, check for updates and update, if needed:
				// TEST: Big changes

				// Deal with multiples:
				// Order of priority: collections, tags, authors, years
				// Filters (dealt with on shortcode.request.php): tags?, authors, years
				if ( zp_params.zpCollectionId
						&& zp_params.zpCollectionId.indexOf(',') != -1 )
				{
					zp_collections[index] = zp_params.zpCollectionId.split(',');

					// Set the initial collection:
					// var currentCollection = zp_collections[0];
					zp_params.zpCollectionId = zp_collections[index][0];

					// params: request_start, request_last, $instance, params, update
					// TEST: changed update = true
					zp_get_items ( 0, 0, $instance, zp_params, $fromScratch );
				}
				else
				{
					// Handle inclusive tags (treat exclusive normally):
					if ( zp_params.zpTagId 
							&& zp_params.zpInclusive == true 
							&& zp_params.zpTagId.indexOf(",") != -1 )
					{
						var tempTags = zp_params.zpTagId.split(",");

						jQuery.each( tempTags, function (i, tag)
						{
							zp_params.zpTagId = tag;

							// params: request_start, request_last, $instance, params, update
							// TEST: changed update = true
							zp_get_items ( 0, 0, $instance, zp_params, $fromScratch );
						});
					}
					else
					{
						// Handle authors:
						if ( zp_params.zpAuthor 
								&& zp_params.zpAuthor.indexOf(",") != -1 )
						{
							var tempAuthors = zp_params.zpAuthor.split(",");

							// Handle inclusive authors: 
							if ( zp_params.zpInclusive == true )
							{
								jQuery.each( tempAuthors, function (i, author)
								{
									zp_params.zpAuthor = author;

									// params: request_start, request_last, $instance, params, update
									// TEST: changed update = true
									zp_get_items ( 0, 0, $instance, zp_params, $fromScratch );
								});
							}
							else // exclusive
							{
								// params: request_start, request_last, $instance, params, update
								// TEST: changed update = true
								zp_get_items ( 0, 0, $instance, zp_params, $fromScratch );
							}
						}
						else
						{
							// Handle years:
							if ( zp_params.zpYear 
									&& zp_params.zpYear.indexOf(",") != -1 )
							{
								var tempYears = zp_params.zpYear.split(",");

								jQuery.each( tempYears, function (i, year)
								{
									zp_params.zpYear = year;

									// params: request_start, request_last, $instance, params, update
									// TEST: changed update = true
									zp_get_items ( 0, 0, $instance, zp_params, $fromScratch );
								});
							}
							else // NORMAL, no multiples, no nothin'
							{
								// params: request_start, request_last, $instance, params, update
								// TEST: changed update = true
								zp_get_items ( 0, 0, $instance, zp_params, $fromScratch );
							}
						}
					}
				}
			// }
			// zp_params = JSON.stringify(zp_params);
		});
	} // Zotpress Bibliography



	// Get list items:
	function zp_get_items ( request_start, request_last, $instance, params, update )
	{
		console.log('zp: calling zp_get_items with update check?', update);
		console.log('zp: is an update needed?', params.zpUpdateNeeded);
		
		if ( typeof(request_start) === "undefined" || request_start == "false" || request_start == "" )
			request_start = 0;

		if ( typeof(request_last) === "undefined" || request_last == "false" || request_last == "" )
			request_last = 0;

		jQuery.ajax(
		{
			url: zpShortcodeAJAX.ajaxurl,
			ifModified: true,
			data: {
				'action': 'zpRetrieveViaShortcode',
				'instance_id': $instance.attr("id"),
				'api_user_id': jQuery(".ZP_API_USER_ID", $instance).text(),
				'item_type': jQuery(".ZP_ITEM_TYPE", $instance).text(),

				'item_key': params.zpItemkey,
				'itemtype': params.zpItemType,
				'collection_id': params.zpCollectionId,
				'tag_id': params.zpTagId,

				// 'author': encodeURI(params.zpAuthor).replace("'","%27"),
				// 'author': params.zpAuthor.toString().replace("\\","%5C"),
				'author': params.zpAuthor,
				// 'author': params.zpAuthor.toString().replace("'","&#39;"),
				'year': params.zpYear,
				'style': params.zpStyle,
				'limit': params.zpLimit,
				'title': params.zpTitle,

				'showimage': params.zpShowImages,
				'showtags': params.zpShowTags,
				'downloadable': params.zpDownloadable,
				'inclusive': params.zpInclusive,
				'shownotes': params.zpShowNotes,
				'showabstracts': params.zpShowAbstracts,
				'citeable': params.zpCiteable,

				'target': params.zpTarget,
				'urlwrap': params.zpURLWrap,
				'highlight': params.zpHighlight,

				'sortby': jQuery(".ZP_SORTBY", $instance).text(),
				'order': jQuery(".ZP_ORDER", $instance).text(),

				'update': update,
				'request_update': params.zpUpdateNeeded,
				'request_start': request_start,
				'request_last': request_last,
				'zpShortcode_nonce': zpShortcodeAJAX.zpShortcode_nonce
			},
			xhrFields: {
				withCredentials: true
			},
			success: function(data)
			{
				var zp_items = jQuery.parseJSON( data );
				
				zp_totalItems += zp_items.data.length;
				if ( update ) console.log("zp: running update for items:",zp_totalItems,"->",zp_items.data.length);
				else console.log("zp: adding items:",zp_totalItems,"->",zp_items.data.length);

				// Account for Zotero errors
				// QUESTION: Did something change? Now have to ref [0]
				// 7.3.10: CHECK: Added 'error' status check
				if ( zp_items.status == 'error'
						|| zp_items.status == 'empty'
			 			|| zp_items.data == 'Not found' )
				{
					var zp_msg = zpShortcodeAJAX.txt_zperror + " ";

					if ( zp_items.data == 0 )
					{
						zp_items.data = zpShortcodeAJAX.txt_noitemsfound;
						zp_msg = zp_items.data;
					}
					else {
						zp_msg += zp_items.data;
					}

					console.log( "zp: Zotpress message: " + zp_msg );

					// Hide errors if something shown
					var hideErrMsg = '';
					if ( jQuery( "#"+zp_items.instance+" .zp-List .zp-Entry" ).length > 0 )
						hideErrMsg = ' class="hide"';

					// Remove the loader, display the message
					jQuery( "#"+zp_items.instance+" .zp-List" )
						.removeClass( 'loading' )
						.append( '<p'+hideErrMsg+'>'+zp_msg+'</p>' );
				}

				// Success! Process as items
				else
				{
					// First, remove any PHP SEO items that exist
					jQuery("#"+zp_items.instance+" .zp-SEO-Content").remove();

					// // QUESTION: Then prepare the data as a JSON?
					// zp_items.data = jQuery.parseJSON(zp_items.data);

					// First, display the items from this request, if any:
					if ( typeof zp_items != 'undefined'
							&& zp_items != null
							&& zp_items != 0
							&& zp_items.data.length > 0 )
					{
						var tempItems = "";
						if ( params.zpShowNotes == true )
							var tempNotes = "";


						// Indicate whether cache has been used:
						if ( update === false )
						{
							jQuery("#"+zp_items.instance+" .zp-List").addClass("used_cache");
						}
						else if ( update === true )
						{
							// Remove existing notes temporarily:
							if ( ! jQuery("#"+zp_items.instance+" .zp-List").hasClass("updating")
									&& jQuery("#"+zp_items.instance+" .zp-Citation-Notes").length > 0 )
								jQuery("#"+zp_items.instance+" .zp-Citation-Notes").remove();

							if ( ! jQuery("#"+zp_items.instance+" .zp-List").hasClass("updating") )
								jQuery("#"+zp_items.instance+" .zp-List").addClass("updating");

							params.zpForceNumsCount = 1;
						}


						// CHANGED (7.3): Make sure that there are items ...
						if ( zp_items.data != 0 )
						{
							jQuery.each( zp_items.data, function( index, item )
							{
								var tempItem = "";

								// Determine item reference
								var $item_ref = jQuery("#"+zp_items.instance+" .zp-List #zp-ID-"+jQuery(".ZP_POSTID", $instance).text()+"-"+jQuery(".ZP_API_USER_ID", $instance).text()+"-"+item.key);

								// Replace or skip duplicates
								if ( $item_ref.length > 0
										&& update === false
										&& ! jQuery("#"+zp_items.instance+" .zp-List").hasClass("used_cache") )
									return false;

								// Item Type
								var tempItemType = "none";
								if ( item.data.hasOwnProperty('itemType') )
									tempItemType = item.data.itemType;

								// Year and Date
								var tempItemYear = "0000"; // yyyy
								var tempItemDate = "0000"; // yyyy-mm-dd
								if ( item.meta.hasOwnProperty('parsedDate') )
								{
									tempItemYear = item.meta.parsedDate.substring(0, 4);
									tempItemDate = item.meta.parsedDate;
								}

								// Author
								var tempAuthor = item.data.title;
								if ( item.meta.hasOwnProperty('creatorSummary') )
									tempAuthor = item.meta.creatorSummary.replace( / /g, "-" );

								tempItem += "<div id='zp-ID-"+jQuery(".ZP_POSTID", $instance).text()+"-"+jQuery(".ZP_API_USER_ID", $instance).text()+"-"+item.key+"'";
								tempItem += " data-zp-author-date='"+tempAuthor+"-"+tempItemDate+"'";
								tempItem += " data-zp-date-author='"+tempItemDate+"-"+tempAuthor+"'";
								tempItem += " data-zp-date='"+tempItemDate+"'";
								tempItem += " data-zp-year='"+tempItemYear+"'";
								tempItem += " data-zp-itemtype='"+tempItemType+"'";
								tempItem += " class='zp-Entry zpSearchResultsItem";

								// Add update class to item
								if ( update === true )
									tempItem += " zp_updated";

								// Image
								if ( jQuery("#"+zp_items.instance+" .ZP_SHOWIMAGE").text().trim().length > 0 && item.hasOwnProperty('image') )
								{
									tempItem += " zp-HasImage'>\n";
									tempItem += "<div id='zp-Citation-"+item.key+"' class='zp-Entry-Image hasImage' rel='"+item.key+"'>\n";

									// URL wrap image if applicable
									if ( params.zpURLWrap == "image" && item.data.url != "" )
									{
										tempItem += "<a href='"+item.data.url+"'";
										if ( params.zpTarget ) tempItem += " target='_blank'";
										tempItem += ">";
									}
									tempItem += "<img class='thumb' src='"+item.image[0]+"' alt='image' />\n";
									if ( params.zpURLWrap == "image" && item.data.url != "" ) tempItem += "</a>";
									tempItem += "</div>\n";
								}
								else
								{
									tempItem += "'>\n";
								}

								// Force numbers
								if ( jQuery("#"+zp_items.instance+" .ZP_FORCENUM").text().length > 0
										&& jQuery("#"+zp_items.instance+" .ZP_FORCENUM").text() == '1' )
								{
									if ( ! /csl-left-margin/i.test(item.bib) ) // if existing style numbering not found
									{
										item.bib = item.bib.replace( '<div class="csl-entry">', '<div class="csl-entry">'+params.zpForceNumsCount+". " );
										params.zpForceNumsCount++;
									}
								}

								tempItem += item.bib;

								// Add abstracts, if any
								if ( params.zpShowAbstracts == true &&
										( item.data.hasOwnProperty('abstractNote') && item.data.abstractNote.length > 0 ) )
									tempItem +="<p class='zp-Abstract'><span class='zp-Abstract-Title'>Abstract:</span> " +item.data.abstractNote+ "</p>\n";

								// Add tags, if any
								if ( params.zpShowTags == true &&
										( item.data.hasOwnProperty('tags') && item.data.tags.length > 0 ) )
								{
									tempItem += "<p class='zp-Zotpress-ShowTags'><span class='title'>Tags:</span> ";

									jQuery.each(item.data.tags, function ( tindex, tag )
									{
										tempItem += "<span class='tag'>" + tag.tag + "</span>";
										if ( tindex != (item.data.tags.length-1) ) tempItem += "<span class='separator'>,</span> ";
									});
									tempItem += "</p>\n";
								}

								tempItem += "</div>\n";

								// Add notes, if any
								if ( params.zpShowNotes == true && item.hasOwnProperty('notes') )
									tempNotes += item.notes;


								// Add this item to the list
								// Replace or skip duplicates
								if ( $item_ref.length > 0
										&& update === true )
									$item_ref.replaceWith( jQuery( tempItem ) );
								else
									tempItems += tempItem;

							}); // each item
						} // check that there are items


						// Append cached/initial items to list:
						// if ( update === false )
							jQuery("#"+zp_items.instance+" .zp-List").append( tempItems );

						// Append notes to container, if needed:
						if ( params.zpShowNotes == true
								&& tempNotes.length > 0 )
						{
							tempNotes = "<div class='zp-Citation-Notes'>\n<h4>Notes</h4>\n<ol>\n" + tempNotes;
							tempNotes = tempNotes + "</ol>\n</div><!-- .zp-Citation-Notes -->\n\n";

							jQuery("#"+zp_items.instance).append( tempNotes );
						}


						// Fix incorrect numbering in existing numbered style
						// Ignore: american-antiquity
						if ( jQuery("#"+zp_items.instance+" .zp-List .csl-left-margin").length > 0
								&& ( jQuery(".ZP_STYLE", $instance).text().trim().length > 0
							 			&& jQuery(".ZP_STYLE", $instance).text().trim() != 'american-antiquity' ) )
						{
							params.zpForceNumsCount = 1;

							jQuery("#"+zp_items.instance+" .zp-List .csl-left-margin").each( function( index, item )
							{
								var item_content = jQuery(item).text();
								item_content = item_content.replace( item_content.match(/\d+/)[0], params.zpForceNumsCount );
								jQuery(item).text( item_content );

								params.zpForceNumsCount++;
							});
						}

						// Re-sort, if needed
						zp_bib_reformat( $instance, zp_items, params );

						// Then, continue with other requests, if they exist:
						if ( zp_items.meta.request_next != false
								&& zp_items.meta.request_next != "false" )
						{
							// params: request_start, request_last, $instance, params, update
							// TEST: kept update as var
							zp_get_items ( zp_items.meta.request_next, zp_items.meta.request_last, $instance, params, update );
						}
						else // Otherwise, finish up the initial request(s):
						{
							// Re-sort, if needed
							// zp_bib_reformat( $instance, zp_items, params );

							// Remove loading
							jQuery("#"+zp_items.instance+" .zp-List").removeClass("loading");

							// Check for updates, if needed:
							if ( zp_items.updateneeded )
							{
								console.log("zp: update needed");

								params.zpUpdateNeeded = true;
								zp_get_items ( 0, 0, $instance, params, true );
							}

							else // Or totally finish up and re-sort if needed:
							{
								// If multiple collections, then go to next
								if ( zp_collections[params.zpBibIndex]
										&& zp_collections[params.zpBibIndex].length > 0
										&& zp_collections[params.zpBibIndex][zp_collections[params.zpBibIndex].length-1] != params.zpCollectionId )
								{
									// Set the next collection
									params.zpCollectionId = zp_collections[params.zpBibIndex][ zp_collections[params.zpBibIndex].indexOf(params.zpCollectionId)+1 ];

									// Be sure to turn of update flag for this collection
									jQuery("#"+zp_items.instance+" .zp-List").removeClass("updating");

									// Start the item requests
									// params: request_start, request_last, $instance, params, update
									// TEST: changed update to var from false
									zp_get_items ( 0, 0, $instance, params, update );
								}
								else
								{
									// TEST: We just reformatted, so let's not do it again
									console.log("zp: done");
									console.log("---");

									// 7.3.14: Add class to indicate done updating
									jQuery("#"+zp_items.instance+" .zp-List").addClass("done-updating");

									// zp_bib_reformat( $instance, zp_items, params );
								}
							}
						}
					}

					// Message that there's no items
					else
					{
						console.log("zp: done");

						if ( update === true )
						{
							jQuery("#"+$instance.attr("id")+" .zp-List").removeClass("loading");

	                        if ( jQuery("#"+$instance.attr("id")+" .zp-List .zp-Entry").length == 0 )
								jQuery("#"+$instance.attr("id")+" .zp-List").append("<p>There are no citations to display.</p>\n");
						}
					}
				}
			},
			error: function(errorThrown)
			{
                console.log("zp: Zotpress via WP AJAX Error: ", errorThrown);
			}
		});

	} // function zp_get_items


	function zp_bib_reformat( $instance, zp_items, zp_params )
	{
		console.log('zp: reformatting instance', zp_items.instance);

		var sortby = jQuery(".ZP_SORTBY", $instance).text();
		var orderby = jQuery(".ZP_ORDER", $instance).text();

		var sortOrder = "data-zp-author-date";
		if ( sortby == "date")
			sortOrder = "data-zp-date-author";

		// First, sort items the typical way
		typicalSort( zp_items, sortby, sortOrder, orderby );

		// Deal with the Title situation
		if ( zp_params.zpTitle !== false )
		{
			// First, make an object of title sort types;
			// each will contain an arr of entry IDs in this format:
			// { titleSortType: [ entryID ] }
			// e.g., { '2017': [ 'zp-ID--237927-PZ2HQ9V7', 'zp-ID--237927-HSHEURFV' ] }
			// e.g., { 'book': [ 'zp-ID--237927-PZ2HQ9V7', 'zp-ID--237927-HSHEURFV' ] }
			var titleSortedEntries = {};

			// Then, get the Title sort type per item
			jQuery("#"+zp_items.instance+" .zp-List div.zp-Entry").each(
				function(i, entry)
				{
					// Then the title sort types, e.g., particular years
					var titleSortType = jQuery(entry).data( 'zp-'+zp_params.zpTitle );

					// Add title sort type to arr, if not there
					if ( titleSortedEntries.hasOwnProperty(titleSortType) === false )
						titleSortedEntries[titleSortType] = [];

					// Add entry ID and data sort to the right title sort type
					titleSortedEntries[titleSortType].push(
						// { [jQuery(entry).attr('id')] : titleSortType }
						jQuery(entry).attr('id')
					);
				}
			);

			// Then, sort the title sort, depending on its type
			var titleSortedEntriesOrder = Object.getOwnPropertyNames(titleSortedEntries);

			if ( zp_params.zpTitle == "itemtype" )
			{
				// First, make an array with the desired order of known types
				// NOTE: Based on https://www.zotero.org/support/kb/item_types_and_fields
				var orderedTypes = [
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
				];

				// Then, remove any that aren't in the existing array
				orderedTypes.slice(0).forEach( function(orderedType, i)
				{
					if ( titleSortedEntriesOrder.indexOf(orderedType) == -1 )
						orderedTypes.splice( orderedTypes.indexOf(orderedType), 1 );
				});

				titleSortedEntriesOrder = orderedTypes;
			}
			else // Assume year
			{
				if ( orderby == "asc" )
					titleSortedEntriesOrder.sort( function(a, b) { return a-b; } );
				else
					titleSortedEntriesOrder.sort( function(a, b) { return b-a; } );
			}

			// Next, restructure bib based on this order
			titleSortedEntriesOrder.forEach( function(orderedType, i)
			{
				// First, make a nice name for the type
				var tempTitle = orderedType;

				// Deal with `itemtype` formatting
				if ( zp_params.zpTitle == "itemtype" )
				{
					if ( tempTitle.match(/[A-Z]/) !== null )
					{
						var tempPos = tempTitle.match(/[A-Z]/).index;
						tempTitle = tempTitle.substr(0, tempPos)+' '+tempTitle.substr(tempPos);
					}
					tempTitle = tempTitle.charAt(0).toUpperCase() + tempTitle.slice(1);
				}

				// Deal with no date, if applicable
				if ( tempTitle == "0000" )
					tempTitle = "No date";

				// Then, add the header, if it doesn't exist
				// REVIEW: This only considers date/year, after initial sort,
				// and if header not already there
				if ( zp_params.zpTitle == "year" )
				{
					// Just add the header, initially or if it doesn't exist
					if ( jQuery("#"+$instance.attr("id")+" .zp-List h3[rel='"+orderedType+"']").length == 0 )
						jQuery("#"+$instance.attr("id")+" .zp-List").append( "<h3 rel='"+orderedType+"'>"+tempTitle+"</h3>\n" );
				}
				// else // Just add the header, initially
				else if ( jQuery("#"+$instance.attr("id")+" .zp-List h3[rel='"+orderedType+"']").length == 0 )
				{
					jQuery("#"+$instance.attr("id")+" .zp-List").append( "<h3 rel='"+orderedType+"'>"+tempTitle+"</h3>\n" );
				}

				// Then, reverse order of entries, in some cases:
				// REVIEW: Only if TITLE=year and orderby=ASC
				if ( zp_params.zpTitle == "year"
		 				&& orderby == "asc" )
					titleSortedEntries[orderedType].reverse();
				// REVIEW: Only TITLE=itemtype and SORTBY=date
				else if ( zp_params.zpTitle == "itemtype"
						&& sortby == "date" )
					titleSortedEntries[orderedType].reverse();

				// Then, move the items under the right heading
				titleSortedEntries[orderedType].forEach( function(entryID, i)
				{
					jQuery("#"+$instance.attr("id")+" #"+entryID).insertAfter(
						jQuery("#"+$instance.attr("id")+" .zp-List h3[rel='"+orderedType+"']")
					);
				});
			}); // each orderedType
		} // Titles situation

	} // function zp_bib_reformat()


	// Used by zp_bib_reformat()
	function typicalSort( zp_items, sortby, sortOrder, orderby )
	{
		console.log('zp: running typical sort for instance ', zp_items.instance);

		// Re-sort if not numbered and sorting by author or date
		if ( ['author', 'date'].indexOf(sortby) !== -1
				&& jQuery('#'+zp_items.instance+' .zp-List .csl-left-margin').length == 0 )
		{
			jQuery('#'+zp_items.instance+' .zp-List div.zp-Entry').sort(
				function(a,b)
				{
					var an = a.getAttribute(sortOrder).toLowerCase(),
						bn = b.getAttribute(sortOrder).toLowerCase();

					if (an > bn)
						if ( orderby == 'asc' )
							return 1;
						else
							return -1;
					else if (an < bn)
						if ( orderby == 'asc' )
							return -1;
						else
							return 1;
					else
						return 0;

				}).detach().appendTo( '#'+zp_items.instance+' .zp-List' );
		}
	}


});
