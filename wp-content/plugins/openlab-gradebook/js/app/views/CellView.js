define(['jquery', 'backbone', 'underscore', "views/CommentView"],
        function ($, Backbone, _, CommentView) {
            var CellView = Backbone.View.extend({
                tagName: 'td',
                className: 'cell',
                events: {
                    "blur .grade-numeric": "edit",
                    "keypress .grade-numeric": "updateOnEnter"
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
                    var template = _.template($('#edit-cell-template').html());
                    var compiled = template({cell: this.model, gradebook: this.gradebook});
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

                    return this.$el;
                },
                shiftCell: function (ev) {
                    this.remove();
                    if (ev.get('id') === this.model.get('amid')) {
                        this.model.set({assign_order: parseInt(ev.get('assign_order'))});
                    }
                },
                updateOnEnter: function (e) {
                    if (e.keyCode == 13) {
                        this.$el.find('.grade-numeric').blur();
                    }
                },
                hideInput: function (value) {
                    var self = this;
                    if ((parseFloat(value) != this.model.get('assign_points_earned')) || (parseInt(value) === 0 && (this.model.get('is_null') || this.model.get('is_null') === 1))) {
                        this.$el.find('.grade-numeric').attr('contenteditable', 'false').css('opacity', '0.42');
                        this.model.save({assign_points_earned: parseFloat(value)}, {wait: true, success: function (model, response) {
                                self.render();
                                Backbone.pubSub.trigger('updateAverageGrade', response );
                            }});
                    } else {
                        this.$el.find('.grade-numeric').attr('contenteditable', 'true');
                    }
                },
                edit: function () {
                    this.hideInput(this.$el.find('.grade-numeric').html().trim());
                },
                hoverCell: function (ev) {
                    if (this.model.get('amid') === ev.get('id')) {
                        this.model.set({
                            hover: ev.get('hover')
                        });
                        this.$el.find('.grade-numeric').toggleClass('hover', ev.get('hover'));
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
            return CellView
        });