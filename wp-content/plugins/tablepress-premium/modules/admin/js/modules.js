/**
 * JavaScript code for the "Modules" screen.
 *
 * @package TablePress
 * @subpackage Views JavaScript
 * @author Tobias Bäthge
 * @since 2.0.0
 */

/**
 * Internal dependencies.
 */
import { $ } from '../../../admin/js/common/functions';
import { register_save_changes_keyboard_shortcut } from '../../../admin/js/common/keyboard-shortcut';

/**
 * Event handler for the beforeunload event.
 *
 * @param {Event} event Browser's `beforeunload` event.
 */
const beforeunload_dialog = ( event ) => {
	event.preventDefault(); // Cancel the event as stated by the standard.
	event.returnValue = ''; // Chrome requires returnValue to be set.
};

const $form = $( '#tablepress-page-form' );

/**
 * On form submit: Enable disabled input fields, so that they are sent in the HTTP POST request.
 */
$form.addEventListener( 'submit', function () {
	this.querySelectorAll( ':scope input' ).forEach( ( field ) => ( field.disabled = false ) );
	window.removeEventListener( 'beforeunload', beforeunload_dialog );
} );

let have_unsaved_changes = false;

/**
 * On checkbox change: Register beforeunload handler to trigger a "Save changes" warning.
 */
$form.addEventListener( 'change', () => {
	// Bail early if this function was already called.
	if ( have_unsaved_changes ) {
		return;
	}

	have_unsaved_changes = true;
	window.addEventListener( 'beforeunload', beforeunload_dialog );
} );

register_save_changes_keyboard_shortcut( $( '#tablepress-modules-save-changes' ) );
