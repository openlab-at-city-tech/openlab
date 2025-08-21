jQuery(document).ready(function()
{
	///////////////////////////////
	//							 //
	//   ZOTPRESS DOWNLOADS      //
	//							 //
	///////////////////////////////

    if ( jQuery(".zp-Zotpress").length > 0 )
	{
        jQuery(document).on('click', '.zp-getDownloadURL', function(event) {

            // event.preventDefault();
            var $this = jQuery(this);

            let zpDLData = $this.data('zp-dl').split('&');
            
            let zp_dldata = {
                'action': 'zpDLViaAJAX',
                // 'instance_id': $instance.attr("id"),
                'api_user_id': zpDLData[0].split('=')[1],
                'dlkey': zpDLData[1].split('=')[1],
                'content_type': zpDLData[2].split('=')[1],
                'zpDL_nonce': zpDLAJAX.zpDL_nonce
            };

            let zp_dldataParams = jQuery.param(zp_dldata);

            // 7.4: Note that the first opens in a new window ... the second doesn't
            // window.open(zpDLAJAX.ajaxurl+'?'+zp_dldataParams);
            window.location = zpDLAJAX.ajaxurl+'?'+zp_dldataParams;

        });

    } // if ( jQuery(".zp-Zotpress").length > 0 )
});