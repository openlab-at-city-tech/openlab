jQuery(document).ready(function()
{
    
    /****************************************************************************************
     *
     *     ZOTPRESS HELP
     *
     ****************************************************************************************/
    
    if ( jQuery("#zp-Zotero-API").length > 0 )
	{
		jQuery("#zp-Zotero-API").tabs();
        
		jQuery('.zp-Tab-Link').click( function(e)
		{
			e.preventDefault();
            
			jQuery("#zp-Zotero-API").tabs( 
				"option", 
				"active", 
				jQuery( jQuery(this).attr("href"), jQuery("#zp-Zotero-API")).index()-1
			);
            
	        jQuery("html,body").animate({ 
	        	scrollTop: jQuery("#zp-Zotero-API-Hash").offset().top
	        }, 500);
	    });
	}
    
    
    if ( jQuery("input.zp-Zotero-API-Attributes-Search-Input").length > 0 )
	{
        jQuery("input.zp-Zotero-API-Attributes-Search-Input").each( function()
        {
            var $searchInput = jQuery(this);
            var $searchParent = $searchInput.parent().parent().parent();
            
            $searchInput
                .bind( "keydown", function( event )
                {
                    // Don't submit the form when pressing enter
                    if ( event.keyCode === 13 ) {
                        event.preventDefault();
                    }
                    
                    // Otherwise, show loading icon and hide search icon
                    jQuery("#zp-Zotero-API .ui-tabs-panel[aria-hidden=false] .zp-Zotero-API-Attributes-Search-Status .dashicons").hide();
                    jQuery("#zp-Zotero-API .ui-tabs-panel[aria-hidden=false] .zp-Zotero-API-Attributes-Search-Status .zp-Loading").show();
                    
                })
                .bind( "keyup", function( event )
                {
                    // Hide loading and show search
                    jQuery("#zp-Zotero-API .ui-tabs-panel[aria-hidden=false] .zp-Zotero-API-Attributes-Search-Status .zp-Loading").hide();
                    jQuery("#zp-Zotero-API .ui-tabs-panel[aria-hidden=false] .zp-Zotero-API-Attributes-Search-Status .dashicons").show();
                    
                    // Only search if 3+ characters
                    if ( $searchInput.val().length >= 3 )
                    {
                        jQuery(".zp-Zotero-API-Attribute", $searchParent).each( function()
                        {
                            var tempkey = String( jQuery(this).data("keywords") );
                            
                            if ( tempkey.indexOf($searchInput.val()) == -1 )
                                jQuery(this).hide();
                            else
                                jQuery(this).show();
                        });
                    }
                    else // Show everything
                    {
                        jQuery(".zp-Zotero-API-Attribute", $searchParent).show();
                    }
                });
        });
    }
});