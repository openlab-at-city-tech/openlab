define(['jquery','backbone','underscore'],
function($,Backbone,_){
	var DetailsAssignmentView = Backbone.View.extend({
 		id: 'base-modal',
    	className: 'modal fade',
        initialize: function(options){   
			this.options = options.options; 
			this.course = options.course;            	  	         
            $('body').append(this.render().el);     	
            return this;
        },          
        render: function(options) {
            var self = this;
            var assignment = this.model;
            var template = _.template($('#details-assignment-template').html());
            var compiled = template({assignment: assignment});
            self.$el.html(compiled);                 
			this.$el.modal('show');   			    		              
            return this;
        }
    });
  	return DetailsAssignmentView;
});