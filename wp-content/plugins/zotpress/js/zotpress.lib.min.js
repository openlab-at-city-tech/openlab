jQuery(document).ready(function()
{
	if ( jQuery(".zp-Browse").length > 0 )
	{
		// Set up global array
		window.zpBrowseList = new Array();

		// Go through each instance
		jQuery(".zp-Browse").each( function(l)
		{
			var $thisLib = jQuery(this);

			// Add to global array
			window.zpBrowseList.push(
				{
					'id' : $thisLib.attr('id'),
					'page' : 1,
					'maxPerPage' : 10,
					'paginate' : ''
				}
			);

			var urlParams = new URLSearchParams(window.location.search);

			// Add inline anchor scroll down
			if ( urlParams.get('browsing') )
			{
				var t_offset = 20;
				if ( jQuery("#wpadminbar").length > 0 )
					t_offset = jQuery("#wpadminbar").height();

				jQuery([document.documentElement, document.body]).scrollTop(
					jQuery('a[name="'+$thisLib.attr('id')+'"]').offset().top - t_offset
				);
			}

			// Set max per page (pagination)
		    if ( jQuery(".ZOTPRESS_AC_MAXPERPAGE", $thisLib).length > 0
					&& jQuery(".ZOTPRESS_AC_MAXPERPAGE", $thisLib).val().length > 0 )
				window.zpBrowseList[l].maxPerPage = jQuery(".ZOTPRESS_AC_MAXPERPAGE", $thisLib).val();

			// Set up pagination
			// window.zpBrowseList[l].paginate = function zpACPagination(is_new_query, do_append)
			window.zpBrowseList[l].paginate = function(is_new_query=false, do_append=false)
			{
				// e.g.
				// window.zpBrowseList[l].maxPerPage = 10
				// window.zpBrowseList[l].page = 3
				// 0-9, 10-19, 20-29 ...

				// Set parameter defaults
				if ( typeof(do_append) === "undefined"
						|| do_append == "false" || do_append == "" )
					do_append = false;
				else
					do_append = true;

				if ( is_new_query == true )
					window.zpBrowseList[l].page = 1;

				// Show the results given the current pagination page
				jQuery(".zpSearchResultsContainer", $thisLib)
					.children()
					.addClass("hidden")
					.slice( (window.zpBrowseList[l].page-1)*window.zpBrowseList[l].maxPerPage, (window.zpBrowseList[l].page*window.zpBrowseList[l].maxPerPage) )
					.removeClass("hidden");

				// Generate paging menu
				if ( do_append
						|| is_new_query == true
						|| jQuery(".zpSearchResultsPaging", $thisLib).children().length == 0 )
				{
					jQuery(".zpSearchResultsPaging", $thisLib).empty();

					for (i = 1; i < (Math.ceil(jQuery(".zpSearchResultsContainer", $thisLib).children().length/window.zpBrowseList[l].maxPerPage)+1); i++)
					{
						if ( jQuery(".zpSearchResultsPagingContainer .title", $thisLib).length == 0 )
							jQuery(".zpSearchResultsPagingContainerInner", $thisLib).prepend("<span class='title'>Page</span>");

						if ( i == 1 )
							jQuery(".zpSearchResultsPaging", $thisLib).append("<a class='selected' href='javascript:void(0)'><span>"+i+"</span></a>");
						else
							jQuery(".zpSearchResultsPaging", $thisLib).append("<a href='javascript:void(0)'><span>"+i+"</span></a>");
					}
				}

				// Add a scroller, if doesn't exist
				if ( jQuery(".zpSearchResultsPagingScroller", $thisLib).length == 0 )
				{
					// Update the width of the crop
					// NOTE: Based on five page numbers shown
					// jQuery(".zpSearchResultsPagingCrop", $thisLib).width( jQuery(".zpSearchResultsPaging a.selected", $thisLib).outerWidth() * 5 );

					// Add the scroller
					jQuery(".zpSearchResultsPagingContainerInner", $thisLib)
						.append( '<div class="zpSearchResultsPagingScroller"><span class="zpSearchResultsPagingBack">&#8249;</span><span class="zpSearchResultsPagingForward">&#8250;</span></div>' );

					// Add event handler for "back"
					jQuery(".zpSearchResultsPagingContainer", $thisLib).on( 'click', '.zpSearchResultsPagingBack', function()
					{
						var leftPos = parseInt( jQuery(".zpSearchResultsPaging", $thisLib).css('left') );
						var shiftW = parseInt(jQuery(".zpSearchResultsPaging a.selected", $thisLib).css('width')) + ( parseInt(jQuery(".zpSearchResultsPaging a.selected", $thisLib).css("border-left-width")) * 2 );

						// Don't go too far forward/right = past zpSearchResultsPaging.width
						if ( leftPos != 0 )
							jQuery(".zpSearchResultsPaging", $thisLib).css('left', leftPos+shiftW+'px');
					});

					// Add event handler for "forward"
					jQuery(".zpSearchResultsPagingContainer", $thisLib).on( 'click', '.zpSearchResultsPagingForward', function()
					{
						var leftPos = parseInt( jQuery(".zpSearchResultsPaging", $thisLib).css('left') );
						var shiftW = parseInt(jQuery(".zpSearchResultsPaging a.selected", $thisLib).css('width')) + ( parseInt(jQuery(".zpSearchResultsPaging a.selected", $thisLib).css("border-left-width")) * 2 );

						// Don't go too far back/left = past 0
						if ( ( leftPos * -1 ) < ( jQuery(".zpSearchResultsPaging", $thisLib).width() - 50 ) )
							jQuery(".zpSearchResultsPaging", $thisLib).css('left', leftPos-shiftW+'px');
					});
				}

				// Show it
				if ( jQuery(".zpSearchResultsPagingContainer", $thisLib).is(":hidden") )
					jQuery(".zpSearchResultsPagingContainer", $thisLib).show();

			};

			jQuery('body').on('click', '#'+$thisLib.attr('id')+' .zpSearchResultsPaging a', function()
			{
				var zpLibOffsetTop = jQuery(".zp-List", $thisLib).offset().top;

				// Highlight this link
				jQuery(".zpSearchResultsPaging a", $thisLib).removeClass("selected");
				jQuery(this).addClass("selected");

				// Update pagination page
				window.zpBrowseList[l].page = jQuery(this).text();

				// Scroll back up to top of list
				setTimeout(function() {
					jQuery([document.documentElement, document.body]).scrollTop(
						zpLibOffsetTop
					);
				}, 50);

				// Update
				window.zpBrowseList[l].paginate(false);
			});


			/* ***********************
			*
			*     DROPDOWN ONLY
			*
			************************ */

			// NAVIGATE BY COLLECTION

			jQuery('div.zp-Browse-Bar', $thisLib)
				.delegate("select.zp-Browse-Collections-Select", "change", function() // .change
			{
				if ( jQuery(this).val() != "blank" ) // top level
					zpNavigator( 'collections', jQuery(this), $thisLib, urlParams );

			});

			// REVIEW: Not needed?
			// Deal with single "back" option
			jQuery('div.zp-Browse-Bar', $thisLib)
				.delegate("select.zp-Browse-Collections-Select", "mousedown", function() // .change
			{
				// nothing
			});


			// NAVIGATE BY TAG

			jQuery('div.zp-Browse-Bar', $thisLib)
				.delegate("select.zp-List-Tags", "change", function()
			{
				zpNavigator( 'tags', jQuery(this), $thisLib, urlParams );

			}); // nav by tag

			function zpNavigator(browseType, $this, $thisLib, urlParams) {

				var zpHref = window.location.href.split("?");

				// Set page to Zotpress
				if ( ! urlParams.get('page')
						|| urlParams.get('page') != 'Zotpress' )
					urlParams.set('page', 'Zotpress');

				// Set api_user_id, if needed
				if ( ! urlParams.get('api_user_id')
						&& jQuery(".zp-FilterByAccount", $thisLib).length > 0 )
					urlParams.set('api_user_id', jQuery("option:selected", jQuery("."+$thisLib.attr("id")+" .zp-FilterByAccount")).val());

				if ( browseType == "collections" )
				{
					if ( $this.val() != "toplevel" )
					{
						// Get selected collection
						var temp = jQuery("option:selected", $this).text().split(" (");

						// var new_location = zpHref[0] + "?collection_id=" + jQuery("option:selected", this).val() + "&collection_name=" + temp[0].replace( / /g, "+" ) + zp_extra_params;
						// var new_location = zpHref[0] + "?collection_id=" + jQuery("option:selected", this).val() + "&collection_name=" + encodeURI(encodeURIComponent(temp[0])) + zp_extra_params;
						// var new_location = zpHref[0] + "?subcollection_id=" + jQuery("option:selected", this).val() + "&subcollection_name=" + encodeURI(encodeURIComponent(temp[0])) + zp_extra_params;
						urlParams.set('subcollection_id', jQuery("option:selected", $this).val());
						urlParams.set('subcollection_name', encodeURI(temp[0]));

						// Assume browsing
						urlParams.set('browsing', 'true');
					}
					else // Go to toplevel
					{
						// REVIEW: Force toplevel
						// if ( jQuery(".ZP_COLLECTION_ID", $thisLib).length === 0 )
						// REVIEW 2: If they click it, then do it
						// if ( jQuery(".ZP_TOPLEVEL", $thisLib).length > 0 )
						// {
							urlParams.set('toplevel', 'toplevel');
							// if ( zp_extra_params.length > 0 )
							// 	new_location += "&";
							// else
							// 	new_location += "?"
							// new_location += "toplevel=toplevel";
							urlParams.delete('subcollection_id');
							urlParams.delete('subcollection_name');
						// }

						// Assume browsing
						urlParams.set('browsing', 'true');
					}

					// Unset tags
					urlParams.delete('lib_tag');

				} // browseType = collections

				else if ( browseType == "tags"
		 				&& $this.val() != "--No Tag Selected--" )
				{
					if ( $this.val() != "toplevel" )
					{
						// Assume selected a tag if not top level
						urlParams.set('lib_tag', jQuery("option:selected", $this).val());

						// Assume browsing
						urlParams.set('browsing', 'true');
					}
					else // toplevel
					{
						urlParams.delete('lib_tag');
						// urlParams.delete('browsing');
						// urlParams.delete('page');
						// urlParams.delete('api_user_id');
					}

					// Unset Collections
					urlParams.delete('subcollection_id');
					urlParams.delete('subcollection_name');

				} // browseType = tags

				// Turn params to string
				urlParams = urlParams.toString();

				// Make new url
				var newURL = new URL(zpHref[0]+ '?' + urlParams);

				// Go to the new url
				window.location = newURL;
			}

		}); // each Library

	} // Zotpress Library

});
