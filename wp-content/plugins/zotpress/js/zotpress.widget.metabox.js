jQuery(document).ready(function()
{


    /***********************************
	*
	*     ZOTPRESS METABOX
	*
	************************************/

    if ( jQuery("#zp-ZotpressMetaBox").length > 0 )
	{
		jQuery("#zp-ZotpressMetaBox").tabs(
		{
			activate: function( event, ui )
			{
				if ( ui.newPanel.attr('id') == "zp-ZotpressMetaBox-InText" )
					jQuery("#zp-ZotpressMetaBox-List").addClass("intext");
				else
					jQuery("#zp-ZotpressMetaBox-List").removeClass("intext");
			}
		});
	}



    /////////////////////////////////
	//                             //
	//   ZOTPRESS BIBLIO CREATOR   //
	//                             //
	/////////////////////////////////

    //
    //   VARIABLES
    //

    window.zpBiblio = {
		"author": false, "year": false, "style": false, "sortby": false,
        "sort": false, "image": false, "download": false, "notes": false,
        "zpabstract": false, "cite": false, "title": false, "limit": false
	};
    window.zpInText = {
		"format": false, "etal": false, "and": false, "separator": false,
        "brackets": false
	};
    window.zpInTextBib = {
		"style": false, "sortby": false, "sort": false, "image": false,
        "title": false, "download": false, "zpabstract": false,
        "notes": false, "cite": false
	};
	window.zpRefItems = [];


    //
    // SEARCH AUTOCOMPLETE
    //

    jQuery("input#zp-ZotpressMetaBox-Search-Input")
        .bind( "keydown", function( event )
        {
            // Don't navigate away from the field on tab when selecting an item
            // if ( event.keyCode === jQuery.ui.keyCode.TAB &&
            //         jQuery( this ).data( "autocomplete" ).menu.active ) {
            //     event.preventDefault();
            // }

            // Don't submit the form when pressing enter
            if ( event.keyCode === 13 )
                event.preventDefault();
        })
        .bind( "focus", function( event )
        {
            // Set the account, in case it's changed
            jQuery(this).autocomplete( 'option', 'source', zpWidgetMetabox.ajaxurl + "?action=zpWidgetMetabox-submit&api_user_id=" + jQuery("#zp-ZotpressMetaBox-Acccount-Select").val() );

            // Hide the shortcode, if shown
            jQuery("#zp-ZotpressMetaBox-Biblio-Generate-Inner").hide('fast');
			jQuery("#zp-ZotpressMetaBox-InText-Generate-Inner").hide('fast');
        })
        .bind( "blur", function( event ) {})
        .autocomplete(
        {
			source: zpWidgetMetabox.ajaxurl + "?action=zpWidgetMetabox-submit&api_user_id=" + jQuery("#zp-ZotpressMetaBox-Acccount-Select").val(),
            minLength: 3,
            focus: function() {
                // prevent value inserted on focus
                return false;
            },
			create: function () {
				jQuery(this).data( "ui-autocomplete" )._renderItem = function( ul, item ) {
					return jQuery( "<li data-api_user_id='"+item.api_user_id+"'>" )
						.append( "<a><strong>" + item.author + "</strong> " + item.label + "</a>" )
						.appendTo( ul );
				}
			},
            open: function () {
				var widget = jQuery(this).data('ui-autocomplete'),
						menu = widget.menu,
						$ul = menu.element;

				menu.element.addClass("zp-autocomplete");
                jQuery(".zp-autocomplete .ui-menu-item:first").addClass("first");

                // Change width of autocomplete dropdown based on input size
                if ( jQuery("#ZotpressMetaBox").parent().attr("id") == "normal-sortables" )
                    menu.element.addClass("zp-autocomplete-wide");
                else
                    menu.element.removeClass("zp-autocomplete-wide");
            },
            select: function( event, ui )
            {
                // Check if item is already in the list
                var check = false;
                jQuery.each(window.zpRefItems, function(index, item) {
                    if (item.itemkey == ui.item.value)
                        check = true;
                });

                if (check === false)
				{
                    // Add to list, if not already there
					window.zpRefItems.push({ "api_user_id": ui.item.api_user_id, "itemkey": ui.item.value, "pages": false});

                    // Add visual indicator
                    var uilabel = (ui.item.label).split(")",1) + ")";

                    var content = "<div class='item' rel='"+ui.item.value+"' data-api_user_id='"+ui.item.api_user_id+"'";
                    if ( ui.item.nickname )
                        content += " data-nickname='"+ui.item.nickname+"'";
                    content += ">";
                    content += "<span class='label'>"+ ui.item.author + ui.item.label +"</span>";
                    content += "<div class='options'>";
                    content += "<label for='zp-Item-"+ui.item.value+"'>"+zpWidgetMetabox.txt_pages+":</label><input id='zp-Item-"+ui.item.value+"' type='text'>";
                    content += "</div>";
                    content += "<div class='item_key'>&rsaquo; "+zpWidgetMetabox.txt_itemkey+": <strong>" + ui.item.value + "</strong></div>";
                    content += "<div class='account'";
                    if ( ui.item.nickname )
                        content += " data-nickname='"+ui.item.nickname+"'";
                    content += "data-api_user_id='"+ui.item.api_user_id+"'>&rsaquo; "+zpWidgetMetabox.txt_account+": <strong>";
                    if ( ui.item.nickname )
                        content += ui.item.nickname+" - ";
                    content += ui.item.api_user_id+"</strong></div>";
                    content += "<div class='delete'>&times;</div>";
                    content += "</div>\n";

                    jQuery("#zp-ZotpressMetaBox-List-Inner")
						.append(content);

                    // Remove text from input
                    jQuery("input#zp-ZotpressMetaBox-Search-Input").val("").focus();
                }
                return false;
            }
        }
    );

    // Citation list item delete button
    jQuery("#zp-ZotpressMetaBox-List div.item .delete")
        .livequery('click', function(event)
        {
            var $parent = jQuery(this).parent();

            // Make sure toggle is closed
            if (jQuery(".toggle", $parent).hasClass("active")) {
                jQuery(this).toggleClass("active");
                jQuery(".options", $parent).slideToggle('fast');
            }

            // Remove item from JSON
            jQuery.each(window.zpRefItems, function(index, item) {
                if (item.itemkey == $parent.attr("rel"))
                    window.zpRefItems.splice(index, 1);
            });

            // Remove visual indicator
            $parent.remove();

            // Hide the shortcode, if shown
            jQuery("#zp-ZotpressMetaBox-Biblio-Generate-Inner").hide('fast');
			jQuery("#zp-ZotpressMetaBox-InText-Generate-Inner").hide('fast');
        });

    // Hide generated shortcodes on citation item list or options change
    // Citation item list change: Bibliography
    jQuery("#zp-ZotpressMetaBox-List div.item")
        .livequery('click', function(event)
        {
            jQuery("#zp-ZotpressMetaBox-Biblio-Generate-Inner").hide('fast');
        }
    );

    // Options change: Bibliography
    jQuery("#zp-ZotpressMetaBox-Biblio-Options")
        .click(function(event)
        {
            jQuery("#zp-ZotpressMetaBox-Biblio-Generate-Inner").hide('fast');
        }
    );

    // Options change: In-text
    jQuery("#zp-ZotpressMetaBox-InText-Options")
        .click(function(event)
        {
            jQuery("#zp-ZotpressMetaBox-InText-Generate-Inner").hide('fast');
        }
    );


    //
    // OPTIONS TOGGLE BUTTON
    //

    // Bibliography
    jQuery("#zp-ZotpressMetaBox-Biblio-Options h4 .toggle")
        .click(function(event)
        {
            jQuery(this).toggleClass("active");
            jQuery(".toggle-button", jQuery(this)).toggleClass("dashicons-arrow-down-alt2 dashicons-arrow-up-alt2");
            jQuery("#zp-ZotpressMetaBox-Biblio-Options-Inner").slideToggle('fast');
        }
    );

    // In-text
    jQuery("#zp-ZotpressMetaBox-InText-Options h4 .toggle")
        .click(function(event)
        {
            jQuery(this).toggleClass("active");
            jQuery(".toggle-button", jQuery(this)).toggleClass("dashicons-arrow-down-alt2 dashicons-arrow-up-alt2");
            jQuery("#zp-ZotpressMetaBox-InText-Options-Inner").slideToggle('fast');
        }
    );

    // In-text Bib
    jQuery("#zp-ZotpressMetaBox-InTextBib-Options h4 .toggle")
        .click(function(event)
        {
            jQuery(this).toggleClass("active");
            jQuery(".toggle-button", jQuery(this)).toggleClass("dashicons-arrow-down-alt2 dashicons-arrow-up-alt2");
            jQuery("#zp-ZotpressMetaBox-InTextBib-Options-Inner").slideToggle('fast');
        }
    );


    //
    // GENERATE SHORTCODE BUTTONS
    //

    // Bibliography
    jQuery("#zp-ZotpressMetaBox-Biblio-Generate-Button")
        .click(function(event)
        {
            if ( ! jQuery(this).parent().parent().parent().parent()
                    .hasClass("zp-ShortcodeBuilder") )
            {
                // Generate and add shortcode string to the textarea
                jQuery("#zp-ZotpressMetaBox-Biblio-Generate-Text")
                    .text( window.zpGenerateShortcodeString("bib") );

                // Reveal shortcode
                jQuery("#zp-ZotpressMetaBox-Biblio-Generate-Inner")
                    .slideDown('fast');
            }
        }
    );

    // In-text
    jQuery("#zp-ZotpressMetaBox-InText-Generate-Button")
        .click( function(event)
        {
            if ( ! jQuery(this).parent().parent().parent().parent()
                    .hasClass("zp-ShortcodeBuilder") )
            {
                // Add it to the textarea
                jQuery("#zp-ZotpressMetaBox-InText-Generate-Text")
                    .text( window.zpGenerateShortcodeString("intext") );

                // Reveal shortcode
                jQuery("#zp-ZotpressMetaBox-InText-Generate-Inner")
                    .slideDown('fast');
            }
        }
    );

    // In-text Bib
    jQuery("#zp-ZotpressMetaBox-InTextBib-Generate-Button")
        .click( function(event)
        {
            if ( ! jQuery(this).parent().parent().parent().parent()
                    .hasClass("zp-ShortcodeBuilder") )
            {
                // Add it to the textarea
                jQuery("#zp-ZotpressMetaBox-InTextBib-Generate-Text")
                    .text( window.zpGenerateShortcodeString("intextbib") );

                // Reveal shortcode
                jQuery("#zp-ZotpressMetaBox-InTextBib-Generate-Inner")
                    .slideDown('fast');
            }
        }
    );


    //
    // CLEAR SHORTCODE BUTTONS
    //

    // Bibliography
    jQuery("#zp-ZotpressMetaBox-Biblio-Clear-Button")
        .click(function(event)
        {
            // Clear zpBiblio
            window.zpBiblio.author = false;
            window.zpBiblio.year = false;
            window.zpBiblio.style = false;
            window.zpBiblio.sortby = false;
            window.zpBiblio.sort = false;
            window.zpBiblio.image = false;
            window.zpBiblio.title = false;
            window.zpBiblio.download = false;
            window.zpBiblio.notes = false;
            window.zpBiblio.zpabstract = false;
            window.zpBiblio.cite = false;
            window.zpBiblio.limit = false;

            // Clear citation list
            jQuery.each(window.zpRefItems, function(index, item) {
                window.zpRefItems.splice(index, 1);
            });

            // Hide options and shortcode
            jQuery("#zp-ZotpressMetaBox-Biblio-Options-Inner").slideUp('fast');
            jQuery("#zp-ZotpressMetaBox-Biblio-Options h4 .toggle").removeClass("active");
            jQuery("#zp-ZotpressMetaBox-Biblio-Generate-Inner").slideUp('fast');

            // Clear inputs
            jQuery("#zp-ZotpressMetaBox-Biblio-Options-Author").val("");
            jQuery("#zp-ZotpressMetaBox-Biblio-Options-Year").val("");
            jQuery("#zp-ZotpressMetaBox-Biblio-Options-Limit").val("");

            // Reset select inputs
            jQuery("#zp-ZotpressMetaBox-Biblio-Options-Style option").removeAttr('checked');
            jQuery("#zp-ZotpressMetaBox-Biblio-Options-Style").val(jQuery("#zp-ZotpressMetaBox-Biblio-Options-Style option[rel='default']").val());

            // Clear radio inputs
            jQuery("#zp-ZotpressMetaBox-Biblio-Options .zp-ZotpressMetaBox-Field-Radio input[type='radio']").prop('checked', false);

            // Remove visual indicators
            jQuery("div#zp-ZotpressMetaBox-List div.item").remove();
        });

    // In-text
    jQuery("#zp-ZotpressMetaBox-InText-Clear-Button")
        .click(function(event)
        {
            // Clear zpInText
            window.zpInText.format = false;
            window.zpInText.brackets = false;
            window.zpInText.etal = false;
            window.zpInText.and = false;
            window.zpInText.separator = false;

            // Clear citation item list
            jQuery.each(window.zpRefItems, function(index, item) {
                window.zpRefItems.splice(index, 1);
            });

            // Hide options and shortcode
            jQuery("#zp-ZotpressMetaBox-InText-Options-Inner").slideUp('fast');
            jQuery("#zp-ZotpressMetaBox-InText-Options h4 .toggle").removeClass("active");
            jQuery("#zp-ZotpressMetaBox-InText-Generate-Inner").slideUp('fast');

            // Reset inputs
            jQuery("#zp-ZotpressMetaBox-InText-Options-Format").val("(%a%, %d%, %p%)");
            jQuery("#zp-ZotpressMetaBox-InText-Options-Brackets").val("default");
            jQuery("#zp-ZotpressMetaBox-InText-Options-Etal").val("default");
            jQuery("#zp-ZotpressMetaBox-InText-Options-And").val("default");
            jQuery("#zp-ZotpressMetaBox-InText-Options-Separator").val("default");

            // Remove visual indicators
            jQuery("div#zp-ZotpressMetaBox-List div.item").remove();
        }
    );

    // In-text Bib
    jQuery("#zp-ZotpressMetaBox-InTextBib-Clear-Button")
        .click(function(event)
        {
            // Clear zpInText
            window.zpInText.style = false;
            window.zpInText.sortby = false;
            window.zpInText.sort = false;
            window.zpInText.image = false;
            window.zpInText.title = false;
            window.zpInText.download = false;
            window.zpInText.zpabstract = false;
            window.zpInText.notes = false;
            window.zpInText.cite = false;

            // Hide options and shortcode
            jQuery("#zp-ZotpressMetaBox-InTextBib-Options-Inner").slideUp('fast');
            jQuery("#zp-ZotpressMetaBox-InTextBib-Options h4 .toggle").removeClass("active");
            jQuery("#zp-ZotpressMetaBox-InTextBib-Generate-Inner").slideUp('fast');

            // Clear select inputs
            jQuery("#zp-ZotpressMetaBox-InTextBib-Options-Style option").removeAttr('checked');
            jQuery("#zp-ZotpressMetaBox-InTextBib-Options-Style").val(jQuery("#zp-ZotpressMetaBox-InTextBib-Options-Style option[rel='default']").val());
            jQuery("#zp-ZotpressMetaBox-InTextBib-Options-SortBy option").removeAttr('checked');
            jQuery("#zp-ZotpressMetaBox-InTextBib-Options-SortBy").val(jQuery("#zp-ZotpressMetaBox-InTextBib-Options-SortBy option[rel='default']").val());

            // Clear radio inputs
            jQuery("#zp-ZotpressMetaBox-InTextBib-Options .zp-ZotpressMetaBox-Field-Radio input[type='radio']").prop('checked', false);
        }
    );


    //
    // FUNCTION: GENERATE SHORTCODE
    //
    // scType = bib, intext, intextbib
    // returns shortcode string or empty string
    //
    window.zpGenerateShortcodeString = function( scType = "bib" )
    {
        var zpBiblioShortcode = "";

        if ( scType == "bib" )
        {
            // Grab the author, year, style, sortby options
            window.zpBiblio.author = jQuery.trim(jQuery("#zp-ZotpressMetaBox-Biblio-Options-Author").val());
            window.zpBiblio.year = jQuery.trim(jQuery("#zp-ZotpressMetaBox-Biblio-Options-Year").val());
            window.zpBiblio.style = jQuery.trim(jQuery("#zp-ZotpressMetaBox-Biblio-Options-Style").val());
            window.zpBiblio.sortby = jQuery.trim(jQuery("#zp-ZotpressMetaBox-Biblio-Options-SortBy").val());
            window.zpBiblio.limit = jQuery.trim(jQuery("#zp-ZotpressMetaBox-Biblio-Options-Limit").val());

            // Grab the sort order option
            if (jQuery("input#zp-ZotpressMetaBox-Biblio-Options-Sort-ASC").is(':checked') === true)
                window.zpBiblio.sort = "ASC";
            if (jQuery("input#zp-ZotpressMetaBox-Biblio-Options-Sort-DESC").is(':checked') === true)
                window.zpBiblio.sort = "DESC";

            // Grab the image option
            if (jQuery("input#zp-ZotpressMetaBox-Biblio-Options-Image-Yes").is(':checked') === true)
                window.zpBiblio.image = "yes";
            else
                window.zpBiblio.image = "";

            // Grab the title option
            if (jQuery("input#zp-ZotpressMetaBox-Biblio-Options-Title-Yes").is(':checked') === true)
                window.zpBiblio.title = "yes";
            else
                window.zpBiblio.title = "";

            // Grab the download option
            if (jQuery("input#zp-ZotpressMetaBox-Biblio-Options-Download-Yes").is(':checked') === true)
                window.zpBiblio.download = "yes";
            else
                window.zpBiblio.download = "";

            // Grab the abstract option
            if (jQuery("input#zp-ZotpressMetaBox-Biblio-Options-Abstract-Yes").is(':checked') === true)
                window.zpBiblio.zpabstract = "yes";
            else
                window.zpBiblio.zpabstract = "";

            // Grab the notes option
            if (jQuery("input#zp-ZotpressMetaBox-Biblio-Options-Notes-Yes").is(':checked') === true)
                window.zpBiblio.notes = "yes";
            else
                window.zpBiblio.notes = "";

            // Grab the cite option
            if (jQuery("input#zp-ZotpressMetaBox-Biblio-Options-Cite-Yes").is(':checked') === true)
                window.zpBiblio.cite = "yes";
            else
                window.zpBiblio.cite = "";

            // Generate bibliography shortcode
            zpBiblioShortcode = "[zotpress";

            // Determine if single account or multiple
            if ( window.zpRefItems.length > 0 )
            {
                var tempItems = "";
                jQuery.each(window.zpRefItems, function(index, item) {
                    if ( index != "0") tempItems = tempItems + ","; // comma separator
                    tempItems = tempItems + "{" + item.api_user_id + ":" + item.itemkey + "}";
                });
                zpBiblioShortcode += " items=\"" + tempItems + "\"";
            }

            if (window.zpBiblio.author != "") zpBiblioShortcode += " author=\"" + window.zpBiblio.author + "\"";
            if (window.zpBiblio.year != "") zpBiblioShortcode += " year=\"" + window.zpBiblio.year + "\"";
            if (window.zpBiblio.style != "") zpBiblioShortcode += " style=\"" + window.zpBiblio.style + "\"";
            if (window.zpBiblio.sortby != "" && window.zpBiblio.sortby != "default") zpBiblioShortcode += " sortby=\"" + window.zpBiblio.sortby + "\"";
            if (window.zpBiblio.sort != "") zpBiblioShortcode += " sort=\"" + window.zpBiblio.sort + "\"";
            if (window.zpBiblio.image != "") zpBiblioShortcode += " showimage=\"" + window.zpBiblio.image + "\"";
            if (window.zpBiblio.download != "") zpBiblioShortcode += " download=\"" + window.zpBiblio.download + "\"";
            if (window.zpBiblio.zpabstract != "") zpBiblioShortcode += " abstract=\"" + window.zpBiblio.zpabstract + "\"";
            if (window.zpBiblio.notes != "") zpBiblioShortcode += " notes=\"" + window.zpBiblio.notes + "\"";
            if (window.zpBiblio.cite != "") zpBiblioShortcode += " cite=\"" + window.zpBiblio.cite + "\"";
            if (window.zpBiblio.title != "") zpBiblioShortcode += " title=\"" + window.zpBiblio.title + "\"";
            if (window.zpBiblio.limit != "") zpBiblioShortcode += " limit=\"" + window.zpBiblio.limit + "\"";

            zpBiblioShortcode += "]";
        }

        else if ( scType == "intext" )
        {
            // Update page parameters for all citations
            jQuery("#zp-ZotpressMetaBox-List .item").each( function(vindex, vitem)
            {
                if ( jQuery.trim( jQuery("input", vitem).val() ).length > 0 )
                {
                    jQuery.each(window.zpRefItems, function(index, item)
                    {
                        if (item.itemkey == jQuery(vitem).attr("rel")) {
                            item.pages = jQuery.trim(jQuery("input", vitem).val());
                        }
                    });
                }
            });

            // Grab the format option
            window.zpInText.format = jQuery.trim(jQuery("#zp-ZotpressMetaBox-InText-Options-Format").val());

            // Grab the brackets option
            window.zpInText.brackets = jQuery.trim(jQuery("#zp-ZotpressMetaBox-InText-Options-Brackets").val());

            // Grab the et al option
            window.zpInText.etal = jQuery.trim(jQuery("#zp-ZotpressMetaBox-InText-Options-Etal").val());

            // Grab the and option
            window.zpInText.and = jQuery.trim(jQuery("#zp-ZotpressMetaBox-InText-Options-And").val());

            // Grab the separator option
            window.zpInText.separator = jQuery.trim(jQuery("#zp-ZotpressMetaBox-InText-Options-Separator").val());

            // Generate in-text shortcode
            zpBiblioShortcode = "[zotpressInText";

            if ( window.zpRefItems.length > 0 )
            {
                zpBiblioShortcode += " item=\"";
                jQuery.each(window.zpRefItems, function(index, item)
                {
                    zpBiblioShortcode += "{" + item.api_user_id + ":" + item.itemkey;
                    if (item.pages !== false) zpBiblioShortcode += "," + item.pages;
                    zpBiblioShortcode += "},";
                });
                zpBiblioShortcode = zpBiblioShortcode.substring(0, zpBiblioShortcode.length - 1) + "\""; // get rid of last comma
            }

            // FIXME: Is this necessary? For backwards compatibility?
            // if (jQuery("#zp-ZotpressMetaBox-Account").length > 0)
                // zpBiblioShortcode += " userid=\"" + jQuery("#zp-ZotpressMetaBox-Account").attr("rel") + "\"";

            if (window.zpInText.format != "" && window.zpInText.format != "(%a%, %d%, %p%)")
                zpBiblioShortcode += " format=\"" + window.zpInText.format + "\"";

			if (window.zpInText.brackets != "" && window.zpInText.brackets != "default")
				zpBiblioShortcode += " brackets=\"" + window.zpInText.brackets + "\"";

			if (window.zpInText.etal != "" && window.zpInText.etal != "default")
				zpBiblioShortcode += " etal=\"" + window.zpInText.etal + "\"";

			if (window.zpInText.and != "" && window.zpInText.and != "default")
				zpBiblioShortcode += " and=\"" + window.zpInText.and + "\"";

			if (window.zpInText.separator != "" && window.zpInText.separator != "default")
				zpBiblioShortcode += " separator=\"" + window.zpInText.separator + "\"";

            zpBiblioShortcode += "]";

        }

        else if ( scType == "intextbib" )
        {
            // Grab the style option
            window.zpInTextBib.style = jQuery.trim(jQuery("#zp-ZotpressMetaBox-InTextBib-Options-Style").val());

            // Grab the sortby option
            window.zpInTextBib.sortby = jQuery.trim(jQuery("#zp-ZotpressMetaBox-InTextBib-Options-SortBy").val());

            // Grab the sort order option
            if (jQuery("input#zp-ZotpressMetaBox-InTextBib-Options-Sort-ASC").is(':checked') === true)
                window.zpInTextBib.sort = "ASC";
            if (jQuery("input#zp-ZotpressMetaBox-InTextBib-Options-Sort-DESC").is(':checked') === true)
                window.zpInTextBib.sort = "DESC";

            // Grab the image option
            if (jQuery("input#zp-ZotpressMetaBox-InTextBib-Options-Image-Yes").is(':checked') === true)
                window.zpInTextBib.image = "yes";
            else
                window.zpInTextBib.image = "";

            // Grab the title option
            if (jQuery("input#zp-ZotpressMetaBox-InTextBib-Options-Title-Yes").is(':checked') === true)
                window.zpInTextBib.title = "yes";
            else
                window.zpInTextBib.title = "";

            // Grab the download option
            if (jQuery("input#zp-ZotpressMetaBox-InTextBib-Options-Download-Yes").is(':checked') === true)
                window.zpInTextBib.download = "yes";
            else
                window.zpInTextBib.download = "";

            // Grab the abstract option
            if (jQuery("input#zp-ZotpressMetaBox-InTextBib-Options-Abstract-Yes").is(':checked') === true)
                window.zpInTextBib.zpabstract = "yes";
            else
                window.zpInTextBib.zpabstract = "";

            // Grab the notes option
            if (jQuery("input#zp-ZotpressMetaBox-InTextBib-Options-Notes-Yes").is(':checked') === true)
                window.zpInTextBib.notes = "yes";
            else
                window.zpInTextBib.notes = "";

            // Grab the cite option
            if (jQuery("input#zp-ZotpressMetaBox-InTextBib-Options-Cite-Yes").is(':checked') === true)
                window.zpInTextBib.cite = "yes";
            else
                window.zpInTextBib.cite = "";

            // Generate in-text bibliography shortcode
            zpBiblioShortcode = "[zotpressInTextBib";

            if (window.zpInTextBib.style != "") zpBiblioShortcode += " style=\"" + window.zpInTextBib.style + "\"";
            if (window.zpInTextBib.sortby != "" && window.zpInTextBib.sortby != "default") zpBiblioShortcode += " sortby=\"" + window.zpInTextBib.sortby + "\"";
            if (window.zpInTextBib.sort != "") zpBiblioShortcode += " sort=\"" + window.zpInTextBib.sort + "\"";
            if (window.zpInTextBib.image != "") zpBiblioShortcode += " showimage=\"" + window.zpInTextBib.image + "\"";
            if (window.zpInTextBib.title != "") zpBiblioShortcode += " title=\"" + window.zpInTextBib.title + "\"";
            if (window.zpInTextBib.download != "") zpBiblioShortcode += " download=\"" + window.zpInTextBib.download + "\"";
            if (window.zpInTextBib.zpabstract != "") zpBiblioShortcode += " abstract=\"" + window.zpInTextBib.zpabstract + "\"";
            if (window.zpInTextBib.notes != "") zpBiblioShortcode += " notes=\"" + window.zpInTextBib.notes + "\"";
            if (window.zpInTextBib.cite != "") zpBiblioShortcode += " cite=\"" + window.zpInTextBib.cite + "\"";

            zpBiblioShortcode += "]";
        }

        return zpBiblioShortcode;
    }

});
