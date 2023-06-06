jQuery( function(){

    //disable enter on search input
    jQuery('#trp-add-ons-search-input').on('keypress', function(event) {
        if (event.keyCode == 13) {
            event.preventDefault();
        }
    });

    jQuery('#trp-add-ons-search-input').on( 'keyup', function(e){

        var search = jQuery(this).val().toUpperCase();

        //hide individual add-ons
        jQuery('tbody tr').each( function(){
            addonName = jQuery( '.trp-add-ons-name', jQuery(this) ).text();
            if (addonName.toUpperCase().indexOf(search) > -1) {
                jQuery(this).show();
            } else {
                jQuery(this).hide();
            }
        });


        //hide the whole table as well when there are no more add-ons in it
        jQuery( 'tbody' ).each( function(){
            hideTable = true;
            jQuery( 'tr', jQuery( this ) ).each( function(){
                if( jQuery(this).css('display') != "none" ) {
                    hideTable = false;
                }
            });

            if( hideTable )
                jQuery( this ).closest( '.trp-add-ons-section' ).hide();
            else
                jQuery( this ).closest( '.trp-add-ons-section' ).show();

        })
    } );

    //disabled buttons prevent click
    jQuery( '.trp-add-ons-section .button[disabled]' ).on( 'click', function(e){
        e.preventDefault();

        //add a tooltip
        pointer_content = '<h3>'+ trp_add_ons_pointer.tooltip_header +'</h3>';
        pointer_content += '<p>'+ trp_add_ons_pointer.tooltip_content +'</p>';

        jQuery( this ).pointer({
            content: pointer_content,
            position: { 'edge': 'right', 'align' : 'middle' },
            close: function() {
                // This function is fired when you click the close button
            }
        }).pointer('open');

    });

})