const mix = require( 'laravel-mix' );
const DependencyExtractionWebpackPlugin = require( '@wordpress/dependency-extraction-webpack-plugin' );

/*
 * Sets the development path to assets. By default, this is the `/resources`
 * folder in the theme.
 */
const devPath = 'dist';

/**
 * Disable OS notifications.
 */
mix.disableNotifications();

/*
 * Sets the path to the generated assets. By default, this is the `/public` folder
 * in the theme. If doing something custom, make sure to change this everywhere.
 */
mix.setPublicPath( 'build' );

/*
 * Builds sources maps for assets.
 *
 * @link https://laravel.com/docs/5.6/mix#css-source-maps
 */
mix.sourceMaps();

/*
 * Compile JavaScript.
 */
mix.js( `${devPath}/js/classic-editor.js`, 'js' ).react();
mix.js( `${devPath}/js/block-editor.js`, 'js' ).react();;

/**
 * Copy files.
 */
mix.copy( `${devPath}/js/plugin.js`, 'build/js' );

// Compile SASS/CSS.
mix.sass( `${devPath}/scss/editor.scss`, 'css' )
	.sass( `${devPath}/scss/style.scss`, 'css' );

/*
 * Add custom Webpack configuration.
 */
mix.webpackConfig( {
	// stats        : 'minimal',
	// devtool      : mix.inProduction() ? false : 'source-map',
	// performance  : { hints  : false },
	plugins      : [
		new DependencyExtractionWebpackPlugin(),
	]
} );
