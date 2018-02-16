define(['jquery', 'backbone', 'underscore'],
        function ($, Backbone, _) {
            var DeleteGradebookView = Backbone.View.extend({
                id: 'base-modal',
                className: 'modal fade',
                events: {
                    'hidden.bs.modal': 'deleteCancel',
                    'keyup': 'keyPressHandler',
                    'click #delete-gradebook-delete': 'submitForm',
                    'submit #delete-gradebook-form': 'deleteSave'
                },
                initialize: function (options) {
                    this.gradebook = options.gradebook;
                    this.course = options.course;
                    $('body').append(this.render().el);
                    return this;
                },
                render: function () {
                    var self = this;
                    if (this.course) {
                        var template = _.template($('#delete-gradebook-template').html());
                        var compiled = template({course: this.course});
                        this.$el.html(compiled);
                    }
                    this.$el.modal('show');
                    return this;
                },
                keyPressHandler: function (e) {
                    if (e.keyCode == 27)
                        this.deleteCancel();
                    if (e.keyCode == 13)
                        this.submitForm();
                    return this;
                },
                deleteCancel: function () {
                    this.$el.data('modal', null);
                    this.remove();
                    return false;
                },
                submitForm: function () {
                    $('#delete-gradebook-form').submit();
                },
                deleteSave: function (ev) {
                    ev.preventDefault();
                    this.model.set({selected: false});
                    this.model.destroy();
                }
            });
            return DeleteGradebookView;
        });