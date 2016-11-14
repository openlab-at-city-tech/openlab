define(['jquery','backbone','underscore','models/User','models/UserList','bootstrap3-typeahead'],
function($,Backbone,_,User,UserList,typeahead){
	var EditStudentView = Backbone.View.extend({
 		id: 'base-modal',
    	className: 'modal fade',
        events: {
            'hidden.bs.modal' : 'editCancel',
			'keyup'  : 'keyPressHandler',                          
            'click #edit-student-save': 'submitForm',            
            'submit #edit-student-form': 'editSave',      
            'input #user_login': 'loginSearch'            
        },
        initialize: function(options){  
			this.course = options.course;
			this.gradebook = options.gradebook;  
			this.minLength = 2; 
            this.student = this.model || null;
            if(!this.student){
            	this.userList = new UserList();
            }       	           
            $('body').append(this.render().el);     	
            return this;
        },        
        render: function() {
		    var self = this;     
            var template = _.template($('#edit-student-template').html());
            var compiled = template({student: this.student, course: this.course });                
            self.$el.html(compiled);  
            this.$el.modal('show');            				
            return self.el;
        },  
 		keyPressHandler: function(e) {
            if (e.keyCode == 27) this.editCancel();
            if (e.keyCode == 13) this.submitForm();
            return this;
        },  
        getUsersLogin: function(){
			this.availableTags = [
      			"testing"
    		];  
        },                
        editCancel: function() {
			this.$el.data('modal', null);            
            this.remove();           
            return false;
        },
        submitForm: function(){        	
          $('#edit-student-form').submit();
        },  
        loginSearch: function(){
        	var self = this;
			this.userList.search = $('#user_login').val();
			if(this.userList.search.length <2){
				$('#user_login').typeahead('destroy');			
				return false;
			}
			if(this.userList.search.length === 2){
			this.userList.fetch({success: function(){
				var users = _.map(self.userList.models, function(user){
					var filtered_user = user.get('data').user_login;
					return filtered_user;
				});
				$('#user_login').typeahead({source: users });			
			}});
			}
			return this;
        },      
        editSave: function(ev) {
        	var self = this;
            var studentInformation = $(ev.currentTarget).serializeObject();
            if(this.student){
            	studentInformation.id = parseInt(studentInformation.id);
            	this.student.save(studentInformation, {wait: true});
				this.$el.modal('hide');              	
            } else {
             	delete(studentInformation['id']);
            	var toadds = new User(studentInformation);
            	toadds.save(studentInformation,{success: function(model){
                		_.each(model.get('cells'), function(cell) {
                  	  		self.gradebook.cells.add(cell);
              			});
              			var _student = new User(model.get('student'));
                		self.gradebook.students.add(_student);                       	 
                		self.$el.modal('hide');   
            		}
            	});            	
            }            				
            return false;
        }
    });
    return EditStudentView;
});