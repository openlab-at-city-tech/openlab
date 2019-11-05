/**
 * Internal dependencies
 */
import format from './format';
import validateLicense from './validate-license';

/**
 * Format the attribution for render.
 *
 * @param {Object} data    Form input data.
 * @param {Array} licenses List of licenses.
 *
 * @return {string} Formatted attribution.
 */
const formatAttribution = ( data, licenses ) => {
	const parts = [];

	if ( data.authorName ) {
		parts.push( format( data.authorName, data.authorUrl ) );
	}

	if ( data.datePublished ) {
		parts.push( `(${ data.datePublished })` );
	}

	if ( data.title ) {
		parts.push( format( data.title, data.titleUrl ) );
	}

	if ( data.derivative ) {
		const url = format( data.derivative, data.derivative );
		parts.push( `Retrieved from ${ url }` );
	}

	if ( data.publisher ) {
		parts.push( format( data.publisher, data.publisherUrl ) );
	}

	if ( data.project ) {
		parts.push( format( data.project, data.publisherUrl ) );
	}

	if ( data.license ) {
		const license = validateLicense( licenses, data.license );
		const url = format( license.label, license.url );
		parts.push( `Licensed under ${ url }` );
	}

	return parts.join( '. ' );
};

export default formatAttribution;
