jQuery(document).ready( function()
{


	/*

		NAVIGATION STYLES

	*/

    jQuery("div#zp-Zotpress div#zp-Zotpress-Navigation a.nav-item").click( function() {
        jQuery(this).addClass("active");
    });



    /*

        DISMISS ADMIN NOTIFICATIONS

    */

    jQuery(".Zotpress_update_notice .notice-dismiss.text").click( function()
    {
        jQuery(".Zotpress_update_notice").slideUp("fast");
    });

	/*

		COPYING ITEM KEYS ON CLICK

	*/

	jQuery('.zp-Entry-ID-Text span').click( function() {
		jQuery(this).parent().find('input').show().select();
		jQuery(this).hide();
	});
	jQuery('.zp-Entry-ID-Text input').blur( function() {
		jQuery(this).hide();
		jQuery(this).parent().find('span').show();
	});



	/*

		FILTER CITATIONS

	*/

	// FILTER BY ACCOUNT

	jQuery('div.zp-Browse-Accounts').delegate("select#zp-FilterByAccount", "change", function()
	{
		var id = jQuery(this).val();

		jQuery(this).addClass("loading");
		jQuery(".zp-Browse-Account-Options a").addClass("disabled").unbind("click",
			function (e) {
				e.preventDefault();
				return false;
			}
		);

		window.location = "admin.php?page=Zotpress&api_user_id="+id;
	});


	/*

		CITATION IMAGE HOVER

	*/

	jQuery('div.zp-List').delegate("div.zp-Entry-Image", "mouseenter mouseleave", function () {
		jQuery(this).toggleClass("hover");
	});


    /*

        SETUP PAGE "COMPLETE" BUTTON

    */

    jQuery("input#zp-Zotpress-Setup-Options-Complete").click(function()
    {
		window.parent.location = "admin.php?page=Zotpress&accounts=true";

        return false;
    });



    /*

        SYNC ACCOUNT WITH ZOTPRESS

    */

    jQuery('#zp-Connect').click(function ()
    {

        // Disable all the text fields
        jQuery('input[name!=update], textarea, select').attr('disabled','true');

        // Show the loading sign
        jQuery('.zp-Errors').hide();
        jQuery('.zp-Success').hide();
        jQuery('.zp-Loading').show();

		jQuery.ajax(
		{
			url: zpAccountsAJAX.ajaxurl,
			data: {
				'action': 'zpAccountsViaAJAX',
				'action_type': 'add_account',
				'account_type': jQuery('select[name=account_type] option:selected').val(),
				'api_user_id': jQuery('input[name=api_user_id]').val(),
				'public_key': jQuery('input[name=public_key]').val(),
				'nickname': escape(jQuery('input[name=nickname]').val()),
				'zpAccountsAJAX_nonce': zpAccountsAJAX.zpAccountsAJAX_nonce
			},
			xhrFields: {
				withCredentials: true
			},
			success: function(xml)
			{
				var $result = jQuery('result', xml).attr('success');

				if ($result == "true")
				{
					jQuery('div.zp-Errors').hide();
					jQuery('.zp-Loading').hide();
					jQuery('div.zp-Success').html("<p><strong>"+zpAccountsAJAX.txt_success+"!</strong> "+zpAccountsAJAX.txt_accvalid+"</p>\n");

					jQuery('div.zp-Success').show();

					// SETUP
					if (jQuery("div#zp-Setup").length > 0)
					{
						jQuery.doTimeout(1000,function() {
							window.parent.location = "admin.php?page=Zotpress&setup=true&setupstep=two";
						});
					}

					// REGULAR
					else
					{
						jQuery.doTimeout(1000,function()
						{
							jQuery('div#zp-AddAccount').slideUp("fast");
							jQuery('form#zp-Add')[0].reset();
							jQuery('input[name!=update], textarea, select').removeAttr('disabled');
							jQuery('div.zp-Success').hide();

							DisplayAccounts();
						});
					}
				}
				else // Show errors
				{
					jQuery('input, textarea, select').removeAttr('disabled');
					jQuery('div.zp-Errors').html("<p><strong>"+zpAccountsAJAX.txt_oops+"</strong> "+jQuery('errors', xml).text()+"</p>\n");
					jQuery('div.zp-Errors').show();
					jQuery('.zp-Loading').hide();
				}
			},
			error: function(errorThrown)
			{
				console.log(errorThrown);
			}
		});

        return false;
    });



    /*

        REMOVE ACCOUNT

    */

    jQuery('#zp-Accounts').delegate("a.delete", "click", function ()
	{
        $this = jQuery(this);
        $thisProject = $this.parent().parent();

        var confirmDelete = confirm(zpAccountsAJAX.txt_sureremove);

        if (confirmDelete==true)
        {
			jQuery.ajax(
			{
				url: zpAccountsAJAX.ajaxurl,
				data: {
					'action': 'zpAccountsViaAJAX',
					'action_type': 'delete_account',
					'api_user_id': $this.attr("href").replace("#", ""),
					'zpAccountsAJAX_nonce': zpAccountsAJAX.zpAccountsAJAX_nonce
				},
				xhrFields: {
					withCredentials: true
				},
				success: function(xml)
				{
					if ( jQuery('result', xml).attr('success') == "true" )
					{
						if ( jQuery('result', xml).attr('total_accounts') == 0 )
							window.location = 'admin.php?page=Zotpress';
						else
							window.location = 'admin.php?page=Zotpress&accounts=true';
					}
					else
					{
						alert( "Sorry - couldn't delete that account." );
					}
				},
				error: function(errorThrown)
				{
					console.log(errorThrown);
				}
            });
        }

        return false;
    });



    /*

        CLEAR ACCOUNT CACHE

    */

    jQuery('#zp-Accounts').delegate("a.cache", "click", function ()
	{
        $this = jQuery(this);
        $thisProject = $this.parent().parent();

        var confirmClearCache = confirm(zpAccountsAJAX.txt_surecache);

        if (confirmClearCache==true)
        {
            // Show loading
            $this.removeClass("dashicons-image-rotate")
                .addClass("loading");

            // Clear the cache
			jQuery.ajax(
			{
				url: zpAccountsAJAX.ajaxurl,
				data: {
					'action': 'zpAccountsViaAJAX',
					'action_type': 'clear_cache',
					'api_user_id': $this.attr("href").replace("#", ""),
					'zpAccountsAJAX_nonce': zpAccountsAJAX.zpAccountsAJAX_nonce
				},
				xhrFields: {
					withCredentials: true
				},
                complete: function()
                {
                    // Always remove loading sign
                    $this.removeClass("loading")
                        .addClass("dashicons-image-rotate");
                },
				success: function(xml)
				{
					if ( jQuery('result', xml).attr('success') == "true" )
					{
						// alert( zpAccountsAJAX.txt_cachecleared );
                        jQuery("#zp-ManageAccounts").prepend("<div class='notice notice-success Zotpress_update_notice'><p>"+zpAccountsAJAX.txt_cachecleared+"</p></div>");
                        jQuery("#zp-ManageAccounts .notice").delay(2000).animate({ height: 'toggle', opacity: 'toggle' }, 'fast', function() { jQuery(this).remove(); } )
					}
					else
					{
						alert( "Sorry - couldn't clear the cache for that account." );
					}
				},
				error: function(errorThrown)
				{
					console.log(errorThrown);
				}
            });
        }

        return false;
    });



    /*

        SET ACCOUNT TO DEFAULT

    */

    // Remove set link if there's only one account
    if ( jQuery(".zp-Account-Default").length > 0 )
    {
        if ( jQuery(".zp-IsDefaultAccount").length == 1 )
        {
            jQuery(".zp-Account-Default")
                .click(function(e) { e.preventDefault() })
                .addClass("inactive");
        }
        else // Multiple accounts
        {
            jQuery(".zp-Account-Default").click(function()
        	{
        		var $this = jQuery(this);

        		// Prep for data validation
        		$this.addClass("loading");

        		// Determine account
        		var zpTempType = "button";
        		var zpTempAccount = "";

        		if ( $this.attr("rel") != "undefined" )
        		{
        			zpTempType = "icon";
        			zpTempAccount = $this.attr("rel");
        		}

        		if ( jQuery("select#zp-Zotpress-Options-Account").length > 0 )
        		{
        			zpTempType = "form";
        			zpTempAccount = jQuery("select#zp-Zotpress-Options-Account option:selected").val();
        		}

        		// Prep for data validation
        		if ( zpTempType == "form" )
        		{
        			jQuery(this).attr('disabled','true');
        			jQuery('#zp-Zotpress-Options-Account .zp-Loading').show();
        		}

        		// AJAX
        		jQuery.ajax(
        		{
        			url: zpAccountsAJAX.ajaxurl,
        			data: {
        				'action': 'zpAccountsViaAJAX',
        				'action_type': 'default_account',
        				'api_user_id': zpTempAccount,
        				'zpAccountsAJAX_nonce': zpAccountsAJAX.zpAccountsAJAX_nonce
        			},
        			xhrFields: {
        				withCredentials: true
        			},
        			success: function(xml)
        			{
        				var $result = jQuery('result', xml).attr('success');

        				if ( zpTempType == "form" )
        				{
        					jQuery('#zp-Zotpress-Options-Account .zp-Loading').hide();
        					jQuery('input#zp-Zotpress-Options-Account-Button').removeAttr('disabled');

        					if ($result == "true")
        					{
        						jQuery('#zp-Zotpress-Options-Account div.zp-Errors').hide();
        						jQuery('#zp-Zotpress-Options-Account div.zp-Success').show();

        						jQuery.doTimeout(1000,function() {
        							jQuery('#zp-Zotpress-Options-Account div.zp-Success').hide();
        						});
        					}
        					else // Show errors
        					{
        						jQuery('#zp-Zotpress-Options-Account div.zp-Errors').html("<p>"+jQuery('errors', xml).text()+"</p>\n");
        						jQuery('#zp-Zotpress-Options-Account div.zp-Errors').show();
        					}
        				}

        				else
        				{
        					$this.removeClass("success loading");

        					if ($result == "true")
        					{
        						jQuery(".zp-Account-Default")
                                    .removeClass("dashicons-star-filled")
                                    .addClass("dashicons-star-empty");

                                $this.removeClass("dashicons-star-empty")
                                    .addClass("dashicons-star-filled");

                                if ( $this.hasClass("zp-Account-Default") )
                                    $this.addClass("disabled")
                                        .text( zpAccountsAJAX.txt_default );
        					}
        					else // Show errors
        					{
        						alert(jQuery('errors', xml).text());
        					}
        				}
        			},
        			error: function(errorThrown)
        			{
        				console.log(errorThrown);
        			}
        		});

        		// Cancel default behaviours
        		return false;

        	});
        }
    }





    /*

        SET STYLE

    */

	if ( jQuery("select#zp-Zotpress-Options-Style").length > 0 )
	{
		// Show/hide add style input
		jQuery("#zp-Zotpress-Options-Style").change(function()
		{
			if (this.value === 'new-style')
			{
				jQuery("#zp-Zotpress-Options-Style-New-Container").show();
			}
			else
			{
				jQuery("#zp-Zotpress-Options-Style-New-Container").hide();
				jQuery("#zp-Zotpress-Options-Style-New").val("");
			}
		});


		jQuery("#zp-Zotpress-Options-Style-Button").click(function()
		{
			var $this = jQuery(this);
			var updateStyleList = false;

			// Prep for data validation
			$this.addClass("loading");

			// Determine if using existing or adding new
            // If adding new, also update Zotpress_StyleList option
			var styleOption = jQuery('select#zp-Zotpress-Options-Style').val();
			if ( styleOption == "new-style" )
			{
				styleOption = jQuery("#zp-Zotpress-Options-Style-New").val();
				updateStyleList = true;
			}

			if ( styleOption != "" )
			{
				// Prep for data validation
				jQuery(this).attr('disabled','true');
				jQuery('#zp-Zotpress-Options-Style-Container .zp-Loading').show();

				// AJAX
				jQuery.ajax(
				{
					url: zpAccountsAJAX.ajaxurl,
					data: {
						'action': 'zpAccountsViaAJAX',
						'action_type': 'default_style',
						'style': styleOption,
						'zpAccountsAJAX_nonce': zpAccountsAJAX.zpAccountsAJAX_nonce
					},
					xhrFields: {
						withCredentials: true
					},
					success: function(xml)
					{
						var $result = jQuery('result', xml).attr('success');

						jQuery('input#zp-Zotpress-Options-Style-Button').removeAttr('disabled');
						jQuery('#zp-Zotpress-Options-Style-Container .zp-Loading').hide();

						if ($result == "true")
						{
							jQuery('#zp-Zotpress-Options-Style-Container div.zp-Errors').hide();
							jQuery('#zp-Zotpress-Options-Style-Container div.zp-Success').show();

							jQuery.doTimeout(1000,function()
							{
								jQuery('#zp-Zotpress-Options-Style-Container div.zp-Success').hide();

								if (updateStyleList === true)
								{
									jQuery('#zp-Zotpress-Options-Style').prepend(jQuery("<option/>", {
										value: styleOption,
										text: styleOption,
										selected: "selected"
									}));

									jQuery("#zp-Zotpress-Options-Style-New-Container").hide();
									jQuery("#zp-Zotpress-Options-Style-New").val("");
								}
							});
						}
						else // Show errors
						{
							jQuery('#zp-Zotpress-Options-Style-Container div.zp-Errors').html(jQuery('errors', xml).text()+"\n");
							jQuery('#zp-Zotpress-Options-Style-Container div.zp-Errors').show();
						}
					},
					error: function(errorThrown)
					{
						console.log(errorThrown);
					}
				});
			}
			else // Show errors
			{
				jQuery('#zp-Zotpress-Options-Style-Container div.zp-Errors').html("No style was entered.\n");
				jQuery('#zp-Zotpress-Options-Style-Container div.zp-Errors').show();
			}

			// Cancel default behaviours
			return false;

		});
	}








    /*

        SET REFERENCE WIDGET FOR CPT'S

    */

	jQuery("#zp-Zotpress-Options-CPT-Button").click(function()
	{
		var $this = jQuery(this);

		// Determine if using existing or adding new
        // If adding new, also update Zotpress_StyleList option
		// Get all post types
		var zpTempCPT = "";
		jQuery("input[name='zp-CTP']:checked").each( function() {
			zpTempCPT = zpTempCPT + "," + jQuery(this).val();
		});

		if ( zpTempCPT != "" )
		{
			// Prep for data validation
			jQuery(this).attr('disabled','true');
			jQuery('#zp-Zotpress-Options-CPT .zp-Loading').show();

			// AJAX
			jQuery.ajax(
			{
				url: zpAccountsAJAX.ajaxurl,
				data: {
					'action': 'zpAccountsViaAJAX',
					'action_type': 'ref_widget_cpt',
					'cpt': zpTempCPT,
					'zpAccountsAJAX_nonce': zpAccountsAJAX.zpAccountsAJAX_nonce
				},
				xhrFields: {
					withCredentials: true
				},
				success: function(xml)
				{
					var $result = jQuery('result', xml).attr('success');

					jQuery('#zp-Zotpress-Options-CPT .zp-Loading').hide();
					jQuery('input#zp-Zotpress-Options-CPT-Button').removeAttr('disabled');

					if ($result == "true")
					{
						jQuery('#zp-Zotpress-Options-CPT div.zp-Errors').hide();
						jQuery('#zp-Zotpress-Options-CPT div.zp-Success').show();

						jQuery.doTimeout(1000,function() {
							jQuery('#zp-Zotpress-Options-CPT div.zp-Success').hide();
						});
					}
					else // Show errors
					{
						jQuery('#zp-Zotpress-Options-CPT div.zp-Errors').html("<p>"+jQuery('errors', xml).text()+"</p>\n");
						jQuery('#zp-Zotpress-Options-CPT div.zp-Errors').show();
					}
				},
				error: function(errorThrown)
				{
					console.log(errorThrown);
				}
			});
		}
		else // Show errors
		{
			jQuery('#zp-Zotpress-Options-CPT div.zp-Errors').html("No content type was selected.\n");
			jQuery('#zp-Zotpress-Options-CPT div.zp-Errors').show();
		}

		// Cancel default behaviours
		return false;

	});



    /*

        RESET ZOTPRESS

    */

	jQuery("#zp-Zotpress-Options-Reset-Button").click(function()
	{
		var $this = jQuery(this);

		var confirmDelete = confirm(zpAccountsAJAX.txt_surereset);

		if ( confirmDelete == true )
		{
			// Prep for data validation
			jQuery(this).attr( 'disabled', 'true' );
			jQuery('#zp-Zotpress-Options-Reset .zp-Loading').show();

			// Prep for data validation
			jQuery(this).attr('disabled','true');
			jQuery('#zp-Zotpress-Options-Reset .zp-Loading').show();

			// AJAX
			jQuery.ajax(
			{
				url: zpAccountsAJAX.ajaxurl,
				data: {
					'action': 'zpAccountsViaAJAX',
					'action_type': 'reset',
					'reset': "true",
					'zpAccountsAJAX_nonce': zpAccountsAJAX.zpAccountsAJAX_nonce
				},
				xhrFields: {
					withCredentials: true
				},
				success: function(xml)
				{
					var $result = jQuery('result', xml).attr('success');

					jQuery('#zp-Zotpress-Options-Reset .zp-Loading').hide();
					jQuery('input#zp-Zotpress-Options-Reset-Button').removeAttr('disabled');

					if ($result == "true")
					{
						jQuery('#zp-Zotpress-Options-Reset div.zp-Errors').hide();
						jQuery('#zp-Zotpress-Options-Reset div.zp-Success').show();

						jQuery.doTimeout(1000,function() {
							jQuery('#zp-Zotpress-Options-Reset div.zp-Success').hide();
							window.parent.location = "admin.php?page=Zotpress";
						});
					}
					else // Show errors
					{
						jQuery('#zp-Zotpress-Options-Reset div.zp-Errors').html("<p>"+jQuery('errors', xml).text()+"</p>\n");
						jQuery('#zp-Zotpress-Options-Reset div.zp-Errors').show();
					}
				},
				error: function(errorThrown)
				{
					console.log(errorThrown);
				}
			});
		}

		// Cancel default behaviours
		return false;

	});



	/*

        ADD/UPDATE ITEM IMAGE

    */

	var zp_uploader;

	jQuery(".zp-List").on("click", ".zp-Entry-Image a.upload", function(e)
	{
        e.preventDefault();

		$this = jQuery(this);

        if (zp_uploader)
		{
            zp_uploader.open();
            return;
        }

        zp_uploader = wp.media.frames.file_frame = wp.media(
		{
			title: zpAccountsAJAX.txt_chooseimg,
			button: {
				text: zpAccountsAJAX.txt_chooseimg
			},
			multiple: false
		});

        zp_uploader.on( 'select', function()
		{
            attachment = zp_uploader.state().get('selection').first().toJSON();

			// Save as featured image
			jQuery.ajax(
			{
				url: zpAccountsAJAX.ajaxurl,
				data: {
					'action': 'zpAccountsViaAJAX',
					'action_type': 'add_image',
					'api_user_id': jQuery(".ZP_API_USER_ID").text(),
					'item_key': $this.attr('rel'),
					'image_id': attachment.id,
					'zpAccountsAJAX_nonce': zpAccountsAJAX.zpAccountsAJAX_nonce
				},
				success: function(xml)
				{
					var $result = jQuery('result', xml).attr('success');

					if ( $result == "true" )
					{
                        console.log("zp: Found image to set.");

                        // NOTE: Sometimes WP doesn't provide a thumbnail, just full
                        // Maybe because some images are so small they are thumbnail-sized

                        var thumbURL = "";
                        if ( attachment.sizes.hasOwnProperty("thumbnail") )
                            thumbURL = attachment.sizes.thumbnail.url;
                        else if ( attachment.sizes.hasOwnProperty("full") )
                            thumbURL = attachment.sizes.full.url;

						if ( $this.parent().find(".thumb").length > 0 ) // update existing
                        {
							$this.parent().find(".thumb").attr("src", thumbURL);
						}
						else // set image
                        {
							$this.parent().addClass("hasImage");
							$this.parent().prepend("<img class='thumb' src='"+thumbURL+"' alt='image'>");
						}
                        // Update button text
                        $this.text(zpAccountsAJAX.txt_changeimg);

                        // Add remove button
                        $this.parent().prepend("<a title='"+zpAccountsAJAX.txt_removeimg+"' class='delete' rel='"+$this.attr('rel')+"' href='#'>&times;</a>\n");
					}
					else // Show errors
					{
						alert ("Sorry, featured image couldn't be set.");
					}
				},
				error: function(errorThrown)
				{
					console.log(errorThrown);
				}
			});
        });

        zp_uploader.open();

    });



    /*

        REMOVE ITEM IMAGE

    */

	jQuery(".zp-List").on("click", ".zp-Entry-Image a.delete", function(e)
	{
        e.preventDefault();

		$this = jQuery(this);

		jQuery.ajax(
		{
			url: zpAccountsAJAX.ajaxurl,
			data: {
				'action': 'zpAccountsViaAJAX',
				'action_type': 'remove_image',
				'api_user_id': jQuery(".ZP_API_USER_ID").text(),
				'item_key': $this.attr('rel'),
				'zpAccountsAJAX_nonce': zpAccountsAJAX.zpAccountsAJAX_nonce
			},
			xhrFields: {
				withCredentials: true
			},
			success: function(xml)
			{
				var $result = jQuery('result', xml).attr('success');

				if ( $result == "true" )
				{
					$this.parent().removeClass("hasImage");
					$this.parent().find(".thumb").remove();

                    // Update button text
                    jQuery('.upload', $this.parent()).text(zpAccountsAJAX.txt_setimg);
				}
				else // Show errors
				{
					alert ("Sorry, featured image couldn't be set.");
				}
			},
			error: function(errorThrown)
			{
				console.log(errorThrown);
			}
		});
	});



});
