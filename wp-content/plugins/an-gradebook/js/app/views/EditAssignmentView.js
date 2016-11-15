define(['jquery','backbone','underscore','models/Assignment','jquery-ui'],
function($,Backbone,_,Assignment){
	var EditAssignmentView = Backbone.View.extend({
 		id: 'base-modal',
    	className: 'modal fade',
        events: {
            'hidden.bs.modal' : 'editCancel',
			'keyup'  : 'keyPressHandler',    			                                   
            'click #edit-assignment-save': 'submitForm',
            'submit #edit-assignment-form': 'editSave'
        },
        initialize: function(options){  
			this.options = options.options;		
			this.gradebook = options.gradebook;
			this.course = options.course;         	                       	
           	this.assignment = this.model || null;           	
            $('body').append(this.render().el);
            $('#assign-date-datepicker, #assign-due-datepicker').datepicker();
            $('#assign-date-datepicker, #assign-due-datepicker').datepicker('option','dateFormat','yy-mm-dd');
            if(this.assignment){                  
            	$('#assign-date-datepicker').datepicker('setDate', this.assignment.get('assign_date'));                                                            
            	$('#assign-due-datepicker').datepicker('setDate', this.assignment.get('assign_due'));       		    		 
            }     	
            return this;
        },
        render: function() {
           // var gradebook = this.courses.findWhere({selected: true});            
            var template = _.template($('#edit-assignment-template').html());
            var compiled = template({assignment: this.assignment, course: this.course , role: this.role});
            this.$el.html(compiled);                 
			this.$el.modal('show');
			var self = this;
			_.defer(function(){
				this.inputName = self.$('input[name="assign_name"]');
				var strLength= inputName.val().length;
				if(self.assignment){
					$("#assign_visibility_options option[value='" + self.assignment.get('assign_visibility') + "']").attr("selected", "selected");
				}
			});    			    		              
            return this;
        },   
		keyPressHandler: function(e) {
            if (e.keyCode == 27) this.editCancel();
            if (e.keyCode == 13) this.submitForm();
            return this;
        },             
        editCancel: function() {
			this.$el.data('modal', null);           
            this.remove();
            return false;
        },
        submitForm: function(){   
          $('#edit-assignment-form').submit();
        },
        editSave: function(ev) {
            ev.preventDefault();
            var self = this;
            var assignmentInformation = $(ev.currentTarget).serializeObject(); 
			var x = $(ev.currentTarget).serializeObject().id;        
            var toadd = this.gradebook.assignments.findWhere({id : parseInt(x)});
            if(toadd){
            	toadd.save(assignmentInformation,{wait: true});          	
            } else {
             	delete(assignmentInformation['id']);
            	var toadds = new Assignment(assignmentInformation);
            	toadds.save(assignmentInformation,{success: function(model,response){
            		self.gradebook.assignments.add(response['assignment']); 
                	_.each(response['cells'], function(cell) {
                    	self.gradebook.cells.add(cell)
                	});             		 
            		}
            	});            	
            }
			this.$el.modal('hide');
            return false;
        }
    });
	return EditAssignmentView;
}); 