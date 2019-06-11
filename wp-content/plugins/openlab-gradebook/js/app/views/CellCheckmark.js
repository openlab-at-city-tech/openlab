define(['jquery', 'backbone', 'underscore', 'models/letterGrades', "views/CommentView"],
        function ($, Backbone, _, letterGrades, CommentView) {

            var CellCheckmark = Backbone.View.extend({
                tagName: 'td',
                className: 'cell',
                events: {
                    'change input.grade-checkmark': 'edit'
                },
                initialize: function (options) {
                    this.course = options.course;
                    this.gradebook = options.gradebook;
                    this.listenTo(this.gradebook.assignments, 'change:hover', this.hoverCell);
                    this.listenTo(this.gradebook.assignments, 'change:assign_order', this.shiftCell);
                    this.listenTo(this.gradebook.assignments, 'change:visibility', this.visibilityCell);
                },
                render: function () {
                    var self = this;

                    this.$el.attr('data-id', this.model.get('amid'));

                    var _assignment = this.gradebook.assignments.findWhere({id: this.model.get('amid')});
                    if (_assignment) {
                        this.$el.toggleClass('hidden', !_assignment.get('visibility'));
                    }
                    var template = _.template($('#edit-cell-checkmark-template').html());

                    var compiled = template({cell: this.model, assignment: _assignment, role: this.gradebook.role});
                    this.$el.html(compiled);

                    if (
                        self.gradebook.role === "instructor" ||
                        (self.gradebook.role === "student" && this.model.get('comments'))
                    ) {
                        var comment = new CommentView({
                            model: this.model,
                            gradebook: self.gradebook,
                            name: _assignment.get('assign_name'),
                            username: this.model.get('username'),
                            type: 'cell'
                        });
                        
                        this.$el
                            .find(".cell-wrapper")
                            .append(comment.render());
                    }

                    return this.el;
                },
                shiftCell: function (ev) {
                    this.remove();
                    if (ev.get('id') === this.model.get('amid')) {
                        this.model.set({assign_order: parseInt(ev.get('assign_order'))});
                    }
                },
                updateOnEnter: function (e) {
                    if (e.keyCode == 13) {
                        this.$el.blur();
                    }
                },
                hideInput: function (value) {
                    var self = this;
                    
                    this.model.save({assign_points_earned: parseFloat(value)}, {wait: true, success: function (model, response) {
                            self.render();
                            Backbone.pubSub.trigger('updateAverageGrade', response );
                        }});
                },
                edit: function () {
                    this.$el.attr('contenteditable', 'false');
                    var value = 0;

                    if (this.$el.find('.grade-checkmark').is(':checked')) {
                        value = 100;
                    }
                    
                    this.$el.find('.grade-checkmark').attr('disabled', 'disabled');
                    this.hideInput(value);
                },
                hoverCell: function (ev) {
                    if (this.model.get('amid') === ev.get('id')) {
                        this.model.set({
                            hover: ev.get('hover')
                        });
                        this.$el.toggleClass('hover', ev.get('hover'));
                    }
                },
                visibilityCell: function (ev) {
                    if (this.model.get('amid') === ev.get('id')) {
                        this.model.set({
                            visibility: ev.get('visibility')
                        });
                        this.render();
                    }
                },
                close: function (ev) {
                    this.remove();
                }
            });
            return CellCheckmark;
        });