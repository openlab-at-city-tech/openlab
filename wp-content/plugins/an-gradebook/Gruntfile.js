module.exports = function(grunt){
	grunt.initConfig({
		requirejs: {
		  compile: {
		    options: {
			    baseUrl: './js',
			    paths : {
			    	'models' : 'app/models',
			    	'views' : 'app/views',
			    	'jquery' : 'lib/jquery/jquery.min',
					'jquery-ui' : 'lib/jquery-ui/jquery-ui.min',    	
			    	'backbone': 'lib/backbone/backbone-min',
			    	'underscore': 'lib/underscore/underscore-min',
			    	'bootstrap': 'lib/bootstrap/js/bootstrap.min',
			    	'chart': 'lib/chart/chart.min',
			    	'bootstrap3-typeahead': 'lib/bootstrap3-typeahead/bootstrap3-typeahead.min' 	
			    },
				shim: {
					'bootstrap':{
						deps: ['jquery']
					}
			    },    
			    include: 'an-gradebook-app',
			    out: './js/an-gradebook-app-min.js'
		    }
		  }
		}
	});
	
	grunt.loadNpmTasks('grunt-contrib-requirejs');
}