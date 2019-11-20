/**
 * Internal dependencies
 */
import format from './format';
import validateLicense from './validate-license';

/**
 * Format attribution license.
 * @param {Object} license License object.
 * @param {string} value  Current license.
 *
 * @return {string} Formatted license string.
 */
const formatLicense = ( license, value ) => {
	let text = '';

	switch ( value ) {
		case 'pd':
		case 'fu':
			text = `${ license.label }.`;
			break;

		case 'u':
			text = 'License unknown.';
			break;

		default:
			text = `Licensed under ${ format( license.label, license.url ) }.`;
			break;
	}

	return text;
};

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
		parts.push( formatLicense( license, data.license ) );
	}

	let attribution = parts.join( '. ' );

	if ( data.derivative ) {
		attribution += ` / A derivative from the <a href="${ data.derivative }">original work</a>`;
	}

	return attribution;
};

export default formatAttribution;
