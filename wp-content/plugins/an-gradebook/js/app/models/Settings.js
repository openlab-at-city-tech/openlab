define(['backbone','underscore'],function(Backbone,_){ 
	var Settings = Backbone.Model.extend({
        defaults : {
    		administrator: true,
    		editor: false,
        	contributor: false,
        	author: false,
    		subscriber: false
    	},	
        fetchSettings: function(){
            var self = this;
            var promise = new Promise(function(resolve,reject){
                if(self.url()){
                    self.fetch({success:function(){
                        resolve(self);
                    }})
                } else {
                    self.set(self.parse({"gradebook_administrators":{"editor":"true","author":"true","administrator":true}}));
                    resolve(self);
                }
            });
            return promise;          
        },
        url: function(){
        	if(this.get('action')!=='save'){
        		return ajaxurl + '?action=an_gradebook_get_settings';
        	} else {
        		return ajaxurl + '?action=an_gradebook_set_settings';        		
        	}
        },
        parse: function(response){
        	return response.gradebook_administrators;
        }
	});
	return Settings;
});
