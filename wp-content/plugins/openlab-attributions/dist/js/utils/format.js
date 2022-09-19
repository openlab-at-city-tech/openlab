/**
 * WordPress dependencies
 */
import { sprintf, __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import validateLicense from './validate-license';

/**
 * Returns attribution name or HTML link.
 *
 * @param {string} name Attribution name.
 * @param {string} url Attribution URL.
 *
 * @return {string} Formated attribution.
 */
export function format( name, url ) {
	if ( ! url ) {
		return name;
	}

	return `<a href="${ url }">${ name }</a>`;
}

/**
 * Format attribution license.
 *
 * @param {string} value  Current license.
 *
 * @return {string} Formatted license string.
 */
function formatLicense( value ) {
	let text = '';
	const license = validateLicense( value );

	switch ( value ) {
		case 'pd':
		case 'fu':
			text = `${ license.label }.`;
			break;

		case 'u':
			text = __( 'License unknown.', 'openlab-attributions' );
			break;

		default:
			text = sprintf( __( 'Licensed under %s.', 'openlab-attributions' ), format( license.label, license.url ) );
			break;
	}

	return text;
}

/**
 * Format "Adapted From" data into a string.
 *
 * @param {Object} data                Form input data.
 * @param {string} data.adaptedTitle   Title
 * @param {string} data.adaptedAuthor  Author
 * @param {string} data.adaptedLicense License
 * @param {string} data.derivative     URL
 * @return {string}                    Formatted "Adapted From" string.
 */
function formatAdaptedFrom( {
	adaptedTitle,
	adaptedAuthor,
	adaptedLicense,
	derivative,
} ) {
	const isLegacy =
		! adaptedTitle && ! adaptedAuthor && ! adaptedLicense && derivative;

	if ( isLegacy ) {
		return sprintf(
			__( 'Adapted from the <a href="%s">original work</a>', 'openlab-attributions' ),
			derivative
		);
	}

	const license = validateLicense( adaptedLicense );

	if ( ! adaptedAuthor ) {
		return sprintf(
			__( 'Adapted from %1$s, licensed under %2$s.', 'openlab-attributions' ),
			format( adaptedTitle, derivative ),
			format( license?.label, license?.url )
		);
	}

	return sprintf(
		__( 'Adapted from %1$s by %2$s, licensed under %3$s.', 'openlab-attributions' ),
		format( adaptedTitle, derivative ),
		format( adaptedAuthor, null ),
		format( license?.label, license?.url )
	);
}

/**
 * Format the attribution for render.
 *
 * @param {Object} data    Form input data.
 *
 * @return {string} Formatted attribution.
 */
export function formatAttribution( data ) {
	const parts = [];

	if ( data.title ) {
		parts.push( format( data.title, data.titleUrl ) );
	}

	if ( data.authorName ) {
		parts.push( format( data.authorName, data.authorUrl ) );
	}

	if ( data.publisher ) {
		parts.push( format( data.publisher, data.publisherUrl ) );
	}

	if ( data.project ) {
		parts.push( format( data.project, data.publisherUrl ) );
	}

	if ( data.datePublished ) {
		parts.push( data.datePublished );
	}

	if ( data.license ) {
		parts.push( formatLicense( data.license ) );
	}

	let attribution = parts.join( '. ' );

	if ( data.adaptedTitle || data.derivative ) {
		const separator = parts.length > 0 ? '. ' : '';
		attribution += `${ separator }${ formatAdaptedFrom( data ) }`;
	}

	// Append "." at the end of the sentence if there is none.dd 
	if( attribution.substring(attribution.length -1) != '.' ) {
		attribution += '.';
	}

	return attribution;
}
