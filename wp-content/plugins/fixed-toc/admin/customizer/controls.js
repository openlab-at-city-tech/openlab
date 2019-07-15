/**
 * Script for custom customize control.
 */
(function($) {	
	
	$(document).ready(function() {
		
		// Toggle fields
		(function() {
			// Set data
			var data = {
				
				location_fixed_position: {
					location_vertical_offset: { type: 'select', val: ['top-left', 'top-right', 'bottom-left', 'bottom-right'], increase: 1 }
				},
				
				contents_display_in_post: {
					contents_float_in_post:	{ type: 'check', val: true, increase: 1 },
					contents_position_in_post:	{ type: 'check', val: true, increase: 1 },
					contents_width_in_post:	{ type: 'check', val: true, increase: 1/2 },
					contents_height_in_post:	{ type: 'check', val: true, increase: 1 },
					contents_col_exp_init:	{ type: 'check', val: true, increase: 1 }
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
					return $( '#customize-control-fixedtoc-field-' + triggerName ).find( ':input' );
				},

				getTarget: function( targetName ) {
					return $( '#customize-control-fixedtoc-field-' + targetName );
				}			

			}; // End control

			control.toggleFields();		
		})(); // End toggle fields.
		
	}); // End ready()
	
})( jQuery );