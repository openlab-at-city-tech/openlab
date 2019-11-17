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
		const license = validateLicense( licenses, data.license );
		const url = format( license.label, license.url );
		parts.push( ( 'pd' === data.license ) ? `${ url }.` : `Licensed under ${ url }.` );
	}

	let attribution = parts.join( '. ' );

	if ( data.derivative ) {
		attribution += ` / A derivative from the <a href="${ data.derivative }">original work</a>`;
	}

	return attribution;
};

export default formatAttribution;
