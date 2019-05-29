define(['backbone'], function (Backbone) {
    var Cell = Backbone.Model.extend({
        defaults: {
            uid: null,
            gbid: null,
            assign_order: null,
            amid: null,
            assign_points_earned: 0,
            selected: false,
            hover: false,
            visibility: true,
            display: false,
            comments: null,
        },
        url: function () {
            if (this.get('id')) {
                return ajaxurl + '?action=cell&id=' + this.get('id');
            } else {
                return ajaxurl + '?action=cell';
            }
        }
    });
    return Cell;
});