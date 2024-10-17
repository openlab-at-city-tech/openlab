jQuery(document).ready(function()
{

    /****************************************************************************************
     *
     *     ZOTPRESS LIB SEARCHBAR
     *
     ****************************************************************************************/

	// TODO: notes, abstract, target
	// TODO: will call the same term if copy-pasted in
	// TODO: Is it actually updating?

	if ( jQuery(".zp-Zotpress-SearchBox").length > 0 )
	{
		var zp_totalItems = 0;
		var zpItemsFlag = true;
		var zpItemNum = 1;
		var zpLastTerm = "";
		var zpSearchBarParams = "";
		var zpSearchBarSource = zpShortcodeAJAX.ajaxurl + "?action=zpRetrieveViaShortcode&zpShortcode_nonce="+zpShortcodeAJAX.zpShortcode_nonce;
		var zpShowTags = false; if ( jQuery(".ZP_SHOWTAGS").length > 0 && parseInt( jQuery(".ZP_SHOWTAGS").text() ) == "1" ) zpShowTags = true;
		var zpShowImages = false; if ( jQuery(".ZOTPRESS_AC_IMAGES").length > 0 ) zpShowImages = true;
		var zpUpdateNeeded = false; if ( jQuery(".ZP_UPDATENEEDED").text().trim().length > 0 && jQuery(".ZP_UPDATENEEDED").text() == "true" ) zpUpdateNeeded = true;


		// This just sets the params
		function zp_set_lib_searchbar_params( filter, start, last )
		{
			// Set parameter defaults
			if ( typeof(filter) === "undefined" || filter == "false" || filter == "" )
				filter = false;
			if ( typeof(start) === "undefined" || start == "false" || start == "" )
				start = false;
			if ( typeof(last) === "undefined" || last == "false" || last == "" )
				last = false;

			zpSearchBarParams = "";

			// Get param basics
			zpSearchBarParams += "&api_user_id="+jQuery(".ZOTPRESS_USER").val();
			zpSearchBarParams += "&item_type=items";
			zpSearchBarParams += "&downloadable="+jQuery(".ZOTPRESS_AC_DOWNLOAD").val();
			zpSearchBarParams += "&style="+jQuery(".ZP_STYLE").text();
			zpSearchBarParams += "&sortby="+jQuery(".ZP_SORTBY").text();
			zpSearchBarParams += "&order="+jQuery(".ZP_ORDER").text();
			zpSearchBarParams += "&citeable="+jQuery(".ZOTPRESS_AC_CITE").val();

			// Deal with possible max results
			if ( jQuery(".ZOTPRESS_AC_MAXRESULTS").val().length > 0 )
				zpSearchBarParams += "&maxresults=" + jQuery(".ZOTPRESS_AC_MAXRESULTS").val();

			// Deal with possible showtags
			if ( zpShowTags ) zpSearchBarParams += "&showtags=true";

			// Deal with possible showimage
			if ( zpShowImages ) zpSearchBarParams += "&showimage=true";

			// Deal with next and last
			if ( start !== false ) zpSearchBarParams += "&request_start="+start;
			if ( last !== false ) zpSearchBarParams += "&request_last="+last;

			// Deal with updating:
			zpSearchBarParams += "&update=true";
			zpSearchBarParams += "&request_update="+zpUpdateNeeded;

			// Deal with possible filters
			if ( filter )
				zpSearchBarParams += "&filter="+filter;
			else if ( jQuery("input[name=zpSearchFilters]").length > 0 )
				zpSearchBarParams += "&filter="+jQuery("input[name=zpSearchFilters]:checked").val();
		}
		zp_set_lib_searchbar_params( false, false, false );


		// Deal with change in filters
		jQuery("input[name='zpSearchFilters']").click(function()
		{
			// Update filter param
			if ( jQuery("input[name=zpSearchFilters]").length > 0 )
				zp_set_lib_searchbar_params ( jQuery(this).val(), false, false );

			// Update autocomplete URL
			jQuery("input.zp-Zotpress-SearchBox-Input").autocomplete( "option", "source", zpSearchBarSource+zpSearchBarParams );

			// If there's already text, search again
			if ( jQuery("input.zp-Zotpress-SearchBox-Input").val().length > 0
					&& jQuery("input.zp-Zotpress-SearchBox-Input").val() != zpShortcodeAJAX.txt_typetosearch )
				jQuery("input.zp-Zotpress-SearchBox-Input").autocomplete("search");
		});


		// Set up autocomplete
		jQuery("input.zp-Zotpress-SearchBox-Input")
			.bind( "keydown", function( event )
			{
				// Don't navigate away from the input on tab when selecting an item
				if ( event.keyCode === jQuery.ui.keyCode.TAB
						&& jQuery( this ).data( "autocomplete" ).menu.active )
					event.preventDefault();

				// Don't submit the form when pressing enter
				if ( event.keyCode === 13 )
					event.preventDefault();
			})
			.bind( "focus", function( event )
			{
				// Remove help text on focus
				if ( jQuery(this).val() == zpShortcodeAJAX.txt_typetosearch ) {

					jQuery(this).val("");
					jQuery(this).removeClass("help");
				}
			})
			.bind( "blur", function( event )
			{
				// Don't search if the term doesn't change
				if ( jQuery.trim(jQuery(this).val()) == zpLastTerm )
					jQuery(this).attr('autocomplete', 'off');
				else
					jQuery(this).attr('autocomplete', 'on');

				// Add help text on blur, if nothing there
				if ( jQuery.trim(jQuery(this).val()) == "" ) {

					jQuery(this).val(zpShortcodeAJAX.txt_typetosearch);
					jQuery(this).addClass("help");
				}
			})
			.autocomplete({
				source: zpSearchBarSource+zpSearchBarParams,
				minLength: jQuery(".ZOTPRESS_AC_MINLENGTH").val(),
				focus: function() {
					// prevent value inserted on focus
					return false;
				},
				change: function() {

					// Don't search if the term doesn't change
					if ( jQuery.trim(jQuery(this).val()) == zpLastTerm )
						return false;
					// 	jQuery(this).attr('autocomplete', 'off');
					// else
					// 	jQuery(this).attr('autocomplete', 'on');	
				},
				search: function( event, ui )
				{
					// Don't search if the term doesn't change
					var tempCurrentTerm = jQuery(this).val();

					if ( event.hasOwnProperty('currentTarget') ) 
						tempCurrentTerm = event.currentTarget.value;

					console.log('zp: autocomplete search starts');
					//  for', tempCurrentTerm, zpSearchBarParams);
										
					// TODO: Is this BROKEN!??
					// Check if update needed:
					jQuery.ajax({
						// url: zpShortcodeAJAX.ajaxurl,
						url: zpSearchBarSource+zpSearchBarParams+"&term="+tempCurrentTerm,
						ifModified: true,
						xhrFields: {
							withCredentials: true
						},
						success: function(data)
						{
							var zp_items = jQuery.parseJSON( data );
			
							if ( zp_items.updateneeded )
								zpUpdateNeeded = zp_items.updateneeded;
						
							console.log('zp: calling zp_get_items with update check?', 'always');
							console.log('zp: is an update needed?', zpUpdateNeeded);			
						},
						error: function(errorThrown)
						{
							console.log("zp: Zotpress via WP AJAX Error: ", errorThrown);
						}
					});


					// Reset item numbering
					zpItemNum = 1;
					console.log(tempCurrentTerm, tempCurrentTerm, zpLastTerm);

					if ( zpItemsFlag == true
							|| ( tempCurrentTerm && tempCurrentTerm != zpLastTerm ) )
					{
						console.log('zp: show loading for new query');

						// 7.3.9: Reset this flag
						zpItemsFlag = true;

						// Show loading icon
						jQuery(".zp-List .zpSearchLoading").addClass("show");

						// Empty and hide pagination
						if ( jQuery(".zpSearchResultsPaging").length > 0 ) {

							jQuery(".zpSearchResultsPaging").empty();
							jQuery(".zpSearchResultsPagingContainer").hide();
						}

						// Remove old results
						jQuery(".zpSearchResultsContainer").empty();

						// // Reset the query
						zp_set_lib_searchbar_params( false, false, false );
						jQuery("input.zp-Zotpress-SearchBox-Input").autocomplete( "option", "source", zpSearchBarSource+zpSearchBarParams );

						// Reset the current pagination
						window.zpPage = 1;

						// if ( zpItemsFlag == true 
						// 		&& tempCurrentTerm )
						if ( tempCurrentTerm )
							zpLastTerm = tempCurrentTerm;
					}
				},
				response: function( event, ui )
				{
					// Don't search if the term doesn't change
					// if ( jQuery.trim(jQuery(this).val()) != zpLastTerm ) {
						
						console.log('zp: autocomplete response?', ui.content[4]);

						var tempCurrentTerm = jQuery(this).val();

						// Remove loading icon
						jQuery(".zp-List .zpSearchLoading").removeClass("show");

						// First, deal with any errors or blank results
						if ( ui.content == "0"
								|| ui.content[0].label == "empty" )
						{
							if ( jQuery(".zpSearchResultsPaging").length > 0 ) {

								jQuery(".zpSearchResultsPaging").empty();
								jQuery(".zpSearchResultsPagingContainer").hide();
							}
							jQuery(".zpSearchResultsContainer").append("<p>No items found.</p>\n");
						}

						// Display list of search results
						else
						{
							// NEW in 7.3.6: Why is it [4] instead of [3] now?
							var zp_items = ui.content[4];
		
							zp_totalItems += zp_items.length;
							// if ( update ) console.log("zp: running update for items:",zp_totalItems,"->",zp_items.length);
							// else console.log("zp: adding items:",zp_totalItems,"->",zp_items.length);
							
							zp_format_intext_results(zp_items, zpShowTags, zpItemNum);

							if ( zpItemsFlag == true )
								// window.zpACPagination(zpItemsFlag, false);
								window.zpBrowseList[0].paginate(zpItemsFlag, false);
							else
								// window.zpACPagination(zpItemsFlag, true);
								window.zpBrowseList[0].paginate(zpItemsFlag, true);
							zpItemsFlag = false;

							zpLastTerm = tempCurrentTerm;

							console.log('zp: request next:', ui.content[3].request_next, 'request last:', ui.content[3].request_last);

							// Then, continue with other requests, if they exist
							// NEW in 7.3.6: Why is it [3] instead of [2] now?
							// if ( ui.content[3].request_next != false
							// 		&& ui.content[3].request_next != "false" )
							// {
							if ( Number.isInteger(ui.content[3].request_next)
									&& ui.content[3].request_next > 0 ) {

								zp_set_lib_searchbar_params( false, ui.content[3].request_next, ui.content[3].request_last );

								jQuery("input.zp-Zotpress-SearchBox-Input").autocomplete( "option", "source", zpSearchBarSource+zpSearchBarParams );
								jQuery("input.zp-Zotpress-SearchBox-Input").autocomplete( "search" );
							}

							// If no other requests, check for updates needed
							else {

								if ( zpUpdateNeeded )
								{
									console.log("zp: now for the update ...");

									// TODO: For some reason, setting
									// request_update=true
									// is not actually requesting an update ...
									jQuery("input.zp-Zotpress-SearchBox-Input").autocomplete( "option", "source", zpSearchBarSource+zpSearchBarParams.replace("request_update=false", "request_update=true") );
									jQuery("input.zp-Zotpress-SearchBox-Input").autocomplete( "search" );

									zpUpdateNeeded = false;

									// // Add loading icon
									// jQuery(".zp-List .zpSearchLoading").addClass("show");

									// jQuery.ajax({
									// 	url: zpSearchBarSource+zpSearchBarParams.replace("request_update=false", "request_update=true")+"&term="+zpLastTerm,
									// 	ifModified: true,
									// 	xhrFields: {
									// 		withCredentials: true
									// 	},
									// 	success: function(data)
									// 	{
									// 		var zp_items = jQuery.parseJSON( data );
							
									// 		// if ( zp_items.updateneeded )
									// 		// 	zpUpdateNeeded = zp_items.updateneeded;
										
									// 		// console.log('zp: calling zp_get_items with update check?', 'always');
									// 		// console.log('zp: is an update needed?', zpUpdateNeeded);

									// 		// Empty and hide pagination
									// 		if ( jQuery(".zpSearchResultsPaging").length > 0 ) {

									// 			jQuery(".zpSearchResultsPaging").empty();
									// 			jQuery(".zpSearchResultsPagingContainer").hide();
									// 		}

									// 		// Remove old results
									// 		jQuery(".zpSearchResultsContainer").empty();

									// 		// Format and add new results
									// 		zp_format_intext_results(zp_items.data, zpShowTags, zpItemNum);
											
									// 		// Reset the query
									// 		zp_set_lib_searchbar_params( false, false, false );
									// 		jQuery("input.zp-Zotpress-SearchBox-Input").autocomplete( "option", "source", zpSearchBarSource+zpSearchBarParams );
									// 		jQuery("input.zp-Zotpress-SearchBox-Input").autocomplete( "search" );

									// 		// Reset the current pagination
									// 		window.zpPage = 1;

									// 		window.zpBrowseList[0].paginate(zpItemsFlag, true);
									// 		zpItemsFlag = false;
			
									// 		zpUpdateNeeded = false;

									// 		// Remove loading icon
									// 		jQuery(".zp-List .zpSearchLoading").removeClass("show");
									// 	},
									// 	error: function(errorThrown)
									// 	{
									// 		console.log("zp: Zotpress via WP AJAX Error: ", errorThrown);
									// 	}
									// });
								}
								else // No update needed
								{
									// window.zpACPagination(zpItemsFlag, true);
									window.zpBrowseList[0].paginate(zpItemsFlag, true);
									zpItemsFlag = false;
								}
							}
						}
					// }
				},
				open: function ()
				{
					// Don't show the dropdown
					jQuery(".ui-autocomplete").hide();
				}
			});

	} // Zotpress SearchBar Library



	function zp_format_intext_results(zp_items, zpShowTags, zpItemNum)
	{
		jQuery.each( zp_items, function( index, item )
		{
			var tempItem = "<div id='zp-Entry-"+item.key+"' class='zp-Entry zpSearchResultsItem hidden'>\n";

			if ( zpShowImages
					&& item.hasOwnProperty('image') )
			{
				tempItem += "<div id='zp-Citation-"+item.key+"' class='zp-Entry-Image hasImage' rel='"+item.key+"'>\n";
				tempItem += "<img class='thumb' src='"+item.image[0]+"' alt='image' />\n";
				tempItem += "</div><!-- .zp-Entry-Image -->\n";
			}

			// Replace num due to style
			if ( item.bib.indexOf("[1]") != -1 )
			{
				item.bib = item.bib.replace("[1]", "["+zpItemNum+"]");
				zpItemNum++;
			}

			// Bibliography entry
			tempItem += item.bib;

			if ( ( zpShowTags
					|| jQuery("input.tag[name=zpSearchFilters]:checked").length > 0 )
					&& item.data.tags.length > 0 )
			{
				tempItem += "<span class='item_key'>Tag(s): ";

				jQuery.each( item.data.tags, function ( tindex, tagval )
				{
					if ( tindex != 0 ) tempItem += ", ";
					tempItem += tagval.tag;
				});
			}

			jQuery(".zpSearchResultsContainer").append(tempItem+"</div><!-- .zp-Entry -->\n");

			jQuery(".zpSearchResultsPagingContainer").show();
		});
	} // zp_format_intext_results()

});
