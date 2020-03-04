jQuery(document).ready(function ($) {
    $('.advgb-tabs-wrapper').each(function () {
        var activeTab = $(this).data('tab-active');
        var tabPanel = $(this).find('.advgb-tab-panel');

        var tab = $(this).find('li.advgb-tab:not(".advgb-tab-active")');
        if($(this).prop('id') !== '') {
            tab = $(this).find('li.advgb-tab:not(".ui-state-active")');
        }
        var tabs = $(this).find('.advgb-tab');
        var bodyHeaders = $(this).find('.advgb-tab-body-header');
        var bodyContainers = $(this).find('.advgb-tab-body-container');
        var bgColor = tab.css('background-color');
        var borderColor = tab.css('border-color');
        var borderWidth = tab.css('border-width');
        var borderStyle = tab.css('border-style');
        var borderRadius = tab.css('border-radius');
        var textColor = tab.find('a').css('color');

        if($(this).prop('id') !== '') {
            $( this ).find( ".advgb-tab a:not(.ui-tabs-anchor)" ).unbind( "click" );
            // Render tabs UI
            $( this ).tabs( {
                active: parseInt( activeTab ),
                activate: function ( e, ui ) {
                    var newIdx = ui.newTab.index();
                    bodyHeaders.removeClass( 'header-active' );
                    bodyHeaders.eq( newIdx ).addClass( 'header-active' );
                }
            } );
        } else {

            $( this ).find( ".advgb-tab a:not(.advgb-tabs-anchor)" ).unbind( "click" );

            tabs.on( 'click', function ( event ) {
                event.preventDefault();
                var currentTabActive = $( event.target ).closest( '.advgb-tab' );
                var href = currentTabActive.find( 'a' ).attr( 'href' );

                tabs.removeClass( 'advgb-tab-active' );
                currentTabActive.addClass( 'advgb-tab-active' );
                bodyContainers.find( '.advgb-tab-body' ).hide();
                bodyContainers.find( '.advgb-tab-body[aria-labelledby="' + href.replace( /^#/, "" ) + '"]' ).show();
            } );

            tabs.eq( activeTab ).trigger( 'click' ); // Default
        }

        bodyHeaders.eq(activeTab).addClass('header-active');
        bodyHeaders.css({
            backgroundColor: bgColor,
            color: textColor,
            borderColor: borderColor,
            borderWidth: borderWidth,
            borderStyle: borderStyle,
            borderRadius: borderRadius
        })
    });

    $('.advgb-tab-body-header').click(function () {
        var bodyContainer = $(this).closest('.advgb-tab-body-container');
        var bodyWrapper = $(this).closest('.advgb-tab-body-wrapper');
        var tabsWrapper = $(this).closest('.advgb-tabs-wrapper');
        var tabsPanel = tabsWrapper.find('.advgb-tabs-panel');
        var idx = bodyContainer.index();

        bodyWrapper.find('.advgb-tab-body-header').removeClass('header-active');
        $(this).addClass('header-active');
        tabsPanel.find('.advgb-tab').eq(idx).find('a').trigger('click');
    })
});