module.exports = function (grunt) {
    require('jit-grunt')(grunt);
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        htmlclean: {
            options: {
                unprotect: /(<[^\S\f]*\?[^\S\f]*php\b[\s\S]*)/ig,
                protect: /(?:#|\/\/)[^\r\n]*|\/\*[\s\S]*?\*\/\n\r\n\r/ig
            },
            deploy: {
                expand: true,
                cwd: 'parts/source/',
                src: '**/*.php',
                dest: 'parts/'
            }
        },
        concat: {
            options: {
                separator: ';'
            },
            vendor: {
                src: ['js/bootstrap.min.js',
                    'node_modules/jcarousellite/jcarousellite.js',
                    'js/easyaccordion.js',
                    'js/jquery.easing.1.3.js',
                    'js/jquery.mobile.customized.min.js',
                    'js/camera.min.js',
                    'js/jQuery.succinct.mod.js',
                    'js/detect-zoom.js'],
                dest: 'js/dist/vendor.js'
            },
        },
        less: {
            development: {
                options: {
                    compress: false, //compression seems to be stripping out the stylesheet comments, which we need
                    optimization: 2
                },
                files: {
                    "style.css": "style.less", // destination file and source file
                    "../../mu-plugins/css/openlab-toolbar.css": "../../mu-plugins/css/openlab-toolbar.less"
                }
            }
        },
        watch: {
            styles: {
                files: ['*.less', '../../mu-plugins/css/*.less'], // which files to watch
                tasks: ['less'],
                options: {
                    nospawn: true
                }
            },
            scripts: {
                files: ['js/*.js'],
                tasks: ['concat'],
                options: {
                    nospawn: true
                }
            },
            core: {
                files: ['parts/source/**/*.php'], // which files to watch
                tasks: ['htmlclean'],
                options: {
                    nospawn: true
                }
            }
        }
    });
    grunt.loadNpmTasks('grunt-htmlclean');
    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.registerTask('default', ['concat', 'htmlclean', 'less', 'watch']);
};
