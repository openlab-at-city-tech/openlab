define(['jquery', 'backbone', 'underscore', 'models/letterGrades'],
        function ($, Backbone, _, letterGrades) {

            var CellDropdown = Backbone.View.extend({
                tagName: 'td',
                className: 'cell',
                events: {
                    'change select.grade-selector': 'edit'
                },
                initialize: function (options) {
                    this.course = options.course;
                    this.gradebook = options.gradebook;
                    this.listenTo(this.gradebook.assignments, 'change:hover', this.hoverCell);
                    this.listenTo(this.gradebook.assignments, 'change:assign_order', this.shiftCell);
                    this.listenTo(this.gradebook.assignments, 'change:visibility', this.visibilityCell);
                    
                    //letter grades borrowed from https://sps.cuny.edu/about/dean/policies/academic-and-student-policies/grading-policies-undergraduate
                    this.thisLetterGrades = new letterGrades([
                        {
                            label: '--',
                            value: '--',
                            range_low: 0,
                            range_high: 0
                        },
                        {
                            label: 'A+',
                            value: 100,
                            range_low: 100,
                            range_high: 101
                        },
                        {
                            label: 'A',
                            value: 96,
                            range_low: 93,
                            range_high: 100
                        },
                        {
                            label: 'A-',
                            value: 91.5,
                            range_low: 90,
                            range_high: 93
                        },
                        {
                            label: 'B+',
                            value: 88.5,
                            range_low: 87,
                            range_high: 90
                        },
                        {
                            label: 'B',
                            value: 85,
                            range_low: 83,
                            range_high: 87
                        },
                        {
                            label: 'B-',
                            value: 81.5,
                            range_low: 80,
                            range_high: 83
                        },
                        {
                            label: 'C+',
                            value: 78.5,
                            range_low: 77,
                            range_high: 80
                        },
                        {
                            label: 'C',
                            value: 75,
                            range_low: 73,
                            range_high: 77
                        },
                        {
                            label: 'C-',
                            value: 71.5,
                            range_low: 70,
                            range_high: 73
                        },
                        {
                            label: 'D+',
                            value: 68.5,
                            range_low: 67,
                            range_high: 70
                        },
                        {
                            label: 'D',
                            value: 65,
                            range_low: 63,
                            range_high: 67
                        },
                        {
                            label: 'D-',
                            value: 61.5,
                            range_low: 60,
                            range_high: 63
                        },
                        {
                            label: 'F',
                            value: 50,
                            range_low: 1,
                            range_high: 60
                        }
                    ]);

                },
                render: function () {
                    var self = this;

                    this.$el.attr('data-id', this.model.get('amid'));

                    var _assignment = this.gradebook.assignments.findWhere({id: this.model.get('amid')});

                    if (_assignment) {
                        this.$el.toggleClass('hidden', !_assignment.get('visibility'));
                    }
                    var template = _.template($('#edit-cell-dropdown-template').html());
                    
                    var compiled = template({cell: this.model, assignment: _assignment, grades: this.thisLetterGrades, role: this.gradebook.role});
                    this.$el.html(compiled);
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
                    this.model.save({assign_points_earned: value}, {wait: true, success: function (model, response) {
                            self.render();
                            Backbone.pubSub.trigger('updateAverageGrade', response );
                        }});
                },
                edit: function () {
                    this.$el.attr('contenteditable', 'false');
                    this.$el.find('.grade-selector').attr('disabled', 'disabled');
                    this.hideInput(this.$el.find('.grade-selector').val());
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
            return CellDropdown;
        });