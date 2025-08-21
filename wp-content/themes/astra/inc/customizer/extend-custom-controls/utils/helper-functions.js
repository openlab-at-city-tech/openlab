/**
 * Converts a hex color code to an RGB array.
 *
 * @param {string} hex - The hex color code.
 * @returns {number[]} - The RGB array.
 */
const hexToRgb = ( hex ) => {
	hex = hex?.replace( /^#/, '' );
	if ( hex?.length === 3 ) {
		hex = hex
			.split( '' )
			.map( ( c ) => c + c )
			.join( '' );
	}
	let bigint = parseInt( hex, 16 );
	return [ ( bigint >> 16 ) & 255, ( bigint >> 8 ) & 255, bigint & 255 ];
};

/**
 * Determines whether a dark icon is preferred based on the provided background color.
 *
 * @param {string} color - The color code (hex or CSS variable) to analyze.
 * @returns {boolean} - Returns true if the background is light (requiring a dark icon), false otherwise.
 */
const isDarkishColor = ( color ) => {
	// Check if the color is a CSS variable
	if ( color?.startsWith( '--' ) ) {
		color = getCssVariableColor( color );
	}

	// Convert color to RGB
	let rgb = hexToRgb( color );

	// Calculate relative luminance
	let luminance = 0.2126 * ( rgb[ 0 ] / 255 ) + 0.7152 * ( rgb[ 1 ] / 255 ) + 0.0722 * ( rgb[ 2 ] / 255 );

	// Return true if the background is light (luminance > 0.5), meaning the icon should be dark
	return luminance > 0.5;
};

export { hexToRgb, isDarkishColor };
