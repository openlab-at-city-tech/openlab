define(['backbone'], function (Backbone) {
    var letterGrade = Backbone.Model.extend({
        defaults: {
            label: 'A+',
            value: 100
        }
    });
    return letterGrade;
});