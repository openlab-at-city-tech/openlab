import svelte from 'rollup-plugin-svelte';
import babel from '@rollup/plugin-babel';
import commonjs from '@rollup/plugin-commonjs';
import resolve from '@rollup/plugin-node-resolve';
import livereload from 'rollup-plugin-livereload';
import { terser } from 'rollup-plugin-terser';

const production = !process.env.ROLLUP_WATCH;
const minify = !!process.env.minify;

export default {
	input: 'main.js',
	output: {
		sourcemap: !production || minify,
		name: 'bookly',
		format: 'iife',
		file: !production || minify ? '../bookly.min.js' : '../bookly.js',
		globals: {
			jquery: 'jQuery'
		}
	},
	external: ['jquery'],
	plugins: [
		svelte({
			// enable run-time checks when not in production
			dev: !production,
			// we'll extract any component CSS out into
			// a separate file - better for performance
			// css: css => {
			// 	css.write('../../css/bookly.css');
			// }
		}),

		// If you have external dependencies installed from
		// npm, you'll most likely need these plugins. In
		// some cases you'll need additional configuration -
		// consult the documentation for details:
		// https://github.com/rollup/plugins/tree/master/packages/commonjs
		resolve({
			browser: true,
			dedupe: ['svelte']
		}),
		commonjs(),

		production && babel({
			extensions: ['.js', '.mjs', '.html', '.svelte'],
			babelHelpers: 'runtime',
			// babelHelpers: 'bundled',
			exclude: ['node_modules/@babel/**', 'node_modules/core-js-pure/**'],
			presets: [
				['@babel/preset-env', {
					targets: '> 0.25%, not dead',
					// modules: false,
					// spec: true,
					// forceAllTransforms: true,
					// useBuiltIns: 'usage',
					shippedProposals: true,
					// corejs: 3
				}]
			],
			plugins: [
				['@babel/plugin-transform-runtime', {
					useESModules: true,
					corejs: 3
				}]
			]
		}),

		// Watch the `public` directory and refresh the
		// browser on changes when not in production
		!production && livereload('..'),

		// If we're building for production (npm run build
		// instead of npm run dev), minify
		minify && terser()
	],
	watch: {
		clearScreen: false
	}
};
