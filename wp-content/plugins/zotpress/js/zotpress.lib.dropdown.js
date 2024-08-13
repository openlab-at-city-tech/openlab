jQuery(document).ready(function()
{
	///////////////////////////////////
	//                               //
	//   ZOTPRESS LIBRARY DROPDOWN   //
	//                               //
	///////////////////////////////////

	// TODO: Like the searchbar, doesn't update display right away after updating
	// TODO: notes, abstract

	if ( jQuery(".zp-Browse").length > 0 )
	{
		// Go through each instance
		for (let l = 0; l < window.zpBrowseList.length; ++l)
		{
			var zpThisLib = jQuery("#"+window.zpBrowseList[l].id); // zp-Browse

			var zpThisLibProps = {
				'zpCollectionId' : false,
				'zpTagId' : false,
				'zpShowTags' : false,
				'zpShowImages' : false,
				'zpIsAdmin' : false,
				'zpTarget' : false,
				'zpTopLevel' : false,
				'zpURLWrap' : false,
				'zpItemsFlag' : true
			};

			if ( jQuery(".ZP_COLLECTION_ID", zpThisLib).length > 0 ) zpThisLibProps.zpCollectionId = jQuery(".ZP_COLLECTION_ID", zpThisLib).text();
			if ( jQuery(".ZP_TAG_ID", zpThisLib).length > 0 ) zpThisLibProps.zpTagId = jQuery(".ZP_TAG_ID", zpThisLib).text();
			if ( jQuery(".ZP_SHOWTAGS").length > 0 && parseInt( jQuery(".ZP_SHOWTAGS").text() ) == "1" ) zpThisLibProps.zpShowTags = true;
			if ( jQuery(".ZP_SHOWIMAGE", zpThisLib).length > 0 &&  ( jQuery(".ZP_SHOWIMAGE", zpThisLib).text() == "yes" || jQuery(".ZP_SHOWIMAGE", zpThisLib).text() == "true" ||  jQuery(".ZP_SHOWIMAGE", zpThisLib).text() == "1" ) ) zpThisLibProps.zpShowImages = true;
			if ( jQuery(".ZP_ISADMIN", zpThisLib).length > 0 ) zpThisLibProps.zpIsAdmin = true;
			if ( jQuery(".ZP_TARGET", zpThisLib).length > 0 && jQuery(".ZP_TARGET").text().length > 0 ) zpThisLibProps.zpTarget = true;
			if ( jQuery(".ZP_TOPLEVEL", zpThisLib).length > 0 )
			{
				zpThisLibProps.zpTopLevel = jQuery(".ZP_TOPLEVEL", zpThisLib).text();

				if ( zpThisLibProps.zpCollectionId === false
				 			&& zpThisLibProps.zpTagId === false )
					zpThisLibProps.zpCollectionId = zpThisLibProps.zpTopLevel;
			}
			if ( jQuery(".ZP_URLWRAP", zpThisLib).length > 0 ) zpThisLibProps.zpURLWrap = jQuery(".ZP_URLWRAP", zpThisLib).text();
			var zpUpdateNeeded = false; if ( jQuery(".ZP_UPDATENEEDED", zpThisLib).text().trim().length > 0 && jQuery(".ZP_UPDATENEEDED", zpThisLib).text() == "true" ) zpUpdateNeeded = true;

			// Handle no browse bar
			var browsebar = true; if ( jQuery(".ZP_BROWSEBAR", zpThisLib).text() == "" ) browsebar = false;

			if ( browsebar )
			{
				zplib_get_collections ( l, zpThisLibProps, zpThisLib, 0, 0, false );
				zplib_get_tags ( l, zpThisLibProps, zpThisLib, 0, 0, false );
			}

			// Otherwise, get items
			zplib_get_items ( l, zpThisLibProps, zpThisLib, 0, 0, false );

			console.log('---');
		}
	} // Zotpress DropDown Library


	// Corrects numeric citations
	function zp_relabel_numbers(zpThisLib)
	{
		if ( jQuery("div.zp-List .csl-left-margin", zpThisLib).length != 0
		    && /\d/.test( jQuery("div.zp-List .csl-left-margin", zpThisLib).text() ) )
		{
		  var count = 1;

		  jQuery("div.zp-List .csl-left-margin", zpThisLib).each(function()
          {
            jQuery(this).text( jQuery(this).text().replace(/(\d+)/, count) );
            count++;
          });
		}
	}


  	// Get list of collections
	function zplib_get_collections ( l, zpThisLibProps, zpThisLib, request_start, request_last, update )
	{
		// Set parameter defaults
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
				'api_user_id': jQuery(".ZP_API_USER_ID", zpThisLib).text(),
				'item_type': 'collections',
				'collection_id': zpThisLibProps.zpCollectionId,
				'request_start': request_start,
				'request_last': request_last,
				'sortby': 'title',
				'get_top': true,
				'update': update,
				'zpShortcode_nonce': zpShortcodeAJAX.zpShortcode_nonce
			},
			xhrFields: {
				withCredentials: true
			},
			success: function(data)
			{
				var zp_collections = jQuery.parseJSON( data );
				var zp_collection_options = "";

				// Remove cached bib before adding updates
				// REVIEW: Is adding used_cache necessary?
				if ( update === false ) jQuery("select.zp-Browse-Collections-Select", zpThisLib).addClass("used_cache");
				if ( update === true && ! jQuery("select.zp-Browse-Collections-Select", zpThisLib).hasClass("updating") )
				{
					jQuery("select.zp-Browse-Collections-Select", zpThisLib).find('*').not('.blank').remove();
					jQuery("select.zp-Browse-Collections-Select", zpThisLib).addClass("updating");

					if ( zpThisLibProps.zpTagId )
						jQuery("select.zp-Browse-Collections-Select", zpThisLib)
							.append( "<option value='blank'>--"+zpShortcodeAJAX.txt_nocollsel+"--</option>" );
					// if ( ! zpTagId && ! zpCollectionId ) jQuery("select.zp-Browse-Collections-Select", zpThisLib).append( "<option value='toplevel'>"+zpShortcodeAJAX.txt_toplevel+"</option>" );
				}

				// Add Top Level Collection option to the select
				if ( zpThisLibProps.zpCollectionId
						&& jQuery(".zp-Browse-Collections-Select option.blank", zpThisLib).length == 0 )
						// && jQuery(".zp-Browse-Collections-Select option.toplevel", zpThisLib).length == 0 )
					if ( jQuery(".ZP_COLLECTION_NAME", zpThisLib).length > 0 )
						jQuery("select.zp-Browse-Collections-Select", zpThisLib)
							.append( "<option value='blank' class='blank'>"+jQuery(".ZP_COLLECTION_NAME", zpThisLib).text()+"</option>\n" );
					// else
					// 	jQuery("select.zp-Browse-Collections-Select", zpThisLib)
					// 		.append( "<option value='blank' class='blank'>Default Collection</option>\n" );

				if ( zp_collections != "0"
						&& zp_collections.data.length > 0
						&& zp_collections.data != "0" )
				{
					jQuery.each(zp_collections.data, function( index, collection )
					{
						var temp = "<option value='"+collection.key+"'";
						if ( zpThisLibProps.zpCollectionId == collection.key ) temp += " selected='selected'";
						temp += ">";
						if ( zpThisLibProps.zpCollectionId ) temp += "- "; // For subcollection dropdown indent
						temp += collection.data.name+" (";
						if ( collection.meta.numCollections > 0 ) temp += collection.meta.numCollections+" "+zpShortcodeAJAX.txt_subcoll+", ";
						temp += collection.meta.numItems+" "+zpShortcodeAJAX.txt_items+")</option>\n";

						zp_collection_options += temp;
					});

					jQuery("select.zp-Browse-Collections-Select", zpThisLib).append( zp_collection_options );

					// Then, continue with other requests, if they exist
					if ( zp_collections.meta.request_next != false
							&& zp_collections.meta.request_next != "false" )
						zplib_get_collections ( l, zpThisLibProps, zpThisLib, zp_collections.meta.request_next, zp_collections.meta.request_last, update );
					else
						if ( ! jQuery("select.zp-Browse-Collections-Select", zpThisLib).hasClass("updating") )
							zplib_get_collections ( l, zpThisLibProps, zpThisLib, 0, 0, true );
				}

				// Add "Back" if not Toplevel
				// TODO: How to go up a level when in a subcollection?
				if ( zpThisLibProps.zpCollectionId
						&& zpThisLibProps.zpCollectionId != "toplevel"
						&& jQuery(".zp-Browse-Collections-Select option.toplevel", zpThisLib).length == 0 )
					// jQuery("select.zp-Browse-Collections-Select", zpThisLib).append( "<option value='toplevel' class='toplevel'>&larr; "+zpShortcodeAJAX.txt_backtotop+"</option>\n" );
					jQuery("select.zp-Browse-Collections-Select", zpThisLib).append( "<option value='toplevel' class='toplevel'>&larr; "+zpShortcodeAJAX.txt_backtotop+"</option>\n" );

				// Remove loading indicator
				jQuery("select.zp-Browse-Collections-Select", zpThisLib).removeClass("loading").find(".loading").remove();
			},
			error: function(jqXHR)
			{
				console.log("zp: error for zplib_get_collections(): ", jqXHR.statusText);
			},
			complete: function( jqXHRr, textStatus )　{}
		});
	}


	// Get list of tags
	function zplib_get_tags ( i, zpThisLibProps, zpThisLib, request_start, request_last, update )
	{
		// Set parameter defaults
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
				'api_user_id': jQuery(".ZP_API_USER_ID", zpThisLib).text(),
				'item_type': 'tags',
				'is_dropdown': true,
				'maxtags': jQuery(".ZP_MAXTAGS", zpThisLib).text(),
				'request_start': request_start,
				'request_last': request_last,
				'update': update,
				'zpShortcode_nonce': zpShortcodeAJAX.zpShortcode_nonce
			},
			xhrFields: {
				withCredentials: true
			},
			success: function(data)
			{
				var zp_tags = jQuery.parseJSON( data );

				var zp_tag_options = "<option class='zp-List-Tags-Select' name='zp-List-Tags-Select'>--"+zpShortcodeAJAX.txt_notagsel+"--</option>\n";
				if ( zpThisLibProps.zpTagId ) zp_tag_options = "<option value='toplevel' class='toplevel'>--"+zpShortcodeAJAX.txt_unsettag+"--</option>\n";


				// Remove cached bib before adding updates
				if ( update === false ) jQuery("select.zp-List-Tags", zpThisLib).addClass("used_cache");
				if ( update === true && ! jQuery("select.zp-List-Tags", zpThisLib).hasClass("updating") )
					jQuery("select.zp-List-Tags", zpThisLib).empty().addClass("updating");

				if ( zp_tags !== 0 && zp_tags.data.length > 0 )
				{
					jQuery.each( zp_tags.data, function( index, tag )
					{
						var temp = "<option class='zp-List-Tag' value='"+tag.tag.replace(/ /g, "+")+"'";

						if ( jQuery(".ZP_TAG_ID", zpThisLib).length > 0
								&& jQuery(".ZP_TAG_ID", zpThisLib).text() == tag.tag )
						{
							temp += " selected='selected'";
						}
						temp += ">"+tag.tag+" ("+tag.meta.numItems+" "+zpShortcodeAJAX.txt_items+")</option>\n";

						zp_tag_options += temp;
					});
					jQuery("select.zp-List-Tags", zpThisLib).append( zp_tag_options );

					// Then, continue with other requests, if they exist
					if ( zp_tags.meta.request_next != false
							&& zp_tags.meta.request_next != "false" )
						zplib_get_tags ( i, zpThisLibProps, zpThisLib, zp_tags.meta.request_next, zp_tags.meta.request_last, update );
					else
						if ( ! jQuery("select.zp-List-Tags", zpThisLib).hasClass("updating") )
							zplib_get_tags ( i, zpThisLibProps, zpThisLib, 0, 0, true );

					// Remove loading indicator
					jQuery("select.zp-List-Tags", zpThisLib).removeClass("loading").find(".loading").remove();
				}
				else // Feedback
				{
					// Remove loading indicator
					jQuery("select.zp-List-Tags", zpThisLib).removeClass("loading").find(".loading").remove();

					jQuery("select.zp-List-Tags", zpThisLib).append(
						"<option rel='empty' value='empty'>"+zpShortcodeAJAX.txt_notags+"</option>"
						);
				}
			},
			error: function(jqXHR)
			{
				console.log("zp: error for zplib_get_tags(): ", jqXHR.statusText);
			},
			complete: function( jqXHRr, textStatus ) {}
		});
	}


	// Get list items
	function zplib_get_items ( i, zpThisLibProps, zpThisLib, request_start, request_last, update )
	{
		console.log('zp: calling zplib_get_items with update check?', update);
		console.log('zp: is an update needed?', zpUpdateNeeded);

		// Set parameter defaults
		if ( typeof(request_start) === "undefined" || request_start == "false" || request_start == "" )
			request_start = 0;

		if ( typeof(request_last) === "undefined" || request_last == "false" || request_last == "" )
			request_last = 0;

		// Feedback on where in item chunking we're at
		if ( jQuery(".zp-List", zpThisLib).hasClass("loading")
			 	&& jQuery(".zp-List", zpThisLib).find(".zp_display_progress").text() == "" )
		{
			jQuery(".zp-List", zpThisLib).append(
				"<div class='zp_display_progress'>"+zpShortcodeAJAX.txt_loading+" ...</div>");
		}

		jQuery.ajax(
		{
            async: true,
			url: zpShortcodeAJAX.ajaxurl,
			ifModified: true,
			data: {
				'action': 'zpRetrieveViaShortcode',
				'api_user_id': jQuery(".ZP_API_USER_ID", zpThisLib).text(),
				'is_dropdown': true,
				'item_type': 'items',

				'citeable': jQuery(".ZP_CITEABLE", zpThisLib).text(),
				'downloadable': jQuery(".ZP_DOWNLOADABLE", zpThisLib).text(),
				'showtags': zpThisLibProps.zpShowTags,
				'showimage': zpThisLibProps.zpShowImages,

				'target': zpThisLibProps.zpTarget,
				'urlwrap': zpThisLibProps.zpURLWrap,

				'collection_id': zpThisLibProps.zpCollectionId,
				'tag_id': zpThisLibProps.zpTagId,
				'get_top': true,

				'sortby': jQuery(".ZP_SORTBY", zpThisLib).text(),
				'order': jQuery(".ZP_ORDER", zpThisLib).text(),

				'update': update,
				'request_update': zpUpdateNeeded,
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

				// Remove cached bib before adding updates
				if ( update === false )
					jQuery(".zp-List", zpThisLib).addClass("used_cache");
				else if ( update === true )
					if ( ! jQuery(".zp-List", zpThisLib).hasClass("updating") )
						jQuery(".zp-List", zpThisLib).addClass("updating");

				// First, display the items from this request, if any
				if ( typeof zp_items != 'undefined'
						&& zp_items != null
						&& zp_items != 0
						&& zp_items.data.length > 0
					 	&& zp_items.data != 0 ) // REVIEW 7.3.1.1
				{
					var tempItems = "";

					// Feedback on where in item chunking we're at
					if ( ! jQuery(".zp-List", zpThisLib).hasClass("updating")
							&& ( zp_items.meta.request_last !== false && zp_items.meta.request_last != "false" )
							&& ( zp_items.meta.request_last !== 0 ) )
					{
						jQuery(".zp-List", zpThisLib).find(".zp_display_progress").html(
							"Loading "
							+ (zp_items.meta.request_next) + "-" + (zp_items.meta.request_next+50)
							+ " out of " + (parseInt(zp_items.meta.request_last)+50) + "..." );
					}

					// CHANGED (7.3): Make sure that there are items ...
					if ( zp_items.data != 0 )
					{
						jQuery.each(zp_items.data, function( index, item )
						{
							var tempItem = "";

							// Determine item reference
							var $item_ref = jQuery("div.zp-List .zp-ID-"+item.library.id+"-"+item.key, zpThisLib);

							// Year
							var tempItemYear = "0000"; if ( item.meta.hasOwnProperty('parsedDate') ) tempItemYear = item.meta.parsedDate.substring(0, 4);

							// Author
							var tempAuthor = item.data.title;
							if ( item.meta.hasOwnProperty('creatorSummary') )
								tempAuthor = item.meta.creatorSummary.replace( / /g, "-" );

							tempItem += "<div id='zp-ID-"+item.library.id+"-"+item.key+"' class='zp-Entry zpSearchResultsItem hidden";

							// Add update class to item
							if ( update === true ) tempItem += " zp_updated";

							tempItem += "' data-zp-author-year='"+tempAuthor+"-"+tempItemYear+"'";
							tempItem += "' data-zp-year-author='"+tempItemYear+"-"+tempAuthor+"'";
							tempItem += ">\n";

							if ( zpThisLibProps.zpIsAdmin
									|| ( zpThisLibProps.zpShowImages && item.hasOwnProperty('image') ) )
							{
								tempItem += "<div id='zp-Citation-"+item.key+"' class='zp-Entry-Image";
								if ( item.hasOwnProperty('image') ) tempItem += " hasImage";
								tempItem += "' rel='"+item.key+"'>\n";

								if ( item.hasOwnProperty('image') ) tempItem += "<img class='thumb' src='"+item.image[0]+"' alt='image' />\n";
								if ( zpThisLibProps.zpIsAdmin )
	                                if ( item.hasOwnProperty('image') ) tempItem += "<a title='Change Image' class='upload' rel='"+item.key+"' href='#'>"+zpShortcodeAJAX.txt_changeimg+"</a>\n";
	                                else tempItem += "<a title='Set Image' class='upload' rel='"+item.key+"' href='#'>"+zpShortcodeAJAX.txt_setimg+"</a>\n";
								if ( zpThisLibProps.zpIsAdmin && item.hasOwnProperty('image') ) tempItem += "<a title='"+zpShortcodeAJAX.txt_removeimg+"' class='delete' rel='"+item.key+"' href='#'>&times;</a>\n";

								tempItem += "</div><!-- .zp-Entry-Image -->\n";
							}

							tempItem += item.bib;

							// Show Tags
							if ( zpThisLibProps.zpShowTags
							 		&& item.data.tags.length > 0 )
							{
								tempItem += "<span class='item_key'>Tag(s): ";

								jQuery.each( item.data.tags, function ( tindex, tagval )
								{
									if ( tindex != 0 ) tempItem += ", ";
									tempItem += tagval.tag;
								});
							}

							// Show item key if admin
							if ( zpThisLibProps.zpIsAdmin )
	                            tempItem += "<label for='item_key'>"+zpShortcodeAJAX.txt_itemkey+":</label><input type='text' name='item_key' class='item_key' value='"+item.key+"'>\n";

							tempItem += "</div><!-- .zp-Entry -->\n";


							// Add this item to the list
							// Replace or skip duplicates
							if ( $item_ref.length > 0
									&& update === true ) {
								$item_ref.replaceWith( jQuery( tempItem ) );
							}
							else {
								tempItems += tempItem;
							}

						}); // Display items
					} // check that there's items

					// If no updates, then append items
					if ( update === false )
						jQuery(".zpSearchResultsContainer", zpThisLib).append( tempItems );


					// Then, continue with other requests, if they exist
					// CHANGED (7.3): Rather than calling requests that may not be
					// viewed, let the user manually load as needed
					// TODO: Load up until the next page each time?
					if ( zp_items.meta.request_next != false
							&& zp_items.meta.request_next != "false" )
					{
						// Update the pagination flag
						if ( zpThisLibProps.zpItemsFlag == true )
							window.zpBrowseList[i].paginate(zpThisLibProps.zpItemsFlag, false);
						else
							window.zpBrowseList[i].paginate(zpThisLibProps.zpItemsFlag, true);
						zpThisLibProps.zpItemsFlag = false;

						// // Update the paging
						// if ( request_start == 0
						// 		&& zp_items.meta.request_last > 0 )
						// {
							// Update the width of the inner paging element
							// jQuery(".zpSearchResultsPaging", zpThisLib).width( jQuery(".zpSearchResultsPaging a", zpThisLib).length * 50 + "px" );

							// 7.3.9: Moved to zotpress.lib.js, since applies to searchbar
							// // Add a scroller, if doesn't exist
							// if ( jQuery(".zpSearchResultsPagingScroller", zpThisLib).length == 0 )
							// {
							// 	// Update the width of the crop
							// 	// NOTE: Based on five page numbers shown
							// 	// jQuery(".zpSearchResultsPagingCrop", zpThisLib).width( jQuery(".zpSearchResultsPaging a.selected", zpThisLib).outerWidth() * 5 );

							// 	// Add the scroller
							// 	jQuery(".zpSearchResultsPagingContainerInner", zpThisLib)
							// 		.append( '<div class="zpSearchResultsPagingScroller"><span class="zpSearchResultsPagingBack">&#8249;</span><span class="zpSearchResultsPagingForward">&#8250;</span></div>' );

							// 	// Add event handler for "back"
							// 	jQuery(".zpSearchResultsPagingContainer", zpThisLib).on( 'click', '.zpSearchResultsPagingBack', function()
							// 	{
							// 		var leftPos = parseInt( jQuery(".zpSearchResultsPaging", zpThisLib).css('left') );
							// 		var shiftW = parseInt(jQuery(".zpSearchResultsPaging a.selected", zpThisLib).css('width')) + ( parseInt(jQuery(".zpSearchResultsPaging a.selected", zpThisLib).css("border-left-width")) * 2 );

							// 		// Don't go too far forward/right = past zpSearchResultsPaging.width
							// 		if ( leftPos != 0 )
							// 			jQuery(".zpSearchResultsPaging", zpThisLib).css('left', leftPos+shiftW+'px');
							// 	});

							// 	// Add event handler for "forward"
							// 	jQuery(".zpSearchResultsPagingContainer", zpThisLib).on( 'click', '.zpSearchResultsPagingForward', function()
							// 	{
							// 		var leftPos = parseInt( jQuery(".zpSearchResultsPaging", zpThisLib).css('left') );
							// 		var shiftW = parseInt(jQuery(".zpSearchResultsPaging a.selected", zpThisLib).css('width')) + ( parseInt(jQuery(".zpSearchResultsPaging a.selected", zpThisLib).css("border-left-width")) * 2 );

							// 		// Don't go too far back/left = past 0
							// 		if ( ( leftPos * -1 ) < ( jQuery(".zpSearchResultsPaging", zpThisLib).width() - 50 ) )
							// 			jQuery(".zpSearchResultsPaging", zpThisLib).css('left', leftPos-shiftW+'px');
							// 	});
							// }
						// } // Update pagination

                        // If numeric, update numbers
                        zp_relabel_numbers(zpThisLib);

						// Then, continue with the next set in the request
						zplib_get_items( i, zpThisLibProps, zpThisLib, zp_items.meta.request_next, zp_items.meta.request_last, update );
					}
					else // No further requests
					{
						window.zpBrowseList[i].paginate(zpThisLibProps.zpItemsFlag);
						zpThisLibProps.zpItemsFlag = false;

						// Remove loading and feedback
						jQuery(".zp-List", zpThisLib).removeClass("loading");
						jQuery(".zp-List", zpThisLib).find(".zp_display_progress").remove();

						// Check for updates, if needed:
						if ( zp_items.updateneeded )
						{
							console.log("zp: update needed");

							zpUpdateNeeded = true;
							// zplib_get_items ( i, zpThisLibProps, zpThisLib, request_start, request_last, update )
							zplib_get_items ( i, zpThisLibProps, zpThisLib, 0, 0, true );
							// zplib_get_items ( 0, 0, $instance, params, true );
						}

						// // Check for updates
						// if ( ! jQuery(".zp-List", zpThisLib).hasClass("updating") )
						// {
						// 	// Parameters: request_start, request_last, update
						// 	zplib_get_items ( i, zpThisLibProps, zpThisLib, 0, 0, true );
						// }
						else // If none, then re/sort and re/number
						{
							zpUpdateNeeded = false;

							var sortby = jQuery(".ZP_SORTBY", zpThisLib).text();
							var orderby = jQuery(".ZP_ORDER", zpThisLib).text();

							// Re-sort if not numbered and sorting by author or date
							if ( ["author","date"].indexOf(sortby) !== -1
									&& jQuery("div.zp-List .csl-left-margin", zpThisLib).length == 0 )
							{
								var sortOrder = "data-zp-author-year";
								if ( sortby == "date") sortOrder = "data-zp-year-author";

								jQuery("#"+zp_items.instance+" .zp-List div.zp-Entry", zpThisLib).sort(function(a,b)
								{
									var an = a.getAttribute(sortOrder).toLowerCase(),
										bn = b.getAttribute(sortOrder).toLowerCase();

									if (an > bn)
										if ( orderby == "asc" )
											return 1;
										else
											return -1;
									else if (an < bn)
										if ( orderby == "asc" )
											return -1;
										else
											return 1;
									else
										return 0;

								}).detach().appendTo("#"+zp_items.instance+" .zp-List", zpThisLib);
							}

                            // If numerical, update numbers
                            zp_relabel_numbers(zpThisLib);
						}
					}
				}

				// Message that there's no items
				else
				{
					jQuery(".zp-List", zpThisLib).removeClass("loading");
					jQuery(".zp-List", zpThisLib).find(".zp_display_progress").remove();

					jQuery(".zpSearchResultsContainer", zpThisLib).append("<p>"+zpShortcodeAJAX.txt_nocitations+"</p>\n");
				}
			},
			error: function(jqXHR)
			{
				console.log("Error for zplib_get_items(): ", jqXHR.statusText);
			}
		});
	}

});
