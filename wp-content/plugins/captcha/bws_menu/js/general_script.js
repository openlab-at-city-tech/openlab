function bws_show_settings_notice() {
	(function($) {
		$( '.updated.fade, .error' ).css( 'display', 'none' );
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
	});
})(jQuery);