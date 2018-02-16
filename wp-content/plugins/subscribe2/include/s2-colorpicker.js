// version 1.0 - original version
// version 1.1 - Update for Subscribe2 9.0 to remove unecessary code now WordPress 3.3 is minimum requirement
// version 1.2 - Initialise the colour fields on page load so they are the correct colour
jQuery( document ).ready(function () {
	jQuery( document ).on( 'focus', '.colorpickerField', function () {
		if ( jQuery( this ).is( '.s2_initialised' ) || -1 !== this.id.search( '__i__' ) ) {
			return; // exit early, already initialized or not activated
		}
		jQuery( this ).addClass( 's2_initialised' );
		var picker,
			field = this.id.substr( 0, 20 );
		jQuery( '.s2_colorpicker' ).each(function () {
			if ( -1 !== this.id.search( field ) ) {
				picker = this.id;
				return false; // stop looping
			}
		});
		jQuery( this ).on( 'focusin', function () {
			jQuery( '.s2_colorpicker' ).slideUp();
			jQuery.farbtastic( '#' + picker ).linkTo( this );
			jQuery( '#' + picker ).slideDown();
		});
		jQuery( this ).on( 'focusout', function () {
			jQuery( '#' + picker ).slideUp();
		});
		jQuery( this ).trigger( 'focus' );
	});
	jQuery( '.colorpickerField' ).each(function () {
		jQuery.farbtastic( '#' + this.id ).linkTo( this );
	});
});