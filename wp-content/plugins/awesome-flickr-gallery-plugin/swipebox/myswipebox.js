;( function( $ ) {

    $( '.swipebox' ).swipebox( {
        useCSS : true, // false will force the use of jQuery for animations
        useSVG : true, // false to force the use of png for buttons
        initialIndexOnArray : 0, // which image index to init when a array is passed
        hideCloseButtonOnMobile : false, // true will hide the close button on mobile devices
        hideBarsDelay : 0, // delay before hiding bars on desktop
        loopAtEnd: false // true will return to the first image after the last image is reached
    } );

} )( jQuery );
