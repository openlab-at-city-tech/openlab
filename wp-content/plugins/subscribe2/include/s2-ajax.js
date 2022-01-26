/* global s2AjaxScriptStrings */
// Version 1.0 - original version
// Version 1.1 - added position and minimum width and height attributes to .dialog
// Version 1.2 - added nonce use for form submission
// Version 1.3 - eslinted

var s2jQuery = jQuery.noConflict();
s2jQuery( document ).ready(
	function() {
			var dialog  = s2jQuery( '<div></div>' );
			var ajaxurl = s2AjaxScriptStrings.ajaxurl;
			s2jQuery( 'a.s2popup' ).click(
				function( event ) {
					var data = {
						'action': 'subscribe2_form',
						'data': s2jQuery( 'a.s2popup' ).attr( 'id' )
					};
					event.preventDefault();
					jQuery.post(
						ajaxurl,
						data,
						function( response ) {
							dialog.html( response );
						}
					);
					dialog.dialog(
						{
							modal: true,
							zIndex: 10000,
							minWidth: 350,
							minHeight: 300,
							title: s2AjaxScriptStrings.title,
							closeText: ""
						}
					);
					dialog.dialog( 'open' );
				}
			);
			s2jQuery( document ).on(
				'submit',
				'#s2ajaxform',
				function( event ) {
					var email     = s2jQuery( '#s2ajaxform input[name=email]' ).val();
					var ip        = s2jQuery( '#s2ajaxform input[name=ip]' ).val();
					var firstname = s2jQuery( '#s2ajaxform input[name=firstname]' ).val();
					var lastname  = s2jQuery( '#s2ajaxform input[name=lastname]' ).val();
					var uri       = s2jQuery( '#s2ajaxform input[name=uri]' ).val();
					var btn       = s2jQuery( this ).find( 'input[type=submit][clicked=true]' );
					var data;

					event.preventDefault();

					if ( 'undefined' === typeof firstname ) {
						firstname = '';
					}

					if ( 'undefined' === typeof lastname ) {
						lastname = '';
					}

					if ( 'undefined' === typeof uri ) {
						uri = 'http://';
					}

					if ( btn.length && s2jQuery( '#s2ajaxform' ).has( btn ) ) {
						data = {
							'action': 'subscribe2_submit',
							'nonce': s2AjaxScriptStrings.nonce,
							'data': {
								'email': email,
								'ip': ip,
								'firstname': firstname,
								'lastname': lastname,
								'uri': uri,
								'button': btn.attr( 'name' )
							}
						};
						jQuery.post(
							ajaxurl,
							data,
							function( response ) {
								dialog.html( response );
							}
						);
					}
				}
			);

			// Allows detection of which button was clicked
			s2jQuery( document ).on(
				'click',
				'#s2ajaxform input[type=submit]',
				function() {
					s2jQuery( '#s2ajaxform input[type=submit]' ).removeAttr( 'clicked' );
					s2jQuery( this ).attr( 'clicked', 'true' );
				}
			);

			// when form is closed return to default
			s2jQuery( document ).on(
				'dialogclose',
				function() {
					dialog.html( dialog );
				}
			);
	}
);
