define(['jquery','backbone','underscore', 'views/StudentView', 'views/AssignmentView', 'views/EditStudentView', 
	'views/EditAssignmentView'],
function($, Backbone, _, StudentView, AssignmentView, EditStudentView, EditAssignmentView){
      var GradebookView = Backbone.View.extend({
        initialize: function(options) {
            var self = this;  
            var _request = 0; 
			this.xhrs = [];
			this._subviews =[];            
            this.options = options;
            this.filter_option = "-1";	
			this.course = options.course;	
            console.log(this.course);				
			this.gradebook = options.gradebook;						
            console.log(this.gradebook);
        	this.listenTo(self.gradebook.students, 'add remove', self.render);                                  
			this.listenTo(self.gradebook.cells, 'add remove change:assign_order', self.render);                      
			this.listenTo(self.gradebook.assignments, 'add remove change:assign_order change:assign_category', self.render);                                   
			this.listenTo(self.gradebook.assignments, 'change:sorted', self.sortByAssignment); 
			this.render();            					
            return this;
        },
  		clearSubViews : function(){
  			var self = this;
		  	_.each(self._subviews,function(view){
		  	   view.close();
		  	});
		  	this._subviews = [];    			 
  		},	        
        events: {
            'click button#add-student': 'addStudent',
            'click button#add-assignment': 'addAssignment',
            'click button#filter-assignments': 'filterAssignments',
            'click [class^=gradebook-student-column-]' : 'sortGradebookBy',       
        },
        render: function() {
        	var self = this;
        	this.clearSubViews();
        	var course = this.course;
        	var _x = _.map(self.gradebook.assignments.models, function(model){return model.get('assign_category');});
         	var _assign_categories = _.without(_.uniq(_x),"") || null;                                                       
            var template = _.template($('#gradebook-interface-template').html());
            var compiled = template({course : self.course, assign_categories: _assign_categories, role: this.role});     
           	$('#wpbody-content').append(self.$el.html(compiled));
            $('#filter-assignments-select').val(this.filter_option);                 	
        	switch(this.gradebook.sort_key){
        		case 'cell':    
        			_.each(this.sort_column, function(cell) {
                		var view = new StudentView({
                    		model: self.gradebook.students.get(cell.get('uid')), course: self.course, gradebook: self.gradebook, options: self.options
                		}); 
                		self._subviews.push(view);
						$('#students').append(view.render());                     
            		});                          		
            		var y = self.gradebook.assignments.models;
            		y = _.sortBy(y,function(assign){ return assign.get('assign_order');});
            		_.each(y, function(assignment) {
                		var view = new AssignmentView({
                    		model: assignment, course: self.course, gradebook: self.gradebook
                	});
                		self._subviews.push(view);
                		$('#students-header tr').append(view.render());
            		});
            		break;
            	case 'student':     
            		_.each(this.gradebook.sort_column.models, function(student) { 
                		var view = new StudentView({model: student, course: self.course, gradebook: self.gradebook, options: self.options});
                		self._subviews.push(view);                		
						$('#students').append(view.render());              		                 
            		});             		            		  
            		var y = self.gradebook.assignments;
            		y = _.sortBy(y.models,function(assign){ return assign.get('assign_order');});  
            		_.each(y, function(assignment) {
                		var view = new AssignmentView({model: assignment, course: self.course, gradebook: self.gradebook});
                		self._subviews.push(view);                		
                		$('#students-header tr').append(view.render());
            		}); 
            		break;             	
            }    
            this.filterAssignments();                                                 
            return this;
        },   
        filterAssignments: function() {       
        	var _x = $('#filter-assignments-select').val();	
        	this.filter_option = _x;
            var _toHide = this.gradebook.assignments.filter(
	            function(assign){
               		return assign.get('assign_category') != _x;
            	}
        	);
            var _toShow = this.gradebook.assignments.filter(
	            function(assign){
               		return assign.get('assign_category') === _x;
            	}
        	);  
        	if( _x === "-1"){
        		this.gradebook.assignments.each(function(assign){
                	assign.set({visibility: true});
				});
        	} else {      	
        		_.each(_toHide,function(assign){
                	assign.set({visibility: false});
            	});
            	_.each(_toShow,function(assign){
                	assign.set({visibility: true});
            	});
            }        
        },     
        addAssignment: function(ev) {    
            var view = new EditAssignmentView({course: this.course, gradebook: this.gradebook});           
        },    
        addStudent: function(ev) {  
            var view = new EditStudentView({course: this.course, gradebook: this.gradebook}); 
            $('body').append(view.render());                     
        }, 
        checkStudentSortDirection: function(){
        	if( this.gradebook.students.sort_direction === 'asc' ){
        		this.gradebook.students.sort_direction = 'desc';
        	} else {
        		this.gradebook.students.sort_direction = 'asc';
        	}
        },
        sortGradebookBy: function(ev){
	    	var column = ev.target.className.replace('gradebook-student-column-','');    
			this.gradebook.sort_key = 'student';          			
			this.gradebook.students.sort_key = column;			
			this.checkStudentSortDirection();
			this.gradebook.students.sort();
			this.render();
        },                   
        sortByAssignment: function(ev) {
            var x = this.gradebook.cells.where({amid: parseInt(ev.get('id'))});       			
			this.sort_column = _.sortBy(x,function(cell){
				if (ev.get('sorted')==='asc'){
					return cell.get('assign_points_earned');
				} else {
					return -1*cell.get('assign_points_earned');
				}
			});             		
			this.gradebook.sort_key = 'cell';                                                
           	this.render();                          
        },
        close: function() {
        	this.clearSubViews();	 		 			
		    _.map(this.xhrs,function(xhr){ xhr.abort()});      			
 		  	this.remove();        	
        }        
    });
	return GradebookView;
	});