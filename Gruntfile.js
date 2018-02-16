module.exports = function (grunt) {

    var checkFilePath = function (filepath) {
        if (!grunt.file.exists(filepath)) {
            grunt.fail.warn('Could not find: ' + filepath);
        } else {
            return true;
        }
    }

    require('jit-grunt')(grunt);
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        concat: {
            buddypress: {
                filter: checkFilePath,
                nonull: true,
                options: {
                    separator: ';'
                },
                src: [
                    'wp-content/plugins/buddypress/bp-core/js/confirm.js',
                    'wp-content/plugins/buddypress/bp-core/js/widget-members.js',
                    'wp-content/plugins/buddypress/bp-core/js/jquery-query.js',
                    'wp-content/plugins/buddypress/bp-core/js/vendor/jquery-cookie.js',
                    'wp-content/plugins/buddypress/bp-core/js/vendor/jquery-scroll-to.js',
                    'wp-content/plugins/buddypress-group-documents/js/general.js',
                    'wp-content/plugins/buddypress-group-email-subscription/bp-activity-subscription-js.js',
                    'wp-content/plugins/buddypress/bp-core/js/vendor/jquery.caret.js',
                    'wp-content/plugins/buddypress/bp-core/js/vendor/jquery.atwho.js',
                    'wp-content/plugins/buddypress/bp-activity/js/mentions.js'
                ],
                dest: 'wp-content/js/buddypress.js'
            },
            smoothscroll: {
                filter: checkFilePath,
                nonull: true,
                options: {
                    separator: ';'
                },
                src: [
                    'wp-content/mu-plugins/js/jquery-smooth-scroll/jquery.smooth-scroll.min.js',
                    'wp-content/mu-plugins/js/hyphenator/hyphenator.js',
                    'wp-content/mu-plugins/js/succint/jQuery.succinct.mod.js',
                    'wp-content/mu-plugins/js/select2/select2.min.js',
                    'wp-content/mu-plugins/js/openlab/openlab.search.js',
                    'wp-content/mu-plugins/js/openlab/openlab.truncation.js',
                    'wp-content/mu-plugins/js/openlab/openlab.nav.js',
                    'wp-content/mu-plugins/js/openlab/openlab.theme.fixes.js',
                ],
                dest: 'wp-content/js/smoothscroll.js'
            },
            rootblogcss: {
                filter: checkFilePath,
                nonull: true,
                options: {
                    separator: "\n"
                },
                src: [
                    'wp-content/plugins/achievements/templates/achievements/css/achievements.css',
                    'wp-content/plugins/bbpress/templates/default/css/bbpress.css',
                    'wp-content/plugins/contact-form-7/includes/css/styles.css',
                    'wp-content/plugins/post-gallery-widget/css/style.css',
                    'wp-content/plugins/cac-featured-content/css/cfcw-default.css',
                    'wp-content/plugins/buddypress/bp-templates/bp-legacy/css/buddypress.css',
                    'wp-content/plugins/buddypress/bp-activity/css/mentions.css',
                    'wp-content/plugins/buddypress-group-email-subscription/css/bp-activity-subscription-css.css'
                ],
                dest: 'wp-content/css/root-blog-styles.css'
            }
        },
        less: {
            production: {
                files: {
                    'wp-content/mu-plugins/css/openlab-toolbar.css': 'wp-content/mu-plugins/css/openlab-toolbar.less'
                }
            }
        },
        cssmin: {
            options: {

            },
            production: {
                files: {
                    'wp-content/mu-plugins/css/openlab-toolbar.css': 'wp-content/mu-plugins/css/openlab-toolbar.css'
                }
            }
        }

    });
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.registerTask('default', ['concat', 'less', 'cssmin']);
};
