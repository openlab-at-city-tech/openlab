module.exports = function (grunt) {

	// Start out by loading the grunt modules we'll need
	require('load-grunt-tasks')(grunt);

	// Show elapsed time
	require('time-grunt')(grunt);

	grunt.initConfig(
		{

			/**
			 * Processes and compresses JavaScript.
			 */
			uglify: {

				production: {

					options: {
						beautify:         false,
						preserveComments: false,
						mangle:           {
							reserved: ['jQuery']
						}
					},

					files: {
						'assets/js/ufhealth-require-image-alt-tags.min.js': [
							'assets/js/src/ufhealth-require-image-alt-tags.js'
						]
					}
				}
			},

			/**
			 * Auto-prefix CSS Elements after SASS is processed.
			 */
			autoprefixer: {

				options: {
					browsers: ['last 5 versions'],
					map:      true
				},

				files: {
					expand:  true,
					flatten: true,
					src:     ['assets/css/ufhealth-require-image-alt-tags.css'],
					dest:    'assets/css'
				}
			},

			/**
			 * Minify CSS after prefixes are added
			 */
			cssmin: {

				target: {

					files: [{
						expand: true,
						cwd:    'assets/css',
						src:    ['ufhealth-require-image-alt-tags.css'],
						dest:   'assets/css',
						ext:    '.min.css'
					}]

				}
			},

			/**
			 * Process SASS
			 */
			sass: {

				dist: {

					options: {
						style:     'expanded',
						sourceMap: true,
						noCache:   true
					},

					files: {
						'assets/css/ufhealth-require-image-alt-tags.css': 'assets/css/scss/ufhealth-require-image-alt-tags.scss'
					}
				}
			},

			/**
			 * Update translation file.
			 */
			makepot: {

				target: {
					options: {
						type:        'wp-plugin',
						domainPath:  '/languages',
						mainFile:    'ufhealth-require-image-alt-tags.php',
						potFilename: 'ufhealth-require-image-alt-tags.pot'
					}
				}
			},

			phpunit: {

				classes: {
					dir: 'tests/'
				},

				options: {

					bin:        './vendor/bin/phpunit',
					testSuffix: 'Tests.php',
					bootstrap:  'bootstrap.php',
					colors:     true

				}
			},

			/**
			 * Clean up the JavaScript
			 */
			jshint: {
				options: {
					jshintrc: true
				},
				all:     ['assets/js/src/ufhealth-require-image-alt-tags.js']
			},

			/**
			 * Watch scripts and styles for changes
			 */
			watch: {

				options: {
					livereload: true
				},

				scripts: {

					files: [
						'assets/js/src/*'
					],

					tasks: ['uglify:production']

				},

				styles: {

					files: [
						'assets/css/scss/*'
					],

					tasks: ['sass', 'autoprefixer', 'cssmin']

				}
			}
		}
	);

	// A very basic default task.
	grunt.registerTask('default', ['phpunit', 'jshint', 'uglify:production', 'sass', 'autoprefixer', 'cssmin', 'makepot']);
	grunt.registerTask('dev', ['default', 'watch']);

};