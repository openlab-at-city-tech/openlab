module.exports = function( grunt ) {

	'use strict';

	// Project configuration
	grunt.initConfig( {

		pkg: grunt.file.readJSON( 'package.json' ),

		addtextdomain: {
			options: {
				textdomain: 'trustedlogin',
			},
			update_all_domains: {
				options: {
					updateDomains: true
				},
				src: [ '*.php', '**/*.php', '!\.git/**/*', '!bin/**/*', '!node_modules/**/*', '!tests/**/*' ]
			}
		},

		makepot: {
			target: {
				options: {
					domainPath: '/languages',
					exclude: [ '\.git/*', 'bin/*', 'node_modules/*', 'tests/*' ],
					mainFile: 'src/Client.php',
					potFilename: 'trustedlogin-client.pot',
					potHeaders: {
						poedit: true,
						'x-poedit-keywordslist': true
					},
					type: 'wp-plugin',
					updateTimestamp: true
				}
			}
		},

		watch: {
			scss: {
				files: ['src/assets/src/*.scss'],
				tasks: ['sass:dist', 'postcss:dist']
			},
			options: {
				livereload: true
			}
		},

		postcss: {
			options: {
				map: false,
				processors: [
					require('autoprefixer')
				]
			},
			dist: {
				src: 'src/assets/trustedlogin.css'
			}
		},

		sass: {
			options: {
				style: 'compressed',
				sourceMap: false,
				noCache: true,
			},
			dist: {
				files: [{
					expand: true,
					cwd: 'src/assets/src',
					src: ['trustedlogin.scss'],
					dest: 'src/assets',
					ext: '.css'
				}]
			}
		},
	} );

	grunt.loadNpmTasks( 'grunt-wp-i18n' );
	grunt.loadNpmTasks( 'grunt-contrib-sass' );
	grunt.loadNpmTasks( 'grunt-contrib-watch' );
	grunt.loadNpmTasks( 'grunt-postcss' );
	grunt.registerTask( 'default', [ 'i18n', 'sass', 'watch' ] );
	grunt.registerTask( 'i18n', ['addtextdomain', 'makepot'] );

	grunt.util.linefeed = '\n';

};
