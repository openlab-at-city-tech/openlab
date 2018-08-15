define(['jquery', 'backbone', 'underscore', 'models/letterGrades'],
        function ($, Backbone, _, letterGrades) {

            var CellMidSemester = Backbone.View.extend({
                tagName: 'td',
                className: 'cell',
                events: {
                    'change select.grade-selector': 'edit'
                },
                initialize: function (options) {
                    this.course = options.course;
                    this.gradebook = options.gradebook;
                    this.listenTo(this.gradebook.assignments, 'change:hover', this.hoverCell);
                    
                    //letter grades borrowed from https://sps.cuny.edu/about/dean/policies/academic-and-student-policies/grading-policies-undergraduate
                    this.thisLetterGrades = new letterGrades([
                        {
                            label: '--',
                            value: '--',
                        },
                        {
                            label: 'P = Passing Work',
                            value: 'passing',
                        },
                        {
                            label: 'P',
                            value: 'passing_display',
                        },
                        {
                            label: 'BL = Borderline',
                            value: 'borderline',
                        },
                        {
                            label: 'BL',
                            value: 'borderline_display',
                        },
                        {
                            label: 'U = Unsatisfactory',
                            value: 'unsatisfactory',
                        },
                        {
                            label: 'U',
                            value: 'unsatisfactory_display',
                        },
                        {
                            label: 'SA = Stopped Attending',
                            value: 'stopped_attending',
                        },
                        {
                            label: 'SA',
                            value: 'stopped_attending_display',
                        },
                    ]);

                },
                render: function () {
                    var self = this;

                    this.$el.attr('data-id', this.model.get('amid'));

                    var _assignment = this.gradebook.assignments.findWhere({id: this.model.get('amid')});
                    if (_assignment) {
                        this.$el.toggleClass('hidden', !_assignment.get('visibility'));
                    }
                    var template = _.template($('#edit-cell-dropdown-mid-final-template').html());
                    
                    var compiled = template({cell: this.model, assignment: _assignment, grades: this.thisLetterGrades, role: this.gradebook.role});
                    this.$el.html(compiled);
                    return this.el;
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
                close: function (ev) {
                    this.remove();
                }
            });
            return CellMidSemester;
        });