(function($) {
	
	$(document).ready( function() {
		var innerEle = $( "#fixedtoc-metabox-inner" );
		var postID = $( '#post_ID' ).attr( 'value' );
		
		// Accordion
		(function() {
			innerEle.accordion({
				heightStyle: "content",
				collapsible: true,
				icons: {
					header: "dashicons dashicons-arrow-down",
					activeHeader: "dashicons dashicons-arrow-up"
				},
				active: parseInt( getCookie( 'fixedtocInnerActiveIndex' + postID) )
			});
			
			innerEle.on( "accordionactivate", function() {
				var active = $( this ).accordion( "option", "active" );
				setCookie( 'fixedtocInnerActiveIndex' + postID, active, 7 );
			} );
		})();
		
		// Toggle form field to disabled
		(function(){
			$( '.ftoc-field-control' ).click(function() {
				var trEle = $( this ).parents('tr');
				trEle.toggleClass('ftoc-disabled');
				var fieldEles = trEle.children('td').find( 'input, select, textarea' );
				
				fieldEles.each(function() {
					if ( false === $( this ).prop( 'disabled' ) ) {
						$( this ).prop( 'disabled', true );
					} else {
						$( this ).prop( 'disabled', false );
					}					
				});
			});
		})();
		
		// Show/hide setting sections
		(function() {
			var onOffEle = $( '#ftoc-onoff-toggle' );
			toggleInner();
			onOffEle.change( toggleInner );
			
			function toggleInner() {
				if ( onOffEle.is( ':checked' ) ) {
					$( "#fixedtoc-metabox-inner, #fixedtoc-document" ).show();
				} else {
					$( "#fixedtoc-metabox-inner, #fixedtoc-document" ).hide();
				}
			}
		})();
		
		// Color picker
		(function() {
			$( '.fixedtoc-color-field' ).wpColorPicker();
		})();
		
		// Toggle fields
		(function() {
			// Set data
			var data = {
				
				location_fixed_position: {
					location_vertical_offset: { type: 'select', val: ['top-left', 'top-right', 'bottom-left', 'bottom-right'], increase: 1 }
				},

				contents_display_in_post: {
					contents_position_in_post:	{ type: 'check', val: true, increase: 1 },
					contents_float_in_post:	{ type: 'check', val: true, increase: 1 },
					contents_width_in_post:	{ type: 'check', val: true, increase: 1/2 },
					contents_height_in_post:	{ type: 'check', val: true, increase: 1 }				
				},

				contents_float_in_post: {
					contents_width_in_post: { type: 'multi_check', val: ['left', 'right'], increase: 1/2 }
				},

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
					if ( Array.isArray( val ) ) {
						for ( var i = 0, len = val.length; i < len; i++  ) {
							if ( $trigger.val() == val[ i ] ) {
								return true;
							}
						}
						return false;
					} else {
						if ( val == $trigger.val() ) {
							return true;
						} else {
							return false;
						}						
					}
				}
			}; // End detect

			// Control
			var control = {
				fields: {},

				toggleFields: function() {
					this.setFields();
					$.each( data, function( k, v ) {
						var $trigger = control.getTrigger( k );
						control.toggle( v );
						$trigger.change( function() {
							control.setFields();
							control.toggle( v );
						} );
					} );
				},

				toggle: function( targets ) {
					$.each( this.fields, function( k, v ) {
						var $target = control.getTarget( k );
						if ( 1 <= control.fields[ k ] ) {
							$target.show( 200 );
						} else {
							$target.hide( 200 );
						}
					});
				},

				setFields: function() {
					this.fields = {};
					$.each( data, function( triggerName, targets ) {
						$trigger = control.getTrigger( triggerName );
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

				getTrigger: function( triggerName ) {
					return $( '#tr_fixedtoc_meta_' + triggerName ).children( 'td' ).find( 'input:not([type="hidden"]), textarea, select' );
				},

				getTarget: function( targetName ) {
					return $( '#tr_fixedtoc_meta_' + targetName );
				}			

			}; // End control

			control.toggleFields();	
			
		})(); // End toggle fields.
		
		// Set Cookie
		function setCookie(cname, cvalue, exdays) {
			var d = new Date();
			d.setTime(d.getTime() + (exdays*24*60*60*1000));
			var expires = "expires="+d.toUTCString();
			document.cookie = cname + "=" + cvalue + "; " + expires;
		}

		// Get Cookie
		function getCookie(cname) {
			var name = cname + "=";
			var ca = document.cookie.split(';');
			for(var i=0; i<ca.length; i++) {
				var c = ca[i];
				while (c.charAt(0)==' ') {
					c = c.substring(1);
				}
				if (c.indexOf(name) === 0) {
					return c.substring(name.length, c.length);
				}
			}
			return false;
		}		
		
	} );
	
})(jQuery);