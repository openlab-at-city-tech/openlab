/**
 * Internal dependencies
 */
import format from './format';
import validateLicense from './validate-license';

/**
 * Format attribution for shortcode.
 *
 * @param {Object} data    Form input data.
 * @param {Array} licenses List of licenses.
 *
 * @return {string} Formatted attribution.
 */
const formatAttribution = ( data, licenses ) => {
	if ( data.title ) {
		data.title = format( data.title, data.titleUrl );
	}

	if ( data.author ) {
		data.author = format( data.author, data.authorUrl );
	}

	if ( data.publisher ) {
		data.publisher = format( data.publisher, data.publisherUrl );
	}

	if ( data.project ) {
		data.project = format( data.project, data.projectUrl );
	}

	// Get our license object from the list.
	const license = validateLicense( licenses, data.license );

	if ( license ) {
		data.license = format( license.label, license.url );
	}

	const attribution = `
			${ data.title }
			${ data.author ? `by ${ data.author }.` : '' }
			${ data.year ? `${ data.year }.` : '' }
			${ data.publisher ? `${ data.publisher }.` : '' }
			${ data.license ? `is licensed under ${ data.license }.` : '' }
			${ data.derivative ? `Receive from ${ data.derivative }` : '' }
	`;

	return attribution.trim();
};

export default formatAttribution;
