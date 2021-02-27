const licenses = window.attrLicenses || [];

/**
 * Validate selected license against pre-defined ones.
 *
 * @param {string} value  Selected license.
 *
 * @return {Object} License object.
 */
function validateLicense( value ) {
	const license = licenses
		.filter( ( option ) => option.value === value )
		.pop();

	return license;
}

export default validateLicense;
