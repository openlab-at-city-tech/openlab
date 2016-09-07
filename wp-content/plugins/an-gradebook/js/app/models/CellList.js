define(['backbone','models/Cell'],function(Backbone,Cell){ 
    var Cells = Backbone.Collection.extend({
        model: Cell     
    });   
    return Cells;
});
