(function($){
	$.fn.canopen = function() {
		$( this ).addClass( "canopen ui-accordion-header ui-helper-reset ui-state-default ui-corner-top ui-corner-bottom" )
		.prepend( '<span class="ui-icon ui-icon-triangle-1-e"></span>' )
		.on(
			'keypress click',
			function(e) {
				if (e.type != 'click' && e.which != 13) {
					return true;
				}

				$target = $( e.target );

				if ($target.hasClass( "ui-accordion-header-active" )) {
					$target
					.toggleClass( "ui-accordion-header-active ui-state-active ui-state-default" )
					.find( "> .ui-icon" ).toggleClass( "ui-icon-triangle-1-e ui-icon-triangle-1-s" ).end()
					.next().slideToggle( 400,function () { $target.toggleClass( "ui-corner-bottom" )} );
				} else {
					$target
					.toggleClass( "ui-accordion-header-active ui-state-active ui-state-default ui-corner-bottom" )
					.find( "> .ui-icon" ).toggleClass( "ui-icon-triangle-1-e ui-icon-triangle-1-s" ).end()
					.next().slideToggle();
				}

				return false;
			}
		)
		.next()
		.addClass( "ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom" )
		.hide();
	};

	$.fn.cannotopen = function() {
		$( this ).addClass( "cannotopen ui-accordion-header ui-helper-reset ui-state-default ui-corner-top ui-corner-bottom" )
		.hover( function() { $( this ).toggleClass( "ui-state-hover" ); } )
		.next()
		.addClass( "ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom" )
		.hide();
	};

	$.fn.opensection = function() {
		$( this )
		.toggleClass( "ui-accordion-header-active ui-state-active ui-state-default ui-corner-bottom" )
		.find( "> .ui-icon" ).toggleClass( "ui-icon-triangle-1-e ui-icon-triangle-1-s" ).end()
		.next().slideToggle();
		return false;
	}
}(jQuery))

document.webwork_scaffold_init = function( el ) {
	jQuery( el ).find( '.section-div h3' ).canopen();
}
