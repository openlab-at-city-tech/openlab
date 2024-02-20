jQuery(document).ready(function()
{
	//////////////////////////
	//						//
	//   ZOTPRESS IN-TEXT   //
	//						//
	//////////////////////////

	// TO REVIEW AFTER CUSTOMIZING FOR ASPIRE LAB:
	// - Disambiguation, especially more than 2 items
	// - Use of the JSON cache

	if ( jQuery(".zp-Zotpress-InTextBib").length > 0 )
	{
		var zp_totalItems = 0;

		// Create global array for citations per post
		window.zpIntextCitations = {};
		window.zpIntextCitationCount = {};

		jQuery(".zp-Zotpress-InTextBib").each( function( index, instance )
		{
			var $instance = jQuery(instance);
      		var zp_params = {};
			window.zpIntextCitations["post-"+jQuery(".ZP_POSTID", $instance).text()] = {};

			zp_params.zpItemkey = false; if ( jQuery(".ZP_ITEM_KEY", $instance).text().trim().length > 0 ) zp_params.zpItemkey = jQuery(".ZP_ITEM_KEY", $instance).text();

			zp_params.zpStyle = false; if ( jQuery(".ZP_STYLE", $instance).text().trim().length > 0 ) zp_params.zpStyle = jQuery(".ZP_STYLE", $instance).text();
			zp_params.zpTitle = false; if ( jQuery(".ZP_TITLE", $instance).text().trim().length > 0 && jQuery(".ZP_TITLE", $instance).text().trim() != "no" ) zp_params. zpTitle = jQuery(".ZP_TITLE", $instance).text();

			zp_params.zpShowImages = false; if ( jQuery(".ZP_SHOWIMAGE", $instance).text().trim().length > 0 ) zp_params.zpShowImages = jQuery(".ZP_SHOWIMAGE", $instance).text().trim();
			zp_params.zpShowTags = false; if ( jQuery(".ZP_SHOWTAGS", $instance).text().trim().length > 0 ) zp_params.zpShowTags = true;
			zp_params.zpDownloadable = false; if ( jQuery(".ZP_DOWNLOADABLE", $instance).text().trim().length > 0 ) zp_params.zpDownloadable = true;
			zp_params.zpShowNotes = false; if ( jQuery(".ZP_NOTES", $instance).text().trim().length > 0 ) zp_params.zpShowNotes = true;
			zp_params.zpShowAbstracts = false; if ( jQuery(".ZP_ABSTRACT", $instance).text().trim().length > 0 ) zp_params.zpShowAbstracts = true;
			zp_params.zpCiteable = false; if ( jQuery(".ZP_CITEABLE", $instance).text().trim().length > 0 ) zp_params.zpCiteable = true;
			zp_params.zpTarget = false; if ( jQuery(".ZP_TARGET", $instance).text().trim().length > 0 ) zp_params.zpTarget = true;
			zp_params.zpURLWrap = false; if ( jQuery(".ZP_URLWRAP", $instance).text().trim().length > 0 ) zp_params.zpURLWrap = jQuery(".ZP_URLWRAP", $instance).text();
			zp_params.zpHighlight = false; if ( jQuery(".ZP_HIGHLIGHT", $instance).text().trim().length > 0 ) zp_params.zpHighlight = jQuery(".ZP_HIGHLIGHT", $instance).text();

			zp_params.zpSortBy = false; if ( jQuery(".ZP_SORTBY", $instance).text().trim().length > 0 ) zp_params.zpSortBy = jQuery(".ZP_SORTBY", $instance).text();
			zp_params.zpOrder = false; if ( jQuery(".ZP_ORDER", $instance).text().trim().length > 0 ) zp_params.zpOrder = jQuery(".ZP_ORDER", $instance).text();

			zp_params.zpUpdateNeeded = false; if ( jQuery(".ZP_UPDATENEEDED", $instance).text().trim().length > 0 && jQuery(".ZP_UPDATENEEDED", $instance).text() == "true" ) zp_params.zpUpdateNeeded = true;
			zp_params.zpJSON = false; if ( jQuery(".ZP_JSON", $instance).text().trim().length > 0 ) zp_params.zpJSON = jQuery(".ZP_JSON", $instance).text().trim();

			
			// First, get encoded/serialized JSON data from PHP:
			if ( jQuery(".ZP_JSON", $instance).text().trim().length > 0 )
			{
				var zp_items = JSON.parse(decodeURIComponent(zp_params.zpJSON));

				zp_process_intext ($instance, zp_params.zpItemkey, zp_items, zp_params, false);
			}
			else // Assume no cache:
			{
				// params: request_start, request_last, $instance, params, update
				zp_get_items ( 0, 0, $instance, zp_params, false );
			}
		});

	} // Zotpress In-Text


	// Get list items
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
				'type': "intext",

				'item_key': params.zpItemkey,

				'style': params.zpStyle,
				'title': params.zpTitle,

				'showimage': params.zpShowImages,
				'showtags': params.zpShowTags,
				'downloadable': params.zpDownloadable,
				'shownotes': params.zpShowNotes,
				'showabstracts': params.zpShowAbstracts,
				'citeable': params.zpCiteable,

				'target': params.zpTarget,
				'urlwrap': params.zpURLWrap,
				'highlight': params.zpHighlight,

				'sortby': params.zpSortBy,
				'order': params.zpOrder,

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

				zp_process_intext ($instance, params.zpItemkey, zp_items, params, update);

			},
			error: function(errorThrown)
			{
				console.log( 'zp: Zotpress Error:' + errorThrown );
			}
		});

	} // function zp_get_items


	function zp_process_intext ($instance, item_keys, zp_items, params, update)
	{
		// Handle any Zotero errors
		if ( zp_items === false 
				|| zp_items.status == 'error' )
		{
			console.log( 'zp: Zotpress Error: ' + zp_items.data );

			// Hide errors if something shown
			var hideErrMsg = '';
			if ( jQuery( "#"+zp_items.instance+" .zp-List .zp-Entry" ).length > 0 )
				hideErrMsg = ' class="hide"';

			// Remove the loader
			jQuery( "#"+zp_items.instance+" .zp-List" )
				.removeClass( 'loading' )
				.append( '<p'+hideErrMsg+'>Zotpress Error: '+zp_items.data+'</p>' );
		}

		// Process as items
		else
		{
			// First, display the items from this request, if any
			if ( typeof zp_items != 'undefined'
					&& zp_items != null && parseInt(zp_items) != 0
					&& zp_items.data.length > 0 )
			{
				// var tempItems = "";
				if ( params.zpShowNotes == true ) // Changed in 7.3.7
					var tempNotes = "";
				var $postRef = jQuery($instance).parent();


				// Indicate whether cache has been used
				if ( update === false )
				{
					jQuery("#"+zp_items.instance+" .zp-List").addClass("used_cache");
				}
				else if ( update === true )
				{
					// Remove existing notes temporarily
					if ( ! jQuery("#"+zp_items.instance+" .zp-List").hasClass("updating")
							&& jQuery("#"+zp_items.instance+" .zp-Citation-Notes").length > 0 )
						jQuery("#"+zp_items.instance+" .zp-Citation-Notes").remove();

					if ( ! jQuery("#"+zp_items.instance+" .zp-List").hasClass("updating") )
						jQuery("#"+zp_items.instance+" .zp-List").addClass("updating");
				}


				// Format in-text citations
				zp_format_intext_citations( $instance, params.zpItemkey, zp_items.data, params, update );

				// Format in-text bibliography
				tempNotes = zp_format_intextbib ( $instance, zp_items, params.zpItemkey, params, update );

				// Add cached OR initial request items (first 50) to list
				if ( update === false )
				{
					// First, remove any PHP SEO container
					// REVIEW: Why?
					jQuery("#"+zp_items.instance+" .zp-SEO-Content .zp-Entry").unwrap();
				}

				// Append notes to container
				if ( params.zpShowNotes == true && tempNotes.length > 0 )
				{
					tempNotes = "<div class='zp-Citation-Notes'>\n<h4>Notes</h4>\n<ol>\n" + tempNotes;
					tempNotes = tempNotes + "</ol>\n</div><!-- .zp-Citation-Notes -->\n\n";

					jQuery("#"+zp_items.instance).append( tempNotes );
				}


				// Then, continue with other requests, if they exist
				if ( zp_items.meta.request_next != false 
						&& zp_items.meta.request_next != "false" )
				{
					zp_get_items ( zp_items.meta.request_next, zp_items.meta.request_last, $instance, params, update );
				}
				else // Otherwise, finish up and/or check for updates
				{
					// Remove loading
					jQuery("#"+zp_items.instance+" .zp-List").removeClass("loading");

					// Check for updates
					if ( ! jQuery("#"+zp_items.instance+" .zp-List").hasClass("updating") )
					{
						zp_get_items ( 0, 0, $instance, params, true );
					}
					else // Or finish up
					{
						// Check for updates, if needed:
						if ( zp_items.updateneeded )
						{
							console.log("zp: update needed");

							params.zpUpdateNeeded = true;
							zp_get_items ( 0, 0, $instance, params, true );
						}

						else // Or totally finish up and re-sort if needed:
						{
							var sortby = params.zpSortBy;
							var orderby = params.zpOrder;
	
							// Re-sort if not numbered and sorting by author or date
							if ( ['author', 'date'].indexOf(sortby) !== -1
									&& jQuery("#"+zp_items.instance+" .zp-List .csl-left-margin").length == 0 )
							{
								var sortOrder = "zp-author-date";
								if ( sortby == "date") sortOrder = "zp-date-author";
	
								jQuery("#"+zp_items.instance+" .zp-List div.zp-Entry").sort( function(a,b)
								{
									var an = jQuery(a).data(sortOrder).toLowerCase(),
										bn = jQuery(b).data(sortOrder).toLowerCase();
	
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
	
								}).detach().appendTo("#"+zp_items.instance+" .zp-List");
							}

							// If re-sorting and numbered ...
							// TODO: Make a function rather than duplicate code
							else if ( ['author', 'date'].indexOf(sortby) == -1
									&& jQuery("#"+zp_items.instance+" .zp-List .csl-left-margin").length != 0 )
							{
								console.log('zp: re-sorting numbered citations in bibliography');

								jQuery("#"+zp_items.instance+" .zp-List div.zp-Entry").sort( function(a,b)
								{
									var an = parseInt( jQuery('.csl-left-margin', a).text() ),
										bn = parseInt( jQuery('.csl-left-margin', b).text() );

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
	
								}).detach().appendTo("#"+zp_items.instance+" .zp-List");
							}

							console.log("zp: done");
							console.log("---");
						}
					}
				}
			}

			// Message that there's no items
			else
			{
				console.log("zp: no items");
				console.log("---");

				var tempPost = $instance.attr("class");
				tempPost = tempPost.replace("zp-Zotpress zp-Zotpress-InTextBib zp-Post-", "");

				// Removes loading icon and in-text data; accounts for post-ID and non-standard themes
				if ( jQuery("#post-"+tempPost).length > 0 )
					jQuery("#post-"+tempPost+" .zp-InText-Citation").removeClass("loading").remove();
				else
					jQuery("#"+$instance.attr("id")).parent().find(".zp-InText-Citation").removeClass("loading").remove();

				jQuery("#"+$instance.attr("id")+" .zp-List").removeClass("loading");
				jQuery("#"+$instance.attr("id")+" .zp-List").append("<p>There are no citations to display.</p>\n");
			}

		} // Error handling
	} // zp_process_intext



	function zp_format_intext_citations ( $instance, item_keys, item_data, params, update )
	{
		// Tested formats:
		// KEY
		// {KEY}
		// {KEY,3-9}
		// KEY,{KEY,8}
		// KEY, (3, 42)

		var intext_citations = [];

		// Create array for multiple in-text citations -- semicolon
		if ( item_keys.indexOf(";") != -1 ) 
			intext_citations = item_keys.split( ";" );
		else 
			intext_citations.push( item_keys );
		

		// Re-structure item_data
		var tempItem_data = {};
		jQuery.each( item_data, function (index, value )
		{
			if ( ! tempItem_data.hasOwnProperty(value.key) )
				tempItem_data[value.key] = value;
		});
		item_data = tempItem_data;


		// REVIEW: Account for repeat citations
		var intextGroupTracker = {};
		// var authDateList = [];

		jQuery.each( intext_citations, function (index, intext_citation)
		{
			var intext_citation_output = "";
			var $postRef = jQuery($instance).parent();

			// REVIEW: Is this the right reformatting for the ID?
			// var tempId = intext_citation.replace( /{/g, "-" ).replace( /}/g, "-" ).replace( /,/g, "_" ).replace( /\//g, "_" ).replace( /\+/g, "_" ).replace( /&/g, "_" ).replace( / /g, "_" ).replace( /:/g, "--" );
			var tempId = intext_citation.replace( /{/g, "-" ).replace( /}/g, "-" ).replace( /,/g, "_" ).replace( /:/g, "-" );
			
			// REVIEW: No longer counting by post
			// var intext_citation_id = "zp-InText-zp-ID-"+jQuery(".ZP_API_USER_ID", $instance).text()+"-"+tempId+"-"+jQuery(".ZP_POSTID", $instance).text()+"-"+(index+1);
			var intext_citation_id = "zp-InText-zp-ID-"+tempId+"-wp"+jQuery(".ZP_POSTID", $instance).text();

			// REVIEW: Account for repeat citation groups
			var intext_group_index = 0;

			// Make a tracker for multiples, if one doesn't exist
			if ( jQuery("."+intext_citation_id, $postRef ).length > 1 )
				if ( ! intextGroupTracker.hasOwnProperty(intext_citation_id) )
					intextGroupTracker[intext_citation_id] = 0;
				else // Set index
					intext_group_index = intextGroupTracker[intext_citation_id];

			var intext_citation_params = JSON.parse( jQuery("."+intext_citation_id+":eq("+intext_group_index+")", $postRef ).attr("rel").replace( /'/g, '"') );

			// REVIEW: New way based on new format
			// Expects: {api:key}, with pages in intext_citation_params

			// Divide up multiple items (if exist): always produce an array
			intext_citation_split = intext_citation.split( "},{" );

			// Prepare it as an array
			intext_citation = new Array();

			jQuery.each ( intext_citation_split, function ( id, item )
			{
				item_parts = item.split( ":" );

				// Deal with pages
				item_pages = false;

				if ( intext_citation_params.pages != "np" )
				{
					// TEST: If multiple, non-contiguous, then we expect ++
					item_pages = intext_citation_params.pages.replaceAll('++', ', ');

					// Split the string of all page references in this in-text instance
					item_pages = item_pages.split( "--" )[id];

					if ( item_pages == "np" )
						item_pages = false;
				}

				intext_citation[id] =
				{
					"api_user_id": item_parts[0].replace( "{", "" ),
					"key": item_parts[1].replace( "}", "" ),
					"post_id": jQuery(".ZP_POSTID", $instance).text(),
					"pages": item_pages,
					"class": intext_citation_id
				};
			});

			// Go through each item in the citation; can be one or more items
			var group_authors = [];

			jQuery.each( intext_citation, function( cindex, item )
			{
				var item_citation = "";
				var item_authors = "";
				var item_year ="";

				// Add to global array, if not already there
				if ( ! window.zpIntextCitations["post-"+item.post_id].hasOwnProperty(item.key) )
				{
					window.zpIntextCitations["post-"+item.post_id][item.key] = item;

					// Make sure count for this post exists:
					if ( typeof window.zpIntextCitationCount["post-"+item.post_id] === 'undefined')
						window.zpIntextCitationCount["post-"+item.post_id] = 0;

					window.zpIntextCitationCount["post-"+item.post_id]++;
					window.zpIntextCitations["post-"+item.post_id][item.key]["num"] = window.zpIntextCitationCount["post-"+item.post_id];
				}
				//else // If already there, add to item keys -- does this make sense? Just repeats the html id ...
				//{
				//	window.zpIntextCitations["post-"+item.post_id][item.key]["citation_ids"] += intext_citation_id + " ";
				//}

				// Deal with authors and etal
				////jQuery.each( item_data, function ( kindex, response_item )
				////{
				//	if ( response_item.data.key != item.key ) return true;

				if ( item_data.hasOwnProperty(item.key) )
				{
					// Deal with authors
					if ( item_data[item.key].data.hasOwnProperty("creators") )
					{
						var tempAuthorCount = 0;
						var tempAuthorTypeExists = false;

						// First, check if there are any Author types
						jQuery.each( item_data[item.key].data.creators, function( ai, author )
						{
							if ( author.creatorType == "author" ) {
								tempAuthorTypeExists = true;
								return false;
							}
						});

						// Continue, only including non-Author types if no Author types
						jQuery.each( item_data[item.key].data.creators, function( ai, author )
						{
							if ( tempAuthorTypeExists
									&& author.creatorType != "author" )
								return true;

							tempAuthorCount++;

							if ( ai != 0 && tempAuthorCount > 1 ) item_authors += ", ";
							if ( author.hasOwnProperty("name") ) item_authors += author.name;
							else if ( author.hasOwnProperty("lastName") ) item_authors += author.lastName;
						});

						// Deal with duplicates in the group
						if ( group_authors.indexOf(item_authors) == -1 )
							group_authors[group_authors.length] = item_authors;
						// TEST: This removes the author names from items with the same name+year combo
						// TEST: I don't think we want that ...
						// else
							// item_authors = "";

						// Create authors array (easier to deal with)
						item_authors = item_authors.split(", ");

						// Deal with et al for more than two authors
						if ( jQuery.isArray(item_authors)
								&& item_authors.length > 2 )
						{
							if ( intext_citation_params.etal == ""
									|| intext_citation_params.etal == "default" )
							{
								// if ( update == false
								// 		&& window.zpIntextCitations["post-"+item.post_id][item.key]["citation_ids"].length > 1 )
								// 	item_authors = item_authors[0] + " <em>et al.</em>";
							}
							else if ( intext_citation_params.etal == "yes" )
							{
								item_authors = item_authors[0] + " <em>et al.</em>";
							}
						}

						// Deal with "and" for multiples that are not using "etal"
						// NOTE: ampersand [default], and, comma, comma-amp, comma-and
						if ( jQuery.isArray(item_authors)
								&& item_authors.length > 1
								&& item_authors.indexOf("et al") == -1 )
						{
							var temp_and = " &amp; ";

							if ( intext_citation_params.and == "and" )
								temp_and = " and ";
							else if ( intext_citation_params.and == "comma-and" )
								temp_and = ", and ";
							else if ( intext_citation_params.and == "comma-amp" )
								temp_and = ", &amp; ";
							else if ( intext_citation_params.and == "comma" )
								temp_and = ", ";
							else
								temp_and = " &amp; ";

							var temp = item_authors.join().replace( /,/g, ", " );
							item_authors = temp.substring( 0, temp.lastIndexOf(", ") ) + temp_and +  item_authors[item_authors.length-1];
						}
					}
					else // Use title if no author
					{
						item_authors += item_data[item.key].data.title;
					}

					// Get year or n.d.
					if ( item_data[item.key].meta.hasOwnProperty("parsedDate") )
						item_year = item_data[item.key].meta.parsedDate.substring(0, 4);
					else
						item_year = "n.d.";

					// Format anchor title attribute
					// Apostrophe fix by Chris Wentzloff
					window.zpIntextCitations["post-"+item.post_id][item.key]["intexttitle"] = 
						"title='"+JSON.stringify(item_authors).replace( "<em>et al.</em>", "et al." ).replace( /\"/g, "" ).replace( "[", "" ).replace( "]", "" ).replace(/’/g,'&#39;').replace(/'/g,'&#39;') + " (" + item_year + "). " + item_data[item.key].data.title + ".' ";

				} // if item_data.hasOwnProperty(item.key)
				//}); // each request data item

				// Display with numbers
				if ( intext_citation_params.format.indexOf("%num%") != -1 )
				{
					var default_format = intext_citation_params.format;

					//item_citation = Object.keys(window.zpIntextCitations["post-"+item.post_id]).indexOf( item.key) + 1;
					var item_citation_num = window.zpIntextCitations["post-"+item.post_id][item.key]["num"];

					// If using parenthesis format:
					// if ( intext_citation_params.format == "(%num%)" )
					// 	item_citation = "("+item_citation+")";
					item_citation = intext_citation_params.format.replace( "%num%" , item_citation_num );
					// if ( intext_citation_params.format == "(%num%)" )
					// 	item_citation = "("+item_citation+")";

					// Deal with pages
					if ( item.pages != false )
					{
						var multip = "p. ";
						if ( item.pages.indexOf("-") != -1 )
							multip = "pp. ";

						item_citation = item_citation.replace( "%p%" , multip+item.pages );
					}
					// Get rid of %p% placeholder, if not used
					else
					{
						item_citation = item_citation.replace( ", %p%" , "" );
					}

					// If more than one item in group, remove ), (
					if ( intext_citation.length > 1 )
					{
						if ( cindex != intext_citation.length - 1 )
							item_citation = item_citation.replace( ")", "" );

						if ( cindex != 0 )
							if ( item_authors == "" )
								item_citation = item_citation.replace( "(, ", "" );
							else
								item_citation = item_citation.replace( "(", "" );
					}

					// Deal with brackets
					if ( intext_citation_params.brackets )
					{
						item_citation = item_citation.replace( "(", "" );
						item_citation = item_citation.replace( ")", "" );
					}
				}

				// Display regularly, e.g., author and year and pages
				else
				{
					var default_format = intext_citation_params.format;

					// Add in author
					item_citation = intext_citation_params.format.replace( "%a%" , item_authors );

					// Add in year
					item_citation = item_citation.replace( "%d%" , item_year );

					// Deal with pages
					if ( item.pages == false )
					{
						item_citation = item_citation.replace( ", %p%" , "" );
						item_citation = item_citation.replace( "%p%" , "" );
					}
					else // pages exist
					{
						item_citation = item_citation.replace( "%p%" , item.pages );
					}

					// If more than one item in group w parentheses, remove: ), (
					if ( ( default_format == "(%a%, %d%, %p%)" 
							|| default_format == "(%a%, %d%)" )
							&& intext_citation.length > 1 )
					{
						if ( cindex != intext_citation.length - 1 )
							item_citation = item_citation.replace( ")", "" );

						if ( cindex != 0 )
							if ( item_authors == "" )
								item_citation = item_citation.replace( "(, ", "" );
							else
								item_citation = item_citation.replace( "(", "" );
					}

					// If more than one item in group w/o parentheses, remove: ,
					if ( ( default_format == "%a%, %d%, %p%" 
						|| default_format == "%a%, %d%" )
						&& intext_citation.length > 1
						&& cindex != 0
						&& item_authors == "" )
					{
						item_citation = item_citation.replace( ", ", "" );
					}
				} // non-numerical display

				// Add anchor title and anchors
				if ( ! window.zpIntextCitations["post-"+item.post_id][item.key].hasOwnProperty("intexttitle"))
					window.zpIntextCitations["post-"+item.post_id][item.key]["intexttitle"] = "";

				item_citation = "<a rel='"+item.key+"' "+window.zpIntextCitations["post-"+item.post_id][item.key]["intexttitle"]+"class='zp-ZotpressInText' href='#zp-ID-"+item.post_id+"-"+item.api_user_id+"-"+item.key+"'>" + item_citation + "</a>";

				// Deal with <sup>
				if ( intext_citation_params.format.indexOf("sup") != "-1" )
					item_citation = "<sup>"+item_citation+"</sup>";

				// Add to intext_citation array
				intext_citation[cindex]["intext"] = item_citation;

				// Add class/id for good measure
				intext_citation[cindex]["class"] = item.class;

				// Add author-date reference for disambiguation
				// REVIEW: Assumes abbr. author plus full date is "unique"
				var tempItemDate = "0000";
				var tempItemYear = "0000";
				if ( item_data[item.key].meta.hasOwnProperty('parsedDate') ) {
					tempItemDate = item_data[item.key].meta.parsedDate;
					tempItemYear = item_data[item.key].meta.parsedDate.substring(0, 4);
				}
	
				// Author
				var tempAuthor = "";
				if ( item_data[item.key].meta.hasOwnProperty('creatorSummary') )
					tempAuthor = item_data[item.key].meta.creatorSummary.replace( /['" ]/g, "-" );
	
				intext_citation[cindex]["year"] = tempItemYear;
				intext_citation[cindex]["authdate"] = tempAuthor+'-'+tempItemDate;
				// authDateList.push(intext_citation[cindex]["authdate"]);

			}); // format each item


			// Format citation group
			var intext_citation_pre = ""; if ( intext_citation_params.brackets ) intext_citation_pre = "["; // &#91;
			var intext_citation_post = ""; if ( intext_citation_params.brackets ) intext_citation_post = "]"; // &#93;

			intext_citation_output = intext_citation_pre;

			jQuery.each( intext_citation, function(cindex, item)
			{
				// Determine separator
				if ( cindex != 0 )
				{
					if ( intext_citation_params.separator == "comma" )
						intext_citation_output += ", ";
					else
						intext_citation_output += "; ";
				}
				intext_citation_output += item.intext;

			}); // display each item

			intext_citation_output += intext_citation_post;

			// Add to placeholder
			// REVIEW: Updated ref to class instead of ID
			// jQuery("#"+intext_citation_id).removeClass("loading").html( intext_citation_output );
			jQuery("."+intext_citation_id+":eq("+intext_group_index+")", $postRef )
				.removeClass("loading")
				.html( intext_citation_output );

			// REVIEW: Increase group tracker, if needed
			if ( intextGroupTracker.hasOwnProperty(intext_citation_id) )
				intextGroupTracker[intext_citation_id]++;

		}); // each intext_citation

	} // zp_format_intext_citations



	function zp_format_intextbib ( $instance, zp_items, zp_itemkeys, params, update )
	{
		var tempNotes = "";
    	// var tempItemsArr = {}; // Format: ["itemkey", "data"]
		var tempHasNum = false;
		var zpPostID = jQuery(".ZP_POSTID", $instance).text();
		var itemNumOrderArr = []; // NOTE: 0 index always empty

		// Disambiguation by Chris Wentzloff
		var authDateArr = []; // authDateStr
		var authDateList = {}; // {authDateStr-index: {item.key, authDateStrDis}}
		var alphaArray = ['a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z'];

		jQuery.each( zp_items.data, function( index, item )
		{
			var tempItem = "";
			// var tempNotes = "";

			// Determine item reference
			// e.g., zp-ID-406-1573921-VPACLPQ8
			var $item_ref = jQuery("#"+zp_items.instance+" .zp-List #zp-ID-"+zpPostID+"-"+item.library.id+"-"+item.key);

			// Skip duplicates
			// REVIEW: Blocking the rest of formatting, but what about dupes?
			// if ( $item_ref.length > 0 )
			// 	return true;

			// Year
			// REVIEW: Now we're using the whole date
			var tempItemDate = "0000";
			var tempItemYear = "0000";
			// if ( item.data.hasOwnProperty('date') )
			// 	tempItemDate = item.data.date;
			if ( item.meta.hasOwnProperty('parsedDate') ) {
				tempItemDate = item.meta.parsedDate;
				tempItemYear = item.meta.parsedDate.substring(0, 4);
			}

			// Author
			var tempAuthor = "";
			if ( item.meta.hasOwnProperty('creatorSummary') )
				tempAuthor = item.meta.creatorSummary.replace( /['" ]/g, "-" );

			// Title
			var tempTitleAbrv = "";

			if ( params.zpTitle )
			{
				// Create abbreviation
				tempTitleAbrv = item.data.title.substr(10).replace( /['" ]/g, "-" );

				// TEST: Display the title as the year
				tempItem += "<h3>"+tempItemYear+"</h3>\n";
			}


			// Disambiguation originally by Chris Wentzloff
			// REVIEW: Wasn't working right; KS refurbished
			// TODO: Starts with b, then goes to a, then c ..?
			var authDateStr = tempAuthor+'-'+tempItemDate;
			var authDateStrDisAlpha = '';
			var authDateInstances = 1;
			var flag = false;
	
			if ( authDateArr.includes(authDateStr) )
			{
				flag = true;

				// Find out how many are there
				authDateInstances = authDateArr.filter((v) => (v === authDateStr)).length;

				// If an item with the same ID exists, then disambiguate
				authDateStrDisAlpha = alphaArray[authDateInstances-1];

				// Now count this one too
				authDateInstances += 1;
			}

			// Keep track of author-date in general
			authDateArr.push(authDateStr);
			authDateList[authDateStr+'-'+(authDateInstances-1)] = {
				'itemkey': item.key, 
				'authDateStrDisAlpha': authDateStrDisAlpha
			};

			tempItem += "<div id='zp-ID-"+jQuery(".ZP_POSTID", $instance).text()+"-"+item.library.id+"-"+item.key+"'";
			tempItem += " data-zp-author-date='"+tempAuthor+"-"+tempItemDate+"'";
			tempItem += " data-zp-date-author='"+tempItemDate+"-"+tempAuthor+"'";
			tempItem += " class='zp-Entry zpSearchResultsItem zp-Num-"+window.zpIntextCitations["post-"+zpPostID][item.key]["num"];

			// Disambiguation by Chris Wentzloff
			// ... Heavilly edited by KS

			if ( flag )
			{
				// Be sure to get the first one if it's the second one
				if ( authDateInstances == 2 ) 
				{
					var tempPrevItemKey = authDateList[authDateStr+'-0'].itemkey;
					var tempPrevItem = window.zpIntextCitations["post-"+jQuery(".ZP_POSTID", $instance).text()][tempPrevItemKey];

					// Create the disambiguation based on the year
					// var tempItemYearDis = tempPrevItem.year + authDateList[authDateStr+'-0'].authDateStrDisAlpha;
					var tempItemYearDis = tempPrevItem.year + 'b';

					// Update the data
					window.zpIntextCitations["post-"+jQuery(".ZP_POSTID", $instance).text()][tempPrevItemKey].intexttitle = 
						window.zpIntextCitations["post-"+jQuery(".ZP_POSTID", $instance).text()][tempPrevItemKey].intexttitle.replaceAll( tempItemYear, tempItemYearDis );

					window.zpIntextCitations["post-"+jQuery(".ZP_POSTID", $instance).text()][tempPrevItemKey].intext = 
						window.zpIntextCitations["post-"+jQuery(".ZP_POSTID", $instance).text()][tempPrevItemKey].intext.replaceAll( tempItemYear, tempItemYearDis );

					// Replace the bibliography
					jQuery.each( zp_items.data, function( bindex, bitem )
					{
						if ( bitem.key == tempPrevItemKey
								&& zp_items.data[bindex].bib.indexOf(tempItemYearDis) == -1 )
						{
							zp_items.data[bindex].bib = zp_items.data[bindex].bib.replace( tempItemYear, tempItemYearDis );
						}
					});

					// Check the in-text spans
					jQuery('.'+window.zpIntextCitations["post-"+jQuery(".ZP_POSTID", $instance).text()][tempPrevItemKey].class).each( function()
					{
						// Change the text of the first one
						jQuery('a[rel="'+tempPrevItemKey+'"', this).text( 
							jQuery('a[rel="'+tempPrevItemKey+'"', this).text().replaceAll( tempItemYear, tempItemYearDis ) 
						);

						jQuery('a[rel="'+tempPrevItemKey+'"', this).attr('title', 
						jQuery('a[rel="'+tempPrevItemKey+'"', this).attr('title').replaceAll( tempItemYear, tempItemYearDis ) 
						);
					});

					// Update the bib
					var $tempEntry = jQuery("#zp-ID-"+zpPostID+"-"+item.library.id+"-"+tempPrevItemKey);
					var $tempCsl = jQuery("#zp-ID-"+zpPostID+"-"+item.library.id+"-"+tempPrevItemKey+" .csl-entry");
					
					if ( $tempCsl.html().indexOf(tempItemYearDis) == -1 )
						$tempCsl.html(
							$tempCsl.html().replace( tempItemYear, tempItemYearDis )
						);

					$tempEntry
						.addClass('zp_disam')
						.attr("data-zp-author-date", tempAuthor+"-"+tempItemYearDis)
						.attr("data-zp-date-author", tempItemYearDis+"-"+tempAuthor);

				}

				// Create the disambiguation based on the year
				// var tempItemYearDis = tempItemYear + alphaArray[authDateInstances-1];
				var tempItemYearDis = tempItemYear + authDateStrDisAlpha;

				// Replace the in-text citation text
				window.zpIntextCitations["post-"+jQuery(".ZP_POSTID", $instance).text()][item.key].intexttitle = 
					window.zpIntextCitations["post-"+jQuery(".ZP_POSTID", $instance).text()][item.key].intexttitle.replaceAll( tempItemYear, tempItemYearDis );

				window.zpIntextCitations["post-"+jQuery(".ZP_POSTID", $instance).text()][item.key].intext = 
					window.zpIntextCitations["post-"+jQuery(".ZP_POSTID", $instance).text()][item.key].intext.replaceAll( tempItemYear, tempItemYearDis );

				// Need to replace the year with the new year-letter string here ...
				if ( item.bib.indexOf(tempItemYearDis) == -1 )
					item.bib = item.bib.replace( tempItemYear, tempItemYearDis );
				// And there ...
				if ( zp_items.data[index].bib.indexOf(tempItemYearDis) == -1 )
					zp_items.data[index].bib = zp_items.data[index].bib.replace( tempItemYear, tempItemYearDis );

				// Find all instances of the in-text citation, and update the year
				// jQuery('a[href="#zp-ID-'+jQuery(".ZP_POSTID", $instance).text()+'-'+item.library.id+'-'+item.key+'"]').each(function(){
				jQuery('.'+window.zpIntextCitations["post-"+jQuery(".ZP_POSTID", $instance).text()][item.key].class).each( function()
				{
					// // Handle grouped in-text citations (a) that might be together
					// if ( this.childNodes.length > 1 )
					// {
						// Change the text
						// jQuery('a:eq('+authDateInstances+')', this).text( 
						jQuery('a[rel="'+item.key+'"', this).text( 
							jQuery('a[rel="'+item.key+'"', this).text().replaceAll( tempItemYear, tempItemYearDis ) 
						);
						// Change the title
						jQuery('a[rel="'+item.key+'"', this).attr('title', 
							jQuery('a[rel="'+item.key+'"', this).attr('title').replaceAll( tempItemYear, tempItemYearDis ) 
						);
					// }
					// else {
					// 	// Change the text
					// 	jQuery('a', this).text( 
					// 		jQuery('a', this).text().replaceAll( tempItemYear, tempItemYearDis ) 
					// 	);
					// 	// Change the title
					// 	jQuery('a', this).attr('title', 
					// 		jQuery('a', this).attr('title').replaceAll( tempItemYear, tempItemYearDis ) 
					// 	);
					// }
				});

				// // Update the bib
				var $tempEntry = jQuery("#zp-ID-"+zpPostID+"-"+item.library.id+"-"+item.key);
				// var $tempEntry = jQuery("#"+zp_items.instance+" .zp-List .zp-Entry[data-zp-author-date='"+tempAuthor+"-"+tempItemYear+"']");
				// var $tempCsl = jQuery("#zp-ID-"+zpPostID+"-"+item.library.id+"-"+item.key+" .csl-entry");
				var $tempCsl = jQuery("#zp-ID-"+zpPostID+"-"+item.library.id+"-"+item.key+" .csl-entry");

				if ( $tempCsl.html()
						&& $tempCsl.html().indexOf(tempItemYearDis) == -1 )
					$tempCsl.html(
						$tempCsl.html().replace( tempItemYear, tempItemYearDis )
					);

				$tempEntry
					.addClass('zp_disam')
					.attr("data-zp-author-date", tempAuthor+"-"+tempItemYearDis)
					.attr("data-zp-date-author", tempItemYearDis+"-"+tempAuthor);

			} // if flag for disambiguation needed




			// Add update class to item
			if ( update === true ) tempItem += " zp_updated";


			// Image
			if ( jQuery("#"+zp_items.instance+" .ZP_SHOWIMAGE").text().trim().length > 0
					&& item.hasOwnProperty('image') )
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
				tempItem += "</div><!-- .zp-Entry-Image -->\n";
			}
			else
			{
				tempItem += "'>\n";
			}

			// Make sure forcenumbers is applied, if needed
			// NOTE: item may change
			var temp = zp_forcenumbers( zp_items.instance, item, zpPostID );

			item = temp.item;
			itemNumOrderArr[temp.itemNumOrder.order] = temp.itemNumOrder.key;

			if ( /csl-left-margin/i.test(item.bib) )
				tempHasNum = true;

			// Then add the (modified) bib
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
			if ( params.zpShowNotes == true
					&& item.hasOwnProperty('notes') )
			{
				tempNotes += item.notes;
			}

			// Add this item to the list; replace or skip duplicates
			if ( $item_ref.length > 0
					&& update === true )
			{
				$item_ref.replaceWith( jQuery( tempItem ) );
			}
			else // When not updating ...
			{
				// Add new items, freshly retrieved
				if ( $item_ref.length == 0 )
				{
					jQuery("#"+zp_items.instance+" .zp-List").append( tempItem );
				}
				else // Or replace existing
				{
					$item_ref.replaceWith( jQuery( tempItem ) );
				}
			}

		}); // each item


		// var tempItemsOrdered = '';

		// If in-text formatted as number (i.e. %num%), re-order
		if ( tempHasNum
				&& update === false )
		{
			// If first request (first 50)
			if ( jQuery("#"+zp_items.instance+" .zp-List .zp-Entry").length == 0 )
			{
				// REVIEW: Not necessary?
				// console.log("IT: NUM: no entries, not updating");
			}
			else // Subsequent requests for this bib
			{
				for ( var num = 1; num < itemNumOrderArr.length; num++ )
				{
					// Get item info
					var item = window.zpIntextCitations["post-"+zpPostID][itemNumOrderArr[num]];
					var $item = jQuery("#" + "zp-ID-"+zpPostID + "-" + window.zpIntextCitations["post-"+zpPostID][itemNumOrderArr[num]].api_user_id + "-" + itemNumOrderArr[num]);

					// Insert into proper place
					jQuery("#"+zp_items.instance+" .zp-List").append( $item );
				}
			}
		}
		else
		{
			// REVIEW: Not necessary?
			// console.log("IT: not hasnum or updating");
		}

		return tempNotes;

	} // function zp_format_intextbib



	function zp_forcenumbers( zp_instance, zp_item, zpPostID )
	{
		var itemNumOrder = { 'order' : 0, 'item_key' : '' };

		// Only forcenumbers if desired, and needed
		if ( /csl-left-margin/i.test(zp_item.bib) )
		{
			var $item_content = jQuery.parseHTML(zp_item.bib);
			var item_num_content = jQuery(".csl-left-margin", $item_content).text();
			item_num_content = item_num_content.replace( item_num_content.match(/\d+/)[0], window.zpIntextCitations["post-"+zpPostID][zp_item.key]["num"] );

			jQuery(".csl-left-margin", $item_content).text(item_num_content);

			// Update the HTML output
			zp_item.bib = jQuery('<div>').append( $item_content ).html();

			// Add to the order array
			itemNumOrder.order = window.zpIntextCitations["post-"+zpPostID][zp_item.key]["num"];
			itemNumOrder.key = zp_item.key;
		}
		else // no left margin = no numbers yet
		{
			// But only number if asked for
			if ( jQuery("#"+zp_instance+" .ZP_FORCENUM").text().length > 0
					&& jQuery("#"+zp_instance+" .ZP_FORCENUM").text() == "1" )
			{
				var $item_content = jQuery.parseHTML(zp_item.bib);

				jQuery('.csl-entry', $item_content).prepend( '<div class="csl-left-margin" style="display: inline;">'+window.zpIntextCitations["post-"+zpPostID][zp_item.key]['num']+'. </div>' );

				// Update the HTML output
				zp_item.bib = jQuery('<div>').append( $item_content ).html();

				// Add to the order array
				itemNumOrder.order = window.zpIntextCitations["post-"+zpPostID][zp_item.key]['num'];
				itemNumOrder.key = zp_item.key;
			}
		}

		return { 'itemNumOrder' : itemNumOrder, 'item' : zp_item };

	} // function zp_forcenumbers


});
