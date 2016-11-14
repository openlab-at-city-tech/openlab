define(['backbone'],function(Backbone){ 
	var Course = Backbone.Model.extend({
        defaults: {
            name: 'Calculus I',
            school: 'Bergen',
            semester: 'Fall',
            year: '2014'
        },
        fetchCourse: function(){  
            var self = this;
            var promise = new Promise(function(resolve,reject){          
                if(self.url()){
                    self.fetch({success: function(){
                        resolve(self);
                    }});
                } else {                    
                    self.set(self.parse({"id":"1","name":"Calculus I","school":"Bergen Community College","semester":"Fall","year":"2010"}));                 
                    resolve(self);
                }                
            });  
            return promise;   
        },
        parse: function(response){
            response.id = parseInt(response.id);
            response.year = parseInt(response.year);            
            return response;
        },
        url: function(){        	
        	if(this.get('id')){
        		return ajaxurl + '?action=course&id='+this.get('id');
        	} else{
        		return ajaxurl + '?action=course';
        	}
        },
        export2csv: function(){
        	window.location.assign(ajaxurl + '?action=get_csv&id='+this.get('id'));
        }
	});
	return Course;
});