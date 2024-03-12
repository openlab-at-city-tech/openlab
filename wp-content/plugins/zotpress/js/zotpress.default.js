jQuery(document).ready(function()
{
    ////////////////////////////////////////
	//							          //
	//   DEFAULT FRONT-END INTERACTIONS   //
	//							          //
	////////////////////////////////////////

	//
    //   SCROLL TO BIB ENTRY
    //   For in-text citations and notes
    //

    jQuery(".zp-List").on( "click", ".zp-Notes-Reference a", zp_scroll_to );
    jQuery("body").on( "click", "a.zp-ZotpressInText", zp_scroll_to );

    function zp_scroll_to()
    {
        // Get link object
        var $this = jQuery(this);
        var thisHref = $this.attr("href");

        // Adjust scroll to position based on WP admin bar
        var adminBarShowing = 0;
        if ( jQuery("#wpadminbar").length > 0 )
            adminBarShowing = jQuery("#wpadminbar").height();

        // Animate the scroll and highlight
        jQuery(document.body).animate({
            scrollTop: jQuery(thisHref).offset().top - adminBarShowing
        }, 800, 'swing', function() {
            // Highlight fadein
            jQuery(thisHref).effect("highlight", { color: "#C5EFF7", easing: "easeInOutQuad" }, 1200);
        });
    }

});
