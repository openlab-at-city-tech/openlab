define(['jquery','backbone','underscore','chart'],
function($,Backbone,_,Chart){
	var AssignmentStatisticsView = Backbone.View.extend({
 		id: 'base-modal',
    	className: 'modal fade',
        events: { 
            'hidden.bs.modal' : 'editCancel',  
            'shown.bs.modal' : 'displayChart',         
			'keyup'  : 'keyPressHandler'
        },		
		initialize: function(){		
			this.render();	 	  			     			 		   
		},		
		displayChart: function(){
			var self = this;
			$.ajax({
				url: ajaxurl,
				data: {
					action: 'angb_statistics',
					chart_type: 'pie_chart',
					gbid: this.model.get('gbid'),
					amid: this.model.get('id')
				},
				dataType: 'json',
				success: function(data){
					var ctx = $('#myChart').get(0).getContext("2d");
					var myNewChart = new Chart(ctx).Pie(data);
					$('.labeled-chart-container').append(myNewChart.generateLegend());
				}				
			});					
		},
        render: function(data) {	        
            var self = this;
            var assignment = this.model;
            var template = _.template($('#stats-assignment-template').html());
            var compiled = template({assignment: assignment});
            $('body').append(self.$el.html(compiled).el);   
			this.$el.modal('show'); 			                                                                                              					
        },
        editCancel: function() {
			this.$el.data('modal', null);         
            this.remove();            
            return false;
        },
 		keyPressHandler: function(e) {
            if (e.keyCode == 27) this.editCancel();
            return this;
        }        
    });
	return AssignmentStatisticsView;
});