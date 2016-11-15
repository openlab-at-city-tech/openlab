define(['jquery','backbone','underscore'],
function($,Backbone,_){
	var CellView = Backbone.View.extend({
        tagName: 'td',
        className: 'cell',
        events: {
            "blur": "edit",
            "keypress": "updateOnEnter"
        },
        initialize: function(options) {  
            this.course = options.course;  
            this.gradebook = options.gradebook;	                     
            this.listenTo(this.gradebook.assignments, 'change:hover', this.hoverCell);            
            this.listenTo(this.gradebook.assignments, 'change:assign_order', this.shiftCell);                
            this.listenTo(this.gradebook.assignments, 'change:visibility', this.visibilityCell);                                                                                                 
        },
        render: function() {
        	var self = this;
        	if(this.gradebook.role === 'instructor'){
        		this.$el.attr('contenteditable','true');
        	} else {
        		this.$el.css('cursor','default');
        	}
        	var _assignment = this.gradebook.assignments.findWhere({id : this.model.get('amid')});
            if(_assignment){
            	this.$el.toggleClass('hidden', !_assignment.get('visibility'));           
            }
            var template = _.template($('#edit-cell-template').html());
            var compiled = template({cell: this.model});
            this.$el.html(compiled);
            return this.el;
        },         
        shiftCell: function(ev){
        	this.remove();         
        	if(ev.get('id') === this.model.get('amid')){    	
				this.model.set({assign_order: parseInt(ev.get('assign_order'))});   
        	}
        },
        updateOnEnter: function(e) {
            if (e.keyCode == 13){                    
            	this.$el.blur();      	              	
            }
        },
        hideInput: function(value) { 
            var self = this;               
            if(parseFloat(value) != this.model.get('assign_points_earned')){
            	this.model.save({assign_points_earned: parseFloat(value)},{wait: true, success: function(model,response){					            	
            		self.render();
            	}});
            } else {
            	this.$el.attr('contenteditable','true');
            }
        },
        edit: function() {
        	this.$el.attr('contenteditable','false');
        	this.hideInput(this.$el.html().trim());
        },
        hoverCell: function(ev) {
            if (this.model.get('amid') === ev.get('id')) {
                this.model.set({
                    hover: ev.get('hover')
                });
                this.$el.toggleClass('hover',ev.get('hover'));                
 			} 
        },      
        visibilityCell: function(ev) {
            if (this.model.get('amid') === ev.get('id')) {
                this.model.set({
                    visibility: ev.get('visibility')
                });
                this.render();
 			}
        },
        close: function(ev) {
        	this.remove();
        }             
    });
	return CellView
});