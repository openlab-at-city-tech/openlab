module.exports = function (grunt) {
    require('jit-grunt')(grunt);
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        concat: {
            buddypress: {
                options: {
                    separator: ';'
                },
                src: [
			'wp-content/plugins/buddypress/bp-core/js/confirm.min.js',
			'wp-content/plugins/buddypress/bp-core/js/widget-members.min.js',
			'wp-content/plugins/buddypress/bp-core/js/jquery-query.min.js',
			'wp-content/plugins/buddypress/bp-core/js/jquery-cookie.min.js',
			'wp-content/plugins/buddypress/bp-core/js/jquery-scroll-to.min.js',
			'wp-content/plugins/buddypress-group-documents/js/general.js',
			'wp-content/plugins/buddypress-group-email-subscription/bp-activity-subscription.js',
			'wp-content/plugins/buddypress/bp-core/js/jquery.caret.min.js',
			'wp-content/plugins/buddypress/bp-core/js/jquery.atwho.min.js',
			'wp-content/plugins/buddypress/bp-activity/js/mentions.min.js'
		],
                dest: 'wp-content/js/buddypress.js'
            },
	    smoothscroll: {
                options: {
                    separator: ';'
                },
		src: [
			'wp-content/mu-plugins/js/jquery-smooth-scroll/jquery.smooth-scroll.min.js',
			'wp-content/mu-plugins/js/jquery-custom-select/jquery.customSelect.min.js',
			'wp-content/mu-plugins/js//hyphenator/hyphenator.js',
			'wp-content/mu-plugins/js/openlab/openlab.search.js',
			'wp-content/mu-plugins/js/openlab/openlab.nav.js',
			'wp-content/mu-plugins/js/openlab/openlab.theme.fixes.js',
		],
		dest: 'wp-content/js/smoothscroll.js'
	    },
	    rootblogcss: {
                options: {
                    separator: "\n"
                },
		src: [
			'wp-content/plugins/achievements/templates/achievements/css/achievements.css',
			'wp-content/plugins/bbpress/templates/default/css/bbpress.css',
			'wp-content/plugins/contact-form-7/includes/css/styles.css',
			'wp-content/plugins/post-gallery-widget/css/style.css',
			'wp-content/plugins/cac-featured-content/css/cfcw-default.css',
			'wp-content/plugins/buddypress/bp-templates/bp-legacy/css/buddypress.min.css',
			'wp-content/plugins/buddypress/bp-activity/css/mentions.min.css',
			'wp-content/plugins/buddypress-group-email-subscription/css/bp-activity-subscription-css.css'
		],
		dest: 'wp-content/css/root-blog-styles.css'
	    }
        }
    });
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.registerTask('default', ['concat']);
};
