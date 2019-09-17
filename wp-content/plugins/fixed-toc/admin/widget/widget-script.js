(function($) {
	// Toggle fields
	(function() {
		// Set data
		var data = {

			contents_header_font_family: {
				contents_header_customize_font_family: { type: 'select', val: 'customize', increase: 1 }
			},

			contents_list_font_family: {
				contents_list_customize_font_family: { type: 'select', val: 'customize', increase: 1 }
			},

			contents_list_nested: {
				contents_list_strong_1st: { type: 'check', val: true, increase: 1 },
				contents_list_colexp: { type: 'check', val: true, increase: 1 },
				contents_list_sub_icon: { type: 'check', val: true, increase: 1/2 },
				contents_list_colexp_init_state: { type: 'check', val: true, increase: 1/4 },
				contents_list_accordion: { type: 'check', val: true, increase: 0.3334 },
			},

			contents_list_colexp: {
				contents_list_sub_icon: { type: 'check', val: true, increase: 1/2 },
				contents_list_colexp_init_state: { type: 'check', val: true, increase: 1/4 },
				contents_list_accordion: { type: 'check', val: true, increase: 0.3334 }
			},

			contents_list_sub_icon: {
				contents_list_accordion: { type: 'check', val: true, increase: 0.3334 },
				contents_list_colexp_init_state: { type: 'check', val: true, increase: 1/4 },
			},

			contents_list_accordion: {
				contents_list_colexp_init_state: { type: 'check', val: false, increase: 1/4 },
			},

		}; // End data

		// Detect input value
		var detectInput = {
			isIncrease: function( type, $trigger, val ) {
				var result = false;
				switch ( type ) {
					case 'check' : {
						result = this.check( $trigger, val );
						break;
					}

					case 'multi_check' : {
						result = this.multiCheck( $trigger, val );
						break;
					}

					case 'select' : {
						result = this.select( $trigger, val );
						break;
					}
				}
				return result;
			},

			check: function( $trigger, val ) {
				var checked = false;
				if ( $trigger.is( ':checked' ) ) {
					checked = true;
				}
				return val == checked;
			},

			multiCheck: function( $trigger, val ) {
				var checked = false;
				$trigger.each( function() {
					if ( $( this ).is( ':checked' ) ) {
						checked = ( -1 !== val.indexOf( $( this ).val() ) );
					}
				} );
				return checked;
			},

			select: function( $trigger, val ) {
				if ( val == $trigger.val() ) {
					return true;
				} else {
					return false;
				}
			}
		}; // End detect

		// Control
		var control = {
			fields: {},

			toggleFields: function( index ) {
				this.setFields( index );
				$.each( data, function( k, v ) {
					var $trigger = control.getTrigger( k, index );
					control.toggle( v, index );

					$trigger.change( function() {
						control.setFields( index );
						control.toggle( v, index );
					} );
				} );
			},

			toggle: function( targets, index ) {
				$.each( this.fields, function( k, v ) {
					var $target = control.getTarget( k, index );
					if ( 1 <= control.fields[ k ] ) {
						$target.show( 200 );
					} else {
						$target.hide( 200 );
					}
				});
			},

			setFields: function( index ) {
				this.fields = {};
				$.each( data, function( triggerName, targets ) {
					$trigger = control.getTrigger( triggerName, index );
					$.each( targets, function( targetName, v ) {
						var visibility = 0;
						if ( undefined !== control.fields[ targetName ] ) {
							visibility = control.fields[ targetName ];
						}

						if ( detectInput.isIncrease( v.type, $trigger, v.val ) ) {
							visibility = visibility + parseFloat( v.increase );
						} else {
							visibility = 0;
						}

						control.fields[ targetName ] = visibility;
					} );
				} );
			},

			getTrigger: function( triggerName, index ) {
				return $( '#div-widget-fixedtoc-' + index + '-' + triggerName ).find( 'input:not([type="hidden"]), textarea, select' );
			},

			getTarget: function( targetName, index ) {
				return $( '#div-widget-fixedtoc-' + index + '-' + targetName );
			}

		}; // End control
		
		function initToggleFields( widget ) {
			if ( ! widget.length ) {
				return;
			}
			
			var id = widget.attr( 'id' );
			var match = id.match( /fixedtoc\-(\d)$/i );
			if ( match ) {
				var index = match[1];
				control.toggleFields( index );
			}
		}
		
		function onFormUpdate( event, widget ) {
			initToggleFields( widget );
		}

		$( document ).on( 'widget-added widget-updated', onFormUpdate );

		$( document ).ready( function() {
			$( '#widgets-right .widget' ).each( function () {
				initToggleFields( $( this ) );
			});
		} );
				
	})(); // End toggle fields.
	
	
	// Color Picker
	(function() {
		function initColorPicker( widget ) {
			widget.find( '.fixedtoc-color-field' ).wpColorPicker( {
				change: _.throttle( function() { // For Customizer
					$(this).trigger( 'change' );
				} )
			});
		}

		function onFormUpdate( event, widget ) {
			initColorPicker( widget );
		}

		$( document ).on( 'widget-added widget-updated', onFormUpdate );

		$( document ).ready( function() {
			$( '#widgets-right .widget:has(.fixedtoc-color-field)' ).each( function () {
				initColorPicker( $( this ) );
			} );
		} );		
	})();
	
})( jQuery );