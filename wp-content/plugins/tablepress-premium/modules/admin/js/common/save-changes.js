/**
 * Save changes functions for modules.
 *
 * @package TablePress
 * @subpackage Views JavaScript
 * @author Tobias Bäthge
 * @since 2.0.0
 */

/* globals ajaxurl */
/* eslint-disable jsdoc/valid-types */

/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { buildQueryString } from '@wordpress/url';

/**
 * Internal dependencies.
 */
import { $ } from '../../../../admin/js/common/functions';

/**
 * Save Changes to the server.
 *
 * @param {HTMLElement} dom_node     DOM node into which to insert the spinner and notice.
 * @param {Object}      request_data Data for the AJAX request.
 */
export const save_changes = function ( dom_node, request_data ) {
	dom_node.insertAdjacentHTML( 'beforeend', `<span id="spinner-save-changes" class="spinner-save-changes spinner is-active" title="${ __( 'Changes are being saved …', 'tablepress' ) }"></span>` );
	$( '.button-save-changes' ).forEach( ( button ) => ( button.disabled = true ) );

	document.body.classList.add( 'wait' );

	// Save the table data to the server via an AJAX request.
	fetch( ajaxurl, {
		method: 'POST',
		headers: {
			'Content-Type': 'application/x-www-form-urlencoded',
			Accept: 'application/json',
		},
		body: buildQueryString( request_data ),
	} )
	// Check for HTTP connection problems.
	.then( ( response ) => {
		if ( ! response.ok ) {
			throw new Error( `There was a problem with the server, HTTP response code ${ response.status } (${ response.statusText }).` );
		}
		return response.json();
	} )
	// Check for problems with the transmitted data.
	.then( ( data ) => {
		if ( 'undefined' === typeof data || null === data || '-1' === data || 'undefined' === typeof data.success ) {
			throw new Error( 'The JSON data returned from the server is unclear or incomplete.' );
		}

		if ( true !== data.success ) {
			const debug_html = data.error_details ? `</p><p>These errors were encountered:</p><pre>${ data.error_details }</pre><p>` : '';
			throw new Error( `The data could not be saved to the database properly.${ debug_html }` );
		}

		save_changes_success( data );
	} )
	// Handle errors.
	.catch( ( error ) => save_changes_error( error.message ) );
};

/**
 * [success description]
 *
 * @param {[type]} data [description]
 */
const save_changes_success = function ( data ) {
	const action_messages = {
		success_save: __( 'The changes were saved successfully.', 'tablepress' ),
		error_save:   __( 'Attention: Unfortunately, an error occurred.', 'tablepress' ),
	};
	const type = ( data.message.includes( 'error' ) ) ? 'error' : 'success';
	save_changes_after_saving_notice( type, action_messages[ data.message ] );
};

/**
 * [error description]
 *
 * @param {[type]} message [description]
 */
const save_changes_error = function ( message ) {
	message = __( 'Attention: Unfortunately, an error occurred.', 'tablepress' ) + ' ' + message;
	save_changes_after_saving_notice( 'error', message );
};

/**
 * [after_saving_notice description]
 *
 * @param {[type]} type    [description]
 * @param {[type]} message [description]
 */
const save_changes_after_saving_notice = function ( type, message ) {
	const div_id = `save-changes-${ Date.now() }`;

	const $spinner = $( '#spinner-save-changes' );
	$spinner.parentNode.insertAdjacentHTML( 'afterend', `<div id="${ div_id }" class="ajax-alert notice notice-${ type }"><p>${ message }</p></div>` );
	$spinner.remove();

	const $notice = $( `#${ div_id }` );
	void $notice.offsetWidth; // Trick browser layout engine. Necessary to make CSS transition work.
	$notice.style.opacity = 0;
	$notice.addEventListener( 'transitionend', () => $notice.remove() );

	$( '.button-save-changes' ).forEach( ( button ) => ( button.disabled = false ) );

	document.body.classList.remove( 'wait' );
};
