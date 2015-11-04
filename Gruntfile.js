module.exports = function (grunt) {
    require('jit-grunt')(grunt);
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        concat: {
            options: {
                separator: ';'
            },
            buddypress: {
                src: [
			'wp-content/plugins/buddypress/bp-core/js/confirm.min.js',
			'wp-content/plugins/buddypress/bp-core/js/widget-members.min.js',
			'wp-content/plugins/buddypress/bp-core/js/jquery-query.min.js',
			'wp-content/plugins/buddypress/bp-core/js/jquery-cookie.min.js',
			'wp-content/plugins/buddypress/bp-core/js/jquery-scroll-to.min.js',
		],
                dest: 'wp-content/js/buddypress.js'
            },
	    smoothscroll: {
		src: [
			'wp-content/mu-plugins/js/jquery-smooth-scroll/jquery.smooth-scroll.min.js',
			'wp-content/mu-plugins/js/jquery-custom-select/jquery.customSelect.min.js',
			'wp-content/mu-plugins/js/hyphenator/openlab.search.js',
			'wp-content/mu-plugins/js/openlab/openlab.search.js',
			'wp-content/mu-plugins/js/openlab/openlab.nav.js',
			'wp-content/mu-plugins/js/openlab/openlab.theme.fixes.js',
		],
		dest: 'wp-content/js/smoothscroll.js'
	    }
        },
    });
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.registerTask('default', ['concat']);
};
