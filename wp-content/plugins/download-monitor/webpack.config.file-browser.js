const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const path = require( 'path' );

module.exports = {
	...defaultConfig,
	entry: { index: './assets/apps/file-browser/index.jsx' },
	output: {
		path: path.resolve( __dirname, 'assets/js/file-browser' ),
	},
};
