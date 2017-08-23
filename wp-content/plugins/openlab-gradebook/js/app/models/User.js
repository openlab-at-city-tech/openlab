define(['backbone'], function (Backbone) {
    var User = Backbone.Model.extend({
        defaults: {
            first_name: 'john',
            last_name: 'doe',
            selected: false,
            user_login: null
        },
        url: function () {
            if (this.get('id')) {
                return ajaxurl + '?action=oplb_user&id=' + this.get('id') + '&gbid=' + this.get('gbid') + '&delete_options=' + this.get('delete_options');
            } else {
                return ajaxurl + '?action=oplb_user';
            }
        }
    });
    return User;
});
