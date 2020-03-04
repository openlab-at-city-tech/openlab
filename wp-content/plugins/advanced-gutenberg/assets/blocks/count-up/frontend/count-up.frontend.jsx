import $ from 'jquery'
import counterUp from 'counterup2'
require( 'waypoints/lib/noframework.waypoints.js' )

$(document).ready(function() {
    const els = document.querySelectorAll( '.advgb-counter-number' );
    if(els.length > 0) {
        [...els].forEach(el => {
            new Waypoint( {
                element: el,
                handler: function() {
                    counterUp( el, {
                        duration: 1200,
                        delay: 10,
                    } );
                    this.destroy()
                },
                offset: 'bottom-in-view',
            } )
        })

    }
})

