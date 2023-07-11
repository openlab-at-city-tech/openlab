const wpPreset = require( '@wordpress/postcss-plugins-preset' );

module.exports = {
	plugins: [
		...wpPreset,
		require( 'postcss-custom-media' )(),          // Custom media queries: https://www.npmjs.com/package/postcss-custom-media
		require( 'postcss-combine-media-query' )(),   // Combine media queries: https://www.npmjs.com/package/postcss-combine-media-query
		require( 'postcss-sort-media-queries' )()	  // Sort media queries: https://www.npmjs.com/package/postcss-sort-media-queries
	]
};
