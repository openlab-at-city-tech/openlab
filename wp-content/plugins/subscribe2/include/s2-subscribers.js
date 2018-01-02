/* global s2_script_strings */
/* exported s2_delete_check, bu_cats, bu_format, bu_digest, s2_script_strings */
// Version 1.0 - original version
// Version 1.1 - context specific delete warnings for registered or public subscribers
// Version 1.2 - single and plural string confirmation messages

function s2_delete_check() {
	document.getElementById( 'doaction' ).onclick = submitHandler;
	document.getElementById( 'doaction2' ).onclick = submitHandler;
}
function submitHandler() {
	var location, action1, action2, agree, selected;
	location = document.getElementById( 's2_location' );
	action1 = document.getElementById( 'bulk-action-selector-top' );
	action2 = document.getElementById( 'bulk-action-selector-bottom' );
	agree = false;
	selected = document.querySelectorAll( 'input[name="subscriber[]"]:checked' ).length;
	if ( 'delete' === action1.value || 'delete' === action2.value ) {
		if ( 'registered' === location.value ) {
			if ( selected > 1 ) {
				agree = window.confirm( s2_script_strings.registered_confirm_plural );
			} else {
				agree = window.confirm( s2_script_strings.registered_confirm_single );
			}
		} else if ( 'public' === location.value ) {
			if ( selected > 1 ) {
				agree = window.confirm( s2_script_strings.public_confirm_plural );
			} else {
				agree = window.confirm( s2_script_strings.public_confirm_single );
			}
		}
	}
	return agree;
}
function bu_cats() {
	var action, actions = document.getElementsByName( 'manage' );
	for ( var i = 0; i < actions.length; i++ ) {
		if ( actions[i].checked ) {
			action = actions[i].value;
		}
	}
	document.getElementById( 'bulk-action-selector-top' ).value = action;
	document.getElementById( 'doaction' ).click();
}
function bu_format() {
	document.getElementById( 'bulk-action-selector-top' ).value = 'format';
	document.getElementById( 'doaction' ).click();
}
function bu_digest() {
	document.getElementById( 'bulk-action-selector-top' ).value = 'digest';
	document.getElementById( 'doaction' ).click();
}
window.onload = s2_delete_check;