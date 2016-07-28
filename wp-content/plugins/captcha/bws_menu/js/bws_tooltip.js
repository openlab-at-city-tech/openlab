/**
 * BWS tooltip function
 *
 */
(function($) {
	$(document).ready( function() {
		jQuery.bwsTooltip = function( pointer_options ) {
			var pointer_buttons = pointer_options['buttons'];
			/* extend pointer options - add close button */
			pointer_options = $.extend( pointer_options, {
				buttons: function(event, t) {
					var button;
					/* check and add dismiss-type buttons */
					for ( var but in pointer_buttons ) {
						if ( typeof pointer_buttons[ but ]['type'] != 'undefined' && pointer_buttons[ but ]['type'] == 'dismiss' && typeof pointer_buttons[ but ]['text'] != 'undefined' && pointer_buttons[ but ]['text'] != '' ) {
							button += '<a style="margin:0px 5px 2px;" class="button-secondary">' + pointer_buttons[ but ]['text'] + '</a>';
						}
					}
					button = jQuery( button );
					button.bind('click.pointer', function () {
						t.element.pointer('close');
					});
					return button;
				},
				/* add ajax dismiss functionality */
				close : $.proxy(function () {
					if ( pointer_options['actions']['onload'] == true ) {
						$.post( ajaxurl, this );
					}
				}, {
					pointer: pointer_options['tooltip_id'],
					action: 'dismiss-wp-pointer'
				})
			});
			/* function to display pointer */
			function displayPointer( cssSelector ) {
				cssSelector.pointer( pointer_options ).pointer({
					pointerClass: 'wp-pointer ' + pointer_options["tooltip_id"],
					content: pointer_options['content'],
					position: {
						edge: pointer_options['position']['edge'],
						align: pointer_options['position']['align'],
					},
				}).pointer('open');
				/* display buttons that are not type of dismiss */
				for ( var but in pointer_buttons ) {
					if ( typeof pointer_buttons[ but ]['type'] != 'undefined' && pointer_buttons[ but ]['type'] != 'dismiss' && typeof pointer_buttons[ but ]['text'] != 'undefined' && pointer_buttons[ but ]['text'] != '' ) {
						$( '.' + pointer_options['tooltip_id'] + ' .button-secondary').first().before( '<a class="button-primary" style="margin-right: 5px;" ' +
						( ( pointer_buttons[ but ]['type'] == 'link' && typeof pointer_buttons[ but ]['link'] != 'undefined' && pointer_buttons[ but ]['link'] != '') ? 'target="_blank" href="' + pointer_buttons[ but ]['link'] + '"' : '' )
						+ '>' + pointer_buttons[ but ]['text'] + '</a>' );
					};
				}
				/* adjust position of pointer */
				topPos = parseInt( $( "." + pointer_options["tooltip_id"] ).css("top") ) + parseInt( pointer_options['position']['pos-top'] );
				leftPos = parseInt( $( "." + pointer_options["tooltip_id"] ).css("left") ) + parseInt( pointer_options['position']['pos-left'] );
				if ( pointer_options['position']['align'] == 'left' ) {
					leftPos += cssSelector.outerWidth()/2;
				};
				$( "." + pointer_options["tooltip_id"] ).css({ "top": topPos + "px", "left": leftPos + "px" });
				/* adjust z-index if need */
				pointerZindex = parseInt( $( "." + pointer_options["tooltip_id"] ).css("z-index") );
				if ( pointerZindex != pointer_options['position']['zindex'] ) {
					$( "." + pointer_options["tooltip_id"] ).css({ "z-index": pointer_options['position']['zindex'] });
				}
			}

			/* display pointer for the first time */
			if ( pointer_options['actions']['onload'] ) {
				if ( pointer_options['set_timeout'] > 0 ) {
					var settime = parseInt( pointer_options['set_timeout'] );					
					setTimeout( function() {
						displayPointer( $( pointer_options['css_selector'] ) );
					}, settime );	
				} else {
					displayPointer( $( pointer_options['css_selector'] ) );
				}
			}
						
			/* display pointer when clicked on selector */
			if ( pointer_options['actions']['click'] ) {
				$( pointer_options['css_selector'] ).click( function () {
					displayPointer( $( this ) );
				});
			}
		};
	})
})(jQuery);