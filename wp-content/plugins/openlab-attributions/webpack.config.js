const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const path = require( 'path' );
const CopyPlugin = require( 'copy-webpack-plugin' );

module.exports = {
	...defaultConfig,
	entry: {
		'js/block-editor': path.resolve( __dirname, 'dist/js/block-editor.js' ),
		'js/classic-editor': path.resolve(
			__dirname,
			'dist/js/classic-editor.js'
		),
		'css/editor': path.resolve( __dirname, 'dist/scss/editor.scss' ),
		'css/style': path.resolve( __dirname, 'dist/scss/style.scss' ),
	},
	output: {
		...defaultConfig.output,
		path: path.resolve( __dirname, 'build' ),
		filename: '[name].js',
	},
	optimization: {
		...defaultConfig.optimization,
		splitChunks: {
			cacheGroups: {
				default: false,
			},
		},
	},
	plugins: [
		...defaultConfig.plugins,
		new CopyPlugin( {
			patterns: [
				{
					from: path.resolve( __dirname, 'dist/js/plugin.js' ),
					to: path.resolve( __dirname, 'build/js/plugin.js' ),
				},
			],
		} ),
	],
};
