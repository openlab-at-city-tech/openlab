define(['backbone','models/Assignment'],function(Backbone,Assignment){ 
	var Assignments = Backbone.Collection.extend({
        model: Assignment
    });
    return Assignments;
});