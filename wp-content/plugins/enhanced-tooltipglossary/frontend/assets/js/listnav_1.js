/*
 * Inside this closure we use $ instead of jQuery in case jQuery is reinitialized again before document.ready()
 */
( function ( $ ) {
    "use strict";

    $( document ).ready( function () {

        if ( window.cmtt_listnav_data !== undefined && window.cmtt_listnav_data.listnav && window.cmtt_listnav_data.list_id ) {
            $( document ).ready( function ( $ ) {
                $( "#" + window.cmtt_listnav_data.list_id ).listnav( window.cmtt_listnav_data.listnav );
            } );
        }

        // Prevent scroll down when spacebar pressed
        document.getElementById( 'glossaryList-nav' ).addEventListener( 'keydown', function ( e ) {
            if ( ( e.keycode || e.which ) === 32 ) {
                e.preventDefault();
            }
        }, false );

        $( '.listNav' ).keydown( function ( e ) {
            if ( e.which === 39 ) { // 'right' arrow pressed
                e.preventDefault();
                $( '.ln-letters a:focus' ).attr( 'tabindex', '-1' ); // restore letter from which clicking to non-tabbable state
                $( '.ln-letters a:focus' ).next().focus().attr( 'tabindex', '0' ); // make receiving letter tabbable
            }

            if ( e.which === 37 ) { // 'left' arrow pressed
                e.preventDefault();
                $( '.ln-letters a:focus' ).attr( 'tabindex', '-1' ); // restore letter from which clicking to non-tabbable state
                $( '.ln-letters a:focus' ).prev().focus().attr( 'tabindex', '0' ); // make receiving letter tabbable
            }

            if ( e.which === 13 || e.which === 32 ) { // space bar pressed
                $( e.target ).click().focus();
            }

            $( '.ln-letters a:first' ).attr( 'tabindex', '0' ); // first letter must always be tabbable

        } );

    } );
}( jQuery ) );