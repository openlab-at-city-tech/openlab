define(['jquery','backbone','underscore','views/AssignmentStatisticsView','views/EditAssignmentView', 'views/DetailsAssignmentView','jquery-ui'],
function($,Backbone,_,AssignmentStatisticsView, EditAssignmentView, DetailsAssignmentView){
	var AssignmentView = Backbone.View.extend({
        tagName: 'th',
        className: 'assignment-tools assignment',
        events: {
            'click li.assign-submenu-sort': 'sortColumn',
            'click .dashicons-menu': 'toggleAssignmentMenu',
            'click li.assign-submenu-details': 'detailsAssignment',
            'click li.assign-submenu-delete' : 'deleteAssignment',
            'click li.assign-submenu-edit' : 'editAssignment',         
            'click li.assign-submenu-left' : 'shiftAssignmentLeft',              
            'click li.assign-submenu-right' : 'shiftAssignmentRight',                         
            'click li.assign-submenu-right' : 'shiftAssignmentRight',               
            'click li.assign-submenu-stats' : 'statsAssignment',            
            'mouseenter div.column-frame' : 'mouseEnter',
            'mouseleave div.column-frame' : 'mouseLeave'
        },
        initialize: function(options) {
			this.gradebook = options.gradebook; 
			this.course = options.course;     	            
            this.assignment = this.model;
			this.listenTo(this.assignment, 'change:assign_name', this.render);         
            this.listenTo(this.assignment, 'change:sorted', this.sortColumnCSS);
            this.listenTo(this.assignment, 'change:visibility', this.visibilityColumnCSS);   
            this.listenTo(this.gradebook.students, 'add remove', this.close);      
            this.listenTo(this.gradebook.assignments, 'add remove change:sorted change:assign_order', this.close);                                            
           	this.render();            	                       
        },
        mouseEnter: function(){
        	this.$el.addClass('hover');
        	this.assignment.set({hover: true});
        },
        mouseLeave: function(){
        	this.$el.removeClass('hover');	
        	this.assignment.set({hover: false});	
        }, 
        detailsAssignment: function(ev){
        	ev.preventDefault();
            var view = new DetailsAssignmentView({model: this.assignment, course: this.course});
        },    
        shiftAssignmentLeft: function(ev){
        	ev.preventDefault();          
        	var x = this.gradebook.assignments.findWhere({assign_order: this.model.get('assign_order')-1});
        	x.save({assign_order: this.model.get('assign_order'), assign_visibility_options: x.get('assign_visibility')});
			this.assignment.save({assign_order: this.model.get('assign_order')-1, assign_visibility_options: this.model.get('assign_visibility')});
        },	
        shiftAssignmentRight: function(ev){
        	ev.preventDefault();          
        	var x = this.gradebook.assignments.findWhere({assign_order: this.model.get('assign_order')+1});            
        	x.save({assign_order: this.model.get('assign_order'), assign_visibility_options: x.get('assign_visibility')});
			this.assignment.save({assign_order: this.model.get('assign_order')+1, assign_visibility_options: this.model.get('assign_visibility')});
        },        
        toggleAssignmentMenu: function(){
        	var _assign_menu = $('#column-assign-id-'+this.model.get('id'));
        	if( _assign_menu.css('display') === 'none'){
        		var view = this;
				_assign_menu.toggle(1, function(){
        			var self = this;				
					$(document).one('click',function(){
						$(self).hide();
						view.model.set({hover:false}); 
					});		
				});
			}
        },
        render: function() {  
            this.visibilityColumnCSS();             
        	var order = this.assignment.get('sorted') === 'asc' ? 'down' : 'up';
			var template = _.template($('#assignment-view-template').html());
			var compiled = template({
                    assignment: this.assignment,
                    role: this.role,
                    min: _.min(this.gradebook.assignments.models, function(assignment){ return assignment.get('assign_order');}),
                    max: _.max(this.gradebook.assignments.models, function(assignment){ return assignment.get('assign_order');})                
                });
        	this.$el.html(compiled);         	
        	return this.el;
        },
        sortColumn: function(ev){
        	ev.preventDefault();  
            if (this.assignment.get('sorted')) {
            	if (this.assignment.get('sorted') === 'desc') {
                	this.assignment.set({sorted: 'asc'});
            	} else {
                	this.assignment.set({sorted: 'desc'});            
            	}
            } else {
                var x = this.gradebook.assignments.find(function(model){
                	return model.get('sorted').length >0;
                });
                x && x.set({ sorted: '' });
                this.assignment.set({ sorted: 'asc' });
            }        	
        },
        sortColumnCSS: function() {
            if (this.assignment.get('sorted')) {
        		var desc = this.$el.hasClass('desc');
        		this.$el.toggleClass( "desc", !desc ).toggleClass( "asc", desc );   
            } else {
                this.$el.removeClass('asc desc');
                this.$el.addClass('asc');			
            }
        },              
        visibilityColumnCSS: function(ev) {
            if (this.assignment.get('visibility')) {
                this.$el.removeClass('hidden');
            } else {
                this.$el.addClass('hidden');
            }
        },
        statsAssignment: function(ev){
        	ev.preventDefault();  
            var view = new AssignmentStatisticsView({model: this.assignment, options: this.options});           
        }, 
        editAssignment: function(ev) {
        	ev.preventDefault();                	      
            var view = new EditAssignmentView({model: this.assignment, gradebook: this.gradebook, course: this.course});           
        },               
        deleteAssignment: function(ev) {
			ev.preventDefault();    
			var self = this;      
			this.assignment.destroy({success: 
				function (model){
					var _cells = self.gradebook.cells.where({amid: model.get('id')});					
					self.gradebook.cells.remove(_cells);									
	            	var _x = model.get('assign_order'); 	            
	            	if(self.gradebook.assignments.models.length){
						var _y = _.max(self.gradebook.assignments.models, function(assignment){ return assignment.get('assign_order');});                     			            	
	            		for( i = _x; i < _y.get('assign_order'); i++){
	            			var _z = self.gradebook.assignments.findWhere({assign_order: i+1});
	            			_z.save({assign_order: i});
	            		}
	            	}	            	
        		}}
            );        
        },
        close: function(ev){  
			this.remove();			
        }                     
    });
	return AssignmentView;
});