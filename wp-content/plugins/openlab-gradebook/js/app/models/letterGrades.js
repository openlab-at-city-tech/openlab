define(['backbone', 'models/letterGrade'], function (Backbone, letterGrade) {
    var letterGrades = Backbone.Collection.extend({
        model: letterGrade
    });
    
    return letterGrades;
});