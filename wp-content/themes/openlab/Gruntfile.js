module.exports = function (grunt) {
    require('jit-grunt')(grunt);
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        concat: {
            options: {
                separator: ';'
            },
            vendor: {
                src: ['js/bootstrap.min.js',
                    'node_modules/jcarousellite/jcarousellite.js',
                    'js/easyaccordion.js',
                    'js/jquery.easing.1.4.1.js',
                    'js/jquery.mobile.customized.min.js',
                    'js/camera.mod.js',
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
                }
            }
        },
        watch: {
            styles: {
                files: ['*.less'], // which files to watch
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
            }
        }
    });
    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.registerTask('default', ['concat', 'less'/*, 'watch'*/]);
};
