define(['jquery','backbone','underscore','views/StatisticsView','views/EditStudentView','views/DeleteStudentView','views/CellView'],
function($,Backbone,_,StatisticsView,EditStudentView,DeleteStudentView, CellView){
	var StudentView = Backbone.View.extend(
	/** @lends StudentView.prototype */	
	{	
        tagName: 'tr',
        events: {
            'click a.edit-student': 'editStudent',
            'click a.delete-student': 'deleteStudent',
            'click a.student-statistics': 'studentStatistics', 
            'click .dashicons-menu': 'toggleStudentMenu',
            'click li.student-submenu-delete' : 'deleteStudent',
            'click li.student-submenu-edit' : 'editStudent',         
            'click li.student-submenu-stats' : 'studentStatistics',                         
        },
        /** @constructs */        
        initialize: function(options) {
        	var self = this;
			this._subviews =[];          	
			this.gradebook = options.gradebook;
			this.course = options.course;
           	this.student = this.model;	                     
           	this.listenTo(this.model, 'change', this.render);            
        },    
        render: function() {
			var self = this;
            var template = _.template($('#student-view-template').html()); 
            var compiled = template({student: this.model, role: this.role});
            this.$el.html(compiled);     
            var gbid = parseInt(self.course.get('id')); //anq: why is this not already an integer??
            var x = this.gradebook.cells.where({
            	uid: parseInt(this.model.get('id')),		//anq: why is this not already an integer??
            	gbid: gbid
            	});
           	x = _.sortBy(x,function(model){ return model.get('assign_order');});        	
            var self = this;
            _.each(x, function(cell) {
                var view = new CellView({course: self.course, gradebook: self.gradebook, model: cell, options: self.options});
                self._subviews.push(view);
                self.$el.append(view.render());
            });
            return this.el;
        },
  		clearSubViews : function(){
  			var self = this;
		  	_.each(self._subviews,function(view){
		  	   view.close();
		  	});
		  	this._subviews = [];    			 
  		},        
        toggleStudentMenu: function(){
        	var _student_menu = $('#row-student-id-'+this.model.get('id'));
        	if( _student_menu.css('display') === 'none'){
        		var view = this;
				_student_menu.toggle(1, function(){
        			var self = this;				
					$(document).one('click',function(){
						$(self).hide();
						//view.model.set({hover:false}); 
					});		
				});
			}
        },        
        selectAllStudents: function(){
        	var _selected = $('#cb-select-all-1').is(':checked');        
        	if(_selected){
				$('#cb-select-'+this.model.get('id')).prop('checked',true);
			} else {
				$('#cb-select-'+this.model.get('id')).prop('checked',false);
			}
        },
        selectStudent: function(ev) {
        	var _selected = $('#cb-select-'+this.model.get('id')).is(':checked');
        	this.model.set({selected: _selected})      	
            var x = AN.GlobalVars.assignments.findWhere({
                selected: true
            });
        	if(_selected){
				$('#cb-select-'+this.model.get('id')).prop('checked',true);
			} else {
				$('#cb-select-'+this.model.get('id')).prop('checked',false);
			}            
            x && x.set({
                selected: false
            });
        },
        studentStatistics: function(ev){
        	ev.preventDefault();    
            var view = new StatisticsView({model: this.model, options: this.options});          
        },
        editStudent: function(ev){        	
        	ev.preventDefault();    
            var view = new EditStudentView({model: this.model, course: this.course, options: this.options});             	
        },
        deleteStudent: function(ev){
        	ev.preventDefault();
        	var view = new DeleteStudentView({model: this.model, gradebook: this.gradebook, course: this.course});              	      	
        },   
        /** removes view and any subviews */            
        close: function(ev) {        	
        	this.clearSubViews();        	
			this.remove();
        }
    });
	return StudentView;
});