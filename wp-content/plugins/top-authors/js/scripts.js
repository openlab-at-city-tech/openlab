(function( $ ) {

    function ta_set_dropdowns() {
        $('.widget-liquid-right .top-authors .role-select').SumoSelect({
            placeholder: ta.role_select_placeholder
        });
        $('.widget-liquid-right .top-authors .post-type-select').SumoSelect({
            placeholder: ta.post_type_select_placeholder,
            selectAll: true
        });
    }

    ta_set_dropdowns();

    $( document ).ajaxComplete(function( event, xhr, settings ) {
        if( settings.data.indexOf( 'top-authors' ) > -1 && settings.data.indexOf( 'action=widgets-order' ) === -1 ) {
            ta_set_dropdowns();
        }
    })

    $( document ).on( 'click', '.top-authors .help', function() {
        $(this).parents('p:first').next('.help-content').toggle();
        return false;
    })

    $( document ).on( 'change', '.top-authors .preset-selector', function() {
        if( $(this).val() === 'custom' ) {
            $(this).parents('.top-authors').find('.custom-settings ').show();
        }
        else {
            $(this).parents('.top-authors').find('.custom-settings ').hide();
        }
        return false;
    })

})( jQuery );
