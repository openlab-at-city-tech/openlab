/**
 * Returns attribution name or HTML link.
 *
 * @param {string} name Attribution name.
 * @param {string} url Attribution URL.
 *
 * @return {string} Formated attribution.
 */
const format = ( name, url ) => {
	if ( ! url ) {
		return name;
	}

	return `<a href="${ url }">${ name }</a>`;
};

export default format;
