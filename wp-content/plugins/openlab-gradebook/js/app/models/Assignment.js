define(['backbone', 'underscore'], function (Backbone, _) {
    var Assignment = Backbone.Model.extend({
        defaults: {
            assign_category: '',
            assign_name: '',
            assign_due: '',
            assign_date: '',
            assign_grade_type: 'numeric',
            assign_weight: 0,
            gbid: null,
            sorted: '',
            visibility: true,
            publish: true,
            selected: false
        },
        url: function () {
            if (this.get('id')) {
                return ajaxurl + '?action=assignment&id=' + this.get('id') + '&gbid=' + this.get('gbid');
            } else {
                return ajaxurl + '?action=assignment';
            }
        }
    });
    return Assignment;
});