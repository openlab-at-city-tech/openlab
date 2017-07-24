function bws_show_settings_notice() {
	(function($) {
		$( '.updated.fade:not(.bws_visible), .error:not(.bws_visible)' ).css( 'display', 'none' );
		$( '#bws_save_settings_notice' ).css( 'display', 'block' );
	})(jQuery);
}

(function($) {
	$( document ).ready( function() {
		/**
		 * add notice about changing on the settings page 
		 */
		$( '.bws_form input, .bws_form textarea, .bws_form select' ).bind( "change paste select", function() {
			if ( $( this ).attr( 'type' ) != 'submit' && ! $( this ).hasClass( 'bws_no_bind_notice' ) ) {
				bws_show_settings_notice();
			};
		});
		$( '.bws_save_anchor' ).on( "click", function( event ) {
			event.preventDefault();
			$( '.bws_form #bws-submit-button' ).click();
		});

		/* custom code */
		if ( typeof CodeMirror == 'function' ) {
			if ( $( '#bws_newcontent_css' ).length > 0 ) {
				var editor = CodeMirror.fromTextArea( document.getElementById( 'bws_newcontent_css' ), {
					mode: "css",
					theme: "default",
					styleActiveLine: true,
					matchBrackets: true,
					lineNumbers: true,
					addModeClass: 'bws_newcontent_css'
				});
			}		
	
			if ( $( '#bws_newcontent_php' ).length > 0 ) {
				var editor = CodeMirror.fromTextArea( document.getElementById( "bws_newcontent_php" ), {
					mode: 'text/x-php',
					styleActiveLine: true,
					matchBrackets: true,	
					lineNumbers: true,				
				});
				/* disable lines */
				editor.markText( {ch:0,line:0}, {ch:0,line:5}, { readOnly: true, className: 'bws-readonly' } );
			}

			if ( $( '#bws_newcontent_js' ).length > 0 ) {
				var editor = CodeMirror.fromTextArea( document.getElementById( "bws_newcontent_js" ), {
					mode: 'javascript',
					styleActiveLine: true,
					matchBrackets: true,	
					lineNumbers: true,				
				});
			}
		}

		/* banner to settings */
		$( '.bws_banner_to_settings_joint .bws-details' ).addClass( 'hidden' ).removeClass( 'hide-if-js' );	
		$( '.bws_banner_to_settings_joint .bws-more-links' ).on( "click", function( event ) {
			event.preventDefault();
			if ( $( '.bws_banner_to_settings_joint .bws-less' ).hasClass( 'hidden' ) ) {
				$( '.bws_banner_to_settings_joint .bws-less, .bws_banner_to_settings_joint .bws-details' ).removeClass( 'hidden' );
				$( '.bws_banner_to_settings_joint .bws-more' ).addClass( 'hidden' );
			} else {
				$( '.bws_banner_to_settings_joint .bws-less, .bws_banner_to_settings_joint .bws-details' ).addClass( 'hidden' );
				$( '.bws_banner_to_settings_joint .bws-more' ).removeClass( 'hidden' );
			}
		});

		/* help tooltips */
		if ( $( '.bws_help_box' ).length > 0 ) {
			if ( $( 'body' ).hasClass( 'rtl' ) ) {
				var current_position = { my: "right top+15", at: "right bottom" };
			} else {
				var current_position = { my: "left top+15", at: "left bottom" };
			}			
			$( document ).tooltip( {
				items: $( '.bws_help_box' ),
				content: function() {
		        	return $( this ).find( '.bws_hidden_help_text' ).html()
		        },
		        show: null, /* show immediately */
		        tooltipClass: "bws-tooltip-content",
		        position: current_position,
				open: function( event, ui ) {					
					if ( typeof( event.originalEvent ) === 'undefined' ) {
						return false;
					}
					if ( $( event.originalEvent.target ).hasClass( 'bws-auto-width' ) ) {
						ui.tooltip.css( "max-width", "inherit" );
					}
					var $id = $( ui.tooltip ).attr( 'id' );
					/* close any lingering tooltips */
					$( 'div.ui-tooltip' ).not( '#' + $id ).remove();
				},
				close: function( event, ui ) {
					ui.tooltip.hover( function() {
						$( this ).stop( true ).fadeTo( 200, 1 ); 
					},
					function() {
						$( this ).fadeOut( '200', function() {
							$( this ).remove();
						});
					});
				}
		    });
		}

		/**
		 * Handle the styling of the "Settings" tab on the plugin settings page
		 */
		var tabs = $( '#bws_settings_tabs_wrapper' );
		if ( tabs.length ) {
			var current_tab_field = $( 'input[name="bws_active_tab"]' ),
				prevent_tabs_change = false,
				active_tab = current_tab_field.val();
			if ( '' == active_tab ) {
				var active_tab_index = 0;
			} else {
				var active_tab_index = $( '#bws_settings_tabs li[data-slug=' + active_tab + ']' ).index();
			}

			$( '.bws_tab' ).css( 'min-height', $( '#bws_settings_tabs' ).css( 'height' ) );

			/* jQuery tabs initialization */
			tabs.tabs({
				active: active_tab_index
			}).on( "tabsactivate", function( event, ui ) {
				if ( ! prevent_tabs_change ) {
					active_tab = ui.newTab.data( 'slug' );
					current_tab_field.val( active_tab );
				}
				prevent_tabs_change = false;
			});
			$( '.bws_trigger_tab_click' ).on( 'click', function () {
				$( '#bws_settings_tabs a[href="' + $( this ).attr( 'href' ) + '"]' ).click();
			});
		}
		/**
		 * Hide content for options on the plugin settings page
		 */
		var options = $( '.bws_option_affect' );
		if ( options.length ) {
			options.each( function() {
				var element = $( this );
				if ( element.is( ':selected' ) || element.is( ':checked' ) ) {
					$( element.data( 'affect-show' ) ).show();
					$( element.data( 'affect-hide' ) ).hide();
				} else {
					$( element.data( 'affect-show' ) ).hide();
					$( element.data( 'affect-hide' ) ).show();
				}
				if ( element.is( 'option' ) ) {
					element.parent().on( 'change', function() {
						var affect_hide = element.data( 'affect-hide' ),
							affect_show = element.data( 'affect-show' );
						if ( element.is( ':selected' ) ) {
							$( affect_show ).show();
							$( affect_hide ).hide();
						} else {
							$( affect_show ).hide();
							$( affect_hide ).show();
						}
					});
				} else {
					element.on( 'change', function() {
						var affect_hide = element.data( 'affect-hide' ),
							affect_show = element.data( 'affect-show' );
						if ( element.is( ':selected' ) || element.is( ':checked' ) ) {
							$( affect_show ).show();
							$( affect_hide ).hide();
						} else {
							$( affect_show ).hide();
							$( affect_hide ).show();
						}
					});
				}
			});
		}
	});
})(jQuery);