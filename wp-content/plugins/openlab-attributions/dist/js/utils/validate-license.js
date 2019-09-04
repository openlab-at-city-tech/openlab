/**
 * Validate selected license against pre-defined ones.
 *
 * @param {Array} options List of licenses.
 * @param {string} value  Selected license.
 *
 * @return {Object} License object.
 */
const validateLicense = ( options, value ) => {
	const license = options
		.filter( ( option ) => option.label === value )
		.pop();

	return license;
};

export default validateLicense;
