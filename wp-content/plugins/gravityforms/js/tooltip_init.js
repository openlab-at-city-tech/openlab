jQuery( function() {
	gform_initialize_tooltips();
} );

function gform_initialize_tooltips() {
	var $tooltips = jQuery( '.gf_tooltip' );
	if ( ! $tooltips.length ) {
		return;
	}

	$tooltips.tooltip( {
		show: {
			effect: 'fadeIn',
			duration: 200,
			delay: 100,
		},
		position:     {
			my: 'center bottom',
			at: 'center-3 top-11',
		},
		tooltipClass: 'arrow-bottom',
		items: '[aria-label]',
		content: function () {
			return jQuery( this ).attr( 'aria-label' );
		},
		open:         function ( event, ui ) {
			if ( typeof ( event.originalEvent ) === 'undefined' ) {
				return false;
			}

			// set the tooltip offset on reveal based on tip width and offset of trigger to handle dynamic changes in overflow
			setTimeout( function() {
				var leftOffset = ( this.getBoundingClientRect().left - ( ( ui.tooltip[0].offsetWidth / 2 ) - 5 ) ).toFixed(3);
				ui.tooltip.css( 'left', leftOffset + 'px' );
			}.bind( this ), 100 );


			var $id = ui.tooltip.attr( 'id' );
			jQuery( 'div.ui-tooltip' ).not( '#' + $id ).remove();
		},
		close:        function ( event, ui ) {
			ui.tooltip.hover( function () {
					jQuery( this ).stop( true ).fadeTo( 400, 1 );
				},
				function () {
					jQuery( this ).fadeOut( '500', function () {
						jQuery( this ).remove();
					} );
				} );
		}
	} );
}

function gform_system_shows_scrollbars() {
	var parent = document.createElement("div");
	parent.setAttribute("style", "width:30px;height:30px;");
	parent.classList.add('scrollbar-test');

	var child = document.createElement("div");
	child.setAttribute("style", "width:100%;height:40px");
	parent.appendChild(child);
	document.body.appendChild(parent);

	var scrollbarWidth = 30 - parent.firstChild.clientWidth;

	document.body.removeChild(parent);

	return scrollbarWidth ? true : false;
}
