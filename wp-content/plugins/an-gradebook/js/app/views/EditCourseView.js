define(['jquery','backbone','underscore','models/User','models/Course','bootstrap'],
function($,Backbone,_, User, Course){
	EditCourseView = Backbone.View.extend({
 		id: 'base-modal',
    	className: 'modal fade',
        events: {
            'hidden.bs.modal' : 'editCancel',
            'keyup'  : 'keyPressHandler',        
            'click #edit-course-save': 'submitForm',
            'submit #edit-course-form': 'editSave'
        },
        initialize: function(options){  
			//this.options = options.options;
            //	_(this).extend(this.options.gradebook_state);     
            this.course = this.model || null; 
            this.courseList = this.collection;               	        
            $('body').append(this.render().el);
            return this;                
        },
        render: function() {
            var self = this;  
            var template = _.template($('#edit-course-template').html());
            var compiled = template({course: this.course});
            self.$el.html(compiled);                                                      
            this.$el.modal('show');
			_.defer(function(){
				this.inputName = self.$('input[name="name"]');
				var strLength= inputName.val().length;
				//inputName.focus();				
				//inputName[0].setSelectionRange(strLength, strLength);
			});            
            return this;
        },              
        keyPressHandler: function(e) {
        	var self = this;
			switch(e.keyCode){
				case 27: 
					self.$el.modal('hide');  
					break;
				case 13: 		
					self.submitForm();
					break;
			}					
            return this;
        },                 
        editCancel: function() {
			this.$el.data('modal', null);        
            this.remove();                                      
            return false;
        },
        submitForm: function(){
        	$('#edit-course-form').submit();
        },
        editSave: function(ev) {
        	var self = this;
            var courseInformation = $(ev.currentTarget).serializeObject(); //action: "add_course" or action: "update_course" is hidden in the edit-course-template 
        	if(this.course){            
	        	this.model.save(courseInformation,{wait: true});
				this.$el.modal('hide');         
			} else {
				delete(courseInformation['id']);
            	var toadds = new Course(courseInformation);
            	toadds.save(courseInformation,{success: function(model){
            		 var _user = new User(model.get('user'));
            		 //self.roles.add(_user);
            		 var _course = new Course(model.get('course'));            		 
            		 self.courseList.add(_course);              		 
					 self.$el.modal('hide');                		 
            		}
            	});            				
			}
            return false;
        }
    });
	return EditCourseView;
});
     
    