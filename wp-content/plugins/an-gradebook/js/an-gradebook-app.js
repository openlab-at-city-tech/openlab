angb_require_config = {
    //By default load any module IDs from js/lib
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
    }
};

require.config(angb_require_config);

require(['jquery','app/router/GradeBookRouter','bootstrap'],
	function($,GradeBookRouter,bootstrap){       
    	$.fn.serializeObject = function() {
        	var o = {};
        	var a = this.serializeArray();
        $.each(a, function() {
            if (o[this.name] !== undefined) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    }    
    var App = new GradeBookRouter();
});

