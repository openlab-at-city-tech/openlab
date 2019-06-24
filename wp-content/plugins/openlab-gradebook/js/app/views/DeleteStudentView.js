define(['jquery', 'backbone', 'underscore'],
        function ($, Backbone, _) {
            var DeleteStudentView = Backbone.View.extend({
                id: 'base-modal',
                className: 'modal fade',
                events: {
                    'hidden.bs.modal': 'deleteCancel',
                    'keyup': 'keyPressHandler',
                    'click #delete-student-delete': 'submitForm',
                    'submit #delete-student-form': 'deleteSave'
                },
                initialize: function (options) {
                    this.gradebook = options.gradebook;
                    this.course = options.course;
                    this.student = this.model;
                    $('body').append(this.render().el);
                    return this;
                },
                render: function () {
                    var self = this;
                    if (this.student) {
                        var template = _.template($('#delete-student-template').html());
                        var compiled = template({student: this.student, course: this.course});
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
                    $('#delete-student-form').submit();
                },
                deleteSave: function (ev) {
                    ev.preventDefault();
                    var self = this;
                    var studentInformation = $(ev.currentTarget).serializeObject();
                    var x = studentInformation.id;
                    var todel = this.model;
                    var self = this;
                    todel.destroy({success: function (model, response) {

                            var cells = self.gradebook.cells;

                            _.each(cells.models, function (cell, index) {

                                if (typeof cell === 'undefined') {
                                    return;
                                }

                                if (cell.get('uid') === self.model.get('id')) {

                                    cells.models.splice(index, 1);

                                }

                            });

                            self.$el.modal('hide');
                        }});
                }
            });
            return DeleteStudentView;
        });