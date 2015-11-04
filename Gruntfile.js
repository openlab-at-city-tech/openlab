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
        },
    });
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.registerTask('default', ['concat']);
};
