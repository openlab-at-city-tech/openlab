module.exports = function (grunt) {

    var checkFilePath = function (filepath) {
        if (!grunt.file.exists(filepath)) {
            grunt.fail.warn('Could not find: ' + filepath);
        } else {
            return true;
        }
    }

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        htmlclean: {
            options: {
                unprotect: /(<[^\S\f]*\?[^\S\f]*php\b[\s\S]*)/ig,
                protect: /(?:#|\/\/)[^\r\n]*|\/\*[\s\S]*?\*\/\n\r\n\r/ig
            },
            deploy: {
                expand: true,
                cwd: 'components/parts/source/',
                src: '**/*.php',
                dest: 'components/parts/'
            }
        },
        copy: {
            main: {
                files: [
                    {
                        expand: true,
                        cwd: 'node_modules/chart.js/dist/',
                        src: ['**'],
                        dest: 'js/lib/chart/'
                    }
                ]
            }
        },
        concat: {
            jscrollpane: {
                filter: checkFilePath,
                nonull: true,
                options: {
                    separator: ';'
                },
                src: [
                    'node_modules/jscrollpane/script/jquery.mousewheel.js',
                    'node_modules/jscrollpane/script/mwheelIntent.js',
                    'node_modules/jscrollpane/script/jquery.jscrollpane.min.js'
                ],
                dest: 'js/lib/jscrollpane/jscrollpane.dist.js'
            },
            vendorcss: {
                filter: checkFilePath,
                nonull: true,
                options: {
                    separator: "\n"
                },
                src: [
                    'node_modules/jscrollpane/style/jquery.jscrollpane.css'
                ],
                dest: 'css/vendor.css'
            }
        },
        requirejs: {
            compile: {
                options: {
                    baseUrl: './js',
                    paths: {
                        'models': 'app/models',
                        'views': 'app/views',
                        'router': 'app/router',
                        'bootstrap': 'lib/bootstrap/js/bootstrap.min',
                        'chart': 'lib/chart/Chart.min',
                        'bootstrap3-typeahead': 'lib/bootstrap3-typeahead/bootstrap3-typeahead.min',
                        'jscrollpane': 'lib/jscrollpane/jscrollpane.dist'
                    },
                    shim: {
                        'bootstrap': {
                            deps: ['jquery']
                        }
                    },
                    include: 'oplb-gradebook-app',
                    out: './js/oplb-gradebook-app-min.js'
                }
            }
        },
        less: {
            development: {
                options: {
                    compress: false,
                    optimization: 2
                },
                files: {
                    "GradeBook.css": "GradeBook.less"
                }
            }
        },
    });

    grunt.loadNpmTasks('grunt-htmlclean');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-requirejs');
    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.registerTask('default', ['copy', 'concat', 'htmlclean', 'requirejs', 'less']);
};