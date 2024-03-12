jQuery(document).ready( function()
{

    jQuery(document).on( 'click', '.Zotpress_update_notice .notice-dismiss', function()
    {
        jQuery.ajax({
            url: zpNoticesAJAX.ajaxurl,
            data: {
                action: 'zpNoticesViaAJAX'
            },
            xhrFields: {
                withCredentials: true
            }
        });
    });

});
