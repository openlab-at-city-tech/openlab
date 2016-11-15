define(['jquery','backbone','underscore', 'views/EditCourseView'],
function($,Backbone,_, EditCourseView){
	var CourseView = Backbone.View.extend(
	/** @lends CourseView.prototype */
	{
	  /**
	   * @class CourseView class description
	   *
	   * @augments Backbone.View
	   * @constructs
	   */
        initialize: function(options) {
			this.options = options.options;   
            this.course = this.model;  
            console.log(this.course);
            this.courseList = this.collection;
            this.listenTo(this.model, 'change', this.render);	
        },	
        /** @constant {string} */   	
		tagName: 'tr',
        events: {
            'click li.course-submenu-delete' : 'deleteCourse',
            'click li.course-submenu-export2csv' : 'exportToCSV',            
            'click li.course-submenu-edit': 'editCourse'            
        },
        /** Exports course data to CSV. */        
        exportToCSV: function(ev){
        	ev.preventDefault();
        	this.model.export2csv();
        },   
        /** Delete course. */             
        deleteCourse: function(ev) {
        	ev.preventDefault();
        	this.model.set({selected: false});
        	this.model.destroy(); 
        },    
        /** Edit course. */              
        editCourse: function() {
            var view = new EditCourseView({model: this.course, collection: this.courseList, options: this.options});
            return false;
        },    
        /** Render the course view. */                  
        render: function() {
        	var self = this;
            var template = _.template($('#course-view-template').html());
            var compiled = template({course : this.course}); 
            return this.$el.html(compiled)    	        
           
        },
        /** Remove view and subviews. */
        close: function() {
        	this.remove();
        },        
    });
    return CourseView;
});
    