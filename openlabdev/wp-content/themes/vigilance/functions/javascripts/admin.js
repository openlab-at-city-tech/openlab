jQuery(document).ready(function($) {
	// hides as soon as the DOM is ready
	$( 'div.v-option-body' ).hide();
	// shows on clicking the noted link
	$( 'h3' ).click(function() {
		$(this).toggleClass("open");
		$(this).next("div").slideToggle( '1000' );
		return false;
	});
	// logo init
	if($('#logo_upload_button').length > 0) {
		Logo.init();
	}
});

function toggleColorpicker (link, id, toggledir, opentext, closetext) {
	jQuery( '.colorpicker_container' ).hide();
	if (toggledir == "open") {
		jQuery( '#'+id+'_colorpicker' ).show();
		jQuery(link).replaceWith( '<a href="javascript:return false;" onclick="toggleColorpicker (this, \''+id+'\', \'close\', \''+opentext+'\', \''+closetext+'\' )">'+closetext+'</a>' );
	} else {
		jQuery(link).replaceWith( '<a href="javascript:return false;" onclick="toggleColorpicker (this, \''+id+'\', \'open\', \''+opentext+'\', \''+closetext+'\' )">'+opentext+'</a>' );
	}
}

var Logo = {
		init: function (){
					new AjaxUpload( 'logo_upload_button', {
							action: '/wp-admin/admin-ajax.php',
							data: { action: 'uploadLogo' },
							autoSubmit: true,
							responseType: 'text/html',
							onChange: function ( file, extension ){},
							onSubmit: function ( file, extension ) {
										if ( !( extension && /^(jpg|png|jpeg|gif)$/i.test( extension ) ) ){
													Logo.display_error('<div class="logo-error">Error: invalid file extension</div>');
													return false;
											}
							},
							onComplete: function ( file, response ) {
										if ( response.match(/class="logo-error"/) ){
											Logo.display_error(response);
										} else {
											jQuery( '.logo-preview-link a.thickbox' ).attr( 'href', response ).show().next( 'div' ).hide();
											jQuery( '.logo-file-name' ).html( file );
											jQuery( '.logo-image-input' ).val( file ).parent( '.v-field-c') .hide().next( '.v-field-logo' ).show();
										}
							}
					});
		},
		remove: function (){
					jQuery.ajax({
						type: 'POST',
						url: '/wp-admin/admin-ajax.php',
						data: { action: 'deleteLogo' },
						success: function ( html ){
									jQuery( '.logo-error' ).remove();
									jQuery( '.logo-image-input' ).val( '' ).parent( '.v-field-c' ).show().next( '.v-field-logo' ).hide();
						},
						error: function ( html ){
									alert( html );
						}
					});
					return false;
		},
		view: function ( el ){
					window.open( jQuery( el ).attr( 'href' )+jQuery( '.logo-image-input' ).val() );
					return false;
		},
		display_error: function ( msg ){
					jQuery( '.logo-error' ).remove();
					jQuery( '#logo_upload_button' ).after(msg);
		},
		preview: function () {
					alert( 'test' );
		}
}