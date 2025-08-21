jQuery(document).ready(function()
{
	///////////////////////////////
	//							 //
	//   ZOTPRESS CITEABLE       //
	//							 //
	///////////////////////////////

    if ( jQuery(".zp-Zotpress").length > 0 )
	{
        jQuery(document).on('click', '.zp-CiteRIS', function(event) {

            // event.preventDefault();
            var $this = jQuery(this);

            let zpCiteData = $this.data('zp-cite').split('&');
            
            let zp_citedata = {
                'action': 'zpCiteViaAJAX',
                // 'instance_id': $instance.attr("id"),
                'api_user_id': zpCiteData[0].split('=')[1],
                'item_key': zpCiteData[1].split('=')[1],
                'zpCite_nonce': zpCiteAJAX.zpCite_nonce
            };

            let zp_citedataParams = jQuery.param(zp_citedata);

            // 7.4: Note that the first opens in a new window ... the second doesn't
            // window.open(zpDLAJAX.ajaxurl+'?'+zp_citedataParams);
            window.location = zpDLAJAX.ajaxurl+'?'+zp_citedataParams;

        });

    } // if ( jQuery(".zp-Zotpress").length > 0 )
});