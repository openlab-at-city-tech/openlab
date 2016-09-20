define(['backbone'],function(Backbone){ 
	var Config = Backbone.Model.extend({
        url: function(){
        		return ajaxurl + '?action=get_gradebook_config';
        }
	});
	return Config;
});
